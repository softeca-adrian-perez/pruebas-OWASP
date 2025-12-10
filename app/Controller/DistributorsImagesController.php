<?php
class DistributorsImagesController extends AppController
{
    public $uses = array(
        'DistributorImage',
        'Distributor',
        'LogChange',
        'RequestedChange'
    );

    /**
     * Download DistributorImage.
     */
    public function download_file($id)
    {
        $distributorImage = $this->DistributorImage->findById($id);

        if ($distributorImage) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $distributor = $this->Distributor->findByIdAndAagRegionId($distributorImage['DistributorImage']['distributor_id'], $aagRegionId);

            if (
                $distributor &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
                $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
            ) {
                $path = substr(ConstantsPath::DIR_DISTRIBUTOR_IMAGES, 3) . DS;
                if(Configure::read('AZURE_FILES')) {
                    $filePath = FileManager::get_url($path . $distributorImage['DistributorImage']['file'], false);
                    $headers = get_headers($filePath, 1);
                } else {
                    $filePath = ConstantsFilePaths::DISTRIBUTORS_IMAGES_ABSOLUTE . $distributorImage['DistributorImage']['file'];
                }
                if ((!Configure::read('AZURE_FILES') && file_exists($filePath)) || (Configure::read('AZURE_FILES') && $headers && (strpos($headers[0], '200') !== false))) {
                    $this->download_file_name($distributorImage['DistributorImage']['source_name'], $distributorImage['DistributorImage']['file'], $path);
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
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create DistributorImage.
     */
    public function add_image_distributor($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $images = $this->DistributorImage->findAllByDistributorId($distributor['Distributor']['id']);
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'view',
                    $distributor_id
                ),
            );

            if (!$this->request->is('get')) {
                $checkImage = false;
                $uploadFiles = $this->request->data;
                $save = false;

                $cont = 1;
                foreach ($uploadFiles['file-content'] as $image) {
                    $checkImage = FileManager::check_image($uploadFiles['DistributorImage']['files'][$cont], $image);
                    if ($checkImage == ConstantsFileErrorTypes::OK) {
                        $save = $this->DistributorImage->uploadDistributorImages(
                            $image,
                            $uploadFiles['DistributorImage']['files'][$cont],
                            $distributor_id,
                            ConstantsFileType::IMAGE
                        );
                        if (!$save) {
                            break;
                        }
                    } else {
                        $user = $this->Session->read('Auth');
                        $change = $this->RequestedChange->create_request_edit_description(
                            $this->DistributorImage->table,
                            'Distributor.Images',
                            $user['User']['id'],
                            $distributor_id,
                            $this->request->data['change_description'],
                            ConstantsLogType::DISTRIBUTOR
                        );
                        if ($change) {
                            $checkImage = FileManager::check_image($uploadFiles['DistributorImage']['files'], $uploadFiles['DistributorImage']['new_image']);
                            if ($checkImage == ConstantsFileErrorTypes::OK) {
                                $save = $this->RequestedChange->RequestedChangeImage->uploadDistributorImages(
                                    $uploadFiles['DistributorImage']['new_image'],
                                    $uploadFiles['DistributorImage']['files'],
                                    $change['RequestedChange']['id'],
                                    ConstantsFileType::IMAGE
                                );
                            }
                        }
                    }
                    $cont++;
                }
                if (!$checkImage || $checkImage == ConstantsFileErrorTypes::OK) {
                    if ($save) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    } elseif (!$save) {
                        $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                    } elseif ($save == ConstantsFlag::ERROR_DIMENSIONS) {
                        $this->Session->setFlashError(__t(ConstantsMessages::MAX_FILE_SIZE));
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } elseif ($checkImage == ConstantsFileErrorTypes::SIZE_ERROR) {
                    $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
                }

                $this->redirect($this->here);
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'distributor' => $distributor,
                'distributor_id' => $distributor_id,
                'images' => $images,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX set DistributorImage as NO principal.
     */
    public function ajax_not_principal_image()
    {
        $this->verify_ajax($this->request);

        $id = $this->request->data['id'];
        $distributorImage = $this->DistributorImage->findById($id);

        if ($distributorImage) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $distributor = $this->Distributor->findByIdAndAagRegionId($distributorImage['DistributorImage']['distributor_id'], $aagRegionId);

            if (
                $distributor &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
                $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
            ) {
                if (!$this->request->is('get')) {
                    $edit = $this->DistributorImage->convertToPrincipalDistributorImage($distributorImage['DistributorImage']['id'], ConstantsBooleans::NO);

                    if ($edit) {
                        $newData = array(
                            'principal' => 'Principal'
                        );
                        $oldData = array(
                            'principal' => 'Not Principal'
                        );
                        $this->LogChange->get_params_create_log_edit(
                            $newData,
                            $oldData,
                            $this->DistributorImage->table,
                            $this->Session->read('Auth'),
                            $distributorImage['DistributorImage']['distributor_id'],
                            ConstantsLogType::DISTRIBUTOR
                        );
                    } else {
                        $this->Session->setFlashError(__t('Garage.Cant_principal'));
                    }

                    $images = $this->DistributorImage->findAllByDistributorId($distributorImage['DistributorImage']['distributor_id']);
                    $files = Hash::extract($images, '{n}.DistributorImage');
                    $files['DistributorImage'] = $files;
                    $this->set(
                        array(
                            'current_network' => CakeSession::read('Auth.User.CurrentNetwork'),
                            'distributor' => $files,
                            'images' => $images,
                            'principal' => $id,
                        )
                    );

                    $this->layout = null;
                    $this->render('/DistributorsImages/Elements/gallery');
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
     * AJAX set DistributorImage as NO principal.
     */
    public function ajax_principal_image()
    {
        $this->verify_ajax($this->request);

        $id = $this->request->data['id'];
        $distributorImage = $this->DistributorImage->findById($id);

        if ($distributorImage) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $distributor = $this->Distributor->findByIdAndAagRegionId($distributorImage['DistributorImage']['distributor_id'], $aagRegionId);

            if (
                $distributor &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
                $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
            ) {
                if (!$this->request->is('get')) {
                    $principalOld = $this->DistributorImage->findByPrincipalAndDistributorId(ConstantsBooleans::YES, $distributorImage['DistributorImage']['distributor_id']);

                    if (!empty($principalOld)) {
                        if ($this->DistributorImage->convertToPrincipalDistributorImage($principalOld['DistributorImage']['id'], ConstantsBooleans::NO)) {
                            $principalNew = $this->DistributorImage->convertToPrincipalDistributorImage($id, ConstantsBooleans::YES);
                            if (!$principalNew) {
                                $this->Session->setFlashError(__t('Distributor.Cant_principal'));
                            }
                        } else {
                            $this->Session->setFlashError(__t('Distributor.Cant_principal'));
                        }
                    } else {
                        if ($this->DistributorImage->convertToPrincipalDistributorImage($id, ConstantsBooleans::YES)) {
                            $newData = array(
                                'principal' => 'Not Principal'
                            );
                            $oldData = array(
                                'principal' => 'Principal'
                            );
                            $this->LogChange->get_params_create_log_edit(
                                $newData,
                                $oldData,
                                $this->DistributorImage->table,
                                $this->Session->read('Auth'),
                                $distributorImage['DistributorImage']['distributor_id'],
                                ConstantsLogType::DISTRIBUTOR
                            );
                        } else {
                            $this->Session->setFlashError(__t('Distributor.Cant_principal'));
                        }
                    }

                    $images = $this->DistributorImage->findAllByDistributorId($distributorImage['DistributorImage']['distributor_id']);
                    $files = Hash::extract($images, '{n}.DistributorImage');
                    $files['DistributorImage'] = $files;
                    $this->set(
                        array(
                            'current_network' => CakeSession::read('Auth.User.CurrentNetwork'),
                            'distributor' => $files,
                            'images' => $images,
                            'principal' => $id,

                        )
                    );

                    $this->layout = null;
                    $this->render('/DistributorsImages/Elements/gallery');
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
     * AJAX delete DistributorImage.
     */
    public function ajax_delete_file()
    {
        $this->verify_ajax($this->request);

        $id = $this->request->data['id'];
        $distributorImage = $this->DistributorImage->findById($id);

        if ($distributorImage) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $distributor = $this->Distributor->findByIdAndAagRegionId($distributorImage['DistributorImage']['distributor_id'], $aagRegionId);

            if (
                $distributor &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
                $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
            ) {
                if (!$this->request->is('get')) {
                    if ($this->DistributorImage->deleteDistributorImage($id)) {
                        $this->LogChange->get_params_create_log_delete(
                            $distributorImage['DistributorImage'],
                            $this->DistributorImage->table,
                            $this->Session->read('Auth'),
                            $distributorImage['DistributorImage']['distributor_id'],
                            ConstantsLogType::DISTRIBUTOR
                        );
                    } else {
                        $this->Session->setFlashError(__t('Distributor.Cant_delete_image'));
                    }

                    $files = $this->DistributorImage->findAllByDistributorId($distributorImage['DistributorImage']['distributor_id']);
                    $files = Hash::extract($files, '{n}.DistributorImage');
                    $files['DistributorImage'] = $files;
                    $this->set(
                        array(
                            'distributor' => $files,
                        )
                    );

                    $this->layout = null;
                    $this->render('/Distributors/Elements/form_attached_facade_images');
                }
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }
}
