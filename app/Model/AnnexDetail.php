<?php
class AnnexDetail extends AppModel
{
	public $useTable = 'annex_details';

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

	public function search_list_region($aag_region_id)
	{
		return $this->find('list', array(
			'conditions' => array(
				'AnnexDetail.aag_region_id' => $aag_region_id
			),
			'fields' => array(
				'id',
				'name_' . __l()
			),
			'order' => array(
				'name_' . __l()
			)
		));
	}

	public function add_annex_detail($data)
	{
		$fields = array(
			'AnnexDetail' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
				'aag_region_id',
			)
		);
		$this->create();

		$annex_detail_bd = $this->guardar($data, $fields);
		if (!$annex_detail_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function edit_annex_detail($data)
	{
		$fields = array(
			'AnnexDetail' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
			)
		);

		$annex_detail_bd = $this->guardar($data, $fields);
		if (!$annex_detail_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getData()
	{
		return $this->find(
			'all',
			array(
				'order' => array(
					'name_' . __l() => 'asc'
				)
			)
		);
	}

	public function getDataRegion($aag_region_id)
	{
		$conditions = array();
		$conditions = array('AnnexDetail.aag_region_id' => $aag_region_id);

		return $this->find('all', array(
			'conditions' => $conditions,
			'order' => array(
				'name_' . __l() => 'asc'
			)
		));
	}
}
