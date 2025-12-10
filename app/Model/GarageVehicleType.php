<?php

class GarageVehicleType extends AppModel{

    public $useTable = 'garages_vehicle_types';

    /**
     * Find the vehicle types related to each garage
     * @param $garage_id
     *
     * @return array
     */
    public function findVehicleTypes( $garage_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'VehicleType',
                        'table' => 'vehicle_types',
                        'type' => 'INNER',
                        'conditions' => array(
                            'VehicleType.id = GarageVehicleType.vehicle_type_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageVehicleType.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'GarageVehicleType.*, VehicleType.*'
                ),
            )
        );
    }

    public function removeGarageVehiclesTypes( $garage_id ){
        $vehicles_types = $this->findAllByGarageId( $garage_id );
        foreach ($vehicles_types as $vehicle_type) {
            $this->delete($vehicle_type['GarageVehicleType']['id']);
        }
    }

    public function addRelationGarageVehicleType( $garage_id , $vehicle_type_id ){
        $model = array(
            'garage_id' => $garage_id,
            'vehicle_type_id' => $vehicle_type_id
        );

        $this->create();
        if($this->save($model)){
            return true;
        }else{
            return false;
        }
    }

    public function findVehicleTypesByGarage( $garage_id ){
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'VehicleType',
                        'table' => 'vehicle_types',
                        'type' => 'INNER',
                        'conditions' => array(
                            'VehicleType.id = GarageVehicleType.vehicle_type_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageVehicleType.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'VehicleType.id'
                ),
            )
        );
    }


    public function createGarageVehicleType( $garage_json , $garage_id, &$errors ){ //The data comes from a Json left on the server
        /////4x4
        if($garage_json['Garage']['C4WD']){
            $garage_vehicle_tmp = array(
                'GarageVehicleType' => array(
                    'garage_id' => $garage_id,
                    'vehicle_type_id' => 2,
                )
            );
            $this->create();
            if(!$this->save($garage_vehicle_tmp)){
                CakeLog::write('updates', 'The link to vehicle type C4WD could not be created'. PHP_EOL);
				$errors['The link to vehicle type C4WD could not be created'] = translateDataErrors($this->validationErrors);
            }
        }
        if($garage_json['Garage']['LCV']){
            $garage_vehicle_tmp = array(
                'GarageVehicleType' => array(
                    'garage_id' => $garage_id,
                    'vehicle_type_id' => 1,
                )
            );
            $this->create();
            if(!$this->save($garage_vehicle_tmp)){
                CakeLog::write('updates', 'The link to vehicle type LCV could not be created'. PHP_EOL);
				$errors['The link to vehicle type LCV could not be created'] = translateDataErrors($this->validationErrors);
            }
        }
        if($garage_json['Garage']['Hybrid']){
            $garage_vehicle_tmp = array(
                'GarageVehicleType' => array(
                    'garage_id' => $garage_id,
                    'vehicle_type_id' => 4,
                )
            );
            $this->create();
            if(!$this->save($garage_vehicle_tmp)){
                CakeLog::write('updates', 'The link to vehicle type Hybrid could not be created'. PHP_EOL);
				$errors['The link to vehicle type Hybrid could not be created'] = translateDataErrors($this->validationErrors);
            }
        }
        if($garage_json['Garage']['Electric']){
            $garage_vehicle_tmp = array(
                'GarageVehicleType' => array(
                    'garage_id' => $garage_id,
                    'vehicle_type_id' => 3,
                )
            );
            $this->create();
            if(!$this->save($garage_vehicle_tmp)){
                CakeLog::write('updates', 'The link to vehicle type Electric could not be created'. PHP_EOL);
				$errors['The link to vehicle type Electric could not be created'] = translateDataErrors($this->validationErrors);
            }
        }
        $this->commit();
        return true;
    }

    public function updateGarageVehicleType( $garage_json , $garage_exist_id, &$errors = array() ){ //The data comes from a Json left on the server
        $garages_vehicle_type_exist = $this->findVehicleTypesByGarage( $garage_exist_id );

        /////4x4
        if($garage_json['Garage']['C4WD']){
            $vehicle_type = 2;
            if(!in_array($vehicle_type, $garages_vehicle_type_exist)){
                $garage_vehicle_tmp = array(
                    'GarageVehicleType' => array(
                        'garage_id' => $garage_exist_id,
                        'vehicle_type_id' => $vehicle_type,
                    )
                );
                $this->create();
                if(!$this->save($garage_vehicle_tmp)){
                    CakeLog::write('updates', 'The link to vehicle type C4WD could not be created'. PHP_EOL);
					$errors['The link to vehicle type C4WD could not be created'] = translateDataErrors($this->validationErrors);
                }
            }
            else{
                $key = array_search($vehicle_type, $garages_vehicle_type_exist);
                unset($garages_vehicle_type_exist[$key]);
            }
        }

        if($garage_json['Garage']['LCV']){
            $vehicle_type = 1;
            if(!in_array($vehicle_type, $garages_vehicle_type_exist)) {
                $garage_vehicle_tmp = array(
                    'GarageVehicleType' => array(
                        'garage_id' => $garage_exist_id,
                        'vehicle_type_id' => $vehicle_type,
                    )
                );
                $this->create();
                if(!$this->save($garage_vehicle_tmp)){
                    CakeLog::write('updates', 'The link to vehicle type LCV could not be created'. PHP_EOL);
					$errors['The link to vehicle type LCV could not be created'] = translateDataErrors($this->validationErrors);
                }
            }
            else{
                $key = array_search($vehicle_type, $garages_vehicle_type_exist);
                unset($garages_vehicle_type_exist[$key]);
            }

        }
        if($garage_json['Garage']['Hybrid']){
            $vehicle_type = 4;
            if(!in_array($vehicle_type, $garages_vehicle_type_exist)) { //If it wasn't related, I link it
                $garage_vehicle_tmp = array(
                    'GarageVehicleType' => array(
                        'garage_id' => $garage_exist_id,
                        'vehicle_type_id' => $vehicle_type,
                    )
                );
                $this->create();
                if(!$this->save($garage_vehicle_tmp)){
                    CakeLog::write('updates', 'The link to vehicle type Hybrid could not be created'. PHP_EOL);
					$errors['The link to vehicle type Hybrid could not be created'] = translateDataErrors($this->validationErrors);
                }
            }
            else{
                $key = array_search($vehicle_type, $garages_vehicle_type_exist);
                unset($garages_vehicle_type_exist[$key]);
            }

        }
        if($garage_json['Garage']['Electric']){
            $vehicle_type = 3;
            if(!in_array($vehicle_type, $garages_vehicle_type_exist)) {
                $garage_vehicle_tmp = array(
                    'GarageVehicleType' => array(
                        'garage_id' => $garage_exist_id,
                        'vehicle_type_id' => $vehicle_type,
                    )
                );
                $this->create();
                if(!$this->save($garage_vehicle_tmp)){
                    CakeLog::write('updates', 'The link to vehicle type Electric could not be created'. PHP_EOL);
					$errors['The link to vehicle type Electric could not be created'] = translateDataErrors($this->validationErrors);
                }
            }
            else{ //If it's already related, I don't do anything.
                $key = array_search($vehicle_type, $garages_vehicle_type_exist);
                unset($garages_vehicle_type_exist[$key]);
            }

        }

        //remove vehicle's types that have not arrived through the JSON
        foreach($garages_vehicle_type_exist as $key => $vehicle_type_id){
            $this->delete($key);
        }

        $this->commit();
        return true;
    }

}
?>
