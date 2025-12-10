<?php

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 *
 *
 * @pw_element ServiceItemArray $ServiceItemArray
 *
 * @pw_complex HeavyComVehicleServices
 */

class HeavyComVehicleServices{

    public $ServiceItemArray;

    public function HeavyComVehicleServices($services){
        $services_array = array();
        foreach($services as $service){
            $services_array[] = new ServiceItem($service);
        }
        $this->ServiceItemArray = $services_array;
    }
}