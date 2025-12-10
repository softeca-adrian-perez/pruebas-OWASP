<?php
class AppointmentsCommentsController extends AppController
{
    public $uses = array(
        'User',
        'Appointment',
        'AppointmentComment',
        'AppointmentContactList',
        'AppointmentFile',
        'ContactContactList',
        'Contact',
        'User',
        'Alert',
        'Distributor',
        'Garage',
        'AppointmentFeeling',
        'AppointmentObjectiveComment'
    );

    /**
     * AJAX create AppointmentComment.
     */
    public function ajax_add($is_event = null)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $action = ConstantsActions::COMMENT;

            $comment = array(
                'AppointmentComment' => array(
                    'appointment_id' => $this->request->data['appointment_id'],
                    'body' => $this->request->data['body']
                )
            );
            $commentBd = $this->AppointmentComment->new_comment($comment);
            if ($commentBd) {
                $appointment = $this->Appointment->findById($this->request->data['appointment_id']);

                if ($appointment['Appointment']['feedback']) {
                    $appointment['Appointment']['appointment_contact_lists'] = $this->AppointmentContactList->find('list', array(
                        'conditions' => array(
                            'appointment_id' => $appointment['Appointment']['id'],
                        ),
                        'fields' => 'contact_list_id'
                    ));

                    $comment_creator_tmp = $this->User->find('first', array(
                        'conditions' => array(
                            'id' => CakeSession::read('Auth.User.id')
                        ),
                        'fields' => array(
                            'full_name'
                        )
                    ));

                    $comment_creator = $comment_creator_tmp['User']['full_name'];

                    if (is_null($is_event)) {
                        $customer = '';
                        if (!is_null($appointment['Appointment']['garage_id'])) {
                            $customer_tmp = $this->Garage->find('first', array(
                                'conditions' => array(
                                    'id' => $appointment['Appointment']['garage_id']
                                ),
                                'fields' => array(
                                    'name'
                                )
                            ));
                            $customer = ' - ' . $customer_tmp['Garage']['name'];
                        } elseif (!is_null($appointment['Appointment']['distributor_id'])) {
                            $customer_tmp = $this->Distributor->find('first', array(
                                'conditions' => array(
                                    'id' => $appointment['Appointment']['distributor_id']
                                ),
                                'fields' => array(
                                    'name'
                                )
                            ));
                            $customer = ' - ' . $customer_tmp['Distributor']['name'];
                        }
                        $subject = sprintf(__t('Alert.New_visit_comment'), $comment_creator . $customer);

                        $this->sendDataAlert($appointment, $subject, __t('Alert.New_comment'), $commentBd, $action);
                    } else {
                        $subject = sprintf(__t('Alert.New_event_comment'), $comment_creator);

                        $this->sendDataAlertEvent($appointment, $subject, __t('Alert.New_comment'), $commentBd, $action);
                    }
                }
            }

            $users = $this->User->listCompleteNameRegion($aagRegionId);
            $usersImages = $this->User->UserImage->find('all');
            $comments = $this->Appointment->AppointmentComment->find('all', array(
                'conditions' => array(
                    'appointment_id' => $this->request->data['appointment_id']
                ),
                'order' => array(
                    'creation_date' => 'DESC'
                ),
                'limit' => 5
            ));
            $commentsNumber = $this->Appointment->AppointmentComment->find('count', array(
                'conditions' => array(
                    'appointment_id' => $this->request->data['appointment_id']
                )
            ));

            $loadMore = count($comments) == $commentsNumber ? ConstantsBooleans::NO : ConstantsBooleans::YES;

            $this->set(array(
                'comments' => $comments,
                'users' => $users,
                'users_images' => $usersImages,
                'load_more' => $loadMore,
                'start' => 5
            ));

            $this->layout = null;
            $this->render('/Appointments/Elements/comments');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX load more AppointmentComment
     */
    public function ajax_load_more()
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $start = $this->request->data['start'] + 5;

            $usersImages = $this->User->UserImage->find('all');
            $users = $this->User->listCompleteNameRegion($aagRegionId);
            $comments = $this->Appointment->AppointmentComment->find('all', array(
                'conditions' => array(
                    'appointment_id' => $this->request->data['appointment_id']
                ),
                'order' => array(
                    'creation_date' => 'DESC'
                ),
                'limit' => $start
            ));
            $commentsNumber = $this->Appointment->AppointmentComment->find('count', array(
                'conditions' => array(
                    'appointment_id' => $this->request->data['appointment_id']
                )
            ));

            $loadMore = count($comments) == $commentsNumber ? ConstantsBooleans::NO : ConstantsBooleans::YES;

            $this->set(array(
                'comments' => $comments,
                'users' => $users,
                'users_images' => $usersImages,
                'load_more' => $loadMore,
                'start' => $start
            ));

            $this->layout = null;
            $this->render('/Appointments/Elements/comments');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function sendDataAlert($appointmentData, $emailSubject, $alertBody, $commentBd, $action)
    {
        $contacts = array();
        $appointmentUser = $this->Appointment->findById($appointmentData['Appointment']['id']);
        $userCreator = $this->User->findById($appointmentUser['Appointment']['user_creation_id']);
        $userAssigned = $this->User->findById($appointmentData['Appointment']['user_assigned_id']);
        $contacts[] = $this->Contact->findById($userCreator['User']['contact_id']);
        $contacts[] = $this->Contact->findById($userAssigned['User']['contact_id']);

        $contactsList = $this->AppointmentContactList->findAllByAppointmentId($appointmentData['Appointment']['id']);
        if (!empty($contactsList)) {
            foreach ($contactsList as $contact_list) {
                $contacts_tmp = $this->ContactContactList->findAllByContactListId($contact_list['AppointmentContactList']['contact_list_id']);
                foreach ($contacts_tmp as $contact_tmp) {
                    $contact = $this->Contact->findById($contact_tmp['ContactContactList']['contact_id']);
                    if (!empty($contact)) {
                        $contacts[] = $contact;
                    }
                }
            }
        }

        //Parent of user creator
        $contactUserCreator = $this->Contact->findById($userCreator['User']['contact_id']);
        if ($contactUserCreator['Contact']['contact_id']) {
            $contacts[] = $this->Contact->findById($contactUserCreator['Contact']['contact_id']);
        }

        $contacts = array_map("unserialize", array_unique(array_map("serialize", $contacts)));

        $url = Router::url(array(
            'controller' => 'appointments',
            'action' => 'edit',
            $appointmentData['Appointment']['id']
        ));

        $appointmentEmail = array(
            'title' => __t('Appointment.Appointments'),
            'date' => $appointmentData['Appointment']['date'],
            'start_time' => $appointmentData['Appointment']['start_time'],
            'end_time' => $appointmentData['Appointment']['end_time'],
            'link' => ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . $url,
            'feedback' => $appointmentData['Appointment']['feedback'],
            'customer_performance_summary' => $appointmentData['Appointment']['customer_performance_summary']
        );

        if (isset($appointmentData['Appointment']['description'])) {
            $appointmentEmail['notes'] = $appointmentData['Appointment']['description'];
        }

        if (isset($appointmentData['Appointment']['appointment_feeling_id']) && !empty($appointmentData['Appointment']['appointment_feeling_id'])) {
            $appointmentFeeling = $this->AppointmentFeeling->findById($appointmentData['Appointment']['appointment_feeling_id']);

            if ($appointmentFeeling) {
                $appointmentEmail['feeling'] = $appointmentFeeling['AppointmentFeeling']['name_' . __l()];
                $appointmentEmail['feeling_color'] = $appointmentFeeling['AppointmentFeeling']['color'];
            }
        }

        $userTmp = $this->User->findById($appointmentData['Appointment']['user_assigned_id']);
        $appointmentEmail['assigned_to'] = $userTmp['User']['name'] . " " . $userTmp['User']['surname'];

        $appointmentEmail['comment'] = $commentBd['AppointmentComment']['body'];
        $appointmentEmail['comment_user_id'] = $commentBd['AppointmentComment']['user_id'];

        if (!empty($appointmentData['Appointment']['garage_id'])) {
            $garage = $this->Garage->findById($appointmentData['Appointment']['garage_id']);
            $appointmentEmail['customer_id'] = $garage['Garage']['g_number_id'];
            $appointmentEmail['customer_name'] = $garage['Garage']['name'];
        } elseif (!empty($appointmentData['Appointment']['distributor_id'])) {
            $distributor = $this->Distributor->findById($appointmentData['Appointment']['distributor_id']);
            $appointmentEmail['customer_id'] = $distributor['Distributor']['account_number'];
            $appointmentEmail['customer_name'] = $distributor['Distributor']['name'];
            $appointmentEmail['mtd'] = $appointmentData['Appointment']['mtd'];
            $appointmentEmail['qtd'] = $appointmentData['Appointment']['qtd'];
            $appointmentEmail['ytd'] = $appointmentData['Appointment']['ytd'];
            $appointmentEmail['objectives'] = $this->AppointmentObjectiveComment->getObjectivesToEmail($appointmentData['Appointment']['id']);
        }

        if (!empty($appointmentData['Appointment']['id'])) {
            $appointmentEmail['files'] = $this->AppointmentFile->findAllByAppointmentId($appointmentData['Appointment']['id']);
        }

        $comment = true;
        $this->Alert->new_alert($contacts, ConstantsAlerts::APPOINTMENT, $alertBody, $emailSubject, $url, $appointmentEmail, $appointmentData['Appointment']['id'], $appointmentData['Appointment']['user_assigned_id'], $action, null, null, $comment);
    }

    private function sendDataAlertEvent($appointmentData, $emailSubject, $alertBody, $commentBd, $action)
    {
        $contacts = array();
        $appointmentUser = $this->Appointment->findById($appointmentData['Appointment']['id']);
        $userCreator = $this->User->findById($appointmentUser['Appointment']['user_creation_id']);
        $userAssigned = $this->User->findById($appointmentData['Appointment']['user_assigned_id']);
        $contacts[] = $this->Contact->findById($userCreator['User']['contact_id']);
        $contacts[] = $this->Contact->findById($userAssigned['User']['contact_id']);

        $contactsList = $this->AppointmentContactList->findAllByAppointmentId($appointmentData['Appointment']['id']);
        if (!empty($contactsList)) {
            foreach ($contactsList as $contact_list) {
                $contacts_tmp = $this->ContactContactList->findAllByContactListId($contact_list['AppointmentContactList']['contact_list_id']);
                foreach ($contacts_tmp as $contact_tmp) {
                    $contact = $this->Contact->findById($contact_tmp['ContactContactList']['contact_id']);
                    if (!empty($contact)) {
                        $contacts[] = $contact;
                    }
                }
            }
        }

        //Parent of user creator
        $contactUserCreator = $this->Contact->findById($userCreator['User']['contact_id']);
        if ($contactUserCreator['Contact']['contact_id']) {
            $contacts[] = $this->Contact->findById($contactUserCreator['Contact']['contact_id']);
        }

        $contacts = array_map("unserialize", array_unique(array_map("serialize", $contacts)));

        $url = Router::url(array(
            'controller' => 'appointments',
            'action' => 'edit_event',
            $appointmentData['Appointment']['id']
        ));

        $date = $appointmentData['Appointment']['date'];

        if (!Fecha::isDate($appointmentData['Appointment']['date'])) {
            $date = Fecha::toFormatoBd($appointmentData['Appointment']['date']);
        }

        if (isset($appointmentData['Appointment']['end_date']) && !empty($appointmentData['Appointment']['end_date'])) {
            $endDate = Fecha::toFormatoBd($appointmentData['Appointment']['end_date']);
        } else {
            $endDate = $appointmentData['Appointment']['date'];
        }

        $appointmentEmail = array(
            'title' => $appointmentData['Appointment']['title'],
            'date' => $date,
            'end_date' => $endDate,
            'start_time' => $appointmentData['Appointment']['start_time'],
            'end_time' => $appointmentData['Appointment']['end_time'],
            'description' => $appointmentData['Appointment']['description'],
            'user_creator' => $userCreator['User']['full_name'],
            'link' => ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . $url
        );

        if (isset($appointmentData['Appointment']['appointment_feeling_id']) && !empty($appointmentData['Appointment']['appointment_feeling_id'])) {
            $appointmentFeeling = $this->AppointmentFeeling->findById($appointmentData['Appointment']['appointment_feeling_id']);

            if ($appointmentFeeling) {
                $appointmentEmail['feeling'] = $appointmentFeeling['AppointmentFeeling']['name_' . __l()];
                $appointmentEmail['feeling_color'] = $appointmentFeeling['AppointmentFeeling']['color'];
            }
        }

        $userTmp = $this->User->findById($appointmentData['Appointment']['user_assigned_id']);
        $appointmentEmail['assigned_to'] = $userTmp['User']['name'] . " " . $userTmp['User']['surname'];

        $appointmentEmail['comment'] = $commentBd['AppointmentComment']['body'];
        $appointmentEmail['comment_user_id'] = $commentBd['AppointmentComment']['user_id'];

        if (!empty($appointmentData['Appointment']['id'])) {
            $appointmentEmail['files'] = $this->AppointmentFile->findAllByAppointmentId($appointmentData['Appointment']['id']);
        }

        $comment = true;
        $this->Alert->new_alert($contacts, ConstantsAlerts::EVENT, $alertBody, $emailSubject, $url, $appointmentEmail, $appointmentData['Appointment']['id'], $appointmentData['Appointment']['user_assigned_id'], $action, null, null, $comment);
    }
}
