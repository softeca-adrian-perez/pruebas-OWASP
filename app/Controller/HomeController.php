<?php
set_time_limit(60 * 60 * 60);

class HomeController extends AppController
{
    public $uses = array(
        'Alert',
        'Appointment',
        'AppointmentStatus',
        'Distributor',
        'Communication',
        'CommunicationFile',
        'CommunicationSection',
        'Garage',
        'GarageNetwork',
        'Network',
        'Shortcut',
        'ShortcutType',
        'ShortcutTradingGroup',
        'SectionSubsection',
        'TradingGroup',
        'TradingGroupNetwork',
        'GarageCustomerActivity',
        'DistributorDistributorNetwork',
        'DistributorCustomerActivity',
        'Panel.PanelWidget',
        'Panel.GeneralManagerWidgetsRole',
        'AagRegion',
        'Country',
        'User'
    );

    /**
     * Home page.
     */
    public function home()
    {
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            $alerts = $this->Alert->findTaskByUserIdAndRead(CakeSession::read('Auth.User.id'), ConstantsBooleans::NO);
            $alert_types = $this->Alert->AlertType->search_list();
            $status_list = $this->AppointmentStatus->search_list();

            $this->setVarDate();

            $this->set(array(
                'alerts' => $alerts,
                'alert_types' => $alert_types,
                'status_list' => $status_list,
                'active_page' => ConstantsActiveHomePage::PAGE_1,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Home dashboard. Only for SuperAdmin.
     */
    public function home_dashboard()
    {
        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
            $user = CakeSession::read('Auth.User');
            $current_network_id = CakeSession::read('Auth.User.current_network');
            $current_network = $this->Network->find(
                'first',
                array(
                    'conditions' => array(
                        'Network.id =' => $current_network_id
                    )
                )
            );

            $this->set(array(
                'current_network' => $current_network,
                'user' => $user,
                'active_page' => ConstantsActiveHomePage::PAGE_1,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Articles home page.
     */
    public function home_page2()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS)
        ) {
            $aag_region_id = CakeSession::read('Auth.User.aag_region_id');
            $communication_sections = $this->CommunicationSection->search_list_region($aag_region_id);
            $categories = $this->CommunicationSection->search_categories_datas($aag_region_id);
            $categories_communications = array();

            foreach ($categories as $category) {
                $categories_communications[$category['CommunicationSection']['id']]['Id'] = $category['CommunicationSection']['id'];
                $categories_communications[$category['CommunicationSection']['id']]['Section'] = $category['CommunicationSection']['name_' . __l()];
                $categories_communications[$category['CommunicationSection']['id']]['SectionImage'] = $category['CommunicationSection']['image'];
                $categories_communications[$category['CommunicationSection']['id']]['Scrolling'] = $category['CommunicationSection']['scrolling'];
                if (CakeSession::read('Auth.User.garage_id')) {
                    $networks = $this->GarageNetwork->findNetworksByGarage(CakeSession::read('Auth.User.garage_id'));
                    $activities = $this->GarageCustomerActivity->getActivitiesByGarage(CakeSession::read('Auth.User.garage_id'));
                    if ($networks) {
                        if ($activities) {
                            $categories_communications[$category['CommunicationSection']['id']]['Communications'] = $this->CommunicationSection->search_communications_by_section($category['CommunicationSection']['id'], $networks, null, $activities, CakeSession::read('Auth.User'));
                        } elseif (!$activities) {
                            $categories_communications[$category['CommunicationSection']['id']]['Communications'] = $this->CommunicationSection->search_communications_by_section($category['CommunicationSection']['id'], $networks, null, null, CakeSession::read('Auth.User'));
                        }
                    } else {
                        if ($activities) {
                            $categories_communications[$category['CommunicationSection']['id']]['Communications'] = $this->CommunicationSection->search_communications_by_section($category['CommunicationSection']['id'], null, null, $activities, CakeSession::read('Auth.User'));
                        } elseif (!$activities) {
                            $categories_communications[$category['CommunicationSection']['id']]['Communications'] = $this->CommunicationSection->search_communications_by_section($category['CommunicationSection']['id'], null, null, null, CakeSession::read('Auth.User'));
                        }
                    }
                } elseif (CakeSession::read('Auth.User.distributor_id')) {
                    $networks = $this->DistributorDistributorNetwork->findNetworksByDistributor(CakeSession::read('Auth.User.distributor_id'));
                    $activities = $this->DistributorCustomerActivity->getActivitiesByDistributor(CakeSession::read('Auth.User.distributor_id'));

                    if ($networks) {
                        if ($activities) {
                            $categories_communications[$category['CommunicationSection']['id']]['Communications'] = $this->CommunicationSection->search_communications_by_section($category['CommunicationSection']['id'], null, $networks, $activities, CakeSession::read('Auth.User'));
                        } elseif (!$activities) {
                            $categories_communications[$category['CommunicationSection']['id']]['Communications'] = $this->CommunicationSection->search_communications_by_section($category['CommunicationSection']['id'], null, $networks, null, CakeSession::read('Auth.User'));
                        }
                    } else {
                        if ($activities) {
                            $categories_communications[$category['CommunicationSection']['id']]['Communications'] = $this->CommunicationSection->search_communications_by_section($category['CommunicationSection']['id'], null, null, $activities, CakeSession::read('Auth.User'));
                        } elseif (!$activities) {
                            $categories_communications[$category['CommunicationSection']['id']]['Communications'] = $this->CommunicationSection->search_communications_by_section($category['CommunicationSection']['id'], null, null, null, CakeSession::read('Auth.User'));
                        }
                    }
                } else { // TODO - Hacer por permisos
                    $categories_communications[$category['CommunicationSection']['id']]['Communications'] = $this->CommunicationSection->search_all_communications_by_section($category['CommunicationSection']['id']);
                }
            }

            if (CakeSession::read('Auth.User.garage_id')) {
                $activities = $this->GarageCustomerActivity->getActivitiesByGarage(CakeSession::read('Auth.User.garage_id'));

                if ($activities) {
                    $last_communications = $this->Communication->getLastCommunications(CakeSession::read('Auth.User.current_network'), null, $activities, CakeSession::read('Auth.User'));
                    $pop_ups = $this->Communication->findPopUps(CakeSession::read('Auth.User.current_network'), null, $activities, CakeSession::read('Auth.User'));
                } elseif (!$activities) {
                    $last_communications = $this->Communication->getLastCommunications(CakeSession::read('Auth.User.current_network'), null, null, CakeSession::read('Auth.User'));
                    $pop_ups = $this->Communication->findPopUps(CakeSession::read('Auth.User.current_network'), null, null, CakeSession::read('Auth.User'));
                }
            } elseif (CakeSession::read('Auth.User.distributor_id')) {
                $networks = $this->DistributorDistributorNetwork->findNetworksByDistributor(CakeSession::read('Auth.User.distributor_id'));
                $activities = $this->DistributorCustomerActivity->getActivitiesByDistributor(CakeSession::read('Auth.User.distributor_id'));

                if ($activities) {
                    $last_communications = $this->Communication->getLastCommunications(null, $networks, $activities, CakeSession::read('Auth.User'));
                    $pop_ups = $this->Communication->findPopUps(null, $networks, $activities, CakeSession::read('Auth.User'));
                } elseif (!$activities) {
                    $last_communications = $this->Communication->getLastCommunications(null, $networks, null, CakeSession::read('Auth.User'));
                    $pop_ups = $this->Communication->findPopUps(null, $networks, null, CakeSession::read('Auth.User'));
                }
            } else { // TODO - Hacer por permisos
                $last_communications = $this->Communication->getLastCommunicationsAdminAagRegion($aag_region_id);
                $pop_ups = array();
            }

            $this->setVarDate();
            $this->set(array(
                'active_page' => ConstantsActiveHomePage::PAGE_2,
                'categories_communications' => $categories_communications ?? array(),
                'last_communications' => $last_communications,
                'communication_sections' => $communication_sections,
                'pop_ups' => $pop_ups
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Pop ups.
     */
    public function pop_ups()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS)
        ) {
            $aag_region_id = CakeSession::read('Auth.User.aag_region_id');
            $communication_sections = $this->CommunicationSection->search_list_region($aag_region_id);
            $sections_subsections = $this->SectionSubsection->search_list_region($aag_region_id);

            $pop_ups = array();

            if (CakeSession::read('Auth.User.garage_id')) {
                $activities = $this->GarageCustomerActivity->getActivitiesByGarage(CakeSession::read('Auth.User.garage_id'));

                if ($activities) {
                    $pop_ups = $this->Communication->findPopUps(CakeSession::read('Auth.User.current_network'), null, $activities, CakeSession::read('Auth.User'));
                } elseif (!$activities) {
                    $pop_ups = $this->Communication->findPopUps(CakeSession::read('Auth.User.current_network'), null, null, CakeSession::read('Auth.User'));
                }
            } elseif (CakeSession::read('Auth.User.distributor_id')) {
                $networks = $this->DistributorDistributorNetwork->findNetworksByDistributor(CakeSession::read('Auth.User.distributor_id'));
                $activities = $this->DistributorCustomerActivity->getActivitiesByDistributor(CakeSession::read('Auth.User.distributor_id'));

                if ($activities) {
                    $pop_ups = $this->Communication->findPopUps(null, $networks, $activities, CakeSession::read('Auth.User'));
                } elseif (!$activities) {
                    $pop_ups = $this->Communication->findPopUps(null, $networks, null, CakeSession::read('Auth.User'));
                }
            } else { // TODO - Hacer por permisos
                $pop_ups = array();
            }

            $this->set(
                array(
                    'pop_ups' => $pop_ups,
                    'communication_sections' => $communication_sections,
                    'sections_subsections' => $sections_subsections,
                )
            );

            $this->layout = false;
            $this->render('../Home/pop_ups');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Software home page.
     */
    public function home_page3()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_SHORTCUTS)
        ) {
            $shortcut_types = $this->ShortcutType->find('all');
            $aag_region_id = CakeSession::read('Auth.User.aag_region_id');
            if (CakeSession::read('Auth.User.garage_id')) {
                $networks = $this->GarageNetwork->findNetworksByGarage(CakeSession::read('Auth.User.garage_id'));
                $activities = $this->GarageCustomerActivity->getActivitiesByGarage(CakeSession::read('Auth.User.garage_id'));
                if ($networks) {
                    if ($activities) {
                        $shortcuts_position_1 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION1, CakeSession::read('Auth.User.current_network'), null, $activities, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_2 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION2, CakeSession::read('Auth.User.current_network'), null, $activities, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_3 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION3, CakeSession::read('Auth.User.current_network'), null, $activities, CakeSession::read('Auth.User'), $aag_region_id);
                        $favorites_shortcuts = $this->Shortcut->findsMyFavoritesShortcuts(CakeSession::read('Auth.User.id'), CakeSession::read('Auth.User.current_network'));
                    } elseif (!$activities) {
                        $shortcuts_position_1 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION1, CakeSession::read('Auth.User.current_network'), null, null, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_2 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION2, CakeSession::read('Auth.User.current_network'), null, null, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_3 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION3, CakeSession::read('Auth.User.current_network'), null, null, CakeSession::read('Auth.User'), $aag_region_id);
                        $favorites_shortcuts = $this->Shortcut->findsMyFavoritesShortcuts(CakeSession::read('Auth.User.id'), CakeSession::read('Auth.User.current_network'));
                    }
                } else {
                    if ($activities) {
                        $shortcuts_position_1 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION1, null, null, $activities, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_2 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION2, null, null, $activities, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_3 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION3, null, null, $activities, CakeSession::read('Auth.User'), $aag_region_id);
                        $favorites_shortcuts = $this->Shortcut->findsMyFavoritesShortcuts(CakeSession::read('Auth.User.id'), null);
                    } elseif (!$activities) {
                        $shortcuts_position_1 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION1, null, null, null, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_2 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION2, null, null, null, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_3 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION3, null, null, null, CakeSession::read('Auth.User'), $aag_region_id);
                        $favorites_shortcuts = $this->Shortcut->findsMyFavoritesShortcuts(CakeSession::read('Auth.User.id'), null);
                    }
                }
            } elseif (CakeSession::read('Auth.User.distributor_id')) {
                $networks = $this->DistributorDistributorNetwork->findNetworksByDistributor(CakeSession::read('Auth.User.distributor_id'));
                $activities = $this->DistributorCustomerActivity->getActivitiesByDistributor(CakeSession::read('Auth.User.distributor_id'));

                if ($networks) {
                    if ($activities) {
                        $shortcuts_position_1 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION1, null, $networks, $activities, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_2 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION2, null, $networks, $activities, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_3 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION3, null, $networks, $activities, CakeSession::read('Auth.User'), $aag_region_id);
                        $favorites_shortcuts = $this->Shortcut->findsMyFavoritesShortcuts(CakeSession::read('Auth.User.id'), null);
                    } elseif (!$activities) {
                        $shortcuts_position_1 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION1, null, $networks, null, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_2 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION2, null, $networks, null, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_3 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION3, null, $networks, null, CakeSession::read('Auth.User'), $aag_region_id);
                        $favorites_shortcuts = $this->Shortcut->findsMyFavoritesShortcuts(CakeSession::read('Auth.User.id'), null);
                    }
                } else {
                    if ($activities) {
                        $shortcuts_position_1 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION1, null, null, $activities, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_2 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION2, null, null, $activities, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_3 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION3, null, null, $activities, CakeSession::read('Auth.User'), $aag_region_id);
                        $favorites_shortcuts = $this->Shortcut->findsMyFavoritesShortcuts(CakeSession::read('Auth.User.id'), null);
                    } elseif (!$activities) {
                        $shortcuts_position_1 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION1, null, null, null, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_2 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION2, null, null, null, CakeSession::read('Auth.User'), $aag_region_id);
                        $shortcuts_position_3 = $this->Shortcut->getShortcutsByPosition(ConstantsShortcutPosition::POSITION3, null, null, null, CakeSession::read('Auth.User'), $aag_region_id);
                        $favorites_shortcuts = $this->Shortcut->findsMyFavoritesShortcuts(CakeSession::read('Auth.User.id'), null);
                    }
                }
            } else { // TODO - Hacer por permisos
                $shortcuts_position_1 = $this->Shortcut->getAllByShortcutTypeId(ConstantsShortcutPosition::POSITION1, $aag_region_id);
                $shortcuts_position_2 = $this->Shortcut->getAllByShortcutTypeId(ConstantsShortcutPosition::POSITION2, $aag_region_id);
                $shortcuts_position_3 = $this->Shortcut->getAllByShortcutTypeId(ConstantsShortcutPosition::POSITION3, $aag_region_id);
                $favorites_shortcuts = array();
            }

            $this->setVarDate();
            $this->setPreview();

            $this->set(array(
                'active_page' => ConstantsActiveHomePage::PAGE_3,
                'shortcut_types' => $shortcut_types,
                'favorites_shortcuts' => $favorites_shortcuts,
                'shortcuts_position_1' => $shortcuts_position_1,
                'shortcuts_position_2' => $shortcuts_position_2,
                'shortcuts_position_3' => $shortcuts_position_3
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Statistics.
     */
    public function widget_general()
    {
        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
            $user = $this->Acceso->user();
            $networks_availables = $this->Network->find('all');
            $trading_groups = $this->TradingGroup->find('list');

            $current_network_id = CakeSession::read('Auth.User.current_network');
            $current_network = $this->Network->find(
                'first',
                array(
                    'conditions' => array(
                        'Network.id =' => $current_network_id
                    )
                )
            );

            $allowed_widgets = $this->PanelWidget->getWidgetsRole($user['Role']['id']);
            $displayed_widgets = $this->PanelWidget->getWidgetsRole($user['Role']['id']);
            $hidden_widgets = $this->PanelWidget->getWidgetsAvailable($displayed_widgets, $user['Role']['id']);

            if (!$displayed_widgets) {  // We don't current distinguish between default status (no per-user settings) and users that intentionally remove all widgets
                $displayed_widgets = $allowed_widgets;
            }

            $title_widgets = array();
            foreach ($allowed_widgets as $widget) {
                $title_widgets[$widget['PanelWidget']['id']] = $widget['PanelWidget']['display_name'];
            }

            $conditions = $this->request->query;
            $this->request->data['Search'] = $conditions;

            $networks = $this->Network->find('all');
            $networks_total_garages_by_network = $this->Network->get_networks_number_garages($conditions);
            $networks_with_type = $this->Network->get_networks_with_type($user);

            foreach ($networks_total_garages_by_network as &$network) {
                $network_id = $network['Network']['id'];
                $network['garages_statistics'] = $this->Garage->get_widget_fields_statistics($network_id, $conditions);
                $network['garages_number_services'] = $this->Garage->get_widget_statics_services($network_id, $conditions);
                $network['garages_number_vehicles'] = $this->Garage->get_widget_statics_vehicles($network_id, $conditions);
            }

            $regions = $this->AagRegion->region_list();
            $countries = $this->Country->get_list();
            $network_list = $this->Network->find('list');

            $current_country =  CakeSession::read('Auth.User.current_country');
            $current_region = CakeSession::read('Auth.User.current_region');

            $this->set(array(
                'networks_availables' => $networks_availables,
                'networks' => $networks,
                'networks_with_type' => $networks_with_type,
                'networks_total_garages_by_network' => $networks_total_garages_by_network,
                'widgets' => $displayed_widgets,
                'title_widgets' => $title_widgets,
                'widgets_available' => $hidden_widgets,
                'current_network' => $current_network,
                'trading_groups' => $trading_groups,
                'regions' => $regions,
                'countries' => $countries,
                'network_list' => $network_list,
                'current_country' => $current_country,
                'current_region' => $current_region,
                'active_page' => ConstantsActiveHomePage::PAGE_6,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX load modal communication.
     */
    public function ajax_modal_communication($communication_id)
    {
        $this->verify_ajax($this->request);

        $aag_region_id = CakeSession::read('Auth.User.aag_region_id');
        $communication = $this->Communication->getCommunicationByIdAndAagRegionId($communication_id, $aag_region_id);

        if (
            $communication &&
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS)
        ) {
            $communication_files = $this->CommunicationFile->getListByCommunicationId($communication_id);
            $communication_sections = $this->CommunicationSection->search_list_region($aag_region_id);
            $sections_subsections = $this->SectionSubsection->search_list_region($aag_region_id);

            $this->set(
                array(
                    'communication' => $communication,
                    'communication_files' => $communication_files,
                    'communication_sections' => $communication_sections,
                    'sections_subsections' => $sections_subsections,
                )
            );

            $this->layout = false;
            $this->render('../Home/Elements/modal_communication');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarDate()
    {
        $date = getdate();
        $day_week = array(
            '1' => __t('Garage.Monday'),
            '2' => __t('Garage.Tuesday'),
            '3' => __t('Garage.Wednesday'),
            '4' => __t('Garage.Thursday'),
            '5' => __t('Garage.Friday'),
            '6' => __t('Garage.Saturday'),
            '7' => __t('Garage.Sunday'),
        );
        $date = $date['mday'] . '/' . $date['mon'] . '/' . $date['year'];

        $this->set(array(
            'date' => $date,
            'day_week' => $day_week[date('N')],
        ));
    }

    /**
     * AJAX get events list for home calendar.
     */
    public function ajax_events_list($user_assigned_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->User->findById($user_assigned_id);

        if ($user && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            $start = $this->request->query['start'];
            $end = $this->request->query['end'];
            $events = $this->Appointment->getHomeCalendarEvents($start, $end, $user_assigned_id);
            echo json_encode($events);
            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get garage events list for home calendar.
     */
    public function ajax_events_list_garage($garage_id)
    {
        $this->verify_ajax($this->request);

        $garage = $this->Garage->findById($garage_id);

        if ($garage && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            $start = $this->request->query['start'];
            $end = $this->request->query['end'];
            $events = $this->Appointment->getHomeCalendarEventsGarage($start, $end, $garage_id);
            echo json_encode($events);
            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get distributor events list for home calendar.
     */
    public function ajax_events_list_distributor($distributor_id)
    {
        $this->verify_ajax($this->request);

        $distributor = $this->Distributor->findById($distributor_id);

        if ($distributor && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            $start = $this->request->query['start'];
            $end = $this->request->query['end'];
            $events = $this->Appointment->getHomeCalendarEventsDistributor($start, $end, $distributor_id);
            echo json_encode($events);
            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get info for software home page.
     */
    public function ajax_preview_software()
    {
        $this->verify_ajax($this->request);

        if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_SHORTCUTS)) {
            $permissions = true;
            $shortcut_types = $this->ShortcutType->find('all');

            if ($this->request->data['network_id'] != null) {
                $shortcuts_position_1 = $this->Shortcut->findsGaragesShortcutsPreview(ConstantsShortcutPosition::POSITION1, $this->request->data['network_id']);
                $shortcuts_position_2 = $this->Shortcut->findsGaragesShortcutsPreview(ConstantsShortcutPosition::POSITION2, $this->request->data['network_id']);
                $shortcuts_position_3 = $this->Shortcut->findsGaragesShortcutsPreview(ConstantsShortcutPosition::POSITION3, $this->request->data['network_id']);
                $favorites_shortcuts = array();
            } elseif ($this->request->data['trading_group_id'] != null) {
                $shortcuts_position_1 = $this->Shortcut->findsDistributorsShortcutsPreview(ConstantsShortcutPosition::POSITION1, $this->request->data['trading_group_id']);
                $shortcuts_position_2 = $this->Shortcut->findsDistributorsShortcutsPreview(ConstantsShortcutPosition::POSITION2, $this->request->data['trading_group_id']);
                $shortcuts_position_3 = $this->Shortcut->findsDistributorsShortcutsPreview(ConstantsShortcutPosition::POSITION3, $this->request->data['trading_group_id']);
                $favorites_shortcuts = array();
            } else {
                $networks = $this->Network->find('all');
                $shortcuts_position_1 = $this->Shortcut->findAllByShortcutTypeId(ConstantsShortcutPosition::POSITION1);
                $shortcuts_position_2 = $this->Shortcut->findAllByShortcutTypeId(ConstantsShortcutPosition::POSITION2);
                $shortcuts_position_3 = $this->Shortcut->findAllByShortcutTypeId(ConstantsShortcutPosition::POSITION3);
                $favorites_shortcuts = $this->Shortcut->findsFavoritesShortcuts(CakeSession::read('Auth.User.id'), Hash::extract($networks, '{n}.Network.id'));
                if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::ADMIN) {
                    $permissions = false;
                }
            }

            $this->setVarDate();
            $this->set(array(
                'active_page' => ConstantsActiveHomePage::PAGE_3,
                'shortcut_types' => $shortcut_types,
                'favorites_shortcuts' => $favorites_shortcuts,
                'shortcuts_position_1' => $shortcuts_position_1,
                'shortcuts_position_2' => $shortcuts_position_2,
                'shortcuts_position_3' => $shortcuts_position_3,
                'permissions' => $permissions
            ));

            $this->layout = false;
            $this->render('../Home/Elements/software');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setPreview()
    {
        $user = $this->Acceso->user();
        $aag_region_id = $user['aag_region_id'];
        if (in_array(ConstantsPermissionsGrouping::SUPPLIERS, $this->Session->read('Auth.User.Permissions'))) {
            $value_permission = true;
            $networks = $this->Network->findAllByAagRegionId($aag_region_id);
            $trading_groups = $this->TradingGroup->getTradingGroupIndependentWithOutPermissions($aag_region_id);
            $this->set(array(
                'networks' => $networks,
                'trading_groups' => $trading_groups,
                'value_permission' => $value_permission
            ));
        } else {
            $value_permission = false;
            $this->set(array(
                'value_permission' => $value_permission
            ));
        }
    }

    /**
     * Privacy notice.
     */
    public function privacy_notice()
    {
        $user = $this->Acceso->user();

        $code = Configure::read('privacy_notice.defaultCode');
        $regionCode = Configure::read('privacy_notice.regionsCodes.' . $user['aag_region_id']);
        if (isset($regionCode) && !empty($regionCode)) {
            $code = $regionCode;
        }

        $this->set(array(
            'cdn_script' => Configure::read('privacy_notice.url') . (Configure::read('ENVIRONMENT_PRO') ? '' : Configure::read('privacy_notice.draftUrlSegment')) . $code . '.json',
            'privacy_notice_code' => 'otnotice-' . $code,
            'settings' => Configure::read('ENVIRONMENT_PRO') ? PRIVACY_NOTICE_SETTINGS_CODE : ''
        ));
        $this->render('/Elements/Comun/privacy_notice');
    }
}
