<?php

class TaskDistributor extends AppModel {
    public $useTable = 'tasks_distributors';

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    private $_queries = array(
        'search_all_tasks_by_distributor_id' => array(
            'joins' => array(
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Task.id = TaskDistributor.task_id'
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
                'TaskDistributor.*',
                'User.*'
            )
        ),
        'completed_distributor_tasks' => array(
            'joins' => array(
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Task.id = TaskDistributor.task_id',
                    ),
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = TaskDistributor.distributor_id',
                    ),
                )
            ),
            'group' => array(
                'Task.id'
            ),
            'order' => 'TaskDistributor.completed_date DESC',
            'fields' => array(
                'Task.*',
                'TaskDistributor.*'
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
        return array('TaskDistributor.completed' => $status);
    }

    private function _conditionCreationDateFrom($creation_date_from){
        return array('Task.creation_date >=' => Fecha::toFormatoBd($creation_date_from));
    }

    private function _conditionCreationDateTo($creation_date_to){
        return array('Task.creation_date <=' =>  Fecha::toFormatoBd($creation_date_to));
    }

    private function _conditionCompletedDateFrom($completed_date_from){
        return array('TaskDistributor.completed_date >=' =>  Fecha::toFormatoBd($completed_date_from));
    }

    private function _conditionCompletedDateTo($completed_date_to){
        return array('TaskDistributor.completed_date <=' =>  Fecha::toFormatoBd($completed_date_to));
    }


    public function new_task_distributor( $task_distributor ) {
        $fields = array(
            'TaskDistributor' => array(
                'task_id',
                'distributor_id',
                'completed',
                'completed_date'
            )
        );

        $this->create();
        $task_contact_list_bd = $this->guardar($task_distributor, $fields);
        if (!$task_contact_list_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function change_status_and_date($task_distributor, $completed_date){
        $fields = array(
            'TaskDistributor' => array(
                'id',
                'completed',
                'completed_date'
            )
        );

        if ($task_distributor['TaskDistributor']['completed']) {
            $task_distributor_tmp = array(
                'TaskDistributor' => array(
                    'id' => $task_distributor['TaskDistributor']['id'],
                    'completed' => ConstantsBooleans::NO_ACTIVE,
                    'completed_date' => $completed_date
                )
            );
        } else {
            $task_distributor_tmp = array(
                'TaskDistributor' => array(
                    'id' => $task_distributor['TaskDistributor']['id'],
                    'completed' => ConstantsBooleans::ACTIVE,
                    'completed_date' => $completed_date
                )
            );
        }
        $task_distributor_bd = $this->guardar($task_distributor_tmp, $fields);
        if ($task_distributor_bd) {
            $this->commit();
        }
        return $task_distributor_bd;
    }

    public function getAllByDistributorIdAndNotCompleted($distributor_id){
        return $this->find('all',array(
                'joins' => array(
                    array(
                        'alias' => 'Task',
                        'table' => 'tasks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Task.id = TaskDistributor.task_id',
                        ),
                    ),
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Distributor.id = TaskDistributor.distributor_id',
                        ),
                    )
                ),
                'group' => array(
                    'Task.id'
                ),
                'conditions' => array(
                    'Distributor.id' => $distributor_id,
                    'TaskDistributor.completed' => ConstantsBooleans::NO,
                ),
                'fields' => array(
                    'Task.*',
                    'TaskDistributor.*'
                )
            )
        );
    }

    public function getCountAllByDistributorIdAndCompleted($distributor_id){
        return $this->find('count',array(
                'joins' => array(
                    array(
                        'alias' => 'Task',
                        'table' => 'tasks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Task.id = TaskDistributor.task_id',
                        ),
                    ),
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Distributor.id = TaskDistributor.distributor_id',
                        ),
                    )
                ),
                'group' => array(
                    'Task.id'
                ),
                'conditions' => array(
                    'Distributor.id' => $distributor_id,
                    'TaskDistributor.completed' => ConstantsBooleans::YES,
                ),
                'fields' => array(
                    'Task.*',
                    'TaskDistributor.*'
                )
            )
        );
    }

    public function getAllByTaskId($task_id) {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = TaskDistributor.distributor_id'
                    ),
                ),
            ),
            'conditions' => array(
                'TaskDistributor.task_id' => $task_id
            ),
            'fields' => array(
                'TaskDistributor.completed',
                'Distributor.name'
            )
        ));
    }

    public function getTaskDistributorCount($task_id) {
        return $this->find('count', array(
            'conditions' => array(
                'TaskDistributor.task_id' => $task_id,
                'TaskDistributor.completed' => ConstantsBooleans::YES
            )
        ));
    }

    public function getAllTaskDistributorCount($task_id) {
        return $this->find('count', array(
            'conditions' => array(
                'TaskDistributor.task_id' => $task_id
            )
        ));
    }

    public function getAllTasksByDistributorId($distributor_id){
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Task.id = TaskDistributor.task_id'
                    ),
                ),
            ),
            'conditions' => array(
                'TaskDistributor.distributor_id' => $distributor_id
            ),
            'fields' => array(
                'Task.*',
                'TaskDistributor.*'
            )
        ));
    }
}