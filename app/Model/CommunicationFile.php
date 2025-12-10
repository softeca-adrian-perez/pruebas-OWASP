<?php

class CommunicationFile extends AppModel{
    public $useTable = 'communications_files';

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
                'uploadDir' => ConstantsPath::DIR_COMMUNICATIONS_FILES,
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

    public function saveFile($file, $communication_id, $file_type) {

        $source_name = $file['name'];
        $file['name'] = FileManager::get_renamed_name($file['name']);

        $file_new['CommunicationFile'] = array(
            'communication_id' => $communication_id,
            'creation_date' => date('Y-m-d H:i:s'),
            'file' => $file,
            'type' => $file['type'],
            'source_name' => $source_name,
        );
        
        // $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        // if( !in_array( $ext, Configure::read('ConstantsFileTypes') )){
        //     return false;
        // }

        $this->create();
        if(!$this->save($file_new)){
            return false;
        }

        $file = $this->findById($this->id);
        if(!FileManager::upload_file(WWW_ROOT . ConstantsPath::DIR_COMMUNICATIONS_FILES . '/' . $file['CommunicationFile']['file'], substr(ConstantsPath::DIR_COMMUNICATIONS_FILES, 3), $file['CommunicationFile']['file'], $file_type)){
            return false;
        }
        
        return true;
    }

    public function deleteCommunicationFile( $communication_id ){
        $communication_file = $this->findById( $communication_id );
        if (FileManager::delete_file(WWW_ROOT , substr(ConstantsPath::DIR_COMMUNICATIONS_FILES, 3) . DS . $communication_file['CommunicationFile']['file'])) {
            return $this->eliminar( $communication_file['CommunicationFile']['id'] );
        }
        return false;
    }

    public function getListByCommunicationId( $communication_id ){
        return $this->find('list',
            array(
                'conditions' => array(
                    'communication_id' => $communication_id
                ),
                'fields' => array(
                    'id',
                    'file'
                )
            )
        );
    }

}