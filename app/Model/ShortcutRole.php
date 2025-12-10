<?php

class ShortcutRole extends AppModel{
    public $useTable = 'shortcuts_roles';

    public function add( $shortcut_id , $role_id ){

        $fields = array(
            'ShortcutRole' => array(
                'shortcut_id',
                'role_id',
            )
        );

        $shortcut_role['ShortcutRole']['shortcut_id'] = $shortcut_id;
        $shortcut_role['ShortcutRole']['role_id'] = $role_id;

        $this->create();
        if(!$this->guardar( $shortcut_role, $fields )){
            return false;
        }

        return true;
    }

}