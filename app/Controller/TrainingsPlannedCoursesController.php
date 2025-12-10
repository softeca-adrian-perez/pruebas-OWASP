<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class TrainingsPlannedCoursesController extends AppController
{
    public $uses = array(
        'TrainingPlannedCourse',
        'TrainingCourse',
        'TrainingProvider',
        'TrainingTrainer',
        'CourseType',
        'TrainingCredit',
        'Venue',
        'TrainingDelegate',
        'ReasonDelegateCancelled',
		'SalesArea'
    );

    /**
     * TrainingPlannedCourse home page.
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
            $statusList = array(
                ConstantsPlannnedCourseStatus::INACTIVE => __t('Training.Inactive'),
                ConstantsPlannnedCourseStatus::ACTIVE => __t('Training.Active'),
                ConstantsPlannnedCourseStatus::CANCELED => __t('Training.Canceled')
            );

            $avalaibilityList = array(
                ConstantsAvailabilityName::UNAVAILABLE => __t('Training.Unavailable'),
                ConstantsAvailabilityName::AVAILABLE => __t('Training.Available'),
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            if (!isset($searcher['TrainingPlannedCourse_date_from'])) {
                $searcher['TrainingPlannedCourse_date_from'] = Fecha::toFormatoVistaFecha(date("Y-m-d"));
            }

            $trainingPlannedCourses = $this->custom_pagination(
                $this->TrainingPlannedCourse->_query('home'),
                $this->TrainingPlannedCourse->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'TrainingPlannedCourse',
                null,
                'PaginatorOrderCustom'
            );

            $countAvailable = [];
            foreach ($trainingPlannedCourses as $trainingPlannedCourse) {
                $countAvailable[$trainingPlannedCourse['TrainingPlannedCourse']['id']] = $this->TrainingDelegate->getNumberOfDelegatesAvailable($trainingPlannedCourse['TrainingPlannedCourse']['id']);
            }

            $arrayValueName = array();
            if (!empty($searcher['Venue_name'])) {
                $arrayValueName = $this->Venue->getVenuesNameByIdVenue($searcher['Venue_name']);
            }

            $isCheckedOnline = isset($searcher['is_online']) && $searcher['is_online'] == ConstantsBooleans::ACTIVE ? true : false;

            $this->set(array(
                'trainings_planned_courses' => $trainingPlannedCourses,
                'course_type_list' => $this->CourseType->getListOfData(),
                'training_provider_list' => $this->TrainingProvider->searchList(),
                'status_list' => $statusList,
                'availability_list' => $avalaibilityList,
                'max_value_price' => $this->TrainingCourse->checkMaxValuePrice(),
                'price_credit_search' => isset($searcher['TrainingCourse_price_credit']) ? $searcher['TrainingCourse_price_credit'] : null,
                'count_available' => $countAvailable,
                'date_from_today' => isset($searcher['TrainingPlannedCourse_date_from']) ? Fecha::toFormatoVistaFecha($searcher['TrainingPlannedCourse_date_from']) : null,
                'course_list' => $this->TrainingCourse->searchList(),
                'max_cost_training' => $this->TrainingCourse->checkMaxValueCost(),
                'price_cost_training' => isset($searcher['TrainingCourse_cost_training']) ? $searcher['TrainingCourse_cost_training'] : null,
                'array_venue_name' => $arrayValueName,
                'is_checked_online' => $isCheckedOnline,
				'sales_area' => $this->SalesArea->searchListByRegion(CakeSession::read('Auth.User.aag_region_id'))
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create TrainingPlannedCourse.
     */
    public function add($course_id = null)
    {
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            if (!$this->request->is('get')) {
                if ($this->TrainingPlannedCourse->add($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'trainings_planned_courses',
                            'action' => 'edit',
                            $this->TrainingPlannedCourse->getLastInsertID()
                        )
                    );
                } else {
                    $venue = isset($this->request->data['TrainingPlannedCourse']['venue_id']) ? $this->Venue->findById($this->request->data['TrainingPlannedCourse']['venue_id']) : '';
                    $this->set(array(
                        'venue_data' => $venue,
                    ));
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                if ($course_id) {
                    $course = $this->TrainingPlannedCourse->findById($course_id);
                    $training_course_id = isset($course) ? $course_id : null;
                }
            }
            $this->setVarForm();
            $this->set(array(
                'training_course_id' => isset($training_course_id) ? $training_course_id : null,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit TrainingPlannedCourse.
     */
    public function edit($training_planned_course_id)
    {
        $trainingPlannedCourse = $this->TrainingPlannedCourse->findById($training_planned_course_id);
        if (
            $trainingPlannedCourse &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {

            if (!$trainingPlannedCourse) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_PLANNED_COURSE));
                $this->redirect(
                    array(
                        'controller' => 'trainings_courses',
                        'action' => 'home',
                    )
                );
            }

            if ($this->request->is('get')) {
                $trainingPlannedCourse['TrainingPlannedCourse']['date_from'] = Fecha::toFormatoVistaFecha($trainingPlannedCourse['TrainingPlannedCourse']['date_from']);
                $trainingPlannedCourse['TrainingPlannedCourse']['date_to'] = Fecha::toFormatoVistaFecha($trainingPlannedCourse['TrainingPlannedCourse']['date_to']);
                $availabilityValue = $trainingPlannedCourse['TrainingPlannedCourse']['availability'];
                $this->request->data = $trainingPlannedCourse;
            } else {
                $this->request->data['User']['id'] = $training_planned_course_id;
                if ($this->TrainingPlannedCourse->edit($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'training_planned_course_id' => $training_planned_course_id,
                'course_type' => $this->TrainingCourse->getTrainingCourseTypeById($trainingPlannedCourse['TrainingPlannedCourse']['training_course_id']),
                'venue_data' => $this->Venue->findById($trainingPlannedCourse['TrainingPlannedCourse']['venue_id']),
                'array_trainer_name' => $this->TrainingTrainer->getTrainersNameByIdTrainer($trainingPlannedCourse['TrainingPlannedCourse']['training_trainer_id']),
                'availabilityValue' => isset($availabilityValue) ? $availabilityValue : null,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm()
    {
        $active = array(
            ConstantsBooleans::NO => __t('General.No_active'),
            ConstantsBooleans::YES => __t('General.Active')
        );

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'trainings_courses',
                'action' => 'home',
            ),
        );
        $this->set(array(
            'cancel_action' => $cancelAction,
            'active' => $active,
            'trainings_courses' => $this->TrainingCourse->searchListActive(),
            'trainings_trainers' => $this->TrainingTrainer->searchList(),
            'venues' => $this->Venue->search_list(),
        ));
    }

    /**
     * AJAX get TrainingCourse.
     */
    public function ajax_select_course()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $this->autoRender = false;
            return json_encode($this->TrainingCourse->getTrainingCourseTypeById($this->request->data['id']));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get TrainingTrainer.
     */
    public function ajax_select_provider()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $this->autoRender = false;
            return json_encode($this->TrainingTrainer->getTrainingTrainerByTrainingProviderId($this->request->data['name_provider']));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get Venues.
     */
    public function ajax_select_venue()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            $this->autoRender = false;
            return json_encode($this->Venue->findById($this->request->data['id']));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX change TeainingPlannedCourse status to cancel.
     */
    public function ajax_toggle_status_cancel($training_planned_course_id)
    {
        $this->verify_ajax($this->request);
        $trainingPlannedCourse = $this->TrainingPlannedCourse->findById($training_planned_course_id, 'id');

        if (
            $trainingPlannedCourse &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $this->autoRender = false;
            return $this->TrainingPlannedCourse->toggleStatusCancel($training_planned_course_id);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX change TeainingPlannedCourse status to inactive.
     */
    public function ajax_toggle_status_inactive($training_planned_course_id)
    {
        $this->verify_ajax($this->request);
        $trainingPlannedCourse = $this->TrainingPlannedCourse->findById($training_planned_course_id, 'id');

        if (
            $trainingPlannedCourse &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $this->autoRender = false;
            return $this->TrainingPlannedCourse->toggleStatusInactive($training_planned_course_id);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX change TeainingPlannedCourse status to active.
     */
    public function ajax_toggle_status_active($training_planned_course_id)
    {
        $this->verify_ajax($this->request);
        $trainingPlannedCourse = $this->TrainingPlannedCourse->findById($training_planned_course_id, 'id');

        if (
            $trainingPlannedCourse &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $this->autoRender = false;
            return $this->TrainingPlannedCourse->toggleStatusActive($training_planned_course_id);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * TrainingPlannedCourse Excel generation.
     */
    public function training_planned_courses_excel()
    {
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $courses = $this->TrainingPlannedCourse->find(
                'all',
                array(
                    'conditions' => $this->TrainingPlannedCourse->conditions($searcher),
                    'joins' => array(
                        array(
                            'alias' => 'Venue',
                            'table' => 'venues',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Venue.id = TrainingPlannedCourse.venue_id',
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
                            'alias' => 'TrainingTrainer',
                            'table' => 'trainings_trainers',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingTrainer.id = TrainingPlannedCourse.training_trainer_id',
                            ),
                        ),
                        array(
                            'alias' => 'CourseType',
                            'table' => 'courses_types',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'CourseType.id = TrainingCourse.course_type_id',
                            ),
                        ),
                        array(
                            'alias' => 'TrainingDelegate',
                            'table' => 'trainings_delegates',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingDelegate.training_planned_course_id = TrainingPlannedCourse.id',
                            ),
                        ),
                    ),
                    'fields' => array(
                        'TrainingPlannedCourse.status',
                        'TrainingPlannedCourse.date_from',
                        'TrainingPlannedCourse.date_to',
                        'TrainingPlannedCourse.duration',
                        'TrainingPlannedCourse.availability',
                        'TrainingPlannedCourse.invoice_number',
                        'Venue.name',
						'Venue.sales_area_id',
                        'Venue.address_1',
                        'Venue.address_2',
                        'Venue.town',
                        'Venue.post_code',
                        'TrainingCourse.name',
                        'TrainingCourse.price_credit',
                        'TrainingCourse.cost_training',
                        'TrainingTrainer.name',
                        'CourseType.name_' . __l(),
                        '(SELECT COUNT(*) FROM trainings_delegates
                        AS TrainingDelegate
                        WHERE TrainingDelegate.training_planned_course_id = TrainingPlannedCourse.id
                        AND TrainingDelegate.cancelled != ' . ConstantsBooleans::ACTIVE . ')
                        AS delegates_num',
                        'COUNT(TrainingDelegate.id) AS delegates_num_all',
                    ),
                    'order' => 'TrainingCourse.name',
                    'group' => 'TrainingPlannedCourse.id'
                )
            );

            $config = CakeSession::read('Auth.User.Config');

            $this->set(array(
                'courses' => $courses,
                'config' => $config,
				'sales_area' => $this->SalesArea->searchListByRegion(CakeSession::read('Auth.User.aag_region_id'))
            ));

            $this->render('/TrainingsPlannedCourses/Elements/export_excel_courses');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * General training Excel generation.
     */
    public function general_training_excel()
    {
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
            )
        ) {
            set_time_limit(18000);
            ini_set('memory_limit', '3G');

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $status = Configure::read('Network_Status');
            $conditionStatus = '';
            foreach ($status as $statusKey => $statusValue) {
                $conditionStatus .= "WHEN GarageNetwork.status = $statusKey THEN '" . __t($statusValue) . "' ";
            }

            $courses = $this->TrainingPlannedCourse->find(
                'all',
                array(
                    'conditions' => $this->TrainingPlannedCourse->conditions($searcher),
                    'joins' => array(
                        array(
                            'alias' => 'Venue',
                            'table' => 'venues',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Venue.id = TrainingPlannedCourse.venue_id',
                            ),
                            'fields' => array(
                                'Venue.id',
                                'Venue.name',
                                'Venue.address_1',
                                'Venue.post_code',
                                'Venue.town',
                            ),
                        ),
                        array(
                            'alias' => 'TrainingCourse',
                            'table' => 'trainings_courses',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingCourse.id = TrainingPlannedCourse.training_course_id',
                            ),
                            'fields' => array(
                                'TrainingCourse.id',
                                'TrainingCourse.course_type_id',
                                'TrainingCourse.training_provider_id',
                                'TrainingCourse.duration',
                                'TrainingCourse.price',
                                'TrainingCourse.price_credit',
                                'TrainingCourse.cost_training',
                                'TrainingCourse.part_number',
                                'TrainingCourse.invoice_number',
                                'TrainingCourse.name',
                            ),
                        ),
                        array(
                            'alias' => 'TrainingTrainer',
                            'table' => 'trainings_trainers',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingTrainer.id = TrainingPlannedCourse.training_trainer_id',
                            ),
                            'fields' => array(
                                'TrainingTrainer.id',
                                'TrainingTrainer.name',
                                'TrainingTrainer.email',
                                'TrainingTrainer.phone',
                            ),
                        ),
                        array(
                            'alias' => 'CourseType',
                            'table' => 'courses_types',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'CourseType.id = TrainingCourse.course_type_id',
                            ),
                        ),
                        array(
                            'alias' => 'TrainingProvider',
                            'table' => 'trainings_providers',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingProvider.id = TrainingCourse.training_provider_id',
                            ),
                            'fields' => array(
                                'TrainingProvider.id',
                                'TrainingProvider.name',
                                'TrainingProvider.email',
                                'TrainingProvider.phone',
                            ),
                        ),
                        array(
                            'alias' => 'TrainingDelegate',
                            'table' => 'trainings_delegates',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingDelegate.training_planned_course_id = TrainingPlannedCourse.id',
                            ),
                            'fields' => array(
                                'TrainingDelegate.id',
                                'TrainingDelegate.garage_contact_staff_id',
                                'TrainingDelegate.network_id',
                                'TrainingDelegate.order_number',
                                'TrainingDelegate.invoice_number',
                                'TrainingDelegate.is_refund_eligible',
                                'TrainingDelegate.cancelled',
                                'TrainingDelegate.reason_cancelled_id',
                            ),
                        ),
                        array(
                            'alias' => 'GarageContactStaff',
                            'table' => 'garages_contacts_staff',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageContactStaff.id = TrainingDelegate.garage_contact_staff_id',
                            ),
                            'fields' => array(
                                'GarageContactStaff.id',
                                'GarageContactStaff.garage_id',
                                'GarageContactStaff.contact_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageContactStaff.garage_id',
                            ),
                            'fields' => array(
                                'Garage.id',
                                'Garage.province_id',
                                'Garage.name',
                                'Garage.ref_code',
                                'Garage.town',
                                'Garage.postcode',
                                'Garage.address1',
                                'Garage.email',
                                'Garage.phone',
                                'Garage.mobile',
                                'Garage.g_number_id',
                            ),
                        ),
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Network.id = TrainingDelegate.network_id',
                            ),
                            'fields' => array(
                                'Network.id',
                                'Network.name',
                            ),
                        ),
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.garage_id = Garage.id',
                                'GarageNetwork.network_id = Network.id',
                            ),
                            'fields' => array(
                                'GarageNetwork.id',
                                'GarageNetwork.garage_id',
                                'GarageNetwork.network_id',
                                'GarageNetwork.annex_detail_id',
                                'GarageNetwork.status',
                            ),
                        ),
                        array(
                            'alias' => 'AnnexDetail',
                            'table' => 'annex_details',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'AnnexDetail.id = GarageNetwork.annex_detail_id',
                            ),
                        ),
                        array(
                            'alias' => 'Contact',
                            'table' => 'contacts',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Contact.id = GarageContactStaff.contact_id',
                            ),
                            'fields' => array(
                                'Contact.id',
                                'Contact.first_name',
                                'Contact.last_name',
                                'Contact.email',
                                'Contact.phone',
                            ),
                        ),
                        array(
                            'alias' => 'Province',
                            'table' => 'provinces',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Province.id = Garage.province_id',
                            ),
                            'fields' => array(
                                'Province.id',
                                'Province.name',
                            ),
                        ),
                        array(
                            'alias' => 'GarageDistributor',
                            'table' => 'garages_distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageDistributor.garage_id = Garage.id',
                            ),
                            'fields' => array(
                                'GarageDistributor.id',
                                'GarageDistributor.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = GarageDistributor.distributor_id',
                            ),
                            'fields' => array(
                                'Distributor.id',
                                'Distributor.account_number',
                            ),
                        ),
                    ),
                    'fields' => array(
                        'TrainingPlannedCourse.status',
                        'TrainingPlannedCourse.date_from',
                        'TrainingPlannedCourse.date_to',
                        'TrainingPlannedCourse.starting_time',
                        'TrainingPlannedCourse.duration',
                        'TrainingPlannedCourse.availability',
                        'TrainingPlannedCourse.invoice_number',
                        'Venue.name',
                        'Venue.address_1',
                        'Venue.post_code',
                        'Venue.town',
                        'TrainingCourse.duration',
                        'TrainingCourse.price',
                        'TrainingCourse.price_credit',
                        'TrainingCourse.cost_training',
                        'TrainingCourse.part_number',
                        'TrainingCourse.invoice_number',
                        'TrainingCourse.name',
                        'TrainingTrainer.name',
                        'TrainingTrainer.email',
                        'TrainingTrainer.phone',
                        'CourseType.*',
                        'TrainingProvider.name',
                        'TrainingProvider.email',
                        'TrainingProvider.phone',
                        'TrainingDelegate.order_number',
                        'TrainingDelegate.invoice_number',
                        'TrainingDelegate.is_refund_eligible',
                        'TrainingDelegate.cancelled',
                        'TrainingDelegate.reason_cancelled_id',
                        'Garage.name',
                        'Garage.ref_code',
                        'Garage.town',
                        'Garage.postcode',
                        'Garage.address1',
                        'Garage.email',
                        'Garage.phone',
                        'Garage.mobile',
                        'Garage.g_number_id',
                        'Contact.first_name',
                        'Contact.last_name',
                        'Contact.email',
                        'Contact.phone',
                        'Province.name',
                        'Network.name',
                        'Distributor.account_number',
                        'GROUP_CONCAT(DISTINCT CONCAT(Network.name, " - ", IFNULL(AnnexDetail.name_' . __l() . ', ""), "(",
                    CASE
                        ' . $conditionStatus . '
                        ELSE GarageNetwork.status
                    END
                    , ")") SEPARATOR ", ") AS network_concatenated_fields',
                    ),
                    'order' => 'TrainingCourse.name',
                    'group' => array('TrainingPlannedCourse.id', 'TrainingDelegate.id'),

                )
            );

            $config = CakeSession::read('Auth.User.Config');

            $this->set(array(
                'courses' => $courses,
                'config' => $config,
                'user_aag_region_id' => CakeSession::read('Auth.User.aag_region_id'),
                'user_role' => CakeSession::read('Auth.User.role_id'),
                'reason_cancelled_list' => $this->ReasonDelegateCancelled->search_list(),
            ));

            $this->render('/TrainingsPlannedCourses/Elements/export_training_excel');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get TrainingPlannedCourse count.
     */
    public function ajax_count_trainings()
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

                $conditions = array($this->TrainingPlannedCourse->conditions($searcher));
                $queryType = ConstantsQueryTypes::COUNT;

                $countDelegates = $this->TrainingPlannedCourse->dynamicTypeExportQuery($queryType, $aagRegionId, $conditions);
            }
            return json_encode($countDelegates);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX generate TrainingPlannedCourse CSV.
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
                $conditions = array($this->TrainingPlannedCourse->conditions($searcher));
                $queryType = ConstantsQueryTypes::ALL;

                $training = $this->TrainingPlannedCourse->dynamicTypeExportQuery($queryType, $aagRegionId, $conditions);
                $this->generateCsv($training, $email, $user);
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

        $filename = 'Training' . Fecha::getCompleteDate() . '.csv';
        $fileFullName = ConstantsPath::DIR_CSV_EXPORT_FILES_ABSOLUTE . DS . $filename;

        $controller = $this->params['controller'];

        $readonCancelledList = $this->ReasonDelegateCancelled->search_list();

        $file = fopen($fileFullName, 'a');

        $table = array(
            __t('TrainingProvider.Name', $languageCode),
            __t('TrainingProvider.Provider_email', $languageCode),
            __t('TrainingProvider.Provider_phone_number', $languageCode),
            __t('TrainingTrainer.name', $languageCode),
            __t('TrainingTrainer.Trainer_email', $languageCode),
            __t('TrainingTrainer.Trainer_phone', $languageCode),
            __t('Training.Course_type', $languageCode),
            __t('Training.Course_duration', $languageCode),
            __t('Training.Course_price', $languageCode),
            __t('Training.Course_credits', $languageCode),
            __t('Training.Cost_of_course', $languageCode),
            __t('Training.Part_number', $languageCode),
            __t('TrainingCourse.Invoice_number', $languageCode),
            __t('TrainingPlannedCourse.Name', $languageCode),
            __t('Training.Date_from', $languageCode),
            __t('Training.Date_to', $languageCode),
            __t('Event.Start_time', $languageCode),
            __t('Training.Duration', $languageCode),
            __t('Training.Availability', $languageCode),
            __t('TrainingPlannedCourse.Invoice_number', $languageCode),
            __t('Training.Venue', $languageCode),
            __t('Visit.Address', $languageCode),
            __t('Garage.Postcode', $languageCode),
            __t('Garage.Town', $languageCode),
            __t('Training.Delegate_name', $languageCode),
            __t('Training.Delegate_general_detail', $languageCode),
            __t('Training.Phone', $languageCode),
            __t('Training.Purchase_order_number', $languageCode),
            __t('Training.Invoice_number', $languageCode),
            __t('Training.Credits_taken', $languageCode),
            __t('CRM.Cancelled', $languageCode),
            __t('General.Cancelled_reason', $languageCode),
            __t('Training.Network_name', $languageCode),
            __t('Training.Garage_name', $languageCode),
            __t('Garage.Town', $languageCode),
            __t('Garage.Postcode', $languageCode),
            __t('Garage.Province', $languageCode),
            __t('Garage.Garage_address', $languageCode),
            __t('Garage.Garage_email', $languageCode),
            __t('Garage.Garage_phone_number', $languageCode),
            __t('Distributor.Account_number', $languageCode),
            __t('Garage.G_number', $languageCode),
        );

        fputcsv($file, $table);
        foreach ($data as $course) {
            $row = array(
                $course['TrainingProvider']['name'],
                $course['TrainingProvider']['email'],
                $course['TrainingProvider']['phone'],
                $course['TrainingTrainer']['name'],
                $course['TrainingTrainer']['email'],
                $course['TrainingTrainer']['phone'],
                $course['CourseType']['name_' . __l()],
                $course['TrainingCourse']['duration'],
                $course['TrainingCourse']['price'],
                $course['TrainingCourse']['price_credit'],
                $course['TrainingCourse']['cost_training'],
                $course['TrainingCourse']['part_number'],
                $course['TrainingCourse']['invoice_number'],
                $course['TrainingCourse']['name'],
                Fecha::toFormatoVistaFecha($course['TrainingPlannedCourse']['date_from']),
                Fecha::toFormatoVistaFecha($course['TrainingPlannedCourse']['date_to']),
                $course['TrainingPlannedCourse']['starting_time'],
                $course['TrainingPlannedCourse']['duration'],
                $course['TrainingPlannedCourse']['availability'],
                $course['TrainingPlannedCourse']['invoice_number'],
                $course['Venue']['name'],
                $course['Venue']['address_1'],
                $course['Venue']['post_code'],
                $course['Venue']['town'],
                $course['Contact']['first_name'] . ' ' . $course['Contact']['last_name'],
                $course['Contact']['email'],
                $course['Contact']['phone'],
                $course['TrainingDelegate']['order_number'],
                $course['TrainingDelegate']['invoice_number'],
                ($course['TrainingDelegate']['is_refund_eligible'] == ConstantsBooleans::ACTIVE) ? __t('General.Yes') : __t('General.No'),
                $course['TrainingDelegate']['cancelled'] == ConstantsBooleans::YES ? __t('General.Yes') : __t('General.No'),
                isset($course['TrainingDelegate']['reason_cancelled_id']) ? $readonCancelledList[$course['TrainingDelegate']['reason_cancelled_id']] : '',
                $course[0]['network_concatenated_fields'],
                $course['Garage']['name'] . ' - ' . $course['Garage']['ref_code'],
                $course['Garage']['town'],
                $course['Garage']['postcode'],
                $course['Province']['name'],
                $course['Garage']['address1'],
                $course['Garage']['email'],
                isset($course['Garage']['phone']) ? $course['Garage']['phone'] : $course['Garage']['mobile'],
                $course['Distributor']['account_number'],
                $course['Garage']['g_number_id'],
            );

            fputcsv($file, $row);
        }
        fclose($file);

        $this->generateEmail($filename, $fileFullName, $user, $email, $controller);
    }
}
