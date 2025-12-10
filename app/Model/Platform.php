<?php
class Platform extends AppModel
{
	public $useTable = 'platforms';

	public $hasOne = array(
		'AagRegion'
	);

	public function getPlatformsByAagRegion($aagRegionId)
	{
		return $this->find('all', array(
			'conditions' => array(
				'OR' => array(
					'aag_region_id' => $aagRegionId,
					'aag_region_id IS NULL'
				)
			),
			'order' => 'id ASC'
		));
	}

	public function getPlatformsListByAagRegion($aagRegionId)
	{
		return $this->find('list', array(
			'conditions' => array(
				'OR' => array(
					'aag_region_id' => $aagRegionId,
					'aag_region_id IS NULL'
				)
			),
			'order' => 'id ASC'
		));
	}
}
