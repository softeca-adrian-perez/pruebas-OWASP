<?php
class SupplierTradingGroup extends AppModel {
    public $useTable = 'suppliers_trading_groups';

    public $hasMany = array(
        'Supplier',
        'TradingGroup',
    );

    public function add($supplier_id, $trading_group_id) {
        $fields = array(
            'SupplierTradingGroup' => array(
                'supplier_id',
                'trading_group_id',
            )
        );

        $supplier_trading_group['SupplierTradingGroup']['supplier_id'] = $supplier_id;
        $supplier_trading_group['SupplierTradingGroup']['trading_group_id'] = $trading_group_id;

        $this->create();
        if(!$this->save($supplier_trading_group, $fields)) {
            return false;
        }

        return true;
    }

    public function edit($supplier_id, $trading_group_id) {
        $fields = array(
            'SupplierTradingGroup' => array(
                'supplier_id',
                'trading_group_id',
            )
        );

        $supplier_trading_group['SupplierTradingGroup']['supplier_id'] = $supplier_id;
        $supplier_trading_group['SupplierTradingGroup']['trading_group_id'] = $trading_group_id;

        if(!$this->save($supplier_trading_group, $fields)) {
            return false;
        }

        return true;
    }
}