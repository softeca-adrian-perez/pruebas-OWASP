<?php
App::uses('PaginatorUrlGarage', 'View/Helper');
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class GaragesController extends AppController
{
    public $uses = array(
        'Agreement',
        'Garage',
        'Appointment',
        'Country',
        'Contact',
        'ContactList',
        'ContactRegion',
        'Distributor',
        'GarageAgreement',
        'GarageContactList',
        'GarageContactBdm',
        'GarageContactStaff',
        'GarageContactGeneralBranchManager',
        'GarageDistributor',
        'GarageImage',
        'GarageFile',
        'GarageNetwork',
        'GarageComment',
        'GarageService',
        'GarageSoftware',
        'GarageSpecialistMake',
        'GarageVehicle',
        'GarageFigure',
        'GarageFigureDetail',
        'GarageBrand',
        'GarageSale',
        'GarageStatus',
        'GarageVehicleType',
        'GarageVisitFrequency',
        'GarageWebsite',
        'Network',
        'NetworkContractType',
        'NetworkStatus',
        'Position',
        'PositionConfig',
        'PositionConfigNetwork',
        'PostcodeProvince',
        'Province',
        'Region',
        'AagRegion',
        'Service',
        'Software',
        'TradingGroup',
        'Task',
        'TaskFile',
        'User',
        'Vehicle',
        'VehicleType',
        'Website',
        'LogChange',
        'RequestedChange',
        'Alert',
        'SoftwareType',
        'Supplier',
        'SoftwareManufacture',
        'GarageEquipment',
        'Brand',
        'Equipment',
        'EquipmentType',
        'GarageBrand',
        'InsuranceAgreement',
        'EmployeeType',
        'GarageEmployee',
        'WorkshopActivity',
        'GarageWorkshopActivity',
        'GarageCustomerActivity',
        'CustomerActivity',
        'LeavingReasonType',
        'Permission',
        'GroupPermissionPermission',
        'Role',
        'NetworkContactBdm',
        'BillingSchedule',
        'Config',
        'EmployeeType',
        'Postcode',
        'GarageB2bPostcode',
        'GarageB2cPostcode',
        'CampaignEntry',
        'GarageCampaign',
        'Facility',
        'GarageFacility',
        'ValueAddSupplier',
        'ValueAddSupplierType',
        'GarageValueAddSupplier',
        'Order',
        'GarageProduct',
        'City',
        'GarageValueAdd',
        'ValueAdd',
        'RepairMaintenance',
        'TrainingCreditMovement',
        'ActualizarFTPGarage',
        'AnnexDetail',
        'GarageContactBdm',
        'TrainingCreditNetwork',
        'ReasonAllowance',
        'TrainingAllowance',
        'Erp',
        'AssociationType',
        'Language',
        'OrderType',
        'ConfigModuleRegionRole',
        'TrainingDelegate',
        'TrainingPlannedCourse',
        'SalesArea'
    );

    public $components = array(
        'Mpdf.Mpdf',
    );

    /**
     * Garages home.
     */
    public function home()
    {
        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        if (
            $role_id == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                (
                    !in_array($role_id, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ||
                    (
                        $role_id == ConstantsRoles::DISTRIBUTOR &&
                        $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                    )
                )
            )
        ) {
            $networksData = $this->Network->getNetworksConditions($aag_region_id);
            $config = CakeSession::read('Auth.User.Config');
            $contact_id = $user['contact_id'];
            $contact = $this->Contact->findById($contact_id);
            $position_id = $contact['Contact']['position_id'];
            $positions_configs = $this->PositionConfig->findAllByPositionId($position_id);

            if (!empty($positions_configs)) {
                // Check how many networks has this position.
                $networks = array();
                foreach ($positions_configs as $position_config) {
                    if ($position_config['PositionConfig']['all_networks'] == ConstantsBooleans::YES) {
                        $networks = $networksData;
                        break;
                    } else {
                        $position_config_newtworks = $this->PositionConfigNetwork->findAllByPositionConfigId($position_config['PositionConfig']['id']);
                        foreach ($position_config_newtworks as $position_config_newtwork) {
                            $nerwork_id = $position_config_newtwork['PositionConfigNetwork']['network_id'];
                            $network = $this->Network->findById($nerwork_id);
                            if (isset($network['Network'])) {
                                $networks[$nerwork_id] = $network['Network']['name'];
                            }
                        }
                    }
                }
            } else {
                $networks = $networksData;
            }

            $external_agreements = $this->Agreement->external_agreements_list($aag_region_id);
            $internal_agreements = $this->GarageAgreement->internal_agreements_list($aag_region_id);

            $trading_groups = $this->TradingGroup->getTradingGroupGarageConditions($aag_region_id);
            $services = $this->Service->search_list();
            $vehicles_types = $this->VehicleType->search_list();
            $networks_statuses_all = Configure::read('Network_Status');
            foreach ($networks_statuses_all as $key => $network_status) {
                $networks_statuses_all[$key] = __t($network_status);
            }
            $networks_statuses_tmp = $this->GarageNetwork->getStatusFromGarageNetworks();

            $networks_statuses = array();
            foreach ($networks_statuses_tmp as $network_status_tmp) {
                $networks_statuses[$network_status_tmp] = $networks_statuses_all[$network_status_tmp];
            }
            $lists_bdm = $this->GarageContactBdm->getAllBDMContacts($aag_region_id);
            $bdm = array();
            foreach ($lists_bdm as $key => $list_bdm) {
                $id = $list_bdm['Contact']['id'];
                $bdm[$id] = $list_bdm[0]['full_name'];
            }
            $lead_sources = Configure::read('lead_source');
            foreach ($lead_sources as $key => $lead_source) {
                $lead_sources[$key] = __t($lead_source);
            }

            $lists_annex_detail = $this->AnnexDetail->getDataRegion($aag_region_id);
            $annex_detail = array();
            foreach ($lists_annex_detail as $key => $list_annex_detail) {
                $annex_detail[$list_annex_detail['AnnexDetail']['id']] = $list_annex_detail['AnnexDetail']['name_' . __l()];
            }

            $province_list = $this->Garage->Province->find('list');

            $regions = array();
            // super admin can search through all regions
            if ($user['role_id'] == ConstantsRoles::SUPER_ADMIN) {
                $regions = $this->AagRegion->region_list();
            }

            $searcher = $this->request->query;
            $searcher = self::multipleFieldsArrayCheck($searcher);
            $searcher['aag_region_id'] = $aag_region_id;

            $this->request->data['Search'] = $searcher;

            $garages = array();
            $countries = array();

            // if user is super admin or isn't super admin but has AAG region ID set
            if (
                $role_id == ConstantsRoles::SUPER_ADMIN ||
                $role_id != ConstantsRoles::SUPER_ADMIN && !empty($aag_region_id)
            ) {
                $conditionsJoins = $this->Garage->conditionsJoins($searcher);
                $joins = array_merge(
                    array(
                        array(
                            'alias' => 'Province',
                            'table' => 'provinces',
                            'type' => 'LEFT',
                            'conditions' => 'Garage.province_id = Province.id',
                            'fields' => array(
                                'Province.id',
                                'Province.country_id',
                            ),
                        ),
                        array(
                            'alias' => 'Country',
                            'table' => 'countries',
                            'type' => 'LEFT',
                            'conditions' => 'Province.country_id = Country.id',
                            'fields' => array(
                                'Country.id',
                                'Country.country_id',
                            ),
                        ),
                    ),
                    $conditionsJoins
                );

                // Delete conditions searcher - added in joins relations
                $keysSearcherDeleted = array(
                    'trading_group_id',
                    'network_id',
                    'status_id',
                    'annex_detail_id',
                    'bdm_id',
                    'service_id',
                    'distributor_id',
                    'vehicle_type_id',
                    'external_agreements',
                    'internal_agreements'
                );
                foreach ($keysSearcherDeleted as $keySearcherDeleted) {
                    unset($searcher[$keySearcherDeleted]);
                }

                $conditions = $this->Garage->conditions($searcher);
                $conditions[] = $this->User->viewUserGarages($this->Acceso->user());

                $garages = $this->custom_pagination(
                    array(
                        'joins' => $joins,
                        'order' => array(
                            'Garage.name' => 'asc'
                        ),
                        'fields' => array(
                            'Garage.*',
                            'Country.name',
                            'Country.aag_region_id'
                        ),
                        'group' => array('Garage.id'),
                    ),
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Garage',
                    null,
                    'PaginatorOrderCustom'
                );

                foreach ($garages as $key => $garage) {
                    $garageId = $garage['Garage']['id'];
                    $garage_tmp = $this->Garage->findById($garageId);
                    $garages[$key]['Distributors'] = $this->GarageDistributor->findAllByGarageId($garageId);
                    $garages[$key]['Garage']['status'] = $garage_tmp['Garage']['status'];
                    $garages[$key]['FiguresDetail'] = $this->GarageFigureDetail->findByCustomerNo($garage['Garage']['g_number_id']);
                    $garages[$key]['BDMS'] = $this->GarageContactBdm->getBDMByGarage($garageId);

                    if ($garage['Garage']['last_visit']) {
                        $interval = strtotime(date('Y-m-d')) - strtotime($garage['Garage']['last_visit']);
                        $garages[$key]['LatestVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
                    } else {
                        $garages[$key]['LatestVisit'] = '--';
                    }

                    $networkId = $this->AagRegion->get_aag_region_network($garage['Garage']['aag_region_id']);
                    $garageNetwork = $this->GarageNetwork->findAllByGarageIdAndNetworkId($garageId, $networkId);

                    // only if Garage was active and GarageNetwork was live garage access is shown
                    if ($garageNetwork) {
                        // we add all options for networks buttons if there is more than 1 garagenetwork
                        foreach ($garageNetwork as $gn) {
                            $garages[$key]['ShowGarageAccess'][] = $garage['Garage']['status'] == ConstantsGarageStatus::ACTIVE &&
                                $gn['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE;
                            $garages[$key]['NetworkId'][] = $gn['GarageNetwork']['network_id'];
                            $garages[$key]['GarageNetworkId'][] = $gn['GarageNetwork']['id'];
                            $network = $this->Network->findById($gn['GarageNetwork']['network_id']);
                            $garages[$key]['Network'][] = $network['Network']['name'];
                        }
                    }
                }

                $conditions = array('Country.aag_region_id' => $aag_region_id);
                $countries = $this->Country->get_list_conditions($conditions);
                $cities = $this->City->get_list_conditions($conditions);
            }

            $garages_permissions = $this->Permission->getByGroupingPermission(ConstantsGroupingPermissions::GARAGE);

            $distributors = array();
            if (!empty($this->request->query['distributor_id'])) {
                $distributors = $this->Distributor->getDistributorsCompleteNamesByListOfIds($this->request->query['distributor_id'], $aag_region_id);
            }

            $array_city_name = array();
            if (!empty($searcher['city_id'])) {
                $array_city_name = $this->City->getCityCompleteNamesByListOfIds($searcher['city_id']);
            }

            $salesArea = $this->SalesArea->searchListByRegion($aag_region_id);

            $this->set(array(
                'garages' => $garages,
                'trading_groups' => $trading_groups,
                'networks' => $networks,
                'services' => $services,
                'vehicles_types' => $vehicles_types,
                'networks_statuses' => $networks_statuses,
                'lead_sources' => $lead_sources,
                'province_list' => $province_list,
                'distributors' => json_encode($distributors),
                'distributors_list' => array(),
                'regions' => $regions,
                'countries' => $countries,
                'cities' => $cities,
                'bdm' => $bdm,
                'garages_permissions' => $garages_permissions,
                'config' => $config,
                'external_agreements' => $external_agreements,
                'internal_agreements' => $internal_agreements,
                'user' => $user,
                'user_aag_region_id' => $aag_region_id,
                'user_role' => $role_id,
                'annex_detail' => $annex_detail,
                'array_city_name' => json_encode($array_city_name),
                'erp_providers' => $this->Erp->getErpsByAagRegionId($aag_region_id),
                'sales_area' => $salesArea
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Garage view.
     */
    public function view($garage_id)
    {
        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        $garage = $this->Garage->getGarageContactList($garage_id, $aag_region_id);

        if (
            $garage &&
            (
                $role_id == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array($role_id, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            $role_id == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    )
                )
            )
        ) {
            $this->ApiRm = ClassRegistry::init("ApiRm.ApiRm");
            $internal_agreements_rm = $this->ApiRm->get_internal_agreements($garage_id);
            $internal_agreements_alliance = $this->GarageAgreement->get_internal_agreements($garage_id);
            $garage_internal_agreements = $this->GarageAgreement->getInternalAgreementArray($internal_agreements_rm, $internal_agreements_alliance);
            $garage_external_agreements = $this->GarageAgreement->findExternalByGarage($garage_id);

            $contact_lists = $this->ContactList->find('list');

            $status = $garage['Garage']['status'];
            $regions = $this->Region->region_list();

            if ($status != ConstantsGarageStatusDe::POTENTIAL) {
                $lead_sources = Configure::read('lead_source');
                foreach ($lead_sources as $key => $lead_source) {
                    $lead_sources[$key] = __t($lead_source);
                }

                $province_list = $this->Garage->Province->find('list');
                $garage = $this->Garage->findById($garage_id);
                $workshop_activities_garage = $this->GarageWorkshopActivity->getWorkshopActivitiesByGarageId($garage['Garage']['id']);
                $workshop_activities_list = $this->WorkshopActivity->search_list();
                $garage_services = $this->GarageService->findServices($garage_id);
                $garage_vehicle_types = $this->GarageVehicleType->findVehicleTypes($garage_id);
                $garage_vehicles = $this->GarageVehicle->findVehicles($garage_id);
                $garage_vehicle_specialists = $this->GarageSpecialistMake->findVehicles($garage_id);
                $garage_principal_image = $this->Garage->getPrincipalImageDatas($garage_id);
                $software = $this->Software->search_list($garage['Garage']['aag_region_id']);
                $garage_software = $this->GarageSoftware->findAllByGarageId($garage_id);
                $websites = $this->Website->search_list($garage['Garage']['aag_region_id']);
                $garage_websites = $this->GarageWebsite->findAllByGarageId($garage_id);
                $distributors = $this->Distributor->find('list');
                $garage_distributors = $this->GarageDistributor->getAllByGarageId($garage_id);
                $networks = $this->Network->find('all');
                $trading_groups = $this->TradingGroup->find('all');
                $networks_statuses = Configure::read('Network_Status');
                foreach ($networks_statuses as $key => $network_status) {
                    $networks_statuses[$key] = __t($network_status);
                }
                $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);

                $user = $this->Acceso->user();
                $positions_configs = $this->PositionConfig->findAllByPositionId($user['Contact']['position_id']);

                if (!empty($positions_configs)) {
                    // Check how many networks has this position.
                    $garage_networks = array();
                    $garage_internal_networks = array();
                    $garage_external_networks = array();
                    foreach ($positions_configs as $position_config) {
                        if (($position_config['PositionConfig']['all_networks'] == ConstantsBooleans::YES) && ($position_config['PositionConfig']['position_config_type_id'] == ConstantsPositionConfigType::GARAGE)) {
                            $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
                            $garage_internal_networks = $this->GarageNetwork->findInternalByGarage($garage_id);
                            $garage_external_networks = $this->GarageNetwork->findExternalByGarage($garage_id);
                            break;
                        } else {
                            $position_config_newtworks = $this->PositionConfigNetwork->findAllByPositionConfigId($position_config['PositionConfig']['id']);

                            foreach ($position_config_newtworks as $position_config_newtwork) {
                                $network_id = $position_config_newtwork['PositionConfigNetwork']['network_id'];

                                $garage_net = $this->GarageNetwork->findByGarageIdAndNetworkId($garage_id, $network_id);

                                if (!empty($garage_net)) {
                                    $garage_networks[] = $garage_net;
                                }
                            }
                        }
                    }
                    if (empty($garage_net)) {
                        $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
                    }
                }

                $images = $this->GarageImage->findAllByGarageId($garage_id);
                $garage_statuses = Configure::read('Garage_Status');
                foreach ($garage_statuses as $key => $garage_status) {
                    $garage_statuses[$key] = __t($garage_status);
                }

                $garage_contacts_bdm = $this->GarageContactBdm->getSomeByGarageId($garage_id);
                foreach ($garage_contacts_bdm as $key => $garage_contact_bdm) {
                    $garage_contacts_bdm[$key]['Network'] = $this->NetworkContactBdm->findAllNetworksByGarageIdAndContactId($garage_id, $garage_contact_bdm['Contact']['id']);
                }
                $garage_contacts_staff = $this->GarageContactStaff->getSomeByGarageId($garage_id);
                $garage_contacts_general_branch_manager = $this->GarageContactGeneralBranchManager->getContactsByGarageIdLimited($garage_id);
                $garage_comments = $this->GarageComment->getAllByGarageWithUsersLimited($garage_id, ConstantsPagination::SIZE_COMMENTS);
                $positions = $this->Position->search_list();
                $logs_changes = $this->LogChange->get_last_changes_by_garage($garage_id);
                $services = $this->Service->find('all');
                $vehicle_types = $this->VehicleType->find('all');
                $vehicles = $this->Vehicle->find('all');
                $networks_contracts = $this->NetworkContractType->search_list();
                $visit_frequency = $this->GarageVisitFrequency->find('list');
                $software_types = $this->SoftwareType->search_list($garage['Garage']['aag_region_id']);
                $suppliers = $this->Supplier->search_list();
                $software_manufactures = $this->SoftwareManufacture->search_list();
                $garage_equipments = $this->GarageEquipment->findAllByGarageId($garage_id);
                $equipment_types = $this->EquipmentType->search_list($garage['Garage']['aag_region_id']);
                $equipments = $this->Equipment->search_list($garage['Garage']['aag_region_id']);
                $brands = $this->Brand->search_list();
                $parts_brands = $this->Brand->find('all');
                $garages_parts_brands = $this->GarageBrand->findPartBrands($garage_id);
                $insurance_agreements = $this->InsuranceAgreement->find('list');
                $employee_types = $this->EmployeeType->search_list();
                $garage_employees = $this->GarageEmployee->findAllByGarageId($garage_id);
                $customers_activities = $this->CustomerActivity->search_list();
                $garage_activities = $this->GarageCustomerActivity->findAllByGarageId($garage_id);
                $agreements = $this->GarageAgreement->findAllByGarageId($garage_id);
                $get_list_config_tabs = $this->Config->get_list_config_tabs();

                $province = $this->Province->findById($garage['Garage']['province_id']);
                if (!empty($province)) {
                    $country = $this->Country->findById($province['Province']['country_id']);
                    $this->set(
                        array(
                            'country' => $country
                        )
                    );
                }
                $this->set(array(
                    'garage' => $garage,
                    'garage_services' => $garage_services,
                    'garage_vehicles' => $garage_vehicles,
                    'garage_vehicle_specialists' => $garage_vehicle_specialists,
                    'garage_vehicle_types' => $garage_vehicle_types,
                    'garage_software' => $garage_software,
                    'garage_websites' => $garage_websites,
                    'visit_frequency' => $visit_frequency,
                    'websites' => $websites,
                    'software' => $software,
                    'province_list' => $province_list,
                    'lead_sources' => $lead_sources,
                    'garage_distributors' => $garage_distributors,
                    'distributors' => $distributors,
                    'networks' => $networks,
                    'trading_groups' => $trading_groups,
                    'networks_statuses' => $networks_statuses,
                    'garage_networks' => $garage_networks,
                    'garage_internal_networks' => $garage_internal_networks,
                    'garage_external_networks' => $garage_external_networks,
                    'garage_statuses' => $garage_statuses,
                    'garage_contacts_bdm' => $garage_contacts_bdm,
                    'garage_contacts_staff' => $garage_contacts_staff,
                    'garage_contacts_general_branch_manager' => $garage_contacts_general_branch_manager,
                    'garage_comments' => $garage_comments,
                    'images' => $images,
                    'positions' => $positions,
                    'regions' => $regions,
                    'garage_principal_image' => $garage_principal_image,
                    'logs_changes' => $logs_changes,
                    'services' => $services,
                    'vehicle_types' => $vehicle_types,
                    'vehicles' => $vehicles,
                    'networks_contracts' => $networks_contracts,
                    'contact_lists' => $contact_lists,
                    'software_types' => $software_types,
                    'suppliers' => $suppliers,
                    'software_manufactures' => $software_manufactures,
                    'equipments' => $equipments,
                    'brands' => $brands,
                    'garage_equipments' => $garage_equipments,
                    'equipment_types' => $equipment_types,
                    'parts_brands' => $parts_brands,
                    'garages_parts_brands' => $garages_parts_brands,
                    'insurance_agreements' => $insurance_agreements,
                    'employee_types' => $employee_types,
                    'garage_employees' => $garage_employees,
                    'garage_activities' => $garage_activities,
                    'workshop_activities_garage' => $workshop_activities_garage,
                    'workshop_activities_list' => $workshop_activities_list,
                    'customers_activities' => $customers_activities,
                    'agreements' => $agreements,
                    'get_list_config_tabs' => $get_list_config_tabs,
                    'garage_internal_agreements' => $garage_internal_agreements,
                    'garage_external_agreements' => $garage_external_agreements,
                    'user_aag_region_id' => $aag_region_id,
                    'user_role' => $role_id
                ));
            } else {
                $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);

                $contact_id = $user['contact_id'];
                $contact = $this->Contact->findById($contact_id);
                $position_id = $contact['Contact']['position_id'];
                $positions_configs = $this->PositionConfig->findAllByPositionId($position_id);

                if (!empty($positions_configs)) {
                    // Check how many networks has this position.
                    $garage_networks = array();
                    foreach ($positions_configs as $position_config) {
                        if ($position_config['PositionConfig']['all_networks'] == ConstantsBooleans::YES) {
                            $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
                            break;
                        } else {
                            $position_config_newtworks = $this->PositionConfigNetwork->findAllByPositionConfigId($position_config['PositionConfig']['id']);
                            foreach ($position_config_newtworks as $position_config_newtwork) {
                                $nerwork_id = $position_config_newtwork['PositionConfigNetwork']['network_id'];

                                $garage_net = $this->GarageNetwork->findByGarageIdAndNetworkId($garage_id, $nerwork_id);
                                if (!empty($garage_net)) {
                                    $garage_networks[] = $garage_net;
                                }
                            }
                        }
                    }
                }

                $garage_statuses_de = Configure::read('Garage_Status_De');
                foreach ($garage_statuses_de as $key => $status_tmp) {
                    $garage_statuses_de[$key] = __t($status_tmp);
                }

                $garage_images = $this->GarageImage->getListByGarageId($garage_id);

                $this->set(array(
                    'garage' => $garage,
                    'status' => $status,
                    'garage_images' => $garage_images,
                    'garage_statuses_de' => $garage_statuses_de,
                    'contact_lists' => $contact_lists,
                    'garage_networks' => $garage_networks,
                ));
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Garage my data.
     */
    public function my_data($garage_id)
    {
        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        $garage = $this->Garage->getGarageContactList($garage_id, $aag_region_id);

        if (
            $garage &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $role_id == ConstantsRoles::DISTRIBUTOR &&
            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
        ) {
            if (!is_null(CakeSession::read('Auth.User.garage_id')) && $garage_id != CakeSession::read('Auth.User.garage_id')) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_GARAGE));
                $this->redirect(
                    array(
                        'controller' => 'home',
                        'action' => 'home',
                    )
                );
            } elseif (!is_null(CakeSession::read('Auth.User.distributor_id')) && $garage_id != CakeSession::read('Auth.User.distributor_id')) {
                $distributors = $this->Garage->GarageDistributor->Distributor->getAllDistributorAndBranchesByDistributorId(CakeSession::read('Auth.User.distributor_id'));
                $garages = $this->Garage->GarageDistributor->findAllByDistributorId(Hash::extract($distributors, '{n}.Distributor.id'));
                if (!in_array($garage_id, Hash::extract($garages, '{n}.GarageDistributor.garage_id'))) {
                    $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_GARAGE));
                    $this->redirect(
                        array(
                            'controller' => 'home',
                            'action' => 'home',
                        )
                    );
                }
            }

            $position_id = CakeSession::read('Auth.User.Contact.position_id');
            $position_configs = $this->PositionConfig->findAllByPositionId($position_id);
            $group_permission_permission = $this->GroupPermissionPermission->findAllByGroupPermissionId(Hash::extract($position_configs, '{n}.PositionConfig.group_permission_id'));
            $permissions_id = Hash::extract($group_permission_permission, '{n}.GroupPermissionPermission.permission_id');

            $add_users = false;
            if (
                (
                    in_array($permissions_id, array(ConstantsPermissionsGrouping::CREATE_GARAGE_USER, ConstantsPermissionsGrouping::CREATE_GARAGE_USER_D)) &&
                    in_array($role_id, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
                ) ||
                (
                    $permissions_id == ConstantsPermissionsGrouping::CREATE_DISTRIBUTOR_USER &&
                    $role_id == ConstantsRoles::DISTRIBUTOR
                )
            ) {
                $add_users = true;
            }

            $contact_lists = $this->ContactList->find('list');

            $status = $garage['Garage']['status'];

            //Regions
            $regions = $this->Region->region_list();

            if ($status != ConstantsGarageStatusDe::POTENTIAL) {
                $lead_sources = Configure::read('lead_source');
                foreach ($lead_sources as $key => $lead_source) {
                    $lead_sources[$key] = __t($lead_source);
                }

                $province_list = $this->Garage->Province->find('list');
                $garage = $this->Garage->findById($garage_id);
                $garage_services = $this->GarageService->findServices($garage_id);
                $garage_vehicle_types = $this->GarageVehicleType->findVehicleTypes($garage_id);
                $garage_vehicles = $this->GarageVehicle->findVehicles($garage_id);
                $garage_vehicle_specialists = $this->GarageSpecialistMake->findVehicles($garage_id);
                $garage_principal_image = $this->Garage->getPrincipalImageDatas($garage_id);
                $workshop_activities_garage = $this->GarageWorkshopActivity->getWorkshopActivitiesByGarageId($garage['Garage']['id']);
                $workshop_activities_list = $this->WorkshopActivity->search_list();
                $software = $this->Software->search_list($garage['Garage']['aag_region_id']);
                $garage_software = $this->GarageSoftware->findAllByGarageId($garage_id);
                $websites = $this->Website->search_list($garage['Garage']['aag_region_id']);
                $garage_websites = $this->GarageWebsite->findAllByGarageId($garage_id);
                $distributors = $this->Distributor->find('list');
                $garage_distributors = $this->GarageDistributor->getAllByGarageId($garage_id);
                $networks = $this->Network->find('all');
                $trading_groups = $this->TradingGroup->find('all');
                $networks_statuses = Configure::read('Network_Status');
                foreach ($networks_statuses as $key => $network_status) {
                    $networks_statuses[$key] = __t($network_status);
                }
                $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
                $images = $this->GarageImage->findAllByGarageId($garage_id);
                $garage_statuses = Configure::read('Garage_Status');
                foreach ($garage_statuses as $key => $garage_status) {
                    $garage_statuses[$key] = __t($garage_status);
                }

                $garage_contacts_bdm = $this->GarageContactBdm->getSomeByGarageId($garage_id);
                foreach ($garage_contacts_bdm as $key => $garage_contact_bdm) {
                    $garage_contacts_bdm[$key]['Network'] = $this->NetworkContactBdm->findAllNetworksByGarageIdAndContactId($garage_id, $garage_contact_bdm['Contact']['id']);
                }
                $garage_contacts_staff = $this->GarageContactStaff->getSomeByGarageId($garage_id);
                $garage_contacts_general_branch_manager = $this->GarageContactGeneralBranchManager->getContactsByGarageIdLimited($garage_id);
                $garage_comments = $this->GarageComment->getAllByGarageWithUsersLimited($garage_id, ConstantsPagination::SIZE_PAGE_LARGE);
                $positions = $this->Position->search_list();
                $logs_changes = $this->LogChange->get_last_changes_by_garage($garage_id);
                $services = $this->Service->find('all');
                $vehicle_types = $this->VehicleType->find('all');
                $vehicles = $this->Vehicle->find('all');
                $networks_contracts = $this->NetworkContractType->search_list();
                $visit_frequency = $this->GarageVisitFrequency->find('list');
                $software_types = $this->SoftwareType->search_list($garage['Garage']['aag_region_id']);
                $suppliers = $this->Supplier->search_list();
                $software_manufactures = $this->SoftwareManufacture->search_list();
                $garage_equipments = $this->GarageEquipment->findAllByGarageId($garage_id);
                $equipment_types = $this->EquipmentType->search_list($garage['Garage']['aag_region_id']);
                $equipments = $this->Equipment->search_list($garage['Garage']['aag_region_id']);
                $brands = $this->Brand->search_list();
                $parts_brands = $this->Brand->find('all');
                $garages_parts_brands = $this->GarageBrand->findPartBrands($garage_id);
                $insurance_agreements = $this->InsuranceAgreement->find('list');
                $employee_types = $this->EmployeeType->search_list();
                $garage_employees = $this->GarageEmployee->findAllByGarageId($garage_id);
                $customers_activities = $this->CustomerActivity->search_list();
                $garage_activities = $this->GarageCustomerActivity->findAllByGarageId($garage_id);

                $province = $this->Province->findById($garage['Garage']['province_id']);
                if (!empty($province)) {
                    $country = $this->Country->findById($province['Province']['country_id']);
                    $this->set(
                        array(
                            'country' => $country
                        )
                    );
                }

                $this->set(array(
                    'garage' => $garage,
                    'garage_services' => $garage_services,
                    'garage_vehicles' => $garage_vehicles,
                    'garage_vehicle_specialists' => $garage_vehicle_specialists,
                    'garage_vehicle_types' => $garage_vehicle_types,
                    'garage_software' => $garage_software,
                    'garage_websites' => $garage_websites,
                    'workshop_activities_garage' => $workshop_activities_garage,
                    'workshop_activities_list' => $workshop_activities_list,
                    'visit_frequency' => $visit_frequency,
                    'websites' => $websites,
                    'software' => $software,
                    'province_list' => $province_list,
                    'lead_sources' => $lead_sources,
                    'garage_distributors' => $garage_distributors,
                    'distributors' => $distributors,
                    'networks' => $networks,
                    'trading_groups' => $trading_groups,
                    'networks_statuses' => $networks_statuses,
                    'garage_networks' => $garage_networks,
                    'garage_statuses' => $garage_statuses,
                    'garage_contacts_bdm' => $garage_contacts_bdm,
                    'garage_contacts_staff' => $garage_contacts_staff,
                    'garage_contacts_general_branch_manager' => $garage_contacts_general_branch_manager,
                    'garage_comments' => $garage_comments,
                    'images' => $images,
                    'positions' => $positions,
                    'regions' => $regions,
                    'garage_principal_image' => $garage_principal_image,
                    'logs_changes' => $logs_changes,
                    'services' => $services,
                    'vehicle_types' => $vehicle_types,
                    'vehicles' => $vehicles,
                    'networks_contracts' => $networks_contracts,
                    'contact_lists' => $contact_lists,
                    'software_types' => $software_types,
                    'suppliers' => $suppliers,
                    'software_manufactures' => $software_manufactures,
                    'equipments' => $equipments,
                    'brands' => $brands,
                    'garage_equipments' => $garage_equipments,
                    'equipment_types' => $equipment_types,
                    'parts_brands' => $parts_brands,
                    'garages_parts_brands' => $garages_parts_brands,
                    'insurance_agreements' => $insurance_agreements,
                    'employee_types' => $employee_types,
                    'garage_employees' => $garage_employees,
                    'garage_activities' => $garage_activities,
                    'customers_activities' => $customers_activities,
                    'add_users' => $add_users,
                    'user_aag_region_id' => $aag_region_id,
                    'user_role' => $role_id
                ));
            } else {
                $garage_statuses_de = Configure::read('Garage_Status_De');
                foreach ($garage_statuses_de as $key => $status) {
                    $garage_statuses_de[$key] = __t($status);
                }

                $garage_images = $this->GarageImage->getListByGarageId($garage_id);

                $this->set(array(
                    'garage' => $garage,
                    'status' => $status,
                    'garage_images' => $garage_images,
                    'garage_statuses_de' => $garage_statuses_de,
                    'contact_lists' => $contact_lists,
                ));
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create Garage.
     */
    public function add()
    {
        $user = $this->Acceso->user();
        $user_aag_region_id = $user['aag_region_id'];
        $user_role_id = $user['role_id'];
        $new_garage_country_selected = null;
        $new_garage_province_selected = null;

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE, ConstantsPermissionsGrouping::CREATE_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            !in_array($user_role_id, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR))
        ) {
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'garages',
                    'action' => 'home',
                ),
            );

            $workshopActivities = $this->WorkshopActivity->search_list();

            $countries = $this->Country->find('list', array(
                'conditions' => array(
                    'Country.aag_region_id' => $user_aag_region_id
                ),
                'order' => 'Country.name',
            ));
            $countriesData = $this->Country->find('all', array(
                'fields' => array('name', 'country_code_2')
            ));
            $countriesCode = array();
            foreach ($countriesData as $country) {
                $countriesCode[$country['Country']['name']] = strtoupper($country['Country']['country_code_2']);
            }
            $countCountries = count($countries);
            $provinces = '';
            $cities = '';

            if ($countCountries > 0 && !empty($countries)) {
                $searchCountry = array_keys($countries);
                $provinces = $this->Province->getProvincesByCountry($searchCountry[0]);
                if (count($provinces) > 0 && !empty($provinces)) {
                    $searchCity = array_keys($provinces);
                    $cities = $this->City->getCitiesByProvince($searchCity[0]);
                }
            }

            $languages = $this->Language->getLanguagesCodeNameWithoutLoco();

            if (!$this->request->is('get')) {
                $error = false;
                if (isset($this->request->data['Garage']['slug']) && !empty(trim($this->request->data['Garage']['slug']))) {
                    $slug = $this->request->data['Garage']['slug'];
                    $this->request->data['Garage']['slug'] = $this->Garage->generateSlug($slug, false);
                } elseif (isset($this->request->data['Garage']['business_name']) && !empty(trim($this->request->data['Garage']['business_name']))) {
                    $slug = $this->request->data['Garage']['business_name'];
                    $this->request->data['Garage']['slug'] = $this->Garage->generateSlug($slug, false);
                } elseif (isset($this->request->data['Garage']['name']) && !empty(trim($this->request->data['Garage']['name']))) {
                    $slug = $this->request->data['Garage']['name'];
                    $this->request->data['Garage']['slug'] = $this->Garage->generateSlug($slug, false);
                }

                if (isset($this->request->data['Garage']['country'])) {
                    $new_garage_country_selected = $this->request->data['Garage']['country'];
                    $provinces = $this->Province->getProvincesByCountry($new_garage_country_selected);

                    if (isset($this->request->data['province_id'])) {
                        $cities = $this->City->getCitiesByProvince($this->request->data['province_id']);
                    }
                }

                if (!$error) {
                    if (isset($this->request->data['city_id'])) {
                        $this->request->data['Garage']['city_id'] = $this->request->data['city_id'];
                    }
                    if (isset($this->request->data['province_id'])) {
                        $this->request->data['Garage']['province_id'] = $this->request->data['province_id'];
                    }
                    $this->request->data['Garage']['manually_created'] = ConstantsBooleans::YES;
                    $garage = $this->Garage->add_garage($this->request->data, $user);
                    if ($garage) {
                        //Add garage contacts bdm
                        if (!empty($this->request->data['GarageContact']['GaragesBDM'])) {
                            foreach ($this->request->data['GarageContact']['GaragesBDM'] as $contact_bdm) {
                                $garage_bdm = array(
                                    'GarageContactBdm' => array(
                                        'garage_id' => $garage['Garage']['id'],
                                        'contact_id' => $contact_bdm,
                                    )
                                );

                                $garage_contact = $this->GarageContactBdm->new_garage_contact_bdm($garage_bdm);
                                if ($garage_contact) {
                                    $this->LogChange->add_contact_log(
                                        $this->GarageContactBdm->table,
                                        $this->Session->read('Auth'),
                                        $garage['Garage']['id'],
                                        ConstantsLogType::GARAGE,
                                        $contact_bdm
                                    );
                                    echo ConstantsBooleans::YES;
                                } else {
                                    echo ConstantsBooleans::NO;
                                }
                            }
                        }
                        $msg = h(sprintf(__t('Garage.Well_add'), $garage['Garage']['name']));
                        $this->Session->setFlashSuccess($msg);
                        $this->redirect(
                            array(
                                'controller' => 'garages',
                                'action' => 'edit',
                                $this->Garage->getLastInsertID()
                            )
                        );
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                }
            }

            $regions = $this->Region->region_list();

            $conditions = array('AagRegion.id' => $user_aag_region_id);
            $aag_regions = $this->AagRegion->region_list_conditions($conditions);

            $garageStatuses = Configure::read('Garage_Status');
            foreach ($garageStatuses as $key => $garage_status) {
                $garageStatuses[$key] = __t($garage_status);
            }

            $visitFrequency = $this->GarageVisitFrequency->find('list');
            $insuranceAgreements = $this->InsuranceAgreement->find('list');
            $manually_created =  ConstantsBooleans::YES;

            $lists_bdm = $this->GarageContactBdm->getAllBDMContacts($user_aag_region_id);
            $all_contacts_bdm = array();
            foreach ($lists_bdm as $key => $list_bdm) {
                $id = $list_bdm['Contact']['id'];
                $all_contacts_bdm[$id] = $list_bdm[0]['full_name'];
            }

            $getListConfigTabs = $this->Config->get_list_config_tabs();
            $salesArea = $this->SalesArea->searchListByRegion($user_aag_region_id);

            $this->set(array(
                'cancel_action' => $cancelAction,
                'cities' => $cities,
                'provinces' => $provinces,
                'countries' => $countries,
                'countries_code' => $countriesCode,
                'count_countries' => $countCountries,
                'regions' => $regions,
                'garage_statuses' => $garageStatuses,
                'visit_frequency' => $visitFrequency,
                'workshop_activities' => $workshopActivities,
                'insurance_agreements' => $insuranceAgreements,
                'get_list_config_tabs' => $getListConfigTabs,
                'contacts' => array(),
                'all_contacts_bdm' => $all_contacts_bdm,
                'aag_regions' => $aag_regions,
                'user_aag_region_id' => $user_aag_region_id,
                'user_role_id' => $user_role_id,
                'manually_created' => $manually_created,
                'primaryContact' => isset($primaryContact) ? $primaryContact : null,
                'erp_providers' => $this->Erp->getErpsByAagRegionId($user_aag_region_id),
                'languages' => $languages,
                'new_garage_country_selected' => $new_garage_country_selected,
                'sales_area' => $salesArea

            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add Garage opening hours.
     */
    public function add_opening_garage($garage_id)
    {
        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aag_region_id);
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                $role_id == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array($role_id, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            $role_id == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $garage_old_data_tmp = $garage;

            if (!$this->request->is('get') && $role_id != ConstantsRoles::SUPER_ADMIN) {
                $user = $user = $this->Session->read('Auth');

                if (isset($this->request->data['request_changes'])) {
                    $result = $this->RequestedChange->create_request_edit(
                        $garage_old_data_tmp['Garage'],
                        $this->request->data['Garage'],
                        $this->Garage->table,
                        $user,
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );
                    if ($result) {
                        $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                    }
                } else {
                    $garageBd = $this->Garage->add_opening_garage($this->request->data, $garage_id);
                    if ($garageBd) {
                        $this->LogChange->get_params_create_log_edit(
                            $garage_old_data_tmp['Garage'],
                            $garageBd['Garage'],
                            $this->Garage->table,
                            $user,
                            $garage_id,
                            ConstantsLogType::GARAGE
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                }
            } else {
                $this->request->data = $garage;
            }
            $get_list_config_tabs = $this->Config->get_list_config_tabs();
            $this->setVarCancel($garage_id);
            $this->set(array(
                'garage' => $garage,
                'garage_networks' => $garage_networks,
                'get_list_config_tabs' => $get_list_config_tabs,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add Garage additional info and other details.
     */
    public function add_aditional_info_and_other_details_garage($garage_id)
    {
        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aag_region_id);
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }
        if (
            $garage &&
            (
                $role_id == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array($role_id, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            $role_id == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $country = array();
            if (isset($garage['Garage']['province_id'])) {
                $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
                $postcodes = $this->PostcodeProvince->findListByProvinceId($garage['Garage']['province_id']);
            } elseif (isset($garage['Garage']['city_id'])) {
                $country = $this->Country->get_country_by_city($garage['Garage']['city_id']);
                $city = $this->City->findById($garage['Garage']['city_id']);
                $postcodes = $this->PostcodeProvince->findListByProvinceId($city['City']['province_id']);
            }
            $garage_old_data_tmp = $garage;
            if (!$this->request->is('get') && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                $user = $this->Session->read('Auth');
                if (isset($this->request->data['request_changes'])) {
                    $result[] = $this->RequestedChange->create_request_edit(
                        $garage_old_data_tmp['Garage'],
                        $this->request->data['Garage'],
                        $this->Garage->table,
                        $user,
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );
                    if ($result) {
                        $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                    }
                } else {
                    $garageBd = $this->Garage->add_aditional_info_and_other_details_garage($this->request->data, $this->Session->read('Auth'));
                    if ($garageBd) {
                        $this->LogChange->get_params_create_log_edit(
                            $garage_old_data_tmp['Garage'],
                            $garageBd['Garage'],
                            $this->Garage->table,
                            $user,
                            $garage_id,
                            ConstantsLogType::GARAGE
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                }
            } else {
                $this->request->data = $garage;
                $this->request->data['Garage']['ev_ppe_audited_date'] = Fecha::toFormatoVistaFecha($garage['Garage']['ev_ppe_audited_date']);
                $this->request->data['Garage']['postcode_b2b'] = $this->GarageB2bPostcode->get_list($garage_id);
                $this->request->data['Garage']['postcode_b2c'] = $this->GarageB2cPostcode->get_list($garage_id);
            }

            $get_list_config_tabs = $this->Config->get_list_config_tabs();

            $this->setVarCancel($garage_id);
            $this->set(array(
                'garage' => $garage,
                'garage_networks' => $garage_networks,
                'get_list_config_tabs' => $get_list_config_tabs,
                'postcodes' => $postcodes ?? array(),
                'country' => $country,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add Garage orders.
     */
    public function add_orders($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $config = CakeSession::read('Auth.User.Config');;

            $orders = $this->custom_pagination(
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Product',
                            'table' => 'garages_products',
                            'type' => 'LEFT',
                            'conditions' => 'Product.order_id = Order.id'
                        ),
                    ),
                    'conditions' => array(
                        'Order.garage_id' => $garage_id,
                    ),
                    'fields' => array(
                        'Order.*',
                        'Product.id',
                        'Product.quantity',
                    ),
                    'group' => array(
                        'Order.id'
                    ),
                ),
                array(),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Order',
                null,
                'PaginatorOrderCustom'
            );

            $country = array();
            if (isset($garage['Garage']['province_id'])) {
                $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
            } elseif (isset($garage['Garage']['city_id'])) {
                $country = $this->Country->get_country_by_city($garage['Garage']['city_id']);
            }

            if (!$this->request->is('get') && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Garage->table,
                    'Equipment.Equipment',
                    $user['User']['id'],
                    $garage_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::GARAGE
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $get_list_config_tabs = $this->Config->get_list_config_tabs();
            $this->setVarCancel($garage_id);
            $this->set(array(
                'garage' => $garage,
                'garage_networks' => $garage_networks,
                'config' => $config,
                'get_list_config_tabs' => $get_list_config_tabs,
                'orders' => $orders,
                'country' => $country,
                'order_types_list' => $this->OrderType->search_list(),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add Garage activities and services.
     */
    public function add_activities_and_services_garage($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $facilities = $this->Facility->search_list();
            $value_and_suppliers = $this->ValueAddSupplier->getDataByGarage($garage_id);
            $value_and_supplier_type = $this->ValueAddSupplierType->getDataByGarage($garage_id);
            $garage_value_supplier = $this->GarageValueAddSupplier->getDataByGarage($garage_id);
            $garage_services = $this->GarageService->findServicesByGarage($garage_id);
            $garage_vehicle = $this->GarageVehicle->findVehiclesByGarage($garage_id);
            $garage_vehicle_specialists = $this->GarageSpecialistMake->findVehiclesSpecialistByGarage($garage_id);
            $garage_vehicle_types = $this->GarageVehicleType->findVehicleTypesByGarage($garage_id);
            $garage_part_brand = $this->GarageBrand->findPartBrandByGarage($garage_id);
            $vehicles_and_specialists = $this->Vehicle->findVehiclesAndSpecialistById($garage_id);

            $garage_customer_activities = $this->GarageCustomerActivity->getAllByGarageIdByOrder($garage_id);
            $customers_activities = $this->CustomerActivity->search_list();

            if (!$this->request->is('get') && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                if (!isset($this->request->data['request_changes'])) {
                    $garage = $this->Garage->add_activities_and_services_garage($this->request->data, $this->Session->read('Auth'));
                } else {
                    $garage = true;
                }
                $user = $this->Session->read('Auth');
                if ($garage) {
                    if (!isset($this->request->data['request_changes'])) {
                        $garage_services_log = $this->GarageService->findServicesByGarage($garage_id);
                        $garage_vehicle_log = $this->GarageVehicle->findVehiclesByGarage($garage_id);
                        $garage_vehicle_specialists_log = $this->GarageSpecialistMake->findVehiclesSpecialistByGarage($garage_id);
                        $garage_vehicle_types_log = $this->GarageVehicleType->findVehicleTypesByGarage($garage_id);
                        $garage_part_brand_log = $this->GarageBrand->findPartBrandByGarage($garage_id);
                    } else {
                        $garage_services_log = $this->request->data['Garage']['Service'];
                        foreach (array_keys($garage_services_log, 0) as $key) {
                            unset($garage_services_log[$key]);
                        }
                        $garage_vehicle_log = $this->request->data['Garage']['Vehicle'];
                        foreach (array_keys($garage_vehicle_log, 0) as $key) {
                            unset($garage_vehicle_log[$key]);
                        }
                        $garage_vehicle_specialists_log = $this->request->data['Garage']['VehicleSpecialist'];
                        foreach (array_keys($garage_vehicle_specialists_log, 0) as $key) {
                            unset($garage_vehicle_specialists_log[$key]);
                        }
                        $garage_vehicle_types_log = $this->request->data['Garage']['VehicleType'];
                        foreach (array_keys($garage_vehicle_types_log, 0) as $key) {
                            unset($garage_vehicle_types_log[$key]);
                        }
                        $garage_part_brand_log = $this->request->data['Garage']['Brand'];
                        foreach (array_keys($garage_part_brand_log, 0) as $key) {
                            unset($garage_part_brand_log[$key]);
                        }
                    }
                    if (!empty($garage_services)) {
                        $garage_services = array_combine($garage_services, $garage_services);
                        if (!empty($garage_services_log)) {
                            $garage_services_log = array_combine($garage_services_log, $garage_services_log);
                        }
                    }
                    if (!empty($garage_vehicle)) {
                        $garage_vehicle = array_combine($garage_vehicle, $garage_vehicle);
                        if (!empty($garage_vehicle_log)) {
                            $garage_vehicle_log = array_combine($garage_vehicle_log, $garage_vehicle_log);
                        }
                    }
                    if (!empty($garage_vehicle_specialists)) {
                        $garage_vehicle_specialists = array_combine($garage_vehicle_specialists, $garage_vehicle_specialists);
                        if (!empty($garage_vehicle_specialists_log)) {
                            $garage_vehicle_specialists_log = array_combine($garage_vehicle_specialists_log, $garage_vehicle_specialists_log);
                        }
                    }
                    if (!empty($garage_vehicle_types)) {
                        $garage_vehicle_types = array_combine($garage_vehicle_types, $garage_vehicle_types);
                        if (!empty($garage_vehicle_types_log)) {
                            $garage_vehicle_types_log = array_combine($garage_vehicle_types_log, $garage_vehicle_types_log);
                        }
                    }
                    if (!empty($garage_part_brand)) {
                        $garage_part_brand = array_combine($garage_part_brand, $garage_part_brand);
                        if (!empty($garage_part_brand_log)) {
                            $garage_part_brand_log = array_combine($garage_part_brand_log, $garage_part_brand_log);
                        }
                    }
                    if (!isset($this->request->data['request_changes'])) {
                        $this->LogChange->get_params_create_log_edit(
                            $garage_services,
                            $garage_services_log,
                            $this->GarageService->table,
                            $user,
                            $garage_id,
                            ConstantsLogType::GARAGE,
                            'service_id'
                        );
                        $this->LogChange->get_params_create_log_edit(
                            $garage_vehicle,
                            $garage_vehicle_log,
                            $this->GarageVehicle->table,
                            $user,
                            $garage_id,
                            ConstantsLogType::GARAGE,
                            'vehicle_id'
                        );
                        $this->LogChange->get_params_create_log_edit(
                            $garage_vehicle_specialists,
                            $garage_vehicle_specialists_log,
                            $this->GarageSpecialistMake->table,
                            $user,
                            $garage_id,
                            ConstantsLogType::GARAGE,
                            'vehicle_id'
                        );
                        $this->LogChange->get_params_create_log_edit(
                            $garage_vehicle_types,
                            $garage_vehicle_types_log,
                            $this->GarageVehicleType->table,
                            $user,
                            $garage_id,
                            ConstantsLogType::GARAGE,
                            'vehicle_type_id'
                        );
                        $this->LogChange->get_params_create_log_edit(
                            $garage_part_brand,
                            $garage_part_brand_log,
                            $this->GarageBrand->table,
                            $user,
                            $garage_id,
                            ConstantsLogType::GARAGE,
                            'brand_id'
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $result = array(true);
                        $result[] = $this->RequestedChange->create_request_edit_description(
                            $this->Garage->table,
                            'Garage.Activity',
                            $user['User']['id'],
                            $garage_id,
                            $this->request->data['change_description'],
                            ConstantsLogType::GARAGE
                        );
                        $result[] = $this->RequestedChange->create_request_edit(
                            $garage_services,
                            $garage_services_log,
                            $this->GarageService->table,
                            $user,
                            $garage_id,
                            ConstantsLogType::GARAGE,
                            'service_id'
                        );
                        $result[] = $this->RequestedChange->create_request_edit(
                            $garage_vehicle,
                            $garage_vehicle_log,
                            $this->GarageVehicle->table,
                            $user,
                            $garage_id,
                            ConstantsLogType::GARAGE,
                            'vehicle_id'
                        );
                        $result[] = $this->RequestedChange->create_request_edit(
                            $garage_vehicle_specialists,
                            $garage_vehicle_specialists_log,
                            $this->GarageSpecialistMake->table,
                            $user,
                            $garage_id,
                            ConstantsLogType::GARAGE,
                            'vehicle_id'
                        );
                        $result[] = $this->RequestedChange->create_request_edit(
                            $garage_vehicle_types,
                            $garage_vehicle_types_log,
                            $this->GarageVehicleType->table,
                            $user,
                            $garage_id,
                            ConstantsLogType::GARAGE,
                            'vehicle_type_id'
                        );
                        $result[] = $this->RequestedChange->create_request_edit(
                            $garage_part_brand,
                            $garage_part_brand_log,
                            $this->GarageBrand->table,
                            $user,
                            $garage_id,
                            ConstantsLogType::GARAGE,
                            'brand_id'
                        );
                        if (!in_array(false, $result)) {
                            $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                            $this->redirect($this->here);
                        } else {
                            $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                        }
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                $this->request->data = $garage;
                $this->request->data['Garage']['facilities'] = $this->GarageFacility->get_list($garage_id);
            }

            $get_list_config_tabs = $this->Config->get_list_config_tabs();
            $this->setVarCancel($garage_id);
            $this->set(array(
                'garage' => $garage,
                'garage_services' => $garage_services,
                'garage_vehicle' => $garage_vehicle,
                'garage_vehicle_specialists' => $garage_vehicle_specialists,
                'garage_vehicle_types' => $garage_vehicle_types,
                'services' => $this->Service->find('all'),
                'vehicles' => $this->Vehicle->find('all'),
                'vehiclespecialist' => $this->Vehicle->find('all'),
                'vehicletypes' => $this->VehicleType->find('all'),
                'vehicles_and_specialists' => $vehicles_and_specialists,
                'garage_networks' => $garage_networks,
                'garage_customer_activities' => $garage_customer_activities,
                'customers_activities' => $customers_activities,
                'get_list_config_tabs' => $get_list_config_tabs,
                'facilities' => $facilities,
                'value_and_suppliers' => $value_and_suppliers,
                'value_and_supplier_type' => $value_and_supplier_type,
                'garage_value_supplier' => $garage_value_supplier,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add Garage employee.
     */
    public function add_employee_garage($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }
        $get_list_config_tabs = $this->Config->get_list_config_tabs();

        if (
            $garage &&
            $get_list_config_tabs[ConstantsTabs::EMPLOYEES] == ConstantsBooleans::ACTIVE &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $employee_types = $this->EmployeeType->search_list();
            $garage_employees = $this->GarageEmployee->findAllByGarageId($garage_id);

            if (!$this->request->is('get') && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Garage->table,
                    'Garage.Employees',
                    $user['User']['id'],
                    $garage_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::GARAGE
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $this->setVarCancel($garage_id);
            $this->set(array(
                'garage' => $garage,
                'employee_types' => $employee_types,
                'garage_employees' => $garage_employees,
                'garage_networks' => $garage_networks,
                'get_list_config_tabs' => $get_list_config_tabs,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add Garage distributor and networks.
     */
    public function add_dis_and_net_garage($garage_id)
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);
        $garage_internal_networks = $this->GarageNetwork->findInternalByGarageAndAagRegionId($garage_id, $aagRegionId);
        $garage_external_networks = $this->GarageNetwork->findExternalByGarageAndAagRegionId($garage_id, $aagRegionId);

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $networks = $this->Network->findListByAagRegionId($aagRegionId);
            $trading_groups = $this->TradingGroup->findAllByAagRegionId($aagRegionId);

            $this->ApiRm = ClassRegistry::init("ApiRm.ApiRm");
            $internal_agreements_rm = $this->ApiRm->get_internal_agreements($garage_id);
            $internal_agreements_alliance = $this->GarageAgreement->get_internal_agreements($garage_id);
            $garage_internal_agreements = $this->GarageAgreement->getInternalAgreementArray($internal_agreements_rm, $internal_agreements_alliance);
            $garage_external_agreements = $this->GarageAgreement->findExternalByGarage($garage_id);

            $reasons_leaving = $this->LeavingReasonType->getList();
            $garage_distributors = $this->Distributor->getAllByGarageIdByOrderGarage($garage_id);

            $networks_statuses = Configure::read('Network_Status');
            foreach ($networks_statuses as $key => $network_status) {
                $networks_statuses[$key] = __t($network_status);
            }

            $garage_networks = $this->GarageNetwork->findAllByGarageIdAndAagRegionId($garage_id, $aagRegionId);
            $networks_image = $this->Network->findAllByAagRegionId($aagRegionId);
            $networks_contracts = $this->NetworkContractType->search_list('list');
            $suppliers = $this->Supplier->findListByAagRegionId($aagRegionId);

            if (!$this->request->is('get') && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                if (!empty($this->request->data['Garage']['GaragesDistributors'])) {
                    $garages_distributors = $this->request->data['Garage']['GaragesDistributors'];
                    foreach ($garages_distributors as $value) {
                        $distributor_id_and_order = explode(' ', $value);
                        if (count($distributor_id_and_order) < 3 || $distributor_id_and_order[1] == 'undefined') {
                            if ($distributor_id_and_order[1] == 'undefined') {
                                $order = $distributor_id_and_order[2];
                            } else {
                                $order = $distributor_id_and_order[1];
                            }
                            $garage_distributor = array(
                                'garage_id' => $garage_id,
                                'distributor_id' => $distributor_id_and_order[0],
                                'order' => $order
                            );
                            $this->GarageDistributor->new_garage_distributor($garage_distributor);
                        } elseif (count($distributor_id_and_order) == 3) {
                            $garage_distributor_tmp = array(
                                'GarageDistributor' => array(
                                    'id' => $distributor_id_and_order[1],
                                    'order' => $distributor_id_and_order[2],
                                )
                            );
                            $this->GarageDistributor->change_order($garage_distributor_tmp);
                        }
                    }
                }
                if (!empty($this->request->data['Garage']['DeleteGaragesDistributors'])) {
                    $garages_distributors_delete = $this->request->data['Garage']['DeleteGaragesDistributors'];
                    foreach ($garages_distributors_delete as $garage_distributor) {
                        $this->GarageDistributor->delete($garage_distributor);
                    }
                    $garage_customer_activities = $this->GarageDistributor->getAllByGarageIdByOrder($garage_id);
                    $counter = 1;
                    foreach ($garage_customer_activities as $garage_distributors) {
                        $garage_distributors['GarageDistributor']['order'] = $counter;
                        $counter++;
                        $this->GarageDistributor->change_order($garage_distributors);
                    }
                }
                $this->redirect($this->here);
            } else {
                $this->request->data = $garage;
            }

            $get_list_config_tabs = $this->Config->get_list_config_tabs();

            $this->setVarCancel($garage_id);
            $this->set(array(
                'garage' => $garage,
                'garage_distributors' => $garage_distributors,
                'networks' => $networks,
                'suppliers' => $suppliers,
                'trading_groups' => $trading_groups,
                'networks_statuses' => $networks_statuses,
                'garage_networks' => $garage_networks,
                'garage_internal_networks' => $garage_internal_networks,
                'garage_external_networks' => $garage_external_networks,
                'garage_internal_agreements' => $garage_internal_agreements,
                'garage_external_agreements' => $garage_external_agreements,
                'networks_contracts' => $networks_contracts,
                'networks_image' => $networks_image,
                'reasons_leaving' => $reasons_leaving,
                'get_list_config_tabs' => $get_list_config_tabs,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add Garage marketing and image.
     */
    public function add_marketing_and_image_garage($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $websites = $this->Website->search_list(CakeSession::read('Auth.User.aag_region_id'));
            $garage_websites = $this->GarageWebsite->findAllByGarageId($garage_id);
            $images = $this->GarageImage->findAllByGarageId($garage['Garage']['id']);
            $campaign_entries = $this->CampaignEntry->getDataByGarage($garage_id, CakeSession::read('Auth.User.aag_region_id'));
            $campaigns = $this->GarageCampaign->getDataByGarage($garage_id);
            $cancel_action = array(
                'url_cancel' => array(
                    'controller' => 'garages',
                    'action' => 'view',
                    $garage_id
                ),
            );

            if (!$this->request->is('get') && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                $upload_files = $this->request->data;
                $result = false;
                $check_image = false;

                $old_data = $this->Garage->findById($garage_id);

                $cont = 1;
                if (!isset($upload_files['file-content'])) {
                    $garage = $this->Garage->add_marketing_and_image_garage($this->request->data);
                } else {
                    foreach ($upload_files['file-content'] as $image) {
                        $check_image = FileManager::check_image($upload_files['GarageImage']['files'][$cont], $image);
                        if ($check_image == ConstantsFileErrorTypes::OK) {
                            $garage = $this->Garage->add_marketing_and_image_garage($this->request->data);
                            $result = $this->GarageImage->uploadGarageImages(
                                $image,
                                $upload_files['GarageImage']['files'][$cont],
                                $garage_id,
                                ConstantsFileType::IMAGE
                            );
                            if (!$result) {
                                break;
                            }
                            $cont++;
                        } else {
                            $garage = true;
                        }
                    }
                }
                if ($garage) {
                    if (!$check_image || $check_image == ConstantsFileErrorTypes::OK) {
                        $user = $this->Session->read('Auth');
                        if (!isset($this->request->data['request_changes'])) {
                            $this->LogChange->get_params_create_log_edit(
                                $old_data['Garage'],
                                $this->request->data['Garage'],
                                $this->Garage->table,
                                $user,
                                $garage_id,
                                ConstantsLogType::GARAGE
                            );

                            $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                            $this->redirect($this->here);
                        } else {
                            $change = $this->RequestedChange->create_request_edit_description(
                                $this->GarageImage->table,
                                'Garage.Images',
                                $user['User']['id'],
                                $garage_id,
                                $this->request->data['change_description'],
                                ConstantsLogType::GARAGE
                            );
                            if ($change) {
                                $check_image = FileManager::check_image($this->request->data['GarageImage']['files'], $upload_files['GarageImage']['new_image']);
                                $this->RequestedChange->RequestedChangeImage->uploadGarageImages(
                                    $upload_files['GarageImage']['new_image'],
                                    $upload_files['GarageImage']['files'],
                                    $change['RequestedChange']['id'],
                                    ConstantsFileType::IMAGE
                                );
                            }
                            if (!$check_image || $check_image == ConstantsFileErrorTypes::OK) {
                                $result = array(true);
                                $result[] = $this->RequestedChange->create_request_edit(
                                    $old_data['Garage'],
                                    $this->request->data['Garage'],
                                    $this->Garage->table,
                                    $user,
                                    $garage_id,
                                    ConstantsLogType::GARAGE
                                );
                                $result[] = $this->RequestedChange->create_request_edit_description(
                                    $this->Garage->table,
                                    'Garage.Marketing',
                                    $user['User']['id'],
                                    $garage_id,
                                    $this->request->data['change_description'],
                                    ConstantsLogType::GARAGE
                                );
                                if (!in_array(false, $result)) {
                                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                                    $this->redirect($this->here);
                                } else {
                                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                                }
                            } elseif ($check_image == ConstantsFileErrorTypes::SIZE_ERROR) {
                                $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                            } else {
                                $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                            }
                        }
                    } elseif ($check_image == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    $garage = $this->Garage->findById($this->request->data['Garage']['id']);
                }
            } else {
                $this->request->data = $garage;
            }

            $lead_sources = Configure::read('lead_source');
            foreach ($lead_sources as $key => $lead_source) {
                $lead_sources[$key] = __t($lead_source);
            }

            $get_list_config_tabs = $this->Config->get_list_config_tabs();
            $this->setVarCancel($garage_id);
            $this->set(array(
                'garage' => $garage,
                'websites' => $websites,
                'garage_websites' => $garage_websites,
                'lead_sources' => $lead_sources,
                'garage_networks' => $garage_networks,
                'cancel_action' => $cancel_action,
                'garage_id' => $garage_id,
                'images' => $images,
                'get_list_config_tabs' => $get_list_config_tabs,
                'campaign_entries' => $campaign_entries,
                'campaigns' => $campaigns,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX create GarageCampaign.
     */
    public function ajax_save_new_value_campaign($garage_id)
    {
        $this->verify_ajax($this->request);

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            (
                !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                (
                    CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                    $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                )
            ) &&
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
        ) {
            $this->autoRender = false;
            $this->request->data['GarageCampaign']['garage_campaign_id'] = $this->request->data['campaign'];
            $this->request->data['GarageCampaign']['number'] = $this->request->data['number'];

            $result = $this->GarageCampaign->add_garage_campaign($this->request->data, $garage_id);
            $old_data = array();

            if ($result) {
                $this->LogChange->get_params_create_log_edit(
                    $old_data,
                    $result['GarageCampaign'],
                    $this->GarageCampaign->table,
                    $this->Session->read('Auth'),
                    $garage_id,
                    ConstantsLogType::GARAGE
                );
                return true;
            } else {
                return $result;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get CampaignEntry and GarageCampaign.
     */
    public function ajax_get_values_list_campaign($garage_id)
    {
        $this->verify_ajax($this->request);

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $this->autoRender = false;

            $result['CampaignEntry'] = $this->CampaignEntry->getDataByGarage($garage_id, CakeSession::read('Auth.User.aag_region_id'));
            $result['GarageCampaign'] = $this->GarageCampaign->getDataByGarage($garage_id);
            return json_encode($result);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete GarageCampaign.
     */
    public function ajax_delete_value_campaign()
    {
        $this->verify_ajax($this->request);

        $garageCampaign = $this->GarageCampaign->findById($this->request->data['id']);

        if ($garageCampaign) {
            $garageId = $garageCampaign['GarageCampaign']['garage_id'];

            $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
            $garage_networks = $this->GarageNetwork->findAllByGarageId($garageId);
            if (empty($garage_networks)) {
                $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garageId, 'network_id' => '-1',));
            }

            if (
                $garage &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                (
                    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                    (
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                        $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                    )
                ) &&
                $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
            ) {
                $this->autoRender = false;
                $result = $this->GarageCampaign->delete($this->request->data['id']);

                if ($result) {
                    $this->LogChange->get_params_create_log_delete(
                        $garageCampaign['GarageCampaign'],
                        $this->GarageCampaign->table,
                        $this->Session->read('Auth'),
                        $garageId,
                        ConstantsLogType::GARAGE
                    );
                }
                return $result;
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add Garage equipment and software.
     */
    public function add_equipment_and_software_garage($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN |
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $config = CakeSession::read('Auth.User.Config');

            $garage_equipments = $this->GarageEquipment->findAllByGarageId($garage_id);
            $equipment_types = $this->EquipmentType->search_list($garage['Garage']['aag_region_id']);
            $equipments = $this->Equipment->search_list($garage['Garage']['aag_region_id']);
            $brands = $this->Brand->search_list();
            $suppliers = $this->Supplier->search_list();
            $billings_schedules = $this->BillingSchedule->search_list($garage['Garage']['aag_region_id']);
            $country = array();
            if (isset($garage['Garage']['province_id'])) {
                $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
            } elseif (isset($garage['Garage']['city_id'])) {
                $country = $this->Country->get_country_by_city($garage['Garage']['city_id']);
            }

            $software = $this->Software->search_list($garage['Garage']['aag_region_id']);
            $garage_software = $this->GarageSoftware->findAllByGarageId($garage_id);
            $software_types = $this->SoftwareType->search_list($garage['Garage']['aag_region_id']);
            $software_manufactures = $this->SoftwareManufacture->search_list();

            foreach ($garage_equipments as $key => $garage_tmp) {
                $garage_equipments[$key]['GarageEquipment']['start_date'] = Fecha::toFormatoVistaFecha($garage_tmp['GarageEquipment']['start_date']);
                $garage_equipments[$key]['GarageEquipment']['end_date'] = Fecha::toFormatoVistaFecha($garage_tmp['GarageEquipment']['end_date']);
            }

            if (!$this->request->is('get') && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Garage->table,
                    'Equipment.Equipment',
                    $user['User']['id'],
                    $garage_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::GARAGE
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $get_list_config_tabs = $this->Config->get_list_config_tabs();
            $this->setVarCancel($garage_id);
            $this->set(array(
                'garage' => $garage,
                'equipments' => $equipments,
                'garage_equipments' => $garage_equipments,
                'equipment_types' => $equipment_types,
                'suppliers' => $suppliers,
                'brands' => $brands,
                'garage_networks' => $garage_networks,
                'software' => $software,
                'garage_software' => $garage_software,
                'software_types' => $software_types,
                'software_manufactures' => $software_manufactures,
                'config' => $config,
                'billings_schedules' => $billings_schedules,
                'get_list_config_tabs' => $get_list_config_tabs,
                'country' => $country,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create ValueAdd RequestChange.
     */
    public function add_value_add($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $config = CakeSession::read('Auth.User.Config');
            $billings_schedules = $this->BillingSchedule->search_list($garage['Garage']['aag_region_id']);

            $country = array();
            if (isset($garage['Garage']['province_id'])) {
                $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
            } elseif (isset($garage['Garage']['city_id'])) {
                $country = $this->Country->get_country_by_city($garage['Garage']['city_id']);
            }

            $value_add = $this->ValueAdd->search_list();

            $garages_values_adds = $this->custom_pagination(
                $this->GarageValueAdd->_query('home'),
                array('GarageValueAdd.garage_id' => $garage_id),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'GarageValueAdd',
                null,
                'PaginatorOrderCustom'
            );

            $get_list_config_tabs = $this->Config->get_list_config_tabs();
            $this->setVarCancel($garage_id);
            $this->set(array(
                'garage' => $garage,
                'garage_networks' => $garage_networks,
                'value_add' => $value_add,
                'garages_values_adds' => $garages_values_adds,
                'config' => $config,
                'billings_schedules' => $billings_schedules,
                'get_list_config_tabs' => $get_list_config_tabs,
                'country' => $country,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete RequestedChange.
     */
    public function ajax_delete_requested_change($change_id)
    {
        $this->verify_ajax($this->request);

        $requestedChange = $this->RequestedChange->findById($change_id);

        if ($requestedChange) {
            $garageId = $requestedChange['RequestedChange']['garage_id'];

            $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
            $garage_networks = $this->GarageNetwork->findAllByGarageId($garageId);
            if (empty($garage_networks)) {
                $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garageId, 'network_id' => '-1',));
            }

            if (
                $garage &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
                $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
            ) {
                $delete = $this->RequestedChange->delete($change_id);
                if ($delete) {
                    $this->Session->setFlashInfo(__t(ConstantsMessages::WELL_DELETED));
                    $precess = 'true';
                    $error_text = '';
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
                    $precess = 'false';
                    $error_text = __t('RequestedChanges.Bad_Deleted');
                }
                $ret = array(
                    'precess' => $precess,
                    'error_text' => $error_text
                );

                $js_array = json_encode($ret);
                echo $js_array;

                $this->layout = $this->autoRender = false;
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get info contact. Called when creating a new garage to get BDM contact info.
     */
    public function ajax_get_info_contact()
    {
        $this->verify_ajax($this->request);

        $contact_id = $this->request->data['contact_id'];
        $contact_bdm = $this->Contact->findById($contact_id);

        if (
            $contact_bdm &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE, ConstantsPermissionsGrouping::CREATE_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR))
        ) {
            echo json_encode($contact_bdm);

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit Garage.
     */
    public function edit($garageId)
    {
        $user = $this->Acceso->user();
        $user_aag_region_id = $user['aag_region_id'];
        $user_role_id = $user['role_id'];

        $garage = $this->Garage->findByIdAndAagRegionId($garageId, $user_aag_region_id);

        if (
            $garage &&
            (
                $user_role_id == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    !in_array($user_role_id, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF))
                )
            )
        ) {
            $contactStaff = $this->GarageContactStaff->getContactStaffByGarageId($garageId);
            if ($contactStaff) {
                $primaryContact = $this->Contact->findById($contactStaff['GarageContactStaff']['contact_id']);
            } else {
                $primaryContact = null;
            }

            $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);
            if (empty($garageNetworks)) {
                $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garageId, 'network_id' => '-1',));
            }

            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE);

            $allContactsBdm = $this->Contact->getBDMContactWithoutAssociation($garageId, $user_aag_region_id);
            $contacts = $this->GarageContactBdm->getAllByGarageId($garageId);

            $workshopActivities = $this->WorkshopActivity->search_list();
            if (!$garage) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_GARAGE));
                $this->redirect(
                    array(
                        'controller' => 'home',
                        'action' => 'home',
                    )
                );
            }

            $conditions = array('Country.aag_region_id' => $user_aag_region_id);
            $countries = $this->Country->get_list_conditions($conditions);

            $countCountries = count($countries);
            $provinces = '';
            $allInactiveProvinces = $this->Province->getInactiveProvinceByAagRegion($user_aag_region_id);
            $inactiveProvince = array();

            $languages = $this->Language->getLanguagesCodeNameWithoutLoco();

            $configModuleRegionRole = $this->ConfigModuleRegionRole->findByConfigIdAndAagRegionIdAndRoleId(ConstantsConfigModules::CRM, $user_aag_region_id, $user_role_id);

            foreach ($allInactiveProvinces as $key => $provinceValue) {
                if (isset($garage['Garage']['province_id']) && $garage['Garage']['province_id'] == $key) {
                    $inactiveProvince = array(
                        'id' => $key,
                        'text' => $provinceValue
                    );
                }
            }

            if ($countCountries > 1) {
                if ($garage['Garage']['city_id'] != '') {
                    $provinceCity = $this->City->getProvinceByCity($garage['Garage']['city_id']);
                    if ($provinceCity != '') {
                        $provinces = $this->Province->getProvincesByCountry($provinceCity['Province']['country_id']);
                        $garage['Garage']['province_id'] = $provinceCity['Province']['id'];
                        $garage['Garage']['country'] = $provinceCity['Province']['country_id'];
                    }
                } else {
                    $provinces = $this->Province->getListByAagRegion($user_aag_region_id);
                }
            } else {
                $searchCountry = array_keys($countries);
                $provinces = $this->Province->getProvincesByCountry($searchCountry[0]);
            }

            $error = false;
            if (!$this->request->is('get') && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                //Add garage contacts bdm
                if (!empty($this->request->data['GarageContact']['GaragesBDM'])) {
                    $garages_contacts = $this->request->data['GarageContact']['GaragesBDM'];
                    foreach ($garages_contacts as $value) {
                        $contact_id_and_order = explode(' ', $value);
                        if (count($contact_id_and_order) < 3 || $contact_id_and_order[1] == 'undefined') {
                            if ($contact_id_and_order[1] == 'undefined') {
                                $order = $contact_id_and_order[2];
                            } else {
                                $order = $contact_id_and_order[1];
                            }
                            $garage_contact = array(
                                'garage_id' => $garageId,
                                'contact_id' => $contact_id_and_order[0],
                                'order' => $order
                            );
                            if ($this->GarageContactBdm->new_garage_contact_bdm($garage_contact)) {
                                $this->LogChange->add_contact_log(
                                    $this->GarageContactBdm->table,
                                    $this->Session->read('Auth'),
                                    $garage['Garage']['id'],
                                    ConstantsLogType::GARAGE,
                                    $value
                                );
                                echo ConstantsBooleans::YES;
                            } else {
                                echo ConstantsBooleans::NO;
                            }
                        } elseif (count($contact_id_and_order) == 3) {
                            $garage_contact_tmp = array(
                                'GarageContactBdm' => array(
                                    'id' => $contact_id_and_order[1],
                                    'order' => $contact_id_and_order[2],
                                )
                            );
                            $this->GarageContactBdm->change_order($garage_contact_tmp);
                        }
                    }
                }
                unset($this->request->data['Garage']['DistributorName']);
                if (isset($this->request->data['Garage']['slug']) && !empty(trim($this->request->data['Garage']['slug'])) && ($this->request->data['Garage']['slug'] != $garage['Garage']['slug'])) {
                    $slug = $this->request->data['Garage']['slug'];
                    $this->request->data['Garage']['slug'] = $this->Garage->generateSlug($slug, false);
                }

                if (!empty($this->request->data['GarageContact']['DeleteGaragesBDM'])) {
                    $garages_contacts_delete = $this->request->data['GarageContact']['DeleteGaragesBDM'];
                    foreach ($garages_contacts_delete as $garage_contact) {
                        $this->GarageContactBdm->delete($garage_contact);
                    }
                    $garage_contacts_activities = $this->GarageContactBdm->getAllByGarageId($garageId);
                    $counter = 1;
                    foreach ($garage_contacts_activities as $garage_contacts) {
                        $garage_contacts['GarageContactBdm']['order'] = $counter;
                        $counter++;
                        $this->GarageContactBdm->change_order($garage_contacts);
                    }
                }
                if (!$error) {
                    if (isset($this->request->data['city_id'])) {
                        $this->request->data['Garage']['city_id'] = $this->request->data['city_id'];
                    }
                    if (isset($this->request->data['province_id'])) {
                        $this->request->data['Garage']['province_id'] = $this->request->data['province_id'];
                    }

                    $oldData = $this->Garage->findById($garageId);

                    // Sets erp_email field only for benelux garages with erp_id.
                    // In any other case this field is set to null.
                    if (isset($this->request->data['Garage']['erp_id']) && $this->request->data['Garage']['aag_region_id'] == Configure::read('AAG_REGION_ID_BENELUX')) {
                        $erp = $this->Erp->findById($this->request->data['Garage']['erp_id']);
                        $this->request->data['Garage']['erp_email'] = $this->request->data['Garage']['ref_code'] . '_' . strtolower($erp['Erp']['erp_code']) . '@asm.com';
                    }

                    $user = $this->Session->read('Auth');
                    if (isset($this->request->data['request_changes'])) {
                        $result[] = $this->RequestedChange->create_request_edit(
                            $oldData['Garage'],
                            $this->request->data['Garage'],
                            $this->Garage->table,
                            $user,
                            $garageId,
                            ConstantsLogType::GARAGE
                        );
                        $newActivities = $this->request->data['GarageWorkshop']['workshop_activities'] != '' ?
                            $this->request->data['GarageWorkshop']['workshop_activities'] :
                            array();
                        $result[] = $this->RequestedChange->create_request_edit(
                            $this->GarageWorkshopActivity->getWorkshopActivitiesByGarageId($garage['Garage']['id']),
                            $newActivities,
                            $this->GarageWorkshopActivity->table,
                            $user,
                            $garageId,
                            ConstantsLogType::GARAGE,
                            'workshop_activity_id'
                        );
                        if (!in_array(false, $result)) {
                            $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                            $this->redirect($this->request->here);
                        } else {
                            $error = true;
                            $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                        }
                    } else {
                        $garageValidation = $this->Garage->edit_garage($this->request->data);
                        if ($garageValidation) {
                            $garageBd = $this->Garage->findById($garage['Garage']['id']);
                            // Send Garage to RM
                            $result = $this->RepairMaintenance->update_garage($garageBd);
                            if (!$result) {
                                $error = true;
                                $this->Session->setFlashError(__t("Repair.Error"));
                            }
                            $this->LogChange->get_params_create_log_edit(
                                $oldData['Garage'],
                                $garageBd['Garage'],
                                $this->Garage->table,
                                $user,
                                $garageId,
                                ConstantsLogType::GARAGE
                            );
                            if (isset($this->request->data['GarageWorkshop']['workshop_activities'])) {
                                $this->GarageWorkshopActivity->add_activities(
                                    $this->request->data['GarageWorkshop']['workshop_activities'],
                                    $garage['Garage']['id'],
                                    $user
                                );
                            }
                            if ($garageBd['Garage']['status'] == ConstantsGarageStatus::INACTIVE) {
                                $this->GarageNetwork->update_status_inactive_garage($garageBd['Garage']['id'], $garageBd['Garage']['aag_region_id'], $user);
                            }
                            if (!$error) {
                                $msg = h(sprintf(__t('Garage.Well_edit'), $garageBd['Garage']['name']));
                                $this->Session->setFlashSuccess($msg);
                                $this->redirect($this->request->here);
                            }
                        } else {
                            $error = true;
                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                        }
                    }
                }
            } else {
                $garage['GarageWorkshop']['workshop_activities'] = $this->GarageWorkshopActivity->getWorkshopActivitiesByGarageId($garage['Garage']['id']);
                $this->request->data = $garage;
                if ($this->request->data['Garage']['province_id']) {
                    $country = $this->Country->get_country_by_province($this->request->data['Garage']['province_id']);
                    $this->request->data['Garage']['country'] = $country['Country']['id'];
                }
            }

            $regions = $this->Region->region_list();
            $aag_regions = $this->AagRegion->region_list();
            $garageStatuses = Configure::read('Garage_Status');
            foreach ($garageStatuses as $key => $garage_status) {
                $garageStatuses[$key] = __t($garage_status);
            }

            $visitFrequency = $this->GarageVisitFrequency->find('list');
            $insuranceAgreements = $this->InsuranceAgreement->find('list');

            if (!$error) {
                $this->addContactsBdmActive($garageId);
            }
            $getListConfigTabs = $this->Config->get_list_config_tabs();

            $cities = '';
            if (isset($garage['Garage']['province_id'])) {
                $provinceId = $garage['Garage']['province_id'];
                $garage['Garage']['province'] = $provinceId;
                $cities = $this->City->getCitiesByProvince($provinceId);
                $countrySearch = $this->Country->get_country_by_province($provinceId);
                $garage['Garage']['country'] = $countrySearch['Country']['id'];
            }

            $positions = $this->Position->search_list();
            $salesArea = $this->SalesArea->searchListByRegion($user_aag_region_id);

            $this->setVarCancel($garageId);
            $this->set(array(
                'garage' => $garage,
                'cities' => $cities,
                'provinces' => $provinces,
                'inactive_province' => $inactiveProvince,
                'countries' => $countries,
                'count_countries' => $countCountries,
                'regions' => $regions,
                'garage_statuses' => $garageStatuses,
                'visit_frequency' => $visitFrequency,
                'insurance_agreements' => $insuranceAgreements,
                'workshop_activities' => $workshopActivities,
                'garage_networks' => $garageNetworks,
                'all_contacts_bdm' => $allContactsBdm,
                'get_list_config_tabs' => $getListConfigTabs,
                'aag_regions' => $aag_regions,
                'user_aag_region_id' => $user_aag_region_id,
                'user_role_id' => $user_role_id,
                'primaryContact' => isset($primaryContact) ? $primaryContact : null,
                'contacts' => $contacts,
                'erp_providers' => $this->Erp->getErpsByAagRegionId($user_aag_region_id),
                'networks_garage_last' => $this->Garage->getGarageNetworksLastInfo($garageId),
                'languages' => $languages,
                'positions' => $positions,
                'configModuleRegionRole' => $configModuleRegionRole,
                'sales_area' => $salesArea
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Ajax call to get province name and country id by postcode_province data.
     */
    public function ajax_get_province_and_country_by_postcode()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF))
        ) {
            $this->layout = $this->autoRender = false;
            $postcode = $this->request->query['postcode'];
            $countryCode = $this->request->query['country_code'];
            $postcodeProvince = $this->PostcodeProvince->findProvinceIdByPostcodeAndCountryCode($postcode, $countryCode);

            if ($postcodeProvince) {
                $provinceId = $postcodeProvince['PostcodeProvince']['province_id'];
                $provinceSearch = $this->Province->findById($provinceId);
                if ($provinceSearch) {
                    $countrySearch = $this->Country->get_country_by_province($provinceSearch['Province']['id']);
                    if ($countrySearch) {
                        $countryId = $countrySearch['Country']['id'];
                        $result = array($provinceSearch['Province']['name'], $countryId);
                    }
                }
            } else {
                $result = null;
            }

            return json_encode($result);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Garage RequestedCharge.
     */
    public function requested_changes($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $tmp = $this->RequestedChange->_query('search');
            $tmp['conditions'] = array(
                'RequestedChange.garage_id' => $garage_id,
            );
            $tmp['order'] = array(
                'RequestedChange.date' => 'desc'
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $conditions = $this->RequestedChange->conditions($searcher);

            $requested_changes = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'RequestedChange',
                null,
                'PaginatorOrderCustom'
            );
            $user = $this->Session->read('Auth');
            $workshop_activities = $this->WorkshopActivity->search_list();
            $get_list_config_tabs = $this->Config->get_list_config_tabs();
            $this->set(
                array(
                    'requested_changes' => $requested_changes,
                    'garage_id' => $garage_id,
                    'garage' => $garage,
                    'workshop_activities' => $workshop_activities,
                    'user_id' => $user['User']['id'],
                    'get_list_config_tabs' => $get_list_config_tabs,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add Garage contacts BDM.
     */
    // public function add_contacts_bdm($garage_id)
    // {
    //     $garage = $this->Garage->findById($garage_id);
    //     $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
    //     if (empty($garage_networks)) {
    //         $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
    //     }

    //     if (
    //         CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN |
    //         (
    //             $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
    //             CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
    //             $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
    //             (
    //                 !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
    //                 (
    //                     CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
    //                     $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
    //                 )
    //             ) &&
    //             $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
    //         )
    //     ) {
    //         $searcher = $this->request->query;
    //         $this->request->data['Search'] = $searcher;
    //         $positions = $this->Position->search_list();
    //         $positions_list = $this->Position->search_list_bdm();

    //         if (isset($searcher['active']) && $searcher['active'] == ConstantsBooleans::NO_ACTIVE) {
    //             $conditions = $this->Contact->conditions($searcher);
    //             $check_condition = true;
    //             foreach ($conditions as $condition) {
    //                 if (isset($condition['Contact']['position_id'])) {
    //                     $check_condition = false;
    //                 }
    //             }
    //             if ($check_condition) {
    //                 $positions_tmp = $this->Position->getPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::AAG_MANAGER));
    //                 $conditions[] = array('Contact.position_id' => Hash::extract($positions_tmp, '{n}.Position.id'));
    //             }

    //             $contacts = $this->custom_pagination(
    //                 array(),
    //                 $conditions,
    //                 ConstantsPagination::SIZE_PAGE_SMALL,
    //                 'Contact'
    //             );
    //             $is_checked_my_contacts = false;
    //         } else {
    //             $conditions = $this->Contact->conditions($searcher);
    //             $conditions[] = array('GarageContactBdm.garage_id' => $garage_id);
    //             foreach ($conditions as $condition) {
    //                 if (!isset($condition['Contact']['position_id'])) {
    //                     $positions_tmp = $this->Position->getPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::AAG_MANAGER));
    //                     $conditions[] = array('Contact.position_id' => Hash::extract($positions_tmp, '{n}.Position.id'));
    //                 }
    //             }

    //             $contacts = $this->custom_pagination(
    //                 $this->Contact->_query('my_contacts_bdm_garages'),
    //                 $conditions,
    //                 ConstantsPagination::SIZE_PAGE_SMALL,
    //                 'Contact'
    //             );
    //             $is_checked_my_contacts = true;
    //         }
    //         if ($check_condition) {
    //             $positions_tmp = $this->Position->getPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::AAG_MANAGER));
    //             $conditions[] = array('Contact.position_id' => Hash::extract($positions_tmp, '{n}.Position.id'));
    //         }

    //         $contacts = $this->custom_pagination(
    //             array(),
    //             $conditions,
    //             ConstantsPagination::SIZE_PAGE_SMALL,
    //             'Contact'
    //         );
    //         $is_checked_my_contacts = false;

    //         $garage_contacts = $this->GarageContactBdm->find('all', array(
    //             'conditions' => array(
    //                 'garage_id' => $garage_id
    //             )
    //         ));

    //         if (!$this->request->is('get') && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
    //             $user = $this->Session->read('Auth');
    //             $result = $this->RequestedChange->create_request_edit_description(
    //                 $this->Garage->table,
    //                 'Contact.BDM',
    //                 $user['User']['id'],
    //                 $garage_id,
    //                 $this->request->data['change_description'],
    //                 ConstantsLogType::GARAGE
    //             );
    //             if ($result) {
    //                 $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
    //                 $this->redirect($this->here);
    //             } else {
    //                 $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
    //             }
    //         }

    //         $this->set(array(
    //             'contacts' => $contacts,
    //             'garage_contacts' => $garage_contacts,
    //             'garage_id' => $garage_id,
    //             'garage' => $garage,
    //             'positions' => $positions,
    //             'positions_list' => $positions_list,
    //             'is_checked_my_contacts' => $is_checked_my_contacts,
    //             'garage_networks' => $garage_networks,
    //         ));
    //     } else {
    //         header('HTTP/1.0 401 Unauthorized');
    //         exit;
    //     }
    // }

    private function addContactsBdmActive($garage_id)
    {
        $garage = $this->Garage->findById($garage_id);
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        $searcher = $this->request->query;
        $this->request->data['Search'] = $searcher;
        $positions = $this->Position->search_list();
        $positions_list = $this->Position->search_list_bdm();

        $conditions = $this->Contact->conditions($searcher);
        $conditions[] = array('GarageContactBdm.garage_id' => $garage_id);
        foreach ($conditions as $condition) {
            if (!isset($condition['Contact']['position_id'])) {
                $positions_tmp = $this->Position->getPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::AAG_MANAGER));
                $conditions[] = array('Contact.position_id' => Hash::extract($positions_tmp, '{n}.Position.id'));
            }
        }

        $contacts = $this->custom_pagination(
            $this->Contact->_query('my_contacts_bdm_garages'),
            $conditions,
            ConstantsPagination::SIZE_PAGE_SMALL,
            'Contact'
        );
        $is_checked_my_contacts = true;

        $garage_contacts = $this->GarageContactBdm->find('all', array(
            'conditions' => array(
                'garage_id' => $garage_id
            )
        ));
        $old_data = $this->Garage->findById($garage_id);

        if (!$this->request->is('get')) {
            $user = $this->Session->read('Auth');
            if (!isset($this->request->data['request_changes'])) {
                $this->LogChange->get_params_create_log_edit(
                    $old_data['Garage'],
                    $this->request->data['Garage'],
                    $this->Garage->table,
                    $user,
                    $garage_id,
                    ConstantsLogType::GARAGE
                );

                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                $this->redirect($this->here);
            } else {
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Garage->table,
                    'Contact.BDM',
                    $user['User']['id'],
                    $garage_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::GARAGE
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }
        }

        $this->set(array(
            'contacts' => $contacts,
            'garage_contacts' => $garage_contacts,
            'garage_id' => $garage_id,
            'garage' => $garage,
            'positions' => $positions,
            'positions_list' => $positions_list,
            'is_checked_my_contacts' => $is_checked_my_contacts,
            'garage_networks' => $garage_networks,
        ));
    }

    /**
     * Add Garage Contact staff
     */
    public function add_contacts_staff($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $show_contacts_check = false;
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            $positions = $this->Position->search_list();
            $positions_list = $this->Position->search_list_staff();

            if (isset($searcher['active']) && $searcher['active'] == ConstantsBooleans::NO_ACTIVE) {
                $tmp = $this->Contact->_query('search_staff');
                $conditions = $this->Contact->conditions($searcher);
                $contacts = $this->custom_pagination(
                    $tmp,
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Contact',
                    null,
                    'PaginatorOrderCustom'
                );
                $is_checked_my_contacts = false;
            } else {
                $show_contacts_check = true;
                $tmp = $this->Contact->_query('my_contacts_staff_garages');
                $conditions = $this->Contact->conditions($searcher);
                $conditions[] = array('GarageContactStaff.garage_id' => $garage_id);
                $contacts = $this->custom_pagination(
                    $tmp,
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Contact'
                );
                $is_checked_my_contacts = true;
            }

            $garage_contacts = $this->GarageContactStaff->find('all', array(
                'conditions' => array(
                    'garage_id' => $garage_id
                )
            ));

            if (!$this->request->is('get')) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Garage->table,
                    'Contact.Staff',
                    $user['User']['id'],
                    $garage_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::GARAGE
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $get_list_config_tabs = $this->Config->get_list_config_tabs();
            $employees = $this->GarageEmployee->getDataByGarage($garage_id);
            $employee_types = $this->EmployeeType->getDataByGarage($garage_id);

            $this->set(array(
                'show_contacts_check' => $show_contacts_check,
                'contacts' => $contacts,
                'garage_contacts' => $garage_contacts,
                'garage_id' => $garage_id,
                'garage' => $garage,
                'positions' => $positions,
                'positions_list' => $positions_list,
                'is_checked_my_contacts' => $is_checked_my_contacts,
                'garage_networks' => $garage_networks,
                'get_list_config_tabs' => $get_list_config_tabs,
                'employees' => $employees,
                'employee_types' => $employee_types,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX edit GarageContactStaff change priority.
     */
    public function ajax_edit_garage_contact_staff_priority($dataId)
    {
        $this->verify_ajax($this->request);

        $garageStaff = $this->GarageContactStaff->findById($dataId);

        if ($garageStaff) {
            $garageId = $garageStaff['GarageContactStaff']['garage_id'];
            $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
            $garage_networks = $this->GarageNetwork->findAllByGarageId($garageId);
            if (empty($garage_networks)) {
                $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garageId, 'network_id' => '-1',));
            }

            if (
                $garage &&
                (
                    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                    (
                        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                        CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                        $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
                        $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                    )
                )
            ) {
                $this->layout = $this->autoRender = false;

                $priorityCheckerCount = $this->GarageContactStaff->getPriorityChecker($garageStaff['GarageContactStaff']['garage_id']);
                if ($priorityCheckerCount != 0) {
                    $this->GarageContactStaff->updateGarageContactStaffByGarageId($garageStaff['GarageContactStaff']['garage_id']);
                }
                if ($garageStaff['GarageContactStaff']['priority'] != 0) {
                    $garageStaff['GarageContactStaff']['priority'] = 0;
                } else {
                    $garageStaff['GarageContactStaff']['priority'] = 1;
                }

                if ($this->GarageContactStaff->edit_garage_contact_staff($garageStaff)) {
                    return true;
                }

                return false;
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add Garage contacts general branch manager.
     */
    public function add_contacts_general_branch_manager($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        $get_list_config_tabs = $this->Config->get_list_config_tabs();

        if (
            $garage &&
            $get_list_config_tabs[ConstantsTabs::GENERAL_BRANCH_MANAGER] == ConstantsBooleans::ACTIVE &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN |
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $searcher = $this->request->query;

            $this->request->data['Search'] = $searcher;
            $positions = $this->Position->search_list();
            $positions_list = $this->Position->search_list_general_branch_manager_garage();

            if (isset($searcher['active']) && $searcher['active'] == ConstantsBooleans::NO_ACTIVE) {
                $tmp = $this->Contact->_query('search_general_branch_manager_garage');
                $conditions = $this->Contact->conditions($searcher);
                $contacts = $this->custom_pagination(
                    $tmp,
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Contact'
                );
                $is_checked_my_contacts = false;
            } else {
                $tmp = $this->Contact->_query('my_contacts_general_branch_manager_garages');
                $conditions = $this->Contact->conditions($searcher);
                $conditions[] = array('GarageContactGeneralBranchManager.garage_id' => $garage_id);
                $contacts = $this->custom_pagination(
                    $tmp,
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Contact'
                );
                $is_checked_my_contacts = true;
            }

            if (!$this->request->is('get')) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Garage->table,
                    'Contact.General_branch_manager',
                    $user['User']['id'],
                    $garage_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::GARAGE
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $garage_contacts = $this->GarageContactGeneralBranchManager->find('all', array(
                'conditions' => array(
                    'garage_id' => $garage_id
                )
            ));

            $this->set(array(
                'contacts' => $contacts,
                'garage_contacts' => $garage_contacts,
                'garage_id' => $garage_id,
                'garage' => $garage,
                'positions' => $positions,
                'positions_list' => $positions_list,
                'is_checked_my_contacts' => $is_checked_my_contacts,
                'garage_networks' => $garage_networks,
                'get_list_config_tabs' => $get_list_config_tabs,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add Garage contacts from My data.
     */
    public function garage_add_contacts($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id')) &&
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
        ) {
            $searcher = $this->request->query;

            $this->request->data['Search'] = $searcher;
            $positions = $this->Position->search_list();
            $positions_list = $this->Position->search_list_general_branch_manager_garage();

            if (isset($searcher['active']) && $searcher['active'] == ConstantsBooleans::NO_ACTIVE) {
                $tmp = $this->Contact->_query('search_general_branch_manager_garage');
                $conditions = $this->Contact->conditions($searcher);
                $contacts = $this->custom_pagination(
                    $tmp,
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Contact'
                );
            } else {
                $tmp = $this->Contact->_query('my_contacts_general_branch_manager_garages');
                $conditions = $this->Contact->conditions($searcher);
                $conditions[] = array('GarageContactGeneralBranchManager.garage_id' => $garage_id);
                $contacts = $this->custom_pagination(
                    $tmp,
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Contact'
                );
            }

            $garage_contacts = $this->GarageContactGeneralBranchManager->find('all', array(
                'conditions' => array(
                    'garage_id' => $garage_id
                )
            ));

            $this->set(array(
                'contacts' => $contacts,
                'garage_contacts' => $garage_contacts,
                'garage_id' => $garage_id,
                'garage' => $garage,
                'positions' => $positions,
                'positions_list' => $positions_list,
                'garage_networks' => $garage_networks,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add Garage comments.
     */
    public function add_comments($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN |
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $search_comments = $this->GarageComment->_query('Search');
            $conditions_comments = array(
                'GarageComment.garage_id' => $garage_id
            );
            $garage_comments = $this->custom_pagination(
                $search_comments,
                $conditions_comments,
                ConstantsPagination::SIZE_PAGE_SMALL,
                $this->GarageComment
            );
            $get_list_config_tabs = $this->Config->get_list_config_tabs();
            $this->setVarCancel($garage_id);
            $this->set(array(
                'garage' => $garage,
                'garage_id' => $garage_id,
                'garage_comments' => $garage_comments,
                'garage_networks' => $garage_networks,
                'get_list_config_tabs' => $get_list_config_tabs,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Admin Garage tab to see log changes and comments.
     */
    public function add_admin($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN |
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            $tmp = $this->LogChange->_query('search');
            $tmp['conditions'] = array(
                'LogChange.garage_id' => $garage_id,
            );
            $tmp['order'] = array(
                'LogChange.id' => 'desc'
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $conditions = $this->LogChange->conditions($searcher);

            $logs_changes = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'LogChange',
                null,
                'PaginatorOrderCustom'
            );
            $get_list_config_tabs = $this->Config->get_list_config_tabs();

            $this->set(
                array(
                    'logs_changes' => $logs_changes,
                    'garage_id' => $garage_id,
                    'garage' => $garage,
                    'garage_networks' => $garage_networks,
                    'get_list_config_tabs' => $get_list_config_tabs,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarCancel($garage_id)
    {
        $cancel_action = array(
            'url_cancel' => array(
                'controller' => 'garages',
                'action' => 'view',
                $garage_id
            ),
        );

        $this->set(array(
            'cancel_action' => $cancel_action,
            'garage_id' => $garage_id,
        ));
    }

    /**
     * AJAX search home.
     */
    public function ajax_search_home($type_param = null)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        if (
            $role_id == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                (
                    !in_array($role_id, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ||
                    (
                        $role_id == ConstantsRoles::DISTRIBUTOR &&
                        $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                    )
                )
            )
        ) {
            $networks = $this->Network->getListByRegion($aag_region_id);
            $trading_groups = $this->TradingGroup->getIndependent($aag_region_id);
            $services = $this->Service->search_list();
            $vehicles_types = $this->VehicleType->search_list();
            $networks_statuses = Configure::read('Network_Status');
            foreach ($networks_statuses as $key => $network_status) {
                $networks_statuses[$key] = __t($network_status);
            }
            $lead_sources = Configure::read('lead_source');
            foreach ($lead_sources as $key => $lead_source) {
                $lead_sources[$key] = __t($lead_source);
            }

            $province_list = $this->Garage->Province->getListByRegion($aag_region_id);
            $distributors_list = $this->Distributor->getListByRegion($aag_region_id);
            $garages_permissions = $this->Permission->getByGroupingPermission(ConstantsGroupingPermissions::GARAGE);
            $salesArea = $this->SalesArea->searchListByRegion($aag_region_id);

            $regions = array();
            // super admin can search through all regions
            if ($user['role_id'] == ConstantsRoles::SUPER_ADMIN) {
                $regions = $this->AagRegion->region_list();
            }

            $searcher = $this->request->query;
            $searcher = self::multipleFieldsArrayCheck($searcher);
            $searcher['aag_region_id'] = $aag_region_id;

            $this->request->data['Search'] = $searcher;

            $lists_bdm = $this->GarageContactBdm->getAllBDMContacts($aag_region_id);
            $bdm = array();
            foreach ($lists_bdm as $key => $list_bdm) {
                $id = $list_bdm['Contact']['id'];
                $bdm[$id] = $list_bdm[0]['full_name'];
            }

            $conditions = $this->Garage->conditions($searcher);
            $conditions[] = $this->User->viewUserGarages($this->Acceso->user());

            if (!empty($searcher['search_my_customers'])) {
                $tmp = array(
                    'joins' => array(
                        array(
                            'table' => 'garages_contacts_bdm',
                            'alias' => 'GarageContactBdm',
                            'type' => 'INNER',
                            'conditions' => array(
                                'GarageContactBdm.garage_id = Garage.id'
                            )
                        ),
                    ),
                    'order' => array(
                        'Garage.name' => 'asc'
                    )
                );
            } else {
                $tmp = array(
                    'order' => array(
                        'Garage.name' => 'asc'
                    )
                );
            }

            $garages = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Garage',
                'PaginatorUrlGarage'
            );

            foreach ($garages as $key => $garage) {
                $garageId = $garage['Garage']['id'];
                $garage_tmp = $this->Garage->findById($garageId);
                $garages[$key]['Distributors'] = $this->GarageDistributor->findAllByGarageId($garageId);
                $garages[$key]['Garage']['status'] = $garage_tmp['Garage']['status'];
                $garages[$key]['FiguresDetail'] = $this->GarageFigureDetail->findByCustomerNo($garage['Garage']['g_number_id']);
                $garages[$key]['BDMS'] = $this->GarageContactBdm->getBDMByGarage($garageId);

                if ($garage['Garage']['last_visit']) {
                    $interval = strtotime(date('Y-m-d')) - strtotime($garage['Garage']['last_visit']);
                    $garages[$key]['LatestVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
                } else {
                    $garages[$key]['LatestVisit'] = '--';
                }

                $networkId = $this->AagRegion->get_aag_region_network($garage['Garage']['aag_region_id']);
                $garageNetwork = $this->GarageNetwork->findAllByGarageIdAndNetworkId($garageId, $networkId);

                if ($garageNetwork) {
                    // we add all options for networks buttons if there is more than 1 garagenetwork
                    foreach ($garageNetwork as $gn) {
                        $garages[$key]['ShowGarageAccess'][] = $garage['Garage']['status'] == ConstantsGarageStatus::ACTIVE &&
                            $gn['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE;
                        $garages[$key]['NetworkId'][] = $gn['GarageNetwork']['network_id'];
                        $garages[$key]['GarageNetworkId'][] = $gn['GarageNetwork']['id'];
                        $network = $this->Network->findById($gn['GarageNetwork']['network_id']);
                        $garages[$key]['Network'][] = $network['Network']['name'];
                    }
                }
            }

            $distributors = array();
            if (!empty($searcher['distributor_id'])) {
                $distributors = $this->Distributor->getDistributorsCompleteNamesByListOfIds($searcher['distributor_id'], $aag_region_id);
            }

            $this->set(array(
                'garages' => $garages,
                'trading_groups' => $trading_groups,
                'networks' => $networks,
                'services' => $services,
                'vehicles_types' => $vehicles_types,
                'networks_statuses' => $networks_statuses,
                'lead_sources' => $lead_sources,
                'province_list' => $province_list,
                'distributors' => $distributors,
                'distributors_list' => $distributors_list,
                'selected_distributors' => json_encode($distributors),
                'garages_permissions' => $garages_permissions,
                'regions' => $regions,
                'bdm' => $bdm,
                'user_role' => $role_id,
                'sales_area' => $salesArea
            ));

            $type_url_ajax = Configure::read('TypeSearchAjax');

            $this->layout = null;
            $this->render($type_url_ajax[$type_param]);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Garage planning visit Excel generation.
     */
    public function garages_planning_visit_excel($controller)
    {
        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($role_id, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $garages_id = $this->request->query;
            $garages = array();

            foreach ($garages_id as $key => $garage_id) {
                $garage = $this->Garage->findByIdAndAagRegionId($key, $aag_region_id);
                if ($garage) {
                    $garages[] = $garage;
                }
            }

            $this->calcCrmVisits($garages, $controller);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Garages Excel generation.
     */
    public function garages_excel($controller)
    {
        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        if (
            $role_id == ConstantsRoles::SUPER_ADMIN ||
            (
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array($role_id, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ||
                        (
                            $role_id == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    )
                ) ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    !in_array($role_id, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
                )
            )
        ) {
            ini_set('memory_limit', '-1');
            set_time_limit(18000);

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $conditions = array();
            if (isset($aag_region_id)) {
                $conditions['Garage.aag_region_id'] = $aag_region_id;
            }
            if (isset($searcher['distributor_id']) && $searcher['distributor_id'] != null) {
                $conditions['Distributor.id IN'] = $searcher['distributor_id'];
            }
            if (isset($searcher['network_id']) && $searcher['network_id'] != null) {
                $conditions['Network.id IN'] = $searcher['network_id'];
            }
            if (!empty($searcher['city_id'])) {
                $containsZero = false;
                foreach ($searcher['city_id'] as $value) {
                    if ($value == 0) {
                        $containsZero = true;
                        break;
                    }
                }
                if ($containsZero && $searcher['city_id']) {
                    $conditions[]['OR'] = array(
                        'Garage.city_id IN' => $searcher['city_id'],
                        'Garage.city_id IS NULL'
                    );
                } elseif ($containsZero) {
                    $conditions[] = 'Garage.city_id IS NULL';
                } else {
                    $conditions['Garage.city_id IN'] = $searcher['city_id'];
                }
            }
            if (!empty($searcher['status_id'])) {
                $conditions['GarageNetwork.status IN'] = $searcher['status_id'];
            }

            $status = Configure::read('Network_Status');
            $conditionStatus = '';
            foreach ($status as $statusKey => $statusValue) {
                $conditionStatus .= "WHEN GarageNetwork.status = $statusKey THEN '" . __t($statusValue) . "' ";
            }
            $garages = $this->Garage->find(
                'all',
                array(
                    'conditions' => array(
                        $this->Garage->conditions($searcher),
                        $this->User->viewUserGarages($this->Acceso->user()),
                        $conditions
                    ),
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.garage_id = Garage.id',
                            ),
                            'fields' => array(
                                'GarageNetwork.id',
                                'GarageNetwork.garage_id',
                                'GarageNetwork.network_id',
                                'GarageNetwork.annex_detail_id',
                                'GarageNetwork.contract_received_date',
                                'GarageNetwork.contract_start_date',
                                'GarageNetwork.date_on_hold',
                                'GarageNetwork.leaving_date',
                                'GarageNetwork.status',
                            ),
                        ),
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Network.id = GarageNetwork.network_id',
                            ),
                            'fields' => array(
                                'Network.id',
                                'Network.name',
                            ),
                        ),
                        array(
                            'alias' => 'AnnexDetail',
                            'table' => 'annex_details',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'AnnexDetail.id = GarageNetwork.annex_detail_id',
                            ),
                        ),
                        array(
                            'alias' => 'GarageDistributor',
                            'table' => 'garages_distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageDistributor.garage_id = Garage.id',
                                'GarageDistributor.id = (
                                    SELECT MIN(gd1.id)
                                    FROM garages_distributors AS gd1
                                    WHERE gd1.garage_id = Garage.id
                                )',
                            ),
                            'fields' => array(
                                'GarageDistributor.id',
                                'GarageDistributor.garage_id',
                                'GarageDistributor.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = GarageDistributor.distributor_id',
                            ),
                            'fields' => array(
                                'Distributor.id',
                                'Distributor.trading_group_id',
                                'Distributor.name',
                                'Distributor.account_number',
                                'Distributor.MAMID',
                                'Distributor.address1',
                                'Distributor.town',
                                'Distributor.postcode',
                                'Distributor.association_type_id',
                            ),
                        ),
                        array(
                            'alias' => 'GarageContactsStaff',
                            'table' => 'garages_contacts_staff',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageContactsStaff.garage_id = Garage.id',
                                'GarageContactsStaff.priority' => 1,
                            ),
                            'fields' => array(
                                'GarageContactsStaff.id',
                                'GarageContactsStaff.garage_id',
                                'GarageContactsStaff.priority',
                            ),
                        ),
                        array(
                            'alias' => 'Contact',
                            'table' => 'contacts',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Contact.id = GarageContactsStaff.contact_id',
                            ),
                            'fields' => array(
                                'Contact.id',
                                'Contact.first_name',
                                'Contact.last_name',
                                'Contact.email',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                            'fields' => array(
                                'TradingGroup.id',
                                'TradingGroup.name',
                            ),
                        ),
                        array(
                            'alias' => 'AagRegion',
                            'table' => 'aag_regions',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'AagRegion.id = Garage.aag_region_id',
                            ),
                            'fields' => array(
                                'AagRegion.id',
                                'AagRegion.name',
                            ),
                        ),
                    ),
                    'fields' => array(
                        'Garage.id',
                        'Garage.g_number_id',
                        'Garage.name',
                        'Garage.slug',
                        'Garage.province_id',
                        'Garage.town',
                        'Garage.address1',
                        'Garage.postcode',
                        'Garage.address2',
                        'Garage.address3',
                        'Garage.address4',
                        'Garage.sales_area_id',
                        'Garage.phone',
                        'Garage.email',
                        'Garage.ref_code',
                        'Garage.last_visit',
                        'GarageNetwork.id',
                        'GarageNetwork.garage_id',
                        'GarageNetwork.network_id',
                        'GarageNetwork.annex_detail_id',
                        'GarageNetwork.contract_received_date',
                        'GarageNetwork.contract_start_date',
                        'GarageNetwork.date_on_hold',
                        'GarageNetwork.leaving_date',
                        'GarageNetwork.status',
                        'AnnexDetail.*',
                        'GROUP_CONCAT(DISTINCT CONCAT(Network.name, " - ", IFNULL(AnnexDetail.name_' . __l() . ', ""), "(",
                        CASE
                            ' . $conditionStatus . '
                            ELSE GarageNetwork.status
                        END
                        , ")") SEPARATOR ", ") AS network_concatenated_fields',
                        'GROUP_CONCAT(DISTINCT CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.contract_received_date, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS contract_received_date',
                        'GROUP_CONCAT(DISTINCT CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.contract_start_date, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS contract_start_date',
                        'GROUP_CONCAT(DISTINCT CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.date_on_hold, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS date_on_hold',
                        'GROUP_CONCAT(DISTINCT CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.leaving_date, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS leaving_date',
                        'Network.id',
                        'Network.name',
                        'Distributor.id',
                        'Distributor.trading_group_id',
                        'Distributor.name',
                        'Distributor.account_number',
                        'Distributor.MAMID',
                        'Distributor.address1',
                        'Distributor.town',
                        'Distributor.postcode',
                        'Distributor.association_type_id',
                        'Contact.id',
                        'Contact.first_name',
                        'Contact.last_name',
                        'Contact.email',
                        'TradingGroup.id',
                        'TradingGroup.name',
                        'AagRegion.id',
                        'AagRegion.name',
                    ),
                    'order' => array('Garage.name' => 'asc'),
                    'group' => array('Garage.id'),
                )
            );

            $all_garage_statuses = Configure::read('Garage_Status');

            foreach ($all_garage_statuses as $key => $status) {
                $all_garage_statuses[$key] = __t($status);
            }

            $province_list = $this->Garage->Province->find('list');
            $association_list = $this->AssociationType->search_list($aag_region_id);
            $salesArea = $this->SalesArea->searchListByRegion($aag_region_id);

            foreach ($garages as $key => $garage) {
                $garages[$key]['BDMS'] = $this->GarageContactBdm->getBDMByGarage($garage['Garage']['id']);
                $garages[$key][0]['MAM_7'] = $this->GarageSoftware->findByGarageIdAndSoftwareId($garage['Garage']['id'], 7);
                $garages[$key][0]['TECHNICAL_HELPLINE_6'] = $this->GarageSoftware->findByGarageIdAndSoftwareId($garage['Garage']['id'], 6);
                $garages[$key][0]['PAINTING_1'] = $this->Order->findByGarageIdAndOrderTypeId($garage['Garage']['id'], 1);
                $garages[$key][0]['SIGNAGE_2'] = $this->Order->findByGarageIdAndOrderTypeId($garage['Garage']['id'], 2);
                if ($garage['Garage']['last_visit']) {
                    $interval = strtotime(date('Y-m-d')) - strtotime($garage['Garage']['last_visit']);
                    $garages[$key]['LatestVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
                } else {
                    $garages[$key]['LatestVisit'] = '--';
                }
            }


            $config = CakeSession::read('Auth.User.Config');

            $this->set(array(
                'garages' => $garages,
                'controller' => $controller,
                'config' => $config,
                'all_garage_statuses' => $all_garage_statuses,
                'province_list' => $province_list,
                'association_list' => $association_list,
                'sales_area' => $salesArea,
            ));

            $this->render('/Garages/Elements/export_excel_garages');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    private function calcCrmVisits($garages, $controller = null)
    {
        $all_garage_statuses = Configure::read('Garage_Status');

        foreach ($all_garage_statuses as $key => $status) {
            $all_garage_statuses[$key] = __t($status);
        }

        $province_list = $this->Garage->Province->find('list');


        foreach ($garages as $key => $garage) {
            $garages[$key]['BDMS'] = $this->GarageContactBdm->getBDMByGarage($garage['Garage']['id']);
            if ($garage['Garage']['last_visit']) {
                $interval = strtotime(date('Y-m-d')) - strtotime($garage['Garage']['last_visit']);
                $garages[$key]['LatestVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
            } else {
                $garages[$key]['LatestVisit'] = '--';
            }
        }


        $config = CakeSession::read('Auth.User.Config');

        $this->set(array(
            'garages' => $garages,
            'controller' => $controller,
            'config' => $config,
            'all_garage_statuses' => $all_garage_statuses,
            'province_list' => $province_list,
        ));

        set_time_limit(18000);
        ini_set('memory_limit', '-1');

        $this->render('/Garages/Elements/export_excel_garages');
        $this->response->type('xlsx');
        $this->layout = false;
    }

    /**
     * AJAX get event list for calendar shown in My Garage for garage role access or in Oppening Hours Garage tab.
     */
    public function ajax_events_list($garage_id)
    {
        $this->verify_ajax($this->request);

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    (
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::GARAGE ||
                        (
                            (
                                !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                                (
                                    CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                                    $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                                )
                            ) &&
                            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                        )
                    ) &&
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES)
                )
            )
        ) {
            $events = $this->Garage->getCalendarEvents($garage_id);
            echo json_encode($events);
            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get garages.
     */
    public function ajax_get_garages($user_aag_region_id)
    {
        $this->verify_ajax($this->request);
        if (!isset($this->request->query['page'])) {
            $this->request->query['page'] = '';
        }
        if (!isset($this->request->query['search'])) {
            $this->request->query['search'] = '';
        }

        $this->params['named'] = array('page' => $this->request->query['page']);
        $query_tmp = $this->Garage->_query('ajax_garages');
        if (isset($this->request->query['network_id'])) {
            $query_tmp['joins'][] =
                array(
                    'table' => 'garages_networks',
                    'alias' => 'GarageNetwork',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id'
                    )
                );
            $conditions[] = array('GarageNetwork.network_id' => $this->request->query['network_id']);
        }

        $conditions[] = array('Garage.complete_name LIKE' => '%' . $this->request->query['search'] . '%');
        $conditions[] = array('Garage.aag_region_id' => $user_aag_region_id);

        if (!empty($this->request->query['garage_filter_branch'])) {
            $query_tmp['joins'][] =
                array(
                    'alias' => 'GarageDistributor',
                    'table' => 'garages_distributors',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Garage.id = GarageDistributor.garage_id'
                    ),
                );
            $conditions[] = array('GarageDistributor.distributor_id' => $this->request->query['garage_filter_branch']);
        }
        if (!empty($this->request->query['garage_filter_bdm'])) {
            $query_tmp['joins'][] =
                array(
                    'alias' => 'GarageContactBdm',
                    'table' => 'garages_contacts_bdm',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Garage.id = GarageContactBdm.garage_id'
                    ),
                );
            $conditions[] = array('GarageContactBdm.contact_id' => $this->request->query['garage_filter_bdm']);
        }

        if (!empty($this->request->query['garage_filter_rsm'])) {
            $contacts_tmp = $this->Contact->findListByContactId($this->request->query['garage_filter_rsm']);
            $contacts = array();
            $contacts[] = $this->request->query['garage_filter_rsm'];
            foreach ($contacts_tmp as $contact_tmp_id => $contact_tmp) {
                $contacts[] = $contact_tmp_id;
            }
            $garages = $this->Garage->getGaragesByContacts($contacts, $user_aag_region_id);
            $flag_condition = true;
            foreach ($conditions as $key => $condition) {
                if (isset($condition['Garage.id'])) {
                    $conditions[$key]['Garage.id'] = Hash::extract($garages, '{n}');
                    $flag_condition = false;
                }
            }
            if ($flag_condition) {
                $conditions[]['Garage.id'] = Hash::extract($garages, '{n}');
            }
        }

        if (!empty($this->request->query['garage_filter_customer_status'])) {
            $conditions[] = array('Garage.status' => $this->request->query['garage_filter_customer_status']);
        }

        $garages = $this->custom_pagination(
            $query_tmp,
            $conditions,
            ConstantsPagination::SIZE_PAGE_SMALL,
            'Garage'
        );

        $this->autoRender = null;
        return json_encode($garages);
    }

    /**
     * Garage modal search user permissions list.
     */
    public function ajax_search_users_garage_permissions()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                (
                    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ||
                    (
                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                        $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                    )
                )
            )
        ) {
            $search = $this->request->query;
            $data = $this->request->data;

            $contacts_tmp = $this->whoViewThisGarage($data['garage_id'], $search['permission_id']);
            $query = $this->User->_query('getByContactId');
            $conditions['User.contact_id'] = $contacts_tmp;
            $query['conditions'] = $conditions;
            $query['order'] = array('User.name');
            $users_garage_permissions = $this->User->find('all', $query);

            $positions = $this->Position->search_list();
            $roles = $this->Role->search_list();
            $garages_permissions = $this->Permission->getByGroupingPermission(ConstantsGroupingPermissions::GARAGE);

            $this->set(array(
                'users_garage_permissions' => $users_garage_permissions,
                'garages_permissions' => $garages_permissions,
                'positions' => $positions,
                'roles' => $roles
            ));

            $this->layout = null;
            $this->render('/Garages/Elements/garages_permissions_search');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function whoViewThisGarage($garage_id, $permission_id)
    {
        $garage = $this->Garage->findById($garage_id);
        $garage_networks = $this->GarageNetwork->findNetworksByGarage($garage_id);
        $group_permissions = $this->GroupPermissionPermission->getAllGroupPermissionsListByPermissionId($permission_id);

        $positions = $this->PositionConfig->getPositionsConfigToGarage($group_permissions, $garage_networks);

        $contacts = $this->Contact->getContactListByPositionId($positions);

        return $contacts;
    }

    /**
     * Edit GarageAgreement from Distributor/Network Garage tab.
     */
    public function edit_garage_agreement($garage_agreement_id)
    {
        $agreement = $this->GarageAgreement->findById($garage_agreement_id);

        $garageId = $agreement['GarageAgreement']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $garage &&
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES)
        ) {
            $cancel_action = array(
                'url_cancel' => array(
                    'controller' => 'garages',
                    'action' => 'add_dis_and_net_garage',
                    $garage['Garage']['id']
                ),
            );
            if (!$this->request->is('get')) {
                if ($this->GarageAgreement->edit_garage_agreement($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));

                    $this->redirect(
                        array(
                            'controller' => 'garages',
                            'action' => 'add_dis_and_net_garage',
                            $garage['Garage']['id']
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->set(array(
                'cancel_action' => $cancel_action,
                'agreement' => $agreement,
                'garage' => $garage,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX update Edit/View button.
     */
    public function ajax_update_edit()
    {
        $this->verify_ajax($this->request);
        // T001 SECURITY - It is not changed
        CakeSession::write('Auth.User.edit_enabled', (CakeSession::read('Auth.User.edit_enabled') ? ConstantsBooleans::NO_ACTIVE : ConstantsBooleans::ACTIVE));
        $this->autoRender = false;
    }

    /**
     * AJAX get Garages name by AagRegion.
     * Used for dynamic selects.
     */
    public function get_garages_name_region()
    {
        $this->verify_ajax($this->request);

        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        $garages_name = $this->Garage->obtenerPosiblesGaragesAjaxRegion($this->request->query, $aag_region_id);

        $list_garages = array();
        foreach ($garages_name as $key => $garage) {
            $list_garages[] = array(
                'id' => $key,
                'text' => $garage,
            );
        }

        $list_garages_completa['items'] = $list_garages;

        return json_encode($list_garages_completa);
    }

    /**
     * AJAX get Garage live names.
     * Used for dynamic selects.
     */
    public function get_garages_name_live()
    {
        $this->verify_ajax($this->request);

        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        $garages_name = $this->Garage->getPossiblesGaragesLiveAjax($this->request->query, $aag_region_id);

        $list_garages = array();
        foreach ($garages_name as $key => $garage) {
            $list_garages[] = array(
                'id' => $key,
                'text' => $garage,
            );
        }

        $list_garages_complete['items'] = $list_garages;

        return json_encode($list_garages_complete);
    }

    /**
     * Garage training credits tab.
     */
    public function training_credits($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF))
                )
            )
        ) {
            $user = $this->Acceso->user();
            $aag_region_id = $user['aag_region_id'];

            $get_list_config_tabs = $this->Config->get_list_config_tabs();
            $networks = $this->Network->getNetworksTraining($aag_region_id);
            $user = $this->Acceso->user();
            $contact_id = $user['contact_id'];

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $searcher['Garage_name'] = $garage_id;

            if (isset($searcher['is_actual']) && $searcher['is_actual'] == ConstantsBooleans::ACTIVE) {
                $is_actual_checked = true;
            } elseif (isset($searcher['is_actual']) && $searcher['is_actual'] == ConstantsBooleans::NO_ACTIVE) {
                $is_actual_checked = false;
            } else {
                $searcher['is_actual'] = 1;
                $is_actual_checked = true;
            }

            $garages_networks = $this->custom_pagination(
                $this->Garage->_queryTrainingGarage('home_training', $garage_id),
                $this->Garage->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Garage',
                null,
                'PaginatorOrderCustom'
            );

            $sums_given_list = $this->TrainingAllowance->getAllGivenSumsList();
            $sums_spent_list = $this->TrainingAllowance->getAllSpentSumsList();

            $this->set(array(
                'garage' => $garage,
                'garage_id' => $garage_id,
                'garages_networks' => $garages_networks,
                'network_list' => $networks,
                'user' => $user['Role']['id'],
                'contact_id' => $contact_id,
                'garage' => $this->Garage->findById($garage_id),
                'get_list_config_tabs' => $get_list_config_tabs,
                'trainings_allowances' => $this->TrainingAllowance->getListAllowanceFromGarageId($garage_id),
                'is_actual_checked' => $is_actual_checked,
                'sums_given_list' => $sums_given_list,
                'sums_spent_list' => $sums_spent_list,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Garage training credits movements tab.
     */
    public function training_credits_movements($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF))
                )
            )
        ) {
            $user = $this->Acceso->user();
            $aag_region_id = $user['aag_region_id'];

            $get_list_config_tabs = $this->Config->get_list_config_tabs();
            $networks = $this->Network->getNetworksTraining($aag_region_id);
            $user = $this->Acceso->user();
            $contact_id = $user['contact_id'];

            $cancelled = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            if (isset($searcher['is_actual']) && $searcher['is_actual'] == ConstantsBooleans::ACTIVE) {
                $is_actual_checked = true;
            } elseif (isset($searcher['is_actual']) && $searcher['is_actual'] == ConstantsBooleans::NO_ACTIVE) {
                $is_actual_checked = false;
            } else {
                $searcher['is_actual'] = ConstantsBooleans::YES;
                $is_actual_checked = true;
            }

            $garages_networks = $this->custom_pagination(
                $this->TrainingCreditMovement->_query('home', $garage_id),
                $this->TrainingCreditMovement->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'TrainingCreditMovement',
                null,
                'PaginatorOrderCustom'
            );

            $array_delegate_name = array();
            if (!empty($searcher['Delegate_name'])) {
                $array_delegate_name = $this->Contact->getContactsNameByIdContact($searcher['Delegate_name']);
            }

            $this->set(array(
                'garage' => $garage,
                'garage_id' => $garage_id,
                'garages_networks' => $garages_networks,
                'network_list' => $networks,
                'user' => $user['Role']['id'],
                'contact_id' => $contact_id,
                'garage' => $this->Garage->findById($garage_id),
                'get_list_config_tabs' => $get_list_config_tabs,
                'cancelled' => $cancelled,
                'array_delegate_name' => $array_delegate_name,
                'reasons_allowance_list' => $this->ReasonAllowance->search_list(),
                'trainings_allowances' => $this->TrainingAllowance->getListAllowanceFromGarageId($garage_id),
                'is_actual_checked' => $is_actual_checked,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX autocomplete Garage G number for UK AAGRegion called in garage creation.
     */
    public function ajax_autocomplete_g_number()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.aag_region_id') != ConstantsAAGRegionId::BENELUX &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE, ConstantsPermissionsGrouping::CREATE_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR))
        ) {
            $maxGNumber = $this->Garage->find('first', array(
                'fields' => array('MAX(CAST(SUBSTRING(g_number_id, 2) AS SIGNED)) AS max_g_number'),
            ));
            if (!empty($maxGNumber)) {
                $maxGNumber = intval($maxGNumber[0]['max_g_number']) + 1;
            } else {
                $maxGNumber = 1;
            }
            $this->autoRender = false;
            $this->response->type('json');
            echo json_encode(['new_g_number' => 'G' . $maxGNumber]);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * This function recieves the request of a garage search, multiple fields are turned into an array.
     * Depending on whether the request comes from ajax or not a string of ids(separated by comma) or an array is recieved.
     *
     * @param requestData search request.
     * @return requestData search request without strings.
     */

    private function multipleFieldsArrayCheck($requestData)
    {
        $multipleSearchFields = array('trading_group_id', 'aag_region_id', 'country_id', 'city_id', 'status_id', 'annex_detail_id', 'bdm_id', 'service_id', 'distributor_id', 'vehicle_type_id', 'lead_source', 'external_agreements', 'internal_agreements', 'erp_id');

        foreach ($multipleSearchFields as $value) {
            if (isset($requestData[$value]) && !empty($requestData[$value]) && !is_array($requestData[$value])) {
                $requestData[$value] = explode(',', $requestData[$value]);
            }
        }

        return $requestData;
    }

    /**
     * Export Garages in PDF.
     */
    public function export_garages_pdf($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));

        if (
            $garage &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF))
        ) {
            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $aagRegionId = $garage['Garage']['aag_region_id'];

            $garage['WorkshopActivities'] = $this->GarageWorkshopActivity->export_workshop_activities($garage_id);
            $garage['ContactsBDM'] = $this->GarageContactBdm->getAllByGarageId($garage_id);
            $garage['Positions'] = $this->Position->search_list();
            $contactStaff = $this->GarageContactStaff->getContactStaffByGarageId($garage_id);
            $garage['PrimaryContact'] = !empty($contactStaff) ?
                $this->Contact->findById($contactStaff['GarageContactStaff']['contact_id']) : null;

            $garage['Province'] = $this->Province->findById($garage['Garage']['province_id']);
            $garage['Country'] = array();
            if (!empty($garage['Province'])) {
                $garage['Country'] = $this->Country->findById($garage['Province']['Province']['country_id']);
            }
            $garage['City'] = $this->City->findById($garage['Garage']['city_id']);
            $garage['Region'] = $this->AagRegion->findById($garage['Garage']['aag_region_id']);

            $garage['Distributors'] = $this->Distributor->getAllByGarageIdByOrder($garage_id);

            $garage['InternalNetworksDis'] = $this->GarageNetwork->findInternalByGarageAndAagRegionId($garage_id, $aagRegionId);
            $garage['ExternalNetworksDis'] = $this->GarageNetwork->findExternalByGarageAndAagRegionId($garage_id, $aagRegionId);
            $garage['InternalNetworks'] = $this->Network->findInternal($aagRegionId);
            $garage['ExternalNetworks'] = $this->Network->findExternal($aagRegionId);
            $garage['TradingGroups'] = $this->TradingGroup->getTradingGroupRegion($aagRegionId);
            $garage['NetworkStatus'] = Configure::read('Network_Status');

            $this->ApiRm = ClassRegistry::init("ApiRm.ApiRm");
            $internal_agreements_rm = $this->ApiRm->get_internal_agreements($garage_id);
            $internal_agreements_alliance = $this->GarageAgreement->get_internal_agreements($garage_id);
            $garage['InternalAgreements'] = $this->GarageAgreement->getInternalAgreementArray($internal_agreements_rm, $internal_agreements_alliance);
            $garage['ExternalAgreements'] = $this->GarageAgreement->findExternalByGarage($garage_id);

            $garage['CustomerActivities'] = $this->GarageCustomerActivity->getAllByGarageIdByOrder($garage_id);
            $garage['Activities'] = $this->CustomerActivity->search_list();
            $garage['GarageFacilities'] = $this->GarageFacility->get_list($garage_id);
            $garage['Facilities'] = $this->Facility->search_list();

            $garage['GarageValueSupplier'] = $this->GarageValueAddSupplier->getDataByGarage($garage_id);

            $garage['GarageServices'] = $this->GarageService->findServicesByGarage($garage_id);
            $garage['Services'] = $this->Service->search_list();
            $garage['VehicleSpecialist'] = $this->Vehicle->findVehiclesSpecialistExport($garage_id);
            $garage['GarageVehicleType'] = $this->GarageVehicleType->findVehicleTypesByGarage($garage_id);
            $garage['VehicleType'] = $this->VehicleType->search_list();

            $garage['Equipment'] = $this->GarageEquipment->get_export_garage_equipment($garage_id, $aagRegionId);
            $garage['Software'] = $this->GarageSoftware->findSoftwareExport($garage_id, $aagRegionId);
            $garage['Suppliers'] = $this->Supplier->search_list();
            $garage['Brands'] = $this->Brand->search_list();
            $garage['BillingSchedule'] = $this->BillingSchedule->search_list($aagRegionId);

            $garage['Orders'] = $this->GarageProduct->getAllProducts($garage_id);

            $garage['Employees'] = $this->GarageEmployee->findAllByGarageId($garage_id);
            $garage['EmployeeTypes'] = $this->EmployeeType->search_list();
            $garage['Staff'] = $this->GarageContactStaff->getAllByGarageId($garage_id);
            $garage['Position'] = $this->Position->search_list();

            $garage['GarageStatus'] = array(
                ConstantsGarageStatus::ACTIVE => __t('General.Active'),
                ConstantsGarageStatus::ONSTOP => __t('General.Inactive'),
                ConstantsGarageStatus::INACTIVE => __t('Garage.Inactive'),
            );

            $garage['Admin'] = $this->GarageComment->findByGarageIdAndOrder($garage_id);
            foreach ($garage['Admin'] as $staff) {
                $garage['UserNames'][$staff['GarageComment']['id']] = $this->User->getCompleteName($staff['GarageComment']['user_id']);
            }

            $garage['SalesArea'] = $this->SalesArea->findById($garage['Garage']['sales_area_id']);

            $this->set(array(
                'garage' => $garage
            ));
            $this->layout = null;
            $this->autoRender = false;

            $html = $this->render('/Garages/Elements/export_garage_pdf');

            $mpdf = new \Mpdf\Mpdf();
            $mpdf->SetFooter('{PAGENO}');
            $mpdf->WriteHTML($html);
            $mpdf->Output(__t('Garage.pdf'), 'I');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Counts garages
     * When exporting garages:
     * If number of garages is greater than the max allowed, export will be sent via email.
     */
    public function ajax_count_garages()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        if (
            $role_id == ConstantsRoles::SUPER_ADMIN ||
            (
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array($role_id, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ||
                        (
                            $role_id == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    )
                ) ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    !in_array($role_id, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
                )
            )
        ) {
            $this->layout = $this->autoRender = false;
            if (!$this->request->is('get')) {
                $searcher = $this->request->data;
                $this->request->data['Search'] = $searcher;

                $conditions = array();
                if (isset($aag_region_id)) {
                    $conditions['Garage.aag_region_id'] = $aag_region_id;
                }
                if (!empty($searcher['distributor_id'])) {
                    $conditions['Distributor.id IN'] = $searcher['distributor_id'];
                }
                if (isset($searcher['network_id']) && $searcher['network_id'] != null) {
                    $conditions['Network.id IN'] = $searcher['network_id'];
                }
                if (!empty($searcher['city_id'])) {
                    $containsZero = false;
                    foreach ($searcher['city_id'] as $value) {
                        if ($value == 0) {
                            $containsZero = true;
                            break;
                        }
                    }
                    if ($containsZero && $searcher['city_id']) {
                        $conditions[]['OR'] = array(
                            'Garage.city_id IN' => $searcher['city_id'],
                            'Garage.city_id IS NULL'
                        );
                    } elseif ($containsZero) {
                        $conditions[] = 'Garage.city_id IS NULL';
                    } else {
                        $conditions['Garage.city_id IN'] = $searcher['city_id'];
                    }
                }
                if (!empty($searcher['status_id'])) {
                    $conditions['GarageNetwork.status IN'] = $searcher['status_id'];
                }

                $conditions[] = $this->Garage->conditions($searcher);
                $conditions[] = $this->User->viewUserGarages($this->Acceso->user());

                $fields = array('Garage.id');
                $query_type = ConstantsQueryTypes::COUNT;
                $countGarages = $this->Garage->dynamicTypeGaragesExportQuery($query_type, $conditions, $fields);
                $countGarages = $countGarages ? $countGarages : 0;
            }
            return json_encode($countGarages);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX gets garages export data to be sent via email.
     */
    public function ajax_get_csv_data()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        if (
            $role_id == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                (
                    !in_array($role_id, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ||
                    (
                        $role_id == ConstantsRoles::DISTRIBUTOR &&
                        $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                    )
                )
            )
        ) {
            if (!$this->request->is('get')) {
                ini_set('memory_limit', '-1');
                set_time_limit(18000);

                $this->layout = $this->autoRender = false;

                $email = $this->request->data['contact-email'];

                // Here we stop the ajax conexion
                // We need to pass $user as a parameter

                header("Content-Length: 0");
                header('Connection: close');
                flush();
                session_write_close();
                if (is_callable('fastcgi_finish_request')) {
                    fastcgi_finish_request();
                }

                // wait 10s
                sleep(10);

                $searcher = $this->request->data;
                $this->request->data['Search'] = $searcher;

                $conditions = array();
                if (isset($aag_region_id)) {
                    $conditions['Garage.aag_region_id'] = $aag_region_id;
                }
                if (!empty($searcher['distributor_id'])) {
                    $conditions['Distributor.id IN'] = $searcher['distributor_id'];
                }
                if (isset($searcher['network_id']) && $searcher['network_id'] != null) {
                    $conditions['Network.id IN'] = $searcher['network_id'];
                }
                if (!empty($searcher['city_id'])) {
                    $containsZero = false;
                    foreach ($searcher['city_id'] as $value) {
                        if ($value == 0) {
                            $containsZero = true;
                            break;
                        }
                    }
                    if ($containsZero && $searcher['city_id']) {
                        $conditions[]['OR'] = array(
                            'Garage.city_id IN' => $searcher['city_id'],
                            'Garage.city_id IS NULL'
                        );
                    } elseif ($containsZero) {
                        $conditions[] = 'Garage.city_id IS NULL';
                    } else {
                        $conditions['Garage.city_id IN'] = $searcher['city_id'];
                    }
                }
                if (!empty($searcher['status_id'])) {
                    $conditions['GarageNetwork.status IN'] = $searcher['status_id'];
                }

                $status = Configure::read('Network_Status');
                $conditionStatus = '';
                foreach ($status as $statusKey => $statusValue) {
                    $conditionStatus .= "WHEN GarageNetwork.status = $statusKey THEN '" . __t($statusValue) . "' ";
                }
                $garages = $this->Garage->find(
                    'all',
                    array(
                        'conditions' => array(
                            $this->Garage->conditions($searcher),
                            $this->User->viewUserGarages($this->Acceso->user()),
                            $conditions
                        ),
                        'joins' => array(
                            array(
                                'alias' => 'GarageNetwork',
                                'table' => 'garages_networks',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'GarageNetwork.garage_id = Garage.id',
                                ),
                            ),
                            array(
                                'alias' => 'Network',
                                'table' => 'networks',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Network.id = GarageNetwork.network_id',
                                ),
                                'fields' => 'Network.name',
                            ),
                            array(
                                'alias' => 'AnnexDetail',
                                'table' => 'annex_details',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'AnnexDetail.id = GarageNetwork.annex_detail_id',
                                ),
                            ),
                            array(
                                'alias' => 'GarageDistributor',
                                'table' => 'garages_distributors',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'GarageDistributor.garage_id = Garage.id',
                                    'GarageDistributor.id = (
                                    SELECT MIN(gd1.id)
                                    FROM garages_distributors AS gd1
                                    WHERE gd1.garage_id = Garage.id
                                )',
                                ),
                            ),
                            array(
                                'alias' => 'Distributor',
                                'table' => 'distributors',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Distributor.id = GarageDistributor.distributor_id',
                                ),
                                'fields' => array(
                                    'Distributor.account_number',
                                    'Distributor.name',
                                    'Distributor.address1',
                                    'Distributor.town',
                                    'Distributor.postcode',
                                    'Distributor.association_type_id',
                                    'Distributor.MAMID'
                                ),
                            ),
                            array(
                                'alias' => 'AssociationType',
                                'table' => 'associations_types',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'AssociationType.id = Distributor.association_type_id',
                                ),
                                'fields' => 'AssociationType.name_' . __l()
                            ),
                            array(
                                'alias' => 'GarageContactsStaff',
                                'table' => 'garages_contacts_staff',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'GarageContactsStaff.garage_id = Garage.id',
                                    'GarageContactsStaff.priority' => 1,
                                ),
                            ),
                            array(
                                'alias' => 'Contact',
                                'table' => 'contacts',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Contact.id = GarageContactsStaff.contact_id',
                                ),
                                'fields' => array(
                                    'Contact.first_name',
                                    'Contact.last_name',
                                    'Contact.email',
                                ),
                            ),
                            array(
                                'alias' => 'TradingGroup',
                                'table' => 'trading_groups',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'TradingGroup.id = Distributor.trading_group_id',
                                ),
                                'fields' => 'TradingGroup.name'
                            ),
                            array(
                                'alias' => 'AagRegion',
                                'table' => 'aag_regions',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'AagRegion.id = Garage.aag_region_id',
                                ),
                                'fields' => 'AagRegion.name'
                            ),
                            array(
                                'alias' => 'Province',
                                'table' => 'provinces',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Garage.province_id = Province.id',
                                ),
                                'fields' => 'Province.name',
                            ),
                        ),
                        'fields' => array(
                            'Garage.id',
                            'Garage.g_number_id',
                            'Garage.name',
                            'Garage.town',
                            'Garage.address1',
                            'Garage.postcode',
                            'Garage.address2',
                            'Garage.address3',
                            'Garage.address4',
                            'Garage.sales_area_id',
                            'Garage.phone',
                            'Garage.email',
                            'Garage.ref_code',
                            'Garage.last_visit',
                            'Garage.slug',
                            'GROUP_CONCAT(DISTINCT CONCAT(Network.name, " - ", IFNULL(AnnexDetail.name_' . __l() . ', ""), "(",
                            CASE
                                ' . $conditionStatus . '
                                ELSE GarageNetwork.status
                            END
                            , ")") SEPARATOR ", ") AS network_concatenated_fields',
                            'GROUP_CONCAT(DISTINCT CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.contract_received_date, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS contract_received_date',
                            'GROUP_CONCAT(DISTINCT CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.contract_start_date, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS contract_start_date',
                            'GROUP_CONCAT(DISTINCT CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.date_on_hold, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS date_on_hold',
                            'GROUP_CONCAT(DISTINCT CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.leaving_date, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS leaving_date',
                            'Network.name',
                            'Distributor.account_number',
                            'Distributor.name',
                            'Distributor.address1',
                            'Distributor.town',
                            'Distributor.postcode',
                            'Distributor.association_type_id',
                            'Distributor.MAMID',
                            'Contact.first_name',
                            'Contact.last_name',
                            'Contact.email',
                            'TradingGroup.name',
                            'AagRegion.name',
                            'AssociationType.name_' . __l(),
                            'Province.name',
                        ),
                        'order' => array('Garage.name' => 'asc'),
                        'group' => array('Garage.id'),
                    )
                );
                $this->generateCsv($garages, $email, $user);
            }
            exit;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get CRM garages export data to be sent via email.
     */
    public function ajax_get_csv_clients_data()
    {
        $this->verify_ajax($this->request);

        ini_set('memory_limit', '-1');
        set_time_limit(18000);
        $this->layout = $this->autoRender = false;
        $email = $this->request->data['contact-email'];
        if (!$this->request->is('get')) {

            // Here we stop the ajax conexion
            // We need to pass $user as a parameter
            $user = $this->Acceso->user();

            header("Content-Length: 0");
            header('Connection: close');
            flush();
            session_write_close();
            if (is_callable('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }

            // wait 10s
            sleep(10);

            $user_role_id = $user['role_id'];
            $aag_region_id = $user['aag_region_id'];

            $searcher = $this->request->data;
            $this->request->data['Search'] = $searcher;

            $conditions = array();
            $conditions = $this->Garage->conditions($searcher);
            $conditions[] = $this->User->viewUserGarages($user);
            if ($user_role_id != ConstantsRoles::SUPER_ADMIN) {
                $conditions[] = array('Garage.aag_region_id' => $aag_region_id);
            }

            $status = Configure::read('Network_Status');
            $conditionStatus = '';
            foreach ($status as $statusKey => $statusValue) {
                $conditionStatus .= "WHEN GarageNetwork.status = $statusKey THEN '" . __t($statusValue) . "' ";
            }
            $garages = $this->Garage->find(
                'all',
                array(
                    'conditions' => array(
                        $conditions
                    ),
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.garage_id = Garage.id',
                            ),
                        ),
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Network.id = GarageNetwork.network_id',
                            ),
                            'fields' => 'Network.name',
                        ),
                        array(
                            'alias' => 'AnnexDetail',
                            'table' => 'annex_details',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'AnnexDetail.id = GarageNetwork.annex_detail_id',
                            ),
                        ),
                        array(
                            'alias' => 'GarageDistributor',
                            'table' => 'garages_distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageDistributor.garage_id = Garage.id',
                                'GarageDistributor.id = (
                                    SELECT MIN(gd1.id)
                                    FROM garages_distributors AS gd1
                                    WHERE gd1.garage_id = Garage.id
                                )',
                            ),
                        ),
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = GarageDistributor.distributor_id',
                            ),
                            'fields' => array(
                                'Distributor.account_number',
                                'Distributor.name',
                                'Distributor.address1',
                                'Distributor.town',
                                'Distributor.postcode',
                                'Distributor.association_type_id',
                                'Distributor.MAMID',
                            ),
                        ),
                        array(
                            'alias' => 'AssociationType',
                            'table' => 'associations_types',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'AssociationType.id = Distributor.association_type_id',
                            ),
                            'fields' => 'AssociationType.name_' . __l()
                        ),
                        array(
                            'alias' => 'GarageContactsStaff',
                            'table' => 'garages_contacts_staff',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageContactsStaff.garage_id = Garage.id',
                                'GarageContactsStaff.priority' => 1,
                            ),
                        ),
                        array(
                            'alias' => 'Contact',
                            'table' => 'contacts',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Contact.id = GarageContactsStaff.contact_id',
                            ),
                            'fields' => array(
                                'Contact.first_name',
                                'Contact.last_name',
                                'Contact.email',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                            'fields' => 'TradingGroup.name'
                        ),
                        array(
                            'alias' => 'AagRegion',
                            'table' => 'aag_regions',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'AagRegion.id = Garage.aag_region_id',
                            ),
                            'fields' => 'AagRegion.name'
                        ),
                        array(
                            'alias' => 'Province',
                            'table' => 'provinces',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.province_id = Province.id',
                            ),
                            'fields' => 'Province.name',
                        ),
                    ),
                    'fields' => array(
                        'Garage.id',
                        'Garage.g_number_id',
                        'Garage.name',
                        'Garage.town',
                        'Garage.address1',
                        'Garage.postcode',
                        'Garage.address2',
                        'Garage.address3',
                        'Garage.address4',
                        'Garage.phone',
                        'Garage.email',
                        'Garage.ref_code',
                        'Garage.last_visit',
                        'Garage.slug',
                        'GROUP_CONCAT(DISTINCT CONCAT(Network.name, " - ", IFNULL(AnnexDetail.name_' . __l() . ', ""), "(",
                            CASE
                                ' . $conditionStatus . '
                                ELSE GarageNetwork.status
                            END
                            , ")") SEPARATOR ", ") AS network_concatenated_fields',
                        'GROUP_CONCAT(DISTINCT CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.contract_received_date, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS contract_received_date',
                        'GROUP_CONCAT(DISTINCT CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.contract_start_date, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS contract_start_date',
                        'GROUP_CONCAT(DISTINCT CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.date_on_hold, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS date_on_hold',
                        'GROUP_CONCAT(DISTINCT CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.leaving_date, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS leaving_date',
                        'Network.name',
                        'Distributor.account_number',
                        'Distributor.name',
                        'Distributor.MAMID',
                        'Distributor.address1',
                        'Distributor.town',
                        'Distributor.postcode',
                        'Distributor.association_type_id',
                        'Contact.first_name',
                        'Contact.last_name',
                        'Contact.email',
                        'TradingGroup.name',
                        'AagRegion.name',
                        'AssociationType.name_' . __l(),
                        'Province.name',
                    ),
                    'order' => array('Garage.name' => 'asc'),
                    'group' => array('Garage.id'),
                )
            );

            $this->generateCsvClients($garages, $email, $user);
        }
        exit;
    }


    private function generateCsv($data, $email, $user)
    {
        $userAagRegionId = $user['aag_region_id'];
        $languageCode = $user['language_code'];
        $config = $user['Config'];
        $association_list = $this->AssociationType->search_list($userAagRegionId);
        $salesArea = $this->SalesArea->searchListByRegion($userAagRegionId);

        $date = date('Y-m-d');

        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $filename = 'Garages' . Fecha::getCompleteDate() . '.csv';
        $fileFullName = ConstantsPath::DIR_CSV_EXPORT_FILES_ABSOLUTE . DS . $filename;
        $controller = $this->params['controller'];
        $file = fopen($fileFullName, 'a');

        $table = array();

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $table_tmp = array(
                __t('Garage.G_number', $languageCode),
            );
            $table = array_merge($table, $table_tmp);
        }

        $table_tmp = array(
            __t('Training.Network_name', $languageCode),
            __t('Appointment.Customer', $languageCode),
            __t('Garage.Slug', $languageCode),
        );
        $table = array_merge($table, $table_tmp);

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $table_tmp = array(
                __t('Garage.BDM', $languageCode),
            );
            $table = array_merge($table, $table_tmp);
        }

        if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
            $table_tmp = array(
                __t('Garage.County', $languageCode),
            );
            $table = array_merge($table, $table_tmp);
        }

        $table_tmp = array(
            __t('Garage.Town', $languageCode),
            __t('Garage.Address', $languageCode),
            __t('Garage.Postcode', $languageCode),
            __t('Garage.Address_2', $languageCode),
            __t('Garage.Address_3', $languageCode),
            __t('Garage.Address_4', $languageCode),
            __t('Garage.Sales_area', $languageCode),
            __t('Garage.Phone', $languageCode),
            __t('Garage.Email_2', $languageCode),
        );
        $table = array_merge($table, $table_tmp);

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $table_tmp = array(
                __t('Garage.Ref_code', $languageCode),
            );
            $table = array_merge($table, $table_tmp);
        }

        $table_tmp = array(
            __t('Garage.Last_visit', $languageCode),
            __t('Garage.Primary_contact', $languageCode),
            __t('Garage.Customer_email', $languageCode),
            __t('Garage.Member_number', $languageCode),
            __t('Garage.Member_name', $languageCode),
        );
        $table = array_merge($table, $table_tmp);

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $table_tmp = array(
                __t('Distributor.MAMID', $languageCode)
            );
            $table = array_merge($table, $table_tmp);
        }

        $table_tmp = array(
            __t('Garage.Member_address', $languageCode),
            __t('Garage.Member_town', $languageCode),
            __t('Garage.Member_postcode', $languageCode),
            __t('Garage.Trading_group', $languageCode),
            __t('Distributor.Association', $languageCode),
            __t('Garage.Received_date', $languageCode),
            __t('Equipment.Start_date', $languageCode),
            __t('Network.On_hold_date', $languageCode),
            __t('Activity.Left_date', $languageCode),
            __t('Garage.Region', $languageCode),
            __t('Software.Technical_helpline', $languageCode),
            __t('Software.Mam', $languageCode),
            __t('Distributor.Painting', $languageCode),
            __t('Garage.Signage', $languageCode)
        );
        $table = array_merge($table, $table_tmp);

        fputcsv($file, $table);
        foreach ($data as $garage) {
            if ($garage['Garage']['last_visit']) {
                $interval = strtotime($date) - strtotime($garage['Garage']['last_visit']);
                $garage['Garage']['LatestVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
            } else {
                $garage['Garage']['LatestVisit'] = '--';
            }

            $garage_row = array();

            if ($userAagRegionId == ConstantsAAGRegionId::UK) {
                $garage_row[] = $garage['Garage']['g_number_id'];
            }

            $garage_row[] = $garage[0]['network_concatenated_fields'];
            $garage_row[] = $garage['Garage']['name'];
            $garage_row[] = !empty(trim(($garage['Garage']['slug']))) ? $garage['Garage']['slug'] : '--';

            if ($userAagRegionId == ConstantsAAGRegionId::UK) {
                $garage['BDMS'] = $this->GarageContactBdm->getBDMByGarage($garage['Garage']['id']);
                if (!empty($garage['BDMS'])) {
                    $bdms_text = array();
                    foreach ($garage['BDMS'] as $bdm) {
                        $bdms_text[] = $bdm[0]['full_name'];
                    }
                    $garage_row[] = implode("\n", $bdms_text);
                } else {
                    $garage_row[] = '';
                }
            }

            if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
                $garage_row[] = $garage['Province']['name'];
            }

            $garage_row[] = $garage['Garage']['town'];
            $garage_row[] = $garage['Garage']['address1'];
            $garage_row[] = $garage['Garage']['postcode'];
            $garage_row[] = $garage['Garage']['address2'];
            $garage_row[] = $garage['Garage']['address3'];
            $garage_row[] = $garage['Garage']['address4'];
            $garage_row[] = isset($salesArea[$garage['Garage']['sales_area_id']]) ? $salesArea[$garage['Garage']['sales_area_id']] : "";
            $garage_row[] = $garage['Garage']['phone'];
            $garage_row[] = $garage['Garage']['email'];

            if ($userAagRegionId == ConstantsAAGRegionId::UK) {
                $garage_row[] = $garage['Garage']['ref_code'];
            }

            $garage_row[] = isset($garage['Garage']['last_visit']) ? $garage['Garage']['last_visit'] : '';
            $garage_row[] = $garage['Contact']['first_name'] . ' ' . $garage['Contact']['last_name'];
            $garage_row[] = $garage['Contact']['email'];
            $garage_row[] = $garage['Distributor']['account_number'];
            $garage_row[] = $garage['Distributor']['name'];

            if ($userAagRegionId == ConstantsAAGRegionId::UK) {
                $garage_row[] = $garage['Distributor']['MAMID'];
            }

            $garage_row[] = $garage['Distributor']['address1'];
            $garage_row[] = $garage['Distributor']['town'];
            $garage_row[] = $garage['Distributor']['postcode'];
            $garage_row[] = $garage['TradingGroup']['name'];
            $garage_row[] = (isset($garage['Distributor']['association_type_id']) &&
                (isset($association_list) && !empty($association_list)))
                ? $association_list[$garage['Distributor']['association_type_id']] : '';
            $garage_row[] = !is_null($garage[0]['contract_received_date']) ? $garage[0]['contract_received_date'] : '--';
            $garage_row[] = !is_null($garage[0]['contract_start_date']) ? $garage[0]['contract_start_date'] : '--';
            $garage_row[] = !is_null($garage[0]['date_on_hold']) ? $garage[0]['date_on_hold'] : '--';
            $garage_row[] = !is_null($garage[0]['leaving_date']) ? $garage[0]['leaving_date'] : '--';
            $garage_row[] = $garage['AagRegion']['name'];

            $garage['MAM_7'] = $this->GarageSoftware->findByGarageIdAndSoftwareId($garage['Garage']['id'], 7);
            $garage['TECHNICAL_HELPLINE_6'] = $this->GarageSoftware->findByGarageIdAndSoftwareId($garage['Garage']['id'], 6);
            $garage['PAINTING_1'] = $this->Order->findByGarageIdAndOrderTypeId($garage['Garage']['id'], 1);
            $garage['SIGNAGE_2'] = $this->Order->findByGarageIdAndOrderTypeId($garage['Garage']['id'], 2);

            $garage_row[] = ($garage['TECHNICAL_HELPLINE_6']) ? __t('General.Yes') : __t('General.No');
            $garage_row[] = ($garage['MAM_7']) ? __t('General.Yes') : __t('General.No');
            $garage_row[] = ($garage['PAINTING_1']) ? __t('General.Yes') : __t('General.No');
            $garage_row[] = ($garage['SIGNAGE_2']) ? __t('General.Yes') : __t('General.No');

            fputcsv($file, $garage_row);
        }
        fclose($file);

        $this->generateEmail($filename, $fileFullName, $user, $email, $controller);
    }

    private function generateCsvClients($data, $email, $user)
    {
        $userAagRegionId = $user['aag_region_id'];
        $languageCode = $user['language_code'];
        $config = $user['Config'];
        $association_list = $this->AssociationType->search_list($userAagRegionId);

        $date = date('Y-m-d');

        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $filename = 'Garages' . Fecha::getCompleteDate() . '.csv';
        $fileFullName = ConstantsPath::DIR_CSV_EXPORT_FILES_ABSOLUTE . DS . $filename;
        $controller = $this->params['controller'];
        $file = fopen($fileFullName, 'a');
        $table = array();

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $table[] = __t('Garage.G_number', $languageCode);
        }
        $table[] = __t('Training.Network_name', $languageCode);
        $table[] = __t('Appointment.Customer', $languageCode);
        $table[] = __t('Garage.BDM', $languageCode);
        if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
            $table[] = __t('Garage.County', $languageCode);
        }
        $table[] = __t('Garage.Town', $languageCode);
        $table[] = __t('Garage.Address', $languageCode);
        $table[] = __t('Garage.Postcode', $languageCode);
        $table[] = __t('Garage.Address_2', $languageCode);
        $table[] = __t('Garage.Address_3', $languageCode);
        $table[] = __t('Garage.Address_4', $languageCode);
        $table[] = __t('Garage.Phone', $languageCode);
        $table[] = __t('Garage.Email_2', $languageCode);
        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $table[] = __t('Garage.Ref_code', $languageCode);
        }

        $table[] = __t('Garage.Last_visit', $languageCode);
        $table[] = __t('Garage.Primary_contact', $languageCode);
        $table[] = __t('Garage.Member_number', $languageCode);
        $table[] = __t('Garage.Member_name', $languageCode);
        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $table[] = __t('Distributor.MAMID', $languageCode);
        }
        $table[] = __t('Garage.Member_address', $languageCode);
        $table[] = __t('Garage.Member_postcode', $languageCode);
        $table[] = __t('Garage.Trading_group', $languageCode);
        $table[] = __t('Distributor.Association', $languageCode);
        $table[] = __t('Garage.Received_date', $languageCode);
        $table[] = __t('Equipment.Start_date', $languageCode);
        $table[] = __t('Network.On_hold_date', $languageCode);
        $table[] = __t('Activity.Left_date', $languageCode);
        $table[] = __t('CRM.Remaining_days', $languageCode);
        $table[] = __t('Software.Technical_helpline', $languageCode);
        $table[] = __t('Software.Mam', $languageCode);
        $table[] = __t('Distributor.Painting', $languageCode);
        $table[] = __t('Garage.Signage', $languageCode);

        fputcsv($file, $table);

        foreach ($data as $garage) {
            $garage_row = array();
            if ($garage['Garage']['last_visit']) {
                $interval = strtotime($date) - strtotime($garage['Garage']['last_visit']);
                $garage['Garage']['LatestVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
            } else {
                $garage['Garage']['LatestVisit'] = '--';
            }

            if ($userAagRegionId == ConstantsAAGRegionId::UK) {
                $garage_row[] = $garage['Garage']['g_number_id'];
            }

            $garage_row[] = $garage[0]['network_concatenated_fields'];
            $garage_row[] = $garage['Garage']['name'];

            if ($userAagRegionId == ConstantsAAGRegionId::UK) {
                $garage['BDMS'] = $this->GarageContactBdm->getBDMByGarage($garage['Garage']['id']);
                if (!empty($garage['BDMS'])) {
                    $bdms_text = array();
                    foreach ($garage['BDMS'] as $bdm) {
                        $bdms_text[] = $bdm[0]['full_name'];
                    }
                    $garage_row[] = implode("\n", $bdms_text);
                } else {
                    $garage_row[] = '';
                }
            }

            if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
                $garage_row[] = $garage['Province']['name'];
            }

            $garage_row[] = $garage['Garage']['town'];
            $garage_row[] = $garage['Garage']['address1'];
            $garage_row[] = $garage['Garage']['postcode'];
            $garage_row[] = $garage['Garage']['address2'];
            $garage_row[] = $garage['Garage']['address3'];
            $garage_row[] = $garage['Garage']['address4'];
            $garage_row[] = $garage['Garage']['phone'];
            $garage_row[] = $garage['Garage']['email'];

            if ($userAagRegionId == ConstantsAAGRegionId::UK) {
                $garage_row[] = $garage['Garage']['ref_code'];
            }

            $garage_row[] = $garage['Garage']['last_visit'];
            $garage_row[] = $garage['Contact']['first_name'] . ' ' . $garage['Contact']['last_name'];
            $garage_row[] = $garage['Distributor']['account_number'];
            $garage_row[] = $garage['Distributor']['name'];
            if ($userAagRegionId == ConstantsAAGRegionId::UK) {
                $garage_row[] = $garage['Distributor']['MAMID'];
            }
            $garage_row[] = $garage['Distributor']['address1'];
            $garage_row[] = $garage['Distributor']['postcode'];
            $garage_row[] = $garage['TradingGroup']['name'];
            $garage_row[] = (isset($garage['Distributor']['association_type_id']) &&
                (isset($association_list) && !empty($association_list)))
                ? $association_list[$garage['Distributor']['association_type_id']] : '';
            $garage_row[] = !is_null($garage[0]['contract_received_date']) ? $garage[0]['contract_received_date'] : '--';
            $garage_row[] = !is_null($garage[0]['contract_start_date']) ? $garage[0]['contract_start_date'] : '--';
            $garage_row[] = !is_null($garage[0]['date_on_hold']) ? $garage[0]['date_on_hold'] : '--';
            $garage_row[] = !is_null($garage[0]['leaving_date']) ? $garage[0]['leaving_date'] : '--';
            $garage_row[] = $garage['Garage']['LatestVisit'] . ' days';

            $garage['MAM_7'] = $this->GarageSoftware->findByGarageIdAndSoftwareId($garage['Garage']['id'], 7);
            $garage['TECHNICAL_HELPLINE_6'] = $this->GarageSoftware->findByGarageIdAndSoftwareId($garage['Garage']['id'], 6);
            $garage['PAINTING_1'] = $this->Order->findByGarageIdAndOrderTypeId($garage['Garage']['id'], 1);
            $garage['SIGNAGE_2'] = $this->Order->findByGarageIdAndOrderTypeId($garage['Garage']['id'], 2);

            $garage_row[] = ($garage['TECHNICAL_HELPLINE_6']) ? __t('General.Yes') : __t('General.No');
            $garage_row[] = ($garage['MAM_7']) ? __t('General.Yes') : __t('General.No');
            $garage_row[] = ($garage['PAINTING_1']) ? __t('General.Yes') : __t('General.No');
            $garage_row[] = ($garage['SIGNAGE_2']) ? __t('General.Yes') : __t('General.No');

            fputcsv($file, $garage_row);
        }
        fclose($file);

        $this->generateEmail($filename, $fileFullName, $user, $email, $controller);
    }

    /**
     * AJAX get Garages names.
     * Used for dynamic selects.
     */
    public function get_garages_name_only_live()
    {
        $this->verify_ajax($this->request);
        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        $role_id = $user['role_id'];

        $garages_name = $this->Garage->getPossiblesGaragesOnlyLiveAjax($this->request->query, $aag_region_id);

        $list_garages = array();
        foreach ($garages_name as $key => $garage) {
            $list_garages[] = array(
                'id' => $key,
                'text' => $garage,
            );
        }

        $list_garages_complete['items'] = $list_garages;

        return json_encode($list_garages_complete);
    }
}
