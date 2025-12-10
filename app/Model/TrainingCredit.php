<?php

class TrainingCredit extends AppModel
{
	public $useTable = 'trainings_credits';

    public $validate = array(
        'pound' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_number'
            ),
        ),
        'credit' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_number'
            ),
        ),
    );

    public function checkBD() {
    
        $existingRow = $this->find('first', array(
            'fields' => array(
                'TrainingCredit.*'
            ),
        ));
    
        if ($existingRow) {
            return $existingRow;
        } else {
            return null;
        }
    }


    
    public function add( $training_credit ){
        $fields = array(
            'TrainingCredit' => array(
                'pound',
                'credit',
            )
        );

        $this->create();
        if($this->guardar($training_credit, $fields)){
            return $training_credit;
        }

    }

	public function edit( $training_credit ){
        $fields = array(
            'TrainingCredit' => array(
                'id',
                'pound',
                'credit',
            )
        );

        $training_credit_id = $this->find('first', array(
            'TrainingCredit' => array(
                'id',
            )
        ));

        $training_credit['TrainingCredit']['id'] = $training_credit_id['TrainingCredit']['id'];
        $training_credit_bd = $this->guardar($training_credit, $fields);
        
        if(!$training_credit_bd){
            return false;
        }
        $this->commit();
        return $training_credit_bd;
    }
    
}
