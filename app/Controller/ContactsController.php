<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class ContactsController extends AppController
{
    public $uses = array(
        'Contact',
        'ContactRegion',
        'Email',
        'Garage',
        'GroupPermissionUser',
        'GroupPermission',
        'GroupPermissionPermission',
        'GarageContactGeneralBranchManager',
        'DistributorContactGeneralBranchManager',
        'PermissionUser',
        'Position',
        'Region',
        'AagRegion',
        'LogChange',
        'LogTable',
        'User',
        'UserImage',
        'UserPreference',
        'UserRecoverPassword',
        'Network',
        'DistributorNetwork',
        'DistributorNetworkContactBdm',
        'NetworkContactBdm',
        'LogisticCenter',
        'PositionConfig',
        'GarageContactStaff',
        'Country',
        'Distributor',
        'Role'
    );

    /**
     * Contacts home page.
     */
    public function home()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_CONTACT) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
            )
        ) {
            $positions = $this->Contact->Position->search_list();
            $logisticCenters = $this->LogisticCenter->getList();

            $positionsList = $this->Contact->Position->getlist();
            $aagRegions = $this->AagRegion->region_list();
            $optPositions = $this->optPositions($positionsList);
            $regions = $this->Region->region_list();

            $searcher = $this->request->query;
            if (!isset($searcher['aag_region_id'])) {
                $searcher['aag_region_id'] = $aagRegionId;
            }
            $this->request->data['Search'] = $searcher;

            $conditions = $this->Contact->conditions($searcher);
            $contacts = $this->custom_pagination(
                array('order' => array(
                    'Contact.first_name' => 'asc',
                    'Contact.last_name' => 'asc'
                )),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Contact',
                null,
                'PaginatorOrderCustom'
            );

            foreach ($contacts as $key => $contact) {
                $contactRegionsTmp = $this->ContactRegion->findAllByContactId($contact['Contact']['id']);
                $contacts[$key]['Contact']['regions'] = Hash::extract($contactRegionsTmp, '{n}.ContactRegion.region_id');
            }

            $this->set(
                array(
                    'contacts' => $contacts,
                    'positions' => $positions,
                    'opt_positions' => $optPositions,
                    'regions' => $regions,
                    'logistic_centers' => $logisticCenters,
                    'aag_regions' => $aagRegions,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Contacts directory page.
     */
    public function directory()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_DIRECTORY) &&
                $roleId != ConstantsRoles::GARAGE
            )
        ) {
            $garageId = $user['garage_id'];
            $distributorId = $user['distributor_id'];

            $roles = Configure::read('Roles_Directory');
            $positions = $this->Contact->Position->search_list();

            $searcher = $this->request->query;
            if (!isset($searcher['aag_region_id'])) {
                $searcher['aag_region_id'] = $aagRegionId;
            }
            $this->request->data['Search'] = $searcher;

            $conditions = $this->Contact->conditions($searcher);
            $positionsList = $this->Contact->Position->getListDirectory($roles);
            $positionsListSearch = $this->Contact->Position->getListSearchDirectory($roles);

            if ($garageId) {
                $groupPermission = $this->GroupPermissionPermission->getAllGroupPermissionsListByPermissionId(ConstantsPermissionsGrouping::VIEW_GARAGE);
                $positionsConfig1 = $this->PositionConfig->getPositionsConfigByGroupPermissionsAllNetworks($groupPermission);
                $positionsConfig2 = $this->PositionConfig->getPositionsConfigByGroupPermissionsByNetwork($groupPermission, CakeSession::read('Auth.User.current_network'));
                $positionsConfig = array_merge($positionsConfig1, $positionsConfig2);
                $positionsConfig = array_unique($positionsConfig);

                foreach ($positionsList as $key => $positionList) {
                    if (!in_array($positionList, $positionsConfig)) {
                        unset($positionsList[$key]);
                    }
                }

                foreach ($positionsListSearch as $key => $positionList) {
                    if (!in_array($key, $positionsConfig)) {
                        unset($positionsListSearch[$key]);
                    }
                }
            } elseif ($distributorId) {
                $groupPermission = $this->GroupPermissionPermission->getAllGroupPermissionsListByPermissionId(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR);
                $positionsConfig1 = $this->PositionConfig->getPositionsConfigByGroupPermissionsAllTraidingGroup($groupPermission);
                $positionsConfig2 = $this->PositionConfig->getPositionsConfigByGroupPermissionsByTraidingGroup($groupPermission, CakeSession::read('Auth.User.trading_group'));
                $positionsConfig = array_merge($positionsConfig1, $positionsConfig2);
                $positionsConfig = array_unique($positionsConfig);

                foreach ($positionsList as $key => $positionList) {
                    if (!in_array($positionList, $positionsConfig)) {
                        unset($positionsList[$key]);
                    }
                }

                foreach ($positionsListSearch as $key => $positionList) {
                    if (!in_array($key, $positionsConfig)) {
                        unset($positionsListSearch[$key]);
                    }
                }
            }

            $query = $this->Contact->getQueryContactsDataByPositionId($positionsList);
            $contacts = $this->custom_pagination(
                $query,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL
            );

            $this->set(
                array(
                    'positions' => $positions,
                    'positions_list' => $positionsList,
                    'position' => null,
                    'contacts' => $contacts,
                    'positions_list_search' => $positionsListSearch
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX load contacts for directory.
     */
    public function ajax_load_contacts()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_DIRECTORY) &&
                $roleId != ConstantsRoles::GARAGE
            )
        ) {
            $conditionsSearcher = $this->request->data['searcher'];
            $garageId = $user['garage_id'];

            if ($this->request->data['position_id'] == 0) {
                $roles = Configure::read('Roles_Directory');
                $positions = $this->Contact->Position->search_list();
                $positionsList = $this->Contact->Position->getListDirectory($roles);

                if ($garageId) {
                    $groupPermission = $this->GroupPermissionPermission->getAllGroupPermissionsListByPermissionId(ConstantsPermissionsGrouping::VIEW_GARAGE);
                    $positionsConfig1 = $this->PositionConfig->getPositionsConfigByGroupPermissionsAllNetworks($groupPermission);
                    $positionsConfig2 = $this->PositionConfig->getPositionsConfigByGroupPermissionsByNetwork($groupPermission, CakeSession::read('Auth.User.current_network'));
                    $positionsConfig = array_merge($positionsConfig1, $positionsConfig2);
                    $positionsConfig = array_unique($positionsConfig);

                    foreach ($positionsList as $key => $positionList) {
                        if (!in_array($positionList, $positionsConfig)) {
                            unset($positionsList[$key]);
                        }
                    }
                } elseif (CakeSession::read('Auth.User.distributor_id')) {
                    $groupPermission = $this->GroupPermissionPermission->getAllGroupPermissionsListByPermissionId(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR);
                    $positionsConfig1 = $this->PositionConfig->getPositionsConfigByGroupPermissionsAllTraidingGroup($groupPermission);
                    $positionsConfig2 = $this->PositionConfig->getPositionsConfigByGroupPermissionsByTraidingGroup($groupPermission, CakeSession::read('Auth.User.trading_group'));
                    $positionsConfig = array_merge($positionsConfig1, $positionsConfig2);
                    $positionsConfig = array_unique($positionsConfig);

                    foreach ($positionsList as $key => $positionList) {
                        if (!in_array($positionList, $positionsConfig)) {
                            unset($positionsList[$key]);
                        }
                    }
                }
                $contacts = $this->Contact->getContactsDataByPositionId($positionsList, $conditionsSearcher);
                $position = null;
            } elseif ($this->request->data['position_id'] != 0) {
                $position_id = $this->request->data['position_id'];
                $positions = $this->Contact->Position->search_list();
                $position = $this->Position->findById($position_id);
                $contacts = $this->Contact->getContactsDataByPositionId($position_id, $conditionsSearcher);
            }

            $this->set(array(
                'position' => $position,
                'positions' => $positions,
                'contacts' => $contacts,
            ));

            $this->layout = null;
            $this->render('../Contacts/Elements/ajax_load_contacts');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Contact logs.
     */
    public function logs_contact()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::ADMIN &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_CONTACT) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
        ) {
            $positionTable = $this->LogTable->findByName($this->Contact->table);

            $tmp = $this->LogChange->_query('search_table_position');
            $tmp['conditions'] = array(
                'LogChange.table_id' => $positionTable['LogTable']['id'],
            );
            $tmp['order'] = array(
                'LogChange.date' => 'desc'
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $conditions = $this->LogChange->conditions($searcher);

            $logsChangesPositionContact = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'LogChange',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(
                array(
                    'logs_changes_position_contact' => $logsChangesPositionContact
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Contact GroupPermissions logs.
     */
    public function log_group_permissions()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::ADMIN &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_CONTACT) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
        ) {
            $groupPermissionTable = $this->LogTable->findByName($this->GroupPermission->table);
            $groupPermissionPermissionTable = $this->LogTable->findByName($this->GroupPermissionPermission->table);

            $tmp = $this->LogChange->_query('search_table_permission');
            $tmp['conditions'] = array(
                'LogChange.table_id' => array(
                    $groupPermissionTable['LogTable']['id'],
                    $groupPermissionPermissionTable['LogTable']['id']
                )
            );

            $tmp['order'] = array(
                'LogChange.date' => 'desc'
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $conditions = $this->LogChange->conditions($searcher);

            $logsChangesPermissionPosition = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'LogChange',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(
                array(
                    'logs_changes_permission_position' => $logsChangesPermissionPosition
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Contact Positions logs.
     */
    public function log_positions()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::ADMIN &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_CONTACT) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
        ) {
            $positionTable = $this->LogTable->findByName($this->Position->table);

            $tmp = $this->LogChange->_query('search_table_group_permission');
            $tmp['conditions'] = array(
                'LogChange.table_id' => $positionTable['LogTable']['id'],
            );

            $tmp['order'] = array(
                'LogChange.date' => 'desc'
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $conditions = $this->LogChange->conditions($searcher);

            $logsChangesPermissionGroupPermission = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'LogChange',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(
                array(
                    'logs_changes_permission_group_permission' => $logsChangesPermissionGroupPermission
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Contact view page.
     */
    public function view($contact_id = null)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $contact = $this->Contact->findByIdAndAagRegionId($contact_id, $aagRegionId);
        if (
            $contact &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_CONTACT) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
                )
            )
        ) {
            $positions = $this->Contact->Position->search_list();
            $position = $this->Contact->Position->findById($contact['Contact']['position_id']);

            $roles = $this->Role->search_list();
            $garage_networks = $this->NetworkContactBdm->findAllByContact($contact_id);
            $contact['garage_networks'] = $garage_networks;
            $distributor_networks = $this->DistributorNetworkContactBdm->findAllByContact($contact_id);
            $contact['distributor_networks'] = $distributor_networks;
            $networks_images = $this->Network->find('list', array('fields' => 'image'));
            $networks_list = $this->Network->find('list', array('fields' => 'id,name'));
            $distributor_networks_images = $this->DistributorNetwork->find('list', array('fields' => 'image'));
            $distributor_networks_list = $this->DistributorNetwork->find('list', array('fields' => 'id,name'));
            $users = $this->User->getUsersByContact($contact['Contact']['id']);
            $languages = $this->User->Language->getLanguagesIdName();
            $contact_regions = $this->ContactRegion->findAllByContactId($contact_id);
            $regions = $this->Region->find('list', array('fields' => 'id,name'));

            $this->setVarForm();
            $this->set(array(
                'contact' => $contact,
                'regions' => $regions,
                'contact_regions' => $contact_regions,
                'users' => $users,
                'roles' => $roles,
                'positions' => $positions,
                'position' => $position,
                'languages' => $languages,
                'networks_images' => $networks_images,
                'distributor_networks_images' => $distributor_networks_images,
                'networks_list' => $networks_list,
                'distributor_networks_list' => $distributor_networks_list,
                'garage_name' => $contact['Contact']['garage_id'] ? $this->Garage->findById($contact['Contact']['garage_id']) : null,
                'distributor_name' => $contact['Contact']['distributor_id'] ? $this->Distributor->findById($contact['Contact']['distributor_id']) : null,
                'logistic_name' => $contact['Contact']['logistic_center_id'] ? $this->LogisticCenter->findById($contact['Contact']['logistic_center_id']) : null,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create contact.
     */
    public function add($controller, $action, $id = null)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
            )
        ) {
            $networks = $this->Network->find('list', array(
                'conditions' => array(
                    'Network.aag_region_id' => $aagRegionId
                ),
                'order' => 'Network.name'
            ));

            $distributors_networks = $this->DistributorNetwork->find('list', array('order' => 'DistributorNetwork.name'));

            $positions_list_bdm = json_encode($this->Position->findListByRoleId(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM)));
            $option_garage = array();
            $option_distributor = array();
            $garages_list = array();
            $distributors_list = array();

            if ($action == 'add_contacts_bdm') {
                $positions = $this->Contact->Position->search_list_bdm_roles(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::AAG_MANAGER));
            } elseif ($action == 'add_contacts_staff' && $controller == 'garages') {
                $positionsList = $this->Contact->Position->get_list_staff_garage_manager();
                $positions = $this->optPositions($positionsList);
                $option_garage_tmp = $this->Garage->getGarageNameById($id, $aagRegionId);

                if (!$option_garage_tmp) {
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                }

                $option_garage[$id] = $option_garage_tmp[0]['Garage']['name'];
            } elseif ($action == 'add_contacts_staff' && $controller == 'distributors') {
                $positionsList = $this->Contact->Position->get_list_staff_distributor_manager();
                $positions = $this->optPositions($positionsList);
                $option_distributor_tmp = $this->Distributor->getDistributorNameById($id, $aagRegionId);

                if (!$option_distributor_tmp) {
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                }

                $option_distributor[$id] = $option_distributor_tmp[0]['Distributor']['name'];
            } elseif ($action == 'add_contacts_general_branch_manager' && $controller == 'garages') {
                $positions = $this->Contact->Position->search_list_general_branch_manager_garage();
            } elseif ($action == 'add_contacts_general_branch_manager' && $controller == 'distributors') {
                $positions = $this->Contact->Position->search_list_general_branch_manager_distributor();
            } else {
                $positionsList = $this->Contact->Position->getlist();
                $positions = $this->optPositions($positionsList);
            }

            if (!$this->request->is('get')) {
                $last_id = $this->Contact->find(
                    'first',
                    array(
                        'order' => 'id DESC',
                        'fields' => 'id'
                    )
                );

                if (empty($this->request->data['Contact']['identification_number'])) {
                    $region_identification_number = '';
                    if (CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_UK_IRELAND')){
                        $region_identification_number = 'UK';
                    } elseif (CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_BENELUX')) {
                        $region_identification_number = 'BX';
                    }
                    $this->request->data['Contact']['identification_number'] = $region_identification_number . "-" . ($last_id['Contact']['id'] + 1);
                }

                // Save ajax selected value if forms fails
                if (!empty($this->request->data['Contact']['garage_id'])) {
                    $garage_contact = $this->Garage->findById($this->request->data['Contact']['garage_id']);
                    $garages_list = array($garage_contact['Garage']['id'] => $garage_contact['Garage']['complete_search']);
                }

                if (!empty($this->request->data['Contact']['distributor_id'])) {
                    $distributor_contact = $this->Distributor->findById($this->request->data['Contact']['distributor_id']);
                    $distributors_list = array($distributor_contact['Distributor']['id'] => $distributor_contact['Distributor']['complete_search']);
                }

                $contact = $this->Contact->new_contact($this->request->data);

                if ($contact) {
                    $contact_id = $this->Contact->getLastInsertID();
                    if (is_array($this->request->data['NetworkContactBdm']['network_id'])) {
                        foreach ($this->request->data['NetworkContactBdm']['network_id'] as $network_id) {
                            $this->NetworkContactBdm->add($network_id, $contact_id);
                        }
                    }
                    if (is_array($this->request->data['DistributorNetworkContactBdm']['distributor_network_id'])) {
                        foreach ($this->request->data['DistributorNetworkContactBdm']['distributor_network_id'] as $distributor_network_id) {
                            $this->DistributorNetworkContactBdm->add($distributor_network_id, $contact_id);
                        }
                    }
                    $no_flag = true;

                    //add region or regions

                    if (!empty($this->request->data['Contact']['region'])) {
                        $contact_region_bdm = array(
                            'contact_id' => $contact['Contact']['id'],
                            'region_id' => $this->request->data['Contact']['region'],
                        );
                        $no_flag = $this->ContactRegion->save($contact_region_bdm);
                    }

                    if (!empty($this->request->data['Contact']['regions_cv'])) {
                        $this->request->data['Contact']['regions'] = $this->request->data['Contact']['regions_cv'];
                    } elseif (!empty($this->request->data['Contact']['regions_lv'])) {
                        $this->request->data['Contact']['regions'] = $this->request->data['Contact']['regions_lv'];
                    }

                    if (isset($this->request->data['Contact']['regions'])) {
                        foreach ($this->request->data['Contact']['regions'] as $region) {
                            $contact_region_bdm = array(
                                'contact_id' => $contact['Contact']['id'],
                                'region_id' => $region,
                            );

                            $this->ContactRegion->create();
                            if (!$this->ContactRegion->save($contact_region_bdm)) {
                                $no_flag = false;
                            }
                        }
                    }

                    if ($action == 'add_contacts_bdm' && $controller == 'garages') {
                        $contact_id = $this->Contact->getLastInsertID();
                        $garage_contact_bdm = array(
                            'garage_id' => $id,
                            'contact_id' => $contact_id
                        );
                        $no_flag = $this->Contact->GarageContactBdm->new_garage_contact_bdm($garage_contact_bdm);
                    } elseif ($action == 'add_contacts_staff' && $controller == 'garages') {
                        $contact_id = $this->Contact->getLastInsertID();
                        $garage_contact_staff = array(
                            'garage_id' => $id,
                            'contact_id' => $contact_id,
                            'interest' => $this->request->data['GarageContactStaff']['interest']
                        );
                        $no_flag = $this->Contact->GarageContactStaff->new_garage_contact_staff($garage_contact_staff);
                    } elseif ($action == 'add_contacts_general_branch_manager' && $controller == 'garages') {
                        $contact_id = $this->Contact->getLastInsertID();
                        $garage_contact_general_branch_manager = array(
                            'garage_id' => $id,
                            'contact_id' => $contact_id
                        );
                        $no_flag = $this->Contact->GarageContactGeneralBranchManager->new_garage_contact_general_branch_manager($garage_contact_general_branch_manager);
                    } elseif ($action == 'add_contacts_bdm' && $controller == 'distributors') {
                        $contact_id = $this->Contact->getLastInsertID();
                        $no_flag = $this->Contact->DistributorContactBdm->addDistributorContactBdm($id, $contact_id);
                    } elseif ($action == 'add_contacts_staff' && $controller == 'distributors') {
                        $contact_id = $this->Contact->getLastInsertID();
                        $distributor_contact_staff = array(
                            'distributor_id' => $id,
                            'contact_id' => $contact_id
                        );
                        $no_flag = $this->Contact->DistributorContactStaff->new_distributor_contact_staff($distributor_contact_staff);
                    } elseif ($action == 'add_contacts_general_branch_manager' && $controller == 'distributors') {
                        $contact_id = $this->Contact->getLastInsertID();
                        $distributor_contact_general_branch_manager = array(
                            'distributor_id' => $id,
                            'contact_id' => $contact_id
                        );
                        $no_flag = $this->Contact->DistributorContactGeneralBranchManager->new_distributor_contact_general_branch_manager($distributor_contact_general_branch_manager);
                    }
                    $user = $this->Session->read('Auth');
                    $contact_new_position = array();
                    $contact_new_position['Contact']['position_id'] = $contact['Contact']['position_id'];

                    $this->LogChange->get_params_create_log_edit(
                        null,
                        $contact_new_position['Contact'],
                        $this->Contact->table,
                        $user,
                        $id,
                        ConstantsLogType::CONTACT
                    );

                    if ($no_flag) {
                        $msg = h(sprintf(__t('Contact.Well_add'), $contact['Contact']['first_name'] . " " . $contact['Contact']['last_name']));
                        $this->Session->setFlashSuccess($msg);
                        $this->redirect(
                            array(
                                'controller' => 'contacts',
                                'action' => 'edit',
                                'contacts',
                                'home',
                                $this->Contact->getLastInsertID(),
                                null,
                            )
                        );
                    } else {
                        $this->Session->setFlashError(__t('Contact.Error_new_contact'));
                    }
                } else {
                    if (isset($default_id_number)) {
                        $this->request->data['Contact']['identification_number'] = '';
                    }
                    if ($this->request->data['Contact']['position_id'] == ConstantsPositions::GARAGE_MANAGER_ID && empty($this->request->data['Contact']['garage_id'])) {
                        $this->Session->setFlashError(__t('Validation.Mandatory_to_choose_a_garage'));
                    } else {
                        $this->Session->setFlashError(__t('Contact.Error_new_contact'));
                    }
                }
            }

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => $controller,
                    'action' => $action,
                    $id
                ),
            );
            $this->setVarForm();
            $this->set(
                array(
                    'networks' => $networks,
                    'distributors_networks' => $distributors_networks,
                    'cancel_action' => $cancelAction,
                    'positions' => $positions,
                    'positions_list_bdm' => $positions_list_bdm,
                    'garage_id' => $id,
                    'option_garage' => $option_garage,
                    'option_distributor' => $option_distributor,
                    'garages_list' => $garages_list,
                    'distributors_list' => $distributors_list
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create contact and user.
     */
    public function add_contact_and_user($back, $customer_id = null)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
            )
        ) {
            if (isset($this->request->params['named']['garage_id'])) {
                $garage_id = $this->request->params['named']['garage_id'];
                $positionsList = $this->Contact->Position->get_list_staff_garage_manager();
            } elseif (is_null($customer_id)) {
                if ($roleId == ConstantsRoles::SUPER_ADMIN) {
                    $positionsList = $this->Contact->Position->getlist();
                } else {
                    $this->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER);
                    $positionsList = $this->Contact->Position->getlist();

                    foreach ($positionsList as $key => $position) {
                        if ($position['Position']['role_id'] == ConstantsRoles::SUPER_ADMIN) {
                            unset($positionsList[$key]);
                        }
                    }
                }
            } elseif (in_array($roleId, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR))) {
                if ($back == ConstantsBackContactUser::BACK_GARAGES) {
                    $positionsList = $this->Contact->Position->getPositionByRole(ConstantsRoles::GARAGE);
                } elseif ($back == ConstantsBackContactUser::BACK_DISTRIBUTORS) {
                    $positionsList = $this->Contact->Position->getPositionByRole(ConstantsRoles::DISTRIBUTOR);
                }
            } else {
                $this->Session->setFlashError(__t('Contact.Error_new_contact'));
                $this->redirect(array(
                    'controller' => 'home',
                    'action' => 'home'
                ));
            }

            $positions = $this->optPositions($positionsList);
            $positions_list_bdm = json_encode($this->Position->findListByRoleId(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM)));
            $roles = $this->User->Role->search_list();
            $positions_roles = $this->User->Role->getPositionRole();

            $networks = $this->Network->getListByRegion($aagRegionId);
            $distributors_networks = $this->DistributorNetwork->find('list', array('order' => 'DistributorNetwork.name'));

            $languages = $this->User->Language->getLanguagesCodeNameWithoutLoco();

            $option_garage = array();
            $option_distributor = array();
            $garages_list = array();
            $distributors_list = array();

            $aagRegions = $this->AagRegion->region_list();
            $user = $this->Acceso->user();
            $country_id = $user['country_id'];

            if (isset($this->request->params['named']['garage_id'])) {
                $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);

                if (!$garage) {
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                }

                if (!empty($garage['Garage']['province_id'])) {
                    $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
                } else {
                    $postcode_tmp = explode(' ', $garage['Garage']['postcode']);
                    $province = $this->Garage->getProvinceIdByPostcode($postcode_tmp[0]);
                    $country = $this->Country->get_country_by_province($province);
                }
                $country_id = $country['Country']['id'];
            } elseif (!empty($customer_id) && !isset($garage_id)) {
                $distributor = $this->Distributor->findByIdAndAagRegionId($customer_id, $aagRegionId);

                if (!$distributor) {
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                }

                if (!empty($distributor['Distributor']['province_id'])) {
                    $country = $this->Country->get_country_by_province($distributor['Distributor']['province_id']);
                } else {
                    $postcode_tmp = explode(' ', $distributor['Distributor']['postcode']);
                    $province = $this->Garage->getProvinceIdByPostcode($postcode_tmp[0]);
                    $country = $this->Country->get_country_by_province($province);
                }
                $country_id = $country['Country']['id'];
            }

            $countries = $this->Country->get_country_by_region($aagRegionId);
            $countries[ConstantsConfigSelect::ALL] = __t('General.All');

            if ($back == ConstantsBackContactUser::BACK_CONTACTS) {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'contacts',
                        'action' => 'home',
                    ),
                );
            } elseif ($back == ConstantsBackContactUser::BACK_USERS) {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'users',
                        'action' => 'listing',
                    ),
                );
            } elseif ($back == ConstantsBackContactUser::BACK_GARAGES) {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'garages',
                        'action' => 'garage_add_contacts',
                        $customer_id
                    ),
                );
            } elseif ($back == ConstantsBackContactUser::BACK_DISTRIBUTORS) {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'distributors',
                        'action' => 'distributor_add_contacts',
                        $customer_id
                    ),
                );

                $option_distributor_tmp = $this->Distributor->getDistributorNameById($customer_id, $aagRegionId);
                $option_distributor[$customer_id] = $option_distributor_tmp[0]['Distributor']['name'];
            } elseif ($back == ConstantsBackContactUser::BACK_GARAGES_ID) {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'garages',
                        'action' => 'add_contacts_staff',
                        $garage_id
                    ),
                );

                $option_garage_tmp = $this->Garage->getGarageNameById($garage_id, $aagRegionId);
                $option_garage[$garage_id] = $option_garage_tmp[0]['Garage']['name'];
            }

            if (!$this->request->is('get')) {
                $position = $this->Position->findById($this->request->data['Contact']['position_id']);
                if (isset($position['Position'])) {
                    $this->request->data['User']['role_id'] = $position['Position']['role_id'];
                }

                if (!isset($this->request->data['User']['country_id'])){
                    $this->request->data['User']['country_id'] = $this->request->data['User']['user_country_id'];
                }

                $last_id = $this->Contact->find(
                    'first',
                    array(
                        'order' => 'id DESC',
                        'fields' => 'id'
                    )
                );

                if (empty($this->request->data['Contact']['identification_number'])) {
                    $region_identification_number = '';
                    if (CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_UK_IRELAND')){
                        $region_identification_number = 'UK';
                    } elseif (CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_BENELUX')) {
                        $region_identification_number = 'BX';
                    }
                    $this->request->data['Contact']['identification_number'] = $region_identification_number . "-" . ($last_id['Contact']['id'] + 1);
                }

                // Save ajax selected value if forms fails
                if (!empty($this->request->data['Contact']['garage_id'])) {
                    $garage_contact = $this->Garage->findById($this->request->data['Contact']['garage_id']);
                    $garages_list = array($garage_contact['Garage']['id'] => $garage_contact['Garage']['complete_search']);
                }

                if (!empty($this->request->data['Contact']['distributor_id'])) {
                    $distributor_contact = $this->Distributor->findById($this->request->data['Contact']['distributor_id']);
                    $distributors_list = array($distributor_contact['Distributor']['id'] => $distributor_contact['Distributor']['complete_search']);
                }

                $contact = $this->Contact->new_contact($this->request->data);
                if (!$contact) {
                    if (isset($default_id_number)) {
                        $this->request->data['Contact']['identification_number'] = '';
                    }
                    if ($this->request->data['Contact']['position_id'] == ConstantsPositions::GARAGE_MANAGER_ID && empty($this->request->data['Contact']['garage_id'])) {
                        $this->Session->setFlashError(__t('Validation.Mandatory_to_choose_a_garage'));
                    } else {
                        $this->Session->setFlashError(__t('Contact.Error_new_contact'));
                    }
                } else {
                    $contact_id = $this->Contact->getLastInsertID();
                    if (is_array($this->request->data['NetworkContactBdm']['network_id'])) {
                        foreach ($this->request->data['NetworkContactBdm']['network_id'] as $network_id) {
                            $this->NetworkContactBdm->add($network_id, $contact_id);
                        }
                    }
                    if (is_array($this->request->data['DistributorNetworkContactBdm']['distributor_network_id'])) {
                        foreach ($this->request->data['DistributorNetworkContactBdm']['distributor_network_id'] as $distributor_network_id) {
                            $this->DistributorNetworkContactBdm->add($distributor_network_id, $contact_id);
                        }
                    }
                    //add region or regions

                    if (isset($this->request->data['Contact']['region']) && $this->request->data['Contact']['region']) {
                        $contact_region_bdm = array(
                            'contact_id' => $contact['Contact']['id'],
                            'region_id' => $this->request->data['Contact']['region'],
                        );
                        $this->ContactRegion->save($contact_region_bdm);
                    }

                    if (!empty($this->request->data['Contact']['regions_cv'])) {
                        $this->request->data['Contact']['regions'] = $this->request->data['Contact']['regions_cv'];
                    } elseif (!empty($this->request->data['Contact']['regions_lv'])) {
                        $this->request->data['Contact']['regions'] = $this->request->data['Contact']['regions_lv'];
                    } else {
                        $this->request->data['Contact']['regions'] = array();
                    }
                    if ($this->request->data['Contact']['regions']) {
                        foreach ($this->request->data['Contact']['regions'] as $region) {
                            $contact_region_bdm = array(
                                'contact_id' => $contact['Contact']['id'],
                                'region_id' => $region,
                            );

                            $this->ContactRegion->create();
                            $this->ContactRegion->save($contact_region_bdm);
                        }
                    }

                    $user = $this->User->new_user_contact($contact);
                    $user_bd = $this->User->findById($this->User->getLastInsertID());

                    $user_auth = $this->Session->read('Auth');
                    $contact_new_position = array();
                    $contact_new_position['Contact']['position_id'] = $contact['Contact']['position_id'];

                    $error_rm = false;

                    if ($user && !$error_rm) {
                        if (isset($this->request->params['named']['garage_id']) || $user_bd['User']['role_id'] == ConstantsRoles::GARAGE) {
                            $garage_id = isset($this->request->params['named']['garage_id']) ? $this->request->params['named']['garage_id'] : $user_bd['User']['garage_id'];
                            if ($garage_id) {
                                $garage_contact_tmp = array(
                                    'garage_id' => $garage_id,
                                    'contact_id' => $contact_id
                                );
                                $garage_contact = $this->GarageContactStaff->new_garage_contact_staff($garage_contact_tmp);
                                if ($garage_contact) {
                                    $this->LogChange->add_contact_log(
                                        $this->GarageContactStaff->table,
                                        $this->Session->read('Auth'),
                                        $garage_id,
                                        ConstantsLogType::GARAGE,
                                        $contact_id
                                    );
                                    $success = array(
                                        'success' => 'ok'
                                    );
                                    $js_array = json_encode($success);
                                    echo $js_array;
                                }
                            }
                        }

                        // if the user has been created successfully: Send an email with data and link.
                        $new_user_password = $this->UserRecoverPassword->add_user_password($user_bd['User']['id']);

                        $this->LogChange->get_params_create_log_edit(
                            null,
                            $contact_new_position['Contact'],
                            $this->Contact->table,
                            $user_auth,
                            $contact['Contact']['first_name'] . " " . $contact['Contact']['last_name'],
                            ConstantsLogType::CONTACT
                        );

                        if ($new_user_password && $customer_id) {
                            if ($back == ConstantsBackContactUser::BACK_GARAGES) {
                                $garage_contact_tmp = array(
                                    'garage_id' => $customer_id,
                                    'contact_id' => $contact_id
                                );

                                if (!$this->GarageContactGeneralBranchManager->findByContactIdAndGarageId($contact_id, $customer_id)) {
                                    $this->GarageContactGeneralBranchManager->new_garage_contact_general_branch_manager($garage_contact_tmp);
                                }
                            } elseif ($back == ConstantsBackContactUser::BACK_DISTRIBUTORS) {
                                $distributor_contact_tmp = array(
                                    'distributor_id' => $customer_id,
                                    'contact_id' => $contact_id
                                );

                                if (!$this->DistributorContactGeneralBranchManager->findByContactIdAndDistributorId($contact_id, $customer_id)) {
                                    $this->DistributorContactGeneralBranchManager->new_distributor_contact_general_branch_manager($distributor_contact_tmp);
                                }
                            }
                        }

                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));

                        if ($back == ConstantsBackContactUser::BACK_GARAGES) {
                            $this->redirect(
                                array(
                                    'controller' => 'garages',
                                    'action' => 'my_data',
                                    $customer_id
                                )
                            );
                        } elseif ($back == ConstantsBackContactUser::BACK_DISTRIBUTORS) {
                            $this->redirect(
                                array(
                                    'controller' => 'distributors',
                                    'action' => 'my_data',
                                    $customer_id
                                )
                            );
                        } elseif ($back == ConstantsBackContactUser::BACK_GARAGES_ID) {
                            $this->redirect(
                                array(
                                    'controller' => 'garages',
                                    'action' => 'add_contacts_staff',
                                    $garage_id
                                )
                            );
                        } else {
                            $this->redirect(
                                array(
                                    'controller' => 'contacts',
                                    'action' => 'edit',
                                    'contacts',
                                    'home',
                                    $this->Contact->getLastInsertID(),
                                    null,
                                )
                            );
                        }
                    } else {
                        if ($error_rm) {
                            // RM ERROR
                            $this->Session->setFlashError(__t("Repair.Error"));
                        } else {
                            $this->Session->setFlashError(__t('User.Error_new_user'));
                        }
                    }
                }
            }
            $this->setVarForm();
            $this->set(
                array(
                    'cancel_action' => $cancelAction,
                    'positions' => $positions,
                    'roles' => $roles,
                    'positions_roles' => $positions_roles,
                    'languages' => $languages,
                    'networks' => $networks,
                    'distributors_networks' => $distributors_networks,
                    'customer_id' => $customer_id,
                    'positions_list_bdm' => $positions_list_bdm,
                    'back' => $back,
                    'option_garage' => $option_garage,
                    'countries' => $countries,
                    'country_id' => $country_id,
                    'aag_regions' => $aagRegions,
                    'option_distributor' => $option_distributor,
                    'garages_list' => $garages_list,
                    'distributors_list' => $distributors_list
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit contact.
     */
    public function edit($controller, $action, $contact_id, $id = null)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $contact = $this->Contact->findByIdAndAagRegionId($contact_id, $aagRegionId);

        if (
            $contact &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
                )
            )
        ) {
            $contactRegionsTmp = $this->ContactRegion->findAllByContactId($contact_id);
            $contact_regions = Hash::extract($contactRegionsTmp, '{n}.ContactRegion.region_id');

            $networks = $this->Network->find('list', array(
                'conditions' => array(
                    'Network.aag_region_id' => $aagRegionId
                ),
                'order' => 'Network.name'
            ));

            $distributors_networks = $this->DistributorNetwork->find('list', array('order' => 'DistributorNetwork.name'));
            $positions_bdm = $this->Position->getListPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG));
            if (isset($id)) {
                $garage_id_param = $id;
            } else {
                $garage_id_param = null;
            }
            if (in_array($contact['Contact']['position_id'], $positions_bdm)) {
                $contact['Contact']['region'] = $contact_regions;
            } elseif ($contact['Contact']['position_id'] == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID || $contact['Contact']['position_id'] == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID) {
                $contact['Contact']['regions'] = $contact_regions;
            }

            $delete_contact = $this->Contact->check_delete($contact_id);

            if ($action == 'add_contacts_bdm') {
                $positions = $this->Contact->Position->search_list_bdm();
            } elseif ($action == 'add_contacts_staff' && $controller == 'garages') {
                $positionsList = $this->Contact->Position->get_list_staff_garage_manager();
                $positions = $this->optPositions($positionsList);
            } elseif ($action == 'add_contacts_staff' && $controller == 'distributors') {
                $positionsList = $this->Contact->Position->get_list_staff_distributor_manager();
                $positions = $this->optPositions($positionsList);
            } elseif ($action == 'add_contacts_general_branch_manager' && $controller == 'garages') {
                $positions = $this->Contact->Position->search_list_general_branch_manager_garage();
            } elseif ($action == 'add_contacts_general_branch_manager' && $controller == 'distributors') {
                $positions = $this->Contact->Position->search_list_general_branch_manager_distributor();
            } else {
                $positionsList = $this->Contact->Position->getlist();
                $positions = $this->optPositions($positionsList);
            }

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => $controller,
                    'action' => $action,
                    $id
                ),
            );

            if (isset($contact['Contact']['garage_id'])) {
                $garage = $this->Garage->findById($contact['Contact']['garage_id']);
            } else {
                $garage = $this->Garage->findById($id);
            }

            if (!empty($garage)) {
                $garages_list = array($garage['Garage']['id'] => $garage['Garage']['complete_search']);
            } else {
                $garages_list = array();
            }

            if (isset($contact['Contact']['distributor_id'])) {
                $distributor = $this->Distributor->findById($contact['Contact']['distributor_id']);
            } else {
                $distributor = $this->Distributor->findById($id);
            }

            if (!empty($distributor)) {
                $distributors_list = array($distributor['Distributor']['id'] => $distributor['Distributor']['complete_search']);
            } else {
                $distributors_list = array();
            }

            if (!$this->request->is('get')) {
                if (!isset($this->request->data['Contact']['contact_id'])) {
                    $this->request->data['Contact']['contact_id'] = null;
                }

                if (empty($this->request->data['Contact']['identification_number'])) {
                    $this->request->data['Contact']['identification_number'] = $contact['Contact']['identification_number'];
                }

                $contact_bd = $this->Contact->edit_contact($this->request->data);
                if ($contact_bd) {
                    $contact_id = $contact_bd['Contact']['id'];
                    $garage_staff_id = $this->GarageContactStaff->getStaffData($garage_id_param, $contact_id);

                    if (!empty($garage_staff_id[0]['GarageContactStaff']['id']) && isset($this->request->data['GarageContactStaff']['interest'])) {
                        $garageContact = $this->GarageContactStaff->findById($garage_staff_id[0]['GarageContactStaff']['id']);
                        $garageContact['GarageContactStaff']['interest'] = $this->request->data['GarageContactStaff']['interest'];
                        $this->GarageContactStaff->edit_garage_contact_staff($garageContact);
                    }
                    if (is_array($this->request->data['NetworkContactBdm']['network_id'])) {
                        $this->NetworkContactBdm->deleteAll(array('NetworkContactBdm.contact_id' => $contact_id));
                        foreach ($this->request->data['NetworkContactBdm']['network_id'] as $network_id) {
                            $this->NetworkContactBdm->add($network_id, $contact_id);
                        }
                    }
                    if (is_array($this->request->data['DistributorNetworkContactBdm']['distributor_network_id'])) {
                        $this->DistributorNetworkContactBdm->deleteAll(array('DistributorNetworkContactBdm.contact_id' => $contact_id));
                        foreach ($this->request->data['DistributorNetworkContactBdm']['distributor_network_id'] as $distributor_network_id) {
                            $this->DistributorNetworkContactBdm->add($distributor_network_id, $contact_id);
                        }
                    }

                    //delete regions
                    foreach ($contactRegionsTmp as $region) {
                        $this->ContactRegion->delete($region['ContactRegion']['id']);
                    }

                    //add region or regions
                    if (isset($this->request->data['Contact']['region']) && $this->request->data['Contact']['region']) {
                        $contact_region_bdm = array(
                            'contact_id' => $contact['Contact']['id'],
                            'region_id' => $this->request->data['Contact']['region'],
                        );
                        $this->ContactRegion->create();
                        $this->ContactRegion->save($contact_region_bdm);
                    }

                    if (!empty($this->request->data['Contact']['regions_cv'])) {
                        $this->request->data['Contact']['regions'] = $this->request->data['Contact']['regions_cv'];
                    } elseif (!empty($this->request->data['Contact']['regions_lv'])) {
                        $this->request->data['Contact']['regions'] = $this->request->data['Contact']['regions_lv'];
                    } else {
                        $this->request->data['Contact']['regions'] = array();
                    }
                    if ($this->request->data['Contact']['regions']) {
                        foreach ($this->request->data['Contact']['regions'] as $region) {
                            $contact_region_bdm = array(
                                'contact_id' => $contact['Contact']['id'],
                                'region_id' => $region,
                            );

                            $this->ContactRegion->create();
                            $this->ContactRegion->save($contact_region_bdm);
                        }
                    }
                    $user = $this->Session->read('Auth');
                    $contact_old_position = array();
                    $contact_old_position['Contact']['position_id'] = $contact['Contact']['position_id'];
                    $contact_new_position = array();
                    $contact_new_position['Contact']['position_id'] = $contact_bd['Contact']['position_id'];

                    $this->LogChange->get_params_create_log_edit(
                        $contact_old_position['Contact'],
                        $contact_new_position['Contact'],
                        $this->Contact->table,
                        $user,
                        $contact['Contact']['first_name'] . " " . $contact['Contact']['last_name'],
                        ConstantsLogType::CONTACT
                    );

                    $msg = h(sprintf(__t('Contact.Well_edit'), $contact_bd['Contact']['first_name'] . " " . $contact_bd['Contact']['last_name']));
                    $this->Session->setFlashSuccess($msg);
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t('Contact.Error_edit'));
                }
            } else {
                if ($contact['Contact']['position_id'] == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID) {
                    $contact['Contact']['regions_cv'] = $contact['Contact']['regions'];
                } elseif ($contact['Contact']['position_id'] == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID) {
                    $contact['Contact']['regions_lv'] = $contact['Contact']['regions'];
                }

                //Networks
                $networks_contact = $this->NetworkContactBdm->findAllByContactId($contact['Contact']['id']);
                if (!empty($networks_contact)) {
                    foreach ($networks_contact as $key => $network) {
                        $networks_select[$key] = $network['NetworkContactBdm']['network_id'];
                    }
                    $contact['NetworkContactBdm']['network_id'] = $networks_select;
                }
                //Distributor Networks
                $distributor_networks_contact = $this->DistributorNetworkContactBdm->findAllByContactId($contact['Contact']['id']);
                if (!empty($distributor_networks_contact)) {
                    foreach ($distributor_networks_contact as $key => $distributor_network) {
                        $distributor_networks_select[$key] = $distributor_network['DistributorNetworkContactBdm']['distributor_network_id'];
                    }
                    $contact['DistributorNetworkContactBdm']['distributor_network_id'] = $distributor_networks_select;
                }
                $this->request->data = $contact;
            }
            $positions_list_bdm = json_encode($this->Position->findListByRoleId(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM)));
            $this->setVarForm($contact_regions);
            $this->set(
                array(
                    'cancel_action' => $cancelAction,
                    'positions' => $positions,
                    'delete_contact' => $delete_contact,
                    'contact_id' => $contact_id,
                    'garages_list' => $garages_list,
                    'garage_id' => $id,
                    'networks' => $networks,
                    'distributors_networks' => $distributors_networks,
                    'customer_id' => $id,
                    'positions_list_bdm' => $positions_list_bdm,
                    'garage_contact' => $this->GarageContactStaff->getStaffData($garage_id_param, $contact_id),
                    'contact_aag_region' => $contact['Contact']['aag_region_id'],
                    'option_garage' => array(),
                    'distributors_list' => $distributors_list,
                    'option_distributor' => array(),
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete contact.
     */
    public function delete($contact_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $contact = $this->Contact->findByIdAndAagRegionId($contact_id, $aagRegionId);

        if (
            $contact &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
                )
            )
        ) {
            $class_error = $this->Contact->check_delete($contact_id);

            if ($class_error !== true) {
                $msg = __t('Alert.' . 'Error_contact_' . $class_error);
                $this->Session->setFlashError($msg);
            } else {
                $users = $this->User->findAllByContactId($contact_id);

                if ($users) {
                    foreach ($users as $user) {
                        $class_error2 = $this->User->check_delete($user['User']['id']);

                        if ($class_error2 !== true) {
                            $msg = __t('Alert.' . 'Error_user_' . $class_error2);
                            $this->Session->setFlashError($msg);
                            goto end;
                        } else {

                            $permissions_user = $this->PermissionUser->findAllByUserId($user['User']['id']);
                            if ($permissions_user) {
                                foreach ($permissions_user as $permission_user) {
                                    $this->PermissionUser->eliminar($permission_user['PermissionUser']['id']);
                                }
                            }

                            $user_recover_passwords = $this->UserRecoverPassword->findAllByUserId($user['User']['id']);
                            if ($user_recover_passwords) {
                                foreach ($user_recover_passwords as $user_recover_password) {
                                    $this->UserRecoverPassword->delete($user_recover_password['UserRecoverPassword']['id']);
                                }
                            }

                            $groups_permission_user = $this->GroupPermissionUser->findAllByUserId($user['User']['id']);
                            if ($groups_permission_user) {
                                foreach ($groups_permission_user as $group_permission_user) {
                                    $this->GroupPermissionUser->delete($group_permission_user['GroupPermissionUser']['id']);
                                }
                            }

                            $user_image = $this->UserImage->findByUserId($user['User']['id']);
                            if ($user_image) {
                                $this->UserImage->deleteUserImage($user_image['UserImage']['id']);
                            }

                            $this->User->delete($user['User']['id']);
                        }
                    }
                    $contact = $this->Contact->findById($contact_id);

                    $delete_contact = $this->Contact->delete($contact_id);

                    $this->LogChange->get_params_create_log_delete(
                        $contact['Contact'],
                        $this->Contact->table,
                        $this->Session->read('Auth'),
                        $contact['Contact']['first_name'] . " " . $contact['Contact']['last_name'],
                        ConstantsLogType::CONTACT
                    );

                    if ($delete_contact) {
                        $this->Session->setFlashSuccess(__t('Contact.Well_deleted'));
                    } else {
                        $this->Session->setFlashSuccess(__t('Contact.Bad_deleted'));
                    }
                } else {
                    $contact = $this->Contact->findById($contact_id);
                    $delete_contact = $this->Contact->delete($contact_id);

                    $contact_tmp['Contact']['position_id'] = $contact['Contact']['position_id'];

                    $this->LogChange->get_params_create_log_delete(
                        $contact_tmp['Contact'],
                        $this->Contact->table,
                        $this->Session->read('Auth'),
                        $contact['Contact']['first_name'] . " " . $contact['Contact']['last_name'],
                        ConstantsLogType::CONTACT
                    );

                    if ($delete_contact) {
                        $this->Session->setFlashSuccess(__t('Contact.Well_deleted'));
                    } else {
                        $this->Session->setFlashSuccess(__t('Contact.Bad_deleted'));
                    }
                }
            }
            end:
            $this->redirect(
                array(
                    'controller' => 'contacts',
                    'action' => 'home',
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete contact from garage/distributor.
     */
    public function deleteFromGarageDistributor($contact_id, $redirect_link_controller, $redirect_link_action, $redirect_link_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $contact = $this->Contact->findByIdAndAagRegionId($contact_id, $aagRegionId);

        if (
            $contact &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
                )
            )
        ) {
            $class_error = $this->Contact->check_delete_from_garage_distributor($contact_id);

            if ($class_error !== true) {
                $msg = __t('Alert.' . 'Error_contact_' . $class_error);
                $this->Session->setFlashError($msg);
            } else {
                $users = $this->User->findAllByContactId($contact_id);

                if ($users) {
                    foreach ($users as $user) {
                        $class_error2 = $this->User->check_delete($user['User']['id']);
                        if ($class_error2 !== true) {
                            $msg = __t('Alert.' . 'Error_user_' . $class_error2);
                            $this->Session->setFlashError($msg);
                            goto end;
                        } else {
                            $permissions_user = $this->PermissionUser->findAllByUserId($user['User']['id']);
                            if ($permissions_user) {
                                foreach ($permissions_user as $permission_user) {
                                    $this->PermissionUser->eliminar($permission_user['PermissionUser']['id']);
                                }
                            }

                            $user_recover_passwords = $this->UserRecoverPassword->findAllByUserId($user['User']['id']);
                            if ($user_recover_passwords) {
                                foreach ($user_recover_passwords as $user_recover_password) {
                                    $this->UserRecoverPassword->delete($user_recover_password['UserRecoverPassword']['id']);
                                }
                            }

                            $groups_permission_user = $this->GroupPermissionUser->findAllByUserId($user['User']['id']);
                            if ($groups_permission_user) {
                                foreach ($groups_permission_user as $group_permission_user) {
                                    $this->GroupPermissionUser->delete($group_permission_user['GroupPermissionUser']['id']);
                                }
                            }

                            $user_image = $this->UserImage->findByUserId($user['User']['id']);
                            if ($user_image) {
                                $this->UserImage->deleteUserImage($user_image['UserImage']['id']);
                            }

                            $this->User->delete($user['User']['id']);
                        }
                    }
                    $contact = $this->Contact->findById($contact_id);

                    if ($redirect_link_controller == 'garages') {
                        $garages_contact = $this->GarageContactGeneralBranchManager->findAllByContactId($contact_id);
                        $this->GarageContactGeneralBranchManager->deleteAll(Hash::extract($garages_contact, '{n}.GarageContactGeneralBranchManager.id'));
                    } elseif ($redirect_link_controller == 'distributors') {
                        $distributors_contact = $this->DistributorContactGeneralBranchManager->findAllByContactId($contact_id);
                        $this->DistributorContactGeneralBranchManager->deleteAll(Hash::extract($distributors_contact, '{n}.DistributorContactGeneralBranchManager.id'));
                    }

                    $delete_contact = $this->Contact->delete($contact_id);

                    $this->LogChange->get_params_create_log_delete(
                        $contact['Contact'],
                        $this->Contact->table,
                        $this->Session->read('Auth'),
                        $contact['Contact']['first_name'] . " " . $contact['Contact']['last_name'],
                        ConstantsLogType::CONTACT
                    );

                    if ($delete_contact) {
                        $this->Session->setFlashSuccess(__t('Contact.Well_deleted'));
                    } else {
                        $this->Session->setFlashSuccess(__t('Contact.Bad_deleted'));
                    }
                } else {
                    $contact = $this->Contact->findById($contact_id);

                    if ($redirect_link_controller == 'garages') {
                        $garages_contact = $this->GarageContactGeneralBranchManager->findAllByContactId($contact_id);
                        $this->GarageContactGeneralBranchManager->deleteAll(Hash::extract($garages_contact, '{n}.GarageContactGeneralBranchManager.id'));
                    } elseif ($redirect_link_controller == 'distributors') {
                        $distributors_contact = $this->DistributorContactGeneralBranchManager->findAllByContactId($contact_id);
                        $this->DistributorContactGeneralBranchManager->deleteAll(Hash::extract($distributors_contact, '{n}.DistributorContactGeneralBranchManager.id'));
                    }

                    $delete_contact = $this->Contact->delete($contact_id);

                    $contact_tmp['Contact']['position_id'] = $contact['Contact']['position_id'];

                    $this->LogChange->get_params_create_log_delete(
                        $contact_tmp['Contact'],
                        $this->Contact->table,
                        $this->Session->read('Auth'),
                        $contact['Contact']['first_name'] . " " . $contact['Contact']['last_name'],
                        ConstantsLogType::CONTACT
                    );

                    if ($delete_contact) {
                        $this->Session->setFlashSuccess(__t('Contact.Well_deleted'));
                    } else {
                        $this->Session->setFlashSuccess(__t('Contact.Bad_deleted'));
                    }
                }
            }
            end:

            $this->redirect(
                array(
                    'controller' => $redirect_link_controller,
                    'action' => $redirect_link_action,
                    $redirect_link_id
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    private function optPositions($positions)
    {
        $optPositions = array();
        foreach ($positions as $position) {
            $optPositions[$position['Role']['name' . __s()]][$position['Position']['id']] = '- ' . $position['Position']['name' . __s()];
        }

        return $optPositions;
    }

    private function setVarForm($contact_regions = null)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $titles = $this->Contact->ContactTitle->find(
            'list',
            array(
                'order' => array('name'),
            )
        );

        $contacts_bdm = $this->Contact->getRSMContact($aagRegionId);

        $regions = $this->Region->region_list();
        $regions_used_by_cv = $this->Region->region_list_used_by_cv();
        $regions_used_by_lv = $this->Region->region_list_used_by_lv();
        $logisticCenters = $this->LogisticCenter->getList();

        $aagRegions = $this->AagRegion->region_list();

        if (!is_null($contact_regions) && !empty($contact_regions)) {
            foreach ($contact_regions as $contact_region) {
                if (array_key_exists($contact_region, $regions_used_by_cv)) {
                    unset($regions_used_by_cv[$contact_region]);
                }
            }
        }
        if (!is_null($contact_regions) && !empty($contact_regions)) {
            foreach ($contact_regions as $contact_region) {
                if (array_key_exists($contact_region, $regions_used_by_lv)) {
                    unset($regions_used_by_lv[$contact_region]);
                }
            }
        }

        $regions_cv = array_diff($regions, $regions_used_by_cv);
        $regions_lv = array_diff($regions, $regions_used_by_lv);

        if ($roleId == ConstantsRoles::SUPER_ADMIN) {
            $this->User->validator()->remove('aag_region_id');
        }

        $this->set(array(
            'titles' => $titles,
            'contacts_bdm' => $contacts_bdm,
            'aag_regions' => $aagRegions,
            'regions' => $regions,
            'regions_cv' => $regions_cv,
            'regions_lv' => $regions_lv,
            'logistic_centers' => $logisticCenters,
            'user_aag_region_id' => $aagRegionId,
            'user_role_id' => $roleId
        ));
    }

    /**
     * AJAX get ContactDelegate names.
     * Used for dynamic selects.
     */
    public function get_contacts_name_delegates()
    {
        $this->verify_ajax($this->request);

        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $contacts_name = $this->Contact->getContactsDelegatesAjax($this->request->query, $aagRegionId);

        $list_contacts = array();
        foreach ($contacts_name as $key => $contact) {
            $list_contacts[] = array(
                'id' => $key,
                'text' => $contact,
            );
        }

        $list_contacts_complete['items'] = $list_contacts;

        return json_encode($list_contacts_complete);
    }

    /**
     * AJAX load countries by AAG region.
     */
    public function ajax_load_countries()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
            )
        ) {
            $this->layout = false;
            $this->autoRender = false;

            $aagRegionId = $this->request->data['aag_region_id'];

            $countries = $this->Country->get_country_by_region($aagRegionId);
            $countries[ConstantsConfigSelect::ALL] = __t('General.All');

            return json_encode($countries);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get country by Garage ID.
     */
    public function ajax_get_country_garages()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
            )
        ) {
            $this->layout = false;
            $this->autoRender = false;

            $garage_id = $this->request->data['customer_id'];

            $garage = $this->Garage->findById($garage_id);
            if (!empty($garage['Garage']['province_id'])) {
                $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
            } else {
                $postcode_tmp = explode(' ', $garage['Garage']['postcode']);
                $province = $this->Garage->getProvinceIdByPostcode($postcode_tmp[0]);
                $country = $this->Country->get_country_by_province($province);
            }

            return $country['Country']['id'];
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get country by Distributor ID.
     */
    public function ajax_get_country_distributors()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
            )
        ) {
            $this->layout = false;
            $this->autoRender = false;

            $distributor_id = $this->request->data['customer_id'];

            $distributor = $this->Distributor->findById($distributor_id);
            if (!empty($distributor['Distributor']['province_id'])) {
                $country = $this->Country->get_country_by_province($distributor['Distributor']['province_id']);
            } else {
                $postcode_tmp = explode(' ', $distributor['Distributor']['postcode']);
                $province = $this->Garage->getProvinceIdByPostcode($postcode_tmp[0]);
                $country = $this->Country->get_country_by_province($province);
            }

            return $country['Country']['id'];
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get contacts emails by AAG region..
     * Used for dynamic selects.
     */
    public function get_contacts_email_region()
    {
        $this->verify_ajax($this->request);

        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $contacts_email = $this->Contact->obtenerPosiblesContactsEmailsAjaxRegion($this->request->query, $aagRegionId);

        $list_contacts_emails = array();
        foreach ($contacts_email as $key => $contact_email) {
            $list_contacts_emails[] = array(
                'id' => $key,
                'text' => $contact_email,
            );
        }

        $list_contacts_email_complete['items'] = $list_contacts_emails;
        return json_encode($list_contacts_email_complete);
    }
}
