<?php

class OrderProduct extends AppModel
{
	public $useTable = 'order_products';

	public $hasMany = array(
		'GarageProduct',
	);

	public $hasOne = array(
		'AagRegion',
	);

	public $validate = array(
		'name_en' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'name_fr' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'name_de' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'name_nl' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
	);

	public function search_list($aag_region_id)
	{
		return $this->find('list', array(
			'conditions' => array(
				'aag_region_id' => $aag_region_id
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

	public function add_order_products($data)
	{
		$fields = array(
			'OrderProduct' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
				'aag_region_id'
			)
		);
		$this->create();

		$order_products_bd = $this->guardar($data, $fields);
		if (!$order_products_bd) {
			return false;
		} else {
			$this->commit();
			return true;
		}
	}

	public function edit_order_products($data)
	{
		$fields = array(
			'OrderProduct' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
			)
		);

		$order_products_bd = $this->guardar($data, $fields);
		if (!$order_products_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getData($aag_region_id)
	{
		return $this->find(
			'all',
			array(
				'conditions' => array(
					'aag_region_id' => $aag_region_id
				),
				'order' => array(
					'name_' . __l() => 'asc'
				)
			)
		);
	}

	public function getProductsFromOrderId($order_id, $aagRegionId)
	{
		return
			$this->find(
				'all',
				array(
					'joins' => array(
						array(
							'alias' => 'Product',
							'table' => 'garages_products',
							'type' => 'LEFT',
							'conditions' => 'Product.product_id = OrderProduct.id'
						),
					),
					'conditions' => array(
						'OrderProduct.aag_region_id' => $aagRegionId,
						'Product.order_id' => $order_id,
					),
					'fields' => array(
						'OrderProduct.*',
						'Product.*'
					),
				)
			);
	}

	public function edit_order_product_ajax($data)
	{
		$fields = array(
			'OrderProduct' => array(
				'name_' . __l()
			)
		);

		$order_products_bd = $this->guardar($data, $fields);
		if (!$order_products_bd) {
			return false;
		}

		$this->commit();
		return true;
	}
}
