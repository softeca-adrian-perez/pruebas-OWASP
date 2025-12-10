<?php

class GarageContactList extends AppModel{
    public $useTable = 'garages_contacts_lists';

    public function add( $garage_contact_list ){
        $fields = array(
            'GarageContactList' => array(
                'garage_id',
                'contact_list_id',
            )
        );
        $this->create();
        $garage_contact_list_bd = $this->guardar($garage_contact_list, $fields);
        if ( !$garage_contact_list_bd ){
            return false;
        }
        return true;
    }
}