<?php
class Booking extends AppModel
{
    public $useTable = 'bookings';

    public $validate = array(
        'quotation_id' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'customer_name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'customer_phone' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'customer_email' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'plate' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'brand' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'model' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'version' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'fuel' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'additional_info' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();

        if (isset($fields['customer_name']) && !empty($fields['customer_name'])) {
            $conditions[] = $this->conditionCustomerName($fields['customer_name']);
        }
        if (!empty($fields['from'])) {
            $conditions[] = $this->conditionDateFrom($fields['from']);
        }
        if (!empty($fields['to'])) {
            $conditions[] = $this->conditionDateTo($fields['to']);
        }
        if (!empty($fields['creation_date_from'])) {
            $conditions[] = $this->conditionDateCreationFrom($fields['creation_date_from']);
        }
        if (!empty($fields['creation_date_to'])) {
            $conditions[] = $this->conditionDateCreationTo($fields['creation_date_to']);
        }
        if (!empty($fields['quotation_id'])) {
            $conditions[] = $this->conditionQuotationId($fields['quotation_id']);
        }
        if (!empty($fields['network_id'])) {
            $conditions[] = $this->conditionNetwork($fields['network_id']);
        }
        if (isset($fields['garage_id']) && !empty($fields['garage_id'])) {
            $conditions[] = $this->conditionGarageId($fields['garage_id']);
        }
        if (isset($fields['marketing_acceptance'])) {
            $conditions[] = $this->conditionMarketingAcceptance($fields['marketing_acceptance']);
        }
        if (isset($fields['booking_status'])) {
            $conditions[] = $this->conditionsBookingStatus($fields['booking_status']);
        }
        if (isset($fields['child_network_id']) && !empty($fields['child_network_id'])) {
            $conditions[] = $this->conditionsChildNetwork($fields['child_network_id']);
        }
        return $conditions;
    }

    private function conditionCustomerName($customerName)
    {
        return array('Booking.customer_name LIKE' => '%' . $customerName . '%');
    }

    private function conditionDateFrom($fromDate)
    {
        $fromDate = Fecha::toFormatoBd($fromDate);
        return array('Booking.date >=' => $fromDate);
    }

    private function conditionDateTo($toDate)
    {
        $toDate = Fecha::toFormatoBd($toDate);
        return array('Booking.date <=' => date('Y-m-d', strtotime($toDate)));
    }

    private function conditionDateCreationFrom($creationDateFrom)
    {
        $creationDateFrom = Fecha::toFormatoBd($creationDateFrom);
        return array('Booking.creation_date >=' => $creationDateFrom);
    }

    private function conditionDateCreationTo($creationDateTo)
    {
        $creationDateTo = Fecha::toFormatoBd($creationDateTo);
        return array('Booking.creation_date <' => date('Y-m-d', strtotime($creationDateTo . " +1 days")));
    }

    private function conditionQuotationId($quotationId)
    {
        return array('Booking.quotation_id LIKE' => '%' . $quotationId . '%');
    }

    private function conditionNetwork($networkId)
    {
        return array('Booking.network_id' => $networkId);
    }

    private function conditionGarageId($garageId)
    {
        return array('Booking.garage_id' => $garageId);
    }

    private function conditionMarketingAcceptance($marketingAcceptance)
    {
        if ($marketingAcceptance == '1' || $marketingAcceptance == '0') {
            return array('Booking.marketing_acceptance' => $marketingAcceptance);
        }
    }

    private function conditionsBookingStatus($bookingStatus)
    {
        if ($bookingStatus != '') {
            return array('Booking.booking_status' => $bookingStatus);
        }
    }

    private function conditionsChildNetwork($childNetworkId)
    {
        return array('Booking.child_network_id' => $childNetworkId);
    }

    public function query($index)
    {
        return $this->queries[$index];
    }

    private $queries = array(
        'search' => array(
            'fields' => array(
                'Booking.*'
            ),
            'order' => 'Booking.date desc',
        ),
        'bookingsNetwork' => array(
            'joins' => array(
                array(
                    'table' => 'garages',
                    'alias' => 'Garage',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Booking.garage_id = Garage.id'
                    )
                ),
            ),
			'fields' => array(
				'Booking.*',
				'Garage.name'
			),
			'order' => 'Booking.date desc, Garage.name asc'
		)
    );

    public function getBookingsNetwork($conditions)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'table' => 'garages',
                    'alias' => 'Garage',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Booking.garage_id = Garage.id'
                    )
                ),
            ),
            'fields' => array(
                'Booking.*',
                'Garage.name'
            ),
            'conditions' => $conditions,
            'order' => array(
                'Booking.date' => 'desc',
                'Garage.name' => 'desc'
            )
        ));
    }

    public function getTotalBookingsStatistics($data, $labelsX, $groupby, $hasQuotation = true)
    {
        // Changes values to date filters
        $data['creation_date_from'] = $data['from'];
        $data['creation_date_to'] = $data['to'];
        unset($data['from']);
        unset($data['to']);

        $conditions = array();
        $conditions[] = $this->conditions($data);
        $conditions[] = $hasQuotation ? array('quotation_id IS NOT NULL') : array('quotation_id IS NULL');

        if ($groupby == 'day') {
            $field = $groupby = 'DAY(creation_date)';
        } elseif ($groupby == 'month') {
            $field = 'MONTHNAME(creation_date)';
            $groupby = 'MONTH(creation_date)';
        } else {
            $field = $groupby = 'YEAR(creation_date)';
        }

        $results = $this->find('all', array(
            'fields' => array(
                $field . ' as time',
                'count(id) AS total',
            ),
            'conditions' => $conditions,
            'group' => array(
                $groupby,
            ),
        ));

        $results = Hash::extract($results, '{n}.{n}');
        foreach ($labelsX as $labelX) {
            $key = array_search($labelX, array_column($results, 'time'));
            if ($key !== false) {
                $bookings[] = array_column($results, 'total')[$key];
            } else {
                $bookings[] = 0;
            }
        }
        return $bookings ?? [];
    }

    public function edit($booking)
    {
        $fields = array(
            'Booking' => array(
                'id',
                'booking_status',
            )
        );

        return $this->guardar($booking, $fields);
    }

    public function getBookingsPending()
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'Booking.booking_status' => ConstantsBookingsStatus::PENDING,
                ),
            )
        );
    }

    public function getBookingsPendingOutOfDate()
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'Booking.booking_status' => ConstantsBookingsStatus::PENDING,
                    'Booking.date <' => date("y-m-d")
                ),
            )
        );
    }

    public function changeBookingStatus($booking, $newStatus)
    {
        $fields = array(
            'Booking' => array(
                'id',
                'booking_status',
            )
        );

        $booking['Booking']['booking_status'] = $newStatus;

        $bookingBd = $this->guardar($booking, $fields);
        if (!$bookingBd) {
            return false;
        }

        $this->commit();
        return $bookingBd;
    }

    public function getVariablesValues($sms_template_description, $bookingId, $garage_name, $network_name, $work_name, $url_short)
    {
        $booking = $this->findById($bookingId);
        $search = array(
            '$description.garage_name',
            '$description.date',
            '$description.time',
            '$description.Time_to',
            '$description.plate',
            '$description.customer_name',
            '$description.customer_email',
            '$description.customer_phone',
            '$description.booking_id',
            '$description.network_name',
            '$description.work_name',
            '$description.url',
        );
        $replace = [
            $garage_name,
            $booking['Booking']['date'],
            date('H:i', strtotime($booking['Booking']['time'])),
            date('H:i', strtotime($booking['Booking']['time_to'])),
            Texto::encryptDecryptText($booking['Booking']['plate']),
            Texto::encryptDecryptText($booking['Booking']['customer_name']),
            Texto::encryptDecryptText($booking['Booking']['customer_email']),
            Texto::encryptDecryptText($booking['Booking']['customer_phone']),
            $booking['Booking']['id'],
            $network_name,
            $work_name,
            $url_short
        ];

        $sms_template_description = str_replace($search, $replace, $sms_template_description);
        return $sms_template_description;
    }
}
