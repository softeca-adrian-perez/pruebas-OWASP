<?php

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 * Basic garage information: name, manager, address ...
 *
 * @pw_element string $garage_id
 * @pw_element string $garage_name
 * @pw_element string $garage_code
 * @pw_element string $creditor_number
 * @pw_element string $payment_terms
 * @pw_element string $address
 * @pw_element string $phone
 * @pw_element string $phone_international
 * @pw_element string $fax
 * @pw_element string $email
 * @pw_element string $web
 * @pw_element WsCountry $WsCountry
 * @pw_element WsProvince $WsProvince
 * @pw_element string $postal_code
 * @pw_element string $town
 * @pw_element decimal $latitude
 * @pw_element decimal $longitude
 * @pw_element boolean $active
 * @pw_element date $leaving_date
 * @pw_element boolean $service_24h
 * @pw_element string $service_phone_number
 * @pw_element string $service_phone_international
 * @pw_element string $official_branded
 * @pw_element string $oem_parts
 * @pw_element boolean $ecommerce
 * @pw_element boolean $customer_reseller
 * @pw_element boolean $customer_garages
 * @pw_element boolean $customer_drives_diy
 * @pw_element boolean $delivery_counter_fob
 * @pw_element boolean $delivery_transport_cif
 * @pw_element WsNetwork $WsNetwork
 * @pw_element boolean $is_a24h 1 if is a 24h garage.
 * @pw_element boolean $offers_a24h 1 if it offers 24h assistance to its corporative vehicles.
 * @pw_element string $service_24h_phone phone for 24h assistance to its corporative vehicles.
 * @pw_element int $rating_rm Garage's rating in RM.
 * @pw_element boolean $bds_agreement 1 if it has BDS agreements
 * @pw_element string $garage_responsible contact person responsible of the assistances.
 *
 * @pw_complex GeneralData
 */

class GeneralData{

    public $garage_id;
    public $garage_name;
    public $garage_code;
    public $creditor_number;
    public $payment_terms;
    public $address;
    public $phone;
    public $phone_international;
    public $fax;
    public $email;
    public $web;
    public $WsCountry;
    public $WsProvince;
    public $postal_code;
    public $town;
    public $latitude;
    public $longitude;
    public $active;
    public $leaving_date;
    public $service_24h;
    public $service_phone_number;
    public $service_phone_international;
    public $official_branded;
    public $oem_parts;
    public $ecommerce;
    public $customer_reseller;
    public $customer_garages;
    public $customer_drives_diy;
    public $delivery_counter_fob;
    public $delivery_transport_cif;
    public $WsNetwork;
    public $is_a24h;
    public $offers_a24h;
    public $service_24h_phone;
    public $rating_rm;
    public $bds_agreement;
    public $garage_responsible;


    public function GeneralData($general_data){
        $this->garage_id = $general_data['garage_id'];
        $this->garage_name = $general_data['garage_name'];
        $this->garage_code = $general_data['garage_code'];
        $this->creditor_number = $general_data['creditor_number'];
        $this->payment_terms = $general_data['payment_terms'];
        $this->address = $general_data['address'];
        $this->phone = $general_data['phone'];
        $this->phone_international = $general_data['phone_international'];
        $this->fax = $general_data['fax'];
        $this->email = $general_data['email'];
        $this->web = $general_data['web'];
        $this->WsCountry = new WsCountry($general_data['country']);
        $this->WsProvince = new WsProvince($general_data['province']);
        $this->postal_code = $general_data['postal_code'];
        $this->town = $general_data['town'];
        $this->latitude = $general_data['latitude'];
        $this->longitude = $general_data['longitude'];
        $this->active = ($general_data['active'] == true) ? true : false;
        $this->leaving_date = $general_data['leaving_date'];
        $this->service_24h = true;//($general_data['service_24h'] == null) ? false : true;
        $this->service_phone_number = $general_data['service_phone_number'];
        $this->service_phone_international = $general_data['service_phone_international'];
        $this->official_branded = $general_data['official_branded'];
        $this->oem_parts = $general_data['oem_parts'];
        $this->ecommerce = ($general_data['ecommerce'] == true) ? true : false;
        $this->customer_reseller = ($general_data['customer_reseller'] == true) ? true : false;
        $this->customer_garages = ($general_data['customer_garages'] == true) ? true : false;
        $this->customer_drives_diy = ($general_data['customer_drives_diy'] == true) ? true : false;
        $this->delivery_counter_fob = ($general_data['delivery_counter_fob'] == true) ? true : false;
        $this->delivery_transport_cif = ($general_data['delivery_transport_cif'] == true) ? true : false;
        $this->WsNetwork = new WsNetwork($general_data['network']);
        $this->is_a24h = $general_data['is_a24h'];
        $this->offers_a24h = $general_data['offers_a24h'];
        $this->service_24h_phone = $general_data['service_24h_phone'];
        $this->rating_rm = $general_data['rating_rm'];
        $this->bds_agreement = $general_data['bds_agreement'];
        $this->garage_responsible = $general_data['garage_responsible'];
    }
}