<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class EmailsController extends AppController
{
    var $uses = array(
        'Email',
        'User',
        'EmailType',
        'AagRegion',
        'SendgridLicenseConfig',
        'Country',
        'Platform',
        'Language',
        'SendGridEmailTypeTemplate',
        'SendGridEmailTypeViewVar',
        'LanguageWebNetwork',
        'LanguageWebFlag'
    );

    /**
     * Emails home page.
     */
    public function home()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_EMAILS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::EMAILS)
            )
        ) {
            $aagRegions = array();
            if ($roleId == ConstantsRoles::SUPER_ADMIN) {
                $aagRegions = $this->AagRegion->find('list');
            }

            if (!empty($this->request->query)) {
                $searcher = $this->request->query;
                CakeSession::write('Auth.User.query', $searcher);
            } else {
                $searcher = CakeSession::read('Auth.User.query');
            }

            // filter by aag_region_id
            $searcher['aag_region_id'] = $aagRegionId;
            $this->request->data['Search'] = $searcher;

            $conditions = $this->Email->conditions($searcher);
            $conditions[] = array('Email.email_type_id !=' => ConstantsEmailTypes::ERROR);

            $emails = $this->custom_pagination(
                array(),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Email',
                null,
                'PaginatorOrderCustom'
            );

            foreach ($emails as $key => $email) {
                $emailType = $this->EmailType->findById($email['Email']['email_type_id']);
                $platform = $this->Platform->findById($email['Email']['platform_id']);
                $emails[$key]['Email']['type'] = $emailType ? $emailType['EmailType']['name_' . __l()] : '';
                $emails[$key]['Email']['platform'] = $platform ? $platform['Platform']['name'] : '';

                $vars_ser = unserialize($email['Email']['view_vars']);
                if (isset($vars_ser['files'])) {
                    $emails[$key]['Email']['files'] = $vars_ser['files'];
                }

                if (isset($vars_ser['event_action']) && $vars_ser['event_action'] !== null) {
                    $emails[$key]['Attachments']['Event_action'] = $vars_ser['event_action'];
                }
            }

            $to = $this->Email->getToFromEmails();

            $types = $this->EmailType->getListEmailTypes();
            $platforms = $this->Platform->getPlatformsListByAagRegion($aagRegionId);

            $sent_types = Configure::read('email_status');
            foreach ($sent_types as $key => $sent_type) {
                $sent_types[$key] = __t($sent_type);
            }

            $this->set(array(
                'role_id' => $roleId,
                'aag_region_id' => $aagRegionId,
                'aagRegions' => $aagRegions,
                'emails' => $emails,
                'to' => $to,
                'types' => $types,
                'sent_types' => $sent_types,
                'platforms' => $platforms
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * View email.
     */
    public function view($email_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $email = $this->Email->findByIdAndAagRegionId($email_id, $aagRegionId);

        if (
            $email &&
            (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_EMAILS) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::EMAILS)
                )
            )
        ) {
            $emailType = $this->EmailType->findById($email['Email']['email_type_id']);
            $email['Email']['type'] = $emailType ? $emailType['EmailType']['name_' . __l()] : '';

            $attachments = unserialize($email['Email']['attachments']);
            $emailViewVars = unserialize($email['Email']['view_vars']);

            $email['Attachments']['Path'] = $attachments;
            if (isset($emailViewVars['title'])) {
                $email['Attachments']['Title'] = $emailViewVars['title'];
            }

            if (isset($emailViewVars['event_action']) && $emailViewVars['event_action'] !== null) {
                $email['Attachments']['Event_action'] = $emailViewVars['event_action'];
            }

            if (isset($emailViewVars['files'])) {
                $email['Attachments']['Files'] = $emailViewVars['files'];
            }

            $country = $this->Country->findById($email['Email']['country_id']);
            $platform = $this->Platform->findById($email['Email']['platform_id']);

            $sendgridEmailTypeViewVars = array();
            $emailViewVarsKeys = array();

            if ($platform['Platform']['external'] != ConstantsBooleans::YES) {
                $sendgridEmailTypeViewVars = $this->SendGridEmailTypeViewVar->getAllSendGridEmailTypeViewVarsByEmailType($email['Email']['email_type_id']);
            } else {
                $emailViewVarsKeys = array_keys($emailViewVars);
            }

            $types = $this->EmailType->getListEmailTypes();

            $this->set(array(
                'attachments' => $attachments,
                'email' => $email,
                'email_view_vars' => $emailViewVars,
                'sendgrid_email_type_view_vars' => $sendgridEmailTypeViewVars,
                'country' => $country,
                'types' => $types,
                'isExternal' => $platform['Platform']['external'],
                'emailViewVarsKeys' => $emailViewVarsKeys
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Resend emails, only the new ones genereted for SendGrid.
     */
    public function ajax_resend_email($email_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        $email = $this->Email->findByIdAndOldAndAagRegionId($email_id, ConstantsBooleans::NO, $aagRegionId);

        if (
            $email && (
                $roleId == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_EMAILS) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::EMAILS) &&
                    $roleId == ConstantsRoles::ADMIN
                )
            )
        ) {
            $new_email = $email;
            $new_email['Email']['sent'] = ConstantsBooleans::NO;
            $new_email['Email']['retries'] = 0;
            $new_email['Email']['creation_date'] = date('Y-m-d H:i:s');
            unset($new_email['Email']['id']);

            // Validate if user country or language has been changed since email creation
            if ($new_email['Email']['email_type_id'] == ConstantsEmailTypes::NEW_USER_PASSWORD) {
                $new_email_view_vars = unserialize($new_email['Email']['view_vars']);
                $user_bd = $this->User->findByUsername($new_email_view_vars['user_username']);

                if ($new_email['Email']['country_id'] != $user_bd['User']['country_id']) {
                    $new_email['Email']['country_id'] = $user_bd['User']['country_id'];
                }

                if ($new_email['Email']['language_id'] != $user_bd['User']['language_id']) {
                    $language_code = $this->Language->findById($user_bd['User']['language_id']);
                    $new_email['Email']['language_id'] = $user_bd['User']['language_id'];
                    $new_email_view_vars['language_code'] = $language_code['Language']['code'];
                    $new_email['Email']['view_vars'] = serialize($new_email_view_vars);
                }
            }

            $fields = array(
                'Email' => array(
                    'from',
                    'to',
                    'bcc',
                    'subject',
                    'body',
                    'sent',
                    'retries',
                    'email_type_id',
                    'view_vars',
                    'template',
                    'attachments',
                    'creation_date',
                    'platform_id',
                    'language_id',
                    'language_web_id',
                    'country_id',
                    'old',
                    'aag_region_id'
                ),
            );

            $this->Email->guardar($new_email, $fields);

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Email config section in Maintenance menu. Configuration for SendGrid. Only for Admin.
     */
    public function maintenance($platformId)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        if (
            SENDGRID_EMAIL_SHOW_MAINTENANCE_CONFIG &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE) &&
            (
                ($aagRegionId == ConstantsAAGRegionId::UK && in_array($platformId, array(ConstantsPlatform::GNM, ConstantsPlatform::AGN, ConstantsPlatform::P360))) ||
                ($aagRegionId == ConstantsAAGRegionId::BENELUX && in_array($platformId, array(ConstantsPlatform::GNM, ConstantsPlatform::GV, ConstantsPlatform::GC, ConstantsPlatform::P360)))
            )
        ) {
            $platform_data = $this->Platform->findById($platformId, array('fields' => '*'));
            $platforms = $this->Platform->getPlatformsByAagRegion($aagRegionId);
            $platformsList = $this->Platform->getPlatformsListByAagRegion($aagRegionId);

            $sendgridLicenseConfig = $this->SendgridLicenseConfig->getConfigByAagRegion($aagRegionId, $platformId);
            $availableCountries = $this->Country->get_country_by_region($aagRegionId);

            $emailTypesWebsLanguages = array();
            $languagesWebAndFlags = array();
            $languagesWebList = array();

            foreach ($availableCountries as $key => $country) {
                $sendgridLicenseConfigCountryPlatform = $this->SendgridLicenseConfig->findByCountryIdAndPlatformId($key, $platformId);
                if ($sendgridLicenseConfigCountryPlatform) {
                    unset($availableCountries[$key]);
                }
            }

            $countries = $this->Country->get_country_by_region($aagRegionId);

            // get all emailtypes without license association and active
            $emailTypes = $this->EmailType->findAllByActiveAndSendgridLicensesConfigId();

            // if platform is external (platform from API)
            if (!empty($platform_data['Platform']['external'])) {
                // get all emailtypes
                $emailTypes = $this->EmailType->findAllByActive(ConstantsBooleans::ACTIVE, '*', 'name_' . __l() . ' ASC');
                $emailTypesIdKeys = array();
                // loop depends on how many license has the platform
                foreach ($sendgridLicenseConfig as $key => $sengrid_license) {
                    // Get the list of email type IDs associated with this license
                    $emailTypesIdKeys = $this->EmailType->getListEmailTypesById($sengrid_license['SendgridLicenseConfig']['id'] ?? '');
                    // Filter email types based on the retrieved IDs
                    $emailTypes[$sengrid_license['SendgridLicenseConfig']['id']][] = array_filter($emailTypes, static function ($emailType) use ($emailTypesIdKeys) {
                        return in_array($emailType['EmailType']['id'], $emailTypesIdKeys);
                    });
                }
            }
            // if the platform is agn or gv, only certain types of email are shown
            else if ($platformId != ConstantsPlatform::GNM) {
                $emailTypesWebsLanguages = array_filter($emailTypes, static function ($emailType) {
                    $emailTypesAgnGV = array(
                        ConstantsEmailTypes::BOOKING_CUSTOMER,
                        ConstantsEmailTypes::ENQUIRY_CUSTOMER,
                    );
                    return in_array($emailType['EmailType']['id'], $emailTypesAgnGV);
                });
                $emailTypes = array_filter($emailTypes, static function ($emailType) {
                    $emailTypesAgnGV = array(
                        ConstantsEmailTypes::BOOKING_GARAGE,
                        ConstantsEmailTypes::BOOKING_DISTRIBUTOR,
                        ConstantsEmailTypes::BOOKING_REMINDER,
                        ConstantsEmailTypes::ENQUIRY_GARAGE,
                        ConstantsEmailTypes::ENQUIRY_GARAGE_ANSWER,
                        ConstantsEmailTypes::ONBOARDING
                    );
                    return in_array($emailType['EmailType']['id'], $emailTypesAgnGV);
                });
                $platformNetworkId = $this->Platform->findById($platformId)['Platform']['network_id'];
                $languagesWebAndFlags = $this->LanguageWebNetwork->getLanguagesWebsNetwork($platformNetworkId);
                $languagesWebList = $this->LanguageWebNetwork->getListLanguagesIdNameByNetwork($platformNetworkId);
            } else {
                $emailTypes = array_filter($emailTypes, static function ($emailType) {
                    $emailTypesAgnGV = array(
                        ConstantsEmailTypes::BOOKING_GARAGE,
                        ConstantsEmailTypes::BOOKING_CUSTOMER,
                        ConstantsEmailTypes::BOOKING_DISTRIBUTOR,
                        ConstantsEmailTypes::BOOKING_REMINDER,
                        ConstantsEmailTypes::ENQUIRY_GARAGE,
                        ConstantsEmailTypes::ENQUIRY_CUSTOMER,
                        ConstantsEmailTypes::ENQUIRY_GARAGE_ANSWER,
                        ConstantsEmailTypes::ONBOARDING
                    );
                    return !in_array($emailType['EmailType']['id'], $emailTypesAgnGV);
                });
            }

            $languages = $this->Language->getLanguagesWithoutLoco();
            $languagesList = $this->Language->getLanguagesCodeNameWithoutLoco();

            $this->set(array(
                'sendgrid_config' => $sendgridLicenseConfig,
                'all_countries' => $countries,
                'available_countries' => $availableCountries,
                'platforms' => $platforms,
                'platforms_list' => $platformsList,
                'email_types' => $emailTypes,
                'languages' => $languages,
                'languages_list' => $languagesList,
                'languages_web_list_and_flags' => $languagesWebAndFlags,
                'email_types_webs_languages' => $emailTypesWebsLanguages,
                'languages_web_list' => $languagesWebList,
                'is_external' => $this->Platform->field('external', ['id' => $platformId])
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add new SendGridLicenseConfig.
     */
    public function ajax_add_sendgrid_license_config()
    {
        $this->verify_ajax($this->request);

        if (
            SENDGRID_EMAIL_SHOW_MAINTENANCE_CONFIG &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $this->autoRender = false;
            if (!$this->request->is('get')) {
                $sendgridConfigData = $this->request->data;
                if ($this->SendgridLicenseConfig->add($sendgridConfigData)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit SendGridLicenseConfig.
     */
    public function ajax_edit_sendgrid_license_config()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $sendgridConfigData = $this->request->data;

        $sendgridLicenseConfig = $this->SendgridLicenseConfig->findByIdAndAagRegionId($sendgridConfigData['SendgridLicenseConfig']['id'], $aagRegionId);

        if (
            $sendgridLicenseConfig &&
            SENDGRID_EMAIL_SHOW_MAINTENANCE_CONFIG &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $this->autoRender = false;

            if (!empty($sendgridConfigData)) {
                if ($this->SendgridLicenseConfig->edit($sendgridConfigData)) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete SendGridLicenseConfig. Only for Admin.
     */
    public function ajax_delete_sengrid_license($sendgrid_license_config_id, $platformId)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $sendgridLicenseConfig = $this->SendgridLicenseConfig->findByIdAndAagRegionId($sendgrid_license_config_id, $aagRegionId);

        $sendgridLicenseConfigId = $this->request->data['sendgrid_license_id'];
        $platformId = $this->request->data['platform_id'];

        if (
            $sendgridLicenseConfig &&
            SENDGRID_EMAIL_SHOW_MAINTENANCE_CONFIG &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE) &&
            (
                ($aagRegionId == ConstantsAAGRegionId::UK && in_array($platformId, array(ConstantsPlatform::GNM, ConstantsPlatform::AGN, ConstantsPlatform::P360))) ||
                ($aagRegionId == ConstantsAAGRegionId::BENELUX && in_array($platformId, array(ConstantsPlatform::GNM, ConstantsPlatform::GV, ConstantsPlatform::GC, ConstantsPlatform::P360)))
            )
        ) {
            $this->autoRender = false;

            if (!$this->request->is('get')) {

                // delete emailtypes_template
                $emailTypesTemplateList = $this->SendGridEmailTypeTemplate->findAllByCountryIdAndPlatformId($sendgridLicenseConfig['SendgridLicenseConfig']['country_id'], $platformId);
                foreach ($emailTypesTemplateList as $email_type_template) {
                    $this->SendGridEmailTypeTemplate->delete($email_type_template['SendGridEmailTypeTemplate']['id']);
                }

                // desactivar emailtypes
                $platform_data = $this->Platform->findById($platformId, array('fields' => '*'));
                if (!empty($platform_data['Platform']['external'])) {
                    $this->EmailType->deactivateByLicenseId($sendgrid_license_config_id);
                }

                return $this->SendgridLicenseConfig->delete($sendgrid_license_config_id);
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add a new SendgridEmailTypeTemplate. Only for Admin.
     */
    public function ajax_add_sendgrid_email_type_template()
    {
        $this->verify_ajax($this->request);

        if (
            SENDGRID_EMAIL_SHOW_MAINTENANCE_CONFIG &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $this->autoRender = false;

            if (!$this->request->is('get')) {
                $sendgridEmailTypeTemplate = $this->request->data;
                $result = $this->SendGridEmailTypeTemplate->add($sendgridEmailTypeTemplate);
                return $result;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit SendgridEmailTypeTemplate. Only for Admin.
     */
    public function ajax_edit_sendgrid_email_type_template()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $sendgridEmailTypeTemplateData = $this->request->data;

        $sendgridLicenseConfig = $this->SendGridEmailTypeTemplate->findByIdAndAagRegionId($sendgridEmailTypeTemplateData['id'], $aagRegionId);

        if (
            $sendgridLicenseConfig &&
            SENDGRID_EMAIL_SHOW_MAINTENANCE_CONFIG &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $this->autoRender = false;
            if (!$this->request->is('get')) {
                $result = $this->SendGridEmailTypeTemplate->edit($sendgridEmailTypeTemplateData);
                return $result;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete SendgridEmailTypeTemplate. Only for Admin.
     */
    public function delete_sendgrid_email_type_template($sendgrid_email_type_template_id, $platform_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $sendgridEmailTypeTemplate = $this->SendGridEmailTypeTemplate->findByIdAndAagRegionId($sendgrid_email_type_template_id, $aagRegionId);

        if (
            $sendgridEmailTypeTemplate &&
            SENDGRID_EMAIL_SHOW_MAINTENANCE_CONFIG &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE) &&
            (
                ($aagRegionId == ConstantsAAGRegionId::UK && in_array($platform_id, array(ConstantsPlatform::GNM, ConstantsPlatform::AGN, ConstantsPlatform::P360))) ||
                ($aagRegionId == ConstantsAAGRegionId::BENELUX && in_array($platform_id, array(ConstantsPlatform::GNM, ConstantsPlatform::GV, ConstantsPlatform::GC, ConstantsPlatform::P360)))
            )
        ) {
            if ($this->SendGridEmailTypeTemplate->delete($sendgrid_email_type_template_id)) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
            }

            $this->redirect(
                array(
                    'controller' => 'emails',
                    'action' => 'maintenance',
                    $platform_id
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }
}
