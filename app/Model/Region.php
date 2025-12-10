<?php

class Region extends AppModel
{
    public $useTable = 'regions';
    public $displayField = 'name';
    public $order = 'name';

    var $hasMany = array(
        'Country'
    );

    public $validate = array(
        'name' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'code' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_code',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    private $_queries = array(
        'maintenance_search' => array(
            'fields' => array(
                'Region.id',
                'Region.name',
                'Region.code',
            ),
            'order' => 'Region.name asc'
        )
    );

    public function createRegionOnlyCode($region_code)
    { // New Region comes from a JSON, The data comes from a Json left on the server
        $region_tmp = array(
            'Region' => array(
                'code' => $region_code,
                'name' => $region_code,
                'creation_date' => date('Y-m-d H:i:s')
            )
        );

        $this->create();
        $region_create = $this->save($region_tmp);
        if (!$region_create) {
            CakeLog::write('salesDe', 'The region could not be created.' . PHP_EOL);
        }
        CakeLog::write('salesDe', 'The region could be created.' . PHP_EOL);
        $this->commit();
        return $region_create;
    }

    public function add($region)
    {
        $fields = array(
            'Region' => array(
                'name',
                'code',
                'creation_date',
            )
        );

        $this->create();
        $region['Region']['creation_date'] = date('Y-m-d H:i:s');

        $region_bd = $this->guardar($region, $fields);
        if (!$region_bd) {
            return false;
        }
        return true;
    }

    public function edit($region)
    {
        $fields = array(
            'Region' => array(
                'id',
                'name',
                'code',
                'creation_date',
            )
        );

        $region['Region']['creation_date'] = date('Y-m-d H:i:s');
        $region_bd = $this->guardar($region, $fields);
        if (!$region_bd) {
            return false;
        }
        return true;
    }

    public function search_list()
    {
        return $this->find('list', array(
            'fields' => array(
                'code',
                'name'
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function search_list_code()
    {
        return $this->find('list', array(
            'fields' => array(
                'code',
                'id',
            ),
        ));
    }

    public function region_list()
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

    public function region_list_conditions($conditions)
    {
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

    public function getRegionsByContact($contact_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'ContactRegion',
                    'table' => 'contacts_regions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Region.id = ContactRegion.region_id'
                    )
                ),
            ),
            'conditions' => array(
                'ContactRegion.contact_id' => $contact_id
            ),
            'fields' => array(
                'Region.code',
            ),
            'order' => array(
                'Region.name',
            )
        ));
    }

    public function region_list_used_by_cv()
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'ContactRegion',
                    'table' => 'contacts_regions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ContactRegion.region_id = Region.id'
                    )
                ),
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ContactRegion.contact_id = Contact.id'
                    )
                )
            ),
            'conditions' => array(
                'Contact.position_id' => ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID
            ),
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function region_list_used_by_lv()
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'ContactRegion',
                    'table' => 'contacts_regions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ContactRegion.region_id = Region.id'
                    )
                ),
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ContactRegion.contact_id = Contact.id'
                    )
                )
            ),
            'conditions' => array(
                'Contact.position_id' => ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID
            ),
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function getRegionsListCodeByContact($contact_id)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'ContactRegion',
                    'table' => 'contacts_regions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Region.id = ContactRegion.region_id'
                    )
                ),
            ),
            'conditions' => array(
                'ContactRegion.contact_id' => $contact_id
            ),
            'fields' => array(
                'Region.id',
                'Region.code',
            ),
            'order' => array(
                'Region.name',
            )
        ));
    }

    public function getRegionsListByContact($contact_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'ContactRegion',
                    'table' => 'contacts_regions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Region.id = ContactRegion.region_id'
                    )
                ),
            ),
            'conditions' => array(
                'ContactRegion.contact_id' => $contact_id
            ),
            'fields' => array(
                'Region.id',
            ),
            'order' => array(
                'Region.name',
            )
        ));
    }
}
