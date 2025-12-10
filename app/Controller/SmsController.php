<?php
App::uses('ShortnerUrlApi', 'Lib');

class SmsController extends AppController
{
    public $uses = array(
        'Sms',
        'SmsTemplate',
        'SmsTemplateType',
        'Province',
        'Country',
        'ShortnerUrl',
    );

    /**
     * SMS home page.
     */
    public function home($license_id = null)
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS) &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_SMS_CONFIGURATION)
            )
        ) {
            $aagRegion = CakeSession::read('Auth.User.aag_region_id');

            $tokenLogin = $this->Sms->findByRoleIdAndAagRegionId(
                CakeSession::read('Auth.User.role_id'),
                $aagRegion
            );

            if (!empty($license_id)) {
                $tokenLogin = $this->Sms->findById($license_id);
            }

            if (isset($tokenLogin['Sms'])) {
                $tokenLogin['Sms']['username_iframe'] = Texto::encryptDecryptText($tokenLogin['Sms']['username_iframe']);
                $tokenLogin['Sms']['password_iframe'] = Texto::encryptDecryptText($tokenLogin['Sms']['password_iframe']);
                $tokenLogin['Sms']['license_iframe'] = Texto::encryptDecryptText($tokenLogin['Sms']['license_iframe']);
                $tokenLogin['Sms']['username_api'] = Texto::encryptDecryptText($tokenLogin['Sms']['username_api']);
                $tokenLogin['Sms']['password_api'] = Texto::encryptDecryptText($tokenLogin['Sms']['password_api']);
                $tokenLogin['Sms']['license_api'] = Texto::encryptDecryptText($tokenLogin['Sms']['license_api']);
            }

            $countries = [];
            if (isset($aagRegion)) {
                $countries = $this->Country->get_country_by_region($aagRegion);
            }

            $this->set(array(
                'tokenLogin' => $tokenLogin,
                'countries' => $countries,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * SMS list page.
     */
    public function list()
    {
        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
        ) {
            $aagRegion = CakeSession::read('Auth.User.aag_region_id');
            $smsLicenses = $this->Sms->getLicensesByRegion($aagRegion);
            $smsTemplates = $this->SmsTemplate->getTemplateByRegion($aagRegion);

            $countries = [];
            if (isset($aagRegion)) {
                $countries = $this->Country->get_country_by_region($aagRegion);
            }

            $countriesNoTemplate = $countries;
            foreach ($smsTemplates as &$smsTemplate) {
                $countryTemplateId = $smsTemplate['SmsTemplate']['country_id'];
                $countryTemplate = $this->Country->findById($countryTemplateId);
                if ($countryTemplate) {
                    $smsTemplate['Country']['name'] = $countryTemplate['Country']['name'];
                    unset($countriesNoTemplate[$countryTemplate['Country']['id']]);
                }
                $templateTypeId = $smsTemplate['SmsTemplate']['template_type_id'];
                $templateType = $this->SmsTemplateType->findById($templateTypeId);
                if ($templateType) {
                    $smsTemplate['SmsTemplateType']['name'] = $templateType['SmsTemplateType']['name' . __s()];
                }
            }

            $template_types = $this->SmsTemplateType->search_list();

            $countriesNoLicense = $countries;
            foreach ($smsLicenses as &$smsLicense) {
                $smsLicense['Sms']['username_iframe'] = Texto::encryptDecryptText($smsLicense['Sms']['username_iframe']);
                $smsLicense['Sms']['password_iframe'] = Texto::encryptDecryptText($smsLicense['Sms']['password_iframe']);
                $smsLicense['Sms']['license_iframe'] = Texto::encryptDecryptText($smsLicense['Sms']['license_iframe']);
                $smsLicense['Sms']['username_api'] = Texto::encryptDecryptText($smsLicense['Sms']['username_api']);
                $smsLicense['Sms']['password_api'] = Texto::encryptDecryptText($smsLicense['Sms']['password_api']);
                $smsLicense['Sms']['license_api'] = Texto::encryptDecryptText($smsLicense['Sms']['license_api']);
                $smsLicense['ShortnerUrl']['api_key'] = Texto::encryptDecryptText($smsLicense['ShortnerUrl']['api_key']);
                $countryId = $smsLicense['Sms']['country_id'];
                $country = $this->Country->findById($countryId);
                if ($country) {
                    $smsLicense['Country']['name'] = $country['Country']['name'];
                    unset($countriesNoLicense[$country['Country']['id']]);
                }
            }

            $this->set(array(
                'tokenLogin' => $smsLicenses,
                'countries' => $countries,
                'countriesNoLicense' => $countriesNoLicense,
                'sms_templates' => $smsTemplates,
                'template_types' => $template_types,
                'countriesNoTemplate' => $countriesNoTemplate
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get countries.
     */
    public function ajax_get_countries_template()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
        ) {
            $this->layout = false;
            $this->autoRender = false;

            $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
            if (isset($aagRegionId) && isset($this->request->data['template_type_id']) && !empty($this->request->data['template_type_id'])) {
                $countries = $this->SmsTemplate->getCountriesByTemplateTypeId($aagRegionId, $this->request->data['template_type_id']);
            }
            return json_encode($countries ?? []);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search options add template list.
     */
    public function ajax_search_options_add_template_list()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
        ) {
            $this->set(
                array(
                    'template_type_id' => $this->request->data['template_type_id'],
                )
            );

            $this->layout = null;
            $this->render('../Sms/Elements/options_add_template');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX edit SMS license.
     */
    public function ajax_edit_license_config()
    {
        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
        ) {
            $smsConfigData = $this->request->data;

            if (!empty($smsConfigData)) {
                if ($this->Sms->edit_sms_config($smsConfigData)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->layout = null;
            $this->render('../Sms/list');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create SMS license.
     */
    public function add_license_config()
    {
        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
        ) {
            if (!$this->request->is('get')) {
                $smsConfigData = $this->request->data;
                if ($this->Sms->add_sms_config($smsConfigData)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->layout = null;
            $this->render('../Sms/list');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete SMS license and ShortnerUrl configuration.
     */
    public function delete($sms_license_id)
    {
        $this->verify_ajax($this->request);
        $shortner_url_id = $this->ShortnerUrl->findBySmsLicenseConfigId($sms_license_id);

        if (
            $shortner_url_id &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
        ) {
            $this->autoRender = false;
            $this->ShortnerUrl->delete($shortner_url_id['ShortnerUrl']['id']);
            $this->Sms->delete($sms_license_id);
            $this->render('../Sms/list');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX create SmsTemplate.
     */
    public function add_template()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
        ) {
            if (!$this->request->is('get')) {
                $smsTemplate = $this->request->data;
                $smsTemplate['SmsTemplate']['aag_region_id'] = CakeSession::read('Auth.User.aag_region_id') ?? null;
                if ($this->SmsTemplate->add($smsTemplate)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->layout = null;
            $this->render('../Sms/list');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX edit SmsTemplate.
     */
    public function ajax_create_template()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
        ) {
            $smsTemplate = $this->request->data;
            $smsTemplate['SmsTemplate']['aag_region_id'] = CakeSession::read('Auth.User.aag_region_id') ?? null;
            if ($this->SmsTemplate->edit($smsTemplate)) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
            }

            $this->layout = null;
            $this->render('../Sms/list');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete SmsTemplate.
     */
    public function delete_template($sms_template_id)
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
        ) {
            $this->autoRender = false;
            $this->SmsTemplate->delete($sms_template_id);
            $this->render('../Sms/list');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * API to get SMS info for specific country.
     */
    public function get_info_sms_country()
    {
        $this->layout = false;
        $this->autoRender = false;
        $smsLicense = array();

        if (!$this->request->is('get')) {
            $datos = $this->request->input('json_decode');
            $province = $this->Province->findByProvinceCode($datos->province_code);
            $country = $this->Country->findById($province['Province']['country_id']);
            $license = $this->Sms->findByCountryId($country['Country']['id']);
            if (!empty($license)) {
                $smsLicense['Sms']['username_api'] = Texto::encryptDecryptText($license['Sms']['username_api']);
                $smsLicense['Sms']['password_api'] = Texto::encryptDecryptText($license['Sms']['password_api']);
                $smsLicense['Sms']['license_api'] = Texto::encryptDecryptText($license['Sms']['license_api']);
            }
        }

        return json_encode($smsLicense);
    }

    /**
     * AJAX edit ShortnerUrl license config.
     */
    public function ajax_edit_license_config_url()
    {
        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
        ) {
            $msg = false;
            $shortnerUrlConfigData = $this->request->data;
            if (!empty($shortnerUrlConfigData)) {
                $count = $this->ShortnerUrl->getCountAllBySmsId($shortnerUrlConfigData['ShortnerUrl']['sms_license_config_id']);
                if ($count < 1) {
                    if ($this->ShortnerUrl->add($shortnerUrlConfigData)) {
                        $msg = true;
                    }
                } else {
                    if ($this->ShortnerUrl->edit($shortnerUrlConfigData)) {
                        $msg = true;
                    }
                }
                if ($msg) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->layout = null;
            $this->render('../Sms/list');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX shortner URL api convert.
     */
    public function shortner_url_api_convert()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
        ) {
            $this->autoRender = false;

            $smsTemplate = $this->request->data;
            $smsTemplate['long_url'] = 'https://aaggnm.com/users/login';
            $countryId = $smsTemplate['country_id'];

            $shortnerData = array(
                'country_id' => $countryId,
                'long_url' => $smsTemplate['long_url'],
                'expire_at_datetime' => $smsTemplate['expire_at_datetime'],
                'expire_at_views' => $smsTemplate['expire_at_views'],
                'domain' => $smsTemplate['domain'],
                'api_key' => $smsTemplate['api_key'] ?? null,
            );

            $shortnerUrlApi = new ShortnerUrlApi();
            $resultToken = $shortnerUrlApi->getUrlShortTest($shortnerData);

            $msg = '';
            if ($resultToken == ConstantsErrorUrl::BAD_APIKEY) {
                $msg = 'error_apikey';
            } elseif ($resultToken == ConstantsErrorUrl::BAD_DOMAIN) {
                $msg = 'error_domain';
            } elseif ($resultToken == ConstantsStatusCode::BAD_REQUEST) {
                $msg = 'error';
            } else {
                $msg = 'ok';
            }
            return $msg;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX check length from country.
     */
    public function ajax_check_length_from_country()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
        ) {
            $this->layout = false;
            $this->autoRender = false;

            $data = $this->ShortnerUrl->getDomainByCountry($this->request->data['country_id']);

            return json_encode($data['ShortnerUrl'] ?? []);
        } else {
            throw new UnauthorizedException();
        }
    }
}
