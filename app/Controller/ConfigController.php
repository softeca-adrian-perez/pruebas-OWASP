<?php
class ConfigController extends AppController
{
    public $uses = array(
        'Config',
        'ConfigList',
        'Equipment',
        'GarageEquipment',
        'EquipmentType',
        'Software',
        'GarageSoftware',
        'DistributorSoftware',
        'SoftwareType',
        'SoftwareManufacture',
        'Garage',
        'LeavingReasonType',
        'GarageNetwork',
        'BillingSchedule',
        'GarageAgreement',
        'Website',
        'GarageWebsite',
        'HoldReasonType',
        'AnnexDetail',
        'EmployeeType',
        'GarageEmployee',
        'Postcode',
        'GarageB2bPostcode',
        'GarageB2cPostcode',
        'CampaignEntry',
        'Facility',
        'GarageFacility',
        'ValueAddSupplierType',
        'ValueAddSupplier',
        'GarageValueAddSupplier',
        'GarageProduct',
        'GarageCampaign',
        'RoomType',
        'StandSize',
        'ConferenceDelegate',
        'CourseType',
        'TrainingCourse',
        'Province',
        'City',
        'Vehicle',
        'GarageVehicle',
        'GarageValueAdd',
        'ValueAdd',
        'ReasonDelegate',
        'TrainingDelegate',
        'AagRegion',
        'Role',
        'ConfigModuleRegionRole',
        'ServiceDriver',
        'Network',
        'CourtesyCarType',
        'ReasonDelegateCancelled',
        'VenueType',
        'Venue',
        'ReasonAllowance',
        'TrainingCreditNetwork',
        'AssociationType',
        'Distributor',
        'NetworkContractType',
        'OrderTypeProduct',
        'OrderType',
        'OrderProduct',
        'Country',
        'NetworkCity',
        'GarageNetworkServiceDriver',
        'SalesArea'
    );

    /**
     * Config maintenance home page.
     */
    public function home()
    {
        $user = $this->Acceso->User();
        $userRoleId = $user['role_id'];
        $userAagRegionId = $user['aag_region_id'];

        if (
            $userRoleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CONFIGURATION, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $config = $this->Config->ConfigSection->search_list();

            $aagRegions = $this->AagRegion->region_list();

            $provinces = $this->Province->getListByAagRegion($userAagRegionId);
            $orderTypes = $this->OrderType->search_list();
            $orderProduct = $this->OrderProduct->search_list($userAagRegionId);

            $networks = $this->Network->getNetworksConditions($userAagRegionId);

            $roles = $this->Role->search_list_not_superadmin();

            $conditions = array('Country.aag_region_id' => $userAagRegionId);
            $countries = $this->Country->get_list_conditions($conditions);
            $cities = $this->City->getListByAagRegion($userAagRegionId);

            $countCountries = count($countries);
            $provincesFromCountry = '';
            $citiesFromProvinces = '';

            if ($countCountries > 0 && !empty($countries)) {
                $searchCountry = array_keys($countries);
                $provincesFromCountry = $this->Province->getProvincesByCountry($searchCountry[0]);
                if (count($provincesFromCountry) > 0 && !empty($provincesFromCountry)) {
                    $searchCity = array_keys($provincesFromCountry);
                    $citiesFromProvinces = $this->City->getCitiesByProvince($searchCity[0]);
                }
            }

            foreach ($config as $key => $section) {
                $aagConfig[$key]['Name'] = $section;
                $aagConfig[$key]['Sections'] = Hash::extract($this->Config->findAllBySectionId($key), '{n}.Config');
            }

            $lists = $this->ConfigList->getList($userAagRegionId);

            $this->set(
                array(
                    'aag_config' => $aagConfig,
                    'lists' => $lists,
                    'provinces' => $provinces,
                    'networks' => $networks,
                    'aag_regions' => $aagRegions,
                    'user_role_id' => $userRoleId,
                    'roles' => $roles,
                    'order_types' => $orderTypes,
                    'order_product' => $orderProduct,
                    'countries' => $countries,
                    'provinces_from_country' => $provincesFromCountry,
                    'cities_from_provinces' => $citiesFromProvinces,
                    'user_aag_region_id' => $userAagRegionId,
                    'cities' => $cities
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Default configuration. Only for SuperAdmin.
     */
    public function default()
    {
        $user = $this->Acceso->User();
        $userRoleId = $user['role_id'];

        if ($userRoleId == ConstantsRoles::SUPER_ADMIN) {
            $config =  $this->Config->ConfigSection->search_list();

            foreach ($config as $key => $section) {
                $aagConfig[$key]['Name'] = $section;
                $aagConfig[$key]['Sections'] = Hash::extract($this->Config->findAllBySectionId($key), '{n}.Config');
            }

            $this->set(
                array(
                    'user_role_id' => $userRoleId,
                    'config' => $config,
                    'aag_config' => $aagConfig,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get modules config. Only for SuperAdmin.
     */
    public function ajax_get_modules_config()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->User();
        $userRoleId = $user['role_id'];

        if ($userRoleId == ConstantsRoles::SUPER_ADMIN) {
            $this->autoRender = false;
            $configModules = array();
            $aagRegionId = $this->request->data['aag_region_id'];

            if (isset($this->request->data['role_id'])) {
                $rolesIds = $this->request->data['role_id'];
                foreach ($rolesIds as $roleId) {
                    $configModules[] = $this->ConfigModuleRegionRole->get_list_config_module_region_role($aagRegionId, $roleId);
                }
            }

            if ((isset($aagRegionId) && !empty($aagRegionId)) && (isset($rolesIds) && !empty($rolesIds))) {
                $aagRegionAndRole = 1;
                $message = $this->allModulesActive($aagRegionId, $rolesIds);
            } else {
                $aagRegionAndRole = 0;
            }


            if (empty($configModules)) {
                $configModules = $this->Config->get_list_config_modules();
            }

            $result = [
                'config_modules' => $configModules,
                'message' => isset($message) ? $message : null,
                'aag_region_and_role' => $aagRegionAndRole,
            ];
            return json_encode($result);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get modules status. Only for SuperAdmin.
     */
    public function ajax_get_modules_status()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->User();
        $userRoleId = $user['role_id'];

        if ($userRoleId == ConstantsRoles::SUPER_ADMIN) {
            $aagRegionId = $this->request->data['aag_region_id'];
            $rolesIds = $this->request->data['role_id'];
            $configs = $this->ConfigModuleRegionRole->get_status_roles($aagRegionId, $rolesIds);
            $configFinal = array();

            foreach ($configs as $config) {
                //if id is already stablished
                if (isset($configFinal[$config['Config']['id']]) && !empty($configFinal[$config['Config']['id']])) {
                    //compare and set value
                    if ($configFinal[$config['Config']['id']] == 1 && $config['ConfigModuleRegionRole']['active'] == 1) {
                        $configFinal[$config['Config']['id']] = 1;
                    } elseif ($configFinal[$config['Config']['id']] == 0 && $config['ConfigModuleRegionRole']['active'] == 0) {
                        $configFinal[$config['Config']['id']] = 0;
                    } else {
                        $configFinal[$config['Config']['id']] = 3;
                    }
                } else {
                    //set value
                    if ($config['ConfigModuleRegionRole']['active'] == 1) {
                        $configFinal[$config['Config']['id']] = 1;
                    } elseif ($config['ConfigModuleRegionRole']['active'] == 0) {
                        $configFinal[$config['Config']['id']] = 0;
                    }
                }
            }
            $this->autoRender = false;
            return json_encode($configFinal);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Private function that checks if all config modules are active.
     */
    private function allModulesActive($aagRegionId, $rolesIds)
    {
        $allModules = $this->ConfigModuleRegionRole->get_list_config_module_region_role($aagRegionId, $rolesIds);
        $countActiveConfigModules = $this->ConfigModuleRegionRole->count_active_config_modules($aagRegionId, $rolesIds);
        $countInactiveConfigModules = $this->ConfigModuleRegionRole->count_inactive_config_modules($aagRegionId, $rolesIds);
        $message = false;

        if (count($allModules) == $countActiveConfigModules && count($allModules) != 0) {
            $message = true;
        } elseif (count($allModules) == $countInactiveConfigModules && count($allModules) != 0) {
            $message = false;
        } else {
            $message = 'multivalue';
        }

        return json_encode($message);
    }

    /**
     * AJAX check all modules as active. Only for SuperAdmin.
     */
    public function ajax_check_active_modules()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->User();
        $userRoleId = $user['role_id'];

        if ($userRoleId == ConstantsRoles::SUPER_ADMIN) {
            $this->autoRender = false;
            $message = '';
            if (!empty($this->request->data['aag_region_id']) && !empty($this->request->data['role_id'])) {
                $aagRegionId = $this->request->data['aag_region_id'];
                $rolesIds = $this->request->data['role_id'];
                $message = $this->allModulesActive($aagRegionId, $rolesIds);
            }
            return json_encode($message);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Saves default configuration. Only for SuperAdmin.
     */
    public function ajax_save_config()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->User();
        $userRoleId = $user['role_id'];

        if ($userRoleId == ConstantsRoles::SUPER_ADMIN) {
            $configs = $this->request->data;
            foreach ($configs as $configId => $active) {
                $configTmp = array(
                    'Config' => array(
                        'id' => $configId,
                        'active' =>  $active == 'true' ? ConstantsBooleans::ACTIVE : ConstantsBooleans::NO_ACTIVE
                    )
                );

                if ($active == 'false') {
                    $configModuleRegionRoleTmp = array(
                        'config_id' => $configId,
                    );
                }
                $this->Config->saveMany($configTmp);
            }

            foreach ($configModuleRegionRoleTmp as $configIdCheck) {
                $this->ConfigModuleRegionRole->update_all_config_registers($configIdCheck);
            }

            $this->Config = ClassRegistry::init('Config');
            $aagConfig = $this->Config->get_list_config();
            CakeSession::write('Auth.User.Config', $aagConfig); // T001 SECURITY - It is not changed

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Given a region, it saves all modules config for one or many users. Only for SuperAdmin.
     */
    public function ajax_save_all_modules_config()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->User();
        $userRoleId = $user['role_id'];

        if ($userRoleId == ConstantsRoles::SUPER_ADMIN) {
            $this->autoRender = false;

            $aagRegionId = $this->request->data['aag_region_id'];
            $rolesIds = $this->request->data['role_id'];
            $active = $this->request->data['active'];

            $configModuleRegionRoles = array();
            foreach ($rolesIds as $roleId) {
                $configModuleRegionRoles_aux = $this->ConfigModuleRegionRole->find(
                    'all',
                    array(
                        'conditions' => array(
                            'role_id' => $roleId,
                            'aag_region_id' => $aagRegionId
                        ),
                    )
                );
                $configModuleRegionRoles = array_merge($configModuleRegionRoles, $configModuleRegionRoles_aux);
            }

            if ($configModuleRegionRoles) {
                foreach ($configModuleRegionRoles as $configModuleRegionRole) {
                    $configTmp = array(
                        'ConfigModuleRegionRole' => array(
                            'id' => $configModuleRegionRole['ConfigModuleRegionRole']['id'],
                            'config_id' => $configModuleRegionRole['ConfigModuleRegionRole']['config_id'],
                            'aag_region_id' => $configModuleRegionRole['ConfigModuleRegionRole']['aag_region_id'],
                            'role_id' => $configModuleRegionRole['ConfigModuleRegionRole']['role_id'],
                            'active' =>  $active
                        )
                    );

                    $this->ConfigModuleRegionRole->save($configTmp);
                }
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Given a region and a module, it saves a config to one or many users. Only for SuperAdmin.
     */
    public function ajax_save_module_config()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->User();
        $userRoleId = $user['role_id'];

        if ($userRoleId == ConstantsRoles::SUPER_ADMIN) {
            $data = $this->request->data;
            $configId = $this->request->data['config-id'];
            $aagRegionId = $this->request->data['aag_region_id'];
            $rolesIds =  $this->request->data['role_id'];
            $active = $data['active'];

            $configModuleRegionRoles = array();

            foreach ($rolesIds as $roleId) {
                $configModuleRegionRoles[] = $this->ConfigModuleRegionRole->find(
                    'all',
                    array(
                        'conditions' => array(
                            'config_id' => $configId,
                            'role_id' => $roleId,
                            'aag_region_id' => $aagRegionId
                        ),
                    )
                );
            }

            foreach ($configModuleRegionRoles as $configModuleRegionRole) {
                $configTmp = array(
                    'ConfigModuleRegionRole' => array(
                        'id' => $configModuleRegionRole[0]['ConfigModuleRegionRole']['id'],
                        'config_id' => $configModuleRegionRole[0]['ConfigModuleRegionRole']['config_id'],
                        'aag_region_id' => $configModuleRegionRole[0]['ConfigModuleRegionRole']['aag_region_id'],
                        'role_id' => $configModuleRegionRole[0]['ConfigModuleRegionRole']['role_id'],
                        'active' =>  $active
                    )
                );

                $this->ConfigModuleRegionRole->save($configTmp);
            }

            $this->ajax_get_modules_status();

            $message = $this->allModulesActive($aagRegionId, $roleId);

            $aagConfigModuleRegionRole = $this->ConfigModuleRegionRole->get_list_config_module_region_role($aagRegionId, $roleId);
            CakeSession::write('Auth.User.Config.Module', $aagConfigModuleRegionRole);
            $this->autoRender = false;
            return $message;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create list value. Only for Admin.
     */
    public function ajax_save_new_value()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CONFIGURATION, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE) &&
            $roleId == ConstantsRoles::ADMIN
        ) {
            $this->autoRender = false;

            switch ($this->request->data['list_id']) {
                case ConstantsLists::EQUIPMENT_NAME:
                    $this->request->data['Equipment']['name_en'] = $this->request->data['name'];
                    $this->request->data['Equipment']['name_fr'] = $this->request->data['name'];
                    $this->request->data['Equipment']['name_de'] = $this->request->data['name'];
                    $this->request->data['Equipment']['name_nl'] = $this->request->data['name'];
                    $this->request->data['Equipment']['name_es'] = $this->request->data['name'];
                    $this->request->data['Equipment']['aag_region_id'] = $aagRegionId;
                    return $this->Equipment->add_equipment($this->request->data);

                case ConstantsLists::EQUIPMENT_TYPE:
                    $this->request->data['EquipmentType']['name_en'] = $this->request->data['name'];
                    $this->request->data['EquipmentType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['EquipmentType']['name_de'] = $this->request->data['name'];
                    $this->request->data['EquipmentType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['EquipmentType']['name_es'] = $this->request->data['name'];
                    $this->request->data['EquipmentType']['aag_region_id'] = $aagRegionId;
                    return $this->EquipmentType->add_equipment($this->request->data);

                case ConstantsLists::SOFTWARE_NAME:
                    $this->request->data['Software']['name_en'] = $this->request->data['name'];
                    $this->request->data['Software']['name_fr'] = $this->request->data['name'];
                    $this->request->data['Software']['name_de'] = $this->request->data['name'];
                    $this->request->data['Software']['name_nl'] = $this->request->data['name'];
                    $this->request->data['Software']['name_es'] = $this->request->data['name'];
                    $this->request->data['Software']['aag_region_id'] = $aagRegionId;
                    return $this->Software->new_software($this->request->data);

                case ConstantsLists::SOFTWARE_TYPE:
                    $this->request->data['SoftwareType']['name_en'] = $this->request->data['name'];
                    $this->request->data['SoftwareType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['SoftwareType']['name_de'] = $this->request->data['name'];
                    $this->request->data['SoftwareType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['SoftwareType']['name_es'] = $this->request->data['name'];
                    $this->request->data['SoftwareType']['aag_region_id'] = $aagRegionId;
                    return $this->SoftwareType->add_software_type($this->request->data);

                case ConstantsLists::SOFTWARE_PROVIDER:
                    $this->request->data['SoftwareManufacture']['name_en'] = $this->request->data['name'];
                    $this->request->data['SoftwareManufacture']['name_fr'] = $this->request->data['name'];
                    $this->request->data['SoftwareManufacture']['name_de'] = $this->request->data['name'];
                    $this->request->data['SoftwareManufacture']['name_nl'] = $this->request->data['name'];
                    $this->request->data['SoftwareManufacture']['name_es'] = $this->request->data['name'];
                    $this->request->data['SoftwareManufacture']['aag_region_id'] = $aagRegionId;
                    return $this->SoftwareManufacture->add_software_manufacture($this->request->data);

                case ConstantsLists::EQUIPMENT_SOFTWARE_BILLING_SCHEDULE:
                    $this->request->data['BillingSchedule']['name_en'] = $this->request->data['name'];
                    $this->request->data['BillingSchedule']['name_fr'] = $this->request->data['name'];
                    $this->request->data['BillingSchedule']['name_de'] = $this->request->data['name'];
                    $this->request->data['BillingSchedule']['name_nl'] = $this->request->data['name'];
                    $this->request->data['BillingSchedule']['name_es'] = $this->request->data['name'];
                    $this->request->data['BillingSchedule']['aag_region_id'] = $aagRegionId;
                    return $this->BillingSchedule->add_billing_schedule($this->request->data);

                case ConstantsLists::WEBSITE_NAME:
                    $this->request->data['Website']['name'] = $this->request->data['name'];
                    $this->request->data['Website']['aag_region_id'] = $aagRegionId;
                    return $this->Website->new_website($this->request->data);
                case ConstantsLists::COURTESY_CAR_TYPE:
                    $this->request->data['CourtesyCarType']['name_en'] = $this->request->data['name'];
                    $this->request->data['CourtesyCarType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['CourtesyCarType']['name_de'] = $this->request->data['name'];
                    $this->request->data['CourtesyCarType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['CourtesyCarType']['name_es'] = $this->request->data['name'];
                    $this->request->data['CourtesyCarType']['aag_region_id'] = $aagRegionId;
                    return $this->CourtesyCarType->add_courtesy_car_type($this->request->data);
                case ConstantsLists::LEAVING_REASON:
                    $this->request->data['LeavingReasonType']['name_en'] = $this->request->data['name'];
                    $this->request->data['LeavingReasonType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['LeavingReasonType']['name_de'] = $this->request->data['name'];
                    $this->request->data['LeavingReasonType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['LeavingReasonType']['name_es'] = $this->request->data['name'];
                    $this->request->data['LeavingReasonType']['aag_region_id'] = $aagRegionId;
                    return $this->LeavingReasonType->add_leaving_reason_type($this->request->data);

                case ConstantsLists::HOLD_REASON:
                    $this->request->data['HoldReasonType']['name_en'] = $this->request->data['name'];
                    $this->request->data['HoldReasonType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['HoldReasonType']['name_de'] = $this->request->data['name'];
                    $this->request->data['HoldReasonType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['HoldReasonType']['name_es'] = $this->request->data['name'];
                    $this->request->data['HoldReasonType']['aag_region_id'] = $aagRegionId;
                    return $this->HoldReasonType->add_hold_reason_type($this->request->data);

                case ConstantsLists::ANNEX_DETAILS:
                    $this->request->data['AnnexDetail']['name_en'] = $this->request->data['name'];
                    $this->request->data['AnnexDetail']['name_fr'] = $this->request->data['name'];
                    $this->request->data['AnnexDetail']['name_de'] = $this->request->data['name'];
                    $this->request->data['AnnexDetail']['name_nl'] = $this->request->data['name'];
                    $this->request->data['AnnexDetail']['name_es'] = $this->request->data['name'];
                    $this->request->data['AnnexDetail']['aag_region_id'] = $aagRegionId;
                    return $this->AnnexDetail->add_annex_detail($this->request->data);

                case ConstantsLists::EMPLOYEE_TYPES:
                    $this->request->data['EmployeeType']['name_en'] = $this->request->data['name'];
                    $this->request->data['EmployeeType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['EmployeeType']['name_de'] = $this->request->data['name'];
                    $this->request->data['EmployeeType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['EmployeeType']['name_es'] = $this->request->data['name'];
                    $this->request->data['EmployeeType']['aag_region_id'] = $aagRegionId;
                    return $this->EmployeeType->add_employee_type($this->request->data);

                case ConstantsLists::POSTCODES:
                    $this->request->data['Postcode']['name'] = $this->request->data['name'];
                    return $this->Postcode->add_postcode($this->request->data);

                case ConstantsLists::CAMPAING_ENTRIES:
                    $this->request->data['CampaignEntry']['name_en'] = $this->request->data['name'];
                    $this->request->data['CampaignEntry']['name_fr'] = $this->request->data['name'];
                    $this->request->data['CampaignEntry']['name_de'] = $this->request->data['name'];
                    $this->request->data['CampaignEntry']['name_nl'] = $this->request->data['name'];
                    $this->request->data['CampaignEntry']['name_es'] = $this->request->data['name'];
                    $this->request->data['CampaignEntry']['aag_region_id'] = $aagRegionId;
                    return $this->CampaignEntry->add_campaign_entries($this->request->data);

                case ConstantsLists::ORDER_PRODUCTS:
                    $this->request->data['OrderProduct']['name_en'] = $this->request->data['name'];
                    $this->request->data['OrderProduct']['name_fr'] = $this->request->data['name'];
                    $this->request->data['OrderProduct']['name_de'] = $this->request->data['name'];
                    $this->request->data['OrderProduct']['name_nl'] = $this->request->data['name'];
                    $this->request->data['OrderProduct']['name_es'] = $this->request->data['name'];
                    $this->request->data['OrderProduct']['aag_region_id'] = $aagRegionId;
                    return $this->OrderProduct->add_order_products($this->request->data);

                case ConstantsLists::FACILITIES:
                    $this->request->data['Facility']['name_en'] = $this->request->data['name'];
                    $this->request->data['Facility']['name_fr'] = $this->request->data['name'];
                    $this->request->data['Facility']['name_de'] = $this->request->data['name'];
                    $this->request->data['Facility']['name_nl'] = $this->request->data['name'];
                    $this->request->data['Facility']['name_es'] = $this->request->data['name'];
                    $this->request->data['Facility']['aag_region_id'] = $aagRegionId;
                    return $this->Facility->add_facility($this->request->data);

                case ConstantsLists::VALUE_ADD_SUPPLIER:
                    $this->request->data['ValueAddSupplier']['name_en'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplier']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplier']['name_de'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplier']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplier']['name_es'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplier']['aag_region_id'] = $aagRegionId;
                    return $this->ValueAddSupplier->add_value_add_supplier($this->request->data);

                case ConstantsLists::VALUE_ADD_SUPPLIER_TYPE:
                    $this->request->data['ValueAddSupplierType']['name_en'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplierType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplierType']['name_de'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplierType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplierType']['name_es'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplierType']['aag_region_id'] = $aagRegionId;
                    return $this->ValueAddSupplierType->add_value_add_supplier_type($this->request->data);

                case ConstantsLists::ROOM_TYPE:
                    $this->request->data['RoomType']['name_en'] = $this->request->data['name'];
                    $this->request->data['RoomType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['RoomType']['name_de'] = $this->request->data['name'];
                    $this->request->data['RoomType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['RoomType']['name_es'] = $this->request->data['name'];
                    $this->request->data['RoomType']['aag_region_id'] = $aagRegionId;
                    return $this->RoomType->add_room_type($this->request->data);

                case ConstantsLists::STAND_SIZE:
                    $this->request->data['StandSize']['name_en'] = $this->request->data['name'];
                    $this->request->data['StandSize']['name_fr'] = $this->request->data['name'];
                    $this->request->data['StandSize']['name_de'] = $this->request->data['name'];
                    $this->request->data['StandSize']['name_nl'] = $this->request->data['name'];
                    $this->request->data['StandSize']['name_es'] = $this->request->data['name'];
                    $this->request->data['StandSize']['aag_region_id'] = $aagRegionId;
                    return $this->StandSize->add_stand_size($this->request->data);

                case ConstantsLists::CITIES:
                    $this->request->data['City']['name'] = $this->request->data['name'];
                    $this->request->data['City']['latitude'] = $this->request->data['latitude'];
                    $this->request->data['City']['longitude'] = $this->request->data['longitude'];
                    $this->request->data['City']['province_id'] = $this->request->data['province_id'];
                    $city = $this->City->add_city($this->request->data);
                    return $city ? true : false;

                case ConstantsLists::COURSE_TYPE:
                    $this->request->data['CourseType']['name_en'] = $this->request->data['name'];
                    $this->request->data['CourseType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['CourseType']['name_de'] = $this->request->data['name'];
                    $this->request->data['CourseType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['CourseType']['name_es'] = $this->request->data['name'];
                    $this->request->data['CourseType']['aag_region_id'] = $aagRegionId;
                    return $this->CourseType->add_course_type($this->request->data);

                case ConstantsLists::VALUES_ADDS:
                    $this->request->data['ValueAdd']['name_en'] = $this->request->data['name'];
                    $this->request->data['ValueAdd']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ValueAdd']['name_de'] = $this->request->data['name'];
                    $this->request->data['ValueAdd']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ValueAdd']['name_es'] = $this->request->data['name'];
                    $this->request->data['ValueAdd']['aag_region_id'] = $aagRegionId;
                    return $this->ValueAdd->add_values_adds($this->request->data);

                case ConstantsLists::REASON_DELEGATE:
                    $this->request->data['ReasonDelegate']['name_en'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegate']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegate']['name_de'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegate']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegate']['name_es'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegate']['aag_region_id'] = $aagRegionId;
                    return $this->ReasonDelegate->add_reason_delegate($this->request->data);

                case ConstantsLists::SERVICES_DRIVERS:
                    $this->request->data['ServiceDriver']['network_id'] = $this->request->data['network_id'];
                    $this->request->data['ServiceDriver']['name_en'] = $this->request->data['name'];
                    $this->request->data['ServiceDriver']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ServiceDriver']['name_de'] = $this->request->data['name'];
                    $this->request->data['ServiceDriver']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ServiceDriver']['name_es'] = $this->request->data['name'];
                    $this->request->data['ServiceDriver']['aag_region_id'] = $aagRegionId;
                    return $this->ServiceDriver->add_services_drivers($this->request->data);

                case ConstantsLists::REASON_DELEGATE_CANCELLED:
                    $this->request->data['ReasonDelegateCancelled']['name_en'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegateCancelled']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegateCancelled']['name_de'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegateCancelled']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegateCancelled']['name_es'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegateCancelled']['aag_region_id'] = $aagRegionId;
                    return $this->ReasonDelegateCancelled->add_reason_delegate($this->request->data);

                case ConstantsLists::VENUES_TYPES:
                    $this->request->data['VenueType']['name_en'] = $this->request->data['name'];
                    $this->request->data['VenueType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['VenueType']['name_de'] = $this->request->data['name'];
                    $this->request->data['VenueType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['VenueType']['name_es'] = $this->request->data['name'];
                    $this->request->data['VenueType']['aag_region_id'] = $aagRegionId;
                    return $this->VenueType->add_venue_type($this->request->data);

                case ConstantsLists::REASON_ALLOWANCE:
                    $this->request->data['ReasonAllowance']['name_en'] = $this->request->data['name'];
                    $this->request->data['ReasonAllowance']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ReasonAllowance']['name_de'] = $this->request->data['name'];
                    $this->request->data['ReasonAllowance']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ReasonAllowance']['name_es'] = $this->request->data['name'];
                    $this->request->data['ReasonAllowance']['aag_region_id'] = $aagRegionId;
                    return $this->ReasonAllowance->add_reason_allowance($this->request->data);

                case ConstantsLists::ASSOCIATION_TYPE:
                    $this->request->data['AssociationType']['name_en'] = $this->request->data['name'];
                    $this->request->data['AssociationType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['AssociationType']['name_de'] = $this->request->data['name'];
                    $this->request->data['AssociationType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['AssociationType']['name_es'] = $this->request->data['name'];
                    $this->request->data['AssociationType']['aag_region_id'] = $aagRegionId;
                    return $this->AssociationType->add_association_type_type($this->request->data);

                case ConstantsLists::NETWORK_CONTRACT_TYPE:
                    $this->request->data['NetworkContractType']['name_en'] = $this->request->data['name'];
                    $this->request->data['NetworkContractType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['NetworkContractType']['name_de'] = $this->request->data['name'];
                    $this->request->data['NetworkContractType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['NetworkContractType']['name_es'] = $this->request->data['name'];
                    $this->request->data['NetworkContractType']['aag_region_id'] = $aagRegionId;
                    return $this->NetworkContractType->add_network_contract_type_type($this->request->data);

                case ConstantsLists::ORDER_TYPES_PRODUCTS:
                    $this->request->data['OrderTypeProduct']['order_product_id'] = $this->request->data['order_product_id'];
                    $this->request->data['OrderTypeProduct']['order_type_id'] = $this->request->data['order_type_id'];
                    $this->request->data['OrderTypeProduct']['aag_region_id'] = $aagRegionId;

                    return $this->OrderTypeProduct->add_order_type_product($this->request->data);

                case ConstantsLists::ORDER_TYPES:
                    $this->request->data['OrderType']['name_en'] = $this->request->data['name'];
                    $this->request->data['OrderType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['OrderType']['name_de'] = $this->request->data['name'];
                    $this->request->data['OrderType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['OrderType']['aag_region_id'] = $aagRegionId;

                    return $this->OrderType->add_order_type($this->request->data);

                case ConstantsLists::APPROVED_GARAGE_CITY:
                    $citiesId = array();
                    if (!empty($this->request->data['city_id'])) {
                        $citiesId = $this->request->data['city_id'];
                    } elseif (!empty($this->request->data['province_id'])) {
                        $citiesId = $this->City->getCitiesIdByProvince($this->request->data['province_id']);
                    } elseif (!empty($this->request->data['country_id'])) {
                        $citiesId = $this->City->getCitiesIdByCountry($this->request->data['country_id']);
                    }

                    if (!empty($citiesId)) {
                        $network_id = NETWORK_ID_AGN;

                        foreach ($citiesId as $value) {

                            $exist = $this->NetworkCity->findByNetworkIdAndCityId($network_id, $value);

                            if (!$exist) {
                                $networkCity = array(
                                    'NetworkCity' => array(
                                        'network_id' => $network_id,
                                        'city_id' => $value
                                    )
                                );

                                $this->NetworkCity->add_network_city($networkCity);
                            }
                        }
                        return true;
                    }
                    return false;

                case ConstantsLists::GARAGEVERGELIJKER_CITY:
                    $citiesId = array();
                    if (!empty($this->request->data['city_id'])) {
                        $citiesId = $this->request->data['city_id'];
                    } elseif (!empty($this->request->data['province_id'])) {
                        $citiesId = $this->City->getCitiesIdByProvince($this->request->data['province_id']);
                    } elseif (!empty($this->request->data['country_id'])) {
                        $citiesId = $this->City->getCitiesIdByCountry($this->request->data['country_id']);
                    }

                    if (!empty($citiesId)) {
                        $network_id = NETWORK_ID_GV;

                        foreach ($citiesId as $value) {

                            $exist = $this->NetworkCity->findByNetworkIdAndCityId($network_id, $value);

                            if (!$exist) {
                                $networkCity = array(
                                    'NetworkCity' => array(
                                        'network_id' => $network_id,
                                        'city_id' => $value
                                    )
                                );

                                $this->NetworkCity->add_network_city($networkCity);
                            }
                        }
                        return true;
                    }
                    return false;

                case ConstantsLists::GARAGE_CHECKER_CITY:
                    $citiesId = array();
                    if (!empty($this->request->data['city_id'])) {
                        $citiesId = $this->request->data['city_id'];
                    } elseif (!empty($this->request->data['province_id'])) {
                        $citiesId = $this->City->getCitiesIdByProvince($this->request->data['province_id']);
                    } elseif (!empty($this->request->data['country_id'])) {
                        $citiesId = $this->City->getCitiesIdByCountry($this->request->data['country_id']);
                    }

                    if (!empty($citiesId)) {
                        $network_id = NETWORK_ID_GC;

                        foreach ($citiesId as $value) {

                            $exist = $this->NetworkCity->findByNetworkIdAndCityId($network_id, $value);

                            if (!$exist) {
                                $networkCity = array(
                                    'NetworkCity' => array(
                                        'network_id' => $network_id,
                                        'city_id' => $value
                                    )
                                );

                                $this->NetworkCity->add_network_city($networkCity);
                            }
                        }
                        return true;
                    }
                    return false;

                case ConstantsLists::SALES_AREA:
                    $this->request->data['SalesArea']['name_en'] = $this->request->data['name'];
                    $this->request->data['SalesArea']['name_fr'] = $this->request->data['name'];
                    $this->request->data['SalesArea']['name_de'] = $this->request->data['name'];
                    $this->request->data['SalesArea']['name_nl'] = $this->request->data['name'];
                    $this->request->data['SalesArea']['name_es'] = $this->request->data['name'];
                    $this->request->data['SalesArea']['aag_region_id'] = $aagRegionId;

                    return $this->SalesArea->add_sales_area($this->request->data);
                default:
                    return false;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Get list values.
     */
    public function ajax_get_values_from_list()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CONFIGURATION, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $this->autoRender = false;

            switch ($this->request->data['list']) {
                case ConstantsLists::EQUIPMENT_NAME:
                    $temp = $this->Equipment->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["Equipment"]['id'];
                        $result[$key]['name'] = $value["Equipment"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::EQUIPMENT_TYPE:
                    $temp = $this->EquipmentType->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["EquipmentType"]['id'];
                        $result[$key]['name'] = $value["EquipmentType"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::SOFTWARE_NAME:
                    $temp = $this->Software->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["Software"]['id'];
                        $result[$key]['name'] = $value["Software"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::SOFTWARE_TYPE:
                    $temp = $this->SoftwareType->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["SoftwareType"]['id'];
                        $result[$key]['name'] = $value["SoftwareType"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::SOFTWARE_PROVIDER:
                    $temp = $this->SoftwareManufacture->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["SoftwareManufacture"]['id'];
                        $result[$key]['name'] = $value["SoftwareManufacture"]['name_' . __l()];
                    }
                    return json_encode($result);
                case ConstantsLists::EQUIPMENT_SOFTWARE_BILLING_SCHEDULE:
                    $temp = $this->BillingSchedule->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["BillingSchedule"]['id'];
                        $result[$key]['name'] = $value["BillingSchedule"]['name_' . __l()];
                    }
                    return json_encode($result);
                case ConstantsLists::WEBSITE_NAME:
                    $temp = $this->Website->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["Website"]['id'];
                        $result[$key]['name'] = $value["Website"]['name'];
                    }
                    return json_encode($result);
                case ConstantsLists::COURTESY_CAR_TYPE:
                    $temp = $this->CourtesyCarType->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["CourtesyCarType"]['id'];
                        $result[$key]['name'] = $value["CourtesyCarType"]['name_' . __l()];
                    }
                    return json_encode($result);
                case ConstantsLists::LEAVING_REASON:
                    $temp = $this->LeavingReasonType->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["LeavingReasonType"]['id'];
                        $result[$key]['name'] = $value["LeavingReasonType"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::HOLD_REASON:
                    $temp = $this->HoldReasonType->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["HoldReasonType"]['id'];
                        $result[$key]['name'] = $value["HoldReasonType"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::ANNEX_DETAILS:
                    $temp = $this->AnnexDetail->getDataRegion($aagRegionId);

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["AnnexDetail"]['id'];
                        $result[$key]['name'] = $value["AnnexDetail"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::EMPLOYEE_TYPES:
                    $temp = $this->EmployeeType->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["EmployeeType"]['id'];
                        $result[$key]['name'] = $value["EmployeeType"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::POSTCODES:
                    $temp = $this->Postcode->getDataByAagRegion($aagRegionId);

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["Postcode"]['id'];
                        $result[$key]['name'] = $value["Postcode"]['name'];
                    }
                    return json_encode($result);

                case ConstantsLists::CAMPAING_ENTRIES:
                    $temp = $this->CampaignEntry->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["CampaignEntry"]['id'];
                        $result[$key]['name'] = $value["CampaignEntry"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::ORDER_PRODUCTS:
                    $temp = $this->OrderProduct->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["OrderProduct"]['id'];
                        $result[$key]['name'] = $value["OrderProduct"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::FACILITIES:
                    $temp = $this->Facility->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["Facility"]['id'];
                        $result[$key]['name'] = $value["Facility"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::VALUE_ADD_SUPPLIER:
                    $temp = $this->ValueAddSupplier->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["ValueAddSupplier"]['id'];
                        $result[$key]['name'] = $value["ValueAddSupplier"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::VALUE_ADD_SUPPLIER_TYPE:
                    $temp = $this->ValueAddSupplierType->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["ValueAddSupplierType"]['id'];
                        $result[$key]['name'] = $value["ValueAddSupplierType"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::ROOM_TYPE:
                    $temp = $this->RoomType->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["RoomType"]['id'];
                        $result[$key]['name'] = $value["RoomType"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::STAND_SIZE:
                    $temp = $this->StandSize->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["StandSize"]['id'];
                        $result[$key]['name'] = $value["StandSize"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::COURSE_TYPE:
                    $temp = $this->CourseType->getData(CakeSession::read('Auth.User.aag_region_id'));

                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value['CourseType']['id'];
                        $result[$key]['name'] = $value['CourseType']['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::CITIES:
                    $temp = $this->City->getDataByAagRegion($user['aag_region_id']);
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $province = $this->Province->findById($value['City']['province_id']);
                        $result[$key]['id'] = $value['City']['id'];
                        $result[$key]['name'] = $value['City']['name'];
                        $result[$key]['latitude'] = !empty($value['City']['latitude']) ? $value['City']['latitude'] : '';
                        $result[$key]['longitude'] = !empty($value['City']['longitude']) ? $value['City']['longitude'] : '';
                        $result[$key]['province_id'] = $value['City']['province_id'];
                        $result[$key]['province'] = $province ? $province['Province']['name'] : '';
                    }
                    return json_encode($result);

                case ConstantsLists::VALUES_ADDS:
                    $temp = $this->ValueAdd->getData(CakeSession::read('Auth.User.aag_region_id'));
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["ValueAdd"]['id'];
                        $result[$key]['name'] = $value["ValueAdd"]['name_' . __l()];
                    }
                    return json_encode($result);

                case ConstantsLists::SERVICES_DRIVERS:
                    $temp = $this->ServiceDriver->getDataByAagRegion($user['aag_region_id']);
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $network = $this->Network->findById($value['ServiceDriver']['network_id']);
                        $result[$key]['id'] = $value["ServiceDriver"]['id'];
                        $result[$key]['name'] = $value["ServiceDriver"]['name_' . __l()];
                        $result[$key]['network_id'] = $value['ServiceDriver']['network_id'];
                        $result[$key]['network'] = $network ? $network['Network']['name'] : '';
                    }
                    return json_encode($result);

                case ConstantsLists::REASON_DELEGATE:
                    $temp = $this->ReasonDelegate->getData(CakeSession::read('Auth.User.aag_region_id'));
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["ReasonDelegate"]['id'];
                        $result[$key]['name'] = $value["ReasonDelegate"]['name_' . __l()];
                    }

                    return json_encode($result);

                case ConstantsLists::REASON_DELEGATE_CANCELLED:
                    $temp = $this->ReasonDelegateCancelled->getData(CakeSession::read('Auth.User.aag_region_id'));
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["ReasonDelegateCancelled"]['id'];
                        $result[$key]['name'] = $value["ReasonDelegateCancelled"]['name_' . __l()];
                    }

                    return json_encode($result);

                case ConstantsLists::VENUES_TYPES:
                    $temp = $this->VenueType->getData(CakeSession::read('Auth.User.aag_region_id'));
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["VenueType"]['id'];
                        $result[$key]['name'] = $value["VenueType"]['name_' . __l()];
                    }

                    return json_encode($result);

                case ConstantsLists::REASON_ALLOWANCE:
                    $temp = $this->ReasonAllowance->getData(CakeSession::read('Auth.User.aag_region_id'));
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["ReasonAllowance"]['id'];
                        $result[$key]['name'] = $value["ReasonAllowance"]['name_' . __l()];
                    }

                    return json_encode($result);

                case ConstantsLists::ASSOCIATION_TYPE:
                    $temp = $this->AssociationType->getData(CakeSession::read('Auth.User.aag_region_id'));
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["AssociationType"]['id'];
                        $result[$key]['name'] = $value["AssociationType"]['name_' . __l()];
                    }

                    return json_encode($result);

                case ConstantsLists::NETWORK_CONTRACT_TYPE:
                    $temp = $this->NetworkContractType->getData(CakeSession::read('Auth.User.aag_region_id'));
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["NetworkContractType"]['id'];
                        $result[$key]['name'] = $value["NetworkContractType"]['name_' . __l()];
                    }

                    return json_encode($result);

                case ConstantsLists::ORDER_TYPES_PRODUCTS:
                    $temp = $this->OrderTypeProduct->getData(CakeSession::read('Auth.User.aag_region_id'));
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["OrderTypeProduct"]['id'];
                        $result[$key]['order_type_id'] = $value["OrderTypeProduct"]['order_type_id'];
                        $result[$key]['order_product_id'] = $value["OrderTypeProduct"]['order_product_id'];
                        $result[$key]['order_type_name'] = $value["OrderTypes"]['name_' . __l()];
                        $result[$key]['order_product_name'] = $value["OrderProducts"]['name_' . __l()];
                    }

                    $result['OrderType'] = $this->OrderType->search_list();
                    $result['OrderProduct'] = $this->OrderProduct->search_list(CakeSession::read('Auth.User.aag_region_id'));

                    return json_encode($result);

                case ConstantsLists::ORDER_TYPES:
                    $temp = $this->OrderType->getData(CakeSession::read('Auth.User.aag_region_id'));
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["OrderType"]['id'];
                        $result[$key]['name'] = $value["OrderType"]['name_' . __l()];
                    }

                    return json_encode($result);

                case ConstantsLists::APPROVED_GARAGE_CITY:
                    $network_id = NETWORK_ID_AGN;
                    $temp = $this->NetworkCity->getData($network_id);
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value['NetworkCity']['id'];
                        $result[$key]['city_name'] = $value['City']['name'];
                        $result[$key]['province_name'] = $value['Province']['name'];
                        $result[$key]['country_name'] = $value['Country']['name'];
                    }

                    $conditions = array('Country.aag_region_id' => $aagRegionId);
                    $result['Country'] = $this->Country->get_list_conditions($conditions);
                    $result['City'] = $this->City->getListByAagRegion($aagRegionId);

                    return json_encode($result);

                case ConstantsLists::GARAGEVERGELIJKER_CITY:
                    $network_id = NETWORK_ID_GV;
                    $temp = $this->NetworkCity->getData($network_id);
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value['NetworkCity']['id'];
                        $result[$key]['city_name'] = $value['City']['name'];
                        $result[$key]['province_name'] = $value['Province']['name'];
                        $result[$key]['country_name'] = $value['Country']['name'];
                    }

                    $conditions = array('Country.aag_region_id' => $aagRegionId);
                    $result['Country'] = $this->Country->get_list_conditions($conditions);
                    $result['City'] = $this->City->getListByAagRegion($aagRegionId);

                    return json_encode($result);

                case ConstantsLists::GARAGE_CHECKER_CITY:
                    $network_id = NETWORK_ID_GC;
                    $temp = $this->NetworkCity->getData($network_id);
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value['NetworkCity']['id'];
                        $result[$key]['city_name'] = $value['City']['name'];
                        $result[$key]['province_name'] = $value['Province']['name'];
                        $result[$key]['country_name'] = $value['Country']['name'];
                    }

                    $conditions = array('Country.aag_region_id' => $aagRegionId);
                    $result['Country'] = $this->Country->get_list_conditions($conditions);
                    $result['City'] = $this->City->getListByAagRegion($aagRegionId);

                    return json_encode($result);

                case ConstantsLists::APPROVED_GARAGE_SERVICE_TO_DRIVER:
                    $network_id = NETWORK_ID_AGN;
                    $temp = $this->ServiceDriver->getDataForNetworkServiceDriverList($network_id);
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["ServiceDriver"]['id'];
                        $result[$key]['name'] = $value["ServiceDriver"]['name_' . __l()];
                    }

                    return json_encode($result);

                case ConstantsLists::GARAGEVERGELIJKER_SERVICE_TO_DRIVER:
                    $network_id = NETWORK_ID_GV;
                    $temp = $this->ServiceDriver->getDataForNetworkServiceDriverList($network_id);
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["ServiceDriver"]['id'];
                        $result[$key]['name'] = $value["ServiceDriver"]['name_' . __l()];
                    }

                    return json_encode($result);

                case ConstantsLists::GARAGE_CHECKER_SERVICE_TO_DRIVER:
                    $network_id = NETWORK_ID_GC;
                    $temp = $this->ServiceDriver->getDataForNetworkServiceDriverList($network_id);
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["ServiceDriver"]['id'];
                        $result[$key]['name'] = $value["ServiceDriver"]['name_' . __l()];
                    }

                    return json_encode($result);

                case ConstantsLists::SALES_AREA:
                    $temp = $this->SalesArea->getData(CakeSession::read('Auth.User.aag_region_id'));
                    $result = array();
                    foreach ($temp as $key => $value) {
                        $result[$key]['id'] = $value["SalesArea"]['id'];
                        $result[$key]['name'] = $value["SalesArea"]['name_' . __l()];
                    }
                    return json_encode($result);

                default:
                    return json_encode(array());
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Get list value.
     */
    public function ajax_get_data_from_value()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CONFIGURATION, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $this->autoRender = false;

            switch ($this->request->data['list']) {
                case ConstantsLists::EQUIPMENT_NAME:
                    $temp = $this->Equipment->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["Equipment"]['id'];
                    $result['name'] = $temp[0]["Equipment"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::EQUIPMENT_TYPE:
                    $temp = $this->EquipmentType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["EquipmentType"]['id'];
                    $result['name'] = $temp[0]["EquipmentType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::SOFTWARE_NAME:
                    $temp = $this->Software->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["Software"]['id'];
                    $result['name'] = $temp[0]["Software"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::SOFTWARE_TYPE:
                    $temp = $this->SoftwareType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["SoftwareType"]['id'];
                    $result['name'] = $temp[0]["SoftwareType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::SOFTWARE_PROVIDER:
                    $temp = $this->SoftwareManufacture->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["SoftwareManufacture"]['id'];
                    $result['name'] = $temp[0]["SoftwareManufacture"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::EQUIPMENT_SOFTWARE_BILLING_SCHEDULE:
                    $temp = $this->BillingSchedule->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["BillingSchedule"]['id'];
                    $result['name'] = $temp[0]["BillingSchedule"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::WEBSITE_NAME:
                    $temp = $this->Website->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["Website"]['id'];
                    $result['name'] = $temp[0]["Website"]['name'];
                    return json_encode($result);

                case ConstantsLists::COURTESY_CAR_TYPE:
                    $temp = $this->CourtesyCarType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["CourtesyCarType"]['id'];
                    $result['name'] = $temp[0]["CourtesyCarType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::LEAVING_REASON:
                    $temp = $this->LeavingReasonType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["LeavingReasonType"]['id'];
                    $result['name'] = $temp[0]["LeavingReasonType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::HOLD_REASON:
                    $temp = $this->HoldReasonType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["HoldReasonType"]['id'];
                    $result['name'] = $temp[0]["HoldReasonType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::ANNEX_DETAILS:
                    $temp = $this->AnnexDetail->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["AnnexDetail"]['id'];
                    $result['name'] = $temp[0]["AnnexDetail"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::EMPLOYEE_TYPES:
                    $temp = $this->EmployeeType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["EmployeeType"]['id'];
                    $result['name'] = $temp[0]["EmployeeType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::POSTCODES:
                    $temp = $this->Postcode->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["Postcode"]['id'];
                    $result['name'] = $temp[0]["Postcode"]['name'];
                    return json_encode($result);

                case ConstantsLists::FACILITIES:
                    $temp = $this->Facility->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["Facility"]['id'];
                    $result['name'] = $temp[0]["Facility"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::VALUE_ADD_SUPPLIER:
                    $temp = $this->ValueAddSupplier->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["ValueAddSupplier"]['id'];
                    $result['name'] = $temp[0]["ValueAddSupplier"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::VALUE_ADD_SUPPLIER_TYPE:
                    $temp = $this->ValueAddSupplierType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["ValueAddSupplierType"]['id'];
                    $result['name'] = $temp[0]["ValueAddSupplierType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::CAMPAING_ENTRIES:
                    $temp = $this->CampaignEntry->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["CampaignEntry"]['id'];
                    $result['name'] = $temp[0]["CampaignEntry"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::ORDER_PRODUCTS:
                    $temp = $this->OrderProduct->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["OrderProduct"]['id'];
                    $result['name'] = $temp[0]["OrderProduct"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::ROOM_TYPE:
                    $temp = $this->RoomType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["RoomType"]['id'];
                    $result['name'] = $temp[0]["RoomType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::STAND_SIZE:
                    $temp = $this->StandSize->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["StandSize"]['id'];
                    $result['name'] = $temp[0]["StandSize"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::COURSE_TYPE:
                    $temp = $this->CourseType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["CourseType"]['id'];
                    $result['name'] = $temp[0]["CourseType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::CITIES:
                    $temp = $this->City->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["City"]['id'];
                    $result['name'] = $temp[0]["City"]['name'];
                    $result['latitude'] = $temp[0]["City"]['latitude'];
                    $result['longitude'] = $temp[0]["City"]['longitude'];
                    $result['province_id'] = $temp[0]["City"]['province_id'];
                    return json_encode($result);

                case ConstantsLists::VALUES_ADDS:
                    $temp = $this->ValueAdd->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["ValueAdd"]['id'];
                    $result['name'] = $temp[0]["ValueAdd"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::REASON_DELEGATE:
                    $temp = $this->ReasonDelegate->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["ReasonDelegate"]['id'];
                    $result['name'] = $temp[0]["ReasonDelegate"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::SERVICES_DRIVERS:
                    $temp = $this->ServiceDriver->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["ServiceDriver"]['id'];
                    $result['name'] = $temp[0]["ServiceDriver"]['name_' . __l()];
                    $result['network_id'] = $temp[0]["ServiceDriver"]['network_id'];
                    return json_encode($result);

                case ConstantsLists::REASON_DELEGATE_CANCELLED:
                    $temp = $this->ReasonDelegateCancelled->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["ReasonDelegateCancelled"]['id'];
                    $result['name'] = $temp[0]["ReasonDelegateCancelled"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::VENUES_TYPES:
                    $temp = $this->VenueType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["VenueType"]['id'];
                    $result['name'] = $temp[0]["VenueType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::REASON_ALLOWANCE:
                    $temp = $this->ReasonAllowance->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["ReasonAllowance"]['id'];
                    $result['name'] = $temp[0]["ReasonAllowance"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::ASSOCIATION_TYPE:
                    $temp = $this->AssociationType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["AssociationType"]['id'];
                    $result['name'] = $temp[0]["AssociationType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::NETWORK_CONTRACT_TYPE:
                    $temp = $this->NetworkContractType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["NetworkContractType"]['id'];
                    $result['name'] = $temp[0]["NetworkContractType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::ORDER_TYPES_PRODUCTS:
                    $temp = $this->OrderTypeProduct->findAllById($this->request->data['value_id']);
                    $orderType = $this->OrderType->findById($temp[0]["OrderTypeProduct"]['order_type_id']);
                    $orderProduct = $this->OrderProduct->findById($temp[0]["OrderTypeProduct"]['order_product_id']);
                    $result['id'] = $temp[0]["OrderTypeProduct"]['id'];
                    $result['order_type_id'] = $temp[0]["OrderTypeProduct"]['order_type_id'];
                    $result['order_product_id'] = $temp[0]["OrderTypeProduct"]['order_product_id'];
                    $result['order_type_name'] = $orderType["OrderType"]['name_' . __l()];
                    $result['order_product_name'] = $orderProduct["OrderProduct"]['name_' . __l()];
                    $result['OrderType'] = $this->OrderType->search_list();
                    $result['OrderProduct'] = $this->OrderProduct->search_list(CakeSession::read('Auth.User.aag_region_id'));
                    return json_encode($result);

                case ConstantsLists::ORDER_TYPES:
                    $temp = $this->OrderType->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["OrderType"]['id'];
                    $result['name'] = $temp[0]["OrderType"]['name_' . __l()];
                    return json_encode($result);

                case ConstantsLists::APPROVED_GARAGE_CITY:
                case ConstantsLists::GARAGEVERGELIJKER_CITY:
                case ConstantsLists::GARAGE_CHECKER_CITY:
                    $temp = $this->NetworkCity->findAllById($this->request->data['value_id']);
                    $result['network_id'] = $temp[0]["NetworkCity"]['network_id'];
                    $result['city_id'] = $temp[0]["NetworkCity"]['city_id'];
                    return json_encode($result);

                case ConstantsLists::SALES_AREA:
                    $temp = $this->SalesArea->findAllById($this->request->data['value_id']);
                    $result['id'] = $temp[0]["SalesArea"]['id'];
                    $result['name'] = $temp[0]["SalesArea"]['name_' . __l()];
                    return json_encode($result);

                default:
                    return json_encode(array());
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete list value. Only for Admin.
     */
    public function ajax_delete_value()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CONFIGURATION, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE) &&
            $roleId == ConstantsRoles::ADMIN
        ) {
            $this->autoRender = false;
            switch ($this->request->data['list']) {
                case ConstantsLists::EQUIPMENT_NAME:
                    if ($this->GarageEquipment->findAllByEquipmentId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->Equipment->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::EQUIPMENT_TYPE:
                    if ($this->GarageEquipment->findAllByEquipmentTypeId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->EquipmentType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::SOFTWARE_NAME:
                    if (
                        $this->GarageSoftware->findAllBySoftwareId($this->request->data['value_id']) ||
                        $this->DistributorSoftware->findAllBySoftwareId($this->request->data['value_id'])
                    ) {
                        return false;
                    } else {
                        $this->Software->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::SOFTWARE_TYPE:
                    if (
                        $this->GarageSoftware->findAllBySoftwareTypeId($this->request->data['value_id']) ||
                        $this->DistributorSoftware->findAllBySoftwareTypeId($this->request->data['value_id'])
                    ) {
                        return false;
                    } else {
                        $this->SoftwareType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::SOFTWARE_PROVIDER:
                    if (
                        $this->GarageSoftware->findAllBySoftwareManufactureId($this->request->data['value_id']) ||
                        $this->DistributorSoftware->findAllBySoftwareManufactureId($this->request->data['value_id'])
                    ) {
                        return false;
                    } else {
                        $this->SoftwareManufacture->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::EQUIPMENT_SOFTWARE_BILLING_SCHEDULE:
                    if (
                        $this->GarageEquipment->findAllByBillingScheduleId($this->request->data['value_id']) ||
                        $this->GarageSoftware->findAllByBillingScheduleId($this->request->data['value_id'])
                    ) {
                        return false;
                    } else {
                        $this->BillingSchedule->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::WEBSITE_NAME:
                    if ($this->GarageWebsite->findAllByWebsiteId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->Website->delete($this->request->data['value_id']);
                        return true;
                    }
                case ConstantsLists::COURTESY_CAR_TYPE:
                    if ($this->CourtesyCarType->findAllById($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->CourtesyCarType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::LEAVING_REASON:
                    if (
                        $this->GarageNetwork->findAllByReasonLeavingId($this->request->data['value_id']) ||
                        $this->GarageAgreement->findAllByReasonLeavingId($this->request->data['value_id'])
                    ) {
                        return false;
                    } else {
                        $this->LeavingReasonType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::HOLD_REASON:
                    if (
                        $this->GarageNetwork->findAllByReasonHoldId($this->request->data['value_id']) ||
                        $this->GarageAgreement->findAllByReasonHoldId($this->request->data['value_id'])
                    ) {
                        return false;
                    } else {
                        $this->HoldReasonType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::ANNEX_DETAILS:
                    if ($this->GarageNetwork->findAllByAnnexDetailId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->AnnexDetail->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::EMPLOYEE_TYPES:
                    if ($this->GarageEmployee->findAllByEmployeeTypeId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->EmployeeType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::POSTCODES:
                    if ($this->GarageB2bPostcode->findAllByPostcodeId($this->request->data['value_id']) || $this->GarageB2cPostcode->findAllByPostcodeId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->Postcode->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::CAMPAING_ENTRIES:
                    if ($this->GarageCampaign->findAllByGarageCampaignId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->CampaignEntry->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::ORDER_PRODUCTS:
                    if ($this->GarageProduct->findAllByProductId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->OrderProduct->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::FACILITIES:
                    if ($this->GarageFacility->findAllByFacilityId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->Facility->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::VALUE_ADD_SUPPLIER:
                    if ($this->GarageValueAddSupplier->findAllByValueAddSupplierId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->ValueAddSupplier->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::VALUE_ADD_SUPPLIER_TYPE:
                    if ($this->GarageValueAddSupplier->findAllByValueAddSupplierTypeId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->ValueAddSupplierType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::ROOM_TYPE:
                    if ($this->ConferenceDelegate->findAllByRoomTypeId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->RoomType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::STAND_SIZE:
                    if ($this->ConferenceDelegate->findAllByStandSizeId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->StandSize->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::COURSE_TYPE:
                    if ($this->TrainingCourse->findAllByCourseTypeId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->CourseType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::CITIES:
                    if ($this->Garage->findByCityId($this->request->data['value_id'])) {
                        return false;
                    } elseif ($this->NetworkCity->findByCityId($this->request->data['value_id'])) {
                        return 2;
                    } else {
                        $this->City->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::VALUES_ADDS:
                    if ($this->GarageValueAdd->findAllByValueAddId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->ValueAdd->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::REASON_DELEGATE:
                    if ($this->TrainingDelegate->findAllByReasonDelegateId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->ReasonDelegate->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::SERVICES_DRIVERS:
                    if ($this->GarageNetworkServiceDriver->findByServiceDriverId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->ServiceDriver->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::REASON_DELEGATE_CANCELLED:
                    if ($this->TrainingDelegate->findAllByReasonCancelledId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->ReasonDelegateCancelled->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::VENUES_TYPES:
                    if ($this->Venue->findAllByVenueTypeId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->VenueType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::REASON_ALLOWANCE:
                    if ($this->TrainingCreditNetwork->findAllByReasonAllowanceId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->ReasonAllowance->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::ASSOCIATION_TYPE:
                    if ($this->Distributor->findAllByAssociationTypeId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->AssociationType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::NETWORK_CONTRACT_TYPE:
                    if ($this->GarageNetwork->findAllByNetworkContractTypeId($this->request->data['value_id'])) {
                        return false;
                    } else {
                        $this->NetworkContractType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::ORDER_TYPES_PRODUCTS:
                    $this->OrderTypeProduct->delete($this->request->data['value_id']);
                    return true;

                case ConstantsLists::ORDER_TYPES:
                    if ($this->OrderTypeProduct->findAllByOrderTypeIdAndAagRegionId(
                        $this->request->data['value_id'],
                        CakeSession::read('Auth.User.aag_region_id')
                    )) {
                        return false;
                    } else {
                        $this->OrderType->delete($this->request->data['value_id']);
                        return true;
                    }

                case ConstantsLists::APPROVED_GARAGE_CITY:
                case ConstantsLists::GARAGEVERGELIJKER_CITY:
                case ConstantsLists::GARAGE_CHECKER_CITY:
                    $this->NetworkCity->delete($this->request->data['value_id']);
                    return true;

                case ConstantsLists::SALES_AREA:
                    if (
                        $this->Garage->findAllBySalesAreaId($this->request->data['value_id']) ||
                        $this->Distributor->findAllBySalesAreaId($this->request->data['value_id']) ||
                        $this->Venue->findAllBySalesAreaId($this->request->data['value_id'])
                    ) {
                        return false;
                    } else {
                        $this->SalesArea->delete($this->request->data['value_id']);
                        return true;
                    }

                default:
                    return false;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit list value. Only for Admin.
     */
    public function ajax_edit_value()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CONFIGURATION, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE) &&
            $roleId == ConstantsRoles::ADMIN
        ) {
            $this->autoRender = false;

            switch ($this->request->data['list']) {
                case ConstantsLists::EQUIPMENT_NAME:
                    $this->request->data['Equipment']['name_en'] = $this->request->data['name'];
                    $this->request->data['Equipment']['name_fr'] = $this->request->data['name'];
                    $this->request->data['Equipment']['name_de'] = $this->request->data['name'];
                    $this->request->data['Equipment']['name_nl'] = $this->request->data['name'];
                    $this->request->data['Equipment']['name_es'] = $this->request->data['name'];
                    $this->request->data['Equipment']['id'] = $this->request->data['value_id'];
                    return $this->Equipment->edit_equipment($this->request->data);

                case ConstantsLists::EQUIPMENT_TYPE:
                    $this->request->data['EquipmentType']['name_en'] = $this->request->data['name'];
                    $this->request->data['EquipmentType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['EquipmentType']['name_de'] = $this->request->data['name'];
                    $this->request->data['EquipmentType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['EquipmentType']['name_es'] = $this->request->data['name'];
                    $this->request->data['EquipmentType']['id'] = $this->request->data['value_id'];
                    return $this->EquipmentType->edit_equipment($this->request->data);

                case ConstantsLists::SOFTWARE_NAME:
                    $this->request->data['Software']['name_en'] = $this->request->data['name'];
                    $this->request->data['Software']['name_fr'] = $this->request->data['name'];
                    $this->request->data['Software']['name_de'] = $this->request->data['name'];
                    $this->request->data['Software']['name_nl'] = $this->request->data['name'];
                    $this->request->data['Software']['name_es'] = $this->request->data['name'];
                    $this->request->data['Software']['id'] = $this->request->data['value_id'];
                    return $this->Software->edit_software($this->request->data);

                case ConstantsLists::SOFTWARE_TYPE:
                    $this->request->data['SoftwareType']['name_en'] = $this->request->data['name'];
                    $this->request->data['SoftwareType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['SoftwareType']['name_de'] = $this->request->data['name'];
                    $this->request->data['SoftwareType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['SoftwareType']['name_es'] = $this->request->data['name'];
                    $this->request->data['SoftwareType']['id'] = $this->request->data['value_id'];
                    return $this->SoftwareType->edit_software_type($this->request->data);

                case ConstantsLists::SOFTWARE_PROVIDER:
                    $this->request->data['SoftwareManufacture']['name_en'] = $this->request->data['name'];
                    $this->request->data['SoftwareManufacture']['name_fr'] = $this->request->data['name'];
                    $this->request->data['SoftwareManufacture']['name_de'] = $this->request->data['name'];
                    $this->request->data['SoftwareManufacture']['name_nl'] = $this->request->data['name'];
                    $this->request->data['SoftwareManufacture']['name_es'] = $this->request->data['name'];
                    $this->request->data['SoftwareManufacture']['id'] = $this->request->data['value_id'];
                    return $this->SoftwareManufacture->edit_software_manufacture($this->request->data);

                case ConstantsLists::EQUIPMENT_SOFTWARE_BILLING_SCHEDULE:
                    $this->request->data['BillingSchedule']['name_en'] = $this->request->data['name'];
                    $this->request->data['BillingSchedule']['name_fr'] = $this->request->data['name'];
                    $this->request->data['BillingSchedule']['name_de'] = $this->request->data['name'];
                    $this->request->data['BillingSchedule']['name_nl'] = $this->request->data['name'];
                    $this->request->data['BillingSchedule']['name_es'] = $this->request->data['name'];
                    $this->request->data['BillingSchedule']['id'] = $this->request->data['value_id'];
                    return $this->BillingSchedule->edit_billing_schedule($this->request->data);

                case ConstantsLists::WEBSITE_NAME:
                    $this->request->data['Website']['name'] = $this->request->data['name'];
                    $this->request->data['Website']['id'] = $this->request->data['value_id'];
                    return $this->Website->edit_website($this->request->data);

                case ConstantsLists::COURTESY_CAR_TYPE:
                    $this->request->data['CourtesyCarType']['name_en'] = $this->request->data['name'];
                    $this->request->data['CourtesyCarType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['CourtesyCarType']['name_de'] = $this->request->data['name'];
                    $this->request->data['CourtesyCarType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['CourtesyCarType']['name_es'] = $this->request->data['name'];
                    $this->request->data['CourtesyCarType']['id'] = $this->request->data['value_id'];
                    return $this->CourtesyCarType->edit_courtesy_car_type($this->request->data);

                case ConstantsLists::LEAVING_REASON:
                    $this->request->data['LeavingReasonType']['name_en'] = $this->request->data['name'];
                    $this->request->data['LeavingReasonType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['LeavingReasonType']['name_de'] = $this->request->data['name'];
                    $this->request->data['LeavingReasonType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['LeavingReasonType']['name_es'] = $this->request->data['name'];
                    $this->request->data['LeavingReasonType']['id'] = $this->request->data['value_id'];
                    return $this->LeavingReasonType->edit_leaving_reason_type($this->request->data);

                case ConstantsLists::HOLD_REASON:

                    $this->request->data['HoldReasonType']['name_en'] = $this->request->data['name'];
                    $this->request->data['HoldReasonType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['HoldReasonType']['name_de'] = $this->request->data['name'];
                    $this->request->data['HoldReasonType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['HoldReasonType']['name_es'] = $this->request->data['name'];
                    $this->request->data['HoldReasonType']['id'] = $this->request->data['value_id'];
                    return $this->HoldReasonType->edit_hold_reason_type($this->request->data);

                case ConstantsLists::ANNEX_DETAILS:
                    $this->request->data['AnnexDetail']['name_en'] = $this->request->data['name'];
                    $this->request->data['AnnexDetail']['name_fr'] = $this->request->data['name'];
                    $this->request->data['AnnexDetail']['name_de'] = $this->request->data['name'];
                    $this->request->data['AnnexDetail']['name_nl'] = $this->request->data['name'];
                    $this->request->data['AnnexDetail']['name_es'] = $this->request->data['name'];
                    $this->request->data['AnnexDetail']['id'] = $this->request->data['value_id'];
                    return $this->AnnexDetail->edit_annex_detail($this->request->data);

                case ConstantsLists::EMPLOYEE_TYPES:
                    $this->request->data['EmployeeType']['name_en'] = $this->request->data['name'];
                    $this->request->data['EmployeeType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['EmployeeType']['name_de'] = $this->request->data['name'];
                    $this->request->data['EmployeeType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['EmployeeType']['name_es'] = $this->request->data['name'];
                    $this->request->data['EmployeeType']['id'] = $this->request->data['value_id'];
                    return $this->EmployeeType->edit_employee_type($this->request->data);

                case ConstantsLists::POSTCODES:
                    $this->request->data['Postcode']['name'] = $this->request->data['name'];
                    $this->request->data['Postcode']['id'] = $this->request->data['value_id'];
                    return $this->Postcode->edit_postcode($this->request->data);

                case ConstantsLists::CAMPAING_ENTRIES:
                    $this->request->data['CampaignEntry']['name_en'] = $this->request->data['name'];
                    $this->request->data['CampaignEntry']['name_fr'] = $this->request->data['name'];
                    $this->request->data['CampaignEntry']['name_de'] = $this->request->data['name'];
                    $this->request->data['CampaignEntry']['name_nl'] = $this->request->data['name'];
                    $this->request->data['CampaignEntry']['name_es'] = $this->request->data['name'];
                    $this->request->data['CampaignEntry']['id'] = $this->request->data['value_id'];
                    return $this->CampaignEntry->edit_campaign_entries($this->request->data);

                case ConstantsLists::ORDER_PRODUCTS:
                    $this->request->data['OrderProduct']['name_en'] = $this->request->data['name'];
                    $this->request->data['OrderProduct']['name_fr'] = $this->request->data['name'];
                    $this->request->data['OrderProduct']['name_de'] = $this->request->data['name'];
                    $this->request->data['OrderProduct']['name_nl'] = $this->request->data['name'];
                    $this->request->data['OrderProduct']['name_es'] = $this->request->data['name'];
                    $this->request->data['OrderProduct']['id'] = $this->request->data['value_id'];
                    return $this->OrderProduct->edit_order_products($this->request->data);

                case ConstantsLists::FACILITIES:
                    $this->request->data['Facility']['name_en'] = $this->request->data['name'];
                    $this->request->data['Facility']['name_fr'] = $this->request->data['name'];
                    $this->request->data['Facility']['name_de'] = $this->request->data['name'];
                    $this->request->data['Facility']['name_nl'] = $this->request->data['name'];
                    $this->request->data['Facility']['name_es'] = $this->request->data['name'];
                    $this->request->data['Facility']['id'] = $this->request->data['value_id'];
                    return $this->Facility->edit_facility($this->request->data);

                case ConstantsLists::VALUE_ADD_SUPPLIER:
                    $this->request->data['ValueAddSupplier']['name_en'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplier']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplier']['name_de'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplier']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplier']['name_es'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplier']['id'] = $this->request->data['value_id'];
                    return $this->ValueAddSupplier->edit_value_add_supplier($this->request->data);

                case ConstantsLists::VALUE_ADD_SUPPLIER_TYPE:
                    $this->request->data['ValueAddSupplierType']['name_en'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplierType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplierType']['name_de'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplierType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplierType']['name_es'] = $this->request->data['name'];
                    $this->request->data['ValueAddSupplierType']['id'] = $this->request->data['value_id'];
                    return $this->ValueAddSupplierType->edit_value_add_supplier_type($this->request->data);

                case ConstantsLists::ROOM_TYPE:
                    $this->request->data['RoomType']['name_en'] = $this->request->data['name'];
                    $this->request->data['RoomType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['RoomType']['name_de'] = $this->request->data['name'];
                    $this->request->data['RoomType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['RoomType']['name_es'] = $this->request->data['name'];
                    $this->request->data['RoomType']['id'] = $this->request->data['value_id'];
                    return $this->RoomType->edit_room_type($this->request->data);

                case ConstantsLists::STAND_SIZE:
                    $this->request->data['StandSize']['name_en'] = $this->request->data['name'];
                    $this->request->data['StandSize']['name_fr'] = $this->request->data['name'];
                    $this->request->data['StandSize']['name_de'] = $this->request->data['name'];
                    $this->request->data['StandSize']['name_nl'] = $this->request->data['name'];
                    $this->request->data['StandSize']['name_es'] = $this->request->data['name'];
                    $this->request->data['StandSize']['id'] = $this->request->data['value_id'];
                    return $this->StandSize->edit_stand_size($this->request->data);

                case ConstantsLists::COURSE_TYPE:
                    $this->request->data['CourseType']['name_en'] = $this->request->data['name'];
                    $this->request->data['CourseType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['CourseType']['name_de'] = $this->request->data['name'];
                    $this->request->data['CourseType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['CourseType']['name_es'] = $this->request->data['name'];
                    $this->request->data['CourseType']['id'] = $this->request->data['value_id'];
                    return $this->CourseType->edit_course_type($this->request->data);

                case ConstantsLists::CITIES:
                    $this->request->data['City']['name'] = $this->request->data['name'];
                    $this->request->data['City']['latitude'] = $this->request->data['latitude'];
                    $this->request->data['City']['longitude'] = $this->request->data['longitude'];
                    $this->request->data['City']['province_id'] = $this->request->data['province_id'];
                    $this->request->data['City']['id'] = $this->request->data['value_id'];
                    return $this->City->edit_city($this->request->data);

                case ConstantsLists::VALUES_ADDS:
                    $this->request->data['ValueAdd']['name_en'] = $this->request->data['name'];
                    $this->request->data['ValueAdd']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ValueAdd']['name_de'] = $this->request->data['name'];
                    $this->request->data['ValueAdd']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ValueAdd']['name_es'] = $this->request->data['name'];
                    $this->request->data['ValueAdd']['id'] = $this->request->data['value_id'];
                    return $this->ValueAdd->edit_values_adds($this->request->data);

                case ConstantsLists::REASON_DELEGATE:
                    $this->request->data['ReasonDelegate']['name_en'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegate']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegate']['name_de'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegate']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegate']['name_es'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegate']['id'] = $this->request->data['value_id'];
                    return $this->ReasonDelegate->edit_reason_delegate($this->request->data);

                case ConstantsLists::SERVICES_DRIVERS:
                    $this->request->data['ServiceDriver']['network_id'] = $this->request->data['network_id'];
                    $this->request->data['ServiceDriver']['name_en'] = $this->request->data['name'];
                    $this->request->data['ServiceDriver']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ServiceDriver']['name_de'] = $this->request->data['name'];
                    $this->request->data['ServiceDriver']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ServiceDriver']['name_es'] = $this->request->data['name'];
                    $this->request->data['ServiceDriver']['id'] = $this->request->data['value_id'];
                    return $this->ServiceDriver->edit_services_drivers($this->request->data);

                case ConstantsLists::REASON_DELEGATE_CANCELLED:
                    $this->request->data['ReasonDelegateCancelled']['name_en'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegateCancelled']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegateCancelled']['name_de'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegateCancelled']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegateCancelled']['name_es'] = $this->request->data['name'];
                    $this->request->data['ReasonDelegateCancelled']['id'] = $this->request->data['value_id'];
                    return $this->ReasonDelegateCancelled->edit_reason_delegate($this->request->data);

                case ConstantsLists::VENUES_TYPES:
                    $this->request->data['VenueType']['name_en'] = $this->request->data['name'];
                    $this->request->data['VenueType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['VenueType']['name_de'] = $this->request->data['name'];
                    $this->request->data['VenueType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['VenueType']['name_es'] = $this->request->data['name'];
                    $this->request->data['VenueType']['id'] = $this->request->data['value_id'];
                    return $this->VenueType->edit_venue_type($this->request->data);

                case ConstantsLists::REASON_ALLOWANCE:
                    $this->request->data['ReasonAllowance']['name_en'] = $this->request->data['name'];
                    $this->request->data['ReasonAllowance']['name_fr'] = $this->request->data['name'];
                    $this->request->data['ReasonAllowance']['name_de'] = $this->request->data['name'];
                    $this->request->data['ReasonAllowance']['name_nl'] = $this->request->data['name'];
                    $this->request->data['ReasonAllowance']['name_es'] = $this->request->data['name'];
                    $this->request->data['ReasonAllowance']['id'] = $this->request->data['value_id'];
                    return $this->ReasonAllowance->edit_reason_allowance($this->request->data);

                case ConstantsLists::ASSOCIATION_TYPE:
                    $this->request->data['AssociationType']['name_en'] = $this->request->data['name'];
                    $this->request->data['AssociationType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['AssociationType']['name_de'] = $this->request->data['name'];
                    $this->request->data['AssociationType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['AssociationType']['name_es'] = $this->request->data['name'];
                    $this->request->data['AssociationType']['id'] = $this->request->data['value_id'];
                    return $this->AssociationType->edit_association_type($this->request->data);

                case ConstantsLists::NETWORK_CONTRACT_TYPE:
                    $this->request->data['NetworkContractType']['name_en'] = $this->request->data['name'];
                    $this->request->data['NetworkContractType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['NetworkContractType']['name_de'] = $this->request->data['name'];
                    $this->request->data['NetworkContractType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['NetworkContractType']['name_es'] = $this->request->data['name'];
                    $this->request->data['NetworkContractType']['id'] = $this->request->data['value_id'];
                    return $this->NetworkContractType->edit_network_contract_type($this->request->data);

                case ConstantsLists::ORDER_TYPES_PRODUCTS:
                    $this->request->data['OrderTypeProduct']['order_type_id'] = $this->request->data['order_type_id'];
                    $this->request->data['OrderTypeProduct']['order_product_id'] = $this->request->data['order_product_id'];
                    $this->request->data['OrderTypeProduct']['id'] = $this->request->data['value_id'];

                    return $this->OrderTypeProduct->edit_order_type_product($this->request->data);

                case ConstantsLists::ORDER_TYPES:
                    $this->request->data['OrderType']['name_en'] = $this->request->data['name'];
                    $this->request->data['OrderType']['name_fr'] = $this->request->data['name'];
                    $this->request->data['OrderType']['name_de'] = $this->request->data['name'];
                    $this->request->data['OrderType']['name_nl'] = $this->request->data['name'];
                    $this->request->data['OrderType']['id'] = $this->request->data['value_id'];

                    return $this->OrderType->edit_order_type($this->request->data);

                case ConstantsLists::SALES_AREA:
                    $this->request->data['SalesArea']['name_en'] = $this->request->data['name'];
                    $this->request->data['SalesArea']['name_fr'] = $this->request->data['name'];
                    $this->request->data['SalesArea']['name_de'] = $this->request->data['name'];
                    $this->request->data['SalesArea']['name_nl'] = $this->request->data['name'];
                    $this->request->data['SalesArea']['name_es'] = $this->request->data['name'];
                    $this->request->data['SalesArea']['id'] = $this->request->data['value_id'];
                    return $this->SalesArea->edit_sales_area($this->request->data);

                default:
                    return false;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Restore default config values.
     */
    public function restore_default_values()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CONFIGURATION, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $defaultsConfigs = Configure::read('DefaultConfig');
            foreach ($defaultsConfigs as $configId => $active) {
                $configTmp = array(
                    'Config' => array(
                        'id' => $configId,
                        'active' =>  $active
                    )
                );
                $this->Config->saveMany($configTmp);
            }

            $this->Config = ClassRegistry::init('Config');
            $aagConfig = $this->Config->get_list_config();
            $aagConfigModuleRegionRole = $this->ConfigModuleRegionRole->get_list_config_module_region_role($aagRegionId, $roleId);
            CakeSession::write('Auth.User.Config', $aagConfig);
            CakeSession::write('Auth.User.Config.Module', $aagConfigModuleRegionRole);

            $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));

            $this->redirect(
                Router::url(array(
                    'controller' => 'config',
                    'action' => 'home'
                ))
            );
        } else {
            throw new UnauthorizedException();
        }
    }
}
