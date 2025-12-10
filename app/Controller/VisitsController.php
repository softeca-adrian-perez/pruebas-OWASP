<?php
class VisitsController extends AppController
{
    public $uses = array(
        'Appointment',
        'Contact',
        'Garage',
        'GarageImage',
        'GarageContactGeneralBranchManager',
        'DistributorContactGeneralBranchManager',
        'Network',
        'Distributor',
        'DistributorImage',
        'User',
        'ContactRegion',
        'GarageFigureDetail',
        'Route',
        'GarageRoute',
        'DistributorRoute',
        'UserSearch',
        'SearchNetwork',
        'SearchDistributor',
        'SearchContact',
        'GarageNetwork',
        'Position',
        'DistributorContactBdm',
        'Email',
        'AagRegion'
    );

    /**
     * Visits home page.
     */
    public function home($route_id = null)
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $network_options = $this->Network->find('list', array(
                'conditions' => array(
                    'Network.aag_region_id' => $aagRegionId
                ),
                'order' => 'Network.name',
            ));
            $networks_statuses_all = Configure::read('Network_Status');
            foreach ($networks_statuses_all as $key => $network_status) {
                $networks_statuses_all[$key] = __t($network_status);
            }
            $networks_statuses_tmp = $this->GarageNetwork->getStatusFromGarageNetworks();

            $networks_statuses = array();
            foreach ($networks_statuses_tmp as $network_status_tmp) {
                $networks_statuses[$network_status_tmp] = $networks_statuses_all[$network_status_tmp];
            }

            $routes = $this->Route->search_list_garage();
            $searches = $this->UserSearch->getListSearchesByLastUseAndGarages();

            $client_type_options = array(
                'A' => 'A',
            );

            $last_visit_options = array(
                ConstantsLastVisit::NO_VISIT => __t('Visit.No_visit'),
                ConstantsLastVisit::PLUS_SIX_MONTHS => __t('Visit.Plus_6_months'),
                ConstantsLastVisit::THREE_MONTHS_SIX_MONTHS => __t('Visit.3_months_6_months'),
                ConstantsLastVisit::MINUS_THREE_MONTHS => __t('Visit.3_months')
            );

            $distance_options = null;
            if (CakeSession::read('Auth.User.aag_region_id') != Configure::read('AAG_REGION_ID_UK_IRELAND')) {
                $distance_options = Configure::read('Distance_Options_KM');
            } else {
                $distance_options = Configure::read('Distance_Options_Miles');
            }

            $conditions = array('AagRegion.id' => $aagRegionId);
            $regions = $this->AagRegion->region_list_conditions($conditions);

            $this->setUsers();
            $this->set(array(
                'routes' => $routes,
                'route_id' => $route_id,
                'user' => $user,
                'network_options' => $network_options,
                'networks_statuses' => $networks_statuses,
                'distance_options' => $distance_options,
                'last_visit_options' => $last_visit_options,
                'client_type_options' => $client_type_options,
                'distributors' => array(),
                'contacts_bdm' => array(),
                'searches' => $searches,
                'garages' => array(),
                'garages_number' => 0,
                'garages_number_page' => 1,
                'regions' => $regions,
                'super_admin' => $user['role_id'] == ConstantsRoles::SUPER_ADMIN ? true : false,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Visits distributor home page.
     */
    public function home_distributor($route_id = null)
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $routes = $this->Route->search_list_distributor();
            $user = $this->Acceso->user();
            $searches = $this->UserSearch->getListSearchesByLastUseAndDistributors();

            $users_bdm = $this->setUsersDe();

            $client_type_options = array(
                'A' => 'A',
            );

            $last_visit_options = array(
                ConstantsLastVisit::NO_VISIT => __t('Visit.No_visit'),
                ConstantsLastVisit::PLUS_SIX_MONTHS => __t('Visit.Plus_6_months'),
                ConstantsLastVisit::THREE_MONTHS_SIX_MONTHS => __t('Visit.3_months_6_months'),
                ConstantsLastVisit::MINUS_THREE_MONTHS => __t('Visit.3_months')
            );

            $distance_options = null;
            if (CakeSession::read('Auth.User.aag_region_id') != Configure::read('AAG_REGION_ID_UK_IRELAND')) {
                $distance_options = Configure::read('Distance_Options_KM');
            } else {
                $distance_options = Configure::read('Distance_Options_Miles');
            }

            $this->setUsers();
            $this->set(array(
                'distance_options' => $distance_options,
                'user' => $user,
                'last_visit_options' => $last_visit_options,
                'client_type_options' => $client_type_options,
                'contacts_bdm' => array(),
                'users_bdm' => $users_bdm,
                'routes' => $routes,
                'route_id' => $route_id,
                'searches' => $searches,
                'distributors' => array(),
                'distributors_number' => 0,
                'distributors_number_page' => 1
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Routes list.
     */
    public function routes_list()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $user = $this->Acceso->user();
            $route_options = array(
                ConstantsVisitType::GARAGE => __t('Garage.Garage'),
                ConstantsVisitType::DISTRIBUTOR => __t('Distributor.Distributor'),
            );

            if ($user['role_id'] == ConstantsRoles::GPC_LOGISTICS_BDM) {
                unset($route_options[ConstantsVisitType::GARAGE]);
            }

            $search = $this->request->query;
            $this->request->data['Search'] = $search;

            $conditions = $this->Route->conditions($search);
            $conditions['user_creation_id'] = CakeSession::read('Auth.User.id');

            $routes = $this->custom_pagination(
                array(),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                $this->Route
            );

            foreach ($routes as $key => $route) {
                $conditions = array('route_id' => $route['Route']['id']);
                if ($route['Route']['type'] == ConstantsVisitType::GARAGE) {
                    $routes[$key]['Route']['customer_number'] = $this->GarageRoute->find('count', array('conditions' => $conditions));
                } elseif ($route['Route']['type'] == ConstantsVisitType::DISTRIBUTOR) {
                    $routes[$key]['Route']['customer_number'] = $this->DistributorRoute->find('count', array('conditions' => $conditions));
                }
            }

            $this->set(array(
                'route_options' => $route_options,
                'routes' => $routes
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search garages.
     */
    public function ajax_search_garages()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
            $conditions = array();
            $data = $this->request->data;

            if (!empty($data['location']) && !empty($data['region'])) {
                $garages_tmp = $this->Garage->findGaragesPlanningVisits($data['region']);
                $distance_type = null;
                if (CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_UK_IRELAND')) {
                    $distance_type = ConstantsDistanceType::MILES;
                } else {
                    $distance_type = ConstantsDistanceType::KM;
                }

                foreach ($garages_tmp as $key => $garage) {
                    if (isset($data['lat']) && isset($data['lng']) && $data['lat'] != '' && $data['lng'] != '') {
                        $distance = $this->distance($garage['Garage']['latitude'], $garage['Garage']['longitude'], $data['lat'], $data['lng'], $distance_type);
                        if ($distance > $data['distance']) {
                            unset($garages_tmp[$key]);
                        }
                    }
                }
                $garage_condition = Hash::extract($garages_tmp, '{n}.Garage.id');
                $conditions['Garage.id'] = $garage_condition;
            }

            foreach ($data as $key => $item) {
                if (!empty($item)) {
                    switch ($key) {
                        case 'town':
                            $conditions['Garage.town LIKE'] = '%' . $item . '%';
                            break;
                        case 'latest_visit':
                            if ($item == ConstantsLastVisit::PLUS_SIX_MONTHS) {
                                $last_visit = date('Y-m-d', strtotime("-6 months"));
                                $conditions['Garage.last_visit <'] = $last_visit;
                            } elseif ($item == ConstantsLastVisit::THREE_MONTHS_SIX_MONTHS) {
                                $last_visit_start = date('Y-m-d', strtotime("-6 months"));
                                $last_visit_end = date('Y-m-d', strtotime("-3 months"));
                                $conditions['Garage.last_visit >'] = $last_visit_start;
                                $conditions['Garage.last_visit <'] = $last_visit_end;
                            } elseif ($item == ConstantsLastVisit::MINUS_THREE_MONTHS) {
                                $last_visit = date('Y-m-d', strtotime("-3 months"));
                                $conditions['Garage.last_visit >'] = $last_visit;
                            } elseif ($item == ConstantsLastVisit::NO_VISIT) {
                                $conditions['Garage.last_visit'] = null;
                            }
                            break;
                        case 'client_type':
                            $conditions['Garage.client_type'] = $item;
                            break;
                        case 'network':
                            $conditions['GarageNetwork.network_id'] = $item;
                            break;
                        case 'network_status':
                            $conditions['GarageNetwork.status'] = $item;
                            break;
                        case 'distributor':
                            $conditions['GarageDistributor.distributor_id'] = $item;
                            break;
                        case 'route':
                            $conditions['GarageRoute.route_id'] = $item;
                            break;
                        case 'contact':
                            $conditions['GarageContactBdm.contact_id'] = $item;
                            break;
                        case 'my_customers':
                            if ($item == 'true') {
                                $conditions['GarageContactBdm.contact_id'] = CakeSession::read('Auth.User.contact_id');
                            }
                            break;
                        case 'region':
                            $conditions['Garage.aag_region_id'] = $item;
                            break;
                        default:
                            break;
                    }
                }
            }

            $garages = $this->Garage->getVisits($conditions);

            $this->set(array(
                'garages' => $garages,
            ));
            $this->layout = false;
            $this->render('/Visits/Elements/results_table');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search distributors.
     */
    public function ajax_search_distributors()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $conditions = array();
            $data = $this->request->data;

            if (!empty($data['location'])) {
                $distributors_tmp = $this->Distributor->findDitributorsPlanningVisits($aagRegionId);
                $distance_type = null;
                if (CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_UK_IRELAND')) {
                    $distance_type = ConstantsDistanceType::MILES;
                } else {
                    $distance_type = ConstantsDistanceType::KM;
                }
                foreach ($distributors_tmp as $key => $distributor) {
                    if (isset($data['lat']) && isset($data['lng'])) {
                        $distance = $this->distance($distributor['Distributor']['latitude'], $distributor['Distributor']['longitude'], $data['lat'], $data['lng'], $distance_type);
                        if ($distance > $data['distance']) {
                            unset($distributors_tmp[$key]);
                        }
                    }
                }
                $distributor_condition = Hash::extract($distributors_tmp, '{n}.Distributor.id');
                $conditions['Distributor.id'] = $distributor_condition;
            }

            foreach ($data as $key => $item) {
                if (!empty($item)) {
                    switch ($key) {
                        case 'town':
                            $conditions['Distributor.town LIKE'] = '%' . $item . '%';
                            break;
                        case 'latest_visit':
                            if ($item == ConstantsLastVisit::PLUS_SIX_MONTHS) {
                                $last_visit = date('Y-m-d', strtotime("-6 months"));
                                $conditions['Distributor.last_visit <'] = $last_visit;
                            } elseif ($item == ConstantsLastVisit::THREE_MONTHS_SIX_MONTHS) {
                                $last_visit_start = date('Y-m-d', strtotime("-6 months"));
                                $last_visit_end = date('Y-m-d', strtotime("-3 months"));
                                $conditions['Distributor.last_visit >'] = $last_visit_start;
                                $conditions['Distributor.last_visit <'] = $last_visit_end;
                            } elseif ($item == ConstantsLastVisit::MINUS_THREE_MONTHS) {
                                $last_visit = date('Y-m-d', strtotime("-3 months"));
                                $conditions['Distributor.last_visit >'] = $last_visit;
                            } elseif ($item == ConstantsLastVisit::NO_VISIT) {
                                $conditions['Distributor.last_visit'] = null;
                            }
                            break;
                        case 'client_type':
                            $conditions['Distributor.client_type'] = $item;
                            break;
                        case 'route':
                            $conditions['DistributorRoute.route_id'] = $item;
                            break;
                        case 'contact':
                            $conditions['DistributorContactBdm.contact_id'] = $item;
                            break;
                        case 'my_customers':
                            if ($item == 'true') {
                                $conditions['DistributorContactBdm.contact_id'] = CakeSession::read('Auth.User.contact_id');
                            } else {
                                $contact_tmp = $this->Contact->findById($this->Session->read('Auth.User.Contact.id'));
                            }
                            break;
                        default:
                            break;
                    }
                }
            }

            $conditions[] = array('Distributor.status' => ConstantsDistributorStatus::ACTIVE);
            $conditions[] = array('TradingGroup.aag_region_id' => $aagRegionId);
            $distributors = $this->Distributor->getVisits($conditions);

            $this->set(array(
                'distributors' => $distributors,
            ));

            $this->layout = false;
            $this->render('/Visits/Elements/results_table_distributors');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Generate visits.
     */
    public function generate_visits()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $data = $this->request->data;
            $date = $data['date'];
            unset($data['date']);
            $user = $this->User->findById(CakeSession::read('Auth.User.id'));

            foreach ($data as $item) {
                if ($item != 'date') {
                    $appointment = array(
                        'Appointment' => array(
                            'garage_id' => $item['garage_id'],
                            'appointment_feeling_id' => null,
                            'appointment_status_id' => ConstantsStatusAppointments::PLANNED,
                            'appointment_type_id' => ConstantsTypesAppointments::VISIT,
                            'date' => $date,
                            'user_creation_id' => $user['User']['id'],
                            'user_assigned_id' => $item['user_assigned_id'],
                            'start_time' => $item['start_time'],
                            'end_time' => $item['end_time'],
                        ),
                    );
                    $appointment_bd = $this->Appointment->add_appointment($appointment, $user['User']);
                    if (!$appointment_bd) {
                        exit;
                    }
                }
            }
            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Generate visits distributors.
     */
    public function generate_visits_distributors()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $data = $this->request->data;
            $date = $data['date'];
            unset($data['date']);
            $user = $this->User->findById(CakeSession::read('Auth.User.id'));

            foreach ($data as $item) {
                if ($item != 'date') {
                    $user_tmp = $this->User->findById($item['user_assigned_id']);
                    $contact = $this->Contact->findById($user_tmp['User']['contact_id']);
                    $position = $this->Position->findById($contact['Contact']['position_id']);
                    $distributor_contact = $this->DistributorContactBdm->findByDistributorIdAndContactId($item['distributor_id'], $contact['Contact']['id']);

                    if (strpos($position['Position']['name' . __s()], 'ommercial ') &&  !$distributor_contact) { /// Eventual para Francia
                        $this->layout = $this->autoRender = false;
                        return 'Error-customer';
                    }

                    $appointment = array(
                        'Appointment' => array(
                            'distributor_id' => $item['distributor_id'],
                            'appointment_feeling_id' => null,
                            'appointment_status_id' => ConstantsStatusAppointments::PLANNED,
                            'appointment_type_id' => ConstantsTypesAppointments::VISIT,
                            'date' => $date,
                            'user_creation_id' => $user['User']['id'],
                            'user_assigned_id' => $item['user_assigned_id'],
                            'start_time' => $item['start_time'],
                            'end_time' => $item['end_time'],
                        ),
                    );
                    $appointment_bd = $this->Appointment->add_appointment($appointment, $user['User']);
                    if (!$appointment_bd) {
                        exit;
                    }
                }
            }
            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Location garages.
     */
    public function location_garages()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $conditions = array();

            $conditions['Garage.aag_region_id'] =  $aagRegionId;
            $garages = $this->Garage->getGaragesVisits($conditions);
            $this->autoRender = false;
            return json_encode($garages);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Location distributors.
     */
    public function location_distributors()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $conditions = array();

            $conditions[] = array('Distributor.status' => ConstantsDistributorStatus::ACTIVE);
            $conditions[] = array('Distributor.aag_region_id' => $aagRegionId);
            $distributors = $this->Distributor->getDistributorsVisits($conditions);

            $this->autoRender = false;
            return json_encode($distributors);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete search.
     */
    public function ajax_delete_search()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $search_id = $this->request->data['id'];

            $search_networks = $this->SearchNetwork->findAllBySearchId($search_id);
            $search_distributors = $this->SearchDistributor->findAllBySearchId($search_id);
            $search_contacts = $this->SearchContact->findAllBySearchId($search_id);

            foreach ($search_networks as $search_network) {
                $flag_network = $this->SearchNetwork->delete($search_network['SearchNetwork']['id']);
            }

            foreach ($search_distributors as $search_distributor) {
                $flag_distributor = $this->SearchDistributor->delete($search_distributor['SearchDistributor']['id']);
            }

            foreach ($search_contacts as $search_contact) {
                $flag_contact = $this->SearchContact->delete($search_contact['SearchContact']['id']);
            }

            if ((!isset($flag_network) || $flag_network) && (!isset($flag_distributor) || $flag_distributor) && (!isset($flag_contact) || $flag_contact)) {
                $this->UserSearch->delete($search_id);
            }

            $this->autoRender = null;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get data garage.
     */
    public function ajax_get_data_garage()
    {
        $this->verify_ajax($this->request);
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $garage_id = $this->request->data['garage_id'];
            $added = true;

            $garage = $this->Garage->findById($garage_id);
            $garage_manager = $this->GarageContactGeneralBranchManager->findManagerByGarageAndRole($garage_id, ConstantsRoles::GARAGE);

            $appointment = $this->Appointment->getPreviousByGarage($garage['Garage']['id']);
            if (!empty($appointment)) {
                $garage['Garage']['last_visit'] = $appointment['Appointment']['date'];
            } else {
                $garage['Garage']['last_visit'] = null;
            }


            $this->set(array(
                'garage' => $garage,
                'garage_manager' => $garage_manager,
                'added' => $added
            ));

            $this->layout = false;
            $this->render('../Visits/Elements/row_garage');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get data info window garage.
     */
    public function ajax_get_data_infowindow_garage()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $garage_id = $this->request->query['garage_id'];
            $garage = $this->Garage->findById($garage_id);
            $garage_image = $this->GarageImage->getPrincipalImage($garage_id);
            $garage_managers = $this->GarageContactGeneralBranchManager->findManagerByGarageAndRole($garage_id, ConstantsRoles::GARAGE);


            $this->set(array(
                'garage' => $garage,
                'garage_image' => $garage_image,
                'garage_managers' => $garage_managers
            ));

            $this->layout = false;
            $this->render('../Visits/Elements/ajax_get_data_infowindow_garage');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get data distributor.
     */
    public function ajax_get_data_distributor()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $distributor_id = $this->request->data['distributor_id'];
            $added = true;

            $distributor = $this->Distributor->findById($distributor_id);
            $distributor_managers = $this->DistributorContactGeneralBranchManager->findManagerByDistributorAndRole($distributor_id, ConstantsRoles::DISTRIBUTOR);

            $appointment = $this->Appointment->getPreviousByDistributor($distributor['Distributor']['id']);
            if (!empty($appointment)) {
                $distributor['Distributor']['last_visit'] = $appointment['Appointment']['date'];
            } else {
                $distributor['Distributor']['last_visit'] = null;
            }

            $this->set(array(
                'distributor' => $distributor,
                'distributor_managers' => $distributor_managers,
                'added' => $added
            ));

            $this->layout = false;
            $this->render('../Visits/Elements/row_distributor');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get data info window distributor.
     */
    public function ajax_get_data_infowindow_distributor()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $distributor_id = $this->request->query['distributor_id'];
            $distributor = $this->Distributor->findById($distributor_id);
            $distributor_image = $this->DistributorImage->getPrincipalImage($distributor_id);
            $distributor_managers = $this->DistributorContactGeneralBranchManager->findManagerByDistributorAndRole($distributor_id, ConstantsRoles::DISTRIBUTOR);
            $this->set(array(
                'distributor' => $distributor,
                'distributor_image' => $distributor_image,
                'distributor_managers' => $distributor_managers,
            ));

            $this->layout = false;
            $this->render('../Visits/Elements/ajax_get_data_infowindow_distributor');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get route data.
     */
    public function ajax_get_route_data()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
            $conditions = array();
            $data = $this->request->data;
            $route_id = null;

            if (!empty($data['location'])) {
                $garages_tmp = $this->Garage->find('all', array(
                    'fields' => array(
                        'Garage.id',
                        'Garage.latitude',
                        'Garage.longitude',
                    )
                ));
                $distance_type = null;
                if (CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_UK_IRELAND')) {
                    $distance_type = ConstantsDistanceType::MILES;
                } else {
                    $distance_type = ConstantsDistanceType::KM;
                }

                foreach ($garages_tmp as $key => $garage) {
                    if (isset($data['lat']) && isset($data['lng']) && $data['lat'] != '' && $data['lng'] != '') {
                        $distance = $this->distance($garage['Garage']['latitude'], $garage['Garage']['longitude'], $data['lat'], $data['lng'], $distance_type);
                        if ($distance > $data['distance']) {
                            unset($garages_tmp[$key]);
                        }
                    }
                }
                $garage_condition = Hash::extract($garages_tmp, '{n}.Garage.id');
                $conditions['Garage.id'] = $garage_condition;
            }

            foreach ($data as $key => $item) {
                if (!empty($item)) {
                    switch ($key) {
                        case 'town':
                            $conditions['Garage.town LIKE'] = '%' . $item . '%';
                            break;
                        case 'latest_visit':
                            if ($item == ConstantsLastVisit::PLUS_THREE_MONTHS) {
                                $last_visit = date('Y-m-d', strtotime("-3 months"));
                                $conditions['Garage.last_visit <'] = $last_visit;
                            } elseif ($item == ConstantsLastVisit::THREE_MONTHS_TWO_WEEKS) {
                                $last_visit_start = date('Y-m-d', strtotime("-3 months"));
                                $last_visit_end = date('Y-m-d', strtotime("-2 weeks"));
                                $conditions['Garage.last_visit >'] = $last_visit_start;
                                $conditions['Garage.last_visit <'] = $last_visit_end;
                            } elseif ($item == ConstantsLastVisit::MINUS_TWO_WEEKS) {
                                $last_visit = date('Y-m-d', strtotime("-2 weeks"));
                                $conditions['Garage.last_visit >'] = $last_visit;
                            } elseif ($item == ConstantsLastVisit::NO_VISIT) {
                                $conditions['Garage.last_visit'] = null;
                            }
                            break;
                        case 'client_type':
                            $conditions['Garage.client_type'] = $item;
                            break;
                        case 'network':
                            $conditions['GarageNetwork.network_id'] = $item;
                            break;
                        case 'network_status':
                            $conditions['GarageNetwork.status'] = $item;
                            break;
                        case 'distributor':
                            $conditions['GarageDistributor.distributor_id'] = $item;
                            break;
                        case 'route':
                            $conditions['GarageRoute.route_id'] = $item;
                            $route_id = $conditions['GarageRoute.route_id'];
                            break;
                        case 'contact':
                            $conditions['GarageContactBdm.contact_id'] = $item;
                            break;
                        case 'my_customers':
                            if ($item == 'true') {
                                $conditions['GarageContactBdm.contact_id'] = CakeSession::read('Auth.User.contact_id');
                            }
                            break;
                        default:
                            break;
                    }
                }
            }

            $garages_tmp = $this->Garage->getVisits($conditions);
            $garages = array();
            foreach ($garages_tmp as $key => $garage) {
                $garage_route = $this->GarageRoute->findByGarageIdAndRouteId($garage['Garage']['id'], $route_id);
                $garages_tmp[$key]['Garage']['start_time'] = $garage_route['GarageRoute']['start_time'];
                $garages_tmp[$key]['Garage']['end_time'] = $garage_route['GarageRoute']['end_time'];
                $garages[$garage_route['GarageRoute']['order']] = $garages_tmp[$key];
            }

            ksort($garages);

            $this->set(array(
                'garages' => $garages,
            ));

            $this->layout = false;
            $this->render('/Visits/Elements/ajax_route_garages');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get route data distributor.
     */
    public function ajax_get_route_data_distributor()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $conditions = array();
            $data = $this->request->data;
            $route_id = null;

            if (!empty($data['location'])) {
                $distributors_tmp = $this->Distributor->find('all', array(
                    'fields' => array(
                        'Distributor.id',
                        'Distributor.latitude',
                        'Distributor.longitude',
                    )
                ));
                $distance_type = null;
                if (CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_UK_IRELAND')) {
                    $distance_type = ConstantsDistanceType::MILES;
                } else {
                    $distance_type = ConstantsDistanceType::KM;
                }
                foreach ($distributors_tmp as $key => $distributor) {
                    if (isset($data['lat']) && isset($data['lng'])) {
                        $distance = $this->distance($distributor['Distributor']['latitude'], $distributor['Distributor']['longitude'], $data['lat'], $data['lng'], $distance_type);
                        if ($distance > $data['distance']) {
                            unset($distributors_tmp[$key]);
                        }
                    }
                }
                $distributor_condition = Hash::extract($distributors_tmp, '{n}.Distributor.id');
                $conditions['Distributor.id'] = $distributor_condition;
            }

            foreach ($data as $key => $item) {
                if (!empty($item)) {
                    switch ($key) {
                        case 'town':
                            $conditions['Distributor.town LIKE'] = '%' . $item . '%';
                            break;
                        case 'latest_visit':
                            if ($item == ConstantsLastVisit::PLUS_THREE_MONTHS) {
                                $last_visit = date('Y-m-d', strtotime("-3 months"));
                                $conditions['Distributor.last_visit <'] = $last_visit;
                            } elseif ($item == ConstantsLastVisit::THREE_MONTHS_TWO_WEEKS) {
                                $last_visit_start = date('Y-m-d', strtotime("-3 months"));
                                $last_visit_end = date('Y-m-d', strtotime("-2 weeks"));
                                $conditions['Distributor.last_visit >'] = $last_visit_start;
                                $conditions['Distributor.last_visit <'] = $last_visit_end;
                            } elseif ($item == ConstantsLastVisit::MINUS_TWO_WEEKS) {
                                $last_visit = date('Y-m-d', strtotime("-2 weeks"));
                                $conditions['Distributor.last_visit >'] = $last_visit;
                            } elseif ($item == ConstantsLastVisit::NO_VISIT) {
                                $conditions['Distributor.last_visit'] = null;
                            }
                            break;
                        case 'client_type':
                            $conditions['Distributor.client_type'] = $item;
                            break;
                        case 'route':
                            $conditions['DistributorRoute.route_id'] = $item;
                            $route_id = $item;
                            break;
                        case 'contact':
                            $conditions['DistributorContactBdm.contact_id'] = $item;
                            break;
                        case 'my_customers':
                            if ($item == 'true') {
                                $conditions['DistributorContactBdm.contact_id'] = CakeSession::read('Auth.User.contact_id');
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
            $distributors_tmp = $this->Distributor->getVisits($conditions);
            $distributors = array();
            foreach ($distributors_tmp as $key => $distributor) {
                $distributor_route = $this->DistributorRoute->findByDistributorIdAndRouteId($distributor['Distributor']['id'], $route_id);
                $distributors_tmp[$key]['Distributor']['start_time'] = $distributor_route['DistributorRoute']['start_time'];
                $distributors_tmp[$key]['Distributor']['end_time'] = $distributor_route['DistributorRoute']['end_time'];
                $distributors[$distributor_route['DistributorRoute']['order']] = $distributors_tmp[$key];
            }

            ksort($distributors);

            $this->set(array(
                'distributors' => $distributors,
            ));

            $this->layout = false;
            $this->render('/Visits/Elements/ajax_route_distributors');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function distance($lat1, $lon1, $lat2, $lon2, $type)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        // return in kilometres
        if ($type == ConstantsDistanceType::KM) {
            return ($miles * 1.609344);
        } else {
            return $miles;
        }
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

        return $this->User->listCompleteNameRegion($aagRegionId);
    }
}
