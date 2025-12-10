<?php

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 *
 *
 * @pw_element MemberItemArray $MemberItemArray
 *
 * @pw_complex Members
 */

class Members{
    public $MemberItemArray;

    public function Members($members){
        $members_array = array();
        foreach($members as $member){
            $members_array[] = new MemberItem($member);
        }
        $this->MemberItemArray = $members_array;
    }
}