<?php
class EmailDaily extends AppModel
{

    public $useTable = 'emails_daily';

    public $validate = array(
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
        'view_vars' => array(
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

    public function add($email, $language_code)
    {
        $fields = array(
            'EmailDaily' => array(
                'from',
                'to',
                'contact_id',
                'bcc',
                'subject',
                'body',
                'sent',
                'type',
                'action',
                'view_vars',
                'attachments',
                'creation_date',
            ),
        );

        $email['EmailDaily']['creation_date'] = date('Y-m-d H:i:s');
        $email['EmailDaily']['retries'] = 0;
        if (isset($email['EmailDaily']['view_vars']) || !empty($email['EmailDaily']['view_vars'])) {
            $unserialize_view_vars = unserialize($email['EmailDaily']['view_vars']);
        }

        $unserialize_view_vars['footer_text'] = __t('General.Email_footer_text', $language_code);
        $unserialize_view_vars['language_code'] = $language_code;

        $email['EmailDaily']['view_vars'] = serialize($unserialize_view_vars);

        $from_no_region = GNMAAG_EMAIL_CONFIGURATION_NO_REGION_FROM;

        $email['EmailDaily']['from'] = empty($email['EmailDaily']['from']) ? $from_no_region : $email['EmailDaily']['from'];
        $email['EmailDaily']['bcc'] = GNMAAG_BCC;

        $this->create();
        return $this->save($email, true, $fields);
    }

    public function newEmailAlert($email_to, $text, $subject, $language_code, $type, $view_vars)
    {
        $files = array();
        foreach ($view_vars['files'] as $file) {
            if (isset($file['AppointmentFile'])) {
                $filename = DIR_APPOINTMENT_FILES_ABSOLUTE . $file['AppointmentFile']['file'];
            } else if (isset($file['TaskFile'])) {
                $filename = DIR_TASK_FILES_ABSOLUTE . $file['TaskFile']['file'];
            }
            array_push($files, $filename);
        }

        $email = array(
            'EmailDaily' => array(
                'to' => $email_to,
                'contact_id' => $view_vars['contact_id'],
                'subject' => $subject,
                'body' => $text,
                'sent' => 0,
                'type' => $type,
                'action' => $view_vars['event_action'],
                'view_vars' => serialize($view_vars),
                'attachments' => serialize($files)
            )
        );

        return $this->add($email, $language_code);
    }

    public function getAllByNotSent()
    {
        return $this->find('all', array(
            'conditions' => array(
                'sent' => ConstantsBooleans::NO
            )
        ));
    }

    public function sendEmailDaily()
    {
        $this->Email = ClassRegistry::init('Email');
        $language_code = 'en';
        $emails_daily = $this->getAllByNotSent();
        $emails_tmp = array();
        $emailsDailyTypes = array(ConstantsEmailTypes::APPOINTMENT, ConstantsEmailTypes::EVENT, ConstantsEmailTypes::TASK);
        foreach ($emails_daily as $email) {
            // only if type is Appointment, Event or Task a new email is generated (Garage, RM and Article types doest't exists)
            if (in_array($email['EmailDaily']['type'], $emailsDailyTypes)) {
                $emails_tmp[$email['EmailDaily']['contact_id']]['Email']['from'] = $email['EmailDaily']['from'];
                $emails_tmp[$email['EmailDaily']['contact_id']]['Email']['to'] = $email['EmailDaily']['to'];
                $emails_tmp[$email['EmailDaily']['contact_id']]['Email']['cc'] = $email['EmailDaily']['cc'];
                $emails_tmp[$email['EmailDaily']['contact_id']]['Email']['bcc'] = $email['EmailDaily']['bcc'];
                $emails_tmp[$email['EmailDaily']['contact_id']]['Email']['subject'] = __t('Email.Daily_email', $language_code);
                $emails_tmp[$email['EmailDaily']['contact_id']]['Email']['body'] = $email['EmailDaily']['body'];
                $emails_tmp[$email['EmailDaily']['contact_id']]['Email']['sent'] = $email['EmailDaily']['sent'];
                $emails_tmp[$email['EmailDaily']['contact_id']]['Email']['type'] = $email['EmailDaily']['type'];
                $view_vars_tmp = unserialize($email['EmailDaily']['view_vars']);
                $view_vars_tmp['type'] = $email['EmailDaily']['type'];
                $emails_tmp[$email['EmailDaily']['contact_id']]['Email']['view_vars'][] = $view_vars_tmp;
                $attachments = unserialize($email['EmailDaily']['attachments']);
                if (!empty($attachments)) {
                    $emails_tmp[$email['EmailDaily']['contact_id']]['Email']['attachments'][] = $attachments;
                }
            }
        }

        $error = false;
        foreach ($emails_tmp as $email) {
            $email['Email']['view_vars'] = serialize($email['Email']['view_vars']);
            if (isset($email['Email']['attachments'])) {
                $email['Email']['attachments'] = serialize($email['Email']['attachments']);
            }

            $emailBd = $this->Email->add($email);

            // email is sent when it's created
            if ($emailBd) {
                $this->Email->sendSendgridEmail($emailBd);
            } else {
                $error = true;
            }
        }
        if (!$error) {
            if (!$this->_check_as_sent_emails($emails_daily)) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    private function _check_as_sent_emails($emails_daily)
    {
        $this->Email = ClassRegistry::init('Email');
        $fields = array(
            'Email' => array(
                'sent'
            )
        );

        foreach ($emails_daily as $key => $email) {
            $emails_daily[$key]['EmailDaily']['sent'] = ConstantsBooleans::YES;
            $email_daily = $this->guardar($emails_daily[$key], $fields);
            if (!$email_daily) {
                return false;
            }
        }

        return true;
    }
}
