<?php

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 *
 *
 * @pw_element VehicleTypeItemArray $VehicleTypeItemArray
 *
 * @pw_complex HeavyVehicles
 */

class HeavyVehicles{
    public $VehicleTypeItemArray;

    public function HeavyVehicles($vehicle_types){
        $vehicle_types_array = array();
        foreach($vehicle_types as $vehicle_type){
            $vehicle_types_array[] = new VehicleTypeItem($vehicle_type);
        }
        $this->VehicleTypeItemArray = $vehicle_types_array;
    }

}