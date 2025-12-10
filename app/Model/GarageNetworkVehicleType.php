<?php
class GarageNetworkVehicleType extends AppModel
{
    public $useTable = 'garages_networks_vehicle_types';

    public function findGarageNetworkVehicleTypes($garageNetworkId)
    {
        return $this->find(
            "list",
            array(
                "conditions" => array("garage_network_id" => $garageNetworkId),
                "fields" => "GarageNetworkVehicleType.vehicle_type_id"
        ));
    }

    public function findVehicleTypesListByGarageNetworkId($garageNetworkId, $selectedVehicleTypes, $lang)
    {
        if (empty($garageNetworkId) && empty($selectedVehicleTypes)) {
            return array();
        }

        if (!empty($selectedVehicleTypes) && empty($garageNetworkId)) {
            $conditions = array(
                'GarageNetworkVehicleType.vehicle_type_id IN' => $selectedVehicleTypes,
                'GarageNetworkVehicleType.vehicle_type_id = VehicleType.id'
            );
        } else {
            $conditions = array(
                'GarageNetworkVehicleType.garage_network_id IN' => $garageNetworkId,
                'GarageNetworkVehicleType.vehicle_type_id = VehicleType.id'
            );
        }

        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'VehicleType',
                        'table' => 'vehicle_types',
                        'type' => 'INNER',
                        'conditions' => $conditions,
                    ),
                ),
                'fields' => array(
                    'VehicleType.id',
                    'VehicleType.name_' . $lang
                )
            )
        );
    }
}
