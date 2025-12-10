<?php
class Contact extends AppModel
{
    public $useTable = 'contacts';
    public $displayField = 'full_name';

    var $virtualFields = array(
        'full_name' => 'CONCAT(Contact.first_name, " ", Contact.last_name)'
    );

    public $belongsTo = array(
        'ContactTitle',
        'Position',
    );

    public $hasMany = array(
        'GarageContactBdm',
        'GarageContactStaff',
        'GarageContactGeneralBranchManager',
        'DistributorContactBdm',
        'DistributorContactStaff',
        'DistributorContactGeneralBranchManager',
        'GarageNetworkContact' => array(
            'className' => 'GarageNetworkContact',
            'foreignKey' => 'contact_id',
        ),
    );

    public $hasOne = array(
        'AagRegion',
    );

    private $_queries = array(
        'search_staff' => array(
            'joins' => array(
                array(
                    'alias' => 'Position',
                    'table' => 'positions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.position_id = Position.id',
                    ),
                ),
                array(
                    'alias' => 'Role',
                    'table' => 'roles',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Position.role_id = Role.id',
                    ),
                ),
            ),
            'conditions' => array(
                'Role.id' => ConstantsRoles::GENERIC_STAFF
            ),
            'fields' => array(
                'Contact.*',
            ),
            'order' => 'Contact.first_name asc, Contact.last_name asc'
        ),
        'search_general_branch_manager_garage' => array(
            'joins' => array(
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
                'Or' => array(
                    array(
                        'Contact.position_id' => ConstantsPositions::GENERAL_BRANCH_MANAGER_ID
                    ),
                    array(
                        'Position.role_id' => ConstantsRoles::GARAGE
                    ),
                ),
            ),
            'fields' => array(
                'Contact.*',
            ),
        ),
        'search_general_branch_manager_distributor' => array(
            'joins' => array(
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
                'Or' => array(
                    array(
                        'Contact.position_id' => ConstantsPositions::GENERAL_BRANCH_MANAGER_ID
                    ),
                    array(
                        'Position.role_id' => ConstantsRoles::DISTRIBUTOR
                    ),
                ),
            ),
            'fields' => array(
                'Contact.*',
            ),
            'order' => 'Contact.first_name asc, Contact.last_name asc'
        ),
        'my_contacts_bdm_garages' => array(
            'joins' => array(
                array(
                    'alias' => 'GarageContactBdm',
                    'table' => 'garages_contacts_bdm',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.id = GarageContactBdm.contact_id',
                    ),
                ),
            ),
            'fields' => array(
                'Contact.*',
                'GarageContactBdm.*'
            ),
        ),
        'my_contacts_staff_garages' => array(
            'joins' => array(
                array(
                    'alias' => 'GarageContactStaff',
                    'table' => 'garages_contacts_staff',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.id = GarageContactStaff.contact_id',
                    ),
                ),
            ),
            'fields' => array(
                'Contact.*',
                'GarageContactStaff.*'
            ),
            'order' => 'Contact.first_name asc, Contact.last_name asc'
        ),
        'my_contacts_general_branch_manager_garages' => array(
            'joins' => array(
                array(
                    'alias' => 'GarageContactGeneralBranchManager',
                    'table' => 'garages_contacts_general_branch_manager',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.id = GarageContactGeneralBranchManager.contact_id',
                    ),
                ),
            ),
            'fields' => array(
                'Contact.*'
            ),
        ),
        'my_contacts_bdm_distributors' => array(
            'joins' => array(
                array(
                    'alias' => 'DistributorContactBdm',
                    'table' => 'distributors_contacts_bdm',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.id = DistributorContactBdm.contact_id',
                    ),
                ),
            ),
            'fields' => array(
                'Contact.*'
            ),
            'order' => 'Contact.first_name asc'
        ),
        'my_contacts_staff_distributors' => array(
            'joins' => array(
                array(
                    'alias' => 'DistributorContactStaff',
                    'table' => 'distributors_contacts_staff',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.id = DistributorContactStaff.contact_id',
                    ),
                ),
            ),
            'fields' => array(
                'Contact.*'
            ),
            'order' => 'Contact.first_name asc'
        ),
        'my_contacts_general_branch_manager_distributors' => array(
            'joins' => array(
                array(
                    'alias' => 'DistributorContactGeneralBranchManager',
                    'table' => 'distributors_contacts_general_branch_manager',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.id = DistributorContactGeneralBranchManager.contact_id',
                    ),
                ),
            ),
            'fields' => array(
                'Contact.*'
            ),
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public $validate = array(
        'first_name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_first_name',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'last_name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_last_name',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'identification_number' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_identification_number'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 30),
                'message' => 'Validation.Identification_number_too_long',
            ),
        ),
        'email' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_email'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Email_too_long',
            ),
            'email' => array(
                'rule' => 'email',
                'message' => 'Validation.Email_incorrect_format',
            ),
        ),
        'position_id' =>  array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_position'
        ),
        'phone' => array(
            'numeric' => array(
                'rule' => array('decimal', null, "/^[0-9]+$/"),
                'allowEmpty' => true,
                'message' => 'Validation.Only_numeric',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_PHONE),
                'message' => 'Validation.Phone_too_long',
                'allowEmpty' => true,
            ),
        ),
        'mobile_phone' => array(
            'numeric' => array(
                'rule' => array('decimal', null, "/^[0-9]+$/"),
                'allowEmpty' => true,
                'message' => 'Validation.Only_numeric',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_PHONE),
                'message' => 'Validation.Phone_too_long',
                'allowEmpty' => true,
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['first_name'])) {
            $conditions[] = $this->_conditionFirstName($fields['first_name']);
        }
        if (!empty($fields['last_name'])) {
            $conditions[] = $this->_conditionLastName($fields['last_name']);
        }
        if (!empty($fields['position_id'])) {
            $conditions[] = $this->_conditionPosition($fields['position_id']);
        }
        if (!empty($fields['logistic_center_id'])) {
            $conditions[] = $this->_conditionLogisticCenter($fields['logistic_center_id']);
        }
        if (!empty($fields['phone'])) {
            $conditions[] = $this->_conditionPhone($fields['phone']);
        }
        if (!empty($fields['mobile_phone'])) {
            $conditions[] = $this->_conditionMobilePhone($fields['mobile_phone']);
        }
        if (!empty($fields['email'])) {
            $conditions[] = $this->_conditionEmail($fields['email']);
        }
        if (!empty($fields['identification_number'])) {
            $conditions[] = $this->_conditionIdentificationNumber($fields['identification_number']);
        }
        if (!empty($fields['region_id'])) {
            $conditions[] = $this->_conditionRegion($fields['region_id']);
        }
        if (!empty($fields['aag_region_id'])) {
            $conditions[] = $this->_conditionAagRegion($fields['aag_region_id']);
        }
        return $conditions;
    }

    private function _conditionAagRegion($aag_region_id)
    {
        return array('Contact.aag_region_id' => $aag_region_id);
    }

    private function _conditionRegion($region_id)
    {
        $this->ContactRegion = ClassRegistry::init('ContactRegion');
        $contacts = $this->ContactRegion->findAllByRegionId($region_id);
        return array('Contact.id' => Hash::extract($contacts, '{n}.ContactRegion.contact_id'));
    }

    private function _conditionLogisticCenter($logistic_center_id)
    {
        return array('Contact.logistic_center_id' => $logistic_center_id);
    }

    public function _conditionFirstName($first_name)
    {
        return array('Contact.first_name LIKE' => '%' . $first_name . '%');
    }

    public function _conditionLastName($last_name)
    {
        return array('Contact.last_name LIKE' => '%' . $last_name . '%');
    }

    public function _conditionPosition($position_id)
    {
        return array('Contact.position_id' => $position_id);
    }

    public function _conditionPhone($phone)
    {
        return array('Contact.phone LIKE' => '%' . $phone . '%');
    }

    public function _conditionMobilePhone($mobile_phone)
    {
        return array('Contact.mobile_phone LIKE' => '%' . $mobile_phone . '%');
    }

    public function _conditionEmail($email)
    {
        return array('Contact.email LIKE' => '%' . $email . '%');
    }

    public function _conditionIdentificationNumber($identification_number)
    {
        return array('Contact.identification_number LIKE' => '%' . $identification_number . '%');
    }

    public function new_contact($contact)
    {
        $fields = array(
            'Contact' => array(
                'title_id',
                'contact_id',
                'first_name',
                'last_name',
                'position_id',
                'garage_id',
                'distributor_id',
                'logistic_center_id',
                'phone',
                'mobile_phone',
                'email',
                'identification_number',
                'creation_date',
                'guid',
                'aag_region_id'
            )
        );
        $contact['Contact']['first_name'] = ucwords($contact['Contact']['first_name'], "-");
        $contact['Contact']['creation_date'] = date('Y-m-d H:i:s');

        if ($contact['Contact']['position_id'] == ConstantsPositions::GARAGE_MANAGER_ID && empty($contact['Contact']['garage_id'])) {
            return false;
        }

        $contact['Contact']['guid'] = CakeText::uuid();

        $this->create();
        $contact_bd = $this->guardar($contact, $fields);

        if (!$contact_bd) {
            return false;
        }

        return $contact_bd;
    }

    /**
     * Create a new distributor Contact Staff that comes from UK JSON.
     */
    public function createDistributorContactStaff($contact, $distributor_id, $aagRegionId)
    {
        if ($contact) {
            $name = explode(" ", $contact);
            $exist_contact = $this->findByFirstNameAndLastName($name[0], $name[1]);

            if ($exist_contact) {
                $distributor_contact_tmp = array(
                    'DistributorContactStaff' => array(
                        'distributor_id' => $distributor_id,
                        'contact_id' => $exist_contact['Contact']['id'],
                    )
                );
                $distributor_contact_staff = ClassRegistry::init('DistributorContactStaff');
                $distributor_contact_staff->create();
                $distributor_contact_staff_bd = $distributor_contact_staff->save($distributor_contact_tmp);
                if (!$distributor_contact_staff_bd) {
                    CakeLog::write('updates', 'Error when creating distributor contact' . PHP_EOL);
                }
            } else {
                $last_id = $this->find(
                    'first',
                    array(
                        'order' => 'id DESC',
                        'fields' => 'id'
                    )
                );

                $identification_number = 'UK' . "-" . ($last_id['Contact']['id'] + 1);

                $contact_tmp = array(
                    'Contact' => array(
                        'title_id' => null,
                        'contact_id' => null,
                        'first_name' => $name[0],
                        'last_name' => $name[1],
                        'position_id' => ConstantsPositions::DISTRIBUTOR_MANAGER_ID,
                        'phone' => null,
                        'mobile_phone' => null,
                        'email' => $name[0] . '.' . $name[1] . '@groupauto.co.uk',
                        'identification_number' => $identification_number,
                        'creation_date' => date('Y-m-d H:i:s'),
                        'guid' => CakeText::uuid(),
                        'aag_region_id' => $aagRegionId
                    )
                );

                $this->create();
                $contact_bd = $this->save($contact_tmp);
                if (!$contact_bd) {
                    CakeLog::write('updates', 'Error when creating contact' . PHP_EOL);
                }

                $distributor_contact_tmp = array(
                    'DistributorContactStaff' => array(
                        'distributor_id' => $distributor_id,
                        'contact_id' => $contact_bd['Contact']['id'],
                    )
                );

                $distributor_contact_staff = ClassRegistry::init('DistributorContactStaff');
                $distributor_contact_staff->create();
                $distributor_contact_staff_bd = $distributor_contact_staff->save($distributor_contact_tmp);
                if (!$distributor_contact_staff_bd) {
                    CakeLog::write('updates', 'Error when creating distributor contact' . PHP_EOL);
                }
            }
        }

        if (isset($distributor_contact_staff_bd) && !$distributor_contact_staff_bd) {
            return false;
        }

        return true;
    }

    /**
     * Update distributor Contact Staff that comes from UK JSON.
     */
    public function updateDistributorContactStaff($contact, $distributor_id, $aagRegionId)
    {
        if ($contact) {
            $name = explode(" ", $contact);
            $exist_contact = $this->findByFirstNameAndLastName($name[0], $name[1]);

            if ($exist_contact) {
                $exist_contact_to_garage = $this->DistributorContactStaff->findByContactIdAndDistributorId($exist_contact['Contact']['id'],  $distributor_id);
                if (!$exist_contact_to_garage) {
                    $distributor_contact_staff = ClassRegistry::init('DistributorContactStaff');

                    //Delete the previous registration
                    $previous_contact = $this->DistributorContactStaff->getByDistributorAndPosition($distributor_id);
                    if ($previous_contact) {
                        $distributor_contact_staff->delete($previous_contact['DistributorContactStaff']['id']);
                    }

                    //Insert the new record
                    $distributor_contact_tmp = array(
                        'DistributorContactStaff' => array(
                            'distributor_id' => $distributor_id,
                            'contact_id' => $exist_contact['Contact']['id'],
                        )
                    );

                    $distributor_contact_staff->create();
                    if (!$this->save($distributor_contact_staff->save($distributor_contact_tmp))) {
                        CakeLog::write('updates', 'Creation of distributor contact failure' . PHP_EOL);
                    }
                }
            } else {
                $last_id = $this->find(
                    'first',
                    array(
                        'order' => 'id DESC',
                        'fields' => 'id'
                    )
                );

                $identification_number = 'UK' . "-" . ($last_id['Contact']['id'] + 1);


                $contact_tmp = array(
                    'Contact' => array(
                        'title_id' => null,
                        'contact_id' => null,
                        'first_name' => $name[0],
                        'last_name' => $name[1],
                        'position_id' => ConstantsPositions::DISTRIBUTOR_MANAGER_ID,
                        'phone' => null,
                        'mobile_phone' => null,
                        'email' => $name[0] . '.' . $name[1] . '@groupauto.co.uk',
                        'identification_number' => $identification_number,
                        'creation_date' => date('Y-m-d H:i:s'),
                        'guid' => CakeText::uuid(),
                        'aag_region_id' => $aagRegionId
                    )
                );

                $this->create();
                $contact_bd = $this->save($contact_tmp);

                if (!$contact_bd) {
                    CakeLog::write('updates', 'Creation of contact failure' . PHP_EOL);
                }

                //Delete the previous registration
                $previous_contact = $this->DistributorContactStaff->getByDistributorAndPosition($distributor_id);
                if ($previous_contact) {
                    $this->DistributorContactStaff->delete($previous_contact['DistributorContactStaff']['id']);
                }

                if ($contact_bd) {
                    $distributor_contact_tmp = array(
                        'DistributorContactStaff' => array(
                            'distributor_id' => $distributor_id,
                            'contact_id' => $contact_bd['Contact']['id'],
                        )
                    );

                    $distributor_contact_staff = ClassRegistry::init('DistributorContactStaff');
                    $distributor_contact_staff->create();
                    if (!$distributor_contact_staff->save($distributor_contact_tmp)) {
                        CakeLog::write('updates', 'Creation of distributor contact failure' . PHP_EOL);
                    }
                }
            }
        } else {
            $distributor_contact_staff = ClassRegistry::init('DistributorContactStaff');
            //Delete the previous registration
            $previous_contact = $this->DistributorContactStaff->getByDistributorAndPosition($distributor_id);
            if ($previous_contact) {
                $distributor_contact_staff->delete($previous_contact['DistributorContactStaff']['id']);
            }
        }
    }

    /**
     * Create a new BDM TG that comes from UK JSON.
     */
    public function createDistributorBDMTG($contact, $distributor_id, $aagRegionId)
    {
        if ($contact) {
            $name = explode(" ", $contact);
            $exist_contact = $this->findByFirstNameAndLastName($name[0], $name[1]);

            if ($exist_contact) {
                $distributor_contact_bdm = ClassRegistry::init('DistributorContactBdm');
                $distributor_contact_bdm_bd = $distributor_contact_bdm->addDistributorContactBdm($distributor_id, $exist_contact['Contact']['id']);
                if (!$distributor_contact_bdm_bd) {
                    CakeLog::write('updates', 'Error when creating distributor BDM TG' . PHP_EOL);
                }
            } else {
                $last_id = $this->find(
                    'first',
                    array(
                        'order' => 'id DESC',
                        'fields' => 'id'
                    )
                );

                $identification_number = 'UK' . "-" . ($last_id['Contact']['id'] + 1);

                $contact_tmp = array(
                    'Contact' => array(
                        'title_id' => null,
                        'contact_id' => null,
                        'first_name' => $name[0],
                        'last_name' => $name[1],
                        'position_id' => ConstantsPositions::BDM_TG_ID,
                        'phone' => null,
                        'mobile_phone' => null,
                        'email' => $name[0] . '.' . $name[1] . '@groupauto.co.uk',
                        'identification_number' => $identification_number,
                        'creation_date' => date('Y-m-d H:i:s'),
                        'guid' => CakeText::uuid(),
                        'aag_region_id' => $aagRegionId
                    )
                );

                $this->create();
                $contact_bd = $this->save($contact_tmp);
                if (!$contact_bd) {
                    CakeLog::write('updates', 'Error when creating BDM TG' . PHP_EOL);
                }

                if ($contact_bd) {
                    $distributor_contact_bdm = ClassRegistry::init('DistributorContactBdm');
                    $distributor_contact_bdm_bd = $distributor_contact_bdm->addDistributorContactBdm($distributor_id, $contact_bd['Contact']['id']);

                    if (!$distributor_contact_bdm_bd) {
                        CakeLog::write('updates', 'Error when creating distributor contact BDM TG' . PHP_EOL);
                    }

                    $user_tmp = array(
                        'User' => array(
                            'name' => $name[0],
                            'surname' => $name[1],
                            'username' => $name[0] . $name[1],
                            'password' => $name[0] . $name[1],
                            'contact_id' => $distributor_contact_bdm_bd['DistributorContactBdm']['contact_id'],
                            'role_id' => ConstantsRoles::BDM_TG,
                            'language_id' => ConstantsLanguages::ENGLISH,
                            'active' => ConstantsBooleans::ACTIVE,
                            'creation_date' => date('Y-m-d H:i:s'),
                            'guid' => CakeText::uuid(),
                            'aag_region_id' => $aagRegionId,
                            'country_id' => ConstantsCountries::UNITED_KINGDOM
                        )
                    );
                    $user_bdm = ClassRegistry::init('User');

                    $user_bdm->create();
                    $user_bdm->validator()->remove('password');
                    if (!$user_bdm->save($user_tmp)) {
                        CakeLog::write('updates', 'Error when creating user BDM TG' . PHP_EOL);
                    }
                }
            }
        }

        if (!$distributor_contact_bdm_bd) {
            return false;
        }

        return true;
    }

    /**
     * Updates a BDM TG that comes from UK JSON.
     */
    public function updateDistributorBDMTG($contact, $distributor_id, $aagRegionId)
    {
        if ($contact) {
            $name = explode(" ", $contact);
            $exist_contact = $this->findByFirstNameAndLastName($name[0], $name[1]);

            if ($exist_contact) {
                $exist_contact_to_garage = $this->DistributorContactBdm->getContactsByContactAndDistributorAndPosition($exist_contact['Contact']['id'],  $distributor_id, ConstantsPositions::BDM_TG_ID);
                if (!$exist_contact_to_garage) {
                    $distributor_contact_bdm = ClassRegistry::init('DistributorContactBdm');

                    //Delete the previous registration
                    $previous_contact = $this->DistributorContactBdm->getByDistributorAndPosition($distributor_id);
                    if ($previous_contact) {
                        $distributor_contact_bdm->delete($previous_contact['DistributorContactBdm']['id']);
                    }

                    //Insert the new record
                    if (!$distributor_contact_bdm->addDistributorContactBdm($distributor_id, $exist_contact['Contact']['id'])) {
                        CakeLog::write('updates', 'Creation of distributor BDM TG failure' . PHP_EOL);
                    }
                }
            } else {
                $last_id = $this->find(
                    'first',
                    array(
                        'order' => 'id DESC',
                        'fields' => 'id'
                    )
                );

                $identification_number = 'UK' . "-" . ($last_id['Contact']['id'] + 1);

                $contact_tmp = array(
                    'Contact' => array(
                        'title_id' => null,
                        'contact_id' => null,
                        'first_name' => $name[0],
                        'last_name' => $name[1],
                        'position_id' => ConstantsPositions::BDM_TG_ID,
                        'phone' => null,
                        'mobile_phone' => null,
                        'email' => $name[0] . '.' . $name[1] . '@groupauto.co.uk',
                        'identification_number' => $identification_number,
                        'creation_date' => date('Y-m-d H:i:s'),
                        'guid' => CakeText::uuid(),
                        'aag_region_id' => $aagRegionId
                    )
                );

                $this->create();
                $contact_bd = $this->save($contact_tmp);
                if (!$contact_bd) {
                    CakeLog::write('updates', 'Creation of BDM TG failure' . PHP_EOL);
                }

                //Delete the previous registration
                $previous_contact = $this->DistributorContactBdm->getByDistributorAndPosition($distributor_id);
                if ($previous_contact) {
                    $this->DistributorContactBdm->delete($previous_contact['DistributorContactBdm']['id']);
                }
                if ($contact_bd) {
                    $distributor_contact_bdm = ClassRegistry::init('DistributorContactBdm');
                    if (!$distributor_contact_bdm->addDistributorContactBdm($distributor_id, $contact_bd['Contact']['id'])) {
                        CakeLog::write('updates', 'Creation of distributor BDM TG failure' . PHP_EOL);
                    }
                }
            }
        } else {
            $distributor_contact_bdm = ClassRegistry::init('DistributorContactBdm');
            //Delete the previous registration
            $previous_contact = $this->DistributorContactBdm->getByDistributorAndPosition($distributor_id);
            if ($previous_contact) {
                $distributor_contact_bdm->delete($previous_contact['DistributorContactBdm']['id']);
            }
        }
    }

    /**
     * Create a new BDM GPC that comes from UK JSON.
     */
    public function createDistributorBDMGPC($contact, $distributor_id, $aagRegionId)
    {
        if ($contact) {
            $name = explode(" ", $contact);
            $name0 = $name[0];
            $name1 = isset($name[1]) ? $name[1] : '-';
            $exist_contact = $this->findByFirstNameAndLastName($name0, $name1);

            if ($exist_contact) {
                $distributor_contact_bdm = ClassRegistry::init('DistributorContactBdm');
                $distributor_contact_bdm_bd = $distributor_contact_bdm->addDistributorContactBdm($distributor_id, $exist_contact['Contact']['id']);
                if (!$distributor_contact_bdm_bd) {
                    CakeLog::write('updates', 'Error when creating distributor BDM GPC' . PHP_EOL);
                }
            } else {
                $last_id = $this->find(
                    'first',
                    array(
                        'order' => 'id DESC',
                        'fields' => 'id'
                    )
                );

                $identification_number = 'UK' . "-" . ($last_id['Contact']['id'] + 1);

                $contact_tmp = array(
                    'Contact' => array(
                        'title_id' => null,
                        'contact_id' => null,
                        'first_name' => $name0,
                        'last_name' => $name1,
                        'position_id' => ConstantsPositions::BDM_GPC_ID,
                        'phone' => null,
                        'mobile_phone' => null,
                        'email' => $name0 . '.' . $name1 . '@groupauto.co.uk',
                        'identification_number' => $identification_number,
                        'creation_date' => date('Y-m-d H:i:s'),
                        'guid' => CakeText::uuid(),
                        'aag_region_id' => $aagRegionId
                    )
                );

                $this->create();
                $contact_bd = $this->save($contact_tmp);
                if (!$contact_bd) {
                    CakeLog::write('updates', 'Error when creating BDM AAG' . PHP_EOL);
                }

                if ($contact_bd) {
                    $distributor_contact_bdm = ClassRegistry::init('DistributorContactBdm');
                    $distributor_contact_bdm_bd = $distributor_contact_bdm->addDistributorContactBdm($distributor_id, $contact_bd['Contact']['id']);
                    if (!$distributor_contact_bdm_bd) {
                        CakeLog::write('updates', 'Error when creating distributor contact BDM GPC' . PHP_EOL);
                    }

                    $user_tmp = array(
                        'User' => array(
                            'name' => $name0,
                            'surname' => $name1,
                            'username' => $name0 . $name1,
                            'password' => $name0 . $name1,
                            'contact_id' => $distributor_contact_bdm_bd['DistributorContactBdm']['contact_id'],
                            'role_id' => ConstantsRoles::GPC_LOGISTICS_BDM,
                            'language_id' => ConstantsLanguages::ENGLISH,
                            'active' => ConstantsBooleans::ACTIVE,
                            'creation_date' => date('Y-m-d H:i:s'),
                            'guid' => CakeText::uuid(),
                            'aag_region_id' => $aagRegionId,
                            'country_id' => ConstantsCountries::UNITED_KINGDOM
                        )
                    );
                    $user_bdm = ClassRegistry::init('User');
                    $user_bdm->create();
                    $user_bdm->validator()->remove('password');
                    if (!$user_bdm->save($user_tmp)) {
                        CakeLog::write('updates', 'Error when creating user BDM GPC' . PHP_EOL);
                    }
                }
            }
        }

        if (!$distributor_contact_bdm_bd) {
            return false;
        }

        return true;
    }

    /**
     * Updates BDM GPC that comes from UK JSON.
     */
    public function updateDistributorBDMGPC($contact, $distributor_id, $aagRegionId)
    {
        if ($contact) {
            $name = explode(" ", $contact);
            $name0 = $name[0];
            $name1 = isset($name[1]) ? $name[1] : '-';
            $exist_contact = $this->findByFirstNameAndLastName($name0, $name1);

            if ($exist_contact) {
                $exist_contact_to_garage = $this->DistributorContactBdm->getContactsByContactAndDistributorAndPosition($exist_contact['Contact']['id'],  $distributor_id, ConstantsPositions::BDM_GPC_ID);
                if (!$exist_contact_to_garage) {
                    $distributor_contact_bdm = ClassRegistry::init('DistributorContactBdm');

                    //Delete the previous registration
                    $previous_contact = $this->DistributorContactBdm->getByDistributorAndPositionBDMTG($distributor_id);
                    if ($previous_contact) {
                        $distributor_contact_bdm->delete($previous_contact['DistributorContactBdm']['id']);
                    }

                    //Insert the new record
                    if (!$distributor_contact_bdm->addDistributorContactBdm($distributor_id, $exist_contact['Contact']['id'])) {
                        CakeLog::write('updates', 'Creation of distributor BDM GPC failure' . PHP_EOL);
                    }
                }
            } else {
                $last_id = $this->find(
                    'first',
                    array(
                        'order' => 'id DESC',
                        'fields' => 'id'
                    )
                );

                $identification_number = 'UK' . "-" . ($last_id['Contact']['id'] + 1);

                $contact_tmp = array(
                    'Contact' => array(
                        'title_id' => null,
                        'contact_id' => null,
                        'first_name' => $name0,
                        'last_name' => $name1,
                        'position_id' => ConstantsPositions::BDM_GPC_ID,
                        'phone' => null,
                        'mobile_phone' => null,
                        'email' => $name0 . '.' . $name1 . '@groupauto.co.uk',
                        'identification_number' => $identification_number,
                        'creation_date' => date('Y-m-d H:i:s'),
                        'guid' => CakeText::uuid(),
                        'aag_region_id' => $aagRegionId
                    )
                );

                $this->create();
                $contact_bd = $this->save($contact_tmp);
                if (!$contact_bd) {
                    CakeLog::write('updates', 'Creation of BDM GPC failure' . PHP_EOL);
                }

                //Delete the previous registration
                $previous_contact = $this->DistributorContactBdm->getByDistributorAndPositionBDMTG($distributor_id);
                if ($previous_contact) {
                    $this->DistributorContactBdm->delete($previous_contact['DistributorContactBdm']['id']);
                }
                if ($contact_bd) {
                    $distributor_contact_bdm = ClassRegistry::init('DistributorContactBdm');
                    if (!$distributor_contact_bdm->addDistributorContactBdm($distributor_id, $contact_bd['Contact']['id'])) {
                        CakeLog::write('updates', 'Creation of distributor BDM GPC failure' . PHP_EOL);
                    }
                }
            }
        } else {
            $distributor_contact_bdm = ClassRegistry::init('DistributorContactBdm');
            //Delete the previous registration
            $previous_contact = $this->DistributorContactBdm->getByDistributorAndPositionBDMTG($distributor_id);
            if ($previous_contact) {
                $distributor_contact_bdm->delete($previous_contact['DistributorContactBdm']['id']);
            }
        }
    }

    public function edit_contact($contact)
    {
        $fields = array(
            'Contact' => array(
                'id',
                'title_id',
                'contact_id',
                'first_name',
                'last_name',
                'position_id',
                'garage_id',
                'distributor_id',
                'logistic_center_id',
                'phone',
                'mobile_phone',
                'email',
                'identification_number',
                'aag_region_id'
            )
        );
        $contact['Contact']['first_name'] = ucwords($contact['Contact']['first_name'], "-");
        $contact['Contact']['first_name'] = ucwords($contact['Contact']['first_name']);

        $contact_bd = $this->guardar($contact, $fields);
        if (!$contact_bd) {
            return false;
        }

        $this->User = ClassRegistry::init('User');
        $users = $this->User->findAllByContactId($contact_bd['Contact']['id']);

        foreach ($users as $user) {
            $this->User->edit_user($user, $contact_bd['Contact']['position_id'], $contact_bd['Contact']['id']);
        }

        return $contact_bd;
    }

    public function getGarageManagerByGarage($garage_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageContactGeneralBranchManager',
                        'table' => 'garages_contacts_general_branch_manager',
                        'type' => 'LEFT',
                        'conditions' => 'GarageContactGeneralBranchManager.contact_id = Contact.id'
                    ),
                    array(
                        'alias' => 'Position',
                        'table' => 'positions',
                        'type' => 'INNER',
                        'conditions' => 'Position.id = Contact.position_id'
                    ),
                ),
                'conditions' => array(
                    'GarageContactGeneralBranchManager.garage_id' => $garage_id,
                    'Position.role_id' => ConstantsRoles::GARAGE,
                ),
                'fields' => array(
                    'Contact.*'
                ),
            )
        );
    }

    public function getDistributorManagerByDistributor($distributor_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'DistributorContactGeneralBranchManager',
                        'table' => 'distributors_contacts_general_branch_manager',
                        'type' => 'LEFT',
                        'conditions' => 'DistributorContactGeneralBranchManager.contact_id = Contact.id'
                    ),
                    array(
                        'alias' => 'Position',
                        'table' => 'positions',
                        'type' => 'INNER',
                        'conditions' => 'Position.id = Contact.position_id'
                    ),
                ),
                'conditions' => array(
                    'DistributorContactGeneralBranchManager.distributor_id' => $distributor_id,
                    'Position.role_id' => ConstantsRoles::DISTRIBUTOR,
                ),
                'fields' => array(
                    'Contact.*'
                ),
            )
        );
    }

    public function getByIdWithGarage($contact_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageContactBdm',
                        'table' => 'garages_contacts_bdm',
                        'type' => 'INNER',
                        'conditions' => 'GarageContactBdm.contact_id = Contact.id'
                    ),
                ),
                'conditions' => array(
                    'Contact.id' => $contact_id,
                ),
                'fields' => array(
                    'Contact.*'
                ),
            )
        );
    }

    public function getByIdWithDistributor($contact_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'DistributorContactBdm',
                        'table' => 'distributors_contacts_bdm',
                        'type' => 'INNER',
                        'conditions' => 'DistributorContactBdm.contact_id = Contact.id'
                    ),
                ),
                'conditions' => array(
                    'Contact.id' => $contact_id,
                ),
                'fields' => array(
                    'Contact.*'
                ),
            )
        );
    }

    public function getRSMContact($aag_region_id)
    {
        $conditions_aag_region = array();
        $conditions_aag_region = array('Contact.aag_region_id' => $aag_region_id);

        $this->Position = ClassRegistry::init('Position');
        $bdm_positions = $this->Position->getListPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG));
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Or' => array(
                        array(
                            'Contact.position_id' => ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID
                        ),
                        array(
                            'Contact.position_id' => ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID
                        ),
                        array(
                            'Contact.position_id' => $bdm_positions,
                        )
                    ),
                    $conditions_aag_region
                ),
                'fields' => array(
                    'Contact.id',
                    'Contact.full_name'
                ),
                'order' => array(
                    'Contact.first_name',
                    'Contact.last_name'
                )
            )
        );
    }

    public function getBDMContact($aag_region_id)
    {
        $this->Position = ClassRegistry::init('Position');
        $bdm_positions = $this->Position->getListPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM));

        return $this->find(
            'list',
            array(
                'conditions' => array(
                    array(
                        'Contact.position_id' => $bdm_positions,
                        'Contact.aag_region_id' => $aag_region_id
                    ),
                ),
                'fields' => array(
                    'Contact.id',
                    'Contact.full_name'
                ),
                'order' => array(
                    'Contact.first_name',
                    'Contact.last_name'
                )
            )
        );
    }

    public function getBDMContactWithoutAssociation($garage_id, $aag_region_id)
    {
        $this->Position = ClassRegistry::init('Position');
        $bdm_positions = $this->Position->getListPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM));

        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageContactBdm',
                        'table' => 'garages_contacts_bdm',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageContactBdm.contact_id = Contact.id',
                            'GarageContactBdm.garage_id' => $garage_id,
                        ),
                    ),
                ),
                'conditions' => array(
                    'Contact.position_id' => $bdm_positions,
                    'GarageContactBdm.garage_id IS NULL',
                    'Contact.aag_region_id' => $aag_region_id
                ),
                'fields' => array(
                    'Contact.id',
                    'Contact.full_name'
                ),
                'order' => array(
                    'Contact.first_name',
                    'Contact.last_name'
                )
            )
        );
    }

    public function BDM_list()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'Contact.identification_number',
                    'Contact.full_name'
                ),
                'order' => array(
                    'Contact.first_name',
                    'Contact.last_name'
                )
            )
        );
    }

    public function check_delete($contact_id)
    {
        $class_array = array(
            'Contact' => $Contact = ClassRegistry::init('Contact'),
            //'User' => $User = ClassRegistry::init('User'),
            'ContactContactList' => $ContactContactList = ClassRegistry::init('ContactContactList'),
            'DistributorContactBdm' => $DistributorContactBdm = ClassRegistry::init('DistributorContactBdm'),
            'DistributorContactGeneralBranchManager' => $DistributorContactGeneralBranchManager = ClassRegistry::init('DistributorContactGeneralBranchManager'),
            'DistributorContactStaff' => $DistributorContactStaff = ClassRegistry::init('DistributorContactStaff'),
            'GarageContactBdm' => $GarageContactBdm = ClassRegistry::init('GarageContactBdm'),
            'GarageContactGeneralBranchManager' => $GarageContactGeneralBranchManager = ClassRegistry::init('GarageContactGeneralBranchManager'),
            'GarageContactStaff' => $GarageContactStaff = ClassRegistry::init('GarageContactStaff'),
            'DistributorNetworkContactBdm' => $DistributorNetworkContactBdm = ClassRegistry::init('DistributorNetworkContactBdm'),
            'NetworkContactBdm' => $NetworkContactBdm = ClassRegistry::init('NetworkContactBdm'),
            'AppointmentContact' => $AppointmentContact = ClassRegistry::init('AppointmentContact'),
        );

        foreach ($class_array as $key => $Class) {
            $tmp = $Class->findByContactId($contact_id);
            if (!empty($tmp)) {
                return $key;
            }
        }

        $tmp = ClassRegistry::init('Appointment')->findByVisitContactId($contact_id);
        if (!empty($tmp)) {
            return 'AppointmentVisitContact';
        }

        return true;
    }

    public function check_delete_from_garage_distributor($contact_id)
    {
        $class_array = array(
            'Contact' => $Contact = ClassRegistry::init('Contact'),
            //'User' => $User = ClassRegistry::init('User'),
            'ContactContactList' => $ContactContactList = ClassRegistry::init('ContactContactList'),
            'DistributorContactBdm' => $DistributorContactBdm = ClassRegistry::init('DistributorContactBdm'),
            'DistributorContactStaff' => $DistributorContactStaff = ClassRegistry::init('DistributorContactStaff'),
            'GarageContactBdm' => $GarageContactBdm = ClassRegistry::init('GarageContactBdm'),
            'GarageContactStaff' => $GarageContactStaff = ClassRegistry::init('GarageContactStaff'),
        );

        foreach ($class_array as $key => $Class) {
            $tmp = $Class->findByContactId($contact_id);
            if (!empty($tmp)) {
                return $key;
            }
        }

        return true;
    }

    public function getListByPositionIdAndAagRegionId($positions, $aag_region_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Contact.position_id' => $positions,
                    'Contact.aag_region_id' => $aag_region_id
                ),
                'fields' => array(
                    'id',
                    'full_name'
                )
            )
        );
    }

    public function getListByRoleId($roles)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Position',
                        'table' => 'positions',
                        'type' => 'INNER',
                        'conditions' => 'Position.id = Contact.position_id'
                    ),
                ),
                'conditions' => array(
                    'Position.role_id' => $roles
                ),
                'fields' => array(
                    'Contact.id',
                    'Contact.full_name'
                )
            )
        );
    }

    public function getListByRoleIdAndRegionId($roles, $aag_region_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Position',
                        'table' => 'positions',
                        'type' => 'INNER',
                        'conditions' => 'Position.id = Contact.position_id'
                    ),
                ),
                'conditions' => array(
                    'Contact.aag_region_id' => $aag_region_id,
                    'Position.role_id' => $roles
                ),
                'fields' => array(
                    'Contact.id',
                    'Contact.full_name'
                )
            )
        );
    }

    public function getContactsDataByPositionId($position, $condition_searcher)
    {

        $conditions = array(
            'Contact.position_id' => $position
        );

        if ($condition_searcher['first_name'] != '') {
            $conditions["Contact.first_name LIKE"] = '%' . $condition_searcher['first_name'] . '%';
        }
        if ($condition_searcher['last_name'] != '') {
            $conditions["Contact.last_name LIKE"] = '%' . $condition_searcher['last_name'] . '%';
        }
        if ($condition_searcher['position'] != '') {
            $conditions["Contact.position LIKE"] = '%' . $condition_searcher['position'] . '%';
        }
        if ($condition_searcher['email'] != '') {
            $conditions["Contact.email LIKE"] = '%' . $condition_searcher['email'] . '%';
        }

        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'User.contact_id = Contact.id'
                ),
                array(
                    'alias' => 'UserImage',
                    'table' => 'users_images',
                    'type' => 'LEFT',
                    'conditions' => 'User.id = UserImage.user_id'
                ),
            ),
            'conditions' => array(
                $conditions
            ),
            'fields' => array(
                'Contact.*',
                'User.*',
                'UserImage.*',
            ),
            'order' => array(
                'Contact.first_name',
                'Contact.last_name'
            )
        ));
    }

    public function getQueryContactsDataByPositionId($position)
    {
        $query = array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'User.contact_id = Contact.id'
                ),
                array(
                    'alias' => 'UserImage',
                    'table' => 'users_images',
                    'type' => 'LEFT',
                    'conditions' => 'User.id = UserImage.user_id'
                ),
            ),
            'conditions' => array(
                'Contact.position_id' => $position
            ),
            'fields' => array(
                'Contact.*',
                'User.*',
                'UserImage.*',
            ),
            'order' => array(
                'Contact.first_name',
                'Contact.last_name'
            )
        );

        return $query;
    }

    public function getAllExceptGarageManagerAndDistributorManager($aag_region_id)
    {
        return $this->find('list', array(
            'joins' => array(
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
                'Contact.aag_region_id' => $aag_region_id,
                'Position.role_id !=' => array(Constantsroles::GARAGE, ConstantsRoles::DISTRIBUTOR)
            ),
            'fields' => array(
                'id',
                'full_name'
            )
        ));
    }

    public function listCompleteNameRegion($aag_region_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Contact.aag_region_id' => $aag_region_id
                ),
                'fields' => array(
                    'Contact.full_name',
                ),
                'order' => array(
                    'Contact.first_name',
                    'Contact.last_name'
                )
            )
        );
    }

    public function getContactListByPositionId($positions)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Contact.position_id' => $positions,
                    'Contact.aag_region_id' => CakeSession::read('Auth.User.aag_region_id')
                ),
                'fields' => array(
                    'Contact.id',
                ),
            )
        );
    }

    public function getContactsVisitByGarage($garage_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageContactGeneralBranchManager',
                        'table' => 'garages_contacts_general_branch_manager',
                        'type' => 'LEFT',
                        'conditions' => 'GarageContactGeneralBranchManager.contact_id = Contact.id'
                    ),
                    array(
                        'alias' => 'GarageContactStaff',
                        'table' => 'garages_contacts_staff',
                        'type' => 'LEFT',
                        'conditions' => 'GarageContactStaff.contact_id = Contact.id'
                    ),
                    // array(
                    //     'alias' => 'GarageContactBdm',
                    //     'table' => 'garages_contacts_bdm',
                    //     'type' => 'LEFT',
                    //     'conditions' => 'GarageContactBdm.contact_id = Contact.id'
                    // ),
                ),
                'conditions' => array(
                    'OR' => array(
                        'GarageContactGeneralBranchManager.garage_id' => $garage_id,
                        'GarageContactStaff.garage_id' => $garage_id,
                        // 'GarageContactBdm.garage_id' => $garage_id,
                    )
                ),
                'fields' => array(
                    'Contact.full_name',
                ),
                'order' => array(
                    'Contact.first_name',
                    'Contact.last_name'
                )
            )
        );
    }

    public function getContactsVisitByDistributor($distributor_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'DistributorContactGeneralBranchManager',
                        'table' => 'distributors_contacts_general_branch_manager',
                        'type' => 'LEFT',
                        'conditions' => 'DistributorContactGeneralBranchManager.contact_id = Contact.id'
                    ),
                    array(
                        'alias' => 'DistributorContactStaff',
                        'table' => 'distributors_contacts_staff',
                        'type' => 'LEFT',
                        'conditions' => 'DistributorContactStaff.contact_id = Contact.id'
                    ),
                    // array(
                    //     'alias' => 'DistributorContactBdm',
                    //     'table' => 'distributors_contacts_bdm',
                    //     'type' => 'LEFT',
                    //     'conditions' => 'DistributorContactBdm.contact_id = Contact.id'
                    // ),
                ),
                'conditions' => array(
                    'OR' => array(
                        'DistributorContactGeneralBranchManager.distributor_id' => $distributor_id,
                        'DistributorContactStaff.distributor_id' => $distributor_id,
                        //'DistributorContactBdm.distributor_id' => $distributor_id
                    )
                ),
                'fields' => array(
                    'Contact.full_name',
                ),
                'order' => array(
                    'Contact.first_name',
                    'Contact.last_name'
                )
            )
        );
    }

    public function getChildren($contact_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'User',
                        'table' => 'users',
                        'type' => 'INNER',
                        'conditions' => 'User.contact_id = Contact.id'
                    ),
                ),
                'conditions' => array(
                    'Contact.contact_id' => $contact_id,
                ),
                'fields' => array(
                    'User.id',
                    'Contact.full_name',
                ),
            )
        );
    }

    public function getContactChildren($contact_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Contact.contact_id' => $contact_id,
                ),
                'fields' => array(
                    'Contact.id',
                    'Contact.full_name',
                ),
            )
        );
    }

    /**
     * FRANCE JSON.
     */
    public function createRepContact($garage, $garage_id)
    {
        $clear_characters = array(" ", ".");
        if (isset($garage['collaborateur']['dirigeant'][0])) {
            foreach ($garage['collaborateur']['dirigeant'] as $contact) {
                //If garage has email. Find by email. Else: find by name and surname
                if ($contact['email'] != null && $contact['email'] != '') {
                    $contact_bd = $this->findByEmail($contact['email']);
                } else {
                    $contact_bd = $this->findByFirstNameAndLastName($contact['prenom'], $contact['nom']);
                }

                //If the contact doesn't exists, We create a new contact
                if (empty($contact_bd)) {

                    $contact_new = array(
                        'Contact' => array(
                            'first_name' => ucwords(mb_strtolower($contact['prenom'])),
                            'last_name' => ucwords(mb_strtolower($contact['nom'])),
                            'position_id' => ConstantsPositions::GARAGE_MANAGER_ID,
                            'garage_id' => $garage_id,
                            'phone' => ($contact['telephone'] != '') ? str_replace($clear_characters, '', $contact['telephone']) : null,
                            'mobile_phone' => ($contact['mobile'] != '') ? str_replace($clear_characters, '', $contact['mobile']) : null,
                            'email' => ($contact['email'] != '') ? $contact['email'] : '',
                            'identification_number' => 'Fr-garage-' . $garage_id . '-' . $contact['nom'],
                            'creation_date' => date('Y-m-d H:i:s'),
                        )
                    );
                    $this->validator()->remove('email');
                    $this->create();
                    $contact_bd = $this->save($contact_new);
                }

                $contact_id = $contact_bd['Contact']['id'];
                $this->GarageContactGeneralBranchManager = ClassRegistry::init('GarageContactGeneralBranchManager');
                //Find distributor_contact_staff by distributor and contact. if it doesn't exits, we create it.
                $garage_contact_general_branch_manager = $this->GarageContactGeneralBranchManager->findByGarageIdAndContactId($garage_id, $contact_id);
                if (empty($garage_contact_general_branch_manager) && ($garage_id != null && $contact_id != null)) {
                    $garage_contact_general_branch_manager_new = array(
                        'GarageContactGeneralBranchManager' => array(
                            'garage_id' => $garage_id,
                            'contact_id' => $contact_id,
                        )
                    );
                    $this->GarageContactGeneralBranchManager->create();
                    $garage_contact_general_branch_manager_bd = $this->GarageContactGeneralBranchManager->save($garage_contact_general_branch_manager_new);
                    if (!$garage_contact_general_branch_manager_bd) {
                        CakeLog::write('updates-france', 'The Garage Contact could not be created.' . PHP_EOL);
                    }
                }
            }
        }

        if (isset($garage['collaborateur']['coordinateur'][0])) {
            foreach ($garage['collaborateur']['coordinateur'] as $contact) {
                //If garage has email. Find by email. Else: find by name and surname
                if ($contact['email'] != null && $contact['email'] != '') {
                    $contact_bd = $this->findByEmail($contact['email']);
                } else {
                    $contact_bd = $this->findByFirstNameAndLastName($contact['prenom'], $contact['nom']);
                }

                //If the contact doesn't exists, We create a new contact
                if (empty($contact_bd)) {

                    $contact_new = array(
                        'Contact' => array(
                            'first_name' => ucwords(mb_strtolower($contact['prenom'])),
                            'last_name' => ucwords(mb_strtolower($contact['nom'])),
                            'position_id' => ConstantsPositions::BDM_AAG_ID,
                            'garage_id' => $garage_id,
                            'phone' => ($contact['telephone'] != '') ? str_replace($clear_characters, '', $contact['telephone']) : null,
                            'mobile_phone' => ($contact['mobile'] != '') ? str_replace($clear_characters, '', $contact['mobile']) : null,
                            'email' => ($contact['email'] != '') ? $contact['email'] : '',
                            'identification_number' => 'Fr-garage-' . $garage_id . '-' . $contact['nom'],
                            'creation_date' => date('Y-m-d H:i:s'),
                        )
                    );
                    $this->validator()->remove('email');
                    $this->create();
                    $contact_bd = $this->save($contact_new);
                }

                $contact_id = $contact_bd['Contact']['id'];
                $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');
                //Find distributor_contact_staff by distributor and contact. if it doesn't exits, we create it.
                $garage_contact_bdm = $this->GarageContactBdm->findByGarageIdAndContactId($garage_id, $contact_id);
                if (empty($garage_contact_bdm) && ($garage_id != null && $contact_id != null)) {
                    $garage_contact_bdm_new = array(
                        'GarageContactBdm' => array(
                            'garage_id' => $garage_id,
                            'contact_id' => $contact_id,
                        )
                    );
                    $this->GarageContactBdm->create();
                    $garage_contact_bdm_bd = $this->GarageContactBdm->save($garage_contact_bdm_new);
                    if (!$garage_contact_bdm_bd) {
                        CakeLog::write('updates-france', 'The Garage Contact could not be created.' . PHP_EOL);
                    }
                }


                if ($contact_bd) {
                    $contact_id = $contact_bd['Contact']['id'];
                    $network_model = ClassRegistry::init('Network');
                    $garage_model = ClassRegistry::init('Garage');
                    $this->NetworkContactBdm = ClassRegistry::init('NetworkContactBdm');

                    $networks_contact = $this->NetworkContactBdm->findListByContactAndGarageId($contact_id, $garage_id);
                    $network = $network_model->findByName($contact['reseau']);
                    if ($network && (!$networks_contact || !in_array($network['Network']['id'], $networks_contact))) {
                        $network_contact_new = array(
                            'NetworkContactBdm' => array(
                                'network_id' => $network['Network']['id'],
                                'contact_id' => $contact_id,
                                'garage_id' => $garage_id,
                            )
                        );
                        $this->NetworkContactBdm->create();
                        $network_contact_bdm_bd = $this->NetworkContactBdm->save($network_contact_new);
                        if (!$network_contact_bdm_bd) {
                            CakeLog::write('updates-france', 'The Network Contact could not be created.' . PHP_EOL);
                        }
                    }
                }
            }
        }

        if (isset($garage['collaborateur']['rdr'][0])) {
            foreach ($garage['collaborateur']['rdr'] as $contact) {
                //If garage has email. Find by email. Else: find by name and surname
                if ($contact['email'] != null && $contact['email'] != '') {
                    $contact_bd = $this->findByEmail($contact['email']);
                } else {
                    $contact_bd = $this->findByFirstNameAndLastName($contact['prenom'], $contact['nom']);
                }

                //If the position doesn't exists, We create a new position
                $this->Position = ClassRegistry::init('Position');
                if (!isset($contact['fonction']) || $contact['fonction'] == '') {
                    $position_id_tmp = ConstantsPositions::BDM_AAG_ID;
                } else {
                    $position_bd = $this->Position->findByNameFr($contact['fonction']);
                    if (!$position_bd) {
                        $position_new = array(
                            'Position' => array(
                                'name_en' => $contact['fonction'],
                                'name_fr' => $contact['fonction'],
                                'name_de' => $contact['fonction'],
                                'name_lc' => 'Bd.Positions',
                                'role_id' => ConstantsRoles::BDM_AAG
                            )
                        );
                        $this->Position->create();
                        $position_bd = $this->Position->save($position_new);
                        if (!$position_bd) {
                            CakeLog::write('updates-france', 'The Position could not be created.' . PHP_EOL);
                        }
                    }
                    $position_id_tmp = $position_bd['Position']['id'];
                }

                //If the contact doesn't exists, We create a new contact
                if (empty($contact_bd)) {

                    $contact_new = array(
                        'Contact' => array(
                            'first_name' => ucwords(mb_strtolower($contact['prenom'])),
                            'last_name' => ucwords(mb_strtolower($contact['nom'])),
                            'position_id' => $position_id_tmp,
                            'garage_id' => $garage_id,
                            'phone' => ($contact['telephone'] != '') ? str_replace($clear_characters, '', $contact['telephone']) : null,
                            'mobile_phone' => ($contact['mobile'] != '') ? str_replace($clear_characters, '', $contact['mobile']) : null,
                            'email' => ($contact['email'] != '') ? $contact['email'] : '',
                            'identification_number' => 'Fr-garage-' . $garage_id . '-' . $contact['nom'],
                            'creation_date' => date('Y-m-d H:i:s'),
                        )
                    );
                    $this->validator()->remove('email');
                    $this->create();
                    $contact_bd = $this->save($contact_new);
                    if (!$contact_bd) {
                        CakeLog::write('updates-france', 'The Garage Contact could not be created.' . PHP_EOL);
                    }
                }

                $contact_id = $contact_bd['Contact']['id'];
                //Find distributor_contact_staff by distributor and contact. if it doesn't exits, we create it.
                $garage_contact_bdm = $this->GarageContactBdm->findByGarageIdAndContactId($garage_id, $contact_id);
                $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');
                if (empty($garage_contact_bdm) && ($garage_id != null && $contact_id != null)) {
                    $garage_contact_bdm_new = array(
                        'GarageContactBdm' => array(
                            'garage_id' => $garage_id,
                            'contact_id' => $contact_id,
                        )
                    );
                    $this->GarageContactBdm->create();
                    $garage_contact_bdm_bd = $this->GarageContactve($garage_contact_bdm_new);
                    if (!$garage_contact_bdm_bd) {
                        CakeLog::write('updates-france', 'The Garage Contact could not be created.' . PHP_EOL);
                    }
                }

                if ($contact_bd) {
                    $contact_id = $contact_bd['Contact']['id'];
                    $network_model = ClassRegistry::init('Network');
                    $garage_model = ClassRegistry::init('Garage');
                    $this->NetworkContactBdm = ClassRegistry::init('NetworkContactBdm');

                    $networks_contact = $this->NetworkContactBdm->findListByContactAndGarageId($contact_id, $garage_id);
                    $network = $network_model->findByName($contact['reseau']);
                    if ($network && (!$networks_contact || !in_array($network['Network']['id'], $networks_contact))) {
                        $network_contact_new = array(
                            'NetworkContactBdm' => array(
                                'network_id' => $network['Network']['id'],
                                'contact_id' => $contact_id,
                                'garage_id' => $garage_id,
                            )
                        );
                        $this->NetworkContactBdm->create();
                        $network_contact_bdm_bd = $this->NetworkContactBdm->save($network_contact_new);
                        if (!$network_contact_bdm_bd) {
                            CakeLog::write('updates-france', 'The Garage Contact could not be created.' . PHP_EOL);
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * FRANCE JSON.
     */
    public function updateRepContact($garage, $garage_exist)
    {
        $clear_characters = array(" ", ".");
        if (isset($garage['collaborateur'])) {
            if (isset($garage['collaborateur']['dirigeant'])) {
                if (isset($garage['collaborateur']['dirigeant'][0])) {
                    foreach ($garage['collaborateur']['dirigeant'] as $contact) {
                        //If garage has email. Find by email. Else: find by name and surname
                        if ($contact['email'] != null && $contact['email'] != '') {
                            $contact_bd = $this->findByEmail($contact['email']);
                        } else {
                            $contact_bd = $this->findByFirstNameAndLastName($contact['prenom'], $contact['nom']);
                        }

                        //If the contact doesn't exists, We create a new contact
                        if (empty($contact_bd)) {

                            $contact_new = array(
                                'Contact' => array(
                                    'first_name' => ucwords(mb_strtolower($contact['prenom'])),
                                    'last_name' => ucwords(mb_strtolower($contact['nom'])),
                                    'position_id' => ConstantsPositions::GARAGE_MANAGER_ID,
                                    'garage_id' => $garage_exist['Garage']['id'],
                                    'phone' => ($contact['telephone'] != '') ? str_replace($clear_characters, '', $contact['telephone']) : null,
                                    'mobile_phone' => ($contact['mobile'] != '') ? str_replace($clear_characters, '', $contact['mobile']) : null,
                                    'email' => ($contact['email'] != '') ? $contact['email'] : '',
                                    'identification_number' => 'Fr-garage-' . $garage_exist['Garage']['id'] . '-' . $contact['nom'],
                                    'creation_date' => date('Y-m-d H:i:s'),
                                )
                            );
                            $this->validator()->remove('email');
                            $this->create();
                            $contact_bd = $this->save($contact_new);
                        }


                        $contact_id = $contact_bd['Contact']['id'];
                        $this->GarageContactGeneralBranchManager = ClassRegistry::init('GarageContactGeneralBranchManager');
                        //Find distributor_contact_staff by distributor and contact. if it doesn't exits, we create it.
                        $garage_contact_general_branch_manager = $this->GarageContactGeneralBranchManager->findByGarageIdAndContactId($garage_exist['Garage']['id'], $contact_id);
                        if (empty($garage_contact_general_branch_manager) && ($garage_exist['Garage']['id'] != null && $contact_id != null)) {
                            $garage_contact_general_branch_manager_new = array(
                                'GarageContactGeneralBranchManager' => array(
                                    'garage_id' => $garage_exist['Garage']['id'],
                                    'contact_id' => $contact_id,
                                )
                            );
                            $this->GarageContactGeneralBranchManager->create();
                            $garage_contact_general_branch_manager_bd = $this->GarageContactGeneralBranchManager->save($garage_contact_general_branch_manager_new);
                            if (!$garage_contact_general_branch_manager_bd) {
                                CakeLog::write('updates-france', 'The Garage Contact could not be created.' . PHP_EOL);
                            }
                        }
                    }
                }
            }

            if (isset($garage['collaborateur']['coordinateur'])) {
                if (isset($garage['collaborateur']['coordinateur'][0])) {
                    foreach ($garage['collaborateur']['coordinateur'] as $contact) {
                        //If garage has email. Find by email. Else: find by name and surname
                        if ($contact['email'] != null && $contact['email'] != '') {
                            $contact_bd = $this->findByEmail($contact['email']);
                        } else {
                            $contact_bd = $this->findByFirstNameAndLastName($contact['prenom'], $contact['nom']);
                        }

                        //If the contact doesn't exists, We create a new contact
                        if (empty($contact_bd)) {

                            $contact_new = array(
                                'Contact' => array(
                                    'first_name' => ucwords(mb_strtolower($contact['prenom'])),
                                    'last_name' => ucwords(mb_strtolower($contact['nom'])),
                                    'position_id' => ConstantsPositions::BDM_AAG_ID,
                                    'garage_id' => $garage_exist['Garage']['id'],
                                    'phone' => ($contact['telephone'] != '') ? str_replace($clear_characters, '', $contact['telephone']) : null,
                                    'mobile_phone' => ($contact['mobile'] != '') ? str_replace($clear_characters, '', $contact['mobile']) : null,
                                    'email' => ($contact['email'] != '') ? $contact['email'] : '',
                                    'identification_number' => 'Fr-garage-' . $garage_exist['Garage']['id'] . '-' . $contact['nom'],
                                    'creation_date' => date('Y-m-d H:i:s'),
                                )
                            );
                            $this->validator()->remove('email');
                            $this->create();
                            $contact_bd = $this->save($contact_new);
                        }

                        $contact_id = $contact_bd['Contact']['id'];
                        $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');
                        //Find distributor_contact_staff by distributor and contact. if it doesn't exits, we create it.
                        $garage_contact_bdm = $this->GarageContactBdm->findByGarageIdAndContactId($garage_exist['Garage']['id'], $contact_id);
                        if (empty($garage_contact_bdm) && ($garage_exist['Garage']['id'] != null && $contact_id != null)) {
                            $garage_contact_bdm_new = array(
                                'GarageContactBdm' => array(
                                    'garage_id' => $garage_exist['Garage']['id'],
                                    'contact_id' => $contact_id,
                                )
                            );
                            $this->GarageContactBdm->create();
                            $garage_contact_bdm_bd = $this->GarageContactBdm->save($garage_contact_bdm_new);
                            if (!$garage_contact_bdm_bd) {
                                CakeLog::write('updates-france', 'The Garage Contact could not be created.' . PHP_EOL);
                            }
                        }


                        if ($contact_bd) {
                            $contact_id = $contact_bd['Contact']['id'];
                            $network_model = ClassRegistry::init('Network');
                            $garage_model = ClassRegistry::init('Garage');
                            $this->NetworkContactBdm = ClassRegistry::init('NetworkContactBdm');

                            $networks_contact = $this->NetworkContactBdm->findListByContactAndGarageId($contact_id, $garage_exist['Garage']['id']);
                            $network = $network_model->findByName($contact['reseau']);
                            if ($network && (!$networks_contact || !in_array($network['Network']['id'], $networks_contact))) {
                                $network_contact_new = array(
                                    'NetworkContactBdm' => array(
                                        'network_id' => $network['Network']['id'],
                                        'contact_id' => $contact_id,
                                        'garage_id' => $garage_exist['Garage']['id'],
                                    )
                                );
                                $this->NetworkContactBdm->create();
                                $network_contact_bdm_bd = $this->NetworkContactBdm->save($network_contact_new);
                                if (!$network_contact_bdm_bd) {
                                    CakeLog::write('updates-france', 'The Network Contact could not be created.' . PHP_EOL);
                                }
                            }
                        }
                    }
                }
            }

            if (isset($garage['collaborateur']['rdr'])) {
                if (isset($garage['collaborateur']['rdr'][0])) {
                    foreach ($garage['collaborateur']['rdr'] as $contact) {
                        //If garage has email. Find by email. Else: find by name and surname
                        if ($contact['email'] != null && $contact['email'] != '') {
                            $contact_bd = $this->findByEmail($contact['email']);
                        } else {
                            $contact_bd = $this->findByFirstNameAndLastName($contact['prenom'], $contact['nom']);
                        }

                        //If the position doesn't exists, We create a new position
                        $this->Position = ClassRegistry::init('Position');
                        if (!isset($contact['fonction']) || $contact['fonction'] == '') {
                            $position_id_tmp = ConstantsPositions::BDM_AAG_ID;
                        } else {
                            $position_bd = $this->Position->findByNameFr($contact['fonction']);
                            if (!$position_bd) {
                                $position_new = array(
                                    'Position' => array(
                                        'name_en' => $contact['fonction'],
                                        'name_fr' => $contact['fonction'],
                                        'name_de' => $contact['fonction'],
                                        'name_lc' => 'Bd.Positions',
                                        'role_id' => ConstantsRoles::BDM_AAG
                                    )
                                );
                                $this->Position->create();
                                $position_bd = $this->Position->save($position_new);
                                if (!$position_bd) {
                                    CakeLog::write('updates-france', 'The Position could not be created.' . PHP_EOL);
                                }
                            }
                            $position_id_tmp = $position_bd['Position']['id'];
                        }

                        //If the contact doesn't exists, We create a new contact
                        if (empty($contact_bd)) {

                            $contact_new = array(
                                'Contact' => array(
                                    'first_name' => ucwords(mb_strtolower($contact['prenom'])),
                                    'last_name' => ucwords(mb_strtolower($contact['nom'])),
                                    'position_id' => $position_id_tmp,
                                    'garage_id' => $garage_exist['Garage']['id'],
                                    'phone' => ($contact['telephone'] != '') ? str_replace($clear_characters, '', $contact['telephone']) : null,
                                    'mobile_phone' => ($contact['mobile'] != '') ? str_replace($clear_characters, '', $contact['mobile']) : null,
                                    'email' => ($contact['email'] != '') ? $contact['email'] : '',
                                    'identification_number' => 'Fr-garage-' . $garage_exist['Garage']['id'] . '-' . $contact['nom'],
                                    'creation_date' => date('Y-m-d H:i:s'),
                                )
                            );
                            $this->validator()->remove('email');
                            $this->create();
                            $contact_bd = $this->save($contact_new);
                            if (!$contact_bd) {
                                CakeLog::write('updates-france', 'The Garage Contact could not be created.' . PHP_EOL);
                            }
                        }

                        $contact_id = $contact_bd['Contact']['id'];

                        //Find distributor_contact_staff by distributor and contact. if it doesn't exits, we create it.
                        $garage_contact_bdm = $this->GarageContactBdm->findByGarageIdAndContactId($garage_exist['Garage']['id'], $contact_id);
                        $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');
                        if (empty($garage_contact_bdm) && ($garage_exist['Garage']['id'] != null && $contact_id != null)) {
                            $garage_contact_bdm_new = array(
                                'GarageContactBdm' => array(
                                    'garage_id' => $garage_exist['Garage']['id'],
                                    'contact_id' => $contact_id,
                                )
                            );
                            $this->GarageContactBdm->create();
                            $garage_contact_bdm_bd = $this->GarageContactBdm->save($garage_contact_bdm_new);
                            if (!$garage_contact_bdm_bd) {
                                CakeLog::write('updates-france', 'The Garage Contact could not be created.' . PHP_EOL);
                            }
                        }

                        if ($contact_bd) {
                            $contact_id = $contact_bd['Contact']['id'];
                            $network_model = ClassRegistry::init('Network');
                            $garage_model = ClassRegistry::init('Garage');
                            $this->NetworkContactBdm = ClassRegistry::init('NetworkContactBdm');

                            $networks_contact = $this->NetworkContactBdm->findListByContactAndGarageId($contact_id, $garage_exist['Garage']['id']);
                            $network = $network_model->findByName($contact['reseau']);
                            if ($network && (!$networks_contact || !in_array($network['Network']['id'], $networks_contact))) {
                                $network_contact_new = array(
                                    'NetworkContactBdm' => array(
                                        'network_id' => $network['Network']['id'],
                                        'contact_id' => $contact_id,
                                        'garage_id' => $garage_exist['Garage']['id'],
                                    )
                                );
                                $this->NetworkContactBdm->create();
                                $network_contact_bdm_bd = $this->NetworkContactBdm->save($network_contact_new);
                                if (!$network_contact_bdm_bd) {
                                    CakeLog::write('updates-france', 'The Garage Contact could not be created.' . PHP_EOL);
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * FRANCE JSON.
     */
    public function createDisCollaborateur($dis_json, $distributor_id)
    {
        $this->DistributorContactStaff = ClassRegistry::init('DistributorContactStaff');
        $this->Position = ClassRegistry::init('Position');
        $this->DistributorContactGeneralBranchManager = ClassRegistry::init('DistributorContactGeneralBranchManager');

        if (isset($distributor['collaborateur']['dirigeant'][0])) {
            foreach ($distributor['collaborateur']['dirigeant'] as $key => $contact) {

                //If garage has email. Find by email. Else: find by name and surname
                if ($contact['email'] != null && $contact['email'] != '') {
                    $contact_bd = $this->findByEmail($contact['email']);
                } else {
                    $contact_bd = $this->findByFirstNameAndLastName(utf8_decode($contact['prenom']), utf8_decode($contact['nom']));
                }

                //If the contact doesn't exists, We create a new contact
                if (empty($contact_bd) && $contact['prenom'] && $contact['nom'] && $contact['email']) {

                    //If the position doesn't exists, We create a new position
                    if ($contact['fonction'] == '') {
                        $position_id_tmp = ConstantsPositions::DISTRIBUTOR_MANAGER_ID;
                    } else {
                        $position_bd = $this->Position->findByNameFr($contact['fonction']);
                        if (!$position_bd) {
                            $position_new = array(
                                'Position' => array(
                                    'name_en' => $contact['fonction'],
                                    'name_fr' => $contact['fonction'],
                                    'name_de' => $contact['fonction'],
                                    'name_lc' => 'Bd.Positions',
                                    'role_id' => ConstantsRoles::AAG_MANAGER
                                )
                            );
                            $this->Position->create();
                            $position_bd = $this->Position->save($position_new);
                            if (!$position_bd) {
                                CakeLog::write('updates-france', 'The Position could not be created.' . PHP_EOL);
                            }
                        }
                        $position_id_tmp = $position_bd['Position']['id'];
                    }

                    $contact_new = array(
                        'Contact' => array(
                            'first_name' => ucwords(mb_strtolower($contact['prenom'])),
                            'last_name' => ucwords(mb_strtolower($contact['nom'])),
                            'position_id' => $position_id_tmp,
                            'distributor_id' => $distributor_id,
                            'phone' => is_numeric(str_replace($clear_characters, '', $contact['telephone'])) && ($contact['telephone'] != '') ? str_replace($clear_characters, '', $contact['telephone']) : null,
                            'mobile_phone' => is_numeric(str_replace($clear_characters, '', $contact['mobile'])) && ($contact['mobile'] != '') ? str_replace($clear_characters, '', $contact['mobile']) : null,
                            'email' => ($contact['email'] != '') ? $contact['email'] : '',
                            'identification_number' => $contact['id_isa'],
                            'creation_date' => date('Y-m-d H:i:s'),
                        )
                    );
                    $this->validator()->remove('email');
                    $this->create();
                    $contact_bd = $this->save($contact_new);
                    if (!$contact_bd) {
                        CakeLog::write('updates-france', 'The Contact could not be created.' . PHP_EOL);
                    }
                }

                if ($contact_bd) {
                    $contact_id = $contact_bd['Contact']['id'];
                    //Find distributor_contact_staff by distributor and contact. if it doesn't exits, we create it.
                    $distributor_contact_general_branch_manager = $this->DistributorContactGeneralBranchManager->findByDistributorIdAndContactId($distributor_id, $contact_id);
                    if (empty($distributor_contact_general_branch_manager) && ($distributor_id != null && $contact_id != null)) {
                        $distributor_contact_general_branch_manager_new = array(
                            'DistributorContactGeneralBranchManager' => array(
                                'distributor_id' => $distributor_id,
                                'contact_id' => $contact_id,
                            )
                        );
                        $this->DistributorContactGeneralBranchManager->create();
                        $distributor_contact_general_branch_manager_bd = $this->DistributorContactGeneralBranchManager->save($distributor_contact_general_branch_manager_new);
                        if (!$distributor_contact_general_branch_manager_bd) {
                            CakeLog::write('updates-france', 'The Distributor Contact General Branch Manager could not be created.' . PHP_EOL);
                        }
                    }
                }
            }
        }

        if (isset($distributor['collaborateur']['divers'][0])) {
            foreach ($distributor['collaborateur']['divers'] as $contact) {

                //If garage has email. Find by email. Else: find by name and surname
                if ($contact['email'] != null && $contact['email'] != '') {
                    $contact_bd = $this->findByEmail($contact['email']);
                } else {
                    $contact_bd = $this->findByFirstNameAndLastName($contact['prenom'], $contact['nom']);
                }

                //If the contact doesn't exists, We create a new contact
                if (empty($contact_bd) && $contact['prenom'] && $contact['nom'] && $contact['email']) {

                    //If the position doesn't exists, We create a new position
                    if ($contact['fonction'] == '') {
                        $position_id_tmp = ConstantsPositions::GENERIC_STAFF_ID;
                    } else {
                        $position_bd = $this->Position->findByNameFr($contact['fonction']);
                        if (!$position_bd) {
                            $position_new = array(
                                'Position' => array(
                                    'name_en' => $contact['fonction'],
                                    'name_fr' => $contact['fonction'],
                                    'name_de' => $contact['fonction'],
                                    'name_lc' => 'Bd.Positions',
                                    'role_id' => ConstantsRoles::DISTRIBUTOR
                                )
                            );
                            $this->Position->create();
                            $position_bd = $this->Position->save($position_new);
                            if (!$position_bd) {
                                CakeLog::write('updates-france', 'The Position could not be created.' . PHP_EOL);
                            }
                        }
                        $position_id_tmp = $position_bd['Position']['id'];
                    }

                    $contact_new = array(
                        'Contact' => array(
                            'first_name' => ucwords(mb_strtolower($contact['prenom'])),
                            'last_name' => ucwords(mb_strtolower($contact['nom'])),
                            'position_id' => $position_id_tmp,
                            'distributor_id' => $distributor_id,
                            'phone' => is_numeric(str_replace($clear_characters, '', $contact['telephone'])) && ($contact['telephone'] != '') ? str_replace($clear_characters, '', $contact['telephone']) : null,
                            'mobile_phone' => is_numeric(str_replace($clear_characters, '', $contact['mobile'])) && ($contact['mobile'] != '') ? str_replace($clear_characters, '', $contact['mobile']) : null,
                            'email' => ($contact['email'] != '') ? $contact['email'] : '',
                            'identification_number' => $contact['id_isa'],
                            'creation_date' => date('Y-m-d H:i:s'),
                        )
                    );
                    $this->validator()->remove('email');
                    $this->create();
                    $contact_bd = $this->save($contact_new);
                    if (!$contact_bd) {
                        CakeLog::write('updates-france', 'The Contact could not be created.' . PHP_EOL);
                    }
                }

                if ($contact_bd) {
                    $contact_id = $contact_bd['Contact']['id'];

                    //Find distributor_contact_staff by distributor and contact. if it doesn't exits, we create it.
                    $distributor_contact_staff = $this->DistributorContactStaff->findByDistributorIdAndContactId($distributor_id, $contact_id);
                    if (empty($distributor_contact_staff) && ($distributor_id != null && $contact_id != null)) {
                        $distributor_contact_staff_new = array(
                            'DistributorContactStaff' => array(
                                'distributor_id' => $distributor_id,
                                'contact_id' => $contact_id,
                            )
                        );
                        $this->DistributorContactStaff->create();
                        $distributor_contact_staff_bd = $this->DistributorContactStaff->save($distributor_contact_staff_new);
                        if (!$distributor_contact_staff_bd) {
                            CakeLog::write('updates-france', 'The Distributor Contact Staff could not be created.' . PHP_EOL);
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * FRANCE JSON.
     */
    public function updateDisCollaborateur($dis_json, $exist_distributor)
    {
        if (isset($distributor['collaborateur'])) {
            $this->DistributorContactStaff = ClassRegistry::init('DistributorContactStaff');
            $this->Position = ClassRegistry::init('Position');
            $this->DistributorContactGeneralBranchManager = ClassRegistry::init('DistributorContactGeneralBranchManager');

            if (isset($distributor['collaborateur']['dirigeant']) && isset($distributor['collaborateur']['dirigeant'][0])) {
                foreach ($distributor['collaborateur']['dirigeant'] as $key => $contact) {

                    //If garage has email. Find by email. Else: find by name and surname
                    if ($contact['email'] != null && $contact['email'] != '') {
                        $contact_bd = $this->findByEmail($contact['email']);
                    } else {
                        $contact_bd = $this->findByFirstNameAndLastName(utf8_decode($contact['prenom']), utf8_decode($contact['nom']));
                    }

                    //If the contact doesn't exists, We create a new contact
                    if (empty($contact_bd) && $contact['prenom'] && $contact['nom'] && $contact['email']) {

                        //If the position doesn't exists, We create a new position
                        if ($contact['fonction'] == '') {
                            $position_id_tmp = ConstantsPositions::DISTRIBUTOR_MANAGER_ID;
                        } else {
                            $position_bd = $this->Position->findByNameFr($contact['fonction']);
                            if (!$position_bd) {
                                $position_new = array(
                                    'Position' => array(
                                        'name_en' => $contact['fonction'],
                                        'name_fr' => $contact['fonction'],
                                        'name_de' => $contact['fonction'],
                                        'name_lc' => 'Bd.Positions',
                                        'role_id' => ConstantsRoles::AAG_MANAGER
                                    )
                                );
                                $this->Position->create();
                                $position_bd = $this->Position->save($position_new);
                                if (!$position_bd) {
                                    CakeLog::write('updates-france', 'The Position could not be created.' . PHP_EOL);
                                }
                            }
                            $position_id_tmp = $position_bd['Position']['id'];
                        }

                        $contact_new = array(
                            'Contact' => array(
                                'first_name' => ucwords(mb_strtolower($contact['prenom'])),
                                'last_name' => ucwords(mb_strtolower($contact['nom'])),
                                'position_id' => $position_id_tmp,
                                'distributor_id' => $exist_distributor['Distributor']['id'],
                                'phone' => is_numeric(str_replace($clear_characters, '', $contact['telephone'])) && ($contact['telephone'] != '') ? str_replace($clear_characters, '', $contact['telephone']) : null,
                                'mobile_phone' => is_numeric(str_replace($clear_characters, '', $contact['mobile'])) && ($contact['mobile'] != '') ? str_replace($clear_characters, '', $contact['mobile']) : null,
                                'email' => ($contact['email'] != '') ? $contact['email'] : '',
                                'identification_number' => $contact['id_isa'],
                                'creation_date' => date('Y-m-d H:i:s'),
                            )
                        );
                        $this->validator()->remove('email');
                        $this->create();
                        $contact_bd = $this->save($contact_new);
                        if (!$contact_bd) {
                            CakeLog::write('updates-france', 'The Contact could not be created.' . PHP_EOL);
                        }
                    }

                    if ($contact_bd) {
                        $contact_id = $contact_bd['Contact']['id'];
                        //Find distributor_contact_staff by distributor and contact. if it doesn't exits, we create it.
                        $distributor_contact_general_branch_manager = $this->DistributorContactGeneralBranchManager->findByDistributorIdAndContactId($exist_distributor['Distributor']['id'], $contact_id);
                        if (empty($distributor_contact_general_branch_manager) && ($exist_distributor['Distributor']['id'] != null && $contact_id != null)) {
                            $distributor_contact_general_branch_manager_new = array(
                                'DistributorContactGeneralBranchManager' => array(
                                    'distributor_id' => $exist_distributor['Distributor']['id'],
                                    'contact_id' => $contact_id,
                                )
                            );
                            $this->DistributorContactGeneralBranchManager->create();
                            $distributor_contact_general_branch_manager_bd = $this->DistributorContactGeneralBranchManager->save($distributor_contact_general_branch_manager_new);
                            if (!$distributor_contact_general_branch_manager_bd) {
                                CakeLog::write('updates-france', 'The Distributor Contact General Branch Manager could not be created.' . PHP_EOL);
                            }
                        }
                    }
                }
            }

            if (isset($distributor['collaborateur']['divers']) && isset($distributor['collaborateur']['divers'][0])) {
                foreach ($distributor['collaborateur']['divers'] as $contact) {

                    //If garage has email. Find by email. Else: find by name and surname
                    if ($contact['email'] != null && $contact['email'] != '') {
                        $contact_bd = $this->findByEmail($contact['email']);
                    } else {
                        $contact_bd = $this->findByFirstNameAndLastName($contact['prenom'], $contact['nom']);
                    }

                    //If the contact doesn't exists, We create a new contact
                    if (empty($contact_bd) && $contact['prenom'] && $contact['nom'] && $contact['email']) {

                        //If the position doesn't exists, We create a new position
                        if ($contact['fonction'] == '') {
                            $position_id_tmp = ConstantsPositions::GENERIC_STAFF_ID;
                        } else {
                            $position_bd = $this->Position->findByNameFr($contact['fonction']);
                            if (!$position_bd) {
                                $position_new = array(
                                    'Position' => array(
                                        'name_en' => $contact['fonction'],
                                        'name_fr' => $contact['fonction'],
                                        'name_de' => $contact['fonction'],
                                        'name_lc' => 'Bd.Positions',
                                        'role_id' => ConstantsRoles::DISTRIBUTOR
                                    )
                                );
                                $this->Position->create();
                                $position_bd = $this->Position->save($position_new);
                                if (!$position_bd) {
                                    CakeLog::write('updates-france', 'The Position could not be created.' . PHP_EOL);
                                }
                            }
                            $position_id_tmp = $position_bd['Position']['id'];
                        }

                        $contact_new = array(
                            'Contact' => array(
                                'first_name' => ucwords(mb_strtolower($contact['prenom'])),
                                'last_name' => ucwords(mb_strtolower($contact['nom'])),
                                'position_id' => $position_id_tmp,
                                'distributor_id' => $exist_distributor['Distributor']['id'],
                                'phone' => is_numeric(str_replace($clear_characters, '', $contact['telephone'])) && ($contact['telephone'] != '') ? str_replace($clear_characters, '', $contact['telephone']) : null,
                                'mobile_phone' => is_numeric(str_replace($clear_characters, '', $contact['mobile'])) && ($contact['mobile'] != '') ? str_replace($clear_characters, '', $contact['mobile']) : null,
                                'email' => ($contact['email'] != '') ? $contact['email'] : '',
                                'identification_number' => $contact['id_isa'],
                                'creation_date' => date('Y-m-d H:i:s'),
                            )
                        );
                        $this->validator()->remove('email');
                        $this->create();
                        $contact_bd = $this->save($contact_new);
                        if (!$contact_bd) {
                            CakeLog::write('updates-france', 'The Contact could not be created.' . PHP_EOL);
                        }
                    }

                    if ($contact_bd) {
                        $contact_id = $contact_bd['Contact']['id'];

                        //Find distributor_contact_staff by distributor and contact. if it doesn't exits, we create it.
                        $distributor_contact_staff = $this->DistributorContactStaff->findByDistributorIdAndContactId($exist_distributor['Distributor']['id'], $contact_id);
                        if (empty($distributor_contact_staff) && ($exist_distributor['Distributor']['id'] != null && $contact_id != null)) {
                            $distributor_contact_staff_new = array(
                                'DistributorContactStaff' => array(
                                    'distributor_id' => $exist_distributor['Distributor']['id'],
                                    'contact_id' => $contact_id,
                                )
                            );
                            $this->DistributorContactStaff->create();
                            $distributor_contact_staff_bd = $this->DistributorContactStaff->save($distributor_contact_staff_new);
                            if (!$distributor_contact_staff_bd) {
                                CakeLog::write('updates-france', 'The Distributor Contact Staff could not be created.' . PHP_EOL);
                            }
                        }
                    }
                }
            }
        }


        return true;
    }

    public function getContactListDelegates($contact_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Contact.contact_id' => $contact_id,
                ),
                'fields' => array(
                    'Contact.id',
                    'Contact.full_name',
                ),
            )
        );
    }

    public function search_list($aag_region_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'aag_region_id' => $aag_region_id
            ),
            'fields' => array(
                'id',
                'full_name'
            ),
            'order' => array(
                'first_name',
                'last_name'
            )
        ));
    }

    public function getDelegateNameById($delegate_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'Contact.id' => $delegate_id,
            ),
        ));
    }

    public function getContactFromGarageNetwork($garage_network_id)
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'Contact.full_name',
                    'id'
                ),
                'joins' => array(
                    array(
                        'table' => 'garages_networks_contacts',
                        'alias' => "GarageNetworkContact",
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetworkContact.contact_id = Contact.id',
                        ),
                    )
                ),
                'conditions' => array(
                    'GarageNetworkContact.garage_network_id' => $garage_network_id,
                ),
                'order' => 'Contact.id'
            )
        );
    }

    public function getBeneluxAdminsEmails()
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'INNER',
                        'conditions' => array(
                            'User.contact_id = Contact.id'
                        )
                    )
                ),
                'conditions' => array(
                    'User.aag_region_id' => ConstantsAAGRegionId::BENELUX,
                    'User.role_id' => ConstantsRoles::ADMIN,
                    'Contact.email IS NOT NULL'
                ),
                'fields' => array(
                    'Contact.email'
                )
            )
        );
    }

    public function search_list_regions($aag_region_id, $role_id)
    {
        $conditions = array();
        if ($role_id != ConstantsRoles::SUPER_ADMIN) {
            $conditions = array('Contact.aag_region_id' => $aag_region_id);
        }
        return $this->find('list', array(
            'fields' => array(
                'id',
                'full_name'
            ),
            'conditions' => $conditions,
            'order' => array(
                'Contact.first_name',
                'Contact.last_name'
            )
        ));
    }

    public function getContactsNameByIdContact($contact_id)
    {
        $resultArray = array();
        $query = $this->find('first', array(
            'conditions' => array(
                'Contact.id' => $contact_id
            ),
            'fields' => array(
                'Contact.id',
                'Contact.full_name'
            ),
        ));

        if ($query) {
            $contact = $query['Contact'];
            $resultArray[$contact['id']] = $contact['full_name'];
        }
        return $resultArray;
    }

    public function findContactsEmailsConditions($list_of_ids, $aag_region_id)
    {
        $conditions = array('Contact.aag_region_id' => $aag_region_id);

        $emails = $this->find('all', array(
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.contact_id = Contact.id'
                    )
                )
            ),
            'conditions' => array(
                'Contact.id' => $list_of_ids,
                $conditions
            ),
            'fields' => array(
                'Contact.id',
                'Contact.email'
            ),
            'group' => 'Contact.email'
        ));

        $resultArray = array();
        if ($emails) {
            foreach ($emails as $email) {
                $resultArray[] = array(
                    'id' => $email['Contact']['id'],
                    'text' => $email['Contact']['email'],
                );
            }
        }
        return $resultArray;
    }

    public function obtenerPosiblesContactsEmailsAjaxRegion($condiciones, $aag_region_id)
    {
        $contacts = $this->obtenerPosiblesContactsEmailsQueryRegion($condiciones, $aag_region_id);
        $contacts = Hash::combine($contacts, '{n}.Contact.id', array('%s', '{n}.Contact.email'));

        return $contacts;
    }

    public function obtenerPosiblesContactsEmailsQueryRegion($condiciones_ajax = array(), $aag_region_id)
    {
        $conditions = array('Contact.aag_region_id' => $aag_region_id);

        if (isset($condiciones_ajax['email']) && !empty($condiciones_ajax['email'])) {
            $condiciones_ajax = array(
                'OR' => array(
                    'Contact.email LIKE' => '%' . $condiciones_ajax['email'] . '%',
                )
            );
        }

        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'INNER',
                        'conditions' => array(
                            'User.contact_id = Contact.id'
                        )
                    )
                ),
                'conditions' => array(
                    $condiciones_ajax,
                    $conditions
                ),
                'fields' => array(
                    'Contact.id',
                    'Contact.email',
                ),
                'order' => array(
                    'Contact.email',
                ),
                'group' => array(
                    'Contact.email'
                )
            )
        );
    }

    public function getContactsAjax($conditions, $aag_region_id, $role_id)
    {
        $contacts = $this->getContactsQuery($conditions, $aag_region_id, $role_id);
        $contacts = Hash::combine($contacts, '{n}.Contact.id', array('%s', '{n}.Contact.full_name'));

        return $contacts;
    }

    public function getContactsQuery($conditions_ajax = array(), $aag_region_id, $role_id)
    {
        $conditions_region = array();
        if ($role_id != ConstantsRoles::SUPER_ADMIN) {
            $conditions_region = array('Contact.aag_region_id' => $aag_region_id);
        }
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
                'conditions' => array(
                    $conditions_ajax,
                    $conditions_region,
                ),
                'fields' => array(
                    'Contact.id',
                    'Contact.full_name'
                ),
                'group' => array(
                    'Contact.id'
                ),
            )
        );
    }

    public function getContactsDelegatesAjax($conditions, $aag_region_id)
    {
        $contacts = $this->getContactsDelegatesQuery($conditions, $aag_region_id);
        $contacts = Hash::combine($contacts, '{n}.Contact.id', array('%s', '{n}.Contact.full_name'));

        return $contacts;
    }

    public function getContactsDelegatesQuery($conditions_ajax = array(), $aag_region_id)
    {
        $conditions_region = array('Contact.aag_region_id' => $aag_region_id);

        if (isset($conditions_ajax['name']) && !empty($conditions_ajax['name'])) {
            $contact_name_parts = explode(' ', $conditions_ajax['name']);

            if (count($contact_name_parts) == 2) {
                $conditions_ajax = array(
                    'AND' => array(
                        'Contact.first_name LIKE' => '%' . $contact_name_parts[0] . '%',
                        'Contact.last_name LIKE' => '%' . $contact_name_parts[1] . '%',
                    )
                );
            } else {
                $conditions_ajax = array(
                    'OR' => array(
                        array('Contact.first_name LIKE' => '%' . $conditions_ajax['name'] . '%'),
                        array('Contact.last_name LIKE' => '%' . $conditions_ajax['name'] . '%'),
                    )
                );
            }
        }

        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageContactStaff',
                        'table' => 'garages_contacts_staff',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageContactStaff.contact_id = Contact.id',
                        ),
                    ),
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Garage.id = GarageContactStaff.garage_id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    $conditions_ajax,
                    $conditions_region,
                    'Network.id' => array(NETWORK_ID_AUTOCARE, NETWORK_ID_TOPTRUCK, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_GEXPERT)
                ),
                'fields' => array(
                    'Contact.id',
                    'Contact.full_name'
                ),
                'group' => array(
                    'Contact.id'
                ),
            )
        );
    }

	/**
     * Returns the email of the contact and language id of user
     */
    public function getContactEmail($contact_id)
    {
		$userClass = ClassRegistry::init('User');
		$contact = $this->findById($contact_id);
		if (isset($contact['Contact']['email']) && !empty($contact['Contact']['email'])) {
			$user = $userClass->findByContactId($contact_id);
			if (isset($user['User']['language_id']) && !empty($user['User']['language_id'])) {
				return $contact['Contact']['email'] . ' ' . $user['User']['language_id'];
			}
		}
        return null;
    }
}
