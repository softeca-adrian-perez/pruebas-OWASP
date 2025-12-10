<?php

class GarageSpecialistMake extends AppModel{

    public $useTable = 'garages_specialist_makes';

    /**
     * Find the vehicles specialist makes to each garage
     * @param $garage_id
     *
     * @return array
     */
    public function findVehicles( $garage_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Vehicle',
                        'table' => 'vehicles',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Vehicle.id = GarageSpecialistMake.vehicle_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageSpecialistMake.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'GarageSpecialistMake.*, Vehicle.*'
                ),
            )
        );
    }

    public function removeGarageSpecialists( $garage_id ){
        $vehicles = $this->findAllByGarageId( $garage_id );

        foreach ($vehicles as $vehicle) {
            $this->delete($vehicle['GarageSpecialistMake']['id']);
        }
    }

    public function removeSpecialistFromGarages( $vehicle_id ){
        $garages = $this->findAllByGarageId( $vehicle_id );

        foreach ($garages as $garage) {
            $this->delete($garage['GarageSpecialistMake']['id']);
        }
    }

    public function addRelationGarageSpecialists( $garage_id , $vehicle_id ){
        $model = array(
            'garage_id' => $garage_id,
            'vehicle_id' => $vehicle_id
        );

        $this->create();
        if($this->save($model)){
            return true;
        }else{
            return false;
        }
    }

    public function findVehiclesSpecialistByGarage( $garage_id ){
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Vehicle',
                        'table' => 'vehicles',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Vehicle.id = GarageSpecialistMake.vehicle_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageSpecialistMake.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Vehicle.id'
                ),
            )
        );
    }

}
?>