<?php
class VehiclesController extends AppController
{
    public $uses = array(
        'Vehicle',
        'GarageVehicle',
        'Language',
        'Garage',
        'GarageNetwork',
        'GarageNetworkVehicle',
        'GarageNetworkVehicleBlackList',
        'GarageSpecialistMake'
    );

    /**
     * Vehicle maintenance page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::VEHICLES, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $selectedLanguage = 'name_' . $this->Session->read('Auth.User.language_code');
            $vehicles = $this->Vehicle->find(
                'all',
                array(
                    'order' => $selectedLanguage,
                )
            );

            $this->set(
                array(
                    'languages' => $this->Language->getLanguagesCodeWithoutLoco(),
                    'vehicles' => $vehicles,
                    'selected_language' => $selectedLanguage,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX create/edit Vehicle.
     */
    public function ajax_add_edit_vehicle($vehicle_id = null)
    {
        $this->verify_ajax($this->request);

        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::VEHICLES, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            if (!$this->request->is('get')) {
                $flag = $this->checkNames($this->request->data);
                if ($flag == ConstantsFlag::ERROR_NO) {
                    $languages = $this->Language->getLanguagesCodeWithoutLoco();

                    $vehicleBd = isset($vehicle_id) ? $this->Vehicle->findById($vehicle_id) : array();
                    foreach ($languages as $language) {
                        $vehicleBd['Vehicle']['name_' . $language] = $this->request->data['Vehicle']['name_' . $language];
                    }
                    if (isset($file_name)) {
                        $vehicleBd['Vehicle']['url'] = $file_name;
                    }

                    $result = isset($vehicle_id) ? $this->Vehicle->save($vehicleBd) : $this->Vehicle->new_vehicle($vehicleBd);
                    if (!$result) {
                        $error = ConstantsAlertsErrors::ERROR_GENERAL;
                    }
                } else {
                    $error = ConstantsAlertsErrors::ERROR_NAME_EMPTY;
                }
                if (isset($error)) {
                    $this->Session->setFlashError(__t($error));
                } else {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                }

                $selectedLanguage = 'name_' . $this->Session->read('Auth.User.language_code');
                $vehicles = $this->Vehicle->find(
                    'all',
                    array(
                        'order' => $selectedLanguage,
                    )
                );
                $this->set(array(
                    'selected_language' => $selectedLanguage,
                    'vehicles' => $vehicles,
                ));

                $this->layout = null;
                $this->render('../Vehicles/Elements/table_vehicles');
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete Vehicle.
     */
    public function ajax_delete_vehicle($vehicle_id)
    {
        $this->verify_ajax($this->request);
        $vehicle = $this->Vehicle->findById($vehicle_id);

        if (
            $vehicle &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::VEHICLES, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $error = false;

            if (!$this->request->is('get')) {
                $garagesVehicle = $this->GarageVehicle->find('all', array(
                    'conditions' => array(
                        'vehicle_id' => $vehicle_id,
                    )
                ));
                $garagesNetworksVehicle = $this->GarageNetworkVehicle->find('all', array(
                    'conditions' => array(
                        'vehicle_id' => $vehicle_id,
                    )
                ));
                $garagesNetworksVehiclesBlackList = $this->GarageNetworkVehicleBlackList->find('all', array(
                    'conditions' => array(
                        'vehicle_id' => $vehicle_id,
                    )
                ));
                $garagesSpecialistMake = $this->GarageSpecialistMake->find('all', array(
                    'conditions' => array(
                        'vehicle_id' => $vehicle_id,
                    )
                ));
                foreach ($garagesVehicle as $garage_vehicle) {
                    $this->GarageVehicle->delete($garage_vehicle['GarageVehicle']['id']);
                }
                foreach ($garagesNetworksVehicle as $garage_network_vehicle) {
                    if (!$this->GarageNetworkVehicle->delete($garage_network_vehicle['GarageNetworkVehicle']['id'])) {
                        $error = true;
                    }
                }
                foreach ($garagesNetworksVehiclesBlackList as $garage_network_vehicle_black_list) {
                    if (!$this->GarageNetworkVehicleBlackList->delete($garage_network_vehicle_black_list['GarageNetworkVehicleBlackList']['id'])) {
                        $error = true;
                    }
                }
                foreach ($garagesSpecialistMake as $garage_specialist_make) {
                    if (!$this->GarageSpecialistMake->delete($garage_specialist_make['GarageSpecialistMake']['id'])) {
                        $error = true;
                    }
                }

                if (!$error) {
                    if ($this->Vehicle->delete($vehicle_id)) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
                    } else {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::BAD_DELETED));
                    }
                } else {
                    $this->Session->setFlashSuccess(__t(ConstantsAlertsErrors::ERROR_DELETE));
                }

                $selectedLanguage = 'name_' . $this->Session->read('Auth.User.language_code');
                $vehicles = $this->Vehicle->find(
                    'all',
                    array(
                        'order' => $selectedLanguage,
                    )
                );
                $this->set(array(
                    'selected_language' => $selectedLanguage,
                    'vehicles' => $vehicles,
                ));

                $this->layout = null;
                $this->render('../Vehicles/Elements/table_vehicles');
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    private function checkNames()
    {
        $flag = ConstantsFlag::ERROR_NO;
        if (empty($this->request->data['Vehicle']['name_en'])) {
            $flag = ConstantsFlag::ERROR_NAME_EMPTY;
        } else {
            $languages = $this->Language->getLanguagesCodeWithoutLoco();
            foreach ($languages as $language) {
                if (empty($this->request->data['Vehicle']['name_' . $language])) {
                    $this->request->data['Vehicle']['name_' . $language] = $this->request->data['Vehicle']['name_en'];
                }
            }
        }
        return $flag;
    }

    /**
     *  API to get all the vehicles for a list of networks
     */
    public function get_vehicles()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && !$this->request->is('get')) {
            $networksIds = isset($dataReceived->networks) ? $dataReceived->networks : null;

            $sendData = array('brands' => array());

            if (empty($networksIds)) {
                return $this->returnJsonResult($sendData);
            }

            //Selects language.
            $lang = isset($dataReceived->lang) ? $dataReceived->lang : null;

            if (empty($lang)) {
                $lang = 'en';
            }

            $lang = strtolower($lang);

            //Languages codes array.
            $code = $this->Language->find('first', array(
                'conditions' => array(
                    'code != ' => ConstantsLanguages::LOCO_CODE,
                    'code' => $lang
                )
            ));

            if (!$code) {
                return $this->returnJsonResult($sendData);
            }

            foreach ($networksIds as $networkId) {
                $garagesNetworksIdsCounter = $this->GarageNetwork->find('count', array(
                    'conditions' => array(
                        'GarageNetwork.network_id' => $networkId,
                        'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    ),
                    'fields' => 'GarageNetwork.id'
                ));

                $vehiclesBlackListIds = $this->Vehicle->findVehiclesBlackList($garagesNetworksIdsCounter, $networkId);

                // get all vehicles minus $vehiclesBlackListIds
                $vehiclesAll = $this->Vehicle->find('all', array(
                    'conditions' => array(
                        'NOT' => array('Vehicle.id' => $vehiclesBlackListIds)
                    ),
                    'fields' => array(
                        'Vehicle.*'
                    )
                ));

                $brands = array();

                foreach ($vehiclesAll as $vehicle) {
                    $brands[] = array(
                        'id' => $vehicle['Vehicle']['id'],
                        'name' => $vehicle['Vehicle']['name_' . $lang]
                    );
                }
                $sendData['brands'][] = array(
                    'network_id' => $networkId,
                    'brands' => $brands
                );
            }
            return $this->returnJsonResult($sendData);
        }
    }

    /**
     *  API to get all the locations (cities) for a network and vehicle
     */
    public function get_locations_vehicle()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && !$this->request->is('get')) {
            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $vehicleId = isset($dataReceived->vehicle_id) ? $dataReceived->vehicle_id : null;

            if (empty($networkId) || empty($vehicleId)) {
                exit;
            }

            $garagesNetworksVehiclesBlackListIds = $this->GarageNetworkVehicleBlackList->findGarageNetworkVehicleBlackListVehicle($vehicleId);

            $cities = $this->Vehicle->findCitiesInNetworkVehicle($garagesNetworksVehiclesBlackListIds, $networkId);

            $locations = array();

            foreach ($cities as $city) {
                $locations[] = array(
                    'id' => $city['NetworkCity']['city_id'],
                    'name' => $city['City']['name']
                );
            }
            $sendData = array('locations' => $locations);

            return $this->returnJsonResult($sendData);
        }
    }

    /**
     *  Get info vehicle.
     */
    public function getDataVehicle()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::VEHICLES, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            if (isset($this->request->query['id']) && !empty($this->request->query['id'])) {
                $languages = $this->Language->getLanguagesCodeWithoutLoco();
                $vehicle = $this->Vehicle->findById($this->request->query['id']);
                if (isset($vehicle['Vehicle']) && !empty($vehicle['Vehicle'])) {
                    foreach ($languages as $language) {
                        $vehicleData[$language] = $vehicle['Vehicle']['name_' . $language] ?? '';
                    }
                }
            }
            $this->autoRender = false;
            $this->response->type('json');
            echo json_encode(array('vehicle' => $vehicleData ?? []));
        } else {
            throw new UnauthorizedException();
        }
    }
}
