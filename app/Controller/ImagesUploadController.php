<?php
class ImagesUploadController extends AppController
{
    public function upload($typeTinymce)
    {
        $user = $this->Acceso->user();
        $result = array(
            'uploaded' => 0
        );
        if ($user) {
            if (!isset($typeTinymce) || !in_array($typeTinymce,array_keys(ConstantsTypesTinyMce::TYPES_TINY_MCE))) {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
            $uploadedFile = isset($this->request->params['form']['file']) ? $this->request->params['form']['file'] : null;

            if ($uploadedFile['error'] != 0) {
                $result['error']['message'] = __t('Constants.Error_uploading_file');
                return $this->returnJsonResult($result);
            }

            if (!empty($uploadedFile)) {
                $imageSize = $uploadedFile['size'];

                if ($imageSize < ConstantsUpdateImage::SIZE_FILES_UPLOAD) {
                    $extension = FileManager::getExtension($uploadedFile);
                    if (isset($extension) && in_array($extension, ConstantsUpdateImage::EXTENSIONS_FILES)) {
                        $guid = FileManager::generate_unique_id();
                        $filename = ($user['aag_region_id'] ?? '-') .'-'. time() . '-' . $guid. '.'. $extension;
                        $completeAbsolutePath = ConstantsFilePaths::TINYMCE_IMAGES_ABSOLUTE . ConstantsTypesTinyMce::TYPES_TINY_MCE[$typeTinymce] . DS .$filename;
                        move_uploaded_file($uploadedFile['tmp_name'], $completeAbsolutePath);

                        if (FileManager::upload_file(
                            $completeAbsolutePath,
                            ConstantsFilePaths::TINYMCE_IMAGES_RELATIVE . ConstantsTypesTinyMce::TYPES_TINY_MCE[$typeTinymce] . DS,
                            $filename,
                            ConstantsFileType::IMAGE
                        )) {
                            $result['file'] = $filename;
                            $result['uploaded'] = 1;
                            if (\Configure::read('AZURE_FILES')) {
                                $imageUrl = FileManager::get_url(ConstantsFilePaths::TINYMCE_IMAGES_RELATIVE . ConstantsTypesTinyMce::TYPES_TINY_MCE[$typeTinymce] . DS .
                                $filename,false);
                            } else {
                                $imageUrl = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . DS . ConstantsFilePaths::TINYMCE_IMAGES_RELATIVE . ConstantsTypesTinyMce::TYPES_TINY_MCE[$typeTinymce] . DS .
                                $filename;
                            }
                        } else {
                            $result['uploaded'] = 0;
                        }
                        $result['url'] = $imageUrl;
                        $result['location'] = $imageUrl;
                        return $this->returnJsonResult($result);
                    }
                } elseif (!$isImage) {
                    $result['error']['message'] = __t('Constants.File_must_be') . ' ' . implode(', ', ConstantsUpdateImage::EXTENSIONS_FILES);
                    return $this->returnJsonResult($result);
                } else {
                    $result['error']['message'] = __t('Constants.Max_file_size_is') . ' ' . ConstantsUpdateImage::SIZE_FILES_UPLOAD_TEXT;
                    return $this->returnJsonResult($result);
                }
            } else {
                $result['error']['message'] = __t('Constants.No_extension');
                return $this->returnJsonResult($result);
            }
        } else {
            $result['error']['message'] = __t('Constants.No_extension');
            return $this->returnJsonResult($result);
        }
    }
}
