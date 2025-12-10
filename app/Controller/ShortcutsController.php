<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class ShortcutsController extends AppController
{
    public $uses = array(
        'Shortcut',
        'CustomerActivity',
        'GarageDistributorShortcut',
        'Network',
        'DistributorNetwork',
        'TradingGroup',
        'Position',
        'Role',
        'RepairMaintenance',
        'ShortcutNetwork',
        'ShortcutTradingGroup',
        'ShortcutRole',
        'ShortcutType',
        'ShortcutDistributorNetwork',
        'ShortcutPosition',
        'ShortcutCustomerActivity',
    );

    /**
     * Shortcuts home page for software widget.
     */
    public function home() {}

    /**
     * Delete shortcut.
     */
    public function remove_shortcut($id)
    {
        $this->verify_ajax($this->request);
        $shortcut_data = $this->GarageDistributorShortcut->findById($id);
        $this->GarageDistributorShortcut->delete($id);
        $shortcut = $this->Shortcut->findById($shortcut_data['GarageDistributorShortcut']['shortcut_id']);

        if (CakeSession::read('Auth.User.garage_id')) {
            $favorites_shortcuts = $this->Shortcut->findsFavoritesShortcuts(CakeSession::read('Auth.User.id'), CakeSession::read('Auth.User.current_network'));
        } else {
            $networks = $this->Network->find('all');
            $favorites_shortcuts = $this->Shortcut->findsFavoritesShortcuts(CakeSession::read('Auth.User.id'), Hash::extract($networks, '{n}.Network.id'));
        }

        $this->set(array(
            'shortcut' => $shortcut,
            'favorites_shortcuts' => $favorites_shortcuts
        ));

        $this->layout = false;
        $this->render('home');
    }

    /**
     * AJAX shortcut view.
     */
    public function ajax_view($shortcut_id)
    {
        $this->verify_ajax($this->request);
        $shortcut = $this->Shortcut->findById($shortcut_id);

        if (
            $shortcut &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->haveDefaultPermission(ConstantsPermissionsGrouping::SHORTCUTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
                )
            )
        ) {
            $user = CakeSession::read('Auth.User');

            $this->AagRegion = ClassRegistry::init("AagRegion");
            $aag_region = $this->AagRegion->findById($user['aag_region_id']);
            $urlRM = $aag_region['AagRegion']['url_rm'];

            if ($shortcut['Shortcut']['code'] == 'RM') {
                $tokenLoginRM = $this->RepairMaintenance->operacion_login($user);
            }

            if ($this->request->is('get')) {
                if (CakeSession::read('Auth.User.garage_id')) {
                    $shortcut_data = $this->GarageDistributorShortcut->findByGarageIdAndShortcutId($user['garage_id'], $shortcut_id);
                } elseif (CakeSession::read('Auth.User.distributor_id')) {
                    $shortcut_data = $this->GarageDistributorShortcut->findByDistributorIdAndShortcutId($user['distributor_id'], $shortcut_id);
                } else {
                    $shortcut_data = $this->GarageDistributorShortcut->findByShortcutId($shortcut_id);
                }
            } else {

                $shortcut_data = $this->request->data;
                $shortcut_data['Shortcut']['garage_id'] = $user['garage_id'];
                $shortcut_data['Shortcut']['distributor_id'] = $user['distributor_id'];
                $shortcut_data['Shortcut']['shortcut_id'] = $shortcut_id;
                $shortcut_data['Shortcut']['user_id'] = CakeSession::read('Auth.User.id');

                $shortcut_data = $this->GarageDistributorShortcut->Add($shortcut_data);

                if (!$shortcut_data) {
                    $this->Session->setFlashError(__t('General.Bad_save'));
                }
            }

            //JWT: Decoding field parameter_value_2 before connect.
            if (isset($shortcut_data['GarageDistributorShortcut']['parameter_value_2'])) {
                $jwt_encode = $shortcut_data['GarageDistributorShortcut']['parameter_value_2'];
                $key = Texto::encryptDecryptText(JWT_CODE_WORD, false);
                $jwt_decode = JWT::decode($jwt_encode, $key, array('HS256'));

                $shortcut_data['GarageDistributorShortcut']['parameter_value_2'] = $jwt_decode;
                $shortcut_data['Shortcut']['parameter_value_2'] = $jwt_decode;
            }

            $this->set(array(
                'shortcut' => $shortcut,
                'shortcut_data' => $shortcut_data,
                'tokenLoginRM' => (!empty($tokenLoginRM)) ? $tokenLoginRM : array(),
                'urlRM' => isset($urlRM) ? $urlRM : '',
                'user' => $user,
            ));

            if ($shortcut_data) {
                $this->request->data['Shortcut'] = $shortcut_data['GarageDistributorShortcut'];
                $this->layout = false;
                $this->render('/Shortcuts/view');
            } elseif ($shortcut['Shortcut']['is_sso']) {
                $this->request->data = $this->Shortcut->findById($shortcut_id);
                $this->layout = false;
                $this->render('/Shortcuts/home');
            } else {
                $this->layout = false;
                $this->render('view');
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Shortcuts maintenance page.
     */
    public function maintenance_shortcuts()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::SHORTCUTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $search = $this->request->query;
            $search['aag_region_id'] = $aagRegionId;
            $this->request->data['Search'] = $search;
            $conditions = $this->Shortcut->conditions($search);
            $shortcuts = $this->custom_pagination(
                $this->Shortcut->_query('search_maintenance'),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                null,
                'PaginatorOrderCustom'
            );

            $networksList = $this->Network->find('list', array(
                'conditions' => array(
                    'Network.aag_region_id' => $aagRegionId
                ),
                'order' => 'Network.name',
            ));
            // full list, in case there are other networks linked (from another region)
            $networksListAll = $this->Network->find('list');
            $rolesList = $this->Role->search_list();
            $shortcutsTypesList = $this->ShortcutType->search_list();

            $active = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $this->set(array(
                'shortcuts' => $shortcuts,
                'networks_list' => $networksList,
                'networks_list_all' => $networksListAll,
                'roles_list' => $rolesList,
                'shortcuts_types_list' => $shortcutsTypesList,
                'active' => $active,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Shortcuts types maintence types.
     */
    public function maintenance_shortcuts_types()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::SHORTCUTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $shortcuts_types = $this->custom_pagination(
                $this->ShortcutType->_query('home'),
                array(),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'ShortcutType',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(array(
                'shortcuts_types' => $shortcuts_types,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create Shortcut.
     */
    public function add()
    {
        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::SHORTCUTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'shortcuts',
                    'action' => 'maintenance_shortcuts',
                ),
            );

            if (!$this->request->is('get')) {
                $errorFile = false;
                $checkImage = false;
                if (!empty($this->request->data['Shortcut']['new_image'])) {
                    $checkImage = FileManager::check_image($this->request->data['Shortcut']['image'], $this->request->data['Shortcut']['new_image']);
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $image = FileManager::upload_image_webroot($this->request->data['Shortcut']['new_image'], $this->request->data['Shortcut']['image'], ConstantsFileType::IMAGE, FilePaths::SHORTCUT_IMAGES_RELATIVE);
                        if (!$image) {
                            $errorFile = true;
                        }
                        $this->request->data['Shortcut']['image'] = $image;
                    }
                }
                $this->request->data['Shortcut']['end_date'] = Fecha::toFormatoBd($this->request->data['Shortcut']['end_date']);
                $this->request->data['Shortcut']['start_date'] = Fecha::toFormatoBd($this->request->data['Shortcut']['start_date']);
                $shortcutBd = $this->Shortcut->add($this->request->data);

                if ($shortcutBd && !$errorFile) {
                    if (!$checkImage || $checkImage == ConstantsFileErrorTypes::OK) {
                        foreach ($this->request->data['Network'] as $network) {
                            if ($network != 0) {
                                if (!$this->ShortcutNetwork->add($shortcutBd['Shortcut']['id'], $network)) {
                                    return false;
                                }
                            }
                        }

                        if (isset($this->request->data['DistributorNetwork'])) {
                            foreach ($this->request->data['DistributorNetwork'] as $key => $distributor_network) {
                                if ($distributor_network != 0) {
                                    if (!$this->ShortcutDistributorNetwork->add($shortcutBd['Shortcut']['id'], $distributor_network)) {
                                        return false;
                                    }
                                }
                            }
                        }

                        foreach ($this->request->data['TradingGroup'] as $key => $trading_group) {
                            if ($trading_group != 0) {
                                if (!$this->ShortcutTradingGroup->add($shortcutBd['Shortcut']['id'], $trading_group)) {
                                    return false;
                                }
                            }
                        }

                        foreach ($this->request->data['GaragePosition'] as $position_id => $value) {
                            if ($value != 0) {
                                if (!$this->ShortcutPosition->add($shortcutBd['Shortcut']['id'], $position_id)) {
                                    return false;
                                }
                            }
                        }

                        foreach ($this->request->data['DistributorPosition'] as $position_id => $value) {
                            if ($value != 0) {
                                if (!$this->ShortcutPosition->add($shortcutBd['Shortcut']['id'], $position_id)) {
                                    return false;
                                }
                            }
                        }

                        foreach ($this->request->data['Activity'] as $activity_id => $value) {
                            if ($value != 0) {
                                if (!$this->ShortcutCustomerActivity->add($shortcutBd['Shortcut']['id'], $activity_id)) {
                                    return false;
                                }
                            }
                        }

                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect(
                            array(
                                'controller' => 'shortcuts',
                                'action' => 'edit',
                                $this->Shortcut->getLastInsertID()
                            )
                        );
                    }
                } elseif ($errorFile) {
                    $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                }
            }

            $garagePositions = $this->getRolePositions(ConstantsRoles::GARAGE);
            $distributorPositions = $this->getRolePositions(ConstantsRoles::DISTRIBUTOR);

            $this->setVarForm();
            $this->setVarNetworks();
            $this->setVarDistributorsNetworks();
            $this->setVarTradingGroups();
            $this->setVarCustomerActivities();
            $this->set(array(
                'cancel_action' => $cancelAction,
                'garage_positions' => $garagePositions,
                'distributor_positions' => $distributorPositions,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit shortcut.
     */
    public function edit($shortcut_id)
    {
        $shortcut = $this->Shortcut->findShorcutDatas($shortcut_id);
        if (
            $shortcut &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::SHORTCUTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'shortcuts',
                    'action' => 'maintenance_shortcuts',
                ),
            );

            $delete_shortcut = $this->Shortcut->check_delete($shortcut_id);

            if (!$this->request->is('get')) {
                $errorFile = false;
                $checkImage = false;
                if ($this->request->data['Shortcut']['new_image'] == 'false') {
                    $errorFile = true;
                } elseif (!empty($this->request->data['Shortcut']['new_image'])) {
                    $checkImage = FileManager::check_image($this->request->data['Shortcut']['image'], $this->request->data['Shortcut']['new_image']);
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $oldImage = $shortcut['Shortcut']['image'];
                        $image = FileManager::upload_image_webroot($this->request->data['Shortcut']['new_image'], $this->request->data['Shortcut']['image'], ConstantsFileType::IMAGE, FilePaths::SHORTCUT_IMAGES_RELATIVE);
                        if ($image) {
                            FileManager::delete_file(WWW_ROOT, FilePaths::SHORTCUT_IMAGES_RELATIVE . $oldImage);
                            $this->request->data['Shortcut']['image'] = $image;
                        } else {
                            $errorFile = true;
                        }
                    }
                }
                if ($this->request->data['Shortcut']['is_sso'] == ConstantsBooleans::NO_ACTIVE) {
                    $this->request->data['Shortcut']['parameter_name_1'] = '';
                    $this->request->data['Shortcut']['parameter_name_2'] = '';
                }

                $this->request->data['Shortcut']['end_date'] = Fecha::toFormatoBd($this->request->data['Shortcut']['end_date']);
                $this->request->data['Shortcut']['start_date'] = Fecha::toFormatoBd($this->request->data['Shortcut']['start_date']);
                $shortcutBd = $this->Shortcut->edit($this->request->data);
                if (!$errorFile) {
                    if (!$checkImage || $checkImage == ConstantsFileErrorTypes::OK) {
                        if ($shortcutBd) {
                            $id = $shortcut['Shortcut']['id'];
                            // Delete networks
                            $shortcut_networks = $this->ShortcutNetwork->findAllByShortcutId($id);
                            if ($shortcut_networks) {
                                foreach ($shortcut_networks as $shortcut_network) {
                                    if (!$this->ShortcutNetwork->delete($shortcut_network['ShortcutNetwork']['id'])) {
                                        return false;
                                    }
                                }
                            }

                            // Delete distributors_networks
                            $this->ShortcutDistributorNetwork->deleteAll(array('shortcut_id' => $shortcutBd['Shortcut']['id']));

                            // Delete trading_groups
                            $shortcutTradingGroups = $this->ShortcutTradingGroup->findAllByShortcutId($id);
                            if ($shortcutTradingGroups) {
                                foreach ($shortcutTradingGroups as $shortcut) {
                                    if (!$this->ShortcutTradingGroup->delete($shortcut['ShortcutTradingGroup']['id'])) {
                                        return false;
                                    }
                                }
                            }

                            // Delete Positions
                            $this->ShortcutPosition->deleteAll(array('shortcut_id' => $shortcutBd['Shortcut']['id']));

                            // Delete Activities
                            $this->ShortcutCustomerActivity->deleteAll(array('shortcut_id' => $shortcutBd['Shortcut']['id']));

                            foreach ($this->request->data['Network'] as $key => $network) {
                                if ($network != 0) {
                                    if (!$this->ShortcutNetwork->add($shortcutBd['Shortcut']['id'], $network)) {
                                        return false;
                                    }
                                }
                            }

                            if (isset($this->request->data['DistributorNetwork'])) {
                                foreach ($this->request->data['DistributorNetwork'] as $key => $distributor_network) {
                                    if ($distributor_network != 0) {
                                        if (!$this->ShortcutDistributorNetwork->add($shortcutBd['Shortcut']['id'], $distributor_network)) {
                                            return false;
                                        }
                                    }
                                }
                            }

                            foreach ($this->request->data['TradingGroup'] as $key => $trading_group) {
                                if ($trading_group != 0) {
                                    if (!$this->ShortcutTradingGroup->add($shortcutBd['Shortcut']['id'], $trading_group)) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['GaragePosition'] as $position_id => $value) {
                                if ($value != 0) {
                                    if (!$this->ShortcutPosition->add($shortcutBd['Shortcut']['id'], $position_id)) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['DistributorPosition'] as $position_id => $value) {
                                if ($value != 0) {
                                    if (!$this->ShortcutPosition->add($shortcutBd['Shortcut']['id'], $position_id)) {
                                        return false;
                                    }
                                }
                            }

                            foreach ($this->request->data['Activity'] as $activity_id => $value) {
                                if ($value != 0) {
                                    if (!$this->ShortcutCustomerActivity->add($shortcutBd['Shortcut']['id'], $activity_id)) {
                                        return false;
                                    }
                                }
                            }

                            $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                            $this->redirect($this->request->here);
                        }
                    } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                } else {
                    $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                }
            } else {
                $this->request->data = $shortcut;
                $this->request->data['Shortcut']['Networks'] = explode(",", $shortcut[0]['Networks']);
                $this->request->data['Shortcut']['end_date'] = Fecha::toFormatoVista($this->request->data['Shortcut']['end_date']);
                $this->request->data['Shortcut']['start_date'] = Fecha::toFormatoVista($this->request->data['Shortcut']['start_date']);
            }

            $garagePositions = $this->getRolePositions(ConstantsRoles::GARAGE);
            $distributorPositions = $this->getRolePositions(ConstantsRoles::DISTRIBUTOR);
            $shortcutDistributorsNetworks = $this->ShortcutDistributorNetwork->getShortcutDistributorsNetworksByShortcutId($shortcut['Shortcut']['id']);
            $shortcutTradingGroups = $this->ShortcutTradingGroup->getTGByShortcutId($shortcut['Shortcut']['id']);
            $shortcutPositions = $this->ShortcutPosition->getListPositionsByShortcutId($shortcut['Shortcut']['id']);
            $shortcutCustomersActivities = $this->ShortcutCustomerActivity->getListActivitiesByShortcutId($shortcut['Shortcut']['id']);

            $this->setVarForm();
            $this->setVarNetworks();
            $this->setVarDistributorsNetworks();
            $this->setVarCustomerActivities();
            $this->setVarShortcutsNetworks($shortcut_id);
            $this->setVarShortcutsTradingGroups($shortcut_id);
            $this->setVarTradingGroups();
            $this->set(array(
                'cancel_action' => $cancelAction,
                'shortcut' => $shortcut,
                'shortcut_distributors_networks' => $shortcutDistributorsNetworks,
                'shortcut_trading_groups' => $shortcutTradingGroups,
                'shortcut_positions' => $shortcutPositions,
                'shortcut_customers_activities' => $shortcutCustomersActivities,
                'delete_shortcut' => $delete_shortcut,
                'garage_positions' => $garagePositions,
                'distributor_positions' => $distributorPositions,
                'shortcut_id' => $shortcut_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * View Software shortcut.
     */
    public function software_view($shortcut_id)
    {
        $shortcut = $this->Shortcut->findShorcutDatas($shortcut_id);
        if (
            $shortcut &&
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
        ) {
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'shortcuts',
                    'action' => 'maintenance_shortcuts',
                ),
            );

            $delete_shortcut = $this->Shortcut->check_delete($shortcut_id);

            $this->request->data = $shortcut;
            $this->request->data['Shortcut']['Networks'] = explode(",", $shortcut[0]['Networks']);
            $this->request->data['Shortcut']['end_date'] = Fecha::toFormatoVista($this->request->data['Shortcut']['end_date']);
            $this->request->data['Shortcut']['start_date'] = Fecha::toFormatoVista($this->request->data['Shortcut']['start_date']);

            $garagePositions = $this->getRolePositions(ConstantsRoles::GARAGE);
            $distributorPositions = $this->getRolePositions(ConstantsRoles::DISTRIBUTOR);
            $shortcutDistributorsNetworks = $this->ShortcutDistributorNetwork->getShortcutDistributorsNetworksByShortcutId($shortcut['Shortcut']['id']);
            $shortcutTradingGroups = $this->ShortcutTradingGroup->getTGByShortcutId($shortcut['Shortcut']['id']);
            $shortcutPositions = $this->ShortcutPosition->getListPositionsByShortcutId($shortcut['Shortcut']['id']);
            $shortcutCustomersActivities = $this->ShortcutCustomerActivity->getListActivitiesByShortcutId($shortcut['Shortcut']['id']);

            $this->setVarForm();
            $this->setVarNetworks();
            $this->setVarDistributorsNetworks();
            $this->setVarCustomerActivities();
            $this->setVarShortcutsNetworks($shortcut_id);
            $this->setVarShortcutsTradingGroups($shortcut_id);
            $this->setVarTradingGroups();
            $this->set(array(
                'cancel_action' => $cancelAction,
                'shortcut' => $shortcut,
                'shortcut_distributors_networks' => $shortcutDistributorsNetworks,
                'shortcut_trading_groups' => $shortcutTradingGroups,
                'shortcut_positions' => $shortcutPositions,
                'shortcut_customers_activities' => $shortcutCustomersActivities,
                'delete_shortcut' => $delete_shortcut,
                'garage_positions' => $garagePositions,
                'distributor_positions' => $distributorPositions,
                'shortcut_id' => $shortcut_id,
                'shurtcut_type_list' => $this->ShortcutType->search_list(),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete shortcurt.
     */
    public function delete($shortcut_id)
    {
        $shortcutName = $this->Shortcut->findById($shortcut_id);
        if (
            $shortcutName &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::SHORTCUTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            try {
                $shortcut = $this->Shortcut->delete($shortcut_id);
                $shortcut = FileManager::delete_file(WWW_ROOT, FilePaths::SHORTCUT_IMAGES_RELATIVE . $shortcutName['Shortcut']['image']);
            } catch (Exception $e) {
                $this->Session->setFlashError(__t('Shortcut.Delete_error'));
            }

            if ($shortcut) {
                $this->Session->setFlashSuccess(__t('Shortcut.Well_deleted'));
            } else {
                $this->Session->setFlashError(__t('Shortcut.Delete_error'));
            }

            $this->redirect(
                array(
                    'controller' => 'shortcuts',
                    'action' => 'maintenance_shortcuts',
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit shortcut type.
     */
    public function edit_type($shortcut_type_id)
    {
        $shortcutType = $this->ShortcutType->findById($shortcut_type_id);
        if (
            $shortcutType &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::SHORTCUTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'shortcuts',
                    'action' => 'maintenance_shortcuts_types',
                ),
            );
            if (!$this->request->is('get')) {
                $shortcutBd = $this->ShortcutType->edit($this->request->data);

                if ($shortcutBd) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                }
            } else {
                $this->request->data = $shortcutType;
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'shortcut_type' => $shortcutType,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX load favorite Shortcuts.
     */
    public function ajax_load_favorite()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::SHORTCUTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            if (CakeSession::read('Auth.User.garage_id')) {
                $favorites_shortcuts = $this->Shortcut->findsFavoritesShortcuts(CakeSession::read('Auth.User.id'), CakeSession::read('Auth.User.current_network'));
            } else {
                $networks = $this->Network->find('all');
                $favorites_shortcuts = $this->Shortcut->findsFavoritesShortcuts(CakeSession::read('Auth.User.id'), Hash::extract($networks, '{n}.Network.id'));
            }
            $this->set(array(
                'favorites_shortcuts' => $favorites_shortcuts
            ));
            $this->layout = false;
            $this->render('../Home/Elements/favorites');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX edit favorite Shortcut.
     */
    public function ajax_edit_favorite()
    {
        $this->verify_ajax($this->request);

        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::SHORTCUTS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            if (CakeSession::read('Auth.User.garage_id')) {
                $garage_distributor_shortcut = $this->GarageDistributorShortcut->findByGarageIdAndShortcutId(CakeSession::read('Auth.User.garage_id'), $this->request->data['id']);
            } elseif (CakeSession::read('Auth.User.distributor_id')) {
                $garage_distributor_shortcut = $this->GarageDistributorShortcut->findByDistributorIdAndShortcutId(CakeSession::read('Auth.User.distributor_id'), $this->request->data['id']);
            } else {
                $garage_distributor_shortcut = $this->GarageDistributorShortcut->findByShortcutId($this->request->data['id']);
            }

            if (!empty($garage_distributor_shortcut)) {
                $shortcut_favorite = array(
                    'GarageDistributorShortcut' => array(
                        'id' => $garage_distributor_shortcut['GarageDistributorShortcut']['id'],
                        'fav' => $this->request->data['fav'],
                    )
                );
                $flag_save = $this->GarageDistributorShortcut->edit($shortcut_favorite);
            } else {
                $shortcut_favorite = array(
                    'Shortcut' => array(
                        'garage_id' => CakeSession::read('Auth.User.garage_id') != ConstantsBooleans::NO_ACTIVE ? CakeSession::read('Auth.User.garage_id') : null,
                        'distributor_id' => CakeSession::read('Auth.User.distributor_id') != ConstantsBooleans::NO_ACTIVE ? CakeSession::read('Auth.User.distributor_id') : null,
                        'shortcut_id' => $this->request->data['id'],
                        'network_id' => CakeSession::read('Auth.User.current_network'),
                        'user_id' => CakeSession::read('Auth.User.id'),
                        'fav' => ConstantsBooleans::YES,
                    )
                );
                $flag_save = $this->GarageDistributorShortcut->add($shortcut_favorite);
            }

            if ($flag_save) {
                if (CakeSession::read('Auth.User.garage_id')) {
                    $favorites_shortcuts = $this->Shortcut->findsFavoritesShortcuts(CakeSession::read('Auth.User.id'), CakeSession::read('Auth.User.current_network'));
                } else {
                    $networks = $this->Network->find('all');
                    $favorites_shortcuts = $this->Shortcut->findsFavoritesShortcuts(CakeSession::read('Auth.User.id'), Hash::extract($networks, '{n}.Network.id'));
                }
            } else {
                $favorites_shortcuts = null;
            }

            $this->set(array(
                'favorites_shortcuts' => $favorites_shortcuts
            ));
            $this->layout = false;
            $this->render('../Home/Elements/favorites');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm()
    {
        $shortcutTypes = $this->ShortcutType->search_list();
        $networksList = $this->Network->find('list');
        $rolesList = $this->Role->search_list();
        unset($rolesList[ConstantsRoles::ADMIN]);

        $this->set(array(
            'shortcut_types' => $shortcutTypes,
            'networks_list' => $networksList,
            'roles_list' => $rolesList,
        ));
    }

    /**
     * Set Networks by AagRegion ID.
     */
    private function setVarNetworks()
    {
        $networks = $this->Network->find(
            'all',
            array(
                'fields' => array(
                    'Network.id',
                    'Network.name',
                    'Network.image',
                ),
                'conditions' => array(
                    'Network.aag_region_id' => CakeSession::read('Auth.User.aag_region_id')
                ),
            )
        );

        $this->set(array(
            'networks' => $networks
        ));
    }

    /**
     * Get Positions by Role ID.
     */
    private function getRolePositions($role_id)
    {
        $positions = $this->Position->find(
            'all',
            array(
                'conditions' => array(
                    'role_id' => $role_id
                ),
                'fields' => array(
                    'Position.id',
                    'Position.name' . __s(),
                )
            )
        );

        return $positions;
    }

    /**
     * Set Distributor Networks by AagRegion ID.
     */
    private function setVarDistributorsNetworks()
    {
        $distributorsNetworks = $this->DistributorNetwork->find(
            'all',
            array(
                'fields' => array(
                    'DistributorNetwork.id',
                    'DistributorNetwork.name',
                    'DistributorNetwork.image',
                ),
                'conditions' => array(
                    'DistributorNetwork.aag_region_id' => CakeSession::read('Auth.User.aag_region_id')
                )
            )
        );

        $this->set(array(
            'distributors_networks' => $distributorsNetworks
        ));
    }

    /**
     * Set Trading Groups independent by AagRegion ID.
     */
    private function setVarTradingGroups()
    {
        $tradingGroups = $this->TradingGroup->find(
            'all',
            array(
                'conditions' => array(
                    'TradingGroup.independent' => ConstantsBooleans::YES,
                    'TradingGroup.aag_region_id' => CakeSession::read('Auth.User.aag_region_id')
                ),
                'fields' => array(
                    'TradingGroup.id',
                    'TradingGroup.name',
                    'TradingGroup.image',
                )
            )
        );

        $this->set(array(
            'trading_groups' => $tradingGroups
        ));
    }

    /**
     * Set Customer Activities.
     */
    private function setVarCustomerActivities()
    {
        $customersActivities = $this->CustomerActivity->search_list();

        $this->set(array(
            'customers_activities' => $customersActivities
        ));
    }

    /**
     * Set Shortcut Networks by Shortcut ID.
     */
    private function setVarShortcutsNetworks($shortcut_id)
    {
        $shortcutsNetworks = $this->ShortcutNetwork->getShortcutsNetworksByShortcutId($shortcut_id);

        $this->set(array(
            'shortcuts_networks' => $shortcutsNetworks
        ));
    }

    /**
     * Set Shortcut Trading Groups by Shortcut ID.
     */
    private function setVarShortcutsTradingGroups($shortcut_id)
    {
        $shortcutsTradingGroups  = $this->ShortcutTradingGroup->getTGByShortcutId($shortcut_id);

        $this->set(array(
            'shortcuts_trading_groups' => $shortcutsTradingGroups
        ));
    }
}
