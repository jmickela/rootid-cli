<?php

namespace RootidCLI\Commands\Test;

use RootidCLI\Traits\SiteInfo;
use RootidCLI\Traits\SiteOperations;

class RunSiteTests extends \Robo\Tasks {
    use SiteInfo;
    use SiteOperations;

    /**
     * Runs tests against the local version of the site.
     *
     * @command test
     *
     * @option env Specific site environment to sync from.
     * @option init If set this will create references images as well as run a test.
     *
     * @usage --machine-token=<machine_token> Logs in a user granted the machine token <machine_token>.
     * @usage Logs in a user with a previously saved machine token.
     * @usage --email=<email> Logs in a user with a previously saved machine token belonging to <email>.
     */
    public function runTests($options = ['env' => 'live', 'generate' => NULL]) {
        $site_data = $this->getSite();

        // If the env isn't live, dev, or test, then make sure the env actually exists on Pantheon.
        if(!in_array($options['env'], ['live', 'dev', 'test'])) {
            //TODO: Use terminus to verify this env exists.
        }

        chdir('./.tests');

        if($options['generate']) {
            $this->generateReferenceImages();
        }

        $this->runVisualRegressionTests();

        
    }

    private function generateReferenceImages() {
        $this->taskExec('backstop')->args('referance', '--config=backstop.js')->run();
    }

    private function runVisualRegressionTests() {
        // Run the tests set up in the ROOT/.tests/backstop.js file.

        $this->taskExec('backstop')->args('test', '--config=backstop.js')->run();
    }
}