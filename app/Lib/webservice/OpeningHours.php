<?php

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 * Basic garage information: name, manager, address ...
 *
 * @pw_element string $monday_friday
 * @pw_element string $weekend
 * @pw_element string $monday_open_1
 * @pw_element string $monday_closed_1
 * @pw_element string $monday_open_2
 * @pw_element string $monday_closed_2
 * @pw_element string $tuesday_open_1
 * @pw_element string $tuesday_closed_1
 * @pw_element string $tuesday_open_2
 * @pw_element string $tuesday_closed_2
 * @pw_element string $wednesday_open_1
 * @pw_element string $wednesday_closed_1
 * @pw_element string $wednesday_open_2
 * @pw_element string $wednesday_closed_2
 * @pw_element string $thursday_open_1
 * @pw_element string $thursday_closed_1
 * @pw_element string $thursday_open_2
 * @pw_element string $thursday_closed_2
 * @pw_element string $friday_open_1
 * @pw_element string $friday_closed_1
 * @pw_element string $friday_open_2
 * @pw_element string $friday_closed_2
 * @pw_element string $saturday_open_1
 * @pw_element string $saturday_closed_1
 * @pw_element string $saturday_open_2
 * @pw_element string $saturday_closed_2
 * @pw_element string $sunday_open_1
 * @pw_element string $sunday_closed_1
 * @pw_element string $sunday_open_2
 * @pw_element string $sunday_closed_2
 *
 * @pw_complex OpeningHours
 */

class OpeningHours{
    public $monday_friday;
    public $weekend;

    public $monday_open_1;
    public $monday_closed_1;
    public $monday_open_2;
    public $monday_closed_2;
    public $tuesday_open_1;
    public $tuesday_closed_1;
    public $tuesday_open_2;
    public $tuesday_closed_2;
    public $wednesday_open_1;
    public $wednesday_closed_1;
    public $wednesday_open_2;
    public $wednesday_closed_2;
    public $thursday_open_1;
    public $thursday_closed_1;
    public $thursday_open_2;
    public $thursday_closed_2;
    public $friday_open_1;
    public $friday_closed_1;
    public $friday_open_2;
    public $friday_closed_2;
    public $saturday_open_1;
    public $saturday_closed_1;
    public $saturday_open_2;
    public $saturday_closed_2;
    public $sunday_open_1;
    public $sunday_closed_1;
    public $sunday_open_2;
    public $sunday_closed_2;

    public function OpeningHours($opening_hours){
        foreach($opening_hours as $column => $value){
            $this->$column = $value;
        }
    }
}