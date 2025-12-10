<?php

class DistributorDistributorActivityDe extends AppModel{

    public $useDbConfig = 'gnmaag_de';
    public $useTable = 'distributors_distributors_activities';

    var $hasMany = array(
        'DistributorActivityDe'
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
                            'Distributor.id = DistributorDistributorActivityDe.distributor_id',
                        ),
                    ),
                    array(
                        'alias' => 'DistributorActivityDe',
                        'table' => 'distributors_activities',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorActivityDe.id = DistributorDistributorActivityDe.distributor_activity_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorDistributorActivityDe.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'DistributorDistributorActivityDe.*',
                    'DistributorActivityDe.name_' . __l()
                ),
            )
        );
    }

}