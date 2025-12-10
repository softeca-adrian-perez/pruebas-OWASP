<?php

class GarageCourtesyCarType extends AppModel{
    public $useTable = 'garages_courtesy_car_types';

    public function findGarageCourtesyCarTypes( $garage_id , $field ){
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'CourtesyCarType',
                        'table' => 'courtesy_car_types',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CourtesyCarType.id = GarageCourtesyCarType.courtesy_car_type_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageCourtesyCarType.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'CourtesyCarType.'.$field,
                ),
            )
        );
    }

    public function addRelationGarageCourtesyCarType( $garage_id , $courtesy_car_type_id ){
        $model = array(
            'garage_id' => $garage_id,
            'courtesy_car_type_id' => $courtesy_car_type_id
        );

        $this->create();
        if($this->save($model)){
            return true;
        }else{
            return false;
        }
    }

    public function removeGarageCourtesyCarTypes( $garage_id ){
        $garage_courtesy_car_types = $this->findAllByGarageId( $garage_id );

        foreach ($garage_courtesy_car_types as $garage_courtesy_car_type) {
            $this->delete($garage_courtesy_car_type['GarageCourtesyCarType']['id']);
        }
    }

}