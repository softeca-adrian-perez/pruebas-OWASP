<?php

class DistributorNetworkContactBdm extends AppModel{
    public $useTable = 'distributors_networks_contacts_bdm';

    public function add($distributor_network_id, $contact_id) {
        $fields = array(
            'DistributorNetworkContactBdm' => array(
                'distributor_network_id',
                'contact_id',
            )
        );

        $distributor_network_contact['DistributorNetworkContactBdm']['distributor_network_id'] = $distributor_network_id;
        $distributor_network_contact['DistributorNetworkContactBdm']['contact_id'] = $contact_id;

        $this->create();
        if(!$this->save($distributor_network_contact, $fields)) {
            return false;
        }

        return true;
    }

    public function findListByContactAndDistributorId( $contact_id , $distributor_id ){
        return $this->find('list',array(
            'conditions' => array(
                'contact_id' => $contact_id,
                'distributor_id' => $distributor_id,
            ),
            'fields' => array(
                'id',
                'distributor_network_id'
            )
        ));
    }

    public function findAllNetworksByDistributorIdAndContactId( $distributor_id , $contact_id ){
        return $this->find('all',array(
            'joins' => array(
                array(
                    'alias' => 'DistributorNetwork',
                    'table' => 'distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorNetwork.id = DistributorNetworkContactBdm.distributor_network_id',
                    ),
                ),
            ),
            'conditions' => array(
                'contact_id' => $contact_id,
                'distributor_id' => $distributor_id,
            ),
            'fields' => array(
                'DistributorNetwork.*'
            )
        ));
    }

    public function findAllByContact( $contact_id ){
        return $this->find('all',array(
            'conditions' => array(
                'contact_id' => $contact_id,
            ),
            'fields' => array(
                'DistributorNetworkContactBdm.*',
            ),
            'group' => array(
                'DistributorNetworkContactBdm.distributor_network_id'
            ),
        ));
    }


}