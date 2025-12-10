<?php

class GarageWorkshopActivity extends AppModel{
    public $useTable = 'garages_workshop_activities';

    public function add_activities( $workshop_activities, $garage_id, $user ){
        $workshop_activities_bd = $this->findAllByGarageId( $garage_id );

        $old_data = $this->findAllByGarageId( $garage_id );
        if(!empty($old_data)){
            $old_data = array_combine(
                Hash::extract($old_data, '{n}.GarageWorkshopActivity.workshop_activity_id'),
                Hash::extract($old_data, '{n}.GarageWorkshopActivity.workshop_activity_id')
            );
        } else {
            $old_data = array();
        }

        foreach($workshop_activities_bd as $workshop_activity_bd){
            $this->delete($workshop_activity_bd['GarageWorkshopActivity']['id']);
        }

        $fields = array(
            'GarageWorkshopActivity' => array(
                'garage_id',
                'workshop_activity_id'
            )
        );

        if(is_array($workshop_activities)){
            foreach($workshop_activities as $workshop_activity){
                $workshop_activity_save = array(
                    'GarageWorkshopActivity' => array(
                        'garage_id' => $garage_id,
                        'workshop_activity_id' => $workshop_activity,
                    )
                );
                $this->create();
                $workshop_activity_save_bd = $this->guardar($workshop_activity_save, $fields);
                if ( !$workshop_activity_save_bd ){
                    return false;
                }
            }
        }

        $new_data = $this->findAllByGarageId( $garage_id );
        if(!empty($new_data)){
            $new_data = array_combine(
                Hash::extract($new_data, '{n}.GarageWorkshopActivity.workshop_activity_id'),
                Hash::extract($new_data, '{n}.GarageWorkshopActivity.workshop_activity_id')
            );
        } else {
            $new_data = array();
        }

        $this->LogChange = ClassRegistry::init('LogChange');

        $this->LogChange->get_params_create_log_edit(
            $old_data,
            $new_data,
            $this->table,
            $user,
            $garage_id,
            ConstantsLogType::GARAGE,
            'workshop_activity_id'
        );
        
        $this->commit();
        return true;
    }

    public function getWorkshopActivitiesByGarageId( $garage_id ){
        return $this->find('list', array(
            'conditions' => array(
                'garage_id' => $garage_id
            ),
            'fields' => array('workshop_activity_id')
        ));
    }

    public function export_workshop_activities ( $garage_id ){
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'WorkshopActivities',
                    'table' => 'workshop_activities',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageWorkshopActivity.workshop_activity_id = WorkshopActivities.id'
                    )
                )
            ),
            'conditions' => array(
                'GarageWorkshopActivity.garage_id' => $garage_id
            ),
            'fields' => array(
                'WorkshopActivities.name_' . __l()
            )
        ));
    }
}