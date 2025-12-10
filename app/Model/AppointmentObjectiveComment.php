<?php

class AppointmentObjectiveComment extends AppModel
{
	public $useTable = 'appointments_objective_comments';

	public $validate = array(
		'objective' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
	);

	public function conditions($fields)
	{
		$conditions = array();

		if (!empty($fields['distributor'])) {
			$conditions[] = $this->_conditionDistributor($fields['distributor']);
		}

		if (!empty($fields['objectives'])) {
			$conditions[] = $this->_conditionObjectives($fields['objectives']);
		}

		if (!empty($fields['status'])) {
			$conditions[] = $this->_conditionStatus($fields['status']);
		}

		if (!empty($fields['date_from'])) {
			$conditions[] = $this->_conditionDateFrom($fields['date_from']);
		}

		if (!empty($fields['date_to'])) {
			$conditions[] = $this->_conditionDateTo($fields['date_to']);
		}

		if (!empty($fields['bdm'])) {
			$conditions[] = $this->_conditionBdm($fields['bdm']);
		}

		return $conditions;
	}

	private function _conditionDistributor($distributor)
	{
		return array('Appointment.distributor_id' => $distributor);
	}

	private function _conditionObjectives($objectives)
	{
		return array('AppointmentObjectiveComment.objective_id' => $objectives);
	}

	private function _conditionStatus($status)
	{
		return array('AppointmentObjectiveComment.status' => $status);
	}

	private function _conditionBdm($bdm)
	{
		return array('User.contact_id' => $bdm);
	}

	private function _conditionDateFrom($from_date)
	{
		$from_date = Fecha::toFormatoBd($from_date);
		return array('Appointment.date >=' => $from_date);
	}

	private function _conditionDateTo($to_date)
	{
		$to_date = Fecha::toFormatoBd($to_date);
		return array('Appointment.date <=' => $to_date);
	}

	public function _query($index)
	{
		return $this->_queries[$index];
	}

	private $_queries = array(
		'search' => array(
			'joins' => array(
				array(
					'alias' => 'Appointment',
					'table' => 'appointments',
					'type' => 'INNER',
					'conditions' => array(
						'AppointmentObjectiveComment.appointment_id = Appointment.id',
					),
				),
				array(
					'alias' => 'Distributor',
					'table' => 'distributors',
					'type' => 'INNER',
					'conditions' => array(
						'Distributor.id = Appointment.distributor_id',
					),
				),
				array(
					'alias' => 'AppointmentObjective',
					'table' => 'appointments_objectives',
					'type' => 'INNER',
					'conditions' => array(
						'AppointmentObjective.id = AppointmentObjectiveComment.objective_id',
					),
				),
				array(
					'alias' => 'User',
					'table' => 'users',
					'type' => 'INNER',
					'conditions' => array(
						'Appointment.user_assigned_id = User.id',
					),
				),
			),
			'fields' => array(
				'Appointment.id',
				'Appointment.date',
				'AppointmentObjective.name',
				'AppointmentObjectiveComment.objective_id',
				'AppointmentObjectiveComment.status',
				'AppointmentObjectiveComment.comment',
				'Distributor.name',
				'User.name',
				'User.surname'
			),
			'order' => 'Appointment.date desc, Appointment.start_time desc',
		),
	);

	public function add_edit($objectives, $appointment_id, $distributor_id, $type)
	{


		foreach ($objectives as $objective_id => $objective) {

			if (isset($objective['objective_id']) && $objective['objective_id']) {
				if (!is_numeric($objective['objective_id'])) {
					$fields = array(
						'AppointmentObjectiveComment' => array(
							'appointment_id',
							'objective',
							'type',
							'status',
							'comment',
							'creation_date',
						)
					);

					$objective_tmp['AppointmentObjectiveComment']['appointment_id'] = $appointment_id;
					$objective_tmp['AppointmentObjectiveComment']['objective'] = $objective['objective_id'];
					$objective_tmp['AppointmentObjectiveComment']['type'] = $type;
					$objective_tmp['AppointmentObjectiveComment']['status'] = isset($objective['status']) ? $objective['status'] : ConstantsBooleans::NO;
					$objective_tmp['AppointmentObjectiveComment']['comment'] = isset($objective['comment']) ? $objective['comment'] : null;
					$objective_tmp['AppointmentObjectiveComment']['creation_date'] = date('Y/m/d H:i:s');

					$this->create();
					$objective_bd = $this->guardar($objective_tmp, $fields);
				} else {
					$objective_exist = $this->findByAppointmentIdAndObjectiveIdAndType($appointment_id, $objective_id, $type);
					if ($objective_exist) {
						$fields = array(
							'AppointmentObjectiveComment' => array(
								'id',
								'status',
								'comment',
							)
						);

						$objective_exist['AppointmentObjectiveComment']['status'] = isset($objective['status']) ? $objective['status'] : ConstantsBooleans::NO;
						$objective_exist['AppointmentObjectiveComment']['comment'] = isset($objective['comment']) ? $objective['comment'] : null;
						$this->guardar($objective_exist, $fields);
					} else {
						$objective_tmp = array();
						$fields = array(
							'AppointmentObjectiveComment' => array(
								'appointment_id',
								'objective_id',
								'type',
								'status',
								'comment',
								'creation_date',
							)
						);

						$objective_tmp['AppointmentObjectiveComment']['appointment_id'] = $appointment_id;
						$objective_tmp['AppointmentObjectiveComment']['objective_id'] = $objective_id;
						$objective_tmp['AppointmentObjectiveComment']['type'] = $type;
						$objective_tmp['AppointmentObjectiveComment']['status'] = isset($objective['status']) ? $objective['status'] : ConstantsBooleans::NO;
						$objective_tmp['AppointmentObjectiveComment']['comment'] = isset($objective['comment']) ? $objective['comment'] : null;
						$objective_tmp['AppointmentObjectiveComment']['creation_date'] = date('Y/m/d H:i:s');

						$this->DistributorObjective = ClassRegistry::init('DistributorObjective');
						$this->AppointmentObjective = ClassRegistry::init('AppointmentObjective');
						$objective_existe = $this->DistributorObjective->findByDistributorIdAndObjectiveId($distributor_id, $objective_id);
						$appointment_objective = $this->AppointmentObjective->findById($objective_id);
						$this->create();
						if (!$objective_existe['DistributorObjective']['from'] && !$objective_existe['DistributorObjective']['to']) {
							$objective_bd = $this->guardar($objective_tmp, $fields);
							if ($objective_bd) {
								$this->DistributorObjective->delete($objective_existe['DistributorObjective']['id']);
							}
						} else if ($appointment_objective['AppointmentObjective']['tg_personal'] || $appointment_objective['AppointmentObjective']['gpc_personal']) {
							$objective_bd = $this->guardar($objective_tmp, $fields);
						} else if ($objective_existe['DistributorObjective']['from'] && !$objective_existe['DistributorObjective']['to']) {
							if (strtotime($objective_existe['DistributorObjective']['from']) <= strtotime(date('Y-m-d'))) {
								$objective_bd = $this->guardar($objective_tmp, $fields);
							}
						} else if ($objective_existe['DistributorObjective']['from'] && $objective_existe['DistributorObjective']['to']) {
							if (
								strtotime($objective_existe['DistributorObjective']['from']) <= strtotime(date('Y-m-d')) &&
								strtotime($objective_existe['DistributorObjective']['to']) >= strtotime(date('Y-m-d'))
							) {
								$objective_bd = $this->guardar($objective_tmp, $fields);
							}
						}
						$this->commit();
					}
				}
			} else {
				$objective_exist = $this->findByAppointmentIdAndObjectiveAndType($appointment_id, $objective_id, $type);
				if ($objective_exist) {
					$fields = array(
						'AppointmentObjectiveComment' => array(
							'objective',
							'status',
							'comment',
						)
					);

					$objective_exist['AppointmentObjectiveComment']['status'] = isset($objective['status']) ? $objective['status'] : ConstantsBooleans::NO;
					$objective_exist['AppointmentObjectiveComment']['comment'] = isset($objective['comment']) ? $objective['comment'] : null;
					$this->guardar($objective_exist, $fields);
				}
			}
		}

		$this->commit();
		return true;
	}

	public function getObjectives($appointment_id)
	{
		$objectives_tmp = $this->find(
			'all',
			array(
				'conditions' => array(
					'AppointmentObjectiveComment.appointment_id' => $appointment_id
				),
				'fields' => array(
					'AppointmentObjectiveComment.*',
				),
			)
		);

		$objectives = null;
		foreach ($objectives_tmp as $objective_tmp) {
			$objectives[$objective_tmp['AppointmentObjectiveComment']['objective_id']] = array(
				'id' => $objective_tmp['AppointmentObjectiveComment']['id'],
				'appointment_id' => $objective_tmp['AppointmentObjectiveComment']['appointment_id'],
				'objective_id' => $objective_tmp['AppointmentObjectiveComment']['objective_id'],
				'objective' => $objective_tmp['AppointmentObjectiveComment']['objective'],
				'status' => $objective_tmp['AppointmentObjectiveComment']['status'],
				'comment' => $objective_tmp['AppointmentObjectiveComment']['comment'],
			);
		}

		return $objectives;
	}

	public function getObjectivesPersonal($appointment_id)
	{
		$objectives_tmp = $this->find(
			'all',
			array(
				'conditions' => array(
					'AppointmentObjectiveComment.appointment_id' => $appointment_id,
					'AppointmentObjectiveComment.type' => 1,
				),
				'fields' => array(
					'AppointmentObjectiveComment.*',
				),
			)
		);

		$objectives = null;
		foreach ($objectives_tmp as $objective_tmp) {
			$objectives[$objective_tmp['AppointmentObjectiveComment']['objective_id']] = array(
				'id' => $objective_tmp['AppointmentObjectiveComment']['id'],
				'appointment_id' => $objective_tmp['AppointmentObjectiveComment']['appointment_id'],
				'objective_id' => $objective_tmp['AppointmentObjectiveComment']['objective_id'],
				'objective' => $objective_tmp['AppointmentObjectiveComment']['objective'],
				'type' => $objective_tmp['AppointmentObjectiveComment']['type'],
				'status' => $objective_tmp['AppointmentObjectiveComment']['status'],
				'comment' => $objective_tmp['AppointmentObjectiveComment']['comment'],
			);
		}

		return $objectives;
	}

	public function getObjectivesManagement($appointment_id)
	{
		$objectives_tmp = $this->find(
			'all',
			array(
				'conditions' => array(
					'AppointmentObjectiveComment.appointment_id' => $appointment_id,
					'AppointmentObjectiveComment.type' => 2,
				),
				'fields' => array(
					'AppointmentObjectiveComment.*',
				),
			)
		);

		$objectives = null;
		foreach ($objectives_tmp as $objective_tmp) {
			$objectives[$objective_tmp['AppointmentObjectiveComment']['objective_id']] = array(
				'id' => $objective_tmp['AppointmentObjectiveComment']['id'],
				'appointment_id' => $objective_tmp['AppointmentObjectiveComment']['appointment_id'],
				'objective_id' => $objective_tmp['AppointmentObjectiveComment']['objective_id'],
				'objective' => $objective_tmp['AppointmentObjectiveComment']['objective'],
				'type' => $objective_tmp['AppointmentObjectiveComment']['type'],
				'status' => $objective_tmp['AppointmentObjectiveComment']['status'],
				'comment' => $objective_tmp['AppointmentObjectiveComment']['comment'],
			);
		}

		return $objectives;
	}

	public function getObjectivesToEmail($appointment_id)
	{

		$objectives_status = array(
			'0' => __t('Objective.Pending'),
			'1' => __t('General.Success'),
			'2' => __t('Objective.Failed'),
			'3' => __t('Objective.Requires_manager')
		);

		$objectives_tmp = $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'AppointmentObjective',
						'table' => 'appointments_objectives',
						'type' => 'INNER',
						'conditions' => 'AppointmentObjectiveComment.objective_id = AppointmentObjective.id',
					),
				),
				'conditions' => array(
					'AppointmentObjectiveComment.appointment_id' => $appointment_id
				),
				'fields' => array(
					'AppointmentObjectiveComment.*',
					'AppointmentObjective.*',
				),
			)
		);

		$objectives = null;
		foreach ($objectives_tmp as $objective_tmp) {
			$objectives[] = array(
				'id' => $objective_tmp['AppointmentObjectiveComment']['id'],
				'name' => $objective_tmp['AppointmentObjective']['name'],
				'type' => ($objective_tmp['AppointmentObjective']['tg_personal'] || $objective_tmp['AppointmentObjective']['tg_management']) ? 0 : 1,
				'personal' => $objective_tmp['AppointmentObjective']['tg_personal'] || $objective_tmp['AppointmentObjective']['gpc_personal'],
				'management' => $objective_tmp['AppointmentObjective']['tg_management'] || $objective_tmp['AppointmentObjective']['gpc_management'],
				'status' => $objectives_status[$objective_tmp['AppointmentObjectiveComment']['status']],
				'comment' => $objective_tmp['AppointmentObjectiveComment']['comment'],
				'objective_type' => $objective_tmp['AppointmentObjectiveComment']['type'],
			);
		}

		$objectives_tmp_null = $this->find(
			'all',
			array(
				'fields' => array(
					'AppointmentObjectiveComment.*',
				),
				'conditions' => array(
					'AppointmentObjectiveComment.appointment_id' => $appointment_id,
					'AppointmentObjectiveComment.type' => ConstantsTypeAgreement::PERSONAL,
					'AppointmentObjectiveComment.objective_id IS NULL',
				),

			)
		);

		foreach ($objectives_tmp_null as $objective_tmp) {
			$objectives[$objective_tmp['AppointmentObjectiveComment']['objective']] = array(
				'id' => $objective_tmp['AppointmentObjectiveComment']['id'],
				'name' => $objective_tmp['AppointmentObjectiveComment']['objective'],
				'type' => ConstantsTypeAgreement::PERSONAL,
				'personal' => ConstantsTypeAgreement::PERSONAL,
				'management' => null,
				'status' => $objectives_status[$objective_tmp['AppointmentObjectiveComment']['status']],
				'comment' => $objective_tmp['AppointmentObjectiveComment']['comment'],
				'objective_type' => $objective_tmp['AppointmentObjectiveComment']['type'],
			);
		}

		return $objectives;
	}


	public function getObjectivesPersonalType($appointment_id)
	{
		$objectives_tmp = $this->find(
			'all',
			array(
				'conditions' => array(
					'AppointmentObjectiveComment.appointment_id' => $appointment_id,
					'AppointmentObjectiveComment.type' => ConstantsTypeAgreement::PERSONAL,
				),
				'fields' => array(
					'AppointmentObjectiveComment.*',
				),
			)
		);

		$objectives = null;
		$condition_objective = null;
		foreach ($objectives_tmp as $objective_tmp) {
			if ($objective_tmp['AppointmentObjectiveComment']['objective_id'] == null) {
				$condition_objective = $objective_tmp['AppointmentObjectiveComment']['objective'];
			} else {
				$condition_objective = $objective_tmp['AppointmentObjectiveComment']['objective_id'];
			}
			$objectives[$condition_objective] = array(
				'id' => $objective_tmp['AppointmentObjectiveComment']['id'],
				'appointment_id' => $objective_tmp['AppointmentObjectiveComment']['appointment_id'],
				'objective_id' => $objective_tmp['AppointmentObjectiveComment']['objective_id'],
				'objective' => $objective_tmp['AppointmentObjectiveComment']['objective'],
				'type' => $objective_tmp['AppointmentObjectiveComment']['type'],
				'status' => $objective_tmp['AppointmentObjectiveComment']['status'],
				'comment' => $objective_tmp['AppointmentObjectiveComment']['comment'],
			);
		}

		return $objectives;
	}

	public function getObjectivesManagementType($appointment_id)
	{
		$objectives_tmp = $this->find(
			'all',
			array(
				'conditions' => array(
					'AppointmentObjectiveComment.appointment_id' => $appointment_id,
					'AppointmentObjectiveComment.type' => ConstantsTypeAgreement::MANAGEMENT,
				),
				'fields' => array(
					'AppointmentObjectiveComment.*',
				),
			)
		);

		$objectives = null;
		foreach ($objectives_tmp as $objective_tmp) {
			$objectives[$objective_tmp['AppointmentObjectiveComment']['objective_id']] = array(
				'id' => $objective_tmp['AppointmentObjectiveComment']['id'],
				'appointment_id' => $objective_tmp['AppointmentObjectiveComment']['appointment_id'],
				'objective_id' => $objective_tmp['AppointmentObjectiveComment']['objective_id'],
				'objective' => $objective_tmp['AppointmentObjectiveComment']['objective'],
				'type' => $objective_tmp['AppointmentObjectiveComment']['type'],
				'status' => $objective_tmp['AppointmentObjectiveComment']['status'],
				'comment' => $objective_tmp['AppointmentObjectiveComment']['comment'],
			);
		}

		return $objectives;
	}

	public function new_appointment_objective($appointment_objective)
	{
		$fields = array(
			'AppointmentObjectiveComment' => array(
				'appointment_id',
				'objective_id',
				'type',
				'status',
				'creation_date',
			)
		);

		$objective_tmp['AppointmentObjectiveComment']['appointment_id'] = $appointment_objective['AppointmentObjectiveComment']['appointment_id'];
		$objective_tmp['AppointmentObjectiveComment']['objective_id'] = $appointment_objective['AppointmentObjectiveComment']['objective_id'];
		$objective_tmp['AppointmentObjectiveComment']['type'] = $appointment_objective['AppointmentObjectiveComment']['type'];
		$objective_tmp['AppointmentObjectiveComment']['status'] = ConstantsBooleans::NO;
		$objective_tmp['AppointmentObjectiveComment']['creation_date'] = date('Y/m/d H:i:s');

		$this->create();

		$appointment_objective_bd = $this->guardar($objective_tmp, $fields);

		if (!$appointment_objective_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function new_appointment_objective_noId($appointment_objective)
	{
		$fields = array(
			'AppointmentObjectiveComment' => array(
				'appointment_id',
				'objective',
				'type',
				'status',
				'creation_date',
			)
		);

		$objective_tmp['AppointmentObjectiveComment']['appointment_id'] = $appointment_objective['AppointmentObjectiveComment']['appointment_id'];
		$objective_tmp['AppointmentObjectiveComment']['objective'] = $appointment_objective['AppointmentObjectiveComment']['objective_id'];
		$objective_tmp['AppointmentObjectiveComment']['type'] = $appointment_objective['AppointmentObjectiveComment']['type'];
		$objective_tmp['AppointmentObjectiveComment']['status'] = ConstantsBooleans::NO;
		$objective_tmp['AppointmentObjectiveComment']['creation_date'] = date('Y/m/d H:i:s');

		$this->create();

		$appointment_objective_bd = $this->guardar($objective_tmp, $fields);

		if (!$appointment_objective_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getObjectivesPersonalAssoc($appointment_id)
	{
		$objectives_tmp = $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'AppointmentObjective',
						'table' => 'appointments_objectives',
						'type' => 'INNER',
						'conditions' => 'AppointmentObjectiveComment.objective_id = AppointmentObjective.id',
					),
				),
				'conditions' => array(
					'AppointmentObjectiveComment.appointment_id' => $appointment_id,
					'AppointmentObjectiveComment.type' => ConstantsTypeAgreement::PERSONAL,
				),
				'fields' => array(
					'AppointmentObjectiveComment.*',
				),
			)
		);

		$objectives = null;
		foreach ($objectives_tmp as $objective_tmp) {
			$objectives[$objective_tmp['AppointmentObjectiveComment']['objective_id']] = array(
				'id' => $objective_tmp['AppointmentObjectiveComment']['id'],
				'appointment_id' => $objective_tmp['AppointmentObjectiveComment']['appointment_id'],
				'objective_id' => $objective_tmp['AppointmentObjectiveComment']['objective_id'],
				'objective' => $objective_tmp['AppointmentObjectiveComment']['objective'],
				'type' => $objective_tmp['AppointmentObjectiveComment']['type'],
				'status' => $objective_tmp['AppointmentObjectiveComment']['status'],
				'comment' => $objective_tmp['AppointmentObjectiveComment']['comment'],
			);
		}

		$objectives_tmp_null = $this->find(
			'all',
			array(
				'fields' => array(
					'AppointmentObjectiveComment.*',
				),
				'conditions' => array(
					'AppointmentObjectiveComment.appointment_id' => $appointment_id,
					'AppointmentObjectiveComment.type' => ConstantsTypeAgreement::PERSONAL,
					'AppointmentObjectiveComment.objective_id IS NULL',
				),

			)
		);

		foreach ($objectives_tmp_null as $objective_tmp) {
			$objectives[$objective_tmp['AppointmentObjectiveComment']['objective']] = array(
				'id' => $objective_tmp['AppointmentObjectiveComment']['id'],
				'appointment_id' => $objective_tmp['AppointmentObjectiveComment']['appointment_id'],
				'objective_id' => $objective_tmp['AppointmentObjectiveComment']['objective_id'],
				'objective' => $objective_tmp['AppointmentObjectiveComment']['objective'],
				'type' => $objective_tmp['AppointmentObjectiveComment']['type'],
				'status' => $objective_tmp['AppointmentObjectiveComment']['status'],
				'comment' => $objective_tmp['AppointmentObjectiveComment']['comment'],
			);
		}

		return $objectives;
	}

	public function checkDistributorsObjectivesFromToAlreadyCreated($distributorsObjectiveWhereAndTo)
	{

		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Appointment',
						'table' => 'appointments',
						'type' => 'INNER',
						'conditions' => 'Appointment.id = AppointmentObjectiveComment.appointment_id',
					),
				),
				'conditions' => array(
					'AppointmentObjectiveComment.objective_id' => $distributorsObjectiveWhereAndTo['DistributorObjective']['objective_id'],
					'AppointmentObjectiveComment.type' => ConstantsTypeAgreement::MANAGEMENT,
					'Appointment.distributor_id' => $distributorsObjectiveWhereAndTo['DistributorObjective']['distributor_id'],
					'AppointmentObjectiveComment.creation_date >=' => $distributorsObjectiveWhereAndTo['DistributorObjective']['from'],
					'AppointmentObjectiveComment.creation_date <=' => $distributorsObjectiveWhereAndTo['DistributorObjective']['to'],
				),
				'fields' => array(
					'AppointmentObjectiveComment.*',
				),
				'order' => 'AppointmentObjectiveComment.id ASC',
			)
		);
	}
}
