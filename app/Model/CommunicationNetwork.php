<?php

class CommunicationNetwork extends AppModel{
    public $useTable = 'communications_networks';

    public function add( $communication_id , $network_id ){

        $fields = array(
            'CommunicationNetwork' => array(
                'communication_id',
                'network_id',
            )
        );
        $communication_network['CommunicationNetwork']['communication_id'] = $communication_id;
        $communication_network['CommunicationNetwork']['network_id'] = $network_id;

        $this->create();
        if(!$this->guardar( $communication_network, $fields )){
            return false;
        }

        return true;
    }

    public function getListByCommunicationId( $communication_id ){
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = CommunicationNetwork.network_id'
                        )
                    )
                ),
                'conditions' => array(
                    'communication_id' => $communication_id
                ),
                'fields' => array(
                    'id',
                    'Network.name'
                )
            )
        );
    }

    public function getCommunicationsNetworksByCommunicationId( $communication_id ){
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = CommunicationNetwork.network_id'
                        )
                    )
                ),
                'conditions' => array(
                    'communication_id' => $communication_id
                ),
                'fields' => array(
                    'network_id',
                    'Network.name'
                )
            )
        );
    }
}