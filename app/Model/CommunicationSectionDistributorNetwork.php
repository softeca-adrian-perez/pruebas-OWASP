<?php

class CommunicationSectionDistributorNetwork extends AppModel{
    public $useTable = 'communications_sections_distributors_networks';

    public function add( $communication_section_id , $distributor_network_id ){

        $fields = array(
            'CommunicationSectionDistributorNetwork' => array(
                'communication_section_id',
                'distributor_network_id',
            )
        );
        $communication_section_distributor_network['CommunicationSectionDistributorNetwork']['communication_section_id'] = $communication_section_id;
        $communication_section_distributor_network['CommunicationSectionDistributorNetwork']['distributor_network_id'] = $distributor_network_id;

        $this->create();
        if(!$this->guardar( $communication_section_distributor_network, $fields )){
            return false;
        }

        return true;
    }

    public function getCommunicationsSectionsDistributorsNetworksByCommunicationId( $communication_section_id ){
        return $this->find('list',
        array(
            'joins' => array(
                array(
                    'alias' => 'DistributorNetwork',
                    'table' => 'distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorNetwork.id = CommunicationSectionDistributorNetwork.distributor_network_id'
                    )
                )
            ),
            'conditions' => array(
                'communication_section_id' => $communication_section_id
            ),
            'fields' => array(
                'DistributorNetwork.id',
                'DistributorNetwork.name'
            )
        )
        );
    }


}