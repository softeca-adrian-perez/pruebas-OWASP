<?php

App::uses('Agn', 'Lib');
class Garage extends AppModel
{
    public $sendInfoCacheDataWebs = false;
    public $oldGarageCity = null;
    public $useTable = 'garages';
    public $displayField = 'name';
    public $virtualFields = array(
        'complete_name' => 'CONCAT(IFNULL(Garage.name, \'\'), " - ", IFNULL(Garage.g_number_id, \'\'))',
        'complete_name_france' => 'CONCAT(IFNULL(Garage.name, \'\'), " - ", IFNULL(Garage.siret, \'\'))',
        'complete_search' => 'CONCAT(IFNULL(Garage.name, \'\'), " - ", IFNULL(Garage.g_number_id, \'\'), " - ", IFNULL(Garage.town, \'\'))'
    );

    public $hasOne = array(
        'Province',
        'GarageNetwork',
        'GarageAgreement',
        'GarageDistributor',
        'City',
        'AagRegion'
    );

    public $hasMany = array(
        'Appointment',
        'GarageRoute',
        'MessageGarage'
    );

    public $hasAndBelongsToMany = array(
        'Service',
        'VehicleType',
        'Network',
        'Distributor',
        'Agreement',
        'Fleet'
    );

    public function beforeSave($options = array())
    {
        if (
            isset($this->data['Garage']['id']) && !empty($this->data['Garage']['id']) &&
            isset($this->data['Garage']['city_id']) && !empty($this->data['Garage']['city_id'])
        ) {
            $garage = $this->findById($this->data['Garage']['id']);
            if (
                $garage['Garage']['city_id'] !== $this->data['Garage']['city_id'] ||
                $garage['Garage']['town'] !== $this->data['Garage']['town'] ||
                $garage['Garage']['postcode'] !== $this->data['Garage']['postcode'] ||
                $garage['Garage']['province_id'] !== $this->data['Garage']['province_id'] ||
                $garage['Garage']['address1'] !== $this->data['Garage']['address1'] ||
                $garage['Garage']['address2'] !== $this->data['Garage']['address2'] ||
                $garage['Garage']['address3'] !== $this->data['Garage']['address3'] ||
                $garage['Garage']['address4'] !== $this->data['Garage']['address4']
            ) {
                $this->sendInfoCacheDataWebs = true;
                $this->oldGarageCity = $garage['Garage']['city_id'];
            }
        }
        return true;
    }

    public function afterSave($created, $options = array())
    {
        if (isset($this->sendInfoCacheDataWebs) && $this->sendInfoCacheDataWebs && isset($this->oldGarageCity) && !empty($this->oldGarageCity)) {
            $networkData = $this->getInternalNetworkFromGarage($this->data['Garage']['id']);
            if (isset($networkData)) {
                $agn = new Agn();
                foreach ($networkData as $network) {
                    $agn->purgeCacheLocationData($network['Network']['guid'], $this->oldGarageCity, $this->data['Garage']['city_id']);
                }
            }
        }
        return true;
    }

    public $validate = array(
        'name' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'address1' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_address',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'email' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Email_too_long',
                'allowEmpty' => true,
            ),
            'email' => array(
                'rule' => 'email',
                'message' => 'Validation.Email_incorrect_format',
                'allowEmpty' => true,
            ),
        ),
        'marketing_email' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Email_too_long',
                'allowEmpty' => true,
            ),
            'email' => array(
                'rule' => 'email',
                'message' => 'Validation.Email_incorrect_format',
                'allowEmpty' => true,
            ),
        ),
        'phone' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_PHONE),
                'message' => 'Validation.Phone_too_long',
                'allowEmpty' => true,
            ),
        ),
        'phone_international' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_PHONE),
                'message' => 'Validation.Phone_too_long',
                'allowEmpty' => true,
            ),
        ),
        'mobile' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_PHONE),
                'message' => 'Validation.Phone_too_long',
                'allowEmpty' => true,
            ),
        ),
        'fax' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_PHONE),
                'message' => 'Validation.Fax_too_long',
                'allowEmpty' => true,
            ),
        ),
        'service_24h_phone' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_PHONE),
                'message' => 'Validation.Phone_too_long',
                'allowEmpty' => true,
            ),
        ),
        'aag_region_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_region',
            ),
        ),
        'garage_code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'g_number_id' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'ref_code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
            'unique' => array(
                'rule' => 'checkUniqueRefCode',
                'message' => 'Validation.Erp_must_be_unique',
            ),
        ),
        'business_name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'slug' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'comment' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'client_type' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'VAT_code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'siret' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'detax_code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'reason_leaving_date' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'status' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'documents_legal' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'diesel_liability' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'turnover_id' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'flat_rate' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'address2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'address3' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'address4' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'latitude' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'longitude' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'town' => array(
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
        'web' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'monday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'monday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'monday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'monday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'tuesday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'tuesday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'tuesday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'tuesday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'wednesday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'wednesday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'wednesday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'wednesday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'thursday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'thursday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'thursday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'thursday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'friday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'friday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'friday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'friday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'saturday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'saturday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'saturday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'saturday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'sunday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'sunday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'sunday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'sunday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_SMALL),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'fleet_mot' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'fleet_labour_rate' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'long_life_oil_price_b2b' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'standard_oil_price_b2b' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'retail_mot' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'retail_labour_rate' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'long_life_oil_price_b2c' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'standard_oil_price_b2c' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'ev_charge_points' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'kwh_charging_retail_price' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'kwh_charging_fleet_price' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'ramps' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'technician' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'MOT_bays' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Validation.Must_be_a_number',
            ),
            'range' => array(
                'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Invalid_range',
            ),
        ),
        'spend_this_month' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'spend_last_month' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'spend_12_month' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'spend_projected' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'foundation_year' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'lead_source' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'interests' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'province_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_province',
            )
        ),
    );

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

    private $_queries = array(
        'search' => array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'GarageService',
                    'table' => 'garages_services',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageService.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'GarageDistributor',
                    'table' => 'garages_distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageDistributor.garage_id = Garage.id',
                    ),
                ),
            ),
            'fields' => array(
                'Garage.*',
            ),
            'group' => array(
                'Garage.id',
            ),
        ),
        'visit' => array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Appointment.garage_id = Garage.id',
                    ),
                ),
            ),
            'fields' => array(
                'Garage.id',
                'Garage.name',
                'Garage.client_type',
                'Garage.town',
                'Garage.g_number_id',
                'Garage.address1',
                'Appointment.id',
                'Appointment.date',
                'max(Appointment.date) as max_appointment_date',
            ),
            'group' => array(
                'Garage.id',
            ),
            'order' => array(
                'Appointment.date' => 'asc',
                'Garage.name' => 'asc'
            )
        ),
        'ajax_garages' => array(
            'fields' => array(
                'Garage.id',
                'Garage.complete_name',
            ),
        ),
        'clients' => array(
            'fields' => array(
                'Garage.id',
                'Garage.name',
                'Garage.town',
                'Garage.g_number_id',
                'Garage.address1',
                'Garage.last_visit',
            ),
            'order' => array(
                'Garage.last_visit' => 'asc',
                'Garage.name' => 'asc',
            ),
            'limit' => ConstantsPagination::SIZE_PAGE_SMALL,
        ),
        'home_training' => array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id',
                        'GarageNetwork.status' => array(ConstantsNetworksStatus::LIVE, ConstantsNetworksStatus::ON_HOLD, ConstantsNetworksStatus::LEFT),
                    ),
                ),
                array(
                    'alias' => 'TrainingAllowance',
                    'table' => 'trainings_allowances',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TrainingAllowance.garage_network_id = GarageNetwork.id',
                    ),
                ),
                array(
                    'alias' => 'Network',
                    'table' => 'networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Network.id = GarageNetwork.network_id',
                        'Network.id' => array(NETWORK_ID_AUTOCARE, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_TOPTRUCK, NETWORK_ID_GEXPERT),
                    ),
                ),
                array(
                    'alias' => 'TrainingCreditNetwork',
                    'table' => 'trainings_credits_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingCreditNetwork.garage_network_id = GarageNetwork.id',
                    ),
                ),
                array(
                    'alias' => 'TrainingPlannedCourse',
                    'table' => 'trainings_planned_courses',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingPlannedCourse.id = TrainingCreditNetwork.training_planned_course_id',
                    ),
                ),
            ),
            'fields' => array(
                'TrainingCreditNetwork.id',
                'TrainingAllowance.id',
                'TrainingAllowance.start_date',
                'TrainingAllowance.end_date',
                'TrainingAllowance.is_actual',
                'GarageNetwork.id',
                'GarageNetwork.default_credit',
                'GarageNetwork.credit',
                'GarageNetwork.garage_id',
                'Garage.id',
                'Garage.name',
                'Garage.business_name',
                'Network.credit',
            ),
            'order' => 'Garage.name asc, TrainingAllowance.start_date desc',
            'group' => 'TrainingAllowance.id',
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['trading_group_id'])) {
            $conditions[] = $this->_conditionTradingGroup($fields['trading_group_id']);
        }
        if (!empty($fields['aag_region_id'])) {
            $conditions[] = $this->_conditionRegion($fields['aag_region_id']);
        }
        if (!empty($fields['country_id'])) {
            $conditions[] = $this->_conditionCountry($fields['country_id']);
        }
        if (!empty($fields['network_id']) && !empty($fields['status_id'])) {
            $conditions[] = $this->_conditionNetworkIdStatus($fields['network_id'], $fields['status_id']);
        } else {
            if (!empty($fields['status_id'])) {
                $conditions[] = $this->_conditionNetworkStatus($fields['status_id']);
            }
            if (!empty($fields['network_id'])) {
                $conditions[] = $this->_conditionNetwork($fields['network_id']);
            }
        }
        if (!empty($fields['name'])) {
            $conditions[] = $this->_conditionName($fields['name']);
        }
        if (!empty($fields['g_number_id'])) {
            $conditions[] = $this->_conditionGNumber($fields['g_number_id']);
        }
        if (!empty($fields['ref_code'])) {
            $conditions[] = $this->_conditionAutopart($fields['ref_code']);
        }
        if (!empty($fields['erp_id'])) {
            $conditions[] = $this->_conditionErpId($fields['erp_id']);
        }
        if (!empty($fields['service_id'])) {
            $conditions[] = $this->_conditionService($fields['service_id']);
        }
        if (!empty($fields['vehicle_type_id'])) {
            $conditions[] = $this->_conditionVehicleType($fields['vehicle_type_id']);
        }
        if (isset($fields['city_id']) && $fields['city_id'] != '') {
            $conditions[] = $this->_conditionCity($fields['city_id']);
        }
        if (isset($fields['province_id']) && $fields['province_id'] != '') {
            $conditions[] = $this->_conditionProvince($fields['province_id']);
        }
        if (!empty($fields['postcode'])) {
            $conditions[] = $this->_conditionPostcode($fields['postcode']);
        }
        if (!empty($fields['phone'])) {
            $conditions[] = $this->_conditionPhone($fields['phone']);
        }
        if (!empty($fields['ramps'])) {
            $conditions[] = $this->_conditionRamps($fields['ramps']);
        }
        if (!empty($fields['MOT_bays'])) {
            $conditions[] = $this->_conditionMOTBays($fields['MOT_bays']);
        }
        if (!empty($fields['foundation_year'])) {
            $conditions[] = $this->_conditionFoundationYear($fields['foundation_year']);
        }
        if (!empty($fields['lead_source'])) {
            $conditions[] = $this->_conditionLeadSource($fields['lead_source']);
        }
        if (!empty($fields['distributor_id'])) {
            $conditions[] = $this->_conditionDistributor($fields['distributor_id']);
        }
        if (!empty($fields['client_type'])) {
            $conditions[] = $this->_conditionClientType($fields['client_type']);
        }
        if (!empty($fields['bdm_id'])) {
            $conditions[] = $this->_conditionBDM($fields['bdm_id']);
        }
        if (!empty($fields['status'])) {
            $conditions[] = $this->_conditionStatusGarage($fields['status']);
        }
        if (!empty($fields['sales_area_id'])) {
            $conditions[] = $this->_conditionSalesArea($fields['sales_area_id']);
        }
        if (!empty($fields['search_my_customers'])) {
            $conditions[] = $this->_conditionMyCustomer($fields['search_my_customers']);
        }
        if (!empty($fields['external_agreements'])) {
            $conditions[] = $this->_conditionExternalAgreements($fields['external_agreements']);
        }
        if (!empty($fields['internal_agreements'])) {
            $conditions[] = $this->_conditionInternalAgreements($fields['internal_agreements']);
        }
        if (!empty($fields['Garage_name'])) {
            $conditions[] = $this->_conditionGarage_name($fields['Garage_name']);
        }
        if (!empty($fields['Network_name'])) {
            $conditions[] = $this->_conditionNetwork_name($fields['Network_name']);
        }
        if (!empty($fields['TrainingCreditNetwork_date_from'])) {
            $conditions[] = $this->_conditionDateFrom($fields['TrainingCreditNetwork_date_from']);
        }
        if (!empty($fields['TrainingCreditNetwork_date_to'])) {
            $conditions[] = $this->_conditionDateTo($fields['TrainingCreditNetwork_date_to']);
        }
        if (!empty($fields['town'])) {
            $conditions[] = $this->_conditionTown($fields['town']);
        }
        if (!empty($fields['annex_detail_id'])) {
            $conditions[] = $this->_conditionAnnexDetail($fields['annex_detail_id']);
        }
        if (!empty($fields['is_actual'])) {
            $conditions[] = $this->_conditionIsActual($fields['is_actual']);
        }
        if (!empty($fields['TrainingAllowances_complete_date'])) {
            $conditions[] = $this->_conditionTrainingAllowance($fields['TrainingAllowances_complete_date']);
        }

        return $conditions;
    }

    private function _conditionMyCustomer($search_my_customers)
    {
        return array('GarageContactBdm.contact_id' => CakeSession::read('Auth.User.contact_id'));
    }

    private function _conditionSalesArea($sales_area_id)
    {
        return array('Garage.sales_area_id' => $sales_area_id);
    }

    private function _conditionCity($city)
    {
        $cities_ids = array();
        $hasNullCondition = false;

        foreach ($city as $city_individual) {
            if ($city_individual == 0) {
                $hasNullCondition = true;
            } else {
                $cities_ids[] = $city_individual;
            }
        }
        if ($hasNullCondition && $cities_ids) {
            return array(
                'OR' => array(
                    'Garage.city_id IN' => $cities_ids,
                    'Garage.city_id IS NULL'
                )
            );
        } else if ($hasNullCondition) {
            return array('Garage.city_id IS NULL');
        } else {
            return array('Garage.city_id IN' => $cities_ids);
        }
    }

    private function _conditionProvince($provinces)
    {
        return array('Garage.province_id' => $provinces);
    }

    private function _conditionPostcode($postcode)
    {
        return array('Garage.postcode LIKE' => '%' . $postcode . '%');
    }

    private function _conditionTown($town)
    {
        return array('Garage.town LIKE' => '%' . $town . '%');
    }

    private function _conditionPhone($phone)
    {
        return array('Garage.phone LIKE' => '%' . $phone . '%');
    }

    private function _conditionDistributor($distributor_id)
    {
        $this->GaragesDistributor = ClassRegistry::init('GaragesDistributor');
        $garages = $this->GaragesDistributor->findAllByDistributorId($distributor_id);
        return array('Garage.id' => Hash::extract($garages, '{n}.GaragesDistributor.garage_id'));
    }

    private function _conditionBDM($contact_id)
    {
        $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');
        $garages = $this->GarageContactBdm->findAllByContactId($contact_id);
        return array('Garage.id' => Hash::extract($garages, '{n}.GarageContactBdm.garage_id'));
    }

    private function _conditionRamps($ramps)
    {
        return array('Garage.ramps LIKE' => '%' . $ramps . '%');
    }

    private function _conditionMOTBays($MOT_bays)
    {
        return array('Garage.MOT_bays LIKE' => '%' . $MOT_bays . '%');
    }

    private function _conditionFoundationYear($foundation_year)
    {
        return array('Garage.foundation_year LIKE' => '%' . $foundation_year . '%');
    }

    public function _conditionLeadSource($lead_source)
    {
        return array('Garage.lead_source' => $lead_source);
    }

    private function _conditionName($name)
    {
        return array('Garage.name LIKE' => '%' . $name . '%');
    }

    private function _conditionGNumber($g_number_id)
    {
        return array('Garage.g_number_id' => $g_number_id);
    }

    private function _conditionAutopart($ref_code)
    {
        return array('Garage.ref_code LIKE' => '%' . $ref_code . '%');
    }

    private function _conditionErpId($erp_id)
    {
        return array('Garage.erp_id' => $erp_id);
    }

    private function _conditionService($service_id)
    {
        $this->GarageService = ClassRegistry::init('GarageService');
        $garages = $this->GarageService->findAllByServiceId($service_id);
        return array('Garage.id' => Hash::extract($garages, '{n}.GarageService.garage_id'));
    }

    private function _conditionVehicleType($vehicle_type_id)
    {
        $this->GarageVehicleType = ClassRegistry::init('GarageVehicleType');
        $garages = $this->GarageVehicleType->findAllByVehicleTypeId($vehicle_type_id);
        return array('Garage.id' => Hash::extract($garages, '{n}.GarageVehicleType.garage_id'));
    }

    private function _conditionNetwork($network_id)
    {
        $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
        $garages = $this->GarageNetwork->findAllByNetworkIdAndLast($network_id, ConstantsBooleans::YES);
        return array('Garage.id' => Hash::extract($garages, '{n}.GarageNetwork.garage_id'));
    }

    private function _conditionNetworkStatus($status)
    {
        $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
        $garages = $this->GarageNetwork->findAllByStatusAndLast($status, ConstantsBooleans::YES);
        return array('Garage.id' => Hash::extract($garages, '{n}.GarageNetwork.garage_id'));
    }

    private function _conditionNetworkIdStatus($network_id, $status)
    {
        $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
        $garages = $this->GarageNetwork->findAllByNetworkIdAndStatusAndLast($network_id, $status, ConstantsBooleans::YES);
        return array('Garage.id' => Hash::extract($garages, '{n}.GarageNetwork.garage_id'));
    }

    private function _conditionRegion($aag_region_id)
    {
        return array('Garage.aag_region_id' => $aag_region_id);
    }

    private function _conditionCountry($country_id)
    {
        $garages = $this->getGaragesByCountry($country_id);
        return array('Garage.id' => Hash::extract($garages, '{n}'));
    }

    private function _conditionTradingGroup($trading_group_id)
    {
        $garages = $this->getGaragesWithTradingGroupById($trading_group_id);
        return array('Garage.id' => Hash::extract($garages, '{n}.Garage.id'));
    }

    private function _conditionClientType($client_type)
    {
        return array('Garage.client_type' => $client_type);
    }

    private function _conditionStatusGarage($status)
    {
        return array('Garage.status' => $status);
    }

    private function _conditionExternalAgreements($external_agreements)
    {
        $garages = $this->GarageAgreement->findAllByAgreementId($external_agreements);
        return array('Garage.id' => Hash::extract($garages, '{n}.GarageAgreement.garage_id'));
    }

    private function _conditionInternalAgreements($internal_agreements)
    {
        $garages = $this->GarageAgreement->findAllByAgreementCode($internal_agreements);
        return array('Garage.id' => Hash::extract($garages, '{n}.GarageAgreement.garage_id'));
    }

    private function _conditionGarage_name($garage_name)
    {
        $this->Garage = ClassRegistry::init('Garage');
        $garages = $this->Garage->findAllById($garage_name);
        return array('Garage.id' => Hash::extract($garages, '{n}.Garage.id'));
    }

    private function _conditionNetwork_name($network_name)
    {
        return array('Network.id =' => $network_name);
    }

    private function _conditionDateFrom($date_from)
    {
        return array('DATE(TrainingCreditNetwork.creation_date) >=' => Fecha::toFormatoBd($date_from));
    }

    private function _conditionDateTo($date_to)
    {
        return array('DATE(TrainingCreditNetwork.creation_date) <=' => Fecha::toFormatoBd($date_to));
    }

    private function _conditionAnnexDetail($annex_detail_id)
    {
        $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
        $garages = $this->GarageNetwork->findAllByAnnexDetailId($annex_detail_id);
        return array('Garage.id' => Hash::extract($garages, '{n}.GarageNetwork.garage_id'));
    }

    private function _conditionTrainingAllowance($training_allowance_id)
    {
        $this->TrainingAllowance = ClassRegistry::init('TrainingAllowance');
        $training_allowance = $this->TrainingAllowance->findFirstById($training_allowance_id);
        return array(
            'TrainingAllowance.start_date' => $training_allowance['TrainingAllowance']['start_date'],
            'TrainingAllowance.end_date' => $training_allowance['TrainingAllowance']['end_date']
        );
    }

    private function _conditionIsActual($is_actual)
    {
        return array('TrainingAllowance.is_actual =' => $is_actual);
    }

    public function conditionsJoins($fields)
    {
        $conditionsJoins = array();
        if (!empty($fields['trading_group_id'])) {
            $conditionsJoins[] = $this->_conditionJoinTradingGroup($fields['trading_group_id']);
        }
        if (!empty($fields['network_id']) && !empty($fields['status_id'])) {
            $conditionsJoins[] = $this->_conditionJoinNetworkIdStatus($fields['network_id'], $fields['status_id']);
        } else {
            if (!empty($fields['status_id'])) {
                $conditionsJoins[] = $this->_conditionJoinNetworkStatus($fields['status_id']);
            }
            if (!empty($fields['network_id'])) {
                $conditionsJoins[] = $this->_conditionJoinNetwork($fields['network_id'], false);
            }
        }
        // only for recommended garages and quoting
        if (!empty($fields['network_id_recommended_quoting'])) {
            $conditionsJoins[] = $this->_conditionJoinNetwork($fields['network_id_recommended_quoting'], true);
        }
        if (!empty($fields['service_id'])) {
            $conditionsJoins[] = $this->_conditionJoinService($fields['service_id']);
        }
        if (!empty($fields['vehicle_type_id'])) {
            $conditionsJoins[] = $this->_conditionJoinVehicleType($fields['vehicle_type_id']);
        }
        if (!empty($fields['distributor_id'])) {
            $conditionsJoins[] = $this->_conditionJoinDistributor($fields['distributor_id']);
        }
        if (!empty($fields['bdm_id'])) {
            $conditionsJoins[] = $this->_conditionJoinBDM($fields['bdm_id']);
        }
        if (!empty($fields['external_agreements'])) {
            $conditionsJoins[] = $this->_conditionJoinExternalAgreements($fields['external_agreements']);
        }
        if (!empty($fields['internal_agreements'])) {
            $conditionsJoins[] = $this->_conditionJoinInternalAgreements($fields['internal_agreements']);
        }
        if (!empty($fields['annex_detail_id'])) {
            $conditionsJoins[] = $this->_conditionJoinAnnexDetail($fields['annex_detail_id']);
        }
        if (!empty($fields['enquiries_active'])) {
            $conditionsJoins[] = $this->_conditionJoinEnquiriesActive($fields['enquiries_active'], $fields['network_id']);
        }
        if (!empty($fields['quoting_active'])) {
            $conditionsJoins[] = $this->_conditionJoinQuotingActive($fields['quoting_active'], $fields['network_id']);
        }
        return $conditionsJoins;
    }

    private function _conditionJoinTradingGroup($trading_group_id)
    {
        return array(
            'alias' => 'GarageNetworkTrading',
            'table' => 'garages_networks',
            'type' => 'INNER',
            'conditions' => array(
                'GarageNetworkTrading.garage_id = Garage.id',
                'GarageNetworkTrading.trading_group_id' => $trading_group_id,
            ),
        );
    }

    private function _conditionJoinNetworkIdStatus($network_id, $status_id)
    {
        return array(
            'alias' => 'GarageNetwork',
            'table' => 'garages_networks',
            'type' => 'INNER',
            'conditions' => array(
                'GarageNetwork.garage_id = Garage.id',
                'GarageNetwork.network_id' => $network_id,
                'GarageNetwork.status' => $status_id,
                'GarageNetwork.last' => ConstantsBooleans::YES
            ),
        );
    }

    private function _conditionJoinNetworkStatus($status_id)
    {
        return array(
            'alias' => 'GarageNetworkStatus',
            'table' => 'garages_networks',
            'type' => 'INNER',
            'conditions' => array(
                'GarageNetworkStatus.garage_id = Garage.id',
                'GarageNetworkStatus.status' => $status_id,
                'GarageNetworkStatus.last' => ConstantsBooleans::YES
            ),
        );
    }

    private function _conditionJoinNetwork($network_id, $isLive)
    {
        $conditions = array(
            'GarageNetworkLast.garage_id = Garage.id',
            'GarageNetworkLast.network_id' => $network_id,
            'GarageNetworkLast.last' => ConstantsBooleans::YES
        );

        if ($isLive) {
            $conditions[] = array('GarageNetworkLast.status' => ConstantsNetworksStatus::LIVE);
        }
        return array(
            'alias' => 'GarageNetworkLast',
            'table' => 'garages_networks',
            'type' => 'INNER',
            'conditions' => $conditions
        );
    }

    private function _conditionJoinService($service_id)
    {
        return array(
            'alias' => 'GarageService',
            'table' => 'garages_services',
            'type' => 'INNER',
            'conditions' => array(
                'GarageService.garage_id = Garage.id',
                'GarageService.service_id' => $service_id,
            ),
        );
    }

    private function _conditionJoinVehicleType($vehicle_type_id)
    {
        return array(
            'alias' => 'GarageVehicleType',
            'table' => 'garages_vehicle_types',
            'type' => 'INNER',
            'conditions' => array(
                'GarageVehicleType.garage_id = Garage.id',
                'GarageVehicleType.vehicle_type_id' => $vehicle_type_id,
            ),
        );
    }

    private function _conditionJoinDistributor($distributor_id)
    {
        return array(
            'alias' => 'GarageDistributor',
            'table' => 'garages_distributors',
            'type' => 'INNER',
            'conditions' => array(
                'GarageDistributor.garage_id = Garage.id',
                'GarageDistributor.id = (
                    SELECT MIN(gd1.id)
                    FROM garages_distributors AS gd1
                    WHERE gd1.garage_id = Garage.id
                )',
                'GarageDistributor.distributor_id' => $distributor_id,
            ),
        );
    }

    private function _conditionJoinBDM($contact_id)
    {
        return array(
            'alias' => 'GarageContactBdm',
            'table' => 'garages_contacts_bdm',
            'type' => 'INNER',
            'conditions' => array(
                'GarageContactBdm.garage_id = Garage.id',
                'GarageContactBdm.contact_id' => $contact_id,
            ),
        );
    }

    private function _conditionJoinExternalAgreements($external_agreements)
    {
        return array(
            'alias' => 'GarageAgreement',
            'table' => 'garages_agreements',
            'type' => 'INNER',
            'conditions' => array(
                'GarageAgreement.garage_id = Garage.id',
                'GarageAgreement.agreement_id' => $external_agreements,
            ),
        );
    }

    private function _conditionJoinInternalAgreements($internal_agreements)
    {
        return array(
            'alias' => 'GarageAgreement',
            'table' => 'garages_agreements',
            'type' => 'INNER',
            'conditions' => array(
                'GarageAgreement.garage_id = Garage.id',
                'GarageAgreement.agreement_code' => $internal_agreements,
            ),
        );
    }

    private function _conditionJoinAnnexDetail($annex_detail_id)
    {
        return array(
            'alias' => 'GarageNetworkAnnexDetail',
            'table' => 'garages_networks',
            'type' => 'INNER',
            'conditions' => array(
                'GarageNetworkAnnexDetail.garage_id = Garage.id',
                'GarageNetworkAnnexDetail.annex_detail_id' => $annex_detail_id,
            ),
        );
    }

    private function _conditionJoinEnquiriesActive($enquiries_active, $network_id)
    {
        return array(
            'alias' => 'GarageNetworkEnquiriesActive',
            'table' => 'garages_networks',
            'type' => 'INNER',
            'conditions' => array(
                'GarageNetworkEnquiriesActive.garage_id = Garage.id',
                'GarageNetworkEnquiriesActive.enquiries_active' => $enquiries_active,
                'GarageNetworkEnquiriesActive.network_id' => $network_id
            ),
        );
    }

    private function _conditionJoinQuotingActive($quoting_active, $network_id)
    {
        return array(
            'alias' => 'GarageNetworkEnquiriesActive',
            'table' => 'garages_networks',
            'type' => 'INNER',
            'conditions' => array(
                'GarageNetworkEnquiriesActive.garage_id = Garage.id',
                'GarageNetworkEnquiriesActive.quoting_active' => $quoting_active,
                'GarageNetworkEnquiriesActive.network_id' => $network_id
            ),
        );
    }

    public function _queryTraining($index, $networks)
    {
        $query = $this->_queries[$index];

        if (!empty($networks)) {
            $query['conditions'][] = array('Network.name' => $networks);
        }

        return $query;
    }

    public function _queryTrainingGarage($index, $garage_id)
    {
        $query = $this->_queries[$index];

        if (!empty($garage_id)) {
            $query['conditions'][] = array('Garage.id' => $garage_id);
        }

        return $query;
    }

    public function add_garage($garage, $user)
    {
        $fields = array(
            'Garage' => array(
                'business_name',
                'name',
                'g_number_id',
                'ref_code',
                'siret',
                'VAT_code',
                'address1',
                'address2',
                'address3',
                'address4',
                'latitude',
                'longitude',
                'city_id',
                'province_id',
                'postcode',
                'phone',
                'mobile',
                'phone_international',
                'email',
                'web',
                'fax',
                'service_24h_phone',
                'status',
                'creation_date',
                'modification_date',
                'visit_frequency',
                'visit_monday',
                'visit_tuesday',
                'visit_wednesday',
                'visit_thursday',
                'visit_friday',
                'documents_legal',
                'diesel_liability',
                'affiliation_assembly',
                'insurance_agreement_id',
                'user_id',
                'slug',
                'town',
                'aag_region_id',
                'manually_created',
                'erp_id',
                'creditor_number',
                'payment_terms',
                'company_code',
                'language_id',
                'sales_area_id'
            )
        );

        $garage['Garage']['creation_date'] = date('Y-m-d H:i:s');
        $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');
        $garage['Garage']['user_id'] = $user['id'];
        if (
            !empty($garage['Garage']['web']) &&
            substr($garage['Garage']['web'], 0, 7) !== "http://" &&
            substr($garage['Garage']['web'], 0, 8) !== "https://"
        ) {
            $garage['Garage']['web'] = 'http://' . $garage['Garage']['web'];
        }
        $this->create();

        $garageBd = $this->guardar($garage, $fields);
        if (!$garageBd) {
            return false;
        }
        $this->saveGarageCode($garageBd);
        $this->generateGuid($garageBd);
        $this->commit();
        return $garageBd;
    }

    public function add_garage_de($garage, $user)
    {
        $fields = array(
            'Garage' => array(
                'name',
                'business_name',
                'address1',
                'status',
                'comment',
                'creation_date',
                'modification_date',
                'user_id',
            )
        );

        $garage['Garage']['business_name'] = $garage['Garage']['name'];
        $garage['Garage']['creation_date'] = date('Y-m-d H:i:s');
        $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');
        $garage['Garage']['user_id'] = $user['id'];
        if (!isset($garage['Garage']['address1'])) {
            $garage['Garage']['address1'] = '--';
        }
        $this->create();
        $garage_bd = $this->guardar($garage, $fields);
        if (!$garage_bd) {
            return false;
        }
        return $garage_bd;
    }

    public function saveGarageCode($garageNew)
    {
        $fields = array(
            'Garage' => array(
                'garage_code',
            )
        );

        $garage['Garage']['garage_code'] = $garageNew['Garage']['id'];
        $garageBd = $this->guardar($garage, $fields);
        if (!$garageBd) {
            return false;
        }
        return $garageBd;
    }

    public function saveLastVisit($garage_id, $last_visit)
    {
        $fields = array(
            'Garage' => array(
                'last_visit',
            )
        );

        $garage['Garage']['id'] = $garage_id;
        $garage['Garage']['last_visit'] = $last_visit;
        $garage_bd = $this->guardar($garage, $fields);
        if (!$garage_bd) {
            return false;
        }
        return $garage_bd;
    }

    public function getCompleteList($aag_region_id, $role_id)
    {
        $conditions_aag_region = array();
        if ($role_id != ConstantsRoles::SUPER_ADMIN) {
            $conditions_aag_region = array('Garage.aag_region_id' => $aag_region_id);
        }
        return $this->find(
            'list',
            array(
                'conditions' => $conditions_aag_region,
                'fields' => array(
                    'complete_name'
                ),
                'order' => array(
                    'complete_name'
                ),
            )
        );
    }

    public function getCompleteListFrance()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'complete_name_france'
                ),
                'order' => array(
                    'complete_name_france'
                ),
            )
        );
    }

    public function add_opening_garage($garage)
    {
        $fields = array(
            'Garage' => array(
                'monday_open_1',
                'monday_closed_1',
                'monday_open_2',
                'monday_closed_2',
                'tuesday_open_1',
                'tuesday_closed_1',
                'tuesday_open_2',
                'tuesday_closed_2',
                'wednesday_open_1',
                'wednesday_closed_1',
                'wednesday_open_2',
                'wednesday_closed_2',
                'thursday_open_1',
                'thursday_closed_1',
                'thursday_open_2',
                'thursday_closed_2',
                'friday_open_1',
                'friday_closed_1',
                'friday_open_2',
                'friday_closed_2',
                'saturday_open_1',
                'saturday_closed_1',
                'saturday_open_2',
                'saturday_closed_2',
                'sunday_open_1',
                'sunday_closed_1',
                'sunday_open_2',
                'sunday_closed_2',
                'modification_date',
            )
        );

        $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');

        $garage_bd = $this->guardar($garage, $fields);
        if (!$garage_bd) {
            return false;
        }

        return $garage_bd;
    }

    public function getOpeningHours($garage)
    {
        $week_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday');
        $weekend = array('saturday', 'sunday');

        $hours = array(
            'monday_friday' => $this->timeText($week_days, $garage),
            'weekend' => $this->timeText($weekend, $garage),
        );

        foreach ($garage['Garage'] as $column => $valor) {
            foreach ($week_days as $day) {
                if (strpos($column, $day . '_') === 0) {
                    $hours[$column] = $valor;
                }
            }
            foreach ($weekend as $day) {
                if (strpos($column, $day . '_') === 0) {
                    $hours[$column] = $valor;
                }
            }
        }


        if ($hours['monday_friday'] == 'closed' and $hours['weekend'] == 'closed') {
            $hours['monday_friday'] = $hours['weekend'] = null;
        }
        return $hours;
    }

    private function timeText($days, $garage)
    {
        $ranks = array(array('open_1', 'closed_1'), array('open_2', 'closed_2'));
        $short_text = '';
        $long_text = '';
        foreach ($days as $day) {
            $h = $garage['Garage'][$day . '_' . $ranks[0][0]] . ' - ' . $garage['Garage'][$day . '_' . $ranks[0][1]];
            if ($h != ' - ') {
                $h2 = $garage['Garage'][$day . '_' . $ranks[1][0]] . ' - ' . $garage['Garage'][$day . '_' . $ranks[1][1]];
                if ($h2 != ' - ') {
                    $h .= ' and ' . $h2;
                }
            }
            if ($h == ' - ') {
                $h = 'closed';
            }
            if ($short_text === '') {
                $short_text = $h;
            } elseif ($short_text != $h) {
                $short_text = null;
            }
            if ($long_text != '') {
                $long_text .= ', ';
            }
            $long_text .= $day . ' (' . $h . ')';
        }
        return ($short_text != null) ? $short_text : $long_text;
    }

    public function add_activities_and_services_garage($garage, $user)
    {
        $config = CakeSession::read('Auth.User.Config');
        //Add new services
        $this->GarageService = ClassRegistry::init('GarageService');
        $old_data = $this->GarageService->findAllByGarageId($garage['Garage']['id']);

        $this->GarageService->removeGarageServices($garage['Garage']['id'], $user);

        foreach ($garage['Garage']['Service'] as $service) {
            if ($service != ConstantsBooleans::NO) {
                $this->GarageService->addRelationGarageService($garage['Garage']['id'], $service);
            }
        }

        $new_data = $this->GarageService->findAllByGarageId($garage['Garage']['id']);
        //$this->GarageService->getList( $old_data, $new_data, $user, $garage['Garage']['id'] );

        ///Add new Vehicles
        $this->GarageVehicle = ClassRegistry::init('GarageVehicle');
        $this->GarageVehicle->removeGarageVehicles($garage['Garage']['id']);
        foreach ($garage['Garage']['Vehicle'] as $vehicle) {
            if ($vehicle != ConstantsBooleans::NO) {
                $this->GarageVehicle->addRelationGarageVehicle($garage['Garage']['id'], $vehicle);
            }
        }

        ///Add new Specialist
        $this->GarageSpecialistMake = ClassRegistry::init('GarageSpecialistMake');
        $this->GarageSpecialistMake->removeGarageSpecialists($garage['Garage']['id']);
        foreach ($garage['Garage']['VehicleSpecialist'] as $vehicle) {
            if ($vehicle != ConstantsBooleans::NO) {
                $this->GarageSpecialistMake->addRelationGarageSpecialists($garage['Garage']['id'], $vehicle);
            }
        }

        ///Add new Vehicles Types
        $this->GarageVehicleType = ClassRegistry::init('GarageVehicleType');
        $this->GarageVehicleType->removeGarageVehiclesTypes($garage['Garage']['id']);
        foreach ($garage['Garage']['VehicleType'] as $vehicle_type) {
            if ($vehicle_type != ConstantsBooleans::NO) {
                $this->GarageVehicleType->addRelationGarageVehicleType($garage['Garage']['id'], $vehicle_type);
            }
        }

        ///Add new parts brands
        if ($config[ConstantsConfig::PARTS_BRANDS]) {
            $this->GarageBrand = ClassRegistry::init('GarageBrand');
            $this->GarageBrand->removeGaragePartBrand($garage['Garage']['id']);
            if (isset($garage['Garage']['Brand'])) {
                foreach ($garage['Garage']['Brand'] as $part_brand) {
                    if ($part_brand != ConstantsBooleans::NO) {
                        $this->GarageBrand->addRelationGaragePartBrand($garage['Garage']['id'], $part_brand);
                    }
                }
            }
        }

        // Delete Facilities and add new
        $this->GarageFacility = ClassRegistry::init('GarageFacility');
        $old_data = $this->GarageFacility->findAllByGarageId($garage['Garage']['id']);
        if (!empty($old_data)) {
            $old_data = array_combine(
                Hash::extract($old_data, '{n}.GarageFacility.facility_id'),
                Hash::extract($old_data, '{n}.GarageFacility.facility_id')
            );
        } else {
            $old_data = array();
        }
        $save_data = $this->GarageFacility->update_garage_facility($garage['Garage']['id'], (isset($garage['Garage']['facilities']) ? $garage['Garage']['facilities'] : null));
        $new_data = $this->GarageFacility->findAllByGarageId($garage['Garage']['id']);
        if (!empty($new_data)) {
            $new_data = array_combine(
                Hash::extract($new_data, '{n}.GarageFacility.facility_id'),
                Hash::extract($new_data, '{n}.GarageFacility.facility_id')
            );
        } else {
            $new_data = array();
        }
        $this->LogChange = ClassRegistry::init('LogChange');
        $this->LogChange->get_params_create_log_edit(
            $old_data,
            $new_data,
            $this->GarageFacility->table,
            $user,
            $garage['Garage']['id'],
            ConstantsLogType::GARAGE,
            'facility_id'
        );

        $this->edit_modification_date_garage($garage['Garage']['id']);

        $this->commit();
        return $garage;
    }

    public function edit_creation_date($creation_date, $garage_id)
    {
        $fields = array(
            'Garage' => array(
                'creation_date',
            )
        );

        $garage['Garage']['id'] = $garage_id;
        $garage['Garage']['creation_date'] = $creation_date;

        $garage_bd = $this->guardar($garage, $fields);
        if (!$garage_bd) {
            return false;
        }

        return $garage_bd;
    }

    public function add_aditional_info_and_other_details_garage($garage, $user)
    {
        $fields = array(
            'Garage' => array(
                'modification_date',
                'ramps',
                'MOT_bays',
                'technician',
                'spend_this_month',
                'spend_last_month',
                'spend_12_month',
                'spend_projected',
                'fleet_work_direction',
                'fleet_mot',
                'fleet_labour_rate',
                'long_life_oil_price_b2b',
                'standard_oil_price_b2b',
                'collection_delivery_b2b',
                'retail_mot',
                'retail_labour_rate',
                'long_life_oil_price_b2c',
                'standard_oil_price_b2c',
                'collection_delivery_b2c',
                'ev_charge_points',
                'kwh_charging_retail_price',
                'kwh_charging_fleet_price',
                'ev_ppe_audited_date',
                'courtesy_car',
            )
        );

        if (!isset($garage['Garage']['fleet_work_direction'])) {
            $garage['Garage']['fleet_work_direction'] = 0;
        }

        if (!isset($garage['Garage']['collection_delivery_b2b'])) {
            $garage['Garage']['collection_delivery_b2b'] = 0;
        }

        if (!isset($garage['Garage']['collection_delivery_b2c'])) {
            $garage['Garage']['collection_delivery_b2c'] = 0;
        }

        if (isset($garage['Garage']['ev_ppe_audited_date'])) {
            $garage['Garage']['ev_ppe_audited_date'] = Fecha::toFormatoBd($garage['Garage']['ev_ppe_audited_date']);
        }

        if (!isset($garage['Garage']['courtesy_car'])) {
            $garage['Garage']['courtesy_car'] = ConstantsBooleans::NO;
        }

        $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');
        $garage_bd = $this->guardar($garage, $fields);
        if (!$garage_bd) {
            return false;
        }

        // Delete Postcodes B2B and add new
        $this->GarageB2bPostcode = ClassRegistry::init('GarageB2bPostcode');
        $oldData = array();
        $oldDataForB2b = $this->GarageB2bPostcode->findAllByGarageId($garage_bd['Garage']['id']);
        if (!empty($oldDataForB2b)) {
            $oldData = Hash::extract($oldDataForB2b, '{n}.GarageB2bPostcode.postcode_id');
        }
        $newData =  isset($garage['Garage']['postcode_b2b']) ? $garage['Garage']['postcode_b2b'] : null;
        if ($oldData !== $newData) {
            $saveData = $this->GarageB2bPostcode->update_garage_postcode($garage_bd['Garage']['id'], $oldData, $newData);
            if ($saveData) {
                $newDataForB2b = $this->GarageB2bPostcode->findAllByGarageId($garage_bd['Garage']['id']);

                $this->LogChange = ClassRegistry::init('LogChange');
                $this->PostcodeProvince = ClassRegistry::init('PostcodeProvince');

                // Logs for remove data
                foreach ($oldDataForB2b as $oldEntry) {
                    $postcode = $this->PostcodeProvince->findById($oldEntry['GarageB2bPostcode']['postcode_id']);
                    $oldEntry['GarageB2bPostcode']['postcode_id'] = $postcode['PostcodeProvince']['postcode'];
                    $this->LogChange->get_params_create_log_delete(
                        $oldEntry['GarageB2bPostcode'],
                        $this->GarageB2bPostcode->table,
                        $user,
                        $garage_bd['Garage']['id'],
                        ConstantsLogType::GARAGE,
                        'postcode_id_b2b'
                    );
                }
                // Logs for new data
                foreach ($newDataForB2b as $newEntry) {
                    $postcode = $this->PostcodeProvince->findById($newEntry['GarageB2bPostcode']['postcode_id']);
                    $newEntry['GarageB2bPostcode']['postcode_id'] = $postcode['PostcodeProvince']['postcode'];
                    $this->LogChange->get_params_create_log_add(
                        $newEntry['GarageB2bPostcode'],
                        $this->GarageB2bPostcode->table,
                        $user,
                        $garage_bd['Garage']['id'],
                        ConstantsLogType::GARAGE,
                        'postcode_id_b2b'
                    );
                }
            }
        }

        // Delete Postcodes B2C and add new
        $this->GarageB2cPostcode = ClassRegistry::init('GarageB2cPostcode');
        $oldData = array();
        $oldDataForB2c = $this->GarageB2cPostcode->findAllByGarageId($garage_bd['Garage']['id']);
        if (!empty($oldDataForB2c)) {
            $oldData = Hash::extract($oldDataForB2c, '{n}.GarageB2cPostcode.postcode_id');
        }
        $newData =  isset($garage['Garage']['postcode_b2c']) ? $garage['Garage']['postcode_b2c'] : null;
        if ($oldData !== $newData) {
            $saveData = $this->GarageB2cPostcode->update_garage_postcode($garage_bd['Garage']['id'], $oldData, $newData);
            if ($saveData) {
                $newDataForB2c = $this->GarageB2cPostcode->findAllByGarageId($garage_bd['Garage']['id']);

                // Logs for remove data
                foreach ($oldDataForB2c as $oldEntry) {
                    $postcode = $this->PostcodeProvince->findById($oldEntry['GarageB2cPostcode']['postcode_id']);
                    $oldEntry['GarageB2cPostcode']['postcode_id'] = $postcode['PostcodeProvince']['postcode'];
                    $this->LogChange->get_params_create_log_delete(
                        $oldEntry['GarageB2cPostcode'],
                        $this->GarageB2cPostcode->table,
                        $user,
                        $garage_bd['Garage']['id'],
                        ConstantsLogType::GARAGE,
                        'postcode_id_b2c'
                    );
                }
                // Logs for new data
                foreach ($newDataForB2c as $newEntry) {
                    $postcode = $this->PostcodeProvince->findById($newEntry['GarageB2cPostcode']['postcode_id']);
                    $newEntry['GarageB2cPostcode']['postcode_id'] = $postcode['PostcodeProvince']['postcode'];
                    $this->LogChange->get_params_create_log_add(
                        $newEntry['GarageB2cPostcode'],
                        $this->GarageB2cPostcode->table,
                        $user,
                        $garage_bd['Garage']['id'],
                        ConstantsLogType::GARAGE,
                        'postcode_id_b2c'
                    );
                }
            }
        }

        $this->commit();
        return $garage_bd;
    }

    public function add_marketing_and_image_garage($garage)
    {
        $fields = array(
            'Garage' => array(
                'lead_source',
                'marketing_email',
                'interests',
                'modification_date',
            )
        );

        $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');
        $garage_bd = $this->guardar($garage, $fields);
        if (!$garage_bd) {
            return false;
        }

        $this->commit();
        return $garage_bd;
    }

    public function edit_modification_date_garage($garage_id)
    {
        $fields = array(
            'Garage' => array(
                'modification_date',
            )
        );

        $garage['Garage']['id'] = $garage_id;
        $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');

        $garage_bd = $this->guardar($garage, $fields);
        if (!$garage_bd) {
            return false;
        }

        $this->commit();
        return $garage_bd;
    }

    public function UpdateGarageLastVisit($garage_id, $last_visit)
    {
        $fields = array(
            'Garage' => array(
                'last_visit',
            )
        );

        $garage['Garage']['id'] = $garage_id;
        $garage['Garage']['last_visit'] = $last_visit;

        $garage_bd = $this->guardar($garage, $fields);
        if (!$garage_bd) {
            return false;
        }

        $this->commit();
        return $garage_bd;
    }

    public function edit_garage($garage)
    {
        $fields = array(
            'Garage' => array(
                'business_name',
                'name',
                'g_number_id',
                'ref_code',
                'foundation_year',
                'siret',
                'VAT_code',
                'address1',
                'address2',
                'address3',
                'address4',
                'latitude',
                'longitude',
                'town',
                'province_id',
                'postcode',
                'phone',
                'mobile',
                'phone_international',
                'email',
                'web',
                'fax',
                'service_24h_phone',
                'status',
                'visit_frequency',
                'visit_monday',
                'visit_tuesday',
                'visit_wednesday',
                'visit_thursday',
                'visit_friday',
                'documents_legal',
                'diesel_liability',
                'affiliation_assembly',
                'insurance_agreement_id',
                'modification_date',
                'city_id',
                'slug',
                'town',
                'aag_region_id',
                'manually_created',
                'erp_email',
                'erp_id',
                'creditor_number',
                'payment_terms',
                'company_code',
                'language_id',
                'sales_area_id'
            )
        );

        $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');

        if (
            !empty($garage['Garage']['web']) &&
            substr($garage['Garage']['web'], 0, 7) !== "http://" &&
            substr($garage['Garage']['web'], 0, 8) !== "https://"
        ) {
            $garage['Garage']['web'] = 'http://' . $garage['Garage']['web'];
        }

        $garageBd = $this->guardar($garage, $fields);
        if (!$garageBd) {
            return false;
        }

        $this->commit();
        return $garageBd;
    }

    public function findByGarageIdList()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'Garage.g_number_id',
                    'Garage.id',
                )
            )
        );
    }

    public function findByGarageName()
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'table' => 'garages_contacts_bdm',
                        'alias' => 'GarageContactBdm',
                        'type' => 'Left',
                        'conditions' => array(
                            'GarageContactBdm.garage_id = Garage.id'
                        )
                    ),
                ),
                'fields' => array(
                    'Garage.id',
                    'Garage.name',
                    'Garage.last_visit',
                    'Garage.town',
                    'Garage.address1',
                    'Garage.address2',
                    'Garage.address3',
                    'Garage.address4',
                    'Garage.phone',
                    'Garage.g_number_id',
                    'Garage.ref_code',
                    'group_concat(distinct GarageContactBdm.contact_id separator ",") as bdms',
                ),
                'group' => array(
                    'Garage.id,Garage.name,Garage.last_visit,Garage.town,Garage.address1,Garage.address2,Garage.address3,Garage.address4,Garage.phone,Garage.g_number_id,Garage.ref_code'
                ),
            )
        );
    }

    public function getPrincipalImageDatas($garage_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'table' => 'garages_images',
                        'alias' => 'GarageImage',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageImage.principal' => true,
                            'GarageImage.garage_id = Garage.id'
                        )
                    ),
                ),
                'conditions' => array(
                    'Garage.id' => $garage_id,
                ),
                'fields' => array(
                    'GarageImage.*',
                )
            )
        );
    }

    public function getGaragesWithTradingGroupById($trading_group_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),

                ),
                'conditions' => array(
                    'GarageNetwork.trading_group_id' => $trading_group_id,
                ),
                'fields' => array(
                    'Garage.id'
                ),
            )
        );
    }

    public function getVisits($conditions)
    {
        $garages = $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Appointment.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'GarageDistributor',
                    'table' => 'garages_distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageDistributor.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'GarageRoute',
                    'table' => 'garages_routes',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageRoute.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'GarageContactBdm',
                    'table' => 'garages_contacts_bdm',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageContactBdm.garage_id = Garage.id'
                    )
                ),
            ),
            'conditions' => $conditions,
            'group' => array(
                'Garage.id'
            ),
            'fields' => array(
                'Garage.id',
                'Garage.name',
                'Garage.g_number_id',
                'Garage.client_type',
                'Garage.postcode',
                'Garage.town',
                'Garage.address1',
                'Garage.latitude',
                'Garage.longitude',
                'Garage.last_visit',
                'GarageNetwork.status',
                'Appointment.date'
            ),
            'order' => array(
                array('Garage.last_visit', 'Garage.name')
            ),
            'limit' => ConstantsPagination::SIZE_PLANNING
        ));

        return $garages;
    }

    public function getLastVisits()
    {

        $user = CakeSession::read('Auth.User');
        $role_id = $user['role_id'];
        if ($role_id == ConstantsRoles::BDM_AAG || $role_id == ConstantsRoles::BDM_TG) {
            $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');
            $garages_users = $this->GarageContactBdm->findAllByContactId($user['contact_id']);
            $conditions = array('Garage.id' => Hash::extract($garages_users, '{n}.GarageContactBdm.garage_id'));
        } else if ($role_id != ConstantsRoles::SUPER_ADMIN) {
            $user_aag_region_id = $user['aag_region_id'];
            $conditions[] = array('Garage.aag_region_id' => $user_aag_region_id);
        } else {
            $conditions = array();
        }

        $garages = $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Appointment.garage_id = Garage.id',
                        'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
                        'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT
                    ),
                ),
            ),
            'group' => array(
                'Garage.id',
                'Garage.business_name',
                'Garage.town',
                'Garage.address1'
            ),
            'order' => array(
                'max(Appointment.date)' => 'desc',
                'Garage.name' => 'asc'
            ),
            'conditions' => $conditions,
            'limit' => ConstantsPagination::SIZE_PAGE_SMALL,
            'fields' => array(
                'Garage.id',
                'Garage.name',
                'Garage.business_name',
                'Garage.town',
                'Garage.address1',
                'max(Appointment.date) as max_appointment_date'
            )
        ));

        return $garages;
    }

    public function getGaragesVisits($conditions)
    {
        $garages = $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'GarageRoute',
                    'table' => 'garages_routes',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageRoute.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'GarageContactBdm',
                    'table' => 'garages_contacts_bdm',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageContactBdm.garage_id = Garage.id'
                    )
                ),
                array(
                    'alias' => 'GarageDistributor',
                    'table' => 'garages_distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageDistributor.garage_id = Garage.id',
                    ),
                ),
            ),
            'conditions' => $conditions,
            'fields' => array(
                'Garage.id',
                'Garage.business_name',
                'Garage.latitude',
                'Garage.longitude',
                'Garage.town',
                'Garage.last_visit',
                'GarageNetwork.network_id',
                'GarageNetwork.status',
                'GarageDistributor.distributor_id',
                'group_concat(distinct GarageRoute.route_id separator ",") as routes',
                'group_concat(distinct GarageContactBdm.contact_id separator ",") as contacts',
            ),
            'group' => array(
                'Garage.id'
            ),
            'order' => array(
                'Garage.business_name',
            )
        ));

        foreach ($garages as $key => $garage) {
            if (!is_null($garages[$key]['Garage']['last_visit'])) {
                $three_months = date('Y-m-d', strtotime("-3 months"));
                $six_months = date('Y-m-d', strtotime("-6 months"));
                if ($garages[$key]['Garage']['last_visit'] < $six_months) {
                    $garages[$key]['Garage']['last_visit'] = ConstantsLastVisit::PLUS_SIX_MONTHS;
                } else if ($garages[$key]['Garage']['last_visit'] > $three_months) {
                    $garages[$key]['Garage']['last_visit'] = ConstantsLastVisit::MINUS_THREE_MONTHS;
                } else {
                    $garages[$key]['Garage']['last_visit'] = ConstantsLastVisit::THREE_MONTHS_SIX_MONTHS;
                }
            } else {
                $garages[$key]['Garage']['last_visit'] = ConstantsLastVisit::NO_VISIT;
            }
        }

        return $garages;
    }

    /**
     * New Garage comes from a JSON, The data comes from a Json left on the server.
     */
    public function createGarage($garage_json, &$errors)
    {
        $clear_characters = array("#", "*", " ", ".");
        $clear_characters_address = array("#");
        $garage_json['Garage']['Tel'] = str_replace(' ', '', $garage_json['Garage']['Tel']);
        $garage_tmp = array(
            'Garage' => array(
                'business_name' => ($garage_json['Garage']['BusinessName']) ? $garage_json['Garage']['BusinessName'] : null,
                'name' => ($garage_json['Garage']['BusinessName']) ? $garage_json['Garage']['BusinessName'] : null,
                'garage_code' => ($garage_json['Garage']['GarageNumberCRM']) ? $garage_json['Garage']['GarageNumberCRM'] : null,
                'g_number_id' => ($garage_json['Garage']['GarageNumberCRM']) ? $garage_json['Garage']['GarageNumberCRM'] : null,
                'ref_code' => ($garage_json['Garage']['GarageRef']) ? $garage_json['Garage']['GarageRef'] : null,
                'status' => ($garage_json['Garage']['StatusCode'] == true) ? ConstantsGarageStatus::ACTIVE : ConstantsGarageStatus::ONSTOP,
                'address1' => ($garage_json['Garage']['Addr1']) ? str_replace($clear_characters_address, '', $garage_json['Garage']['Addr1']) : null,
                'address2' => ($garage_json['Garage']['Addr2']) ? $garage_json['Garage']['Addr2'] : null,
                'address3' => ($garage_json['Garage']['Addr3']) ? $garage_json['Garage']['Addr3'] : null,
                'address4' => ($garage_json['Garage']['Addr4']) ? $garage_json['Garage']['Addr4'] : null,
                'latitude' =>  null,
                'longitude' => null,
                'town' => ($garage_json['Garage']['AddrTown']) ? trim(str_replace($clear_characters, '', $garage_json['Garage']['AddrTown'])) : null,
                'city_id' => null,
                'province_id' => null,
                'postcode' => ($garage_json['Garage']['AddrPCode']) ? $garage_json['Garage']['AddrPCode'] : null,
                'phone' => ($garage_json['Garage']['Tel']) && is_numeric($garage_json['Garage']['Tel']) ? trim(str_replace($clear_characters, '', $garage_json['Garage']['Tel'])) : null,
                'mobile' => ($garage_json['Garage']['Mobile']) && is_numeric($garage_json['Garage']['Mobile']) ? trim(str_replace($clear_characters, '', $garage_json['Garage']['Mobile'])) : null,
                'phone_international' =>  null,
                'fax' =>  null,
                // 'email' =>  $this->_validateEmail(trim($garage_json['Garage']['Email'])) ? trim($garage_json['Garage']['Email']) : null,
                'email' => !empty($garage_json['Garage']['Email']) ? trim($garage_json['Garage']['Email']) : null,
                'web' => ($garage_json['Garage']['Website']) ? $garage_json['Garage']['Website'] : null,
                'service_24h_phone' =>  null,
                'monday_open_1' => ($garage_json['Garage']['MonOpen'] != '-1') ? $garage_json['Garage']['MonOpen'] : null,
                'monday_closed_1' => ($garage_json['Garage']['MonClose'] != '-1') ? $garage_json['Garage']['MonClose'] : null,
                'monday_open_2' =>  null,
                'monday_closed_2' =>  null,
                'tuesday_open_1' => ($garage_json['Garage']['TueOpen'] != '-1') ? $garage_json['Garage']['TueOpen'] : null,
                'tuesday_closed_1' => ($garage_json['Garage']['TueClose'] != '-1') ? $garage_json['Garage']['TueClose'] : null,
                'tuesday_open_2' =>  null,
                'tuesday_closed_2' =>  null,
                'wednesday_open_1' => ($garage_json['Garage']['WedOpen'] != '-1') ? $garage_json['Garage']['WedOpen'] : null,
                'wednesday_closed_1' => ($garage_json['Garage']['WedClose'] != '-1') ? $garage_json['Garage']['WedClose'] : null,
                'wednesday_open_2' =>  null,
                'wednesday_closed_2' =>  null,
                'thursday_open_1' => ($garage_json['Garage']['ThruOpen'] != '-1') ? $garage_json['Garage']['ThruOpen'] : null,
                'thursday_closed_1' => ($garage_json['Garage']['ThruClose'] != '-1') ? $garage_json['Garage']['ThruClose'] : null,
                'thursday_open_2' =>  null,
                'thursday_closed_2' =>  null,
                'friday_open_1' => ($garage_json['Garage']['FriOpen'] != '-1') ? $garage_json['Garage']['FriOpen'] : null,
                'friday_closed_1' => ($garage_json['Garage']['FriClose'] != '-1') ? $garage_json['Garage']['FriClose'] : null,
                'friday_open_2' =>  null,
                'friday_closed_2' =>  null,
                'saturday_open_1' => ($garage_json['Garage']['SatOpen'] != '-1') ? $garage_json['Garage']['SatOpen'] : null,
                'saturday_closed_1' => ($garage_json['Garage']['SatClose'] != '-1') ? $garage_json['Garage']['SatClose'] : null,
                'saturday_open_2' =>  null,
                'saturday_closed_2' =>  null,
                'sunday_open_1' => ($garage_json['Garage']['SunOpen'] != '-1') ? $garage_json['Garage']['SunOpen'] : null,
                'sunday_closed_1' => ($garage_json['Garage']['SunClose'] != '-1') ? $garage_json['Garage']['SunClose'] : null,
                'sunday_open_2' =>  null,
                'sunday_closed_2' =>  null,
                'ramps' => ($garage_json['Garage']['Ramps']) ? $garage_json['Garage']['Ramps'] : null,
                'MOT_bays' => ($garage_json['Garage']['MOTBays']) ? $garage_json['Garage']['MOTBays'] : null,
                'spend_this_month' => ($garage_json['Garage']['SpendThisMonth']) ? $garage_json['Garage']['SpendThisMonth'] : null,
                'spend_last_month' => ($garage_json['Garage']['SpendLastMonth']) ? $garage_json['Garage']['SpendLastMonth'] : null,
                'spend_12_month' => ($garage_json['Garage']['Spend12Months']) ? $garage_json['Garage']['Spend12Months'] : null,
                'spend_projected' => ($garage_json['Garage']['ProjectedSpend']) ? $garage_json['Garage']['ProjectedSpend'] : null,
                'foundation_year' => ($garage_json['Garage']['GarageYearEstablished']) ? $garage_json['Garage']['GarageYearEstablished'] : null,
                'lead_source' =>  null,
                'marketing_email' =>  null,
                'interests' => ($garage_json['Garage']['Interests']) ? $garage_json['Garage']['Interests'] : null,
                'creation_date' =>  date('Y-m-d H:i:s'),
                'modification_date' =>  date('Y-m-d H:i:s'),
                'user_id' =>  5,
                'aag_region_id' => Configure::read('AAG_REGION_ID_UK_IRELAND'),
                'manually_created' => ConstantsBooleans::NO
            )
        );

        if (isset($garage_json['Garage']['AddrPCode'])) {
            $prepAddr = (isset($garage_json['Garage']['AddrTown'])) ? $garage_json['Garage']['AddrPCode'] . ' ' . $garage_json['Garage']['AddrPCode'] : $garage_json['Garage']['AddrTown'];
            $prepAddr = (isset($garage_json['Garage']['Addr1'])) ? $prepAddr . ' ' . $garage_json['Garage']['Addr1'] : $prepAddr;
            $prepAddr = str_replace(' ', '+', $prepAddr);
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&key=' . Texto::encryptDecryptText(GOOGLE_API_KEY_REVIEWS_GV, false) . '&sensor=false&v=3');

            $output = json_decode($geocode);
            if (isset($output->results[0])) {
                $latitude = $output->results[0]->geometry->location->lat;
                $longitude = $output->results[0]->geometry->location->lng;
                $garage_tmp['Garage']['latitude'] = $latitude;
                $garage_tmp['Garage']['longitude'] = $longitude;
            } else {
                CakeLog::write('updates', 'The garage could not be geolocated.' . PHP_EOL);
                array_push($errors, 'The garage could not be geolocated');
            }
        }

        //POSTAL CODE
        $post_code_province = ClassRegistry::init('PostcodeProvince');
        $cityClass = ClassRegistry::init('City');
        $provinceClass = ClassRegistry::init('Province');
        $countryClass = ClassRegistry::init('Country');

        if ($garage_tmp['Garage']['postcode'] != null) {
            $postcode = $garage_tmp['Garage']['postcode'];
            $out_code = explode(" ", $postcode);
            $postcode = $out_code[0];

            $region = Configure::read('AAG_REGION_ID_UK_IRELAND');
            $postal_code_province = $post_code_province->findPostcodeProvinceByPostcodeInRegion($postcode, $region);
            if (!empty($postal_code_province)) {
                $garage_tmp['Garage']['province_id'] = $postal_code_province['PostcodeProvince']['province_id'];
                $city = isset($garage_tmp['Garage']['town']) ? $cityClass->findCityByNameInRegion($garage_tmp['Garage']['town'], $region) : '';
                if (empty($city) && isset($garage_tmp['Garage']['town'])) {
                    $province = $provinceClass->findById($postal_code_province['PostcodeProvince']['province_id']);
                    $country = $countryClass->findById($province['Province']['country_id']);

                    $dataLatitudeLongitude = self::getLatitudeLongitude($country, $province, $garage_tmp['Garage']['town'], $postcode);
                    $cityArray = array("City" => array(
                        'name' => $garage_tmp['Garage']['town'],
                        'latitude' => $dataLatitudeLongitude['latitude'],
                        'longitude' => $dataLatitudeLongitude['longitude'],
                        'province_id' => $postal_code_province['PostcodeProvince']['province_id'],
                    ));
                    $city = $cityClass->add_city($cityArray);
                }
                if (isset($city)) {
                    $garage_tmp['Garage']['city_id'] = $city['City']['id'];
                }
            }
        }

        $this->validator()->remove('address1');
        $this->create();

        $garage_create = $this->save($garage_tmp);
        if (!$garage_create) {
            CakeLog::write('updates', 'The garage could not be created.' . PHP_EOL);
            $errors['The garage could not be created'] = translateDataErrors($this->validationErrors);
            CakeLog::write('updates', print_r($this->validationErrors, true) . PHP_EOL);
        } else {
            $this->generateGuid($garage_create);
        }

        $this->commit();
        return $garage_create;
    }

    /**
     * The Garage comes from a Json left on the UK server.
     */
    public function updateGarage($garage_json, $garage_exist, &$errors = array())
    {
        $clear_characters = array(" ", ".");
        $clear_characters_address = array("#");
        $fields = array(
            'Garage' => array(
                'id',
                'business_name',
                'name',
                'ref_code',
                'address1',
                'address2',
                'address3',
                'address4',
                'latitude',
                'longitude',
                'town',
                'province_id',
                'postcode',
                'phone',
                'mobile',
                'email',
                'web',
                'status',
                'monday_open_1',
                'monday_closed_1',
                'monday_open_2',
                'monday_closed_2',
                'tuesday_open_1',
                'tuesday_closed_1',
                'tuesday_open_2',
                'tuesday_closed_2',
                'wednesday_open_1',
                'wednesday_closed_1',
                'wednesday_open_2',
                'wednesday_closed_2',
                'thursday_open_1',
                'thursday_closed_1',
                'thursday_open_2',
                'thursday_closed_2',
                'friday_open_1',
                'friday_closed_1',
                'friday_open_2',
                'friday_closed_2',
                'saturday_open_1',
                'saturday_closed_1',
                'saturday_open_2',
                'saturday_closed_2',
                'sunday_open_1',
                'sunday_closed_1',
                'sunday_open_2',
                'sunday_closed_2',
                'ramps',
                'MOT_bays',
                'spend_this_month',
                'spend_last_month',
                'spend_12_month',
                'spend_projected',
                'foundation_year',
                'interests',
                'modification_date',
                'aag_region_id',
            )
        );

        $garage_json['Garage']['Tel'] = str_replace(' ', '', $garage_json['Garage']['Tel']);

        $garage_tmp = array(
            'Garage' => array(
                'id' => $garage_exist['Garage']['id'],
                'business_name' => ($garage_json['Garage']['BusinessName']) ? $garage_json['Garage']['BusinessName'] : null,
                'name' => ($garage_json['Garage']['BusinessName']) ? $garage_json['Garage']['BusinessName'] : null,
                'ref_code' => ($garage_json['Garage']['GarageRef']) ? $garage_json['Garage']['GarageRef'] : null,
                'status' => ($garage_json['Garage']['StatusCode'] == true) ? ConstantsGarageStatus::ACTIVE : ConstantsGarageStatus::ONSTOP,
                'address1' => ($garage_json['Garage']['Addr1']) ? str_replace($clear_characters_address, '', $garage_json['Garage']['Addr1']) : null,
                'address2' => ($garage_json['Garage']['Addr2']) ? $garage_json['Garage']['Addr2'] : null,
                'address3' => ($garage_json['Garage']['Addr3']) ? $garage_json['Garage']['Addr3'] : null,
                'address4' => ($garage_json['Garage']['Addr4']) ? $garage_json['Garage']['Addr4'] : null,
                'town' => ($garage_json['Garage']['AddrTown']) ? trim(str_replace($clear_characters, '', $garage_json['Garage']['AddrTown'])) : null,
                'city_id' => null,
                'province_id' => null,
                'postcode' => ($garage_json['Garage']['AddrPCode']) ? $garage_json['Garage']['AddrPCode'] : null,
                'phone' => ($garage_json['Garage']['Tel']) && is_numeric($garage_json['Garage']['Tel']) ? trim(str_replace($clear_characters, '', $garage_json['Garage']['Tel'])) : null,
                'mobile' => ($garage_json['Garage']['Mobile']) && is_numeric($garage_json['Garage']['Mobile']) ? trim(str_replace($clear_characters, '', $garage_json['Garage']['Mobile'])) : null,
                // 'email' =>  $this->_validateEmail(trim($garage_json['Garage']['Email'])) ? trim($garage_json['Garage']['Email']) : null,
                'email' => !empty($garage_json['Garage']['Email']) ? trim($garage_json['Garage']['Email']) : null,
                'web' => ($garage_json['Garage']['Website']) ? $garage_json['Garage']['Website'] : null,
                'monday_open_1' => ($garage_json['Garage']['MonOpen'] != '-1') ? $garage_json['Garage']['MonOpen'] : null,
                'monday_closed_1' => ($garage_json['Garage']['MonClose'] != '-1') ? $garage_json['Garage']['MonClose'] : null,
                'monday_open_2' =>  null,
                'monday_closed_2' =>  null,
                'tuesday_open_1' => ($garage_json['Garage']['TueOpen'] != '-1') ? $garage_json['Garage']['TueOpen'] : null,
                'tuesday_closed_1' => ($garage_json['Garage']['TueClose'] != '-1') ? $garage_json['Garage']['TueClose'] : null,
                'tuesday_open_2' =>  null,
                'tuesday_closed_2' =>  null,
                'wednesday_open_1' => ($garage_json['Garage']['WedOpen'] != '-1') ? $garage_json['Garage']['WedOpen'] : null,
                'wednesday_closed_1' => ($garage_json['Garage']['WedClose'] != '-1') ? $garage_json['Garage']['WedClose'] : null,
                'wednesday_open_2' =>  null,
                'wednesday_closed_2' =>  null,
                'thursday_open_1' => ($garage_json['Garage']['ThruOpen'] != '-1') ? $garage_json['Garage']['ThruOpen'] : null,
                'thursday_closed_1' => ($garage_json['Garage']['ThruClose'] != '-1') ? $garage_json['Garage']['ThruClose'] : null,
                'thursday_open_2' =>  null,
                'thursday_closed_2' =>  null,
                'friday_open_1' => ($garage_json['Garage']['FriOpen'] != '-1') ? $garage_json['Garage']['FriOpen'] : null,
                'friday_closed_1' => ($garage_json['Garage']['FriClose'] != '-1') ? $garage_json['Garage']['FriClose'] : null,
                'friday_open_2' =>  null,
                'friday_closed_2' =>  null,
                'saturday_open_1' => ($garage_json['Garage']['SatOpen'] != '-1') ? $garage_json['Garage']['SatOpen'] : null,
                'saturday_closed_1' => ($garage_json['Garage']['SatClose'] != '-1') ? $garage_json['Garage']['SatClose'] : null,
                'saturday_open_2' =>  null,
                'saturday_closed_2' =>  null,
                'sunday_open_1' => ($garage_json['Garage']['SunOpen'] != '-1') ? $garage_json['Garage']['SunOpen'] : null,
                'sunday_closed_1' => ($garage_json['Garage']['SunClose'] != '-1') ? $garage_json['Garage']['SunClose'] : null,
                'sunday_open_2' =>  null,
                'sunday_closed_2' =>  null,
                'ramps' => ($garage_json['Garage']['Ramps']) ? $garage_json['Garage']['Ramps'] : null,
                'MOT_bays' => ($garage_json['Garage']['MOTBays']) ? $garage_json['Garage']['MOTBays'] : null,
                'spend_this_month' => ($garage_json['Garage']['SpendThisMonth']) ? $garage_json['Garage']['SpendThisMonth'] : null,
                'spend_last_month' => ($garage_json['Garage']['SpendLastMonth']) ? $garage_json['Garage']['SpendLastMonth'] : null,
                'spend_12_month' => ($garage_json['Garage']['Spend12Months']) ? $garage_json['Garage']['Spend12Months'] : null,
                'spend_projected' => ($garage_json['Garage']['ProjectedSpend']) ? $garage_json['Garage']['ProjectedSpend'] : null,
                'foundation_year' => ($garage_json['Garage']['GarageYearEstablished']) ? $garage_json['Garage']['GarageYearEstablished'] : null,
                'interests' => ($garage_json['Garage']['Interests']) ? $garage_json['Garage']['Interests'] : null,
                'modification_date' =>  date('Y-m-d H:i:s'),
                'aag_region_id' => Configure::read('AAG_REGION_ID_UK_IRELAND')
            )
        );

        if ($garage_json['Garage']['AddrPCode'] != $garage_exist['Garage']['postcode']) {
            if (isset($garage_json['Garage']['AddrPCode'])) {
                $prepAddr = (isset($garage_json['Garage']['AddrTown'])) ? $garage_json['Garage']['AddrPCode'] . ' ' . $garage_json['Garage']['AddrPCode'] : $garage_json['Garage']['AddrTown'];
                $prepAddr = (isset($garage_json['Garage']['Addr1'])) ? $prepAddr . ' ' . $garage_json['Garage']['Addr1'] : $prepAddr;
                $prepAddr = str_replace(' ', '+', $prepAddr);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&key=' . Texto::encryptDecryptText(GOOGLE_API_KEY_REVIEWS_GV, false) . '&sensor=false&v=3');

                $output = json_decode($geocode);
                if (isset($output->results[0])) {
                    $latitude = $output->results[0]->geometry->location->lat;
                    $longitude = $output->results[0]->geometry->location->lng;
                    $garage_tmp['Garage']['latitude'] = $latitude;
                    $garage_tmp['Garage']['longitude'] = $longitude;
                } else {
                    CakeLog::write('updates', 'The garage could not be geolocated.' . PHP_EOL);
                    array_push($errors, 'The garage could not be geolocated');
                }
            }
        }

        //POSTAL CODE
        $post_code_province = ClassRegistry::init('PostcodeProvince');
        $cityClass = ClassRegistry::init('City');
        $provinceClass = ClassRegistry::init('Province');
        $countryClass = ClassRegistry::init('Country');

        if ($garage_tmp['Garage']['postcode'] != null) {
            $postcode = $garage_tmp['Garage']['postcode'];
            $out_code = explode(" ", $postcode);
            $postcode = $out_code[0];

            $region = Configure::read('AAG_REGION_ID_UK_IRELAND');
            $postal_code_province = $post_code_province->findPostcodeProvinceByPostcodeInRegion($postcode, $region);
            if (!empty($postal_code_province)) {
                $garage_tmp['Garage']['province_id'] = $postal_code_province['PostcodeProvince']['province_id'];
                $city = isset($garage_tmp['Garage']['town']) ? $cityClass->findCityByNameInRegion($garage_tmp['Garage']['town'], $region) : '';
                if (empty($city) && isset($garage_tmp['Garage']['town'])) {
                    $province = $provinceClass->findById($postal_code_province['PostcodeProvince']['province_id']);
                    $country = $countryClass->findById($province['Province']['country_id']);

                    $dataLatitudeLongitude = self::getLatitudeLongitude($country, $province, $garage_tmp['Garage']['town'], $postcode);
                    $cityArray = array("City" => array(
                        'name' => $garage_tmp['Garage']['town'],
                        'latitude' => $dataLatitudeLongitude['latitude'],
                        'longitude' => $dataLatitudeLongitude['longitude'],
                        'province_id' => $postal_code_province['PostcodeProvince']['province_id'],
                    ));
                    $city = $cityClass->add_city($cityArray);
                }
                if (isset($city)) {
                    $garage_tmp['Garage']['city_id'] = $city['City']['id'];
                }
            }
        }

        $this->validator()->remove('phone');
        $this->validator()->remove('mobile');
        $this->validator()->remove('fax');
        $this->validator()->remove('service_24h_phone');

        $this->validator()->remove('address1');

        $update_garage_bd = $this->save($garage_tmp, $fields);
        if (!$update_garage_bd) {
            CakeLog::write('updates', 'The garage could not be updated.' . PHP_EOL);
            $errors['The garage could not be updated'] = translateDataErrors($this->validationErrors);
        }

        $this->commit();
        return $update_garage_bd;
    }

    public static function getLatitudeLongitude($country, $province, $cityName, $postcode, $address1 = null)
    {
        $prepAddr = isset($country['Country']['name']) ? $country['Country']['name'] : '';
        $prepAddr = isset($province['Province']['name']) ? $prepAddr . ' ' . $province['Province']['name'] : $prepAddr;
        $prepAddr = $prepAddr . ' ' . $cityName . ' ' . ($address1 ?? '') . ' ' . $postcode;
        $prepAddr = str_replace(' ', '+', $prepAddr);
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&key=' . Texto::encryptDecryptText(GOOGLE_API_KEY_REVIEWS_GV, false) . '&sensor=false&v=3');

        $output = json_decode($geocode);
        if (isset($output->results[0])) {
            $latitude = $output->results[0]->geometry->location->lat;
            $longitude = $output->results[0]->geometry->location->lng;
        }

        return array(
            'latitude' => $latitude ?? '',
            'longitude' => $longitude ?? ''
        );
    }

    public static function getLatitudeLongitudeData($country_name, $province_name, $city_name, $postcode, $address1 = null)
    {
        $data = array();
        if (!empty($country_name)) {
            $data[] = $country_name;
        }
        if (!empty($province_name)) {
            $data[] = $province_name;
        }
        if (!empty($city_name)) {
            $data[] = $city_name;
        }
        if (!empty($postcode)) {
            $data[] = $postcode;
        }
        if (!empty($address1)) {
            $data[] = $address1;
        }
        $dataStr = str_replace(' ', '+', implode('+', $data));
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $dataStr . '&key=' . Texto::encryptDecryptText(GOOGLE_API_KEY_REVIEWS_GV, false) . '&sensor=false&v=3');

        $output = json_decode($geocode);
        if (isset($output->results[0])) {
            $latitude = $output->results[0]->geometry->location->lat;
            $longitude = $output->results[0]->geometry->location->lng;
        }

        return array(
            'latitude' => $latitude ?? '',
            'longitude' => $longitude ?? ''
        );
    }

    /**
     * The Garage comes from a Json left on the FRANCE server.
     */
    public function createRep($garage)
    {
        $clear_characters = array(" ", ".");
        $garage_tmp = array();
        $garage_tmp = array(
            'Garage' => array(
                'id' => ($garage['id_isa']) ? $garage['id_isa'] : null,
                'business_name' => isset($garage['identite']['raison_sociale']) ? $garage['identite']['raison_sociale'] : '--',
                'name' => isset($garage['identite']['raison_sociale']) ? $garage['identite']['raison_sociale'] : '--',
                'siret' => isset($garage['identite']['siret']) ? $garage['identite']['siret'] : null,
                'VAT_code' => isset($garage['identite']['TVA']) ? $garage['identite']['TVA'] : null,
                'client_type' => 'A',
                'g_number_id' => ($garage['id_isa']) ? $garage['id_isa'] : null,
                'ref_code' => isset($garage['code']) && $garage['code'] != '' ? $garage['code'] : null,
                'status' =>  $garage['actif'] == true ? ConstantsGarageStatus::ACTIVE : ConstantsGarageStatus::ONSTOP,
                'address1' =>  isset($garage['identite']['adresse1']) && $garage['identite']['adresse1'] ? $garage['identite']['adresse1'] : ((isset($garage['identite']['adresse2']) && ($garage['identite']['adresse2']) ? $garage['identite']['adresse2'] : null)),
                'address2' =>  isset($garage['identite']['adresse2']) && $garage['identite']['adresse2'] ? $garage['identite']['adresse2'] : null,
                'address3' => null,
                'address4' => null,
                'latitude' => (isset($garage['geolocalisation']['latitude'])) ? $garage['geolocalisation']['latitude'] : null,
                'longitude' => (isset($garage['geolocalisation']['longitude'])) ? $garage['geolocalisation']['longitude'] : null,
                'town' =>  isset($garage['identite']['ville']) ? $garage['identite']['ville'] : null,
                'province_id' =>  null,
                'postcode' =>  isset($garage['identite']['code_postal']) ? $garage['identite']['code_postal'] : null,
                'phone' =>  isset($garage['identite']['telephone']) && is_numeric(trim(str_replace($clear_characters, '', $garage['identite']['telephone']))) ? trim(str_replace($clear_characters, '', $garage['identite']['telephone'])) : null,
                'mobile' =>  isset($garage['identite']['telephone']) && is_numeric(trim(str_replace($clear_characters, '', $garage['identite']['telephone']))) ? trim(str_replace($clear_characters, '', $garage['identite']['telephone'])) : null,
                'phone_international' =>  isset($garage['identite']['telephone']) && is_numeric(trim(str_replace($clear_characters, '', $garage['identite']['telephone']))) ? trim(str_replace($clear_characters, '', $garage['identite']['telephone'])) : null,
                'fax' =>  isset($garage['identite']['fax'])  && is_numeric($garage['identite']['fax']) ? str_replace($clear_characters, '', $garage['identite']['fax']) : null,
                'email' =>  isset($garage['identite']['email'])  && $this->is_valid_email(trim($garage['identite']['email'])) ? trim($garage['identite']['email']) : null,
                'web' =>  isset($garage['identite']['url']) ? $garage['identite']['url'] : null,
                'service_24h_phone' =>  null,
                'monday_open_1' =>  isset($garage['horaire']) && ($garage['horaire']['lundi']['matin']['ouverture'] != '00:00') ? $garage['horaire']['lundi']['matin']['ouverture'] : null,
                'monday_closed_1' => isset($garage['horaire']) && ($garage['horaire']['lundi']['matin']['fermeture'] != '00:00') ? $garage['horaire']['lundi']['matin']['fermeture'] : null,
                'monday_open_2' =>  isset($garage['horaire']) && ($garage['horaire']['lundi']['apres_midi']['ouverture'] != '00:00') ? $garage['horaire']['lundi']['apres_midi']['ouverture'] : null,
                'monday_closed_2' =>  isset($garage['horaire']) && ($garage['horaire']['lundi']['apres_midi']['fermeture'] != '00:00') ? $garage['horaire']['lundi']['apres_midi']['fermeture'] : null,
                'tuesday_open_1' =>  isset($garage['horaire']) && ($garage['horaire']['mardi']['matin']['ouverture'] != '00:00') ? $garage['horaire']['mardi']['matin']['ouverture'] : null,
                'tuesday_closed_1' =>  isset($garage['horaire']) && ($garage['horaire']['mardi']['matin']['fermeture'] != '00:00') ? $garage['horaire']['mardi']['matin']['fermeture'] : null,
                'tuesday_open_2' =>  isset($garage['horaire']) && ($garage['horaire']['mardi']['apres_midi']['ouverture'] != '00:00') ? $garage['horaire']['mardi']['apres_midi']['ouverture'] : null,
                'tuesday_closed_2' =>  isset($garage['horaire']) && ($garage['horaire']['mardi']['apres_midi']['fermeture'] != '00:00') ? $garage['horaire']['mardi']['apres_midi']['fermeture'] : null,
                'wednesday_open_1' =>  isset($garage['horaire']) && ($garage['horaire']['mercredi']['matin']['ouverture'] != '00:00') ? $garage['horaire']['mercredi']['matin']['ouverture'] : null,
                'wednesday_closed_1' => isset($garage['horaire']) && ($garage['horaire']['mercredi']['matin']['fermeture'] != '00:00') ? $garage['horaire']['mercredi']['matin']['fermeture'] : null,
                'wednesday_open_2' =>  isset($garage['horaire']) && ($garage['horaire']['mercredi']['apres_midi']['ouverture'] != '00:00') ? $garage['horaire']['mercredi']['apres_midi']['ouverture'] : null,
                'wednesday_closed_2' =>  isset($garage['horaire']) && ($garage['horaire']['mercredi']['apres_midi']['fermeture'] != '00:00') ? $garage['horaire']['mercredi']['apres_midi']['fermeture'] : null,
                'thursday_open_1' => isset($garage['horaire']) && ($garage['horaire']['jeudi']['matin']['ouverture'] != '00:00') ? $garage['horaire']['jeudi']['matin']['ouverture'] : null,
                'thursday_closed_1' =>  isset($garage['horaire']) && ($garage['horaire']['jeudi']['matin']['fermeture'] != '00:00') ? $garage['horaire']['jeudi']['matin']['fermeture'] : null,
                'thursday_open_2' =>  isset($garage['horaire']) && ($garage['horaire']['jeudi']['apres_midi']['ouverture'] != '00:00') ? $garage['horaire']['jeudi']['apres_midi']['ouverture'] : null,
                'thursday_closed_2' =>  isset($garage['horaire']) && ($garage['horaire']['jeudi']['apres_midi']['fermeture'] != '00:00') ? $garage['horaire']['jeudi']['apres_midi']['fermeture'] : null,
                'friday_open_1' =>  isset($garage['horaire']) && ($garage['horaire']['vendredi']['matin']['ouverture'] != '00:00') ? $garage['horaire']['vendredi']['matin']['ouverture'] : null,
                'friday_closed_1' =>  isset($garage['horaire']) && ($garage['horaire']['vendredi']['matin']['fermeture'] != '00:00') ? $garage['horaire']['vendredi']['matin']['fermeture'] : null,
                'friday_open_2' =>  isset($garage['horaire']) && ($garage['horaire']['vendredi']['apres_midi']['ouverture'] != '00:00') ? $garage['horaire']['vendredi']['apres_midi']['ouverture'] : null,
                'friday_closed_2' =>  isset($garage['horaire']) && ($garage['horaire']['vendredi']['apres_midi']['fermeture'] != '00:00') ? $garage['horaire']['vendredi']['apres_midi']['fermeture'] : null,
                'saturday_open_1' => isset($garage['horaire']) && ($garage['horaire']['samedi']['matin']['ouverture'] != '00:00') ? $garage['horaire']['samedi']['matin']['ouverture'] : null,
                'saturday_closed_1' =>  isset($garage['horaire']) && ($garage['horaire']['samedi']['matin']['fermeture'] != '00:00') ? $garage['horaire']['samedi']['matin']['fermeture'] : null,
                'saturday_open_2' =>  isset($garage['horaire']) && ($garage['horaire']['samedi']['apres_midi']['ouverture'] != '00:00') ? $garage['horaire']['samedi']['apres_midi']['ouverture'] : null,
                'saturday_closed_2' =>  isset($garage['horaire']) && ($garage['horaire']['samedi']['apres_midi']['fermeture'] != '00:00') ? $garage['horaire']['samedi']['apres_midi']['fermeture'] : null,
                'sunday_open_1' =>  isset($garage['horaire']) && ($garage['horaire']['dimanche']['matin']['ouverture'] != '00:00') ? $garage['horaire']['dimanche']['matin']['ouverture'] : null,
                'sunday_closed_1' =>  isset($garage['horaire']) && ($garage['horaire']['dimanche']['matin']['fermeture'] != '00:00') ? $garage['horaire']['dimanche']['matin']['fermeture'] : null,
                'sunday_open_2' =>  isset($garage['horaire']) && ($garage['horaire']['dimanche']['apres_midi']['ouverture'] != '00:00') ? $garage['horaire']['dimanche']['apres_midi']['ouverture'] : null,
                'sunday_closed_2' =>  isset($garage['horaire']) && ($garage['horaire']['dimanche']['apres_midi']['fermeture'] != '00:00') ? $garage['horaire']['dimanche']['apres_midi']['fermeture'] : null,
                'ramps' =>  null,
                'MOT_bays' =>  null,
                'technician' => null,
                'spend_this_month' =>  null,
                'spend_last_month' => null,
                'spend_12_month' => null,
                'spend_projected' => null,
                'foundation_year' => null,
                'lead_source' =>  null,
                'marketing_email' =>  null,
                'interests' =>  null,
                'creation_date' =>  date('Y-m-d H:i:s'),
                'modification_date' =>  date('Y-m-d H:i:s'),
                'user_id' =>  4,
            )
        );

        //POSTAL CODE
        $post_code_province = ClassRegistry::init('PostcodeProvince');

        $postcode = $garage_tmp['Garage']['postcode'];
        if ($garage_tmp['Garage']['postcode'] != null) {
            $out_code = explode(" ", $postcode);
            $postcode = $out_code[0];

            $postal_code_province = $post_code_province->findByPostcode($postcode);
            if (!empty($postal_code_province)) {
                $garage_tmp['Garage']['province_id'] = $postal_code_province['PostcodeProvince']['province_id'];
            }
        }

        $garage_json = ClassRegistry::init('Garage');
        $garage_json->validator()->remove('address1');
        $garage_json->create();
        $garage_bd = $garage_json->save($garage_tmp);

        if (!$garage_bd) {
            CakeLog::write('updates-france', 'The garage with ID ISA : ' . $garage['id_isa'] . ' could not be created.' . PHP_EOL);
        }
        $this->commit();

        return $garage_bd;
    }

    /**
     * The Garage comes from a Json left on the FRANCE server.
     */
    public function updateRep($garage_json, $garage_exist)
    {
        $clear_characters = array(" ", ".");

        $fields = array(
            'Garage' => array(
                'id',
                'business_name',
                'name',
                'siret',
                'VAT_code',
                'client_type',
                'g_number_id',
                'ref_code',
                'status',
                'address1',
                'address2',
                'address3',
                'address4',
                'latitude',
                'longitude',
                'town',
                'province_id',
                'postcode',
                'phone',
                'mobile',
                'phone_international',
                'fax',
                'email',
                'web',
                'monday_open_1',
                'monday_closed_1',
                'monday_open_2',
                'monday_closed_2',
                'tuesday_open_1',
                'tuesday_closed_1',
                'tuesday_open_2',
                'tuesday_closed_2',
                'wednesday_open_1',
                'wednesday_closed_1',
                'wednesday_open_2',
                'wednesday_closed_2',
                'thursday_open_1',
                'thursday_closed_1',
                'thursday_open_2',
                'thursday_closed_2',
                'friday_open_1',
                'friday_closed_1',
                'friday_open_2',
                'friday_closed_2',
                'saturday_open_1',
                'saturday_closed_1',
                'saturday_open_2',
                'saturday_closed_2',
                'sunday_open_1',
                'sunday_closed_1',
                'sunday_open_2',
                'sunday_closed_2',
                'ramps',
                'MOT_bays',
                'technician',
                'foundation_year',
                'lead_source',
                'marketing_email',
                'interests',
                'modification_date',
                'user_id',
            )
        );

        $garage_tmp = array(
            'Garage' => array(
                'id' => $garage_exist['Garage']['id'],
                'business_name' => isset($garage_json['identite']) && isset($garage_json['identite']['raison_sociale']) ? $garage_json['identite']['raison_sociale'] : $garage_exist['Garage']['business_name'],
                'name' => isset($garage_json['identite']) && isset($garage_json['identite']['raison_sociale']) ? $garage_json['identite']['raison_sociale'] : $garage_exist['Garage']['name'],
                'siret' => isset($garage_json['identite']) && isset($garage_json['identite']['siret']) ? $garage_json['identite']['siret'] : $garage_exist['Garage']['siret'],
                'VAT_code' => isset($garage_json['identite']) && isset($garage_json['identite']['TVA']) ? $garage_json['identite']['TVA'] : $garage_exist['Garage']['VAT_code'],
                'client_type' => 'A',
                'g_number_id' => isset($garage_json['id_isa']) ? $garage_json['id_isa'] : $garage_exist['Garage']['g_number_id'],
                'ref_code' => isset($garage_json['code']) && $garage_json['code'] != '' ? $garage_json['code'] : $garage_exist['Garage']['ref_code'],
                'status' =>  isset($garage_json['actif']) && ($garage_json['actif'] == true ? ConstantsGarageStatus::ACTIVE : $garage_json['actif'] == false) ? ConstantsGarageStatus::ONSTOP : $garage_exist['Garage']['status'],
                'address1' =>  isset($garage_json['identite']) && isset($garage_json['identite']['adresse1']) ? $garage_json['identite']['adresse1'] : $garage_exist['Garage']['address1'],
                'address2' =>  isset($garage_json['identite']) && isset($garage_json['identite']['adresse2']) ? $garage_json['identite']['adresse2'] : $garage_exist['Garage']['address2'],
                'address3' => null,
                'address4' => null,
                'latitude' =>  isset($garage_json['geolocalisation']) && isset($garage_json['geolocalisation']['latitude']) ? $garage_json['geolocalisation']['latitude'] : $garage_exist['Garage']['latitude'],
                'longitude' => isset($garage_json['geolocalisation']) && isset($garage_json['geolocalisation']['longitude']) ? $garage_json['geolocalisation']['longitude'] : $garage_exist['Garage']['longitude'],
                'town' =>  isset($garage_json['identite']) && isset($garage_json['identite']['ville']) ? $garage_json['identite']['ville'] : $garage_exist['Garage']['town'],
                'province_id' =>  null,
                'postcode' =>  isset($garage_json['identite']) && isset($garage_json['identite']['code_postal']) ? $garage_json['identite']['code_postal'] : $garage_exist['Garage']['postcode'],
                'phone' =>  isset($garage_json['identite']) && isset($garage_json['identite']['telephone']) && is_numeric(trim(str_replace($clear_characters, '', $garage_json['identite']['telephone']))) ? trim(str_replace($clear_characters, '', $garage_json['identite']['telephone'])) : $garage_exist['Garage']['phone'],
                'mobile' =>  isset($garage_json['identite']) && isset($garage_json['identite']['telephone']) && is_numeric(trim(str_replace($clear_characters, '', $garage_json['identite']['telephone']))) ? trim(str_replace($clear_characters, '', $garage_json['identite']['telephone'])) : $garage_exist['Garage']['mobile'],
                'phone_international' =>  isset($garage_json['identite']) && isset($garage_json['identite']['telephone']) && is_numeric(trim(str_replace($clear_characters, '', $garage_json['identite']['telephone']))) ? trim(str_replace($clear_characters, '', $garage_json['identite']['telephone'])) : $garage_exist['Garage']['phone_international'],
                'fax' =>  isset($garage_json['identite']) && isset($garage_json['identite']['fax']) && is_numeric($garage_json['identite']['fax']) ? str_replace($clear_characters, '', $garage_json['identite']['fax']) : $garage_exist['Garage']['fax'],
                'email' =>  isset($garage_json['identite']) && isset($garage_json['identite']['email']) && $this->is_valid_email(trim($garage_json['identite']['email'])) ? trim($garage_json['identite']['email']) : $garage_exist['Garage']['email'],
                'web' =>  isset($garage_json['identite']) && isset($garage_json['identite']['url']) ? $garage_json['identite']['url'] : $garage_exist['Garage']['web'],
                'service_24h_phone' =>  null,
                'monday_open_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['lundi']) && isset($garage_json['horaire']['lundi']['matin']) && isset($garage_json['horaire']['lundi']['matin']['ouverture']) && isset($garage_json['horaire']['lundi']['matin']['ouverture'])) ? $garage_json['horaire']['lundi']['matin']['ouverture'] : $garage_exist['Garage']['monday_open_1'],
                'monday_closed_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['lundi']) && isset($garage_json['horaire']['lundi']['matin']) && isset($garage_json['horaire']['lundi']['matin']['fermeture']) && isset($garage_json['horaire']['lundi']['matin']['fermeture'])) ? $garage_json['horaire']['lundi']['matin']['fermeture'] : $garage_exist['Garage']['monday_closed_1'],
                'monday_open_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['lundi']) && isset($garage_json['horaire']['lundi']['apres_midi']) && isset($garage_json['horaire']['lundi']['apres_midi']['ouverture']) && isset($garage_json['horaire']['lundi']['apres_midi']['ouverture'])) ? $garage_json['horaire']['lundi']['apres_midi']['ouverture'] : $garage_exist['Garage']['monday_open_2'],
                'monday_closed_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['lundi']) && isset($garage_json['horaire']['lundi']['apres_midi']) && isset($garage_json['horaire']['lundi']['apres_midi']['fermeture']) && isset($garage_json['horaire']['lundi']['apres_midi']['fermeture'])) ? $garage_json['horaire']['lundi']['apres_midi']['fermeture'] : $garage_exist['Garage']['monday_closed_2'],
                'tuesday_open_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['mardi']) && isset($garage_json['horaire']['mardi']['matin']) && isset($garage_json['horaire']['mardi']['matin']['ouverture']) && isset($garage_json['horaire']['mardi']['matin']['ouverture'])) ? $garage_json['horaire']['mardi']['matin']['ouverture'] : $garage_exist['Garage']['tuesday_open_1'],
                'tuesday_closed_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['mardi']) && isset($garage_json['horaire']['mardi']['matin']) && isset($garage_json['horaire']['mardi']['matin']['fermeture']) && isset($garage_json['horaire']['mardi']['matin']['fermeture'])) ? $garage_json['horaire']['mardi']['matin']['fermeture'] : $garage_exist['Garage']['tuesday_closed_1'],
                'tuesday_open_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['mardi']) && isset($garage_json['horaire']['mardi']['apres_midi']) && isset($garage_json['horaire']['mardi']['apres_midi']['ouverture']) && isset($garage_json['horaire']['mardi']['apres_midi']['ouverture'])) ? $garage_json['horaire']['mardi']['apres_midi']['ouverture'] : $garage_exist['Garage']['tuesday_open_2'],
                'tuesday_closed_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['mardi']) && isset($garage_json['horaire']['mardi']['apres_midi']) && isset($garage_json['horaire']['mardi']['apres_midi']['fermeture']) && isset($garage_json['horaire']['mardi']['apres_midi']['fermeture'])) ? $garage_json['horaire']['mardi']['apres_midi']['fermeture'] : $garage_exist['Garage']['tuesday_closed_2'],
                'wednesday_open_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['mercredi']) && isset($garage_json['horaire']['mercredi']['matin']) && isset($garage_json['horaire']['mercredi']['matin']['ouverture']) && isset($garage_json['horaire']['mercredi']['matin']['ouverture'])) ? $garage_json['horaire']['mercredi']['matin']['ouverture'] : $garage_exist['Garage']['wednesday_open_1'],
                'wednesday_closed_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['mercredi']) && isset($garage_json['horaire']['mercredi']['matin']) && isset($garage_json['horaire']['mercredi']['matin']['fermeture']) && isset($garage_json['horaire']['mercredi']['matin']['fermeture'])) ? $garage_json['horaire']['mercredi']['matin']['fermeture'] : $garage_exist['Garage']['wednesday_closed_1'],
                'wednesday_open_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['mercredi']) && isset($garage_json['horaire']['mercredi']['apres_midi']) && isset($garage_json['horaire']['mercredi']['apres_midi']['ouverture']) && isset($garage_json['horaire']['mercredi']['apres_midi']['ouverture'])) ? $garage_json['horaire']['mercredi']['apres_midi']['ouverture'] : $garage_exist['Garage']['wednesday_open_2'],
                'wednesday_closed_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['mercredi']) && isset($garage_json['horaire']['mercredi']['apres_midi']) && isset($garage_json['horaire']['mercredi']['apres_midi']['fermeture']) && isset($garage_json['horaire']['mercredi']['apres_midi']['fermeture'])) ? $garage_json['horaire']['mercredi']['apres_midi']['fermeture'] : $garage_exist['Garage']['wednesday_closed_2'],
                'thursday_open_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['jeudi']) && isset($garage_json['horaire']['jeudi']['matin']) && isset($garage_json['horaire']['jeudi']['matin']['ouverture']) && isset($garage_json['horaire']['jeudi']['matin']['ouverture'])) ? $garage_json['horaire']['jeudi']['matin']['ouverture'] : $garage_exist['Garage']['thursday_open_1'],
                'thursday_closed_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['jeudi']) && isset($garage_json['horaire']['jeudi']['matin']) && isset($garage_json['horaire']['jeudi']['matin']['fermeture']) && isset($garage_json['horaire']['jeudi']['matin']['fermeture'])) ? $garage_json['horaire']['jeudi']['matin']['fermeture'] : $garage_exist['Garage']['thursday_closed_1'],
                'thursday_open_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['jeudi']) && isset($garage_json['horaire']['jeudi']['apres_midi']) && isset($garage_json['horaire']['jeudi']['apres_midi']['ouverture']) && isset($garage_json['horaire']['jeudi']['apres_midi']['ouverture'])) ? $garage_json['horaire']['jeudi']['apres_midi']['ouverture'] : $garage_exist['Garage']['thursday_open_2'],
                'thursday_closed_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['jeudi']) && isset($garage_json['horaire']['jeudi']['apres_midi']) && isset($garage_json['horaire']['jeudi']['apres_midi']['fermeture']) && isset($garage_json['horaire']['jeudi']['apres_midi']['fermeture'])) ? $garage_json['horaire']['jeudi']['apres_midi']['fermeture'] : $garage_exist['Garage']['thursday_closed_2'],
                'friday_open_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['vendredi']) && isset($garage_json['horaire']['vendredi']['matin']) && isset($garage_json['horaire']['vendredi']['matin']['ouverture']) && isset($garage_json['horaire']['vendredi']['matin']['ouverture'])) ? $garage_json['horaire']['vendredi']['matin']['ouverture'] : $garage_exist['Garage']['friday_open_1'],
                'friday_closed_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['vendredi']) && isset($garage_json['horaire']['vendredi']['matin']) && isset($garage_json['horaire']['vendredi']['matin']['fermeture']) && isset($garage_json['horaire']['vendredi']['matin']['fermeture'])) ? $garage_json['horaire']['vendredi']['matin']['fermeture'] : $garage_exist['Garage']['friday_closed_1'],
                'friday_open_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['vendredi']) && isset($garage_json['horaire']['vendredi']['apres_midi']) && isset($garage_json['horaire']['vendredi']['apres_midi']['ouverture']) && isset($garage_json['horaire']['vendredi']['apres_midi']['ouverture'])) ? $garage_json['horaire']['vendredi']['apres_midi']['ouverture'] : $garage_exist['Garage']['friday_open_2'],
                'friday_closed_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['vendredi']) && isset($garage_json['horaire']['vendredi']['apres_midi']) && isset($garage_json['horaire']['vendredi']['apres_midi']['fermeture']) && isset($garage_json['horaire']['vendredi']['apres_midi']['fermeture'])) ? $garage_json['horaire']['vendredi']['apres_midi']['fermeture'] : $garage_exist['Garage']['friday_closed_2'],
                'saturday_open_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['samedi']) && isset($garage_json['horaire']['samedi']['matin']) && isset($garage_json['horaire']['samedi']['matin']['ouverture']) && isset($garage_json['horaire']['samedi']['matin']['ouverture'])) ? $garage_json['horaire']['samedi']['matin']['ouverture'] : $garage_exist['Garage']['saturday_open_1'],
                'saturday_closed_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['samedi']) && isset($garage_json['horaire']['samedi']['matin']) && isset($garage_json['horaire']['samedi']['matin']['fermeture']) && isset($garage_json['horaire']['samedi']['matin']['fermeture'])) ? $garage_json['horaire']['samedi']['matin']['fermeture'] : $garage_exist['Garage']['saturday_closed_1'],
                'saturday_open_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['samedi']) && isset($garage_json['horaire']['samedi']['apres_midi']) && isset($garage_json['horaire']['samedi']['apres_midi']['ouverture']) && isset($garage_json['horaire']['samedi']['apres_midi']['ouverture'])) ? $garage_json['horaire']['samedi']['apres_midi']['ouverture'] : $garage_exist['Garage']['saturday_open_2'],
                'saturday_closed_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['samedi']) && isset($garage_json['horaire']['samedi']['apres_midi']) && isset($garage_json['horaire']['samedi']['apres_midi']['fermeture']) && isset($garage_json['horaire']['samedi']['apres_midi']['fermeture'])) ? $garage_json['horaire']['samedi']['apres_midi']['fermeture'] : $garage_exist['Garage']['saturday_closed_2'],
                'sunday_open_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['dimanche']) && isset($garage_json['horaire']['dimanche']['matin']) && isset($garage_json['horaire']['dimanche']['matin']['ouverture']) && isset($garage_json['horaire']['dimanche']['matin']['ouverture'])) ? $garage_json['horaire']['dimanche']['matin']['ouverture'] : $garage_exist['Garage']['sunday_open_1'],
                'sunday_closed_1' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['dimanche']) && isset($garage_json['horaire']['dimanche']['matin']) && isset($garage_json['horaire']['dimanche']['matin']['fermeture']) && isset($garage_json['horaire']['dimanche']['matin']['fermeture'])) ? $garage_json['horaire']['dimanche']['matin']['fermeture'] : $garage_exist['Garage']['sunday_closed_1'],
                'sunday_open_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['dimanche']) && isset($garage_json['horaire']['dimanche']['apres_midi']) && isset($garage_json['horaire']['dimanche']['apres_midi']['ouverture']) && isset($garage_json['horaire']['dimanche']['apres_midi']['ouverture'])) ? $garage_json['horaire']['dimanche']['apres_midi']['ouverture'] : $garage_exist['Garage']['sunday_open_2'],
                'sunday_closed_2' => (isset($garage_json['horaire']) && isset($garage_json['horaire']['dimanche']) && isset($garage_json['horaire']['dimanche']['apres_midi']) && isset($garage_json['horaire']['dimanche']['apres_midi']['fermeture']) && isset($garage_json['horaire']['dimanche']['apres_midi']['fermeture'])) ? $garage_json['horaire']['dimanche']['apres_midi']['fermeture'] : $garage_exist['Garage']['sunday_closed_2'],
                'ramps' =>  null,
                'MOT_bays' =>  null,
                'technician' => null,
                'foundation_year' => null,
                'lead_source' =>  null,
                'marketing_email' =>  null,
                'interests' =>  null,
                'modification_date' =>  date('Y-m-d H:i:s'),
                'user_id' =>  4,
            )
        );

        //POSTAL CODE
        $post_code_province = ClassRegistry::init('PostcodeProvince');

        $postcode = $garage_tmp['Garage']['postcode'];
        if ($garage_tmp['Garage']['postcode'] != null) {
            $out_code = explode(" ", $postcode);
            $postcode = $out_code[0];

            $postal_code_province = $post_code_province->findByPostcode($postcode);
            if (!empty($postal_code_province)) {
                $garage_tmp['Garage']['province_id'] = $postal_code_province['PostcodeProvince']['province_id'];
            }
        }

        $garage_json = ClassRegistry::init('Garage');
        $garage_json->validator()->remove('address1');
        $garage_json->create();
        $garage_bd = $garage_json->save($garage_tmp);

        $this->commit();

        return $garage_bd;
    }

    public function getGaragesByContacts($contacts, $aagRegionId)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'table' => 'garages_contacts_bdm',
                    'alias' => 'GarageContactBdm',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageContactBdm.garage_id = Garage.id'
                    )
                ),
            ),
            'conditions' => array(
                'GarageContactBdm.contact_id' => $contacts,
                'Garage.aag_region_id' => $aagRegionId
            ),
            'fields' => array(
                'Garage.id'
            )
        ));
    }

    public function getGaragesByCountry($country_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'table' => 'provinces',
                    'alias' => 'Province',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Province.id = Garage.province_id'
                    )
                )
            ),
            'conditions' => array(
                'Province.country_id' => $country_id
            ),
            'fields' => array(
                'Garage.id'
            )
        ));
    }

    public function getGaragesByNetwork($network_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'table' => 'garages_networks',
                    'alias' => 'GarageNetwork',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id'
                    )
                )
            ),
            'conditions' => array(
                'GarageNetwork.network_id' => $network_id
            ),
            'fields' => array(
                'Garage.id',
                'Garage.complete_name'
            )
        ));
    }

    public function getGaragesIdAndRecommendedByNetwork($network_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'table' => 'garages_networks',
                    'alias' => 'GarageNetwork',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id'
                    )
                )
            ),
            'conditions' => array(
                'GarageNetwork.network_id' => $network_id
            ),
            'fields' => array(
                'Garage.id',
                'Garage.recommended_network'
            )
        ));
    }

    public function is_valid_email($str)
    {
        $result = (false !== filter_var($str, FILTER_VALIDATE_EMAIL));

        if ($result) {
            list($user, $domain) = preg_split('/@/', $str);

            $result = checkdnsrr($domain, 'MX');
        }

        return $result;
    }

    /**
     * function to correctly set the length of the postal code. DE & FR
     *
     * @param $long_tmp length of the postal code
     * @param $postal_code postal code
     * @return string postal code with the correct length
     */
    private function _set_size_postcode($long_tmp, $postal_code)
    {
        $postcode = $postal_code;
        $diff = ConstantsLengthPostcode::POSTCODE_FR - $long_tmp;
        for ($i = 1; $i <= $diff; $i++) {
            $postcode = '0' . $postcode;
        }

        return $postcode;
    }

    public function getProvinceIdByPostcode($garage_postcode)
    {
        $postcode_province = ClassRegistry::init('PostcodeProvince');

        $postal_code_province = $postcode_province->findByPostcode($garage_postcode);
        $garage_province_id = '';
        if (!empty($postal_code_province)) {
            $garage_province_id = $postal_code_province['PostcodeProvince']['province_id'];
        }

        return $garage_province_id;
    }

    public function getCalendarEvents($garage_id)
    {
        if (CakeSession::read('Auth.User.current_network')) {
            $Network = ClassRegistry::init('Network');
            $network = $Network->findById(CakeSession::read('Auth.User.current_network'));
            $color = $network['Network']['primary_color'];
        } else {
            $color = '6a99ff';
        }
        $garage = $this->findById($garage_id);
        $garage_js = array();
        $days = array(
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        );
        foreach ($days as $key => $day) {
            if (!empty($garage['Garage'][$day . '_open_1']) && !empty($garage['Garage'][$day . '_closed_1'])) {
                $garage_js[] = array(
                    'id' => uniqid(),
                    'color' => $color,
                    'start' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) . ' ' . $garage['Garage'][$day . '_open_1'],
                    'end' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) . ' ' . $garage['Garage'][$day . '_closed_1'],
                    'open' => $garage['Garage'][$day . '_open_1'],
                    'closed' => $garage['Garage'][$day . '_closed_1'],
                    'day' => $key + 1
                );
            }
            if (!empty($garage['Garage'][$day . '_open_2']) && !empty($garage['Garage'][$day . '_closed_2'])) {
                $garage_js[] = array(
                    'id' => uniqid(),
                    'color' => $color,
                    'start' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) . ' ' . $garage['Garage'][$day . '_open_2'],
                    'end' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) . ' ' . $garage['Garage'][$day . '_closed_2'],
                    'open' => $garage['Garage'][$day . '_open_2'],
                    'closed' => $garage['Garage'][$day . '_closed_2'],
                    'day' => $key + 1
                );
            }
        }

        return $garage_js;
    }

    public function getByGarageName($name)
    {
        return $this->find('all', array(
            'conditions' => array(
                'complete_name LIKE' => '%' . $name . '%'
            ),
            'fields' => array(
                'id',
                'complete_name'
            )
        ));
    }

    public function getListByTaskId($task_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'TaskGarage',
                    'table' => 'tasks_garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Garage.id = TaskGarage.garage_id'
                    ),
                ),
            ),
            'conditions' => array(
                'task_id' => $task_id
            ),
            'fields' => array(
                'Garage.id',
                'Garage.complete_search'
            )
        ));
    }

    public function getCustomPagination($conditions)
    {
        return $this->find('all', array(
            // 'joins'=> array(
            //     array(
            //         'alias' => 'Appointment',
            //         'table' => 'appointments',
            //         'type' => 'LEFT',
            //         'conditions' => array(
            //             'Appointment.garage_id = Garage.id',
            //             'Appointment.date <= ' => date('Y-m-d'),
            //             'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
            //             'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT
            //         ),
            //     ),
            // ),
            'fields' => array(
                'Garage.id',
                'Garage.name',
                //'Garage.client_type',
                'Garage.town',
                'Garage.g_number_id',
                'Garage.address1',
                'Garage.last_visit',
                // 'Appointment.id',
                // 'Appointment.date',
                // 'max(Appointment.date) as max_appointment_date',
            ),
            // 'order' => array(
            //     $pagination_order => $pagination_direction,
            // ),
            // 'group' => array(
            //     'Garage.id',
            // ),
            'conditions' => $conditions,
            'order' => array(
                'Garage.last_visit desc'
            ),
            //'page' => $pagination_page,
            'limit' => ConstantsPagination::SIZE_PAGE_SMALL,
        ));
    }

    public function getPaginationCount($conditions)
    {
        if (isset($conditions['bdm_id'])) {
            $conditions['contact_id'] = $conditions['bdm_id'];
            unset($conditions['bdm_id']);
        }

        return $this->find('count', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Appointment.garage_id = Garage.id',
                        'Appointment.date <= ' => date('Y-m-d'),
                        'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
                        'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT
                    ),
                ),
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'GarageService',
                    'table' => 'garages_services',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageService.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'GarageContactBdm',
                    'table' => 'garages_contacts_bdm',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageContactBdm.garage_id = Garage.id',
                    ),
                ),
            ),
            'fields' => array(
                'Garage.id',
            ),
            'group' => array(
                'Garage.id',
            ),
            'conditions' => $conditions,
        ));
    }

    public function getGarageDistributorData($garage_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageDistributor',
                        'table' => 'garages_distributors',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageDistributor.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Distributor.id = GarageDistributor.distributor_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Garage.id' => $garage_id
                ),
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.account_number',
                    'GarageDistributor.principal',
                ),
            )
        );
    }

    public function getGarageNetworkData($garage_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Garage.id' => $garage_id
                ),
                'fields' => array(
                    'Network.id',
                    'Network.name',
                    'Network.image',
                    'GarageNetwork.contract_start_date',
                    'GarageNetwork.contract_end_date',
                    'GarageNetwork.status',
                ),
            )
        );
    }

    public function getGarageNetworksLastInfo($garage_id)
    {
        $networksLastByGarage = $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Garage.id' => $garage_id,
                    'GarageNetwork.last' => ConstantsBooleans::YES,
                ),
                'fields' => array(
                    'Network.id',
                    'Network.name',
                    'Network.image',
                    'GarageNetwork.status',
                ),
            )
        );

        $networksImagesGarage = array();
        $networks_statuses = Configure::read('Network_Status');
        foreach ($networksLastByGarage as $network) {
            $networksImagesGarage[] = array(
                'id' => $network['Network']['id'],
                'name' => $network['Network']['name'],
                'image' => $network['Network']['image'],
                'status' => $network['GarageNetwork']['status'],
                'statusText' => isset($network['GarageNetwork']['status']) && !empty($network['GarageNetwork']['status'])
                    ? __t($networks_statuses[$network['GarageNetwork']['status']]) : ''
            );
        }
        return $networksImagesGarage;
    }

    /**
     * Get Garage and GarageContactList.
     */
    public function getGarageContactList($garageId, $aagRegionId)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageContactList',
                        'table' => 'garages_contacts_lists',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageContactList.garage_id = Garage.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Garage.id' => $garageId,
                    'Garage.aag_region_id' => $aagRegionId
                ),
                'fields' => array(
                    'Garage.*',
                    'GarageContactList.*',
                ),
            )
        );
    }

    public function getGarageTasks($conditions, $joins)
    {
        return $this->find(
            'all',
            array(
                'joins' => $joins,
                'conditions' => $conditions,
                'fields' => array(
                    'Garage.id',
                )
            )
        );
    }

    public function findGaragesList()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'Garage.g_number_id',
                    'Garage.name',
                )
            )
        );
    }

    public function getAllNetworkIdAndRegionId($network_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageNetwork.network_id' => $network_id
                ),
                'fields' => array(
                    'Garage.id'
                )
            )
        );
    }

    public function getListByMessageId($message_id)
    {
        return $this->find('list', array(
            'fields' => array(
                'Garage.id',
                'Garage.complete_name'
            ),
            'joins' => array(
                array(
                    'alias' => 'MessageGarage',
                    'table' => 'messages_garages',
                    'type' => 'INNER',
                    'conditions' => 'MessageGarage.garage_id = Garage.id'
                ),
            ),
            'conditions' => array(
                'MessageGarage.message_id' => $message_id
            ),
        ));
    }

    private function _validateEmail($email)
    {
        $_emailPattern = '/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/ui';
        if ($_emailPattern === null) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return true;
            }
        } elseif (preg_match($_emailPattern, $email)) {
            return true;
        }
        return false;
    }

    public function _validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function getGarageWithOutNetwork()
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'Left',
                        'conditions' => array(
                            'Garage.id = GarageNetwork.garage_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageNetwork.id' => null
                ),
                'fields' => array(
                    'Garage.*',
                ),
                'order' => 'Garage.name DESC'
            )
        );
    }

    public function search_list()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function getGarageNameById($garageId, $aagRegionId)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'Garage.id' => $garageId,
                    'Garage.aag_region_id' => $aagRegionId
                ),
                'fields' => array(
                    'Garage.name',
                ),
                'group' => array(
                    'Garage.name'
                ),
            )
        );
    }

    public function obtenerPosiblesGaragesAjax($condiciones)
    {
        $garages = $this->obtenerPosiblesGaragesQuery($condiciones);
        $garages = Hash::combine($garages, '{n}.Garage.id', array('%s', '{n}.Garage.complete_search'));

        return $garages;
    }

    public function obtenerPosiblesGaragesQuery($condiciones_ajax = array())
    {

        if (isset($condiciones_ajax['name']) && !empty($condiciones_ajax['name'])) {
            $condiciones_ajax = array(
                'OR' => array(
                    'Garage.name LIKE' => '%' . $condiciones_ajax['name'] . '%',
                    'Garage.g_number_id LIKE' => '%' . $condiciones_ajax['name'] . '%'
                )
            );
        }

        return $this->find(
            'all',
            array(
                'conditions' => $condiciones_ajax,
                'fields' => array(
                    'Garage.id',
                    'Garage.complete_search'
                ),
                'group' => array(
                    'Garage.id'
                ),
            )
        );
    }

    public function obtenerPosiblesGaragesAjaxRegion($condiciones, $aag_region_id)
    {
        $garages = $this->obtenerPosiblesGaragesQueryRegion($condiciones, $aag_region_id);
        $garages = Hash::combine($garages, '{n}.Garage.id', array('%s', '{n}.Garage.complete_search'));

        return $garages;
    }

    public function obtenerPosiblesGaragesQueryRegion($condiciones_ajax = array(), $aag_region_id)
    {
        $conditions = array('Garage.aag_region_id' => $aag_region_id);

        if (isset($condiciones_ajax['name']) && !empty($condiciones_ajax['name'])) {
            $condiciones_ajax = array(
                'OR' => array(
                    'Garage.name LIKE' => '%' . $condiciones_ajax['name'] . '%',
                    'Garage.g_number_id LIKE' => '%' . $condiciones_ajax['name'] . '%'
                )
            );
        }

        return $this->find(
            'all',
            array(
                'conditions' => array(
                    $condiciones_ajax,
                    $conditions
                ),
                'fields' => array(
                    'Garage.id',
                    'Garage.complete_search'
                ),
                'group' => array(
                    'Garage.id'
                ),
            )
        );
    }

    public function getPossiblesGaragesStaffLiveAjax($conditions, $aag_region_id, $role_id)
    {
        $garages = $this->getPossiblesGaragesStaffLiveQuery($conditions, $aag_region_id, $role_id);
        $garages = Hash::combine($garages, '{n}.Garage.id', array('%s', '{n}.Garage.complete_search'));

        return $garages;
    }

    public function getPossiblesGaragesStaffLiveQuery($conditions_ajax = array(), $aag_region_id, $role_id)
    {
        $conditions_region = array();
        if ($role_id != ConstantsRoles::SUPER_ADMIN) {
            $conditions_region = array('Garage.aag_region_id' => $aag_region_id);
        }
        if (isset($conditions_ajax['name']) && !empty($conditions_ajax['name'])) {
            $conditions_ajax = array(
                'OR' => array(
                    'Garage.name LIKE' => '%' . $conditions_ajax['name'] . '%',
                    'Garage.g_number_id LIKE' => '%' . $conditions_ajax['name'] . '%'
                )
            );
        }

        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageContactStaff',
                        'table' => 'garages_contacts_staff',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageContactStaff.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageContactStaff.garage_id IS NOT NULL',
                    'GarageNetwork.garage_id IS NOT NULL',
                    $conditions_ajax,
                    $conditions_region,
                ),
                'fields' => array(
                    'Garage.id',
                    'Garage.complete_search'
                ),
                'group' => array(
                    'Garage.id'
                ),
            )
        );
    }

    public function getGaragesNameByIdGarage($garage_id)
    {
        $resultArray = array();
        $query = $this->find('first', array(
            'conditions' => array(
                'Garage.id' => $garage_id
            ),
            'fields' => array(
                'Garage.id',
                'Garage.name'
            ),
        ));

        if ($query) {
            $garage = $query['Garage'];
            $resultArray[$garage['id']] = $garage['name'];
        }
        return $resultArray;
    }

    public function getListByIdGarage($garage_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'Garage.id' => $garage_id
            ),
        ));
    }

    public function getCompleteListOnContactStaff()
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageContactStaff',
                        'table' => 'garages_contacts_staff',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageContactStaff.garage_id = Garage.id',
                        ),
                    ),
                ),
                array(
                    'fields' => array(
                        'Garage.complete_name'
                    ),
                    'order' => array(
                        'Garage.complete_name'
                    ),
                    'group' => array(
                        'Garage.id'
                    ),
                )
            )
        );
    }

    public function getCompleteListOnContactStaffAndNetwork()
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageContactStaff',
                        'table' => 'garages_contacts_staff',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageContactStaff.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                ),
                array(
                    'conditions' => array(
                        'AND' => array(
                            'GarageContactStaff.garage_id IS NOT NULL',
                            'GarageNetwork.garage_id IS NOT NULL',
                        ),
                    ),
                    'fields' => array(
                        'Garage.complete_name'
                    ),
                    'order' => array(
                        'Garage.complete_name'
                    ),
                    'group' => array(
                        'Garage.id'
                    ),
                )
            )
        );
    }

    public function getCompleteListOnContactStaffLive()
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageContactStaff',
                        'table' => 'garages_contacts_staff',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageContactStaff.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                            'Network.training =' => ConstantsBooleans::ACTIVE,
                        ),
                    ),
                ),
                array(
                    'fields' => array(
                        'Garage.complete_name'
                    ),
                    'order' => array(
                        'Garage.complete_name'
                    ),
                    'group' => array(
                        'Garage.id'
                    ),
                )
            )
        );
    }

    public function getCompleteListOnContactStaffSubtractible()
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageContactStaff',
                        'table' => 'garages_contacts_staff',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageContactStaff.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                            'Network.training =' => ConstantsBooleans::ACTIVE,
                            'Network.credit IS NOT NULL',
                        ),
                    ),
                ),
                array(
                    'fields' => array(
                        'Garage.complete_name'
                    ),
                    'order' => array(
                        'Garage.complete_name'
                    ),
                    'group' => array(
                        'Garage.id'
                    ),
                )
            )
        );
    }

    public function getCompleteListOnLive()
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                            'Network.training =' => ConstantsBooleans::ACTIVE,
                        ),
                    ),
                ),
                array(
                    'conditions' => array(
                        'Garage.aag_region_id' => ConstantsAAGRegionId::UK,
                    ),
                    'fields' => array(
                        'Garage.complete_name'
                    ),
                    'order' => array(
                        'Garage.complete_name'
                    ),
                    'group' => array(
                        'Garage.id'
                    ),
                )
            )
        );
    }

    public function get_widget_fields_statistics($network_id, $conditions)
    {
        $condition_region = [];
        $condition_country = [];
        $condition_network = [];

        if (!empty($conditions)) {
            if (!empty($conditions['region_id'])) {
                $condition_region[] = ['AagRegion.id' => $conditions['region_id']];
            }
            if (!empty($conditions['country_id'])) {
                $condition_country[] = ['Country.id' => $conditions['country_id']];
            }
            if (!empty($conditions['network_id'])) {
                $condition_network[] = ['Network.id' => $conditions['network_id']];
            }
        }

        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.id = GarageNetwork.garage_id',
                    ),
                ),
                array(
                    'alias' => 'Network',
                    'table' => 'networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Network.id = GarageNetwork.id',
                    ),
                ),
                array(
                    'alias' => 'City',
                    'table' => 'cities',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'City.id = Garage.city_id',
                    ),
                ),
                array(
                    'alias' => 'Province',
                    'table' => 'provinces',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Province.id = City.province_id',
                    ),
                ),
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Country.id = Province.country_id',
                    ),
                ),
                array(
                    'alias' => 'AagRegion',
                    'table' => 'aag_regions',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'AagRegion.id = Country.aag_region_id',
                    ),
                )
            ),
            'conditions' => array(
                'GarageNetwork.network_id' => $network_id,
                $condition_region,
                $condition_country,
                $condition_network
            ),
            'fields' => array(
                'sum(case when (Garage.address1 != null || Garage.address1 != "") then 1 else 0 end) address1',
                'sum(case when (Garage.city_id != null || Garage.city_id != "") then 1 else 0 end) city_id',
                'sum(case when (Garage.postcode != null || Garage.postcode != "") then 1 else 0 end) postcode',
                'sum(case when (Garage.phone != null || Garage.phone != "") then 1 else 0 end) phone',
                'sum(case when (Garage.email != null || Garage.email != "") then 1 else 0 end) email',
                'sum(case when (Garage.monday_open_1 != null || Garage.monday_open_1 != "" || Garage.monday_closed_1 != null || Garage.monday_closed_1 != "" ||Garage.monday_open_2 != null || Garage.monday_open_2 != "" || Garage.monday_closed_2 != null || Garage.monday_closed_2 != "" || Garage.tuesday_open_1 != null || Garage.tuesday_open_1 != "" || Garage.tuesday_closed_1 != null || Garage.tuesday_closed_1 != "" || Garage.tuesday_open_2 != null || Garage.tuesday_open_2 != "" || Garage.tuesday_closed_2 != null || Garage.tuesday_closed_2 != "" || Garage.wednesday_open_1 != null || Garage.wednesday_open_1 != "" || Garage.wednesday_closed_1 != null || Garage.wednesday_closed_1 != "" || Garage.wednesday_open_2 != null || Garage.wednesday_open_2 != "" || Garage.wednesday_closed_2 != null || Garage.wednesday_closed_2 != "" || Garage.thursday_open_1 != null || Garage.thursday_open_1 != "" || Garage.thursday_closed_1 != null || Garage.thursday_closed_1 != "" || Garage.thursday_open_2 != null || Garage.thursday_open_2 != "" || Garage.thursday_closed_2 != null || Garage.thursday_closed_2 != "" || Garage.friday_open_1 != null || Garage.friday_open_1 != "" || Garage.friday_closed_1 != null || Garage.friday_closed_1 != "" || Garage.friday_open_2 != null || Garage.friday_open_2 != "" || Garage.friday_closed_2 != null || Garage.friday_closed_2 != "" || Garage.saturday_open_1 != null || Garage.saturday_open_1 != "" || Garage.saturday_closed_1 != null || Garage.saturday_closed_1 != "" || Garage.saturday_open_2 != null || Garage.saturday_open_2 != "" || Garage.saturday_closed_2 != null || Garage.saturday_closed_2 != "" || Garage.sunday_open_1 != null || Garage.sunday_open_1 != "" || Garage.sunday_closed_1 != null || Garage.sunday_closed_1 != "" || Garage.sunday_open_2 != null || Garage.sunday_open_2 != "" || Garage.sunday_closed_2 != null || Garage.sunday_closed_2 != "" ) then 1 else 0 end) opening_hours',
            ),
        ));
    }

    public function get_widget_statics_services($network_id, $conditions)
    {
        $condition_region = [];
        $condition_country = [];
        $condition_network = [];

        if (!empty($conditions)) {
            if (!empty($conditions['region_id'])) {
                $condition_region[] = ['AagRegion.id' => $conditions['region_id']];
            }
            if (!empty($conditions['country_id'])) {
                $condition_country[] = ['Country.id' => $conditions['country_id']];
            }
            if (!empty($conditions['network_id'])) {
                $condition_network[] = ['Network.id' => $conditions['network_id']];
            }
        }
        return $this->find('count', array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.id = GarageNetwork.garage_id',
                    ),
                ),
                array(
                    'alias' => 'GarageService',
                    'table' => 'garages_services',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageService.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'Network',
                    'table' => 'networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Network.id = GarageNetwork.id',
                    ),
                ),
                array(
                    'alias' => 'City',
                    'table' => 'cities',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'City.id = Garage.city_id',
                    ),
                ),
                array(
                    'alias' => 'Province',
                    'table' => 'provinces',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Province.id = City.province_id',
                    ),
                ),
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Country.id = Province.country_id',
                    ),
                ),
                array(
                    'alias' => 'AagRegion',
                    'table' => 'aag_regions',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'AagRegion.id = Country.aag_region_id',
                    ),
                )
            ),
            'conditions' => array(
                'GarageNetwork.network_id' => $network_id,
                $condition_region,
                $condition_country,
                $condition_network
            ),
            'fields' => array(
                'Garage.id'
            ),
            'group' => array(
                'GarageService.garage_id'
            )
        ));
    }

    public function get_widget_statics_vehicles($network_id, $conditions)
    {
        $condition_region = [];
        $condition_country = [];
        $condition_network = [];

        if (!empty($conditions)) {
            if (!empty($conditions['region_id'])) {
                $condition_region[] = ['AagRegion.id' => $conditions['region_id']];
            }
            if (!empty($conditions['country_id'])) {
                $condition_country[] = ['Country.id' => $conditions['country_id']];
            }
            if (!empty($conditions['network_id'])) {
                $condition_network[] = ['Network.id' => $conditions['network_id']];
            }
        }
        return $this->find('count', array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.id = GarageNetwork.garage_id',
                    ),
                ),
                array(
                    'alias' => 'GarageVehicleType',
                    'table' => 'garages_vehicle_types',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageVehicleType.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'Network',
                    'table' => 'networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Network.id = GarageNetwork.id',
                    ),
                ),
                array(
                    'alias' => 'City',
                    'table' => 'cities',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'City.id = Garage.city_id',
                    ),
                ),
                array(
                    'alias' => 'Province',
                    'table' => 'provinces',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Province.id = City.province_id',
                    ),
                ),
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Country.id = Province.country_id',
                    ),
                ),
                array(
                    'alias' => 'AagRegion',
                    'table' => 'aag_regions',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'AagRegion.id = Country.aag_region_id',
                    ),
                )
            ),
            'conditions' => array(
                'GarageNetwork.network_id' => $network_id,
                $condition_region,
                $condition_country,
                $condition_network
            ),
            'fields' => array(
                'Garage.id'
            ),
            'group' => array(
                'GarageVehicleType.garage_id'
            )
        ));
    }

    /**
     * Create a garage that comes from a JSON from Lobster with the data for the GV network.
     *
     * @param garage Garage
     */
    public function addGarageGV($garage)
    {
        $fields = array(
            'Garage' => array(
                'guid',
                'garage_code',
                'name',
                'business_name',
                'ref_code',
                'erp_id',
                'status',
                'phone',
                'mobile',
                'email',
                'web',
                'address1',
                'postcode',
                'town',
                'city_id',
                'province_id',
                'latitude',
                'longitude',
                'monday_open_1',
                'monday_closed_1',
                'tuesday_open_1',
                'tuesday_closed_1',
                'wednesday_open_1',
                'wednesday_closed_1',
                'thursday_open_1',
                'thursday_closed_1',
                'friday_open_1',
                'friday_closed_1',
                'saturday_open_1',
                'saturday_closed_1',
                'sunday_open_1',
                'sunday_closed_1',
                'aag_region_id',
                'creation_date',
                'modification_date',
                'repairmaintenance',
                'payment_terms',
                'company_code'
            )
        );

        $garage['Garage']['creation_date'] = date('Y-m-d H:i:s');
        $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');

        if (!isset($garage['Garage']['address1'])) {
            $garage['Garage']['address1'] = '--';
        }

        $this->create();
        $garageBd = $this->guardar($garage, $fields);
        if (!$garageBd) {
            return false;
        }

        $this->saveGarageCode($garageBd);
        $this->commit();
        return $garageBd;
    }

    /**
     * Update a garage that comes from a JSON from Lobster with the data for the GV network.
     *
     * @param garage Garage
     */
    public function updateGarageGV($garage)
    {
        $fields = array(
            'Garage' => array(
                'guid',
                'ref_code',
                'status',
                'address1',
                'postcode',
                'latitude',
                'longitude',
                'town',
                'city_id',
                'province_id',
                'modification_date',
                'payment_terms',
                'company_code'
            )
        );
        $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');

        $garageBd = $this->guardar($garage, $fields);
        if (!$garageBd) {
            return false;
        }

        //If the garage is updated through the Lobster Json its data must be synchronized with R&M
        $this->RepairMaintenance = ClassRegistry::init('RepairMaintenance');
        $result = $this->RepairMaintenance->update_garage($garageBd);

        if (!$result) {
            return false;
        }

        $this->commit();
        return $garageBd;
    }

    public function subscriptions_for_widget($userConditions)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        )
                    ),
                ),
                'fields' => array(
                    'YEAR(GarageNetwork.contract_start_date) as year',
                    'MONTH(GarageNetwork.contract_start_date) as month',
                    'count(Garage.id) as quantity',
                    'group_concat(distinct Garage.id separator ",") as Garages_id',
                ),
                'conditions' => array(
                    'GarageNetwork.contract_start_date >=' => date('Y-m-d', strtotime("-1 year", strtotime(Fecha::converUtcToTimeZoneNow('Y-m-d')))),
                    $userConditions
                ),
                'group' => array(
                    'YEAR(GarageNetwork.contract_start_date)',
                    'MONTH(GarageNetwork.contract_start_date)'
                ),
                'order' => array(
                    'YEAR(GarageNetwork.contract_start_date)' => 'asc',
                    'MONTH(GarageNetwork.contract_start_date)' => 'asc',
                ),
            )
        );
    }

    public function unsubscriptions_for_widget($userConditions)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        )
                    ),
                ),
                'fields' => array(
                    'YEAR(GarageNetwork.contract_end_date) as year',
                    'MONTH(GarageNetwork.contract_end_date) as month',
                    'count(Garage.id) as quantity',
                    'group_concat(distinct Garage.id separator ",") as Garages_id',
                ),
                'conditions' => array(
                    'GarageNetwork.contract_end_date >=' => date('Y-m-d', strtotime("-1 year", strtotime(Fecha::converUtcToTimeZoneNow('Y-m-d')))),
                    $userConditions
                ),
                'group' => array(
                    'YEAR(GarageNetwork.contract_end_date)',
                    'MONTH(GarageNetwork.contract_end_date)'
                ),
                'order' => array(
                    'YEAR(GarageNetwork.contract_end_date)' => 'asc',
                    'MONTH(GarageNetwork.contract_end_date)' => 'asc',
                ),
            )
        );
    }

    /**
     * Generate the slug from the passed text.
     */
    public function generateSlug($textSlug, $verification = false)
    {
        $originals = 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜàáâãäåæçèéêëìíîïðñòóôõöøùúûüýýþÿ';
        $modified = 'aaaaaaceeeeiiiinoooooouuuuaaaaaaaceeeeiiiidnoooooouuuuyyby';
        $text = strtr($textSlug, $originals, $modified);
        $text = strtolower($text);
        $text = str_replace(' ', '-', $text);
        $text = preg_replace('/[^a-zA-Z0-9\_\-]+/', '', $text);
        $text = preg_replace('/\_/', "-", $text);
        $slug = preg_replace('/(\-[\-]+)/', "-", $text);

        if (!$verification && count($this->findBySlug($slug, ['id'])) > 0) {
            $n = 1;
            do {
                $slug .= '-' . $n;
                $n++;
            } while (count($this->findBySlug($slug, ['id'])) > 0);
        }
        return $slug;
    }

    /**
     * Update the Garage slug.
     *
     * @param garage Garage
     */
    public function updateGarageSlug($garage)
    {
        $fields = array(
            'Garage' => array(
                'slug',
                'modification_date'
            )
        );
        $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');

        $garageBd = $this->guardar($garage, $fields);
        if (!$garageBd) {
            return false;
        }

        $this->commit();
        return $garageBd;
    }

    /**
     * Returns the email of the garage if it has one, or the emails of the garage users if it doesn't.
     */
    public function getGarageEmail($garage)
    {
        $emails = array();

        if (empty($garage['Garage']['email'])) {
            $userClass = ClassRegistry::init('User');
            $contactClass = ClassRegistry::init('Contact');
            $usersGarage = $userClass->findAllByGarageId($garage['Garage']['id']);

            foreach ($usersGarage as $user) {
                if (!empty($user['User']['contact_id'])) {
                    $contact = $contactClass->findById($user['User']['contact_id']);

                    if ($contact && !empty($contact['Contact']['email'])) {
                        $emails[] = $contact['Contact']['email'] . ' ' . $user['User']['language_id'];
                    }
                }
            }
        } else {
            $emails[] = $garage['Garage']['email'] . ' ' . $garage['Garage']['language_id'];
        }

        return $emails;
    }

    /**
     * Update the Garage contact info (phone, mobile, fax, email, web, etc).
     *
     * @param garage Garage
     */
    public function updateGarageContactInfo($garage)
    {
        $fields = array(
            'Garage' => array(
                'phone',
                'mobile',
                'phone_international',
                'service_24h_phone',
                'fax',
                'email',
                'web',
                'modification_date'
            )
        );
        $garage['Garage']['phone'] = trim($garage['Garage']['phone']);
        $garage['Garage']['mobile'] = trim($garage['Garage']['mobile']);
        if (isset($garage['Garage']['phone_international'])) {
            $garage['Garage']['phone_international'] = trim($garage['Garage']['phone_international']);
        }
        if (isset($garage['Garage']['service_24h_phone'])) {
            $garage['Garage']['service_24h_phone'] = trim($garage['Garage']['service_24h_phone']);
        }
        $garage['Garage']['fax'] = trim($garage['Garage']['fax']);
        $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');
        if (
            !empty($garage['Garage']['web']) &&
            substr($garage['Garage']['web'], 0, 7) !== "http://" &&
            substr($garage['Garage']['web'], 0, 8) !== "https://"
        ) {
            $garage['Garage']['web'] = 'http://' . $garage['Garage']['web'];
        }

        $garageBd = $this->guardar($garage, $fields);
        if (!$garageBd) {
            return false;
        }

        $this->commit();
        return $garageBd;
    }

    /*
    * Generates a guid for the given garage.
    *
    * @param Garage garage
    */
    public function generateGuid(array $garage)
    {
        $fields = array(
            'Garage' => array(
                'guid'
            )
        );

        $garage['Garage']['guid'] = CakeText::uuid();
        return $this->guardar($garage, $fields);
    }

    /**
     * Generates a guid for every garage in DB.
     */
    public function generateGuidForAllGaragesWithoutIt(): void
    {
        $garagesWithoutGuid = $this->find('all', array(
            'conditions' => array(
                'Garage.guid' => null
            )
        ));

        foreach ($garagesWithoutGuid as $garage) {
            $this->generateGuid($garage);
        }
    }

    public function getCompleteListRegion($aag_region_id)
    {
        $conditions = array();
        $conditions = array('Garage.aag_region_id' => $aag_region_id);

        return $this->find(
            'list',
            array(
                'conditions' => $conditions,
                'fields' => array(
                    'complete_name'
                ),
                'order' => array(
                    'complete_name'
                ),
            )
        );
    }

    public function findGaragesPlanningVisits($aag_region_id)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'Garage.aag_region_id' => $aag_region_id,
                    'Garage.latitude IS NOT NULL',
                    'Garage.longitude IS NOT NULL',
                    'Garage.status' => ConstantsGarageStatus::ACTIVE,
                ),
                'fields' => array(
                    'Garage.id',
                    'Garage.latitude',
                    'Garage.longitude'
                )
            )
        );
    }

    public function crear_erp_email_talleres()
    {

        ini_set('memory_limit', '2G');
        set_time_limit(4 * 60 * 60);

        $this->Erp = ClassRegistry::init('Erp');
        $this->RepairMaintenance = ClassRegistry::init('RepairMaintenance');

        $garages = $this->find(
            'all',
            array(
                'conditions' => array(
                    'Garage.erp_id IS NOT NULL',
                    'Garage.erp_email IS NULL',
                    'Garage.aag_region_id' => Configure::read('AAG_REGION_ID_BENELUX')
                )
            )
        );

        $fields = array(
            'Garage' => array(
                'erp_email'
            )
        );

        foreach ($garages as $garage) {

            $erp = $this->Erp->findById($garage['Garage']['erp_id']);
            $garage['Garage']['erp_email'] = $garage['Garage']['ref_code'] . '_' . strtolower($erp['Erp']['erp_code']) . '@asm.com';
            $garageBd = $this->guardar($garage, $fields);

            if (!$garageBd) {
                return false;
            }

            $this->commit();

            $result = $this->RepairMaintenance->update_garage($garageBd);

            if (!$result) {
                return false;
            }
        }
    }

    public function getPossiblesGaragesLiveAjax($conditions, $aag_region_id)
    {
        $garages = $this->getPossiblesGaragesLiveQuery($conditions, $aag_region_id);
        $garages = Hash::combine($garages, '{n}.Garage.id', array('%s', '{n}.Garage.complete_search'));

        return $garages;
    }

    public function getPossiblesGaragesLiveQuery($conditions_ajax = array(), $aag_region_id)
    {
        $conditions_region = array('Garage.aag_region_id' => $aag_region_id);

        if (isset($conditions_ajax['name']) && !empty($conditions_ajax['name'])) {
            $conditions_ajax = array(
                'OR' => array(
                    'Garage.name LIKE' => '%' . $conditions_ajax['name'] . '%',
                    'Garage.g_number_id LIKE' => '%' . $conditions_ajax['name'] . '%'
                )
            );
        }

        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                            'Network.id' => array(NETWORK_ID_AUTOCARE, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_TOPTRUCK, NETWORK_ID_GEXPERT),
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageNetwork.garage_id IS NOT NULL',
                    'GarageNetwork.status' => array(ConstantsNetworksStatus::LIVE, ConstantsNetworksStatus::ON_HOLD, ConstantsNetworksStatus::LEFT),
                    $conditions_ajax,
                    $conditions_region,
                ),
                'fields' => array(
                    'Garage.id',
                    'Garage.complete_search'
                ),
                'group' => array(
                    'Garage.id'
                ),
            )
        );
    }

    /**
     * When exporting garages, the maximum number of garages allowed is checked.
     * When maximum is reached, garages are obtained.
     */
    public function dynamicTypeGaragesExportQuery($type, $conditions, $fields)
    {
        return $this->find(
            $type,
            array(
                'conditions' => array($conditions),
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                        ),
                    ),
                    array(
                        'alias' => 'AnnexDetail',
                        'table' => 'annex_details',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'AnnexDetail.id = GarageNetwork.annex_detail_id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageDistributor',
                        'table' => 'garages_distributors',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageDistributor.garage_id = Garage.id',
                            'GarageDistributor.id = (
                                SELECT MIN(gd1.id)
                                FROM garages_distributors AS gd1
                                WHERE gd1.garage_id = Garage.id
                            )',
                        ),
                    ),
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Distributor.id = GarageDistributor.distributor_id',
                        ),
                    ),
                    array(
                        'alias' => 'AssociationType',
                        'table' => 'associations_types',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'AssociationType.id = Distributor.association_type_id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageContactsStaff',
                        'table' => 'garages_contacts_staff',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageContactsStaff.garage_id = Garage.id',
                            'GarageContactsStaff.priority' => 1,
                        ),
                    ),
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Contact.id = GarageContactsStaff.contact_id',
                        ),
                    ),
                    array(
                        'alias' => 'TradingGroup',
                        'table' => 'trading_groups',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'TradingGroup.id = Distributor.trading_group_id',
                        ),
                    ),
                    array(
                        'alias' => 'AagRegion',
                        'table' => 'aag_regions',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'AagRegion.id = Garage.aag_region_id',
                        ),
                    ),
                    array(
                        'alias' => 'Province',
                        'table' => 'provinces',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Garage.province_id = Province.id',
                        ),
                    ),
                ),
                'fields' => $fields,
                'order' => array('Garage.name' => 'asc'),
                'group' => array('Garage.id'),
            )
        );
    }
    public function updateGarageRecommendedNetwork($garage_id, $recommended)
    {
        $fields = array(
            'Garage' => array(
                'id',
                'recommended_network',
                'modification_date'
            )
        );
        $garage = $this->findById($garage_id);
        $garage['Garage']['recommended_network'] = $recommended;
        $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');
        return $this->guardar($garage, $fields);
    }

    public function getPossiblesGaragesOnlyLiveAjax($conditions, $aag_region_id)
    {
        $garages = $this->getPossiblesGaragesOnlyLiveQuery($conditions, $aag_region_id);
        $garages = Hash::combine($garages, '{n}.Garage.id', array('%s', '{n}.Garage.complete_search'));

        return $garages;
    }

    public function getPossiblesGaragesOnlyLiveQuery($conditions_ajax = array(), $aag_region_id)
    {
        $conditions_region = array('Garage.aag_region_id' => $aag_region_id);

        if (isset($conditions_ajax['name']) && !empty($conditions_ajax['name'])) {
            $conditions_ajax = array(
                'OR' => array(
                    'Garage.name LIKE' => '%' . $conditions_ajax['name'] . '%',
                    'Garage.g_number_id LIKE' => '%' . $conditions_ajax['name'] . '%'
                )
            );
        }

        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                            'Network.id' => array(NETWORK_ID_AUTOCARE, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_TOPTRUCK, NETWORK_ID_GEXPERT),
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageNetwork.garage_id IS NOT NULL',
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    $conditions_ajax,
                    $conditions_region,
                ),
                'fields' => array(
                    'Garage.id',
                    'Garage.complete_search'
                ),
                'group' => array(
                    'Garage.id'
                ),
            )
        );
    }

    public function default_opening_hours_values_in_garage($garage)
    {
        $garageNetwork = $this->GarageNetwork->findByGarageId($garage['Garage']['id']);

        if (
            empty($garage['Garage']['monday_open_1']) && empty($garage['Garage']['monday_closed_1']) && empty($garage['Garage']['monday_open_2']) &&
            empty($garage['Garage']['monday_closed_2']) && empty($garage['Garage']['tuesday_open_1']) && empty($garage['Garage']['tuesday_closed_1']) &&
            empty($garage['Garage']['tuesday_open_2']) && empty($garage['Garage']['tuesday_closed_2']) && empty($garage['Garage']['wednesday_open_1']) &&
            empty($garage['Garage']['wednesday_closed_1']) && empty($garage['Garage']['wednesday_open_2']) && empty($garage['Garage']['wednesday_closed_2']) &&
            empty($garage['Garage']['thursday_open_1']) && empty($garage['Garage']['thursday_closed_1']) && empty($garage['Garage']['thursday_open_2']) &&
            empty($garage['Garage']['thursday_closed_2']) && empty($garage['Garage']['friday_open_1']) && empty($garage['Garage']['friday_closed_1']) &&
            empty($garage['Garage']['friday_open_2']) && empty($garage['Garage']['friday_closed_2']) && empty($garage['Garage']['saturday_open_1']) &&
            empty($garage['Garage']['saturday_closed_1']) && empty($garage['Garage']['saturday_open_2']) && empty($garage['Garage']['saturday_closed_2']) &&
            empty($garage['Garage']['sunday_open_1']) && empty($garage['Garage']['sunday_closed_1']) && empty($garage['Garage']['sunday_open_2']) &&
            empty($garage['Garage']['sunday_closed_2'])
        ) {
            $garage['Garage']['monday_open_1'] = $garageNetwork['GarageNetwork']['monday_planner_open_1'] ?? null;
            $garage['Garage']['monday_closed_1'] = $garageNetwork['GarageNetwork']['monday_planner_closed_1'] ?? null;
            $garage['Garage']['monday_open_2'] = $garageNetwork['GarageNetwork']['monday_planner_open_2'] ?? null;
            $garage['Garage']['monday_closed_2'] = $garageNetwork['GarageNetwork']['monday_planner_closed_2'] ?? null;
            $garage['Garage']['tuesday_open_1'] = $garageNetwork['GarageNetwork']['tuesday_planner_open_1'] ?? null;
            $garage['Garage']['tuesday_closed_1'] = $garageNetwork['GarageNetwork']['tuesday_planner_closed_1'] ?? null;
            $garage['Garage']['tuesday_open_2'] = $garageNetwork['GarageNetwork']['tuesday_planner_open_2'] ?? null;
            $garage['Garage']['tuesday_closed_2'] = $garageNetwork['GarageNetwork']['tuesday_planner_closed_2'] ?? null;
            $garage['Garage']['wednesday_open_1'] = $garageNetwork['GarageNetwork']['wednesday_planner_open_1'] ?? null;
            $garage['Garage']['wednesday_closed_1'] = $garageNetwork['GarageNetwork']['wednesday_planner_closed_1'] ?? null;
            $garage['Garage']['wednesday_open_2'] = $garageNetwork['GarageNetwork']['wednesday_planner_open_2'] ?? null;
            $garage['Garage']['wednesday_closed_2'] = $garageNetwork['GarageNetwork']['wednesday_planner_closed_2'] ?? null;
            $garage['Garage']['thursday_open_1'] = $garageNetwork['GarageNetwork']['thursday_planner_open_1'] ?? null;
            $garage['Garage']['thursday_closed_1'] = $garageNetwork['GarageNetwork']['thursday_planner_closed_1'] ?? null;
            $garage['Garage']['thursday_open_2'] = $garageNetwork['GarageNetwork']['thursday_planner_open_2'] ?? null;
            $garage['Garage']['thursday_closed_2'] = $garageNetwork['GarageNetwork']['thursday_planner_closed_2'] ?? null;
            $garage['Garage']['friday_open_1'] = $garageNetwork['GarageNetwork']['friday_planner_open_1'] ?? null;
            $garage['Garage']['friday_closed_1'] = $garageNetwork['GarageNetwork']['friday_planner_closed_1'] ?? null;
            $garage['Garage']['friday_open_2'] = $garageNetwork['GarageNetwork']['friday_planner_open_2'] ?? null;
            $garage['Garage']['friday_closed_2'] = $garageNetwork['GarageNetwork']['friday_planner_closed_2'] ?? null;
            $garage['Garage']['saturday_open_1'] = $garageNetwork['GarageNetwork']['saturday_planner_open_1'] ?? null;
            $garage['Garage']['saturday_closed_1'] = $garageNetwork['GarageNetwork']['saturday_planner_closed_1'] ?? null;
            $garage['Garage']['saturday_open_2'] = $garageNetwork['GarageNetwork']['saturday_planner_open_2'] ?? null;
            $garage['Garage']['saturday_closed_2'] = $garageNetwork['GarageNetwork']['saturday_planner_closed_2'] ?? null;
            $garage['Garage']['sunday_open_1'] = $garageNetwork['GarageNetwork']['sunday_planner_open_1'] ?? null;
            $garage['Garage']['sunday_closed_1'] = $garageNetwork['GarageNetwork']['sunday_planner_closed_1'] ?? null;
            $garage['Garage']['sunday_open_2'] = $garageNetwork['GarageNetwork']['sunday_planner_open_2'] ?? null;
            $garage['Garage']['sunday_closed_2'] = $garageNetwork['GarageNetwork']['sunday_planner_closed_2'] ?? null;
            $garage['Garage']['modification_date'] = date('Y-m-d H:i:s');
        }


        $garage_bd = $this->save($garage);
        if (!$garage_bd) {
            return false;
        }

        return $garage_bd;
    }

    public function getInternalNetworkFromGarage($garage_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Garage.id' => $garage_id,
                    'GarageNetwork.last' => ConstantsBooleans::YES,
                    'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                    'Network.internal' => ConstantsBooleans::YES,
                    'Network.id' => array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC)
                ),
                'fields' => array(
                    'Network.guid',
                ),
            )
        );
    }
}
