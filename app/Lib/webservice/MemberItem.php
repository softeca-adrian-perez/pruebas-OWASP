<?php

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 *
 * @pw_element string $name
 *
 * @pw_complex MemberItem
 */

class MemberItem{

    public $name;

    public function MemberItem($member){
        $this->name = $member['Distributor']['name'];
    }
}

/**
 * @pw_complex MemberItemArray An array of Members
 */