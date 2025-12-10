<?php

App::uses('HttpSocket', 'Network/Http');

class Webservice{
    var $useTable = false;

    public $validate = array(
        'name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function getAllGarageData($network_guid){
        $garages = $this->_getAllGarages($network_guid);

        $garage_response = array();
        foreach($garages as $garage){
            /* Obtener imagen? */
            /* Obtener los communications (acuerdos de internacional)? */
            /* ID de la base de datos */
            /* Country mejor un objeto con el codigo de pais de la ISO 3166-1 https://es.wikipedia.org/wiki/ISO_3166-1 */
            /* Province mejor un objeto con el codigo de provincia de la ISO 3166-2:ES */

            $services = $this->_getServicesOfGarage($garage);
            $vehicles_types = $this->_gatVehiclesTypesOfGarage($garage);

            $garage_response[] = array(
                'GeneralData' => $this->_getGeneralData($garage),
                'Manager' => $this->_getManager($garage),
                'Company' => $this->_getCompany($garage),
                'Members' => $this->_getMembers($garage),
                'Coordinators' => $this->_getCoordinators($garage),
                'OpeningHours' => $this->_getOpeningHours($garage),

                //Services
                //EUROGAGE
                'PassengersVehicle' => $this->_getPassengersVehicleServices($services, $network),
                'PassengersDriver' => $this->_getPassengersDriverServices($services, $network),
                //TOP_TRUCK
                'HeavyComVehicle' => $this->_getHeavyComVehicleServices($services, $network),
                'HeavyComDriver' => $this->_getHeavyComDriverServices($services, $network),

                //Vehicle types
                'LightVehicles' => $this->_getLightVehicles($vehicles_types),
                'HeavyVehicles' => $this->_getHeavyVehicles($vehicles_types),
            );
        }
        $garage_response = $this->addProveedorBDS($garage_response);
        return  $garage_response;
    }

    private function _getAllGarages($network_guid){
        $this->Garage = ClassRegistry::init("Garage");

        $params = array(
            'joins' => array(
                array(
                    'table' => 'provinces',
                    'alias' => 'Province',
                    'type' => 'left',
                    'conditions' => array(
                        'Province.id = Garage.province_id',
                    )
                ),
                array(
                    'table' => 'countries',
                    'alias' => 'Country',
                    'type' => 'left',
                    'conditions' => array(
                        'Country.id = Province.country_id',
                    )
                ),
                array(
                    'table' => 'garages_networks',
                    'alias' => 'GarageNetwork',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id',
                    )
                ),
                array(
                    'table' => 'networks',
                    'alias' => 'Network',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Network.id = GarageNetwork.network_id',
                    )
                ),
            ),
            'conditions' => array(
                'Network.guid' => $network_guid,
                'Garage.status' => ConstantsGarageStatus::ACTIVE,
            ),
            'fields' => array(
                'Garage.*',
                'Province.*',
                'Country.*',
                'Network.*',
            )
        );

        return $this->Garage->find('all', $params);
    }

    private function _getGeneralData($garage){

        $address = $garage['Garage']['address1'];
        if(!empty($garage['Garage']['address2'])){
            $address .= ', ' . $garage['Garage']['address2'];
        }
        if(!empty($garage['Garage']['address3'])){
            $address .= ', ' . $garage['Garage']['address3'];
        }
        if(!empty($garage['Garage']['address4'])){
            $address .= ', ' . $garage['Garage']['address4'];
        }

        return array(
            'garage_id' =>  $garage['Garage']['id'],
            'garage_name' =>  $garage['Garage']['name'],
            'garage_code' => $garage['Garage']['g_number_id'],
            'creditor_number' => $garage['Garage']['creditor_number'],
            'payment_terms' => $garage['Garage']['payment_terms'],
            'address' => $address,
            'phone' => $garage['Garage']['phone'],
            'phone_international' => $garage['Garage']['phone_international'],
            'fax' => $garage['Garage']['fax'],
            'email' => $garage['Garage']['email'],
            'web' => $garage['Garage']['web'],
            'country' => ($garage['Garage']['province_id'])? $this->_getCountryByProvince($garage['Garage']['province_id']) : '',
            'province' => ($garage['Garage']['province_id'])? $this->_getProvince($garage['Garage']['province_id']) : '',
            'postal_code' => $garage['Garage']['postcode'],
            'town' => $garage['Garage']['town'],
            'latitude' => $garage['Garage']['latitude'],
            'longitude' => $garage['Garage']['longitude'],
            'active' => $garage['Garage']['status'] == ConstantsGarageStatus::ACTIVE ? 1 : 0,
            'leaving_date' => $garage['Garage']['leaving_date'],
            '24h_service' => !empty($garage['Garage']['service_24h_phone']),
            'service_phone_number' => $garage['Garage']['service_24h_phone'],
            'service_phone_international' => $garage['Garage']['service_24h_phone'],
            'official_branded' => null,
            'oem_parts' => null,
            'ecommerce' => $garage['Garage']['leaving_date'],
            'customer_reseller' => null,
            'customer_garages' => null,
            'customer_drives_diy' => null,
            'delivery_counter_fob' => null,
            'delivery_transport_cif' => null,
            'network' => $this->_getNetwork($garage['Network'])
        );
    }

    private function _getNetwork($network){
        $network_result = array();
        $network_result['id'] = $network['id'];
        $network_result['name'] = $network['name'];
        $network_result['network_type'] = $network['network_type'];
        return $network_result;
    }

    private function _getCountryByProvince($province_id){
        $Country = ClassRegistry::init("Country");

        $params = array(
            'joins' => array(
                array(
                    'table' => 'provinces',
                    'alias' => 'Province',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Province.country_id = Country.id',
                    )
                ),
            ),
            'conditions' => array(
                'Province.id' => $province_id,
            ),
            'fields' => array(
                'Country.*',
            )
        );
        $country = $Country->find('first', $params);

        return $country['Country'];
    }

    private function _getProvince($province_id){
        $Province = ClassRegistry::init("Province");

        $params = array(
            'conditions' => array(
                'Province.id' => $province_id,
            ),
            'fields' => array(
                'Province.name, Province.province_code',
            )
        );

        $province = $Province->find('first', $params);

        return $province['Province'];
    }

    //Todo: Queda pendiente devolver el manager. Obtener de garage_contacts_staff
    private function _getManager($garage){

        $manager = array(
            'manager_title' => null,
            'manager_name' => null,
            'manager_surname' => null,
            'manager_phone' => null,
        );

        return $manager;
    }

    private function _getCompany($garage){
        $company = array(
            'company_name' => $garage['Garage']['business_name'],
            'company_address' => null,
            'country' => ($garage['Garage']['province_id'])? $this->_getCountryByProvince($garage['Garage']['province_id']) : '',
            'province' => ($garage['Garage']['province_id'])? $this->_getProvince($garage['Garage']['province_id']) : '',
            'company_postal_code' => null,
            'vat_number' => $garage['Garage']['VAT_code'],
            'company_town' => null,
        );

        return $company;
    }

    private function _getMembers($garage){
        $Member = ClassRegistry::init("Distributor");

        $params = array(
            'joins' => array(
                array(
                    'table' => 'garages_distributors',
                    'alias' => 'GarageDistributor',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageDistributor.distributor_id = Distributor.id',
                    )
                ),
            ),
            'conditions' => array(
                'GarageDistributor.garage_id' => $garage['Garage']['id'],
            ),
            'fields' => array(
                'Distributor.name',
            )
        );
        $members = $Member->find('all', $params);
        return $members;
    }

    private function _getCoordinators($garage){
        return null;
    }

    private function _getOpeningHours($garage){
        return $this->Garage->getOpeningHours($garage);
    }


    private function _getServicesOfGarage($garage){
        $Service = ClassRegistry::init("Service");

        $params = array(
            'joins' => array(
                array(
                    'table' => 'garages_services',
                    'alias' => 'GarageService',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageService.service_id = Service.id',
                    )
                ),
            ),
            'conditions' => array(
                'GarageService.garage_id' => $garage['Garage']['id'],
            ),
            'fields' => array(
                'DISTINCT Service.international_code',
            )
        );
        return $Service->find('all', $params);
    }

    private function _getPassengersVehicleServices($services, $network){
        $services_passenger = array();
        foreach ($services as $service) {
            if($network == ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE){
                switch($service['Service']['international_code']){

                    case ConstantsServiceInternationalCode::BODYWORK:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::MECHANIC:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::ELECTRICITY:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::PAINT:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::FAST_FIT:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::COOL_HEATING:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::INJECTION:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::BRAKES:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::TYRES:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::ELECTRONICS:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::DIAGNOSIS:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                }
            }
        }

        return $services_passenger;
    }

    private function _getPassengersDriverServices($services, $network){
        $services_passenger = array();
        foreach ($services as $service) {
            if($network == ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE){
                switch($service['Service']['international_code']){

                    case ConstantsServiceInternationalCode::TECHNICAL_INSPECTION:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::TOWING:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::COURTESY_CAR:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::PICK_DELIVER:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                }
            }
        }
        return $services_passenger;
    }

    private function _getHeavyComVehicleServices($services, $network){
        $services_passenger = array();
        foreach ($services as $service) {
            if($network == ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE){
                switch($service['Service']['international_code']){

                    case ConstantsServiceInternationalCode::BODYWORK:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::MECHANIC:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::ELECTRICITY:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::PAINT:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::DIAGNOSIS:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::COOL_HEATING:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::INJECTION:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::BRAKES:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::TYRES:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::ELECTRONICS:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::TACHYMETER:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::GEARBOX:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::SUSPENSION:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::ENGINE:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::STEERING_CONTROL:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::REFRIGERATED_VEHICLE:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::FAST_FIT:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::REPAIR_PUNCTURE:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::LIGHTING:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::OZONE_CLEANING:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                }
            }
        }

        return $services_passenger;
    }

    private function _getHeavyComDriverServices($services, $network){
        $services_passenger = array();
        foreach ($services as $service) {
            if($network == ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE){
                switch($service['Service']['international_code']){

                    case ConstantsServiceInternationalCode::TOWING:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::WAITING_ROOM:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::TECHNICAL_INSPECTION:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::COURTESY_CAR:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::WASHING_CLEANING:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::RENTAL_VEHICLE:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                    case ConstantsServiceInternationalCode::PICK_DELIVER:
                        $services_passenger[] = $service['Service']['international_code'];
                        break;

                }
            }
        }

        return $services_passenger;
    }

    private function _gatVehiclesTypesOfGarage($garage){
        $VehicleType = ClassRegistry::init('VehicleType');

        $params = array(
            'joins' => array(
                array(
                    'table' => 'garages_vehicle_types',
                    'alias' => 'GarageVehicleType',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageVehicleType.vehicle_type_id = VehicleType.id',
                    )
                ),
            ),
            'conditions' => array(
                'GarageVehicleType.garage_id' => $garage['Garage']['id'],
            ),
            'fields' => array(
                'DISTINCT VehicleType.international_code',
            )
        );
        return $VehicleType->find('all', $params);
    }

    private function _getLightVehicles($vehicles_types){
        $types = array();
        foreach ($vehicles_types as $vehicle_type) {
            switch($vehicle_type['VehicleType']['international_code']){

                case ConstantsVehicleTypeInternationalCode::PASSENGER_CAR:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;

                case ConstantsVehicleTypeInternationalCode::ALL_TERRAIN:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;

                case ConstantsVehicleTypeInternationalCode::MOTORCYCLES:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;

                case ConstantsVehicleTypeInternationalCode::VANS:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;

                case ConstantsVehicleTypeInternationalCode::LIGHT_COMMERCIAL_VEHICLE:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;

                case ConstantsVehicleTypeInternationalCode::HEAVY_COMMERCIAL_VEHICLE:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;
            }
        }
        return $types;
    }

    private function _getHeavyVehicles($vehicles_types){
        $types = array();
        foreach ($vehicles_types as $vehicle_type) {
            switch($vehicle_type['VehicleType']['international_code']){

                case ConstantsVehicleTypeInternationalCode::BUS_COACH:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;

                case ConstantsVehicleTypeInternationalCode::TRAILER:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;

                case ConstantsVehicleTypeInternationalCode::TRUCKS:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;

                case ConstantsVehicleTypeInternationalCode::LIGHT_VEHICLES:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;

                case ConstantsVehicleTypeInternationalCode::INDUSTRIAL_VAN:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;

                case ConstantsVehicleTypeInternationalCode::HEAVY_DUTY:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;

                case ConstantsVehicleTypeInternationalCode::AGRICULTURAL:
                    $types[] = $vehicle_type['VehicleType']['international_code'];
                    break;

            }

        }
        return $types;
    }

    public function getGarageDataById($garage_id){
        $garages = $this->_getAllGaragesById($garage_id);

        $garage_response = array();
        foreach($garages as $garage){
            /* Obtener imagen? */
            /* Obtener los communications (acuerdos de internacional)? */
            /* ID de la base de datos */
            /* Country mejor un objeto con el codigo de pais de la ISO 3166-1 https://es.wikipedia.org/wiki/ISO_3166-1 */
            /* Province mejor un objeto con el codigo de provincia de la ISO 3166-2:ES */

            $services = $this->_getServicesOfGarage($garage);
            $vehicles_types = $this->_gatVehiclesTypesOfGarage($garage);

            $garage_response[] = array(
                'GeneralData' => $this->_getGeneralData($garage),
                'Manager' => $this->_getManager($garage),
                'Company' => $this->_getCompany($garage),
                'Members' => $this->_getMembers($garage),
                'Coordinators' => $this->_getCoordinators($garage),
                'OpeningHours' => $this->_getOpeningHours($garage),

                //Services
                //EUROGAGE
                'PassengersVehicle' => $this->_getPassengersVehicleServices($services, ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE),
                'PassengersDriver' => $this->_getPassengersDriverServices($services, ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE),
                //TOP_TRUCK
                'HeavyComVehicle' => $this->_getHeavyComVehicleServices($services, ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE),
                'HeavyComDriver' => $this->_getHeavyComDriverServices($services, ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE),

                //Vehicle types
                'LightVehicles' => $this->_getLightVehicles($vehicles_types),
                'HeavyVehicles' => $this->_getHeavyVehicles($vehicles_types),
            );
        }
        return  $garage_response;
    }

    public function getGarageDataByCode($garage_code){
        $garages = $this->_getAllGaragesByCode($garage_code);

        $garage_response = array();
        foreach($garages as $garage){
            /* Obtener imagen? */
            /* Obtener los communications (acuerdos de internacional)? */
            /* ID de la base de datos */
            /* Country mejor un objeto con el codigo de pais de la ISO 3166-1 https://es.wikipedia.org/wiki/ISO_3166-1 */
            /* Province mejor un objeto con el codigo de provincia de la ISO 3166-2:ES */

            $services = $this->_getServicesOfGarage($garage);
            $vehicles_types = $this->_gatVehiclesTypesOfGarage($garage);

            $garage_response[] = array(
                'GeneralData' => $this->_getGeneralData($garage),
                'Manager' => $this->_getManager($garage),
                'Company' => $this->_getCompany($garage),
                'Members' => $this->_getMembers($garage),
                'Coordinators' => $this->_getCoordinators($garage),
                'OpeningHours' => $this->_getOpeningHours($garage),

                //Services
                //EUROGAGE
                'PassengersVehicle' => $this->_getPassengersVehicleServices($services, ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE),
                'PassengersDriver' => $this->_getPassengersDriverServices($services, ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE),
                //TOP_TRUCK
                'HeavyComVehicle' => $this->_getHeavyComVehicleServices($services, ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE),
                'HeavyComDriver' => $this->_getHeavyComDriverServices($services, ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE),

                //Vehicle types
                'LightVehicles' => $this->_getLightVehicles($vehicles_types),
                'HeavyVehicles' => $this->_getHeavyVehicles($vehicles_types),
            );
        }

        return  $garage_response;
    }

    public function getGarageDataFiltered($username = null, $password = null, $ids = null, $nombre = null, $codigo = null, $distribuidor = null, $coordinador = null, $localizacion = null, $categoria = null, $services_searched = null, $network_id = null, $vehicle_type = null, $lat = null, $lng = null){
        $ids != '' ? $ids = explode(',',$ids) : $ids = null;
        $services_searched != '' ? $services_searched = explode(',',$services_searched) : $services_searched = null;
        $vehicle_type != '' ? $vehicle_type = explode(',',$vehicle_type) : $vehicle_type = null;
        $garages = $this->_getDataGaragesFiltered($ids, $nombre, $codigo, $distribuidor, $coordinador, $localizacion, $categoria, $services_searched, $network_id, $vehicle_type, $lat, $lng);

        $garage_response = array();
        foreach($garages as $garage){

            $services = $this->_getServicesOfGarage($garage);
            $vehicles_types = $this->_gatVehiclesTypesOfGarage($garage);

            $garage_response[] = array(
                'GeneralData' => $this->_getGeneralData($garage),
                'Manager' => $this->_getManager($garage),
                'Company' => $this->_getCompany($garage),
                'Members' => $this->_getMembers($garage),
                'Coordinators' => $this->_getCoordinators($garage),
                'OpeningHours' => $this->_getOpeningHours($garage),

                //Services
                //EUROGAGE
                'PassengersVehicle' => $this->_getPassengersVehicleServices($services, ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE),
                'PassengersDriver' => $this->_getPassengersDriverServices($services, ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE),
                //TOP_TRUCK
                'HeavyComVehicle' => $this->_getHeavyComVehicleServices($services, ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE),
                'HeavyComDriver' => $this->_getHeavyComDriverServices($services, ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE),

                //Vehicle types
                'LightVehicles' => $this->_getLightVehicles($vehicles_types),
                'HeavyVehicles' => $this->_getHeavyVehicles($vehicles_types),
            );
        }
        return  $garage_response;
    }

    public function GetDataDistributorsCoordinators($username = null, $password = null, $ids = null){
        $ids != '' ? $ids = explode(',',$ids) : $ids = null;

        $garage_response = array();
        foreach($ids as $id){

            $garage['Garage']['id'] = $id;
            $garage_response[] = array(
                'Members' => $this->_getMembers($garage),
                'Coordinators' => $this->_getCoordinators($garage),
            );
        }

        return  $garage_response;
    }

    private function _getAllGaragesById($garage_id){
        $garage_id != '' ? $garage_id = explode(',',$garage_id) : $garage_id = null;
        $this->Garage = ClassRegistry::init("Garage");

        $params = array(
            'joins' => array(
                array(
                    'table' => 'provinces',
                    'alias' => 'Province',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Province.id = Garage.province_id',
                    )
                ),
                array(
                    'table' => 'countries',
                    'alias' => 'Country',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Country.id = Province.country_id',
                    )
                ),
                array(
                    'table' => 'garages_networks',
                    'alias' => 'GarageNetwork',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id',
                    )
                ),
                array(
                    'table' => 'networks',
                    'alias' => 'Network',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Network.id = GarageNetwork.network_id',
                    )
                ),
            ),
            'conditions' => array(
                'Garage.id' => $garage_id,
                'Garage.status' => ConstantsGarageStatus::ACTIVE,
            ),
            'fields' => array(
                'Garage.*',
                'Province.*',
                'Country.*',
                'Network.*',
            )
        );

        return $this->Garage->find('all', $params);
    }

    private function _getAllGaragesByCode($garage_code){
        $this->Garage = ClassRegistry::init("Garage");

        $params = array(
            'joins' => array(
                array(
                    'table' => 'provinces',
                    'alias' => 'Province',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Province.id = Garage.province_id',
                    )
                ),
                array(
                    'table' => 'countries',
                    'alias' => 'Country',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Country.id = Province.country_id',
                    )
                ),
                array(
                    'table' => 'garages_networks',
                    'alias' => 'GarageNetwork',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id',
                    )
                ),
                array(
                    'table' => 'networks',
                    'alias' => 'Network',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Network.id = GarageNetwork.network_id',
                    )
                ),
            ),
            'conditions' => array(
                'Garage.g_number_id' => $garage_code,
                'Garage.status' => ConstantsGarageStatus::ACTIVE,
            ),
            'fields' => array(
                'Garage.*',
                'Province.*',
                'Country.*',
                'Network.*',
            )
        );

        return $this->Garage->find('all', $params);
    }

    private function _conditionIds($ids){
        return array('Garage.id' => $ids );
    }
    private function _conditionName($name){
        return array('Garage.name LIKE' => '%' . $name . '%');
    }
    private function _conditionCode($code){
        return array('Garage.g_number_id LIKE' => '%' . $code . '%');
    }
    private function _conditionMember($member){
        return array('Distributor.name LIKE' =>  '%' . $member . '%' );
    }
    private function _conditionCoordinator($coordinator){
        return array('OR' => array(
            'User.name LIKE' =>  '%' . $coordinator . '%' ,
            'User.surname LIKE' =>  '%' . $coordinator . '%')
        );
    }
    private function _conditionProvince($province){
        return array('Province.province_code' => $province );
    }
    private function _conditionServices($services){
        return array('Service.international_code' => $services );
    }
    private function _conditionNetwork($network_id){
		if ($network_id == '9999') {
			return array(
				'OR' => array(
					array('GarageNetwork.garage_id IS NULL'),
					array('GarageNetwork.status !=' => ConstantsNetworksStatus::LIVE)
				)
			);
		} else {
			return array('GarageNetwork.network_id' => $network_id );
		}
    }
    private function _conditionVehicleType($vehicle_type){
        return array('VehicleType.international_code' => $vehicle_type );
    }

    public function conditions($fields){
        $conditions = array();

        if(isset($fields['ids']) && $fields['ids']!=='') {
            $conditions[] = $this->_conditionIds($fields['ids']);
        }
        if(isset($fields['nombre']) && $fields['nombre']!==''){
            $conditions[] = $this->_conditionName($fields['nombre']);
        }
        if(isset($fields['codigo_red']) && $fields['codigo_red']!==''){
            $conditions[] = $this->_conditionCode($fields['codigo_red']);
        }
        if(isset($fields['distribuidor']) && $fields['distribuidor']!==''){
            $conditions[] = $this->_conditionMember($fields['distribuidor']);
        }
        if(isset($fields['coordinador']) && $fields['coordinador']!==''){
            $conditions[] = $this->_conditionCoordinator($fields['coordinador']);
        }
        if(isset($fields['provincia']) && $fields['provincia']!==''){
            $conditions[] = $this->_conditionProvince($fields['provincia']);
        }
        if(isset($fields['services_searched']) && $fields['services_searched']!==''){
            $conditions[] = $this->_conditionServices($fields['services_searched']);
        }
        if(isset($fields['network_id']) && $fields['network_id']!==''){
            $conditions[] = $this->_conditionNetwork($fields['network_id']);
        }
        if(isset($fields['vehicle_type']) && $fields['vehicle_type']!==''){
            $conditions[] = $this->_conditionVehicleType($fields['vehicle_type']);
        }
        return $conditions;
    }

    private function _getDataGaragesFiltered($ids = null, $nombre= null, $codigo= null, $distribuidor= null, $coordinador= null, $provincia= null, $categoria= null, $services_searched = null, $network_id = null, $vehicle_type = null, $lat = null, $lng = null){
        $this->Garage = ClassRegistry::init("Garage");

        $fields = array();

        !is_null($ids) ? $fields['ids'] = $ids :$fields['ids'] = '';
        !is_null($nombre) ? $fields['nombre'] = $nombre : $fields['nombre'] = '';
        !is_null($codigo) ? $fields['codigo_red'] = $codigo : $fields['codigo_red'] ='';
        !is_null($distribuidor) ? $fields['distribuidor'] = $distribuidor :$fields['distribuidor'] =  '';
        !is_null($coordinador) ? $fields['coordinador'] = $coordinador : $fields['coordinador'] = '';
        !is_null($provincia) ? $fields['provincia'] = $provincia : $fields['provincia'] = '';
        !is_null($services_searched) ? $fields['services_searched'] = $services_searched : $fields['services_searched'] = '';
        !is_null($network_id) ? $fields['network_id'] = $network_id : $fields['network_id'] = '';
        !is_null($vehicle_type) ? $fields['vehicle_type'] = $vehicle_type : $fields['vehicle_type'] = '';

        $conditions = $this->conditions($fields);

        if (
            empty($nombre) && empty($codigo) &&
            empty($distribuidor) && empty($coordinador) &&
            empty($provincia) && empty($services_searched) &&
            empty($network_id) && empty($vehicle_type) &&
            empty($categoria)
        ) {
            $fields = array(
                'Garage.*',
            );
        } else {
        $fields = array(
            'Garage.*',
            'Province.*',
            'Country.*',
            'Network.*',
        );
        }

        if(empty($provincia) && !empty($lat) && !empty($lng)){
            $conditions[] = array ('Garage.longitude is NOT NULL');
            $conditions[] = array ('Garage.latitude is NOT NULL');
            $order = 'distance ASC';
            $limit = 25;
            $fields[] = 'ACOS(COS(RADIANS(Garage.latitude))
                            * COS(RADIANS('. $lat . '))
                            * COS(RADIANS(Garage.longitude - '. $lng . '))
                            + SIN(RADIANS(Garage.latitude))
                            * SIN(RADIANS('. $lat . '))
                            ) AS distance';
        } else {
            $order = array(
                'Garage.name ASC',
                'Distributor.name DESC',
                'Service.name_en DESC',
                'VehicleType.name_en ASC',
            );
            $limit = null;
        }

        $params = array(
            'joins' => array(
                array(
                    'table' => 'provinces',
                    'alias' => 'Province',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Province.id = Garage.province_id',
                    )
                ),
                array(
                    'table' => 'countries',
                    'alias' => 'Country',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Country.id = Province.country_id',
                    )
                ),
                array(
                    'table' => 'garages_networks',
                    'alias' => 'GarageNetwork',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id',
                    )
                ),
                array(
                    'table' => 'networks',
                    'alias' => 'Network',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Network.id = GarageNetwork.network_id',
                    )
                ),
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.garage_id = Garage.id',
                    )
                ),
                array(
                    'table' => 'garages_distributors',
                    'alias' => 'GarageDistributor',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.id = GarageDistributor.garage_id',
                    )
                ),
                array(
                    'table' => 'distributors',
                    'alias' => 'Distributor',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Distributor.id = GarageDistributor.distributor_id',
                    )
                ),
                array(
                    'table' => 'garages_services',
                    'alias' => 'GarageService',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageService.garage_id = Garage.id',
                    )
                ),
                array(
                    'alias' => 'Service',
                    'table' => 'services',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Service.id = GarageService.service_id',
                    ),
                ),
                array(
                    'alias' => 'GarageVehicleType',
                    'table' => 'garages_vehicle_types',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageVehicleType.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'VehicleType',
                    'table' => 'vehicle_types',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageVehicleType.vehicle_type_id = VehicleType.id',
                    ),
                ),
        ),
            'conditions' => $conditions,
            'fields' => $fields,
            'order' => $order,
            'group' => 'Garage.id',
            'limit' => $limit

        );

        return $this->Garage->find('all', $params);
    }

    public function addProveedorBDS($garages){
        $this->AagRegion = ClassRegistry::init("AagRegion");
        $aag_regions = $this->AagRegion->find('all');
        foreach($aag_regions as $aag_region){
            if(isset($aag_region['AagRegion']['url_rm'])){
                if(REPAIR_MAINTENANCE_SEND_DATA && Configure::read('repair-maintenance.url_24h') != '' && $this->_worksURL($aag_region['AagRegion']['url_rm'] . Configure::read('repair-maintenance.url_24h')) ){
                    $HttpSocket = new HttpSocket(
                        array(
                            'ssl_verify_peer' => false,
                            'ssl_verify_host' => false,
                            'ssl_allow_self_signed' => true,
                            'ssl_verify_peer_name' => false
                        )
                    );
                    foreach($garages as &$garage){
                        $result = $HttpSocket->get($aag_region['AagRegion']['url_rm'] . Configure::read('repair-maintenance.url_24h') . $garage['GeneralData']['garage_id']);
                        $result = json_decode($result->body);
                        $datos = $result->data;
                        $garage['GeneralData']['is_a24h'] = isset($datos->PmTaller->es_servicio_bds)?$datos->PmTaller->es_servicio_bds:0;
                        $garage['GeneralData']['offers_a24h'] = isset($datos->PmTaller->es_proveedor_bds)?$datos->PmTaller->es_proveedor_bds:0;
                        $garage['GeneralData']['service_24h_phone'] = isset($datos->PmTaller->telefono_24h)?$datos->PmTaller->telefono_24h:'';
                        $garage['GeneralData']['rating_rm'] = isset($datos->PmTaller->valoracion)?$datos->PmTaller->valoracion:0;
                        $garage['GeneralData']['bds_agreement'] = isset($datos->PmTaller->bds_agreement)?$datos->PmTaller->bds_agreement:0;
                        $garage['GeneralData']['garage_responsible'] = isset($datos->PmTaller->garage_responsible)?$datos->PmTaller->garage_responsible:0;
                    }
                } else {
                    foreach($garages as &$garage){
                        $garage['GeneralData']['is_a24h'] = 0;
                        $garage['GeneralData']['offers_a24h'] = 0;
                        $garage['GeneralData']['service_24h_phone'] = '';
                        $garage['GeneralData']['rating_rm'] = 0;
                        $garage['GeneralData']['bds_agreement'] = 0;
                        $garage['GeneralData']['garage_responsible'] = 0;
                    }
                }
            }
        }
        return $garages;
    }

    private function _worksURL($url){
        $resURL = curl_init();
        curl_setopt($resURL, CURLOPT_URL, $url);
        curl_setopt($resURL, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($resURL, CURLOPT_HEADERFUNCTION, 'curlHeaderCallback');
        curl_setopt($resURL, CURLOPT_FAILONERROR, 1);
        curl_exec ($resURL);
        $intReturnCode = curl_getinfo($resURL, CURLINFO_HTTP_CODE);
        curl_close ($resURL);
        if ($intReturnCode != 200 && $intReturnCode != 302 && $intReturnCode != 304) {
            return false;
        } else {
            return true;
        }
    }

}