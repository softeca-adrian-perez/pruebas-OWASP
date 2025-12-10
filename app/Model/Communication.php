<?php

class Communication extends AppModel
{
    public $useTable = 'communications';

    public $hasAndBelongsToMany = array(
        'User' => array(
            'joinTable' => 'communications_users',
            'foreignKey' => 'communication_id',
            'associationForeignKey' => 'user_id',
            'with' => 'CommunicationUser',
        ),
    );

    public $validate = array(
        'title' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_title',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'subtitle' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'url' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'body' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'image' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_logo'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'start_date' =>  array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_start_date',
        ),
        'communication_section_id' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_section',
        ),
        'section_subsection_id' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_subsection',
        ),
        'start_date' => array(
            'rule' => 'date',
            'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'end_date' => array(
            'rule' => 'date',
            'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
    );

    private $_queries = array(
        'search_maintenance' => array(
            'joins' => array(
                array(
                    'alias' => 'CommunicationNetwork',
                    'table' => 'communications_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CommunicationNetwork.communication_id = Communication.id',
                    ),
                ),
                array(
                    'alias' => 'CommunicationSection',
                    'table' => 'communications_sections',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.communication_section_id = CommunicationSection.id',
                    ),
                ),
            ),
            'fields' => array(
                'Communication.*',
                'group_concat(distinct CommunicationNetwork.network_id separator ",") as Networks',
            ),
            'group' => array(
                'Communication.id',
            ),
            'order' => 'Communication.start_date desc , Communication.title asc'
        ),
        'getCommunicationsByNetworksAndSection' => array(
            'joins' => array(
                array(
                    'alias' => 'CommunicationSection',
                    'table' => 'communications_sections',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.communication_section_id = CommunicationSection.id',
                    ),
                ),
                array(
                    'alias' => 'CommunicationNetwork',
                    'table' => 'communications_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CommunicationNetwork.communication_id = Communication.id',
                    ),
                ),
            ),
            'conditions' => array(
                'Communication.active' => ConstantsBooleans::ACTIVE,
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'fields' => array(
                'Communication.*',
                'CommunicationSection.*',
            ),
            'group' => array(
                'Communication.id',
            )
        ),
        'getCommunicationsSearchPage2ByNetworks' => array(
            'joins' => array(
                array(
                    'alias' => 'CommunicationNetwork',
                    'table' => 'communications_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CommunicationNetwork.communication_id = Communication.id',
                    ),
                ),
            ),
            'conditions' => array(
                'Communication.active' => ConstantsBooleans::ACTIVE,
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'fields' => array(
                'Communication.*',
            ),
            'group' => array(
                'Communication.id',
            )
        ),
        'getCommunicationsByTradingGroupAndSection' => array(
            'joins' => array(
                array(
                    'alias' => 'CommunicationSection',
                    'table' => 'communications_sections',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.communication_section_id = CommunicationSection.id',
                    ),
                ),
                array(
                    'alias' => 'CommunicationTradingGroup',
                    'table' => 'communications_trading_groups',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CommunicationTradingGroup.communication_id = Communication.id',
                    ),
                ),
            ),
            'conditions' => array(
                'Communication.active' => ConstantsBooleans::ACTIVE,
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'fields' => array(
                'Communication.*',
                'CommunicationSection.*',
            ),
            'group' => array(
                'Communication.id',
            )
        ),
        'getCommunicationsSearchPage2ByTradingGroup' => array(
            'joins' => array(
                array(
                    'alias' => 'CommunicationTradingGroup',
                    'table' => 'communications_trading_groups',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CommunicationTradingGroup.communication_id = Communication.id',
                    ),
                ),
            ),
            'conditions' => array(
                'Communication.active' => ConstantsBooleans::ACTIVE,
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'fields' => array(
                'Communication.*',
            ),
            'group' => array(
                'Communication.id',
            )
        )
    );


    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['title'])) {
            $conditions[] = $this->_conditionTitle($fields['title']);
        }
        if (!empty($fields['network'])) {
            $conditions[] = $this->_conditionNetwork($fields['network']);
        }
        if (!empty($fields['distributor_network'])) {
            $conditions[] = $this->_conditionDistributorNetwork($fields['distributor_network']);
        }
        if (!empty($fields['trading_group'])) {
            $conditions[] = $this->_conditionTradingGroup($fields['trading_group']);
        }
        if (!empty($fields['activity'])) {
            $conditions[] = $this->_conditionActivity($fields['activity']);
        }
        if (!empty($fields['type'])) {
            $conditions[] = $this->_conditionType($fields['type']);
        }
        if (!empty($fields['sub_type'])) {
            $conditions[] = $this->_conditionSubType($fields['sub_type']);
        }
        if (isset($fields['active'])) {
            $conditions[] = $this->_conditionActive($fields['active']);
        }
        if (isset($fields['is_popup'])) {
            $conditions[] = $this->_conditionIsPopup($fields['is_popup']);
        }
        return $conditions;
    }

    public function _conditionTitle($title)
    {
        return array('Communication.title LIKE' => '%' . $title . '%');
    }

    public function _conditionNetwork($network_id)
    {
        $this->CommunicationNetwork = ClassRegistry::init('CommunicationNetwork');
        $communications_network = $this->CommunicationNetwork->findAllByNetworkId($network_id);
        return array('Communication.id' => Hash::extract($communications_network, '{n}.CommunicationNetwork.communication_id'));
    }

    public function _conditionDistributorNetwork($distribution_network_id)
    {
        $this->CommunicationDistributorNetwork = ClassRegistry::init('CommunicationDistributorNetwork');
        $communications_distributor_network = $this->CommunicationDistributorNetwork->findAllByDistributorNetworkId($distribution_network_id);
        return array('Communication.id' => Hash::extract($communications_distributor_network, '{n}.CommunicationDistributorNetwork.communication_id'));
    }

    public function _conditionTradingGroup($trading_group_id)
    {
        $this->CommunicationTradingGroup = ClassRegistry::init('CommunicationTradingGroup');
        $communications_trading_group = $this->CommunicationTradingGroup->findAllByTradingGroupId($trading_group_id);
        return array('Communication.id' => Hash::extract($communications_trading_group, '{n}.CommunicationTradingGroup.communication_id'));
    }

    public function _conditionActivity($activity_id)
    {
        $this->CommunicationCustomerActivity = ClassRegistry::init('CommunicationCustomerActivity');
        $communications_activity = $this->CommunicationCustomerActivity->findAllByCustomerActivityId($activity_id);
        return array('Communication.id' => Hash::extract($communications_activity, '{n}.CommunicationCustomerActivity.communication_id'));
    }

    public function _conditionType($communication_section_id)
    {
        return array('Communication.communication_section_id' => $communication_section_id);
    }

    public function _conditionSubType($communication_subsection_id)
    {
        return array('Communication.section_subsection_id' => $communication_subsection_id);
    }

    private function _conditionActive($active)
    {
        //If you get a 2, recharge as is.
        if ($active == '1' or $active == '0') {
            return array('Communication.active' => $active);
        }
    }

    private function _conditionIsPopup($pop_up)
    {
        //If you get a 2, recharge as is.
        if ($pop_up == '1' or $pop_up == '0') {
            return array('Communication.is_popup' => $pop_up);
        }
    }

    public function _conditionsCommunicationsByNetworksAndSection($networks, $communication_section_id, $title)
    {
        $networks_condition = '';
        if (CakeSession::read('Auth.User.garage_id') || CakeSession::read('Auth.User.distributor_id')) {
            $networks_condition = array('CommunicationNetwork.network_id' => $networks);
        }

        return array(
            'Communication.title LIKE' => '%' . $title . '%',
            $networks_condition,
            'CommunicationSection.id' => $communication_section_id,
            'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
            array(
                'OR' => array(
                    'end_date' => null,
                    'end_date >=' => date('Y-m-d')
                )
            ),
        );
    }

    public function _conditionsCommunicationsByNetworksAndSectionAndSubsection($networks, $communication_section_id, $subsection_id, $title)
    {
        $networks_condition = '';
        if (CakeSession::read('Auth.User.garage_id') || CakeSession::read('Auth.User.distributor_id')) {
            $networks_condition = array('CommunicationNetwork.network_id' => $networks);
        }

        return array(
            'Communication.title LIKE' => '%' . $title . '%',
            $networks_condition,
            'CommunicationSection.id' => $communication_section_id,
            'Communication.section_subsection_id' => $subsection_id,
            'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
            array(
                'OR' => array(
                    'end_date' => null,
                    'end_date >=' => date('Y-m-d')
                )
            ),
        );
    }

    public function _conditionsCommunicationsByTradingGroupAndSection($distributor_id, $communication_section_id, $title)
    {
        return array(
            'Communication.title LIKE' => '%' . $title . '%',
            'CommunicationTradingGroup.trading_group_id' => $distributor_id,
            'CommunicationSection.id' => $communication_section_id,
            'Communication.active' => ConstantsBooleans::YES,
            'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
            array(
                'OR' => array(
                    'end_date' => null,
                    'end_date >=' => date('Y-m-d')
                )
            ),
        );
    }

    public function _conditionsCommunicationsByTradingGroupAndSectionAndSubsection($distributor_id, $communication_section_id, $subsection_id, $title)
    {
        return array(
            'Communication.title LIKE' => '%' . $title . '%',
            'CommunicationTradingGroup.trading_group_id' => $distributor_id,
            'CommunicationSection.id' => $communication_section_id,
            'Communication.section_subsection_id' => $subsection_id,
            'Communication.active' => ConstantsBooleans::YES,
            'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
            array(
                'OR' => array(
                    'end_date' => null,
                    'end_date >=' => date('Y-m-d')
                )
            ),
        );
    }

    public function _conditionsCommunicationsSearchPage2ByNetworks($networks, $param)
    {
        return array(
            'CommunicationNetwork.network_id' => $networks,
            array(
                'OR' => array(
                    'Communication.title' . ' LIKE' => '%' . $param . '%',
                    'Communication.subtitle' . ' LIKE' => '%' . $param . '%',
                    'Communication.body' . ' LIKE' => '%' . $param . '%'
                )
            ),
            'Communication.active' => ConstantsBooleans::YES,
            'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
            array(
                'OR' => array(
                    'end_date' => null,
                    'end_date >=' => date('Y-m-d')
                )
            ),
        );
    }

    public function _conditionsCommunicationsSearchPage2ByNetworksAndSection($networks, $communication_section_id, $param)
    {
        return array(
            'CommunicationNetwork.network_id' => $networks,
            'Communication.communication_section_id' => $communication_section_id,
            array(
                'OR' => array(
                    'Communication.title' . ' LIKE' => '%' . $param . '%',
                    'Communication.subtitle' . ' LIKE' => '%' . $param . '%',
                    'Communication.body' . ' LIKE' => '%' . $param . '%'
                )
            ),
            'Communication.active' => ConstantsBooleans::YES,
            'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
            array(
                'OR' => array(
                    'end_date' => null,
                    'end_date >=' => date('Y-m-d')
                )
            ),
        );
    }

    public function _conditionsCommunicationsSearchPage2ByTradingGroup($distributor_id, $param)
    {
        return array(
            'CommunicationTradingGroup.trading_group_id' => $distributor_id,
            array(
                'OR' => array(
                    'Communication.title' . ' LIKE' => '%' . $param . '%',
                    'Communication.subtitle' . ' LIKE' => '%' . $param . '%',
                    'Communication.body' . ' LIKE' => '%' . $param . '%'
                )
            ),
            'Communication.active' => ConstantsBooleans::YES,
            'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
            array(
                'OR' => array(
                    'end_date' => null,
                    'end_date >=' => date('Y-m-d')
                )
            ),
        );
    }

    public function _conditionsCommunicationsSearchPage2ByTradingGroupAndSection($distributor_id, $communication_section_id, $param)
    {
        return array(
            'CommunicationTradingGroup.trading_group_id' => $distributor_id,
            'Communication.communication_section_id' => $communication_section_id,
            array(
                'OR' => array(
                    'Communication.title' . ' LIKE' => '%' . $param . '%',
                    'Communication.subtitle' . ' LIKE' => '%' . $param . '%',
                    'Communication.body' . ' LIKE' => '%' . $param . '%'
                )
            ),
            'Communication.active' => ConstantsBooleans::YES,
            'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
            array(
                'OR' => array(
                    'end_date' => null,
                    'end_date >=' => date('Y-m-d')
                )
            ),
        );
    }

    public function getCommunicationByIdAndAagRegionId($communicationId, $aagRegionId)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationSection',
                        'table' => 'communications_sections',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Communication.communication_section_id = CommunicationSection.id',
                        ),
                    ),
                    array(
                        'alias' => 'CommunicationNetwork',
                        'table' => 'communications_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Communication.id = CommunicationNetwork.communication_id'
                        )
                    )
                ),
                'conditions' => array(
                    'Communication.id' => $communicationId,
                    'Communication.aag_region_id' => $aagRegionId
                ),
                'fields' => array(
                    'Communication.*',
                    'CommunicationSection.name' . __s(),
                    'CommunicationNetwork.*'
                ),
                'group' => array(
                    'Communication.id',
                ),
            )
        );
    }

    public function getCommunicationsByNetworks($network_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationSection',
                        'table' => 'communications_sections',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Communication.communication_section_id = CommunicationSection.id',
                        ),
                    ),
                    array(
                        'alias' => 'CommunicationNetwork',
                        'table' => 'communications_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CommunicationNetwork.communication_id = Communication.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'CommunicationNetwork.network_id' => $network_id,
                    'Communication.active' => ConstantsBooleans::YES,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                    'CommunicationSection.id',
                    'CommunicationSection.name' . __s(),
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function getCommunicationsByNetworksAndSection($network_id, $section_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationSection',
                        'table' => 'communications_sections',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Communication.communication_section_id = CommunicationSection.id',
                        ),
                    ),
                    array(
                        'alias' => 'CommunicationNetwork',
                        'table' => 'communications_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CommunicationNetwork.communication_id = Communication.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'CommunicationNetwork.network_id' => $network_id,
                    'CommunicationSection.id' => $section_id,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                    'CommunicationSection.id',
                    'CommunicationSection.name' . __s(),
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function getCommunicationsByNetworksAndSectionAndSubsection($network_id, $section_id, $subsection_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationSection',
                        'table' => 'communications_sections',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Communication.communication_section_id = CommunicationSection.id',
                        ),
                    ),
                    array(
                        'alias' => 'CommunicationNetwork',
                        'table' => 'communications_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CommunicationNetwork.communication_id = Communication.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'CommunicationNetwork.network_id' => $network_id,
                    'CommunicationSection.id' => $section_id,
                    'Communication.section_subsection_id' => $subsection_id,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                    'CommunicationSection.id',
                    'CommunicationSection.name' . __s(),
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function getCommunicationByNetworkSectionAndSubsection($network_id, $section_id, $subsection_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationSection',
                        'table' => 'communications_sections',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Communication.communication_section_id = CommunicationSection.id',
                        ),
                    ),
                    array(
                        'alias' => 'CommunicationNetwork',
                        'table' => 'communications_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CommunicationNetwork.communication_id = Communication.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'CommunicationNetwork.network_id' => $network_id,
                    'CommunicationSection.id' => $section_id,
                    'Communication.section_subsection_id' => $subsection_id,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                    'CommunicationSection.id',
                    'CommunicationSection.name' . __s(),
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function getCommunicationsByTradingGroup($trading_group_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationSection',
                        'table' => 'communications_sections',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Communication.communication_section_id = CommunicationSection.id',
                        ),
                    ),
                    array(
                        'alias' => 'CommunicationTradingGroup',
                        'table' => 'communications_trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CommunicationTradingGroup.communication_id = Communication.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'CommunicationTradingGroup.trading_group_id' => $trading_group_id,
                    'Communication.active' => ConstantsBooleans::YES,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                    'CommunicationSection.name' . __s(),
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function getCommunicationsByTradingGroupAndSection($trading_group_id, $section_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationSection',
                        'table' => 'communications_sections',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Communication.communication_section_id = CommunicationSection.id',
                        ),
                    ),
                    array(
                        'alias' => 'CommunicationTradingGroup',
                        'table' => 'communications_trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CommunicationTradingGroup.communication_id = Communication.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'CommunicationTradingGroup.trading_group_id' => $trading_group_id,
                    'CommunicationSection.id' => $section_id,
                    'Communication.active' => ConstantsBooleans::YES,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                    'CommunicationSection.name' . __s(),
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function getCommunicationsByTradingGroupAndSectionAndSubsection($trading_group_id, $section_id, $subsection_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationSection',
                        'table' => 'communications_sections',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Communication.communication_section_id = CommunicationSection.id',
                        ),
                    ),
                    array(
                        'alias' => 'CommunicationTradingGroup',
                        'table' => 'communications_trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CommunicationTradingGroup.communication_id = Communication.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'CommunicationTradingGroup.trading_group_id' => $trading_group_id,
                    'CommunicationSection.id' => $section_id,
                    'Communication.section_subsection_id' => $subsection_id,
                    'Communication.active' => ConstantsBooleans::YES,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                    'CommunicationSection.name' . __s(),
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function getCommunicationsByTradingGroupSectionAndSubsection($trading_group_id, $section_id, $subsection_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationSection',
                        'table' => 'communications_sections',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Communication.communication_section_id = CommunicationSection.id',
                        ),
                    ),
                    array(
                        'alias' => 'CommunicationTradingGroup',
                        'table' => 'communications_trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CommunicationTradingGroup.communication_id = Communication.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'CommunicationTradingGroup.trading_group_id' => $trading_group_id,
                    'CommunicationSection.id' => $section_id,
                    'Communication.section_subsection_id' => $subsection_id,
                    'Communication.active' => ConstantsBooleans::YES,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                    'CommunicationSection.name' . __s(),
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function getCommunicationBySectionAndSubsectionId($communication_section_id, $subsection_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationSection',
                        'table' => 'communications_sections',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Communication.communication_section_id = CommunicationSection.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Communication.communication_section_id' => $communication_section_id,
                    'Communication.section_subsection_id' => $subsection_id,
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                    'CommunicationSection.name' . __s(),
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function getCommunicationsSearchPage2ByNetworks($network_id, $param)
    {

        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationNetwork',
                        'table' => 'communications_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CommunicationNetwork.communication_id = Communication.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'CommunicationNetwork.network_id' => $network_id,
                    array(
                        'OR' => array(
                            'Communication.title' . ' LIKE' => '%' . $param . '%',
                            'Communication.subtitle' . ' LIKE' => '%' . $param . '%',
                            'Communication.body' . ' LIKE' => '%' . $param . '%'
                        )
                    ),
                    'Communication.active' => ConstantsBooleans::YES,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function getCommunicationsSearchPage2ByNetworksAndSection($network_id, $param, $section_id)
    {

        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationNetwork',
                        'table' => 'communications_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CommunicationNetwork.communication_id = Communication.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'CommunicationNetwork.network_id' => $network_id,
                    'Communication.communication_section_id' => $section_id,
                    array(
                        'OR' => array(
                            'Communication.title' . ' LIKE' => '%' . $param . '%',
                            'Communication.subtitle' . ' LIKE' => '%' . $param . '%',
                            'Communication.body' . ' LIKE' => '%' . $param . '%'
                        )
                    ),
                    'Communication.active' => ConstantsBooleans::YES,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function getCommunicationsSearchPage2ByTradingGroup($trading_group_id, $param)
    {

        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationTradingGroup',
                        'table' => 'communications_trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CommunicationTradingGroup.communication_id = Communication.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'CommunicationTradingGroup.trading_group_id' => $trading_group_id,
                    array(
                        'OR' => array(
                            'Communication.title' . ' LIKE' => '%' . $param . '%',
                            'Communication.subtitle' . ' LIKE' => '%' . $param . '%',
                            'Communication.body' . ' LIKE' => '%' . $param . '%'
                        )
                    ),
                    'Communication.active' => ConstantsBooleans::YES,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function getCommunicationsSearchPage2ByTradingGroupAndSection($trading_group_id, $param, $section_id)
    {

        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CommunicationTradingGroup',
                        'table' => 'communications_trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CommunicationTradingGroup.communication_id = Communication.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'CommunicationTradingGroup.trading_group_id' => $trading_group_id,
                    'Communication.communication_section_id' => $section_id,
                    array(
                        'OR' => array(
                            'Communication.title' . ' LIKE' => '%' . $param . '%',
                            'Communication.subtitle' . ' LIKE' => '%' . $param . '%',
                            'Communication.body' . ' LIKE' => '%' . $param . '%'
                        )
                    ),
                    'Communication.active' => ConstantsBooleans::YES,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                ),
                'group' => array(
                    'Communication.id',
                )
            )
        );
    }

    public function add($communication)
    {
        $fields = array(
            'Communication' => array(
                'title',
                'subtitle',
                'url',
                'body',
                'image',
                'communication_section_id',
                'section_subsection_id',
                'start_date',
                'end_date',
                'active',
                'user_id',
                'creation_date',
                'without_networks',
                'without_distributor_networks',
                'without_activity',
                'aag_member_yes',
                'aag_member_no',
                'is_popup',
                'start_date_popup',
                'end_date_popup',
                'aag_region_id',
            )
        );

        $this->create();
        $communication['Communication']['creation_date'] = date('Y-m-d H:i:s');
        $communication['Communication']['user_id'] = CakeSession::read('Auth.User.id');
        if (!empty($communication['Communication']['url']) && substr($communication['Communication']['url'], 0, 7) !== "http://" && substr($communication['Communication']['url'], 0, 8) !== "https://") {
            $communication['Communication']['url'] = 'http://' . $communication['Communication']['url'];
        }

        $communication_bd = $this->guardar($communication, $fields);
        if (!$communication_bd) {
            return false;
        }

        return $communication_bd;
    }

    public function edit($communication)
    {
        $fields = array(
            'Communication' => array(
                'title',
                'subtitle',
                'url',
                'body',
                'communication_section_id',
                'section_subsection_id',
                'start_date',
                'end_date',
                'active',
                'user_id',
                'creation_date',
                'without_networks',
                'without_distributor_networks',
                'without_activity',
                'aag_member_yes',
                'aag_member_no',
                'is_popup',
                'start_date_popup',
                'end_date_popup',
            )
        );
        if (!empty($communication['Communication']['new_image'])) {
            $fields['Communication'][] = 'image';
        }
        $communication['Communication']['creation_date'] = date('Y-m-d');
        $communication['Communication']['user_id'] = CakeSession::read('Auth.User.id');
        if (!empty($communication['Communication']['url']) && substr($communication['Communication']['url'], 0, 7) !== "http://" && substr($communication['Communication']['url'], 0, 8) !== "https://") {
            $communication['Communication']['url'] = 'http://' . $communication['Communication']['url'];
        }

        $communication_bd = $this->guardar($communication, $fields);
        if (!$communication_bd) {
            return false;
        }

        return $communication_bd;
    }

    public function getAllBySectionSubsectionId($subsection_id)
    {

        $query = array(
            'conditions' => array(
                'Communication.section_subsection_id' => $subsection_id,
                'Communication.active' => ConstantsBooleans::YES,
                'Communication.start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                array(
                    'OR' => array(
                        'Communication.end_date' => null,
                        'Communication.end_date >=' => date('Y-m-d')
                    )
                ),
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'fields' => array(
                'Communication.*',
            ),
            'group' => array(
                'Communication.id',
            ),
        );

        return $this->find('all', $query);
    }

    public function getLastCommunicationsAdminAagRegion($aag_region_id)
    {

        $query = array(
            'conditions' => array(
                'Communication.active' => ConstantsBooleans::YES,
                'Communication.start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                'Communication.aag_region_id' => $aag_region_id,
                array(
                    'OR' => array(
                        'Communication.end_date' => null,
                        'Communication.end_date >=' => date('Y-m-d')
                    )
                ),
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'fields' => array(
                'Communication.*',
            ),
            'group' => array(
                'Communication.id',
            ),
            'limit' => 10

        );

        return $this->find('all', $query);
    }

    public function getLastCommunications($networks, $distributors_networks, $activities, $user)
    {
        $query = array(
            'joins' => array(
                array(
                    'alias' => 'CommunicationPosition',
                    'table' => 'communications_positions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationPosition.communication_id'
                    )
                ),
            ),
            'conditions' => array(
                'Communication.aag_region_id' => $user['aag_region_id'],
                'Communication.active' => ConstantsBooleans::YES,
                'Communication.start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                array(
                    'OR' => array(
                        'Communication.end_date' => null,
                        'Communication.end_date >=' => date('Y-m-d')
                    )
                ),
                'CommunicationPosition.position_id' => $user['Contact']['position_id'],
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'fields' => array(
                'Communication.*',
            ),
            'group' => array(
                'Communication.id',
            ),
            'limit' => 10

        );

        if ($user['Contact']['garage_id']) {
            if ($networks) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationNetwork',
                    'table' => 'communications_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationNetwork.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationNetwork.network_id' => $networks);
            } else {
                $query['conditions'][] = array('Communication.without_networks' => ConstantsBooleans::YES);
            }
        }

        if ($user['Contact']['distributor_id']) {
            $this->Distributor = ClassRegistry::init('Distributor');
            $distributor = $this->Distributor->findById($user['Contact']['distributor_id']);

            if ($distributor['Distributor']['aag_member']) {
                $query['conditions'][] = array('Communication.aag_member_yes' => ConstantsBooleans::YES);
            } else {
                $query['conditions'][] = array('Communication.aag_member_no' => ConstantsBooleans::YES);
            }

            $query['joins'][] = array(
                'alias' => 'CommunicationTradingGroup',
                'table' => 'communications_trading_groups',
                'type' => 'INNER',
                'conditions' => array(
                    'Communication.id = CommunicationTradingGroup.communication_id'
                )
            );
            $query['conditions'][] = array('CommunicationTradingGroup.trading_group_id' => $distributor['Distributor']['trading_group_id']);

            if ($distributors_networks) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationDistributionNetwork',
                    'table' => 'communications_distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationDistributionNetwork.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationDistributionNetwork.distributor_network_id' => $distributors_networks);
            } else {
                $query['conditions'][] = array('Communication.without_distributor_networks' => ConstantsBooleans::YES);
            }
        }

        if ($activities) {
            $query['joins'][] = array(
                'alias' => 'CommunicationCustomerActivity',
                'table' => 'communications_customers_activities',
                'type' => 'INNER',
                'conditions' => array(
                    'Communication.id = CommunicationCustomerActivity.communication_id'
                )
            );
            $query['conditions'][] = array('CommunicationCustomerActivity.customer_activity_id' => $activities);
        } else {
            $query['conditions'][] = array('Communication.without_activity' => ConstantsBooleans::YES);
        }

        return $this->find('all', $query);
    }

    public function findPopUps($networks, $distributors_networks, $activities, $user)
    {

        $query = array(
            'joins' => array(
                array(
                    'alias' => 'CommunicationPosition',
                    'table' => 'communications_positions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationPosition.communication_id'
                    )
                ),
            ),
            'conditions' => array(
                'Communication.active' => ConstantsBooleans::YES,
                'Communication.start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                array(
                    'OR' => array(
                        'Communication.end_date' => null,
                        'Communication.end_date >=' => date('Y-m-d')
                    )
                ),
                'CommunicationPosition.position_id' => $user['Contact']['position_id'],
                'Communication.is_popup' => ConstantsBooleans::YES,
                'Communication.start_date_popup <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                array(
                    'OR' => array(
                        'Communication.end_date_popup' => null,
                        'Communication.end_date_popup >=' => date('Y-m-d')
                    )
                ),
            ),
            'order' => array(
                'Communication.start_date_popup desc'
            ),
            'fields' => array(
                'Communication.*',
            ),
            'group' => array(
                'Communication.id',
            ),
        );

        if ($user['Contact']['garage_id']) {
            if ($networks) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationNetwork',
                    'table' => 'communications_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationNetwork.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationNetwork.network_id' => $networks);
            } else {
                $query['conditions'][] = array('Communication.without_networks' => ConstantsBooleans::YES);
            }
        }

        if ($user['Contact']['distributor_id']) {
            $this->Distributor = ClassRegistry::init('Distributor');
            $distributor = $this->Distributor->findById($user['Contact']['distributor_id']);

            if ($distributor['Distributor']['aag_member']) {
                $query['conditions'][] = array('Communication.aag_member_yes' => ConstantsBooleans::YES);
            } else {
                $query['conditions'][] = array('Communication.aag_member_no' => ConstantsBooleans::YES);
            }

            $query['joins'][] = array(
                'alias' => 'CommunicationTradingGroup',
                'table' => 'communications_trading_groups',
                'type' => 'INNER',
                'conditions' => array(
                    'Communication.id = CommunicationTradingGroup.communication_id'
                )
            );
            $query['conditions'][] = array('CommunicationTradingGroup.trading_group_id' => $distributor['Distributor']['trading_group_id']);

            if ($distributors_networks) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationDistributionNetwork',
                    'table' => 'communications_distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationDistributionNetwork.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationDistributionNetwork.distributor_network_id' => $distributors_networks);
            } else {
                $query['conditions'][] = array('Communication.without_distributor_networks' => ConstantsBooleans::YES);
            }
        }

        if ($activities) {
            $query['joins'][] = array(
                'alias' => 'CommunicationCustomerActivity',
                'table' => 'communications_customers_activities',
                'type' => 'INNER',
                'conditions' => array(
                    'Communication.id = CommunicationCustomerActivity.communication_id'
                )
            );
            $query['conditions'][] = array('CommunicationCustomerActivity.customer_activity_id' => $activities);
        } else {
            $query['conditions'][] = array('Communication.without_activity' => ConstantsBooleans::YES);
        }

        $pop_ups = $this->find('all', $query);

        $this->CommunicationUser = ClassRegistry::init('CommunicationUser');
        $pop_ups_read = $this->CommunicationUser->getPopUpsRead($user);

        foreach ($pop_ups as $key => $pop_up) {
            if (in_array($pop_up['Communication']['id'], $pop_ups_read)) {
                unset($pop_ups[$key]);
            }
        }
        $pop_ups = array_values($pop_ups);

        return $pop_ups;
    }

    public function getLastCommunicationBySection($section_id, $subsection_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'Communication.active' => ConstantsBooleans::YES,
                    'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),
                ),
                'conditions' => array(
                    'Communication.communication_section_id' => $section_id,
                    'Communication.section_subsection_id' => $subsection_id,
                    'Communication.active' => ConstantsBooleans::YES,
                ),
                'order' => array(
                    'Communication.start_date desc'
                ),
                'fields' => array(
                    'Communication.*',
                ),
            )
        );
    }

    public function getCommunicationsSearch($user)
    {
        $query = array(
            'conditions' => array(
                'Communication.aag_region_id' => $user['aag_region_id'],
                'Communication.active' => ConstantsBooleans::YES,
                'Communication.start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                array(
                    'OR' => array(
                        'Communication.end_date' => null,
                        'Communication.end_date >=' => date('Y-m-d')
                    )
                ),
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'fields' => array(
                'Communication.*',
            ),
            'group' => array(
                'Communication.id',
            ),
        );

        if ($user['Contact']['garage_id'] || $user['Contact']['distributor_id']) {
            $query['joins'][] = array(
                'alias' => 'CommunicationPosition',
                'table' => 'communications_positions',
                'type' => 'INNER',
                'conditions' => array(
                    'Communication.id = CommunicationPosition.communication_id'
                )
            );
            $query['conditions'][] = array('CommunicationPosition.position_id' => $user['Contact']['position_id']);
        }

        if (!is_null($user['Contact']['garage_id'])) {
            $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
            $this->GarageCustomerActivity = ClassRegistry::init('GarageCustomerActivity');
            $networks = $this->GarageNetwork->findNetworksByGarage(CakeSession::read('Auth.User.garage_id'));
            $activities = $this->GarageCustomerActivity->getActivitiesByGarage(CakeSession::read('Auth.User.garage_id'));

            if ($networks) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationNetwork',
                    'table' => 'communications_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationNetwork.communication_id',
                    )
                );
                $query['conditions'][] = array('CommunicationNetwork.network_id' => CakeSession::read('Auth.User.current_network'));
            } else {
                $query['conditions'][] = array('Communication.without_networks' => ConstantsBooleans::YES);
            }

            if (!empty($activities)) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationCustomerActivity',
                    'table' => 'communications_customers_activities',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationCustomerActivity.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationCustomerActivity.customer_activity_id' => $activities);
            } else {
                $query['conditions'][] = array('Communication.without_activity' => ConstantsBooleans::YES);
            }
        }

        if (!is_null($user['Contact']['distributor_id'])) {
            $this->DistributorDistributorNetwork = ClassRegistry::init('DistributorDistributorNetwork');
            $this->DistributorCustomerActivity = ClassRegistry::init('DistributorCustomerActivity');
            $networks = $this->DistributorDistributorNetwork->findNetworksByDistributor(CakeSession::read('Auth.User.distributor_id'));
            $activities = $this->DistributorCustomerActivity->getActivitiesByDistributor(CakeSession::read('Auth.User.distributor_id'));
            $this->Distributor = ClassRegistry::init('Distributor');
            $distributor = $this->Distributor->findById($user['Contact']['distributor_id']);

            if ($distributor['Distributor']['aag_member']) {
                $query['conditions'][] = array('Communication.aag_member_yes' => ConstantsBooleans::YES);
            } else {
                $query['conditions'][] = array('Communication.aag_member_no' => ConstantsBooleans::YES);
            }

            $query['joins'][] = array(
                'alias' => 'CommunicationTradingGroup',
                'table' => 'communications_trading_groups',
                'type' => 'INNER',
                'conditions' => array(
                    'Communication.id = CommunicationTradingGroup.communication_id'
                )
            );
            $query['conditions'][] = array('CommunicationTradingGroup.trading_group_id' => $distributor['Distributor']['trading_group_id']);

            if ($networks) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationDistributionNetwork',
                    'table' => 'communications_distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationDistributionNetwork.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationDistributionNetwork.distributor_network_id' => $networks);
            } else {
                $query['conditions'][] = array('Communication.without_distributor_networks' => ConstantsBooleans::YES);
            }

            if (!empty($activities)) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationCustomerActivity',
                    'table' => 'communications_customers_activities',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationCustomerActivity.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationCustomerActivity.customer_activity_id' => $activities);
            } else {
                $query['conditions'][] = array('Communication.without_activity' => ConstantsBooleans::YES);
            }
        }

        return $query;
    }

    public function getCommunicationsSinceLastLogin($user)
    {
        $query = array(
            'joins' => array(
                array(
                    'alias' => 'CommunicationSection',
                    'table' => 'communications_sections',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Communication.communication_section_id = CommunicationSection.id'
                    )
                ),
            ),
            'conditions' => array(
                'Communication.aag_region_id' => $user['aag_region_id'],
                'Communication.active' => ConstantsBooleans::YES,
                'Communication.start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                array(
                    'OR' => array(
                        'Communication.end_date' => null,
                        'Communication.end_date >=' => date('Y-m-d')
                    )
                ),
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'fields' => array(
                'Communication.*',
                'CommunicationSection.name' . __s() . ' AS name',
            ),
            'group' => array(
                'Communication.id',
            ),
        );
        if ($user['last_login']) {
            $query['conditions']['Communication.creation_date >'] = $user['last_login'];
        }

        if ($user['Contact']['garage_id'] || $user['Contact']['distributor_id']) {
            $query['joins'][] = array(
                'alias' => 'CommunicationPosition',
                'table' => 'communications_positions',
                'type' => 'INNER',
                'conditions' => array(
                    'Communication.id = CommunicationPosition.communication_id'
                )
            );
            $query['conditions'][] = array('CommunicationPosition.position_id' => $user['Contact']['position_id']);
        }

        if (!is_null($user['Contact']['garage_id'])) {
            $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
            $this->GarageCustomerActivity = ClassRegistry::init('GarageCustomerActivity');
            $networks = $this->GarageNetwork->findActiveNetworksByGarage(CakeSession::read('Auth.User.garage_id'));
            $activities = $this->GarageCustomerActivity->getActivitiesByGarage(CakeSession::read('Auth.User.garage_id'));

            if ($networks) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationNetwork',
                    'table' => 'communications_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationNetwork.communication_id',
                    )
                );
                $query['conditions'][] = array('CommunicationNetwork.network_id' => $networks);
            } else {
                $query['conditions'][] = array('Communication.without_networks' => ConstantsBooleans::YES);
            }

            if (!empty($activities)) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationCustomerActivity',
                    'table' => 'communications_customers_activities',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationCustomerActivity.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationCustomerActivity.customer_activity_id' => $activities);
            } else {
                $query['conditions'][] = array('Communication.without_activity' => ConstantsBooleans::YES);
            }
        }

        if (!is_null($user['Contact']['distributor_id'])) {
            $this->DistributorDistributorNetwork = ClassRegistry::init('DistributorDistributorNetwork');
            $this->DistributorCustomerActivity = ClassRegistry::init('DistributorCustomerActivity');
            $networks = $this->DistributorDistributorNetwork->findNetworksByDistributor(CakeSession::read('Auth.User.distributor_id'));
            $activities = $this->DistributorCustomerActivity->getActivitiesByDistributor(CakeSession::read('Auth.User.distributor_id'));
            $this->Distributor = ClassRegistry::init('Distributor');
            $distributor = $this->Distributor->findById($user['Contact']['distributor_id']);

            if ($distributor['Distributor']['aag_member']) {
                $query['conditions'][] = array('Communication.aag_member_yes' => ConstantsBooleans::YES);
            } else {
                $query['conditions'][] = array('Communication.aag_member_no' => ConstantsBooleans::YES);
            }

            $query['joins'][] = array(
                'alias' => 'CommunicationTradingGroup',
                'table' => 'communications_trading_groups',
                'type' => 'INNER',
                'conditions' => array(
                    'Communication.id = CommunicationTradingGroup.communication_id'
                )
            );
            $query['conditions'][] = array('CommunicationTradingGroup.trading_group_id' => $distributor['Distributor']['trading_group_id']);

            if ($networks) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationDistributionNetwork',
                    'table' => 'communications_distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationDistributionNetwork.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationDistributionNetwork.distributor_network_id' => $networks);
            } else {
                $query['conditions'][] = array('Communication.without_distributor_networks' => ConstantsBooleans::YES);
            }

            if (!empty($activities)) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationCustomerActivity',
                    'table' => 'communications_customers_activities',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationCustomerActivity.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationCustomerActivity.customer_activity_id' => $activities);
            } else {
                $query['conditions'][] = array('Communication.without_activity' => ConstantsBooleans::YES);
            }
        }

        $new_articles = $this->find('all', $query);
        return $new_articles;
    }

    public function getCommunicationsBySectionAndSubsections($section_id, $subsection_id, $networks, $distributors_networks, $activities, $user)
    {

        $query = array(
            'joins' => array(
                array(
                    'alias' => 'CommunicationPosition',
                    'table' => 'communications_positions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationPosition.communication_id'
                    )
                ),
            ),
            'conditions' => array(
                'Communication.aag_region_id' => $user['aag_region_id'],
                'CommunicationPosition.position_id' => $user['Contact']['position_id'],
                'Communication.communication_section_id' => $section_id,
                'Communication.section_subsection_id' => $subsection_id,
                'Communication.active' => ConstantsBooleans::YES,
                'Communication.start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                array(
                    'OR' => array(
                        'Communication.end_date' => null,
                        'Communication.end_date >=' => date('Y-m-d')
                    )
                ),
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'fields' => array(
                'Communication.*',
            ),
            'group' => array(
                'Communication.id',
            ),
        );

        if ($user['Contact']['garage_id']) {
            if ($networks) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationNetwork',
                    'table' => 'communications_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationNetwork.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationNetwork.network_id' => $networks);
            } else {
                $query['conditions'][] = array('Communication.without_networks' => ConstantsBooleans::YES);
            }
        }

        if ($user['Contact']['distributor_id']) {
            $this->Distributor = ClassRegistry::init('Distributor');
            $distributor = $this->Distributor->findById($user['Contact']['distributor_id']);

            if ($distributor['Distributor']['aag_member']) {
                $query['conditions'][] = array('Communication.aag_member_yes' => ConstantsBooleans::YES);
            } else {
                $query['conditions'][] = array('Communication.aag_member_no' => ConstantsBooleans::YES);
            }

            $query['joins'][] = array(
                'alias' => 'CommunicationTradingGroup',
                'table' => 'communications_trading_groups',
                'type' => 'INNER',
                'conditions' => array(
                    'Communication.id = CommunicationTradingGroup.communication_id'
                )
            );
            $query['conditions'][] = array('CommunicationTradingGroup.trading_group_id' => $distributor['Distributor']['trading_group_id']);

            if ($distributors_networks) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationDistributionNetwork',
                    'table' => 'communications_distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationDistributionNetwork.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationDistributionNetwork.distributor_network_id' => $distributors_networks);
            } else {
                $query['conditions'][] = array('Communication.without_distributor_networks' => ConstantsBooleans::YES);
            }
        }

        if ($activities) {
            $query['joins'][] = array(
                'alias' => 'CommunicationCustomerActivity',
                'table' => 'communications_customers_activities',
                'type' => 'INNER',
                'conditions' => array(
                    'Communication.id = CommunicationCustomerActivity.communication_id'
                )
            );
            $query['conditions'][] = array('CommunicationCustomerActivity.customer_activity_id' => $activities);
        } else {
            $query['conditions'][] = array('Communication.without_activity' => ConstantsBooleans::YES);
        }
        return $this->find('all', $query);
    }
}
