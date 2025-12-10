<?php

require(dirname(__FILE__) . '/../Lib/Texto.php');
require(dirname(__FILE__) . '/../Config/configuration.php');

try {
    // LeadGen
    $urlLeadGen = @$_SERVER['GNMAAG_LEADGEN_API_URL_CONFIGURATION'] . '/auth/login';
    $dataLeadGen = array(
        'email' => @$_SERVER['GNMAAG_LEADGEN_API_EMAIL'],
        'password' => Texto::encryptDecryptText(@$_SERVER['GNMAAG_LEADGEN_API_KEY_CONFIGURATION'], false),
    );
    $resultLeadGen = connect($urlLeadGen, $dataLeadGen);
    echo isset($resultLeadGen) && isset($resultLeadGen['token']) ? "<br>" . 'LeadGen OK' : "<br>" . 'LeadGen Error';

    // AGN
    $urlAGN = @$_SERVER['GNMAAG_AGN_API_URL_CONFIGURATION'] . '/auth/login';
    $dataAGN = array(
        'email' => @$_SERVER['GNMAAG_AGN_API_EMAIL'],
        'password' => Texto::encryptDecryptText(@$_SERVER['GNMAAG_AGN_API_KEY_CONFIGURATION'], false),
    );

    $resultAGN = connect($urlAGN, $dataAGN);
    echo isset($resultAGN) && isset($resultAGN['token']) ? "<br>" . 'AGN OK' : "<br>" . 'AGN Error';
} catch (\Exception $e) {
    returnError();
}

function connect($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type:application/json',
        'User-Agent: CakePHP'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $result;
}

function devolver_error()
function returnError()
{
    header(@$_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    exit(0);
}
