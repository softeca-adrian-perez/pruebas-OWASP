<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('Leadgen', 'Lib');
App::import('Vendor', 'icalendar/zapcallib');
App::import('Vendor', 'sendgrid/php-http-client/lib/Client');
App::import('Vendor', 'sendgrid/php-http-client/lib/Response');
App::import('Vendor', 'sendgrid/sendgrid/lib/BaseSendGridClientInterface');
App::import('Vendor', 'sendgrid/sendgrid/lib/SendGrid');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/Mail');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/MimeType');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/EmailAddress');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/From');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/To');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/Bcc');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/Cc');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/Subject');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/Content');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/Personalization');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/TypeException');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/TemplateId');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/DynamicTemplateData');
App::import('Vendor', 'sendgrid/sendgrid/lib/mail/Attachment');
App::import('Vendor', 'sendgrid/sendgrid/lib/helper/Assert');
App::import('Vendor', 'sendgrid/php-http-client/lib/exception/InvalidRequest');

class Email extends AppModel
{
    public $useTable = 'emails';
    public $order = "Email.id DESC";

    public $validate = array(
        'from_name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'from' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'to' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'cc' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'bcc' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'subject' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'body' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'last_error_message' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'view_vars' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'template' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'attachments' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['to'])) {
            $conditions[] = $this->_conditionForwardTo($fields['to']);
        }
        if (!empty($fields['type'])) {
            $conditions[] = $this->_conditionType($fields['type']);
        }
        if (!empty($fields['sent'])) {
            $conditions[] = $this->_conditionSent($fields['sent']);
        }
        if (!empty($fields['creation_date'])) {
            $conditions[] = $this->conditionCreationDateFrom($fields['creation_date']);
            $conditions[] = $this->conditionCreationDateTo($fields['creation_date']);
        }
        if (!empty($fields['subject'])) {
            $conditions[] = $this->_conditionSubject($fields['subject']);
        }
        if (!empty($fields['aag_region_id'])) {
            $conditions[] = $this->_conditionAagRegion($fields['aag_region_id']);
        }
        if (!empty($fields['platform'])) {
            $conditions[] = $this->_conditionPlatform($fields['platform']);
        }
        return $conditions;
    }

    private function _conditionForwardTo($to)
    {
        $email_bd = $this->findById($to);
        $email = $email_bd['Email']['to'];
        return array('Email.to' => $email);
    }

    public function _conditionType($type)
    {
        return array('Email.email_type_id' => $type);
    }

    public function _conditionPlatform($platform)
    {
        return array('Email.platform_id' => $platform);
    }

    public function _conditionSent($sent)
    {
        if ($sent == ConstantsStatusEmail::ERROR) {
            return array(
                'Email.sent' => ConstantsBooleans::NO,
                'Email.retries > 0'
            );
        } elseif ($sent == ConstantsStatusEmail::PENDING) {
            return array(
                'Email.sent' => ConstantsBooleans::NO,
                'Email.retries = 0'
            );
        } else {
            return array(
                'Email.sent' => ConstantsBooleans::YES,
            );
        }
    }

    private function conditionCreationDateFrom($fromDate)
    {
        $fromDate = Fecha::toFormatoBd($fromDate);
        return array('Email.creation_date >=' => $fromDate);
    }

    private function conditionCreationDateTo($toDate)
    {
        $toDate = Fecha::toFormatoBd($toDate);
        return array('Email.creation_date <' => date('Y-m-d', strtotime($toDate . " +1 days")));
    }

    public function _conditionSubject($subject)
    {
        return array('Email.subject LIKE' => '%' . $subject . '%');
    }

    public function _conditionAagRegion($aag_region_id)
    {
        if ($aag_region_id == Configure::read('AAG_REGION_ID_BENELUX') || $aag_region_id == Configure::read('AAG_REGION_ID_UK_IRELAND')) {
            return array('Email.aag_region_id' => $aag_region_id);
        } else {
            return array('Email.aag_region_id' => null);
        }
    }

    public function getToFromEmails()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'Email.to',
                ),
                'order' => 'Email.to ASC',
                'group' => 'Email.to',
            )
        );
    }

    /**
     * Send not sent emails by SendGrid.
     */
    public function sentNotSentSendGridEmails()
    {
        // block unsent emails
        $db = $this->getDataSource();
        $db->fetchAll('SELECT id FROM emails WHERE sent = 0 FOR UPDATE;');

        $emailsNotSent = $this->find(
            'all',
            array(
                'conditions' => array(
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries <' => ConstantsEmail::NUM_RETRIES
                ),
                'limit' => ConstantsEmail::NUM_EMAIL_SEND
            )
        );
        foreach ($emailsNotSent as $email) {
            $this->sendSendgridEmail($email);
        }
    }

    public function sendNotificationErrors()
    {
        $emails_no_enviados = $this->find(
            'all',
            array(
                'conditions' => array(
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsEmail::NUM_RETRIES,
                    'notification_error' => ConstantsBooleans::NO
                )
            )
        );

        foreach ($emails_no_enviados as $email_error) {
            foreach (Configure::read('Email.notification') as $email_to) {
                $email = new CakeEmail(Configure::read('Email.configuracion'));
                $email->from($email_error['Email']['from']);
                $email->to($email_to);
                $email->subject(__t('Email.Email_not_send', array(
                    'language_code' => ConstantsLanguages::ENGLISH_CODE
                )));
                $email->template('email_error', 'email');
                $email->emailFormat('html');
                $email->viewVars(
                    array(
                        'id' => $email_error['Email']['id'],
                        'to' => $email_error['Email']['to'],
                        'subject' => $email_error['Email']['subject']
                    )
                );

                // if it´s configured to send emails
                if (LEADGEN_EMAILS_SEND_TO_REAL_EMAILS == ConstantsBooleans::YES) {
                    $email->send();
                }
            }
            $email_error['Email']['notification_error'] = ConstantsBooleans::YES;
            $fields = array(
                'Email' => array(
                    'notification_error',
                )
            );
            return $this->save($email_error, true, $fields);
        }

        $emails_office = $this->find(
            'all',
            array(
                'conditions' => array(
                    'last_error_message' => ConstantsEmail::ERROR_OFFICE,
                    'notification_error' => ConstantsBooleans::NO
                )
            )
        );

        foreach ($emails_office as $email_error) {
            foreach (Configure::read('Email.notification') as $email_to) {
                $email = new CakeEmail(Configure::read('Email.configuracion'));
                $email->from($email_error['Email']['from']);
                $email->to($email_to);
                $email->subject(__t('Email.Notification_not_send', array(
                    'language_code' => ConstantsLanguages::ENGLISH_CODE
                )));
                $email->template('email_error', 'email');
                $email->emailFormat('html');
                $email->viewVars(
                    array(
                        'id' => $email_error['Email']['id'],
                        'to' => $email_error['Email']['to'],
                        'subject' => $email_error['Email']['subject'],
                    )
                );

                // if it´s configured to send emails
                if (LEADGEN_EMAILS_SEND_TO_REAL_EMAILS == ConstantsBooleans::YES) {
                    $email->send();
                }
            }
            $email_error['Email']['notification_error'] = ConstantsBooleans::YES;
            $fields = array(
                'Email' => array(
                    'notification_error',
                )
            );
            return $this->save($email_error, true, $fields);
        }
    }

    /**
     * Send email with SendGrid API.
     */
    public function sendSendgridEmail($emailSend)
    {
        $this->SendgridLicenseConfig = ClassRegistry::init('SendgridLicenseConfig');
        $this->SendGridEmailTypeTemplate = ClassRegistry::init('SendGridEmailTypeTemplate');
        $this->SendGridEmailTypeViewVar = ClassRegistry::init('SendGridEmailTypeViewVar');
        $this->Language = ClassRegistry::init('Language');

        try {
            $sendToRealRecipient = SENDGRID_EMAIL_SEND_TO_REAL_RECIPIENT == ConstantsBooleans::YES;
            $emailTypeId = $emailSend['Email']['email_type_id'];
            $emailLanguageId = isset($emailSend['Email']['language_id']) ? $emailSend['Email']['language_id'] : null;
            $emailLanguageWebId = isset($emailSend['Email']['language_web_id']) ? $emailSend['Email']['language_web_id'] : null;
            $emailPlatformId = $emailSend['Email']['platform_id'];
            $emailCountryId = $emailSend['Email']['country_id'];
            $emailAagRegionId = $emailSend['Email']['aag_region_id'];

            if (!empty($emailLanguageWebId) || !empty($emailLanguageId)) {
                $sendGridLicenseConfig = $this->SendgridLicenseConfig->findByPlatformIdAndCountryIdAndAagRegionId($emailPlatformId, $emailCountryId, $emailAagRegionId);

                // if the SendGrid license hasn't been configured for country and region
                if ($sendGridLicenseConfig) {

                    if (in_array($emailTypeId, array(ConstantsEmailTypes::BOOKING_CUSTOMER, ConstantsEmailTypes::ENQUIRY_CUSTOMER)) && isset($emailLanguageWebId)) {
                        $sendgridEmailTypeTemplate = $this->SendGridEmailTypeTemplate->findByEmailTypeIdAndLanguageWebIdAndPlatformIdAndCountryIdAndAagRegionId($emailTypeId, $emailLanguageWebId, $emailPlatformId, $emailCountryId, $emailAagRegionId);
                    } elseif (isset($emailLanguageId)) {
                        $sendgridEmailTypeTemplate = $this->SendGridEmailTypeTemplate->findByEmailTypeIdAndLanguageIdAndPlatformIdAndCountryIdAndAagRegionId($emailTypeId, $emailLanguageId, $emailPlatformId, $emailCountryId, $emailAagRegionId);
                    }

                    // if the template hasn't been configured for the email type, country and region
                    if ($sendgridEmailTypeTemplate) {

                        $email = new \SendGrid\Mail\Mail();

                        // from email configured in SendgridLicenseConfig
                        $email->setFrom($sendGridLicenseConfig['SendgridLicenseConfig']['from_email']);

                        $to = explode(';', $emailSend['Email']['to']);

                        foreach ($to as $key => $email_to) {
                            // if the email has to be sent to the actual recipient or
                            // if it does not have to be sent but the recipient is in the default list, it adds
                            if (
                                $sendToRealRecipient ||
                                (!$sendToRealRecipient && strpos(SENDGRID_EMAIL_DEFAULT_EMAILS, $email_to) !== false)
                            ) {
                                $email->addTo($email_to);
                            } else {
                                unset($to[$key]);
                            }
                        }

                        // if there isn't recipient to send the emails to, it is not sent, but marked as sent
                        $sendEmail = !empty($to);

                        if ($sendEmail) {
                            if (!empty($emailSend['Email']['cc'])) {
                                $cc = $sendToRealRecipient == ConstantsBooleans::NO ?
                                    array() : explode(';', $emailSend['Email']['cc']);

                                foreach ($cc as $email_cc) {
                                    $email->addCc($email_cc);
                                }
                            }

                            if (!empty($emailSend['Email']['bcc'])) {
                                $bcc = $sendToRealRecipient == ConstantsBooleans::NO ?
                                    array() : explode(';', $emailSend['Email']['bcc']);

                                foreach ($bcc as $email_bcc) {
                                    $email->addBcc($email_bcc);
                                }
                            }

                            $personalization = $email->getPersonalization();
                            $personalization->setHasDynamicTemplate(true);
                            $email->setTemplateId($sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['sendgrid_template_id']);

                            // if email has viewvars
                            if ($emailSend['Email']['view_vars']) {
                                $emailViewVars = unserialize($emailSend['Email']['view_vars']);

                                // if viewvars could be unserialized
                                if (!empty($emailViewVars)) {
                                    foreach ($emailViewVars as $emailViewVarName => $emailViewVarValue) {
                                        $this->addDynamicTemplateDataSendgrid($personalization, $emailTypeId, $emailViewVarName, $emailViewVarValue);
                                    }

                                    // get all the viewvars already added to personalization to add the remaining ones
                                    $dynamicTemplateData = $personalization->getDynamicTemplateData();
                                    // get all the SendGridViewVars that has to be send by email type
                                    $sendGridEmailTypeViewVars = $this->SendGridEmailTypeViewVar->getAllSendGridEmailTypeViewVarsByEmailType($emailTypeId);

                                    foreach ($sendGridEmailTypeViewVars as $sendGridEmailTypeViewVar) {
                                        $viewVarName = $sendGridEmailTypeViewVar['SendGridViewVar']['name'];
                                        if (!isset($dynamicTemplateData[$viewVarName])) {
                                            $personalization->addDynamicTemplateData($viewVarName, null);
                                        }
                                    }

                                    if (isset($emailSend['Email']['attachments'])) {
                                        $attachments_tmp = unserialize($emailSend['Email']['attachments']);
                                        if (is_array($attachments_tmp)) {
                                            foreach ($attachments_tmp as $attachment_tmp) {
                                                $attachments[] = $attachment_tmp;
                                            }
                                        }

                                        if (!empty($attachments) && isset($emailViewVars['attachments_filenames'])) {
                                            $filenames = $emailViewVars['attachments_filenames'];

                                            foreach ($attachments as $key => $attachment) {
                                                $file_encoded = base64_encode(file_get_contents($attachment));
                                                $attachment = new \SendGrid\Mail\Attachment();
                                                $attachment->setContent($file_encoded);
                                                $attachment->setDisposition("attachment");
                                                $attachment->setFilename($filenames[$key]);
                                                $email->addAttachment($attachment);
                                            }
                                        }
                                    }
                                } else {
                                    $sendEmail = false;
                                }
                            }

                            $sendgrid = new SendGrid(Texto::encryptDecryptText($sendGridLicenseConfig['SendgridLicenseConfig']['api_key'], false));

                            if ($sendEmail) {
                                $response = $sendgrid->send($email);

                                if ($response->statusCode() != ConstantsStatusCode::ACCEPTED) {
                                    $this->addRetry('Sendgrid.Email_cannot_be_sent', $emailSend['Email']['id']);
                                } else {
                                    $this->changeStatusSent($emailSend['Email']['id']);

                                    if (
                                        $emailSend['Email']['email_type_id'] == ConstantsEmailTypes::RECOVER_PASSWORD ||
                                        $emailSend['Email']['email_type_id'] == ConstantsEmailTypes::NEW_USER_PASSWORD
                                    ) {
                                        $this->deleteViewVars($emailSend['Email']['id']);
                                    }
                                }
                            } else {
                                $this->addRetry('Email.Viewvars_cannot_be_unserialized', $emailSend['Email']['id']);
                            }
                        } else {
                            // if there isn't recipient to send the emails to, it is not sent, but marked as sent
                            $this->changeStatusSent($emailSend['Email']['id']);

                            if (
                                $emailSend['Email']['email_type_id'] == ConstantsEmailTypes::RECOVER_PASSWORD ||
                                $emailSend['Email']['email_type_id'] == ConstantsEmailTypes::NEW_USER_PASSWORD
                            ) {
                                $this->deleteViewVars($emailSend['Email']['id']);
                            }
                        }
                    } else {
                        // templete not configured
                        $this->addRetry('Sendgrid.Template_id_not_configured', $emailSend['Email']['id']);
                    }
                } else {
                    // license not configured
                    $this->addRetry('Sendgrid.License_not_configured', $emailSend['Email']['id']);
                }
            } else {
                // license not configured
                $this->addRetry('Sendgrid.Language_not_received_from_platform', $emailSend['Email']['id']);
            }
        } catch (Exception $e) {
            $this->addRetry($e->getMessage(), $emailSend['Email']['id']);
        }
    }

    /**
     * Add the DynamicTemplateData variables for the SendGrid emails in the specified personalization.
     */
    private function addDynamicTemplateDataSendgrid($personalization, $emailTypeId, $viewVarName, $viewVarValue)
    {
        // get the SendGridViewVar filtering by email type ID and name (name or old_value)
        $sendgridEmailTypeViewVars = $this->SendGridEmailTypeViewVar->getSendGridEmailTypeViewVarByEmailTypeAndName($emailTypeId, $viewVarName);

        if ($sendgridEmailTypeViewVars) {
            $sendgridEmailTypeViewVarsName = $sendgridEmailTypeViewVars['SendGridViewVar']['name'];

            $viewVarValue = Texto::decryptSendGridViewVarValue($emailTypeId, $sendgridEmailTypeViewVarsName, $viewVarValue);

            $personalization->addDynamicTemplateData($sendgridEmailTypeViewVarsName, $viewVarValue);
        }
    }

    /**
     * Set certain email ViewVars to null.
     */
    public function deleteViewVars($emailId)
    {
        $email = $this->findById($emailId);

        if ($email) {
            $emailViewVars = unserialize($email['Email']['view_vars']);

            $emailViewVarsDelete = array(
                'gnm_pwd_url'
            );

            foreach ($emailViewVars as $emailViewVarName => $emailViewVarValue) {
                if (in_array($emailViewVarName, $emailViewVarsDelete)) {
                    $emailViewVars[$emailViewVarName] = null;
                }
            }

            $email['Email']['view_vars'] = serialize($emailViewVars);

            $fields = array(
                'Email' => array(
                    'view_vars',
                )
            );

            return $this->save($email, true, $fields);
        }
        return false;
    }

    /**
     * Create a new email.
     */
    public function add($email)
    {
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
                'aag_region_id',
                'guid'
            ),
        );

        $emailAagRegionId = $email['Email']['aag_region_id'];

        $email['Email']['creation_date'] = date('Y-m-d H:i:s');
        $email['Email']['retries'] = ConstantsBooleans::NO;
        $email['Email']['old'] = ConstantsBooleans::NO;

        if (isset($email['Email']['view_vars']) || !empty($email['Email']['view_vars'])) {
            $unserializeViewVars = unserialize($email['Email']['view_vars']);
        }

        $fromUsingRegion = GNMAAG_EMAIL_CONFIGURATION_NO_REGION_FROM;
        if ($emailAagRegionId == Configure::read('AAG_REGION_ID_BENELUX')) {
            $fromUsingRegion = GNMAAG_EMAIL_CONFIGURATION_BENELUX_FROM;
        } elseif ($emailAagRegionId == Configure::read('AAG_REGION_ID_UK_IRELAND')) {
            $fromUsingRegion = GNMAAG_EMAIL_CONFIGURATION_UK_IRELAND_FROM;
        }

        $email['Email']['from'] = empty($email['Email']['from']) ? $fromUsingRegion : $email['Email']['from'];

        $bcc = null;
        if ($emailAagRegionId == Configure::read('AAG_REGION_ID_UK_IRELAND')) {
            $bcc = isset($unserializeViewVars['network_id']) ? GNMAAG_WEBS_BCC : GNMAAG_BCC;
        }

        $email['Email']['bcc'] = $bcc;
        $email['Email']['guid'] = CakeText::uuid();

        $this->create();
        return $this->save($email, true, $fields);
    }

    public function newEmailAlert($emailTo, $text, $subject, $languageCode, $type, $viewVarsTmp, $user, $action)
    {
        if (!empty($emailTo)) {
            $files = array();
            $filenames = array();

            foreach ($viewVarsTmp['files'] as $file) {
                if (isset($file['AppointmentFile'])) {
                    $filename = $file['AppointmentFile']['file'];
                    $filenamePath = DIR_APPOINTMENT_FILES_ABSOLUTE . $file['AppointmentFile']['file'];
                } elseif (isset($file['TaskFile'])) {
                    $filename = $file['TaskFile']['file'];
                    $filenamePath = DIR_TASK_FILES_ABSOLUTE . $file['TaskFile']['file'];
                }
                array_push($files, $filenamePath);
                array_push($filenames, $filename);
            }

            $viewVars = array(
                'language_code' => $languageCode,
                'title' => isset($viewVarsTmp['title']) ? $viewVarsTmp['title'] : null,
                'attachments_filenames' => $filenames
            );

            $email_type_id = '';

            switch ($type) {
                case ConstantsAlerts::APPOINTMENT:
                    $email_type_id = ConstantsEmailTypes::APPOINTMENT;
                    switch ($action) {
                        case ConstantsActions::ADD:
                            $viewVars['actions'] = ConstantsActionsNames::CREATE;
                            break;
                        case ConstantsActions::EDIT:
                            $viewVars['actions'] = ConstantsActionsNames::UPDATE;
                            break;
                        case ConstantsActions::COMMENT:
                            $viewVars['actions'] = ConstantsActionsNames::COMMENT;
                            break;
                        case ConstantsActions::DELETE:
                            $viewVars['actions'] = ConstantsActionsNames::DELETE;
                            break;
                        default:
                            break;
                    }

                    $template = 'email_appointment';
                    $commenters_name = array();
                    $commenters_id = array();
                    $this->AppointmentComment = ClassRegistry::init('AppointmentComment');
                    $this->User = ClassRegistry::init('User');
                    $comments = $this->AppointmentComment->findAllByAppointmentId($viewVarsTmp['event_id']);

                    if (!empty($comments)) {
                        foreach ($comments as $key => $comment) {
                            $commenters_id[$key] = $comment['AppointmentComment']['user_id'];
                        }
                        foreach ($commenters_id as $key => $commenter) {
                            $user = $this->User->findById($commenter);
                            $commenters_name[] = $user['User']['full_name'];
                        }
                        $viewVarsTmp['commenters_name'] = implode(", ", $commenters_name);
                    }

                    $viewVars['customer_id'] = isset($viewVarsTmp['customer_id']) ? $viewVarsTmp['customer_id'] : null;
                    $viewVars['customer_name'] = isset($viewVarsTmp['customer_name']) ? $viewVarsTmp['customer_name'] : null;
                    $viewVars['assigned_to'] = isset($viewVarsTmp['assigned_to']) ? $viewVarsTmp['assigned_to'] : null;
                    $viewVars['appointment_date'] = isset($viewVarsTmp['date']) ? Fecha::toSendgridFormat($viewVarsTmp['date'], Fecha::_FORMATO_BD_FECHA) : null;
                    $viewVars['appointment_start_time'] = isset($viewVarsTmp['start_time']) ? strftime("%H:%M", strtotime($viewVarsTmp['start_time'])) : null;
                    $viewVars['appointment_end_time'] = isset($viewVarsTmp['end_time']) ? strftime("%H:%M", strtotime($viewVarsTmp['end_time'])) : null;
                    $viewVars['appointment_feeling'] = isset($viewVarsTmp['feeling']) ? $viewVarsTmp['feeling'] : null;
                    $viewVars['appointment_feeling_color'] = isset($viewVarsTmp['feeling_color']) ? $viewVarsTmp['feeling_color'] : null;
                    $viewVars['appointment_customer_performance_summary'] = isset($viewVarsTmp['customer_performance_summary']) ? $viewVarsTmp['customer_performance_summary'] : null;
                    $viewVars['appointment_mtd'] = isset($viewVarsTmp['mtd']) ? $viewVarsTmp['mtd'] : null;
                    $viewVars['appointment_qtd'] = isset($viewVarsTmp['qtd']) ? $viewVarsTmp['qtd'] : null;
                    $viewVars['appointment_ytd'] = isset($viewVarsTmp['ytd']) ? $viewVarsTmp['ytd'] : null;
                    $viewVars['appointment_objectives'] = isset($viewVarsTmp['objectives']) ? $viewVarsTmp['objectives'] : null;
                    $viewVars['appointment_feedback'] = isset($viewVarsTmp['feedback']) ? $viewVarsTmp['feedback'] : null;
                    $viewVars['appointment_notes'] = isset($viewVarsTmp['notes']) ? $viewVarsTmp['notes'] : null;
                    $viewVars['appointment_url'] = isset($viewVarsTmp['link']) ? $viewVarsTmp['link'] : null;
                    $viewVars['appointment_comment'] = isset($viewVarsTmp['comment']) ? $viewVarsTmp['comment'] : null;
                    $viewVars['commenters_name'] = isset($viewVarsTmp['commenters_name']) ? $viewVarsTmp['commenters_name'] : null;
                    break;
                case ConstantsAlerts::TASK:
                    $email_type_id = ConstantsEmailTypes::TASK;
                    $template = 'email_task';

                    $viewVars['created_by'] = isset($viewVarsTmp['creator']) ? $viewVarsTmp['creator'] : null;
                    $viewVars['assigned_to'] = isset($viewVarsTmp['assigned_to']) ? $viewVarsTmp['assigned_to'] : null;
                    $viewVars['deadline'] = isset($viewVarsTmp['deadline']) ? Fecha::toSendgridFormat($viewVarsTmp['deadline'], Fecha::_FORMATO_VISTA_FECHA) : null;
                    $viewVars['status'] = isset($viewVarsTmp['status']) ? $viewVarsTmp['status'] : null;
                    $viewVars['customer_name'] = isset($viewVarsTmp['customer']) ? $viewVarsTmp['customer'] : null;
                    $viewVars['body'] = isset($viewVarsTmp['body']) ? $viewVarsTmp['body'] : null;
                    $viewVars['link'] = isset($viewVarsTmp['link']) ? $viewVarsTmp['link'] : null;
                    break;
                case ConstantsAlerts::EVENT:
                    $email_type_id = ConstantsEmailTypes::EVENT;
                    switch ($action) {
                        case ConstantsActions::ADD:
                            $viewVars['actions'] = ConstantsActionsNames::CREATE;
                            break;
                        case ConstantsActions::EDIT:
                            $viewVars['actions'] = ConstantsActionsNames::UPDATE;
                            break;
                        case ConstantsActions::COMMENT:
                            $viewVars['actions'] = ConstantsActionsNames::COMMENT;
                            break;
                        case ConstantsActions::DELETE:
                            $viewVars['actions'] = ConstantsActionsNames::DELETE;
                            break;
                        default:
                            break;
                    }

                    $template = 'email_event';

                    $viewVars['created_by'] = isset($viewVarsTmp['user_creator']) ? $viewVarsTmp['user_creator'] : null;
                    $viewVars['assigned_to'] = isset($viewVarsTmp['assigned_to']) ? $viewVarsTmp['assigned_to'] : null;
                    $viewVars['start_date'] = isset($viewVarsTmp['date']) ? Fecha::toSendgridFormat($viewVarsTmp['date'], Fecha::_FORMATO_BD_FECHA) : null;
                    $viewVars['start_time'] = isset($viewVarsTmp['start_time']) ? strftime("%H:%M", strtotime($viewVarsTmp['start_time'])) : null;
                    $viewVars['end_date'] = isset($viewVarsTmp['end_date']) ? Fecha::toSendgridFormat($viewVarsTmp['end_date'], Fecha::_FORMATO_BD_FECHA) : null;
                    $viewVars['end_time'] = isset($viewVarsTmp['end_time']) ? strftime("%H:%M", strtotime($viewVarsTmp['end_time'])) : null;
                    $viewVars['description'] = isset($viewVarsTmp['description']) ? $viewVarsTmp['description'] : null;
                    $viewVars['feeling'] = isset($viewVarsTmp['feeling']) ? $viewVarsTmp['feeling'] : null;
                    $viewVars['feeling_color'] = isset($viewVarsTmp['feeling_color']) ? $viewVarsTmp['feeling_color'] : null;
                    $viewVars['appointment_url'] = isset($viewVarsTmp['link']) ? $viewVarsTmp['link'] : null;
                    $viewVars['comment'] = isset($viewVarsTmp['comment']) ? $viewVarsTmp['comment'] : null;
                    break;
                default:
                    break;
            }

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' => $subject,
                    'body' => $text,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'template' => $template,
                    'email_type_id' => $email_type_id,
                    'view_vars' => serialize($viewVars),
                    'attachments' => serialize($files),
                    'platform_id' => ConstantsPlatform::GNM,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $user ? $user['User']['country_id'] : null,
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $user ? $user['User']['aag_region_id'] : null,
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Update email field sent to true.
     */
    public function changeStatusSent($emailId)
    {
        $email = $this->findById($emailId);
        $email['Email']['sent'] = ConstantsEmail::SEND;
        $fields = array(
            'Email' => array(
                'sent',
            )
        );
        return $this->save($email, true, $fields);
    }

    /**
     * Adds a retry and saves the error message in the email.
     */
    public function addRetry($mensaje_error, $emailId)
    {
        $email = $this->findById($emailId);
        $email['Email']['retries'] = $email['Email']['retries'] + 1;
        $email['Email']['last_error_message'] = $mensaje_error;

        $fields = array(
            'Email' => array(
                'retries',
                'last_error_message'
            )
        );
        return $this->save($email, true, $fields);
    }

    /**
     * Create the Email that is sent when the user wants to recover the password.
     */
    public function newEmailRecoverPassword($emailTo, $viewVars, $user, $languageCode)
    {
        if (!empty($emailTo)) {
            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' =>  'GNM AAG: ' . __t('Email.Recover_password', $languageCode),
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => 'email_recover_password',
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::RECOVER_PASSWORD,
                    'platform_id' => ConstantsPlatform::GNM,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $user['User']['country_id'],
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $user['User']['aag_region_id']
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Create the Email that is sent when a new user was created.
     */
    public function newEmailNewUserPassword($emailTo, $viewVars, $user, $languageCode)
    {
        if (!empty($emailTo)) {
            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' => 'GNM AAG: ' . __t('Email.New_user_password', $languageCode),
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => 'email_new_user_password',
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::NEW_USER_PASSWORD,
                    'platform_id' => ConstantsPlatform::GNM,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $user['User']['country_id'],
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $user['User']['aag_region_id']
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    public function attach_ics_email($subject, $body, $view_vars)
    {
        $ics_name = 'GNM_Alliance_' . uniqid();
        $icalobj = new ZCiCal();
        $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
        $eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $subject));
        $eventobj->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent($body . '<br> Assigned to: ' . $view_vars['assigned_to'])));
        $eventobj->addNode(new ZCiCalDataNode("UID:" . '123'));
        $eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($view_vars['date'] . " " . $view_vars['start_time'])));
        $eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($view_vars['date'] . " " . $view_vars['end_time'])));

        $filename = DIR_APPOINTMENT_ICS_ABSOLUTE . DS . $ics_name . '.ics';
        $file = fopen($filename, 'w');
        fwrite($file, $icalobj->export());
        fclose($file);

        return array($filename);
    }

    public function sendEmailTaskDeadline($emailTo, $task, $user, $languageCode)
    {
        if (!empty($emailTo)) {
            $viewVars = array(
                'language_code' => $languageCode,
                'title' => isset($task['title']) ? $task['title'] : null,
                'created_by' => isset($task['creator']) ? $task['creator'] : null,
                'assigned_to' => isset($task['assigned_to']) ? $task['assigned_to'] : null,
                'deadline' => isset($task['deadline']) ? Fecha::toSendgridFormat($task['deadline'], Fecha::_FORMATO_VISTA_FECHA) : null,
                'status' => isset($task['status']) ? $task['status'] : null,
                'customer_name' => isset($task['customer']) ? $task['customer'] : null,
                'body' => isset($task['body']) ? $task['body'] : null,
                'link' => isset($task['link']) ? $task['link'] : null
            );

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'bcc' => GNMAAG_BCC,
                    'subject' => sprintf(__t('Alert.Task_deadline_expired', $languageCode), $task['title']),
                    'body' => null,
                    'email_type_id' => ConstantsEmailTypes::TASK,
                    'view_vars' => serialize($viewVars),
                    'sent' => ConstantsBooleans::NO,
                    'creation_date' => date('Y-m-d H:i:s'),
                    'template' => 'email_task',
                    'platform_id' => ConstantsPlatform::GNM,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $user['User']['country_id'],
                    'aag_region_id' => $user['User']['aag_region_id']
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    public function file_post_content($url, $data, $access_token = null, $http_method = null, $office_code = null)
    {
        $post_data = http_build_query($data);
        if (is_null($access_token)) {
            $opts = array(
                'http' =>
                array(
                    'method' => 'POST',
                    'header' => array(
                        'Content-Type: application/x-www-form-urlencoded',
                        'User-Agent: CakePHP'
                    ),
                    'content' => $post_data
                )
            );
        } else {
            $opts = array(
                'http' =>
                array(
                    'method' => $http_method,
                    'header' => array(
                        'Authorization: Bearer ' . $access_token,
                        'Content-Type: application/json',
                        'User-Agent: CakePHP'
                    ),
                    'content' => json_encode($data)
                )
            );
            if ($http_method == ConstantsHttpMethods::PATCH || $http_method == ConstantsHttpMethods::DELETE) {
                $url = $url . '/' . $office_code;
            }
        }
        $context = stream_context_create($opts);

        return json_decode(file_get_contents($url, false, $context), true);
    }

    /**
     * Delete emails older than 3 months and have been sent.
     */
    public function deleteOldEmail()
    {
        $emailIds = $this->find(
            'list',
            array(
                'conditions' => array(
                    'sent' => ConstantsEmail::SEND,
                    'creation_date <= ' => date('Y-m-d', strtotime("-3 months")),
                ),
                'fields' => array(
                    'Email.id',
                )
            )
        );

        if (!empty($emailIds)) {
            $this->deleteAll(array('Email.id' => array_values($emailIds)));
        }
    }

    /**
     * Creates the Email that is sent to the garage when a booking is created.
     */
    public function newEmailBookingGarage($booking, $garageEmail, $garageNetworkId, $garage, $network, $quotationDetails)
    {
        $this->Language = ClassRegistry::init('Language');

        // string composed of email and language id
        $emailLanguage = explode(' ', $garageEmail);

        $emailTo = $emailLanguage[0];

        if (!empty($emailTo)) {

            $languageId = $emailLanguage[1];
            $language = $this->Language->findById($languageId);

            if ($language) {
                $languageCode = $language['Language']['code'];
            } else {
                $languageCode = Configure::read('network_language.' . $booking['Booking']['network_id']) ?? ConstantsLanguages::ENGLISH_CODE;
            }

            $viewVars = array(
                'language_code' => $languageCode,
                'booking_date' => Fecha::toSendgridFormat($booking['Booking']['date'], Fecha::_FORMATO_BD_FECHA),
                'booking_time_from' => $booking['Booking']['time'] ?? '',
                'booking_time_to' => $booking['Booking']['time_to'] ?? '',
                'distance_type' => strtoupper($network['Network']['distance_unit']),
                'garage_name' => $garage['Garage']['name'] ?? '',
                'customer_name' => $booking['Booking']['customer_name'] ?? '',
                'customer_email' => $booking['Booking']['customer_email'] ?? '',
                'customer_phone' => $booking['Booking']['customer_phone'] ?? '',
                'network_name' => $network['Network']['name'] ?? '',
                'booking_additional_info' => $booking['Booking']['additional_info'] ?? '',
                'booking_url' => ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . '/redirect/email?url=' . "/bookings/home/" . $garageNetworkId,
                'quotation_id' => $booking['Booking']['quotation_id'] ?? '',
                'quotation_date' => $quotationDetails['quotation_date'] ?? '', // it comes from Leadgen in the right format (yyyy-mm-ddZ)
                'quotation_expiration' => $quotationDetails['quotation_expiration'] ?? '', // it comes from Leadgen in the right format (yyyy-mm-ddZ)
                'vehicle_brand' => $quotationDetails['vehicle_brand'] ?? '',
                'vehicle_model' => $quotationDetails['vehicle_model'] ?? '',
                'vehicle_version' => $quotationDetails['vehicle_version'] ?? '',
                'vehicle_plate' => $quotationDetails['vehicle_plate'] ?? '',
                'vehicle_vin' => $quotationDetails['vehicle_vin'] ?? '',
                'vehicle_mileage' => $quotationDetails['vehicle_mileage'] ?? '',
                'quotation_vat' => $quotationDetails['quotation_vat'] ?? '',
                'quotation_jobs' => $quotationDetails['quotation_jobs'] ?? '',
                'totals' => $quotationDetails['totals'] ?? '',
                'garage_trading_name' => $garage['Garage']['business_name'] ?? '',
            );

            if (in_array($booking['Booking']['network_id'], array(NETWORK_ID_GV, NETWORK_ID_GC))) {
                $platformId = $booking['Booking']['network_id'] == NETWORK_ID_GV ? ConstantsPlatform::GV : ConstantsPlatform::GC;
                $templateName = 'email_booking_garage_gv';
                $subject = sprintf("%s", __t('EmailGv.Booking_subject_garage', $languageCode));
            } else {
                $platformId = ConstantsPlatform::AGN;
                $templateName = 'email_booking_garage';
                $subject = sprintf(__t('Email.Booking_subject_garage', $languageCode), $booking['Booking']['id']);
            }

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' => $subject,
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => $templateName,
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::BOOKING_GARAGE,
                    'platform_id' => $platformId,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $this->getCountryIdFromGarage($booking['Booking']['garage_id']),
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $garage['Garage']['aag_region_id'],
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Creates the Email that is sent to the customer when a booking is created.
     */
    public function newEmailBookingCustomer($booking, $garage, $network, $quotationDetails, $customerLanguageCode)
    {
        $emailTo = Texto::encryptDecryptText($booking['Booking']['customer_email'], false);

        if (!empty($emailTo)) {
            $languageId = $this->getWebLanguageIdFromLanguageCodeAndNetwork($customerLanguageCode, $network['Network']['id']);
            if (!isset($languageId) || empty($languageId)) {
                $languageId = null;
            }

            $viewVars = array(
                'language_code' => $customerLanguageCode,
                'garage_name' => $garage['Garage']['name'] ?? '',
                'booking_date' => Fecha::toSendgridFormat($booking['Booking']['date'], Fecha::_FORMATO_BD_FECHA),
                'booking_time_from' => $booking['Booking']['time'] ?? '',
                'booking_time_to' => $booking['Booking']['time_to'] ?? '',
                'distance_type' => strtoupper($network['Network']['distance_unit']),
                'customer_name' => $booking['Booking']['customer_name'] ?? '',
                'network_name' => $network['Network']['name'] ?? '',
                'garage_postcode' => $garage['Garage']['postcode'] ?? '',
                'garage_address' => !empty($garage['Garage']['address1']) ? $garage['Garage']['address1'] : null,
                'garage_address2' => !empty($garage['Garage']['address2']) ? $garage['Garage']['address2'] : null,
                'garage_address3' => !empty($garage['Garage']['address3']) ? $garage['Garage']['address3'] : null,
                'garage_address4' => !empty($garage['Garage']['address4']) ? $garage['Garage']['address4'] : null,
                'garage_phone' => $garage['Garage']['phone'] ?? '',
                'customer_email' => $booking['Booking']['customer_email'] ?? '',
                'customer_phone' => $booking['Booking']['customer_phone'] ?? '',
                'garage_city' => isset($garage['Garage']['city']) ? $garage['Garage']['city'] : null,
                'garage_town_city' => $garage['Garage']['town'] ?? '',
                'garage_email' => $garage['Garage']['email'] ?? '',
                'garage_web' => $garage['Garage']['web'] ?? '',
                'quotation_id' => $booking['Booking']['quotation_id'] ?? '',
                'quotation_date' => $quotationDetails['quotation_date'] ?? '',  // it comes from Leadgen in the right format (yyyy-mm-ddZ)
                'quotation_expiration' => $quotationDetails['quotation_expiration'] ?? '',  // it comes from Leadgen in the right format (yyyy-mm-ddZ)
                'vehicle_brand' => $quotationDetails['vehicle_brand'] ?? '',
                'vehicle_model' => $quotationDetails['vehicle_model'] ?? '',
                'vehicle_version' => $quotationDetails['vehicle_version'] ?? '',
                'vehicle_plate' => $quotationDetails['vehicle_plate'] ?? '',
                'vehicle_vin' => $quotationDetails['vehicle_vin'] ?? '',
                'vehicle_mileage' => $quotationDetails['vehicle_mileage'] ?? '',
                'quotation_vat' => $quotationDetails['quotation_vat'] ?? '',
                'quotation_jobs' => $quotationDetails['quotation_jobs'] ?? '',
                'totals' => $quotationDetails['totals'] ?? '',
                'garage_trading_name' => $garage['Garage']['business_name'] ?? '',
            );

            if (in_array($booking['Booking']['network_id'], array(NETWORK_ID_GV, NETWORK_ID_GC))) {
                $platformId = $booking['Booking']['network_id'] == NETWORK_ID_GV ? ConstantsPlatform::GV : ConstantsPlatform::GC;
                $templateName = 'email_booking_customer_gv';
                $subject = sprintf("%s %s", __t('EmailGv.Booking_subject_customer', $customerLanguageCode), ($garage['Garage']['name'] ?? 'Garagevergelijker.nl'));
            } else {
                $platformId = ConstantsPlatform::AGN;
                $templateName = 'email_booking_customer';
                $subject = sprintf(__t('Email.Booking_subject_customer', $customerLanguageCode), Fecha::toFormatoVista($booking['Booking']['date']));
            }

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' => $subject,
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => $templateName,
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::BOOKING_CUSTOMER,
                    'platform_id' => $platformId,
                    'language_web_id' => $languageId,
                    'country_id' => $this->getCountryIdFromGarage($garage['Garage']['id']),
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $garage['Garage']['aag_region_id'],
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Creates the Email that is sent to the distributors when a booking with quotation is created.
     */
    public function newEmailBookingDistributor($booking, $distributor, $garage, $network, $quotationDetails)
    {
        $this->Language = ClassRegistry::init('Language');

        $emailTo = $distributor['Distributor']['email'];

        if (!empty($emailTo)) {
            $languageId = $distributor['Distributor']['language_id'];
            $language = $this->Language->findById($languageId);

            if ($language) {
                $languageCode = $language['Language']['code'];
            } else {
                $languageCode = Configure::read('network_language.' . $booking['Booking']['network_id']) ?? ConstantsLanguages::ENGLISH_CODE;
            }

            $viewVars = array(
                'language_code' => $languageCode,
                'garage_name' => $garage['Garage']['name'] ?? '',
                'garage_city' => isset($garage['Garage']['city']) ? $garage['Garage']['city'] : null,
                'garage_town_city' => $garage['Garage']['town'] ?? '',
                'garage_phone' => $garage['Garage']['phone'] ?? $garage['Garage']['mobile'] ?? '',
                'booking_date' => Fecha::toSendgridFormat($booking['Booking']['date'], Fecha::_FORMATO_BD_FECHA),
                'booking_time_from' => $booking['Booking']['time'] ?? '',
                'booking_time_to' => $booking['Booking']['time_to'] ?? '',
                'work_name' => $booking['Booking']['work_name'] ?? '',
                'vehicle_plate' => $booking['Booking']['plate'] ?? '',
                'vehicle_vin' => $booking['Booking']['vin'] ?? '',
                'vehicle_brand' => $booking['Booking']['brand'] ?? '',
                'vehicle_model' => $booking['Booking']['model'] ?? '',
                'vehicle_version' => $booking['Booking']['version'] ?? '',
                'vehicle_mileage' => $booking['Booking']['mileage'] ?? '',
                'distance_type' => strtoupper($network['Network']['distance_unit']),
                'garage_erp_code' => strtolower($garage['Garage']['ref_code']),
                'garage_trading_name' => $garage['Garage']['business_name'] ?? '',
                'distributor_name' => $distributor['Distributor']['name'] ?? '',
                'quotation_vat' => $quotationDetails['quotation_vat'] ?? '',
                'quotation_jobs' => $quotationDetails['quotation_jobs'] ?? '',
                'totals' => $quotationDetails['totals'] ?? '',
            );

            if (in_array($booking['Booking']['network_id'], array(NETWORK_ID_GV, NETWORK_ID_GC))) {
                $platformId = $booking['Booking']['network_id'] == NETWORK_ID_GV ? ConstantsPlatform::GV : ConstantsPlatform::GC;
            } else {
                $platformId = ConstantsPlatform::AGN;
            }

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' => sprintf(__t('Email.Booking_subject_distributor', $languageCode), $booking['Booking']['id'], $garage['Garage']['name'], $garage['Garage']['ref_code']),
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => 'email_booking_distributor',
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::BOOKING_DISTRIBUTOR,
                    'platform_id' => $platformId,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $this->getCountryIdFromGarage($booking['Booking']['garage_id']),
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $garage['Garage']['aag_region_id'],
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Creates the Email that is sent to the garage when a enquiry is created.
     */
    public function newEmailEnquiryGarage($enquiry, $garageEmail, $network, $garageNetworkId, $garage)
    {
        $this->Language = ClassRegistry::init('Language');

        // string composed of email and language id
        $emailLanguage = explode(' ', $garageEmail);

        $emailTo = $emailLanguage[0];

        if (!empty($emailTo)) {

            $languageId = $emailLanguage[1];
            $language = $this->Language->findById($languageId);

            if ($language) {
                $languageCode = $language['Language']['code'];
            } else {
                $languageCode = Configure::read('network_language.' . $enquiry['Enquiry']['network_id']) ?? ConstantsLanguages::ENGLISH_CODE;
            }

            $viewVars = array(
                'language_code' => $languageCode,
                'network_name' => $network['Network']['name'],
                'enquiry_url' => ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . '/redirect/email?url=' . "/enquiries/edit_enquiry/" . $garageNetworkId . "/" . $enquiry['Enquiry']['id'],
                'garage_name' => $garage['Garage']['name'],
                'garage_city' => isset($garage['Garage']['city']) ? $garage['Garage']['city'] : null,
                'garage_town_city' => $garage['Garage']['town'],
                'vehicle_brand' => $enquiry['Enquiry']['brand'],
                'vehicle_model' => $enquiry['Enquiry']['model'],
                'vehicle_version' => $enquiry['Enquiry']['version'],
                'customer_name' => $enquiry['Enquiry']['name'],
                'customer_phone' => $enquiry['Enquiry']['phone'],
                'vehicle_plate' => $enquiry['Enquiry']['plate'],
                'vehicle_vin' => $enquiry['Enquiry']['vin'],
                'customer_email' => $enquiry['Enquiry']['email'],
                'vehicle_mileage' => $enquiry['Enquiry']['mileage'],
                'enquiry_date' => Fecha::toSendgridFormat($enquiry['Enquiry']['creation_date'], Fecha::_FORMATO_BD_FECHA),
                'work_name' => $enquiry['Enquiry']['work_name'],
                'enquiry_text' => $enquiry['Enquiry']['description'],
                'garage_trading_name' => $garage['Garage']['business_name']
            );

            if (in_array($enquiry['Enquiry']['network_id'], array(NETWORK_ID_GV, NETWORK_ID_GC))) {
                $platformId = $enquiry['Enquiry']['network_id'] == NETWORK_ID_GV ? ConstantsPlatform::GV : ConstantsPlatform::GC;
                $templateName = 'email_enquiry_garage_gv';
                $subject = sprintf(__t('EmailGv.Enquiry_subject_garage', $languageCode));
            } else {
                $platformId = ConstantsPlatform::AGN;
                $templateName = 'email_enquiry_garage';
                $subject = 'GNM AAG: ' . __t('Email.Enquiry_subject_garage', $languageCode);
            }

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' => $subject,
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => $templateName,
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::ENQUIRY_GARAGE,
                    'platform_id' => $platformId,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $this->getCountryIdFromGarage($enquiry['Enquiry']['garage_id']),
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $garage['Garage']['aag_region_id'],
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Creates the Email that is sent to the customer when a enquiry is created.
     */
    public function newEmailEnquiryCustomer($enquiry, $garage, $garageEmails, $network, $customerLanguageCode)
    {
        $emailTo = Texto::encryptDecryptText($enquiry['Enquiry']['email'], false);

        if (!empty($emailTo)) {
            $languageId = $this->getWebLanguageIdFromLanguageCodeAndNetwork($customerLanguageCode, $network['Network']['id']);
            if (!isset($languageId) || empty($languageId)) {
                $languageId = null;
            }
            $garageEmailString = "";

            foreach ($garageEmails as $garageEmail) {
                // string composed of email and language id
                $emailLanguage = explode(' ', $garageEmail);

                if (!empty($garageEmailString)) {
                    $garageEmailString .= ";";
                }

                $garageEmailString .= $emailLanguage[0];
            }

            $viewVars = array(
                'language_code' => $customerLanguageCode,
                'garage_name' => $garage['Garage']['name'],
                'garage_url' => $enquiry['Enquiry']['garage_url'],
                'garage_email' => $garageEmailString,
                'garage_city' => isset($garage['Garage']['city']) ? $garage['Garage']['city'] : null,
                'garage_town_city' => $garage['Garage']['town'],
                'vehicle_brand' => $enquiry['Enquiry']['brand'],
                'vehicle_model' => $enquiry['Enquiry']['model'],
                'vehicle_version' => $enquiry['Enquiry']['version'],
                'customer_name' => $enquiry['Enquiry']['name'],
                'customer_phone' => $enquiry['Enquiry']['phone'],
                'vehicle_plate' => $enquiry['Enquiry']['plate'],
                'vehicle_vin' => $enquiry['Enquiry']['vin'],
                'customer_email' => $enquiry['Enquiry']['email'],
                'vehicle_mileage' => $enquiry['Enquiry']['mileage'],
                'enquiry_date' => Fecha::toSendgridFormat($enquiry['Enquiry']['creation_date'], Fecha::_FORMATO_BD_FECHA),
                'work_name' => $enquiry['Enquiry']['work_name'],
                'network_name' => $network['Network']['name'],
                'enquiry_text' => $enquiry['Enquiry']['description'],
                'garage_trading_name' => $garage['Garage']['business_name']
            );

            if (in_array($enquiry['Enquiry']['network_id'], array(NETWORK_ID_GV, NETWORK_ID_GC))) {
                $platformId = $enquiry['Enquiry']['network_id'] == NETWORK_ID_GV ? ConstantsPlatform::GV : ConstantsPlatform::GC;
                $templateName = 'email_enquiry_customer_gv';
                $subject = sprintf(__t('EmailGv.Enquiry_subject_customer', $customerLanguageCode));
            } else {
                $platformId = ConstantsPlatform::AGN;
                $templateName = 'email_enquiry_customer';
                $subject = 'GNM AAG: ' . __t('Email.Enquiry_subject_customer', $customerLanguageCode);
            }

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' =>  $subject,
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => $templateName,
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::ENQUIRY_CUSTOMER,
                    'platform_id' => $platformId,
                    'language_web_id' => $languageId,
                    'country_id' => $this->getCountryIdFromGarage($enquiry['Enquiry']['garage_id']),
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $garage['Garage']['aag_region_id'],
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Creates the Email that is sent to the customer when a garage answer the enquiry.
     */
    public function newEmailEnquiryGarageAnswer($enquiry, $garage)
    {
        $emailTo = Texto::encryptDecryptText($enquiry['Enquiry']['email'], false);

        if (!empty($emailTo)) {
            $languageCode = Configure::read('network_language.' . $enquiry['Enquiry']['network_id']) ?? ConstantsLanguages::ENGLISH_CODE;

            $viewVars = array(
                'language_code' => $languageCode,
                'garage_name' => $garage['Garage']['name'],
                'enquiry_date' => Fecha::toSendgridFormat($enquiry['Enquiry']['date_answered'], Fecha::_FORMATO_BD_FECHA),
                'enquiry_answer' => $enquiry['Enquiry']['answer'],
                'garage_url' => $enquiry['Enquiry']['garage_url'],
                'garage_trading_name' => $garage['Garage']['business_name']
            );

            if (in_array($enquiry['Enquiry']['network_id'], array(NETWORK_ID_GV, NETWORK_ID_GC))) {
                $platformId = $enquiry['Enquiry']['network_id'] == NETWORK_ID_GV ? ConstantsPlatform::GV : ConstantsPlatform::GC;
            } else {
                $platformId = ConstantsPlatform::AGN;
            }

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' => 'GNM AAG: ' . __t('Email.Enquiry_subject_garage_answer', $languageCode),
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => 'email_enquiry_garage_answer',
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::ENQUIRY_GARAGE_ANSWER,
                    'platform_id' => $platformId,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $this->getCountryIdFromGarage($enquiry['Enquiry']['garage_id']),
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $garage['Garage']['aag_region_id'],
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Creates the Email that is sent to the contact when a new date is set to the GarageNetwork.
     */
    public function newEmailNewDateGarageNetwork($emailTo, $garageNetwork, $modified_date)
    {
        if (!empty($emailTo)) {
            $this->Garage = ClassRegistry::init('Garage');
            $this->Network = ClassRegistry::init('Network');
            $this->Distributor = ClassRegistry::init('Distributor');
            $this->Country = ClassRegistry::init('Country');
            $this->LeavingReasonType = ClassRegistry::init('LeavingReasonType');

            $distributorOrder = $this->Distributor->getFirstByGarageId($garageNetwork['GarageNetwork']['garage_id']);
            $garage = $this->Garage->findById($garageNetwork['GarageNetwork']['garage_id']);
            $network = $this->Network->findById($garageNetwork['GarageNetwork']['network_id']);
            $leavingReasonType = $this->LeavingReasonType->findById($garageNetwork['GarageNetwork']['reason_leaving_id']);

            $country = array();
            if (isset($garage['Garage']['province_id'])) {
                $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
            } elseif (isset($garage['Garage']['city_id'])) {
                $country = $this->Country->get_country_by_city($garage['Garage']['city_id']);
            }

            $languageCode = Configure::read('network_language.' . $garageNetwork['GarageNetwork']['network_id']) ?? ConstantsLanguages::ENGLISH_CODE;

            $viewVars = array(
                'language_code' => $languageCode,
                'garage_name' => $garage ? $garage['Garage']['name'] : null,
                'network_name' => $network ? $network['Network']['name'] : null,
                'garage_g_number' => $garage ? $garage['Garage']['g_number_id'] : null,
                'contract_start_date' => isset($modified_date['contract_start_date']) ? Fecha::toSendgridFormat($modified_date['contract_start_date'], Fecha::_FORMATO_VISTA_FECHA) : null,
                'contract_end_date' =>  isset($modified_date['contract_end_date']) ? Fecha::toSendgridFormat($modified_date['contract_end_date'], Fecha::_FORMATO_VISTA_FECHA) : null,
                'garage_current_charge' => $garageNetwork['GarageNetwork']['current_charge'],
                'garage_member_pays' => $garageNetwork['GarageNetwork']['member_pays'],
                'garage_garage_pays' => $garageNetwork['GarageNetwork']['garage_pays'],
                'leaving_reason' => $leavingReasonType ? $leavingReasonType['LeavingReasonType']['name_' . $languageCode] : null,
                'distributor_account_number_name' => $distributorOrder ? $distributorOrder['Distributor']['account_number'] . ' - ' . $distributorOrder['Distributor']['name'] : null
            );

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' =>  'GNM AAG: ' . __t('Email.Approved_date', $languageCode),
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => 'email_approved_date',
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::APPROVED_DATE,
                    'platform_id' => ConstantsPlatform::GNM,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $country['Country']['id'],
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $garage['Garage']['aag_region_id']
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Create the Email that is sent for the Booking reminder.
     */
    public function newEmailBookingReminder($booking, $garage, $garageEmail, $languageCode)
    {
        // string composed of email and language id
        $emailLanguage = explode(' ', $garageEmail);

        $emailTo = $emailLanguage[0];

        if (!empty($emailTo)) {
            $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
            $garageNetwork = $this->GarageNetwork->findByGarageAndNetwork($booking['Booking']['garage_id'], $booking['Booking']['network_id']);

            // Quotation details need to be retrieved from Leadgen
            if (!empty($booking['Booking']['quotation_id_leadgen'])) {
                $leadgen = new Leadgen();
                $quotationDetails = $leadgen->getQuotationDetails($booking['Booking']['quotation_id_leadgen']);
            }

            $viewVars = array(
                'language_code' => $languageCode,
                'garage_name' => $garage['Garage']['name'] ?? '',
                'booking_date' => !empty($booking['Booking']['date']) ? Fecha::toSendgridFormat($booking['Booking']['date'], Fecha::_FORMATO_BD_FECHA) : '',
                'vehicle_plate' => $booking['Booking']['plate'] ?? '',
                'vehicle_vin' => $booking['Booking']['vin'] ?? '',
                'quotation_id' => $booking['Booking']['quotation_id'] ?? '',
                'customer_name' => $booking['Booking']['customer_name'] ?? '',
                'customer_phone' => $booking['Booking']['customer_phone'] ?? '',
                'customer_email' => $booking['Booking']['customer_email'] ?? '',
                'garage_trading_name' => $garage['Garage']['business_name'] ?? '',
                'quotation_jobs' => $quotationDetails['quotation_jobs'] ?? '',
                'quotation_vat' => $quotationDetails['quotation_vat'] ?? '',
                'totals' => $quotationDetails['totals'] ?? '',
                'vehicle_brand' => $quotationDetails['vehicle_brand'] ?? '',
                'vehicle_mileage' => $quotationDetails['vehicle_mileage'] ?? '',
                'vehicle_model' => $quotationDetails['vehicle_model'] ?? '',
                'vehicle_version' => $quotationDetails['vehicle_version'] ?? '',
                'quotation_date' => $quotationDetails['quotation_date'] ?? '',
                'quotation_expiration' => $quotationDetails['quotation_expiration'] ?? '',
                'booking_url' => ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . '/redirect/email?url=' . "/bookings/home/" . $garageNetwork['GarageNetwork']['id'],
                'booking_time_to' => $booking['Booking']['time_to'] ?? '',
                'booking_time_from' => $booking['Booking']['time'] ?? ''
            );

            if (in_array($booking['Booking']['network_id'], array(NETWORK_ID_GV, NETWORK_ID_GC))) {
                $platformId = $booking['Booking']['network_id'] == NETWORK_ID_GV ? ConstantsPlatform::GV : ConstantsPlatform::GC;
            } else {
                $platformId = ConstantsPlatform::AGN;
            }

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' =>  'GNM AAG: ' . __t('Email.Booking_reminder', $languageCode),
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => 'email_booking_reminder',
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::BOOKING_REMINDER,
                    'platform_id' => $platformId,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $this->getCountryIdFromGarage($booking['Booking']['garage_id']),
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $garage['Garage']['aag_region_id']
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Get the pending Bookings and create a Email for each one for the reminder.
     */
    public function createBookingReminder()
    {
        $this->Booking = ClassRegistry::init('Booking');
        $this->Garage = ClassRegistry::init('Garage');

        $bookings = $this->Booking->getBookingsPending();

        foreach ($bookings as $booking) {
            $garage = $this->Garage->findById($booking['Booking']['garage_id']);

            if ($garage) {
                $garageEmails = $this->Garage->getGarageEmail($garage);
                if (!empty($garageEmails)) {
                    $languageCode = Configure::read('network_language.' . $booking['Booking']['network_id']) ?? ConstantsLanguages::ENGLISH_CODE;

                    foreach ($garageEmails as $garageEmail) {
                        $this->newEmailBookingReminder($booking, $garage, $garageEmail, $languageCode);
                    }
                }
            }
        }
    }

    /**
     * Get the pending Bookings and create a Email for each one for the second reminder.
     */
    public function createSecondBookingReminder()
    {
        $this->Booking = ClassRegistry::init('Booking');
        $this->Garage = ClassRegistry::init('Garage');

        $bookings = $this->Booking->getBookingsPending();

        $actualDate = new DateTime(date("Y-m-d"));

        foreach ($bookings as $booking) {
            $bookingDate = new DateTime($booking['Booking']['date']);
            $bookingCreationDate = new DateTime($booking['Booking']['creation_date']);
            $bookingCreationDate->setTime(0, 0, 0);

            $daysSinceCreation = $actualDate->diff($bookingCreationDate);
            $daysLeft = $actualDate->diff($bookingDate);

            if ($daysLeft->days <= 3 || $bookingDate < $actualDate || $daysSinceCreation->days >= 3) {
                $garage = $this->Garage->findById($booking['Booking']['garage_id']);
                if ($garage) {
                    $garageEmails = $this->Garage->getGarageEmail($garage);

                    if (!empty($garageEmails)) {
                        $languageCode = Configure::read('network_language.' . $booking['Booking']['network_id']) ?? ConstantsLanguages::ENGLISH_CODE;

                        foreach ($garageEmails as $garageEmail) {
                            $this->newEmailBookingReminder($booking, $garage, $garageEmail, $languageCode);
                        }
                    }
                }
            }
        }
    }

    /**
     * Create the Email that is sent when the Lobster GV sync is completed with the log file attached.
     */
    public function newEmailGaragesGVSync($filename, $logFile, $emailTo, $garageAagRegionId, $date, $prefix)
    {
        if (!empty($emailTo)) {
            $languageCode = ConstantsLanguages::ENGLISH_CODE;

            $viewVars = array(
                'language_code' => $languageCode,
                'attachments_filenames' => array($filename),
                'datetime' => Fecha::toSendgridFormat(date("Y-m-d H:i:s"), Fecha::_FORMATO_BD_FECHA_HORA),
                'user_name' => 'Admin',
            );

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' => __t('Garage.Garage', $languageCode) . ' ' . __t('General.Import', $languageCode) . ' ' . $prefix . ' ' . $date,
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => 'email_log_json_format',
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::LOG_JSON_FORMAT,
                    'attachments' => $logFile,
                    'platform_id' => ConstantsPlatform::GNM,
                    'language_id' => ConstantsLanguages::ENGLISH,
                    'country_id' => ConstantsCountries::NETHERLANDS,
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $garageAagRegionId
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Create the Email that is sent when a export was generated.
     */
    public function newEmailExport($filename, $file, $emailTo, $user, $controller)
    {
        if (!empty($emailTo)) {
            $languageCode = $user['language_code'];

            $viewVars = array(
                'language_code' => $languageCode,
                'attachments_filenames' => array($filename),
                'datetime' => Fecha::toSendgridFormat(date("Y-m-d H:i:s"), Fecha::_FORMATO_BD_FECHA_HORA),
                'user_name' => $user['username']
            );

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' => $controller . ' ' . __t('Email.Data_export', $languageCode) . ' ' . date('d/m/Y'),
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => 'email_data_export',
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::DATA_EXPORT,
                    'attachments' => $file,
                    'platform_id' => ConstantsPlatform::GNM,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $user['country_id'],
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $user['aag_region_id']
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Create the Email that is sent for the onboarding.
     */
    public function newEmailOnboarding($garageName, $emailTo, $garageTradingName, $network, $languageId, $countryId)
    {
        if (!empty($emailTo)) {
            $this->Language = ClassRegistry::init('Language');
            $language = $this->Language->findById($languageId);
            $languageCode = isset($language['Language']['code']) ? $language['Language']['code'] : Configure::read('LANGUAGE_CODE_DEFAULT');

            $viewVars = array(
                'language_code' => $languageCode,
                'garage_name' => $garageName,
                'garage_trading_name' => $garageTradingName,
                'network_name' => $network['Network']['name'],
                'gnm_url' => ConstantsHTTP::HTTPS . Configure::read('URL_BASE'),
            );

            if (in_array($network['Network']['id'], array(NETWORK_ID_GV, NETWORK_ID_GC))) {
                $platformId = $network['Network']['id'] == NETWORK_ID_GV ? ConstantsPlatform::GV : ConstantsPlatform::GC;
            } else {
                $platformId = ConstantsPlatform::AGN;
            }

            $networkWeb = str_replace("https://www.", "", $network['Network']['web']);

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' => sprintf(__t('Email.Onboarding_subject', $languageCode), $networkWeb),
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => 'email_onboarding_gv',
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::ONBOARDING,
                    'platform_id' => $platformId,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $countryId,
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => CakeSession::read('Auth.User.aag_region_id')
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Create the Email that is sent when objectives from distributors are being all applied (CRM).
     */

    public function newEmailObjectivesCompleted($filename, $file, $emailTo, $countryId, $aagRegionId, $userName, $objectiveName, $languageCode)
    {
        if (!empty($emailTo)) {
            $viewVars = array(
                'language_code' => $languageCode,
                'attachments_filenames' => array($filename),
                'datetime' => Fecha::toSendgridFormat(date("Y-m-d H:i:s"), Fecha::_FORMATO_BD_FECHA_HORA),
                'user_name' => $userName,
                'objective_name' => $objectiveName
            );

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' => 'GNM AAG: ' . __t('Email.Objectives_completed', $languageCode) . ' ' . date('d/m/Y'),
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => 'email_objective',
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::DISTRIBUTOR_OBJECTIVES,
                    'attachments' => $file,
                    'platform_id' => ConstantsPlatform::GNM,
                    'language_id' => $this->getLanguageIdFromLanguageCode($languageCode),
                    'country_id' => $countryId,
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => $aagRegionId
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Create the Email that is sent when objectives from distributors are being all applied (CRM).
     */
    public function newEmailImportDistributor($emailTo)
    {
        if (!empty($emailTo)) {
            $viewVars = array(
                'language_code' => ConstantsLanguages::ENGLISH_CODE,
                'datetime' => Fecha::toSendgridFormat(date("Y-m-d H:i:s"), Fecha::_FORMATO_BD_FECHA_HORA),
                'user_name' => 'Admin',
            );

            $email = array(
                'Email' => array(
                    'to' => $emailTo,
                    'subject' => 'GNM AAG: ' . __t('Email.Import_distributor', ConstantsLanguages::ENGLISH_CODE) . ' ' . date('d/m/Y'),
                    'body' => null,
                    'sent' => ConstantsEmail::NOT_SEND,
                    'retries' => ConstantsBooleans::NO,
                    'template' => 'email_import_distributor',
                    'view_vars' => serialize($viewVars),
                    'email_type_id' => ConstantsEmailTypes::IMPORT_DISTRIBUTOR,
                    'platform_id' => ConstantsPlatform::GNM,
                    'language_id' => ConstantsLanguages::ENGLISH,
                    'country_id' => ConstantsCountries::UNITED_KINGDOM,
                    'old' => ConstantsBooleans::NO,
                    'aag_region_id' => ConstantsAAGRegionId::UK
                )
            );

            $emailBd = $this->add($email);

            // for SendGrid email is sent when it's created
            if ($emailBd) {
                $this->sendSendgridEmail($emailBd);
                return true;
            }

            return $emailBd;
        } else {
            return true;
        }
    }

    /**
     * Get the Country ID from de the Garage ID.
     */
    private function getCountryIdFromGarage($garageId)
    {
        $this->Garage = ClassRegistry::init('Garage');
        $this->Province = ClassRegistry::init('Province');
        $this->Country = ClassRegistry::init('Country');

        $garageTmp = $this->Garage->findById($garageId);

        if ($garageTmp) {
            $province = $this->Province->findById($garageTmp['Garage']['province_id']);
            if (!empty($province)) {
                $country = $this->Country->findById($province['Province']['country_id']);
                return $country ? $country['Country']['id'] : null;
            }
        }

        return null;
    }

    /**
     * Get the Language ID from de the language code.
     */
    private function getLanguageIdFromLanguageCode($languageCode)
    {
        $this->Language = ClassRegistry::init('Language');

        $language = $this->Language->findByCode($languageCode);

        return $language ? $language['Language']['id'] : null;
    }

    /**
     * Get the Web Language ID from de the language code and network_id.
     */
    private function getWebLanguageIdFromLanguageCodeAndNetwork($languageCode, $networkId)
    {
        $this->LanguageWebNetwork = ClassRegistry::init('LanguageWebNetwork');

        $language = $this->LanguageWebNetwork->findByNetworkIdAndCode($networkId, $languageCode);

        return $language ? $language['LanguageWebNetwork']['id'] : null;
    }

    /**
     * Creates an email that is sent when number of unsent emails is greater than 41.
     */
    public function newEmailUnsentEmails()
    {
        $email = new CakeEmail(Configure::read('Email.configuracion'));
        $email->from(GNM_AAG_UNSENT_EMAILS_FROM);
        $email->to(GNM_AAG_UNSENT_EMAILS_TO);
        $email->subject('GNM AAG: ' . __t('Email.Unsent_emails_subject', GNM_AAG_UNSENT_EMAILS_MAX, ConstantsLanguages::ENGLISH_CODE) . ' ' . date('d/m/Y'));
        $email->template('email_unsent_emails', 'email');
        $email->emailFormat('html');

        $email->send();
    }

    /**
     * Gets the number of unsent emails.
     */
    public function getNumberUnsentEmails($token)
    {
        if (!isset($token) || isset($token) && $token != GNM_AAG_UNSENT_EMAILS_TOKEN) {
            throw new UnauthorizedException();
        }

        $unsentEmails = $this->find(
            'count',
            array(
                'conditions' => array(
                    'Email.sent' => 0,
                ),
                'fields' => array(
                    'Email.id'
                )
            )
        );

        if ($unsentEmails > GNM_AAG_UNSENT_EMAILS_MAX) {
            $this->newEmailUnsentEmails();
        }
    }

    /*
    * Generates a guid for the given network.
    *
    * @param Email $email
    */
    public function generateGuid(array $email)
    {
        $fields = array(
            'Email' => array(
                'id',
                'guid'
            )
        );

        $email['Email']['guid'] = CakeText::uuid();
        return $this->guardar($email, $fields);
    }

    /**
     * Generates a guid for every email in DB without it.
     */
    public function generateGuidForEveryEntranceWithoutIt()
    {
        $emailsWithoutGuid = $this->find('all', array(
            'conditions' => array(
                'Email.guid' => null
            )
        ));

        foreach ($emailsWithoutGuid as $email) {
            $this->generateGuid($email);
        }
    }
}
