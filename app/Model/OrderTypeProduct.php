<?php

class OrderTypeProduct extends AppModel
{
    public $useTable = 'order_types_products';

    public function add_order_type_product($data)
    {
		$fields = array(
			'OrderTypeProduct' => array(
				'order_product_id',
				'order_type_id',
				'aag_region_id',
			)
		);
		$this->create();

		$order_type_product_bd = $this->guardar($data, $fields);
		if (!$order_type_product_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

    public function edit_order_type_product($data)
    {
        $fields = array(
            'OrderTypeProduct' => array(
                'order_product_id',
				'order_type_id',
            )
        );

        $order_type_product_bd = $this->guardar($data, $fields);
		if (!$order_type_product_bd) {
			return false;
		}

		$this->commit();
		return true;
    }

    public function getData($aag_region_id) {
		return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'OrderTypes',
                    'table' => 'order_types',
                    'type' => 'LEFT',
                    'conditions' => 'OrderTypeProduct.order_type_id = OrderTypes.id'
                ),
                array(
                    'alias' => 'OrderProducts',
                    'table' => 'order_products',
                    'type' => 'LEFT',
                    'conditions' => 'OrderTypeProduct.order_product_id = OrderProducts.id'
                ),
            ),
            'conditions' => array(
                'OrderTypeProduct.aag_region_id' => $aag_region_id
            ),
            'fields' => array(
                'OrderTypeProduct.*',
                'OrderTypes.name_' . __l(),
                'OrderProducts.name_' . __l(),
            ),
			'order' => array(
				'OrderTypes.name_' . __l() => 'asc'
				)
			)
		);
	}

    public function search_list_by_order_type($order_type_id){
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'OrderTypes',
                    'table' => 'order_types',
                    'type' => 'LEFT',
                    'conditions' => 'OrderTypeProduct.order_type_id = OrderTypes.id'
                ),
                array(
                    'alias' => 'OrderProducts',
                    'table' => 'order_products',
                    'type' => 'LEFT',
                    'conditions' => 'OrderTypeProduct.order_product_id = OrderProducts.id'
                ),
            ),
            'fields' => array(
                'OrderProducts.id',
                'OrderProducts.name_' . __l()
            ),
			'conditions' => array(
				'OrderTypeProduct.aag_region_id' => CakeSession::read('Auth.User.aag_region_id'),
                'OrderTypeProduct.order_type_id' => $order_type_id
			),
            'order' => array(
                'OrderProducts.name_' . __l()
            )
        ));
    }
}