<?php
class GarageNetworkGenartFamily extends AppModel
{
    public $useTable = 'garages_networks_genarts_families';

	public $validate = array(
		'discount' => array(
			array(
				'rule' => array('comparison', '>=', 0),
				'required' => true,
				'message' => 'Validation.Mandatory_to_numeric_positive',
			),
		),
		'markup' => array(
			array(
				'rule' => array('comparison', '>=', 0),
				'required' => true,
				'message' => 'Validation.Mandatory_to_numeric_positive',
			),
		),
		'surcharge' => array(
			array(
				'rule' => array('comparison', '>=', 0),
				'required' => true,
				'message' => 'Validation.Mandatory_to_numeric_positive',
			),
		)
	);

    public function find_by_garage_network_and_genart_family($garageNetworkId, $genartFamilyId)
    {
        $query = $this->find('first', array(
            'conditions' => array(
                'garage_network_id' => $garageNetworkId,
                'genart_family_id' => $genartFamilyId
            )
        ));
        return $query;
    }

    public function add( $data ){
        $fields = array(
            'GarageNetworkGenartFamily' => array(
                'garage_network_id',
                'genart_family_id',
                'discount',
                'markup',
                'surcharge',
            )
        );
        return $this->guardar($data, $fields);
    }
    public function edit( $garageNetworkGenartFamily )
    {
        $fields = array(
            'GarageNetworkGenartFamily' => array(
                'id',
                'garage_network_id',
                'genart_family_id',
                'discount',
                'markup',
                'surcharge',
            )
        );
        return $this->guardar($garageNetworkGenartFamily['GarageNetworkGenartFamily'], $fields);
    }

    public function getAllByAagRegionId($aagRegionId)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageNetwork.id = GarageNetworkGenartFamily.garage_network_id',
                        ),
                    ),
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Garage.id = GarageNetwork.garage_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Garage.aag_region_id' => $aagRegionId,
                ),
                'fields' => array(
                    'GarageNetworkGenartFamily.*',
                ),
            )
        );
    }
}
