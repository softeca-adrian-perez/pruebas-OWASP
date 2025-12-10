<?php
class SuppliersFilesController extends AppController
{
    public $uses = array(
        'Supplier',
        'SupplierFile',
        'SupplierCategory',
    );

    /**
     * Download Supplier file.
     */
    public function download_file($id)
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $file = $this->SupplierFile->findById($id);
        $supplier = isset($file['SupplierFile']['supplier_id']) ? $this->Supplier->findByIdAndAagRegionId($file['SupplierFile']['supplier_id'], $aagRegionId, 'id') : null;
        if (
            $supplier &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_SUPPLIERS) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
                )
            )
        ) {
            $path = substr(ConstantsPath::DIR_SUPPLIERS_FILES, 3) . DS;
            if(Configure::read('AZURE_FILES')) {
                $filePath = FileManager::get_url($path . $file['SupplierFile']['file'], false);
                $headers = get_headers($filePath, 1);
            } else {
                $filePath = ConstantsPath::DIR_SUPPLIERS_FILES_ABSOLUTE . $file['SupplierFile']['file'];
            }
            if ((!Configure::read('AZURE_FILES') && file_exists($filePath)) || (Configure::read('AZURE_FILES') && $headers && (strpos($headers[0], '200') !== false))) {
                $this->download_file_name($file['SupplierFile']['source_name'], $file['SupplierFile']['file'], $path);
            } else {
                $msg = h(sprintf(__t('General.File_does_not_exists')));
                    $this->Session->setFlashError($msg);
                    $this->redirect(
                        array(
                            'controller' => 'home',
                            'action' => 'home_page2',
                        )
                    );
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete SupplierFile.
     */
    public function ajax_delete_file()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
        ) {
            $id = $this->request->data['id'];
            $supplierFile = $this->SupplierFile->findById($id);

            if (!$this->request->is('get')) {
                $bd = $this->SupplierFile->deleteSupplierFile($id);
                if (!$bd) {
                    $this->Session->setFlashError('Can\'t delete the file');
                }
            }

            $suppliersCategories = $this->SupplierCategory->search_list();
            $suppliersCategoriesReverse = $this->SupplierCategory->search_list_reverse();
            $supplierFiles = array();
            foreach ($suppliersCategories as $categoryId => $name) {
                $files = $this->Supplier->SupplierFile->getFilesBySupplierIdAndCategoryId($supplierFile['SupplierFile']['supplier_id'], $categoryId);
                if (!empty($files)) {
                    $supplierFiles[$name] = $files;
                }
            }

            $this->set(
                array(
                    'supplier_files' => $supplierFiles,
                    'suppliers_categories' => $suppliersCategories,
                    'suppliers_categories_reverse' => $suppliersCategoriesReverse,
                )
            );

            $this->layout = null;
            $this->render('/Suppliers/Elements/form_attached_files');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get category files.
     */
    public function ajax_get_category_file()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
        ) {
            $fileId = $this->request->data['file_id'];

            $supplierFile = $this->SupplierFile->findById($fileId);

            $this->layout = $this->autoRender = false;

            return (!empty($supplierFile)) ? $supplierFile['SupplierFile']['supplier_category_id'] : '';
        } else {
            throw new UnauthorizedException();
        }
    }
}
