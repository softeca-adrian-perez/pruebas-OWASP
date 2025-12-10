<?php

class ShortcutPosition extends AppModel{
    public $useTable = 'shortcuts_positions';

    public function add( $shortcut_id , $position_id ){

        $fields = array(
            'ShortcutPosition' => array(
                'shortcut_id',
                'position_id',
            )
        );
        $shortcut_position['ShortcutPosition']['shortcut_id'] = $shortcut_id;
        $shortcut_position['ShortcutPosition']['position_id'] = $position_id;

        $this->create();
        if(!$this->guardar( $shortcut_position, $fields )){
            return false;
        }

        return true;
    }

    // public function getPositionsByCommunicationId( $communication_id ){
    //     return $this->find('all',
    //         array(
    //             'conditions' => array(
    //                 'communication_id' => $communication_id
    //             ),
    //             'fields' => array(
    //                 'CommunicationPosition.*',
    //             )
    //         )
    //     );
    // }

    public function getListPositionsByShortcutId( $shortcut_id ){
        return $this->find('list',
            array(
                'conditions' => array(
                    'shortcut_id' => $shortcut_id
                ),
                'fields' => array(
                    'ShortcutPosition.id',
                    'ShortcutPosition.position_id',
                )
            )
        );
    }
}