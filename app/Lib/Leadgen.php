<?php
App::uses('HttpSocket', 'Network/Http');
App::uses('ApiEmail', 'Lib');
App::uses('Garage', 'Model');

class Leadgen
{
    /**
     * Get Leadgen token.
     */
    private function getLeadgenToken()
    {
        try {
            $url = GNMAAG_LEADGEN_API_URL_CONFIGURATION . Configure::read('leadgen_api.login_endpoint');
            $httpSocket = new HttpSocket(
                array(
                    'ssl_verify_peer' => false,
                    'ssl_verify_host' => false,
                    'ssl_allow_self_signed' => true,
                    'ssl_verify_peer_name' => false
                )
            );
            $input = array(
                'email' => GNMAAG_LEADGEN_API_EMAIL,
                'password' => Texto::encryptDecryptText(GNMAAG_LEADGEN_API_KEY, false)
            );
            $headers = array();
            $results = $httpSocket->post($url, $input, $headers);
            if (!isset($results->body)) {
                return null;
            }
            $resultsArray = json_decode($results->body, true);
            if (!isset($resultsArray['token'])) {
                return null;
            }
            return $resultsArray['token'];
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Get Leadgen token - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Send request to Leadgen.
     */
    private function sendRequests($url, $inputArray, $isGet = true)
    {
        try {
            $token = $this->getLeadgenToken();

            if ($token == null) {
                return null;
            }

            $httpSocket = new HttpSocket(
                array(
                    'ssl_verify_peer' => false,
                    'ssl_verify_host' => false,
                    'ssl_allow_self_signed' => true,
                    'ssl_verify_peer_name' => false
                )
            );

            $headers = array(
                'header' => array(
                    'Authorization' => 'Bearer ' . $token,
                ),
                'Content-Type' => 'application/json',
                'User-Agent' => 'CakePHP'
            );

            $results = $isGet ?
                $httpSocket->get($url, $inputArray, $headers) :
                $httpSocket->post($url, $inputArray, $headers);

            if (!isset($results->body)) {
                return null;
            }

            return json_decode($results->body, true);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Send request - An error has occured: " . $e->getMessage(), true));
        }
    }

    /**
     * Sync the fluids of all the networks linked to Leadgen.
     */
    public function fluidsSync($networkId = null)
    {
        try {
            $networkClass = ClassRegistry::init('Network');
            $fluidClass = ClassRegistry::init('Fluid');
            $genartMasterClass = ClassRegistry::init('GenartMaster');
            $genartFamilyClass = ClassRegistry::init('GenartFamily');

            $fluidClass->getDataSource()->begin();

            $networksLeadgenIds = array();
            if (!empty($networkId)) {
                $networksLeadgenIds[] = $networkId;
            } else {
                // get all the network linked to leadgen (networks with quoting and pricing type filled) from our BD
                $networksLeadgen = $networkClass->getLeadgenNetworks();

                // extract IDs from the list
                $networksLeadgenIds = Hash::extract($networksLeadgen, '{n}.Network.id');
            }

            foreach ($networksLeadgenIds as $networkId) {
                // fluids obtained from Leadgen
                $fluidsLeadgen = $this->getFluids($networkId);
                if ($fluidsLeadgen == null) {
                    continue;
                }

                if (!empty($fluidsLeadgen)) {
                    // fluids obtained from our BD
                    $fluidsNetwork = $fluidClass->find('all', array(
                        'conditions' => array('network_id' => $networkId),
                        'fields' => array('id', 'code')
                    ));

                    $fluidsNetworkCodes = array();
                    foreach ($fluidsNetwork as $fluid) {
                        $fluidsNetworkCodes[$fluid['Fluid']['code']] = $fluid['Fluid']['id'];
                    }

                    // updates or create fluids whether is in fluidsLeadgen or not
                    foreach ($fluidsLeadgen['fluids'] as $fluid) {
                        $fluidExist = $fluidClass->find('first', array(
                            'conditions' => array(
                                'code' => $fluid['code'],
                                'network_id' => $networkId
                            )
                        ));

                        // if the fluid already exists it is modified
                        if (!empty($fluidExist)) {
                            $fluidExist['Fluid']['code'] = $fluid['code'];
                            //name field is not used anymore
                            //update different languages here
                            $fluidExist['Fluid']['name_en'] = $fluid['name_en'];
                            $fluidExist['Fluid']['name_fr'] = $fluid['name_fr'];
                            $fluidExist['Fluid']['name_de'] = $fluid['name_de'];
                            $fluidExist['Fluid']['name_nl'] = $fluid['name_nl'];
                            $fluidExist['Fluid']['name_es'] = $fluid['name_es'];
                            $fluidExist['Fluid']['price'] = $fluid['price'];
                            $fluidExist['Fluid']['has_advanced_settings'] = $fluid['hasAdvancedSettings'] ?? ConstantsBooleans::NO_ACTIVE;

                            // update the fluid and unset the code in the fluids array
                            if (!$fluidClass->updateFluid($fluidExist)) {
                                return false;
                            }
                            unset($fluidsNetworkCodes[$fluid['code']]);
                        } else {
                            if (!$fluidClass->saveFluid($fluid, $networkId)) {
                                return false;
                            }
                        }

                        if (isset($fluid['specifications'])) {
                            foreach ($fluid['specifications'] as $fluid_spec) {
                                $fluidSpecExist = $fluidClass->find('first', array(
                                    'conditions' => array(
                                        'code' => $fluid_spec['code'],
                                        'network_id' => $networkId
                                    )
                                ));

                                if (!empty($fluidSpecExist)) {
                                    $fluidSpecExist['Fluid']['code'] = $fluid_spec['code'];
                                    $fluidSpecExist['Fluid']['name_en'] = $fluid_spec['name'];
                                    $fluidSpecExist['Fluid']['name_es'] = $fluid_spec['name'];
                                    $fluidSpecExist['Fluid']['name_fr'] = $fluid_spec['name'];
                                    $fluidSpecExist['Fluid']['name_de'] = $fluid_spec['name'];
                                    $fluidSpecExist['Fluid']['name_nl'] = $fluid_spec['name'];
                                    $fluidSpecExist['Fluid']['price'] = $fluid_spec['price'];
                                    if (!$fluidClass->updateFluid($fluidSpecExist, $fluid['code'])) {
                                        return false;
                                    }
                                } else {
                                    $fluid_spec['name_en'] = $fluid_spec['name'];
                                    $fluid_spec['name_fr'] = $fluid_spec['name'];
                                    $fluid_spec['name_de'] = $fluid_spec['name'];
                                    $fluid_spec['name_nl'] = $fluid_spec['name'];
                                    $fluid_spec['name_es'] = $fluid_spec['name'];
                                    unset($fluid_spec['name']);
                                    if (!$fluidClass->saveFluid($fluid_spec, $networkId, $fluid['code'])) {
                                        return false;
                                    }
                                }
                                unset($fluidsNetworkCodes[$fluid_spec['code']]);
                            }
                        }
                    }

                    // disable fluids that aren't in the leadgen info
                    foreach ($fluidsNetworkCodes as $fluidNetworkId) {
                        if (!$fluidClass->disableFluid($fluidNetworkId)) {
                            return false;
                        }
                    }

                    // If there is a family with genarts of type advanced settings but it is removed from leadgen, when synchronizing the data,
                    // the genart of that family is deleted and if there is only that one, the family is deleted.
                    $genartMasterClass->removeFamiliesFromNotAdvancedSettingsGenarts($networkId);
                    $genartFamilyClass->deleteEmptyFamilies($networkId);
                } else {
                    return false;
                }
            }

            $fluidClass->getDataSource()->commit();
            return true;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Fluids sync - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * API call to Leadgen to get fluids.
     *
     * @param int $networkId
     */
    public function getFluids($networkId)
    {
        try {
            $url = GNMAAG_LEADGEN_API_URL_CONFIGURATION . Configure::read('leadgen_api.fluids_endpoint');
            $input = array(
                'network_id' => $networkId
            );
            return $this->sendRequests($url, $input);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Get fluids - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Sync the works of all the networks linked to Leadgen.
     */
    public function worksSync($networkId = null)
    {
        try {
            $networkClass = ClassRegistry::init('Network');
            $workClass = ClassRegistry::init('Work');
            $garageNetworkClass = ClassRegistry::init('GarageNetwork');
            $garageNetworkWorkClass = ClassRegistry::init('GarageNetworkWork');

            $workClass->getDataSource()->begin();

            if (!empty($networkId)) {
                $networksLeadgenIds[] = $networkId;
            } else {
                // get all the network linked to leadgen (networks with quoting and pricing type filled) from our BD
                $networksLeadgen = $networkClass->getLeadgenNetworks();

                // extract IDs from the list
                $networksLeadgenIds = Hash::extract($networksLeadgen, '{n}.Network.id');
            }

            foreach ($networksLeadgenIds as $networkId) {
                // works obtained from Leadgen
                $worksLeagden = $this->getWorks($networkId);
                if ($worksLeagden == null) {
                    continue;
                }

                $worksLeagdenIds = Hash::extract($worksLeagden, 'works.{n}.id');
                $worksLeadgenIdsActivateAllGarages = array();

                // open transaction
                $worksFromDb = $workClass->find('all', array(
                    'conditions' => array('network_id' => $networkId),
                    'fields' => array("Work.id", "Work.code")
                ));

                foreach ($worksLeagden["works"] as $work) {
                    if ($work['activate_works_all_garages'] == 1) {
                        $worksLeadgenIdsActivateAllGarages[] = $work['id'];
                    }

                    $workExistById = $workClass->find('first', array(
                        'conditions' => array(
                            'code' => $work['id'],
                            'network_id' => $networkId
                        )
                    ));
                    if ($workExistById) {
                        // update
                        if (!$workClass->save(array(
                            'id' => $workExistById["Work"]['id'],
                            'name_en' => $work['name_en'],
                            'name_fr' => $work['name_fr'],
                            'name_de' => $work['name_de'],
                            'name_nl' => $work['name_nl'],
                            'name_es' => $work['name_es'],
                            'active' => 1
                        ))) {
                            return false;
                        }
                    } else {
                        // create
                        $workClass->create();
                        if (!$workClass->save(array(
                            'network_id' => $networkId,
                            'name_en' => $work['name_en'],
                            'name_fr' => $work['name_fr'],
                            'name_de' => $work['name_de'],
                            'name_nl' => $work['name_nl'],
                            'name_es' => $work['name_es'],
                            'active' => 1,
                            'code' => $work['id']
                        ))) {
                            return false;
                        }
                    }
                }

                // disable all the works that are not received
                foreach ($worksFromDb as $workDb) {
                    $codeDb = $workDb["Work"]['code'];
                    if (!in_array($codeDb, $worksLeagdenIds)) {
                        $workDbSave = $workClass->save(array(
                            'id' => $workDb["Work"]['id'],
                            'active' => ConstantsBooleans::NO_ACTIVE
                        ));
                        if (!$workDbSave) {
                            return false;
                        }
                    }
                }

                // Insert works in garages_networks
                // Active works from Leadgen which activate to all garages
                $worksFromDbActive = $workClass->find('all', array(
                    'conditions' => array(
                        'network_id' => $networkId,
                        'active' => ConstantsBooleans::ACTIVE,
                        'code' => $worksLeadgenIdsActivateAllGarages
                    ),
                    'fields' => array("id")
                ));
                $worksIdsFromDbActive = Hash::extract($worksFromDbActive, '{n}.Work.id');

                //garages_networks for the network
                $garageNetworksInNetwork = $garageNetworkClass->find('all', array(
                    'conditions' => array(
                        'network_id' => $networkId,
                        'status' => ConstantsNetworksStatus::LIVE
                    ),
                    'fields' => array("id")
                ));
                $garageNetworksInNetworkIds = Hash::extract($garageNetworksInNetwork, '{n}.GarageNetwork.id');

                foreach ($worksIdsFromDbActive as $workActiveId) {
                    //garages_networks_works for a work
                    $garageNetworkWorkInNetwork = $garageNetworkWorkClass->find('all', array(
                        'conditions' => array(
                            'work_id' => $workActiveId
                        ),
                        'fields' => array("garage_network_id")
                    ));
                    $garageNetworkWorkInNetworkIds = Hash::extract($garageNetworkWorkInNetwork, '{n}.GarageNetworkWork.garage_network_id');

                    foreach ($garageNetworksInNetworkIds as $garageNetworkId) {
                        if (!in_array($garageNetworkId, $garageNetworkWorkInNetworkIds)) {
                            // insert
                            $garageNetworkWorkClass->create();
                            if (!$garageNetworkWorkClass->save(array(
                                'garage_network_id' => $garageNetworkId,
                                'work_id' => $workActiveId
                            ))) {
                                return false;
                            }
                        }
                    }
                }
            }

            // commit transaction
            $workClass->getDataSource()->commit();
            return true;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Works sync - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * API call to Leadgen to get works.
     *
     * @param int $networkId
     */
    public function getWorks($networkId)
    {
        try {
            $url = GNMAAG_LEADGEN_API_URL_CONFIGURATION . Configure::read('leadgen_api.works_endpoint');
            $input = array(
                'network_id' => $networkId
            );
            return $this->sendRequests($url, $input);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Get works - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * API call to Leadgen to get works for a specific plate.
     *
     * @param int $networkId
     */
    public function getVehicleWorks($networkId, $plate, $vin, $vehicle_id_leadgen)
    {
        try {
            $url = GNMAAG_LEADGEN_API_URL_CONFIGURATION . Configure::read('leadgen_api.works_endpoint');
            $input = array(
                'network_id' => $networkId,
                'plate' => $plate,
                'vin' => $vin,
                'vehicle_id' => $vehicle_id_leadgen
            );
            return $this->sendRequests($url, $input);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Get vehicle works - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * API call to Leadgen to get quotations.
     *
     * @param int $garagr_id Garage ID
     * @param int $page Page
     * @param int $pageSize Page size (number of quotations per page)
     * @param array $networks Networks
     * @param array $searcher Search parameters
     */
    public function getQuotations($garageId, $page, $pageSize, $networks, $searcher)
    {
        try {
            $dateFrom = isset($searcher["from"]) && !empty($searcher["from"]) ? date("Y-m-d", strtotime($searcher["from"])) : '';
            $dateTo = isset($searcher["to"]) && !empty($searcher["to"]) ? date("Y-m-d", strtotime($searcher["to"])) : '';
            $networks = isset($searcher["network"]) && !empty($searcher["network"]) ? array($searcher["network"]) : $networks;
            $quotationId = isset($searcher["quotation_id"]) && !empty($searcher["quotation_id"]) ? $searcher["quotation_id"] : '';

            $url = GNMAAG_LEADGEN_API_URL_CONFIGURATION . Configure::read('leadgen_api.quotations_endpoint');

            //Leadgen saves garages guids so we send them and afterwards we convert the recived guids into ids
            $garage = ClassRegistry::init('Garage');
            $input = array(
                'garage_id' => $garage->findById($garageId)['Garage']['guid'],
                'page' => $page,
                'page_size' => $pageSize,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'networks' => $networks,
                'quotation_id' => $quotationId
            );
            $response = $this->sendRequests($url, $input);

            if (isset($response['quotations'][0])) {
                foreach ($response['quotations'] as $key => $value) {
                    $garageId = null;
                    if (isset($value['garage_id'])) {
                        $foundGarage = $garage->findByGuid($value['garage_id'], ['id']);
                        if (isset($foundGarage['Garage']['id'])) {
                            $garageId = $foundGarage['Garage']['id'];
                        } else {
                            $garageId = null;
                        }
                    }
                    $response['quotations'][$key]['garage_id'] = $garageId;
                }
            }

            return $response;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Get Quotations - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Sync the detail PDFs of all the quotations linked to Leadgen.
     */
    public function quotationsDetailsPdfSync()
    {
        try {
            $quotationClass = ClassRegistry::init('Quotation');
            $networkClass = ClassRegistry::init('Network');

            // begin transaction
            $quotationClass->getDataSource()->begin();

            // get all the network linked to leadgen (networks with quoting and pricing type filled) from our BD
            $networksLeadgen = $networkClass->getLeadgenNetworks();

            // extract IDs from the list
            $networksLeadgenIds = Hash::extract($networksLeadgen, '{n}.Network.id');

            $limit = 10;

            foreach ($networksLeadgenIds as $networkId) {
                $maxCalls = 50;
                $calls = 0;

                do {
                    // get last inserted ID for the network
                    $quotation = $quotationClass->find('first', array(
                        'conditions' => array('network_id' => $networkId),
                        'order' => array('id' => 'DESC')
                    ));
                    $lastQuotationId = $quotation ? $quotation['Quotation']['id_leadgen'] : 0;
                    $quotationDetailPdfsLeadgen = $this->getQuotationDetailsPdfs($lastQuotationId, $networkId, $limit);

                    if (!empty($quotationDetailPdfsLeadgen['quotations'])) {
                        $this->saveQuotations($quotationDetailPdfsLeadgen['quotations']);
                    }
                    // commit transaction
                    $quotationClass->getDataSource()->commit();

                    $calls++;
                } while ($calls < $maxCalls && !empty($quotationDetailPdfsLeadgen['quotations']));
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Quotations details PDF sync - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Save all quotations obtained from Leadgen call.
     *
     * Only save the details PDF if "base_64" isn't empty, otherwise save the quotation marking it as deleted.
     */
    private function saveQuotations($quotationsLeadgen)
    {
        try {
            $garage = ClassRegistry::init('Garage');
            $quotationClass = ClassRegistry::init('Quotation');

            foreach ($quotationsLeadgen as $quotation) {
                //Leadgen saves garages guids but we save those as their corresponding ids.
                if (isset($quotation['garage_id'])) {
                    $foundGarage = $garage->findByGuid($quotation['garage_id'], ['id']);
                    if (isset($foundGarage['Garage']['id'])) {
                        $quotation['garage_id'] = $foundGarage['Garage']['id'];
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
                if (!empty($quotation['base_64'])) {
                    if (!$quotationClass->uploadQuotationDetailsPdf($quotation)) {
                        exit;
                    }
                } else {
                    if (!$quotationClass->saveQuotation($quotation, ConstantsBooleans::YES)) {
                        exit;
                    }
                }
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Save Quotations - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * API call to Leadgen to get quotations detail PDFs.
     *
     * @param int $networkId
     */
    public function getQuotationDetailsPdfs($lastQuotationId, $networkId, $limit)
    {
        try {
            $url = GNMAAG_LEADGEN_API_URL_CONFIGURATION . Configure::read('leadgen_api.quotations_pdf_endpoint');
            $input = array(
                'id' => $lastQuotationId,
                'network_id' => $networkId,
                'limit' => $limit
            );
            return $this->sendRequests($url, $input);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Get Quotation Details PDF - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * API call to Leadgen to get one quotation PDF
     *
     * @param int $networkId
     */
    public function getQuotationPdf($quotationId)
    {
        try {
            $url = GNMAAG_LEADGEN_API_URL_CONFIGURATION . Configure::read('leadgen_api.quotations_document') . '/' . $quotationId;
            $input = array();
            return $this->sendRequests($url, $input);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Get quotation PDF - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Sync the discount/markup/surcharge/labours of all the networks linked to Leadgen.
     *
     * work.grouping_genart_id
     * grouping_genart
     * genart.grouping_genart_id
     * garage_network_genart.genart_id + ¿.work_id? to be confirmed if needed
     */
    public function workPricesSync($networkId = null)
    {
        try {
            $workClass = ClassRegistry::init('Work');
            $groupingGenartClass = ClassRegistry::init('GroupingGenart');
            $genartClass = ClassRegistry::init('Genart');
            $genartMasterClass = ClassRegistry::init('GenartMaster');
            $networkClass = ClassRegistry::init('Network');
            $genartFamilyClass = ClassRegistry::init('GenartFamily');

            // begin transaction
            $workClass->getDataSource()->begin();
            if (!empty($networkId)) {
                $networksLeadgenIds[] = $networkId;
            } else {
                // get all the network linked to leadgen (networks with quoting and pricing type filled) from our BD
                $networksLeadgen = $networkClass->getLeadgenNetworks();

                // extract IDs from the list
                $networksLeadgenIds = Hash::extract($networksLeadgen, '{n}.Network.id');
            }

            foreach ($networksLeadgenIds as $networkId) {
                // work prices obtained from Leadgen
                $workPricesLeadgen = $this->getWorkPrices($networkId);
                if ($workPricesLeadgen == null) {
                    continue;
                }

                // list to process a grouping genart only once
                $listGroupingGenartsProcessed = array();

                // list to create/update all GenartMaster
                $genartsMaster = array();

                // update all GenartMaster of this network to active = 0 first
                $genartsMasterDb = $genartMasterClass->find('all', array(
                    'conditions' => array(
                        'network_id' => $networkId
                    )
                ));
                foreach ($genartsMasterDb as $genartMasterDb) {
                    $genartMasterDb['GenartMaster']['active'] = ConstantsBooleans::NO_ACTIVE;
                    if (!$genartMasterClass->save($genartMasterDb)) {
                        return false;
                    }
                }

                foreach ($workPricesLeadgen['works'] as $key => $workPrices) {
                    $work = $workClass->find('first', array(
                        'conditions' => array(
                            'network_id' => $networkId,
                            'code' => $workPrices['work_id']
                        )
                    ));

                    // the work must exist (created with sync method)
                    if (empty($work)) {
                        continue;
                    }

                    // if genarts have been specified
                    if (!empty($workPrices['genarts'])) {
                        $groupingGenart = $groupingGenartClass->find('first', array(
                            'conditions' => array(
                                'network_id' => $networkId,
                                'code' => $workPrices['grouping_id']
                            )
                        ));

                        if (empty($groupingGenart)) {
                            // create GroupingGenart
                            $groupingGenartClass->create();
                            $groupingGenart = $groupingGenartClass->save(array(
                                'network_id' => $networkId,
                                'code' => $workPrices['grouping_id']
                            ));
                            if (!$groupingGenart) {
                                return false;
                            }
                        }

                        // update work with the GroupingGenart
                        $work['Work']['grouping_genart_id'] = $groupingGenart['GroupingGenart']['id'];
                        if (!$workClass->save($work)) {
                            return false;
                        }

                        // create genarts if they are not created yet in this sync call
                        if (!in_array($workPrices['grouping_id'], $listGroupingGenartsProcessed)) {
                            // update all genarts of this grouping genart to active = 0 first
                            $genartsDb = $genartClass->find('all', array(
                                'conditions' => array(
                                    'grouping_genart_id' => $groupingGenart['GroupingGenart']['id']
                                )
                            ));
                            foreach ($genartsDb as $genartDb) {
                                $genartDb['Genart']['active'] = ConstantsBooleans::NO_ACTIVE;
                                if (!$genartClass->save($genartDb)) {
                                    return false;
                                }
                            }

                            // create or update genarts
                            foreach ($workPrices['genarts'] as $genart) {
                                // if genart code isn't included in the genarts master array to create/update
                                if (!array_key_exists($genart['code'], $genartsMaster)) {
                                    $genartsMaster[$genart['code']] = array(
                                        'name_en' => $genart['name_en'],
                                        'name_fr' => $genart['name_fr'],
                                        'name_de' => $genart['name_de'],
                                        'name_nl' => $genart['name_nl'],
                                        'name_es' => $genart['name_es'],
                                        'include_vat' => isset($genart['include_vat']) ? $genart['include_vat'] : ConstantsBooleans::NO_ACTIVE,
                                        'is_labour_time' => isset($genart['is_labour_time']) ? $genart['is_labour_time'] : ConstantsBooleans::NO_ACTIVE,
                                        'price_labour_time' => isset($genart['price_labour_time']) ? $genart['price_labour_time'] : null
                                    );
                                }

                                $genartFound = $genartClass->find('first', array(
                                    'conditions' => array(
                                        'code' => $genart['code'],
                                        'grouping_genart_id' => $groupingGenart['GroupingGenart']['id']
                                    )
                                ));

                                if (empty($genartFound)) {
                                    // create genart
                                    $gentartNew = array(
                                        'code' => $genart['code'],
                                        'name_en' => $genart['name_en'],
                                        'name_fr' => $genart['name_fr'],
                                        'name_de' => $genart['name_de'],
                                        'name_nl' => $genart['name_nl'],
                                        'name_es' => $genart['name_es'],
                                        'grouping_genart_id' => $groupingGenart['GroupingGenart']['id'],
                                        'active' => ConstantsBooleans::ACTIVE,
                                        'include_vat' => isset($genart['include_vat']) ? $genart['include_vat'] : ConstantsBooleans::NO_ACTIVE,
                                        'is_labour_time' => isset($genart['is_labour_time']) ? $genart['is_labour_time'] : ConstantsBooleans::NO_ACTIVE,
                                        'price_labour_time' => isset($genart['price_labour_time']) ? $genart['price_labour_time'] : null,
                                        'discount' => isset($genart['discount']) ? $genart['discount'] : null,
                                        'markup' => isset($genart['markup']) ? $genart['markup'] : null,
                                        'surcharge' => isset($genart['surcharge']) ? $genart['surcharge'] : null,
                                    );
                                    $genartClass->create();
                                    if (!$genartClass->save($gentartNew)) {
                                        return false;
                                    }
                                } else {
                                    // update genart
                                    $genartFound['Genart']['name_en'] = $genart['name_en'];
                                    $genartFound['Genart']['name_fr'] = $genart['name_fr'];
                                    $genartFound['Genart']['name_de'] = $genart['name_de'];
                                    $genartFound['Genart']['name_nl'] = $genart['name_nl'];
                                    $genartFound['Genart']['name_es'] = $genart['name_es'];
                                    $genartFound['Genart']['active'] = 1;
                                    $genartFound['Genart']['include_vat'] = isset($genart['include_vat']) ? $genart['include_vat'] : ConstantsBooleans::NO_ACTIVE;
                                    $genartFound['Genart']['is_labour_time'] = isset($genart['is_labour_time']) ? $genart['is_labour_time'] : ConstantsBooleans::NO_ACTIVE;
                                    $genartFound['Genart']['price_labour_time'] = isset($genart['price_labour_time']) ? $genart['price_labour_time'] : null;
                                    $genartFound['Genart']['discount'] = isset($genart['discount']) ? $genart['discount'] : null;
                                    $genartFound['Genart']['markup'] = isset($genart['markup']) ? $genart['markup'] : null;
                                    $genartFound['Genart']['surcharge'] = isset($genart['surcharge']) ? $genart['surcharge'] : null;
                                    if (!$genartClass->save($genartFound)) {
                                        return false;
                                    }
                                }
                            } // genarts

                            $listGroupingGenartsProcessed[] = $workPrices['grouping_id'];
                        }
                    } else {
                        if (isset($workPrices['discount']) || isset($workPrices['markup']) || isset($workPrices['surcharge'])) {
                            // whether the discount, markup or surcharge of the work has been specified
                            $work['Work']['discount'] = isset($workPrices['discount']) ? $workPrices['discount'] : null;
                            $work['Work']['markup'] = isset($workPrices['markup']) ? $workPrices['markup'] : null;
                            $work['Work']['surcharge'] = isset($workPrices['surcharge']) ? $workPrices['surcharge'] : null;
                        }

                        $work['Work']['grouping_genart_id'] = null;

                        if (!$workClass->save($work)) {
                            return false;
                        }
                    }
                }

                foreach ($genartsMaster as $key => $genartMaster) {
                    $genartMasterFound = $genartMasterClass->find('first', array(
                        'conditions' => array(
                            'code' => $key,
                            'network_id' => $networkId
                        )
                    ));

                    if (empty($genartMasterFound)) {
                        // create GenartMaster with (genart_family_id null)
                        $gentartMasterNew = array(
                            'code' => $key,
                            'name_en' => $genartMaster['name_en'],
                            'name_fr' => $genartMaster['name_fr'],
                            'name_de' => $genartMaster['name_de'],
                            'name_nl' => $genartMaster['name_nl'],
                            'name_es' => $genartMaster['name_es'],
                            'include_vat' => isset($genartMaster['include_vat']) ? $genartMaster['include_vat'] : ConstantsBooleans::NO_ACTIVE,
                            'is_labour_time' => isset($genartMaster['is_labour_time']) ? $genartMaster['is_labour_time'] : ConstantsBooleans::NO_ACTIVE,
                            'price_labour_time' => isset($genartMaster['price_labour_time']) ? $genartMaster['price_labour_time'] : null,
                            'network_id' => $networkId,
                            'active' => ConstantsBooleans::ACTIVE
                        );
                        $genartMasterClass->create();
                        if (!$genartMasterClass->save($gentartMasterNew)) {
                            return false;
                        }
                    } else {
                        // update GenartMaster
                        $genartMasterFound['GenartMaster']['name_en'] = $genartMaster['name_en'];
                        $genartMasterFound['GenartMaster']['name_fr'] = $genartMaster['name_fr'];
                        $genartMasterFound['GenartMaster']['name_de'] = $genartMaster['name_de'];
                        $genartMasterFound['GenartMaster']['name_nl'] = $genartMaster['name_nl'];
                        $genartMasterFound['GenartMaster']['name_es'] = $genartMaster['name_es'];
                        $genartMasterFound['GenartMaster']['include_vat'] = isset($genartMaster['include_vat']) ? $genartMaster['include_vat'] : ConstantsBooleans::NO_ACTIVE;
                        $genartMasterFound['GenartMaster']['is_labour_time'] = isset($genartMaster['is_labour_time']) ? $genartMaster['is_labour_time'] : ConstantsBooleans::NO_ACTIVE;
                        $genartMasterFound['GenartMaster']['price_labour_time'] = isset($genartMaster['price_labour_time']) ? $genartMaster['price_labour_time'] : null;
                        $genartMasterFound['GenartMaster']['active'] = 1;
                        if (!$genartMasterClass->save($genartMasterFound)) {
                            return false;
                        }
                    }
                }

                $genartMasterClass->removeFamiliesFromNotActiveGenarts($networkId);
                $genartFamilyClass->deleteEmptyFamilies($networkId);
                // update labours
                $network = $networkClass->findById($networkId);

                $network['Network']['labour_hourly_price'] = $workPricesLeadgen['labour_hourly_price'];
                $network['Network']['labour_hourly_price_electric_vehicles'] = $workPricesLeadgen['labour_hourly_price_electric_vehicles'];
                if (!$networkClass->save($network)) {
                    return false;
                }
            } // network

            // commit transaction
            $workClass->getDataSource()->commit();
            return true;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Work prices sync - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * API call to Leadgen to get prices for works.
     *
     * @param int $networkId
     */
    public function getWorkPrices($networkId)
    {
        try {
            $url = GNMAAG_LEADGEN_API_URL_CONFIGURATION . Configure::read('leadgen_api.prices_endpoint');
            $input = array(
                'network_id' => $networkId
            );
            return $this->sendRequests($url, $input);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Get work prices - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Deletes all the detail PDFs of all the quotations older than 6 months.
     */
    public function deleteQuotationDetailsPdfs()
    {
        try {
            $quotationClass = ClassRegistry::init('Quotation');

            // current date minus 6 months
            $today = new DateTime(date('Y-m-d'));
            $dateMaxTo = $today->sub(new DateInterval('P6M'))->format('Y-m-d');

            $quotations = $quotationClass->find('all', array(
                'conditions' => array(
                    'creation_date <' => $dateMaxTo,
                    'deleted' => ConstantsBooleans::NO
                )
            ));

            foreach ($quotations as $quotation) {
                $quotationClass->deleteQuotationDetailsPdfs($quotation['Quotation']['id']);
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Delete quotation details PDF - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * API call to Leadgen to get quotations statistics.
     *
     * @param array $searcher Search parameters
     */
    public function getQuotationsStatistics($searcher, $labelsX, $groupby)
    {
        try {
            $dateFrom = isset($searcher["from"]) && !empty($searcher["from"]) ? date("Y-m-d", strtotime($searcher["from"])) : '';
            $dateTo = isset($searcher["to"]) && !empty($searcher["to"]) ? date("Y-m-d", strtotime($searcher["to"])) : '';

            $url = GNMAAG_LEADGEN_API_URL_CONFIGURATION . Configure::read('leadgen_api.quotations_statistics');
            $garage = ClassRegistry::init('Garage');

            $garage_id = null;
            if (!empty($searcher['garage_id'])) {
                $garageFound = $garage->findById($searcher['garage_id']);
                if (isset($garageFound['Garage']['guid'])) {
                    $garage_id = $garageFound['Garage']['guid'];
                }
            }

            $input = array(
                'network_id' => $searcher['network_id'],
                'garage_id' => $garage_id,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'labelsX' => $labelsX,
                'groupby' => $groupby
            );
            return $this->sendRequests($url, $input);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Get quotations statistics - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * API call to Leadgen to get quotation details.
     *
     * @param int $quotationId Quotation ID
     */
    public function getQuotationDetails($quotationIdLeadgen)
    {
        try {
            $url = GNMAAG_LEADGEN_API_URL_CONFIGURATION . Configure::read('leadgen_api.quotation_details_endpoint');

            $input = array(
                'quotation_id' => $quotationIdLeadgen
            );
            return $this->sendRequests($url, $input);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Leadgen - Get quotations details - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }
}
