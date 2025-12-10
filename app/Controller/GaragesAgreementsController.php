<?php
class GaragesAgreementsController extends AppController
{
    public $uses = array(
        'GarageAgreement',
        'Garage',
        'Agreement',
        'TradingGroup',
        'Supplier',
        'LeavingReasonType',
        'LogChange',
        'HoldReasonType',
        'LeavingReasonType',
        'GarageNetwork'
    );

    /**
     * View GarageAgreement.
     */
    public function view($garage_agreement_id)
    {
        $garageAgreement = $this->GarageAgreement->getDatasById($garage_agreement_id);

        if ($garageAgreement) {
            $garageId = $garageAgreement['GarageAgreement']['garage_id'];
            $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));

            if (
                $garage &&
                (
                    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                    (
                        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                        CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                        $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                        (
                            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ||
                            (
                                CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                                $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                            )
                        ) &&
                        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::EDIT_AGREEMENTS)
                    )
                )
            ) {
                $reasons = null;
                if ($garageAgreement['GarageAgreement']['reason_leaving_id']) {
                    $reasons = $this->LeavingReasonType->getList();
                } elseif ($garageAgreement['GarageAgreement']['reason_hold_id']) {
                    $reasons = $this->HoldReasonType->getList();
                }

                $suppliers = $this->Supplier->find('list');

                $tradingGroups = $this->TradingGroup->find('all');

                $this->setVarAgreements();
                $this->set(array(
                    'garage_id' => $garageId,
                    'suppliers' => $suppliers,
                    'trading_groups' => $tradingGroups,
                    'garage_agreement' => $garageAgreement,
                    'reasons' => $reasons,
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
     * Create GarageAgreement.
     */
    public function add($garage_id)
    {
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);
        $garageInternalNetworks = $this->GarageNetwork->findInternalByGarageAndAagRegionId($garage_id, $aagRegionId);
        $garageExternalNetworks = $this->GarageNetwork->findExternalByGarageAndAagRegionId($garage_id, $aagRegionId);

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
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) &&
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_AGREEMENTS)
        ) {
            $reasonsLeaving = $this->LeavingReasonType->getList();
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'garages',
                    'action' => 'add_dis_and_net_garage',
                    $garage_id
                ),
            );

            if (!$this->request->is('get')) {
                $oldAgreement = $this->GarageAgreement->findByAgreementIdAndGarageId($this->request->data['GarageAgreement']['agreement_id'], $garage_id);
                if ($oldAgreement) {
                    $this->GarageAgreement->setGarageAgreementNotActive($oldAgreement);
                }

                $garageAgreement = $this->GarageAgreement->add_garage_agreement($this->request->data, $garage_id);
                if ($garageAgreement) {
                    $this->LogChange->get_params_create_log_add(
                        $garageAgreement['GarageAgreement'],
                        $this->GarageAgreement->table,
                        $this->Session->read('Auth'),
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'garages',
                            'action' => 'add_dis_and_net_garage',
                            $garage_id
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarAgreements($garage_id);
            $this->set(array(
                'cancel_action' => $cancelAction,
                'garage_id' => $garage_id,
                'reasons_leaving' => $reasonsLeaving
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit GarageAgreement.
     */
    public function edit($garage_agreement_id)
    {
        $garageAgreement = $this->GarageAgreement->findById($garage_agreement_id);

        if ($garageAgreement) {
            $garageId = $garageAgreement['GarageAgreement']['garage_id'];

            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');

            $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);
            $garageInternalNetworks = $this->GarageNetwork->findInternalByGarageAndAagRegionId($garageId, $aagRegionId);
            $garageExternalNetworks = $this->GarageNetwork->findExternalByGarageAndAagRegionId($garageId, $aagRegionId);

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
                $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) &&
                $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) &&
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::EDIT_AGREEMENTS)
            ) {
                $suppliers = $this->Supplier->find('list');

                if ($garageAgreement['GarageAgreement']['reason_leaving_id']) {
                    $reasons = $this->LeavingReasonType->getList();
                } elseif ($garageAgreement['GarageAgreement']['reason_hold_id']) {
                    $reasons = $this->HoldReasonType->getList();
                } else {
                    $reasons = null;
                }

                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'garages_agreements',
                        'action' => 'view',
                        $garage_agreement_id
                    ),
                );

                if (!$this->request->is('get')) {
                    $oldData = $this->GarageAgreement->getData($garageAgreement['GarageAgreement']['id']);
                    $garageAgreementBd = $this->GarageAgreement->edit_garage_agreement($this->request->data);
                    if ($garageAgreementBd) {
                        if ($oldData != $garageAgreementBd) {
                            $this->LogChange->get_params_create_log_edit(
                                $oldData['GarageAgreement'],
                                $garageAgreementBd['GarageAgreement'],
                                $this->GarageAgreement->table,
                                $this->Session->read('Auth'),
                                $garageAgreement['GarageAgreement']['garage_id'],
                                ConstantsLogType::GARAGE
                            );
                        }
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect(
                            array(
                                'controller' => 'garages',
                                'action' => 'add_dis_and_net_garage',
                                $garageAgreement['GarageAgreement']['garage_id']
                            )
                        );
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $garageAgreement['GarageAgreement']['contract_sent_date'] = Fecha::toFormatoVistaFecha($garageAgreement['GarageAgreement']['contract_sent_date']);
                    $garageAgreement['GarageAgreement']['contract_received_date'] = Fecha::toFormatoVistaFecha($garageAgreement['GarageAgreement']['contract_received_date']);
                    $garageAgreement['GarageAgreement']['contract_start_date'] = Fecha::toFormatoVistaFecha($garageAgreement['GarageAgreement']['contract_start_date']);
                    $garageAgreement['GarageAgreement']['contract_end_date'] = Fecha::toFormatoVistaFecha($garageAgreement['GarageAgreement']['contract_end_date']);
                    $garageAgreement['GarageAgreement']['date_on_hold'] = ($garageAgreement['GarageAgreement']['date_on_hold'] ? Fecha::toFormatoVistaFecha($garageAgreement['GarageAgreement']['date_on_hold']) : "");
                    $garageAgreement['GarageAgreement']['leaving_date'] = ($garageAgreement['GarageAgreement']['leaving_date'] ? Fecha::toFormatoVistaFecha($garageAgreement['GarageAgreement']['leaving_date']) : "");
                    $garageAgreement['GarageAgreement']['reason'] = ($garageAgreement['GarageAgreement']['reason_hold_id'] ? $garageAgreement['GarageAgreement']['reason_hold_id'] : $garageAgreement['GarageAgreement']['reason_leaving_id']);
                    $this->request->data = $garageAgreement;
                }

                $agreementStatuses = Configure::read('Network_Status');
                foreach ($agreementStatuses as $key => $agreementStatus) {
                    $agreementStatuses[$key] = __t($agreementStatus);
                }

                $agreements = $this->Agreement->find('list');
                $garageAgreements = $this->GarageAgreement->findByGarageId($garageAgreement['GarageAgreement']['garage_id']);

                foreach ($garageAgreements as $garageAgreementTmp) {
                    if ($garageAgreementTmp['agreement_id'] != $garageAgreement['GarageAgreement']['agreement_id']) {
                        unset($agreements[$garageAgreementTmp['agreement_id']]);
                    }
                }

                $this->set(array(
                    'cancel_action' => $cancelAction,
                    'garage_agreement' => $garageAgreement,
                    'garage_id' => $garageAgreement['GarageAgreement']['garage_id'],
                    'agreements' => $agreements,
                    'suppliers' => $suppliers,
                    'agreements_statuses' => $agreementStatuses,
                    'reasons' => $reasons
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarAgreements($garage_id = null)
    {
        if (isset($garage_id)) {
            $agreements = $this->Agreement->get_acuerdos_libres($garage_id);
        } else {
            $agreements = $this->Agreement->find('list', array('order' => 'Agreement.nombre'));
        }

        $garageAgreements = $this->GarageAgreement->find('all', array(
            'conditions' => array(
                'garage_id' => $garage_id
            )
        ));

        foreach ($garageAgreements as $garageAgreement) {
            if ($garageAgreement['GarageAgreement']['status'] != ConstantsNetworksStatus::UNSUBSCRIBE) {
                unset($agreements[$garageAgreement['GarageAgreement']['agreement_id']]);
            }
        }

        $agreementStatuses = Configure::read('Network_Status');
        foreach ($agreementStatuses as $key => $agreementStatus) {
            $agreementStatuses[$key] = __t($agreementStatus);
        }

        $this->set(array(
            'agreements' => $agreements,
            'agreements_statuses' => $agreementStatuses,
        ));
    }

    /**
     * AJAX get Reasons.
     */
    public function ajax_load_reasons()
    {
        $this->verify_ajax($this->request);

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
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_AGREEMENTS) ||
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::EDIT_AGREEMENTS)
            )
        ) {
            $this->autoRender = false;
            if ($this->request->data['status_id'] == ConstantsNetworksStatus::ON_HOLD) {
                return json_encode($this->HoldReasonType->find('list'));
            } elseif ($this->request->data['status_id'] == ConstantsNetworksStatus::LEFT) {
                return json_encode($this->LeavingReasonType->find('list'));
            } else {
                return false;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * API to create GarageAgreements from RepairMaintenance.
     */
    public function set_garages_agreement_from_rm()
    {
        $data = $this->request->input('json_decode');
        $data = json_decode(json_encode($data), true);
        $this->GarageAgreement->createGarageAgreementFromRM($data['garages'], $data['agreement']);

        $this->layout = $this->autoRender = false;
        return true;
    }
}
