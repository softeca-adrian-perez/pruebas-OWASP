<?php
class SupplierNetwork extends AppModel {
    public $useTable = 'suppliers_networks';

    public $hasMany = array(
        'Supplier',
        'Network',
    );

    public function add($supplier_id, $network_id) {
        $fields = array(
            'SupplierNetwork' => array(
                'supplier_id',
                'network_id',
            )
        );

        $supplier_network['SupplierNetwork']['supplier_id'] = $supplier_id;
        $supplier_network['SupplierNetwork']['network_id'] = $network_id;

        $this->create();
        if(!$this->save($supplier_network, $fields)) {
            return false;
        }

        return true;
    }

    public function edit($supplier_id, $network_id) {
        $fields = array(
            'SupplierNetwork' => array(
                'supplier_id',
                'network_id',
            )
        );

        $supplier_network['SupplierNetwork']['supplier_id'] = $supplier_id;
        $supplier_network['SupplierNetwork']['network_id'] = $network_id;

        if(!$this->save($supplier_network, $fields)) {
            return false;
        }

        return true;
    }
}