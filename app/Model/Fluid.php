<?php
class Fluid extends AppModel
{
    public $useTable = "fluids";
    public $hasMany = array(
        "GarageNetworkFluid"
    );

    public $validate = array(
		'code' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public function updateFluid($fluid, $parentCode = null)
    {
        $fluid['Fluid']['active'] = ConstantsBooleans::ACTIVE;
        $fluid['Fluid']['parent_code'] = $parentCode;
        return $this->save($fluid);
    }

    public function disableFluid($fluidNewtworkId)
    {
        $fluid = array('Fluid' => array(
            'id' => $fluidNewtworkId,
            'active' => ConstantsBooleans::NO_ACTIVE
        ));
        $fields = array('Fluid'=> array('active'));
        return $this->save($fluid, true, $fields);
    }

    public function saveFluid($fluid, $networkId, $parentCode = null)
    {
        $fluid["network_id"] = $networkId;
        $fluid["parent_code"] = $parentCode;
		$fluid["has_advanced_settings"] = $fluid['hasAdvancedSettings'] ?? ConstantsBooleans::NO_ACTIVE;
        $fluid["active"] = ConstantsBooleans::ACTIVE;

        $fluidSave = array("Fluid" => $fluid);

        $this->create();
        return $this->save($fluidSave["Fluid"]);
    }

	public function networkUseFluids($networkId, $workDealer) {
		$this->Genart = ClassRegistry::init('Genart');
		$parentFluids = $this->find('all', array(
            'conditions' => array(
                'network_id' => $networkId,
                'parent_code' => null,
				'has_advanced_settings' => ConstantsBooleans::NO_ACTIVE,
                'active' => ConstantsBooleans::ACTIVE
            ),
        ));

		// If dealer, show all except fluids with advanced settings
		// Delete fluid without grouping associated -> if there is no dealer job
		if (!$workDealer) {
			foreach ($parentFluids as $key => $parentFluid) {
				if (!$this->Genart->hasGenartGroupingAssociated(ConstantsTypesGenartsLeadGen::FLUID, $parentFluid['Fluid']['code'])) {
					unset($parentFluids[$key]);
				}
			}
		}

		if (isset($parentFluids) && !empty($parentFluids) > 0) {
			return true;
		}
		return false;
	}

    public function getAllByAagRegionId($aagRegionId) {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Network.id = Fluid.network_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Network.aag_region_id' => $aagRegionId,
                ),
                'fields' => array(
                    'Fluid.*',
                ),
            )
        );
    }
}
