<?php

class AssociationType extends AppModel
{
	public $useTable = 'associations_types';

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

	public function search_list($aag_region_id)
	{
		return $this->find('list', array(
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

	public function getList()
	{
		return $this->find(
			'list',
			array(
				'fields' => array(
					'AssociationType.id',
					'AssociationType.name' . __s(),
				),
				'order' => 'AssociationType.name' . __s(),
			)
		);
	}

	public function add_association_type_type($data)
	{
		$fields = array(
			'AssociationType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
				'aag_region_id',
			)
		);
		$this->create();

		$association_type_bd = $this->guardar($data, $fields);
		if (!$association_type_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function edit_association_type($data)
	{
		$fields = array(
			'AssociationType' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
			)
		);

		$association_type_bd = $this->guardar($data, $fields);
		if (!$association_type_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getData($aag_region_id)
	{
		return $this->find(
			'all',
			array(
				'conditions' => array(
					'aag_region_id' => $aag_region_id
				),
				'order' => array(
					'name_' . __l() => 'asc'
				)
			)
		);
	}

	public function getAssociation()
	{
		return $this->find(
			'list',
			array(
				'joins' => array(
					array(
						'alias' => 'Distributor',
						'table' => 'distributors',
						'type' => 'INNER',
						'conditions' => array(
							'AssociationType.id = Distributor.association_type_id'
						)
					)
				),
				'fields' => array(
					'id',
					'name_' . __l()
				)
			)
		);
	}
}
