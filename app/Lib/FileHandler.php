<?php

/**
 * Class File with static utility methods to handle files.
 */
abstract class FileHandler {

    /**
     * Generates a file with the given base 64 data, with the predefined size, quality and format.
     * @param type $image_base64_data
     * @param type $width
     * @param type $height
     * @return GdImage
     */
    static function createImageFileFromBase64($image_base64_data, $width = false, $height = false) {
        if(strpos($image_base64_data, ';base64,') !== false) {
            $image_base64_data = substr($image_base64_data, 22);
        }
        $data = base64_decode($image_base64_data);
        $im = imagecreatefromstring($data);
        if ($width || $height) {
            if (!function_exists('getimagesizefromstring')) {
                $uri = 'data://application/octet-stream;base64,' . $image_base64_data;
                $x = getimagesize($uri);
            }else {
                $x = getimagesizefromstring($data);
            }
//            $x = getimagesizefromstring($data);
            $resized_im = FileHandler::resizeImage($im, $width, $height, $x);
        } else $resized_im = $im;
        return $resized_im;
    }

    /**
     * Resize an image to the given width and height, keeping the original aspect ratio
     * @param resource $im image to resize.
     * @param int $width the new width.
     * @param int $height the new height.
     * @param array $original_size array with the original size of the image.
     * @return resource
     */
    static function resizeImage($im, $width, $height, $original_size){
        $original_width = $original_size['0'];
        $original_height = $original_size['1'];

        if($original_width > $original_height) {
            $height    =   $original_height*($height/$original_width);
        }

        if($original_width < $original_height) {
            $width    =   $original_width*($width/$original_height);
        }

        $resized_im = imagecreatetruecolor($width, $height);
        imagecopyresized($resized_im, $im, 0, 0, 0, 0, $width, $height, $original_width, $original_height);
        return $resized_im;
    }

    /**
     * Convert an image to a string data.
     * @param resource $imagedata
     * @return string
     */
    static function getStringFromImgResource($imagedata){
        ob_start();
        imagejpeg($imagedata);
        $stringdata = ob_get_contents(); // read from buffer
        ob_end_clean(); // delete buffer
        return $stringdata;
    }

    /**
     * Generates a random filename checkings that a file with the obtained
     * filename and extension at the given path doesn't already exists.
     */
    static function generateRandomFileName($file_extension, $file_path) {
        do {
            $filename = CodeGenerator::generateRandomString();
        } while (file_exists($file_path . $filename . $file_extension));
        return $filename . $file_extension;
    }

}
