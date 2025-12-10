<?php
class ApiController extends AppController
{
    /**
     * @apiGroup Authentication
     * @apiName get_authentication
     * @apiDescription Return the token for authentication
     *
     * The client must send this token in the Authorization header when making requests to protected resources
     *
     * Authorization: Bearer token
     * @api {post} /api_rm/workshop/get_authentication Get authentication token
     *
     * @apiParam {String} grant_type Must be "client_credentials"
     * @apiParam {String} client_id Username
     * @apiParam {String} client_secret Password
     *
     * @apiParamExample {json} Examples Request:
     * {
     *     "grant_type": "client_credentials",
     *     "client_id": "test",
     *     "client_secret": "test"
     * }
     *
     * @apiSuccess (Success Response) {String} access_token The access token string
     * @apiSuccess (Success Response) {String} token_type The string "Bearer"
     * @apiSuccess (Success Response) {String} expires_in The duration of time the access token is granted for (seconds)
     *
     * @apiError (Error Response) {String="invalid_request","invalid_credentials"} error The error code
     * @apiError (Error Response) {String} error_description The error descripcion
     *
     * @apiSuccessExample {json} Example Success Response:
     *      {
     *          "access_token": "*******",
     *          "token_type": "Bearer",
     *          "expires_in": 86400
     *      }
     *
     * @apiErrorExample {json} Example Error Response invalid_request:
     * {
     *     "error": "invalid_request",
     *     "error_description": "The request is invalid"
     * }
     *
     * @apiErrorExample {json} Example Error Response invalid_credentials:
     * {
     *     "error": "invalid_credentials",
     *     "error_description": "The credentials are invalid"
     * }
     */
    public function get_authentication()
    {
        $resultado = false;
        $authOk = false;

        // Validate that the request is valid
        if (!$this->getAuthenticationValidRequest($this->request)) {
            $resultado = array(
                'error' => "invalid_request",
                'error_description' => "The request is invalid",
            );
        }

        if (!$resultado) {
            $data = json_decode($this->request->input(), true);

            if ($this->existsUserPassword($data["client_id"], $data["client_secret"])) {
                $resultado = ApiUtil::createToken($data['client_id']);
                $authOk = true;
            } else {
                $resultado = array(
                    'error' => "invalid_credentials",
                    'error_description' => "The credentials are invalid",
                );
            }
        }

        $this->api_log($this->request->input(), $authOk);

        return $this->returnJsonResult($resultado);
    }

    /**
     * Checks if the user exists and has the specified password.
     */
    private function existsUserPassword($username, $password)
    {
        if (($username == GNMAAG_GNM_API_USER && Texto::encryptDecryptText($password, true) == GNMAAG_GNM_API_KEY) ||
            ($username == GNMAAG_GNM_API_USER2 && Texto::encryptDecryptText($password, true) == GNMAAG_GNM_API_KEY2))
        {
            return true;
        }
        return false;
    }

    /**
     * Validate that the request is valid, i.e. that it has the necessary parameters.
     */
    private function getAuthenticationValidRequest($request)
    {
        if (!$request->is('post')) {
            return false;
        }
        $data = json_decode($this->request->input(), true);
        if (!is_array($data)) {
            return false;
        }
        if (!isset($data["grant_type"]) || $data["grant_type"] != "client_credentials") {
            return false;
        }
        if (!isset($data["client_id"])) {
            return false;
        }
        if (!isset($data["client_secret"])) {
            return false;
        }
        return true;
    }
}
