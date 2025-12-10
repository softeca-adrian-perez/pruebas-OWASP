<?php
class SuppliersController extends AppController
{
    public $uses = array(
        'Supplier',
        'Network',
        'SupplierFile',
        'SupplierCategory',
        'TradingGroup',
        'User',
        'AagRegion',
        'SupplierImage'
    );

    /**
     * Suppliers home page.
     */
    public function index()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_SUPPLIERS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::GARAGE) {
                $this->loadModel('GarageNetwork');
                $networks = $this->GarageNetwork->findNetworksByGarage(CakeSession::read('Auth.User.garage_id'));
                foreach ($networks as $network) {
                    $suppliers = $this->Supplier->getAllAboutSuppliersByNetwork($network);
                }
            } elseif (CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR) {
                $this->loadModel('Distributor');
                $distributors = $this->Distributor->findById(CakeSession::read('Auth.User.distributor_id'));
                $suppliers = $this->Supplier->getAllAboutSuppliersByTradingGroup($distributors['Distributor']['trading_group_id']);
            } else {
                $suppliers = $this->Supplier->getAllAboutSuppliersConditions($aagRegionId, CakeSession::read('Auth.User.role_id'));
            }

            foreach ($suppliers as $key => $supplier) {
                $suppliers_categories = $this->SupplierCategory->getByPositionId(CakeSession::read('Auth.User.Contact.position_id'), $user['aag_region_id']);
                $suppliers[$key]['SupplierFile'] = array();
                foreach ($suppliers_categories as $category_id => $name) {
                    $files = $this->Supplier->SupplierFile->getFilesBySupplierIdAndCategoryIdAndActive($supplier['Supplier']['id'], $category_id);
                    if (!empty($files)) {
                        $suppliers[$key]['SupplierFile'][$name] = $files;
                    }
                }

                $suppliers[$key]['Brands'] = $this->Supplier->Brand->getAllBrandsBySupplierId($supplier['Supplier']['id']);
            }

            if (in_array(ConstantsPermissionsGrouping::SUPPLIERS, $this->Session->read('Auth.User.Permissions'))) {
                $value_permission = true;
                $networks = $this->Network->find('all', array(
                    'conditions' => array(
                        'Network.aag_region_id' => $aagRegionId
                    )
                ));
                $trading_groups = $this->TradingGroup->find('all', array(
                    'conditions' => array(
                        'independent' => ConstantsBooleans::YES,
                        'TradingGroup.aag_region_id' => $aagRegionId
                    )
                ));
                $this->set(array(
                    'networks' => $networks,
                    'trading_groups' => $trading_groups
                ));
            } else {
                $value_permission = false;
            }

            $this->setVarDate();

            $this->set(array(
                'suppliers' => $suppliers,
                'value_permission' => $value_permission,
                'active_page' => ConstantsActiveHomePage::PAGE_4,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Supplier maintenance page.
     */
    public function maintenance_suppliers()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $active = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $brands = $this->Supplier->Brand->search_list_conditions($aagRegionId);

            $search = $this->request->query;
            if (!isset($search['aag_region_id'])) {
                $search['aag_region_id'] = $aagRegionId;
            }
            $this->request->data['Search'] = $search;

            $suppliers = $this->custom_pagination(
                $this->Supplier->query('supplier'),
                $this->Supplier->conditions($search),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Supplier',
                null,
                'PaginatorOrderCustom'
            );

            foreach ($suppliers as $key => $supplier) {
                $suppliers_brands = $this->Supplier->Brand->findAllBySupplierId($supplier['Supplier']['id']);
                $suppliers[$key]['Brands'] = '';
                foreach ($suppliers_brands as $brand) {
                    $suppliers[$key]['Brands'] .= $brand['Brand']['name'] . ', ';
                }
                $suppliers[$key]['Brands'] = trim($suppliers[$key]['Brands'], ', ');
            }

            $aagRegionsUser = array();
            $conditions = array('AagRegion.id' => $aagRegionId);
            $aagRegionsUser = $this->AagRegion->region_list_conditions($conditions);

            $this->set(array(
                'suppliers' => $suppliers,
                'brands' => $brands,
                'active' => $active,
                'aag_regions_user' => $aagRegionsUser
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create supplier.
     */
    public function add()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $trading_groups = $this->Supplier->SupplierTradingGroup->TradingGroup->getTradingGroupIndependentWithOutPermissions($aagRegionId);

            $networks = $this->Supplier->SupplierNetwork->Network->find(
                'all',
                array(
                    'conditions' => array(
                        'Network.aag_region_id' => $aagRegionId
                    )
                )
            );

            $url = array(
                'controller' => 'suppliers',
                'action' => 'maintenance_suppliers',
            );

            $suppliers_categories = $this->SupplierCategory->search_list($aagRegionId);

            if (!$this->request->is('get')) {
                $error_ext_file = false;
                $error_size = false;
                $check_file = false;
                if (!empty($this->request->data['SupplierImage']['new_image'])) {
                    $check_image = FileManager::check_image($this->request->data['SupplierImage']['file'], $this->request->data['SupplierImage']['new_image']);
                    if ($check_image == ConstantsFileErrorTypes::OK) {
                        $file_name = FileManager::upload_image_webroot(
                            $this->request->data['SupplierImage']['new_image'],
                            $this->request->data['SupplierImage']['file'],
                            ConstantsFileType::IMAGE,
                            FilePaths::SUPPLIERS_IMAGES_RELATIVE
                        );
                        if ($file_name) {
                            $this->request->data['SupplierImage']['file']['name'] = $file_name;
                            if ($this->Supplier->add($this->request->data)) {
                                if ($this->Supplier->SupplierImage->add($this->request->data, $this->Supplier->getLastInsertID())) {
                                    unset($this->request->data['SupplierFile']['file'][0]);

                                    if (isset($this->request->data['SupplierFile']['suppliers_categories'])) {
                                        //Decode array with suppliers_files information.
                                        $this->request->data['SupplierFile']['suppliers_categories'] = (array) json_decode($this->request->data['SupplierFile']['suppliers_categories']);
                                    }

                                    if (!empty($this->request->data['SupplierFile']['file'])) {
                                        // The set of information comes in the opposite direction to what interests us
                                        if (isset($this->request->data['SupplierFile']['suppliers_categories'])) {
                                            $this->request->data['SupplierFile']['suppliers_categories'] = array_values(array_reverse($this->request->data['SupplierFile']['suppliers_categories']));
                                        }
                                        $this->request->data['SupplierFile']['file'] = array_values($this->request->data['SupplierFile']['file']);

                                        foreach ($this->request->data['SupplierFile']['file'] as $key => $file) {
                                            if (isset($file['error']) && $file['error'] == ConstantsBooleans::NO) {
                                                //Take category_id from array: SupplierCategory if exists
                                                if (isset($this->request->data['SupplierFile']['suppliers_categories'][$key])) {
                                                    $supplier_info = (array) $this->request->data['SupplierFile']['suppliers_categories'][$key];
                                                } else {
                                                    $supplier_info = array();
                                                }
                                                $check_file = FileManager::check_file($file);
                                                if ($check_file == ConstantsFileErrorTypes::OK) {
                                                    if (!$this->Supplier->SupplierFile->saveFile($file, $this->Supplier->getLastInsertID(), $supplier_info, ConstantsFileType::FILE)) {
                                                        $error_ext_file = true;
                                                    }
                                                } else {
                                                    break;
                                                }
                                            } elseif (isset($file['error']) && $file['error'] == ConstantsFlag::ERROR_DIMENSIONS) {
                                                $error_size = true;
                                            }
                                        }
                                    }

                                    $trading_groups_id = [];
                                    if (isset($this->request->data['SupplierTradingGroup']['trading_group_id'])) {
                                        $trading_groups_id = $this->request->data['SupplierTradingGroup']['trading_group_id'];
                                    }
                                    if (!$error_size && !$error_ext_file) {
                                        if (!$check_file || $check_file == ConstantsFileErrorTypes::OK) {
                                            $tradings = array();
                                            foreach ($trading_groups_id as $key => $trading_group_id) {
                                                if ($trading_group_id > 0) {
                                                    $tradings[$key]['SupplierTradingGroup']['supplier_id'] = $this->Supplier->getLastInsertID();
                                                    $tradings[$key]['SupplierTradingGroup']['trading_group_id'] = $trading_group_id;
                                                }
                                            }
                                            $networks_id = $this->request->data['SupplierNetwork']['network_id'];
                                            $nets = array();
                                            foreach ($networks_id as $key => $network_id) {
                                                if ($network_id > 0) {
                                                    $nets[$key]['SupplierNetwork']['supplier_id'] = $this->Supplier->getLastInsertID();
                                                    $nets[$key]['SupplierNetwork']['network_id'] = $network_id;
                                                }
                                            }
                                            $this->Supplier->SupplierNetwork->saveMany($nets);
                                            $this->Supplier->SupplierTradingGroup->saveMany($tradings);
                                            $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                                            $this->redirect(
                                                array(
                                                    'controller' => 'suppliers',
                                                    'action' => 'edit',
                                                    $this->Supplier->getLastInsertID()
                                                )
                                            );
                                        } elseif ($check_file == ConstantsFileErrorTypes::SIZE_ERROR) {
                                            $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                                        } else {
                                            $this->Session->setFlashError(__t(ConstantsMessages::FILE_ERROR_EXTENSION));
                                        }
                                    } elseif ($error_ext_file) {
                                        $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                                    } else {
                                        $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                                    }
                                } else {
                                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                }
                            }
                        } else {
                            $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                        }
                    } elseif ($check_image == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                } else {
                    $this->Session->setFlashError(__t('Validation.Mandatory_to_choose_an_image'));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'trading_groups' => $trading_groups,
                'suppliers_categories' => $suppliers_categories,
                'networks' => $networks,
                'url_cancel' => $url,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit supplier.
     */
    public function edit($supplier_id)
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $supplier = $this->Supplier->getAllAboutSupplierId($supplier_id, $aagRegionId);

        if (
            $supplier &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
        ) {
            $suppliers_categories = $this->SupplierCategory->search_list($aagRegionId);
            $suppliers_categories_reverse = $this->SupplierCategory->search_list_reverse($aagRegionId);
            $supplier_files = array();
            foreach ($suppliers_categories as $category_id => $name) {
                $files = $this->Supplier->SupplierFile->getFilesBySupplierIdAndCategoryId($supplier_id, $category_id);
                if (!empty($files)) {
                    $supplier_files[$name] = $files;
                }
            }
            $supplier_trading_groups = $this->Supplier->SupplierTradingGroup->findAllBySupplierId($supplier_id);
            $supplier_networks = $this->Supplier->SupplierNetwork->findAllBySupplierId($supplier_id);

            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $trading_groups = $this->Supplier->SupplierTradingGroup->TradingGroup->getTradingGroupIndependentWithOutPermissions($aagRegionId);

            $networks = $this->Supplier->SupplierNetwork->Network->find(
                'all',
                array(
                    'conditions' => array(
                        'Network.aag_region_id' => $aagRegionId
                    )
                )
            );

            $url = array(
                'controller' => 'suppliers',
                'action' => 'maintenance_suppliers',
            );

            if ($this->request->is('get')) {
                $this->request->data = $supplier;
                $view_date = Fecha::toFormatoVista($this->request->data['Supplier']['publication_date']);
                $this->request->data['Supplier']['publication_date'] = $view_date;
            } else {
                $error_size = false;
                $error_file = false;
                $check_image = false;
                $check_file = false;
                $supplierImageId = $this->SupplierImage->findBySupplierId($supplier_id);
                if (!empty($this->request->data['SupplierImage']['new_image'])) {
                    $check_image = FileManager::check_image($this->request->data['SupplierImage']['file'], $this->request->data['SupplierImage']['new_image']);
                    if ($check_image == ConstantsFileErrorTypes::OK) {
                        $image = $supplier['SupplierImage']['source_name'];
                        $file_name = FileManager::upload_image_webroot(
                            $this->request->data['SupplierImage']['new_image'],
                            $this->request->data['SupplierImage']['file'],
                            ConstantsFileType::IMAGE,
                            FilePaths::SUPPLIERS_IMAGES_RELATIVE
                        );
                        if ($file_name) {
                            FileManager::delete_file(WWW_ROOT, FilePaths::SUPPLIERS_IMAGES_RELATIVE . $image);
                            $this->request->data['SupplierImage']['file']['name'] = $file_name;
                            $this->Supplier->SupplierImage->edit($this->request->data, $supplier_id);
                        } else {
                            $error_file = true;
                        }
                    }
                } elseif (isset($supplierImageId['SupplierImage']['file'])) {
                    $error_file = false;
                } else {
                    $error_file = true;
                }
                if (!$error_file) {
                    if (!$check_image || $check_image == ConstantsFileErrorTypes::OK) {
                        if ($this->Supplier->edit($this->request->data)) {
                            $error_ext_file = false;
                            unset($this->request->data['SupplierFile']['file'][0]);
                            if (isset($this->request->data['SupplierFile']['suppliers_categories'])) {
                                //Decode array with suppliers_files information.
                                $this->request->data['SupplierFile']['suppliers_categories'] = (array) json_decode($this->request->data['SupplierFile']['suppliers_categories']);
                            }

                            if (!empty($this->request->data['SupplierFile']['file'])) {

                                // The set of information comes in the opposite direction to what interests us
                                if (isset($this->request->data['SupplierFile']['suppliers_categories'])) {
                                    $this->request->data['SupplierFile']['suppliers_categories'] = array_values(array_reverse($this->request->data['SupplierFile']['suppliers_categories']));
                                }
                                $this->request->data['SupplierFile']['file'] = array_values($this->request->data['SupplierFile']['file']);

                                foreach ($this->request->data['SupplierFile']['file'] as $key => $file) {
                                    if (isset($file['error']) && $file['error'] == ConstantsBooleans::NO) {
                                        //Take category_id from array: SupplierCategory if exists
                                        if (isset($this->request->data['SupplierFile']['suppliers_categories'][$key])) {
                                            $supplier_info = (array) $this->request->data['SupplierFile']['suppliers_categories'][$key];
                                        } else {
                                            $supplier_info = array();
                                        }
                                        $check_file = FileManager::check_file($file);
                                        if ($check_file == ConstantsFileErrorTypes::OK) {
                                            if (!$this->Supplier->SupplierFile->saveFile($file, $supplier_id, $supplier_info, ConstantsFileType::FILE)) {
                                                $error_ext_file = true;
                                            }
                                        } else {
                                            break;
                                        }
                                    } elseif (isset($file['error']) && $file['error'] == ConstantsFlag::ERROR_DIMENSIONS) {
                                        $error_size = true;
                                    }
                                }
                            }
                            //If there's no new files but we change an existing one.
                            if (!empty($this->request->data['SupplierFileBefore'])) {
                                foreach ($this->request->data['SupplierFileBefore'] as $file_id => $data) {
                                    //Take category_id from array: SupplierCategory if exists
                                    $supplier_category_id = $data['supplier_category_id'];
                                    $name = $data['name'];
                                    $active = $data['active'];

                                    $file = $this->Supplier->SupplierFile->findById($file_id);
                                    if (!empty($file)) {
                                        $file_to_save['SupplierFile']['id'] = $file_id;
                                        $file_to_save['SupplierFile']['supplier_category_id'] = (intval($supplier_category_id) != 0) ? intval($supplier_category_id) : null;
                                        $file_to_save['SupplierFile']['name'] = $name;
                                        $file_to_save['SupplierFile']['active'] = intval($active);

                                        $this->Supplier->SupplierFile->save($file_to_save);
                                    }
                                }
                            }
                            $this->Supplier->SupplierNetwork->deleteAll(array('supplier_id' => $supplier_id));
                            $this->Supplier->SupplierTradingGroup->deleteAll(array('supplier_id' => $supplier_id));
                            $tradings = array();
                            if (isset($this->request->data['SupplierTradingGroup']['trading_group_id'])) {
                                $trading_groups_id = $this->request->data['SupplierTradingGroup']['trading_group_id'];
                                foreach ($trading_groups_id as $key => $trading_group_id) {
                                    if ($trading_group_id > 0) {
                                        $tradings[$key]['SupplierTradingGroup']['supplier_id'] = $supplier_id;
                                        $tradings[$key]['SupplierTradingGroup']['trading_group_id'] = $trading_group_id;
                                    }
                                }
                            }
                            $networks_id = $this->request->data['SupplierNetwork']['network_id'];
                            $nets = array();
                            foreach ($networks_id as $key => $network_id) {
                                if ($network_id > 0) {
                                    $nets[$key]['SupplierNetwork']['supplier_id'] = $supplier_id;
                                    $nets[$key]['SupplierNetwork']['network_id'] = $network_id;
                                }
                            }
                            $this->Supplier->SupplierNetwork->saveMany($nets);
                            $this->Supplier->SupplierTradingGroup->saveMany($tradings);
                            if (!$check_file || $check_file == ConstantsFileErrorTypes::OK) {
                                if ($error_size) {
                                    $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                                } elseif ($error_ext_file) {
                                    $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                                } else {
                                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                                    $this->redirect($this->request->here);
                                }
                            } elseif ($error_file == ConstantsFileErrorTypes::SIZE_ERROR) {
                                $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                            } else {
                                $this->Session->setFlashError(__t(ConstantsMessages::FILE_ERROR_EXTENSION));
                            }
                        } else {
                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                        }
                    } elseif ($error_file == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                } else {
                    $this->Session->setFlashError(__t('Validation.Mandatory_to_choose_an_image'));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'supplier' => $supplier,
                'supplier_files' => $supplier_files,
                'supplier_trading_groups' => $supplier_trading_groups,
                'supplier_networks' => $supplier_networks,
                'trading_groups' => $trading_groups,
                'networks' => $networks,
                'url_cancel' => $url,
                'suppliers_categories' => $suppliers_categories,
                'suppliers_categories_reverse' => $suppliers_categories_reverse,
                'user_aag_region_id' => $aagRegionId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete supplier.
     */
    public function delete($supplier_id)
    {
		try {
			$aagRegionId = CakeSession::read('Auth.User.aag_region_id');
			$supplier = $this->Supplier->getAllAboutSupplierId($supplier_id, $aagRegionId);
			if (
				$supplier &&
				$this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
				$this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
			) {
				if (
					empty($supplier['Brand']['id']) && empty($supplier['GarageSoftware']['id']) && empty($supplier['DistributorSoftware']['id']) &&
					empty($supplier['GarageEquipment']['id']) && empty($supplier['GarageNetwork']['id'])
				) {
					if (isset($supplier['SupplierImage']['id']) && !empty($supplier['SupplierImage']['id'])) {
						$this->Supplier->SupplierImage->delete($supplier['SupplierImage']['id']);
						if (!empty($supplier['SupplierImage']['file'])) {
							FileManager::delete_file(WWW_ROOT, FilePaths::SUPPLIERS_IMAGES_RELATIVE . $supplier['SupplierImage']['file']);
						}
					}
					$this->Supplier->SupplierFile->deleteSupplierFiles($supplier_id);
					$this->Supplier->SupplierNetwork->deleteAll(array('supplier_id' => $supplier_id));
					$this->Supplier->SupplierTradingGroup->deleteAll(array('supplier_id' => $supplier_id));
					if ($this->Supplier->delete($supplier_id)) {
						$this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
						$this->redirect(array('controller' => 'suppliers', 'action' => 'maintenance_suppliers'));
					}
				} else {
					$this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
					$this->redirect(array('controller' => 'suppliers', 'action' => 'edit', $supplier_id));
				}

				$this->layout = false;
				$this->render(false);
			} else {
				header('HTTP/1.0 401 Unauthorized');
				exit;
			}
		} catch (\Exception $e) {
			$this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
		}
    }

    /**
     * View supplier.
     */
    public function view($supplier_id)
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $supplier = $this->Supplier->getAllAboutSupplierId($supplier_id, $aagRegionId);
        if (
            $supplier &&
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
            )
        ) {
            $supplier['SupplierFile'] = $this->Supplier->SupplierFile->findAllBySupplierId($supplier['Supplier']['id']);

            $this->set(array(
                'supplier' => $supplier
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Suppliers categories maintenance page.
     */
    public function maintenance_suppliers_categories()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS_CATEGORIES)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];


            $conditions = array(
                'aag_region_id' => $aagRegionId
            );

            $suppliers_categories = $this->custom_pagination(
                $this->SupplierCategory->_query('search_maintenance_categories'),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'SupplierCategory',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(array(
                'suppliers_categories' => $suppliers_categories
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create supplier category.
     */
    public function add_category()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS_CATEGORIES)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $conditions = array('AagRegion.id' => $aagRegionId);
            $aagRegions = $this->AagRegion->region_list_conditions($conditions);

            if (!$this->request->is('get')) {
                $supplier_category = $this->SupplierCategory->add($this->request->data);
                if ($supplier_category) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'suppliers',
                            'action' => 'edit_category',
                            $this->SupplierCategory->getLastInsertID()
                        )
                    );
                }
            }

            $this->setVarCancel();

            $this->set(array(
                'aag_regions' => $aagRegions,
                'aag_region' => $aagRegionId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit supplier category.
     */
    public function edit_category($supplier_category_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $supplier_category = $this->SupplierCategory->findByIdAndAagRegionId($supplier_category_id, $aagRegionId);
        if (
            $supplier_category &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS_CATEGORIES)
        ) {
            $conditions = array('AagRegion.id' => $aagRegionId);
            $aagRegions = $this->AagRegion->region_list_conditions($conditions);

            if (!$this->request->is('get')) {
                $supplier_category_bd = $this->SupplierCategory->edit($this->request->data);

                if ($supplier_category_bd) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                }
            } else {
                $this->request->data = $supplier_category;
            }

            $this->setVarCancel();

            $this->set(array(
                'supplier_category' => $supplier_category,
                'aag_regions' => $aagRegions,
                'aag_region' => $aagRegionId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete category.
     */
    public function ajax_delete_category($category_id)
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS_CATEGORIES)
        ) {
            $this->autoRender = false;
            if ($this->SupplierCategory->findById($category_id)) {
                if ($this->SupplierFile->findBySupplierCategoryId($category_id)) {
                    return 2;    // Item in use
                } else {
                    return $this->SupplierCategory->delete($category_id);
                }
            } else {
                return false;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get category name by Category ID.
     */
    public function ajax_get_category_name_by_id()
    {
        $this->verify_ajax($this->request);
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $category_id = $this->request->data['category_id'];
        $supplier_category = $this->SupplierCategory->findByIdAndAagRegionId($category_id, $aagRegionId);
        if (
            $supplier_category &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_SUPPLIERS) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
                )
            )
        ) {

            echo (!empty($supplier_category)) ? $supplier_category['SupplierCategory']['name_' . __l()] : '';
            $this->layout = $this->autoRender = false;
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
     * AJAX preview suppliers.
     */
    public function ajax_preview_suppliers()
    {
        $this->verify_ajax($this->request);
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_SUPPLIERS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
            )
        ) {
            if ($this->request->data['network_id'] != null) {
                $suppliers = $this->Supplier->getAllAboutSuppliersByNetwork($this->request->data['network_id']);
            } elseif ($this->request->data['trading_group_id'] != null) {
                $suppliers = $this->Supplier->getAllAboutSuppliersByTradingGroup($this->request->data['trading_group_id']);
            } else {
                $suppliers = $this->Supplier->getAllAboutSuppliers();
            }

            foreach ($suppliers as $key => $supplier) {
                $suppliers[$key]['SupplierFile'] = $this->Supplier->SupplierFile->findAllBySupplierId($supplier['Supplier']['id']);
                $suppliers[$key]['Brands'] = $this->Supplier->Brand->getAllBrandsBySupplierId($supplier['Supplier']['id']);
            }

            $this->set(array(
                'suppliers' => $suppliers,
            ));

            $this->layout = false;
            $this->render('../Suppliers/Elements/suppliers');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarCancel()
    {
        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'suppliers',
                'action' => 'maintenance_suppliers',
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
        ));
    }

    private function setVarForm()
    {
        $user = $this->Acceso->user();
        $userAagRegionId = $user['aag_region_id'];
        $userRoleId = $user['role_id'];
        $aagRegions = $this->AagRegion->region_list();

        $conditionsUser = array('AagRegion.id' => $userAagRegionId);
        $aagRegionsUser = $this->AagRegion->region_list_conditions($conditionsUser);

        $this->set(array(
            'aag_regions' => $aagRegions,
            'user_aag_region_id' => $userAagRegionId,
            'user_role_id' => $userRoleId,
            'aag_regions_user' => $aagRegionsUser
        ));
    }

    public function get_suppliers_name()
    {
        $this->verify_ajax($this->request);
        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $suppliers_name = $this->Supplier->getSuppliersAjax($this->request->query, $aagRegionId);

        $list_suppliers = array();
        foreach ($suppliers_name as $key => $supplier) {
            $list_suppliers[] = array(
                'id' => $key,
                'text' => $supplier,
            );
        }

        $list_suppliers_complete['items'] = $list_suppliers;

        return json_encode($list_suppliers_complete);
    }
}
