<?php

class Venue extends AppModel
{
    public $useTable = 'venues';
    public $displayField = 'name';

    public $hasOne = array(
        'AagRegion'
    );


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
        'address_1' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_address'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'address_2' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_address'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'aag_region_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_region',
            ),
        ),
        'town' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'post_code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'telephone' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['name'])) {
            $conditions[] = $this->_conditionName($fields['name']);
        }
        if (!empty($fields['address_1'])) {
            $conditions[] = $this->_conditionAddress1($fields['address_1']);
        }
        if (!empty($fields['address_2'])) {
            $conditions[] = $this->_conditionAddress2($fields['address_2']);
        }
        if (!empty($fields['latitude'])) {
            $conditions[] = $this->_conditionLatitude($fields['latitude']);
        }
        if (!empty($fields['longitude'])) {
            $conditions[] = $this->_conditionLongitude($fields['longitude']);
        }
        if (!empty($fields['town'])) {
            $conditions[] = $this->_conditionTown($fields['town']);
        }
        if (!empty($fields['sales_area_id'])) {
            $conditions[] = $this->_conditionSalesArea($fields['sales_area_id']);
        }
        if (!empty($fields['post_code'])) {
            $conditions[] = $this->_conditionPostcode($fields['post_code']);
        }
        if (!empty($fields['telephone'])) {
            $conditions[] = $this->_conditionTelephone($fields['telephone']);
        }
        if (isset($fields['active']) && $fields['active'] !== '') {
            $conditions[] = $this->_conditionActive($fields['active']);
        }
        if (!empty($fields['aag_region_id'])) {
            $conditions[] = $this->_conditionAagRegion($fields['aag_region_id']);
        }
        if (!empty($fields['venue_type_id'])) {
            $conditions[] = $this->_conditionVenueType($fields['venue_type_id']);
        }

        return $conditions;
    }

    private function _conditionName($name)
    {
        return array('Venue.name LIKE' => '%' . $name . '%');
    }

    private function _conditionAddress1($address_1)
    {
        return array('Venue.address_1 LIKE' => '%' . $address_1 . '%');
    }

    private function _conditionAddress2($address_2)
    {
        return array('Venue.address_2 LIKE' => '%' . $address_2 . '%');
    }

    private function _conditionLatitude($latitude)
    {
        return array('Venue.latitude ' => $latitude);
    }

    private function _conditionLongitude($longitude)
    {
        return array('Venue.longitude ' => $longitude);
    }

    private function _conditionTown($town)
    {
        return array('Venue.town LIKE' => '%' . $town . '%');
    }

    private function _conditionSalesArea($sales_area_id)
    {
        return array('Venue.sales_area_id ' => $sales_area_id);
    }

    private function _conditionAagRegion($aag_region_id)
    {
        return array('Venue.aag_region_id' => $aag_region_id);
    }

    private function _conditionPostcode($post_code)
    {
        return array('Venue.post_code LIKE' => '%' . $post_code . '%');
    }

    private function _conditionTelephone($telephone)
    {
        return array('Venue.telephone LIKE' => '%' . $telephone . '%');
    }

    private function _conditionActive($active)
    {
        if ($active == '1' or $active == '0') {
            return array('Venue.active' => $active);
        }
    }

    private function _conditionVenueType($venue_type_id)
    {
        return array('Venue.venue_type_id' => $venue_type_id);
    }

    private $_queries = array(
        'home' => array(
            'joins' => array(
                array(
                    'alias' => 'VenueType',
                    'table' => 'venues_types',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'VenueType.id = Venue.venue_type_id',
                    ),
                ),
            ),
            'fields' => array(
                'Venue.*',
                'VenueType.*',
            ),
            'order' => 'Venue.name asc',
        )
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function getData()
    {
        return $this->find(
            'all',
            array(
                'order' => array(
                    'name' => 'asc'
                )
            )
        );
    }

    public function add($venue)
    {
        $fields = array(
            'Venue' => array(
                'name',
                'address_1',
                'address_2',
                'latitude',
                'longitude',
                'town',
                'post_code',
                'telephone',
                'active',
                'aag_region_id',
                'venue_type_id',
                'sales_area_id'
            )
        );

        $this->create();
        if ($this->guardar($venue, $fields)) {
            return $venue;
        }
    }

    public function edit($venue)
    {
        $fields = array(
            'Venue' => array(
                'name',
                'address_1',
                'address_2',
                'latitude',
                'longitude',
                'town',
                'post_code',
                'telephone',
                'active',
                'aag_region_id',
                'venue_type_id',
                'sales_area_id'
            )
        );

        return $this->guardar($venue, $fields);
    }

    public function deactivateActiveByVenue($venue)
    {
        $fields = array(
            'Venue' => array(
                'name',
                'address_1',
                'address_2',
                'latitude',
                'longitude',
                'town',
                'post_code',
                'telephone',
                'active',
                'aag_region_id'
            )
        );

        $venue['Venue']['active'] = ConstantsBooleans::NO;

        return $this->guardar($venue, $fields);
    }

    public function deleteVenueById($venue_id)
    {
        $this->delete($venue_id);
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

    public function search_list_conditions($aag_region_id)
    {
        $conditions = array();
        $conditions = array('Venue.aag_region_id' => $aag_region_id);

        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            ),
            'conditions' => $conditions,
            'order' => array(
                'name'
            )
        ));
    }

    public function search_list_conditions_conferences_form($aag_region_id)
    {
        $conditions = array('Venue.active' => ConstantsBooleans::YES);
        $conditions[] = array(
            'Venue.aag_region_id' => $aag_region_id,
        );

        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            ),
            'conditions' => $conditions,
            'order' => array(
                'name'
            )
        ));
    }

    public function getVenueNameById($venue_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'Venue.id' => $venue_id,
            ),
        ));
    }

    public function getVenuesHotel($aag_region_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'VenueType',
                        'table' => 'venues_types',
                        'type' => 'INNER',
                        'conditions' => array(
                            'VenueType.id = Venue.venue_type_id'
                        )
                    )
                ),
                'conditions' => array(
                    'Venue.venue_type_id' => ConstantsVenuesTypes::HOTEL,
                    'Venue.aag_region_id' => $aag_region_id
                ),
                'fields' => array(
                    'Venue.name'
                ),
                'order' => 'Venue.name'
            )
        );
    }

    public function getPossiblesVenuesAjax($conditions, $aag_region_id)
    {
        $venues = $this->getPossiblesVenuesQuery($conditions, $aag_region_id);
        $venues = Hash::combine($venues, '{n}.Venue.id', array('%s', '{n}.Venue.name'));

        return $venues;
    }

    public function getPossiblesVenuesQuery($conditions_ajax = array(), $aag_region_id)
    {
        $conditions_region = array('Venue.aag_region_id' => $aag_region_id);

        if (isset($conditions_ajax['name']) && !empty($conditions_ajax['name'])) {
            $conditions_ajax = array(
                'OR' => array(
                    'Venue.name LIKE' => '%' . $conditions_ajax['name'] . '%',
                )
            );
        }

        return $this->find(
            'all',
            array(
                'conditions' => array(
                    $conditions_ajax,
                    $conditions_region,
                ),
                'fields' => array(
                    'Venue.id',
                    'Venue.name'
                ),
                'group' => array(
                    'Venue.id'
                ),
            )
        );
    }

    public function getVenuesNameByIdVenue($venue_id)
    {
        $resultArray = array();
        $query = $this->find('first', array(
            'conditions' => array(
                'Venue.id' => $venue_id
            ),
            'fields' => array(
                'Venue.id',
                'Venue.name'
            ),
        ));

        if ($query) {
            $venue = $query['Venue'];
            $resultArray[$venue['id']] = $venue['name'];
        }
        return $resultArray;
    }

    public function dynamicTypeVenuesExportQuery($query_type, $aag_region_id, $conditions)
    {
        return $this->find($query_type, array(
            'conditions' => array(
                $conditions,
                'Venue.aag_region_id' => $aag_region_id
            ),
            'joins' => array(
                array(
                    'alias' => 'VenueType',
                    'table' => 'venues_types',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'VenueType.id = Venue.venue_type_id',
                    ),
                ),
            ),
            'fields' => array(
                'Venue.*',
                'VenueType.*',
            ),
            'order' => 'Venue.name',
        ));
    }
}
