<?php
if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 * Service item
 *
 * @pw_element string $name Service name
 *
 * @pw_complex ServiceItem
 */

class ServiceItem{
    public $name;

    public function ServiceItem($service_name){
        $this->name = $service_name;
    }
}

/**
 * @pw_complex ServiceItemArray An array of services
 */