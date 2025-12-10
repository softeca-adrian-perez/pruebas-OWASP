<?php
class ServiceDriver extends AppModel
{
    public $useTable = 'services_drivers';

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

    public function findServicesDriversNetwork($networkId)
    {
        return $this->find(
            "all",
            array(
                "conditions" => array("network_id" => $networkId),
                "order" => "name_en asc"
            )
        );
    }

    public function findServicesDriversInNetworks($networkIds)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'ServiceDriver.network_id' => $networkIds
                ),
                'group' => array(
                    'ServiceDriver.id, ServiceDriver.network_id',
                ),
                'fields' => array(
                    'ServiceDriver.*',
                )
            )
        );
    }

    public function findGarageNetworkServicesDrivers($garageNetworkId)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetworkServiceDriver',
                        'table' => 'garages_networks_services_drivers',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetworkServiceDriver.service_driver_id = ServiceDriver.id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.id = GarageNetworkServiceDriver.garage_network_id',
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageNetwork.id' => $garageNetworkId
                ),
                'fields' => array(
                    'ServiceDriver.*'
                )
            )
        );
    }

    public function add_services_drivers($data)
    {
        $fields = array(
            'ValueAdd' => array(
                'network_id',
                'name_en',
                'name_fr',
                'name_de',
                'name_nl',
                'name_es',
                'aag_region_id',
            )
        );
        $this->create();

        $services_drivers_bd = $this->guardar($data, $fields);
        if (!$services_drivers_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function edit_services_drivers($data)
    {
        $fields = array(
            'ValueAdd' => array(
                'name_en',
                'name_fr',
                'name_de',
                'name_nl',
                'name_es',
            )
        );

        $services_drivers_bd = $this->guardar($data, $fields);
        if (!$services_drivers_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function getData()
    {
        return $this->find(
            'all',
            array(
                'order' => array(
                    'name_' . __l() => 'asc'
                )
            )
        );
    }

    public function getDataByAagRegion($aag_region_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = ServiceDriver.network_id',
                        ),
                    ),
                    array(
                        'alias' => 'AagRegion',
                        'table' => 'aag_regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'AagRegion.id = Network.aag_region_id'
                        )
                    ),
                ),
                'conditions' => array(
                    'AagRegion.id' => $aag_region_id
                )
            )
        );
    }

    public function getDataForNetworkServiceDriverList($networkId) {
		return $this->find(
			'all',
			array(
				'conditions' => array(
                    'ServiceDriver.network_id' => $networkId,
                ),
				'fields' => array(
					'ServiceDriver.id',
					'ServiceDriver.name_' . __l(),
				),
				'order' => array(
					'ServiceDriver.name_' . __l() => 'asc'
				)
			)
		);
	}
}
