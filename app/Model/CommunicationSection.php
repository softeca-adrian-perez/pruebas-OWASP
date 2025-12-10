<?php

class CommunicationSection extends AppModel
{
    public $useTable = 'communications_sections';

    public $validate = array(
        'name_en' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_fr' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_de' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_nl' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'image' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    private $_queries = array(
        'search_maintenance_sections' => array(
            'fields' => array(
                '*',
            ),
            'order' => 'CommunicationSection.name_en asc'
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['name' . __s()])) {
            $conditions[] = $this->_conditionName($fields['name' . __s()]);
        }
        if (isset($fields['visual'])) {
            $conditions[] = $this->_conditionVisual($fields['visual']);
        }
        if (isset($fields['scrolling'])) {
            $conditions[] = $this->_conditionScrolling($fields['scrolling']);
        }

        return $conditions;
    }

    public function _conditionName($name)
    {
        return array('CommunicationSection.name' . __s() . ' LIKE' => '%' . $name . '%');
    }

    private function _conditionVisual($visual)
    {

        if ($visual == '1' or $visual == '0') {
            return array('CommunicationSection.visual' => $visual);
        }
    }

    private function _conditionScrolling($scrolling)
    {
        if ($scrolling == '1' or $scrolling == '0') {
            return array('CommunicationSection.scrolling' => $scrolling);
        }
    }

    private function _fieldsCommunicationSection()
    {
        $fields = array(
            'CommunicationSection' => array(
                'name_en',
                'name_fr',
                'name_de',
                'scrolling',
                'visual',
                'image',
                'creation_date',
                'without_networks',
                'without_distributor_networks',
                'without_activity',
                'aag_member_yes',
                'aag_member_no',
                'aag_region_id',
            )
        );
        return $fields;
    }

    public function search_list_region($aag_region_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'CommunicationSection.aag_region_id' => $aag_region_id
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'name_' . __l()
            )
        ));
    }

    public function search_list_with_subsections_region($aag_region_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'SectionSubsection',
                    'table' => 'sections_subsections',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CommunicationSection.id = SectionSubsection.communication_section_id',
                        'SectionSubsection.aag_region_id' => $aag_region_id,
                    )
                ),
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'name_' . __l()
            )
        ));
    }

    public function search_categories_datas($aag_region_id)
    {
        return $this->find('all', array(
            'fields' => array(
                'id',
                'name_' . __l(),
                'scrolling',
                'image'
            ),
            'conditions' => array(
                'CommunicationSection.aag_region_id' => $aag_region_id,
            ),
            'order' => array(
                'name_' . __l()
            )
        ));
    }

    public function search_all_communications_by_section($section_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Communication',
                    'table' => 'communications',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.communication_section_id = CommunicationSection.id'
                    )
                ),
            ),
            'conditions' => array(
                'CommunicationSection.id' => $section_id,
                'Communication.active' => ConstantsBooleans::YES,
                'Communication.start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                array(
                    'OR' => array(
                        'Communication.end_date' => null,
                        'Communication.end_date >=' => date('Y-m-d')
                    )
                ),
            ),

            'fields' => array(
                'Communication.*',
            )
        ));
    }

    public function search_communications_by_section($section_id, $garage_networks, $distributor_networks, $activities, $user)
    {
        $query = array(
            'joins' => array(
                array(
                    'alias' => 'Communication',
                    'table' => 'communications',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.communication_section_id = CommunicationSection.id'
                    )
                ),
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
                'Communication.communication_section_id' => $section_id,
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
        );

        if ($user['Contact']['garage_id']) {
            if ($garage_networks) {
                $query['joins'][] = array(
                    'alias' => 'CommunicationNetwork',
                    'table' => 'communications_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.id = CommunicationNetwork.communication_id'
                    )
                );
                $query['conditions'][] = array('CommunicationNetwork.network_id' => $garage_networks);
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

            if ($distributor_networks) {
                $query['joins'][] =
                    array(
                        'alias' => 'CommunicationDistributionNetwork',
                        'table' => 'communications_distributors_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Communication.id = CommunicationDistributionNetwork.communication_id'
                        )
                    );
                $query['conditions'][] = array('CommunicationDistributionNetwork.distributor_network_id' => $distributor_networks);
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


    public function search_active_list_networks($network_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Communication',
                    'table' => 'communications',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.communication_section_id = CommunicationSection.id'
                    )
                ),
                array(
                    'alias' => 'CommunicationNetwork',
                    'table' => 'communications_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CommunicationNetwork.communication_id = Communication.id'
                    )
                )
            ),
            'conditions' => array(
                'CommunicationNetwork.network_id' => $network_id,
                'Communication.active' => ConstantsBooleans::YES,
                'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                'position' => ConstantsCommunicationSections::TOP,
                array(
                    'OR' => array(
                        'end_date' => null,
                        'end_date >=' => date('Y-m-d')
                    )
                ),
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'group' => array(
                'Communication.id',
            )
        ));
    }

    public function search_active_list_networks_all($network_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Communication',
                    'table' => 'communications',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.communication_section_id = CommunicationSection.id'
                    )
                ),
                array(
                    'alias' => 'CommunicationNetwork',
                    'table' => 'communications_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CommunicationNetwork.communication_id = Communication.id'
                    )
                )
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
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'group' => array(
                'Communication.id',
            )
        ));
    }

    public function search_active_list_trading_groups($trading_group_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Communication',
                    'table' => 'communications',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.communication_section_id = CommunicationSection.id'
                    )
                ),
                array(
                    'alias' => 'CommunicationTradingGroup',
                    'table' => 'communications_trading_groups',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CommunicationTradingGroup.communication_id = Communication.id'
                    )
                )
            ),
            'conditions' => array(
                'CommunicationTradingGroup.trading_group_id' => $trading_group_id,
                'Communication.active' => ConstantsBooleans::YES,
                'start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                'position' => ConstantsCommunicationSections::TOP,
                array(
                    'OR' => array(
                        'end_date' => null,
                        'end_date >=' => date('Y-m-d')
                    )
                ),
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'group' => array(
                'Communication.id',
            )
        ));
    }

    public function search_active_list_trading_groups_all($trading_group_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Communication',
                    'table' => 'communications',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Communication.communication_section_id = CommunicationSection.id'
                    )
                ),
                array(
                    'alias' => 'CommunicationTradingGroup',
                    'table' => 'communications_trading_groups',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CommunicationTradingGroup.communication_id = Communication.id'
                    )
                )
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
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'Communication.start_date desc'
            ),
            'group' => array(
                'Communication.id',
            )
        ));
    }

    public function getCommunicationByPosition($position)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'CommunicationSection.position' => $position
                )
            )
        );
    }

    public function add($communication_section)
    {
        $fields = $this->_fieldsCommunicationSection();
        $communication_section['CommunicationSection']['creation_date'] = date('Y-m-d H:i:s');
        if (empty($communication_section['CommunicationSection']['new_image'])) {
            $communication_section['CommunicationSection']['image'] = null;
        }

        $this->create();
        $communication_section_bd = $this->guardar($communication_section, $fields);

        if (!$communication_section_bd) {
            return false;
        }

        return $communication_section_bd;
    }

    public function edit($communication_section, $communication_section_bd)
    {
        $fields = $this->_fieldsCommunicationSection();

        if (empty($communication_section['CommunicationSection']['new_image'])) {
            $communication_section['CommunicationSection']['image'] = $communication_section_bd['CommunicationSection']['image'];
        }

        $communication_section_bd = $this->guardar($communication_section, $fields);
        if (!$communication_section_bd) {
            return false;
        }

        return $communication_section_bd;
    }


    public function getSubsectionsActive($section_id)
    {

        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Communication',
                    'table' => 'communications',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CommunicationSection.id = Communication.communication_section_id',
                        'Communication.active' => ConstantsBooleans::ACTIVE,
                    )
                ),
            ),
            'conditions' => array(
                'Communication.communication_section_id' => $section_id,
                'Communication.active' => ConstantsBooleans::ACTIVE,
            ),
            'fields' => array(
                'Communication.section_subsection_id',
            ),
        ));
    }
}
