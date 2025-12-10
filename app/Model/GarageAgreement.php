<?php

class GarageAgreement extends AppModel
{
	public $useTable = 'garages_agreements';

	public $hasMany = array(
		'Garage',
	);

	public $validate = array(
		'agreement_id' => array(
			array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Validation.Mandatory_to_choose_a_agreement',
			),
		),
		'status' => array(
			array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Validation.Mandatory_to_choose_a_status',
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
		'parent_acct' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'fleet_agreement' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'fleet_reference' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'agreement_code' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'garage_ref' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
				'message' => 'Validation.Name_is_too_long',
			),
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
	);

	public function createGarageAgreement($garage_json, $garage_id)
	{ //The data comes from a Json left on the server
		if (isset($garage_json['parentAcct']) &&  $garage_json['parentAcct']) {
			$garage_agreement_exist = $this->findByGarageIdAndParentAcctAndGarageRef($garage_id, $garage_json['parentAcct'], $garage_json['GarageRef']);

			if (!$garage_agreement_exist) {
				$garage_agreement_tmp = array(
					'GarageAgreement' => array(
						'garage_id' => $garage_id[''],
						'parent_acct' => $garage_json['parentAcct'],
						'fleet_agreement' => $garage_json['fleetAgreement'],
						'fleet_reference' => $garage_json['fleetReference'],
						'agreement_code' => str_replace($garage_json['parentAcct'], "", $garage_json['GarageRef']),
						'garage_ref' => $garage_json['GarageRef'],
						'creation_date' => date('Y-m-d H:i:s'),
					)
				);

				$this->create();
				if (!$this->save($garage_agreement_tmp)) {
					CakeLog::write('updates', 'Couldn\'t create agreement about the garage' . PHP_EOL);
				} else {
					//Mandamos los datos a R&M para guardarlos alli tb y se guardan si existe el taller en R&M
					$garage['GarageAgreement'] = $garage_agreement_tmp;
					$garage['GNumberId'] = $garage_json['Garage']['GarageNumberCRM'];

					$data = json_encode($garage);
					$httpSocket = new HttpSocket(
						array(
							'ssl_verify_peer' => false,
							'ssl_verify_host' => false,
							'ssl_allow_self_signed' => true,
						)
					);

					$headers = array(
						'header' => array(
							'Authorization' =>
							'Bearer ' . JWT::encode(
								array(
									'id' => 1,
									'exp' => time() + (60 * 60) // expires in 1 minute
								),
								Texto::encryptDecryptText(REPAIR_MAINTENANCE_GNM_CONNECTION_KEY, false)
							),
						),
						'Content-Type' => 'application/json',
						'User-Agent' => 'CakePHP'
					);

					if (REPAIR_MAINTENANCE_SEND_DATA) {
						$this->Garage = ClassRegistry::init("Garage");
						$this->AagRegion = ClassRegistry::init("AagRegion");
						$garage = $this->Garage->findById($garage_id);
						$aag_region = $this->AagRegion->findById($garage['Garage']['aag_region_id']);

						if (isset($aag_region['AagRegion']['url_rm'])) {
							$url =	$aag_region['AagRegion']['url_rm'] . Configure::read('repair-maintenance.url_garage_agreement');
							$httpSocket->post($url, $data, $headers);
						}
					}
				}
			}
		}

		return true;
	}

	public function edit_garage_agreement($data)
	{
		if (isset($data['GarageAgreement']['fleet_reference'])) {
			$fields = array(
				'GarageAgreement' => array(
					'id',
					'agreement_code',
					'fleet_reference',
					'parent_acct',
					'garage_ref',
				)
			);
		} else {
			$fields = array(
				'GarageAgreement' => array(
					'id',
					'status',
					'contract_sent_date',
					'contract_received_date',
					'contract_start_date',
					'contract_end_date',
					'reason_leaving_id',
					'leaving_date',
					'date_on_hold',
					'reason_hold_id',
				)
			);

			if ($data['GarageAgreement']['leaving_date']) {
				$data['GarageAgreement']['reason_leaving_id'] = $data['GarageAgreement']['reason'];
				$data['GarageAgreement']['leaving_date'] = Fecha::toFormatoBd($data['GarageAgreement']['leaving_date']);
				$data['GarageAgreement']['date_on_hold'] = null;
				$data['GarageAgreement']['reason_hold_id'] = null;
			} else if ($data['GarageAgreement']['date_on_hold']) {
				$data['GarageAgreement']['reason_hold_id'] = $data['GarageAgreement']['reason'];
				$data['GarageAgreement']['date_on_hold'] = Fecha::toFormatoBd($data['GarageAgreement']['date_on_hold']);
				$data['GarageAgreement']['leaving_date'] = null;
				$data['GarageAgreement']['reason_leaving_id'] = null;
			} else {
				$data['GarageAgreement']['date_on_hold'] = null;
				$data['GarageAgreement']['reason_hold_id'] = null;
				$data['GarageAgreement']['leaving_date'] = null;
				$data['GarageAgreement']['reason_leaving_id'] = null;
			}
			unset($data['GarageAgreement']['reason']);

			$data['GarageAgreement']['contract_sent_date'] = Fecha::toFormatoBd($data['GarageAgreement']['contract_sent_date']);
			$data['GarageAgreement']['contract_received_date'] = Fecha::toFormatoBd($data['GarageAgreement']['contract_received_date']);

			$data['GarageAgreement']['contract_start_date'] = Fecha::toFormatoBd($data['GarageAgreement']['contract_start_date']);
			$data['GarageAgreement']['contract_end_date'] = Fecha::toFormatoBd($data['GarageAgreement']['contract_end_date']);
		}

		$garage_agreement_bd = $this->guardar($data, $fields);
		if ($garage_agreement_bd) {

			if (isset($garage_agreement_bd['GarageAgreement']['garage_ref'])) {
				$garage_agreement = $this->findById($garage_agreement_bd['GarageAgreement']['id']);
				ApiRm::set_data_agreement_AAG_RM($garage_agreement);
			}
			$this->commit();
			return $garage_agreement_bd;
		} else {
			return false;
		}
	}

	public function findInternalByGarage($garage_id)
	{
		return $this->find(
			'all',
			array(
				'conditions' => array(
					'garage_id' => $garage_id,
					'agreement_id' => NULL,
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
						'alias' => 'Agreement',
						'table' => 'agreements',
						'type' => 'INNER',
						'conditions' => array(
							'Agreement.id = GarageAgreement.agreement_id',
						),
					),
				),
				'conditions' => array(
					'GarageAgreement.garage_id' => $garage_id,
				),
				'fields' => array(
					'GarageAgreement.*',
					'Agreement.nombre'
				)
			)
		);
	}

	public function get_internal_agreements($garage_id)
	{
		return $this->find(
			'all',
			array(
				'conditions' => array(
					'garage_id' => $garage_id,
					'agreement_id' => NULL,
				)
			)
		);
	}

	public function setGarageAgreementNotActive($old_agreement)
	{
		$fields = array(
			'GarageAgreement' => array(
				'agreement_id',
				// 'agreement_contract_type_id',
				// 'garage_number',
				'contract_sent_date',
				'contract_received_date',
				'contract_start_date',
				'contract_end_date',
				'reason_leaving_id',
				'status',
			)
		);
		$old_garage_agreement_bd = $this->guardar($old_agreement, $fields);
		if (!$old_garage_agreement_bd) {
			return false;
		}
		return $old_garage_agreement_bd;
	}

	public function add_garage_agreement($garage_agreement, $garage_id)
	{

		$fields = array(
			'GarageAgreement' => array(
				'garage_id',
				'agreement_id',
				'status',
				'contract_sent_date',
				'contract_received_date',
				'contract_start_date',
				'contract_end_date',
				// 'reason_leaving_id',
				'creation_date',
			)
		);

		if ($garage_agreement['GarageAgreement']['leaving_date']) {
			array_push($fields['GarageAgreement'], "leaving_date");
			array_push($fields['GarageAgreement'], "reason_leaving_id");
			$garage_agreement['GarageAgreement']['reason_leaving_id'] = $garage_agreement['GarageAgreement']['reason'];
			$garage_agreement['GarageAgreement']['leaving_date'] = Fecha::toFormatoBd($garage_agreement['GarageAgreement']['leaving_date']);
		} else if ($garage_agreement['GarageAgreement']['date_on_hold']) {
			array_push($fields['GarageAgreement'], "date_on_hold");
			array_push($fields['GarageAgreement'], "reason_hold_id");
			$garage_agreement['GarageAgreement']['reason_hold_id'] = $garage_agreement['GarageAgreement']['reason'];
			$garage_agreement['GarageAgreement']['date_on_hold'] = Fecha::toFormatoBd($garage_agreement['GarageAgreement']['date_on_hold']);
		} else {
			$garage_agreement['GarageAgreement']['date_on_hold'] = null;
			$garage_agreement['GarageAgreement']['reason_hold_id'] = null;
			$garage_agreement['GarageAgreement']['leaving_date'] = null;
			$garage_agreement['GarageAgreement']['reason_leaving_id'] = null;
		}
		unset($garage_agreement['GarageAgreement']['reason']);

		$garage_agreement['GarageAgreement']['contract_sent_date'] = Fecha::toFormatoBd($garage_agreement['GarageAgreement']['contract_sent_date']);
		$garage_agreement['GarageAgreement']['contract_received_date'] = Fecha::toFormatoBd($garage_agreement['GarageAgreement']['contract_received_date']);

		$garage_agreement['GarageAgreement']['contract_start_date'] = Fecha::toFormatoBd($garage_agreement['GarageAgreement']['contract_start_date']);
		$garage_agreement['GarageAgreement']['contract_end_date'] = Fecha::toFormatoBd($garage_agreement['GarageAgreement']['contract_end_date']);

		$garage_agreement['GarageAgreement']['garage_id'] = $garage_id;
		$garage_agreement['GarageAgreement']['creation_date'] = date('Y-m-d H:i:s');
		$this->create();

		$garage_agreement_bd = $this->guardar($garage_agreement, $fields);
		if (!$garage_agreement_bd) {
			return false;
		}

		$this->Garage = ClassRegistry::init("Garage");
		$this->Garage->edit_modification_date_garage($garage_id);

		$this->commit();
		return $garage_agreement_bd;
	}

	public function getDatasById($garage_agreement_id)
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
							'GarageAgreement.garage_id = Garage.id',
						),
					),
				),
				'conditions' => array(
					'GarageAgreement.id' => $garage_agreement_id,
				),
				'fields' => array(
					'GarageAgreement.*',
					'Garage.name'
				),
			)
		);
	}

	public function getData($garage_agreement_id)
	{
		return $this->find(
			'first',
			array(
				'conditions' => array(
					'GarageAgreement.id' => $garage_agreement_id,
				),
				'fields' => array(
					'id',
					'garage_id',
					'agreement_id',
					'status',
					'contract_sent_date',
					'contract_received_date',
					'contract_start_date',
					'contract_end_date',
					'reason_leaving_id',
					'reason_hold_id',
					'leaving_date',
					'date_on_hold'
				),
			)
		);
	}

	public function createGarageAgreementFromRM($garages, $acuerdo)
	{
		foreach ($garages as $garage) {
			$exist = $this->findByGarageIdAndAgreementCode($garage['PmTaller']['gnm_id'], $acuerdo['GaAcuerdo']['cod_alliance']);
			if (!$exist) {
				$garage_agreement_tmp = array(
					'GarageAgreement' => array(
						'garage_id' => $garage['PmTaller']['gnm_id'],
						'parent_acct' => null,
						'fleet_agreement' => $acuerdo['GaAcuerdo']['nombre'],
						'fleet_reference' => null,
						'agreement_code' => $acuerdo['GaAcuerdo']['cod_alliance'],
						'garage_ref' => null,
						'creation_date' => date('Y-m-d H:i:s'),
					)
				);
				$this->create();
				$this->validator()->remove('agreement_id');
				$this->validator()->remove('status');
				$this->save($garage_agreement_tmp);
			}
		}

		return true;
	}

	public function internal_agreements_list($aag_region_id)
	{
		return $this->find('list', array(
			'joins' => array(
				array(
					'alias' => 'Garage',
					'table' => 'garages',
					'type' => 'INNER',
					'conditions' => array(
						'GarageAgreement.garage_id = Garage.id',
						'Garage.aag_region_id' => $aag_region_id
					)
				),
			),
			'fields' => array(
				'agreement_code',
				'fleet_agreement'
			),
			'group' => 'agreement_code',
			'order' => array(
				'fleet_agreement'
			)
		));
	}

	/**
	 * Get internal agreement array.
	 */
	public function getInternalAgreementArray($agreements_rm, $agreements_alliance)
	{
		$rm_temp = $agreements_rm;
		$alliance_temp = $agreements_alliance;

		foreach ($agreements_rm as $key1 => $value1) {
			$rm_temp[$key1] = $value1;
			$existe_en_ambos = false;

			foreach ($agreements_alliance as $key2 => $value2) {
				if ($value1['GaAcuerdo']['cod_alliance'] == $value2['GarageAgreement']['agreement_code']) {
					$rm_temp[$key1]['GaAcuerdo']['parent_acct'] = $value2['GarageAgreement']['parent_acct'];
					$rm_temp[$key1]['GaAcuerdo']['fleet_reference'] = $value2['GarageAgreement']['fleet_reference'];
					$rm_temp[$key1]['GaAcuerdo']['garage_ref'] = $value2['GarageAgreement']['garage_ref'];
					$rm_temp[$key1]['GaAcuerdo']['id_agreement_alliance'] = $value2['GarageAgreement']['id'];
					$rm_temp[$key1]['GaAcuerdo']['fleet_agreement'] = $value2['GarageAgreement']['fleet_agreement'];
					$rm_temp[$key1]['GaAcuerdo']['agreement_code'] = $value2['GarageAgreement']['agreement_code'];

					$existe_en_ambos = true;
					unset($alliance_temp[$key2]);
				}
			}

			if (!$existe_en_ambos) {
				$rm_temp[$key1]['GaAcuerdo']['parent_acct'] = null;
				$rm_temp[$key1]['GaAcuerdo']['fleet_reference'] = null;
				$rm_temp[$key1]['GaAcuerdo']['garage_ref'] = null;
				$rm_temp[$key1]['GaAcuerdo']['fleet_agreement'] = $rm_temp[$key1]['GaAcuerdo']['nombre'];
				$rm_temp[$key1]['GaAcuerdo']['agreement_code'] = $rm_temp[$key1]['GaAcuerdo']['cod_alliance'];
			}
		}

		foreach ($alliance_temp as $key => $value) {
			$alliance_temp[$key]['GaAcuerdo'] = $alliance_temp[$key]['GarageAgreement'];
			$alliance_temp[$key]['GaAcuerdo']['id_agreement_alliance'] = $alliance_temp[$key]['GaAcuerdo']['id'];
			$alliance_temp[$key]['GaAcuerdoTaller']['fecha_creacion'] = $alliance_temp[$key]['GaAcuerdo']['contract_sent_date'];
			$alliance_temp[$key]['GaAcuerdoTaller']['pm_taller_id'] = $alliance_temp[$key]['GaAcuerdo']['garage_id'];
			$alliance_temp[$key]['GaAcuerdo']['fecha_inicio_vigencia'] = $alliance_temp[$key]['GaAcuerdo']['contract_start_date'];
			unset($alliance_temp[$key]['GarageAgreement']);
		}
		$rm_temp = array_merge($rm_temp, $alliance_temp);
		return $rm_temp;
	}
}
