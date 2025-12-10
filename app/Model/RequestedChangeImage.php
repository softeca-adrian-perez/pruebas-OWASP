<?php

class RequestedChangeImage extends AppModel{
    public $useTable = 'requested_changes_images';

    var $belongsTo = array(
        'RequestedChange',
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
        'nameext_de' => array(
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

    public function beforeValidate( $options = array() ) {
        if( isset( $this->data[$this->alias]['file']['source_name'] ) ) {
            $source_name = $this->data[$this->alias]['file']['source_name'];
            $this->data[$this->alias]['source_name'] = $source_name;
        }
    }

    public function saveFile($file, $request_change_id){
        $successfullySaved = true;

        $file_new['RequestedChangeImage'] = array(
            'requested_change_id' => $request_change_id,
            'creation_date' => date('Y-m-d H:i:s'),
            'file' => $file['name'],
            'type' => $file['type'],
            'source_name' => $file['name'],
        );

        $this->create();
        if(!$this->save($file_new)){
            $successfullySaved = false;
        }

        return $successfullySaved;
    }

    public function uploadDistributorImages($image, $imageFile, $request_change_id, $file_type) {
        if($imageFile['error'] != ConstantsFlag::ERROR_NOT_FILE){
            $pathfile = ConstantsFilePaths::DISTRIBUTORS_REQUEST_IMAGES_ABSOLUTE;
            if (in_array(mime_content_type($image), array('image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp' ))) {
                $imageFile['source'] = file_get_contents($image);
                $imageFile['name'] = FileManager::get_renamed_name(preg_replace("/[^a-zA-Z0-9.]/", "", $imageFile['name']));
                $imageFile['type'] = 'image/webp';

                $imageTiny = FileManager::tiny_to_image($imageFile);

                $img2 = imagecreatefromwebp($imageTiny);
                $imageFile['name'] = $imageTiny;
                $imageFile['ext'] = substr($imageFile['name'], strrpos($imageFile['name'], '.') + 1);

                if(imagewebp($img2, APP . $pathfile . $imageFile['name'])){
                    if(FileManager::upload_file(APP . $pathfile . $imageFile['name'], $pathfile, $imageFile['name'], $file_type)){
                        unlink($imageFile['name']);
                        if(!$this->saveFile($imageFile, $request_change_id)){
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function uploadGarageImages($image, $imageFile, $request_change_id, $file_type) {
        if($imageFile['error'] != ConstantsFlag::ERROR_NOT_FILE){
            $pathfile = ConstantsFilePaths::GARAGES_REQUEST_IMAGES_ABSOLUTE;
            if (in_array(mime_content_type($image), array('image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp' ))) {
                $imageFile['source'] = file_get_contents($image);
                $imageFile['name'] = FileManager::get_renamed_name(preg_replace("/[^a-zA-Z0-9.]/", "", $imageFile['name']));
                $imageFile['type'] = 'image/webp';

                $imageTiny = FileManager::tiny_to_image($imageFile);

                $img2 = imagecreatefromwebp($imageTiny);
                $imageFile['name'] = $imageTiny;
                $imageFile['ext'] = substr($imageFile['name'], strrpos($imageFile['name'], '.') + 1);

                if(imagewebp($img2, APP . $pathfile . $imageFile['name'])){
                    if(FileManager::upload_file(APP . $pathfile . $imageFile['name'], $pathfile, $imageFile['name'], $file_type)){
                        unlink($imageFile['name']);
                        if(!$this->saveFile($imageFile, $request_change_id)){
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
            }
        }
        return true;
    }

}