<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class TrainingsListDelegatesController extends AppController
{
    public $uses = array(
        'TrainingDelegate',
        'TrainingCourse',
        'Garage',
        'Contact',
        'TrainingProvider',
        'Network',
        'ReasonDelegateCancelled',
        'Venue',
        'TrainingPlannedCourse',
        'Distributor',
        'Position'
    );

    /**
     * TrainingListDelegate home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $networks = $this->Network->getNetworksTraining($aagRegionId);
            $cancelled = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );
            $creditTaken = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            $emptyValue = null;
            $delegatesList = $this->custom_pagination(
                $this->TrainingDelegate->_query('home_list', $emptyValue),
                $this->TrainingDelegate->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'TrainingDelegate',
                null,
                'PaginatorOrderCustom'
            );

            $arrayGarageName = array();
            if (!empty($searcher['Garage_name'])) {
                $arrayGarageName = $this->Garage->getGaragesNameByIdGarage($searcher['Garage_name']);
            }

            $arrayVenueName = array();
            if (!empty($searcher['Venue_name'])) {
                $arrayVenueName = $this->Venue->getVenuesNameByIdVenue($searcher['Venue_name']);
            }

            $this->set(array(
                'delegates_list' => $delegatesList,
                'array_garage_name' => $arrayGarageName,
                'max_value_price' => $this->TrainingCourse->checkMaxValuePrice(),
                'price_credit_search' => isset($searcher['TrainingCourse_price_credit']) ? $searcher['TrainingCourse_price_credit'] : null,
                'credit_taken' => $creditTaken,
                'course_list' => $this->TrainingCourse->searchList(),
                'training_provider_list' => $this->TrainingProvider->searchList(),
                'cancelled' => $cancelled,
                'network_list' => $networks,
                'reason_cancelled_list' => $this->ReasonDelegateCancelled->search_list(),
                'array_venue_name' => $arrayVenueName,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search for Training List Delegates.
     */
    public function ajax_search_home($type_param = null)
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $networks = $this->Network->getNetworksTraining($aagRegionId);
            $cancelled = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );
            $creditTaken = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            $emptyValue = null;
            $delegatesList = $this->custom_pagination(
                $this->TrainingDelegate->_query('home_list', $emptyValue),
                $this->TrainingDelegate->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'TrainingDelegate',
                null,
                'PaginatorOrderCustom'
            );

            $arrayGarageName = array();
            if (!empty($searcher['Garage_name'])) {
                $arrayGarageName = $this->Garage->getGaragesNameByIdGarage($searcher['Garage_name']);
            }

            $arrayVenueName = array();
            if (!empty($searcher['Venue_name'])) {
                $arrayVenueName = $this->Venue->getVenuesNameByIdVenue($searcher['Venue_name']);
            }

            $this->set(array(
                'delegates_list' => $delegatesList,
                'array_garage_name' => $arrayGarageName,
                'max_value_price' => $this->TrainingCourse->checkMaxValuePrice(),
                'price_credit_search' => isset($searcher['TrainingCourse_price_credit']) ? $searcher['TrainingCourse_price_credit'] : null,
                'credit_taken' => $creditTaken,
                'course_list' => $this->TrainingCourse->searchList(),
                'training_provider_list' => $this->TrainingProvider->searchList(),
                'cancelled' => $cancelled,
                'network_list' => $networks,
                'reason_cancelled_list' => $this->ReasonDelegateCancelled->search_list(),
                'array_venue_name' => $arrayVenueName,
            ));

            $type_url_ajax = Configure::read('TypeSearchAjax');

            $this->layout = null;
            $this->render($type_url_ajax[$type_param]);
        } else {
            throw new UnauthorizedException();
        }
    }

    public function training_list_delegates_excel()
    {
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            $delegates = $this->TrainingDelegate->find(
                'all',
                array(
                    'conditions' => array(
                                        $this->TrainingDelegate->conditions($searcher),
                                        'TrainingDelegate.network_id IS NOT NULL'
                                    ),
                    'fields' => array(
                        'all',
                    ),
                    'joins' => array(
                        array(
                            'alias' => 'TrainingPlannedCourse',
                            'table' => 'trainings_planned_courses',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingPlannedCourse.id = TrainingDelegate.training_planned_course_id',

                            ),
                        ),
                        array(
                            'alias' => 'TrainingCourse',
                            'table' => 'trainings_courses',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingCourse.id = TrainingPlannedCourse.training_course_id',

                            ),
                        ),
                        array(
                            'alias' => 'GarageContactStaff',
                            'table' => 'garages_contacts_staff',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageContactStaff.id = TrainingDelegate.garage_contact_staff_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageContactStaff.garage_id',
                            ),
                        ),
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Network.id = TrainingDelegate.network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Contact',
                            'table' => 'contacts',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Contact.id = GarageContactStaff.contact_id',
                            ),
                        ),
                        array(
                            'alias' => 'Position',
                            'table' => 'positions',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Position.id = Contact.position_id',
                            ),
                        ),
                        array(
                            'alias' => 'TrainingProvider',
                            'table' => 'trainings_providers',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingProvider.id = TrainingCourse.training_provider_id',
                            ),
                        ),
                        array(
                            'alias' => 'Venue',
                            'table' => 'venues',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Venue.id = TrainingPlannedCourse.venue_id',
                            ),
                        ),
                        array(
                            'alias' => 'GarageDistributor',
                            'table' => 'garages_distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageDistributor.garage_id = Garage.id',
                            ),
                        ),
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = GarageDistributor.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'Province',
                            'table' => 'provinces',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Province.id = Garage.province_id',
                            ),
                        ),
                        array(
                            'alias' => 'Country',
                            'table' => 'countries',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Country.id = Province.country_id',
                            ),
                        ),
                    ),
                    'fields' => array(
                        'TrainingPlannedCourse.id',
                        'TrainingPlannedCourse.date_from',
                        'TrainingPlannedCourse.date_to',
                        'TrainingPlannedCourse.status',
                        'TrainingDelegate.id',
                        'TrainingDelegate.is_refund_eligible',
                        'TrainingDelegate.invoice_number',
                        'TrainingDelegate.order_number',
                        'TrainingDelegate.cancelled',
                        'TrainingDelegate.reason_cancelled_id',
                        'TrainingCourse.name',
                        'TrainingCourse.price_credit',
                        'TrainingCourse.cost_training',
                        'GarageContactStaff.*',
                        'Garage.id',
                        'Garage.name',
                        'Garage.g_number_id',
                        'Garage.address1',
                        'Garage.address2',
                        'Garage.address3',
                        'Garage.postcode',
                        'Garage.town',
                        'Province.name',
                        'Country.name',
                        'Contact.*',
                        'Position.*',
                        'TrainingProvider.id',
                        'TrainingProvider.name',
                        'Network.id',
                        'Network.name',
                        'Venue.name',
                        'Distributor.account_number',
                    ),
                    'order' => 'Contact.first_name',
                )
            );

            $config = CakeSession::read('Auth.User.Config');

            $this->set(array(
                'delegates' => $delegates,
                'config' => $config,
                'reason_cancelled_list' => $this->ReasonDelegateCancelled->search_list(),
            ));

            $this->render('/TrainingsListDelegates/Elements/export_excel_delegates');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get count TrainingDelegates.
     */
    public function ajax_count_delegates()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $this->layout = $this->autoRender = false;
            if (!$this->request->is('get')) {
                $user = $this->Acceso->user();
                $aagRegionId = $user['aag_region_id'];

                $searcher = $this->request->data;
                $this->request->data['Search'] = $searcher;

                $conditions = array($this->TrainingDelegate->conditions($searcher), array('TrainingDelegate.network_id IS NOT NULL'));
                $queryType = ConstantsQueryTypes::COUNT;

                $countDelegates = $this->TrainingDelegate->dynamicTypeExportQuery($queryType, $aagRegionId, $conditions);
            }
            return json_encode($countDelegates);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX generate TRainingListDelegates CSV.
     */
    public function ajax_get_csv_data()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $this->layout = $this->autoRender = false;

            set_time_limit(18000);
            ini_set('memory_limit', '3G');

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

                $aagRegionId = $user['aag_region_id'];

                $searcher = $this->request->data;
                $this->request->data['Search'] = $searcher;
                $conditions = array($this->TrainingDelegate->conditions($searcher), array('TrainingDelegate.network_id IS NOT NULL'));
                $queryType = ConstantsQueryTypes::ALL;

                $delegates = $this->TrainingDelegate->dynamicTypeExportQuery($queryType, $aagRegionId, $conditions);
                $this->generateCsv($delegates, $email, $user);
            }
            exit;
        } else {
            throw new UnauthorizedException();
        }
    }

    private function generateCsv($data, $email, $user)
    {
        $languageCode = $user['language_code'];

        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $filename = 'Delegates' . Fecha::getCompleteDate() . '.csv';
        $fileFullName = ConstantsPath::DIR_CSV_EXPORT_FILES_ABSOLUTE . DS . $filename;

        $controller = $this->params['controller'];

        $file = fopen($fileFullName, 'a');
        $table = array(
            __t('Training.Delegate_name', $languageCode),
            __t('Training.Employment_position', $languageCode),
            __t('Training.Network_name', $languageCode),
            __t('Training.Garage_name', $languageCode),
            __t('Training.Training_provider_name', $languageCode),
            __t('Training.Planned_courses', $languageCode),
            __t('Training.Date_from', $languageCode),
            __t('Training.Date_to', $languageCode),
            __t('Distributor.Account_number', $languageCode),
            __t('Delegate.Venue_name', $languageCode),
            __t('Training.Credits', $languageCode),
            __t('Training.Credits_taken', $languageCode),
            __t('Training.Invoice_number', $languageCode),
            __t('Training.Purchase_order_number', $languageCode),
            __t('CRM.Cancelled', $languageCode),
            __t('General.Cancelled_reason', $languageCode)
        );

        fputcsv($file, $table);
        foreach ($data as $key => $delegate) {
            $row = array(
                $delegate['Contact']['first_name'] . ' ' . $delegate['Contact']['last_name'],
                $delegate['Position']['name' . __s()],
                $delegate['Network']['name'],
                $delegate['Garage']['name'] . ' - ' . $delegate['Garage']['g_number_id'],
                $delegate['TrainingProvider']['name'],
                $delegate['TrainingCourse']['name'],
                $delegate['TrainingPlannedCourse']['date_from'],
                $delegate['TrainingPlannedCourse']['date_to'],
                $delegate['Distributor']['account_number'],
                $delegate['Venue']['name'],
                $delegate['TrainingCourse']['price_credit'],
                ($delegate['TrainingDelegate']['is_refund_eligible'] == 1) ? __t('General.Yes') : __t('General.No'),
                $delegate['TrainingDelegate']['invoice_number'],
                $delegate['TrainingDelegate']['order_number'],
                ($delegate['TrainingDelegate']['cancelled'] == 1) ? __t('General.Yes') : __t('General.No'),
                $delegate['TrainingDelegate']['reason_cancelled_id'],
            );

            fputcsv($file, $row);
        }
        fclose($file);

        $this->generateEmail($filename, $fileFullName, $user, $email, $controller);
    }
}
