<?php
class DistributorsServicesController extends AppController
{
    public $uses = array(
        'DistributorService',
        'ServiceType',
        'LogChange',
    );

    /**
     * Create DistributorService.
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
            $servicesTypes = $this->ServiceType->find('list');
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'add_services',
                    $distributor_id
                ),
            );

            if (!$this->request->is('get')) {
                $serviceBd = $this->DistributorService->add_service($this->request->data, $distributor_id);
                if ($serviceBd) {
                    unset($this->request->data['DistributorService']['id']);
                    unset($this->request->data['DistributorService']['distributor_id']);
                    $this->LogChange->get_params_create_log_add(
                        $this->request->data['DistributorService'],
                        $this->DistributorService->table,
                        $this->Session->read('Auth'),
                        $distributor_id,
                        ConstantsLogType::DISTRIBUTOR
                    );

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));

                    $this->redirect(
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_services',
                            $distributor_id
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'distributor_id' => $distributor_id,
                'services_types' => $servicesTypes
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit DistributorService.
     */
    public function edit($service_id, $distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId, 'id');
        $distributorService = $this->DistributorService->findById($service_id);

        if (
            $distributor && $distributorService &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $servicesTypes = $this->ServiceType->find('list');
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'add_services',
                    $distributor_id
                ),
            );

            if (!$this->request->is('get')) {
                $oldData = $distributorService;
                $serviceBd = $this->DistributorService->edit_service($this->request->data, $distributor_id);
                if ($serviceBd) {
                    if ($oldData != $serviceBd) {
                        unset($oldData['DistributorService']['id']);
                        unset($oldData['DistributorService']['distributor_id']);
                        unset($serviceBd['DistributorService']['id']);
                        unset($serviceBd['DistributorService']['distributor_id']);
                        $this->LogChange->get_params_create_log_edit(
                            $oldData['DistributorService'],
                            $serviceBd['DistributorService'],
                            $this->DistributorService->table,
                            $this->Session->read('Auth'),
                            $distributor_id,
                            ConstantsLogType::DISTRIBUTOR
                        );
                    }

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'distributors',
                            'action' => 'add_services',
                            $distributor_id
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                $distributorService['DistributorService']['start_date'] = Fecha::toFormatoVistaFecha($distributorService['DistributorService']['start_date']);
                $distributorService['DistributorService']['end_date'] = Fecha::toFormatoVistaFecha($distributorService['DistributorService']['end_date']);
                $this->request->data = $distributorService;
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'distributor_id' => $distributor_id,
                'services_types' => $servicesTypes,
                'service' => $distributorService
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete DistributorService.
     */
    public function delete($distributor_service_id, $distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId, 'id');
        $distributorService = $this->DistributorService->findById($distributor_service_id);

        if (
            $distributor && $distributorService &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            if ($this->DistributorService->delete($distributor_service_id)) {
                unset($distributorService['DistributorService']['id']);
                unset($distributorService['DistributorService']['distributor_id']);
                $this->LogChange->get_params_create_log_delete(
                    $distributorService['DistributorService'],
                    $this->DistributorService->table,
                    $this->Session->read('Auth'),
                    $distributor_id,
                    ConstantsLogType::DISTRIBUTOR
                );
            }

            $this->redirect(
                array(
                    'controller' => 'distributors',
                    'action' => 'add_services',
                    $distributor_id
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }
}
