<?php

class SendGridEmailTypeViewVar extends AppModel
{
    public $useTable = 'sendgrid_email_types_viewvars';

    public $hasOne = array(
        'EmailType',
        'SendGridViewVar'
    );

    /**
     * Get the SendGridViewVar filtering by email type ID and name (name or old_value).
     */
    public function getSendGridEmailTypeViewVarByEmailTypeAndName($emailTypeId, $name)
    {
        $sendgridEmailTypeViewVar = $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'SendGridViewVar',
                        'table' => 'sendgrid_viewvars',
                        'type' => 'INNER',
                        'conditions' => array(
                            'SendGridViewVar.id = SendGridEmailTypeViewVar.sendgrid_viewvar_id',
                        ),
                        'fields' => array('name')
                    ),
                ),
                'conditions' => array(
                    'SendGridEmailTypeViewVar.email_type_id' => $emailTypeId,
                    'OR' => array(
                        'SendGridViewVar.old_value LIKE' => '%' . $name . '%',
                        'SendGridViewVar.name LIKE' => $name,
                    )
                ),
                'fields' => array(
                    'SendGridViewVar.name'
                )
            )
        );

        return $sendgridEmailTypeViewVar;
    }

    /**
     * Get all the SendGridViewVar filtering by email type ID.
     */
    public function getAllSendGridEmailTypeViewVarsByEmailType($emailTypeId)
    {
        $sendGridEmailTypeViewVar = $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'SendGridViewVar',
                        'table' => 'sendgrid_viewvars',
                        'type' => 'INNER',
                        'conditions' => array(
                            'SendGridViewVar.id = SendGridEmailTypeViewVar.sendgrid_viewvar_id',
                        ),
                        'fields' => array('name')
                    ),
                ),
                'conditions' => array(
                    'SendGridEmailTypeViewVar.email_type_id' => $emailTypeId,
                ),
                'fields' => array(
                    'SendGridViewVar.name'
                )
            )
        );

        return $sendGridEmailTypeViewVar;
    }
}
