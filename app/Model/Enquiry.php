<?php
class Enquiry extends AppModel
{
    public $useTable = 'enquiries';

	public $validate = array(
		'name' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'email' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'description' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'answer' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
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
        'garage_url' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'phone' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'marketing_acceptance' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public function conditions($fields)
    {
        $conditions = array();

        if (isset($fields['name']) && !empty($fields['name'])) {
            $conditions[] = $this->conditionName($fields['name']);
        }
        if (!empty($fields['from'])) {
            $conditions[] = $this->conditionDateFrom($fields['from']);
        }
        if (!empty($fields['to'])) {
            $conditions[] = $this->conditionDateTo($fields['to']);
        }
        if (isset($fields['answered'])) {
            $conditions[] = $this->conditionAnswered($fields['answered']);
        }
        if (!empty($fields['network'])) {
            $conditions[] = $this->conditionNetwork($fields['network']);
        }
		if (!empty($fields['network_id'])) {
            $conditions[] = $this->conditionNetwork($fields['network_id']);
        }
		if (isset($fields['garage_id']) && !empty($fields['garage_id'])) {
            $conditions[] = $this->conditionGarageId($fields['garage_id']);
        }
        if (isset($fields['child_network_id']) && !empty($fields['child_network_id'])) {
            $conditions[] = $this->conditionsChildNetwork($fields['child_network_id']);
        }
        return $conditions;
    }

    private function conditionName($name)
    {
        return array('Enquiry.name LIKE' => '%' . $name . '%');
    }

    private function conditionDateFrom($fromDate)
    {
        $fromDate = Fecha::toFormatoBd($fromDate);
        return array('Enquiry.creation_date >=' => $fromDate);
    }

    private function conditionDateTo($toDate)
    {
        $toDate = Fecha::toFormatoBd($toDate);
        return array('Enquiry.creation_date <' => date('Y-m-d', strtotime($toDate . " +1 days")));
    }

    private function conditionAnswered($answered)
    {
        if ($answered == '1' || $answered == '0') {
            return array('Enquiry.answered' => $answered);
        }
    }

    private function conditionNetwork($networkId)
    {
        return array('Enquiry.network_id' => $networkId);
    }

	private function conditionGarageId($garageId)
    {
        return array('Enquiry.garage_id' => $garageId);
    }

    private function conditionsChildNetwork($childNetworkId)
    {
        return array('Enquiry.child_network_id' => $childNetworkId);
    }

    public function query($index)
    {
        return $this->queries[$index];
    }

    private $queries = array(
        'search' => array(
            'fields' => array(
                'Enquiry.*'
            ),
            'order' => 'Enquiry.creation_date desc',
        ),
		'enquiriesNetwork' => array(
			'joins' => array(
                array(
                    'table' => 'garages',
                    'alias' => 'Garage',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Enquiry.garage_id = Garage.id'
                    )
                ),
            ),
			'fields' => array(
				'Enquiry.*',
				'Garage.name'
			),
            'order' => 'Enquiry.id desc, Enquiry.name asc',
		)
    );

	public function getEnquiriesNetwork($conditions)
	{
		return $this->find('all', array(
			'joins' => array(
                array(
                    'table' => 'garages',
                    'alias' => 'Garage',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Enquiry.garage_id = Garage.id'
                    )
                ),
            ),
			'fields' => array(
				'Enquiry.*',
				'Garage.name'
			),
			'conditions' => $conditions,
			'order' => array(
				'Enquiry.creation_date' => 'desc',
				'Garage.name' => 'desc'
			)
		));
	}

	public function getTotalEnquiriesStatistics($data, $labelsX, $groupby)
	{
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
				$field . 'as time',
				'count(id) AS total',
			),
			'conditions' => $this->conditions($data),
			'group' => array(
				$groupby,
			),
		));

		$results = Hash::extract($results, '{n}.{n}');
		foreach ($labelsX as $labelX) {
			$key = array_search($labelX, array_column($results, 'time'));
			if ($key !== false) {
				$enquiries[] = array_column($results, 'total')[$key];
			} else {
				$enquiries[] = 0;
			}
		}
		return $enquiries ?? [];
	}

	public function getVariablesValues($sms_template_description, $enquiryId, $garage_name, $url_short)
    {
        $enquiry = $this->findById($enquiryId);
        $search = array(
            '$description.garage_name',
            '$description.date',
			'$description.time',
            '$description.customer_name',
            '$description.customer_email',
            '$description.customer_phone',
            '$description.enquiry_id',
            '$description.url',
        );
        $replace = [
            $garage_name,
            date(Fecha::_FORMATO_BD_FECHA, strtotime($enquiry['Enquiry']['creation_date'])),
			date('H:i', strtotime($enquiry['Enquiry']['creation_date'])),
            Texto::encryptDecryptText($enquiry['Enquiry']['name']),
            Texto::encryptDecryptText($enquiry['Enquiry']['email']),
            Texto::encryptDecryptText($enquiry['Enquiry']['phone']),
            $enquiry['Enquiry']['id'],
            $url_short,
        ];

        $sms_template_description = str_replace($search, $replace, $sms_template_description);
        return $sms_template_description;
    }
}
