<?php
class GaragesCommentsController extends AppController
{
    public $uses = array(
        'GarageComment',
        'Garage',
        'GarageNetwork',
        'User',
        'LogChange'
    );

    // public function view($garage_comment_id)
    // {
    //     $garageComments = $this->GarageComment->findById($garage_comment_id);
    //     $garage = $this->Garage->findById($garageComments['GarageComment']['garage_id']);
    //     $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage['Garage']['id']);
    //     $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE);

    //     $users = $this->User->find('list');
    //     $cancelAction = array(
    //         'url_cancel' => array(
    //             'controller' => 'garages',
    //             'action' => 'add_comment',
    //             $garageComments['GarageComment']['garage_id']
    //         ),
    //     );
    //     $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageComments['GarageComment']['garage_id']);
    //     $this->setVarForm();
    //     $this->set(array(
    //         'user' => $this->Session->read('Auth'),
    //         'garage_comments' => $garageComments,
    //         'garage_networks' => $garageNetworks,
    //         'garage' => $garage,
    //         'users' => $users,
    //         'cancel_action' => $cancelAction,
    //         'garage_id' => $garageComments['GarageComment']['garage_id']
    //     ));
    // }

    /**
     * Create GarageComment.
     */
    public function add($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garageNetworks)) {
            $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
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
        ) {
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'garages',
                    'action' => 'add_comments',
                    $garage_id
                ),
            );

            if ($this->request->is('post')) {
                $garageComment = $this->GarageComment->add_garage_comment($this->request->data, $garage_id, CakeSession::read('Auth.User.id'));
                if ($garageComment) {
                    $this->LogChange->get_params_create_log_add(
                        $garageComment['GarageComment'],
                        $this->GarageComment->table,
                        $this->Session->read('Auth'),
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'garages_comments',
                            'action' => 'edit',
                            $this->GarageComment->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'cancel_action' => $cancelAction,
                'garage_id' => $garage_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit GarageComments.
     */
    public function edit($garage_comment_id)
    {
        $garageComment = $this->GarageComment->findById($garage_comment_id);

        if ($garageComment) {
            $garageId = $garageComment['GarageComment']['garage_id'];

            $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
            $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);
            if (empty($garageNetworks)) {
                $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garageId, 'network_id' => '-1',));
            }

            if (
                $garage &&
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
            ) {
                $users = $this->User->find('list');
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'garages_comments',
                        'action' => 'view',
                        $garageComment['GarageComment']['id']
                    ),
                );

                if (!$this->request->is('get')) {
                    $garageBd = $this->GarageComment->edit_garage_comment($this->request->data, CakeSession::read('Auth.User.id'));
                    if ($garageBd) {
                        $this->LogChange->get_params_create_log_edit(
                            $garageComment['GarageComment'],
                            $garageBd['GarageComment'],
                            $this->GarageComment->table,
                            $this->Session->read('Auth'),
                            $garageComment['GarageComment']['garage_id'],
                            ConstantsLogType::GARAGE
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect(
                            array(
                                'controller' => 'garages',
                                'action' => 'add_comments',
                                $garageComment['GarageComment']['garage_id']
                            )
                        );
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $this->request->data = $garageComment;
                }

                $this->setVarForm();
                $this->set(array(
                    'users' => $users,
                    'garage_comments' => $garageComment,
                    'garage_id' =>  $garageComment['GarageComment']['garage_id'],
                    'cancel_action' => $cancelAction,
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete GarageComment.
     */
    public function delete($garage_id, $garage_comment_id)
    {
        $garageComment = $this->GarageComment->findById($garage_comment_id);

        if ($garageComment) {
            $garageId = $garageComment['GarageComment']['garage_id'];

            $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
            $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);
            if (empty($garageNetworks)) {
                $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garageId, 'network_id' => '-1',));
            }

            if (
                $garage &&
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
            ) {
                $delete = $this->GarageComment->delete($garage_comment_id);
                if ($delete) {
                    $user = $this->Session->read('Auth');
                    $this->LogChange->get_params_create_log_delete(
                        $garageComment['GarageComment'],
                        $this->GarageComment->table,
                        $user,
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
                }

                $this->redirect(array(
                    'controller' => 'garages',
                    'action' => 'add_comments',
                    $garage_id
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm()
    {
        $garageComments = $this->GarageComment->find('list');
        $this->set(array(
            'comment' => $garageComments,
        ));
    }
}
