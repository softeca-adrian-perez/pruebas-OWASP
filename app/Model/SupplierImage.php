<?php
class SupplierImage extends AppModel {

    public $useTable = 'suppliers_images';

    public $belongsTo = array(
        'Supplier',
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

    public function add($image, $supplier_id) {
        $fields = array(
            'SupplierImage' => array(
                'supplier_id',
                'creation_date',
                'file',
                'type',
                'ext',
                'source_name'
            )
        );

        $dateTime = date('Y-m-d H:i:s');

        $image['SupplierImage']['supplier_id'] = $supplier_id;
        $image['SupplierImage']['creation_date'] = $dateTime;

        $image_name = $image['SupplierImage']['file']['name'];
        $type = $image['SupplierImage']['file']['type'];
        $image['SupplierImage']['file'] = $image_name;
        $image['SupplierImage']['type'] = $type;
        $image['SupplierImage']['ext'] = pathinfo($image_name, PATHINFO_EXTENSION);
        $image['SupplierImage']['source_name'] = $image_name;

        $this->create();
        if($this->save($image, true, $fields)) {
            return true;
        }
        return false;
    }

    public function edit($image, $supplier_id) {
        $fields = array(
            'SupplierImage' => array(
                'id',
                'supplier_id',
                'creation_date',
                'file',
                'type',
                'ext',
                'source_name'
            )
        );

        $dateTime = date('Y-m-d H:i:s');
        
        $image['SupplierImage']['supplier_id'] = $supplier_id;
        $image['SupplierImage']['creation_date'] = $dateTime;

        $image_name = $image['SupplierImage']['file']['name'];
        $type = $image['SupplierImage']['file']['type'];
        $image['SupplierImage']['file'] = $image_name;
        $image['SupplierImage']['type'] = $type;
        $image['SupplierImage']['ext'] = pathinfo($image_name, PATHINFO_EXTENSION);
        $image['SupplierImage']['source_name'] = $image_name;

        if($this->save($image, true, $fields)) {
            return true;
        }
        return false;
    }
}