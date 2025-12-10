<?php

class DistributorService extends AppModel{

    public $useTable = 'distributors_services';

    public $validate = array(
        'service_type_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name',
            ),
        ),
        'start_date' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_date',
            ),
        ),
    );

    public function add_service( $service, $distributor_id ){
        $fields = array(
            'DistributorService' => array(
                'distributor_id',
                'service_type_id',
                'start_date',
                'end_date',
                'modification_date',
            )
        );

        $service['DistributorService']['distributor_id'] = $distributor_id;
        $service['DistributorService']['start_date'] = Fecha::toFormatoBD($service['DistributorService']['start_date']);
        $service['DistributorService']['end_date'] = Fecha::toFormatoBD($service['DistributorService']['end_date']);
        $service['DistributorService']['modification_date'] = date('Y-m-d H:i:s');
        
        $this->create();
        $service_bd = $this->guardar($service, $fields);
        if ( !$service_bd ){
            return false;
        }

        $this->commit();
        return $service_bd;
    }

    public function edit_service( $service, $distributor_id ){
        $fields = array(
            'DistributorService' => array(
                'id',
                'distributor_id',
                'service_type_id',
                'start_date',
                'end_date',
                'modification_date',
            )
        );

        $service['DistributorService']['distributor_id'] = $distributor_id;
        $service['DistributorService']['start_date'] = Fecha::toFormatoBD($service['DistributorService']['start_date']);
        $service['DistributorService']['end_date'] = Fecha::toFormatoBD($service['DistributorService']['end_date']);
        $service['DistributorService']['modification_date'] = date('Y-m-d H:i:s');

        $service_bd = $this->guardar($service, $fields);
        if ( !$service_bd ){
            return false;
        }

        $this->commit();
        return $service_bd;
    }


}?>