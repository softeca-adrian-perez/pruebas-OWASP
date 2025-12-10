<?php

class DebriefTaskDistributor extends AppModel
{
    public $useTable = 'debrief_tasks_distributors';

    public function add($distributors, $debrief_task_id)
    {
        foreach ($distributors as $distributor_id) {
            $debrief_task_distributor = array(
                'DebriefTaskDistributor' => array(
                    'debrief_task_id' => $debrief_task_id,
                    'distributor_id' => $distributor_id,
                )
            );
            $this->begin();
            if ($this->saveMany($debrief_task_distributor)) {
                $this->commit();
            }
        }
    }

    public function remove($debrief_task_id)
    {
        $debrief_tasks_distributors_bd = $this->findAllByDebriefTaskId($debrief_task_id);
        foreach ($debrief_tasks_distributors_bd as $debrief_task_distributor_bd) {
            $this->delete($debrief_task_distributor_bd['DebriefTaskDistributor']['id']);
        }
        $this->commit();
    }

    /**
     * Get first DebriefTaskDistributor by DebriefTask ID.
     */
    public function getFirstByDebriefTaskId($debrief_task_id)
    {
        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = DebriefTaskDistributor.distributor_id'
                    ),
                ),
            ),
            'conditions' => array(
                'DebriefTaskDistributor.debrief_task_id' => $debrief_task_id
            ),
            'fields' => array(
                'Distributor.name'
            )
        ));
    }

    public function getListByDebriefTaskId($debrief_task_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = DebriefTaskDistributor.distributor_id'
                    ),
                ),
            ),
            'conditions' => array(
                'DebriefTaskDistributor.debrief_task_id' => $debrief_task_id
            ),
            'fields' => array(
                'Distributor.id',
                'Distributor.name'
            )
        ));
    }
}
