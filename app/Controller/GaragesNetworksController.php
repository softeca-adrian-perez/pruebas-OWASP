<?php
App::uses('Leadgen', 'Lib');
App::uses('AzureBlob', 'Lib');

class GaragesNetworksController extends AppController
{
    public $uses = array(
        'GarageNetwork',
        'Garage',
        'Network',
        'NetworkStatus',
        'NetworkContractType',
        'TradingGroup',
        'TradingGroupNetwork',
        'Supplier',
        'LeavingReasonType',
        'LogChange',
        'HoldReasonType',
        'AnnexDetail',
        'RepairMaintenance',
        'Booking',
        'Work',
        'GarageNetworkWork',
        'VehicleType',
        'GarageVehicleType',
        'GarageNetworkGenart',
        'GarageNetworkWorkLabour',
        'Genart',
        'GarageNetworkImage',
        'Province',
        'Country',
        'GarageNetworkFluid',
        'Fluid',
        'GarageNetworkVehicleType',
        'Service',
        'GarageNetworkService',
        'ServiceDriver',
        'GarageNetworkServiceDriver',
        'Vehicle',
        'GarageNetworkVehicle',
        'GarageNetworkVehicleBlackList',
        'City',
        'GarageNetworkWorkPrice',
        'Language',
        'User',
        'ContactList',
        'Contact',
        'GarageNetworkContact',
        'Email',
        'NetworkContactList',
        'Software',
        'GarageSoftware',
        'GenartFamily',
        'GenartMaster',
        'GarageNetworkGenartFamily',
        'NetworkRecommended',
        'TrainingAllowance',
        'GarageNetworkGenartMaster',
        'TrainingCreditNetwork',
        'LeavingReasonComment'
    );

    /**
     * GarageNetwork view.
     */
    public function view($garage_network_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $garageNetwork = $this->GarageNetwork->getDatasById($garage_network_id);

        $garage = $this->Garage->findByIdAndAagRegionId($garageNetwork['GarageNetwork']['garage_id'], $aagRegionId);

        if ($garage && in_array($roleId, array(ConstantsRoles::SUPER_ADMIN, ConstantsRoles::ADMIN, ConstantsRoles::GARAGE))) {
            if ($garageNetwork['GarageNetwork']['reason_leaving_id']) {
                $reasons = $this->LeavingReasonType->getList();
            } elseif ($garageNetwork['GarageNetwork']['reason_hold_id']) {
                $reasons = $this->HoldReasonType->getList();
            } else {
                $reasons = null;
            }
            $suppliers = $this->Supplier->find('list');

            $tradingGroups = $this->TradingGroup->find('all');

            $this->setVarNetworks();
            $this->set(array(
                'garage_id' => $garageNetwork['GarageNetwork']['garage_id'],
                'suppliers' => $suppliers,
                'trading_groups' => $tradingGroups,
                'garage_network' => $garageNetwork,
                'reasons' => $reasons,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create GarageNetwork.
     */
    public function add($garage_id, $internal = null)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, $aagRegionId);

        if (
            $garage &&
            (
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
            )
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
                    'action' => 'add_dis_and_net_garage',
                    $garage_id
                ),
            );

            if (!$this->request->is('get')) {
                $oldNetwork = $this->GarageNetwork->findByNetworkIdAndGarageIdAndLast($this->request->data['GarageNetwork']['network_id'], $garage_id, ConstantsBooleans::YES);
                if ($oldNetwork) {
                    $this->GarageNetwork->setGarageNetworkNotActive($oldNetwork);
                }

                $garageNetwork = $this->GarageNetwork->addGarageNetwork($this->request->data, $garage_id);

                if ($garageNetwork) {
                    if (
                        in_array($garageNetwork['GarageNetwork']['network_id'], array(NETWORK_ID_AUTOCARE, NETWORK_ID_TOPTRUCK, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_GEXPERT)) &&
                        !empty($garageNetwork['GarageNetwork']['contract_received_date']) && $garageNetwork['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE
                    ) {
                        $garageNetworkId = $garageNetwork['GarageNetwork']['id'];
                        $trainingAllowanceTmp = array();
                        $trainingAllowanceTmp['TrainingAllowance']['garage_network_id'] = $garageNetworkId;

                        $startDate = $garageNetwork['GarageNetwork']['contract_received_date'];
                        $currentDate = date('Y-m-d');
                        while (strtotime($startDate) <= strtotime($currentDate)) {
                            $trainingAllowanceTmp['TrainingAllowance']['start_date'] = $startDate;
                            $trainingAllowanceTmp['TrainingAllowance']['end_date'] = date('Y-m-d', strtotime($startDate . ' +1 year -1 day'));

                            if ($trainingAllowanceTmp['TrainingAllowance']['start_date'] <= $currentDate && $trainingAllowanceTmp['TrainingAllowance']['end_date'] >= $currentDate) {
                                $trainingAllowanceTmp['TrainingAllowance']['is_actual'] = ConstantsBooleans::YES;
                            } else {
                                $trainingAllowanceTmp['TrainingAllowance']['is_actual'] = ConstantsBooleans::NO;
                            }

                            // if allowance doesn't exists, it's created
                            $existingTrainingAllowance = $this->TrainingAllowance->findByGarageNetworkIdAndStartDate($garageNetworkId, $startDate);
                            if (!$existingTrainingAllowance) {
                                $this->TrainingAllowance->add($trainingAllowanceTmp);
                            }

                            $startDate = date('Y-m-d', strtotime($startDate . ' +1 year'));
                        }
                    }

                    $network = $this->Network->findById($garageNetwork['GarageNetwork']['network_id']);
                    $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);

                    $this->Email->newEmailOnboarding($garage['Garage']['name'], $garage['Garage']['email'], $garage['Garage']['business_name'], $network, $garage['Garage']['language_id'], $country['Country']['id']);

                    $this->LogChange->get_params_create_log_add(
                        $garageNetwork['GarageNetwork'],
                        $this->GarageNetwork->table,
                        $this->Session->read('Auth'),
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );

                    //Send emails to contact list
                    $contacts_lists = $this->NetworkContactList->getContactListFromNetwork($garageNetwork['GarageNetwork']['network_id']);
                    if ($contacts_lists) {
                        foreach ($contacts_lists as $contact_list) {
                            $contact_list_data = $this->ContactList->getContactsByContactListAndUser($contact_list);
                            foreach ($contact_list_data as $contact) {
                                if ($this->request->data['GarageNetwork']['contract_start_date'] != '') {
                                    $modified_date['contract_start_date'] = $this->request->data['GarageNetwork']['contract_start_date'];
                                    $this->Email->newEmailNewDateGarageNetwork($contact['Contact']['email'], $garageNetwork, $modified_date);
                                }
                            }
                        }
                    }

                    // Send new network to RM
                    if ($garageNetwork['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                        $this->RepairMaintenance->actualizar_redes_taller($garageNetwork['GarageNetwork']['garage_id'], $garageNetwork['GarageNetwork']['network_id']);
                    }

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'garages_networks',
                            'action' => 'edit',
                            $this->GarageNetwork->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    if (isset($this->request->data['GarageNetwork']['network_id']) && !empty($this->request->data['GarageNetwork']['network_id'])) {
                        $this->set(array(
                            'trading_groups' => $this->TradingGroupNetwork->getListTradingGroupsByRegionAndNetwork($aagRegionId, $this->request->data['GarageNetwork']['network_id']),
                        ));
                    }
                }
            }

            $this->setVarNetworks($garage_id, $internal);
            $this->set(array(
                'cancel_action' => $cancelAction,
                'garage_id' => $garage_id,
                'country' => $country,
                'required_member_fees_fields' => (isset($garageNetwork['GarageNetwork']['contract_start_date']) && !empty(trim($garageNetwork['GarageNetwork']['contract_start_date']))) ? true : false,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit GarageNetwork.
     */
    public function edit($garageNetworkId)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

        if ($garageNetwork) {
            $garageId = $garageNetwork['GarageNetwork']['garage_id'];
            $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);
            $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);

            if (
                $garage &&
                (
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
                )
            ) {
                $networkData = $this->Network->findById($garageNetwork['GarageNetwork']['network_id']);
                $network_id = $networkData['Network']['id'];
                $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);
                $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE);

                $suppliers = $this->Supplier->find('list');
                if ($garage['Garage']['status'] == ConstantsGarageStatus::INACTIVE) {
                    $reasons = $this->LeavingReasonType->getListByRegion($aagRegionId);
                } elseif (
                    $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE
                    && $garageNetwork['GarageNetwork']['status'] == ConstantsNetworksStatus::LEFT
                ) {
                    $reasons = $this->LeavingReasonType->getListByRegionWithoutInactive($aagRegionId);
                } elseif ($garageNetwork['GarageNetwork']['reason_hold_id']) {
                    $reasons = $this->HoldReasonType->getListByRegion($aagRegionId);
                } else {
                    $reasons = null;
                }

                $networksAnnexDetails = $this->AnnexDetail->search_list_region($aagRegionId);

                $country = array();
                if (isset($garage['Garage']['province_id'])) {
                    $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
                } elseif (isset($garage['Garage']['city_id'])) {
                    $country = $this->Country->get_country_by_city($garage['Garage']['city_id']);
                }

                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'garages_networks',
                        'action' => 'view',
                        $garageNetworkId
                    ),
                );

                $commentsLeavingReasons = $this->LeavingReasonComment->findAllByGarageNetworkId($garageNetwork['GarageNetwork']['id']);

                if (!$this->request->is('get')) {
                    $oldData = $this->GarageNetwork->findById($garageNetwork['GarageNetwork']['id']);
                    if (isset($oldData['GarageNetwork']['kiyoh_api_key']) && isset($oldData['GarageNetwork']['location_id'])) {
                        $this->request->data['GarageNetwork']['location_id'] = $oldData['GarageNetwork']['location_id'];
                        $this->request->data['GarageNetwork']['kiyoh_api_key'] = $oldData['GarageNetwork']['kiyoh_api_key'];
                    }

                    //If Garage Network status is set to live, Garage Network shouldn't have any leaving reason set
                    if ($this->request->data['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                        $this->request->data['GarageNetwork']['reason_leaving_id'] = null;
                    }

                    $garageNetworkBd = $this->GarageNetwork->editGarageNetwork($this->request->data);
                    if ($garageNetworkBd) {
                        $contacts_lists = $this->NetworkContactList->getContactListFromNetwork($garageNetwork['GarageNetwork']['network_id']);
                        if ($contacts_lists) {
                            $start_date = false;
                            $end_contract = false;
                            $modified_date = null;
                            foreach ($contacts_lists as $contact_list) {
                                $contact_list_data = $this->ContactList->getContactsByContactListAndUser($contact_list);
                                foreach ($contact_list_data as $contact) {
                                    if ((date("d-m-Y", strtotime($oldData['GarageNetwork']['contract_start_date'])) != $this->request->data['GarageNetwork']['contract_start_date'] || !isset($oldData['GarageNetwork']['contract_start_date'])) && $this->request->data['GarageNetwork']['contract_start_date'] != '') {
                                        $start_date = true;
                                        $modified_date['contract_start_date'] = $this->request->data['GarageNetwork']['contract_start_date'];
                                    }
                                    if ((date("d-m-Y", strtotime($oldData['GarageNetwork']['contract_end_date'])) != $this->request->data['GarageNetwork']['contract_end_date'] || !isset($oldData['GarageNetwork']['contract_end_date'])) && $this->request->data['GarageNetwork']['contract_end_date'] != '') {
                                        $end_contract = true;
                                        $modified_date['contract_end_date'] = $this->request->data['GarageNetwork']['contract_end_date'];
                                    }
                                    if ($start_date || $end_contract) {
                                        $this->Email->newEmailNewDateGarageNetwork($contact['Contact']['email'], $this->request->data, $modified_date, $start_date, $end_contract);
                                    }
                                }
                            }
                        }
                        $oldYear = $oldData['GarageNetwork']['contract_received_date'];
                        $newYear = $garageNetworkBd['GarageNetwork']['contract_received_date'];
                        if (
                            in_array($garageNetworkBd['GarageNetwork']['network_id'], array(NETWORK_ID_AUTOCARE, NETWORK_ID_TOPTRUCK, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_GEXPERT)) &&
                            !empty($garageNetworkBd['GarageNetwork']['contract_received_date']) && $garageNetworkBd['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE && $oldYear != $newYear
                        ) {
                            $trainingAllowanceTmp = array();
                            $trainingAllowanceTmp['TrainingAllowance']['garage_network_id'] = $garageNetworkBd['GarageNetwork']['id'];

                            $allowances_list = $this->TrainingAllowance->getListAllowanceFromGarageNetworkId($garageNetworkBd['GarageNetwork']['id']);
                            if ($allowances_list) {
                                $already_processed_short_allowance = false;

                                foreach ($allowances_list as $allowance) {
                                    $credits_networks = $this->TrainingCreditNetwork->findAllByTrainingAllowanceId($allowance['TrainingAllowance']['id']);

                                    if ($credits_networks) {
                                        foreach ($credits_networks as $credit_network) {
                                            if ($credit_network) {
                                                $this->TrainingCreditNetwork->editAllowance($credit_network);
                                            }
                                        }
                                    }

                                    if ($allowance['TrainingAllowance']['start_date'] < $newYear) {
                                        if (!$already_processed_short_allowance && ((strtotime($newYear) - strtotime($allowance['TrainingAllowance']['start_date'])) < (365 * 24 * 60 * 60))) {
                                            $allowance['TrainingAllowance']['end_date'] = date('Y-m-d', strtotime($newYear . ' -1 day'));
                                            $this->TrainingAllowance->edit($allowance);

                                            $already_processed_short_allowance = true;
                                        }
                                    } else {
                                        $this->TrainingAllowance->delete($allowance['TrainingAllowance']['id']);
                                    }
                                }
                            }

                            $startDate = $garageNetworkBd['GarageNetwork']['contract_received_date'];
                            $lastDate = $this->TrainingCreditNetwork->findLatestDate($garageNetworkId);
                            $creditsHistoryPc = $this->TrainingCreditNetwork->findAllNullByGarageNetworkIdPlannedCourse($trainingAllowanceTmp['TrainingAllowance']['garage_network_id']);
                            $creditsHistory = $this->TrainingCreditNetwork->findAllNullByGarageNetworkId($trainingAllowanceTmp['TrainingAllowance']['garage_network_id']);

                            if ($lastDate == null || (isset($lastDate) && date('Y', strtotime($lastDate)) < date('Y'))) {
                                $lastDate = date('Y-m-d');
                            }
                            while (!empty($startDate) && strtotime($startDate) <= strtotime($lastDate)) {
                                $trainingAllowanceTmp['TrainingAllowance']['start_date'] = $startDate;
                                $trainingAllowanceTmp['TrainingAllowance']['end_date'] = date('Y-m-d', strtotime($startDate . ' +1 year -1 day'));

                                if ($trainingAllowanceTmp['TrainingAllowance']['start_date'] <= date('Y-m-d') && $trainingAllowanceTmp['TrainingAllowance']['end_date'] >= date('Y-m-d')) {
                                    $trainingAllowanceTmp['TrainingAllowance']['is_actual'] = ConstantsBooleans::YES;
                                } else {
                                    $trainingAllowanceTmp['TrainingAllowance']['is_actual'] = ConstantsBooleans::NO;
                                }

                                // if allowance doesn't exists, it's created
                                $existingTrainingAllowance = $this->TrainingAllowance->findByGarageNetworkIdAndStartDate($garageNetworkId, $startDate);
                                if (!$existingTrainingAllowance) {
                                    $this->TrainingAllowance->add($trainingAllowanceTmp);
                                }

                                $startDate = date('Y-m-d', strtotime($startDate . ' +1 year'));
                            }

                            $allowances_list_tmp = $this->TrainingAllowance->getListAllowanceFromGarageNetworkId($garageNetworkBd['GarageNetwork']['id']);
                            foreach ($allowances_list_tmp as $allowance_tmp) {
                                foreach ($creditsHistoryPc as $credit_history_pc) {
                                    $plannedCourseStartDate = $credit_history_pc['TrainingPlannedCourse']['date_from'];
                                    if ($plannedCourseStartDate >= $allowance_tmp['TrainingAllowance']['start_date'] && $plannedCourseStartDate <= $allowance_tmp['TrainingAllowance']['end_date']) {
                                        $this->TrainingCreditNetwork->editNewAllowance($credit_history_pc, $allowance_tmp['TrainingAllowance']['id']);
                                    }
                                }

                                foreach ($creditsHistory as $credit_history) {
                                    $historyStartDate = date('Y-m-d', strtotime($credit_history['TrainingCreditNetwork']['creation_date']));
                                    if ($historyStartDate >= $allowance_tmp['TrainingAllowance']['start_date'] && $historyStartDate <= $allowance_tmp['TrainingAllowance']['end_date']) {
                                        $this->TrainingCreditNetwork->editNewAllowance($credit_history, $allowance_tmp['TrainingAllowance']['id']);
                                    }
                                }
                            }
                        }
                        if ($oldData != $garageNetworkBd) {
                            $this->LogChange->get_params_create_log_edit(
                                $oldData['GarageNetwork'],
                                $garageNetworkBd['GarageNetwork'],
                                $this->GarageNetwork->table,
                                $this->Session->read('Auth'),
                                $garageNetwork['GarageNetwork']['garage_id'],
                                ConstantsLogType::GARAGE
                            );
                        }

                        if (!empty($this->request->data['LeavingReasonComment']['leaving_comment']) && $this->request->data['GarageNetwork']['status'] == ConstantsNetworksStatus::LEFT) {
                            $this->LeavingReasonComment->add($garageNetworkBd['GarageNetwork']['id'], $this->request->data['LeavingReasonComment']['leaving_comment']);
                        }

                        // Update network to RM
                        if ($garageNetworkBd['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                            $this->RepairMaintenance->actualizar_redes_taller($garageNetwork['GarageNetwork']['garage_id'], $garageNetwork['GarageNetwork']['network_id']);
                        } else {
                            $this->RepairMaintenance->actualizar_redes_taller($garageNetwork['GarageNetwork']['garage_id'], $garageNetwork['GarageNetwork']['network_id'], true);
                        }

                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $garageNetwork['GarageNetwork']['contract_sent_date'] = Fecha::toFormatoVistaFecha($garageNetwork['GarageNetwork']['contract_sent_date']);
                    $garageNetwork['GarageNetwork']['contract_received_date'] = Fecha::toFormatoVistaFecha($garageNetwork['GarageNetwork']['contract_received_date']);
                    $garageNetwork['GarageNetwork']['contract_start_date'] = Fecha::toFormatoVistaFecha($garageNetwork['GarageNetwork']['contract_start_date']);
                    $garageNetwork['GarageNetwork']['contract_end_date'] = Fecha::toFormatoVistaFecha($garageNetwork['GarageNetwork']['contract_end_date']);
                    $garageNetwork['GarageNetwork']['date_on_hold'] = $garageNetwork['GarageNetwork']['date_on_hold'] ? Fecha::toFormatoVistaFecha($garageNetwork['GarageNetwork']['date_on_hold']) : "";
                    $garageNetwork['GarageNetwork']['leaving_date'] = $garageNetwork['GarageNetwork']['leaving_date'] ? Fecha::toFormatoVistaFecha($garageNetwork['GarageNetwork']['leaving_date']) : "";
                    $this->request->data = $garageNetwork;
                }

                $networksStatuses = Configure::read('Network_Status');
                foreach ($networksStatuses as $key => $networkStatus) {
                    $networksStatuses[$key] = __t($networkStatus);
                }
                $networks = $this->Network->find('list');
                $tradingGroupsTmp = $this->TradingGroupNetwork->getTradingGroupsByRegionAndNetwork($aagRegionId, $network_id);

                $tradingGroups = array();
                foreach ($tradingGroupsTmp as $tradingGroupTmp) {
                    $tg = $this->TradingGroup->findById($tradingGroupTmp['TradingGroupNetwork']['trading_group_id']);
                    if (isset($tg['TradingGroup'])) {
                        $name = $tg['TradingGroup']['name'];
                        $tradingGroups[$tradingGroupTmp['TradingGroupNetwork']['trading_group_id']] = $name;
                    }
                }
                $garageNetworks = $this->GarageNetwork->findByGarageId($garageId);
                $networksContracts = $this->NetworkContractType->search_list();

                foreach ($garageNetworks as $garageNetworkTmp) {
                    if ($garageNetworkTmp['network_id'] != $garageNetwork['GarageNetwork']['network_id']) {
                        unset($networks[$garageNetworkTmp['network_id']]);
                    }
                }

                $reviewsInfo = isset($garageNetwork['GarageNetwork']['reviews_info']) ? json_decode($garageNetwork['GarageNetwork']['reviews_info']) : null;
                $averageRating = $garageNetwork['GarageNetwork']['rating'];
                $reviewsNumber = $garageNetwork['GarageNetwork']['reviews_number'];

                // only if Garage was active and GarageNetwork was live garage access is shown
                $showGarageAccess = $garage['Garage']['status'] == ConstantsGarageStatus::ACTIVE && $garageNetwork['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE;

                $inactiveLeavingReason = $this->LeavingReasonType->getInactiveIdByRegion($aagRegionId);
                $this->set(array(
                    'cancel_action' => $cancelAction,
                    'garage_network' => $garageNetwork,
                    'garage_id' => $garageId,
                    'garage' => $garage,
                    'networks' => $networks,
                    'suppliers' => $suppliers,
                    'trading_groups' => $tradingGroups,
                    'networks_statuses' => $networksStatuses,
                    'networks_contracts' => $networksContracts,
                    'reasons' => $reasons,
                    'networks_annex_details' => $networksAnnexDetails,
                    'review_data' => $reviewsInfo,
                    'show_garage_access' => $showGarageAccess,
                    'contact_list_selected' => $this->Contact->getContactFromGarageNetwork($garageNetworkId),
                    'country' => $country,
                    'required_member_fees_fields' => (isset($garageNetwork['GarageNetwork']['contract_start_date']) && !empty(trim($garageNetwork['GarageNetwork']['contract_start_date']))) ? true : false,
                    'comments_leaving_reasons' => $commentsLeavingReasons,
                    'inactive_leaving_reason' => json_encode($inactiveLeavingReason['LeavingReasonType']['name_' . __l()]),
                ));
                $this->set('average_rating', $averageRating);
                $this->set('total_reviews', $reviewsNumber);
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarNetworks($garage_id = null, $internal = null)
    {
        $suppliers = $this->Supplier->find('list');

        $user = $this->Acceso->user();
        $user_info = $this->User->findById($user['id']);
        $aagRegionId = $user_info['User']['aag_region_id'];

        if (isset($garage_id)) {
            if ($internal) {
                $networks = $this->Network->findInternal($aagRegionId);
            } else {
                $networks = $this->Network->findExternal($aagRegionId);
            }
        } else {
            $networks = $this->Network->find('list', array(
                'order' => 'Network.name',
                'conditions' => array('aag_region_id' => $aagRegionId)
            ));
        }

        $garage_networks = $this->GarageNetwork->find('all', array(
            'conditions' => array(
                'garage_id' => $garage_id
            )
        ));

        foreach ($garage_networks as $garage_network) {
            if ($garage_network['GarageNetwork']['status'] != ConstantsNetworksStatus::UNSUBSCRIBE) {
                unset($networks[$garage_network['GarageNetwork']['network_id']]);
            }
        }

        $networks_contracts = $this->NetworkContractType->search_list();
        $networks_statuses = Configure::read('Network_Status');
        foreach ($networks_statuses as $key => $network_status) {
            $networks_statuses[$key] = __t($network_status);
        }

        $networks_annex_details = $this->AnnexDetail->search_list_region($aagRegionId);

        $this->set(array(
            'networks' => $networks,
            'suppliers' => $suppliers,
            'networks_statuses' => $networks_statuses,
            'networks_annex_details' => $networks_annex_details,
            'networks_contracts' => $networks_contracts,
        ));
    }

    /**
     * AJAX load trading groups.
     */
    public function ajax_load_trading_groups($network_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            (
                !in_array($roleId, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                (
                    $roleId == ConstantsRoles::DISTRIBUTOR &&
                    $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                )
            )
        ) {
            $tradingGroupsTmp = $this->TradingGroupNetwork->getTradingGroupsByRegionAndNetwork($aagRegionId, $network_id);
            $tradingGroups = array();
            foreach ($tradingGroupsTmp as $tradingGroupTmp) {
                $tg = $this->TradingGroup->findByIdAndAagRegionId($tradingGroupTmp['TradingGroupNetwork']['trading_group_id'], $aagRegionId);
                if (isset($tg['TradingGroup'])) {
                    $name = $tg['TradingGroup']['name'];
                    $tradingGroups[$tradingGroupTmp['TradingGroupNetwork']['trading_group_id']] = $name;
                }
            }

            $this->set(array(
                'trading_groups' => $tradingGroups,
            ));
            $this->layout = null;
            $this->render('../GaragesNetworks/Elements/ajax_load_trading_groups');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX load reasons.
     */
    public function ajax_load_reasons()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            (
                !in_array($roleId, array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                (
                    $roleId == ConstantsRoles::DISTRIBUTOR &&
                    $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                )
            )
        ) {
            $this->autoRender = false;
            if ($this->request->data['status_id'] == ConstantsNetworksStatus::ON_HOLD) {
                return json_encode($this->HoldReasonType->getListByRegion($aagRegionId));
            } elseif ($this->request->data['status_id'] == ConstantsNetworksStatus::LEFT) {
                return json_encode($this->LeavingReasonType->getListByRegionWithoutInactive($aagRegionId));
            } else {
                return false;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Access to Garage network configuration.
     */
    public function network_dashboard($garageNetworkId)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetwork = $this->GarageNetwork->findByIdAndStatus($garageNetworkId, ConstantsNetworksStatus::LIVE);

        if (!$garageNetwork) {
            throw new UnauthorizedException();
        }

        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionIdAndStatus($garageId, $aagRegionId, ConstantsGarageStatus::ACTIVE);

        if (!$garage) {
            throw new UnauthorizedException();
        }

        $networkId = $garageNetwork['GarageNetwork']['network_id'];

        CakeSession::write('Auth.User.current_network', $networkId); // T001 SECURITY - It is not changed

        $this->Acceso->checkGarageAccess($garageId);

        return $this->redirect("/garages_networks/configuration/" . $garageNetworkId);
    }

    /**
     * Garage network fluids pricing setup.
     */
    public function fluids($garageNetworkId)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

        if (!$garageNetwork) {
            throw new UnauthorizedException();
        }

        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

        if (!$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $networkId = $garageNetwork['GarageNetwork']['network_id'];
        $languageCode = self::getLanguageCodeWhenNotTranslatedWithLoco($networkId);

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'garages_network',
                'action' => 'view',
                $garageNetworkId
            ),
        );

        $network = $this->Network->find('first', array(
            'conditions' => array('id' => $networkId),
            'fields' => array('with_vat')
        ));
        $withVat = $network['Network']['with_vat'];

        // fluids that doesn't have a parent code (parent fluids)
        $parentFluids = $this->Fluid->find('all', array(
            'conditions' => array(
                'network_id' => $networkId,
                'parent_code' => null,
                'has_advanced_settings' => ConstantsBooleans::NO_ACTIVE,
                'active' => ConstantsBooleans::ACTIVE
            ),
            'order' => 'name_' . $languageCode . ' asc',
            'contain' => array('GarageNetworkFluid' => array(
                'conditions' => array('GarageNetworkFluid.garage_network_id' => $garageNetworkId)
            ))
        ));

        $workDealer = $this->Work->find('first', array(
            'conditions' => array(
                'network_id' => $networkId,
                'grouping_genart_id' => null,
                'active' => ConstantsBooleans::ACTIVE,
            ),
        ));


        // If dealer, show all except fluids with advanced settings
        // Delete fluid without grouping associated -> if there is no dealer job
        if (!$workDealer) {
            foreach ($parentFluids as $key => $parentFluid) {
                if (!$this->Genart->hasGenartGroupingAssociated(ConstantsTypesGenartsLeadGen::FLUID, $parentFluid['Fluid']['code'])) {
                    unset($parentFluids[$key]);
                }
            }
        }

        $fluids = $parentFluids;
        foreach ($parentFluids as &$parentFluid) {
            $fluidCode = $parentFluid['Fluid']['code'];

            if (empty($parentFluid['GarageNetworkFluid'])) {
                $parentFluid['Fluid']['garage_price'] = null;
            } else {
                $parentFluid['Fluid']['garage_price'] = $parentFluid['GarageNetworkFluid'][0]['price'];
            }

            // fluids with parent code (child fluids)
            $childFluids = $this->Fluid->find('all', array(
                'conditions' => array(
                    'network_id' => $networkId,
                    'parent_code' => $fluidCode,
                    'active' => 1
                ),
                'order' => 'name_' . $languageCode . ' asc',
                'contain' => array('GarageNetworkFluid' => array(
                    'conditions' => array('GarageNetworkFluid.garage_network_id' => $garageNetworkId)
                ))
            ));

            foreach ($childFluids as $childFluid) {
                $garagePrice = !empty($childFluid['GarageNetworkFluid']) ?
                    $childFluid['GarageNetworkFluid'][0]['price'] : null;

                $parentFluid['Fluid']['child_fluids'][] = array(
                    'id' => $childFluid['Fluid']['id'],
                    'name_en' => $childFluid['Fluid']['name_en'],
                    'name_fr' => $childFluid['Fluid']['name_fr'],
                    'name_de' => $childFluid['Fluid']['name_de'],
                    'name_nl' => $childFluid['Fluid']['name_nl'],
                    'name_es' => $childFluid['Fluid']['name_es'],
                    'price' => $childFluid['Fluid']['price'],
                    'garage_price' => $garagePrice,
                );
                $fluids[] = $childFluid;
            }
        }

        $this->set(array(
            "cancel_action" => $cancelAction,
            "fluids" => $parentFluids,
            "garage_network_id" => $garageNetworkId,
            "with_vat" => $withVat,
            "garage_id" => $garageId,
            'garage_name' => $garage['Garage']['name'],
            'network_id' => $networkId,
            'language_code' => $languageCode,
            'quoting_views_active' => $garageNetwork['GarageNetwork']['quoting_views_active'],
            'networkUseFluids' => isset($fluids) && !empty($fluids) ? true : false
        ));

        if (!$this->request->is('get')) {
            $errorSaving = false;
            $fluidPrices = $this->request->data['fluid_price'];

            foreach ($fluids as $fluid) {
                $fluidId = $fluid['Fluid']['id'];

                $garageNetworkFluid = $this->GarageNetworkFluid->find('first', array(
                    "conditions" => array(
                        "garage_network_id" => $garageNetworkId,
                        "fluid_id" => $fluidId
                    )
                ));

                if ($fluidPrices[$fluidId] != '') {
                    if (empty($garageNetworkFluid)) {
                        $this->GarageNetworkFluid->create();
                    }
                    $garageNetworkFluid['GarageNetworkFluid']['garage_network_id'] = $garageNetworkId;
                    $garageNetworkFluid['GarageNetworkFluid']['fluid_id'] = $fluidId;
                    $garageNetworkFluid['GarageNetworkFluid']['price'] = $fluidPrices[$fluidId];

                    if (!$this->GarageNetworkFluid->save($garageNetworkFluid)) {
                        $this->Session->setFlashError(__t('Validation.Update_error'));
                        $errorSaving = true;
                        break;
                    }
                } elseif ($garageNetworkFluid != null && isset($garageNetworkFluid['GarageNetworkFluid']['id'])) {
                    if (!$this->GarageNetworkFluid->delete($garageNetworkFluid['GarageNetworkFluid']['id'])) {
                        $errorSaving = true;
                        break;
                    }
                }
            }

            if (!$errorSaving) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));

                $this->redirect(
                    array(
                        'controller' => 'garages_networks',
                        'action' => 'fluids',
                        $garageNetwork['GarageNetwork']['id']
                    )
                );
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
            }
        }
    }

    /**
     * View that allows to edit the prices of the fluids of a garage network.
     * Permissions checked in fluids function.
     *
     * @param garageNetworkId   Garage Network ID
     */
    public function edit_fluids($garageNetworkId)
    {
        $this->fluids($garageNetworkId);

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'garages_networks',
                'action' => 'view',
                $garageNetworkId
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
            'edit_fluids' => true
        ));

        $this->render('/GaragesNetworks/fluids');
    }

    /**
     * API to get information about the garage.
     *
     * @param network_id Received from POST request
     * @param garage_id Received from POST request
     *
     * @return JSON
     */
    public function garage_card()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {

            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $garageId = isset($dataReceived->garage_id) ? $dataReceived->garage_id : null;

            //AGN sends us a guid as the garage_id, so we turn it into the id
            if ($garageId) {
                $foundGarage = $this->Garage->findByGuid($garageId, ['id']);
                if (isset($foundGarage['Garage']['id'])) {
                    $garageId = $foundGarage['Garage']['id'];
                } else {
                    $garageId = null;
                }
            }

            $sendData = array();

            if (empty($networkId) || empty($garageId)) {
                return $this->returnJsonResult($sendData);
            }

            $garage = $this->Garage->find("first", array(
                "conditions" => array(
                    "Garage.id" => $garageId
                ),
                "contain" => array("GarageNetwork" => array(
                    "conditions" => array(
                        "GarageNetwork.network_id" => $networkId
                    ),
                ))
            ));

            if (!$garage) {
                return $this->returnJsonResult($sendData);
            }

            $network = $this->Network->findById($networkId);

            $province = $this->Province->find("first", array(
                "conditions" => array("id" => $garage["Garage"]["province_id"])
            ));

            $provinceName = $province ? $province["Province"]["name"] : '';

            $sendData = array(
                "garage_name" => $garage["Garage"]["name"],
                "business_name" => $garage["Garage"]["business_name"],
                "about_garage" => $garage["GarageNetwork"]["about"],
                "address1" => $garage["Garage"]["address1"],
                "address2" => $garage["Garage"]["address2"],
                "address3" => $garage["Garage"]["address3"],
                "address4" => $garage["Garage"]["address4"],
                "town" => $garage["Garage"]["town"],
                "province" => $provinceName,
                "postcode" => $garage["Garage"]["postcode"],
                "phone" => $this->getPhoneNumberForPws($network, $garage["Garage"]["phone"])
            );

            $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
            foreach ($days as $day) {
                $sendData["opening_time"][$day][] = $garage["Garage"][$day . "_open_1"];
                $sendData["opening_time"][$day][] = $garage["Garage"][$day . "_closed_1"];
                $sendData["opening_time"][$day][] = $garage["Garage"][$day . "_open_2"];
                $sendData["opening_time"][$day][] = $garage["Garage"][$day . "_closed_2"];
            }

            return $this->returnJsonResult($sendData);
        }
    }

    /**
     * API to get the days and hours garages can take appoinments on, from one date to another.
     *
     * @param network_id Received by POST request
     * @param garage_id Received by POST request, it is the garage guid
     * @param date_from Received by POST request
     * @param date_to Received by POST
     *
     * @return JSON
     */
    public function garage_dates()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {
            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $garageId = isset($dataReceived->garage_id) ? $dataReceived->garage_id : null;
            //AGN sends us a guid as the garage_id, so we turn it into the id
            if ($garageId) {
                $foundGarage = $this->Garage->findByGuid($garageId, ['id']);
                if (isset($foundGarage['Garage']['id'])) {
                    $garageId = $foundGarage['Garage']['id'];
                } else {
                    $garageId = null;
                }
            }

            $dateFrom = isset($dataReceived->date_from) ? new DateTime($dataReceived->date_from) : null;
            $dateTo = isset($dataReceived->date_to) ? new DateTime($dataReceived->date_to) : null;

            $sendData = array('days' => array());

            if (empty($networkId) || empty($garageId) || empty($dateFrom) || empty($dateTo)) {
                return $this->returnJsonResult($sendData);
            }

            $today = new DateTime(date('Y-m-d'));

            // if "date to" is earlier than today date
            if ($today > $dateTo) {
                return $this->returnJsonResult($sendData);
            }

            $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkId($garageId, $networkId);

            if (!$garageNetwork) {
                return $this->returnJsonResult($sendData);
            }

            // add to the actual date the days from which it can be reserved
            $daysMinFrom = $garageNetwork['GarageNetwork']['booking_days_min_from'];
            $todayTmp = clone $today;
            $dateMinFrom = $todayTmp->add(new DateInterval('P' . $daysMinFrom . 'D'));

            // if the min date from is greater than or equal to the specified date from
            if ($dateMinFrom >= $dateFrom) {
                $dateFrom = $dateMinFrom;
            }

            // add to the actual date the days up to which the reservation can be made
            $daysMaxTo = $garageNetwork['GarageNetwork']['booking_days_max_to'];
            $todayTmp2 = clone $today;
            $dateMaxTo = $todayTmp2->add(new DateInterval('P' . $daysMaxTo . 'D'));

            // if the specified date to is greater than the max date to
            if ($dateTo > $dateMaxTo) {
                $dateTo = $dateMaxTo;
            }

            $garage = $this->Garage->find("first", array(
                "conditions" => array(
                    "Garage.id" => $garageId
                ),
                "contain" => array("GarageNetwork" => array(
                    "conditions" => array(
                        "GarageNetwork.network_id" => $networkId
                    ),
                ))
            ));

            if (!$garage) {
                return $this->returnJsonResult($sendData);
            }

            $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
            $daysHours = array();
            $fromHourMorning = 0;
            $toHourMorning = 0;
            $fromHourAfternoon = 0;
            $toHourAfternoon = 0;

            // store the opening and closing times for each day and each turn
            foreach ($days as $day) {
                //Define opening hours and planner times slots
                $dayOpen1 = $garage["Garage"][$day . "_open_1"];
                $dayPlannerOpen1 = $garage["GarageNetwork"][$day . "_planner_open_1"];

                $dayClosed1 = $garage["Garage"][$day . "_closed_1"];
                $dayPlannerClosed1 = $garage["GarageNetwork"][$day . "_planner_closed_1"];

                $dayOpen2 = $garage["Garage"][$day . "_open_2"];
                $dayPlannerOpen2 = $garage["GarageNetwork"][$day . "_planner_open_2"];

                $dayClosed2 = $garage["Garage"][$day . "_closed_2"];
                $dayPlannerClosed2 = $garage["GarageNetwork"][$day . "_planner_closed_2"];

                //Different scenarios for a garage weekly schedule

                //No opening hour slots
                //1 or 2 opening hours slots, but no planner time slots
                if ((empty($dayOpen1) && empty($dayOpen2)) ||
                    ((!empty($dayOpen1) || !empty($dayOpen2)) && (empty($dayPlannerOpen1) && empty($dayPlannerOpen2)))
                ) {
                    $fromHourMorning = null;
                    $toHourMorning = null;
                    $fromHourAfternoon = null;
                    $toHourAfternoon = null;
                }

                //1 opening slot and 1 planner slot
                elseif ((!empty($dayOpen1) && empty($dayOpen2)) && (!empty($dayPlannerOpen1) && empty($dayPlannerOpen2))) {
                    $fromHourMorning = $dayOpen1 > $dayPlannerOpen1
                        ? $dayOpen1 : $dayPlannerOpen1;

                    $toHourMorning = $dayClosed1 < $dayPlannerClosed1
                        ? $dayClosed1 : $dayPlannerClosed1;

                    $fromHourAfternoon = null;
                    $toHourAfternoon = null;
                }

                //1 opening slot and 2 planner slots
                elseif ((!empty($dayOpen1) && empty($dayOpen2)) && (!empty($dayPlannerOpen1) && !empty($dayPlannerOpen2))) {
                    $fromHourMorning = $dayOpen1 > $dayPlannerOpen1
                        ? $dayOpen1 : $dayPlannerOpen1;
                    $toHourMorning = $dayClosed1 < $dayPlannerClosed1
                        ? $dayClosed1 : $dayPlannerClosed1;
                    $fromHourAfternoon = $dayOpen1 > $dayPlannerOpen2
                        ? $dayOpen1 : $dayPlannerOpen2;
                    $toHourAfternoon = $dayClosed1 < $dayPlannerClosed2
                        ? $dayClosed1 : $dayPlannerClosed2;
                }

                //2 Opening slots and 1 planner slots
                elseif ((!empty($dayOpen1) && !empty($dayOpen2)) && (!empty($dayPlannerOpen1) && empty($dayPlannerOpen2))) {
                    $fromHourMorning = $dayOpen1 > $dayPlannerOpen1
                        ? $dayOpen1 : $dayPlannerOpen1;
                    $toHourMorning = $dayClosed1 < $dayPlannerClosed1
                        ? $dayClosed1 : $dayPlannerClosed1;
                    $fromHourAfternoon = $dayPlannerClosed1 > $dayOpen2
                        ? $dayOpen2 : $dayPlannerClosed1;
                    $toHourAfternoon = $dayPlannerClosed1 > $dayClosed2
                        ? $dayClosed2 : $dayPlannerClosed1;
                }

                //Opening hours 2 slots and 2 planner slots
                elseif ((!empty($dayOpen1) && !empty($dayOpen2)) && (!empty($dayPlannerOpen1) && !empty($dayPlannerOpen2))) {
                    //1 planner slot in each opening slot
                    $fromHourMorning = $dayOpen1 > $dayPlannerOpen1
                        ? $dayOpen1 : $dayPlannerOpen1;
                    $fromHourAfternoon = $dayOpen2 > $dayPlannerOpen2
                        ? $dayOpen2 : $dayPlannerOpen2;
                    $toHourMorning = $dayClosed1 < $dayPlannerClosed1
                        ? $dayClosed1 : $dayPlannerClosed1;
                    $toHourAfternoon = $dayClosed2 < $dayPlannerClosed2
                        ? $dayClosed2 : $dayPlannerClosed2;

                    //2 planner slots in first opening slot
                    //2 planer slots in second opening slot
                    if (($dayOpen1 <= $dayPlannerOpen1 && $dayPlannerClosed2 <= $dayClosed1) ||
                        ($dayOpen2 <= $dayPlannerOpen1 && $dayPlannerClosed2 <= $dayClosed2)
                    ) {
                        $fromHourMorning = $dayPlannerOpen1;
                        $toHourMorning = $dayPlannerClosed1;
                        $fromHourAfternoon = $dayPlannerOpen2;
                        $toHourAfternoon = $dayPlannerClosed2;
                    }
                }

                $maxBookingsMorning = empty($garage["GarageNetwork"][$day . "_planner_max_1"])
                    ? 0 : $garage["GarageNetwork"][$day . "_planner_max_1"];
                $maxBookingsAfternoon = empty($garage["GarageNetwork"][$day . "_planner_max_2"])
                    ? 0 : $garage["GarageNetwork"][$day . "_planner_max_2"];

                $daysHours[$day] = array(
                    "fromMorning" => ($fromHourMorning < $toHourMorning) ? $fromHourMorning : '',
                    "toMorning" => ($fromHourMorning < $toHourMorning) ? $toHourMorning : '',
                    "fromAfternoon" => ($fromHourAfternoon < $toHourAfternoon) ? $fromHourAfternoon : '',
                    "toAfternoon" => ($fromHourAfternoon < $toHourAfternoon) ? $toHourAfternoon : '',
                    "maxBookingsMorning" => $maxBookingsMorning,
                    "maxBookingsAfternoon" => $maxBookingsAfternoon
                );
            }

            $bookingsGarage = $this->Booking->find("all", array(
                "conditions" => array(
                    "garage_id" => $garageId,
                    "network_id" => $networkId,
                    "date BETWEEN ? AND ? " => array($dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d'))
                )
            ));

            // get the number of days
            $numberDaysShow = ($dateFrom > $dateTo) ? -1 : $dateTo->diff($dateFrom)->days;

            $date = clone $dateFrom;
            for ($i = 0; $i <= $numberDaysShow; $i++) {
                // get the name of the day
                $currentDay = strtolower($date->format('l'));

                $numBookingsMorning = 0;
                $numBookingsAfternoon = 0;

                foreach ($bookingsGarage as $booking) {
                    if ($booking['Booking']['date'] == $date->format('Y-m-d')) {
                        if (
                            !empty($daysHours[$currentDay]['fromMorning'])
                            && !empty($daysHours[$currentDay]['toMorning'])
                            && $booking['Booking']['time_to'] > ($daysHours[$currentDay]['fromMorning'] . ':00')
                            && $booking['Booking']['time'] < ($daysHours[$currentDay]['toMorning'] . ':00')
                        ) {
                            $numBookingsMorning++;
                        }

                        if (
                            !empty($daysHours[$currentDay]['fromAfternoon'])
                            && !empty($daysHours[$currentDay]['toAfternoon'])
                            && $booking['Booking']['time_to'] > ($daysHours[$currentDay]['fromAfternoon'] . ':00')
                            && $booking['Booking']['time'] < ($daysHours[$currentDay]['toAfternoon'] . ':00')
                        ) {
                            $numBookingsAfternoon++;
                        }
                    }
                }

                $daysHoursCurrentDayFromMorning = $daysHours[$currentDay]['fromMorning'];
                $daysHoursCurrentDayToMorning = $daysHours[$currentDay]['toMorning'];
                $daysHoursCurrentDayFromAfternoon = $daysHours[$currentDay]['fromAfternoon'];
                $daysHoursCurrentDayToAfternoon = $daysHours[$currentDay]['toAfternoon'];

                if (
                    $numBookingsMorning < $daysHours[$currentDay]['maxBookingsMorning'] &&
                    !empty($daysHoursCurrentDayFromMorning) &&
                    !empty($daysHoursCurrentDayToMorning)
                ) {
                    $sendData["days"][$date->format('Y-m-d')]["hours"][] = array(
                        "from" => $daysHoursCurrentDayFromMorning,
                        "to" => $daysHoursCurrentDayToMorning
                    );
                }
                if (
                    $numBookingsAfternoon < $daysHours[$currentDay]['maxBookingsAfternoon'] &&
                    !empty($daysHoursCurrentDayFromAfternoon) &&
                    !empty($daysHoursCurrentDayToAfternoon)
                ) {
                    $sendData["days"][$date->format('Y-m-d')]["hours"][] = array(
                        "from" => $daysHoursCurrentDayFromAfternoon,
                        "to" => $daysHoursCurrentDayToAfternoon
                    );
                }

                // add 1 day
                $date->add(new DateInterval('P1D'));
            }

            return $this->returnJsonResult($sendData);
        }
    }

    /**
     * Allow the garage to choose between all the works and vehicle types available in the network.
     * Sends data to the view and process all the data sent by the user in the view.
     *
     * @param garageNetworkId Relation between garage an network in garages_network table
     */
    public function add_works_garage($garageNetworkId)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

        if (!$garageNetwork) {
            throw new UnauthorizedException();
        }

        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

        if (!$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $networkId = $garageNetwork['GarageNetwork']['network_id'];

        $worksAvailable = $this->Work->findWorksNetworkActive($networkId);
        $garageNetworkWorks = $this->GarageNetworkWork->findGarageNetworkWorks($garageNetworkId);

        $servicesAvailable = $this->Service->find('all', array("order" => "name_en asc"));
        $garageNetworkServices = $this->GarageNetworkService->findGarageNetworkServices($garageNetworkId);

        $networkServicesDrivers = $this->ServiceDriver->findServicesDriversNetwork($networkId);
        $garageNetworkServicesDrivers = $this->GarageNetworkServiceDriver->findGarageNetworkServicesDrivers($garageNetworkId);

        $vehiclesAvailable = $this->Vehicle->find('all', array("order" => "name_en asc"));
        $garageNetworkVehicles = $this->GarageNetworkVehicle->findGarageNetworkVehicles($garageNetworkId);
        $garageNetworkVehiclesBlackList = $this->GarageNetworkVehicleBlackList->findGarageNetworkVehiclesBlackList($garageNetworkId);

        $vehicleTypesAvailable = $this->VehicleType->find('all', array("order" => "name_en asc"));
        $garageNetworkVehicleTypes = $this->GarageNetworkVehicleType->findGarageNetworkVehicleTypes($garageNetworkId);

        $cancelAction = array('url_cancel' => array(
            'controller' => 'garages_networks',
            'action' => 'view',
            $garageId
        ));

        $this->set(array(
            'cancel_action' => $cancelAction,
            'garage_network_id' => $garageNetworkId,
            'garage_id' => $garageId,
            'garage_name' => $garage['Garage']['name'],
            'works_available' => $worksAvailable,
            'works_selected' =>  $garageNetworkWorks,
            'services_available' => $servicesAvailable,
            'services_selected' => $garageNetworkServices,
            'services_drivers_available' => $networkServicesDrivers,
            'services_drivers_selected' => $garageNetworkServicesDrivers,
            'vehicles_available' => $vehiclesAvailable,
            'vehicles_selected' => $garageNetworkVehicles,
            'vehicles_black_list_selected' => $garageNetworkVehiclesBlackList,
            'vehicle_types_available' =>  $vehicleTypesAvailable,
            'vehicle_types_selected' => $garageNetworkVehicleTypes,
            'network_id' => $networkId,
            'quoting_views_active' => $garageNetwork['GarageNetwork']['quoting_views_active']
        ));

        // proccessing the POST request
        if (!$this->request->is(['get'])) {
            $errorSaving = false;

            $worksSelectedForm = $this->request->data["Works"] ?? null;
            $servicesSelectedForm = $this->request->data["Service"] ?? null;
            $servicesDriversSelectedForm = $this->request->data["ServiceDriver"] ?? null;
            $vehiclesSelectedForm = $this->request->data["Vehicle"] ?? null;
            $vehiclesBlackListSelectedForm = $this->request->data["VehicleBlackList"] ?? null;
            $vehicleTypesSelectedForm = $this->request->data["VehicleType"] ?? null;

            // WORKS
            if (isset($worksSelectedForm) && !empty($worksSelectedForm)) {
                foreach ($worksSelectedForm as $workKey => $workValue) {
                    if ($workValue == 0) {
                        if (in_array($workKey, $garageNetworkWorks)) {
                            $garageNetworkWork = $this->GarageNetworkWork->find("first", array(
                                "conditions" => array(
                                    "garage_network_id" => $garageNetworkId,
                                    "work_id" => $workKey
                                )
                            ));
                            if (!$this->GarageNetworkWork->delete($garageNetworkWork["GarageNetworkWork"]["id"])) {
                                $errorSaving = true;
                                break;
                            }
                        }
                        unset($garageNetworkWorks[$workKey]);
                        continue;
                    }

                    // save process
                    if (!in_array($workValue, $garageNetworkWorks)) {
                        $garagesNetworksWorks = array(
                            "garage_network_id" => $garageNetworkId,
                            "work_id" => $workValue
                        );
                        $this->GarageNetworkWork->create();
                        if (!$this->GarageNetworkWork->save($garagesNetworksWorks)) {
                            $this->Session->setFlashError("Works can't be saved");
                            $errorSaving = true;
                            break;
                        }
                    }
                }
            }

            // SERVICES
            if (isset($servicesSelectedForm) && !empty($servicesSelectedForm)) {
                foreach ($servicesSelectedForm as $serviceKey => $serviceValue) {
                    if ($serviceValue == 0) {
                        if (in_array($serviceKey, $garageNetworkServices)) {
                            $garageNetworkService = $this->GarageNetworkService->find("first", array(
                                "conditions" => array(
                                    "garage_network_id" => $garageNetworkId,
                                    "service_id" => $serviceKey
                                )
                            ));
                            if (!$this->GarageNetworkService->delete($garageNetworkService["GarageNetworkService"]["id"])) {
                                $errorSaving = true;
                                break;
                            }
                        }
                        unset($servicesSelectedForm[$serviceKey]);
                        continue;
                    }

                    if (!in_array($serviceValue, $garageNetworkServices)) {
                        $garageNetworkService = array(
                            "garage_network_id" => $garageNetworkId,
                            "service_id" => $serviceKey
                        );

                        $this->GarageNetworkService->create();
                        if (!$this->GarageNetworkService->save($garageNetworkService)) {
                            $errorSaving = true;
                            break;
                        }
                    }
                }
            }

            // SERVICES DRIVERS
            if (isset($servicesDriversSelectedForm) && !empty($servicesDriversSelectedForm)) {
                foreach ($servicesDriversSelectedForm as $serviceDriverKey => $serviceDriverValue) {
                    if ($serviceDriverValue == 0) {
                        if (in_array($serviceDriverKey, $garageNetworkServicesDrivers)) {
                            $garageNetworkServiceDriver = $this->GarageNetworkServiceDriver->find("first", array(
                                "conditions" => array(
                                    "garage_network_id" => $garageNetworkId,
                                    "service_driver_id" => $serviceDriverKey
                                )
                            ));
                            if (!$this->GarageNetworkServiceDriver->delete($garageNetworkServiceDriver["GarageNetworkServiceDriver"]["id"])) {
                                $errorSaving = true;
                                break;
                            }
                        }
                        unset($servicesDriversSelectedForm[$serviceDriverKey]);
                        continue;
                    }

                    if (!in_array($serviceDriverValue, $garageNetworkServicesDrivers)) {
                        $garageNetworkServiceDriver = array(
                            "garage_network_id" => $garageNetworkId,
                            "service_driver_id" => $serviceDriverKey
                        );

                        $this->GarageNetworkServiceDriver->create();
                        if (!$this->GarageNetworkServiceDriver->save($garageNetworkServiceDriver)) {
                            $errorSaving = true;
                            break;
                        }
                    }
                }
            }

            // VEHICLES
            if (isset($vehiclesSelectedForm) && !empty($vehiclesSelectedForm)) {
                foreach ($vehiclesSelectedForm as $vehicleKey => $vehicleValue) {
                    if ($vehicleValue == 0) {
                        if (in_array($vehicleKey, $garageNetworkVehicles)) {
                            $garageNetworkVehicle = $this->GarageNetworkVehicle->find("first", array(
                                "conditions" => array(
                                    "garage_network_id" => $garageNetworkId,
                                    "vehicle_id" => $vehicleKey
                                )
                            ));
                            if (!$this->GarageNetworkVehicle->delete($garageNetworkVehicle["GarageNetworkVehicle"]["id"])) {
                                $errorSaving = true;
                                break;
                            }
                        }
                        unset($vehiclesSelectedForm[$vehicleKey]);
                        continue;
                    }

                    if (!in_array($vehicleValue, $garageNetworkVehicles)) {
                        $garageNetworkVehicle = array(
                            "garage_network_id" => $garageNetworkId,
                            "vehicle_id" => $vehicleKey
                        );

                        $this->GarageNetworkVehicle->create();
                        if (!$this->GarageNetworkVehicle->save($garageNetworkVehicle)) {
                            $errorSaving = true;
                            break;
                        }
                    }
                }
            }

            // VEHICLES BLACK LIST
            if (isset($vehiclesBlackListSelectedForm) && !empty($vehiclesBlackListSelectedForm)) {
                foreach ($vehiclesBlackListSelectedForm as $vehicleBlackListKey => $vehicleBlackListValue) {
                    if ($vehicleBlackListValue == 0) {
                        if (in_array($vehicleBlackListKey, $garageNetworkVehiclesBlackList)) {
                            $garageNetworkVehicleBlackList = $this->GarageNetworkVehicleBlackList->find("first", array(
                                "conditions" => array(
                                    "garage_network_id" => $garageNetworkId,
                                    "vehicle_id" => $vehicleBlackListKey
                                )
                            ));
                            if (!$this->GarageNetworkVehicleBlackList->delete($garageNetworkVehicleBlackList["GarageNetworkVehicleBlackList"]["id"])) {
                                $errorSaving = true;
                                break;
                            }
                        }
                        unset($vehiclesBlackListSelectedForm[$vehicleBlackListKey]);
                        continue;
                    }

                    if (!in_array($vehicleBlackListValue, $garageNetworkVehiclesBlackList)) {
                        $garageNetworkVehicleBlackList = array(
                            "garage_network_id" => $garageNetworkId,
                            "vehicle_id" => $vehicleBlackListKey
                        );

                        $this->GarageNetworkVehicleBlackList->create();
                        if (!$this->GarageNetworkVehicleBlackList->save($garageNetworkVehicleBlackList)) {
                            $errorSaving = true;
                            break;
                        }
                    }
                }
            }

            // VEHICLE TYPES
            if (isset($vehicleTypesSelectedForm) && !empty($vehicleTypesSelectedForm)) {
                foreach ($vehicleTypesSelectedForm as $vehicleTypeKey => $vehicleTypeValue) {
                    if ($vehicleTypeValue == 0) {
                        if (in_array($vehicleTypeKey, $garageNetworkVehicleTypes)) {
                            $garageNetworkVehicleType = $this->GarageNetworkVehicleType->find("first", array(
                                "conditions" => array(
                                    "garage_network_id" => $garageNetworkId,
                                    "vehicle_type_id" => $vehicleTypeKey
                                )
                            ));
                            if (!$this->GarageNetworkVehicleType->delete($garageNetworkVehicleType["GarageNetworkVehicleType"]["id"])) {
                                $errorSaving = true;
                                break;
                            }
                        }
                        unset($vehicleTypesSelectedForm[$vehicleTypeKey]);
                        continue;
                    }

                    if (!in_array($vehicleTypeValue, $garageNetworkVehicleTypes)) {
                        $garageNetworkVehicleType = array(
                            "garage_network_id" => $garageNetworkId,
                            "vehicle_type_id" => $vehicleTypeKey
                        );

                        $this->GarageNetworkVehicleType->create();
                        if (!$this->GarageNetworkVehicleType->save($garageNetworkVehicleType)) {
                            $errorSaving = true;
                            break;
                        }
                    }
                }
            }

            if (!$errorSaving) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                return $this->redirect(
                    [
                        "controller" => "garages_networks",
                        "action" => "add_works_garage/" . $garageNetworkId
                    ]
                );
            } else {
                $this->Session->setFlashError("Error saving data");
            }
        }
    }

    /**
     * Creates a token linked to an image.
     */
    private function generateTokenImage($imgId, $controller)
    {
        // random string only used here
        $stringToHash = $imgId . $controller . "F@xz#u~6Bm)vPBj/by%";
        return hash("sha256", $stringToHash);
    }

    /**
     * Shows the image sent by parameter. Used in calls from APIs like garage_info to retrieve image when Azure storage wasn't configured.
     * Permissions checked with passed token.
     */
    public function image($controller, $id, $check)
    {
        $checkEncrypt = $this->generateTokenImage($id, $controller);
        if ($check == $checkEncrypt) {
            $size = "";
            if (isset($this->request->query['size'])) {
                $size = $this->request->query['size'] . "-";
            }

            if ($controller == "gn") {
                $image = $this->GarageNetworkImage->findById($id);

                if (empty($image)) {
                    exit;
                }
                $file = $size . $image["GarageNetworkImage"]["file"];

                if (file_exists(ConstantsFilePaths::GARAGES_NETWORKS_IMAGES_ABSOLUTE . $file)) {
                    return $this->download_file_name($file, $file, ConstantsFilePaths::GARAGES_NETWORKS_IMAGES_ABSOLUTE, false);
                }

                exit;
            } elseif ($controller == "g") {
                $image = $this->GarageImage->findById($id);

                if (empty($image)) {
                    exit;
                }

                if (file_exists(ConstantsFilePaths::GARAGES_IMAGES_RELATIVE . $image["GarageImage"]["file"])) {
                    return $this->download_file_name($image["GarageImage"]["file"], $image["GarageImage"]["file"], ConstantsFilePaths::GARAGES_IMAGES_ABSOLUTE, false);
                }

                exit;
            } elseif ($controller == "n") {
                $image = $this->Network->findById($id);

                if (empty($image)) {
                    exit;
                }

                if (file_exists(ConstantsFilePaths::NETWORK_IMAGES_ABSOLUTE . $image["Network"]["image"])) {
                    return $this->download_file_name($image["Network"]["image"], $image["Network"]["image"], ConstantsFilePaths::NETWORK_IMAGES_ABSOLUTE, false);
                }

                exit;
            } elseif ($controller == "nr") {
                $image = $this->NetworkRecommended->findById($id);

                if (empty($image)) {
                    exit;
                }

                $imageName = $image["NetworkRecommended"]["image_recommended"];
                $imageListName = $image["NetworkRecommended"]["image_recommended_list"];

                $imagePaths = [
                    ConstantsFilePaths::NETWORKS_RECOMMENDED_IMAGES_ABSOLUTE . $imageName,
                    ConstantsFilePaths::NETWORKS_RECOMMENDED_IMAGES_ABSOLUTE . $imageListName
                ];

                foreach ($imagePaths as $path) {
                    if (!empty($path) && file_exists($path)) {
                        return $this->download_file_name(basename($path), basename($path), ConstantsFilePaths::NETWORKS_RECOMMENDED_IMAGES_ABSOLUTE, false);
                    }
                }

                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * API to get all the information about a garage.
     *
     * @param garage_id received by POST request, it's a guid
     * @param network_id received by POST request
     */
    public function garage_info()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {
            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $garageId = isset($dataReceived->garage_id) ? $dataReceived->garage_id : null;
            //AGN sends us a guid as the garage_id, so we turn it into the id
            if ($garageId) {
                $foundGarage = $this->Garage->findByGuid($garageId, ['id']);
                if (isset($foundGarage['Garage']['id'])) {
                    $garageId = $foundGarage['Garage']['id'];
                } else {
                    $garageId = null;
                }
            }
            $slug = isset($dataReceived->slug) ? $dataReceived->slug : null;
            $latitude = isset($dataReceived->latitude) ? $dataReceived->latitude : null;
            $longitude = isset($dataReceived->longitude) ? $dataReceived->longitude : null;
            $plate = isset($dataReceived->plate) ? $dataReceived->plate : null;
            $vin = isset($dataReceived->vin) ? $dataReceived->vin : null;
            $vehicle_id_leadgen = isset($dataReceived->vehicle_id_leadgen) ? $dataReceived->vehicle_id_leadgen : null;
            $lang = isset($dataReceived->lang) ? $dataReceived->lang : null;

            $sendData = array();

            if (empty($networkId) || (empty($garageId) && empty($slug))) {
                return $this->returnJsonResult($sendData);
            }

            if (empty($lang)) {
                $lang = Configure::read('network_language.' . $networkId);
            }

            $lang = strtolower($lang);

            $code = $this->Language->find('first', array(
                'conditions' => array(
                    'code != ' => ConstantsLanguages::LOCO_CODE,
                    'code' => $lang
                )
            ));

            if (!$code) {
                return $this->returnJsonResult($sendData);
            }

            $days = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
            $openingsHours = array();
            $imagesLinks = array();
            $provinceName = null;

            $fields = array("Garage.*");
            if (!empty($latitude) && !empty($longitude) && is_numeric($latitude) && is_numeric($longitude)) {
                $distanceKmsSQL = '60 * 1.1515 * 180/PI()
                    * acos(
                        cos(radians(' . $latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $longitude . '))
                        + sin(radians(' . $latitude . ')) * sin(radians(latitude))
                    )
                    * 1.609344';
                $fields[] = 'truncate(' . $distanceKmsSQL . ', 2) as distanceCalculated';
            }

            $conditionsGarage = array();
            if (!empty($garageId)) {
                $conditionsGarage = array(
                    'id' => $garageId
                );
            } else {
                $conditionsGarage = array(
                    'slug' => $slug
                );
            }

            $garageInfo = $this->Garage->find("first", array(
                "conditions" => $conditionsGarage,
                "fields" => $fields
            ));

            if (!$garageInfo) {
                return $this->returnJsonResult($sendData);
            }

            $garageId = $garageInfo['Garage']['id'];

            $garageNetworkInfo = $this->GarageNetwork->find("first", array(
                "conditions" => array(
                    "garage_id" => $garageId,
                    "network_id" => $networkId,
                    "status" => ConstantsNetworksStatus::LIVE
                )
            ));

            $network = $this->Network->findById($networkId);

            if (!$garageNetworkInfo) {
                return $this->returnJsonResult($sendData);
            }

            $garageNetworkId = $garageNetworkInfo["GarageNetwork"]["id"];

            $garageNetworkImage = $this->GarageNetworkImage->findByGarageNetworkIdAndPrincipal($garageNetworkId, ConstantsBooleans::YES);

            $garageNetworkImages = $this->GarageNetworkImage->findAllByGarageNetworkId($garageNetworkId);

            $province = $this->Province->findById($garageInfo["Garage"]["province_id"]);

            $garageNetworkWorks = $this->Work->findGarageNetworkWorks($garageNetworkId);

            $garageNetworkServices = $this->Service->findGarageNetworkServices($garageNetworkId);

            $garageNetworkServicesDrivers = $this->ServiceDriver->findGarageNetworkServicesDrivers($garageNetworkId);

            $networksInfo = $this->GarageNetwork->find("all", array(
                'joins' => array(
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                            'Network.internal' => ConstantsBooleans::YES
                        ),
                        'fields' => array('Network.id, Network.name, Network.image')
                    )
                ),
                'conditions' => array(
                    'GarageNetwork.garage_id' => $garageId,
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    'GarageNetwork.network_id !=' => $networkId
                ),
                'fields' => array('GarageNetwork.*, Network.id, Network.name, Network.image')
            ));

            $software = $this->Software->find('first', array(
                'conditions' => array(
                    'name_en' => ConstantsSoftwareValues::CALLTRACKS
                ),
                'fields' => 'id'
            ));

            if (!empty($software)) {
                $garageSoftware = $this->GarageSoftware->find('first', array(
                    'conditions' => array(
                        'GarageSoftware.garage_id' => $garageId,
                        'GarageSoftware.software_id' => $software['Software']['id'],
                        'GarageSoftware.username IS NOT NULL',
                        'GarageSoftware.username !=' => ''
                    ),
                    'fields' => 'username'
                ));
            }

            foreach ($days as $day) {
                $openingsHours[$day] = array(
                    $garageInfo["Garage"][$day . "_open_1"] == null ?  "" : $garageInfo["Garage"][$day . "_open_1"],
                    $garageInfo["Garage"][$day . "_closed_1"] == null
                        ?  "" : $garageInfo["Garage"]["$day" . "_closed_1"],
                    $garageInfo["Garage"][$day . "_open_2"] == null ?  "" : $garageInfo["Garage"][$day . "_open_2"],
                    $garageInfo["Garage"][$day . "_closed_2"] == null
                        ?  "" : $garageInfo["Garage"][$day . "_closed_2"]
                );
            }

            $photoPrincipal =  null;
            if (!empty($garageNetworkImage)) {
                if (\Configure::read('AZURE_FILES')) {
                    $photoPrincipal = AzureBlob::getUrlForPublicContainer(ConstantsFilePaths::GARAGES_NETWORKS_IMAGES_RELATIVE . $garageNetworkImage['GarageNetworkImage']['file']);
                } else {
                    $imgId = $garageNetworkImage["GarageNetworkImage"]["id"];
                    $photoPrincipal = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . "/" . $this->request->params['controller'] . "/" .
                        "image/gn/" . $imgId . "/" . $this->generateTokenImage($imgId, "gn");
                }
            }

            $provinceName = empty($province) ? null : $province["Province"]["name"];
            $distance = isset($garageInfo[0]['distanceCalculated']) ? $garageInfo[0]['distanceCalculated'] : null;

            $erpCode = !empty($garageNetworkInfo['GarageNetwork']['sap_code']) ?
                $garageNetworkInfo['GarageNetwork']['sap_code'] : '';
            if (empty($erpCode)) {
                $erpCode = !empty($garageInfo['Garage']['ref_code']) ?
                    $garageInfo['Garage']['ref_code'] : $network['Network']['ref_code'];
            }

            $sendData["garage_id"] = $garageInfo['Garage']['guid'];
            $sendData["garage_name"] = $garageInfo["Garage"]["name"];
            $sendData["business_name"] = $garageInfo["Garage"]["business_name"];
            $sendData["slug"] = $garageInfo["Garage"]["slug"];
            $sendData["address1"] = $garageInfo["Garage"]["address1"];
            $sendData["address2"] = $garageInfo["Garage"]["address2"];
            $sendData["address3"] = $garageInfo["Garage"]["address3"];
            $sendData["address4"] = $garageInfo["Garage"]["address4"];
            $sendData["town"] = $garageInfo["Garage"]["town"];
            $sendData["city_id"] = $garageInfo["Garage"]["city_id"];
            $sendData["province"] = $provinceName;
            $sendData["postcode"] = $garageInfo["Garage"]["postcode"];
            $sendData["latitude"] = $garageInfo["Garage"]["latitude"];
            $sendData["longitude"] = $garageInfo["Garage"]["longitude"];
            $sendData["distance"] = $distance;
            $sendData["photo_principal"] = $photoPrincipal;
            $sendData["phone"] = $this->getPhoneNumberForPws($network, $garageInfo["Garage"]["phone"]);
            $sendData["about"] = $garageNetworkInfo["GarageNetwork"]["about"];
            $sendData["quoting_active"] = $garageNetworkInfo["GarageNetwork"]["quoting_active"];
            $sendData["enquiries_active"] = $garageNetworkInfo["GarageNetwork"]["enquiries_active"];
            $sendData["erp_code"] = !empty($erpCode) ? $erpCode : null;
            $sendData["location_id"] = $garageNetworkInfo["GarageNetwork"]["location_id"];
            $sendData["opening_time"] = $openingsHours;
            $sendData["reviews_number"] = $garageNetworkInfo["GarageNetwork"]["reviews_number"];
            $sendData["rating"] = $garageNetworkInfo["GarageNetwork"]["rating"];
            $sendData["booking_active"] = $this->isBookingActive($garageNetworkInfo);
            //Here the code is kept generic, checking if parent network has childen ones.
            $childNetworks = $this->Network->getListofChildNetworks($networkId);
            if (isset($childNetworks) && !empty($childNetworks)) {
                $sendData["belongs_to_cv_child_network"] = $this->GarageNetwork->garageBelongsToCvNetwork($garageId);
            }
            if (!empty($garageSoftware)) {
                $sendData["username"] = $garageSoftware["GarageSoftware"]["username"];
            } else {
                $sendData["username"] = '';
            }

            if (isset($plate) || (isset($vin)) && isset($vehicle_id_leadgen)) {
                $leadgen = new Leadgen();
                $vehicleWorks = $leadgen->getVehicleWorks($networkId, $plate, $vin, $vehicle_id_leadgen);
                $vehicleWorks = isset($vehicleWorks['works']) ? Hash::extract($vehicleWorks['works'], '{n}.id') : array();
            }

            foreach ($garageNetworkWorks as $work) {
                // if plate has been specified, Leadgen is called to obtain available works for the vehicle
                if (isset($vehicleWorks) && !empty($vehicleWorks)) {
                    if (in_array($work['Work']['code'], $vehicleWorks)) {
                        $sendData['works'][] = array(
                            'id' => $work['Work']['code'],
                            'name' => $work['Work']['name_' . $lang]
                        );
                    }
                } else {
                    $sendData['works'][] = array(
                        'id' => $work['Work']['code'],
                        'name' => $work['Work']['name_' . $lang]
                    );
                }
            }

            foreach ($garageNetworkServices as $service) {
                $sendData["services"][] = array(
                    "id" => $service["Service"]["id"],
                    "name" => $service["Service"]['name_' . $lang]
                );
            }

            foreach ($garageNetworkServicesDrivers as $serviceDriver) {
                $sendData["services_drivers"][] = array(
                    "id" => $serviceDriver["ServiceDriver"]["id"],
                    "name" => $serviceDriver["ServiceDriver"]['name_' . $lang]
                );
            }

            $imagesLinks = null;
            if (!empty($garageNetworkImages)) {
                foreach ($garageNetworkImages as $image) {
                    if (!($image["GarageNetworkImage"]["principal"] == ConstantsBooleans::YES)) {
                        if (\Configure::read('AZURE_FILES')) {
                            $imagesLinks[] = AzureBlob::getUrlForPublicContainer(ConstantsFilePaths::GARAGES_NETWORKS_IMAGES_RELATIVE . $image['GarageNetworkImage']['file']);
                        } else {
                            $imgId = $image["GarageNetworkImage"]["id"];
                            $imagesLinks[] = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . "/" . $this->request->params['controller'] . "/" .
                                "image/gn/" . $imgId . "/" . $this->generateTokenImage($imgId, "gn");
                        }
                    }
                }
                $sendData["images"] = $imagesLinks;
            }

            $recommended = $garageNetworkInfo["GarageNetwork"]["recommended"] == 1;
            $imagesRecommended = array();
            foreach ($networksInfo as $networkInfo) {
                if (
                    $networkInfo["Network"]["id"] == NETWORK_ID_AUTOCARE &&
                    isset($networkInfo['GarageNetwork']['annex_detail_id']) &&
                    $networkInfo['GarageNetwork']['annex_detail_id'] == Configure::read('ANNEX_DETAIL_AUTOCARE_UNBRANDED')
                ) {
                    continue;
                }
                if (\Configure::read('AZURE_FILES')) {
                    $networkLogo = AzureBlob::getUrlForPublicContainer(ConstantsFilePaths::NETWORK_IMAGES_RELATIVE . $networkInfo["Network"]["image"]);
                } else {
                    $networkLogo = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . "/" . $this->request->params['controller'] . "/" .
                        "image/n/" . $networkInfo["Network"]["id"] . "/" .
                        $this->generateTokenImage($networkInfo["Network"]["id"], "n");
                }
                $sendData["networks"][] = array(
                    "id" => $networkInfo["Network"]["id"],
                    "name" => $networkInfo["Network"]["name"],
                    "logo" => $networkLogo
                );
                $networkRecommendedInfo = $this->NetworkRecommended
                    ->getInternalRecommendedNetworks($networkId, $networkInfo["Network"]["id"]);
                if ($networkRecommendedInfo) {
                    if ($networkRecommendedInfo['NetworkRecommended']['recommended_label']) {
                        $recommended = true;
                    }
                    if (!empty($networkRecommendedInfo['NetworkRecommended']['image_recommended'])) {
                        if (\Configure::read('AZURE_FILES')) {
                            $urlImage = AzureBlob::getUrlForPublicContainer(ConstantsFilePaths::NETWORKS_RECOMMENDED_IMAGES_RELATIVE . $networkRecommendedInfo["NetworkRecommended"]["image_recommended"]);
                        } else {
                            $imgId = $networkRecommendedInfo["NetworkRecommended"]["id"];
                            $urlImage = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . "/" . $this->request->params['controller'] . "/" .
                                "image/nr/" . $imgId . "/" . $this->generateTokenImage($imgId, "nr");
                        }
                        $imagesRecommended[] = array(
                            'logo' => $urlImage,
                            'name' => $networkRecommendedInfo['NetworkRecommended']['image_recommended']
                        );
                    }
                }
            }

            $sendData["recommended"] = $recommended;
            $sendData["images_recommended"] = $imagesRecommended;

            return $this->returnJsonResult($sendData);
        }
    }

    /**
     * API to get the prices of a work (discount, markup, surcharge, labours) for a list of garages in a network.
     *
     * @param network_id received by POST request
     * @param garages_codes received by POST request
     * @param work received by POST request
     */
    public function work_prices()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {
            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : '';
            $garagesCodes = isset($dataReceived->garages_codes) ? $dataReceived->garages_codes : '';
            $workCode = isset($dataReceived->work) ? $dataReceived->work : '';

            if (empty($networkId) || empty($garagesCodes) || empty($workCode)) {
                exit;
            }

            $garagesWork = array('garages_work' => array());

            $network = $this->Network->findById($networkId);

            // if network isn't found
            if (!$network) {
                return $this->returnJsonResult($garagesWork);
            }

            $work = $this->Work->find('first', array(
                'conditions' => array(
                    'network_id' => $networkId,
                    'code' => $workCode,
                    'active' => true
                )
            ));

            // if work isn't found
            if (!$work) {
                return $this->returnJsonResult($garagesWork);
            }

            $fluids = $this->Fluid->find('all', array(
                'conditions' => array(
                    'network_id' => $network['Network']['id'],
                    'name_en is not null',
                    'active' => ConstantsBooleans::ACTIVE
                ),
            ));

            $workGenarts = $this->Genart->find('all', array(
                'conditions' => array(
                    'grouping_genart_id' => $work['Work']['grouping_genart_id']
                )
            ));

            // list price: discount - net price: markup / surcharge
            $returnDiscount = $network['Network']['pricing_type_id'] == ConstantsQuotingPricingTypes::LIST_PRICE;

            foreach ($garagesCodes as $garageId) {
                // As we receive a guid we turn it into an id
                if (isset($garageId)) {
                    $foundGarage = $this->Garage->findByGuid($garageId, ['id', 'guid']);
                    if (isset($foundGarage['Garage']['id'])) {
                        $garageId = $foundGarage['Garage']['id'];
                    } else {
                        continue;
                    }
                }

                $garageNetwork = $this->GarageNetwork->find('first', array(
                    'conditions' => array(
                        'garage_id' => $garageId,
                        'network_id' => $networkId,
                        'status' => ConstantsNetworksStatus::LIVE
                    )
                ));

                if (!$garageNetwork) {
                    continue;
                }

                $genarts = array();
                foreach ($workGenarts as $genart) {
                    $returnMarkup = true;
                    $isLabourTime = ($genart['Genart']['is_labour_time'] == ConstantsBooleans::ACTIVE);
                    $value = null;
                    $isLabourPrice = null;

                    // advanced settings
                    $garageNetworkGenart = $this->GarageNetworkGenart->find('first', array(
                        'conditions' => array(
                            'garage_network_id' => $garageNetwork['GarageNetwork']['id'],
                            'genart_id' => $genart['Genart']['id']
                        )
                    ));

                    if (!empty($garageNetworkGenart)) {
                        if ($isLabourTime && ($garageNetworkGenart['GarageNetworkGenart']['is_labour_price'] != null)) {
                            $isLabourPrice = $garageNetworkGenart['GarageNetworkGenart']['is_labour_price'];
                        } elseif ($returnDiscount && $garageNetworkGenart['GarageNetworkGenart']['discount'] != null) {
                            $value = $garageNetworkGenart['GarageNetworkGenart']['discount'];
                        } else {
                            // return markup or nothing
                            if ($garageNetworkGenart['GarageNetworkGenart']['markup'] != null) {
                                $value = $garageNetworkGenart['GarageNetworkGenart']['markup'];
                            }
                            if ($garageNetworkGenart['GarageNetworkGenart']['surcharge'] != null) {
                                $returnMarkup = false;
                                $value = $garageNetworkGenart['GarageNetworkGenart']['surcharge'];
                            }
                        }
                    }

                    // if advanced settigns wasn't specified, search in basic family, if is labour its not needed.
                    if (empty($value) && ($isLabourTime == ConstantsBooleans::NO_ACTIVE)) {
                        $genartMaster = $this->GenartMaster->find('first', array(
                            'conditions' => array(
                                'code' => $genart['Genart']['code'],
                                'network_id' => $networkId
                            )
                        ));

                        if ($genartMaster) {
                            // if GenartMaster has genart_family_id, search by family, if not search by family "Other" (null)
                            if (!empty($genartMaster['GenartMaster']['genart_family_id'])) {
                                $garageNetworkGenartFamily = $this->GarageNetworkGenartFamily->find('first', array(
                                    'conditions' => array(
                                        'garage_network_id' => $garageNetwork['GarageNetwork']['id'],
                                        'genart_family_id' => $genartMaster['GenartMaster']['genart_family_id']
                                    )
                                ));
                            } else {
                                $garageNetworkGenartFamily = $this->GarageNetworkGenartFamily->find('first', array(
                                    'conditions' => array(
                                        'garage_network_id' => $garageNetwork['GarageNetwork']['id'],
                                        'genart_family_id IS NULL'
                                    )
                                ));
                                // if genart is a fluid we don´t return value
                                foreach ($fluids as $fluid) {
                                    if ($fluid['Fluid']['name_en'] == $genart['Genart']['name_en']) {
                                        $garageNetworkGenartFamily = null;
                                    }
                                }
                            }

                            // basic family genart
                            if ($garageNetworkGenartFamily) {
                                if ($returnDiscount) {
                                    // return markup or nothing
                                    if ($garageNetworkGenartFamily['GarageNetworkGenartFamily']['discount'] != null) {
                                        $value = $garageNetworkGenartFamily['GarageNetworkGenartFamily']['discount'];
                                    }
                                } else {
                                    if ($garageNetworkGenartFamily['GarageNetworkGenartFamily']['markup'] != null) {
                                        $value = $garageNetworkGenartFamily['GarageNetworkGenartFamily']['markup'];
                                    }
                                    if ($garageNetworkGenartFamily['GarageNetworkGenartFamily']['surcharge'] != null) {
                                        $returnMarkup = false;
                                        $value = $garageNetworkGenartFamily['GarageNetworkGenartFamily']['surcharge'];
                                    }
                                }
                            }
                        }
                    }

                    //If is_labour_time and its isLabourPrice its not found it may be configured globally (for the garage network) for that genart or might not be configured.
                    if ($isLabourTime && empty($isLabourPrice)) {
                        $garageNetworkGenartMaster = $this->GenartMaster->getLabourPriceGenart($genart['Genart']['code'], $networkId, $garageNetwork['GarageNetwork']['id']);

                        if (isset($garageNetworkGenartMaster['GarageNetworkGenartMaster']['genart_master_labour_price']) && $garageNetworkGenartMaster['GarageNetworkGenartMaster']['genart_master_labour_price'] != null) {
                            $isLabourPrice = $garageNetworkGenartMaster['GarageNetworkGenartMaster']['genart_master_labour_price'];
                        }
                    }

                    if (!empty($value) || !empty($isLabourPrice)) {
                        $genartData = array(
                            'code' => $genart['Genart']['code']
                        );
                        //If has M,S,D and is configured we add it to the genart
                        if (!empty($value)) {
                            if ($returnDiscount) {
                                $type = 'discount';
                            } else {
                                $type = $returnMarkup ? 'markup' : 'surcharge';
                            }
                            $genartData[$type] = $value;
                        }

                        //If is labour and is configured we add it to the genart
                        if ($isLabourTime && !empty($isLabourPrice)) {
                            $genartData['labour_price'] = $isLabourPrice;
                        }
                        $genarts[] = $genartData;
                    }
                }

                $garageInfo = array(
                    'garage_id' => $foundGarage['Garage']['guid'],
                    'genarts' => array() // always include this array in the response
                );

                if (count($workGenarts) == 0) {
                    // there are NO genarts, dealer case
                    // order to query:
                    // advanced: garages_networks_works_prices: discount
                    // basic: garages_networks: dealer_discount

                    // advanced settings
                    $garageNetworkWorkPrice = $this->GarageNetworkWorkPrice->find('first', array(
                        'conditions' => array(
                            'garage_network_id' => $garageNetwork['GarageNetwork']['id'],
                            'work_id' => $work['Work']['id']
                        )
                    ));

                    if ($returnDiscount) {
                        if ($garageNetworkWorkPrice) {
                            if ($garageNetworkWorkPrice['GarageNetworkWorkPrice']['discount'] != null) {
                                $value = $garageNetworkWorkPrice['GarageNetworkWorkPrice']['discount'];
                            }
                        }
                        if (empty($value)) {
                            if ($garageNetwork['GarageNetwork']['dealer_discount'] != null) {
                                $value = $garageNetwork['GarageNetwork']['dealer_discount'];
                            }
                        }
                    } else {
                        $returnMarkup = true;
                        if ($garageNetworkWorkPrice) {
                            if ($garageNetworkWorkPrice['GarageNetworkWorkPrice']['markup'] != null) {
                                $value = $garageNetworkWorkPrice['GarageNetworkWorkPrice']['markup'];
                            } elseif ($garageNetworkWorkPrice['GarageNetworkWorkPrice']['surcharge'] != null) {
                                $returnMarkup = false;
                                $value = $garageNetworkWorkPrice['GarageNetworkWorkPrice']['surcharge'];
                            }
                        }
                        if (empty($value)) {
                            if ($garageNetwork['GarageNetwork']['dealer_markup'] != null) {
                                $value = $garageNetwork['GarageNetwork']['dealer_markup'];
                            } elseif ($garageNetwork['GarageNetwork']['dealer_surcharge'] != null) {
                                $returnMarkup = false;
                                $value = $garageNetwork['GarageNetwork']['dealer_surcharge'];
                            }
                        }
                    }

                    if (!empty($value)) {
                        if ($returnDiscount) {
                            $type = 'discount';
                        } else {
                            $type = $returnMarkup ? 'markup' : 'surcharge';
                        }

                        $garageInfo[$type] = $value;
                    }
                } else {
                    // there are genarts
                    $garageInfo['genarts'] = $genarts;
                }

                // advanced
                $garageNetworkWorkLabour = $this->GarageNetworkWorkLabour->find('first', array(
                    'conditions' => array(
                        'garage_network_id' => $garageNetwork['GarageNetwork']['id'],
                        'work_id' => $work['Work']['id']
                    )
                ));

                if (!empty($garageNetworkWorkLabour)) {
                    if (!empty($garageNetworkWorkLabour['GarageNetworkWorkLabour']['labour_hourly_price'])) {
                        $garageInfo['labour_hourly_price'] = $garageNetworkWorkLabour['GarageNetworkWorkLabour']['labour_hourly_price'];
                    }

                    if (!empty($garageNetworkWorkLabour['GarageNetworkWorkLabour']['labour_hourly_price_electric_vehicles'])) {
                        $garageInfo['labour_hourly_price_electric_vehicles'] = $garageNetworkWorkLabour['GarageNetworkWorkLabour']['labour_hourly_price_electric_vehicles'];
                    }
                }

                // if advanced settings wasn't specified (or is empty), search in GarageNetwork
                if (empty($garageInfo['labour_hourly_price'])) {
                    if (!empty($garageNetwork['GarageNetwork']['labour_hourly_price'])) {
                        $garageInfo['labour_hourly_price'] = $garageNetwork['GarageNetwork']['labour_hourly_price'];
                    }
                }
                if (empty($garageInfo['labour_hourly_price_electric_vehicles'])) {
                    if (!empty($garageNetwork['GarageNetwork']['labour_hourly_price_electric_vehicles'])) {
                        $garageInfo['labour_hourly_price_electric_vehicles'] = $garageNetwork['GarageNetwork']['labour_hourly_price_electric_vehicles'];
                    }
                }

                $garagesWork['garages_work'][] = $garageInfo;
            }

            return $this->returnJsonResult($garagesWork);
        }
    }

    /**
     * View to display information about the location of a garage.
     *
     * @param garageNetworkId   Garage Network ID
     */
    public function general($garageId)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::GARAGE &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES)
        ) {
            $this->Acceso->checkGarageAccess($garageId);

            $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);
            if (!$garage) {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }

            $province = $this->Province->findById($garage['Garage']['province_id']);
            if ($province != null) {
                $country = $this->Country->findById($province['Province']['country_id']);
            }

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'garages_networks',
                    'action' => 'general',
                    $garageId
                ),
            );

            $garageOldDataTmp = $garage;

            if (!$this->request->is('get')) {
                $user = $user = $this->Session->read('Auth');
                if (isset($garage['Garage']['aag_region_id']) && $garage['Garage']['aag_region_id'] == ConstantsAAGRegionId::BENELUX) {
                    $garage = $this->Garage->updateGarageContactInfo($this->request->data);
                }
                if ($garage) {
                    $garage = $this->Garage->add_opening_garage($this->request->data, $garageId);
                    if ($garage) {
                        $this->LogChange->get_params_create_log_edit(
                            $garageOldDataTmp['Garage'],
                            $garage['Garage'],
                            $this->Garage->table,
                            $user,
                            $garageId,
                            ConstantsLogType::GARAGE
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                $this->request->data = $garage;
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'is_aag_region_uk' => ($garage['Garage']['aag_region_id'] && $garage['Garage']['aag_region_id'] == ConstantsAAGRegionId::UK),
                'garage' => $garage,
                'garage_id' => $garageId,
                'province_name' => isset($province['Province']['name']) ? $province['Province']['name'] : "",
                'country_name' => isset($country['Country']['name']) ? $country['Country']['name'] : ""
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * View to allow the garage to manage the times at which the garage can take appointments,
     * as well as the maximum number of appointments that can be booked.
     * Sends data to the view and process all the data sent by the user in the view.
     *
     * @param garageNetworkId GarageNetwork ID
     */
    public function add_planner_garage($garageNetworkId)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

        if (!$garageNetwork) {
            throw new UnauthorizedException();
        }

        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

        if (!$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        // POST
        if (!$this->request->is('get')) {
            if (!isset($this->request->data["request_changes"])) {
                $dataReceived  = $this->request->data['GarageNetwork'];

                $validationError = false;

                // validations booking min days from and max days to
                if (isset($dataReceived['booking_days_min_from']) && intval($dataReceived['booking_days_min_from']) < 1) {
                    $msg = __t('GarageNetwork.Booking_days_from') . ' ' . sprintf(__t('Alert.Cannot_be_less_than'), "1");
                    $this->Session->setFlashError($msg);
                    $validationError = true;
                }
                if (isset($dataReceived['booking_days_min_from']) && intval($dataReceived['booking_days_min_from']) > 100) {
                    $msg = __t('GarageNetwork.Booking_days_from') . ' ' . sprintf(__t('Alert.Cannot_be_more_than'), "100");
                    $this->Session->setFlashError($msg);
                    $validationError = true;
                }
                if (isset($dataReceived['booking_days_max_to']) && intval($dataReceived['booking_days_max_to']) < 1) {
                    $msg = __t('GarageNetwork.Booking_days_to') . ' ' . sprintf(__t('Alert.Cannot_be_less_than'), "1");
                    $this->Session->setFlashError($msg);
                    $validationError = true;
                }
                if (isset($dataReceived['booking_days_max_to']) && intval($dataReceived['booking_days_max_to']) > 100) {
                    $msg = __t('GarageNetwork.Booking_days_to') . ' ' .
                        sprintf(__t('Alert.Cannot_be_more_than'), "100");
                    $this->Session->setFlashError($msg);
                    $validationError = true;
                }

                $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
                foreach ($days as $day) {
                    // validations slots
                    if (isset($dataReceived[$day . '_planner_max_1']) && intval($dataReceived[$day . '_planner_max_1']) < 1) {
                        $msg = __t('General.Max_numbers') . ' ' . sprintf(__t('Alert.Cannot_be_less_than'), "1");
                        $this->Session->setFlashError($msg);
                        $validationError = true;
                    }
                    if (isset($dataReceived[$day . '_planner_max_1']) && intval($dataReceived[$day . '_planner_max_1']) > 100) {
                        $msg = __t('General.Max_numbers') . ' ' . sprintf(__t('Alert.Cannot_be_more_than'), "100");
                        $this->Session->setFlashError($msg);
                        $validationError = true;
                    }
                    if (isset($dataReceived[$day . '_planner_max_2']) && intval($dataReceived[$day . '_planner_max_2']) < 1) {
                        $msg = __t('General.Max_numbers') . ' ' . sprintf(__t('Alert.Cannot_be_less_than'), "1");
                        $this->Session->setFlashError($msg);
                        $validationError = true;
                    }
                    if (isset($dataReceived[$day . '_planner_max_2']) && intval($dataReceived[$day . '_planner_max_2']) > 100) {
                        $msg = __t('General.Max_numbers') . ' ' . sprintf(__t('Alert.Cannot_be_more_than'), "100");
                        $this->Session->setFlashError($msg);
                        $validationError = true;
                    }
                }

                if (!$validationError) {
                    $garageNetwork = $this->GarageNetwork->add_opening_garage($dataReceived);

                    if ($garageNetwork) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                }
            }
        } else {
            $this->request->data = $garageNetwork;
        }

        $this->setVarCancel($garageNetworkId);

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'garages_networks',
                'action' => 'view',
                $garageNetworkId
            ),
        );
        $networkId = $garageNetwork["GarageNetwork"]["network_id"];

        $this->set(array(
            'controller' => "garages_networks",
            'garage' => $garage,
            'garage_name' => $garage['Garage']['name'],
            'garage_network' => $garageNetwork,
            'cancel_action' => $cancelAction,
            'garage_id' => $garageId,
            'garage_network_id' => $garageNetworkId,
            'network_id' => $networkId,
            'quoting_views_active' => $garageNetwork['GarageNetwork']['quoting_views_active']
        ));
    }

    private function setVarCancel($garageNetworkId)
    {
        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'garages',
                'action' => 'view',
                $garageNetworkId
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
            'garage_id' => $garageNetworkId,
        ));
    }

    /**
     * AJAX get planner hours list.
     */
    public function ajax_planner_hours_list($garageNetworkId, $garageId)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

        if (!$garageNetwork) {
            throw new UnauthorizedException();
        }

        $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

        if (!$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $events = $this->GarageNetwork->getCalendarPlannerHours($garageNetworkId, $garageId);
        echo json_encode($events);
        $this->layout = $this->autoRender = false;
    }

    /**
     * View that allows to visualise the prices of the genarts and labours of a garage network.
     *
     * @param garageNetworkId   Garage Network ID
     */
    public function genarts($garageNetworkId)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

        if (!$garageNetwork) {
            throw new UnauthorizedException();
        }

        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

        if (!$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $network = $this->Network->findById($garageNetwork['GarageNetwork']['network_id']);

        $languageCode = self::getLanguageCodeWhenNotTranslatedWithLoco($network['Network']['id']);

        $works = $this->Work->find('all', array(
            'conditions' => array(
                'network_id' => $network['Network']['id'],
                'active' => ConstantsBooleans::ACTIVE
            ),
            'order' => 'name_' . $languageCode . ' ' . 'ASC'
        ));

        $garageNetworkWorks = $this->GarageNetworkWork->find('all', array(
            'conditions' => array(
                'garage_network_id' => $garageNetworkId
            )
        ));
        $worksIdsForTheGarage = Hash::extract($garageNetworkWorks, '{n}.GarageNetworkWork.work_id');

        $fluids = $this->Fluid->find('all', array(
            'conditions' => array(
                'network_id' => $network['Network']['id'],
                'name_en is not null',
                'active' => ConstantsBooleans::ACTIVE
            ),
        ));

        $groupingGenartList = array();
        $groupingGenartExists = array();
        $genartsIdsRepeated = array();
        $worksIdsActive = array();
        $worksIdsInactive = array();

        foreach ($works as &$work) {
            $work['Work']['onlyLabourTimeGenarts'] = ConstantsBooleans::YES;
            if (in_array($work['Work']['id'], $worksIdsForTheGarage)) {
                $worksIdsActive[] = $work['Work']['id'];
            } else {
                $worksIdsInactive[] = $work['Work']['id'];
            }
            $garageNetworkWorkLabour = $this->GarageNetworkWorkLabour->find('first', array(
                'conditions' => array(
                    'garage_network_id' => $garageNetworkId,
                    'work_id' => $work['Work']['id']
                )
            ));

            $garageNetworkWorkPrice = $this->GarageNetworkWorkPrice->find('first', array(
                'conditions' => array(
                    'garage_network_id' => $garageNetworkId,
                    'work_id' => $work['Work']['id']
                )
            ));

            if (!empty($work['Work']['grouping_genart_id'])) {
                $genarts = $this->Genart->getGenartsPerGroupingAndAssociatedPrices($garageNetworkId, $work['Work']['grouping_genart_id']);
                foreach ($genarts as &$genart) {
                    $genartPricesGarage = $this->GarageNetworkGenart->find('first', array(
                        'conditions' => array(
                            'garage_network_id' => $garageNetworkId,
                            'genart_id' => $genart['Genart']['id']
                        )
                    ));
                    $genart['Genart']['garagePrices'] = $genartPricesGarage;

                    $genartMaster = $this->GenartMaster->find('first', array(
                        'conditions' => array(
                            'network_id' => $garageNetwork['GarageNetwork']['network_id'],
                            'code' => $genart['Genart']['code']
                        )
                    ));

                    //If the genart is not is_labour_time = 1 then the work has not just is_labour_time genarts.
                    if (isset($genartMaster['GenartMaster']) && $genartMaster['GenartMaster']['is_labour_time'] == ConstantsBooleans::NO) {
                        $work['Work']['onlyLabourTimeGenarts'] = ConstantsBooleans::NO;
                    }

                    $genartFamily = isset($genartMaster['GenartMaster']) ? $this->GenartFamily->findById($genartMaster['GenartMaster']['genart_family_id']) : null;

                    $garageNetworkGenartFamilies = isset($genartFamily['GenartFamily']) ?
                        $this->GarageNetworkGenartFamily->find_by_garage_network_and_genart_family($garageNetworkId, $genartFamily['GenartFamily']['id']) :
                        $this->GarageNetworkGenartFamily->find_by_garage_network_and_genart_family($garageNetworkId, null);

                    $familyPrice = $familyType = $familyTypeText = null;
                    if (isset($garageNetworkGenartFamilies['GarageNetworkGenartFamily'])) {
                        if (isset($garageNetworkGenartFamilies['GarageNetworkGenartFamily']['markup'])) {
                            $familyPrice = $garageNetworkGenartFamilies['GarageNetworkGenartFamily']['markup'] ?? '';
                            $familyType = 'markup';
                            $familyTypeText = 'Network.Markup';
                        }
                        if (isset($garageNetworkGenartFamilies['GarageNetworkGenartFamily']['surcharge'])) {
                            $familyPrice = $garageNetworkGenartFamilies['GarageNetworkGenartFamily']['surcharge'] ?? '';
                            $familyType = 'surcharge';
                            $familyTypeText = 'Network.Surcharge';
                        }
                        if (isset($garageNetworkGenartFamilies['GarageNetworkGenartFamily']['discount'])) {
                            $familyPrice = $garageNetworkGenartFamilies['GarageNetworkGenartFamily']['discount'] ?? '';
                            $familyType = 'discount';
                            $familyTypeText = 'Network.Discount';
                        }
                    }

                    foreach ($fluids as $fluid) {
                        if ($genart['Genart']['name_en'] != $fluid['Fluid']['name_en']) {
                            $genart['Genart']['genart_family'] = isset($genartFamily['GenartFamily']) ? $genartFamily['GenartFamily'] : null;
                        } else {
                            $genart['Genart']['fluid'] = true;
                            $genart['Genart']['has_advanced_settings'] = $fluid['Fluid']['has_advanced_settings'];
                        }
                    }

                    $genart['Genart']['garage_network_genart_family_type'] = $familyType;
                    $genart['Genart']['garage_network_genart_family_type_text'] = $familyTypeText;
                    $genart['Genart']['garage_network_genart_family'] = $familyPrice;
                }

                // if the genart id already exists, the genart has already been loaded into the document
                if (in_array($work['Work']['grouping_genart_id'], $groupingGenartExists)) {
                    $groupingGenartList[$work['Work']['grouping_genart_id']] = $work['Work']['grouping_genart_id'];
                } else {
                    // if it doesn't already exist, it's added to the array of existing elements
                    $groupingGenartExists[$work['Work']['grouping_genart_id']] = $work['Work']['grouping_genart_id'];
                }
                $work['Work']['genarts'] = $genarts;
            } else {
                //If the work is dealer it has no genarts but markup,surcharge, or discount can be configured.
                $work['Work']['onlyLabourTimeGenarts'] = ConstantsBooleans::NO;
            }

            // GarageNetworkWorkLabour
            $work['Work']['labour_hourly_price'] = isset($garageNetworkWorkLabour['GarageNetworkWorkLabour']['labour_hourly_price']) ? $garageNetworkWorkLabour['GarageNetworkWorkLabour']['labour_hourly_price'] : "";
            $work['Work']['labour_hourly_price_electric_vehicles'] = isset($garageNetworkWorkLabour['GarageNetworkWorkLabour']['labour_hourly_price_electric_vehicles']) ? $garageNetworkWorkLabour['GarageNetworkWorkLabour']['labour_hourly_price_electric_vehicles'] : "";

            // GarageNetworkWorkPrice
            $work['Work']['GarageNetworkWorkPrice']['discount'] = isset($garageNetworkWorkPrice['GarageNetworkWorkPrice']['discount']) ? $garageNetworkWorkPrice['GarageNetworkWorkPrice']['discount'] : "";
            $work['Work']['GarageNetworkWorkPrice']['markup'] = isset($garageNetworkWorkPrice['GarageNetworkWorkPrice']['markup']) ? $garageNetworkWorkPrice['GarageNetworkWorkPrice']['markup'] : "";
            $work['Work']['GarageNetworkWorkPrice']['surcharge'] = isset($garageNetworkWorkPrice['GarageNetworkWorkPrice']['surcharge']) ? $garageNetworkWorkPrice['GarageNetworkWorkPrice']['surcharge'] : "";
        }

        foreach ($groupingGenartList as $groupingIdRepeated) {
            $genartsRepeated = $this->Genart->find('all', array(
                'conditions' => array('grouping_genart_id' => $groupingIdRepeated)
            ));

            foreach ($genartsRepeated as $genartRepeated) {
                $genartsIdsRepeated[] = $genartRepeated['Genart']['id'];
            }
        }

        $pricingType = $network['Network']['pricing_type_id'];

        // 'LIST_PRICE' -> only 'discount'
        // 'NET_PRICE' -> choose between 'markup' or 'surchage'
        $discount = $pricingType == ConstantsQuotingPricingTypes::LIST_PRICE;
        $this->set("discount", $discount);

        if (!$this->request->is('get')) {
            $errorSaving = false;

            $discounts = isset($this->request->data['genart_discount']) ? $this->request->data['genart_discount'] : array();
            $markups = isset($this->request->data['genart_markup']) ? $this->request->data['genart_markup'] : array();
            $surcharges = isset($this->request->data['genart_surcharge']) ? $this->request->data['genart_surcharge'] : array();

            $workPrices = isset($this->request->data['works_prices']) ? $this->request->data['works_prices'] : array();

            $labourPrices = isset($this->request->data['labour_price_works']) ? $this->request->data['labour_price_works'] : array();

            $isLabourPrices = isset($this->request->data['genart_is_labour_price']) ? $this->request->data['genart_is_labour_price'] : array();

            foreach ($works as $work2) {
                // GarageNetworkWorkLabour saving
                $garageNetworkWorkLabour = $this->GarageNetworkWorkLabour->find('first', array(
                    'conditions' => array(
                        'garage_network_id' => $garageNetworkId,
                        'work_id' => $work2['Work']['id']
                    )
                ));

                // whether Labour price or Labour Price Electric has been set, GarageNetworkWorkLabour it's updated
                if (
                    (isset($labourPrices[$work2['Work']['id']]['general']) && $labourPrices[$work2['Work']['id']]['general'] != '') ||
                    (isset($labourPrices[$work2['Work']['id']]['electric']) && $labourPrices[$work2['Work']['id']]['electric'] != '')
                ) {
                    if (empty($garageNetworkWorkLabour)) {
                        $this->GarageNetworkWorkLabour->create();
                    }

                    $garageNetworkWorkLabour['GarageNetworkWorkLabour']['garage_network_id'] = $garageNetworkId;
                    $garageNetworkWorkLabour['GarageNetworkWorkLabour']['work_id'] = $work2['Work']['id'];
                    if (isset($labourPrices[$work2['Work']['id']]['general'])) {
                        $garageNetworkWorkLabour['GarageNetworkWorkLabour']['labour_hourly_price'] = $labourPrices[$work2['Work']['id']]['general'];
                    }
                    if (isset($labourPrices[$work2['Work']['id']]['electric'])) {
                        $garageNetworkWorkLabour['GarageNetworkWorkLabour']['labour_hourly_price_electric_vehicles'] = $labourPrices[$work2['Work']['id']]['electric'];
                    }

                    if (!$this->GarageNetworkWorkLabour->save($garageNetworkWorkLabour)) {
                        $errorSaving = true;
                        break;
                    }
                } elseif (!empty($garageNetworkWorkLabour)) {
                    if (!$this->GarageNetworkWorkLabour->delete($garageNetworkWorkLabour['GarageNetworkWorkLabour']['id'])) {
                        $errorSaving = true;
                        break;
                    }
                }

                // GarageNetworkWorkPrice saving
                $garageNetworkWorkPrice = $this->GarageNetworkWorkPrice->find('first', array(
                    'conditions' => array(
                        'garage_network_id' => $garageNetworkId,
                        'work_id' => $work2['Work']['id']
                    )
                ));

                // whether discount, markup or surgarge of "All genarts from HaynesPro" has been set, GarageNetworkWorkPrice it's updated
                if ((isset($workPrices[$work2['Work']['id']]['discount']) && $workPrices[$work2['Work']['id']]['discount'] != '') ||
                    (isset($workPrices[$work2['Work']['id']]['markup']) && $workPrices[$work2['Work']['id']]['markup'] != '') ||
                    (isset($workPrices[$work2['Work']['id']]['surcharge']) && $workPrices[$work2['Work']['id']]['surcharge'] != '')
                ) {
                    if (empty($garageNetworkWorkPrice)) {
                        $this->GarageNetworkWorkPrice->create();
                    }

                    $garageNetworkWorkPrice['GarageNetworkWorkPrice']['garage_network_id'] = $garageNetworkId;
                    $garageNetworkWorkPrice['GarageNetworkWorkPrice']['work_id'] = $work2['Work']['id'];
                    if (isset($workPrices[$work2['Work']['id']]['discount'])) {
                        $garageNetworkWorkPrice['GarageNetworkWorkPrice']['discount'] = $workPrices[$work2['Work']['id']]['discount'];
                    }
                    if (isset($workPrices[$work2['Work']['id']]['markup'])) {
                        $garageNetworkWorkPrice['GarageNetworkWorkPrice']['markup'] = $workPrices[$work2['Work']['id']]['markup'];
                    }
                    if (isset($workPrices[$work2['Work']['id']]['surcharge'])) {
                        $garageNetworkWorkPrice['GarageNetworkWorkPrice']['surcharge'] = $workPrices[$work2['Work']['id']]['surcharge'];
                    }

                    if (!$this->GarageNetworkWorkPrice->save($garageNetworkWorkPrice)) {
                        $errorSaving = true;
                        break;
                    }
                } elseif (!empty($garageNetworkWorkPrice)) {
                    if (!$this->GarageNetworkWorkPrice->delete($garageNetworkWorkPrice['GarageNetworkWorkPrice']['id'])) {
                        $errorSaving = true;
                        break;
                    }
                }

                if (!empty($work2['Work']['grouping_genart_id'])) {
                    $genarts = $this->Genart->find('all', array(
                        'conditions' => array(
                            'grouping_genart_id' => $work2['Work']['grouping_genart_id'],
                            'active' => ConstantsBooleans::ACTIVE
                        )
                    ));

                    // 'discount', 'markup' and 'surcharge' saving
                    foreach ($genarts as $genart) {
                        $garageNetworkGenart = $this->GarageNetworkGenart->find('first', array(
                            'conditions' => array(
                                'garage_network_id' => $garageNetworkId,
                                'genart_id' => $genart['Genart']['id']
                            )
                        ));

                        $saveGenart = (isset($discounts[$genart['Genart']['id']]) && $discounts[$genart['Genart']['id']] != '') ||
                            (isset($markups[$genart['Genart']['id']]) && $markups[$genart['Genart']['id']] != '') ||
                            (isset($surcharges[$genart['Genart']['id']]) && $surcharges[$genart['Genart']['id']] != '') ||
                            (isset($isLabourPrices[$genart['Genart']['id']]) && $isLabourPrices[$genart['Genart']['id']] != '');

                        // if the record already exists in the DB, it's updated
                        if ($garageNetworkGenart != null) {
                            if (isset($discounts[$genart['Genart']['id']])) {
                                $garageNetworkGenart['GarageNetworkGenart']['discount'] = $discounts[$genart['Genart']['id']];
                            }
                            if (isset($markups[$genart['Genart']['id']])) {
                                $garageNetworkGenart['GarageNetworkGenart']['markup'] = $markups[$genart['Genart']['id']];
                                $garageNetworkGenart['GarageNetworkGenart']['surcharge'] = null;
                            }
                            if (isset($surcharges[$genart['Genart']['id']])) {
                                $garageNetworkGenart['GarageNetworkGenart']['markup'] = null;
                                $garageNetworkGenart['GarageNetworkGenart']['surcharge'] = $surcharges[$genart['Genart']['id']];
                            }

                            if (isset($isLabourPrices[$genart['Genart']['id']])) {
                                $garageNetworkGenart['GarageNetworkGenart']['is_labour_price'] = $isLabourPrices[$genart['Genart']['id']];
                            }
                        } else {
                            // if the record doesn't exist in the DB, a new one is created
                            $this->GarageNetworkGenart->create();
                            $garageNetworkGenart = array('GarageNetworkGenart' => array(
                                'garage_network_id' => $garageNetworkId,
                                'genart_id' => $genart['Genart']['id'],
                                'discount' => isset($discounts[$genart['Genart']['id']]) ? $discounts[$genart['Genart']['id']] : null,
                                'markup' => isset($markups[$genart['Genart']['id']]) ? $markups[$genart['Genart']['id']] : null,
                                'surcharge' => isset($surcharges[$genart['Genart']['id']]) ? $surcharges[$genart['Genart']['id']] : null,
                                'is_labour_price' => isset($isLabourPrices[$genart['Genart']['id']]) ? $isLabourPrices[$genart['Genart']['id']] : null
                            ));
                        }

                        // whether Discount, Markup or Surcharge has been set, GarageNetworkGenart it's updated
                        if ($saveGenart) {
                            if (!$this->GarageNetworkGenart->save($garageNetworkGenart)) {
                                $errorSaving = true;
                                break;
                            }
                        } elseif (
                            $garageNetworkGenart != null &&
                            isset($garageNetworkGenart['GarageNetworkGenart']['id'])
                        ) {
                            if (!$this->GarageNetworkGenart->delete($garageNetworkGenart['GarageNetworkGenart']['id'])) {
                                $errorSaving = true;
                                break;
                            }
                        }
                    }
                }
            }

            if (!$errorSaving) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));

                $this->redirect(
                    array(
                        'controller' => 'garages_networks',
                        'action' => 'genarts',
                        $garageNetwork['GarageNetwork']['id']
                    )
                );
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
            }
        }

        $networkId = $garageNetwork["GarageNetwork"]["network_id"];

        $workDealer = $this->Work->find('first', array(
            'conditions' => array(
                'network_id' => $network['Network']['id'],
                'grouping_genart_id' => null,
                'active' => ConstantsBooleans::ACTIVE,
            ),
        ));

        $this->set(array(
            'garage_network_id' => $garageNetwork['GarageNetwork']['id'],
            'garage_id' => $garageId,
            'garage_name' => $garage['Garage']['name'],
            'works' => $works,
            'works_ids_active' => $worksIdsActive,
            'works_ids_inactive' => $worksIdsInactive,
            'network' => $network,
            'with_vat' => $network['Network']['with_vat'],
            'genarts_ids_repeated' => $genartsIdsRepeated,
            'network_id' => $networkId,
            'garage_network' => $garageNetwork,
            'language_code' => $languageCode,
            'quoting_views_active' => $garageNetwork['GarageNetwork']['quoting_views_active'],
            'networkUseFluids' => $this->Fluid->networkUseFluids($network['Network']['id'], $workDealer)
        ));
    }

    /**
     * View that allows to edit the prices of the genarts and labours of a garage network.
     * Permissions checked in genarts function.
     *
     * @param garageNetworkId   Garage Network ID
     */
    public function edit_genarts($garageNetworkId)
    {
        $this->genarts($garageNetworkId);

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'garages_networks',
                'action' => 'view',
                $garageNetworkId
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
            'edit_genarts' => true
        ));

        $this->render('/GaragesNetworks/genarts');
    }

    private function dataRange($beginYmd, $endYmd)
    {
        $begin = new DateTime($beginYmd);
        $end = new DateTime($endYmd);
        $end = $end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        $dates = array();
        foreach ($period as $dt) {
            $dates[] = $dt->format('Y-m-d');
        }
        return $dates;
    }

    /**
     * API to get the first available date for a booking in a garage for a network
     *
     * @param network_id Received from POST request
     * @param garage_id Received from POST request
     *
     * @return JSON
     */
    public function get_first_available_date()
    {
        $dataReceived = $this->request->input('json_decode');

        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        $sendData = array(
            "first_available_date" => ""
        );

        if (!empty($dataReceived) && $this->request->is("POST")) {
            $garageId = isset($dataReceived->garage_id) ? $dataReceived->garage_id : '';
            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : '';

            $network = $this->Network->findById($networkId);

            if (!$network) {
                return $this->returnJsonResult($sendData);
            }

            $garage = $this->Garage->find('first', array(
                'conditions' => array(
                    'Garage.guid' => $garageId,
                ),
                'fields' => 'Garage.*'
            ));

            if (empty($garage)) {
                return $this->returnJsonResult($sendData);
            }

            $garageNetwork = $this->GarageNetwork->find('first', array(
                'conditions' => array(
                    'GarageNetwork.garage_id' => $garage['Garage']['id'],
                    'GarageNetwork.network_id' => $networkId,
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE
                ),
                'fields' => 'GarageNetwork.*'
            ));

            if (empty($garageNetwork)) {
                return $this->returnJsonResult($sendData);
            }

            $todayDateTime = new DateTime(date('Y-m-d'));
            $startDate = clone $todayDateTime;

            //if booking_days_min_from value is set to 0, it searches from tomorrow
            //if booking_days_min_from value is greater tha 0, it is used
            if ($garageNetwork['GarageNetwork']['booking_days_min_from'] < 1) {
                $startDate->modify('+1 day');
            } else {
                $startDate->modify('+' . $garageNetwork['GarageNetwork']['booking_days_min_from'] . ' day');
            }

            $startDate = $startDate->format('Y-m-d');

            $lastDay = clone $todayDateTime;
            $lastDay->modify('+' . $garageNetwork['GarageNetwork']['booking_days_max_to'] . ' day');
            $endDate = $lastDay->format('Y-m-d');
            $datesToSearch = $this->dataRange($startDate, $endDate);

            $sqlAvailable = '
            SELECT garages.id AS garage_id';
            $selectsToSum = array();
            if (count($datesToSearch) > 0) {
                $i = 0;
                foreach ($datesToSearch as $dateToSearch) {
                    $dayofweek = strtolower(date('l', strtotime($dateToSearch)));
                    $selectsToSum[] = 'case when
                    (garages_networks.' . $dayofweek . '_planner_max_1 > 0 OR garages_networks.' . $dayofweek . '_planner_max_2 > 0)
                    AND IFNULL(garages.' . $dayofweek . '_open_1, "") != "" AND IFNULL(garages_networks.' . $dayofweek . '_planner_open_1, "") != ""
                    then "' . $dateToSearch . '" ELSE null END as DATE' . $i . '';
                    $i++;
                }

                $sqlAvailable .= ', ' . implode(', ', $selectsToSum);
            }
            $sqlAvailable .= '
            from garages
            INNER JOIN garages_networks
            ON garages.id = garages_networks.garage_id
            WHERE garages.id = ' . $garage['Garage']['id'] . '
            AND garages_networks.id = ' . $garageNetwork['GarageNetwork']['id'];

            $dataAvailable = $this->Garage->query($sqlAvailable);

            $datesAvailable = Hash::extract($dataAvailable, '{n}.{n}.{*}');
            $datesAvailable = array_filter($datesAvailable); // remove null values

            $sqlNotAvailable = $this->getSqlGarageDatesNotAvailable($networkId, $endDate, $garage['Garage']['id']);
            $dataNotAvailable = $this->Garage->query($sqlNotAvailable);
            $datesNotAvailable = Hash::extract($dataNotAvailable, '{n}.{*}.date');

            // datesAvailable: contains the dates when the garage is available for a booking (without booking info)
            // datesNotAvailable: contains the dates before the end date when the garage is not available for a booking
            // then, apply diff
            $finalDates = array_diff($datesAvailable, $datesNotAvailable);

            if (!empty($finalDates)) {
                $firstAvailableDate = min($finalDates);
                $sendData = array(
                    "first_available_date" => $firstAvailableDate
                );
            }

            return $this->returnJsonResult($sendData);
        }
        return $this->returnJsonResult($sendData);
    }

    private function getGarageIdsAvailableBeforeDate($networkId, $dateSearch)
    {
        $networkId = intval($networkId);

        // ensure $dateSearch format
        $dateSearchDateTime = new DateTime($dateSearch);
        $dateSearch = $dateSearchDateTime->format('Y-m-d');
        //sql query code
        // dates for each garage when the garage is not available before the date specified (considering garage configuration and bookings)
        $sqlNotAvailable = $this->getSqlGarageDatesNotAvailable($networkId, $dateSearch, null);

        // garages_num_days_available.days_available - ifnull(garages_num_days_not_available.num_days_bookings_not_available, 0) AS days_available_final
        $sql = "SELECT garages_num_days_available.garage_id
        from
        (
        SELECT garages.id AS garage_id,";

        $selectsToSum = array();
        $todayDateTime = new DateTime(date('Y-m-d'));
        $tomorrowDateTime = $todayDateTime->modify('+1 day');
        $datesToSearch = $this->dataRange($tomorrowDateTime->format('Y-m-d'), $dateSearch);
        if (count($datesToSearch) > 0) {
            foreach ($datesToSearch as $dateToSearch) {
                $dayofweek = strtolower(date('l', strtotime($dateToSearch)));
                $selectsToSum[] = "case when
                DATE_ADD(curdate(), INTERVAL garages_networks.booking_days_min_from DAY) <= '" . $dateToSearch . "'
                AND DATE_ADD(curdate(), INTERVAL garages_networks.booking_days_max_to DAY) >= '" . $dateToSearch . "'
                AND (garages_networks." . $dayofweek . "_planner_max_1 > 0 OR garages_networks." . $dayofweek . "_planner_max_2 > 0)
                AND IFNULL(garages." . $dayofweek . "_open_1, '') != '' AND IFNULL(garages_networks." . $dayofweek . "_planner_open_1, '') != ''
                then 1 ELSE 0 END";
            }
            $sql .= implode(" + ", $selectsToSum);
        } else {
            $sql .= "0";
        }

        // num days when the garage is available (without booking information)
        $sql .= " AS days_available

        from garages
        INNER JOIN garages_networks
        ON garages.id = garages_networks.garage_id
        AND garages_networks.network_id = " . $networkId . "
        AND garages_networks.status = " . ConstantsNetworksStatus::LIVE . "
        ) AS garages_num_days_available

        LEFT JOIN

        (
        SELECT garages_bookings_not_available.garage_id, count(garages_bookings_not_available.date) AS num_days_bookings_not_available
        from(
        $sqlNotAvailable
        ) as garages_bookings_not_available
        GROUP BY garages_bookings_not_available.garage_id
        ) AS garages_num_days_not_available

        ON garages_num_days_available.garage_id = garages_num_days_not_available.garage_id
        WHERE garages_num_days_available.days_available - ifnull(garages_num_days_not_available.num_days_bookings_not_available, 0) > 0";

        // the where condition means: garages where the number of days available (possible dates) minus the number of days not available (considering bookings) is > 0

        $data = $this->Garage->query($sql);
        return Hash::extract($data, '{n}.garages_num_days_available.garage_id');
    }

    private function getSqlGarageDatesNotAvailable($network_id, $date_search, $garage_id = null)
    {
        return "SELECT garages.id AS garage_id, bookings.date
        from garages
        INNER JOIN garages_networks
        ON garages.id = garages_networks.garage_id
        AND garages_networks.network_id = " . (int)$network_id . "
        AND garages_networks.status = " . ConstantsNetworksStatus::LIVE . "
        LEFT JOIN bookings
        ON bookings.garage_id = garages.id
        AND bookings.network_id = garages_networks.network_id
        WHERE " .
            (!is_null($garage_id) ? ("garages.id = " . (int)$garage_id . " AND ") : "")
            . "
        DATE_ADD(curdate(), INTERVAL garages_networks.booking_days_min_from DAY) <= '" . $date_search . "'
        AND bookings.date >= DATE_ADD(curdate(), INTERVAL garages_networks.booking_days_min_from DAY)
        AND bookings.date <= LEAST('" . $date_search . "', DATE_ADD(curdate(), INTERVAL garages_networks.booking_days_max_to DAY))
        AND
        (CASE
        WHEN DAYOFWEEK(bookings.date) = 2 AND (garages_networks.monday_planner_max_1 > 0 OR garages_networks.monday_planner_max_2 > 0) THEN 1
        WHEN DAYOFWEEK(bookings.date) = 3 AND (garages_networks.tuesday_planner_max_1 > 0 OR garages_networks.tuesday_planner_max_2 > 0) THEN 1
        WHEN DAYOFWEEK(bookings.date) = 4 AND (garages_networks.wednesday_planner_max_1 > 0 OR garages_networks.wednesday_planner_max_2 > 0) THEN 1
        WHEN DAYOFWEEK(bookings.date) = 5 AND (garages_networks.thursday_planner_max_1 > 0 OR garages_networks.thursday_planner_max_2 > 0) THEN 1
        WHEN DAYOFWEEK(bookings.date) = 6 AND (garages_networks.friday_planner_max_1 > 0 OR garages_networks.friday_planner_max_2 > 0) THEN 1
        WHEN DAYOFWEEK(bookings.date) = 7 AND (garages_networks.saturday_planner_max_1 > 0 OR garages_networks.saturday_planner_max_2 > 0) THEN 1
        WHEN DAYOFWEEK(bookings.date) = 1 AND (garages_networks.sunday_planner_max_1 > 0 OR garages_networks.sunday_planner_max_2 > 0) THEN 1
        ELSE 0
        END) = 1
        AND
        (CASE
        WHEN DAYOFWEEK(bookings.date) = 2 AND IFNULL(garages.monday_open_1, '') != '' AND IFNULL(garages_networks.monday_planner_open_1, '') != '' THEN 1
        WHEN DAYOFWEEK(bookings.date) = 3 AND IFNULL(garages.tuesday_open_1, '') != '' AND IFNULL(garages_networks.tuesday_planner_open_1, '') != '' THEN 1
        WHEN DAYOFWEEK(bookings.date) = 4 AND IFNULL(garages.wednesday_open_1, '') != '' AND IFNULL(garages_networks.wednesday_planner_open_1, '') != '' THEN 1
        WHEN DAYOFWEEK(bookings.date) = 5 AND IFNULL(garages.thursday_open_1, '') != '' AND IFNULL(garages_networks.thursday_planner_open_1, '') != '' THEN 1
        WHEN DAYOFWEEK(bookings.date) = 6 AND IFNULL(garages.friday_open_1, '') != '' AND IFNULL(garages_networks.friday_planner_open_1, '') != '' THEN 1
        WHEN DAYOFWEEK(bookings.date) = 7 AND IFNULL(garages.saturday_open_1, '') != '' AND IFNULL(garages_networks.saturday_planner_open_1, '') != '' THEN 1
        WHEN DAYOFWEEK(bookings.date) = 1 AND IFNULL(garages.sunday_open_1, '') != '' AND IFNULL(garages_networks.sunday_planner_open_1, '') != '' THEN 1
        ELSE 0
        END) = 1
        GROUP BY garages.id, bookings.date,
        garages_networks.monday_planner_max_1,
        garages_networks.monday_planner_max_2,
        garages_networks.tuesday_planner_max_1,
        garages_networks.tuesday_planner_max_2,
        garages_networks.wednesday_planner_max_1,
        garages_networks.wednesday_planner_max_2,
        garages_networks.thursday_planner_max_1,
        garages_networks.thursday_planner_max_2,
        garages_networks.friday_planner_max_1,
        garages_networks.friday_planner_max_2,
        garages_networks.saturday_planner_max_1,
        garages_networks.saturday_planner_max_2,
        garages_networks.sunday_planner_max_1,
        garages_networks.sunday_planner_max_2
        HAVING
        IFNULL(sum(case when
            DAYOFWEEK(bookings.date) = 2 AND bookings.time < garages_networks.monday_planner_closed_1 AND bookings.time_to > garages_networks.monday_planner_open_1
            OR DAYOFWEEK(bookings.date) = 3 AND bookings.time < garages_networks.tuesday_planner_closed_1 AND bookings.time_to > garages_networks.tuesday_planner_open_1
            OR DAYOFWEEK(bookings.date) = 4 AND bookings.time < garages_networks.wednesday_planner_closed_1 AND bookings.time_to > garages_networks.wednesday_planner_open_1
            OR DAYOFWEEK(bookings.date) = 5 AND bookings.time < garages_networks.thursday_planner_closed_1 AND bookings.time_to > garages_networks.thursday_planner_open_1
            OR DAYOFWEEK(bookings.date) = 6 AND bookings.time < garages_networks.friday_planner_closed_1 AND bookings.time_to > garages_networks.friday_planner_open_1
            OR DAYOFWEEK(bookings.date) = 7 AND bookings.time < garages_networks.saturday_planner_closed_1 AND bookings.time_to > garages_networks.saturday_planner_open_1
            OR DAYOFWEEK(bookings.date) = 1 AND bookings.time < garages_networks.sunday_planner_closed_1 AND bookings.time_to > garages_networks.sunday_planner_open_1
            then 1 ELSE 0 END
        ), 0) >= IFNULL(case DAYOFWEEK(bookings.date)
            when 2 then garages_networks.monday_planner_max_1
            when 3 then garages_networks.tuesday_planner_max_1
            when 4 then garages_networks.wednesday_planner_max_1
            when 5 then garages_networks.thursday_planner_max_1
            when 6 then garages_networks.friday_planner_max_1
            when 7 then garages_networks.saturday_planner_max_1
            when 1 then garages_networks.sunday_planner_max_1
            end
        , 0)
        and
        IFNULL(sum(case when
            DAYOFWEEK(bookings.date) = 2 AND bookings.time < garages_networks.monday_planner_closed_2 AND bookings.time_to > garages_networks.monday_planner_open_2
            OR DAYOFWEEK(bookings.date) = 3 AND bookings.time < garages_networks.tuesday_planner_closed_2 AND bookings.time_to > garages_networks.tuesday_planner_open_2
            OR DAYOFWEEK(bookings.date) = 4 AND bookings.time < garages_networks.wednesday_planner_closed_2 AND bookings.time_to > garages_networks.wednesday_planner_open_2
            OR DAYOFWEEK(bookings.date) = 5 AND bookings.time < garages_networks.thursday_planner_closed_2 AND bookings.time_to > garages_networks.thursday_planner_open_2
            OR DAYOFWEEK(bookings.date) = 6 AND bookings.time < garages_networks.friday_planner_closed_2 AND bookings.time_to > garages_networks.friday_planner_open_2
            OR DAYOFWEEK(bookings.date) = 7 AND bookings.time < garages_networks.saturday_planner_closed_2 AND bookings.time_to > garages_networks.saturday_planner_open_2
            OR DAYOFWEEK(bookings.date) = 1 AND bookings.time < garages_networks.sunday_planner_closed_2 AND bookings.time_to > garages_networks.sunday_planner_open_2
            then 1 ELSE 0 end
        ), 0) >= IFNULL(case DAYOFWEEK(bookings.date)
            when 2 then garages_networks.monday_planner_max_2
            when 3 then garages_networks.tuesday_planner_max_2
            when 4 then garages_networks.wednesday_planner_max_2
            when 5 then garages_networks.thursday_planner_max_2
            when 6 then garages_networks.friday_planner_max_2
            when 7 then garages_networks.saturday_planner_max_2
            when 1 then garages_networks.sunday_planner_max_2
            end
        , 0)";
    }

    /**
     * Garage search, returns false when error or array (empty or not) with results.
     */
    private function garageSearchInternal($dataReceived, $searchCityById)
    {
        $limitGarages = 0;
        $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : '';

        $garagesSearch = array();

        if (empty($networkId)) {
            return false;
        }

        $network = $this->Network->findById($networkId);

        if (!$network) {
            return false;
        }

        $plate = isset($dataReceived->plate) ? $dataReceived->plate : null;
        $vin = isset($dataReceived->vin) ? $dataReceived->vin : null;
        $vehicle_id_leadgen = isset($dataReceived->vehicle_id_leadgen) ? $dataReceived->vehicle_id_leadgen : null;
        $workCode = isset($dataReceived->work_id) ? $dataReceived->work_id : '';

        $workIdBd = '';
        if (!empty($workCode)) {
            $work = $this->Work->find('first', array(
                'conditions' => array(
                    'network_id' => $networkId,
                    'code' => $workCode
                )
            ));
            // if no work was found -> id that does not exist, no garages will be found
            $workIdBd = !empty($work) ? $work['Work']['id'] : -1;
        }

        //vehicleId: SEO black list
        $vehicleId = isset($dataReceived->vehicle_id) ? $dataReceived->vehicle_id : '';

        //vehiclesSearch: AGN specialists
        $vehiclesSearch = isset($dataReceived->vehicles) ? $dataReceived->vehicles : '';

        $serviceId = isset($dataReceived->service_id) ? $dataReceived->service_id : '';
        $serviceIdBd = '';
        if (!empty($serviceId)) {
            $service = $this->Service->findById($serviceId);

            // if no service was found -> id that does not exist, no garages will be found
            $serviceIdBd = !empty($service) ? $service['Service']['id'] : -1;
        }

        $servicesSearch = isset($dataReceived->services) ? $dataReceived->services : '';
        if (!empty($serviceIdBd)) {
            if (!empty($servicesSearch)) {
                if (!in_array($serviceIdBd, $servicesSearch)) {
                    $servicesSearch[] = $serviceIdBd;
                }
            } else {
                $servicesSearch = array($serviceIdBd);
            }
        }

        $vehicleTypes = isset($dataReceived->vehicle_types) ? $dataReceived->vehicle_types : '';

        $networksSearch = isset($dataReceived->networks) ? $dataReceived->networks : '';

        $servicesDriversSearch = isset($dataReceived->services_drivers) ? $dataReceived->services_drivers : '';

        //Available date
        $availableDate = isset($dataReceived->available_date) ? $dataReceived->available_date : '';

        //Today is compared to available date
        $today = date('Y-m-d');

        //Garages will be searched only if available date is greater than today
        if (!empty($availableDate) && $availableDate <= $today) {
            return false;
        }

        $garagesIdsDatesAvailable = null;
        if (!empty($availableDate)) {
            $garagesIdsDatesAvailable = $this->getGarageIdsAvailableBeforeDate($networkId, $availableDate);
        }

        //Modified characters
        $originals = 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜàáâãäåæçèéêëìíîïðñòóôõöøùúûüýýþÿ';
        $modified = 'aaaaaaceeeeiiiinoooooouuuuaaaaaaaceeeeiiiidnoooooouuuuyyby';

        // this is the brand that the garage must not include in the black list in AGN
        $vehicleBrand = isset($dataReceived->vehicle_brand) ? $dataReceived->vehicle_brand : '';
        $vehicleBrandId = '';

        if (!empty($vehicleBrand)) {
            $vehicleBrandClean = strtr($vehicleBrand, $originals, $modified);
            $vehicleBrandClean = strtolower($vehicleBrandClean);
            $vehicleBrandClean = str_replace(' ', '', $vehicleBrandClean);

            $vehicle = $this->Vehicle->find('first', array(
                'conditions' => array(
                    'replace(name_en, " ", "")' => $vehicleBrandClean
                )
            ));
            if ($vehicle) {
                $vehicleBrandId = $vehicle['Vehicle']['id'];
            }
        }

        //Array of garages from sql query with available date.
        $conditions = array();
        if (is_array($garagesIdsDatesAvailable)) {
            if (count($garagesIdsDatesAvailable) == 0) {
                $conditions['Garage.id'] = -1; // we need to find no garages
            } else {
                $conditions['Garage.id'] = $garagesIdsDatesAvailable;
            }
        }
        // latitude and longitude in decimal degrees
        $latitude = isset($dataReceived->latitude) ? $dataReceived->latitude : '';
        $longitude = isset($dataReceived->longitude) ? $dataReceived->longitude : '';

        // radius in kilometres (miles * 1.609344 = kilometres)
        if (isset($dataReceived->radius)) {
            $defaultDistance = false;
            $radius = $dataReceived->radius;
        } else {
            $defaultDistance = true;
            if (isset($network['Network']['default_mileage'])) {
                $radius = Numero::convert_unit_to_km($network['Network']['default_mileage'], $network['Network']['distance_unit_id']);
            } else {
                $radius = (isset($network['Network']['aag_region_id']) && $network['Network']['aag_region_id'] == ConstantsAAGRegionId::UK) ? DefaultGarageSearchRadius::AGN : DefaultGarageSearchRadius::GENERAL;
            }
        }

        if ($radius < 0) {
            return false;
        }

        $orderByRecommended = true;
        $excludeGaragesWithoutLocation = false;
        $cityId = isset($dataReceived->city_id) ? $dataReceived->city_id : '';
        if (!empty($cityId)) {
            $city = $this->City->findById($cityId);
            if ($city) {
                if ($searchCityById) {
                    // add city_id condition
                    $conditions['Garage.city_id'] = $cityId;
                } else {
                    $latitude = $city['City']['latitude'];
                    $longitude = $city['City']['longitude'];

                    $radius = '';
                    $limitGarages = 10;
                    $orderByRecommended = false;
                    $excludeGaragesWithoutLocation = true;
                }
            }
        }


        $garageNetworkConditionsJoin = array(
            'GarageNetwork.garage_id = Garage.id',
            'GarageNetwork.network_id' => $networkId,
            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
            'GarageNetwork.last' => ConstantsBooleans::YES,
        );

        //Before implementing the condition of joins we have to decide if we have to remove workshops belonging to CV networks
        //Here first check if its AGN network to dont affect the garage search logic for the rest of networks.
        if ($networkId == NETWORK_ID_AGN && isset($dataReceived->networks)) {
            $isCvSearch = false;

            $cvChildNetworks = $this->Network->getListofChildNetworksByNetworkType($networkId, ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE);

            foreach ($dataReceived->networks as $filteredNetworkId) {
                $isCvSearch = in_array($filteredNetworkId, $cvChildNetworks);

                if ($isCvSearch) {
                    break;
                }
            }

            if (!$isCvSearch) {
                $cvGarageIds = $this->GarageNetwork->getListOfGarageIdsByNetworkIds($cvChildNetworks);
                if (!empty($cvGarageIds)) {
                    $garageNetworkConditionsJoin['GarageNetwork.garage_id NOT IN'] = $cvGarageIds;
                }
            }
        }

        $joins = array(
            array(
                'alias' => 'GarageNetwork',
                'table' => 'garages_networks',
                'type' => 'INNER',
                'conditions' => $garageNetworkConditionsJoin,
            ),
            array(
                'alias' => 'NetworkRecommendedView',
                'table' => 'networks_recommended_view',
                'type' => 'LEFT',
                'conditions' => array(
                    'NetworkRecommendedView.garage_id = Garage.id',
                    'NetworkRecommendedView.network_id' => $networkId,
                ),
            )
        );

        $worksIds = array();
        if (!empty($workIdBd)) {
            $worksIds[] = $workIdBd;
        } elseif (isset($plate) || (isset($vin)) && isset($vehicle_id_leadgen)) {
            $leadgen = new Leadgen();
            $vehicleWorks = $leadgen->getVehicleWorks($networkId, $plate, $vin, $vehicle_id_leadgen);

            if (!empty($vehicleWorks)) {
                $vehicleWorks = Hash::extract($vehicleWorks['works'], '{n}.id');

                $works = $this->Work->findByCodeAndNetworkId($vehicleWorks, $networkId);
                if (!empty($works)) {
                    $worksIds = Hash::extract($works, 'Work.id');
                }
            }
        }

        $fields = array(
            'Garage.*',
            'GarageNetwork.id',
            'GarageNetwork.about',
            'GarageNetwork.quoting_active',
            'GarageNetwork.enquiries_active',
            'GarageNetwork.sap_code',
            'GarageNetwork.recommended',
            'GarageNetwork.reviews_number',
            'GarageNetwork.rating',
            'GarageNetwork.network_id',
            'GarageNetwork.location_id',
            'GarageNetwork.usp1',
            'GarageNetwork.usp2',
            'GarageNetwork.usp3',
            'GarageNetwork.monday_planner_open_1',
            'GarageNetwork.monday_planner_open_2',
            'GarageNetwork.tuesday_planner_open_1',
            'GarageNetwork.tuesday_planner_open_2',
            'GarageNetwork.wednesday_planner_open_1',
            'GarageNetwork.wednesday_planner_open_2',
            'GarageNetwork.thursday_planner_open_1',
            'GarageNetwork.thursday_planner_open_2',
            'GarageNetwork.friday_planner_open_1',
            'GarageNetwork.friday_planner_open_2',
            'GarageNetwork.saturday_planner_open_1',
            'GarageNetwork.saturday_planner_open_2',
            'GarageNetwork.sunday_planner_open_1',
            'GarageNetwork.sunday_planner_open_2',
            'GarageNetwork.monday_planner_max_1',
            'GarageNetwork.monday_planner_max_2',
            'GarageNetwork.tuesday_planner_max_1',
            'GarageNetwork.tuesday_planner_max_2',
            'GarageNetwork.wednesday_planner_max_1',
            'GarageNetwork.wednesday_planner_max_2',
            'GarageNetwork.thursday_planner_max_1',
            'GarageNetwork.thursday_planner_max_2',
            'GarageNetwork.friday_planner_max_1',
            'GarageNetwork.friday_planner_max_2',
            'GarageNetwork.saturday_planner_max_1',
            'GarageNetwork.saturday_planner_max_2',
            'GarageNetwork.sunday_planner_max_1',
            'GarageNetwork.sunday_planner_max_2',
            'GarageNetwork.annex_detail_id',
            'NetworkRecommendedView.garage_id',
        );

        if (isset($worksIds) && !empty($worksIds) || (isset($plate) || (isset($vin)) && isset($vehicle_id_leadgen))) {
            $joins[] = array(
                'alias' => 'GarageNetworkWork',
                'table' => 'garages_networks_works',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetworkWork.garage_network_id = GarageNetwork.id',
                    'GarageNetworkWork.work_id' => $worksIds,
                ),
            );
            $joins[] = array(
                'alias' => 'Work',
                'table' => 'works',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetworkWork.work_id = Work.Id',
                    'Work.active' => ConstantsBooleans::ACTIVE
                ),
            );
            $fields[] = 'GarageNetworkWork.work_id';
        }

        if (!empty($vehicleId)) {
            $joins[] = array(
                'alias' => 'GarageNetworkVehicleBlackListSeo',
                'table' => 'garages_networks_vehicles_black_list',
                'type' => 'LEFT',
                'conditions' => array(
                    'GarageNetworkVehicleBlackListSeo.garage_network_id = GarageNetwork.id',
                    'GarageNetworkVehicleBlackListSeo.vehicle_id' => $vehicleId,
                ),
            );
            $fields[] = 'GarageNetworkVehicleBlackListSeo.vehicle_id';
        }

        if (!empty($vehiclesSearch)) {
            $joins[] = array(
                'alias' => 'GarageNetworkVehicle',
                'table' => 'garages_networks_vehicles',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetworkVehicle.garage_network_id = GarageNetwork.id',
                    'GarageNetworkVehicle.vehicle_id' => $vehiclesSearch,
                ),
            );
            $fields[] = 'GarageNetworkVehicle.vehicle_id';
        }

        if (!empty($servicesSearch)) {
            $joins[] = array(
                'alias' => 'GarageNetworkService',
                'table' => 'garages_networks_services',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetworkService.garage_network_id = GarageNetwork.id',
                    'GarageNetworkService.service_id' => $servicesSearch,
                ),
            );
            $fields[] = 'GarageNetworkService.service_id';
        }

        if (!empty($servicesDriversSearch)) {
            $joins[] = array(
                'alias' => 'GarageNetworkServiceDriver',
                'table' => 'garages_networks_services_drivers',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetworkServiceDriver.garage_network_id = GarageNetwork.id',
                    'GarageNetworkServiceDriver.service_driver_id' => $servicesDriversSearch,
                ),
            );
            $fields[] = 'GarageNetworkServiceDriver.service_driver_id';
        }

        if (!empty($vehicleTypes)) {
            $joins[] = array(
                'alias' => 'GarageNetworkVehicleType',
                'table' => 'garages_networks_vehicle_types',
                'type' => 'INNER',
                'conditions' => array(
                    'GarageNetworkVehicleType.garage_network_id = GarageNetwork.id',
                    'GarageNetworkVehicleType.vehicle_type_id' => $vehicleTypes,
                ),
            );
            $fields[] = 'GarageNetworkVehicleType.vehicle_type_id';
        }

        if (!empty($networksSearch)) {
            $joins[] = array(
                'alias' => 'GarageNetworksSearch',
                'table' => 'garages_networks',
                'type' => 'LEFT',
                'conditions' => array(
                    'GarageNetworksSearch.garage_id = Garage.id',
                    'GarageNetworksSearch.network_id' => $networksSearch,
                    'GarageNetworksSearch.status' => ConstantsNetworksStatus::LIVE,
                ),
            );
            $fields[] = 'GarageNetworksSearch.network_id';
        }

        if (!empty($vehicleBrandId)) {
            $joins[] = array(
                'alias' => 'GarageNetworkVehicleBlackList',
                'table' => 'garages_networks_vehicles_black_list',
                'type' => 'LEFT',
                'conditions' => array(
                    'GarageNetworkVehicleBlackList.garage_network_id = GarageNetwork.id',
                    'GarageNetworkVehicleBlackList.vehicle_id' => $vehicleBrandId,
                ),
            );
            $fields[] = 'GarageNetworkVehicleBlackList.vehicle_id';
        }

        $software = $this->Software->find('first', array(
            'conditions' => array(
                'Software.name_en' => ConstantsSoftwareValues::CALLTRACKS
            ),
            'fields' => 'id'
        ));

        if (!empty($software)) {
            $joins[] = array(
                'alias' => 'GarageSoftware',
                'table' => 'garages_software',
                'type' => 'LEFT',
                'conditions' => array(
                    'GarageSoftware.garage_id = Garage.id',
                    'GarageSoftware.software_id' => $software['Software']['id'],
                    'GarageSoftware.username IS NOT NULL',
                    'GarageSoftware.username !=' => ''
                )
            );

            $fields[] = 'min(GarageSoftware.username) as min_garage_software_username';
        }

        $order = array();
        $orderRecommendedSql = 'CASE WHEN GarageNetwork.recommended = 1 OR NetworkRecommendedView.garage_id is not null then 1 else 0 END';
        if ($orderByRecommended) {
            $order[$orderRecommendedSql] = "desc";
        }
        $order[] = 'Garage.name';

        $conditionsRadius = $distances = array();

        if (!empty($latitude) && !empty($longitude) && is_numeric($latitude) && is_numeric($longitude)) {
            $distanceKmsSQL = '60 * 1.1515 * 180/PI()
                * acos(
                    cos(radians(' . $latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $longitude . '))
                    + sin(radians(' . $latitude . ')) * sin(radians(latitude))
                )
                * 1.609344';
            $fields[] = 'truncate(' . $distanceKmsSQL . ', 2) as distanceCalculated';

            $order = array();
            if ($orderByRecommended) {
                $order[$orderRecommendedSql] = "desc";
            }
            $order[] = $distanceKmsSQL;
            $order[] = 'Garage.name';

            if ($excludeGaragesWithoutLocation) {
                $conditions[] = 'Garage.latitude IS NOT NULL';
                $conditions[] = 'Garage.longitude IS NOT NULL';
            }

            if (!empty($radius)) {
                $radius = floatval($radius);
                $distances = array($radius, $radius * 2, $radius * 4, $radius * 8);
                if ($defaultDistance) {
                    $conditionsRadius[0] = $distanceKmsSQL . ' <=' . $distances[0];
                    $conditionsRadius[1] = $distanceKmsSQL . ' <=' . $distances[1];
                    $conditionsRadius[2] = $distanceKmsSQL . ' <=' . $distances[2];
                    $conditionsRadius[3] = $distanceKmsSQL . ' <=' . $distances[3];
                } else {
                    $conditionsRadius[0] = $distanceKmsSQL . ' <= ' . $radius;
                }
            }
        }

        $find = array(
            'joins' => $joins,
            'fields' => $fields,
            'order' => $order
        );

        if ($limitGarages > 0) {
            $find['limit'] = $limitGarages;
        }

        $havingConditions = array();
        if (!empty($networksSearch)) {
            $havingConditions[] = 'count(distinct GarageNetworksSearch.network_id) = ' . count($networksSearch);
        }
        if (empty($workIdBd) && (isset($plate) || (isset($vin)) && isset($vehicle_id_leadgen))) {
            $havingConditions[] = 'count(distinct GarageNetworkWork.work_id) > 0';
        }
        if (!empty($vehicleTypes)) {
            $havingConditions[] = 'count(distinct GarageNetworkVehicleType.vehicle_type_id) = ' . count($vehicleTypes);
        }
        if (!empty($vehicleId)) {
            $havingConditions[] = 'count(distinct GarageNetworkVehicleBlackListSeo.vehicle_id) = 0';
        }
        if (!empty($vehiclesSearch)) {
            $havingConditions[] = 'count(distinct GarageNetworkVehicle.vehicle_id) = ' . count($vehiclesSearch);
        }
        if (!empty($servicesSearch)) {
            $havingConditions[] = 'count(distinct GarageNetworkService.service_id) = ' . count($servicesSearch);
        }
        if (!empty($servicesDriversSearch)) {
            $havingConditions[] = 'count(distinct GarageNetworkServiceDriver.service_driver_id) = ' . count($servicesDriversSearch);
        }
        if (!empty($vehicleBrandId)) {
            $havingConditions[] = 'count(distinct GarageNetworkVehicleBlackList.vehicle_id) = 0';
        }
        if (!empty($havingConditions)) {
            $find['group'] = 'Garage.id having ' . implode(" and ", $havingConditions);
        } else {
            $find['group'] = 'Garage.id';
        }

        $conditions[] = array('Garage.status' => ConstantsGarageStatus::ACTIVE);
        if (empty($conditionsRadius)) {
            $find['conditions'] = $conditions;
            $garagesRadius = $this->Garage->find('all', $find);
        } elseif (count($conditionsRadius) == 1) {
            $find['conditions'] = array_merge($conditions, array($conditionsRadius[0]));
            $garagesRadius = $this->Garage->find('all', $find);
        } elseif (count($conditionsRadius) == 4) {
            $find['conditions'] = array_merge($conditions, array($conditionsRadius[0]));
            $distanceFinal = $distances[0];
            $garagesRadius = $this->Garage->find('all', $find);
            if (empty($garagesRadius)) {
                $find['conditions'] = array_merge($conditions, array($conditionsRadius[1]));
                $distanceFinal = $distances[1];
                $garagesRadius = $this->Garage->find('all', $find);
                if (empty($garagesRadius)) {
                    $find['conditions'] = array_merge($conditions, array($conditionsRadius[2]));
                    $distanceFinal = $distances[2];
                    $garagesRadius = $this->Garage->find('all', $find);
                    if (empty($garagesRadius)) {
                        $find['conditions'] = array_merge($conditions, array($conditionsRadius[3]));
                        $distanceFinal = $distances[3];
                        $garagesRadius = $this->Garage->find('all', $find);
                    }
                }
            }
        }

        // Recommended Images info
        $imagesRecommendedList = array();
        $networksRecommendedInfo = $this->NetworkRecommended->findAllByNetworkIdAndRecommended($networkId, 1);
        foreach ($networksRecommendedInfo as $networkRecommendedInfo) {
            if (!empty($networkRecommendedInfo['NetworkRecommended']['image_recommended_list'])) {
                if (\Configure::read('AZURE_FILES')) {
                    $urlImage = AzureBlob::getUrlForPublicContainer(ConstantsFilePaths::NETWORKS_RECOMMENDED_IMAGES_RELATIVE . $networkRecommendedInfo["NetworkRecommended"]["image_recommended_list"]);
                } else {
                    $imgListId = $networkRecommendedInfo["NetworkRecommended"]["id"];
                    $urlImage = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . "/" . $this->request->params['controller'] . "/" .
                        "image/nr/" . $imgListId . "/" . $this->generateTokenImage($imgListId, "nr");
                }
                $imagesRecommendedList[$networkRecommendedInfo["NetworkRecommended"]['internal_network_id']] = array(
                    'logo' => $urlImage,
                    'name' => $networkRecommendedInfo['NetworkRecommended']['image_recommended_list']
                );
            }
        }

        $foundGarageNetworkIds = array();
        $foundGaragesIds = array();
        // for each garage within the radius
        foreach ($garagesRadius as $garage) {
            $erpCode = !empty($garage['GarageNetwork']['sap_code']) ?
                $garage['GarageNetwork']['sap_code'] : '';
            if (empty($erpCode)) {
                $erpCode = !empty($garage['Garage']['ref_code']) ?
                    $garage['Garage']['ref_code'] : $network['Network']['ref_code'];
            }

            $recommended = $garage['GarageNetwork']['recommended'] == 1 || $garage['NetworkRecommendedView']['garage_id'] != null;

            $garageInfo = array(
                'id' => $garage['Garage']['guid'], //As the garage_id we return the garage guid
                'garage_name' => $garage['Garage']['name'],
                'business_name' => $garage['Garage']['business_name'],
                'slug' => $garage['Garage']['slug'],
                'address1' => $garage['Garage']['address1'],
                'address2' => $garage['Garage']['address2'],
                'address3' => $garage['Garage']['address3'],
                'address4' => $garage['Garage']['address4'],
                'town' => $garage['Garage']['town'],
                'postcode' => $garage['Garage']['postcode'],
                'latitude' => $garage['Garage']['latitude'],
                'longitude' => $garage['Garage']['longitude'],
                'distance' => isset($garage[0]['distanceCalculated']) ? $garage[0]['distanceCalculated'] : null,
                'photo_principal' => null, // principal image
                'reviews_number' => $garage['GarageNetwork']['reviews_number'],
                'rating' => $garage['GarageNetwork']['rating'],
                'about' => $garage['GarageNetwork']['about'],
                'quoting_active' => $garage['GarageNetwork']['quoting_active'],
                'enquiries_active' => $garage['GarageNetwork']['enquiries_active'],
                'erp_code' => !empty($erpCode) ? strtolower($erpCode) : null,
                'recommended' => $recommended,
                'location_id' => $garage['GarageNetwork']['location_id'],
                'usp1' => $garage["GarageNetwork"]["usp1"],
                'usp2' => $garage["GarageNetwork"]["usp2"],
                'usp3' => $garage["GarageNetwork"]["usp3"],
                'username' => !empty($software) ? $garage[0]['min_garage_software_username'] : null,
                'phone' => $this->getPhoneNumberForPws($network, $garage['Garage']['phone']),
                'booking_active' => $this->isBookingActive($garage)
            );

            if (!empty($garage['Garage']['province_id'])) {
                $province = $this->Province->findById($garage['Garage']['province_id']);
                $garageInfo['province'] = $province['Province']['name'];
            } else {
                $garageInfo['province'] = null;
            }

            $garageImagePrincipal = $this->GarageNetworkImage->find('first', array(
                'conditions' => array(
                    'garage_network_id' => $garage['GarageNetwork']['id'],
                    'principal' => "1"
                )
            ));
            if (!empty($garageImagePrincipal)) {
                if (\Configure::read('AZURE_FILES')) {
                    $garageInfo['photo_principal'] = AzureBlob::getUrlForPublicContainer(ConstantsFilePaths::GARAGES_NETWORKS_IMAGES_RELATIVE . $garageImagePrincipal['GarageNetworkImage']['file']);
                } else {
                    $imgId = $garageImagePrincipal['GarageNetworkImage']['id'];
                    $garageInfo['photo_principal'] = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . "/" .
                        $this->request->params['controller'] . "/" . "image/gn/" . $imgId . "/" .
                        $this->generateTokenImage($imgId, "gn");
                }
            }

            // list of all networks of this garage
            $garageNetworkAll = $this->GarageNetwork->find("all", array(
                'joins' => array(
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                            'Network.internal' => ConstantsBooleans::YES
                        )
                    )
                ),
                'conditions' => array(
                    'GarageNetwork.garage_id' => $garage['Garage']['id'],
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    'GarageNetwork.last' => ConstantsBooleans::YES,
                    'GarageNetwork.network_id !=' => $networkId
                ),
                'fields' => array('GarageNetwork.id, GarageNetwork.annex_detail_id, Network.id, Network.name, Network.image')
            ));

            $networkIdsExistentes = array();
            foreach ($garageNetworkAll as $garageNetworkItem) {
                $networkItemId = $garageNetworkItem['Network']['id'];
                if (in_array($networkItemId, $networkIdsExistentes)) {
                    continue;
                }
                $networkIdsExistentes[] = $networkItemId;

                //If a garage is Autocare unbranded take off Autocare logo on AGN
                if (
                    $networkItemId == NETWORK_ID_AUTOCARE &&
                    isset($garageNetworkItem['GarageNetwork']['annex_detail_id']) &&
                    $garageNetworkItem['GarageNetwork']['annex_detail_id'] == Configure::read('ANNEX_DETAIL_AUTOCARE_UNBRANDED')
                ) {
                    continue;
                }
                if (\Configure::read('AZURE_FILES')) {
                    $networkLogo = AzureBlob::getUrlForPublicContainer(ConstantsFilePaths::NETWORK_IMAGES_RELATIVE . $garageNetworkItem["Network"]["image"]);
                } else {
                    $networkLogo = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . '/' . $this->request->params['controller'] . '/' .
                        'image/n/' . $networkItemId . '/' .
                        $this->generateTokenImage($networkItemId, 'n');
                }
                $garageInfo['networks'][] = array(
                    'id' => $garageNetworkItem['GarageNetwork']['id'],
                    'name' => $garageNetworkItem['Network']['name'],
                    'logo' => $networkLogo
                );
                if (isset($imagesRecommendedList[$garageNetworkItem['Network']['id']]) && !empty($imagesRecommendedList[$garageNetworkItem['Network']['id']])) {
                    $garageInfo['imagesRecommendedList'][] = array(
                        'name' => $imagesRecommendedList[$garageNetworkItem['Network']['id']]['name'],
                        'logo' => $imagesRecommendedList[$garageNetworkItem['Network']['id']]['logo']
                    );
                }
            }

            if (!empty($distanceFinal) && $distanceFinal != $distances[0]) {
                $garagesSearch['radius'] = $distanceFinal;
            }

            $garagesSearch['garages'][] = $garageInfo;

            $foundGarageNetworkIds[] = $garage["GarageNetwork"]["id"];
            $foundGaragesIds[] = $garage["Garage"]["id"];
        }

        $garagesSearch['dynamic_filters'] = $this->getDynamicFilters($foundGarageNetworkIds, $dataReceived, $networkId, $foundGaragesIds);

        $garagesSearch['radius'] = $distanceFinal ?? null;

        if (isset($city) && !empty($city)) {
            $garagesSearch['city_latitude'] = $city['City']['latitude'] ?? null;
            $garagesSearch['city_longitude'] = $city['City']['longitude'] ?? null;
        }
        if (!isset($garagesSearch['garages'])) {
            $garagesSearch['garages'] = array();
        }
        return $garagesSearch;
    }

    /**
     * API to get garage info.
     */
    public function garage_search()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        $garagesSearch = array();
        if (!$this->request->is('get')) {
            $cityId = isset($dataReceived->city_id) ? $dataReceived->city_id : '';
            if (!empty($cityId)) {
                $searchCityById = true;
                $result1 = $this->garageSearchInternal($dataReceived, $searchCityById);
                if ($result1 === false) {
                    return $this->returnJsonResult($garagesSearch);
                } elseif (count($result1) > 0) {
                    return $this->returnJsonResult($result1);
                } else {
                    // try search with another config
                    $searchCityById = false;
                    $result2 = $this->garageSearchInternal($dataReceived, $searchCityById);
                    if ($result2 === false) {
                        return $this->returnJsonResult($garagesSearch);
                    } else {
                        // array with results or empty
                        return $this->returnJsonResult($result2);
                    }
                }
            } else {
                $searchCityById = true; // does not matter, no filter by city_id
                $result1 = $this->garageSearchInternal($dataReceived, $searchCityById);
                if ($result1 === false) {
                    return $this->returnJsonResult($garagesSearch);
                } else {
                    return $this->returnJsonResult($result1);
                }
            }
        }
        return $this->returnJsonResult($garagesSearch);
    }

    /**
     * View to edit basic GarageNetwork data.
     */
    public function configuration($garageNetworkId)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetwork = $this->GarageNetwork->findByIdAndStatus($garageNetworkId, ConstantsNetworksStatus::LIVE);

        if (!$garageNetwork) {
            throw new UnauthorizedException();
        }

        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionIdAndStatus($garageId, $aagRegionId, ConstantsGarageStatus::ACTIVE);

        if (!$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $networkId = $garageNetwork["GarageNetwork"]["network_id"];

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'garages_networks',
                'action' => 'configuration',
                $garageNetworkId
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
            'garage_network' => $garageNetwork,
            'garage_id' => $garageNetwork['GarageNetwork']['garage_id'],
            'garage_network_id' => $garageNetworkId,
            'garage_name' => $garage['Garage']['name'],
            'network_id' => $networkId,
            'quoting_active' => $garageNetwork['GarageNetwork']['quoting_active'],
            'quoting_views_active' => $garageNetwork['GarageNetwork']['quoting_views_active'] ?? null
        ));

        if (!$this->request->is('get')) {
            $data = $this->request->data;

            $quotingActive = isset($data['quoting_active']) ? 1 : 0;
            $enquiriesActive = isset($data['enquiries_active']) ? 1 : 0;
            $about = isset($data['GarageNetwork']['about']) ? $data['GarageNetwork']['about'] : null;
            $usp1 = isset($data['GarageNetwork']['usp1']) ? $data['GarageNetwork']['usp1'] : null;
            $usp2 = isset($data['GarageNetwork']['usp2']) ? $data['GarageNetwork']['usp2'] : null;
            $usp3 = isset($data['GarageNetwork']['usp3']) ? $data['GarageNetwork']['usp3'] : null;

            $garageNetwork['GarageNetwork']['quoting_active'] = $quotingActive;
            $garageNetwork['GarageNetwork']['enquiries_active'] = $enquiriesActive;
            $garageNetwork['GarageNetwork']['about'] = $about;
            $garageNetwork['GarageNetwork']['usp1'] = $usp1;
            $garageNetwork['GarageNetwork']['usp2'] = $usp2;
            $garageNetwork['GarageNetwork']['usp3'] = $usp3;

            $this->GarageNetwork->validator()->remove('current_charge');
            $this->GarageNetwork->validator()->remove('member_pays');
            $this->GarageNetwork->validator()->remove('garage_pays');

            $result = $this->GarageNetwork->save($garageNetwork);

            if ($result) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                $this->redirect(
                    array(
                        'controller' => 'garages_networks',
                        'action' => 'configuration',
                        $garageNetworkId
                    )
                );
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
            }
        }
    }

    /**
     * View to add images to a garage network.
     *
     * @param garageNetworkId   Garage Network ID
     */
    public function images($garageNetworkId)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

        if (!$garageNetwork) {
            throw new UnauthorizedException();
        }

        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

        if (!$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $images = $this->GarageNetworkImage->findAllByGarageNetworkId($garageNetworkId);

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'garages_networks',
                'action' => 'view',
                $garageNetworkId
            )
        );

        $uploadFiles = $this->request->data;
        if (!$this->request->is('get') && isset($uploadFiles['file-content'])) {
            $result = false;
            $check_image = false;

            $imagePrincipal = $this->GarageNetworkImage->findByGarageNetworkIdAndPrincipal($garageNetworkId, ConstantsBooleans::YES);

            $cont = 1;
            foreach ($uploadFiles['file-content'] as $image) {
                $check_image = FileManager::check_image($uploadFiles['GarageNetworkImage']['files'][$cont], $image);

                if ($check_image == ConstantsFileErrorTypes::OK) {
                    $result = $this->GarageNetworkImage->uploadGarageImages(
                        $image,
                        $uploadFiles['GarageNetworkImage']['files'][$cont],
                        $garageNetworkId,
                        ConstantsFileType::IMAGE
                    );
                    if (!$result) {
                        break;
                    } elseif ($cont == 1 && !$imagePrincipal) {
                        $this->GarageNetworkImage->convertToPrincipalGarageNetworkImage($result['GarageNetworkImage']['id'], ConstantsBooleans::YES);
                    }
                    $cont++;
                } else {
                    break;
                }
            }

            if ($check_image == ConstantsFileErrorTypes::OK) {
                if ($result) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                } else {
                    $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                }
            } elseif ($check_image == ConstantsFileErrorTypes::SIZE_ERROR) {
                $this->Session->setFlashError(__t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT);
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::IMAGE_ERROR_EXTENSION));
            }

            $this->redirect($this->here);
        }

        $networkId = $garageNetwork["GarageNetwork"]["network_id"];

        $this->set(array(
            'cancel_action' => $cancelAction,
            'garage_id' => $garageNetwork['GarageNetwork']['garage_id'],
            'network_id' => $garageNetwork['GarageNetwork']['network_id'],
            'garage_network_id' => $garageNetworkId,
            'images' => $images,
            'garage_name' => $garage['Garage']['name'],
            'network_id' => $networkId,
            'quoting_views_active' => $garageNetwork['GarageNetwork']['quoting_views_active'] ?? null
        ));
    }

    /**
     * Download GarageNetworkImage.
     */
    public function download_image($garageNetworkImageId)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetworkImage = $this->GarageNetworkImage->findById($garageNetworkImageId);

        if ($garageNetworkImage) {
            $garageNetwork = $this->GarageNetwork->findById($garageNetworkImage['GarageNetworkImage']['garage_network_id']);

            if (!$garageNetwork) {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }

            $garageId = $garageNetwork['GarageNetwork']['garage_id'];
            $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

            if (!$garage) {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }

            $this->Acceso->checkGarageAccess($garageId);

            $path = substr(ConstantsPath::DIR_GARAGE_NETWORK_IMAGES, 3) . DS;
            $this->download_file_name(
                $garageNetworkImage['GarageNetworkImage']['source_name'],
                $garageNetworkImage['GarageNetworkImage']['file'],
                $path
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX function to set a GarageNetworkImage as no principal.
     */
    public function ajax_not_principal_image()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetworkImageId = $this->request->data['id'];
        $garageNetworkImage = $this->GarageNetworkImage->findById($garageNetworkImageId);

        if ($garageNetworkImage) {
            $garageNetworkId = $garageNetworkImage['GarageNetworkImage']['garage_network_id'];

            if (!$this->request->is('get')) {
                $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

                if (!$garageNetwork) {
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                }

                $garageId = $garageNetwork['GarageNetwork']['garage_id'];
                $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

                if (!$garage) {
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                }

                $this->Acceso->checkGarageAccess($garageId);

                $edit = $this->GarageNetworkImage->convertToPrincipalGarageNetworkImage($garageNetworkImage['GarageNetworkImage']['id'], ConstantsBooleans::NO);
                if (!$edit) {
                    $this->Session->setFlashError(__t('Garage.Cant_principal'));
                }
            }

            $images = $this->GarageNetworkImage->findAllByGarageNetworkId($garageNetworkId);
            $this->set(
                array(
                    'images' => $images,
                )
            );

            $this->layout = null;
            $this->render('/GaragesNetworks/Elements/gallery');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX function to set a GarageNetworkImage as principal.
     */
    public function ajax_principal_image()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetworkImageId = $this->request->data['id'];
        $garageNetworkImage = $this->GarageNetworkImage->findById($garageNetworkImageId);

        if ($garageNetworkImage) {
            $garageNetworkId = $garageNetworkImage['GarageNetworkImage']['garage_network_id'];

            if (!$this->request->is('get')) {
                $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

                if (!$garageNetwork) {
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                }

                $garageId = $garageNetwork['GarageNetwork']['garage_id'];
                $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

                if (!$garage) {
                    header('HTTP/1.0 401 Unauthorized');
                    exit;
                }

                $this->Acceso->checkGarageAccess($garageId);

                $principalOld = $this->GarageNetworkImage->findByPrincipalAndGarageNetworkId(ConstantsBooleans::YES, $garageNetworkId);

                if (!empty($principalOld)) {
                    if ($this->GarageNetworkImage->convertToPrincipalGarageNetworkImage($principalOld['GarageNetworkImage']['id'], ConstantsBooleans::NO)) {
                        $principalNew = $this->GarageNetworkImage->convertToPrincipalGarageNetworkImage($garageNetworkImageId, ConstantsBooleans::YES);
                        if (!$principalNew) {
                            $this->Session->setFlashError(__t('Garage.Cant_principal'));
                        }
                    } else {
                        $this->Session->setFlashError(__t('Garage.Cant_principal'));
                    }
                } else {
                    $delete = $this->GarageNetworkImage->convertToPrincipalGarageNetworkImage($garageNetworkImageId, ConstantsBooleans::YES);
                    if (!$delete) {
                        $this->Session->setFlashError(__t('Garage.Cant_principal'));
                    }
                }
            }

            $images = $this->GarageNetworkImage->findAllByGarageNetworkId($garageNetworkImage['GarageNetworkImage']['garage_network_id']);
            $this->set(
                array(
                    'images' => $images,
                )
            );

            $this->layout = null;
            $this->render('/GaragesNetworks/Elements/gallery');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX function to delete a GarageNetworkImage.
     */
    public function ajax_delete_file()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetworkImageId = $this->request->data['id'];
        $garageNetworkImage = $this->GarageNetworkImage->findById($garageNetworkImageId);

        $garageNetworkId = $garageNetworkImage['GarageNetworkImage']['garage_network_id'];

        if (!$this->request->is('get')) {
            $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

            if (!$garageNetwork) {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }

            $garageId = $garageNetwork['GarageNetwork']['garage_id'];
            $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

            if (!$garage) {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }

            $this->Acceso->checkGarageAccess($garageId);

            $delete = $this->GarageNetworkImage->deleteGarageNetworkImage($garageNetworkImageId);
            if (!$delete) {
                $this->Session->setFlashError(__t('Garage.Cant_delete_image'));
            }
        }

        $images = $this->GarageNetworkImage->findAllByGarageNetworkId($garageNetworkId);
        $this->set(
            array(
                'images' => $images,
            )
        );

        $this->layout = null;
        $this->render('/GaragesNetworks/Elements/gallery');
    }

    /**
     * Garage network basic job settings pricing setup.
     */
    public function basic_job_settings($garageNetworkId)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

        if (!$garageNetwork) {
            throw new UnauthorizedException();
        }

        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, $aagRegionId);

        if (!$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $network = $this->Network->findById($garageNetwork['GarageNetwork']['network_id']);
        $pricingType = $network['Network']['pricing_type_id'];
        $isSurcharge = false;
        $languageCode = self::getLanguageCodeWhenNotTranslatedWithLoco($network['Network']['id']);
        $workDealer = $this->Work->find('first', array(
            'conditions' => array(
                'network_id' => $network['Network']['id'],
                'grouping_genart_id' => null,
                'active' => ConstantsBooleans::ACTIVE,
            ),
        ));
        $fluids = $this->Fluid->find('all', array(
            'conditions' => array(
                'network_id' => $network['Network']['id'],
                'name_en is not null',
                'active' => ConstantsBooleans::ACTIVE
            ),
        ));

        $garage = $this->Garage->findById($garageId);
        $genartsFamilies = $this->GenartFamily->find_by_network_id_active($network['Network']['id']);
        $garagesNetworkGenartNoFamily = $this->GarageNetworkGenartFamily->find_by_garage_network_and_genart_family($garageNetworkId, null);

        if ($pricingType == ConstantsQuotingPricingTypes::LIST_PRICE) {
            foreach ($genartsFamilies as &$genartFamily) {
                $garageNetworkGenartFamily = $this->GarageNetworkGenartFamily->find_by_garage_network_and_genart_family($garageNetworkId, $genartFamily['GenartFamily']['id']);
                $genartMasters = $this->GenartMaster->find_genarts_masters($genartFamily['GenartFamily']['id'], $garageNetwork['GarageNetwork']['network_id']);
                $genartFamily['GenartFamily']['discount'] = $garageNetworkGenartFamily['GarageNetworkGenartFamily']['discount'] ?? '';
                foreach ($genartMasters as $genartMaster) {
                    $master = $this->GenartMaster->findByCode($genartMaster);
                    $genartFamily['GenartFamily']['genart_discount'][$master['GenartMaster']['id']] = $master;
                }
            }

            if (empty($garagesNetworkGenartNoFamily)) {
                $garagesNetworkGenartNoFamily = array(
                    'GarageNetworkGenartFamily' => array(
                        'id' => null,
                        'garage_network_id' => $garageNetworkId,
                    ),
                );
            }

            foreach ($garagesNetworkGenartNoFamily as &$garageNetworkGenartNoFamily) {
                $genartsMaster = $this->GenartMaster->find_genarts_masters(null, $garageNetwork['GarageNetwork']['network_id']);
                $garageNetworkGenartNoFamily['discount'] = $garagesNetworkGenartNoFamily['GarageNetworkGenartFamily']['discount'] ?? '';
                foreach ($genartsMaster as $genartMaster) {
                    $master = $this->GenartMaster->findByCode($genartMaster);
                    foreach ($fluids as $fluid) {
                        if (isset($master['GenartMaster']['name_en']) && $master['GenartMaster']['name_en'] == $fluid['Fluid']['name_en']) {
                            $master = null;
                        }
                    }
                    if (isset($master)) {
                        $garageNetworkGenartNoFamily['genarts_discount'][$master['GenartMaster']['id']] = $master;
                    }
                }
            }
            $discount = true;
        }
        if ($pricingType == ConstantsQuotingPricingTypes::NET_PRICE) {
            foreach ($genartsFamilies as &$genartFamily) {
                $garageNetworkGenartFamily = $this->GarageNetworkGenartFamily->find_by_garage_network_and_genart_family($garageNetworkId, $genartFamily['GenartFamily']['id']);
                $genartMasters = $this->GenartMaster->find_genarts_masters($genartFamily['GenartFamily']['id'], $garageNetwork['GarageNetwork']['network_id']);
                foreach ($genartMasters as $genartMaster) {
                    $master = $this->GenartMaster->findByCodeAndNetworkId($genartMaster, $network['Network']['id']);

                    $genarts = $this->Genart->find('all', array(
                        'joins' => array(
                            array(
                                'alias' => 'GroupingGenarts',
                                'table' => 'grouping_genarts',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Genart.grouping_genart_id = GroupingGenarts.id'
                                ),
                            ),
                            array(
                                'alias' => 'Works',
                                'table' => 'works',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Works.grouping_genart_id = GroupingGenarts.id'
                                ),
                            )
                        ),
                        'conditions' => array(
                            'Genart.active' => ConstantsBooleans::ACTIVE,
                            'Genart.code' => $genartMaster,
                            'GroupingGenarts.network_id' => $network['Network']['id'],
                            'Works.active' => ConstantsBooleans::ACTIVE,
                        ),
                    ));

                    foreach ($genarts as $genart) {
                        if (!empty($garageNetworkGenartFamily['GarageNetworkGenartFamily']['markup'])) {
                            $genartFamily['GenartFamily']['markup'] = $garageNetworkGenartFamily['GarageNetworkGenartFamily']['markup'] ?? '';
                            $genartFamily['GenartFamily']['genart_markup'][$master['GenartMaster']['id']] = $master;
                        } else {
                            $genartFamily['GenartFamily']['surcharge'] = $garageNetworkGenartFamily['GarageNetworkGenartFamily']['surcharge'] ?? '';
                            $genartFamily['GenartFamily']['genart_surcharge'][$master['GenartMaster']['id']] = $master;
                        }
                    }
                }
            }

            if (empty($garagesNetworkGenartNoFamily)) {
                $garagesNetworkGenartNoFamily = array(
                    'GarageNetworkGenartFamily' => array(
                        'id' => null,
                        'garage_network_id' => $garageNetworkId,
                    ),
                );
            }

            foreach ($garagesNetworkGenartNoFamily as &$garageNetworkGenartNoFamily) {
                $genartMasters = $this->GenartMaster->find_genarts_masters(null, $garageNetwork['GarageNetwork']['network_id']);

                foreach ($genartMasters as $genartMaster) {
                    $master = $this->GenartMaster->findByCodeAndNetworkId($genartMaster, $network['Network']['id']);
                    foreach ($fluids as $fluid) {
                        if (isset($master['GenartMaster']['name_en']) && ($master['GenartMaster']['name_en'] == $fluid['Fluid']['name_en'])) {
                            $master = null;
                        }
                    }

                    if (isset($master)) {
                        $genarts = $this->Genart->find('all', array(
                            'joins' => array(
                                array(
                                    'alias' => 'GroupingGenarts',
                                    'table' => 'grouping_genarts',
                                    'type' => 'INNER',
                                    'conditions' => array(
                                        'Genart.grouping_genart_id = GroupingGenarts.id'
                                    ),
                                )
                            ),
                            'conditions' => array(
                                'Genart.active' => "1",
                                'Genart.code' => $genartMaster,
                                'GroupingGenarts.network_id' => $network['Network']['id']
                            ),
                        ));
                        foreach ($genarts as $genart) {
                            if (!empty($genart['Genart']['markup']) || (empty($genart['Genart']['markup']) && empty($genart['Genart']['surcharge']))) {
                                $garageNetworkGenartNoFamily['markup'] = $garageNetworkGenartNoFamily['markup'] ?? '';
                                $garageNetworkGenartNoFamily['genarts_markup'][$master['GenartMaster']['id']] = $master;
                            } else {
                                $garageNetworkGenartNoFamily['surcharge'] = $garageNetworkGenartNoFamily['surcharge'] ?? '';
                                $garageNetworkGenartNoFamily['genarts_surcharge'][$master['GenartMaster']['id']] = $master;
                            }
                        }
                    }
                }
            }
        }

        if (!$this->request->is('get')) {
            $errorSaving = false;
            $discounts = isset($this->request->data['genart_family']['discount']) ? $this->request->data['genart_family']['discount'] : array();
            $markups = isset($this->request->data['genart_family']['markup']) ? $this->request->data['genart_family']['markup'] : array();
            $surcharges = isset($this->request->data['genart_family']['surcharge']) ? $this->request->data['genart_family']['surcharge'] : array();
            $labourPrices = $this->request->data['GarageNetwork'] ?? array();
            $dealerPrices = $this->request->data['genart_dealer'] ?? array();
            $noFamilyGenart = $this->request->data['genart_no_family'] ?? array();

            foreach ($genartsFamilies as &$genartFamily) {
                $garagesNetworksGenartsFamily = $this->GarageNetworkGenartFamily->find_by_garage_network_and_genart_family($garageNetworkId, $genartFamily['GenartFamily']['id']);

                $saveFamilyGenart = (isset($discounts[$genartFamily['GenartFamily']['id']]) && $discounts[$genartFamily['GenartFamily']['id']] != '') ||
                    (isset($markups[$genartFamily['GenartFamily']['id']]) && $markups[$genartFamily['GenartFamily']['id']] != '') ||
                    (isset($surcharges[$genartFamily['GenartFamily']['id']]) && $surcharges[$genartFamily['GenartFamily']['id']] != '');

                if ($garagesNetworksGenartsFamily != null) {
                    if (isset($discounts[$genartFamily['GenartFamily']['id']])) {
                        $garagesNetworksGenartsFamily['GarageNetworkGenartFamily']['discount'] = $discounts[$genartFamily['GenartFamily']['id']];
                    }
                    if (isset($markups[$genartFamily['GenartFamily']['id']])) {
                        $garagesNetworksGenartsFamily['GarageNetworkGenartFamily']['markup'] = $markups[$genartFamily['GenartFamily']['id']];
                        $garagesNetworksGenartsFamily['GarageNetworkGenartFamily']['surcharge'] = null;
                    }
                    if (isset($surcharges[$genartFamily['GenartFamily']['id']])) {
                        $garagesNetworksGenartsFamily['GarageNetworkGenartFamily']['surcharge'] = $surcharges[$genartFamily['GenartFamily']['id']];
                        $garagesNetworksGenartsFamily['GarageNetworkGenartFamily']['markup'] = null;
                    }
                } else {
                    $this->GarageNetworkGenartFamily->create();

                    $garagesNetworksGenartsFamily = array('GarageNetworkGenartFamily' => array(
                        'garage_network_id' => $garageNetworkId,
                        'genart_family_id' => $genartFamily['GenartFamily']['id'],
                        'discount' => isset($discounts[$genartFamily['GenartFamily']['id']]) ? $discounts[$genartFamily['GenartFamily']['id']] : null,
                        'markup' => isset($markups[$genartFamily['GenartFamily']['id']]) ? $markups[$genartFamily['GenartFamily']['id']] : null,
                        'surcharge' => isset($surcharges[$genartFamily['GenartFamily']['id']]) ? $surcharges[$genartFamily['GenartFamily']['id']] : null
                    ));
                }
                if ($saveFamilyGenart) {
                    if (!$this->GarageNetworkGenartFamily->save($garagesNetworksGenartsFamily)) {
                        $errorSaving = true;
                        break;
                    }
                } elseif (
                    $garagesNetworksGenartsFamily != null &&
                    isset($garagesNetworksGenartsFamily['GarageNetworkGenartFamily']['id'])
                ) {
                    if (!$this->GarageNetworkGenartFamily->delete($garagesNetworksGenartsFamily['GarageNetworkGenartFamily']['id'])) {
                        $errorSaving = true;
                        break;
                    }
                }
            }

            $labour_type = array();

            if (!empty($labourPrices['labour_price']) || !empty($labourPrices['labour_price_electric'])) {
                if (isset($labourPrices['labour_price'])) {
                    $labour_type['labour_hourly_price'] = $labourPrices['labour_price'];
                }
                if (isset($labourPrices['labour_price_electric'])) {
                    $labour_type['labour_hourly_price_electric_vehicles'] = $labourPrices['labour_price_electric'];
                }

                if (!$this->GarageNetwork->update_labour_dealer_prices($garageNetwork['GarageNetwork']['id'], $labour_type)) {
                    $errorSaving = true;
                }
            } else {
                $labour_type['labour_hourly_price'] = null;
                $labour_type['labour_hourly_price_electric_vehicles'] = null;

                if (!$this->GarageNetwork->update_labour_dealer_prices($garageNetwork['GarageNetwork']['id'], $labour_type)) {
                    $errorSaving = true;
                }
            }

            $dealer_type = array();

            if ((isset($dealerPrices['discount']) && $dealerPrices['discount'] != '') ||
                (isset($dealerPrices['markup']) && $dealerPrices['markup'] != '') ||
                (isset($dealerPrices['surcharge']) && $dealerPrices['surcharge'] != '')
            ) {
                if (isset($dealerPrices['discount'])) {
                    $dealer_type['discount'] = $dealerPrices['discount'];
                }
                if (isset($dealerPrices['markup'])) {
                    $dealer_type['markup'] = $dealerPrices['markup'];
                    $dealer_type['surcharge'] = null;
                }
                if (isset($dealerPrices['surcharge'])) {
                    $dealer_type['surcharge'] = $dealerPrices['surcharge'];
                    $dealer_type['markup'] = null;
                }
                if (!$this->GarageNetwork->update_dealer_prices($garageNetwork['GarageNetwork']['id'], $dealer_type)) {
                    $errorSaving = true;
                }
            } else {
                $dealer_type['discount'] = null;
                $dealer_type['markup'] = null;
                $dealer_type['surcharge'] = null;
                if (!$this->GarageNetwork->update_dealer_prices($garageNetwork['GarageNetwork']['id'], $dealer_type)) {
                    $errorSaving = true;
                }
            }
            $garagesNetworksGenartsNoFam = $this->GarageNetworkGenartFamily->find_by_garage_network_and_genart_family($garageNetworkId, null);

            if ((isset($noFamilyGenart['discount']) && $noFamilyGenart['discount'] != '') ||
                (isset($noFamilyGenart['markup']) && $noFamilyGenart['markup'] != '') ||
                (isset($noFamilyGenart['surcharge']) && $noFamilyGenart['surcharge'] != '')
            ) {
                if ($garagesNetworksGenartsNoFam != null) {
                    if (isset($noFamilyGenart['discount'])) {
                        $garagesNetworkGenartNoFamily['GarageNetworkGenartFamily']['discount'] = $noFamilyGenart['discount'];
                    }
                    if (isset($noFamilyGenart['markup'])) {
                        $garagesNetworkGenartNoFamily['GarageNetworkGenartFamily']['markup'] = $noFamilyGenart['markup'];
                        $garagesNetworkGenartNoFamily['GarageNetworkGenartFamily']['surcharge'] = null;
                    }
                    if (isset($noFamilyGenart['surcharge'])) {
                        $garagesNetworkGenartNoFamily['GarageNetworkGenartFamily']['surcharge'] = $noFamilyGenart['surcharge'];
                        $garagesNetworkGenartNoFamily['GarageNetworkGenartFamily']['markup'] = null;
                    }
                } else {
                    $this->GarageNetworkGenartFamily->create();

                    $garagesNetworkGenartNoFamily = array('GarageNetworkGenartFamily' => array(
                        'garage_network_id' => $garageNetworkId,
                        'genart_family_id' => null,
                        'discount' => isset($noFamilyGenart['discount']) ? $noFamilyGenart['discount'] : null,
                        'markup' => isset($noFamilyGenart['markup']) ? $noFamilyGenart['markup'] : null,
                        'surcharge' => isset($noFamilyGenart['surcharge']) ? $noFamilyGenart['surcharge'] : null
                    ));
                }
                if (!$this->GarageNetworkGenartFamily->save($garagesNetworkGenartNoFamily)) {
                    $errorSaving = true;
                }
            } elseif (isset($garagesNetworksGenartsNoFam['GarageNetworkGenartFamily']['id']) && $garagesNetworksGenartsNoFam != null) {
                if (!$this->GarageNetworkGenartFamily->delete($garagesNetworksGenartsNoFam['GarageNetworkGenartFamily']['id'])) {
                    $errorSaving = true;
                }
            }

            if (isset($this->request->data['GenartMaster'])) {
                foreach ($this->request->data['GenartMaster'] as $genartMasterId => $genartMaster) {
                    $garageNetworkGenartMaster = $this->GarageNetworkGenartMaster->getGenartByGarageNetworkIdAndGenartMasterId($garageNetworkId, $genartMasterId);
                    if ($garageNetworkGenartMaster != null) {
                        if (isset($genartMaster['genart_master_labour_price'])) {
                            $garageNetworkGenartMaster['GarageNetworkGenartMaster']['genart_master_labour_price'] = $genartMaster['genart_master_labour_price'];
                        }
                    } else {
                        if (empty($genartMaster['genart_master_labour_price'])) {
                            continue;
                        }
                        $this->GarageNetworkGenartMaster->create();

                        $garageNetworkGenartMaster = array('GarageNetworkGenartMaster' => array(
                            'garage_network_id' => $garageNetworkId,
                            'genart_master_id' => $genartMasterId,
                            'genart_master_labour_price' => $genartMaster['genart_master_labour_price'],
                        ));
                    }

                    if (!$this->GarageNetworkGenartMaster->save($garageNetworkGenartMaster)) {
                        $errorSaving = true;
                        break;
                    }
                }
            }

            if (!$errorSaving) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));

                $this->redirect(
                    array(
                        'controller' => 'garages_networks',
                        'action' => 'basic_job_settings',
                        $garageNetworkId
                    )
                );
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
            }
        }

        $workDealerType = '';
        if ($workDealer) {
            if ($pricingType == ConstantsQuotingPricingTypes::LIST_PRICE) {
                $workDealerType = 'discount';
            } else {
                if (!empty($garageNetwork['GarageNetwork']['dealer_markup'])) {
                    $workDealerType = 'markup';
                }
                if (!empty($garageNetwork['GarageNetwork']['dealer_surcharge'])) {
                    $workDealerType = 'surcharge';
                }
            }
        }

        $genarts_with_is_labour_time = $this->GenartMaster->get_genarts_is_labour_by_network($network['Network']['id'], $garageNetworkId);

        $this->set(
            array(
                'garage_network_id' => $garageNetworkId,
                'garage_name' => $garage['Garage']['name'],
                'network_id' => $garageNetwork['GarageNetwork']['network_id'],
                'with_vat' => $network['Network']['with_vat'],
                'genarts_families' => $genartsFamilies,
                'garage_network' => $garageNetwork,
                'network' => $network,
                'garage_network_genart_no_family' => $garagesNetworkGenartNoFamily ?? array(),
                'is_surcharge' => $isSurcharge ?? false,
                'discount' => $discount ?? false,
                'work_dealer' => $workDealer,
                'work_dealer_type' => $workDealerType,
                'language_code' => $languageCode,
                'quoting_views_active' => $garageNetwork['GarageNetwork']['quoting_views_active'],
                'genarts_with_is_labour_time' => $genarts_with_is_labour_time,
                'networkUseFluids' => $this->Fluid->networkUseFluids($network['Network']['id'], $workDealer)
            )
        );
    }

    /**
     * Edit genart families.
     * Permissions checked in basic_job_settings function.
     */
    public function edit_genarts_families($garageNetworkId)
    {
        $this->basic_job_settings($garageNetworkId);

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'garages_networks',
                'action' => 'basic_job_settings',
                $garageNetworkId
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
            'edit_genarts_families' => true
        ));

        $this->render('/GaragesNetworks/basic_job_settings');
    }

    /**
     * When there may be no loco id and lc language is selected this function returns the language for a given network.
     *
     * @param int $networkId The id of the network.
     * @return string An string with the id
     */
    private function getLanguageCodeWhenNotTranslatedWithLoco($networkId)
    {
        $languageCode = __l() == 'lc' ? Configure::read('network_language.' . $networkId) : __l();

        return $languageCode;
    }

    private function isBookingActive($garageNetwork)
    {
        $days = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
        $numberOfSlotsPerDay = 2;
        foreach ($days as $day) {
            for ($slotNumber = 1; $slotNumber <= $numberOfSlotsPerDay; $slotNumber++) {
                $slotName = $day . '_planner_max_' . $slotNumber;
                $openingName = $day . '_planner_open_' . $slotNumber;
                if (isset($garageNetwork["GarageNetwork"][$slotName]) && $garageNetwork["GarageNetwork"][$slotName] > 0 && !empty($garageNetwork["GarageNetwork"][$openingName])) {
                    return true;
                }
            }
        }
        return false;
    }

    private function getDynamicFilters($garageNetworkIds, $dataReceived, $networkId, $garagesIds): array
    {
        $filters = array();

        //Selects language.
        $lang = isset($dataReceived->lang) ? $dataReceived->lang : null;

        if (empty($lang)) {
            $lang = 'en';
        }

        $lang = strtolower($lang);

        //Languages codes array.
        $code = $this->Language->find('first', array(
            'conditions' => array(
                'code != ' => ConstantsLanguages::LOCO_CODE,
                'code' => $lang
            )
        ));

        if (!$code) {
            $lang = 'en';
        }

        $filters['vehicle_types'] = $this->GarageNetworkVehicleType->findVehicleTypesListByGarageNetworkId(
            $garageNetworkIds,
            isset($dataReceived->vehicle_types) ? $dataReceived->vehicle_types : array(),
            $lang
        );
        $filters['brands'] = $this->GarageNetworkVehicle->findVehicleListByGarageNetworkId(
            $garageNetworkIds,
            isset($dataReceived->vehicles) ? $dataReceived->vehicles : array(),
            $lang
        );
        $filters['service_drivers'] = $this->GarageNetworkServiceDriver->findServiceDriverListByGarageNetworkId(
            $garageNetworkIds,
            isset($dataReceived->services_drivers) ? $dataReceived->services_drivers : array(),
            $lang
        );
        $filters['services'] = $this->GarageNetworkService->findServiceListByGarageNetworkId(
            $garageNetworkIds,
            isset($dataReceived->services) ? $dataReceived->services : array(),
            $lang
        );
        $filters['networks'] = $this->GarageNetwork->getOtherNetworksFromGarageNetworkFilteredByGarageId(
            $networkId,
            isset($dataReceived->networks) ? $dataReceived->networks : array(),
            $garagesIds
        );

        return $filters;
    }

    private function getPhoneNumberForPws($network, $phoneNumber){
        if (isset($network['Network']['hide_phone_numbers_in_pws']) && !$network['Network']['hide_phone_numbers_in_pws']) {
            return $phoneNumber;
        }
        return null;
    }
}
