<?php

class SectionSubsectionCustomerActivity extends AppModel{
    public $useTable = 'sections_subsections_customers_activities';

    public function add( $section_subsection_id , $customer_activity_id ){

        $fields = array(
            'SectionSubsectionCustomerActivity' => array(
                'section_subsection_id',
                'customer_activity_id',
            )
        );
        $section_subsection_activity['SectionSubsectionCustomerActivity']['section_subsection_id'] = $section_subsection_id;
        $section_subsection_activity['SectionSubsectionCustomerActivity']['customer_activity_id'] = $customer_activity_id;

        $this->create();
        if(!$this->guardar( $section_subsection_activity, $fields )){
            return false;
        }

        return true;
    }

    public function getActivitiesBySectionSubsectionId( $section_subsection_id ){
        return $this->find('all',
            array(
                'conditions' => array(
                    'section_subsection_id' => $section_subsection_id
                ),
                'fields' => array(
                    'SectionSubsectionCustomerActivity.*',
                )
            )
        );
    }

    public function getListActivitiesBySectionSubsectionId( $section_subsection_id ){
        return $this->find('list',
            array(
                'conditions' => array(
                    'section_subsection_id' => $section_subsection_id
                ),
                'fields' => array(
                    'SectionSubsectionCustomerActivity.id',
                    'SectionSubsectionCustomerActivity.customer_activity_id',
                )
            )
        );
    }
    
}