<?php

class DistributorDistributorActivityUk extends AppModel{

    public $useDbConfig = 'gnmaag_uk';
    public $useTable = 'distributors_distributors_activities';

    var $hasMany = array(
        'DistributorActivityUk'
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
                            'Distributor.id = DistributorDistributorActivityUk.distributor_id',
                        ),
                    ),
                    array(
                        'alias' => 'DistributorActivityUk',
                        'table' => 'distributors_activities',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorActivityUk.id = DistributorDistributorActivityUk.distributor_activity_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorDistributorActivityUk.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'DistributorDistributorActivityUk.*',
                    'DistributorActivityUk.name_' . __l()
                ),
            )
        );
    }

}