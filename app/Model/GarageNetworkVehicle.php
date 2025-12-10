<?php
class GarageNetworkVehicle extends AppModel
{
    public $useTable = 'garages_networks_vehicles';

    public function findGarageNetworkVehicles($garageNetworkId)
    {
        return $this->find(
            "list",
            array(
                "conditions" => array("garage_network_id" => $garageNetworkId),
                "fields" => "GarageNetworkVehicle.vehicle_id"
        ));
    }

    public function findVehicleListByGarageNetworkId($garageNetworkId, $selectedVehicles, $lang)
    {

        if (empty($garageNetworkId) && empty($selectedVehicles)) {
            return array();
        }

        if (!empty($selectedVehicles) && empty($garageNetworkId)) {
            $conditions = array(
                'GarageNetworkVehicle.vehicle_id IN' => $selectedVehicles,
                'GarageNetworkVehicle.vehicle_id = Vehicle.id'
            );
        } else {
            $conditions = array(
                'GarageNetworkVehicle.garage_network_id IN' => $garageNetworkId,
                'GarageNetworkVehicle.vehicle_id = Vehicle.id'
            );
        }

        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Vehicle',
                        'table' => 'vehicles',
                        'type' => 'INNER',
                        'conditions' => $conditions,
                    ),
                ),
                'fields' => array(
                    'Vehicle.id',
                    'Vehicle.name_' . $lang
                )
            )
        );
    }
}
