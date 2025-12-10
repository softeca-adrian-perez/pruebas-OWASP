<?php

class ConfigModuleRegionRoleShell extends Shell
{
    public $uses = array(
		'ConfigModuleRegionRole',
	);

    /**
     * completes module configuration for existing regions.
     * Deactivates new configurations.
     * console\cake configmoduleregionrole removeOldEntries
     */
    public function completeConfigs()
    {
        exit;
        $this->ConfigModuleRegionRole->completeRegionModuleConfiguration();
    }
}