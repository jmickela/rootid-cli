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
        $this->say('Copying Files');
        $this->_copyDir($tmp .'/files_' . $env, $path);

        $this->say('Removing downloaded Files.');
        $this->_exec('rm -rf ' . $tmp . '/files_' . $env);
        $this->_exec('rm ' . $tmp_file);
    }

    private function getSettingsDirectory($site) {
        $path = '.';
        // TODO: get this working!
        // open pantheon.ym, look for web_docroot, if true, add /web

        if($site->framework == 'wordpress') {
            return $path;
        } elseif($site->framework == 'drupal7') {
            return $path . '/sites/default';
        } elseif($site->framework == 'drupal8') {
            return $path . '/sites/default';
        }
    }

    private function getTerminusSite($site_slug, $env) {
        return "{$site_slug}.{$env}";
    }

    private function getDatabaseName($site_slug) {
        return str_replace('-', '_', $site_slug);
    }

    private function getFilesDir($site) {
        $in_web_dir = \Robo\Robo::config()->get('web_docroot');

        $path = ".";
        if($in_web_dir) {
            $path .= "/web";
        }

        if($site->framework == 'drupal8' || $site->framework == 'drupal7') {
            $path .= "/sites/default/files";
        } elseif($site->framework == 'wordpress') {
            $path .= "/wp-content/uploads";
        }

        return $path;
    }
}