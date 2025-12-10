<?php

class SectionSubsectionTradingGroup extends AppModel{
    public $useTable = 'sections_subsections_trading_groups';

    public function add( $section_subsection_id , $trading_group_id ){

        $fields = array(
            'SectionSubsectionTradingGroup' => array(
                'section_subsection_id',
                'trading_group_id',
            )
        );
        $section_subsection_trading_group['SectionSubsectionTradingGroup']['section_subsection_id'] = $section_subsection_id;
        $section_subsection_trading_group['SectionSubsectionTradingGroup']['trading_group_id'] = $trading_group_id;

        $this->create();
        if(!$this->guardar( $section_subsection_trading_group, $fields )){
            return false;
        }

        return true;
    }

    public function getTGBySectionSubsectionId( $section_subsection_id ){
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroup',
                        'table' => 'trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroup.id = SectionSubsectionTradingGroup.trading_group_id'
                        )
                    )
                ),
                'conditions' => array(
                    'section_subsection_id' => $section_subsection_id
                ),
                'fields' => array(
                    'trading_group_id',
                    'TradingGroup.name'
                )
            )
        );
    }

    
}