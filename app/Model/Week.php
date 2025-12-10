<?php

class Week extends AppModel{
    public $useTable = 'weeks';
    public $hasMany = array(
        'Room' => array(
            'className' => 'Room',
            'foreignKey' => 'weeks_id',
        ),
        'TradeShow' => array(
            'className' => 'Room',
            'foreignKey' => 'weeks_id',
        ),
        'Dinner' => array(
            'className' => 'Dinner',
            'foreignKey' => 'weeks_id',
        ),
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

    public function search_list(){
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'id'
            )
        ));
    }

    public function add_week($data) 
    {
		$fields = array(
			'Week' => array(
				'name_en',
				'name_fr',
				'name_de',
			)
		);
		$this->create();

		$week_bd = $this->guardar($data, $fields);
		if (!$week_bd) {
			return false;
		}

		$this->commit();
		return $week_bd;
	}

    public function edit_week($data)
	{
		$fields = array(
			'Week' => array(
				'name_en',
				'name_fr',
				'name_de',
			)
		);

		$week_bd = $this->guardar($data, $fields);
		if (!$week_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getData() {
		return $this->find('all', array(
			'order' => array(
				'name_' . __l() => 'asc'
				)
			)	
		);
	}

    public function getListWeekend(){
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'id'
            ),
            'conditions' => array(
                'Or' => array(
                    array(
                        'Week.id' => ConstantsWeeks::THURSDAY
                    ),
                    array(
                        'Week.id' => ConstantsWeeks::FRIDAY
                    ),
                ),
            ),
        ));
    }

    public function getWeekFromRoom($delegate_id)
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'name_' . __l(), 'id'
                ),
                'joins' => array(
                    array(
                        'table' => 'room',
                        'alias' => "Room",
                        'type' => 'INNER',
                        'conditions' => array(
                            'Room.weeks_id = Week.id',
                        ),
                    )
                ),
                'conditions' => array(
                    'Room.conferences_delegates_id' => $delegate_id,
                ),
                'order' => 'Week.id'
            )
        );
    }

    public function getWeekFromTradeShow($delegate_id)
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'name_' . __l(), 'id'
                ),
                'joins' => array(
                    array(
                        'table' => 'trade_show',
                        'alias' => "TradeShow",
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradeShow.weeks_id = Week.id',
                        ),
                    )
                ),
                'conditions' => array(
                    'TradeShow.conferences_delegates_id' => $delegate_id,
                ),
                'order' => 'Week.id'
            )
        );
    }

    public function getWeekFromDinner($delegate_id)
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'name_' . __l(), 'id'
                ),
                'joins' => array(
                    array(
                        'table' => 'dinner',
                        'alias' => "Dinner",
                        'type' => 'INNER',
                        'conditions' => array(
                            'Dinner.weeks_id = Week.id',
                        ),
                    )
                ),
                'conditions' => array(
                    'Dinner.conferences_delegates_id' => $delegate_id,
                ),
                'order' => 'Week.id'
            )
        );
    }
    
}