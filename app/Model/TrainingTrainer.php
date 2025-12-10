<?php

class TrainingTrainer extends AppModel
{
    public $useTable = 'trainings_trainers';
    public $displayField = 'name';

    public $validate = array(
        'training_provider_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_provider'
            ),
        ),
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'email' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Email_too_long',
            ),
            'email' => array(
                'rule' => 'email',
                'message' => 'Validation.Email_incorrect_format',
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['TrainingTrainer_name'])) {
            $conditions[] = $this->conditionTrainingTrainerName($fields['TrainingTrainer_name']);
        }
        if (!empty($fields['TrainingProvider_name'])) {
            $conditions[] = $this->conditionTrainingProvider($fields['TrainingProvider_name']);
        }

        return $conditions;
    }

    private function conditionTrainingTrainerName($name)
    {
        return array('TrainingTrainer.id =' => $name);
    }

    private function conditionTrainingProvider($training_provider_id)
    {
        return array('TrainingProvider.id =' => $training_provider_id);
    }

    private $queries = array(
        'home' => array(
            'joins' => array(
                array(
                    'alias' => 'TrainingProvider',
                    'table' => 'trainings_providers',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingProvider.id = TrainingTrainer.training_provider_id',
                    ),
                )
            ),
            'fields' => array(
                'TrainingTrainer.*',
                'TrainingProvider.*'
            ),
            'order' => 'TrainingTrainer.name asc'
        ),
    );

    public function _query($index)
    {
        return $this->queries[$index];
    }

    public function add($trainingTrainer)
    {
        $fields = array(
            'TrainingTrainer' => array(
                'training_provider_id',
                'name',
                'email',
                'phone',
                'creation_date'
            )
        );

        $trainingTrainer['TrainingTrainer']['creation_date'] = date('Y-m-d H:i:s');

        $this->create();
        if ($this->guardar($trainingTrainer, $fields)) {
            return $trainingTrainer;
        }
    }

    public function edit($trainingTrainer)
    {
        $fields = array(
            'TrainingTrainer' => array(
                'training_provider_id',
                'name',
                'email',
                'phone',
                'modification_date'
            )
        );

        $trainingTrainer['TrainingTrainer']['modification_date'] = date('Y-m-d H:i:s');

        return $this->guardar($trainingTrainer, $fields);
    }

    public function searchList()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function getTrainingTrainerByTrainingProviderId($training_provider_name)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'TrainingProvider',
                    'table' => 'trainings_providers',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'TrainingProvider.id = TrainingTrainer.training_provider_id',
                    ),
                )
            ),
            'fields' => array(
                'TrainingTrainer.id',
                'TrainingTrainer.name'
            ),
            'order' => array(
                'TrainingTrainer.name'
            ),
            'conditions' => array(
                'TrainingProvider.name' => $training_provider_name,
            ),
            'group' => array(
                'TrainingTrainer.id'
            ),
        ));
    }

    public function getTrainersNameByIdTrainer($trainer_id)
    {
        $resultArray = array();
        $query = $this->find('first', array(
            'conditions' => array(
                'TrainingTrainer.id' => $trainer_id
            ),
            'fields' => array(
                'TrainingTrainer.id',
                'TrainingTrainer.name'
            ),
        ));

        if ($query) {
            $trainer = $query['TrainingTrainer'];
            $resultArray[$trainer['id']] = $trainer['name'];
        }

        return $resultArray;
    }
}
