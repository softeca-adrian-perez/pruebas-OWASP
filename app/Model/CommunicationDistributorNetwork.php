<?php

class CommunicationDistributorNetwork extends AppModel{
    public $useTable = 'communications_distributors_networks';

    public function add( $communication_id , $distributor_network_id ){

        $fields = array(
            'CommunicationDistributorNetwork' => array(
                'communication_id',
                'distributor_network_id',
            )
        );
        $communication_distributor_network['CommunicationDistributorNetwork']['communication_id'] = $communication_id;
        $communication_distributor_network['CommunicationDistributorNetwork']['distributor_network_id'] = $distributor_network_id;

        $this->create();
        if(!$this->guardar( $communication_distributor_network, $fields )){
            return false;
        }

        return true;
    }

    public function getCommunicationsDistributorsNetworksByCommunicationId( $communication_id ){
        return $this->find('list',
        array(
            'joins' => array(
                array(
                    'alias' => 'DistributorNetwork',
                    'table' => 'distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorNetwork.id = CommunicationDistributorNetwork.distributor_network_id'
                    )
                )
            ),
            'conditions' => array(
                'communication_id' => $communication_id
            ),
            'fields' => array(
                'DistributorNetwork.id',
                'DistributorNetwork.name'
            )
        )
        );
    }
}