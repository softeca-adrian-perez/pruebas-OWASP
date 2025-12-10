<?php

class ShortcutCustomerActivity extends AppModel{
    public $useTable = 'shortcuts_customers_activities';

    public function add( $shortcut_id , $customer_activity_id ){

        $fields = array(
            'ShortcutCustomerActivity' => array(
                'shortcut_id',
                'customer_activity_id',
            )
        );
        $shortcut_section_activity['ShortcutCustomerActivity']['shortcut_id'] = $shortcut_id;
        $shortcut_section_activity['ShortcutCustomerActivity']['customer_activity_id'] = $customer_activity_id;

        $this->create();
        if(!$this->guardar( $shortcut_section_activity, $fields )){
            return false;
        }

        return true;
    }

    // public function getActivitiesByCommunicationSectionId( $communication_section_id ){
    //     return $this->find('all',
    //         array(
    //             'conditions' => array(
    //                 'communication_section_id' => $communication_section_id
    //             ),
    //             'fields' => array(
    //                 'CommunicationSectionCustomerActivity.*',
    //             )
    //         )
    //     );
    // }

    public function getListActivitiesByShortcutId( $shortcut_id ){
        return $this->find('list',
            array(
                'conditions' => array(
                    'shortcut_id' => $shortcut_id
                ),
                'fields' => array(
                    'ShortcutCustomerActivity.id',
                    'ShortcutCustomerActivity.customer_activity_id',
                )
            )
        );
    }
    
}