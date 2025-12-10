<?php
class Work  extends AppModel
{
    public $useTable = 'works';

    public $validate = array(
        'name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function findWorksNetworkActive($networkId)
    {
        return $this->find(
            "all",
            array(
                "conditions" => array(
                    "network_id" => $networkId,
                    "active" => "1"
                ),
                "order" => 'name_' . __l() . ' asc'
            )
        );
    }

    public function findWorksInNetworks($networkIds)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetworkWork',
                        'table' => 'garages_networks_works',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Work.id = GarageNetworkWork.work_id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.id = GarageNetworkWork.garage_network_id',
                            'GarageNetwork.network_id' => $networkIds,
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        ),
                    ),
                ),
                'conditions' => array(
                    'Work.active' => 1
                ),
                'group' => array(
                    'Work.id, GarageNetwork.network_id',
                ),
                'fields' => array(
                    'Work.*, GarageNetwork.network_id',
                )
            )
        );
    }

    public function findCitiesInNetworkWork($networkId, $workCode)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetworkWork',
                        'table' => 'garages_networks_works',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Work.id = GarageNetworkWork.work_id'
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.id = GarageNetworkWork.garage_network_id',
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
                    'Work.code' => $workCode,
                    'Work.network_id' => $networkId
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

    public function findGarageNetworkWorks($garageNetworkId)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetworkWork',
                        'table' => 'garages_networks_works',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetworkWork.work_id = Work.id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.id = GarageNetworkWork.garage_network_id',
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageNetwork.id' => $garageNetworkId,
                    'Work.active' => 1
                ),
                'fields' => array(
                    'Work.*'
                )
            )
        );
    }

    /**
     * Find standard or fluid genart in specific work
    */

    public function workHasStandardFluidsGenarts($workId)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Genart',
                        'table' => 'genarts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Work.grouping_genart_id = Genart.grouping_genart_id',
                            'Genart.active' => ConstantsBooleans::ACTIVE,
                            'Genart.code NOT LIKE' => ConstantsTypesGenartsLeadGen::DUMMY . '%'
                        ),
                    ),
                ),
                'conditions' => array(
                    'Work.id' => $workId,
                ),
                'fields' => array(
                    'Genart.code'
                )
            )
        );
    }
}
