<?php

class SupplierCategory extends AppModel
{
    public $useTable = 'suppliers_categories';

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
        ),
        'name_fr' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_de' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_nl' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'aag_region_id' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_region'
        ),
    );

    private $_queries = array(
        'search_maintenance_categories' => array(
            'fields' => array(
                '*',
            ),
            'order' => 'SupplierCategory.name_en asc'
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    private function _fieldsSupplierCategory()
    {
        $fields = array(
            'SupplierCategory' => array(
                'name_en',
                'name_fr',
                'name_de',
                'name_nl',
                'aag_region_id'
            )
        );
        return $fields;
    }

    public function search_list($aagRegionId)
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'conditions' => array(
                'aag_region_id' => $aagRegionId,
            ),
            'order' => array(
                'name_' . __l()
            )
        ));
    }

    public function search_list_reverse($aagRegionId)
    {
        return $this->find('list', array(
            'fields' => array(
                'name_' . __l(),
                'id',
            ),
            'conditions' => array(
                'aag_region_id' => $aagRegionId,
            ),
            'order' => array(
                'name_' . __l()
            )
        ));
    }

    public function add($supplier_category)
    {
        $fields = $this->_fieldsSupplierCategory();

        $this->create();
        $supplier_category_bd = $this->guardar($supplier_category, $fields);

        if (!$supplier_category_bd) {
            return false;
        }

        return $supplier_category_bd;
    }

    public function edit($supplier_category)
    {
        $fields = $this->_fieldsSupplierCategory();

        $supplier_category_bd = $this->guardar($supplier_category, $fields);

        if (!$supplier_category_bd) {
            return false;
        }

        return $supplier_category_bd;
    }

    public function getByPositionId($position_id, $aagRegionId)
    {
        $this->PositionConfig = ClassRegistry::init('PositionConfig');
        $this->PositionConfigSupplierCategory = ClassRegistry::init('PositionConfigSupplierCategory');
        $suppliers_categories_ids = array();
        $positions_config = $this->PositionConfig->findAllByPositionId($position_id);

        foreach ($positions_config as $position_config) {
            if ($position_config['PositionConfig']['all_suppliers_categories']) {
                return $this->search_list($aagRegionId);
            }

            $position_config_supplier_category = $this->PositionConfigSupplierCategory->findByPositionConfigId($position_config['PositionConfig']['id']);
            if (isset($position_config_supplier_category['PositionConfigSupplierCategory'])) {
                if (!in_array($position_config_supplier_category['PositionConfigSupplierCategory']['supplier_category_id'], $suppliers_categories_ids)) {
                    array_push($suppliers_categories_ids, $position_config_supplier_category['PositionConfigSupplierCategory']['supplier_category_id']);
                }
            }
        }

        return $this->find('list', array(
            'conditions' => array(
                'id' => $suppliers_categories_ids
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'name_' . __l()
            )
        ));
    }
}
