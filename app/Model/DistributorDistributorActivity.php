<?php

class DistributorDistributorActivity extends AppModel{
    public $useTable = 'distributors_distributors_activities';

    public function getActivitiesByDistributor( $distributor_id ){
        return $this->find(
            'all',
            array(
                'joins'=> array(
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Distributor.id = DistributorDistributorActivity.distributor_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorDistributorActivity.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'DistributorDistributorActivity.*',
                ),
            )
        );
    }

    public function findActivitiesExport( $distributor_id ){
        return $this->find(
            'all',
            array(
                'joins'=> array(
                    array(
                        'alias' => 'DistributorActivity',
                        'table' => 'distributors_activities',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'DistributorActivity.id = DistributorDistributorActivity.distributor_activity_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorDistributorActivity.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'DistributorDistributorActivity.*',
                    'DistributorActivity.name_' . __l(),
                ),
            )
        );
    }

    public function edit_activity( $distributor ){
        $fields = array(
            'Distributor' => array(
                'distributor_id',
                'distributor_activity_id',
                'join_date',
                'left_date',
            )
        );

        $distributor_distributor_activities = $this->findAllByDistributorId( $distributor['Distributor']['id'] );

        foreach($distributor_distributor_activities as $distributor_distributor_activity){
            $this->delete( $distributor_distributor_activity['DistributorDistributorActivity']['id'] );
        }

        foreach($distributor['DistributorActivityPrimary'] as $key => $activity){
            if($activity['checked']){
                $distributor_bd['distributor_id'] = $distributor['Distributor']['id'];
                $distributor_bd['distributor_activity_id'] = $key;
                $distributor_bd['join_date'] = $activity['join_date'];
                $distributor_bd['left_date'] = $activity['left_date'];
                $this->create();
                $distributor_distributor_activity_bd = $this->guardar( $distributor_bd, $fields );

                if(!$distributor_distributor_activity_bd){
                    return false;
                }
            }
        }

        return true;
    }

    // public function createDistributorDistributorActivity( $distributor_json , $distributor_id ){ // The Distributor Activities comes from a UK JSON

        
    //     if($distributor_json['LV'] == true){
    //         $activity_id = 1;
    //         $distributor_distributor_activity_tmp = array(
    //             'DistributorDistributorActivity' => array(
    //                 'distributor_id' => $distributor_id,
    //                 'distributor_activity_id' => $activity_id,
    //                 'join_date' => ($distributor_json['LVJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['LVJoinDate'],0, -2), 6),0,10)) : null,
    //                 'left_date' => ($distributor_json['LVLeftDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['LVLeftDate'],0, -2), 6),0,10)) : null,
    //             )
    //         );
    //         $this->create();
    //         if(!$this->save($distributor_distributor_activity_tmp)){
    //             CakeLog::write('updates', 'LV activity creation failed'. PHP_EOL);
    //         }

    //     }

    //     if($distributor_json['CV'] == true){
    //         $activity_id = 2;
    //         $distributor_distributor_activity_tmp = array(
    //             'DistributorDistributorActivity' => array(
    //                 'distributor_id' => $distributor_id,
    //                 'distributor_activity_id' => $activity_id,
    //                 'join_date' => ($distributor_json['CVJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['CVJoinDate'],0, -2), 6),0,10)) : null,
    //                 'left_date' => ($distributor_json['CVLeftDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['CVLeftDate'],0, -2), 6),0,10)) : null,
    //             )
    //         );
    //         $this->create();
    //         if(!$this->save($distributor_distributor_activity_tmp)){
    //             CakeLog::write('updates', 'CV activity creation failed'. PHP_EOL);
    //         }
    //     }

    //     if($distributor_json['Refinish'] == true){
    //         $activity_id = 4;
    //         $distributor_distributor_activity_tmp = array(
    //             'DistributorDistributorActivity' => array(
    //                 'distributor_id' => $distributor_id,
    //                 'distributor_activity_id' => $activity_id,
    //                 'join_date' => ($distributor_json['RefinishJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['RefinishJoinDate'],0, -2), 6),0,10)) : null,
    //                 'left_date' => ($distributor_json['RefinishDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['RefinishDate'],0, -2), 6),0,10)) : null,
    //             )
    //         );
    //         $this->create();
    //         if(!$this->save($distributor_distributor_activity_tmp)){
    //             CakeLog::write('updates', 'Refinish activity creation failed'. PHP_EOL);
    //         }
    //     }

    //     if($distributor_json['Retail'] == true){
    //         $activity_id = 5;
    //         $distributor_distributor_activity_tmp = array(
    //             'DistributorDistributorActivity' => array(
    //                 'distributor_id' => $distributor_id,
    //                 'distributor_activity_id' => $activity_id,
    //                 'join_date' => ($distributor_json['RetailJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['RetailJoinDate'],0, -2), 6),0,10)) : null,
    //                 'left_date' => ($distributor_json['RetailLeftDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['RetailLeftDate'],0, -2), 6),0,10)) : null,
    //             )
    //         );
    //         $this->create();
    //         if(!$this->save($distributor_distributor_activity_tmp)){
    //             CakeLog::write('updates', 'Retail activity creation failed'. PHP_EOL);
    //         }
    //     }

    //     if($distributor_json['Service'] == true){
    //         $activity_id = 3;
    //         $distributor_distributor_activity_tmp = array(
    //             'DistributorDistributorActivity' => array(
    //                 'distributor_id' => $distributor_id,
    //                 'distributor_activity_id' => $activity_id,
    //                 'join_date' => ($distributor_json['ServiceJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['ServiceJoinDate'],0, -2), 6),0,10)) : null,
    //                 'left_date' => ($distributor_json['ServiceLeftDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['ServiceLeftDate'],0, -2), 6),0,10)) : null,
    //             )
    //         );
    //         $this->create();
    //         if(!$this->save($distributor_distributor_activity_tmp)){
    //             CakeLog::write('updates', 'Service activity creation failed'. PHP_EOL);
    //         }
    //     }
    // }

    // public function updateDistributorDistributorActivity( $distributor_json , $distributor_id ){ // The Distributor Activities comes from a UK JSON

    //     $all_activities = $this->findAllByDistributorId( $distributor_id );

    //     if($distributor_json['LV'] == true){
    //         $activity_id = 1;
    //         foreach($all_activities as $activity){
    //             if( $activity['DistributorDistributorActivity']['distributor_activity_id'] == $activity_id ){
    //                 $update_activity = $activity['DistributorDistributorActivity']['id'];
    //             }
    //         }

    //         if( isset($update_activity) ){
    //             $distributor_distributor_activity_tmp = array(
    //                 'DistributorDistributorActivity' => array(
    //                     'id' => $update_activity,
    //                     'distributor_id' => $distributor_id,
    //                     'distributor_activity_id' => $activity_id,
    //                     'join_date' => ($distributor_json['LVJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['LVJoinDate'],0, -2), 6),0,10)) : null,
    //                     'left_date' => ($distributor_json['LVLeftDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['LVLeftDate'],0, -2), 6),0,10)) : null,
    //                 )
    //             );
    //             if(!$this->save($distributor_distributor_activity_tmp)){
    //                 CakeLog::write('updates', 'LV activity update failed'. PHP_EOL);
    //             }
    //         }
    //         else{
    //             $distributor_distributor_activity_tmp = array(
    //                 'DistributorDistributorActivity' => array(
    //                     'distributor_id' => $distributor_id,
    //                     'distributor_activity_id' => $activity_id,
    //                     'join_date' => ($distributor_json['LVJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['LVJoinDate'],0, -2), 6),0,10)) : null,
    //                     'left_date' => ($distributor_json['LVLeftDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['LVLeftDate'],0, -2), 6),0,10)) : null,
    //                 )
    //             );
    //             $this->create();
    //             if(!$this->save($distributor_distributor_activity_tmp)){
    //                 CakeLog::write('updates', 'LV activity creation failed'. PHP_EOL);
    //             }
    //         }
    //     }

    //     if($distributor_json['CV'] == true){
    //         $activity_id = 2;
    //         foreach($all_activities as $activity){
    //             if( $activity['DistributorDistributorActivity']['distributor_activity_id'] == $activity_id ){
    //                 $update_activity = $activity['DistributorDistributorActivity']['id'];
    //             }
    //         }

    //         if( isset($update_activity)){
    //             $distributor_distributor_activity_tmp = array(
    //                 'DistributorDistributorActivity' => array(
    //                     'id' => $update_activity,
    //                     'distributor_id' => $distributor_id,
    //                     'distributor_activity_id' => $activity_id,
    //                     'join_date' => ($distributor_json['CVJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['CVJoinDate'],0, -2), 6),0,10)) : null,
    //                     'left_date' => ($distributor_json['CVLeftDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['CVLeftDate'],0, -2), 6),0,10)) : null,
    //                 )
    //             );
    //             if(!$this->save($distributor_distributor_activity_tmp)){
    //                 CakeLog::write('updates', 'CV activity update failed'. PHP_EOL);
    //             }
    //         }
    //         else{
    //             $distributor_distributor_activity_tmp = array(
    //                 'DistributorDistributorActivity' => array(
    //                     'distributor_id' => $distributor_id,
    //                     'distributor_activity_id' => $activity_id,
    //                     'join_date' => ($distributor_json['CVJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['CVJoinDate'],0, -2), 6),0,10)) : null,
    //                     'left_date' => ($distributor_json['CVLeftDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['CVLeftDate'],0, -2), 6),0,10)) : null,
    //                 )
    //             );
    //             $this->create();
    //             if(!$this->save($distributor_distributor_activity_tmp)){
    //                 CakeLog::write('updates', 'CV activity creation failed'. PHP_EOL);
    //             }
    //         }
    //     }

    //     if($distributor_json['Refinish'] == true){
    //         $activity_id = 4;
    //         foreach($all_activities as $activity){
    //             if( $activity['DistributorDistributorActivity']['distributor_activity_id'] == $activity_id ){
    //                 $update_activity = $activity['DistributorDistributorActivity']['id'];
    //             }
    //         }

    //         if(isset($update_activity)){
    //             $distributor_distributor_activity_tmp = array(
    //                 'DistributorDistributorActivity' => array(
    //                     'id' => $update_activity,
    //                     'distributor_id' => $distributor_id,
    //                     'distributor_activity_id' => $activity_id,
    //                     'join_date' => ($distributor_json['RefinishJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['RefinishJoinDate'],0, -2), 6),0,10)) : null,
    //                     'left_date' => ($distributor_json['RefinishDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['RefinishDate'],0, -2), 6),0,10)) : null,
    //                 )
    //             );
    //             if(!$this->save($distributor_distributor_activity_tmp)){
    //                 CakeLog::write('updates', 'Refinish activity update failed'. PHP_EOL);
    //             }
    //         }
    //         else{
    //             $distributor_distributor_activity_tmp = array(
    //                 'DistributorDistributorActivity' => array(
    //                     'distributor_id' => $distributor_id,
    //                     'distributor_activity_id' => $activity_id,
    //                     'join_date' => ($distributor_json['RefinishJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['RefinishJoinDate'],0, -2), 6),0,10)) : null,
    //                     'left_date' => ($distributor_json['RefinishDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['RefinishDate'],0, -2), 6),0,10)) : null,
    //                 )
    //             );
    //             $this->create();
    //             if(!$this->save($distributor_distributor_activity_tmp)){
    //                 CakeLog::write('updates', 'Refinish activity creation failed'. PHP_EOL);
    //             }
    //         }
    //     }

    //     if($distributor_json['Retail'] == true){
    //         $activity_id = 5;
    //         foreach($all_activities as $activity){
    //             if( $activity['DistributorDistributorActivity']['distributor_activity_id'] == $activity_id ){
    //                 $update_activity = $activity['DistributorDistributorActivity']['id'];
    //             }
    //         }

    //         if(isset($update_activity)){
    //             $distributor_distributor_activity_tmp = array(
    //                 'DistributorDistributorActivity' => array(
    //                     'id' => $update_activity,
    //                     'distributor_id' => $distributor_id,
    //                     'distributor_activity_id' => $activity_id,
    //                     'join_date' => ($distributor_json['RetailJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['RetailJoinDate'],0, -2), 6),0,10)) : null,
    //                     'left_date' => ($distributor_json['RetailLeftDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['RetailLeftDate'],0, -2), 6),0,10)) : null,
    //                 )
    //             );
    //             if(!$this->save($distributor_distributor_activity_tmp)){
    //                 CakeLog::write('updates', 'Retail activity update failed'. PHP_EOL);
    //             }
    //         }
    //         else{
    //             $distributor_distributor_activity_tmp = array(
    //                 'DistributorDistributorActivity' => array(
    //                     'distributor_id' => $distributor_id,
    //                     'distributor_activity_id' => $activity_id,
    //                     'join_date' => ($distributor_json['RetailJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['RetailJoinDate'],0, -2), 6),0,10)) : null,
    //                     'left_date' => ($distributor_json['RetailLeftDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['RetailLeftDate'],0, -2), 6),0,10)) : null,
    //                 )
    //             );
    //             $this->create();
    //             if(!$this->save($distributor_distributor_activity_tmp)){
    //                 CakeLog::write('updates', 'Retail activity creation failed'. PHP_EOL);
    //             }
    //         }
    //     }

    //     if($distributor_json['Service'] == true){
    //         $activity_id = 3;
    //         foreach($all_activities as $activity){
    //             if( $activity['DistributorDistributorActivity']['distributor_activity_id'] == $activity_id ){
    //                 $update_activity = $activity['DistributorDistributorActivity']['id'];
    //             }
    //         }

    //         if(isset($update_activity)){
    //             $distributor_distributor_activity_tmp = array(
    //                 'DistributorDistributorActivity' => array(
    //                     'id' => $update_activity,
    //                     'distributor_id' => $distributor_id,
    //                     'distributor_activity_id' => $activity_id,
    //                     'join_date' => ($distributor_json['ServiceJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['ServiceJoinDate'],0, -2), 6),0,10)) : null,
    //                     'left_date' => ($distributor_json['ServiceLeftDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['ServiceLeftDate'],0, -2), 6),0,10)) : null,
    //                 )
    //             );
    //             if(!$this->save($distributor_distributor_activity_tmp)){
    //                 CakeLog::write('updates', 'Service activity update failed'. PHP_EOL);
    //             }
    //         }
    //         else{
    //             $distributor_distributor_activity_tmp = array(
    //                 'DistributorDistributorActivity' => array(
    //                     'distributor_id' => $distributor_id,
    //                     'distributor_activity_id' => $activity_id,
    //                     'join_date' => ($distributor_json['ServiceJoinDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['ServiceJoinDate'],0, -2), 6),0,10)) : null,
    //                     'left_date' => ($distributor_json['ServiceLeftDate']) ? date('Y-m-d',substr(substr(substr($distributor_json['ServiceLeftDate'],0, -2), 6),0,10)) : null,
    //                 )
    //             );
    //             $this->create();
    //             if(!$this->save($distributor_distributor_activity_tmp)){
    //                 CakeLog::write('updates', 'Service activity creation failed'. PHP_EOL);
    //             }
    //         }
    //     }
    // }

}