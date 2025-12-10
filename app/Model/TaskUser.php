<?php

class TaskUser extends AppModel{
    public $useTable = 'tasks_users';

    public $hasOne = array(
        'User',
        'Task',
    );

    public function new_task_user( $task_user ) {
        $fields = array(
            'TaskUser' => array(
                'user_id',
                'task_id',
            )
        );

        $this->create();
        $task_user_bd = $this->guardar($task_user, $fields);
        if (!$task_user_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function getUsersByTask( $task_id ){
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'User.id = TaskUser.user_id'
                ),
            ),
            'conditions' => array(
                'TaskUser.task_id' => $task_id
            ),
            'fields' => array(
                'User.name',
                'User.surname',
            )
        ));
    }

    public function getListUserByTask( $task_id ){
        return $this->find('list', array(
            'conditions' => array(
                'task_id' => $task_id
            ),
            'fields' => array(
                'id',
                'user_id'
            )
        ));
    }

}