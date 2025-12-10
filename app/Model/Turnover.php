<?php

class Turnover extends AppModel{
    public $useTable = 'turnovers';

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
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            // 'order' => array(
            //     'name_' . __l()
            // )
        ));
    }

    public function add_turnover($data) 
    {
		$fields = array(
			'Turnover' => array(
				'name_en',
				'name_fr',
				'name_de',
			)
		);
		$this->create();

		$turnover_bd = $this->guardar($data, $fields);
		if (!$turnover_bd) {
			return false;
		}

		$this->commit();
		return $turnover_bd;
	}

    public function edit_turnover($data)
	{
		$fields = array(
			'Turnover' => array(
				'name_en',
				'name_fr',
				'name_de',
			)
		);

		$turnover_bd = $this->guardar($data, $fields);
		if (!$turnover_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getData() {
		return $this->find('all', array(
			'order' => array(
				'name_' . __l() => 'asc'
				)
			)	
		);
	}

}