<?php
class TrainingPlannedCourse extends AppModel
{
    public $useTable = 'trainings_planned_courses';

    public $validate = array(
        'training_course_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_course'
            ),
        ),
        'training_trainer_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_trainer'
            ),
        ),
        'venue_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_venue'
            ),
        ),
        'availability' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_number'
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['TrainingCourse_name'])) {
            $conditions[] = $this->conditionCourseName($fields['TrainingCourse_name']);
        }
        if (!empty($fields['CourseType_name'])) {
            $conditions[] = $this->conditionCourseType($fields['CourseType_name']);
        }
        if (!empty($fields['TrainingProvider_name'])) {
            $conditions[] = $this->conditionTrainingProvider($fields['TrainingProvider_name']);
        }
        if (!empty($fields['TrainingPlannedCourse_date_from'])) {
            $conditions[] = $this->conditionDateFrom($fields['TrainingPlannedCourse_date_from']);
        }
        if (!empty($fields['TrainingPlannedCourse_date_to'])) {
            $conditions[] = $this->conditionDateTo($fields['TrainingPlannedCourse_date_to']);
        }
        if (!empty($fields['TrainingPlannedCourse_duration'])) {
            $conditions[] = $this->conditionDuration($fields['TrainingPlannedCourse_duration']);
        }
        if (!empty($fields['Venue_name'])) {
            $conditions[] = $this->conditionVenueName($fields['Venue_name']);
        }
        if (isset($fields['TrainingPlannedCourse_status']) && $fields['TrainingPlannedCourse_status'] !== '') {
            $conditions[] = $this->conditionStatus($fields['TrainingPlannedCourse_status']);
        }
        if (!empty($fields['TrainingCourse_cost_training'])) {
            $conditions[] = $this->conditionCostTraining($fields['TrainingCourse_cost_training']);
        }
        if (!empty($fields['TrainingCourse_price_credit'])) {
            $conditions[] = $this->conditionPriceCredit($fields['TrainingCourse_price_credit']);
        }
        if (!empty($fields['TrainingPlannedCourse_availability'])) {
            $conditions[] = $this->conditionAvailability($fields['TrainingPlannedCourse_availability']);
        }
        if (!empty($fields['TrainingTrainer_name'])) {
            $conditions[] = $this->conditionTrainingTrainerName($fields['TrainingTrainer_name']);
        }
        if (!empty($fields['active'])) {
            $conditions[] = $this->conditionActive($fields['active']);
        }
        if (!empty($fields['TrainingCourse_part_number'])) {
            $conditions[] = $this->conditionPartNumber($fields['TrainingCourse_part_number']);
        }
        if (!empty($fields['Delegate_name'])) {
            $conditions[] = $this->conditionContactName($fields['Delegate_name']);
        }
        if (!empty($fields['Garage_name'])) {
            $conditions[] = $this->conditionGarageName($fields['Garage_name']);
        }
        if (!empty($fields['Garage_employment_position'])) {
            $conditions[] = $this->conditionPositionName($fields['Garage_employment_position']);
        }
        if (!empty($fields['Delegate_order_number'])) {
            $conditions[] = $this->conditionOrderNumber($fields['Delegate_order_number']);
        }
        if (!empty($fields['Delegate_invoice_number'])) {
            $conditions[] = $this->conditionInvoiceNumber($fields['Delegate_invoice_number']);
        }
        if (!empty($fields['TrainingPlannedCourse_invoice_number'])) {
            $conditions[] = $this->conditionInvoiceNumberPlanned($fields['TrainingPlannedCourse_invoice_number']);
        }
        if (isset($fields['Delegate_cancelled']) && $fields['Delegate_cancelled'] !== '') {
            $conditions[] = $this->conditionCancelled($fields['Delegate_cancelled']);
        }
        if (!empty($fields['Network_name'])) {
            $conditions[] = $this->conditionNetworkName($fields['Network_name']);
        }
        if (!empty($fields['Distributor_account_number'])) {
            $conditions[] = $this->conditionAccountNumber($fields['Distributor_account_number']);
        }
        if (!empty($fields['Delegate_reason_cancelled_id'])) {
            $conditions[] = $this->conditionCancelledReason($fields['Delegate_reason_cancelled_id']);
        }
        if (!empty($fields['is_online'])) {
            $conditions[] = $this->conditionIsOnline($fields['is_online']);
        }
        if (!empty($fields['TrainingPlannedCourse_sales_area_id'])) {
            $conditions[] = $this->conditionSalesArea($fields['TrainingPlannedCourse_sales_area_id']);
        }

        return $conditions;
    }

    private function conditionCourseName($course_name)
    {
        return array('TrainingCourse.id =' => $course_name);
    }

    private function conditionCourseType($course_type)
    {
        return array('TrainingCourse.course_type_id =' => $course_type);
    }

    private function conditionTrainingProvider($training_provider)
    {
        return array('TrainingCourse.training_provider_id =' => $training_provider);
    }

    private function conditionDateFrom($date_from)
    {
        return array('TrainingPlannedCourse.date_from >=' => Fecha::toFormatoBd($date_from));
    }

    private function conditionDateTo($date_to)
    {
        return array('TrainingPlannedCourse.date_to <=' => Fecha::toFormatoBd($date_to));
    }

    private function conditionDuration($duration)
    {
        return array('TrainingPlannedCourse.duration LIKE' => '%' . $duration . '%');
    }

    private function conditionVenueName($venue_name)
    {
        return array('Venue.id =' => $venue_name);
    }

    private function conditionStatus($status)
    {
        if ($status == '1' || $status == '0' || $status == '2') {
            return array('TrainingPlannedCourse.status' => $status);
        }
    }

    private function conditionCostTraining($cost_training)
    {
        return array('TrainingCourse.cost_training <=' => $cost_training);
    }

    private function conditionPriceCredit($price_credit)
    {
        return array('TrainingCourse.price_credit <=' => $price_credit);
    }

    private function conditionAvailability($availability)
    {
        if ($availability == 'Unavailable') {
            return array('TrainingPlannedCourse.status !=' => '1');
        } else {
            return array('TrainingPlannedCourse.status =' => '1');
        }
    }

    private function conditionTrainingTrainerName($name)
    {
        return array('TrainingTrainer.id =' => $name);
    }

    private function conditionActive($active)
    {
        return array('TrainingCourse.active =' => $active);
    }

    private function conditionPartNumber($part_number)
    {
        return array('TrainingCourse.part_number =' => $part_number);
    }

    private function conditionOrderNumber($order_number)
    {
        return array('TrainingDelegate.order_number LIKE' => '%' . $order_number . '%');
    }

    private function conditionInvoiceNumber($invoice_number)
    {
        return array('TrainingDelegate.invoice_number =' => $invoice_number);
    }

    private function conditionGarageName($garage_name)
    {
        return array('Garage.id =' => $garage_name);
    }

    private function conditionContactName($contact_name)
    {
        $contact_name_parts = explode(' ', $contact_name);

        if (count($contact_name_parts) == 2) {
            $conditions = array(
                'OR' => array(
                    array('AND' => array(
                        'Contact.first_name LIKE' => '%' . $contact_name_parts[0] . '%',
                        'Contact.last_name LIKE' => '%' . $contact_name_parts[1] . '%'
                    )),
                    array('Contact.id' => $contact_name)
                )
            );
        } else {
            $conditions = array(
                'OR' => array(
                    array('Contact.first_name LIKE' => '%' . $contact_name . '%'),
                    array('Contact.last_name LIKE' => '%' . $contact_name . '%'),
                    array('Contact.id' => $contact_name)
                )
            );
        }

        return $conditions;
    }

    private function conditionPositionName($position_name)
    {
        return array('Position.id LIKE' => '%' . $position_name . '%');
    }

    private function conditionInvoiceNumberPlanned($invoice_number)
    {
        return array('TrainingPlannedCourse.invoice_number LIKE' => '%' . $invoice_number . '%');
    }

    private function conditionCancelled($cancelled)
    {
        if ($cancelled == ConstantsBooleans::YES || $cancelled == ConstantsBooleans::NO) {
            return array('TrainingDelegate.cancelled' => $cancelled);
        }
    }

    private function conditionNetworkName($network_name)
    {
        return array('Network.id =' => $network_name);
    }

    private function conditionAccountNumber($account_number)
    {
        return array('Distributor.account_number' => $account_number);
    }

    private function conditionCancelledReason($cancelled_reason)
    {
        return array('TrainingDelegate.reason_cancelled_id' => $cancelled_reason);
    }

    private function conditionIsOnline($is_online)
    {
        return array('TrainingCourse.is_online' => $is_online);
    }

    private function conditionSalesArea($sales_area_id)
    {
        return array('Venue.sales_area_id' => $sales_area_id);
    }

    private $queries = array(
        'home' => array(
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
                    'alias' => 'TrainingProvider',
                    'table' => 'trainings_providers',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingProvider.id = TrainingCourse.training_provider_id',
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
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id',
                    ),
                ),
            ),
            'fields' => array(
                'TrainingPlannedCourse.*',
                'Venue.*',
                'TrainingCourse.*',
                'TrainingTrainer.name',
                'CourseType.*',
                'TrainingProvider.name',
                'TrainingProvider.id'
            ),
            'group' => 'TrainingPlannedCourse.id',
            'order' => 'TrainingCourse.name asc',
        ),
    );

    public function _query($index)
    {
        return $this->queries[$index];
    }

    public function add($trainings_planned_courses)
    {
        $fields = array(
            'TrainingPlannedCourse' => array(
                'training_course_id',
                'training_trainer_id',
                'venue_id',
                'date_from',
                'date_to',
                'starting_time',
                'duration',
                'availability',
                'status',
                'invoice_number',
                'note',
                'creation_date'
            )
        );

        $trainings_planned_courses['TrainingPlannedCourse']['date_from'] = Fecha::toFormatoBd($trainings_planned_courses['TrainingPlannedCourse']['date_from']);
        $trainings_planned_courses['TrainingPlannedCourse']['date_to'] = Fecha::toFormatoBd($trainings_planned_courses['TrainingPlannedCourse']['date_to']);
        $trainings_planned_courses['TrainingPlannedCourse']['status'] = ConstantsPlannnedCourseStatus::ACTIVE;
        $trainings_planned_courses['TrainingPlannedCourse']['creation_date'] = date('Y-m-d H:i:s');
        $this->create();

        $planned_courses_bd = $this->guardar($trainings_planned_courses, $fields);
        if (!$planned_courses_bd) {
            return false;
        }
        $this->commit();
        return $planned_courses_bd;
    }

    public function edit($trainings_planned_courses)
    {
        $fields = array(
            'TrainingPlannedCourse' => array(
                'training_course_id',
                'training_trainer_id',
                'venue_id',
                'date_from',
                'date_to',
                'starting_time',
                'duration',
                'availability',
                'status',
                'invoice_number',
                'note',
                'modification_date'
            )
        );

        $trainings_planned_courses['TrainingPlannedCourse']['date_from'] = Fecha::toFormatoBd($trainings_planned_courses['TrainingPlannedCourse']['date_from']);
        $trainings_planned_courses['TrainingPlannedCourse']['date_to'] = Fecha::toFormatoBd($trainings_planned_courses['TrainingPlannedCourse']['date_to']);
        $trainings_planned_courses['TrainingPlannedCourse']['modification_date'] = date('Y-m-d H:i:s');

        $planned_courses_bd = $this->guardar($trainings_planned_courses, $fields);
        if (!$planned_courses_bd) {
            return false;
        }
        $this->commit();
        return $planned_courses_bd;
    }

    public function toggleStatusCancel($training_planned_course_id)
    {
        $fields = array(
            'TrainingPlannedCourse' => array(
                'status',
                'modification_date'
            )
        );
        $training_planned_course_bd = $this->findById($training_planned_course_id);

        if ($training_planned_course_bd['TrainingPlannedCourse']['status'] != ConstantsPlannnedCourseStatus::CANCELED) {
            $training_planned_course_bd['TrainingPlannedCourse']['status'] = ConstantsPlannnedCourseStatus::CANCELED;
        }

        $training_planned_course_bd['TrainingPlannedCourse']['modification_date'] = date('Y-m-d H:i:s');

        if (!$this->guardar($training_planned_course_bd, $fields)) {
            return false;
        }
        $this->commit();
        return true;
    }

    public function toggleStatusActive($training_planned_course_id)
    {
        $fields = array(
            'TrainingPlannedCourse' => array(
                'status',
                'modification_date'
            )
        );
        $training_planned_course_bd = $this->findById($training_planned_course_id);

        if ($training_planned_course_bd['TrainingPlannedCourse']['status'] != ConstantsPlannnedCourseStatus::ACTIVE) {
            $training_planned_course_bd['TrainingPlannedCourse']['status'] = ConstantsPlannnedCourseStatus::ACTIVE;
        }

        $training_planned_course_bd['TrainingPlannedCourse']['modification_date'] = date('Y-m-d H:i:s');

        if (!$this->guardar($training_planned_course_bd, $fields)) {
            return false;
        }
        $this->commit();
        return true;
    }

    public function toggleStatusInactive($training_planned_course_id)
    {
        $fields = array(
            'TrainingPlannedCourse' => array(
                'status',
                'modification_date'
            )
        );
        $training_planned_course_bd = $this->findById($training_planned_course_id);

        if ($training_planned_course_bd['TrainingPlannedCourse']['status'] != ConstantsPlannnedCourseStatus::INACTIVE) {
            $training_planned_course_bd['TrainingPlannedCourse']['status'] = ConstantsPlannnedCourseStatus::INACTIVE;
        }

        $training_planned_course_bd['TrainingPlannedCourse']['modification_date'] = date('Y-m-d H:i:s');

        if (!$this->guardar($training_planned_course_bd, $fields)) {
            return false;
        }
        $this->commit();
        return true;
    }

    public function getPlannedCourseFromTrainingId($training_course_id, $search)
    {
        $conditions = $this->conditions($search);
        $conditions['TrainingPlannedCourse.training_course_id'] = $training_course_id;

        return
            $this->find(
                'all',
                array(
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
                            'alias' => 'TrainingProvider',
                            'table' => 'trainings_providers',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingProvider.id = TrainingCourse.training_provider_id',
                            ),
                        ),
                    ),
                    'conditions' => $conditions,
                    'fields' => array(
                        'TrainingPlannedCourse.*',
                        'Venue.name',
                        'TrainingCourse.*',
                        'TrainingTrainer.name',
                        'CourseType.*',
                        'TrainingProvider.name'
                    ),
                    'order' => 'TrainingCourse.name',
                )
            );
    }

    public function findTrainingCourseId($training_planned_course_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'TrainingPlannedCourse.id' => $training_planned_course_id,
                ),
                'fields' => array(
                    'TrainingPlannedCourse.training_course_id',
                ),
            )
        );
    }

    public function getByPlannedCourseId($planned_course_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'TrainingPlannedCourse.id' => $planned_course_id,
                ),
            )
        );
    }

    public function dynamicTypeExportQuery($type, $aag_region_id, $conditions)
    {
        $status = Configure::read('Network_Status');
        $conditionStatus = '';
        foreach ($status as $statusKey => $statusValue) {
            $conditionStatus .= "WHEN GarageNetwork.status = $statusKey THEN '" . __t($statusValue) . "' ";
        }

        return $this->find($type, array(
            'conditions' => array(
                $conditions,
                'Contact.aag_region_id' => $aag_region_id,
                'Network.id' => array(NETWORK_ID_AUTOCARE, NETWORK_ID_TOPTRUCK, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_GEXPERT),
            ),
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
                        'GarageDistributor.garage_id',
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
        ));
    }

    public function getListOfTrainingCoursesIdsAfterPlannedDateOrDateNull($date)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'OR' => array(
                        'TrainingPlannedCourse.date_to >=' => $date,
                        'TrainingPlannedCourse.date_to IS NULL'
                    )
                ),
                'fields' => array(
                    'TrainingPlannedCourse.training_course_id'
                )
            )
        );
    }

    public function getListOfIdsBeforeDateTo($date)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'TrainingPlannedCourse.date_to <' => $date
                ),
                'fields' => array(
                    'TrainingPlannedCourse.id'
                )
            )
        );
    }
}
