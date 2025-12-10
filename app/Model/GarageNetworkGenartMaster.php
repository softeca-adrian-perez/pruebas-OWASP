<?php

class GarageNetworkGenartMaster extends AppModel
{
    public $useTable = 'garages_networks_genarts_master';

    public function getGenartByGarageNetworkIdAndGenartMasterId($garage_network_id, $genart_master_id){
        return $this->find('first',
            array(
                'conditions' => array(
                    'garage_network_id' => $garage_network_id,
                    'genart_master_id' => $genart_master_id,
                ),
            )
        );
    }

	public function getGaragesOutsideInterval($networkId, $genartMasterId, $data) {
		return $this->find(
			'list',
			array(
				'joins' => array(
					array(
						'alias' => 'GarageNetwork',
						'table' => 'garages_networks',
						'type' => 'INNER',
						'conditions' => array(
							'GarageNetwork.id = GarageNetworkGenartMaster.garage_network_id',
						),
					),
					array(
						'alias' => 'GarageNetworkGenart',
						'table' => 'garages_networks_genarts',
						'type' => 'INNER',
						'conditions' => array(
							'GarageNetwork.id = GarageNetworkGenart.garage_network_id',
						),
					),
					array(
						'alias' => 'Garage',
						'table' => 'garages',
						'type' => 'INNER',
						'conditions' => array(
							'Garage.id = GarageNetwork.garage_id',
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.network_id' => $networkId,
					'GarageNetworkGenartMaster.genart_master_id' => $genartMasterId,
					array(
						'OR' => array(
							'GarageNetworkGenartMaster.genart_master_labour_price <' => $data['min_labour_price'],
							'GarageNetworkGenartMaster.genart_master_labour_price >' => $data['max_labour_price'],
							'GarageNetworkGenart.is_labour_price <' => $data['min_labour_price'],
							'GarageNetworkGenart.is_labour_price >' => $data['max_labour_price'],
						)
					)
				),
				'fields' => array(
					'Garage.name'
				)
			)
		);
	}

    public function getAllByAagRegionId($aagRegionId)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageNetwork.id = GarageNetworkGenartMaster.garage_network_id',
                        ),
                    ),
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Garage.id = GarageNetwork.garage_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Garage.aag_region_id' => $aagRegionId,
                ),
                'fields' => array(
                    'GarageNetworkGenartMaster.*',
                ),
            )
        );
    }
}
