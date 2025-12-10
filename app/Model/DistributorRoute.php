<?php

class DistributorRoute extends AppModel{
    public $useTable = 'distributors_routes';

    var $hasOne = array(
        'Route',
        'Distributor'
    );

    public $validate = array(
        'order' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'start_time' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'end_time' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
    );

    public function addRelationDistributorRoute( $distributor, $route_id ){
        $distributor_route = array(
            'distributor_id' => $distributor['id'],
            'route_id' => $route_id,
            'order' => $distributor['order'],
            'start_time' => $distributor['start_time'],
            'end_time' => $distributor['end_time']
        );

        $this->create();
        if($this->save($distributor_route)){
            return true;
        }else{
            return false;
        }
    }

    public function removeDistributorRoutes( $route_id ){
        $distributor_routes = $this->findAllByRouteId( $route_id );
        foreach ($distributor_routes as $distributor_route) {
            $this->delete($distributor_route['DistributorRoute']['id']);
        }
    }
}