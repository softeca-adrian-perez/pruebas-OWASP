<?php
class BrandImage extends AppModel {
    public $useTable = 'brands_images';

    public $hasOne = array(
        'Brand',
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

    public function add($image, $brand_id) {
        $fields = array(
            'BrandImage' => array(
                'brand_id',
                'creation_date',
                'file',
                'type',
                'ext',
                'source_name'
            )
        );

        $dateTime = date('Y-m-d H:i:s');

        $image['BrandImage']['brand_id'] = $brand_id;
        $image['BrandImage']['creation_date'] = $dateTime;

        $image_name = $image['BrandImage']['file']['name'];
        $type = $image['BrandImage']['file']['type'];
        $image['BrandImage']['file'] = $image_name;
        $image['BrandImage']['type'] = $type;
        $image['BrandImage']['ext'] = pathinfo($image_name, PATHINFO_EXTENSION);
        $image['BrandImage']['source_name'] = $image_name;

        $this->create();
        if($this->save($image, true, $fields)) {
            return true;
        }
        return false;
    }

    public function edit($image, $brand_id) {
        $fields = array(
            'BrandImage' => array(
                'id',
                'brand_id',
                'creation_date',
                'file',
                'type',
                'ext',
                'source_name'
            )
        );

        $dateTime = date('Y-m-d H:i:s');

        $image['BrandImage']['brand_id'] = $brand_id;
        $image['BrandImage']['creation_date'] = $dateTime;

        $image_name = $image['BrandImage']['file']['name'];
        $type = $image['BrandImage']['file']['type'];
        $image['BrandImage']['file'] = $image_name;
        $image['BrandImage']['type'] = $type;
        $image['BrandImage']['ext'] = pathinfo($image_name, PATHINFO_EXTENSION);
        $image['BrandImage']['source_name'] = $image_name;

        if($this->save($image, true, $fields)) {
            return true;
        }
        return false;
    }
}