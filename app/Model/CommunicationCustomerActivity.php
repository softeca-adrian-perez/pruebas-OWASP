<?php

class CommunicationCustomerActivity extends AppModel{
    public $useTable = 'communications_customers_activities';

    public function add( $communication_id , $customer_activity_id ){

        $fields = array(
            'CommunicationCustomerActivity' => array(
                'communication_id',
                'customer_activity_id',
            )
        );
        $communication_activity['CommunicationCustomerActivity']['communication_id'] = $communication_id;
        $communication_activity['CommunicationCustomerActivity']['customer_activity_id'] = $customer_activity_id;

        $this->create();
        if(!$this->guardar( $communication_activity, $fields )){
            return false;
        }

        return true;
    }
    
    public function getActivitiesByCommunicationId( $communication_id ){
        return $this->find('all',
            array(
                'conditions' => array(
                    'communication_id' => $communication_id
                ),
                'fields' => array(
                    'CommunicationCustomerActivity.*',
                )
            )
        );
    }
}