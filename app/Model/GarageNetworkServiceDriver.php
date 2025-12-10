<?php
class GarageNetworkServiceDriver extends AppModel
{
    public $useTable = 'garages_networks_services_drivers';

    public $hasOne = array(
        'ServiceDriver',
    );

    public function findGarageNetworkServicesDrivers($garageNetworkId)
    {
        return $this->find(
            "list",
            array(
                "conditions" => array("garage_network_id" => $garageNetworkId),
                "fields" => "GarageNetworkServiceDriver.service_driver_id"
        ));
    }

    public function findServiceDriverListByGarageNetworkId($garageNetworkId, $selectedServicesDrivers, $lang)
    {
        if (empty($garageNetworkId) && empty($selectedServicesDrivers)) {
            return array();
        }

        if (!empty($selectedServicesDrivers) && empty($garageNetworkId)) {
            $conditions = array(
                'GarageNetworkServiceDriver.service_driver_id IN' => $selectedServicesDrivers,
                'GarageNetworkServiceDriver.service_driver_id = ServiceDriver.id'
            );
        } else {
            $conditions = array(
                'GarageNetworkServiceDriver.garage_network_id IN' => $garageNetworkId,
                'GarageNetworkServiceDriver.service_driver_id = ServiceDriver.id'
            );
        }
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'ServiceDriver',
                        'table' => 'services_drivers',
                        'type' => 'INNER',
                        'conditions' => $conditions,
                    ),
                ),
                'fields' => array(
                    'ServiceDriver.id',
                    'ServiceDriver.name_' . $lang
                )
            )
        );
    }
}
