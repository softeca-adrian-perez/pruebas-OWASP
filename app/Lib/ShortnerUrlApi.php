<?php
App::uses('HttpSocket', 'Network/Http');
App::uses('ShortnerUrl', 'Model');
App::uses('ShortnerUrlLog', 'Model');

class ShortnerUrlApi
{
    /**
     * Send request.
     */
    private function sendRequests($url, $inputArray, $isGet = true, $token = null)
    {
        try {
            if ($token == null) {
                $token = Texto::encryptDecryptText(SHORTNER_API_KEY, false);
            }

            $httpSocket = new HttpSocket(
                array(
                    'ssl_verify_peer' => false,
                    'ssl_verify_host' => false,
                    'ssl_allow_self_signed' => true,
                    'ssl_verify_peer_name' => false
                )
            );

            $headers = array(
                'header' => array(
                    'Authorization' => 'Bearer ' . $token,
                ),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'CakePHP'
            );

            $results = $isGet ?
                $httpSocket->get($url, $inputArray, $headers) :
                $httpSocket->post($url, $inputArray, $headers);

            if (!isset($results->body)) {
                return array(
                    'body' => null,
                    'code' => $results->code
                );
            }

            return array(
                'body' => json_decode($results->body, true),
                'code' => $results->code
            );
        } catch (Exception $e) {
            CakeLog::debug(print_r("Shortner URL - Send request - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Get URL short.
     */
    public function getUrlShort($data)
    {
        try {
            $url = Configure::read('shortner_url_api.base_url') . Configure::read('shortner_url_api.shorten_endpoint');
            $input = array(
                'long_url' => $data['long_url'],
                'expire_at_datetime' => isset($data['expire_at_datetime']) ?
                    (new DateTime())
                    ->modify('+' . $data['expire_at_datetime'] . ' days')
                    ->format('Y-m-d H:i:s')
                    : null,
                'expire_at_views' => isset($data['expire_at_views']) ? $data['expire_at_views'] : null,
                'domain' => (isset($data['domain']) && !empty($data['domain'])) ? $data['domain'] : SHORTNER_DEFAULT_DOMAIN,
            );

            $result_request = $this->sendRequests($url, $input, false, $data['api_key']);

            if ($result_request != null) {
                if (!$this->postLogShortnerUrl($result_request, $data)) {
                    return ConstantsStatusCode::BAD_REQUEST;
                }
            }

            if ($result_request['code'] != ConstantsStatusCode::OK) {
                return ConstantsStatusCode::BAD_REQUEST;
            } else {
                return $result_request['body'];
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("Shortner URL - Get URL short - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Create ShortnerUrlLog.
     */
    public function postLogShortnerUrl($result_request, $data)
    {
        try {
            $shortnerUrlLogModel = ClassRegistry::init('ShortnerUrlLog');
            return $shortnerUrlLogModel->add($result_request, $data);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Shortner URL - Post log Shortner UL - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Get URL short for test.
     */
    public function getUrlShortTest($data)
    {
        try {
            $url = Configure::read('shortner_url_api.base_url') . Configure::read('shortner_url_api.shorten_endpoint');
            $input = array(
                'long_url' => $data['long_url'],
                'expire_at_datetime' => (isset($data['expire_at_datetime']) & !empty($data['expire_at_datetime'])) ?
                    (new DateTime())
                    ->modify('+' . $data['expire_at_datetime'] . ' days')
                    ->format('Y-m-d H:i:s')
                    : null,
                'expire_at_views' => isset($data['expire_at_views']) ? $data['expire_at_views'] : null,
                'domain' => (isset($data['domain']) && !empty($data['domain'])) ? $data['domain'] : SHORTNER_DEFAULT_DOMAIN,
            );
            $input_apikey = array(
                'long_url' => $data['long_url'],
                'expire_at_datetime' => null,
                'expire_at_views' => null,
                'domain' => null,
            );
            $data['domain'] = (isset($data['domain']) && !empty($data['domain'])) ? $data['domain'] : SHORTNER_DEFAULT_DOMAIN;

            $result_request_apikey = null;
            if ($data['domain'] != SHORTNER_DEFAULT_DOMAIN && $data['domain'] != null) {
                if ($data['api_key'] != null) {
                    $result_request_apikey = $this->sendRequests($url, $input_apikey, false, $data['api_key']);
                } else {
                    return ConstantsStatusCode::BAD_REQUEST;
                }
            }
            if ($result_request_apikey != null) {
                $result_request_apikey['test_config'] = true;
                if (!$this->postLogShortnerUrl($result_request_apikey, $data)) {
                    return ConstantsStatusCode::BAD_REQUEST;
                }
                if ($result_request_apikey['code'] != ConstantsStatusCode::OK) {
                    return ConstantsStatusCode::BAD_REQUEST;
                }
            }

            if ($data['domain'] == SHORTNER_DEFAULT_DOMAIN || $data['domain'] == null) {
                $result_request = $this->sendRequests($url, $input, false);
            } else {
                $result_request = $this->sendRequests($url, $input, false, $data['api_key']);
            }

            if ($result_request != null) {
                $result_request['test_config'] = true;
                if (!$this->postLogShortnerUrl($result_request, $data)) {
                    return ConstantsStatusCode::BAD_REQUEST;
                }
            }

            if ($result_request['code'] != ConstantsStatusCode::OK || empty($result_request['body'])) {
                return ConstantsErrorUrl::BAD_DOMAIN;
            } else {
                return $result_request['body'];
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("Shortner URL - Get URL short test - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }
}
