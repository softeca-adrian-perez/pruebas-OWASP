<?php

class CommunicationSectionPosition extends AppModel{
    public $useTable = 'communications_sections_positions';

    public function add( $communication_section_id , $position_id ){

        $fields = array(
            'CommunicationSectionPosition' => array(
                'communication_section_id',
                'position_id',
            )
        );
        $communication_section_position['CommunicationSectionPosition']['communication_section_id'] = $communication_section_id;
        $communication_section_position['CommunicationSectionPosition']['position_id'] = $position_id;

        $this->create();
        if(!$this->guardar( $communication_section_position, $fields )){
            return false;
        }

        return true;
    }

    public function getPositionsByCommunicationSectionId( $communication_section_id ){
        return $this->find('all',
            array(
                'conditions' => array(
                    'communication_section_id' => $communication_section_id
                ),
                'fields' => array(
                    'CommunicationSectionPosition.*',
                )
            )
        );
    }

    public function getListPositionsByCommunicationSectionId( $communication_section_id ){
        return $this->find('list',
            array(
                'conditions' => array(
                    'communication_section_id' => $communication_section_id
                ),
                'fields' => array(
                    'CommunicationSectionPosition.id',
                    'CommunicationSectionPosition.position_id',
                )
            )
        );
    }

}