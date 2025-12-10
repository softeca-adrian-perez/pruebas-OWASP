<?php

class GarageService extends AppModel{

    public $useTable = 'garages_services';

    var $hasOne = array(
        'Service',
    );

    public function findServices( $garage_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Service',
                        'table' => 'services',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Service.id = GarageService.service_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageService.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'GarageService.*, Service.*'
                ),
            )
        );
    }

    public function removeGarageServices( $garage_id ){
        $services = $this->findAllByGarageId( $garage_id );

        foreach ($services as $service) {
            $this->delete($service['GarageService']['id']);
        }
    }

    public function addRelationGarageService( $garage_id , $service_id ){
        $model = array(
            'garage_id' => $garage_id,
            'service_id' => $service_id
        );

        $this->create();
        if($this->save($model)){
            return true;
        }else{
            return false;
        }
    }

    public function findServicesByGarage( $garage_id ){
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Service',
                        'table' => 'services',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Service.id = GarageService.service_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageService.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'GarageService.service_id'
                ),
            )
        );
    }

    public function getList( $old_data, $new_data, $user, $garage_id ){
        $old_services = "";
        $new_services = "";
        foreach ( $old_data as $key => $service ){
            $tmp_service = $this->Service->findById($service['GarageService']['service_id']);
            $old_services .= $tmp_service['Service']['name_' . __l()] . ", ";
        }
        foreach ( $new_data as $key => $service ){
            $tmp_service = $this->Service->findById($service['GarageService']['service_id']);
            $new_services .= $tmp_service['Service']['name_' . __l()] . ", ";
        }
        $old_services = rtrim($old_services, ', ');
        $new_services = rtrim($new_services, ', ');

        $this->LogChange = ClassRegistry::init('LogChange');
        $this->LogChange->get_params_create_log_service( $this->table, 'service_id', $user, $garage_id, $old_services, $new_services, ConstantsLogType::GARAGE );
    }

    public function createGarageService( $garage_json, $garage_id, &$errors ){ //The data comes from a Json left on the server

        $this->Service = ClassRegistry::init('Service');
        foreach($garage_json['Services'] as $service){
            $service_tmp = $this->Service->findByNameEn($service['SM_Name']);

            if(!$service_tmp){
                $service_bd = array(
                    'Service' => array(
                        'name_en' => $service['SM_Name'],
                        'name_fr' => $service['SM_Name'],
                        'name_de' => $service['SM_Name'],
                        'url' => 'no-image.png',
                    )
                );

                $this->Service->create();
                $service_tmp = $this->Service->save($service_bd);
                if(!$service_tmp){
                    CakeLog::write('updates', 'The '.$service['SM_Name'].' service could not be created'. PHP_EOL);
					$errors['The '.$service['SM_Name'].' service could not be created'] = translateDataErrors($this->Service->validationErrors);
                }
            }

            $garage_service_tmp = array(
                'GarageService' => array(
                    'garage_id' => $garage_id,
                    'service_id' => $service_tmp['Service']['id'],
                )
            );

            $this->create();
            if(!$this->save($garage_service_tmp)){
                CakeLog::write('updates', 'The link to the service could not be created'. PHP_EOL);
				$errors['The link to the service could not be created'] = translateDataErrors($this->validationErrors);
            }

        }
        return true;
    }

    public function updateGarageService( $garage_json, $garage_exist_id, &$errors ){ //The data comes from a Json left on the server

        $garages_service_exist = $this->findServicesByGarage( $garage_exist_id );

        $this->Service = ClassRegistry::init('Service');
        foreach($garage_json['Services'] as $service){
            $service_tmp = $this->Service->findByNameEn($service['SM_Name']);

            if(!$service_tmp){ //If it doesn't exist, I believe it and relate it.
                $service_bd = array(
                    'Service' => array(
                        'name_en' => $service['SM_Name'],
                        'name_fr' => $service['SM_Name'],
                        'name_de' => $service['SM_Name'],
                        'url' => 'no-image.png',
                    )
                );

                $service->create();
                $service_tmp = $service->save($service_bd);
                if(!$service_tmp){
                    CakeLog::write('updates', 'The '.$service['SM_Name'].' service could not be created'. PHP_EOL);
					$errors['The '.$service['SM_Name'].' service could not be created'] = translateDataErrors($service->validationErrors);
                }

                $garage_service_tmp = array(
                    'GarageService' => array(
                        'garage_id' => $garage_exist_id,
                        'service_id' => $service_tmp['Service']['id'],
                    )
                );

                $this->create();
                if(!$this->save($garage_service_tmp)){
                    CakeLog::write('updates', 'The link to service '. $service['SM_Name'].' could not be created'. PHP_EOL);
					$errors['The link to service '. $service['SM_Name'].' could not be created'] = translateDataErrors($this->validationErrors);
                }

            }
            else{
                if(!in_array($service_tmp['Service']['id'], $garages_service_exist)){ //If it wasn't related, I link it
                    $garage_service_tmp = array(
                        'GarageService' => array(
                            'garage_id' => $garage_exist_id,
                            'service_id' => $service_tmp['Service']['id'],
                        )
                    );

                    $this->create();
                    if(!$this->save($garage_service_tmp)){
                        CakeLog::write('updates', 'The link to service '. $service['SM_Name'].' could not be created'. PHP_EOL);
						$errors['The link to service '. $service['SM_Name'].' could not be created'] = translateDataErrors($this->validationErrors);
                    }
                }
                else{ //If it's already related, I don't do anything.
                    $key = array_search($service_tmp['Service']['id'], $garages_service_exist);
                    unset($garages_service_exist[$key]);
                }
            }
        }

        //remove services that have not arrived through JSON
        foreach($garages_service_exist as $key => $service_id){
            $this->delete($key);
        }

        $this->commit();
        return true;
    }

    public function createRepService( $garage_json, $garage_id ){ //The data comes from a Json left on the FR server
        $this->Service = ClassRegistry::init('Service');
        if( isset($garage_json['activite']) ){
            foreach( $garage_json['activite'] as $service ){

                $service_tmp = $this->Service->findByid($service['id_isa']);

                if( empty($service_tmp) ){

                    $service_bd = array(
                        'Service' => array(
                            'name_en' => $service['nom'],
                            'name_fr' => $service['nom'],
                            'name_de' => $service['nom'],
                            'url' => 'no-image.png',
                        )
                    );

                    $this->Service->create();
                    $service_tmp = $this->Service->save($service_bd);
                    if( !$service_tmp ){
                        CakeLog::write('updates-france', 'The service could not be created.'. PHP_EOL);
                    }
                }

                $service_tmp_id = $service_tmp['Service']['id'];

                if($service['actif'] == true){
                    $garage_service_exist = $this->findByGarageIdAndServiceId($garage_id, $service_tmp_id);
                    if(empty($garage_service_exist)){
                        $garage_service_tmp = array(
                            'GarageService' => array(
                                'garage_id' => $garage_id,
                                'service_id' => $service_tmp_id,
                            )
                        );

                        $this->create();
                        if( !$this->save($garage_service_tmp)){
                            CakeLog::write('updates-france', 'The Garage Service could not be created.'. PHP_EOL);
                        }
                    }
                }

            }
        }

        return true;
    }

    public function updateRepService( $garage_json, $garage ){ //The data comes from a Json left on the FR server
        $this->Service = ClassRegistry::init('Service');
        if( isset($garage_json['activite']) ){
            foreach( $garage_json['activite'] as $service ){
                $service_tmp = $this->Service->findByid($service['id_isa']);

                if( empty($service_tmp) ){

                    $service_bd = array(
                        'Service' => array(
                            'name_en' => $service['nom'],
                            'name_fr' => $service['nom'],
                            'name_de' => $service['nom'],
                            'url' => 'no-image.png',
                        )
                    );

                    $this->Service->create();
                    $service_tmp = $this->Service->save($service_bd);
                    if( !$service_tmp ){
                        CakeLog::write('updates-france', 'The service could not be created.'. PHP_EOL);
                    }
                }

                $garage_service_exit = $this->findByGarageIdAndServiceId( $garage['Garage']['id'], $service_tmp['Service']['id'] );
                if( !$garage_service_exit && $service['actif'] == true){
                    $garage_service_tmp = array(
                        'GarageService' => array(
                            'garage_id' => $garage['Garage']['id'],
                            'service_id' => $service_tmp['Service']['id'],
                        )
                    );

                    $this->create();
                    if( !$this->save($garage_service_tmp)){
                        CakeLog::write('updates-france', 'The Garage Service could not be created.'. PHP_EOL);
                    }
                }

            }
        }

        return true;
    }
}
?>
