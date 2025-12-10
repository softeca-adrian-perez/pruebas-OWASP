<?php

class PositionConfig extends AppModel{
    public $useTable = 'positions_config';

    public function new_position_config( $position_config_type_id, $position_id, $group_permission_id ){
        $fields = array(
            'PositionConfig' => array(
                'position_id',
                'position_config_type_id',
                'group_permission_id'
            )
        );

        $position_config = array(
            'PositionConfig' => array(
                'position_id' => $position_id,
                'position_config_type_id' => $position_config_type_id,
                'group_permission_id' => $group_permission_id,
            )
        );

        $this->create();
        $position_config_bd = $this->guardar($position_config, $fields);
        if ( !$position_config_bd ){
            return false;
        }

        return true;
    }

    public function removeAllRecursivelyByPositionId( $position_id ){
        $this->PositionConfigNetwork = ClassRegistry::init('PositionConfigNetwork');
        $this->PositionConfigRegion = ClassRegistry::init('PositionConfigRegion');
        $this->PositionConfigTradingGroup = ClassRegistry::init('PositionConfigTradingGroup');
        $this->PositionConfigBdm = ClassRegistry::init('PositionConfigBdm');
        $this->PositionConfigAagMember = ClassRegistry::init('PositionConfigAagMember');
        $this->PositionConfigDistributorNetwork = ClassRegistry::init('PositionConfigDistributorNetwork');
        $this->PositionConfigCustomerActivity = ClassRegistry::init('PositionConfigCustomerActivity');
        $this->PositionConfigSupplierCategory = ClassRegistry::init('PositionConfigSupplierCategory');
        $this->PositionConfigProfile = ClassRegistry::init('PositionConfigProfile');

        $positions_config = $this->findAllByPositionId( $position_id );
        foreach($positions_config as $position_config){

            $positions_config_network = $this->PositionConfigNetwork->findAllByPositionConfigId($position_config['PositionConfig']['id']);
            foreach($positions_config_network as $position_config_network){
                $this->PositionConfigNetwork->delete($position_config_network['PositionConfigNetwork']['id']);
            }

            $positions_config_region = $this->PositionConfigRegion->findAllByPositionConfigId($position_config['PositionConfig']['id']);
            foreach($positions_config_region as $position_config_region){
                $this->PositionConfigRegion->delete($position_config_region['PositionConfigRegion']['id']);
            }

            $positions_config_trading_group = $this->PositionConfigTradingGroup->findAllByPositionConfigId($position_config['PositionConfig']['id']);
            foreach($positions_config_trading_group as $position_config_trading_group){
                $this->PositionConfigTradingGroup->delete($position_config_trading_group['PositionConfigTradingGroup']['id']);
            }

            $positions_config_bdm = $this->PositionConfigBdm->findAllByPositionConfigId($position_config['PositionConfig']['id']);
            foreach($positions_config_bdm as $position_config_bdm){
                $this->PositionConfigBdm->delete($position_config_bdm['PositionConfigBdm']['id']);
            }

            $positions_config_aag_member = $this->PositionConfigAagMember->findAllByPositionConfigId($position_config['PositionConfig']['id']);
            foreach($positions_config_aag_member as $position_config_aag_member){
                $this->PositionConfigAagMember->delete($position_config_aag_member['PositionConfigAagMember']['id']);
            }

            $positions_config_aag_member = $this->PositionConfigDistributorNetwork->findAllByPositionConfigId($position_config['PositionConfig']['id']);
            foreach($positions_config_aag_member as $position_config_aag_member){
                $this->PositionConfigDistributorNetwork->delete($position_config_aag_member['PositionConfigDistributorNetwork']['id']);
            }

            $positions_config_customer_activity = $this->PositionConfigCustomerActivity->findAllByPositionConfigId($position_config['PositionConfig']['id']);
            foreach($positions_config_customer_activity as $position_config_customer_activity){
                $this->PositionConfigCustomerActivity->delete($position_config_customer_activity['PositionConfigCustomerActivity']['id']);
            }

            $positions_config_supplier_category = $this->PositionConfigSupplierCategory->findAllByPositionConfigId($position_config['PositionConfig']['id']);
            foreach($positions_config_supplier_category as $position_config_supplier_category){
                $this->PositionConfigSupplierCategory->delete($position_config_supplier_category['PositionConfigSupplierCategory']['id']);
            }

            $positions_config_profile = $this->PositionConfigProfile->findAllByPositionConfigId($position_config['PositionConfig']['id']);
            foreach($positions_config_profile as $position_config_profile){
                $this->PositionConfigProfile->delete($position_config_profile['PositionConfigProfile']['id']);
            }

            $this->delete($position_config['PositionConfig']['id']);
        }
        return true;
    }

    public function getPositionConfig( $positions ){
        $position_config = array();
        $this->PositionConfigTradingGroup = ClassRegistry::init('PositionConfigTradingGroup');
        $this->PositionConfigNetwork = ClassRegistry::init('PositionConfigNetwork');
        $this->PositionConfigDistributorNetwork = ClassRegistry::init('PositionConfigDistributorNetwork');
        $this->PositionConfigAagMember = ClassRegistry::init('PositionConfigAagMember');
        $this->PositionConfigProfile = ClassRegistry::init('PositionConfigProfile');
        $this->PositionConfigCustomerActivity = ClassRegistry::init('PositionConfigCustomerActivity');
        $this->PositionConfigSupplierCategory = ClassRegistry::init('PositionConfigSupplierCategory');
        $this->PositionConfigRegion = ClassRegistry::init('PositionConfigRegion');
        $this->PositionConfigBdm = ClassRegistry::init('PositionConfigBdm');

        foreach( $positions as $key => $position){
            $position_config[$key]['position_id'] = $position['Position']['id'];
            $position_config[$key]['role_id'] = $position['Position']['role_id'];
            //$position_config[$key]['Config'] = $this->findAllByPositionId($position['Position']['id']);
            $position_configs = $this->findAllByPositionId($position['Position']['id']);
            foreach( $position_configs as $key2 => $position_config_tmp){
                $position_config[$key]['Config'][$key2]['PositionConfig']['id'] = $position_config_tmp['PositionConfig']['id'];
                $position_config[$key]['Config'][$key2]['PositionConfig']['position_config_type_id'] = $position_config_tmp['PositionConfig']['position_config_type_id'];
                $position_config[$key]['Config'][$key2]['PositionConfig']['group_permission_id'] = $position_config_tmp['PositionConfig']['group_permission_id'];
                $position_config[$key]['Config'][$key2]['PositionConfig']['TradingGroups'] = $this->PositionConfigTradingGroup->findAllByPositionConfigId( $position_config_tmp['PositionConfig']['id'] );
                $position_config[$key]['Config'][$key2]['PositionConfig']['all_trading_groups'] = $position_config_tmp['PositionConfig']['all_trading_groups'];
                $position_config[$key]['Config'][$key2]['PositionConfig']['Bdms'] = $this->PositionConfigBdm->findAllByPositionConfigId( $position_config_tmp['PositionConfig']['id'] );
                $position_config[$key]['Config'][$key2]['PositionConfig']['Networks'] = $this->PositionConfigNetwork->findAllByPositionConfigId( $position_config_tmp['PositionConfig']['id'] );
                $position_config[$key]['Config'][$key2]['PositionConfig']['all_networks'] = $position_config_tmp['PositionConfig']['all_networks'];
                $position_config[$key]['Config'][$key2]['PositionConfig']['DistributorNetworks'] = $this->PositionConfigDistributorNetwork->findAllByPositionConfigId( $position_config_tmp['PositionConfig']['id'] );
                $position_config[$key]['Config'][$key2]['PositionConfig']['all_distributor_networks'] = $position_config_tmp['PositionConfig']['all_distributor_networks'];
                $position_config[$key]['Config'][$key2]['PositionConfig']['AagMembers'] = $this->PositionConfigAagMember->findAllByPositionConfigId( $position_config_tmp['PositionConfig']['id'] );
                $position_config[$key]['Config'][$key2]['PositionConfig']['all_aag_members'] = $position_config_tmp['PositionConfig']['all_aag_members'];
                $position_config[$key]['Config'][$key2]['PositionConfig']['Profiles'] = $this->PositionConfigProfile->findAllByPositionConfigId( $position_config_tmp['PositionConfig']['id'] );
                $position_config[$key]['Config'][$key2]['PositionConfig']['all_profiles'] = $position_config_tmp['PositionConfig']['all_profiles'];
                $position_config[$key]['Config'][$key2]['PositionConfig']['CustomerActivities'] = $this->PositionConfigCustomerActivity->findAllByPositionConfigId( $position_config_tmp['PositionConfig']['id'] );
                $position_config[$key]['Config'][$key2]['PositionConfig']['all_customer_activities'] = $position_config_tmp['PositionConfig']['all_customer_activities'];
                $position_config[$key]['Config'][$key2]['PositionConfig']['SuppliersCategories'] = $this->PositionConfigSupplierCategory->findAllByPositionConfigId( $position_config_tmp['PositionConfig']['id'] );
                $position_config[$key]['Config'][$key2]['PositionConfig']['all_suppliers_categories'] = $position_config_tmp['PositionConfig']['all_suppliers_categories'];
                $position_config[$key]['Config'][$key2]['PositionConfig']['Regions'] = $this->PositionConfigRegion->findAllByPositionConfigId( $position_config_tmp['PositionConfig']['id'] );
                $position_config[$key]['Config'][$key2]['PositionConfig']['all_regions'] = $position_config_tmp['PositionConfig']['all_regions'];
            }
        }
        return $position_config;
    }

    public function setAllNetworks( $position_config_id ){
        $fields = array(
            'PositionConfig' => array(
                'id',
                'all_networks'
            )
        );

        $position_config = array(
            'PositionConfig' => array(
                'id' => $position_config_id,
                'all_networks' => true
            )
        );

        $position_config_bd = $this->guardar($position_config, $fields);
        if ( !$position_config_bd ){
            return false;
        }

        return true;
    }

    public function setAllDistributorNetworks( $position_config_id ){
        $fields = array(
            'PositionConfig' => array(
                'id',
                'all_distributor_networks'
            )
        );

        $position_config = array(
            'PositionConfig' => array(
                'id' => $position_config_id,
                'all_distributor_networks' => true
            )
        );

        $position_config_bd = $this->guardar($position_config, $fields);
        if ( !$position_config_bd ){
            return false;
        }

        return true;
    }

    public function setAllAagMembers( $position_config_id ){
        $fields = array(
            'PositionConfig' => array(
                'id',
                'all_aag_members'
            )
        );

        $position_config = array(
            'PositionConfig' => array(
                'id' => $position_config_id,
                'all_aag_members' => true
            )
        );

        $position_config_bd = $this->guardar($position_config, $fields);
        if ( !$position_config_bd ){
            return false;
        }

        return true;
    }

    public function setAllCustomerActivities( $position_config_id ){
        $fields = array(
            'PositionConfig' => array(
                'id',
                'all_customer_activities'
            )
        );

        $position_config = array(
            'PositionConfig' => array(
                'id' => $position_config_id,
                'all_customer_activities' => true
            )
        );

        $position_config_bd = $this->guardar($position_config, $fields);
        if ( !$position_config_bd ){
            return false;
        }

        return true;
    }

    public function setAllSuppliersCategories( $position_config_id ){
        $fields = array(
            'PositionConfig' => array(
                'id',
                'all_suppliers_categories'
            )
        );

        $position_config = array(
            'PositionConfig' => array(
                'id' => $position_config_id,
                'all_suppliers_categories' => true
            )
        );

        $position_config_bd = $this->guardar($position_config, $fields);
        if ( !$position_config_bd ){
            return false;
        }

        return true;
    }

    public function setAllProfiles( $position_config_id ){
        $fields = array(
            'PositionConfig' => array(
                'id',
                'all_profiles'
            )
        );

        $position_config = array(
            'PositionConfig' => array(
                'id' => $position_config_id,
                'all_profiles' => true
            )
        );

        $position_config_bd = $this->guardar($position_config, $fields);
        if ( !$position_config_bd ){
            return false;
        }

        return true;
    }

    public function setAllRegions( $position_config_id ){
        $fields = array(
            'PositionConfig' => array(
                'id',
                'all_regions'
            )
        );

        $position_config = array(
            'PositionConfig' => array(
                'id' => $position_config_id,
                'all_regions' => true
            )
        );

        $position_config_bd = $this->guardar($position_config, $fields);
        if ( !$position_config_bd ){
            return false;
        }

        return true;
    }
    public function setAllTradingGroups( $position_config_id ){
        $fields = array(
            'PositionConfig' => array(
                'id',
                'all_trading_groups'
            )
        );

        $position_config = array(
            'PositionConfig' => array(
                'id' => $position_config_id,
                'all_trading_groups' => true
            )
        );

        $position_config_bd = $this->guardar($position_config, $fields);
        if ( !$position_config_bd ){
            return false;
        }

        return true;
    }

    public function getPositionsConfigToGarage(  $group_permissions , $garage_networks ){
        $positions_config = array();
        $positions_tmp = array();

        //All networks - All regions
        $positions_tmp1 = array();
        foreach( $group_permissions as $group_permission_id ){
            $positions_config_tmp = $this->findAllByGroupPermissionIdAndAllNetworksAndAllRegions( $group_permission_id , true , true);

            if( $positions_config_tmp ){
                foreach($positions_config_tmp as $position_config_tmp){
                    if( $position_config_tmp ){
                        $positions_tmp1[] = $position_config_tmp['PositionConfig']['position_id'];
                    }
                }
            }
        }

        //Specific networks - All region
        $positions_tmp3 = array();
        foreach( $group_permissions as $group_permission_id ){
            $positions_config_tmp = $this->getAllByGroupPermissionIdAndNetworksAndAllRegions( $group_permission_id , $garage_networks , true);
            if( $positions_config_tmp ){
                foreach($positions_config_tmp as $position_config_tmp){
                    if( $position_config_tmp ){
                        $positions_tmp3[] = $position_config_tmp['PositionConfig']['position_id'];
                    }
                }
            }
        }

        //Specific network - Specific region
        $positions_tmp4 = array();
        foreach( $group_permissions as $group_permission_id ){
            $positions_config_tmp = $this->getAllByGroupPermissionIdAndNetworksAndRegionId( $group_permission_id , $garage_networks );
            if( $positions_config_tmp ){
                foreach($positions_config_tmp as $position_config_tmp){
                    if( $position_config_tmp ){
                        $positions_tmp4[] = $position_config_tmp['PositionConfig']['position_id'];
                    }
                }
            }
        }

        $positions_tmp = array_merge($positions_tmp1, $positions_tmp3, $positions_tmp4);
        $positions_tmp = array_unique($positions_tmp);

        return $positions_tmp;

    }

    public function getPositionsConfigToTradingGroup( $group_permissions, $trading_groups ){
        $positions_config = array();
        $positions_tmp = array();

        //All trading_groups
        $positions_tmp1 = array();
        foreach( $group_permissions as $group_permission_id ){
            $positions_config_tmp = $this->findAllByGroupPermissionIdAndAllTradingGroups( $group_permission_id , true);

            if( $positions_config_tmp ){
                foreach($positions_config_tmp as $position_config_tmp){

                    if( $position_config_tmp ){
                        $positions_tmp1[] = $position_config_tmp['PositionConfig']['position_id'];
                    }
                }
            }
        }

        //Specific trading_group
        $positions_tmp2 = array();
        foreach( $group_permissions as $group_permission_id ){
            $positions_config_tmp = $this->getAllByGroupPermissionIdAndTradingGroups( $group_permission_id , $trading_groups);
            if( $positions_config_tmp ){
                foreach($positions_config_tmp as $position_config_tmp){
                    if( $position_config_tmp ){
                        $positions_tmp2[] = $position_config_tmp['PositionConfig']['position_id'];
                    }
                }
            }
        }

        $positions_tmp = array_merge($positions_tmp1, $positions_tmp2);
        $positions_tmp = array_unique($positions_tmp);

        return $positions_tmp;

    }

    public function getAllByGroupPermissionIdAndAllNetworksAndRegionId( $group_permission_id , $all_networks , $region_id ) {
        return $this->find('all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'PositionConfigRegion',
                        'table' => 'positions_config_regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PositionConfigRegion.position_config_id = PositionConfig.id',
                            'PositionConfigRegion.region_id' => $region_id ,
                        ),
                    ),
                ),
                'conditions' => array(
                    'PositionConfig.all_networks' => $all_networks,
                    'PositionConfig.group_permission_id' => $group_permission_id,
                ),
            )
        );
    }

    public function getAllByGroupPermissionIdAndNetworksAndAllRegions( $group_permission_id , $networks , $regions ) {
        return $this->find('all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'PositionConfigNetwork',
                        'table' => 'positions_config_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PositionConfigNetwork.position_config_id = PositionConfig.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'PositionConfigNetwork.network_id' => $networks,
                    'PositionConfig.group_permission_id' => $group_permission_id,
                    'PositionConfig.all_regions' => $regions,
                ),
            )
        );
    }

    public function getPositionsConfigByGroupPermissionsAllNetworks( $group_permissions ) {
        return $this->find('list',
            array(
                'conditions' => array(
                    'PositionConfig.group_permission_id' => $group_permissions,
                    'PositionConfig.all_networks' => true,
                ),
                'fields' => array(
                    'PositionConfig.position_id',
                ),

            )
        );
    }

    public function getPositionsConfigByGroupPermissionsByNetwork( $group_permissions , $network_id ) {
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'PositionConfigNetwork',
                        'table' => 'positions_config_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PositionConfigNetwork.position_config_id = PositionConfig.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'PositionConfigNetwork.network_id' => $network_id,
                    'PositionConfig.group_permission_id' => $group_permissions,
                ),
                'fields' => array(
                    'PositionConfig.position_id',
                ),

            )
        );
    }

    public function getPositionsConfigByGroupPermissionsAllTraidingGroup( $group_permissions ) {
        return $this->find('list',
            array(
                'conditions' => array(
                    'PositionConfig.group_permission_id' => $group_permissions,
                    'PositionConfig.all_trading_groups' => true,
                ),
                'fields' => array(
                    'PositionConfig.position_id',
                ),

            )
        );
    }

    public function getPositionsConfigByGroupPermissionsByTraidingGroup( $group_permissions , $trading_group_id ) {
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'PositionConfigTradingGroup',
                        'table' => 'positions_config_trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PositionConfigTradingGroup.position_config_id = PositionConfig.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'PositionConfigTradingGroup.trading_group_id' => $trading_group_id,
                    'PositionConfig.group_permission_id' => $group_permissions,
                ),
                'fields' => array(
                    'PositionConfig.position_id',
                ),

            )
        );
    }


    public function getAllByGroupPermissionIdAndNetworksAndRegionId( $group_permission_id , $networks ) {

        $datas = $this->find('all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'PositionConfigNetwork',
                        'table' => 'positions_config_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PositionConfigNetwork.position_config_id = PositionConfig.id',
                        ),
                    ),
                    array(
                        'alias' => 'PositionConfigRegion',
                        'table' => 'positions_config_regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PositionConfigRegion.position_config_id = PositionConfig.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'PositionConfigNetwork.network_id' => $networks,
                    'PositionConfig.group_permission_id' => $group_permission_id,
                    'PositionConfig.all_regions' => ConstantsBooleans::ACTIVE,
                ),
            )
        );

        return $datas;
    }

    public function getAllByGroupPermissionIdAndTradingGroups( $group_permission_id, $trading_groups ) {
        return $this->find('all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'PositionConfigTradingGroup',
                        'table' => 'positions_config_trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PositionConfigTradingGroup.position_config_id = PositionConfig.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'PositionConfigTradingGroup.trading_group_id' => $trading_groups,
                    'PositionConfig.group_permission_id' => $group_permission_id
                ),
            )
        );
    }




}