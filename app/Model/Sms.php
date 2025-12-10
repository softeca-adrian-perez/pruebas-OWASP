<?php
App::uses('MessagingSMS', 'Lib');

class Sms extends AppModel
{

    public $useTable = 'sms_licenses_config';

    public function add_sms_config($sms_config)
    {
        $sms_config['Sms']['username_iframe'] = Texto::encryptDecryptText($sms_config['Sms']['username_iframe'], true);
        $sms_config['Sms']['password_iframe'] = Texto::encryptDecryptText($sms_config['Sms']['password_iframe'], true);
        $sms_config['Sms']['license_iframe'] = Texto::encryptDecryptText($sms_config['Sms']['license_iframe'], true);
        $sms_config['Sms']['username_api'] = Texto::encryptDecryptText($sms_config['Sms']['username_api'], true);
        $sms_config['Sms']['password_api'] = Texto::encryptDecryptText($sms_config['Sms']['password_api'], true);
        $sms_config['Sms']['license_api'] = Texto::encryptDecryptText($sms_config['Sms']['license_api'], true);
        $sms_config['Sms']['aag_region_id'] = CakeSession::read('Auth.User.aag_region_id');

        $fields = array(
            'Sms' => array(
                'role_id',
                'username_iframe',
                'password_iframe',
                'license_iframe',
                'username_api',
                'password_api',
                'license_api',
                'aag_region_id',
                'country_id'
            )
        );

        $this->create();
        $sms_config_bd = $this->guardar($sms_config, $fields);
        if (!$sms_config_bd) {
            return false;
        }
        $this->commit();
        return $sms_config_bd;
    }

    public function edit_sms_config($sms_config)
    {
        $sms_config['Sms']['username_iframe'] = Texto::encryptDecryptText($sms_config['Sms']['username_iframe'], true);
        $sms_config['Sms']['password_iframe'] = Texto::encryptDecryptText($sms_config['Sms']['password_iframe'], true);
        $sms_config['Sms']['license_iframe'] = Texto::encryptDecryptText($sms_config['Sms']['license_iframe'], true);
        $sms_config['Sms']['username_api'] = Texto::encryptDecryptText($sms_config['Sms']['username_api'], true);
        $sms_config['Sms']['password_api'] = Texto::encryptDecryptText($sms_config['Sms']['password_api'], true);
        $sms_config['Sms']['license_api'] = Texto::encryptDecryptText($sms_config['Sms']['license_api'], true);
        $sms_config['Sms']['aag_region_id'] = CakeSession::read('Auth.User.aag_region_id');

        $fields = array(
            'Sms' => array(
                'id',
                'role_id',
                'username_iframe',
                'password_iframe',
                'license_iframe',
                'username_api',
                'password_api',
                'license_api',
                'aag_region_id',
                'country_id'
            )
        );

        return $this->guardar($sms_config['Sms'], $fields);
    }

    public function getLicensesByRegion($aag_region_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'ShortnerUrl',
                    'table' => 'shortner_url_api',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'ShortnerUrl.sms_license_config_id = Sms.id'
                    )
                )
            ),
            'conditions' => array(
                'Sms.aag_region_id' => $aag_region_id,
                'Sms.role_id' => 1
            ),
            'order' => array(
                'Sms.username_iframe'
            ),
            'fields' => array(
                'Sms.*',
                'ShortnerUrl.*',
            )
        ));
    }
}
