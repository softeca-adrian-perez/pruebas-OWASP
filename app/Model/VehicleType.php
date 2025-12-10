<?php

class VehicleType extends AppModel
{
    public $useTable = 'vehicle_types';

    var $hasAndBelongsToMany = array(
        'Garage',
    );

	public $validate = array(
		'international_code' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
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
        'url' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    private $_queries = array(
        'search' => array(
            'fields' => array(
                'VehicleType.*',
            ),
        ),
    );

    public function _query( $index )
    {
        return $this->_queries[$index];
    }

    public function new_vehicle_type( $vehicle_type ){
        $fields = array(
            'VehicleType' => array(
                'name_en',
                'name_fr',
                'name_de',
                'name_es',
                'name_nl',
                'name_lc',
                'url',
            )
        );
        $this->create();
        $service_bd = $this->guardar($vehicle_type, $fields);
        if ( !$service_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    public function findVehicleTypesExport($garage_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageVehicleTypes',
                        'table' => 'garages_vehicle_types',
                        'type' => 'INNER',
                        'conditions' => array(
                            'VehicleType.id = GarageVehicleTypes.vehicle_type_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageVehicleTypes.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'VehicleType.name'.__s(),
                ),
            )
        );
    }

    public function search_list()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'name' . __s()
            )
        ));
    }
}