<?php

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 * Basic garage information: name, manager, address ...
 *
 * @pw_element string $manager_title
 * @pw_element string $manager_name
 * @pw_element string $manager_surname
 * @pw_element string $manager_phone
 *
 * @pw_complex Manager
 */

class Manager{
    public $manager_title;
    public $manager_name;
    public $manager_surname;
    public $manager_phone;

    public function Manager($manager_data){
        $this->manager_title = $manager_data['manager_title'];
        $this->manager_name = $manager_data['manager_name'];
        $this->manager_surname = $manager_data['manager_surname'];
        $this->manager_phone = $manager_data['manager_phone'];
    }
}