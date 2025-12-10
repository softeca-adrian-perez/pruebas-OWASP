<?php

/**
 * @property AclComponent $Acl
 * @property AuthComponent $Auth
 */
class AuthUserComponent extends Component
{
    private $_user_id;
    private $_language_code;

    public function __construct(ComponentCollection $collection, $settings = array())
    {
        $user = CakeSession::read('Auth.User');
        $this->_user_id = isset($user['id']) ? $user['id'] : array();
        $this->_language_code = isset($user['language_code']) ? $user['language_code'] : Configure::read('LANGUAGE_CODE_DEFAULT');
        parent::__construct($collection, $settings);
    }

    public function changeLanguageCode($language_code)
    {
        $validLanguageCodes = CakeSession::read('Config.valid_languages_codes');

        // Validate the language code using a whitelist
        if (in_array($language_code, $validLanguageCodes)) {
            CakeSession::write('Auth.User.language_code', $language_code);
            $this->_language_code = $language_code;
        } else {
            $language_code = Configure::read('LANGUAGE_CODE_DEFAULT');
            CakeSession::write('Auth.User.language_code', $language_code);
            $this->_language_code = $language_code;
        }
    }

    public function getUserId()
    {
        return $this->_user_id;
    }
    public function getLanguageCode()
    {
        return $this->_language_code;
    }
}
