<?php

class TrainingProvider extends AppModel
{
    public $useTable = 'trainings_providers';
    public $displayField = 'name';

    public $validate = array(
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
        'phone' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_PHONE),
                'message' => 'Validation.Phone_too_long',
                'allowEmpty' => true,
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['TrainingProvider_name'])) {
            $conditions[] = $this->conditionTrainingProvider($fields['TrainingProvider_name']);
        }

        return $conditions;
    }

    private function conditionTrainingProvider($training_provider)
    {
        return array('TrainingProvider.id =' => $training_provider);
    }


    private $queries = array(
        'home' => array(
            'conditions' => array(),
            'order' => 'TrainingProvider.name asc'
        )
    );

    public function _query($index)
    {
        return $this->queries[$index];
    }

    public function add($trainingProvider)
    {
        $fields = array(
            'TrainingProvider' => array(
                'name',
                'email',
                'phone',
                'creation_date'
            )
        );

        $trainingProvider['TrainingProvider']['creation_date'] = date('Y-m-d H:i:s');

        $this->create();
        if ($this->guardar($trainingProvider, $fields)) {
            return $trainingProvider;
        }
    }

    public function edit($trainingProvider)
    {
        $fields = array(
            'TrainingProvider' => array(
                'name',
                'email',
                'phone',
                'modification_date'
            )
        );

        $trainingProvider['TrainingProvider']['modification_date'] = date('Y-m-d H:i:s');

        return $this->guardar($trainingProvider, $fields);
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

    public function getByProviderId($provider_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'TrainingProvider.id' => $provider_id,
                ),
            )
        );
    }
}
