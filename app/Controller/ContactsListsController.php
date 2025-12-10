<?php
class ContactsListsController extends AppController
{
    public $uses = array(
        'AppointmentContactList',
        'Contact',
        'ContactList',
        'ContactContactList',
        'TaskContactList',
        'Position'
    );

    /**
     * Contacts lists home page.
     */
    public function home()
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_CONTACT_LIST) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS_LIST)
            )
        ) {
            $contactsLists = $this->ContactList->find('all', array(
                'conditions' => array(
                    'aag_region_id' => $aagRegionId,
                    'OR' => array(
                        'user_id' => CakeSession::read('Auth.User.id'),
                        'global' => ConstantsBooleans::YES
                    ),
                )
            ));
            foreach ($contactsLists as $key => $contactList) {
                $contactsLists[$key]['Contacts'] = $this->ContactList->getContactsByContactListAndUser($contactList['ContactList']['id']);
                $tasksContactsLists = $this->TaskContactList->findByContactListId($contactList['ContactList']['id']);
                if (empty($tasksContactsLists)) {
                    $contactsLists[$key]['Tasks'] = ConstantsBooleans::YES;
                } else {
                    $contactsLists[$key]['Tasks'] = ConstantsBooleans::NO;
                }
            }

            $this->set(
                array(
                    'contacts_lists' => $contactsLists,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create contact list.
     */
    public function add()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT_LIST) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS_LIST)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $positions = $this->Position->search_list();
            $url = Router::url(
                array(
                    'controller' => 'contacts_lists',
                    'action' => 'search_add'
                )
            );
            $contacts = $this->Contact->find('all', array(
                'conditions' => array(
                    'Contact.aag_region_id' => $aagRegionId
                ),
                'order' => array(
                    'first_name',
                ),
                'fields' => array(
                    'id',
                    'first_name',
                    'last_name'
                )
            ));

            $this->set(
                array(
                    'contacts' => $contacts,
                    'url' => $url,
                    'positions' => $positions
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit contact list.
     */
    public function edit($contact_list_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $contactList = $this->ContactList->findByIdAndAagRegionId($contact_list_id, $aagRegionId);

        if (
            $contactList &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT_LIST) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS_LIST)
        ) {
            $positions = $this->Position->search_list();
            $url = Router::url(
                array(
                    'controller' => 'contacts_lists',
                    'action' => 'search_edit'
                )
            );

            $this->request->data = $contactList;
            $contactsLists = $this->ContactList->getContactsByContactListAndUser($contact_list_id);
            $contacts = $this->Contact->find('all', array(
                'conditions' => array(
                    'Contact.aag_region_id' => $aagRegionId
                ),
                'order' => array(
                    'first_name',
                ),
                'fields' => array(
                    'id',
                    'first_name',
                    'last_name'
                ),
                'limit' => 100
            ));

            foreach ($contacts as $key => $contact) {
                foreach ($contactsLists as $myContact) {
                    if ($contact['Contact']['id'] == $myContact['Contact']['id']) {
                        unset($contacts[$key]);
                    }
                }
            }

            $this->set(
                array(
                    'contacts_lists' => $contactsLists,
                    'contacts' => $contacts,
                    'list' => $contactList,
                    'url' => $url,
                    'positions' => $positions,
                    'contact_list_id' => $contact_list_id
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search add.
     */
    public function search_add()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT_LIST) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS_LIST)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $data = $this->request->data;
            if ($data['contact_name'] != '' && $data['position_id'] != '') {
                $contacts = $this->Contact->find('all', array(
                    'conditions' => array(
                        'aag_region_id' => $aagRegionId,
                        'CONCAT(first_name, " ", last_name) LIKE' => '%' . $data['contact_name'] . '%',
                        'position_id' => $data['position_id']
                    ),
                    'order' => array(
                        'first_name',
                    )
                ));
            } elseif ($data['contact_name'] != '') {
                $contacts = $this->Contact->find('all', array(
                    'conditions' => array(
                        'aag_region_id' => $aagRegionId,
                        'CONCAT(first_name, " ", last_name) LIKE' => '%' . $data['contact_name'] . '%',
                    ),
                    'order' => array(
                        'first_name',
                    )
                ));
            } elseif ($data['position_id'] != '') {
                $contacts = $this->Contact->find('all', array(
                    'conditions' => array(
                        'position_id' => $data['position_id'],
                        'aag_region_id' => $aagRegionId
                    ),
                    'order' => array(
                        'first_name',
                    )
                ));
            } else {
                $contacts = $this->Contact->find(
                    'all',
                    array(
                        'conditions' => array(
                            'aag_region_id' => $aagRegionId
                        ),
                        'order' => array(
                            'first_name',
                        )
                    )
                );
            }

            $this->set(
                array(
                    'contacts' => $contacts,
                )
            );

            $this->layout = null;
            $this->render('../ContactsLists/Elements/ajax_list');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search edit.
     */
    public function search_edit()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT_LIST) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS_LIST)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $data = $this->request->data;
            $list = $this->ContactList->findById($data['contact_list_id']);
            $contactsLists = $this->ContactList->getContactsByContactListAndUser($data['contact_list_id']);

            if ($data['contact_name'] != '' && $data['position_id'] != '') {
                $contacts = $this->Contact->find('all', array(
                    'conditions' => array(
                        'aag_region_id' => $aagRegionId,
                        'CONCAT(first_name, " ", last_name) LIKE' => '%' . $data['contact_name'] . '%',
                        'position_id' => $data['position_id']
                    ),
                    'order' => array(
                        'first_name',
                    ),
                    'limit' => 100
                ));
            } elseif ($data['contact_name'] != '') {
                $contacts = $this->Contact->find('all', array(
                    'conditions' => array(
                        'aag_region_id' => $aagRegionId,
                        'CONCAT(first_name, " ", last_name) LIKE' => '%' . $data['contact_name'] . '%',
                    ),
                    'order' => array(
                        'first_name',
                    ),
                    'limit' => 100
                ));
            } elseif ($data['position_id'] != '') {
                $contacts = $this->Contact->find('all', array(
                    'conditions' => array(
                        'position_id' => $data['position_id'],
                        'aag_region_id' => $aagRegionId
                    ),
                    'order' => array(
                        'first_name',
                    ),
                    'limit' => 100
                ));
            } else {
                $contacts = $this->Contact->find(
                    'all',
                    array(
                        'conditions' => array(
                            'aag_region_id' => $aagRegionId
                        ),
                        'order' => array(
                            'first_name',
                        ),
                        'limit' => 100
                    )
                );
            }
            foreach ($contacts as $key => $contact) {
                foreach ($contactsLists as $myContact) {
                    if ($contact['Contact']['id'] == $myContact['Contact']['id']) {
                        unset($contacts[$key]);
                    }
                }
            }

            $this->set(
                array(
                    'contacts_lists' => $contactsLists,
                    'contacts' => $contacts,
                    'list' => $list,
                )
            );

            $this->layout = null;
            $this->render('../ContactsLists/Elements/ajax_list');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search contact list.
     */
    public function search_contact_list($search)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_CONTACT_LIST) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS_LIST)
            )
        ) {
            if ($search == 'all') {
                $contactsLists = $this->ContactList->find('all', array(
                    'conditions' => array(
                        'OR' => array(
                            'user_id' => CakeSession::read('Auth.User.id'),
                            'global' => ConstantsBooleans::YES
                        ),
                        'aag_region_id' => $aagRegionId
                    )
                ));
                foreach ($contactsLists as $key => $contactList) {
                    $contactsLists[$key]['Contacts'] = $this->ContactList->getContactsByContactListAndUser($contactList['ContactList']['id']);
                    $tasksContactsLists = $this->TaskContactList->findByContactListId($contactList['ContactList']['id']);
                    if (empty($tasksContactsLists)) {
                        $contactsLists[$key]['Tasks'] = ConstantsBooleans::YES;
                    } else {
                        $contactsLists[$key]['Tasks'] = ConstantsBooleans::NO;
                    }
                }
            } else {
                $contactsLists = $this->ContactList->find('all', array(
                    'conditions' => array(
                        'OR' => array(
                            'user_id' => CakeSession::read('Auth.User.id'),
                            'global' => ConstantsBooleans::YES
                        ),
                        'name LIKE' => '%' . $search . '%',
                        'aag_region_id' => $aagRegionId
                    )
                ));
                foreach ($contactsLists as $key => $contactList) {
                    $contactsLists[$key]['Contacts'] = $this->ContactList->getContactsByContactListAndUser($contactList['ContactList']['id']);
                    $tasksContactsLists = $this->TaskContactList->findByContactListId($contactList['ContactList']['id']);
                    if (empty($tasksContactsLists)) {
                        $contactsLists[$key]['Tasks'] = ConstantsBooleans::YES;
                    } else {
                        $contactsLists[$key]['Tasks'] = ConstantsBooleans::NO;
                    }
                }
            }

            $this->set(
                array(
                    'contacts_lists' => $contactsLists
                )
            );

            $this->layout = null;
            $this->render('../ContactsLists/Elements/ajax_home');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX search contact.
     */
    public function search_contact($search)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];
        $roleId = $user['aag_region_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_CONTACT_LIST) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS_LIST)
            )
        ) {
            if ($search == 'all') {
                $contactsLists = $this->ContactList->find('all', array(
                    'conditions' => array(
                        'OR' => array(
                            'user_id' => CakeSession::read('Auth.User.id'),
                            'global' => ConstantsBooleans::YES
                        ),
                        'aag_region_id' => $aagRegionId
                    )
                ));

                foreach ($contactsLists as $key => $contactList) {
                    $contactsLists[$key]['Contacts'] = $this->ContactList->getContactsByContactListAndUser($contactList['ContactList']['id']);
                    $tasksContactsLists = $this->TaskContactList->findByContactListId($contactList['ContactList']['id']);
                    if (empty($tasksContactsLists)) {
                        $contactsLists[$key]['Tasks'] = ConstantsBooleans::YES;
                    } else {
                        $contactsLists[$key]['Tasks'] = ConstantsBooleans::NO;
                    }
                }
            } else {
                $contactList_with_contacts = $this->ContactList->getContactsByContactList($search, $aagRegionId);
                $contactList_tmp = array();
                foreach ($contactList_with_contacts as $key => $item) {
                    $contactList_tmp[$key]['ContactList'] = $item['ContactList'];
                }

                $contactsLists = array_intersect_key(
                    $contactList_tmp,
                    array_unique(array_map(function ($item) {
                        return $item['ContactList']['id'];
                    }, $contactList_tmp))
                );

                foreach ($contactsLists as $key => $contactList) {
                    $contactsLists[$key]['Contacts'] = $this->ContactList->getContactsByContactListAndUser($contactList['ContactList']['id']);
                    $tasksContactsLists = $this->TaskContactList->findByContactListId($contactList['ContactList']['id']);
                    if (empty($tasksContactsLists)) {
                        $contactsLists[$key]['Tasks'] = ConstantsBooleans::YES;
                    } else {
                        $contactsLists[$key]['Tasks'] = ConstantsBooleans::NO;
                    }
                }
            }

            $this->set(
                array(
                    'contacts_lists' => $contactsLists
                )
            );

            $this->layout = null;
            $this->render('../ContactsLists/Elements/ajax_home');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete contact list AJAX.
     */
    public function delete_contact_list($contact_list_id)
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT_LIST) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS_LIST)
        ) {
            $this->autoRender = false;
            $this->layout = null;

            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $taskContactList =  $this->TaskContactList->findAllByContactListId($contact_list_id);
            $appointmentContactList =  $this->AppointmentContactList->findAllByContactListId($contact_list_id);

            $contactsLists = $this->ContactList->find('all', array(
                'conditions' => array(
                    'aag_region_id' => $aagRegionId,
                    'OR' => array(
                        'user_id' => CakeSession::read('Auth.User.id'),
                        'global' => ConstantsBooleans::YES
                    ),
                )
            ));

            foreach ($contactsLists as $key => $contactList) {
                $contactsLists[$key]['Contacts'] = $this->ContactList->getContactsByContactListAndUser($contactList['ContactList']['id']);
                $tasksContactsLists = $this->TaskContactList->findByContactListId($contactList['ContactList']['id']);
                if (empty($tasksContactsLists)) {
                    $contactsLists[$key]['Tasks'] = ConstantsBooleans::YES;
                } else {
                    $contactsLists[$key]['Tasks'] = ConstantsBooleans::NO;
                }
            }

            if (empty($taskContactList) && empty($appointmentContactList)) {
                $data = $this->ContactContactList->findAllByContactListId($contact_list_id);
                foreach ($data as $item) {
                    $this->ContactContactList->delete($item['ContactContactList']['id']);
                }

                if ($this->ContactList->delete($contact_list_id)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return 2;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit contact list AJAX.
     */
    public function save_contact_list_ajax($contact_list_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $contactList = $this->ContactList->findByIdAndAagRegionId($contact_list_id, $aagRegionId);

        if (
            $contactList &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT_LIST) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS_LIST)
        ) {
            $contacts = $this->request->data['contacts'];
            $contactListName = $this->request->data['name'];
            $color = $this->request->data['color'];
            if ($this->request->data['global'] == 'false') {
                $global = ConstantsBooleans::NO;
            } else {
                $global = ConstantsBooleans::YES;
            }

            $dataName = array(
                'id' => $contact_list_id,
                'name' => $contactListName,
                'global' => $global,
                'color' => $color
            );
            $this->ContactList->save($dataName);
            $contacts = array_unique($contacts);
            $currentContacts = $this->ContactContactList->find('all', array(
                'conditions' => array(
                    'contact_list_id' => $contact_list_id
                )
            ));

            foreach ($currentContacts as $contact) {
                if (!in_array($contact['ContactContactList']['contact_id'], $contacts)) {
                    $this->ContactContactList->delete($contact['ContactContactList']['id']);
                } else {
                    unset($contacts[array_search($contact['ContactContactList']['contact_id'], $contacts)]);
                }
            }

            foreach ($contacts as $contact) {
                $data = array(
                    'ContactContactList' => array(
                        'contact_id' => $contact,
                        'contact_list_id' => $contact_list_id,
                    )
                );
                $this->ContactContactList->create();

                $this->ContactContactList->save($data);
            }
            $this->autoRender = null;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create contact list AJAX.
     */
    public function create_contact_list_ajax()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT_LIST) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS_LIST)
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $contacts = $this->request->data['contacts'];
            $contactListName = $this->request->data['name'];
            $color = $this->request->data['color'];
            if ($this->request->data['global'] == 'false') {
                $global = ConstantsBooleans::NO;
            } else {
                $global = ConstantsBooleans::YES;
            }
            $dataName = array(
                'name' => $contactListName,
                'user_id' => CakeSession::read('Auth.User.id'),
                'global' => $global,
                'color' => $color,
                'aag_region_id' => $aagRegionId
            );

            $this->ContactList->new_contact_list($dataName);
            $contactList_id = $this->ContactList->getLastInsertID();
            $contacts = array_unique($contacts);

            foreach ($contacts as $contact) {
                $data = array(
                    'ContactContactList' => array(
                        'contact_id' => $contact,
                        'contact_list_id' => $contactList_id
                    )
                );
                $this->ContactContactList->new_contact_contact_list($data);
            }

            $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
            $this->autoRender = null;
        } else {
            throw new UnauthorizedException();
        }
    }
}
