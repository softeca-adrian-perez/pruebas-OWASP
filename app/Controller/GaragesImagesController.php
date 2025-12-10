<?php
class GaragesImagesController extends AppController
{
    public $uses = array(
        'GarageImage',
        'GarageNetwork',
        'Garage',
        'LogChange',
        'RequestedChange'
    );

    /**
     * Download GarageImage.
     */
    public function download_file($id)
    {
        $user = $this->Acceso->user();
        $user_role_id = $user['role_id'];

        $garageImage = $this->GarageImage->findById($id);
        if (
            $garageImage &&
            (
                $user_role_id == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    !in_array($user_role_id, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF))
                )
            )
        ) {
            $path = substr(ConstantsPath::DIR_GARAGE_IMAGES, 3) . DS;
            if(Configure::read('AZURE_FILES')) {
                $filePath = FileManager::get_url($path . $garageImage['GarageImage']['file'], false);
                $headers = get_headers($filePath, 1);
            } else {
                $filePath = ConstantsFilePaths::GARAGES_IMAGES_ABSOLUTE . $garageImage['GarageImage']['file'];
            }
            if ((!Configure::read('AZURE_FILES') && file_exists($filePath)) || (Configure::read('AZURE_FILES') && $headers && (strpos($headers[0], '200') !== false))) {
                $this->download_file_name($garageImage['GarageImage']['source_name'], $garageImage['GarageImage']['file'], $path);
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
     * AJAX convert GarageImage to not principal.
     */
    public function ajax_not_principal_image()
    {
        $this->verify_ajax($this->request);

        $id = $this->request->data['id'];
        $garageImage = $this->GarageImage->findById($id);

        if ($garageImage) {
            $garageId = $garageImage['GarageImage']['garage_id'];

            $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
            $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);
            if (empty($garageNetworks)) {
                $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garageId, 'network_id' => '-1',));
            }

            if (
                $garage &&
                (
                    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                    (
                        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                        CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                        $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                        (
                            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                            (
                                CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                                $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                            )
                        ) &&
                        $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                    )
                )
            ) {
                if (!$this->request->is('get')) {
                    if ($this->GarageImage->convertToPrincipalGarageImage($garageImage['GarageImage']['id'], ConstantsBooleans::NO)) {
                        $newData = array('principal' => 'Principal');
                        $oldData = array('principal' => 'Not Principal');
                        $this->LogChange->get_params_create_log_edit(
                            $newData,
                            $oldData,
                            $this->GarageImage->table,
                            $this->Session->read('Auth'),
                            $garageId,
                            ConstantsLogType::GARAGE
                        );
                    } else {
                        $this->Session->setFlashError(__t('Garage.Cant_principal'));
                    }
                }

                $images = $this->GarageImage->findAllByGarageId($garageId);
                $files = Hash::extract($images, '{n}.GarageImage');
                $files['GarageImage'] = $files;
                $this->set(
                    array(
                        'current_network' => CakeSession::read('Auth.User.CurrentNetwork'),
                        'garage' => $files,
                        'images' => $images,
                        'principal' => $id,
                    )
                );

                $this->layout = null;
                $this->render('/GaragesImages/Elements/gallery');
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX convert GarageImage to principal.
     */
    public function ajax_principal_image()
    {
        $this->verify_ajax($this->request);

        $id = $this->request->data['id'];
        $garageImage = $this->GarageImage->findById($id);

        if ($garageImage) {
            $garageId = $garageImage['GarageImage']['garage_id'];
            $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
            $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);
            if (empty($garageNetworks)) {
                $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garageId, 'network_id' => '-1',));
            }

            if (
                $garage &&
                (
                    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                    (
                        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                        CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                        $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                        (
                            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                            (
                                CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                                $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                            )
                        ) &&
                        $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                    )
                )
            ) {
                if (!$this->request->is('get')) {
                    $garageImageOld = $this->GarageImage->findByPrincipalAndGarageId(ConstantsBooleans::YES, $garageImage['GarageImage']['garage_id']);
                    if (!empty($garageImageOld)) {
                        if ($this->GarageImage->convertToPrincipalGarageImage($garageImageOld['GarageImage']['id'], ConstantsBooleans::NO)) {
                            $garageImageNew = $this->GarageImage->convertToPrincipalGarageImage($id, ConstantsBooleans::YES);
                            if (!$garageImageNew) {
                                $this->Session->setFlashError(__t('Garage.Cant_principal'));
                            }
                        } else {
                            $this->Session->setFlashError(__t('Garage.Cant_principal'));
                        }
                    } else {
                        if ($this->GarageImage->convertToPrincipalGarageImage($id, ConstantsBooleans::YES)) {
                            $newData = array('principal' => 'Not Principal');
                            $oldData = array('principal' => 'Principal');
                            $this->LogChange->get_params_create_log_edit(
                                $newData,
                                $oldData,
                                $this->GarageImage->table,
                                $this->Session->read('Auth'),
                                $garageId,
                                ConstantsLogType::GARAGE
                            );
                        } else {
                            $this->Session->setFlashError(__t('Garage.Cant_principal'));
                        }
                    }
                }

                $images = $this->GarageImage->findAllByGarageId($garageId);
                $files = Hash::extract($images, '{n}.GarageImage');
                $files['GarageImage'] = $files;
                $this->set(
                    array(
                        'current_network' => CakeSession::read('Auth.User.CurrentNetwork'),
                        'garage' => $files,
                        'images' => $images,
                        'principal' => $id,

                    )
                );

                $this->layout = null;
                $this->render('/GaragesImages/Elements/gallery');
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete GarageImage.
     */
    public function ajax_delete_file()
    {
        $this->verify_ajax($this->request);

        $id = $this->request->data['id'];
        $garageImage = $this->GarageImage->findById($id);

        if ($garageImage) {
            $garageId = $garageImage['GarageImage']['garage_id'];
            $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
            $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);
            if (empty($garageNetworks)) {
                $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garageId, 'network_id' => '-1',));
            }

            if (
                $garage &&
                (
                    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                    (
                        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                        CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                        $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                        (
                            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                            (
                                CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                                $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                            )
                        ) &&
                        $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                    )
                )
            ) {
                if (!$this->request->is('get')) {
                    $delete = $this->GarageImage->deleteGarageImage($id);
                    if ($delete) {
                        $tmpLog = array(
                            'GarageImage' => array(
                                'file' => $garageImage['GarageImage']['file']
                            )
                        );
                        $this->LogChange->get_params_create_log_delete(
                            $tmpLog['GarageImage'],
                            $this->GarageImage->table,
                            $this->Session->read('Auth'),
                            $garageId,
                            ConstantsLogType::GARAGE
                        );
                    } else {
                        $this->Session->setFlashError(__t('Garage.Cant_delete_image'));
                    }
                }

                $files = $this->GarageImage->findAllByGarageId($garageId);
                $files = Hash::extract($files, '{n}.GarageImage');
                $files['GarageImage'] = $files;
                $this->set(
                    array(
                        'garage' => $files,
                    )
                );

                $this->layout = null;
                $this->render('/Garages/Elements/form_attached_facade_images');
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }
}
