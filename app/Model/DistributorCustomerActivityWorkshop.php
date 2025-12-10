<?php

class DistributorCustomerActivityWorkshop extends AppModel{

    public $useTable = 'distributors_customer_activities_workshops';

    public function add_activity_workshop( $distributor_customer_activity_id, $workshop_activity_id, $activity_details ){
        $fields = array(
            'DistributorCustomerActivityWorkshop' => array(
                'distributor_customer_activity_id',
                'workshop_activity_id',
                'activity_details',
            )
        );

        $activity_workshop['DistributorCustomerActivityWorkshop']['distributor_customer_activity_id'] = $distributor_customer_activity_id;
        $activity_workshop['DistributorCustomerActivityWorkshop']['workshop_activity_id'] = $workshop_activity_id;
        $activity_workshop['DistributorCustomerActivityWorkshop']['activity_details'] = $activity_details;
        
        $this->create();
        $activity_workshop_bd = $this->guardar($activity_workshop, $fields);
        if ( !$activity_workshop_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    public function createDisActivityWorshop( $distributor_json, $distributor_id ){// New DistributorCustomerActivity comes from FRANCE JSON
        
        if( isset($distributor_json['atelier']) ){
            $this->CustomerActivity = ClassRegistry::init('CustomerActivity');
            $this->WorkshopActivity = ClassRegistry::init('WorkshopActivity');
            $this->DistributorCustomerActivity = ClassRegistry::init('DistributorCustomerActivity');
            $this->DistributorCustomerActivityWorkshop = ClassRegistry::init('DistributorCustomerActivityWorkshop');
    
            foreach($distributor_json['atelier'] as $customer_activity_name => $customer_activity){
                if($customer_activity['actif'] == true){
                    
                    //Customer_activity
                    $customer_activity_bd = $this->CustomerActivity->findByNameFr( $customer_activity_name );
                    $distributor_customer_activity_bd = $this->DistributorCustomerActivity->findByDistributorIdAndCustomerActivityIdAndType( $distributor_id ,  $customer_activity_bd['CustomerActivity']['id'] ,ConstantsBooleans::YES);
                    
                    if( empty($distributor_customer_activity_bd) ){
                        //If the distributor customer activity doesn't exist, we create it.
                        $distributor_customer_activity_tmp = array(
                            'DistributorCustomerActivity' => array(
                                'customer_activity_id' => $customer_activity_bd['CustomerActivity']['id'],
                                'distributor_id' => $distributor_id,
                                'type' => ConstantsBooleans::YES, //ATELIER
                            )
                        );
                        
                        $this->DistributorCustomerActivity->create();
                        $distributor_customer_activity_bd = $this->DistributorCustomerActivity->save($distributor_customer_activity_tmp);
                        if( !$distributor_bd ){
                            CakeLog::write('updates-france', 'The distributor customer activity could not be created.'. PHP_EOL);
                        }
                    }
    
                    $distributor_customer_activity_id = $distributor_customer_activity_bd['DistributorCustomerActivity']['id'];
    
                    foreach($customer_activity['activite'] as $workshop_activity){
                        
                        $workshop_activity_bd = $workshop_activity_model->findByNameFr( $workshop_activity['nom'] );
    
                        //if workshop activity doesn't exists, we create it.
                        if( empty($workshop_activity_bd) ){
                            $workshop_activity_bd = array(
                                'WorkshopActivity' => array(
                                    'name_en' => $workshop_activity['nom'],
                                    'name_fr' => $workshop_activity['nom'],
                                    'name_de' => $workshop_activity['nom'],
                                )
                            );
                            $this->WorkshopActivity->create();
                            $workshop_activity_bd = $this->WorkshopActivity->save($workshop_activity_bd);
                            if( !$workshop_activity_bd ){
                                CakeLog::write('updates-france', 'The Workshop Activity could not be created.'. PHP_EOL);
                            }
                        }
                        $workshop_activity_id = $workshop_activity_bd['WorkshopActivity']['id'];
    
                        $distributor_customer_activity_id = $distributor_customer_activity_bd['DistributorCustomerActivity']['id'];
                        $distributor_customer_activity_workshop_new = array(
                            'DistributorCustomerActivityWorkshop' => array(
                                'distributor_customer_activity_id' => $distributor_customer_activity_id,
                                'workshop_activity_id' => $workshop_activity_id,
                                'activity_details' => isset($workshop_activity['commentaire']) ? $workshop_activity['commentaire'] : null,
                            )
                        );
                        $this->create();
                        $distributor_customer_activity_workshop_bd = $this->save($distributor_customer_activity_workshop_new);
                        if( !$distributor_customer_activity_workshop_bd ){
                            CakeLog::write('updates-france', 'The Distributor Customer Activity Workshop could not be created.'. PHP_EOL);
                        }
                    }
                }
            }
            $this->commit();
        }

        return true;
    } 
    
    public function updateDisActivityWorshop( $distributor_json, $distributor_exist ){// New DistributorCustomerActivity comes from FRANCE JSON

        if( isset($distributor_json['atelier']) ){
            $this->CustomerActivity = ClassRegistry::init('CustomerActivity');
            $this->WorkshopActivity = ClassRegistry::init('WorkshopActivity');
            $this->DistributorCustomerActivity = ClassRegistry::init('DistributorCustomerActivity');
            $this->DistributorCustomerActivityWorkshop = ClassRegistry::init('DistributorCustomerActivityWorkshop');
            foreach($distributor_json['atelier'] as $customer_activity_name => $customer_activity){
                if($customer_activity['actif'] == true){
                    
                    //Customer_activity
                    $customer_activity_bd = $this->CustomerActivity->findByNameFr( $customer_activity_name );
                    $distributor_customer_activity_bd = $this->DistributorCustomerActivity->findByDistributorIdAndCustomerActivityIdAndType( $distributor_exist['Distributor']['id'] ,  $customer_activity_bd['CustomerActivity']['id'] ,ConstantsBooleans::YES);
                    
                    if( !$distributor_customer_activity_bd ){
                        //If the distributor customer activity doesn't exist, we create it.
                        $distributor_customer_activity_tmp = array(
                            'DistributorCustomerActivity' => array(
                                'customer_activity_id' => $customer_activity_bd['CustomerActivity']['id'],
                                'distributor_id' => $distributor_exist['Distributor']['id'],
                                'type' => ConstantsBooleans::YES, //ATELIER
                            )
                        );
                        
                        $this->DistributorCustomerActivity->create();
                        $distributor_customer_activity_bd = $this->DistributorCustomerActivity->save($distributor_customer_activity_tmp);
                        if( !$distributor_customer_activity_bd ){
                            CakeLog::write('updates-france', 'The distributor customer activity could not be created.'. PHP_EOL);
                        }
                    }
    
                    $distributor_customer_activity_id = $distributor_customer_activity_bd['DistributorCustomerActivity']['id'];
                    foreach($customer_activity['activite'] as $workshop_activity){
                        
                        $workshop_activity_bd = $this->WorkshopActivity->findByNameFr( $workshop_activity['nom'] );
                        //if workshop activity doesn't exists, we create it.
                        if( !$workshop_activity_bd ){
                            $workshop_activity_bd = array(
                                'WorkshopActivity' => array(
                                    'name_en' => $workshop_activity['nom'],
                                    'name_fr' => $workshop_activity['nom'],
                                    'name_de' => $workshop_activity['nom'],
                                )
                            );
                            $this->WorkshopActivity->create();
                            $workshop_activity_bd = $this->WorkshopActivity->save($workshop_activity_bd);
                            if( !$workshop_activity_bd ){
                                CakeLog::write('updates-france', 'The Workshop Activity could not be created.'. PHP_EOL);
                            }
                        }

                        $workshop_activity_id = $workshop_activity_bd['WorkshopActivity']['id'];
                        $distributor_customer_activity_workshop_exist = $this->findByDistributorCustomerActivityIdAndWorkshopActivityId( $distributor_customer_activity_id , $workshop_activity_id );

                        if( !$distributor_customer_activity_workshop_exist ){
                            $distributor_customer_activity_id = $distributor_customer_activity_bd['DistributorCustomerActivity']['id'];
                            $distributor_customer_activity_workshop_new = array(
                                'DistributorCustomerActivityWorkshop' => array(
                                    'distributor_customer_activity_id' => $distributor_customer_activity_id,
                                    'workshop_activity_id' => $workshop_activity_id,
                                    'activity_details' => isset($workshop_activity['commentaire']) ? $workshop_activity['commentaire'] : null,
                                )
                            );
                            $this->create();
                            $distributor_customer_activity_workshop_bd = $this->save($distributor_customer_activity_workshop_new);
                            if( !$distributor_customer_activity_workshop_bd ){
                                CakeLog::write('updates-france', 'The Distributor Customer Activity Workshop could not be created.'. PHP_EOL);
                            }
                        }
                        else{
                            $distributor_customer_activity_id = $distributor_customer_activity_bd['DistributorCustomerActivity']['id'];
                            $distributor_customer_activity_workshop_new = array(
                                'DistributorCustomerActivityWorkshop' => array(
                                    'id' => $distributor_customer_activity_workshop_exist['DistributorCustomerActivityWorkshop']['id'],
                                    'distributor_customer_activity_id' => $distributor_customer_activity_id,
                                    'workshop_activity_id' => $workshop_activity_id,
                                    'activity_details' => isset($workshop_activity['commentaire']) ? $workshop_activity['commentaire'] : $distributor_customer_activity_workshop_exist['DistributorCustomerActivityWorkshop']['activity_details'],
                                )
                            );

                            $distributor_customer_activity_workshop_bd = $this->save($distributor_customer_activity_workshop_new);
                            if( !$distributor_customer_activity_workshop_bd ){
                                CakeLog::write('updates-france', 'The Distributor Customer Activity Workshop could not be update.'. PHP_EOL);
                            }
                        }

                    }
                }
            }
            $this->commit();
        }

        return true;

    }

}