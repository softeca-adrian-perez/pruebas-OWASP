<?php

class UserReassignment extends AppModel{
    public $useTable = 'users_reassignments';

    public $hasMany = array(
        'User' ,
    );

    public $validate = array(
        'date_from' => array(
            'rule' => 'date', 'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'date_to' => array(
            'rule' => 'date', 'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
    );

    public function add($user_reassignment){
        $fields = array(
            'UserReassignment' => array(
                'user_id_origin',
                'user_id_destination',
                'garage_contact_bdm_id',
                'distributor_contact_bdm_id',
                'contact_contact_list_id',
                'task_id',
                'appointment_id',
                'route_id',
                'date_from',
                'date_to',
                'active',
                'permanent',
            )
        );

        $this->create();
        return $this->guardar($user_reassignment, $fields);

    }

    public function reassign_user_scheduled_task(){
        $reassignments = $this->findAllByPermanent(ConstantsBooleans::NO);

		$this->begin();
		if ($this->make_revert_reassignment($reassignments)) {
			$this->commit();
		}
    }

    public function make_revert_reassignment($reassignments, $revert = false){
        $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');
        $this->DistributorContactBdm = ClassRegistry::init('DistributorContactBdm');
        $this->ContactContactList = ClassRegistry::init('ContactContactList');
        $this->Task = ClassRegistry::init('Task');
        $this->Appointment = ClassRegistry::init('Appointment');
        $this->Route = ClassRegistry::init('Route');

		$garage_contact_bdms = array();
		$distributor_contact_bdms = array();
		$contact_contact_lists = array();
		$tasks = array();
		$appointments = array();
		$routes = array();
		$idsDelete = array();
		foreach ($reassignments as &$reassignment) {
            if ($reassignment['UserReassignment']['date_to'] == date('Y-m-d') || $revert) {
				$make = false;
			} elseif ($reassignment['UserReassignment']['date_from'] == date('Y-m-d')) {
                $make = true;
            } else {
				continue;
			}
			$user_id = $make ? $reassignment['UserReassignment']['user_id_destination'] : $reassignment['UserReassignment']['user_id_origin'];
			$user_tmp = $this->User->findById($user_id);
			$contact_id = $user_tmp['User']['contact_id'];

			if ($reassignment['UserReassignment']['garage_contact_bdm_id'] != null) {
				$garage_contact_bdm = $this->GarageContactBdm->findById($reassignment['UserReassignment']['garage_contact_bdm_id']);
				$garage_contact_bdm['GarageContactBdm']['contact_id'] = $contact_id;
				$garage_contact_bdms[] = $garage_contact_bdm;
			}

			if ($reassignment['UserReassignment']['distributor_contact_bdm_id'] != null) {
				$distributor_contact_bdm = $this->DistributorContactBdm->findById($reassignment['UserReassignment']['distributor_contact_bdm_id']);
				$distributor_contact_bdm['DistributorContactBdm']['contact_id'] = $contact_id;
                $distributor_contact_bdm['DistributorContactBdm']['updated_at'] = date('Y-m-d H:i:s');
				$distributor_contact_bdms[] = $distributor_contact_bdm;
			}

			if ($reassignment['UserReassignment']['contact_contact_list_id'] != null) {
				$contact_contact_list = $this->ContactContactList->findById($reassignment['UserReassignment']['contact_contact_list_id']);
				$contact_contact_list['ContactContactList']['contact_id'] = $contact_id;
				$contact_contact_lists[] = $contact_contact_list;
			}

			if ($reassignment['UserReassignment']['task_id'] != null) {
				$task = $this->Task->findById($reassignment['UserReassignment']['task_id']);
				$task['Task']['user_assigned_id'] = $user_id;
				$tasks[] = $task;
			}

			if ($reassignment['UserReassignment']['appointment_id'] != null) {
				$appointment = $this->Appointment->findById($reassignment['UserReassignment']['appointment_id']);
				$appointment['Appointment']['user_assigned_id'] = $user_id;
				$appointments[] = $appointment;
			}

			if ($reassignment['UserReassignment']['route_id'] != null) {
				$route = $this->Route->findById($reassignment['UserReassignment']['route_id']);
				$route['Route']['user_assigned_id'] = $user_id;
				$routes[] = $route;
			}

			if ($make) {
				$reassignment['UserReassignment']['active'] = ConstantsBooleans::YES;
			} else {
				$idsDelete[] = $reassignment['UserReassignment']['id'];
			}
		}

		if (
			!$this->GarageContactBdm->guardarVarios($garage_contact_bdms, array('GarageContactBdm' => array('contact_id'))) ||
			!$this->DistributorContactBdm->guardarVarios($distributor_contact_bdms, array('DistributorContactBdm' => array('contact_id', 'updated_at'))) ||
			!$this->ContactContactList->guardarVarios($contact_contact_lists, array('ContactContactList' => array('contact_id'))) ||
			!$this->Task->guardarVarios($tasks, array('Task' => array('user_assigned_id'))) ||
			!$this->Appointment->guardarVarios($appointments, array('Appointment' => array('user_assigned_id'))) ||
			!$this->Route->guardarVarios($routes, array('Route' => array('user_assigned_id'))) ||
			!$this->guardarVarios($reassignments, array('UserReassignment' => array('active')))
		) {
			return false;
		}
		if (!empty($idsDelete)) {
			return $this->deleteAll(['id IN' => $idsDelete ?? []]);
		}
		return true;
    }

    public function getReassignmentByOriginAndDestinationAndDate( $user_origin_id, $destination_origin_id, $date_from, $date_to ){
        return $this->find('first', array(
            'conditions' => array(
                array(
                    'UserReassignment.user_id_origin' => $user_origin_id,
                    'UserReassignment.user_id_destination' => $destination_origin_id,
                    'or' => array(
                        array(
                            'and' => array(
                                'UserReassignment.date_from <= ' => Fecha::toFormatoBd($date_from),
                                'UserReassignment.date_to > ' => Fecha::toFormatoBd($date_from),
                            )
                        ),
                        array(
                            'and' => array(
                                'UserReassignment.date_from <= ' => Fecha::toFormatoBd($date_to),
                                'UserReassignment.date_to > ' => Fecha::toFormatoBd($date_to),
                            )
                        ),
                    )

                )
            ),
            'fields' => array(
                'UserReassignment.*',
            ),
        ));
    }
}
