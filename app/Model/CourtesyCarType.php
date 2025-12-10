<?php

class CourtesyCarType extends AppModel{
    public $useTable = 'courtesy_car_types';

	public $validate = array(
		'name_en' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_fr' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_de' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_nl' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

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

    public function add_courtesy_car_type($data) 
    {
		$fields = array(
			'CourtesyCarType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
                'aag_region_id',
			)
		);
		$this->create();

		$courtesy_car_type_bd = $this->guardar($data, $fields);
		if (!$courtesy_car_type_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

    public function edit_courtesy_car_type($data)
	{
		$fields = array(
			'CourtesyCarType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
			)
		);

		$courtesy_car_type_bd = $this->guardar($data, $fields);
		if (!$courtesy_car_type_bd) {
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

}