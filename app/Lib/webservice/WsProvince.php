<?php

/**
 * Province basic info.
 *
 * @pw_element string $name
 * @pw_element string $province_code
 *
 * @pw_complex WsProvince Province details
 */

class WsProvince{

    public $name;
    public $province_code;

    public function WsProvince($province_data){
        $this->name = isset($province_data['name']) ? $province_data['name'] : '';
        $this->province_code = isset($province_data['province_code']) ? $province_data['province_code'] : '';
    }
}