<?php

class TaskStatus extends AppModel{
    public $useTable = 'tasks_status';

    public $hasMany = array(
        'Task',
    );

    public $validate = array(
		'name_en' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_fr' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_de' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_nl' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public function search_list_all(){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'name' . __s()
            )
        ));
    }

    public function search_list(){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'conditions' => array(
                'id !=' => ConstantsStatusTasks::EXPIRED
            ),
            'order' => array(
                'name' . __s()
            )
        ));
    }

    public function getExpiredStatus(){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'conditions' => array(
                'id' => ConstantsStatusTasks::EXPIRED
            ),
            'order' => array(
                'name' . __s()
            )
        ));
    }
}