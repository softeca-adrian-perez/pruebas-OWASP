<?php

class Service extends AppModel{
    public $useTable = 'services';

    var $hasAndBelongsToMany = array(
        'Garage',
    );

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
        'international_code' => array(
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
                'Service.*',
            ),
        ),
    );

    public function _query( $index ){
        return $this->_queries[$index];
    }

    public function search_list(){
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

    public function new_edit_service( $service, $create ){
        $fields = array(
            'Service' => array(
                'name_en',
                'name_fr',
                'name_de',
				'name_nl',
				'name_es',
                'url',
            )
        );
        if($create){
            $this->create();
        }
        $service_bd = $this->guardar($service, $fields);
        if ( !$service_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    public function findServicesExport($garage_id){
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageService',
                        'table' => 'garages_services',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Service.id = GarageService.service_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageService.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Service.name'.__s(),
                ),
            )
        );
    }

    public function findServicesInNetworks($networkIds)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetworkService',
                        'table' => 'garages_networks_services',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Service.id = GarageNetworkService.service_id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.id = GarageNetworkService.garage_network_id',
                            'GarageNetwork.network_id' => $networkIds,
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        ),
                    ),
                ),
                'group' => array(
                    'Service.id, GarageNetwork.network_id',
                ),
                'fields' => array(
                    'Service.*, GarageNetwork.network_id',
                )
            )
        );
    }

    public function findCitiesInNetworkService($networkId, $serviceId)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetworkService',
                        'table' => 'garages_networks_services',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Service.id = GarageNetworkService.service_id'
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.id = GarageNetworkService.garage_network_id',
                            'GarageNetwork.network_id' => $networkId,
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        ),
                    ),
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Garage.id = GarageNetwork.garage_id'
                        ),
                    ),
                    array(
                        'alias' => 'City',
                        'table' => 'cities',
                        'type' => 'INNER',
                        'conditions' => array(
                            'City.id = Garage.city_id'
                        ),
                    ),
                    array(
                        'alias' => 'NetworkCity',
                        'table' => 'network_cities',
                        'type' => 'INNER',
                        'conditions' => array(
                            'NetworkCity.city_id = City.id',
                            'NetworkCity.network_id' => $networkId,
                        ),
                    ),
                ),
                'conditions' => array(
                    'Service.id' => $serviceId
                ),
                'fields' => array(
                    'DISTINCT NetworkCity.city_id, City.name'
                ),
                'order' => array(
                    'City.name'
                )
            )
        );
    }

    public function findGarageNetworkServices($garageNetworkId)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetworkService',
                        'table' => 'garages_networks_services',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetworkService.service_id = Service.id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.id = GarageNetworkService.garage_network_id',
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageNetwork.id' => $garageNetworkId
                ),
                'fields' => array(
                    'Service.*'
                )
            )
        );
    }
}
