<?php

class SendGridEmailTypeTemplate extends AppModel
{
    public $useTable = 'sendgrid_email_types_templates';

    public $hasOne = array(
        'EmailType',
        'Country',
        'Language',
        'AagRegion'
    );

    public $validate = array(
        'sendgrid_template_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_fill_sendgrid_template_id',
            ),
        )
    );

    public function add($sendgridEmailTypeTemplate)
    {
        $fields = array(
            'SendGridEmailTypeTemplate' => array(
                'email_type_id',
                'sendgrid_template_id',
                'language_id',
                'platform_id',
                'country_id',
                'aag_region_id',
                'creation_date',
                'language_web_id',
                'modification_date'
            )
        );

        if (isset($sendgridEmailTypeTemplate['language_id']) && !empty($sendgridEmailTypeTemplate['language_id'])) {
            $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['language_id'] = $sendgridEmailTypeTemplate['language_id'];
            $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['language_web_id'] = null;
        } elseif (isset($sendgridEmailTypeTemplate['language_web_id']) && !empty($sendgridEmailTypeTemplate['language_web_id'])) {
            $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['language_web_id'] = $sendgridEmailTypeTemplate['language_web_id'];
            $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['language_id'] = null;
        } else {
            return false;
        }

        $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['email_type_id'] = $sendgridEmailTypeTemplate['email_type_id'];
        $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['sendgrid_template_id'] = $sendgridEmailTypeTemplate['sendgrid_template_id'];
        $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['platform_id'] = $sendgridEmailTypeTemplate['platform_id'];
        $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['country_id'] = $sendgridEmailTypeTemplate['country_id'];
        $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['aag_region_id'] = $sendgridEmailTypeTemplate['aag_region_id'];
        $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['creation_date'] = date('Y-m-d H:i:s');
        $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['modification_date'] = date('Y-m-d H:i:s');

        $this->create();

        $sendgridEmailTypeTemplateBd = $this->guardar($sendgridEmailTypeTemplate, $fields);
        if (!$sendgridEmailTypeTemplateBd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function edit($sendgridEmailTypeTemplate)
    {
        $fields = array(
            'SendGridEmailTypeTemplate' => array(
                'id',
                'sendgrid_template_id',
                'modification_date'
            )
        );

        $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['id'] = $sendgridEmailTypeTemplate['id'];
        $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['sendgrid_template_id'] = $sendgridEmailTypeTemplate['sendgrid_template_id'];
        $sendgridEmailTypeTemplate['SendGridEmailTypeTemplate']['modification_date'] = date('Y-m-d H:i:s');

        $sendgridEmailTypeTemplateBd = $this->guardar($sendgridEmailTypeTemplate, $fields);
        if (!$sendgridEmailTypeTemplateBd) {
            return false;
        }

        $this->commit();
        return true;
    }
}
