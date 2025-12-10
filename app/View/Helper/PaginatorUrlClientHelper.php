<?php

App::uses('PaginatorUrlHelper', 'View/Helper');

class PaginatorUrlClientHelper extends PaginatorUrlHelper {
	protected function setUrlParams(){
        return array(
            'controller' => 'clients',
            'action' => 'home'
        );
    }
}
