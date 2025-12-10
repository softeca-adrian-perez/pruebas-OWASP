<?php
class StatisticsController extends AppController
{
    public $uses = array(
        'UserStatistic',
        'Appointment',
        'User',
        'Contact',
        'Position',
        'Network',
        'TradingGroup',
        'CommunicationSection',
        'Communication',
        'Garage',
        'GarageNetwork',
        'Distributor',
        'DistributorNetwork',
        'DistributorDistributorNetwork',
        'DebriefTopic',
    );

    /**
     * Statistics home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $networks = null;
            $tradingGroups = null;

            $networks = $this->Network->find(
                'list',
                array(
                    'conditions' => array(
                        'aag_region_id' => $aagRegionId
                    )
                )
            );
            $tradingGroups = $this->TradingGroup->find(
                'list',
                array(
                    'conditions' => array(
                        'aag_region_id' => $aagRegionId
                    )
                )
            );


            $config = CakeSession::read('Auth.User.Config');

            $trading_group_id = null;
            $network_id = null;

            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d') . " -1 month"));
            $dateTo = date('Y-m-d');
            $conditions = array(
                'UserStatistic.date >=' => $dateFrom,
                'UserStatistic.date <=' => $dateTo,
                'User.aag_region_id' => $aagRegionId,
            );

            if (!empty($this->request->query)) {
                if (!empty($this->request->query['from'])) {
                    $dateFrom = Fecha::toFormatoBD($this->request->query['from']);
                }
                if (!empty($this->request->query['to'])) {
                    $dateTo = Fecha::toFormatoBD($this->request->query['to']);
                }
                if (!empty($this->request->query['trading_group_id'])) {
                    $trading_group_id = $this->request->query['trading_group_id'];
                    $conditions[] = array(
                        'UserStatistic.trading_group_id' => $trading_group_id,
                    );
                } elseif (!empty($this->request->query['network_id'])) {
                    $network_id = $this->request->query['network_id'];
                    $conditions[] = array(
                        'UserStatistic.network_id' => $network_id,
                    );
                }
            }

            $months = $this->months($dateFrom, $dateTo);

            $dateFrom = Fecha::toFormatoVista($dateFrom);
            $dateTo = Fecha::toFormatoVista($dateTo);
            $this->request->query['from'] = $dateFrom;
            $this->request->query['to'] = $dateTo;

            $this->request->data['Search'] = $this->request->query;
            if ($config[ConstantsConfig::STATISTICS_IP]) {
                $users_connections = $this->UserStatistic->searchByIp($conditions);
            } else {
                $users_connections = $this->UserStatistic->searchByUser($conditions);
            }

            foreach ($users_connections as $key => $user_connection) {
                $users_connections[$key]['most_recent'] = $user_connection[0]['most_recent'];
                $users_connections[$key]['total'] = 0;
                $date_from_tmp = date("d-m-Y", strtotime("-1 month", strtotime($dateFrom)));
                $counter = 0;
                foreach ($months as $key_month => $month) {
                    $counter++;
                    $date_from_tmp = date('d-m-Y', strtotime('01-' . date("m-Y", strtotime("+1 month", strtotime($date_from_tmp)))));
                    if ($counter == count($months)) {
                        $date_to_tmp = $dateTo;
                    } else {
                        $date_to_tmp = date("t-m-Y", strtotime($date_from_tmp));
                    }
                    if ($config[ConstantsConfig::STATISTICS_IP]) {
                        $results_tmp = $this->UserStatistic->getAllByIPDateNetworkAndTradingGroup($user_connection['UserStatistic']['ip'], $date_from_tmp, $date_to_tmp, $network_id, $trading_group_id, $aagRegionId);
                    } else {
                        $results_tmp = $this->UserStatistic->getAllByUserIdDateNetworkAndTradingGroup($user_connection['UserStatistic']['user_id'], $date_from_tmp, $date_to_tmp, $network_id, $trading_group_id, $aagRegionId);
                    }
                    $users_connections[$key][$key_month] = $results_tmp;
                    $users_connections[$key]['total'] += $results_tmp;
                }

                if ($user_connection['UserStatistic']['garage_id']) {
                    $garage = $this->Garage->findById($user_connection['UserStatistic']['garage_id']);
                    $users_connections[$key]['customer_name'] = $garage['Garage']['name'];
                    $garage_networks = $this->GarageNetwork->findActiveNetworksByGarage($user_connection['UserStatistic']['garage_id']);
                    $networks_tmp = $this->Network->findAllById(Hash::extract($garage_networks, '{n}'));
                    $users_connections[$key]['access'] = implode(", ", Hash::extract($networks_tmp, '{n}.Network.name'));
                } elseif ($user_connection['UserStatistic']['distributor_id']) {
                    $distributor = $this->Distributor->findById($user_connection['UserStatistic']['distributor_id']);
                    $users_connections[$key]['customer_name'] = $distributor['Distributor']['name'];
                    $trading_group = $this->TradingGroup->findById($distributor['Distributor']['trading_group_id']);
                    $users_connections[$key]['access'] = $trading_group['TradingGroup']['name'];
                } else {
                    $users_connections[$key]['customer_name'] = '';
                    $users_connections[$key]['access'] = '';
                }
            }

            $users_statistics_sections = array();
            $users_statistics_sections_names = array();
            $users_statistics_articles = array();
            $users_statistics_articles_names = array();

            //Pie Chart Sections
            $users_statistics_sections_tmp = $this->UserStatistic->getPieChartSections($trading_group_id, $network_id, $dateFrom, $dateTo, $aagRegionId);
            foreach ($users_statistics_sections_tmp as $statistic_section) {
                if ($statistic_section['UserStatistic']['section_id'] != null) {
                    $users_statistics_sections[] = $this->UserStatistic->countPieChartSections($statistic_section['UserStatistic']['section_id'], $trading_group_id, $network_id, $dateFrom, $dateTo, $aagRegionId);
                    $section_tmp = $this->CommunicationSection->findById($statistic_section['UserStatistic']['section_id']);
                    if ($section_tmp) {
                        $users_statistics_sections_names[] = $section_tmp['CommunicationSection']['name' . __s()];
                    } else {
                        $users_statistics_sections_names[] = array($statistic_section['UserStatistic']['section_name']);
                    }
                }
            }

            //Pie Chart Articles
            $users_statistics_articles_tmp = $this->UserStatistic->getPieChartArticles($trading_group_id, $network_id, $dateFrom, $dateTo, $aagRegionId);
            foreach ($users_statistics_articles_tmp as $statistic_article) {
                if ($statistic_article['UserStatistic']['article_id'] != null) {
                    $users_statistics_articles[] = $this->UserStatistic->countPieChartArticles($statistic_article['UserStatistic']['article_id'], $trading_group_id, $network_id, $dateFrom, $dateTo, $aagRegionId);
                    $article_tmp = $this->Communication->findById($statistic_article['UserStatistic']['article_id']);
                    if ($article_tmp) {
                        $users_statistics_articles_names[] = $article_tmp['Communication']['title'];
                    } else {
                        $users_statistics_articles_names[] = array($statistic_article['UserStatistic']['article_name']);
                    }
                }
            }

            //Chart Bar Devices
            $device_connections = array(
                ConstantsDevices::DESKTOP => 0,
                ConstantsDevices::MOBILE => 0,
                ConstantsDevices::TABLET => 0
            );
            $desktop_connections = array();
            $mobile_connections = array();
            $tablet_connections = array();
            $date_from_tmp = date("d-m-Y", strtotime("-1 month", strtotime($dateFrom)));
            $counter = 0;
            foreach ($months as $key_month => $month) {
                $counter++;
                $date_from_tmp = date('d-m-Y', strtotime('01-' . date("m-Y", strtotime("+1 month", strtotime($date_from_tmp)))));
                if ($counter == count($months)) {
                    $date_to_tmp = $dateTo;
                } else {
                    $date_to_tmp = date("t-m-Y", strtotime($date_from_tmp));
                }
                $desktop_connections[$key_month] = 0;
                $mobile_connections[$key_month] = 0;
                $tablet_connections[$key_month] = 0;
                $current_month = $key_month;
                $current_month++;
                $results_tmp = $this->UserStatistic->getAllByDateNetworkAndTradingGroup($date_from_tmp, $date_to_tmp, $network_id, $trading_group_id, $aagRegionId);
                foreach ($results_tmp as $result) {
                    if ($result['UserStatistic']['device'] == ConstantsDevices::DESKTOP) {
                        $desktop_connections[$key_month]++;
                        $device_connections[ConstantsDevices::DESKTOP]++;
                    } elseif ($result['UserStatistic']['device'] == ConstantsDevices::MOBILE) {
                        $mobile_connections[$key_month]++;
                        $device_connections[ConstantsDevices::MOBILE]++;
                    } elseif ($result['UserStatistic']['device'] == ConstantsDevices::TABLET) {
                        $tablet_connections[$key_month]++;
                        $device_connections[ConstantsDevices::TABLET]++;
                    }
                }

                $statistics_devices[$key_month] = array(
                    ConstantsDevices::DESKTOP => $desktop_connections[$key_month],
                    ConstantsDevices::MOBILE => $mobile_connections[$key_month],
                    ConstantsDevices::TABLET => $tablet_connections[$key_month],
                    'month' => $month
                );
            }

            //Bdms
            $bdms_tmp = $this->User->getBDMUsers();
            foreach ($bdms_tmp as $key => $bdm) {
                $garage_visits = null;
                $distributor_visits = null;
                $garage_visits = $this->Appointment->getGarageVisitByUser($key, Fecha::toFormatoBd($dateFrom), Fecha::toFormatoBd($dateTo));
                $distributor_visits = $this->Appointment->getDistributorVisitByUser($key, Fecha::toFormatoBd($dateFrom), Fecha::toFormatoBd($dateTo));
                $events_visits = $this->Appointment->getEventsVisitByUser($key, Fecha::toFormatoBd($dateFrom), Fecha::toFormatoBd($dateTo));
                $bdms[$key]['name'] = $bdm;
                $bdms[$key]['user_id'] = $key;
                $bdms[$key]['GarageVisits'] = count($garage_visits);
                $bdms[$key]['DistributorVisits'] = count($distributor_visits);
                $bdms[$key]['EventVisits'] = count($events_visits);

                $garage_duration = 0;
                if ($garage_visits) {
                    foreach ($garage_visits as $garage_visit) {
                        $fecha1 = strtotime($garage_visit['Appointment']['date']) + strtotime($garage_visit['Appointment']['start_time']); //fecha inicial
                        $fecha2 = strtotime($garage_visit['Appointment']['end_date']) + strtotime($garage_visit['Appointment']['end_time']); //fecha de cierre
                        $intervalo = ($fecha2 - $fecha1);
                        $garage_duration = $garage_duration + $intervalo;
                    }
                }

                $bdms[$key]['DurationGarageVisits'] = $garage_duration;

                $distributor_duration = 0;
                if ($distributor_visits) {
                    foreach ($distributor_visits as $distributor_visit) {
                        $fecha1 = strtotime($distributor_visit['Appointment']['date']) + strtotime($distributor_visit['Appointment']['start_time']); //fecha inicial
                        $fecha2 = strtotime($distributor_visit['Appointment']['end_date']) + strtotime($distributor_visit['Appointment']['end_time']); //fecha de cierre
                        $intervalo = ($fecha2 - $fecha1);
                        $distributor_duration = $distributor_duration + $intervalo;
                    }
                }
                $bdms[$key]['DurationDistributorVisits'] = $distributor_duration;
                $bdms[$key]['AverageDurationGarageVisits'] = $garage_duration ? $garage_duration / count($garage_visits) : 0;
                $bdms[$key]['AverageDurationDistributorVisits'] = $distributor_duration ? $distributor_duration / count($distributor_visits) : 0;
            }

            $contacts = $this->Contact->getBDMContact($aagRegionId);

            $this->set(
                array(
                    'contacts' => $contacts,
                    'garages' => null,
                    'distributors' => null,
                    'networks' => $networks,
                    'trading_groups' => $tradingGroups,
                    'months' => $months,
                    'users_connections' => $users_connections,
                    'users_statistics_sections' => $users_statistics_sections,
                    'users_statistics_sections_names' => $users_statistics_sections_names,
                    'users_statistics_articles' => $users_statistics_articles,
                    'users_statistics_articles_names' => $users_statistics_articles_names,
                    'device_connections' => json_encode(array_values($device_connections)),
                    'statistics_devices' => $statistics_devices,
                    'bdms' => $bdms,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'network_id' => isset($this->request->query['network_id']) ? $this->request->query['network_id'] : '',
                    'trading_group_id' => isset($this->request->query['trading_group_id']) ? $this->request->query['trading_group_id'] : '',
                    'user_aag_region_id' => CakeSession::read('Auth.User.aag_region_id'),
                    'user_role' => CakeSession::read('Auth.User.role_id')
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Statistics BDM.
     */
    public function bdm()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
            )
        ) {
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
            $status_event_exists = false;

            if ($this->request->data['Search']['appointment_status_id']) {
                foreach ($this->request->data['Search']['appointment_status_id'] as $array_appointment_status) {
                    if ($array_appointment_status == ConstantsStatusAppointments::EVENT) {
                        $status_event_exists = true;
                    }
                }
            }

            $type = 'all';
            if (isset($this->request->data['Search']['garage'])) {
                $type = 'garage';
                $conditions[] = array('Appointment.garage_id !=' => null);
            }
            if (isset($this->request->data['Search']['distributor'])) {
                $type = 'distributor';
                $conditions[] = array('Appointment.distributor_id !=' => null);
            }
            if ($status_event_exists) {
                $type = 'event';
                $conditions[] = array('Appointment.appointment_status_id =' => ConstantsStatusAppointments::EVENT, 'Appointment.appointment_type_id =' => ConstantsTypesAppointments::PROSPECT_GARAGE_VISIT);
            }

            $appointments = $this->custom_pagination(
                $this->Appointment->_query('search_agenda_list'),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                $this->Appointment
            );

            foreach ($appointments as $key => $appointment) {
                $appointments[$key] = $this->Appointment->getAppointmentDataByAppointmentId($appointment['Appointment']['id']);
            }

            $this->setUsers();
            $this->set(
                array(
                    'type' => $type,
                    'appointments' => $appointments,
                    'appointment_status' => $appointmentStatus,
                    'appointment_types' => $appointmentTypes,
                    'appointment_fill_up' => $appointmentFillUp,
                    'feelings' => $feelings,
                    'feelings_list' => $feelingsList,
                    'feelings_list_img' => $feelingsListImg,
                    'feelings_colors' => $feelingsColors,
                    'status' => $status,
                    'user_id' => $search['user_assigned_id'],
                    'from' => $search['from'],
                    'to' => $search['to'],
                    'search' => $search
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Export connections in Excel. Not AJAX request.
     */
    public function ajax_connections_excel()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $config = CakeSession::read('Auth.User.Config');
            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d') . " -1 month"));
            $dateTo = date('Y-m-d');
            $trading_group_id = null;
            $network_id = null;
            $conditions = array(
                'UserStatistic.date >=' => $dateFrom,
                'UserStatistic.date <=' => $dateTo,
                'User.aag_region_id' => $aagRegionId,
            );
            if (!empty($this->request->query)) {
                if (!empty($this->request->query['from'])) {
                    $dateFrom = Fecha::toFormatoBD($this->request->query['from']);
                }
                if (!empty($this->request->query['to'])) {
                    $dateTo = Fecha::toFormatoBD($this->request->query['to']);
                }
                if (!empty($this->request->query['trading_group_id'])) {
                    $trading_group_id = $this->request->query['trading_group_id'];
                    $conditions[] = array(
                        'UserStatistic.trading_group_id' => $trading_group_id,
                    );
                } elseif (!empty($this->request->query['network_id'])) {
                    $network_id = $this->request->query['network_id'];
                    $conditions[] = array(
                        'UserStatistic.network_id' => $network_id,
                    );
                }
            }

            $months = $this->months($dateFrom, $dateTo);

            $dateFrom = Fecha::toFormatoVista($dateFrom);
            $dateTo = Fecha::toFormatoVista($dateTo);
            $this->request->query['from'] = $dateFrom;
            $this->request->query['to'] = $dateTo;

            $this->request->data['Search'] = $this->request->query;
            if ($config[ConstantsConfig::STATISTICS_IP]) {
                $users_connections = $this->UserStatistic->searchByIp($conditions);
            } else {
                $users_connections = $this->UserStatistic->searchByUser($conditions);
            }

            foreach ($users_connections as $key => $user_connection) {
                $users_connections[$key]['most_recent'] = Fecha::toFormatoVista($user_connection[0]['most_recent']);
                $users_connections[$key]['total'] = 0;
                $date_from_tmp = date("d-m-Y", strtotime("-1 month", strtotime($dateFrom)));
                $counter = 0;
                foreach ($months as $key_month => $month) {
                    $counter++;
                    $date_from_tmp = date('d-m-Y', strtotime('01-' . date("m-Y", strtotime("+1 month", strtotime($date_from_tmp)))));
                    if ($counter == count($months)) {
                        $date_to_tmp = $dateTo;
                    } else {
                        $date_to_tmp = date("t-m-Y", strtotime($date_from_tmp));
                    }
                    if ($config[ConstantsConfig::STATISTICS_IP]) {
                        $results_tmp = $this->UserStatistic->getAllByIPDateNetworkAndTradingGroup($user_connection['UserStatistic']['ip'], $date_from_tmp, $date_to_tmp, $network_id, $trading_group_id, $aagRegionId);
                    } else {
                        $results_tmp = $this->UserStatistic->getAllByUserIdDateNetworkAndTradingGroup($user_connection['UserStatistic']['user_id'], $date_from_tmp, $date_to_tmp, $network_id, $trading_group_id, $aagRegionId);
                    }
                    $users_connections[$key][$key_month] = $results_tmp;
                    $users_connections[$key]['total'] += $results_tmp;
                }

                if ($user_connection['UserStatistic']['garage_id']) {
                    $garage = $this->Garage->findById($user_connection['UserStatistic']['garage_id']);
                    $garage_networks = $this->GarageNetwork->findActiveNetworksByGarage($user_connection['UserStatistic']['garage_id']);
                    $networks_tmp = $this->Network->findAllById(Hash::extract($garage_networks, '{n}'));
                    $users_connections[$key]['access'] = implode(", ", Hash::extract($networks_tmp, '{n}.Network.name'));
                    $users_connections[$key]['postcode'] = $garage['Garage']['postcode'];
                    $users_connections[$key]['city'] = $garage['Garage']['town'];
                    $users_connections[$key]['client_code'] = $garage['Garage']['g_number_id'];
                    $users_connections[$key]['company_name'] = $garage['Garage']['name'];
                    $users_connections[$key]['distributors'] = '';
                    $users_connections[$key]['trading_groups'] = '';
                    $users_connections[$key]['profile'] = __t('Garage.Garage');
                    $users_connections[$key]['networks'] = implode(", ", Hash::extract($networks_tmp, '{n}.Network.name'));;
                } elseif ($user_connection['UserStatistic']['distributor_id']) {
                    $distributor = $this->Distributor->findById($user_connection['UserStatistic']['distributor_id']);
                    $trading_group = $this->TradingGroup->findById($distributor['Distributor']['trading_group_id']);
                    $distributors_distributors_networks = $this->DistributorDistributorNetwork->getNetworksByDistributor($user_connection['UserStatistic']['distributor_id']);
                    $distributors_networks_tmp = $this->DistributorNetwork->findAllById(Hash::extract($distributors_distributors_networks, '{n}'));
                    $users_connections[$key]['access'] = $trading_group['TradingGroup']['name'];
                    $users_connections[$key]['postcode'] = $distributor['Distributor']['postcode'];
                    $users_connections[$key]['city'] = $distributor['Distributor']['town'];
                    $users_connections[$key]['client_code'] = $distributor['Distributor']['account_number'];
                    $users_connections[$key]['company_name'] = $distributor['Distributor']['name'];
                    $users_connections[$key]['distributors'] = $distributor['Distributor']['name'];
                    $users_connections[$key]['trading_groups'] = $trading_group['TradingGroup']['name'];
                    $users_connections[$key]['profile'] = __t('Distributor.Distributor');
                    $users_connections[$key]['networks'] = implode(", ", Hash::extract($distributors_networks_tmp, '{n}.DistributorNetwork.name'));
                } else {
                    $users_connections[$key]['access'] = '';
                    $users_connections[$key]['postcode'] = '';
                    $users_connections[$key]['city'] = '';
                    $users_connections[$key]['client_code'] = '';
                    $users_connections[$key]['company_name'] = '';
                    $users_connections[$key]['distributors'] = '';
                    $users_connections[$key]['trading_groups'] = '';
                    $users_connections[$key]['profile'] = __t('General.Other');
                    $users_connections[$key]['networks'] = '';
                }
                $user_tmp = $this->User->findById($user_connection['UserStatistic']['user_id']);
                $users_connections[$key]['email'] = '';
                $users_connections[$key]['phone'] = '';
                $users_connections[$key]['position'] = '';
                $contact_tmp = array();
                if ($user_tmp) {
                    $contact_tmp = $this->Contact->findById($user_tmp['User']['contact_id']);
                }
                if ($contact_tmp) {
                    $position_tmp = $this->Position->findById($contact_tmp['Contact']['position_id']);
                    if ($position_tmp) {
                        $users_connections[$key]['email'] = $contact_tmp['Contact']['email'];
                        $users_connections[$key]['phone'] = $contact_tmp['Contact']['phone'];
                        $users_connections[$key]['position'] = $position_tmp['Position']['name' . __s()];
                    }
                }
            }

            $this->set(array(
                'users_connections' => $users_connections,
                'months' => $months
            ));

            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $this->render('/Statistics/Elements/export_excel_connections');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Export section in Excel. Not AJAX request.
     */
    public function ajax_sections_excel()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
            )
        ) {
            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d') . " -1 month"));
            $dateTo = date('Y-m-d');
            $trading_group_id = null;
            $network_id = null;
            $users_statistics_sections = array();
            $users_statistics_sections_names = array();

            if (!empty($this->request->query)) {
                if (!empty($this->request->query['from'])) {
                    $this->request->query['from'] = Fecha::toFormatoBD($this->request->query['from']);
                }
                if (!empty($this->request->query['to'])) {
                    $this->request->query['to'] = Fecha::toFormatoBD($this->request->query['to']);
                }
                if (!empty($this->request->query['trading_group_id'])) {
                    $trading_group_id = $this->request->query['trading_group_id'];
                } elseif (!empty($this->request->query['network_id'])) {
                    $network_id = $this->request->query['network_id'];
                }
            } else {
                $this->request->query['from'] = $dateFrom;
                $this->request->query['to'] = $dateTo;
            }
            $dateFrom = Fecha::toFormatoVista($this->request->query['from']);
            $dateTo = Fecha::toFormatoVista($this->request->query['to']);

            //Pie Chart Sections
            $users_statistics_sections_tmp = $this->UserStatistic->getPieChartSections($trading_group_id, $network_id, $dateFrom, $dateTo, $aagRegionId);

            foreach ($users_statistics_sections_tmp as $statistic_section) {
                if ($statistic_section['UserStatistic']['section_id'] != null) {
                    $users_statistics_sections[] = $this->UserStatistic->countPieChartSections($statistic_section['UserStatistic']['section_id'], $trading_group_id, $network_id, $dateFrom, $dateTo, $aagRegionId);
                    $section_tmp = $this->CommunicationSection->findById($statistic_section['UserStatistic']['section_id']);
                    if ($section_tmp) {
                        $users_statistics_sections_names[] = $section_tmp['CommunicationSection']['name' . __s()];
                    } else {
                        $users_statistics_sections_names[] = array($statistic_section['UserStatistic']['section_name']);
                    }
                }
            }

            $this->set(array(
                'users_statistics_sections' => $users_statistics_sections,
                'users_statistics_sections_names' => $users_statistics_sections_names,
            ));

            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $this->render('/Statistics/Elements/export_excel_sections');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Export articles in Excel. Not AJAX request.
     */
    public function ajax_articles_excel()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
            )
        ) {
            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d') . " -1 month"));
            $dateTo = date('Y-m-d');
            $trading_group_id = null;
            $network_id = null;
            $users_statistics_articles = array();
            $users_statistics_articles_names = array();

            if (!empty($this->request->query)) {
                if (!empty($this->request->query['from'])) {
                    $this->request->query['from'] = Fecha::toFormatoBD($this->request->query['from']);
                }
                if (!empty($this->request->query['to'])) {
                    $this->request->query['to'] = Fecha::toFormatoBD($this->request->query['to']);
                }
                if (!empty($this->request->query['trading_group_id'])) {
                    $trading_group_id = $this->request->query['trading_group_id'];
                } elseif (!empty($this->request->query['network_id'])) {
                    $network_id = $this->request->query['network_id'];
                }
            } else {
                $this->request->query['from'] = $dateFrom;
                $this->request->query['to'] = $dateTo;
            }
            $dateFrom = Fecha::toFormatoVista($this->request->query['from']);
            $dateTo = Fecha::toFormatoVista($this->request->query['to']);

            //Pie Chart Articles
            $users_statistics_articles_tmp = $this->UserStatistic->getPieChartArticles($trading_group_id, $network_id, $dateFrom, $dateTo, $aagRegionId);
            $users_statistics_articles_url = array();
            foreach ($users_statistics_articles_tmp as $statistic_article) {
                if ($statistic_article['UserStatistic']['article_id'] != null) {
                    $users_statistics_articles[] = $this->UserStatistic->countPieChartArticles($statistic_article['UserStatistic']['article_id'], $trading_group_id, $network_id, $dateFrom, $dateTo, $aagRegionId);
                    $communication_tmp = $this->Communication->findById($statistic_article['UserStatistic']['article_id']);
                    if ($communication_tmp) {
                        $users_statistics_articles_url[] = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . '/communications/home_section/' . $communication_tmp['Communication']['communication_section_id'] . '/' . $communication_tmp['Communication']['section_subsection_id'] . '/' . $statistic_article['UserStatistic']['article_id'];
                    } else {
                        $users_statistics_articles_url[] = '';
                    }

                    $article_tmp = $this->Communication->findById($statistic_article['UserStatistic']['article_id']);
                    if ($article_tmp) {
                        $users_statistics_articles_names[] = $article_tmp['Communication']['title'];
                    } else {
                        $users_statistics_articles_names[] = array($statistic_article['UserStatistic']['article_name']);
                    }
                }
            }

            $this->set(array(
                'users_statistics_articles' => $users_statistics_articles,
                'users_statistics_articles_url' => $users_statistics_articles_url,
                'users_statistics_articles_names' => $users_statistics_articles_names,
            ));

            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $this->render('/Statistics/Elements/export_excel_articles');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Export devices in Excel. Not AJAX request.
     */
    public function ajax_devices_excel()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d') . " -1 month"));
            $dateTo = date('Y-m-d');
            $trading_group_id = null;
            $network_id = null;
            $conditions = array(
                'UserStatistic.date >=' => $dateFrom,
                'UserStatistic.date <=' => $dateTo,
                'UserStatistic.section_id' => null,
                'UserStatistic.article_id' => null,
            );
            if (!empty($this->request->query)) {
                if (!empty($this->request->query['from'])) {
                    $dateFrom = Fecha::toFormatoBD($this->request->query['from']);
                }
                if (!empty($this->request->query['to'])) {
                    $dateTo = Fecha::toFormatoBD($this->request->query['to']);
                }

                $conditions = array(
                    'UserStatistic.date >=' => $dateFrom,
                    'UserStatistic.date <=' => $dateTo,
                );
                if (!empty($this->request->query['trading_group_id'])) {
                    $trading_group_id = $this->request->query['trading_group_id'];
                    $conditions[] = array(
                        'UserStatistic.trading_group_id' => $trading_group_id,
                    );
                } elseif (!empty($this->request->query['network_id'])) {
                    $network_id = $this->request->query['network_id'];
                    $conditions[] = array(
                        'UserStatistic.network_id' => $network_id,
                    );
                }
            }

            $this->request->query['from'] = $dateFrom;
            $this->request->query['to'] = $dateTo;

            $months = $this->months($dateFrom, $dateTo);
            $dateFrom = Fecha::toFormatoVista($dateFrom);
            $dateTo = Fecha::toFormatoVista($dateTo);

            $device_connections = array(
                ConstantsDevices::DESKTOP => 0,
                ConstantsDevices::MOBILE => 0,
                ConstantsDevices::TABLET => 0
            );
            $desktop_connections = array();
            $mobile_connections = array();
            $tablet_connections = array();
            $date_from_tmp = date("d-m-Y", strtotime("-1 month", strtotime($dateFrom)));
            $counter = 0;
            foreach ($months as $key_month => $month) {
                $counter++;
                $date_from_tmp = date('d-m-Y', strtotime('01-' . date("m-Y", strtotime("+1 month", strtotime($date_from_tmp)))));
                if ($counter == count($months)) {
                    $date_to_tmp = $dateTo;
                } else {
                    $date_to_tmp = date("t-m-Y", strtotime($date_from_tmp));
                }
                $desktop_connections[$key_month] = 0;
                $mobile_connections[$key_month] = 0;
                $tablet_connections[$key_month] = 0;
                $current_month = $key_month;
                $current_month++;
                $results_tmp = $this->UserStatistic->getAllByDateNetworkAndTradingGroup($date_from_tmp, $date_to_tmp, $network_id, $trading_group_id, $aagRegionId);
                foreach ($results_tmp as $result) {
                    if ($result['UserStatistic']['device'] == ConstantsDevices::DESKTOP) {
                        $desktop_connections[$key_month]++;
                        $device_connections[ConstantsDevices::DESKTOP]++;
                    } elseif ($result['UserStatistic']['device'] == ConstantsDevices::MOBILE) {
                        $mobile_connections[$key_month]++;
                        $device_connections[ConstantsDevices::MOBILE]++;
                    } elseif ($result['UserStatistic']['device'] == ConstantsDevices::TABLET) {
                        $tablet_connections[$key_month]++;
                        $device_connections[ConstantsDevices::TABLET]++;
                    }
                }

                $statistics_devices[$key_month] = array(
                    ConstantsDevices::DESKTOP => $desktop_connections[$key_month],
                    ConstantsDevices::MOBILE => $mobile_connections[$key_month],
                    ConstantsDevices::TABLET => $tablet_connections[$key_month],
                    'month' => $month
                );
            }
            $this->set(array(
                'statistics_devices' => $statistics_devices
            ));

            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $this->render('/Statistics/Elements/export_excel_devices');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Export BDMs in Excel. Not AJAX request.
     */
    public function ajax_bdms_excel()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
            )
        ) {
            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d') . " -1 month"));
            $dateTo = date('Y-m-d');

            if (!empty($this->request->query)) {
                if (!empty($this->request->query['from'])) {
                    $dateFrom = Fecha::toFormatoBD($this->request->query['from']);
                }
                if (!empty($this->request->query['to'])) {
                    $dateTo = Fecha::toFormatoBD($this->request->query['to']);
                }
            }

            $bdms_tmp = $this->User->getBDMUsers();
            foreach ($bdms_tmp as $key => $bdm) {
                $garage_visits = null;
                $distributor_visits = null;
                $garage_visits = $this->Appointment->getGarageVisitByUser($key, $dateFrom, $dateTo);
                $distributor_visits = $this->Appointment->getDistributorVisitByUser($key, $dateFrom, $dateTo);
                $events_visits = $this->Appointment->getEventsVisitByUser($key, Fecha::toFormatoBd($dateFrom), Fecha::toFormatoBd($dateTo));
                $bdms[$key]['name'] = $bdm;
                $bdms[$key]['GarageVisits'] = count($garage_visits);
                $bdms[$key]['DistributorVisits'] = count($distributor_visits);
                $bdms[$key]['EventVisits'] = count($events_visits);

                $garage_duration = 0;
                if ($garage_visits) {
                    foreach ($garage_visits as $garage_visit) {
                        $fecha1 = strtotime($garage_visit['Appointment']['date']) + strtotime($garage_visit['Appointment']['start_time']); //fecha inicial
                        $fecha2 = strtotime($garage_visit['Appointment']['end_date']) + strtotime($garage_visit['Appointment']['end_time']); //fecha de cierre
                        $intervalo = ($fecha2 - $fecha1);
                        $garage_duration = $garage_duration + $intervalo;
                    }
                }


                $bdms[$key]['DurationGarageVisits'] = $garage_duration;

                $distributor_duration = 0;
                if ($distributor_visits) {
                    foreach ($distributor_visits as $distributor_visit) {
                        $fecha1 = strtotime($distributor_visit['Appointment']['date']) + strtotime($distributor_visit['Appointment']['start_time']); //fecha inicial
                        $fecha2 = strtotime($distributor_visit['Appointment']['end_date']) + strtotime($distributor_visit['Appointment']['end_time']); //fecha de cierre
                        $intervalo = ($fecha2 - $fecha1);
                        $distributor_duration = $distributor_duration + $intervalo;
                    }
                }
                $bdms[$key]['DurationDistributorVisits'] = $distributor_duration;
                $bdms[$key]['AverageDurationGarageVisits'] = $garage_duration ? $garage_duration / count($garage_visits) : 0;
                $bdms[$key]['AverageDurationDistributorVisits'] = $distributor_duration ? $distributor_duration / count($distributor_visits) : 0;
            }

            $this->set(array(
                'bdms' => $bdms
            ));

            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $this->render('/Statistics/Elements/export_excel_bdms');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Export BDM in Excel. Not AJAX request.
     */
    public function ajax_bdm_excel($user_id, $type)
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
            )
        ) {
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

            $search = $this->request->query;
            $search['user_assigned_id'] = $user_id;

            $conditions = $this->Appointment->conditions($search);

            $conditions[]['OR'] = [
                ['Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT],
                ['Appointment.appointment_type_id' => ConstantsTypesAppointments::PROSPECT_GARAGE_VISIT]
            ];

            $conditions[]['OR'] = [
                ['Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED],
                ['Appointment.appointment_status_id' => ConstantsStatusAppointments::EVENT]
            ];

            if ($type == 'garage') {
                $conditions[] = array('Appointment.garage_id !=' => null);
            }
            if ($type == 'distributor') {
                $conditions[] = array('Appointment.distributor_id !=' => null);
            }

            $appointments = $this->Appointment->findAppointmentsBdm($this->Appointment->_query('search_agenda_list'), $conditions);

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
                    'user_id' => $search['user_assigned_id'],
                )
            );

            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $this->render('/Statistics/Elements/export_excel_bdm');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Export garages in Excel. Not AJAX request.
     */
    public function ajax_garages_excel($dateFrom, $dateTo)
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
            )
        ) {
            $dateFrom = Fecha::toFormatoBD($dateFrom);
            $dateTo = Fecha::toFormatoBD($dateTo);

            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $contacts = $this->Contact->getBDMContact($aagRegionId);
            $garages = $this->Appointment->getTopAppointmentGarage($dateFrom, $dateTo, $aagRegionId);

            $this->set(
                array(
                    'garages' => $garages,
                    'contacts' => $contacts,
                )
            );

            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $this->render('/Statistics/Elements/export_excel_garages');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get garages.
     */
    public function ajax_garages($dateFrom, $dateTo, $page = 1)
    {
        $this->verify_ajax($this->request);
        
        set_time_limit(18000);
        ini_set('memory_limit', '-1');

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $dateFrom = Fecha::toFormatoBD($dateFrom);
            $dateTo = Fecha::toFormatoBD($dateTo);
            
            $limit = 20000;
            $offset = ($page - 1) * $limit;

            $contacts = $this->Contact->getBDMContact($aagRegionId);
            $garages = $this->Appointment->getTopAppointmentGarage($dateFrom, $dateTo, $aagRegionId, $limit, $offset);

            $this->set(
                array(
                    'garages' => $garages,
                    'contacts' => $contacts,
                    'user_aag_region_id' => CakeSession::read('Auth.User.aag_region_id'),
                    'user_role' => CakeSession::read('Auth.User.role_id')
                )
            );

            $this->layout = false;
            $this->render('/Statistics/Elements/garages');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Export distributors in Excel. Not AJAX request.
     */
    public function ajax_distributors_excel($dateFrom, $dateTo)
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $dateFrom = Fecha::toFormatoBD($dateFrom);
            $dateTo = Fecha::toFormatoBD($dateTo);

            $distributors = $this->Appointment->getTopAppointmentDistributor($dateFrom, $dateTo, $aagRegionId);
            $contacts = $this->Contact->getBDMContact($aagRegionId);

            $this->set(
                array(
                    'distributors' => $distributors,
                    'contacts' => $contacts,
                )
            );

            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $this->render('/Statistics/Elements/export_excel_distributors');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get distributors.
     */
    public function ajax_distributors($dateFrom, $dateTo)
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $dateFrom = Fecha::toFormatoBD($dateFrom);
            $dateTo = Fecha::toFormatoBD($dateTo);

            $distributors = $this->Appointment->getTopAppointmentDistributor($dateFrom, $dateTo, $aagRegionId);
            $contacts = $this->Contact->getBDMContact($aagRegionId);

            $this->set(
                array(
                    'distributors' => $distributors,
                    'contacts' => $contacts,
                )
            );

            $this->layout = false;
            $this->render('/Statistics/Elements/distributors');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setUsers()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $users = $this->User->listCompleteNameRegion($aagRegionId);

        $allUsers = $this->User->listCompleteNameRegion($aagRegionId);
        $this->set(
            array(
                'users' => $users,
                'all_users' => $allUsers,
            )
        );
    }

    private function months($dateFrom, $dateTo)
    {
        $time_tmp = strtotime($dateFrom);
        $month_start = date('m', $time_tmp);

        $ts1 = strtotime($dateFrom);
        $ts2 = strtotime($dateTo);

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        $diff = (($year2 - $year1) * 12) + ($month2 - $month1);

        $months = array(
            1 => mb_substr(__t('General.January'), 0, 3),
            2 => mb_substr(__t('General.February'), 0, 3),
            3 => mb_substr(__t('General.March'), 0, 3),
            4 => mb_substr(__t('General.April'), 0, 3),
            5 => mb_substr(__t('General.May'), 0, 3),
            6 => mb_substr(__t('General.June'), 0, 3),
            7 => mb_substr(__t('General.July'), 0, 3),
            8 => mb_substr(__t('General.August'), 0, 3),
            9 => mb_substr(__t('General.September'), 0, 3),
            10 => mb_substr(__t('General.October'), 0, 3),
            11 => mb_substr(__t('General.November'), 0, 3),
            12 => mb_substr(__t('General.December'), 0, 3),
            13 => mb_substr(__t('General.January'), 0, 3),
            14 => mb_substr(__t('General.February'), 0, 3),
            15 => mb_substr(__t('General.March'), 0, 3),
            16 => mb_substr(__t('General.April'), 0, 3),
            17 => mb_substr(__t('General.May'), 0, 3),
            18 => mb_substr(__t('General.June'), 0, 3),
            19 => mb_substr(__t('General.July'), 0, 3),
            20 => mb_substr(__t('General.August'), 0, 3),
            21 => mb_substr(__t('General.September'), 0, 3),
            22 => mb_substr(__t('General.October'), 0, 3),
            23 => mb_substr(__t('General.November'), 0, 3),
            24 => mb_substr(__t('General.December'), 0, 3),
        );

        $counter = 0;
        foreach ($months as $key => $month) {
            if ($key < $month_start || $counter > $diff) {
                unset($months[$key]);
            } else {
                $counter++;
            }
        }

        return array_values($months);
    }
}
