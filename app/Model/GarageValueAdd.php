<?php

class GarageValueAdd extends AppModel
{

    public $useTable = 'garages_values_adds';

    public $validate = array(
        'value_add_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_value_add',
            ),
        ),
        'start_date' => array(
            'rule' => 'date',
            'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'end_date' => array(
            'rule' => 'date',
            'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'version' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    private $_queries = array(
        'home' =>
        array(
            'fields' => array(
                'GarageValueAdd.*',
            ),
            'order' => 'GarageValueAdd.start_date desc'
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function add_garage_value_add($garage_value_add, $garage_id)
    {
        $fields = array(
            'GarageValueAdd' => array(
                'garage_id',
                'value_add_id',
                'start_date',
                'end_date',
                'version',
                'billing_schedule_id',
                'amount',
                'member_pay',
                'garage_pay',
                'billed_by_aag',
                'number_subscription',
                'online_ordering',
            )
        );
        $garage_value_add['GarageValueAdd']['garage_id'] = $garage_id;
        $garage_value_add['GarageValueAdd']['start_date'] = Fecha::toFormatoBd($garage_value_add['GarageValueAdd']['start_date']);
        $garage_value_add['GarageValueAdd']['end_date'] = Fecha::toFormatoBd($garage_value_add['GarageValueAdd']['end_date']);
        $this->create();

        $garage_value_add_bd = $this->guardar($garage_value_add, $fields);
        if (!$garage_value_add_bd) {
            return false;
        }

        $this->commit();
        return $garage_value_add_bd;
    }

    public function edit_garage_value_add($garage_value_add)
    {
        $fields = array(
            'GarageValueAdd' => array(
                'garage_id',
                'value_add_id',
                'start_date',
                'end_date',
                'version',
                'billing_schedule_id',
                'amount',
                'member_pay',
                'garage_pay',
                'billed_by_aag',
                'number_subscription',
                'online_ordering',
            )
        );

        $garage_value_add['GarageValueAdd']['start_date'] = Fecha::toFormatoBd($garage_value_add['GarageValueAdd']['start_date']);
        $garage_value_add['GarageValueAdd']['end_date'] = Fecha::toFormatoBd($garage_value_add['GarageValueAdd']['end_date']);

        $this->create();

        $garage_value_add_bd = $this->guardar($garage_value_add, $fields);
        if (!$garage_value_add_bd) {
            return false;
        }

        $this->commit();
        return $garage_value_add_bd;
    }
}
