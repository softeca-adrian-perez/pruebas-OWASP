<?php
App::uses('HttpSocket', 'Network/Http');

class Agn
{
    /**
     * Get AGN token.
     */
    private function getAgnToken()
    {
        try {
            $url = GNMAAG_AGN_API_URL_CONFIGURATION . Configure::read('agn_api.login_endpoint');
            $httpSocket = new HttpSocket(
                array(
                    'ssl_verify_peer' => false,
                    'ssl_verify_host' => false,
                    'ssl_allow_self_signed' => true,
                    'ssl_verify_peer_name' => false
                )
            );
            $input = array(
                'email' => GNMAAG_AGN_API_EMAIL,
                'password' => Texto::encryptDecryptText(GNMAAG_AGN_API_KEY, false)
            );
            $headers = array();
            $results = $httpSocket->post($url, $input, $headers);
            if (!isset($results->body)) {
                return null;
            }
            $resultsArray = json_decode($results->body, true);
            if (!isset($resultsArray['token'])) {
                return null;
            }
            return $resultsArray['token'];
        } catch (Exception $e) {
            CakeLog::debug(print_r("AGN - Get token - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * Send request.
     */
    private function sendRequests($url, $inputArray, $isGet = true)
    {
        try {
            $token = $this->getAgnToken();

            if ($token == null) {
                return null;
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
                'User-Agent' => 'CakePHP'
            );

            $results = $isGet ?
                $httpSocket->get($url, $inputArray, $headers) :
                $httpSocket->post($url, $inputArray, $headers);

            if (!isset($results->body)) {
                return null;
            }

            return json_decode($results->body, true);
        } catch (Exception $e) {
            CakeLog::debug(print_r("AGN - Get token - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * API call to AGN/GV to get the URL to login SEO admin zone.
     *
     * @param int $networkId
     */
    public function getLoginSeoAdminZone($networkGUID)
    {
        try {
            if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN || CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                $url = GNMAAG_AGN_API_URL_CONFIGURATION . Configure::read('agn_api.login_seo_admin_zone_endpoint');
                $input = array(
                    'network_guid' => $networkGUID
                );
                return $this->sendRequests($url, $input);
            } else {
                return null;
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("AGN - Get login seo admin zone - An error has occured: " . $e->getMessage(), true));
            return null;
        }
    }

    /**
     * API call to AGN/GV to update translations.
     *
     * @param int $networkId
     */
    public function updateLocoTranslations($networkGUID, $locale)
    {
        try {
            if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN) {
                $this->Network = ClassRegistry::init('Network');
                $network = $this->Network->findByGuid($networkGUID);
                $prefix = Configure::read('networks_web_login_prefix')[$network['Network']['id']];
                $url = $prefix . Configure::read('agn_api.update_loco_translations_endpoint');
                $input = array(
                    'network_guid' => $networkGUID,
                    'localeCountry' => $locale
                );
                return $this->sendRequests($url, $input);
            } else {
                return null;
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("AGN - Update loco translations - An error has occured: " . $e->getMessage(), true));
            return null;
        }
    }

    /**
     * API call to AGN/GV to create a locale.
     *
     * @param int $networkId
     */
    public function createLocoLanguage($networkGUID, $locale)
    {
        try {
            if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN) {
                $this->Network = ClassRegistry::init('Network');
                $network = $this->Network->findByGuid($networkGUID);
                $prefix = Configure::read('networks_web_login_prefix')[$network['Network']['id']];
                $url = $prefix . Configure::read('agn_api.create_loco_language_endpoint');
                $input = array(
                    'network_guid' => $networkGUID,
                    'locale' => $locale
                );
                return $this->sendRequests($url, $input);
            } else {
                return null;
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("AGN - Create loco language - An error has occured: " . $e->getMessage(), true));
            return null;
        }
    }

    /**
     * API call to AGN/GV to delete a locale.
     *
     * @param int $networkId
     */
    public function deleteLocoLanguage($networkGUID, $locale)
    {
        try {
            if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN) {
                $this->Network = ClassRegistry::init('Network');
                $network = $this->Network->findByGuid($networkGUID);
                $prefix = Configure::read('networks_web_login_prefix')[$network['Network']['id']];
                $url = $prefix . Configure::read('agn_api.delete_loco_language_endpoint');
                $input = array(
                    'network_guid' => $networkGUID,
                    'locale' => $locale
                );
                return $this->sendRequests($url, $input);
            } else {
                return null;
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("AGN - Delete loco language - An error has occured: " . $e->getMessage(), true));
            return null;
        }
    }

    /**
     * API call to AGN/GV to purge cloudflare cache data
     *
     * @param int $networkId
     * @param int $oldCityId
     * @param int $cityId
     */
    public function purgeCacheLocationData($networkGUID, $oldCityId, $cityId)
    {
        try {
            $url = GNMAAG_AGN_API_URL_CONFIGURATION . Configure::read('agn_api.purge_cache_location_data');
            $input = array(
                'network_guid' => $networkGUID,
                'old_city_id' => $oldCityId,
                'city_id' => $cityId
            );
            return $this->sendRequests($url, $input);
        } catch (Exception $e) {
            CakeLog::debug(print_r("AGN - Purge cloudflare cache location data - An error has occured: " . $e->getMessage(), true));
            return null;
        }
    }

    /**
     * API call to AGN/GV to purge cloudflare cache data (Reviews info)
     *
     * @param int $networkId
     * @param boolean $onlyChangeHome
     */
    public function purgeCacheReviewsData($networkGUID, $onlyChangeHome = false)
    {
        try {
            $url = GNMAAG_AGN_API_URL_CONFIGURATION . Configure::read('agn_api.purge_cache_reviews_data');
            $input = array(
                'network_guid' => $networkGUID,
                'only_change_home' => $onlyChangeHome
            );
            return $this->sendRequests($url, $input);
        } catch (Exception $e) {
            CakeLog::debug(print_r("AGN - Purge cloudflare cache reviews data - An error has occured: " . $e->getMessage(), true));
            return null;
        }
    }
}
