<?php

class Distributor extends AppModel
{
    public $useTable = 'distributors';
    public $displayField = 'name';
    public $order = 'Distributor.name';
    public $virtualFields = array(
        'complete_name' => 'CONCAT(IFNULL(Distributor.name, \'\'), " - ", IFNULL(Distributor.account_number, \'\'))',
        'complete_search' => 'CONCAT(IFNULL(Distributor.name, \'\'), " - ", IFNULL(Distributor.account_number, \'\'), " - ", IFNULL(Distributor.town, \'\'))'
    );

    var $hasAndBelongsToMany = array(
        'Garage',
    );

    var $hasMany = array(
        'Appointment',
        'DistributorRoute',
        'MessageDistributor'
    );

    public $hasOne = array(
        'TradingGroup' => array(
            'foreignKey' => 'trading_group_id',
        ),
        'AssociationType' => array(
            'foreignKey' => 'association_type_id',
        ),
    );

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
        'account_number' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_account_number',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'abbreviation' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_abbreviation',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'client_type' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_INT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'trading_group_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_trading_group',
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
        'phone' => array(
            'numeric' => array(
                'rule' => array('decimal', null, "/^[0-9]+$/"),
                'allowEmpty' => true,
                'message' => 'Validation.Only_numeric',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_PHONE),
                'message' => 'Validation.Phone_too_long',
                'allowEmpty' => true,
            ),
        ),
        'fax' => array(
            'numeric' => array(
                'rule' => array('decimal', null, "/^[0-9]+$/"),
                'allowEmpty' => true,
                'message' => 'Validation.Only_numeric',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_PHONE),
                'message' => 'Validation.Fax_too_long',
                'allowEmpty' => true,
            ),
        ),
        'trading_as' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_trading_as',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'web' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_website',
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
        'monday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'monday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'monday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'monday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'tuesday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'tuesday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'tuesday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'tuesday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'wednesday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'wednesday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'wednesday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'wednesday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'thursday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'thursday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'thursday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'thursday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'friday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'friday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'friday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'friday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'saturday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'saturday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'saturday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'saturday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'sunday_open_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'sunday_closed_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'sunday_open_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'sunday_closed_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'reg_number' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_reg_number',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'rebate_name' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_rebate_name',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'currency' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_currency',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'MAMID' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_mamid',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'VAT_number' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_VAT',
            ),
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
        'siret' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'credit_watch' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function UpdateDistributorLastVisit($distributor_id, $last_visit)
    {
        $fields = array(
            'Distributor' => array(
                'last_visit',
            )
        );

        $distributor['Distributor']['id'] = $distributor_id;
        $distributor['Distributor']['last_visit'] = $last_visit;

        $distributor_bd = $this->guardar($distributor, $fields);
        if (!$distributor_bd) {
            return false;
        }

        $this->commit();
        return $distributor_bd;
    }

    private $_queries = array(
        'Search' => array(
            'joins' => array(
                array(
                    'alias' => 'TradingGroup',
                    'table' => 'trading_groups',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Distributor.trading_group_id = TradingGroup.id',
                    ),
                ),
            ),
            'fields' => array(
                'Distributor.*',
                'TradingGroup.*',
            ),
            'order' => array(
                'Distributor.name',
            ),
            'group' => array(
                'Distributor.id',
            ),
        ),
        'visit' => array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Appointment.distributor_id = Distributor.id',
                    ),
                ),
            ),
            'fields' => array(
                'Distributor.*',
                'Appointment.*',
                'max(Appointment.date) as max_appointment_date',
            ),
            'group' => array(
                'Distributor.id',
            ),
            'order' => array(
                'Appointment.date' => 'asc',
                'Distributor.name' => 'asc'
            )
        ),
        'ajax_distributors' => array(
            'fields' => array(
                'Distributor.id',
                'Distributor.complete_name',
            ),
        ),
        'clients' => array(
            'fields' => array(
                'Distributor.*',
            ),
            'limit' => ConstantsPagination::SIZE_PAGE_SMALL,
            'order' => 'Distributor.name asc, Distributor.last_visit asc'
        ),
        'networks' => array(
            'joins' => array(
                array(
                    'alias' => 'DistributorDistributorNetwork',
                    'table' => 'distributors_distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorDistributorNetwork.distributor_id = Distributor.id',
                    ),
                ),
            ),
            'fields' => array(
                'DistributorDistributorNetwork.*',
            ),
            'order' => 'DistributorDistributorNetwork.contract_start_date desc',
        ),
        'contracts' => array(
            'joins' => array(
                array(
                    'alias' => 'DistributorContract',
                    'table' => 'distributors_contracts',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorContract.distributor_id = Distributor.id',
                    ),
                ),
            ),
            'fields' => array(
                'DistributorContract.*',
            ),
            'order' => 'DistributorContract.start_date desc',
        ),
        'software' => array(
            'joins' => array(
                array(
                    'alias' => 'DistributorSoftware',
                    'table' => 'distributors_software',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorSoftware.distributor_id = Distributor.id',
                    ),
                ),
                array(
                    'alias' => 'Software',
                    'table' => 'software',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Software.id = DistributorSoftware.software_id',
                    ),
                ),
            ),
            'fields' => array(
                'Software.*',
                'DistributorSoftware.*',
            ),
            'order' => 'DistributorSoftware.start_date desc',
        ),
        'activities' => array(
            'joins' => array(
                array(
                    'alias' => 'DistributorCustomerActivity',
                    'table' => 'distributors_customer_activities',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorCustomerActivity.distributor_id = Distributor.id',
                    ),
                ),
            ),
            'fields' => array(
                'DistributorCustomerActivity.*',
            ),
            'order' => 'DistributorCustomerActivity.start_date desc',
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

        if (!empty($fields['association_id'])) {
            $conditions[] = $this->_conditionAssociation($fields['association_id']);
        }

        if (!empty($fields['association_type_id'])) {
            $conditions[] = $this->_conditionAssociationType($fields['association_type_id']);
        }

        if (!empty($fields['name'])) {
            $conditions[] = $this->_conditionName($fields['name']);
        }

        if (!empty($fields['account_number'])) {
            $conditions[] = $this->_conditionAccountNumber($fields['account_number']);
        }

        if (!empty($fields['town'])) {
            $conditions[] = $this->_conditionTown($fields['town']);
        }

        if (!empty($fields['MAMID'])) {
            $conditions[] = $this->_conditionMAMID($fields['MAMID']);
        }

        if (!empty($fields['detax_code'])) {
            $conditions[] = $this->_conditionDetax($fields['detax_code']);
        }

        if (!empty($fields['siret'])) {
            $conditions[] = $this->_conditionSiret($fields['siret']);
        }

        if (!empty($fields['VAT_number'])) {
            $conditions[] = $this->_conditionVATNumber($fields['VAT_number']);
        }

        if (!empty($fields['reg_number'])) {
            $conditions[] = $this->_conditionRegNumber($fields['reg_number']);
        }

        if (isset($fields['subsidiary']) && $fields['subsidiary'] != '') {
            $conditions[] = $this->_conditionSubsidiary($fields['subsidiary']);
        }

        if (isset($fields['aag_member']) && $fields['aag_member'] != '') {
            $conditions[] = $this->_conditionAggMember($fields['aag_member']);
        }

        if (isset($fields['head_office']) && $fields['head_office'] != '') {
            $conditions[] = $this->_conditionHeadOffice($fields['head_office']);
        }

        if (!empty($fields['bdm_id'])) {
            $conditions[] = $this->_conditionBDM($fields['bdm_id']);
        }

        if (!empty($fields['sales_area_id'])) {
            $conditions[] = $this->_conditionSalesArea($fields['sales_area_id']);
        }

        if (!empty($fields['search_my_customers'])) {
            $conditions[] = $this->_conditionMyCustomer($fields['search_my_customers']);
        }

        if (!empty($fields['aag_region_id'])) {
            $conditions[] = $this->_conditionAagRegionId($fields['aag_region_id']);
        }

        if (!empty($fields['status'])) {
            $conditions[] = $this->_conditionStatus($fields['status']);
        }

        return $conditions;
    }

    private function _conditionMyCustomer($search_my_customers)
    {
        return array('DistributorContactBdm.contact_id' => CakeSession::read('Auth.User.contact_id'));
    }

    private function _conditionBDM($contact_id)
    {
        $this->DistributorContactBdm = ClassRegistry::init('DistributorContactBdm');
        $distributors = $this->DistributorContactBdm->findAllByContactId($contact_id);
        return array('Distributor.id' => Hash::extract($distributors, '{n}.DistributorContactBdm.distributor_id'));
    }

    private function _conditionTradingGroup($trading_group_id)
    {
        return array('Distributor.trading_group_id' => $trading_group_id);
    }

    private function _conditionSalesArea($sales_area_id)
    {
        return array('Distributor.sales_area_id' => $sales_area_id);
    }

    private function _conditionAssociation($association_id)
    {
        return array('Distributor.association_id' => $association_id);
    }

    private function _conditionAssociationType($association_type_id)
    {
        return array('Distributor.association_type_id' => $association_type_id);
    }

    private function _conditionName($name)
    {
        return array('Distributor.name LIKE' => '%' . $name . '%');
    }

    private function _conditionAccountNumber($account_number)
    {
        return array('Distributor.account_number LIKE' => '%' . $account_number . '%');
    }

    private function _conditionTown($town)
    {
        return array('Distributor.town LIKE' => '%' . $town . '%');
    }

    private function _conditionMAMID($MAMID)
    {
        return array('Distributor.MAMID LIKE' => '%' . $MAMID . '%');
    }

    private function _conditionDetax($detax_code)
    {
        return array('Distributor.detax_code LIKE' => '%' . $detax_code . '%');
    }

    private function _conditionSiret($siret)
    {
        return array('Distributor.siret LIKE' => '%' . $siret . '%');
    }

    private function _conditionVATNumber($VAT_number)
    {
        return array('Distributor.VAT_number LIKE' => '%' . $VAT_number . '%');
    }

    private function _conditionRegNumber($reg_number)
    {
        return array('Distributor.reg_number LIKE' => '%' . $reg_number . '%');
    }

    private function _conditionSubsidiary($subsidiary)
    {

        if ($subsidiary == ConstantsBooleans::YES) {
            return array('Distributor.subsidiary' => ConstantsBooleans::YES);
        }
        if ($subsidiary == ConstantsBooleans::NO) {
            return array('Distributor.subsidiary' => ConstantsBooleans::NO);
        }
    }

    private function _conditionAggMember($aag_member)
    {

        if ($aag_member == ConstantsBooleans::YES) {
            return array('Distributor.aag_member' => ConstantsBooleans::YES);
        }
        if ($aag_member == ConstantsBooleans::NO) {
            return array('Distributor.subsidiary' => ConstantsBooleans::NO);
        }
    }

    private function _conditionHeadOffice($head_office)
    {
        if ($head_office == ConstantsBooleans::YES) {
            return array('Distributor.head_office' => ConstantsBooleans::YES);
        }
        if ($head_office == ConstantsBooleans::NO) {
            return array('Distributor.head_office' => ConstantsBooleans::NO);
        }
    }

    private function _conditionAagRegionId($aag_region_id)
    {
        return array('Distributor.aag_region_id' => $aag_region_id);
    }

    private function _conditionStatus($status)
    {
        return array('Distributor.status' => $status);
    }

    public function add_distributor($distributor, $user)
    {
        $fields = array(
            'Distributor' => array(
                'name',
                'account_number',
                'abbreviation',
                'trading_as',
                'distributor_type_id',
                'phone',
                'fax',
                'email',
                'web',
                'address1',
                'address2',
                'address3',
                'address4',
                'credit_watch',
                'latitude',
                'longitude',
                'town',
                'province_id',
                'postcode',
                'head_office',
                'subsidiary',
                'aag_member',
                'trading_group_id',
                'distributor_id',
                'reg_number',
                'rebate_name',
                'sales_area_id',
                'association_type_id',
                'currency',
                'MAMID',
                'VAT_number',
                'creation_date',
                'modification_date',
                'detax_code',
                'siret',
                'status',
                'aag_region_id',
                'language_id'
            )
        );

        $distributor['Distributor']['creation_date'] = date('Y-m-d H:i:s');
        $distributor['Distributor']['modification_date'] = date('Y-m-d H:i:s');
        $distributor['Distributor']['user_id'] = $user['id'];
        $distributor['Distributor']['aag_region_id'] = $user['aag_region_id'];

        if ($distributor['Distributor']['distributor_id'] == null) {
            $distributor['Distributor']['head_office'] = true;
        } else {
            $distributor['Distributor']['head_office'] = false;
        }

        // if(isset($distributor['Distributor']['association_id'])){
        //     if ($distributor['Distributor']['association_id'] == 8) {
        //         $distributor['Distributor']['subsidiary'] = true;
        //     } else {
        //         $distributor['Distributor']['subsidiary'] = false;
        //     }
        // }

        if (!empty($distributor['Distributor']['web']) && substr($distributor['Distributor']['web'], 0, 7) !== "http://" && substr($distributor['Distributor']['web'], 0, 8) !== "https://") {
            $distributor['Distributor']['web'] = 'http://' . $distributor['Distributor']['web'];
        }

        $this->create();
        $distributor_bd = $this->guardar($distributor, $fields);
        if (!$distributor_bd) {
            return false;
        }

        $this->commit();
        return $distributor_bd;
    }

    public function edit_distributor($distributor)
    {
        $fields = array(
            'Distributor' => array(
                'name',
                'account_number',
                'abbreviation',
                'trading_as',
                'distributor_type_id',
                'phone',
                'fax',
                'email',
                'web',
                'address1',
                'address2',
                'address3',
                'address4',
                'credit_watch',
                'latitude',
                'longitude',
                'town',
                'province_id',
                'postcode',
                'head_office',
                'subsidiary',
                'aag_member',
                'trading_group_id',
                'reg_number',
                'rebate_name',
                'sales_area_id',
                'association_type_id',
                'currency',
                'MAMID',
                'VAT_number',
                'distributor_id',
                'detax_code',
                'siret',
                'status',
                'start_date',
                'end_date',
                'modification_date',
                'language_id'
            )
        );

        if ($distributor['Distributor']['distributor_id'] == null) {
            $distributor['Distributor']['head_office'] = true;
        } else {
            $distributor['Distributor']['head_office'] = false;
        }

        if (!empty($distributor['Distributor']['web']) && substr($distributor['Distributor']['web'], 0, 7) !== "http://" && substr($distributor['Distributor']['web'], 0, 8) !== "https://") {
            $distributor['Distributor']['web'] = 'http://' . $distributor['Distributor']['web'];
        }

        $distributor['Distributor']['start_date'] = Fecha::toFormatoBd($distributor['Distributor']['start_date']);
        $distributor['Distributor']['end_date'] = Fecha::toFormatoBd($distributor['Distributor']['end_date']);
        $distributor['Distributor']['modification_date'] = date('Y-m-d H:i:s');

        $distributor_bd = $this->guardar($distributor, $fields);
        if (!$distributor_bd) {
            return false;
        }

        $this->commit();
        return $distributor_bd;
    }

    public function add_opening_distributor($distributor)
    {
        $fields = array(
            'Distributor' => array(
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

        $distributor['Distributor']['modification_date'] = date('Y-m-d H:i:s');

        $distributor_bd = $this->guardar($distributor, $fields);
        if (!$distributor_bd) {
            return false;
        }

        $this->commit();
        return $distributor_bd;
    }

    public function edit_modification_date($distributor_id)
    {
        $fields = array(
            'Distributor' => array(
                'modification_date',
            )
        );

        $distributor['Distributor']['id'] = $distributor_id;
        $distributor['Distributor']['modification_date'] = date('Y-m-d H:i:s');

        $distributor_bd = $this->guardar($distributor, $fields);
        if (!$distributor_bd) {
            return false;
        }

        return $distributor_bd;
    }

    public function edit_creation_date($creation_date, $distributor_id)
    {
        $fields = array(
            'Distributor' => array(
                'creation_date',
            )
        );

        $distributor['Distributor']['id'] = $distributor_id;
        $distributor['Distributor']['creation_date'] = $creation_date;

        $distributor_bd = $this->guardar($distributor, $fields);
        if (!$distributor_bd) {
            return false;
        }

        return $distributor_bd;
    }

    public function saveLastVisit($distributor_id, $last_visit)
    {
        $fields = array(
            'Distributor' => array(
                'last_visit',
            )
        );

        $distributor['Distributor']['id'] = $distributor_id;
        $distributor['Distributor']['last_visit'] = $last_visit;
        $distributor_bd = $this->guardar($distributor, $fields);
        if (!$distributor_bd) {
            return false;
        }
        return $distributor_bd;
    }

    public function getCompleteList($aag_region_id)
    {
        $conditions_aag_region = array();
        $conditions_aag_region = array('Distributor.aag_region_id' => $aag_region_id);

        $distributors = $this->find(
            'all',
            array(
                'conditions' => $conditions_aag_region,
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.account_number'
                ),
                'order' => array(
                    'Distributor.name',
                    'Distributor.account_number'
                ),
            )
        );

        $completeList = array();
        foreach ($distributors as $distributor) {
            $completeList[$distributor['Distributor']['id']] = $distributor['Distributor']['name'] . ' - ' . $distributor['Distributor']['account_number'];
        }

        return $completeList;
    }

    public function getActiveCompleteList()
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Distributor.status' => ConstantsDistributorStatus::ACTIVE,
                ),
                'fields' => array(
                    'complete_name'
                ),
                'order' => array(
                    'complete_name'
                ),
            )
        );
    }

    public function getCompleteListName()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'name'
                ),
                'order' => array(
                    'name'
                ),
            )
        );
    }

    public function getCompleteListAccountNumber()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'account_number'
                ),
                'order' => array(
                    'account_number'
                ),
            )
        );
    }

    public function getListLV()
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'DistributorDistributorActivity',
                        'table' => 'distributors_distributors_activities',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorDistributorActivity.distributor_id = Distributor.id',
                        ),
                    ),
                    array(
                        'alias' => 'DistributorActivity',
                        'table' => 'distributors_activities',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorActivity.id = DistributorDistributorActivity.distributor_activity_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorActivity.id' => ConstantsDistributorActivity::LV
                ),
                'fields' => array(
                    'complete_name'
                ),
                'order' => array(
                    'complete_name'
                ),
            )
        );
    }

    public function getListCV()
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'DistributorDistributorActivity',
                        'table' => 'distributors_distributors_activities',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorDistributorActivity.distributor_id = Distributor.id',
                        ),
                    ),
                    array(
                        'alias' => 'DistributorActivity',
                        'table' => 'distributors_activities',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorActivity.id = DistributorDistributorActivity.distributor_activity_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorActivity.id' => ConstantsDistributorActivity::CV
                ),
                'fields' => array(
                    'complete_name'
                ),
                'order' => array(
                    'complete_name'
                ),
            )
        );
    }

    public function getAllByGarage($garage_id)
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
                            'Distributor.id = GarageDistributor.distributor_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageDistributor.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Distributor.*',
                    'GarageDistributor.*',
                ),
                'order' => array(
                    'Distributor.name',
                ),
            )
        );
    }

    public function getAllByGarageIdByOrder($garage_id)
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
                            'Distributor.id = GarageDistributor.distributor_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageDistributor.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Distributor.*',
                    'GarageDistributor.*',
                ),
                'order' => array(
                    'GarageDistributor.order'
                ),
            )
        );
    }

    /**
     * Get first Distributor by Garage ID.
     */
    public function getFirstByGarageId($garageId)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageDistributor',
                        'table' => 'garages_distributors',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Distributor.id = GarageDistributor.distributor_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageDistributor.garage_id' => $garageId,
                ),
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.account_number'
                )
            )
        );
    }

    public function getAllByGarageIdByOrderGarage($garage_id)
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
                            'Distributor.id = GarageDistributor.distributor_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageDistributor.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Distributor.name',
                    'Distributor.account_number',
                    'Distributor.MAMID',
                    'Distributor.reg_number',
                    'GarageDistributor.id',
                    'GarageDistributor.distributor_id',
                    'GarageDistributor.principal',
                    'GarageDistributor.order'
                ),
                'order' => array(
                    'GarageDistributor.order'
                ),
            )
        );
    }

    public function getPrincipalImageDatas($distributor_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'DistributorImage',
                        'table' => 'distributors_images',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorImage.principal' => true,
                            'DistributorImage.distributor_id = Distributor.id'
                        )
                    ),
                ),
                'conditions' => array(
                    'Distributor.id' => $distributor_id,
                ),
                'fields' => array(
                    'DistributorImage.*',
                )
            )
        );
    }

    public function getListDistributorAccountNumber()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'Distributor.id',
                    'Distributor.account_number',
                ),
            )
        );
    }

    public function getLastVisits()
    {

        $user = CakeSession::read('Auth.User');
        $role_id = $user['role_id'];
        $aag_region_id = $user['aag_region_id'];

        if ($role_id == ConstantsRoles::BDM_AAG || $role_id == ConstantsRoles::BDM_TG || $role_id == ConstantsRoles::GPC_LOGISTICS_BDM) {
            $this->DistributorContactBdm = ClassRegistry::init('DistributorContactBdm');
            $distributor_users = $this->DistributorContactBdm->findAllByContactId($user['contact_id']);
            $conditions = array('Distributor.id' => Hash::extract($distributor_users, '{n}.DistributorContactBdm.distributor_id'));
        } else {
            $conditions = array();
        }
        $conditions[] = array(
            'Distributor.status' => ConstantsDistributorStatus::ACTIVE,
            'Distributor.aag_region_id' => $aag_region_id
        );

        $distributors = $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Appointment.distributor_id = Distributor.id',
                        'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
                        'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT
                    ),
                ),
            ),
            'group' => array(
                'Distributor.id',
                'Distributor.name',
                'Distributor.town',
                'Distributor.address1'
            ),
            'order' => array(
                'max(Appointment.date)' => 'desc',
                'Distributor.name' => 'asc'
            ),
            'conditions' => $conditions,
            'limit' => ConstantsPagination::SIZE_PAGE_SMALL,
            'fields' => array(
                'Distributor.id',
                'Distributor.name',
                'Distributor.town',
                'Distributor.address1',
                'max(Appointment.date) as max_appointment_date'
            )
        ));

        return $distributors;
    }

    public function getDistributorsVisits($conditions)
    {
        $distributors = $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'DistributorRoute',
                    'table' => 'distributors_routes',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'DistributorRoute.distributor_id = Distributor.id',
                    ),
                ),
                array(
                    'alias' => 'DistributorContactBdm',
                    'table' => 'distributors_contacts_bdm',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'DistributorContactBdm.distributor_id = Distributor.id',
                    ),
                ),
            ),
            'group' => array(
                'Distributor.id'
            ),
            'conditions' => $conditions,
            'fields' => array(
                'Distributor.id',
                'Distributor.name',
                'Distributor.latitude',
                'Distributor.longitude',
                'Distributor.client_type',
                'Distributor.town',
                'Distributor.last_visit',
                'DistributorRoute.route_id',
                'group_concat(distinct DistributorRoute.route_id separator ",") as routes',
                'group_concat(distinct DistributorContactBdm.contact_id separator ",") as contacts',
            ),
            'order' => array(
                'Distributor.name',
            ),
        ));

        foreach ($distributors as $key => $distributor) {
            // $this->DistributorContactBdm = ClassRegistry::init('DistributorContactBdm');
            // $contacts_tmp = $this->DistributorContactBdm->findAllByDistributorId($distributor['Distributor']['id'], array('fields' => 'contact_id'));
            // foreach ($contacts_tmp as $key_contact => $contact) {
            //     $distributors[$key]['Distributor']['contact_id'][] = $contact['DistributorContactBdm']['contact_id'];
            // }
            if (!is_null($distributors[$key]['Distributor']['last_visit'])) {
                $three_months = date('Y-m-d', strtotime("-3 months"));
                $six_months = date('Y-m-d', strtotime("-6 months"));
                if ($distributors[$key]['Distributor']['last_visit'] < $six_months) {
                    $distributors[$key]['Distributor']['last_visit'] = ConstantsLastVisit::PLUS_SIX_MONTHS;
                } else if ($distributors[$key]['Distributor']['last_visit'] > $three_months) {
                    $distributors[$key]['Distributor']['last_visit'] = ConstantsLastVisit::MINUS_THREE_MONTHS;
                } else {
                    $distributors[$key]['Distributor']['last_visit'] = ConstantsLastVisit::THREE_MONTHS_SIX_MONTHS;
                }
            } else {
                $distributors[$key]['Distributor']['last_visit'] = ConstantsLastVisit::NO_VISIT;
            }
        }
        return $distributors;
    }

    public function getVisits($conditions)
    {
        $distributors = $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'TradingGroup',
                    'table' => 'trading_groups',
                    'type' => 'INNER',
                    'conditions' => array(
                        'TradingGroup.id = Distributor.trading_group_id',
                    ),
                ),
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Appointment.distributor_id = Distributor.id',
                    ),
                ),
                array(
                    'alias' => 'DistributorRoute',
                    'table' => 'distributors_routes',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'DistributorRoute.distributor_id = Distributor.id',
                    )
                ),
                array(
                    'alias' => 'DistributorContactBdm',
                    'table' => 'distributors_contacts_bdm',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'DistributorContactBdm.distributor_id = Distributor.id'
                    )
                )
            ),
            'conditions' => $conditions,
            'group' => array(
                'Distributor.id'
            ),
            'fields' => array(
                'Distributor.id',
                'Distributor.name',
                'Distributor.account_number',
                'Distributor.client_type',
                'Distributor.postcode',
                'Distributor.town',
                'Distributor.address1',
                'Distributor.latitude',
                'Distributor.longitude',
                'Distributor.last_visit',
                'Appointment.date'
            ),
            'order' => array(
                array('Distributor.last_visit', 'Distributor.name')
            ),
            'limit' => ConstantsPagination::SIZE_PLANNING
        ));

        return $distributors;
    }

    public function getDistributorsProvinceIdNull()
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'Distributor.province_id' => null
                ),
                'fields' => array(
                    'Distributor.*'
                ),
            )
        );
    }

    public function getDistributorAndBranchesByDistributorId($distributor_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'OR' => array(
                        'Distributor.id' => $distributor_id,
                        'Distributor.distributor_id' => $distributor_id
                    )
                ),
                'fields' => array(
                    'Distributor.complete_name'
                ),
                'order' => array(
                    'Distributor.complete_name'
                ),
            )
        );
    }

    public function getAllDistributorAndBranchesByDistributorId($distributor_id)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'OR' => array(
                        'Distributor.id' => $distributor_id,
                        'Distributor.distributor_id' => $distributor_id
                    )
                ),
                'fields' => array(
                    'Distributor.id'
                ),
            )
        );
    }

    /**
     * Create a new distributor that comes from UK JSON.
     */
    public function createDistributorUk($distributor_json)
    {
        $clear_characters = array("+", "(", ")", " ", ".");
        $distributor = array(
            'Distributor' => array(
                'name' => (isset($distributor_json['Name'])) ? $distributor_json['Name'] : null,
                'account_number' => (isset($distributor_json['AccountNumber'])) ? $distributor_json['AccountNumber'] : null,
                'abbreviation' => (isset($distributor_json['Abbreviation'])) ? $distributor_json['Abbreviation'] : null,
                'head_office' => ($distributor_json['HeadOffice'] == ConstantsBooleans::ACTIVE) ? true : false,
                'distributor_type_id' => ($distributor_json['HeadOffice'] == ConstantsBooleans::ACTIVE) ? 1 : 2,
                'subsidiary' => null,
                'client_type' => 'A',
                'trading_as' => (isset($distributor_json['TradingAs'])) ? $distributor_json['TradingAs'] : null,
                'phone' => (isset($distributor_json['Tel'])) && is_numeric($distributor_json['Tel']) ? trim(str_replace($clear_characters, '', $distributor_json['Tel'])) : null,
                'fax' => (isset($distributor_json['Fax'])) && is_numeric($distributor_json['Fax']) ? trim(str_replace($clear_characters, '', $distributor_json['Fax'])) : null,
                'email' => (isset($distributor_json['Email'])) && $this->is_valid_email($distributor_json['Email']) ? trim($distributor_json['Email']) : null,
                'web' => (isset($distributor_json['WebsiteURL'])) ? $distributor_json['WebsiteURL'] : null,
                'address1' => (isset($distributor_json['Addra'])) ? $distributor_json['Addra'] : null,
                'address2' => (isset($distributor_json['Addrb'])) ? $distributor_json['Addrb'] : null,
                'address3' => (isset($distributor_json['Addrc'])) ? $distributor_json['Addrc'] : null,
                'address4' => (isset($distributor_json['Addrd'])) ? $distributor_json['Addrd'] : null,
                'latitude' => null,
                'longitude' => null,
                'town' => (isset($distributor_json['City'])) ? $distributor_json['City'] : null,
                'province_id' => null,
                'postcode' => (isset($distributor_json['PCode'])) ? $distributor_json['PCode'] : null,
                'reg_number' => (isset($distributor_json['RegNumber'])) ? $distributor_json['RegNumber'] : null,
                'rebate_name' => (isset($distributor_json['RebateName'])) ? $distributor_json['RebateName'] : null,
                'software_id' => null,
                'currency' => (isset($distributor_json['Currency'])) ? $distributor_json['Currency'] : null,
                'MAMID' => (isset($distributor_json['MAMID'])) ? $distributor_json['MAMID'] : null,
                'VAT_number' => (isset($distributor_json['VatNumber'])) ? $distributor_json['VatNumber'] : null,
                'trading_group_id' => null,
                'distributor_id' => null,
                'creation_date' =>  date('Y-m-d H:i:s'),
                'modification_date' =>  date('Y-m-d H:i:s'),
                'user_id' =>  5,
                'status' =>  isset($distributor_json['ExportFlag']) && $distributor_json['ExportFlag'] ? ConstantsDistributorStatus::ACTIVE : (!isset($distributor_json['ExportFlag']) ? ConstantsDistributorStatus::ACTIVE : ConstantsDistributorStatus::INACTIVE),
                'aag_region_id' => isset($distributor_json['aag_region_id']) ? $distributor_json['aag_region_id'] : null,
            )
        );

        if (isset($distributor_json['PCode'])) {
            $prepAddr = isset($distributor_json['Addra']) ? $distributor_json['Addra'] : '';
            $prepAddr = isset($distributor_json['City']) ? $prepAddr . ',' . $distributor_json['City'] : $prepAddr;
            $prepAddr = isset($distributor_json['PCode']) ? $prepAddr . ',' . $distributor_json['PCode'] : $prepAddr;

            $prepAddr = str_replace(' ', '+', $prepAddr);

            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&key=' . Texto::encryptDecryptText(GOOGLE_API_KEY, false) . '&sensor=false&v=3');
            $output = json_decode($geocode);

            if (isset($output->results[0]->geometry->location)) {
                $latitude = $output->results[0]->geometry->location->lat;
                $longitude = $output->results[0]->geometry->location->lng;
                $distributor['Distributor']['latitude'] = $latitude;
                $distributor['Distributor']['longitude'] = $longitude;
            } else {
                CakeLog::write('updates', 'The ' . $distributor['Distributor']['name'] . ' distributor cannot be geolocated' . PHP_EOL);
            }
        }

        $trading_group_bd = null;

        if ($distributor_json['TradingGroup'] != '') {
            if ($distributor_json['TradingGroup'] == 'GA' || $distributor_json['TradingGroup'] == 'GROUPAUTO') {
                $distributor['Distributor']['trading_group_id'] = 1;
            } elseif ($distributor_json['TradingGroup'] == 'UAN') {
                $distributor['Distributor']['trading_group_id'] = 2;
            } elseif ($distributor_json['TradingGroup'] == 'A1MS') {
                $distributor['Distributor']['trading_group_id'] = 4;
            } elseif ($distributor_json['TradingGroup'] == 'AAG Subsidiaries') {
                $distributor['Distributor']['trading_group_id'] = 5;
            } elseif ($distributor_json['TradingGroup'] == 'CAAR') {
                $distributor['Distributor']['trading_group_id'] = 6;
            } elseif ($distributor_json['TradingGroup'] == 'DC to DC') {
                $distributor['Distributor']['trading_group_id'] = 7;
            } elseif ($distributor_json['TradingGroup'] == 'Export') {
                $distributor['Distributor']['trading_group_id'] = 8;
            } elseif ($distributor_json['TradingGroup'] == 'Group Export') {
                $distributor['Distributor']['trading_group_id'] = 9;
            } elseif ($distributor_json['TradingGroup'] == 'IFA') {
                $distributor['Distributor']['trading_group_id'] = 10;
            } elseif ($distributor_json['TradingGroup'] == 'LKQ') {
                $distributor['Distributor']['trading_group_id'] = 11;
            } elseif ($distributor_json['TradingGroup'] == 'MPD') {
                $distributor['Distributor']['trading_group_id'] = 12;
            } elseif ($distributor_json['TradingGroup'] == 'National Retail') {
                $distributor['Distributor']['trading_group_id'] = 13;
            } elseif ($distributor_json['TradingGroup'] == 'OEM/OES') {
                $distributor['Distributor']['trading_group_id'] = 14;
            } elseif ($distributor_json['TradingGroup'] == 'Parts Alliance') {
                $distributor['Distributor']['trading_group_id'] = 15;
            } elseif ($distributor_json['TradingGroup'] == 'PDP') {
                $distributor['Distributor']['trading_group_id'] = 16;
            } elseif ($distributor_json['TradingGroup'] == 'RAPID') {
                $distributor['Distributor']['trading_group_id'] = 17;
            } elseif ($distributor_json['TradingGroup'] == 'Tyre & Fastfit') {
                $distributor['Distributor']['trading_group_id'] = 18;
            } elseif ($distributor_json['TradingGroup'] == 'Z_Other') {
                $distributor['Distributor']['trading_group_id'] = 19;
            } else {
                $this->TradingGroup = ClassRegistry::init("TradingGroup");
                $trading_group_bd = $this->TradingGroup->findByCode($distributor_json['TradingGroup']);

                if ($trading_group_bd) {
                    $distributor['Distributor']['trading_group_id'] = $trading_group_bd['TradingGroup']['id'];
                } else {

                    $fieldst = array(
                        'TradingGroup' => array(
                            'name',
                            'code',
                            'web',
                            'image',
                            'is_cv',
                            'primary_color',
                            'primary_font_color',
                            'primary_background_color',
                            'secondary_color',
                            'secondary_font_color',
                            'secondary_background_color',
                            'tertiary_color',
                            'color_active',
                            'menu_color',
                            'menu_background_color',
                            'color_exito',
                            'color_fallo',
                            'color_informacion',
                            'color_disabled',
                            'creation_date',
                            'aag_region_id',
                        )
                    );

                    $trading_group = array(
                        'TradingGroup' => array(
                            'name' => $distributor_json['TradingGroup'],
                            'code' => $distributor_json['TradingGroup'],
                            'image' => 'no_image.jpg',
                            'web' => null,
                            'is_cv' => null,
                            'independent' => ConstantsBooleans::ACTIVE,
                            'primary_color' => '0064AE',
                            'primary_font_color' => '303030',
                            'primary_background_color' => 'FFFFFF',
                            'secondary_color' => '7591B0',
                            'secondary_font_color' => '606060',
                            'secondary_background_color' => 'F5F5F5',
                            'color_active' => '00C6F3',
                            'tertiary_color' => 'D3DBE2',
                            'menu_color' => '0064AE',
                            'menu_background_color' => 'FFFFFF',
                            'color_exito' => '79D282',
                            'color_fallo' => 'FE472F',
                            'color_informacion' => 'F27B4D',
                            'color_disabled' => 'E7E7E7',
                            'creation_date' =>  date('Y-m-d H:i:s'),
                            'aag_region_id' => Configure::read('AAG_REGION_ID_UK_IRELAND'),
                        )
                    );

                    $this->TradingGroup->create();
                    // Remove validations on create
                    $trading_group_bd = $this->TradingGroup->save($trading_group, array('validate' => false, 'fieldList' => $fieldst));
                    if (!$trading_group_bd) {
                        CakeLog::write('updates', 'The ' . $distributor_json['TradingGroup'] . ' TradingGroup cannot be created' . PHP_EOL);
                    } else {
                        $this->TradingGroup->generate_style_trading_group($trading_group_bd);
                        $distributor['Distributor']['trading_group_id'] = $trading_group_bd['TradingGroup']['id'];
                    }
                }
            }
        } else {
            return false;
        }

        if ($distributor_json['Association'] != '') {
            if ($distributor_json['Association'] == 'Alliance Automotive (UK) Ltd') {
                $distributor['Distributor']['subsidiary'] =  true;
                $distributor['Distributor']['association_type_id'] = ConstantsDistributorsAssociationTypes::SUBSIDIARY;
            } else {
                $distributor['Distributor']['subsidiary'] =  false;
                $distributor['Distributor']['association_type_id'] = ConstantsDistributorsAssociationTypes::INDEPENDENT;
            }
        }

        if (isset($distributor_json['ParentAccount']) && !empty($distributor_json['ParentAccount'])) {
            $exist_parent_distributor = $this->findByAccountNumber($distributor_json['ParentAccount']);
            if ($exist_parent_distributor) {
                $distributor['Distributor']['distributor_id'] = $exist_parent_distributor['Distributor']['id'];
            }
        }

        $this->create();
        // Remove validations on update
        $distributor_bd = $this->save($distributor, array('validate' => false));

        if (!$distributor_bd) {
            CakeLog::write('updates', 'The ' . $distributor_json['Name'] . ' distributor cannot be created' . PHP_EOL);
        }

        return $distributor_bd;
    }

    public function createDis($distributor_json)
    { // New distributor comes from FRANCE JSON
        $clear_characters = array("+", "(", ")", " ", ".");
        $distributor_tmp = array(
            'Distributor' => array(
                'id' => ($distributor_json['id_isa']) ? $distributor_json['id_isa'] : null,
                'name' => isset($distributor_json['identite']['raison_sociale']) ? $distributor_json['identite']['raison_sociale'] : '--',
                'account_number' => isset($distributor_json['code']) ? $distributor_json['code'] : null,
                'abbreviation' => isset($distributor_json['identite']['raison_sociale']) ? $distributor_json['identite']['raison_sociale'] : '--',
                'head_office' => ($distributor_json['organisation'] == 'Siege' || $distributor_json['organisation'] == 'Holding') ? true : false,
                'distributor_type' => null,
                'client_type' => ($distributor_json['organisation']) ? $distributor_json['organisation'] : null,
                'subsidiary' => ($distributor_json['typologie'] == 'Independant') ? false : true,
                'aag_member' => true,
                'trading_as' => isset($distributor_json['identite']['raison_sociale']) ? $distributor_json['identite']['raison_sociale'] : null,
                'phone' => isset($distributor_json['identite']) && isset($distributor_json['identite']['telephone']) && is_numeric(str_replace($clear_characters, '', $distributor_json['identite']['telephone'])) ? str_replace($clear_characters, '', $distributor_json['identite']['telephone']) : null,
                'fax' => isset($distributor_json['identite']['fax']) && is_numeric(str_replace($clear_characters, '', $distributor_json['identite']['fax'])) ? str_replace($clear_characters, '', $distributor_json['identite']['fax']) : null,
                'email' => isset($distributor_json['email']) && $this->is_valid_email($distributor_json['identite']['email']) ? trim($distributor_json['identite']['email']) : null,
                'web' => isset($distributor_json['identite']['url']) ? $distributor_json['identite']['url'] : null,
                'address1' => isset($distributor_json['identite']) && isset($distributor_json['identite']['adresse1']) && ($distributor_json['identite']['adresse1'] != '') ? $distributor_json['identite']['adresse1'] : (isset($distributor_json['identite']) && isset($distributor_json['identite']['adresse2']) && $distributor_json['identite']['adresse2'] != '' ? $distributor_json['identite']['adresse2'] : ''),
                'address2' => isset($distributor_json['identite']) && isset($distributor_json['identite']['adresse2']) && ($distributor_json['identite']['adresse2'] != '') ? $distributor_json['identite']['adresse2'] : '',
                'address3' => null,
                'latitude' => isset($distributor_json['geolocalisation']['latitude']) ? $distributor_json['geolocalisation']['latitude'] : null,
                'longitude' => isset($distributor_json['geolocalisation']['longitude']) ? $distributor_json['geolocalisation']['longitude'] : null,
                'town' => isset($distributor_json['identite']['ville']) ? $distributor_json['identite']['ville'] : null,
                'province_id' => null,
                'postcode' => isset($distributor_json['identite']['code_postal']) ? $distributor_json['identite']['code_postal'] : null,
                'association_type_id' => null,
                'VAT_number' => isset($distributor_json['identite']['TVA']) ? $distributor_json['identite']['TVA'] : null,
                'siret' => isset($distributor_json['identite']['siret']) ? $distributor_json['identite']['siret'] : null,
                'detax_code' => null,
                'trading_group_id' => null,
                'distributor_id' => ($distributor_json['id_groupe_distributeur']) ? $distributor_json['id_groupe_distributeur'] : null,
                'creation_date' =>  date('Y-m-d H:i:s'),
                'modification_date' =>  date('Y-m-d H:i:s'),
                'user_id' =>  4,
                'status' =>  $distributor_json['actif'] ? ConstantsDistributorStatus::ACTIVE : ConstantsDistributorStatus::INACTIVE,
                'monday_open_1' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['lundi']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['lundi']['matin']['ouverture'] : null,
                'monday_closed_1' => isset($distributor_json['horaire']) && ($distributor_json['horaire']['lundi']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['lundi']['matin']['fermeture'] : null,
                'monday_open_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['lundi']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['lundi']['apres_midi']['ouverture'] : null,
                'monday_closed_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['lundi']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['lundi']['apres_midi']['fermeture'] : null,
                'tuesday_open_1' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['mardi']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['mardi']['matin']['ouverture'] : null,
                'tuesday_closed_1' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['mardi']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['mardi']['matin']['fermeture'] : null,
                'tuesday_open_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['mardi']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['mardi']['apres_midi']['ouverture'] : null,
                'tuesday_closed_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['mardi']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['mardi']['apres_midi']['fermeture'] : null,
                'wednesday_open_1' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['mercredi']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['mercredi']['matin']['ouverture'] : null,
                'wednesday_closed_1' => isset($distributor_json['horaire']) && ($distributor_json['horaire']['mercredi']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['mercredi']['matin']['fermeture'] : null,
                'wednesday_open_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['mercredi']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['mercredi']['apres_midi']['ouverture'] : null,
                'wednesday_closed_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['mercredi']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['mercredi']['apres_midi']['fermeture'] : null,
                'thursday_open_1' => isset($distributor_json['horaire']) && ($distributor_json['horaire']['jeudi']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['jeudi']['matin']['ouverture'] : null,
                'thursday_closed_1' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['jeudi']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['jeudi']['matin']['fermeture'] : null,
                'thursday_open_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['jeudi']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['jeudi']['apres_midi']['ouverture'] : null,
                'thursday_closed_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['jeudi']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['jeudi']['apres_midi']['fermeture'] : null,
                'friday_open_1' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['vendredi']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['vendredi']['matin']['ouverture'] : null,
                'friday_closed_1' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['vendredi']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['vendredi']['matin']['fermeture'] : null,
                'friday_open_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['vendredi']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['vendredi']['apres_midi']['ouverture'] : null,
                'friday_closed_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['vendredi']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['vendredi']['apres_midi']['fermeture'] : null,
                'saturday_open_1' => isset($distributor_json['horaire']) && ($distributor_json['horaire']['samedi']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['samedi']['matin']['ouverture'] : null,
                'saturday_closed_1' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['samedi']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['samedi']['matin']['fermeture'] : null,
                'saturday_open_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['samedi']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['samedi']['apres_midi']['ouverture'] : null,
                'saturday_closed_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['samedi']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['samedi']['apres_midi']['fermeture'] : null,
                'sunday_open_1' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['dimanche']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['dimanche']['matin']['ouverture'] : null,
                'sunday_closed_1' => isset($distributor_json['horaire']) &&  ($distributor_json['horaire']['dimanche']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['dimanche']['matin']['fermeture'] : null,
                'sunday_open_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['dimanche']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['dimanche']['apres_midi']['ouverture'] : null,
                'sunday_closed_2' =>  isset($distributor_json['horaire']) && ($distributor_json['horaire']['dimanche']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['dimanche']['apres_midi']['fermeture'] : null,
            )
        );

        //POSTAL CODE
        $post_code_province = ClassRegistry::init('PostcodeProvince');
        $postcode = $distributor_tmp['Distributor']['postcode'];
        if ($postcode != null) {
            $postal_code_province = $post_code_province->findByPostcode($postcode);
            if (!empty($postal_code_province)) {
                $distributor_tmp['Distributor']['province_id'] = $postal_code_province['PostcodeProvince']['province_id'];
            }
        }

        //TRADING GROUP
        if ($distributor_json['groupauto'] == true) {
            $trading_group_id = ConstantsTradingGroupsNames::GROUPAUTO_FRANCE;
        } elseif ($distributor_json['partners'] == true) {
            $trading_group_id = ConstantsTradingGroupsNames::PARTNERS;
        } elseif ($distributor_json['precisium'] == true) {
            $trading_group_id = ConstantsTradingGroupsNames::PRECISIUM;
        } elseif ($distributor_json['gefa'] == true) {
            $trading_group_id = ConstantsTradingGroupsNames::GEF_AUTO;
        }
        $distributor_tmp['Distributor']['trading_group_id'] = $trading_group_id;


        //Associantion Type
        if ($distributor_json['typologie'] == 'Independant') {
            $associantion_type_id = 2;
        } elseif ($distributor_json['typologie'] == 'Filiale') {
            $associantion_type_id = 1;
        }
        $distributor_tmp['Distributor']['association_type_id'] = $associantion_type_id;

        //DISTRIBUTOR_TYPE
        $distributor_type_exist = array();
        if ($distributor_json['organisation'] != '') {
            $this->DistributorType = ClassRegistry::init('DistributorType');

            if ($distributor_json['organisation'] == 'Holding') {
                $distributor_type = 'Siége Social';
            } else if ($distributor_json['organisation'] == 'Siege') {
                $distributor_type = 'Siége Social';
            } else if ($distributor_json['organisation'] == 'Ets Secondaire') {
                $distributor_type = 'Succursale';
            } else if ($distributor_json['organisation'] == 'Ets Secondaire Site Pal') {
                $distributor_type = 'Succursale';
            }

            $distributor_type_exist = $this->DistributorType->findByNameFr($distributor_type);
        }
        $distributor_tmp['Distributor']['distributor_type_id'] = $distributor_type_exist ? $distributor_type_exist['DistributorType']['id'] : null;

        $this->create();
        $distributor_bd = $this->save($distributor_tmp);
        if (!$distributor_bd) {
            CakeLog::write('updates-france', 'The distributor with ID ISA ' . $distributor_json['id_isa'] . ' could not be created.' . PHP_EOL);
        }

        $this->commit();
        return $distributor_bd;
    }

    public function updateDis($distributor_json, $exist_distributor)
    { // New distributor comes from FRANCE JSON
        $clear_characters = array("+", "(", ")", " ", ".");
        $distributor_tmp = array(
            'Distributor' => array(
                'id' => $exist_distributor['Distributor']['id'],
                'name' => isset($distributor_json['identite']) && isset($distributor_json['identite']['raison_sociale']) ? $distributor_json['identite']['raison_sociale'] : $exist_distributor['Distributor']['name'],
                'account_number' => isset($distributor_json['code']) ? $distributor_json['code'] : $exist_distributor['Distributor']['account_number'],
                'abbreviation' => isset($distributor_json['identite']) && isset($distributor_json['identite']['raison_sociale']) ? $distributor_json['identite']['raison_sociale'] : $exist_distributor['Distributor']['abbreviation'],
                'head_office' => ($distributor_json['organisation'] == 'Siege' || $distributor_json['organisation'] == 'Holding') ? true : false,
                'distributor_type' => null,
                'client_type' => isset($distributor_json['organisation']) ? $distributor_json['organisation'] : null,
                'subsidiary' => $distributor_json['typologie'] == 'Independant' ? false : true,
                'aag_member' => true,
                'trading_as' => isset($distributor_json['identite']) && isset($distributor_json['identite']['raison_sociale']) ? $distributor_json['identite']['raison_sociale'] : $exist_distributor['Distributor']['trading_as'],
                'phone' => isset($distributor_json['identite']) && isset($distributor_json['identite']['telephone']) && is_numeric(str_replace($clear_characters, '', $distributor_json['identite']['telephone'])) ? str_replace($clear_characters, '', $distributor_json['identite']['telephone']) : $exist_distributor['Distributor']['phone'],
                'fax' => isset($distributor_json['identite']) && isset($distributor_json['identite']['fax']) && is_numeric(str_replace($clear_characters, '', $distributor_json['identite']['fax'])) ? str_replace($clear_characters, '', $distributor_json['identite']['fax']) : $exist_distributor['Distributor']['fax'],
                'email' => isset($distributor_json['identite']) && isset($distributor_json['identite']['email']) && $this->is_valid_email($distributor_json['identite']['email']) ? trim($distributor_json['identite']['email']) : $exist_distributor['Distributor']['email'],
                'web' => isset($distributor_json['identite']) && isset($distributor_json['identite']['url']) ? $distributor_json['identite']['url'] : $exist_distributor['Distributor']['web'],
                'address1' => isset($distributor_json['identite']) && isset($distributor_json['identite']['adresse1']) && $distributor_json['identite']['adresse1'] != '' ? $distributor_json['identite']['adresse1'] : (isset($distributor_json['identite']) && isset($distributor_json['identite']['adresse1']) && isset($distributor_json['identite']['adresse2']) && $distributor_json['identite']['adresse2'] != $exist_distributor['Distributor']['address1'] ? $distributor_json['identite']['adresse2'] : $exist_distributor['Distributor']['address1']),
                'address2' => isset($distributor_json['identite']) && isset($distributor_json['identite']['adresse2']) && $distributor_json['identite']['adresse2'] != '' ? $distributor_json['identite']['adresse2'] : $exist_distributor['Distributor']['address2'],
                'address3' => null,
                'latitude' => isset($distributor_json['geolocalisation']) && isset($distributor_json['geolocalisation']['latitude']) ? $distributor_json['geolocalisation']['latitude'] : $exist_distributor['Distributor']['latitude'],
                'longitude' => isset($distributor_json['geolocalisation']) && isset($distributor_json['geolocalisation']['longitude']) ? $distributor_json['geolocalisation']['longitude'] : $exist_distributor['Distributor']['longitude'],
                'town' => isset($distributor_json['identite']) && isset($distributor_json['identite']['ville']) ? $distributor_json['identite']['ville'] : $exist_distributor['Distributor']['town'],
                'province_id' => null,
                'postcode' => isset($distributor_json['identite']) && isset($distributor_json['identite']['code_postal']) ? $distributor_json['identite']['code_postal'] : $exist_distributor['Distributor']['postcode'],
                'association_type_id' => null,
                'VAT_number' => isset($distributor_json['identite']) && isset($distributor_json['identite']['TVA']) ? $distributor_json['identite']['TVA'] : $exist_distributor['Distributor']['VAT_number'],
                'siret' => isset($distributor_json['identite']) && isset($distributor_json['identite']['siret']) ? $distributor_json['identite']['siret'] : $exist_distributor['Distributor']['siret'],
                'detax_code' => null,
                'trading_group_id' => null,
                'distributor_id' => isset($distributor_json['id_groupe_distributeur']) ? $distributor_json['id_groupe_distributeur'] : $exist_distributor['Distributor']['distributor_id'],
                'modification_date' =>  date('Y-m-d H:i:s'),
                'user_id' =>  4,
                'status' =>  $distributor_json['actif'] ? ConstantsDistributorStatus::ACTIVE : ConstantsDistributorStatus::INACTIVE,
                'monday_open_1' =>   isset($distributor_json['horaire']) && isset($distributor_json['horaire']['lundi']) && isset($distributor_json['horaire']['lundi']['matin']) && isset($distributor_json['horaire']['lundi']['matin']['ouverture']) && ($distributor_json['horaire']['lundi']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['lundi']['matin']['ouverture'] : $exist_distributor['Distributor']['monday_open_1'],
                'monday_closed_1' => isset($distributor_json['horaire']) && isset($distributor_json['horaire']['lundi']) && isset($distributor_json['horaire']['lundi']['matin']) && isset($distributor_json['horaire']['lundi']['matin']['fermeture']) && ($distributor_json['horaire']['lundi']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['lundi']['matin']['fermeture'] : $exist_distributor['Distributor']['monday_closed_1'],
                'monday_open_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['lundi']) && isset($distributor_json['horaire']['lundi']['apres_midi']) && isset($distributor_json['horaire']['lundi']['apres_midi']['ouverture']) && ($distributor_json['horaire']['lundi']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['lundi']['apres_midi']['ouverture'] : $exist_distributor['Distributor']['monday_open_2'],
                'monday_closed_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['lundi']) && isset($distributor_json['horaire']['lundi']['apres_midi']) && isset($distributor_json['horaire']['lundi']['apres_midi']['fermeture']) && ($distributor_json['horaire']['lundi']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['lundi']['apres_midi']['fermeture'] : $exist_distributor['Distributor']['monday_closed_2'],
                'tuesday_open_1' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['mardi']) && isset($distributor_json['horaire']['mardi']['matin']) && isset($distributor_json['horaire']['mardi']['matin']['ouverture']) && ($distributor_json['horaire']['mardi']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['mardi']['matin']['ouverture'] : $exist_distributor['Distributor']['tuesday_open_1'],
                'tuesday_closed_1' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['mardi']) && isset($distributor_json['horaire']['mardi']['matin']) && isset($distributor_json['horaire']['mardi']['matin']['fermeture']) && ($distributor_json['horaire']['mardi']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['mardi']['matin']['fermeture'] : $exist_distributor['Distributor']['tuesday_closed_1'],
                'tuesday_open_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['mardi']) && isset($distributor_json['horaire']['mardi']['apres_midi']) && isset($distributor_json['horaire']['mardi']['apres_midi']['ouverture']) && ($distributor_json['horaire']['mardi']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['mardi']['apres_midi']['ouverture'] : $exist_distributor['Distributor']['tuesday_open_2'],
                'tuesday_closed_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['mardi']) && isset($distributor_json['horaire']['mardi']['apres_midi']) && isset($distributor_json['horaire']['mardi']['apres_midi']['fermeture']) && ($distributor_json['horaire']['mardi']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['mardi']['apres_midi']['fermeture'] : $exist_distributor['Distributor']['tuesday_closed_2'],
                'wednesday_open_1' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['mercredi']) && isset($distributor_json['horaire']['mercredi']['matin']) && isset($distributor_json['horaire']['mercredi']['matin']['ouverture']) && ($distributor_json['horaire']['mercredi']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['mercredi']['matin']['ouverture'] : $exist_distributor['Distributor']['wednesday_open_1'],
                'wednesday_closed_1' => isset($distributor_json['horaire']) && isset($distributor_json['horaire']['mercredi']) && isset($distributor_json['horaire']['mercredi']['matin']) && isset($distributor_json['horaire']['mercredi']['matin']['fermeture']) && ($distributor_json['horaire']['mercredi']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['mercredi']['matin']['fermeture'] : $exist_distributor['Distributor']['wednesday_closed_1'],
                'wednesday_open_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['mercredi']) && isset($distributor_json['horaire']['mercredi']['apres_midi']) && isset($distributor_json['horaire']['mercredi']['apres_midi']['ouverture']) && ($distributor_json['horaire']['mercredi']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['mercredi']['apres_midi']['ouverture'] : $exist_distributor['Distributor']['wednesday_open_2'],
                'wednesday_closed_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['mercredi']) && isset($distributor_json['horaire']['mercredi']['apres_midi']) && isset($distributor_json['horaire']['mercredi']['apres_midi']['fermeture']) && ($distributor_json['horaire']['mercredi']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['mercredi']['apres_midi']['fermeture'] : $exist_distributor['Distributor']['wednesday_closed_2'],
                'thursday_open_1' => isset($distributor_json['horaire']) && isset($distributor_json['horaire']['jeudi']) && isset($distributor_json['horaire']['jeudi']['matin']) && isset($distributor_json['horaire']['jeudi']['matin']['ouverture']) && ($distributor_json['horaire']['jeudi']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['jeudi']['matin']['ouverture'] : $exist_distributor['Distributor']['thursday_open_1'],
                'thursday_closed_1' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['jeudi']) && isset($distributor_json['horaire']['jeudi']['matin']) && isset($distributor_json['horaire']['jeudi']['matin']['fermeture']) && ($distributor_json['horaire']['jeudi']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['jeudi']['matin']['fermeture'] : $exist_distributor['Distributor']['thursday_closed_1'],
                'thursday_open_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['jeudi']) && isset($distributor_json['horaire']['jeudi']['apres_midi']) && isset($distributor_json['horaire']['jeudi']['apres_midi']['ouverture']) && ($distributor_json['horaire']['jeudi']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['jeudi']['apres_midi']['ouverture'] : $exist_distributor['Distributor']['thursday_open_2'],
                'thursday_closed_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['jeudi']) && isset($distributor_json['horaire']['jeudi']['apres_midi']) && isset($distributor_json['horaire']['jeudi']['apres_midi']['fermeture']) && ($distributor_json['horaire']['jeudi']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['jeudi']['apres_midi']['fermeture'] : $exist_distributor['Distributor']['thursday_closed_2'],
                'friday_open_1' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['vendredi']) && isset($distributor_json['horaire']['vendredi']['matin']) && isset($distributor_json['horaire']['vendredi']['matin']['ouverture']) && ($distributor_json['horaire']['vendredi']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['vendredi']['matin']['ouverture'] : $exist_distributor['Distributor']['friday_open_1'],
                'friday_closed_1' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['vendredi']) && isset($distributor_json['horaire']['vendredi']['matin']) && isset($distributor_json['horaire']['vendredi']['matin']['fermeture']) && ($distributor_json['horaire']['vendredi']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['vendredi']['matin']['fermeture'] : $exist_distributor['Distributor']['friday_closed_1'],
                'friday_open_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['vendredi']) && isset($distributor_json['horaire']['vendredi']['apres_midi']) && isset($distributor_json['horaire']['vendredi']['apres_midi']['ouverture']) && ($distributor_json['horaire']['vendredi']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['vendredi']['apres_midi']['ouverture'] : $exist_distributor['Distributor']['friday_open_2'],
                'friday_closed_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['vendredi']) && isset($distributor_json['horaire']['vendredi']['apres_midi']) && isset($distributor_json['horaire']['vendredi']['apres_midi']['fermeture']) && ($distributor_json['horaire']['vendredi']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['vendredi']['apres_midi']['fermeture'] : $exist_distributor['Distributor']['friday_closed_2'],
                'saturday_open_1' => isset($distributor_json['horaire']) && isset($distributor_json['horaire']['samedi']) && isset($distributor_json['horaire']['samedi']['matin']) && isset($distributor_json['horaire']['samedi']['matin']['ouverture']) && ($distributor_json['horaire']['samedi']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['samedi']['matin']['ouverture'] : $exist_distributor['Distributor']['saturday_open_1'],
                'saturday_closed_1' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['samedi']) && isset($distributor_json['horaire']['samedi']['matin']) && isset($distributor_json['horaire']['samedi']['matin']['fermeture']) && ($distributor_json['horaire']['samedi']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['samedi']['matin']['fermeture'] : $exist_distributor['Distributor']['saturday_closed_1'],
                'saturday_open_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['samedi']) && isset($distributor_json['horaire']['samedi']['apres_midi']) && isset($distributor_json['horaire']['samedi']['apres_midi']['ouverture']) && ($distributor_json['horaire']['samedi']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['samedi']['apres_midi']['ouverture'] : $exist_distributor['Distributor']['saturday_open_2'],
                'saturday_closed_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['samedi']) && isset($distributor_json['horaire']['samedi']['apres_midi']) && isset($distributor_json['horaire']['samedi']['apres_midi']['fermeture']) && ($distributor_json['horaire']['samedi']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['samedi']['apres_midi']['fermeture'] : $exist_distributor['Distributor']['saturday_closed_2'],
                'sunday_open_1' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['dimanche']) && isset($distributor_json['horaire']['dimanche']['matin']) && isset($distributor_json['horaire']['lundi']['matin']['ouverture']) && ($distributor_json['horaire']['dimanche']['matin']['ouverture'] != '00:00') ? $distributor_json['horaire']['dimanche']['matin']['ouverture'] : $exist_distributor['Distributor']['sunday_open_1'],
                'sunday_closed_1' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['dimanche']) && isset($distributor_json['horaire']['dimanche']['matin']) && isset($distributor_json['horaire']['dimanche']['matin']['fermeture']) && ($distributor_json['horaire']['dimanche']['matin']['fermeture'] != '00:00') ? $distributor_json['horaire']['dimanche']['matin']['fermeture'] : $exist_distributor['Distributor']['sunday_closed_1'],
                'sunday_open_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['dimanche']) && isset($distributor_json['horaire']['dimanche']['apres_midi']) && isset($distributor_json['horaire']['dimanche']['apres_midi']['ouverture']) && ($distributor_json['horaire']['dimanche']['apres_midi']['ouverture'] != '00:00') ? $distributor_json['horaire']['dimanche']['apres_midi']['ouverture'] : $exist_distributor['Distributor']['sunday_open_2'],
                'sunday_closed_2' =>  isset($distributor_json['horaire']) && isset($distributor_json['horaire']['dimanche']) && isset($distributor_json['horaire']['dimanche']['apres_midi']) && isset($distributor_json['horaire']['dimanche']['apres_midi']['fermeture']) && ($distributor_json['horaire']['dimanche']['apres_midi']['fermeture'] != '00:00') ? $distributor_json['horaire']['dimanche']['apres_midi']['fermeture'] : $exist_distributor['Distributor']['sunday_closed_2'],
            )
        );

        //POSTAL CODE
        $post_code_province = ClassRegistry::init('PostcodeProvince');
        $postcode = $distributor_tmp['Distributor']['postcode'];
        if ($postcode != null) {
            $postal_code_province = $post_code_province->findByPostcode($postcode);
            if (!empty($postal_code_province)) {
                $distributor_tmp['Distributor']['province_id'] = $postal_code_province['PostcodeProvince']['province_id'];
            }
        }

        //TRADING GROUP
        if ($distributor_json['groupauto'] == true) {
            $trading_group_id = ConstantsTradingGroupsNames::GROUPAUTO_FRANCE;
        } elseif ($distributor_json['partners'] == true) {
            $trading_group_id = ConstantsTradingGroupsNames::PARTNERS;
        } elseif ($distributor_json['precisium'] == true) {
            $trading_group_id = ConstantsTradingGroupsNames::PRECISIUM;
        } elseif ($distributor_json['gefa'] == true) {
            $trading_group_id = ConstantsTradingGroupsNames::GEF_AUTO;
        } else {
            $trading_group_id = $exist_distributor['Distributor']['trading_group_id'];
            //CakeLog::write('updates-france', 'The distributor with ID ISA '.$exist_distributor['Distributor']['id'].' not have trading group'. PHP_EOL);
        }

        $distributor_tmp['Distributor']['trading_group_id'] = $trading_group_id;


        //Associantion Type
        if ($distributor_json['typologie'] == 'Independant') {
            $associantion_type_id = 2;
        } elseif ($distributor_json['typologie'] == 'Filiale') {
            $associantion_type_id = 1;
        }
        $distributor_tmp['Distributor']['association_type_id'] = $associantion_type_id;

        //DISTRIBUTOR_TYPE
        $distributor_type_exist = array();
        if ($distributor_json['organisation'] != '') {
            $this->DistributorType = ClassRegistry::init('DistributorType');

            if ($distributor_json['organisation'] == 'Holding') {
                $distributor_type = 'Siége Social';
            } else if ($distributor_json['organisation'] == 'Siege') {
                $distributor_type = 'Siége Social';
            } else if ($distributor_json['organisation'] == 'Ets Secondaire') {
                $distributor_type = 'Succursale';
            } else if ($distributor_json['organisation'] == 'Ets Secondaire Site Pal') {
                $distributor_type = 'Succursale';
            }

            $distributor_type_exist = $this->DistributorType->findByNameFr($distributor_type);
        }
        $distributor_tmp['Distributor']['distributor_type_id'] = $distributor_type_exist ? $distributor_type_exist['DistributorType']['id'] : null;

        $distributor_bd = $this->save($distributor_tmp);
        if (!$distributor_bd) {
            CakeLog::write('updates-france', 'The distributor with ID ISA ' . $distributor_json['id_isa'] . ' could not be update.' . PHP_EOL);
        }

        $this->commit();
        return $distributor_bd;
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
     * Update a distributor that comes from UK JSON.
     */
    public function updateDistributorUk($distributor_json, $exist_distributor)
    {
        $clear_characters = array("+", "(", ")", " ", ".");

        $fields = array(
            'Distributor' => array(
                'name',
                'abbreviation',
                'trading_as',
                'phone',
                'fax',
                'email',
                'web',
                'address1',
                'address2',
                'address3',
                'address4',
                'latitude',
                'longitude',
                'town',
                'province_id',
                'postcode',
                'head_office',
                'subsidiary',
                'client_type',
                'trading_group_id',
                'reg_number',
                'rebate_name',
                'association_id',
                'currency',
                'MAMID',
                'VAT_number',
                'modification_date',
                'association_type_id',
                'distributor_type_id',
                'status',
                'aag_region_id'
            )
        );

        $distributor = array(
            'Distributor' => array(
                'id' => $exist_distributor['Distributor']['id'],
                'name' => (isset($distributor_json['Name'])) ? $distributor_json['Name'] : null,
                'abbreviation' => (isset($distributor_json['Abbreviation'])) ? $distributor_json['Abbreviation'] : null,
                'head_office' => ($distributor_json['HeadOffice'] == ConstantsBooleans::ACTIVE) ? true : false,
                'distributor_type_id' => ($distributor_json['HeadOffice'] == ConstantsBooleans::ACTIVE) ? 1 : 2,
                'client_type' => 'A',
                'trading_as' => (isset($distributor_json['TradingAs'])) ? $distributor_json['TradingAs'] : null,
                'phone' => (isset($distributor_json['Tel'])) && is_numeric(trim(str_replace($clear_characters, '', $distributor_json['Tel']))) ? trim(str_replace($clear_characters, '', $distributor_json['Tel'])) : null,
                'fax' => (isset($distributor_json['Fax'])) && is_numeric(trim(str_replace($clear_characters, '', $distributor_json['Fax']))) ? trim(str_replace($clear_characters, '', $distributor_json['Fax'])) : null,
                'email' => (isset($distributor_json['Email'])) && $this->is_valid_email($distributor_json['Email']) ? trim($distributor_json['Email']) : null,
                'web' => (isset($distributor_json['WebsiteURL'])) ? $distributor_json['WebsiteURL'] : null,
                'address1' => (isset($distributor_json['Addra'])) ? $distributor_json['Addra'] : null,
                'address2' => (isset($distributor_json['Addrb'])) ? $distributor_json['Addrb'] : null,
                'address3' => (isset($distributor_json['Addrc'])) ? $distributor_json['Addrc'] : null,
                'address4' => (isset($distributor_json['Addrd'])) ? $distributor_json['Addrd'] : null,
                'town' => (isset($distributor_json['City'])) ? $distributor_json['City'] : null,
                'postcode' => (isset($distributor_json['PCode'])) ? $distributor_json['PCode'] : null,
                'reg_number' => (isset($distributor_json['RegNumber'])) ? $distributor_json['RegNumber'] : null,
                'rebate_name' => (isset($distributor_json['RebateName'])) ? $distributor_json['RebateName'] : null,
                'association_id' => null,
                'software_id' => null,
                'currency' => (isset($distributor_json['Currency'])) ? $distributor_json['Currency'] : null,
                'MAMID' => (isset($distributor_json['MAMID'])) ? $distributor_json['MAMID'] : null,
                'VAT_number' => (isset($distributor_json['VatNumber'])) ? $distributor_json['VatNumber'] : null,
                'trading_group_id' => null,
                'distributor_id' => null,
                'modification_date' =>  date('Y-m-d H:i:s'),
                'status' =>  isset($distributor_json['ExportFlag']) && $distributor_json['ExportFlag'] ? ConstantsDistributorStatus::ACTIVE : (!isset($distributor_json['ExportFlag']) ? ConstantsDistributorStatus::ACTIVE : ConstantsDistributorStatus::INACTIVE),
                'aag_region_id' => isset($distributor_json['aag_region_id']) ? $distributor_json['aag_region_id'] : null,
            )
        );

        if (isset($distributor_json['TradingGroup']) && $distributor_json['TradingGroup'] != '') {
            if ($distributor_json['TradingGroup'] == 'GA' || $distributor_json['TradingGroup'] == 'GROUPAUTO') {
                $distributor['Distributor']['trading_group_id'] = 1;
            } elseif ($distributor_json['TradingGroup'] == 'UAN') {
                $distributor['Distributor']['trading_group_id'] = 2;
            } elseif ($distributor_json['TradingGroup'] == 'A1MS') {
                $distributor['Distributor']['trading_group_id'] = 4;
            } elseif ($distributor_json['TradingGroup'] == 'AAG Subsidiaries') {
                $distributor['Distributor']['trading_group_id'] = 5;
            } elseif ($distributor_json['TradingGroup'] == 'CAAR') {
                $distributor['Distributor']['trading_group_id'] = 6;
            } elseif ($distributor_json['TradingGroup'] == 'DC to DC') {
                $distributor['Distributor']['trading_group_id'] = 7;
            } elseif ($distributor_json['TradingGroup'] == 'Export') {
                $distributor['Distributor']['trading_group_id'] = 8;
            } elseif ($distributor_json['TradingGroup'] == 'Group Export') {
                $distributor['Distributor']['trading_group_id'] = 9;
            } elseif ($distributor_json['TradingGroup'] == 'IFA') {
                $distributor['Distributor']['trading_group_id'] = 10;
            } elseif ($distributor_json['TradingGroup'] == 'LKQ') {
                $distributor['Distributor']['trading_group_id'] = 11;
            } elseif ($distributor_json['TradingGroup'] == 'MPD') {
                $distributor['Distributor']['trading_group_id'] = 12;
            } elseif ($distributor_json['TradingGroup'] == 'National Retail') {
                $distributor['Distributor']['trading_group_id'] = 13;
            } elseif ($distributor_json['TradingGroup'] == 'OEM/OES') {
                $distributor['Distributor']['trading_group_id'] = 14;
            } elseif ($distributor_json['TradingGroup'] == 'Parts Alliance') {
                $distributor['Distributor']['trading_group_id'] = 15;
            } elseif ($distributor_json['TradingGroup'] == 'PDP') {
                $distributor['Distributor']['trading_group_id'] = 16;
            } elseif ($distributor_json['TradingGroup'] == 'RAPID') {
                $distributor['Distributor']['trading_group_id'] = 17;
            } elseif ($distributor_json['TradingGroup'] == 'Tyre & Fastfit') {
                $distributor['Distributor']['trading_group_id'] = 18;
            } elseif ($distributor_json['TradingGroup'] == 'Z Other') {
                $distributor['Distributor']['trading_group_id'] = 19;
            } else {
                $this->TradingGroup = ClassRegistry::init("TradingGroup");
                $trading_group = $this->TradingGroup->findByCode($distributor_json['TradingGroup']);

                if ($trading_group) {
                    $distributor['Distributor']['trading_group_id'] = $trading_group['TradingGroup']['id'];
                } else {

                    $fieldst = array(
                        'TradingGroup' => array(
                            'name',
                            'code',
                            'web',
                            'image',
                            'is_cv',
                            'primary_color',
                            'primary_font_color',
                            'primary_background_color',
                            'secondary_color',
                            'secondary_font_color',
                            'secondary_background_color',
                            'tertiary_color',
                            'color_active',
                            'menu_color',
                            'menu_background_color',
                            'color_exito',
                            'color_fallo',
                            'color_informacion',
                            'color_disabled',
                            'creation_date',
                            'aag_region_id',
                        )
                    );

                    $trading_group = array(
                        'TradingGroup' => array(
                            'name' => $distributor_json['TradingGroup'],
                            'code' => $distributor_json['TradingGroup'],
                            'image' => 'no_image.jpg',
                            'web' => null,
                            'is_cv' => null,
                            'independent' => ConstantsBooleans::ACTIVE,
                            'primary_color' => '0064AE',
                            'primary_font_color' => '303030',
                            'primary_background_color' => 'FFFFFF',
                            'secondary_color' => '7591B0',
                            'secondary_font_color' => '606060',
                            'secondary_background_color' => 'F5F5F5',
                            'color_active' => '00C6F3',
                            'tertiary_color' => 'D3DBE2',
                            'menu_color' => '0064AE',
                            'menu_background_color' => 'FFFFFF',
                            'color_exito' => '79D282',
                            'color_fallo' => 'FE472F',
                            'color_informacion' => 'F27B4D',
                            'color_disabled' => 'E7E7E7',
                            'creation_date' =>  date('Y-m-d H:i:s'),
                            'aag_region_id' => Configure::read('AAG_REGION_ID_UK_IRELAND'),
                        )
                    );

                    $this->TradingGroup->create();
                    // Remove validations on update
                    $trading_group_bd = $this->TradingGroup->save($trading_group, array('validate' => false, 'fieldList' => $fieldst));

                    if (!$trading_group_bd) {
                        CakeLog::write('updates', 'The ' . $distributor_json['TradingGroup'] . ' TradingGroup cannot be created' . PHP_EOL);
                    } else {
                        $this->TradingGroup->generate_style_trading_group($trading_group_bd);
                        $distributor['Distributor']['trading_group_id'] = $trading_group_bd['TradingGroup']['id'];
                    }
                }
            }
        } else {
            return false;
        }

        if ($distributor_json['Association'] != '') {
            if ($distributor_json['Association'] == 'Alliance Automotive (UK) Ltd') {
                $distributor['Distributor']['subsidiary'] =  true;
                $distributor['Distributor']['association_type_id'] = ConstantsDistributorsAssociationTypes::SUBSIDIARY;
            } else {
                $distributor['Distributor']['subsidiary'] =  false;
                $distributor['Distributor']['association_type_id'] = ConstantsDistributorsAssociationTypes::INDEPENDENT;
            }
        }

        if (
            isset($distributor_json['Addra']) && !empty($distributor_json['Addra']) &&
            isset($distributor_json['City']) && !empty($distributor_json['City']) &&
            isset($distributor_json['PCode']) && !empty($distributor_json['PCode']) &&
            $distributor_json['PCode'] != $exist_distributor['Distributor']['postcode']
        ) {
            $prepAddr = isset($distributor_json['City']) ? $distributor_json['Addra'] . ',' . $distributor_json['City'] : $distributor['Addra'];
            $prepAddr = isset($distributor_json['PCode']) ? $prepAddr . ',' . $distributor_json['PCode'] : $prepAddr;

            $prepAddr = str_replace(' ', '+', $prepAddr);

            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&key=' . Texto::encryptDecryptText(GOOGLE_API_KEY, false) . '&sensor=false&v=3');
            $output = json_decode($geocode);

            if (isset($output->results[0]->geometry->location)) {
                $latitude = $output->results[0]->geometry->location->lat;
                $longitude = $output->results[0]->geometry->location->lng;
                $distributor['Distributor']['latitude'] = $latitude;
                $distributor['Distributor']['longitude'] = $longitude;
            } else {
                CakeLog::write('updates', 'The ' . $distributor['Distributor']['name'] . ' distributor cannot be geolocated' . PHP_EOL);
            }
        }

        if (isset($distributor_json['ParentAccount'])) {
            $exist_parent_distributor = $this->findByAccountNumber($distributor_json['ParentAccount']);
            if ($exist_parent_distributor) {
                $distributor['Distributor']['distributor_id'] = $exist_parent_distributor['Distributor']['id'];
            }
        }

        // Remove validations on update
        $distributor_bd = $this->save($distributor, array('validate' => false, 'fieldList' => $fields));




        if (!$distributor_bd) {
            CakeLog::write('updates', $distributor['Distributor']['name'] . 'Member not saved correctly' . PHP_EOL);
        }

        $this->commit();
        return $distributor_bd;
    }

    public function getCalendarEvents($distributor_id)
    {
        if (CakeSession::read('Auth.User.current_network')) {
            $Network = ClassRegistry::init('Network');
            $network = $Network->findById(CakeSession::read('Auth.User.current_network'));
            $color = $network['Network']['primary_color'];
        } else {
            $color = '6a99ff';
        }
        $distributor = $this->findById($distributor_id);
        $distributor_js = array();
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
            if (!empty($distributor['Distributor'][$day . '_open_1']) && !empty($distributor['Distributor'][$day . '_closed_1'])) {
                $distributor_js[] = array(
                    'id' => uniqid(),
                    'color' => $color,
                    'start' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) . ' ' . $distributor['Distributor'][$day . '_open_1'],
                    'end' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) . ' ' . $distributor['Distributor'][$day . '_closed_1'],
                    'open' => $distributor['Distributor'][$day . '_open_1'],
                    'closed' => $distributor['Distributor'][$day . '_closed_1'],
                    'day' => $key + 1
                );
            }
            if (!empty($distributor['Distributor'][$day . '_open_2']) && !empty($distributor['Distributor'][$day . '_closed_2'])) {
                $distributor_js[] = array(
                    'id' => uniqid(),
                    'color' => $color,
                    'start' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) . ' ' . $distributor['Distributor'][$day . '_open_2'],
                    'end' => date('Y-m-d', time() + ($key + 1 - date('w')) * 24 * 3600) . ' ' . $distributor['Distributor'][$day . '_closed_2'],
                    'open' => $distributor['Distributor'][$day . '_open_2'],
                    'closed' => $distributor['Distributor'][$day . '_closed_2'],
                    'day' => $key + 1
                );
            }
        }

        return $distributor_js;
    }

    public function getCustomPagination($conditions, $pagination_page, $pagination_order, $pagination_direction)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Appointment.distributor_id = Distributor.id',
                        'Appointment.date <= ' => date('Y-m-d'),
                        'Appointment.appointment_status_id' => ConstantsStatusAppointments::ACCOMPLISHED,
                        'Appointment.appointment_type_id' => ConstantsTypesAppointments::VISIT
                    ),
                ),
            ),
            'fields' => array(
                'Distributor.*',
                'Appointment.*',
                'max(Appointment.date) as max_appointment_date',
            ),
            'group' => array(
                'Distributor.id',
            ),
            'order' => array(
                $pagination_order => $pagination_direction,
            ),
            'conditions' => $conditions,
            'page' => $pagination_page,
            'limit' => ConstantsPagination::SIZE_PAGE_SMALL,
        ));
    }

    public function getPaginationCount($conditions, $searcher = null)
    {

        if ($searcher && !empty($searcher['search_my_customers'])) {
            return $this->find('count', array(
                'joins' => array(
                    array(
                        'alias' => 'Appointment',
                        'table' => 'appointments',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Appointment.distributor_id = Distributor.id',
                        ),
                    ),
                    array(
                        'table' => 'distributors_contacts_bdm',
                        'alias' => 'DistributorContactBdm',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorContactBdm.distributor_id = Distributor.id'
                        )
                    ),
                ),
                'fields' => array(
                    'Distributor.*',
                    'Appointment.*',
                    'max(Appointment.date) as max_appointment_date',
                ),
                'group' => array(
                    'Distributor.id',
                ),
                'conditions' => $conditions,
            ));
        } else {
            return $this->find('count', array(
                'joins' => array(
                    array(
                        'alias' => 'Appointment',
                        'table' => 'appointments',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Appointment.distributor_id = Distributor.id',
                        ),
                    ),
                ),
                'fields' => array(
                    'Distributor.*',
                    'Appointment.*',
                    'max(Appointment.date) as max_appointment_date',
                ),
                'group' => array(
                    'Distributor.id',
                ),
                'conditions' => $conditions,
            ));
        }
    }

    public function getDistributorActivities($distributor_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'DistributorDistributorActivity',
                    'table' => 'distributors_distributors_activities',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorDistributorActivity.distributor_id = Distributor.id',
                    ),
                ),
                array(
                    'alias' => 'DistributorActivity',
                    'table' => 'distributors_activities',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorActivity.id = DistributorDistributorActivity.distributor_activity_id',
                    ),
                ),
            ),
            'conditions' => array(
                'Distributor.id' => $distributor_id
            ),
            'fields' => array(
                'DistributorActivity.*',
            ),
        ));
    }

    public function getDistributorsByContacts($contacts)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'table' => 'distributors_contacts_bdm',
                    'alias' => 'DistributorContactBdm',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistributorContactBdm.distributor_id = Distributor.id'
                    )
                ),
            ),
            'conditions' => array(
                'DistributorContactBdm.contact_id' => $contacts
            ),
            'fields' => array(
                'Distributor.id',
                'Distributor.id'
            )
        ));
    }

    public function getListByTaskId($task_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'TaskDistributor',
                    'table' => 'tasks_distributors',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = TaskDistributor.distributor_id'
                    ),
                ),
            ),
            'conditions' => array(
                'task_id' => $task_id
            ),
            'fields' => array(
                'Distributor.id',
                'Distributor.complete_search'
            )
        ));
    }

    public function getDistributorTasks($conditions, $joins)
    {
        return $this->find(
            'all',
            array(
                'joins' => $joins,
                'conditions' => $conditions,
                'fields' => array(
                    'Distributor.id',
                )
            )
        );
    }

    public function getDistributorAccountNumber($aag_region_id, $role_id)
    {
        $conditions_aag_region = array();
        if ($role_id != ConstantsRoles::SUPER_ADMIN) {
            $conditions_aag_region = array('Distributor.aag_region_id' => $aag_region_id);
        }
        return $this->find(
            'all',
            array(
                'conditions' => $conditions_aag_region,
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.account_number',
                )
            )
        );
    }

    public function getAllDistributorsAccountNumber($name)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'Distributor.name' => $name
                ),
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.account_number',
                )
            )
        );
    }

    public function getListByMessageId($message_id)
    {
        return $this->find('list', array(
            'fields' => array(
                'Distributor.id',
                'Distributor.complete_name'
            ),
            'joins' => array(
                array(
                    'alias' => 'MessageDistributor',
                    'table' => 'messages_distributors',
                    'type' => 'INNER',
                    'conditions' => 'MessageDistributor.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'MessageDistributor.message_id' => $message_id
            ),
        ));
    }

    public function findBranchesByDistributorId($distributor_id)
    {
        return $this->find(
            'all',
            array(
                'fields' => array(
                    'Distributor.id',
                ),
                'conditions' => array(
                    'Distributor.distributor_id' => $distributor_id,
                    'Distributor.id !=' => $distributor_id,
                ),
            )
        );
    }

    public function findByDistributorName($aag_region_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'table' => 'distributors_contacts_bdm',
                        'alias' => 'DistributorContactBdm',
                        'type' => 'Left',
                        'conditions' => array(
                            'DistributorContactBdm.distributor_id = Distributor.id'
                        )
                    ),
                ),
                'conditions' => array(
                    'Distributor.aag_region_id' => $aag_region_id
                ),
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.last_visit',
                    'Distributor.account_number',
                    'Distributor.postcode',
                    'Distributor.town',
                    'Distributor.phone',
                    'Distributor.start_date',
                    'group_concat(distinct DistributorContactBdm.contact_id separator ",") as bdms',
                ),
                'group' => array(
                    'Distributor.id,Distributor.name,Distributor.last_visit,Distributor.account_number,Distributor.postcode,Distributor.town,Distributor.phone,Distributor.start_date'
                ),
            )
        );
    }

    public function obtenerPosiblesDistributorsAjax($condiciones)
    {
        $distributors = $this->obtenerPosiblesDistributorsQuery($condiciones);
        $distributors = Hash::combine($distributors, '{n}.Distributor.id', array('%s', '{n}.Distributor.complete_search'));

        return $distributors;
    }

    public function obtenerPosiblesDistributorsReportingAjax($aag_region_id, $role_id, $condiciones)
    {
        $distributors = $this->obtenerPosiblesDistributorsReportingQuery($aag_region_id, $role_id, $condiciones);
        $distributors = Hash::combine($distributors, '{n}.Distributor.id', array('%s', '{n}.Distributor.complete_search'));

        return $distributors;
    }

    public function obtenerPosiblesDistributorsQuery($condiciones_ajax = array())
    {

        if (isset($condiciones_ajax['name']) && !empty($condiciones_ajax['name'])) {
            $condiciones_ajax = array(
                'OR' => array(
                    'Distributor.name LIKE' => '%' . $condiciones_ajax['name'] . '%',
                    'Distributor.account_number LIKE' => '%' . $condiciones_ajax['name'] . '%'
                )
            );
        }

        return $this->find(
            'all',
            array(
                'conditions' => $condiciones_ajax,
                'fields' => array(
                    'Distributor.id',
                    'Distributor.complete_search'
                ),
                'group' => array(
                    'Distributor.id'
                ),
            )
        );
    }

    public function obtenerPosiblesDistributorsAjaxRegion($condiciones, $aag_region_id)
    {
        $distributors = $this->obtenerPosiblesDistributorsQueryRegion($condiciones, $aag_region_id);
        $distributors = Hash::combine($distributors, '{n}.Distributor.id', array('%s', '{n}.Distributor.complete_search'));

        return $distributors;
    }

    public function obtenerPosiblesDistributorsQueryRegion($condiciones_ajax = array(), $aag_region_id)
    {
        $conditions = array('Distributor.aag_region_id' => $aag_region_id);

        if (isset($condiciones_ajax['name']) && !empty($condiciones_ajax['name'])) {
            $condiciones_ajax = array(
                'OR' => array(
                    'Distributor.name LIKE' => '%' . $condiciones_ajax['name'] . '%',
                    'Distributor.account_number LIKE' => '%' . $condiciones_ajax['name'] . '%'
                )
            );
        }

        return $this->find(
            'all',
            array(
                'conditions' => array(
                    $condiciones_ajax,
                    $conditions,
                    'Distributor.status' => ConstantsDistributorStatus::ACTIVE,
                ),
                'fields' => array(
                    'Distributor.id',
                    'Distributor.complete_search'
                ),
                'group' => array(
                    'Distributor.id'
                )
            )
        );
    }

    public function obtenerPosiblesDistributorsAjaxRegionDynamic($condiciones, $aag_region_id)
    {
        $distributors = $this->obtenerPosiblesDistributorsQueryRegionDynamic($condiciones, $aag_region_id);
        $distributors = Hash::combine($distributors, '{n}.Distributor.id', array('%s', '{n}.Distributor.complete_search'));

        return $distributors;
    }

    public function obtenerPosiblesDistributorsQueryRegionDynamic($condiciones_ajax = array(), $aag_region_id)
    {
        $conditions = array('Distributor.aag_region_id' => $aag_region_id);

        $garage_id = null;
        if (isset($condiciones_ajax['garage_id']) && !empty($condiciones_ajax['garage_id'])) {
            $garage_id = $condiciones_ajax['garage_id'];
        }
        $condiciones_ajax_array = array();
        if (isset($condiciones_ajax['name']) && !empty($condiciones_ajax['name'])) {
            $condiciones_ajax_array  = array(
                'OR' => array(
                    'Distributor.name LIKE' => '%' . $condiciones_ajax['name'] . '%',
                    'Distributor.account_number LIKE' => '%' . $condiciones_ajax['name'] . '%',
                )
            );
        }

        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageDistributor',
                        'table' => 'garages_distributors',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageDistributor.distributor_id = Distributor.id',
                            'GarageDistributor.garage_id' => $garage_id,
                        ),
                    )
                ),
                'conditions' => array(
                    $condiciones_ajax_array,
                    $conditions,
                    'GarageDistributor.garage_id IS NULL',
                    'Distributor.status' => ConstantsDistributorStatus::ACTIVE,
                ),
                'fields' => array(
                    'Distributor.id',
                    'Distributor.complete_search'
                ),
                'group' => array(
                    'Distributor.id'
                ),
            )
        );
    }

    public function obtenerPosiblesDistributorsReportingQuery($aag_region_id, $role_id, $conditions_ajax = array())
    {
        $conditions = array();
        if ($role_id != ConstantsRoles::SUPER_ADMIN) {
            $conditions = array('Distributor.aag_region_id' => $aag_region_id);
        }
        if (isset($conditions_ajax['name']) && !empty($condiciones_ajax['name'])) {
            $conditions_ajax = array(
                'OR' => array(
                    'Distributor.name LIKE' => '%' . $conditions_ajax['name'] . '%',
                    'Distributor.account_number LIKE' => '%' . $conditions_ajax['name'] . '%'
                )
            );
        }

        return $this->find(
            'all',
            array(
                'conditions' => array(
                    $conditions_ajax,
                    $conditions
                ),
                'fields' => array(
                    'Distributor.id',
                    'Distributor.complete_search'
                )
            )
        );
    }

    public function obtenerPosiblesDistributorsObjectivesAjaxRegion($data, $aag_region_id)
    {
        $distributors = array();

        $condiciones_ajax = array();
        $condiciones_ajax[] = array(
            'Distributor.status' => ConstantsDistributorStatus::ACTIVE,
        );
        if (isset($data['rsm_id']) && !empty($data['rsm_id'])) {
            $condiciones_ajax[] = array(
                'DistributorContactBdm.contact_id' => $data['rsm_id'],
            );

            $query = array(
                'joins' => array(
                    array(
                        'alias' => 'DistributorContactBdm',
                        'table' => 'distributors_contacts_bdm',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorContactBdm.distributor_id = Distributor.id',
                        ),
                    ),
                ),
                'conditions' => $condiciones_ajax,
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.account_number',
                    'Distributor.town'
                ),
                'group' => array(
                    'Distributor.id'
                ),
            );

            $distributors =  $this->find('all', $query);
        }

        $condiciones_ajax = array();
        $condiciones_ajax[] = array(
            'Distributor.status' => ConstantsDistributorStatus::ACTIVE,
        );
        if (isset($data['bdm_id']) && !empty($data['bdm_id'])) {
            $condiciones_ajax[] = array(
                'DistributorContactBdm.contact_id' => $data['bdm_id'],
            );
            $query = array(
                'joins' => array(
                    array(
                        'alias' => 'DistributorContactBdm',
                        'table' => 'distributors_contacts_bdm',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorContactBdm.distributor_id = Distributor.id',
                        ),
                    ),
                ),
                'conditions' => $condiciones_ajax,
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.account_number',
                    'Distributor.town'
                ),
                'group' => array(
                    'Distributor.id'
                ),
            );

            $distributors2 =  $this->find('all', $query);
            if ($distributors) {
                $distributors = array_intersect($distributors, $distributors2);
            } else {
                $distributors = $distributors2;
            }
        }

        $condiciones_ajax = array();
        $condiciones_ajax[] = array(
            'Distributor.status' => ConstantsDistributorStatus::ACTIVE,
        );
        if (isset($data['trading_group_id']) && !empty($data['trading_group_id'])) {
            $condiciones_ajax[] = array(
                'Distributor.trading_group_id' => $data['trading_group_id'],
            );

            $query = array(
                'conditions' => $condiciones_ajax,
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.account_number',
                    'Distributor.town'
                ),
                'group' => array(
                    'Distributor.id'
                ),
            );
            $distributors3 = $this->find('all', $query);

            if ($distributors) {
                $distributors = array_intersect($distributors, $distributors3);
            } else {
                $distributors = $distributors3;
            }
        }

        $condiciones_ajax = array();
        $condiciones_ajax[] = array(
            'Distributor.status' => ConstantsDistributorStatus::ACTIVE,
        );
        if (isset($data['customer_activity_id']) && !empty($data['customer_activity_id'])) {
            $condiciones_ajax[] = array(
                'DistributorCustomerActivity.customer_activity_id' => $data['customer_activity_id'],
            );

            $query = array(
                'joins' => array(
                    array(
                        'alias' => 'DistributorCustomerActivity',
                        'table' => 'distributors_customer_activities',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorCustomerActivity.distributor_id = Distributor.id',
                        ),
                    ),
                ),
                'conditions' => $condiciones_ajax,
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.account_number',
                    'Distributor.town'
                ),
                'group' => array(
                    'Distributor.id'
                ),
            );
            $distributors4 = $this->find('all', $query);

            if ($distributors) {
                $distributors = array_intersect($distributors, $distributors4);
            } else {
                $distributors = $distributors4;
            }
        }

        if (
            empty($data['rsm_id']) && !isset($data['rsm_id']) &&
            empty($data['bdm_id']) && !isset($data['bdm_id']) &&
            empty($data['trading_group_id']) && !isset($data['trading_group_id']) &&
            empty($data['customer_activity_id']) && !isset($data['customer_activity_id'])
        ) {
            $conditions = array('Distributor.aag_region_id' => $aag_region_id);

            $query = array(
                'conditions' => array(
                    'Distributor.status' => ConstantsDistributorStatus::ACTIVE
                ),
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.account_number',
                    'Distributor.town',
                    $conditions
                ),
                'group' => array(
                    'Distributor.id'
                ),
            );
            $distributors = $this->find('all', $query);
        }

        return $distributors;
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

    public function getDistributorNameById($distributorId, $aagRegionId)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'Distributor.id' => $distributorId,
                    'Distributor.aag_region_id' => $aagRegionId
                ),
                'fields' => array(
                    'Distributor.name',
                ),
                'group' => array(
                    'Distributor.name'
                ),
            )
        );
    }

    public function getDistributorsNameByIdArray($search)
    {
        $resultArray = array();

        if ($search['distributor_id'] != null) {
            foreach ($search['distributor_id'] as $distributor_id) {
                $query = $this->find('first', array(
                    'conditions' => array('Distributor.id' => $distributor_id),
                    'fields' => array('Distributor.id', 'Distributor.name'),
                ));

                if ($query) {
                    $distributor = $query['Distributor'];
                    $resultArray[$distributor['id']] = $distributor['name'];
                }
            }
        }

        return $resultArray;
    }

    public function getDistributorsNameByIdDistributor($distributor_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'Distributor.id' => $distributor_id
            )
        ));
    }

    public function getDistributorsByName($term, $aag_region_id)
    {
        $conditions_aag_region = array();
        $conditions_aag_region = array('Distributor.aag_region_id' => $aag_region_id);

        $resultArray = array();
        $distributors = $this->find('all', array(
            'conditions' => array(
                'Distributor.name LIKE' => '%' . $term . '%',
                $conditions_aag_region
            ),
            'fields' => array(
                'Distributor.id',
                'Distributor.complete_name'
            )
        ));


        if ($distributors) {
            foreach ($distributors as $distributor) {
                $resultArray[] = array(
                    'id' => $distributor['Distributor']['id'],
                    'text' => $distributor['Distributor']['complete_name'],
                );
            }
        }
        return $resultArray;
    }

    public function getDistributorsCompleteNamesByListOfIds(array $listOfIds, $aag_region_id)
    {
        $conditions = [];
        $conditions[] = array('Distributor.aag_region_id' => $aag_region_id);

        $resultArray = array();
        $distributors = $this->find('all', array(
            'conditions' => array(
                'Distributor.id' => $listOfIds,
                $conditions
            ),
            'fields' => array(
                'Distributor.id',
                'Distributor.complete_name'
            ),
        ));

        if ($distributors) {
            foreach ($distributors as $distributor) {
                $resultArray[] = array(
                    'id' => $distributor['Distributor']['id'],
                    'text' => $distributor['Distributor']['complete_name'],
                );
            }
        }
        return $resultArray;
    }

    public function getListByRegion($aag_region_id)
    {
        $conditions = array();
        $conditions = array('Distributor.aag_region_id' => $aag_region_id);

        return $this->find('list', array(
            'conditions' => $conditions,
            'fields' => array(
                'Distributor.id',
                'Distributor.complete_search',
            ),
            'order' => array(
                'Distributor.complete_search',
            )
        ));
    }

    public function findDitributorsPlanningVisits($aag_region_id)
    {
        $conditions = array(
            'Distributor.status' => ConstantsDistributorStatus::ACTIVE,
            'Distributor.latitude IS NOT NULL',
            'Distributor.longitude IS NOT NULL'
        );

        $conditions = array('Distributor.aag_region_id' => $aag_region_id);

        return $this->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'Distributor.id',
                'Distributor.latitude',
                'Distributor.longitude',
            )
        ));
    }

    public function getListByRegionCRM($aag_region_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'Distributor.aag_region_id' => $aag_region_id,
                'Distributor.status' => ConstantsDistributorStatus::ACTIVE,
            ),
            'fields' => array(
                'Distributor.id',
            ),
            'order' => array(
                'Distributor.complete_search',
            )
        ));
    }

    public function countListByRegionCRM($aag_region_id)
    {
        return $this->find('count', array(
            'conditions' => array(
                'Distributor.aag_region_id' => $aag_region_id,
                'Distributor.status' => ConstantsDistributorStatus::ACTIVE,
            ),
        ));
    }

    public function getListByRegionCRMConditions($aag_region_id, $conditions)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'DistributorContactBdm',
                    'table' => 'distributors_contacts_bdm',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'DistributorContactBdm.distributor_id = Distributor.id',
                    ),
                ),
                array(
                    'alias' => 'DistributorCustomerActivity',
                    'table' => 'distributors_customer_activities',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'DistributorCustomerActivity.distributor_id = Distributor.id',
                    ),
                ),
            ),
            'conditions' => array(
                'Distributor.aag_region_id' => $aag_region_id,
                'Distributor.status' => ConstantsDistributorStatus::ACTIVE,
                $conditions
            ),
            'fields' => array(
                'Distributor.id',
            ),
            'order' => array(
                'Distributor.complete_search',
            ),
            'group' => array(
                'Distributor.id',
            )
        ));
    }

    public function dynamicTypeDistributorsExportQuery($type, $aag_region_id, $conditions, $optional_join)
    {
        return $this->find($type, array(
            'conditions' => array(
                $conditions,
                'Distributor.aag_region_id' => $aag_region_id,
            ),
            'joins' => array(
                array(
                    'alias' => 'TradingGroup',
                    'table' => 'trading_groups',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Distributor.trading_group_id = TradingGroup.id',
                    ),
                ),
                array(
                    'alias' => 'AssociationType',
                    'table' => 'associations_types',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Distributor.association_type_id = AssociationType.id',
                    ),
                ),
                $optional_join,
            ),
            'fields' => array(
                'Distributor.id',
                'Distributor.name',
                'Distributor.head_office',
                'Distributor.postcode',
                'Distributor.town',
                'Distributor.phone',
                'Distributor.last_visit',
                'Distributor.MAMID',
                'Distributor.trading_group_id',
                'Distributor.account_number',
                'TradingGroup.id',
                'TradingGroup.name',
                'AssociationType.*',
            ),
            'order' => array(
                'Distributor.name',
            ),
            'group' => array(
                'Distributor.id',
            ),
        ));
    }
}
