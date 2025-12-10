<?php

class ShortcutTradingGroup extends AppModel{
    public $useTable = 'shortcuts_trading_groups';

    public function add( $shortcut_id , $trading_group_id ){

        $fields = array(
            'ShortcutTradingGroup' => array(
                'shortcut_id',
                'trading_group_id',
            )
        );
        $shortcut_trading_group['ShortcutTradingGroup']['shortcut_id'] = $shortcut_id;
        $shortcut_trading_group['ShortcutTradingGroup']['trading_group_id'] = $trading_group_id;

        $this->create();
        if(!$this->guardar( $shortcut_trading_group, $fields )){
            return false;
        }

        return true;
    }

    public function getListByShortcutId( $shortcut_id ){
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroup',
                        'table' => 'trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroup.id = ShortcutTradingGroup.trading_group_id'
                        )
                    )
                ),
                'conditions' => array(
                    'shortcut_id' => $shortcut_id
                ),
                'fields' => array(
                    'id',
                    'Network.name'
                )
            )
        );
    }
    public function getTGByShortcutId( $shortcut_id ){
        return $this->find('list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroup',
                        'table' => 'trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroup.id = ShortcutTradingGroup.trading_group_id'
                        )
                    )
                ),
                'conditions' => array(
                    'shortcut_id' => $shortcut_id
                ),
                'fields' => array(
                    'trading_group_id',
                    'TradingGroup.name'
                )
            )
        );
    }
}