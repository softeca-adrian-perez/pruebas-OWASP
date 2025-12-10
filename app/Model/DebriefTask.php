<?php

class DebriefTask extends AppModel
{
    public $useTable = 'debrief_tasks';

    public $validate = array(
        'title_en' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_title'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'title_fr' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_title'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'title_de' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_title'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'title_nl' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_title'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'description_en' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_description'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'description_fr' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_description'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'description_de' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_description'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'description_nl' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_description'
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
        'search_maintenance_debrief_tasks' => array(
            'fields' => array(
                'DebriefTask.*',
            ),
            'order' => 'DebriefTask.id desc'
        )

    );

    public function add($debrief_task)
    {
        $fields = array(
            'DebriefTask' => array(
                'title_en',
                'title_fr',
                'title_de',
                'description_en',
                'description_fr',
                'description_de',
                'contact_list_id',
                'user_assigned_id',
                'specific_user'
            )
        );

        $debrief_task = $this->fill_language_fields($debrief_task);

        $this->create();
        $debrief_task_bd = $this->guardar($debrief_task, $fields);

        if (!$debrief_task_bd) {
            return false;
        }

        return $debrief_task_bd;
    }

    public function edit($debrief_task)
    {
        $fields = array(
            'DebriefTask' => array(
                'title_en',
                'title_fr',
                'title_de',
                'description_en',
                'description_fr',
                'description_de',
                'contact_list_id',
                'user_assigned_id',
                'specific_user'
            )
        );

        $debrief_task = $this->fill_language_fields($debrief_task);

        $debrief_task_bd = $this->guardar($debrief_task, $fields);
        if (!$debrief_task_bd) {
            return false;
        }

        return $debrief_task_bd;
    }

    public function increment_use($debrief_task_id)
    {
        $fields = array(
            'DebriefTask' => array(
                'id',
                'uses'
            )
        );

        $debrief_task = $this->findById($debrief_task_id);
        $debrief_task['DebriefTask']['uses']++;

        $debrief_task_bd = $this->guardar($debrief_task, $fields);
        if (!$debrief_task_bd) {
            return false;
        }

        return $debrief_task_bd;
    }

    private function fill_language_fields($debrief_task)
    {
        if (!isset($debrief_task['DebriefTask']['user_assigned_id'])) {
            $debrief_task['DebriefTask']['user_assigned_id'] = null;
        }
        if (!isset($debrief_task['DebriefTask']['contact_list_id'])) {
            $debrief_task['DebriefTask']['contact_list_id'] = null;
        }

        if (!empty($debrief_task['DebriefTask']['title_en'])) {
            $debrief_task['DebriefTask']['title_fr'] = $debrief_task['DebriefTask']['title_en'];
            $debrief_task['DebriefTask']['title_de'] = $debrief_task['DebriefTask']['title_en'];
        } else if (!empty($debrief_task['DebriefTask']['title_fr'])) {
            $debrief_task['DebriefTask']['title_en'] = $debrief_task['DebriefTask']['title_fr'];
            $debrief_task['DebriefTask']['title_de'] = $debrief_task['DebriefTask']['title_fr'];
        } else if (!empty($debrief_task['DebriefTask']['title_de'])) {
            $debrief_task['DebriefTask']['title_en'] = $debrief_task['DebriefTask']['title_de'];
            $debrief_task['DebriefTask']['title_fr'] = $debrief_task['DebriefTask']['title_de'];
        }

        if (!empty($debrief_task['DebriefTask']['description_en'])) {
            $debrief_task['DebriefTask']['description_fr'] = $debrief_task['DebriefTask']['description_en'];
            $debrief_task['DebriefTask']['description_de'] = $debrief_task['DebriefTask']['description_en'];
        } else if (!empty($debrief_task['DebriefTask']['description_fr'])) {
            $debrief_task['DebriefTask']['description_en'] = $debrief_task['DebriefTask']['description_fr'];
            $debrief_task['DebriefTask']['description_de'] = $debrief_task['DebriefTask']['description_fr'];
        } else if (!empty($debrief_task['DebriefTask']['description_de'])) {
            $debrief_task['DebriefTask']['description_en'] = $debrief_task['DebriefTask']['description_de'];
            $debrief_task['DebriefTask']['description_fr'] = $debrief_task['DebriefTask']['description_de'];
        }

        return $debrief_task;
    }

    public function getDebriefTask()
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'DebriefTaskGarage',
                    'table' => 'debrief_tasks_garages',
                    'type' => 'LEFT',
                    'conditions' => 'DebriefTaskGarage.debrief_task_id = DebriefTask.id'
                ),
                array(
                    'alias' => 'DebriefTaskDistributor',
                    'table' => 'debrief_tasks_distributors',
                    'type' => 'LEFT',
                    'conditions' => 'DebriefTaskDistributor.debrief_task_id = DebriefTask.id'
                ),
            ),
            'fields' => array(
                'DebriefTask.*',
                'DebriefTaskGarage.*',
                'DebriefTaskDistributor.*',
            ),
            'group' => array('DebriefTask.id'),
            'order' => array('DebriefTask.uses DESC'),
        ));
    }
}
