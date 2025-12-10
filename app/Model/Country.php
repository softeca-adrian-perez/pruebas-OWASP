<?php
class Country extends AppModel{
    public $useTable = 'countries';
    public $displayField = 'name';
    public $order = 'name';

    var $hasMany = array(
        'Province',
    );

    public $belongsTo = array(
        'AagRegion',
    );

    public $validate = array(
        'name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'country_code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_CODE),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'country_code_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_CODE),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'image' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function get_list(){
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            )
        ));
    }

    public function get_list_conditions($conditions){
        return $this->find('list', array(
            'conditions' => $conditions,
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function get_country_by_region($region_id){
        return $this->find('list', array(
            'conditions' => array(
                'aag_region_id =' => $region_id
            )
        ));
    }

    public function get_country_by_province($province_id){
        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'Province',
                    'table' => 'provinces',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Province.country_id = Country.id'
                    )
                ),
            ),
            'conditions' => array(
                'Province.id =' => $province_id
            )
        ));
    }

    public function get_country_by_city($city_id){
        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'City',
                    'table' => 'cities',
                    'type' => 'INNER',
                ),
                array(
                    'alias' => 'Province',
                    'table' => 'provinces',
                    'type' => 'INNER',
                    'conditions' => array(
                        'City.province_id = Province.id',
                        'Province.country_id = Country.id'
                    )
                ),
            ),
            'conditions' => array(
                'City.id' => $city_id,
            )
        ));
    }

    public function get_country_available_sendgrid($region_id){
        return $this->find('list', array(
            'conditions' => array(
                'aag_region_id =' => $region_id
            )
        ));
    }
}