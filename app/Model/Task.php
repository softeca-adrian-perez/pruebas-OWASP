<?php

class Task extends AppModel
{
    public $useTable = 'tasks';
    //public $displayField = 'appointment_id';

    //Por la relacion del modelo
    public $hasOne = array(
        'Appointment',
        'User',
        'TaskStatus',
    );
    public $hasMany = array(
        'TaskFile',
        'TaskUser',
        'TaskContactList',
    );

    public $validate = array(
        'body' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_fill_the_body',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'task_status_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_status',
            ),
        ),
        'limit_date' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_deadline',
            ),
            array(
                'rule' => 'date',
                'dmy',
                'message' => 'Validation.Format_date',
                'allowEmpty' => false
            ),
        ),
        'user_assigned_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_user_assigned_id',
            ),
        ),
        'title' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_title',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    private $_queries = array(
        'search' => array(
            'joins' => array(
                array(
                    'alias' => 'Users',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Users.id = Task.user_assigned_id',
                    ),
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
                'Users.*',
                'Appointment.appointment_status_id'
            ),
            'order' => 'Task.creation_date DESC'
        ),
        'search_assigned_me_task' => array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.id = Task.appointment_id'
                ),
                array(
                    'alias' => 'TaskGarage',
                    'table' => 'tasks_garages',
                    'type' => 'LEFT',
                    'conditions' => 'TaskGarage.task_id = Task.id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Garage.id = TaskGarage.garage_id'
                ),
                array(
                    'alias' => 'TaskDistributor',
                    'table' => 'tasks_distributors',
                    'type' => 'LEFT',
                    'conditions' => 'TaskDistributor.task_id = Task.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Distributor.id = TaskDistributor.distributor_id'
                ),
                array(
                    'alias' => 'TaskUser',
                    'table' => 'tasks_users',
                    'type' => 'LEFT',
                    'conditions' => 'TaskUser.task_id = Task.id'
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'User.id = TaskUser.user_id'
                ),
            ),
            'fields' => array(
                'Task.*',
                'Garage.name',
                'Distributor.name',
                'Appointment.appointment_status_id'
            ),
            'order' => array('Task.limit_date' => 'ASC', 'Task.creation_date' => 'ASC'),
        ),
        'search_assigned_my_customers' => array(
            'joins' => array(
                array(
                    'alias' => 'TaskGarage',
                    'table' => 'tasks_garages',
                    'type' => 'LEFT',
                    'conditions' => 'TaskGarage.task_id = Task.id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Garage.id = TaskGarage.garage_id'
                ),
                array(
                    'alias' => 'GarageContactBdm',
                    'table' => 'garages_contacts_bdm',
                    'type' => 'LEFT',
                    'conditions' => 'GarageContactBdm.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'TaskDistributor',
                    'table' => 'tasks_distributors',
                    'type' => 'LEFT',
                    'conditions' => 'TaskDistributor.task_id = Task.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Distributor.id = TaskDistributor.distributor_id'
                ),
                array(
                    'alias' => 'DistributorContactBdm',
                    'table' => 'distributors_contacts_bdm',
                    'type' => 'LEFT',
                    'conditions' => 'DistributorContactBdm.distributor_id = Distributor.id'
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
            'order' => array('Task.limit_date' => 'ASC', 'Task.creation_date' => 'ASC'),
            'group' => array(
                'Task.id'
            ),
            'fields' => array(
                'Task.*',
                'Appointment.*'
            ),
        ),
        'search_subtask' => array(
            'joins' => array(
                array(
                    'alias' => 'Users',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Users.id = Task.user_assigned_id',
                    ),
                ),
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Appointment.id = Task.appointment_id',
                    ),
                ),
                array(
                    'alias' => 'TaskContactList',
                    'table' => 'tasks_contacts_lists',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TaskContactList.task_id = Task.id',
                    ),
                ),
                array(
                    'alias' => 'ContactContactList',
                    'table' => 'contacts_contacts_lists',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'ContactContactList.contact_list_id = TaskContactList.contact_list_id',
                    ),
                ),
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'ContactContactList.contact_id = Contact.id',
                    ),
                ),
                array(
                    'alias' => 'TaskGarage',
                    'table' => 'tasks_garages',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TaskGarage.task_id = Task.id',
                    )
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.id = TaskGarage.garage_id'
                    )
                ),
                array(
                    'alias' => 'GarageContactBdm',
                    'table' => 'garages_contacts_bdm',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageContactBdm.garage_id = Garage.id'
                    )
                ),
                array(
                    'alias' => 'TaskDistributor',
                    'table' => 'tasks_distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TaskDistributor.task_id = Task.id',
                    )
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Distributor.id = TaskDistributor.distributor_id'
                    )
                ),
                array(
                    'alias' => 'DistributorContactBdm',
                    'table' => 'distributors_contacts_bdm',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'DistributorContactBdm.distributor_id = Distributor.id'
                    )
                )
            ),
            'fields' => array(
                'Task.*',
                'Users.*',
                'Appointment.appointment_status_id',
                'Garage.id',
                'Garage.name',
                'TaskGarage.*',
                'GarageContactBdm.contact_id',
                'Distributor.id',
                'Distributor.name',
                'TaskDistributor.*',
                'DistributorContactBdm.contact_id',
                'CONCAT(Contact.first_name, " ", Contact.last_name) as full_name'
            ),
            'group' => array('Garage.id', 'Distributor.id'),
            'order' => 'Task.creation_date DESC'
        )

    );

    public function conditions($fields)
    {
        $conditions = array();

        if (!empty($fields['title'])) {
            $conditions[] = $this->_conditionTitle($fields['title']);
        }
        if (!empty($fields['body'])) {
            $conditions[] = $this->_conditionBody($fields['body']);
        }
        if (!empty($fields['limit_date_from'])) {
            $conditions[] = $this->_conditionLimit_date_from($fields['limit_date_from']);
        }
        if (!empty($fields['limit_date_to'])) {
            $conditions[] = $this->_conditionLimit_date_to($fields['limit_date_to']);
        }
        if (!empty($fields['task_status_id'])) {
            $conditions[] = $this->_conditionResolveStatus($fields['task_status_id']);
        }
        if (!empty($fields['assigned_to'])) {
            $conditions[] = $this->_conditionAssignedTo($fields['assigned_to']);
        }
        if (!empty($fields['created_by'])) {
            $conditions[] = $this->_conditionCreatedBy($fields['created_by']);
        }
        if (!empty($fields['id'])) {
            $conditions[] = $this->_conditionId($fields['id']);
        }
        if (isset($fields['assigned_group']) && $fields['assigned_group'] == ConstantsBooleans::ACTIVE) {
            $conditions[] = $this->_conditionAssignedGroup();
        }
        if (isset($fields['assigned_customers']) && $fields['assigned_customers'] == ConstantsBooleans::ACTIVE) {
            $conditions[] = $this->_conditionAssignedCustomers();
        }

        return $conditions;
    }

    private function _conditionTitle($title)
    {
        return array('Task.title LIKE' => '%' . $title . '%');
    }

    private function _conditionBody($body)
    {
        return array('Task.body LIKE' => '%' . $body . '%');
    }

    private function _conditionLimit_date_from($limit_date)
    {
        return array('Task.limit_date >=' =>  Fecha::toFormatoBd($limit_date));
    }
    private function _conditionLimit_date_to($limit_date)
    {
        return array('Task.limit_date <=' =>  Fecha::toFormatoBd($limit_date));
    }

    private function _conditionResolveStatus($task_status_id)
    {
        return array('Task.task_status_id' => $task_status_id);
    }

    private function _conditionAssignedTo($assigned_to)
    {
        return array('Task.user_assigned_id' => $assigned_to);
    }

    private function _conditionCreatedBy($created_by)
    {
        return array('Task.user_creation_id' => $created_by);
    }

    private function _conditionId($task_id)
    {
        return array('Task.id' => $task_id);
    }

    private function _conditionAssignedGroup()
    {
        $user = CakeSession::read('Auth.User.id');
        $user_bd = $this->User->findById($user);
        $contact_id = $user_bd['User']['contact_id'];
        $this->ContactContactList = ClassRegistry::init('ContactContactList');
        $assigned_group_tasks = $this->ContactContactList->getAllAssignedToMyGroupOpenTaskList($contact_id);

        return array('Task.id' => Hash::extract($assigned_group_tasks, '{n}.Task.id'));
    }

    private function _conditionAssignedCustomers()
    {
        $user = CakeSession::read('Auth.User.id');
        $user_bd = $this->User->findById($user);
        $contact_id = $user_bd['User']['contact_id'];
        $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');
        $assigned_customers_tasks = $this->GarageContactBdm->getAllAssignedToMyCustomersOpenTask($contact_id);

        return array('Task.id' => Hash::extract($assigned_customers_tasks, '{n}.Task.id'));
    }

    public function new_task($task, $user)
    {
        $fields = array(
            'Task' => array(
                'appointment_id',
                'title',
                'body',
                'mandatory',
                'task_status_id',
                'resolve_date',
                'limit_date',
                'user_assigned_id',
                'user_creation_id',
                'creation_date',
                'reason'
            )
        );
        $task['Task']['creation_date'] = date('Y-m-d H:i:s');
        $task['Task']['user_creation_id'] = $user;
        if (isset($task['Task']['limit_date'])) {
            $task['Task']['limit_date'] = Fecha::toFormatoBd($task['Task']['limit_date']);
        }

        $this->create();
        $task_bd = $this->guardar($task, $fields);
        if (!$task_bd) {
            return false;
        }
        return true;
    }

    public function edit_task($task, $validate = null)
    {
        $fields = array(
            'Task' => array(
                'id',
                'appointment_id',
                'title',
                'body',
                'mandatory',
                'task_status_id',
                'resolve_date',
                'limit_date',
                'user_assigned_id',
                'creation_date',
                'reason'
            )
        );

        if( !empty($task['Task']['limit_date']) && !Fecha::isDatabaseFormat($task['Task']['limit_date'], '-')){
            $task['Task']['limit_date'] = Fecha::toFormatoBd($task['Task']['limit_date']);
        }

        if (is_null($validate)) {
            $task_bd = $this->guardar($task, $fields);
        } else {
            $task_bd = $this->guardar($task, $fields, $validate);
        }

        if (!$task_bd) {
            return false;
        }
        return true;
    }

    public function change_status($task_garage, $task, $task_garages)
    {
        $fields = array(
            'Task' => array(
                'id',
                'task_status_id',
            )
        );

        if ($task_garage['TaskGarage']['completed']) {
            $task_tmp = array(
                'Task' => array(
                    'id' => $task['Task']['id'],
                    'task_status_id' => ConstantsStatusTasks::PENDING,
                )
            );
            $this->guardar($task_tmp, $fields);
        } else {
            $count = 0;
            foreach ($task_garages as $garage_task) {
                if (!$garage_task['TaskGarage']['completed']) {
                    $count++;
                }
            }
            if ($count <= 1) {
                $task_tmp = array(
                    'Task' => array(
                        'id' => $task['Task']['id'],
                        'task_status_id' => ConstantsStatusTasks::COMPLETED,
                    )
                );

                $this->guardar($task_tmp, $fields);
            }
        }

        $this->commit();
    }

    public function change_status_distributor($task_distributor, $task, $task_distributors)
    {
        $fields = array(
            'Task' => array(
                'id',
                'task_status_id',
            )
        );

        if ($task_distributor['TaskDistributor']['completed']) {
            $task_tmp = array(
                'Task' => array(
                    'id' => $task['Task']['id'],
                    'task_status_id' => ConstantsStatusTasks::PENDING,
                )
            );
            $this->guardar($task_tmp, $fields);
        } else {
            $count = 0;
            foreach ($task_distributors as $distributor_task) {
                if (!$garage_task['TaskDistributor']['completed']) {
                    $count++;
                }
            }
            if ($count <= 1) {
                $task_tmp = array(
                    'Task' => array(
                        'id' => $task['Task']['id'],
                        'task_status_id' => ConstantsStatusTasks::COMPLETED,
                    )
                );

                $this->guardar($task_tmp, $fields);
            }
        }

        $this->commit();
    }

    public function debrief_task_to_task($debrief_task_id, $appointment_id, $deadline, $user_assigned_id_debrief)
    {
        $this->DebriefTask = ClassRegistry::init('DebriefTask');
        $this->DebriefTaskGarage = ClassRegistry::init('DebriefTaskGarage');
        $this->DebriefTaskDistributor = ClassRegistry::init('DebriefTaskDistributor');
        $this->ContactContactList = ClassRegistry::init('ContactContactList');
        $this->TaskGarage = ClassRegistry::init('TaskGarage');
        $this->TaskDistributor = ClassRegistry::init('TaskDistributor');
        $debrief_task = $this->DebriefTask->findById($debrief_task_id);
        $debrief_task_garage = $this->DebriefTaskGarage->findAllByDebriefTaskId($debrief_task_id);
        $debrief_task_distributor = $this->DebriefTaskDistributor->findAllByDebriefTaskId($debrief_task_id);
        if ($user_assigned_id_debrief != null) {
            $user_assigned_id = $user_assigned_id_debrief;
        } else if ($debrief_task['DebriefTask']['contact_list_id'] == null && $debrief_task['DebriefTask']['user_assigned_id'] == null && empty($debrief_task_garage) && empty($debrief_task_distributor)) {
            $user_assigned_id = CakeSession::read('Auth.User.id');
        } else {
            $user_assigned_id = $debrief_task['DebriefTask']['user_assigned_id'];
        }
        $task_tmp = array(
            'Task' => array(
                'appointment_id' => $appointment_id,
                'title' => $debrief_task['DebriefTask']['title' . __s()],
                'body' => $debrief_task['DebriefTask']['description' . __s()],
                'user_assigned_id' => $user_assigned_id,
                'user_creation_id' => CakeSession::read('Auth.User.id'),
                'task_status_id' => ConstantsStatusTasks::PENDING,
                'limit_date' => $deadline,
                'creation_date' => date('Y-m-d H:i:s')
            )
        );

        if ($this->new_task($task_tmp, CakeSession::read('Auth.User.id'))) {
            $task_id = $this->getLastInsertID();
            if (!empty($debrief_task['DebriefTask']['user_assigned_id'])) {
                $task_user_tmp = array(
                    'TaskUser' => array(
                        'user_id' => $debrief_task['DebriefTask']['user_assigned_id'],
                        'task_id' => $task_id
                    )
                );
                $this->TaskUser->new_task_user($task_user_tmp);
            }

            if (!empty($debrief_task['DebriefTask']['contact_list_id'])) {
                $task_contact_list_tmp = array(
                    'TaskContactList' => array(
                        'contact_list_id' => $debrief_task['DebriefTask']['contact_list_id'],
                        'task_id' => $task_id
                    )
                );
                $this->TaskContactList->new_task_contact_list($task_contact_list_tmp);
            }

            if (!empty($debrief_task_garage)) {
                foreach ($debrief_task_garage as $garage_task) {
                    $task_garage_tmp = array(
                        'TaskGarage' => array(
                            'task_id' => $task_id,
                            'garage_id' => $garage_task['DebriefTaskGarage']['garage_id'],
                            'completed' => ConstantsBooleans::NO
                        )
                    );
                    $this->TaskGarage->new_task_garage($task_garage_tmp);
                }
                return __t('Garage.Garage');
            } else if (!empty($debrief_task_distributor)) {
                foreach ($debrief_task_distributor as $distributor_task) {
                    $task_distributor_tmp = array(
                        'TaskDistributor' => array(
                            'task_id' => $task_id,
                            'distributor_id' => $distributor_task['DebriefTaskDistributor']['distributor_id'],
                            'completed' => ConstantsBooleans::NO
                        )
                    );
                    $this->TaskDistributor->new_task_distributor($task_distributor_tmp);
                }
                return __t('Distributor.Distributor');
            } else {
                return __t('Task.Task');
            }
        } else {
            return ConstantsBooleans::NO;
        }
    }

    public function createTaskNewGarage($data, $garage_id)
    {
        $fields = array(
            'Task' => array(
                'body',
                'title',
                'task_status_id',
                'user_creation_id',
                'creation_date'
            )
        );

        $task = array(
            'Task' => array(
                'body' => h(sprintf(
                    __t('Task.Body_task_garage_potential'),
                    $data['Garage']['name'] . ' - ' . $garage_id, CakeSession::read('Auth.User.full_name'),
                    $data['Garage']['comment']
                )),
                'title' => h(sprintf(__t('Task.Title_task_garage_potential'), $data['Garage']['name'])),
                'task_status_id' => ConstantsStatusTasks::PENDING,
                'user_creation_id' => CakeSession::read('Auth.User.id'),
                'creation_date' => date('Y-m-d')
            )
        );

        $this->create();

        $task_bd = $this->guardar($task, $fields);
        if (!$task_bd) {
            return false;
        } else {
            $task_contact_list = array(
                'TaskContactList' => array(
                    'contact_list_id' => $data['ContactList']['contact_list_id'],
                    'task_id' => $this->getLastInsertID()
                )
            );
            if ($this->TaskContactList->new_task_contact_list($task_contact_list)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function ajax_save($data)
    {
        $fields = array(
            'Task' => array(
                'title',
                'body',
                'task_status_id',
                'user_assigned_id',
                'user_creation_id',
                'creation_date',
                'limit_date',
            )
        );

        $this->create();

        $task_bd = $this->guardar($data, $fields);

        if (!$task_bd) {
            return false;
        } else {
            return $task_bd;
        }
    }

    //Pick the task with the user by id
    public function getTask($task_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'INNER',
                        'conditions' => array(
                            'User.id = Task.user_creation_id',
                        )
                    ),
                ),
                'conditions' => array(
                    'Task.id' => $task_id,
                ),
                'fields' => array(
                    'Task.*',
                    'User.*',
                    'CONCAT(User.name, " ", User.surname) full_name'
                )
            )
        );
    }

    public function countAssignedToMeOpenTask($user)
    {
        return $this->find('count', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.id = Task.appointment_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'Task.user_assigned_id' => $user,
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

    public function getAssignedToMeOpenTask($user)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.id = Task.appointment_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'Task.user_assigned_id' => $user,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'Task.limit_date IS NOT NULL',
            ),
            'fields' => array(
                'Task.*',
                'Garage.name',
                'Distributor.name',
                'Appointment.appointment_status_id'
            ),
            'order' => array('Task.limit_date ASC', 'Task.creation_date ASC'),
            'limit' => ConstantsLimitDashboard::TASK
        ));
    }

    public function getCreatedToMeOpenTask($user)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.id = Task.appointment_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'Task.user_creation_id' => $user,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'OR' => array(
                    'Task.limit_date >=' => date('Y-m-d'),
                    'Task.mandatory' => ConstantsBooleans::YES
                )
            ),
            'fields' => array(
                'Task.*',
                'Garage.name',
                'Distributor.name',
                'Appointment.appointment_status_id'
            ),
            'order' => array('Task.limit_date ASC', 'Task.creation_date ASC'),
            'limit' => ConstantsLimitDashboard::TASK
        ));
    }

    public function getAssignedToMeOpenTaskDeadlineNull($user)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.id = Task.appointment_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'Task.user_assigned_id' => $user,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'Task.limit_date' => null,
            ),
            'fields' => array(
                'Task.*',
                'Garage.name',
                'Distributor.name',
                'Appointment.appointment_status_id'
            ),
            'order' => array('Task.limit_date ASC', 'Task.creation_date ASC'),
            'limit' => ConstantsLimitDashboard::TASK
        ));
    }

    public function getCreatedToMeOpenTaskDeadlineNull($user)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.id = Task.appointment_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'Task.user_creation_id' => $user,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'Task.limit_date' => null,
            ),
            'fields' => array(
                'Task.*',
                'Garage.name',
                'Distributor.name',
                'Appointment.appointment_status_id'
            ),
            'order' => array('Task.limit_date ASC', 'Task.creation_date ASC'),
            'limit' => ConstantsLimitDashboard::TASK
        ));
    }

    public function getAssignedToMyOpenTaskDeSortDueToday($user)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.id = Task.appointment_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'Task.user_assigned_id' => $user,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'Task.limit_date' => date('Y-m-d'),
            ),
            'fields' => array(
                'Task.*',
                'Garage.name',
                'Distributor.name',
                'Appointment.appointment_status_id'
            ),
            'order' => array('Task.limit_date ASC', 'Task.creation_date ASC'),
            'limit' => ConstantsLimitDashboard::TASK
        ));
    }

    public function getCreatedToMyOpenTaskDeSortDueToday($user)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.id = Task.appointment_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'Task.user_creation_id' => $user,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'Task.limit_date' => date('Y-m-d'),
            ),
            'fields' => array(
                'Task.*',
                'Garage.name',
                'Distributor.name',
                'Appointment.appointment_status_id'
            ),
            'order' => array('Task.limit_date ASC', 'Task.creation_date ASC'),
            'limit' => ConstantsLimitDashboard::TASK
        ));
    }

    public function getAssignedToMyOpenTaskDeSortDueThisWeek($user)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.id = Task.appointment_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'Task.user_assigned_id' => $user,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'OR' => array(
                    array(
                        'Task.limit_date >=' => date('Y-m-d', strtotime('monday this week')),
                        'Task.limit_date <=' => date('Y-m-d', strtotime('sunday this week')),
                        'Task.mandatory' => ConstantsBooleans::YES,
                    ),
                    array(
                        'Task.limit_date >=' => date('Y-m-d'),
                        'Task.limit_date <=' => date('Y-m-d', strtotime('sunday this week')),
                    )
                )
            ),
            'fields' => array(
                'Task.*',
                'Garage.name',
                'Distributor.name',
                'Appointment.appointment_status_id'
            ),
            'order' => array('Task.limit_date ASC', 'Task.creation_date ASC'),
            'limit' => ConstantsLimitDashboard::TASK
        ));
    }

    public function getCreatedToMyOpenTaskDeSortDueThisWeek($user)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.id = Task.appointment_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'Task.user_creation_id' => $user,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
                'OR' => array(
                    array(
                        'Task.limit_date >=' => date('Y-m-d', strtotime('monday this week')),
                        'Task.limit_date <=' => date('Y-m-d', strtotime('sunday this week')),
                        'Task.mandatory' => ConstantsBooleans::YES,
                    ),
                    array(
                        'Task.limit_date >=' => date('Y-m-d'),
                        'Task.limit_date <=' => date('Y-m-d', strtotime('sunday this week')),
                    )
                )
            ),
            'fields' => array(
                'Task.*',
                'Garage.name',
                'Distributor.name',
                'Appointment.appointment_status_id'
            ),
            'order' => array('Task.limit_date ASC', 'Task.creation_date ASC'),
            'limit' => ConstantsLimitDashboard::TASK
        ));
    }

    public function getCreatedByMeOpenTask($user)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.id = Task.appointment_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'Task.user_creation_id' => $user,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
            ),
            'fields' => array(
                'Task.*',
                'Garage.name',
                'Distributor.name',
                'Appointment.appointment_status_id'
            ),
            'order' => array('Task.limit_date ASC', 'Task.creation_date ASC'),
            'limit' => ConstantsLimitDashboard::TASK
        ));
    }

    public function countCreatedByMeOpenTask($user)
    {
        return $this->find('count', array(
            'joins' => array(
                array(
                    'alias' => 'Appointment',
                    'table' => 'appointments',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.id = Task.appointment_id'
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.garage_id = Garage.id'
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Appointment.distributor_id = Distributor.id'
                ),
            ),
            'conditions' => array(
                'Task.user_creation_id' => $user,
                'Task.task_status_id' => ConstantsStatusTasks::PENDING,
            ),
            'fields' => array(
                'Task.id',
            ),
        ));
    }

    public function getCustomerAppointmentByTaskId($task_id)
    {
        $task = $this->findById($task_id);
        $this->Appointment = ClassRegistry::init('Appointment');
        $this->Garage = ClassRegistry::init('Garage');
        $this->Distributor = ClassRegistry::init('Distributor');
        if ($task['Task']['appointment_id'] != null) {
            $appointment = $this->Appointment->findById($task['Task']['appointment_id']);
            if ($appointment['Appointment']['garage_id'] != null) {
                $garage = $this->Garage->findById($appointment['Appointment']['garage_id']);
                return ' - ' . $garage['Garage']['name'];
            } else if ($appointment['Appointment']['distributor_id'] != null) {
                $distributor = $this->Distributor->findById($appointment['Appointment']['distributor_id']);
                return ' - ' . $distributor['Distributor']['name'];
            }
        }
        return '';
    }

    public function getUrlByTaskId($task_id)
    {
        $task = $this->findById($task_id);
        if ($task['Task']['appointment_id'] != null) {
            $appointment = $this->Appointment->findById($task['Task']['appointment_id']);
            $url = Router::url(array(
                'controller' => 'appointments',
                'action' => 'edit',
                $appointment['Appointment']['id']
            ));

            return $url;
        }

        $url = Router::url(array(
            'controller' => 'tasks',
            'action' => 'edit',
            $task_id
        ));

        return $url;
    }

    public function getTaskStatusByTaskId($task_id)
    {
        $task = $this->findById($task_id);
        return $this->TaskStatus->find('first', array(
            'conditions' => array(
                'id' => $task['Task']['task_status_id']
            ),
            'fields' => array(
                'name_' . __l()
            )
        ));
    }

    public function getAllByAppointmentIdOrderByLimitDate($appointment_id)
    {
        return $this->find('all', array(
            'conditions' => array(
                'appointment_id' => $appointment_id
            ),
            'order' => 'limit_date'
        ));
    }

    public function getAllByTaskCustomerByStatusId($status_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'TaskGarage',
                    'table' => 'tasks_garages',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TaskGarage.task_id = Task.id',
                    ),
                ),
                array(
                    'alias' => 'TaskDistributor',
                    'table' => 'tasks_distributors',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TaskDistributor.task_id = Task.id',
                    ),
                ),
            ),
            'conditions' => array(
                'task_status_id' => $status_id
            ),
            'fields' => array(
                'Task.*',
                'TaskGarage.id',
                'TaskDistributor.id',
            )
        ));
    }

    public function tasksToExpired()
    {

        $tasks = $this->findAllByTaskStatusId(ConstantsStatusTasks::PENDING);

        foreach ($tasks as $task) {
            if ($task['Task']['limit_date'] < date('Y-m-d')) {
                $task['Task']['task_status_id'] = ConstantsStatusTasks::EXPIRED;
                $this->validator()->remove('limit_date');
                $this->validator()->remove('user_assigned_id');
                $this->save($task);
                $this->Alert = ClassRegistry::init('Alert');
                $this->Alert->sendAlertAndEmailTaskDeadline($task);
                $this->commit();
            }
        }

        $this->commit();
    }

    public function tasksToExpiredFrance()
    {

        $tasks = $this->getAllByTaskCustomerByStatusId(ConstantsStatusTasks::PENDING);
        foreach ($tasks as $task) {
            if ($task['Task']['limit_date'] < date('Y-m-d') && (!is_null($task['TaskGarage']['id']) || !is_null($task['TaskDistributor']['id']))) {
                $task_tmp = $this->findById($task['Task']['id']);
                if ($task_tmp['Task']['task_status_id'] != ConstantsStatusTasks::EXPIRED) {
                    $task['Task']['task_status_id'] = ConstantsStatusTasks::EXPIRED;
                    $this->validator()->remove('limit_date');
                    $this->validator()->remove('user_assigned_id');
                    $this->save($task);
                    $this->Alert = ClassRegistry::init('Alert');
                    $this->Alert->sendAlertAndEmailTaskDeadline($task);
                    $this->commit();
                }
            }
        }

        $this->commit();
    }
}
