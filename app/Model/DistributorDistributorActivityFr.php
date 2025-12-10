<?php

class DistributorDistributorActivityFr extends AppModel{

    public $useDbConfig = 'gnmaag_fr';
    public $useTable = 'distributors_distributors_activities';

    var $hasMany = array(
        'DistributorActivityFr'
    );

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
                            'Distributor.id = DistributorDistributorActivityFr.distributor_id',
                        ),
                    ),
                    array(
                        'alias' => 'DistributorActivityFr',
                        'table' => 'distributors_activities',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorActivityFr.id = DistributorDistributorActivityFr.distributor_activity_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorDistributorActivityFr.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'DistributorDistributorActivityFr.*',
                    'DistributorActivityFr.name_' . __l()
                ),
            )
        );
    }

}