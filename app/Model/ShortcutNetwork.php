<?php

class ShortcutNetwork extends AppModel{
    public $useTable = 'shortcuts_networks';

    public function add( $shortcut_id , $network_id ){

        $fields = array(
            'ShortcutNetwork' => array(
                'shortcut_id',
                'network_id',
            )
        );

        $shortcut_network['ShortcutNetwork']['shortcut_id'] = $shortcut_id;
        $shortcut_network['ShortcutNetwork']['network_id'] = $network_id;

        $this->create();
        if(!$this->guardar( $shortcut_network, $fields )){
            return false;
        }

        return true;
    }


    public function getShortcutsNetworksByShortcutId( $shortcut_id ){
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = ShortcutNetwork.network_id'
                        )
                    )
                ),
                'conditions' => array(
                    'shortcut_id' => $shortcut_id
                ),
                'fields' => array(
                    'network_id',
                    'Network.name'
                )
            )
        );
    }



}