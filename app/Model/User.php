<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class User extends AppModel
{
    public $virtualFields = array(
        'full_name' => 'CONCAT(User.name, " ", User.surname)'
    );

    public $displayField = 'name';

    public $hasAndBelongsToMany = array(
        'GroupPermission' => array(
            'joinTable' => 'groups_permissions_users',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'group_permission_id',
            'order' => 'user_id ASC',
        ),
        'Permission' => array(
            'joinTable' => 'permissions_users',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'permission_id',
            'order' => 'user_id ASC',
        ),
        'Communication' => array(
            'joinTable' => 'communications_users',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'communication_id',
            'with' => 'CommunicationUser',
        ),
    );

    public $belongsTo = array(
        'Role',
        'Language',
        'Contact',
        'AagRegion'
    );

    public $hasMany = array(
        'UserRecoverPassword',
        'UserImage' => array(
            'foreignKey' => 'user_id',
            'Task',
            'Message',
        ),
    );

    public $validate = array(
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
            'isUnique' => array(
                'rule' => 'validateUniqueFullName',
                'message' => 'Validation.Name_and_Surname_must_be_unique'
            )
        ),
        'surname' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_surname'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Surname_is_too_long',
            ),
            'isUnique' => array(
                'rule' => 'validateUniqueFullName',
                'message' => 'Validation.Name_and_Surname_must_be_unique'
            )
        ),
        'username' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_username'
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Validation.Username_already_exists'
            ),
            'between' => array(
                'rule'    => array('between', 5, 100),
                'message' => 'Validation.Between_5_and_100_characters'
            )
        ),
        'password' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_password'
            ),
            'between' => array(
                'rule'    => array('between', 10, 50),
                'message' => 'Validation.Between_10_and_50_characters'
            ),
            'validateCamposIguales' => array(
                'rule' => array('validateCamposIguales', 'password_repetido'),
                'message' => 'Validation.Passwords_do_not_match'
            ),
            'validatePassword' => array(
                'rule' => array('validatePassword'),
                'message' => 'Validation.Password_must_have',
            ),
        ),
        'role_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_role'
            ),
        ),
        'aag_region_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_region'
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['user'])) {
            $conditions[] = $this->_conditionNameSurnameLogin($fields['user']);
        }
        if (!empty($fields['name'])) {
            $conditions[] = $this->_conditionName($fields['name']);
        }
        if (!empty($fields['surname'])) {
            $conditions[] = $this->_conditionSurname($fields['surname']);
        }
        if (!empty($fields['username'])) {
            $conditions[] = $this->_conditionLogin($fields['username']);
        }
        if (!empty($fields['position_id'])) {
            $conditions[] = $this->_conditionPosition($fields['position_id']);
        }
        if (!empty($fields['role_id'])) {
            $conditions[] = $this->_conditionRole($fields['role_id']);
        }
        if (!empty($fields['aag_region_id'])) {
            $conditions[] = $this->_conditionRegion($fields['aag_region_id']);
        }
        if (isset($fields['active']) && $fields['active'] !== '') {
            $conditions[] = $this->_conditionActive($fields['active']);
        }
        if (!empty($fields['garage_id'])) {
            $conditions[] = $this->_conditionGarage($fields['garage_id']);
        }
        if (!empty($fields['contact_id'])) {
            $conditions[] = $this->_conditionEmail($fields['contact_id']);
        }

        return $conditions;
    }

    private function _conditionNameSurnameLogin($user)
    {
        return array(
            'OR' => array(
                $this->_conditionName($user),
                $this->_conditionSurname($user),
                $this->_conditionLogin($user),
            ),
        );
    }

    private function _conditionName($name)
    {
        return array('User.name LIKE' => '%' . $name . '%');
    }

    private function _conditionSurname($surname)
    {
        return array('User.surname LIKE' => '%' . $surname . '%');
    }

    private function _conditionPosition($position_id)
    {
        return array('Contact.position_id ' => $position_id);
    }

    private function _conditionLogin($username)
    {
        return array('User.username LIKE' => '%' . $username . '%');
    }

    private function _conditionRole($role)
    {
        return array('User.role_id' => $role);
    }

    private function _conditionRegion($region)
    {
        return array('User.aag_region_id' => $region);
    }

    private function _conditionActive($active)
    {
        //If you get a 2, recharge as is.
        if ($active == '1' || $active == '0') {
            return array('User.active' => $active);
        }
    }

    private function _conditionGarage($garage_id)
    {
        return array(
            'OR' => array(
                'GarageStaff.id' => $garage_id,
                'User.garage_id' => $garage_id
            ),
        );
    }

    private function _conditionEmail($contact_id)
    {

        $this->Contact = ClassRegistry::init('Contact');
        $contact = $this->Contact->findById($contact_id);

        return array('Contact.email LIKE' => '%' . $contact['Contact']['email'] . '%');
    }

    private $_queries = array(
        'listing' => array(
            'joins' => array(
                array(
                    'table' => 'garages_contacts_bdm',
                    'alias' => 'GaragesContactsBdm',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GaragesContactsBdm.contact_id = Contact.id'
                    )
                ),
                array(
                    'table' => 'garages',
                    'alias' => 'GarageBdm',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageBdm.id = GaragesContactsBdm.garage_id'
                    )
                ),
                array(
                    'table' => 'garages_contacts_staff',
                    'alias' => 'GaragesContactsStaff',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GaragesContactsStaff.contact_id = Contact.id'
                    )
                ),
                array(
                    'table' => 'garages',
                    'alias' => 'GarageStaff',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageStaff.id = GaragesContactsStaff.garage_id'
                    )
                ),
                array(
                    'table' => 'garages',
                    'alias' => 'GarageContact',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageContact.id = Contact.garage_id'
                    )
                ),
            ),
            'fields' => array(
                'User.name',
                'User.surname',
                'User.username',
                'User.active',
                'User.guid',
                'User.retries',
                'User.full_name',
                'Contact.position_id',
                'Contact.garage_id',
                'Contact.email',
                'GaragesContactsBdm.id',
                'GarageBdm.business_name',
                'GarageBdm.id',
                'GaragesContactsStaff.id',
                'GarageStaff.business_name',
                'GarageStaff.id',
                'Role.*',
                'GarageContact.business_name',
            ),
            'contain' => array(
                'Role',
                'Contact',
                'UserImage',
                'AagRegion',
            ),
            'order' => 'User.name asc, User.surname asc',
            'group' => 'User.id',
        ),
        'getByContactId' => array(
            'joins' => array(
                array(
                    'table' => 'contacts',
                    'alias' => 'Contact',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.contact_id = Contact.id'
                    )
                ),
            ),
            'fields' => array(
                'User.*',
                'Contact.*',
            )
        )
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function beforeSave($options = array())
    {
        if (!empty($this->data[$this->alias]['password'])) {
            $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                $this->data[$this->alias]['password']
            );
        }
        return true;
    }

    public function add($user)
    { //TODO
        $fields = array(
            'User' => array(
                'name',
                'surname',
                'username',
                'password' => 'password',
                'active',
                'role_id',
                'language_id',
                'garage_id',
                'distributor_id',
                'creation_date',
                'contact_id',
                'aag_region_id',
                'guid',
                'country_id'
            )
        );
        $this->Contact = ClassRegistry::init('Contact');
        $contact = $this->Contact->findById($user['User']['contact_id']);

        $user['User']['creation_date'] = date('Y-m-d H:i:s');
        $user['User']['garage_id'] = isset($contact['Contact']['garage_id']) ? $contact['Contact']['garage_id'] : null;
        $user['User']['distributor_id'] = isset($contact['Contact']['distributor_id']) ? $contact['Contact']['distributor_id'] : null;
        $user['User']['guid'] = CakeText::uuid();

        if (isset($user['User']['country_id']) && $user['User']['country_id'] == ConstantsConfigSelect::ALL) {
            $user['User']['country_id'] = null;
        }

        $this->create();
        $this->removePasswordValidationEmpty($user, $fields);
        if ($this->guardar($user, $fields)) {
            // If the user has been saved correctly, he is assigned the default permissions associated with his role
            $this->GroupPermissionRole = ClassRegistry::init('GroupPermissionRole');
            $user['User']['id'] = $this->id;

            if ($this->GroupPermissionRole->assignDefaultPermissionsUsers($user)) {
                return $user;
            } else {
                return false;
            }
        }
    }

    public function new_user_contact($contact_user)
    {
        $fields = array(
            'User' => array(
                'name',
                'surname',
                'username',
                'password' => 'password',
                'active',
                'role_id',
                'language_id',
                'garage_id',
                'distributor_id',
                'garage_type',
                'distributor_type',
                'creation_date',
                'contact_id',
                'aag_region_id',
                'guid',
                'country_id'
            )
        );

        $user['User'] = $contact_user['User'];
        $user['User']['contact_id'] = $contact_user['Contact']['id'];
        if (isset($contact_user['Contact']['garage_id']) && !empty($contact_user['Contact']['garage_id'])) {
            $user['User']['garage_id'] = $contact_user['Contact']['garage_id'];
        } elseif (isset($contact_user['Contact']['distributor_id']) && !empty($contact_user['Contact']['distributor_id'])) {
            $user['User']['distributor_id'] = $contact_user['Contact']['distributor_id'];
        }
        if ($user['User']['language_id'] == 'en') {
            $user['User']['language_id'] = ConstantsLanguages::ENGLISH;
        } elseif ($user['User']['language_id'] == 'fr') {
            $user['User']['language_id'] = ConstantsLanguages::FRENCH;
        } elseif ($user['User']['language_id'] == 'de') {
            $user['User']['language_id'] = ConstantsLanguages::GERMAN;
        }

        $user['User']['creation_date'] = date('Y-m-d H:i:s');

        $user['User']['name'] = ucwords($user['User']['name'], "-");
        $user['User']['name'] = ucwords($user['User']['name']);
        $user['User']['surname'] = mb_strtoupper($user['User']['surname']);

        $user['User']['guid'] = CakeText::uuid();

        if ($user['User']['country_id'] == ConstantsConfigSelect::ALL) {
            $user['User']['country_id'] = null;
        }

        $this->create();
        $this->removePasswordValidationEmpty($user, $fields);
        $user_tmp = $this->guardar($user, $fields);

        if ($user_tmp) {
            // If the user has been saved correctly, he is assigned the default permissions associated with his role
            $this->GroupPermissionRole = ClassRegistry::init('GroupPermissionRole');
            $user['User']['id'] = $this->id;
            return $this->GroupPermissionRole->assignDefaultPermissionsUsers($user);
        }
        return false;
    }

    /**
     * Searches for any user with same name and surname for validation purposes
     */
    public function validateUniqueFullName(array $data)
    {
        $conditions = array(
            'name' => strtolower($this->data[$this->alias]['name']),
            'surname' => strtolower($this->data[$this->alias]['surname'])
        );
        if (!empty($this->id)) {
            // Make sure we exclude the current record.
            $conditions[$this->alias . '.' . $this->primaryKey . ' !='] = $this->id;
        }
        return $this->find('first', array('conditions' => $conditions)) == null;
    }

    public function editGuid($user)
    {

        $fields = array(
            'User' => array(
                'guid'
            )

        );

        $user['User']['guid'] = CakeText::uuid();

        return $this->guardar($user, $fields);
    }


    public function edit($user)
    {
        $fields = array(
            'User' => array(
                'username',
                'password' => 'password', // For the removePasswordValidationEmpty to work (look for a key in the array called 'password')
                'active',
                'language_id',
                'garage_type',
                'distributor_type',
                'aag_region_id',
                'country_id'
            )
        );

        if (isset($user['User']['country_id']) && $user['User']['country_id'] == ConstantsConfigSelect::ALL) {
            $user['User']['country_id'] = null;
        }

        $this->removePasswordValidationEmpty($user, $fields);

        return $this->guardar($user, $fields);
    }

    public function edit_user($user, $position_id, $contact_id)
    {
        $fields = array(
            'User' => array(
                'name',
                'surname',
                'role_id',
                'garage_id',
                'distributor_id',
                'aag_region_id',
                'country_id'
            )
        );

        $userTmp['User']['id'] = $user['User']['id'];

        $this->Contact = ClassRegistry::init('Contact');
        $contact = $this->Contact->findById($contact_id);

        $userName = ucwords($contact['Contact']['first_name'], "-");
        $userName = ucwords($userName);

        $userTmp['User']['name'] = $userName;
        $userTmp['User']['surname'] = $contact['Contact']['last_name'];
        $userTmp['User']['garage_id'] = $contact['Contact']['garage_id'];
        $userTmp['User']['distributor_id'] = $contact['Contact']['distributor_id'];

        $this->Position = ClassRegistry::init('Position');
        $position = $this->Position->findById($position_id);
        $userTmp['User']['role_id'] = $position['Position']['role_id'];

        $userTmp['User']['country_id'] = $user['User']['country_id'] == ConstantsConfigSelect::ALL ? null : $user['User']['country_id'];
        $userTmp['User']['aag_region_id'] = $user['User']['aag_region_id'];

        return $this->guardar($userTmp, $fields);
    }

    public function edit_my_data($user)
    {
        $fields = array(
            'User' => array(
                'username',
                'password' => 'password', // For the removePasswordValidationEmpty to work (look for a key in the array called 'password')
                'language_id',
                'aag_region_id',
                'country_id'
            )
        );

        if ($user['User']['country_id'] == ConstantsConfigSelect::ALL) {
            $user['User']['country_id'] = null;
        }

        $this->removePasswordValidationEmpty($user, $fields);

        $user_bd = $this->guardar($user, $fields);

        if ($user_bd) {
            $principal_image = $this->getPrincipalImage($user['User']['id']);
            if (isset($user['User']['image']) && $user['User']['image'] != null) {
                if (!empty($principal_image)) {
                    if ($user['User']['image-input']['error'] != 0) {
                        //If exist an error. Delete the new crop photo.
                        FileManager::delete_file(WWW_ROOT, ConstantsPath::DIR_USER_IMAGES_CROP . '/' . $user['User']['image']);
                    } else {
                        $this->UserImage->deleteUserImage($principal_image['UserImage']['id']);
                    }
                }
                $this->UserImage->new_image($user, $user['User']['id']);
                $image = $this->UserImage->findByUserId($user['User']['id']);
                if (!empty($image)) {
                    CakeSession::write('Auth.User.img', $image['UserImage']['id']); // T001 SECURITY - It is not changed
                }
            } elseif (isset($user['User']['image']) && $user['User']['image'] == null) {
                //Photo doesn't change
                $image = $this->UserImage->findByUserId($user['User']['id']);
                if (!empty($image)) {
                    CakeSession::read('Auth.User.img');
                } else {
                    CakeSession::write('Auth.User.img', ''); // T001 SECURITY - It is not changed
                }
            } else {
                $this->UserImage->deleteUserImage($principal_image['UserImage']['id']);
                CakeSession::write('Auth.User.img', ''); // T001 SECURITY - It is not changed
            }
        } else {
            //When a failure occurs in a form field
            $file = $this->UserImage->findByUserId($user['User']['id']);
            if (!empty($user['User']['image'])) {
                FileManager::delete_file(WWW_ROOT, ConstantsPath::DIR_USER_IMAGES_CROP . '/' . $user['User']['image']);
            }
        }
        return $user_bd;
    }

    public function getUserByCMS($contact_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'table' => 'contacts',
                    'alias' => 'Contact',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.id = User.contact_id'
                    )
                ),
            ),
            'conditions' => array(
                'OR' => array(
                    'Contact.position_id' => array(
                        ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID,
                        ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID,
                    ),
                    'Contact.id' => $contact_id,
                    'User.role_id' => array(
                        ConstantsRoles::BDM_AAG,
                        ConstantsRoles::BDM_TG,
                    ),
                )
            ),
            'order' => array(
                'User.full_name'
            ),
            'fields' => array(
                'User.id',
                'User.full_name'
            )
        ));
    }

    public function getContactByCMS($contact_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'table' => 'contacts',
                    'alias' => 'Contact',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.id = User.contact_id'
                    )
                ),
            ),
            'conditions' => array(
                'OR' => array(
                    'Contact.position_id' => array(
                        ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID,
                        ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID,
                    ),
                    'Contact.id' => $contact_id,
                    'User.role_id' => array(
                        ConstantsRoles::BDM_AAG,
                        ConstantsRoles::BDM_TG,
                    ),
                )
            ),
            'order' => array(
                'User.full_name'
            ),
            'fields' => array(
                'Contact.id',
                'User.full_name'
            )
        ));
    }

    public function getUserByRSM($regions)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'table' => 'contacts_regions',
                    'alias' => 'ContactRegion',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ContactRegion.contact_id = User.contact_id'
                    )
                ),
            ),
            'conditions' => array(
                'ContactRegion.region_id' => $regions
            ),
            'order' => array(
                'User.full_name'
            ),
            'fields' => array(
                'User.id',
                'User.full_name'
            )
        ));
    }

    public function getContactByRSM($regions)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'table' => 'contacts',
                    'alias' => 'Contact',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.id = User.contact_id'
                    )
                ),
                array(
                    'table' => 'contacts_regions',
                    'alias' => 'ContactRegion',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ContactRegion.contact_id = User.contact_id'
                    )
                ),
            ),
            'conditions' => array(
                'ContactRegion.region_id' => $regions
            ),
            'order' => array(
                'User.full_name'
            ),
            'fields' => array(
                'Contact.id',
                'User.full_name'
            )
        ));
    }

    public function uploadProfileImage($image_data, $imageFile)
    {
        $res = false;
        if (
            $imageFile['error'] != ConstantsFlag::ERROR_NOT_FILE &&
            in_array(mime_content_type($image_data), array('image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp'))
        ) {
            $imageFile['source'] = file_get_contents($image_data);
            $imageFile['name'] = FileManager::get_renamed_name(preg_replace("/[^a-zA-Z0-9.]/", "", $imageFile['name']));

            $imageTiny = FileManager::tiny_to_image($imageFile);
            $imageFile['name'] = $imageTiny;

            $img2 = imagecreatefromwebp($imageTiny);

            if (imagewebp($img2, ConstantsFilePaths::PROFILE_IMAGES_ABSOLUTE . $imageFile['name'], ConstantsImageParams::IMAGE_QUALITY)) {
                imagedestroy($img2);
                if (FileManager::upload_file(
                    ConstantsFilePaths::PROFILE_IMAGES_ABSOLUTE . $imageFile['name'],
                    ConstantsFilePaths::PROFILE_IMAGES_RELATIVE,
                    $imageFile['name'],
                    ConstantsFileType::IMAGE
                )) {
                    $res = $imageFile['name'];
                }
                unlink($imageFile['name']);
            }
        }

        return $res;
    }

    public function removePasswordValidationEmpty($user, &$fields)
    {
        if (empty($user['User']['password'])) {
            unset($fields['User']['password']);
            $this->validator()->remove('password');
        }
    }

    public function findUsersWhithEmail($user)
    {
        $this->Contact = ClassRegistry::init('Contact');
        $contacts = $this->Contact->findAllByEmail($user['User']['email']);

        $users = array();
        foreach ($contacts as $contact) {
            $user = $this->findByContactId($contact['Contact']['id']);
            if (!empty($user) && $user['User']['active'] == ConstantsBooleans::ACTIVE) {
                $users[] = $user;
            }
        }

        return $users;
    }

    public function getPrincipalImage($user_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'table' => 'users_images',
                        'alias' => 'UserImage',
                        'type' => 'INNER',
                        'conditions' => array(
                            'UserImage.user_id = User.id'
                        )
                    ),
                ),
                'conditions' => array(
                    'User.id' => $user_id,
                ),
                'fields' => array(
                    'UserImage.*',
                )
            )
        );
    }

    public function getUsersByContact($contact_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'table' => 'users_images',
                        'alias' => 'UserImage',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'UserImage.user_id = User.id'
                        )
                    ),
                ),
                'conditions' => array(
                    'User.contact_id' => $contact_id,
                ),
                'fields' => array(
                    'User.*',
                    'UserImage.*',
                )
            )
        );
    }

    public function getUsersByContacts($contacts)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'table' => 'contacts',
                        'alias' => 'Contact',
                        'type' => 'INNER',
                        'conditions' => array(
                            'User.contact_id = Contact.id'
                        )
                    ),
                ),
                'conditions' => array(
                    'User.contact_id' => $contacts,
                ),
                'fields' => array(
                    'User.*',
                    'Contact.*',
                )
            )
        );
    }

    public function changePassword($password, $user_id)
    {
        $fields = array(
            'User' => array(
                'id',
                'password',
                'password_repetido',
                'set_login',
            )
        );
        $password['User']['id'] = $user_id;
        $password['User']['set_login'] = date('Y-m-d H:i:s');

        $password = $this->guardar($password, $fields);
        if ($password) {
            $this->UserRecoverPassword->deleteAll(array(
                'user_id' => $user_id
            ));
            return $password;
        } else {
            return false;
        }
    }

    public function listCompleteNameRegion($aag_region_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'User.aag_region_id' => $aag_region_id
                ),
                'fields' => array(
                    'User.full_name',
                ),
                'order' => array(
                    'full_name'
                )
            )
        );
    }

    public function getCompleteName($user_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'User.id' => $user_id,
                ),
                'fields' => array(
                    'User.full_name',
                ),
            )
        );
    }

    public function getUsersByContactListId($contact_list_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'ContactContactList',
                    'table' => 'contacts_contacts_lists',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.contact_id = ContactContactList.contact_id',
                    ),
                ),
                array(
                    'alias' => 'ContactList',
                    'table' => 'contacts_lists',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ContactList.id = ContactContactList.contact_list_id',
                    ),
                ),
            ),
            'conditions' => array(
                'ContactList.id' => $contact_list_id
            ),
            'fields' => array(
                'User.id',
                'User.full_name'
            )
        ));
    }

    public function viewUserGarages($user)
    {
        $role_id = $user['role_id'];
        $garage_ids = array();

        if ($role_id == ConstantsRoles::GARAGE) {
            $this->Garage = ClassRegistry::init('Garage');
            $garage = $this->Garage->findById(CakeSession::read('Auth.User.garage_id'));
            $garage_ids = array('Garage.id' => $garage['Garage']['id']);
            return $garage_ids;
        } elseif ($role_id == ConstantsRoles::DISTRIBUTOR) {
            $this->GarageDistributor = ClassRegistry::init('GarageDistributor');
            $garages = $this->GarageDistributor->findAllByDistributorId(CakeSession::read('Auth.User.distributor_id'));
            $garage_ids = array('Garage.id' => Hash::extract($garages, '{n}.GarageDistributor.garage_id'));

            $this->Distributor = ClassRegistry::init('Distributor');
            if ($branches = $this->Distributor->findAllByDistributorId(CakeSession::read('Auth.User.distributor_id'))) {
                $distributor_ids = array('Distributor.id' => Hash::extract($branches, '{n}.Distributor.id'));
                foreach ($distributor_ids as $distributor_id) {
                    $garages_tmp = Hash::extract($this->GarageDistributor->findAllByDistributorId($distributor_id), '{n}.GarageDistributor.garage_id');
                    foreach ($garages_tmp as $garage) {
                        if (!in_array($garage, $garage_ids['Garage.id'])) {
                            $garage_ids['Garage.id'][] = $garage;
                        }
                    }
                }
            }

            return $garage_ids;
        }

        $garages_permissions = CakeSession::read('Auth.User.Permissionsv2.networks_regions');
        $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
        $this->Garage = ClassRegistry::init('Garage');

        $garages_networks_regions = array();
        $network_region_check = true;
        if (isset($garages_permissions)) {
            foreach ($garages_permissions as $network_id => $regions) {
                foreach ($regions as $region_id => $permission) {
                    if ($network_id && $region_id) {
                        $network_region_check = false;
                        if ($network_id != -1) { //Without network
                            $garages_tmp = Hash::extract($this->Garage->getAllNetworkIdAndRegionId($network_id), '{n}.Garage.id');
                        }

                        foreach ($garages_tmp as $garage) {
                            if (!in_array($garage, $garage_ids)) {
                                $garages_networks_regions[] = array($garage);
                            }
                        }
                    } elseif (!$network_id && $region_id) {
                        $garages_networks_regions[] = Hash::extract($this->Garage->findAllBySalesAreaId($region_id), '{n}.Garage.id');
                    } elseif ($network_id && !$region_id) {
                        if ($network_id == -1) { //Without network
                            $garages_networks_regions[] = Hash::extract($this->Garage->getGarageWithOutNetwork(), '{n}.Garage.id');
                        } else {
                            $garages_networks_regions[] = Hash::extract($this->GarageNetwork->findAllByNetworkId($network_id), '{n}.GarageNetwork.garage_id');
                        }
                    }
                }
            }
        }

        if (!empty($garages_networks_regions)) {
            $position_id = $user['Contact']['position_id'];
            $this->PositionConfig = ClassRegistry::init('PositionConfig');
            $this->PositionConfigBdm = ClassRegistry::init('PositionConfigBdm');
            $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');

            $position_configs = $this->PositionConfig->findAllByPositionIdAndPositionConfigTypeId($position_id, ConstantsPositionConfigType::GARAGE);
            $bdms = array();
            $garages_bdms = array();
            foreach ($position_configs as $config) {
                $bdms[] = $this->PositionConfigBdm->getPositionConfigBdmId($config['PositionConfig']['id']);
            }

            foreach ($bdms as $config_bdms) {
                foreach ($config_bdms as $bdm) {
                    $user = $this->findById($bdm['PositionConfigBdm']['user_id']);
                    $garages_tmp = $this->GarageContactBdm->getGarageByBDM($user['User']['contact_id']);
                    $garages_bdms[] = Hash::extract($garages_tmp, '{n}.GarageContactBdm.garage_id');
                }
            }
            $garages_bdms = Hash::extract($garages_bdms, '{n}.{n}');

            foreach ($garages_bdms as $garage) {
                if (!in_array($garage, $garages_networks_regions[0])) {
                    $garages_networks_regions[0][] = $garage;
                }
            }
        }

        if (empty($garages_networks_regions) && $network_region_check) {
            return array();
        } elseif (empty($garages_networks_regions) && !$network_region_check) {
            return array('Garage.id' => null);
        } else {
            return array('Garage.id' => Hash::extract($garages_networks_regions, '{n}.{n}'));
        }
    }

    public function viewUserMyBranches($user)
    {
        $role_id = $user['role_id'];
        $distributor_ids = array();
        if ($role_id == ConstantsRoles::DISTRIBUTOR) {
            $this->Distributor = ClassRegistry::init('Distributor');
            $branches = $this->Distributor->findAllByDistributorId(CakeSession::read('Auth.User.distributor_id'));
            $distributor_ids = array('Distributor.id' => Hash::extract($branches, '{n}.Distributor.id'));
            return $distributor_ids;
        }
        return $distributor_ids;
    }

    public function setRetries($user, $retry, $unlock = false)
    {
        $fields = array(
            'User' => array(
                'id',
                'retries',
                'last_login'
            )
        );

        $user_tmp['User'] = array(
            'id' => $user['id'],
            'retries' => $retry,
            'last_login' => $user['last_login'],
        );

        if ($retry == ConstantsBooleans::NO && !$unlock) {
            $user_tmp['User']['last_login'] = date('Y-m-d H:i:s');
        }

        return $this->guardar($user_tmp, $fields);
    }

    public function viewUserDistributors($user)
    {
        $role_id = $user['role_id'];
        $distributor_ids = array();

        if ($role_id == ConstantsRoles::GARAGE) {
            $this->GarageDistributor = ClassRegistry::init('GarageDistributor');
            $distributors = $this->GarageDistributor->findAllByGarageId(CakeSession::read('Auth.User.garage_id'));
            $garage_ids = array('Garage.id' => Hash::extract($distributors, '{n}.GarageDistributor.distributor_id')); //TODO
            return $distributor_ids;
        } elseif ($role_id == ConstantsRoles::DISTRIBUTOR) {
            $this->Distributor = ClassRegistry::init('Distributor');
            $distributor = $this->Distributor->findById(CakeSession::read('Auth.User.distributor_id'));
            $distributor_ids = array('Distributor.id' => $distributor['Distributor']['id']);
            return $distributor_ids;
        }

        $distributor_permissions = CakeSession::read('Auth.User.Permissionsv2.trading_groups');

        if (isset($distributor_permissions[0])) {
        } else {
            $this->Distributor = ClassRegistry::init('Distributor');

            if (!strpos(CakeSession::read('Auth.User.position'), 'ommercial ') && !strpos(CakeSession::read('Auth.User.position'), 'BDM ')) { // Eventual para Francia
                if (isset($distributor_permissions)) {

                    foreach ($distributor_permissions as $key => $trading_group) {
                        $distributors = $this->Distributor->findAllByTradingGroupId($key);
                        $distributor_ids_permmission = array('Distributor.id' => Hash::extract($distributors, '{n}.Distributor.id'));

                        if (isset($distributor_ids['Distributor.id']) || empty($distributor_ids['Distributor.id'])) {
                            foreach ($distributor_ids_permmission['Distributor.id'] as $distributor_tmp) {
                                if (!in_array($distributor_tmp, $distributor_ids)) {
                                    $distributor_ids['Distributor.id'][] = $distributor_tmp;
                                }
                            }
                        } else {
                            foreach ($distributor_ids_permmission['Distributor.id'] as $distributor_tmp) {
                                if (!in_array($distributor_tmp, $distributor_ids)) {
                                    $array_distributor_tmp[] = $distributor_tmp;
                                }
                            }
                            $array_distributor_tmp_id = array_intersect($distributor_ids['Distributor.id'], $array_distributor_tmp);
                        }
                    }
                    if (isset($array_distributor_tmp_id)) {
                        $distributor_ids['Distributor.id'] = array_intersect($distributor_ids['Distributor.id'], $array_distributor_tmp_id);
                    }
                }
            }
        }

        if (!empty($distributor_ids)) {
            $position_id = $user['Contact']['position_id'];
            $this->PositionConfig = ClassRegistry::init('PositionConfig');
            $this->PositionConfigBdm = ClassRegistry::init('PositionConfigBdm');
            $this->DistributorContactBdm = ClassRegistry::init('DistributorContactBdm');

            $position_configs = $this->PositionConfig->findAllByPositionIdAndPositionConfigTypeId($position_id, ConstantsPositionConfigType::DISTRIBUTOR);
            $bdms = array();
            $distributors_bdms = array();

            foreach ($position_configs as $config) {
                $bdms[] = $this->PositionConfigBdm->getPositionConfigBdmId($config['PositionConfig']['id']);
            }

            foreach ($bdms as $config_bdms) {
                foreach ($config_bdms as $bdm) {
                    $user = $this->findById($bdm['PositionConfigBdm']['user_id']);
                    $distributors_tmp = $this->DistributorContactBdm->getDistributorsByBDM($user['User']['contact_id']);
                    $distributors_bdms[] = Hash::extract($distributors_tmp, '{n}.DistributorContactBdm.distributor_id');
                }
            }

            $distributors_bdms = Hash::extract($distributors_bdms, '{n}.{n}');

            foreach ($distributors_bdms as $distributor) {
                if (!in_array($distributor, $distributor_ids)) {
                    $distributor_ids['Distributor.id'][] = $distributor;
                }
            }
        }
        return $distributor_ids;
    }

    public function viewUserAppointments($user)
    {
        $role_id = $user['role_id'];
        $aag_region_id = $user['aag_region_id'];

        if ($role_id == ConstantsRoles::GPC_LOGISTICS_BDM) {
            $this->Appointment = ClassRegistry::init('Appointment');
            $users = $this->getListBDMGPC($aag_region_id);
            $appointments_tmp = array();
            foreach ($users as $key => $user) {
                $appointments_user_children = array();
                $appointments_user_children = $this->Appointment->findAllByUserAssignedId($key);
                $appointments_tmp = $appointments_tmp + $appointments_user_children;
            }
            $appointments = array('Appointment.id' => Hash::extract($appointments_tmp, '{n}.Appointment.id'));
            return $appointments;
        } elseif ($role_id == ConstantsRoles::BDM_AAG || $role_id == ConstantsRoles::BDM_TG) {
            $this->Appointment = ClassRegistry::init('Appointment');
            $appointments_user = $this->Appointment->findAllByUserAssignedId($user['id']);
            $appointments = array('Appointment.id' => Hash::extract($appointments_user, '{n}.Appointment.id'));

            $this->Contact = ClassRegistry::init('Contact');
            $users_children = $this->Contact->getChildren($user['contact_id']);
            if ($users_children) {
                $this->Appointment = ClassRegistry::init('Appointment');
                $appointments_tmp = array();
                foreach ($users_children as $key => $user) {
                    $appointments_user_children = array();
                    $appointments_user_children = $this->Appointment->findAllByUserAssignedId($key);
                    $appointments_tmp = $appointments_tmp + $appointments_user_children;
                }

                $appointments_tmp = array('Appointment.id' => Hash::extract($appointments_tmp, '{n}.Appointment.id'));
                $tmp['Appointment.id'] = array_merge($appointments['Appointment.id'], $appointments_tmp['Appointment.id']);
                return $tmp;
            }
            return $appointments;
        } elseif ($user['Contact']['position_id'] == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID || $user['Contact']['position_id'] == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID) {
            $this->Appointment = ClassRegistry::init('Appointment');
            $appointments_user = $this->Appointment->findAllByUserAssignedId($user['id']);
            return array('Appointment.id' => Hash::extract($appointments_user, '{n}.Appointment.id'));
        }

        return array();
    }

    public function getUserByCode($bdm_code, $roles)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'INNER',
                    'conditions' => 'Contact.id = User.contact_id'
                ),
            ),
            'conditions' => array(
                array(
                    'User.role_id' => $roles,
                    'Contact.identification_number' => $bdm_code,
                )
            ),
            'fields' => array(
                'User.id',
                'User.full_name',
            ),
        ));
    }

    public function getBDMUsers()
    {
        return $this->find(
            'list',
            array(

                'conditions' => array(
                    'Or' => array(
                        array(
                            'User.role_id' => ConstantsRoles::BDM_AAG,
                        ),
                        array(
                            'User.role_id' => ConstantsRoles::BDM_TG,
                        )
                    )
                ),
                'fields' => array(
                    'User.id',
                    'User.full_name'
                ),
                'order' => (
                    'User.full_name'
                )
            )
        );
    }

    public function getBDMGPCUsersId()
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    array(
                        'User.role_id' => ConstantsRoles::GPC_LOGISTICS_BDM,
                    ),
                ),
                'fields' => array(
                    'User.id',
                    'User.id'
                ),
            )
        );
    }

    public function getListBDMGPC($aag_region_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'User.role_id' => ConstantsRoles::GPC_LOGISTICS_BDM,
                    'User.aag_region_id' => $aag_region_id
                ),
                'fields' => array(
                    'User.id',
                    'User.full_name'
                ),
                'order' => array(
                    'full_name'
                )
            )
        );
    }

    public function getBDMUsersId()
    {
        $bdms = $this->find(
            'all',
            array(
                'conditions' => array(
                    'Or' => array(
                        array(
                            'User.role_id' => ConstantsRoles::BDM_AAG,
                        ),
                        array(
                            'User.role_id' => ConstantsRoles::BDM_TG,
                        )
                    )
                ),
                'fields' => array(
                    'group_concat(distinct User.id separator ",") as bdms',
                )
            )
        );

        return Hash::extract($bdms, '{n}.{n}');
    }

    public function findDataUser($user_guid, $aagRegionId)
    {
        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'INNER',
                    'conditions' => 'Contact.id = User.contact_id'
                ),
            ),
            'conditions' => array(
                array(
                    'User.guid' => array($user_guid),
                    'User.aag_region_id' => $aagRegionId,
                )
            ),
            'fields' => array(
                'User.*',
                'Contact.*',
            ),
        ));
    }

    /**
     * Saves the permission groups to which the user belongs
     */
    public function editDataGroupPermissionsUser($group_permission_user)
    {
        // Pass an empty array so that no errors pop up.
        // You have to do this even if the data is not saved in the User table but in groups_permissions_users.
        $fields = array(
            'User' => array(
                'id',
            )
        );

        return $this->guardar($group_permission_user, $fields);
    }

    /**
     * It includes in the load information of the user permissions matrix, those permissions that have been added / removed to the indicated user.
     */
    public function completeInformationMatrixPermissions($matriz_permissions)
    {

        $this->PermissionUser = ClassRegistry::init('PermissionUser');

        $permissions_extra = array();

        // Permits are managed specifically and individually for the user
        $user_specific_permissions = $this->PermissionUser->getPermissionsUsers(null, $matriz_permissions['User']['id']);

        foreach ($user_specific_permissions as $specific_permission) {

            if ($specific_permission['PermissionUser']['exclude_permission'] == false) {
                $permissions_extra[ConstantsPrefixNameFieldPermit::PERMITIR . '_' . $specific_permission['PermissionUser']['permission_id']] = true;
            } elseif ($specific_permission['PermissionUser']['exclude_permission'] == true) {
                $permissions_extra[ConstantsPrefixNameFieldPermit::DENEGAR . '_' . $specific_permission['PermissionUser']['permission_id']] = true;
            }
        }

        $matriz_permissions['CustomPermission'] = $permissions_extra;

        return $matriz_permissions;
    }

    /**
     * Obtiene los distintos permisos necesarios para poder gestionar los permisos del usuario.
     * Se devuelven por referencia los distintos arrays.
     */
    public function getMatrixPermissionsUser($user_id)
    {
        $this->GroupPermission = ClassRegistry::init('GroupPermission');
        $this->GroupPermissionUser = ClassRegistry::init('GroupPermissionUser');

        // Get all permissions (all or those corresponding to the version)
        $permisos = $this->GroupPermission->Permission->getPermissions();

        // Get the permissions that are held due to the user's workgroups
        $permisos_del_usuario_ids = $this->GroupPermissionUser->obtenerPermisosGrupoPermisosDelUsuario($user_id);

        // Complete the general information of the permissions with the corresponding information from the permissions due to the user’s group permissions
        foreach ($permisos as &$permiso) {
            foreach ($permisos_del_usuario_ids as $permission_id) {
                if ($permiso['Permission']['id'] == $permission_id) {
                    $permiso['BelongsGroup']['value'] = ConstantsBooleans::YES;

                    break;
                }
            }
        }

        return $permisos;
    }

    /**
     * Similar to getMatrixPermissionsUser but with one more parameter.
     */
    public function getMatrixPermissionsUserByGroupPermission($user_id, $group_permission_id)
    {
        $this->GroupPermission = ClassRegistry::init('GroupPermission');
        $this->GroupPermissionUser = ClassRegistry::init('GroupPermissionUser');

        // Get all permissions (all or those corresponding to the version)
        $group_permission = $this->GroupPermission->findById($group_permission_id);
        $permisos = $this->GroupPermission->Permission->getPermissionsByType($group_permission['GroupPermission']['position_config_type_id']);

        // Get the permissions that are held due to the user's workgroups
        $permisos_del_usuario_ids = $this->GroupPermissionUser->obtenerPermisosGrupoPermisosDelUsuario($user_id, $group_permission_id);

        // Complete the general information of the permissions with the corresponding information from the permissions due to the user’s group permissions
        foreach ($permisos as &$permiso) {
            foreach ($permisos_del_usuario_ids as $permission_id) {
                if ($permiso['Permission']['id'] == $permission_id) {
                    $permiso['BelongsGroup']['value'] = ConstantsBooleans::YES;
                    break;
                }
            }
        }

        return $permisos;
    }

    /**
     * Saves in the database the custom permissions assigned to the user.
     */
    public function saveMatrixPermissionsUser($matriz_permissions)
    {

        $this->PermissionUser = ClassRegistry::init('PermissionUser');

        $fields = array(
            'PermissionUser' => array(
                'permission_id',
                'user_id',
                'exclude_permission',
            )
        );

        $matriz_final = array();

        // All the custom permissions are crossed to create an array in which they are collected, indicating if it is to "permit" or "deny".
        if (isset($matriz_permissions['CustomPermission'])) {

            foreach ($matriz_permissions['CustomPermission'] as $key => $value) {

                $permission = array();

                // The name of the field will be obtained if it is to allow / deny and the id of the permission.
                $permissions_array = explode('_', $key);

                if (strpos($permissions_array[0], ConstantsPrefixNameFieldPermit::PERMITIR) !== false) {
                    $permission['permission_id'] = $permissions_array[1];
                    $permission['user_id'] = $matriz_permissions['User']['id'];
                    $permission['exclude_permission'] = ConstantsBooleans::NO;
                } elseif (strpos($permissions_array[0], ConstantsPrefixNameFieldPermit::DENEGAR) !== false) {
                    $permission['permission_id'] = $permissions_array[1];
                    $permission['user_id'] = $matriz_permissions['User']['id'];
                    $permission['exclude_permission'] = ConstantsBooleans::YES;
                }

                if (!empty($permission)) {
                    $matriz_final[] = $permission;
                }
            }
        }

        // All the specific permissions that the user can have are deleted and new ones that have been provided are saved
        $this->PermissionUser->deleteAll(
            array(
                'PermissionUser.user_id' => $matriz_permissions['User']['id']
            ),
            false
        );

        if (!empty($matriz_final)) {
            return $this->PermissionUser->saveMany($matriz_final, $fields);
        } else {
            return true;
        }
    }

    public function check_delete($user_id)
    {
        /**
         * The following classes are no longer taken into account:
         * -> PermissionUser
         * -> UserRecoverPassword
         * -> GroupPermissionUser
         * -> Alert
         * -> UserImage
         */
        $class_array = array(
            'TaskUser' => $TaskUser = ClassRegistry::init('TaskUser'),
            'ContactList' => $ContactList = ClassRegistry::init('ContactList'),
            'Tutorial' => $Tutorial = ClassRegistry::init('Tutorial'),
            'Communication' => $Communication = ClassRegistry::init('Communication'),
            'Distributor' => $Distributor = ClassRegistry::init('Distributor'),
            'Garage' => $Garage = ClassRegistry::init('Garage'),
            'DistributorComment' => $DistributorComment = ClassRegistry::init('DistributorComment'),
            'GarageComment' => $GarageComment = ClassRegistry::init('GarageComment'),
            'AppointmentComment' => $AppointmentComment = ClassRegistry::init('AppointmentComment'),
            'LogChange' => $LogChange = ClassRegistry::init('LogChange'),
        );

        $class_array_assgined = array(
            'Appointment' => $Appointment = ClassRegistry::init('Appointment'),
            'Route' => $Route = ClassRegistry::init('Route'),
            'Task' => $Task = ClassRegistry::init('Task')
        );

        $class_array_creation = array(
            'Route' => $Route = ClassRegistry::init('Route'),
            'Task' => $Task = ClassRegistry::init('Task')
        );

        foreach ($class_array as $key => $Class) {
            $tmp = $Class->findByUserId($user_id);
            if (!empty($tmp)) {
                return $key; //Return type of error. Class.
            }
        }
        foreach ($class_array_assgined as $key => $Class) {
            $tmp = $Class->findByUserAssignedId($user_id);
            if (!empty($tmp)) {
                return $key; //Return type of error. Class.
            }
        }
        foreach ($class_array_creation as $key => $Class) {
            $tmp = $Class->findByUserCreationId($user_id);
            if (!empty($tmp)) {
                return $key; //Return type of error. Class.
            }
        }


        return true;
    }

    /**
     * Put data into users_reassigment.
     * find in tables by user_assigned_id or contact_id
     */
    public function assigned_old_values_users_reassignements($data)
    {
        $flag = 0;
        $origin_id = $data['User']['user_origin'];
        $destination_id = $data['User']['user_destination'];
        $permanent = $data['UserReassignment']['permanent'];
        $data_from = $data['UserReassignment']['date_from'];
        $data_to = $data['UserReassignment']['date_to'];

        $this->UserReassignment = ClassRegistry::init('UserReassignment');
        $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');
        $this->DistributorContactBdm = ClassRegistry::init('DistributorContactBdm');
        $this->ContactContactList = ClassRegistry::init('ContactContactList');
        $this->Task = ClassRegistry::init('Task');
        $this->Appointment = ClassRegistry::init('Appointment');
        $this->Route = ClassRegistry::init('Route');

        $user_tmp = $this->findById($origin_id);
        $contact_id = $user_tmp['User']['contact_id'];

        $garages_contacts_bdm = $this->GarageContactBdm->findAllByContactId($contact_id);
        if (!empty($garages_contacts_bdm)) {
            $users_reassignment = array();
            foreach ($garages_contacts_bdm as $garage) {
                $user_reassignment['UserReassignment']['user_id_origin'] = $origin_id;
                $user_reassignment['UserReassignment']['user_id_destination'] = $destination_id;
                $user_reassignment['UserReassignment']['garage_contact_bdm_id'] = $garage['GarageContactBdm']['id'];
                $user_reassignment['UserReassignment']['distributor_contact_bdm_id'] = null;
                $user_reassignment['UserReassignment']['contact_contact_list_id'] = null;
                $user_reassignment['UserReassignment']['task_id'] = null;
                $user_reassignment['UserReassignment']['appointment_id'] = null;
                $user_reassignment['UserReassignment']['route_id'] = null;
                $user_reassignment['UserReassignment']['date_from'] = Fecha::toFormatoBd($data_from);
                $user_reassignment['UserReassignment']['date_to'] = Fecha::toFormatoBd($data_to);
                $user_reassignment['UserReassignment']['active'] = ConstantsBooleans::NO;
                $user_reassignment['UserReassignment']['permanent'] = $permanent;
                $users_reassignment[] = $user_reassignment;
                $flag = true;
            }
            if (!$this->UserReassignment->saveMany($users_reassignment)) {
                return false;
            }
        }

        $distributors_contacts_bdm = $this->DistributorContactBdm->findAllByContactId($contact_id);
        if (!empty($distributors_contacts_bdm)) {
            $users_reassignment = array();
            foreach ($distributors_contacts_bdm as $distributor) {
                $user_reassignment['UserReassignment']['user_id_origin'] = $origin_id;
                $user_reassignment['UserReassignment']['user_id_destination'] = $destination_id;
                $user_reassignment['UserReassignment']['garage_contact_bdm_id'] = null;
                $user_reassignment['UserReassignment']['distributor_contact_bdm_id'] = $distributor['DistributorContactBdm']['id'];
                $user_reassignment['UserReassignment']['contact_contact_list_id'] = null;
                $user_reassignment['UserReassignment']['task_id'] = null;
                $user_reassignment['UserReassignment']['appointment_id'] = null;
                $user_reassignment['UserReassignment']['route_id'] = null;
                $user_reassignment['UserReassignment']['date_from'] = Fecha::toFormatoBd($data_from);
                $user_reassignment['UserReassignment']['date_to'] = Fecha::toFormatoBd($data_to);
                $user_reassignment['UserReassignment']['active'] = ConstantsBooleans::NO;
                $user_reassignment['UserReassignment']['permanent'] = $permanent;
                $users_reassignment[] = $user_reassignment;
                $flag = true;
            }
            if (!$this->UserReassignment->saveMany($users_reassignment)) {
                return false;
            }
        }

        $contacts_contacts_list = $this->ContactContactList->findAllByContactId($contact_id);
        if (!empty($contacts_contacts_list)) {
            $users_reassignment = array();
            foreach ($contacts_contacts_list as $contact_list) {
                $user_reassignment['UserReassignment']['user_id_origin'] = $origin_id;
                $user_reassignment['UserReassignment']['user_id_destination'] = $destination_id;
                $user_reassignment['UserReassignment']['garage_contact_bdm_id'] = null;
                $user_reassignment['UserReassignment']['distributor_contact_bdm_id'] = null;
                $user_reassignment['UserReassignment']['contact_contact_list_id'] = $contact_list['ContactContactList']['id'];
                $user_reassignment['UserReassignment']['task_id'] = null;
                $user_reassignment['UserReassignment']['appointment_id'] = null;
                $user_reassignment['UserReassignment']['route_id'] = null;
                $user_reassignment['UserReassignment']['date_from'] = Fecha::toFormatoBd($data_from);
                $user_reassignment['UserReassignment']['date_to'] = Fecha::toFormatoBd($data_to);
                $user_reassignment['UserReassignment']['active'] = ConstantsBooleans::NO;
                $user_reassignment['UserReassignment']['permanent'] = $permanent;
                $users_reassignment[] = $user_reassignment;
                $flag = true;
            }
            if (!$this->UserReassignment->saveMany($users_reassignment)) {
                return false;
            }
        }

        $tasks = $this->Task->findAllByUserAssignedId($origin_id);
        if (!empty($tasks)) {
            $users_reassignment = array();
            foreach ($tasks as $task) {
                $user_reassignment['UserReassignment']['user_id_origin'] = $origin_id;
                $user_reassignment['UserReassignment']['user_id_destination'] = $destination_id;
                $user_reassignment['UserReassignment']['garage_contact_bdm_id'] = null;
                $user_reassignment['UserReassignment']['distributor_contact_bdm_id'] = null;
                $user_reassignment['UserReassignment']['contact_contact_list_id'] = null;
                $user_reassignment['UserReassignment']['task_id'] = $task['Task']['id'];
                $user_reassignment['UserReassignment']['appointment_id'] = null;
                $user_reassignment['UserReassignment']['route_id'] = null;
                $user_reassignment['UserReassignment']['date_from'] = Fecha::toFormatoBd($data_from);
                $user_reassignment['UserReassignment']['date_to'] = Fecha::toFormatoBd($data_to);
                $user_reassignment['UserReassignment']['active'] = ConstantsBooleans::NO;
                $user_reassignment['UserReassignment']['permanent'] = $permanent;
                $users_reassignment[] = $user_reassignment;
                $flag = true;
            }
            if (!$this->UserReassignment->saveMany($users_reassignment)) {
                return false;
            }
        }

        $appointments = $this->Appointment->findAllByUserAssignedId($origin_id);
        if (!empty($appointments)) {
            $users_reassignment = array();
            foreach ($appointments as $appointment) {
                $user_reassignment['UserReassignment']['user_id_origin'] = $origin_id;
                $user_reassignment['UserReassignment']['user_id_destination'] = $destination_id;
                $user_reassignment['UserReassignment']['garage_contact_bdm_id'] = null;
                $user_reassignment['UserReassignment']['distributor_contact_bdm_id'] = null;
                $user_reassignment['UserReassignment']['contact_contact_list_id'] = null;
                $user_reassignment['UserReassignment']['task_id'] = null;
                $user_reassignment['UserReassignment']['appointment_id'] = $appointment['Appointment']['id'];
                $user_reassignment['UserReassignment']['route_id'] = null;
                $user_reassignment['UserReassignment']['date_from'] = Fecha::toFormatoBd($data_from);
                $user_reassignment['UserReassignment']['date_to'] = Fecha::toFormatoBd($data_to);
                $user_reassignment['UserReassignment']['active'] = ConstantsBooleans::NO;
                $user_reassignment['UserReassignment']['permanent'] = $permanent;
                $users_reassignment[] = $user_reassignment;
                $flag = true;
            }
            if (!$this->UserReassignment->saveMany($users_reassignment)) {
                return false;
            }
        }

        $routes = $this->Route->findAllByUserAssignedId($origin_id);
        if (!empty($routes)) {
            $users_reassignment = array();
            foreach ($routes as $route) {
                $user_reassignment['UserReassignment']['user_id_origin'] = $origin_id;
                $user_reassignment['UserReassignment']['user_id_destination'] = $destination_id;
                $user_reassignment['UserReassignment']['garage_contact_bdm_id'] = null;
                $user_reassignment['UserReassignment']['distributor_contact_bdm_id'] = null;
                $user_reassignment['UserReassignment']['contact_contact_list_id'] = null;
                $user_reassignment['UserReassignment']['task_id'] = null;
                $user_reassignment['UserReassignment']['appointment_id'] = null;
                $user_reassignment['UserReassignment']['route_id'] = $route['Route']['id'];
                $user_reassignment['UserReassignment']['date_from'] = Fecha::toFormatoBd($data_from);
                $user_reassignment['UserReassignment']['date_to'] = Fecha::toFormatoBd($data_to);
                $user_reassignment['UserReassignment']['active'] = ConstantsBooleans::NO;
                $user_reassignment['UserReassignment']['permanent'] = $permanent;
                $users_reassignment[] = $user_reassignment;
                $flag = true;
            }
            if (!$this->UserReassignment->saveMany($users_reassignment)) {
                return false;
            }
        }

        return $flag;
    }

    public function reassign_user($data)
    {
        $origin_id = $data['User']['user_origin'];
        $destination_id = $data['User']['user_destination'];
        $permanent = $data['UserReassignment']['permanent'];

        $user_tmp = $this->findById($origin_id);
        $contact_id = $user_tmp['User']['contact_id'];

        $class_array_user_id = array(
            'GarageContactBdm' => $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm'),
            'DistributorContactBdm' => $this->DistributorContactBdm = ClassRegistry::init('DistributorContactBdm'),
            'ContactContactList' => $this->ContactContactList = ClassRegistry::init('ContactContactList'),
        );

        foreach ($class_array_user_id as $key => $Class) {

            $fields = array(
                $key => array(
                    'contact_id',
                )
            );

            $tmp = $Class->findAllByContactId($contact_id);
            if (!empty($tmp)) {
                foreach ($tmp as $elem_tmp) {
                    $user_destination_tmp = array();
                    $contact_destination_id = null;

                    $user_destination_tmp = $this->findById($destination_id);
                    $contact_destination_id = $user_destination_tmp['User']['contact_id'];
                    $elem_tmp[$key]['contact_id'] = $contact_destination_id;

                    $Class->create();
                    if (!$Class->guardar($elem_tmp, $fields)) {
                        return false;
                    }
                }
            }
        }

        $class_array_user_id = array(
            'Task' => $this->Task = ClassRegistry::init('Task'),
            'Appointment' => $this->Appointment = ClassRegistry::init('Appointment'),
            'Route' => $this->Route = ClassRegistry::init('Route'),
        );

        foreach ($class_array_user_id as $key => $Class) {

            $fields = array(
                $key => array(
                    'user_assigned_id',
                )
            );

            $tmp = $Class->findAllByUserAssignedId($origin_id);
            if (!empty($tmp)) {
                foreach ($tmp as $elem_tmp) {
                    $elem_tmp[$key]['user_assigned_id'] = $destination_id;

                    $Class->create();
                    if (!$Class->guardar($elem_tmp, $fields)) {
                        return false;
                    }
                }
            }
        }

        $this->TaskUser = ClassRegistry::init('TaskUser');

        $fields = array(
            'TaskUser' => array(
                'user_id',
            )
        );

        $tmp = $this->TaskUser->findAllByUserId($origin_id);
        if (!empty($tmp)) {
            foreach ($tmp as $elem_tmp) {
                $elem_tmp['TaskUser']['user_assigned_id'] = $destination_id;

                $this->TaskUser->create();
                if (!$this->TaskUser->guardar($elem_tmp, $fields)) {
                    return false;
                }
            }
        }

        if ($permanent == ConstantsBooleans::ACTIVE) {
            $this->User = ClassRegistry::init('User');

            $fields = array(
                'User' => array(
                    'active',
                )
            );

            $user = $this->User->findById($origin_id);
            $user['User']['active'] = ConstantsBooleans::NO_ACTIVE;
            $this->User->create();
            if (!$this->User->guardar($user, $fields)) {
                return false;
            }
        }

        return true;
    }

    public function reassign_scheduled_task()
    {
        $reassignments = $this->UserReassignment->findAllByPermanent(ConstantsBooleans::NO);

        foreach ($reassignments as $reassignment) {
            if ($reassignment['UserReassignment']['date_to'] == date('Y-m-d')) {
                $this->_revert_reassignment($reassignment);
            } elseif ($reassignment['UserReassignment']['date_from'] <= date('Y-m-d')) {
                $this->_make_reassignment($reassignment);
            }
        }

        $this->commit();
        echo 'FINISH!!';
        $this->layout = $this->autoRender = false;
    }


    public function getUsersAjax($conditions, $aag_region_id)
    {
        $users = $this->getUsersQuery($conditions, $aag_region_id);
        $users = Hash::combine($users, '{n}.User.id', array('%s', '{n}.User.full_name'));

        return $users;
    }

    public function getUsersQuery($conditions_ajax = array(), $aag_region_id)
    {
        $conditions_region = array();
        $conditions_region = array('User.aag_region_id' => $aag_region_id);

        if (isset($conditions_ajax['name']) && !empty($conditions_ajax['name'])) {
            $conditions_ajax = array(
                'OR' => array(
                    'User.name LIKE' => '%' . $conditions_ajax['name'] . '%',
                    'User.surname LIKE' => '%' . $conditions_ajax['name'] . '%'
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
                    'User.id',
                    'User.full_name'
                ),
                'group' => array(
                    'User.id'
                ),
            )
        );
    }

    public function getUsersNameByIdUser($user_id)
    {
        $resultArray = array();
        $query = $this->find('first', array(
            'conditions' => array(
                'User.id' => $user_id
            ),
            'fields' => array(
                'User.id',
                'User.full_name'
            ),
        ));

        if ($query) {
            $user = $query['User'];
            $resultArray[$user['id']] = $user['full_name'];
        }
        return $resultArray;
    }

    public function getUsersBdmAjax($conditions, $aag_region_id)
    {
        $users = $this->getUsersBdmQuery($conditions, $aag_region_id);
        $users = Hash::combine($users, '{n}.User.id', array('%s', '{n}.User.full_name'));

        return $users;
    }

    public function getUsersBdmQuery($conditions_ajax = array(), $aag_region_id)
    {
        $conditions_region = array();
        $conditions_region = array('User.aag_region_id' => $aag_region_id);

        if (isset($conditions_ajax['name']) && !empty($conditions_ajax['name'])) {
            $conditions_ajax = array(
                'OR' => array(
                    'User.name LIKE' => '%' . $conditions_ajax['name'] . '%',
                    'User.surname LIKE' => '%' . $conditions_ajax['name'] . '%'
                )
            );
        }

        return $this->find(
            'all',
            array(
                'conditions' => array(
                    $conditions_ajax,
                    $conditions_region,
                    'User.role_id' => array(
                        ConstantsRoles::BDM_AAG,
                        ConstantsRoles::BDM_TG,
                        ConstantsRoles::ADMIN,
                    ),
                ),
                'fields' => array(
                    'User.id',
                    'User.full_name'
                ),
                'group' => array(
                    'User.id'
                ),
            )
        );
    }

    public function getListRegionBDM($aag_region_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                array(
                    'User.aag_region_id' => $aag_region_id,
                    'User.role_id' => array(
                        ConstantsRoles::BDM_AAG,
                        ConstantsRoles::BDM_TG,
                        ConstantsRoles::ADMIN,
                        ConstantsRoles::GPC_LOGISTICS_BDM,
                        ConstantsRoles::TG_DIRECTOR,
                        ConstantsRoles::AAG_DIRECTOR,
                        ConstantsRoles::AAG_MANAGER,
                        ConstantsRoles::GARAGE_NETWORK_MANAGER,
                        ConstantsRoles::GENERAL_BRANCH_MANAGER,
                        ConstantsRoles::DISTRIBUTOR,
                    ),
                ),
            ),
            'fields' => array(
                'User.id',
                'User.full_name'
            ),
            'order' => array(
                'full_name'
            )
        ));
    }

    public function getListIndividualUser($user_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    array(
                        'User.id' => $user_id,
                    ),
                ),
                'fields' => array(
                    'User.id',
                    'User.full_name'
                ),
                'order' => array(
                    'full_name'
                )
            )
        );
    }

    public function setRepairMaintenance($id, $value = ConstantsBooleans::YES)
    {
        $user = $this->findById($id);
        if (isset($user['User']['id']) && !empty($user['User']['id'])) {
            $fields = array(
                'User' => array(
                    'id',
                    'repairmaintenance'
                )
            );
            $user['User']['repairmaintenance'] = $value;
            return $this->save($user, true, $fields);
        }
    }
}
