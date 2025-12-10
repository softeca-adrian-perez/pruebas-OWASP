<?php
class Brand extends AppModel
{
    public $useTable = 'brands';
    public $order = 'Brand.name';

    public $hasOne = array(
        'Supplier',
        'BrandImage'
    );

    public $hasMany = array(
        'Product'
    );

    public $validate = array(
        'name' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_name',
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
        if (!empty($fields['supplier'])) {
            $conditions[] = $this->_conditionSupplier($fields['supplier']);
        }
        if (isset($fields['active'])) {
            $conditions[] = $this->_conditionActive($fields['active']);
        }
        if (!empty($fields['aag_region_id'])) {
            $conditions[] = $this->_conditionAagRegion($fields['aag_region_id']);
        }
        return $conditions;
    }

    public function _conditionName($name)
    {
        return array('Brand.name LIKE' => '%' . $name . '%');
    }

    public function _conditionSupplier($supplier_id)
    {
        return array('Brand.supplier_id' => $supplier_id);
    }

    public function _conditionActive($active)
    {
        //If you get a 2, recharge as is.
        if ($active == '1' or $active == '0') {
            return array('Brand.active' => $active);
        }
    }

    public function _conditionAagRegion($aag_region_id)
    {
        return array('Supplier.aag_region_id' => $aag_region_id);
    }

    public function _query($aag_region_id)
    {
        $query = array(
            'joins' => array(
                array(
                    'alias' => 'Supplier',
                    'table' => 'suppliers',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Brand.supplier_id = Supplier.id',
                        'Supplier.aag_region_id' => $aag_region_id
                    ),
                ),
                array(
                    'alias' => 'BrandImage',
                    'table' => 'brands_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Brand.id = BrandImage.brand_id',
                    ),
                ),
                array(
                    'alias' => 'SupplierImage',
                    'table' => 'suppliers_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Brand.supplier_id = SupplierImage.supplier_id',
                    ),
                )
            ),
            'fields' => array(
                'Brand.*',
                'BrandImage.*',
                'SupplierImage.*',
            ),
            'order' => 'Brand.name asc'
        );

        return $query;
    }

    public function add($brand)
    {
        $fields = array(
            'Brand' => array(
                'supplier_id',
                'name',
                'active',
            )
        );

        $this->create();
        if ($tmp = $this->save($brand, true, $fields)) {
            return $tmp;
        }
        return false;
    }

    public function edit($brand)
    {
        $fields = array(
            'Brand' => array(
                'supplier_id',
                'name',
                'active',
            )
        );

        if ($tmp = $this->save($brand, true, $fields)) {
            return $tmp;
        }
        return false;
    }

    public function getAllProductsByBrand()
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Product',
                    'table' => 'products',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Brand.id = Product.brand_id',
                    ),
                ),
            ),
            'fields' => array(
                'Product.name',
            ),
        ));
    }

    public function getAllAboutBrands()
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'BrandImage',
                    'table' => 'brands_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Brand.id = BrandImage.brand_id',
                    ),
                ),
            ),
            'fields' => array(
                'Brand.*',
                'BrandImage.*',
            ),
        ));
    }

    public function getAllAboutBrandsConditions($aag_region_id)
    {
        $joins = array(
            array(
                'alias' => 'BrandImage',
                'table' => 'brands_images',
                'type' => 'LEFT',
                'conditions' => array(
                    'Brand.id = BrandImage.brand_id',
                ),
            ),
            array(
                'alias' => 'Supplier',
                'table' => 'suppliers',
                'type' => 'INNER',
                'conditions' => array(
                    'Supplier.id = Brand.supplier_id',
                    'Supplier.aag_region_id' => $aag_region_id
                )
            )
        );

        return $this->find('all', array(
            'joins' => $joins,
            'fields' => array(
                'Brand.*',
                'BrandImage.*',
            ),
        ));
    }

    public function getAllAboutBrandsActive()
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'BrandImage',
                    'table' => 'brands_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Brand.id = BrandImage.brand_id',
                    ),
                ),
            ),
            'conditions' => array(
                'Brand.active' => true,
            ),
            'fields' => array(
                'Brand.*',
                'BrandImage.*',
            ),
        ));
    }

    public function getAllAboutBrandId($brand_id)
    {
        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'BrandImage',
                    'table' => 'brands_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Brand.id = BrandImage.brand_id',
                    ),
                ),
                array(
                    'alias' => 'SupplierImage',
                    'table' => 'suppliers_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Brand.supplier_id = SupplierImage.supplier_id',
                    ),
                ),
                array(
                    'alias' => 'Product',
                    'table' => 'products',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Brand.id = Product.brand_id',
                    ),
                ),
            ),
            'fields' => array(
                'Brand.*',
                'BrandImage.*',
                'SupplierImage.*',
                'Product.*',
            ),
            'conditions' => array(
                'Brand.id' => $brand_id,
            ),
        ));
    }

    public function getAllBrandsBySupplierId($supplier_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'BrandImage',
                    'table' => 'brands_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Brand.id = BrandImage.brand_id',
                    ),
                ),
            ),
            'fields' => array(
                'Brand.*',
                'BrandImage.*',
            ),
            'conditions' => array(
                'Brand.supplier_id' => $supplier_id,
                'Brand.active' => ConstantsBooleans::ACTIVE,
            ),
        ));
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
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Supplier',
                    'table' => 'suppliers',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Supplier.id = Brand.supplier_id',
                        'Supplier.aag_region_id' => $aag_region_id
                    ),
                )
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

    public function createBrandFR($supplier_json, $supplier_create)
    { // New supplier comes from FRANCE JSON

        $brand_tmp = array(
            'Brand' => array(
                'id' => $supplier_create['Supplier']['id'],
                'supplier_id' => $supplier_create['Supplier']['id'],
                'name' => isset($supplier_json['identite']['raison_sociale']) ? $supplier_json['identite']['raison_sociale'] : '--',
                'active' => ($supplier_json['actif']) ? $supplier_json['actif'] : ConstantsBooleans::NO_ACTIVE,
            )
        );

        $this->create();
        $brand_bd = $this->save($brand_tmp);
        if (!$brand_bd) {
            CakeLog::write('updates-france', 'The Brand with ID ISA ' . $supplier_json['id_isa'] . ' could not be created.' . PHP_EOL);
        }

        return $brand_bd;
    }

    public function updateBrandFR($supplier_json, $supplier_update)
    { // New supplier comes from FRANCE JSON

        $brand_exits = $this->findByIdAndSupplierId($supplier_json['id_isa'], $supplier_update['Supplier']['id']);

        if ($brand_exits) {
            $brand_tmp = array(
                'Brand' => array(
                    'id' => $brand_exits['Brand']['id'],
                    'supplier_id' => $brand_exits['Brand']['supplier_id'],
                    'name' => isset($supplier_json['identite']['raison_sociale']) ? $supplier_json['identite']['raison_sociale'] : '--',
                    'active' => ($supplier_json['actif']) ? $supplier_json['actif'] : ConstantsBooleans::NO_ACTIVE,
                )
            );
        } else {
            $brand_tmp = array(
                'Brand' => array(
                    'id' => $supplier_update['Supplier']['id'],
                    'supplier_id' => $supplier_update['Supplier']['id'],
                    'name' => isset($supplier_json['identite']['raison_sociale']) ? $supplier_json['identite']['raison_sociale'] : '--',
                    'active' => ($supplier_json['actif']) ? $supplier_json['actif'] : ConstantsBooleans::NO_ACTIVE,
                )
            );
            $this->create();
        }

        $brand_bd = $this->save($brand_tmp);
        if (!$brand_bd) {
            CakeLog::write('updates-france', 'The Brand with ID ISA ' . $supplier_json['id_isa'] . ' could not be created.' . PHP_EOL);
        }

        return $brand_bd;
    }
}
