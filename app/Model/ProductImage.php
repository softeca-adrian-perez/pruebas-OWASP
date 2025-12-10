<?php
class ProductImage extends AppModel {
    public $useTable = 'products_images';

    public $hasOne = array(
        'Product',
    );

    public $validate = array(
        'new_image' => array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_an_image',
        ),
        'file' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'type' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'ext' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'source_name' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
    );

    public function add($image, $product_id) {
        $fields = array(
            'ProductImage' => array(
                'product_id',
                'creation_date',
                'file',
                'type',
                'ext',
                'source_name'
            )
        );

        $dateTime = date('Y-m-d H:i:s');

        $image['ProductImage']['product_id'] = $product_id;
        $image['ProductImage']['creation_date'] = $dateTime;

        $image_name = $image['ProductImage']['file']['name'];
        $type = $image['ProductImage']['file']['type'];
        $image['ProductImage']['file'] = $image_name;
        $image['ProductImage']['type'] = $type;
        $image['ProductImage']['ext'] = pathinfo($image_name, PATHINFO_EXTENSION);
        $image['ProductImage']['source_name'] = $image_name;

        $this->create();
        if($this->save($image, true, $fields)) {
            return true;
        }
        return false;
    }

    public function edit($image, $product_id) {
        $fields = array(
            'ProductImage' => array(
                'id',
                'product_id',
                'creation_date',
                'file',
                'type',
                'ext',
                'source_name'
            )
        );

        $dateTime = date('Y-m-d H:i:s');

        $image['ProductImage']['product_id'] = $product_id;
        $image['ProductImage']['creation_date'] = $dateTime;

        $image_name = $image['ProductImage']['file']['name'];
        $type = $image['ProductImage']['file']['type'];
        $image['ProductImage']['file'] = $image_name;
        $image['ProductImage']['type'] = $type;
        $image['ProductImage']['ext'] = pathinfo($image_name, PATHINFO_EXTENSION);
        $image['ProductImage']['source_name'] = $image_name;

        if($this->save($image, true, $fields)) {
            return true;
        }
        return false;
    }
}