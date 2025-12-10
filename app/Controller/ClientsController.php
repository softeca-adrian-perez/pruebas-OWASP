<?php
class ClientsController extends AppController
{
    public $uses = array(
        'Appointment',
        'AppointmentStatus',
        'AppointmentFeeling',
        'AppointmentComment',
        'AppointmentType',
        'Association',
        'Garage',
        'City',
        'Contact',
        'Country',
        'Distributor',
        'DistributorContactBdm',
        'DistributorContactStaff',
        'DistributorContactGeneralBranchManager',
        'DistributorComment',
        'DistributorActivity',
        'DistributorDistributorActivity',
        'DistributorActivityPrimary',
        'DistributorSoftware',
        'DistributorFigure',
        'DistributorFigureDetail',
        'DistributorKPI',
        'DistributorLabel',
        'DistributorNetwork',
        'DistributorType',
        'GarageAgreement',
        'GarageContactBdm',
        'GarageContactStaff',
        'GarageContactGeneralBranchManager',
        'GarageDistributor',
        'GarageFigure',
        'GarageFigureDetail',
        'GarageImage',
        'GarageKPI',
        'GarageNetwork',
        'GarageComment',
        'GarageSale',
        'GarageService',
        'GarageSoftware',
        'GarageSpecialistMake',
        'GarageVehicle',
        'GarageVehicleType',
        'GarageVisitFrequency',
        'GarageWebsite',
        'LabelType',
        'LogChange',
        'Network',
        'NetworkContractType',
        'Position',
        'Province',
        'Region',
        'SaleFamily',
        'Service',
        'Software',
        'TaskGarage',
        'TaskDistributor',
        'TaskStatus',
        'TradingGroup',
        'User',
        'Vehicle',
        'VehicleType',
        'Website',
        'Alert',
        'SoftwareType',
        'Supplier',
        'SoftwareManufacture',
        'GarageEquipment',
        'Brand',
        'Equipment',
        'EquipmentType',
        'GarageBrand',
        'Brand',
        'InsuranceAgreement',
        'EmployeeType',
        'GarageEmployee',
        'GarageCustomerActivity',
        'CustomerActivity',
        'Supplier',
        'SoftwareManufacture',
        'Software',
        'CustomerActivity',
        'WorkshopActivity',
        'DistributorCustomerActivity',
        'DistributorCustomerActivityWorkshop',
        'DistributorService',
        'ServiceType',
        'GarageCustomerActivity',
        'GarageWorkshopActivity',
        'DistributorImage',
        'Distributor',
        'DistributorType',
        'DistributorDistributorNetwork',
        'AssociationType',
        'DistributorNetworkContactBdm',
        'NetworkContactBdm',
        'Permission',
        'AppointmentObjective',
        'AppointmentObjectiveComment',
        'AagRegion',
        'Erp',
        'TrainingAllowance',
		'SalesArea'
    );

    /**
     * Clients garages list page.
     */
    public function home()
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::GPC_LOGISTICS_RM))
            )
        ) {
            $url = Router::url(array(
                'controller' => 'clients',
                'action' => 'home'
            ));
            CakeSession::write('Auth.User.appointment_url', $url); // T001 SECURITY - It is not changed

            $search = $this->request->query;
            $search = self::multipleFieldsArrayCheck($search);
            $this->request->data['Search'] = $search;
            $conditions = $this->Garage->conditions($search);
            $conditions[] = $this->User->viewUserGarages($user);

            $conditions[] = array('Garage.aag_region_id' => $aagRegionId);

            if (!empty($search['search_my_customers'])) {
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

            $vehiclesTypes = $this->VehicleType->search_list();
            $leadSources = Configure::read('lead_source');
            foreach ($leadSources as $key => $leadSource) {
                $leadSources[$key] = __t($leadSource);
            }
            $provinceList = $this->Garage->Province->find('list');

            $garages = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Garage',
                null,
                'PaginatorOrderCustom'
            );

            $bdm = array();
            if (isset($searcher['bdm_id']) && !empty($searcher['bdm_id'])) {
                foreach ($search['bdm_id'] as $bdmId) {
                    $bdmContact = $this->Contact->findById($bdmId);
                    $bdm[$bdmId] = $bdmContact['Contact']['first_name'] . ' ' . $bdmContact['Contact']['last_name'];
                }
            }

            foreach ($garages as $key => $garage) {
                $garageTmp = $this->Garage->findById($garage['Garage']['id']);
                $garages[$key]['Distributors'] = $this->GarageDistributor->findAllByGarageId($garage['Garage']['id']);
                $garages[$key]['Garage']['status'] = $garageTmp['Garage']['status'];
                $garages[$key]['FiguresDetail'] = $this->GarageFigureDetail->findByCustomerNo($garage['Garage']['g_number_id']);
                $garages[$key]['BDMS'] = $this->GarageContactBdm->getBDMByGarage($garage['Garage']['id']);

                if ($garage['Garage']['last_visit']) {
                    $interval = strtotime(date('Y-m-d')) - strtotime($garage['Garage']['last_visit']);
                    $garages[$key]['LatestVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
                } else {
                    $garages[$key]['LatestVisit'] = '--';
                }
            }

            $customerTypes = Configure::read('Customer_Types');
            $networks = $this->Network->getNetworksConditions($aagRegionId);
            $tradingGroups = $this->TradingGroup->getTradingGroupGarageConditions($aagRegionId);
            $networksStatusesAll = Configure::read('Network_Status');
            foreach ($networksStatusesAll as $key => $networkStatus) {
                $networksStatusesAll[$key] = __t($networkStatus);
            }
            $networksStatusesTmp = $this->GarageNetwork->getStatusFromGarageNetworks();

            $networksStatuses = array();
            foreach ($networksStatusesTmp as $network_status_tmp) {
                $networksStatuses[$network_status_tmp] = $networksStatusesAll[$network_status_tmp];
            }
            $services = $this->Service->search_list();
            $distributors = array();
            if (!empty($search['distributor_id'])) {
                $distributors = $this->Distributor->getDistributorsCompleteNamesByListOfIds($search['distributor_id'], $aagRegionId);
            }

            $arrayCityName = array();
            if (!empty($search['city_id'])) {
                $arrayCityName = $this->City->getCityCompleteNamesByListOfIds($search['city_id']);
            }

            $regions = $this->Region->region_list();
            $this->set(array(
                'garages' => $garages,
                'customer_types' => $customerTypes,
                'networks' => $networks,
                'trading_groups' => $tradingGroups,
                'networks_statuses' => $networksStatuses,
                'array_city_name' => json_encode($arrayCityName),
                'services' => $services,
                'distributors' => json_encode($distributors),
                'regions' => $regions,
                'vehicles_types' => $vehiclesTypes,
                'lead_sources' => $leadSources,
                'distributors_list' => array(),
                'bdm' => $bdm,
                'province_list' => $provinceList,
                'user_aag_region_id' => $aagRegionId,
                'user_role' => $roleId,
                'erp_providers' => $this->Erp->getErpsByAagRegionId($aagRegionId),
				'sales_area' => $this->SalesArea->searchListByRegion($aagRegionId),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Clients distributors list page.
     */
    public function home_distributors()
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $url = Router::url(array(
                'controller' => 'appointments',
                'action' => 'home_distributors'
            ));
            CakeSession::write('Auth.User.appointment_url', $url); // T001 SECURITY - It is not changed

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $profile = array(
                '0' => __t('Distributor.Branch'),
                '1' => __t('Distributor.Head_office')
            );

            $type = array(
                '0' => __t('Distributor.Independent'),
                '1' => __t('Distributor.Subsidiary')
            );
            asort($type);

            $associations = $this->Association->find(
                'list',
                array(
                    'order' => 'name'
                )
            );

            $distributorsStatuses = Configure::read('Distributor_Status');
            foreach ($distributorsStatuses as $key => $distributorStatus) {
                $distributorsStatuses[$key] = __t($distributorStatus);
            }

            $tradingGroups = $this->TradingGroup->getTradingGroupRegion($aagRegionId);
            $conditions = $this->Distributor->conditions($searcher);
            $conditions[] = $this->User->viewUserDistributors($this->Acceso->user());
            $conditions[] = array('Distributor.aag_region_id' => $aagRegionId);

            if (!empty($searcher['search_my_customers'])) {
                $tmp = $this->Distributor->_query('clients');
                $tmp['joins'] = array(
                    array(
                        'table' => 'distributors_contacts_bdm',
                        'alias' => 'DistributorContactBdm',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorContactBdm.distributor_id = Distributor.id'
                        )
                    ),
                );
            } else {
                $tmp = $this->Distributor->_query('clients');
            }

            $distributors = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Distributor',
                null,
                'PaginatorOrderCustom'
            );
            $paginationCount = $this->Distributor->getPaginationCount($conditions, $searcher);

            $bdm = array();
            if (!empty($searcher['bdm_id'])) {
                $bdmContact = $this->Contact->findById($searcher['bdm_id']);
                $bdm[$searcher['bdm_id']] = $bdmContact['Contact']['first_name'] . ' ' . $bdmContact['Contact']['last_name'];
            }

            foreach ($distributors as $key => $distributor) {
                $distributors[$key]['BDMS'] = $this->DistributorContactBdm->getBDMByDistributor($distributor['Distributor']['id']);
                if ($distributor['Distributor']['last_visit']) {
                    $interval = strtotime(date('Y-m-d')) - strtotime($distributor['Distributor']['last_visit']);
                    $distributors[$key]['LatestVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
                } else {
                    $distributors[$key]['LatestVisit'] = '--';
                }
            }

            $networks = $this->Network->find('list');
            $regions = $this->Region->region_list();
            $distributorTypes = $this->DistributorType->search_list();
            $associationsTypes = $this->AssociationType->search_list($aagRegionId);
            $distributorPermissions = $this->Permission->getByGroupingPermission(ConstantsGroupingPermissions::DISTRIBUTOR);

            $this->set(array(
                'trading_groups' => $tradingGroups,
                'networks' => $networks,
                'distributors' => $distributors,
                'associations' => $associations,
                'profile' => $profile,
                'type' => $type,
                'bdm' => $bdm,
                'pagination_count' => $paginationCount,
                'regions' => $regions,
                'distributor_types' => $distributorTypes,
                'associations_types' => $associationsTypes,
                'distributors_permissions' => $distributorPermissions,
                'distributors_statuses' => $distributorsStatuses,
				'sales_area' => $this->SalesArea->searchListByRegion($aagRegionId),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * CRM garaes report.
     */
    public function report($garage_id)
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::GPC_LOGISTICS_RM))
            )
        ) {
            $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);
            if (!$garage) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_GARAGE));
                $this->redirect(
                    array(
                        'controller' => 'dashboard',
                        'action' => 'home',
                    )
                );
            }

            $this->ApiRm = ClassRegistry::init("ApiRm.ApiRm");
            $internal_agreements_rm = $this->ApiRm->get_internal_agreements($garage_id);
            $internal_agreements_alliance = $this->GarageAgreement->get_internal_agreements($garage_id);
            $garage_internal_agreements = $this->GarageAgreement->getInternalAgreementArray($internal_agreements_rm, $internal_agreements_alliance);
            $garage_external_agreements = $this->GarageAgreement->findExternalByGarage($garage_id);

            $leadSources = Configure::read('lead_source');
            foreach ($leadSources as $key => $leadSource) {
                $leadSources[$key] = __t($leadSource);
            }

            $provinceList = $this->Garage->Province->find('list');
            $garage_services = $this->GarageService->findServices($garage_id);
            $garage_vehicle_types = $this->GarageVehicleType->findVehicleTypes($garage_id);
            $garage_vehicles = $this->GarageVehicle->findVehicles($garage_id);
            $garage_vehicle_specialists = $this->GarageSpecialistMake->findVehicles($garage_id);
            $garage_principal_image = $this->Garage->getPrincipalImageDatas($garage_id);
            $software = $this->Software->search_list($garage['Garage']['aag_region_id']);
            $garage_software = $this->GarageSoftware->findAllByGarageId($garage_id);
            $websites = $this->Website->search_list($garage['Garage']['aag_region_id']);
            $garage_websites = $this->GarageWebsite->findAllByGarageId($garage_id);
            $garage_distributors = $this->GarageDistributor->getAllByGarageId($garage_id);
            $networks = $this->Network->find('all');
            $tradingGroups = $this->TradingGroup->find('all');
            $networksStatuses = Configure::read('Network_Status');
            foreach ($networksStatuses as $key => $networkStatus) {
                $networksStatuses[$key] = __t($networkStatus);
            }
            $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
            $garage_internal_networks = $this->GarageNetwork->findInternalByGarage($garage_id);
            $garage_external_networks = $this->GarageNetwork->findExternalByGarage($garage_id);
            $images = $this->GarageImage->findAllByGarageId($garage_id);
            $garage_statuses = Configure::read('Garage_Status');
            foreach ($garage_statuses as $key => $networkStatus) {
                $garage_statuses[$key] = __t($networkStatus);
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
            $regions = $this->Region->region_list();
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
            $workshop_activities_garage = $this->GarageWorkshopActivity->getWorkshopActivitiesByGarageId($garage['Garage']['id']);
            $workshop_activities_list = $this->WorkshopActivity->search_list();
            $agreements = $this->GarageAgreement->findAllByGarageId($garage_id);
            $province = $this->Province->findById($garage['Garage']['province_id']);
            $city = $this->City->findById($garage['Garage']['city_id']);
            $country = array();

            if (!empty($province)) {
                $country = $this->Country->findById($province['Province']['country_id']);
            } elseif (!empty($city)) {
                $country = $this->Country->get_country_by_province($city['City']['province_id']);
            }

            $user = $this->Acceso->user();
            $contact_id = $user['contact_id'];

            $searcher['Garage_name'] = $garage_id;

            $garages_networks = $this->custom_pagination(
                $this->Garage->_queryTrainingGarage('home_training', $garage_id),
                $this->Garage->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Garage'
            );

            $sums_given_list = $this->TrainingAllowance->getAllGivenSumsList();
            $sums_spent_list = $this->TrainingAllowance->getAllSpentSumsList();

            $this->set(array(
                'garage' => $garage,
                'garage_services' => $garage_services,
                'garage_vehicles' => $garage_vehicles,
                'garage_vehicle_specialists' => $garage_vehicle_specialists,
                'garage_vehicle_types' => $garage_vehicle_types,
                'garage_software' => $garage_software,
                'garage_websites' => $garage_websites,
                'agreements' => $agreements,
                'visit_frequency' => $visit_frequency,
                'websites' => $websites,
                'software' => $software,
                'province_list' => $provinceList,
                'lead_sources' => $leadSources,
                'garage_distributors' => $garage_distributors,
                'networks' => $networks,
                'trading_groups' => $tradingGroups,
                'networks_statuses' => $networksStatuses,
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
                'workshop_activities_garage' => $workshop_activities_garage,
                'workshop_activities_list' => $workshop_activities_list,
                'garage_internal_agreements' => $garage_internal_agreements,
                'garage_external_agreements' => $garage_external_agreements,
                'user_aag_region_id' => $aagRegionId,
                'user_role' => $roleId,
                'country' => $country,
                'contact_id' => $contact_id,
                'garages_networks' => $garages_networks,
                'sums_given_list' => $sums_given_list,
                'sums_spent_list' => $sums_spent_list,
				'sales_area' => $this->SalesArea->searchListByRegion($garage['Garage']['aag_region_id'])
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * CRM distributors report.
     */
    public function report_distributor($distributor_id)
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);
            if (!$distributor) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_DISTRIBUTOR));
                $this->redirect(
                    array(
                        'controller' => 'dashboard',
                        'action' => 'home',
                    )
                );
            }

            $tradingGroups = $this->Distributor->TradingGroup->getIndependent($aagRegionId);
            $distributorTradingGroup = $this->TradingGroup->findById($distributor['Distributor']['trading_group_id']);
            $distributorParentAccount = null;
            if (!empty($distributor['Distributor']['distributor_id'])) {
                $distributorParentAccount = $this->Distributor->findById($distributor['Distributor']['distributor_id']);
            }
            $distributor_contacts_bdm = $this->DistributorContactBdm->getContactsByDistributorIdLimited($distributor_id);
            foreach ($distributor_contacts_bdm as $key => $distributor_contact_bdm) {
                $distributor_contacts_bdm[$key]['DistributorNetwork'] = $this->DistributorNetworkContactBdm->findAllNetworksByDistributorIdAndContactId($distributor_id, $distributor_contact_bdm['Contact']['id']);
                $distributor_contacts_bdm[$key]['Network'] = $this->NetworkContactBdm->getNetworksByContactId($distributor_contact_bdm['Contact']['id']);
            }
            $distributor_contacts_staff = $this->DistributorContactStaff->getContactsByDistributorIdLimited($distributor_id);
            $distributor_contacts_general_branch_manager = $this->DistributorContactGeneralBranchManager->getContactsByDistributorIdLimited($distributor_id);
            $distributor_comments = $this->DistributorComment->getAllByDistributorWithUsersLimited($distributor_id, ConstantsPagination::SIZE_COMMENTS);
            $positions = $this->Position->search_list();
            $distributor_labels = $this->DistributorLabel->findAllByDistributorId($distributor_id);
            $label_types = $this->LabelType->find('list');
            $conditions = array(
                'GarageDistributor.distributor_id' => $distributor_id
            );
            $conditions_networks = array();
            $conditionsStatus = array();

            if (!empty($this->request->query['network_id'])) {
                $conditions_networks = array(
                    'Network.id IN (' . $this->request->query['network_id'] . ')'
                );
            }
            if (!empty($this->request->query['status'])) {
                $conditionsStatus = array(
                    'GarageNetwork.status IN (' . $this->request->query['status'] . ')'
                );
            }
            $conditions = array_merge($conditions_networks, $conditionsStatus, $conditions);
            $tmp = array(
                'joins' => array(
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageDistributor.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                        ),
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'DISTINCT(Garage.id)',
                    'Garage.name',
                    'Garage.province_id',
                    'Garage.town',
                    'Garage.phone',
                    'Garage.g_number_id',
                ),
                'order' => array(
                    'Garage.name' => 'asc'
                ),
                'group' => 'Garage.id'
            );

            $distributor_garages = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'GarageDistributor',
                null,
                'PaginatorOrderCustom'
            );

            foreach ($distributor_garages as $key => $garages_associated) {
                $distributor_garages[$key]['GarageNetwork'] = $this->GarageNetwork->findNetworkAndStatusByGarageIdAndAagRegionId($garages_associated['Garage']['id'], $aagRegionId);
            }

            $count_distributor_garages = $this->GarageDistributor->getPaginationCountGaragesAssociated($conditions);
            $primary_activity = $this->DistributorActivityPrimary->getDistributorActivityPrimary();
            $province = $this->Province->findById($distributor['Distributor']['province_id']);
            $distributor_software = $this->DistributorSoftware->getSoftwareByDistributorIdLimited($distributor_id);
            $logs_changes = $this->LogChange->get_last_changes_by_distributor($distributor_id);
            $provinceList = $this->Garage->Province->find('list');
            $regions = $this->Region->region_list();
            $cancel_action = array(
                'url_cancel' => array(
                    'controller' => 'clients',
                    'action' => 'home_distributors',
                ),
            );

            $distributor_services = $this->DistributorService->findAllByDistributorId($distributor_id);
            $service_types = $this->ServiceType->find('list');
            $software_types = $this->SoftwareType->search_list($distributorTradingGroup['TradingGroup']['aag_region_id']);
            $suppliers = $this->Supplier->search_list();
            $software_manufactures = $this->SoftwareManufacture->search_list();
            $software = $this->Software->search_list($distributorTradingGroup['TradingGroup']['aag_region_id']);
            $customer_activities = $this->CustomerActivity->search_list();
            $workshop_activities = $this->WorkshopActivity->search_list();
            $distributor_activities = $this->DistributorCustomerActivity->findAllByDistributorId($distributor_id);
            $distributor_activity['DistributorCustomerActivity']['workshops'] = array();
            $distributor_activity['DistributorCustomerActivity']['details'] = array();
            $distributorTypes = $this->DistributorType->search_list();
            foreach ($distributor_activities as $key =>  $distributor_activity) {
                $activities_workshops_tmp = $this->DistributorCustomerActivityWorkshop->findAllByDistributorCustomerActivityId($distributor_activity['DistributorCustomerActivity']['id']);
                foreach ($activities_workshops_tmp as $activity_workshop_tmp) {
                    $distributor_activities[$key]['DistributorCustomerActivity']['workshops'][] = $workshop_activities[$activity_workshop_tmp['DistributorCustomerActivityWorkshop']['workshop_activity_id']];
                    $distributor_activities[$key]['DistributorCustomerActivity']['details'][] = $activity_workshop_tmp['DistributorCustomerActivityWorkshop']['activity_details'];
                }
            }

            $images = $this->DistributorImage->findAllByDistributorId($distributor_id);
            $distributor_principal_image = $this->Distributor->getPrincipalImageDatas($distributor_id);

            if (!empty($province)) {
                $country = $this->Country->findById($province['Province']['country_id']);
                $this->set(
                    array(
                        'country' => $country
                    )
                );
            }
            $networkList = $this->Network->getListByRegion($aagRegionId);
            $distributor_networks = $this->DistributorDistributorNetwork->findAllByDistributorId($distributor_id);
            $networks = $this->DistributorNetwork->find('all');
            $networksStatuses = Configure::read('Network_Status');
            foreach ($networksStatuses as $key => $networkStatus) {
                $networksStatuses[$key] = __t($networkStatus);
            }

            $networks_selected = $this->request->query['network_id'] ?? array();
            $status_selected = $this->request->query['status'] ?? array();

            $associationsTypes = $this->AssociationType->search_list($aagRegionId);
            $distributor_statuses_tmp = Configure::read('Distributor_Status');
            foreach ($distributor_statuses_tmp as $key => $distributorStatus) {
                $distributor_statuses[$key] = __t($distributorStatus);
            }
            $this->set(array(
                'distributor' => $distributor,
                'distributor_parent_account' => $distributorParentAccount,
                'distributor_id' => $distributor_id,
                'trading_groups' => $tradingGroups,
                'distributor_contacts_bdm' => $distributor_contacts_bdm,
                'distributor_contacts_staff' => $distributor_contacts_staff,
                'distributor_contacts_general_branch_manager' => $distributor_contacts_general_branch_manager,
                'count_distributor_garages' => $count_distributor_garages,
                'distributor_comments' => $distributor_comments,
                'distributor_garages' => $distributor_garages,
                'distributor_software' => $distributor_software,
                'distributor_activities' => $distributor_activities,
                'province' => $province,
                'primary_activity' => $primary_activity,
                'cancel_action' => $cancel_action,
                'logs_changes' => $logs_changes,
                'province_list' => $provinceList,
                'positions' => $positions,
                'regions' => $regions,
                'distributor_labels' => $distributor_labels,
                'label_types' => $label_types,
                'software_types' => $software_types,
                'suppliers' => $suppliers,
                'software_manufactures' => $software_manufactures,
                'software' => $software,
                'customer_activities' => $customer_activities,
                'workshop_activities' => $workshop_activities,
                'distributor_activities' => $distributor_activities,
                'distributor_services' => $distributor_services,
                'service_types' => $service_types,
                'images' => $images,
                'distributor_principal_image' => $distributor_principal_image,
                'distributor_types' => $distributorTypes,
                'distributor_networks' => $distributor_networks,
                'networks' => $networks,
                'networks_statuses' => $networksStatuses,
                'associations_types' => $associationsTypes,
                'distributor_statuses' => $distributor_statuses,
                'user_aag_region_id' => $aagRegionId,
                'user_role' => $roleId,
                'network_list' => $networkList,
                'networks_selected' => $networks_selected,
                'status_selected' => $status_selected,
				'sales_area' => $this->SalesArea->searchListByRegion($aagRegionId)
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * CRM garages tracking.
     */
    public function tracking($garage_id)
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);

        if (
            $garage &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::GPC_LOGISTICS_RM))
                )
            )
        ) {
            $url = Router::url(array(
                'controller' => 'clients',
                'action' => 'tracking'
            ));
            CakeSession::write('Auth.User.appointment_url', $url); // T001 SECURITY - It is not changed

            $feelings_list = $this->AppointmentFeeling->search_list();
            $feelings_colors = $this->AppointmentFeeling->find('list', array(
                'fields' => array(
                    'id',
                    'color'
                )
            ));
            $status_list = $this->AppointmentStatus->search_list();
            unset($status_list[ConstantsStatusAppointmentsDe::RUNNING]);

            $search = $this->request->query;
            $this->request->data['Search'] = $search;
            $conditions = $this->Appointment->conditions($search);
            $conditions['garage_id'] = $garage_id;
            $tmp = $this->Appointment->_query('search');
            $appointments = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL
            );

            $users = $this->User->find('list');
            $appointments_comments = $this->AppointmentComment->getAllCommentsByGarageId($garage_id);
            $status = $this->AppointmentStatus->search_list();
            $feelings = $this->AppointmentFeeling->find('list', array(
                'fields' => array(
                    'id',
                    'icon'
                )
            ));
            $appointments_types = $this->AppointmentType->search_list();
            $appointments_visit_types = $this->AppointmentType->search_appointments_visit_types();

            $this->set(array(
                'garage' => $garage,
                'feelings_list' => $feelings_list,
                'status_list' => $status_list,
                'feelings_colors' => $feelings_colors,
                'appointments' => $appointments,
                'appointments_comments' => $appointments_comments,
                'feelings' => $feelings,
                'status' => $status,
                'users' => $users,
                'appointments_types' => $appointments_types,
                'appointments_visit_types' => $appointments_visit_types
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * CRM garages task tracking.
     */
    public function tracking_task($garage_id)
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);

        if (
            $garage &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::GPC_LOGISTICS_RM))
                )
            )
        ) {
            $users = $this->User->getListRegionBDM($aagRegionId);

            $status = array(
                '0' => __t('Task.Pending'),
                '1' => __t('CRM.Completed'),
            );

            $search = $this->request->query;
            $this->request->data['Search'] = $search;
            $conditions = $this->TaskGarage->conditions($search);
            $conditions[] = array('TaskGarage.garage_id' => $garage_id);
            $tmp = $this->TaskGarage->_query('search_all_tasks_by_garage_id');
            $tasks = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'TaskGarage'
            );

            $this->set(array(
                'garage' => $garage,
                'tasks' => $tasks,
                'users' => $users,
                'status' => $status,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * CRM distributors tracking.
     */
    public function tracking_distributor($distributor_id)
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                    !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
                )
            )
        ) {
            $url = Router::url(array(
                'controller' => 'clients',
                'action' => 'tracking_distributor'
            ));
            CakeSession::write('Auth.User.appointment_url', $url); // T001 SECURITY - It is not changed

            $feelings_list = $this->AppointmentFeeling->search_list();
            $feelings_colors = $this->AppointmentFeeling->find('list', array(
                'fields' => array(
                    'id',
                    'color'
                )
            ));
            $status_list = $this->AppointmentStatus->search_list();
            $users = $this->User->listCompleteNameRegion($aagRegionId);
            $users_gpc = $this->User->getBDMGPCUsersId();

            $search = $this->request->query;
            $this->request->data['Search'] = $search;

            $conditions = $this->Appointment->conditions($search);
            $conditions['Appointment.distributor_id'] = $distributor_id;
            if ($roleId == ConstantsRoles::GPC_LOGISTICS_BDM) {
                $conditions['Appointment.user_assigned_id IN'] = $users_gpc;
            }

            $tmp = $this->Appointment->_query('search');

            $appointments = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL
            );

            $appointments_comments = $this->AppointmentComment->getAllCommentsByGarageId($distributor_id);
            $status = $this->AppointmentStatus->search_list();
            $feelings = $this->AppointmentFeeling->find('list', array(
                'fields' => array(
                    'id',
                    'icon'
                )
            ));

            $appointments_types = $this->AppointmentType->search_list();
            $appointments_visit_types = $this->AppointmentType->search_appointments_visit_types();

            foreach ($appointments as $key => $appointment) {
                $appointments[$key]['Objectives'] = $this->AppointmentObjectiveComment->getObjectives($appointment['Appointment']['id']);
            }

            $this->set(array(
                'distributor' => $distributor,
                'feelings_list' => $feelings_list,
                'status_list' => $status_list,
                'feelings_colors' => $feelings_colors,
                'appointments' => $appointments,
                'appointments_comments' => $appointments_comments,
                'feelings' => $feelings,
                'status' => $status,
                'users' => $users,
                'users_gpc' => $users_gpc,
                'appointments_types' => $appointments_types,
                'appointments_visit_types' => $appointments_visit_types,
                'management_objectives' => $this->AppointmentObjective->getManagementObjectives(),
                'personal_objectives' => $this->AppointmentObjective->getPersonalObjectives(),
                'objectives_status' => array(
                    '0' => __t('Objective.Pending'),
                    '1' => __t('General.Success'),
                    '2' => __t('Objective.Failed'),
                    '3' => __t('Objective.Requires_manager')
                ),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * CRM distributors task tracking.
     */
    public function tracking_distributor_task($distributor_id)
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                    !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
                )
            )
        ) {
            $users = $this->User->getListRegionBDM($aagRegionId);

            $status = array(
                '0' => __t('Task.Pending'),
                '1' => __t('CRM.Completed'),
            );

            $search = $this->request->query;
            $this->request->data['Search'] = $search;

            $conditions = $this->TaskDistributor->conditions($search);
            $conditions[] = array('TaskDistributor.distributor_id' => $distributor_id);
            $tmp = $this->TaskDistributor->_query('search_all_tasks_by_distributor_id');

            $tasks = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'TaskDistributor'
            );

            $this->set(array(
                'distributor' => $distributor,
                'tasks' => $tasks,
                'users' => $users,
                'status' => $status,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * CRM distributor sales.
     */
    public function distributor_sales($distributor_id)
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                    !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
                )
            )
        ) {
            $this->set(array(
                'distributor' => $distributor,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function months()
    {
        $months = array(
            __t('General.January'),
            __t('General.February'),
            __t('General.March'),
            __t('General.April'),
            __t('General.May'),
            __t('General.June'),
            __t('General.July'),
            __t('General.August'),
            __t('General.September'),
            __t('General.October'),
            __t('General.November'),
            __t('General.December'),
        );

        return $months;
    }

    /*
     * AJAX CRM garages home search.
     */
    public function ajax_search_home($type_param = null)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::GPC_LOGISTICS_RM))
            )
        ) {
            $networks = $this->Network->getListByRegion($aagRegionId);
            $tradingGroups = $this->TradingGroup->getIndependent($aagRegionId);
            $services = $this->Service->search_list();
            $vehiclesTypes = $this->VehicleType->search_list();
            $networksStatuses = Configure::read('Network_Status');
            foreach ($networksStatuses as $key => $networkStatus) {
                $networksStatuses[$key] = __t($networkStatus);
            }
            $leadSources = Configure::read('lead_source');
            foreach ($leadSources as $key => $leadSource) {
                $leadSources[$key] = __t($leadSource);
            }

            $provinceList = $this->Garage->Province->find('list');
            $distributors_list = $this->Distributor->getListByRegion($aagRegionId);
            $garages_permissions = $this->Permission->getByGroupingPermission(ConstantsGroupingPermissions::GARAGE);

            $regions = array();
            // super admin can search through all regions
            if ($roleId == ConstantsRoles::SUPER_ADMIN) {
                $regions = $this->AagRegion->region_list();
            }

            $searcher = $this->request->query;
            $searcher = self::multipleFieldsArrayCheck($searcher);
            $searcher['aag_region_id'] = $aagRegionId;

            $this->request->data['Search'] = $searcher;

            $lists_bdm = $this->GarageContactBdm->getAllBDMContacts($aagRegionId);
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
                'PaginatorUrlClient'
            );

            foreach ($garages as $key => $garage) {
                $garageId = $garage['Garage']['id'];
                $garageTmp = $this->Garage->findById($garageId);
                $garages[$key]['Distributors'] = $this->GarageDistributor->findAllByGarageId($garageId);
                $garages[$key]['Garage']['status'] = $garageTmp['Garage']['status'];
                $garages[$key]['FiguresDetail'] = $this->GarageFigureDetail->findByCustomerNo($garage['Garage']['g_number_id']);
                $garages[$key]['BDMS'] = $this->GarageContactBdm->getBDMByGarage($garageId);
                if ($garage['Garage']['last_visit']) {
                    $interval = strtotime(date('Y-m-d')) - strtotime($garage['Garage']['last_visit']);
                    $garages[$key]['LatestVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
                } else {
                    $garages[$key]['LatestVisit'] = '--';
                }

                $networkId = $this->AagRegion->get_aag_region_network($garage['Garage']['aag_region_id']);
                $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkId($garageId, $networkId);
                if ($garageNetwork) {
                    // only if Garage was active and GarageNetwork was live garage access is shown
                    $garages[$key]['ShowGarageAccess'] = $garage['Garage']['status'] == ConstantsGarageStatus::ACTIVE &&
                        $garageNetwork['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE;
                    $garages[$key]['NetworkId'] = $garageNetwork['GarageNetwork']['network_id'];
                    $garages[$key]['GarageNetworkId'] = $garageNetwork['GarageNetwork']['id'];
                }
            }

            $distributors = array();
            if (!empty($searcher['distributor_id'])) {
                $distributors = $this->Distributor->getDistributorsCompleteNamesByListOfIds($searcher['distributor_id'], $aagRegionId);
            }

            $this->set(array(
                'garages' => $garages,
                'trading_groups' => $tradingGroups,
                'networks' => $networks,
                'services' => $services,
                'vehicles_types' => $vehiclesTypes,
                'networks_statuses' => $networksStatuses,
                'lead_sources' => $leadSources,
                'province_list' => $provinceList,
                'distributors' => $distributors,
                'distributors_list' => $distributors_list,
                'selected_distributors' => json_encode($distributors),
                'garages_permissions' => $garages_permissions,
                'regions' => $regions,
                'bdm' => $bdm,
                'user_role' => $user['role_id'],
            ));

            $type_url_ajax = Configure::read('TypeSearchAjax');

            $this->layout = null;
            $this->render($type_url_ajax[$type_param]);
        } else {
            throw new UnauthorizedException();
        }
    }

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
     * AJAX CRM distributors home search.
     */
    public function ajax_search_distributors($type_param = null)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $searcher = $this->request->query;
            $searcher['aag_region_id'] = $aagRegionId;
            $this->request->data['Search'] = $searcher;

            $associations = $this->Association->find('list', array('order' => 'name'));
            $associationsTypes = $this->AssociationType->search_list($aagRegionId);

            $profile = array(
                '0' => __t('Distributor.Branch'),
                '1' => __t('Distributor.Head_office')
            );
            $type = array(
                '0' => __t('Distributor.Independent'),
                '1' => __t('Distributor.Subsidiary')
            );

            $lists_bdm = $this->DistributorContactBdm->getAllBDMContacts($aagRegionId);
            $bdm = array();
            foreach ($lists_bdm as $key => $list_bdm) {
                $id = $list_bdm['Contact']['id'];
                $bdm[$id] = $list_bdm[0]['full_name'];
            }

            $conditions = $this->Distributor->conditions($searcher);
            $conditions[] = $this->User->viewUserDistributors($this->Acceso->user());

            if (!empty($searcher['search_my_customers'])) {
                $tmp = $this->Distributor->_query('clients');
                $tmp['joins'] = array(
                    array(
                        'table' => 'distributors_contacts_bdm',
                        'alias' => 'DistributorContactBdm',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorContactBdm.distributor_id = Distributor.id'
                        )
                    ),
                );
            } else {
                $tmp = $this->Distributor->_query('clients');
            }

            $distributors = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Distributor',
                'PaginatorUrlClientDistributor'
            );

            foreach ($distributors as $key => $distributor) {
                $distributors[$key]['BDMS'] = $this->DistributorContactBdm->getBDMByDistributor($distributor['Distributor']['id']);
            }

            $tradingGroups = $this->TradingGroup->getIndependent($aagRegionId);
            $regions = $this->Region->region_list();

            $this->set(array(
                'trading_groups' => $tradingGroups,
                'distributors' => $distributors,
                'associations' => $associations,
                'profile' => $profile,
                'type' => $type,
                'regions' => $regions,
                'bdm' => $bdm,
                'associations_types' => $associationsTypes,
            ));

            $type_url_ajax = Configure::read('TypeSearchAjax');

            $this->layout = null;
            $this->render($type_url_ajax[$type_param]);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX CRM distributor associated garages search.
     */
    public function ajax_search_distributors_garages_associated($distributor_id, $type_param = null)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                    !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
                )
            )
        ) {
            $networksStatuses = Configure::read('Network_Status');
            foreach ($networksStatuses as $key => $networkStatus) {
                $networksStatuses[$key] = __t($networkStatus);
            }
            $networkList = $this->Network->getListByRegion($aagRegionId);

            $searcher = $this->request->query;
            $searcher['aag_region_id'] = $aagRegionId;
            $conditions = array(
                'GarageDistributor.distributor_id' => $distributor_id,
                'Garage.aag_region_id' => $aagRegionId,
            );
            $conditionsNetwork = array();
            $conditionsStatus = array();

            if (!empty($searcher['network_id'])) {
                $conditionsNetwork = array(
                    'Network.id IN (' . $searcher['network_id'] . ') '
                );
            };
            if (!empty($searcher['status'])) {
                $conditionsStatus = array(
                    'GarageNetwork.status IN (' . $searcher['status'] . ') '
                );
            }
            $conditions = array_merge($conditionsNetwork, $conditionsStatus, $conditions);

            $tmp = array(
                'joins' => array(
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageDistributor.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                        ),
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'DISTINCT(Garage.id)',
                    'Garage.name',
                    'Garage.province_id',
                    'Garage.town',
                    'Garage.phone',
                    'Garage.g_number_id',
                ),
                'order' => array(
                    'Garage.name' => 'asc',
                ),
                'group' => 'Garage.id'
            );

            $distributor_garages = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'GarageDistributor',
                'PaginatorUrlDistributorGaragesAssociated',
            );

            foreach ($distributor_garages as $key => $garages_associated) {
                $query = array(
                    'joins' => array(
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Network.id = GarageNetwork.network_id',
                            ),
                        ),
                    ),
                    'fields' => array(
                        'Network.name',
                        'Network.image',
                        'GarageNetwork.status',
                    ),
                    'conditions' => array(
                        'GarageNetwork.garage_id' => $garages_associated['Garage']['id'],
                        'Network.aag_region_id' => $aagRegionId,
                        $conditionsNetwork,
                        $conditionsStatus
                    ),
                );

                $distributor_garages[$key]['GarageNetwork'] = $this->GarageNetwork->find('all', $query);
            }

            $count_distributor_garages = $this->GarageDistributor->getPaginationCountGaragesAssociated($conditions);

            $this->set(array(
                'distributor' => $distributor,
                'distributor_garages' => $distributor_garages,
                'networks_statuses' => $networksStatuses,
                'network_list' => $networkList,
                'user_role' => $roleId,
                'user_aag_region_id' => $aagRegionId,
                'count_distributor_garages' => $count_distributor_garages
            ));

            $this->layout = null;
            $this->render('../Distributors/ajax_search_distributors_garages_associated');
        } else {
            throw new UnauthorizedException();
        }
    }
}
