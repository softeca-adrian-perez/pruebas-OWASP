<?php

class NetworkContactBdm extends AppModel{
    public $useTable = 'networks_contacts_bdm';

    public function add($network_id, $contact_id) {
        $fields = array(
            'NetworkContactBdm' => array(
                'network_id',
                'contact_id',
            )
        );

        $network_contact['NetworkContactBdm']['network_id'] = $network_id;
        $network_contact['NetworkContactBdm']['contact_id'] = $contact_id;

        $this->create();
        if(!$this->save($network_contact, $fields)) {
            return false;
        }

        return true;
    }

    public function findListByContactAndDistributorId( $contact_id , $distributor_id ){
        return $this->find('list',array(
            'conditions' => array(
                'contact_id' => $contact_id,
                'distributor_id' => $distributor_id
            ),
            'fields' => array(
                'id',
                'network_id'
            )
        ));
    }

    public function findListByContactAndGarageId( $contact_id , $garage_id ){
        return $this->find('list',array(
            'conditions' => array(
                'contact_id' => $contact_id,
                'garage_id' => $garage_id
            ),
            'fields' => array(
                'id',
                'network_id'
            )
        ));
    }

    public function findAllNetworksByGarageIdAndContactId( $garage_id , $contact_id ){
        return $this->find('all',array(
            'joins' => array(
                array(
                    'alias' => 'Network',
                    'table' => 'networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Network.id = NetworkContactBdm.network_id',
                    ),
                ),
            ),
            'conditions' => array(
                'contact_id' => $contact_id,
                'garage_id' => $garage_id,
            ),
            'fields' => array(
                'Network.*'
            )
        ));
    }

    public function findAllByContact( $contact_id ){
        return $this->find('all',array(
            'conditions' => array(
                'contact_id' => $contact_id,
            ),
            'fields' => array(
                'NetworkContactBdm.*',
            ),
            'group' => array(
                'NetworkContactBdm.network_id'
            ),
        ));
    }

    public function getNetworksByContactId( $contact_id ){
        return $this->find('all',array(
            'joins' => array(
                array(
                    'alias' => 'Network',
                    'table' => 'networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Network.id = NetworkContactBdm.network_id',
                    ),
                ),
            ),
            'conditions' => array(
                'contact_id' => $contact_id,
            ),
            'fields' => array(
                'Network.*'
            ),
            'group' => 'Network.id'
        ));
    }

}