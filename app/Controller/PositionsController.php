<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class PositionsController extends AppController
{
    public $uses = array(
        'Position',
        'GroupPermission',
        'Network',
        'DistributorNetwork',
        'CustomerActivity',
        'SupplierCategory',
        'Position',
        'PositionConfig',
        'PositionConfigType',
        'PositionConfigNetwork',
        'PositionConfigRegion',
        'PositionConfigTradingGroup',
        'PositionConfigDistributorNetwork',
        'PositionConfigAagMember',
        'PositionConfigProfile',
        'PositionConfigActivity',
        'PositionConfigCustomerActivity',
        'PositionConfigSupplierCategory',
        'PositionConfigBdm',
        'RolePositionConfigType',
        'Role',
        'LogChange',
        'Region',
        'Contact',
        'TradingGroup',
        'User',
    );

    /**
     * Positions home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::POSITIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $selectedLanguage = 'name_' . $this->Session->read('Auth.User.language_code');
            $roles = $this->Role->search_list();
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            $conditions = $this->Position->conditions($searcher);

            $positions = $this->custom_pagination(
                $this->Position->_query('home'),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Position',
                null,
                'PaginatorOrderCustom'
            );

            $userList = $this->User->listCompleteNameRegion($aagRegionId);
            $positionsList = $this->Position->search_list();
            $positionsConfig = $this->PositionConfig->getPositionConfig($positions);
            $positionConfigTypes = $this->PositionConfigType->search_list($positions);
            $networks = $this->Network->networksList();
            $tradingGroups = $this->TradingGroup->tradingGroupsList();
            $distributorNetworks = $this->DistributorNetwork->distributorNetworksList();
            $profiles = $this->Position->search_list_profiles();
            $customerActivities = $this->CustomerActivity->search_list();
            $suppliersCategories = $this->SupplierCategory->search_list();
            $aagMembers = array(
                ConstantsAagMember::YES => __t('Communication.Aag_member_yes'),
                ConstantsAagMember::NO => __t('Communication.Aag_member_no')
            );
            $groupPermissions = $this->GroupPermission->search_list();
            $regions = $this->Region->region_list();
            $selectAll = array(
                ConstantsConfigSelect::ALL => __t('General.All')
            );
            $regions = $selectAll + $regions;
            $networks = $selectAll + $networks + array(ConstantsConfigSelect::WITHOUT => __t('Communication.Without_garage_network'));
            $tradingGroups = $selectAll + $tradingGroups;
            $distributorNetworks = $selectAll + $distributorNetworks + array(ConstantsConfigSelect::WITHOUT => __t('Communication.Without_distributor_network'));
            $aagMembers = $selectAll + $aagMembers;
            $customerActivities = $selectAll + $customerActivities + array(ConstantsConfigSelect::WITHOUT => __t('Communication.Without_activity'));
            $suppliersCategories = $selectAll + $suppliersCategories;
            $profiles = $selectAll + $profiles;

            $this->set(
                array(
                    'user_list' => $userList,
                    'positions_config' => $positionsConfig,
                    'roles' => $roles,
                    'selected_language' => $selectedLanguage,
                    'positions' => $positions,
                    'positions_list' => $positionsList,
                    'position_config_types' => $positionConfigTypes,
                    'networks' => $networks,
                    'trading_groups' => $tradingGroups,
                    'distributor_networks' => $distributorNetworks,
                    'aag_members' => $aagMembers,
                    'customer_activities' => $customerActivities,
                    'suppliers_categories' => $suppliersCategories,
                    'profiles' => $profiles,
                    'group_permissions' => $groupPermissions,
                    'regions' => $regions,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create position.
     */
    public function add()
    {
        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::POSITIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $this->Position->validate['name' . __s()] = array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name',
            );
            $roles = $this->Role->search_list();
            $positionConfigTypes = $this->PositionConfigType->search_list();
            $networks = $this->Network->networksList();
            $distributorNetworks = $this->DistributorNetwork->distributorNetworksList();
            $profiles = $this->Position->search_list_profiles();
            $customerActivities = $this->CustomerActivity->search_list();
            $suppliersCategories = $this->SupplierCategory->search_list();
            $aagMembers = array(
                ConstantsAagMember::YES => __t('Communication.Aag_member_yes'),
                ConstantsAagMember::NO => __t('Communication.Aag_member_no')
            );
            $groupPermissions = $this->GroupPermission->search_list();
            $regions = $this->Region->region_list();
            $tradingGroups = $this->TradingGroup->tradingGroupsList();
            $selectAll = array(
                ConstantsConfigSelect::ALL => __t('General.All')
            );
            $regions = $selectAll + $regions;
            $networks = $selectAll + $networks + array(ConstantsConfigSelect::WITHOUT => __t('Network.Without_network'));
            $tradingGroups = $selectAll + $tradingGroups;
            $distributorNetworks = $selectAll + $distributorNetworks + array(ConstantsConfigSelect::WITHOUT => __t('Communication.Without_distributor_network'));
            $aagMembers = $selectAll + $aagMembers;
            $customerActivities = $selectAll + $customerActivities + array(ConstantsConfigSelect::WITHOUT => __t('Communication.Without_activity'));
            $suppliersCategories = $selectAll + $suppliersCategories;
            $profiles = $selectAll + $profiles;
            $bdms = $this->User->getBDMUsers();

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'positions',
                    'action' => 'home',
                ),
            );

            if (!$this->request->is('get')) {
                $this->request->data['Position']['networks'] = json_decode(stripslashes($this->request->data['Position']['networks']), true);
                $this->request->data['Position']['regions'] = json_decode(stripslashes($this->request->data['Position']['regions']), true);
                $this->request->data['Position']['trading_groups'] = json_decode(stripslashes($this->request->data['Position']['trading_groups']), true);
                $this->request->data['Position']['distributor_networks'] = json_decode(stripslashes($this->request->data['Position']['distributor_networks']), true);
                $this->request->data['Position']['aag_members'] = json_decode(stripslashes($this->request->data['Position']['aag_members']), true);
                $this->request->data['Position']['profiles'] = json_decode(stripslashes($this->request->data['Position']['profiles']), true);
                $this->request->data['Position']['customer_activities'] = json_decode(stripslashes($this->request->data['Position']['customer_activities']), true);
                $this->request->data['Position']['suppliers_categories'] = json_decode(stripslashes($this->request->data['Position']['suppliers_categories']), true);
                $this->request->data['Position']['bdms'] = json_decode(stripslashes($this->request->data['Position']['bdms']), true);
                $this->request->data['Position']['position_types'] = json_decode(stripslashes($this->request->data['Position']['position_types']), true);
                $this->request->data['Position']['group_permissions'] = json_decode(stripslashes($this->request->data['Position']['group_permissions']), true);

                if ($this->Position->new_position($this->request->data)) {
                    foreach ($this->request->data['Position']['group_permissions'] as $key => $group_permission_id) {
                        if (!empty($group_permission_id)) {
                            if ($this->PositionConfig->new_position_config($this->request->data['Position']['position_types'][$key], $this->Position->getLastInsertID(), $group_permission_id)) {
                                if (isset($this->request->data['Position']['networks'][$key])) {
                                    foreach ($this->request->data['Position']['networks'][$key] as $network_id) {
                                        if ($network_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllNetworks($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigNetwork->new_position_config_network($this->PositionConfig->getLastInsertID(), $network_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['regions'][$key])) {
                                    foreach ($this->request->data['Position']['regions'][$key] as $region_id) {
                                        if ($region_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllRegions($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigRegion->new_position_config_region($this->PositionConfig->getLastInsertID(), $region_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['trading_groups'][$key])) {
                                    foreach ($this->request->data['Position']['trading_groups'][$key] as $trading_group_id) {
                                        if ($trading_group_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllTradingGroups($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigTradingGroup->new_position_config_trading_group($this->PositionConfig->getLastInsertID(), $trading_group_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['distributor_networks'][$key])) {
                                    foreach ($this->request->data['Position']['distributor_networks'][$key] as $distributor_network_id) {
                                        if ($distributor_network_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllDistributorNetworks($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigDistributorNetwork->new_position_config_distributor_network($this->PositionConfig->getLastInsertID(), $distributor_network_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['aag_members'][$key])) {
                                    foreach ($this->request->data['Position']['aag_members'][$key] as $aag_member) {
                                        if ($aag_member == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllAagMembers($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigAagMember->new_position_config_aag_member($this->PositionConfig->getLastInsertID(), $aag_member)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['profiles'][$key])) {
                                    foreach ($this->request->data['Position']['profiles'][$key] as $profile_id) {
                                        if ($profile_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllProfiles($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigProfile->new_position_config_profile($this->PositionConfig->getLastInsertID(), $profile_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['customer_activities'][$key])) {
                                    foreach ($this->request->data['Position']['customer_activities'][$key] as $customer_activity_id) {
                                        if ($customer_activity_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllCustomerActivities($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigCustomerActivity->new_position_config_customer_activity($this->PositionConfig->getLastInsertID(), $customer_activity_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['suppliers_categories'][$key])) {
                                    foreach ($this->request->data['Position']['suppliers_categories'][$key] as $supplier_category_id) {
                                        if ($supplier_category_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllSuppliersCategories($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigSupplierCategory->new_position_config_supplier_category($this->PositionConfig->getLastInsertID(), $supplier_category_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['bdms'][$key])) {
                                    foreach ($this->request->data['Position']['bdms'][$key] as $user_id) {
                                        if (!$this->PositionConfigBdm->new_position_config_bdm($this->PositionConfig->getLastInsertID(), $user_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                            } else {
                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                            }
                        }
                    }

                    $positionBd = $this->Position->findById($this->Position->id);
                    $positionsConfigBd = $this->PositionConfig->findAllByPositionId($this->Position->id);

                    foreach ($positionsConfigBd as $positionConfig) {
                        $positionBd['Position']['all_networks'][] = $positionConfig['PositionConfig']['all_networks'];
                        $positionBd['Position']['all_regions'][] = $positionConfig['PositionConfig']['all_regions'];
                        $positionBd['Position']['all_trading_groups'][] = $positionConfig['PositionConfig']['all_trading_groups'];
                        $positionBd['Position']['all_distributor_networks'][] = $positionConfig['PositionConfig']['all_distributor_networks'];
                        $positionBd['Position']['all_aag_members'][] = $positionConfig['PositionConfig']['all_aag_members'];
                        $positionBd['Position']['all_profiles'][] = $positionConfig['PositionConfig']['all_profiles'];
                        $positionBd['Position']['all_customer_activities'][] = $positionConfig['PositionConfig']['all_customer_activities'];
                        $positionBd['Position']['all_suppliers_categories'][] = $positionConfig['PositionConfig']['all_suppliers_categories'];
                        $positionBd['Position']['group_permissions'][] = $positionConfig['PositionConfig']['group_permission_id'];
                        $positionBd['Position']['position_types'][] = $positionConfig['PositionConfig']['position_config_type_id'];
                        $positionBd['Position']['networks'][] = Hash::extract($this->PositionConfigNetwork->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigNetwork.network_id');
                        $positionBd['Position']['regions'][] = Hash::extract($this->PositionConfigRegion->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigRegion.region_id');
                        $positionBd['Position']['trading_groups'][] = Hash::extract($this->PositionConfigTradingGroup->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigTradingGroup.trading_group_id');
                        $positionBd['Position']['distributor_networks'][] = Hash::extract($this->PositionConfigDistributorNetwork->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigDistributorNetwork.distributor_network_id');
                        $positionBd['Position']['aag_members'][] = Hash::extract($this->PositionConfigAagMember->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigAagMember.aag_member');
                        $positionBd['Position']['profiles'][] = Hash::extract($this->PositionConfigProfile->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigProfile.profile_id');
                        $positionBd['Position']['customer_activities'][] = Hash::extract($this->PositionConfigCustomerActivity->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigCustomerActivity.customer_activity_id');
                        $positionBd['Position']['suppliers_categories'][] = Hash::extract($this->PositionConfigSupplierCategory->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigSupplierCategory.supplier_category_id');
                        $positionBd['Position']['bdms'][] = Hash::extract($this->PositionConfigBdm->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigBdm.user_id');
                    }
                    $this->LogChange->save_logs_contact_add($positionBd, CakeSession::read('Auth.User.id'), json_decode(stripslashes($this->request->data['Position']['logs']), true), $this->Position->id);

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                }
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'roles' => $roles,
                'position_config_types' => $positionConfigTypes,
                'networks' => $networks,
                'group_permissions' => $groupPermissions,
                'regions' => $regions,
                'trading_groups' => $tradingGroups,
                'distributor_networks' => $distributorNetworks,
                'aag_members' => $aagMembers,
                'profiles' => $profiles,
                'customer_activities' => $customerActivities,
                'suppliers_categories' => $suppliersCategories,
                'bdms' => $bdms,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit position.
     */
    public function edit($position_id)
    {
        $position = $this->Position->findById($position_id);
        if (
            $position &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::POSITIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $this->Position->validate['name' . __s()] = array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name',
            );
            $roles = $this->Role->search_list();
            $positionConfigTypes = $this->PositionConfigType->search_list();
            $networks = $this->Network->networksList();
            $groupPermissions = $this->GroupPermission->search_list();
            $regions = $this->Region->region_list();
            $tradingGroups = $this->TradingGroup->tradingGroupsList();
            $distributorNetworks = $this->DistributorNetwork->distributorNetworksList();
            $profiles = $this->Position->search_list_profiles();
            $customerActivities = $this->CustomerActivity->search_list();
            $suppliersCategories = $this->SupplierCategory->search_list();
            $aagMembers = array(
                ConstantsAagMember::YES => __t('Communication.Aag_member_yes'),
                ConstantsAagMember::NO => __t('Communication.Aag_member_no')
            );
            $selectAll = array(
                ConstantsConfigSelect::ALL => __t('General.All')
            );
            $regions = $selectAll + $regions;
            $networks = $selectAll + $networks + array(ConstantsConfigSelect::WITHOUT => __t('Network.Without_network'));
            $tradingGroups = $selectAll + $tradingGroups;
            $distributorNetworks = $selectAll + $distributorNetworks + array(ConstantsConfigSelect::WITHOUT => __t('Communication.Without_distributor_network'));
            $aagMembers = $selectAll + $aagMembers;
            $customerActivities = $selectAll + $customerActivities + array(ConstantsConfigSelect::WITHOUT => __t('Communication.Without_activity'));
            $suppliersCategories = $selectAll + $suppliersCategories;
            $profiles = $selectAll + $profiles;
            $bdms = $this->User->getBDMUsers();

            $positionsConfig = $this->PositionConfig->findAllByPositionId($position_id);
            $position['Position']['config_length'] = count($positionsConfig);

            foreach ($positionsConfig as $positionConfig) {
                $position['Position']['all_networks'][] = $positionConfig['PositionConfig']['all_networks'];
                $position['Position']['all_regions'][] = $positionConfig['PositionConfig']['all_regions'];
                $position['Position']['all_trading_groups'][] = $positionConfig['PositionConfig']['all_trading_groups'];
                $position['Position']['all_distributor_networks'][] = $positionConfig['PositionConfig']['all_distributor_networks'];
                $position['Position']['all_aag_members'][] = $positionConfig['PositionConfig']['all_aag_members'];
                $position['Position']['all_customer_activities'][] = $positionConfig['PositionConfig']['all_customer_activities'];
                $position['Position']['all_suppliers_categories'][] = $positionConfig['PositionConfig']['all_suppliers_categories'];
                $position['Position']['all_profiles'][] = $positionConfig['PositionConfig']['all_profiles'];
                $position['Position']['group_permissions'][] = $positionConfig['PositionConfig']['group_permission_id'];
                $position['Position']['position_types'][] = $positionConfig['PositionConfig']['position_config_type_id'];
                $position['Position']['networks'][] = Hash::extract($this->PositionConfigNetwork->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigNetwork.network_id');
                $position['Position']['regions'][] = Hash::extract($this->PositionConfigRegion->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigRegion.region_id');
                $position['Position']['trading_groups'][] = Hash::extract($this->PositionConfigTradingGroup->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigTradingGroup.trading_group_id');
                $position['Position']['distributor_networks'][] = Hash::extract($this->PositionConfigDistributorNetwork->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigDistributorNetwork.distributor_network_id');
                $position['Position']['aag_members'][] = Hash::extract($this->PositionConfigAagMember->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigAagMember.aag_member');
                $position['Position']['profiles'][] = Hash::extract($this->PositionConfigProfile->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigProfile.profile_id');
                $position['Position']['customer_activities'][] = Hash::extract($this->PositionConfigCustomerActivity->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigCustomerActivity.customer_activity_id');
                $position['Position']['suppliers_categories'][] = Hash::extract($this->PositionConfigSupplierCategory->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigSupplierCategory.supplier_category_id');
                $position['Position']['bdms'][] = Hash::extract($this->PositionConfigBdm->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigBdm.user_id');
            }
            $position_uses_tmp = $this->Contact->findAllByPositionId($position_id);
            $positionUses = count($position_uses_tmp);

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'positions',
                    'action' => 'home',
                ),
            );

            if (!$this->request->is('get')) {
                $this->request->data['Position']['networks'] = json_decode(stripslashes($this->request->data['Position']['networks']), true);
                $this->request->data['Position']['regions'] = json_decode(stripslashes($this->request->data['Position']['regions']), true);
                $this->request->data['Position']['trading_groups'] = json_decode(stripslashes($this->request->data['Position']['trading_groups']), true);
                $this->request->data['Position']['distributor_networks'] = json_decode(stripslashes($this->request->data['Position']['distributor_networks']), true);
                $this->request->data['Position']['aag_members'] = json_decode(stripslashes($this->request->data['Position']['aag_members']), true);
                $this->request->data['Position']['profiles'] = json_decode(stripslashes($this->request->data['Position']['profiles']), true);
                $this->request->data['Position']['customer_activities'] = json_decode(stripslashes($this->request->data['Position']['customer_activities']), true);
                $this->request->data['Position']['suppliers_categories'] = json_decode(stripslashes($this->request->data['Position']['suppliers_categories']), true);
                $this->request->data['Position']['bdms'] = json_decode(stripslashes($this->request->data['Position']['bdms']), true);
                $this->request->data['Position']['position_types'] = json_decode(stripslashes($this->request->data['Position']['position_types']), true);
                $this->request->data['Position']['group_permissions'] = json_decode(stripslashes($this->request->data['Position']['group_permissions']), true);
                if ($this->Position->edit_position($this->request->data)) {
                    // Delete Data from PositionConfig, PositionConfigNetwork, PositionConfigRegion and PositionConfigTradingGroup
                    $this->PositionConfig->removeAllRecursivelyByPositionId($this->request->data['Position']['id']);

                    foreach ($this->request->data['Position']['group_permissions'] as $key => $group_permission_id) {
                        if (!empty($group_permission_id)) {
                            if ($this->PositionConfig->new_position_config($this->request->data['Position']['position_types'][$key], $this->request->data['Position']['id'], $group_permission_id)) {
                                if (isset($this->request->data['Position']['networks'][$key])) {
                                    foreach ($this->request->data['Position']['networks'][$key] as $network_id) {
                                        if ($network_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllNetworks($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigNetwork->new_position_config_network($this->PositionConfig->getLastInsertID(), $network_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['regions'][$key])) {
                                    foreach ($this->request->data['Position']['regions'][$key] as $region_id) {
                                        if ($region_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllRegions($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigRegion->new_position_config_region($this->PositionConfig->getLastInsertID(), $region_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['trading_groups'][$key])) {
                                    foreach ($this->request->data['Position']['trading_groups'][$key] as $trading_group_id) {
                                        if ($trading_group_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllTradingGroups($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigTradingGroup->new_position_config_trading_group($this->PositionConfig->getLastInsertID(), $trading_group_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['distributor_networks'][$key])) {
                                    foreach ($this->request->data['Position']['distributor_networks'][$key] as $distributor_network_id) {
                                        if ($distributor_network_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllDistributorNetworks($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigDistributorNetwork->new_position_config_distributor_network($this->PositionConfig->getLastInsertID(), $distributor_network_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['aag_members'][$key])) {
                                    foreach ($this->request->data['Position']['aag_members'][$key] as $aag_member) {
                                        if ($aag_member == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllAagMembers($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigAagMember->new_position_config_aag_member($this->PositionConfig->getLastInsertID(), $aag_member)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['profiles'][$key])) {
                                    foreach ($this->request->data['Position']['profiles'][$key] as $profile_id) {
                                        if ($profile_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllProfiles($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigProfile->new_position_config_profile($this->PositionConfig->getLastInsertID(), $profile_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['customer_activities'][$key])) {
                                    foreach ($this->request->data['Position']['customer_activities'][$key] as $customer_activity_id) {
                                        if ($customer_activity_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllCustomerActivities($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigCustomerActivity->new_position_config_customer_activity($this->PositionConfig->getLastInsertID(), $customer_activity_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['suppliers_categories'][$key])) {
                                    foreach ($this->request->data['Position']['suppliers_categories'][$key] as $supplier_category_id) {
                                        if ($supplier_category_id == ConstantsConfigSelect::ALL) {
                                            if (!$this->PositionConfig->setAllSuppliersCategories($this->PositionConfig->getLastInsertID())) {
                                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                            }
                                        } elseif (!$this->PositionConfigSupplierCategory->new_position_config_supplier_category($this->PositionConfig->getLastInsertID(), $supplier_category_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                                if (isset($this->request->data['Position']['bdms'][$key])) {
                                    foreach ($this->request->data['Position']['bdms'][$key] as $user_id) {
                                        if (!$this->PositionConfigBdm->new_position_config_bdm($this->PositionConfig->getLastInsertID(), $user_id)) {
                                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                        }
                                    }
                                }
                            } else {
                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                            }
                        }
                    }

                    $positionBd = $this->Position->findById($this->Position->id);
                    $positionsConfigBd = $this->PositionConfig->findAllByPositionId($position_id);

                    foreach ($positionsConfigBd as $positionConfig) {
                        $positionBd['Position']['all_networks'][] = $positionConfig['PositionConfig']['all_networks'];
                        $positionBd['Position']['all_regions'][] = $positionConfig['PositionConfig']['all_regions'];
                        $positionBd['Position']['all_trading_groups'][] = $positionConfig['PositionConfig']['all_trading_groups'];
                        $positionBd['Position']['all_distributor_networks'][] = $positionConfig['PositionConfig']['all_distributor_networks'];
                        $positionBd['Position']['all_aag_members'][] = $positionConfig['PositionConfig']['all_aag_members'];
                        $positionBd['Position']['all_profiles'][] = $positionConfig['PositionConfig']['all_profiles'];
                        $positionBd['Position']['all_customer_activities'][] = $positionConfig['PositionConfig']['all_customer_activities'];
                        $positionBd['Position']['all_suppliers_categories'][] = $positionConfig['PositionConfig']['all_suppliers_categories'];
                        $positionBd['Position']['group_permissions'][] = $positionConfig['PositionConfig']['group_permission_id'];
                        $positionBd['Position']['position_types'][] = $positionConfig['PositionConfig']['position_config_type_id'];
                        $positionBd['Position']['networks'][] = Hash::extract($this->PositionConfigNetwork->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigNetwork.network_id');
                        $positionBd['Position']['regions'][] = Hash::extract($this->PositionConfigRegion->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigRegion.region_id');
                        $positionBd['Position']['trading_groups'][] = Hash::extract($this->PositionConfigTradingGroup->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigTradingGroup.trading_group_id');
                        $positionBd['Position']['distributor_networks'][] = Hash::extract($this->PositionConfigDistributorNetwork->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigDistributorNetwork.distributor_network_id');
                        $positionBd['Position']['aag_members'][] = Hash::extract($this->PositionConfigAagMember->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigAagMember.aag_member');
                        $positionBd['Position']['profiles'][] = Hash::extract($this->PositionConfigProfile->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigProfile.profile');
                        $positionBd['Position']['suppliers_categories'][] = Hash::extract($this->PositionConfigSupplierCategory->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigSupplierCategory.supplier_category_id');
                        $positionBd['Position']['customer_activities'][] = Hash::extract($this->PositionConfigCustomerActivity->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigCustomerActivity.customer_activity_id');
                        $positionBd['Position']['bdms'][] = Hash::extract($this->PositionConfigBdm->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigBdm.user_id');
                    }
                    $this->LogChange->save_logs_contact_edit($position, $positionBd, CakeSession::read('Auth.User.id'), json_decode(stripslashes($this->request->data['Position']['logs']), true), $positionBd['Position']['id']);

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                }
            } else {
                $this->request->data = $position;
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'roles' => $roles,
                'position_config_types' => $positionConfigTypes,
                'networks' => $networks,
                'group_permissions' => $groupPermissions,
                'regions' => $regions,
                'trading_groups' => $tradingGroups,
                'distributor_networks' => $distributorNetworks,
                'aag_members' => $aagMembers,
                'profiles' => $profiles,
                'customer_activities' => $customerActivities,
                'suppliers_categories' => $suppliersCategories,
                'position_uses' => $positionUses,
                'position_id' => $position_id,
                'position' => $position,
                'bdms' => $bdms,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete position.
     */
    public function delete($position_id)
    {
        $positionBd = $this->Position->findById($position_id);
        if (
            $positionBd &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::POSITIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $positionsConfigBd = $this->PositionConfig->findAllByPositionId($position_id);

            foreach ($positionsConfigBd as $positionConfig) {
                $positionBd['Position']['all_networks'][] = $positionConfig['PositionConfig']['all_networks'];
                $positionBd['Position']['all_regions'][] = $positionConfig['PositionConfig']['all_regions'];
                $positionBd['Position']['all_trading_groups'][] = $positionConfig['PositionConfig']['all_trading_groups'];
                $positionBd['Position']['all_distributor_networks'][] = $positionConfig['PositionConfig']['all_distributor_networks'];
                $positionBd['Position']['all_aag_members'][] = $positionConfig['PositionConfig']['all_aag_members'];
                $positionBd['Position']['all_profiles'][] = $positionConfig['PositionConfig']['all_profiles'];
                $positionBd['Position']['all_customer_activities'][] = $positionConfig['PositionConfig']['all_customer_activities'];
                $positionBd['Position']['all_suppliers_categories'][] = $positionConfig['PositionConfig']['all_suppliers_categories'];
                $positionBd['Position']['group_permissions'][] = $positionConfig['PositionConfig']['group_permission_id'];
                $positionBd['Position']['position_types'][] = $positionConfig['PositionConfig']['position_config_type_id'];
                $positionBd['Position']['networks'][] = Hash::extract($this->PositionConfigNetwork->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigNetwork.network_id');
                $positionBd['Position']['regions'][] = Hash::extract($this->PositionConfigRegion->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigRegion.region_id');
                $positionBd['Position']['trading_groups'][] = Hash::extract($this->PositionConfigTradingGroup->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigTradingGroup.trading_group_id');
                $positionBd['Position']['distributor_networks'][] = Hash::extract($this->PositionConfigDistributorNetwork->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigDistributorNetwork.distributor_network_id');
                $positionBd['Position']['aag_members'][] = Hash::extract($this->PositionConfigAagMember->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigAagMember.aag_member');
                $positionBd['Position']['profiles'][] = Hash::extract($this->PositionConfigProfile->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigProfile.profile');
                $positionBd['Position']['suppliers_categories'][] = Hash::extract($this->PositionConfigSupplierCategory->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigSupplierCategory.supplier_category_id');
                $positionBd['Position']['customer_activities'][] = Hash::extract($this->PositionConfigCustomerActivity->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigCustomerActivity.customer_activity_id');
                $positionBd['Position']['bdms'][] = Hash::extract($this->PositionConfigBdm->findAllByPositionConfigId($positionConfig['PositionConfig']['id']), '{n}.PositionConfigBdm.user_id');
            }

            if ($this->PositionConfig->removeAllRecursivelyByPositionId($position_id)) {
                if ($this->Position->delete($position_id)) {
                    $this->LogChange->save_logs_contact_delete($positionBd, null, CakeSession::read('Auth.User.id'));
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(array(
                        'controller' => 'positions',
                        'action' => 'home',
                    ));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    $this->redirect(array(
                        'controller' => 'positions',
                        'action' => 'home',
                    ));
                }
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                $this->redirect(array(
                    'controller' => 'positions',
                    'action' => 'home',
                ));
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get position config typ by role.
     */
    public function ajax_position_config_type_role()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::POSITIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $roleId = $this->request->data['role_id'];
            $positionConfigTypes = $this->RolePositionConfigType->getListByRoleId($roleId);

            $this->set(array(
                'position_config_types' => $positionConfigTypes
            ));

            $this->layout = false;
            $this->render('../Positions/Elements/position_type');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get position group permissions.
     */
    public function ajax_position_group_permissions()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::POSITIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $position_config_type_id = $this->request->data['position_config_type_id'];
            $groupPermissions = $this->GroupPermission->search_list_by_position_config_type_id($position_config_type_id);

            $this->set(array(
                'group_permissions' => $groupPermissions
            ));

            $this->layout = false;
            $this->render('../Positions/Elements/group_permission');
        } else {
            throw new UnauthorizedException();
        }
    }
}
