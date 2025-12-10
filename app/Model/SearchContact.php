<?php

class SearchContact extends AppModel{
    public $useTable = 'searches_contacts';

    public function new_search_contact( $search_contact ){
        $fields = array(
            'SearchContact' => array(
                'search_id',
                'contact_id'
            )
        );

        $this->create();
        $search_contact_bd = $this->guardar($search_contact, $fields);
        if ( !$search_contact_bd ){
            return false;
        }

        $this->commit();
        return true;
    }
}