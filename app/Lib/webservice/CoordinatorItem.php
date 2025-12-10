<?php

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;


/**
 * CoordinatorItem
 *
 * @pw_element string $name
 * @pw_element string $surname
 *
 * @pw_complex CoordinatorItem Staff
 */
class CoordinatorItem{

    public $name;
    public $surname;

    public function CoordinatorItem($coordinator){
        $this->name = $coordinator['User']['surname'];
        $this->surname = $coordinator['User']['name'];
    }
}

/**
 * @pw_complex CoordinatorItemArray An array of Coordinators
 */