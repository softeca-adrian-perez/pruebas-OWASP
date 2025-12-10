<?php

class SearchNetwork extends AppModel{
    public $useTable = 'searches_networks';

    public function new_search_network( $search_network ){
        $fields = array(
            'SearchNetwork' => array(
                'search_id',
                'network_id'
            )
        );

        $this->create();
        $search_network_bd = $this->guardar($search_network, $fields);
        if ( !$search_network_bd ){
            return false;
        }

        $this->commit();
        return true;
    }
}