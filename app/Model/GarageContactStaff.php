<?php

class GarageContactStaff extends AppModel{
    public $useTable = 'garages_contacts_staff';

    public $validate = array(
		'interest' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public function getAllByGarageId( $garage_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = GarageContactStaff.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageContactStaff.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Contact.*',
                    'GarageContactStaff.*'
                ),
            )
        );
    }

    public function findListByGarageAndPosition( $garage_id , $position_id ){
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = GarageContactStaff.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageContactStaff.garage_id' => $garage_id,
                    'Contact.position_id' => $position_id,
                ),
                'fields' => array(
                    'GarageContactStaff.contact_id'
                ),
            )
        );
    }

    public function getSomeByGarageId( $garage_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = GarageContactStaff.contact_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageContactStaff.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Contact.*'
                ),
                'limit' => 10
            )
        );
    }

    public function findContactsStaffExport($garage_id){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = GarageContactStaff.contact_id',
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
                    'Contact.id'
                ),
                'conditions' => array(
                    'GarageContactStaff.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Contact.*',
                    'ContactTitle.name',
                    'Position.name_' . __l(),
                ),
            )
        );
    }
    public function edit_garage_contact_staff($data){
        $fields = array(
            'GarageContactStaff' => array(
                'id',
                'garage_id',
                'contact_id',
                'interest',
                'priority'
            )
        );

        $garage_contact_staff_bd = $this->guardar($data, $fields);
        if ( !$garage_contact_staff_bd ){
            return false;
        }

        return true;
    }

    public function new_garage_contact_staff( $garage_contact_staff ){
        $fields = array(
            'GarageContactStaff' => array(
                'garage_id',
                'contact_id',
                'interest'
            )
        );
        $this->create();
        $garage_contact_staff_bd = $this->guardar($garage_contact_staff, $fields);
        if ( !$garage_contact_staff_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    public function createGarageContactStaff( $garage_json , $garage_id, &$errors ){ //The data comes from a uk Json left on the server

        if($garage_json['Garage']['Contact'] != ''){
            $contact = ClassRegistry::init('Contact');
            $name = explode(" ", $garage_json['Garage']['Contact']);
            $exist_contact = $contact->findByFirstNameAndLastName($name[0], $name[1]);
            if($exist_contact){
                $garage_contact_tmp = array(
                    'GarageContactStaff' => array(
                        'garage_id' => $garage_id,
                        'contact_id' => $exist_contact['Contact']['id'],
                    )
                );
                $this->create();
                $garage_contact_staff_bd = $this->save($garage_contact_tmp);
                if(!$garage_contact_staff_bd){
                    CakeLog::write('updates', 'The link to the contact could not be created'. PHP_EOL);
					$errors['The link to the contact could not be created'] = translateDataErrors($this->validationErrors);
                }
            }
            else{
                $contact = ClassRegistry::init('Contact');
                $email_exist = $contact->findFirstByEmail($name[0].'.'.$name[1].'@groupauto.co.uk');
                if (!empty($email_exist)) {
                    CakeLog::write('updates', 'The email '.$name[0].'.'.$name[1].'@groupauto.co.uk'.' is already in use by another contact'. PHP_EOL);
					$errors['The email '.$name[0].'.'.$name[1].'@groupauto.co.uk'.' is already in use by another contact'] = translateDataErrors($this->validationErrors);
                    return false;
                }

                $contact_tmp = array(
                    'Contact' => array(
                        'title' => null,
                        'first_name' => $name[0],
                        'last_name' => $name[1],
                        'position_id' => ConstantsPositions::GARAGE_MANAGER_ID,
                        'phone' => null,
                        'email' => $name[0].'.'.$name[1].'@groupauto.co.uk',
                        'identification_number' => 'Uk-'.$garage_id,
                        'creation_date' => date('Y-m-d H:i:s'),
                    )
                );
                $contact = ClassRegistry::init('Contact');
                $contact->create();
                $contact_bd = $contact->save($contact_tmp);
                if(!$contact_bd){
                    CakeLog::write('updates', 'The contact could not be created'. PHP_EOL);
					$errors['The contact could not be created'] = translateDataErrors($contact->validationErrors);
                }

                $garage_contact_tmp = array(
                    'GarageContactStaff' => array(
                        'garage_id' => $garage_id,
                        'contact_id' => $contact_bd['Contact']['id'],
                    )
                );

                $this->create();
                $garage_contact_staff_bd = $this->save($garage_contact_tmp);
                if(!$garage_contact_staff_bd){
                    CakeLog::write('updates', 'The link to the contact could not be created'. PHP_EOL);
					$errors['The link to the contact could not be created'] = translateDataErrors($this->validationErrors);
                }
            }
            $this->commit();
            return $garage_contact_staff_bd;
        }
        else{
            CakeLog::write('updates', 'No contact'. PHP_EOL);
			array_push($errors, 'No contact');
            return true;
        }
    }

    public function updateGarageContactStaff( $garage_json , $exist_garage_id, &$errors ){ //The data comes from a uk Json left on the server
        $this->Contact = ClassRegistry::init('Contact');
        $garage_manager_exist_data =  $this->find(
            'all',
            array(
                'fields' => array(
                    'id',
                    'contact_id'
                ),
                'conditions' => array(
                    'garage_id' => $exist_garage_id
                )
            )
        );
        $garage_manager_exist = array();
        foreach($garage_manager_exist_data as $garage_manager_exist_item){
            $garage_manager_exist[ $garage_manager_exist_item['GarageContactStaff']['id'] ] = $garage_manager_exist_item['GarageContactStaff']['contact_id'];
        }

        if($garage_json['Garage']['Contact'] != '') {

            $name = explode(" ", $garage_json['Garage']['Contact']);
            $exist_contact = $this->Contact->findByFirstNameAndLastName($name[0], $name[1]);

            if( $exist_contact ){
                if(!in_array($exist_contact['Contact']['id'], $garage_manager_exist)){
                    $garage_contact_tmp = array(
                        'GarageContactStaff' => array(
                            'garage_id' => $exist_garage_id,
                            'contact_id' => $exist_contact['Contact']['id'],
                        )
                    );
                    $this->create();
                    if(!$this->save($garage_contact_tmp)){
                        CakeLog::write('updates', 'The link to the contact could not be created'. PHP_EOL);
						$errors['The link to the contact could not be created'] = translateDataErrors($this->validationErrors);
                    }
                }
                else{
                    $key = array_search($exist_contact['Contact']['id'], $garage_manager_exist);
                    unset($garage_manager_exist[$key]);
                }
            }
            else{
                $contact_tmp = array(
                    'Contact' => array(
                        'title' => null,
                        'first_name' => $name[0],
                        'last_name' => $name[1],
                        'position_id' => ConstantsPositions::GARAGE_MANAGER_ID,
                        'phone' => null,
                        'email' => $name[0].'.'.$name[1].'@groupauto.co.uk',
                        'identification_number' => 'Uk-'.$exist_garage_id,
                        'creation_date' => date('Y-m-d H:i:s'),
                    )
                );

                $this->Contact->create();
                $contact_bd = $this->Contact->save($contact_tmp);
                if(!$contact_bd){
                    CakeLog::write('updates', 'The contact could not be created'. PHP_EOL);
					$errors['The contact could not be created'] = translateDataErrors($this->Contact->validationErrors);
                }

                $garage_contact_tmp = array(
                    'GarageContactStaff' => array(
                        'garage_id' => $exist_garage_id,
                        'contact_id' => $contact_bd['Contact']['id'],
                    )
                );
                $this->create();
                if(!$this->save($garage_contact_tmp)){
                    CakeLog::write('updates', 'The link to the contact could not be created'. PHP_EOL);
					$errors['The link to the contact could not be created'] = translateDataErrors($this->validationErrors);
                }
            }
        }
        else{
            CakeLog::write('updates', 'No contact'. PHP_EOL);
			array_push($errors, 'No contact');
            return true;
        }

        //remove garages notes that have not arrived through JSON
        foreach($garage_manager_exist as $key => $contact_id_delete){
            $this->delete($key);
        }

        return true;
    }

    public function getStaffData($garage_id, $contact_id) {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'GarageContactStaff.garage_id' => $garage_id,
                    'GarageContactStaff.contact_id' => $contact_id,
                ),
                'fields' => array(
                    'GarageContactStaff.id',
                    'GarageContactStaff.interest',
                    'GarageContactStaff.priority'
                ),
            )
        );
    }

    public function getStaffGarageIdByStaffId($staff_id) {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'GarageContactStaff.id' => $staff_id,
                ),
                'fields' => array(
                    'GarageContactStaff.garage_id',
                    'GarageContactStaff.contact_id',
                ),
            )
        );
    }

    public function getGarageStaffByIdDelegate($delegate) {
        return $this->find('first', array(
            'conditions' => array(
                'GarageContactStaff.id' => $delegate['TrainingDelegate']['garage_contact_staff_id']
            )
        ));
    }

    public function getNetworkIdByStaffId($staff_id) {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Garage.id = GarageContactStaff.garage_id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageContactStaff.id' => $staff_id,
                ),
                'fields' => array(
                    'Network.id',
                ),
            )
        );
    }

    public function getPriorityChecker($garage_id) {
        return $this->find('count', array(
            'conditions' => array(
                'GarageContactStaff.garage_id' => $garage_id,
                'GarageContactStaff.priority' => 1,
            )
        ));
    }

    public function updateGarageContactStaffByGarageId($garage_id) {
        $conditions = array('garage_id' => $garage_id);
        $fields = array('priority' => 0);
    
        return $this->updateAll($fields, $conditions);
    }

    public function getContactStaffByGarageId($garage_id) {
        return $this->find('first', array(
            'conditions' => array(
                'GarageContactStaff.garage_id' => $garage_id,
                'GarageContactStaff.priority' => 1,
            )
        ));
    }

}
