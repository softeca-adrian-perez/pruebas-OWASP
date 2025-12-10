<?php

class NetworkContactList extends AppModel{
    
    public $useTable = 'networks_contacts_lists';

    public function add($network_id, $network_contact_list ){
        $fields = array(
            'NetworkContactList' => array(
                'network_id',
                'contact_list_id',
            )
        );

        $this->create();
        $network_contact_list_bd = $this->guardar($garage_contact_list, $fields);
        if ( !$network_contact_list_bd ){
            return false;
        }
        return true;
    }

    public function edit($network_id, $contact_list_id){

        $network_contact_list = array();

        $fields = array(
            'NetworkContactList' => array(
                'network_id',
                'contact_list_id',
            )
        );

        $network_contact_list['NetworkContactList']['network_id'] = $network_id;
        $network_contact_list['NetworkContactList']['contact_list_id'] = $contact_list_id;

        $network_contact_list_bd = $this->guardar($network_contact_list, $fields);
        if ( !$network_contact_list_bd ){
            return false;
        }
        return true;
    }

    public function getContactListFromNetwork( $network_id ){
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'NetworkContactList.contact_list_id'
                ),
                'conditions' => array(
                    'NetworkContactList.network_id' => $network_id
                ),
            )
        );
    }

}