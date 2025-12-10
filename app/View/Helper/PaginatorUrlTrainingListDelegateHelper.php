<?php

App::uses('PaginatorUrlHelper', 'View/Helper');

class PaginatorUrlTrainingListDelegateHelper extends PaginatorUrlHelper {
	protected function setUrlParams(){
        return array(
            'controller' => 'trainings_list_delegates',
            'action' => 'home'
        );
    }
}
