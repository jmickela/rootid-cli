<?php

namespace RootidCLI\Commands\Sync;

use RootidCLI\Traits\SiteInfo;
use RootidCLI\Traits\SiteOperations;

class SyncSiteData extends \Robo\Tasks {
    use SiteInfo;
    use SiteOperations;

    /**
     * Syncs Site Data
     *
     * @command sync
     *
     * @option nodb Does not sync site database.
     * @option files Syncs site files.
     * @option env Specific site environment to sync from.
     *
     * @usage --machine-token=<machine_token> Logs in a user granted the machine token <machine_token>.
     * @usage Logs in a user with a previously saved machine token.
     * @usage --email=<email> Logs in a user with a previously saved machine token belonging to <email>.
     */
    public function doSync($options = ['nodb' => NULL, 'files' => NULL, 'env' => 'dev']) {
        $site_data = $this->getSite();

        // If the env isn't live, dev, or test, then make sure the env actually exists on Pantheon.
        if(!in_array($options['env'], ['live', 'dev', 'test'])) {
            //TODO: Use terminus to verify this env exists.
        }

        if(!$options['nodb']) {
            $this->fetchSiteDB($site_data->name, $options['env']);
        }

        if($options['files']) {
            $this->fetchSiteFiles($site_data, $options['env']);
        }
    }
}
