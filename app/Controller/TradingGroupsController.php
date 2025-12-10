<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class TradingGroupsController extends AppController
{
    public $uses = array(
        'TradingGroup',
        'TradingGroupNetwork',
        'Distributor',
        'Permission',
        'User',
        'Role',
        'Position',
        'GroupPermissionPermission',
        'PositionConfig',
        'CommunicationTradingGroup',
        'ShortcutTradingGroup',
        'SupplierTradingGroup',
        'TradingGroupDistributorNetwork',
        'PositionConfigTradingGroup',
        'Contact',
        'AagRegion'
    );

    /**
     * Trading groups home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_TRADING_GROUP) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRADING_GROUPS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $trading_groups_permissions = $this->Permission->getByGroupingPermission(ConstantsGroupingPermissions::TRADING_GROUP);

            $trading_groups = $this->custom_pagination(
                $this->TradingGroup->_query('home'),
                array('TradingGroup.independent' => ConstantsBooleans::YES, 'TradingGroup.aag_region_id' => $aagRegionId),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'TradingGroup',
                null,
                'PaginatorOrderCustom'
            );

            foreach ($trading_groups as $key => $trading_group) {
                $tmp = $this->Distributor->find('count', array(
                    'conditions' => array(
                        'trading_group_id' => $trading_group['TradingGroup']['id'],
                        'aag_region_id' => $aagRegionId
                    )
                ));
                $trading_groups[$key]['TradingGroup']['distributors'] = $tmp;
            }

            $this->set(array(
                'trading_groups' => $trading_groups,
                'trading_groups_permissions' => $trading_groups_permissions
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * TradingGroup view page.
     */
    public function view($trading_group_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $trading_group = $this->TradingGroup->findByIdAndAagRegionId($trading_group_id, $aagRegionId);
        if (
            $trading_group &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_TRADING_GROUP) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::TRADING_GROUPS)
                )
            )
        ) {

            $related_networks_list = $this->TradingGroup->Network->getRelatedNetworksList($trading_group_id);
            $networks = $this->TradingGroup->Network->findNetworksByType($trading_group['TradingGroup']['is_cv']);
            $networks_availables = array_diff($networks, $related_networks_list);

            $related_networks = $this->TradingGroup->Network->getRelatedNetworks($trading_group_id);
            $networks_types = Configure::read('network_types');

            $this->setVarForm();

            $this->set(array(
                'trading_group' => $trading_group,
                'related_networks' => $related_networks,
                'networks_types' => $networks_types,
                'networks_availables' => $networks_availables
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create TradingGroup.
     */
    public function add()
    {
        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRADING_GROUPS)
        ) {
            if (!$this->request->is('get')) {
                $flag = true;
                if ($this->request->data['TradingGroup']['new_image'] == 'false') {
                    $flag = false;
                } elseif (!empty($this->request->data['TradingGroup']['new_image'])) {
                    $check_image = FileManager::check_image($this->request->data['TradingGroup']['image'], $this->request->data['TradingGroup']['new_image']);
                    if ($check_image == ConstantsFileErrorTypes::OK) {
                        $file_name = FileManager::upload_image_webroot($this->request->data['TradingGroup']['new_image'], $this->request->data['TradingGroup']['image'], ConstantsFileType::IMAGE, FilePaths::TRADING_GROUP_IMAGES_RELATIVE);
                        $this->request->data['TradingGroup']['image'] = $file_name;
                        if (!$file_name) {
                            $flag = false;
                        }
                    }
                }

                $trading_group_bd = $this->TradingGroup->new_trading_group($this->request->data);

                if ($flag && $trading_group_bd) {
                    if ($check_image == ConstantsFileErrorTypes::OK) {
                        $this->request->data['TradingGroup']['id'] = $this->TradingGroup->getLastInsertID();
                        $this->TradingGroup->generate_style_trading_group($this->request->data);
                        $msg = h(sprintf(__t('TradingGroup.Edit_well'), $trading_group_bd['TradingGroup']['name']));
                        $this->Session->setFlashSuccess($msg);
                        $this->redirect(
                            array(
                                'controller' => 'trading_groups',
                                'action' => 'edit',
                                $this->TradingGroup->getLastInsertID()
                            )
                        );
                    } elseif ($check_image == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                } else {
                    if (!$flag) {
                        $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                }
            }

            $this->setVarForm();
            $this->setVarDate();
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'trading_groups',
                    'action' => 'home',
                ),
            );
            $this->set(array(
                'cancel_action' => $cancelAction,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit TradingGroup.
     */
    public function edit($trading_group_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $trading_group = $this->TradingGroup->findByIdAndAagRegionId($trading_group_id, $aagRegionId);
        if (
            $trading_group &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRADING_GROUPS)
        ) {
            $oldImage = $trading_group['TradingGroup']['image'];

            if (!$this->request->is('get')) {
                $flag = true;
                $error_size = false;
                $error_upload = false;
                $check_image = false;
                if ($this->request->data['TradingGroup']['new_image'] == 'false') {
                    $flag = false;
                } elseif (!empty($this->request->data['TradingGroup']['new_image'])) {
                    $check_image = FileManager::check_image($this->request->data['TradingGroup']['image'], $this->request->data['TradingGroup']['new_image']);
                    if ($check_image == ConstantsFileErrorTypes::OK) {
                        $file_name = FileManager::upload_image_webroot($this->request->data['TradingGroup']['new_image'], $this->request->data['TradingGroup']['image'], ConstantsFileType::IMAGE, FilePaths::TRADING_GROUP_IMAGES_RELATIVE);
                        $this->request->data['TradingGroup']['image'] = $file_name;
                        if (!$file_name) {
                            $flag = false;
                        } else {
                            $flag = FileManager::delete_file(WWW_ROOT . '../', FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $oldImage);
                        }
                    }
                } else {
                    $image_mame = $this->TradingGroup->findById($trading_group_id);
                    $this->request->data['TradingGroup']['image'] = $image_mame['TradingGroup']['image'];
                }
                $trading_group_bd = $this->TradingGroup->edit_trading_group($this->request->data, $trading_group['TradingGroup']['id']);
                if ($flag && $trading_group_bd) {
                    if (!$check_image || $check_image == ConstantsFileErrorTypes::OK) {
                        $this->TradingGroup->generate_style_trading_group($this->request->data);
                        $msg = h(sprintf(__t('TradingGroup.Edit_well'), $trading_group['TradingGroup']['name']));
                        $this->Session->setFlashSuccess($msg);
                        $this->redirect($this->request->here);
                    } elseif ($check_image == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                } else {
                    if (!$flag) {
                        $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                    } elseif ($error_size) {
                        $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                    } elseif ($error_upload) {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    } elseif ($check_image == ConstantsFileErrorTypes::IMAGE_EXTENSION_ERROR) {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                }
            } else {
                $this->request->data = $trading_group;
            }
            $this->setVarForm();
            $this->setVarDate();
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'trading_groups',
                    'action' => 'view',
                    $trading_group_id
                ),
            );
            $this->set(array(
                'cancel_action' => $cancelAction,
                'trading_group' => $trading_group,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete TradingGroup.
     */
    public function ajax_delete($trading_group_id)
    {
        $this->verify_ajax($this->request);
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $trading_group = $this->TradingGroup->findByIdAndAagRegionId($trading_group_id, $aagRegionId);

        if (
            $aagRegionId &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRADING_GROUPS)
        ) {

            $can_delete = true;
            $can_delete_text = 'true';
            $error_text = '';

            $models_relationship = $this->getModels();

            if (!empty($trading_group)) {
                foreach ($models_relationship as $model => $translation) {
                    $object = $this->$model->findByTradingGroupId($trading_group_id);
                    if (!empty($object)) {
                        $can_delete = false;
                        $error_text = $translation;
                        break;
                    }
                }
            }

            $distributor = $this->Distributor->findFirstByTradingGroupId($trading_group_id);
            if ($can_delete && !$distributor) {
                //Before deleting Trading Group, delete relationships with positions.
                $conditions = array(
                    'trading_group_id' => $trading_group_id
                );
                $positions_bd = $this->PositionConfigTradingGroup->deleteAll($conditions);
                if ($positions_bd) {
                    $tg_bd = $this->TradingGroup->delete($trading_group_id);
                    if (!$tg_bd) {
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
            'CommunicationTradingGroup' =>  __t('General.Relation_with') . ' ' . __t('Communication.Communication'),
            'ShortcutTradingGroup' =>  __t('General.Relation_with') . ' ' . __t('Maintenance.Shortcuts'),
            'SupplierTradingGroup' =>  __t('General.Relation_with') . ' ' . __t('Software.Supplier'),
            'TradingGroupDistributorNetwork' =>  __t('General.Relation_with') . ' ' . __t('Network.Distributors_networks'),
            'TradingGroupNetwork' =>  __t('General.Relation_with') . ' ' . __t('Network.Networks'),
        );
    }

    /**
     * AJAX add TradingGroupNetwork.
     */
    public function ajax_add_network($trading_group_id, $network_id)
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRADING_GROUPS)
        ) {
            $new_trading_group_network = array(
                'trading_group_id' => $trading_group_id,
                'network_id' => $network_id,
            );
            $this->TradingGroupNetwork->new_trading_groups_networks($new_trading_group_network);
            $data_network = $this->TradingGroup->Network->findById($network_id);

            $data_network['Network']['image'] = FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $data_network['Network']['image']);

            $this->layout = false;
            $this->render(false);

            $js_array = json_encode($data_network);
            echo $js_array;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete TradingGroupNEtwork.
     */
    public function ajax_delete_network($trading_group_id, $network_id)
    {
        $this->verify_ajax($this->request);
        $tradingGroupNetwork = $this->TradingGroupNetwork->find('first', array(
            'conditions' => array(
                'trading_group_id' => $trading_group_id,
                'network_id' => $network_id
            )
        ));

        if (
            $tradingGroupNetwork &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::TRADING_GROUPS)
        ) {

            if (!$this->request->is('get')) {
                $delete = $this->TradingGroupNetwork->delete($tradingGroupNetwork['TradingGroupNetwork']['id']);
                if (!$delete) {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
                }
            }

            $this->render(false);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search users trading groups.
     */
    public function ajax_search_users_trading_group_permissions()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_TRADING_GROUP) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::TRADING_GROUPS)
            )
        ) {
            $search = $this->request->query;
            $data = $this->request->data;

            $contacts_tmp = $this->whoViewThisTradingGroup($data['trading_group_id'], $search['permission_id']);
            $query = $this->User->_query('getByContactId');
            $conditions['User.contact_id'] = $contacts_tmp;
            $query['conditions'][] = $conditions;
            $query['conditions'][] = array('User.aag_region_id' => CakeSession::read('Auth.User.aag_region_id'));
            $users_trading_group_permissions = $this->User->find('all', $query);

            $positions = $this->Position->search_list();
            $roles = $this->Role->search_list();
            $trading_groups_permissions = $this->Permission->getByGroupingPermission(ConstantsGroupingPermissions::TRADING_GROUP);

            $this->set(array(
                'users_trading_group_permissions' => $users_trading_group_permissions,
                'trading_groups_permissions' => $trading_groups_permissions,
                'positions' => $positions,
                'roles' => $roles
            ));

            $this->layout = null;
            $this->render('/TradingGroups/Elements/trading_groups_permissions_search');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function whoViewThisTradingGroup($trading_group_id, $permission_id)
    {
        $trading_group = $this->TradingGroup->findById($trading_group_id);
        $group_permissions = $this->GroupPermissionPermission->getAllGroupPermissionsListByPermissionId($permission_id);

        $positions = $this->PositionConfig->getPositionsConfigToTradingGroup($group_permissions, $trading_group['TradingGroup']['id']);

        $contacts = $this->Contact->getContactListByPositionId($positions);

        return $contacts;
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

    private function setVarForm()
    {
        $user = $this->Acceso->user();
        $user_aag_region_id = $user['aag_region_id'];
        $user_role_id = $user['role_id'];
        $aag_regions = $this->AagRegion->region_list();

        $this->set(array(
            'aag_regions' => $aag_regions,
            'user_aag_region_id' => $user_aag_region_id,
            'user_role_id' => $user_role_id,
        ));
    }
}
