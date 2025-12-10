<?php

class SupplierFile extends AppModel {

    public $useTable = 'suppliers_files';

    public $hasOne = array(
        'Supplier',
    );

    public $validate = array(
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
        'name' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public $actsAs = array(
        'Uploader.Attachment' => array(
            'file' => array(
                'uploadDir' => ConstantsPath::DIR_SUPPLIERS_FILES,
                'tempDir' => __DIR__.'/../tmp',
                'overwrite' => false,
                'metaColumns' => array(
                    'ext' => 'ext',
                    'type' => 'type',
                ),
            ),
        ),
    );

    public function beforeValidate($options = array()) {
        if(isset($this->data[$this->alias]['file']['source_name'])) {
            $source_name = $this->data[$this->alias]['file']['source_name'];
            $this->data[$this->alias]['source_name'] = $source_name;
        }
    }

    public function saveFile($file, $supplier_id, $supplier_info = array(), $file_type) {
        $successfullySaved = true;

        $supplier_category_id = $supplier_info['category_id'];
        $supplier_file_name = $supplier_info['file_name'];
        $supplier_file_is_active = ($supplier_info['is_active'] == ConstantsBooleans::YES) ? true : false;

        $file_new['SupplierFile'] = array(
            'supplier_id' => $supplier_id,
            'supplier_category_id' => $supplier_category_id,
            'creation_date' => date('Y-m-d H:i:s'),
            'file' => $file,
            'type' => $file['type'],
            'source_name' => $file['name'],
            'name' => $supplier_file_name,
            'active' => $supplier_file_is_active,
        );

        // $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        // if( !in_array( $ext, Configure::read('ConstantsFileTypes') )){
        //     return false;
        // }

        $this->create();

        if(!$this->save($file_new)) {
            $successfullySaved = false;
        }

        $file = $this->findById($this->id);
        if(!FileManager::upload_file(WWW_ROOT . ConstantsPath::DIR_SUPPLIERS_FILES. '/' . $file['SupplierFile']['file'], substr(ConstantsPath::DIR_SUPPLIERS_FILES, 3), $file['SupplierFile']['file'], $file_type)){
            return false;
        }
        
        return $successfullySaved;
    }

    public function deleteSupplierFile($supplier_file_id) {
        $supplier_file = $this->findById($supplier_file_id);
        $file = substr(ConstantsPath::DIR_SUPPLIERS_FILES,3) . DS . $supplier_file['SupplierFile']['file'];
        if(FileManager::delete_file(WWW_ROOT . '../' , $file)){
            $bd = $this->delete($supplier_file['SupplierFile']['id']);
            if($bd) {
                return true;
            }
        }
        return false;
    }

    public function deleteSupplierFiles($supplier_id) {
        $supplier_files = $this->findAllBySupplierId($supplier_id);
        foreach($supplier_files as $supplier_file) {
            $file = substr(ConstantsPath::DIR_SUPPLIERS_FILES,3) . DS . $supplier_file['SupplierFile']['file'];
            if(FileManager::delete_file(WWW_ROOT . '../' , $file)){
                $this->delete($supplier_file['SupplierFile']['id']);
            }
        }
    }

    public function getFilesBySupplierIdAndCategoryId( $supplier_id, $category_id ){
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'supplier_id' => $supplier_id,
                    'supplier_category_id' => $category_id,
                ),
            )
        );
    }

    public function getFilesBySupplierIdAndCategoryIdAndActive( $supplier_id, $category_id ){
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'supplier_id' => $supplier_id,
                    'supplier_category_id' => $category_id,
                    'active' => ConstantsBooleans::ACTIVE,
                ),
            )
        );
    }

}