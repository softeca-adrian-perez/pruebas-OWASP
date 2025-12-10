<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class TrainingsCoursesController extends AppController
{
    public $uses = array(
        'TrainingCourse',
        'TrainingProvider',
        'TrainingTrainer',
        'CourseType',
        'TrainingCredit',
        'TrainingPlannedCourse',
        'Venue',
        'TrainingDelegate',
        'Country',
    );

    /**
     * Training Course home page.
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

            $availabilityList = array(
                ConstantsAvailabilityName::UNAVAILABLE => __t('Training.Unavailable'),
                ConstantsAvailabilityName::AVAILABLE => __t('Training.Available'),
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $trainingsCourses = $this->custom_pagination(
                $this->TrainingCourse->_query('home'),
                $this->TrainingCourse->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'TrainingCourse',
                null,
                'PaginatorOrderCustom'
            );

            $isCheckedMyContacts = isset($searcher['active']) && $searcher['active'] == ConstantsBooleans::ACTIVE ? true : false;

            $arrayVenueName = array();
            if (!empty($searcher['Venue_name'])) {
                $arrayVenueName = $this->Venue->getVenuesNameByIdVenue($searcher['Venue_name']);
            }

            $isCheckedOnline = isset($searcher['is_online']) && $searcher['is_online'] == ConstantsBooleans::ACTIVE ? true : false;

            $this->set(array(
                'trainings_courses' => $trainingsCourses,
                'course_type_list' => $this->CourseType->getListOfData(),
                'training_provider_list' => $this->TrainingProvider->searchList(),
                'status_list' => $statusList,
                'availability_list' => $availabilityList,
                'max_value_price' => $this->TrainingCourse->checkMaxValuePrice(),
                'max_cost_training' => $this->TrainingCourse->checkMaxValueCost(),
                'price_credit_search' => isset($searcher['TrainingCourse_price_credit']) ? $searcher['TrainingCourse_price_credit'] : null,
                'price_cost_training' => isset($searcher['TrainingCourse_cost_training']) ? $searcher['TrainingCourse_cost_training'] : null,
                'is_checked_my_contacts' => $isCheckedMyContacts,
                'course_list' => $this->TrainingCourse->searchList(),
                'array_venue_name' => $arrayVenueName,
                'is_checked_online' => $isCheckedOnline
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create TrainingCourse.
     */
    public function add()
    {
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            if (!$this->request->is('get')) {
                if ($this->TrainingCourse->add($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'trainings_courses',
                            'action' => 'edit',
                            $this->TrainingCourse->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }
            $this->setVarForm();
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit TrainingCourse.
     */
    public function edit($training_course_id)
    {
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $course = $this->TrainingCourse->findById($training_course_id);

            if (!$course) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_COURSE));
                $this->redirect(
                    array(
                        'controller' => 'trainings_courses',
                        'action' => 'home',
                    )
                );
            }

            if ($this->request->is('get')) {
                $this->request->data = $course;
            } else {
                $this->request->data['User']['id'] = $training_course_id;
                if ($this->TrainingCourse->edit($this->request->data)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'training_course_id' => $training_course_id,
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

        $is_online = array(
            ConstantsBooleans::NO => __t('General.No'),
            ConstantsBooleans::YES => __t('General.Yes')
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
            'trainings_providers' => $this->TrainingProvider->searchList(),
            'courses_types' => $this->CourseType->search_list(),
            'credits' => $this->TrainingCredit->checkBD(),
            'country' => $this->Country->findById(ConstantsCountries::UNITED_KINGDOM),
            'is_online' => $is_online,
        ));
    }

    /**
     * AJAX charge TrainingPlannedCouse.
     */
    public function ajax_charge_training_planned_courses($training_course_id)
    {
        $this->verify_ajax($this->request);
        $result = $this->TrainingPlannedCourse->getPlannedCourseFromTrainingId($training_course_id, $this->request->data);

        if (
            $result &&
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $this->autoRender = false;

            $arrayBd = array();
            foreach ($result as $key => $value) {
                $arrayBd[$key]['id'] = $value['TrainingPlannedCourse']['id'];
                $arrayBd[$key]['training_course_id'] = $value['TrainingCourse']['name'];
                $arrayBd[$key]['training_provider'] = $value['TrainingProvider']['name'];
                $arrayBd[$key]['venue_id'] = $value['Venue']['name'];
                $arrayBd[$key]['date_from'] = Fecha::toFormatoVista($value['TrainingPlannedCourse']['date_from']);
                $arrayBd[$key]['date_to'] = Fecha::toFormatoVista($value['TrainingPlannedCourse']['date_to']);
                $arrayBd[$key]['starting_time'] = $value['TrainingPlannedCourse']['starting_time'];
                $arrayBd[$key]['duration'] = $value['TrainingPlannedCourse']['duration'];
                $arrayBd[$key]['availability'] = $value['TrainingPlannedCourse']['availability'];
                $arrayBd[$key]['status'] = $value['TrainingPlannedCourse']['status'];
                $arrayBd[$key]['cost_training'] = $value['TrainingCourse']['cost_training'];
                $arrayBd[$key]['price_credit'] = $value['TrainingCourse']['price_credit'];
                $arrayBd[$key]['training_course_id'] = $value['TrainingCourse']['id'];
                $arrayBd[$key]['count_available'] = $this->TrainingDelegate->getNumberOfDelegatesAvailable($value['TrainingPlannedCourse']['id']);
                $arrayBd[$key]['availability'] = $value['TrainingPlannedCourse']['availability'];
                $arrayBd[$key]['part_number'] = $value['TrainingCourse']['part_number'];
                $arrayBd[$key]['invoice_number'] = $value['TrainingPlannedCourse']['invoice_number'];
            }
            return json_encode($arrayBd);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Training courses Excel generation.
     */
    public function training_courses_excel()
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
            $query = $this->TrainingCourse->_query('home');
            $query['conditions'] = $this->TrainingCourse->conditions($searcher);

            $courses = $this->TrainingCourse->find(
                'all',
                $query
            );

            $config = CakeSession::read('Auth.User.Config');

            $this->set(array(
                'courses' => $courses,
                'config' => $config,
            ));

            set_time_limit(18000);
            ini_set('memory_limit', '-1');

            $this->render('/TrainingsCourses/Elements/export_excel_training_courses');
            $this->response->type('xlsx');
            $this->layout = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX change TrainingCourse status to inactive.
     */
    public function ajax_toggle_status_active()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $this->autoRender = false;
            return $this->TrainingCourse->toggleStatusInactive($this->request->data['course_id']);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX change TrainingCourse status to active.
     */
    public function ajax_toggle_status_inactive()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
        ) {
            $this->autoRender = false;
            return $this->TrainingCourse->toggleStatusActive($this->request->data['course_id']);
        } else {
            throw new UnauthorizedException();
        }
    }
}
