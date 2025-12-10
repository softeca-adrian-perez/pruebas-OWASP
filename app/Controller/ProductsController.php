<?php
class ProductsController extends AppController
{
    public $uses = array(
        'Brand',
        'Supplier',
        'Product'
    );

    /**
     * Products maintenance page.
     */
    public function maintenance_products()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::PRODUCTS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $active = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $brands = $this->Product->Brand->search_list_conditions($aagRegionId);

            $search = $this->request->query;
            $this->request->data['Search'] = $search;

            $products = $this->custom_pagination(
                $this->Product->_query($aagRegionId),
                $this->Product->conditions($search),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Product',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(array(
                'products' => $products,
                'brands' => $brands,
                'active' => $active,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create product.
     */
    public function add()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::PRODUCTS)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $brands = $this->Product->Brand->getAllAboutBrandsConditions($aagRegionId);

            $url = array(
                'controller' => 'products',
                'action' => 'maintenance_products',
            );

            if (!$this->request->is('get')) {
                $checked_brand = false;
                $brandsId = $this->request->data['Product']['brands_id'];
                foreach ($brandsId as $brand_id) {
                    if ($brand_id > 0) {
                        $this->request->data['Product']['brand_id'] = $brand_id;
                        $checked_brand = true;
                    }
                }
                if ($checked_brand) {
                    if (!empty($this->request->data['ProductImage']['new_image'])) {
                        $checkImage = FileManager::check_image($this->request->data['ProductImage']['file'], $this->request->data['ProductImage']['new_image']);
                        if ($checkImage == ConstantsFileErrorTypes::OK) {
                            $fileName = FileManager::upload_image_webroot(
                                $this->request->data['ProductImage']['new_image'],
                                $this->request->data['ProductImage']['file'],
                                ConstantsFileType::IMAGE,
                                FilePaths::PRODUCTS_IMAGES_RELATIVE
                            );
                            if ($fileName) {
                                $this->request->data['ProductImage']['file']['name'] = $fileName;
                                if ($this->Product->add($this->request->data)) {
                                    if ($this->Product->ProductImage->add($this->request->data, $this->Product->getLastInsertID())) {
                                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                                        $this->redirect(
                                            array(
                                                'controller' => 'products',
                                                'action' => 'edit',
                                                $this->Product->getLastInsertID()
                                            )
                                        );
                                    } else {
                                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                                    }
                                }
                            } else {
                                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
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
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->set(array(
                'url_cancel' => $url,
                'brands' => $brands,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit product.
     */
    public function edit($product_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $product = $this->Product->getAllAboutProductId($product_id);
        $brand = isset($product['Product']['brand_id']) ? $this->Brand->findById($product['Product']['brand_id'], 'supplier_id') : null;
        $supplier = isset($brand) ? $this->Supplier->findByIdAndAagRegionId($brand['Brand']['supplier_id'], $aagRegionId, 'id') : null;

        if (
            $product &&
            $supplier &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::PRODUCTS)
        ) {
            $brands = $this->Product->Brand->getAllAboutBrandsConditions($aagRegionId);
            $url = array(
                'controller' => 'products',
                'action' => 'maintenance_products',
            );

            if ($this->request->is('get')) {
                $this->request->data = $product;
            } else {
                $errorFile = false;
                $checkImage = false;
                if ($this->request->data['ProductImage']['new_image'] == 'false') {
                    $errorFile = true;
                } elseif (!empty($this->request->data['ProductImage']['new_image'])) {
                    $checkImage = FileManager::check_image($this->request->data['ProductImage']['file'], $this->request->data['ProductImage']['new_image']);
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $image = $product['ProductImage']['source_name'];
                        $fileName = FileManager::upload_image_webroot(
                            $this->request->data['ProductImage']['new_image'],
                            $this->request->data['ProductImage']['file'],
                            ConstantsFileType::IMAGE,
                            FilePaths::PRODUCTS_IMAGES_RELATIVE
                        );
                        if ($fileName) {
                            if (!empty($image)) {
                                FileManager::delete_file(WWW_ROOT, FilePaths::PRODUCTS_IMAGES_RELATIVE . $image);
                                $this->request->data['ProductImage']['file']['name'] = $fileName;
                                $this->Product->ProductImage->edit($this->request->data, $product_id);
                            } else {
                                $this->request->data['ProductImage']['file']['name'] = $fileName;
                                $this->Product->ProductImage->edit($this->request->data, $product_id);
                            }
                        } else {
                            $errorFile = true;
                        }
                    }
                }
                $brandsId = $this->request->data['Product']['brands_id'];
                foreach ($brandsId as $brand_id) {
                    if ($brand_id > 0) {
                        $this->request->data['Product']['brand_id'] = $brand_id;
                    }
                }
                if (!$errorFile) {
                    if (!$checkImage || $checkImage == ConstantsFileErrorTypes::OK) {
                        if ($this->Product->edit($this->request->data)) {
                            $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                            $this->redirect($this->request->here);
                        } else {
                            $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                        }
                    } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                        $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                    }
                } else {
                    $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                }
            }

            $this->set(array(
                'brands' => $brands,
                'product' => $product,
                'product_id' => $product_id,
                'url_cancel' => $url,
                'brands' => $brands
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete product.
     */
    public function delete($product_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $product = $this->Product->getAllAboutProductId($product_id);
        $brand = isset($product['Product']['brand_id']) ? $this->Brand->findById($product['Product']['brand_id'], 'supplier_id') : null;
        $supplier = isset($brand) ? $this->Supplier->findByIdAndAagRegionId($brand['Brand']['supplier_id'], $aagRegionId, 'id') : null;
        if (
            $product &&
            $supplier &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::PRODUCTS)
        ) {

            $this->Product->ProductImage->delete($product['ProductImage']['id']);
            FileManager::delete_file(WWW_ROOT, FilePaths::PRODUCTS_IMAGES_RELATIVE . $product['ProductImage']['file']);

            if ($this->Product->delete($product_id)) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
                $this->redirect(array('controller' => 'products', 'action' => 'maintenance_products'));
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
            }

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }
}
