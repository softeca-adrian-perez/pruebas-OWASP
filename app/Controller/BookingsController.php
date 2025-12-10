<?php
App::uses('Leadgen', 'Lib');
App::uses('Kiyoh', 'Lib');
App::uses('MessagingSMS', 'Lib');
App::uses('ShortnerUrlApi', 'Lib');
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class BookingsController extends AppController
{
    public $uses = array(
        'Booking',
        'GarageNetwork',
        'Network',
        'Quotation',
        'Work',
        'Garage',
        'Email',
        'Distributor',
        'GarageDistributor',
        'Sms',
        'SmsTemplate',
        'City',
        'Province',
        'Country',
        'ShortnerUrl',
        'DistanceUnit',
		'Contact'
    );

    /**
     * API to create a new Booking.
     */
    public function create_booking()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && !$this->request->is('get')) {
            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;

            //AGN sends us a guid as the garage_id, so we turn it into the id
            $garageId = null;
            if (isset($dataReceived->garage_id)) {
                $garage = $this->Garage->findByGuid($dataReceived->garage_id, ['id']);
                if (isset($garage['Garage']['id'])) {
                    $garageId = $garage['Garage']['id'];
                }
            }

            $date = isset($dataReceived->date) ? $dataReceived->date : null;
            $time = isset($dataReceived->time) ? $dataReceived->time : null;
            $timeTo = isset($dataReceived->time_to) ? $dataReceived->time_to : null;
            $quotationId = isset($dataReceived->quotation_id) && !empty($dataReceived->quotation_id) ? $dataReceived->quotation_id : null;
            $quotationIdLeadGen = isset($dataReceived->quotation_id_leadgen) && !empty($dataReceived->quotation_id_leadgen) ? $dataReceived->quotation_id_leadgen : null;
            $customerName = isset($dataReceived->customer_name) ? $dataReceived->customer_name : null;
            $customerPhone = isset($dataReceived->customer_phone) && !empty($dataReceived->customer_phone) ? $dataReceived->customer_phone : null;
            $customerEmail = isset($dataReceived->customer_email) ? $dataReceived->customer_email : null;
            $plate = isset($dataReceived->plate) ? $dataReceived->plate : null;
            $vin = isset($dataReceived->vin) ? $dataReceived->vin : null;
            $brand = isset($dataReceived->brand) ? $dataReceived->brand : null;
            $model = isset($dataReceived->model) ? $dataReceived->model : null;
            $version = isset($dataReceived->version) ? $dataReceived->version : null;
            $motExpDate = isset($dataReceived->mot_exp_date) ? new DateTime($dataReceived->mot_exp_date) : null;
            $fuel = isset($dataReceived->fuel) ? $dataReceived->fuel : null;
            $registeredOn = isset($dataReceived->registered_on) ? new DateTime($dataReceived->registered_on) : null;
            $mileage = isset($dataReceived->mileage) ? $dataReceived->mileage : null;
            $workCode = isset($dataReceived->work_id) ? $dataReceived->work_id : null;
            $additionalInfo = isset($dataReceived->additional_info) ? $dataReceived->additional_info : null;
            $bookingStatus = isset($dataReceived->booking_status) ? $dataReceived->booking_status : ConstantsBookingsStatus::PENDING;
            $customerLanguageCode = isset($dataReceived->customer_language_code) ? $dataReceived->customer_language_code : null;
            $childNetworkId = isset($dataReceived->child_network_id) ? $dataReceived->child_network_id : null;

            //If its a child network booking, network its validated, if the child network is CV, set to null the not needed vars
            if (!isset($childNetworkId) || !$this->Network->validateGnmChildNetworkId($childNetworkId, $networkId)) {
                $childNetworkId = null;
            } elseif ($this->Network->isCvNetworkById($childNetworkId)) {
                $quotationId = $quotationIdLeadGen = $plate = $vin = $brand = $model = $version = $motExpDate = $fuel = $registeredOn = $mileage = $workIdBd = null;
            }


            // if "marketing_acceptance" isn't set, it's set to false
            $marketingAcceptance = isset($dataReceived->marketing_acceptance) ? $dataReceived->marketing_acceptance : false;

            // if it's only booking "quotation_id" it's empty, "customer_phone" it's optional
            if (empty($networkId) || empty($garageId) || empty($date) || empty($time) || empty($timeTo) || empty($customerEmail)) {
                exit;
            }

            $garage = $this->Garage->findById($garageId);

            $actualDate = new DateTime();

            $workIdBd = null;
            if (!empty($workCode)) {
                $work = $this->Work->findByNetworkIdAndCode($networkId, $workCode);
                $workIdBd = !empty($work) ? $work['Work']['id'] : null;
            }

            $booking = array(
                'Booking' => array(
                    'network_id' => $networkId,
                    'garage_id' => $garageId,
                    'garage_name' => isset($garage['Garage']['name']) ? $garage['Garage']['name'] : null,
                    'address1' => isset($garage['Garage']['address1']) ? $garage['Garage']['address1'] : null,
                    'address2' => isset($garage['Garage']['address2']) ? $garage['Garage']['address2'] : null,
                    'address3' => isset($garage['Garage']['address3']) ? $garage['Garage']['address3'] : null,
                    'address4' => isset($garage['Garage']['address4']) ? $garage['Garage']['address4'] : null,
                    'town' => isset($garage['Garage']['town']) ? $garage['Garage']['town'] : null,
                    'province' => isset($garage['Garage']['province']) ? $garage['Garage']['province'] : null,
                    'postcode' => isset($garage['Garage']['postcode']) ? $garage['Garage']['postcode'] : null,
                    'date' => $date,
                    'time' => $time,
                    'time_to' => $timeTo,
                    'creation_date' => $actualDate->format('Y-m-d H:i:s'),
                    'quotation_id' => $quotationId,
                    'quotation_id_leadgen' => $quotationIdLeadGen,
                    'customer_name' => $customerName,
                    'customer_phone' => $customerPhone,
                    'customer_email' => $customerEmail,
                    'marketing_acceptance' => $marketingAcceptance,
                    'plate' => $plate,
                    'vin' => $vin,
                    'brand' => $brand,
                    'model' => $model,
                    'version' => $version,
                    'mot_exp_date' => isset($motExpDate) ? $motExpDate->format('Y-m-d H:i:s') : null,
                    'fuel' => $fuel,
                    'registered_on' => isset($registeredOn) ? $registeredOn->format('Y-m-d H:i:s') : null,
                    'mileage' => $mileage,
                    'work_id' => $workIdBd,
                    'child_network_id' => $childNetworkId,
                    'additional_info' => $additionalInfo,
                    'booking_status' => $bookingStatus,
                )
            );

            $encryptedCustomerName = Texto::encryptDecryptText($booking['Booking']['customer_name'], true);
            $encryptedCustomerPhone = Texto::encryptDecryptText($booking['Booking']['customer_phone'], true);
            $encryptedCustomerEmail = Texto::encryptDecryptText($booking['Booking']['customer_email'], true);
            $encryptedPlate = isset($booking['Booking']['plate']) ? Texto::encryptDecryptText($booking['Booking']['plate'], true) : null;
            $encryptedVin = isset($booking['Booking']['vin']) ? Texto::encryptDecryptText($booking['Booking']['vin'], true) : null;

            $booking['Booking']['customer_name'] = $encryptedCustomerName;
            $booking['Booking']['customer_phone'] = $encryptedCustomerPhone;
            $booking['Booking']['customer_email'] = $encryptedCustomerEmail;
            $booking['Booking']['plate'] = $encryptedPlate;
            $booking['Booking']['vin'] = $encryptedVin;

            $bookingBd = $this->Booking->save($booking);

            if ($bookingBd) {
                $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkId($garageId, $networkId);

                $garage = $this->Garage->findById($garageId);

                if ($garage && !empty($garage['Garage']['city_id'])) {
                    $city = $this->City->findById($garage['Garage']['city_id']);

                    if ($city) {
                        $garage['Garage']['city'] = $city['City']['name'];
                    }
                }

                $garageEmails = $this->Garage->getGarageEmail($garage);

                $workName = "";
                if (!empty($bookingBd['Booking']['work_id'])) {
                    $work = $this->Work->findById($bookingBd['Booking']['work_id']);
                    $workName = $work ? $work['Work']['name_' . __l()] : '';
                }
                $bookingBd['Booking']['work_name'] = $workName;

                $networkBd = $this->Network->findById($networkId);

                $distanceUnit = $this->DistanceUnit->findById($networkBd['Network']['distance_unit_id']);
                $networkBd['Network']['distance_unit'] = $distanceUnit['DistanceUnit']['unit'];

                $quotationDetails = array();
                $sendDistributorEmail = false;

                // for Booking Garage or a Booking Customer email, quotation details need to be retrieved from Leadgen
                if (!empty($bookingBd['Booking']['quotation_id_leadgen'])) {
                    $leadgen = new Leadgen();
                    $quotationDetails = $leadgen->getQuotationDetails($bookingBd['Booking']['quotation_id_leadgen']);

                    if (!empty($quotationDetails) && isset($quotationDetails['quotation_jobs'][0], $quotationDetails['totals'])) {
                        $mandatoryGenarts = $quotationDetails['quotation_jobs']['0']['mandatory_genarts'] ?? [];
                        $extraGenarts = $quotationDetails['quotation_jobs']['0']['extra_genarts'] ?? [];
                        $sendEmail = true;

                        // round quotations prices
                        $quotationDetails['quotation_jobs']['0']['labour'] = isset($quotationDetails['quotation_jobs']['0']['labour']) ? $this->roundPrices($quotationDetails['quotation_jobs']['0']['labour']) : 0;
                        $quotationDetails['quotation_jobs']['0']['labour_no_vat'] = isset($quotationDetails['quotation_jobs']['0']['labour_no_vat']) ? $this->roundPrices($quotationDetails['quotation_jobs']['0']['labour_no_vat']) : 0;
                        $quotationDetails['quotation_jobs']['0']['total_price'] = isset($quotationDetails['quotation_jobs']['0']['total_price']) ? $this->roundPrices($quotationDetails['quotation_jobs']['0']['total_price']) : 0;
                        $quotationDetails['quotation_jobs']['0']['total_price_no_vat'] = isset($quotationDetails['quotation_jobs']['0']['total_price_no_vat']) ? $this->roundPrices($quotationDetails['quotation_jobs']['0']['total_price_no_vat']) : 0;
                        $quotationDetails['quotation_jobs']['0']['total_price_discount'] = isset($quotationDetails['quotation_jobs']['0']['total_price_discount']) ? $this->roundPrices($quotationDetails['quotation_jobs']['0']['total_price_discount']) : 0;
                        $quotationDetails['quotation_jobs']['0']['total_price_discount_no_vat'] = isset($quotationDetails['quotation_jobs']['0']['total_price_discount_no_vat']) ? $this->roundPrices($quotationDetails['quotation_jobs']['0']['total_price_discount_no_vat']) : 0;
                        $quotationDetails['quotation_jobs']['0']['total_price_conversion_table'] = isset($quotationDetails['quotation_jobs']['0']['total_price_conversion_table']) ? $this->roundPrices($quotationDetails['quotation_jobs']['0']['total_price_conversion_table']) : 0;

                        // round totals prices
                        $quotationDetails['totals']['total_price'] = isset($quotationDetails['totals']['total_price']) ? $this->roundPrices($quotationDetails['totals']['total_price']) : 0;
                        $quotationDetails['totals']['total_price_no_vat'] = isset($quotationDetails['totals']['total_price_no_vat']) ? $this->roundPrices($quotationDetails['totals']['total_price_no_vat']) : 0;
                        $quotationDetails['totals']['total_price_discount'] = isset($quotationDetails['totals']['total_price_discount']) ? $this->roundPrices($quotationDetails['totals']['total_price_discount']) : 0;
                        $quotationDetails['totals']['total_price_discount_no_vat'] = isset($quotationDetails['totals']['total_price_discount_no_vat']) ? $this->roundPrices($quotationDetails['totals']['total_price_discount_no_vat']) : 0;

                        foreach ($mandatoryGenarts as $mandatoryGenart) {
                            if (empty($mandatoryGenart['price'])) {
                                $sendEmail = false;
                                break;
                            }
                        }
                        // round quotations prices from genarts to 2 decimals
                        if (!empty($quotationDetails['quotation_jobs']['0']['mandatory_genarts'])) {
                            foreach ($quotationDetails['quotation_jobs']['0']['mandatory_genarts'] as &$mandatoryGenartsPrices) {
                                $mandatoryGenartsPrices['price'] = isset($mandatoryGenartsPrices['price']) ? $this->roundPrices($mandatoryGenartsPrices['price']) : 0;
                                $mandatoryGenartsPrices['price_no_vat'] = isset($mandatoryGenartsPrices['price_no_vat']) ? $this->roundPrices($mandatoryGenartsPrices['price_no_vat']) : 0;
                                $mandatoryGenartsPrices['price_discount'] = isset($mandatoryGenartsPrices['price_discount']) ? $this->roundPrices($mandatoryGenartsPrices['price_discount']) : 0;
                                $mandatoryGenartsPrices['price_discount_no_vat'] = isset($mandatoryGenartsPrices['price_discount_no_vat']) ? $this->roundPrices($mandatoryGenartsPrices['price_discount_no_vat']) : 0;
                            }
                        }

                        if ($sendEmail) {
                            foreach ($extraGenarts as $extraGenart) {
                                if (empty($extraGenart['price'])) {
                                    $sendEmail = false;
                                    break;
                                }
                            }
                            // round quotations prices from extra genarts to 2 decimals
                            if (!empty($quotationDetails['quotation_jobs']['0']['extra_genarts'])) {
                                foreach ($quotationDetails['quotation_jobs']['0']['extra_genarts'] as &$extraGenartsPrices) {
                                    $extraGenartsPrices['price'] = isset($extraGenartsPrices['price']) ? $this->roundPrices($extraGenartsPrices['price']) : 0;
                                    $extraGenartsPrices['price_no_vat'] = isset($extraGenartsPrices['price_no_vat']) ? $this->roundPrices($extraGenartsPrices['price_no_vat']) : 0;
                                    $extraGenartsPrices['price_discount'] = isset($extraGenartsPrices['price_discount']) ? $this->roundPrices($extraGenartsPrices['price_discount']) : 0;
                                    $extraGenartsPrices['price_discount_no_vat'] = isset($extraGenartsPrices['price_discount_no_vat']) ? $this->roundPrices($extraGenartsPrices['price_discount_no_vat']) : 0;
                                }
                            }
                        }
                        $sendDistributorEmail = $sendEmail;
                    }
                }

                if (!empty($garageEmails)) {
                    foreach ($garageEmails as $garageEmail) {
                        $this->Email->newEmailBookingGarage($bookingBd, $garageEmail, $garageNetwork['GarageNetwork']['id'], $garage, $networkBd, $quotationDetails);
                    }
                }

				if (isset($networkBd['Network']['email_booking_contact_id']) && !empty($networkBd['Network']['email_booking_contact_id'])) {
					$contact = $this->Contact->getContactEmail($networkBd['Network']['email_booking_contact_id']);
					if (isset($contact)) {
						$this->Email->newEmailBookingGarage($bookingBd, $contact, $garageNetwork['GarageNetwork']['id'], $garage, $networkBd, $quotationDetails);
					}
				}

                // only one email is sent to the customer
                $this->Email->newEmailBookingCustomer($bookingBd, $garage, $networkBd, $quotationDetails, $customerLanguageCode);

                if (!empty($bookingBd['Booking']['quotation_id']) && $bookingBd['Booking']['network_id'] == NETWORK_ID_AGN && $sendDistributorEmail && !empty($bookingBd['Booking']['work_id'])) {

                    // if it's Dealer work or if work has a genart of type fluid or standart, distributor email is sent
                    if (strpos($workCode, 'H') === ConstantsBooleans::NO) {
                        $sendDistributorEmail = true;
                    } else {
                        $genartInWork = $this->Work->workHasStandardFluidsGenarts($bookingBd['Booking']['work_id']);

                        $sendDistributorEmail = $genartInWork ? true : false;
                    }

                    if ($sendDistributorEmail) {
                        $garageDistributors = $this->GarageDistributor->getAllByGarageId($garageId);
                        $arrayDistributorEmails = array();
                        foreach ($garageDistributors as $garageDistributor) {
                            $arrayDistributorEmails[] = $garageDistributor['Distributor']['email'] . ' ' . $garageDistributor['Distributor']['language_id'];

                            if (!empty($garageDistributor['Distributor']['email'])) {
                                $this->Email->newEmailBookingDistributor($bookingBd, $garageDistributor, $garage, $networkBd, $quotationDetails);
                            }
                        }
                    }
                }
            }

            $bookingBd['Booking']['customer_name'] = Texto::encryptDecryptText($booking['Booking']['customer_name'], false);
            $bookingBd['Booking']['customer_phone'] = Texto::encryptDecryptText($booking['Booking']['customer_phone'], false);
            $bookingBd['Booking']['customer_email'] = Texto::encryptDecryptText($booking['Booking']['customer_email'], false);
            $bookingBd['Booking']['plate'] = isset($booking['Booking']['plate']) ? Texto::encryptDecryptText($booking['Booking']['plate'], false) : null;
            $bookingBd['Booking']['vin'] = isset($booking['Booking']['vin']) ? Texto::encryptDecryptText($booking['Booking']['vin'], false) : null;

            $result = array(
                'success' => $bookingBd,
                'error' => $bookingBd ? '' : "Booking can\'t be saved"
            );

            if ($result['success']) {
                $province = $this->Province->findById($garage['Garage']['province_id']);
                if ($province) {
                    $country = $this->Country->findById($province['Province']['country_id']);
                    if ($country) {
                        $phone = null;
                        if (!empty($garage['Garage']['mobile'])) {
                            $garage['Garage']['mobile'] = preg_replace('/[\s\-\.\/]/', '', $garage['Garage']['mobile']);
                            if (substr($garage['Garage']['mobile'], 0, 1) === '0') {
                                $garage['Garage']['mobile'] = substr($garage['Garage']['mobile'], 1);
                            }
                            $phone = $garage['Garage']['mobile'];
                        }
                        $sms_template = $this->SmsTemplate->findByCountryIdAndTemplateTypeId($country['Country']['id'], ConstantsSmsTemplateTypes::BOOKING);
                        if ($sms_template) {
                            $sms_license = $this->Sms->findByCountryId($country['Country']['id']);
                            if ($sms_license && isset($sms_license['Sms'])) {
                                $sms_user = Texto::encryptDecryptText($sms_license['Sms']['username_api']);
                                $sms_password = Texto::encryptDecryptText($sms_license['Sms']['password_api']);
                                $sms_code = Texto::encryptDecryptText($sms_license['Sms']['license_api']);
                            }
                            $workName = $bookingBd['Booking']['work_name'];
                            $url_short = $this->shortnerUrlApiConvert($country['Country']['id'], $bookingBd['Booking']['id']);
                            $text = $this->Booking->getVariablesValues($sms_template['SmsTemplate']['description'], $bookingBd['Booking']['id'], $garage['Garage']['name'], $networkBd['Network']['name'], $workName, $url_short);
                            $sender = $sms_template['SmsTemplate']['sender'];
                            if (!empty($phone && $text && $sms_user && $sms_password && $sms_code)) {
                                MessagingSMS::enviar_sms($phone, $text, $sender, null, $sms_code, $sms_user, $sms_password);
                            }
                        }
                    }
                }

                if ($networkId == NETWORK_ID_GV || $networkId == NETWORK_ID_GC) {
                    $email = Texto::encryptDecryptText($booking['Booking']['customer_email'], false);
                    $garageNetwork = $this->GarageNetwork->find(
                        'first',
                        array('conditions' => array(
                            'GarageNetwork.network_id' => $booking['Booking']['network_id'],
                            'GarageNetwork.garage_id' => $booking['Booking']['garage_id']
                        ))
                    );
                    $locationId = $garageNetwork['GarageNetwork']['location_id'];
                    $name = Texto::encryptDecryptText($booking['Booking']['customer_name'], false);
                    Kiyoh::requestReview($email, $locationId, $name);
                }
            }

            // if the booking was successful return the guid instead of the raw id
            if ($bookingBd) {
                $result['success']['Booking']['garage_id'] = $dataReceived->garage_id;
            }
            return $this->returnJsonResult($result);
        }
    }

    /**
     * View to get all bookings of a garage.
     *
     * @param garageNetworkId $bookings
     */
    public function home($garageNetworkId)
    {
        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

        if (!$garageNetwork) {
            throw new UnauthorizedException();
        }

        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));

        if (!$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $networkId = $garageNetwork['GarageNetwork']['network_id'];

        $user = $this->Acceso->user();
        $userAagRegionId = $user['aag_region_id'];

        $garages = $this->Garage->find('list', array(
            'conditions' => array(
                'Garage.aag_region_id' => $userAagRegionId,
                'Garage.id' => $garageId
            )
        ));

        $searcher = $this->request->query;
        $this->request->data['Search'] = $searcher;
        if (!empty($searcher['customer_name'])) {
            $searcher['customer_name'] = Texto::encryptDecryptText($searcher['customer_name'], true);
        }
        $conditions = $this->Booking->conditions($searcher);

        if (empty($searcher)) {
            $conditions[] = array('garage_id' => $garageId);
        }

        if ($user['role_id'] == ConstantsRoles::GARAGE) {
            $conditions[] = array('Booking.garage_id' => $user['garage_id']);
            $searcher['garage_id'] = $user['garage_id'];
        }

        $bookingsGarage = $this->custom_pagination(
            $this->Booking->query('search'),
            $conditions,
            ConstantsPagination::SIZE_PAGE_SMALL,
            'Booking',
            null,
            'PaginatorOrderCustom'

        );

        foreach ($bookingsGarage as &$bookingGarage) {
            $quotationExist = false;
            if (isset($bookingGarage['Booking']['quotation_id']) && !empty($bookingGarage['Booking']['quotation_id'])) {
                $quotation = $this->Quotation->findByQuotationIdAndNetworkIdAndGarageId($bookingGarage['Booking']['quotation_id'], $networkId, $garageId);
                $quotationExist = !empty($quotation);
                if ($quotationExist) {
                    $bookingGarage['Booking']['quotation_deleted'] = $quotation['Quotation']['deleted'];
                }
            }
            $bookingGarage['Booking']['quotation_exist'] = $quotationExist;
            if ($quotationExist) {
                $bookingGarage['Booking']['file_guid'] = $quotation['Quotation']['file_guid'];
            }
        }

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'garages_networks',
                'action' => 'view',
                $garageNetworkId
            ),
        );

        $bookingStatus = array(
            ConstantsBookingsStatus::PENDING => __t('Booking.Pending'),
            ConstantsBookingsStatus::CANCELLED => __t('Booking.Cancelled'),
            ConstantsBookingsStatus::CONFIRMED => __t('Booking.Confirmed'),
            ConstantsBookingsStatus::COMPLETED => __t('Booking.Completed'),
            ConstantsBookingsStatus::EXPIRED => __t('Booking.Expired'),
        );

        $arrayGarageName = array();
        if ($garageId) {
            $arrayGarageName = $this->Garage->getGaragesNameByIdGarage($garageId);
        } elseif (!empty($searcher['garage_id'])) {
            $arrayGarageName = $this->Garage->getGaragesNameByIdGarage($searcher['garage_id']);
        }

        $this->set(array(
            'garages' => $garages,
            'userAagRegionId' => $userAagRegionId,
            'cancel_action' => $cancelAction,
            'garage_id' => $garageId,
            'garage_name' => $garage['Garage']['name'],
            'garage_network_id' => $garageNetworkId,
            'bookings' => $bookingsGarage,
            'network_id' => $networkId,
            'has_child_networks' => $this->Network->hasChildNetworks($networkId),
            'child_networks' => $this->GarageNetwork->getListofChildNetworksByGarageIdAndMainNetworkId($garageId, $networkId),
            'booking_status' => $bookingStatus,
            'array_garage_name' => $arrayGarageName,
            'quoting_views_active' => $garageNetwork['GarageNetwork']['quoting_views_active']
        ));
    }

    /**
     * Get booking details.
     */
    public function booking_details($garageNetworkId, $bookingId)
    {
        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

        if (!$garageNetwork) {
            throw new UnauthorizedException();
        }

        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));

        $booking = $this->Booking->findByIdAndGarageId($bookingId, $garageId);

        if (!$garage || !$booking) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'enquiries',
                'action' => 'home',
                $garageNetworkId
            ),
            'hide_save' => true
        );

        if ($booking['Booking']['garage_id'] != $garageId) {
            $this->home($garageNetworkId);
        }

        if (!empty($booking['Booking']['work_id'])) {
            $work = $this->Work->findById($booking['Booking']['work_id']);
            $booking['Booking']['work_name'] = $work['Work']['name_' . __l()];
        } else {
            $booking['Booking']['work_name'] = '';
        }

        $this->set(array(
            'garage_network_id' => $garageNetworkId,
            'garage_id' => $garageId,
            'cancel_action' => $cancelAction,
            'booking' => $booking
        ));
    }

    /**
     * Convert URL with ShortnerUrl API.
     */
    private function shortnerUrlApiConvert($countryId, $bookingId)
    {
        $this->autoRender = false;

        $booking = $this->Booking->findById($bookingId);
        $longUrl = Router::url(array(
            'controller' => 'reporting',
            'action' => 'booking_status_edit',
            $booking['Booking']['id'],
            $booking['Booking']['network_id']
        ));

        $shortnerArray = $this->ShortnerUrl->getTokenByCountry($countryId);
        $shortnerData = array(
            'country_id' => $countryId,
            'long_url' => ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . '/redirect/sms?url=' . $longUrl,
            'expire_at_datetime' => $shortnerArray['ShortnerUrl']['expire_days'],
            'expire_at_views' => $shortnerArray['ShortnerUrl']['expire_at_views'],
            'domain' => $shortnerArray['ShortnerUrl']['domain'],
            'api_key' => Texto::encryptDecryptText($shortnerArray['ShortnerUrl']['api_key'], false) ?? null,
        );

        $shortnerUrlApi = new ShortnerUrlApi();
        $resultToken = $shortnerUrlApi->getUrlShort($shortnerData);

        // returns generated short URL if ShortnerUrlApi status is OK or long URL if not
        return $resultToken == ConstantsStatusCode::BAD_REQUEST ? ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . $longUrl : $resultToken['short_url'];
    }

    /**
     * Round passed price.
     */
    private function roundPrices($price)
    {
        return $price !== null ? number_format($price, 2) : null;
    }
}
