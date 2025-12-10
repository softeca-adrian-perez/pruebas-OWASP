<?php

//**********************************************************
//**********************************************************
//***
//*** Curl
//***
//**********************************************************
//**********************************************************

echo "<h1>CURL</h1>";
$ch = curl_init('https://www.howsmyssl.com/a/check');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$data = curl_exec($ch);
curl_close($ch);
$json = JSON_decode($data);

echo "<h1>TLS version: " . $json->tls_version . "</h1>\n";
// echo "<pre>" . print_r($json,true) . "</pre>\n";
