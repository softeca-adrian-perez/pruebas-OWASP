<?php
class ServicesDriversController extends AppController
{
    public $uses = array(
        'ServiceDriver',
        'GarageService',
        'GarageServiceDriver',
        'VehicleType',
        'Language'
    );

    /**
     *  API to get all the services drivers for a list of networks (with garages associated)
     */
    public function get_services_drivers()
    {
        $data_received = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $data_received);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($data_received) && !$this->request->is('get')) {

            $networks_ids = isset($data_received->networks) ? $data_received->networks : null;

            if (empty($networks_ids)) {
                exit;
            }

            $services_drivers = $this->ServiceDriver->findServicesDriversInNetworks($networks_ids);
            $sendData = array('services_drivers' => array());

            foreach ($networks_ids as $network_id) {
                $services_drivers_array = array();
                foreach ($services_drivers as $service_driver) {
                    if ($service_driver['ServiceDriver']['network_id'] == $network_id) {
                        $services_drivers_array[] = array(
                            'id' => $service_driver['ServiceDriver']['id'],
                            'languages' => array(
                                'en' => $service_driver['ServiceDriver']['name_en'],
                                'fr' => $service_driver['ServiceDriver']['name_fr'],
                                'de' => $service_driver['ServiceDriver']['name_de'],
                                'nl' => $service_driver['ServiceDriver']['name_nl'],
                                'es' => $service_driver['ServiceDriver']['name_es']
                            )
                        );
                    }
                }
                $sendData['services_drivers'][] = array(
                    'network_id' => $network_id,
                    'services_drivers' => $services_drivers_array
                );
            }

            return $this->returnJsonResult($sendData);
        }
    }
}
