<?php
require_once(dirname(__FILE__) . '/../../Model/WebService.php');

define('TIMEOUT_DE_WEB_SERVICES', 60 * 10);
ini_set('default_socket_timeout', TIMEOUT_DE_WEB_SERVICES);
set_time_limit(TIMEOUT_DE_WEB_SERVICES);
error_reporting(0);

/**
 * GNM Webservice
 *
 * @service SoapWebservice
 */
class SoapWebservice{

    /**
     * Retrieves basic information from all garages that belong to Eurogarage.
     *
     * @param string $username Username of webservice
     * @param string $password Password of webservice
     * @return Response The response of webservice
     */
    public function GetEurogarageOutletData($username=null, $password=null){
        return $this->getGarageData($username, $password, NETWORK_ID_AUTOCARE);
    }

    /**
     * Retrieves basic information from all garages that belong to Toptruck
     *
     * @param string $username Username of webservice
     * @param string $password Password of webservice
     * @return Response The response of webservice
     */
    public function GetTopTruckOutletData($username=null, $password=null){
        return $this->getGarageData($username, $password, NETWORK_ID_TOPTRUCK);
    }

    /**
     * Retrieves basic information from all garages that belong to G-Car
     *
     * @param string $username Username of webservice
     * @param string $password Password of webservice
     * @return Response The response of webservice
     */
    public function GetGCarOutletData($username=null, $password=null){
        return $this->getGarageData($username, $password, ConstantesRedes::G_CAR);
    }

    /**
     * Retrieves basic information from all garages that belong to G-Car
     *
     * @param string $username Username of webservice
     * @param string $password Password of webservice
     * @return Response The response of webservice
     */
    public function GetGTruckOutletData($username=null, $password=null){
        return $this->getGarageData($username, $password, ConstantesRedes::G_TRUCK);
    }

    /**
     * Retrieves basic information from all garages that belong specific network
     *
     * @param string $username Username of webservice
     * @param string $password Password of webservice
     * @param string $networkGuid Guid of network
     * @return Response The response of webservice
     */
    public function GetGarageDataByNetworkGuid($username=null, $password=null, $networkGuid){
        $authOk = true;
        $this->api_log($this->getGarageData($username, $password, $networkGuid), $authOk);
        return $this->getGarageData($username, $password, $networkGuid);
    }

    /**
     * Retrieves basic information from a garage with an specific garge_code
     *
     * @param string $username Username of webservice
     * @param string $password Password of webservice
     * @param string $garage_code Garage code
     * @return Response The response of webservice
     */
    public function GetGarageDataByCode($username=null, $password=null, $garage_code){
        return $this->getGarageByCode($username, $password, $garage_code);
    }

    /**
     * Retrieves basic information from a garage with an specific garge_id
     *
     * @param string $username Username of webservice
     * @param string $password Password of webservice
     * @param string $garage_id Garage id
     * @return Response The response of webservice
     */
    public function GetGarageDataById($username=null, $password=null, $garage_id){
        return $this->getGarageById($username, $password, $garage_id);
    }

    /**
     * Retrieves basic information from all garages in database
     *
     * @param string $username Username of webservice
     * @param string $password Password of webservice
     * @param string $ids Code of the garage
     * @param string $nombre name of the garage
     * @param string $codigo code of the garage
     * @param string $distribuidor distributor of the garage
     * @param string $coordinador coordinator of the garage
     * @param string $provincia location of the garage
     * @param string $categoria category of the garage
     * @param string $services_searched services of the garage
     * @param string $network_id network of the garage
     * @param string $vehicle_type Type of vehicles repaired by the workshop
     * @param string $lat Latitude of vehicle to repair by the workshop
     * @param string $lng Longitude of vehicle to repair by the workshop
     * @return Response The response of webservice
     */
    public function GetGarageDataFiltered($username = null, $password = null, $ids = null, $nombre = null, $codigo = null, $distribuidor = null, $coordinador = null, $provincia = null, $categoria = null, $services_searched = null, $network_id = null, $vehicle_type = null, $lat = null, $lng = null){
        return $this->getGarageFiltered($username, $password, $ids, $nombre, $codigo, $distribuidor, $coordinador, $provincia, $categoria, $services_searched, $network_id, $vehicle_type, $lat, $lng);
    }

     /**
     * Retrieves basic information from distributors and coordinator
     *
     * @param string $username Username of webservice
     * @param string $password Password of webservice
     * @param string $ids Code of the garage
     * @return Response The response of webservice
     */
    public function GetGarageDataDistributorsCoordinators($username = null, $password = null, $ids = null){
        return $this->getDistributorsCoordinators($username, $password, $ids);
    }

    private function getGarageData($username=null, $password=null, $network_guid){
        ini_set("memory_limit", "256M");

        try {
            if (!$this->doAuthenticate($username, $password)){
                return new Response('Error', 'Invalid username/password', false);
            }
            $garage_array = array();
            $this->Webservice = new Webservice();
            $talleres = $this->Webservice->getAllGarageData($network_guid);

            if(empty($talleres)){
                return new Response('Error', 'No garages in database', false);
            }

            foreach($talleres as $taller){
                $garage_array[] = new OutletData($taller);
            }

            return new Response('OK', null, $garage_array);
        }catch (Exception $exception){
            return new Response('Error', 'Internal error', false);
        }
    }

    private function getGarageById($username=null, $password=null, $garage_id){
        ini_set("memory_limit", "256M");

        try {
            if (!$this->doAuthenticate($username, $password)){
                return new Response('Error', 'Invalid username/password', false);
            }
            $garage_array = array();
            $this->Webservice = new Webservice();
            $talleres = $this->Webservice->getGarageDataById($garage_id);

            if(empty($talleres)){
                return new Response('Error', 'No garages in database', false);
            }

            foreach($talleres as $taller){
                $garage_array[] = new OutletData($taller);
            }

            return new Response('OK', null, $garage_array);
        }catch (Exception $exception){
            return new Response('Error', 'Internal error', false);
        }
    }

    private function getGarageByCode($username=null, $password=null, $garage_code){
        ini_set("memory_limit", "256M");

        try {
            if (!$this->doAuthenticate($username, $password)){
                return new Response('Error', 'Invalid username/password', false);
            }
            $garage_array = array();
            $this->Webservice = new Webservice();
            $talleres = $this->Webservice->getGarageDataByCode($garage_code);

            if(empty($talleres)){
                return new Response('Error', 'No garages in database', false);
            }

            foreach($talleres as $taller){
                $garage_array[] = new OutletData($taller);
            }

            return new Response('OK', null, $garage_array);
        }catch (Exception $exception){
            return new Response('Error', 'Internal error', false);
        }
    }

    public function getGarageFiltered($username = null, $password = null, $ids = null, $nombre = null, $codigo = null, $distribuidor = null, $coordinador = null, $provincia = null, $categoria = null, $services_searched = null, $network_id = null, $vehicle_type = null, $lat = null, $lng = null){
        ini_set("memory_limit", "10240M");
        try {
            if (!$this->doAuthenticate($username, $password)){
                return new Response('Error', 'Invalid username/password', false);
            }
            $garage_array = array();
            $this->Webservice = new Webservice();

            $talleres = $this->Webservice->getGarageDataFiltered($username, $password, $ids, $nombre, $codigo, $distribuidor, $coordinador, $provincia, $categoria, $services_searched, $network_id, $vehicle_type, $lat, $lng);
            if(empty($talleres)){
                return new Response('Error', 'No data found in database', false);
            }
            foreach($talleres as $taller){
                $garage_array[] = new OutletData($taller);
            }
            return new Response('OK', null, $garage_array);
        }catch (Exception $exception){
            return new Response('Error', 'Internal error', false);
        }
    }

    private function doAuthenticate($username, $password) {
        return ($username == WEBSERVICE_USERNAME && $password==Texto::encryptDecryptText(WEBSERVICE_PASSWORD, null));
    }

    public function getDistributorsCoordinators($username = null, $password = null, $ids = null){
        ini_set("memory_limit", "10240M");
        try {
            if (!$this->doAuthenticate($username, $password)){
                return new Response('Error', 'Invalid username/password', false);
            }
            $garage_array = array();
            $this->Webservice = new Webservice();

            $dist_coord = $this->Webservice->GetDataDistributorsCoordinators($username, $password, $ids);
            if(empty($dist_coord)){
                return new Response('Error', 'No data found in database', false);
            }
            foreach($dist_coord as $taller){
                $garage_array[] = new OutletData($taller);
            }
            return new Response('OK', null, $garage_array);
        }catch (Exception $exception){
            return new Response('Error', 'Internal error', false);
        }
    }

    public function api_log($dataReceived, $authOk)
    {
        // api log data
        $ip = $_SERVER['SERVER_ADDR'];
        $date = date('Y-m-d H:i:s');
        $uri = $_SERVER['REQUEST_URI'];

        $log_api = array(
            'LogApi' => array(
                'user_email' => null,
                'ip' => $ip,
                'date' => $date,
                'uri' => $uri,
                'request' => $authOk ? 'req ok' : $dataReceived,
                //'response' => null
                'notes' => $authOk ? 'req ok' : 'req ko'
            )
        );
        $LogApiClass = ClassRegistry::init('LogApi');
        $LogApiClass->save($log_api);
    }


}
