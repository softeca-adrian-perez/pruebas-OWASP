<?php

class Equipment extends AppModel{
    public $useTable = 'equipments';

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

    public function search_list($aag_region_id){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
			'conditions' => array(
				'aag_region_id' => $aag_region_id
			),
            'order' => array(
                'name_' . __l()
            )
        ));
    }

    public function add_equipment($data)
    {
		$fields = array(
			'Equipment' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
				'aag_region_id'
			)
		);
		$this->create();

		$equipment_bd = $this->guardar($data, $fields);
		if (!$equipment_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

    public function edit_equipment($data)
	{
		$fields = array(
			'Equipment' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
			)
		);

		$equipment_bd = $this->guardar($data, $fields);
		if (!$equipment_bd) {
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