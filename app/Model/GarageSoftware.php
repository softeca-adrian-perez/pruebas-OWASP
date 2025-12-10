<?php

class GarageSoftware extends AppModel{

    public $useTable = 'garages_software';

    public $validate = array(
        'software_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_software',
            ),
        ),
        'start_date' => array(
            'rule' => 'date', 'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'end_date' => array(
            'rule' => 'date', 'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'version' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'username' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'password' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
    );

    public function findSoftwareExport($garage_id, $aagRegionId){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Software',
                        'table' => 'software',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Software.id = GarageSoftware.software_id',
                        ),
                    ),
                    array(
                        'alias' => 'SoftwareType',
                        'table' => 'software_types',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'SoftwareType.id = GarageSoftware.software_type_id',
                        ),
                    ),
                    array(
                        'alias' => 'SoftwareManufactures',
                        'table' => 'software_manufactures',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'SoftwareManufactures.id = GarageSoftware.software_manufacture_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageSoftware.garage_id' => $garage_id,
                    'Software.aag_region_id' => $aagRegionId
                ),
                'fields' => array(
                    'Software.name_' . __l() . ' as software_name',
                    'SoftwareType.name_' . __l() . ' as software_type_name',
                    'SoftwareManufactures.name_' . __l() . ' as software_manufactures_name',
                    'GarageSoftware.*',
                ),
            )
        );
    }

    public function add_garage_software( $garage_software , $garage_id ){
        $fields = array(
            'GarageSoftware' => array(
                'garage_id',
                'software_id',
                'software_type_id',
                'supplier_id',
                'software_manufacture_id',
                'start_date',
                'end_date',
                'version',
                'username',
                'password',
                'billing_schedule_id',
                'amount',
                'member_pay',
                'garage_pay',
                'billed_by_aag',
                'number_subscription',
                'online_ordering',
            )
        );
        $garage_software['GarageSoftware']['garage_id'] = $garage_id;
        $garage_software['GarageSoftware']['start_date'] = Fecha::toFormatoBd($garage_software['GarageSoftware']['start_date']);
        $garage_software['GarageSoftware']['end_date'] = Fecha::toFormatoBd($garage_software['GarageSoftware']['end_date']);
        $this->create();

        $garage_software_bd = $this->guardar( $garage_software, $fields );
        if(!$garage_software_bd){
            return false;
        }

        $this->commit();
        return $garage_software_bd;
    }

    public function edit_garage_software( $garage_software ){
        $fields = array(
            'GarageSoftware' => array(
                'software_id',
                'software_type_id',
                'supplier_id',
                'software_manufacture_id',
                'start_date',
                'end_date',
                'version',
                'username',
                'password',
                'billing_schedule_id',
                'amount',
                'member_pay',
                'garage_pay',
                'billed_by_aag',
                'number_subscription',
                'online_ordering',
            )
        );

        $garage_software['GarageSoftware']['start_date'] = Fecha::toFormatoBd($garage_software['GarageSoftware']['start_date']);
        $garage_software['GarageSoftware']['end_date'] = Fecha::toFormatoBd($garage_software['GarageSoftware']['end_date']);

        $this->create();

        $garage_software_bd = $this->guardar( $garage_software, $fields );
        if(!$garage_software_bd){
            return false;
        }

        $this->commit();
        return $garage_software_bd;
    }

    public function removeSoftwareFromGarages( $software_id ){
        $garages = $this->findAllBySoftwareId( $software_id );

        foreach ($garages as $garage) {
            $this->delete($garage['GarageSoftware']['id']);
        }
    }

}
?>