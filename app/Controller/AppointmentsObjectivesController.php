<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class AppointmentsObjectivesController extends AppController
{
	public $uses = array(
		'Appointment',
		'AppointmentObjective',
		'AppointmentObjectiveComment',
		'Contact',
		'ContactRegion',
		'CustomerActivity',
		'DeleteAll',
		'Distributor',
		'DistributorObjective',
		'Position',
		'SetDistributorObjective',
		'TradingGroup',
		'User'
	);

	/**
	 * Appointment objectives maintenance home page.
	 */
	public function maintenance()
	{
		$user = $this->Acceso->user();
		$roleId = $user['role_id'];
		$aagRegionId = $user['aag_region_id'];

		if (
			$this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
			$this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
			in_array($roleId, array(ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))
		) {
			$conditions = array('AppointmentObjective.aag_region_id' => $aagRegionId);
			$objectives = $this->custom_pagination(
				array(),
				$conditions,
				ConstantsPagination::SIZE_PAGE_SMALL,
				'AppointmentObjective',
				null,
				'PaginatorOrderCustom'
			);

			$this->set(array(
				'objectives' => $objectives,
			));
		} else {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}

	/**
	 * Appointment objective reporting.
	 */
	public function reporting()
	{
		$user = $this->Acceso->user();
		$aagRegionId = $user['aag_region_id'];
		$roleId = $user['role_id'];

		if (
			$roleId == ConstantsRoles::SUPER_ADMIN ||
			(
				$this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
				$this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
				!in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
			)
		) {
			$positions = $this->Position->getListPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::ADMIN));
			$conditions_bdm = array(
				'Contact.position_id' => $positions,
				'Contact.aag_region_id' => $aagRegionId
			);

			if (in_array(CakeSession::read('Auth.User.Contact.position_id'), array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID))) {
				$this->ContactRegion = ClassRegistry::init('ContactRegion');
				$this->Distributor = ClassRegistry::init('Distributor');
				$regions_tmp = $this->ContactRegion->findAllByContactId(CakeSession::read('Auth.User.contact_id'));
				$regions = Hash::extract($regions_tmp, '{n}.ContactRegion.region_id');
				$contacts_tmp = $this->ContactRegion->getListByRegion($regions);
				$contacts = Hash::extract($contacts_tmp, '{n}');
				$conditions_bdm['id'] = $contacts;
			} elseif (in_array(CakeSession::read('Auth.User.Contact.position_id'), array(ConstantsPositions::BDM_TG_ID, ConstantsPositions::BDM_AAG_ID, ConstantsPositions::BDM_GPC_ID))) {
				$conditions_bdm['id'] = array(CakeSession::read('Auth.User.contact_id'));
			}

			$contacts_bdm = $this->Contact->find('list', array(
				'conditions' => $conditions_bdm
			));

			$objectives = array();
			$search = $this->request->query;
			CakeSession::delete('Config.reporting_objectives');
			CakeSession::write('Config.reporting_objectives', $search);
			$this->request->data['Search'] = $search;

			$conditions = $this->AppointmentObjectiveComment->conditions($search);
			$conditions[] = array('User.aag_region_id' => $aagRegionId);

			$tmp = $this->AppointmentObjectiveComment->_query('search');

			$objectives = $this->custom_pagination(
				$tmp,
				$conditions,
				ConstantsPagination::SIZE_PAGE_SMALL,
				'AppointmentObjectiveComment',
				null,
				'PaginatorOrderCustom'
			);

			$array_distributor_name = ($search != null) ? $this->Distributor->getDistributorsNameByIdArray($search) : null;

			if (CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::BDM_GPC_ID) {
				$management_objectives = $this->AppointmentObjective->getManagementObjectives(ConstantsBooleans::YES);
			} elseif (in_array(CakeSession::read('Auth.User.Contact.position_id'), array(ConstantsPositions::BDM_TG_ID, ConstantsPositions::BDM_AAG_ID))) {
				$management_objectives = $this->AppointmentObjective->getManagementObjectives(ConstantsBooleans::NO);
			} else {
				$management_objectives = $this->AppointmentObjective->getManagementObjectives();
			}

			$this->set(array(
				'objectives' => $objectives,
				'management_objectives' => $management_objectives,
				'contacts_bdm' => $contacts_bdm,
				'distributors_array' => isset($search['distributor']) ? $search['distributor'] : null,
				'array_distributor_name' => isset($array_distributor_name) ? $array_distributor_name : array(),
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

	/**
	 * CRM create AppointmentObjectives.
	 */
	public function objectives()
	{
		$user = $this->Acceso->user();
		$aagRegionId = $user['aag_region_id'];
		$roleId = $user['role_id'];

		if (
			$this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
			$this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
			in_array($roleId, array(ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))
		) {
			$tg_objectives = $this->AppointmentObjective->getTgManagementObjectivesAagRegion($aagRegionId);
			$gpc_objectives = $this->AppointmentObjective->getGpcManagementObjectivessAagRegion($aagRegionId);
			$trading_groups = $this->TradingGroup->find('list', array(
				'conditions' => array(
					'TradingGroup.aag_region_id' => $aagRegionId
				),
				'order' => 'TradingGroup.name',
			));

			if (in_array(CakeSession::read('Auth.User.Contact.position_id'), array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID))) {
				$contact_tmp = $this->Contact->findById(CakeSession::read('Auth.User.contact_id'));
				$bdm =  array(
					$contact_tmp['Contact']['id'] => $contact_tmp['Contact']['full_name']
				);
			} else {
				$bdm = $this->Contact->getListByRoleIdAndRegionId(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG), $aagRegionId);
			}

			$rsm = $this->Contact->findListByPositionIdAndAagRegionId(array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID), $aagRegionId);

			$cancel_action = array(
				'url_cancel' => array(
					'controller' => 'clients',
					'action' => 'home_distributors',
				),
			);

			if (!$this->request->is('get')) {
				ini_set('memory_limit', '-1');
				set_time_limit(18000);

				$conditions = array();

				if (isset($this->request->data['filter_rsm']) && !empty($this->request->data['filter_rsm'])) {
					$conditions = array('DistributorContactBdm.contact_id' => $this->request->data['filter_rsm']);
				}
				if (isset($this->request->data['filter_bdm']) && !empty($this->request->data['filter_bdm'])) {
					$conditions = array('DistributorContactBdm.contact_id' => $this->request->data['filter_bdm']);
				}
				if (isset($this->request->data['filter_trading']) && !empty($this->request->data['filter_trading'])) {
					$conditions = array('Distributor.trading_group_id' => $this->request->data['filter_trading']);
				}
				if (isset($this->request->data['filter_customer']) && !empty($this->request->data['filter_customer'])) {
					$conditions = array('DistributorCustomerActivity.customer_activity_id' => $this->request->data['filter_customer']);
				}

				$distributors_array = null;
				if (isset($this->request->data['distributors_ids']) && !empty($this->request->data['distributors_ids'])) {
					$distributors_array = $this->request->data['distributors_ids'];
				} else {
					$distributors_array = $this->Distributor->getListByRegionCRMConditions($aagRegionId, $conditions);
				}

				if (sizeof($distributors_array) > ConstantsObjectives::LIMIT_CREATE) {
					foreach (array_unique($this->request->data['objectives_ids']) as $objective_id) {
						$data = array();
						$data['SetDistributorObjective']['distributor_array'] = json_encode($distributors_array);
						$data['SetDistributorObjective']['distributor_array_updated'] = json_encode($distributors_array);
						$data['SetDistributorObjective']['objective_id'] = $objective_id;
						$data['SetDistributorObjective']['from'] = $this->request->data['from'] ? Fecha::toFormatoBd($this->request->data['from']) : NULL;
						$data['SetDistributorObjective']['to'] = $this->request->data['to'] ? Fecha::toFormatoBd($this->request->data['to']) : NULL;
						$data['SetDistributorObjective']['creation_date'] = date('Y-m-d H:i:s');
						$data['SetDistributorObjective']['modification_date'] = date('Y-m-d H:i:s');
						$data['SetDistributorObjective']['is_set'] = ConstantsBooleans::NO;
						$data['SetDistributorObjective']['user_id'] = CakeSession::read('Auth.User.id');
						$data['SetDistributorObjective']['email_to'] = CakeSession::read('Auth.User.Contact.email');
						$this->SetDistributorObjective->add($data);
					}
					$this->SetDistributorObjective->setObjectives();
				} else {
					foreach ($distributors_array as $distributors_id) {
						foreach (array_unique($this->request->data['objectives_ids']) as $objective_id) {
							$this->DistributorObjective->add($distributors_id, $objective_id, $this->request->data['from'], $this->request->data['to']);
						}
					}
				}
			}

			$distributors = $this->Distributor->countListByRegionCRM($aagRegionId);
			$this->set(array(
				'tg_objectives' => $tg_objectives,
				'gpc_objectives' => $gpc_objectives,
				'distributors' => $distributors,
				'cancel_action' => $cancel_action,
				'trading_groups' => $trading_groups,
				'bdm' => $bdm,
				'rsm' => $rsm,
				'activities' => $this->CustomerActivity->search_list(),
			));
		} else {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}

	/**
	 * View CRM AppointmentsObjectives.
	 */
	public function view_objectives()
	{
		$user = $this->Acceso->user();
		$roleId = $user['role_id'];
		$aagRegionId = $user['aag_region_id'];

		if (
			$this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
			$this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
			in_array($roleId, array(ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))
		) {
			$search = $this->request->query;
			if ($search) {
				$this->request->data['Search'] = $search;

				$conditions = $this->DistributorObjective->conditions($search);
				$conditions[] = array('AppointmentObjective.aag_region_id' => $aagRegionId);

				$distributors_objectives = $this->custom_pagination(
					$this->DistributorObjective->_query('search'),
					$conditions,
					ConstantsPagination::SIZE_PAGE_SMALL,
					'DistributorObjective',
					null,
					'PaginatorOrderCustom'
				);

				$distributors_objectives_ids = $this->DistributorObjective->find(
					'all',
					Hash::merge(
						$this->DistributorObjective->_query('search_ids'),
						array(
							'conditions' => $conditions,
						)
					)
				);
				$distributors_objectives_ids = Hash::extract($distributors_objectives_ids, '{n}.DistributorObjective.id');
			} else {
				$conditions = array('AppointmentObjective.aag_region_id' => $aagRegionId);
				$distributors_objectives = $this->custom_pagination(
					$this->DistributorObjective->_query('search'),
					$conditions,
					ConstantsPagination::SIZE_PAGE_SMALL,
					'DistributorObjective',
					null,
					'PaginatorOrderCustom'
				);

				$distributors_objectives_ids = $this->DistributorObjective->find(
					'all',
					Hash::merge(
						$this->DistributorObjective->_query('search_ids'),
						array(
							'conditions' => $conditions,
						)
					)
				);
				$distributors_objectives_ids = Hash::extract($distributors_objectives_ids, '{n}.DistributorObjective.id');
			}

			$this->set(array(
				'distributors_objectives' => $distributors_objectives,
				'management_objectives' => $this->AppointmentObjective->getManagementObjectives(),
				'distributors_objectives_ids' => $distributors_objectives_ids,
			));
		} else {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}

	/**
	 * CRM create AppointmentObjective
	 */
	public function add()
	{
		$user = $this->Acceso->user();
		$roleId = $user['role_id'];
		$aagRegionId = $user['aag_region_id'];

		if (
			$this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
			$this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
			in_array($roleId, array(ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))
		) {
			$url_cancel = array(
				'url_cancel' => array(
					'controller' => 'appointments_objectives',
					'action' => 'maintenance',
				),
			);

			if (!$this->request->is('get')) {
				if ($this->AppointmentObjective->add($this->request->data)) {
					$this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
					$this->redirect(
						array(
							'controller' => 'appointments_objectives',
							'action' => 'edit',
							$this->AppointmentObjective->getLastInsertID()
						)
					);
				} else {
					$this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
				}
			}

			$this->set(array(
				'url_cancel' => $url_cancel,
				'aagRegionId' => $aagRegionId
			));
		} else {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}

	/**
	 * CRM edit AppointmentObjective.
	 */
	public function edit($objective_id)
	{
		$user = $this->Acceso->user();
		$roleId = $user['role_id'];

		$objective = $this->AppointmentObjective->findById($objective_id);

		if (
			$objective &&
			$this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
			$this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
			in_array($roleId, array(ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))
		) {
			$url_cancel = array(
				'url_cancel' => array(
					'controller' => 'appointments_objectives',
					'action' => 'maintenance',
				),
			);

			if ($this->request->is('get')) {
				$this->request->data = $objective;
			} else {
				if ($this->AppointmentObjective->edit($this->request->data)) {
					$this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
					$this->redirect($this->request->here);
				} else {
					$this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
				}
			}

			$this->set(array(
				'objective' => $objective,
				'url_cancel' => $url_cancel,
			));
		} else {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}

	/**
	 * AJAX delete AppointmentObjective.
	 */
	public function ajax_delete($objective_id)
	{
		$this->verify_ajax($this->request);

		$user = $this->Acceso->user();
		$roleId = $user['role_id'];

		if (
			$this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
			$this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
			in_array($roleId, array(ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))
		) {
			$this->autoRender = false;
			if ($this->AppointmentObjective->findById($objective_id)) {
				if ($this->DistributorObjective->findFirstByObjectiveId($objective_id)) {
					return 2;
				} else {
					return $this->AppointmentObjective->delete($objective_id);
				}
			} else {
				return false;
			}
		} else {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}

	/**
	 * AJAX delete DistributorObjective.
	 */
	public function ajax_delete_distributor($distributor_objective_id)
	{
		$this->verify_ajax($this->request);

		$user = $this->Acceso->user();
		$roleId = $user['role_id'];

		if (
			$this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
			$this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
			in_array($roleId, array(ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))
		) {
			$this->autoRender = false;
			if ($this->DistributorObjective->findById($distributor_objective_id)) {
				return $this->DistributorObjective->delete($distributor_objective_id);
			} else {
				return false;
			}
		} else {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}

	/**
	 * AJAX delete all DistributorObjective.
	 */
	public function delete_all_distributors_objectives()
	{
		$user = $this->Acceso->user();
		$roleId = $user['role_id'];

		if (
			$this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
			$this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
			in_array($roleId, array(ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))
		) {
			ini_set('memory_limit', '-1');
			set_time_limit(18000);

			$this->autoRender = false;

			$countDistributorToDelete = 0;

			// get first 2000 DistributorObjectives and delete all
			$dis_objectives_ids = $this->DistributorObjective->search_limit_list();
			if ($dis_objectives_ids) {
				foreach ($dis_objectives_ids as $dis_objective_id) {
					$this->DistributorObjective->delete_objective($dis_objective_id);
				}
			}

			// get last DistributorObjective ID and save the remaining ones in DeleteAll to do the deletion with the scheduled task
			$lastDistributorObjective = $this->DistributorObjective->getLastDistributorObjective();
			if ($lastDistributorObjective) {
				$firstDistributorObjective = $this->DistributorObjective->getFirstDistributorObjective();

				// see if a record exists in DeleteAll for distributor objectives with id to delete up to the last one retrieved
				$isAlredySet = $this->DeleteAll->findByTableAndIdToDelete(ConstantsDeleteAll::DISTRIBUTOR_OBJECTIVES, $lastDistributorObjective['DistributorObjective']['id']);

				// if it doesn't exists, a record is created
				if (!$isAlredySet) {
					$data = array();
					$data['DeleteAll']['id_to_delete'] = $lastDistributorObjective['DistributorObjective']['id'];
					$data['DeleteAll']['last_deleted_id'] = $firstDistributorObjective['DistributorObjective']['id'];
					$data['DeleteAll']['table'] = ConstantsDeleteAll::DISTRIBUTOR_OBJECTIVES;
					$data['DeleteAll']['creation_date'] = date('Y-m-d H:i:s');
					$data['DeleteAll']['modification_date'] = date('Y-m-d H:i:s');
					$data['DeleteAll']['is_completed'] = ConstantsBooleans::NO;
					$this->DeleteAll->add($data);

					$countDistributorToDelete = $this->DistributorObjective->getDistributorObjectivesCountBetweenIds($firstDistributorObjective['DistributorObjective']['id'], $lastDistributorObjective['DistributorObjective']['id']);
				}
			}

			return json_encode($countDistributorToDelete);
		} else {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}

	/**
	 * AppointmentObjectives excel generation.
	 */
	public function appointments_objectives_excel()
	{
		$user = $this->Acceso->user();
		$roleId = $user['role_id'];

		if (
			$roleId == ConstantsRoles::SUPER_ADMIN ||
			(
				$this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
				$this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
				!in_array($roleId, array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
			)
		) {
			set_time_limit(18000);
			ini_set("memory_limit", "1G");

			$search = CakeSession::read('Config.reporting_objectices');

			$conditions = $this->AppointmentObjectiveComment->conditions($search);
			$tmp = $this->AppointmentObjectiveComment->_query('search');

			$objectives = $this->AppointmentObjectiveComment->find(
				'all',
				Hash::merge(
					$tmp,
					array(
						'conditions' => $conditions,
					)
				)
			);

			$this->ajaxObjectivesExcelExport($objectives);
		} else {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}

	private function ajaxObjectivesExcelExport($objectives)
	{
		$this->Position = ClassRegistry::init('Position');
		$this->Contact = ClassRegistry::init('Contact');
		$positions = $this->Position->getListPositionByRole(array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM));
		$conditions_bdm = array(
			'Contact.position_id' => $positions
		);

		if (in_array(CakeSession::read('Auth.User.Contact.position_id'), array(ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID))) {
			$this->ContactRegion = ClassRegistry::init('ContactRegion');
			$this->Distributor = ClassRegistry::init('Distributor');
			$regions_tmp = $this->ContactRegion->findAllByContactId(CakeSession::read('Auth.User.contact_id'));
			$regions = Hash::extract($regions_tmp, '{n}.ContactRegion.region_id');
			$contacts_tmp = $this->ContactRegion->getListByRegion($regions);
			$contacts = Hash::extract($contacts_tmp, '{n}');
			$conditions_bdm['id'] = $contacts;
		}
		$contacts_bdm = $this->Contact->find('list', array(
			'conditions' => $conditions_bdm
		));

		$this->set(array(
			'objectives' => $objectives,
			'management_objectives' => $this->AppointmentObjective->getManagementObjectives(),
			'contacts_bdm' => $contacts_bdm,
			'objectives_status' => array(
				'0' => __t('Objective.Pending'),
				'1' => __t('General.Success'),
				'2' => __t('Objective.Failed'),
				'3' => __t('Objective.Requires_manager')
			),
		));

		set_time_limit(18000);
		ini_set('memory_limit', '-1');

		$this->render('/AppointmentsObjectives/Elements/export_excel_reporting');
		$this->response->type('xlsx');
		$this->layout = false;
	}
}
