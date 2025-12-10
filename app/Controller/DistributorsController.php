<?php
set_time_limit(60 * 60 * 60);
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class DistributorsController extends AppController
{
    public $uses = array(
        'Distributor',
        'Appointment',
        'AppointmentFeeling',
        'AppointmentStatus',
        'AppointmentComment',
        'AppointmentType',
        'Association',
        'Contact',
        'ContactRegion',
        'Country',
        'DistributorContactBdm',
        'DistributorContactStaff',
        'DistributorContactGeneralBranchManager',
        'DistributorComment',
        'DistributorSoftware',
        'DistributorDistributorNetwork',
        'GarageDistributor',
        'Position',
        'Province',
        'TradingGroup',
        'Garage',
        'User',
        'DistributorLabel',
        'LabelType',
        'DistributorService',
        'ServiceType',
        'Network',
        'DistributorNetwork',
        'DistributorContract',
        'DistributorActivity',
        'DistributorActivityPrimary',
        'DistributorDistributorActivity',
        'DistributorCustomerActivity',
        'CustomerActivity',
        'WorkshopActivity',
        'DistributorCustomerActivityWorkshop',
        'LogChange',
        'RequestedChange',
        'LeavingReasonType',
        'Region',
        'SoftwareType',
        'Supplier',
        'SoftwareManufacture',
        'Software',
        'DistributorImage',
        'DistributorType',
        'NetworkContractType',
        'AssociationType',
        'Permission',
        'Role',
        'GroupPermissionPermission',
        'PositionConfig',
        'DistributorNetworkContactBdm',
        'NetworkContactBdm',
        'GarageNetwork',
        'SalesArea'
    );

    /**
     * Distributors home page.
     */
    public function home()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
            )
        ) {
            $searcher = $this->request->query;
            $searcher['aag_region_id'] = $aagRegionId;
            $this->request->data['Search'] = $searcher;

            $profile = array(
                '0' => __t('Distributor.Branch'),
                '1' => __t('Distributor.Head_office')
            );

            $type = array(
                '0' => __t('Distributor.Independent'),
                '1' => __t('Distributor.Subsidiary')
            );

            $associations = $this->Association->find(
                'list',
                array(
                    'order' => 'name'
                )
            );

            $distributors_statuses = Configure::read('Distributor_Status');
            foreach ($distributors_statuses as $key => $distributor_status) {
                $distributors_statuses[$key] = __t($distributor_status);
            }
            $associations_types = $this->AssociationType->search_list($aagRegionId);
            $conditions = $this->Distributor->conditions($searcher);
            $conditions[] = $this->User->viewUserDistributors($this->Acceso->user());

            $bdm = array();
            if (isset($searcher['bdm_id']) && !empty($searcher['bdm_id'])) {
                $bdm_contact = $this->Contact->findById($searcher['bdm_id']);
                $bdm[$searcher['bdm_id']] = $bdm_contact['Contact']['first_name'] . ' ' . $bdm_contact['Contact']['last_name'];
            }

            $distributors = $this->custom_pagination(
                $this->Distributor->_query('clients'),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Distributor',
                null,
                'PaginatorOrderCustom'
            );

            foreach ($distributors as $key => $distributor) {
                $distributors[$key]['BDMS'] = $this->DistributorContactBdm->getBDMByDistributor($distributor['Distributor']['id']);
                if ($distributor['Distributor']['last_visit']) {
                    $interval = strtotime(date('Y-m-d')) - strtotime($distributor['Distributor']['last_visit']);
                    $distributors[$key]['LatestVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
                } else {
                    $distributors[$key]['LatestVisit'] = '--';
                }
            }

            $trading_groups = $this->TradingGroup->getTradingGroupRegion($aagRegionId);
            $regions = $this->Region->region_list();

            $distributors_permissions = $this->Permission->getByGroupingPermission(ConstantsGroupingPermissions::DISTRIBUTOR);
            $salesArea = $this->SalesArea->searchListByRegion($aagRegionId);

            $this->set(array(
                'trading_groups' => $trading_groups,
                'distributors' => $distributors,
                'associations' => $associations,
                'profile' => $profile,
                'type' => $type,
                'bdm' => $bdm,
                'regions' => $regions,
                'associations_types' => $associations_types,
                'distributors_permissions' => $distributors_permissions,
                'distributors_statuses' => $distributors_statuses,
                'sales_area' => $salesArea
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Distributor branches view. Only for Distributor.
     */
    public function branches()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        //if ($roleId == ConstantsRoles::DISTRIBUTOR) {
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            $type = array(
                '0' => __t('Distributor.Independent'),
                '1' => __t('Distributor.Subsidiary')
            );
            $associations = $this->Association->find(
                'list',
                array(
                    'order' => 'name'
                )
            );

            $conditions = $this->Distributor->conditions($searcher);
            $conditions[] = array('Distributor.id !=' => CakeSession::read('Auth.User.distributor_id'));
            $conditions[] = $this->User->viewUserMyBranches($this->Acceso->user());

            $distributors = $this->custom_pagination(
                array('order' => array(
                    'Distributor.name' => 'asc'
                )),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL
            );

            foreach ($distributors as $key => $distributor) {
                $distributors[$key]['BDMS'] = $this->DistributorContactBdm->getBDMByDistributor($distributor['Distributor']['id']);
                if ($distributor['Distributor']['last_visit']) {
                    $interval = strtotime(date('Y-m-d')) - strtotime($distributor['Distributor']['last_visit']);
                    $distributors[$key]['LatestVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
                } else {
                    $distributors[$key]['LatestVisit'] = '--';
                }
            }

            $regions = $this->Region->region_list();
            $trading_groups = $this->TradingGroup->getTradingGroup();

            $this->set(array(
                'distributors' => $distributors,
                'associations' => $associations,
                'trading_groups' => $trading_groups,
                'type' => $type,
                'regions' => $regions,
            ));
        // } else {
        //     header('HTTP/1.0 401 Unauthorized');
        //     exit;
        // }
    }

    /**
     * Distributor view.
     */
    public function view($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
            )
        ) {
            $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);
            if (!$distributor) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_DISTRIBUTOR));
                $this->redirect(
                    array(
                        'controller' => 'home',
                        'action' => 'home',
                    )
                );
            }

            $trading_groups = $this->Distributor->TradingGroup->getIndependent($aagRegionId);

            $distributor_parent_account = $this->Distributor->findById($distributor['Distributor']['distributor_id']);

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
            $distributor_services = $this->DistributorService->findAllByDistributorId($distributor_id);
            $service_types = $this->ServiceType->find('list');
            $images = $this->DistributorImage->findAllByDistributorId($distributor_id);
            $distributor_principal_image = $this->Distributor->getPrincipalImageDatas($distributor_id);
            $networks_selected = $this->request->query['network_id'] ?? null;
            $status_selected = $this->request->query['status'] ?? null;

            $conditions = array(
                'GarageDistributor.distributor_id' => $distributor_id
            );
            $conditions_networks = array();
            $conditions_status = array();

            if (!empty($this->request->query['network_id'])) {
                $conditions_networks = array(
                    'Network.id IN (' . $this->request->query['network_id'] . ')'
                );
            }
            if (!empty($this->request->query['status'])) {
                $conditions_status = array(
                    'GarageNetwork.status IN (' . $this->request->query['status'] . ')'
                );
            }
            $conditions = array_merge($conditions_networks, $conditions_status, $conditions);

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
                'conditions' => array(
                    'GarageDistributor.distributor_id' => $distributor_id,
                ),
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
            $province_list = $this->Garage->Province->find('list');
            $software_types = $this->SoftwareType->search_list($aagRegionId);
            $distributor_types = $this->DistributorType->search_list();
            $suppliers = $this->Supplier->search_list();
            $software_manufactures = $this->SoftwareManufacture->search_list();
            $software = $this->Software->search_list($aagRegionId);
            $customer_activities = $this->CustomerActivity->search_list();
            $workshop_activities = $this->WorkshopActivity->search_list();
            $distributor_activities = $this->DistributorCustomerActivity->findAllByDistributorId($distributor_id);
            $distributor_contracts = $this->DistributorContract->findAllByDistributorId($distributor_id);
            $network_list = $this->Network->getListByRegion($aagRegionId);
            $distributor_activity['DistributorCustomerActivity']['workshops'] = array();
            $distributor_activity['DistributorCustomerActivity']['details'] = array();
            foreach ($distributor_activities as $key =>  $distributor_activity) {
                $activities_workshops_tmp = $this->DistributorCustomerActivityWorkshop->findAllByDistributorCustomerActivityId($distributor_activity['DistributorCustomerActivity']['id']);
                foreach ($activities_workshops_tmp as $activity_workshop_tmp) {
                    $distributor_activities[$key]['DistributorCustomerActivity']['workshops'][] = $workshop_activities[$activity_workshop_tmp['DistributorCustomerActivityWorkshop']['workshop_activity_id']];
                    $distributor_activities[$key]['DistributorCustomerActivity']['details'][] = $activity_workshop_tmp['DistributorCustomerActivityWorkshop']['activity_details'];
                }
            }

            if (!empty($province)) {
                $country = $this->Country->findById($province['Province']['country_id']);
                $this->set(
                    array(
                        'country' => $country
                    )
                );
            }
            $networks = $this->DistributorNetwork->find('all');
            $distributor_networks = $this->DistributorDistributorNetwork->findAllByDistributorId($distributor_id);
            $networks_statuses = Configure::read('Network_Status');
            foreach ($networks_statuses as $key => $network_status) {
                $networks_statuses[$key] = __t($network_status);
            }
            $associations_types = $this->AssociationType->search_list($aagRegionId);
            $distributor_statuses_tmp = Configure::read('Distributor_Status');

            foreach ($distributor_statuses_tmp as $key => $status) {
                $distributor_statuses[$key] = __t($status);
            }

            $salesArea = $this->SalesArea->searchListByRegion($aagRegionId);

            $this->setVarForm();
            $this->set(array(
                'distributor' => $distributor,
                'distributor_id' => $distributor_id,
                'distributor_parent_account' => $distributor_parent_account,
                'distributor_types' => $distributor_types,
                'trading_groups' => $trading_groups,
                'distributor_contacts_bdm' => $distributor_contacts_bdm,
                'distributor_contacts_staff' => $distributor_contacts_staff,
                'distributor_contacts_general_branch_manager' => $distributor_contacts_general_branch_manager,
                'distributor_comments' => $distributor_comments,
                'distributor_garages' => $distributor_garages,
                'count_distributor_garages' => $count_distributor_garages,
                'distributor_software' => $distributor_software,
                'province' => $province,
                'primary_activity' => $primary_activity,
                'logs_changes' => $logs_changes,
                'province_list' => $province_list,
                'positions' => $positions,
                'distributor_labels' => $distributor_labels,
                'label_types' => $label_types,
                'distributor_services' => $distributor_services,
                'service_types' => $service_types,
                'software_types' => $software_types,
                'suppliers' => $suppliers,
                'customer_activities' => $customer_activities,
                'workshop_activities' => $workshop_activities,
                'distributor_activities' => $distributor_activities,
                'software_manufactures' => $software_manufactures,
                'software' => $software,
                'images' => $images,
                'distributor_principal_image' => $distributor_principal_image,
                'networks' => $networks,
                'distributor_networks' => $distributor_networks,
                'networks_statuses' => $networks_statuses,
                'distributor_contracts' => $distributor_contracts,
                'network_list' => $network_list,
                'distributor_statuses' => $distributor_statuses,
                'associations_types' => $associations_types,
                'user_aag_region_id' => $aagRegionId,
                'user_role' => $roleId,
                'networks_selected' => $networks_selected,
                'status_selected' => $status_selected,
                'sales_area' => isset($distributor['Distributor']['sales_area_id']) ? $salesArea[$distributor['Distributor']['sales_area_id']] : ''
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * My data distributor page.
     */
    public function my_data($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];
        $userDistributorId = $user['distributor_id'];

        if (
            $roleId == ConstantsRoles::DISTRIBUTOR &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
        ) {
            $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);
            $allowed_distributors = $this->Distributor->getDistributorAndBranchesByDistributorId($userDistributorId);
            if (!$distributor || !array_key_exists($distributor_id, $allowed_distributors)) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_DISTRIBUTOR));
                $this->redirect(
                    array(
                        'controller' => 'home',
                        'action' => 'home',
                    )
                );
            }

            $position_id = CakeSession::read('Auth.User.Contact.position_id');
            $position_configs = $this->PositionConfig->findAllByPositionId($position_id);
            $group_permission_permission = $this->GroupPermissionPermission->findAllByGroupPermissionId(Hash::extract($position_configs, '{n}.PositionConfig.group_permission_id'));

            $permissions_id = Hash::extract($group_permission_permission, '{n}.GroupPermissionPermission.permission_id');
            if (in_array(ConstantsPermissionsGrouping::CREATE_DISTRIBUTOR_USER, $permissions_id) && $roleId == ConstantsRoles::DISTRIBUTOR) {
                $add_users = true;
            } else {
                $add_users = false;
            }

            $trading_groups = $this->Distributor->TradingGroup->getIndependent($aagRegionId);
            $distributorTradingGroup = $this->TradingGroup->findById($distributor['Distributor']['trading_group_id']);
            $distributors = $this->Distributor->getCompleteList($aagRegionId);
            $distributors_names = $this->Distributor->getCompleteListName();
            $distributors_account_numbers = $this->Distributor->getCompleteListAccountNumber();

            $distributor_contacts_bdm = $this->DistributorContactBdm->getContactsByDistributorIdLimited($distributor_id);
            foreach ($distributor_contacts_bdm as $key => $distributor_contact_bdm) {
                $distributor_contacts_bdm[$key]['DistributorNetwork'] = $this->DistributorNetworkContactBdm->findAllNetworksByDistributorIdAndContactId($distributor_id, $distributor_contact_bdm['Contact']['id']);
                $distributor_contacts_bdm[$key]['Network'] = $this->NetworkContactBdm->getNetworksByContactId($distributor_contact_bdm['Contact']['id']);
            }

            $distributor_contacts_staff = $this->DistributorContactStaff->getContactsByDistributorIdLimited($distributor_id);
            $distributor_contacts_general_branch_manager = $this->DistributorContactGeneralBranchManager->getContactsByDistributorIdLimited($distributor_id);
            $distributor_comments = $this->DistributorComment->getAllByDistributorWithUsersLimited($distributor_id, ConstantsPagination::SIZE_PAGE_LARGE);
            $positions = $this->Position->search_list();
            $distributor_labels = $this->DistributorLabel->findAllByDistributorId($distributor_id);
            $distributor_services = $this->DistributorService->findAllByDistributorId($distributor_id);
            $service_types = $this->ServiceType->find('list');
            $label_types = $this->LabelType->find('list');
            $images = $this->DistributorImage->findAllByDistributorId($distributor_id);
            $distributor_principal_image = $this->Distributor->getPrincipalImageDatas($distributor_id);
            $distributor_garages = $this->GarageDistributor->getByLimitGaragesByDistributorId($distributor_id, 10);
            $count_distributor_garages = $this->GarageDistributor->countAllGaragesByDistributorId($distributor_id);
            $primary_activity = $this->DistributorActivityPrimary->getDistributorActivityPrimary();
            $province = $this->Province->findById($distributor['Distributor']['province_id']);
            $distributor_software = $this->DistributorSoftware->getSoftwareByDistributorIdLimited($distributor_id);
            $logs_changes = $this->LogChange->get_last_changes_by_distributor($distributor_id);
            $province_list = $this->Garage->Province->find('list');
            $software_types = $this->SoftwareType->search_list($distributorTradingGroup['TradingGroup']['aag_region_id']);
            $suppliers = $this->Supplier->search_list();
            $software_manufactures = $this->SoftwareManufacture->search_list();
            $software = $this->Software->search_list($distributorTradingGroup['TradingGroup']['aag_region_id']);
            $customer_activities = $this->CustomerActivity->search_list();
            $workshop_activities = $this->WorkshopActivity->search_list();
            $distributor_activities = $this->DistributorCustomerActivity->findAllByDistributorId($distributor_id);
            $distributor_types = $this->DistributorType->search_list();
            $distributor_activity['DistributorCustomerActivity']['workshops'] = array();
            $distributor_activity['DistributorCustomerActivity']['details'] = array();
            $associations_types = $this->AssociationType->search_list($aagRegionId);
            foreach ($distributor_activities as $key =>  $distributor_activity) {
                $activities_workshops_tmp = $this->DistributorCustomerActivityWorkshop->findAllByDistributorCustomerActivityId($distributor_activity['DistributorCustomerActivity']['id']);
                foreach ($activities_workshops_tmp as $activity_workshop_tmp) {
                    $distributor_activities[$key]['DistributorCustomerActivity']['workshops'][] = $workshop_activities[$activity_workshop_tmp['DistributorCustomerActivityWorkshop']['workshop_activity_id']];
                    $distributor_activities[$key]['DistributorCustomerActivity']['details'][] = $activity_workshop_tmp['DistributorCustomerActivityWorkshop']['activity_details'];
                }
            }

            if (!empty($province)) {
                $country = $this->Country->findById($province['Province']['country_id']);
                $this->set(
                    array(
                        'country' => $country
                    )
                );
            }

            $networks_statuses = Configure::read('Network_Status');
            foreach ($networks_statuses as $key => $network_status) {
                $networks_statuses[$key] = __t($network_status);
            }
            $networks = $this->DistributorNetwork->find('all');
            $distributor_networks = $this->DistributorDistributorNetwork->findAllByDistributorId($distributor_id);
            $distributor_statuses_tmp = Configure::read('Distributor_Status');
            foreach ($distributor_statuses_tmp as $key => $status) {
                $distributor_statuses[$key] = __t($status);
            }

            $this->setVarForm();
            $this->set(array(
                'networks' => $networks,
                'networks_statuses' => $networks_statuses,
                'distributor' => $distributor,
                'distributor_id' => $distributor_id,
                'distributors' => $distributors,
                'distributors_names' => $distributors_names,
                'distributor_networks' => $distributor_networks,
                'distributors_account_numbers' => $distributors_account_numbers,
                'trading_groups' => $trading_groups,
                'distributor_contacts_bdm' => $distributor_contacts_bdm,
                'distributor_contacts_staff' => $distributor_contacts_staff,
                'distributor_contacts_general_branch_manager' => $distributor_contacts_general_branch_manager,
                'distributor_comments' => $distributor_comments,
                'distributor_garages' => $distributor_garages,
                'count_distributor_garages' => $count_distributor_garages,
                'distributor_software' => $distributor_software,
                'distributor_activities' => $distributor_activities,
                'province' => $province,
                'primary_activity' => $primary_activity,
                'logs_changes' => $logs_changes,
                'province_list' => $province_list,
                'positions' => $positions,
                'distributor_labels' => $distributor_labels,
                'label_types' => $label_types,
                'distributor_services' => $distributor_services,
                'service_types' => $service_types,
                'software_types' => $software_types,
                'suppliers' => $suppliers,
                'software_manufactures' => $software_manufactures,
                'software' => $software,
                'customer_activities' => $customer_activities,
                'workshop_activities' => $workshop_activities,
                'distributor_activities' => $distributor_activities,
                'images' => $images,
                'distributor_principal_image' => $distributor_principal_image,
                'distributor_types' => $distributor_types,
                'associations_types' => $associations_types,
                'add_users' => $add_users,
                'distributor_statuses' => $distributor_statuses,
                'user_aag_region_id' => $aagRegionId,
                'user_role' => $roleId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * My branch distributor page.
     */
    public function my_branch($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];
        $userDistributorId = $user['distributor_id'];

        if ($roleId == ConstantsRoles::DISTRIBUTOR) {
            $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);
            $allowed_distributors = $this->Distributor->getDistributorAndBranchesByDistributorId($userDistributorId);
            if (!$distributor || !array_key_exists($distributor_id, $allowed_distributors)) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_DISTRIBUTOR));
                $this->redirect(
                    array(
                        'controller' => 'home',
                        'action' => 'home',
                    )
                );
            }

            $position_id = CakeSession::read('Auth.User.Contact.position_id');
            $position_configs = $this->PositionConfig->findAllByPositionId($position_id);
            $group_permission_permission = $this->GroupPermissionPermission->findAllByGroupPermissionId(Hash::extract($position_configs, '{n}.PositionConfig.group_permission_id'));

            $permissions_id = Hash::extract($group_permission_permission, '{n}.GroupPermissionPermission.permission_id');
            $add_users = in_array(ConstantsPermissionsGrouping::CREATE_DISTRIBUTOR_USER, $permissions_id) && $roleId == ConstantsRoles::DISTRIBUTOR ? true : false;

            $trading_groups = $this->Distributor->TradingGroup->getIndependent($aagRegionId);

            $distributorTradingGroup = $this->TradingGroup->findById($distributor['Distributor']['trading_group_id']);
            $distributors = $this->Distributor->getCompleteList($aagRegionId);
            $distributors_names = $this->Distributor->getCompleteListName();
            $distributors_account_numbers = $this->Distributor->getCompleteListAccountNumber();
            $distributor_contacts_bdm = $this->DistributorContactBdm->getContactsByDistributorIdLimited($distributor_id);
            foreach ($distributor_contacts_bdm as $key => $distributor_contact_bdm) {
                $distributor_contacts_bdm[$key]['DistributorNetwork'] = $this->DistributorNetworkContactBdm->findAllNetworksByDistributorIdAndContactId($distributor_id, $distributor_contact_bdm['Contact']['id']);
                $distributor_contacts_bdm[$key]['Network'] = $this->NetworkContactBdm->getNetworksByContactId($distributor_contact_bdm['Contact']['id']);
            }

            $distributor_contacts_staff = $this->DistributorContactStaff->getContactsByDistributorIdLimited($distributor_id);
            $distributor_contacts_general_branch_manager = $this->DistributorContactGeneralBranchManager->getContactsByDistributorIdLimited($distributor_id);
            $distributor_comments = $this->DistributorComment->getAllByDistributorWithUsersLimited($distributor_id, ConstantsPagination::SIZE_PAGE_LARGE);
            $positions = $this->Position->search_list();
            $distributor_labels = $this->DistributorLabel->findAllByDistributorId($distributor_id);
            $distributor_services = $this->DistributorService->findAllByDistributorId($distributor_id);
            $service_types = $this->ServiceType->find('list');
            $label_types = $this->LabelType->find('list');
            $images = $this->DistributorImage->findAllByDistributorId($distributor_id);
            $distributor_principal_image = $this->Distributor->getPrincipalImageDatas($distributor_id);
            $distributor_garages = $this->GarageDistributor->getByLimitGaragesByDistributorId($distributor_id, 10);
            $count_distributor_garages = $this->GarageDistributor->countAllGaragesByDistributorId($distributor_id);
            $primary_activity = $this->DistributorActivityPrimary->getDistributorActivityPrimary();
            $province = $this->Province->findById($distributor['Distributor']['province_id']);
            $distributor_software = $this->DistributorSoftware->getSoftwareByDistributorIdLimited($distributor_id);
            $logs_changes = $this->LogChange->get_last_changes_by_distributor($distributor_id);
            $province_list = $this->Garage->Province->find('list');
            $software_types = $this->SoftwareType->search_list($distributorTradingGroup['TradingGroup']['aag_region_id']);
            $suppliers = $this->Supplier->search_list();
            $software_manufactures = $this->SoftwareManufacture->search_list();
            $software = $this->Software->search_list($distributorTradingGroup['TradingGroup']['aag_region_id']);
            $customer_activities = $this->CustomerActivity->search_list();
            $workshop_activities = $this->WorkshopActivity->search_list();
            $distributor_activities = $this->DistributorCustomerActivity->findAllByDistributorId($distributor_id);
            $distributor_types = $this->DistributorType->search_list();
            $distributor_activity['DistributorCustomerActivity']['workshops'] = array();
            $distributor_activity['DistributorCustomerActivity']['details'] = array();
            $associations_types = $this->AssociationType->search_list($aagRegionId);
            foreach ($distributor_activities as $key =>  $distributor_activity) {
                $activities_workshops_tmp = $this->DistributorCustomerActivityWorkshop->findAllByDistributorCustomerActivityId($distributor_activity['DistributorCustomerActivity']['id']);
                foreach ($activities_workshops_tmp as $activity_workshop_tmp) {
                    $distributor_activities[$key]['DistributorCustomerActivity']['workshops'][] = $workshop_activities[$activity_workshop_tmp['DistributorCustomerActivityWorkshop']['workshop_activity_id']];
                    $distributor_activities[$key]['DistributorCustomerActivity']['details'][] = $activity_workshop_tmp['DistributorCustomerActivityWorkshop']['activity_details'];
                }
            }

            if (!empty($province)) {
                $country = $this->Country->findById($province['Province']['country_id']);
                $this->set(
                    array(
                        'country' => $country
                    )
                );
            }

            $networks_statuses = Configure::read('Network_Status');
            foreach ($networks_statuses as $key => $network_status) {
                $networks_statuses[$key] = __t($network_status);
            }
            $networks = $this->DistributorNetwork->find('all');
            $distributor_networks = $this->DistributorDistributorNetwork->findAllByDistributorId($distributor_id);
            $distributor_statuses_tmp = Configure::read('Distributor_Status');
            foreach ($distributor_statuses_tmp as $key => $status) {
                $distributor_statuses[$key] = __t($status);
            }

            $this->setVarForm();
            $this->set(array(
                'networks' => $networks,
                'networks_statuses' => $networks_statuses,
                'distributor' => $distributor,
                'distributor_id' => $distributor_id,
                'distributors' => $distributors,
                'distributors_names' => $distributors_names,
                'distributor_networks' => $distributor_networks,
                'distributors_account_numbers' => $distributors_account_numbers,
                'trading_groups' => $trading_groups,
                'distributor_contacts_bdm' => $distributor_contacts_bdm,
                'distributor_contacts_staff' => $distributor_contacts_staff,
                'distributor_contacts_general_branch_manager' => $distributor_contacts_general_branch_manager,
                'distributor_comments' => $distributor_comments,
                'distributor_garages' => $distributor_garages,
                'count_distributor_garages' => $count_distributor_garages,
                'distributor_software' => $distributor_software,
                'distributor_activities' => $distributor_activities,
                'province' => $province,
                'primary_activity' => $primary_activity,
                'logs_changes' => $logs_changes,
                'province_list' => $province_list,
                'positions' => $positions,
                'distributor_labels' => $distributor_labels,
                'label_types' => $label_types,
                'distributor_services' => $distributor_services,
                'service_types' => $service_types,
                'software_types' => $software_types,
                'suppliers' => $suppliers,
                'software_manufactures' => $software_manufactures,
                'software' => $software,
                'customer_activities' => $customer_activities,
                'workshop_activities' => $workshop_activities,
                'distributor_activities' => $distributor_activities,
                'images' => $images,
                'distributor_principal_image' => $distributor_principal_image,
                'distributor_types' => $distributor_types,
                'associations_types' => $associations_types,
                'add_users' => $add_users,
                'distributor_statuses' => $distributor_statuses,
                'user_aag_region_id' => $aagRegionId,
                'user_role' => $roleId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create DistributorNetwork.
     */
    public function add_networks_distributor($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR, ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
        ) {
            $distributor_networks = $this->custom_pagination(
                $this->Distributor->_query('networks'),
                array('DistributorDistributorNetwork.distributor_id' => $distributor_id),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Distributor',
                null,
                'PaginatorOrderCustom'
            );
            $reasons_leaving = $this->LeavingReasonType->getList();
            $networks = $this->DistributorNetwork->find('list');
            $trading_groups = $this->TradingGroup->find('all');
            $networks_statuses = Configure::read('Network_Status');
            foreach ($networks_statuses as $key => $network_status) {
                $networks_statuses[$key] = __t($network_status);
            }
            $networks_image = $this->DistributorNetwork->find('all');
            $networks_contracts = $this->NetworkContractType->search_list('list');
            $suppliers = $this->Supplier->find('list');

            if (!$this->request->is('get')) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Distributor->table,
                    'Network.Networks',
                    $user['User']['id'],
                    $distributor_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::DISTRIBUTOR
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $this->setVarCancel($distributor_id);
            $this->set(array(
                'distributor' => $distributor,
                'distributor_id' => $distributor_id,
                'networks' => $networks,
                'suppliers' => $suppliers,
                'trading_groups' => $trading_groups,
                'networks_statuses' => $networks_statuses,
                'distributor_networks' => $distributor_networks,
                'networks_contracts' => $networks_contracts,
                'networks_image' => $networks_image,
                'reasons_leaving' => $reasons_leaving
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add opening hours distributors.
     */
    public function add_opening_distributor($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR, ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
        ) {
            $distributor_old_data_tmp = $distributor;

            if (!$this->request->is('get')) {
                $user = $this->Session->read('Auth');

                if (isset($this->request->data['request_changes'])) {
                    $result = $this->RequestedChange->create_request_edit(
                        $distributor_old_data_tmp['Distributor'],
                        $this->request->data['Distributor'],
                        $this->Distributor->table,
                        $user,
                        $distributor_id,
                        ConstantsLogType::DISTRIBUTOR
                    );
                    if ($result) {
                        $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                        $this->redirect($this->here);
                    } else {
                        $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                    }
                } else {
                    $distributor = $this->Distributor->add_opening_distributor($this->request->data, $distributor_id);
                    if ($distributor) {
                        $this->LogChange->get_params_create_log_edit(
                            $distributor_old_data_tmp['Distributor'],
                            $distributor['Distributor'],
                            $this->Distributor->table,
                            $user,
                            $distributor_id,
                            ConstantsLogType::DISTRIBUTOR
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                }
            } else {
                $this->request->data = $distributor;
            }

            $this->setVarCancel($distributor_id);
            $this->set(array(
                'distributor' => $distributor,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create Distributor.
     */
    public function add()
    {
        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR, ConstantsPermissionsGrouping::CREATE_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $roleId = $user['role_id'];

            $trading_groups = $this->Distributor->TradingGroup->getIndependent($aagRegionId);

            $associations = $this->Association->find(
                'list',
                array(
                    'order' => 'name'
                )
            );
            $associations_types = $this->AssociationType->search_list($aagRegionId);

            $countries = $this->Country->get_country_by_region($aagRegionId);
            $count_countries = count($countries);
            if ($count_countries > 1) {
                $provinces = '';
            } else {
                $search_country = array_keys($countries);
                $provinces = $this->Province->getProvincesByCountry($search_country[0]);
            }

            $languages = $this->Language->getLanguagesCodeNameWithoutLoco();

            if (!$this->request->is('get')) {
                if (isset($this->request->data['province_id'])) {
                    $this->request->data['Distributor']['province_id'] = $this->request->data['province_id'];
                }
                $distributor_bd = $this->Distributor->add_distributor($this->request->data, $user);
                if ($distributor_bd) {
                    $msg = h(sprintf(__t('Distributor.Well_add'), $distributor_bd['Distributor']['name']));
                    $this->Session->setFlashSuccess($msg);
                    $this->redirect(
                        array(
                            'controller' => 'distributors',
                            'action' => 'edit',
                            $this->Distributor->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t('Distributor.Error_create'));
                }
            }
            $distributor_types = $this->DistributorType->search_list();
            $distributor_statuses_tmp = Configure::read('Distributor_Status');
            foreach ($distributor_statuses_tmp as $key => $status) {
                $distributor_statuses[$key] = __t($status);
            }

            $salesArea = $this->SalesArea->searchListByRegion($aagRegionId);

            $this->setVarForm();
            $this->set(array(
                'trading_groups' => $trading_groups,
                'provinces' => $provinces,
                'countries' => $countries,
                'count_countries' => $count_countries,
                'associations' => $associations,
                'distributor_types' => $distributor_types,
                'distributor_statuses' => $distributor_statuses,
                'associations_types' => $associations_types,
                'user_aag_region_id' => $aagRegionId,
                'languages' => $languages,
                'sales_area' => $salesArea
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit Distributor.
     */
    public function edit($distributor_id)
    {
        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR, ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $roleId = $user['role_id'];

            $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);
            if (!$distributor) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_DISTRIBUTOR));
                $this->redirect(
                    array(
                        'controller' => 'home',
                        'action' => 'home',
                    )
                );
            }

            $distributor_types = $this->DistributorType->search_list();

            $trading_groups = $this->Distributor->TradingGroup->getIndependent($aagRegionId);

            $selected_distributor_parent_account = array();
            if (!empty($distributor['Distributor']['distributor_id'])) {
                $parent_distributor = $this->Distributor->findById($distributor['Distributor']['distributor_id']);
                $selected_distributor_parent_account[$distributor['Distributor']['distributor_id']] = $parent_distributor['Distributor']['complete_name'];
            }

            $associations = $this->Association->find(
                'list',
                array(
                    'order' => 'name'
                )
            );
            $associations_types = $this->AssociationType->search_list($aagRegionId);

            $countries = $this->Country->find('list');
            $count_countries = count($countries);
            if ($count_countries > 1) {
                if ($distributor['Distributor']['province_id'] != '') {
                    $country_province = $this->Province->getCountryByProvince($distributor['Distributor']['province_id']);
                    $provinces = $this->Province->getProvincesByCountry($country_province['Province']['country_id']);
                    $distributor['Distributor']['country'] = $country_province['Province']['country_id'];
                } else {
                    $provinces = '';
                }
            } else {
                $search_country = array_keys($countries);
                $provinces = $this->Province->getProvincesByCountry($search_country[0]);
            }

            $languages = $this->Language->getLanguagesCodeNameWithoutLoco();

            if (!$this->request->is('get')) {
                $user =  $this->Session->read('Auth');
                if (isset($this->request->data['province_id'])) {
                    $this->request->data['Distributor']['province_id'] = $this->request->data['province_id'];
                }
                if (!isset($this->request->data['request_changes'])) {
                    $distributor_bd = $this->Distributor->edit_distributor($this->request->data);
                } else {
                    $distributor_bd = true;
                }
                if ($distributor_bd) {
                    if (!isset($this->request->data['request_changes'])) {
                        $this->LogChange->get_params_create_log_edit(
                            $distributor['Distributor'],
                            $distributor_bd['Distributor'],
                            $this->Distributor->table,
                            $user,
                            $distributor_id,
                            ConstantsLogType::DISTRIBUTOR
                        );

                        $msg = h(sprintf(__t('Distributor.Well_edit'), $distributor['Distributor']['name']));
                        $this->Session->setFlashSuccess($msg);
                        $this->redirect($this->request->here);
                    } else {
                        $result = $this->RequestedChange->create_request_edit(
                            $distributor['Distributor'],
                            $this->request->data['Distributor'],
                            $this->Distributor->table,
                            $user,
                            $distributor_id,
                            ConstantsLogType::DISTRIBUTOR
                        );
                        if ($result) {
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
                $this->request->data = $distributor;
                $this->request->data['Distributor']['start_date'] = Fecha::toFormatoVista($distributor['Distributor']['start_date']);
                $this->request->data['Distributor']['end_date'] = Fecha::toFormatoVista($distributor['Distributor']['end_date']);
            }

            $distributor_statuses_tmp = Configure::read('Distributor_Status');
            foreach ($distributor_statuses_tmp as $key => $status) {
                $distributor_statuses[$key] = __t($status);
            }

            $salesArea = $this->SalesArea->searchListByRegion($aagRegionId);

            $this->setVarCancel($distributor_id);
            $this->set(array(
                'trading_groups' => $trading_groups,
                'distributor' => $distributor,
                'provinces' => $provinces,
                'countries' => $countries,
                'associations' => $associations,
                'count_countries' => $count_countries,
                'distributor_types' => $distributor_types,
                'distributor_statuses' => $distributor_statuses,
                'associations_types' => $associations_types,
                'selected_distributor_parent_account' => $selected_distributor_parent_account,
                'user_aag_region_id' => $aagRegionId,
                'languages' => $languages,
                'sales_area' => $salesArea
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Associated garages home.
     */
    public function home_associated($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $province_list = $this->Province->get_list();

            $tmp = $this->GarageDistributor->_query('Search');
            $tmp['conditions'] = array(
                'GarageDistributor.distributor_id' => $distributor_id,
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            $conditions = $this->GarageDistributor->conditions($searcher);

            $distributor_garages = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'GarageDistributor',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(
                array(
                    'distributor_garages' => $distributor_garages,
                    'distributor' => $distributor,
                    'distributor_id' => $distributor_id,
                    'province_list' => $province_list,
                    'user_aag_region_id' => $aagRegionId,
                    'user_role' => $roleId
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Logs changes home.
     */
    public function home_logs_changes($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $tmp = $this->LogChange->_query('search_distributors');
            $tmp['conditions'] = array(
                'LogChange.distributor_id' => $distributor_id,
            );
            $tmp['order'] = array(
                'LogChange.date' => 'desc'
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

            $this->set(
                array(
                    'logs_changes' => $logs_changes,
                    'distributor_id' => $distributor_id,
                    'distributor' => $distributor
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Distributor requested changes.
     */
    public function requested_changes($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $tmp = $this->RequestedChange->_query('search_distributors');
            $tmp['conditions'] = array(
                'RequestedChange.distributor_id' => $distributor_id,
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

            $this->set(
                array(
                    'requested_changes' => $requested_changes,
                    'distributor_id' => $distributor_id,
                    'distributor' => $distributor,
                    'user_id' => $user['User']['id']
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete requested change.
     */
    public function ajax_delete_requested_change($change_id)
    {
        $requestedChange = $this->RequestedChange->findById($change_id);

        if ($requestedChange) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $distributor = $this->Distributor->findByIdAndAagRegionId($requestedChange['RequestedChange']['distributor_id'], $aagRegionId);

            if (
                $distributor &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
                $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
            ) {
                if ($this->RequestedChange->delete($change_id)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $precess = 'true';
                    $error_text = '';
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    $precess = 'false';
                    $error_text = __t('RequestedChanges.Bad_Deleted');
                }
                $ret = array(
                    'precess' => $precess,
                    'error_text' => $error_text
                );

                echo json_encode($ret);

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
     * AJAX search home.
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
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
            )
        ) {
            $searcher = $this->request->query;
            $searcher['aag_region_id'] = $aagRegionId;
            $this->request->data['Search'] = $searcher;

            $associations = $this->Association->find('list', array('order' => 'name'));
            $associations_types = $this->AssociationType->search_list($aagRegionId);

            $profile = array(
                '0' => __t('Distributor.Branch'),
                '1' => __T('Distributor.Head_office')
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
                'PaginatorUrlDistributor'
            );

            foreach ($distributors as $key => $distributor) {
                $distributors[$key]['BDMS'] = $this->DistributorContactBdm->getBDMByDistributor($distributor['Distributor']['id']);
            }

            $trading_groups = $this->TradingGroup->getIndependent($aagRegionId);
            $regions = $this->Region->region_list();

            $this->set(array(
                'trading_groups' => $trading_groups,
                'distributors' => $distributors,
                'associations' => $associations,
                'profile' => $profile,
                'type' => $type,
                'regions' => $regions,
                'bdm' => $bdm,
                'associations_types' => $associations_types,
            ));

            $type_url_ajax = Configure::read('TypeSearchAjax');

            $this->layout = null;
            $this->render($type_url_ajax[$type_param]);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search home branches.
     */
    public function ajax_search_home_branches()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if ($roleId == ConstantsRoles::DISTRIBUTOR) {
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            $type = array(
                '0' => __t('Distributor.Independent'),
                '1' => __t('Distributor.Subsidiary')
            );
            $associations = $this->Association->find(
                'list',
                array(
                    'order' => 'name'
                )
            );

            $conditions = $this->Distributor->conditions($searcher);
            $conditions[] = $this->User->viewUserMyBranches($this->Acceso->user());

            $distributors = $this->custom_pagination(
                array('order' => array(
                    'Distributor.name' => 'asc'
                )),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL
            );

            $regions = $this->Region->region_list();
            $trading_groups = $this->TradingGroup->getTradingGroup();

            $this->set(array(
                'distributors' => $distributors,
                'associations' => $associations,
                'trading_groups' => $trading_groups,
                'type' => $type,
                'regions' => $regions,
            ));

            $this->layout = null;
            $this->render('../Distributors/Elements/ajax_search_home_branches');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX distributor Excel.
     */
    public function ajax_distributor_excel($controller)
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
            )
        ) {
            set_time_limit(18000);
            ini_set("memory_limit", "1G");
            $searcher = $this->request->query;

            $this->request->data['Search'] = $searcher;

            $conditions = $this->Distributor->conditions($searcher);
            $conditions[] = $this->User->viewUserDistributors($this->Acceso->user());
            $conditions[] = array(
                'Distributor.aag_region_id' => CakeSession::read(
                    'Auth.User.aag_region_id'
                )
            );

            $optional_join = null;

            if (!empty($searcher['search_my_customers'])) {
                $optional_join = array(
                    'table' => 'distributors_contacts_bdm',
                    'alias' => 'DistributorContactBdm',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorContactBdm.distributor_id = Distributor.id'
                    ),
                    'fields' => 'DistributorContactBdm.*',
                );
            }

            $distributors = $this->Distributor->find(
                'all',
                Hash::merge(
                    array(),
                    array(
                        'joins' => array($optional_join),
                        'conditions' => $conditions,
                    )
                )

            );
            $this->ajaxDistributorExcelExport($distributors, $controller);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX distributor Excel branches.
     */
    public function ajax_distributor_excel_branches($controller)
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if ($roleId == ConstantsRoles::DISTRIBUTOR) {
            $searcher = $this->request->query;

            $this->request->data['Search'] = $searcher;

            $conditions = $this->Distributor->conditions($searcher);
            $conditions[] = $this->User->viewUserMyBranches($this->Acceso->user());

            $distributors = $this->Distributor->find(
                'all',
                Hash::merge(
                    array(),
                    array(
                        'conditions' => $conditions,
                    )
                )

            );
            $this->ajaxDistributorExcelExport($distributors, $controller);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX distriburor Excel planning visit.
     */
    public function ajax_distributor_excel_planing_visit($controller)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $distributors_id = $this->request->query;
            $distributors = array();

            foreach ($distributors_id as $key => $distributor_id) {
                $distributor = $this->Distributor->findByIdAndAagRegionId($key, $aagRegionId);
                if ($distributor) {
                    $distributors[] = $distributor;
                }
            }

            $this->ajaxDistributorExcelExport($distributors, $controller);
        } else {
            throw new UnauthorizedException();
        }
    }

    private function ajaxDistributorExcelExport($distributors, $controller = null)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $trading_group_list = $this->Distributor->TradingGroup->getTradingGroupRegion($aagRegionId);
        $trading_groups = $this->TradingGroup->getTradingGroupRegion($aagRegionId);
        $associations = $this->AssociationType->getAssociation();
        $regions = $this->Region->region_list();
        $salesArea = $this->SalesArea->searchListByRegion($aagRegionId);

        foreach ($distributors as $key => $distributor) {
            if ($distributor['Distributor']['last_visit']) {
                $distributors[$key]['LatestVisit'] = $distributor['Distributor']['last_visit'];
                $interval = strtotime(date('Y-m-d')) - strtotime($distributor['Distributor']['last_visit']);
                $distributors[$key]['RemainingVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
            }

            $distributors[$key]['ContactsBdm'] = $this->DistributorContactBdm->findContactsBdmExport($distributor['Distributor']['id']);
        }

        $this->set(array(
            'controller' => $controller,
            'distributors' => $distributors,
            'trading_group_list' => $trading_group_list,
            'trading_groups' => $trading_groups,
            'associations' => $associations,
            'regions' => $regions,
            'sales_area' => $salesArea
        ));

        set_time_limit(18000);
        ini_set('memory_limit', '-1');

        $this->render('/Distributors/Elements/export_excel_distributors');
        $this->response->type('xlsx');
        $this->layout = false;
    }

    private function setVarForm()
    {
        $cancel_action = array(
            'url_cancel' => array(
                'controller' => 'distributors',
                'action' => 'home',
            ),
        );

        $regions = $this->Region->region_list();

        $this->set(array(
            'cancel_action' => $cancel_action,
            'regions' => $regions,
        ));
    }

    /**
     * Create Distributor Activity.
     */
    public function add_activity($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $customer_activities = $this->CustomerActivity->search_list();
            $workshop_activities = $this->WorkshopActivity->search_list();
            $distributor_activities = $this->custom_pagination(
                $this->Distributor->_query('activities'),
                array('DistributorCustomerActivity.distributor_id' => $distributor_id),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Distributor',
                null,
                'PaginatorOrderCustom'
            );
            $distributor_activity['DistributorCustomerActivity']['workshops'] = array();
            $distributor_activity['DistributorCustomerActivity']['details'] = array();
            foreach ($distributor_activities as $key =>  $distributor_activity) {
                $activities_workshops_tmp = $this->DistributorCustomerActivityWorkshop->findAllByDistributorCustomerActivityId($distributor_activity['DistributorCustomerActivity']['id']);
                foreach ($activities_workshops_tmp as $activity_workshop_tmp) {
                    $distributor_activities[$key]['DistributorCustomerActivity']['workshops'][] = $workshop_activities[$activity_workshop_tmp['DistributorCustomerActivityWorkshop']['workshop_activity_id']];
                    $distributor_activities[$key]['DistributorCustomerActivity']['details'][] = $activity_workshop_tmp['DistributorCustomerActivityWorkshop']['activity_details'];
                }
            }

            if (!$this->request->is('get')) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Distributor->table,
                    'Distributor.Activity',
                    $user['User']['id'],
                    $distributor_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::DISTRIBUTOR
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $this->setVarCancel($distributor_id);
            $this->set(array(
                'distributor' => $distributor,
                'distributor_activities' => $distributor_activities,
                'customer_activities' => $customer_activities,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create DistributorServices.
     */
    public function add_services($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $distributor_services = $this->DistributorService->findAllByDistributorId($distributor_id);
            $services_types = $this->ServiceType->find('list');

            if (!$this->request->is('get')) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Distributor->table,
                    'Distributor.Aag_services',
                    $user['User']['id'],
                    $distributor_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::DISTRIBUTOR
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $this->setVarCancel($distributor_id);
            $this->set(array(
                'distributor_services' => $distributor_services,
                'services_types' => $services_types,
                'distributor' => $distributor
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create DistributorLabel.
     */
    public function add_label($distributor_id)
    {
        $config = CakeSession::read('Auth.User.Config');

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR, ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $config[ConstantsConfig::LABEL]
        ) {
            $distributor_labels = $this->DistributorLabel->findAllByDistributorId($distributor_id);
            $label_types = $this->LabelType->find('list');

            if (!$this->request->is('get')) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Distributor->table,
                    'Label.Label',
                    $user['User']['id'],
                    $distributor_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::DISTRIBUTOR
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $this->setVarCancel($distributor_id);
            $this->set(array(
                'distributor_labels' => $distributor_labels,
                'label_types' => $label_types,
                'distributor' => $distributor
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create DistributorSoftware.
     */
    public function add_software($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $distributorTradingGroup = $this->TradingGroup->findById($distributor['Distributor']['trading_group_id']);

            $distributor_software = $this->custom_pagination(
                $this->Distributor->_query('software'),
                array('DistributorSoftware.distributor_id' => $distributor_id),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Distributor',
                null,
                'PaginatorOrderCustom'
            );
            $software_types = $this->SoftwareType->search_list($distributorTradingGroup['TradingGroup']['aag_region_id']);
            $suppliers = $this->Supplier->search_list();
            $software_manufactures = $this->SoftwareManufacture->search_list();

            if (!$this->request->is('get')) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Distributor->table,
                    'Distributor.Software',
                    $user['User']['id'],
                    $distributor_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::DISTRIBUTOR
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $this->setVarCancel($distributor_id);
            $this->set(array(
                'distributor' => $distributor,
                'distributor_id' => $distributor_id,
                'distributor_software' => $distributor_software,
                'software_types' => $software_types,
                'suppliers' => $suppliers,
                'software_manufactures' => $software_manufactures,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create DistributorContract.
     */
    public function add_contract($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $distributor_contracts = $this->custom_pagination(
                $this->Distributor->_query('contracts'),
                array('DistributorContract.distributor_id' => $distributor_id),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Distributor',
                null,
                'PaginatorOrderCustom'
            );
            $trading_groups = $this->TradingGroup->find('list');
            $leaving_reasons = $this->LeavingReasonType->getList();
            $networks_distributors = $this->DistributorNetwork->getListDistributorNetwork();

            if (!$this->request->is('get')) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Distributor->table,
                    'Distributor.Contracts',
                    $user['User']['id'],
                    $distributor_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::DISTRIBUTOR
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $this->setVarCancel($distributor_id);
            $this->set(array(
                'distributor_contracts' => $distributor_contracts,
                'networks_distributors' => $networks_distributors,
                'distributor' => $distributor,
                'trading_groups' => $trading_groups,
                'leaving_reasons' => $leaving_reasons,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add DistributorContactBdm page.
     */
    public function add_contacts_bdm($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            $positions = $this->Position->search_list();

            $positions_list = $this->Position->search_list_bdm_roles(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::AAG_MANAGER, ConstantsRoles::GPC_LOGISTICS_BDM));

            if (isset($searcher['active']) && $searcher['active'] == ConstantsBooleans::NO_ACTIVE) {
                $conditions = $this->Contact->conditions($searcher);
                $check_condition = true;
                foreach ($conditions as $condition) {
                    if (isset($condition['Contact']['position_id'])) {
                        $check_condition = false;
                    }
                }
                if ($check_condition) {
                    $positions_tmp = $this->Position->getPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::AAG_MANAGER, ConstantsRoles::GPC_LOGISTICS_BDM));
                    $conditions[] = array('Contact.position_id' => Hash::extract($positions_tmp, '{n}.Position.id'));
                }
                $contacts = $this->custom_pagination(
                    array(),
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Contact',
                    null,
                    'PaginatorOrderCustom'
                );
                $is_checked_my_contacts = false;
            } else {
                $conditions = $this->Contact->conditions($searcher);
                $conditions[] = array('DistributorContactBdm.distributor_id' => $distributor_id);
                foreach ($conditions as $condition) {
                    if (!isset($condition['Contact']['position_id'])) {
                        $positions_tmp = $this->Position->getPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::AAG_MANAGER, ConstantsRoles::GPC_LOGISTICS_BDM));
                        $conditions[] = array('Contact.position_id' => Hash::extract($positions_tmp, '{n}.Position.id'));
                    }
                }
                $contacts = $this->custom_pagination(
                    $this->Contact->_query('my_contacts_bdm_distributors'),
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Contact',
                    null,
                    'PaginatorOrderCustom'
                );
                $is_checked_my_contacts = true;
            }

            $distributor_contacts = $this->DistributorContactBdm->find('all', array(
                'conditions' => array(
                    'distributor_id' => $distributor_id
                )
            ));

            if (!$this->request->is('get')) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Distributor->table,
                    'Contact.BDM',
                    $user['User']['id'],
                    $distributor_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::DISTRIBUTOR
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $this->set(array(
                'contacts' => $contacts,
                'distributor_contacts' => $distributor_contacts,
                'distributor_id' => $distributor_id,
                'distributor' => $distributor,
                'positions' => $positions,
                'positions_list' => $positions_list,
                'is_checked_my_contacts' => $is_checked_my_contacts
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add DistributorContactStaff page.
     */
    public function add_contacts_staff($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
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
                    'Contact'
                );
                $is_checked_my_contacts = false;
            } else {
                $tmp = $this->Contact->_query('my_contacts_staff_distributors');
                $conditions = $this->Contact->conditions($searcher);
                $conditions[] = array('DistributorContactStaff.distributor_id' => $distributor_id);
                $contacts = $this->custom_pagination(
                    $tmp,
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Contact'
                );
                $is_checked_my_contacts = true;
            }

            $distributor_contacts = $this->DistributorContactStaff->find('all', array(
                'conditions' => array(
                    'distributor_id' => $distributor_id
                )
            ));

            if (!$this->request->is('get')) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Distributor->table,
                    'Contact.Staff',
                    $user['User']['id'],
                    $distributor_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::DISTRIBUTOR
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $this->set(array(
                'contacts' => $contacts,
                'distributor_contacts' => $distributor_contacts,
                'distributor_id' => $distributor_id,
                'distributor' => $distributor,
                'positions' => $positions,
                'positions_list' => $positions_list,
                'is_checked_my_contacts' => $is_checked_my_contacts
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add DistributorContactGeneralBranchManager page.
     */
    public function add_contacts_general_branch_manager($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $positions = $this->Position->search_list();
            $positions_list = $this->Position->search_list_general_branch_manager_distributor();

            if (isset($searcher['active']) && $searcher['active'] == ConstantsBooleans::NO_ACTIVE) {
                $tmp = $this->Contact->_query('search_general_branch_manager_distributor');
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
                $tmp = $this->Contact->_query('my_contacts_general_branch_manager_distributors');
                $conditions = $this->Contact->conditions($searcher);
                $conditions[] = array('DistributorContactGeneralBranchManager.distributor_id' => $distributor_id);
                $contacts = $this->custom_pagination(
                    $tmp,
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Contact',
                    null,
                    'PaginatorOrderCustom'
                );
                $is_checked_my_contacts = true;
            }

            $distributor_contacts = $this->DistributorContactGeneralBranchManager->find('all', array(
                'conditions' => array(
                    'distributor_id' => $distributor_id
                )
            ));

            if (!$this->request->is('get')) {
                $user = $this->Session->read('Auth');
                $result = $this->RequestedChange->create_request_edit_description(
                    $this->Distributor->table,
                    'Contact.General_branch_manager',
                    $user['User']['id'],
                    $distributor_id,
                    $this->request->data['change_description'],
                    ConstantsLogType::DISTRIBUTOR
                );
                if ($result) {
                    $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                    $this->redirect($this->here);
                } else {
                    $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                }
            }

            $this->set(array(
                'contacts' => $contacts,
                'distributor_contacts' => $distributor_contacts,
                'distributor_id' => $distributor_id,
                'distributor' => $distributor,
                'positions' => $positions,
                'positions_list' => $positions_list,
                'is_checked_my_contacts' => $is_checked_my_contacts
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add distributor contacts page.
     */
    public function distributor_add_contacts($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $positions = $this->Position->search_list();
            $positions_list = $this->Position->search_list_general_branch_manager_distributor();

            if (isset($searcher['active']) && $searcher['active'] == ConstantsBooleans::NO_ACTIVE) {
                $tmp = $this->Contact->_query('search_general_branch_manager_distributor');
                $conditions = $this->Contact->conditions($searcher);
                $contacts = $this->custom_pagination(
                    $tmp,
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Contact'
                );
            } else {
                $tmp = $this->Contact->_query('my_contacts_general_branch_manager_distributors');
                $conditions = $this->Contact->conditions($searcher);
                $conditions[] = array('DistributorContactGeneralBranchManager.distributor_id' => $distributor_id);
                $contacts = $this->custom_pagination(
                    $tmp,
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Contact'
                );
            }

            $distributor_contacts = $this->DistributorContactGeneralBranchManager->find('all', array(
                'conditions' => array(
                    'distributor_id' => $distributor_id
                )
            ));

            $this->set(array(
                'contacts' => $contacts,
                'distributor_contacts' => $distributor_contacts,
                'distributor_id' => $distributor_id,
                'distributor' => $distributor,
                'positions' => $positions,
                'positions_list' => $positions_list,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add DistributorComment page.
     */
    public function add_comments($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $search_comments = $this->DistributorComment->_query('Search');
            $conditions_comments = array(
                'DistributorComment.distributor_id' => $distributor_id
            );
            $distributor_comments = $this->custom_pagination(
                $search_comments,
                $conditions_comments,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'DistributorComment',
                null,
                'PaginatorOrderCustom'
            );

            $this->setVarCancel($distributor_id);
            $this->set(array(
                'distributor' => $distributor,
                'distributor_comments' => $distributor_comments,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarCancel($distributor_id)
    {
        $cancel_action = array(
            'url_cancel' => array(
                'controller' => 'distributors',
                'action' => 'view',
                $distributor_id
            ),
        );

        $regions = $this->Region->region_list();

        $this->set(array(
            'distributor_id' => $distributor_id,
            'cancel_action' => $cancel_action,
            'regions' => $regions,
        ));
    }

    /**
     * Location my branches.
     */
    public function location_my_branches($distributor_id)
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if ($roleId == ConstantsRoles::DISTRIBUTOR) {
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $conditions = $this->Distributor->conditions($searcher);
            $conditions[] = $this->User->viewUserMyBranches($this->Acceso->user());

            $distributors = $this->Distributor->find('all', array(
                'conditions' => $conditions,
                'fields' => array(
                    'id',
                    'name',
                    'latitude',
                    'longitude'
                )
            ));

            $this->autoRender = false;
            $this->layout = null;
            return json_encode($distributors);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get distributor events.
     */
    public function ajax_events_list($distributor_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId, 'id');

        if (
            $distributor &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
                )
            )
        ) {
            $events = $this->Distributor->getCalendarEvents($distributor_id);
            echo json_encode($events);
            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get distributors.
     * Used for dynamic selects.
     */
    public function ajax_get_distributors()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $this->params['named'] = array('page' => $this->request->query['page']);
        $query_tmp = $this->Distributor->_query('ajax_distributors');

        $conditions[] = array('Distributor.name LIKE' => '%' . $this->request->query['search'] . '%');
        $conditions[] = array('Distributor.status' => ConstantsDistributorStatus::ACTIVE);
        $conditions[] = array('Distributor.aag_region_id' => $aagRegionId);

        if (!empty($this->request->query['distributor_filter_bdm'])) {
            $query_tmp['joins'] = array(
                array(
                    'alias' => 'DistributorContactBdm',
                    'table' => 'distributors_contacts_bdm',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = DistributorContactBdm.distributor_id'
                    ),
                )
            );
            $conditions[] = array('DistributorContactBdm.contact_id' => $this->request->query['distributor_filter_bdm']);
        }

        if (!empty($this->request->query['distributor_filter_rsm'])) {
            $contacts = array();
            $contacts_tmp = array();
            if (is_array($this->request->query['distributor_filter_rsm'])) {
                $contacts = $this->request->query['distributor_filter_rsm'];
                foreach ($this->request->query['distributor_filter_rsm'] as $id) {
                    $contacts_array_tmp = $this->Contact->find('list', array('conditions' => array('Contact.id' => $id)));
                    $contacts_tmp += $contacts_array_tmp;
                }
            } else {
                $contacts[] = $this->request->query['distributor_filter_rsm'];
                $contacts_tmp = $this->Contact->find('list', array('conditions' => array('Contact.id' => $this->request->query['distributor_filter_rsm'])));
            }
            foreach ($contacts_tmp as $contact_tmp_id => $contact_tmp) {
                $contacts[] = $contact_tmp_id;
            }
            $distributors = $this->Distributor->getDistributorsByContacts($contacts);
            $flag_condition = true;
            foreach ($conditions as $key => $condition) {
                if (isset($condition['Distributor.id'])) {
                    $conditions[$key]['Distributor.id'] = Hash::extract($distributors, '{n}');
                    $flag_condition = false;
                }
            }
            if ($flag_condition) {
                $conditions[]['Distributor.id'] = Hash::extract($distributors, '{n}');
            }
        }

        if (!empty($this->request->query['trading_group_id'])) {
            $conditions[] = array('Distributor.trading_group_id' => $this->request->query['trading_group_id']);
        }

        if (!empty($this->request->query['activity_id'])) {
            $query_tmp['joins'] = array(
                array(
                    'alias' => 'DistributorDistributorActivity',
                    'table' => 'distributors_distributors_activities',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorDistributorActivity.distributor_id = Distributor.id',
                    ),
                ),
            );
            $conditions[] = array('DistributorDistributorActivity.distributor_activity_id' => $this->request->query['activity_id']);
        }

        $distributors = $this->custom_pagination(
            $query_tmp,
            $conditions,
            ConstantsPagination::SIZE_PAGE_SMALL
        );

        $this->autoRender = null;
        return json_encode($distributors);
    }

    /**
     * AJAX get distributor info for Distributors/Networks garage tabs.
     */
    public function ajax_get_info_distributor()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor_id = $this->request->data['distributor_id'];
        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES)
        ) {
            $distributor = $this->Distributor->findById($distributor_id);

            $js_array = json_encode($distributor);
            echo $js_array;

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search user distributor permissions.
     */
    public function ajax_search_users_distributor_permissions()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
            )
        ) {
            $search = $this->request->query;
            $data = $this->request->data;

            $contacts_tmp = $this->whoViewThisDistributor($data['distributor_id'], $search['permission_id']);
            $query = $this->User->_query('getByContactId');
            $conditions['User.contact_id'] = $contacts_tmp;
            $query['conditions'] = $conditions;
            $users_distributor_permissions = $this->User->find('all', $query);

            $positions = $this->Position->search_list();
            $roles = $this->Role->search_list();
            $distributors_permissions = $this->Permission->getByGroupingPermission(ConstantsGroupingPermissions::DISTRIBUTOR);

            $this->set(array(
                'users_distributor_permissions' => $users_distributor_permissions,
                'distributors_permissions' => $distributors_permissions,
                'positions' => $positions,
                'roles' => $roles
            ));

            $this->layout = null;
            $this->render('/Distributors/Elements/distributors_permissions_search');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function whoViewThisDistributor($distributor_id, $permission_id)
    {
        $distributor = $this->Distributor->findById($distributor_id);
        $group_permissions = $this->GroupPermissionPermission->getAllGroupPermissionsListByPermissionId($permission_id);

        $positions = $this->PositionConfig->getPositionsConfigToTradingGroup($group_permissions, $distributor['Distributor']['trading_group_id']);

        $contacts = $this->Contact->getContactListByPositionId($positions);

        return $contacts;
    }

    /**
     * Tracking distributor.
     */
    public function tracking_distributor($distributor_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            (
                in_array($roleId, array(ConstantsRoles::GPC_LOGISTICS_DIRECTOR, ConstantsRoles::GPC_LOGISTICS_RM)) ||
                (
                    $roleId == ConstantsRoles::TG_DIRECTOR &&
                    CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::TRADING_GROUP_DIRECTOR_ID
                )
            )
        ) {
            $url = Router::url(array(
                'controller' => 'distributors',
                'action' => 'tracking_distributor'
            ));

            CakeSession::write('Auth.User.appointment_url', $url); // T001 SECURITY - It is not changed

            $distributor = $this->Distributor->findById($distributor_id);
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
                ConstantsPagination::SIZE_PAGE_SMALL,
                $this->Appointment
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
                'appointments_visit_types' => $appointments_visit_types
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get distributors name.
     * Used for dynamic selects.
     */
    public function get_distributors_name()
    {
        $this->verify_ajax($this->request);

        $this->layout = false;
        $this->autoRender = false;

        $distributors_name = $this->Distributor->obtenerPosiblesDistributorsAjax($this->request->query);

        $list_distributors = array();
        foreach ($distributors_name as $key => $distributor) {
            $list_distributors[] = array(
                'id' => $key,
                'text' => $distributor,
            );
        }

        $list_distributors_completa['items'] = $list_distributors;

        return json_encode($list_distributors_completa);
    }

    /**
     * AJAX get distributor name by AAG region.
     * Used for dynamic selects.
     */
    public function get_distributors_name_region()
    {
        $this->verify_ajax($this->request);

        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $distributors_name = $this->Distributor->obtenerPosiblesDistributorsAjaxRegion($this->request->query, $aagRegionId);

        $list_distributors = array();
        foreach ($distributors_name as $key => $distributor) {
            $list_distributors[] = array(
                'id' => $key,
                'text' => $distributor,
            );
        }

        $list_distributors_completa['items'] = $list_distributors;
        return json_encode($list_distributors_completa);
    }

    /**
     * AJAX get distributor name by AAG region.
     * Used for dynamic selects.
     */
    public function get_distributors_name_region_dynamic()
    {
        $this->verify_ajax($this->request);

        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];
        $distributors_name = $this->Distributor->obtenerPosiblesDistributorsAjaxRegionDynamic($this->request->query, $aagRegionId);

        $list_distributors = array();
        foreach ($distributors_name as $key => $distributor) {
            $list_distributors[] = array(
                'id' => $key,
                'text' => $distributor,
            );
        }

        $list_distributors_completa['items'] = $list_distributors;
        return json_encode($list_distributors_completa);
    }

    /**
     * AJAX DistributorObjective search.
     */
    public function search_distributors_objectives()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (in_array($roleId, array(ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))) {
            $this->layout = $this->autoRender = false;

            if (isset($this->request->data) && !empty($this->request->data)) {
                $distributors_name = $this->Distributor->obtenerPosiblesDistributorsObjectivesAjaxRegion($this->request->data, $aagRegionId);

                $distributors_list = array();
                foreach ($distributors_name as $distributor) {
                    $id = $distributor['Distributor']['id'];
                    $name = $distributor['Distributor']['name'];
                    $account_number = $distributor['Distributor']['account_number'];
                    $town = $distributor['Distributor']['town'];

                    $distributors_list[$id] = $name . ' - ' . $account_number . ' - ' . $town;
                }

                $result = json_encode($distributors_list);
            } else {
                $result = 'all_dis';
            }

            return $result;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get conferences count.
     */
    public function ajax_count_distributors()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
            ) ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $this->layout = $this->autoRender = false;

            if (!$this->request->is('get')) {
                $searcher = $this->request->data;
                $this->request->data['Search'] = $searcher;

                $conditions = $this->Distributor->conditions($searcher);
                $conditions[] = $this->User->viewUserDistributors($this->Acceso->user());

                $optional_join = null;

                if (!empty($searcher['search_my_customers'])) {
                    $optional_join = array(
                        'table' => 'distributors_contacts_bdm',
                        'alias' => 'DistributorContactBdm',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorContactBdm.distributor_id = Distributor.id'
                        ),
                        'fields' => 'DistributorContactBdm.*',
                    );
                }

                $query_type = ConstantsQueryTypes::COUNT;

                $countDistributors = $this->Distributor->dynamicTypeDistributorsExportQuery($query_type, $aagRegionId, $conditions, $optional_join);
                $countDistributors = $countDistributors ? $countDistributors : 0;
            }
            return json_encode($countDistributors);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX CSV distributors generation.
     */
    public function ajax_get_csv_data()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
            ) ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            ini_set('memory_limit', '-1');
            set_time_limit(18000);

            $this->layout = $this->autoRender = false;
            $email = $this->request->data['contact-email'];

            if (!$this->request->is('get')) {
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

                $conditions = $this->Distributor->conditions($searcher);
                $conditions[] = $this->User->viewUserDistributors($this->Acceso->user());

                $optional_join = null;

                if (!empty($searcher['search_my_customers'])) {
                    $optional_join = array(
                        'table' => 'distributors_contacts_bdm',
                        'alias' => 'DistributorContactBdm',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorContactBdm.distributor_id = Distributor.id'
                        ),
                        'fields' => 'DistributorContactBdm.*',
                    );
                }

                $query_type = ConstantsQueryTypes::ALL;
                $distributors = $this->Distributor->dynamicTypeDistributorsExportQuery($query_type, $aagRegionId, $conditions, $optional_join);
                $this->generateCsv($distributors, $email, $user);
            }
            exit;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Generate CSV.
     */
    private function generateCsv($data, $email, $user)
    {
        $languageCode = $user['language_code'];
        $userAagRegionId = $user['aag_region_id'];

        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $filename = 'Distributors' . Fecha::getCompleteDate() . '.csv';
        $fileFullName = ConstantsPath::DIR_CSV_EXPORT_FILES_ABSOLUTE . DS . $filename;

        $controller = $this->params['controller'];

        $file = fopen($fileFullName, 'a');

        $table = array();
        $table_tmp = array(
            __t('Distributor.Account_number', $languageCode),
            __t('Appointment.Customer', $languageCode),
        );
        $table = array_merge($table, $table_tmp);

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $table_tmp = array(
                __t('Distributor.MAMID', $languageCode)
            );
            $table = array_merge($table, $table_tmp);
        }

        $table_tmp = array(
            __t('Distributor.Head_office', $languageCode),
            __t('Config.Tg', $languageCode),
            __t('Distributor.Association', $languageCode),
            __t('Distributor.Postcode', $languageCode),
            __t('Distributor.Town', $languageCode),
            __t('Distributor.Phone', $languageCode),
            __t('Visit.Last_visit', $languageCode),
        );
        $table = array_merge($table, $table_tmp);

        fputcsv($file, $table);
        foreach ($data as $distributor) {
            $distributor_row = array();
            $distributor_row[] = $distributor['Distributor']['account_number'];
            $distributor_row[] = $distributor['Distributor']['name'];

            if ($userAagRegionId == ConstantsAAGRegionId::UK) {
                $distributor_row[] = $distributor['Distributor']['MAMID'];
            }

            $distributor_row[] = ($distributor['Distributor']['head_office']) ? __t('General.Yes') : __t('General.No');
            $distributor_row[] = $distributor['TradingGroup']['name'];
            $distributor_row[] = $distributor['AssociationType']['name' . __s()];
            $distributor_row[] = $distributor['Distributor']['postcode'];
            $distributor_row[] = $distributor['Distributor']['town'];
            $distributor_row[] = $distributor['Distributor']['phone'];
            $distributor_row[] = Fecha::toFormatoVista($distributor['Distributor']['last_visit']);
            fputcsv($file, $distributor_row);
        }
        fclose($file);

        $this->generateEmail($filename, $fileFullName, $user, $email, $controller);
    }

    /**
     * AJAX search distributors garages associated.
     */
    public function ajax_search_distributors_garages_associated($distributor_id, $type_param = null)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
            ) ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $networks_statuses = Configure::read('Network_Status');
            foreach ($networks_statuses as $key => $network_status) {
                $networks_statuses[$key] = __t($network_status);
            }
            $network_list = $this->Network->getListByRegion($aagRegionId);
            $distributor = $this->Distributor->findById($distributor_id);

            $searcher = $this->request->query;
            $searcher['aag_region_id'] = $aagRegionId;
            $conditions = array(
                'GarageDistributor.distributor_id' => $distributor_id,
                'Garage.aag_region_id' => $aagRegionId,
            );
            $conditions_network = array();
            $conditions_status = array();

            if (!empty($searcher['network_id'])) {
                $conditions_network = array(
                    'Network.id IN (' . $searcher['network_id'] . ') '
                );
            };
            if (!empty($searcher['status'])) {
                $conditions_status = array(
                    'GarageNetwork.status IN (' . $searcher['status'] . ') '
                );
            }
            $conditions = array_merge($conditions_network, $conditions_status, $conditions);
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
                'PaginatorUrlDistributorGaragesAssociated',
            );

            $count_distributor_garages = $this->GarageDistributor->getPaginationCountGaragesAssociated($conditions);

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
                        $conditions_network,
                        $conditions_status,
                    ),
                );

                $distributor_garages[$key]['GarageNetwork'] = $this->GarageNetwork->find('all', $query);
            }

            $this->setVarForm();
            $this->set(array(
                'distributor' => $distributor,
                'distributor_garages' => $distributor_garages,
                'networks_statuses' => $networks_statuses,
                'network_list' => $network_list,
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
