<?php

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 * Vehicles Types item
 *
 * @pw_element string $name Agreement name
 *
 * @pw_complex AgreementItem Agreement
 */

class AgreementItem{

    public $name;

    public function AgreementItem($agreement_name){
        $this->name = $agreement_name;
    }
}
/**
 * @pw_complex AgreementItemArray An array of Agreements
 */