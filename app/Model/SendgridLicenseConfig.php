<?php

class SendgridLicenseConfig extends AppModel
{
	public $useTable = 'sendgrid_licenses_config';

	public function add($data)
	{
		$fields = array(
			'SendgridLicenseConfig' => array(
				'api_key',
				'name',
				'from_email',
				'platform_id',
				'creation_date',
				'modification_date',
				'country_id',
				'aag_region_id',
				'guid'
			)
		);

		$data_tmp['SendgridLicenseConfig']['api_key'] = Texto::encryptDecryptText($data['SendgridLicenseConfig']['api_key'], true);
		$data_tmp['SendgridLicenseConfig']['name'] = $data['SendgridLicenseConfig']['name'];
		$data_tmp['SendgridLicenseConfig']['from_email'] = $data['SendgridLicenseConfig']['from_email'];
		$data_tmp['SendgridLicenseConfig']['platform_id'] = $data['SendgridLicenseConfig']['platform_id'];
		$data_tmp['SendgridLicenseConfig']['creation_date'] = date('Y-m-d H:i:s');
		$data_tmp['SendgridLicenseConfig']['modification_date'] = date('Y-m-d H:i:s');
		$data_tmp['SendgridLicenseConfig']['country_id'] = $data['SendgridLicenseConfig']['country_id'];
		$data_tmp['SendgridLicenseConfig']['aag_region_id'] = CakeSession::read('Auth.User.aag_region_id');
		$data_tmp['SendgridLicenseConfig']['guid'] = CakeText::uuid();

		$this->create();

		$sendgrid_license_config_bd = $this->guardar($data_tmp, $fields);
		if (!$sendgrid_license_config_bd) {
			return false;
		}

		$this->commit();
		return $sendgrid_license_config_bd;
	}

	public function edit($data)
	{
		$fields = array(
			'SendgridLicenseConfig' => array(
				'id',
				'api_key',
				'name',
				'from_email',
				'modification_date',
			)
		);

		$data_tmp['SendgridLicenseConfig']['id'] = $data['SendgridLicenseConfig']['id'];
		$data_tmp['SendgridLicenseConfig']['api_key'] = Texto::encryptDecryptText($data['SendgridLicenseConfig']['api_key'], true);
		$data_tmp['SendgridLicenseConfig']['name'] = $data['SendgridLicenseConfig']['name'];
		$data_tmp['SendgridLicenseConfig']['from_email'] = $data['SendgridLicenseConfig']['from_email'];
		$data_tmp['SendgridLicenseConfig']['modification_date'] = date('Y-m-d H:i:s');

		$sendgrid_license_config_bd = $this->guardar($data_tmp, $fields);
		if (!$sendgrid_license_config_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getConfigByAagRegion($aagRegionId, $platformId)
	{
		return $this->find('all', array(
			'joins' => array(
				array(
					'alias' => 'Country',
					'table' => 'countries',
					'type' => 'LEFT',
					'conditions' => array(
						'SendgridLicenseConfig.country_id = Country.id'
					)
				),
			),
			'conditions' => array(
				'SendgridLicenseConfig.aag_region_id' => $aagRegionId,
				'SendgridLicenseConfig.platform_id' => $platformId,
			),
			'order' => array(
				'SendgridLicenseConfig.id'
			),
			'fields' => array(
				'SendgridLicenseConfig.*',
				'Country.name',
			)
		));
	}

	/*
    * Generates a guid for the given sendrgrid license config.
    *
    * @param SendgridLicenseConfig $sendgridLicenseConfig
    */
	public function generateGuid(array $sendgridLicenseConfig)
	{
		$fields = array(
			'SendgridLicenseConfig' => array(
				'id',
				'guid'
			)
		);

		$sendgridLicenseConfig['SendgridLicenseConfig']['guid'] = CakeText::uuid();
		return $this->guardar($sendgridLicenseConfig, $fields);
	}

	/**
	 * Generates a guid for every sendrgrid license config in DB without it.
	 */
	public function generateGuidForEveryEntranceWithoutIt()
	{
		$sendgridLicenseConfigWithoutGuid = $this->find('all', array(
			'conditions' => array(
				'SendgridLicenseConfig.guid' => null
			)
		));

		foreach ($sendgridLicenseConfigWithoutGuid as $sendgridLicenseConfig) {
			$this->generateGuid($sendgridLicenseConfig);
		}
	}
}
