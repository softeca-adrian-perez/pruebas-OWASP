<?php

class CommunicationSectionCustomerActivity extends AppModel{
    public $useTable = 'communications_sections_customers_activities';

    public function add( $communication_section_id , $customer_activity_id ){

        $fields = array(
            'CommunicationSectionCustomerActivity' => array(
                'communication_section_id',
                'customer_activity_id',
            )
        );
        $communication_section_activity['CommunicationSectionCustomerActivity']['communication_section_id'] = $communication_section_id;
        $communication_section_activity['CommunicationSectionCustomerActivity']['customer_activity_id'] = $customer_activity_id;

        $this->create();
        if(!$this->guardar( $communication_section_activity, $fields )){
            return false;
        }

        return true;
    }

    public function getActivitiesByCommunicationSectionId( $communication_section_id ){
        return $this->find('all',
            array(
                'conditions' => array(
                    'communication_section_id' => $communication_section_id
                ),
                'fields' => array(
                    'CommunicationSectionCustomerActivity.*',
                )
            )
        );
    }

    public function getListActivitiesByCommunicationSectionId( $communication_section_id ){
        return $this->find('list',
            array(
                'conditions' => array(
                    'communication_section_id' => $communication_section_id
                ),
                'fields' => array(
                    'CommunicationSectionCustomerActivity.id',
                    'CommunicationSectionCustomerActivity.customer_activity_id',
                )
            )
        );
    }
    
}