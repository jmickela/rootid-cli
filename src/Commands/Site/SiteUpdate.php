<?php

namespace RootidCLI\Commands\Site;

use RootidCLI\Traits\SiteInfo;
use RootidCLI\Traits\SiteOperations;

use Symfony\Component\Console\Question\Question;


class SiteUpdate extends \Robo\Tasks {
    use SiteInfo;
    use SiteOperations;

    /**
     * Updates a site
     * 
     * For Drupal 8, runs composer update in the base directory (TODO: Actually do this!)
     * For Drupal 7, runs drush up
     * For WordPress, runs wp-cli update
     *
     * @command update
     *
     * @usage Updates your website
     */
    public function doUpdate($options = []) {
        $site = $this->getSite();

        if($site->framework == 'drupal8') {
            $this->yell('Drupal 8 is not yet supported. Tell Jason to figure something out!', 80, 'red');
        } else if($site->framework == 'drupal') {

        }
    }
}