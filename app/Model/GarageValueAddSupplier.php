<?php

class GarageValueAddSupplier extends AppModel{
    public $useTable = 'garages_value_add_supplier';

	public $hasMany = array(
		'Garage',
	);

    public $validate = array(
        'value_add_supplier_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_name',
            ),
        ),
        'value_add_supplier_type_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_name',
            ),
        ),
    );

	public function add_garage_value_add_supplier( $garage_value_add_supplier ){
        $fields = array(
            'GarageValueAddSupplier' => array(
                'garage_id',
                'value_add_supplier_id',
                'value_add_supplier_type_id',
                'to_date',
                'from_date',
            )
        );
		
		$garage_value_add_supplier['GarageValueAddSupplier']['to_date'] = Fecha::toFormatoBd($garage_value_add_supplier['GarageValueAddSupplier']['to_date']);
		$garage_value_add_supplier['GarageValueAddSupplier']['from_date'] = Fecha::toFormatoBd($garage_value_add_supplier['GarageValueAddSupplier']['from_date']);
        $this->create();
        $garage_value_add_supplier_bd = $this->guardar( $garage_value_add_supplier, $fields );

        if(!$garage_value_add_supplier_bd){
            return false;
        }

        $this->commit();
        return $garage_value_add_supplier_bd;
    }

    public function edit_garage_value_add_supplier( $garage_value_add_supplier ){
        $fields = array(
            'GarageValueAddSupplier' => array(
                'garage_id',
                'value_add_supplier_id',
                'value_add_supplier_type_id',
                'to_date',
                'from_date',
            )
        );
		
		$garage_value_add_supplier['GarageValueAddSupplier']['to_date'] = Fecha::toFormatoBd($garage_value_add_supplier['GarageValueAddSupplier']['to_date']);
		$garage_value_add_supplier['GarageValueAddSupplier']['from_date'] = Fecha::toFormatoBd($garage_value_add_supplier['GarageValueAddSupplier']['from_date']);

        return $this->guardar($garage_value_add_supplier, $fields);
    }

	public function getDataByGarage($garage_id) {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'table' => 'value_add_suppliers',
                        'alias' => 'ValueAddSupplier',
                        'type' => 'Inner',
                        'conditions' => array(
                            'ValueAddSupplier.id = GarageValueAddSupplier.value_add_supplier_id'
                        )
                    ),
					array(
                        'table' => 'value_add_supplier_type',
                        'alias' => 'ValueAddSupplierType',
                        'type' => 'Inner',
                        'conditions' => array(
                            'ValueAddSupplierType.id = GarageValueAddSupplier.value_add_supplier_type_id'
                        )
                    ),
                ),
				'conditions' => array(
					'GarageValueAddSupplier.garage_id' => $garage_id
				),
                'fields' => array(
                    'GarageValueAddSupplier.id',
                    'ValueAddSupplier.name_' . __l(),
                    'GarageValueAddSupplier.to_date',
                    'GarageValueAddSupplier.from_date',
                    'ValueAddSupplierType.name_' . __l(),
                )
            )
        );
    }

	public function get_value_add_supplier_list($garage_id) {
		return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'table' => 'value_add_suppliers',
                        'alias' => 'ValueAddSupplier',
                        'type' => 'Inner',
                        'conditions' => array(
                            'ValueAddSupplier.id = GarageValueAddSupplier.value_add_supplier_id',
							'GarageValueAddSupplier.garage_id' => $garage_id,
                        )
                    ),
                ),
                'fields' => array(
                    'ValueAddSupplier.id',
                ),
            )
        );
	}

	public function get_value_add_supplier_type_list($garage_id) {
		return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'table' => 'value_add_supplier_type',
                        'alias' => 'ValueAddSupplierType',
                        'type' => 'Inner',
                        'conditions' => array(
                            'ValueAddSupplierType.id = GarageValueAddSupplier.value_add_supplier_type_id',
							'GarageValueAddSupplier.garage_id' => $garage_id,
                        )
                    ),
                ),
                'fields' => array(
                    'ValueAddSupplierType.id',
                ),
            )
        );
	}
}