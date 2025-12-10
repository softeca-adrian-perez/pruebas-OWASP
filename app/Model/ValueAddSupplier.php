<?php

class ValueAddSupplier extends AppModel{
    public $useTable = 'value_add_suppliers';

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

    public function add_value_add_supplier($data) 
    {
		$fields = array(
			'ValueAddSupplier' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
                'aag_region_id',
			)
		);
		$this->create();

		$value_add_supplier_bd = $this->guardar($data, $fields);
		if (!$value_add_supplier_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

    public function edit_value_add_supplier($data)
	{
		$fields = array(
			'ValueAddSupplier' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
			)
		);

		$value_add_supplier_bd = $this->guardar($data, $fields);
		if (!$value_add_supplier_bd) {
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
                        'table' => 'garages_value_add_supplier',
                        'alias' => 'GarageValueAddSupplier',
                        'type' => 'Left',
                        'conditions' => array(
                            'GarageValueAddSupplier.value_add_supplier_id = ValueAddSupplier.id',
							'GarageValueAddSupplier.garage_id' => $garage_id
                        )
                    ),
                ),
				'conditions' => array(
					'GarageValueAddSupplier.value_add_supplier_id IS NULL',
				),
                'fields' => array(
                    'ValueAddSupplier.id',
                    'ValueAddSupplier.name_' . __l(),
                )
            )
        );
    }
    
}