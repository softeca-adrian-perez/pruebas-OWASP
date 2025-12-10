<?php

class UserImage extends AppModel{
    public $useTable = 'users_images';

    public $belongsTo = array(
        'User' ,
    );

    public $validate = array(
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
                'uploadDir' => ConstantsPath::DIR_USER_IMAGES_ORIGINAL,
                'tempDir' => __DIR__.'/../tmp',
                'overwrite' => true,
                'metaColumns' => array(
                    'ext' => 'ext',
                    'type' => 'type',
                ),
            ),
        ),
    );

    public function chkImageExtension($data) {
        if($data['file']['name'] != ''){
            $fileData   = pathinfo($data['file']['name']);
            $ext        = $fileData['extension'];
            $allowExtension = array('gif', 'jpeg', 'png', 'jpg');

            if(in_array($ext, $allowExtension)) {
                $return = true;
            } else {
                $return = false;
            }
        } else {
            $return = false;
        }
        return $return;
    }

    /**
     *
     * @param array $options
     */
    public function beforeValidate($options = array()) {
        if( isset( $this->data[$this->alias]['file']['source_name'] ) ) {
            $source_name = $this->data[$this->alias]['file']['source_name'];
            $this->data[$this->alias]['source_name'] = $source_name;
        }
    }

    public function deleteUserImage($id){
        $delete = false;
        $user_image = $this->findById($id);

        $file = ConstantsFilePaths::PROFILE_IMAGES_ABSOLUTE . $user_image['UserImage']['file'];
        $file_original = ConstantsPath::DIR_USER_IMAGES_ORIGINAL . DS . $user_image['UserImage']['file'];

        if ($this->eliminar($id)) {
            if (!empty($user_image['UserImage']['file'])) {

                FileManager::delete_file(WWW_ROOT, ConstantsPath::DIR_USER_IMAGES_CROP.'/'.$user_image['UserImage']['file']);

                if (file_exists($file_original)) {
                    unlink($file_original);
                    $delete = true;
                }
            }
        }
        return $delete;
    }

    public function new_image($user, $user_id){
        $fields = array(
            'UserImage' => array(
                'user_id',
                'creation_date',
                'file',
                'type',
                'ext',
                'source_name',
            )
        );

        $file['UserImage']['user_id'] = $user_id;
        $file['UserImage']['creation_date'] = date('Y-m-d H:i:s');

        $file['UserImage']['file'] = $user['User']['image-input'];
        $file['UserImage']['file']['name'] = $user['User']['image'];

        $file['UserImage']['type'] = $user['User']['image-input']['type'];
        $file['UserImage']['ext'] = substr($user['User']['image'], -4);
        $file['UserImage']['source_name'] = $user['User']['image-input']['name'];

        $this->create();
        return $this->guardar($file, $fields);

    }

}