<?php
class ValueAddedSupplier extends AppModel{
    public $useTable = 'value_added_suppliers';

    public $validate = array(
        'title' => array(
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
        'supplier_url' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.InvalidUrl',
            ),
            'invalidUrl' => array(
                'allowEmpty' => true,
                'rule' => array('url', true),
                'message' => 'Validation.InvalidUrl',
            ),
        ),
        'aag_region_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_region',
            ),
        )
    );

    public function conditions($fields, $aagRegionId)
    {
        $conditions = array();
        $conditions[] = array('ValueAddedSupplier.aag_region_id' => $aagRegionId);
        if (!empty($fields['title'])) {
            $conditions[] = $this->_conditionTitle($fields['title']);
        }
        if (!empty($fields['supplier_url'])) {
            $conditions[] = $this->_conditionUrl($fields['supplier_url']);
        }
        return $conditions;
    }

    public function _conditionTitle($title)
    {
        return array('ValueAddedSupplier.title LIKE' => '%' . $title . '%');
    }

    public function _conditionUrl($url)
    {
        return array('ValueAddedSupplier.supplier_url LIKE' => '%' . $url . '%');
    }

    private $_queries = array(
        'home' => array(
            'fields' => array(
                'ValueAddedSupplier.guid',
                'ValueAddedSupplier.logo_image',
                'ValueAddedSupplier.title',
                'ValueAddedSupplier.supplier_url',
            )
        )
    );

    public function _query( $index ){
        return $this->_queries[$index];
    }

    public function addValueAddedSupplier($valueAddedSupplier)
    {
        $fields = array(
            'ValueAddedSupplier' => array(
                'guid',
                'aag_region_id',
                'logo_image',
                'title',
                'description',
                'supplier_url',
                'updated_at',
                'created_at'
            )
        );

        $valueAddedSupplier['ValueAddedSupplier']['created_at'] = date('Y-m-d H:i:s');
        $valueAddedSupplier['ValueAddedSupplier']['updated_at'] = date('Y-m-d H:i:s');
        $valueAddedSupplier['ValueAddedSupplier']['guid'] = CakeText::uuid();

        $this->create();

        return $this->guardar($valueAddedSupplier, $fields);
    }

    public function editValueAddedSupplier($valueAddedSupplier)
    {
        $fields = array(
            'ValueAddedSupplier' => array(
                'logo_image',
                'title',
                'description',
                'supplier_url',
                'updated_at'
            )
        );

        $valueAddedSupplier['ValueAddedSupplier']['updated_at'] = date('Y-m-d H:i:s');

        return $this->guardar($valueAddedSupplier, $fields);
    }

    public function deleteValueAddedSupplier($guid)
    {
        $valueAddedSupplierToDelete = $this->findByGuid($guid);
        if (isset($valueAddedSupplierToDelete['ValueAddedSupplier']['id'])) {
            return $this->delete($valueAddedSupplierToDelete['ValueAddedSupplier']['id']);
        }

        return false;
    }
}
