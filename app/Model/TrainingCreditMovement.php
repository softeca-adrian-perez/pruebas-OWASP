<?php
class TrainingCreditMovement extends AppModel
{
    public $useTable = 'trainings_credits_networks';

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['Garage_name'])) {
            $conditions[] = $this->conditionGarageName($fields['Garage_name']);
        }
        if (!empty($fields['Network_name'])) {
            $conditions[] = $this->conditionNetworkName($fields['Network_name']);
        }
        if (!empty($fields['TrainingCreditMovement_date_from'])) {
            $conditions[] = $this->conditionDateFrom($fields['TrainingCreditMovement_date_from']);
        }
        if (!empty($fields['TrainingCreditMovement_date_to'])) {
            $conditions[] = $this->conditionDateTo($fields['TrainingCreditMovement_date_to']);
        }
        if (!empty($fields['Delegate_name'])) {
            $conditions[] = $this->conditionContactId($fields['Delegate_name']);
        }
        if (!empty($fields['Delegate_order_number'])) {
            $conditions[] = $this->conditionOrderNumber($fields['Delegate_order_number']);
        }
        if (isset($fields['Delegate_cancelled']) && $fields['Delegate_cancelled'] !== '') {
            $conditions[] = $this->conditionCancelled($fields['Delegate_cancelled']);
        }
        if (!empty($fields['TrainingAllowances_complete_date'])) {
            $conditions[] = $this->conditionTrainingAllowance($fields['TrainingAllowances_complete_date']);
        }
        if (!empty($fields['is_actual'])) {
            $conditions[] = $this->conditionIsActual($fields['is_actual']);
        }

        return $conditions;
    }

    private function conditionGarageName($garage_name)
    {
        return array('Garage.id =' => $garage_name);
    }

    private function conditionNetworkName($network_name)
    {
        return array('GarageNetwork.network_id =' => $network_name);
    }

    private function conditionDateFrom($date_from)
    {
        return array('DATE(TrainingCreditMovement.creation_date) >=' => Fecha::toFormatoBd($date_from));
    }

    private function conditionDateTo($date_to)
    {
        return array('DATE(TrainingCreditMovement.creation_date) <=' => Fecha::toFormatoBd($date_to));
    }

    private function conditionContactId($contact_id)
    {
        return array('ContactDelegate.id' => $contact_id);
    }

    private function conditionOrderNumber($order_number)
    {
        return array('TrainingDelegate.order_number LIKE' => '%' . $order_number . '%');
    }
    private function conditionCancelled($cancelled)

    {
        if ($cancelled == ConstantsBooleans::YES || $cancelled == ConstantsBooleans::NO) {
            return array('TrainingDelegate.cancelled' => $cancelled);
        }
    }

    private function conditionTrainingAllowance($training_allowance_id)
    {
        $this->TrainingAllowance = ClassRegistry::init('TrainingAllowance');
        $training_allowance = $this->TrainingAllowance->findFirstById($training_allowance_id);
        return array(
            'TrainingAllowance.start_date' => $training_allowance['TrainingAllowance']['start_date'],
            'TrainingAllowance.end_date' => $training_allowance['TrainingAllowance']['end_date']
        );
    }

    private function conditionIsActual($is_actual)
    {
        return array('TrainingAllowance.is_actual =' => $is_actual);
    }

    private $queries = array(
        'home' => array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageNetwork.id = TrainingCreditMovement.garage_network_id',
                    ),
                    'fields' => array(
                        'GarageNetwork.id',
                        'GarageNetwork.credit',
                        'GarageNetwork.garage_id',
                        'GarageNetwork.network_id',
                    ),
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Garage.id = GarageNetwork.garage_id',
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Garage.id',
                        'Garage.name'
                    ),
                ),
                array(
                    'alias' => 'TrainingDelegate',
                    'table' => 'trainings_delegates',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingDelegate.id = TrainingCreditMovement.training_delegate_id',
                    ),
                    'fields' => array(
                        'TrainingDelegate.id',
                        'TrainingDelegate.garage_contact_staff_id',
                        'TrainingDelegate.cancelled',
                        'TrainingDelegate.order_number',
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
                        'GarageContactStaff.contact_id',
                    ),
                ),
                array(
                    'alias' => 'ContactDelegate',
                    'table' => 'contacts',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'ContactDelegate.id = GarageContactStaff.contact_id',
                        'ContactDelegate.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'ContactDelegate.id',
                        'ContactDelegate.first_name',
                        'ContactDelegate.last_name',
                    ),
                ),
                array(
                    'alias' => 'TrainingPlannedCourse',
                    'table' => 'trainings_planned_courses',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingPlannedCourse.id = TrainingCreditMovement.training_planned_course_id',
                    ),
                    'fields' => array(
                        'TrainingPlannedCourse.id',
                        'TrainingPlannedCourse.date_from',
                    ),
                ),
                array(
                    'alias' => 'TrainingAllowance',
                    'table' => 'trainings_allowances',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingAllowance.id = TrainingCreditMovement.training_allowance_id',
                    ),
                ),
            ),
            'fields' => array(
                'TrainingCreditMovement.*',
                'GarageNetwork.credit',
                'GarageNetwork.network_id',
                'Garage.id',
                'Garage.name',
                'ContactDelegate.id',
                'ContactDelegate.first_name',
                'ContactDelegate.last_name',
                'TrainingPlannedCourse.date_from',
                'TrainingDelegate.cancelled',
                'TrainingDelegate.order_number',
                'TrainingAllowance.id',
                'TrainingAllowance.is_actual'
            ),
            'group' => 'TrainingCreditMovement.id',
            'order' => 'TrainingCreditMovement.creation_date desc',
        ),
    );

    public function _query($index, $garage_id)
    {
        $query = $this->queries[$index];
        if ($index === 'home' && $garage_id) {
            $query['conditions']['Garage.id'] = $garage_id;
        }

        return $query;
    }

    public function queryMovement($index)
    {
        return $this->queries[$index];
    }
}
