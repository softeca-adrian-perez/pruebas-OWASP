<?php

App::uses('BaseWidget', 'Panel.Model');

class WidgetAlert extends BaseWidget{

    public $useTable = false;

    public function GetDataWidget(array $user){
        /* @var $alertModel Alert */
        $alertModel = ClassRegistry::init("Alert");

        $allowedRoles = ConstantsRoles::SUPER_ADMIN;

        $data = array();
        if($user['Role']['id'] == $allowedRoles){
            $data = $alertModel->findForWidget($user);
        }

        foreach($data as $key => $alert){
            if( $alert['Alert']['alert_type_id'] == ConstantesAlertsTypesId::RM ){
                $this->RepairMaintenance = ClassRegistry::init("RepairMaintenance");
                $accion_externa = $this->RepairMaintenance->acceso_externo ($user['id'],$alert['Alert']['rm_alert_id'],ConstantsExternalAccess::ALERT);
                $data[$key]['Alert']['url_rm'] =
                    JWT::encode(
                        array(
                            'datos' => $accion_externa['datos'],
                            'check' => $accion_externa['check'],
                        ),
                        Texto::encryptDecryptText(REPAIR_MAINTENANCE_GNM_CONNECTION_KEY, false)
                    );
            }
        }
        return $data;
    }

}
