<?php
if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 * Staff
 *
 * @pw_element CoordinatorItemArray $CoordinatorItemArray
 *
 * @pw_complex Coordinators Coordinators
 */
class Coordinators{

    public $CoordinatorItemArray;

    public function Coordinators($coordinators){
        $coordinators_array = array();
        foreach($coordinators as $coordinator){
            $coordinators_array[] = new CoordinatorItem($coordinator);
        }
        $this->CoordinatorItemArray = $coordinators_array;
    }
}