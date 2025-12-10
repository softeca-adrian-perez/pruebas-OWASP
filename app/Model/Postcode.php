<?php

class Postcode extends AppModel
{
	public $useTable = 'postcodes';
	public $displayField = 'name';

	public $validate = array(
		'name' => array(
			array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Validation.Mandatory_to_choose_a_name',
			),
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		)
	);

	public function add_postcode($data)
	{
		$fields = array(
			'Garage' => array(
				'name'
			)
		);

		$this->create();

		$postcode_bd = $this->guardar($data, $fields);
		if (!$postcode_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function edit_postcode($postcode)
	{
		$fields = array(
			'Postcode' => array(
				'name',
			)
		);

		$postcode_bd = $this->guardar($postcode['Postcode'], $fields);
		if (!$postcode_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getData() {
		return $this->find('all', array(
			'order' => array(
				'name' => 'asc'
				)
			)	
		);
	}

	public function getDataByAagRegion($aag_region_id) {
		return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'PostcodeProvince',
                        'table' => 'postcode_provinces',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PostcodeProvince.postcode = Postcode.id',
                        ),
                    ),
					array(
                        'alias' => 'Province',
                        'table' => 'provinces',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Province.id = PostcodeProvince.province_id',
                        ),
                    ),
					array(
                        'alias' => 'Country',
                        'table' => 'countries',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Country.id = Province.country_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Country.aag_region_id' => $aag_region_id
				),
				'order' => array(
					'Postcode.name' => 'asc'
				),
            )
		);
	}

}
