<?php

App::uses('PaginatorUrlHelper', 'View/Helper');

class PaginatorUrlClientDistributorHelper extends PaginatorUrlHelper {
	protected function setUrlParams(){
        return array(
            'controller' => 'clients',
            'action' => 'home_distributors'
        );
    }
}
