<?php

class GarageProduct extends AppModel
{
    public $useTable = 'garages_products';

    public $belongsTo = array(
        'Orders',
        'GarageProduct',
    );

    public function getAllProducts($garage_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Order',
                        'table' => 'orders',
                        'type' => 'LEFT',
                        'conditions' => 'Order.id = GarageProduct.order_id'
                    ),
                    array(
                        'alias' => 'OrderProduct',
                        'table' => 'order_products',
                        'type' => 'LEFT',
                        'conditions' => 'OrderProduct.id = GarageProduct.product_id'
                    ),
                ),
                'conditions' => array(
                    'Order.garage_id' => $garage_id,

                ),

                'fields' => array(
                    'Order.*',
                    'GarageProduct.*',
                    'OrderProduct.*',
                ),
            )
        );
    }

    public function add_products_orders($products, $order_id)
    {
        $fields = array(
            'GarageProduct' => array(
                'order_id',
                'product_id',
                'quantity',
            )
        );

        foreach ($products as $product) {
            $product_id_and_quantity = explode(' ', $product);
            $product_temp['GarageProduct']['order_id'] = $order_id;
            $product_temp['GarageProduct']['product_id'] = $product_id_and_quantity[0];
            $product_temp['GarageProduct']['quantity'] = $product_id_and_quantity[1];
            $this->create();
            if (!$this->guardar($product_temp, $fields)) {
                return false;
            }
        }

        $this->commit();
        return true;
    }
}
