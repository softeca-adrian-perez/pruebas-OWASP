<?php

class SmsTemplate extends AppModel {

    public $useTable = 'sms_templates';

    public $validate = array(
        'description' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_fill_a_description'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_SMS),
                'message' => 'Validation.Description_is_too_long',
            ),
        ),
        'sender' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_fill_a_description'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Sender_is_too_long',
            ),
        ),
        'country_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
            ),
        ),
    );

    public function getTemplateByRegion($aag_region_id)
    {
        return $this->find('all',array(
            'conditions' => array(
                'SmsTemplate.aag_region_id' => $aag_region_id
            ),
            'order' => array(
                'SmsTemplate.template_type_id'
            )
        ));
    }

	public function getCountriesByTemplateTypeId($aag_region_id, $template_type_id)
    {
		$this->Country = ClassRegistry::init("Country");
        return $this->Country->find('list', array(
			'conditions' => array(
				'Country.aag_region_id =' => $aag_region_id,
				'Country.id NOT IN (SELECT country_id from sms_templates where template_type_id = ' . $template_type_id .' and aag_region_id = ' . $aag_region_id . ')',
				'Country.id IN (SELECT country_id from sms_licenses_config where aag_region_id = ' . $aag_region_id .')'
			)
		));
    }

    public function add($sms_template)
    {
        $fields = array(
            'SmsTemplate' => array(
                'template_type_id',
                'description',
                'aag_region_id',
                'country_id',
                'sender'
            )
        );
        $this->create();
        return $this->guardar($sms_template['SmsTemplate'], $fields);
    }

    public function edit($sms_template)
    {
        $fields = array(
            'SmsTemplate' => array(
                'template_type_id',
                'description',
                'aag_region_id',
                'country_id',
                'sender'
            )
        );

        return $this->guardar($sms_template['SmsTemplate'], $fields);
    }
}
