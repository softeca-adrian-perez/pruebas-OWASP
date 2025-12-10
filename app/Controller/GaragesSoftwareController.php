<?php
class GaragesSoftwareController extends AppController
{
    public $uses = array(
        'Garage',
        'GarageNetwork',
        'GarageSoftware',
        'Software',
        'LogChange',
        'SoftwareType',
        'Supplier',
        'SoftwareManufacture',
        'BillingSchedule',
        'Country',
    );

    // public function view($garage_software_id)
    // {
    //     $garageSoftware = $this->GarageSoftware->findById($garage_software_id);
    //     $garage = $this->Garage->findById($garageSoftware['GarageSoftware']['garage_id']);
    //     $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage['Garage']['id']);
    //     $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE);

    //     $cancelAction = array(
    //         'url_cancel' => array(
    //             'controller' => 'garages',
    //             'action' => 'add_equipment_and_software_garage',
    //             $garageSoftware['GarageSoftware']['garage_id']
    //         ),
    //     );

    //     $this->setVarForm($garage['Garage']['aag_region_id']);
    //     $this->set(array(
    //         'garage_software' => $garageSoftware,
    //         'cancel_action' => $cancelAction,
    //         'garage_id' => $garageSoftware['GarageSoftware']['garage_id'],
    //         'billing_schedule' => $this->BillingSchedule->search_list($garage['Garage']['aag_region_id']),
    //     ));
    // }

    /**
     * Create GarageSoftware
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
            $aagRegionId = $garage['Garage']['aag_region_id'];
            $suppliers = $this->Supplier->getSuppliersByRegion($aagRegionId);

            $country = array();
            if (isset($garage['Garage']['province_id'])) {
                $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
            } elseif (isset($garage['Garage']['city_id'])) {
                $country = $this->Country->get_country_by_city($garage['Garage']['city_id']);
            }

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'garages',
                    'action' => 'add_equipment_and_software_garage',
                    $garage_id
                ),
            );

            if ($this->request->is('post')) {
                $garageSoftware = $this->GarageSoftware->add_garage_software($this->request->data, $garage_id);
                if ($garageSoftware) {
                    $this->LogChange->get_params_create_log_add(
                        $garageSoftware['GarageSoftware'],
                        $this->GarageSoftware->table,
                        $this->Session->read('Auth'),
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'garages_software',
                            'action' => 'edit',
                            $this->GarageSoftware->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm($aagRegionId);
            $this->set(array(
                'garage_id' => $garage_id,
                'cancel_action' => $cancelAction,
                'billing_schedule' => $this->BillingSchedule->search_list($aagRegionId),
                'suppliers' => $suppliers,
                'country' => $country,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit GarageSoftware.
     */
    public function edit($garage_software_id)
    {
        $garageSoftware = $this->GarageSoftware->findById($garage_software_id);

        if ($garageSoftware) {
            $garageId = $garageSoftware['GarageSoftware']['garage_id'];

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
                $aagRegionId = $garage['Garage']['aag_region_id'];
                $suppliers = $this->Supplier->getSuppliersByRegion($aagRegionId);

                $country = array();
                if (isset($garage['Garage']['province_id'])) {
                    $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
                } elseif (isset($garage['Garage']['city_id'])) {
                    $country = $this->Country->get_country_by_city($garage['Garage']['city_id']);
                }

                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'garages_software',
                        'action' => 'view',
                        $garageSoftware['GarageSoftware']['id']
                    ),
                );

                if (!$this->request->is('get')) {
                    $oldData = $garageSoftware;
                    $garageSoftwareBd = $this->GarageSoftware->edit_garage_software($this->request->data);

                    if ($garageSoftwareBd) {
                        $this->LogChange->get_params_create_log_edit(
                            $oldData['GarageSoftware'],
                            $garageSoftwareBd['GarageSoftware'],
                            $this->GarageSoftware->table,
                            $this->Session->read('Auth'),
                            $garageSoftware['GarageSoftware']['garage_id'],
                            ConstantsLogType::GARAGE
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $garageSoftware['GarageSoftware']['start_date'] = Fecha::toFormatoVista($garageSoftware['GarageSoftware']['start_date']);
                    $garageSoftware['GarageSoftware']['end_date'] = Fecha::toFormatoVista($garageSoftware['GarageSoftware']['end_date']);
                    $this->request->data = $garageSoftware;
                }

                $this->setVarForm($aagRegionId);
                $this->set(array(
                    'garage_software' => $garageSoftware,
                    'garage_id' =>  $garageSoftware['GarageSoftware']['garage_id'],
                    'cancel_action' => $cancelAction,
                    'billing_schedule' => $this->BillingSchedule->search_list($aagRegionId),
                    'suppliers' => $suppliers,
                    'country' => $country,
                    'garage' => $garage
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
     * Delete GarageSofteare.
     */
    public function delete($garage_id, $garage_software_id)
    {
        $garageSoftware = $this->GarageSoftware->findById($garage_software_id);
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garageNetworks)) {
            $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage && $garageSoftware &&
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
            $delete = $this->GarageSoftware->delete($garage_software_id);
            if ($delete) {
                $user = $this->Session->read('Auth');
                $this->LogChange->get_params_create_log_delete(
                    $garageSoftware['GarageSoftware'],
                    $this->GarageSoftware->table,
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
                'action' => 'add_equipment_and_software_garage',
                $garage_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm($aag_region_id)
    {
        $software = $this->Software->search_list($aag_region_id);
        $softwareTypes = $this->SoftwareType->search_list($aag_region_id);
        $softwareManufactures = $this->SoftwareManufacture->search_list();

        $this->set(array(
            'software' => $software,
            'software_types' => $softwareTypes,
            'software_manufactures' => $softwareManufactures,
        ));
    }
}
