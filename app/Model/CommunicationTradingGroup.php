<?php

class CommunicationTradingGroup extends AppModel{
    public $useTable = 'communications_trading_groups';

    public function add( $communication_id , $trading_group_id ){

        $fields = array(
            'CommunicationTradingGroup' => array(
                'communication_id',
                'trading_group_id',
            )
        );
        $communication_trading_group['CommunicationTradingGroup']['communication_id'] = $communication_id;
        $communication_trading_group['CommunicationTradingGroup']['trading_group_id'] = $trading_group_id;

        $this->create();
        if(!$this->guardar( $communication_trading_group, $fields )){
            return false;
        }

        return true;
    }

    public function getListByCommunicationId( $communication_id ){
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroup',
                        'table' => 'trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroup.id = CommunicationTradingGroup.trading_group_id'
                        )
                    )
                ),
                'conditions' => array(
                    'communication_id' => $communication_id
                ),
                'fields' => array(
                    'id',
                    'Network.name'
                )
            )
        );
    }
    
    public function getTGByCommunicationId( $communication_id ){
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroup',
                        'table' => 'trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroup.id = CommunicationTradingGroup.trading_group_id'
                        )
                    )
                ),
                'conditions' => array(
                    'communication_id' => $communication_id
                ),
                'fields' => array(
                    'trading_group_id',
                    'TradingGroup.name'
                )
            )
        );
    }
}