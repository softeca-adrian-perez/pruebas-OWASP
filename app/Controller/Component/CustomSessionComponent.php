<?php

App::uses('SessionComponent', 'Controller/Component');

class CustomSessionComponent extends SessionComponent {

    public function setFlashSuccess($mensaje) {
        parent::setFlash($mensaje, 'Comun/alert', array(
            'class' => 'exito',
            'hideSeconds' => ConstantsFlashSeconds::FLASH_SECONDS
        ));
    }

    public function setFlashError($mensaje) {
        parent::setFlash($mensaje, 'Comun/alert', array(
            'class' => 'fallo'
        ));
    }

    public function setFlashInfo($mensaje) {
        parent::setFlash($mensaje, 'Comun/alert', array(
            'class' => 'informacion'
        ));
    }

}