<?php
class Update extends AppModel
{
    /*********************************************************************    DATA UK    **************************************************************************/

    /**
     * Import distributors from UK.
     */
    public function update_members()
    {
        // Include the SFTP class from phpseclib
        set_include_path(WWW_ROOT . '../Vendor/phpseclib');
        require_once WWW_ROOT . '../Vendor/phpseclib/Net/SFTP.php';

        set_time_limit(60 * 60 * 2 );
        ini_set("memory_limit", "3G");

        CakeLog::config('updates', array(
            'engine' => 'FileLog',
            'types' => array('info', 'error', 'warning'),
            'scopes' => array('updates'),
            'file' => 'updates_' . date('Y_m_d_H_i_s') . '.log',
        ));

        CakeLog::write('updates', PHP_EOL);

        $remoteFilename = ConstantesFtp::DISTRIBUTORS_JSON_NAME . date("Ymd", time()) . '.json';
        $sftpFilesDirectory = SFTP_DISTRIBUTOR_IMPORT;
        $pahtRemoteFile = $sftpFilesDirectory . '/' . $remoteFilename;

        $updateFilesDirectory = GNMAAG_DIRECTORY_UPDATE_DISTRIBUTORS;
        $pathLocalFile = $updateFilesDirectory . $remoteFilename;

        // connect to SFTP
        $sftpParams = Configure::read('autopart.FTP');
        $sftp = new Net_SFTP($sftpParams['Host'], $sftpParams['Port']);
        if (!$sftp->login(SFTP_USERNAME, Texto::encryptDecryptText(SFTP_PASSWORD, false))) {
            CakeLog::write('updates', 'Login to SFTP failed' . PHP_EOL);
            exit;
        }

        // change directory in SFTP
        if ($sftp->chdir($sftpFilesDirectory)) {
            // check if file exists in SFTP directory
            if ($sftp->file_exists($remoteFilename)) {
                // save file in local
                $resultGet = $sftp->get($pahtRemoteFile, $pathLocalFile);
                if ($resultGet) {
                    // SFTP disconnect is being called due to an error of losing connection. Leave it here.
                    $sftp->disconnect();
                    CakeLog::write('updates', '-- Reading the Members.json file --' . PHP_EOL);

                    // check if file exists in local
                    if (file_exists($pathLocalFile)) {
                        $this->Contact = ClassRegistry::init('Contact');
                        $this->Distributor = ClassRegistry::init('Distributor');
                        $this->DistributorDistributorActivity = ClassRegistry::init('DistributorDistributorActivity');
                        $this->DistributorCustomerActivity = ClassRegistry::init('DistributorCustomerActivity');
                        $this->Email = ClassRegistry::init('Email');
                        $this->User = ClassRegistry::init('User');

                        $json = file_get_contents($pathLocalFile);
                        $contents = utf8_encode($json);
                        $distributors = json_decode($contents, true);

                        if (!$distributors || !(is_array($distributors))) {
                            CakeLog::write('updates', 'Members.json is empty or not formatted correctly' . PHP_EOL);
                            CakeLog::write('updates', '-- End of Members.json file --' . PHP_EOL);
                        } else {
                            foreach ($distributors as $key => $distributor_json) {
                                // set aag_region_id to UK
                                $aagRegionId = Configure::read('AAG_REGION_ID_UK_IRELAND');
                                $distributor_json['aag_region_id'] = $aagRegionId;

                                $exist_distributor = $this->Distributor->findByAccountNumber($distributor_json['AccountNumber']);
                                if ($exist_distributor) { // If Distributor exists in the Database
                                    CakeLog::write('updates', 'Modifying the ' . $exist_distributor['Distributor']['name'] . ' Distributor - ' . $exist_distributor['Distributor']['account_number'] . PHP_EOL);

                                    // Update Distributor
                                    $distributor_update = $this->Distributor->updateDistributorUk($distributor_json, $exist_distributor);
                                    if ($distributor_update) {
                                        // Update Activities
                                        $this->DistributorCustomerActivity->updateDistributorCustomerActivity($distributor_json, $distributor_update['Distributor']['id']);

                                        // Update PrimaryContact
                                        if (isset($distributor_json['PrimaryContact'])) {
                                            $this->Contact->updateDistributorContactStaff($distributor_json['PrimaryContact'], $distributor_update['Distributor']['id'], $aagRegionId);
                                        }
                                        // Update BDM
                                        if (isset($distributor_json['BDM'])) {
                                            $this->Contact->updateDistributorBDMTG($distributor_json['BDM'], $distributor_update['Distributor']['id'], $aagRegionId);
                                        }
                                        // Update GPC BDM
                                        if (isset($distributor_json['GPCBDM'])) {
                                            $this->Contact->updateDistributorBDMGPC($distributor_json['GPCBDM'], $distributor_update['Distributor']['id'], $aagRegionId);
                                        }
                                    } else {
                                        CakeLog::write('updates', 'ERROR when modifying ' . $exist_distributor['Distributor']['name'] . ' Distributor - ' . $exist_distributor['Distributor']['account_number'] . PHP_EOL);
                                        debug('ERROR when modifying ' . $exist_distributor['Distributor']['name']);
                                    }
                                } else {
                                    // Distributor does not exist in the Database, create distributor
                                    CakeLog::write('updates', 'Creating the ' . $distributor_json['Name'] . ' Distributor - ' . $distributor_json['AccountNumber'] . PHP_EOL);

                                    $distributor_create = $this->Distributor->createDistributorUk($distributor_json);
                                    if ($distributor_create) {
                                        // Create Activities
                                        $this->DistributorCustomerActivity->createDistributorCustomerActivity($distributor_json, $distributor_create['Distributor']['id']);

                                        // CreateContact
                                        if (isset($distributor_json['PrimaryContact'])) {
                                            $this->Contact->createDistributorContactStaff($distributor_json['PrimaryContact'], $distributor_create['Distributor']['id'], $aagRegionId);
                                        }
                                        // Create BDM
                                        if (isset($distributor_json['BDM'])) {
                                            $this->Contact->createDistributorBDMTG($distributor_json['BDM'], $distributor_create['Distributor']['id'], $aagRegionId);
                                        }
                                        // Create GPC BDM
                                        if (isset($distributor_json['GPCBDM'])) {
                                            $this->Contact->createDistributorBDMGPC($distributor_json['GPCBDM'], $distributor_create['Distributor']['id'], $aagRegionId);
                                        }
                                    } else {
                                        CakeLog::write('updates', 'ERROR when creating ' . $distributor_json['Name'] . ' Distributor - ' . $distributor_json['AccountNumber'] . PHP_EOL);
                                        debug('ERROR when creating ' . $distributor_json['Name']);
                                    }
                                }
                            }
                            $this->DistributorCustomerActivity->resetAutoIncrement();

                            // Send email Imported Distributor has been completed
                            $user_data = $this->User->findByUsername('l.balchin_admin');
                            $contact_data = $this->Contact->findById($user_data['User']['contact_id']);
                            $this->Email->newEmailImportDistributor($contact_data['Contact']['email']);
                        }
                        CakeLog::write('updates', '-- End of Members.json file --' . PHP_EOL);
                    } else {
                        CakeLog::write('updates', 'Members.json file does not exist or cannot be read' . PHP_EOL);
                        CakeLog::write('updates', '-- End of Members.json file --' . PHP_EOL);
                    }
                } else {
                    CakeLog::write('updates', $remoteFilename . ' file does not exist or cannot be read' . PHP_EOL);
                }
            } else {
                CakeLog::write('updates', $remoteFilename . ' file does not exist in SFTP' . PHP_EOL);
            }
        } else {
            CakeLog::write('updates', 'Failed to change directory in SFTP' . PHP_EOL);
        }
    }

    /**
     * Import garages from UK.
     */
    public function update_garages()
    {
        set_time_limit(18000);
        ini_set("memory_limit", "1G");

        // change reporting level
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
        App::uses('ConsoleErrorHandler', 'Console');
        $error = Configure::read('Error');
        $errorHandler = new ConsoleErrorHandler();
        if (empty($error['consoleHandler'])) {
            $error['consoleHandler'] = array($errorHandler, 'handleError');
            Configure::write('Error', $error);
        }
        set_error_handler($error['consoleHandler'], E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);

        $this->Garage = ClassRegistry::init('Garage');
        $this->GarageAgreement = ClassRegistry::init('GarageAgreement');
        $this->GarageComment = ClassRegistry::init('GarageComment');
        $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');
        $this->GarageContactStaff = ClassRegistry::init('GarageContactStaff');
        $this->GarageDistributor = ClassRegistry::init('GarageDistributor');
        $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
        $this->GarageService = ClassRegistry::init('GarageService');
        $this->GarageVehicleType = ClassRegistry::init('GarageVehicleType');
        $this->GarageVehicle = ClassRegistry::init('GarageVehicle');
        $this->Email = ClassRegistry::init('Email');
        $this->Network = ClassRegistry::init('Network');

        $update_files_directory = GNMAAG_DIRECTORY_UPDATE_GARAGES; //'C:\proyectos\gnmaag_datas'."/"GNMAAG_DIRECTORY_UPDATE_GARAGES; // core.php
        $path_garages_file = $update_files_directory . "Garages.json";
        $path_garages_file_processed = GNMAAG_DIRECTORY_PROCESSED . "Pre-Garages.json";

        CakeLog::config('updates', array(
            'engine' => 'FileLog',
            'types' => array('info', 'error', 'warning'),
            'scopes' => array('updates'),
            'file' => 'updates.log',
        ));

        CakeLog::write('updates', PHP_EOL);
        CakeLog::write('updates', '-- Reading the Garages.json file --' . PHP_EOL);

        if (file_exists($path_garages_file)) {
            $json = file_get_contents($path_garages_file);
            $contents = utf8_encode($json);
            $garages = json_decode($contents, true);

            if (!$garages  || !(is_array($garages))) {
                CakeLog::write('updates', 'Garages.json is empty' . PHP_EOL);
                CakeLog::write('updates', '-- End of Garages.json file --' . PHP_EOL);
            } else {
                $path = ConstantsPath::DIR_GARAGE_UPDATES_ABSOLUTE . DS . 'garages_logs.json';
                if (file_exists($path)) { // Delete file if exists
                    unlink($path);
                }
                $networks = $this->Network->networksList();
                unset($networks['5']); // Delete LeasePlan network
                $array_networks = array();
                foreach ($networks as $key => $network) {
                    $array_networks[$key] = array('Name' => $network, 'Garages' => array(), 'Summary' => array());
                }
                $initial_data = array(
                    'WithNetworks' => $array_networks,
                    'WithoutNetworks' => array()
                );
                FileManager::generate_json_file($initial_data);
                foreach ($garages as $key => $garage_json) {
                    if (strlen($garage_json['Garage']['GarageNumberCRM']) > 0) {
                        $exist_garage = $this->Garage->findByGNumberIdAndAagRegionId($garage_json['Garage']['GarageNumberCRM'], Configure::read('AAG_REGION_ID_UK_IRELAND'));
                    } else {
                        $exist_garage = $this->Garage->findByRefCodeAndAagRegionId($garage_json['Garage']['GarageRef'], Configure::read('AAG_REGION_ID_UK_IRELAND'));
                    }

                    $errors = array();
                    if ($exist_garage) { // If Garage exists in the Database
                        CakeLog::write('updates', 'Modifying the ' . $exist_garage['Garage']['name'] . ' Garage - ' . $exist_garage['Garage']['g_number_id'] . PHP_EOL);
                        //Update Garage
                        $this->Garage->updateGarage($garage_json, $exist_garage, $errors);
                        //Update Vehicle Types
                        $this->GarageVehicleType->updateGarageVehicleType($garage_json, $exist_garage['Garage']['id'], $errors);
                        //Update Garage Service
                        $this->GarageService->updateGarageService($garage_json, $exist_garage['Garage']['id'], $errors);
                        //Update Garage Vehicle and Specialist
                        $this->GarageVehicle->updateGarageVehicle($garage_json, $exist_garage['Garage']['id'], $errors);
                        //Create Comment
                        $this->GarageComment->updateGarageNotes($garage_json, $exist_garage['Garage']['id'], $errors);
                        //Update Garage Distributor
                        $this->GarageDistributor->updateGarageDistributor($garage_json, $exist_garage['Garage']['id'], $errors);
                        //Create Network
                        $this->GarageNetwork->updateGarageNetwork($garage_json, $exist_garage['Garage']['id'], $errors);
                        //Create Garage Contact Garage Manager
                        $this->GarageContactStaff->updateGarageContactStaff($garage_json, $exist_garage['Garage']['id'], $errors);
                        //Create Contact BDM
                        $this->GarageContactBdm->updateGarageContactBdm($garage_json, $exist_garage['Garage']['id'], $errors);
                        //Create Garage Agreement
                        $this->GarageAgreement->createGarageAgreement($garage_json, $exist_garage['Garage']['id']);

                        // Send the changes to RM
                        $this->RepairMaintenance = ClassRegistry::init('RepairMaintenance');

                        $garage_json['Garage']['aag_region_id'] = $exist_garage['Garage']['aag_region_id'];
                        $garage_json['Garage']['id'] = $exist_garage['Garage']['id'];
                        $garage_json['Garage']['status'] = $garage_json['Garage']['StatusCode'] == 1 ? 2 : 3;
                        $garage_json['Garage']['province_id'] = $exist_garage['Garage']['province_id'];
                        $garage_json['Garage']['service_24h_phone'] = $exist_garage['Garage']['service_24h_phone'];

                        $this->RepairMaintenance->update_garage($garage_json);
                    } else { //Garage not exist in the Database
                        CakeLog::write('updates', 'Create the ' . $garage_json['Garage']['BusinessName'] . ' Garage - ' . $garage_json['Garage']['GarageNumberCRM'] . PHP_EOL);
                        //Create Garage
                        $garage_create = $this->Garage->createGarage($garage_json, $errors);
                        //Create Garage Distributor
                        $this->GarageDistributor->createGarageDistributor($garage_json, $garage_create['Garage']['id'], $errors);
                        //Create Network
                        $this->GarageNetwork->createGarageNetwork($garage_json, $garage_create['Garage']['id'], $errors);
                        // Create Garage Service
                        $this->GarageService->createGarageService($garage_json, $garage_create['Garage']['id'], $errors);
                        //Create Vehicle Types
                        $this->GarageVehicleType->createGarageVehicleType($garage_json, $garage_create['Garage']['id'], $errors);
                        //Create Garage Vehicle and Specialist
                        $this->GarageVehicle->createGarageVehicle($garage_json, $garage_create['Garage']['id'], $errors);
                        //Create Comment
                        $this->GarageComment->createGarageNotes($garage_json, $garage_create['Garage']['id'], $errors);
                        //Create Garage Contact Garage Manager
                        $this->GarageContactStaff->createGarageContactStaff($garage_json, $garage_create['Garage']['id'], $errors);
                        //Create Contact BDM
                        $this->GarageContactBdm->createGarageContactBdm($garage_json, $garage_create['Garage']['id'], $errors);
                        //Create Garage Agreement
                        $this->GarageAgreement->createGarageAgreement($garage_json, $garage_create['Garage']['id']);
                    }

                    // JSON file
                    $garage_data = array(
                        'BusinessName' => $garage_json['Garage']['BusinessName'],
                        'IsCreated' => $exist_garage ? 0 : 1,
                        'WithErrors' => empty($errors) ? 0 : 1,
                        'Errors' => $errors
                    );
                    $garage_id = $exist_garage ? $exist_garage['Garage']['id'] : $garage_create['Garage']['id'];
                    if (count($garage_json['Networks']) > 0) {
                        foreach ($garage_json['Networks'] as $network) {
                            if ($network['StartDate']) {
                                $network_id = $this->GarageNetwork->_networkType($network['ContractType']);
                                FileManager::generate_json_file($garage_data, 'WithNetworks', $network_id, 'Garages', $garage_id);
                            }
                        }
                    } else {
                        FileManager::generate_json_file($garage_data, 'WithoutNetworks', 'Garages', $garage_id);
                    }
                }
            }

            FileManager::get_summary_json_file();
            rename($path_garages_file, $path_garages_file_processed);
            CakeLog::write('updates', '-- End of Garages.json file --' . PHP_EOL);
        } else {
            CakeLog::write('updates', 'Garages.json file does not exist or cannot be read' . PHP_EOL);
            CakeLog::write('updates', '-- End of Garages.json file --' . PHP_EOL);
        }
    }

    /**
     * Update webs bookings to expired
     */
    public function update_bookings_set_expired()
    {
        $this->Booking = ClassRegistry::init('Booking');
        $bookings = $this->Booking->getBookingsPendingOutOfDate();
        foreach ($bookings as $booking){
            $this->Booking->changeBookingStatus($booking, ConstantsBookingsStatus::EXPIRED);
        }
    }
    /******************************************************************    DATA GERMANY    ************************************************************************/

    public function update_profiles()
    {
        set_time_limit(18000);
        ini_set("memory_limit", "1G");

        $this->Garage = ClassRegistry::init('Garage');
        $this->GarageSoftware = ClassRegistry::init('GarageSoftware');
        $this->Software = ClassRegistry::init('Software');


        $update_files_directory = GNMAAG_DIRECTORY_UPDATE_SALES; // core.php
        $path_distributor_file = $update_files_directory . "Profile.json";
        $path_distributor_file_processed = $update_files_directory . "Processed" . "/" . "Profile-" . date('Y-m-d-His') . ".json";

        CakeLog::config('updates-germany', array(
            'engine' => 'FileLog',
            'types' => array('info', 'error', 'warning'),
            'scopes' => array('updates-germany'),
            'file' => 'updates-germany.log',
        ));

        CakeLog::write('updates-germany', PHP_EOL);
        CakeLog::write('updates-germany', '-- Reading the Profile.json file --' . PHP_EOL);

        if (file_exists($path_distributor_file)) {
            $json = file_get_contents($path_distributor_file);
            $contents = utf8_encode($json);
            $software_garages = json_decode($contents, true);

            if (!$software_garages) {
                CakeLog::write('updates-germany', 'Profile.json is empty' . PHP_EOL);
                CakeLog::write('updates-germany', '-- End of Profile.json file --' . PHP_EOL);
            } else {
                foreach ($software_garages['Custumer_NO-Profile'] as $software_garage) {
                    $garage = $this->Garage->findByGNumberId($software_garage['Customer_No']);

                    if ($garage) {
                        $software = $this->Software->findByNameDe($software_garage['Profile']);
                        if (!$software) {
                            $software_tmp = array(
                                'Software' => array(
                                    'name_en' => $software_garage['Profile'],
                                    'name_fr' => $software_garage['Profile'],
                                    'name_de' => $software_garage['Profile'],
                                    'name_nl' => $software_garage['Profile'],
                                    'name_es' => $software_garage['Profile'],
                                    'aag_region_id' => $garage['Garage']['aag_region_id']
                                )
                            );

                            $this->Software->create();
                            $software = $this->Software->save($software_tmp);
                        }

                        if (!$this->GarageSoftware->findByGarageIdAndSoftwareId($garage['Garage']['id'], $software['Software']['id'])) {
                            $software_garage_tmp = array(
                                'GarageSoftware' => array(
                                    'garage_id' => $garage['Garage']['id'],
                                    'software_id' => $software['Software']['id'],
                                    'start_date' => null,
                                    'end_date' => null,
                                    'version' => null,
                                    'url' => null,
                                    'username' => null,
                                    'password' => null,
                                )
                            );

                            $this->GarageSoftware->create();
                            $this->GarageSoftware->save($software_garage_tmp);
                            CakeLog::write('updates-germany', 'Create Garage Software' . PHP_EOL);
                        }
                    } else {
                        //Garage not exist in the Database
                        CakeLog::write('updates-germany', 'Customer_No: ' . $software_garage['Customer_No'] . ' -  not exist in the Database' . PHP_EOL);
                    }
                }
            }

            rename($path_distributor_file, $path_distributor_file_processed);
            CakeLog::write('updates-germany', '-- End of Profile.json file --' . PHP_EOL);
        } else {
            CakeLog::write('updates-germany', 'Profile.json file does not exist or cannot be read' . PHP_EOL);
            CakeLog::write('updates-germany', '-- End of Profile.json file --' . PHP_EOL);
        }
    }

    public function update_kpi_de()
    {
        set_time_limit(18000);
        ini_set("memory_limit", "1G");

        $this->GarageKPI = ClassRegistry::init('GarageKPI');

        $update_files_directory = GNMAAG_DIRECTORY_UPDATE_SALES; // core.php
        $path_sales_de_file = $update_files_directory . "kpi_de.json";
        $path_sales_de_file_processed = $update_files_directory . "Processed" . "/" . date('Y-m-d-His') . " - kpi_de" . ".json";

        CakeLog::config('salesDe', array(
            'engine' => 'FileLog',
            'types' => array('info', 'error', 'warning'),
            'scopes' => array('salesDe'),
            'file' => 'salesDe.log',
        ));

        CakeLog::write('salesDe', PHP_EOL . '-- kpi_de.json file --' . PHP_EOL . PHP_EOL);

        if (file_exists($path_sales_de_file)) {
            $json = file_get_contents($path_sales_de_file);
            $contents = utf8_encode($json);
            $kpi_de = json_decode($contents, true);

            if (!$kpi_de) {
                CakeLog::write('salesDe', 'kpi_de.json is empty' . PHP_EOL);
                CakeLog::write('salesDe', PHP_EOL . '-- End of kpi_de.json file --' . PHP_EOL);
            } else {
                foreach ($kpi_de['KPI'] as $kpi_garage) {
                    $garage_kpi = $this->GarageKPI->findByCustomerNo($kpi_garage['CustomerNo']);
                    $customer_no = $kpi_garage['CustomerNo'];
                    if ($garage_kpi) { // if the customer exists, the data is updated
                        if (isset($kpi_garage['Web_Rate_IAM']) || isset($kpi_garage['Return_Order_Rate']) || isset($kpi_garage['Web_Rate_OE'])) {
                            unset($kpi_garage['CustomerNo']); // do not save it in db, it is repeated
                            $garage_kpi['GarageKPI']['kpis'] = json_encode($kpi_garage);
                            if ($this->GarageKPI->save($garage_kpi)) {
                                CakeLog::write('salesDe', 'Update a record with customer ' . $customer_no . PHP_EOL);
                            } else {
                                CakeLog::write('salesDe', 'ERROR to update a record with customer ' . $customer_no . PHP_EOL);
                            }
                        }
                    } else { // if the customer does not exist, a new record is created
                        if (isset($kpi_garage['Web_Rate_IAM']) || isset($kpi_garage['Return_Order_Rate']) || isset($kpi_garage['Web_Rate_OE'])) {
                            unset($kpi_garage['CustomerNo']); // do not save it in db, it is repeated
                            $garage_kpis_tmp = array(
                                'GarageKPI' => array(
                                    'customer_no' => $customer_no,
                                    'kpis' => json_encode($kpi_garage),
                                )
                            );

                            $this->GarageKPI->create();
                            if ($this->GarageKPI->save($garage_kpis_tmp)) {
                                CakeLog::write('salesDe', 'Creating a new record with customer ' . $customer_no . PHP_EOL);
                            } else {
                                CakeLog::write('salesDe', 'ERROR to create a new record with customer ' . $customer_no . PHP_EOL);
                            }
                        }
                    }
                }
            }
            rename($path_sales_de_file, $path_sales_de_file_processed);
            CakeLog::write('salesDe', PHP_EOL . '-- End of kpi_de.json file --' . PHP_EOL);
        } else {
            CakeLog::write('salesDe', 'kpi_de.json file does not exist or cannot be read' . PHP_EOL);
            CakeLog::write('salesDe', PHP_EOL . '-- End of kpi_de.json file --' . PHP_EOL);
        }
    }

    /*****************************************************************    DATA FRANCE    **************************************************************************/

    public function update_rep()
    {
        set_time_limit(18000);
        ini_set("memory_limit", "1G");

        $this->Garage = ClassRegistry::init('Garage');
        $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
        $this->GarageService = ClassRegistry::init('GarageService');
        $this->GarageDistributor = ClassRegistry::init('GarageDistributor');
        $this->Contact = ClassRegistry::init('Contact');

        $update_files_directory = GNMAAG_DIRECTORY_UPDATE_GARAGES; // core.php
        $files = glob($update_files_directory . "REP_ISA*.json");


        CakeLog::config('updates-france', array(
            'engine' => 'FileLog',
            'types' => array('info', 'error', 'warning'),
            'scopes' => array('updates-france'),
            'file' => 'updates-france.log',
        ));

        foreach ($files as $path_garages_file) {
            $explode_tmp = explode(DS, $path_garages_file);
            $file = end($explode_tmp);
            $explode2_tmp = explode('/', $file);
            $file_processed = end($explode2_tmp);
            $path_garages_file_processed = $update_files_directory . "Processed" . "/" . $file_processed . "__" . date('Y-m-d-His') . ".json";

            CakeLog::write('updates-france', PHP_EOL);
            CakeLog::write('updates-france', '-- Reading the ' . $file . ' file --' . PHP_EOL);

            if (file_exists($path_garages_file)) {
                $json = file_get_contents($path_garages_file);
                $contents = utf8_encode($json);
                $garages = json_decode($contents, true);

                if (!$garages) {
                    CakeLog::write('updates-france', $file . ' is empty' . PHP_EOL);
                    CakeLog::write('updates-france', '-- End of ' . $file . ' file --' . PHP_EOL);
                } else {
                    foreach ($garages['reparateur'] as $key => $rep_json) {
                        $exist_garage = $this->Garage->findByGNumberId($rep_json['id_isa']);

                        if ($exist_garage) { // If Garage exists in the Database
                            CakeLog::write('updates-france', 'Modifying ' . $exist_garage['Garage']['name'] . ' - Id isa: ' . $exist_garage['Garage']['g_number_id'] . PHP_EOL);
                            //Update Garage
                            $rep_create = $this->Garage->updateRep($rep_json, $exist_garage);
                            //Update Network
                            $garage_creation_date = $this->GarageNetwork->updateRepNetwork($rep_json, $exist_garage);
                            //Update Service
                            $this->GarageService->updateRepService($rep_json, $exist_garage);
                            //Update Garage Distributor
                            $this->GarageDistributor->updateRepDistributor($rep_json, $exist_garage);
                            //Update Colaborateur
                            $this->Contact->updateRepContact($rep_json, $exist_garage);
                            //Update GARAGE CREATION
                            $this->Garage->edit_creation_date($garage_creation_date, $exist_garage['Garage']['id']);
                        } else { //Garage not exist in the Database
                            CakeLog::write('updates-france', 'Create Garage with Id ISA: ' . $rep_json['id_isa'] . PHP_EOL);
                            //Create Garage
                            $rep_create = $this->Garage->createRep($rep_json);
                            //Create Network
                            $garage_creation_date = $this->GarageNetwork->createRepNetwork($rep_json, $rep_create['Garage']['id'], $rep_create['Garage']['creation_date']);
                            //Create Service
                            $this->GarageService->createRepService($rep_json, $rep_create['Garage']['id']);
                            //Create Garage Distributor
                            $this->GarageDistributor->createRepDistributor($rep_json, $rep_create['Garage']['id']);
                            //Colaborateur
                            $this->Contact->createRepContact($rep_json, $rep_create['Garage']['id']);
                            //Update GARAGE CREATION
                            $this->Garage->edit_creation_date($garage_creation_date, $rep_create['Garage']['id']);
                        }
                    }
                }
                rename($path_garages_file, $path_garages_file_processed);
                CakeLog::write('updates-france', '-- End of ' . $file . ' file --' . PHP_EOL);
            } else {
                CakeLog::write('updates-france', $file . ' file does not exist or cannot be read' . PHP_EOL);
                CakeLog::write('updates-france', '-- End of ' . $file . ' file --' . PHP_EOL);
            }
        }

        if (!$files) {
            CakeLog::write('updates-france', 'No files to process' . PHP_EOL);
        }
    }

    public function update_suppliersFR()
    {
        set_time_limit(18000);
        ini_set('memory_limit', '-1');

        $update_files_directory = GNMAAG_DIRECTORY_UPDATE_SUPPLIERS; // core.php
        $files = glob($update_files_directory . "frns_ISA_to_MY*.json");

        CakeLog::config('updates-france', array(
            'engine' => 'FileLog',
            'types' => array('info', 'error', 'warning'),
            'scopes' => array('updates-france'),
            'file' => 'updates-france.log',
        ));

        foreach ($files as $path_dis_file) {
            $explode_tmp = explode(DS, $path_dis_file);
            $file = end($explode_tmp);
            $explode2_tmp = explode('/', $file);
            $file_processed = end($explode2_tmp);
            $path_dis_file_processed = GNMAAG_DIRECTORY_PROCESSED . $file_processed . "_" . date('Y-m-d-His') . ".json";

            CakeLog::write('updates-france', PHP_EOL);
            CakeLog::write('updates-france', '-- Reading the ' . $file . ' file --' . PHP_EOL);

            if (file_exists($path_dis_file)) {
                $json = file_get_contents($path_dis_file);
                $contents = utf8_encode($json);
                $suppliers = json_decode($contents, true);

                $this->Supplier = ClassRegistry::init('Supplier');
                $this->Brand = ClassRegistry::init('Brand');
                $this->Product = ClassRegistry::init('Product');

                if (!$suppliers) {
                    CakeLog::write('updates-france', $file . ' is empty' . PHP_EOL);
                    CakeLog::write('updates-france', '-- End of ' . $file . ' file --' . PHP_EOL);
                } else {
                    foreach ($suppliers['fournisseur'] as $key => $supplier_json) {
                        $exist_supplier = $this->Supplier->findById($supplier_json['id_isa']);

                        if ($exist_supplier) {
                            CakeLog::write('updates-france', 'Update Supplier with Id ISA: ' . $supplier_json['id_isa'] . PHP_EOL);
                            //Update Supplier
                            $supplier_update = $this->Supplier->updateSupplierFR($supplier_json, $exist_supplier);
                            //Documents
                            if ($supplier_update && isset($supplier_json['documents'])) {
                                $this->Supplier->updateDocumentsFR($supplier_update['Supplier']['id'], $supplier_json['documents']);
                            }
                            //Brand
                            if ($supplier_update) {
                                $brand_update = $this->Brand->updateBrandFR($supplier_json, $supplier_update);
                            }
                            //Products
                            if (
                                isset($brand_update) &&
                                isset($supplier_json['divers']) &&
                                isset($supplier_json['divers']['produits']) &&
                                $supplier_json['divers']['produits']
                            ) {
                                $this->Product->updateProductFR($supplier_json['divers']['produits'], $brand_update);
                            }
                        } else {
                            CakeLog::write('updates-france', 'Create Supplier with Id ISA: ' . $supplier_json['id_isa'] . PHP_EOL);
                            //Create Supplier
                            $supplier_create = $this->Supplier->createSupplierFR($supplier_json);
                            //Documents
                            if ($supplier_create && isset($supplier_json['documents'])) {
                                $this->Supplier->createDocumentsFR($supplier_create['Supplier']['id'], $supplier_json['documents']);
                            }
                            //Brand
                            if ($supplier_create) {
                                $brand_create = $this->Brand->createBrandFR($supplier_json, $supplier_create);
                            }
                            //Products
                            if (
                                isset($brand_create) &&
                                isset($supplier_json['divers']) &&
                                isset($supplier_json['divers']['produits']) &&
                                $supplier_json['divers']['produits']
                            ) {
                                $this->Product->createProductFR($supplier_json['divers']['produits'], $brand_create);
                            }
                        }
                    }
                }
                rename($path_dis_file, $path_dis_file_processed);
                CakeLog::write('updates-france', '-- End of ' . $file . ' file --' . PHP_EOL);
            } else {
                CakeLog::write('updates-france', $file . ' file does not exist or cannot be read' . PHP_EOL);
                CakeLog::write('updates-france', '-- End of ' . $file . ' file --' . PHP_EOL);
            }
        }

        if (!$files) {
            CakeLog::write('updates-france', 'No files to process' . PHP_EOL);
        }
    }
}
