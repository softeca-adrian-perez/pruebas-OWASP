<?php

class Dinner extends AppModel{
    public $useTable = 'dinner';
    public $belongsTo = array(
        'Week' => array(
            'className' => 'Week',
            'foreignKey' => 'weeks_id',
        ),
        'ConferenceDelegate' => array(
            'className' => 'ConferenceDelegate',
            'foreignKey' => 'conferences_delegates_id',
        )
    );

    public function saveDinnerData($data_dinner) {
        return $this->saveMany($data_dinner);
    }

    public function deleteDinnerByConferenceDelegate($delegate_id) {
        return $this->deleteAll(array('conferences_delegates_id' => $delegate_id), false);
    }


}
