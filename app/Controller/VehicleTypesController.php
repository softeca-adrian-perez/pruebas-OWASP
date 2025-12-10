<?php
class VehicleTypesController extends AppController
{
    public $uses = array(
        'VehicleType',
        'GarageVehicleType',
        'GarageNetworkVehicleType',
        'VehicleType',
        'Language'
    );

    /**
     * Vehicle types maintenance page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::VEHICLE_TYPES, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $selectedLanguage = 'name_' . $this->Session->read('Auth.User.language_code');
            $vehicleTypes = $this->VehicleType->find(
                'all',
                array(
                    'order' => $selectedLanguage,
                )
            );

            $this->set(
                array(
                    'languages' => $this->Language->getLanguagesCodeWithoutLoco(),
                    'vehicle_types' => $vehicleTypes,
                    'selected_language' => $selectedLanguage,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX create/edit VehicleType.
     */
    public function ajax_add_edit_vehicle_type($vehicle_type_id = null)
    {
        $this->verify_ajax($this->request);

        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::VEHICLE_TYPES, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            if (!$this->request->is('get')) {
                $this->addEditVehicleType($vehicle_type_id);
                $selectedLanguage = 'name_' . $this->Session->read('Auth.User.language_code');
                $vehicleTypes = $this->VehicleType->find(
                    'all',
                    array(
                        'order' => $selectedLanguage,
                    )
                );
                $this->set(array(
                    'selected_language' => $selectedLanguage,
                    'vehicle_types' => $vehicleTypes,
                ));

                $this->layout = null;
                $this->render('../VehicleTypes/Elements/table_vehicle_types');
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    private function addEditVehicleType($vehicle_type_id = null)
    {
        $flag = $this->checkNames($this->request->data);

        if ($flag == ConstantsFlag::ERROR_NO) {
            $flag = true;
            $checkImage = false;
            if ($this->request->data['VehicleType']['file']['size'] == 0 && $vehicle_type_id == null) { // Addd -> tiene que llegar imagen
                $flag = false;
            } elseif ($this->request->data['VehicleType']['file']['size'] > 0) {
                $checkImage = FileManager::check_image($this->request->data['VehicleType']['file'], $this->request->data['VehicleType']['new_image']);
                if ($checkImage == ConstantsFileErrorTypes::OK) {
                    $file_name = FileManager::upload_image_webroot($this->request->data['VehicleType']['new_image'], $this->request->data['VehicleType']['file'], ConstantsFileType::IMAGE, FilePaths::ICONS_IMAGES_RELATIVE);
                    if (!$file_name) {
                        $flag = false;
                    }
                }
            }

            if ($flag) {
                if (!$checkImage || $checkImage == ConstantsFileErrorTypes::OK) {
                    $languages = $this->Language->getLanguagesCodeWithoutLoco();
                    $vehicleTypeBd = isset($vehicle_type_id) ? $this->VehicleType->findById($vehicle_type_id) : array();
                    foreach ($languages as $language) {
                        $vehicleTypeBd['VehicleType']['name_' . $language] = $this->request->data['VehicleType']['name_' . $language];
                    }
                    if (isset($file_name)) {
                        $vehicleTypeBd['VehicleType']['url'] = $file_name;
                    }

                    $result = isset($vehicle_type_id) ? $this->VehicleType->save($vehicleTypeBd) : $this->VehicleType->new_vehicle_type($vehicleTypeBd);
                    if ($result) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    }
                } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                    $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                }
            } else {
                if (!$flag) {
                    if ($this->request->data['VehicleType']['file']['size'] == 0) {
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
     * AJAX delete VehicleType.
     */
    public function ajax_delete_vehicle_type($vehicle_type_id)
    {
        $this->verify_ajax($this->request);
        $vehicleType = $this->VehicleType->findById($vehicle_type_id);

        if (
            $vehicleType &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::VEHICLE_TYPES, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            if (!$this->request->is('get')) {
                $garages_networks_vehicle_type = $this->GarageNetworkVehicleType->find('all', array(
                    'conditions' => array(
                        'vehicle_type_id' => $vehicle_type_id,
                    )
                ));
                $garages_vehicle_type = $this->GarageVehicleType->find('all', array(
                    'conditions' => array(
                        'vehicle_type_id' => $vehicle_type_id,
                    )
                ));
                foreach ($garages_networks_vehicle_type as $garage_network_vehicle_type) {
                    $this->GarageNetworkVehicleType->delete($garage_network_vehicle_type['GarageNetworkVehicleType']['id']);
                }
                foreach ($garages_vehicle_type as $garage_vehicle_type) {
                    $this->GarageVehicleType->delete($garage_vehicle_type['GarageVehicleType']['id']);
                }
                if (!isset($error)) {
                    $delete = $this->VehicleType->delete($vehicle_type_id);
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
                $vehicleTypes = $this->VehicleType->find(
                    'all',
                    array(
                        'order' => $selectedLanguage,
                    )
                );
                $this->set(array(
                    'selected_language' => $selectedLanguage,
                    'vehicle_types' => $vehicleTypes,
                ));

                $this->layout = null;
                $this->render('../VehicleTypes/Elements/table_vehicle_types');
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    private function checkNames()
    {
        $flag = ConstantsFlag::ERROR_NO;
        if (empty($this->request->data['VehicleType']['name_en'])) {
            $flag = ConstantsFlag::ERROR_NAME_EMPTY;
        } else {
            $languages = $this->Language->getLanguagesCodeWithoutLoco();
            foreach ($languages as $language) {
                if (empty($this->request->data['VehicleType']['name_' . $language])) {
                    $this->request->data['VehicleType']['name_' . $language] = $this->request->data['VehicleType']['name_en'];
                }
            }
        }
        return $flag;
    }
    /**
     *  API to get all the vehicle types.
     */
    public function get_vehicle_types()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {
            $sendData = array('vehicle_types' => array());

            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : '';

            if ($networkId == '') {
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

            $vehicleTypes = $this->VehicleType->find("all", array(
                "fields" => array("id", "name_en", "name_fr", "name_de", "name_nl")
            ));

            foreach ($vehicleTypes as $vehicle_type) {
                $sendData['vehicle_types'][] = array(
                    'id' => $vehicle_type["VehicleType"]["id"],
                    'name' => $vehicle_type["VehicleType"]["name_" . $lang]
                );
            }

            return $this->returnJsonResult($sendData);
        }
    }

    /**
     *  Get info vehicle type.
     */
    public function getDataVehicleType()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::VEHICLE_TYPES, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            if (isset($this->request->query['id']) && !empty($this->request->query['id'])) {
                $languages = $this->Language->getLanguagesCodeWithoutLoco();
                $vehicleType = $this->VehicleType->findById($this->request->query['id']);
                if (isset($vehicleType['VehicleType']) && !empty($vehicleType['VehicleType'])) {
                    foreach ($languages as $language) {
                        $vehicleTypeData[$language] = $vehicleType['VehicleType']['name_' . $language] ?? '';
                    }
                }
            }
            $this->autoRender = false;
            $this->response->type('json');
            echo json_encode(array('vehicleType' => $vehicleTypeData ?? []));
        } else {
            throw new UnauthorizedException();
        }
    }
}
