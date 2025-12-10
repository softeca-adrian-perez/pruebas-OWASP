<?php
class TrainingDelegate extends AppModel
{
    public $useTable = 'trainings_delegates';

    public $validate = array(
        'network_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_network'
            ),
        ),
        'delegate_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_delegate'
            ),
        ),
        'training_planned_course_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_course'
            ),
        ),
        'garage_contact_staff_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_garage'
            ),
            'uniqueCombination' => array(
                'rule' => 'checkUniqueCombination',
                'message' => 'Validation.Garage_contact_staff_id_must_be_unique'
            ),
        ),
        'order_number' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_number',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'invoice_number' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'reason_delegate_id' => array(
            'notBlank' => array(
                'rule' => array('checkValue'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
        ),
    );

    public function checkUniqueCombination($check)
    {
        if ($this->data['TrainingCourse']['is_online']) {
            return true;
        } else {
            $conditions = array(
                'TrainingDelegate.training_planned_course_id' => $this->data[$this->alias]['training_planned_course_id'],
                'TrainingDelegate.garage_contact_staff_id' => $check,
            );

            if (!empty($this->data[$this->alias]['id'])) {
                $conditions['NOT'] = array('TrainingDelegate.id' => $this->data[$this->alias]['id']);
            }

            return $this->find('count', array('conditions' => $conditions)) === 0;
        }
    }

    public function checkValue($check)
    {
        $isRefundEligible = $this->data[$this->alias]['is_refund_eligible'];
        $reasonDelegateId = $this->data[$this->alias]['reason_delegate_id'];
        if ($isRefundEligible === null || $isRefundEligible == 0) {
            $this->validator()->add('reason_delegate_id', 'notBlank', array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_reson'
            ));
            return !empty($isRefundEligible) || !empty($reasonDelegateId);
        } else {
            $this->validator()->remove('reason_delegate_id', 'notBlank');
        }
        return true;
    }

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['TrainingCourse_name'])) {
            $conditions[] = $this->conditionCourseName($fields['TrainingCourse_name']);
        }
        if (!empty($fields['Garage_name'])) {
            $conditions[] = $this->conditionGarageName($fields['Garage_name']);
        }
        if (!empty($fields['Delegate_name'])) {
            $conditions[] = $this->conditionContactName($fields['Delegate_name']);
        }
        if (!empty($fields['Garage_employment_position'])) {
            $conditions[] = $this->conditionPositionName($fields['Garage_employment_position']);
        }
        if (!empty($fields['TrainingPlannedCourse_date_from'])) {
            $conditions[] = $this->conditionDateFrom($fields['TrainingPlannedCourse_date_from']);
        }
        if (!empty($fields['TrainingPlannedCourse_date_to'])) {
            $conditions[] = $this->conditionDateTo($fields['TrainingPlannedCourse_date_to']);
        }
        if (!empty($fields['TrainingCourse_price_credit'])) {
            $conditions[] = $this->conditionPriceCredit($fields['TrainingCourse_price_credit']);
        }
        if (isset($fields['TrainingDelegate_credit_taken']) && $fields['TrainingDelegate_credit_taken'] !== '') {
            $conditions[] = $this->conditionCreditTaken($fields['TrainingDelegate_credit_taken']);
        }
        if (!empty($fields['Delegate_order_number'])) {
            $conditions[] = $this->conditionOrderNumber($fields['Delegate_order_number']);
        }
        if (!empty($fields['Delegate_invoice_number'])) {
            $conditions[] = $this->conditionInvoiceNumber($fields['Delegate_invoice_number']);
        }
        if (!empty($fields['Provider_name'])) {
            $conditions[] = $this->conditionTrainingProvider($fields['Provider_name']);
        }
        if (isset($fields['Delegate_cancelled']) && $fields['Delegate_cancelled'] !== '') {
            $conditions[] = $this->conditionCancelled($fields['Delegate_cancelled']);
        }
        if (!empty($fields['Network_name'])) {
            $conditions[] = $this->conditionNetworkName($fields['Network_name']);
        }
        if (!empty($fields['Delegate_reason_cancelled_id'])) {
            $conditions[] = $this->conditionCancelledReason($fields['Delegate_reason_cancelled_id']);
        }
        if (!empty($fields['Venue_name'])) {
            $conditions[] = $this->conditionVenueName($fields['Venue_name']);
        }
        if (!empty($fields['Distributor_account_number'])) {
            $conditions[] = $this->conditionAccountNumber($fields['Distributor_account_number']);
        }

        return $conditions;
    }

    private function conditionCourseName($course_name)
    {
        return array('TrainingCourse.id LIKE' => '%' . $course_name . '%');
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
                        'Contact.last_name LIKE' => '%' . $contact_name_parts[1] . '%',
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
    private function conditionDateFrom($date_from)
    {
        return array('TrainingPlannedCourse.date_from >=' => Fecha::toFormatoBd($date_from));
    }
    private function conditionDateTo($date_to)
    {
        return array('TrainingPlannedCourse.date_to <=' => Fecha::toFormatoBd($date_to));
    }
    private function conditionPriceCredit($price_credit)
    {
        return array('TrainingCourse.price_credit <=' => $price_credit);
    }
    private function conditionCreditTaken($credit_taken)
    {
        if ($credit_taken == '1' || $credit_taken == '0') {
            return array('TrainingDelegate.is_refund_eligible' => $credit_taken);
        }
    }
    private function conditionOrderNumber($order_number)
    {
        return array('TrainingDelegate.order_number LIKE' => '%' . $order_number . '%');
    }
    private function conditionInvoiceNumber($invoice_number)
    {
        return array('TrainingDelegate.invoice_number =' => $invoice_number);
    }
    private function conditionTrainingProvider($training_provider)
    {
        return array('TrainingProvider.id =' => $training_provider);
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
    private function conditionCancelledReason($cancelled_reason)
    {
        return array('TrainingDelegate.reason_cancelled_id' => $cancelled_reason);
    }
    private function conditionVenueName($venue_name)
    {
        return array('Venue.id =' => $venue_name);
    }
    private function conditionAccountNumber($account_number)
    {
        return array('Distributor.account_number' => $account_number);
    }

    private $queries = array(
        'home' => array(
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
            ),
            'fields' => array(
                'TrainingPlannedCourse.*',
                'TrainingDelegate.*',
                'TrainingCourse.*',
                'GarageContactStaff.*',
                'Garage.*',
                'Contact.*',
                'Position.*',
                'Network.*',
            ),
            'order' => 'Contact.first_name',
        ),
        'home_list' => array(
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
            ),
            'conditions' => array(
                'Contact.aag_region_id' => ConstantsAAGRegionId::UK,
                'Network.id' => array(NETWORK_ID_AUTOCARE, NETWORK_ID_TOPTRUCK, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_GEXPERT)
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
                'TrainingCourse.id',
                'TrainingCourse.name',
                'TrainingCourse.price_credit',
                'GarageContactStaff.*',
                'Garage.id',
                'Garage.name',
                'Garage.g_number_id',
                'Contact.*',
                'Position.*',
                'TrainingProvider.id',
                'TrainingProvider.name',
                'Network.id',
                'Network.name',
                'Venue.name',
                'Distributor.account_number',
            ),
            'order' => 'Contact.first_name asc',
        ),
    );

    public function _query($index, $training_planned_course_id)
    {
        $query = $this->queries[$index];
        if ($index === 'home' && $training_planned_course_id) {
            $query['conditions']['TrainingPlannedCourse.id'] = $training_planned_course_id;
        }

        return $query;
    }

    public function add($trainings_planned_courses, $staff_id)
    {
        $fields = array(
            'TrainingDelegate' => array(
                'training_planned_course_id',
                'garage_contact_staff_id',
                'is_refund_eligible',
                'reason_delegate_id',
                'order_number',
                'invoice_number',
                'network_id',
                'creation_date'
            )
        );
        $trainings_delegates['TrainingDelegate']['training_planned_course_id'] = $trainings_planned_courses['training_planned_course_id'];
        $trainings_delegates['TrainingDelegate']['garage_contact_staff_id'] =  $staff_id;
        $trainings_delegates['TrainingDelegate']['reason_delegate_id'] = isset($trainings_planned_courses['reason_delegate_id']) ? $trainings_planned_courses['reason_delegate_id'] : null;
        if ($trainings_planned_courses['add_credits_input'] == 'accept') {
            $trainings_delegates['TrainingDelegate']['is_refund_eligible'] = ConstantsBooleans::YES;
        }
        $trainings_delegates['TrainingDelegate']['order_number'] = $trainings_planned_courses['TrainingDelegate']['order_number'];
        $trainings_delegates['TrainingDelegate']['invoice_number'] = $trainings_planned_courses['TrainingDelegate']['invoice_number'];
        $trainings_delegates['TrainingDelegate']['network_id'] = $trainings_planned_courses['TrainingDelegate']['network_id'];

        $trainings_delegates['TrainingCourse']['is_online'] = $trainings_planned_courses['TrainingCourse']['TrainingCourse']['is_online'];

        $trainings_delegates['TrainingCourse']['creation_date'] = date('Y-m-d H:i:s');

        $this->create();
        $trainings_delegates_bd = $this->guardar($trainings_delegates, $fields);
        if (!$trainings_delegates_bd) {
            return false;
        }
        $this->commit();
        return $trainings_delegates_bd;
    }

    public function edit($trainings_planned_courses, $staff_id)
    {
        $fields = array(
            'TrainingDelegate' => array(
                'id',
                'training_planned_course_id',
                'garage_contact_staff_id',
                'order_number',
                'invoice_number',
                'network_id',
                'is_refund_eligible',
                'reason_delegate_id',
                'modification_date'
            )
        );

        $trainings_delegates['TrainingDelegate']['id'] = $trainings_planned_courses['TrainingDelegate']['id'];
        $trainings_delegates['TrainingDelegate']['training_planned_course_id'] = $trainings_planned_courses['training_planned_course_id'];
        $trainings_delegates['TrainingDelegate']['garage_contact_staff_id'] =  $staff_id;
        $trainings_delegates['TrainingDelegate']['order_number'] = $trainings_planned_courses['TrainingDelegate']['order_number'];
        $trainings_delegates['TrainingDelegate']['invoice_number'] = $trainings_planned_courses['TrainingDelegate']['invoice_number'];
        $trainings_delegates['TrainingDelegate']['network_id'] = $trainings_planned_courses['TrainingDelegate']['network_id'];
        $trainings_delegates['TrainingDelegate']['is_refund_eligible'] = $trainings_planned_courses['TrainingDelegate']['is_refund_eligible'];
        if ($trainings_planned_courses['TrainingDelegate']['is_refund_eligible'] == 1) {
            $trainings_delegates['TrainingDelegate']['reason_delegate_id'] = null;
        } else {
            $trainings_delegates['TrainingDelegate']['reason_delegate_id'] = $trainings_planned_courses['TrainingDelegate']['reason_delegate_id'];
        }

        $trainings_delegates['TrainingCourse']['is_online'] = $trainings_planned_courses['TrainingCourse']['TrainingCourse']['is_online'];

        $trainings_delegates['TrainingCourse']['modification_date'] = date('Y-m-d H:i:s');

        $trainings_delegates_bd = $this->guardar($trainings_delegates, $fields);
        if (!$trainings_delegates_bd) {
            return false;
        }
        $this->commit();
        return $trainings_delegates_bd;
    }

    public function getPlannedCourseFromTrainingId($training_course_id)
    {
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
                                'TrainingCourse.id = TrainingPlannedCourse.training_course_id'
                            )
                        ),
                        array(
                            'alias' => 'TrainingTrainer',
                            'table' => 'trainings_trainers',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingTrainer.id = TrainingPlannedCourse.training_trainer_id'
                            )
                        ),
                        array(
                            'alias' => 'CourseType',
                            'table' => 'courses_types',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'CourseType.id = TrainingCourse.course_type_id'
                            )
                        ),
                        array(
                            'alias' => 'TrainingProvider',
                            'table' => 'trainings_providers',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingProvider.id = TrainingCourse.training_provider_id'
                            )
                        )
                    ),
                    'conditions' => array(
                        'TrainingPlannedCourse.training_course_id' => $training_course_id
                    ),
                    'fields' => array(
                        'TrainingPlannedCourse.*',
                        'Venue.name',
                        'TrainingCourse.*',
                        'TrainingTrainer.name',
                        'CourseType.*',
                        'TrainingProvider.name'
                    ),
                    'order' => 'TrainingCourse.name'
                )
            );
    }

    public function getNumberOfDelegatesAvailable($training_planned_course_id)
    {
        return $this->find('count', array(
            'conditions' => array(
                'TrainingDelegate.training_planned_course_id' => $training_planned_course_id,
                'TrainingDelegate.cancelled' => ConstantsBooleans::NO_ACTIVE
            )
        ));
    }

    public function getDelegatesFromPlannedCourseId($training_planned_course_id)
    {
        return
            $this->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'TrainingPlannedCourse',
                            'table' => 'trainings_planned_courses',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingPlannedCourse.id = TrainingDelegate.training_planned_course_id'
                            )
                        ),
                        array(
                            'alias' => 'TrainingCourse',
                            'table' => 'trainings_courses',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TrainingCourse.id = TrainingPlannedCourse.training_course_id'
                            )
                        )
                    ),
                    'conditions' => array(
                        'TrainingDelegate.training_planned_course_id' => $training_planned_course_id,
                        'TrainingDelegate.cancelled !=' => ConstantsBooleans::ACTIVE
                    ),
                    'fields' => array(
                        'TrainingDelegate.*',
                        'TrainingPlannedCourse.*',
                        'TrainingCourse.*'
                    )
                )
            );
    }

    public function cancelDelegate($delegate, $reason_cancelled_id)
    {
        $fields = array(
            'TrainingDelegate' => array(
                'cancelled',
                'reason_cancelled_id',
                'is_refund_eligible',
                'modification_date'
            )
        );

        $delegate['TrainingDelegate']['cancelled'] = ConstantsBooleans::ACTIVE;
        $delegate['TrainingDelegate']['reason_cancelled_id'] = $reason_cancelled_id;
        $delegate['TrainingDelegate']['is_refund_eligible'] = ConstantsBooleans::NO;
        $delegate['TrainingDelegate']['modification_date'] = date('Y-m-d H:i:s');

        $delegate_bd = $this->guardar($delegate, $fields);
        if (!$delegate_bd) {
            return false;
        }
        $this->commit();
        return $delegate_bd;
    }

    public function toggle_status($delegate_data, $substract = null)
    {
        $fields = array(
            'TrainingDelegate' => array(
                'cancelled',
                'reason_cancelled_id',
                'reason_delegate_id',
                'is_refund_eligible',
                'modification_date'
            )
        );

        if ($delegate_data['TrainingDelegate']['cancelled'] == ConstantsBooleans::YES) {
            $delegate_data['TrainingDelegate']['cancelled'] = ConstantsBooleans::NO;
            $delegate_data['TrainingDelegate']['reason_cancelled_id'] = null;
            $delegate_data['TrainingDelegate']['reason_delegate_id'] = ConstantsReasonDelegate::UNCANCELED;
        } else {
            $delegate_data['TrainingDelegate']['cancelled'] = ConstantsBooleans::YES;
        }

        // has to be string the 'true'
        if ($substract == 'true') {
            $delegate_data['TrainingDelegate']['is_refund_eligible'] = ConstantsBooleans::YES;
        }

        $delegate_data['TrainingDelegate']['modification_date'] = date('Y-m-d H:i:s');

        if (!$this->guardar($delegate_data, $fields)) {
            return false;
        }
        $this->commit();
        return true;
    }

    public function cancelDelegateGiven($delegate)
    {
        $fields = array(
            'TrainingDelegate' => array(
                'cancelled',
                'reason_cancelled_id',
                'is_refund_eligible',
                'modification_date'
            )
        );
        $delegate['TrainingDelegate']['cancelled'] = ConstantsBooleans::ACTIVE;
        $delegate['TrainingDelegate']['reason_cancelled_id'] = ConstantsReasonCancelled::COURSE_CANCELLED;
        $delegate['TrainingDelegate']['is_refund_eligible'] = ConstantsBooleans::NO;
        $delegate['TrainingDelegate']['modification_date'] = date('Y-m-d H:i:s');

        $delegate_bd = $this->guardar($delegate, $fields);
        if (!$delegate_bd) {
            return false;
        }
        $this->commit();
        return $delegate_bd;
    }

    /**
     * When exporting delegates, the maximum number of delegates allowed is checked.
     * When maximum is reached, delegates are obtained.
     */
    public function dynamicTypeExportQuery($type, $aag_region_id, $conditions)
    {
        return $this->find($type, array(
            'conditions' => array(
                $conditions,
                'Contact.aag_region_id' => $aag_region_id,
                'Network.id' => array(NETWORK_ID_AUTOCARE, NETWORK_ID_TOPTRUCK, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_GEXPERT),
            ),
            'joins' => array(
                array(
                    'alias' => 'TrainingPlannedCourse',
                    'table' => 'trainings_planned_courses',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingPlannedCourse.id = TrainingDelegate.training_planned_course_id'
                    )
                ),
                array(
                    'alias' => 'TrainingCourse',
                    'table' => 'trainings_courses',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingCourse.id = TrainingPlannedCourse.training_course_id'
                    )
                ),
                array(
                    'alias' => 'GarageContactStaff',
                    'table' => 'garages_contacts_staff',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageContactStaff.id = TrainingDelegate.garage_contact_staff_id'
                    )
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.id = GarageContactStaff.garage_id'
                    )
                ),
                array(
                    'alias' => 'Network',
                    'table' => 'networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Network.id = TrainingDelegate.network_id'
                    )
                ),
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Contact.id = GarageContactStaff.contact_id'
                    )
                ),
                array(
                    'alias' => 'Position',
                    'table' => 'positions',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Position.id = Contact.position_id'
                    )
                ),
                array(
                    'alias' => 'TrainingProvider',
                    'table' => 'trainings_providers',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingProvider.id = TrainingCourse.training_provider_id'
                    )
                ),
                array(
                    'alias' => 'Venue',
                    'table' => 'venues',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Venue.id = TrainingPlannedCourse.venue_id'
                    )
                ),
                array(
                    'alias' => 'GarageDistributor',
                    'table' => 'garages_distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageDistributor.garage_id = Garage.id'
                    )
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Distributor.id = GarageDistributor.distributor_id'
                    )
                )
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
                'TrainingCourse.id',
                'TrainingCourse.name',
                'TrainingCourse.price_credit',
                'GarageContactStaff.*',
                'Garage.id',
                'Garage.name',
                'Garage.g_number_id',
                'Contact.*',
                'Position.*',
                'TrainingProvider.id',
                'TrainingProvider.name',
                'Network.id',
                'Network.name',
                'Venue.name',
                'Distributor.account_number'
            ),
            'order' => 'Contact.first_name ASC'
        ));
    }

    public function getListofDelegatesByPlannedCoursesIds($plannedCoursesIds)
    {
        return $this->find('list', array(
            'conditions' => array(
                'TrainingDelegate.training_planned_course_id IN' => $plannedCoursesIds
            ),
            'fields' => array(
                'TrainingDelegate.id'
            )
        ));
    }
}
