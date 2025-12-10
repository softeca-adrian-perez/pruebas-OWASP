<?php

class EmployeeType extends AppModel{
    public $useTable = 'employee_types';

    public function search_list(){
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'name_' . __l()
            )
        ));
    }

    public function add_employee_type($data) 
    {
		$fields = array(
			'EmployeeType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
                'aag_region_id',
			)
		);
		$this->create();

		$employee_type_bd = $this->guardar($data, $fields);
		if (!$employee_type_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

    public function edit_employee_type($data)
	{
		$fields = array(
			'EmployeeType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
			)
		);

		$employee_type_bd = $this->guardar($data, $fields);
		if (!$employee_type_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getData($aag_region_id) {
		return $this->find('all', array(
			'conditions' => array(
				'aag_region_id' => $aag_region_id
			),
			'order' => array(
				'name_' . __l() => 'asc'
				)
			)
		);
	}

	public function getDataByGarage($garage_id) {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'table' => 'garages_employees',
                        'alias' => 'GarageEmployee',
                        'type' => 'Left',
                        'conditions' => array(
                            'GarageEmployee.employee_type_id = EmployeeType.id',
							'GarageEmployee.garage_id' => $garage_id
                        )
                    ),
                ),
				'conditions' => array(
					'GarageEmployee.employee_type_id IS NULL',
				),
                'fields' => array(
                    'EmployeeType.id',
                    'EmployeeType.name_' . __l(),
                )
            )
        );
    }

}