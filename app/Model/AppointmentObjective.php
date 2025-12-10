<?php

class AppointmentObjective extends AppModel{
    public $useTable = 'appointments_objectives';

	public $validate = array(
        'name' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_name',
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

	public function add( $objective ) {
		$fields = array(
            'AppointmentObjective' => array(
                'name',
                'tg',
                'gpc',
                'tg_personal',
                'tg_management',
				'gpc_personal',
                'gpc_management',
				'aag_region_id'
            )
        );
		$objective_tmp['AppointmentObjective']['name'] = $objective['AppointmentObjective']['name'];

		if( $objective['AppointmentObjective']['tg_personal'] || $objective['AppointmentObjective']['tg_management'] ){
			$objective_tmp['AppointmentObjective']['tg'] = 1;
			if( $objective['AppointmentObjective']['tg_personal'] ){
				$objective_tmp['AppointmentObjective']['tg_personal'] = 1;
			}
			if( $objective['AppointmentObjective']['tg_management'] ){
				$objective_tmp['AppointmentObjective']['tg_management'] = 1;
			}
		}

		if( $objective['AppointmentObjective']['gpc_personal'] || $objective['AppointmentObjective']['gpc_management'] ){
			$objective_tmp['AppointmentObjective']['gpc'] = 1;
			if( $objective['AppointmentObjective']['gpc_personal']){
				$objective_tmp['AppointmentObjective']['gpc_personal'] = 1;
			}
			if( $objective['AppointmentObjective']['gpc_management'] ){
				$objective_tmp['AppointmentObjective']['gpc_management'] = 1;
			}
		}

		$objective_tmp['AppointmentObjective']['aag_region_id'] = $objective['AppointmentObjective']['aag_region_id'];

		$this->create();
        $objective_bd = $this->guardar($objective_tmp, $fields);
        if (!$objective_bd) {
            return false;
        }
        $this->commit();
        return $objective_bd;
    }

	public function edit( $objective ){

		$fields = array(
            'AppointmentObjective' => array(
                'name',
                'tg',
                'gpc',
                'tg_personal',
                'tg_management',
				'gpc_personal',
                'gpc_management',
				'aag_region_id'
            )
        );


		if( $objective['AppointmentObjective']['tg_personal'] || $objective['AppointmentObjective']['tg_management'] ){
			$objective['AppointmentObjective']['tg'] = 1;
		}
		else{
			$objective['AppointmentObjective']['tg'] = 0;
		}

		if( $objective['AppointmentObjective']['gpc_personal'] || $objective['AppointmentObjective']['gpc_management'] ){
			$objective['AppointmentObjective']['gpc'] = 1;
		}
		else{
			$objective['AppointmentObjective']['gpc'] = 0;
		}

        if($tmp = $this->save($objective, true, $fields)) {
            return $tmp;
        }
        return false;


    }

	public function delete_objective( $id ){
        if( $this->eliminar($id)){
            return true;
        }
        return false;
    }

	public function getManagementObjectives( $gpc = null ) {

		if( $gpc ){
			return $this->find('list', array(
                'conditions' => array(
                    'AppointmentObjective.gpc_management' => ConstantsBooleans::YES
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            ));
		}else if( $gpc === null ){
			return $this->find('list', array(
                'conditions' => array(
					'OR' => array(
						'AppointmentObjective.tg_management' => ConstantsBooleans::YES,
						'AppointmentObjective.gpc_management' => ConstantsBooleans::YES,
					)
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            ));
		}
		else{
			return $this->find('list', array(
                'conditions' => array(
                    'AppointmentObjective.tg_management' => ConstantsBooleans::YES,
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            ));
		}

    }

	public function getManagementObjectivesByType( $user , $distributor_id , $appointment_id = null ) {

		if( $user['role_id'] == ConstantsRoles::BDM_TG ){
			$query = array(
				'joins' => array(
					array(
						'alias' => 'DistributorObjective',
						'table' => 'distributors_objectives',
						'type' => 'INNER',
						'conditions' => array(
							'DistributorObjective.objective_id = AppointmentObjective.id',
						),
					),
				),
                'conditions' => array(
                    'AppointmentObjective.tg_management' => ConstantsBooleans::YES,
                    'DistributorObjective.distributor_id' => $distributor_id,
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            );
			$objectives = $this->find('list', $query);

			$query2 = array(
				'joins' => array(
					array(
						'alias' => 'AppointmentObjectiveComment',
						'table' => 'appointments_objective_comments',
						'type' => 'INNER',
						'conditions' => array(
							'AppointmentObjectiveComment.objective_id = AppointmentObjective.id',
						),
					),
				),
                'conditions' => array(
                    'AppointmentObjectiveComment.appointment_id' => $appointment_id,
					'AppointmentObjective.tg_management' => ConstantsBooleans::YES,
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            );
			$objectives2 = $this->find('list', $query2);

			$objectives3 = $objectives + $objectives2;
		}
		else{

			$query = array(
				'joins' => array(
					array(
						'alias' => 'DistributorObjective',
						'table' => 'distributors_objectives',
						'type' => 'INNER',
						'conditions' => array(
							'DistributorObjective.objective_id = AppointmentObjective.id',
						),
					),
				),
                'conditions' => array(
                    'AppointmentObjective.gpc_management' => ConstantsBooleans::YES,
					'DistributorObjective.distributor_id' => $distributor_id,
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            );
			$objectives = $this->find('list', $query);

			$query2 = array(
				'joins' => array(
					array(
						'alias' => 'AppointmentObjectiveComment',
						'table' => 'appointments_objective_comments',
						'type' => 'INNER',
						'conditions' => array(
							'AppointmentObjectiveComment.objective_id = AppointmentObjective.id',
						),
					),
				),
                'conditions' => array(
                    'AppointmentObjectiveComment.appointment_id' => $appointment_id,
					'AppointmentObjective.gpc_management' => ConstantsBooleans::YES,
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            );
			$objectives2 = $this->find('list', $query2);

			$objectives3 = $objectives + $objectives2;

		}

		return $objectives3;
    }

	public function getPersonalObjectives( ) {
        return $this->find('list', array(
                'conditions' => array(
                    'AppointmentObjective.tg_personal' => ConstantsBooleans::YES
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            )
        );
    }

	public function getPersonalObjectivesByType( $user ) {

		if( $user['role_id'] == ConstantsRoles::BDM_TG ){
			$query = array(
                'conditions' => array(
                    'AppointmentObjective.tg_personal' => ConstantsBooleans::YES,
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            );
		}
		else{//$user['role_id'] == ConstantsRoles::GPC_LOGISTICS_BDM
			$query = array(
                'conditions' => array(
                    'AppointmentObjective.gpc_personal' => ConstantsBooleans::YES,
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            );
		}

		$objectives = $this->find('list', $query);

		return $objectives;

    }

	public function getTgManagementObjectives( ) {
        return $this->find('list', array(
                'conditions' => array(
                    'AppointmentObjective.tg_management' => ConstantsBooleans::YES
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            )
        );
    }

	public function getGpcManagementObjectives( ) {
        return $this->find('list', array(
                'conditions' => array(
                    'AppointmentObjective.gpc_management' => ConstantsBooleans::YES
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            )
        );
    }

	public function getPersonalObjectivesByTypeManagement($appointment_id) {

		$query = array(
			'joins' => array(
				array(
					'alias' => 'AppointmentObjectiveComment',
					'table' => 'appointments_objective_comments',
					'type' => 'LEFT',
					'conditions' => array(
						'AppointmentObjectiveComment.objective_id = AppointmentObjective.id',
					),
				),
			),
			'conditions' => array(
				'AppointmentObjectiveComment.type' => ConstantsTypeAgreement::MANAGEMENT,
				'AppointmentObjectiveComment.appointment_id' => $appointment_id,
			),
			'fields' => array(
				'AppointmentObjective.id',
				'AppointmentObjective.name',
			),
			'order' => array('AppointmentObjective.name ASC')
		);

		$objectives = $this->find('list', $query);

		return $objectives;

    }

	public function getPersonalObjectivesByTypePersonal($appointment_id) {

		$query = array(
			'joins' => array(
				array(
					'alias' => 'AppointmentObjectiveComment',
					'table' => 'appointments_objective_comments',
					'type' => 'LEFT',
					'conditions' => array(
						'AppointmentObjectiveComment.objective_id = AppointmentObjective.id',
					),
				),
			),
			'conditions' => array(
				'AppointmentObjectiveComment.type' => ConstantsTypeAgreement::PERSONAL,
				'AppointmentObjectiveComment.appointment_id' => $appointment_id,
			),
			'fields' => array(
				'AppointmentObjective.id',
				'AppointmentObjective.name',
			),
			'order' => array('AppointmentObjective.name ASC')
		);
		
		$objectives = $this->find('list', $query);

		$nullObjectiveQuery = array(
			'joins' => array(
				array(
					'alias' => 'AppointmentObjectiveComment',
					'table' => 'appointments_objective_comments',
					'type' => 'LEFT',
					'conditions' => array(
						'AppointmentObjectiveComment.type' => ConstantsTypeAgreement::PERSONAL,
						'AppointmentObjectiveComment.appointment_id' => $appointment_id,
					),
				),
			),
			'conditions' => array(
				'AppointmentObjectiveComment.objective_id IS NULL',
			),
			'fields' => array(
				'AppointmentObjectiveComment.id',
				'AppointmentObjectiveComment.objective',
			),
			'order' => array('AppointmentObjective.name ASC')
		);

		$nullObjectives = $this->find('list', $nullObjectiveQuery);

		foreach ($nullObjectives as $value) {
			$objectives[$value] = $value;
		}
    	return $objectives;

	}

	public function getPersonalObjectivesByTypePersonalAssoc($appointment_id) {

		$query = array(
			'joins' => array(
				array(
					'alias' => 'AppointmentObjectiveComment',
					'table' => 'appointments_objective_comments',
					'type' => 'LEFT',
					'conditions' => array(
						'AppointmentObjectiveComment.objective_id = AppointmentObjective.id',
					),
				),
			),
			'conditions' => array(
				'AppointmentObjectiveComment.type' => ConstantsTypeAgreement::PERSONAL,
				'AppointmentObjectiveComment.appointment_id' => $appointment_id,
				'AppointmentObjectiveComment.objective_id IS NULL',
			),
			'fields' => array(
				'AppointmentObjective.id',
				'AppointmentObjective.name',
			),
			'order' => array('AppointmentObjective.name ASC')
		);

		$objectives = $this->find('list', $query);

		return $objectives;

	}

	public function getPersonalObjectivesByTypeAssoc($user, $appointment_id) {
		
		if ($user['role_id'] == ConstantsRoles::BDM_TG) {
			$role = 'AppointmentObjective.tg_personal';
		}else{
			$role = 'AppointmentObjective.gpc_personal';
		}

		$query = array(
			'joins' => array(
				array(
					'alias' => 'AppointmentObjectiveComment',
					'table' => 'appointments_objective_comments',
					'type' => 'LEFT',
					'conditions' => array(
						'AppointmentObjectiveComment.objective_id = AppointmentObjective.id',
						'AppointmentObjectiveComment.appointment_id' => $appointment_id,
						'AppointmentObjectiveComment.type' => ConstantsTypeAgreement::PERSONAL
					),
				),
			),
			'conditions' => array(
				$role => ConstantsBooleans::YES,
				'AppointmentObjectiveComment.objective_id IS NULL',
			),
			'fields' => array(
				'AppointmentObjective.id',
				'AppointmentObjective.name'
			),
			'order' => array('AppointmentObjective.name ASC')
		);
	
		$objectives = $this->find('list', $query);
	
		return $objectives;
	}
	
	public function getTgManagementObjectivesAagRegion($aag_region_id) {
        return $this->find('list', array(
                'conditions' => array(
                    'AppointmentObjective.tg_management' => ConstantsBooleans::YES,
                    'AppointmentObjective.aag_region_id' => $aag_region_id,
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            )
        );
    }

	public function getGpcManagementObjectivessAagRegion($aag_region_id) {
        return $this->find('list', array(
                'conditions' => array(
                    'AppointmentObjective.gpc_management' => ConstantsBooleans::YES,
                    'AppointmentObjective.aag_region_id' => $aag_region_id,
                ),
				'fields' => array(
					'AppointmentObjective.id',
					'AppointmentObjective.name'
				),
                'order' => array('AppointmentObjective.name ASC')
            )
        );
    }

}
