<?php
class DistributorsContractsController extends AppController
{
    public $uses = array(
        'Distributor',
        'DistributorContract',
        'TradingGroup',
        'LogChange',
        'LeavingReasonType',
        'DistributorNetwork'
    );

    /**
     * Create DistributorContract.
     */
    public function add($distributor_id)
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
            $tradingGroups = $this->TradingGroup->getIndependent($aagRegionId);
            $leavingReasons = $this->LeavingReasonType->getListByRegion($aagRegionId);
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'add_contract',
                    $distributor_id
                ),
            );

            if (!$this->request->is('get')) {
                $distributorContractBd = $this->DistributorContract->add_contract($this->request->data, $distributor_id);
                if ($distributorContractBd) {
                    $this->LogChange->get_params_create_log_add(
                        $this->request->data['DistributorContract'],
                        $this->DistributorContract->table,
                        $this->Session->read('Auth'),
                        $distributor_id,
                        ConstantsLogType::DISTRIBUTOR
                    );

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'distributors_contracts',
                            'action' => 'edit',
                            $this->DistributorContract->getLastInsertID(),
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
                'trading_groups' => $tradingGroups,
                'leaving_reasons' => $leavingReasons,
                'network_options' => array()
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit DistributorContract.
     */
    public function edit($contract_id, $distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);
        $distributorContract = $this->DistributorContract->findById($contract_id);

        if (
            $distributor && $distributorContract &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $networkOptions = array();
            if ($distributorContract['DistributorContract']['network_id']) {
                $network = $this->DistributorNetwork->findById($distributorContract['DistributorContract']['network_id']);
                $networkOptions = array(
                    $network['DistributorNetwork']['id'] => $network['DistributorNetwork']['name']
                );
            }

            $tradingGroups = $this->TradingGroup->getIndependent($aagRegionId);
            $distributorNetworks = $this->DistributorNetwork->getRelatedNetworksList($distributorContract['DistributorContract']['trading_group_id']);
            $leavingReasons = $this->LeavingReasonType->getListByRegion($aagRegionId);
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'add_contract',
                    $distributor_id
                ),
            );

            if (!$this->request->is('get')) {
                $oldData = $this->DistributorContract->findById($contract_id);
                $distributorContractBd = $this->DistributorContract->edit_contract($this->request->data, $distributor_id);
                if ($distributorContractBd) {
                    if ($oldData != $distributorContractBd) {
                        $this->LogChange->get_params_create_log_edit(
                            $oldData['DistributorContract'],
                            $distributorContractBd['DistributorContract'],
                            $this->DistributorContract->table,
                            $this->Session->read('Auth'),
                            $distributor_id,
                            ConstantsLogType::DISTRIBUTOR
                        );
                    }
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                $contract['DistributorContract']['start_date'] = Fecha::toFormatoVistaFecha($contract['DistributorContract']['start_date']);
                $contract['DistributorContract']['end_date'] = Fecha::toFormatoVistaFecha($contract['DistributorContract']['end_date']);
                $this->request->data = $contract;
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'distributor_id' => $distributor_id,
                'trading_groups' => $tradingGroups,
                'contract' => $contract,
                'leaving_reasons' => $leavingReasons,
                'network_options' => $networkOptions,
                'distributor_networks' => $distributorNetworks,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete DistributorContract.
     */
    public function delete($distributor_contract_id, $distributor_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);
        $distributorContract = $this->DistributorContract->findById($distributor_contract_id, 'id');

        if (
            $distributor && $distributorContract &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $this->DistributorContract->delete($distributor_contract_id);

            $this->redirect(
                array(
                    'controller' => 'distributors',
                    'action' => 'add_contract',
                    $distributor_id
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get DistributorNetworks.
     */
    public function ajax_distributor_networks()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
        ) {
            $distributorNetworks = $this->DistributorNetwork->getRelatedNetworksList($this->request->data['trading_group_id']);

            $this->set(array(
                'distributor_networks' => $distributorNetworks
            ));

            $this->layout = false;
            $this->render('../DistributorsContracts/Elements/distributor_networks');
        } else {
            throw new UnauthorizedException();
        }
    }
}
