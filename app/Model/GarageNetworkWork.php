<?php
class GarageNetworkWork  extends AppModel
{
    public $useTable = 'garages_networks_works';
    public $belongsTo = array("Work");

    public function findGarageNetworkWorks($garageNetworkId)
    {
        return $this->find(
            "list",
            array(
                "conditions" => array("garage_network_id" => $garageNetworkId),
                "fields" => "GarageNetworkWork.work_id"
        ));
    }
}
