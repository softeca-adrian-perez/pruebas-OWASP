<?php

class ShortnerUrlLog extends AppModel
{
    public $useTable = 'shortner_url_log';

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['long_url'])) {
            $conditions[] = $this->_conditionLongUrl($fields['long_url']);
        }
        if (!empty($fields['short_url'])) {
            $conditions[] = $this->_conditionShortUrl($fields['short_url']);
        }
        if (!empty($fields['domain'])) {
            $conditions[] = $this->_conditionDomain($fields['domain']);
        }
        if (!empty($fields['short_id'])) {
            $conditions[] = $this->_conditionShortId($fields['short_id']);
        }
        if (!empty($fields['expire_days'])) {
            $conditions[] = $this->_conditionExpireDays($fields['expire_days']);
        }
        if (!empty($fields['expire_at_datetime_from'])) {
            $conditions[] = $this->_conditionExpireAtDatetimeFrom($fields['expire_at_datetime_from']);
        }
        if (!empty($fields['expire_at_datetime_to'])) {
            $conditions[] = $this->_conditionExpireAtDatetimeTo($fields['expire_at_datetime_to']);
        }
        if (!empty($fields['expire_at_views'])) {
            $conditions[] = $this->_conditionExpireAtViews($fields['expire_at_views']);
        }
        if (isset($fields['status']) && $fields['status'] !== '') {
            $conditions[] = $this->_conditionStatus($fields['status']);
        }
        if (!empty($fields['status_code'])) {
            $conditions[] = $this->_conditionStatusCode($fields['status_code']);
        }
        if (isset($fields['test_config']) && $fields['test_config'] !== '') {
            $conditions[] = $this->_conditionTestCondifg($fields['test_config']);
        }
        if (!empty($fields['creation_date_from'])) {
            $conditions[] = $this->_conditionCreationDateFrom($fields['creation_date_from']);
        }
        if (!empty($fields['creation_date_to'])) {
            $conditions[] = $this->_conditionCreationDateTo($fields['creation_date_to']);
        }

        return $conditions;
    }

    private function _conditionLongUrl($long_url)
    {
        return array('ShortnerUrlLog.long_url' => $long_url);
    }
    private function _conditionShortUrl($short_url)
    {
        return array('ShortnerUrlLog.short_url' => $short_url);
    }
    private function _conditionDomain($domain)
    {
        return array('ShortnerUrlLog.domain' => $domain);
    }
    private function _conditionShortId($short_id)
    {
        return array('ShortnerUrlLog.short_id' => $short_id);
    }
    private function _conditionExpireDays($expire_days)
    {
        return array('ShortnerUrlLog.expire_days' => $expire_days);
    }
    private function _conditionExpireAtDatetimeFrom($expire_at_datetime_from)
    {
        return array('ShortnerUrlLog.expire_at_datetime >=' => Fecha::toFormatoBd($expire_at_datetime_from));
    }
    private function _conditionExpireAtDatetimeTo($expire_at_datetime_to)
    {
        return array('ShortnerUrlLog.expire_at_datetime <=' => Fecha::toFormatoBd($expire_at_datetime_to));
    }
    private function _conditionExpireAtViews($expire_at_views)
    {
        return array('ShortnerUrlLog.expire_at_views' => $expire_at_views);
    }
    private function _conditionStatus($status)
    {
        if ($status == '1' or $status == '0') {
            return array('ShortnerUrlLog.status' => $status);
        }
    }
    private function _conditionStatusCode($status_code)
    {
        return array('ShortnerUrlLog.status_code' => $status_code);
    }
    private function _conditionTestCondifg($test_config)
    {
        if ($test_config == '1' or $test_config == '0') {
            return array('ShortnerUrlLog.test_config' => $test_config);
        }
    }
    private function _conditionCreationDateFrom($creation_date_from)
    {
        return array('ShortnerUrlLog.creation_date >=' => Fecha::toFormatoBd($creation_date_from));
    }
    private function _conditionCreationDateTo($creation_date_to)
    {
        return array('ShortnerUrlLog.creation_date <=' => Fecha::toFormatoBd($creation_date_to));
    }

    public function _query($aag_region_id)
    {
        $query = array(
            'joins' => array(
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Country.id = ShortnerUrlLog.country_id',
                        'Country.aag_region_id' => $aag_region_id
                    ),
                )
            ),
            'conditions' => array(
                'ShortnerUrlLog.test_config !=' => ConstantsBooleans::YES,
            ),
            'fields' => array(
                'ShortnerUrlLog.*',
            ),
            'order' => 'ShortnerUrlLog.id desc',
        );

        return $query;
    }

    public function getData()
    {
        return $this->find(
            'all',
            array(
                'order' => array(
                    'name' => 'asc'
                )
            )
        );
    }

    public function add($url_log, $data)
    {
        if (isset($url_log['body']) && !empty($url_log)) {
            $url_log['ShortnerUrlLog']['long_url'] = $url_log['body']['long_url'];
            $url_log['ShortnerUrlLog']['short_url'] = $url_log['body']['short_url'];
            $url_log['ShortnerUrlLog']['domain'] = $data['domain'];
            $url_log['ShortnerUrlLog']['short_id'] = $url_log['body']['short_id'];
            $url_log['ShortnerUrlLog']['expire_at_datetime'] = $url_log['body']['expire_at_datetime'];
            $url_log['ShortnerUrlLog']['expire_at_views'] = $url_log['body']['expire_at_views'];
            $url_log['ShortnerUrlLog']['creation_date'] = date('Y-m-d H:i:s');
            $url_log['ShortnerUrlLog']['status'] = ConstantsBooleans::ACTIVE;
            $url_log['ShortnerUrlLog']['status_code'] = $url_log['code'];
        } else {
            $url_log['ShortnerUrlLog']['long_url'] = $data['long_url'];
            $url_log['ShortnerUrlLog']['domain'] = $data['domain'];
            $url_log['ShortnerUrlLog']['expire_at_datetime'] = $data['expire_at_datetime'];
            $url_log['ShortnerUrlLog']['expire_at_views'] = $data['expire_at_views'];
            $url_log['ShortnerUrlLog']['creation_date'] = date('Y-m-d H:i:s');
            $url_log['ShortnerUrlLog']['status'] = ConstantsBooleans::NO_ACTIVE;
            $url_log['ShortnerUrlLog']['status_code'] = $url_log['code'];
        }

        if (isset($url_log['test_config']) && !empty($url_log['test_config'])) {
            $url_log['ShortnerUrlLog']['test_config'] = true;
        }

        if (isset($data['expire_at_datetime']) && !empty($data['expire_at_datetime'])) {
            $url_log['ShortnerUrlLog']['expire_days'] = $data['expire_at_datetime'];
        }

        if (isset($data['country_id']) && !empty($data['country_id'])) {
            $url_log['ShortnerUrlLog']['country_id'] = $data['country_id'];
        }

        $fields = array(
            'ShortnerUrlLog' => array(
                'long_url',
                'short_url',
                'domain',
                'short_id',
                'expire_days',
                'expire_at_datetime',
                'expire_at_views',
                'creation_date',
                'status',
                'status_code',
                'test_config',
                'country_id',
            )
        );

        $this->create();
        $url_log_bd = $this->guardar($url_log, $fields);
        if (!$url_log_bd) {
            return false;
        }
        $this->commit();
        return $url_log_bd;
    }

    public function deleteAllOver30Days()
    {
        $date = date('Y-m-d', strtotime('-30 days'));

        return $this->deleteAll(array(
            'ShortnerUrlLog.creation_date <' => $date
        ));
    }
}
