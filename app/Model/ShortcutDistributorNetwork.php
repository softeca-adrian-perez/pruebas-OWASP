<?php

class ShortcutDistributorNetwork extends AppModel{
    public $useTable = 'shortcuts_distributors_networks';

    public function add( $shortcut_id , $distributor_network_id ){

        $fields = array(
            'ShortcutDistributorNetwork' => array(
                'shortcut_id',
                'distributor_network_id',
            )
        );
        $shortcut_distributor_network['ShortcutDistributorNetwork']['shortcut_id'] = $shortcut_id;
        $shortcut_distributor_network['ShortcutDistributorNetwork']['distributor_network_id'] = $distributor_network_id;

        $this->create();
        if(!$this->guardar( $shortcut_distributor_network, $fields )){
            return false;
        }

        return true;
    }

    public function getShortcutDistributorsNetworksByShortcutId( $shortcut_id ){
        return $this->find('list',
        array(
            'joins' => array(
                array(
                    'alias' => 'DistributorNetwork',
                    'table' => 'distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorNetwork.id = ShortcutDistributorNetwork.distributor_network_id'
                    )
                )
            ),
            'conditions' => array(
                'shortcut_id' => $shortcut_id
            ),
            'fields' => array(
                'DistributorNetwork.id',
                'DistributorNetwork.name'
            )
        )
        );
    }
}