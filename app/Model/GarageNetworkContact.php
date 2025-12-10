<?php

class GarageNetworkContact extends AppModel{
    public $useTable = 'garages_networks_contacts';
    public $belongsTo = array(
        'GarageNetwork' => array(
            'className' => 'GarageNetwork',
            'foreignKey' => 'garage_network_id',
        ),
        'Contact' => array(
            'className' => 'Contact',
            'foreignKey' => 'contact_id',
        )
    );

    public function saveContactData($data_contacts) {
        return $this->saveMany($data_contacts);
    }

    public function deleteContactByGarageNetwork($garage_network_id) {
        return $this->deleteAll(array('garage_network_id' => $garage_network_id), false);
    }


}
