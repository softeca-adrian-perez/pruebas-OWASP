<?php
class GarageImage extends AppModel{
    public $useTable = 'garages_images';

    public $belongsTo = array(
        'Garage',
        'LogChange'
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

    public function beforeValidate( $options = array() ) {
        if( isset( $this->data[$this->alias]['file']['source_name'] ) ) {
            $source_name = $this->data[$this->alias]['file']['source_name'];
            $this->data[$this->alias]['source_name'] = $source_name;
        }
    }

    public function uploadGarageImages($image, $imageFile, $garage_id, $file_type = ConstantsFileType::IMAGE)
    {
        $result = false;
        if($imageFile['error'] != ConstantsFlag::ERROR_NOT_FILE){
            $pathFile = ConstantsFilePaths::GARAGES_IMAGES_RELATIVE;
            if (in_array(mime_content_type($image), array('image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp'))) {
                $imageFile['source'] = file_get_contents($image);
                $imageFile['name'] = FileManager::get_renamed_name(preg_replace("/[^a-zA-Z0-9.]/", "", $imageFile['name']));
                $imageFile['type'] = 'image/webp';

                $imageTiny = FileManager::tiny_to_image($imageFile);

                if(filesize($imageTiny) > ConstantsUpdateImage::SIZE_FILES_UPLOAD){
                    return false;
                }

                $img2 = imagecreatefromwebp($imageTiny);
                $imageFile['name'] = $imageTiny;
                $imageFile['ext'] = substr($imageFile['name'], strrpos($imageFile['name'], '.') + 1);

                if (imagewebp($img2, APP . $pathFile . $imageTiny)) {
                    if(Filemanager::upload_file(APP . $pathFile . $imageFile['name'], $pathFile, $imageFile['name'], $file_type)){
                        $garageImage = $this->saveFile($imageFile, $garage_id);
                        $result = $garageImage;
                    }
                    unlink($imageFile['name']);
                }
            }
        }

        if($result){
            $garageClass = ClassRegistry::init("Garage");
            $garageClass->edit_modification_date_garage( $garage_id );
            return $garageImage;
        }

        return $result;
    }

    public function saveFile($file, $garage_id){
        $successfullySaved = true;

        $file_new['GarageImage'] = array(
            'garage_id' => $garage_id,
            'creation_date' => date('Y-m-d H:i:s'),
            'file' => $file['name'],
            'type' => $file['type'],
            'ext' => $file['ext'] ?? null,
            'source_name' => $file['name'],
        );

        $this->create();
        if(!$this->save($file_new)){
            $successfullySaved = false;
        }

        return $successfullySaved;
    }


    public function quitImagePrincipalOfGaregeByGarageId( $garage_id ){
        $this->updateAll(
            array('GarageImage.principal' => ConstantsBooleans::NO),
            array(
                'GarageImage.garage_id' => $garage_id,
                'GarageImage.principal' => ConstantsBooleans::YES,
            )
        );
    }

    /**
     * Borra los ficheros asociados a cada subscription.
     * @param $id
     * @return bool
     */
    public function deleteGarageImage( $id ){
        $garage_image = $this->findById( $id );
        FileManager::delete_file(WWW_ROOT,substr(ConstantsPath::DIR_GARAGE_IMAGES, 3) . DS . $garage_image['GarageImage']['file']);
        if( $this->eliminar( $id ) ){
            if( file_exists( $file ) ){
                unlink( $file );
            }
            return true;
        }
        return false;
    }

    public function convertToPrincipalGarageImage( $id , $principal ){
        $this->Behaviors->disable('Attachment');
        $garage_image = $this->findById( $id );
        $garage_image['GarageImage']['principal'] = $principal;

        if($garage_image_bd = $this->save( $garage_image )){
            return $garage_image_bd;
        }
        else{
            return false;
        }
    }

    public function convertWebGarageImage( $id , $web ){
        $this->Behaviors->disable('Attachment');
        $garage_image = $this->findById($id);
        $garage_image['GarageImage']['web'] = $web;
        if($garage_image = $this->save( $garage_image )){
            return $garage_image;
        }
        else{
            return false;
        }
    }

    public function getListByGarageId( $garage_id ){
        return $this->find('list',
            array(
                'conditions' => array(
                    'garage_id' => $garage_id
                ),
                'fields' => array(
                    'id',
                    'file'
                )
            )
        );
    }

    public function getPrincipalImage( $garage_id ){
        return $this->find('first',
            array(
                'conditions' => array(
                    'principal' => ConstantsBooleans::YES,
                    'garage_id' => $garage_id
                )));
    }

}