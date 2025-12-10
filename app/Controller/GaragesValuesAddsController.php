<?php
class GaragesValuesAddsController extends AppController
{
    public $uses = array(
        'Garage',
        'GarageValueAdd',
        'ValueAdd',
        'GarageNetwork',
        'LogChange',
        'Supplier',
        'BillingSchedule',
        'Country',
    );

    // public function view($garage_value_add_id)
    // {
    //     $garageValueAdd = $this->GarageValueAdd->findById($garage_value_add_id);
    //     $garage = $this->Garage->findById($garageValueAdd['GarageValueAdd']['garage_id']);
    //     $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage['Garage']['id']);
    //     $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE);

    //     $cancelAction = array(
    //         'url_cancel' => array(
    //             'controller' => 'garages',
    //             'action' => 'add_value_add',
    //             $garageValueAdd['GarageValueAdd']['garage_id']
    //         ),
    //     );

    //     $this->setVarForm();
    //     $this->set(array(
    //         'garage_value_add' => $garageValueAdd,
    //         'cancel_action' => $cancelAction,
    //         'garage_id' => $garageValueAdd['GarageValueAdd']['garage_id'],
    //         'billing_schedule' => $this->BillingSchedule->search_list($garage['Garage']['aag_region_id']),
    //     ));
    // }

    /**
     * Create ValueAdd.
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
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
        ) {
            $country = array();
            if (isset($garage['Garage']['province_id'])) {
                $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
            } elseif (isset($garage['Garage']['city_id'])) {
                $country = $this->Country->get_country_by_city($garage['Garage']['city_id']);
            }

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'garages',
                    'action' => 'add_value_add',
                    $garage_id
                ),
            );

            if ($this->request->is('post')) {
                $garageValueAdd = $this->GarageValueAdd->add_garage_value_add($this->request->data, $garage_id);
                if ($garageValueAdd) {
                    $this->LogChange->get_params_create_log_add(
                        $garageValueAdd['GarageValueAdd'],
                        $this->GarageValueAdd->table,
                        $this->Session->read('Auth'),
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'garages_values_adds',
                            'action' => 'edit',
                            $this->GarageValueAdd->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'garage_id' => $garage_id,
                'cancel_action' => $cancelAction,
                'billing_schedule' => $this->BillingSchedule->search_list($garage['Garage']['aag_region_id']),
                'country' => $country,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit ValueAdd.
     */
    public function edit($garage_value_add_id)
    {
        $garageValueAdd = $this->GarageValueAdd->findById($garage_value_add_id);

        if ($garageValueAdd) {
            $garageId = $garageValueAdd['GarageValueAdd']['garage_id'];
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
                !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
                $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
            ) {
                $country = array();
                if (isset($garage['Garage']['province_id'])) {
                    $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
                } elseif (isset($garage['Garage']['city_id'])) {
                    $country = $this->Country->get_country_by_city($garage['Garage']['city_id']);
                }

                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'garages',
                        'action' => 'add_value_add',
                        $garageValueAdd['GarageValueAdd']['id']
                    ),
                );

                if (!$this->request->is('get')) {
                    $oldData = $garageValueAdd;
                    $garageValueAddBd = $this->GarageValueAdd->edit_garage_value_add($this->request->data);

                    if ($garageValueAddBd) {
                        $this->LogChange->get_params_create_log_edit(
                            $oldData['GarageValueAdd'],
                            $garageValueAddBd['GarageValueAdd'],
                            $this->GarageValueAdd->table,
                            $this->Session->read('Auth'),
                            $garageValueAdd['GarageValueAdd']['garage_id'],
                            ConstantsLogType::GARAGE
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $garageValueAdd['GarageValueAdd']['start_date'] = Fecha::toFormatoVista($garageValueAdd['GarageValueAdd']['start_date']);
                    $garageValueAdd['GarageValueAdd']['end_date'] = Fecha::toFormatoVista($garageValueAdd['GarageValueAdd']['end_date']);
                    $this->request->data = $garageValueAdd;
                }

                $this->setVarForm();
                $this->set(array(
                    'garage_value_add' => $garageValueAdd,
                    'garage_id' =>  $garageValueAdd['GarageValueAdd']['garage_id'],
                    'cancel_action' => $cancelAction,
                    'billing_schedule' => $this->BillingSchedule->search_list($garage['Garage']['aag_region_id']),
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
     * Delete ValueAdd.
     */
    public function delete($garage_id, $garage_value_add_id)
    {
        $garageValueAdd = $this->GarageValueAdd->findById($garage_value_add_id);
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garageNetworks)) {
            $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage && $garageValueAdd &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
        ) {
            $user = $this->Session->read('Auth');

            $delete = $this->GarageValueAdd->delete($garage_value_add_id);
            if ($delete) {
                $this->LogChange->get_params_create_log_delete(
                    $garageValueAdd['GarageValueAdd'],
                    $this->GarageValueAdd->table,
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
                'action' => 'add_value_add',
                $garage_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm()
    {
        $valueAdd = $this->ValueAdd->search_list();
        $this->set(array(
            'value_add' => $valueAdd,
        ));
    }
}
