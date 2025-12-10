<?php
class Product extends AppModel
{
    public $useTable = 'products';

    public $hasOne = array(
        'Brand',
        'ProductImage'
    );

    public $validate = array(
        'name' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_name',
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'brand_id' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_brand',
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['name'])) {
            $conditions[] = $this->_conditionName($fields['name']);
        }
        if (!empty($fields['brand'])) {
            $conditions[] = $this->_conditionBrand($fields['brand']);
        }
        if (isset($fields['active'])) {
            $conditions[] = $this->_conditionActive($fields['active']);
        }
        return $conditions;
    }

    public function _conditionName($name)
    {
        return array('Product.name LIKE' => '%' . $name . '%');
    }

    public function _conditionBrand($brand_id)
    {
        return array('Product.brand_id' => $brand_id);
    }

    private function _conditionActive($active)
    {
        //If you get a 2, recharge as is.
        if ($active == '1' or $active == '0') {
            return array('Product.active' => $active);
        }
    }

    public function _query($aag_region_id)
    {
        $query = array(
            'joins' => array(
                array(
                    'alias' => 'ProductImage',
                    'table' => 'products_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Product.id = ProductImage.product_id',
                    ),
                ),
                array(
                    'alias' => 'BrandImage',
                    'table' => 'brands_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Product.brand_id = BrandImage.brand_id',
                    ),
                ),
                array(
                    'alias' => 'Brand',
                    'table' => 'brands',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Product.brand_id = Brand.id',
                    ),
                ),
                array(
                    'alias' => 'Supplier',
                    'table' => 'suppliers',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Brand.supplier_id = Supplier.id',
                        'Supplier.aag_region_id' => $aag_region_id
                    ),
                )
            ),
            'fields' => array(
                'Product.*',
                'ProductImage.*',
                'BrandImage.*',
                'Brand.*',
            ),
            'order' => 'Product.name asc'
        );

        return $query;
    }

    public function add($product)
    {
        $fields = array(
            'Product' => array(
                'brand_id',
                'name',
                'active',
            )
        );

        $this->create();
        if ($tmp = $this->save($product, true, $fields)) {
            return $tmp;
        }
        return false;
    }

    public function edit($product)
    {
        $fields = array(
            'Product' => array(
                'brand_id',
                'name',
                'active',
            )
        );

        if ($tmp = $this->save($product, true, $fields)) {
            return $tmp;
        }
        return false;
    }

    public function getAllAboutProductId($product_id)
    {
        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'ProductImage',
                    'table' => 'products_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Product.id = ProductImage.product_id',
                    ),
                ),
            ),
            'fields' => array(
                'Product.*',
                'ProductImage.*',
            ),
            'conditions' => array(
                'Product.id' => $product_id,
            ),
        ));
    }

    public function getAllProductsByBrandId($brand_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'ProductImage',
                    'table' => 'products_images',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Product.id = ProductImage.product_id',
                    ),
                ),
                array(
                    'alias' => 'Brand',
                    'table' => 'brands',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Brand.id = Product.brand_id',
                    ),
                ),
            ),
            'fields' => array(
                'Product.*',
                'ProductImage.*',
                'Brand.*'
            ),
            'conditions' => array(
                'Product.brand_id' => $brand_id,
                'Product.active' => ConstantsBooleans::ACTIVE,
            ),
            'order' => array(
                'Product.name'
            ),
        ));
    }

    public function createProductFR($products_json, $brand_create)
    { // New supplier comes from FRANCE JSON
        $clear_characters = array("+", "(", ")");
        $products = explode(" - ", $products_json);
        foreach ($products as $product) {
            $product_tmp = array(
                'Product' => array(
                    'brand_id' => $brand_create['Brand']['id'],
                    'name' => str_replace($clear_characters, '-', $product),
                    'active' => $brand_create['Brand']['active'],
                )
            );

            $this->create();
            $product_bd = $this->save($product_tmp);
            if (!$product_bd) {
                CakeLog::write('updates-france', 'The Product ' . $product . ' could not be created.' . PHP_EOL);
            }
        }

        return true;
    }

    public function updateProductFR($products_json, $brand_update)
    { // New supplier comes from FRANCE JSON
        $clear_characters = array("+", "(", ")");
        $products = explode(" - ", $products_json);
        foreach ($products as $product) {
            $brand_exit = $this->findByName(str_replace($clear_characters, '-', $product));

            if (!$brand_exit) {
                $product_tmp = array(
                    'Product' => array(
                        'brand_id' => $brand_update['Brand']['id'],
                        'name' => str_replace($clear_characters, '-', $product),
                        'active' => $brand_update['Brand']['active'],
                    )
                );

                $this->create();
                $product_bd = $this->save($product_tmp);
                if (!$product_bd) {
                    CakeLog::write('updates-france', 'The Product ' . $product . ' could not be created.' . PHP_EOL);
                }
            }
        }

        return true;
    }
}
