<?php

App::uses('AccesoComponent', 'Controller/Component');

class AccesoHelper extends AppHelper {

    private $Acceso;

    public function __construct(View $View, $settings = array()) {
        parent::__construct($View, $settings);
        $this->Acceso = new AccesoComponent(new ComponentCollection());
    }

    public function havePermission( $permiso ){
        return $this->Acceso->havePermission( $permiso );
    }

    public function haveDefaultPermission( $permiso ){
        return $this->Acceso->haveDefaultPermission( $permiso );
    }

    public function haveReversePermission( $permiso ){
        return $this->Acceso->haveReversePermission( $permiso );
    }


    public function haveNetworkRegionPermission( $permiso ){
        return $this->Acceso->haveNetworkRegionPermission( $permiso );
    }

    public function haveTradingGroupPermission( $permiso , $trading_group_id){
        return $this->Acceso->haveTradingGroupPermission( $permiso , $trading_group_id );
    }

    public function haveBranches( $distributor_id ){
        return $this->Acceso->haveBranches( $distributor_id );
    }

    public function haveGarageDistributor( $distributor_id ){
        return $this->Acceso->haveGarageDistributor( $distributor_id );
    }

    public function user($key = null){
        return $this->Acceso->user($key);
    }

    public function rol(){
        return $this->Acceso->rol();
    }

    public function getLanguagesCodeName(){
        return $this->Acceso->getLanguagesCodeName();
    }

    public function haveModulePermission($config_module_id){
        return $this->Acceso->haveModulePermission($config_module_id);
    }

    public function tiene_permiso_rm( $user ){
        return $this->Acceso->tiene_permiso_rm( $user );
    }

	public function operacion_login( $user ){
        return $this->Acceso->operacion_login( $user );
    }


}
