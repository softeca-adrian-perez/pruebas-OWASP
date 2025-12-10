<?php
App::uses('AzureBlob', 'Lib');
App::uses('Tiny', 'Lib');

class FileManager
{
    private static $azure_files = false;

    private static $account_name;
    private static $account_key;
    private static $container;
    private static $connection_string;
    private static $img_types = array('png', 'jpe', 'jpeg', 'jpg', 'gif', 'bmp', 'ico', 'tiff', 'tif', 'svg', 'svgz', 'webp');
    private static $file_types = array('txt', 'pdf', 'doc', 'rtf', 'xls', 'ppt', 'docx', 'xlsx', 'pptx', 'zip', 'rar', 'png', 'jpe', 'jpeg', 'jpg', 'gif', 'bmp', 'ico', 'tiff', 'tif', 'svg', 'svgz', 'webp');

    private static $init = false;

    private static function init()
    {
        if (!self::$init) {
            self::$azure_files = \Configure::read('AZURE_FILES');

            if (self::$azure_files) {
                self::$account_name = \Configure::read('AZURE_CONFIG.account_name');
                self::$account_key = \Configure::read('AZURE_CONFIG.account_key');
                self::$container = \Configure::read('AZURE_CONFIG.container');
                self::$connection_string = implode(';', array(
                    'DefaultEndpointsProtocol=https',
                    'AccountName=' . self::$account_name,
                    'AccountKey=' . self::$account_key,
                ));
            }
        }
        self::$init = true;
    }

    public static function add_problem_upload($problem)
    {
        $upload_problems = CakeSession::read('GNMAAG_UPLOAD_PROBLEMS');
        if (!is_array($upload_problems)) {
            $upload_problems = array();
        }
        $upload_problems[] = $problem;
        CakeSession::write('GNMAAG_UPLOAD_PROBLEMS', $upload_problems);
    }

    public static function clean_problems_upload()
    {
        $upload_problems = [];
        CakeSession::write('GNMAAG_UPLOAD_PROBLEMS', $upload_problems); // T001 SECURITY - It is not changed
    }

    public static function get_problems_upload()
    {
        $upload_problems = CakeSession::read('GNMAAG_UPLOAD_PROBLEMS');
        if (empty($upload_problems)) {
            $upload_problems[0] = __t(ConstantsMessages::BAD_SAVED);
        }
        return $upload_problems;
    }

    /*
    $source_file_path = C:\proyectos\html\alliance\app\webroot\img\profiles\r99KsZyYGrJr57nua7JXR2vdDXU4ttGT.jpg
    $destiny_file_dir = /img/profiles
    $destiny_file_name = r99KsZyYGrJr57nua7JXR2vdDXU4ttGT.jpg
    $file_type = ConstantsFileType::IMAGE
     */
    public static function upload_file($source_file_path, $destiny_file_dir, $destiny_file_name, $file_type, $isPrivateContainer = false)
    {
        self::init();
        if (self::check_extension_file($destiny_file_name, $file_type)) {

            if (\Configure::read('SCANII_ANTIVIRUS_CONFIG.active') && self::check_has_virus_file($source_file_path)) {
                unlink($source_file_path);
                self::add_problem_upload(self::get_real_name($destiny_file_name) . ' - ' . __t(ConstantsMessages::VIRUS_FILE));
                return false;
            }

            if (self::$azure_files) {
                try {
                    if ($isPrivateContainer) {
                        $res = AzureBlob::uploadPrivateBlob($source_file_path, $destiny_file_dir, $destiny_file_name, self::get_mime_type($destiny_file_name));
                    } else {
                        $res = AzureBlob::uploadBlob($source_file_path, $destiny_file_dir, $destiny_file_name, false, self::get_mime_type($destiny_file_name));
                    }
                    unlink($source_file_path);
                    return $res;
                } catch (\Exception $e) {
                    \CakeLog::error($e->getMessage() . PHP_EOL);
                    return false;
                }
            } else {
                return $destiny_file_name;
            }
        } else {
            unlink($source_file_path);
            self::add_problem_upload(self::get_real_name($destiny_file_name) . ' - ' . __t(ConstantsMessages::WRONG_EXTENSION));
            return false;
        }
    }

    private static function exists_file($blobClient, $container, $blob_name)
    {
        try {
            $blob = $blobClient->getBlobProperties($container, $blob_name);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /*
    $blob_name = /img/profiles/r99KsZyYGrJr57nua7JXR2vdDXU4ttGT.jpg
     */
    public static function get_url($blob_name, $isPrivateContainer = false)
    {
        self::init();

        if (self::$azure_files) {
            if ($isPrivateContainer) {
                $res = AzureBlob::getUrlForPrivateContainer($blob_name);
            } else {
                $res = AzureBlob::getUrlForPublicContainer($blob_name);
            }
            return $res;
        } else {
            return $blob_name;
        }
    }

    /*
    $file_dir = C:\proyectos\html\alliance\app\webroot\
    $blob_name = /img/profiles/r99KsZyYGrJr57nua7JXR2vdDXU4ttGT.jpg
     */
    public static function delete_file($file_dir, $blob_name, $isPrivate = false)
    {
        self::init();
        $blob_name = self::remove_first_slash($blob_name);
        if (self::$azure_files) {
            try {
                return AzureBlob::deleteBlob($blob_name, $isPrivate);
            } catch (\Exception $e) {
                \CakeLog::error($e->getMessage() . PHP_EOL);
            }
        } else {
            $file_dir = self::use_slash_and_add_last_slash($file_dir);
            if (file_exists($file_dir . $blob_name) && substr($blob_name, -1) != '/') {
                unlink($file_dir . $blob_name);
            }
            return true;
        }
    }

    private static function remove_first_slash($text)
    {
        if (strlen($text) > 0 && substr($text, 0, 1) == '/') {
            $text = substr($text, 1);
        }
        return $text;
    }

    private static function remove_first_last_slash($text)
    {
        if (strlen($text) > 0 && substr($text, 0, 1) == '/') {
            $text = substr($text, 1);
        }
        if (strlen($text) > 0 && substr($text, -1) == '/') {
            $text = substr($text, 0, -1);
        }
        return $text;
    }

    private static function use_slash_and_add_last_slash($text)
    {
        $text = str_replace('\\', '/', $text);
        if (strlen($text) > 0 && substr($text, -1) != '/') {
            $text = $text . '/';
        }
        return $text;
    }

    public static function get_renamed_name($file_name)
    {
        $file_name_last_parts = pathinfo($file_name);
        $fileNameSanitized = filter_var($file_name_last_parts['filename'], FILTER_SANITIZE_URL);
        if ($fileNameSanitized) {
            $file_name_last_parts['filename'] = $fileNameSanitized;
        }
        return str_replace(' ', '_', $file_name_last_parts['filename'] . '__' . time() . '.' . ($file_name_last_parts['extension'] == 'svg' ? 'png' : $file_name_last_parts['extension']));
    }

    public static function get_real_name($file_name)
    {
        $file_name_last_parts = pathinfo($file_name);
        $filename = $file_name_last_parts['filename'];
        $filename_parts = explode('__', $filename);
        if (is_array($filename_parts) && isset($filename_parts[0])) {
            $filename = $filename_parts[0];
        }
        $extension = $file_name_last_parts['extension'];
        return $filename . '.' . $extension;
    }

    private static function check_extension_file($destiny_file_name, $file_type)
    {
        $ext = pathinfo($destiny_file_name, PATHINFO_EXTENSION);
        $ext = strtolower($ext);

        if ($file_type == ConstantsFileType::IMAGE) {
            if (in_array($ext, self::$img_types)) {
                return true;
            }
        } else if ($file_type == ConstantsFileType::FILE) {
            if (in_array($ext, self::$file_types)) {
                return true;
            }
        }

        return false;
    }

    // https://stackoverflow.com/questions/35299457/getting-mime-type-from-file-name-in-php
    public static function get_mime_type($filename)
    {
        $idx = explode('.', $filename);
        $count_explode = count($idx);
        $idx = strtolower($idx[$count_explode - 1]);

        $mimet = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'webp' => 'image/webp',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        if (isset($mimet[$idx])) {
            return $mimet[$idx];
        } else {
            return 'application/octet-stream';
        }
    }

    public static function check_has_virus_file($source_file_path)
    {

        $client = new Scanii\ScaniiClient(\Configure::read('SCANII_ANTIVIRUS_CONFIG.key'), \Configure::read('SCANII_ANTIVIRUS_CONFIG.secret'));
        $result = $client->process($source_file_path);
        \CakeLog::error(((string) $result->getRawResponse()) . PHP_EOL);

        $contentLength = $result->getContentLength(); // si > 0, significa que lo ha analizado
        $findings = $result->getFindings();

        if ($contentLength > 0 && is_array($findings) && count($findings) == 0) {
            return false;
        }

        return true;
    }

    public static function generate_json_file($data, $key1 = null, $key2 = null, $key3 = null, $key4 = null)
    {
        $path = ConstantsPath::DIR_GARAGE_UPDATES_ABSOLUTE . DS . "garages_logs.json";

        if (file_exists($path)) { // Edit file
            $json = file_get_contents($path);
            $contents = utf8_encode($json);
            $networks_garages = json_decode($contents, true);
        } else { // Create file
            $networks_garages = array();
        }

        if (isset($key4)) {
            $networks_garages[$key1][$key2][$key3][$key4] = $data;
        } elseif (isset($key3)) {
            $networks_garages[$key1][$key2][$key3] = $data;
        } elseif (isset($key2)) {
            $networks_garages[$key1][$key2] = $data;
        } elseif (isset($key1)) {
            $networks_garages[$key1] = $data;
        } else {
            $networks_garages = $data;
        }

        file_put_contents($path, json_encode($networks_garages));
    }

    public static function generate_json_file_lobster($prefix, $data)
    {
        $today = date("Ymd_H\hi\m");
        //This date format is used for the email subject.
        $todaySecondFormat = date('d/m/Y H:i');
        $filename = "Benelux_" . $prefix . "_Import_" . $today . ".json";
        $path = ConstantsPath::DIR_GARAGE_UPDATES_ABSOLUTE . DS . $filename;

        // Create file
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

        return array(
            'filename' => $filename,
            'path' => $path,
            'date' => $todaySecondFormat
        );
    }

    public static function get_summary_json_file()
    {
        $path = ConstantsPath::DIR_GARAGE_UPDATES_ABSOLUTE . DS . "garages_logs.json";

        if (file_exists($path)) { // Edit file
            $json = file_get_contents($path);
            $contents = utf8_encode($json);
            $networks = json_decode($contents, true);
            foreach ($networks as $key => $network) {
                if ($key == 'WithNetworks') {
                    foreach ($network as $key_network => $network_garages) {
                        self::get_array_summary($network_garages, $key, $key_network);
                    }
                } else {
                    self::get_array_summary($network, $key);
                }
            }
        }
    }

    public static function get_array_summary($array_data, $key, $key_network = null)
    {
        $resultsCreateNotErrors = array_filter($array_data['Garages'], function ($data) {
            return $data['IsCreated'] && !$data['WithErrors'];
        });
        $resultsUpdateNotErrors = array_filter($array_data['Garages'], function ($data) {
            return !$data['IsCreated'] && !$data['WithErrors'];
        });
        $resultsCreateErrors = array_filter($array_data['Garages'], function ($data) {
            return $data['IsCreated'] && $data['WithErrors'];
        });
        $resultsUpdateErrors = array_filter($array_data['Garages'], function ($data) {
            return !$data['IsCreated'] && $data['WithErrors'];
        });

        $summary = array(
            "NumberGarages" => count($array_data['Garages']),
            "NumberSuccessfulCreated" => count($resultsCreateNotErrors),
            "NumberSuccessfulUpdated" => count($resultsUpdateNotErrors),
            "NumberFailsCreated" => count($resultsCreateErrors),
            "NumberFailsUpdated" => count($resultsUpdateErrors)
        );
        isset($key_network) ? FileManager::generate_json_file($summary, $key, $key_network, 'Summary') : FileManager::generate_json_file($summary, $key, 'Summary');
    }

    public static function generate_unique_id()
    {
        return uniqid();
    }

    public static function verifyIsImage($uploadedFile)
    {
        $isImage = getimagesize($uploadedFile);
        return isset($isImage['mime']) && in_array($isImage['mime'], ConstantsUpdateImage::MIME_TYPE_FILES);
    }

    public static function getExtension($uploadedFile)
    {
        $extension_split = explode('.', $uploadedFile['name']);
        $extension_end = end($extension_split);
        $extension = strtolower($extension_end);
        return $extension;
    }

    public function generateFileName($fileName)
    {
        return preg_replace('/[^0-9A-Za-z]/', '-', $fileName);
    }

    public static function check_file($file)
    {
        $fileSize = $file['size'];
        if ($fileSize < ConstantsUpdateFile::SIZE_FILES_UPLOAD) {
            $extension = FileManager::getExtension($file);
            if (isset($extension) && in_array($extension, ConstantsUpdateFile::EXTENSIONS_FILES)) {
                return ConstantsFileErrorTypes::OK;
            } else {
                return ConstantsFileErrorTypes::FILE_EXTENSION_ERROR;
            }
        } else {
            return ConstantsFileErrorTypes::SIZE_ERROR;
        }
    }

    public static function check_image($file, $image)
    {
        $imageSize = $file['size'];
        if (empty($imageSize)) {
            return ConstantsFileErrorTypes::SIZE_ERROR;
        }
        $isImage = FileManager::verifyIsImage($image);

        if ($isImage && $imageSize < ConstantsUpdateImage::SIZE_FILES_UPLOAD) {
            $extension = FileManager::getExtension($file);
            if (isset($extension) && in_array($extension, ConstantsUpdateImage::EXTENSIONS_FILES)) {
                return ConstantsFileErrorTypes::OK;
            } else {
                return ConstantsFileErrorTypes::IMAGE_EXTENSION_ERROR;
            }
        } elseif (!$isImage) {
            return ConstantsFileErrorTypes::IMAGE_EXTENSION_ERROR;
        } else {
            return ConstantsFileErrorTypes::SIZE_ERROR;
        }
    }

    public static function tiny_to_image($imageWebp)
    {
        $tiny = new Tiny();
        $urlOutputFile = $tiny->loadImage($imageWebp);

        return $urlOutputFile;
    }

    public static function upload_image_webroot($image_data, $imageFile, $file_type, $pathfile)
    {
        $result = false;
        if ($imageFile['error'] != ConstantsFlag::ERROR_NOT_FILE) {
            if (in_array(mime_content_type($image_data), array('image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp'))) {
                $imageFile['source'] = file_get_contents($image_data);
                $imageFile['name'] = FileManager::get_renamed_name(preg_replace("/[^a-zA-Z0-9.]/", "", $imageFile['name']));

                $imageTiny = self::tiny_to_image($imageFile);

                if (filesize($imageTiny) > ConstantsUpdateImage::SIZE_FILES_UPLOAD) {
                    return false;
                }

                $img2 = imagecreatefromwebp($imageTiny);

                if (imagewebp($img2, WWW_ROOT . $pathfile . '/' . $imageTiny)) {
                    if (FileManager::upload_file(WWW_ROOT . $pathfile . '/' . $imageTiny, $pathfile, $imageTiny, $file_type)) {
                        $result = $imageTiny;
                    }
                    unlink($imageTiny);
                }
            }
        }
        return $result;
    }

    public static function sendImageToTiny($originalPath)
    {
        $tiny = new Tiny();
        return $tiny->send($originalPath);
    }

    public static function resizeUsingTiny($tinyPNGImage, $newFilePath, $width, $height)
    {
        $tiny = new Tiny();
        return $tiny->resize($tinyPNGImage, $newFilePath, $width, $height);
    }
}
