<?php

class GarageFacility extends AppModel{
    public $useTable = 'garages_facilities';

	public $hasMany = array(
		'Garage',
	);

	public function update_garage_facility($garage_id, $data) {
		$old_data = $this->find(
            'all',
            array(
				'conditions' => array(
					'garage_id' => $garage_id
				),
            )
        );

		if ($old_data) {
			foreach ($old_data as $key => $value) {
				$this->delete($value['GarageFacility']['id']);
			}
		}

		$fields = array(
			'GarageFacility' => array(
				'garage_id',
				'facility_id'
			)
		);
		
		if ($data) {
			foreach ($data as $key => $value) {
				$new_entry = array(
					'GarageFacility' => array(
						'garage_id' => $garage_id,
						'facility_id' => $value
					),
				);
				$this->create();
				$garage_facility_bd = $this->guardar($new_entry, $fields);
				if (!$garage_facility_bd) {
					return false;
				}

			}
		}

		$this->Garage = ClassRegistry::init("Garage");
		$this->Garage->edit_modification_date_garage($garage_id);

		$this->commit();
		return true;
	}

	public function get_list($garage_id) {
		return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'table' => 'facilities',
                        'alias' => 'Facility',
                        'type' => 'Inner',
                        'conditions' => array(
                            'Facility.id = GarageFacility.facility_id',
							'GarageFacility.garage_id' => $garage_id,
                        )
                    ),
                ),
                'fields' => array(
                    'Facility.id',
                ),
            )
        );
	}
}