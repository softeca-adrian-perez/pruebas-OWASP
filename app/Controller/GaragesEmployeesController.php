<?php
class GaragesEmployeesController extends AppController
{
    public $uses = array(
        'GarageEmployee',
        'Garage',
        'GarageNetwork',
        'EmployeeType',
        'LogChange',
        'Config'
    );

    // public function view($garage_employee_id)
    // {
    //     $garageEmployee = $this->GarageEmployee->findById($garage_employee_id);
    //     $garage = $this->Garage->findById($garageEmployee['GarageEmployee']['garage_id']);
    //     $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage['Garage']['id']);
    //     $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE);

    //     $cancelAction = array(
    //         'url_cancel' => array(
    //             'controller' => 'garages',
    //             'action' => 'add_employee_garage',
    //             $garageEmployee['GarageEmployee']['garage_id']
    //         ),
    //     );

    //     $this->setVarForm();
    //     $this->set(array(
    //         'garage_employees' => $garageEmployee,
    //         'cancel_action' => $cancelAction,
    //         'garage_id' => $garageEmployee['GarageEmployee']['garage_id']
    //     ));
    // }

    /**
     * Create GarageEmployee from Employees Garage tab.
     */
    public function add($garage_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);
        $getListConfigTabs = $this->Config->get_list_config_tabs();

        if (
            $garage &&
            $getListConfigTabs[ConstantsTabs::EMPLOYEES] == ConstantsBooleans::ACTIVE &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            (
                !in_array($roleId, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                (
                    $roleId == ConstantsRoles::DISTRIBUTOR &&
                    $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                )
            ) &&
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
        ) {
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'garages',
                    'action' => 'add_employee_garage',
                    $garage_id
                ),
            );

            if ($this->request->is('post')) {
                $garageEmployee = $this->GarageEmployee->add_garage_employee($this->request->data, $garage_id);
                if ($garageEmployee) {
                    $this->LogChange->get_params_create_log_add(
                        $garageEmployee['GarageEmployee'],
                        $this->GarageEmployee->table,
                        $this->Session->read('Auth'),
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));

                    $this->redirect(
                        array(
                            'controller' => 'garages',
                            'action' => 'add_employee_garage',
                            $garage_id
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
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit GarageEmployee from Employees Garage tab.
     */
    public function edit($garage_employee_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $garageEmployee = $this->GarageEmployee->findById($garage_employee_id);

        if ($garageEmployee) {
            $garageId = $garageEmployee['GarageEmployee']['garage_id'];
            $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);
            $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);
            $getListConfigTabs = $this->Config->get_list_config_tabs();

            if (
                $garage &&
                $getListConfigTabs[ConstantsTabs::EMPLOYEES] == ConstantsBooleans::ACTIVE &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                (
                    !in_array($roleId, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                    (
                        $roleId == ConstantsRoles::DISTRIBUTOR &&
                        $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                    )
                ) &&
                $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
            ) {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'garages_employees',
                        'action' => 'view',
                        $garageEmployee['GarageEmployee']['id']
                    ),
                );

                if (!$this->request->is('get')) {
                    $garageEmployeesBd = $this->GarageEmployee->edit_garage_employee($this->request->data, $garageEmployee['GarageEmployee']['garage_id']);
                    if ($garageEmployeesBd) {
                        $this->LogChange->get_params_create_log_edit(
                            $garageEmployee['GarageEmployee'],
                            $garageEmployeesBd['GarageEmployee'],
                            $this->GarageEmployee->table,
                            $this->Session->read('Auth'),
                            $garageEmployee['GarageEmployee']['garage_id'],
                            ConstantsLogType::GARAGE
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect(
                            array(
                                'controller' => 'garages',
                                'action' => 'add_employee_garage',
                                $garageEmployeesBd['GarageEmployee']['garage_id']
                            )
                        );
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $this->request->data = $garageEmployee;
                }

                $this->setVarForm();
                $this->set(array(
                    'garage_id' =>  $garageEmployee['GarageEmployee']['garage_id'],
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
     * Delete GarageEmployee from Employees Garage tab.
     */
    public function delete($garage_id, $garage_employees_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $garageEmployee = $this->GarageEmployee->findById($garage_employees_id);
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);
        $getListConfigTabs = $this->Config->get_list_config_tabs();

        if (
            $garage && $garageEmployee &&
            $getListConfigTabs[ConstantsTabs::EMPLOYEES] == ConstantsBooleans::ACTIVE &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            (
                !in_array($roleId, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                (
                    $roleId == ConstantsRoles::DISTRIBUTOR &&
                    $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                )
            ) &&
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
        ) {
            $delete = $this->GarageEmployee->delete($garage_employees_id);
            if ($delete) {
                $this->LogChange->get_params_create_log_delete(
                    $garageEmployee['GarageEmployee'],
                    $this->GarageEmployee->table,
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
                'action' => 'add_employee_garage',
                $garage_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm()
    {
        $employee_types = $this->EmployeeType->search_list();

        $this->set(array(
            'employee_types' => $employee_types,
        ));
    }

    /**
     * Create GarageEmployee from Staff Garage tab.
     */
    public function ajax_save_new_value($garage_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);

        if (
            $garage &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            (
                !in_array($roleId, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                (
                    $roleId == ConstantsRoles::DISTRIBUTOR &&
                    $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                )
            ) &&
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
        ) {
            $this->autoRender = false;

            $this->request->data['GarageEmployee']['employee_type_id'] = $this->request->data['employee'];
            $this->request->data['GarageEmployee']['number'] = $this->request->data['number'];
            $result = $this->GarageEmployee->add_garage_employee($this->request->data, $garage_id);

            if (isset($result['GarageEmployee'])) {
                $oldData = array();
                $this->LogChange->get_params_create_log_edit(
                    $oldData,
                    $result['GarageEmployee'],
                    $this->GarageEmployee->table,
                    $this->Session->read('Auth'),
                    $garage_id,
                    ConstantsLogType::GARAGE
                );
                return $result;
            }
            return false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get GarageEmployees from Staff Garage tab.
     */
    public function ajax_get_values_list($garage_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);

        if (
            $garage &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            (
                !in_array($roleId, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                (
                    $roleId == ConstantsRoles::DISTRIBUTOR &&
                    $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                )
            ) &&
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
        ) {
            $this->autoRender = false;

            $result['EmployeeType'] = $this->EmployeeType->getDataByGarage($garage_id);
            $result['GarageEmployee'] = $this->GarageEmployee->getDataByGarage($garage_id);
            return json_encode($result);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete GarageEmployee from Staff Garage tab.
     */
    public function ajax_delete_value()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $garageEmployee = $this->GarageEmployee->findById($this->request->data['id']);

        if ($garageEmployee) {
            $garageId = $garageEmployee['GarageEmployee']['garage_id'];
            $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);
            $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);

            if (
                $garage &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                (
                    !in_array($roleId, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                    (
                        $roleId == ConstantsRoles::DISTRIBUTOR &&
                        $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                    )
                ) &&
                $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
            ) {
                $this->autoRender = false;
                $result = $this->GarageEmployee->delete($this->request->data['id']);

                if ($result) {
                    $this->LogChange->get_params_create_log_delete(
                        $garageEmployee['GarageEmployee'],
                        $this->GarageEmployee->table,
                        $this->Session->read('Auth'),
                        $garageEmployee['GarageEmployee']['garage_id'],
                        ConstantsLogType::GARAGE
                    );
                }
                return true;
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }
}
