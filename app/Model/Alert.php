<?php

class Alert extends AppModel
{
    public $useTable = 'alerts';

    public $hasOne = array(
        'User',
        'AlertType',
        'Email',
        'EmailDaily'
    );

    public $validate = array(
        'body' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'feedback' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'url' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    private $_queries = array(
        'search' => array(
            'fields' => array(
                'Alert.*'
            ),
            'order' => 'Alert.id desc',
        )

    );

    public function findTaskByUserIdAndRead($user_id, $read)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'Alert.user_id' => $user_id,
                    'Alert.read' => $read
                ),
                'limit' => ConstantsPagination::SIZE_ADVANCED_SEARCH,
                'order' => 'Alert.creation_date',
                'fields' => array(
                    'Alert.*'
                ),
            )
        );
    }

    public function conditions($fields)
    {
        $conditions = array();

        if (!empty($fields['body'])) {
            $conditions[] = $this->_conditionBody($fields['body']);
        }
        if (isset($fields['read'])) {
            $conditions[] = $this->_conditionRead($fields['read']);
        }
        if (!empty($fields['type'])) {
            $conditions[] = $this->_conditionType($fields['type']);
        }
        if (!empty($fields['from'])) {
            $conditions[] = $this->_conditionDateFrom($fields['from']);
        }
        if (!empty($fields['to'])) {
            $conditions[] = $this->_conditionDateTo($fields['to']);
        }

        return $conditions;
    }

    private function _conditionBody($body)
    {
        return array('Alert.body LIKE' => '%' . $body . '%');
    }

    private function _conditionRead($read)
    {
        //If you get a 2, recharge as is.
        if ($read == '1' or $read == '0') {
            return array('Alert.read' => $read);
        }
    }

    private function _conditionType($type)
    {
        return array('Alert.alert_type_id' => $type);
    }

    private function _conditionDateFrom($from_date)
    {
        $from_date = Fecha::toFormatoBd($from_date);
        return array('Alert.creation_date >=' => $from_date);
    }

    private function _conditionDateTo($to_date)
    {
        $to_date = Fecha::toFormatoBd($to_date);
        return array('Alert.creation_date <=' => date('Y-m-d', strtotime($to_date . " +1 days")));
    }

    public function new_alert($contacts, $type, $alert_body, $email_subject, $url, $view_vars, $event_id, $user_assigned_id, $action, $deleted = null, $feedback = null, $comment = null)
    {
        $config = CakeSession::read('Auth.User.Config');

        $this->User = ClassRegistry::init('User');
        $creation_user_id = CakeSession::read('Auth.User.id');

        $view_vars['contacts'] = array();
        foreach ($contacts as $contact) {
            $view_vars['contacts'][$contact['Contact']['email']] = $contact['Contact']['full_name'];
        }

        $contacts_emails_sent = array();
        $office_send = false;
        foreach ($contacts as $key => $contact) {
            $email_body = $alert_body . "<br>" . ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . '/redirect/email?url=' . $url;
            $view_vars_tmp = $view_vars;
            $view_vars_tmp['event_id'] = $event_id;
            $view_vars_tmp['event_action'] = $deleted;
            $view_vars_tmp['contact_id'] = $contact['Contact']['id'];
            if ($office_send == false) {
                $view_vars_tmp['send_office'] = true;
            } else {
                $view_vars_tmp['send_office'] = false;
            }

            $user = $this->User->findByContactId($contact['Contact']['id']);

            $languageCode = ConstantsLanguages::ENGLISH_CODE;
            if (isset($user['User']['language_id'])) {
                $languageCode = $this->User->Language->findById($user['User']['language_id'])['Language']['code'];
            }

            if (!empty($user)) {
                $alert = array(
                    'Alert' => array(
                        'user_id' => $user['User']['id'],
                        'alert_type_id' => $type,
                        'body' => $email_subject,
                        'feedback' => $feedback,
                        'url' => $url,
                        'read' => ConstantsBooleans::NO,
                        'creation_date' => date('Y-m-d H:i:s')
                    )
                );
                $fields = array(
                    'Alert' => array(
                        'user_id',
                        'alert_type_id',
                        'body',
                        'feedback',
                        'url',
                        'read',
                        'creation_date'
                    )
                );
                $view_vars_tmp['user_id'] = $user['User']['id'];

                if (!$comment) {
                    if ($user['User']['id'] != $creation_user_id && !in_array(strtolower($contact['Contact']['email']), $contacts_emails_sent)) {
                        $this->create();
                        $alert_bd = $this->guardar($alert, $fields);
                        if (!$alert_bd) {
                            return false;
                        }
                    }
                } else {
                    if ($user['User']['id'] != $view_vars['comment_user_id'] && !in_array(strtolower($contact['Contact']['email']), $contacts_emails_sent)) {
                        $this->create();
                        $alert_bd = $this->guardar($alert, $fields);
                        if (!$alert_bd) {
                            return false;
                        }
                    }
                }
            } else {
                unset($view_vars_tmp['link']);
                $email_body = $alert_body;
            }

            if (isset($user['User'])) {
                if (!in_array(strtolower($contact['Contact']['email']), $contacts_emails_sent)) {
                    $view_vars_tmp['full_name'] = $user['User']['name'] . ' ' . $user['User']['surname'];
                    array_push($contacts_emails_sent, strtolower($contact['Contact']['email']));
                    if ($config[ConstantsConfig::INSTANT_REPORT]) {
                        if (!$this->Email->newEmailAlert($contact['Contact']['email'], $email_body, $email_subject, $languageCode, $type, $view_vars_tmp, $user, $action)) {
                            return false;
                        } else {
                            $office_send = true;
                        }
                    } else if ($config[ConstantsConfig::DAILY_REPORT]) {
                        if (!$this->EmailDaily->newEmailAlert($contact['Contact']['email'], $email_body, $email_subject, $languageCode, $type, $view_vars_tmp, $user['User']['aag_region_id'])) {
                            return false;
                        } else {
                            $office_send = true;
                        }
                    }
                }
            } else {
                if (!in_array(strtolower($contact['Contact']['email']), $contacts_emails_sent)) {
                    $view_vars_tmp['full_name'] = $contact['Contact']['first_name'] . ' ' . $contact['Contact']['last_name'];
                    array_push($contacts_emails_sent, strtolower($contact['Contact']['email']));
                    if ($config[ConstantsConfig::INSTANT_REPORT]) {
                        if (!$this->Email->newEmailAlert($contact['Contact']['email'], $email_body, $email_subject, $languageCode, $type, $view_vars_tmp, null, $action)) {
                            return false;
                        } else {
                            $office_send = true;
                        }
                    } else if ($config[ConstantsConfig::DAILY_REPORT]) {
                        if (!$this->EmailDaily->newEmailAlert($contact['Contact']['email'], $email_body, $email_subject, $languageCode, $type, $view_vars_tmp, null)) {
                            return false;
                        } else {
                            $office_send = true;
                        }
                    }
                }
            }
        }
        $this->commit();
        return true;
    }

    public function new_alert_without_email($type, $alert_body, $url, $user_id)
    {
        $alert = array(
            'Alert' => array(
                'user_id' => $user_id,
                'alert_type_id' => $type,
                'body' => $alert_body,
                'url' => $url,
                'read' => ConstantsBooleans::NO,
                'creation_date' => date('Y-m-d H:i:s')
            )
        );
        $fields = array(
            'Alert' => array(
                'user_id',
                'alert_type_id',
                'body',
                'url',
                'read',
                'creation_date'
            )
        );

        $this->create();
        $alert_bd = $this->guardar($alert, $fields);
        if (!$alert_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function edit_alert($alert)
    {
        $fields = array(
            'Alert' => array(
                'id',
                'user_id',
                'alert_type_id',
                'body',
                'url',
                'read',
                'creation_date'
            )
        );

        $alert_bd = $this->guardar($alert, $fields);
        if (!$alert_bd) {
            return false;
        }
    }

    public function existAlert($alert_id)
    {
        $alert = $this->find('first', array('conditions' => array('Alert.id' => $alert_id)));
        if ($alert) {
            return true;
        }
        return false;
    }

    public function sendAlertAndEmailTaskDeadline($task)
    {
        $this->Contact = ClassRegistry::init('Contact');
        $this->User = ClassRegistry::init('User');
        $this->Email = ClassRegistry::init('Email');
        $this->Task = ClassRegistry::init('Task');

        $user = $this->User->findById($task['Task']['user_creation_id']);

        if ($user) {
            $languageCode = ConstantsLanguages::ENGLISH_CODE;
            if (isset($user['User']['language_id'])) {
                $languageCode = $this->User->Language->findById($user['User']['language_id'])['Language']['code'];
            }

            $task['languageCode'] = $languageCode;
            $contact = $this->Contact->findById($user['User']['contact_id']);
            if ($contact) {
                $task = $task['Task'];
                $task['creator'] = $user['User']['name'] . " " . $user['User']['surname'];
                $task_status = $this->Task->getTaskStatusByTaskId($task['id']);
                $task['status'] = $task_status['TaskStatus']['name' . __s()];
                $task['customer'] = $this->Task->getCustomerAppointmentByTaskId($task['id']);

                if (isset($task['user_assigned_id']) && !empty($task['user_assigned_id'])) {
                    $user_tmp = $this->User->findById($task['user_assigned_id']);
                    $task['assigned_to'] = $user_tmp['User']['name'] . " " . $user_tmp['User']['surname'];
                }

                $this->Email->sendEmailTaskDeadline($contact['Contact']['email'], $task, $user['User']['aag_region_id'], $task['languageCode']);

                if (!empty($user)) {
                    $fields = array(
                        'Alert' => array(
                            'user_id',
                            'alert_type_id',
                            'body',
                            'url',
                            'read',
                            'creation_date'
                        )
                    );
                    $alert = array(
                        'Alert' => array(
                            'user_id' => $user['User']['id'],
                            'alert_type_id' => ConstantsAlerts::TASK,
                            'body' => h(sprintf(__t('Alert.Task_deadline_expired', $languageCode), $task['title'])),
                            'url' => Router::url(array(
                                'controller' => 'tasks',
                                'action' => 'edit',
                                $task['id']
                            )),
                            'read' => ConstantsBooleans::NO,
                            'creation_date' => date('Y-m-d H:i:s')
                        )
                    );
                    $this->create();
                    $this->guardar($alert, $fields);
                    $this->commit();
                }
            }
        }
    }

    public function edit_alert_rm($alert_rm)
    {
        $this->RepairMaintenance = ClassRegistry::init('RepairMaintenance');
        $result = array();
        $status = true;
        $headers = apache_request_headers();
        $token = explode(' ', $headers['Authorization']);
        $token_data = $this->RepairMaintenance->get_token_data($token[1]);
        if ($token_data['id'] && time() < $token_data['exp']) {
            try {
                $alert = $this->findByRmAlertId($alert_rm->Alerta->id);
                if (empty($alert)) {
                    $fields = array(
                        'Alert' => array(
                            'user_id',
                            'alert_type_id',
                            'body',
                            'read',
                            'creation_date',
                            'rm_alert_id',
                        )
                    );
                    $data_alert = array(
                        'Alert' => array(
                            'user_id' => $alert_rm->Alerta->usuario_id,
                            'alert_type_id' => ConstantsAlerts::RM,
                            'body' => $alert_rm->Alerta->mensaje,
                            'read' => ConstantsBooleans::NO,
                            'creation_date' =>  date('Y-m-d H:i:s'),
                            'rm_alert_id' => $alert_rm->Alerta->id,
                        )
                    );
                    $this->create();
                    if ($this->save($data_alert, true, $fields)) {
                        $result = true;
                    } else {
                        $result = false;
                    }
                } else {
                    $fields = array(
                        'Alert' => array(
                            'id',
                            'read',
                        )
                    );
                    $data_alert = array(
                        'Alert' => array(
                            'id' => $alert['Alert']['id'],
                            'read' => $alert_rm->Alerta->leido,
                        )
                    );
                    if ($this->save($data_alert, true, $fields)) {
                        $result = true;
                    } else {
                        $result = false;
                    }
                }
            } catch (Exception $exception) {
                $status = false;
            }
        }
        return $this->RepairMaintenance->return_result($result, $status);
    }

    public function getAlertsWithItsTypeByUserId($user_id)
    {
        return array(
            'joins' => array(
                array(
                    'alias' => 'AlertType',
                    'table' => 'alerts_types',
                    'type' => 'INNER',
                    'conditions' => array(
                        'AlertType.id = Alert.alert_type_id'
                    ),
                ),
            ),
            'conditions' => array(
                'Alert.user_id' => $user_id,
            ),
            'fields' => array(
                'Alert.*',
                'AlertType.name_en'
            ),
            'order' => array(
                'Alert.id' => 'desc'
            )
        );
    }

    public function findForWidget(array $user)
    {
        $user_conditions = $this->getAlertsWithItsTypeByUserId($user['id'], $user['current_network']);
        $user_conditions['conditions']['read'] = false;
        $user_conditions['order'] = array(
            'Alert.creation_date DESC',
            'Alert.id DESC',
        );
        $user_conditions['limit'] = 5;
        return $this->find('all', $user_conditions);
    }
}
