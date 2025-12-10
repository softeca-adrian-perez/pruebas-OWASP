<?php

class ContactRegion extends AppModel{
    public $useTable = 'contacts_regions';

    public function getBdmPerRegion( $region_code ){
        $this->Position = ClassRegistry::init('Position');
        $positions = $this->Position->getListPositionByRole( array(ConstantsRoles::BDM_AAG,ConstantsRoles::BDM_TG) ); 
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ContactRegion.contact_id = Contact.id',
                        ),
                    ),
                    array(
                        'alias' => 'Region',
                        'table' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ContactRegion.region_id = Region.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Region.code' => $region_code,
                    'Contact.position_id' => $positions,
                ),
                'fields' => array(
                    'Contact.id',
                    'Contact.identification_number',
                ),
            )
        );
    }
    
    public function getBdmIdPerRegion( $region_code ){
        $this->Position = ClassRegistry::init('Position');
        $positions = $this->Position->getListPositionByRole( array(ConstantsRoles::BDM_AAG,ConstantsRoles::BDM_TG) ); 
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ContactRegion.contact_id = Contact.id',
                        ),
                    ),
                    array(
                        'alias' => 'Region',
                        'table' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ContactRegion.region_id = Region.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Region.code' => $region_code,
                    'Contact.position_id' => $positions,
                ),
                'fields' => array(
                    'Contact.id',
                ),
            )
        );
    }

    public function getListByRegion( $regions ){
        return $this->find('list', array(
            'conditions' => array(
                'region_id' => $regions
            ),
            'group' => array('contact_id'),
            'fields' => array(
                'id',
                'contact_id'
            )
        ));
    }

    public function findListByContact( $contact_id){
        return $this->find('list',array(
            'conditions' => array(
                'contact_id' => $contact_id
            ),
            'fields' => array(
                'id',
                'region_id'
            )
        ));
    }

}