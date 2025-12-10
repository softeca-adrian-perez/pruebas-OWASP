<?php

class TaskGarage extends AppModel {
    public $useTable = 'tasks_garages';

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    private $_queries = array(
        'search_all_tasks_by_garage_id' => array(
            'joins' => array(
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Task.id = TaskGarage.task_id'
                    ),
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Task.user_assigned_id = User.id'
                    ),
                )
            ),
            'fields' => array(
                'Task.*',
                'TaskGarage.*',
                'User.*'
            )
        ),
        'completed_garage_tasks' => array(
            'joins' => array(
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Task.id = TaskGarage.task_id',
                    ),
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Garage.id = TaskGarage.garage_id',
                    ),
                )
            ),
            'group' => array(
                'Task.id'
            ),
            'order' => 'TaskGarage.completed_date DESC',
            'fields' => array(
                'Task.*',
                'TaskGarage.*'
            )
        ),
    );

    public function conditions($fields){
        $conditions = array();

        if (!empty($fields['title'])) {
            $conditions[] = $this->_conditionTitle($fields['title']);
        }
        if (!empty($fields['body'])) {
            $conditions[] = $this->_conditionBody($fields['body']);
        }
        if (!empty($fields['created_by'])) {
            $conditions[] = $this->_conditionCreatedBy($fields['created_by']);
        }
        if (!empty($fields['assigned_to'])) {
            $conditions[] = $this->_conditionAssignedTo($fields['assigned_to']);
        }

        if (isset($fields['completed']) && $fields['completed'] != '') {
            $conditions[] = $this->_conditionStatus($fields['completed']);
        }
        if (!empty($fields['creation_date_from'])) {
            $conditions[] = $this->_conditionCreationDateFrom($fields['creation_date_from']);
        }
        if (!empty($fields['creation_date_to'])) {
            $conditions[] = $this->_conditionCreationDateTo($fields['creation_date_to']);
        }
        if( !empty($fields['completed_date_from'])){
            $conditions[] = $this->_conditionCompletedDateFrom($fields['completed_date_from']);
        }
        if( !empty($fields['completed_date_to'])){
            $conditions[] = $this->_conditionCompletedDateTo($fields['completed_date_to']);
        }

        return $conditions;
    }

    private function _conditionTitle( $title ){
        return array('Task.title LIKE' => '%' . $title . '%');
    }

    private function _conditionBody( $body ){
        return array('Task.body LIKE' => '%' . $body . '%');
    }

    private function _conditionAssignedTo($assigned_to){
        return array('Task.user_assigned_id' => $assigned_to);
    }

    private function _conditionCreatedBy($created_by){
        return array('Task.user_creation_id' => $created_by);
    }

    private function _conditionStatus($status){
        return array('TaskGarage.completed' => $status);
    }

    private function _conditionCreationDateFrom($creation_date_from){
        return array('Task.creation_date >=' => Fecha::toFormatoBd($creation_date_from));
    }

    private function _conditionCreationDateTo($creation_date_to){
        return array('Task.creation_date <=' =>  Fecha::toFormatoBd($creation_date_to));
    }

    private function _conditionCompletedDateFrom($completed_date_from){
        return array('TaskGarage.completed_date >=' =>  Fecha::toFormatoBd($completed_date_from));
    }

    private function _conditionCompletedDateTo($completed_date_to){
        return array('TaskGarage.completed_date <=' =>  Fecha::toFormatoBd($completed_date_to));
    }


    public function new_task_garage( $task_garage ) {
        $fields = array(
            'TaskGarage' => array(
                'task_id',
                'garage_id',
                'completed',
                'completed_date'
            )
        );

        $this->create();
        $task_contact_list_bd = $this->guardar($task_garage, $fields);
        if (!$task_contact_list_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function change_status_and_date($task_garage, $completed_date){
        $fields = array(
            'TaskGarage' => array(
                'id',
                'completed',
                'completed_date'
            )
        );

        if ($task_garage['TaskGarage']['completed']) {
            $task_garage_tmp = array(
                'TaskGarage' => array(
                    'id' => $task_garage['TaskGarage']['id'],
                    'completed' => ConstantsBooleans::NO_ACTIVE,
                    'completed_date' => $completed_date
                )
            );
        } else {
            $task_garage_tmp = array(
                'TaskGarage' => array(
                    'id' => $task_garage['TaskGarage']['id'],
                    'completed' => ConstantsBooleans::ACTIVE,
                    'completed_date' => $completed_date
                )
            );
        }
        $task_garage_bd = $this->guardar($task_garage_tmp, $fields);
        if ($task_garage_bd) {
            $this->commit();
        }
        return $task_garage_bd;
    }

    public function getAllByGarageIdAndNotCompleted($garage_id){
        return $this->find('all',array(
                'joins' => array(
                    array(
                        'alias' => 'Task',
                        'table' => 'tasks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Task.id = TaskGarage.task_id',
                        ),
                    ),
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Garage.id = TaskGarage.garage_id',
                        ),
                    )
                ),
                'group' => array(
                    'Task.id'
                ),
                'conditions' => array(
                    'Garage.id' => $garage_id,
                    'TaskGarage.completed' => ConstantsBooleans::NO,
                ),
                'fields' => array(
                    'Task.*',
                    'TaskGarage.*'
                )
            )
        );
    }

    public function getCountAllByGarageIdAndCompleted($garage_id){
        return $this->find('count',array(
                'joins' => array(
                    array(
                        'alias' => 'Task',
                        'table' => 'tasks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Task.id = TaskGarage.task_id',
                        ),
                    ),
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Garage.id = TaskGarage.garage_id',
                        ),
                    )
                ),
                'group' => array(
                    'Task.id'
                ),
                'conditions' => array(
                    'Garage.id' => $garage_id,
                    'TaskGarage.completed' => ConstantsBooleans::YES,
                ),
                'fields' => array(
                    'Task.*',
                    'TaskGarage.*'
                )
            )
        );
    }

    public function getAllByTaskId($task_id) {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Garage.id = TaskGarage.garage_id'
                    ),
                ),
            ),
            'conditions' => array(
                'TaskGarage.task_id' => $task_id
            ),
            'fields' => array(
                'TaskGarage.completed',
                'Garage.name'
            )
        ));
    }

    public function getTaskGarageCount($task_id) {
        return $this->find('count', array(
            'conditions' => array(
                'TaskGarage.task_id' => $task_id,
                'TaskGarage.completed' => ConstantsBooleans::YES
            )
        ));
    }

    public function getAllTaskGarageCount($task_id) {
        return $this->find('count', array(
            'conditions' => array(
                'TaskGarage.task_id' => $task_id
            )
        ));
    }

    public function getAllTasksByGarageId($garage_id){
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Task.id = TaskGarage.task_id'
                    ),
                ),
            ),
            'conditions' => array(
                'TaskGarage.garage_id' => $garage_id
            ),
            'fields' => array(
                'Task.*',
                'TaskGarage.*'
            )
        ));
    }
}