<?php
abstract class BaseWidget extends AppModel{

    /**
     * @param int|null $role_id
     * @return string|null Returns null if role does not have a custom name
     */
    public function getDisplayNameForRole($role_id=null){
        return null;
    }

}
