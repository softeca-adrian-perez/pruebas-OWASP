<?php

class TradeShow extends AppModel{
    public $useTable = 'trade_show';
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

    public function saveTradeShowData($data_trade_show) {
        return $this->saveMany($data_trade_show);
    }

    public function deleteTradeShowByConferenceDelegate($delegate_id) {
        return $this->deleteAll(array('conferences_delegates_id' => $delegate_id), false);
    }


}
