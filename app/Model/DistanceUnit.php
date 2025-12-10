<?php

class DistanceUnit extends AppModel
{
    public $useTable = 'distance_units';

    public function getDistanceUnitNameByUnitDistanceId($unit_distance_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'id =' => $unit_distance_id
            ),
            'fields' => array(
                'unit'
            )
        ));
    }
}
