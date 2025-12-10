<?php
if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 * Vehicles Types item
 *
 * @pw_element string $name Vehicle Type name
 *
 * @pw_complex VehicleTypeItem Member
 */

class VehicleTypeItem{
    public $name;

    public function VehicleTypeItem($vehicle_type_name){
        $this->name = $vehicle_type_name;
    }
}

/**
 * @pw_complex VehicleTypeItemArray An array of vehicles types
 */