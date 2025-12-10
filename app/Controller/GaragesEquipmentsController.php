<?php
class GaragesEquipmentsController extends AppController
{
    public $uses = array(
        'Garage',
        'GarageNetwork',
        'GarageEquipment',
        'Equipment',
        'LogChange',
        'EquipmentType',
        'Supplier',
        'Brand',
        'BillingSchedule',
        'Country',
    );

    // public function view($garage_equipment_id)
    // {
    //     $garageEquipment = $this->GarageEquipment->findById($garage_equipment_id);
    //     $garage = $this->Garage->findById($garageEquipment['GarageEquipment']['garage_id']);
    //     $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage['Garage']['id']);
    //     $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE);

    //     $equipmentTypes = $this->EquipmentType->search_list($garage['Garage']['aag_region_id']);
    //     $equipments = $this->Equipment->search_list($garage['Garage']['aag_region_id']);
    //     $brands = $this->Brand->search_list();
    //     $suppliers = $this->Supplier->search_list();
    //     $billings_schedules = $this->BillingSchedule->search_list($garage['Garage']['aag_region_id']);

    //     $cancelAction = array(
    //         'url_cancel' => array(
    //             'controller' => 'garages',
    //             'action' => 'add_equipment_and_software_garage',
    //             $garageEquipment['GarageEquipment']['garage_id']
    //         ),
    //     );

    //     $this->set(array(
    //         'garage_equipment' => $garageEquipment,
    //         'equipment_types' => $equipmentTypes,
    //         'equipments' => $equipments,
    //         'brands' => $brands,
    //         'suppliers' => $suppliers,
    //         'billings_schedules' => $billings_schedules,
    //         'cancel_action' => $cancelAction,
    //         'garage_id' => $garageEquipment['GarageEquipment']['garage_id'],
    //         'billing_schedule' => $this->BillingSchedule->search_list($garage['Garage']['aag_region_id']),
    //     ));
    // }

    /**
     * Create GarageEquipments.
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
                $garageEquipment = $this->GarageEquipment->add_garage_equipment($this->request->data, $garage_id);
                if ($garageEquipment) {
                    $this->LogChange->get_params_create_log_add(
                        $garageEquipment['GarageEquipment'],
                        $this->GarageEquipment->table,
                        $this->Session->read('Auth'),
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'garages_equipments',
                            'action' => 'edit',
                            $this->GarageEquipment->getLastInsertID()
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
     * Edit GarageEquipment.
     */
    public function edit($garage_equipment_id)
    {
        $garageEquipment = $this->GarageEquipment->findById($garage_equipment_id);

        if ($garageEquipment) {
            $garageId = $garageEquipment['GarageEquipment']['garage_id'];
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
                        'controller' => 'garages_equipments',
                        'action' => 'view',
                        $garageEquipment['GarageEquipment']['id']
                    ),
                );

                if (!$this->request->is('get')) {
                    $oldData = $garageEquipment;
                    $garageEquipmentBd = $this->GarageEquipment->edit_garage_equipment($this->request->data, $garageEquipment['GarageEquipment']['garage_id']);

                    if ($garageEquipmentBd) {
                        $this->LogChange->get_params_create_log_edit(
                            $oldData['GarageEquipment'],
                            $garageEquipmentBd['GarageEquipment'],
                            $this->GarageEquipment->table,
                            $this->Session->read('Auth'),
                            $garageEquipment['GarageEquipment']['garage_id'],
                            ConstantsLogType::GARAGE
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $garageEquipment['GarageEquipment']['start_date'] = Fecha::toFormatoVista($garageEquipment['GarageEquipment']['start_date']);
                    $garageEquipment['GarageEquipment']['end_date'] = Fecha::toFormatoVista($garageEquipment['GarageEquipment']['end_date']);
                    $this->request->data = $garageEquipment;
                }

                $this->setVarForm($aagRegionId);
                $this->set(array(
                    'garage_equipment' => $garageEquipment,
                    'garage_id' =>  $garageEquipment['GarageEquipment']['garage_id'],
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
     * Delete GarageEquipment.
     */
    public function delete($garage_id, $garage_equipment_id)
    {
        $garageEquipment = $this->GarageEquipment->findById($garage_equipment_id);
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garageNetworks)) {
            $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage && $garageEquipment &&
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

            $delete = $this->GarageEquipment->delete($garage_equipment_id);
            if ($delete) {
                $this->LogChange->get_params_create_log_delete(
                    $garageEquipment['GarageEquipment'],
                    $this->GarageEquipment->table,
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

    private function setVarForm($aagRegionId)
    {
        $equipmentTypes = $this->EquipmentType->search_list($aagRegionId);
        $equipments = $this->Equipment->search_list($aagRegionId);
        $brands = $this->Brand->search_list();

        $this->set(array(
            'equipments' => $equipments,
            'equipment_types' => $equipmentTypes,
            'brands' => $brands
        ));
    }
}
