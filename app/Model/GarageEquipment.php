<?php

class GarageEquipment extends AppModel{

    public $useTable = 'garages_equipments';

    public $validate = array(
        'equipment_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_equipment',
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
    );

    public function add_garage_equipment( $garage_equipment , $garage_id ){
        $fields = array(
            'GarageEquipment' => array(
                'garage_id',
                'equipment_id',
                'equipment_type_id',
                'supplier_id',
                'brand_id',
                'start_date',
                'end_date',
                'billing_schedule_id',
                'amount',
                'member_pay',
                'garage_pay',
                'billed_by_aag',
            )
        );

        $garage_equipment['GarageEquipment']['garage_id'] = $garage_id;

        $garage_equipment['GarageEquipment']['start_date'] = Fecha::toFormatoBd( $garage_equipment['GarageEquipment']['start_date'] );
        $garage_equipment['GarageEquipment']['end_date'] = Fecha::toFormatoBd( $garage_equipment['GarageEquipment']['end_date'] );
        $this->create();

        $garage_equipment_bd = $this->guardar( $garage_equipment, $fields );

        if(!$garage_equipment_bd){
            return false;
        }

        return $garage_equipment_bd;
    }

    public function edit_garage_equipment( $garage_equipment ){
        $fields = array(
            'GarageEquipment' => array(
                'garage_id',
                'equipment_id',
                'equipment_type_id',
                'supplier_id',
                'brand_id',
                'start_date',
                'end_date',
                'billing_schedule_id',
                'amount',
                'member_pay',
                'garage_pay',
                'billed_by_aag',
            )
        );

        $garage_equipment['GarageEquipment']['start_date'] = Fecha::toFormatoBd($garage_equipment['GarageEquipment']['start_date']);
        $garage_equipment['GarageEquipment']['end_date'] = Fecha::toFormatoBd($garage_equipment['GarageEquipment']['end_date']);

        $this->create();

        $garage_equipment_bd = $this->guardar( $garage_equipment, $fields );

        if( !$garage_equipment_bd ){
            return false;
        }

        $this->commit();
        return $garage_equipment_bd;
    }

    public function get_export_garage_equipment( $garageId, $aagRegionId )
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'table' => 'equipments',
                        'alias' => 'Equipments',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageEquipment.equipment_id = Equipments.id'
                        )
                    ),
                    array(
                        'table' => 'equipments_types',
                        'alias' => 'EquipmentTypes',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageEquipment.equipment_type_id = EquipmentTypes.id'
                        )
                    ),
                ),
                'conditions' => array(
                    'GarageEquipment.garage_id' => $garageId,
                    'Equipments.aag_region_id' => $aagRegionId,
                    'EquipmentTypes.aag_region_id' => $aagRegionId,
                ),
                'fields' => array(
                    'GarageEquipment.*',
                    'Equipments.name_' . __l() . ' as equipment_name',
                    'EquipmentTypes.name_' . __l() . ' as equipment_type_name',
                ),
            )
        );
    }


}
?>