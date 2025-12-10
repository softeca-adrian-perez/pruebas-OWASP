<?php

class Route extends AppModel{
    public $useTable = 'routes';

    public $hasMany = array(
        'GarageRoute',
        'DistributorRoute'
    );

	public $validate = array(
		'name' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public function conditions( $fields ){
        $conditions = array();
        if(!empty( $fields['name'])){
            $conditions[] = $this->_conditionName( $fields['name'] );
        }
        if(!empty( $fields['type'])){
            $conditions[] = $this->_conditionType( $fields['type'] );
        }
        if(!empty($fields['from'])){
            $conditions[] = $this->_conditionDateFrom($fields['from']);
        }
        if(!empty($fields['to'])){
            $conditions[] = $this->_conditionDateTo($fields['to']);
        }

        return $conditions;
    }

    private function _conditionName( $name ){
        return array('Route.name LIKE' => '%' . $name . '%');
    }

    private function _conditionType( $type ){
        return array('Route.type' => $type);
    }

    private function _conditionDateFrom($from_date){
        return array('Route.creation_date >=' => $from_date);
    }

    private function _conditionDateTo($to_date){
        return array('Route.creation_date <=' => $to_date);
    }

    public function new_route($route){
        $fields = array(
            'Route' => array(
                'type',
                'name',
                'creation_date',
                'user_creation_id',
                'user_assigned_id',
            )
        );
        $route['Route']['creation_date'] = date('Y-m-d');

        $this->create();
        $task_bd = $this->guardar($route, $fields);
        if (!$task_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function edit_route($route){
        $fields = array(
            'Route' => array(
                'id',
                'type',
                'name',
                'creation_date',
            )
        );

        $task_bd = $this->guardar($route, $fields);
        if (!$task_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function search_list(){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            ),
        ));
    }

    public function search_list_garage(){
        return $this->find('list',array(
            'conditions' => array(
                'type' => ConstantsVisitType::GARAGE,
                'user_assigned_id' => CakeSession::read('Auth.User.id')
            ),
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            ),
        ));
    }
    public function search_list_distributor(){
        return $this->find('list',array(
            'conditions' => array(
                'type' => ConstantsVisitType::DISTRIBUTOR,
                'user_assigned_id' => CakeSession::read('Auth.User.id')
            ),
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            ),
        ));
    }
}