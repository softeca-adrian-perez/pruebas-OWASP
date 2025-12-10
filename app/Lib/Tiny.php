<?php
class Tiny
{
    /**
     * Load image to Tiny.
     */
    public static function loadImage($input)
    {
        try {
            $originalFile = pathinfo($input['name']);
            $fileName = substr($originalFile['basename'], 0, strrpos($originalFile['basename'], '.')) . '.webp';

            $request = curl_init();
            curl_setopt_array($request, array(
                CURLOPT_URL => ConstantsTiny::URL_API_TINY,
                CURLOPT_USERPWD => "api:" . Texto::encryptDecryptText(TINY_API_KEY, false),
                CURLOPT_POSTFIELDS => $input['source'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_SSL_VERIFYPEER => false // PENDING
            ));
            $response = curl_exec($request);

            if (curl_getinfo($request, CURLINFO_HTTP_CODE) === 201) {
                $headers = substr($response, 0, curl_getinfo($request, CURLINFO_HEADER_SIZE));
                foreach (explode("\r\n", $headers) as $header) {
                    if (strtolower(substr($header, 0, 10)) === "location: ") {
                        $request = curl_init();
                        curl_setopt_array($request, array(
                            CURLOPT_URL => substr($header, 10),
                            CURLOPT_USERPWD => "api:" . Texto::encryptDecryptText(TINY_API_KEY, false),
                            CURLOPT_HTTPHEADER => array(
                                'Content-Type: application/json',
                                'User-Agent' => 'CakePHP'
                            ),
                            CURLOPT_POSTFIELDS => json_encode(
                                array(
                                    'convert' => array('type' => 'image/webp')
                                )
                            ),
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_SSL_VERIFYPEER => false // PENDING
                        ));
                        file_put_contents($fileName, curl_exec($request));
                        return $fileName;
                    }
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("Tiny - Load image - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    public static function optimiseImageSize($image, $column, $absoluteRoute, $relativeRoute, $model, $isPrivateContainer)
    {
        $imageName = $image[$model][$column];
        if (Configure::read('AZURE_FILES')) {
            $imagePath = FileManager::get_url($relativeRoute . $imageName, $isPrivateContainer);
            $headers = get_headers($imagePath, 1);
            $sizeFirstImage = $headers['Content-Length'];
            $type = $headers['Content-Type'];
        } else {
            $imagePath = $absoluteRoute . $imageName;
            $sizeFirstImage = filesize($imagePath);
        }

        if (
            file_exists($imagePath) ||
            (
                ($type == 'application/octet-stream' && pathinfo($imageName)['extension'] == 'webp' || $type == 'image/webp')
                && $headers && (strpos($headers[0], '200') !== false))
        ) {
            $imageFile['source'] = file_get_contents($imagePath);   // Tiny requires following this format
            $imageFile['name'] = $absoluteRoute . $imageName;
            $imageFile['type'] = 'image/webp';

            do {
                $urlOutputFile = FileManager::tiny_to_image($imageFile);
                $percentageOfImprove = 100 - ((filesize($urlOutputFile) * 100) / $sizeFirstImage);
                $imageFile['source'] = file_get_contents($urlOutputFile);
                $imageFile['name'] = $urlOutputFile;
                $imageFile['type'] = 'image/webp';
                $sizeFirstImage = filesize($urlOutputFile);
            } while ($percentageOfImprove >= TINY_IMPROVE_PERCENTAGE);

            $fileOutput = pathinfo($urlOutputFile);

            if (Configure::read('AZURE_FILES')) {
                file_put_contents($absoluteRoute . $fileOutput['basename'], $imageFile['source']);
                unlink($urlOutputFile);
                $optimized = FileManager::upload_file($absoluteRoute . $fileOutput['basename'], $relativeRoute, $fileOutput['basename'], ConstantsFileType::IMAGE, $isPrivateContainer);
            } else {
                $optimized = rename($fileOutput['basename'], $imagePath);   // Move image to a specific directory
            }

            if ($optimized) {
                $image[$model]['optimized'] = ConstantsBooleans::YES;
                $image[$model]['modification_date'] = date('Y-m-d H:i:s');
                $modelClass = ClassRegistry::init($model);
                $fields = array(
                    $model => array(
                        'optimized',
                        'modification_date'
                    )
                );
                $modelClass->guardar($image, $fields);
            }
        }
    }

    public function send($localPath)
    {
        $request = curl_init();
        curl_setopt_array($request, array(
            CURLOPT_URL => ConstantsTiny::URL_API_TINY,
            CURLOPT_USERPWD => "api:" . Texto::encryptDecryptText(TINY_API_KEY, false),
            CURLOPT_POSTFIELDS => file_get_contents($localPath),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ));
        $response = curl_exec($request);
        $headerSize = curl_getinfo($request, CURLINFO_HEADER_SIZE);
        $image = json_decode(substr($response, $headerSize), true);

        $input = $image['input'];
        $output = $image['output'];

        return [
            'localPath' => $localPath,
            'originalSize' => $input['size'],
            'originalType' => $input['type'],
            'size' => $output['size'],
            'type' => $output['type'],
            'width' => $output['width'],
            'height' => $output['height'],
            'compressionRatio' => $output['ratio'],
            'url' => $output['url'],
            'id' => explode('output/', $output['url'])[1],
        ];
    }

    public function resize($tinyPNGImage, $newFilePath, $width, $height, $method = 'fit')
    {
        $request = curl_init();
        curl_setopt_array($request, array(
            CURLOPT_URL => $tinyPNGImage['url'],
            CURLOPT_USERPWD => "api:" . Texto::encryptDecryptText(TINY_API_KEY, false),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'User-Agent' => 'CakePHP'
            ),
            CURLOPT_POSTFIELDS => json_encode(
                array(
                    'resize' => array(
                        'method' => $method,
                        'width' => $width,
                        'height' => $height
                    )
                )
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ));
        $response = curl_exec($request);
        file_put_contents($newFilePath, $response);

        $tinyPNGImage['width'] = $width;
        $tinyPNGImage['height'] = $height;

        return $tinyPNGImage;
    }
}
