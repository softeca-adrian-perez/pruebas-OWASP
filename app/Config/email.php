<?php
require_once(__DIR__ . '/configuration.php');

class EmailConfig
{
    public $smtp_aag = array(
        'transport' => 'Smtp',
        'from' => GNMAAG_EMAIL_CONFIGURATION_NO_REGION_FROM,
        'host' => GNMAAG_EMAIL_CONFIGURATION_NO_REGION_HOST,
        'port' => GNMAAG_EMAIL_CONFIGURATION_NO_REGION_PORT,
        'timeout' => 30,
        'username' => GNMAAG_EMAIL_CONFIGURATION_NO_REGION_USERNAME,
        'password' => GNMAAG_EMAIL_CONFIGURATION_NO_REGION_PASSWORD,
        'client' => null,
        'log' => false,
        'tls' => GNMAAG_EMAIL_CONFIGURATION_NO_REGION_TLS,
        //'charset' => 'utf-8',
        //'headerCharset' => 'utf-8',
    );

    public $smtp_aag_benelux = array(
        'transport' => 'Smtp',
        'from' => GNMAAG_EMAIL_CONFIGURATION_BENELUX_FROM,
        'host' => GNMAAG_EMAIL_CONFIGURATION_BENELUX_HOST,
        'port' => GNMAAG_EMAIL_CONFIGURATION_BENELUX_PORT,
        'timeout' => 30,
        'username' => GNMAAG_EMAIL_CONFIGURATION_BENELUX_USERNAME,
        'password' => GNMAAG_EMAIL_CONFIGURATION_BENELUX_PASSWORD,
        'client' => null,
        'log' => false,
        'tls' => GNMAAG_EMAIL_CONFIGURATION_BENELUX_TLS,
        //'charset' => 'utf-8',
        //'headerCharset' => 'utf-8',
    );

    public $smtp_aag_uk_ireland = array(
        'transport' => 'Smtp',
        'from' => GNMAAG_EMAIL_CONFIGURATION_UK_IRELAND_FROM,
        'host' => GNMAAG_EMAIL_CONFIGURATION_UK_IRELAND_HOST,
        'port' => GNMAAG_EMAIL_CONFIGURATION_UK_IRELAND_PORT,
        'timeout' => 30,
        'username' => GNMAAG_EMAIL_CONFIGURATION_UK_IRELAND_USERNAME,
        'password' => GNMAAG_EMAIL_CONFIGURATION_UK_IRELAND_PASSWORD,
        'client' => null,
        'log' => false,
        'tls' => GNMAAG_EMAIL_CONFIGURATION_UK_IRELAND_TLS,
        //'charset' => 'utf-8',
        //'headerCharset' => 'utf-8',
    );
}
