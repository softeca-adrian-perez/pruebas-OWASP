<?php

/**
 * In the core there is a key_token variable which is used to generate the token.
 */
class ApiUtil
{
    const ERROR_AUTHENTICATION = 'Authentication error';

    /**
     * Generate token when calling "ApiController/get_authentication".
     */
    public static function createToken($clientId)
    {
        try {
            $now = time();
            $duration = 86400; // 1 dia = 60 * 60 * 24 segundos
            $payload = array(
                "id" => $clientId,
                "exp" => $now + $duration,
                "iat" => $now,
            );

            $token = JWT::encode($payload, Texto::encryptDecryptText(KEY_TOKEN, false));

            return array(
                "access_token"  => $token,
                "token_type" => "Bearer",
                "expires_in" => $duration,
            );
        } catch (Exception $e) {
            CakeLog::debug(print_r("Api - Create token - An error has occured: " . $e->getMessage(), true));
            return null;
        }
    }

    /**
     * Validate the token when receiving calls to the api.
     */
    public static function checkToken($token)
    {
        $result = false;
        $message = '';
        try {
            $payload = JWT::decode($token, Texto::encryptDecryptText(KEY_TOKEN, false));
            $now = time();
            if ((isset($payload->iat) && $payload->iat > $now) || (isset($payload->exp) && $now >= $payload->exp)) {
                throw new Exception('Expired token');
            }

            $result = $payload->id;
        } catch (Exception $e) {
            $message = $e->getMessage();
            if ($message !== 'Expired token') {
                $message = 'Invalid token';
            }
        }

        return array('result' => $result, 'message' => $message);
    }

    /**
     * Checks authentication.
     */
    public static function checkAuthentication($headers)
    {
        try {
            $token = isset($headers['Authorization']) ? $headers['Authorization'] : '';
            if (empty($token)) {
                return false;
            }
            if (strpos($token, 'Bearer ') === false) {
                return false;
            }
            $token = explode(" ", $token);
            if (count($token) != 2) {
                return false;
            }
            $token = $token[1];
            $idUser = ApiUtil::checkToken($token);

            if (!$idUser['result']) {
                return false;
            }

            return $idUser;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Api - Check authentication - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }
}
