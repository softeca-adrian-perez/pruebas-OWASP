<?php
class LeavingReasonType extends AppModel
{
    public $useTable = 'leaving_reason_types';

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
                    'LeavingReasonType.id',
                    'LeavingReasonType.name_' . __l(),
                ),
                'order' => 'LeavingReasonType.name_' . __l(),
            )
        );
    }

	public function getListByRegion($aag_region_id)
	{
		$conditions = array('LeavingReasonType.aag_region_id' => $aag_region_id);

        return $this->find(
            'list',
            array(
				'conditions' => $conditions,
                'fields' => array(
                    'LeavingReasonType.id',
                    'LeavingReasonType.name_' . __l(),
                ),
                'order' => 'LeavingReasonType.name_' . __l(),
            )
        );
    }

	public function getListByRegionWithoutInactive($aag_region_id)
	{
		$conditions = array('LeavingReasonType.aag_region_id' => $aag_region_id);
		
        $reasons = $this->find(
            'list',
            array(
				'conditions' => $conditions,
                'fields' => array(
                    'LeavingReasonType.id',
                    'LeavingReasonType.name_' . __l(),
                ),
                'order' => 'LeavingReasonType.name_' . __l(),
            )
        );
		$key = array_search('Inactive', $reasons);
    	unset($reasons[$key]);
		return $reasons;
    }

    public function add_leaving_reason_type($data)
    {
		$fields = array(
			'LeavingReasonType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
                'aag_region_id',
			)
		);
		$this->create();

		$leaving_reason_type_bd = $this->guardar($data, $fields);
		if (!$leaving_reason_type_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

    public function edit_leaving_reason_type($data)
	{
		$fields = array(
			'LeavingReasonType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
			)
		);

		$leaving_reason_type_bd = $this->guardar($data, $fields);
		if (!$leaving_reason_type_bd) {
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

	public function getInactiveIdByRegion($aag_region_id)
	{
        return $this->find(
            'first',
            array(
				'conditions' => array(
					'LeavingReasonType.name_en' => ConstantsReasonsLeaving::INACTIVE,
					'LeavingReasonType.aag_region_id' => $aag_region_id
				),
                'fields' => array(
                    'LeavingReasonType.id',
					'LeavingReasonType.name_'.__l(),
                ),
            )
        );
    }
}
