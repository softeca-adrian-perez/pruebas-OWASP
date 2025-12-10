<?php
class AppointmentsController extends AppController
{
    public $uses = array(
        'Appointment',
        'Alert',
        'AppointmentContactList',
        'AppointmentContact',
        'AppointmentFeeling',
        'AppointmentFile',
        'AppointmentTopic',
        'AppointmentType',
        'AppointmentObjective',
        'AppointmentObjectiveComment',
        'Contact',
        'ContactContactList',
        'ContactList',
        'ContactRegion',
        'CustomerActivity',
        'Email',
        'EventType',
        'Garage',
        'GarageContactBdm',
        'GarageContactGeneralBranchManager',
        'GarageContactStaff',
        'GarageFigure',
        'GarageFigureDetail',
        'DebriefTask',
        'DebriefTopic',
        'Distributor',
        'DistributorActivityPrimary',
        'DistributorContactBdm',
        'DistributorContactGeneralBranchManager',
        'DistributorContactStaff',
        'DistributorCustomerActivity',
        'Network',
        'Task',
        'TaskContactList',
        'TaskDistributor',
        'TaskGarage',
        'User',
        'DistributorObjective',
        'TradingGroup',
        'DistributorActivity',
    );

    public $components = array(
        'Mpdf.Mpdf',
    );

    /**
     * Appointments home page.
     */
    public function home()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $url = Router::url(array(
                'controller' => 'appointments',
                'action' => 'home'
            ));
            CakeSession::write('Auth.User.appointment_url', $url); // T001 SECURITY - It is not changed

            $this->setUsers();
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Appointments home list page.
     */
    public function home_list()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $url = Router::url(array(
                'controller' => 'appointments',
                'action' => 'home_list'
            ));
            CakeSession::write('Auth.User.appointment_url', $url); // T001 SECURITY - It is not changed

            $appointmentStatus = $this->Appointment->AppointmentStatus->search_list();
            $appointmentTypes = $this->Appointment->AppointmentType->search_list();
            $feelingsList = $this->Appointment->AppointmentFeeling->search_list();
            $feelingsListImg = $this->Appointment->AppointmentFeeling->search_list_icon();
            $feelingsColors = $this->Appointment->AppointmentFeeling->find('list', array(
                'fields' => array(
                    'id',
                    'color'
                )
            ));
            $feelings = $this->Appointment->AppointmentFeeling->find('list', array(
                'fields' => array(
                    'id',
                    'icon'
                )
            ));

            $appointmentFillUp = array(
                '0' => __t('General.No'),
                '1' => __t('General.Yes'),
            );

            $status = $this->Appointment->AppointmentStatus->search_list();
            unset($status[ConstantsStatusAppointmentsDe::RUNNING]);

            $topics = $this->DebriefTopic->getList();

            if (!empty($this->request->query)) {
                $search = $this->request->query;
            } else {
                $search['from'] = date('d-m-Y');

                $search['appointment_status_id'] = array(
                    0 => ConstantsStatusAppointments::ACCOMPLISHED,
                    1 => ConstantsStatusAppointments::PLANNED,
                    3 => ConstantsStatusAppointments::PENDING,
                );
                $search['user_assigned_id'] = CakeSession::read('Auth.User.id');
            }
            $this->request->data['Search'] = $search;

            $conditions = $this->Appointment->conditions($search);
            $conditions[] = array(
                'OR' => array(
                    $this->User->viewUserAppointments($this->Acceso->user()),
                )
            );

            $appointments = $this->custom_pagination(
                $this->Appointment->_query('search_agenda_list'),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Appointment',
                null,
                'PaginatorOrderCustom'
            );

            foreach ($appointments as $key => $appointment) {
                $appointments[$key] = $this->Appointment->getAppointmentDataByAppointmentId($appointment['Appointment']['id']);
            }

            $this->setUsers();
            $this->set(
                array(
                    'appointments' => $appointments,
                    'appointment_status' => $appointmentStatus,
                    'appointment_types' => $appointmentTypes,
                    'appointment_fill_up' => $appointmentFillUp,
                    'feelings' => $feelings,
                    'feelings_list' => $feelingsList,
                    'feelings_list_img' => $feelingsListImg,
                    'feelings_colors' => $feelingsColors,
                    'status' => $status,
                    'topics' => $topics,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Events maintenance home page.
     */
    public function maintenance_home_events()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::EVENTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $condition = !empty($searcher['name' . __s()]) ? array('name' . __s() . ' LIKE' => '%' . $searcher['name' . __s()] . '%') : '';

            $eventsTypesLanguages = $this->AppointmentType->find(
                'all',
                array(
                    'conditions' => array(
                        'is_event' => ConstantsBooleans::YES,
                        $condition
                    ),
                    'fields' => array(
                        'id',
                        'name_en',
                        'name_fr',
                        'name_de',
                    ),
                    'order' => array(
                        'name_en'
                    ),
                )
            );

            $requiredEn = "<span class='c-fallo' style='margin-left:3px;'>*</span>";

            $this->set(
                array(
                    'events_types_languages' => $eventsTypesLanguages,
                    'selected_language' => 'name' . __s(),
                    'required_en' => $requiredEn,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create visit auto save.
     */
    public function add_auto_save()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            if ($this->request->data['Appointment']['date'] == null) {
                $this->layout = $this->autoRender = false;
                return 'Error-date';
            }

            if ($this->request->data['Appointment']['start_time'] == null || $this->request->data['Appointment']['start_time'] == '0:00') {
                $this->request->data['Appointment']['start_time'] = '08:00';
            }
            if ($this->request->data['Appointment']['end_time'] == null || $this->request->data['Appointment']['end_time'] == '0:00') {
                $this->request->data['Appointment']['end_time'] = '21:00';
            }

            $correctTime = true;
            $startTimeForm = $this->request->data['Appointment']['start_time'];
            $endTimeForm = $this->request->data['Appointment']['end_time'];

            if (strlen($startTimeForm) < 5) {
                $startTime = "0" . $startTimeForm;
            } elseif (strlen($startTimeForm) > 5) {
                $this->layout = $this->autoRender = false;
                return 'Error-time';
            } else {
                $startTime = $startTimeForm;
            }

            if (strlen($endTimeForm) < 5) {
                $endTime = "0" . $endTimeForm;
            } elseif (strlen($endTimeForm) > 5) {
                $this->layout = $this->autoRender = false;
                return 'Error-time';
            } else {
                $endTime = $endTimeForm;
            }
            if (isset($startTime) && isset($endTime) && $startTime > $endTime) {
                $this->layout = $this->autoRender = false;
                return 'Error-time';
            }

            if ($correctTime) {
                $this->Appointment->validator()->remove('end_date');
                $this->Appointment->validator()->remove('user_assigned_id');
                $appointmentBd = $this->Appointment->add_appointment($this->request->data, $user, true);
                if ($appointmentBd) {
                    $appointment_id = $appointmentBd['Appointment']['id'];
                    if (!empty($this->request->data['Appointment']['appointment_contact_lists'])) {
                        $this->Appointment->AppointmentContactList->add_appointment_contact_lists($this->request->data['Appointment'], $appointmentBd);
                    }
                }
                if ($appointmentBd) {
                    $this->layout = $this->autoRender = false;
                    return $appointment_id;
                }
                $this->layout = $this->autoRender = false;
                return false;
            } else {
                $this->layout = $this->autoRender = false;
                return false;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit visit auto save.
     */
    public function edit_auto_save($appointment_id)
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        $appointment = $this->Appointment->findById($appointment_id, 'id');
        if (
            $appointment &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            if (isset($this->request->data['Appointment']['visit_contact_name_disabled'])) {
                $this->request->data['Appointment']['visit_contact_name'] = $this->request->data['Appointment']['visit_contact_name_disabled'];
            }
            if (isset($this->request->data['Appointment']['visit_contact_id_disabled'])) {
                $this->request->data['Appointment']['visit_contact_id'] = $this->request->data['Appointment']['visit_contact_id_disabled'];
            }

            if ($this->request->data['Appointment']['date'] == null) {
                $this->layout = $this->autoRender = false;
                return 'Error-date';
            }

            if ($this->request->data['Appointment']['start_time'] == null || $this->request->data['Appointment']['start_time'] == '0:00') {
                $this->request->data['Appointment']['start_time'] = '08:00';
            }
            if ($this->request->data['Appointment']['end_time'] == null || $this->request->data['Appointment']['end_time'] == '0:00') {
                $this->request->data['Appointment']['end_time'] = '21:00';
            }

            $correctTime = true;
            $startTimeForm = $this->request->data['Appointment']['start_time'];
            $endTimeForm = $this->request->data['Appointment']['end_time'];

            if (strlen($startTimeForm) < 5) {
                $startTime = "0" . $startTimeForm;
            } elseif (strlen($startTimeForm) > 8) {
                $this->layout = $this->autoRender = false;
                return 'Error-time';
            } else {
                $startTime = $startTimeForm;
            }

            if (strlen($endTimeForm) < 5) {
                $endTime = "0" . $endTimeForm;
            } elseif (strlen($endTimeForm) > 8) {
                $this->layout = $this->autoRender = false;
                return 'Error-time';
            } else {
                $endTime = $endTimeForm;
            }

            if (isset($startTime) && isset($endTime) && $startTime > $endTime) {
                $this->layout = $this->autoRender = false;
                return 'Error-time';
            }

            if ($correctTime) {
                if (!isset($this->request->data['Appointment']['appointment_feeling_id'])) {
                    $this->request->data['Appointment']['appointment_feeling_id'] = '';
                }

                $appointmentBd = $this->Appointment->edit_appointment($this->request->data, $user);

                $this->AppointmentContactList->remove_contact_lists($appointment_id);
                $this->AppointmentContactList->resetAutoIncrement();
                if (!empty($this->request->data['Appointment']['appointment_contact_lists'])) {
                    //Delete contact lists associated with that appointment before adding a new one
                    $this->Appointment->AppointmentContactList->add_appointment_contact_lists($this->request->data['Appointment'], $appointmentBd);
                }

                $topicsOld = $this->AppointmentTopic->getListTopicsByAppointmentId($appointment_id);
                $this->AppointmentTopic->remove_appointment_topics($appointment_id);
                $this->AppointmentTopic->resetAutoIncrement();
                if (!empty($this->request->data['AppointmentTopic'])) {
                    $this->AppointmentTopic->add_appointment_topics($this->request->data['AppointmentTopic'], $appointment_id);
                }

                if (isset($this->request->data['AppointmentTopic']) && !empty($this->request->data['AppointmentTopic'])) {
                    foreach ($this->request->data['AppointmentTopic'] as $topic) {
                        if (!in_array($topic, $topicsOld)) {
                            $this->DebriefTopic->increment_use($topic);
                        }
                    }
                }

                if ($appointmentBd) {
                    $this->layout = $this->autoRender = false;
                    return $appointmentBd['Appointment']['id'];
                }
                $this->layout = $this->autoRender = false;
                return false;
            } else {
                $this->layout = $this->autoRender = false;
                return false;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create next appointment.
     */
    public function add_appointment_next()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $appointmentId = $this->Appointment->add_appointment_next($this->request->data);
            $this->layout = $this->autoRender = false;
            return $appointmentId;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create visit.
     */
    public function add($garage_id = null, $distributor_id = null)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            set_time_limit(18000);
            ini_set('memory_limit', '-1');
            $action = ConstantsActions::ADD;

            $url = Router::url(
                array(
                    'controller' => 'appointments',
                    'action' => 'add_auto_save',
                )
            );

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'appointments',
                    'action' => 'home',
                ),
            );

            if (!empty($this->request->data['Appointment']['garage_id'])) {
                $garage_id = $this->request->data['Appointment']['garage_id'];
            }

            $appointmentStatus = $this->Appointment->AppointmentStatus->search_list_appointment();
            $appointmentTypes = $this->Appointment->AppointmentType->search_list_appointment();
            $contactsNotifyTo = $this->Contact->getAllExceptGarageManagerAndDistributorManager($aagRegionId);
            $this->setGarageFilterTask();

            $garage = $this->Appointment->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);

            if (!empty($garage)) {
                $garagesList = array($garage['Garage']['id'] => $garage['Garage']['complete_search']);
            } else {
                $garagesList = array();
            }

            $garageManager = $this->GarageContactGeneralBranchManager->findManagerByGarageAndRole($garage_id, ConstantsRoles::GARAGE);
            $garagePrincipalImage = $this->Appointment->Garage->getPrincipalImageDatas($garage_id);
            $distributorPrincipalImage = $this->Appointment->Distributor->getPrincipalImageDatas($distributor_id);

            $distributor = $this->Appointment->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);
            $distributorManager = $this->DistributorContactGeneralBranchManager->findManagerByDistributorAndRole($distributor_id, ConstantsRoles::DISTRIBUTOR);

            $networks = $this->Network->getListByRegion($aagRegionId);
            $conditions = array();
            $conditions[] = array('Distributor.status' => ConstantsDistributorStatus::ACTIVE);

            $distributorsList = array();
            $distributors = array();

            if (!$this->request->is('get')) {
                if (isset($this->request->data['Appointment'])) {
                    $errorSize = false;
                    $checkFile = false;
                    $errorDimensionVirus = false;

                    if (isset($this->request->data['Appointment']['distributor_id']) && $this->request->data['Appointment']['distributor_id'] == '') {
                        if ($distributor_id == 'null') {
                            $this->request->data['Appointment']['distributor_id'] = null;
                        } else {
                            $this->request->data['Appointment']['distributor_id'] = $distributor_id;
                        }
                    }


                    $correctTime = true;
                    $startTimeForm = $this->request->data['Appointment']['start_time'];
                    $endTimeForm = $this->request->data['Appointment']['end_time'];
                    $figuresUk = true;

                    if (strlen($startTimeForm) < 5) {
                        $startTime = "0" . $startTimeForm;
                    } elseif (strlen($startTimeForm) > 5) {
                        $this->Session->setFlashError(__t('Appointment.Error_date_visit'));
                        $correctTime = false;
                    } else {
                        $startTime = $startTimeForm;
                    }

                    if (strlen($endTimeForm) < 5) {
                        $endTime = "0" . $endTimeForm;
                    } elseif (strlen($endTimeForm) > 5) {
                        $this->Session->setFlashError(__t('Appointment.Error_date_visit'));
                        $correctTime = false;
                    } else {
                        $endTime = $endTimeForm;
                    }
                    if (isset($startTime) && isset($endTime) && $startTime > $endTime) {
                        $this->Session->setFlashError(__t('Appointment.Error_date_visit'));
                        $correctTime = false;
                    }

                    if ($correctTime && $figuresUk) {
                        $this->Appointment->validator()->remove('end_date');

                        $tmp = $this->request->data;

                        $flagObjectives = true;
                        if (
                            $this->request->data['Appointment']['distributor_id'] &&
                            (
                                (isset($this->request->data['Appointment']['objectives']) && $this->request->data['Appointment']['objectives']) ||
                                (isset($this->request->data['Appointment']['objectives_personal']) && $this->request->data['Appointment']['objectives_personal'])
                            )
                        ) {
                            if (isset($this->request->data['Appointment']['objectives']) && $this->request->data['Appointment']['objectives']) {
                                foreach ($this->request->data['Appointment']['objectives'] as $obj) {
                                    if (
                                        ($obj['objective_id'] && !isset($obj['status'])) ||
                                        ($obj['objective_id'] && $obj['status'] == 0) ||
                                        ($obj['objective_id'] && ($obj['status'] != 0 && empty($obj['comment'])))
                                    ) {
                                        $flagObjectives = false;
                                    }
                                }
                            }

                            if (isset($this->request->data['Appointment']['objectives_personal']) && $this->request->data['Appointment']['objectives_personal']) {
                                foreach ($this->request->data['Appointment']['objectives_personal'] as $obj2) {
                                    if (
                                        ($obj2['objective_id'] && !isset($obj2['status'])) ||
                                        ($obj2['objective_id'] && $obj2['status'] == 0) ||
                                        ($obj2['objective_id'] && ($obj2['status'] != 0 && empty($obj2['comment'])))
                                    ) {
                                        $flagObjectives = false;
                                    }
                                }
                            }
                        }

                        if (!$flagObjectives) {
                            unset($this->request->data['btn_exit']);
                            $this->request->data['btn_no_exit'] = array();
                        }

                        $saveAndSend = isset($this->request->data['btn_exit']) ? true : null;
                        $appointmentBd = $this->Appointment->add_appointment($this->request->data, $user, true, $saveAndSend);
                        if ($appointmentBd) {
                            $appointment_id = $appointmentBd['Appointment']['id'];
                            if (!empty($this->request->data['PersonalObjective'])) {
                                foreach ($this->request->data['PersonalObjective'] as $personalObjective) {
                                    $appointmentObjectiveBd = array(
                                        'AppointmentObjectiveComment' => array(
                                            'appointment_id' => $this->Appointment->id,
                                            'objective_id' => $personalObjective['id'],
                                            'type' => ConstantsTypeAgreement::PERSONAL,
                                        )
                                    );
                                    if (is_numeric($personalObjective['id'])) {
                                        $appointmentObjective = $this->AppointmentObjectiveComment->new_appointment_objective($appointmentObjectiveBd);
                                    } else {
                                        $appointmentObjective = $this->AppointmentObjectiveComment->new_appointment_objective_noId($appointmentObjectiveBd);
                                    }
                                    if ($appointmentObjective) {
                                        echo ConstantsBooleans::YES;
                                    } else {
                                        echo ConstantsBooleans::NO;
                                    }
                                }
                            }
                            if (!empty($this->request->data['Appointment']['appointment_contact_lists'])) {
                                $this->Appointment->AppointmentContactList->add_appointment_contact_lists($this->request->data['Appointment'], $appointmentBd);
                            }
                            if (!empty($this->request->data['Appointment']['appointment_notify_to'])) {
                                $this->AppointmentContact->add_appointment_contact($this->request->data['Appointment']['appointment_notify_to'], $appointment_id);
                            }
                            if (isset($this->request->data['Appointments']['files']) && !empty($this->request->data['Appointments']['files'])) {
                                foreach ($this->request->data['Appointments']['files'] as $file) {
                                    if ($file['error'] == ConstantsBooleans::NO) {
                                        $checkFile = FileManager::check_file($file);
                                        if ($checkFile == ConstantsFileErrorTypes::OK) {
                                            if (!$this->Appointment->AppointmentFile->saveFile($file, $appointmentBd['Appointment']['id'], ConstantsFileType::FILE)) {
                                                $errorDimensionVirus = true;
                                            }
                                        } else {
                                            break;
                                        }
                                    } elseif ($file['error'] == ConstantsFlag::ERROR_DIMENSIONS) {
                                        $errorSize = true;
                                    }
                                }
                            }
                        }

                        if ($appointmentBd) {
                            if (
                                $appointmentBd['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::ACCOMPLISHED &&
                                (
                                    $appointmentBd['Appointment']['appointment_type_id'] == ConstantsTypesAppointments::VISIT ||
                                    $appointmentBd['Appointment']['appointment_type_id'] == ConstantsTypesAppointments::TEAMS
                                )
                            ) {
                                if ($appointmentBd['Appointment']['garage_id']) {
                                    $garageTmp = $this->Appointment->Garage->findById($appointmentBd['Appointment']['garage_id']);
                                    if (!$garageTmp['Garage']['last_visit'] || strtotime($garageTmp['Garage']['last_visit']) < strtotime(date('Y-m-d'))) {
                                        $this->Appointment->Garage->saveLastVisit($appointmentBd['Appointment']['garage_id'], $appointmentBd['Appointment']['date']);
                                    }
                                } elseif ($appointmentBd['Appointment']['distributor_id']) {
                                    $distributorTmp = $this->Appointment->Distributor->findById($appointmentBd['Appointment']['distributor_id']);
                                    if (!$distributorTmp['Distributor']['last_visit'] || strtotime($distributorTmp['Distributor']['last_visit']) < strtotime(date('Y-m-d'))) {
                                        $this->Appointment->Distributor->saveLastVisit($appointmentBd['Appointment']['distributor_id'], $appointmentBd['Appointment']['date']);
                                    }
                                }
                            }

                            if ($appointmentBd['Appointment']['distributor_id']) {
                                if (isset($this->request->data['Appointment']['objectives'])) {
                                    $this->AppointmentObjectiveComment->add_edit($this->request->data['Appointment']['objectives'], $appointmentBd['Appointment']['id'], $appointmentBd['Appointment']['distributor_id'], ConstantsTypeAgreement::MANAGEMENT);
                                }

                                if (isset($this->request->data['PersonalObjective'])) {
                                    $this->AppointmentObjectiveComment->add_edit($this->request->data['PersonalObjective'], $appointmentBd['Appointment']['id'], $appointmentBd['Appointment']['distributor_id'], ConstantsTypeAgreement::PERSONAL);
                                }
                            }

                            if (!$checkFile || $checkFile == ConstantsFileErrorTypes::OK) {
                                if (!$errorSize) {
                                    if (
                                        (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) &&
                                        isset($this->request->data['Appointment']['feedback']) && !empty($this->request->data['Appointment']['feedback'])
                                    ) {
                                        $userCreatorTmp = $this->User->find('first', array(
                                            'conditions' => array(
                                                'id' => CakeSession::read('Auth.User.id')
                                            ),
                                            'fields' => array(
                                                'full_name'
                                            )
                                        ));
                                        $userCreator = $userCreatorTmp['User']['full_name'];
                                        $customer = '';
                                        if (!is_null($this->request->data['Appointment']['garage_id']) && !empty($this->request->data['Appointment']['garage_id'])) {
                                            $customerTmp = $this->Garage->find('first', array(
                                                'conditions' => array(
                                                    'id' => $this->request->data['Appointment']['garage_id']
                                                ),
                                                'fields' => array(
                                                    'name'
                                                )
                                            ));
                                            $customer = $customerTmp['Garage']['name'] . ' - ';
                                        } elseif (!is_null($this->request->data['Appointment']['distributor_id']) && !empty($this->request->data['Appointment']['distributor_id'])) {
                                            $customerTmp = $this->Distributor->find('first', array(
                                                'conditions' => array(
                                                    'id' => $this->request->data['Appointment']['distributor_id']
                                                ),
                                                'fields' => array(
                                                    'name'
                                                )
                                            ));
                                            $customer = $customerTmp['Distributor']['name'] . ' - ';
                                        }
                                        $subject = sprintf(__t('Alert.New_appointment_subject'), $customer . $userCreator);
                                        $this->request->data['Appointment']['user_creation_id'] = CakeSession::read('Auth.User.id');
                                        if (isset($this->request->data['btn_exit'])) {
                                            $this->sendDataAlert($this->request->data, $subject, __t('Alert.New_appointment'), $appointment_id, $action);
                                        }
                                    }

                                    if (!$errorDimensionVirus) {
                                        $msg = h(sprintf(__t('Appointment.Well_add')));

                                        if (isset($tmp['btn_exit']) && $tmp['btn_exit'] && !$flagObjectives) {
                                            $msg = h(sprintf(__t('Appointment.No_objetives')));
                                            $this->Session->setFlashSuccess($msg);
                                        } else {
                                            $this->Session->setFlashSuccess($msg);
                                        }

                                        if (!isset($this->request->data['btn_no_exit'])) {
                                            $this->redirect(
                                                array(
                                                    'controller' => 'appointments',
                                                    'action' => 'home',
                                                    '?' => array(
                                                        'date' => Fecha::toFormatoVista($appointmentBd['Appointment']['date']),
                                                        'user' => $appointmentBd['Appointment']['user_assigned_id']
                                                    )
                                                )
                                            );
                                        } else {
                                            $this->redirect(
                                                array(
                                                    'controller' => 'appointments',
                                                    'action' => 'edit',
                                                    $this->Appointment->getLastInsertID()
                                                )
                                            );
                                        }
                                    } else {
                                        $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                                    }
                                } else {
                                    $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                                }
                            } elseif ($checkFile == ConstantsFileErrorTypes::SIZE_ERROR) {
                                $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                            } else {
                                $this->Session->setFlashError(__t(ConstantsMessages::FILE_ERROR_EXTENSION));
                            }
                        } else {
                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                        }
                    }
                }
            } else {
                $appointment = array('Appointment' => array());
                if (!empty($this->request->query['date'])) {
                    $appointment['Appointment']['date'] = $this->request->query['date'];
                }

                if (!empty($this->request->query['start_time'])) {
                    $appointment['Appointment']['start_time'] = $this->request->query['start_time'];
                }
                if (!empty($this->request->query['end_time'])) {
                    $appointment['Appointment']['end_time'] = $this->request->query['end_time'];
                }
                if (!empty($this->request->query['user'])) {
                    $appointment['Appointment']['user_assigned_id'] = $this->request->query['user'];
                }
                if ($garage_id != 'null') {
                    $appointment['Appointment']['garage_id'] = $garage_id;
                }
                if ($distributor_id != 'null') {
                    $appointment['Appointment']['distributor_id'] = $distributor_id;
                }
                if (isset($appointment['Appointment']['date'])) {
                    $appointment['Appointment']['date'] = Fecha::toFormatoVistaFecha($appointment['Appointment']['date']);
                }

                $this->request->data = $appointment;
            }

            $this->setVarForm();

            $this->set(array(
                'appointment_status' => $appointmentStatus,
                'appointment_types' => $appointmentTypes,
                'garage' => $garage,
                'garage_manager' => $garageManager,
                'garage_principal_image' => $garagePrincipalImage,
                'distributor_principal_image' => $distributorPrincipalImage,
                'distributor' => $distributor,
                'distributor_manager' => $distributorManager,
                'cancel_action' => $cancelAction,
                'networks' => $networks,
                'distributors' => $distributors,
                'distributors_list' => $distributorsList,
                'garages_list' => $garagesList,
                'url' => $url,
                'contacts_visits' => array(),
                'contacts_notify_to' => $contactsNotifyTo,
                'management_objectives' => $distributor_id ? $this->AppointmentObjective->getManagementObjectivesByType($user, $distributor_id) : array(),
                'personal_objectives' => $this->AppointmentObjective->getPersonalObjectivesByType($user),
                'disabled' => true,
                'hidden' => 'hidden',
                'objectives_status' => array(
                    '0' => __t('Objective.Pending'),
                    '1' => __t('General.Success'),
                    '2' => __t('Objective.Failed'),
                    '3' => __t('Objective.Requires_manager')
                ),
                'user_aag_region_id' => $aagRegionId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX function to see completed garage task.
     */
    public function ajax_get_completed_garage_task()
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
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $garageId = $this->request->data['garage_id'];
            $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

            if (isset($this->request->data['page'])) {
                $this->params['named'] = array('page' => $this->request->data['page']);
            }

            $tasksGaragesCompleted = array();
            if ($garageId != null) {
                $conditions = array(
                    'Garage.id' => $garageId,
                    'TaskGarage.completed' => ConstantsBooleans::YES,
                );
                $tasksGaragesCompleted = $this->custom_pagination(
                    $this->TaskGarage->_query('completed_garage_tasks'),
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'TaskGarage'
                );
            }

            $this->set(
                array(
                    'garage' => $garage,
                    'tasks_garages_completed' => $tasksGaragesCompleted,
                )
            );

            $this->layout = null;
            $this->render('/Appointments/Elements/modal_completed_garage_task');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX function to load more completed garage task.
     */
    public function ajax_load_more_completed_garage_task()
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
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $garageId = $this->request->data['garage_id'];
            $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

            if (isset($this->request->data['page'])) {
                $this->params['named'] = array('page' => $this->request->data['page']);
            }

            $tasksGaragesCompleted = array();
            if ($garageId != null) {
                $conditions = array(
                    'Garage.id' => $garageId,
                    'TaskGarage.completed' => ConstantsBooleans::YES,
                );
                $tasksGaragesCompleted = $this->custom_pagination(
                    $this->TaskGarage->_query('completed_garage_tasks'),
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'TaskGarage'
                );
            }

            $this->set(
                array(
                    'garage' => $garage,
                    'tasks_garages_completed' => $tasksGaragesCompleted,
                )
            );

            $this->layout = null;
            $this->render('/Appointments/Elements/load_more_completed_garage_task');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX function to save a follow up visit.
     */
    public function ajax_save_follow_up_visit()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $appointment['Appointment']['date'] = $this->request->data['date'];
            $appointment['Appointment']['start_time'] = $this->request->data['start_time'];
            $appointment['Appointment']['end_time'] = $this->request->data['end_time'];
            $appointment['Appointment']['garage_id'] = ($this->request->data['garage_id'] != '') ? $this->request->data['garage_id'] : null;
            $appointment['Appointment']['distributor_id'] = ($this->request->data['distributor_id'] != '') ? $this->request->data['distributor_id'] : null;
            $appointment['Appointment']['appointment_status_id'] = isset($this->request->data['appointment_status_id']) ? $this->request->data['appointment_status_id'] : null;
            $appointment['Appointment']['appointment_type_id'] = ConstantsTypesAppointments::VISIT;
            $appointment['Appointment']['user_assigned_id'] = $this->request->data['user_assigned_id'];

            $this->Appointment->add_appointment($appointment, $user, true);

            $garageId = $appointment['Appointment']['garage_id'];
            $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

            $distributorId = $appointment['Appointment']['distributor_id'];
            $distributor = $this->Distributor->findByIdAndAagRegionId($distributorId, $aagRegionId);

            if ($garageId != null) {
                $customerVisits = $this->Appointment->getAllAppointmentByGarageLimitDateToday($garageId);
            } elseif ($distributorId != null) {
                $customerVisits = $this->Appointment->getAllAppointmentByDistributorLimitDateToday($distributorId);
            } else {
                $customerVisits = array();
            }

            $users = $this->User->listCompleteNameRegion($aagRegionId);

            $this->set(
                array(
                    'garage' => $garage,
                    'distributor' => $distributor,
                    'customer_visits' => $customerVisits,
                    'users' => $users,
                )
            );

            $this->layout = null;
            $this->render('/Appointments/Elements/modal_follow_up_visit');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create event.
     */
    public function add_event()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $action = ConstantsActions::ADD;
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'appointments',
                    'action' => 'home',
                ),
            );

            $eventsTypes = $this->AppointmentType->search_list_event();

            $this->setGarageFilterTask();
            $this->setDistributorFilterTask();

            if (!$this->request->is('get')) {
                $user = $this->Acceso->user();

                if ($this->request->data['Appointment']['end_date'] == null) {
                    $this->request->data['Appointment']['end_date'] = $this->request->data['Appointment']['date'];
                }

                $correctTime = true;
                $startTimeForm = $this->request->data['Appointment']['start_time'];
                $endTimeForm = $this->request->data['Appointment']['end_time'];
                $startDate = $this->request->data['Appointment']['date'];
                $endDate = $this->request->data['Appointment']['end_date'];
                $correctTitle = true;
                $title = $this->request->data['Appointment']['title'];

                if (!empty($endDate) && $endDate < $startDate) {
                    $correctTime = false;
                }
                if ($startDate == $endDate) {
                    if (strlen($startTimeForm) < 5) {
                        $startTime = "0" . $startTimeForm;
                    } elseif (strlen($startTimeForm) > 5) {
                        $correctTime = false;
                    } else {
                        $startTime = $startTimeForm;
                    }

                    if (strlen($endTimeForm) < 5) {
                        $endTime = "0" . $endTimeForm;
                    } elseif (strlen($endTimeForm) > 5) {
                        $correctTime = false;
                    } else {
                        $endTime = $endTimeForm;
                    }

                    if (isset($startTime) && isset($endTime) && isset($startDate) && isset($endDate) && ($startTime > $endTime || $startDate > $endDate)) {
                        $correctTime = false;
                    }
                }
                if (empty(trim($title))) {
                    $correctTitle = false;
                }

                if ($correctTime && $correctTitle) {
                    $appointmentBd = $this->Appointment->add_appointment($this->request->data, $user, true);
                    if ($appointmentBd) {
                        $appointment_id = $this->Appointment->getLastInsertID();
                        $appointmentContacts = true;
                        if (!empty($this->request->data['Appointment']['appointment_contact_lists'])) {
                            $appointmentContacts = $this->Appointment->AppointmentContactList->add_appointment_contact_lists($this->request->data['Appointment'], $appointmentBd);
                        }
                    }

                    if ($appointmentBd && $appointmentContacts) {
                        if (
                            (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) &&
                            isset($this->request->data['Appointment']['feedback']) && !empty($this->request->data['Appointment']['feedback'])
                        ) {
                            $userCreatorTmp = $this->User->find('first', array(
                                'conditions' => array(
                                    'id' => CakeSession::read('Auth.User.id')
                                ),
                                'fields' => array(
                                    'full_name'
                                )
                            ));
                            $userCreator = $userCreatorTmp['User']['full_name'];
                            $subject = sprintf(__t('Alert.New_event_subject'), $userCreator);
                            $this->request->data['Appointment']['user_creation_id'] = CakeSession::read('Auth.User.id');
                            $this->sendDataAlertEvent($this->request->data, $subject, __t('Alert.New_event'), $appointment_id, $action);
                        }

                        $msg = h(sprintf(__t('Event.Well_add')));
                        $this->Session->setFlashSuccess($msg);

                        if (!isset($this->request->data['btn_no_exit'])) {
                            $this->redirect(
                                array(
                                    'controller' => 'appointments',
                                    'action' => 'home',
                                    '?' => array(
                                        'date' => Fecha::toFormatoVista($appointmentBd['Appointment']['date']),
                                        'user' => $appointmentBd['Appointment']['user_assigned_id']
                                    )
                                )
                            );
                        } else {
                            $this->redirect(
                                array(
                                    'controller' => 'appointments',
                                    'action' => 'edit_event',
                                    $this->Appointment->getLastInsertID()
                                )
                            );
                        }
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } elseif ($correctTitle) {
                    $this->Session->setFlashError(__t('Appointment.Error_event_date_visit'));
                } elseif ($correctTime) {
                    $this->Session->setFlashError(__t('Validation.Mandatory_to_choose_a_title'));
                }
            } else {
                $appointment = array('Appointment' => array());
                if (!empty($this->request->query['date'])) {
                    $appointment['Appointment']['date'] = $this->request->query['date'];
                }
                if (!empty($this->request->query['start_time'])) {
                    $appointment['Appointment']['start_time'] = $this->request->query['start_time'];
                }
                if (!empty($this->request->query['end_time'])) {
                    $appointment['Appointment']['end_time'] = $this->request->query['end_time'];
                }
                if (!empty($this->request->query['user'])) {
                    $appointment['Appointment']['user_assigned_id'] = $this->request->query['user'];
                }

                $this->request->data = $appointment;
            }

            $this->setVarForm();
            $this->set(
                array(
                    'event_types' => $eventsTypes,
                    'cancel_action' => $cancelAction,
                    'user_aag_region_id' => $user['aag_region_id']
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit visit.
     */
    public function edit($appointment_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $appointment = $this->Appointment->findById($appointment_id);
        if (
            $appointment &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE)) &&
            $appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::EVENT
        ) {
            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $url = Router::url(
                array(
                    'controller' => 'appointments',
                    'action' => 'edit_auto_save',
                    $appointment_id
                )
            );
            $action = ConstantsActions::EDIT;

            $tasksGaragesAll = $this->TaskGarage->getAllByGarageIdAndNotCompleted($appointment['Appointment']['garage_id']);
            $tasksGaragesTotal = count($this->TaskGarage->findAllByGarageId($appointment['Appointment']['garage_id']));
            $contactsNotifyTo = $this->Contact->getAllExceptGarageManagerAndDistributorManager($aagRegionId);

            $topics = $this->DebriefTopic->getList();
            $this->setGarageFilterTask();
            $this->setDistributorFilterTask();

            $tasksGarages = array();
            if (!$appointment) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_VISIT));
                $this->redirect(
                    array(
                        'controller' => 'appointments',
                        'action' => 'home',
                    )
                );
            }

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'appointments',
                    'action' => 'home',
                    '?' => array(
                        'date' => Fecha::toFormatoVista($appointment['Appointment']['date']),
                        'user' => $appointment['Appointment']['user_assigned_id']
                    )
                ),
            );

            $appointmentStatus = $this->Appointment->AppointmentStatus->search_list_appointment();
            $appointmentTopics = $this->AppointmentTopic->findAllByAppointmentId($appointment_id);

            $taskStatusExpired = $this->Task->TaskStatus->getExpiredStatus();

            $appointmentTypes = $this->Appointment->AppointmentType->search_list_appointment();
            $appointmentFiles = $this->Appointment->AppointmentFile->findAllByAppointmentId($appointment_id);
            $appointmentCreatedBy = $this->User->getCompleteName($appointment['Appointment']['user_creation_id']);

            $garage = $this->Appointment->Garage->findByIdAndAagRegionId($appointment['Appointment']['garage_id'], $aagRegionId);
            if ($appointment['Appointment']['garage_id'] && $appointment['Appointment']['garage_id'] != '' && $appointment['Appointment']['garage_id'] != 'false') {
                $contactsVisits = $this->Contact->getContactsVisitByGarage($appointment['Appointment']['garage_id']);
            } elseif ($appointment['Appointment']['distributor_id'] && $appointment['Appointment']['distributor_id'] != '' && $appointment['Appointment']['distributor_id'] != 'false') {
                $contactsVisits = $this->Contact->getContactsVisitByDistributor($appointment['Appointment']['distributor_id']);
            } else {
                $contactsVisits = array();
            }

            $saleFiguresDetails = array();
            $saleFigures = array();
            $garagesList = array();
            $garageAppointmentPrevious = array();
            $conditions = array();
            $distributorsList = array();
            $distributorManager = array();

            if (!empty($garage)) {
                $garagesDistributors = $this->Appointment->Garage->getGarageDistributorData($appointment['Appointment']['garage_id']);
                $garagesNetworks = $this->Appointment->Garage->getGarageNetworkData($appointment['Appointment']['garage_id']);
                $garage['GarageDistributor'] = $garagesDistributors;
                $garage['GarageNetwork'] = $garagesNetworks;

                $garageSalesFiguresDetails = $this->GarageFigureDetail->findByCustomerNo($garage['Garage']['g_number_id']);
                if ($garageSalesFiguresDetails) {
                    $saleFiguresDetails = json_decode($garageSalesFiguresDetails['GarageFigureDetail']['figures_details'], true);
                }

                $garageAppointmentPrevious = $this->Appointment->getPreviousByGarage($garage['Garage']['id']);

                $garageSalesFigures = $this->GarageFigure->findByCustomerNo($garage['Garage']['g_number_id']);
                if ($garageSalesFigures) {
                    $saleFigures = json_decode($garageSalesFigures['GarageFigure']['figures'], true);
                }
                $garagesList = array($garage['Garage']['id'] => $garage['Garage']['complete_search']);
            }
            $garageManager = $this->GarageContactGeneralBranchManager->findManagerByGarageAndRole($appointment['Appointment']['garage_id'], ConstantsRoles::GARAGE);
            $garagePrincipalImage = $this->Appointment->Garage->getPrincipalImageDatas($appointment['Appointment']['garage_id']);
            $distributor = $this->Appointment->Distributor->findById($appointment['Appointment']['distributor_id']);
            $distributorPrincipalImage = $this->Appointment->Distributor->getPrincipalImageDatas($appointment['Appointment']['distributor_id']);

            if (!empty($distributor)) {
                if ($distributor['Distributor']['trading_group_id'] != null) {
                    $distributor_trading_group = $this->Appointment->Distributor->TradingGroup->getTradingGroupById($distributor['Distributor']['trading_group_id']);
                    $distributor['TradingGroup'] = $distributor_trading_group['TradingGroup'];
                }
                $distributorActivities = $this->DistributorCustomerActivity->findAllByDistributorId($distributor['Distributor']['id']);
                $distributor['DistributorActivity'] = $distributorActivities;
                $customerActivities = $this->CustomerActivity->search_list();
                $conditions = array(
                    'Distributor.id' => $distributor['Distributor']['id']
                );
                $this->set(array(
                    'customer_activities' => $customerActivities
                ));

                $distributorConditions = array(
                    'Distributor.id = DistributorContactBdm.distributor_id'
                );
                $distributorsList = $this->Appointment->Distributor->find(
                    'list',
                    array(
                        'joins' => array(
                            array(
                                'alias' => 'DistributorContactBdm',
                                'table' => 'distributors_contacts_bdm',
                                'type' => 'LEFT',
                                'conditions' => $distributorConditions
                            ),
                        ),
                        'conditions' => $conditions,
                        'order' => 'name',
                        'fields' => array(
                            'id',
                            'complete_search'
                        )
                    )
                );
                $distributorManager = $this->DistributorContactGeneralBranchManager->findManagerByDistributorAndRole($appointment['Appointment']['distributor_id'], ConstantsRoles::DISTRIBUTOR);
            }

            $taskStatusList = $this->Task->TaskStatus->search_list_all();
            $taskUsers = null;
            $taskContactLists = null;

            $debriefTasks = $this->DebriefTask->getDebriefTask();
            $debriefTasksList = $this->DebriefTask->find('list', array('fields' => array(
                'id',
                'title' . __s()
            )));
            $debriefTopics = $this->DebriefTopic->getDebriefTopic();
            $debriefTopicsList = $this->DebriefTopic->getList();

            $comments = $this->Appointment->AppointmentComment->find('all', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id
                ),
                'order' => array(
                    'creation_date' => 'DESC'
                ),
                'limit' => ConstantsPagination::SIZE_COMMENTS
            ));
            $commentsNumber = $this->Appointment->AppointmentComment->find('count', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id
                )
            ));

            $loadMore = count($comments) == $commentsNumber ? ConstantsBooleans::NO : ConstantsBooleans::YES;

            $tasks = $this->Appointment->Task->getAllByAppointmentIdOrderByLimitDate($appointment_id);
            foreach ($tasks as $key => $task) {
                $tasksGarages = $this->TaskGarage->findByTaskId($task['Task']['id']);
                $tasksDistributors = $this->TaskDistributor->findByTaskId($task['Task']['id']);

                if (empty($tasksGarages) && empty($tasksDistributors)) {
                    $tasks[$key]['Task']['Users'] = array();
                    $tasks[$key]['Task']['ContactsLists'] = array();
                    $files = $this->Appointment->Task->TaskFile->get_list($task['Task']['id']);
                    $users = $this->Appointment->Task->TaskUser->getUsersByTask($task['Task']['id']);
                    $contactsListsTmp = $this->Appointment->Task->TaskContactList->getContactsListsByTask($task['Task']['id']);
                    foreach ($users as $user) {
                        $tasks[$key]['Task']['Users'][] = $user['User']['name'] . " " . $user['User']['surname'];
                    }
                    foreach ($contactsListsTmp as $contact_list_tmp) {
                        $tasks[$key]['Task']['ContactsLists'][] = $contact_list_tmp['ContactList']['name'];
                    }
                    foreach ($files as $key_file => $file) {
                        $tasks[$key]['Task']['Files'][$key_file] = $file;
                    }
                } else {
                    unset($tasks[$key]);
                }
            }

            $networks = $this->Network->find('list');
            $distributors = array();
            $usersImages = $this->User->UserImage->find('all');

            $garage_id = $appointment['Appointment']['garage_id'];
            $distributor_id = $appointment['Appointment']['distributor_id'];
            if ($garage_id != null) {
                $customerVisits = $this->Appointment->getAllAppointmentByGarageLimitDateToday($garage_id);
            } elseif ($distributor_id != null) {
                $customerVisits = $this->Appointment->getAllAppointmentByDistributorLimitDateToday($distributor_id);
            } else {
                $customerVisits = array();
            }

            $config = CakeSession::read('Auth.User.Config');
            if (!$config[ConstantsConfig::TASK_DEADLINE]) {
                $this->Task->validator()->remove('limit_date');
            }

            if (!$this->request->is('get')) {
                if (
                    (isset($this->request->data['Appointment']['appointment_status_id']) && $this->request->data['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::PENDING) &&
                    (isset($this->request->data['Appointment']['distributor_id']) && $this->request->data['Appointment']['distributor_id']) &&
                    (isset($this->request->data['Appointment']['customer_performance_summary']) && !$this->request->data['Appointment']['customer_performance_summary'])
                ) {
                    $this->Session->setFlashError(__t('Appointment.Error_customer_performance_summary'));
                } elseif (isset($this->request->data['save_management_objectives'])) {
                    if (isset($this->request->data['Appointment']['objectives'])) {
                        $this->AppointmentObjectiveComment->add_edit($this->request->data['Appointment']['objectives'], $this->request->data['Appointment']['id'], $this->request->data['Appointment']['distributor_id'], ConstantsTypeAgreement::MANAGEMENT);
                    }
                    if (isset($this->request->data['Appointment']['objectives_personal'])) {
                        $this->AppointmentObjectiveComment->add_edit($this->request->data['Appointment']['objectives_personal'], $this->request->data['Appointment']['id'], $this->request->data['Appointment']['distributor_id'], ConstantsTypeAgreement::PERSONAL);
                    }
                    if (isset($this->request->data['Appointment']['objectives_personal_'])) {
                        $this->AppointmentObjectiveComment->add_edit($this->request->data['Appointment']['objectives_personal_'], $this->request->data['Appointment']['id'], $this->request->data['Appointment']['distributor_id'], ConstantsTypeAgreement::PERSONAL);
                    }
                } else {
                    if (isset($this->request->data['Appointment']['distributor_id']) && $this->request->data['Appointment']['distributor_id']) {
                        if (isset($this->request->data['Appointment']['objectives'])) {
                            $this->AppointmentObjectiveComment->add_edit($this->request->data['Appointment']['objectives'], $this->request->data['Appointment']['id'], $this->request->data['Appointment']['distributor_id'], ConstantsTypeAgreement::MANAGEMENT);
                        }
                        if (isset($this->request->data['Appointment']['objectives_personal'])) {
                            $this->AppointmentObjectiveComment->add_edit($this->request->data['Appointment']['objectives_personal'], $this->request->data['Appointment']['id'], $this->request->data['Appointment']['distributor_id'], ConstantsTypeAgreement::PERSONAL);
                        }
                        if (isset($this->request->data['Appointment']['objectives_personal_'])) {
                            $this->AppointmentObjectiveComment->add_edit($this->request->data['Appointment']['objectives_personal_'], $this->request->data['Appointment']['id'], $this->request->data['Appointment']['distributor_id'], ConstantsTypeAgreement::PERSONAL);
                        }
                    }

                    $feedback = isset($this->request->data['Appointment']) ? $this->request->data['Appointment']['feedback'] : '';

                    if (isset($this->request->data['Appointment']['visit_contact_name_disabled'])) {
                        $this->request->data['Appointment']['visit_contact_name'] = $this->request->data['Appointment']['visit_contact_name_disabled'];
                    }
                    if (isset($this->request->data['Appointment']['visit_contact_id_disabled'])) {
                        $this->request->data['Appointment']['visit_contact_id'] = $this->request->data['Appointment']['visit_contact_id_disabled'];
                    }

                    if (isset($this->request->data['Appointment']) && ($this->request->data['Appointment']['start_time'] == null || $this->request->data['Appointment']['start_time'] == '0:00')) {
                        $this->request->data['Appointment']['start_time'] = '08:00';
                    }
                    if (isset($this->request->data['Appointment']) && ($this->request->data['Appointment']['end_time'] == null || $this->request->data['Appointment']['end_time'] == '0:00')) {
                        $this->request->data['Appointment']['end_time'] = '21:00';
                    }

                    $errorSize = false;
                    $checkFile = false;
                    $correctTime = true;

                    $startTimeForm = isset($this->request->data['Appointment']) ? $this->request->data['Appointment']['start_time'] : '';
                    $endTimeForm = isset($this->request->data['Appointment']) ? $this->request->data['Appointment']['end_time'] : '';

                    if (strlen($startTimeForm) < 5) {
                        $startTime = "0" . $startTimeForm;
                    } elseif (strlen($startTimeForm) > 8) {
                        $this->Session->setFlashError(__t('Appointment.Error_date_visit'));
                        $correctTime = false;
                    } else {
                        $startTime = $startTimeForm;
                    }

                    if (strlen($endTimeForm) < 5) {
                        $endTime = "0" . $endTimeForm;
                    } elseif (strlen($endTimeForm) > 8) {
                        $this->Session->setFlashError(__t('Appointment.Error_date_visit'));
                        $correctTime = false;
                    } else {
                        $endTime = $endTimeForm;
                    }

                    if (isset($startTime) && isset($endTime) && $startTime > $endTime) {
                        $this->Session->setFlashError(__t('Appointment.Error_date_visit'));
                        $correctTime = false;
                    }

                    $figuresUk = true;
                    if ($correctTime && $figuresUk) {
                        if (!isset($this->request->data['Appointment']['appointment_feeling_id'])) {
                            $this->request->data['Appointment']['appointment_feeling_id'] = '';
                        }

                        $tmp = $this->request->data;
                        $flagObjectives = true;
                        if (
                            isset($this->request->data['Appointment']['distributor_id']) &&
                            (isset($this->request->data['Appointment']['objectives']) || isset($this->request->data['Appointment']['objectives_personal']))
                        ) {
                            if (isset($this->request->data['Appointment']['objectives'])) {
                                foreach ($this->request->data['Appointment']['objectives'] as $obj) {
                                    if (
                                        ($obj['objective_id'] && !isset($obj['status'])) ||
                                        ($obj['objective_id'] && $obj['status'] == 0) ||
                                        ($obj['objective_id'] && ($obj['status'] != 0 && empty($obj['comment'])))
                                    ) {
                                        $flagObjectives = false;
                                    }
                                }
                            }

                            if (isset($this->request->data['Appointment']['objectives_personal'])) {
                                foreach ($this->request->data['Appointment']['objectives_personal'] as $obj2) {
                                    if (!isset($obj2['objective_id'])) {
                                        if (
                                            ($obj2['objective'] && !isset($obj2['status'])) ||
                                            ($obj2['objective'] && $obj2['status'] == 0) ||
                                            ($obj2['objective'] && ($obj2['status'] != 0 && empty($obj2['comment'])))
                                        ) {
                                            $flagObjectives = false;
                                        }
                                    } else {
                                        if (
                                            ($obj2['objective_id'] && !isset($obj2['status'])) ||
                                            ($obj2['objective_id'] && $obj2['status'] == 0) ||
                                            ($obj2['objective_id'] && ($obj2['status'] != 0 && empty($obj2['comment'])))
                                        ) {
                                            $flagObjectives = false;
                                        }
                                    }
                                }
                            }
                        }

                        if (!$flagObjectives) {
                            unset($this->request->data['btn_exit']);
                            $this->request->data['btn_no_exit'] = array();
                        }

                        $saveAndExit = isset($this->request->data['btn_exit']) ? true : null;
                        $appointmentBd = $this->Appointment->edit_appointment($this->request->data, $user, $feedback, true, $saveAndExit);
                        if (
                            $appointmentBd && $appointmentBd['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::ACCOMPLISHED &&
                            (
                                $appointmentBd['Appointment']['appointment_type_id'] == ConstantsTypesAppointments::VISIT ||
                                $appointmentBd['Appointment']['appointment_type_id'] == ConstantsTypesAppointments::TEAMS
                            )
                        ) {
                            if ($appointmentBd['Appointment']['garage_id']) {
                                $garageTmp = $this->Appointment->Garage->findById($appointmentBd['Appointment']['garage_id']);
                                if (!$garageTmp['Garage']['last_visit'] || strtotime($garageTmp['Garage']['last_visit']) < strtotime(date('Y-m-d'))) {
                                    $this->Appointment->Garage->saveLastVisit($appointmentBd['Appointment']['garage_id'], $appointmentBd['Appointment']['date']);
                                }
                            } elseif ($appointmentBd['Appointment']['distributor_id']) {
                                $distributorTmp = $this->Appointment->Distributor->findById($appointmentBd['Appointment']['distributor_id']);
                                if (!$distributorTmp['Distributor']['last_visit'] || strtotime($distributorTmp['Distributor']['last_visit']) < strtotime(date('Y-m-d'))) {
                                    $this->Appointment->Distributor->saveLastVisit($appointmentBd['Appointment']['distributor_id'], $appointmentBd['Appointment']['date']);
                                }
                            }
                        }

                        $appointmentContacts = true;

                        $topicsOld = $this->AppointmentTopic->getListTopicsByAppointmentId($appointment_id);
                        $this->AppointmentTopic->remove_appointment_topics($appointment_id);
                        if (!empty($this->request->data['AppointmentTopic'])) {
                            $this->AppointmentTopic->add_appointment_topics($this->request->data['AppointmentTopic'], $appointment_id);
                        }

                        if (isset($this->request->data['AppointmentTopic']) && !empty($this->request->data['AppointmentTopic'])) {
                            foreach ($this->request->data['AppointmentTopic'] as $topic) {
                                if (!in_array($topic, $topicsOld)) {
                                    $this->DebriefTopic->increment_use($topic);
                                }
                            }
                        }

                        $this->AppointmentContactList->remove_contact_lists($appointment_id);
                        if (isset($this->request->data['Appointment']['appointment_contact_lists']) && !empty($this->request->data['Appointment']['appointment_contact_lists'])) {
                            //Delete contact lists associated with that appointment before adding a new one
                            $appointmentContacts = $this->Appointment->AppointmentContactList->add_appointment_contact_lists($this->request->data['Appointment'], $appointmentBd);
                        }
                        $this->AppointmentContact->remove_contact($appointment_id);
                        if (isset($this->request->data['Appointment']['appointment_notify_to']) && !empty($this->request->data['Appointment']['appointment_notify_to'])) {
                            $this->AppointmentContact->add_appointment_contact($this->request->data['Appointment']['appointment_notify_to'], $appointment_id);
                        }
                        $appointmentFilesFlag = true;

                        if (isset($this->request->data['Appointments']['files'])) {
                            unset($this->request->data['Appointments']['files'][0]);
                            foreach ($this->request->data['Appointments']['files'] as $file) {
                                if ($file['error'] == ConstantsBooleans::NO) {
                                    $checkFile = FileManager::check_file($file);
                                    if ($checkFile == ConstantsFileErrorTypes::OK) {
                                        if (!$this->Appointment->AppointmentFile->saveFile($file, $appointmentBd['Appointment']['id'], ConstantsFileType::FILE)) {
                                            $appointmentFilesFlag = false;
                                        }
                                    } else {
                                        break;
                                    }
                                } elseif ($file['error'] == ConstantsFlag::ERROR_DIMENSIONS) {
                                    $errorSize = true;
                                }
                            }
                        }

                        if ($appointmentBd && $appointmentFilesFlag && $appointmentContacts) {
                            if (!$checkFile || $checkFile == ConstantsFileErrorTypes::OK) {
                                if (!$errorSize) {
                                    if (
                                        (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) &&
                                        isset($this->request->data['Appointment']['feedback']) && !empty($this->request->data['Appointment']['feedback'])
                                    ) {
                                        $userCreatorTmp = $this->User->find('first', array(
                                            'conditions' => array(
                                                'id' => CakeSession::read('Auth.User.id')
                                            ),
                                            'fields' => array(
                                                'full_name'
                                            )
                                        ));
                                        $userCreator = $userCreatorTmp['User']['full_name'];
                                        $customer = '';
                                        if (!is_null($this->request->data['Appointment']['garage_id']) && !empty($this->request->data['Appointment']['garage_id'])) {
                                            $customerTmp = $this->Garage->find('first', array(
                                                'conditions' => array(
                                                    'id' => $this->request->data['Appointment']['garage_id']
                                                ),
                                                'fields' => array(
                                                    'name'
                                                )
                                            ));
                                            $customer = $customerTmp['Garage']['name'] . ' - ';
                                        } elseif (!is_null($this->request->data['Appointment']['distributor_id']) && !empty($this->request->data['Appointment']['distributor_id'])) {
                                            $customerTmp = $this->Distributor->find('first', array(
                                                'conditions' => array(
                                                    'id' => $this->request->data['Appointment']['distributor_id']
                                                ),
                                                'fields' => array(
                                                    'name'
                                                )
                                            ));
                                            $customer = $customerTmp['Distributor']['name'] . ' - ';
                                        }
                                        $subject = sprintf(__t('Alert.Edit_Appointment_subject'), $customer . $userCreator);
                                        $appointmentUser = $this->Appointment->findById($appointment_id);
                                        $this->request->data['Appointment']['user_creation_id'] = $appointmentUser['Appointment']['user_creation_id'];

                                        if (isset($this->request->data['btn_exit'])) {
                                            $this->sendDataAlert($this->request->data, $subject, __t('Alert.Edit_appointment'), $appointment_id, $action);
                                        }
                                    }

                                    $msg = h(sprintf(__t('Appointment.Well_add')));

                                    if (isset($tmp['btn_exit']) && $tmp['btn_exit'] && !$flagObjectives) {
                                        $msg = h(sprintf(__t('Appointment.No_objetives')));
                                        $this->Session->setFlashSuccess($msg);
                                    } else {
                                        $this->Session->setFlashSuccess($msg);
                                    }

                                    if (isset($this->request->data['btn_no_exit'])) {
                                        $this->redirect($this->request->here);
                                    } else {
                                        $this->redirect(
                                            array(
                                                'controller' => 'appointments',
                                                'action' => 'home',
                                                '?' => array(
                                                    'date' => Fecha::toFormatoVista($appointment['Appointment']['date']),
                                                    'user' => $appointment['Appointment']['user_assigned_id']
                                                )
                                            )
                                        );
                                    }
                                } else {
                                    $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                                }
                            } elseif ($checkFile == ConstantsFileErrorTypes::SIZE_ERROR) {
                                $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                            } else {
                                $this->Session->setFlashError(__t(ConstantsMessages::FILE_ERROR_EXTENSION));
                            }
                        } elseif (!$appointmentFilesFlag) {
                            $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                        } else {
                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                        }
                    }
                }
            } else {
                $contactListId = $this->AppointmentContactList->find(
                    'list',
                    array(
                        'conditions' => array(
                            'appointment_id' => $appointment_id
                        ),
                        'fields' => array(
                            'AppointmentContactList.contact_list_id'
                        )
                    )
                );
                $appointment['Appointment']['appointment_contact_lists'] = $contactListId;
                $contactsAppointment = $this->AppointmentContact->find(
                    'list',
                    array(
                        'conditions' => array(
                            'appointment_id' => $appointment_id
                        ),
                        'fields' => array(
                            'AppointmentContact.contact_id'
                        )
                    )
                );
                $appointment['Appointment']['appointment_notify_to'] = $contactsAppointment;
                $appointment['Appointment']['appointment_contact_lists'] = $contactListId;
                $appointment['Appointment']['date'] = Fecha::toFormatoVistaFecha($appointment['Appointment']['date']);
                $this->request->data = $appointment;
            }

            $this->setVarForm();
            $this->set(array(
                'ltm' => $appointment['Appointment']['distributor_id'] ? $this->Appointment->getAllAppointmentByDistributorLastYear($distributor_id) : '',
                'appointment' => $appointment,
                'appointment_status' => $appointmentStatus,
                'appointment_types' => $appointmentTypes,
                'appointment_files' => $appointmentFiles,
                'appointment_created_by' => $appointmentCreatedBy,
                'appointment_id' => $appointment_id,
                'task_status_expired' => $taskStatusExpired,
                'garage' => $garage,
                'garages_list' => $garagesList,
                'sale_figures_details' => $saleFiguresDetails,
                'sale_figures' => $saleFigures,
                'garage_appointment_previous' => $garageAppointmentPrevious,
                'customer_visits' => $customerVisits,
                'task_garages' => $tasksGarages,
                'users_images' => $usersImages,
                'tasks' => $tasks,
                'appointment_topics' => $appointmentTopics,
                'topics' => $topics,
                'garage_principal_image' => $garagePrincipalImage,
                'distributor_principal_image' => $distributorPrincipalImage,
                'garage_manager' => $garageManager,
                'distributor' => $distributor,
                'distributors_list' => $distributorsList,
                'distributor_manager' => $distributorManager,
                'cancel_action' => $cancelAction,
                'networks' => $networks,
                'distributors' => $distributors,
                'comments' => $comments,
                'load_more' => $loadMore,
                'start' => ConstantsPagination::SIZE_COMMENTS,
                'task_status_list' => $taskStatusList,
                'task_users' => $taskUsers,
                'task_contact_lists' => $taskContactLists,
                'tasks_garages_all' => $tasksGaragesAll,
                'tasks_garages_total' => $tasksGaragesTotal,
                'debrief_tasks' => $debriefTasks,
                'debrief_tasks_list' => $debriefTasksList,
                'debrief_topics' => $debriefTopics,
                'debrief_topics_list' => $debriefTopicsList,
                'url' => $url,
                'contacts_visits' => $contactsVisits,
                'contacts_notify_to' => $contactsNotifyTo,
                'management_objectives' => $this->AppointmentObjective->getPersonalObjectivesByTypeManagement($appointment_id),
                'personal_objectives' => $this->AppointmentObjective->getPersonalObjectivesByTypeAssoc($user, $appointment_id),
                'personal_objectives_list' => $this->AppointmentObjective->getPersonalObjectivesByType($user),
                'appointment_objectives_personal' => $this->AppointmentObjectiveComment->getObjectivesPersonal($appointment_id),
                'appointment_objectives_personal_assoc' => $this->AppointmentObjectiveComment->getObjectivesPersonalAssoc($appointment_id),
                'appointment_objectives_management' => $this->AppointmentObjectiveComment->getObjectivesManagement($appointment_id),
                'disabled' => false,
                'hidden' => null,
                'objectives_status' => array(
                    '0' => __t('Objective.Pending'),
                    '1' => __t('General.Success'),
                    '2' => __t('Objective.Failed'),
                    '3' => __t('Objective.Requires_manager')
                ),
                'user_aag_region_id' => $aagRegionId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Export Appointment to PDF.
     */
    public function export_pdf($appointment_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $appointment = $this->Appointment->findById($appointment_id);
        if (
            $appointment &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
                )
            )
        ) {
            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $tasksGaragesAll = $this->TaskGarage->getAllByGarageIdAndNotCompleted($appointment['Appointment']['garage_id']);
            $tasksGaragesTotal = count($this->TaskGarage->findAllByGarageId($appointment['Appointment']['garage_id']));
            $contactsNotifyTo = $this->Contact->getAllExceptGarageManagerAndDistributorManager($aagRegionId);

            $topics = $this->DebriefTopic->getList();
            $this->setGarageFilterTask();
            $this->setDistributorFilterTask();
            $tasksGarages = array();
            if (!$appointment) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_VISIT));
                $this->redirect(
                    array(
                        'controller' => 'appointments',
                        'action' => 'home',
                    )
                );
            }

            $appointmentStatus = $this->Appointment->AppointmentStatus->search_list_appointment();
            $appointmentTopics = $this->AppointmentTopic->findAllByAppointmentId($appointment_id);

            $taskStatusExpired = $this->Task->TaskStatus->getExpiredStatus();

            $appointmentTypes = $this->Appointment->AppointmentType->search_list_appointment();
            $appointmentFiles = $this->Appointment->AppointmentFile->findAllByAppointmentId($appointment_id);
            $appointmentCreatedBy = $this->User->getCompleteName($appointment['Appointment']['user_creation_id']);

            $garage = $this->Appointment->Garage->findByIdAndAagRegionId($appointment['Appointment']['garage_id'], $aagRegionId);
            if ($appointment['Appointment']['garage_id'] && $appointment['Appointment']['garage_id'] != '' && $appointment['Appointment']['garage_id'] != 'false') {
                $contactsVisits = $this->Contact->getContactsVisitByGarage($appointment['Appointment']['garage_id']);
            } elseif ($appointment['Appointment']['distributor_id'] && $appointment['Appointment']['distributor_id'] != '' && $appointment['Appointment']['distributor_id'] != 'false') {
                $contactsVisits = $this->Contact->getContactsVisitByDistributor($appointment['Appointment']['distributor_id']);
            } else {
                $contactsVisits = array();
            }

            $saleFiguresDetails = array();
            $saleFigures = array();
            $garagesList = array();
            $garageAppointmentPrevious = array();
            $conditions = array();
            $distributorsList = array();
            $distributorManager = array();

            if (!empty($garage)) {
                $garagesDistributors = $this->Appointment->Garage->getGarageDistributorData($appointment['Appointment']['garage_id']);
                $garagesNetworks = $this->Appointment->Garage->getGarageNetworkData($appointment['Appointment']['garage_id']);
                $garage['GarageDistributor'] = $garagesDistributors;
                $garage['GarageNetwork'] = $garagesNetworks;

                $garageSalesFiguresDetails = $this->GarageFigureDetail->findByCustomerNo($garage['Garage']['g_number_id']);
                if ($garageSalesFiguresDetails) {
                    $saleFiguresDetails = json_decode($garageSalesFiguresDetails['GarageFigureDetail']['figures_details'], true);
                }

                $garageAppointmentPrevious = $this->Appointment->getPreviousByGarage($garage['Garage']['id']);

                $garageSalesFigures = $this->GarageFigure->findByCustomerNo($garage['Garage']['g_number_id']);
                if ($garageSalesFigures) {
                    $saleFigures = json_decode($garageSalesFigures['GarageFigure']['figures'], true);
                }
                $garagesList = array($garage['Garage']['id'] => $garage['Garage']['complete_search']);
            }
            $garageManager = $this->GarageContactGeneralBranchManager->findManagerByGarageAndRole($appointment['Appointment']['garage_id'], ConstantsRoles::GARAGE);
            $garagePrincipalImage = $this->Appointment->Garage->getPrincipalImageDatas($appointment['Appointment']['garage_id']);

            $distributor = $this->Appointment->Distributor->findByIdAndAagRegionId($appointment['Appointment']['distributor_id'], $aagRegionId);
            $distributorPrincipalImage = $this->Appointment->Distributor->getPrincipalImageDatas($appointment['Appointment']['distributor_id']);

            if (!empty($distributor)) {
                if ($distributor['Distributor']['trading_group_id'] != null) {
                    $distributor_trading_group = $this->Appointment->Distributor->TradingGroup->getTradingGroupById($distributor['Distributor']['trading_group_id']);
                    $distributor['TradingGroup'] = $distributor_trading_group['TradingGroup'];
                }
                $distributorActivities = $this->DistributorCustomerActivity->findAllByDistributorId($distributor['Distributor']['id']);
                $distributor['DistributorActivity'] = $distributorActivities;
                $customerActivities = $this->CustomerActivity->search_list();
                $conditions = array(
                    'Distributor.id' => $distributor['Distributor']['id']
                );
                $this->set(array(
                    'customer_activities' => $customerActivities
                ));

                $distributorConditions = array(
                    'Distributor.id = DistributorContactBdm.distributor_id'
                );
                $distributorsList = $this->Appointment->Distributor->find(
                    'list',
                    array(
                        'joins' => array(
                            array(
                                'alias' => 'DistributorContactBdm',
                                'table' => 'distributors_contacts_bdm',
                                'type' => 'LEFT',
                                'conditions' => $distributorConditions
                            ),
                        ),
                        'conditions' => $conditions,
                        'order' => 'name',
                        'fields' => array(
                            'id',
                            'complete_search'
                        )
                    )
                );
                $distributorManager = $this->DistributorContactGeneralBranchManager->findManagerByDistributorAndRole($appointment['Appointment']['distributor_id'], ConstantsRoles::DISTRIBUTOR);
            }

            $taskStatusList = $this->Task->TaskStatus->search_list_all();
            $taskUsers = null;
            $taskContactLists = null;

            $debriefTasks = $this->DebriefTask->getDebriefTask();
            $debriefTasksList = $this->DebriefTask->find('list', array('fields' => array(
                'id',
                'title' . __s()
            )));
            $debriefTopics = $this->DebriefTopic->getDebriefTopic();
            $debriefTopicsList = $this->DebriefTopic->getList();

            $comments = $this->Appointment->AppointmentComment->find('all', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id
                ),
                'order' => array(
                    'creation_date' => 'DESC'
                ),
                'limit' => ConstantsPagination::SIZE_COMMENTS
            ));
            $commentsNumber = $this->Appointment->AppointmentComment->find('count', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id
                )
            ));

            if (count($comments) == $commentsNumber) {
                $loadMore = ConstantsBooleans::NO;
            } else {
                $loadMore = ConstantsBooleans::YES;
            }

            $tasks = $this->Appointment->Task->getAllByAppointmentIdOrderByLimitDate($appointment_id);
            foreach ($tasks as $key => $task) {
                $tasksGarages = $this->TaskGarage->findByTaskId($task['Task']['id']);
                $tasksDistributors = $this->TaskDistributor->findByTaskId($task['Task']['id']);

                if (empty($tasksGarages) && empty($tasksDistributors)) {
                    $tasks[$key]['Task']['Users'] = array();
                    $tasks[$key]['Task']['ContactsLists'] = array();
                    $files = $this->Appointment->Task->TaskFile->get_list($task['Task']['id']);
                    $users = $this->Appointment->Task->TaskUser->getUsersByTask($task['Task']['id']);
                    $contactsListsTmp = $this->Appointment->Task->TaskContactList->getContactsListsByTask($task['Task']['id']);
                    foreach ($users as $user) {
                        $tasks[$key]['Task']['Users'][] = $user['User']['name'] . " " . $user['User']['surname'];
                    }
                    foreach ($contactsListsTmp as $contact_list_tmp) {
                        $tasks[$key]['Task']['ContactsLists'][] = $contact_list_tmp['ContactList']['name'];
                    }
                    foreach ($files as $key_file => $file) {
                        $tasks[$key]['Task']['Files'][$key_file] = $file;
                    }
                } else {
                    unset($tasks[$key]);
                }
            }

            $networks = $this->Network->find('list');
            $distributors = $this->Distributor->find('list', array('order' => 'name'));
            $usersImages = $this->User->UserImage->find('all');

            $garage_id = $appointment['Appointment']['garage_id'];
            $distributor_id = $appointment['Appointment']['distributor_id'];
            if ($garage_id != null) {
                $customerVisits = $this->Appointment->getAllAppointmentByGarageLimitDateToday($garage_id);
            } elseif ($distributor_id != null) {
                $customerVisits = $this->Appointment->getAllAppointmentByDistributorLimitDateToday($distributor_id);
            } else {
                $customerVisits = array();
            }

            $config = CakeSession::read('Auth.User.Config');
            if (!$config[ConstantsConfig::TASK_DEADLINE]) {
                $this->Task->validator()->remove('limit_date');
            }

            $contactListId = $this->AppointmentContactList->find(
                'list',
                array(
                    'conditions' => array(
                        'appointment_id' => $appointment_id
                    ),
                    'fields' => array(
                        'AppointmentContactList.contact_list_id'
                    )
                )
            );
            $appointment['Appointment']['appointment_contact_lists'] = $contactListId;
            $contactsAppointment = $this->AppointmentContact->find(
                'list',
                array(
                    'conditions' => array(
                        'appointment_id' => $appointment_id
                    ),
                    'fields' => array(
                        'AppointmentContact.contact_id'
                    )
                )
            );
            $appointment['Appointment']['appointment_notify_to'] = $contactsAppointment;
            $appointment['Appointment']['appointment_contact_lists'] = $contactListId;
            $appointment['Appointment']['date'] = Fecha::toFormatoVistaFecha($appointment['Appointment']['date']);

            $this->setVarForm();
            $this->set(array(
                'ltm' => $appointment['Appointment']['distributor_id'] ? $this->Appointment->getAllAppointmentByDistributorLastYear($distributor_id) : '',
                'appointment' => $appointment,
                'appointment_status' => $appointmentStatus,
                'appointment_types' => $appointmentTypes,
                'appointment_files' => $appointmentFiles,
                'appointment_created_by' => $appointmentCreatedBy,
                'appointment_id' => $appointment_id,
                'task_status_expired' => $taskStatusExpired,
                'garage' => $garage,
                'garages_list' => $garagesList,
                'sale_figures_details' => $saleFiguresDetails,
                'sale_figures' => $saleFigures,
                'garage_appointment_previous' => $garageAppointmentPrevious,
                'customer_visits' => $customerVisits,
                'task_garages' => $tasksGarages,
                'users_images' => $usersImages,
                'tasks' => $tasks,
                'appointment_topics' => $appointmentTopics,
                'topics' => $topics,
                'garage_principal_image' => $garagePrincipalImage,
                'distributor_principal_image' => $distributorPrincipalImage,
                'garage_manager' => $garageManager,
                'distributor' => $distributor,
                'distributors_list' => $distributorsList,
                'distributor_manager' => $distributorManager,
                'networks' => $networks,
                'distributors' => $distributors,
                'comments' => $comments,
                'load_more' => $loadMore,
                'start' => ConstantsPagination::SIZE_COMMENTS,
                'task_status_list' => $taskStatusList,
                'task_users' => $taskUsers,
                'task_contact_lists' => $taskContactLists,
                'tasks_garages_all' => $tasksGaragesAll,
                'tasks_garages_total' => $tasksGaragesTotal,
                'debrief_tasks' => $debriefTasks,
                'debrief_tasks_list' => $debriefTasksList,
                'debrief_topics' => $debriefTopics,
                'debrief_topics_list' => $debriefTopicsList,
                'contacts_visits' => $contactsVisits,
                'contacts_notify_to' => $contactsNotifyTo,
                'management_objectives' => $this->AppointmentObjective->getPersonalObjectivesByTypeManagement($appointment_id),
                'personal_objectives' => $this->AppointmentObjective->getPersonalObjectivesByTypePersonal($appointment_id),
                'appointment_objectives' => $this->AppointmentObjectiveComment->getObjectives($appointment_id),
                'appointment_objectives_type_personal' => $this->AppointmentObjectiveComment->getObjectivesPersonalType($appointment_id),
                'appointment_objectives_type_management' => $this->AppointmentObjectiveComment->getObjectivesManagementType($appointment_id),
                'objectives_status' => array(
                    '0' => __t('Objective.Pending'),
                    '1' => __t('General.Success'),
                    '2' => __t('Objective.Failed'),
                    '3' => __t('Objective.Requires_manager')
                ),
            ));

            $this->layout = null;

            $this->autoRender = false;
            $html = $this->render('/Appointments/Elements/export_pdf');

            $mpdf = new \Mpdf\Mpdf();
            $mpdf->SetFooter('{PAGENO}');
            $mpdf->WriteHTML($html);
            $mpdf->Output(__t('Appointment.pdf'), 'I');

        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit event.
     */
    public function edit_event($appointment_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];
        $appointment = $this->Appointment->findById($appointment_id);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE)) &&
            $appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT
        ) {
            if (!$appointment) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_VISIT));
                $this->redirect(
                    array(
                        'controller' => 'appointments',
                        'action' => 'home',
                    )
                );
            }

            $action = ConstantsActions::EDIT;

            $tasksGarages = array();

            $this->setGarageFilterTask();
            $this->setDistributorFilterTask();

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'appointments',
                    'action' => 'home',
                    '?' => array(
                        'date' => Fecha::toFormatoVista($appointment['Appointment']['date']),
                        'user' => $appointment['Appointment']['user_assigned_id']
                    )
                ),
            );

            $eventsTypes = $this->AppointmentType->search_list_event();
            $taskStatusList = $this->Task->TaskStatus->search_list();
            $usersImages = $this->User->UserImage->find('all');
            $taskUsers = null;
            $taskContactLists = null;

            $debriefTasks = $this->DebriefTask->find('all', array('order' => 'uses DESC'));
            $debriefTasksList = $this->DebriefTask->find('list', array('fields' => array(
                'id',
                'title' . __s()
            )));
            $debriefTopics = $this->DebriefTopic->find('all');
            $debriefTopicsList = $this->DebriefTopic->getList();

            $comments = $this->Appointment->AppointmentComment->find('all', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id
                ),
                'order' => array(
                    'creation_date' => 'DESC'
                ),
                'limit' => ConstantsPagination::SIZE_COMMENTS
            ));
            $commentsNumber = $this->Appointment->AppointmentComment->find('count', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id
                )
            ));

            $loadMore = count($comments) == $commentsNumber ?  ConstantsBooleans::NO : ConstantsBooleans::YES;

            $tasks = $this->Appointment->Task->findAllByAppointmentId($appointment_id);
            foreach ($tasks as $key => $task) {
                $tasks[$key]['Task']['Users'] = array();
                $tasks[$key]['Task']['ContactsLists'] = array();
                $files = $this->Appointment->Task->TaskFile->get_list($task['Task']['id']);
                $users_tmp = $this->Appointment->Task->TaskUser->getUsersByTask($task['Task']['id']);
                $contactsListsTmp = $this->Appointment->Task->TaskContactList->getContactsListsByTask($task['Task']['id']);
                foreach ($users_tmp as $user) {
                    $tasks[$key]['Task']['Users'][] = $user['User']['name'] . " " . $user['User']['surname'];
                }
                foreach ($contactsListsTmp as $contact_list_tmp) {
                    $tasks[$key]['Task']['ContactsLists'][] = $contact_list_tmp['ContactList']['name'];
                }
                foreach ($files as $key_file => $file) {
                    $tasks[$key]['Task']['Files'][$key_file] = $file;
                }
            }

            if (!$this->request->is('get')) {
                if (isset($this->request->data['Appointment'])) {

                    $correctTitle = true;
                    $title = $this->request->data['Appointment']['title'];
                    $correctTime = true;
                    $startTimeForm = $this->request->data['Appointment']['start_time'];
                    $endTimeForm = $this->request->data['Appointment']['end_time'];
                    $startDate = $this->request->data['Appointment']['date'];
                    $endDate = $this->request->data['Appointment']['end_date'];
                    if (empty(trim($title))) {
                        $correctTitle = false;
                    }
                    if (!empty($endDate) && $endDate < $startDate) {
                        $correctTime = false;
                    }

                    if ($startDate == $endDate) {
                        if (strlen($startTimeForm) < 5) {
                            $startTime = "0" . $startTimeForm;
                        } elseif (strlen($startTimeForm) > 8) {
                            $this->Session->setFlashError(__t('Appointment.Error_event_date_visit'));
                            $correctTime = false;
                        } else {
                            $startTime = $startTimeForm;
                        }

                        if (strlen($endTimeForm) < 5) {
                            $endTime = "0" . $endTimeForm;
                        } elseif ((strlen($endTimeForm) > 8)) {
                            $this->Session->setFlashError(__t('Appointment.Error_event_date_visit'));
                            $correctTime = false;
                        } else {
                            $endTime = $endTimeForm;
                        }

                        if (isset($startTime) && isset($endTime) && isset($startDate) && isset($endDate) && ($startTime > $endTime || $startDate > $endDate)) {
                            $this->Session->setFlashError(__t('Appointment.Error_event_date_visit'));
                            $correctTime = false;
                        }
                    }

                    if ($correctTime && $correctTitle) {
                        if (!isset($this->request->data['Appointment']['appointment_feeling_id'])) {
                            $this->request->data['Appointment']['appointment_feeling_id'] = '';
                        }

                        $user = $this->Acceso->user();

                        $appointmentBd = $this->Appointment->edit_appointment($this->request->data, $user);
                        $appointmentContacts = true;

                        $this->AppointmentContactList->remove_contact_lists($appointment_id);
                        if (!empty($this->request->data['Appointment']['appointment_contact_lists'])) {
                            $appointmentContacts = $this->Appointment->AppointmentContactList->add_appointment_contact_lists($this->request->data['Appointment'], $appointmentBd);
                        }

                        if ($appointmentBd && $appointmentContacts) {

                            if (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) {
                                if (isset($this->request->data['Appointment']['feedback']) && !empty($this->request->data['Appointment']['feedback'])) {
                                    $userCreatorTmp = $this->User->find('first', array(
                                        'conditions' => array(
                                            'id' => CakeSession::read('Auth.User.id')
                                        ),
                                        'fields' => array(
                                            'full_name'
                                        )
                                    ));
                                    $userCreator = $userCreatorTmp['User']['full_name'];
                                    $subject = sprintf(__t('Alert.Edit_event_subject'), $userCreator);
                                    $appointmentUser = $this->Appointment->findById($appointment_id);
                                    $this->request->data['Appointment']['user_creation_id'] = $appointmentUser['Appointment']['user_creation_id'];

                                    $this->sendDataAlertEvent($this->request->data, $subject, __t('Alert.Edit_event'), $appointment_id, $action);
                                }
                            }

                            $msg = h(sprintf(__t('Event.Well_add')));
                            $this->Session->setFlashSuccess($msg);
                            if (isset($this->request->data['btn_no_exit'])) {
                                $this->redirect(
                                    array(
                                        'controller' => 'appointments',
                                        'action' => 'edit_event',
                                        $appointmentBd['Appointment']['id']
                                    )
                                );
                            } else {
                                $this->redirect(
                                    array(
                                        'controller' => 'appointments',
                                        'action' => 'home',
                                        '?' => array(
                                            'date' => Fecha::toFormatoVista($appointmentBd['Appointment']['date']),
                                            'user' => $appointmentBd['Appointment']['user_assigned_id']
                                        )
                                    )
                                );
                            }
                        } else {
                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                        }
                    } elseif ($correctTitle) {
                        $this->Session->setFlashError(__t('Appointment.Error_event_date_visit'));
                    } elseif ($correctTime) {
                        $this->Session->setFlashError(__t('Validation.Mandatory_to_choose_a_title'));
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                $contactListId = $this->AppointmentContactList->find(
                    'list',
                    array(
                        'conditions' => array(
                            'appointment_id' => $appointment_id
                        ),
                        'fields' => array(
                            'AppointmentContactList.contact_list_id'
                        )
                    )
                );
                $appointment['Appointment']['appointment_contact_lists'] = $contactListId;
                $appointment['Appointment']['date'] = Fecha::toFormatoVistaFecha($appointment['Appointment']['date']);
                if (isset($appointment['Appointment']['end_date'])) {
                    $appointment['Appointment']['end_date'] = Fecha::toFormatoVistaFecha($appointment['Appointment']['end_date']);
                }
                $this->request->data = $appointment;
            }

            $this->setVarForm();
            $this->set(array(
                'appointment' => $appointment,
                'tasks' => $tasks,
                'task_garages' => $tasksGarages,
                'debrief_tasks' => $debriefTasks,
                'debrief_tasks_list' => $debriefTasksList,
                'debrief_topics' => $debriefTopics,
                'debrief_topics_list' => $debriefTopicsList,
                'cancel_action' => $cancelAction,
                'comments' => $comments,
                'load_more' => $loadMore,
                'start' => ConstantsPagination::SIZE_COMMENTS,
                'task_status_list' => $taskStatusList,
                'task_users' => $taskUsers,
                'task_contact_lists' => $taskContactLists,
                'event_types' => $eventsTypes,
                'users_images' => $usersImages,
                'user_aag_region_id' => $aagRegionId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete visit.
     */
    public function delete($appointment_id, $calendar = false)
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        $appointment = $this->Appointment->findById($appointment_id);
        if (
            $appointment &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $action = ConstantsActions::DELETE;

            $appointment['Appointment']['appointment_contact_lists'] = $this->AppointmentContactList->find('list', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id,
                ),
                'fields' => 'contact_list_id'
            ));
            $tasks = $this->Task->find('all', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id,
                )
            ));
            $topics = $this->AppointmentTopic->find('all', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id,
                )
            ));

            foreach ($topics as $topic) {
                $this->AppointmentTopic->delete($topic['AppointmentTopic']['id']);
            }

            foreach ($tasks as $task) {
                $task_id = $task['Task']['id'];
                $task['ContactList']['contact_list_id'] = $this->TaskContactList->find('list', array(
                    'conditions' => array(
                        'task_id' => $task_id,
                    ),
                    'fields' => 'contact_list_id'
                ));
                $userCreatorTmp = $this->User->find('first', array(
                    'conditions' => array(
                        'id' => CakeSession::read('Auth.User.id')
                    ),
                    'fields' => array(
                        'full_name'
                    )
                ));
                $userCreator = $userCreatorTmp['User']['full_name'];
                $customer = '';
                if (!is_null($appointment['Appointment']['garage_id']) && !empty($appointment['Appointment']['garage_id'])) {
                    $customerTmp = $this->Garage->find('first', array(
                        'conditions' => array(
                            'id' => $appointment['Appointment']['garage_id']
                        ),
                        'fields' => array(
                            'name'
                        )
                    ));
                    $customer = $customerTmp['Garage']['name'] . ' - ';
                    $task['Task']['customer'] = $customerTmp['Garage']['name'];
                } elseif (!is_null($appointment['Appointment']['distributor_id']) && !empty($appointment['Appointment']['distributor_id'])) {
                    $customerTmp = $this->Distributor->find('first', array(
                        'conditions' => array(
                            'id' => $appointment['Appointment']['distributor_id']
                        ),
                        'fields' => array(
                            'name'
                        )
                    ));
                    $customer = $customerTmp['Distributor']['name'] . ' - ';
                    $task['Task']['customer'] = $customerTmp['Distributor']['name'];
                }
                $subject = sprintf(__t('Alert.Deleted_task_subject'), $customer . $userCreator);

                $tasksUsers = $this->Task->TaskUser->find('all', array(
                    'conditions' => array(
                        'task_id' => $task_id,
                    )
                ));
                $tasksContactList = $this->Task->TaskContactList->find('all', array(
                    'conditions' => array(
                        'task_id' => $task_id,
                    )
                ));
                $taskStatus = $this->Task->getTaskStatusByTaskId($task_id);
                $task['Task']['status'] = $taskStatus['TaskStatus']['name' . __s()];
                $taskUser = $this->Task->findById($task_id);
                $task['Task']['user_creation_id'] = $taskUser['Task']['user_creation_id'];

                foreach ($tasksUsers as $taskUsers) {
                    $task['User']['user_id'][] = $taskUsers['TaskUser']['user_id'];
                    $this->Task->TaskUser->delete($taskUsers['TaskUser']['id']);
                }
                foreach ($tasksContactList as $task_contact_list) {
                    $task['ContactList']['contact_list_id'][] = $task_contact_list['TaskContactList']['contact_list_id'];
                    $this->Task->TaskContactList->delete($task_contact_list['TaskContactList']['id']);
                }
                $task_files = $this->Task->TaskFile->findAllByTaskId($task_id);
                foreach ($task_files as $task_file) {
                    $this->Task->TaskFile->delete($task_file['TaskFile']['id']);
                }

                if (
                    (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) &&
                    $appointment['Appointment']['feedback']
                ) {
                    $this->sendDataAlertTask($task, $subject, __t('Alert.Delete_task'), $task_id, ConstantsBooleans::NO_ACTIVE);
                }
                $this->Task->delete($task_id);
            }

            $appointmentTmp = $this->Appointment->findById($appointment_id);
            $userCreatorTmp = $this->User->find('first', array(
                'conditions' => array(
                    'id' => CakeSession::read('Auth.User.id')
                ),
                'fields' => array(
                    'full_name'
                )
            ));
            $userCreator = $userCreatorTmp['User']['full_name'];
            $customer = '';
            if (!is_null($appointmentTmp['Appointment']['garage_id']) && !empty($appointmentTmp['Appointment']['garage_id'])) {
                $customerTmp = $this->Garage->find('first', array(
                    'conditions' => array(
                        'id' => $appointmentTmp['Appointment']['garage_id']
                    ),
                    'fields' => array(
                        'name'
                    )
                ));
                $customer = $customerTmp['Garage']['name'] . ' - ';
            } elseif (!is_null($appointmentTmp['Appointment']['distributor_id']) && !empty($appointmentTmp['Appointment']['distributor_id'])) {
                $customerTmp = $this->Distributor->find('first', array(
                    'conditions' => array(
                        'id' => $appointmentTmp['Appointment']['distributor_id']
                    ),
                    'fields' => array(
                        'name'
                    )
                ));
                $customer =  $customerTmp['Distributor']['name'] . ' - ';
            }
            $subject = sprintf(__t('Alert.Deleted_appointment_subject'), $customer . $userCreator);

            $appointmentUser = $this->Appointment->findById($appointment_id);
            $appointment['Appointment']['user_creation_id'] = $appointmentUser['Appointment']['user_creation_id'];
            if (
                (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) &&
                $appointment['Appointment']['feedback']
            ) {
                $this->sendDataAlert($appointment, $subject, __t('Alert.Delete_appointment'), $appointment_id, $action, ConstantsBooleans::NO_ACTIVE);
            }

            $appointmentsComments = $this->Appointment->AppointmentComment->find('all', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id,
                )
            ));
            foreach ($appointmentsComments as $appointmentComments) {
                $this->Appointment->AppointmentComment->delete($appointmentComments['AppointmentComment']['id']);
            }
            $appointmentsContactList = $this->Appointment->AppointmentContactList->find('all', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id,
                )
            ));
            foreach ($appointmentsContactList as $appointmentContactList) {
                $this->Appointment->AppointmentContactList->delete($appointmentContactList['AppointmentContactList']['id']);
            }

            $appointmentsFiles = $this->Appointment->AppointmentFile->find('all', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id,
                )
            ));
            foreach ($appointmentsFiles as $appointmentFiles) {
                $this->Appointment->AppointmentFile->delete($appointmentFiles['AppointmentFile']['id']);
            }

            if ($this->Appointment->delete_appointment($appointment_id)) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
            }
            if ($calendar) {
                $this->autoRender = false;
            } else {
                $this->redirect(array('controller' => 'appointments', 'action' => 'home'));
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete event.
     */
    public function delete_event($appointment_id, $calendar = false)
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        $appointment = $this->Appointment->findById($appointment_id);
        if (
            $appointment &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $action = ConstantsActions::DELETE;
            $appointment['Appointment']['appointment_contact_lists'] = $this->AppointmentContactList->find('list', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id,
                ),
                'fields' => 'contact_list_id'
            ));
            $tasks = $this->Task->find('all', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id,
                )
            ));
            foreach ($tasks as $task) {
                $task_id = $task['Task']['id'];
                $task['ContactList']['contact_list_id'] = $this->TaskContactList->find('list', array(
                    'conditions' => array(
                        'task_id' => $task_id,
                    ),
                    'fields' => 'contact_list_id'
                ));
                $userCreatorTmp = $this->User->find('first', array(
                    'conditions' => array(
                        'id' => CakeSession::read('Auth.User.id')
                    ),
                    'fields' => array(
                        'full_name'
                    )
                ));
                $userCreator = $userCreatorTmp['User']['full_name'];
                $customer = '';
                if (!is_null($appointment['Appointment']['garage_id']) && !empty($appointment['Appointment']['garage_id'])) {
                    $customerTmp = $this->Garage->find('first', array(
                        'conditions' => array(
                            'id' => $appointment['Appointment']['garage_id']
                        ),
                        'fields' => array(
                            'name'
                        )
                    ));
                    $customer = $customerTmp['Garage']['name'] . ' - ';
                    $task['Task']['customer'] = $customerTmp['Garage']['name'];
                } elseif (!is_null($appointment['Appointment']['distributor_id']) && !empty($appointment['Appointment']['distributor_id'])) {
                    $customerTmp = $this->Distributor->find('first', array(
                        'conditions' => array(
                            'id' => $appointment['Appointment']['distributor_id']
                        ),
                        'fields' => array(
                            'name'
                        )
                    ));
                    $customer = $customerTmp['Distributor']['name'] . ' - ';
                    $task['Task']['customer'] = $customerTmp['Distributor']['name'];
                }
                $subject = sprintf(__t('Alert.Deleted_task_subject'), $customer . $userCreator);

                $tasksUsers = $this->Task->TaskUser->find('all', array(
                    'conditions' => array(
                        'task_id' => $task_id,
                    )
                ));
                $tasksContactList = $this->Task->TaskContactList->find('all', array(
                    'conditions' => array(
                        'task_id' => $task_id,
                    )
                ));
                $taskStatus = $this->Task->getTaskStatusByTaskId($task_id);
                $task['Task']['status'] = $taskStatus['TaskStatus']['name' . __s()];
                $taskUser = $this->Task->findById($task_id);
                $task['Task']['user_creation_id'] = $taskUser['Task']['user_creation_id'];

                foreach ($tasksUsers as $taskUsers) {
                    $task['User']['user_id'][] = $taskUsers['TaskUser']['user_id'];
                    $this->Task->TaskUser->delete($taskUsers['TaskUser']['id']);
                }
                foreach ($tasksContactList as $task_contact_list) {
                    $task['ContactList']['contact_list_id'][] = $task_contact_list['TaskContactList']['contact_list_id'];
                    $this->Task->TaskContactList->delete($task_contact_list['TaskContactList']['id']);
                }
                $task['Task']['customer'] = '';

                if (
                    (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) &&
                    $appointment['Appointment']['feedback']
                ) {
                    $this->sendDataAlertTask($task, $subject, __t('Alert.Delete_task'), $task_id, ConstantsBooleans::NO_ACTIVE);
                }
                $this->Task->delete($task_id);
            }

            $userCreatorTmp = $this->User->find('first', array(
                'conditions' => array(
                    'id' => $user['id']
                ),
                'fields' => array(
                    'full_name'
                )
            ));
            $userCreator = $userCreatorTmp['User']['full_name'];
            $subject = sprintf(__t('Alert.Deleted_event_subject'), $userCreator);

            $appointmentUser = $this->Appointment->findById($appointment_id);
            $appointment['Appointment']['user_creation_id'] = $appointmentUser['Appointment']['user_creation_id'];
            if (
                (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) &&
                $appointment['Appointment']['feedback']
            ) {
                $this->sendDataAlertEvent($appointment, $subject, __t('Alert.Delete_event'), $appointment_id, $action, ConstantsBooleans::NO_ACTIVE);
            }

            $appointmentsComments = $this->Appointment->AppointmentComment->find('all', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id,
                )
            ));
            foreach ($appointmentsComments as $appointmentComments) {
                $this->Appointment->AppointmentComment->delete($appointmentComments['AppointmentComment']['id']);
            }
            $appointmentsContactList = $this->Appointment->AppointmentContactList->find('all', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id,
                )
            ));
            foreach ($appointmentsContactList as $appointmentContactList) {
                $this->Appointment->AppointmentContactList->delete($appointmentContactList['AppointmentContactList']['id']);
            }
            $appointmentsFiles = $this->Appointment->AppointmentFile->find('all', array(
                'conditions' => array(
                    'appointment_id' => $appointment_id,
                )
            ));
            foreach ($appointmentsFiles as $appointmentFiles) {
                $this->Appointment->AppointmentFile->delete($appointmentFiles['AppointmentFile']['id']);
            }

            if ($this->Appointment->delete_appointment($appointment_id)) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
            }

            if ($calendar) {
                $this->autoRender = false;
            } else {
                $this->redirect(array('controller' => 'appointments', 'action' => 'home'));
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get event list.
     */
    public function ajax_events_list()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $start = $this->request->query['start'];
            $end = $this->request->query['end'];
            $appointments = $this->Appointment->getCalendarEvents($start, $end, $user);
            echo json_encode($appointments);
            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX advanced search garage.
     */
    public function advanced_search_ajax()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $user = $this->User->findById($this->request->data['user_assigned_id']);
            $data = $this->request->data;
            $limit = ConstantsPagination::SIZE_ADVANCED_SEARCH;
            $conditions = $this->User->viewUserGarages($user['User']);
            if (!empty($data['town'])) {
                $conditions['Garage.town LIKE'] = '%' . $data['town'] . '%';
            }
            if (!empty($data['name'])) {
                $conditions['Garage.name LIKE'] = '%' . $data['name'] . '%';
            }
            if (!empty($data['network'])) {
                $conditions['GarageNetwork.network_id'] = $data['network'];
            }
            if (!empty($data['distributor'])) {
                $conditions['GarageDistributor.distributor_id'] = $data['distributor'];
            }
            $garages = $this->Garage->find('all', array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'LEFT',
                        'conditions' => 'Garage.id = GarageNetwork.garage_id'
                    ),
                    array(
                        'alias' => 'GarageDistributor',
                        'table' => 'garages_distributors',
                        'type' => 'LEFT',
                        'conditions' => 'Garage.id = GarageDistributor.garage_id'
                    ),
                ),
                'conditions' => $conditions,
                'order' => array('Garage.name ASC'),
                'group' => array('Garage.id'),
                'fields' => array(
                    'Garage.id',
                    'Garage.name',
                    'Garage.g_number_id',
                    'Garage.town'
                ),
                'limit' => $limit
            ));

            $garages = Hash::extract($garages, '{n}.{s}');
            echo json_encode($garages);

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX advanced search distributor.
     */
    public function advanced_search_ajax_distributor()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $data = $this->request->data;
            $user = $this->User->findById($this->request->data['user_assigned_id']);
            $limit = ConstantsPagination::SIZE_ADVANCED_SEARCH;

            $conditions = array();
            $conditions = $this->User->viewUserDistributors($user['User']);

            foreach ($data as $key => $item) {
                if (!empty($item) && $key != 'user_assigned_id') {
                    $conditions['Distributor.' . $key . ' LIKE'] = '%' . $item . '%';
                }
            }

            $distributors = $this->Distributor->find('all', array(
                'conditions' => $conditions,
                'order' => array('Distributor.name ASC'),
                'group' => array('Distributor.id'),
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.account_number',
                    'Distributor.town'
                ),
                'limit' => $limit
            ));

            $distributors = Hash::extract($distributors, '{n}.{s}');

            echo json_encode($distributors);

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get garage data.
     */
    public function ajax_get_garage_data($garage_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $garage = $this->Appointment->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);

        if (
            $garage &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                    !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
                )
            )
        ) {
            $garagesDistributors = $this->Appointment->Garage->getGarageDistributorData($garage_id);
            $garagesNetworks = $this->Appointment->Garage->getGarageNetworkData($garage_id);
            $garage['GarageDistributor'] = $garagesDistributors;
            $garage['GarageNetwork'] = $garagesNetworks;
            $garageSalesFiguresDetails = $this->GarageFigureDetail->findByCustomerNo($garage['Garage']['g_number_id']);
            $saleFiguresDetails = array();
            if ($garageSalesFiguresDetails) {
                $saleFiguresDetails = json_decode($garageSalesFiguresDetails['GarageFigureDetail']['figures_details'], true);
            }

            $saleFigures = array();
            $garageSalesFigures = $this->GarageFigure->findByCustomerNo($garage['Garage']['g_number_id']);
            if ($garageSalesFigures) {
                $saleFigures = json_decode($garageSalesFigures['GarageFigure']['figures'], true);
            }

            $garagePrincipalImage = $this->Appointment->Garage->getPrincipalImageDatas($garage_id);
            $garageManager = $this->GarageContactGeneralBranchManager->findManagerByGarageAndRole($garage_id, ConstantsRoles::GARAGE);

            $this->set(array(
                'garage' => $garage,
                'garage_principal_image' => $garagePrincipalImage,
                'garage_manager' => $garageManager,
                'sale_figures_details' => $saleFiguresDetails,
                'sale_figures' => $saleFigures,
            ));

            $this->layout = null;
            $this->render('/Appointments/Elements/garage_info');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get distributor data.
     */
    public function ajax_get_distributor_data($distributor_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $distributor = $this->Appointment->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

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
            $distributorPrincipalImage = $this->Appointment->Distributor->getPrincipalImageDatas($distributor_id);

            if ($distributor['Distributor']['trading_group_id'] != null) {
                $distributor_trading_group = $this->Appointment->Distributor->TradingGroup->getTradingGroupById($distributor['Distributor']['trading_group_id']);
                $distributor['TradingGroup'] = $distributor_trading_group['TradingGroup'];
            }

            $distributorActivities = $this->DistributorCustomerActivity->findAllByDistributorId($distributor['Distributor']['id']);
            $distributor['DistributorActivity'] = $distributorActivities;
            $customerActivities = $this->CustomerActivity->search_list();
            $this->set(array(
                'customer_activities' => $customerActivities
            ));

            $distributorManager = $this->DistributorContactGeneralBranchManager->findManagerByDistributorAndRole($distributor_id, ConstantsRoles::DISTRIBUTOR);

            $this->set(array(
                'distributor' => $distributor,
                'ltm' => $this->Appointment->getAllAppointmentByDistributorLastYear($distributor_id),
                'distributor_principal_image' => $distributorPrincipalImage,
                'distributor_manager' => $distributorManager
            ));

            $this->layout = null;
            $this->render('/Appointments/Elements/garage_info');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX edit event.
     */
    public function ajax_edit_event()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $appointmentBd = array(
                'Appointment' => $this->request->data
            );
            $action = ConstantsActions::EDIT;

            if (strtotime($appointmentBd['Appointment']['date']) < strtotime(date('Y-m-d'))) {
                $appointmentBd['Appointment']['appointment_status_id'] = ConstantsStatusAppointments::PENDING;
            } elseif (
                strtotime($appointmentBd['Appointment']['date']) == strtotime(date('Y-m-d')) &&
                strtotime($appointmentBd['Appointment']['start_time']) < strtotime(date('H:i:s'))
            ) {
                $appointmentBd['Appointment']['appointment_status_id'] = ConstantsStatusAppointments::PENDING;
            } elseif (strtotime($appointmentBd['Appointment']['date']) > strtotime(date('Y-m-d'))) {
                $appointmentBd['Appointment']['appointment_status_id'] = ConstantsStatusAppointments::PLANNED;
            } elseif (
                strtotime($appointmentBd['Appointment']['date']) == strtotime(date('Y-m-d')) &&
                strtotime($appointmentBd['Appointment']['start_time']) > strtotime(date('H:i:s'))
            ) {
                $appointmentBd['Appointment']['appointment_status_id'] = ConstantsStatusAppointments::PLANNED;
            }

            $appointmentEdit = $this->Appointment->edit_appointment($appointmentBd, $user['id'], null, false);

            if ($appointmentEdit) {
                $appointment = $this->Appointment->findById($appointmentBd['Appointment']['id']);

                if (
                    (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) &&
                    isset($appointment['Appointment']['feedback']) && !empty($appointment['Appointment']['feedback'])
                ) {
                    $userCreatorTmp = $this->User->find('first', array(
                        'conditions' => array(
                            'id' => CakeSession::read('Auth.User.id')
                        ),
                        'fields' => array(
                            'full_name'
                        )
                    ));
                    $userCreator = $userCreatorTmp['User']['full_name'];
                    $customer = '';
                    if (!is_null($appointment['Appointment']['garage_id']) && !empty($appointment['Appointment']['garage_id'])) {
                        $customerTmp = $this->Garage->find('first', array(
                            'conditions' => array(
                                'id' => $appointment['Appointment']['garage_id']
                            ),
                            'fields' => array(
                                'name'
                            )
                        ));
                        $customer = $customerTmp['Garage']['name'] . ' - ';
                    } elseif (!is_null($appointment['Appointment']['distributor_id']) && !empty($appointment['Appointment']['distributor_id'])) {
                        $customerTmp = $this->Distributor->find('first', array(
                            'conditions' => array(
                                'id' => $appointment['Appointment']['distributor_id']
                            ),
                            'fields' => array(
                                'name'
                            )
                        ));
                        $customer = $customerTmp['Distributor']['name'] . ' - ';
                    }
                    $contactListId = $this->AppointmentContactList->find(
                        'list',
                        array(
                            'conditions' => array(
                                'appointment_id' => $appointmentBd['Appointment']['id']
                            ),
                            'fields' => array(
                                'AppointmentContactList.contact_list_id'
                            )
                        )
                    );
                    $appointment['Appointment']['appointment_contact_lists'] = $contactListId;
                    $appointmentType = $this->AppointmentType->findById($appointment['Appointment']['appointment_type_id']);
                    if ($appointmentType['AppointmentType']['is_event']) {
                        $subject = sprintf(__t('Alert.Edit_event_subject'), $userCreator);
                        $this->sendDataAlertEvent($appointment, $subject, __t('Alert.Edit_event'), $appointmentBd['Appointment']['id'], $action);
                    } else {
                        $subject = sprintf(__t('Alert.Edit_Appointment_subject'), $customer . $userCreator);
                        $this->sendDataAlert($appointment, $subject, __t('Alert.Edit_appointment'), $appointmentBd['Appointment']['id'], $action);
                    }
                }
            }

            echo json_encode($appointmentEdit);
            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search customer home list.
     */
    public function ajax_search_customer_home_list()
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
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $appointmentStatus = $this->Appointment->AppointmentStatus->search_list();
            $appointmentTypes = $this->Appointment->AppointmentType->search_list();
            $feelingsList = $this->Appointment->AppointmentFeeling->search_list();
            $appointmentFillUp = array(
                '0' => __t('General.No'),
                '1' => __t('General.Yes'),
            );
            $feelingsColors = $this->Appointment->AppointmentFeeling->find('list', array(
                'fields' => array(
                    'id',
                    'color'
                )
            ));
            $feelings = $this->Appointment->AppointmentFeeling->find('list', array(
                'fields' => array(
                    'id',
                    'icon'
                )
            ));
            $status = $this->Appointment->AppointmentStatus->search_list();

            $search = $this->request->query;
            $this->request->data['Search'] = $search;
            $appointments = $this->custom_pagination(
                $this->Appointment->_query('search_agenda_list'),
                $this->Appointment->conditions($search),
                ConstantsPagination::SIZE_PAGE_SMALL
            );

            foreach ($appointments as $key => $appointment) {
                $appointments[$key] = $this->Appointment->getAppointmentDataByAppointmentId($appointment['Appointment']['id']);
            }

            $topics = $this->DebriefTopic->getList();
            $this->setUsers();
            $this->set(
                array(
                    'appointments' => $appointments,
                    'appointment_status' => $appointmentStatus,
                    'appointment_types' => $appointmentTypes,
                    'feelings' => $feelings,
                    'feelings_list' => $feelingsList,
                    'feelings_colors' => $feelingsColors,
                    'status' => $status,
                    'topics' => $topics,
                    'appointment_fill_up' => $appointmentFillUp
                )
            );

            $this->layout = null;
            $this->render('../Appointments/Elements/ajax_home_list');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Send data alert visit.
     */
    private function sendDataAlert($appointmentData, $emailSubject, $alertBody, $appointment_id, $action, $deleted = null)
    {
        $contacts = array();
        $userCreator = $this->User->findById($appointmentData['Appointment']['user_creation_id']);
        $userAssigned = $this->User->findById($appointmentData['Appointment']['user_assigned_id']);
        $contacts[] = $this->Contact->findById($userCreator['User']['contact_id']);
        $contacts[] = $this->Contact->findById($userAssigned['User']['contact_id']);

        if (!empty($appointmentData['Appointment']['appointment_contact_lists'])) {
            foreach ($appointmentData['Appointment']['appointment_contact_lists'] as $contact_list) {
                $contactsContactsList = $this->ContactContactList->findAllByContactListId($contact_list);
                foreach ($contactsContactsList as $contactContactsList) {
                    $contact = $this->Contact->findById($contactContactsList['ContactContactList']['contact_id']);
                    if (!empty($contact)) {
                        $contacts[] = $contact;
                    }
                }
            }
        }

        if (!empty($appointmentData['Appointment']['appointment_notify_to'])) {
            foreach ($appointmentData['Appointment']['appointment_notify_to'] as $contactTmp) {
                $contacts[] = $this->Contact->findById($contactTmp);
            }
        }

        //Parent of user creator
        $contactUserCreator = $this->Contact->findById($userCreator['User']['contact_id']);
        if ($contactUserCreator['Contact']['contact_id']) {
            $contacts[] = $this->Contact->findById($contactUserCreator['Contact']['contact_id']);
        }

        $contacts = array_map("unserialize", array_unique(array_map("serialize", $contacts)));
        $date = Fecha::toFormatoBd($appointmentData['Appointment']['date']);

        $appointmentEmail = array(
            'title' => __t('Appointment.Appointments'),
            'date' => $date,
            'start_time' => $appointmentData['Appointment']['start_time'],
            'end_time' => $appointmentData['Appointment']['end_time'],
            'feedback' => $appointmentData['Appointment']['feedback'],
            'customer_performance_summary' => $appointmentData['Appointment']['customer_performance_summary'],
        );

        if (is_null($deleted)) {
            $url = Router::url(array(
                'controller' => 'appointments',
                'action' => 'edit',
                $appointment_id
            ));
            $appointmentEmail['link'] = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . $url;
        } else {
            $url = '';
        }

        if (isset($appointmentData['Appointment']['description'])) {
            $appointmentEmail['notes'] = $appointmentData['Appointment']['description'];
        }

        if (isset($appointmentData['Appointment']['appointment_feeling_id']) && !empty($appointmentData['Appointment']['appointment_feeling_id'])) {
            $appointmentFeeling = $this->AppointmentFeeling->findById($appointmentData['Appointment']['appointment_feeling_id']);

            if ($appointmentFeeling) {
                $appointmentEmail['feeling'] = $appointmentFeeling['AppointmentFeeling']['name_' . __l()];
                $appointmentEmail['feeling_color'] = $appointmentFeeling['AppointmentFeeling']['color'];
            }
        }

        $userTmp = $this->User->findById($appointmentData['Appointment']['user_assigned_id']);
        $appointmentEmail['assigned_to'] = $userTmp['User']['name'] . " " . $userTmp['User']['surname'];

        if (!empty($appointmentData['Appointment']['garage_id'])) {
            $garage = $this->Garage->findById($appointmentData['Appointment']['garage_id']);
            $appointmentEmail['customer_id'] = $garage['Garage']['g_number_id'];
            $appointmentEmail['customer_name'] = $garage['Garage']['name'];
        } elseif (!empty($appointmentData['Appointment']['distributor_id'])) {
            $distributor = $this->Distributor->findById($appointmentData['Appointment']['distributor_id']);
            $appointmentEmail['customer_id'] = $distributor['Distributor']['account_number'];
            $appointmentEmail['customer_name'] = $distributor['Distributor']['name'];
            $appointmentEmail['mtd'] = $appointmentData['Appointment']['mtd'];
            $appointmentEmail['qtd'] = $appointmentData['Appointment']['qtd'];
            $appointmentEmail['ytd'] = $appointmentData['Appointment']['ytd'];
            $appointmentEmail['objectives'] = $this->AppointmentObjectiveComment->getObjectivesToEmail($appointmentData['Appointment']['id']);
        }

        if (!empty($appointment_id)) {
            $appointmentEmail['files'] = $this->AppointmentFile->findAllByAppointmentId($appointment_id);
        }

        $this->Alert->new_alert($contacts, ConstantsAlerts::APPOINTMENT, $alertBody, $emailSubject, $url, $appointmentEmail, $appointment_id, $appointmentData['Appointment']['user_assigned_id'], $action, $deleted, $appointmentData['Appointment']['feedback']);
    }

    /**
     * Send data alert event.
     */
    private function sendDataAlertEvent($appointmentData, $emailSubject, $alertBody, $appointment_id, $action, $deleted = null)
    {
        $contacts = array();
        $userCreator = $this->User->findById($appointmentData['Appointment']['user_creation_id']);
        $userAssigned = $this->User->findById($appointmentData['Appointment']['user_assigned_id']);
        $contacts[] = $this->Contact->findById($userCreator['User']['contact_id']);
        $contacts[] = $this->Contact->findById($userAssigned['User']['contact_id']);

        if (!empty($appointmentData['Appointment']['appointment_contact_lists'])) {
            foreach ($appointmentData['Appointment']['appointment_contact_lists'] as $contact_list) {
                $contactsContactsList = $this->ContactContactList->findAllByContactListId($contact_list);
                foreach ($contactsContactsList as $contactContactsList) {
                    $contact = $this->Contact->findById($contactContactsList['ContactContactList']['contact_id']);
                    if (!empty($contact)) {
                        $contacts[] = $contact;
                    }
                }
            }
        }

        if (!empty($appointmentData['Appointment']['appointment_notify_to'])) {
            foreach ($appointmentData['Appointment']['appointment_notify_to'] as $contactTmp) {
                $contacts[] = $this->Contact->findById($contactTmp);
            }
        }

        //Parent of user creator
        $contactUserCreator = $this->Contact->findById($userCreator['User']['contact_id']);
        if ($contactUserCreator['Contact']['contact_id']) {
            $contacts[] = $this->Contact->findById($contactUserCreator['Contact']['contact_id']);
        }

        $contacts = array_map("unserialize", array_unique(array_map("serialize", $contacts)));
        $date = Fecha::toFormatoBd($appointmentData['Appointment']['date']);

        if (isset($appointmentData['Appointment']['end_date']) && !empty($appointmentData['Appointment']['end_date'])) {
            $endDate = Fecha::toFormatoBd($appointmentData['Appointment']['end_date']);
        } else {
            $endDate = $appointmentData['Appointment']['date'];
        }

        $appointmentEmail = array(
            'title' => $appointmentData['Appointment']['title'],
            'date' => $date,
            'end_date' => $endDate,
            'start_time' => $appointmentData['Appointment']['start_time'],
            'end_time' => $appointmentData['Appointment']['end_time'],
            'description' => $appointmentData['Appointment']['feedback'],
            'user_creator' => $userCreator['User']['full_name']
        );

        if (is_null($deleted)) {
            $url = Router::url(array(
                'controller' => 'appointments',
                'action' => 'edit_event',
                $appointment_id
            ));
            $appointmentEmail['link'] = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . $url;
        } else {
            $url = '';
        }

        if (isset($appointmentData['Appointment']['appointment_feeling_id']) && !empty($appointmentData['Appointment']['appointment_feeling_id'])) {
            $appointmentFeeling = $this->AppointmentFeeling->findById($appointmentData['Appointment']['appointment_feeling_id']);

            if ($appointmentFeeling) {
                $appointmentEmail['feeling'] = $appointmentFeeling['AppointmentFeeling']['name_' . __l()];
                $appointmentEmail['feeling_color'] = $appointmentFeeling['AppointmentFeeling']['color'];
            }
        }

        $userTmp = $this->User->findById($appointmentData['Appointment']['user_assigned_id']);
        $appointmentEmail['assigned_to'] = $userTmp['User']['name'] . " " . $userTmp['User']['surname'];

        if (!empty($appointment_id)) {
            $appointmentEmail['files'] = $this->AppointmentFile->findAllByAppointmentId($appointment_id);
        }

        $this->Alert->new_alert($contacts, ConstantsAlerts::EVENT, $alertBody, $emailSubject, $url, $appointmentEmail, $appointment_id, $appointmentData['Appointment']['user_assigned_id'], $action, $deleted);
    }

    /**
     * Send data alert task. Same function in TaskController.
     */
    private function sendDataAlertTask($task_data, $emailSubject, $alertBody, $task_id, $deleted = null)
    {
        $contacts = array();

        $userCreator = $this->User->findById($task_data['Task']['user_creation_id']);
        $userAssigned = $this->User->findById($task_data['Task']['user_assigned_id']);
        $contacts[] = $this->Contact->findById($userCreator['User']['contact_id']);
        if (isset($userAssigned['User']['contact_id'])) {
            $contacts[] = $this->Contact->findById($userAssigned['User']['contact_id']);
        }

        if (isset($task_data['ContactList']['contact_list_id']) && is_array($task_data['ContactList']['contact_list_id'])) {
            foreach ($task_data['ContactList']['contact_list_id'] as $contact_list) {
                $contactsContactsList = $this->ContactContactList->findAllByContactListId($contact_list);
                foreach ($contactsContactsList as $contactContactsList) {
                    $contact = $this->Contact->findById($contactContactsList['ContactContactList']['contact_id']);
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
        $contactUserCreator = $this->Contact->findById($userCreator['User']['contact_id']);
        if ($contactUserCreator['Contact']['contact_id']) {
            $contacts[] = $this->Contact->findById($contactUserCreator['Contact']['contact_id']);
        }

        $contacts = array_map("unserialize", array_unique(array_map("serialize", $contacts)));

        if (!isset($task_data['Task']['customer'])) {
            $task_data['Task']['customer'] = '';
        }

        $taskEmail = array(
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
            $taskEmail['link'] = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . $url;
            $taskTmp = $this->Task->findById($task_id);
            $userTmp = $this->User->findById($taskTmp['Task']['user_creation_id']);
            $taskEmail['creator'] = $userTmp['User']['name'] . " " . $userTmp['User']['surname'];
        } else {
            $url = '';
            $userTmp = $this->User->findById($task_data['Task']['user_creation_id']);
            $taskEmail['creator'] = $userTmp['User']['name'] . " " . $userTmp['User']['surname'];
        }

        if (isset($task_data['Task']['user_assigned_id']) && !empty($task_data['Task']['user_assigned_id'])) {
            $userTmp = $this->User->findById($task_data['Task']['user_assigned_id']);
            $taskEmail['assigned_to'] = $userTmp['User']['name'] . " " . $userTmp['User']['surname'];
        }

        if (!empty($task_id)) {
            $taskEmail['files'] = $this->TaskFile->findAllByTaskId($task_id);
        }

        $this->Alert->new_alert($contacts, ConstantsAlerts::TASK, $alertBody, $emailSubject, $url, $taskEmail, $task_id, $task_data['Task']['user_assigned_id'], $deleted);
    }

    /**
     * AJAX search maintenance event.
     */
    public function search_event($search)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::EVENTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $this->checkSearch($search);

            $this->layout = null;
            $this->render('../Appointments/Elements/table_maintenance_events');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX add maintenance event.
     */
    public function ajax_add_event($search)
    {
        $this->verify_ajax($this->request);

        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::EVENTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            if (!$this->request->is('get')) {
                $flag = $this->checkNames($this->request->data);
                if ($flag == ConstantsFlag::ERROR_NO) {
                    $appointmentTypeBd = array(
                        'AppointmentType' => array(
                            'name_en' => $this->request->data['AppointmentType']['name_en'],
                            'name_fr' => $this->request->data['AppointmentType']['name_fr'],
                            'name_de' => $this->request->data['AppointmentType']['name_de'],
                            'is_event' => ConstantsBooleans::YES,
                        )
                    );
                    $appointmentType = $this->AppointmentType->new_appointment_type($appointmentTypeBd);
                    if (!$appointmentType) {
                        $error = ConstantsAlertsErrors::ERROR_GENERAL;
                    }
                } else {
                    $error = ConstantsAlertsErrors::ERROR_NAME_EMPTY;
                }

                $this->checkSearch($search);
                if (isset($error)) {
                    $this->Session->setFlashError(__t($error));
                } else {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                }

                $this->layout = null;
                $this->render('../Appointments/Elements/table_maintenance_events');
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get list of distributors.
     */
    public function ajax_get_list_distributors()
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
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $term = $this->request->query['term'];
            $this->layout = $this->autoRender = false;
            return json_encode($this->Distributor->getDistributorsByName($term, $aagRegionId));
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

        $distributorConditions = array(
            'Distributor.id = DistributorContactBdm.distributor_id'
        );
        $conditions = array();
        $conditions[] = array('Distributor.status' => ConstantsDistributorStatus::ACTIVE);

        $distributorsList = $this->Appointment->Distributor->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'DistributorContactBdm',
                        'table' => 'distributors_contacts_bdm',
                        'type' => 'LEFT',
                        'conditions' => $distributorConditions
                    ),
                ),
                'conditions' => $conditions,
                'order' => 'name',
                'fields' => array(
                    'id',
                    'complete_search'
                )
            )
        );

        $this->autoRender = false;
        return json_encode($distributorsList);
    }

    /**
     * AJAX edit maintenance event.
     */
    public function ajax_edit_appointment_event($event_id, $search)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        $appointmentType = $this->AppointmentType->findById($event_id, 'id');

        if (
            $appointmentType &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->haveDefaultPermission(ConstantsPermissionsGrouping::EVENTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
                )
            )
        ) {
            if (!$this->request->is('get')) {
                $flag = $this->checkNames($this->request->data);
                if ($flag != ConstantsFlag::ERROR_NAME_EMPTY) {
                    $appointmentType = array(
                        'AppointmentType' => array(
                            'id' => $event_id,
                            'name_en' => $this->request->data['AppointmentType']['name_en'],
                            'name_fr' => $this->request->data['AppointmentType']['name_fr'],
                            'name_de' => $this->request->data['AppointmentType']['name_de'],
                        )
                    );
                }
                if ($flag == ConstantsFlag::ERROR_NO) {
                    if (!$this->AppointmentType->save($appointmentType)) {
                        $error = ConstantsAlertsErrors::ERROR_GENERAL;
                    }
                } elseif ($flag == ConstantsFlag::ERROR_NAME_EMPTY) {
                    $error = ConstantsAlertsErrors::ERROR_NAME_EMPTY;
                } elseif ($flag == ConstantsFlag::ERROR_DIMENSIONS) {
                    $error = ConstantsAlertsErrors::ERROR_DIMENSIONS;
                } elseif ($flag == ConstantsFlag::ERROR_EXTENSION) {
                    $error = ConstantsAlertsErrors::ERROR_EXTENSION;
                }
            }

            $this->checkSearch($search);

            if (isset($error)) {
                $this->Session->setFlashError(__t($error));
            } else {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
            }

            $this->layout = null;
            $this->render('../Appointments/Elements/table_maintenance_events');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete maintenance event.
     */
    public function ajax_delete_event($event_type_id, $search)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        $appointmentType = $this->AppointmentType->findById($event_type_id, 'id');

        if (
            $appointmentType &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->haveDefaultPermission(ConstantsPermissionsGrouping::EVENTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
                )
            )
        ) {
            $appointmentsEvents = $this->Appointment->find('all', array(
                'conditions' => array(
                    'appointment_type_id' => $event_type_id,
                )
            ));

            if ($appointmentsEvents) {
                return false;
            } else {
                if (!$this->request->is('get')) {
                    if (!$this->AppointmentType->delete($event_type_id)) {
                        $error = ConstantsAlertsErrors::ERROR_GENERAL;
                    }

                    $this->checkSearch($search);

                    if (isset($error)) {
                        $this->Session->setFlashError(__t($error));
                    } else {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    }

                    $this->layout = null;
                    $this->render('../Appointments/Elements/table_maintenance_events');
                }
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get contact visit.
     */
    public function ajax_get_contact_visit()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $contactsVisits = array();
            if ($this->request->data['garage_id'] && $this->request->data['garage_id'] != '' && $this->request->data['garage_id'] != 'false') {
                $contactsVisits = $this->Contact->getContactsVisitByGarage($this->request->data['garage_id']);
            } elseif ($this->request->data['distributor_id'] && $this->request->data['distributor_id'] != '' && $this->request->data['distributor_id'] != 'false') {
                $contactsVisits = $this->Contact->getContactsVisitByDistributor($this->request->data['distributor_id']);
            }

            if ($this->request->data['appointment_id']) {
                $appointment = $this->Appointment->findById($this->request->data['appointment_id']);
                $this->set(array(
                    'appointment' => $appointment
                ));
            }

            $this->set(array(
                'contacts_visits' => $contactsVisits
            ));

            $this->layout = null;
            $this->render('../Appointments/Elements/contact_visits');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function checkSearch($search)
    {
        $selectedLanguage = 'name_' . $this->Session->read('Auth.User.language_code');
        if ($search == 'all') {
            $eventsTypesLanguages = $this->AppointmentType->find(
                'all',
                array(
                    'conditions' => array(
                        'is_event' => ConstantsBooleans::YES
                    ),
                    'fields' => array(
                        'id',
                        'name_en',
                        'name_fr',
                        'name_de',
                    ),
                    'order' => array(
                        'name_en'
                    ),
                )
            );
        } else {
            $eventsTypesLanguages = $this->AppointmentType->find(
                'all',
                array(
                    'conditions' => array(
                        'is_event' => ConstantsBooleans::YES,
                        $selectedLanguage . ' LIKE' => '%' . $search . '%'
                    ),
                    'fields' => array(
                        'id',
                        'name_en',
                        'name_fr',
                        'name_de',
                    ),
                    'order' => array(
                        'name_en'
                    ),
                )
            );
        }

        $this->set(
            array(
                'events_types_languages' => $eventsTypesLanguages,
                'selected_language' => $selectedLanguage
            )
        );
    }

    private function checkNames()
    {
        $flag = ConstantsFlag::ERROR_NO;

        if (empty($this->request->data['AppointmentType']['name_en'])) {
            $flag = ConstantsFlag::ERROR_NAME_EMPTY;
        } else {
            if (empty($this->request->data['AppointmentType']['name_fr'])) {
                $this->request->data['AppointmentType']['name_fr'] = $this->request->data['AppointmentType']['name_en'];
            }
            if (empty($this->request->data['AppointmentType']['name_de'])) {
                $this->request->data['AppointmentType']['name_de'] = $this->request->data['AppointmentType']['name_en'];
            }
        }

        return $flag;
    }

    private function setVarForm()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $appointment_feelings = $this->Appointment->AppointmentFeeling->find('all');

        $contactLists = $this->ContactList->find('list', array(
            'conditions' => array(
                'OR' => array(
                    'user_id' => CakeSession::read('Auth.User.id'),
                    'aag_region_id' => $aagRegionId,
                )
            ),
            'order' => array('name')
        ));
        $this->setUsers();

        $this->set(array(
            'appointment_feelings' => $appointment_feelings,
            'contact_lists' => $contactLists
        ));
    }

    /**
     * AJAX function to cancel a visit, set status as CANCELLED.
     */
    public function cancel_visit($appointment_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        $appointment = $this->Appointment->findById($appointment_id);
        if (
            $appointment &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $action = ConstantsActions::DELETE;

            $appointment['Appointment']['appointment_status_id'] = ConstantsStatusAppointments::CANCELED;

            $fields = array(
                'Appointment' => array(
                    'id',
                    'appointment_status_id',
                )
            );

            $this->Appointment->guardar($appointment, $fields);

            $appointmentTmp = $this->Appointment->findById($appointment_id);
            $userCreatorTmp = $this->User->find('first', array(
                'conditions' => array(
                    'id' => CakeSession::read('Auth.User.id')
                ),
                'fields' => array(
                    'full_name'
                )
            ));
            $userCreator = $userCreatorTmp['User']['full_name'];
            $customer = '';
            if (!is_null($appointmentTmp['Appointment']['garage_id']) && !empty($appointmentTmp['Appointment']['garage_id'])) {
                $customerTmp = $this->Garage->find('first', array(
                    'conditions' => array(
                        'id' => $appointmentTmp['Appointment']['garage_id']
                    ),
                    'fields' => array(
                        'name'
                    )
                ));
                $customer = $customerTmp['Garage']['name'] . ' - ';
            } elseif (!is_null($appointmentTmp['Appointment']['distributor_id']) && !empty($appointmentTmp['Appointment']['distributor_id'])) {
                $customerTmp = $this->Distributor->find('first', array(
                    'conditions' => array(
                        'id' => $appointmentTmp['Appointment']['distributor_id']
                    ),
                    'fields' => array(
                        'name'
                    )
                ));
                $customer =  $customerTmp['Distributor']['name'] . ' - ';
            }
            $subject = sprintf(__t('Alert.Deleted_appointment_subject'), $customer . $userCreator);

            $appointmentUser = $this->Appointment->findById($appointment_id);
            $appointment['Appointment']['user_creation_id'] = $appointmentUser['Appointment']['user_creation_id'];
            if (!isset($this->request->data['Notification']['generate_alerts']) || $this->request->data['Notification']['generate_alerts']) {
                $this->sendDataAlert($appointment, $subject, __t('Alert.Delete_appointment'), $appointment_id, $action);
            }

            $this->redirect(Router::url(array('controller' => 'appointments', 'action' => 'edit', $appointment_id)));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setGarageFilterTask()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        if (in_array(CakeSession::read('Auth.User.Contact.position_id'), array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID))) {
            $contactTmp = $this->Contact->findById(CakeSession::read('Auth.User.contact_id'));
            $garageBdm =  array(
                $contactTmp['Contact']['id'] => $contactTmp['Contact']['full_name']
            );
        } else {
            $garageBdm = $this->Contact->getListByRoleId(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG));
        }

        if (in_array($roleId, array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))) {
            $contactTmp = $this->Contact->findById(CakeSession::read('Auth.User.contact_id'));

            $contact_contact_id = $this->Contact->findById($contactTmp['Contact']['contact_id']);

            if (isset($contact_contact_id['Contact'])) {
                $garageRsm = array(
                    $contact_contact_id['Contact']['id'] => $contact_contact_id['Contact']['full_name']
                );
            } else {
                $garageRsm = null;
            }
        } else {
            $garageRsm = $this->Contact->getListByPositionIdAndAagRegionId(array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID), $aagRegionId);
        }

        $garageCustomerStatus = Configure::read('Garage_Status') + Configure::read('Garage_Status_De');
        foreach ($garageCustomerStatus as $key => $status) {
            $garageCustomerStatus[$key] = __t($status);
        }

        $this->set(array(
            'garage_branch' => array(),
            'garage_bdm' => $garageBdm,
            'garage_rsm' => $garageRsm,
            'garage_customer_status' => $garageCustomerStatus,
        ));
    }

    private function setDistributorFilterTask()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        if (in_array(CakeSession::read('Auth.User.Contact.position_id'), array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID))) {
            $contactTmp = $this->Contact->findById(CakeSession::read('Auth.User.contact_id'));
            $distributorBdm =  array(
                $contactTmp['Contact']['id'] => $contactTmp['Contact']['full_name']
            );
        } else {
            $distributorBdm = $this->Contact->getListByRoleId(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG));
        }

        if (in_array($roleId, array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))) {
            $contactTmp = $this->Contact->findById(CakeSession::read('Auth.User.contact_id'));
            $contact_contact_id = $this->Contact->findById($contactTmp['Contact']['contact_id']);

            if (isset($contact_contact_id['Contact'])) {
                $distributorRsm = array(
                    $contact_contact_id['Contact']['id'] => $contact_contact_id['Contact']['full_name']
                );
            } else {
                $distributorRsm = null;
            }
        } else {
            $distributorRsm = $this->Contact->getListByPositionIdAndAagRegionId(array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID), $aagRegionId);
        }

        $trading_groups = $this->TradingGroup->getTradingGroup();
        $distributorActivities = $this->DistributorActivity->search_list();

        $this->set(array(
            'distributor_branch' => array(),
            'distributor_bdm' => $distributorBdm,
            'distributor_rsm' => $distributorRsm,
            'trading_groups' => isset($trading_groups) ? $trading_groups : '',
            'distributor_activities' => isset($distributorActivities) ? $distributorActivities : '',
        ));
    }

    private function setUsers()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $users = array();

        if ($roleId == ConstantsRoles::GPC_LOGISTICS_BDM) {
            $users = $this->User->getListBDMGPC();
        } elseif (!in_array($roleId, array(ConstantsRoles::ADMIN, ConstantsRoles::SUPER_ADMIN))) {
            $users = $this->User->getListIndividualUser($user['id']);
        } else {
            $users = $this->User->getListRegionBDM($aagRegionId);
        }

        $this->set(
            array(
                'users' => $users,
            )
        );
    }

    /**
     * AJAX create AppointmentObjectiveComment.
     */
    public function ajax_add_personal_objective()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $appointmentObjectiveBd = array(
                'AppointmentObjectiveComment' => array(
                    'appointment_id' => $this->request->data['appointment_id'],
                    'objective_id' => $this->request->data['objective_id'],
                    'type' => $this->request->data['type'],
                )
            );

            if (is_numeric($this->request->data['objective_id'])) {
                $appointmentObjective = $this->AppointmentObjectiveComment->new_appointment_objective($appointmentObjectiveBd);
            } else {
                $appointmentObjective = $this->AppointmentObjectiveComment->new_appointment_objective_noId($appointmentObjectiveBd);
            }

            echo $appointmentObjective ? ConstantsBooleans::YES : ConstantsBooleans::NO;

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get users name.
     * Used for dynamic selects.
     */
    public function get_users_name()
    {
        $this->verify_ajax($this->request);

        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $usersName = $this->User->getUsersAjax($this->request->query, $aagRegionId);

        $listUsers = array();
        foreach ($usersName as $key => $user) {
            $listUsers[] = array(
                'id' => $key,
                'text' => $user,
            );
        }

        $listUsersComplete['items'] = $listUsers;

        return json_encode($listUsersComplete);
    }

    /**
     * AJAX get BDM users name.
     * Used for dynamic selects.
     */
    public function get_users_bdm_name()
    {
        $this->verify_ajax($this->request);

        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $usersName = $this->User->getUsersBdmAjax($this->request->query, $aagRegionId);

        $listUsers = array();
        foreach ($usersName as $key => $user) {
            $listUsers[] = array(
                'id' => $key,
                'text' => $user,
            );
        }

        $listUsersComplete['items'] = $listUsers;

        return json_encode($listUsersComplete);
    }
}
