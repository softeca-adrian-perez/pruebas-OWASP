<?php

class Fleet extends AppModel
{

    public $useTable = 'fleets';

    public $hasAndBelongsToMany = array(
        'Network'
    );

    public $validate = array(
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'address' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'postcode' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'erp_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_erp_id',
            ),
        ),
        'ref_code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'required' => true,
                'message' => 'Validation.Name_is_too_long',
            ),
            'unique' => array(
                'rule' => 'checkUniqueRefCode',
                'message' => 'Validation.Erp_must_be_unique',
            ),
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Erp_must_be_unique'
            ),
        ),
        'payment_terms' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'tax_code' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_INT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'company_code' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'required' => true,
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'company_name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'aag_region_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_region',
            ),
        )
    );

    private $_queries = array(
        'home' => array(
            'joins' => array(
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Fleet.country_id = Country.id'
                    ),
                    'fields' => array(
                        'Country.id',
                        'Country.name',
                    )
                ),
                array(
                    'alias' => 'Province',
                    'table' => 'provinces',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Fleet.province_id = Province.id',
                    ),
                    'fields' => array(
                        'Province.id',
                        'Province.name',
                    )
                ),
                array(
                    'alias' => 'Erp',
                    'table' => 'erp',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Fleet.erp_id = Erp.id',
                    ),
                    'fields' => array(
                        'Erp.id',
                        'Erp.erp_code',
                    )
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Fleet.ref_code = Garage.ref_code',
                    ),
                    'fields' => array(
                        'Garage.id',
                        'Garage.ref_code',
                    )
                ),
            ),
            'fields' => array(
                'Fleet.id',
                'Fleet.guid',
                'Fleet.name',
                'Fleet.address',
                'Fleet.postcode',
                'Country.name',
                'Province.name',
                'Erp.erp_code',
                'Fleet.ref_code',
                'Fleet.payment_terms',
                'Fleet.tax_code',
                'Fleet.company_code',
                'Fleet.company_name',
                'Fleet.active'
            )
        )
    );

    public function _query( $index ){
        return $this->_queries[$index];
    }

    public function add_fleet($fleet)
    {
        $fields = array(
            'Fleet' => array(
                'name',
                'address',
                'postcode',
                'country_id',
                'province_id',
                'erp_id',
                'ref_code',
                'payment_terms',
                'tax_code',
                'company_code',
                'company_name',
                'aag_region_id',
                'creation_date',
                'modification_date',
                'guid',
                'active'
            )
        );

        $fleet['Fleet']['creation_date'] = date('Y-m-d H:i:s');
        $fleet['Fleet']['modification_date'] = date('Y-m-d H:i:s');
        $fleet['Fleet']['guid'] = CakeText::uuid();

        $this->create();

        $fleetBd = $this->guardar($fleet, $fields);
        if (!$fleetBd) {
            return false;
        }

        if ($fleetBd && isset($fleet['Fleet']['networks'])) {
            $fleetNetworkClass = ClassRegistry::init('FleetNetwork');
            $repairMaintenanceClass = ClassRegistry::init('RepairMaintenance');
            $fleetsNetworks = $fleetNetworkClass->setFleetNetworks($fleetBd['Fleet']['id'], $fleet['Fleet']['networks']);
            $syncRepair = $repairMaintenanceClass->addFleet($fleetBd);
        }

        if (!$fleetBd || !$fleetsNetworks || !$syncRepair) {
            return false;
        }
        return true;
    }

    public function edit_fleet($fleet)
    {
        $fields = array(
            'Fleet' => array(
                'name',
                'address',
                'postcode',
                'country_id',
                'province_id',
                'erp_id',
                'ref_code',
                'payment_terms',
                'tax_code',
                'company_code',
                'company_name',
                'modification_date',
                'active'
            )
        );
        $fleetClass = ClassRegistry::init('FleetNetwork');
        $oldFleet = $this->findById($fleet['Fleet']['id']);
        $oldFleetNetworks = $fleetClass->get_list($fleet['Fleet']['id']);
        $fleet['Fleet']['modification_date'] = date('Y-m-d H:i:s');

        $edited_fleet = $this->guardar($fleet, $fields);
        if (!$edited_fleet) {
            return false;
        }

        $repairMaintenanceClass = ClassRegistry::init('RepairMaintenance');
        if ($edited_fleet && isset($fleet['Fleet']['networks'])) {
            $fleetsNetworks = $fleetClass->setFleetNetworks($fleet['Fleet']['id'], $fleet['Fleet']['networks']);
            $syncRepair = $repairMaintenanceClass->updateFleet($edited_fleet);
        }

        if ($edited_fleet && (!$fleetsNetworks || !$syncRepair)) {
            $this->save($oldFleet, $fields);
            $fleetClass->setFleetNetworks($fleet['Fleet']['id'], $oldFleetNetworks);
            return false;
        }
        return true;
    }

    public function checkUniqueRefCode($field)
    {
        if (!empty($field['ref_code'])) {
            $conditions = array(
                'ref_code' => $field['ref_code']
            );
            return $this->isUnique($conditions);
        }
        return true;
    }

    public function setRepairMaintenance($id, $value = ConstantsBooleans::YES)
    {
        $fleet = $this->findById($id);
        if (isset($fleet['Fleet']['id']) && !empty($fleet['Fleet']['id'])) {
            $fields = array(
                'Fleet' => array(
                    'id',
                    'repairmaintenance'
                )
            );
            $fleet['Fleet']['repairmaintenance'] = $value;
            return $this->save($fleet, true, $fields);
        }
    }

    public function edit_fleet_lobster($fleet)
    {
        $fields = array(
            'Fleet' => array(
                'name',
                'address',
                'postcode',
                'country_id',
                'province_id',
                'payment_terms',
                'company_code',
                'modification_date'
            )
        );
        $fleetClass = ClassRegistry::init('FleetNetwork');
        $oldFleet = $this->findById($fleet['Fleet']['id']);
        $oldFleetNetworks = $fleetClass->get_list($fleet['Fleet']['id']);
        $fleet['Fleet']['modification_date'] = date('Y-m-d H:i:s');

        $edited_fleet = $this->guardar($fleet, $fields);

        $instanceFleetNetwork = ClassRegistry::init('FleetNetwork');

        $repairMaintenanceClass = ClassRegistry::init('RepairMaintenance');

        $fleet['Fleet']['networks'] = $instanceFleetNetwork->findListByFleetId($fleet['Fleet']['id'], ['network_id']);
        if ($edited_fleet && isset($fleet['Fleet']['networks'])) {
            $fleetsNetworks = $instanceFleetNetwork->setFleetNetworks($fleet['Fleet']['id'], $fleet['Fleet']['networks']);
            $syncRepair = $repairMaintenanceClass->updateFleet($edited_fleet);
        }

        if ($edited_fleet && (!$fleetsNetworks || !$syncRepair)) {
            $this->save($oldFleet, $fields);
            $fleetClass->setFleetNetworks($fleet['Fleet']['id'], $oldFleetNetworks);
            return false;
        }
        return $edited_fleet;
    }
}
