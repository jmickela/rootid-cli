<?php

namespace RootidCLI\Traits;

trait SiteOperations {

    function cloneSiteCode($site_slug) {
        $info = shell_exec("terminus connection:info {$site_slug}.dev --format=json");
        $info = json_decode($info);

        // TODO: should there be a predefined projects directory?
        // For now, let the user do stupid things....

        $output = shell_exec($info->git_command);
    }

    /**
     * Downloads site database and loads it into the database.
     */
    function fetchSiteDB($site_slug, $env) {
        $user = \Robo\Robo::config()->get('options.db_username');
        $pass = \Robo\Robo::config()->get('options.db_pass');
        $tmp = \Robo\Robo::config()->get('options.tmp_directory');
        $table = $this->getDatabaseName($site_slug);
        $terminus_site = $this->getTerminusSite($site_slug, $env); 

        $db_file = sprintf("%s/%s.sql.gz", $tmp, $site_slug);

        $this->say('Creating backup on Pantheon.');
        $this->taskExec('terminus')->args('backup:create', $terminus_site, '--element=db')->run();
        $this->say('Downloading backup file.');
        $this->taskExec('terminus')->args('backup:get', $terminus_site, "--to=" . $db_file, '--element=db')->run();

        // Connect to the MySQL server
        $conn = new \mysqli('localhost', $user, $pass);

        // Check connection
        if (mysqli_connect_errno()) {
            exit('Connect failed: '. mysqli_connect_error());
        }

        // Define sql queries to create and drop databases:
        $create_database = "CREATE DATABASE " . $table;
        $drop_database = "DROP DATABASE " . $table;

        // First, check to see whether the database already exists
        if (mysqli_select_db($conn, $table)) {
            // If it exists, drop it -- we want a fresh start
            if ($conn->query($drop_database) === TRUE) {
                // echo "Old database " . $table . " successfully dropped\n";
            }
            else {
                echo 'Error: '. $conn->error;
            }
        } 

        // Then create the new database
        if ($conn->query($create_database) === TRUE) {
            // echo "New database " . $table . " successfully created\n";
        }
        else {
            echo 'Error: '. $conn->error;
        }

        $conn->close();
        
        $this->say('Unzipping and importing data');

        $mysql = "mysql";
        if(!empty($user)) {
          $mysql .= " -u {$user}";
        }
        if(!empty($pass)) {
          $mysql .= " -p{$pass}";
        }
        $mysql .= ' ' . $table;

        $this->_exec("gunzip < {$db_file} | " . $mysql);
        $this->say('Data Import complete, deleting db file.');
        $this->_exec("rm {$db_file}");
    }

    function fetchSiteFiles($site, $env) {
        $path = $this->getFilesDir($site);
        $pantheon_site = $this->getTerminusSite($site->name, $env);
        $tmp = \Robo\Robo::config()->get('options.tmp_directory');
        $tmp_file = sprintf("%s/%s.tar.gz", $tmp, $site->name);
        
        $this->say('Creating Site Archive on Pantheon.');

        $this->taskExec('terminus')->args('backup:create', $pantheon_site, '--element=files')->run();
        $this->say('Downloading files.');
        $this->taskExec('terminus')->args('backup:get', $pantheon_site, '--to=' . $tmp_file, '--element=files')->run();
        $this->say('Unzipping archive');

        @$this->taskExec('tar')->args('-xvf', $tmp_file , '-C', $tmp)->rawArg('>/dev/null')->run();
        if(file_exists ($tmp .'/files_' . $env . '/.htaccess')) {
            $this->_exec('rm ' . $tmp . '/files_' . $env. '/.htaccess');
        }
        $this->say('Copying Files');
        $this->_copyDir($tmp .'/files_' . $env, $path);

        $this->say('Removing downloaded Files.');
        $this->_exec('rm -rf ' . $tmp . '/files_' . $env);
        $this->_exec('rm ' . $tmp_file);
    }


    private function getTerminusSite($site_slug, $env) {
        return "{$site_slug}.{$env}";
    }

    private function getDatabaseName($site_slug) {
        return str_replace('-', '_', $site_slug);
    }

    private function getSettingsDirectory($site) {
        $path = '.';
        
        if(\Robo\Robo::config()->get('web_docroot') && in_array($site->framework, ['drupal', 'drupal8'])) {
            $path .= '/web';
        }

        if($site->framework == 'wordpress') {
            return $path;
        } elseif($site->framework == 'drupal') {
            return $path . '/sites/default';
        } elseif($site->framework == 'drupal8') {
            return $path . '/sites/default';
        }
    }

    private function getFilesDir($site) {
        $in_web_dir = \Robo\Robo::config()->get('web_docroot');

        $path = ".";
        if($in_web_dir) {
            $path .= "/web";
        }

        if($site->framework == 'drupal8' || $site->framework == 'drupal') {
            $path .= "/sites/default/files";
        } elseif($site->framework == 'wordpress') {
            $path .= "/wp-content/uploads";
        }

        return $path;
    }
}