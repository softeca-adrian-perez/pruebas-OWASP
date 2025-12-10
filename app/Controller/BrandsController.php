<?php
class BrandsController extends AppController
{
    /**
     * Brands maintenance page.
     */
    public function maintenance_brands()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::BRANDS)
            )
        ) {
            $active = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $suppliers = $this->Brand->Supplier->search_list_conditions($aagRegionId);

            $search = $this->request->query;
            if (!isset($search['aag_region_id'])) {
                $search['aag_region_id'] = $aagRegionId;
            }
            $this->request->data['Search'] = $search;

            $brands = $this->custom_pagination(
                $this->Brand->_query($aagRegionId),
                $this->Brand->conditions($search),
                ConstantsPagination::SIZE_PAGE_SMALL
            );

            foreach ($brands as $key => $brand) {
                $brandProducts = $this->Brand->Product->findAllByBrandId($brand['Brand']['id']);
                $brands[$key]['Products'] = '';
                foreach ($brandProducts as $product) {
                    $brands[$key]['Products'] .= $product['Product']['name'] . ", ";
                }
                $brands[$key]['Products'] = trim($brands[$key]['Products'], ', ');
            }

            $conditions_user = array();
            if ($roleId != ConstantsRoles::SUPER_ADMIN) {
                $conditions_user = array('AagRegion.id' => $aagRegionId);
            }

            $aagRegionsUser = $this->Brand->Supplier->AagRegion->region_list_conditions($conditions_user);

            $this->set(array(
                'brands' => $brands,
                'suppliers' => $suppliers,
                'active' => $active,
                'aag_regions_user' => $aagRegionsUser,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create brand.
     */
    public function add()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::BRANDS)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $roleId = $user['role_id'];

            $suppliers = $this->Brand->Supplier->getAllAboutSuppliersConditions($aagRegionId);
            $brands = $this->Brand->getAllAboutBrands();

            $url = array(
                'controller' => 'brands',
                'action' => 'maintenance_brands',
            );

            if (!$this->request->is('get')) {
                $checkedSupplier = false;
                $suppliersId = $this->request->data['Brand']['suppliers_id'];
                foreach ($suppliersId as $supplierId) {
                    if ($supplierId > 0) {
                        $this->request->data['Brand']['supplier_id'] = $supplierId;
                        $checkedSupplier = true;
                    }
                }
                if ($checkedSupplier) {
                    if (!empty($this->request->data['BrandImage']['new_image'])) {
                        $checkImage = FileManager::check_image($this->request->data['BrandImage']['file'], $this->request->data['BrandImage']['new_image']);
                        if ($checkImage == ConstantsFileErrorTypes::OK) {
                            $filename = FileManager::upload_image_webroot(
                                $this->request->data['BrandImage']['new_image'],
                                $this->request->data['BrandImage']['file'],
                                ConstantsFileType::IMAGE,
                                FilePaths::BRANDS_IMAGES_RELATIVE
                            );

                            if ($filename) {
                                $this->request->data['BrandImage']['file']['name'] = $filename;
                                $brand = $this->Brand->add($this->request->data);
                                if ($brand) {
                                    if ($this->Brand->BrandImage->add($this->request->data, $this->Brand->getLastInsertID())) {
                                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                                        $this->redirect(
                                            array(
                                                'controller' => 'brands',
                                                'action' => 'edit',
                                                $this->Brand->getLastInsertID()
                                            )
                                        );
                                    } else {
                                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                    }
                                }
                            } else {
                                $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                            }
                        } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                            $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateFile::SIZE_FILES_UPLOAD_TEXT);
                        } else {
                            $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                        }
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'url_cancel' => $url,
                'suppliers' => $suppliers,
                'brands' => $brands,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit brand.
     */
    public function edit($brand_id)
    {
        $brand = $this->Brand->getAllAboutBrandId($brand_id);

        if (
            $brand &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::BRANDS)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];
            $roleId = $user['role_id'];

            $suppliers = $this->Brand->Supplier->getAllAboutSuppliersConditions($aagRegionId);

            $url = array(
                'controller' => 'brands',
                'action' => 'maintenance_brands',
            );

            if ($this->request->is('get')) {
                $this->request->data = $brand;
            } else {
                $flagFileType = false;
                $checkImage = false;
                if ($this->request->data['BrandImage']['new_image'] == 'false') {
                    $flagFileType = true;
                } elseif (!empty($this->request->data['BrandImage']['new_image'])) {
                    $checkImage = FileManager::check_image($this->request->data['BrandImage']['file'], $this->request->data['BrandImage']['new_image']);
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $image = $brand['BrandImage']['source_name'];
                        $filename = FileManager::upload_image_webroot(
                            $this->request->data['BrandImage']['new_image'],
                            $this->request->data['BrandImage']['file'],
                            ConstantsFileType::IMAGE,
                            FilePaths::BRANDS_IMAGES_RELATIVE
                        );
                        if ($filename) {
                            if (!empty($image)) {
                                FileManager::delete_file(WWW_ROOT, FilePaths::BRANDS_IMAGES_RELATIVE . $image);
                                $this->request->data['BrandImage']['file']['name'] = $filename;
                                $this->Brand->BrandImage->edit($this->request->data, $brand_id);
                            } else {
                                $this->request->data['BrandImage']['file']['name'] = $filename;
                                $this->Brand->BrandImage->edit($this->request->data, $brand_id);
                            }
                        } else {
                            $flagFileType = true;
                        }
                    } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                }
                $suppliersId = $this->request->data['Brand']['suppliers_id'];
                foreach ($suppliersId as $supplierId) {
                    if ($supplierId > 0) {
                        $this->request->data['Brand']['supplier_id'] = $supplierId;
                    }
                }
                if (!$checkImage || $checkImage == ConstantsFileErrorTypes::OK) {
                    if (!$flagFileType) {
                        if ($this->Brand->edit($this->request->data)) {
                            $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                            $this->redirect($this->request->here);
                        } else {
                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                        }
                    } else {
                        $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                    }
                } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                    $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'brand' => $brand,
                'suppliers' => $suppliers,
                'url_cancel' => $url,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete brand.
     */
    public function delete($brand_id)
    {
        $brand = $this->Brand->getAllAboutBrandId($brand_id);

        if (
            $brand &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::BRANDS)
        ) {
            if (empty($brand['Product']['id'])) {
                $this->Brand->BrandImage->eliminar($brand['BrandImage']['id']);
                FileManager::delete_file(WWW_ROOT, FilePaths::BRANDS_IMAGES_RELATIVE . $brand['BrandImage']['file']);
                if ($this->Brand->delete($brand_id)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
                    $this->redirect(array('controller' => 'brands', 'action' => 'maintenance_brands'));
                }
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED_BRAND_PRODUCT) . ' ' . $brand['Product']['name']);
                $this->redirect(array('controller' => 'brands', 'action' => 'edit', $brand_id));
            }

            $this->layout = false;
            $this->render(false);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Get brand products.
     */
    public function products()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_SUPPLIERS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
            )
        ) {
            $brandId = $this->request->data['brand_id'];
            $products = $this->Brand->Product->getAllProductsByBrandId($brandId);

            echo json_encode($products);

            $this->autoRender = null;
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm()
    {
        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'brands',
                'action' => 'maintenance_brands',
            ),
        );
        $this->set(array(
            'cancel_action' => $cancelAction,
        ));
    }
}
