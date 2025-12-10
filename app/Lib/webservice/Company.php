<?php

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 *
 * @pw_element string $name
 * @pw_element string $address
 * @pw_element WsProvince $province
 * @pw_element WsCountry $country
 * @pw_element string $postal_code
 * @pw_element string $vat_number
 * @pw_element string $town
 *
 * @pw_complex Company
 */

class Company{
    public $name;
    public $address;
    public $province;
    public $country;
    public $postal_code;
    public $vat_number;
    public $town;

    public function Company($company_data){
        $this->name = $company_data['company_name'];
        $this->address = $company_data['company_address'];
        $this->province = new WsProvince($company_data['province']);
        $this->country = new WsCountry($company_data['country']);
        $this->postal_code = $company_data['company_postal_code'];
        $this->vat_number = $company_data['vat_number'];
        $this->town = $company_data['company_town'];
    }
}