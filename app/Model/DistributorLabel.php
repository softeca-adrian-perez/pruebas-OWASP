<?php

class DistributorLabel extends AppModel{

    public $useTable = 'distributors_labels';

    public $validate = array(
        'label_type_id' => array(
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

    public function add_label( $label, $distributor_id ){
        $fields = array(
            'DistributorLabel' => array(
                'distributor_id',
                'label_type_id',
                'start_date',
                'end_date',
                'modification_date',
            )
        );

        $label['DistributorLabel']['distributor_id'] = $distributor_id;
        $label['DistributorLabel']['start_date'] = Fecha::toFormatoBD($label['DistributorLabel']['start_date']);
        $label['DistributorLabel']['end_date'] = Fecha::toFormatoBD($label['DistributorLabel']['end_date']);
        $label['DistributorLabel']['modification_date'] = date('Y-m-d H:i:s');
        
        $this->create();
        $label_bd = $this->guardar($label, $fields);
        if ( !$label_bd ){
            return false;
        }

        $this->commit();
        return $label_bd;
    }

    public function edit_label( $label, $distributor_id ){
        $fields = array(
            'DistributorLabel' => array(
                'id',
                'distributor_id',
                'label_type_id',
                'start_date',
                'end_date',
                'modification_date',
            )
        );

        $label['DistributorLabel']['distributor_id'] = $distributor_id;
        $label['DistributorLabel']['start_date'] = Fecha::toFormatoBD($label['DistributorLabel']['start_date']);
        $label['DistributorLabel']['end_date'] = Fecha::toFormatoBD($label['DistributorLabel']['end_date']);
        $label['DistributorLabel']['modification_date'] = date('Y-m-d H:i:s');

        $label_bd = $this->guardar($label, $fields);
        if ( !$label_bd ){
            return false;
        }

        $this->commit();
        return $label_bd;
    }


}?>