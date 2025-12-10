<?php

class AzureBlob
{
    private static $init = false;
    private static $serverName = '.blob.core.windows.net/';
    private static $msVersion = '2019-12-12';
    private static $accountName;
    private static $accountKey;
    private static $sasDuration;

    private static function init()
    {
        if (!self::$init && \Configure::read('AZURE_FILES')) {
            self::$accountName = \Configure::read('AZURE_CONFIG.account_name');
            self::$accountKey = \Configure::read('AZURE_CONFIG.account_key');
            self::$sasDuration = \Configure::read('AZURE_CONFIG.sas_duration');
        }
        self::$init = true;
    }

    /**
     * Uploads a blob to the public container.
     */
    static function uploadBlob($fileToUploadPath, $pathInContainer, $fileNameInContainer, $isPrivate, $mimeType)
    {
        self::init();
        $storageAccountname = self::$accountName;
        $accesskey = self::$accountKey;

        $containerName = self::getContainer($isPrivate);

        $pathInContainer = self::addLastSlashIfNeeded($pathInContainer);
        $pathInContainer = self::checkAndaddSlashesInBlob($pathInContainer);

        $URL = "https://$storageAccountname.blob.core.windows.net/$containerName$pathInContainer$fileNameInContainer";

        $Date = gmdate('D, d M Y H:i:s \G\M\T');
        $handle = fopen($fileToUploadPath, "r");
        $fileLen = filesize($fileToUploadPath);

        $headerResource = "x-ms-blob-cache-control:max-age=3600\nx-ms-blob-type:BlockBlob\nx-ms-date:$Date\nx-ms-version:" . self::$msVersion;
        $urlResource = "/$storageAccountname/$containerName$pathInContainer$fileNameInContainer";

        $arraysign = array();
        $arraysign[] = 'PUT';               /*HTTP Verb*/
        $arraysign[] = '';                  /*Content-Encoding*/
        $arraysign[] = '';                  /*Content-Language*/
        $arraysign[] = $fileLen;            /*Content-Length (include value when zero)*/
        $arraysign[] = '';                  /*Content-MD5*/
        $arraysign[] = $mimeType;   /*Content-Type*/
        $arraysign[] = '';                  /*Date*/
        $arraysign[] = '';                  /*If-Modified-Since */
        $arraysign[] = '';                  /*If-Match*/
        $arraysign[] = '';                  /*If-None-Match*/
        $arraysign[] = '';                  /*If-Unmodified-Since*/
        $arraysign[] = '';                  /*Range*/
        $arraysign[] = $headerResource;     /*CanonicalizedHeaders*/
        $arraysign[] = $urlResource;        /*CanonicalizedResource*/

        $str2sign = implode("\n", $arraysign);

        $sig = base64_encode(hash_hmac('sha256', urldecode(utf8_encode($str2sign)), base64_decode($accesskey), true));
        $authHeader = "SharedKey $storageAccountname:$sig";

        $headers = [
            'Authorization: ' . $authHeader,
            'x-ms-blob-cache-control: max-age=3600',
            'x-ms-blob-type: BlockBlob',
            'x-ms-date: ' . $Date,
            'x-ms-version: ' . self::$msVersion,
            'Content-Type: ' . $mimeType,
            'Content-Length: ' . $fileLen,
            'User-Agent: CakePHP'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_INFILE, $handle);
        curl_setopt($ch, CURLOPT_INFILESIZE, $fileLen);
        curl_setopt($ch, CURLOPT_UPLOAD, true);
        curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        return self::checkResponseCode($responseCode);
    }


    /**
     * Downloads a public blob, Currently unused.
     *
     */
    static function downloadBlob($pathInContainer, $blobName)
    {
        self::init();
        $storageAccountname = self::$accountName;
        $containerName = self::getContainer(true);
        $URL = "https://$storageAccountname.blob.core.windows.net/$containerName/$pathInContainer$blobName";

        $accesskey = self::$accountKey;
        $Date = gmdate('D, d M Y H:i:s \G\M\T');

        $headerResource = "x-ms-date:$Date\nx-ms-version:" . self::$msVersion;
        $urlResource = "/$storageAccountname/$containerName/$pathInContainer$blobName";

        $arraysign = array();
        $arraysign[] = 'GET';               /*HTTP Verb*/
        $arraysign[] = '';                  /*Content-Encoding*/
        $arraysign[] = '';                  /*Content-Language*/
        $arraysign[] = '';                  /*Content-Length (include value when zero)*/
        $arraysign[] = '';                  /*Content-MD5*/
        $arraysign[] = '';                  /*Content-Type*/
        $arraysign[] = '';                  /*Date*/
        $arraysign[] = '';                  /*If-Modified-Since */
        $arraysign[] = '';                  /*If-Match*/
        $arraysign[] = '';                  /*If-None-Match*/
        $arraysign[] = '';                  /*If-Unmodified-Since*/
        $arraysign[] = '';                  /*Range*/
        $arraysign[] = $headerResource;     /*CanonicalizedHeaders*/
        $arraysign[] = $urlResource;        /*CanonicalizedResource*/

        $str2sign = implode("\n", $arraysign);

        $sig = base64_encode(hash_hmac('sha256', urldecode(utf8_encode($str2sign)), base64_decode($accesskey), true));
        $authHeader = "SharedKey $storageAccountname:$sig";

        $headers = [
            'Authorization: ' . $authHeader,
            'x-ms-date: ' . $Date,
            'x-ms-version: ' . self::$msVersion,
            'User-Agent: CakePHP'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        //file_put_contents(WWW_ROOT.'', $result);

        curl_close($ch);
    }


    /**
     * Uploads a blob to the private container.
     */
    static function uploadPrivateBlob($fileToUploadPath, $pathInContainer, $fileNameInContainer, $mimeType)
    {
        self::init();
        $storageAccountname = self::$accountName;
        $accesskey = self::$accountKey;
        $serverName = self::$serverName;
        $msVersion = self::$msVersion;

        $containerName = self::getContainer(true);
        $pathInContainer = self::addLastSlashIfNeeded($pathInContainer);
        $URL = "https://$storageAccountname$serverName$containerName/$pathInContainer$fileNameInContainer";

        $Date = gmdate('D, d M Y H:i:s \G\M\T');
        $handle = fopen($fileToUploadPath, "r");
        $fileLen = filesize($fileToUploadPath);

        $headerResource = "x-ms-blob-cache-control:max-age=3600\nx-ms-blob-type:BlockBlob\nx-ms-date:$Date\nx-ms-version:$msVersion";
        $urlResource = "/$storageAccountname/$containerName/$pathInContainer$fileNameInContainer";

        $arraysign = array();
        $arraysign[] = 'PUT';               /*HTTP Verb*/
        $arraysign[] = '';                  /*Content-Encoding*/
        $arraysign[] = '';                  /*Content-Language*/
        $arraysign[] = $fileLen;            /*Content-Length (include value when zero)*/
        $arraysign[] = '';                  /*Content-MD5*/
        $arraysign[] = $mimeType;   /*Content-Type*/
        //$arraysign[] = 'application/pdf';   /*Content-Type*/
        $arraysign[] = '';                  /*Date*/
        $arraysign[] = '';                  /*If-Modified-Since */
        $arraysign[] = '';                  /*If-Match*/
        $arraysign[] = '';                  /*If-None-Match*/
        $arraysign[] = '';                  /*If-Unmodified-Since*/
        $arraysign[] = '';                  /*Range*/
        $arraysign[] = $headerResource;     /*CanonicalizedHeaders*/
        $arraysign[] = $urlResource;        /*CanonicalizedResource*/

        $str2sign = implode("\n", $arraysign);

        $sig = base64_encode(hash_hmac('sha256', urldecode(utf8_encode($str2sign)), base64_decode($accesskey), true));

        $authHeader = "SharedKey $storageAccountname:$sig";
        $headers = [
            'Authorization: ' . $authHeader,
            'x-ms-blob-cache-control: max-age=3600',
            'x-ms-blob-type: BlockBlob',
            'x-ms-date: ' . $Date,
            'x-ms-version: ' . self::$msVersion,
            'Content-Type: ' . $mimeType,
            'Content-Length: ' . $fileLen,
            'User-Agent: CakePHP'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_INFILE, $handle);
        curl_setopt($ch, CURLOPT_INFILESIZE, $fileLen);
        curl_setopt($ch, CURLOPT_UPLOAD, true);
        curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        return self::checkResponseCode($responseCode);
    }


    /**
     * Downloads a private blob, Currently unused.
     *
     */
    static function downloadPrivateBlob($pathInContainer, $blobName)
    {
        self::init();
        $storageAccountname = self::$accountName;
        $accesskey = self::$accountKey;
        $serverName = self::$serverName;
        $containerName = self::getContainer(true);
        $URL = "https://$storageAccountname$serverName$containerName/$pathInContainer$blobName";

        $Date = gmdate('D, d M Y H:i:s \G\M\T');
        $headerResource = "x-ms-date:$Date\nx-ms-version:" . self::$msVersion;

        $urlResource = "/$storageAccountname/$containerName/$pathInContainer$blobName";
        $arraysign = array();
        $arraysign[] = 'GET';               /*HTTP Verb*/
        $arraysign[] = '';                  /*Content-Encoding*/
        $arraysign[] = '';                  /*Content-Language*/
        $arraysign[] = '';                  /*Content-Length (include value when zero)*/
        $arraysign[] = '';                  /*Content-MD5*/
        $arraysign[] = '';                  /*Content-Type*/
        $arraysign[] = '';                  /*Date*/
        $arraysign[] = '';                  /*If-Modified-Since */
        $arraysign[] = '';                  /*If-Match*/
        $arraysign[] = '';                  /*If-None-Match*/
        $arraysign[] = '';                  /*If-Unmodified-Since*/
        $arraysign[] = '';                  /*Range*/
        $arraysign[] = $headerResource;     /*CanonicalizedHeaders*/
        $arraysign[] = $urlResource;        /*CanonicalizedResource*/

        $str2sign = implode("\n", $arraysign);

        $sig = base64_encode(hash_hmac('sha256', urldecode(utf8_encode($str2sign)), base64_decode($accesskey), true));

        $authHeader = "SharedKey $storageAccountname:$sig";

        $headers = [
            'Authorization: ' . $authHeader,
            'x-ms-date: ' . $Date,
            'x-ms-version: ' . self::$msVersion,
            'User-Agent: CakePHP'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);

        //file_put_contents(APP . "Files" . $pathInContainer . $blobName, $result); // save the string to a file

        curl_close($ch);
    }

    /**
     * Returns the name of the container if private ot public.
     */
    private static function getContainer($isPrivate)
    {
        if ($isPrivate) {
            return \Configure::read('AZURE_CONFIG.private_container');
        } else {
            return \Configure::read('AZURE_CONFIG.container');
        }
    }

    /**
     * Adds a slash at the start of the string if there is no one.
     */
    private static function checkAndaddSlashesInBlob($blobName)
    {
        if ($blobName[0] != '/' && $blobName[0] != '\\') {
            $blobName = '/' . $blobName;
        }
        return $blobName;
    }

    /**
     * Adds a slash at the end of the string if there is no one.
     */
    private static function addLastSlashIfNeeded($pathInContainer)
    {
        $lastElement = substr($pathInContainer, -1);
        if (($lastElement != '/') && ($lastElement != '\\')) {
            $pathInContainer = $pathInContainer . '/';
        }
        return $pathInContainer;
    }

    /**
     * Returns the url for a blob in a public container.
     */
    public static function getUrlForPublicContainer($blobName)
    {
        self::init();
        $blobName = str_replace('\\', '/', $blobName);
        $blobName = self::checkAndaddSlashesInBlob($blobName);
        $containerName = self::getContainer(false);
        $storageAccountname = self::$accountName;
        $serverName = self::$serverName;
        return "https://$storageAccountname$serverName$containerName$blobName";
    }

    /**
     * Returns true if the response code is 201.
     */
    private static function checkResponseCode($responseCode)
    {
        if ($responseCode == '201' || $responseCode == '202') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the url for a blob in a private container.
     */
    public static function getUrlForPrivateContainer($blobName)
    {
        self::init();
        $blobName = self::checkAndaddSlashesInBlob($blobName);
        $accountName = self::$accountName;
        $accountKey = self::$accountKey;
        $sasDuration = self::$sasDuration;
        $containerName = self::getContainer(true);

        $blobName = str_replace('\\', '/', $blobName);
        $permissions = "r";
        $sasSignVersion = '2017-04-17';

        $startTime = time();
        $expiryTime = $startTime + $sasDuration;

        $expiryTimeIso = gmdate("Y-m-d\TH:i:s\Z", $expiryTime);

        $stringToSign =
            "r\n" . //Permissions
            "\n" .
            "$expiryTimeIso\n" . //Expiry time of the sas url
            "/blob/$accountName/$containerName$blobName\n" .
            "\n" .
            "\n" .
            "\n" .
            "$sasSignVersion\n" .
            "\n" .
            "\n" .
            "\n" .
            "\n";

        $stringToSign = mb_convert_encoding($stringToSign, 'UTF-8', 'ISO-8859-1');

        $sasToken = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($accountKey), true));

        $blobUrlWithSas = sprintf("https://%s.blob.core.windows.net/%s%s?sv=%s&sr=b&se=%s&sp=%s&sig=%s", $accountName, $containerName, $blobName, $sasSignVersion, $expiryTimeIso, $permissions, rawurlencode($sasToken));

        return $blobUrlWithSas;
    }

    /**
     * Deletes a blob.
     */
    public static function deleteBlob($blobName, $isPrivate = false)
    {
        self::init();
        $storageAccountname = self::$accountName;
        $accesskey = self::$accountKey;
        $containerName = self::getContainer($isPrivate);
        $blobName = self::checkAndaddSlashesInBlob($blobName);
        $URL = "https://$storageAccountname.blob.core.windows.net/$containerName$blobName";

        $Date = gmdate('D, d M Y H:i:s \G\M\T');
        $headerResource = "x-ms-date:$Date\nx-ms-version:" . self::$msVersion;

        $urlResource = "/$storageAccountname/$containerName$blobName";
        $arraysign = array();
        $arraysign[] = 'DELETE';               /*HTTP Verb*/
        $arraysign[] = '';                  /*Content-Encoding*/
        $arraysign[] = '';                  /*Content-Language*/
        $arraysign[] = '';                  /*Content-Length (include value when zero)*/
        $arraysign[] = '';                  /*Content-MD5*/
        $arraysign[] = '';                  /*Content-Type*/
        $arraysign[] = '';                  /*Date*/
        $arraysign[] = '';                  /*If-Modified-Since */
        $arraysign[] = '';                  /*If-Match*/
        $arraysign[] = '';                  /*If-None-Match*/
        $arraysign[] = '';                  /*If-Unmodified-Since*/
        $arraysign[] = '';                  /*Range*/
        $arraysign[] = $headerResource;     /*CanonicalizedHeaders*/
        $arraysign[] = $urlResource;        /*CanonicalizedResource*/

        $str2sign = implode("\n", $arraysign);

        $sig = base64_encode(hash_hmac('sha256', urldecode(utf8_encode($str2sign)), base64_decode($accesskey), true));

        $authHeader = "SharedKey $storageAccountname:$sig";

        $headers = [
            'Authorization: ' . $authHeader,
            'x-ms-date: ' . $Date,
            'x-ms-version: ' . self::$msVersion,
            'User-Agent: CakePHP'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return self::checkResponseCode($responseCode);
    }
}
