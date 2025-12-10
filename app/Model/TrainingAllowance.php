<?php
class TrainingAllowance extends AppModel
{
    public $useTable = 'trainings_allowances';
    public $virtualFields = array(
        'complete_date' => 'CONCAT(
            IFNULL(DATE_FORMAT(TrainingAllowance.start_date, "%d/%m/%Y"), ""),
            " - ",
            IFNULL(DATE_FORMAT(TrainingAllowance.end_date, "%d/%m/%Y"), "")
        )',
    );

    public function add($trainingAllowance)
    {
        $fields = array(
            'TrainingAllowance' => array(
                'garage_network_id',
                'start_date',
                'end_date',
                'is_actual',
                'creation_date'
            )
        );

        $trainingAllowance['TrainingAllowance']['creation_date'] = date('Y-m-d H:i:s');

        $this->create();
        if ($this->guardar($trainingAllowance, $fields)) {
            return $trainingAllowance;
        }
    }

    public function edit($trainingAllowance)
    {
        $fields = array(
            'TrainingAllowance' => array(
                'garage_network_id',
                'start_date',
                'end_date',
                'is_actual',
                'modification_date'
            )
        );

        $trainingAllowance['TrainingAllowance']['modification_date'] = date('Y-m-d H:i:s');

        return $this->guardar($trainingAllowance, $fields);
    }

    public function searchList()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'complete_date'
            ),
            'order' => array(
                'start_date DESC'
            ),
            'group' => array(
                'complete_date'
            )
        ));
    }

    public function updateAllowance7days()
    {
        $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
        $trainingsAllowances = $this->find(
            'all',
            array(
                'conditions' => array(
                    'TrainingAllowance.end_date' => date('Y-m-d', strtotime('+7 days')),
                ),
                'fields' => array(
                    'TrainingAllowance.*'
                ),
            )
        );
        foreach ($trainingsAllowances as $trainingAllowance) {
            $garageNetworkId = $trainingAllowance['TrainingAllowance']['garage_network_id'];

            $trainingAllowanceTmp = array();
            $trainingAllowanceTmp['TrainingAllowance']['garage_network_id'] = $garageNetworkId;

            $startDate = date('Y-m-d', strtotime($trainingAllowance['TrainingAllowance']['start_date'] . ' +1 year'));
            $trainingAllowanceTmp['TrainingAllowance']['start_date'] = $startDate;

            if (date('m-d', strtotime($trainingAllowance['TrainingAllowance']['end_date'])) == '02-29') {
                $trainingAllowanceTmp['TrainingAllowance']['end_date'] = date('Y-m-d', strtotime($trainingAllowance['TrainingAllowance']['end_date'] . ' +1 year -1 day'));
            } else {
                $trainingAllowanceTmp['TrainingAllowance']['end_date'] = date('Y-m-d', strtotime($trainingAllowance['TrainingAllowance']['end_date'] . ' +1 year'));
            }

            // if allowance doesn't exists, it's created
            $existingTrainingAllowance = $this->findByGarageNetworkIdAndStartDate($garageNetworkId, $startDate);
            if (!$existingTrainingAllowance) {
                $this->add($trainingAllowanceTmp);
            }
        }
    }

    public function getAllowanceFromGarageNetworkId($garage_network_id)
    {
        return
            $this->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = TrainingAllowance.garage_network_id'
                            )
                        )
                    ),
                    'conditions' => array(
                        'GarageNetwork.id' => $garage_network_id
                    ),
                    'fields' => array(
                        'TrainingAllowance.*'
                    ),
                    'order' => 'TrainingAllowance.start_date DESC'
                )
            );
    }

    public function getAllAllowanceFromGarageId($garage_id)
    {
        return
            $this->find(
                'all',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = TrainingAllowance.garage_network_id'
                            )
                        ),
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Network.id = GarageNetwork.network_id',
                                'Network.id' => array(NETWORK_ID_AUTOCARE, NETWORK_ID_UNITED_GARAGE_SERVICE)
                            )
                        )
                    ),
                    'conditions' => array(
                        'GarageNetwork.garage_id' => $garage_id
                    ),
                    'fields' => array(
                        'TrainingAllowance.id',
                        'TrainingAllowance.complete_date'
                    ),
                    'order' => 'TrainingAllowance.start_date DESC'
                )
            );
    }

    public function getListAllowanceFromGarageId($garage_id)
    {
        return
            $this->find(
                'list',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = TrainingAllowance.garage_network_id'
                            )
                        ),
                        array(
                            'alias' => 'Network',
                            'table' => 'networks',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Network.id = GarageNetwork.network_id',
                                'Network.id' => array(NETWORK_ID_AUTOCARE, NETWORK_ID_UNITED_GARAGE_SERVICE)
                            )
                        )
                    ),
                    'conditions' => array(
                        'GarageNetwork.garage_id' => $garage_id,
                    ),
                    'fields' => array(
                        'TrainingAllowance.id',
                        'TrainingAllowance.complete_date',
                    ),
                    'order' => 'TrainingAllowance.start_date DESC'
                )
            );
    }

    public function getData()
    {
        return $this->find(
            'all',
            array(
                'fields' => array(
                    'id',
                    'complete_date'
                ),
                'order' => array(
                    'start_date DESC'
                ),
                'group' => array(
                    'complete_date'
                )
            )
        );
    }

    public function getAllowanceFromGarageNetworkIdAndDates($garage_network_id, $date)
    {
        return
            $this->find(
                'first',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = TrainingAllowance.garage_network_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'GarageNetwork.id' => $garage_network_id,
                        'AND' => array(
                            'TrainingAllowance.start_date <=' => $date,
                            'TrainingAllowance.end_date >=' => $date,
                        )
                    ),
                    'fields' => array(
                        'TrainingAllowance.*',
                    ),
                    'order' => 'TrainingAllowance.start_date DESC',
                )
            );
    }

    public function getAllowanceFromGarageNetworkIdFirst($garage_network_id)
    {
        return
            $this->find(
                'first',
                array(
                    'joins' => array(
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.id = TrainingAllowance.garage_network_id'
                            )
                        )
                    ),
                    'conditions' => array(
                        'GarageNetwork.id' => $garage_network_id
                    ),
                    'fields' => array(
                        'TrainingAllowance.*'
                    ),
                    'order' => 'TrainingAllowance.start_date DESC'
                )
            );
    }

    public function getAllSpentSumsList()
    {
        $results = $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TrainingCreditNetwork',
                        'table' => 'trainings_credits_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'TrainingCreditNetwork.training_allowance_id = TrainingAllowance.id'
                        )
                    )
                ),
                'fields' => array(
                    'TrainingAllowance.id',
                    'SUM(TrainingCreditNetwork.credit_spent) as credit_spent'
                ),
                'group' => 'TrainingAllowance.id'
            )
        );

        $sums = [];
        foreach ($results as $result) {
            $sums[$result['TrainingAllowance']['id']] = $result[0]['credit_spent'];
        }

        return $sums;
    }

    public function getAllGivenSumsList()
    {
        $results = $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TrainingCreditNetwork',
                        'table' => 'trainings_credits_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'TrainingCreditNetwork.training_allowance_id = TrainingAllowance.id'
                        )
                    )
                ),
                'fields' => array(
                    'TrainingAllowance.id',
                    'SUM(TrainingCreditNetwork.credit_given) as credit_given'
                ),
                'group' => 'TrainingAllowance.id'
            )
        );

        $sums = [];
        foreach ($results as $result) {
            $sums[$result['TrainingAllowance']['id']] = $result[0]['credit_given'];
        }

        return $sums;
    }

    public function getIdByGarageNetworkId($garage_network_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'TrainingAllowance.garage_network_id' => $garage_network_id,
                    'TrainingAllowance.is_actual' => ConstantsBooleans::YES
                )
            )
        );
    }

    public function updateActualAllowance()
    {
        $today = date('Y-m-d');

        $allowances = $this->find('all', array(
            'conditions' => array(
                'TrainingAllowance.is_actual' => ConstantsBooleans::YES,
                'OR' => array(
                    'TrainingAllowance.start_date >' => $today,
                    'TrainingAllowance.end_date <' => $today
                )
            ),
            'fields' => array(
                'TrainingAllowance.id',
                'TrainingAllowance.garage_network_id',
                'TrainingAllowance.is_actual',
                'TrainingAllowance.start_date',
                'TrainingAllowance.end_date'
            )
        ));

        $dataSource = $this->getDataSource();
        $dataSource->begin();

        try {
            foreach ($allowances as $allowance) {
                $allowance['TrainingAllowance']['is_actual'] = ConstantsBooleans::NO;
                $this->edit($allowance);
            }

            $activeAllowances = $this->find('all', array(
                'conditions' => array(
                    'TrainingAllowance.is_actual' => ConstantsBooleans::NO,
                    'TrainingAllowance.start_date <=' => $today,
                    'TrainingAllowance.end_date >=' => $today
                ),
                'fields' => array(
                    'TrainingAllowance.id',
                    'TrainingAllowance.garage_network_id',
                    'TrainingAllowance.is_actual',
                    'TrainingAllowance.start_date',
                    'TrainingAllowance.end_date'
                ),
                'group' => 'TrainingAllowance.garage_network_id',
                'order' => 'TrainingAllowance.start_date ASC'
            ));

            foreach ($activeAllowances as $activeAllowance) {
                $existingActive = $this->find('count', array(
                    'conditions' => array(
                        'TrainingAllowance.garage_network_id' => $activeAllowance['TrainingAllowance']['garage_network_id'],
                        'TrainingAllowance.is_actual' => ConstantsBooleans::YES,
                        'TrainingAllowance.id !=' => $activeAllowance['TrainingAllowance']['id']
                    )
                ));

                $activeAllowance['TrainingAllowance']['is_actual'] = $existingActive == ConstantsBooleans::NO ? ConstantsBooleans::YES : ConstantsBooleans::NO;
                $this->edit($activeAllowance);
            }

            $dataSource->commit();
        } catch (Exception $e) {
            $dataSource->rollback();
            throw $e;
        }

        return true;
    }

    public function fillMissingAllowances($trainingAllowanceId)
    {
        $trainingAllowance = $this->findById($trainingAllowanceId);

        $existingAllowances = $this->find('all', array(
            'conditions' => array(
                'TrainingAllowance.garage_network_id' => $trainingAllowance['TrainingAllowance']['garage_network_id'],
                'TrainingAllowance.id !=' => $trainingAllowanceId
            ),
            'order' => array('TrainingAllowance.start_date' => 'DESC')
        ));

        $latestStartDate = !empty($existingAllowances) ? $existingAllowances[0]['TrainingAllowance']['start_date'] : null;

        if ($latestStartDate && $latestStartDate < $trainingAllowance['TrainingAllowance']['start_date']) {
            $this->createAllowancesBetweenDates($latestStartDate, $trainingAllowance['TrainingAllowance']['start_date'], $trainingAllowance['TrainingAllowance']['garage_network_id']);
        }
    }

    private function createAllowancesBetweenDates($latestStartDate, $newIdDate, $garageNetworkId)
    {
        $startYear = date('Y', strtotime($latestStartDate)) + 1;
        $endYear = date('Y', strtotime($newIdDate));
        $monthDay = date('m-d', strtotime($latestStartDate));

        for ($year = $startYear; $year < $endYear; $year++) {
            $startDate = date('Y-m-d', strtotime($year . '-' . $monthDay));
            $endDate = date('Y-m-d', strtotime(($year + 1) . '-' . $monthDay . '-1 day'));

            // if allowance doesn't exists, it's created
            $existingTrainingAllowance = $this->findByGarageNetworkIdAndStartDate($garageNetworkId, $startDate);
            if (!$existingTrainingAllowance) {
                $this->add(array(
                    'garage_network_id' => $garageNetworkId,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ));
            }
        }
    }

    public function checkAllowanceToDelegate($garage_network_id, $date_from)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'TrainingAllowance.garage_network_id' => $garage_network_id,
                    'TrainingAllowance.start_date <=' => $date_from,
                    'TrainingAllowance.end_date >=' => $date_from
                )
            )
        );
    }

    public function getListAllowanceFromGarageNetworkId($garageNetworkId)
    {
        return
            $this->find(
                'all',
                array(
                    'conditions' => array(
                        'TrainingAllowance.garage_network_id' => $garageNetworkId
                    ),
                    'fields' => array(
                        'TrainingAllowance.id',
                        'TrainingAllowance.start_date',
                        'TrainingAllowance.end_date'
                    ),
                    'order' => 'TrainingAllowance.start_date DESC'
                )
            );
    }

    public function getListOfAllowancesIdsByDateTo($dateTo)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'TrainingAllowance.end_date <' => $dateTo
                ),
                'fields' => array(
                    'TrainingAllowance.id'
                )
            )
        );
    }
}
