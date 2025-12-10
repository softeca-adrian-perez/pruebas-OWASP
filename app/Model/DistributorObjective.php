<?php

class DistributorObjective extends AppModel
{

    public $useTable = 'distributors_objectives';

    public function conditions($fields)
    {
        $conditions = array();

        if (!empty($fields['distributor'])) {
            $conditions[] = $this->_conditionDistributor($fields['distributor']);
        }

        if (!empty($fields['objectives'])) {
            $conditions[] = $this->_conditionObjectives($fields['objectives']);
        }

        if (!empty($fields['date_from'])) {
            $conditions[] = $this->_conditionDateFrom($fields['date_from']);
        }

        if (!empty($fields['date_to'])) {
            $conditions[] = $this->_conditionDateTo($fields['date_to']);
        }

        if (!empty($fields['bdm'])) {
            $conditions[] = $this->_conditionBdm($fields['bdm']);
        }

        return $conditions;
    }

    private function _conditionDistributor($distributor)
    {
        return array('DistributorObjective.distributor_id' => $distributor);
    }

    private function _conditionObjectives($objectives)
    {
        return array('DistributorObjective.objective_id' => $objectives);
    }

    private function _conditionDateFrom($from_date)
    {
        $from_date = Fecha::toFormatoBd($from_date);
        return array('DistributorObjective.from >=' => $from_date);
    }

    private function _conditionDateTo($to_date)
    {
        $to_date = Fecha::toFormatoBd($to_date);
        return array('DistributorObjective.to <=' => $to_date);
    }

    private function _conditionBdm($bdm)
    {
        return array('DistributorContactBdm.contact_id' => $bdm);
    }

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    private $_queries = array(
        'search' => array(
            'joins' => array(
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = DistributorObjective.distributor_id',
                    ),
                ),
                array(
                    'alias' => 'AppointmentObjective',
                    'table' => 'appointments_objectives',
                    'type' => 'INNER',
                    'conditions' => array(
                        'AppointmentObjective.id = DistributorObjective.objective_id',
                    ),
                ),
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Distributor.id = Appointment.distributor_id',
                    ),
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Appointment.user_assigned_id = User.id',
                    ),
                ),
                array(
                    'alias' => 'DistributorContactBdm',
                    'table' => 'distributors_contacts_bdm',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = DistributorContactBdm.distributor_id',
                    ),
                ),
            ),
            'fields' => array(
                'DistributorObjective.id',
                'AppointmentObjective.name',
                'Distributor.name',
                'Distributor.account_number',
                'Distributor.town',
                'DistributorObjective.from',
                'DistributorObjective.to',
                'User.name',
                'User.surname'
            ),
            'group' => array(
                'DistributorObjective.id',
            ),
            'order' => 'User.name asc, AppointmentObjective.name, Distributor.name asc'
        ),
        'search_ids' => array(
            'joins' => array(
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = DistributorObjective.distributor_id',
                    ),
                ),
                array(
                    'alias' => 'AppointmentObjective',
                    'table' => 'appointments_objectives',
                    'type' => 'INNER',
                    'conditions' => array(
                        'AppointmentObjective.id = DistributorObjective.objective_id',
                    ),
                ),
            ),
            'fields' => array(
                'DistributorObjective.id',
            ),
        ),
    );

    public function add($distributor_id, $objective_id, $from = null, $to = null)
    {

        $fields = array(
            'DistributorObjective' => array(
                'distributor_id',
                'objective_id',
                'from',
                'to',
                'creation_date',
            )
        );

        $distributor_objective['DistributorObjective']['distributor_id'] = $distributor_id;
        $distributor_objective['DistributorObjective']['objective_id'] = $objective_id;
        $distributor_objective['DistributorObjective']['from'] = $from ? Fecha::toFormatoBd($from) : null;
        $distributor_objective['DistributorObjective']['to'] = $to ? Fecha::toFormatoBd($to) : null;
        $distributor_objective['DistributorObjective']['creation_date'] = date('Y/m/d H:i:s');

        $this->create();
        $distributor_objective_bd = $this->guardar($distributor_objective, $fields);

        if (!$distributor_objective_bd) {
            return false;
        }

        $this->commit();
        return $distributor_objective_bd;
    }

    public function delete_objective($id)
    {
        if ($this->eliminar($id)) {
            return true;
        }
        return false;
    }

    public function getObjectives()
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'AppointmentObjective',
                        'table' => 'appointments_objectives',
                        'type' => 'INNER',
                        'conditions' => array(
                            'AppointmentObjective.id = DistributorObjective.objective_id',
                        ),
                    ),
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Distributor.id = DistributorObjective.distributor_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'AppointmentObjective.management' => ConstantsBooleans::YES
                ),
                'fields' => array(
                    'DistributorObjective.*',
                    'AppointmentObjective.*',
                    'Distributor.*',
                ),
                'order' => array('AppointmentObjective.name ASC')
            )
        );
    }

    public function getObjectivesByDistributor($distributor_id, $user)
    {
        $query = array(
            'joins' => array(
                array(
                    'alias' => 'AppointmentObjective',
                    'table' => 'appointments_objectives',
                    'type' => 'INNER',
                    'conditions' => array(
                        'AppointmentObjective.id = DistributorObjective.objective_id',
                    ),
                ),
            ),
            'conditions' => array(
                'DistributorObjective.distributor_id' => $distributor_id,
                'OR' => array(
                    array(
                        'DistributorObjective.from' => null,
                        'DistributorObjective.to' => null
                    ),
                    array(
                        'DistributorObjective.from <=' => date('Y-m-d'),
                        'DistributorObjective.to' => null
                    ),
                    array(
                        'DistributorObjective.from <=' => date('Y-m-d'),
                        'DistributorObjective.to >=' => date('Y-m-d')
                    ),
                )
            ),
            'fields' => array(
                'AppointmentObjective.id',
                'AppointmentObjective.name',
            ),
            'order' => array('AppointmentObjective.name ASC')
        );

        if ($user['Role']['id'] == ConstantsRoles::BDM_TG) {
            $query['conditions'][] = array('AppointmentObjective.tg_management' => ConstantsBooleans::YES);
        } elseif ($user['Role']['id'] == ConstantsRoles::GPC_LOGISTICS_BDM) {
            $query['conditions'][] = array('AppointmentObjective.gpc_management' => ConstantsBooleans::YES);
        }

        return $this->find('list', $query);
    }

    public function deleteDistributorsObjectives()
    {
        $query = array(
            'conditions' => array(
                'DistributorObjective.from !=' => null,
                'DistributorObjective.to !=' => null
            ),
            'fields' => array(
                'DistributorObjective.*',
            ),
        );

        $objectives = $this->find('all', $query);
        foreach ($objectives as $objective) {
            if (strtotime($objective['DistributorObjective']['to']) < strtotime(date('Y-m-d'))) {
                $this->eliminar($objective['DistributorObjective']['id']);
            }
        }
    }

    public function getAllFromDistributorObjective()
    {
        return $this->find(
            'all',
            array(
                'fields' => array(
                    'DistributorObjective.*',
                ),
                'order' => 'DistributorObjective.id ASC',
            )
        );
    }

    public function getAllFromDistributorObjectiveWhereFromAndToExists()
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'DistributorObjective.from !=' => null,
                    'DistributorObjective.to !=' => null
                ),
                'fields' => array(
                    'DistributorObjective.*',
                ),
                'order' => 'DistributorObjective.id ASC',
            )
        );
    }

    public function search_limit_list()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
            ),
            'limit' => ConstantsObjectives::LIMIT_DELETE
        ));
    }

    public function getLastDistributorObjective()
    {
        return $this->find(
            'first',
            array(
                'fields' => array(
                    'DistributorObjective.*',
                ),
                'order' => 'DistributorObjective.id DESC',
            )
        );
    }

    public function getFirstDistributorObjective()
    {
        return $this->find(
            'first',
            array(
                'fields' => array(
                    'DistributorObjective.*',
                ),
                'order' => 'DistributorObjective.id ASC',
            )
        );
    }

    public function search_list_by_last_id($maxId)
    {
        return $this->find('list', array(
            'fields' => array('id'),
            'conditions' => array(
                'id <=' => $maxId,
            ),
            'limit' => ConstantsObjectives::LIMIT_DELETE
        ));
    }

    /**
     * Gets the number of records between the ids passed by parameter.
     */
    public function getDistributorObjectivesCountBetweenIds($firstId, $lastId)
    {
        return $this->find(
            'count',
            array(
                'fields' => array('id'),
                'conditions' => array(
                    'id >=' => $firstId,
                    'id <=' => $lastId
                )
            )
        );
    }
}
