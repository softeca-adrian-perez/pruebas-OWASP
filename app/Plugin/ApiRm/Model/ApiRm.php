<?php
App::uses('HttpSocket', 'Network/Http');

class ApiRm extends AppModel
{
	public $useTable = false;

	/**
	 * Deactivate GMS in R&M.
	 */
	public static function transfer_network_to_repair($network)
	{
		if (!REPAIR_MAINTENANCE_SEND_DATA) {
			return;
		}

		$AagRegion = ClassRegistry::init("AagRegion");
		$aag_region = $AagRegion->findById($network['aag_region_id']);

		if (isset($aag_region['AagRegion']['url_rm'])) {
			$rmPath = $aag_region['AagRegion']['url_rm'] . ConstantesApiRmUrl::TRANSFER_NETWORK;
			$network_repair['Red']['nombre'] = $network['name'];
			$network_repair['Red']['red_id'] = $network['id'];
			$network_repair['Red']['pm_pais_id'] = $network['aag_region_id'] == ConstantsAAGRegionId::BENELUX ? ConstantsRepairCountryId::NETHERLANDS : ConstantsRepairCountryId::UK;
			$network_repair['Red']['es_toptruck'] = $network['network_type'] == 1 ?  0 : 1;

			if (!empty($rmPath)) {
				$httpSocket = new HttpSocket(
					array(
						'ssl_verify_peer' => false,
						'ssl_verify_host' => false,
						'ssl_allow_self_signed' => true,
						'ssl_verify_peer_name' => false,
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
							Texto::encryptDecryptText(REPAIR_MAINTENANCE_KEY, false)
						),
					),
					'Content-Type' => 'application/json',
					'User-Agent' => 'CakePHP'
				);

				try {
					$datos =  $network_repair;
					$datos = json_encode($datos);
					$httpSocket->post($rmPath, $datos, $headers);
				} catch (Exception $exception) {
					// catch
				}
			}
		} else {
			return;
		}
	}

	public static function delete_network_in_repair($network_id)
	{
		if (!REPAIR_MAINTENANCE_SEND_DATA) {
			return true;
		}

		$Network = ClassRegistry::init("Network");
		$AagRegion = ClassRegistry::init("AagRegion");
		$network = $Network->findById($network_id);
		$aag_region = $AagRegion->findById($network['Network']['aag_region_id']);

		if (isset($aag_region['AagRegion']['url_rm'])) {
			$rmPath = $aag_region['AagRegion']['url_rm'] . ConstantesApiRmUrl::DELETE_NETWORK;

			if (!empty($rmPath)) {
				$httpSocket = new HttpSocket(
					array(
						'ssl_verify_peer' => false,
						'ssl_verify_host' => false,
						'ssl_allow_self_signed' => true,
						'ssl_verify_peer_name' => false,
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
							Texto::encryptDecryptText(REPAIR_MAINTENANCE_KEY, false)
						),
					),
					'Content-Type' => 'application/json',
					'User-Agent' => 'CakePHP'
				);

				try {
					$datos = $network_id;
					$datos = json_encode($datos);
					$results = $httpSocket->post($rmPath, $datos, $headers);
					$results = json_decode($results, true);
					$results = $results['data'];
				} catch (Exception $exception) {
					$results = null;
				}
			}
			return $results;
		} else {
			return true;
		}
	}

	/**
	 * Get Agreements.
	 */
	public static function get_internal_agreements($garage_id = null)
	{
		if (!REPAIR_MAINTENANCE_SEND_DATA) {
			return array();
		}

		$Garage = ClassRegistry::init("Garage");
		$AagRegion = ClassRegistry::init("AagRegion");
		$garage = $Garage->findById($garage_id);

		if (isset($garage['Garage'])) {
			$aag_region = $AagRegion->findById($garage['Garage']['aag_region_id']);

			if (isset($aag_region['AagRegion']['url_rm'])) {
				$path = $aag_region['AagRegion']['url_rm'] . ConstantesApiRmUrl::GET_AGREEMENTS . "/" . $garage_id;

				if (!empty($path)) {
					$httpSocket = new HttpSocket(array(
						'ssl_verify_peer' => false,
						'ssl_verify_host' => false,
						'ssl_allow_self_signed' => true,
						'ssl_verify_peer_name' => false,
					));

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

					try {
						$results = $httpSocket->post($path, null, $headers);
						$results = json_decode($results['body'], true);

						$result = JWT::decode($results['data'], Texto::encryptDecryptText(REPAIR_MAINTENANCE_GNM_CONNECTION_KEY, false));
						$result = json_decode(json_encode($result), true);

						$ok = isset($result) ? $result : false;
					} catch (Exception $exception) {
						$ok = array();
					}
				}

				return $ok;
			} else {
				return array();
			}
		} elseif (!$garage_id) {
			$user = CakeSession::read('Auth.User');
			$aag_region = $AagRegion->findById($user['aag_region_id']);

			if (isset($aag_region['AagRegion']['url_rm'])) {
				$path = $aag_region['AagRegion']['url_rm'] . ConstantesApiRmUrl::GET_AGREEMENTS . "/" . $garage_id;

				if (!empty($path)) {
					$httpSocket = new HttpSocket(array(
						'ssl_verify_peer' => false,
						'ssl_verify_host' => false,
						'ssl_allow_self_signed' => true,
						'ssl_verify_peer_name' => false,
					));

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

					try {
						$results = $httpSocket->post($path, null, $headers);
						$results = json_decode($results['body'], true);

						$result = JWT::decode($results['data'], Texto::encryptDecryptText(REPAIR_MAINTENANCE_GNM_CONNECTION_KEY, false));
						$result = json_decode(json_encode($result), true);

						$ok = isset($result) ? $result : false;
					} catch (Exception $exception) {
						$ok = array();
					}
				}

				return $ok;
			} else {
				return array();
			}
		}
	}

	/**
	 * Set Agreement datain RM
	 */
	public static function set_data_agreement_AAG_RM($garage_agreement)
	{
		if (!REPAIR_MAINTENANCE_SEND_DATA) {
			return array();
		}

		$Garage = ClassRegistry::init("Garage");
		$AagRegion = ClassRegistry::init("AagRegion");
		$garage = $Garage->findById($garage_agreement['GarageAgreement']['garage_id']);
		$aag_region = $AagRegion->findById($garage['Garage']['aag_region_id']);

		if (isset($aag_region['AagRegion']['url_rm'])) {
			$path = $aag_region['AagRegion']['url_rm'] . ConstantesApiRmUrl::SET_DATA_AGREEMENT_AAG_RM;

			if (!empty($path)) {
				$httpSocket = new HttpSocket(array(
					'ssl_verify_peer' => false,
					'ssl_verify_host' => false,
					'ssl_allow_self_signed' => true,
					'ssl_verify_peer_name' => false,
				));

				$headers = array(
					'header' => array(
						'Authorization' =>
						'Bearer ' . JWT::encode(
							array(
								'id' => 1,
								'exp' => time() + (60 * 60) // expires in 1 minute
							),
							Texto::encryptDecryptText(REPAIR_MAINTENANCE_KEY, false)
						),
					),
					'Content-Type' => 'application/json',
					'User-Agent' => 'CakePHP'
				);

				try {
					$results = $httpSocket->post($path, $garage_agreement, $headers);
					$results = json_decode($results['body'], true);

					$result = JWT::decode($results['data'], Texto::encryptDecryptText(REPAIR_MAINTENANCE_KEY, false));
					$result = json_decode(json_encode($result), true);

					$ok = isset($result) ? $result : false;
				} catch (Exception $exception) {
					$ok = array();
				}
			}

			return $ok;
		} else {
			return array();
		}
	}

	public static function send_user_to_gnm($user)
	{
		if (!REPAIR_MAINTENANCE_SEND_DATA) {
			return json_decode(json_encode(array('success' => true)));
		}

		$AagRegion = ClassRegistry::init("AagRegion");
		$aag_region = $AagRegion->findById($user['User']['aag_region_id']);

		if ($aag_region['AagRegion']['url_rm']) {
			$rmPath = $aag_region['AagRegion']['url_rm'] . ConstantesApiRmUrl::NEW_RM_USER;

			if (!empty($rmPath)) {
				$httpSocket = new HttpSocket(
					array(
						'ssl_verify_peer' => false,
						'ssl_verify_host' => false,
						'ssl_allow_self_signed' => true,
						'ssl_verify_peer_name' => false,
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
							Texto::encryptDecryptText(REPAIR_MAINTENANCE_KEY, false)
						),
					),
					'Content-Type' => 'application/json',
					'User-Agent' => 'CakePHP'
				);

				try {
					$user = json_encode(base64_encode(Texto::encryptDecryptText(json_encode($user), true, REPAIR_MAINTENANCE_DATA_1, REPAIR_MAINTENANCE_DATA_2)));
					$data = JWT::encode($user, Texto::encryptDecryptText(REPAIR_MAINTENANCE_GNM_CONNECTION_KEY, false));

					$response = $httpSocket->post($rmPath, json_encode($data), $headers);
					return json_decode($response);
				} catch (Exception $exception) {
					// catch
				}
			}
		} else {
			return json_decode(json_encode(array('success' => true)));
		}
	}

	public static function change_active_user_rm($user_id, $activar, $delete_gnm_id = false)
	{
		if (!REPAIR_MAINTENANCE_SEND_DATA) {
			return json_decode(json_encode(array('success' => true)));
		}

		$User = ClassRegistry::init("User");
		$AagRegion = ClassRegistry::init("AagRegion");

		$user = $User->findById($user_id);

		if (!$user) {
			return json_decode(json_encode(array('success' => true)));
		}

		$aag_region = $AagRegion->findById($user['User']['aag_region_id']);

		if ($aag_region['AagRegion']['url_rm']) {
			$rmPath = $aag_region['AagRegion']['url_rm'] . ConstantesApiRmUrl::CHANGE_ACTIVE_RM_USER;

			if (!empty($rmPath)) {
				$httpSocket = new HttpSocket(
					array(
						'ssl_verify_peer' => false,
						'ssl_verify_host' => false,
						'ssl_allow_self_signed' => true,
						'ssl_verify_peer_name' => false,
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
							Texto::encryptDecryptText(REPAIR_MAINTENANCE_KEY, false)
						),
					),
					'Content-Type' => 'application/json',
					'User-Agent' => 'CakePHP'
				);

				try {
					$data = array(
						'user_id' => $user_id,
						'activar' => $activar,
						'delete_gnm_id' => $delete_gnm_id
					);

					$datos = json_encode($data);
					$response = $httpSocket->post($rmPath, $datos, $headers);
					return json_decode($response);
				} catch (Exception $exception) {
					// catch
				}
			}
		} else {
			return json_decode(json_encode(array('success' => true)));
		}
	}
}
