<?php

class GarageEmployee extends AppModel{
    public $useTable = 'garages_employees';

    public $validate = array(
        'number' => array(
            array(
                'rule' => 'numeric',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_number',
            ),
            'range' => array(
                'rule' => array('range', 0, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
    );

    public function add_garage_employee( $garage_employee , $garage_id ){
        $fields = array(
            'GarageEmployee' => array(
                'garage_id',
                'employee_type_id',
                'number',
            )
        );

        $garage_employee['GarageEmployee']['garage_id'] = $garage_id;

        $this->create();

        $garage_employee_bd = $this->guardar( $garage_employee, $fields );

        if(!$garage_employee_bd){
            return false;
        }

        $this->commit();
        return $garage_employee_bd;
    }

    public function edit_garage_employee( $garage_employee ){
        $fields = array(
            'GarageEquipment' => array(
                'garage_id',
                'employee_type_id',
                'number',
            )
        );

        $this->create();

        $garage_employee_bd = $this->guardar( $garage_employee, $fields );

        if( !$garage_employee_bd ){
            return false;
        }

        $this->commit();
        return $garage_employee_bd;
    }

    public function getDataByGarage($garage_id) {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'table' => 'employee_types',
                        'alias' => 'EmployeeType',
                        'type' => 'Inner',
                        'conditions' => array(
                            'EmployeeType.id = GarageEmployee.employee_type_id',
							'GarageEmployee.garage_id' => $garage_id
                        )
                    ),
                ),
                'fields' => array(
                    'GarageEmployee.id',
                    'EmployeeType.name_' . __l(),
                    'GarageEmployee.number',
                )
            )
        );
    }

}