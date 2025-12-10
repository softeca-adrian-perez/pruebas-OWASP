<?php

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 *
 *
 * @pw_element AgreementItemArray $AgreementItemArray
 *
 * @pw_complex Agreements
 */

class Agreements{
    public $AgreementItemArray;

    public function Agreements($agreements){
        $agreements_array = array();
        foreach($agreements as $agreement){
            $agreements_array[] = new AgreementItem($agreement);
        }
        $this->AgreementItemArray = $agreements_array;
    }
}