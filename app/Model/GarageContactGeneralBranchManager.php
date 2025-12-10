<?php

class GarageContactGeneralBranchManager extends AppModel{
    public $useTable = 'garages_contacts_general_branch_manager';

    public function getContactsByGarageId( $garage_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = GarageContactGeneralBranchManager.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageContactGeneralBranchManager.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Contact.*',
                ),
            )
        );
    }

    public function getContactsByGarageIdLimited( $garage_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = GarageContactGeneralBranchManager.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageContactGeneralBranchManager.garage_id' => $garage_id,
                ),
                'limit' => 10,
                'fields' => array(
                    'Contact.*',
                ),
            )
        );
    }

    public function new_garage_contact_general_branch_manager( $garage_contact_general_branch_manager ){
        $fields = array(
            'GarageContactGeneralBranchManager' => array(
                'garage_id',
                'contact_id'
            )
        );
        $this->create();
        $garage_contact_general_branch_manager_bd = $this->guardar($garage_contact_general_branch_manager, $fields);
        if ( !$garage_contact_general_branch_manager_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    // public function findManagerByGarage( $garage_id , $position_id ){
    //     return $this->find(
    //         'all',
    //         array(
    //             'joins' => array(
    //                 array(
    //                     'alias' => 'Contact',
    //                     'table' => 'contacts',
    //                     'type' => 'INNER',
    //                     'conditions' => array(
    //                         'Contact.id = GarageContactGeneralBranchManager.contact_id',
    //                     ),
    //                 ),
    //             ),
    //             'conditions' => array(
    //                 'GarageContactGeneralBranchManager.garage_id' => $garage_id,
    //                 'Contact.position_id' => $position_id,
    //             ),
    //             'fields' => array(
    //                 'Contact.*'
    //             ),
    //         )
    //     );
    // }

    public function findManagerByGarageAndRole( $garage_id , $role_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = GarageContactGeneralBranchManager.contact_id',
                        ),
                    ),
                    array(
                        'alias' => 'Position',
                        'table' => 'positions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Position.id = Contact.position_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageContactGeneralBranchManager.garage_id' => $garage_id,
                    'Position.role_id' => $role_id,
                ),
                'fields' => array(
                    'Contact.*'
                ),
            )
        );
    }

}