<?php
App::uses('ShortnerUrlApi', 'Lib');
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class EnquiriesController extends AppController
{
    public $uses = array(
        'Enquiry',
        'GarageNetwork',
        'Network',
        'Work',
        'Garage',
        'Network',
        'Email',
        'LogApi',
        'Sms',
        'SmsTemplate',
        'City',
        'Province',
        'Country',
        'ShortnerUrl',
		'Contact'
    );

    /**
     * API to create a new enquiry.
     */
    public function create_enquiry()
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
                if ($garage) {
                    $garageId = $garage['Garage']['id'];
                }
            }
            $name = isset($dataReceived->name) ? $dataReceived->name : null;
            $phone = isset($dataReceived->phone) ? $dataReceived->phone : null;
            $email = isset($dataReceived->email) ? $dataReceived->email : null;
            $description = isset($dataReceived->description) ? $dataReceived->description : null;
            $marketing_acceptance = isset($dataReceived->marketing_acceptance) ? $dataReceived->marketing_acceptance : '';
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
            $garageUrl = isset($dataReceived->garage_url) ? $dataReceived->garage_url : 'https://www.google.es/';
            $customerLanguageCode = isset($dataReceived->customer_language_code) ? $dataReceived->customer_language_code : null;
            $childNetworkId = isset($dataReceived->child_network_id) ? $dataReceived->child_network_id : null;

            //If its a child network enquirie, network its validated, if the child network is CV, set to null the not needed vars
            if (!isset($childNetworkId) || !$this->Network->validateGnmChildNetworkId($childNetworkId, $networkId)) {
                $childNetworkId = null;
            } elseif ($this->Network->isCvNetworkById($childNetworkId)) {
                $plate = $vin = $brand = $model = $version = $motExpDate = $fuel = $registeredOn = $mileage = $workIdBd = null;
            }

            if (empty($networkId) || empty($garageId) || empty($name) || empty($email) || empty($description) || empty($garageUrl) || empty($phone)) {
                exit;
            }

            $workIdBd = null;
            $workName = '';
            if (!empty($workCode)) {
                $work = $this->Work->findByNetworkIdAndCode($networkId, $workCode);
                $workIdBd = !empty($work) ? $work['Work']['id'] : null;
                $workName = !empty($work) ? $work['Work']['name_' . __l()] : '';
            }

            $enquiry = array(
                'Enquiry' => array(
                    'network_id' => $networkId,
                    'garage_id' => $garageId,
                    'name' => $name,
                    'email' => $email,
                    'description' => $description,
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
                    'garage_url' => $garageUrl,
                    'phone' => $phone,
                    'child_network_id' => $childNetworkId,
                    'marketing_acceptance' => $marketing_acceptance,
                    'creation_date' => date('Y-m-d H:i:s')
                )
            );

            $encryptedName = Texto::encryptDecryptText($enquiry['Enquiry']['name'], true);
            $encryptedPhone = Texto::encryptDecryptText($enquiry['Enquiry']['phone'], true);
            $encryptedEmail = Texto::encryptDecryptText($enquiry['Enquiry']['email'], true);
            $encryptedPlate = isset($enquiry['Enquiry']['plate']) ? Texto::encryptDecryptText($enquiry['Enquiry']['plate'], true) : null;
            $encryptedVin = isset($enquiry['Enquiry']['vin']) ? Texto::encryptDecryptText($enquiry['Enquiry']['vin'], true) : null;

            $enquiry['Enquiry']['name'] = $encryptedName;
            $enquiry['Enquiry']['phone'] = $encryptedPhone;
            $enquiry['Enquiry']['email'] = $encryptedEmail;
            $enquiry['Enquiry']['plate'] = $encryptedPlate;
            $enquiry['Enquiry']['vin'] = $encryptedVin;
            $enquiry['Enquiry']['work_name'] = $workName;

            $enquiryBd = $this->Enquiry->save($enquiry);

            if ($enquiryBd) {
                $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkId($garageId, $networkId);

                $garage = $this->Garage->findById($garageId);

                if ($garage && !empty($garage['Garage']['city_id'])) {
                    $city = $this->City->findById($garage['Garage']['city_id']);

                    if ($city) {
                        $garage['Garage']['city'] = $city['City']['name'];
                    }
                }

                $network = $this->Network->findById($networkId);
                $garageEmails = $this->Garage->getGarageEmail($garage);

                if (!empty($garageEmails)) {
                    foreach ($garageEmails as $garageEmail) {
                        $this->Email->newEmailEnquiryGarage($enquiryBd, $garageEmail, $network, $garageNetwork['GarageNetwork']['id'], $garage, $workName);
                    }

					if (isset($network['Network']['email_enquiry_contact_id']) && !empty($network['Network']['email_enquiry_contact_id'])) {
						$contact = $this->Contact->getContactEmail($network['Network']['email_enquiry_contact_id']);
						if (isset($contact)) {
							$this->Email->newEmailEnquiryGarage($enquiryBd, $contact, $network, $garageNetwork['GarageNetwork']['id'], $garage, $workName);
						}
					}

                    // only one email is sent to the customer
                    $this->Email->newEmailEnquiryCustomer($enquiryBd, $garage, $garageEmails, $network, $customerLanguageCode);
                }
            }

            $enquiryBd['Enquiry']['name'] = Texto::encryptDecryptText($enquiry['Enquiry']['name'], false);
            $enquiryBd['Enquiry']['phone'] = Texto::encryptDecryptText($enquiry['Enquiry']['phone'], false);
            $enquiryBd['Enquiry']['email'] = Texto::encryptDecryptText($enquiry['Enquiry']['email'], false);
            $enquiryBd['Enquiry']['plate'] = isset($enquiry['Enquiry']['plate']) ? Texto::encryptDecryptText($enquiry['Enquiry']['plate'], false) : null;
            $enquiryBd['Enquiry']['vin'] = isset($enquiry['Enquiry']['vin']) ? Texto::encryptDecryptText($enquiry['Enquiry']['vin'], false) : null;

            $result = array(
                'success' => $enquiryBd,
                'error' => $enquiryBd ? '' : "Enquiry can\'t be saved"
            );

            //If the enquiry was successful return the guid instead of the raw id
            if ($enquiryBd) {
                $result['success']['Enquiry']['garage_id'] = $dataReceived->garage_id;
            }

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

                        $sms_template = $this->SmsTemplate->findByCountryIdAndTemplateTypeId($country['Country']['id'], ConstantsSmsTemplateTypes::ENQUIRY);
                        if ($sms_template) {
                            $sms_license = $this->Sms->findByCountryId($country['Country']['id']);
                            if (isset($sms_license['Sms'])) {
                                $sms_user = Texto::encryptDecryptText($sms_license['Sms']['username_api']);
                                $sms_password = Texto::encryptDecryptText($sms_license['Sms']['password_api']);
                                $sms_code = Texto::encryptDecryptText($sms_license['Sms']['license_api']);
                            }
                            $url_short = $this->shortnerUrlApiConvert($country['Country']['id'], $enquiryBd['Enquiry']['id']);
                            $text = $this->Enquiry->getVariablesValues($sms_template['SmsTemplate']['description'], $enquiryBd['Enquiry']['id'], $garage['Garage']['name'], $url_short);
                            $sender = $sms_template['SmsTemplate']['sender'];
                            if (!empty($phone && $text && $sms_user && $sms_password && $sms_code)) {
                                MessagingSMS::enviar_sms($phone, $text, $sender, null, $sms_code, $sms_user, $sms_password);
                            }
                        }
                    }
                }
            }

            return $this->returnJsonResult($result);
        }
    }

    /**
     * View to get all enquiries of a garage.
     *
     * @param garageNetworkId Garage Network ID
     */
    public function home($garageNetworkId)
    {
        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);
        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));

        if (!$garageNetwork || !$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $networkId = $garageNetwork['GarageNetwork']['network_id'];

        $searcher = $this->request->query;
        $this->request->data['Search'] = $searcher;
        if (!empty($searcher['name'])) {
            $searcher['name'] = Texto::encryptDecryptText($searcher['name'], true);
        }
        $conditions = $this->Enquiry->conditions($searcher);
        $conditions[] = array('Enquiry.garage_id' => $garageId);

        $enquiriesGarage = $this->custom_pagination(
            $this->Enquiry->query('search'),
            $conditions,
            ConstantsPagination::SIZE_PAGE_SMALL,
            'Enquiry',
            null,
            'PaginatorOrderCustom'
        );

        $networksGarage = CakeSession::read('Auth.User.networks');
        if (empty($networksGarage)) {
            $networksGarage = array($networkId);
        }

        $networks = array();
        foreach ($networksGarage as $networkId) {
            $network = $this->Network->findById($networkId);
            $networks[$networkId] = $network['Network']['name'];
        }

        $answeredTypes = array(
            ConstantsBooleans::NO => __t('General.No'),
            ConstantsBooleans::YES => __t('General.Yes')
        );

        foreach ($enquiriesGarage as &$enquiryGarage) {
            $enquiryGarage['Enquiry']['description'] = strlen($enquiryGarage['Enquiry']['description']) > 40 ?
                substr($enquiryGarage['Enquiry']['description'], 0, 40) . '...' :
                $enquiryGarage['Enquiry']['description'];
            $enquiryGarage['Enquiry']['network_name'] = $networks[$enquiryGarage['Enquiry']['network_id']];
        }

        $array_garage_name = array();
        if ($garageId) {
            $array_garage_name = $this->Garage->getGaragesNameByIdGarage($garageId);
        } elseif (!empty($searcher['garage_id'])) {
            $array_garage_name = $this->Garage->getGaragesNameByIdGarage($searcher['garage_id']);
        }

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'garages_networks',
                'action' => 'view',
                $garageNetworkId
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
            'garage_id' => $garageId,
            'garage_name' => $garage['Garage']['name'],
            'garage_network_id' => $garageNetworkId,
            'enquiries' => $enquiriesGarage,
            'has_child_networks' => $this->Network->hasChildNetworks($networkId),
            'child_networks' => $this->GarageNetwork->getListofChildNetworksByGarageIdAndMainNetworkId($garageId, $networkId),
            'networks' => $networks,
            'answered_types' => $answeredTypes,
            'network_id' => $networkId,
            'array_garage_name' => $array_garage_name,
            'quoting_views_active' => $garageNetwork['GarageNetwork']['quoting_views_active']
        ));
    }

    /**
     * View to see the details of the enquiry and answer it if it hasn't been answered.
     */
    public function edit_enquiry($garageNetworkId, $enquiryId)
    {
        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);

        if (!$garageNetwork) {
            throw new UnauthorizedException();
        }

        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));

        $enquiry = $this->Enquiry->findById($enquiryId);

        if (!$garage || !$enquiry) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'enquiries',
                'action' => 'home',
                $garageNetworkId
            ),
        );

        if ($enquiry['Enquiry']['garage_id'] != $garageId) {
            $this->home($garageNetworkId);
        }

        $workName = "";
        if (!empty($enquiry['Enquiry']['work_id'])) {
            $work = $this->Work->findById($enquiry['Enquiry']['work_id']);
            $workName = $work ? $work['Work']['name_' . __l()] : "";
        }
        $enquiry['Enquiry']['work_name'] = $workName;

        $this->set(array(
            'garage_network_id' => $garageNetworkId,
            'cancel_action' => $cancelAction,
            'enquiry' => $enquiry,
            'garage_id' => $garageId
        ));

        if (!$this->request->is('get')) {
            $data = $this->request->data;
            $enquiry['Enquiry']['answer'] = $data['Enquiry']['answer'];
            $enquiry['Enquiry']['answered'] = true;
            $enquiry['Enquiry']['date_answered'] = date('Y-m-d H:i:s');

            $enquiryBd = $this->Enquiry->save($enquiry);

            if ($enquiryBd) {
                $garage = $this->Garage->findById($garageId);

                $this->Email->newEmailEnquiryGarageAnswer($enquiryBd, $garage);

                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                $this->redirect(
                    array(
                        'controller' => 'enquiries',
                        'action' => 'home',
                        $garageNetworkId
                    )
                );
            }
        }
    }

    /**
     * Convert URL with ShortnerUrl API.
     */
    private function shortnerUrlApiConvert($country_id, $enquiry_id)
    {
        $this->autoRender = false;

        $enquiry = $this->Enquiry->findById($enquiry_id);
        $garageNetwork = $this->GarageNetwork->findByGarageIdAndNetworkId($enquiry['Enquiry']['garage_id'], $enquiry['Enquiry']['network_id']);

        $longUrl = Router::url(array(
            'controller' => 'enquiries',
            'action' => 'edit_enquiry',
            $garageNetwork['GarageNetwork']['id'],
            $enquiry['Enquiry']['id']
        ));

        $shortnerArray = $this->ShortnerUrl->getTokenByCountry($country_id);
        $shortnerData = array(
            'country_id' => $country_id,
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
}
