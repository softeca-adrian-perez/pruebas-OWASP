<?php

class ContactList extends AppModel{
    public $useTable = 'contacts_lists';

    public $validate = array(
        'name' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function new_contact_list( $contact_list ){
        $fields = array(
            'ContactList' => array(
                'name',
                'user_id',
                'color',
                'global',
                'aag_region_id',
            )
        );

        $contact_list_bd = $this->guardar($contact_list, $fields);

        if ( !$contact_list_bd ){
            return false;
        }

        return true;
    }

    public function getContactsByContactListAndUser( $contact_list_id ){
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'ContactContactList',
                    'table' => 'contacts_contacts_lists',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ContactContactList.contact_list_id = ContactList.id',
                    ),
                ),
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ContactContactList.contact_id = Contact.id',
                    ),
                ),
            ),
            'conditions' => array(
                'contact_list_id' => $contact_list_id
            ),
            'fields' => array(
                'Contact.*'
            ),
            'order' => array(
                'Contact.first_name',
            )
        ));
    }

    public function getContactList($aag_region_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'ContactList.aag_region_id' => $aag_region_id,
                'OR' => array(
                    'user_id' => CakeSession::read('Auth.User.id'),
                    'global' => ConstantsBooleans::YES
                ),
            )
        ));
    }

    public function getContactsByContactList( $name, $aag_region_id  ){
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'ContactContactList',
                    'table' => 'contacts_contacts_lists',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ContactList.id = ContactContactList.contact_list_id',
                        'ContactList.aag_region_id' => $aag_region_id
                    ),
                ),
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.id = ContactContactList.contact_id',
                    ),
                ),
            ),
            'conditions' => array(
                'CONCAT(Contact.first_name, " ", Contact.last_name) LIKE' => '%' . $name . '%'
            ),
            'fields' => array(
                'Contact.*',
                'ContactList.*'
            )
        ));
    }

    public function getAllContactListWithUser(){
        return $this->find('list',array(
            'joins' => array(
                array(
                    'alias' => 'ContactContactList',
                    'table' => 'contacts_contacts_lists',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ContactList.id = ContactContactList.contact_list_id',
                    ),
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.contact_id = ContactContactList.contact_id',
                    ),
                ),
            ),
            'fields' => array(
                'ContactList.id',
                'ContactList.name'
            )
        ));
    }

}