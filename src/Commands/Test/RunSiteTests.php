<?php

namespace RootidCLI\Commands\Test;

use RootidCLI\Traits\SiteInfo;
use RootidCLI\Traits\SiteOperations;

class RunSiteTests extends \Robo\Tasks {
    use SiteInfo;
    use SiteOperations;

    public function __construct() {
        $this->backstop_config = BASE_DIR . "/test-runners/backstop-config.js";
    }


    /**
     * Runs tests against the local version of the site.
     *
     * @command cypress:run
     *
     *
     * @usage --machine-token=<machine_token> Logs in a user granted the machine token <machine_token>.
     * @usage Logs in a user with a previously saved machine token.
     * @usage --email=<email> Logs in a user with a previously saved machine token belonging to <email>.
     * 
     * @alias cypruss
     */
    public function runIntegrationTests() {
        $site_data = $this->getSite();

        $test_url = $this->getLocalSiteRoot($site_data);

        $_ENV['CYPRESS_BASE_URL'] = $test_url;

        //chdir('./.tests');

        //$tests = file_get_contents('elements.json');
        //$_ENV['TEST_ELEMENTS'] = $tests;

        //if($options['generate']) {
        //    $this->generateReferenceImages();
        //}

        $this->runCypressTests($test_url);        
    }

    /**
     * Runs tests against the local version of the site.
     *
     * @command cypress:open
     *
     */
    public function openIntegrationTestResults() {
        $site_data = $this->getSite();
        $test_url = $this->getLocalSiteRoot($site_data);
        $_ENV['CYPRESS_BASE_URL'] = $test_url;
        $this->openCypressTestResults($test_url);
    }

    /**
     * Runs tests against the local version of the site.
     *
     * @command backstop
     *
     * @option env Specific site environment to sync from.
     * @option generate If set this will create references images as well as run a test.
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

        $ref_url = "https://" . $options['env'] . '-' . $site_data->name . ".pantheonsite.io";
        $test_url = $this->getLocalSiteRoot($site_data);

        // See /test-runners/backstop-config.js for when these get used.
        $_ENV['BACKSTOP_REF_URL'] = $ref_url;
        $_ENV['BACKSTOP_TEST_URL'] = $test_url;

        chdir('./.tests');

        $tests = file_get_contents('elements.json');
        $_ENV['TEST_ELEMENTS'] = $tests;

        if($options['generate']) {
            $this->generateReferenceImages();
        }

        $this->runVisualRegressionTests();        
    }

    private function generateReferenceImages() {
        $this->taskExec('backstop')->args('reference', '--config=' . $this->backstop_config)->run();
    }

    private function runVisualRegressionTests() {
        // Run the tests set up in the ROOT/.tests/backstop.js file.
        $this->taskExec('backstop')->args('test', '--config=' . $this->backstop_config)->run();
        $this->taskExec('backstop')->args('openReport', '--config=' . $this->backstop_config)->run();
    }

    private function runCypressTests($base_path) {
        $this->taskExec('cypress')->args('run', '--env')->rawArg('BASE_PATH=' . $base_path)->run();
    }

    private function openCypressTestResults($test_url) {
        $this
            ->taskExec('cypress')
            ->args('open', '--env')
            ->rawArg('BASE_PATH=' . $test_url)
            ->rawArg('--project ./.tests')
            ->run();
    }
}