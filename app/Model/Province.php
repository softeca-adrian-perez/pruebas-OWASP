<?php
class Province extends AppModel{
    public $useTable = 'provinces';
    public $displayField = 'name';

    var $hasMany = array(
        'Garage',
        'City'
    );

    public $belongsTo = array(
        'Country',
    );

    public $validate = array(
		'name' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'province_code' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_CODE_PROVINCE),
                'message' => 'Validation.Name_is_too_long',
            ),
		)
	);

    /**
     * Busca las provincias de cada country.
     *
     * @param $country_id
     * @return array
     */
    public function get_list(){
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            ),
            'conditions' => array(
                'active' => ConstantsBooleans::YES
            )
        ));
    }

    public function getListByAagRegion($aag_region_id){
        return $this->find(
        'list',
        array(
            'joins' => array(
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Country.id = Province.country_id',
                    ),
                ),
                array(
                    'alias' => 'AagRegion',
                    'table' => 'aag_regions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'AagRegion.id = Country.aag_region_id'
                    )
                ),
            ),
            'conditions' => array(
                'AagRegion.id' => $aag_region_id,
                'Province.active' => ConstantsBooleans::YES
            ),
            'fields' => array(
                'Province.id',
                'Province.name'
			),
			'order' =>array(
				'Province.name'
			),
        ));
    }

    public function getProvincesByCountry( $country_id ){
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Province.country_id' => $country_id,
                    'Province.active' => ConstantsBooleans::YES
                ),
                'fields' => array(
                    'Province.name'
                ),
                'order' =>array(
                    'Province.name'
                ),
            )
        );
    }

    public function getCountryByProvince( $province_id ){
        return $this->find(
            'first',
            array(
                'joins' => array(
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
                    'Province.id' => $province_id,
                ),
                'fields' => array(
                    'Province.country_id, Province.province_code, Country.name,Country.country_code'
                ),
            )
        );
    }

    public function getListByRegion($aag_region_id)
    {
        $conditions = array(
            'Country.aag_region_id' => $aag_region_id,
            'Province.active' => ConstantsBooleans::YES
        );
        return $this->find('list',array(
            'joins' => array(
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Country.id = Province.country_id',
                    ),
                ),
            ),
            'conditions' => $conditions,
            'fields' => array(
                'Province.id',
                'Province.name',
            ),
            'order' => array(
                'Province.name',
            )
        ));
    }

    public function getInactiveProvinceByAagRegion($aag_region_id){
        return $this->find(
        'list',
        array(
            'joins' => array(
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Country.id = Province.country_id',
                    ),
                ),
                array(
                    'alias' => 'AagRegion',
                    'table' => 'aag_regions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'AagRegion.id = Country.aag_region_id'
                    )
                ),
            ),
            'conditions' => array(
                'AagRegion.id' => $aag_region_id,
                'Province.active' => ConstantsBooleans::NO
            ),
            'fields' => array(
                'Province.id',
                'Province.name'
			),
			'order' =>array(
				'Province.name'
			),
        ));
    }
}
