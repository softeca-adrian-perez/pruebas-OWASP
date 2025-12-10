<?php

class OrderType extends AppModel{
    public $useTable = 'order_types';

	public $validate = array(
		'name_en' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
			'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Validation.Username_already_exists'
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
			'conditions' => array(
				'aag_region_id' => CakeSession::read('Auth.User.aag_region_id')
			),
            'order' => array(
                'name_' . __l()
            )
        ));
    }

    public function add_order_type($data)
    {
		$fields = array(
			'OrderType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'aag_region_id'
			)
		);
		$this->create();

		$order_type_bd = $this->guardar($data, $fields);
		if (!$order_type_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

    public function edit_order_type($data)
	{
		$fields = array(
			'OrderType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl'
			)
		);

		$order_type_bd = $this->guardar($data, $fields);
		if (!$order_type_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getData($aagRegionId) {
		return $this->find('all', array(
			'conditions' => array(
				'aag_region_id' => $aagRegionId
			),
			'order' => array(
				'name_' . __l() => 'asc'
				)
			)
		);
	}

	public function edit_order_type_ajax($data)
	{
		$fields = array(
			'OrderType' => array(
				'name_' . __l()
			)
		);

		$order_type_bd = $this->guardar($data, $fields);
		if (!$order_type_bd) {
			return false;
		}

		$this->commit();
		return true;

	}
}