<?php
class CitiesController extends AppController
{
    public $uses = array(
        "City",
        "Network",
        "NetworkCity",
        "Province"
    );

    /**
     * API to get network locations.
     */
    public function get_network_locations()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        $data_received = $this->request->input('json_decode');
        if (!empty($data_received) && !$this->request->is('get')) {
            $network_id = isset($data_received->network) ? $data_received->network : null;
        }

        if (empty($network_id)) {
            return $this->returnJsonResult(array());
        }

        $aagRegionId = $this->Network->get_network_region($network_id);

        if (empty($aagRegionId)) {
            return $this->returnJsonResult(array());
        }

        $joins = array();

        $joins[] = array(
            'alias' => 'Province',
            'table' => 'provinces',
            'type' => 'INNER',
            'conditions' => array(
                'Province.id = City.province_id',
            ),
        );

        $joins[] = array(
            'alias' => 'Country',
            'table' => 'countries',
            'type' => 'INNER',
            'conditions' => array(
                'Country.id = Province.country_id',
                'Country.aag_region_id' => $aagRegionId,
            ),
        );

        $joins[] = array(
            'alias' => 'Garage',
            'table' => 'garages',
            'type' => 'LEFT',
            'conditions' => array(
                'Garage.city_id = City.id',
                'Garage.aag_region_id' => $aagRegionId,
                'Garage.status' => ConstantsGarageStatus::ACTIVE
            ),
        );

        $joins[] = array(
            'alias' => 'GarageNetwork',
            'table' => 'garages_networks',
            'type' => 'LEFT',
            'conditions' => array(
                'GarageNetwork.garage_id = Garage.id',
                'GarageNetwork.network_id' => $network_id,
                'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                'GarageNetwork.last' => ConstantsBooleans::YES,
            ),
        );

        $joins[] = array(
            'alias' => 'NetworkCity',
            'table' => 'network_cities',
            'type' => 'INNER',
            'conditions' => array(
                'NetworkCity.city_id = City.id',
                'NetworkCity.network_id' => $network_id,
            ),
        );

        $find = array(
            'fields' => array('Province.id, Province.name, NetworkCity.city_id, City.name, City.latitude, City.longitude, COUNT(DISTINCT GarageNetwork.garage_id) AS total_garages'),
            'conditions' => 'NetworkCity.city_id IS NOT NULL',
            'joins' => $joins,
            'group' => 'NetworkCity.city_id'
        );

        $cities = $this->City->find('all', $find);

        foreach ($cities as $city) {
            $sendData['network_locations'][] = array(
                'code_province_gnm' => $city['Province']['id'],
                'province_name' => $city['Province']['name'],
                'code_location_gnm' => $city['NetworkCity']['city_id'],
                'name' => $city['City']['name'],
                'latitude' => $city['City']['latitude'],
                'longitude' => $city['City']['longitude'],
                'total_garages' => $city[0]['total_garages'],
            );
        }

        return $this->returnJsonResult($sendData);
    }

    /**
     * AJAX load cities.
     */
    public function ajax_load_cities()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CONFIGURATION, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            ) ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE, ConstantsPermissionsGrouping::CREATE_GARAGE) &&
                CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                !in_array($roleId, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR))
            )
        ) {
            $province_id = $this->request->data['province_id'];
            $cityFieldName = $this->request->data['city_field_name'];
            $citySelected = isset($this->request->data['city_selected']) ?? null;
            $isConfig = $this->request->data['is_config'];

            $cities = $this->City->getCitiesByProvince($province_id);

            $provinces = $this->Province->find('list');
            $countProvinces = count($provinces);

            $this->set(array(
                'cities_list' => $cities,
                'city_field_name' => $cityFieldName,
                'count_provinces' => $countProvinces,
                'city_selected' => $citySelected,
                'select_multiple' => $isConfig
            ));
            $this->layout = null;
            $this->render('../Cities/Elements/ajax_load_cities');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get cities name.
     * Used for dynamic selects.
     */
    public function get_cities_name()
    {
        $this->verify_ajax($this->request);

        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $citiesName = $this->City->getCitiesAjax($this->request->query, $aagRegionId);
        $listCities = array();

        foreach ($citiesName as $key => $city) {
            $listCities[] = array(
                'id' => $key,
                'text' => $city,
            );
        }

        $list_cities_complete['items'] = $listCities;

        return json_encode($list_cities_complete);
    }
}
