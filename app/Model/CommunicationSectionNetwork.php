<?php

class CommunicationSectionNetwork extends AppModel{
    public $useTable = 'communications_sections_networks';

    public function add( $communication_section_id , $network_id ){

        $fields = array(
            'CommunicationSectionNetwork' => array(
                'communication_section_id',
                'network_id',
            )
        );
        $communication_section_network['CommunicationSectionNetwork']['communication_section_id'] = $communication_section_id;
        $communication_section_network['CommunicationSectionNetwork']['network_id'] = $network_id;

        $this->create();
        if(!$this->guardar( $communication_section_network, $fields )){
            return false;
        }

        return true;
    }

    public function getCommunicationsSectionsNetworksByCommunicationId( $communication_section_id ){
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = CommunicationSectionNetwork.network_id'
                        )
                    )
                ),
                'conditions' => array(
                    'communication_section_id' => $communication_section_id
                ),
                'fields' => array(
                    'CommunicationSectionNetwork.network_id',
                    'Network.name'
                )
            )
        );
    }


}