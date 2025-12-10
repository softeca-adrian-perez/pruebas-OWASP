<?php
class City extends AppModel
{
    public $useTable = 'cities';

    public $validate = array(
        'name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function add_city($data)
    {
        $fields = array(
            'City' => array(
                'name',
                'latitude',
                'longitude',
                'province_id'
            )
        );
        $this->create();

        $cities_bd = $this->guardar($data, $fields);
        if (!$cities_bd) {
            return false;
        }

        $this->commit();
        return $cities_bd;
    }

    public function edit_city($data)
    {
        $fields = array(
            'City' => array(
                'name',
                'latitude',
                'longitude',
                'province_id'
            )
        );

        $cities_bd = $this->guardar($data, $fields);
        if (!$cities_bd) {
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
                    'name' => 'asc'
                )
            )
        );
    }

    public function getListByAagRegion($aag_region_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Province',
                        'table' => 'provinces',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Province.id = City.province_id',
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
                'fields' => array(
                    'City.name'
                ),
                'order' => array(
                    'City.name'
                ),
            )
        );
    }

    public function getDataByAagRegion($aag_region_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Province',
                        'table' => 'provinces',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Province.id = City.province_id',
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
                )
            )
        );
    }

    public function getCitiesByProvince($province_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'City.province_id' => $province_id,
                ),
                'fields' => array(
                    'City.name'
                ),
                'order' => array(
                    'City.name'
                ),
            )
        );
    }

    public function getCitiesIdByProvince($provinceId)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'City.province_id' => $provinceId,
                ),
                'fields' => array(
                    'City.id'
                )
            )
        );
    }

    public function getCitiesIdByCountry($countryId)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Province',
                    'table' => 'provinces',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Province.id = City.province_id'
                    )
                ),
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Country.id = Province.country_id'
                    )
                ),
            ),
            'conditions' => array(
                'Province.country_id' => $countryId,
            ),
            'fields' => array(
                'City.id'
            )
        ));
    }

    public function getProvinceByCity($city_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Province',
                        'table' => 'provinces',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Province.id = City.province_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'City.id' => $city_id,
                ),
                'fields' => array(
                    'City.id, City.province_id, Province.id, Province.name, Province.country_id'
                ),
            )
        );
    }

    /**
     * Search a city by the given name in region.
     */
    public function findCityByNameInRegion($cityName, $region)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Province',
                        'table' => 'provinces',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Province.id = City.province_id',
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
                    'City.name' => $cityName,
                    'Country.aag_region_id' => $region
                ),
                'fields' => array(
                    'City.id, City.province_id'
                ),
            )
        );
    }

    public function get_list_conditions($conditions)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Province',
                    'table' => 'provinces',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Province.id = City.province_id'
                    )
                ),
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Country.id = Province.country_id'
                    )
                ),
            ),
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

    public function getCitiesAjax($conditions, $aag_region_id)
    {
        $cities = $this->getCitiesQuery($conditions, $aag_region_id);
        $cities = Hash::combine($cities, '{n}.City.id', array('%s', '{n}.City.name'));

        return $cities;
    }

    public function getCitiesQuery($conditions_ajax = array(), $aag_region_id)
    {
        $conditions_region = array();
        $conditions_region = array('Country.aag_region_id' => $aag_region_id);

        if (isset($conditions_ajax['name']) && !empty($conditions_ajax['name'])) {
            $conditions_ajax = array(
                'OR' => array(
                    'City.name LIKE' => '%' . $conditions_ajax['name'] . '%',
                )
            );
        }

        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Province',
                        'table' => 'provinces',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Province.id = City.province_id'
                        )
                    ),
                    array(
                        'alias' => 'Country',
                        'table' => 'countries',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Country.id = Province.country_id'
                        )
                    ),
                ),
                'conditions' => array(
                    $conditions_ajax,
                    $conditions_region,
                ),
                'fields' => array(
                    'City.id',
                    'City.name'
                ),
                'order' => array(
                    'City.name'
                )
            )
        );
    }

    public function getCityCompleteNamesByListOfIds(array $listOfIds)
    {
        $cities = $this->find('all', array(
            'conditions' => array(
                "City.id" => $listOfIds
            ),
            'fields' => array(
                'City.id',
                'City.name'
            ),
        ));

        if ($cities) {
            foreach ($cities as $city) {
                $resultArray[] = array(
                    'id' => $city['City']['id'],
                    'text' => $city['City']['name'],
                );
            }
        }
        return $resultArray;
    }

    public function getNetworkCityInfo($networkId, $cityId)
    {
        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'NetworkCity',
                    'table' => 'network_cities',
                    'type' => 'INNER',
                    'conditions' => array(
                        'NetworkCity.city_id = City.id',
                        'NetworkCity.city_id' => $cityId,
                        'NetworkCity.network_id' => $networkId,
                    )
                )
            )
        ));
    }
}
