<?php

class GenartMaster extends AppModel
{
    public $useTable = 'genarts_master';

    public function get_genarts_by_network($network_id, $genart_family_id = null)
    {
        $query = $this->find('list', array(
            'conditions' => array(
                'network_id' => $network_id,
                'active' => ConstantsBooleans::ACTIVE,
                'is_labour_time' => ConstantsBooleans::NO_ACTIVE,
                'or' => array(
                    'genart_family_id' => $genart_family_id,
                    'genart_family_id IS NULL'
                )
            ),
            'fields' => array(
                'id',
                'name_en',
                'name_fr',
                'name_de',
                'name_nl',
                'name_es',
                'network_id',
                'genart_family_id',
            ),
        ));

        return $query;
    }

    public function find_genarts($genart_family_id, $network_id)
    {
        $query = $this->find('list', array(
            'conditions' => array(
                'network_id' => $network_id,
                'genart_family_id' => $genart_family_id
            ),
            'fields' => array(
                'id',
            ),
        ));
        return $query;
    }

    public function find_genarts_masters($genart_family_id, $network_id)
    {
        $query = $this->find('list', array(
            'conditions' => array(
                'network_id' => $network_id,
                'genart_family_id' => $genart_family_id,
                'is_labour_time' => ConstantsBooleans::NO_ACTIVE,
                'active' => ConstantsBooleans::ACTIVE
            ),
            'fields' => array(
                'id',
                'code',
            ),
        ));
        return $query;
    }

    public function find_by_genart_family_id($genart_family_id, $network_id)
    {
        $query = $this->find('list', array(
            'conditions' => array(
                'network_id' => $network_id,
                'genart_family_id' => $genart_family_id
            ),
            'fields' => array(
                'genart_family_id',
                'code',
            ),
        ));
        return $query;
    }

    public function edit($genarts_master)
    {
        $fields = array(
            'GenartMaster' => array(
                'id',
                'name_en',
                'name_fr',
                'name_de',
                'name_nl',
                'name_es',
                'min_labour_price',
                'max_labour_price',
                'slider_increment',
                'network_id',
                'genart_family_id'
            )
        );

        return $this->guardar($genarts_master, $fields);
    }

    public function get_genarts_is_labour_by_network($network_id, $garageNetworkId)
    {
        return $this->find('all', array(
            'conditions' => array(
                'network_id' => $network_id,
                'active' => ConstantsBooleans::ACTIVE,
                'is_labour_time' => ConstantsBooleans::ACTIVE,
            ),
            'joins' => array(
                array(
                    'table' => 'garages_networks_genarts_master',
                    'alias' => 'GarageNetworkGenartMaster',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GenartMaster.id = genart_master_id',
                        'GarageNetworkGenartMaster.garage_network_id = ' . $garageNetworkId
                    ),
                )
            ),
            'fields' => array(
                'id',
                'name_en',
                'name_fr',
                'name_de',
                'name_nl',
                'name_es',
                'min_labour_price',
                'max_labour_price',
                'include_vat',
                'price_labour_time',
                'slider_increment',
                'GarageNetworkGenartMaster.genart_master_labour_price'
            )
        ));
    }

    public function removeFamiliesFromNotActiveGenarts($network_id){
        $families = $this->find('all', array(
            'conditions' => array(
                'network_id' => $network_id,
                'or' => array(
                    'active' => ConstantsBooleans::NO_ACTIVE,
                    'is_labour_time' => ConstantsBooleans::ACTIVE
                )
            ),
            'fields' => array(
                'GenartMaster.*'
            )
        ));

        foreach ($families as $key => $family) {
            $families[$key]['GenartMaster']['genart_family_id'] = null;
        }

        return $this->saveAll($families);
    }

	public function removeFamiliesFromNotAdvancedSettingsGenarts($network_id){
		$this->Fluid = ClassRegistry::init('Fluid');
		$fluidsNotAdvancedSettings = $this->Fluid->find('all', array(
			'conditions' => array(
                'network_id' => $network_id,
                'active' => ConstantsBooleans::ACTIVE,
                'has_advanced_settings' => ConstantsBooleans::NO_ACTIVE
            ),
            'fields' => array(
                'code'
            )
		));

		foreach ($fluidsNotAdvancedSettings as $key => $fluidNotAdvancedSettings) {
			$codes[] = ConstantsTypesGenartsLeadGen::FLUID . $fluidNotAdvancedSettings['Fluid']['code'];
		}

		if (isset($codes) && !empty($codes)) {
			$families = $this->find('all', array(
				'conditions' => array(
					'network_id' => $network_id,
					'active' => ConstantsBooleans::ACTIVE,
					'code IN' => $codes,
					'genart_family_id IS NOT NULL',
				),
				'fields' => array(
					'GenartMaster.*'
				)
			));

			foreach ($families as $key => $family) {
				$families[$key]['GenartMaster']['genart_family_id'] = null;
			}
			return $this->saveAll($families);
		}
		return true;
    }

    public function getLabourPriceGenart($genartCode, $networkId, $garageNetworkId){
        return $this->find('first', array(
            'conditions' => array(
                'code' => $genartCode,
                'network_id' => $networkId
            ),
            'joins' => array(
                array(
                    'table' => 'garages_networks_genarts_master',
                    'alias' => 'GarageNetworkGenartMaster',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GenartMaster.id = GarageNetworkGenartMaster.genart_master_id',
                        'GarageNetworkGenartMaster.garage_network_id = ' . $garageNetworkId
                    ),
                )
            ),
            'fields' => array(
                'GenartMaster.id',
                'GarageNetworkGenartMaster.genart_master_labour_price'
            ),
        ));
    }

    public function getAllByAagRegionId($aagRegionId)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Network.id = GenartMaster.network_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Network.aag_region_id' => $aagRegionId,
                ),
                'fields' => array(
                    'GenartMaster.*',
                ),
            )
        );
    }
}
