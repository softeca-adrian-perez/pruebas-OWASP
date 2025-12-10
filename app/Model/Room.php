<?php

class Room extends AppModel{
    public $useTable = 'room';
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

    public function saveRoomData($data_room) {
        return $this->saveMany($data_room);
    }

    public function deleteRoomByConferenceDelegate($delegate_id) {
        return $this->deleteAll(array('conferences_delegates_id' => $delegate_id), false);
    }


}
