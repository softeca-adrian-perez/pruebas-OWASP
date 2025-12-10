<?php

class GarageB2bPostcode extends AppModel
{

	public $useTable = 'garages_b2b_postcodes';

	public $hasMany = array(
		'Garage',
	);

	public function update_garage_postcode($garage_id, $oldData, $newData)
	{
		if (!empty($oldData)) {
			$this->deleteAll(['GarageB2bPostcode.postcode_id' => $oldData,'GarageB2bPostcode.garage_id' => $garage_id], false);
		}

		$fields = array(
			'GarageB2bPostcode' => array(
				'garage_id',
				'postcode_id'
			)
		);
		if ($newData) {
			foreach ($newData as $key => $value) {
				$new_entry = array(
					'GarageB2bPostcode' => array(
						'garage_id' => $garage_id,
						'postcode_id' => $value
					),
				);
				$this->create();
				$garage_postcode_bd = $this->guardar($new_entry, $fields);
				if (!$garage_postcode_bd) {
					return false;
				}
			}
		}

		$this->Garage = ClassRegistry::init("Garage");
		$this->Garage->edit_modification_date_garage($garage_id);

		$this->commit();
		return true;
	}

	public function get_list($garage_id)
	{
		return $this->find(
			'list',
			array(
				'joins' => array(
					array(
						'table' => 'postcode_provinces',
						'alias' => 'PostcodeProvince',
						'type' => 'Inner',
						'conditions' => array(
							'PostcodeProvince.id = GarageB2bPostcode.postcode_id',
							'GarageB2bPostcode.garage_id' => $garage_id,
						)
					),
				),
				'fields' => array(
					'PostcodeProvince.id'
				),
			)
		);
	}
}
