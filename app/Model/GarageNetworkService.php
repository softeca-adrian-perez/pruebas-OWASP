<?php
class GarageNetworkService extends AppModel
{
    public $useTable = 'garages_networks_services';

    var $hasOne = array(
        'Service',
    );

    public function findGarageNetworkServices($garageNetworkId)
    {
        return $this->find(
            "list",
            array(
                "conditions" => array("garage_network_id" => $garageNetworkId),
                "fields" => "GarageNetworkService.service_id"
        ));
    }

    public function findServiceListByGarageNetworkId($garageNetworkId, $selectedServices, $lang)
    {

        if (empty($garageNetworkId) && empty($selectedServices)) {
            return array();
        }

        if (!empty($selectedServices) && empty($garageNetworkId)) {
            $conditions = array(
                'GarageNetworkService.service_id IN' => $selectedServices,
                'GarageNetworkService.service_id = Service.id'
            );
        } else {
            $conditions = array(
                'GarageNetworkService.garage_network_id IN' => $garageNetworkId,
                'GarageNetworkService.service_id = Service.id'
            );
        }

        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Service',
                        'table' => 'services',
                        'type' => 'INNER',
                        'conditions' => $conditions,
                    ),
                ),
                'fields' => array(
                    'Service.id',
                    'Service.name_' . $lang
                )
            )
        );
    }
}
