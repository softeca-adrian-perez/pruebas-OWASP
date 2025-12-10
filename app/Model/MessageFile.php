<?php

class MessageFile extends AppModel{

    public $useTable = 'messages_files';

    public $belongsTo = array(
        'Message',
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
	);

    public $actsAs = array(
        'Uploader.Attachment' => array(
            'file' => array(
                'uploadDir' => ConstantsPath::DIR_MESSAGES_FILES,
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

    public function saveFile($file, $message_id, $file_type){
        $successfullySaved = true;

        $file_new = array(
            'message_id' => $message_id,
            'creation_date' => date('Y-m-d H:i:s'),
            'file' => $file,
            'type' => $file['type'],
            'source_name' => $file['name'],
        );

        // $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        // if( !in_array( $ext, Configure::read('ConstantsFileTypes') )){
        //     return false;
        // }

        $this->create();
        if(!$this->save($file_new)){
            $successfullySaved = false;
        }

        $file = $this->findById($this->id);
        if(!FileManager::upload_file(WWW_ROOT . ConstantsPath::DIR_MESSAGES_FILES . '/' . $file['MessageFile']['file'], substr(ConstantsPath::DIR_MESSAGES_FILES, 3), $file['MessageFile']['file'], $file_type)){
            return false;
        }

        return $successfullySaved;
    }

    public function deleteMessageFile( $message_id ){
        $message_file = $this->findById( $message_id );
        return $this->eliminar( $message_file['MessageFile']['id'] );
    }

    public function getListByMessageId( $message_id ){
        return $this->find('list',
            array(
                'conditions' => array(
                    'message_id' => $message_id
                ),
                'fields' => array(
                    'id',
                    'file'
                )
            )
        );
    }

}
