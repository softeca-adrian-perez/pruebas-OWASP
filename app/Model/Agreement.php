<?php

class Agreement extends AppModel
{
	public $useTable = 'agreements';
	public $displayField = 'nombre';

	public $hasAndBelongsToMany = array(
		'Garage' => array(
			'joinTable' => 'garages_agreements',
			'foreignKey' => 'agreement_id',
		),
	);

	public $hasOne = array(
		'AagRegion'
	);

	public $validate = array(
		'nombre' => array(
			array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Validation.Mandatory_to_choose_a_name',
			),
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'codigo' => array(
			array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Validation.Mandatory_to_choose_a_code',
			),
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'aag_region_id' => array(
			array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Validation.Mandatory_to_choose_a_region',
			),
		)
	);

	public function add_agreement($data)
	{
		$fields = array(
			'Garage' => array(
				'nombre',
				'codigo',
				'fecha_creacion',
				'aag_region_id'
			)
		);
		$data['Agreement']['fecha_creacion'] = date('Y-m-d H:i:s');

		$this->create();

		$agreement_bd = $this->guardar($data, $fields);
		if (!$agreement_bd) {
			return false;
		}

		$this->commit();
		return $agreement_bd;
	}

	public function edit_agreement($agreement)
	{
		$fields = array(
			'Agreement' => array(
				'nombre',
				'codigo',
				'aag_region_id'
			)
		);

		$agreement_bd = $this->guardar($agreement['Agreement'], $fields);
		if (!$agreement_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function get_acuerdos_libres($garage_id)
	{
		return $this->find(
			'list',
			array(
				'joins' => array(
					array(
						'alias' => 'GarageAgreement',
						'table' => 'garages_agreements',
						'type' => 'LEFT',
						'conditions' => array(
							'GarageAgreement.agreement_id = Agreement.id',
						),
					),
				),
				'conditions' => array(
					'GarageAgreement.agreement_id IS NULL',
				),
				'fields' => array(
					'Agreement.id',
					'Agreement.nombre'
				)
			)
		);
	}

	public function external_agreements_list($aag_region_id)
	{
		return $this->find('list', array(
			'conditions' => array('Agreement.aag_region_id' => $aag_region_id),
			'fields' => array(
				'id',
				'nombre'
			),
			'order' => array(
				'nombre'
			)
		));
	}
}
