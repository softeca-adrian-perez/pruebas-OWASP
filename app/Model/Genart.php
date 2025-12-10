<?php
class Genart extends AppModel
{
	public $useTable = 'genarts';

    /**
     * This function gets from the genarts of a work every price associated in the family and for the garage network
     * work relationship, also the max and min prices that can be configured. Used to display the advanced job tab.
     * @param network_id id of the garage network relationship.
     * @param groupingId id of the grouping.
     */
    public function getGenartsPerGroupingAndAssociatedPrices($garageNetworkId, $groupingId){
        return $this->find('all', array(
            'joins' => array(
                array(
                    'table' => 'grouping_genarts',
                    'alias' => 'GroupingGenart',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Genart.grouping_genart_id = GroupingGenart.id',
                    )
                ),
                array(
                    'table' => 'genarts_master',
                    'alias' => 'GenartMaster',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'BINARY Genart.code = BINARY GenartMaster.code', //As both codes are not encoded alike they are parsed to Binary to be compared.
                        'GroupingGenart.network_id = GenartMaster.network_id'
                    )
                ),
                array(
                    'table' => 'garages_networks_genarts_master',
                    'alias' => 'GarageNetworkGenartMaster',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GenartMaster.id = GarageNetworkGenartMaster.genart_master_id',
                        'GarageNetworkGenartMaster.garage_network_id =' . $garageNetworkId
                    )
                ),
                array(
                    'table' => 'garages_networks_genarts',
                    'alias' => 'GarageNetworkGenart',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Genart.id = GarageNetworkGenart.genart_id',
                        'GarageNetworkGenart.garage_network_id =' . $garageNetworkId
                    )
                ),
            ),
            'conditions' => array(
                'Genart.grouping_genart_id' => $groupingId,
                'Genart.active' => ConstantsBooleans::ACTIVE
            ),
            'fields' => array(
                'Genart.*',
                'GroupingGenart.id',
                'GroupingGenart.code',
                'GenartMaster.id',
                'GenartMaster.is_labour_time',
                'GenartMaster.min_labour_price',
                'GenartMaster.max_labour_price',
                'GenartMaster.price_labour_time',
                'GenartMaster.slider_increment',
                'GarageNetworkGenartMaster.id',
                'GarageNetworkGenart.discount',
				'GarageNetworkGenart.markup',
				'GarageNetworkGenart.surcharge',
				'GarageNetworkGenart.is_labour_price',
                'GarageNetworkGenartMaster.genart_master_labour_price'
            )
        ));
    }

	public function hasGenartGroupingAssociated($typeGenart, $code) {
		$data = $this->find('first', array(
            'conditions' => array(
                'Genart.code' => $typeGenart . $code,
                'Genart.active' => ConstantsBooleans::ACTIVE
            ),
        ));
		if (isset($data) && !empty($data)) {
			return true;
		}
		return false;
	}

    public function getAllByAagRegionId($aagRegionId)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'table' => 'grouping_genarts',
                    'alias' => 'GroupingGenart',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Genart.grouping_genart_id = GroupingGenart.id',
                    )
                ),
                array(
                    'table' => 'networks',
                    'alias' => 'Network',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GroupingGenart.network_id = Network.id'
                    )
                ),
            ),
            'conditions' => array(
                'Network.aag_region_id' => $aagRegionId,
            ),
            'fields' => array(
                'Genart.*'
            )
        ));
    }
}
