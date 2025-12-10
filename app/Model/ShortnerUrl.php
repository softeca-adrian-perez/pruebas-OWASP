<?php

class ShortnerUrl extends AppModel{
    public $useTable = 'shortner_url_api';

    public function add($url_config)
    {
        $url_config['ShortnerUrl']['expire_days'] = $url_config['ShortnerUrl']['expire_days'];
        $url_config['ShortnerUrl']['expire_at_views'] = $url_config['ShortnerUrl']['expire_at_views'];
		if ($url_config['ShortnerUrl']['domain'] == Configure::read('shortner_url_api.default_domain') || empty($url_config['ShortnerUrl']['domain'])) {
			$url_config['ShortnerUrl']['domain'] = Configure::read('shortner_url_api.default_domain');
			$url_config['ShortnerUrl']['api_key'] = SHORTNER_API_KEY;
		}else {
			$url_config['ShortnerUrl']['domain'] = $url_config['ShortnerUrl']['domain'];
			$url_config['ShortnerUrl']['api_key'] = Texto::encryptDecryptText($url_config['ShortnerUrl']['api_key'], true);
		}
		$url_config['ShortnerUrl']['creation_date'] = date('Y-m-d H:i:s');
		$url_config['ShortnerUrl']['modification_date'] = date('Y-m-d H:i:s');
		$url_config['ShortnerUrl']['user_id'] = CakeSession::read('Auth.User.id');
        $url_config['ShortnerUrl']['sms_license_config_id'] = $url_config['ShortnerUrl']['sms_license_config_id'];

        $fields = array(
			'ShortnerUrl' => array(
                'api_key',
				'expire_days',
				'expire_at_views',
				'domain',
				'creation_date',
				'modification_date',
				'user_id',
                'sms_license_config_id',
			)
		);

        $this->create();
        $url_config_bd = $this->guardar($url_config, $fields);
        if(!$url_config_bd){
            return false;
        }
        $this->commit();
		return $url_config_bd;
    }

    public function edit($url_config)
    {
        $url_config['ShortnerUrl']['expire_days'] = $url_config['ShortnerUrl']['expire_days'];
        $url_config['ShortnerUrl']['expire_at_views'] = $url_config['ShortnerUrl']['expire_at_views'];
        if ($url_config['ShortnerUrl']['domain'] == Configure::read('shortner_url_api.default_domain') || empty($url_config['ShortnerUrl']['domain'])) {
			$url_config['ShortnerUrl']['domain'] = Configure::read('shortner_url_api.default_domain');
			$url_config['ShortnerUrl']['api_key'] = SHORTNER_API_KEY;
		}else {
			$url_config['ShortnerUrl']['domain'] = $url_config['ShortnerUrl']['domain'];
			$url_config['ShortnerUrl']['api_key'] = Texto::encryptDecryptText($url_config['ShortnerUrl']['api_key'], true);
		}
		$url_config['ShortnerUrl']['modification_date'] = date('Y-m-d H:i:s');

        $fields = array(
			'ShortnerUrl' => array(
                'api_key',
				'expire_days',
				'expire_at_views',
				'domain',
				'modification_date',
			)
		);

		return $this->guardar($url_config, $fields);
    }

	public function getTokenByCountry($country) {
		return $this->find('first', array(
			'joins' => array(
				array(
					'alias' => 'Sms',
					'table' => 'sms_licenses_config',
					'type' => 'LEFT',
					'conditions' => array(
						'Sms.id = ShortnerUrl.sms_license_config_id'
					)
				),
			),
			'conditions' => array(
				'Sms.country_id' => $country
			),
			'field' => array(
				'ShortnerUrl.*'
				)
			)
		);
	}

	public function getShortnerByRegion($aag_region_id) {
        return $this->find('all',array(
			'joins' => array(
				array(
					'alias' => 'Sms',
					'table' => 'sms_licenses_config',
					'type' => 'LEFT',
					'conditions' => array(
						'Sms.id = ShortnerUrl.sms_license_config_id'
					)
				),
			),
            'conditions' => array(
                'Sms.aag_region_id' => $aag_region_id,
            ),
            'order' => array(
                'Sms.name'
            )
        ));
    }

	public function getCountAllBySmsId($sms_id){
        return $this->find('count',array(
                'conditions' => array(
                    'sms_license_config_id' => $sms_id,
                ),
            )
        );
    }

	public function getDomainByCountry($country_id) {
        return $this->find('first',array(
			'joins' => array(
				array(
					'alias' => 'Sms',
					'table' => 'sms_licenses_config',
					'type' => 'LEFT',
					'conditions' => array(
						'Sms.id = ShortnerUrl.sms_license_config_id'
					)
				),
			),
            'conditions' => array(
                'Sms.country_id' => $country_id,
            ),
            'order' => array(
                'ShortnerUrl.domain'
            )
        ));
    }

	public function shortnerByCountry($country) {
		return $this->find('first', array(
			'joins' => array(
				array(
					'alias' => 'Sms',
					'table' => 'sms_licenses_config',
					'type' => 'LEFT',
					'conditions' => array(
						'Sms.id = ShortnerUrl.sms_license_config_id'
					)
				),
			),
			'conditions' => array(
				'Sms.country_id' => $country
			),
			)
		);
	}

}