<?php

App::uses('PaginatorUrlHelper', 'View/Helper');

class PaginatorUrlDistributorHelper extends PaginatorUrlHelper {
	protected function setUrlParams(){
        return array(
            'controller' => 'distributors',
            'action' => 'home'
        );
    }
}
