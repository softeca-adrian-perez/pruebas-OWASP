<?php
App::uses('Leadgen', 'Lib');
App::uses('Feefo', 'Lib');

class ReportingController extends AppController
{
	public $uses = array(
		"GarageNetwork",
		"Garage",
		"Network",
		"Enquiry",
		"Booking",
		"Enquiry",
		"Quotation",
		"Work",
		"ReviewRequest"
	);

	/**
	 * Reporting home page.
	 */
	public function home($id, $isNetwork = false)
	{
		if ($isNetwork) {
			$userRegion = CakeSession::read('Auth.User.aag_region_id');
			$garageNetworkId = null;
			$network_id = $id;
			$network = $this->Network->findByIdAndAagRegionId($network_id, $userRegion);

			if (
				in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::SUPER_ADMIN, ConstantsRoles::GARAGE)) ||
				!$network ||
				!$this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK) ||
				!$this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
			) {
				header('HTTP/1.0 401 Unauthorized');
				exit;
			}
		} else {
			$garageNetworkId = $id;
			$garageNetwork = $this->GarageNetwork->findById($id);
			if ($garageNetwork) {
				$network_id = isset($garageNetwork['GarageNetwork']['network_id']) ? $garageNetwork['GarageNetwork']['network_id'] : null;
				$garage_id = isset($garageNetwork['GarageNetwork']['garage_id']) ? $garageNetwork['GarageNetwork']['garage_id'] : null;
				$garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));

				if (!$garage) {
					header('HTTP/1.0 401 Unauthorized');
					exit;
				}

				$this->Acceso->checkGarageAccess($garage_id);
			} else {
				header('HTTP/1.0 401 Unauthorized');
				exit;
			}
		}

		// Filter data
		$searcher = $this->request->query;

		if (!isset($searcher['from']) && !isset($searcher['to'])) {
			$searcher['from'] = Fecha::toFormatoVista(date('Y-m-d', strtotime(date('Y-m-d') . ' -30 days')));
		}
		if (!isset($searcher['to'])) {
			$searcher['to'] = Fecha::toFormatoVista(date('Y-m-d'));
		}

		$searcher['network_id'] = $network_id;
		if (isset($garage_id)) {
			$searcher['garage_id'] = $garage_id;
		}
		$this->request->data['Search'] = $searcher;

		// Get data information
		$leadgen = new Leadgen();
		$dataMetricLabelsX = self::getDataMetricsLabelsX($searcher);
		$quotationsStatistics = $leadgen->getQuotationsStatistics($searcher, $dataMetricLabelsX['labelsX'], $dataMetricLabelsX['groupby']);

		$quotations = null;
		if (!empty($quotationsStatistics) && isset($quotationsStatistics['result'])) {
			$currency = $quotationsStatistics['result']['currency'] ?? '';
			$quotations = $quotationsStatistics['result']['totals'] ?? [];
			$datasets_data = $quotationsStatistics['result']['datasets_data'] ?? [];
			$labels = $quotationsStatistics['result']['labels'] ?? [];
		}

		$enquiries = $this->Enquiry->getTotalEnquiriesStatistics($searcher, $dataMetricLabelsX['labelsX'], $dataMetricLabelsX['groupby']);
		$bookingsWithQuotation = $this->Booking->getTotalBookingsStatistics($searcher, $dataMetricLabelsX['labelsX'], $dataMetricLabelsX['groupby']);
		$bookingsWithoutQuotation = $this->Booking->getTotalBookingsStatistics($searcher, $dataMetricLabelsX['labelsX'], $dataMetricLabelsX['groupby'], false);

		$dataMetrics = array(
			'quotations' => $quotations,
			'enquiries' => $enquiries,
			'bookingsWithQuotation' => $bookingsWithQuotation,
			'bookingsWithoutQuotation' => $bookingsWithoutQuotation,
		);

		$this->set(array(
			'isNetwork' => $isNetwork,
			'user_aag_region_id' => $this->Network->get_network_region($network_id),
			'network_name' => $network['Network']['name'] ?? null,
			'network_id' => $network_id,
			'garages_network' => $isNetwork ? $this->Garage->getGaragesByNetwork($network_id) : array(),
			'garage_id' => isset($this->request->data['Search']['garage_id']) ? $this->request->data['Search']['garage_id'] : false,
			'garage_name' => $isNetwork ? '' : $garage['Garage']['name'],
			'garage_network_id' => $garageNetworkId,
			'currency' => $currency ?? '',
			'totalQuotations' => array_sum($quotations ?? []),
			'datasets_data' => $datasets_data ?? [],
			'labels' => $labels ?? [],
			'totalEnquiries' => array_sum($enquiries),
			'totalBookingsWithQuotation' => array_sum($bookingsWithQuotation),
			'totalBookingsWithoutQuotation' => array_sum($bookingsWithoutQuotation),
			'has_child_networks' => $this->Network->hasChildNetworks($network_id),
            'child_networks' => $isNetwork ? $this->Network->getListofChildNetworksWithNames($network_id) : $this->GarageNetwork->getListofChildNetworksByGarageIdAndMainNetworkId($garage['Garage']['id'], $network_id),
			'metricLabels' => $dataMetricLabelsX['labelsX'],
			'metricsData' => self::getDataMetricsArray($dataMetrics),
			'titleText' => $dataMetricLabelsX['titleText'],
			'quoting_views_active' => $garageNetwork['GarageNetwork']['quoting_views_active'] ?? false
		));
	}

	private function getDataMetricsLabelsX($searcher)
	{
		$strtotimeFrom = strtotime($searcher['from']);
		$strtotimeTo = strtotime($searcher['to']);
		$anioFrom = date("Y", $strtotimeFrom);
		$monthFrom = date("m", $strtotimeFrom);
		$dayFrom = date("j", $strtotimeFrom);
		$anioTo = date("Y", $strtotimeTo);
		$monthTo = date("m", $strtotimeTo);
		$dayTo = date("j", $strtotimeTo);

		$dateFrom = new DateTime($searcher['from']);
		$dateTo = new DateTime($searcher['to']);
		$interval = $dateFrom->diff($dateTo);

		if ($interval->format("%y") > 0) { // Group by years
			$groupby = 'year';
			for ($i = $anioFrom; $i <= $anioTo; $i++) {
				$labelsX[] = $i;
			}
		} else {
			if ($interval->format("%m") > 0) { // Group by months
				if ($anioFrom !== $anioTo) {
					for ($i = $monthFrom; $i <= 12; $i++) {
						$labelsX[] = date('F', mktime(0, 0, 0, $i, 10));
					}
					for ($i = 1; $i <= $monthTo; $i++) {
						$labelsX[] = date('F', mktime(0, 0, 0, $i, 10));
					}
				} else {
					for ($i = $monthFrom; $i <= $monthTo; $i++) {
						$labelsX[] = date('F', mktime(0, 0, 0, $i, 10));
					}
				}
			} else { // Group by days
				$groupby = 'day';
				if ($monthFrom !== $monthTo) {
					$numDaysFrom = cal_days_in_month(CAL_GREGORIAN, $monthFrom, $anioFrom);
					for ($i = $dayFrom; $i <= $numDaysFrom; $i++) {
						$labelsX[] = $i;
					}
					for ($i = 1; $i <= $dayTo; $i++) {
						$labelsX[] = $i;
					}
					$titleText = $dayFrom . '-' . $numDaysFrom . ' ' . date("M", $strtotimeFrom) . '; 1-' . $dayTo . ' ' . date("M", $strtotimeTo);
				} else {
					for ($i = $dayFrom; $i <= $dayTo; $i++) {
						$labelsX[] = $i;
					}
				}
			}
		}
		return ['groupby' => $groupby ?? 'month', 'labelsX' => $labelsX ?? [], 'titleText' => $titleText ?? ''];
	}

	private function getDataMetricsArray($dataMetrics)
	{
		return array(
			[
				'label' => __t('Reporting.Quotations'),
				'data' => $dataMetrics['quotations'],
				'borderColor' => '#e6194b',
				'fill' => false,
			],
			[
				'label' => __t('Reporting.Enquiries'),
				'data' => $dataMetrics['enquiries'],
				'borderColor' => '#3cb44b',
				'fill' => false,
			],
			[
				'label' => __t('Reporting.BookingsWithQuotation'),
				'data' => $dataMetrics['bookingsWithQuotation'],
				'borderColor' => '#ffe119',
				'fill' => false,
			],
			[
				'label' => __t('Reporting.BookingsWithoutQuotation'),
				'data' => $dataMetrics['bookingsWithoutQuotation'],
				'borderColor' => '#4363d8',
				'fill' => false,
			],
		);
	}

	/**
	 * Download reporting data graph.
	 */
	public function downloadExcel($graphicType)
	{
		$config = CakeSession::read('Auth.User.Config');

		$this->set(array(
			'graphicType' => $graphicType,
			'datasets' => $this->request->query['datasets_data'],
			'labels' => $this->request->query['labels'],
			'config' => $config,
		));

		set_time_limit(18000);
		ini_set('memory_limit', '-1');

		$this->render('/Reporting/Elements/export_excel');
		$this->response->type('xlsx');
		$this->layout = false;
	}

	/**
	 * Reporting bookings tab.
	 * Only accessible from networks actions.
	 */
	public function bookings($networkId)
	{
		$user = $this->Acceso->user();
		$aag_region_id = $user['aag_region_id'];

		$network = $this->Network->findByIdAndAagRegionId($networkId, $aag_region_id);

		if (
			!$network ||
			in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::SUPER_ADMIN, ConstantsRoles::GARAGE)) ||
			!$this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK) ||
			!$this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
		) {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
		$searcher = $this->request->query;
		$this->request->data['Search'] = $searcher;
		$conditions = $this->Booking->conditions($searcher);
		$conditions[] = array('network_id' => $networkId);
		$conditions[] = array('Garage.aag_region_id' => $aag_region_id);
		if ($user['role_id'] == ConstantsRoles::GARAGE) {
			$conditions[] = array('Booking.garage_id' => $user['garage_id']);
			$searcher['garage_id'] = $user['garage_id'];
		}

		$bookings = $this->custom_pagination(
			$this->Booking->query('bookingsNetwork'),
			$conditions,
			ConstantsPagination::SIZE_PAGE_SMALL,
			'Booking'
		);

		$marketing_acceptance_types = array(
			ConstantsBooleans::NO => __t('General.No'),
			ConstantsBooleans::YES => __t('General.Yes')
		);

		$booking_status = array(
			ConstantsBookingsStatus::PENDING => __t('Booking.Pending'),
			ConstantsBookingsStatus::CANCELLED => __t('Booking.Cancelled'),
			ConstantsBookingsStatus::CONFIRMED => __t('Booking.Confirmed'),
			ConstantsBookingsStatus::COMPLETED => __t('Booking.Completed'),
			ConstantsBookingsStatus::EXPIRED => __t('Booking.Expired'),
		);

		$array_garage_name = array();
		if (!empty($searcher['garage_id']) || $user['role_id'] == ConstantsRoles::GARAGE) {
			$array_garage_name = $this->Garage->getGaragesNameByIdGarage($searcher['garage_id']);
		}

		$this->set(array(
			'userAagRegionId' => $this->Network->get_network_region($networkId),
			'network_name' => $network['Network']['name'] ?? null,
			'network_id' => $networkId,
			'garages_network' => $this->Garage->getGaragesByNetwork($networkId),
			'has_child_networks' => $this->Network->hasChildNetworks($networkId),
            'child_networks' => $this->Network->getListofChildNetworksWithNames($networkId),
			'garage_id' => isset($this->request->data['Search']['garage_id']) ? $this->request->data['Search']['garage_id'] : false,
			'bookings' => $bookings,
			'marketing_acceptance_types' => $marketing_acceptance_types,
			'booking_status' => $booking_status,
			'array_garage_name' => $array_garage_name,
		));
	}

	/**
	 * Reporting enquiries tab.
	 * Only accessible from networks actions.
	 */
	public function enquiries($networkId)
	{
		$user = $this->Acceso->user();
		$aag_region_id = $user['aag_region_id'];

		$network = $this->Network->findByIdAndAagRegionId($networkId, $aag_region_id);

		if (
			!$network ||
			in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::SUPER_ADMIN, ConstantsRoles::GARAGE)) ||
			!$this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK) ||
			!$this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
		) {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}

		$searcher = $this->request->query;
		$this->request->data['Search'] = $searcher;

		$conditions = $this->Enquiry->conditions($searcher);
		$conditions[] = array('network_id' => $networkId);

		$enquiries = $this->custom_pagination(
			$this->Enquiry->query('enquiriesNetwork'),
			$conditions,
			ConstantsPagination::SIZE_PAGE_SMALL,
			'Enquiry',
			null,
			'PaginatorOrderCustom'
		);

		$answeredTypes = array(
			ConstantsBooleans::NO => __t('General.No'),
			ConstantsBooleans::YES => __t('General.Yes')
		);

		$array_garage_name = array();
		if (!empty($searcher['garage_id'])) {
			$array_garage_name = $this->Garage->getGaragesNameByIdGarage($searcher['garage_id']);
		}

		$this->set(array(
			'user_aag_region_id' => $this->Network->get_network_region($networkId),
			'network_name' => $network['Network']['name'] ?? null,
			'network_id' => $networkId,
			'garages_network' => $this->Garage->getGaragesByNetwork($networkId),
			'has_child_networks' => $this->Network->hasChildNetworks($networkId),
            'child_networks' => $this->Network->getListofChildNetworksWithNames($networkId),
			'garage_id' => isset($this->request->data['Search']['garage_id']) ? $this->request->data['Search']['garage_id'] : false,
			'enquiries' => $enquiries,
			'answered_types' => $answeredTypes,
			'userAagRegionId' => $user['aag_region_id'],
			'array_garage_name' => $array_garage_name,
		));
	}

	/**
	 * Reporting and Bookings bookings status edit.
	 */
	public function booking_status_edit($bookingId, $network_id, $controller = null)
	{
		$user = $this->Acceso->user();
		$network = $this->Network->findByIdAndAagRegionId($network_id, $user['aag_region_id']);
		$booking = $this->Booking->findByIdAndNetworkId($bookingId, $network_id);

		$garageNetwork = $this->GarageNetwork->findByGarageAndNetwork($booking['Booking']['garage_id'], $network_id);
		$garageId = $garageNetwork['GarageNetwork']['garage_id'];
		$garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
		if (
			$network && $booking &&
			(
				$this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK) &&
				$this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS) &&
				!in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
			) ||
			(
				$garageNetwork && $garage && !$this->Acceso->checkGarageAccess($garageId)
			)
		) {

			if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::GARAGE) {
				$urlRedirect = array(
					'controller' => 'bookings',
					'action' => 'home',
					$garageNetwork['GarageNetwork']['id'],
				);
			} else {
				$urlRedirect = !empty($controller) && $controller == 'reporting' ?
					array(
						'controller' => 'reporting',
						'action' => 'bookings',
						$network_id
					) :
					array(
						'controller' => 'bookings',
						'action' => 'home',
						$garageNetwork['GarageNetwork']['id'],
					);
			}

			if (!$booking) {
				$this->Session->setFlashError(__t(ConstantsMessages::NO_PERMISSION));
				$this->redirect($urlRedirect);
			}

			if ($this->request->is('get')) {
				$this->request->data = $booking;
			} else {
				$this->request->data['User']['id'] = $bookingId;
				if ($this->Booking->edit($this->request->data)) {
					$bookingStatus = $this->request->data['Booking']['booking_status'];
					if (
						$network_id == NETWORK_ID_AGN &&
						($bookingStatus == ConstantsBookingsStatus::CONFIRMED ||
							$bookingStatus == ConstantsBookingsStatus::COMPLETED) &&
						!$this->ReviewRequest->hasRequestedReview($booking['Booking']['id'])
					) {
						$garage = $this->Garage->findById($booking['Booking']['garage_id']);
						$productSearchCode = $garage['Garage']['g_number_id'];
						$email = Texto::encryptDecryptText($booking['Booking']['customer_email'], false);
						$date = $booking['Booking']['date'];
						$name = Texto::encryptDecryptText($booking['Booking']['customer_name'], false);
						$description = $garage['Garage']['business_name'];
						$requestGuid = CakeText::uuid();
						$response = Feefo::requestReview($productSearchCode, $email, $date, $requestGuid, $name, $description);
						$request = array($productSearchCode, $email, $date, $name, $requestGuid, $description);
						$this->ReviewRequest->add(
							$booking['Booking']['id'],
							$garage['Garage']['aag_region_id'],
							json_encode($request),
							json_encode($response)
						);
						$msg = __t('Review.Request_successful');
						$this->Session->setFlashSuccess($msg);
						$this->redirect($urlRedirect);
					} elseif (
						$network_id == NETWORK_ID_AGN &&
						($bookingStatus == ConstantsBookingsStatus::PENDING || $bookingStatus == ConstantsBookingsStatus::CANCELLED) &&
						!$this->ReviewRequest->hasRequestedReview($booking['Booking']['id'])
					) {
						$this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
						$this->redirect($urlRedirect);
					} elseif ($network_id != NETWORK_ID_AGN) {
						$this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
						$this->redirect($urlRedirect);
					} else {
						$msg = __t('Review.Request_unsuccessful ');
						$this->Session->setFlashError($msg);
						$this->redirect($urlRedirect);
					}
					$this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
					$this->redirect(
						$urlRedirect
					);
				} else {
					$this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
				}
			}

			$this->setVarForm($network_id);
			$this->set(array(
				'booking_id' => $bookingId,
				'url_redirect' => $urlRedirect
			));
		} else {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
	}

	private function setVarForm($network_id)
	{
		$booking_status = array(
			ConstantsBookingsStatus::PENDING => __t('Booking.Pending'),
			ConstantsBookingsStatus::CANCELLED => __t('Booking.Cancelled'),
			ConstantsBookingsStatus::CONFIRMED => __t('Booking.Confirmed'),
			ConstantsBookingsStatus::COMPLETED => __t('Booking.Completed'),
		);

		$cancel_action = array(
			'url_cancel' => array(
				'controller' => 'reporting',
				'action' => 'bookings',
			),
		);
		$this->set(array(
			'cancel_action' => $cancel_action,
			'booking_status' => $booking_status,
			'network_id' => $network_id,
		));
	}

	/**
	 * Reporting marketing emails excel.
	 * Only accesible from networks actions.
	 */
	public function marketing_emails_excel($network_id)
	{
		$user = $this->Acceso->user();
		$aag_region_id = $user['aag_region_id'];

		$network = $this->Network->findByIdAndAagRegionId($network_id, $aag_region_id);

		if (
			CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
			!$network ||
			CakeSession::read('Auth.User.role_id') == ConstantsRoles::GARAGE ||
			!$this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK) ||
			!$this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
		) {
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}

		$enquiries = $this->Enquiry->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Garage',
						'table' => 'garages',
						'type' => 'LEFT',
						'conditions' => array(
							'Enquiry.garage_id = Garage.id'
						),
					),
					array(
						'alias' => 'City',
						'table' => 'cities',
						'type' => 'LEFT',
						'conditions' => array(
							'Garage.city_id = City.id',
						),
					),
					array(
						'alias' => 'Work',
						'table' => 'works',
						'type' => 'LEFT',
						'conditions' => array(
							'Enquiry.work_id = Work.id',
						),
					),
				),
				'conditions' => array(
					'Enquiry.marketing_acceptance =' . ConstantsBooleans::ACTIVE,
					'Enquiry.network_id =' . $network_id
				),
				'fields' => array(
					'Enquiry.name',
					'Enquiry.email',
					'Enquiry.plate',
					'Enquiry.mot_exp_date',
					'City.name',
					'Enquiry.creation_date',
					'Work.name_' . __l() . ' as name'
				),
			)
		);

		$bookings = $this->Booking->find(
			'all',
			array(
				'joins' => array(
					array(
						'alias' => 'Garage',
						'table' => 'garages',
						'type' => 'LEFT',
						'conditions' => array(
							'Booking.garage_id = Garage.id'
						),
					),
					array(
						'alias' => 'City',
						'table' => 'cities',
						'type' => 'LEFT',
						'conditions' => array(
							'Garage.city_id = City.id',
						),
					),
					array(
						'alias' => 'Work',
						'table' => 'works',
						'type' => 'LEFT',
						'conditions' => array(
							'Booking.work_id = Work.id',
						),
					),
				),
				'conditions' => array(
					'Booking.marketing_acceptance =' . ConstantsBooleans::ACTIVE,
					'Booking.network_id =' . $network_id
				),
				'fields' => array(
					'Booking.customer_name as name',
					'Booking.customer_email as email',
					'Booking.plate',
					'Booking.mot_exp_date',
					'City.name',
					'Booking.creation_date',
					'Work.name_' . __l() . ' as name'
				),
			)
		);

		$this->set(array(
			'enquiriesBookings' => array_merge($enquiries, $bookings)
		));

		$this->render('/Reporting/Elements/export_excel_marketing_emails');
		$this->response->type('.xlsx');
		$this->layout = false;
	}
}
