<?php

class DebriefTaskGarage extends AppModel
{
    public $useTable = 'debrief_tasks_garages';

    public function add($garages, $debrief_task_id)
    {
        foreach ($garages as $garage_id) {
            $debrief_task_garage = array(
                'DebriefTaskGarage' => array(
                    'debrief_task_id' => $debrief_task_id,
                    'garage_id' => $garage_id,
                )
            );
            $this->begin();
            if ($this->saveMany($debrief_task_garage)) {
                $this->commit();
            }
        }
    }

    public function remove($debrief_task_id)
    {
        $debrief_tasks_garages_bd = $this->findAllByDebriefTaskId($debrief_task_id);
        foreach ($debrief_tasks_garages_bd as $debrief_task_garage_bd) {
            $this->delete($debrief_task_garage_bd['DebriefTaskGarage']['id']);
        }
        $this->commit();
    }

    /**
     * Get first DebriefTaskGarage by DebriefTask ID.
     */
    public function getFirstByDebriefTaskId($debrief_task_id)
    {
        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Garage.id = DebriefTaskGarage.garage_id'
                    ),
                ),
            ),
            'conditions' => array(
                'DebriefTaskGarage.debrief_task_id' => $debrief_task_id
            ),
            'fields' => array(
                'Garage.name'
            )
        ));
    }

    public function getListByDebriefTaskId($debrief_task_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Garage.id = DebriefTaskGarage.garage_id'
                    ),
                ),
            ),
            'conditions' => array(
                'DebriefTaskGarage.debrief_task_id' => $debrief_task_id
            ),
            'fields' => array(
                'Garage.id',
                'Garage.name'
            )
        ));
    }
}
