<?php
class HoldReasonType extends AppModel
{
    public $useTable = 'hold_reason_types';

	public $validate = array(
		'name_en' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_MEDIO),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_fr' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_MEDIO),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_de' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_MEDIO),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_nl' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_MEDIO),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
		'name_es' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_MEDIO),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public function getList()
	{
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'HoldReasonType.id',
                    'HoldReasonType.name' . __s(),
                ),
                'order' => 'HoldReasonType.name' . __s(),
            )
        );
    }

	public function getListByRegion($aag_region_id)
	{
		$conditions = array('HoldReasonType.aag_region_id' => $aag_region_id);
		
        return $this->find(
            'list',
            array(
				'conditions' => $conditions,
                'fields' => array(
                    'HoldReasonType.id',
                    'HoldReasonType.name' . __s(),
                ),
                'order' => 'HoldReasonType.name' . __s(),
            )
        );
    }

    public function add_hold_reason_type($data)
    {
		$fields = array(
			'HoldReasonType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
                'aag_region_id',
			)
		);
		$this->create();

		$hold_reason_type_bd = $this->guardar($data, $fields);
		if (!$hold_reason_type_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

    public function edit_hold_reason_type($data)
	{
		$fields = array(
			'HoldReasonType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
			)
		);

		$hold_reason_type_bd = $this->guardar($data, $fields);
		if (!$hold_reason_type_bd) {
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
