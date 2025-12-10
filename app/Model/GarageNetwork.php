<?php
class GarageNetwork extends AppModel
{
	public $sendInfoCacheDataWebs = false;
	public $useTable = 'garages_networks';

	public $hasOne = array(
		'Garage',
	);

	public $hasMany = array(
		"Work",
		'GarageNetworkContact' => array(
			'className' => 'GarageNetworkContact',
			'foreignKey' => 'garage_network_id',
		),
		'VehicleType',
		'Service',
		'GarageService',
		'GarageNetworkService',
		'GarageNetworkWork',
		'GarageNetworkVehicleType'
	);

	public $belongsTo = array(
		'Contract',
		'Network'
	);

	public $validate = array(
		'network_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
				'message' => 'Validation.Mandatory_to_choose_a_network'
			),
		),
		'trading_group_id' => array(
			'rule' => 'checkBlankTradingGroup',
			'message' => 'Validation.Mandatory_to_choose_a_trading_group'
		),
		'status' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
				'message' => 'Validation.Mandatory_to_choose_a_status'
			),
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'contract_sent_date' => array(
			'rule' => 'date',
			'dmy',
			'message' => 'Validation.Format_date',
			'allowEmpty' => true
		),
		'contract_received_date' => array(
			'rule' => 'date',
			'dmy',
			'message' => 'Validation.Format_date',
			'allowEmpty' => true
		),
		'contract_start_date' => array(
			'rule' => 'date',
			'dmy',
			'message' => 'Validation.Format_date',
			'allowEmpty' => true
		),
		'date_on_hold' => array(
			'rule' => 'date',
			'dmy',
			'message' => 'Validation.Format_date',
			'allowEmpty' => true
		),
		'contract_end_date' => array(
			'rule' => 'date',
			'dmy',
			'message' => 'Validation.Format_date',
			'allowEmpty' => true
		),
		'leaving_date' => array(
			'rule' => 'date',
			'dmy',
			'message' => 'Validation.Format_date',
			'allowEmpty' => true
		),
		'booking_days_min_from' => array(
			'rule' => 'numeric',
			'message' => 'Validation.Mandatory_to_choose_a_number',
			'allowEmpty' => false
		),
		'booking_days_max_to' => array(
			'rule' => 'numeric',
			'message' => 'Validation.Mandatory_to_choose_a_number',
			'allowEmpty' => false
		),
		'reason_on_hold' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'reason_leaving' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'about' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'monday_open_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'monday_closed_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'monday_open_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'monday_closed_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'tuesday_open_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'tuesday_closed_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'tuesday_open_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'tuesday_closed_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'wednesday_open_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'wednesday_closed_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'wednesday_open_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'wednesday_closed_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'thursday_open_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'thursday_closed_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'thursday_open_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'thursday_closed_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'friday_open_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'friday_closed_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'friday_open_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'friday_closed_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'saturday_open_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'saturday_closed_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'saturday_open_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'saturday_closed_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'sunday_open_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'sunday_closed_1' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'sunday_open_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'sunday_closed_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'code' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'sap_code' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'location_id' => array(
			array(
				'rule' => 'checkBlankLocationId',
				'message' => 'Validation.Mandatory_to_fill_location_id',
			),
		),
		'kiyoh_api_key' => array(
			array(
				'rule' => 'checkBlankKiyohApiKey',
				'message' => 'Validation.Mandatory_to_kiyoh_api_key',
			),
		),
		'current_charge' => array(
			'notBlank' => array(
				'rule' => 'checkBlankCurrentCharge',
				'message' => 'Validation.Mandatory_to_choose_a_current_charge'
			),
		),
		'member_pays' => array(
			'rule' => 'checkBlankMemberPays',
			'message' => 'Validation.Mandatory_to_choose_a_member_pays',
		),
		'garage_pays' => array(
			'rule' => 'checkBlankGaragePays',
			'message' => 'Validation.Mandatory_to_choose_a_garage_pays',
		),
	);

	public function checkBlankCurrentCharge($check)
	{
		if (empty($check['current_charge']) && !empty($this->data[$this->alias]['contract_start_date']) && $this->data[$this->alias]['status'] == ConstantsNetworksStatus::LIVE) {
			return false;
		} else {
			return true;
		}
	}

	public function checkBlankMemberPays($check)
	{
		if (empty($check['member_pays']) && !empty($this->data[$this->alias]['contract_start_date']) && $this->data[$this->alias]['status'] == ConstantsNetworksStatus::LIVE) {
			return false;
		} else {
			return true;
		}
	}

	public function checkBlankGaragePays($check)
	{
		if (empty($check['garage_pays']) && !empty($this->data[$this->alias]['contract_start_date']) && $this->data[$this->alias]['status'] == ConstantsNetworksStatus::LIVE) {
			return false;
		} else {
			return true;
		}
	}

	public function checkBlankLocationId($check)
	{
		if (isset($check['location_id'])) {
			if (!empty($check['location_id'])) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	public function checkBlankKiyohApiKey($check)
	{
		if (isset($check['kiyoh_api_key'])) {
			if (!empty($check['kiyoh_api_key'])) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	public function checkBlankTradingGroup($check)
	{
		$this->TradingGroupNetwork = ClassRegistry::init('TradingGroupNetwork');
		if (empty($check['trading_group_id']) && !empty($this->data[$this->alias]['network_id'])) {
			$count_tg = $this->TradingGroupNetwork->countByNetwork($this->data[$this->alias]['network_id']);
			if ($count_tg > 0) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}

	public function conditions($fields)
	{
		$conditions = array();
		if (!empty($fields['Network_name'])) {
			$conditions[] = $this->_conditionNetworkName($fields['Network_name']);
		}
		if (!empty($fields['Garage_name'])) {
			$conditions[] = $this->_conditionGarageName($fields['Garage_name']);
		}

		return $conditions;
	}

	private function _conditionNetworkName($network_name)
	{
		return array('Network.id =' => $network_name);
	}

	private function _conditionGarageName($garage_name)
	{
		return array('Garage.id =' => $garage_name);
	}

	private $_queries = array(
		'home' => array(
			'conditions' => array(),
			'order' => array(
				'name' => 'asc'
			)
		)
	);

	public function _query($index)
	{
		return $this->_queries[$index];
	}

	public function addGarageNetwork($garage_network, $garage_id)
	{
		$this->Network = ClassRegistry::init('Network');

		$fields = array(
			'GarageNetwork' => array(
				'garage_id',
				'network_id',
				'supplier_id',
				'network_contract_type_id',
				'garage_number',
				'contract_sent_date',
				'contract_received_date',
				'contract_start_date',
				'contract_end_date',
				'status',
				'last',
				'dd_active',
				'annex_detail_id',
				'recommended',
				'creation_date',
				'credit',
				'location_id',
				'kiyoh_api_key',
				'default_credit',
				'current_charge',
				'member_pays',
				'garage_pays',
				'trading_group_id'
			)
		);

		$network = $this->Network->findById($garage_network['GarageNetwork']['network_id']);

		if ($garage_network['GarageNetwork']['leaving_date']) {
			array_push($fields['GarageNetwork'], "leaving_date");
			array_push($fields['GarageNetwork'], "reason_leaving_id");
			$garage_network['GarageNetwork']['reason_leaving_id'] = $garage_network['GarageNetwork']['reason_leaving_id'];
			$garage_network['GarageNetwork']['leaving_date'] = Fecha::toFormatoBd($garage_network['GarageNetwork']['leaving_date']);
			$garage_network['GarageNetwork']['date_on_hold'] = null;
			$garage_network['GarageNetwork']['reason_hold_id'] = null;
		} elseif ($garage_network['GarageNetwork']['date_on_hold']) {
			array_push($fields['GarageNetwork'], "date_on_hold");
			array_push($fields['GarageNetwork'], "reason_hold_id");
			$garage_network['GarageNetwork']['reason_hold_id'] = $garage_network['GarageNetwork']['reason_leaving_id'];
			$garage_network['GarageNetwork']['date_on_hold'] = Fecha::toFormatoBd($garage_network['GarageNetwork']['date_on_hold']);
			$garage_network['GarageNetwork']['leaving_date'] = null;
			$garage_network['GarageNetwork']['reason_leaving_id'] = null;
		} else {
			$garage_network['GarageNetwork']['date_on_hold'] = null;
			$garage_network['GarageNetwork']['reason_hold_id'] = null;
			$garage_network['GarageNetwork']['leaving_date'] = null;
			$garage_network['GarageNetwork']['reason_leaving_id'] = null;
		}
		unset($garage_network['GarageNetwork']['reason_leaving_id']);

		if (isset($garage_network['GarageNetwork']['trading_group_id']) && $garage_network['GarageNetwork']['trading_group_id'] != "") {
			array_push($fields['GarageNetwork'], 'trading_group_id');
		}

		$garage_network['GarageNetwork']['contract_sent_date'] = Fecha::toFormatoBd($garage_network['GarageNetwork']['contract_sent_date']);
		$garage_network['GarageNetwork']['contract_received_date'] = Fecha::toFormatoBd($garage_network['GarageNetwork']['contract_received_date']);

		$garage_network['GarageNetwork']['contract_start_date'] = Fecha::toFormatoBd($garage_network['GarageNetwork']['contract_start_date']);
		$garage_network['GarageNetwork']['contract_end_date'] = Fecha::toFormatoBd($garage_network['GarageNetwork']['contract_end_date']);

		$garage_network['GarageNetwork']['garage_number'] = null;

		$garage_network['GarageNetwork']['garage_id'] = $garage_id;
		$garage_network['GarageNetwork']['creation_date'] = date('Y-m-d H:i:s');

		if (!empty($garage_network['GarageNetwork']['contract_start_date']) && $garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
			$garage_network['GarageNetwork']['credit'] = $network['Network']['credit'];
			$garage_network['GarageNetwork']['default_credit'] = $network['Network']['credit'];
		}
		$this->create();

		$garage_network_bd = $this->guardar($garage_network, $fields);
		if (!$garage_network_bd) {
			return false;
		}

		$garageClass = ClassRegistry::init("Garage");
		$garageClass->edit_modification_date_garage($garage_id);
		$this->commit();

		//Set default values for garage network
		$networkId = $garage_network_bd['GarageNetwork']['network_id'];
		$garageId = $garage_network_bd['GarageNetwork']['garage_id'];
		if ($networkId == NETWORK_ID_AGN) {
			$this->setDefaultValuesAgn($garage_network_bd, $garageId);
		} elseif ($networkId == NETWORK_ID_GV) {
			$this->setDefaultValuesGv($garage_network_bd, $garageId);
		} elseif ($networkId == NETWORK_ID_GC) {
			$this->setDefaultValuesGc($garage_network_bd, $garageId);
		}

		//Then update the required data child networks related
		$this->updateParentNetworkOnChildStatusChange($garage_network_bd, $network);

		return $garage_network_bd;
	}

	public function editGarageNetwork($garage_network)
	{
		$this->Network = ClassRegistry::init('Network');

		$fields = array(
			'GarageNetwork' => array(
				'network_id',
				'network_contract_type_id',
				'supplier_id',
				'garage_number',
				'contract_sent_date',
				'contract_received_date',
				'contract_start_date',
				'contract_end_date',
				'date_on_hold',
				'reason_hold_id',
				'reason_leaving_id',
				'leaving_date',
				'status',
				'last',
				'dd_active',
				'annex_detail_id',
				'modification_date',
				'recommended',
				'credit',
				'location_id',
				'kiyoh_api_key',
				'default_credit',
				'current_charge',
				'member_pays',
				'garage_pays',
				'trading_group_id'
			)
		);

		$garage_network['GarageNetwork']['leaving_date'] = Fecha::toFormatoBd($garage_network['GarageNetwork']['leaving_date']);
		$garage_network['GarageNetwork']['date_on_hold'] = Fecha::toFormatoBd($garage_network['GarageNetwork']['date_on_hold']);

		if (!isset($garage_network['GarageNetwork']['dd_active'])) {
			$garage_network['GarageNetwork']['dd_active'] = false;
		}

		if (!isset($garage_network['GarageNetwork']['recommended'])) {
			$garage_network['GarageNetwork']['recommended'] = false;
		}

		if (!isset($garage_network['GarageNetwork']['location_id'])) {
			$garage_network['GarageNetwork']['location_id'] = null;
		}

		if (!isset($garage_network['GarageNetwork']['kiyoh_api_key'])) {
			$garage_network['GarageNetwork']['kiyoh_api_key'] = null;
		}

		$garage_network['GarageNetwork']['contract_sent_date'] = Fecha::toFormatoBd($garage_network['GarageNetwork']['contract_sent_date']);
		$garage_network['GarageNetwork']['contract_received_date'] = Fecha::toFormatoBd($garage_network['GarageNetwork']['contract_received_date']);
		$garage_network['GarageNetwork']['contract_start_date'] = Fecha::toFormatoBd($garage_network['GarageNetwork']['contract_start_date']);
		$garage_network['GarageNetwork']['contract_end_date'] = Fecha::toFormatoBd($garage_network['GarageNetwork']['contract_end_date']);
		$garage_network['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');
		$garage_network['GarageNetwork']['garage_number'] = null;

		$network = $this->Network->findById($garage_network['GarageNetwork']['network_id']);
		if (!empty($garage_network['GarageNetwork']['contract_start_date']) && $garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
			$garage_network['GarageNetwork']['credit'] = $network['Network']['credit'];
			$garage_network['GarageNetwork']['default_credit'] = $network['Network']['credit'];
		}

		$garage_network_bd = $this->guardar($garage_network, $fields);
		if (!$garage_network_bd) {
			return false;
		}

		$this->Garage = ClassRegistry::init("Garage");
		$this->Garage->edit_modification_date_garage($garage_network_bd['GarageNetwork']['garage_id']);

		$this->commit();

		$this->updateParentNetworkOnChildStatusChange($garage_network_bd, $network);

		return $garage_network_bd;
	}

	public function getDatasById($garage_distributor_id)
	{
		return $this->find(
			'first',
			array(
				'joins' => array(
					array(
						'alias' => 'Garage',
						'table' => 'garages',
						'type' => 'INNER',
						'conditions' => array(
							'GarageNetwork.garage_id = Garage.id',
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.id' => $garage_distributor_id,
				),
				'fields' => array(
					'GarageNetwork.*',
					'Garage.name'
				),
			)
		);
	}

	public function setGarageNetworkNotActive($old_network)
	{
		$fields = array(
			'GarageNetwork' => array(
				'network_id',
				'network_contract_type_id',
				'garage_number',
				'contract_sent_date',
				'contract_received_date',
				'contract_start_date',
				'contract_end_date',
				'reason_leaving_id',
				'status',
				'last',
				'modification_date'
			)
		);

		$old_network['GarageNetwork']['last'] = ConstantsBooleans::NO_ACTIVE;
		$old_network['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

		$old_garage_network_bd = $this->guardar($old_network, $fields);
		if (!$old_garage_network_bd) {
			return false;
		}
		return $old_garage_network_bd;
	}

	public function getAllByNetworkIdAndStatusAndLast($network_id, $aag_region_id, $status, $last)
	{
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Garage',
						'table' => 'garages',
						'type' => 'INNER',
						'conditions' => array(
							'Garage.id = GarageNetwork.garage_id'
						)
					)
				),
				'conditions' => array(
					'GarageNetwork.network_id' => $network_id,
					'GarageNetwork.status' => $status,
					'GarageNetwork.last' => $last,
					'Garage.aag_region_id' => $aag_region_id
				),
			)
		);
	}

	public function findNetworksExport($garage_id)
	{
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'INNER',
						'conditions' => array(
							'Network.id = GarageNetwork.network_id',
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.garage_id' => $garage_id,
				),
				'fields' => array(
					'Network.name',
					'GarageNetwork.network_contract_type_id',
					'GarageNetwork.contract_sent_date',
					'GarageNetwork.contract_received_date',
					'GarageNetwork.contract_start_date',
					'GarageNetwork.contract_end_date',
					'GarageNetwork.leaving_date',
					'GarageNetwork.status',
				),
			)
		);
	}

	public function findListNetworksByGarage($garage_id)
	{
		return $this->find(
			'list',
			array(
				'conditions' => array(
					'GarageNetwork.garage_id' => $garage_id,
					'GarageNetwork.last' => ConstantsBooleans::ACTIVE,
				),
				'fields' => array(
					'GarageNetwork.id',
					'GarageNetwork.network_id',
				),
			)
		);
	}

	public function findInternalByGarage($garage_id)
	{
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'INNER',
						'conditions' => array(
							'Network.id = GarageNetwork.network_id',
						),
					),
				),
				'conditions' => array(
					'Network.internal' => ConstantsBooleans::YES,
					'GarageNetwork.garage_id' => $garage_id,
				)
			)
		);
	}

	/**
	 * Gets all the internal GarageNetworks in the Garage and the AAG Region.
	 */
	public function findInternalByGarageAndAagRegionId($garageId, $aagRegionId)
	{
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'INNER',
						'conditions' => array(
							'Network.id = GarageNetwork.network_id',
						),
					),
				),
				'conditions' => array(
					'Network.internal' => ConstantsBooleans::YES,
					'Network.aag_region_id' => $aagRegionId,
					'GarageNetwork.garage_id' => $garageId,
				)
			)
		);
	}

	public function findExternalByGarage($garage_id)
	{
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'INNER',
						'conditions' => array(
							'Network.id = GarageNetwork.network_id',
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.garage_id' => $garage_id,
					'OR' => array(
						'Network.internal != ' . ConstantsBooleans::YES,
						'Network.internal IS NULL',
					)
				)
			)
		);
	}

	/**
	 * Gets all the external GarageNetworks in the Garage and the AAG Region.
	 */
	public function findExternalByGarageAndAagRegionId($garageId, $aagRegionId)
	{
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'INNER',
						'conditions' => array(
							'Network.id = GarageNetwork.network_id',
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.garage_id' => $garageId,
					'Network.aag_region_id' => $aagRegionId,
					'OR' => array(
						'Network.internal != ' . ConstantsBooleans::YES,
						'Network.internal IS NULL',
					)
				)
			)
		);
	}

	/**
	 * Gets all the GarageNetworks in the Garage and the AAG Region.
	 */
	public function findAllByGarageIdAndAagRegionId($garageId, $aagRegionId)
	{
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'INNER',
						'conditions' => array(
							'Network.id = GarageNetwork.network_id',
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.garage_id' => $garageId,
					'Network.aag_region_id' => $aagRegionId
				)
			)
		);
	}

	public function findNetworksByGarage($garage_id)
	{
		return $this->find(
			'list',
			array(
				'conditions' => array(
					'GarageNetwork.garage_id' => $garage_id,
				),
				'fields' => array(
					'GarageNetwork.network_id',
				),
			)
		);
	}

	public function getNetworkNameByIdGarage($garage_id)
	{
		return $this->find(
			'list',
			array(
				'joins' => array(
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'INNER',
						'conditions' => array(
							'Network.id = GarageNetwork.network_id',
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.garage_id' => $garage_id,
				),
				'fields' => array(
					'Network.id',
					'Network.name',
				),
			)
		);
	}

	public function findActiveNetworksByGarage($garage_id)
	{
		return $this->find(
			'list',
			array(
				'conditions' => array(
					'GarageNetwork.garage_id' => $garage_id,
					'GarageNetwork.status' => ConstantsNetworksStatus::LIVE
				),
				'fields' => array(
					'GarageNetwork.network_id'
				)
			)
		);
	}

	public function getStatusFromGarageNetworks()
	{
		return $this->find(
			'list',
			array(
				'fields' => array(
					'GarageNetwork.status',
				),
				'conditions' => array(
					'GarageNetwork.status !=' => null,
					'GarageNetwork.status !=' => ''
				),
				'group' => 'GarageNetwork.status'
			)
		);
	}

	public function findListByGarageIdAndContractStartDateAndNetworkId($garage_id, $date, $network_id)
	{
		return $this->find(
			'first',
			array(
				'conditions' => array(
					'GarageNetwork.garage_id' => $garage_id,
					'GarageNetwork.network_id' => $network_id,
					'GarageNetwork.contract_start_date' => $date
				),
				'fields' => array(
					'GarageNetwork.id'
				),
			)
		);
	}

	public function findByGarageAndNetwork($garage_id, $network_id)
	{
		return $this->find(
			'first',
			array(
				'conditions' => array(
					'GarageNetwork.garage_id' => $garage_id,
					'GarageNetwork.network_id' => $network_id,
				),
				'fields' => array(
					'GarageNetwork.*'
				),
				'order' =>
				'GarageNetwork.contract_start_date desc'
			)
		);
	}

	public function createGarageNetwork($garage_json, $garage_id, &$errors)
	{
		//The data comes from a Json left on the server
		foreach ($garage_json['Networks'] as $network) {

			$network_type = $this->_networkType($network['ContractType']);
			$trading_group = $this->tradingGroup($network['ContractType']);

			$garage_network_tmp = array(
				'GarageNetwork' => array(
					'garage_id' => $garage_id,
					'network_id' => $network_type,
					'trading_group_id' => $trading_group,
					'contract_sent_date' => ($network['ContractSentDate']) ? $this->extractDate($network['ContractSentDate']) : null,
					'contract_received_date' => ($network['ContractReceivedDate']) ? $this->extractDate($network['ContractReceivedDate']) : null,
					'contract_start_date' => ($network['StartDate']) ? $this->extractDate($network['StartDate']) : null,
					'contract_end_date' => ($network['CancellationDate']) ? $this->extractDate($network['CancellationDate']) : null,
					'reason_leaving_id' => null,
					'status' => $this->statusNetwork($network),
					'creation_date' => date('Y-m-d H:i:s')
				)
			);

			$garage_network = $this->findByGarageIdAndNetworkId($garage_id, $network_type);
			if ($garage_network) {
				if ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
					if ($garage_network_tmp['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
						$last = ConstantsBooleans::YES;
					} else {
						$last = ConstantsBooleans::NO;
					}
				} else {
					$last = ConstantsBooleans::NO;
				}
			} else {
				$last = ConstantsBooleans::YES;
			}

			$garage_network_tmp['GarageNetwork']['last'] = $last;

			if ($network['ReasonForLeaving']) {
				$this->LeavingReasonType = ClassRegistry::init('LeavingReasonType');
				$leaving_reason_type_exits = $this->LeavingReasonType->findByNameEn($network['ReasonForLeaving']);
				if (!$leaving_reason_type_exits) {
					$leaving_reason_type_tmp = array(
						'LeavingReasonType' => array(
							'name_en' => $network['ReasonForLeaving'],
							'name_fr' => $network['ReasonForLeaving'],
							'name_de' => $network['ReasonForLeaving'],
							'name_lc' => 'Bd.Leaving_reason_types',
						)
					);

					$this->LeavingReasonType->create();
					$leaving_reason_type_exits = $this->LeavingReasonType->save($leaving_reason_type_tmp);
					if (!$leaving_reason_type_exits) {
						CakeLog::write('updates', 'Couldn\'t create comment about the garage' . PHP_EOL);
						$errors['Couldn\'t create comment about the garage'] = translateDataErrors($this->LeavingReasonType->validationErrors);
						return true;
					}

					$this->validator()->remove('contract_sent_date');
					$this->validator()->remove('current_charge');
					$this->validator()->remove('member_pays');
					$this->validator()->remove('garage_pays');
					$this->create();

					if (!$this->save($garage_network_tmp)) {
						debug($this->validationErrors);
						CakeLog::write('updates', 'Network link could not be created' . PHP_EOL);
						$errors['Network link could not be created'] = translateDataErrors($this->validationErrors);
					}
					return true;
				} else {
					$garage_network_tmp['GarageNetwork']['reason_leaving_id'] = $leaving_reason_type_exits['LeavingReasonType']['id'];
					$this->validator()->remove('contract_sent_date');
					$this->validator()->remove('current_charge');
					$this->validator()->remove('member_pays');
					$this->validator()->remove('garage_pays');
					$this->create();

					if (!$this->save($garage_network_tmp)) {
						debug($this->validationErrors);
						CakeLog::write('updates', 'Network link could not be created' . PHP_EOL);
						$errors['Network link could not be created'] = translateDataErrors($this->validationErrors);
					}

					return true;
				}
			}

			$this->validator()->remove('contract_sent_date');
			$this->validator()->remove('current_charge');
			$this->validator()->remove('member_pays');
			$this->validator()->remove('garage_pays');
			$this->create();

			if (!$this->save($garage_network_tmp)) {
				debug($this->validationErrors);
				CakeLog::write('updates', 'Network link could not be created' . PHP_EOL);
				$errors['Network link could not be created'] = translateDataErrors($this->validationErrors);
			}
		}

		/**
		 * Buscamos si viene algo en el campo "fleetAgreement"
		 * Si tiene algo se busca si hay alguna red que se llame de esa manera
		 * si lo hay no tiene relacion anterior se crea, si ya tiene relacion no se hace nada
		 */
		if (isset($garage_json['Garage']['fleetAgreement']) && $garage_json['Garage']['fleetAgreement']) {
			$this->Network = ClassRegistry::init('Network');
			$network_exist = $this->Network->findByName($garage_json['Garage']['fleetAgreement']);
			if ($network_exist) {
				$garage_network = $this->findByGarageIdAndNetworkId($garage_id, $network_exist['Network']['id']);
				if (!$garage_network) {
					$garage_network_tmp = array();
					$garage_network_tmp = array(
						'GarageNetwork' => array(
							'garage_id' => $garage_id,
							'network_id' => $network_exist['Network']['id'],
							'trading_group_id' => null,
							'contract_sent_date' => null,
							'contract_received_date' => null,
							'contract_start_date' => null,
							'contract_end_date' => null,
							'reason_leaving_id' => null,
							'status' => ConstantsNetworksStatus::LIVE,
							'modification_date' => date('Y-m-d'),
						)
					);

					$this->validator()->remove('contract_sent_date');
					$this->validator()->remove('current_charge');
					$this->validator()->remove('member_pays');
					$this->validator()->remove('garage_pays');
					$this->validator()->remove('trading_group_id');
					$this->create();

					if (!$this->save($garage_network_tmp)) {
						debug($this->validationErrors);
						CakeLog::write('updates', 'Network link could not be created' . PHP_EOL);
						$errors['Network link could not be created'] = translateDataErrors($this->validationErrors);
					}
				}
			}
		}
		return true;
	}

	public function createCustomerNetwork($customer_json, $customer_id)
	{
		//The data comes from a Json left on the germany server
		$garage_network_tmp = array(
			'GarageNetwork' => array(
				'garage_id' => $customer_id,
				'network_id' => 1,
				'trading_group_id' => 2,
				'contract_sent_date' => null,
				'contract_received_date' => null,
				'contract_start_date' => date('Y-m-d H:i:s'),
				'contract_end_date' => null,
				'reason_leaving_id' => null,
				'last' => ConstantsBooleans::YES,
				'status' => ConstantsNetworksStatus::LIVE
			)
		);

		$this->validator()->remove('contract_start_date');
		$this->validator()->remove('contract_sent_date');
		$this->validator()->remove('current_charge');
		$this->validator()->remove('member_pays');
		$this->validator()->remove('garage_pays');
		$this->create();
		if (!$this->save($garage_network_tmp)) {
			debug($this->validationErrors);
			CakeLog::write('updates-germany', 'Network link could not be created' . PHP_EOL);
		}
		return true;
	}

	public function createRepNetwork($garage_json, $garage_id, $garage_creation_date)
	{
		foreach ($garage_json['reseau'] as $network) {

			if (
				$network['nom'] == 'Top Garage' ||
				$network['nom'] == 'Etape Auto' ||
				$network['nom'] == 'Garage Premier' ||
				$network['nom'] == 'Etape Auto Relais' ||
				$network['nom'] == 'Mon Garage' ||
				$network['nom'] == 'Precisium Garage' ||
				$network['nom'] == 'Precisium Relais' ||
				$network['nom'] == 'Garage & CO' ||
				$network['nom'] == 'Pieces Auto' ||
				$network['nom'] == 'Top Carrosserie' ||
				$network['nom'] == 'Mon Carrossier' ||
				$network['nom'] == 'Mon Carrosier' ||
				$network['nom'] == 'Precisium Carrosserie' ||
				$network['nom'] == 'Top Truck'
			) {
				$network_model = ClassRegistry::init('Network');
				$network_current = $network_model->findByName($network['nom']);
				$network_id = $network_current['Network']['id'];

				if ($garage_json['groupauto'] == true) {
					$trading_group = ConstantsTradingGroupsNames::GROUPAUTO_FRANCE;
				} elseif ($garage_json['partners'] == true) {
					$trading_group = ConstantsTradingGroupsNames::PARTNERS;
				} elseif ($garage_json['precisium'] == true) {
					$trading_group = ConstantsTradingGroupsNames::PRECISIUM;
				} elseif ($garage_json['gefa'] == true) {
					$trading_group = ConstantsTradingGroupsNames::GEF_AUTO;
				}

				$garage_network_tmp = array(
					'GarageNetwork' => array(
						'garage_id' => $garage_id,
						'network_id' => $network_id,
						'trading_group_id' => $trading_group,
						'garage_number' => (isset($network['code'])) ? $network['code'] : null,
						'contract_sent_date' => null,
						'contract_received_date' => null,
						'contract_start_date' => ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : null,
						'contract_end_date' => ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : null,
						'reason_leaving' => null,
						'status' => ($network['actif'] == true) ? ConstantsNetworksStatus::LIVE : ConstantsNetworksStatus::UNSUBSCRIBE,
					)
				);

				$garage_network = $this->findByGarageIdAndNetworkId($garage_id, $network_id);
				if (!empty($garage_network) && $garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
					if ($garage_network_tmp['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
						$last = ConstantsBooleans::YES;
					} else {
						$last = ConstantsBooleans::NO;
					}
				} else {
					$last = ConstantsBooleans::YES;
				}
				$garage_network_tmp['GarageNetwork']['last'] = $last;

				$this->validator()->remove('contract_sent_date');
				$this->validator()->remove('current_charge');
				$this->validator()->remove('member_pays');
				$this->validator()->remove('garage_pays');
				$this->create();
				$garagenetwork_tmp = $this->save($garage_network_tmp);
				if (!$garagenetwork_tmp) {
					CakeLog::write('updates-france', 'The garage network could not be created.' . PHP_EOL);
				}

				if (($network['date_signature'] != '0000-00-00') && (strtotime($garage_creation_date) > strtotime($network['date_signature']))) {
					$garage_creation_date = $network['date_signature'];
				}
			}

			if (
				$network['nom'] == 'Top Garage' ||
				$network['nom'] == 'Etape Auto' ||
				$network['nom'] == 'Garage Premier' ||
				$network['nom'] == 'Etape Auto Relais' ||
				$network['nom'] == 'Mon Garage' ||
				$network['nom'] == 'Precisium Garage' ||
				$network['nom'] == 'Precisium Relais' ||
				$network['nom'] == 'Garage & CO' ||
				$network['nom'] == 'Pieces Auto'

			) {
				$garage_activity_model = ClassRegistry::init('GarageCustomerActivity');
				$garage_activity = $garage_activity_model->findByGarageIdAndCustomerActivityId($garage_id, 1); // VL

				if (!$garage_activity) {

					$garage_activity_bd = array(
						'GarageCustomerActivity' => array(
							'garage_id' => $garage_id,
							'customer_activity_id' => 1,
							'order' => 1
						)
					);

					$garage_activity_model->create();
					$garage_activity_tmp = $garage_activity_model->save($garage_activity_bd);
					if (!$garage_activity_tmp) {
						CakeLog::write('updates-france', 'The garage activity could not be created.' . PHP_EOL);
					}
				}
			} elseif (
				$network['nom'] == 'Top Carrosserie' ||
				$network['nom'] == 'Mon Carrossier' ||
				$network['nom'] == 'Mon Carrosier' ||
				$network['nom'] == 'Precisium Carrosserie'
			) {
				$garage_activity_model = ClassRegistry::init('GarageCustomerActivity');
				$garage_activity = $garage_activity_model->findByGarageIdAndOrder($garage_id, 1); //Order
				if (!$garage_activity) {

					$garage_activity_bd = array(
						'GarageCustomerActivity' => array(
							'garage_id' => $garage_id,
							'customer_activity_id' => 5,
							'order' => 1
						)
					);
					$garage_activity_model->create();
					$garage_activity_tmp = $garage_activity_model->save($garage_activity_bd);
					if (!$garage_activity_tmp) {
						CakeLog::write('updates-france', 'The garage activity could not be created.' . PHP_EOL);
					}
				} else {
					$garage_activity = $garage_activity_model->findLastOrderByGarageId($garage_id); //Order

					$garage_activity_bd = array(
						'GarageCustomerActivity' => array(
							'garage_id' => $garage_id,
							'customer_activity_id' => 5,
							'order' => $garage_activity[0][0]['max_tmp'] + 1
						)
					);
					$garage_activity_model->create();
					$garage_activity_tmp = $garage_activity_model->save($garage_activity_bd);
					if (!$garage_activity_tmp) {
						CakeLog::write('updates-france', 'The garage activity could not be created.' . PHP_EOL);
					}
				}
			} elseif (
				$network['nom'] == 'Top Truck'
			) {
				$garage_activity_model = ClassRegistry::init('GarageCustomerActivity');
				$garage_activity = $garage_activity_model->findByGarageIdAndOrder($garage_id, 1); //Order
				if (!$garage_activity) {

					$garage_activity_bd = array(
						'GarageCustomerActivity' => array(
							'garage_id' => $garage_id,
							'customer_activity_id' => 2,
							'order' => 1
						)
					);
					$garage_activity_model->create();
					$garage_activity_tmp = $garage_activity_model->save($garage_activity_bd);
					if (!$garage_activity_tmp) {
						CakeLog::write('updates-france', 'The garage activity could not be created.' . PHP_EOL);
					}
				} else {
					$garage_activity = $garage_activity_model->findLastOrderByGarageId($garage_id); //Order

					$garage_activity_bd = array(
						'GarageCustomerActivity' => array(
							'garage_id' => $garage_id,
							'customer_activity_id' => 2,
							'order' => $garage_activity['GarageCustomerActivity'][0][0]['max_tmp'] + 1
						)
					);
					$garage_activity_model->create();
					$garage_activity_tmp = $garage_activity_model->save($garage_activity_bd);
					if (!$garage_activity_tmp) {
						CakeLog::write('updates-france', 'The garage activity could not be created.' . PHP_EOL);
					}
				}
			} elseif (
				$network['nom'] == 'Top Revision'
			) {
				$service_model = ClassRegistry::init('Service');
				$garage_service_model = ClassRegistry::init('GarageService');

				$service_tmp = $service_model->findByNameFr('top revision');

				if (empty($service_tmp)) {
					$service_bd = array(
						'Service' => array(
							'name_en' => 'top revision',
							'name_fr' => 'top revision',
							'name_de' => 'top revision',
							'url' => 'no-image.png',
						)
					);

					$service_model->create();
					$service_tmp = $service_model->save($service_bd);
					if (!$service_tmp) {
						CakeLog::write('updates-france', 'The service could not be created.' . PHP_EOL);
					}
				}

				$garage_service_tmp = array(
					'GarageService' => array(
						'garage_id' => $garage_id,
						'service_id' => $service_tmp['Service']['id'],
					)
				);

				$garage_service_model->create();
				$garage_service_model->save($garage_service_tmp);
				if (!$garage_service_model) {
					CakeLog::write('updates-france', 'The garage service could not be created.' . PHP_EOL);
				}
			}
		}

		$this->commit();
		return $garage_creation_date;
	}

	public function updateRepNetwork($garage_json, $garage_exist)
	{
		$garage_creation_date = $garage_exist['Garage']['creation_date'];
		if (isset($garage_json['reseau'])) {
			foreach ($garage_json['reseau'] as $network) {

				if (
					$network['nom'] == 'Top Garage' ||
					$network['nom'] == 'Etape Auto' ||
					$network['nom'] == 'Garage Premier' ||
					$network['nom'] == 'Etape Auto Relais' ||
					$network['nom'] == 'Mon Garage' ||
					$network['nom'] == 'Precisium Garage' ||
					$network['nom'] == 'Precisium Relais' ||
					$network['nom'] == 'Garage & CO' ||
					$network['nom'] == 'Pieces Auto' ||
					$network['nom'] == 'Top Carrosserie' ||
					$network['nom'] == 'Mon Carrossier' ||
					$network['nom'] == 'Mon Carrosier' ||
					$network['nom'] == 'Precisium Carrosserie' ||
					$network['nom'] == 'Top Truck'
				) {
					$network_model = ClassRegistry::init('Network');
					$network_current = $network_model->findByName($network['nom']);
					$network_id = $network_current['Network']['id'];

					if ($garage_json['groupauto'] == true) {
						$trading_group = ConstantsTradingGroupsNames::GROUPAUTO_FRANCE;
					} elseif ($garage_json['partners'] == true) {
						$trading_group = ConstantsTradingGroupsNames::PARTNERS;
					} elseif ($garage_json['precisium'] == true) {
						$trading_group = ConstantsTradingGroupsNames::PRECISIUM;
					} elseif ($garage_json['gefa'] == true) {
						$trading_group = ConstantsTradingGroupsNames::GEF_AUTO;
					} else {
						if (
							$network['nom'] == 'Top Garage' ||
							$network['nom'] == 'Garage Premier' ||
							$network['nom'] == 'Top Carrosserie' ||
							$network['nom'] == 'Top Truck'
						) {
							$trading_group = ConstantsTradingGroupsNames::GROUPAUTO_FRANCE;
						} elseif (
							$network['nom'] == 'Mon Garage' ||
							$network['nom'] == 'Mon Carrossier' ||
							$network['nom'] == 'Mon Carrosier'
						) {
							$trading_group = ConstantsTradingGroupsNames::PARTNERS;
						} elseif (
							$network['nom'] == 'Precisium Carrosserie' ||
							$network['nom'] == 'Precisium Garage' ||
							$network['nom'] == 'Precisium Relais'
						) {
							$trading_group = ConstantsTradingGroupsNames::PRECISIUM;
						} elseif (
							$network['nom'] == 'Etape Auto' ||
							$network['nom'] == 'Etape Auto Relais'
						) {
							$trading_group = ConstantsTradingGroupsNames::GEF_AUTO;
						}
					}

					$garage_network_exist = $this->findByGarageIdAndNetworkIdAndTradingGroupId($garage_exist['Garage']['id'], $network_id, $trading_group);
					if ($garage_network_exist) {
						$garage_network_tmp = array(
							'GarageNetwork' => array(
								'id' => $garage_network_exist['GarageNetwork']['id'],
								'garage_id' => $garage_exist['Garage']['id'],
								'network_id' => $network_id,
								'trading_group_id' => $trading_group,
								'garage_number' => isset($network['code']) ? $network['code'] : $garage_network_exist['GarageNetwork']['garage_number'],
								'contract_sent_date' => null,
								'contract_received_date' => null,
								'contract_start_date' => isset($network['date_signature']) && ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : $garage_network_exist['GarageNetwork']['contract_start_date'],
								'contract_end_date' => isset($network['date_resiliation']) && ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : $garage_network_exist['GarageNetwork']['contract_end_date'],
								'reason_leaving' => null,
								'status' => $network['actif'] == true ? ConstantsNetworksStatus::LIVE : ConstantsNetworksStatus::UNSUBSCRIBE,
							)
						);

						if ($garage_network_tmp['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
							$last = ConstantsBooleans::YES;
						} else {
							$last = ConstantsBooleans::NO;
						}

						$garage_network_tmp['GarageNetwork']['last'] = $last;

						$this->validator()->remove('contract_sent_date');
						$this->validator()->remove('current_charge');
						$this->validator()->remove('member_pays');
						$this->validator()->remove('garage_pays');
						$garagenetwork_tmp = $this->save($garage_network_tmp);
						if (!$garagenetwork_tmp) {
							CakeLog::write('updates-france', 'The garage network could not be updated.' . PHP_EOL);
						}

						if (isset($network['date_signature']) && $network['date_signature'] != '0000-00-00' && strtotime($garage_creation_date) > strtotime($network['date_signature'])) {
							$garage_creation_date = $network['date_signature'];
						}
					} else {
						$garage_network_tmp = array(
							'GarageNetwork' => array(
								'garage_id' => $garage_exist['Garage']['id'],
								'network_id' => $network_id,
								'trading_group_id' => $trading_group,
								'garage_number' => (isset($network['code'])) ? $network['code'] : null,
								'contract_sent_date' => null,
								'contract_received_date' => null,
								'contract_start_date' => ($network['date_signature'] != '0000-00-00') ? $network['date_signature'] : null,
								'contract_end_date' => ($network['date_resiliation'] != '0000-00-00') ? $network['date_resiliation'] : null,
								'reason_leaving' => null,
								'status' => ($network['actif'] == true) ? ConstantsNetworksStatus::LIVE : ConstantsNetworksStatus::UNSUBSCRIBE,
							)
						);

						$garage_network = $this->findByGarageIdAndNetworkId($garage_exist['Garage']['id'], $network_id);
						if (!empty($garage_network) && $garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
							if (($garage_network_tmp['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE)) {
								$last = ConstantsBooleans::YES;
							} else {
								$last = ConstantsBooleans::NO;
							}
						} else {
							$last = ConstantsBooleans::YES;
						}
						$garage_network_tmp['GarageNetwork']['last'] = $last;

						$this->validator()->remove('contract_sent_date');
						$this->validator()->remove('current_charge');
						$this->validator()->remove('member_pays');
						$this->validator()->remove('garage_pays');
						$this->create();
						$garagenetwork_tmp = $this->save($garage_network_tmp);
						if (!$garagenetwork_tmp) {
							CakeLog::write('updates-france', 'The garage network could not be created.' . PHP_EOL);
						}

						if (($network['date_signature'] != '0000-00-00') && (strtotime($garage_creation_date) > strtotime($network['date_signature']))) {
							$garage_creation_date = $network['date_signature'];
						}
					}
				}

				if (
					$network['nom'] == 'Top Garage' ||
					$network['nom'] == 'Etape Auto' ||
					$network['nom'] == 'Garage Premier' ||
					$network['nom'] == 'Etape Auto Relais' ||
					$network['nom'] == 'Mon Garage' ||
					$network['nom'] == 'Precisium Garage' ||
					$network['nom'] == 'Precisium Relais' ||
					$network['nom'] == 'Garage & CO' ||
					$network['nom'] == 'Pieces Auto'
				) {
					$garage_activity_model = ClassRegistry::init('GarageCustomerActivity');
					$garage_activity = $garage_activity_model->findByGarageIdAndCustomerActivityId($garage_exist['Garage']['id'], 1); // VL

					if (!$garage_activity) {

						$garage_activity_bd = array(
							'GarageCustomerActivity' => array(
								'garage_id' => $garage_exist['Garage']['id'],
								'customer_activity_id' => 1,
								'order' => 1
							)
						);

						$garage_activity_model->create();
						$garage_activity_tmp = $garage_activity_model->save($garage_activity_bd);
						if (!$garage_activity_tmp) {
							CakeLog::write('updates-france', 'The garage activity could not be created.' . PHP_EOL);
						}
					}
				} elseif (
					$network['nom'] == 'Top Carrosserie' ||
					$network['nom'] == 'Mon Carrossier' ||
					$network['nom'] == 'Mon Carrosier' ||
					$network['nom'] == 'Precisium Carrosserie'
				) {
					$garage_activity_model = ClassRegistry::init('GarageCustomerActivity');
					$garage_customer_activity_exist = $garage_activity_model->findByGarageIdAndCustomerActivityId($garage_exist['Garage']['id'], 2);
					if (!$garage_customer_activity_exist) {
						$garage_activity = $garage_activity_model->findByGarageIdAndOrder($garage_exist['Garage']['id'], 1); //Order
						if (!$garage_activity) {

							$garage_activity_bd = array(
								'GarageCustomerActivity' => array(
									'garage_id' => $garage_exist['Garage']['id'],
									'customer_activity_id' => 5,
									'order' => 1
								)
							);
							$garage_activity_model->create();
							$garage_activity_tmp = $garage_activity_model->save($garage_activity_bd);
							if (!$garage_activity_tmp) {
								CakeLog::write('updates-france', 'The garage activity could not be created.' . PHP_EOL);
							}
						} else {
							$garage_activity = $garage_activity_model->findLastOrderByGarageId($garage_exist['Garage']['id']); //Order

							$garage_activity_bd = array(
								'GarageCustomerActivity' => array(
									'garage_id' => $garage_exist['Garage']['id'],
									'customer_activity_id' => 5,
									'order' => $garage_activity[0][0]['max_tmp'] + 1
								)
							);
							$garage_activity_model->create();
							$garage_activity_tmp = $garage_activity_model->save($garage_activity_bd);
							if (!$garage_activity_tmp) {
								CakeLog::write('updates-france', 'The garage activity could not be created.' . PHP_EOL);
							}
						}
					}
				} elseif (
					$network['nom'] == 'Top Truck'
				) {
					$garage_activity_model = ClassRegistry::init('GarageCustomerActivity');

					$garage_customer_activity_exist = $garage_activity_model->findByGarageIdAndCustomerActivityId($garage_exist['Garage']['id'], 2);
					if (!$garage_customer_activity_exist) {
						$garage_activity = $garage_activity_model->findByGarageIdAndOrder($garage_exist['Garage']['id'], 1); //Order
						if (!$garage_activity) {

							$garage_activity_bd = array(
								'GarageCustomerActivity' => array(
									'garage_id' => $garage_exist['Garage']['id'],
									'customer_activity_id' => 2,
									'order' => 1
								)
							);
							$garage_activity_model->create();
							$garage_activity_tmp = $garage_activity_model->save($garage_activity_bd);
							if (!$garage_activity_tmp) {
								CakeLog::write('updates-france', 'The garage activity could not be created.' . PHP_EOL);
							}
						} else {
							$garage_activity = $garage_activity_model->findLastOrderByGarageId($garage_exist['Garage']['id']); //Order

							$garage_activity_bd = array(
								'GarageCustomerActivity' => array(
									'garage_id' => $garage_exist['Garage']['id'],
									'customer_activity_id' => 2,
									'order' => $garage_activity['GarageCustomerActivity'][0][0]['max_tmp'] + 1
								)
							);
							$garage_activity_model->create();
							$garage_activity_tmp = $garage_activity_model->save($garage_activity_bd);
							if (!$garage_activity_tmp) {
								CakeLog::write('updates-france', 'The garage activity could not be created.' . PHP_EOL);
							}
						}
					}
				} elseif (
					$network['nom'] == 'Top Revision'
				) {
					$service_model = ClassRegistry::init('Service');
					$garage_service_model = ClassRegistry::init('GarageService');

					$service_tmp = $service_model->findByNameFr('top revision');

					if (empty($service_tmp)) {
						$service_bd = array(
							'Service' => array(
								'name_en' => 'top revision',
								'name_fr' => 'top revision',
								'name_de' => 'top revision',
								'url' => 'no-image.png',
							)
						);

						$service_model->create();
						$service_tmp = $service_model->save($service_bd);
						if (!$service_tmp) {
							CakeLog::write('updates-france', 'The service could not be created.' . PHP_EOL);
						}
					}

					$garage_service_exist = $garage_service_model->findByGarageIdAndServiceId($garage_exist['Garage']['id'], $service_tmp['Service']['id']);
					if (!$garage_service_exist) {
						$garage_service_tmp = array(
							'GarageService' => array(
								'garage_id' => $garage_exist['Garage']['id'],
								'service_id' => $service_tmp['Service']['id'],
							)
						);

						$garage_service_model->create();
						$garage_service_model->save($garage_service_tmp);
						if (!$garage_service_model) {
							CakeLog::write('updates-france', 'The garage service could not be created.' . PHP_EOL);
						}
					}
				}
			}
			$this->commit();
		}
		return $garage_creation_date;
	}

	/**
	 * The data comes from a Json left on the server.
	 */
	public function updateGarageNetwork($garage_json, $exist_garage_id, &$errors)
	{
		$this->RepairMaintenance = ClassRegistry::init('RepairMaintenance');
		$garage_networks = $this->findListNetworksByGarage($exist_garage_id);

		// FIRST DELETE ALL NETWORK RM
		foreach ($garage_networks as $key => $garage_network_id) {
			$this->RepairMaintenance->actualizar_redes_taller($exist_garage_id, $garage_network_id, true);
		}

		foreach ($garage_json['Networks'] as $network) {
			if ($network['StartDate']) {
				$network_type = $this->_networkType($network['ContractType']);
				$trading_group = $this->tradingGroup($network['ContractType']);

				$local_garage_network = $this->findFirstByGarageIdAndNetworkIdAndStatusAndLast(
					$exist_garage_id,
					$network_type,
					$this->statusNetwork($network),
					ConstantsBooleans::ACTIVE
				);

				if (!$local_garage_network) {
					$garage_network_tmp = array(
						'GarageNetwork' => array(
							'garage_id' => $exist_garage_id,
							'network_id' => $network_type,
							'trading_group_id' => $trading_group,
							'contract_sent_date' => ($network['ContractSentDate']) ? $this->extractDate($network['ContractSentDate']) : null,
							'contract_received_date' => ($network['ContractReceivedDate']) ? $this->extractDate($network['ContractReceivedDate']) : null,
							'contract_start_date' => ($network['StartDate']) ? $this->extractDate($network['StartDate']) : null,
							'contract_end_date' => ($network['CancellationDate']) ? $this->extractDate($network['CancellationDate']) : null,
							'reason_leaving_id' => null,
							'status' => $this->statusNetwork($network),
							'creation_date' => date('Y-m-d H:i:s'),
							'last' => ConstantsBooleans::ACTIVE
						)
					);

					if ($network['ReasonForLeaving']) {
						$this->LeavingReasonType = ClassRegistry::init('LeavingReasonType');
						$leaving_reason_type_exits = $this->LeavingReasonType->findByNameEn($network['ReasonForLeaving']);
						if (!$leaving_reason_type_exits) {
							$leaving_reason_type_tmp = array(
								'LeavingReasonType' => array(
									'name_en' => $network['ReasonForLeaving'],
									'name_fr' => $network['ReasonForLeaving'],
									'name_de' => $network['ReasonForLeaving'],
									'name_lc' => 'Bd.Leaving_reason_types',
								)
							);

							$this->LeavingReasonType->create();
							$leaving_reason_type_exits = $this->LeavingReasonType->save($leaving_reason_type_tmp);
							if (!$leaving_reason_type_exits) {
								CakeLog::write('updates', 'Couldn\'t create comment about the garage' . PHP_EOL);
								$errors['Couldn\'t create comment about the garage'] = translateDataErrors($this->LeavingReasonType->validationErrors);
								return;
							}

							$this->validator()->remove('contract_sent_date');
							$this->validator()->remove('current_charge');
							$this->validator()->remove('member_pays');
							$this->validator()->remove('garage_pays');
							$this->create();

							if (!$this->save($garage_network_tmp)) {
								debug($this->validationErrors);
								CakeLog::write('updates', 'Network link could not be created' . PHP_EOL);
								$errors['Network link could not be created'] = translateDataErrors($this->validationErrors);
								return;
							}
						} else {
							$garage_network_tmp['GarageNetwork']['reason_leaving_id'] = $leaving_reason_type_exits['LeavingReasonType']['id'];
							$this->validator()->remove('contract_sent_date');
							$this->validator()->remove('current_charge');
							$this->validator()->remove('member_pays');
							$this->validator()->remove('garage_pays');
							$this->create();

							if (!$this->save($garage_network_tmp)) {
								debug($this->validationErrors);
								CakeLog::write('updates', 'Network link could not be created' . PHP_EOL);
								$errors['Network link could not be created'] = translateDataErrors($this->validationErrors);
								return;
							}
						}
					} else {
						$this->validator()->remove('contract_sent_date');
						$this->validator()->remove('current_charge');
						$this->validator()->remove('member_pays');
						$this->validator()->remove('garage_pays');
						$this->create();

						if (!$this->save($garage_network_tmp)) {
							debug($this->validationErrors);
							CakeLog::write('updates', 'Network link could not be created' . PHP_EOL);
							$errors['Network link could not be created'] = translateDataErrors($this->validationErrors);
							return;
						}
					}

					// NETWORK RM
					if ($garage_network_tmp['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
						// ADD NETWORK RM
						$this->RepairMaintenance->actualizar_redes_taller($exist_garage_id, $network_type);
					}
				} else {
					unset($garage_networks[$local_garage_network['GarageNetwork']['id']]);
				}
			}
		}

		// Check last = 0 for old data
		foreach ($garage_networks as $key => $garage_network_id) {
			$this->updateLast($key);
		}

		return true;
	}

	public function getAllByGarageIdAndActiveAndGarageSpecificQuoting($garage_id)
	{
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'INNER',
						'conditions' => array(
							'Network.id = GarageNetwork.network_id',
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.garage_id' => $garage_id,
					'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
					'Network.quoting_type_id' => ConstantsQuotingPricingTypes::GARAGE_SPECIFIC_QUOTING
				),
				'order' => 'Network.name DESC'
			)
		);
	}

	public function getContractStartDateByGarage($garage_id)
	{
		return $this->find(
			'first',
			array(
				'conditions' => array(
					'GarageNetwork.garage_id' => $garage_id,
				),
				'order' => 'GarageNetwork.contract_start_date ASC'
			)
		);
	}

	public function _networkType($type)
	{
		$network_type = '';
		if ($type == 'AGN') {
			$network_type = 2;
		} elseif ($type == 'AutoCare') {
			$network_type = 1;
		} elseif ($type == 'TopTruck') {
			$network_type = 3;
		} elseif ($type == 'UGS') {
			$network_type = 4;
		}
		return $network_type;
	}

	private function tradingGroup($networktype)
	{
		$trading_group = '';
		if ($networktype == 'AGN') {
			$trading_group = 1;
		} elseif ($networktype == 'AutoCare') {
			$trading_group = 1;
		} elseif ($networktype == 'TopTruck') {
			$trading_group = 1;
		} elseif ($networktype == 'UGS') {
			$trading_group = 2;
		}
		return $trading_group;
	}

	private function statusNetwork($network)
	{
		if ($network['CancellationDate']) {
			return ConstantsNetworksStatus::UNSUBSCRIBE;
		} elseif ($network['StartDate']) {
			return ConstantsNetworksStatus::LIVE;
		} elseif ($network['ContractReceivedDate']) {
			return ConstantsNetworksStatus::AWAITING_DECISION;
		} elseif ($network['ContractSentDate']) {
			return ConstantsNetworksStatus::AWAITING_VISIT;
		} else {
			return ConstantsNetworksStatus::PROSPECT;
		}
	}

	private function extractDate($date)
	{
		return date('Y-m-d', substr(substr(substr($date, 0, -2), 6), 0, 10));
	}

	public function getByGarageId($garage_id)
	{
		$garage =  $this->find(
			'first',
			array(
				'conditions' => array(
					'GarageNetwork.garage_id' => $garage_id,
					'OR' => array(
						array('GarageNetwork.network_id' => NETWORK_ID_AUTOCARE),
						array('GarageNetwork.network_id' => NETWORK_ID_UNITED_GARAGE_SERVICE)
					)
				),
			)
		);
		if (!$garage) {
			$garage =  $this->find(
				'first',
				array(
					'conditions' => array(
						'GarageNetwork.garage_id' => $garage_id,
					),
				)
			);
		}
		return $garage;
	}

	/**
	 * From the garages list of a network, gets the other
	 * networks that these garages belong to (always checking the status)
	 *
	 * @param int $network_id
	 */
	public function getOtherNetworksFromGarageNetwork($network_id)
	{
		$data = $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'GarageNetwork2',
						'table' => 'garages_networks',
						'type' => 'INNER',
						'conditions' => array(
							'GarageNetwork.garage_id = GarageNetwork2.garage_id',
							'GarageNetwork2.status' => ConstantsNetworksStatus::LIVE,
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.network_id' => $network_id,
					'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
				),
				'fields' => array(
					'distinct(GarageNetwork2.network_id)'
				),
			)
		);
		return Hash::extract($data, '{n}.GarageNetwork2.network_id');
	}

	/**
	 * From the garages list of a network, gets the other
	 * networks that these garages belong to (always checking the status), in this version its filtered
	 * by the garage id thay is appearinng in the garages list of AGN, it also returns the final list needed for the AGN
	 * filters
	 *
	 * @param int $networkId
	 * @param array $garagesIds
	 * @return array Hash table with network id as key and network value as name
	 */
	public function getOtherNetworksFromGarageNetworkFilteredByGarageId($networkId, $selectedNetworks, $garagesIds)
	{
		if (!empty($garagesIds)) {
			$conditions = array(
				'GarageNetwork.garage_id IN' => $garagesIds,
				'GarageNetwork.garage_id = GarageNetwork2.garage_id',
				'GarageNetwork2.status' => ConstantsNetworksStatus::LIVE,
			);
		} elseif (!empty($selectedNetworks)) {
			return $this->Network->find('list', array(
				'conditions' => array(
					'id' => $selectedNetworks,
					'id !=' => $networkId, // do not include the network from the input parameter
					'internal' => true, // only internal networks
				),
				'fields' => array('id', 'name')
			));
		} else {
			$conditions = array(
				'GarageNetwork.garage_id = GarageNetwork2.garage_id',
				'GarageNetwork2.status' => ConstantsNetworksStatus::LIVE,
			);
		}

		$networksIds = $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'GarageNetwork2',
						'table' => 'garages_networks',
						'type' => 'INNER',
						'conditions' => $conditions,
					),
				),
				'conditions' => array(
					'GarageNetwork.network_id' => $networkId,
					'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
				),
				'fields' => array(
					'distinct(GarageNetwork2.network_id)'
				),
			)
		);

		$networksIds = Hash::extract($networksIds, '{n}.GarageNetwork2.network_id');

		return $this->Network->find('list', array(
			'conditions' => array(
				'id' => $networksIds,
				'id !=' => $networkId, // do not include the network from the input parameter
				'internal' => true, // only internal networks
			),
			'fields' => array('id', 'name')
		));
	}

	public function add_opening_garage($garageNetwork)
	{
		$fields = array(
			'GarageNetwork' => array(
				'monday_planner_open_1',
				'monday_planner_closed_1',
				'monday_planner_open_2',
				'monday_planner_closed_2',
				'tuesday_planner_open_1',
				'tuesday_planner_closed_1',
				'tuesday_planner_open_2',
				'tuesday_planner_closed_2',
				'wednesday_planner_open_1',
				'wednesday_planner_closed_1',
				'wednesday_planner_open_2',
				'wednesday_planner_closed_2',
				'thursday_planner_open_1',
				'thursday_planner_closed_1',
				'thursday_planner_open_2',
				'thursday_planner_closed_2',
				'friday_planner_open_1',
				'friday_planner_closed_1',
				'friday_planner_open_2',
				'friday_planner_closed_2',
				'saturday_planner_open_1',
				'saturday_planner_closed_1',
				'saturday_planner_open_2',
				'saturday_planner_closed_2',
				'sunday_planner_open_1',
				'sunday_planner_closed_1',
				'sunday_planner_open_2',
				'sunday_planner_closed_2',
				'monday_planner_max_1',
				'monday_planner_max_2',
				'tuesday_planner_max_1',
				'tuesday_planner_max_2',
				'wednesday_planner_max_1',
				'wednesday_planner_max_2',
				'thursday_planner_max_1',
				'thursday_planner_max_2',
				'friday_planner_max_1',
				'friday_planner_max_2',
				'saturday_planner_max_1',
				'saturday_planner_max_2',
				'sunday_planner_max_1',
				'sunday_planner_max_2',
				'modification_date',
				'booking_days_min_from',
				'booking_days_max_to'
			)
		);

		$garageNetwork['modification_date'] = date('Y-m-d H:i:s');
		$days = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
		foreach ($days as $day) {
			$garageNetwork[$day . "_planner_open_1"] = empty($garageNetwork[$day . "_planner_open_1"]) ? null : $garageNetwork[$day . "_planner_open_1"];
			$garageNetwork[$day . "_planner_closed_1"] = empty($garageNetwork[$day . "_planner_closed_1"]) ? null : $garageNetwork[$day . "_planner_closed_1"];
			$garageNetwork[$day . "_planner_open_2"] = empty($garageNetwork[$day . "_planner_open_2"]) ? null : $garageNetwork[$day . "_planner_open_2"];
			$garageNetwork[$day . "_planner_closed_2"] = empty($garageNetwork[$day . "_planner_closed_2"]) ? null : $garageNetwork[$day . "_planner_closed_2"];
			if (!isset($garageNetwork[$day . "_planner_max_1"])) {
				$garageNetwork[$day . "_planner_max_1"] = null;
			}

			if (!isset($garageNetwork[$day . "_planner_max_2"])) {
				$garageNetwork[$day . "_planner_max_2"] = null;
			}
		}

		$garageToSave["GarageNetwork"] = $garageNetwork;
		$garageBd = $this->guardar($garageToSave, $fields);
		if (!$garageBd) {
			return false;
		}

		return $garageBd;
	}

	public function getCalendarPlannerHours($garageNetworkId, $garageId)
	{
		if (CakeSession::read('Auth.User.current_network')) {
			$networkClass = ClassRegistry::init('Network');
			$network = $networkClass->findById(CakeSession::read('Auth.User.current_network'));
			$color = $network['Network']['primary_color'];
		} else {
			$color = '6a99ff';
		}

		$garageNetwork = $this->findById($garageNetworkId);

		$garageClass = ClassRegistry::init('Garage');
		$garage = $garageClass->findById($garageId);

		$garageJs = array();
		$days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');

		foreach ($days as $key => $day) {
			if (
				!empty($garage['Garage'][strtolower($day) . '_open_1']) ||
				!empty($garage['Garage'][strtolower($day) . '_open_2'])
			) {

				if (
					!empty($garage['Garage'][strtolower($day) . '_open_1']) &&
					!empty($garage['Garage'][$day . '_open_1']) &&
					!empty($garage['Garage'][$day . '_closed_1'])
				) {
					$garageJs[] = array(
						'id' => uniqid(),
						'color' => $color,
						'start' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) .
							' ' . $garage['Garage'][$day . '_open_1'],
						'end' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) .
							' ' . $garage['Garage'][$day . '_closed_1'],
						'open' => $garage['Garage'][$day . '_open_1'],
						'closed' => $garage['Garage'][$day . '_closed_1'],
						'day' => $key + 1,
						'rendering' => 'background',
						'editable' => false,
					);
				}
				if (
					!empty($garage['Garage'][strtolower($day) . '_open_2']) &&
					!empty($garage['Garage'][$day . '_open_2']) &&
					!empty($garage['Garage'][$day . '_closed_2'])
				) {
					$garageJs[] = array(
						'id' => uniqid(),
						'color' => $color,
						'start' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) .
							' ' . $garage['Garage'][$day . '_open_2'],
						'end' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) .
							' ' . $garage['Garage'][$day . '_closed_2'],
						'open' => $garage['Garage'][$day . '_open_2'],
						'closed' => $garage['Garage'][$day . '_closed_2'],
						'day' => $key + 1,
						'rendering' => 'background',
						'editable' => false,
					);
				}
			}
		}
		foreach ($days as $key => $day) {
			if (
				!empty($garage['Garage'][strtolower($day) . '_open_1']) ||
				!empty($garage['Garage'][strtolower($day) . '_open_2'])
			) {
				if (
					!empty($garageNetwork['GarageNetwork'][$day . '_planner_open_1']) &&
					!empty($garageNetwork['GarageNetwork'][$day . '_planner_closed_1'])
				) {
					$garageJs[] = array(
						'id' => uniqid(),
						'color' => $color,
						'start' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) .
							' ' . $garageNetwork['GarageNetwork'][$day . '_planner_open_1'],
						'end' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) .
							' ' . $garageNetwork['GarageNetwork'][$day . '_planner_closed_1'],
						'open' => $garageNetwork['GarageNetwork'][$day . '_planner_open_1'],
						'closed' => $garageNetwork['GarageNetwork'][$day . '_planner_closed_1'],
						'day' => $key + 1
					);
				}
				if (
					!empty($garageNetwork['GarageNetwork'][$day . '_planner_open_2']) &&
					!empty($garageNetwork['GarageNetwork'][$day . '_planner_closed_2'])
				) {
					$garageJs[] = array(
						'id' => uniqid(),
						'color' => $color,
						'start' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) .
							' ' . $garageNetwork['GarageNetwork'][$day . '_planner_open_2'],
						'end' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) .
							' ' . $garageNetwork['GarageNetwork'][$day . '_planner_closed_2'],
						'open' => $garageNetwork['GarageNetwork'][$day . '_planner_open_2'],
						'closed' => $garageNetwork['GarageNetwork'][$day . '_planner_closed_2'],
						'day' => $key + 1
					);
				}
			}
		}
		return $garageJs;
	}

	public function getGarageRegionsByNetwork($region_id, $network_id, $conditions)
	{
		if (!empty($conditions['aag_region_id'])) {
			if (in_array($region_id, $conditions['aag_region_id'])) {
				$region_ids = $region_id;
			} else {
				return 0;
			}
		} else {
			$region_ids = $region_id;
		}

		if (!empty($conditions['network_id'])) {
			if (in_array($network_id, $conditions['network_id'])) {
				$network_ids = $network_id;
			} else {
				return 0;
			}
		} else {
			$network_ids = $network_id;
		}

		return  $this->find(
			'count',
			array(
				'joins' => array(
					array(
						'alias' => 'Garage',
						'table' => 'garages',
						'type' => 'LEFT',
						'conditions' => array(
							'GarageNetwork.garage_id = Garage.id'
						)
					),
					array(
						'alias' => 'City',
						'table' => 'cities',
						'type' => 'LEFT',
						'conditions' => array(
							'Garage.city_id = City.id'
						)
					),
					array(
						'alias' => 'Province',
						'table' => 'provinces',
						'type' => 'LEFT',
						'conditions' => array(
							'Province.id = City.province_id'
						)
					),
					array(
						'alias' => 'Country',
						'table' => 'countries',
						'type' => 'LEFT',
						'conditions' => array(
							'Country.id = Province.country_id'
						)
					),
					array(
						'alias' => 'AagRegion',
						'table' => 'aag_regions',
						'type' => 'LEFT',
						'conditions' => array(
							'AagRegion.id = Country.aag_region_id'
						)
					),
				),
				'conditions' => array(
					'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
					'GarageNetwork.network_id ' => $network_ids,
					'AagRegion.id ' => $region_ids,
				)
			)
		);
	}

	/**
	 * Update the GarageNetwork modification date.
	 */
	public function updateGarageNetworkModificationDate($garageNetworkId)
	{
		$fields = array(
			'GarageNetwork' => array(
				'modification_date',
			)
		);

		$garageNetwork['GarageNetwork'] = array(
			'id' => $garageNetworkId,
			'modification_date' => date('Y-m-d H:i:s')
		);

		$garageNetworkBd = $this->guardar($garageNetwork, $fields);
		if (!$garageNetworkBd) {
			return false;
		}

		$this->commit();
		return $garageNetworkBd;
	}

	/**
	 * Create a GarageNetwork that comes from a JSON from Lobster with the data for the GV network.
	 */
	public function addGarageNetworkGV($garageNetwork)
	{
		$fields = array(
			'GarageNetwork' => array(
				'garage_id',
				'network_id',
				'status',
				'code',
				'sap_code',
				'creation_date'
			)
		);

		$garageNetwork['GarageNetwork']['creation_date'] = date('Y-m-d H:i:s');
		$this->create();

		$garageNetworkBd = $this->guardar($garageNetwork, $fields);
		if (!$garageNetworkBd) {
			return false;
		}

		$garageClass = ClassRegistry::init("Garage");
		$garageClass->edit_modification_date_garage($garageNetwork['GarageNetwork']['garage_id']);

		$this->commit();
		return $garageNetworkBd;
	}

	/**
	 * Update a GarageNetwork that comes from a JSON from Lobster with the data for the GV network.
	 */
	public function updateGarageNetworkGV($garageNetwork)
	{
		$fields = array(
			'GarageNetwork' => array(
				'status',
				'sap_code',
				'modification_date'
			)
		);
		$garageNetwork['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

		$garageNetworkBd = $this->guardar($garageNetwork, $fields);
		if (!$garageNetworkBd) {
			return false;
		}

		$garageClass = ClassRegistry::init("Garage");
		$garageClass->edit_modification_date_garage($garageNetwork['GarageNetwork']['garage_id']);

		$this->commit();
		return $garageNetworkBd;
	}

	public function editCreditGarageNetworkByID($training_credit, $garage_network)
	{
		// Update GarageNetwork
		$fields = array(
			'GarageNetwork' => array(
				'credit',
				'modification_date'
			)
		);
		$garage_network['GarageNetwork']['credit'] = $garage_network['GarageNetwork']['credit'] + $training_credit['TrainingNetworkCredit']['credit_given'];
		$garage_network['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

		$garages_networks_bd = $this->guardar($garage_network, $fields);
		if (!$garages_networks_bd) {
			return false;
		}
		return $garages_networks_bd;
	}

	public function editCreditGarageNetworkByIDSpent($garage_network)
	{
		$fields = array(
			'GarageNetwork' => array(
				'credit',
				'modification_date'
			)
		);
		$garages_networks['GarageNetwork']['id'] = $garage_network['GarageNetwork']['GarageNetwork']['id'];
		$garages_networks['GarageNetwork']['credit'] = $garage_network['GarageNetwork']['GarageNetwork']['credit'] - $garage_network['TrainingCourse']['TrainingCourse']['price_credit'];
		$garage_network['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

		$garages_networks_bd = $this->guardar($garages_networks, $fields);
		if (!$garages_networks_bd) {
			return false;
		}
		return $garages_networks_bd;
	}

	public function editCreditGarageNetworkReturnCredit($garage_network)
	{
		$fields = array(
			'GarageNetwork' => array(
				'credit',
				'modification_date'
			)
		);
		$garages_networks['GarageNetwork']['id'] = $garage_network['GarageNetwork']['GarageNetwork']['id'];
		$garages_networks['GarageNetwork']['credit'] = $garage_network['GarageNetwork']['GarageNetwork']['credit'] + $garage_network['TrainingCourse']['TrainingCourse']['price_credit'];
		$garage_network['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

		$garages_networks_bd = $this->guardar($garages_networks, $fields);
		if (!$garages_networks_bd) {
			return false;
		}
		return $garages_networks_bd;
	}

	public function editCreditGarageByDelegate($garage_network, $course, $training_credit)
	{
		$fields = array(
			'GarageNetwork' => array(
				'credit',
				'modification_date'
			)
		);
		$garages_networks['GarageNetwork']['id'] = $garage_network['GarageNetwork']['id'];
		if (isset($training_credit['TrainingNetworkCredit']['credit_given'])) {
			$garages_networks['GarageNetwork']['credit'] = $garage_network['GarageNetwork']['credit'] + $training_credit['TrainingNetworkCredit']['credit_given'];
		} else {
			$garages_networks['GarageNetwork']['credit'] = $garage_network['GarageNetwork']['credit'] - $course['TrainingCourse']['price_credit'];
		}
		$garage_network['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

		$garages_networks_bd = $this->guardar($garages_networks, $fields);
		if (!$garages_networks_bd) {
			return false;
		}
		return $garages_networks_bd;
	}

	public function editCreditGarageNetworkReturnCreditTwo($garage_network)
	{
		$fields = array(
			'GarageNetwork' => array(
				'credit',
				'modification_date'
			)
		);

		$garages_networks['GarageNetwork']['id'] = $garage_network['GarageNetwork']['GarageNetwork']['id'];
		$garages_networks['GarageNetwork']['credit'] = $garage_network['GarageNetwork']['GarageNetwork']['credit'] + $garage_network['TrainingCourse']['price_credit'];
		$garages_networks['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

		$garages_networks_bd = $this->guardar($garages_networks, $fields);
		if (!$garages_networks_bd) {
			return false;
		}
		return $garages_networks_bd;
	}

	/**
	 * Default: Mark last as 0 (desactivate)
	 */
	private function updateLast($garage_network_id, $activate = false)
	{
		$fields = array(
			'GarageNetwork' => array(
				'last',
			)
		);

		$garage_network = $this->findFirstById($garage_network_id);
		$garage_network['GarageNetwork']['last'] = $activate ? ConstantsBooleans::ACTIVE : ConstantsBooleans::NO_ACTIVE;

		return $this->guardar($garage_network, $fields);
	}

	/**
	 * Set up deafult values for AGN garages.
	 */
	public function setDefaultValuesAgn($garageNetwork = null, $garageId)
	{

		$garage = $this->Garage->findById($garageId);

		//Services
		$services = $this->Service->find('all');

		//Vehicle types
		$vehicleTypes = $this->VehicleType->find('all');

		if ($garageNetwork['GarageNetwork']['network_id'] == NETWORK_ID_AGN) {
			$garageNetwork['GarageNetwork']['quoting_views_active'] = ConstantsBooleans::ACTIVE;
			$garageNetwork['GarageNetwork']['quoting_active'] = ConstantsBooleans::ACTIVE;
			$garageNetwork['GarageNetwork']['enquiries_active'] = ConstantsBooleans::ACTIVE;
			$garageNetwork['GarageNetwork']['about'] = '';
			$garageNetwork['GarageNetwork']['booking_days_min_from'] = 3;
			$garageNetwork['GarageNetwork']['booking_days_max_to'] = 60;
			$garageNetwork['GarageNetwork']['monday_planner_max_1'] = 1;
			$garageNetwork['GarageNetwork']['monday_planner_max_2'] = 1;
			$garageNetwork['GarageNetwork']['tuesday_planner_max_1'] = 1;
			$garageNetwork['GarageNetwork']['tuesday_planner_max_2'] = 1;
			$garageNetwork['GarageNetwork']['wednesday_planner_max_1'] = 1;
			$garageNetwork['GarageNetwork']['wednesday_planner_max_2'] = 1;
			$garageNetwork['GarageNetwork']['thursday_planner_max_1'] = 1;
			$garageNetwork['GarageNetwork']['thursday_planner_max_2'] = 1;
			$garageNetwork['GarageNetwork']['friday_planner_max_1'] = 1;
			$garageNetwork['GarageNetwork']['friday_planner_max_2'] = 1;
			$garageNetwork['GarageNetwork']['saturday_planner_max_1'] = 1;
			$garageNetwork['GarageNetwork']['saturday_planner_max_2'] = 0;
			$garageNetwork['GarageNetwork']['sunday_planner_max_1'] = 0;
			$garageNetwork['GarageNetwork']['sunday_planner_max_2'] = 0;
			$garageNetwork['GarageNetwork']['monday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['monday_planner_closed_1'] = '17:00';
			$garageNetwork['GarageNetwork']['tuesday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['tuesday_planner_closed_1'] = '17:00';
			$garageNetwork['GarageNetwork']['wednesday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['wednesday_planner_closed_1'] = '17:00';
			$garageNetwork['GarageNetwork']['thursday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['thursday_planner_closed_1'] = '17:00';
			$garageNetwork['GarageNetwork']['friday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['friday_planner_closed_1'] = '17:00';
			$garageNetwork['GarageNetwork']['saturday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['saturday_planner_closed_1'] = '12:00';

			$fields = array(
				'GarageNetwork' => array(
					'quoting_views_active',
					'quoting_active',
					'enquiries_active',
					'about',
					'booking_days_min_from',
					'booking_days_max_to',
					'monday_planner_max_1',
					'monday_planner_max_2',
					'tuesday_planner_max_1',
					'tuesday_planner_max_2',
					'wednesday_planner_max_1',
					'wednesday_planner_max_2',
					'thursday_planner_max_1',
					'thursday_planner_max_2',
					'friday_planner_max_1',
					'friday_planner_max_2',
					'saturday_planner_max_1',
					'saturday_planner_max_2',
					'sunday_planner_max_1',
					'sunday_planner_max_2',
					'monday_planner_open_1',
					'monday_planner_closed_1',
					'tuesday_planner_open_1',
					'tuesday_planner_closed_1',
					'wednesday_planner_open_1',
					'wednesday_planner_closed_1',
					'thursday_planner_open_1',
					'thursday_planner_closed_1',
					'friday_planner_open_1',
					'friday_planner_closed_1',
					'saturday_planner_open_1',
					'saturday_planner_closed_1',
				)
			);

			$this->save($garageNetwork, array('fieldList' => $fields));
			$this->Garage->default_opening_hours_values_in_garage($garage);

			//Services
			foreach ($services as $servide) {
				$garageNetworkServiceSave = array(
					'GarageNetworkService' => array(
						'garage_network_id' => $garageNetwork['GarageNetwork']['id'],
						'service_id' => $servide['Service']['id'],
					)
				);

				$fields = array('GarageNetworkService' => array('garage_network_id', 'service_id'));
				$this->GarageNetworkService->create();
				$this->GarageNetworkService->save($garageNetworkServiceSave, array('fieldList' => $fields));

				$garageServiceSave = array(
					'GarageService' => array(
						'garage_id' => $garageNetwork['GarageNetwork']['garage_id'],
						'service_id' => $servide['Service']['id'],
					)
				);

				$fields = array('GarageService' => array('garage_id', 'service_id'));
				$this->GarageService->create();
				$this->GarageService->save($garageServiceSave, array('fieldList' => $fields));
			}

			//Vehicle types
			foreach ($vehicleTypes as $vehicleType) {
				$garageNetworkVehicleTypeSave = array(
					'GarageNetworkVehicleType' => array(
						'garage_network_id' => $garageNetwork['GarageNetwork']['id'],
						'vehicle_type_id' => $vehicleType['VehicleType']['id'],
					)
				);

				$fields = array('GarageNetworkVehicleType' => array('garage_network_id', 'vehicle_type_id'));
				$this->GarageNetworkVehicleType->create();
				$this->GarageNetworkVehicleType->save($garageNetworkVehicleTypeSave, array('fieldList' => $fields));
			}
		}
	}

	/**
	 * Set up deafult values for GV garages.
	 */
	public function setDefaultValuesGv($garageNetwork = null, $garageId)
	{
		$garage = $this->Garage->findById($garageId);
		//GV Works
		$gvWorks = $this->Work->find(
			'all',
			array(
				'conditions' => array(
					'Work.network_id' => NETWORK_ID_GV,
					'Work.active' => ConstantsBooleans::ACTIVE
				)
			)
		);

		// Vehicle types
		$vehicleTypes = $this->VehicleType->find('all');

		if ($garageNetwork['GarageNetwork']['network_id'] == NETWORK_ID_GV) {
			$garageNetwork['GarageNetwork']['quoting_views_active'] = ConstantsBooleans::ACTIVE;
			$garageNetwork['GarageNetwork']['quoting_active'] = ConstantsBooleans::ACTIVE;
			$garageNetwork['GarageNetwork']['enquiries_active'] = ConstantsBooleans::ACTIVE;
			$garageNetwork['GarageNetwork']['about'] = 'Bij ons autobedrijf kun je terecht voor onderhoud en reparatie aan jouw auto. Klik hierboven op afspraak maken en plan een afspraak in!';
			$garageNetwork['GarageNetwork']['booking_days_min_from'] = 1;
			$garageNetwork['GarageNetwork']['booking_days_max_to'] = 90;
			$garageNetwork['GarageNetwork']['monday_planner_max_1'] = 10;
			$garageNetwork['GarageNetwork']['monday_planner_max_2'] = 10;
			$garageNetwork['GarageNetwork']['tuesday_planner_max_1'] = 10;
			$garageNetwork['GarageNetwork']['tuesday_planner_max_2'] = 10;
			$garageNetwork['GarageNetwork']['wednesday_planner_max_1'] = 10;
			$garageNetwork['GarageNetwork']['wednesday_planner_max_2'] = 10;
			$garageNetwork['GarageNetwork']['thursday_planner_max_1'] = 10;
			$garageNetwork['GarageNetwork']['thursday_planner_max_2'] = 10;
			$garageNetwork['GarageNetwork']['friday_planner_max_1'] = 10;
			$garageNetwork['GarageNetwork']['friday_planner_max_2'] = 10;
			$garageNetwork['GarageNetwork']['saturday_planner_max_1'] = 10;
			$garageNetwork['GarageNetwork']['saturday_planner_max_2'] = 10;
			$garageNetwork['GarageNetwork']['sunday_planner_max_1'] = 10;
			$garageNetwork['GarageNetwork']['sunday_planner_max_2'] = 10;
			$garageNetwork['GarageNetwork']['monday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['monday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['tuesday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['tuesday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['wednesday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['wednesday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['thursday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['thursday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['friday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['friday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['saturday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['saturday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['sunday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['sunday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['monday_planner_open_2'] = '13:00';
			$garageNetwork['GarageNetwork']['monday_planner_closed_2'] = '17:00';
			$garageNetwork['GarageNetwork']['tuesday_planner_open_2'] = '13:00';
			$garageNetwork['GarageNetwork']['tuesday_planner_closed_2'] = '17:00';
			$garageNetwork['GarageNetwork']['wednesday_planner_open_2'] = '13:00';
			$garageNetwork['GarageNetwork']['wednesday_planner_closed_2'] = '17:00';
			$garageNetwork['GarageNetwork']['thursday_planner_open_2'] = '13:00';
			$garageNetwork['GarageNetwork']['thursday_planner_closed_2'] = '17:00';
			$garageNetwork['GarageNetwork']['friday_planner_open_2'] = '13:00';
			$garageNetwork['GarageNetwork']['friday_planner_closed_2'] = '17:00';
			$garageNetwork['GarageNetwork']['saturday_planner_open_2'] = '13:00';
			$garageNetwork['GarageNetwork']['saturday_planner_closed_2'] = '17:00';
			$garageNetwork['GarageNetwork']['sunday_planner_open_2'] = '13:00';
			$garageNetwork['GarageNetwork']['sunday_planner_closed_2'] = '17:00';

			$fields = array(
				'GarageNetwork' => array(
					'quoting_views_active',
					'quoting_active',
					'enquiries_active',
					'about',
					'booking_days_min_from',
					'booking_days_max_to',
					'monday_planner_max_1',
					'monday_planner_max_2',
					'tuesday_planner_max_1',
					'tuesday_planner_max_2',
					'wednesday_planner_max_1',
					'wednesday_planner_max_2',
					'thursday_planner_max_1',
					'thursday_planner_max_2',
					'friday_planner_max_1',
					'friday_planner_max_2',
					'saturday_planner_max_1',
					'saturday_planner_max_2',
					'sunday_planner_max_1',
					'sunday_planner_max_2',
					'monday_planner_open_1',
					'monday_planner_closed_1',
					'tuesday_planner_open_1',
					'tuesday_planner_closed_1',
					'wednesday_planner_open_1',
					'wednesday_planner_closed_1',
					'thursday_planner_open_1',
					'thursday_planner_closed_1',
					'friday_planner_open_1',
					'friday_planner_closed_1',
					'saturday_planner_open_1',
					'saturday_planner_closed_1',
					'sunday_planner_open_1',
					'sunday_planner_closed_1',
					'monday_planner_open_2',
					'monday_planner_closed_2',
					'tuesday_planner_open_2',
					'tuesday_planner_closed_2',
					'wednesday_planner_open_2',
					'wednesday_planner_closed_2',
					'thursday_planner_open_2',
					'thursday_planner_closed_2',
					'friday_planner_open_2',
					'friday_planner_closed_2',
					'saturday_planner_open_2',
					'saturday_planner_closed_2',
					'sunday_planner_open_2',
					'sunday_planner_closed_2'
				)
			);

			$this->save($garageNetwork, array('fieldList' => $fields));
			$this->Garage->default_opening_hours_values_in_garage($garage);

			//Setup Garage Networks Services

			//Works
			foreach ($gvWorks as $gvWork) {

				$garageNetworkWorkSave = array(
					'GarageNetworkWork' => array(
						'garage_network_id' => $garageNetwork['GarageNetwork']['id'],
						'work_id' => $gvWork['Work']['id'],
					)
				);

				$fields = array('GarageNetworkWork' => array('garage_network_id', 'work_id'));
				$this->GarageNetworkWork->create();
				$this->GarageNetworkWork->save($garageNetworkWorkSave, array('fieldList' => $fields));
			}

			//Vehicle types
			foreach ($vehicleTypes as $vehicleType) {
				$garageNetworkVehicleTypeSave = array(
					'GarageNetworkVehicleType' => array(
						'garage_network_id' => $garageNetwork['GarageNetwork']['id'],
						'vehicle_type_id' => $vehicleType['VehicleType']['id'],
					)
				);

				$fields = array('GarageNetworkVehicleType' => array('garage_network_id', 'vehicle_type_id'));
				$this->GarageNetworkVehicleType->create();
				$this->GarageNetworkVehicleType->save($garageNetworkVehicleTypeSave, array('fieldList' => $fields));
			}
		}
	}

	/**
	 * Set up deafult values for GC garages.
	 */
	public function setDefaultValuesGc($garageNetwork = null, $garageId)
	{
		$garage = $this->Garage->findById($garageId);
		//GC Works
		$gcWorks = $this->Work->find(
			'all',
			array(
				'conditions' => array(
					'Work.network_id' => NETWORK_ID_GC,
					'Work.active' => ConstantsBooleans::ACTIVE
				)
			)
		);

		// Vehicle types
		$vehicleTypes = $this->VehicleType->find('all');

		if ($garageNetwork['GarageNetwork']['network_id'] == NETWORK_ID_GC) {
			$garageNetwork['GarageNetwork']['quoting_views_active'] = ConstantsBooleans::ACTIVE;
			$garageNetwork['GarageNetwork']['quoting_active'] = ConstantsBooleans::ACTIVE;
			$garageNetwork['GarageNetwork']['enquiries_active'] = ConstantsBooleans::ACTIVE;
			$garageNetwork['GarageNetwork']['about'] = 'Bij ons autobedrijf kun je terecht voor onderhoud en reparatie aan jouw auto. Klik hierboven op afspraak maken en plan een afspraak in! Vous pouvez contacter notre entreprise automobile pour l’entretien et les réparations de votre voiture. Cliquez sur prendre rendez-vous ci-dessus et prenez rendez-vous !';
			$garageNetwork['GarageNetwork']['booking_days_min_from'] = 1;
			$garageNetwork['GarageNetwork']['booking_days_max_to'] = 90;
			$garageNetwork['GarageNetwork']['monday_planner_max_1'] = 10;
			$garageNetwork['GarageNetwork']['monday_planner_max_2'] = 10;
			$garageNetwork['GarageNetwork']['tuesday_planner_max_1'] = 10;
			$garageNetwork['GarageNetwork']['tuesday_planner_max_2'] = 10;
			$garageNetwork['GarageNetwork']['wednesday_planner_max_1'] = 10;
			$garageNetwork['GarageNetwork']['wednesday_planner_max_2'] = 10;
			$garageNetwork['GarageNetwork']['thursday_planner_max_1'] = 10;
			$garageNetwork['GarageNetwork']['thursday_planner_max_2'] = 10;
			$garageNetwork['GarageNetwork']['friday_planner_max_1'] = 10;
			$garageNetwork['GarageNetwork']['friday_planner_max_2'] = 10;
			$garageNetwork['GarageNetwork']['monday_planner_open_1'] = '08:30';
			$garageNetwork['GarageNetwork']['monday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['tuesday_planner_open_1'] = '08:30';
			$garageNetwork['GarageNetwork']['tuesday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['wednesday_planner_open_1'] = '08:30';
			$garageNetwork['GarageNetwork']['wednesday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['thursday_planner_open_1'] = '08:30';
			$garageNetwork['GarageNetwork']['thursday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['friday_planner_open_1'] = '08:30';
			$garageNetwork['GarageNetwork']['friday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['saturday_planner_open_1'] = '09:00';
			$garageNetwork['GarageNetwork']['saturday_planner_closed_1'] = '12:00';
			$garageNetwork['GarageNetwork']['monday_planner_open_2'] = '13:00';
			$garageNetwork['GarageNetwork']['monday_planner_closed_2'] = '18:00';
			$garageNetwork['GarageNetwork']['tuesday_planner_open_2'] = '13:00';
			$garageNetwork['GarageNetwork']['tuesday_planner_closed_2'] = '18:00';
			$garageNetwork['GarageNetwork']['wednesday_planner_open_2'] = '13:00';
			$garageNetwork['GarageNetwork']['wednesday_planner_closed_2'] = '18:00';
			$garageNetwork['GarageNetwork']['thursday_planner_open_2'] = '13:00';
			$garageNetwork['GarageNetwork']['thursday_planner_closed_2'] = '18:00';
			$garageNetwork['GarageNetwork']['friday_planner_open_2'] = '13:00';
			$garageNetwork['GarageNetwork']['friday_planner_closed_2'] = '18:00';

			$fields = array(
				'GarageNetwork' => array(
					'quoting_views_active',
					'quoting_active',
					'enquiries_active',
					'about',
					'booking_days_min_from',
					'booking_days_max_to',
					'monday_planner_max_1',
					'monday_planner_max_2',
					'tuesday_planner_max_1',
					'tuesday_planner_max_2',
					'wednesday_planner_max_1',
					'wednesday_planner_max_2',
					'thursday_planner_max_1',
					'thursday_planner_max_2',
					'friday_planner_max_1',
					'friday_planner_max_2',
					'monday_planner_open_1',
					'monday_planner_closed_1',
					'tuesday_planner_open_1',
					'tuesday_planner_closed_1',
					'wednesday_planner_open_1',
					'wednesday_planner_closed_1',
					'thursday_planner_open_1',
					'thursday_planner_closed_1',
					'friday_planner_open_1',
					'friday_planner_closed_1',
					'saturday_planner_open_1',
					'saturday_planner_closed_1',
					'monday_planner_open_2',
					'monday_planner_closed_2',
					'tuesday_planner_open_2',
					'tuesday_planner_closed_2',
					'wednesday_planner_open_2',
					'wednesday_planner_closed_2',
					'thursday_planner_open_2',
					'thursday_planner_closed_2',
					'friday_planner_open_2',
					'friday_planner_closed_2'
				)
			);

			$this->save($garageNetwork, array('fieldList' => $fields));
			$this->Garage->default_opening_hours_values_in_garage($garage);

			//Setup Garage Networks Services

			//Works
			foreach ($gcWorks as $gcWork) {

				$garageNetworkWorkSave = array(
					'GarageNetworkWork' => array(
						'garage_network_id' => $garageNetwork['GarageNetwork']['id'],
						'work_id' => $gcWork['Work']['id'],
					)
				);

				$fields = array('GarageNetworkWork' => array('garage_network_id', 'work_id'));
				$this->GarageNetworkWork->create();
				$this->GarageNetworkWork->save($garageNetworkWorkSave, array('fieldList' => $fields));
			}

			//Vehicle types
			foreach ($vehicleTypes as $vehicleType) {
				$garageNetworkVehicleTypeSave = array(
					'GarageNetworkVehicleType' => array(
						'garage_network_id' => $garageNetwork['GarageNetwork']['id'],
						'vehicle_type_id' => $vehicleType['VehicleType']['id'],
					)
				);

				$fields = array('GarageNetworkVehicleType' => array('garage_network_id', 'vehicle_type_id'));
				$this->GarageNetworkVehicleType->create();
				$this->GarageNetworkVehicleType->save($garageNetworkVehicleTypeSave, array('fieldList' => $fields));
			}
		}
	}

	public function getCompleteListOnContactStaffSubtractible()
	{
		return $this->find(
			'list',
			array(
				'joins' => array(
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'INNER',
						'conditions' => array(
							'Network.id = GarageNetwork.network_id',
							'Network.training =' => ConstantsBooleans::ACTIVE,
							'Network.credit IS NOT NULL',
							'Network.credit !=' => 0,
							'Network.credit !=' => '',
						),
					),
					array(
						'alias' => 'Garage',
						'table' => 'garages',
						'type' => 'INNER',
						'conditions' => array(
							'Garage.id = GarageNetwork.garage_id',
						),
					),
				),
				array(
					'conditions' => array(
						'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
					),
					'fields' => array(
						'Garage.name',
						'GarageNetwork.id',
					),
					'order' => array(
						'Garage.complete_name'
					),
					'group' => array(
						'GarageNetwork.id'
					),
				)
			)
		);
	}

	public function getNetworkNameByIdGarageAndTraining($garage_id, $isEdit = false)
	{
		if (!$isEdit) {
			$conditions = array(
				'GarageNetwork.garage_id' => $garage_id,
				'Network.training' => ConstantsBooleans::ACTIVE,
				'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
			);
		} else {
			$conditions = array(
				'GarageNetwork.garage_id' => $garage_id
			);
		}
		return $this->find(
			'list',
			array(
				'joins' => array(
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'INNER',
						'conditions' => array(
							'Network.id = GarageNetwork.network_id',
						),
					),
				),
				'conditions' => $conditions,
				'fields' => array(
					'Network.id',
					'Network.name',
				),
			)
		);
	}

	public function findAllByAnnexDetailId($annex_detail_id)
	{
		return $this->find(
			'all',
			array(
				'conditions' => array(
					'GarageNetwork.annex_detail_id' => $annex_detail_id,
				)
			)
		);
	}

	public function update_dealer_prices($garage_network_id, $dealer_type)
	{
		$fields = array(
			'GarageNetwork' => array(
				'dealer_discount',
				'dealer_markup',
				'dealer_surcharge',
				'modification_date'
			)
		);

		$garage_network = $this->findFirstById($garage_network_id);

		foreach ($dealer_type as $type => $value) {
			$garage_network['GarageNetwork']['dealer_' . $type] = $value;
		}

		$garage_network['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

		return $this->guardar($garage_network, $fields);
	}

	public function update_labour_dealer_prices($garage_network_id, $labour_type)
	{
		$fields = array(
			'GarageNetwork' => array(
				'labour_hourly_price',
				'labour_hourly_price_electric_vehicles',
				'modification_date'
			)
		);

		$garage_network = $this->findFirstById($garage_network_id);

		foreach ($labour_type as $type => $value) {
			$garage_network['GarageNetwork'][$type] = $value;
		}

		$garage_network['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

		return $this->guardar($garage_network, $fields);
	}

	public function findAllByLiveAndReceiveDate($aagRegionId)
	{
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Garage',
						'table' => 'garages',
						'type' => 'INNER',
						'conditions' => array(
							'Garage.id = GarageNetwork.garage_id',
						),
					),
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'INNER',
						'conditions' => array(
							'Network.id = GarageNetwork.network_id',
							'Network.id' => array(NETWORK_ID_AUTOCARE, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_TOPTRUCK, NETWORK_ID_GEXPERT),
						),
					),
				),
				'conditions' => array(
					'Garage.aag_region_id' => $aagRegionId,
					'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
					'GarageNetwork.contract_received_date IS NOT NULL',
				),
				'fields' => array(
					'GarageNetwork.id',
					'GarageNetwork.contract_received_date',
					'GarageNetwork.garage_id',
					'GarageNetwork.network_id',
				),
			)
		);
	}

	public function update_status_inactive_garage($garageId, $aagRegionId, $user)
	{
		$this->LogChange = ClassRegistry::init('LogChange');
		$this->LeavingReasonComment = ClassRegistry::init('LeavingReasonComment');
		$this->LeavingReasonType = ClassRegistry::init('LeavingReasonType');

		$fields = array(
			'GarageNetwork' => array(
				'status',
				'reason_leaving',
				'reason_leaving_id',
				'modification_date'
			)
		);

		$garageNetworks = $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Garage',
						'table' => 'garages',
						'type' => 'INNER',
						'conditions' => array(
							'Garage.id = GarageNetwork.garage_id',
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.garage_id' => $garageId,
					'Garage.aag_region_id' => $aagRegionId,
					'GarageNetwork.status !=' => ConstantsNetworksStatus::LEFT,
				),
			)
		);

		$inactive_reason_leaving_id = $this->LeavingReasonType->getInactiveIdByRegion($aagRegionId);

		foreach ($garageNetworks as $garageNetwork) {
			$oldData = array(
				'GarageNetwork' => array(
					'status' => $garageNetwork['GarageNetwork']['status'],
					'reason_leaving_id' => $garageNetwork['GarageNetwork']['reason_leaving_id'],
					'reason_leaving' => $garageNetwork['GarageNetwork']['reason_leaving']
				)
			);
			$garageNetwork['GarageNetwork']['status'] = ConstantsNetworksStatus::LEFT;
			$garageNetwork['GarageNetwork']['reason_leaving_id'] = $inactive_reason_leaving_id['LeavingReasonType']['id'] ?? null;
			$garageNetwork['GarageNetwork']['reason_leaving'] = null;
			$garageNetwork['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');
			$garageNetworkBd = $this->guardar($garageNetwork, $fields);

			$newData = array(
				'GarageNetwork' => array(
					'status' => $garageNetworkBd['GarageNetwork']['status'],
					'reason_leaving_id' => $garageNetworkBd['GarageNetwork']['reason_leaving_id'],
					'reason_leaving' => $garageNetworkBd['GarageNetwork']['reason_leaving']
				)
			);
			if ($oldData != $garageNetworkBd) {
				$this->LogChange->get_params_create_log_edit(
					$oldData['GarageNetwork'],
					$newData['GarageNetwork'],
					$this->table,
					$user,
					$garageNetwork['GarageNetwork']['garage_id'],
					ConstantsLogType::GARAGE
				);
			}
			$this->LeavingReasonComment->add($garageNetwork['GarageNetwork']['id'], __t('GarageNetwork.Garage_status_left'));
		}
	}

	public function getAllByNetworkIdAndQuotingActiveAndLast($network_id, $aag_region_id, $quoting_active, $last)
	{
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Garage',
						'table' => 'garages',
						'type' => 'INNER',
						'conditions' => array(
							'Garage.id = GarageNetwork.garage_id',
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.network_id' => $network_id,
					'GarageNetwork.quoting_active' => $quoting_active,
					'GarageNetwork.last' => $last,
					'Garage.aag_region_id' => $aag_region_id
				),
			)
		);
	}

	public function getAllByNetworkIdAndEnquiriesActiveAndLast($network_id, $aag_region_id, $enquiries_active, $last)
	{
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Garage',
						'table' => 'garages',
						'type' => 'INNER',
						'conditions' => array(
							'Garage.id = GarageNetwork.garage_id',
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.network_id' => $network_id,
					'GarageNetwork.enquiries_active' => $enquiries_active,
					'GarageNetwork.last' => $last,
					'Garage.aag_region_id' => $aag_region_id
				),
			)
		);
	}

	public function getGaragesOutsideInterval($networkId, $data, $is_electric = false)
	{
		if ($is_electric) {
			$conditionsLabour = array(
				'OR' => array(
					'GarageNetwork.labour_hourly_price_electric_vehicles <' => $data['min_labour_price_ev'],
					'GarageNetwork.labour_hourly_price_electric_vehicles >' => $data['max_labour_price'],
					'GarageNetworkWorkLabour.labour_hourly_price_electric_vehicles <' => $data['min_labour_price_ev'],
					'GarageNetworkWorkLabour.labour_hourly_price_electric_vehicles >' => $data['max_labour_price'],
				)
			);
		} else {
			$conditionsLabour = array(
				'OR' => array(
					'GarageNetwork.labour_hourly_price <' => $data['min_labour_price'],
					'GarageNetwork.labour_hourly_price >' => $data['max_labour_price'],
					'GarageNetworkWorkLabour.labour_hourly_price <' => $data['min_labour_price'],
					'GarageNetworkWorkLabour.labour_hourly_price >' => $data['max_labour_price'],
				)
			);
		}

		return $this->find(
			'list',
			array(
				'joins' => array(
					array(
						'alias' => 'Garage',
						'table' => 'garages',
						'type' => 'INNER',
						'conditions' => array(
							'Garage.id = GarageNetwork.garage_id',
						),
					),
					array(
						'alias' => 'GarageNetworkWorkLabour',
						'table' => 'garages_networks_works_labours',
						'type' => 'INNER',
						'conditions' => array(
							'GarageNetwork.id = GarageNetworkWorkLabour.garage_network_id',
						),
					),
				),
				'conditions' => array(
					'GarageNetwork.network_id' => $networkId,
					$conditionsLabour
				),
				'fields' => array(
					'Garage.name'
				)
			)
		);
	}

	public function findNetworkAndStatusByGarageIdAndAagRegionId($garageId, $aagRegionId)
	{
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'INNER',
						'conditions' => array(
							'Network.id = GarageNetwork.network_id',
						),
					),
				),
				'fields' => array(
					'Network.name',
					'Network.image',
					'GarageNetwork.status',
				),
				'conditions' => array(
					'GarageNetwork.garage_id' => $garageId,
					'Network.aag_region_id' => $aagRegionId
				)
			)
		);
	}

	/*
	 * Param 1: recommended network ID from first part of the view. Recommended action starts into the modal part from the outside button
	 * Param 2: recommended value associated to id from Param 1 (options value = true / false)
	 * Param 3: list of recommended networks ID like recommended_label from modal button is true
	*/
	public function setAllGaragesFromNetworkRecommended($internal_network_id, $recommended, $networkRecommendLabelIds, $network_id, $aag_region_id)
	{
		$this->Garage = ClassRegistry::init('Garage');

		$allGarageNetworks = $this->Garage->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'GarageNetwork',
						'table' => 'garages_networks',
						'type' => 'INNER',
						'conditions' => array(
							'Garage.id = GarageNetwork.garage_id',
							'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
							'GarageNetwork.network_id' => $network_id,
							'GarageNetwork.last' => ConstantsBooleans::YES,
						),
					),
					array(
						'alias' => 'GarageNetwork1',
						'table' => 'garages_networks',
						'type' => 'INNER',
						'conditions' => array(
							'Garage.id = GarageNetwork1.garage_id',
							'GarageNetwork1.status' => ConstantsNetworksStatus::LIVE,
							'GarageNetwork1.network_id' => $internal_network_id,
							'GarageNetwork1.last' => ConstantsBooleans::YES,
						),
					),
				),
				'conditions' => array(
					'Garage.status' => ConstantsGarageStatus::ACTIVE,
					'Garage.aag_region_id' => $aag_region_id
				),
				'fields' => array(
					'Garage.id',
					'GarageNetwork.id'
				)
			)
		);

		if (!$recommended) {	// Remove network if it has become false
			if (isset($networkRecommendLabelIds[$internal_network_id])) {
				unset($networkRecommendLabelIds[$internal_network_id]);
			}
			$networkRecommendLabelIds = array_keys($networkRecommendLabelIds);
		}

		foreach ($allGarageNetworks as $garageNetwork) {
			$this->updateGarageNetworkRecommended($garageNetwork, $recommended);	// Change garage_network recommended value like $recommended value

			if ($recommended) {
				// Change garage recommended_network value like $recommended value. Our goal is block the button if all network is recommended
				$this->Garage->updateGarageRecommendedNetwork($garageNetwork['Garage']['id'], $recommended);
			} else {
				$unrecommendedGarage = false;
				if (!empty($networkRecommendLabelIds)) {
					$this->findByGarageIdAndNetworkId($garageNetwork['Garage']['id'], $networkRecommendLabelIds);
					$garageNetworkRecommendedLabel = $this->findByGarageIdAndNetworkId($garageNetwork['Garage']['id'], $networkRecommendLabelIds);

					if (!$garageNetworkRecommendedLabel) {
						$unrecommendedGarage = true;
					}
				} else {
					$unrecommendedGarage = true;
				}
				if ($unrecommendedGarage) {	// If there are not any recommended networks, all recommended values form garagesNetworks will be false
					$this->Garage->updateGarageRecommendedNetwork($garageNetwork['Garage']['id'], ConstantsBooleans::NO);
				}
			}
		}
	}

	public function setAllGaragesNetworkRecommended($garage_id, $networks_id, $recommended)
	{
		$allGarageNetworks = $this->findAllByGarageIdAndNetworkIdAndStatus($garage_id, $networks_id, ConstantsNetworksStatus::LIVE);
		foreach ($allGarageNetworks as $garageNetwork) {
			if (!$this->updateGarageNetworkRecommended($garageNetwork, $recommended)) {
				return false;
			}
		}
		return true;
	}

	public function updateGarageNetworkRecommended($garageNetwork, $recommended)
	{
		$fields = array(
			'GarageNetwork' => array(
				'id',
				'recommended',
				'modification_date'
			)
		);
		$garageNetwork['GarageNetwork']['recommended'] = $recommended;
		$garageNetwork['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');
		return $this->guardar($garageNetwork, $fields);
	}

	public function updateGarageNetworkLoop($garageNetwork, $loop)
	{
		$fields = array(
			'GarageNetwork' => array(
				'id',
				'loop',
				'modification_date'
			)
		);

		$garageNetwork['GarageNetwork']['loop'] = $loop;
		$garageNetwork['GarageNetwork']['modification_date'] = date('Y-m-d H:i:s');

		return $this->guardar($garageNetwork, $fields);
	}

	public function getListOfGarageIdsByNetworkIds($networkIds)
	{
		return $this->find(
			'list',
			array(
				'conditions' => array(
					'GarageNetwork.network_id' => $networkIds,
					'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
				),
				'group' => array(
					'GarageNetwork.garage_id'
				),
				'fields' => array(
					'GarageNetwork.garage_id'
				)
			)
		);
	}

	public function getListofChildNetworksByGarageIdAndMainNetworkId($garageId, $mainNetworkId)
	{
		$childNetworks = $this->Network->getListofChildNetworks($mainNetworkId);
		$activeNetworks = $this->findActiveNetworksByGarage($garageId);

		$childNetworksOfGarage = array_intersect($childNetworks, $activeNetworks);

		if (!empty($childNetworksOfGarage)) {
			return $this->Network->find(
				'list',
				array(
					'conditions' => array(
						'Network.id' => $childNetworksOfGarage,
					),
					'fields' => array(
						'Network.id',
						'Network.name'
					)
				)
			);
		}
		return array();
	}

	public function garageBelongsToCvNetwork($garageId): bool
	{
		$allNetworksofGarage = $this->findActiveNetworksByGarage($garageId);
		$allNetworksofGarage = $this->Network->findAllById($allNetworksofGarage);
		foreach ($allNetworksofGarage as $network) {
			if ($network["Network"]["network_type"] == ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE) {
				return true;
			}
		}
		return false;
	}

	private function updateParentNetworkOnChildStatusChange($garage_network_bd, $network)
	{
		$editParentNetwork = false;
		if ($garage_network_bd['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE && $network["Network"]["network_type"] == ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE && !empty($network["Network"]["parent_network_id"])) {
			//Garage belongs to an LV network, and it is added to a CV network
			$networkIdToEdit = $network["Network"]["parent_network_id"];
			$quotingViews = ConstantsBooleans::NO_ACTIVE;
			$quotingActive = ConstantsBooleans::NO_ACTIVE;
			$editParentNetwork = true;
		} elseif ($garage_network_bd['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE && $this->Network->hasCvChildNetworks($network["Network"]["id"])) {
			//Garage belongs to a child CV network, and it is added to a parent LV network
			$networkIdToEdit = $network["Network"]["id"];
			$quotingViews = ConstantsBooleans::NO_ACTIVE;
			$quotingActive = ConstantsBooleans::NO_ACTIVE;
			$editParentNetwork = true;
		} elseif ($garage_network_bd['GarageNetwork']['status'] != ConstantsNetworksStatus::LIVE && $network["Network"]["network_type"] == ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE && !empty($network["Network"]["parent_network_id"]) && $this->Network->getNumberofChildNetworksByNetworkType($network["Network"]["parent_network_id"])) {
			//Garage belongs to an parent LV and child CV network, and it is removed from its last CV network: Garage will have quoting views enabled and quoting deactivated
			$networkIdToEdit = $network["Network"]["parent_network_id"];
			$quotingViews = ConstantsBooleans::ACTIVE;
			$quotingActive = ConstantsBooleans::NO_ACTIVE;
			$editParentNetwork = true;
		}

		if ($editParentNetwork) {
			$parentGarageNetwork = $this->findByGarageAndNetwork($garage_network_bd['GarageNetwork']['garage_id'], $networkIdToEdit);
			if (!empty($parentGarageNetwork)) {
				$parentGarageNetwork['GarageNetwork']['modification_date'] = date('Y-m-d');
				$parentGarageNetwork['GarageNetwork']['quoting_views_active'] = $quotingViews;
				$parentGarageNetwork['GarageNetwork']['quoting_active'] = $quotingActive;
				$fields = array(
					'GarageNetwork' => array(
						'modification_date',
						'quoting_views_active',
						'quoting_active'
					)
				);
				$this->commit();
				return $this->guardar($parentGarageNetwork, $fields);
			}
		}

		return true;
	}
}
