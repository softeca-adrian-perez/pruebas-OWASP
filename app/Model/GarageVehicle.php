<?php

class GarageVehicle extends AppModel{

    public $useTable = 'garages_vehicles';

    /**
     * Find the vehicles related to each garage
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
                            'Vehicle.id = GarageVehicle.vehicle_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageVehicle.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'GarageVehicle.*, Vehicle.*'
                ),
            )
        );
    }

    public function removeGarageVehicles( $garage_id ){
        $vehicles = $this->findAllByGarageId( $garage_id );

        foreach ($vehicles as $vehicle) {
            $this->delete($vehicle['GarageVehicle']['id']);
        }
    }
    public function removeVehicleFromGarages( $vehicle_id ){
        $garages = $this->findAllByGarageId( $vehicle_id );

        foreach ($garages as $garage) {
            $this->delete($garage['GarageVehicle']['id']);
        }
    }

    public function addRelationGarageVehicle( $garage_id , $vehicle_id ){
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


    public function findVehiclesByGarage( $garage_id ){
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Vehicle',
                        'table' => 'vehicles',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Vehicle.id = GarageVehicle.vehicle_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageVehicle.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Vehicle.id'
                ),
            )
        );
    }

    public function createGarageVehicle( $garage_json , $garage_id, &$errors ){ //The data comes from a Json left on the server

        $vehicle = ClassRegistry::init('Vehicle');
        foreach($garage_json['Makes'] as $make){
            $vehicle_tmp = $vehicle->findByNameEn($make['mm_name']);

            if(!$vehicle_tmp){
                $vehicle_bd = array(
                    'Vehicle' => array(
                        'name_en' => $make['mm_name'],
                        'name_fr' => $make['mm_name'],
                        'name_de' => $make['mm_name'],
                    )
                );

                $vehicle->create();
                $vehicle_tmp = $vehicle->save($vehicle_bd);
                if(!$vehicle_tmp){
                    CakeLog::write('updates', 'Unable to create '.$make['mm_name'].' vehicle'. PHP_EOL);
					$errors['Unable to create '.$make['mm_name'].' vehicle'] = translateDataErrors($vehicle->validationErrors);
                }
            }

            $garage_vehicle_tmp = array(
                'GarageVehicle' => array(
                    'garage_id' => $garage_id,
                    'vehicle_id' => $vehicle_tmp['Vehicle']['id'],
                )
            );

            $this->create();
            if(!$this->save($garage_vehicle_tmp)){
                CakeLog::write('updates', 'It was not possible to create the link with the vehicle marker'. PHP_EOL);
				$errors['It was not possible to create the link with the vehicle marker'] = translateDataErrors($this->validationErrors);
            }

            if($make['GM_IsSpecialist']){
                $garage_specialist_tmp = array(
                    'GarageSpecialistMake' => array(
                        'garage_id' => $garage_id,
                        'vehicle_id' => $vehicle_tmp['Vehicle']['id'],
                    )
                );

                $garage_specialist = ClassRegistry::init('GarageSpecialistMake');
                $garage_specialist->create();
                if(!$garage_specialist->save($garage_specialist_tmp)){
                    CakeLog::write('updates', 'It was not possible to create the link with the specialist brand of the vehicle'. PHP_EOL);
					$errors['It was not possible to create the link with the specialist brand of the vehicle'] = translateDataErrors($garage_specialist->validationErrors);
                }

            }
        }
        return true;

    }

    public function updateGarageVehicle( $garage_json , $garage_exist_id, &$errors ){ //The data comes from a Json left on the server

        $this->GarageSpecialistMake = ClassRegistry::init('GarageSpecialistMake');
        $this->Vehicle = ClassRegistry::init('Vehicle');
        $garages_vehicle_exist = $this->findVehiclesByGarage( $garage_exist_id );
        $garages_vehicle_specialist_exist = $this->GarageSpecialistMake->findVehiclesSpecialistByGarage( $garage_exist_id );

        foreach($garage_json['Makes'] as $make){
            $vehicle_tmp = $this->Vehicle->findByNameEn($make['mm_name']);
            if(!$vehicle_tmp){ //If it doesn't exist, I believe it and relate it.
                $vehicle_bd = array(
                    'Vehicle' => array(
                        'name_en' => $make['mm_name'],
                        'name_fr' => $make['mm_name'],
                        'name_de' => $make['mm_name'],
                    )
                );
                $this->Vehicle->create();
                $vehicle_tmp = $this->Vehicle->save($vehicle_bd);
                if(!$vehicle_tmp){
                    CakeLog::write('updates', 'Unable to create '.$make['mm_name'].' vehicle'. PHP_EOL);
					$errors['Unable to create '.$make['mm_name'].' vehicle'] = translateDataErrors($this->Vehicle->validationErrors);
                }

                $garage_vehicle_tmp = array(
                    'GarageVehicle' => array(
                        'garage_id' => $garage_exist_id,
                        'vehicle_id' => $vehicle_tmp['Vehicle']['id'],
                    )
                );
                $this->create();
                if(!$this->save($garage_vehicle_tmp)){
                    CakeLog::write('updates', 'Unable to link to '.$make['mm_name'].' vehicle'. PHP_EOL);
					$errors['Unable to link to '.$make['mm_name'].' vehicle'] = translateDataErrors($this->validationErrors);
                }

                if($make['GM_IsSpecialist']){ //If it doesn't exist, I believe it and relate it.
                    $garage_specialist_tmp = array(
                        'GarageSpecialistMake' => array(
                            'garage_id' => $garage_exist_id,
                            'vehicle_id' => $vehicle_tmp['Vehicle']['id'],
                        )
                    );
                    $this->GarageSpecialistMake->create();
                    if(!$this->GarageSpecialistMake->save($garage_specialist_tmp)){
                        CakeLog::write('updates', 'It was not possible to create the link with the specialized brand '.$make['mm_name']. PHP_EOL);
						$errors['It was not possible to create the link with the specialized brand '.$make['mm_name']] = translateDataErrors($this->GarageSpecialistMake->validationErrors);
                    }
                }
            }
            else{
                if(!in_array($vehicle_tmp['Vehicle']['id'], $garages_vehicle_exist)){ //If it wasn't related, I link it
                    $garage_vehicle_tmp = array(
                        'GarageVehicle' => array(
                            'garage_id' => $garage_exist_id,
                            'vehicle_id' => $vehicle_tmp['Vehicle']['id'],
                        )
                    );
                    $this->create();
                    if(!$this->save($garage_vehicle_tmp)){
                        CakeLog::write('updates', 'Unable to link to '.$make['mm_name'].' vehicle'. PHP_EOL);
						$errors['Unable to link to '.$make['mm_name'].' vehicle'] = translateDataErrors($this->validationErrors);
                    }

                    if($make['GM_IsSpecialist']){ //If it doesn't exist, I believe it and relate it.
                        $garage_specialist_tmp = array(
                            'GarageSpecialistMake' => array(
                                'garage_id' => $garage_exist_id,
                                'vehicle_id' => $vehicle_tmp['Vehicle']['id'],
                            )
                        );

                        $this->GarageSpecialistMake->create();
                        if(!$this->GarageSpecialistMake->save($garage_specialist_tmp)){
                            CakeLog::write('updates', 'It was not possible to create the link with the specialized brand '.$make['mm_name']. PHP_EOL);
							$errors['It was not possible to create the link with the specialized brand '.$make['mm_name']] = translateDataErrors($this->GarageSpecialistMake->validationErrors);
                        }
                    }
                }
                else{ //If it's already related, I don't do anything.
                    $key = array_search($vehicle_tmp['Vehicle']['id'], $garages_vehicle_exist);
                    unset($garages_vehicle_exist[$key]);
                    //If it exists, I'm looking for a specialist or not
                    if($make['GM_IsSpecialist']) { //If it doesn't exist, I believe it and relate it.
                        if(!in_array($vehicle_tmp['Vehicle']['id'], $garages_vehicle_specialist_exist)) { //If it wasn't related, I link it
                            $garage_specialist_tmp = array(
                                'GarageSpecialistMake' => array(
                                    'garage_id' => $garage_exist_id,
                                    'vehicle_id' => $vehicle_tmp['Vehicle']['id'],
                                )
                            );
                            $this->GarageSpecialistMake->create();
                            if(!$this->GarageSpecialistMake->save($garage_specialist_tmp)){
                                CakeLog::write('updates', 'It was not possible to create the link with the specialized brand '.$make['mm_name']. PHP_EOL);
								$errors['It was not possible to create the link with the specialized brand '.$make['mm_name']] = translateDataErrors($this->GarageSpecialistMake->validationErrors);
                            }
                        }
                        else{ //If it's already related, I don't do anything.
                            $key = array_search($vehicle_tmp['Vehicle']['id'], $garages_vehicle_specialist_exist);
                            unset($garages_vehicle_specialist_exist[$key]);
                        }
                    }
                }
            }
        }

        //remove vehicles that have not arrived through JSON
        foreach($garages_vehicle_exist as $key => $vehicle_id){
            $this->delete($key);
        }
        //remove vehicles specialist that have not arrived through JSON
        foreach($garages_vehicle_specialist_exist as $key => $vehicle_specialist_id){
            $this->GarageSpecialistMake->delete($key);
        }

        return true;
    }

}
?>
