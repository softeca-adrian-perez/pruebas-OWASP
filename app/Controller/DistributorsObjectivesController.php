<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class DistributorsObjectivesController extends AppController
{
	public $uses = array(
		'AppointmentObjective',
		'AppointmentObjectiveComment',
		'Contact',
		'ContactRegion',
		'Distributor',
		'DistributorObjective',
		'Position',
		'User'

	);

	/**
	 * AJAX get DistributorObjectives.
	 */
	public function ajax_get_distributor_objectives()
	{
		$this->verify_ajax($this->request);

		$user = $this->Acceso->user();
		$aagRegionId = $user['aag_region_id'];
		$roleId = $user['role_id'];

		$distributorId = $this->request->data['distributor_id'];

		$distributor = $this->Distributor->findByIdAndAagRegionId($distributorId, $aagRegionId, 'id');

		if ($distributor && in_array($roleId, array(ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))) {
			$objectives = $this->DistributorObjective->getObjectivesByDistributor($distributorId, $user);

			$this->set(array(
				'management_objectives' => $objectives,
				'objectives_status' => array(
					'0' => __t('Objective.Pending'),
					'1' => __t('General.Success'),
					'2' => __t('Objective.Failed'),
					'3' => __t('Objective.Requires_manager')
				),
			));

			$this->layout = null;
			$this->render('/Appointments/Elements/management_objectives');
		} else {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}

	/**
	 * Not visited DistributorObjectives.
	 */
	public function not_visited()
	{
		$user = $this->Acceso->user();
		$roleId = $user['role_id'];
		$aagRegionId = $user['aag_region_id'];
		$userContactId = $user['contact_id'];

		if (in_array($roleId, array(ConstantsRoles::SUPER_ADMIN, ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))) {
			$positions = $this->Position->getListPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::ADMIN));
			$conditionsBdm = array(
				'Contact.position_id' => $positions,
				'Contact.aag_region_id' => $aagRegionId
			);

			if (in_array(CakeSession::read('Auth.User.Contact.position_id'), array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID))) {
				$regionsTmp = $this->ContactRegion->findAllByContactId($userContactId);
				$regions = Hash::extract($regionsTmp, '{n}.ContactRegion.region_id');
				$contactsTmp = $this->ContactRegion->getListByRegion($regions);
				$contacts = Hash::extract($contactsTmp, '{n}');
				$conditionsBdm['id'] = $contacts;
			} elseif (in_array(CakeSession::read('Auth.User.Contact.position_id'), array(ConstantsPositions::BDM_TG_ID, ConstantsPositions::BDM_AAG_ID, ConstantsPositions::BDM_GPC_ID))) {
				$conditionsBdm['id'] = array($userContactId);
			}

			$contacts_bdm = $this->Contact->find('list', array(
				'conditions' => $conditionsBdm
			));

			$objectives = array();
			$search = $this->request->query;
			CakeSession::delete('Config.reporting_objectives');

			CakeSession::write('Config.reporting_objectives', $search);
			$this->request->data['Search'] = $search;

			$distributorsObjectiveWhereAndTo = $this->DistributorObjective->getAllFromDistributorObjectiveWhereFromAndToExists();
			$arrayTakeOutWhereAndTo = [];
			foreach ($distributorsObjectiveWhereAndTo as $distributorsObjective) {
				$arrayAppointmentObjectiveComment = $this->AppointmentObjectiveComment->checkDistributorsObjectivesFromToAlreadyCreated($distributorsObjective);
				if ($arrayAppointmentObjectiveComment) {
					$arrayTakeOutWhereAndTo[] = $distributorsObjective['DistributorObjective']['id'];
				}
			}

			$conditions = $this->DistributorObjective->conditions($search);
			$conditions[] = array('User.aag_region_id' => $aagRegionId);

			$tmp = $this->DistributorObjective->_query('search');

			if ($arrayTakeOutWhereAndTo) {
				$conditions[] = array('DistributorObjective.id !=' => $arrayTakeOutWhereAndTo);
			}

			$objectives = $this->custom_pagination(
				$tmp,
				$conditions,
				ConstantsPagination::SIZE_PAGE_SMALL,
				'DistributorObjective',
				null,
				'PaginatorOrderCustom'
			);

			$arrayDistributorName = $search != null ? $this->Distributor->getDistributorsNameByIdArray($search) : null;

			if (CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::BDM_GPC_ID) {
				$managementObjectives = $this->AppointmentObjective->getManagementObjectives(ConstantsBooleans::YES);
			} elseif (in_array(CakeSession::read('Auth.User.Contact.position_id'), array(ConstantsPositions::BDM_TG_ID, ConstantsPositions::BDM_AAG_ID))) {
				$managementObjectives = $this->AppointmentObjective->getManagementObjectives(ConstantsBooleans::NO);
			} else {
				$managementObjectives = $this->AppointmentObjective->getManagementObjectives();
			}

			$this->set(array(
				'objectives' => $objectives,
				'management_objectives' => $managementObjectives,
				'contacts_bdm' => $contacts_bdm,
				'distributors_array' => isset($search['distributor']) ? $search['distributor'] : null,
				'array_distributor_name' => isset($arrayDistributorName) ? $arrayDistributorName : array(),
				'objectives_status' => array(
					'0' => __t('Objective.Pending'),
					'1' => __t('General.Success'),
					'2' => __t('Objective.Failed'),
					'3' => __t('Objective.Requires_manager')
				),
			));
		} else {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}
}
