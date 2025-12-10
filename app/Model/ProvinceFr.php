<?php
class ProvinceFr extends AppModel{
    public $useTable = 'provinces';
    public $useDbConfig = 'gnmaag_fr';

    var $hasMany = array(
        'Garage',
    );

    public $belongsTo = array(
        'Country',
    );

    /**
     * Busca las provincias de cada country.
     *
     * @param $country_id
     * @return array
     */
    public function getProvincesByCountry( $country_id ){
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'ProvinceFr.country_id' => $country_id,
                ),
                'fields' => array(
                    'ProvinceFr.name'
                ),
                'order' =>array(
                    'ProvinceFr.name'
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
                            'Country.id = ProvinceFr.country_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'ProvinceFr.id' => $province_id,
                ),
                'fields' => array(
                    'ProvinceFr.country_id, ProvinceFr.province_code, Country.name,Country.country_code'
                ),
            )
        );
    }

}













