<?php

App::uses('PaginatorUrlHelper', 'View/Helper');

class PaginatorUrlDistributorGaragesAssociatedHelper extends PaginatorUrlHelper {
	protected function setUrlParams(){
        if ($this->request->controller == 'distributors'){
            return array(
                'controller' => 'distributors',
                'action' => 'view',
                $this->request->params['pass'][0]
            );
        } elseif ($this->request->controller == 'clients'){
            return array(
                'controller' => 'clients',
                'action' => 'report_distributor',
                $this->request->params['pass'][0]
            );
        }

    }
}