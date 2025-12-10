<?php

App::uses('PaginatorUrlHelper', 'View/Helper');

class PaginatorUrlGarageHelper extends PaginatorUrlHelper {
	protected function setUrlParams(){
        return array(
            'controller' => 'garages',
            'action' => 'home'
        );
    }
}
