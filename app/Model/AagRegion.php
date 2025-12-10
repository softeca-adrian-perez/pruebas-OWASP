<?php
class AagRegion extends AppModel
{
    public $useTable = 'aag_regions';
    public $displayField = 'name';
    public $order = 'name';

    var $hasMany = array(
        'Country',
        'User'
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
        'image' => array(
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
        'maintenance_search' => array(
            'fields' => array(
                'AagRegion.id',
                'AagRegion.name',
                'AagRegion.code',
            ),
        )
    );

    public function add( $region ){
        $fields = array(
            'AagRegion' => array(
                'name',
                'code',
                'creation_date',
            )
        );

        $this->create();
        $region['AagRegion']['creation_date'] = date('Y-m-d H:i:s');

        $region_bd = $this->guardar($region, $fields);
        if (!$region_bd) {
            return false;
        }
        return true;
    }

    public function edit( $region ){
        $fields = array(
            'AagRegion' => array(
                'id',
                'name',
                'code',
                'creation_date',
            )
        );

        $region['AagRegion']['creation_date'] = date('Y-m-d H:i:s');
        $region_bd = $this->guardar($region, $fields);
        if(!$region_bd){
            return false;
        }
        return true;
    }

    public function search_list(){
        return $this->find('list',array(
            'fields' => array(
                'code',
                'name'
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function region_list(){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function region_list_conditions($conditions){
        return $this->find('list',array(
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

    /**
     * Returns the network associated with the AAG region.
     */
    public function get_aag_region_network($aagRegionId)
    {
        switch ($aagRegionId) {
            case Configure::read('AAG_REGION_ID_UK_IRELAND'):
                return NETWORK_ID_AGN;

            case Configure::read('AAG_REGION_ID_BENELUX'):
                return array(NETWORK_ID_GV, NETWORK_ID_GC);

            default:
                return null;
        }
    }
}