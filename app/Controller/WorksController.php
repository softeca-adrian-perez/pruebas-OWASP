<?php
class WorksController extends AppController
{
    public $uses = array(
        "Work",
        "GroupingGenart",
        "Genart",
        "Network"
    );

    /**
     *  API to get all the works for a list of networks with garages
     */
    public function get_works()
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

            $works = $this->Work->findWorksInNetworks($networks_ids);

            $sendData = array('works' => array());

            foreach ($networks_ids as $network_id) {
                $works_array = array();
                foreach ($works as $work) {
                    if ($work['GarageNetwork']['network_id'] == $network_id) {
                        $works_array[] = array(
                            'id' => $work['Work']['code'],
                            'languages' => array(
                                'en' => $work['Work']['name_en'],
                                'fr' => $work['Work']['name_fr'],
                                'de' => $work['Work']['name_de'],
                                'nl' => $work['Work']['name_nl'],
                                'es' => $work['Work']['name_es']
                            )
                        );
                    }
                }
                $sendData['works'][] = array(
                    'network_id' => $network_id,
                    'works' => $works_array
                );
            }

            return $this->returnJsonResult($sendData);
        }
    }

    /**
     *  API to get all the locations (cities) for a network and work
     */
    public function get_locations_work()
    {
        $data_received = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $data_received);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }
        if (!empty($data_received) && !$this->request->is('get')) {

            $network_id = isset($data_received->network_id) ? $data_received->network_id : null;
            $work_code = isset($data_received->work_code) ? $data_received->work_code : null;

            if (empty($network_id) || empty($work_code)) {
                exit;
            }

            $works = $this->Work->findCitiesInNetworkWork($network_id, $work_code);

            $locations = array();

            foreach ($works as $work) {
                $locations[] = array(
                    'id' => $work['NetworkCity']['city_id'],
                    'name' => $work['City']['name']
                );
            }
            $sendData = array('locations' => $locations);

            return $this->returnJsonResult($sendData);
        }
    }
}
