<?php

class UserStatistic extends AppModel
{

    public $useTable = 'users_statistics';

    public $validate = array(
        'user_name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'section_name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'article_name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'ip' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['network_id'])) {
            $conditions[] = $this->_conditionTradingGroup($fields['network_id']);
        }
        if (!empty($fields['trading_group_id'])) {
            $conditions[] = $this->_conditionNetwork($fields['trading_group_id']);
        }
        if (!empty($fields['from'])) {
            $conditions[] = $this->_conditionDateFrom($fields['from']);
        }
        if (!empty($fields['to'])) {
            $conditions[] = $this->_conditionDateTo($fields['to']);
        }

        return $conditions;
    }

    public function _conditionNetwork($network_id)
    {
        return array('UserStatistic.network_id' => $network_id);
    }

    public function _conditionTradingGroup($trading_group_id)
    {
        return array('UserStatistic.trading_group_id' => $trading_group_id);
    }

    private function _conditionDateFrom($from_date)
    {
        $from_date = Fecha::toFormatoBd($from_date);
        return array('Alert.creation_date >=' => $from_date);
    }

    private function _conditionDateTo($to_date)
    {
        $to_date = Fecha::toFormatoBd($to_date);
        return array('Alert.creation_date <=' => date('Y-m-d', strtotime($to_date . " +1 days")));
    }

    public function searchByUser($conditions)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.id = UserStatistic.user_id',
                    ),
                ),
            ),
            'conditions' => $conditions,
            'order' => array(
                'MAX(UserStatistic.date) DESC'
            ),
            'fields' => array(
                'UserStatistic.*',
                'User.name',
                'User.surname',
                'MAX(UserStatistic.date) AS most_recent'
            ),
            'group' => array(
                'User.id'
            ),
        ));
    }

    public function searchByIp($conditions)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.id = UserStatistic.user_id',
                    ),
                ),
            ),
            'conditions' => $conditions,
            'order' => array(
                'MAX(UserStatistic.date) DESC'
            ),
            'fields' => array(
                'UserStatistic.*',
                'MAX(UserStatistic.date) AS most_recent'
            ),
            'group' => array(
                'UserStatistic.ip'
            ),
        ));
    }

    public function getPieChartSections($trading_group_id, $network_id, $date_from, $date_to, $aagRegionId)
    {
        $conditions = array(
            'date >=' => Fecha::toFormatoBd($date_from),
            'date <=' => Fecha::toFormatoBd($date_to),
        );

        if ($trading_group_id != null) {
            $conditions[] = array(
                'trading_group_id' => $trading_group_id
            );
        }

        if ($network_id != null) {
            $conditions[] = array(
                'network_id' => $network_id
            );
        }
        $conditions[] = array('User.aag_region_id' => $aagRegionId);

        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.id = UserStatistic.user_id',
                    ),
                ),
            ),
            'conditions' => $conditions,
            'group' => array(
                'section_id'
            ),
            'order' => array(
                'section_name'
            )
        ));
    }
    public function countPieChartSections($section_id, $trading_group_id, $network_id, $date_from, $date_to, $aagRegionId)
    {
        $conditions = array(
            'section_id' => $section_id,
            'date >=' => Fecha::toFormatoBd($date_from),
            'date <=' => Fecha::toFormatoBd($date_to),
        );

        if ($trading_group_id != null) {
            $conditions[] = array(
                'trading_group_id' => $trading_group_id
            );
        }

        if ($network_id != null) {
            $conditions[] = array(
                'network_id' => $network_id
            );
        }
        $conditions[] = array('User.aag_region_id' => $aagRegionId);

        return $this->find('count', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.id = UserStatistic.user_id',
                    ),
                ),
            ),
            'conditions' => $conditions,
        ));
    }

    public function getPieChartArticles($trading_group_id, $network_id, $date_from, $date_to)
    {
        $conditions = array(
            'date >=' => Fecha::toFormatoBd($date_from),
            'date <=' => Fecha::toFormatoBd($date_to),
        );

        if ($trading_group_id != null) {
            $conditions[] = array(
                'trading_group_id' => $trading_group_id
            );
        }

        if ($network_id != null) {
            $conditions[] = array(
                'network_id' => $network_id
            );
        }

        return $this->find('all', array(
            'conditions' => $conditions,
            'group' => array(
                'article_id'
            ),
        ));
    }

    public function countPieChartArticles($article_id, $trading_group_id, $network_id, $date_from, $date_to)
    {
        $conditions = array(
            'article_id' => $article_id,
            'date >=' => Fecha::toFormatoBd($date_from),
            'date <=' => Fecha::toFormatoBd($date_to),
        );

        if ($trading_group_id != null) {
            $conditions[] = array(
                'trading_group_id' => $trading_group_id
            );
        }

        if ($network_id != null) {
            $conditions[] = array(
                'network_id' => $network_id
            );
        }

        return $this->find('count', array(
            'conditions' => $conditions
        ));
    }

    public function getAllByIPDateNetworkAndTradingGroup($ip, $date_from, $date_to, $network_id, $trading_group_id, $aagRegionId)
    {
        $conditions = array(
            'ip' => $ip,
            'date >=' => Fecha::toFormatoBd($date_from),
            'date <=' => Fecha::toFormatoBd($date_to),
            'section_id' => null,
            'article_id' => null,
        );

        if ($trading_group_id != null) {
            $conditions[] = array(
                'trading_group_id' => $trading_group_id
            );
        }

        if ($network_id != null) {
            $conditions[] = array(
                'network_id' => $network_id
            );
        }
        $conditions[] = array('User.aag_region_id' => $aagRegionId);

        return $this->find('count', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.id = UserStatistic.user_id',
                    ),
                ),
            ),
            'conditions' => $conditions,
        ));
    }

    public function getAllByUserIdDateNetworkAndTradingGroup($user_id, $date_from, $date_to, $network_id, $trading_group_id, $aagRegionId)
    {
        $conditions = array(
            'user_id' => $user_id,
            'date >=' => Fecha::toFormatoBd($date_from),
            'date <=' => Fecha::toFormatoBd($date_to),
            'section_id' => null,
            'article_id' => null,
        );

        if ($trading_group_id != null) {
            $conditions[] = array(
                'trading_group_id' => $trading_group_id
            );
        }

        if ($network_id != null) {
            $conditions[] = array(
                'network_id' => $network_id
            );
        }
        $conditions[] = array('User.aag_region_id' => $aagRegionId);

        return $this->find('count', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.id = UserStatistic.user_id',
                    ),
                ),
            ),
            'conditions' => $conditions
        ));
    }

    public function getAllByDateNetworkAndTradingGroup($date_from, $date_to, $network_id, $trading_group_id, $aagRegionId)
    {
        $conditions = array(
            'date >=' => Fecha::toFormatoBd($date_from),
            'date <=' => Fecha::toFormatoBd($date_to),
            'section_id' => null,
            'article_id' => null,
        );

        if ($trading_group_id != null) {
            $conditions[] = array(
                'trading_group_id' => $trading_group_id
            );
        }

        if ($network_id != null) {
            $conditions[] = array(
                'network_id' => $network_id
            );
        }
        $conditions[] = array('User.aag_region_id' => $aagRegionId);

        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.id = UserStatistic.user_id',
                    ),
                ),
            ),
            'conditions' => $conditions
        ));
    }
}
