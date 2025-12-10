<?php

class GenartFamily extends AppModel
{
    public $useTable = 'genarts_families';

    public $validate = array(
        'name_en' => array(
            array(
                'rule' => 'notBlank',
                'required' => 'true',
                'message' => 'Validation.Mandatory_to_choose_a_name',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            )
        ),
        'network_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => 'true',
            )
        ),
        'genarts' => array(
            array(
                'rule' => array('multiple', array('min' => 1)),
                'required' => 'true',
                'message' => 'Validation.Mandatory_to_choose_a_genart'
            )
        )
    );

    public function conditions($fields)
    {
        $conditions = array();

        if(isset($fields['network_id'])){
            $conditions[] = $this->_conditionNetworkId($fields['network_id']);
        }
        if(!empty($fields['name_'.__l()])){
            $conditions[] = $this->_conditionName($fields['name_'.__l()]);
        }
        if(isset($fields['active']) && $fields['active'] !== ''){
            $conditions[] = $this->_conditionActive($fields['active']);
        }

        return $conditions;
    }

    public function _conditionNetworkId( $networkId)
    {
        return array('network_id' => $networkId);
    }

    public function _conditionName( $name )
    {
        return array('name_'.__l().' LIKE' => '%' . $name . '%');
    }

    public function _conditionActive( $active )
    {
        if($active == '1' || $active == '0' ){
            return array('active' => $active);
        }
    }

    private $_queries = array(
		'search' => array(
			'conditions' => array(
			),
			'order' => array(
				'name_en' => 'asc'
			)
		)
	);

	public function _query( $index )
    {
        return $this->_queries[$index];
    }

    public function add( $data ){
        $fields = array(
            'GenartFamily' => array(
                'name_en',
                'name_es',
                'name_fr',
                'name_nl',
                'name_de',
                'active',
                'network_id',
                'genarts'
            ),
        );

        return $this->guardar($data, $fields);
    }

    public function edit( $genartFamily )
    {
        $fields = array(
            'GenartFamily' => array(
                'id',
                'name_en',
                'name_es',
                'name_fr',
                'name_nl',
                'name_de',
                'active',
                'network_id',
                'genarts'
            )
        );
        return $this->guardar($genartFamily['GenartFamily'], $fields);
    }

    public function find_by_network_id_active($networkId)
    {
        $query = $this->find('all', array(
            'conditions' => array(
                'network_id' => $networkId,
                'active' => ConstantsBooleans::ACTIVE
            ),
            'fields' => array(
                    'id',
                    'name_en',
                    'name_es',
                    'name_fr',
                    'name_nl',
                    'name_de',
                    'active',
                    'network_id'
                ),
            ));
        return $query;
    }

    public function deleteEmptyFamilies($networkId){
        $families = $this->find('all', array(
            'joins' => array(
                array(
                    'table' => 'genarts_master',
                    'alias' => 'GenartMaster',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GenartMaster.network_id = ' . $networkId,
                        'GenartFamily.id = GenartMaster.genart_family_id'
                    ),
                )
            ),
            'fields' => array(
                'GenartFamily.*',
                'GenartMaster.*'
            ),
            'conditions' => array(
                'GenartFamily.network_id = ' . $networkId,
                'GenartMaster.id IS NULL'
            )
        ));

        foreach ($families as $family) {
            if (isset($family['GenartFamily']['id']) && !empty($family['GenartFamily']['id'])) {
                $this->delete($family['GenartFamily']['id']);
            }
        }
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
                            'Network.id = GenartFamily.network_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Network.aag_region_id' => $aagRegionId,
                ),
                'fields' => array(
                    'GenartFamily.*',
                ),
            )
        );
    }
}