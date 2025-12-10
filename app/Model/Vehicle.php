<?php

class Vehicle extends AppModel
{
    public $useTable = 'vehicles';

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

    public function new_vehicle($vehicle)
    {
        $fields = array(
            'Vehicle' => array(
                'name_en',
                'name_fr',
                'name_de',
                'name_nl',
                'name_es',
            )
        );
        $this->create();
        $service_bd = $this->guardar($vehicle, $fields);
        if (!$service_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function findVehiclesBrandExport($garage_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageVehicle',
                        'table' => 'garages_vehicles',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Vehicle.id = GarageVehicle.vehicle_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageVehicle.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Vehicle.name' . __s(),
                ),
            )
        );
    }

    public function findVehiclesSpecialistExport($garage_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'table' => 'garages_vehicles',
                        'alias' => 'GarageVehicle',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageVehicle.garage_id' => $garage_id,
                            'GarageVehicle.vehicle_id = Vehicle.id'
                        )
                    ),
                    array(
                        'table' => 'garages_specialist_makes',
                        'alias' => 'GarageSpecialistMake',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageSpecialistMake.garage_id' => $garage_id,
                            'GarageSpecialistMake.vehicle_id = Vehicle.id'
                        )
                    ),
                ),
                'fields' => array(
                    'Vehicle.name_' . __l(),
                ),
            )
        );
    }

    public function findVehiclesAndSpecialistById($garage_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'table' => 'garages_vehicles',
                        'alias' => 'GarageVehicle',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageVehicle.garage_id' => $garage_id,
                            'GarageVehicle.vehicle_id = Vehicle.id'
                        )
                    ),
                    array(
                        'table' => 'garages_specialist_makes',
                        'alias' => 'GarageSpecialistMake',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageSpecialistMake.garage_id' => $garage_id,
                            'GarageSpecialistMake.vehicle_id = Vehicle.id'
                        )
                    ),
                ),
                'fields' => array(
                    'Vehicle.*',
                    'GarageVehicle.*',
                    'GarageSpecialistMake.*'
                )
            )
        );
    }

    private $_queries = array(
        'search' =>
        array(
            'fields' => array(
                'Vehicle.*',
            )
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function search_list()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'name' . __s(),
            ),
        ));
    }

    public function findVehiclesInNetworks($networkId, $vehiclesBlackListIds)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetworkVehicle',
                        'table' => 'garages_networks_vehicles',
                        'type' => 'INNER',
                        'conditions' => array(
                            'NOT' => array('Vehicle.id' => $vehiclesBlackListIds)
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.id = GarageNetworkVehicle.garage_network_id',
                            'GarageNetwork.network_id' => $networkId,
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        ),
                    ),
                ),
                'group' => array(
                    'Vehicle.id, GarageNetwork.network_id',
                ),
                'fields' => array(
                    'Vehicle.*, GarageNetwork.network_id',
                )
            )
        );
    }

    public function findVehiclesNotBlackList($networkIds, $vehiclesBlackListIds)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetworkVehicle',
                        'table' => 'vehicles',
                        'type' => 'INNER',
                        'conditions' => array(
                            'NOT' => array('Vehicle.id' => $vehiclesBlackListIds)
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.id = GarageNetworkVehicle.garage_network_id',
                            'GarageNetwork.network_id' => $networkIds,
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        ),
                    )
                ),
                'group' => array(
                    'Vehicle.id, GarageNetwork.network_id',
                ),
                'fields' => array(
                    'Vehicle.*, GarageNetwork.network_id',
                )
            )
        );
    }

    public function findVehiclesBlackList($times, $networkId)
    {
        $this->GarageNetworkVehicleBlackList = ClassRegistry::init('GarageNetworkVehicleBlackList');
        $data = $this->GarageNetworkVehicleBlackList->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageNetwork.id = GarageNetworkVehicleBlackList.garage_network_id',
                        ),
                    )
                ),
                'conditions' => array(
                    'GarageNetwork.network_id' => $networkId,
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                ),
                'fields' => array(
                    'GarageNetworkVehicleBlackList.vehicle_id',
                ),
                'group' => array(
                    'GarageNetworkVehicleBlackList.vehicle_id'
                ),
                'having' => array(
                    'COUNT(GarageNetworkVehicleBlackList.vehicle_id)' => $times
                )
            )
        );
        return Hash::extract($data, '{n}.garages_networks_vehicles_black_list.vehicle_id');
    }

    public function getData()
    {
        return $this->find(
            'all',
            array(
                'order' => array(
                    'name' . __s() => 'asc'
                )
            )
        );
    }

    public function edit_vehicle($data)
    {
        $fields = array(
            'Vehicle' => array(
                'name_en',
                'name_fr',
                'name_de',
                'name_nl',
                'name_es',
            )
        );

        $vehicle_bd = $this->guardar($data, $fields);
        if (!$vehicle_bd) {
            return false;
        }

        $this->commit();
        return true;
    }
    public function add_vehicle($data)
    {
        $fields = array(
            'Vehicle' => array(
                'name_en',
                'name_fr',
                'name_de',
                'name_nl',
                'name_es',
            )
        );
        $this->create();

        $vehicle_bd = $this->guardar($data, $fields);
        if (!$vehicle_bd) {
            return false;
        }

        $this->commit();
        return $vehicle_bd;
    }

    public function findCitiesInNetworkVehicle($garagesNetworksVehiclesBlackListIds, $networkId)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'NOT' => array('GarageNetwork.id' => $garagesNetworksVehiclesBlackListIds),
                            'GarageNetwork.network_id' => $networkId,
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE
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
                'fields' => array(
                    'DISTINCT NetworkCity.city_id, City.name'
                ),
                'order' => array(
                    'City.name'
                )
            )
        );
    }
}
