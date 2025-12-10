<?php

class GarageFile extends AppModel{
    public $useTable = 'garages_files';

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
	);

    public $actsAs = array(
        'Uploader.Attachment' => array(
            'file' => array(
                'uploadDir' => ConstantsPath::DIR_GARAGE_FILES,
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
        if( isset( $this->data[$this->alias]['file']['source_name'] ) ) {
            $source_name = $this->data[$this->alias]['file']['source_name'];
            $this->data[$this->alias]['source_name'] = $source_name;
        }
    }

    public function saveFile($file, $garage_id){
        $successfullySaved = true;

        $file_new['GarageFile'] = array(
            'garage_id' => $garage_id,
            'creation_date' => date('Y-m-d H:i:s'),
            'file' => $file,
            'type' => $file['type'],
            'source_name' => $file['name'],
        );

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if( !in_array( $ext, Configure::read('ConstantsFileTypes') )){
            return false;
        }
        
        $this->create();
        if(!$this->save($file_new)){
            $successfullySaved = false;
        }

        return $successfullySaved;
    }

    public function deleteGarageFile( $garage_id ){
        $garage_file = $this->findById( $garage_id );
        return $this->eliminar( $garage_file['GarageFile']['id'] );
    }

}