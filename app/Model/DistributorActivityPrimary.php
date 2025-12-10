<?php

class DistributorActivityPrimary extends AppModel{
    public $useTable = 'distributors_activities_primary';

    public function getDistributorActivityPrimary(){
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'name'.__s()
                ),
            )
        );
    }

}