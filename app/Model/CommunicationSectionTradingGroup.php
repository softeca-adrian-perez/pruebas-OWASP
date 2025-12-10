<?php

class CommunicationSectionTradingGroup extends AppModel{
    public $useTable = 'communications_sections_trading_groups';

    public function add( $communication_section_id , $trading_group_id ){

        $fields = array(
            'CommunicationSectionTradingGroup' => array(
                'communication_section_id',
                'trading_group_id',
            )
        );
        $communication_section_trading_group['CommunicationSectionTradingGroup']['communication_section_id'] = $communication_section_id;
        $communication_section_trading_group['CommunicationSectionTradingGroup']['trading_group_id'] = $trading_group_id;

        $this->create();
        if(!$this->guardar( $communication_section_trading_group, $fields )){
            return false;
        }

        return true;
    }

    public function getTGByCommunicationSectionId( $communication_section_id ){
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroup',
                        'table' => 'trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroup.id = CommunicationSectionTradingGroup.trading_group_id'
                        )
                    )
                ),
                'conditions' => array(
                    'communication_section_id' => $communication_section_id
                ),
                'fields' => array(
                    'trading_group_id',
                    'TradingGroup.name'
                )
            )
        );
    }

    
}