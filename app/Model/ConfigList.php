<?php

class ConfigList extends AppModel
{
	public $useTable = 'lists';
	public $displayField = 'name';

	public $validate = array(
		'name' => array(
			array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Validation.Mandatory_to_choose_a_name',
			),
		)
	);

	public function getList($user_aag_region_id) {
		return $this->find('list', array(
			'conditions' => array(
				'OR' => array(
					'aag_region_id IS NULL',
					'aag_region_id' => $user_aag_region_id
				),
            ),
			'order' => array(
				'name' => 'asc'
			)
		));
	}
}
