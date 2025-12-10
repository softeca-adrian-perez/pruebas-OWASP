<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class DistributorsNetworksController extends AppController
{
    public $uses = array(
        'DistributorNetwork',
        'Network',
        'DistributorDistributorNetwork',
        'TradingGroup',
        'TradingGroupDistributorNetwork',
        'DistributorNetworkContactBdm',
    );

    /**
     * Distributor networks home page.
     */
    public function home()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR_NETWORK) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS_NETWORKS)
            )
        ) {
            $networks_types = Configure::read('network_types');

            $networks = $this->custom_pagination(
                array(
                    'joins' => array(
                        array(
                            'alias' => 'TradingGroupDistributorNetwork',
                            'table' => 'trading_groups_distributors_networks',
                            'type' => 'INNER',
                            'conditions' => array(
                                'TradingGroupDistributorNetwork.network_id = DistributorNetwork.id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'INNER',
                            'conditions' => array(
                                'TradingGroup.id = TradingGroupDistributorNetwork.trading_group_id',
                                'TradingGroup.aag_region_id' => $aagRegionId
                            ),
                        )
                    ),
                    'conditions' => array(
                        'DistributorNetwork.aag_region_id' => $aagRegionId,
                    ),
                    'group' => 'DistributorNetwork.id',    //As the network can belong to more than one tg we must ensure its not displayed twice
                    'order' => 'DistributorNetwork.name',
                ),
                array(),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'DistributorNetwork',
                null,
                'PaginatorOrderCustom'
            );

            $trading_groups = $this->DistributorNetwork->TradingGroup->getIndependent($aagRegionId);

            foreach ($networks as $key => $network) {
                $networks[$key]['DistributorNetwork']['trading_groups'] = $this->TradingGroupDistributorNetwork->findAllByNetworkId($network['DistributorNetwork']['id']);
                $networks[$key]['DistributorNetwork']['not_converted'] = count($this->DistributorDistributorNetwork->findAllByNetworkIdAndStatusAndLast($network['DistributorNetwork']['id'], __t(ConstantsNetworksStatus::NOT_CONVERTED), ConstantsBooleans::YES));
                $networks[$key]['DistributorNetwork']['prospect'] = count($this->DistributorDistributorNetwork->findAllByNetworkIdAndStatusAndLast($network['DistributorNetwork']['id'], array(__t(ConstantsNetworksStatus::PROSPECT), __t(ConstantsNetworksStatus::AWAITING_VISIT), __t(ConstantsNetworksStatus::AWAITING_DECISION)), ConstantsBooleans::YES));
                $networks[$key]['DistributorNetwork']['live'] = count($this->DistributorDistributorNetwork->findAllByNetworkIdAndStatusAndLast($network['DistributorNetwork']['id'], __t(ConstantsNetworksStatus::LIVE), ConstantsBooleans::YES));
                $networks[$key]['DistributorNetwork']['unsubscribe'] = count($this->DistributorDistributorNetwork->findAllByNetworkIdAndStatusAndLast($network['DistributorNetwork']['id'], __t(ConstantsNetworksStatus::UNSUBSCRIBE), ConstantsBooleans::YES));
                $networks[$key]['DistributorNetwork']['total'] = count($this->DistributorDistributorNetwork->getAllByNetworkIdAndStatusAndLast($network['DistributorNetwork']['id'], array(ConstantsNetworksStatus::NOT_CONVERTED, ConstantsNetworksStatus::PROSPECT, ConstantsNetworksStatus::AWAITING_VISIT, ConstantsNetworksStatus::AWAITING_DECISION, ConstantsNetworksStatus::LIVE), ConstantsBooleans::YES));
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
     * View DistributorNetwork.
     */
    public function view($distributor_network_id)
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR_NETWORK) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS_NETWORKS)
            )
        ) {
            $distributor_network = $this->DistributorNetwork->findDatas($distributor_network_id);
            $trading_groups = $this->DistributorNetwork->TradingGroup->getTradingGroupIndependent();
            $presets = Configure::read('Presets');

            $selected_preset = false;
            foreach ($presets as $key => $preset) {
                if (($preset[0] == $distributor_network['DistributorNetwork']['image_cluster']) && ($preset[1] == $distributor_network['DistributorNetwork']['image_pin'])) {
                    $selected_preset = $key;
                }
            }

            $this->setVarForm();
            $this->set(array(
                'distributor_network' => $distributor_network,
                'trading_groups' => $trading_groups,
                'presets' => $presets,
                'selected_preset' => $selected_preset
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create DistributorNetwork.
     */
    public function add()
    {
        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR_NETWORK) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS_NETWORKS)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $trading_groups = null;

            $presets = Configure::read('Presets');
            $this->setVarDate();

            if (!$this->request->is('get')) {
                $flag_new = $this->uploadImages($presets);
                $error_size = false;
                $error_upload = false;
                $check_image = false;
                $this->request->data['DistributorNetwork']['aag_region_id'] = $aagRegionId;
                if ($this->data['DistributorNetwork']['image']['name'] != "") {
                    if (!empty($this->request->data['DistributorNetwork']['new_image'])) {
                        $check_image = FileManager::check_image($this->request->data['DistributorNetwork']['image'], $this->request->data['DistributorNetwork']['new_image']);
                        if ($check_image == ConstantsFileErrorTypes::OK) {
                            $file_name = FileManager::upload_image_webroot($this->request->data['DistributorNetwork']['new_image'], $this->request->data['DistributorNetwork']['image'], ConstantsFileType::IMAGE, FilePaths::NETWORKS_IMAGES_RELATIVE);
                            $this->request->data['DistributorNetwork']['image'] = $file_name;
                        }
                    }
                } else {
                    $this->request->data['DistributorNetwork']['image'] = "";
                }
                if (!$check_image || $check_image == ConstantsFileErrorTypes::OK) {
                    if (!$flag_new && !$error_size && !$error_upload) {
                        $distributor_network = $this->DistributorNetwork->new_networks($this->request->data);
                        if ($distributor_network) {
                            $this->request->data['TradingGroup']['id'] = $this->TradingGroup->getLastInsertID();
                            $msg = h(sprintf(__t('Network.Well_add'), $distributor_network['DistributorNetwork']['name']));
                            $this->Session->setFlashSuccess($msg);
                            $this->redirect(
                                array(
                                    'controller' => 'distributors_networks',
                                    'action' => 'edit',
                                    $this->DistributorNetwork->getLastInsertID()
                                )
                            );
                        } else {
                            $this->Session->setFlashError(__t('Network.Error_create'));
                        }
                    } else {
                        if ($error_upload) {
                            $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                        } elseif ($error_size) {
                            $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                        } else {
                            $this->Session->setFlashError(__t($flag_new));
                        }
                    }
                } elseif ($check_image == ConstantsFileErrorTypes::SIZE_ERROR) {
                    $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'trading_groups' => $trading_groups,
                'presets' => $presets
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit DistributorNetwork.
     */
    public function edit($distributor_network_id)
    {
        $distributor_network = $this->DistributorNetwork->findDatas($distributor_network_id);

        if (
            $distributor_network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR_NETWORK) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS_NETWORKS)
        ) {
            $oldImage = $distributor_network['DistributorNetwork']['image'];
            $trading_groups = $this->DistributorNetwork->TradingGroup->getTradingGroupIndependent();
            $presets = Configure::read('Presets');

            $selected_preset = false;
            foreach ($presets as $key => $preset) {
                if (($preset[0] == $distributor_network['DistributorNetwork']['image_cluster']) && ($preset[1] == $distributor_network['DistributorNetwork']['image_pin'])) {
                    $selected_preset = $key;
                }
            }

            $this->setVarDate();

            if (!$this->request->is('get')) {
                $error_file = false;
                $error_size = false;
                $check_image = false;

                if (!empty($this->request->data['DistributorNetwork']['new_image'])) {
                    $check_image = FileManager::check_image($this->request->data['DistributorNetwork']['image'], $this->request->data['DistributorNetwork']['new_image']);
                    if ($check_image == ConstantsFileErrorTypes::OK) {
                        $file_name = FileManager::upload_image_webroot($this->request->data['DistributorNetwork']['new_image'], $this->request->data['DistributorNetwork']['image'], ConstantsFileType::IMAGE, FilePaths::NETWORKS_IMAGES_RELATIVE);
                        $this->request->data['DistributorNetwork']['image'] = $file_name;
                        if (!$file_name) {
                            $error_file = true;
                        } else {
                            $error_file = !FileManager::delete_file(WWW_ROOT, FilePaths::NETWORKS_IMAGES_RELATIVE . $oldImage);
                        }
                    }
                } else {
                    $image_mame = $this->DistributorNetwork->findById($distributor_network_id);
                    $this->request->data['DistributorNetwork']['image'] = $image_mame['DistributorNetwork']['image'];
                }
                $flag_new = $this->uploadImages($presets);

                if (!$check_image || $check_image == ConstantsFileErrorTypes::OK) {
                    if (!$error_file && !$flag_new && !$error_size) {
                        $distributor_network_bd = $this->DistributorNetwork->edit_network($this->request->data);
                        if ($distributor_network_bd) {
                            $msg = h(sprintf(__t('Network.Well_edit'), $distributor_network['DistributorNetwork']['name']));
                            $this->Session->setFlashSuccess($msg);
                            $this->redirect($this->request->here);
                        }
                    } else {
                        if ($error_file) {
                            $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                        } elseif ($error_size) {
                            $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                        } else {
                            $this->Session->setFlashError(__t($flag_new));
                        }
                    }
                } elseif ($check_image == ConstantsFileErrorTypes::SIZE_ERROR) {
                    $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                }
            } else {
                $this->request->data = $distributor_network;
            }

            $this->setVarForm();
            $this->set(array(
                'distributor_network' => $distributor_network,
                'trading_groups' => $trading_groups,
                'presets' => $presets,
                'selected_preset' => $selected_preset
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete DistributorNetwork.
     */
    public function ajax_delete($distributor_network_id)
    {
        $this->verify_ajax($this->request);

        $distributor_network = $this->DistributorNetwork->findById($distributor_network_id);

        if (
            $distributor_network &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR_NETWORK) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS_NETWORKS)
        ) {
            $can_delete = true;
            $can_delete_text = 'true';
            $error_text = '';

            $models_relationship = $this->getModels();

            if (!empty($distributor_network)) {
                foreach ($models_relationship as $model => $translation) {
                    if ($model == 'DistributorDistributorNetwork' || $model == 'TradingGroupDistributorNetwork') {
                        $object = $this->$model->findByNetworkId($distributor_network_id); //It's incorrect in database. Field name must be distributor_country_id
                    } else {
                        $object = $this->$model->findByDistributorNetworkId($distributor_network_id);
                    }
                    if (!empty($object)) {
                        $can_delete = false;
                        $error_text = $translation;
                        break;
                    }
                }
            }

            if ($can_delete) {
                $conditions = array(
                    'network_id' => $distributor_network_id
                );
                //Delete the relationship between network and trading group
                $trading_groups_networks_bd = $this->TradingGroupDistributorNetwork->deleteAll($conditions);
                if ($trading_groups_networks_bd) {
                    //IN THE FUTURE: Before deleting Network, delete relationships with positions.
                    $d_net_bd = $this->DistributorNetwork->delete($distributor_network_id);
                    if (!$d_net_bd) {
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
            'DistributorNetworkContactBdm' =>  __t('General.Relation_with') . ' ' . __t('Contact.Contact'),
            'DistributorDistributorNetwork' =>  __t('General.Relation_with') . ' ' . __t('Garage.Garage'),
        );
    }

    private function setVarForm()
    {
        $cancel_action = array(
            'url_cancel' => array(
                'controller' => 'distributors_networks',
                'action' => 'home',
            ),
        );

        $networks_types = Configure::read('network_types');

        $this->set(array(
            'cancel_action' => $cancel_action,
            'networks_types' => $networks_types,
        ));
    }

    /**
     * Upload DistributorNetwork images.
     */
    private function uploadImages($presets)
    {
        $flag = false;
        if (isset($this->request->data['RadioGroup']) && $this->request->data['RadioGroup'] == ConstantsNetworksPins::CUSTOM) {
            if ($this->data['DistributorNetwork']['image_pin']['name'] != "") {
                $file = new File($this->data['DistributorNetwork']['image_pin']['tmp_name']);
                $path_parts = pathinfo($this->data['DistributorNetwork']['image_pin']['name']);
                $ext = $path_parts['extension'];
                $tam_img_pin = getimagesize($this->request->data['DistributorNetwork']['image_pin']['tmp_name']);
                if (($tam_img_pin[0] != ConstantsNetworkImageDimension::WIDTH_PIN) || ($tam_img_pin[1] != ConstantsNetworkImageDimension::HEIGHT_PIN)) {
                    $flag = ConstantsAlertsErrors::ERROR_DIMENSIONS;
                }
                if ($ext != 'jpg' && $ext != 'png') {
                    $flag = ConstantsAlertsErrors::ERROR_DIMENSIONS; // bad extension
                } else {
                    $filename = FileManager::get_renamed_name($this->data['DistributorNetwork']['image_pin']['name']);
                    $data = $file->read();
                    $file->close();
                    $file = new File(WWW_ROOT . FilePaths::PINS_IMAGES_REALTIVE . $filename, true);
                    $file->write($data);
                    $file->close();
                    $this->request->data['DistributorNetwork']['image_pin'] = $filename;
                }
            } else {
                $this->request->data['DistributorNetwork']['image_pin'] = "";
            }

            if ($this->data['DistributorNetwork']['image_cluster']['name'] != "") {
                $file = new File($this->data['DistributorNetwork']['image_cluster']['tmp_name']);
                $path_parts = pathinfo($this->data['DistributorNetwork']['image_cluster']['name']);
                $ext = $path_parts['extension'];
                $tam_img_cluster = getimagesize($this->request->data['DistributorNetwork']['image_cluster']['tmp_name']);
                if (($tam_img_cluster[0] != ConstantsNetworkImageDimension::WIDTH_CLUSTER) || ($tam_img_cluster[1] != ConstantsNetworkImageDimension::HEIGHT_CLUSTER)) {
                    $flag = ConstantsAlertsErrors::ERROR_DIMENSIONS;
                }
                if ($ext != 'jpg' && $ext != 'png') {
                    $flag = ConstantsAlertsErrors::ERROR_EXTENSION; // bad extension
                } else {
                    $filename = FileManager::get_renamed_name($this->data['DistributorNetwork']['image_cluster']['name']);
                    $data = $file->read();
                    $file->close();
                    $file = new File(WWW_ROOT . FilePaths::PINS_IMAGES_REALTIVE . $filename, true);
                    $file->write($data);
                    $file->close();
                    $this->request->data['DistributorNetwork']['image_cluster'] = $filename;
                }
            } else {
                $this->request->data['DistributorNetwork']['image_cluster'] = "";
            }
        } elseif (isset($this->request->data['RadioGroup'])) {
            $this->request->data['DistributorNetwork']['image_cluster'] = $presets[$this->request->data['RadioGroup']][0];
            $this->request->data['DistributorNetwork']['image_pin'] = $presets[$this->request->data['RadioGroup']][1];
        } else {
            $this->request->data['DistributorNetwork']['image_cluster'] = $presets[ConstantsNetworksPins::DEFAULT][0];
            $this->request->data['DistributorNetwork']['image_pin'] = $presets[ConstantsNetworksPins::DEFAULT][1];
        }

        return $flag;
    }

    /**
     * AJAX get trading groups.
     */
    public function ajax_get_tradings_groups()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR_NETWORK) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS_NETWORKS)
            )
        ) {
            $trading_groups = null;
            $distributor_network = array();
            if (!$this->request->is('get')) {
                $data = $this->request->data;
                $distributor_network_id = isset($data['distributor_network_id']) ? $data['distributor_network_id'] : null;
                $distributor_network = $this->DistributorNetwork->findDatas($distributor_network_id);
                if (isset($data['network_type']) && $data['network_type'] == 'LV') {
                    $trading_groups = $this->DistributorNetwork->TradingGroup->getTradingGroupIndependentByLVType($aagRegionId);
                } elseif (isset($data['network_type']) && $data['network_type'] == 'CV') {
                    $trading_groups = $this->DistributorNetwork->TradingGroup->getTradingGroupIndependentByCVType($aagRegionId);
                } else {
                    $trading_groups = null;
                }
            }

            $this->set(array(
                'trading_groups' => $trading_groups,
                'distributor_network' => $distributor_network,
            ));

            $this->layout = false;
            $this->render('../DistributorsNetworks/Elements/trading_groups');
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
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR_NETWORK) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS_NETWORKS)
            )
        ) {
            $this->autoRender = null;
            return json_encode($this->DistributorNetwork->findNetworksWithDistributors());
        } else {
            throw new UnauthorizedException();
        }
    }
}
