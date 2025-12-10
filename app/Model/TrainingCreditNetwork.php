<?php
class TrainingCreditNetwork extends AppModel
{
    public $useTable = 'trainings_credits_networks';

    public $validate = array(
        'description' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['Garage_name'])) {
            $conditions[] = $this->conditionGarageName($fields['Garage_name']);
        }
        if (!empty($fields['Network_name'])) {
            $conditions[] = $this->conditionNetworkName($fields['Network_name']);
        }

        return $conditions;
    }

    private function conditionGarageName($garage_name)
    {
        return array('Garage.id =' => $garage_name);
    }
    private function conditionNetworkName($network_name)
    {
        return array('Network.id =' => $network_name);
    }

    public function checkBD()
    {
        $existingRow = $this->find('first', array(
            'fields' => array(
                'TrainingCreditNetwork.*'
            ),
        ));

        if ($existingRow) {
            return $existingRow;
        } else {
            return null;
        }
    }

    public function addTrainingsCreditsNetworks($training_credit)
    {
        $fields = array(
            'TrainingCreditNetwork' => array(
                'garage_network_id',
                'credit_given',
                'contact_id',
                'description',
                'reason_allowance_id',
                'training_allowance_id',
                'creation_date'
            )
        );

        $training_credit['TrainingCreditNetwork']['garage_network_id'] = $training_credit['TrainingNetworkCredit']['garage_network_id'];
        $training_credit['TrainingCreditNetwork']['credit_given'] = $training_credit['TrainingNetworkCredit']['credit_given'];
        $training_credit['TrainingCreditNetwork']['contact_id'] = $training_credit['TrainingNetworkCredit']['contact_id'];
        $training_credit['TrainingCreditNetwork']['description'] = ConstantsDescriptionCredits::EXTRA_GIVEN;
        $training_credit['TrainingCreditNetwork']['reason_allowance_id'] = $training_credit['TrainingNetworkCredit']['reason_allowance_id'];
        if (isset($training_credit['TrainingNetworkCredit']['training_allowance_id'])) {
            $training_credit['TrainingCreditNetwork']['training_allowance_id'] = $training_credit['TrainingNetworkCredit']['training_allowance_id'];
        }
        $training_credit['TrainingCreditNetwork']['creation_date'] = date('Y-m-d H:i:s');

        $this->create();

        $training_credit_bd = $this->guardar($training_credit, $fields);
        if (!$training_credit_bd) {
            return false;
        }
        $this->commit();
        return $training_credit_bd;
    }

    public function addTrainingsCreditsNetworksAccept($delegate)
    {
        $fields = array(
            'TrainingCreditNetwork' => array(
                'garage_network_id',
                'credit_spent',
                'credit',
                'contact_id',
                'description',
                'training_planned_course_id',
                'training_delegate_id',
                'training_allowance_id',
                'creation_date'
            )
        );
        $training_credit['TrainingCreditNetwork']['garage_network_id'] = $delegate['GarageNetwork']['GarageNetwork']['id'];
        $training_credit['TrainingCreditNetwork']['contact_id'] = $delegate['Contact'];
        $training_credit['TrainingCreditNetwork']['description'] = $delegate['TrainingCourse']['TrainingCourse']['name'];
        $training_credit['TrainingCreditNetwork']['training_planned_course_id'] = $delegate['training_planned_course_id'];
        if ($delegate['add_credits_input'] == 'accept') {
            $training_credit['TrainingCreditNetwork']['credit_spent'] = $delegate['TrainingCourse']['TrainingCourse']['price_credit'];
        }
        if (isset($delegate['training_delegate_id'])) {
            $training_credit['TrainingCreditNetwork']['training_delegate_id'] = $delegate['training_delegate_id'];
        }
        if (isset($delegate['TrainingNetworkCredit']['training_allowance_id'])) {
            $training_credit['TrainingCreditNetwork']['training_allowance_id'] = $delegate['TrainingNetworkCredit']['training_allowance_id'];
        }
        $training_credit['TrainingCreditNetwork']['creation_date'] = date('Y-m-d H:i:s');

        $this->create();

        $training_credit_bd = $this->guardar($training_credit, $fields);
        if (!$training_credit_bd) {
            return false;
        }
        $this->commit();
        return $training_credit_bd;
    }

    public function addTrainingsCreditsNetworksDelete($delegate)
    {
        $fields = array(
            'TrainingCreditNetwork' => array(
                'garage_network_id',
                'credit_given',
                'credit',
                'contact_id',
                'description',
                'training_planned_course_id',
                'training_delegate_id',
                'training_allowance_id',
                'creation_date'
            )
        );
        $training_credit['TrainingCreditNetwork']['garage_network_id'] = $delegate['GarageNetwork']['GarageNetwork']['id'];
        $training_credit['TrainingCreditNetwork']['contact_id'] = $delegate['Contact'];
        $training_credit['TrainingCreditNetwork']['description'] = ConstantsDescriptionCredits::REFUNDED_CREDITS;
        $training_credit['TrainingCreditNetwork']['training_planned_course_id'] = $delegate['TrainingPlannedCourse']['TrainingPlannedCourse']['id'];
        if ($delegate['add_credits_input'] == 'accept') {
            $training_credit['TrainingCreditNetwork']['credit_given'] = $delegate['TrainingCourse']['TrainingCourse']['price_credit'];
        }
        if (isset($delegate['training_delegate_id'])) {
            $training_credit['TrainingCreditNetwork']['training_delegate_id'] = $delegate['training_delegate_id'];
        }
        if (isset($delegate['TrainingNetworkCredit']['training_allowance_id'])) {
            $training_credit['TrainingCreditNetwork']['training_allowance_id'] = $delegate['TrainingNetworkCredit']['training_allowance_id'];
        }
        $training_credit['TrainingCreditNetwork']['creation_date'] = date('Y-m-d H:i:s');

        $this->create();

        $training_credit_bd = $this->guardar($training_credit, $fields);
        if (!$training_credit_bd) {
            return false;
        }
        return $training_credit_bd;
    }

    public function addTrainingsCreditsNetworksDeleteFromPlannedCourse($delegate)
    {
        $fields = array(
            'TrainingCreditNetwork' => array(
                'garage_network_id',
                'credit_given',
                'credit',
                'contact_id',
                'description',
                'training_planned_course_id',
                'training_delegate_id',
                'training_allowance_id',
                'creation_date'
            )
        );
        $training_credit['TrainingCreditNetwork']['garage_network_id'] = $delegate['GarageNetwork']['GarageNetwork']['id'];
        $training_credit['TrainingCreditNetwork']['contact_id'] = $delegate['Contact'];
        $training_credit['TrainingCreditNetwork']['description'] = ConstantsDescriptionCredits::REFUNDED_CREDITS;
        $training_credit['TrainingCreditNetwork']['training_planned_course_id'] = $delegate['TrainingPlannedCourse']['TrainingPlannedCourse']['id'];
        if ($delegate['add_credits_input'] == 'accept') {
            $training_credit['TrainingCreditNetwork']['credit_given'] = $delegate['TrainingCourse']['price_credit'];
        }
        if (isset($delegate['TrainingDelegate']['id'])) {
            $training_credit['TrainingCreditNetwork']['training_delegate_id'] = $delegate['TrainingDelegate']['id'];
        }
        if (isset($delegate['TrainingNetworkCredit']['training_allowance_id'])) {
            $training_credit['TrainingCreditNetwork']['training_allowance_id'] = $delegate['TrainingNetworkCredit']['training_allowance_id'];
        }
        $training_credit['TrainingCreditNetwork']['creation_date'] = date('Y-m-d H:i:s');

        $this->create();

        $training_credit_bd = $this->guardar($training_credit, $fields);
        if (!$training_credit_bd) {
            return false;
        }
        return $training_credit_bd;
    }

    public function addTrainingsCreditsNetworksYearly($training_credit)
    {
        $fields = array(
            'TrainingCreditNetwork' => array(
                'garage_network_id',
                'description',
                'creation_date'
            )
        );

        $training_credit['TrainingCreditNetwork']['description'] = ConstantsDescriptionCredits::YEARLY_RENEW;
        $training_credit['TrainingCreditNetwork']['creation_date'] = date('Y-m-d H:i:s');

        $this->create();

        $training_credit_bd = $this->guardar($training_credit, $fields);
        if (!$training_credit_bd) {
            return false;
        }
        $this->commit();
        return true;
    }

    public function addTrainingsCreditsNetworksDelegate($delegate)
    {
        $fields = array(
            'TrainingCreditNetwork' => array(
                'garage_network_id',
                'credit_given',
                'credit_spent',
                'contact_id',
                'description',
                'training_planned_course_id',
                'training_delegate_id',
                'training_allowance_id',
                'creation_date'
            )
        );

        $training_credit['TrainingCreditNetwork']['garage_network_id'] = $delegate['TrainingNetworkCredit']['garage_network_id'];
        $training_credit['TrainingCreditNetwork']['contact_id'] = $delegate['TrainingNetworkCredit']['contact_id'];
        $training_credit['TrainingCreditNetwork']['training_planned_course_id'] = $delegate['TrainingNetworkCredit']['training_planned_course_id'];

        if (isset($delegate['TrainingNetworkCredit']['credit_given'])) {
            $training_credit['TrainingCreditNetwork']['credit_given'] = $delegate['TrainingNetworkCredit']['credit_given'];
            $training_credit['TrainingCreditNetwork']['description'] = ConstantsDescriptionCredits::REFUNDED_CREDITS;
        } else {
            $training_credit['TrainingCreditNetwork']['credit_spent'] = $delegate['TrainingNetworkCredit']['credit_spent'];
            $training_credit['TrainingCreditNetwork']['description'] = $delegate['TrainingNetworkCredit']['description'];
        }
        if (isset($delegate['TrainingDelegate']['id'])) {
            $training_credit['TrainingCreditNetwork']['training_delegate_id'] = $delegate['TrainingDelegate']['id'];
        }
        if (isset($delegate['TrainingNetworkCredit']['training_allowance_id'])) {
            $training_credit['TrainingCreditNetwork']['training_allowance_id'] = $delegate['TrainingNetworkCredit']['training_allowance_id'];
        }
        $training_credit['TrainingCreditNetwork']['creation_date'] = date('Y-m-d H:i:s');

        $this->create();

        $training_credit_bd = $this->guardar($training_credit, $fields);
        if (!$training_credit_bd) {
            return false;
        }
        return $training_credit_bd;
    }

    public function edit($training_credit, $training_allowance)
    {
        $fields = array(
            'TrainingCreditNetwork' => array(
                'training_allowance_id',
                'modification_date'
            )
        );

        $training_credit['TrainingCreditNetwork']['training_allowance_id'] = $training_allowance['TrainingAllowance']['id'];
        $training_credit['TrainingCreditNetwork']['modification_date'] = date('Y-m-d H:i:s');

        return $this->guardar($training_credit, $fields);
    }

    public function editAllowance($training_credit)
    {
        $fields = array(
            'TrainingCreditNetwork' => array(
                'training_allowance_id',
                'modification_date'
            )
        );

        $training_credit['TrainingCreditNetwork']['training_allowance_id'] = null;
        $training_credit['TrainingCreditNetwork']['modification_date'] = date('Y-m-d H:i:s');

        return $this->guardar($training_credit, $fields);
    }

    public function findAllNullByGarageNetworkId($garage_network_id)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'TrainingCreditNetwork.training_allowance_id IS NULL',
                    'TrainingCreditNetwork.training_planned_course_id IS NULL',
                    'TrainingCreditNetwork.garage_network_id' => $garage_network_id
                ),
                'fields' => array(
                    'TrainingCreditNetwork.id',
                    'TrainingCreditNetwork.creation_date'
                ),
                'order' => array(
                    'id' => 'asc'
                )
            )
        );
    }

    public function findAllNullByGarageNetworkIdPlannedCourse($garage_network_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TrainingPlannedCourse',
                        'table' => 'trainings_planned_courses',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TrainingPlannedCourse.id = TrainingCreditNetwork.training_planned_course_id'
                        )
                    )
                ),
                'conditions' => array(
                    'TrainingCreditNetwork.training_allowance_id IS NULL',
                    'TrainingCreditNetwork.garage_network_id' => $garage_network_id
                ),
                'fields' => array(
                    'TrainingCreditNetwork.id',
                    'TrainingPlannedCourse.date_from'
                ),
                'order' => array(
                    'id' => 'asc'
                ),
            )
        );
    }

    public function editNewAllowance($training_credit, $training_allowance_id)
    {
        $fields = array(
            'TrainingCreditNetwork' => array(
                'training_allowance_id',
                'modification_date'
            )
        );

        $training_credit['TrainingCreditNetwork']['training_allowance_id'] = $training_allowance_id;
        $training_credit['TrainingCreditNetwork']['modification_date'] = date('Y-m-d H:i:s');

        return $this->guardar($training_credit, $fields);
    }

    public function findLatestDate($garage_network_id)
    {
        $maxDateFromResult = $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'TrainingPlannedCourse',
                    'table' => 'trainings_planned_courses',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingPlannedCourse.id = TrainingCreditNetwork.training_planned_course_id',
                    ),
                ),
            ),
            'conditions' => array(
                'TrainingCreditNetwork.garage_network_id' => $garage_network_id,
            ),
            'fields' => array(
                'MAX(TrainingPlannedCourse.date_from) AS max_date_from'
            )
        ));

        $maxDateModifyResult = $this->find('first', array(
            'conditions' => array(
                'TrainingCreditNetwork.garage_network_id' => $garage_network_id,
            ),
            'fields' => array(
                'MAX(TrainingCreditNetwork.creation_date) AS max_creation_date'
            )
        ));

        $maxDateFrom = !empty($maxDateFromResult[0]['max_date_from']) ? $maxDateFromResult[0]['max_date_from'] : null;
        $maxDateModify = !empty($maxDateModifyResult[0]['max_creation_date']) ? date('Y-m-d', strtotime($maxDateModifyResult[0]['max_creation_date'])) : null;

        if ($maxDateFrom && $maxDateModify) {
            return max($maxDateFrom, $maxDateModify);
        } elseif ($maxDateFrom) {
            return $maxDateFrom;
        } elseif ($maxDateModify) {
            return $maxDateModify;
        } else {
            return null;
        }
    }

    public function findAllByAllowanceId($allowance_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TrainingPlannedCourse',
                        'table' => 'trainings_planned_courses',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TrainingPlannedCourse.id = TrainingCreditNetwork.training_planned_course_id'
                        )
                    )
                ),
                'conditions' => array(
                    'TrainingCreditNetwork.training_allowance_id' => $allowance_id
                ),
                'fields' => array(
                    'TrainingCreditNetwork.id',
                    'TrainingCreditNetwork.creation_date',
                    'TrainingPlannedCourse.date_from'
                ),
                'order' => array(
                    'id' => 'asc'
                )
            )
        );
    }
}
