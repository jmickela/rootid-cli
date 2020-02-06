<?php

namespace RootidCLI\Commands\Site;

use RootidCLI\Traits\SiteInfo;
use RootidCLI\Traits\SiteOperations;
use Robo\Robo;

use Symfony\Component\Console\Question\Question;


class SiteImport extends \Robo\Tasks {
    use SiteInfo;
    use SiteOperations;

    /**
     * Imports a site
     *
     * This is more involved than it sounds. It should do a code/db/files import first.
     * then it should import
     *
     *
     *
     * @command import
     * @option env Specific site environment to sync from.
     *
     *
     * @usage --machine-token=<machine_token> Logs in a user granted the machine token <machine_token>.
     * @usage Logs in a user with a previously saved machine token.
     * @usage --email=<email> Logs in a user with a previously saved machine token belonging to <email>.
     */
    public function doImport($options = ['env' => 'dev']) {
        $sites = $this->getSiteList();
        $config = \Robo\Robo::config();

        $question = new Question('Site to Import: ');
        $question->setAutocompleterValues($sites);
        $answer = trim($this->doAsk($question));

        $site = $this->getSite($answer);

        if (!is_object($site)) {
            // Most likely cause is terminus not finding a site for some reason.
            return 1;
        }

        $db_name = $this->getDatabaseName($site->name);

        // Make sure this site isn't already installed:
        if (file_exists('./' . $answer)) {
            $this->yell('Project directory already exists, please use sync instead of importing again.', 60, 'red');
            return 1;
        }

        // Clone the site files
        $this->cloneSiteCode($site->name);

        // Change to the site directory
        chdir('./' . $answer);

        // At this point, there may be a new pantheon.yml file, load it so we know if there is a /web directory.
        Robo::loadConfiguration(["pantheon.yml"]);

        // Copy templates and fill in their data.

        $replacement_patterns = [
            'DB_USER_PLACEHOLDER' => $config->get('options.db_username'),
            'DB_PASS_PLACEHOLDER' => $config->get('options.db_password'),
            'DB_NAME_PLACEHOLDER' => $db_name
        ];
        $settings_dir = $this->getSettingsDirectory($site);

        if ($site->framework == 'drupal') { // DRUPAL 7 ===============================================
            $settings_file_contents = file_get_contents(BASE_DIR . '/templates/drupal7/settings.local.php');

            // plug in site- and user-specific values
            foreach ($replacement_patterns as $key => $val) {
                $settings_file_contents = str_replace($key, $val, $settings_file_contents);
            }

            // set up settings.local.php
            file_put_contents($settings_dir . '/settings.local.php', $settings_file_contents);
        } elseif ($site->framework == 'drupal8') { // DRUPAL 8 =====================================
            $settings_file_contents = file_get_contents(BASE_DIR . '/templates/drupal8/settings.local.php');

            // plug in site- and user-specific values
            foreach ($replacement_patterns as $key => $val) {
                $settings_file_contents = str_replace($key, $val, $settings_file_contents);
            }

            // set up settings.local.php
            file_put_contents($settings_dir . '/settings.local.php', $settings_file_contents);

            // copy services.local.yml
            copy(BASE_DIR . "/templates/drupal8/services.local.yml", $settings_dir . "/services.local.yml");
        } elseif ($site->framework == 'wordpress') { // WORDPRESS =======================================
            $settings_file_contents = file_get_contents(BASE_DIR . '/templates/wordpress/wp-config-local.php');

            // plug in site- and user-specific values
            foreach ($replacement_patterns as $key => $val) {
                $settings_file_contents = str_replace($key, $val, $settings_file_contents);
            }

            // set up wp-config-local.php
            file_put_contents($settings_dir . '/wp-config-local.php', $settings_file_contents);
        }

        // Create database
        $this->createDatabase($db_name);

        // Import data
        $this->fetchSiteDB($site->name, $options['env']);

        // Import files
        $this->fetchSiteFiles($site, $options['env']);

        // Clear Drupal cache
        if ($site->framework == 'drupal8') {
            echo ("Clearing cache... \n");
            shell_exec("drush cr");
            echo ("Cache cleared \n");
        } elseif ($site->framework == 'drupal') {
            echo ("Clearing cache... \n");
            shell_exec("drush cc all");
            echo ("Cache cleared \n");
        }
    }

    private function createDatabase($db_name) {
        $config = \Robo\Robo::config();

        // connect to the MySQL server
        $conn = mysqli_connect('localhost', $config->get('options.db_username'), $config->get('options.db_password'));

        // check connection
        if (mysqli_connect_errno()) {
            exit('Connect failed: ' . mysqli_connect_error());
        }

        // Check to see if the database already exists, if it does, just drop it.
        $result = mysqli_query($conn, "SHOW DATABASES LIKE '$db_name';");

        $row = $result->fetch_assoc();

        if ($row != null) {
            $sql = "DROP DATABASE $db_name";
            echo ("Dropping the old database: ");

            if (mysqli_query($conn, $sql)) {
                echo ("Record deleted successfully \n");
            } else {
                echo ("\n Error deleting record: " . mysqli_error($conn) . "\n");
            }
        }

        // sql query with CREATE DATABASE
        $sql = "CREATE DATABASE `" . $db_name . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
        // Performs the $sql query on the server to create the database
        if (mysqli_query($conn, $sql) === TRUE) {
            echo ("Database " . $db_name . " successfully created \n");
        } else {
            echo ('Error: ' . $conn->error . "\n");
        }
        mysqli_close($conn);
    }
}
