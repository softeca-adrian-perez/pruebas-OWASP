<?php

class TaskContactList extends AppModel{
    public $useTable = 'tasks_contacts_lists';

    public $hasOne = array(
        'ContactList',
        'Task',
    );

    public function new_task_contact_list( $task_contact_list ) {
        $fields = array(
            'TaskContactList' => array(
                'contact_list_id',
                'task_id',
            )
        );

        $this->create();
        $task_contact_list_bd = $this->guardar($task_contact_list, $fields);
        if (!$task_contact_list_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function getContactsListsByTask( $task_id ){
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'ContactList',
                    'table' => 'contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'ContactList.id = TaskContactList.contact_list_id'
                ),
            ),
            'conditions' => array(
                'TaskContactList.task_id' => $task_id
            ),
            'fields' => array(
                'ContactList.id',
                'ContactList.name',
            )
        ));
    }

    public function getListContactListByTask( $task_id ){
        return $this->find('list', array(
            'conditions' => array(
                'task_id' => $task_id
            ),
            'fields' => array(
                'contact_list_id',
                'contact_list_id'
            )
        ));
    }

}