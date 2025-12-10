<?php

class PositionConfigCustomerActivity extends AppModel{
    public $useTable = 'positions_config_customer_activities';

    public function new_position_config_customer_activity( $position_config_id, $customer_activity_id ){
        $fields = array(
            'PositionConfigCustomerActivity' => array(
                'position_config_id',
                'customer_activity_id'
            )
        );

        $position_config_customer_activity = array(
            'PositionConfigCustomerActivity' => array(
                'position_config_id' => $position_config_id,
                'customer_activity_id' => $customer_activity_id
            )
        );
        
        $this->create();
        $position_config_customer_activity_bd = $this->guardar($position_config_customer_activity, $fields);
        if ( !$position_config_customer_activity_bd ){
            return false;
        }

        return true;
    }
}