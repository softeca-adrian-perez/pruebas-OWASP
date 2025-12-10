<?php
class GarageNetworkImage extends AppModel
{
    public $useTable = "garages_networks_images";

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

    public $sizes = [
        'x-sm' => ['width' => 300, 'height' => 300],
        'sm' => ['width' => 500, 'height' => 500],
        'md' => ['width' => 700, 'height' => 700],
        'lg' => ['width' => 1024, 'height' => 1024],
        'xl' => ['width' => 1670, 'height' => 1670],
    ];

    /**
     * Upload GarageNetworkImage.
     */
	public function uploadGarageImages($image, $imageFile, $garageNetworkId, $file_type = ConstantsFileType::IMAGE)
    {
		$result = false;
		if ($imageFile['error'] != ConstantsFlag::ERROR_NOT_FILE) {
			$pathFile = ConstantsFilePaths::GARAGES_NETWORKS_IMAGES_RELATIVE;
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

				if (imagewebp($img2, APP. $pathFile . $imageTiny)) {
					if(FileManager::upload_file(APP . $pathFile . $imageFile['name'], $pathFile, $imageFile['name'], $file_type)){
						$garageNetworkImage = $this->saveFile($imageFile, $garageNetworkId);
						$result = $garageNetworkImage;

                        $this->saveResizedCopies($pathFile, $imageFile['name']);
					}
					unlink($imageFile['name']);
				}
			}
		}

		if ($result) {
			$garageNetworkClass = ClassRegistry::init("GarageNetwork");
			$garageNetworkClass->updateGarageNetworkModificationDate($garageNetworkId);
			return $garageNetworkImage;
		}

		return $result;
    }

    private function saveResizedCopies($pathFile, $fileName)
    {
        $tinyPNGImage = FileManager::sendImageToTiny($fileName);

        if (!$tinyPNGImage) {
            return false;
        }

        foreach ($this->sizes as $name => $size) {
            $newFileName = $name . '-' . $fileName;
            $newFilePath = APP . $pathFile . $newFileName;

            if (
                !FileManager::resizeUsingTiny($tinyPNGImage, $newFilePath, $size['width'], $size['height'])
                || !FileManager::upload_file($newFilePath, $pathFile, $newFileName, ConstantsFileType::IMAGE)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Save GarageNetworkImage.
     */
    public function saveFile($file, $garageNetworkId)
    {
        $garageNetworkImage['GarageNetworkImage'] = array(
            'garage_network_id' => $garageNetworkId,
            'creation_date' => date('Y-m-d H:i:s'),
            'file' => $file['name'],
            'type' => $file['type'],
			'ext' => $file['ext'] ?? null,
            'source_name' => $file['name'],
            'principal' => ConstantsBooleans::NO
        );

        $this->create();
        return $this->save($garageNetworkImage);
    }

	/**
	 * Delete GarageNetworkImage and the attached file.
	 */
    public function deleteGarageNetworkImage($garageNetworkImageId)
	{
        $garageNetworkImage = $this->findById($garageNetworkImageId);
        FileManager::delete_file(WWW_ROOT, substr(ConstantsPath::DIR_GARAGE_NETWORK_IMAGES, 3) . DS . $garageNetworkImage['GarageNetworkImage']['file']);

        foreach (array_keys($this->sizes) as $size) {
            FileManager::delete_file(WWW_ROOT, substr(ConstantsPath::DIR_GARAGE_NETWORK_IMAGES, 3) . DS . $size . '-' . $garageNetworkImage['GarageNetworkImage']['file']);
        }

        return $this->eliminar($garageNetworkImageId);
    }

	/**
	 * Set principal value in GarageNetworkImage.
	 */
    public function convertToPrincipalGarageNetworkImage($garageNetworkImageId, $principal)
	{
        $this->Behaviors->disable('Attachment');
        $garageNetworkImage = $this->findById($garageNetworkImageId);
        $garageNetworkImage['GarageNetworkImage']['principal'] = $principal;

        $garageImageBd = $this->save($garageNetworkImage);
        if (!$garageImageBd) {
            return false;
        }
        return $garageImageBd;
    }
}
