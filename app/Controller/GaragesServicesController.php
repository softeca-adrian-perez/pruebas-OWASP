<?php
class GaragesServicesController extends AppController
{
    public $uses = array(
        'Garage',
        'LogChange',
        'Supplier',
        'Service',
        'Facility',
        'ValueAddSupplier',
        'ValueAddSupplierType',
        'GarageValueAddSupplier',
        'GarageNetwork'
    );

    /**
     * Create GarageValueAddSupplier.
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
            $facilities = $this->Facility->search_list();
            $valuesAddSupplier = $this->ValueAddSupplier->getDataByGarage($garage_id);
            $valueAddSupplierTypes = $this->ValueAddSupplierType->getDataByGarage($garage_id);

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'garages',
                    'action' => 'add_equipment_and_software_garage',
                    $garage_id
                ),
            );

            if (!$this->request->is('get')) {
                $garageValueAddSupplier = $this->GarageValueAddSupplier->add_garage_value_add_supplier($this->request->data);

                if ($garageValueAddSupplier) {
                    $this->LogChange->get_params_create_log_add(
                        $garageValueAddSupplier['GarageValueAddSupplier'],
                        $this->GarageValueAddSupplier->table,
                        $this->Session->read('Auth'),
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'garages_services',
                            'action' => 'edit',
                            $this->GarageValueAddSupplier->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->set(array(
                'garage_id' => $garage_id,
                'cancel_action' => $cancelAction,
                'facilities' => $facilities,
                'value_and_suppliers' => $valuesAddSupplier,
                'value_and_supplier_type' => $valueAddSupplierTypes,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit GarageValueAddSupplier.
     */
    public function edit($service_id)
    {
        $garageValueAddSupplier = $this->GarageValueAddSupplier->findById($service_id);

        if (!$garageValueAddSupplier) {
            $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_SERVICE));
            $this->redirect(
                array(
                    'controller' => 'garages',
                    'action' => 'add_activities_and_services_garage',
                    $garageId
                )
            );
        }

        $garageId = $garageValueAddSupplier['GarageValueAddSupplier']['garage_id'];

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
            $facilities = $this->Facility->search_list();
            $valuesAddSupplier = $this->ValueAddSupplier->search_list();
            $valueAddSupplierTypes = $this->ValueAddSupplierType->search_list();

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'garages',
                    'action' => 'add_activities_and_services_garage',
                    $garageId
                ),
            );

            $garageValueAddSupplier['GarageValueAddSupplier']['from_date'] = Fecha::toFormatoVista($garageValueAddSupplier['GarageValueAddSupplier']['from_date']);
            $garageValueAddSupplier['GarageValueAddSupplier']['to_date'] = Fecha::toFormatoVista($garageValueAddSupplier['GarageValueAddSupplier']['to_date']);

            if ($this->request->is('get')) {
                $this->request->data = $garageValueAddSupplier;
            } else {
                $this->request->data['User']['id'] = $service_id;
                $oldData = $this->GarageValueAddSupplier->findById($garageValueAddSupplier['GarageValueAddSupplier']['id']);
                $garageValueAddSupplierBd = $this->GarageValueAddSupplier->edit_garage_value_add_supplier($this->request->data);
                if ($garageValueAddSupplierBd) {
                    $this->LogChange->get_params_create_log_edit(
                        $oldData['GarageValueAddSupplier'],
                        $garageValueAddSupplierBd['GarageValueAddSupplier'],
                        $this->GarageValueAddSupplier->table,
                        $this->Session->read('Auth'),
                        $garageId,
                        ConstantsLogType::GARAGE
                    );
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->set(array(
                'service_id' => $service_id,
                'cancel_action' => $cancelAction,
                'facilities' => $facilities,
                'value_and_suppliers' => $valuesAddSupplier,
                'value_and_supplier_type' => $valueAddSupplierTypes,
                'garage_id' => $garageId,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }
}
