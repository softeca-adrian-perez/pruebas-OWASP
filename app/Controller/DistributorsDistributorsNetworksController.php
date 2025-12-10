<?php
class DistributorsDistributorsNetworksController extends AppController
{
    public $uses = array(
        'DistributorDistributorNetwork',
        'Distributor',
        'DistributorNetwork',
        'NetworkStatus',
        'TradingGroup',
        'TradingGroupDistributorNetwork',
        'LeavingReasonType',
        'LogChange'
    );

    /**
     * Distributors distributors networks view.
     */
    public function view($distributor_distributor_network_id)
    {
        $distributorDistributorNetwork = $this->DistributorDistributorNetwork->getDatasById($distributor_distributor_network_id);

        if ($distributorDistributorNetwork) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $distributor = $this->Distributor->findByIdAndAagRegionId($distributorDistributorNetwork['DistributorDistributorNetwork']['distributor_id'], $aagRegionId);

            if (
                $distributor &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
                $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
            ) {
                $reasons_leaving = $this->LeavingReasonType->getList();
                $trading_groups = $this->TradingGroup->find('all');

                $networks_statuses = Configure::read('Network_Status');
                foreach ($networks_statuses as $key => $network_status) {
                    $networks_statuses[$key] = __t($network_status);
                }
                $distributors_networks = $this->DistributorNetwork->find('list', array('order' => 'DistributorNetwork.name'));

                $this->set(array(
                    'distributor_id' => $distributorDistributorNetwork['DistributorDistributorNetwork']['distributor_id'],
                    'trading_groups' => $trading_groups,
                    'distributor_network' => $distributorDistributorNetwork,
                    'reasons_leaving' => $reasons_leaving,
                    'networks_statuses' => $networks_statuses,
                    'distributors_networks' => $distributors_networks,
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
     * Create DistributorDistributorNetwork.
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
            $distributor_networks = $this->DistributorDistributorNetwork->findAllByDistributorId($distributor_id);
            $reasons_leaving = $this->LeavingReasonType->getListByRegion($aagRegionId, $user['role_id']);

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'add_networks_distributor',
                    $distributor_id
                ),
            );
            $networks_statuses = Configure::read('Network_Status');
            foreach ($networks_statuses as $key => $network_status) {
                $networks_statuses[$key] = __t($network_status);
            }
            $networks = $this->DistributorNetwork->findByRegion($aagRegionId);
            foreach ($distributor_networks as $distributor_network) {
                if ($distributor_network['DistributorDistributorNetwork']['status'] != ConstantsNetworksStatus::UNSUBSCRIBE) {
                    unset($networks[$distributor_network['DistributorDistributorNetwork']['network_id']]);
                }
            }

            if (!$this->request->is('get')) {
                $old_network = $this->DistributorDistributorNetwork->findByNetworkIdAndDistributorIdAndLast($this->request->data['DistributorDistributorNetwork']['network_id'], $distributor_id, ConstantsBooleans::YES);
                if ($old_network) {
                    $this->DistributorDistributorNetwork->setDistributorNetworkNotActive($old_network);
                }

                $distributor_network = $this->DistributorDistributorNetwork->add_distributor_network($this->request->data, $distributor_id);
                if ($distributor_network) {
                    $this->LogChange->get_params_create_log_add(
                        $distributor_network['DistributorDistributorNetwork'],
                        $this->DistributorDistributorNetwork->table,
                        $this->Session->read('Auth'),
                        $distributor_id,
                        ConstantsLogType::DISTRIBUTOR
                    );

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'distributors_distributors_networks',
                            'action' => 'edit',
                            $this->DistributorDistributorNetwork->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'distributor_id' => $distributor_id,
                'reasons_leaving' => $reasons_leaving,
                'networks_statuses' => $networks_statuses,
                'networks' => $networks,
                'trading_groups' => array()
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit DistributorDistributorNetwork.
     */
    public function edit($distributor_distributor_network_id)
    {
        $distributorDistributorNetwork = $this->DistributorDistributorNetwork->getDatasById($distributor_distributor_network_id);

        if ($distributorDistributorNetwork) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $distributor = $this->Distributor->findByIdAndAagRegionId($distributorDistributorNetwork['DistributorDistributorNetwork']['distributor_id'], $aagRegionId);

            if (
                $distributor &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
                $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
            ) {
                $reasons_leaving = $this->LeavingReasonType->getListByRegion($aagRegionId, $user['role_id']);
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'distributors_distributors_networks',
                        'action' => 'view',
                        $distributor_distributor_network_id
                    ),
                );
                $networks_statuses = Configure::read('Network_Status');
                foreach ($networks_statuses as $key => $network_status) {
                    $networks_statuses[$key] = __t($network_status);
                }
                $trading_groups = $this->TradingGroupDistributorNetwork->getListTradingGroupsByRegionAndNetwork($aagRegionId, $distributorDistributorNetwork['DistributorDistributorNetwork']['network_id']);

                $networks = $this->DistributorNetwork->findListByAagRegionId($aagRegionId);

                if (!$this->request->is('get')) {
                    $old_data = $this->DistributorDistributorNetwork->findById($distributorDistributorNetwork['DistributorDistributorNetwork']['id']);
                    $distributor_network_bd = $this->DistributorDistributorNetwork->edit_distributor_network($this->request->data);
                    if ($distributor_network_bd) {
                        if ($old_data != $distributor_network_bd) {
                            $this->LogChange->get_params_create_log_edit(
                                $old_data,
                                $distributorDistributorNetwork['DistributorDistributorNetwork'],
                                $this->DistributorDistributorNetwork->table,
                                $this->Session->read('Auth'),
                                $distributor['Distributor']['id'],
                                ConstantsLogType::DISTRIBUTOR
                            );
                        }
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $distributorDistributorNetwork['DistributorDistributorNetwork']['contract_start_date'] = Fecha::toFormatoVistaFecha($distributorDistributorNetwork['DistributorDistributorNetwork']['contract_start_date']);
                    $distributorDistributorNetwork['DistributorDistributorNetwork']['contract_end_date'] = Fecha::toFormatoVistaFecha($distributorDistributorNetwork['DistributorDistributorNetwork']['contract_end_date']);
                    $this->request->data = $distributorDistributorNetwork;
                }

                $this->set(array(
                    'cancel_action' => $cancelAction,
                    'distributor_network' => $distributorDistributorNetwork,
                    'distributor_id' => $distributorDistributorNetwork['DistributorDistributorNetwork']['distributor_id'],
                    'networks' => $networks,
                    'trading_groups' => $trading_groups,
                    'networks_statuses' => $networks_statuses,
                    'reasons_leaving' => $reasons_leaving,
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
     * AJAX get trading groups.
     */
    public function ajax_load_trading_groups()
    {
        $this->verify_ajax($this->request);
        $networkId = $this->request->data['network_id'];

        $distributorNetwork = $this->DistributorNetwork->findById($networkId, 'id');

        if (
            $distributorNetwork &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $tradingGroupFieldName = $this->request->data['trading_group_field_name'];
            $tradingGroups = $this->TradingGroupDistributorNetwork->getListTradingGroupsByRegionAndNetwork($aagRegionId, $networkId);

            $this->set(array(
                'trading_group_field_name' => $tradingGroupFieldName,
                'trading_groups' => $tradingGroups,
            ));
            $this->layout = null;
            $this->render('../DistributorsDistributorsNetworks/Elements/ajax_load_trading_groups');
        } else {
            throw new UnauthorizedException();
        }
    }
}
