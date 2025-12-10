<?php

class Conference extends AppModel
{
    public $useTable = 'conferences';
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
        'duration' => array(
            array(
                'rule' => array('comparison', '>', 0),
                'message' => 'Validation.Greater_than_zero',
                'required' => true,
            ),
            array(
                'rule' => 'numeric',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_number',
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['name'])) {
            $conditions[] = $this->_conditionName($fields['name']);
        }
        if (!empty($fields['start_date'])) {
            $conditions[] = $this->_conditionStartDate($fields['start_date']);
        }
        if (!empty($fields['duration'])) {
            $conditions[] = $this->_conditionDuration($fields['duration']);
        }
        if (!empty($fields['venue_id'])) {
            $conditions[] = $this->_conditionVenue($fields['venue_id']);
        }

        return $conditions;
    }

    private function _conditionName($name)
    {
        return array('Conference.name LIKE' => '%' . $name . '%');
    }

    private function _conditionStartDate($start_date)
    {
        return array('Conference.start_date LIKE' => '%' . Fecha::toFormatoBd($start_date) . '%');
    }

    private function _conditionDuration($duration)
    {
        return array('Conference.duration' => $duration);
    }

    private function _conditionVenue($venue)
    {
        return array('Venue.id' => $venue);
    }

    public function _query($aag_region_id)
    {
        $query = array(
            'joins' => array(
                array(
                    'alias' => 'Venue',
                    'table' => 'venues',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Venue.id = Conference.venue_id',
                    ),
                )
            ),
            'conditions' => array(
                'Conference.aag_region_id' => $aag_region_id
            ),
            'fields' => array(
                'Conference.*',
                'Venue.name'
            ),
            'order' => 'Conference.name asc'
        );

        return $query;
    }

    public function add($conference)
    {
        $fields = array(
            'Conference' => array(
                'name',
                'start_date',
                'duration',
                'venue_id',
                'status',
                'aag_region_id'
            )
        );
        $conference['Conference']['start_date'] = Fecha::toFormatoBd($conference['Conference']['start_date']);
        $conference['Conference']['aag_region_id'] = CakeSession::read('Auth.User.aag_region_id');
        $this->create();

        $conference_bd = $this->guardar($conference, $fields);
        if (!$conference_bd) {
            return false;
        }

        return $conference_bd;
    }

    public function edit($conference)
    {
        $fields = array(
            'Conference' => array(
                'name',
                'start_date',
                'duration',
                'venue_id',
                'status',
            )
        );
        $conference['Conference']['start_date'] = Fecha::toFormatoBd($conference['Conference']['start_date']);

        $conference_bd = $this->guardar($conference, $fields);
        if (!$conference_bd) {
            return false;
        }

        return $conference_bd;
    }

    public function toggle_status($conference_id)
    {
        $fields = array(
            'Conference' => array(
                'status',
            )
        );
        $conference_bd = $this->findById($conference_id);

        if ($conference_bd['Conference']['status'] == ConstantsBooleans::YES) {
            $conference_bd['Conference']['status'] = ConstantsBooleans::NO;
        } else {
            $conference_bd['Conference']['status'] = ConstantsBooleans::YES;
        }

        if (!$this->guardar($conference_bd, $fields)) {
            return false;
        }

        return true;
    }

    public function search_list()
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

    public function getConferenceNameById($conference_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'Conference.id' => $conference_id,
            ),
        ));
    }

    public function getCompleteListRegionActive($aag_region_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Conference.aag_region_id' => $aag_region_id,
                    'status' => ConstantsBooleans::YES,
                ),
                'fields' => array(
                    'Conference.id',
                    'Conference.name'
                ),
                'order' => array(
                    'Conference.name'
                ),
            )
        );
    }

    public function dynamicTypeConferencesExportQuery($query_type, $aag_region_id, $conditions)
    {
        return $this->find($query_type, array(
            'joins' => array(
                array(
                    'alias' => 'Venue',
                    'table' => 'venues',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Venue.id = Conference.venue_id',
                    ),
                ),
            ),
            'conditions' => array(
                $conditions,
                'Conference.aag_region_id' => $aag_region_id
            ),
            'fields' => array(
                'Conference.*',
                'Venue.name',
            ),
            'order' => array(
                'Conference.id'
            ),
            'group' => array(),
        ));
    }
}
