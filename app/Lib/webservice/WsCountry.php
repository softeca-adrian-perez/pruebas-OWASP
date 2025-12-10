<?php

/**
 * Country basic info.
 *
 * @pw_element string $name
 * @pw_element string $country_code
 *
 * @pw_complex WsCountry Country details
 */

class WsCountry{

    public $name;
    public $country_code;

    public function WsCountry($country_data){
        $this->name = isset($country_data['name']) ? $country_data['name'] : '';
        $this->country_code = isset($country_data['country_code']) ? $country_data['country_code'] : '';
    }

}