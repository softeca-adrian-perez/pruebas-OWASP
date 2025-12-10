<?php

class NetworkContractType extends AppModel{
    public $useTable = 'networks_contract_types';

	public $validate = array(
		'name_en' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_fr' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_de' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_nl' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public function search_list(){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name_' . __l()
            )
        ));
    }

    public function getList()
	{
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'NetworkContractType.id',
                    'NetworkContractType.name' . __s(),
                ),
                'order' => 'NetworkContractType.name' . __s(),
            )
        );
    }

    public function add_network_contract_type_type($data)
    {
		$fields = array(
			'NetworkContractType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
                'aag_region_id',
			)
		);
		$this->create();

		$network_contract_type_bd = $this->guardar($data, $fields);
		if (!$network_contract_type_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

    public function edit_network_contract_type($data)
	{
		$fields = array(
			'NetworkContractType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
			)
		);

		$network_contract_type_bd = $this->guardar($data, $fields);
		if (!$network_contract_type_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getData($aag_region_id) {
		return $this->find('all', array(
			'conditions' => array(
				'aag_region_id' => $aag_region_id
			),
			'order' => array(
				'name_' . __l() => 'asc'
				)
			)
		);
	}
}