<?php

class DistributorContract extends AppModel{

    public $useTable = 'distributors_contracts';

    public $validate = array(
        'trading_group_id' => array(
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
            array(
                'rule' => 'date', 'dmy',
                'message' => 'Validation.Format_date',
                'allowEmpty' => true
            )
        ),
        'end_date' => array(
            'rule' => 'date', 'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'leaving_reason' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
    );

    public function add_contract( $contract, $distributor_id ){
        $fields = array(
            'DistributorContract' => array(
                'distributor_id',
                'trading_group_id',
                'network_id',
                'start_date',
                'end_date',
                'leaving_reason_id',
                'modification_date',
            )
        ); 
        
        $contract['DistributorContract']['distributor_id'] = $distributor_id;
        $contract['DistributorContract']['start_date'] = Fecha::toFormatoBD($contract['DistributorContract']['start_date']);
        $contract['DistributorContract']['end_date'] = Fecha::toFormatoBD($contract['DistributorContract']['end_date']);
        $contract['DistributorContract']['modification_date'] = date('Y-m-d H:i:s');
        
        $this->create();
        $contract_bd = $this->guardar($contract, $fields);
        if ( !$contract_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    public function edit_contract( $contract, $distributor_id ){
        $fields = array(
            'DistributorContract' => array(
                'id',
                'distributor_id',
                'trading_group_id',
                'network_id',
                'start_date',
                'end_date',
                'leaving_reason_id',
                'modification_date',
            )
        );

        $contract['DistributorContract']['distributor_id'] = $distributor_id;
        $contract['DistributorContract']['start_date'] = Fecha::toFormatoBD($contract['DistributorContract']['start_date']);
        $contract['DistributorContract']['end_date'] = Fecha::toFormatoBD($contract['DistributorContract']['end_date']);
        $contract['DistributorContract']['modification_date'] = date('Y-m-d H:i:s');

        $contract_bd = $this->guardar($contract, $fields);
        if ( !$contract_bd ){
            return false;
        }

        $this->commit();
        return $contract_bd;
    }


}?>