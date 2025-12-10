<?php
class GarageNetworkVehicleBlackList extends AppModel
{
    public $useTable = 'garages_networks_vehicles_black_list';

    public function findGarageNetworkVehiclesBlackList($garageNetworkId)
    {
        return $this->find(
            "list",
            array(
                "conditions" => array("garage_network_id" => $garageNetworkId),
                "fields" => "GarageNetworkVehicleBlackList.vehicle_id"
        ));
    }

    public function findGarageNetworkVehicleBlackListVehicle($vehicleId)
    {
        $data = $this->find('all', array(
            'conditions' => array(
                'GarageNetworkVehicleBlackList.vehicle_id' => $vehicleId
            ),
            'fields' => 'GarageNetworkVehicleBlackList.garage_network_id'
        ));

        return Hash::extract($data, '{n}.GarageNetworkVehicleBlackList.garage_network_id');
    }
}
