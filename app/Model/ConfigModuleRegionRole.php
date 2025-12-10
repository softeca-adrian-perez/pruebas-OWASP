<?php

class ConfigModuleRegionRole extends AppModel
{

    public $useTable = 'config_modules_regions_roles';

    public $hasOne = array(
        'ConfigSection',
        'AagRegion',
        'Role'
    );

    public $validate = array(
        'role_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_role',
            ),
        ),
        'aag_region_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_role',
            ),
        ),
    );

    public function get_list_config_module_region_role($aag_region_id, $role_id)
    {
        $this->Config = ClassRegistry::Init('Config');
        if (!empty($aag_region_id) && !empty($role_id)) {
            $data = $this->Config->find('all', array(
                'fields' => array(
                    'Config.id',
                    'IFNULL(ConfigModuleRegionRole.active, Config.active) as active',
                ),
                'joins' => array(
                    array(
                        'alias' => 'ConfigModuleRegionRole',
                        'table' => 'config_modules_regions_roles',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Config.id = ConfigModuleRegionRole.config_id'
                        )
                    ),
                ),
                'conditions' => array(
                    'Config.section_id' => ConstantsSections::MODULES,
                    'ConfigModuleRegionRole.aag_region_id' => $aag_region_id,
                    'ConfigModuleRegionRole.role_id' => $role_id
                ),
            ));
            $result = array();
            foreach ($data as $dataTmp) {
                $result[] = array(
                    'id' => $dataTmp['Config']['id'],
                    'active' => $dataTmp['0']['active']
                );
            }
            return $result;
        } elseif (!empty($aag_region_id) && empty($role_id)) {
            $data = $this->Config->find('all', array(
                'fields' => array(
                    'Config.id',
                    'IFNULL(ConfigModuleRegionRole.active, Config.active) as active',
                ),
                'joins' => array(
                    array(
                        'alias' => 'ConfigModuleRegionRole',
                        'table' => 'config_modules_regions_roles',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Config.id = ConfigModuleRegionRole.config_id',
                            'ConfigModuleRegionRole.role_id IS NULL'
                        )
                    ),
                ),
                'conditions' => array(
                    'Config.section_id' => ConstantsSections::MODULES,
                    'ConfigModuleRegionRole.aag_region_id' => $aag_region_id,
                ),
            ));
            if ($data) {
                $result = array();
                foreach ($data as $dataTmp) {
                    $result[] = array(
                        'id' => $dataTmp['Config']['id'],
                        'active' => $dataTmp['0']['active']
                    );
                }
                return $result;
            } else {
                $data = $this->Config->find('all', array(
                    'fields' => array(
                        'id',
                        'active'
                    ),
                    'conditions' => array(
                        'Config.section_id' => ConstantsSections::MODULES
                    ),
                ));
                if ($data) {
                    return $data;
                }
            }
        } else {
            $data = $this->Config->find('all', array(
                'fields' => array(
                    'id',
                    'active'
                ),
                'conditions' => array(
                    'Config.section_id' => ConstantsSections::MODULES
                ),
            ));
            if ($data) {
                return $data;
            }
        }
    }

    public function get_list_config_modules()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'config_id',
                    'aag_region_id',
                    'role_id',
                    'active',
                )
            )
        );
    }

    public function count_active_config_modules($aag_region_id, $role_id)
    {
        return $this->find('count', array(
            'fields' => 'id',
            'conditions' => array(
                'ConfigModuleRegionRole.aag_region_id' => $aag_region_id,
                'ConfigModuleRegionRole.role_id' => $role_id,
                'ConfigModuleRegionRole.active' => 1,
            ),
        ));
    }

    public function count_inactive_config_modules($aag_region_id, $role_id)
    {
        return $this->find('count', array(
            'fields' => 'id',
            'conditions' => array(
                'ConfigModuleRegionRole.aag_region_id' => $aag_region_id,
                'ConfigModuleRegionRole.role_id' => $role_id,
                'ConfigModuleRegionRole.active' => 0,
            ),
        ));
    }

    public function update_all_config_registers($config_id)
    {
        $sql = 'UPDATE config_modules_regions_roles
        SET config_modules_regions_roles.active = 0
        WHERE config_id = ' . $config_id . ' AND config_modules_regions_roles.active = 1';

        $this->query($sql);
    }

    public function get_status_roles($aag_region_id, $roles_ids)
    {
        $this->Config = ClassRegistry::Init('Config');
        $response = array();
        if (!empty($aag_region_id) && !empty($roles_ids)) {
            $results = $this->Config->find('all', array(
                'fields' => array(
                    'Config.id',
                    'ConfigModuleRegionRole.active as active',
                ),
                'joins' => array(
                    array(
                        'alias' => 'ConfigModuleRegionRole',
                        'table' => 'config_modules_regions_roles',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Config.id = ConfigModuleRegionRole.config_id'
                        )
                    ),
                ),
                'conditions' => array(
                    'Config.section_id' => ConstantsSections::MODULES,
                    'ConfigModuleRegionRole.aag_region_id' => $aag_region_id,
                    'ConfigModuleRegionRole.role_id' => $roles_ids
                ),
                'order' => 'Config.id',
            ));
        }
        return $results;
    }

    /**
     * completes module configuration for existing regions.
     */
    public function completeRegionModuleConfiguration()
    {
        $this->Config = ClassRegistry::Init('Config');
        $this->Role = ClassRegistry::Init('Role');
        $this->AagRegion = ClassRegistry::Init('AagRegion');

        $configs = $this->Config->find('list', array(
            'conditions' => array(
                'Config.section_id' => ConstantsSections::MODULES
            ),
            'fields' => array(
                'Config.id'
            ),
        ));

        $roles = $this->Role->find('list', array(
            'fields' => 'id'
        ));

        $regions = $this->AagRegion->find('list', array(
            'fields' => 'id'
        ));

        foreach ($regions as $region) {
            foreach ($roles as $role) {
                foreach ($configs as $config) {
                    $moduleConfig = $this->find('first', array(
                        'conditions' => array(
                            'ConfigModuleRegionRole.config_id' => $config,
                            'ConfigModuleRegionRole.aag_region_id' => $region,
                            'ConfigModuleRegionRole.role_id' => $role,
                        ),
                    ));

                    if (!$moduleConfig || !isset($moduleConfig) || empty($moduleConfig)) {
                        $newConfig = array(
                            'config_id' => $config,
                            'aag_region_id' => $region,
                            'role_id' => $role,
                            'active' => 0
                        );

                        $this->create();
                        $this->save($newConfig);
                        $this->commit();
                    }
                }
            }
        }
    }
}
