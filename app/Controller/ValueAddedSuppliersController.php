<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class ValueAddedSuppliersController extends AppController
{
    public $uses = array(
        'ValueAddedSupplier'
    );

    public function home()
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        if (
            $this->Acceso->haveModulePermission(ConstantsConfigModules::VALUE_ADDED_SUPPLIER) || $roleId == ConstantsRoles::SUPER_ADMIN
        ) {
            $this->set(array(
                'valueAddedSuppliers' => $this->ValueAddedSupplier->findAllByAagRegionId($aagRegionId)
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    public function management_home()
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];


        if (
            $roleId == ConstantsRoles::ADMIN &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::VALUE_ADDED_SUPPLIER)
        ) {

            $search = $this->request->query;
            $this->request->data['Search'] = $search;

            $valueAddedSuppliers = $this->custom_pagination(
                $this->ValueAddedSupplier->_query('home'),
                $this->ValueAddedSupplier->conditions($search, $aagRegionId),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'ValueAddedSupplier',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(array(
                'valueAddedSuppliers' => $valueAddedSuppliers
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    public function add()
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        if ($roleId == ConstantsRoles::ADMIN &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::VALUE_ADDED_SUPPLIER)) {

            if (!$this->request->is('get')) {
                $valueAddedSupplier = $this->request->data;
                $valueAddedSupplier['ValueAddedSupplier']['aag_region_id'] = $aagRegionId;
                $check_image = false;
                $error_file = false;
                if (!empty($valueAddedSupplier['ValueAddedSupplier']['new_image'])) {
                    $check_image = FileManager::check_image($valueAddedSupplier['ValueAddedSupplier']['logo_image'], $valueAddedSupplier['ValueAddedSupplier']['new_image']);
                    $error_file = true;
                    if ($check_image == ConstantsFileErrorTypes::OK) {
                        $error_file = false;
                        $file_name = FileManager::upload_image_webroot($valueAddedSupplier['ValueAddedSupplier']['new_image'], $valueAddedSupplier['ValueAddedSupplier']['logo_image'], ConstantsFileType::IMAGE, FilePaths::VALUE_ADDED_SUPPLIERS_LOGO_IMAGES_RELATIVE);
                        $valueAddedSupplier['ValueAddedSupplier']['logo_image'] = $file_name;
                        if (!$file_name) {
                            $error_file = true;
                        }
                    }
                } else {
                    $valueAddedSupplier['ValueAddedSupplier']['logo_image'] = null;
                }

                if (!$error_file) {
                    $valueAddedSupplier = $this->ValueAddedSupplier->addValueAddedSupplier($valueAddedSupplier);
                }

                if ($valueAddedSupplier) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(array('controller' => 'value_added_suppliers', 'action' => 'management_home'));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();

        } else {
            throw new UnauthorizedException();
        }
    }

    public function edit($guid)
    {
        $user = $this->Acceso->User();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if ($roleId == ConstantsRoles::ADMIN &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::VALUE_ADDED_SUPPLIER)) {

            $valueAddedSupplier = $this->ValueAddedSupplier->findByGuidAndAagRegionId($guid, $aagRegionId);
            if (!isset($valueAddedSupplier) || empty($valueAddedSupplier)) {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }

            if ($this->request->is('get')) {
                $this->request->data = $valueAddedSupplier;
            } else {
                $this->request->data['ValueAddedSupplier']['id'] = $valueAddedSupplier['ValueAddedSupplier']['id'];

                $check_image = false;
                $error_file = false;
                if (!empty($this->request->data['ValueAddedSupplier']['new_image'])) {
                    $check_image = FileManager::check_image($this->request->data['ValueAddedSupplier']['logo_image'], $this->request->data['ValueAddedSupplier']['new_image']);
                    $error_file = true;
                    if ($check_image == ConstantsFileErrorTypes::OK) {
                        $error_file = false;
                        $file_name = FileManager::upload_image_webroot($this->request->data['ValueAddedSupplier']['new_image'], $this->request->data['ValueAddedSupplier']['logo_image'], ConstantsFileType::IMAGE, FilePaths::VALUE_ADDED_SUPPLIERS_LOGO_IMAGES_RELATIVE);
                        $this->request->data['ValueAddedSupplier']['logo_image'] = $file_name;
                        if (!$file_name) {
                            $error_file = true;
                        } elseif (!empty($valueAddedSupplier['ValueAddedSupplier']['logo_image'])) {
                            $error_file = !FileManager::delete_file(WWW_ROOT, FilePaths::VALUE_ADDED_SUPPLIERS_LOGO_IMAGES_RELATIVE . $valueAddedSupplier['ValueAddedSupplier']['logo_image']);
                        }
                    }
                } else {
                    $this->request->data['ValueAddedSupplier']['logo_image'] = $valueAddedSupplier['ValueAddedSupplier']['logo_image'];
                }

                if (!$error_file) {
                    $resp = $this->ValueAddedSupplier->editValueAddedSupplier($this->request->data);
                }
                if ($resp) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();

            $this->set(array(
                'valueAddedSupplier' => $valueAddedSupplier,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm()
    {
        $cancel_action = array(
            'url_cancel' => array(
                'controller' => 'value_added_suppliers',
                'action' => 'management_home',
            ),
        );

        $this->set(array(
            'cancel_action' => $cancel_action,
        ));
    }


    public function delete($guid)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::ADMIN &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::VALUE_ADDED_SUPPLIER)
        ) {
            $valueAddedSupplier = $this->ValueAddedSupplier->findByGuidAndAagRegionId($guid, $aagRegionId);

            $deleteValueAddedSupplier = false;
            $error_file = false;
            if ($valueAddedSupplier && !empty($valueAddedSupplier['ValueAddedSupplier']['logo_image'])) {
                $error_file = !FileManager::delete_file(WWW_ROOT, FilePaths::VALUE_ADDED_SUPPLIERS_LOGO_IMAGES_RELATIVE . $valueAddedSupplier['ValueAddedSupplier']['logo_image']);
            }
            if (!$error_file && $valueAddedSupplier) {
                $deleteValueAddedSupplier = $this->ValueAddedSupplier->deleteValueAddedSupplier($guid);
            }

            if ($deleteValueAddedSupplier) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
            } else {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::BAD_SAVED));
            }
            $this->redirect(
                array(
                    'controller' => 'value_added_suppliers',
                    'action' => 'management_home',
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }
}
