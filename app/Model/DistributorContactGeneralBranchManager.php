<?php

class DistributorContactGeneralBranchManager extends AppModel{
    public $useTable = 'distributors_contacts_general_branch_manager';

    public function getContactsByDistributorId( $distributor_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactGeneralBranchManager.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorContactGeneralBranchManager.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'Contact.*',
                ),
            )
        );
    }

    public function getContactsByDistributorIdLimited( $distributor_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactGeneralBranchManager.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorContactGeneralBranchManager.distributor_id' => $distributor_id,
                ),
                'limit' => 10,
                'fields' => array(
                    'Contact.*',
                ),
            )
        );
    }

    public function new_distributor_contact_general_branch_manager( $distributor_contact_general_branch_manager ){
        $fields = array(
            'DistributorContactGeneralBranchManager' => array(
                'distributor_id',
                'contact_id'
            )
        );
        $this->create();
        $distributor_contact_general_branch_manager_bd = $this->guardar($distributor_contact_general_branch_manager, $fields);
        if ( !$distributor_contact_general_branch_manager_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    // public function findManagerByDistributor( $distributor_id , $position_id ){
    //     return $this->find(
    //         'all',
    //         array(
    //             'joins' => array(
    //                 array(
    //                     'alias' => 'Contact',
    //                     'table' => 'contacts',
    //                     'type' => 'INNER',
    //                     'conditions' => array(
    //                         'Contact.id = DistributorContactGeneralBranchManager.contact_id',
    //                     ),
    //                 ),
    //             ),
    //             'conditions' => array(
    //                 'DistributorContactGeneralBranchManager.distributor_id' => $distributor_id,
    //                 'Contact.position_id' => $position_id,
    //             ),
    //             'fields' => array(
    //                 'Contact.*'
    //             ),
    //         )
    //     );
    // }

    public function findManagerByDistributorAndRole( $distributor_id , $role_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactGeneralBranchManager.contact_id',
                        ),
                    ),
                    array(
                        'alias' => 'Position',
                        'table' => 'positions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.position_id = Position.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorContactGeneralBranchManager.distributor_id' => $distributor_id,
                    'Position.role_id' => $role_id,
                ),
                'fields' => array(
                    'Contact.*'
                ),
            )
        );
    }

}