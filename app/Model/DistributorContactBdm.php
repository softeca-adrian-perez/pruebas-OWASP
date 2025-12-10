<?php

class DistributorContactBdm extends AppModel
{
    public $useTable = 'distributors_contacts_bdm';

    public function getContactsByDistributorId($distributor_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactBdm.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorContactBdm.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'Contact.*',
                ),
            )
        );
    }

    public function getContactsByContactAndDistributorAndPosition($contact_id, $distributor_id, $position_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactBdm.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorContactBdm.distributor_id' => $distributor_id,
                    'DistributorContactBdm.contact_id' => $contact_id,
                    'Contact.position_id' => $position_id,
                ),
                'fields' => array(
                    'Contact.*',
                ),
            )
        );
    }

    public function getDistributorsByBDM($contact_id)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'DistributorContactBdm.contact_id' => $contact_id,
                ),
                'fields' => array(
                    'DistributorContactBdm.distributor_id'
                ),
            )
        );
    }

    public function getByDistributorAndPosition($distributor_id)
    {
        $this->Position = ClassRegistry::init('Position');
        $positions = $this->Position->getListPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG));
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactBdm.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorContactBdm.distributor_id' => $distributor_id,
                    'Contact.position_id' => $positions,
                ),
                'fields' => array(
                    'DistributorContactBdm.*',
                    'Contact.*'
                ),
            )
        );
    }

    public function getByDistributorAndPositionBDMTG($distributor_id)
    {
        $this->Position = ClassRegistry::init('Position');
        $positions = $this->Position->getListPositionByRole(array(ConstantsRoles::GPC_LOGISTICS_BDM));
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactBdm.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorContactBdm.distributor_id' => $distributor_id,
                    'Contact.position_id' => $positions,
                ),
                'fields' => array(
                    'DistributorContactBdm.*',
                    'Contact.*'
                ),
            )
        );
    }


    public function getContactsByDistributorIdLimited($distributor_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactBdm.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorContactBdm.distributor_id' => $distributor_id,
                ),
                'limit' => 10,
                'fields' => array(
                    'Contact.*',
                ),
            )
        );
    }

    public function getAllBDMContacts($aag_region_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactBdm.contact_id',
                            'Contact.aag_region_id' => $aag_region_id
                        ),
                    ),
                ),
                'group' => array(
                    'Contact.id'
                ),
                'order' => array(
                    'Contact.first_name'
                ),
                'fields' => array(
                    'Contact.id',
                    'CONCAT(Contact.first_name, " ", Contact.last_name) full_name'
                ),
            )
        );
    }

    public function getBDMByDistributor($distributor_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactBdm.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorContactBdm.distributor_id' => $distributor_id,
                ),
                'group' => array(
                    'Contact.id'
                ),
                'order' => array(
                    'Contact.first_name'
                ),
                'fields' => array(
                    'CONCAT(Contact.first_name, " ", Contact.last_name) full_name'
                ),
            )
        );
    }

    public function findContactsBdmExport($distributor_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactBdm.contact_id',
                        ),
                    ),
                    array(
                        'alias' => 'ContactTitle',
                        'table' => 'contacts_titles',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ContactTitle.id = Contact.title_id',
                        ),
                    ),
                    array(
                        'alias' => 'Position',
                        'table' => 'positions',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Position.id = Contact.position_id',
                        ),
                    ),
                ),
                'group' => array(
                    'DistributorContactBdm.distributor_id'
                ),
                'conditions' => array(
                    'DistributorContactBdm.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'Contact.*',
                    'ContactTitle.name',
                    'Position.name_' . __l(),
                ),
            )
        );
    }


    public function addDistributorContactBdm($distributorId, $contactId)
    {
        $response = $this->findFirstByDistributorIdAndContactId($distributorId, $contactId);
        if ($response) {
            return false;
        }

        $fields = array(
            'DistributorContactBdm' => array(
                'distributor_id',
                'contact_id',
                'created_at',
                'updated_at'
            )
        );

        $values = array(
            'DistributorContactBdm' => array(
                'distributor_id' => $distributorId,
                'contact_id' => $contactId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            )
        );

        $this->create();
        $result = $this->guardar($values, $fields);
        if (!$result) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function getContactsBdmAjax($conditions, $aag_region_id)
    {
        $contactsBdm = $this->getContactsBdmQuery($conditions, $aag_region_id);
        $contactsBdm = Hash::combine($contactsBdm, '{n}.Contact.id', array('%s', '{n}.0.full_name'));

        return $contactsBdm;
    }

    public function getContactsBdmQuery($conditions_ajax = array(), $aag_region_id)
    {
        $conditions_region = array('Contact.aag_region_id' => $aag_region_id);
        
        if (isset($conditions_ajax['name']) && !empty($conditions_ajax['name'])) {
            $conditions_ajax = array(
                'OR' => array(
                    'Contact.first_name LIKE' => '%' . $conditions_ajax['name'] . '%',
                    'Contact.last_name LIKE' => '%' . $conditions_ajax['name'] . '%'
                )
            );
        }

        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = DistributorContactBdm.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    $conditions_ajax,
                    $conditions_region,
                ),
                'fields' => array(
                    'Contact.id',
                    'CONCAT(Contact.first_name, " ", Contact.last_name) full_name'
                ),
                'group' => array(
                    'Contact.id'
                ),
                'order' => array(
                    'Contact.first_name'
                ),
            )
        );
	}
}
