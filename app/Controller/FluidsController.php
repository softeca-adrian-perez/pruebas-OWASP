<?php
class FluidsController extends AppController
{
    public $uses = array(
        "GarageNetwork",
        "GarageNetworkFluid",
        "Fluid",
        "Garage"
    );

    /**
     * API returns an array with the fluids the garage has in the network sent.
     *
     * @param network_id Recieved by POST request
     * @param garages_codes Recieved by POST request, list of guids
     *
     * @return array
     */
    public function get_fluids_garages()
    {
        $dataReceived = $this->request->input('json_decode');
        $authOk = $this->api_auth_and_log(getallheaders(), $dataReceived);
        if (!$authOk) {
            $resultado['auth'] = ApiUtil::ERROR_AUTHENTICATION;
            return $this->returnJsonResult($resultado);
        }

        if (!empty($dataReceived) && $this->request->is("POST")) {
            $sendData = array('garage_fluids' => array());

            $networkId = isset($dataReceived->network_id) ? $dataReceived->network_id : null;
            $garageCodes = isset($dataReceived->garages_codes) ? $dataReceived->garages_codes : null;

            if (empty($networkId) || empty($garageCodes)) {
                return $this->returnJsonResult($sendData);
            }

            foreach ($garageCodes as $garageId) {
                $fluidsGarageSend = array();

                $garageFoundId = null;
                if ($garageId) {
                    $garageFound = $this->Garage->findByGuid($garageId, ['id']);
                    if (isset($garageFound['Garage']['id'])) {
                        $garageFoundId = $garageFound['Garage']['id'];
                    }
                }
                $garageNetwork = $this->GarageNetwork->find('first', array(
                    'conditions' => array(
                        'network_id' => $networkId,
                        'garage_id' => $garageFoundId,
                        'status' => ConstantsNetworksStatus::LIVE
                    )
                ));

                if (!$garageNetwork) {
                    return $this->returnJsonResult($sendData);
                }

                $garageNetworkFluids = $this->Fluid->find(
                    'all',
                    array(
                        'joins' => array(
                            array(
                                'alias' => 'GarageNetworkFluid',
                                'table' => 'garages_networks_fluids',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'GarageNetworkFluid.fluid_id = Fluid.id',
                                    'GarageNetworkFluid.garage_network_id' => $garageNetwork["GarageNetwork"]["id"],
                                ),
                            )
                        ),
                        'conditions' => array(
                            "Fluid.has_advanced_settings" => ConstantsBooleans::NO_ACTIVE,
                            "Fluid.active" => ConstantsBooleans::ACTIVE
                        ),
                        'fields' => array('Fluid.*', 'GarageNetworkFluid.*')
                    )
                );

                $dataFluidsGarage = array();
                $parentCodesActives = array();

                foreach ($garageNetworkFluids as $garageNetworkFluid) {
                    if ($garageNetworkFluid["Fluid"]["parent_code"] == null) {
                        // parent code

                        $parentCodesActives[] = $garageNetworkFluid["Fluid"]["code"];

                        // there is price
                        if ($garageNetworkFluid["GarageNetworkFluid"]["price"] != null) {
                            $code = $garageNetworkFluid["Fluid"]["code"];
                            if (!isset($dataFluidsGarage[$code])) {
                                $dataFluidsGarage[$code] = array();
                            }
                            $dataFluidsGarage[$code]['price'] = $garageNetworkFluid["GarageNetworkFluid"]["price"];
                        }
                    } else {
                        // child code

                        // there is price
                        if ($garageNetworkFluid["GarageNetworkFluid"]["price"] != null) {
                            $parentCode = $garageNetworkFluid["Fluid"]["parent_code"];
                            if (!isset($dataFluidsGarage[$parentCode])) {
                                $dataFluidsGarage[$parentCode] = array(
                                    'specs' => array()
                                );
                            }

                            $dataFluidsGarage[$parentCode]['specs'][] = array(
                                'code' => $garageNetworkFluid["Fluid"]["code"],
                                'price' => $garageNetworkFluid["GarageNetworkFluid"]["price"]
                            );
                        }
                    }
                }

                $fluidsGarageSend["garage_id"] = $garageId;

                foreach ($dataFluidsGarage as $parentCode => $parentCodeData) {
                    if (in_array($parentCode, $parentCodesActives)) {
                        $tmp = array(
                            'code' => $parentCode,
                        );
                        if (isset($parentCodeData['price'])) {
                            $tmp['price'] = $parentCodeData['price'];
                        }
                        if (isset($parentCodeData['specs'])) {
                            $tmp['specs'] = $parentCodeData['specs'];
                        }
                        $fluidsGarageSend["fluids"][] = $tmp;
                    }
                }

                $sendData["garage_fluids"][] = $fluidsGarageSend;
            }

            return $this->returnJsonResult($sendData);
        }
    }
}
