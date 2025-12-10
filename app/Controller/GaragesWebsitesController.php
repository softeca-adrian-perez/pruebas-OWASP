<?php
class GaragesWebsitesController extends AppController
{
    public $uses = array(
        'Garage',
        'GarageNetwork',
        'GarageWebsite',
        'Website',
        'LogChange',
    );

    // public function view($garage_website_id)
    // {
    //     $website = $this->GarageWebsite->findById($garage_website_id);
    //     $garage = $this->Garage->findById($website['GarageWebsite']['garage_id']);
    //     $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage['Garage']['id']);
    //     $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE);

    //     $this->setVarWebsites();
    //     $this->set(array(
    //         'website' => $website,
    //         'garage_id' => $website['GarageWebsite']['garage_id']
    //     ));
    // }

    /**
     * Create GarageWebsite.
     */
    public function add($garage_id)
    {
        $garage = $this->Garage->findById($garage_id);
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garageNetworks)) {
            $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
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
                    'action' => 'add_marketing_and_image_garage',
                    $garage_id
                ),
            );

            if ($this->request->is('post')) {
                $garageWebsite = $this->GarageWebsite->add_garage_website($this->request->data, $garage_id);
                if ($garageWebsite) {
                    $this->LogChange->get_params_create_log_add(
                        $garageWebsite['GarageWebsite'],
                        $this->GarageWebsite->table,
                        $this->Session->read('Auth'),
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'garages_websites',
                            'action' => 'edit',
                            $this->GarageWebsite->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarWebsites();
            $this->set(array(
                'cancel_action' => $cancelAction,
                'garage_id' => $garage_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit GarageWebsite.
     */
    public function edit($garage_website_id)
    {
        $garageWebsite = $this->GarageWebsite->findById($garage_website_id);

        if ($garageWebsite) {
            $garageId = $garageWebsite['GarageWebsite']['garage_id'];
            $garage = $this->Garage->findById($garageId);
            $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);
            if (empty($garageNetworks)) {
                $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garageId, 'network_id' => '-1',));
            }

            if (
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
                        'controller' => 'garages_websites',
                        'action' => 'view',
                        $garage_website_id
                    ),
                );

                if (!$this->request->is('get')) {
                    $oldData = $garageWebsite;

                    $garageWebsiteBd = $this->GarageWebsite->edit_garage_website($this->request->data);
                    if ($garageWebsiteBd) {
                        $this->LogChange->get_params_create_log_edit(
                            $oldData['GarageWebsite'],
                            $garageWebsiteBd['GarageWebsite'],
                            $this->GarageWebsite->table,
                            $this->Session->read('Auth'),
                            $garageWebsite['GarageWebsite']['garage_id'],
                            ConstantsLogType::GARAGE
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect(
                            array(
                                'controller' => 'garages_websites',
                                'action' => 'edit',
                                $garage_website_id
                            )
                        );
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $this->request->data = $garageWebsite;
                }

                $this->setVarWebsites();
                $this->set(array(
                    'cancel_action' => $cancelAction,
                    'garage_id' => $garageId,
                    'garage_website_id' => $garage_website_id,
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
     * Delete GarageWebsite.
     */
    public function delete($garage_id, $garage_website_id)
    {
        $garageWebsite = $this->GarageWebsite->findById($garage_website_id);
        $garage = $this->Garage->findById($garage_id);
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garageNetworks)) {
            $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage && $garageWebsite &&
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
            $user = $this->Session->read('Auth');

            if ($this->GarageWebsite->delete($garage_website_id)) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
            }

            $this->LogChange->get_params_create_log_delete(
                $garageWebsite['GarageWebsite'],
                $this->GarageWebsite->table,
                $user,
                $garage_id,
                ConstantsLogType::GARAGE
            );

            $this->redirect(array(
                'controller' => 'garages',
                'action' => 'add_marketing_and_image_garage',
                $garage_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarWebsites()
    {
        $websites = $this->Website->search_list(CakeSession::read('Auth.User.aag_region_id'));

        $this->set(array(
            'websites' => $websites,
        ));
    }
}
