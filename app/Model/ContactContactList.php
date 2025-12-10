<?php

class ContactContactList extends AppModel{
    public $useTable = 'contacts_contacts_lists';

    public function new_contact_contact_list( $contact_contact_list ){
        $fields = array(
            'ContactContactList' => array(
                'contact_id',
                'contact_list_id',
            )
        );
        $this->create();
        $contact_contact_list_bd = $this->guardar($contact_contact_list, $fields);
        if ( !$contact_contact_list_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    private $_queries = array(
        'search_assigned_my_group' => array(
            'joins' => array(
                array(
                    'alias' => 'TaskContactList',
                    'table' => 'tasks_contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.contact_list_id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'ContactList',
                    'table' => 'contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'ContactList.id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.task_id = Task.id'
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'User.id = Task.user_assigned_id'
                ),
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Appointment.id = Task.appointment_id',
                    ),
                ),
            ),
            'fields' => array(
                'Task.*',
                'User.*',
                'Appointment.*'
            ),
            'order' => 'Task.creation_date DESC'
        ),
    );


    public function getAllAssignedToMyGroupTask( $conditions ){
        return $this->find('all',array(
            'joins' => array(
                array(
                    'alias' => 'TaskContactList',
                    'table' => 'tasks_contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.contact_list_id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'ContactList',
                    'table' => 'contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'ContactList.id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.task_id = Task.id'
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'User.id = Task.user_assigned_id'
                ),
            ),
            'conditions' => $conditions,
            'order' => array(
                'Task.limit_date asc',
                'Task.creation_date desc',
            ),
            'fields' => array(
                'Task.*',
                'User.name',
                'User.surname',
                'ContactList.name'
            ),
        ));
    }

    public function getAssignedToMyGroupOpenTask( $contact_id ){
        return $this->find('all',array(
            'joins' => array(
                array(
                    'alias' => 'TaskContactList',
                    'table' => 'tasks_contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.contact_list_id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'ContactList',
                    'table' => 'contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'ContactList.id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.task_id = Task.id'
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'User.id = Task.user_assigned_id'
                ),
            ),
            'conditions' => array(
                'ContactContactList.contact_id' => $contact_id,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'Task.limit_date !=' => null,
                // 'OR' => array(
                //     'Task.limit_date >=' => date('Y-m-d'),
                //     'Task.mandatory' => ConstantsBooleans::YES
                // )
            ),
            'order' => array(
                'Task.limit_date asc',
                'Task.creation_date desc',
            ),
            'limit' => ConstantsLimitDashboard::TASK,
            'fields' => array(
                'Task.*',
                'User.name',
                'User.surname',
                'ContactList.name'
            ),
        ));
    }
    public function getAllAssignedToMyGroupOpenTaskList( $contact_id ){
        return $this->find('all',array(
            'joins' => array(
                array(
                    'alias' => 'TaskContactList',
                    'table' => 'tasks_contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.contact_list_id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'INNER',
                    'conditions' => 'Contact.id = ContactContactList.contact_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.task_id = Task.id'
                ),
            ),
            'conditions' => array(
                'ContactContactList.contact_id' => $contact_id,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'OR' => array(
                    'Task.limit_date' => null,
                    'Task.limit_date >=' => date('Y-m-d'),
                    'Task.mandatory' => ConstantsBooleans::YES
                )
            ),
            'fields' => array(
                'Task.id',
            ),
        ));
    }
    public function getAssignedToMyGroupOpenTaskDeadlineNull( $contact_id ){
        return $this->find('all',array(
            'joins' => array(
                array(
                    'alias' => 'TaskContactList',
                    'table' => 'tasks_contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.contact_list_id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'ContactList',
                    'table' => 'contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'ContactList.id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.task_id = Task.id'
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'User.id = Task.user_assigned_id'
                ),
            ),
            'conditions' => array(
                'ContactContactList.contact_id' => $contact_id,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'Task.limit_date' => null,
            ),
            'order' => array(
                'Task.limit_date asc',
                'Task.creation_date desc',
            ),
            'limit' => ConstantsLimitDashboard::TASK,
            'fields' => array(
                'Task.*',
                'User.name',
                'User.surname',
                'ContactList.name'
            ),
        ));
    }

    public function countAssignedToMyGroupOpenTask( $contact_id ){
        return $this->find('count',array(
            'joins' => array(
                array(
                    'alias' => 'TaskContactList',
                    'table' => 'tasks_contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.contact_list_id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'INNER',
                    'conditions' => 'Contact.id = ContactContactList.contact_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.task_id = Task.id'
                ),
            ),
            'conditions' => array(
                'ContactContactList.contact_id' => $contact_id,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                // 'OR' => array(
                //     'Task.limit_date' => null,
                //     'Task.limit_date >=' => date('Y-m-d'),
                //     'Task.mandatory' => ConstantsBooleans::YES
                // )
            ),
            'fields' => array(
                'Task.id',
            ),
        ));
    }

    public function getAssignedToMyGroupOpenTaskSortDueToday( $contact_id ){
        return $this->find('all',array(
            'joins' => array(
                array(
                    'alias' => 'TaskContactList',
                    'table' => 'tasks_contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.contact_list_id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'ContactList',
                    'table' => 'contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'ContactList.id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.task_id = Task.id'
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'User.id = Task.user_assigned_id'
                ),
            ),
            'conditions' => array(
                'ContactContactList.contact_id' => $contact_id,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'Task.limit_date' => date('Y-m-d'),
            ),
            'order' => array(
                'Task.limit_date asc',
                'Task.creation_date desc',
            ),
            'limit' => ConstantsLimitDashboard::TASK,
            'fields' => array(
                'Task.*',
                'User.name',
                'User.surname',
                'ContactList.name'
            ),
        ));
    }

    public function getAssignedToMyGroupOpenTaskSortDueThisWeek( $contact_id ){
        return $this->find('all',array(
            'joins' => array(
                array(
                    'alias' => 'TaskContactList',
                    'table' => 'tasks_contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.contact_list_id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'ContactList',
                    'table' => 'contacts_lists',
                    'type' => 'INNER',
                    'conditions' => 'ContactList.id = ContactContactList.contact_list_id'
                ),
                array(
                    'alias' => 'Task',
                    'table' => 'tasks',
                    'type' => 'INNER',
                    'conditions' => 'TaskContactList.task_id = Task.id'
                ),

                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'User.id = Task.user_assigned_id'
                ),
            ),
            'conditions' => array(
                'ContactContactList.contact_id' => $contact_id,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'OR' => array(
                    array(
                        'Task.limit_date >=' => date('Y-m-d'),
                        'Task.limit_date <=' => date('Y-m-d', strtotime('sunday this week')),
                    ),
                    array(
                        'Task.limit_date >=' => date('Y-m-d', strtotime('monday this week')),
                        'Task.limit_date <=' => date('Y-m-d', strtotime('sunday this week')),
                        'Task.mandatory' => ConstantsBooleans::YES
                    )
                )
            ),
            'order' => array(
                'Task.limit_date asc',
                'Task.creation_date desc'
            ),
            'limit' => ConstantsLimitDashboard::TASK,
            'fields' => array(
                'Task.*',
                'User.name',
                'User.surname',
                'ContactList.name'
            ),
        ));
    }

}