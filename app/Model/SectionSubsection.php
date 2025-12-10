<?php

class SectionSubsection extends AppModel
{
    public $useTable = 'sections_subsections';

    public $validate = array(
        'name_en' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
        ),
        'name_fr' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
        ),
        'name_de' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
        ),
        'communication_section_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_section'
            ),
        ),
    );

    private $_queries = array(
        'search_maintenance_subsections' => array(
            'fields' => array(
                '*',
            ),
            'order' => 'SectionSubsection.name_en asc'
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
        if (!empty($fields['communication_section_id'])) {
            $conditions[] = $this->_conditionCommunicationSectionId($fields['communication_section_id']);
        }
        return $conditions;
    }

    public function _conditionName($name)
    {
        return array('SectionSubsection.name' . __s() . ' LIKE' => '%' . $name . '%');
    }

    private function _conditionCommunicationSectionId($communication_section_id)
    {
        return array('SectionSubsection.communication_section_id' => $communication_section_id);
    }

    public function search_list_region($aag_region_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'SectionSubsection.aag_region_id' => $aag_region_id
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

    public function getSubsectionsBySectionAndAagRegionId($section_id, $aag_region_id)
    {
        return $this->find('all', array(
            'fields' => array(
                'id',
                'name_' . __l(),
                'image'
            ),
            'conditions' => array(
                'SectionSubsection.communication_section_id' => $section_id,
                'SectionSubsection.aag_region_id' => $aag_region_id
            ),
            'order' => array(
                'name_' . __l()
            )
        ));
    }

    public function add($section_subsection)
    {
        $fields = array(
            'SectionSubsection' => array(
                'name_en',
                'name_fr',
                'name_de',
                'communication_section_id',
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

        $this->create();
        $section_subsection['SectionSubsection']['creation_date'] = date('Y-m-d H:i:s');
        if (empty($section_subsection['SectionSubsection']['new_image'])) {
            $section_subsection['SectionSubsection']['image'] = null;
        }

        $section_subsection_bd = $this->guardar($section_subsection, $fields);
        if (!$section_subsection_bd) {
            return false;
        }

        return $section_subsection_bd;
    }

    public function edit($section_subsection, $section_subsection_bd)
    {
        $fields = array(
            'SectionSubsection' => array(
                'name_en',
                'name_fr',
                'name_de',
                'communication_section_id',
                'image',
                'without_networks',
                'without_distributor_networks',
                'without_activity',
                'aag_member_yes',
                'aag_member_no',
            )
        );

        if (empty($section_subsection['SectionSubsection']['new_image'])) {
            $section_subsection['SectionSubsection']['image'] = $section_subsection_bd['SectionSubsection']['image'];
        }

        $section_subsection_bd = $this->guardar($section_subsection, $fields);
        if (!$section_subsection_bd) {
            return false;
        }

        return $section_subsection_bd;
    }

    public function getSubsectionByCommunicationSection($comm_section_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'CommunicationSection',
                    'table' => 'communications_sections',
                    'type' => 'INNER',
                    'conditions' => array(
                        'SectionSubsection.communication_section_id = CommunicationSection.id'
                    )
                ),
            ),
            'conditions' => array(
                'SectionSubsection.communication_section_id' => $comm_section_id
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            ),
        ));
    }

    public function getSubsections($section_id, $networks, $distributors_networks, $activities, $user)
    {

        $query = array(
            'joins' => array(
                array(
                    'alias' => 'SectionSubsectionPosition',
                    'table' => 'sections_subsections_positions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'SectionSubsection.id = SectionSubsectionPosition.section_subsection_id'
                    )
                ),
            ),
            'conditions' => array(
                'SectionSubsectionPosition.position_id' => $user['Contact']['position_id'],
                'SectionSubsection.communication_section_id' => $section_id,
                'SectionSubsection.aag_region_id' => $user['Contact']['aag_region_id'],
            ),
            'order' => array(
                'SectionSubsection.name' . __s()
            ),
            'fields' => array(
                'SectionSubsection.*',
            ),
            'group' => array(
                'SectionSubsection.id',
            ),
        );

        if ($user['Contact']['garage_id']) {
            if ($networks) {
                $query['joins'][] = array(
                    'alias' => 'SectionSubsectionNetwork',
                    'table' => 'sections_subsections_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'SectionSubsection.id = SectionSubsectionNetwork.section_subsection_id'
                    )
                );
                $query['conditions'][] = array('SectionSubsectionNetwork.network_id' => $networks);
            } else {
                $query['conditions'][] = array('SectionSubsection.without_networks' => ConstantsBooleans::YES);
            }
        }

        if ($user['Contact']['distributor_id']) {
            $this->Distributor = ClassRegistry::init('Distributor');
            $distributor = $this->Distributor->findById($user['Contact']['distributor_id']);

            if ($distributor['Distributor']['aag_member']) {
                $query['conditions'][] = array('SectionSubsection.aag_member_yes' => ConstantsBooleans::YES);
            } else {
                $query['conditions'][] = array('SectionSubsection.aag_member_no' => ConstantsBooleans::YES);
            }

            $query['joins'][] = array(
                'alias' => 'SectionSubsectionTradingGroup',
                'table' => 'sections_subsections_trading_groups',
                'type' => 'INNER',
                'conditions' => array(
                    'SectionSubsection.id = SectionSubsectionTradingGroup.section_subsection_id'
                )
            );
            $query['conditions'][] = array('SectionSubsectionTradingGroup.trading_group_id' => $distributor['Distributor']['trading_group_id']);

            if ($distributors_networks) {
                $query['joins'][] = array(
                    'alias' => 'SectionSubsectionDistributionNetwork',
                    'table' => 'sections_subsections_distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'SectionSubsection.id = SectionSubsectionDistributionNetwork.section_subsection_id'
                    )
                );
                $query['conditions'][] = array('SectionSubsectionDistributionNetwork.distributor_network_id' => $distributors_networks);
            } else {
                $query['conditions'][] = array('SectionSubsection.without_distributor_networks' => ConstantsBooleans::YES);
            }
        }

        if ($activities) {
            $query['joins'][] = array(
                'alias' => 'SectionSubsectionCustomerActivity',
                'table' => 'sections_subsections_customers_activities',
                'type' => 'INNER',
                'conditions' => array(
                    'SectionSubsection.id = SectionSubsectionCustomerActivity.section_subsection_id'
                )
            );
            $query['conditions'][] = array('SectionSubsectionCustomerActivity.customer_activity_id' => $activities);
        } else {
            $query['conditions'][] = array('SectionSubsection.without_activity' => ConstantsBooleans::YES);
        }

        return $this->find('all', $query);
    }
}
