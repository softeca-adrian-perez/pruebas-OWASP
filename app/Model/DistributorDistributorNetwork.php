<?php

class DistributorDistributorNetwork extends AppModel
{

    public $useTable = 'distributors_distributors_networks';

    public $hasOne = array(
        'Distributor',
    );

    public $belongsTo = array(
        'Contract',
    );

    public $validate = array(
        'network_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_network',
            ),
        ),
        'status' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_status',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'contract_start_date' => array(
            'rule' => 'date',
            'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),

        'contract_end_date' => array(
            'rule' => 'date',
            'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
    );

    public function add_distributor_network($data, $distributor_id)
    {

        $fields = array(
            'DistributorDistributorNetwork' => array(
                'distributor_id',
                'network_id',
                'trading_group_id',
                'garage_number',
                'contract_start_date',
                'contract_end_date',
                'reason_leaving_id',
                'status',
                'last',
            )
        );
        $data['DistributorDistributorNetwork']['trading_group_id'] = $data['trading_group_id'];
        $data['DistributorDistributorNetwork']['contract_start_date'] = Fecha::toFormatoBd($data['DistributorDistributorNetwork']['contract_start_date']);
        $data['DistributorDistributorNetwork']['contract_end_date'] = Fecha::toFormatoBd($data['DistributorDistributorNetwork']['contract_end_date']);
        $data['DistributorDistributorNetwork']['garage_number'] = null;

        $data['DistributorDistributorNetwork']['distributor_id'] = $distributor_id;
        $this->create();

        $data_bd = $this->guardar($data, $fields);

        if (!$data_bd) {
            return false;
        }

        $this->Distributor = ClassRegistry::init("Distributor");
        $this->Distributor->edit_modification_date($distributor_id);

        $this->commit();
        return $data_bd;
    }

    public function edit_distributor_network($data)
    {
        $fields = array(
            'DistributorDistributorNetwork' => array(
                'network_id',
                'trading_group_id',
                'garage_number',
                'contract_start_date',
                'contract_end_date',
                'reason_leaving_id',
                'status',
                'last',
                'modification_date',
            )
        );

        $data['DistributorDistributorNetwork']['contract_start_date'] = Fecha::toFormatoBd($data['DistributorDistributorNetwork']['contract_start_date']);
        $data['DistributorDistributorNetwork']['contract_end_date'] = Fecha::toFormatoBd($data['DistributorDistributorNetwork']['contract_end_date']);
        $data['DistributorDistributorNetwork']['garage_number'] = null;
        $data['DistributorDistributorNetwork']['modification_date'] = date('Y-m-d H:i:s');

        $data_bd = $this->guardar($data, $fields);

        if (!$data_bd) {
            return false;
        }

        $this->Distributor = ClassRegistry::init("Distributor");
        $this->Distributor->edit_modification_date($data_bd['DistributorDistributorNetwork']['distributor_id']);

        $this->commit();
        return $data_bd;
    }

    public function getDatasById($distributor_distributor_network_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorDistributorNetwork.distributor_id = Distributor.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorDistributorNetwork.id' => $distributor_distributor_network_id,
                ),
                'fields' => array(
                    'DistributorDistributorNetwork.*',
                    'Distributor.name'
                ),
            )
        );
    }

    public function setDistributorNetworkNotActive($old_network)
    {
        $fields = array(
            'DistributorDistributorNetwork' => array(
                'network_id',
                'network_contract_type_id',
                'garage_number',
                'contract_start_date',
                'contract_end_date',
                'reason_leaving_id',
                'status',
                'last',
            )
        );
        $old_network['DistributorDistributorNetwork']['last'] = ConstantsBooleans::NO_ACTIVE;
        $old_distributor_network_bd = $this->guardar($old_network, $fields);
        if (!$old_distributor_network_bd) {
            return false;
        }
        return $old_distributor_network_bd;
    }

    public function getAllByNetworkIdAndStatusAndLast($network_id, $status, $last)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'DistributorDistributorNetwork.network_id' => $network_id,
                    'DistributorDistributorNetwork.status' => $status,
                    'DistributorDistributorNetwork.last' => $last,
                ),
            )
        );
    }

    public function findNetworksByDistributor($distributor_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'DistributorDistributorNetwork.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'DistributorDistributorNetwork.network_id',
                ),
            )
        );
    }

    public function getAllByDistributorIdAndActive($distributor_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'DistributorNetwork',
                        'table' => 'distributors_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorNetwork.id = DistributorDistributorNetwork.network_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorDistributorNetwork.distributor_id' => $distributor_id,
                    'DistributorDistributorNetwork.status' => ConstantsNetworksStatus::LIVE
                ),
                'order' => 'DistributorNetwork.name DESC'
            )
        );
    }

    public function  createDisNetwork($distributor_json, $distributor_id, $distributor_creation_date)
    { // New DistributorDistributorNetwork comes from FRANCE JSON

        foreach ($distributor_json['reseau'] as $network) {
            if (
                $network['nom'] == 'Groupauto VL' ||
                $network['nom'] == 'G-Truck' ||
                $network['nom'] == 'Color Services' ||
                $network['nom'] == 'Partner\'s' ||
                $network['nom'] == 'Partner\'s VI' ||
                $network['nom'] == 'MP Truck' ||
                $network['nom'] == 'Precisium VL' ||
                $network['nom'] == 'Precisium' ||
                $network['nom'] == 'Precisium PL' ||
                $network['nom'] == 'Precisium Color' ||
                $network['nom'] == 'Precisium Industrie' ||
                $network['nom'] == 'Gef\'Auto Distribution'
            ) {
                $this->DistributorNetwork = ClassRegistry::init('DistributorNetwork');
                if ($network['nom'] == 'Precisium') {
                    $network['nom'] = 'Precisium VL';
                }
                $network_current = $this->DistributorNetwork->findByName($network['nom']);
                $network_id = $network_current['DistributorNetwork']['id'];

                $trading_group = '';
                if ($distributor_json['groupauto'] == true) {
                    $trading_group = ConstantsTradingGroupsNames::GROUPAUTO_FRANCE;
                } elseif ($distributor_json['partners'] == true) {
                    $trading_group = ConstantsTradingGroupsNames::PARTNERS;
                } elseif ($distributor_json['precisium'] == true) {
                    $trading_group = ConstantsTradingGroupsNames::PRECISIUM;
                } elseif ($distributor_json['gefa'] == true) {
                    $trading_group = ConstantsTradingGroupsNames::GEF_AUTO;
                } else {
                    debug('ERROR');
                }

                $distributor_distributor_network_tmp = array(
                    'DistributorDistributorNetwork' => array(
                        'distributor_id' => $distributor_id,
                        'network_id' => $network_id,
                        'trading_group_id' => $trading_group,
                        'garage_number' => isset($network['code']) ? $network['code'] : $distributor_json['code'],
                        'contract_start_date' => ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : null,
                        'contract_end_date' => ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : null,
                        'reason_leaving' => null,
                        'status' => ($network['actif'] == true) ? ConstantsNetworksStatus::LIVE : ConstantsNetworksStatus::UNSUBSCRIBE,
                    )
                );

                $distributor_distributor_network = $this->findByDistributorIdAndNetworkId($distributor_id, $network_id);
                if (!empty($distributor_distributor_network) && $distributor_distributor_network['DistributorDistributorNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                    if (($distributor_distributor_network_tmp['DistributorDistributorNetwork']['status'] == ConstantsNetworksStatus::LIVE)) {
                        $last = ConstantsBooleans::YES;
                    } else {
                        $last = ConstantsBooleans::NO;
                    }
                } else {
                    $last = ConstantsBooleans::YES;
                }
                $distributor_distributor_network_tmp['DistributorDistributorNetwork']['last'] = $last;


                $this->validator()->remove('contract_sent_date');
                $this->create();
                if (!$this->save($distributor_distributor_network_tmp)) {
                    CakeLog::write('updates-france', 'The Network distributor with ID DISTRIBUTOR ' . $distributor_id . ' could not be created.' . PHP_EOL);
                }

                if (($network['date_signature'] != '0000-00-00') && (strtotime($distributor_creation_date) > strtotime($network['date_signature']))) {
                    $distributor_creation_date = $network['date_signature'];
                }
            } else if (
                $network['nom'] == 'Pare-Brise Center' ||
                $network['nom'] == 'Utilitaire Service Center' ||
                $network['nom'] == 'Pneumatiques Service Center'
            ) {
                $this->LabelType = ClassRegistry::init('LabelType');
                $this->DistributorLabel = ClassRegistry::init('DistributorLabel');
                $label = $this->LabelType->findByName($network['nom']);

                //If this label doesn't exits
                if (empty($label)) {
                    $label_tmp = array(
                        'LabelType' => array(
                            'name' => $network['nom'],
                        )
                    );
                    $label_model->create();
                    $label = $this->LabelType->save($label_tmp);
                    if (!$label) {
                        CakeLog::write('updates-france', 'Label could not be created.' . PHP_EOL);
                    }
                }
                $label_id = $label['LabelType']['id'];

                $distributor_label_tmp = array(
                    'DistributorLabel' => array(
                        'distributor_id' => $distributor_id,
                        'label_type_id' => $label_id,
                        'start_date' => ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : null,
                        'end_date' => ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : null,
                        'modification_date' => null,
                    )
                );

                $this->DistributorLabel->validator()->remove('start_date');
                $this->DistributorLabel->create();
                $distributor_bd = $this->DistributorLabel->save($distributor_label_tmp);
                if (!$distributor_bd) {
                    CakeLog::write('updates-france', 'Distributor Label could not be created.' . PHP_EOL);
                }

                if (($network['date_signature'] != '0000-00-00') && (strtotime($distributor_creation_date) > strtotime($network['date_signature']))) {
                    $distributor_creation_date = $network['date_signature'];
                }
            } else if (
                $network['nom'] == 'Infotruck' ||
                $network['nom'] == 'Cattronic' ||
                $network['nom'] == 'Hotline technique' ||
                $network['nom'] == 'Precisio' ||
                $network['nom'] == 'Drivista' ||
                $network['nom'] == 'Drivista-shop' ||
                $network['nom'] == 'Geraucat'
            ) {
                $this->ServiceType = ClassRegistry::init('ServiceType');
                $this->DistributorService = ClassRegistry::init('DistributorService');
                $aag_service = $aag_service_model->findByName($network['nom']);

                //If this service doesn't exits
                if (empty($aag_service)) {
                    $aag_service_tmp = array(
                        'ServiceType' => array(
                            'name' => $network['nom'],
                        )
                    );
                    $this->ServiceType->create();
                    $aag_service = $this->ServiceType->save($aag_service_tmp);
                    if (!$aag_service) {
                        CakeLog::write('updates-france', 'Service could not be created.' . PHP_EOL);
                    }
                }
                $aag_service_id = $aag_service['ServiceType']['id'];

                $distributor_service_tmp = array(
                    'DistributorService' => array(
                        'distributor_id' => $distributor_id,
                        'service_type_id' => $aag_service_id,
                        'start_date' => ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : null,
                        'end_date' => ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : null,
                        'modification_date' => null,
                    )
                );

                $this->DistributorService->validator()->remove('start_date');
                $this->DistributorService->create();
                $distributor_service_model_bd = $this->DistributorService->save($distributor_service_tmp);
                if (!$distributor_service_model_bd) {
                    CakeLog::write('updates-france', 'Distributor Service could not be created.' . PHP_EOL);
                }
                if (($network['date_signature'] != '0000-00-00') && (strtotime($distributor_creation_date) > strtotime($network['date_signature']))) {
                    $distributor_creation_date = $network['date_signature'];
                }
            } else {

                $this->ServiceType = ClassRegistry::init('ServiceType');
                $this->DistributorService = ClassRegistry::init('DistributorService');
                $aag_service = $this->ServiceType->findByName($network['nom']);

                //If this service doesn't exits
                if (empty($aag_service)) {
                    $aag_service_tmp = array(
                        'ServiceType' => array(
                            'name' => $network['nom'],
                        )
                    );
                    $this->ServiceType->create();
                    $aag_service = $this->ServiceType->save($aag_service_tmp);
                    if (!$aag_service) {
                        CakeLog::write('updates-france', 'Service could not be created.' . PHP_EOL);
                    }
                }
                $aag_service_id = $aag_service['ServiceType']['id'];

                $distributor_service_tmp = array(
                    'DistributorService' => array(
                        'distributor_id' => $distributor_id,
                        'service_type_id' => $aag_service_id,
                        'start_date' => ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : null,
                        'end_date' => ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : null,
                        'modification_date' => null,
                    )
                );

                $this->DistributorService->validator()->remove('start_date');
                $this->DistributorService->create();
                $distributor_service_model_bd = $this->DistributorService->save($distributor_service_tmp);
                if (!$distributor_service_model_bd) {
                    CakeLog::write('updates-france', 'Distributor Service could not be created.' . PHP_EOL);
                }

                if (($network['date_signature'] != '0000-00-00') && (strtotime($distributor_creation_date) > strtotime($network['date_signature']))) {
                    $distributor_creation_date = $network['date_signature'];
                }
            }
        }
        $this->commit();
        return $distributor_creation_date;
    }

    public function  updateDisNetwork($distributor_json, $distributor_exist)
    { // New DistributorDistributorNetwork comes from FRANCE JSON comes from FRANCE JSON

        $distributor_creation_date = $distributor_exist['Distributor']['creation_date'];
        if (isset($distributor_json['reseau'])) {
            foreach ($distributor_json['reseau'] as $network) {

                if (
                    $network['nom'] == 'Groupauto VL' ||
                    $network['nom'] == 'G-Truck' ||
                    $network['nom'] == 'Color Services' ||
                    $network['nom'] == 'Partner\'s' ||
                    $network['nom'] == 'Partner\'s VI' ||
                    $network['nom'] == 'MP Truck' ||
                    $network['nom'] == 'Precisium VL' ||
                    $network['nom'] == 'Precisium' ||
                    $network['nom'] == 'Precisium PL' ||
                    $network['nom'] == 'Precisium Color' ||
                    $network['nom'] == 'Precisium Industrie' ||
                    $network['nom'] == 'Gef\'Auto Distribution'
                ) {
                    $this->DistributorNetwork = ClassRegistry::init('DistributorNetwork');
                    if ($network['nom'] == 'Precisium') {
                        $network['nom'] = 'Precisium VL';
                    }
                    $network_current = $this->DistributorNetwork->findByName($network['nom']);
                    $network_id = $network_current['DistributorNetwork']['id'];

                    $trading_group = '';
                    if ($distributor_json['groupauto'] == true) {
                        $trading_group = ConstantsTradingGroupsNames::GROUPAUTO_FRANCE;
                    } elseif ($distributor_json['partners'] == true) {
                        $trading_group = ConstantsTradingGroupsNames::PARTNERS;
                    } elseif ($distributor_json['precisium'] == true) {
                        $trading_group = ConstantsTradingGroupsNames::PRECISIUM;
                    } elseif ($distributor_json['gefa'] == true) {
                        $trading_group = ConstantsTradingGroupsNames::GEF_AUTO;
                    } else {
                        $trading_group_id = $distributor_exist['Distributor']['trading_group_id'];
                    }

                    $distributor_distributor_network_exist = $this->findByDistributorIdAndNetworkId($distributor_exist['Distributor']['id'], $network_id);

                    if (!$distributor_distributor_network_exist) {
                        $distributor_distributor_network_tmp = array(
                            'DistributorDistributorNetwork' => array(
                                'distributor_id' => $distributor_exist['Distributor']['id'],
                                'network_id' => $network_id,
                                'trading_group_id' => $trading_group,
                                'garage_number' => (isset($network['code'])) ? $network['code'] : $distributor['code'],
                                'contract_start_date' => ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : null,
                                'contract_end_date' => ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : null,
                                'reason_leaving' => null,
                                'status' => ($network['actif'] == true) ? ConstantsNetworksStatus::LIVE : ConstantsNetworksStatus::UNSUBSCRIBE,
                            )
                        );

                        $distributor_distributor_network = $this->findByDistributorIdAndNetworkId($distributor_exist['Distributor']['id'], $network_id);
                        if (!empty($distributor_distributor_network) && $distributor_distributor_network['DistributorDistributorNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                            if (($distributor_distributor_network_tmp['DistributorDistributorNetwork']['status'] == ConstantsNetworksStatus::LIVE)) {
                                $last = ConstantsBooleans::YES;
                            } else {
                                $last = ConstantsBooleans::NO;
                            }
                        } else {
                            $last = ConstantsBooleans::YES;
                        }
                        $distributor_distributor_network_tmp['DistributorDistributorNetwork']['last'] = $last;


                        $this->validator()->remove('contract_sent_date');
                        $this->create();
                        if (!$this->save($distributor_distributor_network_tmp)) {
                            CakeLog::write('updates-france', 'The Network distributor with ID DISTRIBUTOR ' . $distributor_exist['Distributor']['id'] . ' could not be created.' . PHP_EOL);
                        }

                        if (($network['date_signature'] != '0000-00-00') && (strtotime($distributor_creation_date) > strtotime($network['date_signature']))) {
                            $distributor_creation_date = $network['date_signature'];
                        }
                    } else {
                        $distributor_distributor_network_tmp = array(
                            'DistributorDistributorNetwork' => array(
                                'id' => $distributor_distributor_network_exist['DistributorDistributorNetwork']['id'],
                                'distributor_id' => $distributor_exist['Distributor']['id'],
                                'network_id' => $network_id,
                                'trading_group_id' => $trading_group,
                                'garage_number' => isset($network['code']) ? $network['code'] : $distributor_distributor_network_exist['DistributorDistributorNetwork']['garage_number'],
                                'contract_start_date' => isset($network['date_signature']) && ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : $distributor_distributor_network_exist['DistributorDistributorNetwork']['contract_start_date'],
                                'contract_end_date' => isset($network['contract_end_date']) && ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : $distributor_distributor_network_exist['DistributorDistributorNetwork']['contract_end_date'],
                                'reason_leaving' => null,
                                'status' => ($network['actif'] == true) ? ConstantsNetworksStatus::LIVE : ConstantsNetworksStatus::UNSUBSCRIBE,
                            )
                        );

                        $distributor_distributor_network = $this->findByDistributorIdAndNetworkId($distributor_exist['Distributor']['id'], $network_id);
                        if (!empty($distributor_distributor_network) && $distributor_distributor_network['DistributorDistributorNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                            if (($distributor_distributor_network_tmp['DistributorDistributorNetwork']['status'] == ConstantsNetworksStatus::LIVE)) {
                                $last = ConstantsBooleans::YES;
                            } else {
                                $last = ConstantsBooleans::NO;
                            }
                        } else {
                            $last = ConstantsBooleans::YES;
                        }
                        $distributor_distributor_network_tmp['DistributorDistributorNetwork']['last'] = $last;

                        $this->validator()->remove('contract_sent_date');
                        if (!$this->save($distributor_distributor_network_tmp)) {
                            CakeLog::write('updates-france', 'The Network distributor with ID DISTRIBUTOR ' . $distributor_exist['Distributor']['id'] . ' could not be updated.' . PHP_EOL);
                        }

                        if (isset($network['date_signature']) && ($network['date_signature'] != '0000-00-00') && (strtotime($distributor_creation_date) > strtotime($network['date_signature']))) {
                            $distributor_creation_date = $network['date_signature'];
                        }
                    }
                } else if (
                    $network['nom'] == 'Pare-Brise Center' ||
                    $network['nom'] == 'Utilitaire Service Center' ||
                    $network['nom'] == 'Pneumatiques Service Center'
                ) {
                    $this->LabelType = ClassRegistry::init('LabelType');
                    $this->DistributorLabel = ClassRegistry::init('DistributorLabel');
                    $label = $this->LabelType->findByName($network['nom']);

                    //If this label doesn't exits
                    if (empty($label)) {
                        $label_tmp = array(
                            'LabelType' => array(
                                'name' => $network['nom'],
                            )
                        );
                        $label_model->create();
                        $label = $this->LabelType->save($label_tmp);
                        if (!$label) {
                            CakeLog::write('updates-france', 'Label could not be created.' . PHP_EOL);
                        }
                    }
                    $label_id = $label['LabelType']['id'];

                    $distributor_label_exist = $this->DistributorLabel->findByDistributorIdAndLabelTypeId($distributor_exist['Distributor']['id'], $label_id);
                    if (!$distributor_label_exist) {
                        $distributor_label_tmp = array(
                            'DistributorLabel' => array(
                                'distributor_id' => $distributor_exist['Distributor']['id'],
                                'label_type_id' => $label_id,
                                'start_date' => ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : null,
                                'end_date' => ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : null,
                                'modification_date' => date('Y-m-d'),
                            )
                        );

                        $this->DistributorLabel->validator()->remove('start_date');
                        $this->DistributorLabel->create();
                        $distributor_bd = $this->DistributorLabel->save($distributor_label_tmp);
                        if (!$distributor_bd) {
                            CakeLog::write('updates-france', 'Distributor Label could not be created.' . PHP_EOL);
                        }

                        if (($network['date_signature'] != '0000-00-00') && (strtotime($distributor_creation_date) > strtotime($network['date_signature']))) {
                            $distributor_creation_date = $network['date_signature'];
                        }
                    } else {
                        $distributor_label_tmp = array(
                            'DistributorLabel' => array(
                                'id' => $distributor_label_exist['DistributorLabel']['id'],
                                'distributor_id' => $distributor_exist['Distributor']['id'],
                                'label_type_id' => $label_id,
                                'start_date' => isset($network['date_signature']) && ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : $distributor_label_exist['DistributorLabel']['start_date'],
                                'end_date' => isset($network['date_resiliation']) && ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : $distributor_label_exist['DistributorLabel']['end_date'],
                                'modification_date' => date('Y-m-d'),
                            )
                        );

                        $this->DistributorLabel->validator()->remove('start_date');
                        $distributor_bd = $this->DistributorLabel->save($distributor_label_tmp);
                        if (!$distributor_bd) {
                            CakeLog::write('updates-france', 'Distributor Label could not be updated.' . PHP_EOL);
                        }

                        if (isset($network['date_signature']) && ($network['date_signature'] != '0000-00-00') && (strtotime($distributor_creation_date) > strtotime($network['date_signature']))) {
                            $distributor_creation_date = $network['date_signature'];
                        }
                    }
                } else if (
                    $network['nom'] == 'Infotruck' ||
                    $network['nom'] == 'Cattronic' ||
                    $network['nom'] == 'Hotline technique' ||
                    $network['nom'] == 'Precisio' ||
                    $network['nom'] == 'Drivista' ||
                    $network['nom'] == 'Drivista-shop' ||
                    $network['nom'] == 'Geraucat'
                ) {
                    $this->ServiceType = ClassRegistry::init('ServiceType');
                    $this->DistributorService = ClassRegistry::init('DistributorService');
                    $aag_service = $this->ServiceType->findByName($network['nom']);

                    //If this service doesn't exits
                    if (empty($aag_service)) {
                        $aag_service_tmp = array(
                            'ServiceType' => array(
                                'name' => $network['nom'],
                            )
                        );
                        $this->ServiceType->create();
                        $aag_service = $this->ServiceType->save($aag_service_tmp);
                        if (!$aag_service) {
                            CakeLog::write('updates-france', 'Service could not be created.' . PHP_EOL);
                        }
                    }
                    $aag_service_id = $aag_service['ServiceType']['id'];
                    $distributor_service_exist = $this->DistributorService->findByDistributorIdAndServiceTypeId($distributor_exist['Distributor']['id'], $aag_service_id);

                    if (!$distributor_service_exist) {
                        $distributor_service_tmp = array(
                            'DistributorService' => array(
                                'distributor_id' => $distributor_exist['Distributor']['id'],
                                'service_type_id' => $aag_service_id,
                                'start_date' => ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : null,
                                'end_date' => ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : null,
                                'modification_date' => null,
                            )
                        );

                        $this->DistributorService->validator()->remove('start_date');
                        $this->DistributorService->create();
                        $distributor_service_model_bd = $this->DistributorService->save($distributor_service_tmp);
                        if (!$distributor_service_model_bd) {
                            CakeLog::write('updates-france', 'Distributor Service could not be created.' . PHP_EOL);
                        }
                        if (($network['date_signature'] != '0000-00-00') && (strtotime($distributor_creation_date) > strtotime($network['date_signature']))) {
                            $distributor_creation_date = $network['date_signature'];
                        }
                    } else {
                        $distributor_service_tmp = array(
                            'DistributorService' => array(
                                'id' => $distributor_service_exist['DistributorService']['id'],
                                'distributor_id' => $distributor_exist['Distributor']['id'],
                                'service_type_id' => $aag_service_id,
                                'start_date' => isset($network['date_signature']) && ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : $distributor_service_exist['DistributorService']['start_date'],
                                'end_date' => isset($network['date_resiliation']) && ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : $distributor_service_exist['DistributorService']['end_date'],
                                'modification_date' => null,
                            )
                        );

                        $this->DistributorService->validator()->remove('start_date');
                        $this->DistributorService->create();
                        $distributor_service_model_bd = $this->DistributorService->save($distributor_service_tmp);
                        if (!$distributor_service_model_bd) {
                            CakeLog::write('updates-france', 'Distributor Service could not be created.' . PHP_EOL);
                        }
                        if (isset($network['date_signature']) && ($network['date_signature'] != '0000-00-00') && (strtotime($distributor_creation_date) > strtotime($network['date_signature']))) {
                            $distributor_creation_date = $network['date_signature'];
                        }
                    }
                } else {

                    $this->ServiceType = ClassRegistry::init('ServiceType');
                    $this->DistributorService = ClassRegistry::init('DistributorService');
                    $aag_service = $this->ServiceType->findByName($network['nom']);

                    //If this service doesn't exits
                    if (empty($aag_service)) {
                        $aag_service_tmp = array(
                            'ServiceType' => array(
                                'name' => $network['nom'],
                            )
                        );
                        $this->ServiceType->create();
                        $aag_service = $this->ServiceType->save($aag_service_tmp);
                        if (!$aag_service) {
                            CakeLog::write('updates-france', 'Service could not be created.' . PHP_EOL);
                        }
                    }
                    $aag_service_id = $aag_service['ServiceType']['id'];
                    $distributor_service_exist = $this->DistributorService->findByDistributorIdAndServiceTypeId($distributor_exist['Distributor']['id'], $aag_service_id);
                    if (!$distributor_service_exist) {
                        $distributor_service_tmp = array(
                            'DistributorService' => array(
                                'distributor_id' => $distributor_exist['Distributor']['id'],
                                'service_type_id' => $aag_service_id,
                                'start_date' => ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : null,
                                'end_date' => ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : null,
                                'modification_date' => null,
                            )
                        );

                        $this->DistributorService->validator()->remove('start_date');
                        $this->DistributorService->create();
                        $distributor_service_model_bd = $this->DistributorService->save($distributor_service_tmp);
                        if (!$distributor_service_model_bd) {
                            CakeLog::write('updates-france', 'Distributor Service could not be created.' . PHP_EOL);
                        }
                        if (($network['date_signature'] != '0000-00-00') && (strtotime($distributor_creation_date) > strtotime($network['date_signature']))) {
                            $distributor_creation_date = $network['date_signature'];
                        }
                    } else {
                        $distributor_service_tmp = array(
                            'DistributorService' => array(
                                'id' => $distributor_service_exist['DistributorService']['id'],
                                'distributor_id' => $distributor_exist['Distributor']['id'],
                                'service_type_id' => $aag_service_id,
                                'start_date' => isset($network['date_signature']) && ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : $distributor_service_exist['DistributorService']['start_date'],
                                'end_date' => isset($network['date_resiliation']) && ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : $distributor_service_exist['DistributorService']['end_date'],
                                'modification_date' => null,
                            )
                        );

                        $this->DistributorService->validator()->remove('start_date');
                        $this->DistributorService->create();
                        $distributor_service_model_bd = $this->DistributorService->save($distributor_service_tmp);
                        if (!$distributor_service_model_bd) {
                            CakeLog::write('updates-france', 'Distributor Service could not be created.' . PHP_EOL);
                        }
                        if (isset($network['date_signature']) && ($network['date_signature'] != '0000-00-00') && (strtotime($distributor_creation_date) > strtotime($network['date_signature']))) {
                            $distributor_creation_date = $network['date_signature'];
                        }
                    }
                }
            }
            $this->commit();
        }

        return $distributor_creation_date;
    }

    public function getNetworksByDistributor($distributor_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'DistributorDistributorNetwork.distributor_id' => $distributor_id
                ),
                'fields' => array(
                    'DistributorDistributorNetwork.network_id',
                ),
            )
        );
    }
}
