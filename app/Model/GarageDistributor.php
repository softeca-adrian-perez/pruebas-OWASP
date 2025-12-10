<?php

class GarageDistributor extends AppModel
{

    public $useTable = 'garages_distributors';

    public $hasOne = array(
        'Garage',
        'Distributor',
    );

    public $validate = array(
        'distributor_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_distributor',
            ),
        ),
    );

    private $_queries = array(
        'Search' => array(
            'joins' => array(
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageDistributor.garage_id = Garage.id',
                    ),
                ),
            ),
            'fields' => array(
                'Garage.id',
                'Garage.name',
                'Garage.province_id',
                'Garage.town',
                'Garage.phone',
                'Garage.g_number_id',
            ),
            'order' => 'Garage.name asc'
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['name'])) {
            $conditions[] = $this->_conditionName($fields['name']);
        }
        if (!empty($fields['province_id'])) {
            $conditions[] = $this->_conditionProvince($fields['province_id']);
        }
        if (!empty($fields['town'])) {
            $conditions[] = $this->_conditionTown($fields['town']);
        }
        if (!empty($fields['phone'])) {
            $conditions[] = $this->_conditionPhone($fields['town']);
        }
        if (!empty($fields['g_number_id'])) {
            $conditions[] = $this->_conditionGNumber($fields['g_number_id']);
        }

        return $conditions;
    }

    private function _conditionName($name)
    {
        $garages = $this->Garage->find('all', array(
            'conditions' => array(
                'name LIKE' => '%' . $name . '%'
            ),
            'fields' => array(
                'id'
            )
        ));
        return array('GarageDistributor.garage_id' => Hash::extract($garages, '{n}.Garage.id'));
    }

    private function _conditionProvince($province_id)
    {
        $garages = $this->Garage->find('all', array(
            'conditions' => array(
                'province_id' => $province_id
            ),
            'fields' => array(
                'id'
            )
        ));
        return array('GarageDistributor.garage_id' => Hash::extract($garages, '{n}.Garage.id'));
    }

    private function _conditionTown($town)
    {
        $garages = $this->Garage->find('all', array(
            'conditions' => array(
                'town LIKE' => '%' . $town . '%'
            ),
            'fields' => array(
                'id'
            )
        ));
        return array('GarageDistributor.garage_id' => Hash::extract($garages, '{n}.Garage.id'));
    }

    private function _conditionPhone($phone)
    {
        $garages = $this->Garage->find('all', array(
            'conditions' => array(
                'phone LIKE' => '%' . $phone . '%'
            ),
            'fields' => array(
                'id'
            )
        ));
        return array('GarageDistributor.garage_id' => Hash::extract($garages, '{n}.Garage.id'));
    }

    private function _conditionGNumber($g_number_id)
    {
        $garages = $this->Garage->find('all', array(
            'conditions' => array(
                'g_number_id LIKE' => '%' . $g_number_id . '%'
            ),
            'fields' => array(
                'id'
            )
        ));
        return array('GarageDistributor.garage_id' => Hash::extract($garages, '{n}.Garage.id'));
    }

    public function new_garage_distributor($garage_distributor)
    {
        $fields = array(
            'GarageDistributor' => array(
                'garage_id',
                'distributor_id',
                'order',
            )
        );
        $this->create();
        $garage_distributor_bd = $this->guardar($garage_distributor, $fields);
        if (!$garage_distributor_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function edit_garage_distributor($garage_distributor)
    {
        $fields = array(
            'GarageDistributor' => array(
                'id',
                'garage_id',
                'distributor_id',
                'order',
            )
        );
        $garage_distributor_bd = $this->guardar($garage_distributor, $fields);
        if (!$garage_distributor_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function change_order($garage_distributor)
    {
        $fields = array(
            'GarageDistributor' => array(
                'id',
                'principal',
                'order',
            )
        );
        if ($garage_distributor['GarageDistributor']['order'] == 1) {
            $garage_distributor['GarageDistributor']['principal'] = ConstantsBooleans::YES;
        } else {
            $garage_distributor['GarageDistributor']['principal'] = ConstantsBooleans::NO;
        }

        $garage_distributor_bd = $this->guardar($garage_distributor, $fields);

        if (!$garage_distributor_bd) {
            return false;
        }

        $this->commit();
        return $garage_distributor_bd;
    }

    public function getAllByGarageIdByOrder($garage_id)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'garage_id' => $garage_id
                ),
                'order' => array('order'),
            )
        );
    }

    public function getPrincipalDistributorByGarageId($garage_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'GarageDistributor.garage_id' => $garage_id,
                    'GarageDistributor.principal' => ConstantsBooleans::YES,
                ),
                'fields' => array(
                    'GarageDistributor.*'
                )
            )
        );
    }

    public function getAllByGarageId($garage_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageDistributor.distributor_id = Distributor.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageDistributor.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'GarageDistributor.*',
                    'Distributor.*'
                )
            )
        );
    }

    public function setNotPrincipalDistributors($garage_distributor)
    {
        $fields = array(
            'GarageWebsite' => array(
                'distributor_id',
                'principal',
            )
        );
        $garage_distributor['GarageDistributor']['principal'] = ConstantsBooleans::NO_ACTIVE;

        $garage_distributor_bd = $this->guardar($garage_distributor, $fields);

        if (!$garage_distributor_bd) {
            return false;
        }

        return $garage_distributor_bd;
    }

    public function getByLimitGaragesByDistributorId($distributor_id, $limit)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageDistributor.garage_id = Garage.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageDistributor.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'Garage.id',
                    'Garage.name',
                    'Garage.province_id',
                    'Garage.town',
                    'Garage.phone',
                    'Garage.g_number_id',
                ),
                'order' => array(
                    'Garage.name'
                ),
                'limit' => $limit
            )
        );
    }

    public function countAllGaragesByDistributorId($distributor_id)
    {
        return $this->find(
            'count',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GarageDistributor.garage_id = Garage.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageDistributor.distributor_id' => $distributor_id,
                ),
                'fields' => array(
                    'Garage.id',
                ),
                'order' => array(
                    'Garage.name'
                ),
            )
        );
    }

    public function findDistributorsExport($garage_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
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
                    'GarageDistributor.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Distributor.id',
                    'Distributor.name',
                    'Distributor.account_number',
                    // 'Distributor.MAMID',
                    // 'Distributor.reg_number',
                    'GarageDistributor.principal',
                ),
            )
        );
    }

    public function createGarageDistributor($garage_json, $garage_id, &$errors)
    { //The data comes from a Json left on the UK server

        if ($garage_json['Garage']['MemberNbrCRM']) {
            $distributor = ClassRegistry::init('Distributor');
            $distributor_tmp = $distributor->findByAccountNumber($garage_json['Garage']['MemberNbrCRM']);
            if ($distributor_tmp) {
                $garage_distributor_tmp = array(
                    'GarageDistributor' => array(
                        'garage_id' => $garage_id,
                        'distributor_id' => $distributor_tmp['Distributor']['id'],
                        'principal' => ConstantsBooleans::YES,
                    )
                );

                $this->create();
                if (!$this->save($garage_distributor_tmp)) {
                    CakeLog::write('updates', 'The link to the distributor could not be created' . PHP_EOL);
                    $errors['The link to the distributor could not be created'] = translateDataErrors($this->validationErrors);
                }
                return true;
            }
        } else {
            return true;
        }
    }

    public function createCustomerDistributor($customer_json, $customer_id)
    { //The data comes from a Json left on the GERMANY server

        if ($customer_json['ResponsibilityCenter']) {
            $distributor = ClassRegistry::init('Distributor');
            $distributor_tmp = $distributor->findByAccountNumber($customer_json['ResponsibilityCenter']);

            if ($distributor_tmp) {
                $garage_distributor_tmp = array(
                    'GarageDistributor' => array(
                        'garage_id' => $customer_id,
                        'distributor_id' => $distributor_tmp['Distributor']['id'],
                        'principal' => ConstantsBooleans::YES,
                    )
                );

                $this->create();
                if (!$this->save($garage_distributor_tmp)) {
                    CakeLog::write('updates-germany', 'The link to the distributor could not be created' . PHP_EOL);
                }
                return true;
            }
        } else {
            return true;
        }
    }

    public function updateGarageDistributor($garage_json, $exist_garage_id, &$errors)
    { //The data comes from a Json left on the UK server
        $garage_distributors_exist = $this->findListByGarageId($exist_garage_id);

        if ($garage_json['Garage']['MemberNbrCRM']) {
            $this->Distributor = ClassRegistry::init('Distributor');
            $distributor_tmp = $this->Distributor->findByAccountNumber($garage_json['Garage']['MemberNbrCRM']);
            if ($distributor_tmp) {
                if (!in_array($distributor_tmp['Distributor']['id'], $garage_distributors_exist)) {
                    $garage_distributor_tmp = array(
                        'GarageDistributor' => array(
                            'garage_id' => $exist_garage_id,
                            'distributor_id' => $distributor_tmp['Distributor']['id'],
                            'principal' => ConstantsBooleans::YES,
                        )
                    );
                    $this->create();
                    if (!$this->save($garage_distributor_tmp)) {
                        CakeLog::write('updates', 'The link to the distributor could not be created' . PHP_EOL);
                        $errors['The link to the distributor could not be created'] = translateDataErrors($this->validationErrors);
                    }
                } else {
                    $key = array_search($distributor_tmp['Distributor']['id'], $garage_distributors_exist);
                    unset($garage_distributors_exist[$key]);
                }
            }
        }

        //remove garages distributors that have not arrived through JSON
        foreach ($garage_distributors_exist as $key => $garage_distributor_id) {
            $this->delete($key);
        }

        return true;
    }

    public function updateCustomerDistributor($garage_json, $exist_customer_id)
    { //The data comes from a Json left on the GERMANY server
        $customer_distributors_exist = $this->findListByGarageId($exist_customer_id);

        if ($garage_json['ResponsibilityCenter']) {
            $this->Distributor = ClassRegistry::init('Distributor');
            $distributor_tmp = $this->Distributor->findByAccountNumber($garage_json['ResponsibilityCenter']);

            if ($distributor_tmp) {
                if (!in_array($distributor_tmp['Distributor']['id'], $customer_distributors_exist)) {
                    $customer_distributor_tmp = array(
                        'GarageDistributor' => array(
                            'garage_id' => $exist_customer_id,
                            'distributor_id' => $distributor_tmp['Distributor']['id'],
                            'principal' => ConstantsBooleans::YES,
                        )
                    );
                    $this->create();
                    if (!$this->save($customer_distributor_tmp)) {
                        CakeLog::write('updates-germany', 'The link to the distributor could not be created' . PHP_EOL);
                    }
                } else {
                    $key = array_search($distributor_tmp['Distributor']['id'], $customer_distributors_exist);
                    unset($customer_distributors_exist[$key]);
                }
            }
        }

        //remove customer distributors that have not arrived through JSON
        foreach ($customer_distributors_exist as $key => $garage_distributor_id) {
            $this->delete($key);
        }

        return true;
    }

    public function createRepDistributor($garage, $garage_id)
    { //The data comes from a Json left on the FR server
        if ($garage['id_groupe_distributeur'] != '' || $garage['id_groupe_distributeur'] != null) {
            $this->Distributor = ClassRegistry::init('GarageDistributor');
            $distributor_bd = $this->Distributor->findById($garage['id_groupe_distributeur']);

            if (!empty($distributor_bd)) {
                $garage_distributor_tmp = array(
                    'GarageDistributor' => array(
                        'garage_id' => $garage_id,
                        'distributor_id' => $garage['id_groupe_distributeur']
                    )
                );

                $this->create();
                if (!$this->save($garage_distributor_tmp)) {
                    CakeLog::write('updates-france', 'The Garage Distributor could not be created.' . PHP_EOL);
                }
            }
        }
        return true;
    }

    public function updateRepDistributor($garage, $garage_exist)
    { //The data comes from a Json left on the FR server
        if (isset($garage['id_groupe_distributeur'])) {
            if ($garage['id_groupe_distributeur'] != '' || $garage['id_groupe_distributeur'] != null) {
                $this->Distributor = ClassRegistry::init('GarageDistributor');
                $distributor_bd = $this->Distributor->findById($garage['id_groupe_distributeur']);
                $garage_distributor_exist = $this->findByGarageIdAndDistributorId($garage_exist['Garage']['id'], $garage['id_groupe_distributeur']);

                if (!empty($distributor_bd) && !$garage_distributor_exist) {
                    $garage_distributor_tmp = array(
                        'GarageDistributor' => array(
                            'garage_id' => $garage_exist['Garage']['id'],
                            'distributor_id' => $garage['id_groupe_distributeur']
                        )
                    );

                    $this->create();
                    if (!$this->save($garage_distributor_tmp)) {
                        CakeLog::write('updates-france', 'The Garage Distributor could not be created.' . PHP_EOL);
                    }
                }
            }
        }

        return true;
    }



    public function findListByGarageId($garage_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'GarageDistributor.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'GarageDistributor.distributor_id'
                ),
            )
        );
    }

    public function getPaginationCountGaragesAssociated($conditions)
    {
        return $this->find('count', (array(
            'joins' => array(
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageDistributor.garage_id = Garage.id',
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
                array(
                    'alias' => 'Network',
                    'table' => 'networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Network.id = GarageNetwork.network_id',
                    ),
                ),
            ),
            'conditions' => $conditions,
            'fields' => 'DISTINCT(Garage.id)',
            'group' => 'Garage.id'
        )));
    }
}
