<?php

class DistributorContactStaff extends AppModel{
    public $useTable = 'distributors_contacts_staff';

    public function getContactsByDistributorId( $distributor_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactStaff.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorContactStaff.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'Contact.*',
                ),
            )
        );
    }

    public function getContactsByDistributorIdLimited( $distributor_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactStaff.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorContactStaff.distributor_id' => $distributor_id,
                ),
                'limit' => 10,
                'fields' => array(
                    'Contact.*',
                ),
            )
        );
    }

    public function new_distributor_contact_staff( $distributor_contact_staff ){
        $fields = array(
            'DistributorContactStaff' => array(
                'distributor_id',
                'contact_id'
            )
        );
        $this->create();
        $distributor_contact_staff_bd = $this->guardar($distributor_contact_staff, $fields);
        if ( !$distributor_contact_staff_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    public function getByDistributorAndPosition( $distributor_id ){
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactStaff.contact_id',
                        ),
                    ),
                    array(
                        'alias' => 'Position',
                        'table' => 'positions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.position_id = Position.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorContactStaff.distributor_id' => $distributor_id,
                    'Position.role_id' => ConstantsRoles::DISTRIBUTOR,
                ),
                'fields' => array(
                    'DistributorContactStaff.*',
                    'Contact.*'
                ),
            )
        );
    }

    public function findContactsStaffExport($distributor_id){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactStaff.contact_id',
                        ),
                    ),
                    array(
                        'alias' => 'ContactTitle',
                        'table' => 'contacts_titles',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ContactTitle.id = Contact.title_id',
                        ),
                    ),
                    array(
                        'alias' => 'Position',
                        'table' => 'positions',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Position.id = Contact.position_id',
                        ),
                    ),
                ),
                'group' => array(
                    'Contact.id'
                ),
                'conditions' => array(
                    'DistributorContactStaff.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'Contact.*',
                    'ContactTitle.name',
                    'Position.name_' . __l(),
                ),
            )
        );
    }

}