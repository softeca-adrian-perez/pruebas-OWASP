<?php

class Appointment extends AppModel
{
    public $useTable = 'appointments';

    public $hasOne = array(
        'Garage',
        'Distributor',
        'AppointmentFeeling',
        'AppointmentStatus',
        'AppointmentType',
        'AppointmentTopic',
        'GarageNetwork',
    );

    public $hasMany = array(
        'AppointmentFile',
        'AppointmentContactList',
        'AppointmentComment',
        'Task'
    );

    public $validate = array(
        'date' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_date',
            ),
            array(
                'rule' => 'date',
                'dmy',
                'message' => 'Validation.Format_date',
                'allowEmpty' => false
            ),
        ),
        'end_date' => array(
            'rule' => 'date',
            'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'appointment_status_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_status',
            ),
        ),
        'visit_contact_name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'description' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'mtd' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'qtd' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'ytd' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'title' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            )
        ),
        'feedback' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'customer_performance_summary' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'user_assigned_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_user_assigned_id',
            )
        ),
        'start_time' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_date',
            )
        ),
        'end_time' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_date',
            )
        ),

    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['date'])) {
            $conditions[] = $this->_conditionDate($fields['date']);
        }
        if (!empty($fields['from'])) {
            $conditions[] = $this->_conditionDateFrom($fields['from']);
        }
        if (!empty($fields['to'])) {
            $conditions[] = $this->_conditionDateTo($fields['to']);
        }
        if (!empty($fields['date_year'])) {
            $conditions[] = $this->_conditionYear($fields['date_year']);
        }
        if (!empty($fields['date_month'])) {
            $conditions[] = $this->_conditionMonth($fields['date_month']);
        }
        if (!empty($fields['appointment_status_id'])) {
            $conditions[] = $this->_conditionStatus($fields['appointment_status_id']);
        }
        if (!empty($fields['appointment_feeling_id'])) {
            $conditions[] = $this->_conditionFeeling($fields['appointment_feeling_id']);
        }
        if (isset($fields['requires_follow_up']) && $fields['requires_follow_up'] != '') {
            $tmp = $this->_conditionFollowUp($fields['requires_follow_up']);
            if (is_array($tmp)) {
                $conditions[] = $tmp;
            }
        }
        if (isset($fields['feedback_fill_up']) && $fields['feedback_fill_up'] != '') {
            $tmp = $this->_conditionFeedbackFillUp($fields['feedback_fill_up']);
            if (is_array($tmp)) {
                $conditions[] = $tmp;
            }
        }
        if (!empty($fields['appointment_type_id'])) {
            $conditions[] = $this->_conditionType($fields['appointment_type_id']);
        }
        if (!empty($fields['name_customer'])) {
            $conditions[] = $this->_conditionNameCustomer($fields['name_customer']);
        }
        if (!empty($fields['topic_id'])) {
            $conditions[] = $this->_conditionTopic($fields['topic_id']);
        }
        if (!empty($fields['user_assigned_id'])) {
            $conditions[] = $this->_conditionUser($fields['user_assigned_id']);
        }

        return $conditions;
    }

    private function _conditionUser($user_assigned_id)
    {
        return array('Appointment.user_assigned_id' => $user_assigned_id);
    }
    private function _conditionTopic($topic_id)
    {
        $appointments = $this->AppointmentTopic->findAllByTopicId($topic_id);
        return array('Appointment.id' => Hash::extract($appointments, '{n}.AppointmentTopic.appointment_id'));
    }
    private function _conditionDate($date)
    {
        return array('Appointment.date' => $date);
    }
    private function _conditionDateFrom($from_date)
    {
        $from_date = Fecha::toFormatoBd($from_date);
        return array('Appointment.date >=' => $from_date);
    }

    private function _conditionDateTo($to_date)
    {
        $to_date = Fecha::toFormatoBd($to_date);
        return array('Appointment.date <=' => $to_date);
    }
    private function _conditionYear($date_year)
    {
        return array('YEAR(Appointment.date)' => $date_year);
    }
    private function _conditionMonth($month)
    {
        return array('MONTH(Appointment.date)' => $month);
    }
    private function _conditionStatus($status_id)
    {
        return array('Appointment.appointment_status_id' => $status_id);
    }
    private function _conditionFeeling($feeling_id)
    {
        return array('Appointment.appointment_feeling_id' => $feeling_id);
    }
    private function _conditionType($type_id)
    {
        return array('Appointment.appointment_type_id' => $type_id);
    }
    private function _conditionFollowUp($follow_up)
    {
        if ($follow_up == ConstantsBooleans::YES) {
            return array('Appointment.requires_follow_up' => ConstantsBooleans::YES);
        }
        return null;
    }
    private function _conditionFeedbackFillUp($feedback_fill_up)
    {
        if ($feedback_fill_up == ConstantsBooleans::NO) {
            return array(
                'OR' => array(
                    array('Appointment.feedback' => ''),
                    array('Appointment.feedback' => null)
                ),
            );
        } else if ($feedback_fill_up == ConstantsBooleans::YES) {
            return array(
                'Appointment.feedback !=' => '',
            );
        }
    }
    private function _conditionNameCustomer($name_customer)
    {
        return array(
            'OR' => array(
                'Garage.name LIKE' => '%' . $name_customer . '%',
                'Distributor.name LIKE' => '%' . $name_customer . '%'
            ),
        );
    }
    public function _query($index)
    {
        return $this->_queries[$index];
    }

    private $_queries = array(
        'search' => array(
            'joins' => array(
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.id = Appointment.garage_id',
                    ),
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Distributor.id = Appointment.distributor_id',
                    ),
                ),
            ),
            'fields' => array(
                'Appointment.*',
                'Garage.*',
                'Distributor.*',
            ),
            'order' => array(
                'Appointment.date' => 'desc',
                'Appointment.start_time' => 'desc'
            )
        ),
        'search_agenda_list' => array(
            'joins' => array(
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.id = Appointment.garage_id',
                    ),
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Distributor.id = Appointment.distributor_id',
                    ),
                ),
            ),
            'fields' => array(
                'Appointment.id',
            ),
            'order' => 'Appointment.date asc, Appointment.start_time asc'
        ),
    );

    public function add_appointment($appointment, $user, $validation = null, $appointment_save_and_send = null)
    {
        $fields = array(
            'Appointment' => array(
                'garage_id',
                'distributor_id',
                'appointment_feeling_id',
                'appointment_status_id',
                'appointment_type_id',
                'visit_contact_id',
                'visit_contact_name',
                'date',
                'end_date',
                'start_time',
                'end_time',
                'description',
                'mtd',
                'qtd',
                'ytd',
                'feedback',
                'customer_performance_summary',
                'requires_follow_up',
                'user_assigned_id',
                'user_creation_id',
                'creation_date',
                'feedback_user_modification',
                'feedback_date_creation',
            )
        );

        // Title field is required when saving an event, but it isn't when saving a visit.
        if (isset($appointment['Appointment']['appointment_status_id']) && $appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) {
            $fields['Appointment'][] = 'title';
        }

        if (isset($appointment['Appointment']['appointment_status_id']) && $appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::EVENT && isset($appointment['Appointment']['visit_contact_id']) && !is_numeric($appointment['Appointment']['visit_contact_id'])) {
            $appointment['Appointment']['visit_contact_name'] = $appointment['Appointment']['visit_contact_id'];
            $appointment['Appointment']['visit_contact_id'] = null;
        } else {
            $appointment['Appointment']['visit_contact_name'] = null;
        }
        if (!isset($appointment['Appointment']['appointment_feeling_id'])) {
            $appointment['Appointment']['appointment_feeling_id'] = null;
        }

        if (!empty($appointment['Appointment']['feedback'])) {
            $appointment['Appointment']['feedback_date_creation'] = date('Y-m-d H:i:s');
            $appointment['Appointment']['feedback_user_modification'] = $user['id'];
        }

        if (!empty($appointment['Appointment']['feedback']) && $appointment_save_and_send) {
            $config = CakeSession::read('Auth.User.Config');
            if ($config[ConstantsConfig::PENDING_VISIT] && $appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::EVENT) {
                $appointment['Appointment']['appointment_status_id']  = ConstantsStatusAppointments::ACCOMPLISHED;
            }
        } else {
            $appointment['Appointment']['feedback_date_creation'] = null;
            $appointment['Appointment']['feedback_user_modification'] = null;
        }
        $appointment['Appointment']['creation_date'] = date('Y-m-d H:i:s');
        $appointment['Appointment']['user_creation_id'] = $user['id'];
        $appointment['Appointment']['date'] = Fecha::toFormatoBd($appointment['Appointment']['date']);
        if (isset($appointment['Appointment']['end_date']) && !empty($appointment['Appointment']['end_date'])) {
            $appointment['Appointment']['end_date'] = Fecha::toFormatoBd($appointment['Appointment']['end_date']);
        } else {
            $appointment['Appointment']['end_date'] = $appointment['Appointment']['date'];
        }

        $interval = strtotime(date('Y-m-d')) - strtotime($appointment['Appointment']['date']);
        if (
            $appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::PLANNED &&
            empty($appointment['Appointment']['feedback']) &&
            $interval > 0
        ) {

            $appointment['Appointment']['appointment_status_id']  = ConstantsStatusAppointments::PENDING;
        }

        $this->create();


        if (!is_null($validation)) {
            $appointment_bd = $this->guardar($appointment, $fields);
        } else {
            $appointment_bd = $this->guardar($appointment, $fields, false);
        }

        if (!$appointment_bd) {
            return false;
        }

        $this->commit();

        return $appointment_bd;
    }

    public function add_appointment_next($appointment)
    {
        $fields = array(
            'Appointment' => array(
                'garage_id',
                'distributor_id',
                'appointment_feeling_id',
                'appointment_status_id',
                'appointment_type_id',
                'visit_contact_id',
                'visit_contact_name',
                'date',
                'reminder',
                'end_date',
                'start_time',
                'end_time',
                'description',
                'mtd',
                'qtd',
                'ytd',
                'feedback',
                'requires_follow_up',
                'user_assigned_id',
                'user_creation_id',
                'creation_date',
                'feedback_user_modification',
                'feedback_date_creation',
            )
        );

        // Title field is required when saving an event, but it isn't when saving a visit.
        if (isset($appointment['Appointment']['appointment_status_id']) && $appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) {
            $fields['Appointment'][] = 'title';
        }

        $appointment_tmp['Appointment']['garage_id'] = $appointment['garage_id'];
        $appointment_tmp['Appointment']['distributor_id'] = $appointment['distributor_id'];
        $appointment_tmp['Appointment']['appointment_type_id'] = ConstantsTypesAppointments::VISIT;
        $appointment_tmp['Appointment']['visit_contact_id'] = null;
        $appointment_tmp['Appointment']['visit_contact_name'] = null;
        $appointment_tmp['Appointment']['appointment_feeling_id'] = null;
        $appointment_tmp['Appointment']['feedback_date_creation'] = null;
        $appointment_tmp['Appointment']['feedback_user_modification'] = null;
        $appointment_tmp['Appointment']['creation_date'] = date('Y-m-d H:i:s');
        $appointment_tmp['Appointment']['reminder'] = $appointment['reminder'] ? ConstantsBooleans::YES : ConstantsBooleans::NO;
        $appointment_tmp['Appointment']['user_assigned_id'] = $appointment['user_assigned_id'];
        $appointment_tmp['Appointment']['user_creation_id'] = $appointment['user_creation_id'];
        $appointment_tmp['Appointment']['date'] = Fecha::toFormatoBd($appointment['appointment_date']);
        $appointment_tmp['Appointment']['end_date'] = Fecha::toFormatoBd($appointment['appointment_date']);

        $appointment_tmp['Appointment']['start_time'] = $appointment['start_time'];
        $appointment_tmp['Appointment']['end_time'] = $appointment['end_time'];

        if (
            strtotime($appointment['appointment_date']) < strtotime(date('Y-m-d'))
        ) {
            $appointment_tmp['Appointment']['appointment_status_id']  = ConstantsStatusAppointments::PENDING;
        } else {
            $appointment_tmp['Appointment']['appointment_status_id']  = ConstantsStatusAppointments::PLANNED;
        }

        $this->create();
        $appointment_bd = $this->guardar($appointment_tmp, $fields, false);

        if (!$appointment_bd) {
            return false;
        }

        return $appointment_bd['Appointment']['id'];
    }

    public function edit_appointment($appointment, $user, $feedback = null, $validation = true, $appointment_save_and_send = null)
    {
        $fields = array(
            'Appointment' => array(
                'id',
                'garage_id',
                'distributor_id',
                'appointment_feeling_id',
                'appointment_status_id',
                'appointment_type_id',
                'visit_contact_id',
                'visit_contact_name',
                'date',
                'end_date',
                'start_time',
                'end_time',
                'description',
                'mtd',
                'qtd',
                'ytd',
                'feedback',
                'customer_performance_summary',
                'requires_follow_up',
                'user_assigned_id',
                'user_creation_id',
                'creation_date',
                'feedback_user_modification',
                'feedback_date_creation',
            )
        );

        // Title field is required when saving an event, but it isn't when saving a visit.
        if (isset($appointment['Appointment']['appointment_status_id']) && $appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) {
            $fields['Appointment'][] = 'title';
        }

        if (isset($appointment['Appointment']['appointment_status_id']) && $appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::EVENT && isset($appointment['Appointment']['visit_contact_id']) && !is_numeric($appointment['Appointment']['visit_contact_id'])) {
            $appointment['Appointment']['visit_contact_name'] = $appointment['Appointment']['visit_contact_id'];
            $appointment['Appointment']['visit_contact_id'] = null;
        } else {
            $appointment['Appointment']['visit_contact_name'] = null;
        }

        if (!empty($feedback)) {
            $appointment['Appointment']['feedback_date_creation'] = date('Y-m-d H:i:s');
            $appointment['Appointment']['feedback_user_modification'] = $user['id'];
        }

        if (!empty($feedback) && $appointment_save_and_send) {
            $config = CakeSession::read('Auth.User.Config');
            if ($config[ConstantsConfig::PENDING_VISIT] && $appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::EVENT) {
                $appointment['Appointment']['appointment_status_id']  = ConstantsStatusAppointments::ACCOMPLISHED;
            }
        }

        if (isset($appointment['Appointment']['date'])) {
            $appointment['Appointment']['date'] = Fecha::toFormatoBd($appointment['Appointment']['date']);
        }
        if (isset($appointment['Appointment']['end_date']) && !empty($appointment['Appointment']['end_date'])) {
            $appointment['Appointment']['end_date'] = Fecha::toFormatoBd($appointment['Appointment']['end_date']);
        } else if ($validation == true) {
            $appointment['Appointment']['end_date'] = isset($appointment['Appointment']['date']) ? $appointment['Appointment']['date'] : null;
        }

        if ($validation) {
            $appointment_bd = $this->guardar($appointment, $fields);
        } else {
            $appointment_bd = $this->guardar($appointment, $fields, $validation);
        }
        if (!$appointment_bd) {
            return false;
        }

        return $appointment_bd;
    }

    public function delete_appointment($id)
    {
        if ($this->eliminar($id)) {
            return true;
        }
        return false;
    }

    public function getCalendarEvents($start, $end, $user)
    {
        $user_id = $user['id'];
        $user_role_id = $user['role_id'];

        $conditions = array();
        if ($user_role_id != ConstantsRoles::SUPER_ADMIN && $user_role_id != ConstantsRoles::ADMIN) {
            $conditions = array(
                'Appointment.user_assigned_id' => $user_id,
                'Appointment.user_creation_id' => $user_id,
            );
        }

        $appointments = $this->find('all', array(
            'conditions' => array(
                'date >=' => $start,
                'date <' => $end,
                $conditions
            ),
            'fields' => array(
                'Appointment.id',
                'Appointment.appointment_status_id',
                'Appointment.garage_id',
                'Appointment.distributor_id',
                'Appointment.end_date',
                'Appointment.end_time',
                'Appointment.date',
                'Appointment.start_time',
                'Appointment.user_assigned_id',
                'Appointment.title',
            )
        ));

        $appointments_js = array();
        foreach ($appointments as $key => $appointment) {
            if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::PLANNED) {
                $appointments_js[$key]['borderColor'] = ConstantsStatusColorAppointments::PLANNED;
            } else if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::ACCOMPLISHED) {
                $appointments_js[$key]['borderColor'] = ConstantsStatusColorAppointments::ACCOMPLISHED;
            } else if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::CANCELED) {
                $appointments_js[$key]['borderColor'] = ConstantsStatusColorAppointments::CANCELED;
            } else if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::RESCHEDULED) {
                $appointments_js[$key]['borderColor'] = ConstantsStatusColorAppointments::RESCHEDULED;
            } else if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::PENDING) {
                $appointments_js[$key]['borderColor'] = ConstantsStatusColorAppointments::PENDING;
            } else if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointmentsDe::RUNNING) {
                $appointments_js[$key]['borderColor'] = ConstantsStatusColorAppointments::RUNNING;
            }

            $appointments_js[$key]['eventTextColor'] = 'black';
            $appointments_js[$key]['color'] = 'white';
            if (isset($appointment['Appointment']['garage_id'])) {
                $this->Garage = ClassRegistry::init('Garage');
                $garage = $this->Garage->findById($appointment['Appointment']['garage_id']);
                $appointments_js[$key]['garage_name'] = $garage['Garage']['name'];
                $appointments_js[$key]['url_b'] = '/clients/report/' . $appointment['Appointment']['garage_id'];
                $appointments_js[$key]['img'] = 'icon-garages';
            } else if (isset($appointment['Appointment']['distributor_id'])) {
                $this->Distributor = ClassRegistry::init('Distributor');
                $distributor = $this->Distributor->findById($appointment['Appointment']['distributor_id']);
                $appointments_js[$key]['garage_name'] = $distributor['Distributor']['name'];
                $appointments_js[$key]['url_b'] = '/clients/report_distributor/' . $appointment['Appointment']['distributor_id'];
                $appointments_js[$key]['img'] = 'icon-distributors';
            }
            $appointments_js[$key]['textColor'] = '#9ea4b2';
            $appointments_js[$key]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
            $appointments_js[$key]['start'] = $appointment['Appointment']['date'] . " " . $appointment['Appointment']['start_time'];
            $appointments_js[$key]['status'] = $appointment['Appointment']['appointment_status_id'];
            $appointments_js[$key]['url_a'] = '/appointments/edit/' . $appointment['Appointment']['id'];
            $appointments_js[$key]['user_assigned_id'] =  $appointment['Appointment']['user_assigned_id'];
            $appointments_js[$key]['appointment_id'] =  $appointment['Appointment']['id'];
            if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) {
                $this->AppointmentType = ClassRegistry::init('AppointmentType');
                //$appointments_types = $this->AppointmentType->search_list_event();
                $appointments_js[$key]['borderColor'] = ConstantsStatusColorAppointments::EVENT;
                if (isset($appointments_js[$key]['end'])) {
                    $appointments_js[$key]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                }
                $appointments_js[$key]['garage_name'] = $appointment['Appointment']['title'];
                $appointments_js[$key]['url_a'] = '/appointments/edit_event/' . $appointment['Appointment']['id'];
                $appointments_js[$key]['is_event'] = ConstantsBooleans::ACTIVE;
            }
        }

        return $appointments_js;
    }

    public function getHomeCalendarEvents($start, $end, $user_assigned_id)
    {
        $appointments = $this->find('all', array(
            'conditions' => array(
                'date >=' => $start,
                'date <' => $end,
                'user_assigned_id' => $user_assigned_id
            ),
            'order' => 'start_time asc'
        ));

        $appointments_js = array();
        foreach ($appointments as $key => $appointment) {
            if ($appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::EVENT) {
                $appointments_js[$appointment['Appointment']['date']]['event_type'] = '1';
                $appointments_js[$appointment['Appointment']['date']]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                $appointments_js[$appointment['Appointment']['date']]['start'] = $appointment['Appointment']['date'] . " " . $appointment['Appointment']['start_time'];
                $appointments_js[$appointment['Appointment']['date']]['eventTextColor'] = 'black';
                $appointments_js[$appointment['Appointment']['date']]['borderColor'] = 'rgb(71,134,255)';
                $appointments_js[$appointment['Appointment']['date']]['color'] = 'rgb(245, 245, 245)';
                if (isset($appointment['Appointment']['garage_id'])) {
                    $this->Garage = ClassRegistry::init('Garage');
                    $garage = $this->Garage->findById($appointment['Appointment']['garage_id']);
                    $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['garage_name'] = $garage['Garage']['name'];
                } else if (isset($appointment['Appointment']['distributor_id'])) {
                    $this->Distributor = ClassRegistry::init('Distributor');
                    $distributor = $this->Distributor->findById($appointment['Appointment']['distributor_id']);
                    $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['garage_name'] = $distributor['Distributor']['name'];
                }
                $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['start'] = $appointment['Appointment']['date'] . " " . $appointment['Appointment']['start_time'];
                $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['status'] = $appointment['Appointment']['appointment_status_id'];
                $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['appointment_id'] =  $appointment['Appointment']['id'];
            }
        }

        $events_js = array();
        foreach ($appointments as $key => $appointment) {
            if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) {
                $events_js[$appointment['Appointment']['date']]['event_type'] = '2';
                $events_js[$appointment['Appointment']['date']]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                $events_js[$appointment['Appointment']['date']]['start'] = $appointment['Appointment']['date'] . " " . $appointment['Appointment']['start_time'];
                $events_js[$appointment['Appointment']['date']]['eventTextColor'] = 'black';
                $events_js[$appointment['Appointment']['date']]['color'] = 'rgb(245, 245, 245)';
                $events_js[$appointment['Appointment']['date']]['borderColor'] = 'rgb(132,193,91)';
                $this->AppointmentType = ClassRegistry::init('AppointmentType');
                //$appointments_types = $this->AppointmentType->search_list_event();
                $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['borderColor'] = ConstantsStatusColorAppointments::EVENT;
                if (isset($events_js[$appointment['Appointment']['date']]['end'])) {
                    $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                }
                $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['start'] = $appointment['Appointment']['date'] . " " . $appointment['Appointment']['start_time'];
                $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['garage_name'] = $appointment['Appointment']['title'];
                $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['appointment_id'] =  $appointment['Appointment']['id'];
            }
        }
        $appointments_js = array_values($appointments_js);
        $events_js = array_values($events_js);
        $events = array_merge($appointments_js, $events_js);

        return $events;
    }

    public function getHomeCalendarEventsGarage($start, $end, $garage_id)
    {
        $appointments = $this->find('all', array(
            'conditions' => array(
                'date >=' => $start,
                'date <' => $end,
                'garage_id' => $garage_id
            ),
            'order' => 'start_time asc'
        ));

        $appointments_js = array();
        foreach ($appointments as $key => $appointment) {
            if ($appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::EVENT) {
                $appointments_js[$appointment['Appointment']['date']]['event_type'] = '1';
                $appointments_js[$appointment['Appointment']['date']]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                $appointments_js[$appointment['Appointment']['date']]['start'] = $appointment['Appointment']['date'] . " " . $appointment['Appointment']['start_time'];
                $appointments_js[$appointment['Appointment']['date']]['eventTextColor'] = 'black';
                $appointments_js[$appointment['Appointment']['date']]['borderColor'] = 'rgb(71,134,255)';
                $appointments_js[$appointment['Appointment']['date']]['color'] = 'rgb(245, 245, 245)';
                if (isset($appointment['Appointment']['garage_id'])) {
                    $this->Garage = ClassRegistry::init('Garage');
                    $garage = $this->Garage->findById($appointment['Appointment']['garage_id']);
                    $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['garage_name'] = $garage['Garage']['name'];
                } else if (isset($appointment['Appointment']['distributor_id'])) {
                    $this->Distributor = ClassRegistry::init('Distributor');
                    $distributor = $this->Distributor->findById($appointment['Appointment']['distributor_id']);
                    $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['garage_name'] = $distributor['Distributor']['name'];
                }
                $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['start'] = $appointment['Appointment']['date'] . " " . $appointment['Appointment']['start_time'];
                $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['status'] = $appointment['Appointment']['appointment_status_id'];
                $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['appointment_id'] =  $appointment['Appointment']['id'];
            }
        }

        $events_js = array();
        foreach ($appointments as $key => $appointment) {
            if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) {
                $events_js[$appointment['Appointment']['date']]['event_type'] = '2';
                $events_js[$appointment['Appointment']['date']]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                $events_js[$appointment['Appointment']['date']]['start'] = $appointment['Appointment']['date'] . " " . $appointment['Appointment']['start_time'];
                $events_js[$appointment['Appointment']['date']]['eventTextColor'] = 'black';
                $events_js[$appointment['Appointment']['date']]['color'] = 'rgb(245, 245, 245)';
                $events_js[$appointment['Appointment']['date']]['borderColor'] = 'rgb(132,193,91)';
                $this->AppointmentType = ClassRegistry::init('AppointmentType');
                //$appointments_types = $this->AppointmentType->search_list_event();
                $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['borderColor'] = ConstantsStatusColorAppointments::EVENT;
                if (isset($events_js[$appointment['Appointment']['date']]['end'])) {
                    $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                }
                $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['garage_name'] = $appointment['Appointment']['title'];
                $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['appointment_id'] =  $appointment['Appointment']['id'];
            }
        }
        $appointments_js = array_values($appointments_js);
        $events_js = array_values($events_js);
        $events = array_merge($appointments_js, $events_js);

        return $events;
    }
    public function getHomeCalendarEventsDistributor($start, $end, $distributor_id)
    {
        $appointments = $this->find('all', array(
            'conditions' => array(
                'date >=' => $start,
                'date <' => $end,
                'distributor_id' => $distributor_id
            ),
            'order' => 'start_time asc'
        ));

        $appointments_js = array();
        foreach ($appointments as $key => $appointment) {
            if ($appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::EVENT) {
                $appointments_js[$appointment['Appointment']['date']]['event_type'] = '1';
                $appointments_js[$appointment['Appointment']['date']]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                $appointments_js[$appointment['Appointment']['date']]['start'] = $appointment['Appointment']['date'] . " " . $appointment['Appointment']['start_time'];
                $appointments_js[$appointment['Appointment']['date']]['eventTextColor'] = 'black';
                $appointments_js[$appointment['Appointment']['date']]['borderColor'] = 'rgb(71,134,255)';
                $appointments_js[$appointment['Appointment']['date']]['color'] = 'rgb(245, 245, 245)';
                if (isset($appointment['Appointment']['garage_id'])) {
                    $this->Garage = ClassRegistry::init('Garage');
                    $garage = $this->Garage->findById($appointment['Appointment']['garage_id']);
                    $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['garage_name'] = $garage['Garage']['name'];
                } else if (isset($appointment['Appointment']['distributor_id'])) {
                    $this->Distributor = ClassRegistry::init('Distributor');
                    $distributor = $this->Distributor->findById($appointment['Appointment']['distributor_id']);
                    $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['garage_name'] = $distributor['Distributor']['name'];
                }
                $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['start'] = $appointment['Appointment']['date'] . " " . $appointment['Appointment']['start_time'];
                $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['status'] = $appointment['Appointment']['appointment_status_id'];
                $appointments_js[$appointment['Appointment']['date']]['CustomerAppointment'][$key]['appointment_id'] =  $appointment['Appointment']['id'];
            }
        }

        $events_js = array();
        foreach ($appointments as $key => $appointment) {
            if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) {
                $events_js[$appointment['Appointment']['date']]['event_type'] = '2';
                $events_js[$appointment['Appointment']['date']]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                $events_js[$appointment['Appointment']['date']]['start'] = $appointment['Appointment']['date'] . " " . $appointment['Appointment']['start_time'];
                $events_js[$appointment['Appointment']['date']]['eventTextColor'] = 'black';
                $events_js[$appointment['Appointment']['date']]['color'] = 'rgb(245, 245, 245)';
                $events_js[$appointment['Appointment']['date']]['borderColor'] = 'rgb(132,193,91)';
                $this->AppointmentType = ClassRegistry::init('AppointmentType');
                //$appointments_types = $this->AppointmentType->search_list_event();
                $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['borderColor'] = ConstantsStatusColorAppointments::EVENT;
                if (isset($events_js[$appointment['Appointment']['date']]['end'])) {
                    $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['end'] = $appointment['Appointment']['end_date'] . " " . $appointment['Appointment']['end_time'];
                }
                $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['garage_name'] = $appointment['Appointment']['title'];
                $events_js[$appointment['Appointment']['date']]['CustomerEvent'][$key]['appointment_id'] =  $appointment['Appointment']['id'];
            }
        }
        $appointments_js = array_values($appointments_js);
        $events_js = array_values($events_js);
        $events = array_merge($appointments_js, $events_js);

        return $events;
    }

    public function getNextAppointmentByGarage($garage_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'Appointment.garage_id' => $garage_id,
                    'Appointment.date >=' => date('Y-m-d'),
                ),
                'order' => array(
                    'Appointment.date'
                ),
                'fields' => array(
                    'Appointment.*'
                ),
            )
        );
    }

    public function getNextAppointmentByDistributor($distributor_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'Appointment.distributor_id' => $distributor_id,
                    'Appointment.date >=' => date('Y-m-d'),
                ),
                'order' => array(
                    'Appointment.date'
                ),
                'fields' => array(
                    'Appointment.*'
                ),
            )
        );
    }

    public function getPreviousByGarage($garage_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'Appointment.garage_id' => $garage_id,
                    'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
                    'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT,
                    'Appointment.date <' => date('Y-m-d'),
                ),
                'order' => array(
                    'Appointment.date DESC'
                ),
                'fields' => array(
                    'Appointment.*'
                ),
            )
        );
    }

    public function getPreviousByDistributor($distributor_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'Appointment.distributor_id' => $distributor_id,
                    'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
                    'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT,
                    'Appointment.date <' => date('Y-m-d'),
                ),
                'order' => array(
                    'Appointment.date DESC'
                ),
                'fields' => array(
                    'Appointment.*'
                ),
            )
        );
    }

    public function getNextVisits($user_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'LEFT',
                        'conditions' => 'Appointment.garage_id = Garage.id'
                    ),
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'LEFT',
                        'conditions' => 'Appointment.distributor_id = Distributor.id'
                    ),
                ),
                'conditions' => array(
                    'Appointment.user_assigned_id' => $user_id,
                    'Appointment.date >=' => date('Y-m-d'),
                    'Appointment.appointment_status_id' => array(ConstantsStatusAppointments::PLANNED, ConstantsStatusAppointments::EVENT)
                ),
                'order' => array(
                    'Appointment.date',
                    'Appointment.start_time'
                ),
                'fields' => array(
                    'Appointment.id',
                    'Appointment.requires_follow_up',
                    'Appointment.date',
                    'Appointment.visit_contact_id',
                    'Appointment.visit_contact_name',
                    'Appointment.garage_id',
                    'Appointment.distributor_id',
                    'Appointment.title',
                    'Appointment.appointment_status_id',
                    'Appointment.start_time',
                    'Distributor.name',
                    'Garage.name'
                ),
                'limit' => ConstantsLimitDashboard::APPOINTMENT
            )
        );
    }

    public function getNextVisitsRequires($user_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'LEFT',
                        'conditions' => 'Appointment.garage_id = Garage.id'
                    ),
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'LEFT',
                        'conditions' => 'Appointment.distributor_id = Distributor.id'
                    ),
                ),
                'conditions' => array(
                    'Appointment.user_assigned_id' => $user_id,
                    'Appointment.requires_follow_up' => ConstantsBooleans::YES
                ),
                'order' => array(
                    'Appointment.date',
                    'Appointment.start_time'
                ),
                'fields' => array(
                    'Appointment.id',
                    'Appointment.requires_follow_up',
                    'Appointment.date',
                    'Appointment.visit_contact_id',
                    'Appointment.visit_contact_name',
                    'Appointment.garage_id',
                    'Appointment.distributor_id',
                    'Appointment.appointment_status_id',
                    'Appointment.title',
                    'Distributor.name',
                    'Garage.name'
                ),
                'limit' => ConstantsLimitDashboard::APPOINTMENT
            )
        );
    }

    public function getVisitsWithoutFeedback($user_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'LEFT',
                        'conditions' => 'Appointment.garage_id = Garage.id'
                    ),
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'LEFT',
                        'conditions' => 'Appointment.distributor_id = Distributor.id'
                    ),
                ),
                'conditions' => array(
                    'Appointment.user_assigned_id' => $user_id,
                    'Appointment.appointment_status_id != ' => ConstantsStatusAppointments::EVENT,
                    'OR' => array(
                        array('Appointment.feedback' => ''),
                        array('Appointment.feedback' => null)
                    )
                ),
                'order' => array(
                    'Appointment.date',
                    'Appointment.start_time'
                ),
                'fields' => array(
                    'Appointment.*',
                    'Distributor.*',
                    'Garage.*'
                ),
                'limit' => ConstantsLimitDashboard::APPOINTMENT
            )
        );
    }

    public function getVisitsWithoutFeedbackAndStatusPending($user_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'LEFT',
                        'conditions' => 'Appointment.garage_id = Garage.id'
                    ),
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'LEFT',
                        'conditions' => 'Appointment.distributor_id = Distributor.id'
                    ),
                ),
                'conditions' => array(
                    'Appointment.user_assigned_id' => $user_id,
                    'Appointment.appointment_status_id = ' => ConstantsStatusAppointments::PENDING,
                    'OR' => array(
                        array('Appointment.feedback' => ''),
                        array('Appointment.feedback' => null)
                    )
                ),
                'order' => array(
                    'Appointment.date',
                    'Appointment.start_time'
                ),
                'fields' => array(
                    'Appointment.id',
                    'Appointment.requires_follow_up',
                    'Appointment.date',
                    'Appointment.visit_contact_id',
                    'Appointment.visit_contact_name',
                    'Appointment.garage_id',
                    'Appointment.distributor_id',
                    'Distributor.name',
                    'Garage.name'
                ),
                'limit' => ConstantsLimitDashboard::APPOINTMENT
            )
        );
    }

    public function getLastVisits()
    {
        return $this->find('all', array(
            'conditions' => array(
                'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
                'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT,
                'Appointment.garage_id IS NOT NULL'
            ),
            'fields' => array(
                'garage_id',
                'distributor_id',
                'date',
            ),
            'order' => array(
                'Appointment.date'
            )
        ));
    }

    public function getGarageVisitByUser($user_id, $date_from, $date_to)
    {
        return $this->find('all', array(
            'conditions' => array(
                'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
                'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT,
                'Appointment.garage_id IS NOT NULL',
                'Appointment.user_assigned_id' => $user_id,
                'Appointment.date >=' => $date_from,
                'Appointment.date <=' => $date_to,
            ),
            'fields' => array(
                'id',
                'garage_id',
                'date',
                'end_date',
                'start_time',
                'end_time',
            ),
            'order' => array(
                'Appointment.date'
            )
        ));
    }

    public function getDistributorVisitByUser($user_id, $date_from, $date_to)
    {
        return $this->find('all', array(
            'conditions' => array(
                'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
                'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT,
                'Appointment.distributor_id IS NOT NULL',
                'Appointment.user_assigned_id' => $user_id,
                'Appointment.date >=' => $date_from,
                'Appointment.date <=' => $date_to,
            ),
            'fields' => array(
                'id',
                'distributor_id',
                'date',
                'end_date',
                'start_time',
                'end_time',
            ),
            'order' => array(
                'Appointment.date'
            )
        ));
    }

    public function getEventsVisitByUser($user_id, $date_from, $date_to)
    {
        return $this->find('all', array(
            'conditions' => array(
                'Appointment.appointment_status_id' => ConstantsStatusAppointments::EVENT,
                'Appointment.appointment_type_id' => ConstantsTypesAppointments::PROSPECT_GARAGE_VISIT,
                'Appointment.user_assigned_id' => $user_id,
                'Appointment.date >=' => $date_from,
                'Appointment.date <=' => $date_to,
            ),
            'fields' => array(
                'id',
                'date',
                'end_date',
                'start_time',
                'end_time',
            ),
            'order' => array(
                'Appointment.date'
            )
        ));
    }

    public function getLastVisitsByDistributor()
    {
        return $this->find('all', array(
            'conditions' => array(
                'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
                'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT,
                'Appointment.distributor_id IS NOT NULL'
            ),
            'fields' => array(
                'garage_id',
                'distributor_id',
                'date',
            ),
            'order' => array(
                'Appointment.date'
            )
        ));
    }

    public function countAppointmentsByAssignedAndStatus($user_assigned_id, $appointment_status)
    {
        return $this->find('count', array(
            'conditions' => array(
                'Appointment.appointment_status_id' => $appointment_status,
                'Appointment.user_assigned_id' => $user_assigned_id,
                'or' => array(
                    'Appointment.distributor_id IS NOT NULL',
                    'Appointment.garage_id IS NOT NULL',

                )
            ),
            'fields' => array(
                'Appointment.*',
            ),
        ));
    }

    public function countAppointmentsByGarageUserAndStatus($garage_id, $user_assigned_id, $appointment_status)
    {
        return $this->find('count', array(
            'conditions' => array(
                'Appointment.appointment_status_id' => $appointment_status,
                'or' => array(
                    'Appointment.garage_id' => $garage_id,
                    'Appointment.user_assigned_id' => $user_assigned_id,
                )
            ),
            'fields' => array(
                'Appointment.*',
            ),
        ));
    }
    public function countAppointmentsByDistributorUserAndStatus($distributor_id, $user_assigned_id, $appointment_status)
    {
        return $this->find('count', array(
            'conditions' => array(
                'Appointment.appointment_status_id' => $appointment_status,
                'or' => array(
                    'Appointment.distributor_id' => $distributor_id,
                    'Appointment.user_assigned_id' => $user_assigned_id,
                )
            ),
            'fields' => array(
                'Appointment.*',
            ),
        ));
    }

    public function getAppointmentWithCustomerByUserAssignedIdAndStatus($user_assigned_id, $status_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'Appointment.appointment_status_id' => $status_id,
                'Appointment.user_assigned_id' => $user_assigned_id,
            ),
            'fields' => array(
                'Appointment.*',
                'Garage.name',
                'Distributor.name',
            ),
        ));
    }

    public function getAllAppointmentByGarageLimitDateToday($garage_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'Appointment.user_Assigned_id = User.id'
                ),
            ),
            'conditions' => array(
                'Appointment.garage_id' => $garage_id,
                'Appointment.date >=' => date('Y-m-d'),
            ),
            'order' => array(
                'Appointment.date',
                'Appointment.start_time',
            ),
            'fields' => array(
                'Appointment.*',
                'User.name',
                'User.surname',
            ),
            'limit' => ConstantsLimitDashboard::APPOINTMENT
        ));
    }

    public function getAllAppointmentByDistributorLimitDateToday($distributor_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'Appointment.user_Assigned_id = User.id'
                ),
            ),
            'conditions' => array(
                'Appointment.distributor_id' => $distributor_id,
                'Appointment.date >=' => date('Y-m-d'),
            ),
            'order' => array(
                'Appointment.date',
                'Appointment.start_time',
            ),
            'fields' => array(
                'Appointment.*',
                'User.name',
                'User.surname',
            ),
            'limit' => ConstantsLimitDashboard::APPOINTMENT
        ));
    }

    public function getAllAppointmentByDistributorLastYear($distributor_id)
    {
        return $this->find('count', array(
            'conditions' => array(
                'Appointment.distributor_id' => $distributor_id,
                'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
                'Appointment.date <' => date('Y-m-d'),
                'Appointment.date >' => date('Y-m-d', strtotime(date('Y-m-d') . " -1 year")),
            ),
            'order' => array(
                'Appointment.date',
                'Appointment.start_time',
            ),
            'fields' => array(
                'Appointment.*',
            ),
        ));
    }

    public function getAppointmentDataByAppointmentId($appointment_id)
    {
        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.id = Appointment.garage_id',
                    ),
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Distributor.id = Appointment.distributor_id',
                    ),
                ),
            ),
            'conditions' => array(
                'Appointment.id' => $appointment_id
            ),
            'fields' => array(
                'Appointment.id',
                'Appointment.date',
                'Appointment.end_date',
                'Appointment.start_time',
                'Appointment.end_time',
                'Appointment.title',
                'Appointment.feedback',
                'Appointment.requires_follow_up',
                'Appointment.garage_id',
                'Appointment.distributor_id',
                'Appointment.appointment_status_id',
                'Appointment.appointment_feeling_id',
                'Appointment.appointment_type_id',
                'Appointment.user_assigned_id',
                'Garage.name',
                'Distributor.name',
            ),
        ));
    }

    public function getTopAppointmentGarage($date_from, $date_to, $aag_region_id, $limit, $offset)
    {
        return $this->_getAppointmentGarage($date_from, $date_to, $aag_region_id, $limit, $offset); //$users
    }

    private function _getAppointmentGarage($date_from, $date_to, $aag_region_id, $limit, $offset)
    {
        $conditions = array(
            'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
            'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT,
            'Appointment.date >=' => $date_from,
            'Appointment.date <=' => $date_to
        );

        $conditions[] = array('Garage.aag_region_id' => $aag_region_id);

        $garages = $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Garage.id = Appointment.garage_id',
                    ),
                ),
            ),
            'conditions' => $conditions,
            'fields' => array(
                'Appointment.id',
                'Appointment.garage_id',
            ),
        ));

        $conditions = array('aag_region_id' => $aag_region_id);

        $garages_tmp = $this->Garage->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'Garage.id',
                'Garage.name',
                'Garage.last_visit',
                'Garage.town',
                'Garage.address1',
                'Garage.address2',
                'Garage.address3',
                'Garage.address4',
                'Garage.phone',
                'Garage.g_number_id',
                'Garage.ref_code',
            ),
            'limit' => $limit,
            'offset' => $offset,
        ));

        foreach ($garages_tmp as $garage_tmp) {
            $appointments_garage[$garage_tmp['Garage']['id']]['name'] = $garage_tmp['Garage']['name'];
            $appointments_garage[$garage_tmp['Garage']['id']]['count'] = 0;
            $appointments_garage[$garage_tmp['Garage']['id']]['last_visit'] = $garage_tmp['Garage']['last_visit'];
            $appointments_garage[$garage_tmp['Garage']['id']]['town'] = $garage_tmp['Garage']['town'];
            $appointments_garage[$garage_tmp['Garage']['id']]['address'] = $garage_tmp['Garage']['address1'] . ' ' . $garage_tmp['Garage']['address2'] . ' ' . $garage_tmp['Garage']['address3'] . ' ' . $garage_tmp['Garage']['address4'];
            $appointments_garage[$garage_tmp['Garage']['id']]['phone'] = $garage_tmp['Garage']['phone'];
            $appointments_garage[$garage_tmp['Garage']['id']]['g_number_id'] = $garage_tmp['Garage']['g_number_id'];
            $appointments_garage[$garage_tmp['Garage']['id']]['ref_code'] = $garage_tmp['Garage']['ref_code'];
            $appointments_garage[$garage_tmp['Garage']['id']]['bdms'] = isset($garage_tmp[0]) ? explode(",", $garage_tmp[0]['bdms']) : null;
            $appointments_garage[$garage_tmp['Garage']['id']]['contract_start'] = $this->GarageNetwork->getContractStartDateByGarage($garage_tmp['Garage']['id']);
        }

        foreach ($garages as $garage) {
            $appointments_garage[$garage['Appointment']['garage_id']]['count'] = $appointments_garage[$garage['Appointment']['garage_id']]['count'] + 1;
        }

        return $appointments_garage;
    }

    public function getTopAppointmentDistributor($date_from, $date_to, $aag_region_id)
    {
        return $this->_getAppointmentDistributor($date_from, $date_to, $aag_region_id);
    }

    private function _getAppointmentDistributor($date_from, $date_to, $aag_region_id)
    {
        $conditions = array(
            'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
            'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT,
            'Appointment.date >=' => $date_from,
            'Appointment.date <=' => $date_to,
        );

        $distributors = $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = Appointment.distributor_id',
                        'Distributor.aag_region_id' => $aag_region_id
                    ),
                ),
            ),
            'conditions' => $conditions,
            'fields' => array(
                'Appointment.id',
                'Appointment.distributor_id',
            ),
        ));

        $distributors_tmp = $this->Distributor->findByDistributorName($aag_region_id);

        foreach ($distributors_tmp as $distributor_tmp) {
            $appointments_distributor[$distributor_tmp['Distributor']['id']]['name'] = $distributor_tmp['Distributor']['name'];
            $appointments_distributor[$distributor_tmp['Distributor']['id']]['count'] = 0;
            $appointments_distributor[$distributor_tmp['Distributor']['id']]['last_visit'] = $distributor_tmp['Distributor']['last_visit'];
            $appointments_distributor[$distributor_tmp['Distributor']['id']]['account_number'] = $distributor_tmp['Distributor']['account_number'];
            $appointments_distributor[$distributor_tmp['Distributor']['id']]['postcode'] = $distributor_tmp['Distributor']['postcode'];
            $appointments_distributor[$distributor_tmp['Distributor']['id']]['town'] = $distributor_tmp['Distributor']['town'];
            $appointments_distributor[$distributor_tmp['Distributor']['id']]['phone'] = $distributor_tmp['Distributor']['phone'];
            $appointments_distributor[$distributor_tmp['Distributor']['id']]['contract_start'] = $distributor_tmp['Distributor']['start_date'];
            $appointments_distributor[$distributor_tmp['Distributor']['id']]['bdms'] = isset($distributor_tmp[0]) ? explode(",", $distributor_tmp[0]['bdms']) : null;
        }

        foreach ($distributors as $distributor) {
            $appointments_distributor[$distributor['Appointment']['distributor_id']]['count'] = $appointments_distributor[$distributor['Appointment']['distributor_id']]['count'] + 1;
        }

        return $appointments_distributor;
    }


    public function findAppointmentsBdm($query, $conditions)
    {
        $query['conditions'] = $conditions;
        $appointments = $this->find('all', $query);
        return $appointments;
    }


    // Function to Task Scheduled
    public function appointmentsToPending()
    {
        $this->Config = ClassRegistry::init('Config');
        $config = $this->Config->findById(ConstantsConfig::PENDING_VISIT);

        if ($config['Config']['active']) {
            $appointments = $this->findAllByAppointmentStatusId(ConstantsStatusAppointments::PLANNED);

            foreach ($appointments as $appointment) {
                if (strtotime($appointment['Appointment']['date']) < strtotime(date('Y-m-d')) && ($appointment['Appointment']['feedback'] == '')) {
                    $appointment['Appointment']['appointment_status_id'] = ConstantsStatusAppointments::PENDING;
                    $this->save($appointment);
                } else if ((strtotime($appointment['Appointment']['date']) == strtotime(date('Y-m-d')) && $appointment['Appointment']['feedback'] == '') &&
                    strtotime($appointment['Appointment']['start_time']) < strtotime(date('H:i:s'))
                ) {
                    $appointment['Appointment']['appointment_status_id'] = ConstantsStatusAppointments::PENDING;
                    $this->save($appointment);
                }
            }

            $this->commit();
        }
    }

    /**
     * Appointment reminder email.
     * Function to AppointmentShell.
     */
    public function appointmentsEmailReminder()
    {
        $appointments = $this->findAllByReminderAndAppointmentStatusId(ConstantsBooleans::YES, array(ConstantsStatusAppointments::PLANNED, ConstantsStatusAppointments::PENDING, ConstantsStatusAppointments::RESCHEDULED));
        $this->Email = ClassRegistry::init('Email');
        $this->User = ClassRegistry::init('User');
        $this->Contact = ClassRegistry::init('Contact');
        $this->Garage = ClassRegistry::init('Garage');
        $this->Country = ClassRegistry::init('Country');
        $this->Distributor = ClassRegistry::init('Distributor');
        $country = null;

        foreach ($appointments as $appointment) {
            if ($appointment['Appointment']['date'] == date('Y-m-d', strtotime(date('Y-m-d') . ' -1 day'))) {

                $url = Router::url(array(
                    'controller' => 'appointments',
                    'action' => 'edit',
                    $appointment['Appointment']['id']
                ));

                $user_tmp = $this->User->findById($appointment['Appointment']['user_assigned_id']);

                $languageCode = $languageCode = ConstantsLanguages::ENGLISH_CODE;
                $languageId = null;

                if (isset($user_tmp['User']['language_id'])) {
                    $languageCode = $this->User->Language->findById($user_tmp['User']['language_id'])['Language']['code'];
                    $languageId = $user_tmp['User']['language_id'];
                } else {
                    $language = $this->User->Language->findByCode($languageCode);
                    $languageId = $language ? $language['Language']['id'] : null;
                }

                $country = null;

                if (!empty($appointment['Appointment']['garage_id'])) {
                    $garage_tmp = $this->Garage->findById($appointment['Appointment']['garage_id']);
                    if ($garage_tmp) {
                        $province = $this->Province->findById($garage_tmp['Garage']['province_id']);
                        if (!empty($province)) {
                            $country = $this->Country->findById($province['Province']['country_id']);
                        }
                    }
                }

                if (!empty($appointment['Appointment']['distributor_id'])) {
                    $distributor_tmp = $this->Distributor->findById($user_tmp['distributor_id']);
                    if ($distributor_tmp) {
                        $postcode_tmp = explode(' ', $distributor_tmp['Distributor']['postcode']);
                        $province = $this->Garage->getProvinceIdByPostcode($postcode_tmp[0]);
                        $country = $this->Country->get_country_by_province($province);
                    }
                }

                $contact = $this->Contact->findById($user_tmp['User']['contact_id']);

                if ($contact && $country) {
                    $fullUrl = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . $url;

                    $email = array(
                        'Email' => array(
                            'to' => $contact['Contact']['email'],
                            'bcc' => GNMAAG_BCC,
                            'subject' => __t('Email.Appointment_reminder', $languageCode),
                            'body' => $fullUrl,
                            'template' => 'email_appointment_reminder',
                            'email_type_id' => ConstantsEmailTypes::APPOINTMENT_REMINDER,
                            'view_vars' => serialize(
                                array(
                                    'language_code' => $languageCode,
                                    'appointment_url' => $fullUrl,
                                    'appointment_date' => Fecha::toSendgridFormat($appointment['Appointment']['date'], Fecha::_FORMATO_BD_FECHA),
                                    'appointment_start_time' => $appointment['Appointment']['start_time'],
                                    'appointment_end_time' => $appointment['Appointment']['end_time']
                                )
                            ),
                            'sent' => ConstantsBooleans::NO,
                            'creation_date' => date('Y-m-d H:i:s'),
                            'platform_id' => ConstantsPlatform::GNM,
                            'language_id' => $languageId,
                            'coutry_id' => $country,
                            'aag_region_id' => $user_tmp['User']['aag_region_id'],
                        )
                    );

                    $emailBd = $this->Email->add($email);

                    // for SendGrid email is sent when it's created
                    if ($emailBd) {
                        $this->Email->sendSendgridEmail($emailBd);
                        return true;
                    }

                    return false;
                }
            }
        }
    }

    public function updateAppointmentsToPending()
    {
        $appointments = $this->findAllByAppointmentStatusId(array(ConstantsStatusAppointments::PLANNED, ConstantsStatusAppointments::RESCHEDULED));
        foreach ($appointments as $appointment) {
            if ($appointment['Appointment']['date'] < date('Y-m-d') && ($appointment['Appointment']['feedback'] == ' ' || $appointment['Appointment']['feedback'] == null)) {
                $this->UpdateAppointmentPending($appointment['Appointment']['id']);

                //$appointment['Appointment']['appointment_status_id'] = ConstantsStatusAppointments::PENDING;
                //$this->save($appointment);
            }
        }

        $this->commit();
    }

    public function UpdateAppointmentPending($appointment_id)
    {
        $fields = array(
            'Appointment' => array(
                'appointment_status_id',
            )
        );

        $appointment['Appointment']['id'] = $appointment_id;
        $appointment['Appointment']['appointment_status_id'] = ConstantsStatusAppointments::PENDING;

        $appointment_bd = $this->guardar($appointment, $fields);
        if (!$appointment_bd) {
            return false;
        }

        $this->commit();
        return $appointment_bd;
    }
}
