<?php

class CommunicationPosition extends AppModel{
    public $useTable = 'communications_positions';

    public function add( $communication_id , $position_id ){

        $fields = array(
            'CommunicationPosition' => array(
                'communication_id',
                'position_id',
            )
        );
        $communication_position['CommunicationPosition']['communication_id'] = $communication_id;
        $communication_position['CommunicationPosition']['position_id'] = $position_id;

        $this->create();
        if(!$this->guardar( $communication_position, $fields )){
            return false;
        }

        return true;
    }

    public function getPositionsByCommunicationId( $communication_id ){
        return $this->find('all',
            array(
                'conditions' => array(
                    'communication_id' => $communication_id
                ),
                'fields' => array(
                    'CommunicationPosition.*',
                )
            )
        );
    }
}