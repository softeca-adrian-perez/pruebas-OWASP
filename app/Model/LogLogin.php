<?php

class LogLogin extends AppModel
{
    public $useTable = 'logs_login';

    public function add_login($userId, $login, $error = null)
    {
        $fields = array(
            'LogLogin' => array(
                'user_id',
                'error',
                'login',
                'date',
            )
        );

        $logLogin = array(
            'LogLogin' => array(
                'user_id' => $userId,
                'error' => $error,
                'login' => $login,
                'date' => date('Y-m-d H:i:s')
            )
        );

        $this->create();
        if ($tmp = $this->save($logLogin, true, $fields)) {
            return $tmp;
        }
        return false;
    }

}