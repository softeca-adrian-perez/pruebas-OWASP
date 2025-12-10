<?php

class DebriefTopic extends AppModel
{
    public $useTable = 'debrief_topics';

    public $validate = array(
        'name_en' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'Validation.Name_must_be_unique',
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

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    private $_queries = array(
        'search_maintenance_debrief_topics' => array(
            'fields' => array(
                'DebriefTopic.*',
            ),
            'order' => 'DebriefTopic.id desc'
        )

    );

    public function getList()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'DebriefTopic.id',
                    'DebriefTopic.name' . __s(),
                ),
                'order' => 'DebriefTopic.name' . __s(),
            )
        );
    }

    public function add($debrief_topic)
    {
        $fields = array(
            'DebriefTopic' => array(
                'name_en',
                'name_fr',
                'name_de',
            )
        );

        $this->create();
        $debrief_topic_bd = $this->guardar($debrief_topic, $fields);

        if (!$debrief_topic_bd) {
            return false;
        }

        return $debrief_topic_bd;
    }

    public function edit($debrief_topic)
    {
        $fields = array(
            'DebriefTopic' => array(
                'name_en',
                'name_fr',
                'name_de',
            )
        );

        $debrief_topic_bd = $this->guardar($debrief_topic, $fields);
        if (!$debrief_topic_bd) {
            return false;
        }

        return $debrief_topic_bd;
    }

    public function increment_use($debrief_topic_id)
    {
        $fields = array(
            'DebriefTopic' => array(
                'id',
                'uses'
            )
        );

        $debrief_topic = $this->findById($debrief_topic_id);
        $debrief_topic['DebriefTopic']['uses']++;

        $debrief_topic_bd = $this->guardar($debrief_topic, $fields);
        if (!$debrief_topic_bd) {
            return false;
        }

        return $debrief_topic_bd;
    }

    public function getDebriefTopic()
    {
        return $this->find('all', array(
            'fields' => array(
                'DebriefTopic.*',
            ),
            'order' => array('DebriefTopic.uses DESC'),
        ));
    }

    public function getListTopicsInUse()
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'AppointmentTopic',
                    'table' => 'appointments_topics',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'DebriefTopic.id = AppointmentTopic.topic_id'
                    )
                ),
            ),
            'fields' => array(
                'DebriefTopic.id',
                'AppointmentTopic.appointment_id'
            )
        ));
    }
}
