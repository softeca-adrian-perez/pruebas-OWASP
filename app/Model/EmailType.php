<?php

class EmailType extends AppModel
{
    public $useTable = 'email_types';

    /**
     * Get all EmailType.
     */
    public function getListEmailTypes()
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'active' => ConstantsBooleans::ACTIVE,
                ),
                'fields' => array(
                    'id',
                    'name_' . __l(),
                ),
                'order' => 'name_' . __l(),
            )
        );
    }

    /*
    * Generates a guid for the given email type.
    *
    * @param EmailType $emailType
    */
	public function generateGuid(array $emailType)
	{
		$fields = array(
			'EmailType' => array(
				'id',
				'guid'
			)
		);

		$emailType['EmailType']['guid'] = CakeText::uuid();
		return $this->guardar($emailType, $fields);
	}

	/**
	 * Generates a guid for every email type in DB without it.
	 */
	public function generateGuidForEveryEntranceWithoutIt()
	{
		$emailTypeWithoutGuid = $this->find('all', array(
			'conditions' => array(
				'EmailType.guid' => null
			)
		));

		foreach ($emailTypeWithoutGuid as $emailType) {
			$this->generateGuid($emailType);
		}
	}
    
	public function getListEmailTypesById($sendgrid_license_config_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'active' => ConstantsBooleans::ACTIVE,
                    'sendgrid_license_config_id' => $sendgrid_license_config_id,
                ),
                'fields' => array(
                    'id',
                ),
            )
        );
    }

    public function findAllByActiveAndSendgridLicensesConfigId() {
        return $this->find('all', [
            'conditions' => [
                'active' => ConstantsBooleans::ACTIVE,
                'sendgrid_license_config_id' => null
            ],
            'fields' => '*',
            'order' => ['name_' . __l() => 'ASC']
        ]);
    }

    public function deactivateByLicenseId($sendgrid_license_config_id)
    {
        $fieldsToUpdate = array('EmailType.active' => 0, 'EmailType.sendgrid_license_config_id' => null);
        $conditions = array('EmailType.sendgrid_license_config_id' => $sendgrid_license_config_id);

        return $this->updateAll($fieldsToUpdate, $conditions);
    }

}
