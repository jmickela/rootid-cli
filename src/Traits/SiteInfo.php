<?php

namespace RootidCLI\Traits;

trait SiteInfo {

    function getSite($site_name='') {
        $out = shell_exec("terminus site:info {$site_name} --format=json");
        $out = json_decode($out);
        return $out;
    }

    /**
     * Returns an array of site slugs that the user has access to.
     */
    function getSiteList() {
        $sites = shell_exec('terminus site:list --format=json');
        $sites = json_decode($sites);
        $output = [];

        foreach($sites as $key => $site) {
            $output[] = $site->name;
        }
        
        return $output;
    }
}