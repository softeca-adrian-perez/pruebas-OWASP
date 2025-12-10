<?php

class Software extends AppModel{
    public $useTable = 'software';

    public $hasOne = array(
        'AagRegion',
    );

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

    public function new_software($software){
        $fields = array(
            'Software' => array(
                'name_en',
                'name_fr',
                'name_de',
                'name_nl',
                'name_es',
                'aag_region_id'
            )
        );

        $this->create();
        $service_bd = $this->guardar($software, $fields);
        if ( !$service_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    private $_queries = array(
        'search' =>
            array(
                'fields' => array(
                    'Software.*',
                )
            ),
    );

    public function _query( $index ){
        return $this->_queries[$index];
    }

    public function search_list($aag_region_id){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'conditions' => array(
				'aag_region_id' => $aag_region_id
			),
            'order' => array(
                'name_' . __l()
            )
        ));
    }

    public function edit_software($data)
	{
		$fields = array(
			'Software' => array(
				'name_en',
				'name_fr',
				'name_de',
                'name_nl',
                'name_es',
			)
		);

		$software_bd = $this->guardar($data, $fields);
		if (!$software_bd) {
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