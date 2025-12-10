<?php

class SearchDistributor extends AppModel{
    public $useTable = 'searches_distributors';

    public function new_search_distributor( $search_distributor ){
        $fields = array(
            'SearchDistributor' => array(
                'search_id',
                'distributor_id'
            )
        );

        $this->create();
        $search_distributor_bd = $this->guardar($search_distributor, $fields);
        if ( !$search_distributor_bd ){
            return false;
        }

        $this->commit();
        return true;
    }
}