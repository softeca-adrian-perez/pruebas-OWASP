<?php

class ConferenceDelegate extends AppModel
{
    public $useTable = 'conferences_delegates';
    public $hasMany = array(
        'Room' => array(
            'className' => 'Room',
            'foreignKey' => 'conferences_delegates_id',
        ),
        'TradeShow' => array(
            'className' => 'Room',
            'foreignKey' => 'conferences_delegates_id',
        ),
        'Dinner' => array(
            'className' => 'Dinner',
            'foreignKey' => 'conferences_delegates_id',
        ),
    );

    public $validate = array(
        'conferences_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
        ),
        'contact' => array(
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
        'email' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'diet_requirements' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'guest_name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'stand_number' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'bespoke_stand_details' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'website_entry_id' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['conference_name'])) {
            $conditions[] = $this->_conditionConference($fields['conference_name']);
        }
        if (!empty($fields['contact'])) {
            $conditions[] = $this->_conditionDelegate($fields['contact']);
        }
        if (!empty($fields['venue_name'])) {
            $conditions[] = $this->_conditionVenue($fields['venue_name']);
        }
        if (!empty($fields['distributor_name'])) {
            $conditions[] = $this->_conditionDistributor($fields['distributor_name']);
        }
        if (!empty($fields['supplier_name'])) {
            $conditions[] = $this->_conditionSupplier($fields['supplier_name']);
        }
        if (!empty($fields['garage_name'])) {
            $conditions[] = $this->_conditionGarage($fields['garage_name']);
        }

        return $conditions;
    }

    private function _conditionConference($conference_id)
    {
        return array('ConferenceDelegate.conferences_id' => $conference_id);
    }

    private function _conditionDelegate($delegate_id)
    {
        return array('ConferenceDelegate.delegate_id' => $delegate_id);
    }

    private function _conditionVenue($venue_id)
    {
        return array('ConferenceDelegate.venue_id' => $venue_id);
    }

    private function _conditionDistributor($distributor_id)
    {
        return array('ConferenceDelegate.distributors_id' => $distributor_id);
    }

    private function _conditionSupplier($supplier_id)
    {
        return array('ConferenceDelegate.suppliers_id' => $supplier_id);
    }

    private function _conditionGarage($garage_id)
    {
        return array('ConferenceDelegate.garages_id' => $garage_id);
    }


    private $_queries = array(
        'home' => array(
            'joins' => array(
                array(
                    'alias' => 'Conference',
                    'table' => 'conferences',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Conference.id = ConferenceDelegate.conferences_id',
                    ),
                ),
                array(
                    'alias' => 'Venue',
                    'table' => 'venues',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Venue.id = Conference.venue_id',
                    ),
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Distributor.id = ConferenceDelegate.distributors_id',
                    ),
                ),
                array(
                    'alias' => 'Supplier',
                    'table' => 'suppliers',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = ConferenceDelegate.suppliers_id',
                    ),
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.id = ConferenceDelegate.garages_id',
                    ),
                ),
            ),
            'fields' => array(
                'ConferenceDelegate.*',
                'Conference.name',
                'Venue.name',
                'Distributor.name',
                'Supplier.name',
                'Garage.*'
            ),
            'order' => 'ConferenceDelegate.contact'
        ),
    );

    public function _query($aag_region_id)
    {
        $conditions = array();
        $conditions = array('Venue.aag_region_id' => $aag_region_id);

        $query = array(
            'joins' => array(
                array(
                    'alias' => 'Conference',
                    'table' => 'conferences',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Conference.id = ConferenceDelegate.conferences_id',
                    ),
                ),
                array(
                    'alias' => 'Venue',
                    'table' => 'venues',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Venue.id = Conference.venue_id',
                    ),
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Distributor.id = ConferenceDelegate.distributors_id',
                    ),
                ),
                array(
                    'alias' => 'Supplier',
                    'table' => 'suppliers',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Supplier.id = ConferenceDelegate.suppliers_id',
                    ),
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.id = ConferenceDelegate.garages_id',
                    ),
                ),
            ),
            'conditions' => $conditions,
            'fields' => array(
                'ConferenceDelegate.*',
                'Conference.name',
                'Venue.name',
                'Distributor.name',
                'Supplier.name',
                'Garage.name'
            ),
            'order' => 'ConferenceDelegate.contact'
        );

        return $query;
    }

    public function add($delegate)
    {
        $fields = array(
            'ConferenceDelegate' => array(
                'conferences_id',
                'booking_date',
                'garages_id',
                'distributors_id',
                'suppliers_id',
                'delegate_id',
                'contact',
                'email',
                'room_type_id',
                'nights',
                'vegetarian',
                'diet_requirements',
                'guest_name',
                'guest_separate_room',
                'stand_number',
                'stand_size_id',
                'stand_power_required',
                'bespoke_stand_details',
                'website_entry_id',
                'venue_id',
            )
        );
        $delegate['ConferenceDelegate']['booking_date'] = Fecha::toFormatoBd($delegate['ConferenceDelegate']['booking_date']);
        $delegate['ConferenceDelegate']['garages_id'] = isset($delegate['ConferenceDelegate']['garages_id']) ? $delegate['ConferenceDelegate']['garages_id'] : null;
        $delegate['ConferenceDelegate']['distributors_id'] = isset($delegate['ConferenceDelegate']['distributors_id']) ? $delegate['ConferenceDelegate']['distributors_id'] : null;
        $delegate['ConferenceDelegate']['suppliers_id'] = isset($delegate['ConferenceDelegate']['suppliers_id']) ? $delegate['ConferenceDelegate']['suppliers_id'] : null;
        $this->create();

        $delegate_bd = $this->guardar($delegate, $fields);
        if (!$delegate_bd) {
            return false;
        }
        $this->commit();
        return $delegate_bd;
    }

    public function edit($delegate)
    {
        $fields = array(
            'ConferenceDelegate' => array(
                'conferences_id',
                'booking_date',
                'garages_id',
                'distributors_id',
                'suppliers_id',
                'delegate_id',
                'contact',
                'email',
                'room_type_id',
                'nights',
                'vegetarian',
                'diet_requirements',
                'guest_name',
                'guest_separate_room',
                'stand_number',
                'stand_size_id',
                'stand_power_required',
                'bespoke_stand_details',
                'website_entry_id',
                'venue_id',
            )
        );
        $delegate['ConferenceDelegate']['booking_date'] = Fecha::toFormatoBd($delegate['ConferenceDelegate']['booking_date']);
        $delegate['ConferenceDelegate']['garages_id'] = isset($delegate['ConferenceDelegate']['garages_id']) ? $delegate['ConferenceDelegate']['garages_id'] : null;
        $delegate['ConferenceDelegate']['distributors_id'] = isset($delegate['ConferenceDelegate']['distributors_id']) ? $delegate['ConferenceDelegate']['distributors_id'] : null;
        $delegate['ConferenceDelegate']['suppliers_id'] = isset($delegate['ConferenceDelegate']['suppliers_id']) ? $delegate['ConferenceDelegate']['suppliers_id'] : null;

        $delegate_bd = $this->guardar($delegate, $fields);
        if (!$delegate_bd) {
            return false;
        }
        $this->commit();
        return $delegate_bd;
    }

    public function dynamicTypeConferencesDelegatesExportQuery($query_type, $aag_region_id, $conditions)
    {
        return $this->find(
            $query_type,
            array(
                'conditions' => array(
                    $conditions,
                    'Venue.aag_region_id' => $aag_region_id,
                ),
                'joins' => array(
                    array(
                        'alias' => 'Conference',
                        'table' => 'conferences',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Conference.id = ConferenceDelegate.conferences_id',
                        ),
                    ),
                    array(
                        'alias' => 'Venue',
                        'table' => 'venues',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Venue.id = Conference.venue_id',
                        ),
                    ),
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Distributor.id = ConferenceDelegate.distributors_id',
                        ),
                    ),
                    array(
                        'alias' => 'Supplier',
                        'table' => 'suppliers',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Supplier.id = ConferenceDelegate.suppliers_id',
                        ),
                    ),
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Garage.id = ConferenceDelegate.garages_id',
                        ),
                    ),
                ),
                'fields' => array(
                    'ConferenceDelegate.contact',
                    'ConferenceDelegate.garages_id',
                    'ConferenceDelegate.suppliers_id',
                    'ConferenceDelegate.distributors_id',
                    'ConferenceDelegate.conferences_id',
                    'Conference.name',
                    'Venue.name',
                    'Distributor.name',
                    'Supplier.name',
                    'Garage.name'
                ),
                'order' => 'ConferenceDelegate.contact'
            )
        );
    }
}
