<?php
class ServicesController extends AppController
{
    public $uses = array(
        'Service',
        'GarageService',
        'GarageNetworkService',
        'VehicleType',
        'Language'
    );

    /**
     * Services maintenance page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::SERVICES, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $selectedLanguage = 'name_' . $this->Session->read('Auth.User.language_code');
            $services = $this->Service->find(
                'all',
                array(
                    'order' => $selectedLanguage,
                )
            );

            $this->set(
                array(
                    'languages' => $this->Language->getLanguagesCodeWithoutLoco(),
                    'services' => $services,
                    'selected_language' => $selectedLanguage,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create/edit service. Only for Super Admin.
     */
    public function ajax_add_edit_service($service_id = null)
    {
        $this->verify_ajax($this->request);

        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
            if (!$this->request->is('get')) {
                $this->addEditService($service_id);
                $selectedLanguage = 'name_' . $this->Session->read('Auth.User.language_code');
                $services = $this->Service->find(
                    'all',
                    array(
                        'order' => $selectedLanguage,
                    )
                );
                $this->set(array(
                    'selected_language' => $selectedLanguage,
                    'services' => $services,
                ));

                $this->layout = null;
                $this->render('../Services/Elements/table_services');
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Private add/edit service.
     */
    private function addEditService($service_id = null)
    {
        $flag = $this->checkNames($this->request->data);

        if ($flag == ConstantsFlag::ERROR_NO) {
            $flag = true;
            $checkImage = false;
            if ($this->request->data['Service']['file']['size'] == 0 && $service_id == null) { // Addd -> tiene que llegar imagen
                $flag = false;
            } elseif ($this->request->data['Service']['file']['size'] > 0) {
                $checkImage = FileManager::check_image($this->request->data['Service']['file'], $this->request->data['Service']['new_image']);
                if ($checkImage == ConstantsFileErrorTypes::OK) {
                    $file_name = FileManager::upload_image_webroot($this->request->data['Service']['new_image'], $this->request->data['Service']['file'], ConstantsFileType::IMAGE, FilePaths::ICONS_IMAGES_RELATIVE);
                    if (!$file_name) {
                        $flag = false;
                    }
                }
            }

            if ($flag) {
                if (!$checkImage || $checkImage == ConstantsFileErrorTypes::OK) {
                    $languages = $this->Language->getLanguagesCodeWithoutLoco();
                    $serviceBd = isset($service_id) ? $this->Service->findById($service_id) : array();
                    foreach ($languages as $language) {
                        $serviceBd['Service']['name_' . $language] = $this->request->data['Service']['name_' . $language];
                    }
                    if (isset($file_name)) {
                        $serviceBd['Service']['url'] = $file_name;
                    }
                    $service = $this->Service->new_edit_service($serviceBd, isset($service_id) ? false : true);
                    if ($service) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    }
                } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                    $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                }
            } else {
                if (!$flag) {
                    if ($this->request->data['Service']['file']['size'] == 0) {
                        $this->Session->setFlashError(__t(ConstantsAlertsErrors::ERROR_NOT_FILE));
                    } else {
                        $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }
        } else {
            $this->Session->setFlashError(__t(ConstantsAlertsErrors::ERROR_NAME_EMPTY));
        }
    }

    /**
     * Delete service. Only for Super Admin.
     */
    public function ajax_delete_service($service_id)
    {
        $this->verify_ajax($this->request);

        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
            if (!$this->request->is('get')) {
                $garagesNetworksService = $this->GarageNetworkService->find('all', array(
                    'conditions' => array(
                        'service_id' => $service_id,
                    )
                ));
                $garagesService = $this->GarageService->find('all', array(
                    'conditions' => array(
                        'service_id' => $service_id,
                    )
                ));
                foreach ($garagesNetworksService as $garageNetworkService) {
                    if (!$this->GarageNetworkService->delete($garageNetworkService['GarageNetworkService']['id'])) {
                        $error = ConstantsAlertsErrors::ERROR_GENERAL;
                    }
                }
                foreach ($garagesService as $garageService) {
                    if (!$this->GarageService->delete($garageService['GarageService']['id'])) {
                        $error = ConstantsAlertsErrors::ERROR_GENERAL;
                    }
                }
                if (!isset($error)) {
                    $serviceFile = $this->Service->findById($service_id);
                    $file = "." . FilePaths::ICONS_IMAGES_RELATIVE . $serviceFile['Service']['url'];
                    $numberServices = $this->Service->find('count', array(
                        'conditions' => array(
                            'url' => $serviceFile['Service']['url']
                        )
                    ));
                    $numberVehicleTypes = $this->VehicleType->find('count', array(
                        'conditions' => array(
                            'url' => $serviceFile['Service']['url']
                        )
                    ));
                    if ($numberServices == 1 && $numberVehicleTypes == 0 && file_exists($file)) {
                        unlink($file);
                    }

                    $delete = $this->Service->delete($service_id);
                    if (!$delete) {
                        $error = ConstantsAlertsErrors::ERROR_GENERAL;
                    }
                }

                if (isset($error)) {
                    $this->Session->setFlashError(__t($error));
                } else {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                }

                $selectedLanguage = 'name_' . $this->Session->read('Auth.User.language_code');
                $services = $this->Service->find(
                    'all',
                    array(
                        'order' => $selectedLanguage,
                    )
                );
                $this->set(array(
                    'selected_language' => $selectedLanguage,
                    'services' => $services,
                ));

                $this->layout = null;
                $this->render('../Services/Elements/table_services');
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    private function checkNames()
    {
        $flag = ConstantsFlag::ERROR_NO;
        if (empty($this->request->data['Service']['name_en'])) {
            $flag = ConstantsFlag::ERROR_NAME_EMPTY;
        } else {
            $languages = $this->Language->getLanguagesCodeWithoutLoco();
            foreach ($languages as $language) {
                if (empty($this->request->data['Service']['name_' . $language])) {
                    $this->request->data['Service']['name_' . $language] = $this->request->data['Service']['name_en'];
                }
            }
        }
        return $flag;
    }

    /**
     *  API to get all the services for a list of networks (with garages associated)
     */
    public function get_services()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && !$this->request->is('get')) {
            $networksIds = isset($dataReceived->networks) ? $dataReceived->networks : null;
            $sendData = array('services' => array());

            if (empty($networksIds)) {
                return $this->returnJsonResult($sendData);
            }
            $services = $this->Service->findServicesInNetworks($networksIds);

            foreach ($networksIds as $networkId) {
                $servicesArray = array();
                foreach ($services as $service) {
                    if ($service['GarageNetwork']['network_id'] == $networkId) {
                        $servicesArray[] = array(
                            'code' => $service['Service']['id'],
                            'languages' => array(
                                'en' => $service['Service']['name_en'],
                                'fr' => $service['Service']['name_fr'],
                                'de' => $service['Service']['name_de'],
                                'nl' => $service['Service']['name_nl'],
                                'es' => $service['Service']['name_es']
                            )
                        );
                    }
                }

                $sendData['services'][] = array(
                    'network_id' => $networkId,
                    'services' => $servicesArray
                );
            }

            return $this->returnJsonResult($sendData);
        }
    }

    /**
     *  API to get all the locations (cities) for a network and service.
     */
    public function get_locations_service()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && !$this->request->is('get')) {
            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $serviceId = isset($dataReceived->service_id) ? $dataReceived->service_id : null;

            $sendData = array('locations' => array());

            if (empty($networkId) || empty($serviceId)) {
                return $this->returnJsonResult($sendData);
            }

            $cities = $this->Service->findCitiesInNetworkService($networkId, $serviceId);

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
     *  Get info service.
     */
    public function getDataService()
    {
        if (isset($this->request->query['id']) && !empty($this->request->query['id'])) {
            $languages = $this->Language->getLanguagesCodeWithoutLoco();
            $service = $this->Service->findById($this->request->query['id']);
            if (isset($service['Service']) && !empty($service['Service'])) {
                foreach ($languages as $language) {
                    $serviceData[$language] = $service['Service']['name_' . $language] ?? '';
                }
            }
        }
        $this->autoRender = false;
        $this->response->type('json');
        echo json_encode(array('service' => $serviceData ?? []));
    }
}
