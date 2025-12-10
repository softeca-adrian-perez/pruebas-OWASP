<?php
App::uses('Agn', 'Lib');
App::uses('Leadgen', 'Lib');
set_time_limit(60 * 60);

class NetworksController extends AppController
{
    public $uses = array(
        'Network',
        'Contact',
        'ContactList',
        'NetworkContactList',
        'GarageNetwork',
        'TradingGroup',
        'TradingGroupNetwork',
        'NetworkContactBdm',
        'CommunicationNetwork',
        'ShortcutNetwork',
        'SupplierNetwork',
        'PositionConfig',
        'PositionConfigNetwork',
        'SearchNetwork',
        'Country',
        'AagRegion',
        'NetworkRecommended',
        'GenartFamily',
        'GenartMaster',
        'Genart',
        'Fluid',
        'Garage',
        'City',
        'User',
        'Province',
        'Erp',
        'GarageNetworkGenartMaster',
        'DistanceUnit',
        'AnnexDetail',
        'Service',
        'GarageContactBdm',
        'VehicleType',
        'Agreement',
        'GarageAgreement',
        'LanguageWebNetwork',
        'LanguageWebFlag',
        'SendGridEmailTypeTemplate'
    );

    /**
     * Garage networks home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $roleId = $user['role_id'];

            $contact_id = $user['contact_id'];
            $contact = $this->Contact->findById($contact_id);

            $position_id = $contact['Contact']['position_id'];
            $positions_configs = $this->PositionConfig->findAllByPositionId($position_id);

            $networks = array();
            $networks_initial = $this->Network->getAllByPermissionsAndRegion();
            $networks_types = Configure::read('network_types');
            $trading_groups = $this->TradingGroup->getIndependent($aagRegionId);

            if ($roleId != ConstantsRoles::SUPER_ADMIN && !empty($positions_configs)) {
                // Check how many networks has this position
                foreach ($positions_configs as $position_config) {
                    $networks_tmp = $networks_initial;
                    if ($position_config['PositionConfig']['all_networks'] == ConstantsBooleans::YES) {
                        $networks = $networks_tmp;
                        break;
                    } else {
                        $position_config_newtworks = $this->PositionConfigNetwork->findAllByPositionConfigId($position_config['PositionConfig']['id']);
                        foreach ($position_config_newtworks as $position_config_newtwork) {
                            $nerwork_id = $position_config_newtwork['PositionConfigNetwork']['network_id'];
                            foreach ($networks_tmp as $key => $network) {
                                if ($nerwork_id == $network['Network']['id']) {
                                    $networks[] = $network;
                                }
                            }
                        }
                    }
                }
            } else {
                $networks = $networks_initial;
            }

            foreach ($networks as $key => $network) {
                $networks[$key]['Network']['trading_groups'] = $this->TradingGroupNetwork->findAllByNetworkId($network['Network']['id']);
                $networks[$key]['Network']['not_converted'] = count($this->GarageNetwork->getAllByNetworkIdAndStatusAndLast($network['Network']['id'], $aagRegionId, __t(ConstantsNetworksStatus::NOT_CONVERTED), ConstantsBooleans::YES));
                $networks[$key]['Network']['prospect'] = count($this->GarageNetwork->getAllByNetworkIdAndStatusAndLast($network['Network']['id'], $aagRegionId, array(__t(ConstantsNetworksStatus::PROSPECT), __t(ConstantsNetworksStatus::AWAITING_VISIT), __t(ConstantsNetworksStatus::AWAITING_DECISION)), ConstantsBooleans::YES));
                $networks[$key]['Network']['live'] = count($this->GarageNetwork->getAllByNetworkIdAndStatusAndLast($network['Network']['id'], $aagRegionId, __t(ConstantsNetworksStatus::LIVE), ConstantsBooleans::YES));
                $networks[$key]['Network']['unsubscribe'] = count($this->GarageNetwork->getAllByNetworkIdAndStatusAndLast($network['Network']['id'], $aagRegionId, __t(ConstantsNetworksStatus::UNSUBSCRIBE), ConstantsBooleans::YES));
                $networks[$key]['Network']['on_hold'] = count($this->GarageNetwork->getAllByNetworkIdAndStatusAndLast($network['Network']['id'], $aagRegionId, __t(ConstantsNetworksStatus::ON_HOLD), ConstantsBooleans::YES));
                $networks[$key]['Network']['total'] = count($this->GarageNetwork->getAllByNetworkIdAndStatusAndLast($network['Network']['id'], $aagRegionId, array(ConstantsNetworksStatus::NOT_CONVERTED, ConstantsNetworksStatus::PROSPECT, ConstantsNetworksStatus::LIVE, ConstantsNetworksStatus::ON_HOLD), ConstantsBooleans::YES));
                $networks[$key]['Network']['quoting_active'] = count($this->GarageNetwork->getAllByNetworkIdAndQuotingActiveAndLast($network['Network']['id'], $aagRegionId, ConstantsBooleans::YES, ConstantsBooleans::YES));
                $networks[$key]['Network']['enquiries_active'] = count($this->GarageNetwork->getAllByNetworkIdAndEnquiriesActiveAndLast($network['Network']['id'], $aagRegionId, ConstantsBooleans::YES, ConstantsBooleans::YES));
            }

            $this->set(array(
                'networks' => $networks,
                'trading_groups' => $trading_groups,
                'networks_types' => $networks_types,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create GarageNetwork.
     */
    public function add()
    {
        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_NETWORK) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            $presets = Configure::read('Presets');

            $this->setVarDate();

            if (!$this->request->is('get')) {
                $flag = false;
                $errorUploadPin = $this->uploadImages($presets);
                $errorSize = false;
                $checkImage = false;

                if ($this->data['Network']['image']['name'] != "") {
                    if ($this->request->data['Network']['new_image'] != 'false' && !empty($this->request->data['Network']['new_image'])) {
                        $checkImage = FileManager::check_image($this->request->data['Network']['image'], $this->request->data['Network']['new_image']);
                        if ($checkImage == ConstantsFileErrorTypes::OK) {
                            $filename = FileManager::upload_image_webroot($this->request->data['Network']['new_image'], $this->request->data['Network']['image'], ConstantsFileType::IMAGE, FilePaths::NETWORKS_IMAGES_RELATIVE);
                            $this->request->data['Network']['image'] = $filename;
                        }
                    }
                } else {
                    $this->request->data['Network']['image'] = "";
                }

                if (!$checkImage || $checkImage == ConstantsFileErrorTypes::OK) {
                    if (!$flag && !$errorUploadPin && !$errorSize) {

                        $network = $this->Network->new_networks($this->request->data);
                        if ($network) {
                            if ($this->request->data['Network']['internal']) {
                                $this->ApiRm = ClassRegistry::init("ApiRm.ApiRm");
                                $networkAlliance = $this->request->data['Network'];
                                $networkAlliance['id'] = $this->Network->getLastInsertID();
                                $this->ApiRm->transfer_network_to_repair($networkAlliance);
                            }
                            $this->request->data['TradingGroup']['id'] = $this->TradingGroup->getLastInsertID();
                            $msg = h(sprintf(__t('Network.Well_add'), $this->request->data['Network']['name']));
                            $this->Session->setFlashSuccess($msg);
                            $this->redirect(
                                array(
                                    'controller' => 'networks',
                                    'action' => 'edit',
                                    $this->Network->getLastInsertID()
                                )
                            );
                        } else {
                            $this->Session->setFlashError(__t('Network.Error_create'));
                        }
                    } else {
                        if ($flag) {
                            $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                        } elseif ($errorSize) {
                            $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                        } else {
                            $this->Session->setFlashError(__t($errorUploadPin));
                        }
                    }
                } elseif ($check_image == ConstantsFileErrorTypes::SIZE_ERROR) {
                    $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                }
            }

            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $this->setVarForm();
            $this->set(array(
                'contacts_lists' => $this->ContactList->getContactList($aagRegionId),
                'trading_groups' => null,
                'presets' => $presets,
                'network_id' => null,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit GarageNetwork.
     */
    public function edit($network_id)
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $network = $this->Network->findDatas($network_id, $aagRegionId);

        if (
            $network['Network']['id'] &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_NETWORK) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            $presets = Configure::read('Presets');
            $oldImage = $network['Network']['image'];

            $selectedPreset = false;
            foreach ($presets as $key => $preset) {
                if ($preset[0] == $network['Network']['image_cluster'] && $preset[1] == $network['Network']['image_pin']) {
                    $selectedPreset = $key;
                }
            }

            $this->setVarDate();

            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            if (!$this->request->is('get')) {
                $errorSize = false;
                $errorFile = false;
                $checkImage = false;

                if ($this->request->data['Network']['new_image'] == 'false') {
                    $errorFile = true;
                } elseif (!empty($this->request->data['Network']['new_image'])) {
                    $checkImage = FileManager::check_image($this->request->data['Network']['image'], $this->request->data['Network']['new_image']);
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $filename = FileManager::upload_image_webroot($this->request->data['Network']['new_image'], $this->request->data['Network']['image'], ConstantsFileType::IMAGE, FilePaths::NETWORKS_IMAGES_RELATIVE);
                        $this->request->data['Network']['image'] = $filename;
                        $this->request->data['Network']['optimized'] = ConstantsBooleans::NO;
                        if (!$filename) {
                            $errorFile = true;
                        } else {
                            $errorFile = !FileManager::delete_file(WWW_ROOT, FilePaths::NETWORKS_IMAGES_RELATIVE . $oldImage);
                        }
                    }
                } else {
                    $imageName = $this->Network->findById($network_id);
                    $this->request->data['Network']['image'] = $imageName['Network']['image'];
                }

                // upload custom pin and cluster
                $errorUploadPin = $this->uploadImages($presets);

                if (!$checkImage || $checkImage == ConstantsFileErrorTypes::OK) {
                    if (!$errorUploadPin && !$errorSize && !$errorFile) {
                        if ($this->request->data['NetworkContactList']['contact_list_id']) {
                            $oldData = $this->NetworkContactList->find(
                                'all',
                                array(
                                    'conditions' => array(
                                        'NetworkContactList.network_id' => $network_id
                                    )
                                )
                            );

                            $contactListArray = array();
                            foreach ($oldData as $data) {
                                $contactListArray[] = $data['NetworkContactList']['contact_list_id'];
                            }

                            foreach ($this->request->data['NetworkContactList']['contact_list_id'] as $contactListId) {
                                if (!in_array($contactListId, $contactListArray)) {
                                    $this->NetworkContactList->edit($network_id, $contactListId);
                                }
                            }

                            foreach ($contactListArray as $list_id) {
                                if (!in_array($list_id, $this->request->data['NetworkContactList']['contact_list_id'])) {
                                    $this->NetworkContactList->deleteAll(
                                        array(
                                            'NetworkContactList.contact_list_id' => $list_id,
                                            'NetworkContactList.network_id' => $network_id
                                        )
                                    );
                                }
                            }
                        } else {
                            $this->NetworkContactList->deleteAll(
                                array(
                                    'NetworkContactList.network_id' => $network_id
                                )
                            );
                        }

                        $networkDb = $this->Network->edit_network($this->request->data);
                        if ($networkDb) {
                            if ($this->request->data['Network']['internal']) {
                                $this->ApiRm = ClassRegistry::init("ApiRm.ApiRm");
                                $network_alliance = $this->request->data['Network'];
                                $this->ApiRm->transfer_network_to_repair($network_alliance);
                            }
                            $msg = h(sprintf(__t('Network.Well_edit'), $network['Network']['name']));
                            $this->Session->setFlashSuccess($msg);
                            $this->redirect($this->request->here);
                        }
                    } else {
                        if ($errorFile) {
                            $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                        } elseif ($errorSize) {
                            $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                        } else {
                            $this->Session->setFlashError(__t($errorUploadPin));
                        }
                    }
                } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                    $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                }
            } else {
                $network['Network']['date_restarting_credit'] = Fecha::toFormatoVistaFecha($network['Network']['date_restarting_credit']);
                $this->request->data = $network;
            }

            $networksLoginSeo = array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC);

            $trainingTrue = $this->Network->getTrainingByNetworkId($network_id);

            $this->setVarForm();
            $this->set(array(
                'contacts_lists' => $this->ContactList->getContactList($aagRegionId),
                'contact_list_selected' => $this->NetworkContactList->getContactListFromNetwork($network_id),
                'network' => $network,
                'trading_groups' => $this->Network->TradingGroup->getTradingGroupIndependent(),
                'presets' => $presets,
                'selected_preset' => $selectedPreset,
                'network_id' => $network_id,
                'rating' => in_array($network_id, $networksLoginSeo) ? $network['Network']['rating'] : null,
                'total_reviews' => in_array($network_id, $networksLoginSeo) ? $network['Network']['reviews_number'] : null,
                'has_access_login_seo_admin_zone' => in_array($network_id, $networksLoginSeo),
                'training_boolean' => $trainingTrue['Network']['training'] == ConstantsBooleans::ACTIVE ? true : false,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete GarageNetwork.
     */
    public function ajax_delete($network_id)
    {
        $this->verify_ajax($this->request);
        $userAagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $network = $this->Network->findByIdAndAagRegionId($network_id, $userAagRegionId);

        if (
            $network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {

            $can_delete = true;
            $can_delete_text = 'true';
            $error_text = '';

            $models_relationship = $this->getModels();

            if (!empty($network)) {
                foreach ($models_relationship as $model => $translation) {
                    $object = $this->$model->findByNetworkId($network_id);
                    if (!empty($object)) {
                        $can_delete = false;
                        $error_text = $translation;
                        break;
                    }
                }
            }
            if ($can_delete && $network['Network']['internal']) {
                $this->ApiRm = ClassRegistry::init("ApiRm.ApiRm");
                $result = $this->ApiRm->delete_network_in_repair($network['Network']['id']);
                if (!$result) {
                    $can_delete = false;
                    $error_text = __t('Network.This_network_is_already_in_use_in_repair_maintenance');
                }
            }
            if ($can_delete) {
                $conditions = array(
                    'network_id' => $network_id
                );
                //Before deleting Network, delete relationships with positions.
                $positions_bd = $this->PositionConfigNetwork->deleteAll($conditions);

                //Delete the relationship between network and trading group
                $trading_groups_networks_bd = $this->TradingGroupNetwork->deleteAll($conditions);

                if ($positions_bd && $trading_groups_networks_bd) {
                    $net_bd = $this->Network->delete($network_id);
                    if (!$net_bd) {
                        $can_delete_text = 'false';
                    }
                } else {
                    $can_delete_text = 'false';
                }
            } else {
                $can_delete_text = 'false';
            }

            $ret = array(
                'precess' => $can_delete_text,
                'error_text' => $error_text
            );

            $js_array = json_encode($ret);
            echo $js_array;

            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    private function getModels()
    {
        return array(
            'CommunicationNetwork' =>  __t('General.Relation_with') . ' ' . __t('Communication.Communication'),
            'NetworkContactBdm' =>  __t('General.Relation_with') . ' ' . __t('Contact.Contact'),
            'GarageNetwork' =>  __t('General.Relation_with') . ' ' . __t('Garage.Garage'),
            'SearchNetwork' =>  __t('General.Relation_with') . ' ' . __t('General.Search'),
            'ShortcutNetwork' =>  __t('General.Relation_with') . ' ' . __t('Maintenance.Shortcuts'),
            'SupplierNetwork' =>  __t('General.Relation_with') . ' ' . __t('Software.Supplier'),
        );
    }

    private function setVarForm()
    {
        $cancel_action = array(
            'url_cancel' => array(
                'controller' => 'networks',
                'action' => 'home',
            ),
        );

        $networks_types = Configure::read('network_types');

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];
        $conditions = array('AagRegion.id' => $aagRegionId);
        $aagRegions = $this->AagRegion->region_list_conditions($conditions);

        $this->set(array(
            'cancel_action' => $cancel_action,
            'networks_types' => $networks_types,
            'aag_regions' => $aagRegions,
            'user_aag_region_id' => $aagRegionId,
            'user_role_id' => $roleId,
        ));
    }

    private function uploadImages($presets)
    {
        if (isset($this->request->data['RadioGroup']) && $this->request->data['RadioGroup'] == ConstantsNetworksPins::CUSTOM) {

            if (empty($this->data['Network']['image_pin']['name']) || empty($this->data['Network']['image_cluster']['name'])) {
                return __t('General.Custom_cluster_custom_pin_must_be_uploaded');
            }

            $file = new File($this->data['Network']['image_pin']['tmp_name']);
            $tam_img_pin = getimagesize($this->request->data['Network']['image_pin']['tmp_name']);

            if ($tam_img_pin[0] != ConstantsNetworkImageDimension::WIDTH_PIN || $tam_img_pin[1] != ConstantsNetworkImageDimension::HEIGHT_PIN) {
                return ConstantsAlertsErrors::ERROR_DIMENSIONS;
            }

            $filenamePin = FileManager::get_renamed_name($this->data['Network']['image_pin']['name']);
            $data = $file->read();
            $file->close();
            $file = new File(WWW_ROOT . FilePaths::PINS_IMAGES_REALTIVE . $filenamePin, true);
            $file->write($data);
            $file->close();
            $this->request->data['Network']['image_pin'] = $filenamePin;

            // upload pin
            if (!FileManager::upload_file(WWW_ROOT . FilePaths::PINS_IMAGES_REALTIVE . $filenamePin, FilePaths::PINS_IMAGES_REALTIVE, $filenamePin, ConstantsFileType::IMAGE)) {
                return true;
            }

            $file = new File($this->data['Network']['image_cluster']['tmp_name']);
            $tam_img_cluster = getimagesize($this->request->data['Network']['image_cluster']['tmp_name']);
            if ($tam_img_cluster[0] != ConstantsNetworkImageDimension::WIDTH_CLUSTER || $tam_img_cluster[1] != ConstantsNetworkImageDimension::HEIGHT_CLUSTER) {
                return ConstantsAlertsErrors::ERROR_DIMENSIONS;
            }

            $filenameCluster = FileManager::get_renamed_name($this->data['Network']['image_cluster']['name']);
            $data = $file->read();
            $file->close();
            $file = new File(WWW_ROOT . FilePaths::PINS_IMAGES_REALTIVE . $filenameCluster, true);
            $file->write($data);
            $file->close();
            $this->request->data['Network']['image_cluster'] = $filenameCluster;

            // upload cluster
            if (!FileManager::upload_file(WWW_ROOT . FilePaths::PINS_IMAGES_REALTIVE . $filenameCluster, FilePaths::PINS_IMAGES_REALTIVE, $filenameCluster, ConstantsFileType::IMAGE)) {
                return true;
            }

            // if everything is ok
            return false;
        } elseif (isset($this->request->data['RadioGroup'])) {
            $this->request->data['Network']['image_cluster'] = $presets[$this->request->data['RadioGroup']][0];
            $this->request->data['Network']['image_pin'] = $presets[$this->request->data['RadioGroup']][1];
        } else {
            $this->request->data['Network']['image_cluster'] = $presets[ConstantsNetworksPins::DEFAULT][0];
            $this->request->data['Network']['image_pin'] = $presets[ConstantsNetworksPins::DEFAULT][1];
        }
        return false;
    }

    /**
     * AJAX get trading groups.
     */
    public function ajax_get_tradings_groups()
    {
        $this->verify_ajax($this->request);
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
            )
        ) {
            $trading_groups = null;
            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
            $network = array();
            if (!$this->request->is('get')) {
                $data = $this->request->data;
                $network_id = isset($data['network_id']) ? $data['network_id'] : null;
                $network = ($network_id != null) ? $this->Network->findDatas($network_id, $aagRegionId) : array();
                if (isset($data['network_type']) && $data['network_type'] == 'LV') {
                    $trading_groups = $this->Network->TradingGroup->getTradingGroupIndependentByLVType($aagRegionId);
                } elseif (isset($data['network_type']) && $data['network_type'] == 'CV') {
                    $trading_groups = $this->Network->TradingGroup->getTradingGroupIndependentByCVType($aagRegionId);
                } else {
                    $trading_groups = null;
                }
            }

            $this->set(array(
                'trading_groups' => $trading_groups,
                'network' => $network,
            ));

            $this->layout = false;
            $this->render('../Networks/Elements/trading_groups');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarDate()
    {
        $date = getdate();
        $day_week = array(
            '1' => __t('Garage.Monday'),
            '2' => __t('Garage.Tuesday'),
            '3' => __t('Garage.Wednesday'),
            '4' => __t('Garage.Thursday'),
            '5' => __t('Garage.Friday'),
            '6' => __t('Garage.Saturday'),
            '7' => __t('Garage.Sunday'),
        );
        $date = $date['mday'] . '/' . $date['mon'] . '/' . $date['year'];

        $this->set(array(
            'date' => $date,
            'day_week' => $day_week[date('N')],
        ));
    }

    /**
     * Get data map.
     */
    public function getDataMap()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
            )
        ) {
            ini_set('memory_limit', '1G');
            $this->autoRender = null;
            return json_encode($this->Network->findNetworksWithGarages($aagRegionId));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Get data dashboard map used for widgets.
     */
    public function getDataDashboardMap()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
            )
        ) {
            $conditions = [];
            if (isset($this->request->data['network_id'])) {
                $networks_ids = $this->request->data['network_id'];
                $conditions[] = array('Network.id' =>  $networks_ids);
            }
            if (isset($this->request->data['country_id'])) {
                $countries_ids = $this->request->data['country_id'];
                $conditions[] = array('Country.id' =>  $countries_ids);
            }
            if (isset($this->request->data['region_id'])) {
                $regions_ids = $this->request->data['region_id'];
                $conditions[] = array('Network.aag_region_id' =>  $regions_ids);
            }
            $this->autoRender = null;
            return json_encode($this->Network->findNetworksWithGaragesDashboard($conditions));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Api to get the different networks within a country and a region.
     */
    public function get_network_list()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("get")) {
            $sendData = array();
            $countryCode = isset($dataReceived->country_code) ? $dataReceived->country_code : null;

            if (empty($countryCode)) {
                return $this->returnJsonResult($sendData);
            }

            //Converts lang to lower case.
            $countryCode = strtolower($countryCode);

            $country = $this->Country->find('first', array(
                'conditions' => array(
                    'country_code_2' => $countryCode
                )
            ));

            if (!$country) {
                return $this->returnJsonResult($sendData);
            }

            $aagRegionId = $country['Country']['aag_region_id'];

            if (empty($aagRegionId)) {
                return $this->returnJsonResult($sendData);
            }

            $conditions =  array('aag_region_id' => $aagRegionId);
            if (!isset($dataReceived->allNetworks)) {
                $conditions[] = array('quoting_type_id IS NULL', 'pricing_type_id IS NULL');
            }

            $networks = $this->Network->find('all', array(
                'conditions' => $conditions,
                'fields' => array('id', 'name')
            ));

            if (!$networks) {
                return $this->returnJsonResult($sendData);
            }

            foreach ($networks as $network) {
                $networkFinal[] = array(
                    'id' => $network['Network']['id'],
                    'name' => $network['Network']['name']
                );
            }

            if (!isset($networkFinal) || empty($networkFinal)) {
                return $this->returnJsonResult($sendData);
            }

            $sendData['Networks'] = $networkFinal;
            return $this->returnJsonResult($sendData);
        }
    }

    /**
     * Api to get the different networks to display a select in AGN in the search engine.
     */
    public function get_network_search_list()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {
            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : '';

            $networksListToSend = array("networks" => array());

            if (empty($networkId)) {
                return $this->returnJsonResult($networksListToSend);
            }

            $networksIds = $this->GarageNetwork->getOtherNetworksFromGarageNetwork($networkId);

            $networks = $this->Network->find('all', array(
                'conditions' => array(
                    'id' => $networksIds,
                    'id !=' => $networkId, // do not include the network from the input parameter
                    'internal' => true, // only internal networks
                ),
                'fields' => array('id', 'name')
            ));

            foreach ($networks as $network) {
                $networksListToSend["networks"][] = array(
                    "id" => $network["Network"]["id"],
                    "name" => $network["Network"]["name"]
                );
            }

            return $this->returnJsonResult($networksListToSend);
        }
    }

    /**
     * API to update network fields quoting, pricing and vat with data received by POST.
     */
    public function update_quoting_pricing()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {
            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $typeQuoting = isset($dataReceived->type_quoting) ? $dataReceived->type_quoting : null;
            $typePricing = isset($dataReceived->type_pricing) ? $dataReceived->type_pricing : null;
            $withVat = isset($dataReceived->with_vat) ? $dataReceived->with_vat : null;
            $distance_unit_id = isset($dataReceived->distance_unit_id) ?? null;
            $country_code_language = isset($dataReceived->country_code_language) ?  $dataReceived->country_code_language : null;

            $output = null;

            // $with_vat needs to check with null, because false is a valid value
            if (empty($networkId) || empty($typeQuoting) || empty($typePricing) || empty($distance_unit_id) || $withVat === null || $country_code_language === null) {
                $output = array(
                    "success" => false,
                    "error" => 'Fields missing'
                );
            }

            if (empty($output)) {
                // network to update
                $networkUpdate = $this->Network->findById($networkId);
                if (!$networkUpdate) {
                    $output = array(
                        "success" => false,
                        "error" => 'Network not found'
                    );
                }
            }

            if (empty($output)) {
                // update fields with data received
                $networkUpdate["Network"]["quoting_type_id"] = $typeQuoting;
                $networkUpdate["Network"]["pricing_type_id"] = $typePricing;
                $networkUpdate["Network"]["with_vat"] = $withVat;
                $networkUpdate["Network"]["distance_unit_id"] = $distance_unit_id;
                $networkUpdate["Network"]["country_code_language"] = $country_code_language;

                try {
                    if (!$this->Network->save($networkUpdate)) {
                        $output = array(
                            "success" => false,
                            "error" => "Error updating data"
                        );
                    } else {
                        $output = array(
                            "success" => true,
                            "error" => ''
                        );
                    }
                } catch (Exception $e) {
                    $output = array(
                        "success" => false,
                        "error" => "Error saving data"
                    );
                }
            }

            return $this->returnJsonResult($output);
        }
    }

    /**
     * API to get ERP Account Code of a network.
     */
    public function get_network_erp_account_code()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("get")) {
            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : '';

            if (empty($networkId)) {
                return $this->returnJsonResult(array());
            }

            $network = $this->Network->findById($networkId);

            return $this->returnJsonResult(array("erp_account_code" => $network["Network"]["ref_code"]));
        }
    }

    /**
     * Login SEO admin zone for specific network.
     */
    public function login_seo_admin_zone($network_guid)
    {
        if (
            isset($network_guid) &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            $network = $this->Network->findByGuid($network_guid);

            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                $agn = new Agn();
                $urlJson = $agn->getLoginSeoAdminZone($network_guid);
                $url = isset($urlJson['url']) ? $urlJson['url'] : null;

                $redirectError = array(
                    'controller' => 'networks',
                    'action' => 'home'
                );

                if (empty($url)) {
                    $this->redirect($redirectError);
                }

                $prefixFound = false;
                foreach (Configure::read('networks_web_login_prefix') as $url_prefix) {
                    if (substr($url, 0, strlen($url_prefix)) == $url_prefix) {
                        $prefixFound = true;
                        break;
                    }
                }

                if ($prefixFound) {
                    $this->redirect($url);
                } else {
                    $this->redirect($redirectError);
                }
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * API to update network fields quoting, pricing and vat with data received by POST.
     */
    public function update_leadgen_information()
    {
        try {
            $dataReceived = $this->request->input('json_decode');
            $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
            if (!$authOk) {
                $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
                return $this->returnJsonResult($resultado);
            }

            // send response
            $response = array(
                'success' => true,
                'error' => ''
            );
            echo $this->returnJsonResult($response);

            header("Content-Length: 0");
            header('Connection: close');
            flush();
            session_write_close();
            if (is_callable('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }

            // wait 10s
            sleep(10);

            if (!empty($dataReceived) && !$this->request->is('get')) {
                $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
                $type = isset($dataReceived->type) ? $dataReceived->type : null;

                if (empty($networkId) || empty($type)) {
                    exit;
                }

                // network to update
                $networkUpdate = $this->Network->findById($networkId);
                if (!$networkUpdate) {
                    exit;
                }

                $leadgen = new Leadgen();

                switch ($type) {
                    case ConstantsUpdateFromLeadgenInfoTypes::FLUIDS:
                        $leadgen->fluidsSync($networkId);
                        break;
                    case ConstantsUpdateFromLeadgenInfoTypes::SERVICES:
                        $leadgen->worksSync($networkId);
                        //When updating works, genarts and groupings must be updated too
                        $leadgen->workPricesSync($networkId);
                        break;
                    case ConstantsUpdateFromLeadgenInfoTypes::GROUPINGS_DATA:
                        $leadgen->workPricesSync($networkId);
                        break;
                    default:
                        break;
                }
            }
            exit;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Update Leadgen information - An error has occured: " . $e->getMessage(), true));
            exit;
        }
    }

    /**
     * Network families configuration.
     */
    public function families_configuration($networkId)
    {
        $network = $this->Network->findByIdAndAagRegionId($networkId, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {

            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                $active = array(
                    ConstantsBooleans::NO => __t('General.No'),
                    ConstantsBooleans::YES => __t('General.Yes')
                );

                $searcher = $this->request->query;

                $this->request->data['Search'] = $searcher;
                $searcher['network_id'] = $networkId;

                $families = $this->custom_pagination(
                    $this->GenartFamily->_query('search'),
                    $this->GenartFamily->conditions($searcher),
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'GenartFamily'
                );

                $fluids = $this->Fluid->find('all', array(
                    'conditions' => array(
                        'network_id' => $networkId,
                        'name_en is not null',
                        'active' => ConstantsBooleans::ACTIVE
                    ),
                ));

                foreach ($families as &$family) {
                    $genartFamilies = $this->GenartMaster->findAllByGenartFamilyIdAndActiveAndIsLabourTime($family['GenartFamily']['id'], ConstantsBooleans::ACTIVE, ConstantsBooleans::NO_ACTIVE);
                    $family['GenartFamily']['genarts'] = $genartFamilies;
                }

                $genartsNoFamily = $this->GenartMaster->findAllByNetworkIdAndGenartFamilyIdAndActiveAndIsLabourTime($networkId, null, ConstantsBooleans::ACTIVE, ConstantsBooleans::NO_ACTIVE);

                foreach ($genartsNoFamily as $key => $genartNoFamily) {
                    foreach ($fluids as $fluid) {
                        if ($fluid['Fluid']['name_en'] == $genartNoFamily['GenartMaster']['name_en'] && !$fluid['Fluid']['has_advanced_settings']) {
                            unset($genartsNoFamily[$key]);
                        }
                    }
                }

                $all_genarts_master = $this->GenartMaster->findAllByNetworkIdAndIsLabourTimeAndActive($networkId, ConstantsBooleans::ACTIVE, ConstantsBooleans::ACTIVE);

                $this->setVarForm();
                $this->set(array(
                    'network_id' => $networkId,
                    'active' => $active,
                    'families' => $families,
                    'genarts_no_family' => $genartsNoFamily,
                    'all_genarts_master' => $all_genarts_master,
                    'network' => $network
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit GenartMaster labour interval.
     */
    public function edit_genart_labour_interval($network_id, $genart_master_id)
    {
        $network = $this->Network->findByIdAndAagRegionId($network_id, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {

            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                $genartMaster = $this->GenartMaster->findById($genart_master_id);

                if (!isset($genartMaster['GenartMaster']) || $genartMaster['GenartMaster']['is_labour_time'] == ConstantsBooleans::NO_ACTIVE) {
                    $this->Session->setFlashError(__t(ConstantsMessages::NO_PERMISSION));
                    $this->redirect(
                        array(
                            'controller' => 'networks',
                            'action' => 'families_configuration',
                            $network_id
                        )
                    );
                }

                if (!$this->request->is('get')) {
                    $request = $this->request->data;
                    $garageNetworks = $this->GarageNetworkGenartMaster->getGaragesOutsideInterval($network_id, $genart_master_id, $request['GenartMaster']);
                    if (
                        (!isset($garageNetworks) || (isset($garageNetworks) && empty($garageNetworks))) &&
                        isset($request['GenartMaster']['min_labour_price']) && isset($request['GenartMaster']['max_labour_price']) &&
                        isset($request['GenartMaster']['slider_increment'])
                    ) {
                        if (!is_numeric($request['GenartMaster']['min_labour_price']) || !is_numeric($request['GenartMaster']['max_labour_price']) || !is_numeric($request['GenartMaster']['slider_increment']) || $request['GenartMaster']['min_labour_price'] < 0 || $request['GenartMaster']['max_labour_price'] < 0 || $request['GenartMaster']['slider_increment'] <= 0) {
                            $this->Session->setFlashError(__t('Validation.Mandatory_to_numeric_positive'));
                            $this->redirect($this->here);
                        }
                        if ($request['GenartMaster']['min_labour_price'] > $request['GenartMaster']['max_labour_price']) {
                            $this->Session->setFlashError(__t('Validation.Min_max'));
                            $this->redirect($this->here);
                        }
                        $genartMaster['GenartMaster']['min_labour_price'] = $request['GenartMaster']['min_labour_price'];
                        $genartMaster['GenartMaster']['max_labour_price'] = $request['GenartMaster']['max_labour_price'];
                        $genartMaster['GenartMaster']['slider_increment'] = $request['GenartMaster']['slider_increment'];
                        if ($this->GenartMaster->edit($genartMaster)) {
                            $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                            $this->redirect(
                                array(
                                    'controller' => 'networks',
                                    'action' => 'families_configuration',
                                    $network_id
                                )
                            );
                        } else {
                            $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                            $this->redirect($this->here);
                        }
                    }
                }

                $this->setVarForm();
                $this->set(array(
                    'garageNetworks' => $garageNetworks ?? null,
                    'network_id' => $network_id,
                    'genart_master' => $genartMaster
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit Network labour internal.
     */
    public function edit_labour_interval($network_id, $is_electric)
    {
        $network = $this->Network->findByIdAndAagRegionId($network_id, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {

            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                if (!$this->request->is('get')) {
                    $request = $this->request->data;
                    $garageNetworks = $this->GarageNetwork->getGaragesOutsideInterval($network['Network']['id'], $request['Network'], $is_electric);
                    if (!isset($garageNetworks) || (isset($garageNetworks) && empty($garageNetworks))) {
                        if (isset($request['Network']['min_labour_price']) && isset($request['Network']['max_labour_price']) && isset($request['Network']['slider_increment'])) {
                            if (!is_numeric($request['Network']['min_labour_price']) || !is_numeric($request['Network']['max_labour_price']) || !is_numeric($request['Network']['slider_increment']) || $request['Network']['min_labour_price'] < 0 || $request['Network']['max_labour_price'] < 0 || $request['Network']['slider_increment'] < 0) {
                                $this->Session->setFlashError(__t('Validation.Mandatory_to_numeric_positive'));
                                $this->redirect($this->here);
                            }
                            if ($request['Network']['min_labour_price'] > $request['Network']['max_labour_price']) {
                                $this->Session->setFlashError(__t('Validation.Min_max'));
                                $this->redirect($this->here);
                            }
                            if (!$is_electric) {
                                $network['Network']['min_labour_price'] = $request['Network']['min_labour_price'];
                                $network['Network']['max_labour_price'] = $request['Network']['max_labour_price'];
                                $network['Network']['slider_increment'] = $request['Network']['slider_increment'];
                            } else {
                                $network['Network']['min_labour_price_ev'] = $request['Network']['min_labour_price'];
                                $network['Network']['max_labour_price_ev'] = $request['Network']['max_labour_price'];
                                $network['Network']['slider_increment_ev'] = $request['Network']['slider_increment'];
                            }
                            $result = $this->Network->edit_labour_interval($network);

                            if ($result) {
                                $this->Session->setFlashSuccess(__t('RequestedChanges.Request_saved'));
                                $this->redirect(
                                    array(
                                        'controller' => 'networks',
                                        'action' => 'families_configuration',
                                        $network_id
                                    )
                                );
                            } else {
                                $this->Session->setFlashError(__t('RequestedChanges.Request_not_saved'));
                                $this->redirect($this->here);
                            }
                        } else {
                            $this->Session->setFlashError(__t('Config.Empty_field'));
                            $this->redirect($this->here);
                        }
                    }
                }

                $this->setVarForm();
                $this->set(array(
                    'garageNetworks' => $garageNetworks ?? null,
                    'network_id' => $network_id,
                    'network' => $network,
                    'is_electric' => $is_electric
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete GenartMaster labour interval.
     */
    public function ajax_delete_genart_labour_interval($genartMasterId)
    {
        $this->verify_ajax($this->request);
        $this->autoRender = false;

        $genartMaster = $this->GenartMaster->findById($genartMasterId);
        if ($genartMaster) {
            $network = $this->Network->findByIdAndAagRegionId($genartMaster['GenartMaster']['network_id'], CakeSession::read('Auth.User.aag_region_id'));
        }
        if (
            $network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {

            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                $genartMaster['GenartMaster']['min_labour_price'] = null;
                $genartMaster['GenartMaster']['max_labour_price'] = null;
                $genartMaster['GenartMaster']['slider_increment'] = null;
                return $this->GenartMaster->edit($genartMaster);
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete Network labour interval.
     */
    public function ajax_delete_labour_interval($network_id, $is_electric)
    {
        $this->verify_ajax($this->request);
        $this->autoRender = false;
        $network = $this->Network->findByIdAndAagRegionId($network_id, CakeSession::read('Auth.User.aag_region_id'));

        if (
            $network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {

            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                if (!$is_electric) {
                    $network['Network']['min_labour_price'] = null;
                    $network['Network']['max_labour_price'] = null;
                    $network['Network']['slider_increment'] = null;
                } else {
                    $network['Network']['min_labour_price_ev'] = null;
                    $network['Network']['max_labour_price_ev'] = null;
                    $network['Network']['slider_increment_ev'] = null;
                }
                return $this->Network->edit_labour_interval($network);
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add families configuration.
     */
    public function add_families_configuration($networkId)
    {
        $network = $this->Network->findByIdAndAagRegionId($networkId, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {

            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                if (!$this->request->is('get')) {
                    $this->request->data['GenartFamily']['network_id'] = $networkId;
                    if ($this->GenartFamily->add($this->request->data)) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));

                        $genarts = $this->request->data['GenartFamily']['genarts'];
                        foreach ($genarts as $genart) {
                            $genartMaster = $this->GenartMaster->findById($genart);
                            $genartMaster['GenartMaster']['genart_family_id'] = $this->GenartFamily->id;
                            $this->GenartMaster->edit($genartMaster);
                        }

                        $this->redirect(
                            array(
                                'controller' => 'networks',
                                'action' => 'families_configuration',
                                $networkId,
                            )
                        );
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                }

                $active = array(
                    ConstantsBooleans::NO => __t('General.No'),
                    ConstantsBooleans::YES => __t('General.Yes')
                );

                $fluids = $this->Fluid->find('all', array(
                    'conditions' => array(
                        'network_id' => $networkId,
                        'name_en is not null',
                        'active' => ConstantsBooleans::ACTIVE
                    ),
                ));

                $network = $this->Network->getNameByIdNetwork($networkId);
                $genarts = $this->GenartMaster->get_genarts_by_network($networkId);

                foreach ($genarts as $key => $genart) {
                    foreach ($fluids as $fluid) {
                        if ($genart == $fluid['Fluid']['name_en'] && !$fluid['Fluid']['has_advanced_settings']) {
                            unset($genarts[$key]);
                        }
                    }
                }

                $this->setVarForm();
                $this->set(array(
                    'network_id' => $networkId,
                    'active' => $active,
                    'network' => $network,
                    'genarts' => $genarts,
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit GenartFamily.
     */
    public function edit_genart_family($network_id, $family_id)
    {
        $network = $this->Network->findByIdAndAagRegionId($network_id, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                $network = $this->Network->getNameByIdNetwork($network_id);
                $genartFamily = $this->GenartFamily->findById($family_id);
                $genartsMasters = $this->GenartMaster->get_genarts_by_network($network_id, $family_id);
                $selectedGenarts = $this->GenartMaster->find_genarts($family_id, $network_id);

                $fluids = $this->Fluid->find('all', array(
                    'conditions' => array(
                        'network_id' => $network_id,
                        'name_en is not null',
                        'active' => ConstantsBooleans::ACTIVE
                    ),
                ));

                foreach ($genartsMasters as $key => $genart) {
                    foreach ($fluids as $fluid) {
                        if ($genart == $fluid['Fluid']['name_en'] && !$fluid['Fluid']['has_advanced_settings']) {
                            unset($genartsMasters[$key]);
                        }
                    }
                }

                if (!$genartFamily) {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
                    $this->redirect(
                        array(
                            'controller' => 'networks',
                            'action' => 'families_configuration',
                            $network_id
                        )
                    );
                }

                if (!$this->request->is('get')) {
                    $genartFamily = array(
                        'GenartFamily' => array(
                            'id' => $family_id,
                            'name_en' => $this->request->data['GenartFamily']['name_en'],
                            'name_es' => $this->request->data['GenartFamily']['name_es'],
                            'name_fr' => $this->request->data['GenartFamily']['name_fr'],
                            'name_nl' => $this->request->data['GenartFamily']['name_nl'],
                            'name_de' => $this->request->data['GenartFamily']['name_de'],
                            'active' => $this->request->data['GenartFamily']['active'],
                            'network_id' => $network_id,
                            'genarts' => $this->request->data['GenartFamily']['genarts'],
                        )
                    );

                    if ($this->GenartFamily->edit($genartFamily)) {
                        $genartsMasters = $this->request->data['GenartFamily']['genarts'];

                        foreach ($genartsMasters as $genart) {
                            $genartMaster = $this->GenartMaster->findById($genart);
                            $genartMaster['GenartMaster']['genart_family_id'] = $this->GenartFamily->id;
                            $this->GenartMaster->edit($genartMaster);
                        }

                        $genartsFamilyNull = array_diff($selectedGenarts, $genartsMasters);

                        foreach ($genartsFamilyNull as $genartFamilyNull) {
                            $genartMaster = $this->GenartMaster->findById($genartFamilyNull);
                            $genartMaster['GenartMaster']['genart_family_id'] = null;
                            $this->GenartMaster->edit($genartMaster);
                        }

                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect(
                            array(
                                'controller' => 'networks',
                                'action' => 'families_configuration',
                                $network_id
                            )
                        );
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $this->request->data = $genartFamily;
                }

                $active = array(
                    ConstantsBooleans::NO => __t('General.No'),
                    ConstantsBooleans::YES => __t('General.Yes')
                );

                $this->setVarForm();
                $this->set(array(
                    'network_id' => $network_id,
                    'network' => $network,
                    'genarts' => $genartsMasters,
                    'active' => $active,
                    'selected_genarts' => $selectedGenarts,
                    'genart_family' => $genartFamily
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Recommended networks.
     */
    public function recommended_networks($network_id)
    {
        $network = $this->Network->findByIdAndAagRegionId($network_id, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                $user = $this->Acceso->user();
                $aagRegionId = $user['aag_region_id'];
                $config = CakeSession::read('Auth.User.Config');
                $roleId = $user['role_id'];

                if (!$this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)) {
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                } else {
                    $searcher = $this->request->query;
                    $searcher = self::multipleFieldsArrayCheck($searcher);
                    $this->request->data['Search'] = $searcher;

                    if (!empty($searcher['network_id'])) {
                        $searcher['network_id_recommended_quoting'] = $searcher['network_id'];
                        unset($searcher['network_id']);
                    }

                    $conditionsJoins = $this->Garage->conditionsJoins($searcher);
                    $joins = array_merge(
                        array(
                            array(
                                'alias' => 'City',
                                'table' => 'cities',
                                'type' => 'LEFT',
                                'conditions' => 'Garage.city_id = City.id'
                            ),
                            array(
                                'alias' => 'Province',
                                'table' => 'provinces',
                                'type' => 'LEFT',
                                'conditions' => 'City.province_id = Province.id'
                            ),
                            array(
                                'alias' => 'Country',
                                'table' => 'countries',
                                'type' => 'LEFT',
                                'conditions' => 'Province.country_id = Country.id'
                            ),
                            array(
                                'alias' => 'GarageNetwork',
                                'table' => 'garages_networks',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Garage.id = GarageNetwork.garage_id',
                                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                                    'GarageNetwork.network_id' => $network_id,
                                    'GarageNetwork.last' => ConstantsBooleans::YES
                                )
                            ),
                        ),
                        $conditionsJoins
                    );

                    $conditions = $this->Garage->conditions($searcher);
                    $conditions[] = $this->User->viewUserGarages($this->Acceso->user());
                    $conditions[] = array('Garage.status' => ConstantsGarageStatus::ACTIVE);
                    $conditions[] = array('Garage.aag_region_id' => $aagRegionId);

                    $originalNetworkId = $this->Network->findById($network_id)['Network']['id'];
                    $networks = $this->Network->getAllByInternalNetwork($originalNetworkId);

                    $recommendedStatus = array();
                    $recommendedLabelTrue = array();
                    foreach ($networks as $network) {
                        $internalNetworkId = $network['Network']['id'];
                        $recommendedStatus[$internalNetworkId] = $this->NetworkRecommended
                            ->getRecommendedStatus($originalNetworkId, $internalNetworkId);

                        $recommendedLabelInternalNetwork = $this->NetworkRecommended->getRecommendedlabel($originalNetworkId, $internalNetworkId);
                        if ($recommendedLabelInternalNetwork) {
                            $recommendedLabelTrue[$internalNetworkId] = $internalNetworkId;
                        }
                    }

                    if (!$this->request->is('get')) {
                        $check_image = false;
                        $check_image_list = false;
                        // save data from the list of networks
                        foreach ($this->request->data['Network'] as $internalNetworkId => $networkData) {
                            $networkRecommended = $this->NetworkRecommended
                                ->findByNetworkIdAndInternalNetworkId($originalNetworkId, $internalNetworkId);
                            if ($networkRecommended) {
                                // edit, the modal data will be saved later (if it exists)
                                $networkRecommended['NetworkRecommended']['recommended'] = $networkData['recommended'];
                                $this->NetworkRecommended->save($networkRecommended);
                            } else {
                                // create
                                if (isset($this->request->data['NetworkRecommended']['network_recommended_id']) && $internalNetworkId == $this->request->data['NetworkRecommended']['network_recommended_id']) {
                                    // save data from the modal
                                    $recommendedLabel = $this->request->data['NetworkRecommended']['recommended_label'];
                                    $imageRecommended = null;
                                    $imageRecommendedList = null;
                                    if (isset($this->request->data['NetworkRecommended']['new_image_recommended'])) {
                                        $check_image = FileManager::check_image($this->request->data['NetworkRecommended']['image_recommended'], $this->request->data['NetworkRecommended']['new_image_recommended']);
                                        if ($check_image == ConstantsFileErrorTypes::OK) {
                                            $file_name = FileManager::upload_image_webroot(
                                                $this->request->data['NetworkRecommended']['new_image_recommended'],
                                                $this->request->data['NetworkRecommended']['image_recommended'],
                                                ConstantsFileType::IMAGE,
                                                FilePaths::NETWORKS_RECOMMENDED_IMAGES
                                            );
                                            $imageRecommended = $file_name;
                                        }
                                    }
                                    if (isset($this->request->data['NetworkRecommended']['new_image_recommended_list'])) {
                                        $check_image_list = FileManager::check_image($this->request->data['NetworkRecommended']['image_recommended_list'], $this->request->data['NetworkRecommended']['new_image_recommended_list']);
                                        if ($check_image_list == ConstantsFileErrorTypes::OK) {
                                            $file_name_list = FileManager::upload_image_webroot(
                                                $this->request->data['NetworkRecommended']['new_image_recommended_list'],
                                                $this->request->data['NetworkRecommended']['image_recommended_list'],
                                                ConstantsFileType::IMAGE,
                                                FilePaths::NETWORKS_RECOMMENDED_IMAGES
                                            );
                                            $imageRecommendedList = $file_name_list;
                                        }
                                    }
                                } else {
                                    // do not save data from the modal
                                    $recommendedLabel = 0;
                                    $imageRecommended = null;
                                    $imageRecommendedList = null;
                                }
                                $this->NetworkRecommended->createNetworkRecommended(
                                    $originalNetworkId,
                                    $internalNetworkId,
                                    $networkData['recommended'],
                                    $recommendedLabel,
                                    $imageRecommended,
                                    $imageRecommendedList
                                );
                            }
                        }
                        // edit data from the modal with one network recommended record,
                        if (isset($this->request->data['NetworkRecommended']['network_recommended_id'])) {
                            $data = $this->request->data['NetworkRecommended'];
                            $networkRecommendedModal = $this->NetworkRecommended
                                ->findByNetworkIdAndInternalNetworkId($data['network_id'], $data['network_recommended_id']);
                            $oldImage = $networkRecommendedModal['NetworkRecommended']['image_recommended'];
                            $oldImageList = $networkRecommendedModal['NetworkRecommended']['image_recommended_list'];
                            $networkRecommendedModal['NetworkRecommended']['recommended_label'] = $data['recommended_label'];
                            if (!empty($data['new_image_recommended'])) {
                                $check_image = FileManager::check_image($data['image_recommended'], $data['new_image_recommended']);
                                if ($check_image == ConstantsFileErrorTypes::OK) {
                                    $file_name = FileManager::upload_image_webroot(
                                        $data['new_image_recommended'],
                                        $data['image_recommended'],
                                        ConstantsFileType::IMAGE,
                                        FilePaths::NETWORKS_RECOMMENDED_IMAGES
                                    );
                                    FileManager::delete_file(WWW_ROOT, FilePaths::NETWORKS_RECOMMENDED_IMAGES . $oldImage);
                                    $networkRecommendedModal['NetworkRecommended']['image_recommended'] = $file_name;
                                }
                            }
                            if (!empty($data['new_image_recommended_list'])) {
                                $check_image_list = FileManager::check_image($data['image_recommended_list'], $data['new_image_recommended_list']);
                                if ($check_image_list == ConstantsFileErrorTypes::OK) {
                                    $file_name_list = FileManager::upload_image_webroot(
                                        $data['new_image_recommended_list'],
                                        $data['image_recommended_list'],
                                        ConstantsFileType::IMAGE,
                                        FilePaths::NETWORKS_RECOMMENDED_IMAGES
                                    );
                                    FileManager::delete_file(WWW_ROOT, FilePaths::NETWORKS_RECOMMENDED_IMAGES . $oldImageList);
                                    $networkRecommendedModal['NetworkRecommended']['image_recommended_list'] = $file_name_list;
                                }
                            }
                            $networkRecommendedModal['NetworkRecommended']['optimized'] = ConstantsBooleans::NO;
                            $networkRecommendedModal['NetworkRecommended']['modification_date'] = date('Y-m-d H:i:s');
                            $this->NetworkRecommended->save($networkRecommendedModal);
                            $this->GarageNetwork->setAllGaragesFromNetworkRecommended($data['network_recommended_id'], $data['recommended_label'], $recommendedLabelTrue, $data['network_id'], $aagRegionId);
                        }
                        $msg = __t(ConstantsMessages::WELL_SAVED);
                        $this->Session->setFlashSuccess($msg);
                        $this->redirect(array('controller' => 'networks', 'action' => 'recommended_networks', $network_id));
                    }

                    $garagesQuery = array(
                        'joins' => $joins,
                        'order' => 'Garage.name asc',
                        'fields' => array(
                            'Garage.id, Garage.name, Garage.city_id, Country.name, Garage.address1, Garage.phone,
                            Garage.ref_code, GarageNetwork.id, GarageNetwork.recommended, GarageNetwork.network_id,
                            Garage.recommended_network'
                        ),
                        'conditions' => $conditions
                    );
                    $garagesAll = $this->Garage->find('all', $garagesQuery);
                    $garageNetworksAll = array_column($garagesAll, 'GarageNetwork');

                    if (isset($searcher['recommended']) && !empty($searcher['recommended'])) {
                        foreach ($garageNetworksAll as $garageNetwork) {
                            $garageNetworkBd = $this->GarageNetwork->findById($garageNetwork['id']);
                            if (isset($searcher['recommended']) && !empty($searcher['recommended'])) {
                                $garageNetworkBd['GarageNetwork']['recommended'] = $searcher['recommended'] == 'true' ? ConstantsBooleans::ACTIVE : ConstantsBooleans::NO_ACTIVE;
                            }

                            $this->GarageNetwork->validator()->remove('contract_sent_date');
                            $this->GarageNetwork->validator()->remove('current_charge');
                            $this->GarageNetwork->validator()->remove('member_pays');
                            $this->GarageNetwork->validator()->remove('garage_pays');
                            $this->GarageNetwork->validator()->remove('trading_group_id');

                            $this->GarageNetwork->save($garageNetworkBd);
                        }
                        $params = array_merge_recursive($this->request->named, $this->request->query);
                        if (isset($params['recommended'])) {
                            unset($params['recommended']);
                        }
                        $this->redirect(array(
                            'controller' => 'networks',
                            'action' => 'recommended_networks',
                            $network_id,
                            '?' => $params
                        ));
                    }

                    $garages = $this->custom_pagination(
                        $garagesQuery,
                        [],
                        ConstantsPagination::SIZE_PAGE_SMALL,
                        'Garage',
                        null,
                        'PaginatorOrderCustom'
                    );

                    $recommendGaragesActive = array_column($garageNetworksAll, 'recommended');

                    $countsRecommendGaragesActives = array_count_values(
                        array_map('intval', $recommendGaragesActive)
                    );

                    $trading_groups = $this->TradingGroup->getTradingGroupGarageConditions($aagRegionId);
                    $countries = $this->Country->get_country_by_region($aagRegionId);
                    $cities = $this->City->get_list_conditions(array('Country.aag_region_id' => $aagRegionId));
                    $listNetworks = $this->Network->findInternal($aagRegionId, $network_id);

                    $lists_annex_detail = $this->AnnexDetail->getDataRegion($aagRegionId);
                    $annex_detail = array();
                    foreach ($lists_annex_detail as $key => $list_annex_detail) {
                        $annex_detail[$list_annex_detail['AnnexDetail']['id']] = $list_annex_detail['AnnexDetail']['name_' . __l()];
                    }

                    $lists_bdm = $this->GarageContactBdm->getAllBDMContacts($aagRegionId);
                    $bdm = array();
                    foreach ($lists_bdm as $key => $list_bdm) {
                        $id = $list_bdm['Contact']['id'];
                        $bdm[$id] = $list_bdm[0]['full_name'];
                    }

                    $services = $this->Service->search_list();
                    $erps = $this->Erp->getErpsByAagRegionId($aagRegionId);
                    $vehicles_types = $this->VehicleType->search_list();

                    $lead_sources = Configure::read('lead_source');
                    foreach ($lead_sources as $key => $lead_source) {
                        $lead_sources[$key] = __t($lead_source);
                    }

                    $external_agreements = $this->Agreement->external_agreements_list($aagRegionId);
                    $internal_agreements = $this->GarageAgreement->internal_agreements_list($aagRegionId);

                    $this->setVarForm();
                    $this->set(array(
                        'trading_groups' => $trading_groups,
                        'services' => $services,
                        'vehicles_types' => $vehicles_types,
                        'lead_sources' => $lead_sources,
                        'config' => $config,
                        'external_agreements' => $external_agreements,
                        'internal_agreements' => $internal_agreements,
                        'annex_detail' => $annex_detail,
                        'user' => $user,
                        'networks' => $networks,
                        'network_id' => $network_id,
                        'recommended_status' => $recommendedStatus,
                        'countries' => $countries ?? [],
                        'cities' => $cities ?? [],
                        'erp_providers' => $erps ?? [],
                        'garages' => $garages ?? [],
                        'listNetworks' => $listNetworks ?? [],
                        'globalRecommendGaragesActives' => isset($countsRecommendGaragesActives[0]) ? false : true,
                    ));
                }
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    public function ajax_save_garage_recommended()
    {
        $this->verify_ajax($this->request);
        $data = $this->request->data;

        if (
            isset($data['garage_network_id']) && !empty($data['garage_network_id']) &&
            isset($data['value']) && !empty($data['value'])
        ) {
            $garageNetworkBd = $this->GarageNetwork->findById($data['garage_network_id']);
            $originalNetworkId = $garageNetworkBd['GarageNetwork']['network_id'];

            if (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
            ) {
                $network = $this->Network->findById($originalNetworkId);

                if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                    $success = false;
                    $networks = $this->Network->getAllByInternalNetwork($originalNetworkId);
                    $networkIds =  Hash::extract($networks, '{n}.Network.id');
                    $networkIds[] = $originalNetworkId;
                    $recommended = $data['value'] == 'true' ? ConstantsBooleans::ACTIVE : ConstantsBooleans::NO_ACTIVE;

                    if ($this->GarageNetwork->setAllGaragesNetworkRecommended($garageNetworkBd['GarageNetwork']['garage_id'], $networkIds, $recommended)) {
                        $success = true;
                    }
                    $this->autoRender = false;
                    return json_encode($success);
                } else {
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                }
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        }
    }

    /**
     * Modal recommended label.
     */
    public function modal_recommended_label($networkId, $networkRecommendedId)
    {
        $this->verify_ajax($this->request);
        $network = $this->Network->findByIdAndAagRegionId($networkId, CakeSession::read('Auth.User.aag_region_id'));
        $networkRecommended = $this->NetworkRecommended->findByNetworkIdAndInternalNetworkId($networkId, $networkRecommendedId);

        if (
            $network &&
            $networkRecommended &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {

            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                $this->layout = 'ajax';
                $this->set(array(
                    'network_id' => $networkId,
                    'network_recommended' => $networkRecommended
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete recommended label.
     */
    public function ajax_delete_recommended_image()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            $this->autoRender = false;
            $recommendedId = $this->request->data('recommendedId');
            return $this->NetworkRecommended->deleteImageRecommended($recommendedId);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete recommended image list.
     */
    public function ajax_delete_recommended_image_list()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            $this->autoRender = false;
            $recommendedId = $this->request->data('recommendedId');
            return $this->NetworkRecommended->deleteImageRecommendedList($recommendedId);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete families.
     */
    public function ajax_delete_families($family_id)
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            $this->autoRender = false;
            return $this->GenartFamily->delete($family_id);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Networks quoting.
     */
    public function quoting($network_id)
    {
        $network = $this->Network->findByIdAndAagRegionId($network_id, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {

            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                $user = $this->Acceso->user();
                $aagRegionId = $user['aag_region_id'];

                $searcher = $this->request->query;
                $searcher = self::multipleFieldsArrayCheck($searcher);
                $this->request->data['Search'] = $searcher;
                $networks = array($network_id);
                if (isset($searcher['additional_network_id']) && !empty($searcher['additional_network_id'])) {
                    array_push($networks, $searcher['additional_network_id']);
                }

                if (!empty($searcher['network_id'])) {
                    $searcher['network_id_recommended_quoting'] = $searcher['network_id'];
                    unset($searcher['network_id']);
                }

                $conditionsJoins = $this->Garage->conditionsJoins($searcher);
                $joins = array_merge(
                    array(
                        array(
                            'alias' => 'City',
                            'table' => 'cities',
                            'type' => 'LEFT',
                            'conditions' => 'Garage.city_id = City.id'
                        ),
                        array(
                            'alias' => 'Province',
                            'table' => 'provinces',
                            'type' => 'LEFT',
                            'conditions' => 'City.province_id = Province.id'
                        ),
                        array(
                            'alias' => 'Country',
                            'table' => 'countries',
                            'type' => 'LEFT',
                            'conditions' => 'Province.country_id = Country.id'
                        ),
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                                'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                                'GarageNetwork.network_id' => $network_id,
                                'GarageNetwork.last' => ConstantsBooleans::YES
                            )
                        ),
                    ),
                    $conditionsJoins
                );

                $conditions = $this->Garage->conditions($searcher);
                $conditions[] = $this->User->viewUserGarages($this->Acceso->user());
                $conditions[] = array('Garage.status' => ConstantsGarageStatus::ACTIVE);

                $garagesQuery = array(
                    'joins' => $joins,
                    'order' => array(
                        'Garage.name' => 'asc'
                    ),
                    'fields' => array(
                        'Garage.id, Garage.name, Garage.city_id, Garage.province_id, Garage.address1, Garage.phone, Garage.erp_id,
					Garage.ref_code, GarageNetwork.id, GarageNetwork.garage_id , GarageNetwork.quoting_active, GarageNetwork.quoting_views_active'
                    ),
                    'conditions' => $conditions
                );

                //Garages belonging to a CV child network cant have quoting views activated, so by those garage_id they are ignored in bulk garages updates and listed to have deactivated toggles in the views
                $cvGarageIds = array();
                $cvChildNetworks = $this->Network->getListofChildNetworksByNetworkType($network_id, ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE);
                if (isset($cvChildNetworks) && !empty($cvChildNetworks)) {
                    $cvGarageIds = $this->GarageNetwork->getListOfGarageIdsByNetworkIds($cvChildNetworks);
                }

                $garagesAll = $this->Garage->find('all', $garagesQuery);
                $garageNetworksAll = array_column($garagesAll, 'GarageNetwork');

                if (
                    (isset($searcher['is_quoting_active']) && !empty($searcher['is_quoting_active'])) ||
                    (isset($searcher['quoting_views_active']) && !empty($searcher['quoting_views_active']))
                ) {
                    foreach ($garageNetworksAll as $garageNetwork) {
                        $garageNetworkBd = $this->GarageNetwork->findById($garageNetwork['id']);

                        if (in_array($garageNetworkBd['GarageNetwork']['garage_id'], $cvGarageIds)) {
                            continue;
                        }

                        if (isset($searcher['is_quoting_active']) && !empty($searcher['is_quoting_active'])) {
                            $garageNetworkBd['GarageNetwork']['quoting_active'] = $searcher['is_quoting_active'] == 'true' ? ConstantsBooleans::ACTIVE : ConstantsBooleans::NO_ACTIVE;
                        }
                        if (isset($searcher['quoting_views_active']) && !empty($searcher['quoting_views_active'])) {
                            $garageNetworkBd['GarageNetwork']['quoting_views_active'] = $searcher['quoting_views_active'] == 'true' ? ConstantsBooleans::ACTIVE : ConstantsBooleans::NO_ACTIVE;
                        }
                        if (
                            isset($searcher['quoting_views_active']) && $searcher['quoting_views_active'] == 'false'
                            && $garageNetworkBd['GarageNetwork']['quoting_active']
                        ) {
                            $garageNetworkBd['GarageNetwork']['quoting_active'] = ConstantsBooleans::NO_ACTIVE;
                        }

                        $this->GarageNetwork->validator()->remove('contract_sent_date');
                        $this->GarageNetwork->validator()->remove('current_charge');
                        $this->GarageNetwork->validator()->remove('member_pays');
                        $this->GarageNetwork->validator()->remove('garage_pays');
                        $this->GarageNetwork->validator()->remove('trading_group_id');

                        $this->GarageNetwork->save($garageNetworkBd);
                    }
                    $params = array_merge_recursive($this->request->named, $this->request->query);
                    if (isset($params['is_quoting_active'])) {
                        unset($params['is_quoting_active']);
                    }
                    if (isset($params['quoting_views_active'])) {
                        unset($params['quoting_views_active']);
                    }
                    $this->redirect(array(
                        'controller' => 'networks',
                        'action' => 'quoting',
                        $network_id,
                        '?' => $params
                    ));
                }

                $garages = $this->custom_pagination(
                    $garagesQuery,
                    [],
                    ConstantsPagination::SIZE_PAGE_SMALL,
                    'Garage',
                    null,
                    'PaginatorOrderCustom'
                );

                $garageNetworksWithNoCvGarages = array_filter($garageNetworksAll, function ($garageNetwork) use ($cvGarageIds) {
                    return !in_array($garageNetwork['garage_id'], $cvGarageIds);
                });

                $quotingActives = array_column($garageNetworksWithNoCvGarages, 'quoting_active');
                $quotingViewsActive = array_column($garageNetworksWithNoCvGarages, 'quoting_views_active');

                $countsQuotingActives = array_count_values(
                    array_map('intval', $quotingActives)
                );
                $countsQuotingViewsActives = array_count_values(
                    array_map('intval', $quotingViewsActive)
                );

                $provinces = $this->Province->getListByAagRegion($aagRegionId);
                $cities = $this->City->get_list_conditions(array('Country.aag_region_id' => $aagRegionId));
                $erps = $this->Erp->getErpsByAagRegionId($aagRegionId);
                $networks = $this->Network->findInternal($aagRegionId, $network_id);

                $this->setVarForm();
                $this->set(array(
                    'network_id' => $network_id,
                    'garages' => $garages ?? [],
                    'networks' => $networks ?? [],
                    'provinces' => $provinces ?? [],
                    'cities' => $cities ?? [],
                    'erps' => $erps ?? [],
                    'cvGarageIds' => $cvGarageIds ?? [],
                    'globalQuotingActives' => isset($countsQuotingActives[0]) ? false : true,
                    'globalQuotingViewsActives' => isset($countsQuotingViewsActives[0]) ? false : true,
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * This function recieves the request of a garage search, multiple fields are turned into an array.
     * Depending on whether the request comes from ajax or not a string of ids(separated by comma) or an array is recieved.
     *
     * @param requestData search request.
     * @return requestData search request without strings.
     */
    private function multipleFieldsArrayCheck($requestData)
    {
        $multipleSearchFields = array('aag_region_id', 'country_id', 'city_id');

        foreach ($multipleSearchFields as $value) {
            if (isset($requestData[$value]) && !empty($requestData[$value]) && !is_array($requestData[$value])) {
                $requestData[$value] = explode(',', $requestData[$value]);
            }
        }

        return $requestData;
    }

    /**
     * Save GarageNetwork quoting.
     */
    public function ajax_save_garage_quoting()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            $data = $this->request->data;
            $success = false;
            $this->autoRender = false;

            if (
                isset($data['garage_network_id']) && !empty($data['garage_network_id']) &&
                isset($data['value']) && !empty($data['value']) &&
                isset($data['type']) && !empty($data['type'])
            ) {
                $garageNetworkBd = $this->GarageNetwork->findById($data['garage_network_id']);
                $network = $this->Network->findById($garageNetworkBd['GarageNetwork']['network_id']);

                $belongsToCvChildNetwork = false;
                if (isset($garageNetworkBd) && !empty($garageNetworkBd)) {
                    $childNetworks = $this->Network->getListofChildNetworks($network["GarageNetwork"]["network_id"]);
                    if (isset($childNetworks) && !empty($childNetworks)) {
                        $belongsToCvChildNetwork = $this->GarageNetwork->garageBelongsToCvNetwork($network["GarageNetwork"]["garage_id"]);
                    }
                }

                if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC)) && !$belongsToCvChildNetwork) {
                    $garageNetworkBd['GarageNetwork'][$data['type']] = $data['value'] == 'true' ? ConstantsBooleans::ACTIVE : ConstantsBooleans::NO_ACTIVE;

                    if ($data['value'] == 'false' && $garageNetworkBd['GarageNetwork']['quoting_active']) {
                        $garageNetworkBd['GarageNetwork']['quoting_active'] = ConstantsBooleans::NO_ACTIVE;
                    }

                    $this->GarageNetwork->validator()->remove('contract_sent_date');
                    $this->GarageNetwork->validator()->remove('current_charge');
                    $this->GarageNetwork->validator()->remove('member_pays');
                    $this->GarageNetwork->validator()->remove('garage_pays');
                    $this->GarageNetwork->validator()->remove('trading_group_id');

                    if ($this->GarageNetwork->save($garageNetworkBd)) {
                        $success = true;
                    }
                    return json_encode($success);
                } else {
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                }
            } else {
                return json_encode($success);
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Network general settings.
     */
    public function general_settings($network_id)
    {
        $network = $this->Network->findByIdAndAagRegionId($network_id, CakeSession::read('Auth.User.aag_region_id'));
        if (
            $network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                $network = $this->Network->findById($network_id);
                $email_booking_contact_id = $email_enquiry_contact_id = array();
                $distance_unit_name = $this->DistanceUnit->getDistanceUnitNameByUnitDistanceId($network['Network']['distance_unit_id']);

                if (isset($network['Network']['email_booking_contact_id']) && !empty($network['Network']['email_booking_contact_id'])) {
                    $email_booking_contact_id = $this->Contact->findContactsEmailsConditions($network['Network']['email_booking_contact_id'], $network['Network']['aag_region_id']);
                }
                if (isset($network['Network']['email_enquiry_contact_id']) && !empty($network['Network']['email_enquiry_contact_id'])) {
                    $email_enquiry_contact_id = $this->Contact->findContactsEmailsConditions($network['Network']['email_enquiry_contact_id'], $network['Network']['aag_region_id']);
                }

                if ($this->request->is('get')) {
                    $this->request->data = $network;
                } else {
                    $this->request->data['Network']['id'] = $network['Network']['id'];
                    if (isset($this->request->data['hide_phone_numbers_in_pws'])) {
                        $this->request->data['Network']['hide_phone_numbers_in_pws'] = $this->request->data['hide_phone_numbers_in_pws'] === 'on' ? ConstantsBooleans::ACTIVE : ConstantsBooleans::NO_ACTIVE;
                    } else {
                        $this->request->data['Network']['hide_phone_numbers_in_pws'] = ConstantsBooleans::NO_ACTIVE;
                    }
                    if ($this->Network->edit_mileage_emails_network_by_network_id($this->request->data)) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                }

                $this->setVarForm();
                $this->set(array(
                    'network_id' => $network_id,
                    'distance_unit_name' => $distance_unit_name,
                    'languages_webs_network' => $this->LanguageWebNetwork->getLanguagesWebsNetwork($network_id),
                    'languages_loco' => array(),
                    'languages_webs_flags' => $this->LanguageWebFlag->getLanguagesWebsFlags(),
                    'email_booking_contact_id' => json_encode($email_booking_contact_id),
                    'email_enquiry_contact_id' => json_encode($email_enquiry_contact_id),
                    'hide_phone_numbers_in_pws' => isset($network['Network']['hide_phone_numbers_in_pws']) && $network['Network']['hide_phone_numbers_in_pws'] ? true : false
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Networks loop.
     */
    public function loop($network_id)
    {
        $network = $this->Network->findByIdAndAagRegionId($network_id, CakeSession::read('Auth.User.aag_region_id'));
        if (
            CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::BENELUX && $network && $network['Network']['loop'] &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {

            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $searcher = $this->request->query;
            $searcher = self::multipleFieldsArrayCheck($searcher);
            $this->request->data['Search'] = $searcher;

            $conditionsJoins = $this->Garage->conditionsJoins($searcher);
            $joins = array_merge(
                array(
                    array(
                        'alias' => 'City',
                        'table' => 'cities',
                        'type' => 'LEFT',
                        'conditions' => 'Garage.city_id = City.id'
                    ),
                    array(
                        'alias' => 'Province',
                        'table' => 'provinces',
                        'type' => 'LEFT',
                        'conditions' => 'City.province_id = Province.id'
                    ),
                    array(
                        'alias' => 'Country',
                        'table' => 'countries',
                        'type' => 'LEFT',
                        'conditions' => 'Province.country_id = Country.id'
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Garage.id = GarageNetwork.garage_id',
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                            'GarageNetwork.network_id' => $network_id,
                            'GarageNetwork.last' => ConstantsBooleans::YES
                        )
                    ),
                ),
                $conditionsJoins
            );

            $conditions = $this->Garage->conditions($searcher);
            $conditions[] = $this->User->viewUserGarages($this->Acceso->user());
            $conditions[] = array('Garage.status' => ConstantsGarageStatus::ACTIVE);

            $garagesQuery = array(
                'joins' => $joins,
                'order' => 'Garage.name asc',
                'fields' => array(
                    'Garage.name, Garage.city_id, Garage.province_id, Garage.address1, Garage.phone, Garage.erp_id,
                Garage.ref_code, GarageNetwork.id, GarageNetwork.loop'
                ),
                'conditions' => $conditions
            );

            $garagesAll = $this->Garage->find('all', $garagesQuery);
            $garageNetworksAll = array_column($garagesAll, 'GarageNetwork');

            if (isset($searcher['loop']) && !empty($searcher['loop'])) {
                foreach ($garageNetworksAll as $garageNetwork) {
                    $garageNetworkBd = $this->GarageNetwork->findById($garageNetwork['id']);
                    if (isset($searcher['loop']) && !empty($searcher['loop'])) {
                        $garageNetworkBd['GarageNetwork']['loop'] = $searcher['loop'] == 'true' ? ConstantsBooleans::ACTIVE : ConstantsBooleans::NO_ACTIVE;
                    }

                    $this->GarageNetwork->validator()->remove('contract_sent_date');
                    $this->GarageNetwork->validator()->remove('current_charge');
                    $this->GarageNetwork->validator()->remove('member_pays');
                    $this->GarageNetwork->validator()->remove('garage_pays');
                    $this->GarageNetwork->validator()->remove('trading_group_id');

                    $this->GarageNetwork->save($garageNetworkBd);
                }
                $params = array_merge_recursive($this->request->named, $this->request->query);
                if (isset($params['loop'])) {
                    unset($params['loop']);
                }
                $this->redirect(array(
                    'controller' => 'networks',
                    'action' => 'loop',
                    $network_id,
                    '?' => $params
                ));
            }

            $garages = $this->custom_pagination(
                $garagesQuery,
                [],
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Garage',
                null,
                'PaginatorOrderCustom'
            );

            $loopViewsActive = array_column($garageNetworksAll, 'loop');
            $countsLoopActives = array_count_values(
                array_map('intval', $loopViewsActive)
            );

            $provinces = $this->Province->getListByAagRegion($aagRegionId);
            $cities = $this->City->get_list_conditions(array('Country.aag_region_id' => $aagRegionId));
            $erps = $this->Erp->getErpsByAagRegionId($aagRegionId);

            $this->set(array(
                'network_id' => $network_id,
                'provinces' => $provinces ?? [],
                'cities' => $cities ?? [],
                'erps' => $erps ?? [],
                'garages' => $garages,
                'globalLoopActives' => isset($countsLoopActives[0]) ? false : true,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Save GarageNetwork loop.
     */
    public function ajax_save_garage_loop()
    {
        $this->verify_ajax($this->request);
        $data = $this->request->data;

        if (
            isset($data['garage_network_id']) && !empty($data['garage_network_id']) &&
            isset($data['value']) && !empty($data['value'])
        ) {
            $garageNetworkBd = $this->GarageNetwork->findById($data['garage_network_id']);
            $originalNetworkId = $garageNetworkBd['GarageNetwork']['network_id'];

            if (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
            ) {
                $success = true;
                $loop = $data['value'] == 'true' ? ConstantsBooleans::ACTIVE : ConstantsBooleans::NO_ACTIVE;

                $allGarageNetworks = $this->GarageNetwork->findAllByGarageIdAndNetworkIdAndStatusAndLast($garageNetworkBd['GarageNetwork']['garage_id'], $originalNetworkId, ConstantsNetworksStatus::LIVE, ConstantsBooleans::YES);
                foreach ($allGarageNetworks as $garageNetwork) {
                    if (!$this->GarageNetwork->updateGarageNetworkLoop($garageNetwork, $loop)) {
                        $success = false;
                        break;
                    }
                }
                $this->autoRender = false;
                return json_encode($success);
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        }
    }

    /**
     * AJAX create/edit LanguageWebNetwork.
     */
    public function ajax_add_edit_language_web()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            $data = $this->request->data;
            $this->layout = $this->autoRender = false;

            if (
                isset($data['network_id']) && !empty($data['network_id']) &&
                isset($data['name_loco']) && !empty($data['name_loco']) &&
                isset($data['code_loco']) && !empty($data['code_loco']) &&
                isset($data['flag_loco']) && !empty($data['flag_loco'])
            ) {
                $network = $this->Network->findById($data['network_id']);

                if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                    $success = 'false';
                    $flag = $this->LanguageWebFlag->findByUrl($data['flag_loco']);
                    if (isset($flag)) {
                        $data['flag_id'] = $flag['LanguageWebFlag']['id'];
                        $languageWebNetwork = $this->LanguageWebNetwork->findByNetworkIdAndCode($data['network_id'], $data['code_loco']);
                        if ((!isset($data['id']) || (isset($data['id']) && empty($data['id']))) && isset($languageWebNetwork) && !empty($languageWebNetwork)) {
                            $error = __t('Language.Duplicate');
                        } else {
                            $resultSave = $this->LanguageWebNetwork->saveLanguageWebNetwork($data);
                            if ($resultSave['success']) {
                                $success = 'true';
                            } else {
                                $error = $resultSave['error'];
                            }
                        }
                    }
                    echo json_encode(
                        array(
                            'success' => $success,
                            'error_text' => $error ?? ''
                        )
                    );
                } else {
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                }
            } else {
                $error = __t('Language.Data_required');
                echo json_encode(
                    array(
                        'success' => false,
                        'error_text' => $error ?? ''
                    )
                );
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete LanguageWebNetwork.
     */
    public function ajax_delete_language_web($network_id, $language_web_id)
    {
        $this->verify_ajax($this->request);
        $network = $this->Network->findByIdAndAagRegionId($network_id, CakeSession::read('Auth.User.aag_region_id'));

        if (
            $network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {

            if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                $languageWebNetwork = $this->LanguageWebNetwork->findById($language_web_id);

                if (isset($languageWebNetwork) && !empty($languageWebNetwork) && $this->LanguageWebNetwork->delete($language_web_id)) {
                    // Delete in LOCO
                    $agn = new Agn();
                    $result = $agn->deleteLocoLanguage($network['Network']['guid'], $languageWebNetwork['LanguageWebNetwork']['code'] . '-' . $network['Network']['country_code_language']);
                    if ($result['success'] == 'true' && $this->SendGridEmailTypeTemplate->deleteAll(['language_web_id' => $language_web_id], false)) {
                        $success = 'true';
                    }
                }

                echo json_encode(array('precess' => $success ?? 'false'));
                $this->layout = $this->autoRender = false;
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX activate LanguageWebNetwork.
     */
    public function ajax_active_language_web($language_web_id)
    {
        $this->verify_ajax($this->request);
        $languageWebNetworkSelected = $this->LanguageWebNetwork->findById($language_web_id);
        $network = $this->Network->findByIdAndAagRegionId($languageWebNetworkSelected['LanguageWebNetwork']['network_id'], CakeSession::read('Auth.User.aag_region_id'));

        if (
            $languageWebNetworkSelected &&
            $network &&
            in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC)) &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            if (isset($languageWebNetworkSelected) && !empty($languageWebNetworkSelected)) {
                $languageWebNetworkSelected['LanguageWebNetwork']['active'] = !$languageWebNetworkSelected['LanguageWebNetwork']['active'];
                if ($this->LanguageWebNetwork->save($languageWebNetworkSelected)) {
                    $success = 'true';
                }
            }
            echo json_encode(array('precess' => $success ?? 'false'));
            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX update LOCO translations.
     */
    public function updateLocoTranslations($networkId, $locale = null)
    {
        $this->verify_ajax($this->request);
        $network = $this->Network->findByIdAndAagRegionId($networkId, CakeSession::read('Auth.User.aag_region_id'));

        if (
            $network &&
            in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC)) &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK, ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
        ) {
            $agn = new Agn();
            $result = $agn->updateLocoTranslations($network['Network']['guid'], isset($locale) ? $locale . '-' . $network['Network']['country_code_language'] : null);
            echo json_encode(array('precess' => $result['success'] ?? 'false'));
            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Api to get the different languages and flags.
     */
    public function get_languages_flags()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {
            if (isset($dataReceived->network_id)) {
                $languagesFlags = $this->LanguageWebNetwork->getLanguagesWebsNetwork($dataReceived->network_id, false);
            }
            return $this->returnJsonResult($languagesFlags ?? array());
        }
    }
}
