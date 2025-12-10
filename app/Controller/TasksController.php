<?php
class TasksController extends AppController
{
    public $uses = array(
        'Task',
        'ContactList',
        'Alert',
        'Garage',
        'Distributor',
        'User',
        'Contact',
        'ContactRegion',
        'DebriefTask',
        'DebriefTaskGarage',
        'DebriefTaskDistributor',
        'DebriefTopic',
        'Appointment',
        'TaskContactList',
        'TaskFile',
        'TaskGarage',
        'TaskDistributor',
        'GarageContactBdm',
        'ContactContactList',
        'Contact',
        'User',
        'TaskContactList',
        'TaskUser',
        'AppointmentTopic',
        'DebriefTask',
        'DebriefTopic',
        'Region',
        'ContactRegion',
        'AppointmentTopic',
        'TradingGroup',
        'DistributorActivity',
    );

    /**
     * CRM Tasks home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $user = CakeSession::read('Auth.User.id');
            $status = $this->Task->TaskStatus->search_list_all();

            if (!empty($this->request->query)) {
                $searcher = $this->request->query;
                CakeSession::write('Auth.User.query', $searcher);
            } else {
                $searcher = CakeSession::read('Auth.User.query');
            }

            $this->request->data['Search'] = $searcher;

            $existing_views = array(
                ConstantsTasksExistingViews::INDEX_ASSIGNED_TO_ME => __t(ConstantsTasksExistingViews::ASSIGNED_TO_ME),
                ConstantsTasksExistingViews::INDEX_CREATED_BY_ME => __t(ConstantsTasksExistingViews::CREATED_BY_ME),
                ConstantsTasksExistingViews::INDEX_ASSIGNED_TO_MY_CUSTOMERS => __t(ConstantsTasksExistingViews::ASSIGNED_TO_MY_CUSTOMERS),
            );

            $config = CakeSession::read('Auth.User.Config');
            if ($config[ConstantsConfig::ASSIGN_TO_GROUP]) {
                $existing_views[ConstantsTasksExistingViews::INDEX_ASSIGNED_TO_MY_GROUP] = __t(ConstantsTasksExistingViews::ASSIGNED_TO_MY_GROUP);
            }

            $users = $this->setUsersDe();
            if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN) {
                $existing_views[ConstantsTasksExistingViews::INDEX_ALL] = __t(ConstantsTasksExistingViews::ALL);
            }

            $this->set(array(
                'users' => $users,
                'status' => $status,
                'user' => $user,
                'existing_views' => $existing_views,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * CRM Subtasks home page.
     */
    public function subtasks_home()
    {
        if (
            isset($this->request->query['id']) &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
                )
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $users = $this->Task->User->listCompleteNameRegion($aagRegionId);
            $status = $this->Task->TaskStatus->search_list();

            $searcher = $this->request->query;

            $conditions = $this->Task->conditions($searcher);
            if ($this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG || $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_TG) {
                $conditions[] = array('OR' => array(
                    array(
                        'GarageContactBdm.contact_id' => $this->Session->read('Auth.User.contact_id'),
                    ),
                    array(
                        'GarageContactBdm.contact_id' => null,
                    )
                ));
                $conditions[] = array('OR' => array(
                    array(
                        'DistributorContactBdm.contact_id' => $this->Session->read('Auth.User.contact_id'),
                    ),
                    array(
                        'DistributorContactBdm.contact_id' => null,
                    )
                ));
            }

            $tasks = $this->custom_pagination(
                $this->Task->_query('search_subtask'),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL
            );

            foreach ($tasks as $key => $task) {
                $tasks[$key]['Task']['creation_date'] = Fecha::toFormatoVistaFecha($task['Task']['creation_date']);
                $tasks[$key]['Task']['limit_date'] = Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
                $tasks[$key]['TaskContactList'] = $this->TaskContactList->findAllByTaskId($tasks[$key]['Task']['id']);
            }

            $this->set(array(
                'tasks' => $tasks,
                'users' => $users,
                'status' => $status,
                'params' => http_build_query($this->request->query) . "\n"
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * View CMR task.
     */
    public function view($task_id)
    {
        $task = $this->Task->getTask($task_id);
        if (
            $task &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
                )
            )
        ) {
            $this->set(array(
                'task' => $task,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create CRM task.
     */
    public function add($appointment_id = null)
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
            $this->setGarageFilter();
            $this->setDistributorFilter();
            $debrief_tasks = $this->DebriefTask->find('all', array('order' => 'uses DESC'));
            $debrief_tasks_list = $this->DebriefTask->find('list', array('fields' => array(
                'id',
                'title' . __s()
            )));

            $debrief_topics = $this->DebriefTopic->find('all');
            $debrief_topics_list = $this->DebriefTopic->getList();
            $task_status_list = $this->Task->TaskStatus->search_list();
            $task_status_expired = $this->Task->TaskStatus->getExpiredStatus();
            $contact_lists = $this->ContactList->find('list', array(
                'conditions' => array(
                    'OR' => array(
                        'user_id' => CakeSession::read('Auth.User.id'),
                        'global' => ConstantsBooleans::YES
                    )
                )
            ));

            if (!is_null($appointment_id)) {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'appointments',
                        'action' => 'edit',
                        $appointment_id
                    ),
                );
            } else {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'tasks',
                        'action' => 'home',
                        '?' => array(
                            'assigned_to' => null,
                        ),
                    ),
                );
            }

            $config = CakeSession::read('Auth.User.Config');
            if (!$config[ConstantsConfig::TASK_DEADLINE]) {
                $this->Task->validator()->remove('limit_date');
            }

            if (!$this->request->is('get')) {
                if ($this->request->data['Task']['send_to'] != ConstantsTasks::USER) {
                    $this->Task->validator()->remove('user_assigned_id');
                }
                $error_size = false;
                $check_file = false;

                $task = array(
                    'Task' => array(
                        'appointment_id' => $appointment_id,
                        'body' => $this->request->data['Task']['body'],
                        'title' => isset($this->request->data['Task']['title']) ? $this->request->data['Task']['title'] : '',
                        'limit_date' => $this->request->data['Task']['limit_date'],
                        'task_status_id' => $this->request->data['Task']['task_status_id'],
                        'user_id' => CakeSession::read('Auth.User.id'),
                        'user_assigned_id' => isset($this->request->data['Task']['user_assigned_id']) ? $this->request->data['Task']['user_assigned_id'] : null,
                        'reason' => $this->request->data['Task']['reason'],
                        'mandatory' => isset($this->request->data['Task']['mandatory']) ? $this->request->data['Task']['mandatory'] : null
                    )
                );

                $user = CakeSession::read('Auth.User.id');
                if ($this->Task->new_task($task, $user)) {
                    $task_id = $this->Task->getLastInsertID();
                    $contacts_lists_bd = true;
                    $users_bd = true;
                    $files_bd = true;
                    $empty_garages = false;
                    $empty_distributors = false;
                    $data_user = $this->request->data['User'];
                    $data_files = $this->request->data['Task']['files'];
                    $data_contact_list = isset($this->request->data['ContactList']) ? $this->request->data['ContactList'] : array();
                    $conditions = array();
                    $joins = array();
                    if ($this->request->data['Task']['send_to'] == ConstantsTasks::GARAGE) {
                        $conditions = array(
                            'Garage.aag_region_id' => $aagRegionId
                        );
                        if (!empty($this->request->data['TaskGarage']['garage_id'])) {
                            foreach ($this->request->data['TaskGarage']['garage_id'] as $garage_id) {
                                $task_garage = array(
                                    'TaskGarage' => array(
                                        'task_id' => $task_id,
                                        'garage_id' => $garage_id,
                                        'completed' => $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED ? ConstantsBooleans::YES : ConstantsBooleans::NO
                                    )
                                );
                                $this->TaskGarage->saveMany($task_garage);
                            }
                        } elseif (empty($this->request->data['TaskGarage']['garage_id'])) {
                            $filters = $this->request->data['GarageFilter'];
                            if (!empty($filters['branch'])) {
                                $joins[] = array(
                                    'alias' => 'GarageDistributor',
                                    'table' => 'garages_distributors',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'GarageDistributor.garage_id = Garage.id',
                                    ),
                                );
                                $conditions[0]['GarageDistributor.distributor_id'] = $filters['branch'];
                            }
                            if (!empty($filters['BDM']) || CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_AAG || CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_TG) {
                                $joins[] = array(
                                    'alias' => 'GarageContactBdm',
                                    'table' => 'garages_contacts_bdm',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'GarageContactBdm.garage_id = Garage.id',
                                    ),
                                );
                                if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_AAG || CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_TG) {
                                    $conditions[0]['GarageContactBdm.contact_id'] = array($this->Session->read('Auth.User.contact_id'));
                                } else {
                                    $conditions[0]['GarageContactBdm.contact_id'] = array($filters['BDM']);
                                }
                            }
                            if (!isset($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId(CakeSession::read('Auth.User.contact_id'));
                                $contacts = array();
                                $contacts[] = CakeSession::read('Auth.User.contact_id');
                                foreach ($contacts_tmp as $contact_tmp_id => $contact_tmp) {
                                    $contacts[] = $contact_tmp_id;
                                }
                                $garages = $this->Garage->getGaragesByContacts($contacts, $aagRegionId);
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
                            } elseif (!empty($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId($filters['RSM']);
                                $contacts = array();
                                $contacts[] = $filters['RSM'];
                                foreach ($contacts_tmp as $contact_tmp_id => $contact_tmp) {
                                    $contacts[] = $contact_tmp_id;
                                }
                                $garages = $this->Garage->getGaragesByContacts($contacts, $aagRegionId);
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
                            if (!empty($filters['Customer_status'])) {
                                $conditions[0]['Garage.status'] = $filters['Customer_status'];
                            }
                            $garages = $this->Garage->getGarageTasks($conditions, $joins);
                            if (empty($garages)) {
                                $empty_garages = true;
                            } else {
                                foreach ($garages as $garage) {
                                    $task_garage = array(
                                        'TaskGarage' => array(
                                            'task_id' => $task_id,
                                            'garage_id' => $garage['Garage']['id'],
                                            'completed' => $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED ? ConstantsBooleans::YES : ConstantsBooleans::NO
                                        )
                                    );
                                    $this->TaskGarage->saveMany($task_garage);
                                }
                            }
                        }
                    } elseif ($this->request->data['Task']['send_to'] == ConstantsTasks::DISTRIBUTOR) {
                        $conditions = array(
                            'Distributor.aag_region_id' => $aagRegionId
                        );
                        if (!empty($this->request->data['TaskDistributor']['distributor_id'])) {
                            foreach ($this->request->data['TaskDistributor']['distributor_id'] as $distributor_id) {
                                $task_distributor = array(
                                    'TaskDistributor' => array(
                                        'task_id' => $task_id,
                                        'distributor_id' => $distributor_id,
                                        'completed' => $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED ? ConstantsBooleans::YES : ConstantsBooleans::NO
                                    )
                                );
                                $this->TaskDistributor->saveMany($task_distributor);
                            }
                        } elseif (empty($this->request->data['TaskDistributor']['distributor_id'])) {
                            $filters = $this->request->data['DistributorFilter'];
                            if (!empty($filters['BDM']) || CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_AAG || CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_TG) {
                                $joins[] = array(
                                    'alias' => 'DistributorContactBdm',
                                    'table' => 'distributors_contacts_bdm',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'DistributorContactBdm.distributor_id = Distributor.id',
                                    ),
                                );
                                if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_AAG || CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_TG) {
                                    $conditions[0]['DistributorContactBdm.contact_id'] = array($this->Session->read('Auth.User.contact_id'));
                                } else {
                                    $conditions[0]['DistributorContactBdm.contact_id'] = array($filters['BDM']);
                                }
                            }
                            if (!isset($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId(CakeSession::read('Auth.User.contact_id'));
                                $contacts = array();
                                $contacts[] = CakeSession::read('Auth.User.contact_id');
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
                            } elseif (!empty($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId($filters['RSM']);
                                $contacts = array();
                                $contacts[] = $filters['RSM'];
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
                            $distributors = $this->Distributor->getDistributorTasks($conditions, $joins);

                            if (empty($distributors)) {
                                $empty_distributors = true;
                            } else {
                                foreach ($distributors as $distributor) {
                                    $task_distributor = array(
                                        'TaskDistributor' => array(
                                            'task_id' => $task_id,
                                            'distributor_id' => $distributor['Distributor']['id'],
                                            'completed' => $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED ? ConstantsBooleans::YES : ConstantsBooleans::NO
                                        )
                                    );
                                    $this->TaskDistributor->saveMany($task_distributor);
                                }
                            }
                        }
                    } elseif ($this->request->data['Task']['send_to'] == ConstantsTasks::USER) {
                        $conditions = array(
                            'User.aag_region_id' => $aagRegionId
                        );
                        if (isset($this->request->data['Task']['user_assigned_id']) && !empty($this->request->data['Task']['user_assigned_id'])) {
                            $user = array(
                                'TaskUser' => array(
                                    'user_id' => $this->request->data['Task']['user_assigned_id'],
                                    'task_id' => $task_id
                                )
                            );
                            $this->TaskUser->new_task_user($user);
                        }
                    }

                    if (!empty($data_contact_list['contact_list_id'])) {
                        $task_contact_list_bd = array(
                            'TaskContactList' => array(
                                'contact_list_id' => $data_contact_list['contact_list_id'],
                                'task_id' => $task_id,
                            )
                        );
                        $contacts_lists_bd = $this->Task->TaskContactList->new_task_contact_list($task_contact_list_bd);
                    }

                    if (!empty($data_user['user_id'])) {
                        $users_bd = false;
                        foreach ($data_user['user_id'] as $user) {
                            $task_user = array(
                                'TaskUser' => array(
                                    'user_id' => $user,
                                    'task_id' => $task_id
                                )
                            );

                            $users_bd = $this->Task->TaskUser->new_task_user($task_user);
                        }
                    }
                    if (!empty($data_files)) {
                        foreach ($data_files as $file) {
                            if ($file['error'] == ConstantsBooleans::NO) {
                                $check_file = FileManager::check_file($file);
                                if ($check_file == ConstantsFileErrorTypes::OK) {
                                    if (!$this->Task->TaskFile->saveFile($file, $task_id, ConstantsFileType::FILE)) {
                                        $files_bd = false;
                                    }
                                } else {
                                    break;
                                }
                            } elseif ($file['error'] == ConstantsFlag::ERROR_DIMENSIONS) {
                                $error_size = true;
                            }
                        }
                    }

                    if ($contacts_lists_bd == ConstantsBooleans::YES && $users_bd == ConstantsBooleans::YES && $files_bd == ConstantsBooleans::YES && $empty_distributors == ConstantsBooleans::NO && $empty_garages == ConstantsBooleans::NO) {
                        $user_creator_tmp = $this->User->find('first', array(
                            'conditions' => array(
                                'id' => CakeSession::read('Auth.User.id')
                            ),
                            'fields' => array(
                                'full_name'
                            )
                        ));

                        if (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) {
                            $user_creator = $user_creator_tmp['User']['full_name'];
                            $customer = $this->Task->getCustomerAppointmentByTaskId($task_id);
                            $this->request->data['Task']['customer'] = $customer;
                            $subject = sprintf(__t('Alert.New_task_subject'), $customer . $user_creator);
                            $task_status = $this->Task->getTaskStatusByTaskId($task_id);
                            $this->request->data['Task']['status'] = $task_status['TaskStatus']['name' . __s()];
                            $this->request->data['Task']['user_creation_id'] = CakeSession::read('Auth.User.id');
                            if (!isset($this->request->data['Task']['check_send']) || $this->request->data['Task']['check_send']) {
                                $this->sendDataAlert($this->request->data, $subject, __t('Alert.New_task'), $task_id);
                            }
                        }

                        if (!$check_file || $check_file == ConstantsFileErrorTypes::OK) {
                            if ($error_size) {
                                $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                            } else {
                                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                                $this->redirect(
                                    array(
                                        'controller' => 'tasks',
                                        'action' => 'edit',
                                        $this->Task->getLastInsertID()
                                    )
                                );
                            }
                        } elseif ($check_file == ConstantsFileErrorTypes::SIZE_ERROR) {
                            $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                        } else {
                            $this->Session->setFlashError(__t(ConstantsMessages::FILE_ERROR_EXTENSION));
                        }
                    } elseif (!$files_bd) {
                        $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                    } elseif ($empty_garages == ConstantsBooleans::YES || $empty_distributors == ConstantsBooleans::YES) {
                        $this->Session->setFlashError(__t('Validation.Users_not_found'));
                    } else {
                        $this->Session->setFlashError(__t(ConstantsAlertsErrors::ERROR_GENERAL));
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsAlertsErrors::ERROR_GENERAL));
                }
            }

            $trading_groups = $this->TradingGroup->getTradingGroup();
            $distributor_activities = $this->DistributorActivity->search_list();

            $user = $this->Acceso->user();
            $userAagRegionId = $user['aag_region_id'];

            $this->set(array(
                'cancel_action' => $cancelAction,
                'contact_lists' => $contact_lists,
                'task_status_list' => $task_status_list,
                'task_status_expired' => $task_status_expired,
                'debrief_tasks' => $debrief_tasks,
                'debrief_tasks_list' => $debrief_tasks_list,
                'debrief_topics' => $debrief_topics,
                'debrief_topics_list' => $debrief_topics_list,
                'task_garages' => array(),
                'task_distributors' => array(),
                'trading_groups' => isset($trading_groups) ? $trading_groups : '',
                'distributor_activities' => isset($distributor_activities) ? $distributor_activities : '',
                'user_aag_region_id' => $userAagRegionId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX create task.
     */
    public function ajax_create_task()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $bdm_code = $this->request->data['bdm_code'];

            $user = $this->User->getUserByCode($bdm_code, array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM));
            $task_status_list = $this->Task->TaskStatus->search_list();

            $this->set(
                array(
                    'task_status_list' => $task_status_list,
                    'user' => $user,
                )
            );
            $config = CakeSession::read('Auth.User.Config');
            if (!$config[ConstantsConfig::TASK_DEADLINE]) {
                $this->Task->validator()->remove('limit_date');
            }

            $this->layout = null;
            $this->render('../Tasks/Elements/ajax_create_task');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX create task.
     */
    public function ajax_submit_create_task()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $task_ajax = $this->request->data;

            $task_tmp['Task']['body'] = $task_ajax['Task']['body'];
            $task_tmp['Task']['title'] = $task_ajax['Task']['title'];
            $task_tmp['Task']['task_status_id'] = $task_ajax['Task']['task_status_id'];
            $task_tmp['Task']['user_creation_id'] = CakeSession::read('Auth.User.id');
            $task_tmp['Task']['limit_date'] = Fecha::toFormatoBd($task_ajax['Task']['limit_date']);
            $task_tmp['Task']['creation_date'] = date('Y-m-d');
            $task_tmp['Task']['user_assigned_id'] = $task_ajax['Task']['user_assigned_id'];

            $this->autoRender = false;
            $config = CakeSession::read('Auth.User.Config');
            if (!$config[ConstantsConfig::TASK_DEADLINE]) {
                $this->Task->validator()->remove('limit_date');
            }

            if ($task_bd = $this->Task->ajax_save($task_tmp)) {
                foreach ($task_ajax['Task']['files'] as $file) {
                    if ($file['error'] == ConstantsBooleans::NO) {
                        $check_file = FileManager::check_file($file);
                        if ($check_file == ConstantsFileErrorTypes::OK) {
                            if (!$this->Task->TaskFile->saveFile($file, $task_bd['Task']['id'], ConstantsFileType::FILE)) {
                                return 'error_file';
                            }
                        } else {
                            return 'error_file';
                        }
                    }
                }
            } else {
                return false;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit CRM task.
     */
    public function edit($task_id, $appointment_id = null)
    {
        $task_bd = $this->Task->getTask($task_id);
        if (
            $task_bd &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
            $array_user_name = array();
            $this->setGarageFilter();
            $this->setDistributorFilter();
            $debrief_tasks = $this->DebriefTask->find('all', array('order' => 'uses DESC'));
            $debrief_tasks_list = $this->DebriefTask->find('list', array('fields' => array(
                'id',
                'title' . __s()
            )));

            $debrief_topics = $this->DebriefTopic->find('all');
            $debrief_topics_list = $this->DebriefTopic->getList();
            $task_status_expired = $this->Task->TaskStatus->getExpiredStatus();
            if (!$task_bd) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_TASK));
                $this->redirect(
                    array(
                        'controller' => 'tasks',
                        'action' => 'home',
                        '?' => array(
                            'assigned_to' => null,
                        ),
                    )
                );
            }

            $contact_lists = $this->ContactList->find('list', array(
                'conditions' => array(
                    'OR' => array(
                        'user_id' => CakeSession::read('Auth.User.id'),
                        'global' => ConstantsBooleans::YES
                    )
                )
            ));
            $task_files = $this->Task->TaskFile->findAllByTaskId($task_id);
            $task_users = $this->Task->TaskUser->getListUserByTask($task_id);
            $task_contact_lists = $this->Task->TaskContactList->getListContactListByTask($task_id);
            $task_status_list = $this->Task->TaskStatus->search_list();

            if (!is_null($appointment_id)) {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'appointments',
                        'action' => 'edit',
                        $appointment_id
                    ),
                );
            } else {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'tasks',
                        'action' => 'home',
                        '?' => array(
                            'assigned_to' => null,
                        ),
                    ),
                );
            }
            $config = CakeSession::read('Auth.User.Config');
            if (!$config[ConstantsConfig::TASK_DEADLINE]) {
                $this->Task->validator()->remove('limit_date');
            }
            if (!$this->request->is('get')) {
                if ($this->request->data['Task']['send_to'] != ConstantsTasks::USER) {
                    $this->Task->validator()->remove('user_assigned_id');
                }
                $error_size = false;
                $check_file = false;

                $task = array(
                    'Task' => array(
                        'id' => $task_id,
                        'title' => isset($this->request->data['Task']['title']) ? $this->request->data['Task']['title'] : null,
                        'body' => $this->request->data['Task']['body'],
                        'limit_date' => $this->request->data['Task']['limit_date'],
                        'task_status_id' => $this->request->data['Task']['task_status_id'],
                        'user_creation_id' => CakeSession::read('Auth.User.id'),
                        'user_assigned_id' => isset($this->request->data['Task']['user_assigned_id']) ? $this->request->data['Task']['user_assigned_id'] : null,
                        'reason' => $this->request->data['Task']['reason'],
                        'mandatory' => isset($this->request->data['Task']['mandatory']) ? $this->request->data['Task']['mandatory'] : null
                    )
                );
                if (is_null($appointment_id)) {
                    if (isset($this->request->data['Task']['appointment_id'])) {
                        $appointment_id = $this->request->data['Task']['appointment_id'];
                        $task['Task']['appointment_id'] = $appointment_id;
                    }
                } else {
                    $task['Task']['appointment_id'] = $appointment_id;
                }

                if ($this->Task->edit_task($task)) {
                    $task_contact_lists_bd = $this->TaskContactList->findAllByTaskId($task['Task']['id']);
                    foreach ($task_contact_lists_bd as $task_contact_list_bd) {
                        $this->TaskContactList->delete($task_contact_list_bd['TaskContactList']['id']);
                    }
                    $task_users_bd = $this->TaskUser->findAllByTaskId($task['Task']['id']);
                    foreach ($task_users_bd as $task_user_bd) {
                        $this->TaskUser->delete($task_user_bd['TaskUser']['id']);
                    }
                    $task_garages_bd = $this->TaskGarage->findAllByTaskId($task['Task']['id']);
                    foreach ($task_garages_bd as $task_garage_bd) {
                        $this->TaskGarage->delete($task_garage_bd['TaskGarage']['id']);
                    }
                    $task_distributors_bd = $this->TaskDistributor->findAllByTaskId($task['Task']['id']);
                    foreach ($task_distributors_bd as $task_distributor_bd) {
                        $this->TaskDistributor->delete($task_distributor_bd['TaskDistributor']['id']);
                    }
                    $contacts_lists_bd = true;
                    $files_bd = true;
                    $topic_bd = true;
                    $users_bd = true;
                    $data_user = $this->request->data['Task']['user_assigned_id'];
                    $data_contact_list = isset($this->request->data['ContactList']) ? $this->request->data['ContactList'] : array();
                    $data_files = $this->request->data['Task']['files'];
                    if (isset($this->request->data['AppointmentTopic'])) {
                        $data_topics = $this->request->data['AppointmentTopic'];
                    }
                    if ($this->request->data['Task']['send_to'] == ConstantsTasks::GARAGE) {
                        if (!empty($this->request->data['TaskGarage']['garage_id'])) {
                            foreach ($this->request->data['TaskGarage']['garage_id'] as $garage_id) {
                                $task_garage = array(
                                    'TaskGarage' => array(
                                        'task_id' => $task_id,
                                        'garage_id' => $garage_id,
                                        'completed' => $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED ? ConstantsBooleans::YES : ConstantsBooleans::NO
                                    )
                                );
                                $this->TaskGarage->saveMany($task_garage);
                            }
                        } elseif (empty($this->request->data['TaskGarage']['garage_id'])) {
                            $filters = $this->request->data['GarageFilter'];
                            if (!empty($filters['branch'])) {
                                $joins[] = array(
                                    'alias' => 'GarageDistributor',
                                    'table' => 'garages_distributors',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'GarageDistributor.garage_id = Garage.id',
                                    ),
                                );
                                $conditions[0]['GarageDistributor.distributor_id'] = $filters['branch'];
                            }
                            if (
                                !empty($filters['BDM']) ||
                                ($this->Session->read('Auth.User.role_id') == ConstantsRoles::GPC_LOGISTICS_BDM) ||
                                ($this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG) ||
                                ($this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_TG)
                            ) {
                                $joins[] = array(
                                    'alias' => 'GarageContactBdm',
                                    'table' => 'garages_contacts_bdm',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'GarageContactBdm.garage_id = Garage.id',
                                    ),
                                );
                                if (
                                    $this->Session->read('Auth.User.role_id') == ConstantsRoles::GPC_LOGISTICS_BDM ||
                                    $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG ||
                                    $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_TG
                                ) {
                                    $conditions[0]['GarageContactBdm.contact_id'] = array($this->Session->read('Auth.User.contact_id'));
                                } else {
                                    $conditions[0]['GarageContactBdm.contact_id'] = array($filters['BDM']);
                                }
                            }
                            if (!isset($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId(CakeSession::read('Auth.User.contact_id'));
                                $contacts = array();
                                $contacts[] = CakeSession::read('Auth.User.contact_id');
                                foreach ($contacts_tmp as $contact_tmp_id => $contact_tmp) {
                                    $contacts[] = $contact_tmp_id;
                                }
                                $garages = $this->Garage->getGaragesByContacts($contacts, $aagRegionId);
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
                            } elseif (!empty($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId($filters['RSM']);
                                $contacts = array();
                                $contacts[] = $filters['RSM'];
                                foreach ($contacts_tmp as $contact_tmp_id => $contact_tmp) {
                                    $contacts[] = $contact_tmp_id;
                                }
                                $garages = $this->Garage->getGaragesByContacts($contacts, $aagRegionId);
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
                            if (!empty($filters['Customer_status'])) {
                                $conditions[0]['Garage.status'] = $filters['Customer_status'];
                            }
                            $garages = $this->Garage->getGarageTasks($conditions, $joins);
                            foreach ($garages as $garage) {
                                $task_garage = array(
                                    'TaskGarage' => array(
                                        'task_id' => $task_id,
                                        'garage_id' => $garage['Garage']['id'],
                                        'completed' => $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED ? ConstantsBooleans::YES : ConstantsBooleans::NO
                                    )
                                );
                                $this->TaskGarage->saveMany($task_garage);
                            }
                        }
                    } elseif ($this->request->data['Task']['send_to'] == ConstantsTasks::DISTRIBUTOR) {
                        if (!empty($this->request->data['TaskDistributor']['distributor_id'])) {
                            foreach ($this->request->data['TaskDistributor']['distributor_id'] as $distributor_id) {
                                $task_distributor = array(
                                    'TaskDistributor' => array(
                                        'task_id' => $task_id,
                                        'distributor_id' => $distributor_id,
                                        'completed' => $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED ? ConstantsBooleans::YES : ConstantsBooleans::NO
                                    )
                                );
                                $this->TaskDistributor->saveMany($task_distributor);
                            }
                        } elseif (empty($this->request->data['TaskDistributor']['distributor_id'])) {
                            $filters = $this->request->data['DistributorFilter'];
                            if (
                                !empty($filters['BDM']) ||
                                ($this->Session->read('Auth.User.role_id') == ConstantsRoles::GPC_LOGISTICS_BDM) ||
                                ($this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG) ||
                                ($this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_TG)
                            ) {
                                $joins[] = array(
                                    'alias' => 'DistributorContactBdm',
                                    'table' => 'distributors_contacts_bdm',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'DistributorContactBdm.distributor_id = Distributor.id',
                                    ),
                                );
                                if (
                                    $this->Session->read('Auth.User.role_id') == ConstantsRoles::GPC_LOGISTICS_BDM ||
                                    $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG ||
                                    $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_TG
                                ) {
                                    $conditions[0]['DistributorContactBdm.contact_id'] = array($this->Session->read('Auth.User.contact_id'));
                                } else {
                                    $conditions[0]['DistributorContactBdm.contact_id'] = array($filters['BDM']);
                                }
                            }
                            if (!isset($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId(CakeSession::read('Auth.User.contact_id'));
                                $contacts = array();
                                $contacts[] = CakeSession::read('Auth.User.contact_id');
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
                            } elseif (!empty($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId($filters['RSM']);
                                $contacts = array();
                                $contacts[] = $filters['RSM'];
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
                            $distributors = $this->Distributor->getDistributorTasks($conditions, $joins);
                            foreach ($distributors as $distributor) {
                                $task_distributor = array(
                                    'TaskDistributor' => array(
                                        'task_id' => $task_id,
                                        'distributor_id' => $distributor['Distributor']['id'],
                                        'completed' => $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED ? ConstantsBooleans::YES : ConstantsBooleans::NO
                                    )
                                );
                                $this->TaskDistributor->saveMany($task_distributor);
                            }
                        }
                    } elseif ($this->request->data['Task']['send_to'] == ConstantsTasks::USER) {
                        if (!empty($data_contact_list['contact_list_id'])) {
                            $task_contact_list_bd = array(
                                'TaskContactList' => array(
                                    'contact_list_id' => $data_contact_list['contact_list_id'],
                                    'task_id' => $task_id,
                                )
                            );
                            $contacts_lists_bd = $this->Task->TaskContactList->new_task_contact_list($task_contact_list_bd);
                        }

                        if (!empty($data_user)) {
                            $users_bd = false;
                            $task_user = array(
                                'user_id' => $data_user['user_id'],
                                'task_id' => $task_id
                            );

                            $users_bd = $this->Task->TaskUser->new_task_user($task_user);
                        }
                    }
                    if (!empty($data_contact_list['contact_list_id'])) {
                        if (is_array($data_contact_list['contact_list_id'])) {
                            $contacts_lists_bd = false;
                            foreach ($data_contact_list['contact_list_id'] as $contact_list) {
                                $task_contact_list = array(
                                    'contact_list_id' => $contact_list,
                                    'task_id' => $task['Task']['id']
                                );
                                $contacts_lists_bd = $this->Task->TaskContactList->new_task_contact_list($task_contact_list);
                            }
                        }
                    }

                    if (!empty($data_files)) {
                        foreach ($data_files as $file) {
                            if ($file['error'] == ConstantsBooleans::NO) {
                                $check_file = FileManager::check_file($file);
                                if ($check_file == ConstantsFileErrorTypes::OK) {
                                    if (!$this->Task->TaskFile->saveFile($file, $task_id, ConstantsFileType::FILE)) {
                                        $files_bd = false;
                                    }
                                } else {
                                    break;
                                }
                            } elseif ($file['error'] == ConstantsFlag::ERROR_DIMENSIONS) {
                                $error_size = true;
                            }
                        }
                    }

                    if (isset($data_topics)) {
                        foreach ($data_topics as $topic) {
                            $topic_tmp = array(
                                'AppointmentTopic' => array(
                                    'task_id' => $task_id,
                                    'topic_id' => $topic
                                )
                            );
                            if (!$this->AppointmentTopic->add_appointment_topic($topic_tmp)) {
                                $topic_bd = false;
                            }
                        }
                    }

                    if ($contacts_lists_bd == ConstantsBooleans::YES && $topic_bd == ConstantsBooleans::YES && $files_bd == ConstantsBooleans::YES && $users_bd == ConstantsBooleans::YES) {
                        $user_creator_tmp = $this->User->find('first', array(
                            'conditions' => array(
                                'id' => CakeSession::read('Auth.User.id')
                            ),
                            'fields' => array(
                                'full_name'
                            )
                        ));
                        $user_creator = $user_creator_tmp['User']['full_name'];
                        if (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) {
                            $customer = $this->Task->getCustomerAppointmentByTaskId($task_id);
                            $this->request->data['Task']['customer'] = $customer;
                            $task_status = $this->Task->getTaskStatusByTaskId($task_id);
                            $this->request->data['Task']['status'] = $task_status['TaskStatus']['name' . __s()];
                            $subject = sprintf(__t('Alert.Edit_task_status_subject'), $task_status['TaskStatus']['name' . __s()], $customer . $user_creator);
                            $task_user = $this->Task->findById($task_id);
                            $this->request->data['Task']['user_creation_id'] = $task_user['Task']['user_creation_id'];
                            if (isset($this->request->data['Task']['check_send'])) {
                                if ($this->request->data['Task']['check_send'] == ConstantsBooleans::YES) {
                                    $this->sendDataAlert($this->request->data, $subject, __t('Alert.Edit_task_status'), $task_id);
                                }
                            }
                        }
                        if (!$check_file || $check_file == ConstantsFileErrorTypes::OK) {
                            if ($error_size) {
                                $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                            } elseif (!$files_bd) {
                                $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                            } else {
                                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                                $this->redirect($this->request->here);
                            }
                        } elseif ($check_file == ConstantsFileErrorTypes::SIZE_ERROR) {
                            $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                        } else {
                            $this->Session->setFlashError(__t(ConstantsMessages::FILE_ERROR_EXTENSION));
                        }
                    } else {
                        $this->Session->setFlashError(__t(ConstantsAlertsErrors::ERROR_GENERAL));
                    }
                } else {
                    $task_users = $this->TaskUser->getListUserByTask($task_bd['Task']['id']);
                    if (!empty($task_users)) {
                        $task_bd['TaskUser']['users'] =  $task_users;
                    }

                    $task_bd['ContactList']['contact_list_id'] = $task_contact_lists;
                    $task_bd['Task']['limit_date'] = ($task_bd['Task']['limit_date'] != '') ? Fecha::toFormatoVistaFecha($task_bd['Task']['limit_date']) : '';
                    $task_garages = $this->Garage->getListByTaskId($task_id);
                    $task_distributors = $this->Distributor->getListByTaskId($task_id);

                    $this->request->data = $task_bd;

                    $this->set(array(
                        'appointment_topics' => $this->AppointmentTopic->getListByAppointmentId($appointment_id),
                        'topics' => $this->DebriefTopic->find('list'),
                        'task_garages' => $task_garages,
                        'task_distributors' => $task_distributors
                    ));
                }
            } else {
                $task_users = $this->TaskUser->getListUserByTask($task_bd['Task']['id']);
                if (!empty($task_users)) {
                    $task_bd['TaskUser']['users'] =  $task_users;
                }

                $task_bd['ContactList']['contact_list_id'] = $task_contact_lists;
                $task_bd['Task']['limit_date'] = ($task_bd['Task']['limit_date'] != '') ? Fecha::toFormatoVistaFecha($task_bd['Task']['limit_date']) : '';
                $task_garages = $this->Garage->getListByTaskId($task_id);
                $task_distributors = $this->Distributor->getListByTaskId($task_id);
                $this->request->data = $task_bd;
                if (!empty($task_bd['Task']['user_assigned_id'])) {
                    $array_user_name = $this->User->getUsersNameByIdUser($task_bd['Task']['user_assigned_id']);
                }

                $this->set(array(
                    'appointment_topics' => $this->AppointmentTopic->getListByAppointmentId($appointment_id),
                    'topics' => $this->DebriefTopic->find('list'),
                    'task_garages' => $task_garages,
                    'task_distributors' => $task_distributors
                ));
            }

            $user = $this->Acceso->user();
            $userAagRegionId = $user['aag_region_id'];

            $this->set(array(
                'task' => $task_bd,
                'cancel_action' => $cancelAction,
                'task_users' => $task_users,
                'task_contact_lists' => $task_contact_lists,
                'task_files' => $task_files,
                'task_status_expired' => $task_status_expired,
                'contact_lists' => $contact_lists,
                'task_status_list' => $task_status_list,
                'debrief_tasks' => $debrief_tasks,
                'debrief_tasks_list' => $debrief_tasks_list,
                'debrief_topics' => $debrief_topics,
                'debrief_topics_list' => $debrief_topics_list,
                'user_aag_region_id' => $userAagRegionId,
                'array_user_name' => $array_user_name,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete CRM task.
     */
    public function delete_task($task_id)
    {
        $task = $this->Task->findById($task_id);
        if (
            $task &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $task_status = $this->Task->getTaskStatusByTaskId($task_id);
            $task['Task']['status'] = $task_status['TaskStatus']['name' . __s()];
            $user_creator_tmp = $this->User->find('first', array(
                'conditions' => array(
                    'id' => CakeSession::read('Auth.User.id')
                ),
                'fields' => array(
                    'full_name'
                )
            ));
            $user_creator = $user_creator_tmp['User']['full_name'];
            $customer = $this->Task->getCustomerAppointmentByTaskId($task_id);
            $task['Task']['customer'] = $customer;
            $subject = sprintf(__t('Alert.Deleted_task_subject'), $customer . $user_creator);
            $task_user = $this->Task->findById($task_id);
            $task['Task']['user_creation_id'] = $task_user['Task']['user_creation_id'];

            $users = $this->Task->User->listCompleteNameRegion($aagRegionId);
            $tasks_users = $this->Task->TaskUser->findAllByTaskId($task_id);
            $tasks_contact_list = $this->Task->TaskContactList->findAllByTaskId($task_id);
            $tasks_garages = $this->TaskGarage->findAllByTaskId($task_id);

            $task_status = $this->Task->getTaskStatusByTaskId($task_id);
            $task['Task']['status'] = $task_status['TaskStatus']['name' . __s()];
            foreach ($tasks_users as $task_users) {
                $task['User']['user_id'][] = $task_users['TaskUser']['user_id'];
                $this->Task->TaskUser->delete($task_users['TaskUser']['id']);
            }

            foreach ($tasks_contact_list as $task_contact_list) {
                $task['ContactList']['contact_list_id'][] = $task_contact_list['TaskContactList']['contact_list_id'];
                $this->Task->TaskContactList->delete($task_contact_list['TaskContactList']['id']);
            }

            $task_files = $this->Task->TaskFile->findAllByTaskId($task_id);
            foreach ($task_files as $task_file) {
                $this->Task->TaskFile->delete($task_file['TaskFile']['id']);
            }

            foreach ($tasks_garages as $task_garage) {
                $this->TaskGarage->delete($task_garage['TaskGarage']['id']);
            }
            if (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) {
                $this->sendDataAlert($task, $subject, __t('Alert.Delete_task'), $task_id, ConstantsBooleans::NO_ACTIVE);
            }

            $delete = $this->Task->delete($task_id);
            if (!$delete) {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
            }

            $searcher = CakeSession::read('Auth.User.query');
            $this->request->data['Search'] = $searcher;
            $tasks = $this->custom_pagination(
                $this->Task->_query('search'),
                $this->Task->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL
            );

            foreach ($tasks as $key => $task) {
                $tasks[$key]['Task']['creation_date'] = Fecha::toFormatoVistaFecha($task['Task']['creation_date']);
                $tasks[$key]['Task']['limit_date'] = Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
                $tasks[$key]['TaskContactList'] = $this->TaskContactList->findAllByTaskId($tasks[$key]['Task']['id']);
                $tasks[$key]['TaskGarage'] = $this->TaskGarage->getAllByTaskId($tasks[$key]['Task']['id']);
            }

            $this->set(array(
                'tasks' => $tasks,
                'users' => $users
            ));

            $this->redirect(
                array(
                    'controller' => 'tasks',
                    'action' => 'home',
                    '?' => array(
                        'assigned_to' => null,
                    ),
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete CRM task.
     */
    public function ajax_delete_task($task_id, $appointment_id = null)
    {
        $this->verify_ajax($this->request);
        $task = $this->Task->findById($task_id);

        if (
            $task &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $task_status = $this->Task->getTaskStatusByTaskId($task_id);
            $task['Task']['status'] = $task_status['TaskStatus']['name' . __s()];
            $user_creator_tmp = $this->User->find('first', array(
                'conditions' => array(
                    'id' => CakeSession::read('Auth.User.id')
                ),
                'fields' => array(
                    'full_name'
                )
            ));
            $user_creator = $user_creator_tmp['User']['full_name'];
            $customer = $this->Task->getCustomerAppointmentByTaskId($task_id);
            $task['Task']['customer'] = $customer;

            $subject = sprintf(__t('Alert.Deleted_task_subject'), $customer . $user_creator);
            $task_user = $this->Task->findById($task_id);
            $task['Task']['user_creation_id'] = $task_user['Task']['user_creation_id'];

            $tasks_users = $this->Task->TaskUser->findAllByTaskId($task_id);
            $tasks_contact_list = $this->Task->TaskContactList->findAllByTaskId($task_id);
            $tasks_garages = $this->TaskGarage->findAllByTaskId($task_id);
            $tasks_distributors = $this->TaskDistributor->findAllByTaskId($task_id);

            $task_files = $this->Task->TaskFile->findAllByTaskId($task_id);
            foreach ($task_files as $task_file) {
                $this->Task->TaskFile->delete($task_file['TaskFile']['id']);
            }

            foreach ($tasks_users as $task_users) {
                $task['User']['user_id'][] = $task_users['TaskUser']['user_id'];
                $this->Task->TaskUser->delete($task_users['TaskUser']['id']);
            }

            foreach ($tasks_contact_list as $task_contact_list) {
                $task['ContactList']['contact_list_id'][] = $task_contact_list['TaskContactList']['contact_list_id'];
                $this->Task->TaskContactList->delete($task_contact_list['TaskContactList']['id']);
            }

            foreach ($tasks_garages as $task_garage) {
                $this->TaskGarage->delete($task_garage['TaskGarage']['id']);
            }

            foreach ($tasks_distributors as $task_distributor) {
                $this->TaskDistributor->delete($task_distributor['TaskDistributor']['id']);
            }

            if (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) {
                $this->sendDataAlert($task, $subject, __t('Alert.Delete_task'), $task_id, ConstantsBooleans::NO_ACTIVE);
            }

            $delete = $this->Task->delete($task_id);
            if (!$delete) {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
            }

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete CRM subtask
     */
    public function ajax_delete_subtask()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $users = $this->Task->User->listCompleteNameRegion($aagRegionId);
            $status = $this->Task->TaskStatus->search_list();

            $searcher = $this->request->query;

            $conditions = $this->Task->conditions($searcher);
            if (in_array($this->Session->read('Auth.User.role_id'), array(ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))) {
                $conditions[] = array('GarageContactBdm.contact_id' => $this->Session->read('Auth.User.contact_id'));
                $conditions[] = array('DistributorContactBdm.contact_id' => $this->Session->read('Auth.User.contact_id'));
            }

            if ($this->request->query['task_garage_id'] != 'undefined') {
                if ($this->TaskGarage->delete($this->request->query['task_garage_id'])) {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
                } else {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
                }
            }
            if ($this->request->query['task_distributor_id'] != 'undefined') {
                if ($this->TaskDistributor->delete($this->request->query['task_distributor_id'])) {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
                } else {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
                }
            }

            $tasks = $this->custom_pagination(
                $this->Task->_query('search_subtask'),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL
            );

            foreach ($tasks as $key => $task) {
                $tasks[$key]['Task']['creation_date'] = Fecha::toFormatoVistaFecha($task['Task']['creation_date']);
                $tasks[$key]['Task']['limit_date'] = Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
                $tasks[$key]['TaskContactList'] = $this->TaskContactList->findAllByTaskId($tasks[$key]['Task']['id']);
            }

            $this->set(array(
                'tasks' => $tasks,
                'users' => $users,
                'status' => $status,
                'params' => http_build_query($this->request->query) . "\n"
            ));

            $this->layout = null;
            $this->render('../Tasks/Elements/results_table_subtasks');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX check CRM task.
     */
    public function ajax_check_task($task_id)
    {
        $this->verify_ajax($this->request);
        $task = $this->Task->findById($task_id);

        if (
            $task &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {

            //We calculate if this task has several garages associated with it.
            $tasks_garages = $this->TaskGarage->findAllByTaskId($task['Task']['id']);

            $task['Task']['task_status_id'] = ConstantsStatusTasks::COMPLETED;
            $task['Task']['resolve_date'] = date('Y-m-d H:i:s');
            $task_status = $this->Task->getTaskStatusByTaskId($task_id);
            $task['Task']['status'] = $task_status['TaskStatus']['name' . __s()];

            $tasks_users = $this->Task->TaskUser->find('all', array(
                'conditions' => array(
                    'task_id' => $task_id,
                )
            ));
            $tasks_contact_list = $this->Task->TaskContactList->find('all', array(
                'conditions' => array(
                    'task_id' => $task_id,
                )
            ));

            foreach ($tasks_users as $task_users) {
                $task['User']['user_id'][] = $task_users['TaskUser']['user_id'];
            }

            foreach ($tasks_contact_list as $task_contact_list) {
                $task['ContactList']['contact_list_id'][] = $task_contact_list['TaskContactList']['contact_list_id'];
            }

            if ($this->Task->edit_task($task, ConstantsBooleans::NO)) {
                if (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) {
                    $user_creator_tmp = $this->User->find('first', array(
                        'conditions' => array(
                            'id' => CakeSession::read('Auth.User.id')
                        ),
                        'fields' => array(
                            'full_name'
                        )
                    ));
                    $user_creator = $user_creator_tmp['User']['full_name'];
                    $customer = $this->Task->getCustomerAppointmentByTaskId($task_id);
                    $task['Task']['customer'] = $customer;
                    $task_status = $this->Task->getTaskStatusByTaskId($task_id);
                    $task['Task']['status'] = $task_status['TaskStatus']['name' . __s()];

                    $subject = sprintf(__t('Alert.Edit_task_status_subject'), $task_status['TaskStatus']['name' . __s()], $customer . $user_creator);
                    $task_user = $this->Task->findById($task_id);
                    $task['Task']['user_creation_id'] = $task_user['Task']['user_creation_id'];
                    $this->sendDataAlert($task, $subject, __t('Alert.Edit_task_status'), $task_id);
                }
            }

            if (!empty($tasks_garages) && count($tasks_garages) == 1) {
                $fields = array(
                    'TaskGarage' => array(
                        'id',
                        'completed'
                    )
                );
                $tasks_garages[0]['TaskGarage']['completed'] = ConstantsBooleans::YES;

                $bd = $this->TaskGarage->guardar($tasks_garages[0], $fields);
            }

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX check CRM task garage.
     */
    public function ajax_check_task_garage($task_id, $garage_id)
    {
        $this->verify_ajax($this->request);
        $task = $this->Task->findById($task_id);
        $task_garage = $this->TaskGarage->findByTaskIdAndGarageId($task_id, $garage_id);

        if (
            $task &&
            $task_garage &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {

            // We calculate if this task has several garages associated with it.
            // If this is the last subtask to complete, complete the parent task as well.
            $tasks_garages = $this->TaskGarage->findAllByTaskId($task['Task']['id']);

            $fields = array(
                'TaskGarage' => array(
                    'id',
                    'completed'
                )
            );
            $task_garage['TaskGarage']['completed'] = ConstantsBooleans::YES;

            $correct = true;
            $bd = $this->TaskGarage->guardar($task_garage, $fields);
            if (!$bd) {
                $correct = false;
            }

            $cont = 0;
            $task_completed = true;
            foreach ($tasks_garages as $task_garage) {
                if ($task_garage['TaskGarage']['completed'] == false) {
                    $cont++;
                }
                if ($cont > 1) { //Greater than one because the minimum its going to be the one that we save.
                    $task_completed = false;
                }
            }

            if ($task_completed) {
                $fields = array(
                    'Task' => array(
                        'id',
                        'task_status_id'
                    )
                );
                $task['Task']['task_status_id'] = ConstantsStatusTasks::COMPLETED;

                $bd = $this->Task->guardar($task, $fields);
                if (!$bd) {
                    $correct = false;
                }
            }

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX check CRM task distributors.
     */
    public function ajax_check_task_distributor($task_id, $distributor_id)
    {
        $this->verify_ajax($this->request);
        $task = $this->Task->findById($task_id);
        $task_distributor = $this->TaskDistributor->findByTaskIdAndDistributorId($task_id, $distributor_id);
        if (
            $task &&
            $task_distributor &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {

            // We calculate if this task has several distributors associated with it.
            // If this is the last subtask to complete, complete the parent task as well.
            $tasks_distributors = $this->TaskDistributor->findAllByTaskId($task['Task']['id']);

            $fields = array(
                'TaskDistributor' => array(
                    'id',
                    'completed'
                )
            );
            $task_distributor['TaskDistributor']['completed'] = ConstantsBooleans::YES;

            $correct = true;
            $bd = $this->TaskDistributor->guardar($task_distributor, $fields);
            if (!$bd) {
                $correct = false;
            }

            $cont = 0;
            $task_completed = true;
            foreach ($tasks_distributors as $task_distributor) {
                if ($task_distributor['TaskDistributor']['completed'] == false) {
                    $cont++;
                }
                if ($cont > 1) { //Greater than one because the minimum its going to be the one that we save.
                    $task_completed = false;
                }
            }

            if ($task_completed) {
                $fields = array(
                    'Task' => array(
                        'id',
                        'task_status_id'
                    )
                );
                $task['Task']['task_status_id'] = ConstantsStatusTasks::COMPLETED;

                $bd = $this->Task->guardar($task, $fields);
                if (!$bd) {
                    $correct = false;
                }
            }

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX uncheck CRM task.
     */
    public function ajax_uncheck_task($task_id)
    {
        $this->verify_ajax($this->request);
        $task = $this->Task->findById($task_id);

        if (
            $task &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {

            //We calculate if this task has several garages associated with it.
            $tasks_garages = $this->TaskGarage->findAllByTaskId($task['Task']['id']);

            $task['Task']['task_status_id'] = ConstantsStatusTasks::PENDING;
            $task['Task']['resolve_date'] = date('Y-m-d H:i:s');

            $tasks_users = $this->Task->TaskUser->find('all', array(
                'conditions' => array(
                    'task_id' => $task_id,
                )
            ));
            $tasks_contact_list = $this->Task->TaskContactList->find('all', array(
                'conditions' => array(
                    'task_id' => $task_id,
                )
            ));

            foreach ($tasks_users as $task_users) {
                $task['User']['user_id'][] = $task_users['TaskUser']['user_id'];
            }

            foreach ($tasks_contact_list as $task_contact_list) {
                $task['ContactList']['contact_list_id'][] = $task_contact_list['TaskContactList']['contact_list_id'];
            }

            if ($this->Task->edit_task($task, ConstantsBooleans::NO)) {
                if (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) {
                    $user_creator_tmp = $this->User->find('first', array(
                        'conditions' => array(
                            'id' => CakeSession::read('Auth.User.id')
                        ),
                        'fields' => array(
                            'full_name'
                        )
                    ));
                    $user_creator = $user_creator_tmp['User']['full_name'];
                    $customer = $this->Task->getCustomerAppointmentByTaskId($task_id);
                    $task['Task']['customer'] = $customer;
                    $task_status = $this->Task->getTaskStatusByTaskId($task_id);
                    $task['Task']['status'] = $task_status['TaskStatus']['name' . __s()];
                    $subject = sprintf(__t('Alert.Edit_task_status_subject'), $task_status['TaskStatus']['name' . __s()], $customer . $user_creator);
                    $task_user = $this->Task->findById($task_id);
                    $task['Task']['user_creation_id'] = $task_user['Task']['user_creation_id'];
                    $this->sendDataAlert($task, $subject, __t('Alert.Edit_task_status'), $task_id);
                }
            }

            if (!empty($tasks_garages) && count($tasks_garages) == 1) {
                $fields = array(
                    'TaskGarage' => array(
                        'id',
                        'completed'
                    )
                );
                $tasks_garages[0]['TaskGarage']['completed'] = ConstantsBooleans::NO;

                $bd = $this->TaskGarage->guardar($tasks_garages[0], $fields);
            }

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX uncheck CRM task garage.
     */
    public function ajax_uncheck_task_garage($task_id, $garage_id)
    {
        $this->verify_ajax($this->request);
        $task = $this->Task->findById($task_id);
        $task_garage = $this->TaskGarage->findByTaskIdAndGarageId($task_id, $garage_id);

        if (
            $task &&
            $task_garage &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {

            // We calculate if this task has several garages associated with it.
            // If this is the last subtask to mark the parent task as pending, mark the parent task as pending as well.
            $tasks_garages = $this->TaskGarage->findAllByTaskId($task['Task']['id']);

            $fields = array(
                'TaskGarage' => array(
                    'id',
                    'completed'
                )
            );
            $task_garage['TaskGarage']['completed'] = ConstantsBooleans::NO;

            $correct = true;
            $bd = $this->TaskGarage->guardar($task_garage, $fields);
            if (!$bd) {
                $correct = false;
            }

            $cont = 0;
            $task_uncompleted = true;
            foreach ($tasks_garages as $task_garage) {
                if ($task_garage['TaskGarage']['completed'] == true) {
                    $cont++;
                }
                if ($cont > 0) { //If there is a completed = true we already mark the task parent as pending
                    $task_uncompleted = false;
                }
            }

            if (!$task_uncompleted) {
                $fields = array(
                    'Task' => array(
                        'id',
                        'task_status_id'
                    )
                );
                $task['Task']['task_status_id'] = ConstantsStatusTasks::PENDING;

                $bd = $this->Task->guardar($task, $fields);
                if (!$bd) {
                    $correct = false;
                }
            }

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX check contact list for appointments CRM.
     */
    public function ajax_contact_list_form()
    {
        $this->verify_ajax($this->request);
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $this->autoRender = false;
            $data = $this->request->data;
            $contact_lists = $data['contact_list_id'];
            if (!empty($contact_lists)) {
                foreach ($contact_lists as $contact_list_id) {
                    $contacts = $this->ContactContactList->findAllByContactListId($contact_list_id);
                    foreach ($contacts as $contact) {
                        if ($contact['ContactContactList']['contact_id'] == CakeSession::read('Auth.User.contact_id')) {
                            return true;
                        }
                    }
                }
            }
            return false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX fill form for CRM appointments
     */
    public function ajax_fill_form($task_id)
    {
        $this->verify_ajax($this->request);
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $array_user_name = array();
            $debrief_tasks = $this->DebriefTask->find('all', array('order' => 'uses DESC'));
            $debrief_tasks_list = $this->DebriefTask->find('list', array('fields' => array(
                'id',
                'title' . __s()
            )));
            $debrief_topics = $this->DebriefTopic->find('all');
            $debrief_topics_list = $this->DebriefTopic->getList();
            $users = $this->Task->User->listCompleteNameRegion($aagRegionId);
            $contact_lists = $this->ContactList->find('list', array(
                'conditions' => array(
                    'OR' => array(
                        'user_id' => CakeSession::read('Auth.User.id'),
                        'global' => ConstantsBooleans::YES
                    )
                )
            ));

            $task_files = $this->Task->TaskFile->findAllByTaskId($task_id);
            $task_users = $this->Task->TaskUser->getListUserByTask($task_id);
            $task_garages = $this->Garage->getListByTaskId($task_id);
            $task_contact_lists = $this->Task->TaskContactList->getListContactListByTask($task_id);
            $task_status_list = $this->Task->TaskStatus->search_list_all();
            $task_status_expired = $this->Task->TaskStatus->getExpiredStatus();
            $topics = $this->DebriefTopic->getList();
            $task = $this->Task->getTask($task_id);

            if (!empty($task['Task']['user_assigned_id'])) {
                $array_user_name = $this->User->getUsersNameByIdUser($task['Task']['user_assigned_id']);
            }

            $task['Task']['creation_date'] = Fecha::toFormatoVistaFecha($task['Task']['creation_date']);
            $task['Task']['limit_date'] = Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
            $task['ContactList'] = $task_contact_lists;

            $contact_lists_id = key($task_contact_lists);

            $this->request->data = $task;

            $this->setGarageFilter();
            $this->setDistributorFilter();
            $config = CakeSession::read('Auth.User.Config');
            if (!$config[ConstantsConfig::TASK_DEADLINE]) {
                $this->Task->validator()->remove('limit_date');
            }

            $this->set(array(
                'task' => $task,
                'users' => $users,
                'task_users' => $task_users,
                'task_contact_lists' => $task_contact_lists,
                'task_files' => $task_files,
                'task_garages' => $task_garages,
                'topics' => $topics,
                'contact_lists' => $contact_lists,
                'contact_lists_id' => $contact_lists_id,
                'task_status_list' => $task_status_list,
                'task_status_expired' => $task_status_expired,
                'debrief_tasks' => $debrief_tasks,
                'debrief_tasks_list' => $debrief_tasks_list,
                'debrief_topics' => $debrief_topics,
                'debrief_topics_list' => $debrief_topics_list,
                'user_aag_region_id' => $aagRegionId,
                'array_user_name' => $array_user_name,
            ));

            $this->layout = null;
            $this->render('../Appointments/Elements/form_create_task_germany');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX save CRM appointment.
     */
    public function ajax_submit_form($appointment_id, $task_id = null)
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $users = $this->Task->User->listCompleteNameRegion($aagRegionId);
            $contact_lists = $this->ContactList->find('list', array(
                'conditions' => array(
                    'OR' => array(
                        'user_id' => CakeSession::read('Auth.User.id'),
                        'global' => ConstantsBooleans::YES
                    )
                )
            ));
            $task_status_list = $this->Task->TaskStatus->search_list();
            $task = array(
                'Task' => array(
                    'id' => $task_id,
                    'appointment_id' => $appointment_id,
                    'title' => isset($this->request->data['Task']['title']) ? $this->request->data['Task']['title'] : null,
                    'body' => $this->request->data['Task']['body'],
                    'limit_date' => $this->request->data['Task']['limit_date'],
                    'task_status_id' => $this->request->data['Task']['task_status_id'],
                    'user_assigned_id' => isset($this->request->data['Task']['user_assigned_id']) ? $this->request->data['Task']['user_assigned_id'] : null,
                    'reason' => $this->request->data['Task']['reason'],
                    'mandatory' => isset($this->request->data['Task']['mandatory']) ? $this->request->data['Task']['mandatory'] : null
                )
            );

            if (!is_null($task_id)) {
                $task_bd = $this->Task->findById($task_id);
                $task['Task']['id'] = $task_id;
                $task_contact_lists_bd = $this->TaskContactList->findAllByTaskId($task['Task']['id']);
                foreach ($task_contact_lists_bd as $task_contact_list_bd) {
                    $this->TaskContactList->delete($task_contact_list_bd['TaskContactList']['id']);
                }
                $task_users_bd = $this->TaskUser->findAllByTaskId($task['Task']['id']);
                foreach ($task_users_bd as $task_user_bd) {
                    $this->TaskUser->delete($task_user_bd['TaskUser']['id']);
                }
            }

            $config = CakeSession::read('Auth.User.Config');
            if (!$config[ConstantsConfig::TASK_DEADLINE]) {
                $this->Task->validator()->remove('limit_date');
            }
            $user = CakeSession::read('Auth.User.id');

            if ($this->request->data['Task']['send_to'] != ConstantsTasks::USER) {
                $this->Task->validator()->remove('user_assigned_id');
            }

            if ($this->Task->new_task($task, $user)) {
                if (is_null($task_id) || $task_id == 'undefined') {
                    $task_id = $this->Task->getLastInsertID();
                }

                $contacts_lists_bd = true;
                $users_bd = true;
                $files_bd = true;
                $data_user = $this->request->data['User'];
                $data_files = $this->request->data['Task']['files'];
                $data_contact_list = isset($this->request->data['ContactList']) ? $this->request->data['ContactList'] : array();
                $conditions = array();
                $joins = array();

                if ($this->request->data['Task']['send_to'] == ConstantsTasks::GARAGE) {
                    if (!empty($this->request->data['TaskGarage']['garage_id'])) {
                        foreach ($this->request->data['TaskGarage']['garage_id'] as $garage_id) {
                            $task_garage = array(
                                'TaskGarage' => array(
                                    'task_id' => $task_id,
                                    'garage_id' => $garage_id,
                                    'completed' => $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED ? ConstantsBooleans::YES : ConstantsBooleans::NO
                                )
                            );
                            $this->TaskGarage->saveMany($task_garage);
                        }
                    } elseif (empty($this->request->data['TaskGarage']['garage_id'])) {
                        $filters = $this->request->data['GarageFilter'];
                        if (!empty($filters['branch'])) {
                            $joins[] = array(
                                'alias' => 'GarageDistributor',
                                'table' => 'garages_distributors',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'GarageDistributor.garage_id = Garage.id',
                                ),
                            );
                            $conditions[0]['GarageDistributor.distributor_id'] = $filters['branch'];
                        }
                        if (
                            !empty($filters['BDM']) ||
                            ($this->Session->read('Auth.User.role_id') == ConstantsRoles::GPC_LOGISTICS_BDM) ||
                            ($this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG) ||
                            ($this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_TG)
                        ) {
                            $joins[] = array(
                                'alias' => 'GarageContactBdm',
                                'table' => 'garages_contacts_bdm',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'GarageContactBdm.garage_id = Garage.id',
                                ),
                            );
                            if (
                                $this->Session->read('Auth.User.role_id') == ConstantsRoles::GPC_LOGISTICS_BDM ||
                                $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG ||
                                $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_TG
                            ) {
                                $conditions[0]['GarageContactBdm.contact_id'] = array($this->Session->read('Auth.User.contact_id'));
                            } else {
                                $conditions[0]['GarageContactBdm.contact_id'] = array($filters['BDM']);
                            }
                        }
                        if (!isset($filters['RSM'])) {
                            $contacts_tmp = $this->Contact->findListByContactId(CakeSession::read('Auth.User.contact_id'));
                            $contacts = array();
                            $contacts[] = CakeSession::read('Auth.User.contact_id');
                            foreach ($contacts_tmp as $contact_tmp_id => $contact_tmp) {
                                $contacts[] = $contact_tmp_id;
                            }
                            $garages = $this->Garage->getGaragesByContacts($contacts, $aagRegionId);
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
                        } elseif (!empty($filters['RSM'])) {
                            $contacts_tmp = $this->Contact->findListByContactId($filters['RSM']);
                            $contacts = array();
                            $contacts[] = $filters['RSM'];
                            foreach ($contacts_tmp as $contact_tmp_id => $contact_tmp) {
                                $contacts[] = $contact_tmp_id;
                            }
                            $garages = $this->Garage->getGaragesByContacts($contacts, $aagRegionId);
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
                        if (!empty($filters['Customer_status'])) {
                            $conditions[0]['Garage.status'] = $filters['Customer_status'];
                        }
                        $garages = $this->Garage->getGarageTasks($conditions, $joins);
                        foreach ($garages as $garage) {
                            $task_garage = array(
                                'TaskGarage' => array(
                                    'task_id' => $task_id,
                                    'garage_id' => $garage['Garage']['id'],
                                    'completed' => $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED ? ConstantsBooleans::YES : ConstantsBooleans::NO
                                )
                            );
                            $this->TaskGarage->saveMany($task_garage);
                        }
                    }
                } elseif ($this->request->data['Task']['send_to'] == ConstantsTasks::DISTRIBUTOR) {
                    if (!empty($this->request->data['TaskDistributor']['distributor_id'])) {
                        foreach ($this->request->data['TaskDistributor']['distributor_id'] as $distributor_id) {
                            $task_distributor = array(
                                'TaskDistributor' => array(
                                    'task_id' => $task_id,
                                    'distributor_id' => $distributor_id,
                                    'completed' => $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED ? ConstantsBooleans::YES : ConstantsBooleans::NO
                                )
                            );
                            $this->TaskDistributor->saveMany($task_distributor);
                        }
                    } elseif (empty($this->request->data['TaskDistributor']['distributor_id'])) {
                        $filters = $this->request->data['DistributorFilter'];
                        if (
                            !empty($filters['BDM']) ||
                            ($this->Session->read('Auth.User.role_id') == ConstantsRoles::GPC_LOGISTICS_BDM) ||
                            ($this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG) ||
                            ($this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_TG)
                        ) {
                            $joins[] = array(
                                'alias' => 'DistributorContactBdm',
                                'table' => 'distributors_contacts_bdm',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'DistributorContactBdm.distributor_id = Distributor.id',
                                ),
                            );
                            if (
                                $this->Session->read('Auth.User.role_id') == ConstantsRoles::GPC_LOGISTICS_BDM ||
                                $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG ||
                                $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_TG
                            ) {
                                $conditions[0]['DistributorContactBdm.contact_id'] = array($this->Session->read('Auth.User.contact_id'));
                            } else {
                                $conditions[0]['DistributorContactBdm.contact_id'] = array($filters['BDM']);
                            }
                        }
                        if (!isset($filters['RSM'])) {
                            $contacts_tmp = $this->Contact->findListByContactId(CakeSession::read('Auth.User.contact_id'));
                            $contacts = array();
                            $contacts[] = CakeSession::read('Auth.User.contact_id');
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
                        } elseif (!empty($filters['RSM'])) {
                            $contacts_tmp = $this->Contact->findListByContactId($filters['RSM']);
                            $contacts = array();
                            $contacts[] = $filters['RSM'];
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
                        $distributors = $this->Distributor->getDistributorTasks($conditions, $joins);
                        foreach ($distributors as $distributor) {
                            $task_distributor = array(
                                'TaskDistributor' => array(
                                    'task_id' => $task_id,
                                    'distributor_id' => $distributor['Distributor']['id'],
                                    'completed' => $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED ? ConstantsBooleans::YES : ConstantsBooleans::NO
                                )
                            );
                            $this->TaskDistributor->saveMany($task_distributor);
                        }
                    }
                } elseif ($this->request->data['Task']['send_to'] == ConstantsTasks::USER) {
                    if (!empty($data_contact_list['contact_list_id'])) {
                        $task_contact_list_bd = array(
                            'TaskContactList' => array(
                                'contact_list_id' => $data_contact_list['contact_list_id'],
                                'task_id' => $task_id,
                            )
                        );
                        $contacts_lists_bd = $this->Task->TaskContactList->new_task_contact_list($task_contact_list_bd);
                    }

                    if (!empty($data_user['user_id'])) {
                        $users_bd = false;
                        if (is_array($data_user['user_id'])) {
                            foreach ($data_user['user_id'] as $user) {
                                $task_user = array(
                                    'user_id' => $user,
                                    'task_id' => $task_id
                                );

                                $users_bd = $this->Task->TaskUser->new_task_user($task_user);
                            }
                        } else {
                            $task_user = array(
                                'user_id' => $data_user['user_id'],
                                'task_id' => $task_id
                            );

                            $users_bd = $this->Task->TaskUser->new_task_user($task_user);
                        }
                    }
                }

                if (!empty($data_files)) {
                    foreach ($data_files as $file) {
                        if ($file['error'] == ConstantsBooleans::NO) {
                            $check_file = FileManager::check_file($file);
                            if ($check_file == ConstantsFileErrorTypes::OK) {
                                if (!$this->Task->TaskFile->saveFile($file, $task_id, ConstantsFileType::FILE)) {
                                    $files_bd = false;
                                }
                            } else {
                                $files_bd = false;
                            }
                        }
                    }
                }

                if ($contacts_lists_bd == ConstantsBooleans::YES && $users_bd == ConstantsBooleans::YES && $files_bd == ConstantsBooleans::YES) {
                    if (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) {
                        if (isset($task_bd) && !empty($task_bd)) {
                            $user_creator_tmp = $this->User->find('first', array(
                                'conditions' => array(
                                    'id' => CakeSession::read('Auth.User.id')
                                ),
                                'fields' => array(
                                    'full_name'
                                )
                            ));
                            $user_creator = $user_creator_tmp['User']['full_name'];
                            $customer = $this->Task->getCustomerAppointmentByTaskId($task_id);
                            $this->request->data['Task']['customer'] = $customer;
                            $task_user = $this->Task->findById($task_id);
                            $this->request->data['Task']['user_creation_id'] = $task_user['Task']['user_creation_id'];
                            $task_status = $this->Task->getTaskStatusByTaskId($task_id);
                            $subject = sprintf(__t('Alert.Edit_task_status_subject'), $task_status['TaskStatus']['name' . __s()], $customer . $user_creator);
                            $this->request->data['Task']['status'] = $task_status['TaskStatus']['name' . __s()];
                            $this->sendDataAlert($this->request->data, $subject, __t('Alert.Edit_task'), $task_id);
                        } else {
                            $user_creator_tmp = $this->User->find('first', array(
                                'conditions' => array(
                                    'id' => CakeSession::read('Auth.User.id')
                                ),
                                'fields' => array(
                                    'full_name'
                                )
                            ));
                            $user_creator = $user_creator_tmp['User']['full_name'];
                            $customer = $this->Task->getCustomerAppointmentByTaskId($task_id);
                            $this->request->data['Task']['customer'] = $customer;

                            $subject = sprintf(__t('Alert.New_task_subject'), $customer . $user_creator);
                            $task_user = $this->Task->findById($task_id);
                            $this->request->data['Task']['user_creation_id'] = $task_user['Task']['user_creation_id'];
                            $task_status = $this->Task->getTaskStatusByTaskId($task_id);
                            $this->request->data['Task']['status'] = $task_status['TaskStatus']['name' . __s()];
                            $this->sendDataAlert($this->request->data, $subject, __t('Alert.New_task'), $task_id);
                        }
                    }
                } else {
                    throw new Exception('error');
                }
            } else {
                throw new Exception('error');
            }

            $tasks = $this->Task->getAllByAppointmentIdOrderByLimitDate($appointment_id);
            foreach ($tasks as $key => $task) {
                $tasks_garages = $this->TaskGarage->findByTaskId($task['Task']['id']);
                $tasks_distributors = $this->TaskDistributor->findByTaskId($task['Task']['id']);

                if (empty($tasks_garages) && empty($tasks_distributors)) {
                    $tasks[$key]['Task']['creation_date'] = Fecha::toFormatoVistaFecha($task['Task']['creation_date']);
                    $tasks[$key]['Task']['limit_date'] = Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
                    $tasks[$key]['Task']['Users'] = array();
                    $tasks[$key]['Task']['ContactsLists'] = array();
                    $files = $this->Task->TaskFile->get_list($task['Task']['id']);
                    $users_tmp = $this->Task->TaskUser->getUsersByTask($task['Task']['id']);
                    $contacts_lists_tmp = $this->Task->TaskContactList->getContactsListsByTask($task['Task']['id']);
                    foreach ($users_tmp as $user) {
                        $tasks[$key]['Task']['Users'][] = $user['User']['name'] . " " . $user['User']['surname'];
                    }
                    foreach ($contacts_lists_tmp as $contact_list_tmp) {
                        $tasks[$key]['Task']['ContactsLists'][] = $contact_list_tmp['ContactList']['name'];
                    }
                    foreach ($files as $key_file => $file) {
                        $tasks[$key]['Task']['Files'][$key_file] = $file;
                    }
                } else {
                    unset($tasks[$key]);
                }
            }
            $this->set(array(
                'tasks' => $tasks,
                'users' => $users,
                'contact_lists' => $contact_lists,
                'task_status_list' => $task_status_list,
            ));

            $this->layout = null;
            $this->render('../Appointments/Elements/tasks');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX show CRM tasks.
     */
    public function ajax_render_tasks()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $appointment_id = $this->request->data['appointment_id'];
            $users = $this->Task->User->listCompleteNameRegion($aagRegionId);
            $tasks = $this->Task->getAllByAppointmentIdOrderByLimitDate($appointment_id);
            $task_status_list = $this->Task->TaskStatus->search_list();
            $contact_lists = $this->ContactList->find('list', array(
                'conditions' => array(
                    'OR' => array(
                        'user_id' => CakeSession::read('Auth.User.id'),
                        'global' => ConstantsBooleans::YES
                    )
                )
            ));

            foreach ($tasks as $key => $task) {
                $tasks_garages = $this->TaskGarage->findByTaskId($task['Task']['id']);

                if (empty($tasks_garages)) {
                    $tasks[$key]['Task']['creation_date'] = Fecha::toFormatoVistaFecha($task['Task']['creation_date']);
                    $tasks[$key]['Task']['limit_date'] = Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
                    $tasks[$key]['Task']['Users'] = array();
                    $tasks[$key]['Task']['ContactsLists'] = array();
                    $files = $this->Task->TaskFile->get_list($task['Task']['id']);
                    $users_tmp = $this->Task->TaskUser->getUsersByTask($task['Task']['id']);
                    $contacts_lists_tmp = $this->Task->TaskContactList->getContactsListsByTask($task['Task']['id']);
                    foreach ($users_tmp as $user) {
                        $tasks[$key]['Task']['Users'][] = $user['User']['name'] . " " . $user['User']['surname'];
                    }
                    foreach ($contacts_lists_tmp as $contact_list_tmp) {
                        $tasks[$key]['Task']['ContactsLists'][] = $contact_list_tmp['ContactList']['name'];
                    }
                    foreach ($files as $key_file => $file) {
                        $tasks[$key]['Task']['Files'][$key_file] = $file;
                    }
                } else {
                    unset($tasks[$key]);
                }
            }
            $this->set(array(
                'tasks' => $tasks,
                'users' => $users,
                'contact_lists' => $contact_lists,
                'task_status_list' => $task_status_list,
            ));

            $this->layout = null;
            $this->render('../Appointments/Elements/tasks');
        } else {
            throw new UnauthorizedException();
        }
    }

    public function ajax_add_debrief_task()
    {
        $this->verify_ajax($this->request);
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $this->setGarageFilter();
        $this->setDistributorFilter();
        $this->DebriefTask->increment_use($this->request->data['debrief_task_id']);
        $users = $this->Task->User->listCompleteNameRegion($aagRegionId);
        $contact_lists = $this->ContactList->find('list', array(
            'conditions' => array(
                'OR' => array(
                    'user_id' => CakeSession::read('Auth.User.id'),
                    'global' => ConstantsBooleans::YES
                )
            )
        ));
        $config = CakeSession::read('Auth.User.Config');
        if (!$config[ConstantsConfig::TASK_DEADLINE]) {
            $this->Task->validator()->remove('limit_date');
        }
        $task_status_list = $this->Task->TaskStatus->search_list();
        if (!isset($this->request->data['user_assigned_id'])) {
            $this->request->data['user_assigned_id'] = null;
        }
        $new_task = $this->Task->debrief_task_to_task($this->request->data['debrief_task_id'], $this->request->data['appointment_id'], $this->request->data['deadline'], $this->request->data['user_assigned_id']);

        $tasks = $this->Task->getAllByAppointmentIdOrderByLimitDate($this->request->data['appointment_id']);

        foreach ($tasks as $key => $task) {
            $tasks_garages = $this->TaskGarage->findByTaskId($task['Task']['id']);
            $tasks_distributors = $this->TaskDistributor->findByTaskId($task['Task']['id']);

            if (empty($tasks_garages) && empty($tasks_distributors)) {
                $tasks[$key]['Task']['creation_date'] = Fecha::toFormatoVistaFecha($task['Task']['creation_date']);
                $tasks[$key]['Task']['limit_date'] = Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
                $tasks[$key]['Task']['Users'] = array();
                $tasks[$key]['Task']['ContactsLists'] = array();
                $files = $this->Task->TaskFile->get_list($task['Task']['id']);
                $users_tmp = $this->Task->TaskUser->getUsersByTask($task['Task']['id']);
                $contacts_lists_tmp = $this->Task->TaskContactList->getContactsListsByTask($task['Task']['id']);
                foreach ($users_tmp as $user) {
                    $tasks[$key]['Task']['Users'][] = $user['User']['name'] . " " . $user['User']['surname'];
                }
                foreach ($contacts_lists_tmp as $contact_list_tmp) {
                    $tasks[$key]['Task']['ContactsLists'][] = $contact_list_tmp['ContactList']['name'];
                }
                foreach ($files as $key_file => $file) {
                    $tasks[$key]['Task']['Files'][$key_file] = $file;
                }
            } else {
                unset($tasks[$key]);
            }
        }

        $user = $this->Acceso->user();
        $userAagRegionId = $user['aag_region_id'];

        $this->set(array(
            'tasks' => $tasks,
            'users' => $users,
            'contact_lists' => $contact_lists,
            'task_status_list' => $task_status_list,
            'user_aag_region_id' => $userAagRegionId
        ));

        $this->layout = null;
        $this->render('../Appointments/Elements/tasks');
    }

    public function ajax_sort_assigned_tasks_by_recently_added($user_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $users = $this->User->listCompleteNameRegion($aagRegionId);
        $assigned_tasks = $this->Task->getAssignedToMeOpenTask($user_id);
        $assigned_tasks_deadline_null = $this->Task->getAssignedToMeOpenTaskDeadlineNull($user_id);

        $this->set(array(
            'assigned_tasks' => $assigned_tasks,
            'assigned_tasks_deadline_null' => $assigned_tasks_deadline_null,
            'user' => $user_id,
            'users' => $users,
        ));

        $this->layout = null;
        $this->render('../Dashboard/Elements/results_table_assigned');
    }

    public function ajax_sort_assigned_tasks_by_due_today($user_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $users = $this->User->listCompleteNameRegion($aagRegionId);
        $assigned_tasks = $this->Task->getAssignedToMyOpenTaskDeSortDueToday($user_id);

        $this->set(array(
            'assigned_tasks' => $assigned_tasks,
            'user' => $user_id,
            'users' => $users,
        ));

        $this->layout = null;
        $this->render('../Dashboard/Elements/results_table_assigned');
    }

    public function ajax_sort_assigned_tasks_by_due_week($user_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $users = $this->User->listCompleteNameRegion($aagRegionId);
        $assigned_tasks = $this->Task->getAssignedToMyOpenTaskDeSortDueThisWeek($user_id);

        $this->set(array(
            'assigned_tasks' => $assigned_tasks,
            'user' => $user_id,
            'users' => $users,
        ));

        $this->layout = null;
        $this->render('../Dashboard/Elements/results_table_assigned');
    }

    public function ajax_sort_created_tasks_by_recently_added($user_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $users = $this->User->listCompleteNameRegion($aagRegionId);
        $created_tasks = $this->Task->getCreatedToMeOpenTask($user);
        $created_tasks_deadline_null = $this->Task->getCreatedToMeOpenTaskDeadlineNull($user_id);

        $this->set(array(
            'created_tasks' => $created_tasks,
            'created_tasks_deadline_null' => $created_tasks_deadline_null,
            'user' => $user_id,
            'users' => $users,
        ));

        $this->layout = null;
        $this->render('../Dashboard/Elements/results_table_created');
    }

    public function ajax_sort_created_tasks_by_due_today($user_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $users = $this->User->listCompleteNameRegion($aagRegionId);
        $created_tasks = $this->Task->getCreatedToMyOpenTaskDeSortDueToday($user_id);

        $this->set(array(
            'created_tasks' => $created_tasks,
            'user' => $user_id,
            'users' => $users,
        ));

        $this->layout = null;
        $this->render('../Dashboard/Elements/results_table_created');
    }

    public function ajax_sort_created_tasks_by_due_week($user_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $users = $this->User->listCompleteNameRegion($aagRegionId);
        $created_tasks = $this->Task->getAssignedToMyOpenTaskDeSortDueThisWeek($user_id);

        $this->set(array(
            'created_tasks' => $created_tasks,
            'user' => $user_id,
            'users' => $users,
        ));

        $this->layout = null;
        $this->render('../Dashboard/Elements/results_table_created');
    }

    public function ajax_sort_assigned_group_tasks_by_recently_added($user)
    {
        $this->verify_ajax($this->request);
        $user_bd = $this->User->findById($user);
        $contact_id = $user_bd['User']['contact_id'];

        $assigned_group_tasks = $this->ContactContactList->getAssignedToMyGroupOpenTask($contact_id);
        $assigned_group_tasks_deadline_null = $this->ContactContactList->getAssignedToMyGroupOpenTaskDeadlineNull($contact_id);

        $this->set(array(
            'assigned_group_tasks' => $assigned_group_tasks,
            'assigned_group_tasks_deadline_null' => $assigned_group_tasks_deadline_null,
            'user' => $user,
        ));

        $this->layout = null;
        $this->render('../Dashboard/Elements/results_table_assigned_group');
    }

    public function ajax_sort_assigned_group_tasks_by_due_today($user)
    {
        $this->verify_ajax($this->request);
        $user_bd = $this->User->findById($user);
        $contact_id = $user_bd['User']['contact_id'];

        $assigned_group_tasks = $this->ContactContactList->getAssignedToMyGroupOpenTaskSortDueToday($contact_id);

        $this->set(array(
            'assigned_group_tasks' => $assigned_group_tasks,
            'user' => $user,
        ));

        $this->layout = null;
        $this->render('../Dashboard/Elements/results_table_assigned_group');
    }

    public function ajax_sort_assigned_group_tasks_by_due_week($user)
    {
        $this->verify_ajax($this->request);
        $user_bd = $this->User->findById($user);
        $contact_id = $user_bd['User']['contact_id'];

        $assigned_group_tasks = $this->ContactContactList->getAssignedToMyGroupOpenTaskSortDueThisWeek($contact_id);

        $this->set(array(
            'assigned_group_tasks' => $assigned_group_tasks,
            'user' => $user,
        ));

        $this->layout = null;
        $this->render('../Dashboard/Elements/results_table_assigned_group');
    }

    public function ajax_sort_assigned_customer_tasks_by_recently_added($user)
    {
        $this->verify_ajax($this->request);
        $user_bd = $this->User->findById($user);
        $contact_id = $user_bd['User']['contact_id'];

        $assigned_customers_tasks_tmp = $this->GarageContactBdm->getAssignedToMyCustomersOpenTask($contact_id);
        $assigned_customers_tasks_deadline_null_tmp = $this->GarageContactBdm->getAssignedToMyCustomersOpenTaskDeadlineNull($contact_id);

        //group the tasks that belong to the same garage
        $assigned_customers_tasks = array();
        foreach ($assigned_customers_tasks_tmp as $task) {
            if (in_array($task['Task']['id'], array_keys($assigned_customers_tasks))) {
                $assigned_customers_tasks[$task['Task']['id']]['Garage'][] = $task['Garage']['name'];
            } else {
                $assigned_customers_tasks[$task['Task']['id']] = $task;
            }
        }
        $assigned_customers_tasks_deadline_null = array();
        foreach ($assigned_customers_tasks_deadline_null_tmp as $task) {
            if (in_array($task['Task']['id'], array_keys($assigned_customers_tasks))) {
                $assigned_customers_tasks_deadline_null[$task['Task']['id']]['Garage'][] = $task['Garage']['name'];
            } else {
                $assigned_customers_tasks_deadline_null[$task['Task']['id']] = $task;
            }
        }

        $this->set(array(
            'assigned_customers_tasks' => $assigned_customers_tasks,
            'assigned_customers_tasks_deadline_null' => $assigned_customers_tasks_deadline_null,
            'user' => $user,
        ));

        $this->layout = null;
        $this->render('../Dashboard/Elements/results_table_assigned_customers');
    }

    public function ajax_sort_assigned_customer_tasks_by_due_today($user)
    {
        $this->verify_ajax($this->request);
        $user_bd = $this->User->findById($user);
        $contact_id = $user_bd['User']['contact_id'];

        $assigned_customers_tasks_tmp = $this->GarageContactBdm->getAssignedToMyCustomersOpenTaskSortDueDay($contact_id);

        //group the tasks that belong to the same garage
        $assigned_customers_tasks = array();
        foreach ($assigned_customers_tasks_tmp as $task) {
            if (in_array($task['Task']['id'], array_keys($assigned_customers_tasks))) {
                $assigned_customers_tasks[$task['Task']['id']]['Garage'][] = $task['Garage']['name'];
            } else {
                $assigned_customers_tasks[$task['Task']['id']] = $task;
            }
        }

        $this->set(array(
            'assigned_customers_tasks' => $assigned_customers_tasks,
            'user' => $user,
        ));

        $this->layout = null;
        $this->render('../Dashboard/Elements/results_table_assigned_customers');
    }

    public function ajax_sort_assigned_customer_tasks_by_due_week($user)
    {
        $this->verify_ajax($this->request);
        $user_bd = $this->User->findById($user);
        $contact_id = $user_bd['User']['contact_id'];

        $assigned_customers_tasks_tmp = $this->GarageContactBdm->getAssignedToMyCustomersOpenTaskSortDueThisWeek($contact_id);

        //Group the tasks that belong to the same garage
        $assigned_customers_tasks = array();
        foreach ($assigned_customers_tasks_tmp as $task) {
            if (in_array($task['Task']['id'], array_keys($assigned_customers_tasks))) {
                $assigned_customers_tasks[$task['Task']['id']]['Garage'][] = $task['Garage']['name'];
            } else {
                $assigned_customers_tasks[$task['Task']['id']] = $task;
            }
        }

        $this->set(array(
            'assigned_customers_tasks' => $assigned_customers_tasks,
            'user' => $user,
        ));

        $this->layout = null;
        $this->render('../Dashboard/Elements/results_table_assigned_customers');
    }

    public function ajax_assigned_group_task_de()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $users = $this->Task->User->listCompleteNameRegion($aagRegionId);
        $user = CakeSession::read('Auth.User.id');
        $user_bd = $this->User->findById($user);
        $contact_id = $user_bd['User']['contact_id'];
        $status = $this->Task->TaskStatus->search_list_all();

        $conditions = $this->setConditionsTaskDe($this->request->data);

        $conditions[] = array('ContactContactList.contact_id' => $contact_id);

        $assigned_group_tasks = $this->custom_pagination(
            $this->ContactContactList->_query('search_assigned_my_group'),
            $conditions,
            ConstantsPagination::SIZE_PAGE_SMALL,
            $this->ContactContactList
        );

        foreach ($assigned_group_tasks as $key => $task) {
            $assigned_group_tasks[$key]['TaskContactList'] = $this->TaskContactList->findAllByTaskId($assigned_group_tasks[$key]['Task']['id']);
            $assigned_group_tasks[$key]['TaskGarage']['completed'] = $this->TaskGarage->getTaskGarageCount($task['Task']['id']);
            $assigned_group_tasks[$key]['TaskGarage']['total'] = $this->TaskGarage->getAllTaskGarageCount($task['Task']['id']);
            if ($assigned_group_tasks[$key]['TaskGarage']['total'] == ConstantsBooleans::ACTIVE) {
                $garage_task = $this->TaskGarage->findByTaskId($task['Task']['id']);
                $assigned_group_tasks[$key]['Garage'] = $this->Garage->findById($garage_task['TaskGarage']['garage_id']);
            }
        }

        $this->set(array(
            'tasks' => $assigned_group_tasks,
            'user' => $user,
            'users' => $users,
            'status' => $status
        ));

        $this->layout = null;
        $this->render('../Tasks/Elements/results_table');
    }

    public function ajax_created_me_task_de()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $users = $this->Task->User->listCompleteNameRegion($aagRegionId);
        $user = CakeSession::read('Auth.User.id');
        $conditions = $this->setConditionsTaskDe($this->request->data);
        $conditions[] = array('Task.user_creation_id' => $this->Session->read('Auth.User.id'));
        $status = $this->Task->TaskStatus->search_list_all();
        if (isset($this->request->data['paginator_size'])) {
            CakeSession::write('Auth.Paginator.paginator_size', Numero::validateSessionNumeric($this->request->data['paginator_size']));
        }

        $tasks = $this->custom_pagination(
            $this->Task->_query('search_assigned_me_task'),
            $conditions,
            ConstantsPagination::SIZE_PAGE_SMALL
        );

        foreach ($tasks as $key => $task) {
            $tasks[$key]['TaskContactList'] = $this->TaskContactList->findAllByTaskId($tasks[$key]['Task']['id']);
            $tasks[$key]['TaskGarage']['completed'] = $this->TaskGarage->getTaskGarageCount($task['Task']['id']);
            $tasks[$key]['TaskGarage']['total'] = $this->TaskGarage->getAllTaskGarageCount($task['Task']['id']);
            if ($tasks[$key]['TaskGarage']['total'] == ConstantsBooleans::ACTIVE) {
                $garage_task = $this->TaskGarage->findByTaskId($task['Task']['id']);
                $tasks[$key]['Garage'] = $this->Garage->findById($garage_task['TaskGarage']['garage_id']);
            }
            $tasks[$key]['TaskDistributor']['completed'] = $this->TaskDistributor->getTaskDistributorCount($task['Task']['id']);
            $tasks[$key]['TaskDistributor']['total'] = $this->TaskDistributor->getAllTaskDistributorCount($task['Task']['id']);
            if ($tasks[$key]['TaskDistributor']['total'] == ConstantsBooleans::ACTIVE) {
                $distributor_task = $this->TaskDistributor->findByTaskId($task['Task']['id']);
                $tasks[$key]['Distributor'] = $this->Distributor->findById($distributor_task['TaskDistributor']['distributor_id']);
            }
        }

        $this->set(array(
            'tasks' => $tasks,
            'user' => $user,
            'users' => $users,
            'status' => $status
        ));

        $this->layout = null;
        $this->render('../Tasks/Elements/results_table');
    }

    public function ajax_assigned_customers_task_de()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $users = $this->Task->User->listCompleteNameRegion($aagRegionId);
        $user = CakeSession::read('Auth.User.id');
        $user_bd = $this->User->findById($user);
        $contact_id = $user_bd['User']['contact_id'];
        $status = $this->Task->TaskStatus->search_list_all();
        if (isset($this->request->data['paginator_size'])) {
            CakeSession::write('Auth.Paginator.paginator_size', Numero::validateSessionNumeric($this->request->data['paginator_size']));
        }

        $conditions = $this->setConditionsTaskDe($this->request->data);
        $conditions[] = array(
            'OR' => array(
                array('GarageContactBdm.contact_id' => $contact_id),
                array('DistributorContactBdm.contact_id' => $contact_id)
            )
        );

        $assigned_customer_tasks = $this->custom_pagination(
            $this->Task->_query('search_assigned_my_customers'),
            $conditions,
            ConstantsPagination::SIZE_PAGE_SMALL
        );

        foreach ($assigned_customer_tasks as $key => $task) {
            $assigned_customer_tasks[$key]['TaskContactList'] = $this->TaskContactList->findAllByTaskId($assigned_customer_tasks[$key]['Task']['id']);
            $assigned_customer_tasks[$key]['TaskGarage']['completed'] = $this->TaskGarage->getTaskGarageCount($task['Task']['id']);
            $assigned_customer_tasks[$key]['TaskGarage']['total'] = $this->TaskGarage->getAllTaskGarageCount($task['Task']['id']);
            if ($assigned_customer_tasks[$key]['TaskGarage']['total'] == ConstantsBooleans::ACTIVE) {
                $garage_task = $this->TaskGarage->findByTaskId($task['Task']['id']);
                $assigned_customer_tasks[$key]['Garage'] = $this->Garage->findById($garage_task['TaskGarage']['garage_id']);
            }
            $assigned_customer_tasks[$key]['TaskDistributor']['completed'] = $this->TaskDistributor->getTaskDistributorCount($task['Task']['id']);
            $assigned_customer_tasks[$key]['TaskDistributor']['total'] = $this->TaskDistributor->getAllTaskDistributorCount($task['Task']['id']);
            if ($assigned_customer_tasks[$key]['TaskDistributor']['total'] == ConstantsBooleans::ACTIVE) {
                $distributor_task = $this->TaskDistributor->findByTaskId($task['Task']['id']);
                $assigned_customer_tasks[$key]['Distributor'] = $this->Distributor->findById($distributor_task['TaskDistributor']['distributor_id']);
            }
        }

        $this->set(array(
            'tasks' => $assigned_customer_tasks,
            'user' => $user,
            'users' => $users,
            'status' => $status
        ));

        $this->layout = null;
        $this->render('../Tasks/Elements/results_table');
    }

    public function ajax_assigned_me_task_de()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $users = $this->Task->User->listCompleteNameRegion($aagRegionId);
        $user = CakeSession::read('Auth.User.id');
        $status = $this->Task->TaskStatus->search_list_all();
        if (isset($this->request->data['paginator_size'])) {
            CakeSession::write('Auth.Paginator.paginator_size', Numero::validateSessionNumeric($this->request->data['paginator_size']));
        }

        $conditions = $this->setConditionsTaskDe($this->request->data);
        $conditions[] = array('Task.user_assigned_id' => $user);

        $assigned_customer_tasks = $this->custom_pagination(
            $this->Task->_query('search_assigned_me_task'),
            $conditions,
            ConstantsPagination::SIZE_PAGE_SMALL,
            $this->Task
        );

        foreach ($assigned_customer_tasks as $key => $task) {
            $assigned_customer_tasks[$key]['TaskContactList'] = $this->TaskContactList->findAllByTaskId($assigned_customer_tasks[$key]['Task']['id']);
            $assigned_customer_tasks[$key]['TaskGarage']['completed'] = $this->TaskGarage->getTaskGarageCount($task['Task']['id']);
            $assigned_customer_tasks[$key]['TaskGarage']['total'] = $this->TaskGarage->getAllTaskGarageCount($task['Task']['id']);
            if ($assigned_customer_tasks[$key]['TaskGarage']['total'] == ConstantsBooleans::ACTIVE) {
                $garage_task = $this->TaskGarage->findByTaskId($task['Task']['id']);
                $assigned_customer_tasks[$key]['Garage'] = $this->Garage->findById($garage_task['TaskGarage']['garage_id']);
            }
            $assigned_customer_tasks[$key]['TaskDistributor']['completed'] = $this->TaskDistributor->getTaskDistributorCount($task['Task']['id']);
            $assigned_customer_tasks[$key]['TaskDistributor']['total'] = $this->TaskDistributor->getAllTaskDistributorCount($task['Task']['id']);
            if ($assigned_customer_tasks[$key]['TaskDistributor']['total'] == ConstantsBooleans::ACTIVE) {
                $distributor_task = $this->TaskDistributor->findByTaskId($task['Task']['id']);
                $assigned_customer_tasks[$key]['Distributor'] = $this->Distributor->findById($distributor_task['TaskDistributor']['distributor_id']);
            }
        }

        $this->set(array(
            'tasks' => $assigned_customer_tasks,
            'user' => $user,
            'users' => $users,

            'status' => $status
        ));

        $this->layout = null;
        $this->render('../Tasks/Elements/results_table');
    }

    public function ajax_all_task_de()
    {
        $this->verify_ajax($this->request);
        $user = $this->Acceso->user();
        $user_id = CakeSession::read('Auth.User.id');
        $aagRegionId = $user['aag_region_id'];

        $status = $this->Task->TaskStatus->search_list_all();
        if (isset($this->request->data['paginator_size'])) {
            CakeSession::write('Auth.Paginator.paginator_size', Numero::validateSessionNumeric($this->request->data['paginator_size']));
        }

        $conditions = $this->setConditionsTaskDe($this->request->data);
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::GPC_LOGISTICS_BDM ||
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_AAG ||
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_TG
        ) {
            $conditions[]['Task.user_assigned_id'] = array('0' => '46', '1' => '45');
        }
        $users = $this->User->listCompleteNameRegion($aagRegionId);

        $conditions['OR'] = [
            ['Garage.aag_region_id' => $aagRegionId],
            ['Distributor.aag_region_id' => $aagRegionId],
            ['User.aag_region_id' => $aagRegionId]
        ];

        $assigned_customer_tasks = $this->custom_pagination(
            $this->Task->_query('search_assigned_me_task'),
            $conditions,
            ConstantsPagination::SIZE_PAGE_SMALL,
            $this->Task
        );


        foreach ($assigned_customer_tasks as $key => $task) {
            $assigned_customer_tasks[$key]['TaskContactList'] = $this->TaskContactList->findAllByTaskId($assigned_customer_tasks[$key]['Task']['id']);
            $assigned_customer_tasks[$key]['TaskGarage']['completed'] = $this->TaskGarage->getTaskGarageCount($task['Task']['id']);
            $assigned_customer_tasks[$key]['TaskGarage']['total'] = $this->TaskGarage->getAllTaskGarageCount($task['Task']['id']);
            if ($assigned_customer_tasks[$key]['TaskGarage']['total'] == ConstantsBooleans::ACTIVE) {
                $garage_task = $this->TaskGarage->findByTaskId($task['Task']['id']);
                $assigned_customer_tasks[$key]['Garage'] = $this->Garage->findById($garage_task['TaskGarage']['garage_id']);
            }
            $assigned_customer_tasks[$key]['TaskDistributor']['completed'] = $this->TaskDistributor->getTaskDistributorCount($task['Task']['id']);
            $assigned_customer_tasks[$key]['TaskDistributor']['total'] = $this->TaskDistributor->getAllTaskDistributorCount($task['Task']['id']);
            if ($assigned_customer_tasks[$key]['TaskDistributor']['total'] == ConstantsBooleans::ACTIVE) {
                $distributor_task = $this->TaskDistributor->findByTaskId($task['Task']['id']);
                $assigned_customer_tasks[$key]['Distributor'] = $this->Distributor->findById($distributor_task['TaskDistributor']['distributor_id']);
            }
        }

        $this->set(array(
            'tasks' => $assigned_customer_tasks,
            'user' => $user_id,
            'users' => $users,
            'status' => $status
        ));

        $this->layout = null;
        $this->render('../Tasks/Elements/results_table');
    }

    private function setConditionsTaskDe($data)
    {
        $conditions = array();
        if ($data['title'] != '') {
            $conditions[] = array('Task.title LIKE' => '%' . $data['title'] . '%');
        }
        if (isset($data['user_creation_id']) && $data['user_creation_id'] != '') {
            $conditions[] = array('Task.user_creation_id' => $data['user_creation_id']);
        }
        if (isset($data['user_assigned_id']) && $data['user_assigned_id'] != '') {
            $conditions[] = array('Task.user_assigned_id' => $data['user_assigned_id']);
        }
        if ($data['task_status_id'] != '') {
            $conditions[] = array('Task.task_status_id' => $data['task_status_id']);
        }
        if ($data['limit_date_from'] != '') {
            $conditions[] = array('Task.limit_date >=' => Fecha::toFormatoBd($data['limit_date_from']));
        }
        if ($data['limit_date_to'] != '') {
            $conditions[] = array('Task.limit_date <=' => Fecha::toFormatoBd($data['limit_date_to']));
        }

        return $conditions;
    }

    /**
     * Send data alert.
     */
    private function sendDataAlert($task_data, $email_subject, $alert_body, $task_id, $deleted = null)
    {
        $contacts = array();

        $user_creator = $this->User->findById($task_data['Task']['user_creation_id']);
        $user_assigned = $this->User->findById($task_data['Task']['user_assigned_id']);
        $contacts[] = $this->Contact->findById($user_creator['User']['contact_id']);
        if (isset($user_assigned['User']['contact_id'])) {
            $contacts[] = $this->Contact->findById($user_assigned['User']['contact_id']);
        }

        if (isset($task_data['ContactList']['contact_list_id']) && is_array($task_data['ContactList']['contact_list_id'])) {
            foreach ($task_data['ContactList']['contact_list_id'] as $contact_list) {
                $contacts_contacts_list = $this->ContactContactList->findAllByContactListId($contact_list);
                foreach ($contacts_contacts_list as $contact_contact_list) {
                    $contact = $this->Contact->findById($contact_contact_list['ContactContactList']['contact_id']);
                    if (!empty($contact)) {
                        $contacts[] = $contact;
                    }
                }
            }
        }

        if (!empty($task_data['User']['user_id'])) {
            foreach ($task_data['User']['user_id'] as $user_id) {
                $user = $this->User->findById($user_id);
                if (!empty($user)) {
                    $contact = $this->Contact->findById($user['User']['contact_id']);
                    if (!empty($contact)) {
                        $contacts[] = $contact;
                    }
                }
            }
        }

        //Parent of user creator
        $contact_user_creator = $this->Contact->findById($user_creator['User']['contact_id']);
        if ($contact_user_creator['Contact']['contact_id']) {
            $contacts[] = $this->Contact->findById($contact_user_creator['Contact']['contact_id']);
        }

        $contacts = array_map("unserialize", array_unique(array_map("serialize", $contacts)));

        if (!isset($task_data['Task']['customer'])) {
            $task_data['Task']['customer'] = '';
        }

        $task_email = array(
            'title' => $task_data['Task']['title'],
            'deadline' => $task_data['Task']['limit_date'],
            'status' => $task_data['Task']['status'],
            'body' => $task_data['Task']['body'],
            'date' => date('d-m-Y'),
            'start_time' => date('H:m'),
            'end_time' => date('H:m'),
            'customer' => $task_data['Task']['customer']
        );

        if (is_null($deleted)) {
            $url = $this->Task->getUrlByTaskId($task_id);
            $task_email['link'] = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . $url;
            $task_tmp = $this->Task->findById($task_id);
            $user_tmp = $this->User->findById($task_tmp['Task']['user_creation_id']);
            $task_email['creator'] = $user_tmp['User']['name'] . " " . $user_tmp['User']['surname'];
        } else {
            $url = '';
            $user_tmp = $this->User->findById($task_data['Task']['user_creation_id']);
            $task_email['creator'] = $user_tmp['User']['name'] . " " . $user_tmp['User']['surname'];
        }

        if (isset($task_data['Task']['user_assigned_id']) && !empty($task_data['Task']['user_assigned_id'])) {
            $user_tmp = $this->User->findById($task_data['Task']['user_assigned_id']);
            $task_email['assigned_to'] = $user_tmp['User']['name'] . " " . $user_tmp['User']['surname'];
        }

        if (!empty($task_id)) {
            $task_email['files'] = $this->TaskFile->findAllByTaskId($task_id);
        }

        $this->Alert->new_alert($contacts, ConstantsAlerts::TASK, $alert_body, $email_subject, $url, $task_email, $task_id, $task_data['Task']['user_assigned_id'], $deleted);
    }

    public function ajax_get_users()
    {
        $this->verify_ajax($this->request);
        if (isset($this->request->data['contact_list_id']) && !empty($this->request->data['contact_list_id'])) {
            $users = $this->User->getUsersByContactListId($this->request->data['contact_list_id']);
            $this->set(array(
                'users' => $users,
            ));
        } else {
            $this->setUsers();
        }

        if (isset($this->request->data['action'])) {
            $this->set(array(
                'action' => $this->request->data['action'],
            ));
        }

        $this->set(array(
            'user_assigned_id' => isset($this->request->data['user_assigned_id']) ? $this->request->data['user_assigned_id'] : null
        ));

        $this->layout = false;
        $this->render('../Tasks/Elements/form_assigned_task');
    }

    /**
     * Task maintenance page.
     */
    public function maintenance_tasks()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::TASKS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $users = $this->User->listCompleteNameRegion($aagRegionId);
            $contact_lists = $this->ContactList->find('list', array(
                'conditions' => array(
                    'OR' => array(
                        'user_id' => CakeSession::read('Auth.User.id'),
                        'global' => ConstantsBooleans::YES
                    )
                ),
                'order' => array('name')
            ));

            $debrief_tasks = $this->custom_pagination(
                $this->DebriefTask->_query('search_maintenance_debrief_tasks'),
                array(),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'DebriefTask',
                null,
                'PaginatorOrderCustom'
            );

            foreach ($debrief_tasks as $key => $debrief_task) {
                $debrief_tasks[$key]['garages'] = null;
                $debrief_tasks[$key]['distributors'] = null;

                $debriefTaskGarageCount = $this->DebriefTaskGarage->findCountByDebriefTaskId($debrief_task['DebriefTask']['id']);
                if ($debriefTaskGarageCount > 0) {
                    if ($debriefTaskGarageCount > 1) {
                        $debrief_tasks[$key]['garages'] = __t('Task.Multiple_garages');
                    } else {
                        $debriefTaskGarage = $this->DebriefTaskGarage->getFirstByDebriefTaskId($debrief_task['DebriefTask']['id']);
                        $debrief_tasks[$key]['garages'] = $debriefTaskGarage['Garage']['name'];
                    }
                }
                $debriefTaskDistributorCount = $this->DebriefTaskDistributor->findCountByDebriefTaskId($debrief_task['DebriefTask']['id']);
                if ($debriefTaskDistributorCount > 0) {
                    if ($debriefTaskDistributorCount > 1) {
                        $debrief_tasks[$key]['distributors'] = __t('Task.Multiple_distributors');
                    } else {
                        $debriefTaskDistributor = $this->DebriefTaskDistributor->getFirstByDebriefTaskId($debrief_task['DebriefTask']['id']);
                        $debrief_tasks[$key]['distributors'] = $debriefTaskDistributor['Distributor']['name'];
                    }
                }
            }

            $this->set(
                array(
                    'debrief_tasks' => $debrief_tasks,
                    'users' => $users,
                    'contact_lists' => $contact_lists
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Topic maintenance page.
     */
    public function maintenance_topics()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::TOPICS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {

            $topics_in_use = $this->DebriefTopic->getListTopicsInUse();

            $debrief_topics = $this->custom_pagination(
                $this->DebriefTopic->_query('search_maintenance_debrief_topics'),
                array(),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'DebriefTopic',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(
                array(
                    'debrief_topics' => $debrief_topics,
                    'topics_in_use' => $topics_in_use,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create DebriefTask.
     */
    public function  add_debrief_task()
    {
        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::TASKS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $this->setGarageFilter();
            $this->setDistributorFilter();

            $contact_lists = $this->ContactList->find('list', array(
                'conditions' => array(
                    'OR' => array(
                        'user_id' => CakeSession::read('Auth.User.id'),
                        'global' => ConstantsBooleans::YES
                    ),
                    'aag_region_id' => $aagRegionId
                ),
                'order' => array('name')
            ));

            $users = $this->User->listCompleteNameRegion($aagRegionId);

            if (!$this->request->is('get')) {
                if ($this->request->data['DebriefTask']['send_to'] == ConstantsTasks::USER) {
                    if (
                        !isset($this->request->data['DebriefTask']['specific_user']) ||
                        (
                            isset($this->request->data['DebriefTask']['specific_user']) &&
                            $this->request->data['DebriefTask']['specific_user'] == '1' &&
                            (
                                (!isset($this->request->data['DebriefTask']['contact_list_id']) || empty($this->request->data['DebriefTask']['contact_list_id'])) &&
                                (!isset($this->request->data['DebriefTask']['user_assigned_id']) || empty($this->request->data['DebriefTask']['user_assigned_id']))
                            )
                        )
                    ) {
                        if (CakeSession::read('Auth.User.Config')[ConstantsConfig::ASSIGN_TO_GROUP]) {
                            $this->Session->setFlashError(__t('DebriefTask.assign_to_contact_lists_mandatory'));
                        } else {
                            $this->Session->setFlashError(__t('DebriefTask.assign_to_mandatory'));
                        }

                        $this->redirect(
                            array(
                                'controller' => 'tasks',
                                'action' => $this->request->data['DebriefTask']['DebriefAction']
                            )
                        );
                    }
                    if ($this->request->data['DebriefTask']['specific_user'] == '2') {
                        $this->request->data['DebriefTask']['user_assigned_id'] = CakeSession::read('Auth.User.id');
                    }
                }
                $debrief_task_bd = $this->DebriefTask->add($this->request->data);

                if ($debrief_task_bd) {
                    $debrief_task_id = $this->DebriefTask->getLastInsertId();
                    if ($this->request->data['DebriefTask']['send_to'] == ConstantsTasks::GARAGE) {
                        if (!empty($this->request->data['TaskGarage']['garage_id'])) {
                            $this->DebriefTaskGarage->add($this->request->data['TaskGarage']['garage_id'], $debrief_task_id);
                        } elseif (empty($this->request->data['TaskGarage']['garage_id'])) {
                            $conditions = array();
                            $joins = array();
                            $filters = $this->request->data['GarageFilter'];
                            if (!empty($filters['branch'])) {
                                $joins[] = array(
                                    'alias' => 'GarageDistributor',
                                    'table' => 'garages_distributors',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'GarageDistributor.garage_id = Garage.id',
                                    ),
                                );
                                $conditions[0]['GarageDistributor.distributor_id'] = $filters['branch'];
                            }
                            if (
                                !empty($filters['BDM']) ||
                                in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))
                            ) {
                                $joins[] = array(
                                    'alias' => 'GarageContactBdm',
                                    'table' => 'garages_contacts_bdm',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'GarageContactBdm.garage_id = Garage.id',
                                    ),
                                );
                                if (in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))) {
                                    $conditions[0]['GarageContactBdm.contact_id'] = array($this->Session->read('Auth.User.contact_id'));
                                } else {
                                    $conditions[0]['GarageContactBdm.contact_id'] = array($filters['BDM']);
                                }
                            }
                            if (!isset($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId(CakeSession::read('Auth.User.contact_id'));
                                $contacts = array();
                                $contacts[] = CakeSession::read('Auth.User.contact_id');
                                foreach ($contacts_tmp as $contact_tmp_id => $contact_tmp) {
                                    $contacts[] = $contact_tmp_id;
                                }
                                $garages = $this->Garage->getGaragesByContacts($contacts, $aagRegionId);
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
                            } elseif (!empty($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId($filters['RSM']);
                                $contacts = array();
                                $contacts[] = $filters['RSM'];
                                foreach ($contacts_tmp as $contact_tmp_id => $contact_tmp) {
                                    $contacts[] = $contact_tmp_id;
                                }
                                $garages = $this->Garage->getGaragesByContacts($contacts, $aagRegionId);
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
                            if (!empty($filters['Customer_status'])) {
                                $conditions[0]['Garage.status'] = $filters['Customer_status'];
                            }
                            $conditions[]['Garage.aag_region_id'] = $aagRegionId;
                            $garages = $this->Garage->getGarageTasks($conditions, $joins);
                            if (!empty($garages)) {
                                foreach ($garages as $garage) {
                                    $task_garage = array(
                                        'DebriefTaskGarage' => array(
                                            'debrief_task_id' => $debrief_task_id,
                                            'garage_id' => $garage['Garage']['id']
                                        )
                                    );
                                    $this->DebriefTaskGarage->saveMany($task_garage);
                                }
                            }
                        }
                    } elseif ($this->request->data['DebriefTask']['send_to'] == ConstantsTasks::DISTRIBUTOR) {
                        if (!empty($this->request->data['TaskDistributor']['distributor_id'])) {
                            $this->DebriefTaskDistributor->add($this->request->data['TaskDistributor']['distributor_id'], $debrief_task_id);
                        } elseif (empty($this->request->data['TaskDistributor']['distributor_id'])) {
                            $conditions = array();
                            $joins = array();
                            $filters = $this->request->data['DistributorFilter'];
                            if (
                                !empty($filters['BDM']) ||
                                in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))
                            ) {
                                $joins[] = array(
                                    'alias' => 'DistributorContactBdm',
                                    'table' => 'distributors_contacts_bdm',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'DistributorContactBdm.distributor_id = Distributor.id',
                                    ),
                                );
                                if (in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))) {
                                    $conditions[0]['DistributorContactBdm.contact_id'] = array($this->Session->read('Auth.User.contact_id'));
                                } else {
                                    $conditions[0]['DistributorContactBdm.contact_id'] = array($filters['BDM']);
                                }
                            }
                            if (!isset($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId(CakeSession::read('Auth.User.contact_id'));
                                $contacts = array();
                                $contacts[] = CakeSession::read('Auth.User.contact_id');
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
                            } elseif (!empty($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId($filters['RSM']);
                                $contacts = array();
                                $contacts[] = $filters['RSM'];
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
                            $conditions[]['Distributor.aag_region_id'] = $aagRegionId;
                            $distributors = $this->Distributor->getDistributorTasks($conditions, $joins);

                            if (!empty($distributors)) {
                                foreach ($distributors as $distributor) {
                                    $task_distributor = array(
                                        'DebriefTaskDistributor' => array(
                                            'debrief_task_id' => $debrief_task_id,
                                            'distributor_id' => $distributor['Distributor']['id']
                                        )
                                    );
                                    $this->DebriefTaskDistributor->saveMany($task_distributor);
                                }
                            }
                        }
                    }

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'tasks',
                            'action' => 'maintenance_tasks',
                        )
                    );
                }
            }

            $this->setVarCancel1();
            $this->set(array(
                'contact_lists' => $contact_lists,
                'users' => $users,
                'task_garages' => array(),
                'task_distributors' => array(),
                'user_aag_region_id' => $aagRegionId,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create DebriefTopic.
     */
    public function add_debrief_topic()
    {
        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::TOPICS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::TOPICS, ConstantsPermissionsGrouping::MAINTENANCE);
            if (!$this->request->is('get')) {
                $debrief_topic_bd = $this->DebriefTopic->add($this->request->data);

                if ($debrief_topic_bd) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'tasks',
                            'action' => 'maintenance_topics',
                        )
                    );
                }
            }

            $this->setVarCancel2();
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit DebriefTask.
     */
    public function edit_debrief_task($debrief_task_id)
    {
        $debrief_task = $this->DebriefTask->findById($debrief_task_id);
        if (
            $debrief_task &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::TASKS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $this->setGarageFilter();
            $this->setDistributorFilter();

            $contact_lists = $this->ContactList->find('list', array(
                'conditions' => array(
                    'OR' => array(
                        'user_id' => CakeSession::read('Auth.User.id'),
                        'global' => ConstantsBooleans::YES
                    ),
                    'aag_region_id' => $aagRegionId
                ),
                'order' => array('name')
            ));

            $users = $this->User->listCompleteNameRegion($aagRegionId);
            $task_garages = $this->DebriefTaskGarage->getListByDebriefTaskId($debrief_task_id);
            $task_distributors = $this->DebriefTaskDistributor->getListByDebriefTaskId($debrief_task_id);

            if (!$this->request->is('get')) {
                if ($this->request->data['DebriefTask']['send_to'] == ConstantsTasks::USER && $this->request->data['DebriefTask']['specific_user'] == '2') {
                    $this->request->data['DebriefTask']['user_assigned_id'] = CakeSession::read('Auth.User.id');
                }
                if (
                    $this->request->data['DebriefTask']['send_to'] == ConstantsTasks::USER &&
                    $this->request->data['DebriefTask']['specific_user'] == '1' &&
                    ((!isset($this->request->data['DebriefTask']['contact_list_id']) || empty($this->request->data['DebriefTask']['contact_list_id'])) &&
                        (!isset($this->request->data['DebriefTask']['user_assigned_id']) || empty($this->request->data['DebriefTask']['user_assigned_id'])))
                ) {
                    if (CakeSession::read('Auth.User.Config')[ConstantsConfig::ASSIGN_TO_GROUP]) {
                        $this->Session->setFlashError(__t('DebriefTask.assign_to_contact_lists_mandatory'));
                    } else {
                        $this->Session->setFlashError(__t('DebriefTask.assign_to_mandatory'));
                    }

                    $this->redirect(
                        array(
                            'controller' => 'tasks',
                            'action' => $this->request->data['DebriefTask']['DebriefAction'],
                            $debrief_task_id
                        )
                    );
                }
                $debrief_task_bd = $this->DebriefTask->edit($this->request->data);

                if ($debrief_task_bd) {
                    $this->DebriefTaskGarage->remove($debrief_task_bd['DebriefTask']['id']);
                    $this->DebriefTaskDistributor->remove($debrief_task_bd['DebriefTask']['id']);
                    if (!empty($this->request->data['TaskGarage']['garage_id'])) {
                        $this->DebriefTaskGarage->add($this->request->data['TaskGarage']['garage_id'], $debrief_task_bd['DebriefTask']['id']);
                    } elseif (!empty($this->request->data['TaskDistributor']['distributor_id'])) {
                        $this->DebriefTaskDistributor->add($this->request->data['TaskDistributor']['distributor_id'], $debrief_task_bd['DebriefTask']['id']);
                    }
                    if ($this->request->data['DebriefTask']['send_to'] == ConstantsTasks::GARAGE) {
                        if (!empty($this->request->data['TaskGarage']['garage_id'])) {
                            $this->DebriefTaskGarage->add($this->request->data['TaskGarage']['garage_id'], $debrief_task_id);
                        } elseif (empty($this->request->data['DebriefTaskGarage']['garage_id'])) {
                            $conditions = array();
                            $joins = array();
                            $filters = $this->request->data['GarageFilter'];
                            if (!empty($filters['branch'])) {
                                $joins[] = array(
                                    'alias' => 'GarageDistributor',
                                    'table' => 'garages_distributors',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'GarageDistributor.garage_id = Garage.id',
                                    ),
                                );
                                $conditions[0]['GarageDistributor.distributor_id'] = $filters['branch'];
                            }
                            if (
                                !empty($filters['BDM']) ||
                                in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))
                            ) {
                                $joins[] = array(
                                    'alias' => 'GarageContactBdm',
                                    'table' => 'garages_contacts_bdm',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'GarageContactBdm.garage_id = Garage.id',
                                    ),
                                );
                                if (in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))) {
                                    $conditions[0]['GarageContactBdm.contact_id'] = array($this->Session->read('Auth.User.contact_id'));
                                } else {
                                    $conditions[0]['GarageContactBdm.contact_id'] = array($filters['BDM']);
                                }
                            }
                            if (!isset($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId(CakeSession::read('Auth.User.contact_id'));
                                $contacts = array();
                                $contacts[] = CakeSession::read('Auth.User.contact_id');
                                foreach ($contacts_tmp as $contact_tmp_id => $contact_tmp) {
                                    $contacts[] = $contact_tmp_id;
                                }
                                $garages = $this->Garage->getGaragesByContacts($contacts, $aagRegionId);
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
                            } elseif (!empty($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId($filters['RSM']);
                                $contacts = array();
                                $contacts[] = $filters['RSM'];
                                foreach ($contacts_tmp as $contact_tmp_id => $contact_tmp) {
                                    $contacts[] = $contact_tmp_id;
                                }
                                $garages = $this->Garage->getGaragesByContacts($contacts, $aagRegionId);
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
                            if (!empty($filters['Customer_status'])) {
                                $conditions[0]['Garage.status'] = $filters['Customer_status'];
                            }
                            $conditions[]['Garage.aag_region_id'] = $aagRegionId;
                            $garages = $this->Garage->getGarageTasks($conditions, $joins);
                            if (!empty($garages)) {
                                foreach ($garages as $garage) {
                                    $task_garage = array(
                                        'DebriefTaskGarage' => array(
                                            'debrief_task_id' => $debrief_task_id,
                                            'garage_id' => $garage['Garage']['id']
                                        )
                                    );
                                    $this->DebriefTaskGarage->saveMany($task_garage);
                                }
                            }
                        }
                    } elseif ($this->request->data['DebriefTask']['send_to'] == ConstantsTasks::DISTRIBUTOR) {
                        if (!empty($this->request->data['TaskDistributor']['distributor_id'])) {
                            $this->DebriefTaskDistributor->add($this->request->data['TaskDistributor']['distributor_id'], $debrief_task_id);
                        } elseif (empty($this->request->data['DebriefTaskDistributor']['distributor_id'])) {
                            $conditions = array();
                            $joins = array();
                            $filters = $this->request->data['DistributorFilter'];
                            if (
                                !empty($filters['BDM']) ||
                                in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))
                            ) {
                                $joins[] = array(
                                    'alias' => 'DistributorContactBdm',
                                    'table' => 'distributors_contacts_bdm',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'DistributorContactBdm.distributor_id = Distributor.id',
                                    ),
                                );
                                if (in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))) {
                                    $conditions[0]['DistributorContactBdm.contact_id'] = array($this->Session->read('Auth.User.contact_id'));
                                } else {
                                    $conditions[0]['DistributorContactBdm.contact_id'] = array($filters['BDM']);
                                }
                            }
                            if (!isset($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId(CakeSession::read('Auth.User.contact_id'));
                                $contacts = array();
                                $contacts[] = CakeSession::read('Auth.User.contact_id');
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
                            } elseif (!empty($filters['RSM'])) {
                                $contacts_tmp = $this->Contact->findListByContactId($filters['RSM']);
                                $contacts = array();
                                $contacts[] = $filters['RSM'];
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
                            $conditions[]['Distributor.aag_region_id'] = $aagRegionId;
                            $distributors = $this->Distributor->getDistributorTasks($conditions, $joins);

                            if (!empty($distributors)) {
                                foreach ($distributors as $distributor) {
                                    $task_distributor = array(
                                        'DebriefTaskDistributor' => array(
                                            'debrief_task_id' => $debrief_task_id,
                                            'distributor_id' => $distributor['Distributor']['id']
                                        )
                                    );
                                    $this->DebriefTaskDistributor->saveMany($task_distributor);
                                }
                            }
                        }
                    }
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'tasks',
                            'action' => 'maintenance_tasks',
                        )
                    );
                }
            } else {
                $this->request->data = $debrief_task;
                $this->request->data['Garage']['garage_id'] = $this->DebriefTaskGarage->findListByDebriefTaskId($debrief_task['DebriefTask']['id']);
            }

            $this->setVarCancel1();
            $this->set(array(
                'contact_lists' => $contact_lists,
                'users' => $users,
                'task_garages' => $task_garages,
                'task_distributors' => $task_distributors,
                'debrief_task_id' => $debrief_task_id,
                'user_aag_region_id' => $aagRegionId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit DebriefTopic.
     */
    public function edit_debrief_topic($debrief_topic_id)
    {
        $debrief_topic = $this->DebriefTopic->findById($debrief_topic_id);
        if (
            $debrief_topic &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::TOPICS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $topic_in_use = $this->AppointmentTopic->findByTopicId($debrief_topic_id);

            if (!$this->request->is('get')) {
                $debrief_task_bd = $this->DebriefTopic->edit($this->request->data);
                if ($debrief_task_bd) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                }
            } else {
                $this->request->data = $debrief_topic;
            }

            $this->set(array(
                'topic_in_use' => $topic_in_use,
                'debrief_topic_id' => $debrief_topic_id
            ));

            $this->setVarCancel2();
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete DebriefTopic
     */
    public function delete_debrief_topic($debrief_topic_id)
    {
        $debriefTopic = $this->DebriefTopic->findById($debrief_topic_id);
        if (
            $debriefTopic &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::TOPICS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $error_topics = false;
            $appointments_topics = $this->AppointmentTopic->findAllByTopicId($debrief_topic_id);
            foreach ($appointments_topics as $appointment_topic) {
                if (!$this->AppointmentTopic->delete($appointment_topic['AppointmentTopic']['id'])) {
                    $error_topics = true;
                }
            }

            if (!$error_topics) {
                if ($this->DebriefTopic->delete($debrief_topic_id)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
                    $this->redirect(
                        array(
                            'controller' => 'tasks',
                            'action' => 'maintenance_topics',
                        )
                    );
                } else {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::BAD_DELETED));
                    $this->redirect(
                        array(
                            'controller' => 'tasks',
                            'action' => 'edit_debrief_topic',
                            $debrief_topic_id
                        )
                    );
                }
            } else {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::BAD_DELETED));
                $this->redirect(
                    array(
                        'controller' => 'tasks',
                        'action' => 'edit_debrief_topic',
                        $debrief_topic_id
                    )
                );
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete DebriefTask.
     */
    public function delete_debrief_task($debrief_task_id)
    {
        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::TASKS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $debrief_tasks_garages = $this->DebriefTaskGarage->findAllByDebriefTaskId($debrief_task_id);
            $debrief_tasks_distributors = $this->DebriefTaskDistributor->findAllByDebriefTaskId($debrief_task_id);

            $flag_error = false;
            foreach ($debrief_tasks_garages as $debrief_task_garage) {
                if (!$this->DebriefTaskGarage->delete($debrief_task_garage['DebriefTaskGarage']['id'])) {
                    $flag_error = true;
                }
            }
            foreach ($debrief_tasks_distributors as $debrief_task_distributor) {
                if (!$this->DebriefTaskDistributor->delete($debrief_task_distributor['DebriefTaskDistributor']['id'])) {
                    $flag_error = true;
                }
            }
            if (!$flag_error) {
                if ($this->DebriefTask->delete($debrief_task_id)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
                    $this->redirect(
                        array(
                            'controller' => 'tasks',
                            'action' => 'maintenance_tasks',
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
                    $this->redirect(
                        array(
                            'controller' => 'tasks',
                            'action' => 'edit_debrief_task',
                            $debrief_task_id
                        )
                    );
                }
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
                $this->redirect(
                    array(
                        'controller' => 'tasks',
                        'action' => 'edit_debrief_task',
                        $debrief_task_id
                    )
                );
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get CRM garage tasks.
     */
    public function ajax_garage_tasks()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $garage_id = $this->request->data['garage_id'];
            $tasks_garages = $this->TaskGarage->getAllByGarageIdAndNotCompleted($garage_id);
            $tasks_garages_total = count($this->TaskGarage->findAllByGarageId($garage_id));
            $this->set(
                array(
                    'tasks_garages' => $tasks_garages,
                    'tasks_garages_total' => $tasks_garages_total
                )
            );

            $this->layout = false;
            $this->render('../Appointments/Elements/ajax_garage_tasks');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get CRM distributor tasks.
     */
    public function ajax_distributor_tasks()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $distributor_id = $this->request->data['distributor_id'];
            $tasks_distributors = $this->TaskDistributor->getAllByDistributorIdAndNotCompleted($distributor_id);
            $tasks_distributors_total = count($this->TaskDistributor->findAllByDistributorId($distributor_id));

            $this->set(
                array(
                    'tasks_distributors' => $tasks_distributors,
                    'tasks_distributors_total' => $tasks_distributors_total
                )
            );

            $this->layout = false;
            $this->render('../Appointments/Elements/ajax_distributor_tasks');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX change CRM task garage.
     */
    public function ajax_change_task_garage()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $garage_id = $this->request->data['garage_id'];
            $task_id = $this->request->data['task_id'];
            if ($this->request->data['action'] == 'complete') {
                $completed_date = date('Y-m-d H:i:s');
            } else {
                $completed_date = null;
            }

            $task_garage = $this->TaskGarage->findByGarageIdAndTaskId($garage_id, $task_id);
            $task_garages = $this->TaskGarage->findAllByTaskId($task_id);
            $task = $this->Task->findById($task_id);

            $this->TaskGarage->change_status_and_date($task_garage, $completed_date);
            $this->Task->change_status($task_garage, $task, $task_garages);

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX change CRM task distributor.
     */
    public function ajax_change_task_distributor()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $distributor_id = $this->request->data['distributor_id'];
            $task_id = $this->request->data['task_id'];
            if ($this->request->data['action'] == 'complete') {
                $completed_date = date('Y-m-d H:i:s');
            } else {
                $completed_date = null;
            }

            $task_distributor = $this->TaskDistributor->findByDistributorIdAndTaskId($distributor_id, $task_id);
            $task_distributors = $this->TaskDistributor->findAllByTaskId($task_id);
            $task = $this->Task->findById($task_id);

            $this->TaskDistributor->change_status_and_date($task_distributor, $completed_date);
            $this->Task->change_status($task_distributor, $task, $task_distributors);

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    public function ajax_get_data_debrief_task()
    {
        $this->verify_ajax($this->request);
        $debrief_task_id = $this->request->data['debrief_task_id'];
        $debrief_task = $this->DebriefTask->findById($debrief_task_id);
        $debrief_task_garage = $this->DebriefTaskGarage->findAllByDebriefTaskId($debrief_task_id);

        $debrief_data = array(
            'garage' => count($debrief_task_garage),
            'contact_list' => $debrief_task['DebriefTask']['contact_list_id'],
            'user_assigned' => $debrief_task['DebriefTask']['user_assigned_id']
        );

        $this->autoRender = false;
        return json_encode($debrief_data);
    }

    private function setGarageFilter()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garage_branch = array();
        if (CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID ||  CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID) {
            $contact_tmp = $this->Contact->findById(CakeSession::read('Auth.User.Contact.id'));
            $garage_bdm =  array(
                $contact_tmp['Contact']['id'] => $contact_tmp['Contact']['full_name']
            );
        } else {
            $garage_bdm = $this->Contact->getListByRoleIdAndRegionId(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM), $aagRegionId);
        }

        if (in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))) {
            $contact_tmp = $this->Contact->findById(CakeSession::read('Auth.User.Contact.id'));
            $contact_contact_id = $this->Contact->findById($contact_tmp['Contact']['contact_id']);
            if (isset($contact_contact_id['Contact'])) {
                $garage_rsm = array(
                    $contact_contact_id['Contact']['id'] => $contact_contact_id['Contact']['full_name']
                );
            } else {
                $garage_rsm = null;
            }
        } else {
            $garage_rsm = $this->Contact->getListByPositionIdAndAagRegionId(array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID), $aagRegionId);
        }

        $garage_customer_status = Configure::read('Garage_Status') + Configure::read('Garage_Status_De');
        foreach ($garage_customer_status as $key => $status) {
            $garage_customer_status[$key] = __t($status);
        }

        $this->set(array(
            'garage_branch' => $garage_branch,
            'garage_bdm' => $garage_bdm,
            'garage_rsm' => $garage_rsm,
            'garage_customer_status' => $garage_customer_status,
        ));
    }

    private function setDistributorFilter()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $role_id = $user['role_id'];

        $distributor_branch = array();
        if (CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID ||  CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID) {
            $contact_tmp = $this->Contact->findById(CakeSession::read('Auth.User.contact_id'));
            $distributor_bdm =  array(
                $contact_tmp['Contact']['id'] => $contact_tmp['Contact']['full_name']
            );
        } else {
            if (in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))) {
                $contact_tmp = $this->Contact->findById(CakeSession::read('Auth.User.contact_id'));
                $distributor_bdm =  array(
                    $contact_tmp['Contact']['id'] => $contact_tmp['Contact']['full_name']
                );
                $contacts_children = $this->Contact->getContactChildren(CakeSession::read('Auth.User.contact_id'));
                if ($contacts_children) {
                    $contacts_tmp = array();
                    $distributors_contact =  $this->Contact->getContactChildren(CakeSession::read('Auth.User.contact_id'));
                    $contacts_tmp = $contacts_tmp + $distributors_contact;
                    $distributor_bdm = array_merge($distributor_bdm, $contacts_tmp);
                }
            } else {
                $distributor_bdm = $this->Contact->getBDMContact($aagRegionId);
            }
        }

        if (in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))) {
            $contact_tmp = $this->Contact->findById(CakeSession::read('Auth.User.contact_id'));
            $contact_contact_id = $this->Contact->findById($contact_tmp['Contact']['contact_id']);
            if (isset($contact_contact_id['Contact'])) {
                $distributor_rsm = array(
                    $contact_contact_id['Contact']['id'] => $contact_contact_id['Contact']['full_name']
                );
            } else {
                $distributor_rsm = null;
            }
        } else {
            $distributor_rsm = $this->Contact->getListByPositionIdAndAagRegionId(array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID), $aagRegionId);
        }

        $this->set(array(
            'distributor_branch' => $distributor_branch,
            'distributor_bdm' => $distributor_bdm,
            'distributor_rsm' => $distributor_rsm,
        ));
    }

    private function setVarCancel1()
    {
        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'tasks',
                'action' => 'maintenance_tasks',
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
        ));
    }

    private function setVarCancel2()
    {
        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'tasks',
                'action' => 'maintenance_topics',
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
        ));
    }

    private function setUsers()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $users = $this->User->listCompleteNameRegion($aagRegionId);

        $this->set(
            array(
                'users' => $users
            )
        );
    }

    private function setUsersDe()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $users = $this->User->getListRegionBDM($aagRegionId);

        return $users;
    }

    public function ajax_add_debrief_topic()
    {
        $this->verify_ajax($this->request);
        $debrief_topic_bd = $this->DebriefTopic->add($this->request->data);
        $this->autoRender = false;

        return json_encode($debrief_topic_bd);
    }
}
