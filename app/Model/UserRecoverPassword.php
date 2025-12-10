<?php

class UserRecoverPassword extends AppModel
{

    public $useTable = 'users_recover_passwords';

    public $belongsTo = array(
        'User'
    );

    public $validate = array(
        'key' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'user_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_user'
            ),
        ),
    );

    /**
     * Create a UserRecoverPassword.
     */
    public function add($userId, $sendEmail = true)
    {
        $fields = array(
            'UserRecoverPassword' => array(
                'user_id',
                'key',
                'creation_date',
                'new_user'
            )
        );
        $this->deleteAll(array('user_id' => $userId));

        $userRecoverPassword = array(
            'UserRecoverPassword' => array(
                'user_id' => $userId,
                'key' => sha1($userId . date('Y-m-d H:i:s') . 'GNMAAG'),
                'creation_date' => date('Y-m-d H:i:s'),
                'new_user' => ConstantsBooleans::NO
            )
        );

        $this->create();
        $userRecoverPasswordBd = $this->guardar($userRecoverPassword, $fields);

        if ($userRecoverPasswordBd) {
            if ($sendEmail) {
                $user = $this->User->findById($userId);

                $languageCode = ConstantsLanguages::ENGLISH_CODE;
                if (isset($user['User']['language_id'])) {
                    $language = $this->User->Language->findById($user['User']['language_id']);
                    if ($language) {
                        $languageCode = $language['Language']['code'];
                    }
                }

                if (!$this->sendEmailRecoverPassword($userRecoverPasswordBd['UserRecoverPassword']['id'], $user, $languageCode)) {
                    return false;
                }
            }
            return $userRecoverPasswordBd;
        } else {
            return false;
        }
    }

    /**
     * Create a UserRecoverPassword when a new user is created.
     */
    public function add_user_password($userId)
    {
        $fields = array(
            'UserRecoverPassword' => array(
                'user_id',
                'key',
                'creation_date',
                'new_user',
            )
        );
        $this->deleteAll(array('user_id' => $userId));

        $userRecoverPassword = array(
            'UserRecoverPassword' => array(
                'user_id' => $userId,
                'key' => sha1($userId . date('Y-m-d H:i:s') . 'GNMAAG'),
                'creation_date' => date('Y-m-d H:i:s'),
                'new_user' => ConstantsBooleans::YES
            )
        );

        $this->create();
        $userRecoverPasswordBd = $this->guardar($userRecoverPassword, $fields);

        if ($userRecoverPasswordBd) {
            $user = $this->User->findById($userId);

            $languageCode = ConstantsLanguages::ENGLISH_CODE;
            if (isset($user['User']['language_id'])) {
                $language = $this->User->Language->findById($user['User']['language_id']);
                if ($language) {
                    $languageCode = $language['Language']['code'];
                }
            }

            if (!$this->sendEmailNewUserPassword($userRecoverPasswordBd['UserRecoverPassword']['id'], $user, $languageCode)) {
                return false;
            }
            return $userRecoverPasswordBd;
        } else {
            return false;
        }
    }

    /**
     * Create a new Recover Password email.
     */
    public function sendEmailRecoverPassword($userRecoverPasswordId, $user, $languageCode)
    {
        $this->Email = ClassRegistry::init('Email');
        $roleClass = ClassRegistry::init('Role');
        $roles = $roleClass->search_list();

        $tmp = $this->_sendEmail($userRecoverPasswordId);
        $emailTo = $tmp['email_to'];
        $userRecoverPassword = $tmp['recover_password'];

        $viewVars = array(
            'language_code' => $languageCode,
            'user_name' => $userRecoverPassword['User']['name'],
            'gnm_pwd_url' => $userRecoverPassword['url'],
            'user_type' => $roles[$userRecoverPassword['User']['role_id']],
        );

        if (!$this->Email->newEmailRecoverPassword($emailTo, $viewVars, $user, $languageCode)) {
            return false;
        }

        return true;
    }

    /**
     * Create a new New User Password email.
     */
    public function sendEmailNewUserPassword($userRecoverPasswordId, $user, $languageCode)
    {
        $this->Email = ClassRegistry::init('Email');
        $roleClass = ClassRegistry::init('Role');
        $roles = $roleClass->search_list();

        $tmp = $this->_sendEmail($userRecoverPasswordId, $languageCode);
        $emailTo = $tmp['email_to'];
        $userRecoverPassword = $tmp['recover_password'];

        $viewVars = array(
            'language_code' => $languageCode,
            'user_name' => $userRecoverPassword['User']['name'],
            'user_username' => $userRecoverPassword['User']['username'],
            'gnm_pwd_url' => $userRecoverPassword['url'],
            'user_type' => $roles[$userRecoverPassword['User']['role_id']],
        );

        if (!$this->Email->newEmailNewUserPassword($emailTo, $viewVars, $user, $languageCode)) {
            return false;
        }

        return true;
    }

    private function _sendEmail($userRecoverPasswordId)
    {
        $this->Contact = ClassRegistry::init('Contact');
        $this->contain('User');
        $userRecoverPassword = $this->findById($userRecoverPasswordId);
        $contact = $this->Contact->findById($userRecoverPassword['User']['contact_id']);
        $emailTo = $contact['Contact']['email'];

        $userRecoverPassword['url'] = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . Router::url(
            array(
                'controller' => 'users',
                'action' => 'change_password',
                $userRecoverPassword['UserRecoverPassword']['user_id'],
                $userRecoverPassword['UserRecoverPassword']['key'],
            )
        );

        return array(
            'email_to' => $emailTo,
            'recover_password' => $userRecoverPassword,
        );
    }

    public function deleteRecoverKeys()
    {
        $recoverKeys = $this->find('all', array(
            'conditions' => array(
                'TIMESTAMPDIFF(HOUR, UserRecoverPassword.creation_date, NOW()) > ' => 24
            )
        ));

        foreach ($recoverKeys as $recoverKey) {
            $this->delete($recoverKey['UserRecoverPassword']['id']);
        }
    }
}
