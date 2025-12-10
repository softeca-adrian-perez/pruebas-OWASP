<?php

class GarageContactBdm extends AppModel
{
    public $useTable = 'garages_contacts_bdm';

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    private $_queries = array(
        'search_assigned_my_customers' => array(
            'joins' => array(
                array(
                    'alias' => 'TaskGarage',
                    'table' => 'tasks_garages',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.garage_id = GarageContactBdm.garage_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => 'Garage.id = GarageContactBdm.garage_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.task_id = Task.id'
                ),
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Appointment.id = Task.appointment_id',
                    ),
                ),
            ),
            'order' => array(
                'Task.limit_date asc',
                'Task.creation_date desc',
            ),
            'group' => array(
                'Task.id'
            ),
            'fields' => array(
                'Task.*',
                'Appointment.*'
            ),
        ),
    );

    public function getAllByGarageId($garage_id)
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
                            'Contact.id = GarageContactBdm.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageContactBdm.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Contact.*',
                    'GarageContactBdm.*'
                ),
                'order' => 'GarageContactBdm.order'
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
                            'Contact.id = GarageContactBdm.contact_id',
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

    public function getBDMByGarage($garage_id)
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
                            'Contact.id = GarageContactBdm.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageContactBdm.garage_id' => $garage_id,
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

    public function getGarageByBDM($contact_id)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'GarageContactBdm.contact_id' => $contact_id,
                ),
                'fields' => array(
                    'GarageContactBdm.garage_id'
                ),
            )
        );
    }

    public function getSomeByGarageId($garage_id)
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
                            'Contact.id = GarageContactBdm.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageContactBdm.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Contact.*'
                ),
                'limit' => 10
            )
        );
    }

    public function new_garage_contact_bdm($garage_contact_bdm)
    {
        $fields = array(
            'GarageContactBdm' => array(
                'garage_id',
                'contact_id',
                'order'
            )
        );
        $this->create();
        $garage_contact_bdm_bd = $this->guardar($garage_contact_bdm, $fields);
        if (!$garage_contact_bdm_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function findContactsBdmExport($garage_id)
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
                            'Contact.id = GarageContactBdm.contact_id',
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
                    'GarageContactBdm.garage_id'
                ),
                'conditions' => array(
                    'GarageContactBdm.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Contact.*',
                    'ContactTitle.name',
                    'Position.name_' . __l(),
                ),
            )
        );
    }

    public function createGarageContactBdm($garage_json, $garage_id, &$errors)
    { //The data comes from a Json left on the UK server

        if ($garage_json['Garage']['BDMNameCRM'] && $garage_json['Garage']['BDMNameMAM'] != 'NO BDM') {
            $contact = ClassRegistry::init('Contact');
            $name = explode(" ", $garage_json['Garage']['BDMNameCRM']);
            $exist_contact = $contact->findByFirstNameAndLastName($name[0], $name[1]);
            if ($exist_contact) {
                $garage_contact_tmp = array(
                    'GarageContactBdm' => array(
                        'garage_id' => $garage_id,
                        'contact_id' => $exist_contact['Contact']['id'],
                    )
                );
                $this->create();
                if (!$this->save($garage_contact_tmp)) {
                    CakeLog::write('updates', 'Link could not be created with BDM contact' . PHP_EOL);
                    $errors['Link could not be created with BDM contact'] = translateDataErrors($this->validationErrors);
                }
            } else {
                $email_exist = $contact->findFirstByEmail($name[0] . '.' . $name[1] . '@groupauto.co.uk');
                if (!empty($email_exist)) {
                    CakeLog::write('updates', 'The email ' . $name[0] . '.' . $name[1] . '@groupauto.co.uk' . ' is already in use by another BDM contact' . PHP_EOL);
                    $errors['The email ' . $name[0] . '.' . $name[1] . '@groupauto.co.uk' . ' is already in use by another BDM contact'] = translateDataErrors($this->validationErrors);
                    return false;
                }
                $contact_tmp = array(
                    'Contact' => array(
                        'title' => null,
                        'first_name' => $name[0],
                        'last_name' => $name[1],
                        'position_id' => ConstantsPositions::BDM_TG_ID,
                        'phone' => null,
                        'email' => $name[0] . '.' . $name[1] . '@groupauto.co.uk',
                        'identification_number' => 'Uk-BDM-TG-' . $garage_id,
                        'creation_date' => date('Y-m-d H:i:s'),
                    )
                );
                $contact->create();
                $contact_bd = $contact->save($contact_tmp);
                if (!$contact_bd) {
                    CakeLog::write('updates', 'BDM TG contact could not be created' . PHP_EOL);
                    $errors['BDM TG contact could not be created'] = translateDataErrors($contact->validationErrors);
                }

                $garage_contact_tmp = array(
                    'GarageContactBdm' => array(
                        'garage_id' => $garage_id,
                        'contact_id' => $contact_bd['Contact']['id'],
                    )
                );
                $this->create();
                if (!$this->save($garage_contact_tmp)) {
                    CakeLog::write('updates', 'Link could not be created with BDM TG contact' . PHP_EOL);
                    $errors['Link could not be created with BDM TG contact'] = translateDataErrors($this->validationErrors);
                }
            }
        }

        if ($garage_json['Garage']['BDMNameMAM'] && $garage_json['Garage']['BDMNameMAM'] != 'NO BDM') {
            $contact = ClassRegistry::init('Contact');
            $name = explode(" ", $garage_json['Garage']['BDMNameMAM']);
            $exist_contact = $contact->findByFirstNameAndLastName($name[0], $name[1]);
            if ($exist_contact) {
                $garage_contact_tmp = array(
                    'GarageContactBdm' => array(
                        'garage_id' => $garage_id,
                        'contact_id' => $exist_contact['Contact']['id'],
                    )
                );
                $this->create();
                if (!$this->save($garage_contact_tmp)) {
                    CakeLog::write('updates', 'Link could not be created with BDM contact' . PHP_EOL);
                    $errors['Link could not be created with BDM contact'] = translateDataErrors($this->validationErrors);
                }
            } else {
                $email_exist = $contact->findFirstByEmail($name[0] . '.' . $name[1] . '@groupauto.co.uk');
                if (!empty($email_exist)) {
                    CakeLog::write('updates', 'The email ' . $name[0] . '.' . $name[1] . '@groupauto.co.uk' . ' is already in use by another BDM contact' . PHP_EOL);
                    $errors['The email ' . $name[0] . '.' . $name[1] . '@groupauto.co.uk' . ' is already in use by another BDM contact'] = translateDataErrors($this->validationErrors);
                    return false;
                }
                $contact_tmp = array(
                    'Contact' => array(
                        'title' => null,
                        'first_name' => $name[0],
                        'last_name' => $name[1],
                        'position_id' => ConstantsPositions::BDM_AAG_ID,
                        'phone' => null,
                        'email' => $name[0] . '.' . $name[1] . '@groupauto.co.uk',
                        'identification_number' => 'Uk-BDM-AAG-' . $garage_id,
                        'creation_date' => date('Y-m-d H:i:s'),
                    )
                );
                $contact->create();
                $contact_bd = $contact->save($contact_tmp);
                if (!$contact_bd) {
                    CakeLog::write('updates', 'BDM AAG contact could not be created' . PHP_EOL);
                    $errors['BDM AAG contact could not be created'] = translateDataErrors($contact->validationErrors);
                }

                $garage_contact_tmp = array(
                    'GarageContactBdm' => array(
                        'garage_id' => $garage_id,
                        'contact_id' => $contact_bd['Contact']['id'],
                    )
                );

                $this->create();
                $garage_contact_bdm_bd = $this->save($garage_contact_tmp);
                if (!$this->save($garage_contact_bdm_bd)) {
                    CakeLog::write('updates', 'Link could not be created with BDM contact' . PHP_EOL);
                    $errors['Link could not be created with BDM contact'] = translateDataErrors($this->validationErrors);
                }

                $user_tmp = array(
                    'User' => array(
                        'name' => $name[0],
                        'surname' => $name[1],
                        'username' => $name[0] . $name[1],
                        'password' => $name[0] . $name[1],
                        'contact_id' => $garage_contact_bdm_bd['GarageContactBdm']['contact_id'],
                        'role_id' => ConstantsRoles::BDM_AAG,
                        'language_id' => ConstantsLanguages::ENGLISH,
                        'active' => ConstantsBooleans::ACTIVE,
                        'creation_date' => date('Y-m-d H:i:s'),
                    )
                );
                $this->User = ClassRegistry::init('User');
                $this->User->create();
                $this->User->validator()->remove('password');
                if (!$this->User->save($user_tmp)) {
                    CakeLog::write('updates', 'Unable to create user related to BDM AAG contact' . PHP_EOL);
                    $errors['Unable to create user related to BDM AAG contact'] = translateDataErrors($this->User->validationErrors);
                }
            }
        }

        $this->commit();
        return true;
    }

    public function createCustomerContactBdm($garage_json, $customer_id)
    { //The data comes from a Json left on the GERMANY server

        $this->Contact = ClassRegistry::init('Contact');

        if ($garage_json['Salesperson_Parts']) {
            $exist_contact = $this->Contact->findByIdentificationNumber($garage_json['Salesperson_Parts']);
            if ($exist_contact) {
                $garage_contact_bdm_tmp = array(
                    'GarageContactBdm' => array(
                        'garage_id' => $customer_id,
                        'contact_id' => $exist_contact['Contact']['id'],
                    )
                );

                $this->create();
                $this->save($garage_contact_bdm_tmp);
            }
        }

        if ($garage_json['Salesperson_Garage_Equipment']) {
            $exist_contact = $this->Contact->findByIdentificationNumber($garage_json['Salesperson_Garage_Equipment']);
            if ($exist_contact) {
                $garage_contact_bdm_tmp = array(
                    'GarageContactBdm' => array(
                        'garage_id' => $customer_id,
                        'contact_id' => $exist_contact['Contact']['id'],
                    )
                );

                $this->create();
                $this->save($garage_contact_bdm_tmp);
            }
        }
    }

    public function findListByGarageAndPosition($garage_id, $position_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = GarageContactBdm.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageContactBdm.garage_id' => $garage_id,
                    'Contact.position_id' => $position_id,
                ),
                'fields' => array(
                    'GarageContactBdm.contact_id'
                ),
            )
        );
    }

    public function countAssignedToMyCustomersOpenTask($contact)
    {
        return $this->find('count', array(
            'joins' => array(
                array(
                    'alias' => 'TaskGarage',
                    'table' => 'tasks_garages',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.garage_id = GarageContactBdm.garage_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.task_id = Task.id'
                ),
            ),
            'conditions' => array(
                'GarageContactBdm.contact_id' => $contact,
                'TaskGarage.completed' => ConstantsBooleans::NO
            ),
            'group' => array(
                'Task.id'
            ),
            'fields' => array(
                'Task.*',
            ),
        ));
    }

    public function getAllAssignedToMyCustomersOpenTask($contact)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'TaskGarage',
                    'table' => 'tasks_garages',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.garage_id = GarageContactBdm.garage_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.task_id = Task.id'
                ),
            ),
            'conditions' => array(
                'GarageContactBdm.contact_id' => $contact,
                'TaskGarage.completed' => ConstantsBooleans::NO,
                'OR' => array(
                    'Task.limit_date' => null,
                    'Task.limit_date >=' => date('Y-m-d'),
                    'Task.mandatory' => ConstantsBooleans::YES
                )
            ),
            'group' => array(
                'Task.id'
            ),
            'fields' => array(
                'Task.id',
            ),
        ));
    }

    public function getAssignedToMyCustomersOpenTask($contact)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'TaskGarage',
                    'table' => 'tasks_garages',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.garage_id = GarageContactBdm.garage_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => 'Garage.id = GarageContactBdm.garage_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.task_id = Task.id'
                ),
            ),
            'conditions' => array(
                'GarageContactBdm.contact_id' => $contact,
                'TaskGarage.completed' => ConstantsBooleans::NO,
                'Task.limit_date !=' => null,
                // 'OR' => array(
                //     'Task.limit_date >=' => date('Y-m-d'),
                //     'Task.mandatory' => ConstantsBooleans::YES
                // )
            ),
            'order' => array(
                'Task.limit_date asc',
                'Task.creation_date desc',
            ),
            //            'group' => array(
            //                'Task.id'
            //            ),
            'fields' => array(
                'Task.*',
                'Garage.name'
            ),
        ));
    }
    public function getAssignedToMyCustomersOpenTaskDeadlineNull($contact)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'TaskGarage',
                    'table' => 'tasks_garages',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.garage_id = GarageContactBdm.garage_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => 'Garage.id = GarageContactBdm.garage_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.task_id = Task.id'
                ),
            ),
            'conditions' => array(
                'GarageContactBdm.contact_id' => $contact,
                'TaskGarage.completed' => ConstantsBooleans::NO,
                'Task.limit_date' => null,
            ),
            'order' => array(
                'Task.limit_date asc',
                'Task.creation_date desc',
            ),
            //            'group' => array(
            //                'Task.id'
            //            ),
            'fields' => array(
                'Task.*',
                'Garage.name'
            ),
        ));
    }

    public function getAssignedToMyCustomersOpenTaskSortDueDay($contact)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'TaskGarage',
                    'table' => 'tasks_garages',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.garage_id = GarageContactBdm.garage_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => 'Garage.id = GarageContactBdm.garage_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.task_id = Task.id'
                ),
            ),
            'conditions' => array(
                'GarageContactBdm.contact_id' => $contact,
                'TaskGarage.completed' => ConstantsBooleans::NO,
                'Task.limit_date' => date('Y-m-d'),
            ),
            'order' => array(
                'Task.limit_date asc',
            ),
            //            'group' => array(
            //                'Task.id'
            //            ),
            'fields' => array(
                'Task.*',
                'Garage.name'
            ),
        ));
    }

    public function getAssignedToMyCustomersOpenTaskSortDueThisWeek($contact)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'TaskGarage',
                    'table' => 'tasks_garages',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.garage_id = GarageContactBdm.garage_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => 'Garage.id = GarageContactBdm.garage_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskGarage.task_id = Task.id'
                ),
            ),
            'conditions' => array(
                'GarageContactBdm.contact_id' => $contact,
                'TaskGarage.completed' => ConstantsBooleans::NO,
                'OR' => array(
                    array(
                        'Task.limit_date >=' => date('Y-m-d'),
                        'Task.limit_date <=' => date('Y-m-d', strtotime('sunday this week')),
                    ),
                    array(
                        'Task.limit_date >=' => date('Y-m-d', strtotime('monday this week')),
                        'Task.limit_date <=' => date('Y-m-d', strtotime('sunday this week')),
                        'Task.mandatory' => ConstantsBooleans::YES
                    )
                ),
            ),
            'order' => array(
                'Task.limit_date asc'
            ),
            //            'group' => array(
            //                'Task.id'
            //            ),
            'fields' => array(
                'Task.*',
                'Garage.name'
            ),
        ));
    }


    public function updateGarageContactBdm($garage_json, $exist_garage_id, &$errors)
    { //The data comes from a Json left on the UK server

        $garage_bmd_exist =  $this->findListByGarageId($exist_garage_id);
        if ($garage_json['Garage']['BDMNameCRM'] && $garage_json['Garage']['BDMNameCRM'] != 'NO BDM') {
            $contact = ClassRegistry::init('Contact');
            $name = explode(" ", $garage_json['Garage']['BDMNameCRM']);
            $exist_contact = $contact->findByFirstNameAndLastName($name[0], $name[1]);
            //$garage_bmd_tg_exist =  $this->findListByGarageId( $exist_garage_id );

            if ($exist_contact) {
                if (!in_array($exist_contact['Contact']['id'], $garage_bmd_exist)) {
                    $garage_contact_tmp = array(
                        'GarageContactBdm' => array(
                            'garage_id' => $exist_garage_id,
                            'contact_id' => $exist_contact['Contact']['id'],
                        )
                    );

                    $this->create();
                    $garage_contact_bd = $this->save($garage_contact_tmp);
                    if (!$garage_contact_bd) {
                        CakeLog::write('updates', 'The link to the contact BDM TG could not be created' . PHP_EOL);
                        $errors['The link to the contact BDM TG could not be created'] = translateDataErrors($this->validationErrors);
                    }
                } else {
                    $key = array_search($exist_contact['Contact']['id'], $garage_bmd_exist);
                    unset($garage_bmd_exist[$key]);
                }
            } else {
                $contact_tmp = array(
                    'Contact' => array(
                        'title' => null,
                        'first_name' => $name[0],
                        'last_name' => $name[1],
                        'position_id' => ConstantsPositions::BDM_TG_ID,
                        'phone' => null,
                        'email' => $name[0] . '.' . $name[1] . '@groupauto.co.uk',
                        'identification_number' => 'Uk-BDM-TG-' . $exist_garage_id,
                        'creation_date' => date('Y-m-d H:i:s'),
                    )
                );
                $contact = ClassRegistry::init('Contact');
                $contact->create();
                $contact_bd = $contact->save($contact_tmp);
                if (!$contact_bd) {
                    CakeLog::write('updates', 'The contact BDM TG could not be created' . PHP_EOL);
                    $errors['The contact BDM TG could not be created'] = translateDataErrors($contact->validationErrors);
                }

                $garage_contact_tmp = array(
                    'GarageContactBdm' => array(
                        'garage_id' => $exist_garage_id,
                        'contact_id' => $contact_bd['Contact']['id'],
                    )
                );
                $this->create();
                if (!$this->save($garage_contact_tmp)) {
                    CakeLog::write('updates', 'The link to the contact BDM TG could not be created' . PHP_EOL);
                    $errors['The link to the contact BDM TG could not be created'] = translateDataErrors($this->validationErrors);
                }
            }
        }

        if ($garage_json['Garage']['BDMNameMAM'] && $garage_json['Garage']['BDMNameMAM'] != 'NO BDM') {
            $contact = ClassRegistry::init('Contact');
            $name = explode(" ", $garage_json['Garage']['BDMNameMAM']);
            $exist_contact = $contact->findByFirstNameAndLastName($name[0], $name[1]);
            //$garage_bmd_aag_exist =  $this->findListByGarageId( $exist_garage_id );

            if ($exist_contact) {
                if (!in_array($exist_contact['Contact']['id'], $garage_bmd_exist)) {
                    $garage_contact_tmp = array(
                        'GarageContactBdm' => array(
                            'garage_id' => $exist_garage_id,
                            'contact_id' => $exist_contact['Contact']['id'],
                        )
                    );
                    $this->create();
                    $this->save($garage_contact_tmp);
                } else {
                    $key = array_search($exist_contact['Contact']['id'], $garage_bmd_exist);
                    unset($garage_bmd_exist[$key]);
                }
            } else {
                $contact_tmp = array(
                    'Contact' => array(
                        'title' => null,
                        'first_name' => $name[0],
                        'last_name' => $name[1],
                        'position_id' => ConstantsPositions::BDM_AAG_ID,
                        'phone' => null,
                        'email' => $name[0] . '.' . $name[1] . '@groupauto.co.uk',
                        'identification_number' => 'Uk-BDM-AAG-' . $exist_garage_id,
                        'creation_date' => date('Y-m-d H:i:s'),
                    )
                );
                $contact = ClassRegistry::init('Contact');
                $contact->create();
                $contact_bd = $contact->save($contact_tmp);

                $garage_contact_tmp = array(
                    'GarageContactBdm' => array(
                        'garage_id' => $exist_garage_id,
                        'contact_id' => $contact_bd['Contact']['id'],
                    )
                );

                $this->create();
                $garage_contact_bdm_bd = $this->save($garage_contact_tmp);

                $user_tmp = array(
                    'User' => array(
                        'name' => $name[0],
                        'surname' => $name[1],
                        'username' => $name[0] . $name[1],
                        'password' => $name[0] . $name[1],
                        'contact_id' => $garage_contact_bdm_bd['GarageContactBdm']['contact_id'],
                        'role_id' => ConstantsRoles::BDM_AAG,
                        'language_id' => ConstantsLanguages::ENGLISH,
                        'active' => ConstantsBooleans::ACTIVE,
                        'creation_date' => date('Y-m-d H:i:s'),
                    )
                );
                $this->User = ClassRegistry::init('User');

                $this->User->create();
                $this->User->validator()->remove('password');
                $user_bdm = $this->User->save($user_tmp);
            }
        }

        //remove garages BDM AAG that have not arrived through JSON
        foreach ($garage_bmd_exist as $key => $garage_bdm_aag_id) {
            $this->delete($key);
        }

        $this->commit();
        return true;
    }

    public function updateCustomerContactBdm($garage_json, $exist_customer_id)
    { //The data comes from a Json left on the GERMANY server

        $this->Contact = ClassRegistry::init('Contact');
        $customer_bmd_exist =  $this->findListByGarageId($exist_customer_id);

        if ($garage_json['Salesperson_Parts']) {
            $exist_contact = $this->Contact->findByIdentificationNumber($garage_json['Salesperson_Parts']);
            if ($exist_contact) {
                if (!in_array($exist_contact['Contact']['id'], $customer_bmd_exist)) {
                    $garage_contact_tmp = array(
                        'GarageContactBdm' => array(
                            'garage_id' => $exist_customer_id,
                            'contact_id' => $exist_contact['Contact']['id'],
                        )
                    );
                    $this->create();
                    $this->save($garage_contact_tmp);
                } else {
                    $key = array_search($exist_contact['Contact']['id'], $customer_bmd_exist);
                    unset($customer_bmd_exist[$key]);
                }
            }
        }

        if ($garage_json['Salesperson_Garage_Equipment']) {
            $exist_contact = $this->Contact->findByIdentificationNumber($garage_json['Salesperson_Garage_Equipment']);
            if ($exist_contact) {
                if (!in_array($exist_contact['Contact']['id'], $customer_bmd_exist)) {
                    $garage_contact_tmp = array(
                        'GarageContactBdm' => array(
                            'garage_id' => $exist_customer_id,
                            'contact_id' => $exist_contact['Contact']['id'],
                        )
                    );
                    $this->create();
                    $this->save($garage_contact_tmp);
                } else {
                    $key = array_search($exist_contact['Contact']['id'], $customer_bmd_exist);
                    unset($customer_bmd_exist[$key]);
                }
            }
        }

        //remove garages BDM AAG that have not arrived through JSON
        foreach ($customer_bmd_exist as $key => $garage_bdm_aag_id) {
            $this->delete($key);
        }
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
                            'Contact.id = GarageContactBdm.contact_id',
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

    public function change_order($garage_contact)
    {
        $fields = array(
            'GarageContactBdm' => array(
                'id',
                'principal',
                'order',
            )
        );
        if ($garage_contact['GarageContactBdm']['order'] == 1) {
            $garage_contact['GarageContactBdm']['principal'] = ConstantsBooleans::YES;
        } else {
            $garage_contact['GarageContactBdm']['principal'] = ConstantsBooleans::NO;
        }

        $garage_contact_bd = $this->guardar($garage_contact, $fields);

        if (!$garage_contact_bd) {
            return false;
        }

        $this->commit();
        return $garage_contact_bd;
    }
}
