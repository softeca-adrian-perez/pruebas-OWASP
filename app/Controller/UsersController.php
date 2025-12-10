<?php
class UsersController extends AppController
{
    public function beforeFilter()
    {
        $this->Auth->allow(array(
            'recover_password',
            'change_password',
            'privacy_notice'
        ));
        parent::beforeFilter();
    }

    public $uses = array(
        'User',
        'UserImage',
        'Appointment',
        'ContactContactList',
        'ContactRegion',
        'DistributorContactBdm',
        'GarageContactBdm',
        'GroupPermissionUser',
        'PermissionUser',
        'PositionConfig',
        'Position',
        'Route',
        'Task',
        'UserPreference',
        'UserRecoverPassword',
        'UserReassignment',
        'GroupPermission',
        'Network',
        'DistributorNetwork',
        'TradingGroup',
        'Region',
        'AagRegion',
        'PositionConfigType',
        'Garage',
        'Email',
        'Country',
        'LogLogin',
        'Contact',
        'Alert',
        'GarageStaff',
        'Communication',
        'Distributor',
        'LogisticCenter',
        'NetworkContactBdm',
        'DistributorNetworkContactBdm',
        'Config',
        'ConfigModuleRegionRole',
        'DistributorDistributorNetwork',
        'GarageNetwork',
        'Language',
        'UserStatistic'
    );

    /**
     * Login.
     */
    public function login()
    {
        if (!$this->request->is('get')) {
            if (isset($this->request->data['cf-turnstile-response'])) {
                $response = $this->request->data['cf-turnstile-response']; //Catch captcha response
                $cfConnectingIp = $this->request->header('cf-connecting-ip') ?? null;
                $verify = $this->verify_captcha($response, $cfConnectingIp);

                $varAutomaticTest = Texto::encryptDecryptText(TOKEN_AUTOMATIC_TEST);
                $passwordToken = isset($this->request['data']['User']['password']) ? $this->request['data']['User']['password'] : null;

                // if password has a token for unit tests or automatic tests, captcha captcha is skipped
                if (!empty($varAutomaticTest) && $passwordToken && strpos($passwordToken, $varAutomaticTest)) {
                    $verify = true;
                    $this->request->data['User']['password'] = substr($passwordToken, 0, strlen($passwordToken) - strlen($varAutomaticTest));
                }

                if ($verify) {
                    $exit_user = $this->User->findByUsername($this->request['data']['User']['username']);
                    if ($exit_user && $exit_user['User']['set_login'] == null) {
                        $key = $this->User->UserRecoverPassword->add($exit_user['User']['id'], false);
                        $this->redirect(array(
                            'controller' => 'users',
                            'action' => 'change_password',
                            $exit_user['User']['id'],
                            $key['UserRecoverPassword']['key']
                        ));
                    } elseif ($exit_user && Configure::read('max_login_retries') == ($exit_user['User']['retries'])) {
                        $this->LogLogin->add_login($exit_user['User']['id'], ConstantsBooleans::NO, __t(ConstantsMessages::NULL_RETRIES));
                        $this->Session->setFlashError(__t(ConstantsMessages::NULL_RETRIES));
                    } elseif ($exit_user && $exit_user['User']['active'] == false) {
                        $this->LogLogin->add_login($exit_user['User']['id'], ConstantsBooleans::NO, __t(ConstantsMessages::INCORRECT_LOGIN));
                        $this->Session->setFlashError(__t(ConstantsMessages::INCORRECT_LOGIN));
                    } else {
                        $user = $this->Acceso->identificar_clave_maestra($this->request, $this->response);
                        if ($this->Auth->login($user)) {
                            $this->fillSession($this->Acceso->user());
                            $this->savePermissionsInSession($this->Acceso->user());
                            if (in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR))) {
                                $this->createNewArticlesAlerts($this->Acceso->user());
                            }
                            $this->User->setRetries($this->Acceso->user(), ConstantsBooleans::NO);
                            $this->registerData();
                            $this->LogLogin->add_login($exit_user['User']['id'], ConstantsBooleans::YES);

                            $user = $this->Acceso->user();

                            if (
                                in_array($user['role_id'], array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM)) ||
                                in_array($user['Contact']['position_id'], array(ConstantsPositions::NATIONAL_SALES_MANAGER_CV_ID, ConstantsPositions::NATIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID))
                            ) {
                                $this->redirect(array(
                                    'controller' => 'dashboard',
                                    'action' => 'home',
                                ));
                            } else {
                                $this->redirect($this->Auth->redirectUrl());
                            }
                        } else {
                            if ($exit_user) {
                                $this->User->setRetries($exit_user['User'], $exit_user['User']['retries'] + 1);
                                if (Configure::read('max_login_retries') == ($exit_user['User']['retries'] + 1)) {
                                    $this->LogLogin->add_login($exit_user['User']['id'], ConstantsBooleans::NO, __t(ConstantsMessages::NULL_RETRIES));
                                    $this->Session->setFlashError(__t(ConstantsMessages::NULL_RETRIES));
                                } else {
                                    $this->LogLogin->add_login($exit_user['User']['id'], ConstantsBooleans::NO, __t(ConstantsMessages::INCORRECT_LOGIN));
                                    $this->Session->setFlashError(__t(ConstantsMessages::INCORRECT_LOGIN));
                                }
                            } else {
                                $this->Session->setFlashError(__t(ConstantsMessages::INCORRECT_LOGIN));
                            }
                        }
                    }
                } else {
                    $this->Session->setFlashError(__t('Users.Cloudflare_verification'));
                }
            } else {
                $this->Session->setFlashError(__t('Users.Cloudflare_verification'));
            }
        } else {
            if ($this->Acceso->user()) {
                $this->redirect(array(
                    'controller' => 'home',
                    'action' => 'home',
                ));
            }
        }

        $this->layout = 'default_login';
    }

    private function createNewArticlesAlerts($user)
    {
        $newArticles = $this->Communication->getCommunicationsSinceLastLogin($user);
        foreach ($newArticles as $new_article) {
            $url = Router::url(array(
                'controller' => 'communications',
                'action' => 'home_section',
                $new_article['Communication']['communication_section_id'],
                $new_article['Communication']['section_subsection_id'],
                $new_article['Communication']['id'],
                ConstantsBooleans::YES
            ));
            $this->Alert->new_alert_without_email(
                ConstantsAlerts::ARTICLE,
                sprintf(__t('Alert.New_article'), $new_article['Communication']['title'], $new_article['CommunicationSection']['name']),
                $url,
                $user['id']
            );
        }
    }

    /**
     * User reassignments maintenance page.
     */
    public function reassignments()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE, ConstantsPermissionsGrouping::REASSIGNMENT) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $usersList = $this->User->find('list', array(
                'fields' => array(
                    'id',
                    'full_name'
                ),
            ));
            $usersTmp = $this->User->find('all', array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = User.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'User.role_id' => array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG)
                ),
                'fields' => array(
                    'User.id',
                    'User.name',
                    'User.surname',
                    'Contact.email',
                ),
                'order' => 'User.full_name'
            ));

            $users = array();
            $userReassignments = array();
            foreach ($usersTmp as $userTmp) {
                $userReassign = $this->UserReassignment->findByUserIdOrigin($userTmp['User']['id']);
                if (empty($userReassign)) {
                    $users[] = $userTmp;
                } else {
                    if ($userReassign['UserReassignment']['date_from'] >= date('Y-m-d') || $userReassign['UserReassignment']['permanent']) {
                        $userReassignments[] = $userReassign;
                    }
                }
            }

            $this->set(array(
                'users' => $users,
                'users_list' => $usersList,
                'user_reassignments' => $userReassignments
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search users for Reassignment.
     */
    public function ajax_search_user()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE, ConstantsPermissionsGrouping::REASSIGNMENT) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $data = $this->request->data;
            $id = $data['id'];

            if (empty($data['name'])) {
                $users = $this->User->find('all', array(
                    'joins' => array(
                        array(
                            'alias' => 'Contact',
                            'table' => 'contacts',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Contact.id = User.contact_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'User.role_id' => array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG),
                        'User.active' => ConstantsBooleans::YES
                    ),
                    'fields' => array(
                        'User.id',
                        'User.name',
                        'User.surname',
                        'Contact.email',
                    )
                ));
            } else {
                $users = $this->User->find('all', array(
                    'joins' => array(
                        array(
                            'alias' => 'Contact',
                            'table' => 'contacts',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Contact.id = User.contact_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'User.role_id' => array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG),
                        'User.full_name LIKE' => '%' . $data['name'] . '%',
                        'User.active' => ConstantsBooleans::YES
                    ),
                    'fields' => array(
                        'User.id',
                        'User.name',
                        'User.surname',
                        'Contact.email',
                    )
                ));
            }

            $this->set(
                array(
                    'users' => $users,
                    'id' => $id
                )
            );

            $this->layout = false;
            $this->render('../Users/Elements/ajax_list');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Reassign user.
     */
    public function reassign_user()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE, ConstantsPermissionsGrouping::REASSIGNMENT) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $data = $this->request->data;

            //Put it in table: $users_reassignments
            $bool1 = isset($data['User']) ? $this->User->assigned_old_values_users_reassignements($data) : false;

            //Change values
            if (isset($data['UserReassignment']['permanent']) && $data['UserReassignment']['permanent'] == ConstantsBooleans::YES) {
                $bool2 = $this->User->reassign_user($data);
            } elseif (isset($data['UserReassignment']['permanent']) && $data['UserReassignment']['permanent'] == ConstantsBooleans::NO) {
                $bool2 = true;
                $this->UserReassignment->reassign_user_scheduled_task();
            } else {
                $bool2 = false;
            }

            if ($bool1 === ConstantsBooleans::NO) {
                $this->Session->setFlashInfo(__t('User.Reassignment_bad_empty'));
                $this->redirect(array(
                    'controller' => 'users',
                    'action' => 'reassignments'
                ));
            } elseif (!$bool1 && !$bool2) {
                $this->Session->setFlashError(__t('User.Reassignment_bad'));
                $this->redirect(array(
                    'controller' => 'users',
                    'action' => 'reassignments'
                ));
            } else {
                $this->Session->setFlashSuccess(__t('User.Reassignment_well'));
                $this->redirect(array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                ));
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Reverte and delete UserReassignment.
     */
    public function revert_and_delete_reassignment($user_id_origin, $user_id_destination, $dateFrom)
    {
        $reassignments = $this->UserReassignment->findAllByUserIdOriginAndUserIdDestinationAndDateFrom($user_id_origin, $user_id_destination, $dateFrom);
        if (
            $reassignments &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE, ConstantsPermissionsGrouping::REASSIGNMENT) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $this->UserReassignment->make_revert_reassignment($reassignments, true);

            $this->redirect(Router::url(
                array(
                    'controller' => 'users',
                    'action' => 'reassignments'
                )
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX check correct reassignment.
     */
    public function ajax_check_correct_reassignment()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE, ConstantsPermissionsGrouping::REASSIGNMENT) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $flag = false;

            $userOriginId = $this->request->data['user_origin_id'];
            $dateFrom = $this->request->data['start_date'];
            $permanent = $this->request->data['permanent'];

            //a - B ; B - x -> Origin is a destination user in BD. (Evalue date fields)
            $userAssignedBdOrign = $this->UserReassignment->findAllByUserIdDestinationAndPermanent($userOriginId, false);

            if (!empty($userAssignedBdOrign)) {
                foreach ($userAssignedBdOrign as $elem) {
                    if (
                        (
                            Fecha::toFormatoBd($dateFrom) > $elem['UserReassignment']['date_from'] &&
                            Fecha::toFormatoBd($dateFrom) < $elem['UserReassignment']['date_to']
                        ) ||
                        $permanent == 'true'
                    ) {
                        $flag = true;
                    }
                }
                if ($flag) {
                    $success_origin = array(
                        'success_origin' => 'true'
                    );
                    $jsArray = json_encode($success_origin);
                    echo $jsArray;
                }
            }

            if (!$flag) {
                $success = array();
                $jsArray = json_encode($success);
                echo $jsArray;
            }

            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Function to restore the reassignment to the initial state
     */
    public function ajax_restructure_reassignment()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE, ConstantsPermissionsGrouping::REASSIGNMENT) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $userOriginId = $this->request->data['user_origin_id'];
            $dateFrom = $this->request->data['start_date'];
            $permanent = $this->request->data['permanent'];

            $userAssignedBdOrign = $this->UserReassignment->findAllByUserIdDestinationAndPermanent($userOriginId, ConstantsBooleans::NO);

            foreach ($userAssignedBdOrign as $rowReassigned) {
                //Overlapping dates
                if (
                    (
                        Fecha::toFormatoBd($dateFrom) >= $rowReassigned['UserReassignment']['date_from'] &&
                        Fecha::toFormatoBd($dateFrom) < $rowReassigned['UserReassignment']['date_to']
                    ) ||
                    $permanent == 'true'
                ) {
                    //origin_id
                    $user_id = $rowReassigned['UserReassignment']['user_id_origin'];
                    $userTmp = $this->User->findById($user_id);
                    //contact user origin
                    $contact_id = $userTmp['User']['contact_id'];

                    if ($rowReassigned['UserReassignment']['garage_contact_bdm_id'] != null) {
                        $garageContactBdm = $this->GarageContactBdm->findById($rowReassigned['UserReassignment']['garage_contact_bdm_id']);
                        $garageContactBdm['GarageContactBdm']['contact_id'] = $contact_id;
                        $fields = array(
                            'GarageContactBdm' => array(
                                'contact_id'
                            )
                        );
                        $this->GarageContactBdm->guardar($garageContactBdm, $fields);
                    }

                    if ($rowReassigned['UserReassignment']['distributor_contact_bdm_id'] != null) {
                        $distributorContactBdm = $this->DistributorContactBdm->findById($rowReassigned['UserReassignment']['distributor_contact_bdm_id']);
                        $distributorContactBdm['DistributorContactBdm']['contact_id'] = $contact_id;
                        $distributorContactBdm['DistributorContactBdm']['updated_at'] = date('Y-m-d H:i:s');
                        $fields = array(
                            'DistributorContactBdm' => array(
                                'contact_id',
                                'updated_at'
                            )
                        );
                        $this->DistributorContactBdm->guardar($distributorContactBdm, $fields);
                    }

                    if ($rowReassigned['UserReassignment']['contact_contact_list_id'] != null) {
                        $contactContactList = $this->ContactContactList->findById($rowReassigned['UserReassignment']['contact_contact_list_id']);
                        $contactContactList['ContactContactList']['contact_id'] = $contact_id;
                        $fields = array(
                            'ContactContactList' => array(
                                'contact_id'
                            )
                        );
                        $this->ContactContactList->guardar($contactContactList, $fields);
                    }

                    if ($rowReassigned['UserReassignment']['task_id'] != null) {
                        $task = $this->Task->findById($rowReassigned['UserReassignment']['task_id']);
                        $task['Task']['user_assigned_id'] = $user_id;
                        $fields = array(
                            'Task' => array(
                                'user_assigned_id'
                            )
                        );
                        $this->Task->guardar($task, $fields);
                    }

                    if ($rowReassigned['UserReassignment']['appointment_id'] != null) {
                        $appointment = $this->Appointment->findById($rowReassigned['UserReassignment']['appointment_id']);
                        $appointment['Appointment']['user_assigned_id'] = $user_id;
                        $fields = array(
                            'Appointment' => array(
                                'user_assigned_id'
                            )
                        );
                        $this->Appointment->guardar($appointment, $fields);
                    }

                    if ($rowReassigned['UserReassignment']['route_id'] != null) {
                        $route = $this->Route->findById($rowReassigned['UserReassignment']['route_id']);
                        $route['Route']['user_assigned_id'] = $user_id;
                        $fields = array(
                            'Route' => array(
                                'user_assigned_id'
                            )
                        );
                        $this->Route->guardar($route, $fields);
                    }

                    $this->UserReassignment->delete($rowReassigned['UserReassignment']['id']);
                }
            }

            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    private function fillSession($user)
    {
        require_once(dirname(__FILE__) . '/../Vendor/Mobile_Detect/Mobile_Detect.php');
        $mobileDetect = new Mobile_Detect();
        if ($mobileDetect->isTablet()) {
            CakeSession::write('Auth.User.device', ConstantsDevices::TABLET); // T001 SECURITY - It is not changed
        } elseif ($mobileDetect->isMobile()) {
            CakeSession::write('Auth.User.device', ConstantsDevices::MOBILE); // T001 SECURITY - It is not changed
        } else {
            CakeSession::write('Auth.User.device', ConstantsDevices::DESKTOP); // T001 SECURITY - It is not changed
        }

        CakeSession::write('Auth.User.language_code', Configure::read('LANGUAGE_CODE_DEFAULT')); // T001 SECURITY - It is not changed

        $aagConfig = $this->Config->get_list_config();
        CakeSession::write('Auth.User.Config', $aagConfig); // T001 SECURITY - It is not changed

        $aagConfigModuleRegionRole = $this->ConfigModuleRegionRole->get_list_config_module_region_role($user['aag_region_id'], $user['role_id']);
        CakeSession::write('Auth.User.Config.Module', $aagConfigModuleRegionRole); // T001 SECURITY - It is not changed

        $userPreferencesTmp = $this->UserPreference->findAllByUserId($user['id']);
        $userPreferences = array();
        foreach ($userPreferencesTmp as $userPreference) {
            $userPreferences[$userPreference['UserPreference']['preference_id']] = $userPreference['UserPreference']['value'];
        }
        CakeSession::write('Auth.User.Preferences', $userPreferences); // T001 SECURITY - It is not changed

        $img = $this->UserImage->findByUserId($user['id']);
        if (!empty($img)) {
            CakeSession::write('Auth.User.img', $img['UserImage']['id']); // T001 SECURITY - It is not changed
        }

        if ($user['language_id']) {
            $language = $this->Language->findById($user['language_id']);
            CakeSession::write('Auth.User.language_code', $language['Language']['code']); // T001 SECURITY - It is not changed
            CakeSession::write('GOOGLE_MAPS_API', 'https://maps.googleapis.com/maps/api/js?libraries=places&language=' . $language['Language']['code'] . '&key=' . Texto::encryptDecryptText(GOOGLE_API_KEY, false));
        }

        if ($user['role_id'] == ConstantsRoles::GARAGE || $user['role_id'] == ConstantsRoles::DISTRIBUTOR) {
            if ($user['garage_id'] != null) {
                $garageNetworks = $this->GarageNetwork->getAllByGarageIdAndActiveAndGarageSpecificQuoting($user['garage_id']);
                $networks = Hash::extract($garageNetworks, "{n}.GarageNetwork.network_id");
                CakeSession::write('Auth.User.networks', $networks); // T001 SECURITY - It is not changed
                if (!empty($networks[0])) {
                    CakeSession::write('Auth.User.current_network', $networks[0]); // T001 SECURITY - It is not changed
                } else {
                    CakeSession::write('Auth.User.current_network', null); // T001 SECURITY - It is not changed
                }
            } elseif ($user['distributor_id'] != null) {
                $distributor = $this->Distributor->findById($user['distributor_id']);
                CakeSession::write('Auth.User.trading_group', $distributor['Distributor']['trading_group_id']); // T001 SECURITY - It is not changed

                $distributorsNetworks = $this->DistributorDistributorNetwork->getAllByDistributorIdAndActive($user['distributor_id']);
                $networks = Hash::extract($distributorsNetworks, "{n}.DistributorDistributorNetwork.network_id");
                CakeSession::write('Auth.User.distributor_networks', $networks); // T001 SECURITY - It is not changed
                if (!empty($networks)) {
                    CakeSession::write('Auth.User.current_network', $networks[0]); // T001 SECURITY - It is not changed
                } else {
                    CakeSession::write('Auth.User.current_network', null); // T001 SECURITY - It is not changed
                }
            }
        }

        if ($user['role_id'] != ConstantsRoles::SUPER_ADMIN && $user['aag_region_id'] != null) {
            $aagRegion = $this->AagRegion->findById($user['aag_region_id']);
            CakeSession::write('Auth.User.current_region', $aagRegion['AagRegion']['id']); // T001 SECURITY - It is not changed
            CakeSession::write('Auth.User.aag_region_id', $user['aag_region_id']); // T001 SECURITY - It is not changed
        }

        if ($user['role_id'] == ConstantsRoles::SUPER_ADMIN) {
            CakeSession::write('Auth.User.aag_region_id', $user['aag_region_id']); // T001 SECURITY - It is not changed

            $networks = $this->Network->find('all');
            $networksIds = Hash::extract($networks, "{n}.Network.id");
            CakeSession::write('Auth.User.networks', $networksIds); // T001 SECURITY - It is not changed
            CakeSession::write('Auth.User.current_network', $networksIds[0]); // T001 SECURITY - It is not changed

            $aagRegions = $this->AagRegion->find('all');
            $aagRegionsIds = Hash::extract($aagRegions, "{n}.AagRegion.id");
            CakeSession::write('Auth.User.regions', $aagRegionsIds); // T001 SECURITY - It is not changed
            CakeSession::write('Auth.User.current_region', $aagRegionsIds[0]); // T001 SECURITY - It is not changed

            $countries = $this->Country->find('all');
            $countriesIds = Hash::extract($countries, "{n}.Country.id");
            CakeSession::write('Auth.User.countries', $countriesIds); // T001 SECURITY - It is not changed
            CakeSession::write('Auth.User.current_country', $countriesIds[0]); // T001 SECURITY - It is not changed
        }

        $contact = $this->Contact->findById(CakeSession::read('Auth.User.contact_id'));
        $position = $this->Position->findById($contact['Contact']['position_id']);
        CakeSession::write('Auth.User.position', $position['Position']['name' . __s()]); // T001 SECURITY - It is not changed

        $version = file_get_contents(VERSION_CACHE_NETWORK);
        CakeSession::write('Auth.User.version_cache_network', Numero::validateSessionNumeric($version));
    }

    /**
     * Logout.
     */
    public function logout()
    {
        $this->redirect($this->Auth->logout());
    }

    /**
     * User listing page.
     */
    public function listing()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::USERS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $active = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $positionsList = $this->Position->getlist();
            $positions = $this->optPositions($positionsList);

            $positionsList = $this->Position->search_list();

            $searcher = $this->request->query;
            $searcher['aag_region_id'] = $aagRegionId;
            $this->request->data['Buscador'] = $searcher;

            $users = $this->custom_pagination(
                $this->User->_query('listing'),
                $this->User->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'User',
                null,
                'PaginatorOrderCustom'
            );

            $roles = $this->User->Role->search_list();

            $arrayGarageName = array();
            if (!empty($searcher['garage_id'])) {
                $arrayGarageName = $this->Garage->getGaragesNameByIdGarage($searcher['garage_id']);
            }

            $contactsEmails = array();
            if (!empty($searcher['contact_id'])) {
                $contactsEmails = $this->Contact->findContactsEmailsConditions($searcher['contact_id'], $aagRegionId);
            }

            $this->set(array(
                'users' => $users,
                'roles' => $roles,
                'positions' => $positions,
                'positions_list' => $positionsList,
                'active' => $active,
                'userAagRegionId' => $aagRegionId,
                'array_garage_name' => $arrayGarageName,
                'contacts_emails' => json_encode($contactsEmails),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add user to contact.
     */
    public function add_user_to_contact($contact_id)
    {
        $contact = $this->Contact->findByIdAndAagRegionId($contact_id, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $contact &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
                )
            )
        ) {
            $languages = $this->User->Language->getLanguagesIdName();
            $position = $this->Position->findById($contact['Contact']['position_id']);
            $positions = $this->Contact->Position->search_list();
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $roleId = $user['role_id'];
            $countries = $this->Country->get_country_by_region(CakeSession::read('Auth.User.aag_region_id'));
            $countries[ConstantsConfigSelect::ALL] = __t('General.All');
            $country_id = $user['country_id'];

            $aagRegions = $this->AagRegion->find(
                'list',
                array(
                    'conditions' => array(
                        'id' =>  $aagRegionId
                    )
                )
            );

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'contacts',
                    'action' => 'home',
                ),
            );

            if (!$this->request->is('get')) {
                $this->request->data['User']['contact_id'] = $contact_id;
                $this->request->data['User']['aag_region_id'] = $aagRegionId;
                $userBd = $this->User->add($this->request->data);
                if ($userBd) {

                    $this->UserRecoverPassword->add_user_password($userBd['User']['id']);

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(array(
                        'controller' => 'contacts',
                        'action' => 'home',
                    ));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                $this->request->data['User']['name'] = $contact['Contact']['first_name'];
                $this->request->data['User']['surname'] = $contact['Contact']['last_name'];
                $this->request->data['User']['role_id'] = $position['Position']['role_id'];
            }

            unset($this->request->data['User']['password']);
            unset($this->request->data['User']['password_repetido']);

            $this->set(array(
                'role_id' => $roleId,
                'aag_regions' => $aagRegions,
                'languages' => $languages,
                'cancel_action' => $cancelAction,
                'positions' => $positions,
                'contact' => $contact,
                'countries' => $countries,
                'country_id' => $country_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit User.
     */
    public function edit($user_guid = null)
    {
        $user = $this->User->findByGuidAndAagRegionId($user_guid, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $user &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::USERS)
                )
            )
        ) {
            $contact = $this->Contact->findById($user['User']['contact_id']);
            $user = array_merge($user, $contact);

            $positions = $this->Contact->Position->search_list();
            $position = $this->Position->findById($user['Contact']['position_id']);

            $countries = $this->Country->get_country_by_region(CakeSession::read('Auth.User.aag_region_id'));
            if (in_array($user['User']['role_id'], array(ConstantsRoles::SUPER_ADMIN, ConstantsRoles::ADMIN, ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG))) {
                $countries[ConstantsConfigSelect::ALL] = __t('General.All');
            }

            if (!$user) {
                throw new NotFoundException();
            }

            if ($this->request->is('get')) {
                $this->request->data = $user;
            } else {
                $this->request->data['User']['guid'] = $user_guid;

                if ($this->User->edit($this->request->data)) {
                    $errorRm = false;

                    // CHANGE ACTIVE GARAGE USER FROM RM
                    if (
                        $user['User']['role_id'] == ConstantsRoles::GARAGE &&
                        $this->request->data['User']['active'] != $user['User']['active']
                    ) {
                        $garage = $this->Garage->findById($user['User']['garage_id']);
                        if (isset($garage['Garage']) && $garage['Garage']['repairmaintenance']) {
                            $this->ApiRm = ClassRegistry::init("ApiRm.ApiRm");
                            $data = $user['User']['id'];
                            $response = $this->ApiRm->change_active_user_rm($data, $this->request->data['User']['active'], false);
                            $errorRm = !$response->success;
                        }
                    }

                    if (!$errorRm) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect(array(
                            'action' => 'edit/' . $this->request->data['User']['guid']
                        ));
                    } else {
                        // RM ERROR
                        $this->Session->setFlashError(__t("Repair.Error"));
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            unset($this->request->data['User']['password']);
            unset($this->request->data['User']['password_repetido']);

            $this->setVarForm();
            $this->set(array(
                'is_my_data' => false,
                'positions' => $positions,
                'position' => $position,
                'user' => $user,
                'countries' => $countries,
                'contact' => $contact
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * My data.
     */
    public function my_data()
    {
        $user = $this->Acceso->user();
        $aagRegions = $this->AagRegion->region_list();
        $userBd = $this->User->findById(CakeSession::read('Auth.User.id'));
        $positions = $this->Position->search_list();
        $contact = $this->Contact->findById($user['Contact']['id']);
        $regionsTmp = $this->Region->getRegionsListByContact($user['Contact']['id']);
        $regionsList = $this->Region->getRegionsListCodeByContact($user['Contact']['id']);
        $regions = Hash::extract($regionsTmp, '{n}.Region.id');
        $position = $this->Position->findById($user['Contact']['position_id']);
        $countries = $this->Country->get_country_by_region(CakeSession::read('Auth.User.aag_region_id'));
        $countries[ConstantsConfigSelect::ALL] = __t('General.All');

        if (
            in_array($user['role_id'], array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM)) ||
            in_array($user['Contact']['position_id'], array(ConstantsPositions::NATIONAL_SALES_MANAGER_CV_ID, ConstantsPositions::NATIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID))
        ) {
            $urlCancel = Router::url(array(
                'controller' => 'dashboard',
                'action' => 'home'
            ));
        } else {
            $urlCancel = Router::url(array(
                'controller' => 'home',
                'action' => 'home'
            ));
        }

        $principalImage = $this->User->getPrincipalImage($user['id']);
        $user['UserImagen']['Imagen'] = $principalImage;
        $imgUrl = ConstantsPath::ADD_IMAGE_IMAGE;

        if ($this->request->is('get')) {
            $this->request->data['User'] = $userBd['User'];
            $this->request->data['Contact'] = $contact['Contact'];
            $this->request->data['Contact']['regions'] = $regions;
        } else {
            $error_ext = false;
            $check_image = false;
            $this->request->data['User']['id'] = $userBd['User']['id'];
            if ($user['role_id'] != ConstantsRoles::SUPER_ADMIN) {
                $this->request->data['User']['aag_region_id'] = $userBd['User']['aag_region_id'];
            }
            if (!empty($this->request->data['User']['new_profile_image'])) {
                //When we change the photo
                if ($this->request->data['User']['new_profile_image'] != ConstantsUserImage::CONST_DELETE_IMAGE) {
                    $check_image = FileManager::check_image($this->request->data['User']['image-input'], $this->request->data['User']['new_profile_image']);
                    if ($check_image == ConstantsFileErrorTypes::OK) {
                        $this->request->data['User']['image'] = $this->User->uploadProfileImage($this->request->data['User']['new_profile_image'], $this->request->data['User']['image-input']);
                        if (!$this->request->data['User']['image']) {
                            $error_ext = true;
                        }
                        $this->request->data['User']['new_profile_image'] = null;
                    }
                } else {
                    //When we delete the photo
                    unset($this->request->data['User']['new_profile_image']);
                    unset($this->request->data['User']['image']);
                }
            } else {
                //When the photo doesn't change
                if (isset($this->request->data['User']['image'])) {
                    $imgUrl = FileManager::get_url(substr(ConstantsPath::DIR_USER_IMAGES_CROP, 1) . '/' . $this->request->data['User']['image']);
                }
            }
            if (!$error_ext) {
                if (!$check_image || $check_image == ConstantsFileErrorTypes::OK) {
                    if (isset($this->request->data['User']['country_id']) && !empty($this->request->data['User']['country_id']) && $this->User->edit_my_data($this->request->data)) {
                        CakeSession::write('Auth.User.country_id', $this->request->data['User']['country_id']);
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect(array(
                            'action' => 'my_data'
                        ));
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } elseif ($check_image == ConstantsFileErrorTypes::SIZE_ERROR) {
                    $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                }
            } else {
                $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
            }
        }

        unset($this->request->data['User']['password']);
        unset($this->request->data['User']['password_repetido']);

        $this->setVarForm();

        $updatedUser = $this->User->findById(CakeSession::read('Auth.User.id'));
        if (isset($updatedUser)) {
            $user = $updatedUser;
        }

        $this->set(array(
            'is_my_data' => true,
            'user' => $user,
            'positions' => $positions,
            'position' => $position,
            'regions' => $regions,
            'regions_list' => $regionsList,
            'principal_image' => $principalImage,
            'img_url' => $imgUrl,
            'url_cancel' => $urlCancel,
            'aag_regions' => $aagRegions,
            'countries' => $countries,
            'contact' => $contact
        ));
    }

    /**
     * Reset User password.
     */
    public function reset_password()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::USERS)
            )
        ) {
            $user = $this->Acceso->user();
            $userBd = $this->User->findById(CakeSession::read('Auth.User.id'));

            if (!$this->request->is('get')) {
                $newPassword = $this->request->data['User']['password'];
                $newPasswordRepeat = $this->request->data['User']['password_repetido'];
                $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
                $newPasswordHash = $passwordHasher->hash($newPassword);
                $this->request->data['User']['id'] = $userBd['User']['id'];

                //Verifies repeat password and old password
                if ($newPasswordHash != $userBd['User']['password']) {
                    if ($this->User->changePassword($this->request->data, $userBd['User']['id'])) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect(array(
                            'action' => 'my_data'
                        ));
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED_2));
                }
            }

            $this->set(array(
                'user' => $user,
                'user_bd' => $userBd,
                'new_password' => isset($new_password) ? $new_password : '',
                'new_password_repeat' => isset($new_password_repeat) ? $new_password_repeat : '',
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * My preferences.
     */
    public function my_preferences()
    {
        $user = $this->Acceso->user();

        $userPreferencesTmp = $this->UserPreference->findAllByUserId($user['id']);
        $userPreferences = array();
        foreach ($userPreferencesTmp as $userPreference) {
            $userPreferences[$userPreference['UserPreference']['preference_id']] = $userPreference['UserPreference']['value'];
        }

        $preferenceCalendar = isset($userPreferences[1]) ? $userPreferences[1] : ConstantsCalendarView::WORKWEEK;

        $calendarViews = array(
            ConstantsCalendarView::DAY => __t('UserPreference.Day'),
            ConstantsCalendarView::WORKWEEK => __t('UserPreference.Work_week'),
            ConstantsCalendarView::WEEK => __t('UserPreference.Week'),
            ConstantsCalendarView::MONTH => __t('UserPreference.Month'),
        );

        $pagination = array(
            ConstantsPagination::SIZE_PAGE_SMALL => ConstantsPagination::SIZE_PAGE_SMALL,
            ConstantsPagination::SIZE_PAGE_MEDIUM => ConstantsPagination::SIZE_PAGE_MEDIUM,
            ConstantsPagination::SIZE_PAGE_LARGE => ConstantsPagination::SIZE_PAGE_LARGE,
        );

        if (!$this->request->is('get')) {
            if ($this->UserPreference->add($this->request->data['UserPreference']['calendar'], $user['id'])) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                $this->redirect(array(
                    'action' => 'my_preferences'
                ));
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
            }
        }

        $this->set(array(
            'calendar_views' => $calendarViews,
            'pagination' => $pagination,
            'preference_calendar' => $preferenceCalendar,
        ));
    }

    /**
     * AJAX save user preferences.
     */
    public function ajax_save_preferences()
    {
        $this->verify_ajax($this->request);

        $preferences = $this->request->data;

        foreach ($preferences as $preference_id => $value) {
            $this->UserPreference->save_preferences(CakeSession::read('Auth.User.id'), $preference_id, $value);
        }

        $userPreferencesTmp = $this->UserPreference->findAllByUserId(CakeSession::read('Auth.User.id'));
        $userPreferences = array();
        foreach ($userPreferencesTmp as $userPreference) {
            $userPreferences[$userPreference['UserPreference']['preference_id']] = $userPreference['UserPreference']['value'];
        }
        CakeSession::write('Auth.User.Preferences', $userPreferences); // T001 SECURITY - It is not changed

        $this->autoRender = false;
    }

    private function setVarForm()
    {
        $actualUser = $this->Acceso->user();
        $roles = $this->User->Role->search_list();
        $languages = $this->User->Language->getLanguagesCodeNameWithoutLoco();
        $userRegions = $this->User->AagRegion->region_list();
        $aagRegions = $this->AagRegion->region_list();

        $this->set(array(
            'actual_user' => $actualUser,
            'roles' => $roles,
            'languages' => $languages,
            'regions_users' => $userRegions,
            'aag_regions' => $aagRegions
        ));
    }

    /**
     * Unlock User.
     */
    public function unset_lock($user_guid = null)
    {
        $user = $this->User->findByGuidAndAagRegionId($user_guid, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $user &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::USERS)
                )
            )
        ) {
            $this->User->setRetries($user['User'], ConstantsBooleans::NO, true);
            $this->Session->setFlashSuccess(__t('Contact.Well_unlocked'));
            $this->redirect(
                array(
                    'controller' => 'users',
                    'action' => 'listing',
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete User and its associated registers.
     */
    public function delete($user_guid = null)
    {
        $user = $this->User->findByGuidAndAagRegionId($user_guid, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $user &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER, ConstantsPermissionsGrouping::DELETE_USER) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::USERS)
                )
            )
        ) {
            $classError = $this->User->check_delete($user['User']['id']);
            if ($classError !== true) {
                $this->Session->setFlashError(__t('Alert.' . 'Error_user_' . $classError));
            } else {
                $permissionsUser = $this->PermissionUser->findAllByUserId($user['User']['id']);
                if ($permissionsUser) {
                    foreach ($permissionsUser as $permission_user) {
                        $this->PermissionUser->eliminar($permission_user['PermissionUser']['id']);
                    }
                }

                $userRecoverPasswords = $this->UserRecoverPassword->findAllByUserId($user['User']['id']);
                if ($userRecoverPasswords) {
                    foreach ($userRecoverPasswords as $user_recover_password) {
                        $this->UserRecoverPassword->delete($user_recover_password['UserRecoverPassword']['id']);
                    }
                }

                $groupsPermissionUser = $this->GroupPermissionUser->findAllByUserId($user['User']['id']);
                if ($groupsPermissionUser) {
                    foreach ($groupsPermissionUser as $group_permission_user) {
                        $this->GroupPermissionUser->delete($group_permission_user['GroupPermissionUser']['id']);
                    }
                }

                $userImage = $this->UserImage->findByUserId($user['User']['id']);
                if ($userImage) {
                    $this->UserImage->deleteUserImage($userImage['UserImage']['id']);
                }

                $userPreference = $this->UserPreference->findByUserId($user['User']['id']);
                if ($userPreference) {
                    $this->UserPreference->delete($userPreference['UserPreference']['id']);
                }

                //Delete alerts associated to the user.
                $userAlerts = $this->Alert->findAllByUserId($user['User']['id']);
                if (isset($userAlerts)) {
                    foreach ($userAlerts as $user_alert) {
                        $this->Alert->delete($user_alert['Alert']['id']);
                    }
                }

                $deleteUser = $this->User->delete($user['User']['id']);

                if ($deleteUser) {
                    // DISABLE USER FROM RM
                    if ($user['User']['role_id'] == ConstantsRoles::GARAGE && $user['User']['aag_region_id'] == ConstantsAAGRegionId::UK) {
                        $garage = $this->Garage->findById($user['User']['garage_id']);
                        if ($garage['Garage']['repairmaintenance']) {
                            $this->ApiRm = ClassRegistry::init("ApiRm.ApiRm");
                            $this->ApiRm->change_active_user_rm($user['User']['id'], false, true);
                        }
                    }
                    $this->Session->setFlashSuccess(__t('Contact.Well_deleted'));
                } else {
                    $this->Session->setFlashSuccess(__t('Contact.Bad_deleted'));
                }
            }

            $this->redirect(
                array(
                    'controller' => 'users',
                    'action' => 'listing',
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * User view
     */
    public function view($user_guid = null)
    {
        $this->User->contain('Role');
        $user = $this->User->findByGuidAndAagRegionId($user_guid, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $user &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::USERS)
                )
            )
        ) {
            $userImage = $this->UserImage->findByUserId($user['User']['id']);
            $contact = $this->Contact->findById($user['User']['contact_id']);
            $user = array_merge($user, $contact);
            $positions = $this->Contact->Position->search_list();

            $contact['garage_networks'] = $this->NetworkContactBdm->findAllByContactId($contact['Contact']['id']);
            $contact['distributor_networks'] = $this->DistributorNetworkContactBdm->findAllByContactId($contact['Contact']['id']);
            $networksImages = $this->Network->find('list', array('fields' => 'image'));
            $distributorNetworksImages = $this->DistributorNetwork->find('list', array('fields' => 'image'));
            $contactRegions = $this->ContactRegion->findAllByContactId($contact['Contact']['id']);
            $regions = $this->Region->find('list', array('fields' => 'id,name'));
            $aagRegion = $this->AagRegion->findById($user['User']['aag_region_id']);
            $aagRegionName = $aagRegion['AagRegion']['name'];
            $countries = $this->Country->get_country_by_region($user['User']['aag_region_id']);

            $this->setVarForm();
            $this->set(array(
                'user' => $user,
                'user_image' => $userImage,
                'regions' => $regions,
                'contact_regions' => $contactRegions,
                'positions' => $positions,
                'contact' => $contact,
                'networks_images' => $networksImages,
                'distributor_networks_images' => $distributorNetworksImages,
                'garage_name' => $contact['Contact']['garage_id'] ? $this->Garage->findById($contact['Contact']['garage_id']) : null,
                'distributor_name' => $contact['Contact']['distributor_id'] ? $this->Distributor->findById($contact['Contact']['distributor_id']) : null,
                'logistic_name' => $contact['Contact']['logistic_center_id'] ? $this->LogisticCenter->findById($contact['Contact']['logistic_center_id']) : null,
                'aag_region_name' => $aagRegionName,
                'countries' => $countries
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Recover password from login.
     */
    public function recover_password()
    {
        if (!$this->request->is('get')) {
            $response = $this->request->data['cf-turnstile-response']; //Catch captcha response
            $cfConnectingIp = $this->request->header('cf-connecting-ip') ?? null;
            if ($this->verify_captcha($response, $cfConnectingIp)) {
                $user = $this->User->findByUsername($this->request->data['User']['username']);

                if (empty($user)) {
                    $this->Session->setFlashError(__t('User.Error_username'));
                } else {
                    if (Configure::read('max_login_retries') == ($user['User']['retries'])) {
                        $this->Session->setFlashError(__t(ConstantsMessages::NULL_RETRIES));
                    } else {
                        $bool = $this->UserRecoverPassword->add($user['User']['id']);
                        if ($bool) {
                            $this->Session->setFlashSuccess(__t('User.Recover_password'));
                        } else {
                            $this->Session->setFlashError(__t('User.Error_username'));
                        }
                    }
                }
            }
        }

        $this->layout = 'default_login';
    }

    /**
     * Recover User password from user list.
     */
    public function recover_password_user_list($username)
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::USERS)
            )
        ) {
            if (!$this->request->is('get')) {
                $user = $this->User->findByUsername($username);
                if (empty($user)) {
                    $errorText = __t('User.Error_username');
                } else {
                    if (Configure::read('max_login_retries') == ($user['User']['retries'])) {
                        $errorText = __t(ConstantsMessages::NULL_RETRIES);
                    } else {
                        if ($this->User->UserRecoverPassword->add($user['User']['id'])) {
                            $precess = 'true';
                        } else {
                            $errorText = __t('User.Error_username');
                        }
                    }
                }
            }

            $jsArray = json_encode(
                array(
                    'precess' => $precess ?? 'false',
                    'error_text' => $errorText ?? ''
                )
            );
            echo $jsArray;

            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Change user password.
     */
    public function change_password($user_id, $key)
    {
        $this->User->UserRecoverPassword->Behaviors->disable('FechaHora');
        $recoverPassword = $this->User->UserRecoverPassword->findByUserId($user_id);

        $error_rm = false;
        $user_bd = $this->User->findById($user_id);
        $contact = $this->Contact->findById($user_bd['User']['contact_id']);

        if (!$recoverPassword || $recoverPassword['UserRecoverPassword']['key'] != $key || Fecha::getDistance($recoverPassword['UserRecoverPassword']['creation_date'], date('Y-m-d H:i:s'), null, 'h') > 1) {
            throw new UnauthorizedException();
        }

        if (!$this->request->is('get')) {
            $response = $this->request->data['cf-turnstile-response']; //Catch captcha response
            $cfConnectingIp = $this->request->header('cf-connecting-ip') ?? null;
            if ($this->verify_captcha($response, $cfConnectingIp)) {
                if ($this->User->changePassword($this->request->data, $user_id)) {
                    /**
                     * After commit we get user as password has changed.
                     * Password is hashed.
                     */
                    $user_bd_password = $this->User->findById($user_id);

                    //RepairMaintenance API call
                    $user_bd_password['User']['password'] = $this->request->data['User']['password'];
                    $this->check_send_user_to_repair($user_bd_password, $contact, $error_rm);
                    unset($user_bd_password['User']['password']);
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'login',
                    ));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }
        }

        unset($this->request->data['User']['password']);
        unset($this->request->data['User']['password_repetido']);

        $this->layout = 'default_login';
    }

    private function savePermissionsInSession($user)
    {
        $permissions = $this->User->Permission->obtenerPermisosUsuario($user);
        $permissionsv2 = $this->User->Permission->obtenerPermisosUsuariov2($user);
        $permissionsv2_reverse = $this->User->Permission->reversePermissions($permissionsv2);

        CakeSession::write(ConstantsSessionVariables::PERMISOS_DEL_USUARIO, $permissions); // T001 SECURITY - It is not changed
        CakeSession::write('Auth.User.Permissionsv2', $permissionsv2); // T001 SECURITY - It is not changed
        CakeSession::write('Auth.User.Permissionsv2Reverse', $permissionsv2_reverse); // T001 SECURITY - It is not changed
    }

    /**
     * AJAX get permissions by group.
     */
    public function ajax_get_permission_by_group()
    {
        $this->verify_ajax($this->request);

        $user_id = $this->request->data['user_id'];
        $user = $this->User->findById($user_id);

        if (
            $user &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER, ConstantsPermissionsGrouping::VIEW_PERMISSION) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::USERS)
                )
            )
        ) {
            $groupPermissionId = $this->request->data['group_permission_id'];

            $this->User->contain(
                array(
                    'Role',
                    'GroupPermission',
                )
            );

            if (!$user) {
                throw new NotFoundException();
            }

            $permissionsMatrix = $this->User->getMatrixPermissionsUserByGroupPermission($user_id, $groupPermissionId);

            $this->set(
                array(
                    'user' => $user,
                    'matriz_permissions' => $permissionsMatrix,
                    'groupPermissions' => $this->User->GroupPermission->search_list()
                )
            );

            $this->layout = null;
            $this->render('/Users/Elements/matriz_permissions');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Manage permissions config.
     */
    public function manage_permissions_config($user_guid)
    {
        $user = $this->User->findDataUser($user_guid, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $user &&
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER, ConstantsPermissionsGrouping::VIEW_PERMISSION) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::USERS)
            )
        ) {
            $positions = $this->Position->findAllById($user['Contact']['position_id']);

            $positionsConfig = $this->PositionConfig->getPositionConfig($positions);
            $positionsConfigTypes = $this->PositionConfigType->search_list();
            $groupPermissions = $this->GroupPermission->search_list();
            $networks = $this->Network->networksList();
            $regions = $this->Region->search_list();
            $tradingGroups = $this->TradingGroup->tradingGroupsList();

            $this->set(
                array(
                    'user' => $user,
                    'positions_config' => $positionsConfig,
                    'positions_config_types' => $positionsConfigTypes,
                    'group_permissions' => $groupPermissions,
                    'networks' => $networks,
                    'trading_groups' => $tradingGroups,
                    'regions' => $regions,
                    'matriz_permissions' => array()
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    private function optPositions($positions)
    {
        $opt_positions = array();
        foreach ($positions as $position) {
            $opt_positions[$position['Role']['name' . __s()]][$position['Position']['id']] = '- ' . $position['Position']['name' . __s()];
        }

        return $opt_positions;
    }

    private function registerData()
    {
        if (!CakeSession::read('Auth.User.master_key')) {
            $stats = array(
                'user_id' => CakeSession::read('Auth.User.id'),
                'user_name' => CakeSession::read('Auth.User.full_name'),
                'trading_group_id' => !is_null(CakeSession::read('Auth.User.trading_group')) ? CakeSession::read('Auth.User.trading_group') : null,
                'network_id' => !is_null(CakeSession::read('Auth.User.trading_group')) ? null : CakeSession::read('Auth.User.current_network'),
                'garage_id' => CakeSession::read('Auth.User.garage_id'),
                'distributor_id' => CakeSession::read('Auth.User.distributor_id'),
                'section_id' => null,
                'section_name' => null,
                'article_id' => null,
                'article_name' => null,
                'date' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'device' => CakeSession::read('Auth.User.device'),
                'ip' => $_SERVER['REMOTE_ADDR']
            );

            $this->UserStatistic->create();
            $this->UserStatistic->save($stats);
        }
    }

    /**
     * Privacy notice.
     */
    public function privacy_notice()
    {
        $code = Configure::read('privacy_notice.defaultCode');

        $this->set(array(
            'cdn_script' => Configure::read('privacy_notice.url') . (Configure::read('ENVIRONMENT_PRO') ? '' : Configure::read('privacy_notice.draftUrlSegment')) . $code . '.json',
            'privacy_notice_code' => 'otnotice-' . $code,
            'settings' => Configure::read('ENVIRONMENT_PRO') ? PRIVACY_NOTICE_SETTINGS_CODE : ''
        ));

        $this->layout = 'default_login';
    }

    /**
     * Checks if user has any repairmaintenance garages associated.
     * If so, user is sent to Repair via API.
     */
    private function check_send_user_to_repair($user_bd, $contact, $error_rm)
    {
        // User must be a garage manager role and must not have been sent to Repair
        if ($user_bd['User']['role_id'] == ConstantsRoles::GARAGE && $user_bd['User']['repairmaintenance'] != ConstantsBooleans::YES) {
            $user_temp = $user_bd;
            $user_temp['User']['email'] = $contact['Contact']['email'];

            $this->Garage = ClassRegistry::init('Garage');
            $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
            $garage = $this->Garage->findById($user_bd['User']['garage_id']);

            if (isset($garage['Garage']) && $garage['Garage']['repairmaintenance']) {
                $garage_networks = $this->GarageNetwork->findAllByGarageIdAndStatus($user_bd['User']['garage_id'], ConstantsNetworksStatus::LIVE);
                $user_temp['redes'] = $garage_networks;
                $this->ApiRm = ClassRegistry::init("ApiRm.ApiRm");
                $response = $this->ApiRm->send_user_to_gnm($user_temp);
                // After API call, if success then we set ['User']['repairmaintenance'] to 1.
                if (isset($response) && $response->success) {
                    $this->User->setRepairMaintenance($user_bd['User']['id']);
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
}
