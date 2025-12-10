<?php
App::import('Vendor', 'PHPExcel', array('file' => 'phpexcel' . DS . 'PHPExcel.php'));

class ActualizarFTPGarage extends AppModel
{
    public $useTable = false;

    /**
     * Garage CSV export.
     */
    public function loadGarageCsv()
    {
        ini_set('memory_limit', '-1');
        $garage_file = $this->createData();
        $this->generateCsv($garage_file);
    }

    /**
     * Delete file if it exists.
     */
    private function fileExistsLocal()
    {
        $filename = date("N") . ConstantesFtp::GARAGE_NAME;
        $path = WWW_ROOT . ConstantesFtp::CSV_GARAGE_PATH . $filename . '.csv';

        if (file_exists($path)) {
            if (unlink($path)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Get data for Garage CSV export.
     */
    private function createData()
    {
        $language_code = 'en';
        $this->Garage = ClassRegistry::init('Garage');
        if (!$this->fileExistsLocal()) {
            $status = Configure::read('Network_Status');
            $conditionStatus = '';
            foreach ($status as $statusKey => $statusValue) {
                $conditionStatus .= "WHEN GarageNetwork.status = $statusKey THEN '" . __t($statusValue, $language_code) . "' ";
            }
            return $this->Garage->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.garage_id = Garage.id',
                            ),
                            'fields' => array(
                                'GarageNetwork.id',
                                'GarageNetwork.garage_id',
                                'GarageNetwork.network_id',
                                'GarageNetwork.annex_detail_id',
                                'GarageNetwork.status',
                                'GarageNetwork.contract_start_date',
                            ),
                        ),
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Network.id = GarageNetwork.network_id',
                            ),
                            'fields' => array(
                                'Network.id',
                                'Network.name',
                            ),
                        ),
                        array(
                            'alias' => 'AnnexDetail',
                            'table' => 'annex_details',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'AnnexDetail.id = GarageNetwork.annex_detail_id',
                            ),
                        ),
                        array(
                            'alias' => 'GarageDistributor',
                            'table' => 'garages_distributors',
                            'type' => 'INNER',
                            'conditions' => array(
                                'GarageDistributor.garage_id = Garage.id',
                                'GarageDistributor.id = (
                                    SELECT MIN(gd1.id)
                                    FROM garages_distributors AS gd1
                                    WHERE gd1.garage_id = Garage.id
                                )',
                            ),
                            'fields' => array(
                                'GarageDistributor.id',
                                'GarageDistributor.garage_id',
                                'GarageDistributor.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Distributor.id = GarageDistributor.distributor_id',
                            ),
                            'fields' => array(
                                'Distributor.id',
                                'Distributor.name',
                                'Distributor.account_number',
                            ),
                        ),
                        array(
                            'alias' => 'GarageContactsStaff',
                            'table' => 'garages_contacts_staff',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageContactsStaff.garage_id = Garage.id',
                                'GarageContactsStaff.priority' => 1,
                            ),
                            'fields' => array(
                                'GarageContactsStaff.id',
                                'GarageContactsStaff.garage_id',
                                'GarageContactsStaff.priority',
                                'GarageContactsStaff.contact_id',
                            ),
                        ),
                        array(
                            'alias' => 'Contact',
                            'table' => 'contacts',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Contact.id = GarageContactsStaff.contact_id',
                            ),
                            'fields' => array(
                                'Contact.id',
                                'Contact.first_name',
                                'Contact.last_name',
                            ),
                        ),
                    ),
                    'fields' => array(
                        'Garage.name',
                        'Garage.g_number_id',
                        'Garage.address1',
                        'Garage.address2',
                        'Garage.address3',
                        'Garage.address4',
                        'Garage.town',
                        'Garage.postcode',
                        'Garage.phone',
                        'Garage.mobile',
                        'Garage.email',
                        'Garage.status',
                        'Garage.creation_date',
                        'GarageNetwork.status',
                        'GarageNetwork.contract_start_date',
                        'AnnexDetail.*',
                        'GROUP_CONCAT(DISTINCT CONCAT(Network.name, " - ", IFNULL(AnnexDetail.name_' . __l() . ', ""), "(",
                            CASE
                                ' . $conditionStatus . '
                                ELSE IFNULL(GarageNetwork.status, "--")
                            END
                            , ")") SEPARATOR ", ") AS network_concatenated_fields',
                        'GROUP_CONCAT(CONCAT(COALESCE(DATE_FORMAT(GarageNetwork.contract_start_date, "%d/%m/%Y"), "--")) SEPARATOR ", ") AS garages_networks_start_date',
                        'Network.name',
                        'Distributor.name',
                        'Distributor.account_number',
                        'Contact.first_name',
                        'Contact.last_name',
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'order' => array('Garage.name' => 'asc'),
                    'group' => array('Garage.id'),
                )
            );
        }
    }

    /**
     * Generate Garage CSV.
     */
    private function generateCsv($data)
    {
        $language_code = 'en';
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $filename = date("N") . ConstantesFtp::GARAGE_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_GARAGE_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            __t('CSV.Crm_id', $language_code),
            __t('CSV.Garage_name', $language_code),
            __t('CSV.Garage_number', $language_code),
            __t('CSV.Address_line_1', $language_code),
            __t('CSV.Address_line_2', $language_code),
            __t('CSV.Address_line_3', $language_code),
            __t('CSV.Address_line_4', $language_code),
            __t('CSV.Town_id', $language_code),
            __t('CSV.Address_postalcode', $language_code),
            __t('CSV.Network_type', $language_code),
            __t('CSV.Phone', $language_code),
            __t('CSV.Mobile_phone', $language_code),
            __t('CSV.Email', $language_code),
            __t('CSV.Bureau_account_name', $language_code),
            __t('CSV.Member_name', $language_code),
            __t('CSV.Member_id', $language_code),
            __t('CSV.Primary_contact', $language_code),
            __t('CSV.Fax', $language_code),
            __t('CSV.Contract_start_date', $language_code),
            __t('CSV.Entity_creation_date', $language_code),
            __t('CSV.Status', $language_code),
        );
        fputcsv($file, $table);
        $status = Configure::read('Garage_Status');
        foreach ($data as $garage) {
            $fila = array(
                '',
                $garage['Garage']['name'],
                $garage['Garage']['g_number_id'],
                $garage['Garage']['address1'],
                $garage['Garage']['address2'],
                $garage['Garage']['address3'],
                $garage['Garage']['address4'],
                $garage['Garage']['town'],
                $garage['Garage']['postcode'],
                $garage[0]['network_concatenated_fields'],
                $garage['Garage']['phone'],
                $garage['Garage']['mobile'],
                $garage['Garage']['email'],
                '',
                $garage['Distributor']['name'],
                $garage['Distributor']['account_number'],
                $garage['Contact']['first_name'] . ' ' . $garage['Contact']['last_name'],
                '',
                (!is_null($garage[0]['garages_networks_start_date'])) ? $garage[0]['garages_networks_start_date'] : '--',
                Fecha::toFormatoVistaFechaHora($garage['Garage']['creation_date']),
                __t($status[$garage['Garage']['status']], $language_code),
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    /**
     * Send Garage CSV export to SFTP.
     */
    public function sendSftp()
    {
        try {
            // Include the SFTP class from phpseclib
            set_include_path(WWW_ROOT . '../Vendor/phpseclib');
            require_once WWW_ROOT . '../Vendor/phpseclib/Net/SFTP.php';

            $remoteFilename = date("Y-m-d", time()) . ConstantesFtp::GARAGE_NAME;
            $localFilename = date("N") . ConstantesFtp::GARAGE_NAME;
            $pathFilename = WWW_ROOT . ConstantesFtp::CSV_GARAGE_PATH . $localFilename . '.csv';
            $sftpParams = Configure::read('autopart.FTP');

            $sftp = new Net_SFTP($sftpParams['Host'], $sftpParams['Port']);
            if (!$sftp->login(SFTP_USERNAME, Texto::encryptDecryptText(SFTP_PASSWORD, false))) {
                CakeLog::debug(print_r("SFTP - Export - Login failed", true));
                return false;
            }
            $remoteFile = $remoteFilename . '.csv';
            $ftpFilesDirectory = SFTP_REMOTE;
            if ($sftp->chdir($ftpFilesDirectory)) {
                if ($sftp->file_exists($remoteFile) && !$sftp->delete($remoteFile)) {
                    CakeLog::debug(print_r("SFTP - Export - Failed to delete the file", true));
                }
            } else {
                CakeLog::debug(print_r("SFTP - Export - Failed to change directory", true));
            }

            if ($sftp->put(SFTP_REMOTE . '/' . $remoteFile, $pathFilename, NET_SFTP_LOCAL_FILE)) {
                return true;
            } else {
                CakeLog::debug(print_r("SFTP - Export - Upload failed", true));
                return false;
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("SFTP - Export DB - An exception has ocurred " . $e->getMessage(), true));
        }
    }

    /**
     * Database ZIP CSV export.
     */
    public function loadDatabaseCsv()
    {
        ini_set('memory_limit', '-1');
        $this->generateCsvDb();
    }

    /**
     * Delete file if it exists.
     */
    private function fileExistsLocalDb($const_table)
    {
        $filename = date("N") . $const_table;
        $path = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';

        if (file_exists($path)) {
            if (unlink($path)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Generate database CSVs.
     */
    private function generateCsvDb()
    {
        $this->agreements_csv();
        $this->annex_details_csv();
        $this->appointments_csv();
        $this->associations_types_csv();
        $this->associations_csv();
        $this->billings_schedules_csv();
        $this->bookings_csv();
        $this->cities_csv();
        $this->contacts_csv();
        $this->contacts_titles_csv();
        $this->countries_csv();
        $this->courses_types_csv();
        $this->courtesy_car_types_csv();
        $this->customers_activities_csv();
        $this->distributors_csv();
        $this->distributors_activities_csv();
        $this->distributors_activities_primary_csv();
        $this->distributors_comments_csv();
        $this->distributors_contacts_bdm_csv();
        $this->distributors_contacts_general_branch_manager_csv();
        $this->distributors_contacts_staff_csv();
        $this->distributors_contracts_csv();
        $this->distributors_customer_activities_csv();
        $this->distributors_customer_activities_workshops_csv();
        $this->distributors_distributors_activities_csv();
        $this->distributors_distributors_networks_csv();
        $this->distributors_figures_csv();
        $this->distributors_figures_details_csv();
        $this->distributors_images_csv();
        $this->distributors_kpis_csv();
        $this->distributors_labels_csv();
        $this->distributors_networks_csv();
        $this->distributors_networks_contacts_bdm_csv();
        $this->distributors_objectives_csv();
        $this->distributors_routes_csv();
        $this->distributors_services_csv();
        $this->distributors_software_csv();
        $this->distributors_types_csv();
        $this->employee_types_csv();
        $this->enquiries_csv();
        $this->equipments_csv();
        $this->equipments_types_csv();
        $this->facilities_csv();
        $this->fluids_csv();
        $this->garages_csv();
        $this->garages_agreements_csv();
        $this->garages_b2b_postcodes_csv();
        $this->garages_b2c_postcodes_csv();
        $this->garages_brands_csv();
        $this->garages_campaign_csv();
        $this->garages_comments_csv();
        $this->garages_contacts_bdm_csv();
        $this->garages_contacts_general_branch_manager_csv();
        $this->garages_contacts_lists_csv();
        $this->garages_contacts_staff_csv();
        $this->garages_courtesy_car_types_csv();
        $this->garages_customers_activities_csv();
        $this->garages_distributors_csv();
        $this->garages_distributors_shortcuts_csv();
        $this->garages_employees_csv();
        $this->garages_equipments_csv();
        $this->garages_facilities_csv();
        $this->garages_figures_csv();
        $this->garages_figures_details_csv();
        $this->garages_files_csv();
        $this->garages_images_csv();
        $this->garages_kpis_csv();
        $this->garages_networks_csv();
        $this->garages_networks_contacts_csv();
        $this->garages_networks_fluids_csv();
        $this->garages_networks_genarts_csv();
        $this->garages_networks_genarts_families_csv();
        $this->garages_networks_genarts_master_csv();
        $this->garages_networks_images_csv();
        $this->garages_networks_services_csv();
        $this->garages_networks_services_drivers_csv();
        $this->garages_networks_vehicles_csv();
        $this->garages_networks_vehicles_black_list_csv();
        $this->garages_networks_vehicle_types_csv();
        $this->garages_networks_works_csv();
        $this->garages_networks_works_labours_csv();
        $this->garages_networks_works_prices_csv();
        $this->garages_oils_csv();
        $this->garages_products_csv();
        $this->garages_routes_csv();
        $this->garages_services_csv();
        $this->garages_software_csv();
        $this->garages_specialist_makes_csv();
        $this->garages_statuses_csv();
        $this->garages_values_adds_csv();
        $this->garages_value_add_supplier_csv();
        $this->garages_vehicles_csv();
        $this->garages_vehicle_types_csv();
        $this->garages_visit_frequencies_csv();
        $this->garages_websites_csv();
        $this->garages_workshop_activities_csv();
        $this->genarts_csv();
        $this->genarts_families_csv();
        $this->genarts_master_csv();
        $this->grouping_genarts_csv();
        $this->hold_reason_types_csv();
        $this->leaving_reason_types_csv();
        $this->lists_csv();
        $this->networks_csv();
        $this->networks_contacts_bdm_csv();
        $this->networks_contacts_lists_csv();
        $this->networks_contract_types_csv();
        $this->networks_statuses_csv();
        $this->orders_csv();
        $this->order_products_csv();
        $this->order_types_csv();
        $this->positions_csv();
        $this->postcodes_csv();
        $this->postcode_provinces_csv();
        $this->provinces_csv();
        $this->reasons_delegates_csv();
        $this->regions_csv();
        $this->roles_csv();
        $this->services_csv();
        $this->services_drivers_csv();
        $this->services_types_csv();
        $this->software_csv();
        $this->software_manufactures_csv();
        $this->software_types_csv();
        $this->tasks_csv();
        $this->trading_groups_csv();
        $this->trading_groups_distributors_networks_csv();
        $this->trading_groups_networks_csv();
        $this->trainings_courses_csv();
        $this->trainings_credits_csv();
        $this->trainings_credits_networks_csv();
        $this->trainings_delegates_csv();
        $this->trainings_planned_courses_csv();
        $this->trainings_providers_csv();
        $this->trainings_trainers_csv();
        $this->users_csv();
        $this->values_adds_csv();
        $this->value_add_suppliers_csv();
        $this->value_add_supplier_type_csv();
        $this->vehicle_types_csv();
        $this->venues_csv();
        $this->works_csv();
        $this->workshop_activities_csv();
    }

    private function agreements_create_data()
    {
        $this->Agreement = ClassRegistry::init('Agreement');
        if (!$this->fileExistsLocalDb(ConstantesFtp::AGREEMENT_NAME)) {
            return $this->Agreement->find(
                'all',
                array(
                    'conditions' => array(
                        'Agreement.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Agreement.*',
                    ),
                )
            );
        }
    }

    private function annex_details_create_data()
    {
        $this->AnnexDetail = ClassRegistry::init('AnnexDetail');
        if (!$this->fileExistsLocalDb(ConstantesFtp::ANNEX_DETAILS_NAME)) {
            return $this->AnnexDetail->find(
                'all',
                array(
                    'conditions' => array(
                        'AnnexDetail.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'AnnexDetail.*',
                    ),
                )
            );
        }
    }

    private function associations_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::ASSOCIATIONS_NAME)) {
        }
    }

    private function associations_types_create_data()
    {
        $this->AssociationType = ClassRegistry::init('AssociationType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::ASSOCIATIONS_TYPES_NAME)) {
            return $this->AssociationType->find(
                'all',
                array(
                    'conditions' => array(
                        'AssociationType.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'AssociationType.*',
                    ),
                )
            );
        }
    }

    private function appointments_data()
    {
        $appointments = ClassRegistry::init('Appointment');
        if (!$this->fileExistsLocalDb(ConstantesFtp::APPOINTMENTS_NAME)) {
            return $appointments->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garages',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garages.id = Appointment.garage_id',
                            ),
                        ),
                        array(
                            'alias' => 'Distributors',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributors.id = Appointment.distributor_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'OR' => array(
                            'Garages.aag_region_id' => ConstantsAAGRegionId::UK,
                            'Distributors.aag_region_id' => ConstantsAAGRegionId::UK,
                        )
                    ),
                    'fields' => array(
                        'Appointment.*',
                    ),
                )
            );
        }
    }

    private function billings_schedules_create_data()
    {
        $this->BillingSchedule = ClassRegistry::init('BillingSchedule');
        if (!$this->fileExistsLocalDb(ConstantesFtp::BILLINGS_SCHEDULES_NAME)) {
            return $this->BillingSchedule->find(
                'all',
                array(
                    'conditions' => array(
                        'BillingSchedule.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'BillingSchedule.*',
                    ),
                )
            );
        }
    }

    private function bookings_create_data()
    {
        $this->Booking = ClassRegistry::init('Booking');
        if (!$this->fileExistsLocalDb(ConstantesFtp::BOOKINGS_NAME)) {
            return $this->Booking->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = Booking.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                            'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Booking.*',
                    ),
                )
            );
        }
    }

    private function cities_create_data()
    {
        $this->City = ClassRegistry::init('City');
        if (!$this->fileExistsLocalDb(ConstantesFtp::CITIES_NAME)) {
            return $this->City->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Province',
                            'table' => 'provinces',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Province.id = City.province_id',
                            ),
                        ),
                        array(
                            'alias' => 'Country',
                            'table' => 'countries',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Country.id = Province.country_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Country.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'City.*',
                    ),
                )
            );
        }
    }

    private function contacts_create_data()
    {
        $this->Contact = ClassRegistry::init('Contact');
        if (!$this->fileExistsLocalDb(ConstantesFtp::CONTACTS_NAME)) {
            return $this->Contact->find(
                'all',
                array(
                    'conditions' => array(
                        'Contact.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Contact.*',
                    ),
                )
            );
        }
    }

    private function contacts_titles_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::CONTACTS_TITLES_NAME)) {
        }
    }

    private function countries_create_data()
    {
        $this->Country = ClassRegistry::init('Country');
        if (!$this->fileExistsLocalDb(ConstantesFtp::COUNTRIES_NAME)) {
            return $this->Country->find(
                'all',
                array(
                    'conditions' => array(
                        'Country.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Country.*',
                    ),
                )
            );
        }
    }

    private function courses_types_create_data()
    {
        $this->CourseType = ClassRegistry::init('CourseType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::COURSES_TYPES_NAME)) {
            return $this->CourseType->find(
                'all',
                array(
                    'conditions' => array(
                        'CourseType.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'CourseType.*',
                    ),
                )
            );
        }
    }

    private function courtesy_car_types_create_data()
    {
        $this->CourtesyCarType = ClassRegistry::init('CourtesyCarType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::COURTESY_CAR_TYPES_NAME)) {
            return $this->CourtesyCarType->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageCourtesyCarType',
                            'table' => 'garages_courtesy_car_types',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageCourtesyCarType.courtesy_car_type_id = CourtesyCarType.id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageCourtesyCarType.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'CourtesyCarType.*',
                    ),
                )
            );
        }
    }

    private function customers_activities_create_data()
    {
        $this->CustomerActivity = ClassRegistry::init('CustomerActivity');
        if (!$this->fileExistsLocalDb(ConstantesFtp::CUSTOMERS_ACTIVITIES_NAME)) {
            return $this->CustomerActivity->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageCustomerActivity',
                            'table' => 'garages_customers_activities',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageCustomerActivity.customer_activity_id = CustomerActivity.id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageCustomerActivity.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'CustomerActivity.*',
                    ),
                )
            );
        }
    }

    private function distributors_create_data()
    {
        $this->Distributor = ClassRegistry::init('Distributor');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_NAME)) {
            return $this->Distributor->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Distributor.*',
                    ),
                )
            );
        }
    }

    private function distributors_activities_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_ACTIVITIES_NAME)) {
        }
    }

    private function distributors_activities_primary_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_ACTIVITIES_PRIMARY_NAME)) {
        }
    }

    private function distributors_comments_create_data()
    {
        $this->DistributorComment = ClassRegistry::init('DistributorComment');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_COMMENTS_NAME)) {
            return $this->DistributorComment->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorComment.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorComment.*',
                    ),
                )
            );
        }
    }

    private function distributors_contacts_bdm_create_data()
    {
        $this->DistributorContactBdm = ClassRegistry::init('DistributorContactBdm');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_CONTACTS_BDM_NAME)) {
            return $this->DistributorContactBdm->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorContactBdm.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorContactBdm.*',
                    ),
                )
            );
        }
    }

    private function distributors_contacts_general_branch_manager_create_data()
    {
        $this->DistributorContactGeneralBranchManager = ClassRegistry::init('DistributorContactGeneralBranchManager');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_CONTACTS_GENERAL_BRANCH_MANAGER_NAME)) {
            return $this->DistributorContactGeneralBranchManager->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorContactGeneralBranchManager.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorContactGeneralBranchManager.*',
                    ),
                )
            );
        }
    }

    private function distributors_contacts_staff_create_data()
    {
        $this->DistributorContactStaff = ClassRegistry::init('DistributorContactStaff');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_CONTACTS_STAFF_NAME)) {
            return $this->DistributorContactStaff->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorContactStaff.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorContactStaff.*',
                    ),
                )
            );
        }
    }

    private function distributors_contracts_create_data()
    {
        $this->DistributorContract = ClassRegistry::init('DistributorContract');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_CONTRACTS_NAME)) {
            return $this->DistributorContract->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = DistributorContract.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorContract.*',
                    ),
                )
            );
        }
    }

    private function distributors_customer_activities_create_data()
    {
        $this->DistributorCustomerActivity = ClassRegistry::init('DistributorCustomerActivity');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_CUSTOMER_ACTIVITIES_NAME)) {
            return $this->DistributorCustomerActivity->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorCustomerActivity.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorCustomerActivity.*',
                    ),
                )
            );
        }
    }

    private function distributors_customer_activities_workshops_create_data()
    {
        $this->DistributorCustomerActivityWorkshop = ClassRegistry::init('DistributorCustomerActivityWorkshop');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_CUSTOMER_ACTIVITIES_WORKSHOPS_NAME)) {
            return $this->DistributorCustomerActivityWorkshop->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'DistributorCustomerActivity',
                            'table' => 'distributors_customer_activities',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'DistributorCustomerActivity.id = DistributorCustomerActivityWorkshop.distributor_customer_activity_id',
                            ),
                        ),
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorCustomerActivity.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorCustomerActivityWorkshop.*',
                    ),
                )
            );
        }
    }

    private function distributors_distributors_activities_create_data()
    {
        $this->DistributorDistributorActivity = ClassRegistry::init('DistributorDistributorActivity');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_DISTRIBUTORS_ACTIVITIES_NAME)) {
            return $this->DistributorDistributorActivity->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorDistributorActivity.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorDistributorActivity.*',
                    ),
                )
            );
        }
    }

    private function distributors_distributors_networks_create_data()
    {
        $this->DistributorDistributorNetwork = ClassRegistry::init('DistributorDistributorNetwork');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_DISTRIBUTORS_NETWORKS_NAME)) {
            return $this->DistributorDistributorNetwork->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorDistributorNetwork.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorDistributorNetwork.*',
                    ),
                )
            );
        }
    }

    private function distributors_figures_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_FIGURES_NAME)) {
        }
    }

    private function distributors_figures_details_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_FIGURES_DETAILS_NAME)) {
        }
    }

    private function distributors_images_create_data()
    {
        $this->DistributorImage = ClassRegistry::init('DistributorImage');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_IMAGES_NAME)) {
            return $this->DistributorImage->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorImage.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorImage.*',
                    ),
                )
            );
        }
    }

    private function distributors_kpis_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_KPIS_NAME)) {
        }
    }

    private function distributors_labels_create_data()
    {
        $this->DistributorLabel = ClassRegistry::init('DistributorLabel');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_LABELS_NAME)) {
            return $this->DistributorLabel->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorLabel.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorLabel.*',
                    ),
                )
            );
        }
    }

    private function distributors_networks_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_NETWORKS_NAME)) {
        }
    }

    private function distributors_networks_contacts_bdm_create_data()
    {
        $this->DistributorNetworkContactBdm = ClassRegistry::init('DistributorNetworkContactBdm');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_NETWORKS_CONTACTS_BDM_NAME)) {
            return $this->DistributorNetworkContactBdm->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = DistributorNetworkContactBdm.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorNetworkContactBdm.*',
                    ),
                )
            );
        }
    }

    private function distributors_objectives_create_data()
    {
        $this->DistributorObjective = ClassRegistry::init('DistributorObjective');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_OBJECTIVES_NAME)) {
            return $this->DistributorObjective->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorObjective.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorObjective.*',
                    ),
                )
            );
        }
    }

    private function distributors_routes_create_data()
    {
        $this->DistributorRoute = ClassRegistry::init('DistributorRoute');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_ROUTES_NAME)) {
            return $this->DistributorRoute->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorRoute.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorRoute.*',
                    ),
                )
            );
        }
    }

    private function distributors_services_create_data()
    {
        $this->DistributorService = ClassRegistry::init('DistributorService');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_SERVICES_NAME)) {
            return $this->DistributorService->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorService.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorService.*',
                    ),
                )
            );
        }
    }

    private function distributors_software_create_data()
    {
        $this->DistributorSoftware = ClassRegistry::init('DistributorSoftware');
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_SOFTWARE_NAME)) {
            return $this->DistributorSoftware->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Distributor',
                            'table' => 'distributors',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Distributor.id = DistributorSoftware.distributor_id',
                            ),
                        ),
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = Distributor.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'DistributorSoftware.*',
                    ),
                )
            );
        }
    }

    private function distributors_types_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::DISTRIBUTORS_TYPES_NAME)) {
        }
    }

    private function employee_types_create_data()
    {
        $this->EmployeeType = ClassRegistry::init('EmployeeType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::EMPLOYEE_TYPES_NAME)) {
            return $this->EmployeeType->find(
                'all',
                array(
                    'conditions' => array(
                        'EmployeeType.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'EmployeeType.*',
                    ),
                )
            );
        }
    }

    private function enquiries_create_data()
    {
        $this->Enquiry = ClassRegistry::init('Enquiry');
        if (!$this->fileExistsLocalDb(ConstantesFtp::ENQUIRIES_NAME)) {
            return $this->Enquiry->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = Enquiry.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Enquiry.*',
                    ),
                )
            );
        }
    }

    private function equipments_create_data()
    {
        $this->Equipment = ClassRegistry::init('Equipment');
        if (!$this->fileExistsLocalDb(ConstantesFtp::EQUIPMENTS_NAME)) {
            return $this->Equipment->find(
                'all',
                array(
                    'conditions' => array(
                        'Equipment.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Equipment.*',
                    ),
                )
            );
        }
    }

    private function equipments_types_create_data()
    {
        $this->EquipmentType = ClassRegistry::init('EquipmentType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::EQUIPMENTS_TYPES_NAME)) {
            return $this->EquipmentType->find(
                'all',
                array(
                    'conditions' => array(
                        'EquipmentType.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'EquipmentType.*',
                    ),
                )
            );
        }
    }

    private function facilities_create_data()
    {
        $this->Facility = ClassRegistry::init('Facility');
        if (!$this->fileExistsLocalDb(ConstantesFtp::FACILITIES_NAME)) {
            return $this->Facility->find(
                'all',
                array(
                    'conditions' => array(
                        'Facility.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Facility.*',
                    ),
                )
            );
        }
    }

    private function fluids_create_data()
    {
        $this->Fluid = ClassRegistry::init('Fluid');
        if (!$this->fileExistsLocalDb(ConstantesFtp::FLUID_NAME)) {
            return $this->Fluid->getAllByAagRegionId(ConstantsAAGRegionId::UK);
        }
    }

    private function garages_create_data()
    {
        $this->Garage = ClassRegistry::init('Garage');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NAME)) {
            return $this->Garage->find(
                'all',
                array(
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Garage.*',
                    ),
                )
            );
        }
    }

    private function garages_agreements_create_data()
    {
        $this->GarageAgreement = ClassRegistry::init('GarageAgreement');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_AGREEMENTS_NAME)) {
            return $this->GarageAgreement->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageAgreement.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageAgreement.*',
                    ),
                )
            );
        }
    }

    private function garages_b2b_postcodes_create_data()
    {
        $this->GarageB2bPostcode = ClassRegistry::init('GarageB2bPostcode');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_B2B_POSTCODES_NAME)) {
            return $this->GarageB2bPostcode->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageB2bPostcode.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageB2bPostcode.*',
                    ),
                )
            );
        }
    }

    private function garages_b2c_postcodes_create_data()
    {
        $this->GarageB2cPostcode = ClassRegistry::init('GarageB2cPostcode');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_B2C_POSTCODES_NAME)) {
            return $this->GarageB2cPostcode->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageB2cPostcode.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageB2cPostcode.*',
                    ),
                )
            );
        }
    }

    private function garages_brands_create_data()
    {
        $this->GarageBrand = ClassRegistry::init('GarageBrand');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_BRANDS_NAME)) {
            return $this->GarageBrand->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageBrand.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageBrand.*',
                    ),
                )
            );
        }
    }

    private function garages_campaign_create_data()
    {
        $this->GarageCampaign = ClassRegistry::init('GarageCampaign');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_CAMPAIGN_NAME)) {
            return $this->GarageCampaign->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageCampaign.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageCampaign.*',
                    ),
                )
            );
        }
    }

    private function garages_comments_create_data()
    {
        $this->GarageComment = ClassRegistry::init('GarageComment');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_COMMENTS_NAME)) {
            return $this->GarageComment->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageComment.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageComment.*',
                    ),
                )
            );
        }
    }

    private function garages_contacts_bdm_create_data()
    {
        $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_CONTACTS_BDM_NAME)) {
            return $this->GarageContactBdm->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageContactBdm.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageContactBdm.*',
                    ),
                )
            );
        }
    }

    private function garages_contacts_general_branch_manager_create_data()
    {
        $this->GarageContactGeneralBranchManager = ClassRegistry::init('GarageContactGeneralBranchManager');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_CONTACTS_GENERAL_BRANCH_MANAGER_NAME)) {
            return $this->GarageContactGeneralBranchManager->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageContactGeneralBranchManager.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageContactGeneralBranchManager.*',
                    ),
                )
            );
        }
    }

    private function garages_contacts_lists_create_data()
    {
        $this->GarageContactList = ClassRegistry::init('GarageContactList');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_CONTACTS_LISTS_NAME)) {
            return $this->GarageContactList->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageContactList.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageContactList.*',
                    ),
                )
            );
        }
    }

    private function garages_contacts_staff_create_data()
    {
        $this->GarageContactStaff = ClassRegistry::init('GarageContactStaff');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_CONTACTS_STAFF_NAME)) {
            return $this->GarageContactStaff->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageContactStaff.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageContactStaff.*',
                    ),
                )
            );
        }
    }

    private function garages_courtesy_car_types_create_data()
    {
        $this->GarageCourtesyCarType = ClassRegistry::init('GarageCourtesyCarType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_COURTESY_CAR_TYPES_NAME)) {
            return $this->GarageCourtesyCarType->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageCourtesyCarType.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageCourtesyCarType.*',
                    ),
                )
            );
        }
    }

    private function garages_customers_activities_create_data()
    {
        $this->GarageCustomerActivity = ClassRegistry::init('GarageCustomerActivity');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_CUSTOMERS_ACTIVITIES_NAME)) {
            return $this->GarageCustomerActivity->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageCustomerActivity.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageCustomerActivity.*',
                    ),
                )
            );
        }
    }

    private function garages_distributors_create_data()
    {
        $this->GarageDistributor = ClassRegistry::init('GarageDistributor');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_DISTRIBUTORS_NAME)) {
            return $this->GarageDistributor->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageDistributor.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageDistributor.*',
                    ),
                )
            );
        }
    }

    private function garages_distributors_shortcuts_create_data()
    {
        $this->GarageDistributorShortcut = ClassRegistry::init('GarageDistributorShortcut');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_DISTRIBUTORS_SHORTCUTS_NAME)) {
            return $this->GarageDistributorShortcut->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageDistributorShortcut.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageDistributorShortcut.*',
                    ),
                )
            );
        }
    }

    private function garages_employees_create_data()
    {
        $this->GarageEmployee = ClassRegistry::init('GarageEmployee');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_EMPLOYEES_NAME)) {
            return $this->GarageEmployee->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageEmployee.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageEmployee.*',
                    ),
                )
            );
        }
    }

    private function garages_equipments_create_data()
    {
        $this->GarageEquipment = ClassRegistry::init('GarageEquipment');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_EQUIPMENTS_NAME)) {
            return $this->GarageEquipment->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageEquipment.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageEquipment.*',
                    ),
                )
            );
        }
    }

    private function garages_facilities_create_data()
    {
        $this->GarageFacility = ClassRegistry::init('GarageFacility');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_FACILITIES_NAME)) {
            return $this->GarageFacility->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageFacility.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageFacility.*',
                    ),
                )
            );
        }
    }

    private function garages_figures_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_FIGURES_NAME)) {
        }
    }

    private function garages_figures_details_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_FIGURES_DETAILS_NAME)) {
        }
    }

    private function garages_files_create_data()
    {
        $this->GarageFile = ClassRegistry::init('GarageFile');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_FILES_NAME)) {
            return $this->GarageFile->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageFile.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageFile.*',
                    ),
                )
            );
        }
    }

    private function garages_images_create_data()
    {
        $this->GarageImage = ClassRegistry::init('GarageImage');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_IMAGES_NAME)) {
            return $this->GarageImage->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageImage.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageImage.*',
                    ),
                )
            );
        }
    }

    private function garages_kpis_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_KPIS_NAME)) {
        }
    }

    private function garages_networks_create_data()
    {
        $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_NAME)) {
            return $this->GarageNetwork->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetwork.*',
                    ),
                )
            );
        }
    }

    private function garages_networks_contacts_create_data()
    {
        $this->GarageNetworkContact = ClassRegistry::init('GarageNetworkContact');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_CONTACTS_NAME)) {
            return $this->GarageNetworkContact->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = GarageNetworkContact.garage_network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetworkContact.*',
                    ),
                )
            );
        }
    }

    private function garages_networks_fluids_create_data()
    {
        $this->GarageNetworkFluid = ClassRegistry::init('GarageNetworkFluid');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_FLUIDS_NAME)) {
            return $this->GarageNetworkFluid->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = GarageNetworkFluid.garage_network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetworkFluid.*',
                    ),
                )
            );
        }
    }

    private function garages_networks_genarts_create_data()
    {
        $this->GarageNetworkGenart = ClassRegistry::init('GarageNetworkGenart');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_GENARTS_NAME)) {
            return $this->GarageNetworkGenart->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = GarageNetworkGenart.garage_network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetworkGenart.*',
                    ),
                )
            );
        }
    }

    private function garages_networks_genarts_families_create_data()
    {
        $this->GarageNetworkGenartFamily = ClassRegistry::init('GarageNetworkGenartFamily');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_GENARTS_FAMILIES_NAME)) {
            return $this->GarageNetworkGenartFamily->getAllByAagRegionId(ConstantsAAGRegionId::UK);
        }
    }

    private function garages_networks_genarts_master_create_data()
    {
        $this->GarageNetworkGenartMaster = ClassRegistry::init('GarageNetworkGenartMaster');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_GENARTS_MASTER_NAME)) {
            return $this->GarageNetworkGenartMaster->getAllByAagRegionId(ConstantsAAGRegionId::UK);
        }
    }

    private function garages_networks_images_create_data()
    {
        $this->GarageNetworkImage = ClassRegistry::init('GarageNetworkImage');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_IMAGES_NAME)) {
            return $this->GarageNetworkImage->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = GarageNetworkImage.garage_network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetworkImage.*',
                    ),
                )
            );
        }
    }

    private function garages_networks_services_create_data()
    {
        $this->GarageNetworkService = ClassRegistry::init('GarageNetworkService');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_SERVICES_NAME)) {
            return $this->GarageNetworkService->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = GarageNetworkService.garage_network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetworkService.*',
                    ),
                )
            );
        }
    }

    private function garages_networks_services_drivers_create_data()
    {
        $this->GarageNetworkServiceDriver = ClassRegistry::init('GarageNetworkServiceDriver');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_SERVICES_DRIVERS_NAME)) {
            return $this->GarageNetworkServiceDriver->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = GarageNetworkServiceDriver.garage_network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetworkServiceDriver.*',
                    ),
                )
            );
        }
    }

    private function garages_networks_vehicles_create_data()
    {
        $this->GarageNetworkVehicle = ClassRegistry::init('GarageNetworkVehicle');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_VEHICLES_NAME)) {
            return $this->GarageNetworkVehicle->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = GarageNetworkVehicle.garage_network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetworkVehicle.*',
                    ),
                )
            );
        }
    }

    private function garages_networks_vehicles_black_list_create_data()
    {
        $this->GarageNetworkVehicleBlackList = ClassRegistry::init('GarageNetworkVehicleBlackList');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_VEHICLES_BLACK_LIST_NAME)) {
            return $this->GarageNetworkVehicleBlackList->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = GarageNetworkVehicleBlackList.garage_network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetworkVehicleBlackList.*',
                    ),
                )
            );
        }
    }

    private function garages_networks_vehicle_types_create_data()
    {
        $this->GarageNetworkVehicleType = ClassRegistry::init('GarageNetworkVehicleType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_VEHICLE_TYPES_NAME)) {
            return $this->GarageNetworkVehicleType->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = GarageNetworkVehicleType.garage_network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetworkVehicleType.*',
                    ),
                )
            );
        }
    }

    private function garages_networks_works_create_data()
    {
        $this->GarageNetworkWork = ClassRegistry::init('GarageNetworkWork');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_WORKS_NAME)) {
            return $this->GarageNetworkWork->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = GarageNetworkWork.garage_network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetworkWork.*',
                    ),
                )
            );
        }
    }

    private function garages_networks_works_labours_create_data()
    {
        $this->GarageNetworkWorkLabour = ClassRegistry::init('GarageNetworkWorkLabour');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_WORKS_LABOURS_NAME)) {
            return $this->GarageNetworkWorkLabour->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = GarageNetworkWorkLabour.garage_network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetworkWorkLabour.*',
                    ),
                )
            );
        }
    }

    private function garages_networks_works_prices_create_data()
    {
        $this->GarageNetworkWorkPrice = ClassRegistry::init('GarageNetworkWorkPrice');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_NETWORKS_WORKS_PRICES_NAME)) {
            return $this->GarageNetworkWorkPrice->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = GarageNetworkWorkPrice.garage_network_id',
                            ),
                        ),
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageNetwork.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageNetworkWorkPrice.*',
                    ),
                )
            );
        }
    }

    private function garages_oils_create_data()
    {
        $this->GarageOil = ClassRegistry::init('GarageOil');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_OILS_NAME)) {
            return $this->GarageOil->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageOil.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageOil.*',
                    ),
                )
            );
        }
    }

    private function garages_products_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_PRODUCTS_NAME)) {
        }
    }

    private function garages_routes_create_data()
    {
        $this->GarageRoute = ClassRegistry::init('GarageRoute');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_ROUTES_NAME)) {
            return $this->GarageRoute->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageRoute.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageRoute.*',
                    ),
                )
            );
        }
    }

    private function garages_services_create_data()
    {
        $this->GarageService = ClassRegistry::init('GarageService');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_SERVICES_NAME)) {
            return $this->GarageService->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageService.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageService.*',
                    ),
                )
            );
        }
    }

    private function garages_software_create_data()
    {
        $this->GarageSoftware = ClassRegistry::init('GarageSoftware');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_SOFTWARE_NAME)) {
            return $this->GarageSoftware->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageSoftware.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageSoftware.*',
                    ),
                )
            );
        }
    }

    private function garages_specialist_makes_create_data()
    {
        $this->GarageSpecialistMake = ClassRegistry::init('GarageSpecialistMake');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_SPECIALIST_MAKES_NAME)) {
            return $this->GarageSpecialistMake->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageSpecialistMake.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageSpecialistMake.*',
                    ),
                )
            );
        }
    }

    private function garages_statuses_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_STATUSES_NAME)) {
        }
    }

    private function garages_values_adds_create_data()
    {
        $this->GarageValueAdd = ClassRegistry::init('GarageValueAdd');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_VALUES_ADDS_NAME)) {
            return $this->GarageValueAdd->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageValueAdd.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageValueAdd.*',
                    ),
                )
            );
        }
    }

    private function garages_value_add_supplier_create_data()
    {
        $this->GarageValueAddSupplier = ClassRegistry::init('GarageValueAddSupplier');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_VALUE_ADD_SUPPLIER_NAME)) {
            return $this->GarageValueAddSupplier->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageValueAddSupplier.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageValueAddSupplier.*',
                    ),
                )
            );
        }
    }

    private function garages_vehicles_create_data()
    {
        $this->GarageVehicle = ClassRegistry::init('GarageVehicle');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_VEHICLES_NAME)) {
            return $this->GarageVehicle->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageVehicle.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageVehicle.*',
                    ),
                )
            );
        }
    }

    private function garages_vehicle_types_create_data()
    {
        $this->GarageVehicleType = ClassRegistry::init('GarageVehicleType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_VEHICLE_TYPES_NAME)) {
            return $this->GarageVehicleType->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageVehicleType.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageVehicleType.*',
                    ),
                )
            );
        }
    }

    private function garages_visit_frequencies_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_VISIT_FREQUENCIES_NAME)) {
        }
    }

    private function garages_websites_create_data()
    {
        $this->GarageWebsite = ClassRegistry::init('GarageWebsite');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_WEBSITES_NAME)) {
            return $this->GarageWebsite->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageWebsite.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageWebsite.*',
                    ),
                )
            );
        }
    }

    private function garages_workshop_activities_create_data()
    {
        $this->GarageWorkshopActivity = ClassRegistry::init('GarageWorkshopActivity');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GARAGES_WORKSHOP_ACTIVITIES_NAME)) {
            return $this->GarageWorkshopActivity->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = GarageWorkshopActivity.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'GarageWorkshopActivity.*',
                    ),
                )
            );
        }
    }

    private function genarts_create_data()
    {
        $this->Genart = ClassRegistry::init('Genart');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GENARTS_NAME)) {
            return $this->Genart->getAllByAagRegionId(ConstantsAAGRegionId::UK);
        }
    }

    private function genarts_families_create_data()
    {
        $this->GenartFamily = ClassRegistry::init('GenartFamily');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GENARTS_FAMILIES_NAME)) {
            return $this->GenartFamily->getAllByAagRegionId(ConstantsAAGRegionId::UK);
        }
    }

    private function genarts_master_create_data()
    {
        $this->GenartMaster = ClassRegistry::init('GenartMaster');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GENARTS_MASTER_NAME)) {
            return $this->GenartMaster->getAllByAagRegionId(ConstantsAAGRegionId::UK);
        }
    }

    private function grouping_genarts_create_data()
    {
        $this->GroupingGenart = ClassRegistry::init('GroupingGenart');
        if (!$this->fileExistsLocalDb(ConstantesFtp::GROUPING_GENARTS_NAME)) {
            return $this->GroupingGenart->getAllByAagRegionId(ConstantsAAGRegionId::UK);
        }
    }

    private function hold_reason_types_create_data()
    {
        $this->HoldReasonType = ClassRegistry::init('HoldReasonType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::HOLD_REASON_TYPES_NAME)) {
            return $this->HoldReasonType->find(
                'all',
                array(
                    'conditions' => array(
                        'HoldReasonType.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'HoldReasonType.*',
                    ),
                )
            );
        }
    }

    private function leaving_reason_types_create_data()
    {
        $this->LeavingReasonType = ClassRegistry::init('LeavingReasonType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::LEAVING_REASON_TYPES_NAME)) {
            return $this->LeavingReasonType->find(
                'all',
                array(
                    'conditions' => array(
                        'LeavingReasonType.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'LeavingReasonType.*',
                    ),
                )
            );
        }
    }

    private function lists_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::LISTS_NAME)) {
        }
    }

    private function networks_create_data()
    {
        $this->Network = ClassRegistry::init('Network');
        if (!$this->fileExistsLocalDb(ConstantesFtp::NETWORKS_NAME)) {
            return $this->Network->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'PricingType',
                            'table' => 'pricings_types',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Network.pricing_type_id = PricingType.id',
                            ),
                        ),
                        array(
                            'alias' => 'QuotingType',
                            'table' => 'quotings_types',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Network.quoting_type_id = QuotingType.id',
                            ),
                        )
                    ),
                    'conditions' => array(
                        'Network.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Network.*',
                        'PricingType.*',
                        'QuotingType.*'
                    ),
                )
            );
        }
    }

    private function networks_contacts_bdm_create_data()
    {
        $this->NetworkContactBdm = ClassRegistry::init('NetworkContactBdm');
        if (!$this->fileExistsLocalDb(ConstantesFtp::NETWORKS_CONTACTS_BDM_NAME)) {
            return $this->NetworkContactBdm->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Network.id = NetworkContactBdm.network_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Network.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'NetworkContactBdm.*',
                    ),
                )
            );
        }
    }

    private function networks_contacts_lists_create_data()
    {
        $this->NetworkContactList = ClassRegistry::init('NetworkContactList');
        if (!$this->fileExistsLocalDb(ConstantesFtp::NETWORKS_CONTACTS_LISTS_NAME)) {
            return $this->NetworkContactList->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Network.id = NetworkContactList.network_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Network.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'NetworkContactList.*',
                    ),
                )
            );
        }
    }

    private function networks_contract_types_create_data()
    {
        $this->NetworkContractType = ClassRegistry::init('NetworkContractType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::NETWORKS_CONTRACT_TYPES_NAME)) {
            return $this->NetworkContractType->find(
                'all',
                array(
                    'conditions' => array(
                        'NetworkContractType.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'NetworkContractType.*',
                    ),
                )
            );
        }
    }

    private function networks_statuses_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::NETWORKS_STATUSES_NAME)) {
        }
    }

    private function orders_create_data()
    {
        $this->Order = ClassRegistry::init('Order');
        if (!$this->fileExistsLocalDb(ConstantesFtp::ORDERS_NAME)) {
            return $this->Order->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Garage',
                            'table' => 'garages',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.id = Order.garage_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Order.*',
                    ),
                )
            );
        }
    }

    private function order_products_create_data()
    {
        $this->OrderProduct = ClassRegistry::init('OrderProduct');
        if (!$this->fileExistsLocalDb(ConstantesFtp::ORDER_PRODUCTS_NAME)) {
            return $this->OrderProduct->find(
                'all',
                array(
                    'conditions' => array(
                        'OrderProduct.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'OrderProduct.*',
                    ),
                )
            );
        }
    }

    private function order_types_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::ORDER_TYPES_NAME)) {
        }
    }

    private function positions_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::POSITIONS_NAME)) {
        }
    }

    private function postcodes_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::POSTCODES_NAME)) {
        }
    }

    private function postcode_provinces_create_data()
    {
        $this->PostcodeProvince = ClassRegistry::init('PostcodeProvince');
        if (!$this->fileExistsLocalDb(ConstantesFtp::POSTCODE_PROVINCES_NAME)) {
            return $this->PostcodeProvince->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Province',
                            'table' => 'provinces',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Province.id = PostcodeProvince.province_id',
                            ),
                        ),
                        array(
                            'alias' => 'Country',
                            'table' => 'countries',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Country.id = Province.country_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Country.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'PostcodeProvince.*',
                    ),
                )
            );
        }
    }

    private function provinces_create_data()
    {
        $this->Province = ClassRegistry::init('Province');
        if (!$this->fileExistsLocalDb(ConstantesFtp::PROVINCES_NAME)) {
            return $this->Province->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Country',
                            'table' => 'countries',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Country.id = Province.country_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Country.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Province.*',
                    ),
                )
            );
        }
    }

    private function reasons_delegates_create_data()
    {
        $this->ReasonDelegate = ClassRegistry::init('ReasonDelegate');
        if (!$this->fileExistsLocalDb(ConstantesFtp::REASONS_DELEGATES_NAME)) {
            return $this->ReasonDelegate->find(
                'all',
                array(
                    'conditions' => array(
                        'ReasonDelegate.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'ReasonDelegate.*',
                    ),
                )
            );
        }
    }

    private function regions_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::REGIONS_NAME)) {
        }
    }

    private function roles_create_data()
    {
        $this->Role = ClassRegistry::init('Role');
        if (!$this->fileExistsLocalDb(ConstantesFtp::ROLES_NAME)) {
            return $this->Role->find(
                'all',
                array(
                    'fields' => array(
                        'Role.*',
                    ),
                )
            );
        }
    }

    private function services_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::SERVICES_NAME)) {
        }
    }

    private function services_drivers_create_data()
    {
        $this->ServiceDriver = ClassRegistry::init('ServiceDriver');
        if (!$this->fileExistsLocalDb(ConstantesFtp::SERVICES_DRIVERS_NAME)) {
            return $this->ServiceDriver->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Network.id = ServiceDriver.network_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Network.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'ServiceDriver.*',
                    ),
                )
            );
        }
    }

    private function services_types_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::SERVICES_TYPES_NAME)) {
        }
    }

    private function software_create_data()
    {
        $this->Software = ClassRegistry::init('Software');
        if (!$this->fileExistsLocalDb(ConstantesFtp::SOFTWARE_NAME)) {
            return $this->Software->find(
                'all',
                array(
                    'conditions' => array(
                        'Software.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Software.*',
                    ),
                )
            );
        }
    }

    private function software_manufactures_create_data()
    {
        $this->SoftwareManufacture = ClassRegistry::init('SoftwareManufacture');
        if (!$this->fileExistsLocalDb(ConstantesFtp::SOFTWARE_MANUFACTURES_NAME)) {
            return $this->SoftwareManufacture->find(
                'all',
                array(
                    'conditions' => array(
                        'SoftwareManufacture.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'SoftwareManufacture.*',
                    ),
                )
            );
        }
    }

    private function software_types_create_data()
    {
        $this->SoftwareType = ClassRegistry::init('SoftwareType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::SOFTWARE_TYPES_NAME)) {
            return $this->SoftwareType->find(
                'all',
                array(
                    'conditions' => array(
                        'SoftwareType.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'SoftwareType.*',
                    ),
                )
            );
        }
    }

    private function tasks_data()
    {
        $tasks = ClassRegistry::init('Task');
        if (!$this->fileExistsLocalDb(ConstantesFtp::TASKS_NAME)) {
            return $tasks->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Users',
                            'table' => 'users',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Task.user_creation_id = Users.id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'OR' => array(
                            'Users.aag_region_id' => ConstantsAAGRegionId::UK,
                        )
                    ),
                    'fields' => array(
                        'Task.*',
                    ),
                )
            );
        }
    }

    private function trading_groups_create_data()
    {
        $this->TradingGroup = ClassRegistry::init('TradingGroup');
        if (!$this->fileExistsLocalDb(ConstantesFtp::TRADING_GROUPS_NAME)) {
            return $this->TradingGroup->find(
                'all',
                array(
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'TradingGroup.*',
                    ),
                )
            );
        }
    }

    private function trading_groups_distributors_networks_create_data()
    {
        $this->TradingGroupDistributorNetwork = ClassRegistry::init('TradingGroupDistributorNetwork');
        if (!$this->fileExistsLocalDb(ConstantesFtp::TRADING_GROUPS_DISTRIBUTORS_NETWORKS_NAME)) {
            return $this->TradingGroupDistributorNetwork->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = TradingGroupDistributorNetwork.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'TradingGroupDistributorNetwork.*',
                    ),
                )
            );
        }
    }

    private function trading_groups_networks_create_data()
    {
        $this->TradingGroupNetwork = ClassRegistry::init('TradingGroupNetwork');
        if (!$this->fileExistsLocalDb(ConstantesFtp::TRADING_GROUPS_NETWORKS_NAME)) {
            return $this->TradingGroupNetwork->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'TradingGroup',
                            'table' => 'trading_groups',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'TradingGroup.id = TradingGroupNetwork.trading_group_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'TradingGroup.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'TradingGroupNetwork.*',
                    ),
                )
            );
        }
    }

    private function trainings_courses_create_data()
    {
        $this->TrainingCourse = ClassRegistry::init('TrainingCourse');
        if (!$this->fileExistsLocalDb(ConstantesFtp::TRAININGS_COURSES_NAME)) {
            return $this->TrainingCourse->find(
                'all',
                array(
                    'fields' => array(
                        'TrainingCourse.*',
                    ),
                )
            );
        }
    }

    private function trainings_credits_create_data()
    {
        $this->TrainingCredit = ClassRegistry::init('TrainingCredit');
        if (!$this->fileExistsLocalDb(ConstantesFtp::TRAININGS_CREDITS_NAME)) {
            return $this->TrainingCredit->find(
                'all',
                array(
                    'fields' => array(
                        'TrainingCredit.*',
                    ),
                )
            );
        }
    }

    private function trainings_credits_networks_create_data()
    {
        $this->TrainingCreditNetwork = ClassRegistry::init('TrainingCreditNetwork');
        if (!$this->fileExistsLocalDb(ConstantesFtp::TRAININGS_CREDITS_NETWORKS_NAME)) {
            return $this->TrainingCreditNetwork->find(
                'all',
                array(
                    'fields' => array(
                        'TrainingCreditNetwork.*',
                    ),
                )
            );
        }
    }

    private function trainings_delegates_create_data()
    {
        $this->TrainingDelegate = ClassRegistry::init('TrainingDelegate');
        if (!$this->fileExistsLocalDb(ConstantesFtp::TRAININGS_DELEGATES_NAME)) {
            return $this->TrainingDelegate->find(
                'all',
                array(
                    'fields' => array(
                        'TrainingDelegate.*',
                    ),
                )
            );
        }
    }

    private function trainings_planned_courses_create_data()
    {
        $this->TrainingPlannedCourse = ClassRegistry::init('TrainingPlannedCourse');
        if (!$this->fileExistsLocalDb(ConstantesFtp::TRAININGS_PLANNED_COURSES_NAME)) {
            return $this->TrainingPlannedCourse->find(
                'all',
                array(
                    'fields' => array(
                        'TrainingPlannedCourse.*',
                    ),
                )
            );
        }
    }

    private function trainings_providers_create_data()
    {
        $this->TrainingProvider = ClassRegistry::init('TrainingProvider');
        if (!$this->fileExistsLocalDb(ConstantesFtp::TRAININGS_PROVIDERS_NAME)) {
            return $this->TrainingProvider->find(
                'all',
                array(
                    'fields' => array(
                        'TrainingProvider.*',
                    ),
                )
            );
        }
    }

    private function trainings_trainers_create_data()
    {
        $this->TrainingTrainer = ClassRegistry::init('TrainingTrainer');
        if (!$this->fileExistsLocalDb(ConstantesFtp::TRAININGS_TRAINERS_NAME)) {
            return $this->TrainingTrainer->find(
                'all',
                array(
                    'fields' => array(
                        'TrainingTrainer.*',
                    ),
                )
            );
        }
    }

    private function users_create_data()
    {
        $this->User = ClassRegistry::init('User');
        if (!$this->fileExistsLocalDb(ConstantesFtp::USERS_NAME)) {
            return $this->User->find(
                'all',
                array(
                    'conditions' => array(
                        'User.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'User.*',
                    ),
                )
            );
        }
    }

    private function values_adds_create_data()
    {
        $this->ValueAdd = ClassRegistry::init('ValueAdd');
        if (!$this->fileExistsLocalDb(ConstantesFtp::VALUES_ADDS_NAME)) {
            return $this->ValueAdd->find(
                'all',
                array(
                    'conditions' => array(
                        'ValueAdd.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'ValueAdd.*',
                    ),
                )
            );
        }
    }

    private function value_add_suppliers_create_data()
    {
        $this->ValueAddSupplier = ClassRegistry::init('ValueAddSupplier');
        if (!$this->fileExistsLocalDb(ConstantesFtp::VALUE_ADD_SUPPLIERS_NAME)) {
            return $this->ValueAddSupplier->find(
                'all',
                array(
                    'conditions' => array(
                        'ValueAddSupplier.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'ValueAddSupplier.*',
                    ),
                )
            );
        }
    }

    private function value_add_supplier_type_create_data()
    {
        $this->ValueAddSupplierType = ClassRegistry::init('ValueAddSupplierType');
        if (!$this->fileExistsLocalDb(ConstantesFtp::VALUE_ADD_SUPPLIER_TYPE_NAME)) {
            return $this->ValueAddSupplierType->find(
                'all',
                array(
                    'conditions' => array(
                        'ValueAddSupplierType.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'ValueAddSupplierType.*',
                    ),
                )
            );
        }
    }

    private function vehicle_types_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::VEHICLE_TYPES_NAME)) {
        }
    }

    private function venues_create_data()
    {
        $this->Venue = ClassRegistry::init('Venue');
        if (!$this->fileExistsLocalDb(ConstantesFtp::VENUES_NAME)) {
            return $this->Venue->find(
                'all',
                array(
                    'conditions' => array(
                        'Venue.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Venue.*',
                    ),
                )
            );
        }
    }

    private function works_create_data()
    {
        $this->Work = ClassRegistry::init('Work');
        if (!$this->fileExistsLocalDb(ConstantesFtp::WORKS_NAME)) {
            return $this->Work->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Network.id = Work.network_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Network.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Work.*',
                    ),
                )
            );
        }
    }

    private function workshop_activities_create_data()
    {
        if (!$this->fileExistsLocalDb(ConstantesFtp::WORKSHOP_ACTIVITIES_NAME)) {
        }
    }

    private function agreements_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->agreements_create_data();

        $filename = date("N") . ConstantesFtp::AGREEMENT_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'code',
            'creation_date',
        );
        fputcsv($file, $table);
        foreach ($data as $agreements) {
            $fila = array(
                $agreements['Agreement']['id'],
                $agreements['Agreement']['nombre'],
                $agreements['Agreement']['codigo'],
                $agreements['Agreement']['fecha_creacion'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function annex_details_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->annex_details_create_data();

        $filename = date("N") . ConstantesFtp::ANNEX_DETAILS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $annex_details) {
            $fila = array(
                $annex_details['AnnexDetail']['id'],
                $annex_details['AnnexDetail']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function appointments_csv()
    {
        try {
            ini_set('memory_limit', '3G');
            set_time_limit(60 * 60 * 24);

            $data = $this->appointments_data();
            $appointments = ClassRegistry::init('Appointment');
            $appointmentObjectivesComments = ClassRegistry::init('AppointmentObjectiveComment');
            $appointmentObjectives = ClassRegistry::init('AppointmentObjective');

            $managementObjectives = array();
            $personalObjectives = array();

            $appointment_status = $appointments->AppointmentStatus->search_list();
            $appointment_types = $appointments->AppointmentType->search_list();
            $feelings_list = $appointments->AppointmentFeeling->search_list();
            $requiresFollowUp = array(
                ConstantsBooleans::NO => __t('General.No', __l()),
                ConstantsBooleans::YES => __t('General.Yes', __l())
            );
            $objectives_status = array(
                '0' => __t('Objective.Pending', __l()),
                '1' => __t('General.Success', __l()),
                '2' => __t('Objective.Failed', __l()),
                '3' => __t('Objective.Requires_manager', __l())
            );

            $filename = date("N") . ConstantesFtp::APPOINTMENTS_NAME;
            $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
            $file = fopen($pathFilename, 'a');
            $table = array(
                'id',
                'customer_id',
                'customer_type',
                'date',
                'end_date',
                'start_time',
                'end_time',
                'assigned_to',
                'meeting_debrief',
                'customer_performance_summary',
                'management_objetives',
                'management_objetives_status',
                'management_objetives_comment',
                'personal_objetives',
                'personal_objetives_status',
                'personal_objetives_comment',
                'MTD +/-',
                'QTD +/-',
                'YTD +/-',
                'requires_follow_up',
                'feeling',
                'status',
                'type',
            );
            fputcsv($file, $table);
            foreach ($data as $key => $appointments) {
                $objectiveManagementName = '';
                $objectiveManagementStatus = '';
                $objectiveManagementComment = '';
                $personalManagementName = '';
                $personalManagementStatus = '';
                $personalManagementComment = '';
                $managementObjectives = $appointmentObjectives->getPersonalObjectivesByTypeManagement($appointments['Appointment']['id']);
                $appointmentManagementObjectives = $appointmentObjectivesComments->getObjectivesManagement($appointments['Appointment']['id']);
                $personalObjectives = $appointmentObjectives->getPersonalObjectivesByTypePersonal($appointments['Appointment']['id']);
                $appointmentPersonalObjectives = $appointmentObjectivesComments->getObjectivesPersonal($appointments['Appointment']['id']);

                if (!empty($managementObjectives) && !empty($appointmentManagementObjectives)) {
                    foreach ($managementObjectives as $key => $managementObjective) {
                        $objectiveManagementName .= $managementObjective . ',';
                        $objectiveManagementStatus .= !empty($key) ? $objectives_status[$appointmentManagementObjectives[$key]['status']] . ',' : null;
                        $objectiveManagementComment .= $appointmentManagementObjectives[$key]['comment'] . ',';
                    }
                }
                if (!empty($personalObjectives) && !empty($appointmentPersonalObjectives)) {
                    foreach ($personalObjectives as $key => $personalObjective) {
                        $personalManagementName .= $personalObjective . ',';
                        $personalManagementStatus .= !empty($key) ? $objectives_status[$appointmentPersonalObjectives[$key]['status']] . ',' : null;
                        $personalManagementComment .= $appointmentPersonalObjectives[$key]['comment'] . ',';
                    }
                }

                $customerType = null;
                if (!empty($appointments['Appointment']['garage_id'])) {
                    $customerType = __t('Garage.Garage', __l());
                } elseif (!empty($appointments['Appointment']['distributor_id'])) {
                    $customerType = __t('Distributor.Distributor', __l());
                }

                $fila = array(
                    $appointments['Appointment']['id'],
                    isset($appointments['Appointment']['garage_id']) ? $appointments['Appointment']['garage_id'] : $appointments['Appointment']['distributor_id'],
                    $customerType,
                    $appointments['Appointment']['date'],
                    $appointments['Appointment']['end_date'],
                    $appointments['Appointment']['start_time'],
                    $appointments['Appointment']['end_time'],
                    $appointments['Appointment']['user_assigned_id'] ?? null,
                    !empty($appointments['Appointment']['feedback']) ? h($appointments['Appointment']['feedback']) : null,
                    !empty($appointments['Appointment']['customer_perfomance_summary']) ? h($appointments['Appointment']['customer_perfomance_summary']) : null,
                    $objectiveManagementName,
                    $objectiveManagementStatus,
                    $objectiveManagementComment,
                    $personalManagementName,
                    $personalManagementStatus,
                    $personalManagementComment,
                    $appointments['Appointment']['mtd'],
                    $appointments['Appointment']['qtd'],
                    $appointments['Appointment']['ytd'],
                    $requiresFollowUp[$appointments['Appointment']['requires_follow_up']] ?? null,
                    $feelings_list[$appointments['Appointment']['appointment_feeling_id']] ?? null,
                    $appointment_status[$appointments['Appointment']['appointment_status_id']],
                    $appointment_types[$appointments['Appointment']['appointment_type_id']],
                );
                fputcsv($file, $fila);
            }
            fclose($file);
        } catch (Exception $e) {
            CakeLog::debug(print_r("SFTP - Export DB - An exception has ocurred " . $e->getMessage(), true));
        }
    }

    private function associations_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->associations_types_create_data();

        $filename = date("N") . ConstantesFtp::ASSOCIATIONS_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $associations_types) {
            $fila = array(
                $associations_types['AssociationType']['id'],
                $associations_types['AssociationType']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function associations_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->associations_create_data();

        $filename = date("N") . ConstantesFtp::ASSOCIATIONS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function billings_schedules_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->billings_schedules_create_data();

        $filename = date("N") . ConstantesFtp::BILLINGS_SCHEDULES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $billings_schedules) {
            $fila = array(
                $billings_schedules['BillingSchedule']['id'],
                $billings_schedules['BillingSchedule']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function bookings_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->bookings_create_data();

        $filename = date("N") . ConstantesFtp::BOOKINGS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'network_id',
            'garage_id',
            'date',
            'time',
            'time_to',
            'creation_date',
            'quotation_id',
            'customer_name',
            'customer_phone',
            'customer_email',
            'marketing_acceptance',
            'plate',
            'brand',
            'model',
            'version',
            'mot_exp_date',
            'fuel',
            'registered_on',
            'mileage',
            'work_id',
            'additional_info',
            'booking_status',
        );
        fputcsv($file, $table);
        foreach ($data as $bookings) {
            $fila = array(
                $bookings['Booking']['id'],
                $bookings['Booking']['network_id'],
                $bookings['Booking']['garage_id'],
                $bookings['Booking']['date'],
                $bookings['Booking']['time'],
                $bookings['Booking']['time_to'],
                $bookings['Booking']['creation_date'],
                $bookings['Booking']['quotation_id'],
                $bookings['Booking']['customer_name'],
                $bookings['Booking']['customer_phone'],
                $bookings['Booking']['customer_email'],
                $bookings['Booking']['marketing_acceptance'],
                $bookings['Booking']['plate'] = Texto::encryptDecryptText($bookings['Booking']['plate']),
                $bookings['Booking']['brand'],
                $bookings['Booking']['model'],
                $bookings['Booking']['version'],
                $bookings['Booking']['mot_exp_date'],
                $bookings['Booking']['fuel'],
                $bookings['Booking']['registered_on'],
                $bookings['Booking']['mileage'],
                $bookings['Booking']['work_id'],
                $bookings['Booking']['additional_info'],
                $bookings['Booking']['booking_status'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function cities_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->cities_create_data();

        $filename = date("N") . ConstantesFtp::CITIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'latitude',
            'longitude',
            'province_id',
        );
        fputcsv($file, $table);
        foreach ($data as $cities) {
            $fila = array(
                $cities['City']['id'],
                $cities['City']['name'],
                $cities['City']['latitude'],
                $cities['City']['longitude'],
                $cities['City']['province_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function contacts_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->contacts_create_data();

        $filename = date("N") . ConstantesFtp::CONTACTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'title_id',
            'contact_id',
            'first_name',
            'last_name',
            'position_id',
            'garage_id',
            'distributor_id',
            'logistic_center_id',
            'phone',
            'mobile_phone',
            'identification_number',
            'email',
            'creation_date',
            'guid',
        );
        fputcsv($file, $table);
        foreach ($data as $contacts) {
            $fila = array(
                $contacts['Contact']['id'],
                $contacts['Contact']['title_id'],
                $contacts['Contact']['contact_id'],
                $contacts['Contact']['first_name'],
                $contacts['Contact']['last_name'],
                $contacts['Contact']['position_id'],
                $contacts['Contact']['garage_id'],
                $contacts['Contact']['distributor_id'],
                $contacts['Contact']['logistic_center_id'],
                $contacts['Contact']['phone'],
                $contacts['Contact']['mobile_phone'],
                $contacts['Contact']['identification_number'],
                $contacts['Contact']['email'],
                $contacts['Contact']['creation_date'],
                $contacts['Contact']['guid'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function contacts_titles_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->contacts_titles_create_data();

        $filename = date("N") . ConstantesFtp::CONTACTS_TITLES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function countries_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->countries_create_data();

        $filename = date("N") . ConstantesFtp::COUNTRIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'country_code',
            'country_code_2',
            'image',
            'currency',
            'symbol',
        );
        fputcsv($file, $table);
        foreach ($data as $countries) {
            $fila = array(
                $countries['Country']['id'],
                $countries['Country']['name'],
                $countries['Country']['country_code'],
                $countries['Country']['country_code_2'],
                $countries['Country']['image'],
                $countries['Country']['currency'],
                $countries['Country']['symbol'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function courses_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->courses_types_create_data();

        $filename = date("N") . ConstantesFtp::COURSES_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $courses_types) {
            $fila = array(
                $courses_types['CourseType']['id'],
                $courses_types['CourseType']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function courtesy_car_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->courtesy_car_types_create_data();

        $filename = date("N") . ConstantesFtp::COURTESY_CAR_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $courtesy_car_types) {
            $fila = array(
                $courtesy_car_types['CourtesyCarType']['id'],
                $courtesy_car_types['CourtesyCarType']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function customers_activities_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->customers_activities_create_data();

        $filename = date("N") . ConstantesFtp::CUSTOMERS_ACTIVITIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $customers_activities) {
            $fila = array(
                $customers_activities['CustomerActivity']['id'],
                $customers_activities['CustomerActivity']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'account_number',
            'abbreviation',
            'head_office',
            'parent_id',
            'client_type',
            'subsidiary',
            'aag_member',
            'trading_as',
            'phone',
            'fax',
            'email',
            'web',
            'standalone',
            'start_date',
            'end_date',
            'address1',
            'address2',
            'address3',
            'address4',
            'latitude',
            'longitude',
            'province_id',
            'sales_area_id',
            'postcode',
            'monday_open_1',
            'monday_closed_1',
            'monday_open_2',
            'monday_closed_2',
            'tuesday_open_1',
            'tuesday_closed_1',
            'tuesday_open_2',
            'tuesday_closed_2',
            'wednesday_open_1',
            'wednesday_closed_1',
            'wednesday_open_2',
            'wednesday_closed_2',
            'thursday_open_1',
            'thursday_closed_1',
            'thursday_open_2',
            'thursday_closed_2',
            'friday_open_1',
            'friday_closed_1',
            'friday_open_2',
            'friday_closed_2',
            'saturday_open_1',
            'saturday_closed_1',
            'saturday_open_2',
            'saturday_closed_2',
            'sunday_open_1',
            'sunday_closed_1',
            'sunday_open_2',
            'sunday_closed_2',
            'last_visit',
            'reg_number',
            'rebate_name',
            'primary_activity_id',
            'association_id',
            'association_type_id',
            'currency',
            'MAMID',
            'VAT_number',
            'detax_code',
            'siret',
            'credit_watch',
            'trading_group_id',
            'distributor_id',
            'user_id',
            'creation_date',
            'modification_date',
            'distributor_type_id',
            'status',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors) {
            $fila = array(
                $distributors['Distributor']['id'],
                $distributors['Distributor']['name'],
                $distributors['Distributor']['account_number'],
                $distributors['Distributor']['abbreviation'],
                $distributors['Distributor']['head_office'],
                $distributors['Distributor']['parent_id'],
                $distributors['Distributor']['client_type'],
                $distributors['Distributor']['subsidiary'],
                $distributors['Distributor']['aag_member'],
                $distributors['Distributor']['trading_as'],
                $distributors['Distributor']['phone'],
                $distributors['Distributor']['fax'],
                $distributors['Distributor']['email'],
                $distributors['Distributor']['web'],
                $distributors['Distributor']['standalone'],
                $distributors['Distributor']['start_date'],
                $distributors['Distributor']['end_date'],
                $distributors['Distributor']['address1'],
                $distributors['Distributor']['address2'],
                $distributors['Distributor']['address3'],
                $distributors['Distributor']['address4'],
                $distributors['Distributor']['latitude'],
                $distributors['Distributor']['longitude'],
                $distributors['Distributor']['province_id'],
                $distributors['Distributor']['sales_area_id'],
                $distributors['Distributor']['postcode'],
                $distributors['Distributor']['monday_open_1'],
                $distributors['Distributor']['monday_closed_1'],
                $distributors['Distributor']['monday_open_2'],
                $distributors['Distributor']['monday_closed_2'],
                $distributors['Distributor']['tuesday_open_1'],
                $distributors['Distributor']['tuesday_closed_1'],
                $distributors['Distributor']['tuesday_open_2'],
                $distributors['Distributor']['tuesday_closed_2'],
                $distributors['Distributor']['wednesday_open_1'],
                $distributors['Distributor']['wednesday_closed_1'],
                $distributors['Distributor']['wednesday_open_2'],
                $distributors['Distributor']['wednesday_closed_2'],
                $distributors['Distributor']['thursday_open_1'],
                $distributors['Distributor']['thursday_closed_1'],
                $distributors['Distributor']['thursday_open_2'],
                $distributors['Distributor']['thursday_closed_2'],
                $distributors['Distributor']['friday_open_1'],
                $distributors['Distributor']['friday_closed_1'],
                $distributors['Distributor']['friday_open_2'],
                $distributors['Distributor']['friday_closed_2'],
                $distributors['Distributor']['saturday_open_1'],
                $distributors['Distributor']['saturday_closed_1'],
                $distributors['Distributor']['saturday_open_2'],
                $distributors['Distributor']['saturday_closed_2'],
                $distributors['Distributor']['sunday_open_1'],
                $distributors['Distributor']['sunday_closed_1'],
                $distributors['Distributor']['sunday_open_2'],
                $distributors['Distributor']['sunday_closed_2'],
                $distributors['Distributor']['last_visit'],
                $distributors['Distributor']['reg_number'],
                $distributors['Distributor']['rebate_name'],
                $distributors['Distributor']['primary_activity_id'],
                $distributors['Distributor']['association_id'],
                $distributors['Distributor']['association_type_id'],
                $distributors['Distributor']['currency'],
                $distributors['Distributor']['MAMID'],
                $distributors['Distributor']['VAT_number'],
                $distributors['Distributor']['detax_code'],
                $distributors['Distributor']['siret'],
                $distributors['Distributor']['credit_watch'],
                $distributors['Distributor']['trading_group_id'],
                $distributors['Distributor']['distributor_id'],
                $distributors['Distributor']['user_id'],
                $distributors['Distributor']['creation_date'],
                $distributors['Distributor']['modification_date'],
                $distributors['Distributor']['distributor_type_id'],
                $distributors['Distributor']['status'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_activities_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $this->distributors_activities_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_ACTIVITIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function distributors_activities_primary_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $this->distributors_activities_primary_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_ACTIVITIES_PRIMARY_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function distributors_comments_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_comments_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_COMMENTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'body',
            'creation_date',
            'user_id',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_comments) {
            $fila = array(
                $distributors_comments['DistributorComment']['id'],
                $distributors_comments['DistributorComment']['distributor_id'],
                $distributors_comments['DistributorComment']['body'],
                $distributors_comments['DistributorComment']['creation_date'],
                $distributors_comments['DistributorComment']['user_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_contacts_bdm_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_contacts_bdm_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_CONTACTS_BDM_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'contact_id',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_contacts_bdm) {
            $fila = array(
                $distributors_contacts_bdm['DistributorContactBdm']['id'],
                $distributors_contacts_bdm['DistributorContactBdm']['distributor_id'],
                $distributors_contacts_bdm['DistributorContactBdm']['contact_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_contacts_general_branch_manager_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_contacts_general_branch_manager_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_CONTACTS_GENERAL_BRANCH_MANAGER_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'contact_id',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_contacts_general_branch_manager) {
            $fila = array(
                $distributors_contacts_general_branch_manager['DistributorContactGeneralBranchManager']['id'],
                $distributors_contacts_general_branch_manager['DistributorContactGeneralBranchManager']['distributor_id'],
                $distributors_contacts_general_branch_manager['DistributorContactGeneralBranchManager']['contact_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_contacts_staff_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_contacts_staff_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_CONTACTS_STAFF_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'contact_id',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_contacts_staff) {
            $fila = array(
                $distributors_contacts_staff['DistributorContactStaff']['id'],
                $distributors_contacts_staff['DistributorContactStaff']['distributor_id'],
                $distributors_contacts_staff['DistributorContactStaff']['contact_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_contracts_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_contracts_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_CONTRACTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'trading_group_id',
            'network_id',
            'distributor_network_id',
            'start_date',
            'end_date',
            'leaving_reason',
            'leaving_reason_id',
            'modification_date',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_contracts) {
            $fila = array(
                $distributors_contracts['DistributorContract']['id'],
                $distributors_contracts['DistributorContract']['distributor_id'],
                $distributors_contracts['DistributorContract']['trading_group_id'],
                $distributors_contracts['DistributorContract']['network_id'],
                $distributors_contracts['DistributorContract']['distributor_network_id'],
                $distributors_contracts['DistributorContract']['start_date'],
                $distributors_contracts['DistributorContract']['end_date'],
                $distributors_contracts['DistributorContract']['leaving_reason'],
                $distributors_contracts['DistributorContract']['leaving_reason_id'],
                $distributors_contracts['DistributorContract']['modification_date'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_customer_activities_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_customer_activities_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_CUSTOMER_ACTIVITIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'customer_activity_id',
            'distributor_id',
            'type',
            'start_date',
            'end_date',
            'modification_date',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_customer_activities) {
            $fila = array(
                $distributors_customer_activities['DistributorCustomerActivity']['id'],
                $distributors_customer_activities['DistributorCustomerActivity']['customer_activity_id'],
                $distributors_customer_activities['DistributorCustomerActivity']['distributor_id'],
                $distributors_customer_activities['DistributorCustomerActivity']['type'],
                $distributors_customer_activities['DistributorCustomerActivity']['start_date'],
                $distributors_customer_activities['DistributorCustomerActivity']['end_date'],
                $distributors_customer_activities['DistributorCustomerActivity']['modification_date'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_customer_activities_workshops_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_customer_activities_workshops_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_CUSTOMER_ACTIVITIES_WORKSHOPS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_customer_activity_id',
            'workshop_activity_id',
            'activity_details',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_customer_activities_workshops) {
            $fila = array(
                $distributors_customer_activities_workshops['DistributorCustomerActivityWorkshop']['id'],
                $distributors_customer_activities_workshops['DistributorCustomerActivityWorkshop']['distributor_customer_activity_id'],
                $distributors_customer_activities_workshops['DistributorCustomerActivityWorkshop']['workshop_activity_id'],
                $distributors_customer_activities_workshops['DistributorCustomerActivityWorkshop']['activity_details'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_distributors_activities_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_distributors_activities_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_DISTRIBUTORS_ACTIVITIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'distributor_activity_id',
            'join_date',
            'left_date',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_distributors_activities) {
            $fila = array(
                $distributors_distributors_activities['DistributorDistributorActivity']['id'],
                $distributors_distributors_activities['DistributorDistributorActivity']['distributor_id'],
                $distributors_distributors_activities['DistributorDistributorActivity']['distributor_activity_id'],
                $distributors_distributors_activities['DistributorDistributorActivity']['join_date'],
                $distributors_distributors_activities['DistributorDistributorActivity']['left_date'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_distributors_networks_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_distributors_networks_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_DISTRIBUTORS_NETWORKS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'network_id',
            'trading_group_id',
            'garage_number',
            'contract_start_date',
            'contract_end_date',
            'reason_leaving_id',
            'status',
            'last',
            'modification_date',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_distributors_networks) {
            $fila = array(
                $distributors_distributors_networks['DistributorDistributorNetwork']['id'],
                $distributors_distributors_networks['DistributorDistributorNetwork']['distributor_id'],
                $distributors_distributors_networks['DistributorDistributorNetwork']['network_id'],
                $distributors_distributors_networks['DistributorDistributorNetwork']['trading_group_id'],
                $distributors_distributors_networks['DistributorDistributorNetwork']['garage_number'],
                $distributors_distributors_networks['DistributorDistributorNetwork']['contract_start_date'],
                $distributors_distributors_networks['DistributorDistributorNetwork']['contract_end_date'],
                $distributors_distributors_networks['DistributorDistributorNetwork']['reason_leaving_id'],
                $distributors_distributors_networks['DistributorDistributorNetwork']['status'],
                $distributors_distributors_networks['DistributorDistributorNetwork']['last'],
                $distributors_distributors_networks['DistributorDistributorNetwork']['modification_date'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_figures_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_figures_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_FIGURES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'customer_no',
            'figures',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function distributors_figures_details_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_figures_details_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_FIGURES_DETAILS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'customer_no',
            'figures_details',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function distributors_images_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_images_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_IMAGES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'creation_date',
            'file',
            'type',
            'ext',
            'source_name',
            'principal',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_images) {
            $fila = array(
                $distributors_images['DistributorImage']['id'],
                $distributors_images['DistributorImage']['distributor_id'],
                $distributors_images['DistributorImage']['creation_date'],
                $distributors_images['DistributorImage']['file'],
                $distributors_images['DistributorImage']['type'],
                $distributors_images['DistributorImage']['ext'],
                $distributors_images['DistributorImage']['source_name'],
                $distributors_images['DistributorImage']['principal'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_kpis_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_kpis_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_KPIS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'customer_no',
            'kpis',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function distributors_labels_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_labels_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_LABELS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'label_type_id',
            'start_date',
            'end_date',
            'modification_date',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_labels) {
            $fila = array(
                $distributors_labels['DistributorLabel']['id'],
                $distributors_labels['DistributorLabel']['distributor_id'],
                $distributors_labels['DistributorLabel']['label_type_id'],
                $distributors_labels['DistributorLabel']['start_date'],
                $distributors_labels['DistributorLabel']['end_date'],
                $distributors_labels['DistributorLabel']['modification_date'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_networks_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_networks_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_NETWORKS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'image',
            'image_pin',
            'image_cluster',
            'web',
            'network_type',
            'primary_color',
            'primary_font_color',
            'primary_background_color',
            'secondary_color',
            'secondary_font_color',
            'secondary_background_color',
            'color_active',
            'tertiary_color',
            'menu_color',
            'menu_background_color',
            'color_exito',
            'color_fallo',
            'color_informacion',
            'color_disabled',
            'creation_date',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function distributors_networks_contacts_bdm_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_networks_contacts_bdm_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_NETWORKS_CONTACTS_BDM_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_network_id',
            'contact_id',
            'distributor_id',
            'garage_id',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_networks_contacts_bdm) {
            $fila = array(
                $distributors_networks_contacts_bdm['DistributorNetworkContactBdm']['id'],
                $distributors_networks_contacts_bdm['DistributorNetworkContactBdm']['distributor_network_id'],
                $distributors_networks_contacts_bdm['DistributorNetworkContactBdm']['contact_id'],
                $distributors_networks_contacts_bdm['DistributorNetworkContactBdm']['distributor_id'],
                $distributors_networks_contacts_bdm['DistributorNetworkContactBdm']['garage_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_objectives_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_objectives_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_OBJECTIVES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'objective_id',
            'from',
            'to',
            'creation_date',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_objectives) {
            $fila = array(
                $distributors_objectives['DistributorObjective']['id'],
                $distributors_objectives['DistributorObjective']['distributor_id'],
                $distributors_objectives['DistributorObjective']['objective_id'],
                $distributors_objectives['DistributorObjective']['from'],
                $distributors_objectives['DistributorObjective']['to'],
                $distributors_objectives['DistributorObjective']['creation_date'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_routes_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_routes_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_ROUTES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'route_id',
            'order',
            'start_time',
            'end_time',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_routes) {
            $fila = array(
                $distributors_routes['DistributorRoute']['id'],
                $distributors_routes['DistributorRoute']['distributor_id'],
                $distributors_routes['DistributorRoute']['route_id'],
                $distributors_routes['DistributorRoute']['order'],
                $distributors_routes['DistributorRoute']['start_time'],
                $distributors_routes['DistributorRoute']['end_time'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_services_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_services_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_SERVICES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'service_type_id',
            'start_date',
            'end_date',
            'modification_date',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_services) {
            $fila = array(
                $distributors_services['DistributorService']['id'],
                $distributors_services['DistributorService']['distributor_id'],
                $distributors_services['DistributorService']['service_type_id'],
                $distributors_services['DistributorService']['start_date'],
                $distributors_services['DistributorService']['end_date'],
                $distributors_services['DistributorService']['modification_date'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_software_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_software_create_data();
        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_SOFTWARE_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'distributor_id',
            'software_id',
            'software_type_id',
            'supplier_id',
            'software_manufacture_id',
            'start_date',
            'modification_date',
            'end_date',
            'version',
            'username',
            'password',
        );
        fputcsv($file, $table);
        foreach ($data as $distributors_software) {
            $fila = array(
                $distributors_software['DistributorSoftware']['id'],
                $distributors_software['DistributorSoftware']['distributor_id'],
                $distributors_software['DistributorSoftware']['software_id'],
                $distributors_software['DistributorSoftware']['software_type_id'],
                $distributors_software['DistributorSoftware']['supplier_id'],
                $distributors_software['DistributorSoftware']['software_manufacture_id'],
                $distributors_software['DistributorSoftware']['start_date'],
                $distributors_software['DistributorSoftware']['modification_date'],
                $distributors_software['DistributorSoftware']['end_date'],
                $distributors_software['DistributorSoftware']['version'],
                $distributors_software['DistributorSoftware']['username'],
                $distributors_software['DistributorSoftware']['password'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function distributors_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->distributors_types_create_data();

        $filename = date("N") . ConstantesFtp::DISTRIBUTORS_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function employee_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->employee_types_create_data();

        $filename = date("N") . ConstantesFtp::EMPLOYEE_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $employee_types) {
            $fila = array(
                $employee_types['EmployeeType']['id'],
                $employee_types['EmployeeType']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function enquiries_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->enquiries_create_data();

        $filename = date("N") . ConstantesFtp::ENQUIRIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'network_id',
            'garage_id',
            'name',
            'email',
            'description',
            'creation_date',
            'answer',
            'answered',
            'date_answered',
            'plate',
            'vin',
            'brand',
            'model',
            'version',
            'mot_exp_date',
            'fuel',
            'registered_on',
            'mileage',
            'work_id',
            'garage_url',
            'phone',
            'marketing_acceptance',
        );
        fputcsv($file, $table);
        foreach ($data as $enquiries) {
            $fila = array(
                $enquiries['Enquiry']['id'],
                $enquiries['Enquiry']['network_id'],
                $enquiries['Enquiry']['garage_id'],
                $enquiries['Enquiry']['name'],
                $enquiries['Enquiry']['email'],
                str_replace(array("\r", "\n"), ' ', $enquiries['Enquiry']['description']),
                $enquiries['Enquiry']['creation_date'],
                str_replace(array("\r", "\n"), ' ', $enquiries['Enquiry']['answer']),
                $enquiries['Enquiry']['answered'],
                $enquiries['Enquiry']['date_answered'],
                $enquiries['Enquiry']['plate'] = Texto::encryptDecryptText($enquiries['Enquiry']['plate'], false),
                $enquiries['Enquiry']['vin'],
                $enquiries['Enquiry']['brand'],
                $enquiries['Enquiry']['model'],
                $enquiries['Enquiry']['version'],
                $enquiries['Enquiry']['mot_exp_date'],
                $enquiries['Enquiry']['fuel'],
                $enquiries['Enquiry']['registered_on'],
                $enquiries['Enquiry']['mileage'],
                $enquiries['Enquiry']['work_id'],
                $enquiries['Enquiry']['garage_url'],
                $enquiries['Enquiry']['phone'],
                $enquiries['Enquiry']['marketing_acceptance'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function equipments_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->equipments_create_data();

        $filename = date("N") . ConstantesFtp::EQUIPMENTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $equipments) {
            $fila = array(
                $equipments['Equipment']['id'],
                $equipments['Equipment']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function equipments_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->equipments_types_create_data();

        $filename = date("N") . ConstantesFtp::EQUIPMENTS_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $equipments_types) {
            $fila = array(
                $equipments_types['EquipmentType']['id'],
                $equipments_types['EquipmentType']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function facilities_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->facilities_create_data();

        $filename = date("N") . ConstantesFtp::FACILITIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $facilities) {
            $fila = array(
                $facilities['Facility']['id'],
                $facilities['Facility']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function fluids_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->fluids_create_data();

        $filename = date("N") . ConstantesFtp::FLUID_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'network_id',
            'code',
            'name',
            'price',
            'parent_code',
            'has_advanced_settings',
            'active'
        );
        fputcsv($file, $table);
        foreach ($data as $fluid) {
            $fila = array(
                $fluid['Fluid']['id'],
                $fluid['Fluid']['network_id'],
                $fluid['Fluid']['code'],
                $fluid['Fluid']['name_' . __l()],
                $fluid['Fluid']['price'],
                $fluid['Fluid']['parent_code'],
                $fluid['Fluid']['has_advanced_settings'],
                $fluid['Fluid']['active']
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'guid',
            'g_number_id',
            'ref_code',
            'business_name',
            'name',
            'slug',
            'email',
            'comment',
            'client_type',
            'VAT_code',
            'is_cv',
            'siret',
            'detax_code',
            'sales_area_id',
            'insurance_agreement_id',
            'leaving_date',
            'reason_leaving_date',
            'status',
            'affiliation_assembly',
            'documents_legal',
            'diesel_liability',
            'turnover_id',
            'flat_rate',
            'address1',
            'address2',
            'address3',
            'address4',
            'latitude',
            'longitude',
            'town',
            'province_id',
            'postcode',
            'phone',
            'mobile',
            'phone_international',
            'fax',
            'web',
            'service_24h_phone',
            'monday_open_1',
            'monday_closed_1',
            'monday_open_2',
            'monday_closed_2',
            'tuesday_open_1',
            'tuesday_closed_1',
            'tuesday_open_2',
            'tuesday_closed_2',
            'wednesday_open_1',
            'wednesday_closed_1',
            'wednesday_open_2',
            'wednesday_closed_2',
            'thursday_open_1',
            'thursday_closed_1',
            'thursday_open_2',
            'thursday_closed_2',
            'friday_open_1',
            'friday_closed_1',
            'friday_open_2',
            'friday_closed_2',
            'saturday_open_1',
            'saturday_closed_1',
            'saturday_open_2',
            'saturday_closed_2',
            'sunday_open_1',
            'sunday_closed_1',
            'sunday_open_2',
            'sunday_closed_2',
            'visit_frequency',
            'visit_monday',
            'visit_tuesday',
            'visit_wednesday',
            'visit_thursday',
            'visit_friday',
            'last_visit',
            'ramps',
            'MOT_bays',
            'technician',
            'spend_this_month',
            'spend_last_month',
            'spend_12_month',
            'spend_projected',
            'foundation_year',
            'lead_source',
            'marketing_email',
            'interests',
            'user_id',
            'creation_date',
            'modification_date',
            'fleet_work_direction',
            'fleet_mot',
            'fleet_labour_rate',
            'long_life_oil_price_b2b',
            'standard_oil_price_b2b',
            'collection_delivery_b2b',
            'retail_mot',
            'retail_labour_rate',
            'long_life_oil_price_b2c',
            'standard_oil_price_b2c',
            'collection_delivery_b2c',
            'ev_charge_points',
            'kwh_charging_retail_price',
            'kwh_charging_fleet_price',
            'ev_ppe_audited_date',
            'city_id',
            'courtesy_car',
            'manually_created',
            'repairmaintenance',
            'campaign_entries',
        );
        fputcsv($file, $table);
        foreach ($data as $garages) {
            $fila = array(
                $garages['Garage']['id'],
                $garages['Garage']['guid'],
                $garages['Garage']['g_number_id'],
                $garages['Garage']['ref_code'],
                $garages['Garage']['business_name'],
                $garages['Garage']['name'],
                $garages['Garage']['slug'],
                $garages['Garage']['email'],
                $garages['Garage']['comment'],
                $garages['Garage']['client_type'],
                $garages['Garage']['VAT_code'],
                $garages['Garage']['is_cv'],
                $garages['Garage']['siret'],
                $garages['Garage']['detax_code'],
                $garages['Garage']['sales_area_id'],
                $garages['Garage']['insurance_agreement_id'],
                $garages['Garage']['leaving_date'],
                $garages['Garage']['reason_leaving_date'],
                $garages['Garage']['status'],
                $garages['Garage']['affiliation_assembly'],
                $garages['Garage']['documents_legal'],
                $garages['Garage']['diesel_liability'],
                $garages['Garage']['turnover_id'],
                $garages['Garage']['flat_rate'],
                $garages['Garage']['address1'],
                $garages['Garage']['address2'],
                $garages['Garage']['address3'],
                $garages['Garage']['address4'],
                $garages['Garage']['latitude'],
                $garages['Garage']['longitude'],
                $garages['Garage']['town'],
                $garages['Garage']['province_id'],
                $garages['Garage']['postcode'],
                $garages['Garage']['phone'],
                $garages['Garage']['mobile'],
                $garages['Garage']['phone_international'],
                $garages['Garage']['fax'],
                $garages['Garage']['web'],
                $garages['Garage']['service_24h_phone'],
                $garages['Garage']['monday_open_1'],
                $garages['Garage']['monday_closed_1'],
                $garages['Garage']['monday_open_2'],
                $garages['Garage']['monday_closed_2'],
                $garages['Garage']['tuesday_open_1'],
                $garages['Garage']['tuesday_closed_1'],
                $garages['Garage']['tuesday_open_2'],
                $garages['Garage']['tuesday_closed_2'],
                $garages['Garage']['wednesday_open_1'],
                $garages['Garage']['wednesday_closed_1'],
                $garages['Garage']['wednesday_open_2'],
                $garages['Garage']['wednesday_closed_2'],
                $garages['Garage']['thursday_open_1'],
                $garages['Garage']['thursday_closed_1'],
                $garages['Garage']['thursday_open_2'],
                $garages['Garage']['thursday_closed_2'],
                $garages['Garage']['friday_open_1'],
                $garages['Garage']['friday_closed_1'],
                $garages['Garage']['friday_open_2'],
                $garages['Garage']['friday_closed_2'],
                $garages['Garage']['saturday_open_1'],
                $garages['Garage']['saturday_closed_1'],
                $garages['Garage']['saturday_open_2'],
                $garages['Garage']['saturday_closed_2'],
                $garages['Garage']['sunday_open_1'],
                $garages['Garage']['sunday_closed_1'],
                $garages['Garage']['sunday_open_2'],
                $garages['Garage']['sunday_closed_2'],
                $garages['Garage']['visit_frequency'],
                $garages['Garage']['visit_monday'],
                $garages['Garage']['visit_tuesday'],
                $garages['Garage']['visit_wednesday'],
                $garages['Garage']['visit_thursday'],
                $garages['Garage']['visit_friday'],
                $garages['Garage']['last_visit'],
                $garages['Garage']['ramps'],
                $garages['Garage']['MOT_bays'],
                $garages['Garage']['technician'],
                $garages['Garage']['spend_this_month'],
                $garages['Garage']['spend_last_month'],
                $garages['Garage']['spend_12_month'],
                $garages['Garage']['spend_projected'],
                $garages['Garage']['foundation_year'],
                $garages['Garage']['lead_source'],
                $garages['Garage']['marketing_email'],
                $garages['Garage']['interests'],
                $garages['Garage']['user_id'],
                $garages['Garage']['creation_date'],
                $garages['Garage']['modification_date'],
                $garages['Garage']['fleet_work_direction'],
                $garages['Garage']['fleet_mot'],
                $garages['Garage']['fleet_labour_rate'],
                $garages['Garage']['long_life_oil_price_b2b'],
                $garages['Garage']['standard_oil_price_b2b'],
                $garages['Garage']['collection_delivery_b2b'],
                $garages['Garage']['retail_mot'],
                $garages['Garage']['retail_labour_rate'],
                $garages['Garage']['long_life_oil_price_b2c'],
                $garages['Garage']['standard_oil_price_b2c'],
                $garages['Garage']['collection_delivery_b2c'],
                $garages['Garage']['ev_charge_points'],
                $garages['Garage']['kwh_charging_retail_price'],
                $garages['Garage']['kwh_charging_fleet_price'],
                $garages['Garage']['ev_ppe_audited_date'],
                $garages['Garage']['city_id'],
                $garages['Garage']['courtesy_car'],
                $garages['Garage']['manually_created'],
                $garages['Garage']['repairmaintenance'],
                $garages['Garage']['campaign_entries'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_agreements_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_agreements_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_AGREEMENTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'parent_acct',
            'fleet_agreement',
            'fleet_reference',
            'agreement_code',
            'garage_ref',
            'agreement_id',
            'creation_date',
            'contract_sent_date',
            'contract_received_date',
            'contract_start_date',
            'contract_end_date',
            'date_on_hold',
            'reason_on_hold',
            'reason_leaving',
            'leaving_date',
            'status',
            'reason_leaving_id',
            'agreement_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_agreements) {
            $fila = array(
                $garages_agreements['GarageAgreement']['id'],
                $garages_agreements['GarageAgreement']['garage_id'],
                $garages_agreements['GarageAgreement']['parent_acct'],
                $garages_agreements['GarageAgreement']['fleet_agreement'],
                $garages_agreements['GarageAgreement']['fleet_reference'],
                $garages_agreements['GarageAgreement']['agreement_code'],
                $garages_agreements['GarageAgreement']['garage_ref'],
                $garages_agreements['GarageAgreement']['agreement_id'],
                $garages_agreements['GarageAgreement']['creation_date'],
                $garages_agreements['GarageAgreement']['contract_sent_date'],
                $garages_agreements['GarageAgreement']['contract_received_date'],
                $garages_agreements['GarageAgreement']['contract_start_date'],
                $garages_agreements['GarageAgreement']['contract_end_date'],
                $garages_agreements['GarageAgreement']['date_on_hold'],
                $garages_agreements['GarageAgreement']['reason_on_hold'],
                $garages_agreements['GarageAgreement']['reason_leaving'],
                $garages_agreements['GarageAgreement']['leaving_date'],
                $garages_agreements['GarageAgreement']['status'],
                $garages_agreements['GarageAgreement']['reason_leaving_id'],
                $garages_agreements['GarageAgreement']['agreement_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_b2b_postcodes_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_b2b_postcodes_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_B2B_POSTCODES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'postcode_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_b2b_postcodes) {
            $fila = array(
                $garages_b2b_postcodes['GarageB2bPostcode']['id'],
                $garages_b2b_postcodes['GarageB2bPostcode']['garage_id'],
                $garages_b2b_postcodes['GarageB2bPostcode']['postcode_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_b2c_postcodes_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_b2c_postcodes_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_B2C_POSTCODES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'postcode_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_b2c_postcodes) {
            $fila = array(
                $garages_b2c_postcodes['GarageB2cPostcode']['id'],
                $garages_b2c_postcodes['GarageB2cPostcode']['garage_id'],
                $garages_b2c_postcodes['GarageB2cPostcode']['postcode_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_brands_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_brands_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_BRANDS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'brand_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_brands) {
            $fila = array(
                $garages_brands['GarageBrand']['id'],
                $garages_brands['GarageBrand']['garage_id'],
                $garages_brands['GarageBrand']['brand_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_campaign_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_campaign_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_CAMPAIGN_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'garage_campaign_id',
            'number',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_campaign) {
            $fila = array(
                $garages_campaign['GarageCampaign']['id'],
                $garages_campaign['GarageCampaign']['garage_id'],
                $garages_campaign['GarageCampaign']['garage_campaign_id'],
                $garages_campaign['GarageCampaign']['number'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_comments_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_comments_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_COMMENTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'body',
            'creation_date',
            'user_id',
            'created_by_name',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_comments) {
            $fila = array(
                $garages_comments['GarageComment']['id'],
                $garages_comments['GarageComment']['garage_id'],
                $garages_comments['GarageComment']['body'],
                $garages_comments['GarageComment']['creation_date'],
                $garages_comments['GarageComment']['user_id'],
                $garages_comments['GarageComment']['created_by_name'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_contacts_bdm_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_contacts_bdm_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_CONTACTS_BDM_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'contact_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_contacts_bdm) {
            $fila = array(
                $garages_contacts_bdm['GarageContactBdm']['id'],
                $garages_contacts_bdm['GarageContactBdm']['garage_id'],
                $garages_contacts_bdm['GarageContactBdm']['contact_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_contacts_general_branch_manager_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_contacts_general_branch_manager_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_CONTACTS_GENERAL_BRANCH_MANAGER_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'contact_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_contacts_general_branch_manager) {
            $fila = array(
                $garages_contacts_general_branch_manager['GarageContactGeneralBranchManager']['id'],
                $garages_contacts_general_branch_manager['GarageContactGeneralBranchManager']['garage_id'],
                $garages_contacts_general_branch_manager['GarageContactGeneralBranchManager']['contact_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_contacts_lists_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_contacts_lists_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_CONTACTS_LISTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'contact_list_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_contacts_lists) {
            $fila = array(
                $garages_contacts_lists['GarageContactList']['id'],
                $garages_contacts_lists['GarageContactList']['garage_id'],
                $garages_contacts_lists['GarageContactList']['contact_list_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_contacts_staff_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_contacts_staff_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_CONTACTS_STAFF_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'contact_id',
            'interest',
            'priority',
            'main_contact',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_contacts_staff) {
            $fila = array(
                $garages_contacts_staff['GarageContactStaff']['id'],
                $garages_contacts_staff['GarageContactStaff']['garage_id'],
                $garages_contacts_staff['GarageContactStaff']['contact_id'],
                $garages_contacts_staff['GarageContactStaff']['interest'],
                $garages_contacts_staff['GarageContactStaff']['priority'],
                $garages_contacts_staff['GarageContactStaff']['main_contact'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_courtesy_car_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_courtesy_car_types_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_COURTESY_CAR_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'courtesy_car_type_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_courtesy_car_types) {
            $fila = array(
                $garages_courtesy_car_types['GarageCourtesyCarType']['id'],
                $garages_courtesy_car_types['GarageCourtesyCarType']['garage_id'],
                $garages_courtesy_car_types['GarageCourtesyCarType']['courtesy_car_type_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_customers_activities_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_customers_activities_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_CUSTOMERS_ACTIVITIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'customer_activity_id',
            'order',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_customers_activities) {
            $fila = array(
                $garages_customers_activities['GarageCustomerActivity']['id'],
                $garages_customers_activities['GarageCustomerActivity']['garage_id'],
                $garages_customers_activities['GarageCustomerActivity']['customer_activity_id'],
                $garages_customers_activities['GarageCustomerActivity']['order'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_distributors_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_distributors_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_DISTRIBUTORS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'distributor_id',
            'principal',
            'order',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_distributors) {
            $fila = array(
                $garages_distributors['GarageDistributor']['id'],
                $garages_distributors['GarageDistributor']['garage_id'],
                $garages_distributors['GarageDistributor']['distributor_id'],
                $garages_distributors['GarageDistributor']['principal'],
                $garages_distributors['GarageDistributor']['order'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_distributors_shortcuts_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_distributors_shortcuts_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_DISTRIBUTORS_SHORTCUTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'distributor_id',
            'shortcut_id',
            'network_id',
            'user_id',
            'parameter_value_1',
            'parameter_value_2',
            'fav',
            'creation_date',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_distributors_shortcuts) {
            $fila = array(
                $garages_distributors_shortcuts['GarageDistributorShortcut']['id'],
                $garages_distributors_shortcuts['GarageDistributorShortcut']['garage_id'],
                $garages_distributors_shortcuts['GarageDistributorShortcut']['distributor_id'],
                $garages_distributors_shortcuts['GarageDistributorShortcut']['shortcut_id'],
                $garages_distributors_shortcuts['GarageDistributorShortcut']['network_id'],
                $garages_distributors_shortcuts['GarageDistributorShortcut']['user_id'],
                $garages_distributors_shortcuts['GarageDistributorShortcut']['parameter_value_1'],
                $garages_distributors_shortcuts['GarageDistributorShortcut']['parameter_value_2'],
                $garages_distributors_shortcuts['GarageDistributorShortcut']['fav'],
                $garages_distributors_shortcuts['GarageDistributorShortcut']['creation_date'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_employees_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_employees_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_EMPLOYEES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'employee_type_id',
            'number',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_employees) {
            $fila = array(
                $garages_employees['GarageEmployee']['id'],
                $garages_employees['GarageEmployee']['garage_id'],
                $garages_employees['GarageEmployee']['employee_type_id'],
                $garages_employees['GarageEmployee']['number'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_equipments_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_equipments_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_EQUIPMENTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'equipment_id',
            'equipment_type_id',
            'supplier_id',
            'brand_id',
            'billing_schedule_id',
            'amount',
            'member_pay',
            'garage_pay',
            'billed_by_aag',
            'start_date',
            'end_date',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_equipments) {
            $fila = array(
                $garages_equipments['GarageEquipment']['id'],
                $garages_equipments['GarageEquipment']['garage_id'],
                $garages_equipments['GarageEquipment']['equipment_id'],
                $garages_equipments['GarageEquipment']['equipment_type_id'],
                $garages_equipments['GarageEquipment']['supplier_id'],
                $garages_equipments['GarageEquipment']['brand_id'],
                $garages_equipments['GarageEquipment']['billing_schedule_id'],
                $garages_equipments['GarageEquipment']['amount'],
                $garages_equipments['GarageEquipment']['member_pay'],
                $garages_equipments['GarageEquipment']['garage_pay'],
                $garages_equipments['GarageEquipment']['billed_by_aag'],
                $garages_equipments['GarageEquipment']['start_date'],
                $garages_equipments['GarageEquipment']['end_date'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_facilities_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_facilities_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_FACILITIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'facility_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_facilities) {
            $fila = array(
                $garages_facilities['GarageFacility']['id'],
                $garages_facilities['GarageFacility']['garage_id'],
                $garages_facilities['GarageFacility']['facility_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_figures_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_figures_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_FIGURES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'customer_no',
            'figures',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function garages_figures_details_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_figures_details_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_FIGURES_DETAILS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'customer_no',
            'figures_details',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function garages_files_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_files_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_FILES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'creation_date',
            'file',
            'type',
            'ext',
            'source_name',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_files) {
            $fila = array(
                $garages_files['GarageFile']['id'],
                $garages_files['GarageFile']['garage_id'],
                $garages_files['GarageFile']['creation_date'],
                $garages_files['GarageFile']['file'],
                $garages_files['GarageFile']['type'],
                $garages_files['GarageFile']['ext'],
                $garages_files['GarageFile']['source_name'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_images_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_images_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_IMAGES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'creation_date',
            'file',
            'type',
            'ext',
            'source_name',
            'principal',
            'web',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_images) {
            $fila = array(
                $garages_images['GarageImage']['id'],
                $garages_images['GarageImage']['garage_id'],
                $garages_images['GarageImage']['creation_date'],
                $garages_images['GarageImage']['file'],
                $garages_images['GarageImage']['type'],
                $garages_images['GarageImage']['ext'],
                $garages_images['GarageImage']['source_name'],
                $garages_images['GarageImage']['principal'],
                $garages_images['GarageImage']['web'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_kpis_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_kpis_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_KPIS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'customer_no',
            'kpis',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function garages_networks_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'network_id',
            'trading_group_id',
            'supplier_id',
            'reason_leaving_id',
            'reason_hold_id',
            'network_contract_type_id',
            'garage_number',
            'contract_sent_date',
            'contract_received_date',
            'contract_start_date',
            'date_on_hold',
            'reason_on_hold',
            'contract_end_date',
            'reason_leaving',
            'leaving_date',
            'status',
            'last',
            'dd_active',
            'annex_detail_id',
            'creation_date',
            'modification_date',
            'credit',
            'default_credit',
            'quoting_active',
            'enquiries_active',
            'about',
            'monday_planner_max_1',
            'monday_planner_max_2',
            'tuesday_planner_max_1',
            'tuesday_planner_max_2',
            'wednesday_planner_max_1',
            'wednesday_planner_max_2',
            'thursday_planner_max_1',
            'thursday_planner_max_2',
            'friday_planner_max_1',
            'friday_planner_max_2',
            'saturday_planner_max_1',
            'saturday_planner_max_2',
            'sunday_planner_max_1',
            'sunday_planner_max_2',
            'monday_planner_open_1',
            'monday_planner_closed_1',
            'monday_planner_open_2',
            'monday_planner_closed_2',
            'tuesday_planner_open_1',
            'tuesday_planner_closed_1',
            'tuesday_planner_open_2',
            'tuesday_planner_closed_2',
            'wednesday_planner_open_1',
            'wednesday_planner_closed_1',
            'wednesday_planner_open_2',
            'wednesday_planner_closed_2',
            'thursday_planner_open_1',
            'thursday_planner_closed_1',
            'thursday_planner_open_2',
            'thursday_planner_closed_2',
            'friday_planner_open_1',
            'friday_planner_closed_1',
            'friday_planner_open_2',
            'friday_planner_closed_2',
            'saturday_planner_open_1',
            'saturday_planner_closed_1',
            'saturday_planner_open_2',
            'saturday_planner_closed_2',
            'sunday_planner_open_1',
            'sunday_planner_closed_1',
            'sunday_planner_open_2',
            'sunday_planner_closed_2',
            'booking_days_min_from',
            'booking_days_max_to',
            'code',
            'sap_code',
            'recommended',
            'location_id',
            'rating',
            'reviews_number',
            // 'reviews_info',
            'usp1',
            'usp2',
            'usp3',
            'dealer_discount',
            'dealer_surcharge',
            'dealer_markup',
            'labour_hourly_price',
            'labour_hourly_price_electric_vehicles',
            'current_charge',
            'member_pays',
            'garage_pays',
            'imported',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks) {
            $fila = array(
                $garages_networks['GarageNetwork']['id'],
                $garages_networks['GarageNetwork']['garage_id'],
                $garages_networks['GarageNetwork']['network_id'],
                $garages_networks['GarageNetwork']['trading_group_id'],
                $garages_networks['GarageNetwork']['supplier_id'],
                $garages_networks['GarageNetwork']['reason_leaving_id'],
                $garages_networks['GarageNetwork']['reason_hold_id'],
                $garages_networks['GarageNetwork']['network_contract_type_id'],
                $garages_networks['GarageNetwork']['garage_number'],
                $garages_networks['GarageNetwork']['contract_sent_date'],
                $garages_networks['GarageNetwork']['contract_received_date'],
                $garages_networks['GarageNetwork']['contract_start_date'],
                $garages_networks['GarageNetwork']['date_on_hold'],
                $garages_networks['GarageNetwork']['reason_on_hold'],
                $garages_networks['GarageNetwork']['contract_end_date'],
                $garages_networks['GarageNetwork']['reason_leaving'],
                $garages_networks['GarageNetwork']['leaving_date'],
                $garages_networks['GarageNetwork']['status'],
                $garages_networks['GarageNetwork']['last'],
                $garages_networks['GarageNetwork']['dd_active'],
                $garages_networks['GarageNetwork']['annex_detail_id'],
                $garages_networks['GarageNetwork']['creation_date'],
                $garages_networks['GarageNetwork']['modification_date'],
                $garages_networks['GarageNetwork']['credit'],
                $garages_networks['GarageNetwork']['default_credit'],
                $garages_networks['GarageNetwork']['quoting_active'],
                $garages_networks['GarageNetwork']['enquiries_active'],
                $garages_networks['GarageNetwork']['about'],
                $garages_networks['GarageNetwork']['monday_planner_max_1'],
                $garages_networks['GarageNetwork']['monday_planner_max_2'],
                $garages_networks['GarageNetwork']['tuesday_planner_max_1'],
                $garages_networks['GarageNetwork']['tuesday_planner_max_2'],
                $garages_networks['GarageNetwork']['wednesday_planner_max_1'],
                $garages_networks['GarageNetwork']['wednesday_planner_max_2'],
                $garages_networks['GarageNetwork']['thursday_planner_max_1'],
                $garages_networks['GarageNetwork']['thursday_planner_max_2'],
                $garages_networks['GarageNetwork']['friday_planner_max_1'],
                $garages_networks['GarageNetwork']['friday_planner_max_2'],
                $garages_networks['GarageNetwork']['saturday_planner_max_1'],
                $garages_networks['GarageNetwork']['saturday_planner_max_2'],
                $garages_networks['GarageNetwork']['sunday_planner_max_1'],
                $garages_networks['GarageNetwork']['sunday_planner_max_2'],
                $garages_networks['GarageNetwork']['monday_planner_open_1'],
                $garages_networks['GarageNetwork']['monday_planner_closed_1'],
                $garages_networks['GarageNetwork']['monday_planner_open_2'],
                $garages_networks['GarageNetwork']['monday_planner_closed_2'],
                $garages_networks['GarageNetwork']['tuesday_planner_open_1'],
                $garages_networks['GarageNetwork']['tuesday_planner_closed_1'],
                $garages_networks['GarageNetwork']['tuesday_planner_open_2'],
                $garages_networks['GarageNetwork']['tuesday_planner_closed_2'],
                $garages_networks['GarageNetwork']['wednesday_planner_open_1'],
                $garages_networks['GarageNetwork']['wednesday_planner_closed_1'],
                $garages_networks['GarageNetwork']['wednesday_planner_open_2'],
                $garages_networks['GarageNetwork']['wednesday_planner_closed_2'],
                $garages_networks['GarageNetwork']['thursday_planner_open_1'],
                $garages_networks['GarageNetwork']['thursday_planner_closed_1'],
                $garages_networks['GarageNetwork']['thursday_planner_open_2'],
                $garages_networks['GarageNetwork']['thursday_planner_closed_2'],
                $garages_networks['GarageNetwork']['friday_planner_open_1'],
                $garages_networks['GarageNetwork']['friday_planner_closed_1'],
                $garages_networks['GarageNetwork']['friday_planner_open_2'],
                $garages_networks['GarageNetwork']['friday_planner_closed_2'],
                $garages_networks['GarageNetwork']['saturday_planner_open_1'],
                $garages_networks['GarageNetwork']['saturday_planner_closed_1'],
                $garages_networks['GarageNetwork']['saturday_planner_open_2'],
                $garages_networks['GarageNetwork']['saturday_planner_closed_2'],
                $garages_networks['GarageNetwork']['sunday_planner_open_1'],
                $garages_networks['GarageNetwork']['sunday_planner_closed_1'],
                $garages_networks['GarageNetwork']['sunday_planner_open_2'],
                $garages_networks['GarageNetwork']['sunday_planner_closed_2'],
                $garages_networks['GarageNetwork']['booking_days_min_from'],
                $garages_networks['GarageNetwork']['booking_days_max_to'],
                $garages_networks['GarageNetwork']['code'],
                $garages_networks['GarageNetwork']['sap_code'],
                $garages_networks['GarageNetwork']['recommended'],
                $garages_networks['GarageNetwork']['location_id'],
                $garages_networks['GarageNetwork']['rating'],
                $garages_networks['GarageNetwork']['reviews_number'],
                // $garages_networks['GarageNetwork']['reviews_info'],
                $garages_networks['GarageNetwork']['usp1'],
                $garages_networks['GarageNetwork']['usp2'],
                $garages_networks['GarageNetwork']['usp3'],
                $garages_networks['GarageNetwork']['dealer_discount'],
                $garages_networks['GarageNetwork']['dealer_surcharge'],
                $garages_networks['GarageNetwork']['dealer_markup'],
                $garages_networks['GarageNetwork']['labour_hourly_price'],
                $garages_networks['GarageNetwork']['labour_hourly_price_electric_vehicles'],
                $garages_networks['GarageNetwork']['current_charge'],
                $garages_networks['GarageNetwork']['member_pays'],
                $garages_networks['GarageNetwork']['garage_pays'],
                $garages_networks['GarageNetwork']['imported'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_contacts_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_contacts_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_CONTACTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'contact_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_contacts) {
            $fila = array(
                $garages_networks_contacts['GarageNetworkContact']['id'],
                $garages_networks_contacts['GarageNetworkContact']['garage_network_id'],
                $garages_networks_contacts['GarageNetworkContact']['contact_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_fluids_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_fluids_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_FLUIDS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'fluid_id',
            'price',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_fluids) {
            $fila = array(
                $garages_networks_fluids['GarageNetworkFluid']['id'],
                $garages_networks_fluids['GarageNetworkFluid']['garage_network_id'],
                $garages_networks_fluids['GarageNetworkFluid']['fluid_id'],
                $garages_networks_fluids['GarageNetworkFluid']['price'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_genarts_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_genarts_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_GENARTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'genart_id',
            'discount',
            'markup',
            'surcharge',
            'is_labour_price'
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_genarts) {
            $fila = array(
                $garages_networks_genarts['GarageNetworkGenart']['id'],
                $garages_networks_genarts['GarageNetworkGenart']['garage_network_id'],
                $garages_networks_genarts['GarageNetworkGenart']['genart_id'],
                $garages_networks_genarts['GarageNetworkGenart']['discount'],
                $garages_networks_genarts['GarageNetworkGenart']['markup'],
                $garages_networks_genarts['GarageNetworkGenart']['surcharge'],
                $garages_networks_genarts['GarageNetworkGenart']['is_labour_price'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_genarts_families_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_genarts_families_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_GENARTS_FAMILIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'genart_family_id',
            'discount',
            'markup',
            'surcharge'
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_genarts_families) {
            $fila = array(
                $garages_networks_genarts_families['GarageNetworkGenartFamily']['id'],
                $garages_networks_genarts_families['GarageNetworkGenartFamily']['garage_network_id'],
                $garages_networks_genarts_families['GarageNetworkGenartFamily']['genart_family_id'],
                $garages_networks_genarts_families['GarageNetworkGenartFamily']['discount'],
                $garages_networks_genarts_families['GarageNetworkGenartFamily']['markup'],
                $garages_networks_genarts_families['GarageNetworkGenartFamily']['surcharge']
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_genarts_master_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_genarts_master_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_GENARTS_MASTER_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'genart_master_id',
            'genart_master_labour_price'
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_genarts_master) {
            $fila = array(
                $garages_networks_genarts_master['GarageNetworkGenartMaster']['id'],
                $garages_networks_genarts_master['GarageNetworkGenartMaster']['garage_network_id'],
                $garages_networks_genarts_master['GarageNetworkGenartMaster']['genart_master_id'],
                $garages_networks_genarts_master['GarageNetworkGenartMaster']['genart_master_labour_price']
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_images_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_images_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_IMAGES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'creation_date',
            'file',
            'type',
            'ext',
            'source_name',
            'principal',
            'web',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_images) {
            $fila = array(
                $garages_networks_images['GarageNetworkImage']['id'],
                $garages_networks_images['GarageNetworkImage']['garage_network_id'],
                $garages_networks_images['GarageNetworkImage']['creation_date'],
                $garages_networks_images['GarageNetworkImage']['file'],
                $garages_networks_images['GarageNetworkImage']['type'],
                $garages_networks_images['GarageNetworkImage']['ext'],
                $garages_networks_images['GarageNetworkImage']['source_name'],
                $garages_networks_images['GarageNetworkImage']['principal'],
                $garages_networks_images['GarageNetworkImage']['web'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_services_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_services_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_SERVICES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'service_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_services) {
            $fila = array(
                $garages_networks_services['GarageNetworkService']['id'],
                $garages_networks_services['GarageNetworkService']['garage_network_id'],
                $garages_networks_services['GarageNetworkService']['service_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_services_drivers_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_services_drivers_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_SERVICES_DRIVERS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'service_driver_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_services_drivers) {
            $fila = array(
                $garages_networks_services_drivers['GarageNetworkServiceDriver']['id'],
                $garages_networks_services_drivers['GarageNetworkServiceDriver']['garage_network_id'],
                $garages_networks_services_drivers['GarageNetworkServiceDriver']['service_driver_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_vehicles_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_vehicles_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_VEHICLES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'vehicle_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_vehicles) {
            $fila = array(
                $garages_networks_vehicles['GarageNetworkVehicle']['id'],
                $garages_networks_vehicles['GarageNetworkVehicle']['garage_network_id'],
                $garages_networks_vehicles['GarageNetworkVehicle']['vehicle_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_vehicles_black_list_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_vehicles_black_list_create_data();


        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_VEHICLES_BLACK_LIST_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'vehicle_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_vehicles_black_list) {
            $fila = array(
                $garages_networks_vehicles_black_list['GarageNetworkVehicleBlackList']['id'],
                $garages_networks_vehicles_black_list['GarageNetworkVehicleBlackList']['garage_network_id'],
                $garages_networks_vehicles_black_list['GarageNetworkVehicleBlackList']['vehicle_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_vehicle_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_vehicle_types_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_VEHICLE_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'vehicle_type_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_vehicle_types) {
            $fila = array(
                $garages_networks_vehicle_types['GarageNetworkVehicleType']['id'],
                $garages_networks_vehicle_types['GarageNetworkVehicleType']['garage_network_id'],
                $garages_networks_vehicle_types['GarageNetworkVehicleType']['vehicle_type_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_works_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_works_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_WORKS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'work_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_works) {
            $fila = array(
                $garages_networks_works['GarageNetworkWork']['id'],
                $garages_networks_works['GarageNetworkWork']['garage_network_id'],
                $garages_networks_works['GarageNetworkWork']['work_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_works_labours_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_works_labours_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_WORKS_LABOURS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'work_id',
            'labour_hourly_price',
            'labour_hourly_price_electric_vehicles',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_works_labours) {
            $fila = array(
                $garages_networks_works_labours['GarageNetworkWorkLabour']['id'],
                $garages_networks_works_labours['GarageNetworkWorkLabour']['garage_network_id'],
                $garages_networks_works_labours['GarageNetworkWorkLabour']['work_id'],
                $garages_networks_works_labours['GarageNetworkWorkLabour']['labour_hourly_price'],
                $garages_networks_works_labours['GarageNetworkWorkLabour']['labour_hourly_price_electric_vehicles'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_networks_works_prices_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_networks_works_prices_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_NETWORKS_WORKS_PRICES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'work_id',
            'discount',
            'markup',
            'surcharge',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_networks_works_prices) {
            $fila = array(
                $garages_networks_works_prices['GarageNetworkWorkPrice']['id'],
                $garages_networks_works_prices['GarageNetworkWorkPrice']['garage_network_id'],
                $garages_networks_works_prices['GarageNetworkWorkPrice']['work_id'],
                $garages_networks_works_prices['GarageNetworkWorkPrice']['discount'],
                $garages_networks_works_prices['GarageNetworkWorkPrice']['markup'],
                $garages_networks_works_prices['GarageNetworkWorkPrice']['surcharge'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_oils_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_oils_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_OILS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'oil_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_oils) {
            $fila = array(
                $garages_oils['GarageOil']['id'],
                $garages_oils['GarageOil']['garage_id'],
                $garages_oils['GarageOil']['oil_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_products_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_products_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_PRODUCTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'order_id',
            'product_id',
            'quantity',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function garages_routes_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_routes_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_ROUTES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'route_id',
            'order',
            'start_time',
            'end_time',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_routes) {
            $fila = array(
                $garages_routes['GarageRoute']['id'],
                $garages_routes['GarageRoute']['garage_id'],
                $garages_routes['GarageRoute']['route_id'],
                $garages_routes['GarageRoute']['order'],
                $garages_routes['GarageRoute']['start_time'],
                $garages_routes['GarageRoute']['end_time'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_services_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_services_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_SERVICES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'service_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_services) {
            $fila = array(
                $garages_services['GarageService']['id'],
                $garages_services['GarageService']['garage_id'],
                $garages_services['GarageService']['service_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_software_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_software_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_SOFTWARE_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'software_id',
            'software_type_id',
            'supplier_id',
            'software_manufacture_id',
            'billing_schedule_id',
            'amount',
            'member_pay',
            'garage_pay',
            'billed_by_aag',
            'start_date',
            'end_date',
            'version',
            'username',
            'password',
            'number_subscription',
            'online_ordering',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_software) {
            $fila = array(
                $garages_software['GarageSoftware']['id'],
                $garages_software['GarageSoftware']['garage_id'],
                $garages_software['GarageSoftware']['software_id'],
                $garages_software['GarageSoftware']['software_type_id'],
                $garages_software['GarageSoftware']['supplier_id'],
                $garages_software['GarageSoftware']['software_manufacture_id'],
                $garages_software['GarageSoftware']['billing_schedule_id'],
                $garages_software['GarageSoftware']['amount'],
                $garages_software['GarageSoftware']['member_pay'],
                $garages_software['GarageSoftware']['garage_pay'],
                $garages_software['GarageSoftware']['billed_by_aag'],
                $garages_software['GarageSoftware']['start_date'],
                $garages_software['GarageSoftware']['end_date'],
                $garages_software['GarageSoftware']['version'],
                $garages_software['GarageSoftware']['username'],
                $garages_software['GarageSoftware']['password'],
                $garages_software['GarageSoftware']['number_subscription'],
                $garages_software['GarageSoftware']['online_ordering'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_specialist_makes_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_specialist_makes_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_SPECIALIST_MAKES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'vehicle_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_specialist_makes) {
            $fila = array(
                $garages_specialist_makes['GarageSpecialistMake']['id'],
                $garages_specialist_makes['GarageSpecialistMake']['garage_id'],
                $garages_specialist_makes['GarageSpecialistMake']['vehicle_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_statuses_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_statuses_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_STATUSES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function garages_values_adds_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_values_adds_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_VALUES_ADDS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'value_add_id',
            'start_date',
            'end_date',
            'version',
            'billing_schedule_id',
            'amount',
            'member_pay',
            'garage_pay',
            'billed_by_aag',
            'number_subscription',
            'online_ordering',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_values_adds) {
            $fila = array(
                $garages_values_adds['GarageValueAdd']['id'],
                $garages_values_adds['GarageValueAdd']['garage_id'],
                $garages_values_adds['GarageValueAdd']['value_add_id'],
                $garages_values_adds['GarageValueAdd']['start_date'],
                $garages_values_adds['GarageValueAdd']['end_date'],
                $garages_values_adds['GarageValueAdd']['version'],
                $garages_values_adds['GarageValueAdd']['billing_schedule_id'],
                $garages_values_adds['GarageValueAdd']['amount'],
                $garages_values_adds['GarageValueAdd']['member_pay'],
                $garages_values_adds['GarageValueAdd']['garage_pay'],
                $garages_values_adds['GarageValueAdd']['billed_by_aag'],
                $garages_values_adds['GarageValueAdd']['number_subscription'],
                $garages_values_adds['GarageValueAdd']['online_ordering'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_value_add_supplier_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_value_add_supplier_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_VALUE_ADD_SUPPLIER_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'value_add_supplier_id',
            'value_add_supplier_type_id',
            'to_date',
            'from_date',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_value_add_supplier) {
            $fila = array(
                $garages_value_add_supplier['GarageValueAddSupplier']['id'],
                $garages_value_add_supplier['GarageValueAddSupplier']['garage_id'],
                $garages_value_add_supplier['GarageValueAddSupplier']['value_add_supplier_id'],
                $garages_value_add_supplier['GarageValueAddSupplier']['value_add_supplier_type_id'],
                $garages_value_add_supplier['GarageValueAddSupplier']['to_date'],
                $garages_value_add_supplier['GarageValueAddSupplier']['from_date'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_vehicles_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_vehicles_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_VEHICLES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'vehicle_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_vehicles) {
            $fila = array(
                $garages_vehicles['GarageVehicle']['id'],
                $garages_vehicles['GarageVehicle']['garage_id'],
                $garages_vehicles['GarageVehicle']['vehicle_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_vehicle_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_vehicle_types_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_VEHICLE_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'vehicle_type_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_vehicle_types) {
            $fila = array(
                $garages_vehicle_types['GarageVehicleType']['id'],
                $garages_vehicle_types['GarageVehicleType']['garage_id'],
                $garages_vehicle_types['GarageVehicleType']['vehicle_type_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_visit_frequencies_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_visit_frequencies_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_VISIT_FREQUENCIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function garages_websites_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_websites_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_WEBSITES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'website_id',
            'url',
            'tagline',
            'description',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_websites) {
            $fila = array(
                $garages_websites['GarageWebsite']['id'],
                $garages_websites['GarageWebsite']['garage_id'],
                $garages_websites['GarageWebsite']['website_id'],
                $garages_websites['GarageWebsite']['url'],
                $garages_websites['GarageWebsite']['tagline'],
                $garages_websites['GarageWebsite']['description'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function garages_workshop_activities_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->garages_workshop_activities_create_data();

        $filename = date("N") . ConstantesFtp::GARAGES_WORKSHOP_ACTIVITIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'workshop_activity_id',
        );
        fputcsv($file, $table);
        foreach ($data as $garages_workshop_activities) {
            $fila = array(
                $garages_workshop_activities['GarageWorkshopActivity']['id'],
                $garages_workshop_activities['GarageWorkshopActivity']['garage_id'],
                $garages_workshop_activities['GarageWorkshopActivity']['workshop_activity_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function genarts_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->genarts_create_data();

        $filename = date("N") . ConstantesFtp::GENARTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'grouping_genart_id',
            'code',
            'name',
            'include_vat',
            'is_labour_time',
            'price_labour_time',
            'discount',
            'markup',
            'surcharge',
            'active'
        );
        fputcsv($file, $table);
        foreach ($data as $genart) {
            $fila = array(
                $genart['Genart']['id'],
                $genart['Genart']['grouping_genart_id'],
                $genart['Genart']['code'],
                $genart['Genart']['name_' . __l()],
                $genart['Genart']['include_vat'],
                $genart['Genart']['is_labour_time'],
                $genart['Genart']['price_labour_time'],
                $genart['Genart']['discount'],
                $genart['Genart']['markup'],
                $genart['Genart']['surcharge'],
                $genart['Genart']['active'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function genarts_families_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->genarts_families_create_data();

        $filename = date("N") . ConstantesFtp::GENARTS_FAMILIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'active',
            'network_id'
        );
        fputcsv($file, $table);
        foreach ($data as $genart_family) {
            $fila = array(
                $genart_family['GenartFamily']['id'],
                $genart_family['GenartFamily']['name_' . __l()],
                $genart_family['GenartFamily']['active'],
                $genart_family['GenartFamily']['network_id'],

            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function genarts_master_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->genarts_master_create_data();

        $filename = date("N") . ConstantesFtp::GENARTS_MASTER_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'code',
            'name',
            'include_vat',
            'is_labour_time',
            'price_labour_time',
            'min_labour_price',
            'max_labour_price',
            'slider_increment',
            'network_id',
            'genart_family_id',
            'active'
        );
        fputcsv($file, $table);
        foreach ($data as $genartMaster) {
            $fila = array(
                $genartMaster['GenartMaster']['id'],
                $genartMaster['GenartMaster']['code'],
                $genartMaster['GenartMaster']['name_' . __l()],
                $genartMaster['GenartMaster']['include_vat'],
                $genartMaster['GenartMaster']['is_labour_time'],
                $genartMaster['GenartMaster']['price_labour_time'],
                $genartMaster['GenartMaster']['min_labour_price'],
                $genartMaster['GenartMaster']['max_labour_price'],
                $genartMaster['GenartMaster']['slider_increment'],
                $genartMaster['GenartMaster']['network_id'],
                $genartMaster['GenartMaster']['genart_family_id'],
                $genartMaster['GenartMaster']['active'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function grouping_genarts_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->grouping_genarts_create_data();

        $filename = date("N") . ConstantesFtp::GROUPING_GENARTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'network_id',
            'code'
        );
        fputcsv($file, $table);
        foreach ($data as $genart) {
            $fila = array(
                $genart['GroupingGenart']['id'],
                $genart['GroupingGenart']['network_id'],
                $genart['GroupingGenart']['code']
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function hold_reason_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->hold_reason_types_create_data();

        $filename = date("N") . ConstantesFtp::HOLD_REASON_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $hold_reason_types) {
            $fila = array(
                $hold_reason_types['HoldReasonType']['id'],
                $hold_reason_types['HoldReasonType']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function leaving_reason_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->leaving_reason_types_create_data();

        $filename = date("N") . ConstantesFtp::LEAVING_REASON_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $leaving_reason_types) {
            $fila = array(
                $leaving_reason_types['LeavingReasonType']['id'],
                $leaving_reason_types['LeavingReasonType']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function lists_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->lists_create_data();

        $filename = date("N") . ConstantesFtp::LISTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function networks_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->networks_create_data();
        $filename = date("N") . ConstantesFtp::NETWORKS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'quoting_type_id',
            'quoting_type_name',
            'pricing_type_id',
            'pricing_type_name',
            'name',
            'image',
            'image_pin',
            'image_cluster',
            'web',
            'network_type',
            'primary_color',
            'primary_font_color',
            'primary_background_color',
            'secondary_color',
            'secondary_font_color',
            'secondary_background_color',
            'tertiary_color',
            'tertiary_font_color',
            'quaternary_color',
            'quaternary_font_color',
            'font_default_color',
            'menu_background_color',
            'creation_date',
            'labour_hourly_price',
            'labour_hourly_price_electric_vehicles',
            'min_labour_price',
            'max_labour_price',
            'slider_increment',
            'min_labour_price_ev',
            'max_labour_price_ev',
            'slider_increment_ev',
            'with_vat',
            'rating',
            'reviews_number',
            'reviews_info',
            'color_exito',
            'color_fallo',
            'color_informacion',
            'color_disabled',
            'internal',
            'ref_code',
            'credit',
            'date_restarting_credit',
            'training',
        );
        fputcsv($file, $table);
        foreach ($data as $networks) {
            $fila = array(
                $networks['Network']['id'],
                $networks['Network']['quoting_type_id'],
                $networks['QuotingType']['name'],
                $networks['Network']['pricing_type_id'],
                $networks['PricingType']['name'],
                $networks['Network']['name'],
                $networks['Network']['image'],
                $networks['Network']['image_pin'],
                $networks['Network']['image_cluster'],
                $networks['Network']['web'],
                $networks['Network']['network_type'],
                $networks['Network']['primary_color'],
                $networks['Network']['primary_font_color'],
                $networks['Network']['primary_background_color'],
                $networks['Network']['secondary_color'],
                $networks['Network']['secondary_font_color'],
                $networks['Network']['secondary_background_color'],
                $networks['Network']['tertiary_color'],
                $networks['Network']['tertiary_font_color'],
                $networks['Network']['quaternary_color'],
                $networks['Network']['quaternary_font_color'],
                $networks['Network']['font_default_color'],
                $networks['Network']['menu_background_color'],
                $networks['Network']['creation_date'],
                $networks['Network']['labour_hourly_price'],
                $networks['Network']['labour_hourly_price_electric_vehicles'],
                $networks['Network']['min_labour_price'],
                $networks['Network']['max_labour_price'],
                $networks['Network']['slider_increment'],
                $networks['Network']['slider_increment_ev'],
                $networks['Network']['min_labour_price_ev'],
                $networks['Network']['max_labour_price_ev'],
                $networks['Network']['with_vat'],
                $networks['Network']['rating'],
                $networks['Network']['reviews_number'],
                $networks['Network']['reviews_info'],
                $networks['Network']['color_exito'],
                $networks['Network']['color_fallo'],
                $networks['Network']['color_informacion'],
                $networks['Network']['color_disabled'],
                $networks['Network']['internal'],
                $networks['Network']['ref_code'],
                $networks['Network']['credit'],
                $networks['Network']['date_restarting_credit'],
                $networks['Network']['training'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function networks_contacts_bdm_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->networks_contacts_bdm_create_data();

        $filename = date("N") . ConstantesFtp::NETWORKS_CONTACTS_BDM_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'network_id',
            'contact_id',
            'distributor_id',
            'garage_id',
        );
        fputcsv($file, $table);
        foreach ($data as $networks_contacts_bdm) {
            $fila = array(
                $networks_contacts_bdm['NetworkContactBdm']['id'],
                $networks_contacts_bdm['NetworkContactBdm']['network_id'],
                $networks_contacts_bdm['NetworkContactBdm']['contact_id'],
                $networks_contacts_bdm['NetworkContactBdm']['distributor_id'],
                $networks_contacts_bdm['NetworkContactBdm']['garage_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function networks_contacts_lists_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->networks_contacts_lists_create_data();

        $filename = date("N") . ConstantesFtp::NETWORKS_CONTACTS_LISTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'network_id',
            'contact_list_id',
        );
        fputcsv($file, $table);
        foreach ($data as $networks_contacts_lists) {
            $fila = array(
                $networks_contacts_lists['NetworkContactList']['id'],
                $networks_contacts_lists['NetworkContactList']['network_id'],
                $networks_contacts_lists['NetworkContactList']['contact_list_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function networks_contract_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->networks_contract_types_create_data();

        $filename = date("N") . ConstantesFtp::NETWORKS_CONTRACT_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $networks_contract_types) {
            $fila = array(
                $networks_contract_types['NetworkContractType']['id'],
                $networks_contract_types['NetworkContractType']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function networks_statuses_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->networks_statuses_create_data();

        $filename = date("N") . ConstantesFtp::NETWORKS_STATUSES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function orders_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->orders_create_data();

        $filename = date("N") . ConstantesFtp::ORDERS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_id',
            'order_number',
            'invoice_date',
            'order_date',
            'completed_date',
            'invoce_number',
            'invoice_amount',
            'notes',
            'signage_sign_A_q',
            'signage_sign_B1_q',
            'signage_sign_B2_q',
            'signage_sign_C1_q',
            'signage_sign_C2_q',
            'signage_sign_C3_q',
            'signage_sign_D1_q',
            'signage_sign_D2_q',
            'signage_sign_bespoke_q',
            'rebranding_signage',
            'order_type_id',
        );
        fputcsv($file, $table);
        foreach ($data as $order) {
            $fila = array(
                $order['Order']['id'],
                $order['Order']['garage_id'],
                $order['Order']['order_number'],
                $order['Order']['invoice_date'],
                $order['Order']['order_date'],
                $order['Order']['completed_date'],
                $order['Order']['invoce_number'],
                $order['Order']['invoice_amount'],
                $order['Order']['notes'],
                $order['Order']['signage_sign_A_q'],
                $order['Order']['signage_sign_B1_q'],
                $order['Order']['signage_sign_B2_q'],
                $order['Order']['signage_sign_C1_q'],
                $order['Order']['signage_sign_C2_q'],
                $order['Order']['signage_sign_C3_q'],
                $order['Order']['signage_sign_D1_q'],
                $order['Order']['signage_sign_D2_q'],
                $order['Order']['signage_sign_bespoke_q'],
                $order['Order']['rebranding_signage'],
                $order['Order']['order_type_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function order_products_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->order_products_create_data();

        $filename = date("N") . ConstantesFtp::ORDER_PRODUCTS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $order_products) {
            $fila = array(
                $order_products['OrderProduct']['id'],
                $order_products['OrderProduct']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function order_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $this->order_types_create_data();

        $filename = date("N") . ConstantesFtp::ORDER_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function positions_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $this->positions_create_data();

        $filename = date("N") . ConstantesFtp::POSITIONS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'role_id',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function postcodes_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $this->postcodes_create_data();

        $filename = date("N") . ConstantesFtp::POSTCODES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function postcode_provinces_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->postcode_provinces_create_data();
        $filename = date("N") . ConstantesFtp::POSTCODE_PROVINCES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'postcode',
            'province_code',
            'province_id',
        );
        fputcsv($file, $table);
        foreach ($data as $postcode_provinces) {
            $fila = array(
                $postcode_provinces['PostcodeProvince']['id'],
                $postcode_provinces['PostcodeProvince']['postcode'],
                $postcode_provinces['PostcodeProvince']['province_code'],
                $postcode_provinces['PostcodeProvince']['province_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function provinces_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->provinces_create_data();

        $filename = date("N") . ConstantesFtp::PROVINCES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'province_code',
            'country_id',
        );
        fputcsv($file, $table);
        foreach ($data as $provinces) {
            $fila = array(
                $provinces['Province']['id'],
                $provinces['Province']['name'],
                $provinces['Province']['province_code'],
                $provinces['Province']['country_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function reasons_delegates_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->reasons_delegates_create_data();

        $filename = date("N") . ConstantesFtp::REASONS_DELEGATES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $reasons_delegates) {
            $fila = array(
                $reasons_delegates['ReasonDelegate']['id'],
                $reasons_delegates['ReasonDelegate']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function regions_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->regions_create_data();

        $filename = date("N") . ConstantesFtp::REGIONS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'code',
            'creation_date',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function roles_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->roles_create_data();

        $filename = date("N") . ConstantesFtp::ROLES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'role',
        );
        fputcsv($file, $table);
        foreach ($data as $services_drivers) {
            $fila = array(
                $services_drivers['Role']['id'],
                $services_drivers['Role']['name_' . __l()],
                $services_drivers['Role']['role'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function services_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->services_create_data();

        $filename = date("N") . ConstantesFtp::SERVICES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'international_code',
            'url',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function services_drivers_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->services_drivers_create_data();

        $filename = date("N") . ConstantesFtp::SERVICES_DRIVERS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'network_id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $services_drivers) {
            $fila = array(
                $services_drivers['ServiceDriver']['id'],
                $services_drivers['ServiceDriver']['network_id'],
                $services_drivers['ServiceDriver']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function services_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->services_types_create_data();

        $filename = date("N") . ConstantesFtp::SERVICES_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function software_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->software_create_data();

        $filename = date("N") . ConstantesFtp::SOFTWARE_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $software) {
            $fila = array(
                $software['Software']['id'],
                $software['Software']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function software_manufactures_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->software_manufactures_create_data();

        $filename = date("N") . ConstantesFtp::SOFTWARE_MANUFACTURES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $software_manufactures) {
            $fila = array(
                $software_manufactures['SoftwareManufacture']['id'],
                $software_manufactures['SoftwareManufacture']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function software_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->software_types_create_data();

        $filename = date("N") . ConstantesFtp::SOFTWARE_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $software_types) {
            $fila = array(
                $software_types['SoftwareType']['id'],
                $software_types['SoftwareType']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function tasks_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->tasks_data();
        $tasksStatus = ClassRegistry::init('TaskStatus');
        $tasksStatusList = $tasksStatus->search_list_all();

        $filename = date("N") . ConstantesFtp::TASKS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');

        $table = array(
            'id',
            'task',
            'body',
            'created_by',
            'assigned_to',
            'creation_date',
            'resolve_date',
            'deadline',
            'appointment_id',
            'status',
        );
        fputcsv($file, $table);
        foreach ($data as $tasks) {
            $fila = array(
                $tasks['Task']['id'],
                $tasks['Task']['title'],
                preg_replace("/[\r\n|\n|\r]+/", " ", $tasks['Task']['body']),
                $tasks['Task']['user_creation_id'],
                $tasks['Task']['user_assigned_id'],
                $tasks['Task']['creation_date'],
                $tasks['Task']['resolve_date'],
                $tasks['Task']['limit_date'],
                $tasks['Task']['appointment_id'],
                $tasksStatusList[$tasks['Task']['task_status_id']],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function trading_groups_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->trading_groups_create_data();

        $filename = date("N") . ConstantesFtp::TRADING_GROUPS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'code',
            'image',
            'web',
            'is_cv',
            'independent',
            'primary_color',
            'primary_font_color',
            'primary_background_color',
            'secondary_color',
            'secondary_font_color',
            'secondary_background_color',
            'color_active',
            'tertiary_color',
            'menu_color',
            'menu_background_color',
            'color_exito',
            'color_fallo',
            'color_informacion',
            'color_disabled',
            'creation_date',
        );
        fputcsv($file, $table);
        foreach ($data as $trading_groups) {
            $fila = array(
                $trading_groups['TradingGroup']['id'],
                $trading_groups['TradingGroup']['name'],
                $trading_groups['TradingGroup']['code'],
                $trading_groups['TradingGroup']['image'],
                $trading_groups['TradingGroup']['web'],
                $trading_groups['TradingGroup']['is_cv'],
                $trading_groups['TradingGroup']['independent'],
                $trading_groups['TradingGroup']['primary_color'],
                $trading_groups['TradingGroup']['primary_font_color'],
                $trading_groups['TradingGroup']['primary_background_color'],
                $trading_groups['TradingGroup']['secondary_color'],
                $trading_groups['TradingGroup']['secondary_font_color'],
                $trading_groups['TradingGroup']['secondary_background_color'],
                $trading_groups['TradingGroup']['color_active'],
                $trading_groups['TradingGroup']['tertiary_color'],
                $trading_groups['TradingGroup']['menu_color'],
                $trading_groups['TradingGroup']['menu_background_color'],
                $trading_groups['TradingGroup']['color_exito'],
                $trading_groups['TradingGroup']['color_fallo'],
                $trading_groups['TradingGroup']['color_informacion'],
                $trading_groups['TradingGroup']['color_disabled'],
                $trading_groups['TradingGroup']['creation_date'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function trading_groups_distributors_networks_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->trading_groups_distributors_networks_create_data();

        $filename = date("N") . ConstantesFtp::TRADING_GROUPS_DISTRIBUTORS_NETWORKS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'trading_group_id',
            'network_id',
        );
        fputcsv($file, $table);
        foreach ($data as $trading_groups_distributors_networks) {
            $fila = array(
                $trading_groups_distributors_networks['TradingGroupDistributorNetwork']['id'],
                $trading_groups_distributors_networks['TradingGroupDistributorNetwork']['trading_group_id'],
                $trading_groups_distributors_networks['TradingGroupDistributorNetwork']['network_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function trading_groups_networks_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->trading_groups_networks_create_data();

        $filename = date("N") . ConstantesFtp::TRADING_GROUPS_NETWORKS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'trading_group_id',
            'network_id',
        );
        fputcsv($file, $table);
        foreach ($data as $trading_groups_networks) {
            $fila = array(
                $trading_groups_networks['TradingGroupNetwork']['id'],
                $trading_groups_networks['TradingGroupNetwork']['trading_group_id'],
                $trading_groups_networks['TradingGroupNetwork']['network_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function trainings_courses_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->trainings_courses_create_data();

        $filename = date("N") . ConstantesFtp::TRAININGS_COURSES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'course_type_id',
            'training_provider_id',
            'duration',
            'price',
            'price_credit',
            'cost_training',
            'description',
            'part_number',
            'invoice_number',
            'active',
            'guid',
        );
        fputcsv($file, $table);
        foreach ($data as $trainings_courses) {
            $fila = array(
                $trainings_courses['TrainingCourse']['id'],
                $trainings_courses['TrainingCourse']['name'],
                $trainings_courses['TrainingCourse']['course_type_id'],
                $trainings_courses['TrainingCourse']['training_provider_id'],
                $trainings_courses['TrainingCourse']['duration'],
                $trainings_courses['TrainingCourse']['price'],
                $trainings_courses['TrainingCourse']['price_credit'],
                $trainings_courses['TrainingCourse']['cost_training'],
                $trainings_courses['TrainingCourse']['description'],
                $trainings_courses['TrainingCourse']['part_number'],
                $trainings_courses['TrainingCourse']['invoice_number'],
                $trainings_courses['TrainingCourse']['active'],
                $trainings_courses['TrainingCourse']['guid'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function trainings_credits_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->trainings_credits_create_data();

        $filename = date("N") . ConstantesFtp::TRAININGS_CREDITS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'pound',
            'credit',
        );
        fputcsv($file, $table);
        foreach ($data as $trainings_credits) {
            $fila = array(
                $trainings_credits['TrainingCredit']['id'],
                $trainings_credits['TrainingCredit']['pound'],
                $trainings_credits['TrainingCredit']['credit'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function trainings_credits_networks_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->trainings_credits_networks_create_data();

        $filename = date("N") . ConstantesFtp::TRAININGS_CREDITS_NETWORKS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'garage_network_id',
            'credit_spent',
            'credit_given',
            'credit',
            'creation_date',
            'contact_id',
            'description',
            'training_planned_course_id',
        );
        fputcsv($file, $table);
        foreach ($data as $trainings_credits_networks) {
            $fila = array(
                $trainings_credits_networks['TrainingCreditNetwork']['id'],
                $trainings_credits_networks['TrainingCreditNetwork']['garage_network_id'],
                $trainings_credits_networks['TrainingCreditNetwork']['credit_spent'],
                $trainings_credits_networks['TrainingCreditNetwork']['credit_given'],
                $trainings_credits_networks['TrainingCreditNetwork']['credit'],
                $trainings_credits_networks['TrainingCreditNetwork']['creation_date'],
                $trainings_credits_networks['TrainingCreditNetwork']['contact_id'],
                $trainings_credits_networks['TrainingCreditNetwork']['description'],
                $trainings_credits_networks['TrainingCreditNetwork']['training_planned_course_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function trainings_delegates_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->trainings_delegates_create_data();

        $filename = date("N") . ConstantesFtp::TRAININGS_DELEGATES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'training_planned_course_id',
            'garage_contact_staff_id',
            'is_refund_eligible',
            'reason_delegate_id',
            'order_number',
            'invoice_number',
            'cancelled',
            'refund_credits',
            'manual_credits_calculation',
            'paid_separately',
            'reason_cancelled_id',
            'network_id',
        );
        fputcsv($file, $table);
        foreach ($data as $trainings_delegates) {
            $fila = array(
                $trainings_delegates['TrainingDelegate']['id'],
                $trainings_delegates['TrainingDelegate']['training_planned_course_id'],
                $trainings_delegates['TrainingDelegate']['garage_contact_staff_id'],
                $trainings_delegates['TrainingDelegate']['is_refund_eligible'],
                $trainings_delegates['TrainingDelegate']['reason_delegate_id'],
                $trainings_delegates['TrainingDelegate']['order_number'],
                $trainings_delegates['TrainingDelegate']['invoice_number'],
                $trainings_delegates['TrainingDelegate']['cancelled'],
                $trainings_delegates['TrainingDelegate']['refund_credits'],
                $trainings_delegates['TrainingDelegate']['manual_credits_calculation'],
                $trainings_delegates['TrainingDelegate']['paid_separately'],
                $trainings_delegates['TrainingDelegate']['reason_cancelled_id'],
                $trainings_delegates['TrainingDelegate']['network_id'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function trainings_planned_courses_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->trainings_planned_courses_create_data();

        $filename = date("N") . ConstantesFtp::TRAININGS_PLANNED_COURSES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'training_course_id',
            'training_trainer_id',
            'venue_id',
            'date_from',
            'date_to',
            'starting_time',
            'duration',
            'availability',
            'status',
            'new_imported_name',
            'full',
            'guid',
            'invoice_number',
            'note',
        );
        fputcsv($file, $table);
        foreach ($data as $trainings_planned_courses) {
            $fila = array(
                $trainings_planned_courses['TrainingPlannedCourse']['id'],
                $trainings_planned_courses['TrainingPlannedCourse']['training_course_id'],
                $trainings_planned_courses['TrainingPlannedCourse']['training_trainer_id'],
                $trainings_planned_courses['TrainingPlannedCourse']['venue_id'],
                $trainings_planned_courses['TrainingPlannedCourse']['date_from'],
                $trainings_planned_courses['TrainingPlannedCourse']['date_to'],
                $trainings_planned_courses['TrainingPlannedCourse']['starting_time'],
                $trainings_planned_courses['TrainingPlannedCourse']['duration'],
                $trainings_planned_courses['TrainingPlannedCourse']['availability'],
                $trainings_planned_courses['TrainingPlannedCourse']['status'],
                $trainings_planned_courses['TrainingPlannedCourse']['new_imported_name'],
                $trainings_planned_courses['TrainingPlannedCourse']['full'],
                $trainings_planned_courses['TrainingPlannedCourse']['guid'],
                $trainings_planned_courses['TrainingPlannedCourse']['invoice_number'],
                $trainings_planned_courses['TrainingPlannedCourse']['note'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function trainings_providers_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->trainings_providers_create_data();

        $filename = date("N") . ConstantesFtp::TRAININGS_PROVIDERS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'email',
            'phone',
        );
        fputcsv($file, $table);
        foreach ($data as $trainings_providers) {
            $fila = array(
                $trainings_providers['TrainingProvider']['id'],
                $trainings_providers['TrainingProvider']['name'],
                $trainings_providers['TrainingProvider']['email'],
                $trainings_providers['TrainingProvider']['phone'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function trainings_trainers_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->trainings_trainers_create_data();

        $filename = date("N") . ConstantesFtp::TRAININGS_TRAINERS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'training_provider_id',
            'name',
            'email',
            'phone',
        );
        fputcsv($file, $table);
        foreach ($data as $trainings_trainers) {
            $fila = array(
                $trainings_trainers['TrainingTrainer']['id'],
                $trainings_trainers['TrainingTrainer']['training_provider_id'],
                $trainings_trainers['TrainingTrainer']['name'],
                $trainings_trainers['TrainingTrainer']['email'],
                $trainings_trainers['TrainingTrainer']['phone'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function users_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->users_create_data();

        $filename = date("N") . ConstantesFtp::USERS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'contact_id',
            'name',
            'surname',
            'username',
            'password',
            'role_id',
            'language_id',
            'garage_id',
            'distributor_id',
            'garage_type',
            'distributor_type',
            'active',
            'retries',
            'last_login',
            'set_login',
            'repairmaintenance',
            'creation_date',
            'region_id',
            'guid',
        );
        fputcsv($file, $table);
        foreach ($data as $users) {
            $fila = array(
                $users['User']['id'],
                $users['User']['contact_id'],
                $users['User']['name'],
                $users['User']['surname'],
                $users['User']['username'],
                $users['User']['password'],
                $users['User']['role_id'],
                $users['User']['language_id'],
                $users['User']['garage_id'],
                $users['User']['distributor_id'],
                $users['User']['garage_type'],
                $users['User']['distributor_type'],
                $users['User']['active'],
                $users['User']['retries'],
                $users['User']['last_login'],
                $users['User']['set_login'],
                $users['User']['repairmaintenance'],
                $users['User']['creation_date'],
                $users['User']['region_id'],
                $users['User']['guid'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function values_adds_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->values_adds_create_data();

        $filename = date("N") . ConstantesFtp::VALUES_ADDS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $values_adds) {
            $fila = array(
                $values_adds['ValueAdd']['id'],
                $values_adds['ValueAdd']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function value_add_suppliers_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->value_add_suppliers_create_data();

        $filename = date("N") . ConstantesFtp::VALUE_ADD_SUPPLIERS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $value_add_suppliers) {
            $fila = array(
                $value_add_suppliers['ValueAddSupplier']['id'],
                $value_add_suppliers['ValueAddSupplier']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function value_add_supplier_type_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->value_add_supplier_type_create_data();

        $filename = date("N") . ConstantesFtp::VALUE_ADD_SUPPLIER_TYPE_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        foreach ($data as $value_add_supplier_type) {
            $fila = array(
                $value_add_supplier_type['ValueAddSupplierType']['id'],
                $value_add_supplier_type['ValueAddSupplierType']['name_' . __l()],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function vehicle_types_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->vehicle_types_create_data();

        $filename = date("N") . ConstantesFtp::VEHICLE_TYPES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'url',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    private function venues_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->venues_create_data();

        $filename = date("N") . ConstantesFtp::VENUES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
            'address_1',
            'address_2',
            'address_3',
            'address_4',
            'latitude',
            'longitude',
            'town',
            'post_code',
            'telephone',
            'active',
            'guid',
        );
        fputcsv($file, $table);
        foreach ($data as $venues) {
            $fila = array(
                $venues['Venue']['id'],
                $venues['Venue']['name'],
                $venues['Venue']['address_1'],
                $venues['Venue']['address_2'],
                $venues['Venue']['address_3'],
                $venues['Venue']['address_4'],
                $venues['Venue']['latitude'],
                $venues['Venue']['longitude'],
                $venues['Venue']['town'],
                $venues['Venue']['post_code'],
                $venues['Venue']['telephone'],
                $venues['Venue']['active'],
                $venues['Venue']['guid'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function works_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->works_create_data();

        $filename = date("N") . ConstantesFtp::WORKS_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'network_id',
            'grouping_genart_id',
            'active',
            'name',
            'code',
            'discount',
            'markup',
            'surcharge',
        );
        fputcsv($file, $table);
        foreach ($data as $works) {
            $fila = array(
                $works['Work']['id'],
                $works['Work']['network_id'],
                $works['Work']['grouping_genart_id'],
                $works['Work']['active'],
                $works['Work']['name_' . __l()],
                $works['Work']['code'],
                $works['Work']['discount'],
                $works['Work']['markup'],
                $works['Work']['surcharge'],
            );
            fputcsv($file, $fila);
        }
        fclose($file);
    }

    private function workshop_activities_csv()
    {
        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $data = $this->workshop_activities_create_data();

        $filename = date("N") . ConstantesFtp::WORKSHOP_ACTIVITIES_NAME;
        $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.csv';
        $file = fopen($pathFilename, 'a');
        $table = array(
            'id',
            'name',
        );
        fputcsv($file, $table);
        fclose($file);
    }

    /**
     * Send database ZIP export to SFTP.
     */
    public function sendSftpDb()
    {
        try {
            // Include the SFTP class from phpseclib
            set_include_path(WWW_ROOT . '../Vendor/phpseclib');
            require_once WWW_ROOT . '../Vendor/phpseclib/Net/SFTP.php';

            $remoteFilename = date("Y-m-d", time()) . ConstantesFtp::DBZIP_NAME;
            $localFilename = date("N") . ConstantesFtp::DBZIP_NAME;
            $pathFilename = WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $localFilename . '.zip';
            $sftpParams = Configure::read('autopart.FTP');

            $sftp = new Net_SFTP($sftpParams['Host'], $sftpParams['Port']);
            if (!$sftp->login(SFTP_USERNAME, Texto::encryptDecryptText(SFTP_PASSWORD, false))) {
                CakeLog::debug(print_r("SFTP - Export DB - Login failed", true));
                return false;
            }
            $remoteFile = $remoteFilename . '.zip';
            $ftpFilesDirectory = SFTP_REMOTE_DB;
            if ($sftp->chdir($ftpFilesDirectory)) {
                if ($sftp->file_exists($remoteFile) && !$sftp->delete($remoteFile)) {
                    CakeLog::debug(print_r("SFTP - Export DB - Failed to delete the file", true));
                }
            } else {
                CakeLog::debug(print_r("SFTP - Export DB - Failed to change directory", true));
            }

            if ($sftp->put(SFTP_REMOTE_DB . '/' . $remoteFile, $pathFilename, NET_SFTP_LOCAL_FILE)) {
                return true;
            } else {
                CakeLog::debug(print_r("SFTP - Export DB - Upload failed", true));
                return false;
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("SFTP - Export DB - An exception has ocurred " . $e->getMessage(), true));
        }
    }

    /**
     * Create database ZIP.
     */
    public function zipCreate()
    {
        try {
            $filename = date("N") . ConstantesFtp::DBZIP_NAME;
            $tmpZipName =  WWW_ROOT . ConstantesFtp::CSV_DB_PATH . $filename . '.zip';
            $zip = new ZipArchive;
            if ($zip->open($tmpZipName, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                $arrayConstants = $this->arrayCreate();
                foreach ($arrayConstants as $const_table) {
                    $zip->addFile(WWW_ROOT . ConstantesFtp::CSV_DB_PATH . date("N") . $const_table . '.csv', date("Y-m-d", time()) . $const_table . '.csv');
                }
                $zip->close();
            } else {
                CakeLog::debug(print_r("SFTP - Export DB - Failure to open zip file", true));
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("SFTP - Export DB - An exception has ocurred " . $e->getMessage(), true));
        }
    }

    private function arrayCreate()
    {
        $array_constants = array(
            ConstantesFtp::AGREEMENT_NAME,
            ConstantesFtp::ANNEX_DETAILS_NAME,
            ConstantesFtp::APPOINTMENTS_NAME,
            ConstantesFtp::ASSOCIATIONS_TYPES_NAME,
            ConstantesFtp::ASSOCIATIONS_NAME,
            ConstantesFtp::BILLINGS_SCHEDULES_NAME,
            ConstantesFtp::BOOKINGS_NAME,
            ConstantesFtp::CITIES_NAME,
            ConstantesFtp::CONTACTS_NAME,
            ConstantesFtp::CONTACTS_TITLES_NAME,
            ConstantesFtp::COUNTRIES_NAME,
            ConstantesFtp::COURSES_TYPES_NAME,
            ConstantesFtp::COURTESY_CAR_TYPES_NAME,
            ConstantesFtp::CUSTOMERS_ACTIVITIES_NAME,
            ConstantesFtp::DISTRIBUTORS_NAME,
            ConstantesFtp::DISTRIBUTORS_ACTIVITIES_NAME,
            ConstantesFtp::DISTRIBUTORS_ACTIVITIES_PRIMARY_NAME,
            ConstantesFtp::DISTRIBUTORS_COMMENTS_NAME,
            ConstantesFtp::DISTRIBUTORS_CONTACTS_BDM_NAME,
            ConstantesFtp::DISTRIBUTORS_CONTACTS_GENERAL_BRANCH_MANAGER_NAME,
            ConstantesFtp::DISTRIBUTORS_CONTACTS_STAFF_NAME,
            ConstantesFtp::DISTRIBUTORS_CONTRACTS_NAME,
            ConstantesFtp::DISTRIBUTORS_CUSTOMER_ACTIVITIES_NAME,
            ConstantesFtp::DISTRIBUTORS_CUSTOMER_ACTIVITIES_WORKSHOPS_NAME,
            ConstantesFtp::DISTRIBUTORS_DISTRIBUTORS_ACTIVITIES_NAME,
            ConstantesFtp::DISTRIBUTORS_DISTRIBUTORS_NETWORKS_NAME,
            ConstantesFtp::DISTRIBUTORS_FIGURES_NAME,
            ConstantesFtp::DISTRIBUTORS_FIGURES_DETAILS_NAME,
            ConstantesFtp::DISTRIBUTORS_IMAGES_NAME,
            ConstantesFtp::DISTRIBUTORS_KPIS_NAME,
            ConstantesFtp::DISTRIBUTORS_LABELS_NAME,
            ConstantesFtp::DISTRIBUTORS_NETWORKS_NAME,
            ConstantesFtp::DISTRIBUTORS_NETWORKS_CONTACTS_BDM_NAME,
            ConstantesFtp::DISTRIBUTORS_OBJECTIVES_NAME,
            ConstantesFtp::DISTRIBUTORS_ROUTES_NAME,
            ConstantesFtp::DISTRIBUTORS_SERVICES_NAME,
            ConstantesFtp::DISTRIBUTORS_SOFTWARE_NAME,
            ConstantesFtp::DISTRIBUTORS_TYPES_NAME,
            ConstantesFtp::EMPLOYEE_TYPES_NAME,
            ConstantesFtp::ENQUIRIES_NAME,
            ConstantesFtp::EQUIPMENTS_NAME,
            ConstantesFtp::EQUIPMENTS_TYPES_NAME,
            ConstantesFtp::FACILITIES_NAME,
            ConstantesFtp::FLUID_NAME,
            ConstantesFtp::GARAGES_NAME,
            ConstantesFtp::GARAGES_AGREEMENTS_NAME,
            ConstantesFtp::GARAGES_B2B_POSTCODES_NAME,
            ConstantesFtp::GARAGES_B2C_POSTCODES_NAME,
            ConstantesFtp::GARAGES_BRANDS_NAME,
            ConstantesFtp::GARAGES_CAMPAIGN_NAME,
            ConstantesFtp::GARAGES_COMMENTS_NAME,
            ConstantesFtp::GARAGES_CONTACTS_BDM_NAME,
            ConstantesFtp::GARAGES_CONTACTS_GENERAL_BRANCH_MANAGER_NAME,
            ConstantesFtp::GARAGES_CONTACTS_LISTS_NAME,
            ConstantesFtp::GARAGES_CONTACTS_STAFF_NAME,
            ConstantesFtp::GARAGES_COURTESY_CAR_TYPES_NAME,
            ConstantesFtp::GARAGES_CUSTOMERS_ACTIVITIES_NAME,
            ConstantesFtp::GARAGES_DISTRIBUTORS_NAME,
            ConstantesFtp::GARAGES_DISTRIBUTORS_SHORTCUTS_NAME,
            ConstantesFtp::GARAGES_EMPLOYEES_NAME,
            ConstantesFtp::GARAGES_EQUIPMENTS_NAME,
            ConstantesFtp::GARAGES_FACILITIES_NAME,
            ConstantesFtp::GARAGES_FIGURES_NAME,
            ConstantesFtp::GARAGES_FIGURES_DETAILS_NAME,
            ConstantesFtp::GARAGES_FILES_NAME,
            ConstantesFtp::GARAGES_IMAGES_NAME,
            ConstantesFtp::GARAGES_KPIS_NAME,
            ConstantesFtp::GARAGES_NETWORKS_NAME,
            ConstantesFtp::GARAGES_NETWORKS_CONTACTS_NAME,
            ConstantesFtp::GARAGES_NETWORKS_FLUIDS_NAME,
            ConstantesFtp::GARAGES_NETWORKS_GENARTS_NAME,
            ConstantesFtp::GARAGES_NETWORKS_GENARTS_FAMILIES_NAME,
            ConstantesFtp::GARAGES_NETWORKS_GENARTS_MASTER_NAME,
            ConstantesFtp::GARAGES_NETWORKS_IMAGES_NAME,
            ConstantesFtp::GARAGES_NETWORKS_SERVICES_NAME,
            ConstantesFtp::GARAGES_NETWORKS_SERVICES_DRIVERS_NAME,
            ConstantesFtp::GARAGES_NETWORKS_VEHICLES_NAME,
            ConstantesFtp::GARAGES_NETWORKS_VEHICLES_BLACK_LIST_NAME,
            ConstantesFtp::GARAGES_NETWORKS_VEHICLE_TYPES_NAME,
            ConstantesFtp::GARAGES_NETWORKS_WORKS_NAME,
            ConstantesFtp::GARAGES_NETWORKS_WORKS_LABOURS_NAME,
            ConstantesFtp::GARAGES_NETWORKS_WORKS_PRICES_NAME,
            ConstantesFtp::GARAGES_OILS_NAME,
            ConstantesFtp::GARAGES_PRODUCTS_NAME,
            ConstantesFtp::GARAGES_ROUTES_NAME,
            ConstantesFtp::GARAGES_SERVICES_NAME,
            ConstantesFtp::GARAGES_SOFTWARE_NAME,
            ConstantesFtp::GARAGES_SPECIALIST_MAKES_NAME,
            ConstantesFtp::GARAGES_STATUSES_NAME,
            ConstantesFtp::GARAGES_VALUES_ADDS_NAME,
            ConstantesFtp::GARAGES_VALUE_ADD_SUPPLIER_NAME,
            ConstantesFtp::GARAGES_VEHICLES_NAME,
            ConstantesFtp::GARAGES_VEHICLE_TYPES_NAME,
            ConstantesFtp::GARAGES_VISIT_FREQUENCIES_NAME,
            ConstantesFtp::GARAGES_WEBSITES_NAME,
            ConstantesFtp::GARAGES_WORKSHOP_ACTIVITIES_NAME,
            ConstantesFtp::GENARTS_NAME,
            ConstantesFtp::GENARTS_FAMILIES_NAME,
            ConstantesFtp::GENARTS_MASTER_NAME,
            ConstantesFtp::GROUPING_GENARTS_NAME,
            ConstantesFtp::HOLD_REASON_TYPES_NAME,
            ConstantesFtp::LEAVING_REASON_TYPES_NAME,
            ConstantesFtp::LISTS_NAME,
            ConstantesFtp::NETWORKS_NAME,
            ConstantesFtp::NETWORKS_CONTACTS_BDM_NAME,
            ConstantesFtp::NETWORKS_CONTACTS_LISTS_NAME,
            ConstantesFtp::NETWORKS_CONTRACT_TYPES_NAME,
            ConstantesFtp::NETWORKS_STATUSES_NAME,
            ConstantesFtp::ORDERS_NAME,
            ConstantesFtp::ORDER_PRODUCTS_NAME,
            ConstantesFtp::ORDER_TYPES_NAME,
            ConstantesFtp::POSITIONS_NAME,
            ConstantesFtp::POSTCODES_NAME,
            ConstantesFtp::POSTCODE_PROVINCES_NAME,
            ConstantesFtp::PROVINCES_NAME,
            ConstantesFtp::REASONS_DELEGATES_NAME,
            ConstantesFtp::REGIONS_NAME,
            ConstantesFtp::ROLES_NAME,
            ConstantesFtp::SERVICES_NAME,
            ConstantesFtp::SERVICES_DRIVERS_NAME,
            ConstantesFtp::SERVICES_TYPES_NAME,
            ConstantesFtp::SOFTWARE_NAME,
            ConstantesFtp::SOFTWARE_MANUFACTURES_NAME,
            ConstantesFtp::SOFTWARE_TYPES_NAME,
            ConstantesFtp::TASKS_NAME,
            ConstantesFtp::TRADING_GROUPS_NAME,
            ConstantesFtp::TRADING_GROUPS_DISTRIBUTORS_NETWORKS_NAME,
            ConstantesFtp::TRADING_GROUPS_NETWORKS_NAME,
            ConstantesFtp::TRAININGS_COURSES_NAME,
            ConstantesFtp::TRAININGS_CREDITS_NAME,
            ConstantesFtp::TRAININGS_CREDITS_NETWORKS_NAME,
            ConstantesFtp::TRAININGS_DELEGATES_NAME,
            ConstantesFtp::TRAININGS_PLANNED_COURSES_NAME,
            ConstantesFtp::TRAININGS_PROVIDERS_NAME,
            ConstantesFtp::TRAININGS_TRAINERS_NAME,
            ConstantesFtp::USERS_NAME,
            ConstantesFtp::VALUES_ADDS_NAME,
            ConstantesFtp::VALUE_ADD_SUPPLIERS_NAME,
            ConstantesFtp::VALUE_ADD_SUPPLIER_TYPE_NAME,
            ConstantesFtp::VEHICLE_TYPES_NAME,
            ConstantesFtp::VENUES_NAME,
            ConstantesFtp::WORKS_NAME,
            ConstantesFtp::WORKSHOP_ACTIVITIES_NAME
        );

        return $array_constants;
    }
}
