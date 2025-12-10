<?php

class GarageRoute extends AppModel{
    public $useTable = 'garages_routes';

    var $hasOne = array(
        'Route',
        'Garage'
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

    public function addRelationGarageRoute( $garage, $route_id){
        $garage_route = array(
            'garage_id' => $garage['id'],
            'route_id' => $route_id,
            'order' => $garage['order'],
            'start_time' => $garage['start_time'],
            'end_time' => $garage['end_time']
        );

        $this->create();
        if($this->save($garage_route)){
            return true;
        }else{
            return false;
        }
    }

    public function removeGarageRoutes( $route_id ){
        $garage_routes = $this->findAllByRouteId( $route_id );
        foreach ($garage_routes as $garage_route) {
            $this->delete($garage_route['GarageRoute']['id']);
        }
    }
}