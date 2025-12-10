<?php

class DistributorImage extends AppModel{
    public $useTable = 'distributors_images';

    public $belongsTo = array(
        'Distributor',
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

    public function uploadDistributorImages($image, $imageFile, $distributor_id, $file_type) {
        $result = false;
        if($imageFile['error'] != ConstantsFlag::ERROR_NOT_FILE){
            $pathFile = ConstantsFilePaths::DISTRIBUTORS_IMAGES_RELATIVE;
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
                    if(FileManager::upload_file(APP . $pathFile . $imageFile['name'], $pathFile, $imageFile['name'], $file_type)){
                        $distributorImage = $this->saveFile($imageFile, $distributor_id, $file_type);
                        $result = $distributorImage;
                    }
                    unlink($imageFile['name']);
                }
            }
        }

        if($result){
            $distributorClass = ClassRegistry::init("Distributor");
            $distributorClass->edit_modification_date( $distributor_id );
            return $distributorImage;
        }

        return $result;
    }

    public function saveFile($file, $distributor_id, $file_type){
        $successfullySaved = true;

        $file_new['DistributorImage'] = array(
            'distributor_id' => $distributor_id,
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

    public function quitImagePrincipalOfDistributorByDistributorId( $distributor_id ){
        $this->updateAll(
            array('DistributorImage.principal' => ConstantsBooleans::NO),
            array(
                'DistributorImage.distributor_id' => $distributor_id,
                'DistributorImage.principal' => ConstantsBooleans::YES,
            )
        );
    }

    public function deleteDistributorImage( $id ){
        $distributor_image = $this->findById( $id );
        $file = ConstantsPath::DIR_DISTRIBUTOR_IMAGES . DS . $distributor_image['DistributorImage']['file'];
        FileManager::delete_file(WWW_ROOT,substr(ConstantsPath::DIR_DISTRIBUTOR_IMAGES , 3) . DS . $distributor_image['DistributorImage']['file']);
        if( $this->eliminar( $id ) ){
            if( file_exists( $file ) ){
                unlink( $file );
            }
            return true;
        }
        return false;
    }

    public function convertToPrincipalDistributorImage( $id , $principal ){
        $this->Behaviors->disable('Attachment');
        $distributor_image = $this->findById( $id );
        $distributor_image['DistributorImage']['principal'] = $principal;

        if( $distributor_image_bd = $this->save( $distributor_image ) ){
            return $distributor_image_bd;
        }
        else{
            return false;
        }
    }

    public function getListByDistributorId( $distributor_id ){
        return $this->find('list',
            array(
                'conditions' => array(
                    'distributor_id' => $distributor_id
                ),
                'fields' => array(
                    'id',
                    'file'
                )
            )
        );
    }

    public function getPrincipalImage( $distributor_id ){
        return $this->find('first',
            array(
                'conditions' => array(
                    'principal' => ConstantsBooleans::YES,
                    'distributor_id' => $distributor_id
                )));
    }

}