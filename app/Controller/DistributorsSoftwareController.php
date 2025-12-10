<?php
class DistributorsSoftwareController extends AppController
{
    public $uses = array(
        'DistributorSoftware',
        'Distributor',
        'Software',
        'LogChange',
        'SoftwareType',
        'TradingGroup',
        'Supplier',
        'SoftwareManufacture',
        'Software',
    );

    /**
     * View Distributor softwares.
     */
    public function view($distributor_software_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $distributorSoftware = $this->DistributorSoftware->findById($distributor_software_id);

        if ($distributorSoftware) {
            $distributorId = $distributorSoftware['DistributorSoftware']['distributor_id'];
            $distributor = $this->Distributor->findByIdAndAagRegionId($distributorId, $aagRegionId, 'id');

            if (
                $distributor &&
                (
                    $roleId == ConstantsRoles::SUPER_ADMIN ||
                    (
                        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                        CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                        $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
                        $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
                    )
                )
            ) {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'distributors',
                        'action' => 'add_software',
                        $distributorId
                    ),
                );

                $this->setVarForm($distributorId);
                $this->set(array(
                    'distributor_software' => $distributorSoftware,
                    'cancel_action' => $cancelAction,
                    'distributor_id' => $distributorSoftware['DistributorSoftware']['distributor_id']
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /***
     * Create DistributorSoftware.
     */
    public function add($distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId, 'id');

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'add_software',
                    $distributor_id
                ),
            );

            if (!$this->request->is('get')) {
                $distributorSoftware = $this->DistributorSoftware->add_distributor_software($this->request->data, $distributor_id);
                if ($distributorSoftware) {
                    $this->LogChange->get_params_create_log_add(
                        $distributorSoftware['DistributorSoftware'],
                        $this->DistributorSoftware->table,
                        $this->Session->read('Auth'),
                        $distributor_id,
                        ConstantsLogType::DISTRIBUTOR
                    );

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'distributors_software',
                            'action' => 'edit',
                            $this->DistributorSoftware->getLastInsertID(),
                            $distributor_id
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm($distributor_id);
            $this->set(array(
                'cancel_action' => $cancelAction,
                'distributor_id' => $distributor_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit DistributorSoftware.
     */
    public function edit($distributor_software_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributorSoftware = $this->DistributorSoftware->findById($distributor_software_id);

        if ($distributorSoftware) {
            $distributorId = $distributorSoftware['DistributorSoftware']['distributor_id'];
            $distributor = $this->Distributor->findByIdAndAagRegionId($distributorId, $aagRegionId, 'id');

            if (
                $distributor &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
                $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
            ) {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'distributors_software',
                        'action' => 'view',
                        $distributorSoftware['DistributorSoftware']['id']
                    ),
                );

                if (!$this->request->is('get')) {
                    $oldData = $distributorSoftware;
                    $distributorBd = $this->DistributorSoftware->edit_distributor_software($this->request->data);

                    if ($distributorBd) {
                        $this->LogChange->get_params_create_log_edit(
                            $oldData['DistributorSoftware'],
                            $distributorBd['DistributorSoftware'],
                            $this->DistributorSoftware->table,
                            $this->Session->read('Auth'),
                            $distributorId,
                            ConstantsLogType::DISTRIBUTOR
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $distributorSoftware['DistributorSoftware']['start_date'] = Fecha::toFormatoVista($distributorSoftware['DistributorSoftware']['start_date']);
                    $distributorSoftware['DistributorSoftware']['end_date'] = Fecha::toFormatoVista($distributorSoftware['DistributorSoftware']['end_date']);
                    $this->request->data = $distributorSoftware;
                }

                $this->setVarForm($distributorId);
                $this->set(array(
                    'distributor_software' => $distributorSoftware,
                    'distributor_id' => $distributorId,
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
     * Delete DistributorSoftware.
     */
    public function delete($distributor_id, $distributor_software_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributorSoftware = $this->DistributorSoftware->findById($distributor_software_id);

        if ($distributorSoftware) {
            $distributorId = $distributorSoftware['DistributorSoftware']['distributor_id'];
            $distributor = $this->Distributor->findByIdAndAagRegionId($distributorId, $aagRegionId, 'id');

            if (
                $distributor &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
                $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
            ) {
                $user = $this->Session->read('Auth');

                if ($this->DistributorSoftware->delete($distributor_software_id)) {
                    $this->LogChange->get_params_create_log_delete(
                        $distributorSoftware['DistributorSoftware'],
                        $this->DistributorSoftware->table,
                        $user,
                        $distributor_id,
                        ConstantsLogType::DISTRIBUTOR
                    );
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
                }

                $this->redirect(array(
                    'controller' => 'distributors',
                    'action' => 'add_software',
                    $distributor_id
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm($distributorId)
    {
        $distributor = $this->Distributor->findById($distributorId);
        $distributorTradingGroup = $this->TradingGroup->findById($distributor['Distributor']['trading_group_id']);
        $software = $this->Software->search_list($distributorTradingGroup['TradingGroup']['aag_region_id']);
        $softwareTypes = $this->SoftwareType->search_list($distributorTradingGroup['TradingGroup']['aag_region_id']);
        $suppliers = $this->Supplier->search_list();
        $softwareManufactures = $this->SoftwareManufacture->search_list();

        $this->set(array(
            'software' => $software,
            'software_types' => $softwareTypes,
            'suppliers' => $suppliers,
            'software_manufactures' => $softwareManufactures,
        ));
    }
}
