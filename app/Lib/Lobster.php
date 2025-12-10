<?php
App::uses('HttpSocket', 'Network/Http');
class Lobster
{
	/**
	 * Makes auth http request to Lobster and gets Garages JSON file.
	 */
	public function getGaragesGV($erpCode)
	{
		try {
			ini_set('memory_limit', '3G');

			$url = GNMAAG_LOBSTER_BASE_URL . Configure::read('lobster_api.update_garage_endpoint');

			$input = array(
				'AagRegion' => 'Benelux',
				'ErpCode' => $erpCode
			);

			$input = json_encode($input);

			$headers = array(
				'Content-Type' => 'application/json'
			);

			$username = GNMAAG_LOBSTER_API_USER;
			$password = Texto::encryptDecryptText(GNMAAG_LOBSTER_API_KEY, false);

			$http = new HttpSocket(
				array(
					'ssl_verify_peer' => false,
					'ssl_verify_host' => false,
					'ssl_allow_self_signed' => true,
					'ssl_verify_peer_name' => false
				)
			);
			$http->configAuth('Digest', $username, $password);
			$response = $http->post($url, $input, $headers);

			if (!isset($response->body)) {
				return null;
			}
			return json_decode($response->body, true);
		} catch (Exception $e) {
			CakeLog::debug(print_r("Lobster - Get garages GV - An error has occured: " . $e->getMessage(), true));
			return false;
		}
	}

	/**
	 * Generates an email with the log file to be sent to the administrator users.
	 */
	public static function generateEmail($filename, $log_file_path, $aag_region_id, $date, $prefix)
	{
		try {
			$contactClass = ClassRegistry::init('Contact');
			$emailClass = ClassRegistry::init('Email');

			//Obtains the emails of admin type users in the Benelux region
			$emails = $contactClass->getBeneluxAdminsEmails();
			$emailsValidated = array();

			//Validates emails
			foreach ($emails as $email) {
				if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$emailsValidated[] = $email;
				}
			}

			//Implodes valid emails into one string
			$emailsImploded = implode(';', $emailsValidated);

			//Generates Email with validated emails and log file.
			$emailClass->newEmailGaragesGVSync($filename, serialize($log_file_path), $emailsImploded, $aag_region_id, $date, $prefix);
		} catch (Exception $e) {
			CakeLog::debug(print_r("Lobster - Generate email - An error has occured: " . $e->getMessage(), true));
			return false;
		}
	}

	/**
	 * Sync all Benelux Garages.
	 * Creates log file.
	 * Creates email.
	 */
	public function garagesGVSync($erpCode)
	{
		try {
			$requestBody = $this->getGaragesGV($erpCode);

			//Log file structure
			$log = array(
				'CreatedGarages' => array(),
				'UpdatedGarages' => array(),
				'CreatedFleets' => array(),
				'UpdatedFleets' => array(),
				'Errors' => array()
			);

			//Log counters
			$countCreatedGarages = 0;
			$countUpdatedGarages = 0;
			$countCreatedFleets = 0;
			$countUpdatedFleets = 0;
			$countErrors = 0;

			//Class starters
			$garageClass = ClassRegistry::init('Garage');
			$cityClass = ClassRegistry::init('City');
			//$postcodeProvinceClass = ClassRegistry::init('PostcodeProvince');
			$provinceClass = ClassRegistry::init('Province');
			$countryClass = ClassRegistry::init('Country');
			$fleetClass = ClassRegistry::init('Fleet');

			$aag_region_id = Configure::read('AAG_REGION_ID_BENELUX');

			$garageListERPCode = $requestBody['Garage_List'][0]['GarageInfo'];

			//$countProcessed = 0;
			//echo date('Y-m-d H:i:s');
			foreach ($garageListERPCode as $garages) {
				//if($countProcessed == 1) { break; }

				$garage = $garages['Garage'];
				$erpCodeComplete = '';

				if (isset($garage['Erp']) && isset($garage['IdErp'])) {
					// $garage['Erp'] is AX, SAP, etc.
					// $garage['IdErp'] is 001234, etc
					$erpCodeComplete = $garage['Erp'] . $garage['IdErp'];
					$erp_id = Configure::read('ERP_IDS.' . $garage['Erp']);
				} else {
					$countErrors++;
					//If not erp code, sends bussiness name.
					//If not bussiness name, sends empty value.
					$log['Errors'][] = array(
						'BusinessName' => !empty($garage['BusinessName']) ? $garage['BusinessName'] : '',
						'Error' => 'IdErp is empty.'
					);
					continue;
				}

				//Search garage by ref_code and aag_region_id
				//Checks if a garage already exists in database.
				if (isset($garage['company_code']) && !empty(trim($garage['company_code'])) && substr($garage['company_code'], 0, strlen(ConstantsFleet::LOBSTERCODE)) === ConstantsFleet::LOBSTERCODE) {
					$fleetCheck = $fleetClass->findByRefCodeAndErpIdAndAagRegionId($garage['IdErp'], $erp_id, $aag_region_id);
					$garageCheck = null;
					$isFleet = true;
				} elseif (isset($garage['IdErp'])) {
					$garageCheck = $garageClass->findByRefCodeAndErpIdAndAagRegionId($garage['IdErp'], $erp_id, $aag_region_id);
					$fleetCheck = null;
					$isFleet = false;
				} else {
					$garageCheck = $fleetCheck = null;
				}

				if (!isset($garage['BusinessName']) && empty($garage['BusinessName'])) {
					$countErrors++;
					$log['Errors'][] = array(
						'ErpCode' => $erpCodeComplete,
						'CompanyCode' => isset($garage['company_code']) ? $garage['company_code'] : null,
						'Error' => 'BusinessName is empty.'
					);
				}

				//Location fields
				$address1 = '';
				if (isset($garage['Addr1']) && !empty($garage['Addr1'])) {
					$address1 .= $garage['Addr1'];
				} else {
					$countErrors++;
					$log['Errors'][] = array(
						'ErpCode' => $erpCodeComplete,
						'CompanyCode' => isset($garage['company_code']) ? $garage['company_code'] : null,
						'Error' => 'Addr1 is empty.'
					);
				}

				$city = null;
				$province = null;
				$cityName = null;
				$postcode = '';
				$country = null;

				if (isset($garage['AddrPCode']) && !empty($garage['AddrPCode'])) {
					$postcode = $garage['AddrPCode'];
				} else {
					$countErrors++;
					$log['Errors'][] = array(
						'ErpCode' => $erpCodeComplete,
						'CompanyCode' => isset($garage['company_code']) ? $garage['company_code'] : null,
						'Error' => 'AddrPcode is empty.'
					);
				}

				if (isset($garage['AddrTown']) && !empty($garage['AddrTown'])) {
					$city = $cityClass->findCityByNameInRegion($garage['AddrTown'], $aag_region_id);
					$cityName = $garage['AddrTown'];

					//If city doesn't exist it's created
					//Disabled to avoid creating wrong cities coming from Lobster
					/*if (empty($city)) {
					$postcodeDigits = substr($postcode, 0, 4);
					$postcodeProvince = $postcodeProvinceClass->findPostcodeProvinceByPostcodeInRegion(
						$postcodeDigits,
						$aag_region_id
					);

					if ($postcodeProvince) {
						$province = $provinceClass->findById($postcodeProvince['PostcodeProvince']['province_id']);

						if ($province) {
							$country = $countryClass->findById($province['Province']['country_id']);
							//Gets latitude and longitude
							//$dataLatitudeLongitude = $garageClass->getLatitudeLongitude($country, $province, $cityName, $postcode);
							//echo "\n"."ciudad gmaps create";

							$cityArray = array(
								"City" => array(
									'name' => $cityName,
									'latitude' => null, // $dataLatitudeLongitude['latitude'],
									'longitude' => null, // $dataLatitudeLongitude['longitude'],
									'province_id' => $postcodeProvince['PostcodeProvince']['province_id'],
								)
							);
							$city = $cityClass->add_city($cityArray);
						} else {
							$countErrors++;
							$log['Errors'][] = array(
								'ErpCode' => $erpCodeComplete,
								'Error' => 'Province not found.'
							);
						}
					} else {
						$countErrors++;
						$log['Errors'][] = array(
							'ErpCode' => $erpCodeComplete,
							'Error' => 'PostcodeProvince not found.'
						);
					}
				}*/
				} else {
					$countErrors++;
					$log['Errors'][] = array(
						'ErpCode' => $erpCodeComplete,
						'CompanyCode' => isset($garage['company_code']) ? $garage['company_code'] : null,
						'Error' => 'AddrTown is empty.'
					);
				}

				$dataLatitudeLongitude = null;

				//If Garage or fleet exists, it is updated
				if ($isFleet && !empty($fleetCheck)) {
					$locationData = array(
						'city' => !empty($city) ? $city : null,
						'cityName' => !empty($cityName) ? $cityName : null,
						'address1' => !empty($address1) ? $address1 : null,
						'postcode' => !empty($postcode) ? $postcode : null,
						'province' => !empty($province) ? $province : null,
						'country' => !empty($country) ? $country : null,
					);

					$response = self::updateFleet($garage, $fleetCheck, $locationData); //This garage is a fleet to be updated.

					if ($response) {
						$countUpdatedFleets++;
						$log['UpdatedFleets'][] = array(
							'ErpCode' => $erpCodeComplete,
							'CompanyCode' => isset($garage['company_code']) ? $garage['company_code'] : null
						);
					} else {
						$countErrors++;
						$log['Errors'][] = array(
							'ErpCode' => $erpCodeComplete,
							'CompanyCode' => isset($garage['company_code']) ? $garage['company_code'] : null,
							'Error' => 'Couldn\'t update fleet.'
						);
					}
				} elseif (!$isFleet && !empty($garageCheck)) {
					if (!empty($city) && !isset($province) && !isset($country)) {
						$province = $provinceClass->findById($city['City']['province_id']);
						$country = $countryClass->findById($province['Province']['country_id']);
					}


					//Check if location variables are defined and not null/empty.
					if (!empty($address1) && !empty($postcode) && !empty($city) && !empty($cityName) && isset($province) && isset($country)) {
						// Latitude and longitude are updated if the workshop address has changed.
						if (
							$garageCheck['Garage']['address1'] !== $address1 ||
							$garageCheck['Garage']['postcode'] !== $postcode ||
							$garageCheck['Garage']['town'] !== $cityName ||
							$garageCheck['Garage']['city_id'] !== $city['City']['id'] ||
							$garageCheck['Garage']['province_id'] !== $city['City']['province_id']
						) {
							$dataLatitudeLongitude = $garageClass->getLatitudeLongitude($country, $province, $cityName, $postcode, $address1);
							//echo "\n"."garage gmaps update";
						}

						//Check if location fields are not empty to update them
						if (!empty($address1)) {
							$garageCheck['Garage']['address1'] = $address1;
						}
						if (!empty($postcode)) {
							$garageCheck['Garage']['postcode'] = $postcode;
						}
						if (!empty($cityName)) {
							$garageCheck['Garage']['town'] = $cityName;
						}
						if (!empty($city['City']['id'])) {
							$garageCheck['Garage']['city_id'] = $city['City']['id'];
						}
						if (!empty($city['City']['province_id'])) {
							$garageCheck['Garage']['province_id'] = $city['City']['province_id'];
						}
						$garageCheck['Garage']['latitude'] = isset($dataLatitudeLongitude) ? $dataLatitudeLongitude['latitude'] : $garageCheck['Garage']['latitude'];
						$garageCheck['Garage']['longitude'] = isset($dataLatitudeLongitude) ? $dataLatitudeLongitude['longitude'] : $garageCheck['Garage']['longitude'];
					}

					//update fields

					if (empty($garageCheck['Garage']['guid'])) {
						$garageCheck['Garage']['guid'] = CakeText::uuid();
					}

					if (!empty($garage['StatusCode'])) {
						$garageCheck['Garage']['status'] = $garage['StatusCode'];
					}

					if (isset($garage['company_code']) && !empty($garage['company_code']) && ($garageCheck['Garage']['company_code'] != $garage['company_code'])) {
						$garageCheck['Garage']['company_code'] = $garage['company_code'];
					}

					if (isset($garage['payment_terms']) && !empty($garage['payment_terms']) && ($garageCheck['Garage']['payment_terms'] != $garage['payment_terms'])) {
						$garageCheck['Garage']['payment_terms'] = $garage['payment_terms'];
					}

					$garageBD = $garageClass->updateGarageGV($garageCheck);

					if ($garageBD) {
						$countUpdatedGarages++;
						$log['UpdatedGarages'][] = array(
							'ErpCode' => $erpCodeComplete
						);
					} else {
						$countErrors++;
						$log['Errors'][] = array(
							'ErpCode' => $erpCodeComplete,
							'Error' => 'Couldn\'t update garage.'
						);
					}
				} elseif ($isFleet) { //If is fleet and was not found in DB, we create it.
					$response = self::addFleet($garage, $aag_region_id, $erp_id, $city);

					if ($response) {
						$countCreatedFleets++;
						$log['CreatedFleets'][] = array(
							'ErpCode' => $erpCodeComplete,
							'CompanyCode' => isset($garage['company_code']) ? $garage['company_code'] : null,
						);
					} else {
						$countErrors++;
						$log['Errors'][] = array(
							'ErpCode' => $erpCodeComplete,
							'CompanyCode' => isset($garage['company_code']) ? $garage['company_code'] : null,
							'Error' => 'Couldn\'t create fleet.'
						);
					}
				} else {
					//If Garage doesn't exist, Garage is created
					if (!empty($city)) {
						if (!isset($province) && !isset($country)) {
							$province = $provinceClass->findById($city['City']['province_id']);
							$country = $countryClass->findById($province['Province']['country_id']);
						}
						if (!empty($address1) && !empty($postcode) && isset($province) && isset($country)) {
							//$dataLatitudeLongitude = $garageClass->getLatitudeLongitude($country, $province, $cityName, $postcode, $address1);
							//echo "\n"."garage gmaps create";
						}
					}

					$garageArray = array(
						'Garage' => array(
							'guid' => CakeText::uuid(), //Adds guid to new garages
							'name' => !empty($garage['BusinessName']) ? $garage['BusinessName'] : null,
							'ref_code' => !empty($garage['IdErp']) ? $garage['IdErp'] : null, //Allways comes defined with the right prefix.
							'erp_id' => $erp_id,
							'business_name' => !empty($garage['BusinessName']) ? $garage['BusinessName'] : null,
							'status' => !empty($garage['StatusCode']) ? $garage['StatusCode'] : null,
							'address1' => $address1,
							'postcode' => $postcode,
							'town' => !empty($garage['AddrTown']) ? $garage['AddrTown'] : null,
							'city_id' => !empty($city) ? $city['City']['id'] : null,
							'province_id' => !empty($city) ? $city['City']['province_id'] : null,
							'latitude' => !empty($dataLatitudeLongitude['latitude']) ? $dataLatitudeLongitude['latitude'] : null,
							'longitude' => !empty($dataLatitudeLongitude['longitude']) ? $dataLatitudeLongitude['longitude'] : null,
							'monday_open_1' => !empty($garage['MonOpen1']) ? $garage['MonOpen1'] : null,
							'monday_closed_1' => !empty($garage['MonClose1']) ? $garage['MonClose1'] : null,
							'tuesday_open_1' => !empty($garage['TueOpen1']) ? $garage['TueOpen1'] : null,
							'tuesday_closed_1' => !empty($garage['TueClose1']) ? $garage['TueClose1'] : null,
							'wednesday_open_1' => !empty($garage['WedOpen1']) ? $garage['WedOpen1'] : null,
							'wednesday_closed_1' => !empty($garage['WedClose1']) ? $garage['WedClose1'] : null,
							'thursday_open_1' => !empty($garage['ThruOpen1']) ? $garage['ThruOpen1'] : null,
							'thursday_closed_1' => !empty($garage['ThruClose1']) ? $garage['ThruClose1'] : null,
							'friday_open_1' => !empty($garage['FriOpen1']) ? $garage['FriOpen1'] : null,
							'friday_closed_1' => !empty($garage['FriClose1']) ? $garage['FriClose1'] : null,
							'saturday_open_1' => !empty($garage['SatOpen1']) ? $garage['SatOpen1'] : null,
							'saturday_closed_1' => !empty($garage['SatClose1']) ? $garage['SatClose1'] : null,
							'sunday_open_1' => !empty($garage['SunOpen1']) ? $garage['SunOpen1'] : null,
							'sunday_closed_1' => !empty($garage['SunClose1']) ? $garage['SunClose1'] : null,
							'aag_region_id' => $aag_region_id,
							'manually_created' => ConstantsBooleans::NO,
							'repairmaintenance' => ConstantsBooleans::NO, //If its a new garage it will not be synchronized with R&M yet.
							'payment_terms' => !empty($garage['payment_terms']) ? $garage['payment_terms'] : null,
							'company_code' => !empty($garage['company_code']) ? $garage['company_code'] : null,
						)
					);

					$garageBD = $garageClass->addGarageGV($garageArray);

					if ($garageBD) {
						$countCreatedGarages++;
						$log['CreatedGarages'][] = array(
							'ErpCode' => $erpCodeComplete
						);
					} else {
						$countErrors++;
						$log['Errors'][] = array(
							'ErpCode' => $erpCodeComplete,
							'Error' => 'Couldn\'t create garage.'
						);
					}
				}

				//$countProcessed++;
			}

			//Adds total number of created, modified and errors from counters to log file
			$log['CreatedGarages'][] = array(
				'total' => $countCreatedGarages
			);
			$log['CreatedFleets'][] = array(
				'total' => $countCreatedFleets
			);
			$log['UpdatedGarages'][] = array(
				'total' => $countUpdatedGarages
			);
			$log['UpdatedFleets'][] = array(
				'total' => $countUpdatedFleets
			);
			$log['Errors'][] = array(
				'total' => $countErrors
			);

			//Generates log file
			$logInfo = FileManager::generate_json_file_lobster($erpCode, $log);

			$logFileFilename = array($logInfo['filename']);
			$logFilePath = array($logInfo['path']);
			$logFileDate = $logInfo['date'];

			//Generates email
			$this->generateEmail($logFileFilename, $logFilePath, $aag_region_id, $logFileDate, $erpCode);

			//echo "\n".date('Y-m-d H:i:s');
		} catch (Exception $e) {
			CakeLog::debug(print_r("Lobster - Garages GV Sync - An error has occured: " . $e->getMessage(), true));
			return false;
		}
	}

	/**
	 * Sync all the vehicles (brands) for GNM AAG.
	 */
	public function vehiclesSync()
	{
		try {
			exit;
			$json = '{
				"status": 200,
				"took": 105,
				"total": 345,
				"data": [
				  {
					"brand_id": 3854,
					"brand_name": "ABARTH",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 609,
					"brand_name": "AC",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1505,
					"brand_name": "ACURA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5063,
					"brand_name": "ADIVA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4330,
					"brand_name": "ADLY",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4331,
					"brand_name": "AEON",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5500,
					"brand_name": "AIWAYS",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 1480,
					"brand_name": "AIXAM",
					"fuel_type": [
					  "Diesel",
					  "Petrol",
					  "Electric"
					]
				  },
				  {
					"brand_id": 2,
					"brand_name": "ALFA ROMEO",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Petrol/Compressed Natural Gas (CNG)"
					]
				  },
				  {
					"brand_id": 866,
					"brand_name": "ALPINA",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Diesel/Electro",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 810,
					"brand_name": "ALPINE",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 6699,
					"brand_name": "ALRENDO",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 4730,
					"brand_name": "ALVIS",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 2246,
					"brand_name": "AMC",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 2524,
					"brand_name": "APRILIA",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 1360,
					"brand_name": "ARO",
					"fuel_type": [
					  "Diesel",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 3495,
					"brand_name": "ARTEGA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 879,
					"brand_name": "ASIA MOTORS",
					"fuel_type": [
					  "Diesel",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4628,
					"brand_name": "ASIAWING",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 881,
					"brand_name": "ASTON MARTIN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5064,
					"brand_name": "ATALA",
					"fuel_type": [
					  "Mixture"
					]
				  },
				  {
					"brand_id": 5,
					"brand_name": "AUDI",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Diesel/Electro",
					  "Electric",
					  "Petrol/Ethanol",
					  "Petrol/Compressed Natural Gas (CNG)"
					]
				  },
				  {
					"brand_id": 6,
					"brand_name": "AUSTIN",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 1538,
					"brand_name": "AUSTIN-HEALEY",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 3,
					"brand_name": "AUTO UNION",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 799,
					"brand_name": "AUTOBIANCHI",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 3773,
					"brand_name": "BAIC",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4423,
					"brand_name": "BAOTIAN",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 134,
					"brand_name": "BARKAS",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 3071,
					"brand_name": "BAW",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 10,
					"brand_name": "BEDFORD",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 4402,
					"brand_name": "BEELINE",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 2532,
					"brand_name": "BENELLI",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 815,
					"brand_name": "BENTLEY",
					"fuel_type": [
					  "Petrol",
					  "Petrol/Ethanol",
					  "Petrol/Electric",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 4801,
					"brand_name": "BENZHOU",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1485,
					"brand_name": "BERTONE",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 4315,
					"brand_name": "BETA",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4368,
					"brand_name": "BIMOTA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1486,
					"brand_name": "BITTER",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 16,
					"brand_name": "BMW",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Diesel/Electro",
					  "Electric",
					  "Petrol/Compressed Natural Gas (CNG)"
					]
				  },
				  {
					"brand_id": 1537,
					"brand_name": "BOND",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 136,
					"brand_name": "BORGWARD",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1487,
					"brand_name": "BRISTOL",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4540,
					"brand_name": "BRIXTON",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4048,
					"brand_name": "BUELL",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 788,
					"brand_name": "BUGATTI",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 816,
					"brand_name": "BUICK",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 3122,
					"brand_name": "BYD",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 819,
					"brand_name": "CADILLAC",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Ethanol",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 2554,
					"brand_name": "CAGIVA",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 6476,
					"brand_name": "CAKE",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 1488,
					"brand_name": "CALLAWAY",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1489,
					"brand_name": "CARBODIES",
					"fuel_type": [
					  "Diesel"
					]
				  },
				  {
					"brand_id": 1490,
					"brand_name": "CATERHAM",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4617,
					"brand_name": "CCM",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4487,
					"brand_name": "CEZETA",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 4424,
					"brand_name": "CF MOTO",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1491,
					"brand_name": "CHECKER",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 2887,
					"brand_name": "CHERY",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 138,
					"brand_name": "CHEVROLET",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Petrol/Ethanol",
					  "Electric",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 20,
					"brand_name": "CHRYSLER",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 21,
					"brand_name": "CITROËN",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Electric",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Petrol/Electric",
					  "Diesel/Electro",
					  "Petrol/Ethanol"
					]
				  },
				  {
					"brand_id": 4803,
					"brand_name": "CPI",
					"fuel_type": [
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4896,
					"brand_name": "CUPRA",
					"fuel_type": [
					  "Petrol",
					  "Petrol/Electric",
					  "Diesel",
					  "Electric"
					]
				  },
				  {
					"brand_id": 139,
					"brand_name": "DACIA",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Petrol/Ethanol",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 4052,
					"brand_name": "DAELIM",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 185,
					"brand_name": "DAEWOO",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 24,
					"brand_name": "DAF",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 25,
					"brand_name": "DAIHATSU",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 26,
					"brand_name": "DAIMLER",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1493,
					"brand_name": "DALLAS",
					"fuel_type": [
					  "Diesel",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1494,
					"brand_name": "DE LOREAN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1495,
					"brand_name": "DE TOMASO",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4071,
					"brand_name": "DERBI",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4269,
					"brand_name": "DFSK",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 29,
					"brand_name": "DODGE",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Ethanol",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 4658,
					"brand_name": "DONKERVOORT",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 3497,
					"brand_name": "DR",
					"fuel_type": [
					  "Petrol",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 4468,
					"brand_name": "DS",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Diesel/Electro",
					  "Electric"
					]
				  },
				  {
					"brand_id": 3999,
					"brand_name": "DS (CAPSA)",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 2759,
					"brand_name": "DUCATI",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 6501,
					"brand_name": "E.F.O",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 5705,
					"brand_name": "E.GO",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 4288,
					"brand_name": "ECM",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 6177,
					"brand_name": "ELARIS",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 6705,
					"brand_name": "ENERGICA",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 5066,
					"brand_name": "ETON",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 2857,
					"brand_name": "EUNOS",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4072,
					"brand_name": "FANTIC",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 700,
					"brand_name": "FERRARI",
					"fuel_type": [
					  "Petrol",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 35,
					"brand_name": "FIAT",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Electric",
					  "Petrol/Electric",
					  "CNG",
					  "Petrol/Ethanol"
					]
				  },
				  {
					"brand_id": 3738,
					"brand_name": "FISKER",
					"fuel_type": [
					  "Electric",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 36,
					"brand_name": "FORD",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Diesel/Electro",
					  "Petrol/Ethanol",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Electric"
					]
				  },
				  {
					"brand_id": 2864,
					"brand_name": "FORD ASIA & OCEANIA",
					"fuel_type": [
					  "Diesel",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1496,
					"brand_name": "FORD AUSTRALIA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 808,
					"brand_name": "FORD OTOSAN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 776,
					"brand_name": "FORD USA",
					"fuel_type": [
					  "Petrol",
					  "Electric",
					  "Diesel",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 3297,
					"brand_name": "FPV",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 775,
					"brand_name": "FSO",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 5076,
					"brand_name": "GAC MOBYLETTE",
					"fuel_type": [
					  "Mixture"
					]
				  },
				  {
					"brand_id": 5475,
					"brand_name": "GALLOPER",
					"fuel_type": [
					  "Diesel"
					]
				  },
				  {
					"brand_id": 4633,
					"brand_name": "GARELLI",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4289,
					"brand_name": "GASGAS",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 148,
					"brand_name": "GAZ",
					"fuel_type": [
					  "Diesel",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 2590,
					"brand_name": "GEELY",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4403,
					"brand_name": "GENERIC",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4473,
					"brand_name": "GENESIS",
					"fuel_type": [
					  "Diesel",
					  "Petrol",
					  "Electric"
					]
				  },
				  {
					"brand_id": 831,
					"brand_name": "GEO",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 4054,
					"brand_name": "GILERA",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1498,
					"brand_name": "GINETTA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5209,
					"brand_name": "GIOTTI VICTORIA",
					"fuel_type": [
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 812,
					"brand_name": "GLAS",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 39,
					"brand_name": "GMC",
					"fuel_type": [
					  "Petrol",
					  "Petrol/Compressed Natural Gas (CNG)"
					]
				  },
				  {
					"brand_id": 4752,
					"brand_name": "GOUPIL",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 4539,
					"brand_name": "GOVECS",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 2903,
					"brand_name": "GREAT WALL",
					"fuel_type": [
					  "Diesel",
					  "Petrol",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 4055,
					"brand_name": "HARLEY-DAVIDSON",
					"fuel_type": [
					  "Petrol",
					  "Electric"
					]
				  },
				  {
					"brand_id": 4087,
					"brand_name": "HERCULES",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4722,
					"brand_name": "HESKET",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 2794,
					"brand_name": "HILLMAN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1499,
					"brand_name": "HINDUSTAN",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 4314,
					"brand_name": "HMRacing",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 1500,
					"brand_name": "HOBBYCAR",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 45,
					"brand_name": "HONDA",
					"fuel_type": [
					  "Petrol",
					  "Mixture",
					  "Diesel",
					  "Petrol/Electric",
					  "Electric",
					  "Petrol/Ethanol",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 4488,
					"brand_name": "HOREX",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 6513,
					"brand_name": "HORWIN",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 3276,
					"brand_name": "HSV",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5067,
					"brand_name": "HUATIAN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1506,
					"brand_name": "HUMMER",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 4287,
					"brand_name": "HUSABERG",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4241,
					"brand_name": "HUSQVARNA",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4056,
					"brand_name": "HYOSUNG",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 183,
					"brand_name": "HYUNDAI",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Electric",
					  "Diesel/Electro",
					  "Hydrogen",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 4325,
					"brand_name": "INDIAN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1507,
					"brand_name": "INDIGO",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 6576,
					"brand_name": "INEOS",
					"fuel_type": [
					  "Diesel",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1526,
					"brand_name": "INFINITI",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 52,
					"brand_name": "INNOCENTI",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 3084,
					"brand_name": "IRAN KHODRO",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1508,
					"brand_name": "IRMSCHER",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1509,
					"brand_name": "ISDERA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 54,
					"brand_name": "ISUZU",
					"fuel_type": [
					  "Diesel",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4073,
					"brand_name": "ITALJET",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 55,
					"brand_name": "IVECO",
					"fuel_type": [
					  "Diesel",
					  "CNG",
					  "Electric"
					]
				  },
				  {
					"brand_id": 4683,
					"brand_name": "IZH",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 56,
					"brand_name": "JAGUAR",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Diesel/Electro",
					  "Petrol/Electric",
					  "Electric"
					]
				  },
				  {
					"brand_id": 4074,
					"brand_name": "JAWA",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 882,
					"brand_name": "JEEP",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Petrol/Ethanol",
					  "Electric",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 1511,
					"brand_name": "JENSEN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4799,
					"brand_name": "JINLUN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1131,
					"brand_name": "KAWASAKI",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4495,
					"brand_name": "KEEWAY",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 184,
					"brand_name": "KIA",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Electric",
					  "Diesel/Electro",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 4163,
					"brand_name": "KOENIGSEGG",
					"fuel_type": [
					  "Petrol/Electric",
					  "Petrol/Ethanol"
					]
				  },
				  {
					"brand_id": 4070,
					"brand_name": "KREIDLER",
					"fuel_type": [
					  "Mixture",
					  "Petrol",
					  "Electric"
					]
				  },
				  {
					"brand_id": 4498,
					"brand_name": "KSR MOTO",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 2760,
					"brand_name": "KTM",
					"fuel_type": [
					  "Petrol",
					  "Mixture",
					  "Electric"
					]
				  },
				  {
					"brand_id": 4472,
					"brand_name": "KUBA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 2621,
					"brand_name": "KYMCO",
					"fuel_type": [
					  "Petrol",
					  "Mixture",
					  "Ethanol"
					]
				  },
				  {
					"brand_id": 63,
					"brand_name": "LADA",
					"fuel_type": [
					  "Petrol",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 701,
					"brand_name": "LAMBORGHINI",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5130,
					"brand_name": "LAMBRETTA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 64,
					"brand_name": "LANCIA",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Petrol/Electric",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 1820,
					"brand_name": "LAND ROVER",
					"fuel_type": [
					  "Diesel",
					  "Petrol",
					  "Diesel/Electro",
					  "Petrol/Electric",
					  "Petrol/Ethanol/Electric",
					  "Petrol/Ethanol"
					]
				  },
				  {
					"brand_id": 2589,
					"brand_name": "LANDWIND (JMC)",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4075,
					"brand_name": "LAVERDA MOTORCYCLES",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1380,
					"brand_name": "LDV",
					"fuel_type": [
					  "Diesel",
					  "Petrol",
					  "Electric",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 5194,
					"brand_name": "LEVC",
					"fuel_type": [
					  "Petrol/Electric",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5068,
					"brand_name": "LEXMOTO",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 842,
					"brand_name": "LEXUS",
					"fuel_type": [
					  "Petrol",
					  "Petrol/Electric",
					  "Diesel",
					  "Electric"
					]
				  },
				  {
					"brand_id": 3086,
					"brand_name": "LIFAN",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 1513,
					"brand_name": "LIGIER",
					"fuel_type": [
					  "Diesel",
					  "Petrol",
					  "Electric"
					]
				  },
				  {
					"brand_id": 1200,
					"brand_name": "LINCOLN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5069,
					"brand_name": "LINGBEN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4231,
					"brand_name": "LML",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 802,
					"brand_name": "LOTUS",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 3047,
					"brand_name": "LTI",
					"fuel_type": [
					  "Diesel",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4494,
					"brand_name": "LUXXON",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4612,
					"brand_name": "LYNK & CO",
					"fuel_type": [
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 1280,
					"brand_name": "MAHINDRA",
					"fuel_type": [
					  "Diesel",
					  "Petrol",
					  "Electric"
					]
				  },
				  {
					"brand_id": 4076,
					"brand_name": "MAICO",
					"fuel_type": [
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4060,
					"brand_name": "MALAGUTI",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 69,
					"brand_name": "MAN",
					"fuel_type": [
					  "Diesel",
					  "Electric"
					]
				  },
				  {
					"brand_id": 1516,
					"brand_name": "MARCOS",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 771,
					"brand_name": "MASERATI",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 4492,
					"brand_name": "MASH",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 3300,
					"brand_name": "MAXUS",
					"fuel_type": [
					  "Diesel",
					  "Electric"
					]
				  },
				  {
					"brand_id": 2164,
					"brand_name": "MAYBACH",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 72,
					"brand_name": "MAZDA",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Diesel/Electro",
					  "Electric"
					]
				  },
				  {
					"brand_id": 4061,
					"brand_name": "MBK",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1518,
					"brand_name": "MCLAREN",
					"fuel_type": [
					  "Petrol",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 845,
					"brand_name": "MEGA",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Electric"
					]
				  },
				  {
					"brand_id": 4618,
					"brand_name": "MEGELLI",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 74,
					"brand_name": "MERCEDES-BENZ",
					"fuel_type": [
					  "Diesel",
					  "Petrol",
					  "Petrol/Electric",
					  "Diesel/Electro",
					  "Electric",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Hydrogen",
					  "Petrol/Ethanol"
					]
				  },
				  {
					"brand_id": 1520,
					"brand_name": "METROCAB",
					"fuel_type": [
					  "Diesel"
					]
				  },
				  {
					"brand_id": 75,
					"brand_name": "MG",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Electric",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 3838,
					"brand_name": "MG (SAIC)",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 3961,
					"brand_name": "MIA ELECTRIC",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 6545,
					"brand_name": "MICRO",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 4219,
					"brand_name": "MICROCAR",
					"fuel_type": [
					  "Diesel",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1521,
					"brand_name": "MIDDLEBRIDGE",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1522,
					"brand_name": "MINELLI",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1523,
					"brand_name": "MINI",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Electric"
					]
				  },
				  {
					"brand_id": 77,
					"brand_name": "MITSUBISHI",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Electric",
					  "Petrol/Ethanol"
					]
				  },
				  {
					"brand_id": 2904,
					"brand_name": "MITSUOKA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4333,
					"brand_name": "MONDIAL",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 803,
					"brand_name": "MORGAN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 78,
					"brand_name": "MORRIS",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 813,
					"brand_name": "MOSKVICH",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4062,
					"brand_name": "MOTO GUZZI",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4077,
					"brand_name": "MOTO-MORINI",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4328,
					"brand_name": "MOTOBI",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4493,
					"brand_name": "MOTOWELL",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 6567,
					"brand_name": "MOTRON",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5204,
					"brand_name": "MPM MOTORS",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 2761,
					"brand_name": "MV AGUSTA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 670,
					"brand_name": "MWM",
					"fuel_type": [
					  "Electric",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4063,
					"brand_name": "MZ",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4780,
					"brand_name": "NIO",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 4334,
					"brand_name": "NIPPONIA",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 80,
					"brand_name": "NISSAN",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Electric",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 4613,
					"brand_name": "NIU",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 3625,
					"brand_name": "NOBLE",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4723,
					"brand_name": "NORTON",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 81,
					"brand_name": "NSU",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1141,
					"brand_name": "OLDSMOBILE",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1527,
					"brand_name": "OLTCIT",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 6350,
					"brand_name": "ONLINE",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 84,
					"brand_name": "OPEL",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Petrol/Electric",
					  "Electric",
					  "Petrol/Ethanol"
					]
				  },
				  {
					"brand_id": 5123,
					"brand_name": "ORA",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 1529,
					"brand_name": "OSCA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4164,
					"brand_name": "PAGANI",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1530,
					"brand_name": "PANOZ",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1531,
					"brand_name": "PANTHER",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1533,
					"brand_name": "PERODUA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 88,
					"brand_name": "PEUGEOT",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Mixture",
					  "Electric",
					  "Petrol/Electric",
					  "Petrol/Ethanol",
					  "Diesel/Electro",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 4078,
					"brand_name": "PGO MOTORCYCLES",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 181,
					"brand_name": "PIAGGIO",
					"fuel_type": [
					  "Petrol",
					  "Mixture",
					  "Diesel",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Electric",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 3046,
					"brand_name": "PININFARINA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 850,
					"brand_name": "PLYMOUTH",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 5152,
					"brand_name": "POLARIS",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4817,
					"brand_name": "POLESTAR",
					"fuel_type": [
					  "Electric",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 774,
					"brand_name": "PONTIAC",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 92,
					"brand_name": "PORSCHE",
					"fuel_type": [
					  "Petrol",
					  "Petrol/Electric",
					  "Electric",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 851,
					"brand_name": "PREMIER",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 778,
					"brand_name": "PROTON",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 1580,
					"brand_name": "PUCH",
					"fuel_type": [
					  "Mixture",
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 4771,
					"brand_name": "QINGQI",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 6687,
					"brand_name": "QJMOTOR",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4346,
					"brand_name": "QUADRO",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 3689,
					"brand_name": "RAM",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1534,
					"brand_name": "RANGER",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1536,
					"brand_name": "RAYTON FISSORE",
					"fuel_type": [
					  "Diesel"
					]
				  },
				  {
					"brand_id": 4377,
					"brand_name": "REGAL RAPTOR",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 773,
					"brand_name": "RELIANT",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 93,
					"brand_name": "RENAULT",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Electric",
					  "Petrol/Electric",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Petrol/Ethanol",
					  "Diesel/Electro",
					  "Petrol/Compressed Natural Gas (CNG)"
					]
				  },
				  {
					"brand_id": 694,
					"brand_name": "RENAULT TRUCKS",
					"fuel_type": [
					  "Diesel"
					]
				  },
				  {
					"brand_id": 3555,
					"brand_name": "REVA",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 4290,
					"brand_name": "REX",
					"fuel_type": [
					  "Petrol",
					  "Mixture",
					  "Electric"
					]
				  },
				  {
					"brand_id": 1539,
					"brand_name": "RILEY",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4404,
					"brand_name": "RIVERO",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 705,
					"brand_name": "ROLLS-ROYCE",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 95,
					"brand_name": "ROVER",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 4386,
					"brand_name": "ROYAL ENFIELD",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4152,
					"brand_name": "RUF",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 99,
					"brand_name": "SAAB",
					"fuel_type": [
					  "Petrol",
					  "Petrol/Ethanol",
					  "Diesel",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4064,
					"brand_name": "SACHS",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5078,
					"brand_name": "SANBEN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4079,
					"brand_name": "SANGLAS",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 171,
					"brand_name": "SANTANA",
					"fuel_type": [
					  "Diesel"
					]
				  },
				  {
					"brand_id": 1545,
					"brand_name": "SAO",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4624,
					"brand_name": "SCOMADI",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 104,
					"brand_name": "SEAT",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Petrol/Electric",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Electric"
					]
				  },
				  {
					"brand_id": 6178,
					"brand_name": "SEVIC",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 4497,
					"brand_name": "SFM",
					"fuel_type": [
					  "Petrol",
					  "Mixture",
					  "Electric"
					]
				  },
				  {
					"brand_id": 1547,
					"brand_name": "SHELBY",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5133,
					"brand_name": "SHERCO",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4065,
					"brand_name": "SIMSON",
					"fuel_type": [
					  "Mixture"
					]
				  },
				  {
					"brand_id": 1548,
					"brand_name": "SIPANI",
					"fuel_type": [
					  "Diesel",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 106,
					"brand_name": "SKODA",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Electric",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Petrol/Ethanol",
					  "CNG"
					]
				  },
				  {
					"brand_id": 1138,
					"brand_name": "SMART",
					"fuel_type": [
					  "Petrol",
					  "Electric",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 675,
					"brand_name": "SOLO",
					"fuel_type": [
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4400,
					"brand_name": "SOMMER MOTORCYCLES",
					"fuel_type": [
					  "Diesel"
					]
				  },
				  {
					"brand_id": 1549,
					"brand_name": "SPECTRE",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 2755,
					"brand_name": "SPYKER",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 175,
					"brand_name": "SSANGYONG",
					"fuel_type": [
					  "Diesel",
					  "Petrol",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Electric"
					]
				  },
				  {
					"brand_id": 1550,
					"brand_name": "STANDARD AUTOMOBILE",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 176,
					"brand_name": "STEYR",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4015,
					"brand_name": "STREETSCOOTER",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 107,
					"brand_name": "SUBARU",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Electric"
					]
				  },
				  {
					"brand_id": 2716,
					"brand_name": "SUNBEAM",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 6479,
					"brand_name": "SUPER SOCO",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 109,
					"brand_name": "SUZUKI",
					"fuel_type": [
					  "Petrol",
					  "Mixture",
					  "Diesel",
					  "Petrol/Electric"
					]
				  },
				  {
					"brand_id": 4407,
					"brand_name": "SWM",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4245,
					"brand_name": "SYM",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 110,
					"brand_name": "TALBOT",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 178,
					"brand_name": "TATA",
					"fuel_type": [
					  "Diesel",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 3443,
					"brand_name": "TAZZARI",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 3328,
					"brand_name": "TESLA",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 4401,
					"brand_name": "TGB",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 3514,
					"brand_name": "THINK",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 6690,
					"brand_name": "TINBOT",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 1551,
					"brand_name": "TOFAS",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4425,
					"brand_name": "TOMOS",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4080,
					"brand_name": "TORNAX",
					"fuel_type": [
					  "Mixture"
					]
				  },
				  {
					"brand_id": 111,
					"brand_name": "TOYOTA",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Electric",
					  "Hydrogen"
					]
				  },
				  {
					"brand_id": 187,
					"brand_name": "TRABANT",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 112,
					"brand_name": "TRIUMPH",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 4496,
					"brand_name": "TURBHO",
					"fuel_type": [
					  "Petrol",
					  "Mixture"
					]
				  },
				  {
					"brand_id": 861,
					"brand_name": "TVR",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1553,
					"brand_name": "UAZ",
					"fuel_type": [
					  "Petrol",
					  "Diesel"
					]
				  },
				  {
					"brand_id": 4555,
					"brand_name": "UM",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1554,
					"brand_name": "UMM",
					"fuel_type": [
					  "Diesel",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 117,
					"brand_name": "VAUXHALL",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "Petrol/Electric",
					  "Electric",
					  "Petrol/Compressed Natural Gas (CNG)"
					]
				  },
				  {
					"brand_id": 1555,
					"brand_name": "VECTOR",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4379,
					"brand_name": "VENTO",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4069,
					"brand_name": "VESPA",
					"fuel_type": [
					  "Mixture",
					  "Petrol",
					  "Electric"
					]
				  },
				  {
					"brand_id": 4324,
					"brand_name": "VICTORY MOTORCYCLES",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5733,
					"brand_name": "VOGE",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 120,
					"brand_name": "VOLVO",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Petrol/Electric",
					  "Diesel/Electro",
					  "Petrol/Ethanol",
					  "Electric",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Petrol/Liquified Petroleum Gas (LPG)"
					]
				  },
				  {
					"brand_id": 4607,
					"brand_name": "VUHL",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 121,
					"brand_name": "VW",
					"fuel_type": [
					  "Petrol",
					  "Diesel",
					  "Electric",
					  "Petrol/Electric",
					  "Petrol/Compressed Natural Gas (CNG)",
					  "Petrol/Ethanol",
					  "Petrol/Liquified Petroleum Gas (LPG)",
					  "CNG",
					  "Diesel/Electro"
					]
				  },
				  {
					"brand_id": 4323,
					"brand_name": "WANGYE",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 186,
					"brand_name": "WARTBURG",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 907,
					"brand_name": "WESTFIELD",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1558,
					"brand_name": "WIESMANN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1541,
					"brand_name": "WOLSELEY",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 6398,
					"brand_name": "XBUS",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 6588,
					"brand_name": "XEV",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 4667,
					"brand_name": "XGJAO",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5077,
					"brand_name": "XINGYUE",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 6688,
					"brand_name": "YADEA",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 1021,
					"brand_name": "YAMAHA",
					"fuel_type": [
					  "Petrol",
					  "Mixture",
					  "Electric"
					]
				  },
				  {
					"brand_id": 5065,
					"brand_name": "YIBEN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 3193,
					"brand_name": "YUEJIN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 2816,
					"brand_name": "YUGO",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1559,
					"brand_name": "YULON",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 124,
					"brand_name": "ZASTAVA",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 1139,
					"brand_name": "ZAZ",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4604,
					"brand_name": "ZENOS CARS",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 4322,
					"brand_name": "ZERO",
					"fuel_type": [
					  "Electric"
					]
				  },
				  {
					"brand_id": 4802,
					"brand_name": "ZNEN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 5062,
					"brand_name": "ZONGSHEN",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 6502,
					"brand_name": "ZONTES",
					"fuel_type": [
					  "Petrol"
					]
				  },
				  {
					"brand_id": 678,
					"brand_name": "ZUENDAPP",
					"fuel_type": [
					  "Mixture",
					  "Petrol"
					]
				  }
				],
				"message": "Successful."
			  }';

			$jsonDecode = json_decode($json, true);

			$vehiclesBrands = $jsonDecode['data'];

			$vehicleClass = ClassRegistry::init('Vehicle');

			foreach ($vehiclesBrands as $vehicleBrand) {
				$vehicleBd = $vehicleClass->findByNameEn($vehicleBrand['brand_name']);

				if (!$vehicleBd) {
					$vehicleArray = array(
						'Vehicle' => array(
							'name_en' => $vehicleBrand['brand_name'],
							'name_fr' => $vehicleBrand['brand_name'],
							'name_de' => $vehicleBrand['brand_name'],
							'name_nl' => $vehicleBrand['brand_name']
						)
					);

					$vehicleClass->new_vehicle($vehicleArray);
				}
			}
		} catch (Exception $e) {
			CakeLog::debug(print_r("Lobster - Vehicles sync - An error has occured: " . $e->getMessage(), true));
			return false;
		}
	}

	public function citiesSaveCoordinates()
	{
		try {
			ClassRegistry::init('Garage');
			$cityClass = ClassRegistry::init('City');
			$citiesToUpdate = $cityClass->find('all', array(
				'joins' => array(
					array(
						'table' => 'provinces',
						'alias' => 'Province',
						'type' => 'INNER',
						'conditions' => array(
							'Province.id = City.province_id'
						)
					),
					array(
						'table' => 'countries',
						'alias' => 'Country',
						'type' => 'INNER',
						'conditions' => array(
							'Country.id = Province.country_id'
						)
					)
				),
				'conditions' => array(
					'OR' => array(
						'City.latitude IS NULL',
						'City.longitude IS NULL'
					)
				),
				'fields' => array(
					'City.id',
					'City.name as city_name',
					'Province.name as province_name',
					'Country.name as country_name'
				),
				'order' => 'City.id',
				'limit' => 1000
			));
			CakeLog::config('script-save-cities-coordinates', array(
				'engine' => 'FileLog',
				'types' => array('info', 'error', 'warning'),
				'scopes' => array('script-save-cities-coordinates'),
				'file' => 'script-save-cities-coordinates.log',
			));
			$count = 0;
			foreach ($citiesToUpdate as $city) {
				$coords = Garage::getLatitudeLongitudeData(
					trim($city['Country']['country_name']),
					trim($city['Province']['province_name']),
					trim($city['City']['city_name']),
					'', // postcode
					'' // address
				);

				if (!empty($coords['latitude'])) {
					$city_save = array(
						'City' => array(
							'id' => $city['City']['id'],
							'latitude' => $coords['latitude'],
							'longitude' => $coords['longitude'],
						)
					);
					$cityClass->save($city_save, array('fieldList' => array('latitude', 'longitude')));
					CakeLog::write('script-save-cities-coordinates', 'City updated with id ' . $city['City']['id']);
				} else {
					CakeLog::write('script-save-cities-coordinates', 'City NOT updated with id ' . $city['City']['id'] . ' ' . implode(' # ', array(
						trim($city['Country']['country_name']),
						trim($city['Province']['province_name']),
						trim($city['City']['city_name'])
					)));
				}
				$count++;
				if ($count % 100 == 0) {
					echo " # " . $count;
					sleep(5);
				}
			}
		} catch (Exception $e) {
			CakeLog::debug(print_r("Lobster - Cities save coordinates - An error has occured: " . $e->getMessage(), true));
			return false;
		}
	}

	private function updateFleet($updatedFleet, $oldFleet, $locationData)
	{
		try {
			$provinceClass = ClassRegistry::init('Province');
			$countryClass = ClassRegistry::init('Country');
			$fleetClass = ClassRegistry::init('Fleet');
			$city = $locationData['city'];
			$province = $locationData['province'];
			if (!empty($city) && !isset($province['Province']) && !isset($locationData['country'])) {
				$province = $provinceClass->findById($city['City']['province_id']);
				$country = $countryClass->findById($province['Province']['country_id']);
			}

			//Check if location variables are defined and not null/empty.
			if (!empty($locationData['address1']) && !empty($locationData['postcode']) && !empty($city) && !empty($locationData['cityName']) && isset($country)) {

				//Check if location fields are not empty to update them
				if (!empty($locationData['address1'])) {
					$oldFleet['Fleet']['address1'] = $locationData['address1'];
				}
				if (!empty($locationData['postcode'])) {
					$oldFleet['Fleet']['postcode'] = $locationData['postcode'];
				}
				if (!empty($city['City']['province_id'])) {
					$oldFleet['Fleet']['province_id'] = $city['City']['province_id'];
				}
				if (!empty($country['Country']['id'])) {
					$oldFleet['Fleet']['country_id'] = $country['Country']['id'];
				}
			}

			//update fields
			if (isset($updatedFleet['company_code']) && !empty($updatedFleet['company_code']) && ($oldFleet['Fleet']['company_code'] != $updatedFleet['company_code'])) {
				$oldFleet['Fleet']['company_code'] = $updatedFleet['company_code'];
			}

			if (isset($updatedFleet['BusinessName']) && !empty($updatedFleet['BusinessName']) && ($oldFleet['Fleet']['name'] != $updatedFleet['BusinessName'])) {
				$oldFleet['Fleet']['name'] = $updatedFleet['BusinessName'];
			}

			return $fleetClass->edit_fleet_lobster($oldFleet);
		} catch (Exception $e) {
			CakeLog::debug(print_r("Lobster - Update fleet - An error has occured: " . $e->getMessage(), true));
			return false;
		}
	}

	private function addFleet($newFleet, $aag_region_id, $erp_id, $city)
	{
		try {
			$fleetClass = ClassRegistry::init('Fleet');
			if (!empty($city) && !isset($province) && !isset($country)) {
				$provinceClass = ClassRegistry::init('Province');
				$countryClass = ClassRegistry::init('Country');
				$province = $provinceClass->findById($city['City']['province_id']);
				$country = $countryClass->findById($province['Province']['country_id']);
			}
			$fleet = array(
				'Fleet' => array(
					'name' => isset($newFleet['BusinessName']) && !empty($newFleet['BusinessName']) ? $newFleet['BusinessName'] : null,
					'address' => isset($newFleet['Addr1']) && !empty($newFleet['Addr1']) ? $newFleet['Addr1'] : null,
					'postcode' => isset($newFleet['AddrPCode']) && !empty($newFleet['AddrPCode']) ? $newFleet['AddrPCode'] : null,
					'country_id' => isset($country['Country']['id']) && !empty($country['Country']['id']) ? $country['Country']['id'] : null,
					'province_id' => isset($province['Province']['id']) && !empty($province['Province']['id']) ? $province['Province']['id'] : null,
					'erp_id' => $erp_id,
					'ref_code' => $newFleet['IdErp'],
					'payment_terms' => isset($newFleet['payment_terms']) && !empty($newFleet['payment_terms']) ? $newFleet['payment_terms'] : null,
					'tax_code' => isset($newFleet['tax_code']) && !empty($newFleet['tax_code']) ? $newFleet['tax_code'] : null,
					'company_code' => isset($newFleet['company_code']) && !empty($newFleet['company_code']) ? $newFleet['company_code'] : $newFleet['BusinessName'],
					'company_name' => isset($newFleet['company_name']) && !empty($newFleet['company_name']) ? $newFleet['company_name'] : $newFleet['BusinessName'],
					'aag_region_id' => $aag_region_id,
					'networks' => DEFAULT_NETWORK_FLEETS,
					'active' => ConstantsBooleans::YES
				)
			);
			return $fleetClass->add_fleet($fleet);
		} catch (Exception $e) {
			CakeLog::debug(print_r("Lobster - Add fleet - An error has occured: " . $e->getMessage(), true));
			return false;
		}
	}
}
