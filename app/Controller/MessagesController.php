<?php
class MessagesController extends AppController
{
    /**
     * Messages home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_MAILBOX)
        ) {
            $conditions = $this->Message->conditions($this->request->query);
            $this->request->data['Search'] = $this->request->query;

            if ($this->Acceso->user('role_id') == ConstantsRoles::ADMIN) {
                $messages = $this->custom_pagination(
                    $this->Message->query('list'),
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL
                );
            } else {
                $messages = $this->custom_pagination(
                    $this->Message->findAllMessagesBySenderId($this->Acceso->user('id')),
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL
                );
            }

            $this->set(array(
                'messages' => $messages,
                'types' => $this->Message->MessageType->search_list(),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit garage message.
     */
    public function edit_garages($message_id)
    {
        $message = $this->Message->findByIdAndUserId($message_id, $this->Acceso->user('id'));
        if (
            $message &&
            is_null($message['Message']['date_sent']) &&
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_MAILBOX)
        ) {
            if (!$message) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_GARAGE));
                $this->redirect(
                    array(
                        'controller' => 'messages',
                        'action' => 'home',
                    )
                );
            }
            if (!$this->request->is('get')) {
                if (isset($this->request->data['send'])) {
                    if ($this->Message->send_message($this->request->data)) {
                        $this->Session->setFlashSuccess(__t('Message.Well_sent'));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t('Message.Bad_sent'));
                    }
                } else {
                    if ($this->Message->edit_message($this->request->data)) {
                        $this->Session->setFlashSuccess(__t('Message.Well_save'));
                        $this->redirect($this->request->here);
                    } else {
                        if (!empty(FileManager::get_problems_upload())) {
                            $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                        } else {
                            $this->Session->setFlashError(__t('Message.Bad_save'));
                        }
                    }
                }
            }

            $this->request->data = $message;
            $recipients = $this->Message->MessageGarage->getListByMessageId($message_id);
            $message_files = $this->Message->MessageFile->findAllByMessageId($message_id);

            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $urlCancel = array(
                'controller' => 'messages',
                'action' => 'home'
            );
            $this->setGarageFilter();
            $this->set(array(
                'url_cancel' => $urlCancel,
                'message_files' => $message_files,
                'recipients' => $recipients,
                'user_aag_region_id' => $aagRegionId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create garage message.
     */
    public function new_garages()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_MAILBOX)
        ) {
            if (!$this->request->is('get')) {
                $message = $this->Message->new_message(ConstantsMessagesTypes::GARAGE, $this->Acceso->user('id'), $this->request->data);
                if ($message) {
                    $message['Message']['files'] = $this->request->data['Message']['files'];
                    $message['Message']['recipients'] = $this->request->data['Message']['recipients'];
                    if (isset($this->request->data['send'])) {
                        if ($this->Message->send_message($message)) {
                            $this->Session->setFlashSuccess(__t('Message.Well_sent'));
                            $this->redirect($this->request->here);
                        } else {
                            $this->Session->setFlashError(__t('Message.Bad_sent'));
                        }
                    } else {
                        if ($this->Message->edit_message($message)) {
                            $this->Session->setFlashSuccess(__t('Message.Well_save'));
                            $this->redirect($this->request->here);
                        } else {
                            if (!empty(FileManager::get_problems_upload())) {
                                $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                            } else {
                                $this->Session->setFlashError(__t('Message.Bad_save'));
                            }
                        }
                    }
                }
            }

            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $urlCancel = array(
                'controller' => 'messages',
                'action' => 'home'
            );
            $this->setGarageFilter();
            $this->set(array(
                'url_cancel' => $urlCancel,
                'user_aag_region_id' => $aagRegionId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setGarageFilter()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $this->loadModel('Distributor');
        $this->loadModel('Contact');
        $garage_branch = $this->Distributor->find('list');
        if (CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID ||  CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID) {
            $contact_tmp = $this->Contact->findById(CakeSession::read('Auth.User.Contact.id'));
            $garage_bdm =  array(
                $contact_tmp['Contact']['id'] => $contact_tmp['Contact']['full_name']
            );
        } else {
            $garage_bdm = $this->Contact->getListByRoleId(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG));
        }
        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_AAG ||  CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_TG) {
            $contact_tmp = $this->Contact->findById(CakeSession::read('Auth.User.Contact.id'));
            $contact_contact_id = $this->Contact->findById($contact_tmp['Contact']['contact_id']);
            if (isset($contact_contact_id['Contact'])) {
                $garage_rsm = array(
                    $contact_contact_id['Contact']['id'] => $contact_contact_id['Contact']['full_name']
                );
            } else {
                $garage_rsm = null;
            }
        } else {
            $garage_rsm = $this->Contact->getListByPositionIdAndAagRegionId(array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID), $aagRegionId);
        }

        $garage_customer_status = Configure::read('Garage_Status') + Configure::read('Garage_Status_De');
        foreach ($garage_customer_status as $key => $status) {
            $garage_customer_status[$key] = __t($status);
        }

        $this->set(array(
            'garage_branch' => $garage_branch,
            'garage_bdm' => $garage_bdm,
            'garage_rsm' => $garage_rsm,
            'garage_customer_status' => $garage_customer_status,
        ));
    }

    private function setDistributorFilter()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $this->loadModel('Distributor');
        $this->loadModel('Contact');
        $distributor_branch = $this->Distributor->find('list');
        if (CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID ||  CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID) {
            $contact_tmp = $this->Contact->findById(CakeSession::read('Auth.User.contact_id'));
            $distributor_bdm =  array(
                $contact_tmp['Contact']['id'] => $contact_tmp['Contact']['full_name']
            );
        } else {
            $distributor_bdm = $this->Contact->getListByRoleId(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG));
        }
        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_AAG ||  CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_TG) {
            $contact_tmp = $this->Contact->findById(CakeSession::read('Auth.User.contact_id'));
            $contact_contact_id = $this->Contact->findById($contact_tmp['Contact']['contact_id']);
            if (isset($contact_contact_id['Contact'])) {
                $distributor_rsm = array(
                    $contact_contact_id['Contact']['id'] => $contact_contact_id['Contact']['full_name']
                );
            } else {
                $distributor_rsm = null;
            }
        } else {
            $distributor_rsm = $this->Contact->getListByPositionIdAndAagRegionId(array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID), $aagRegionId);
        }

        $this->set(array(
            'distributor_branch' => $distributor_branch,
            'distributor_bdm' => $distributor_bdm,
            'distributor_rsm' => $distributor_rsm,
        ));
    }

    /**
     * Create distributor message.
     */
    public function new_distributors()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_MAILBOX)
        ) {
            if (!$this->request->is('get')) {
                $message = $this->Message->new_message(ConstantsMessagesTypes::DISTRIBUTOR, $this->Acceso->user('id'), $this->request->data);
                if ($message) {
                    $message['Message']['files'] = $this->request->data['Message']['files'];
                    $message['Message']['recipients'] = $this->request->data['Message']['recipients'];
                    if (isset($this->request->data['send'])) {
                        if ($this->Message->send_message($message)) {
                            $this->Session->setFlashSuccess(__t('Message.Well_sent'));
                            $this->redirect($this->request->here);
                        } else {
                            $this->Session->setFlashError(__t('Message.Bad_sent'));
                        }
                    } else {
                        if ($this->Message->edit_message($message)) {
                            $this->Session->setFlashSuccess(__t('Message.Well_save'));
                            $this->redirect($this->request->here);
                        } else {
                            if (!empty(FileManager::get_problems_upload())) {
                                $this->Session->setFlashError(implode('<br>', FileManager::get_problems_upload()));
                            } else {
                                $this->Session->setFlashError(__t('Message.Bad_save'));
                            }
                        }
                    }
                }
            }

            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $urlCancel = array(
                'controller' => 'messages',
                'action' => 'home'
            );
            $this->setDistributorFilter();
            $this->set(array(
                'url_cancel' => $urlCancel,
                'user_aag_region_id' => $aagRegionId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit distributor message.
     */
    public function edit_distributors($message_id)
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_MAILBOX)
        ) {
            $message = $this->Message->findById($message_id);
            if (!$message) {
                $this->Session->setFlashError(__t(ConstantsMessages::NOT_EXIST_GARAGE));
                $this->redirect(
                    array(
                        'controller' => 'messages',
                        'action' => 'home',
                    )
                );
            }
            if (!$this->request->is('get')) {
                if (isset($this->request->data['send'])) {
                    if ($this->Message->send_message($this->request->data)) {
                        $this->Session->setFlashSuccess(__t('Message.Well_sent'));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t('Message.Bad_sent'));
                    }
                } else {
                    if ($this->Message->edit_message($this->request->data)) {
                        $this->Session->setFlashSuccess(__t('Message.Well_save'));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t('Message.Bad_save'));
                    }
                }
            }

            $this->request->data = $message;
            $recipients = $this->Message->MessageDistributor->getListByMessageId($message_id);
            $message_files = $this->Message->MessageFile->findAllByMessageId($message_id);

            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $urlCancel = array(
                'controller' => 'messages',
                'action' => 'home'
            );
            $this->setDistributorFilter();
            $this->set(array(
                'url_cancel' => $urlCancel,
                'message_files' => $message_files,
                'recipients' => $recipients,
                'user_aag_region_id' => $aagRegionId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Received messages.
     */
    public function recieved_messages()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_MAILBOX)
        ) {
            $conditions = $this->Message->conditions($this->request->query);
            $this->request->data['Search'] = $this->request->query;
            if ($this->Acceso->user('role_id') == ConstantsRoles::GARAGE) {
                $messages = $this->custom_pagination(
                    $this->Message->findAllMessagesByGarageRecipient($this->Acceso->user('garage_id')),
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL
                );
            } elseif ($this->Acceso->user('role_id') == ConstantsRoles::DISTRIBUTOR) {
                $messages = $this->custom_pagination(
                    $this->Message->findAllMessagesByDistributorRecipient($this->Acceso->user('distributor_id')),
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL
                );
            } else {
                $messages = $this->custom_pagination(
                    $this->Message->query('list'),
                    $conditions,
                    ConstantsPagination::SIZE_PAGE_SMALL
                );
            }

            $this->set(array(
                'messages' => $messages,
                'role' => $this->Acceso->user('role_id'),
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete message.
     */
    public function delete_message($message_id)
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_MAILBOX)
        ) {
            if ($this->Message->delete_message($message_id)) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
            }
            $this->redirect(array(
                'controller' => 'messages',
                'action' => 'home',
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Message view.
     */
    public function view($message_id)
    {
        $message = $this->Message->findById($message_id);
        if (
            $message &&
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_MAILBOX)
        ) {
            $message_files = $this->Message->MessageFile->findAllByMessageId($message_id);
            $types = $this->Message->MessageType->search_list();
            if (!in_array($this->Acceso->user('role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR))) {
                if ($message['Message']['type'] == ConstantsMessagesTypes::GARAGE) {
                    $recipients = $this->Message->MessageGarage->getListByMessageId($message_id);
                } elseif ($message['Message']['type'] == ConstantsMessagesTypes::DISTRIBUTOR) {
                    $recipients = $this->Message->MessageDistributor->getListByMessageId($message_id);
                }
            } else {
                $recipients = array();
                if ($this->Acceso->user('role_id') == ConstantsRoles::GARAGE) {
                    $this->Message->MessageGarage->readMessage($message_id, $this->Acceso->user('garage_id'));
                } elseif ($this->Acceso->user('role_id') == ConstantsRoles::DISTRIBUTOR) {
                    $this->Message->MessageDistributor->readMessage($message_id, $this->Acceso->user('distributor_id'));
                }
            }

            $this->set(array(
                'message' => $message,
                'message_files' => $message_files,
                'types' => $types,
                'recipients' => $recipients,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }
}
