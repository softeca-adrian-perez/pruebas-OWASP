<?php

class PositionConfigSupplierCategory extends AppModel{
    public $useTable = 'positions_config_suppliers_categories';

    public function new_position_config_supplier_category( $position_config_id, $supplier_category_id ){
        $fields = array(
            'PositionConfigSupplierCategory' => array(
                'position_config_id',
                'supplier_category_id'
            )
        );

        $position_config_supplier_category = array(
            'PositionConfigSupplierCategory' => array(
                'position_config_id' => $position_config_id,
                'supplier_category_id' => $supplier_category_id
            )
        );
        
        $this->create();
        $position_config_supplier_category_bd = $this->guardar($position_config_supplier_category, $fields);
        if ( !$position_config_supplier_category_bd ){
            return false;
        }

        return true;
    }
}