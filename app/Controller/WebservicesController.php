<?php

require_once('../Lib/webservice/php-wsdl/class.phpwsdl.php');

class WebservicesController extends AppController
{
    public function index()
    {
        $this->autoRender = false;
        $wsdl = PhpWsdl::CreateInstance(
            null,
            WEBSERVICE_WSDL,
            '../Lib/webservice/cache',
            array(
                '../Lib/webservice/SoapWebservice.php', // all classes for generator
                '../Lib/webservice/Response.php',
                '../Lib/webservice/OutletData.php',
                '../Lib/webservice/GeneralData.php',
                '../Lib/webservice/WsProvince.php',
                '../Lib/webservice/WsCountry.php',
                '../Lib/webservice/WsNetwork.php',
                '../Lib/webservice/Manager.php',
                '../Lib/webservice/Company.php',

                '../Lib/webservice/Members.php',
                '../Lib/webservice/MemberItem.php',

                '../Lib/webservice/Coordinators.php',
                '../Lib/webservice/CoordinatorItem.php',

                '../Lib/webservice/OpeningHours.php',

                '../Lib/webservice/ServiceItem.php',
                '../Lib/webservice/PassengersVehicleServices.php',
                '../Lib/webservice/PassengersDriverServices.php',
                '../Lib/webservice/HeavyComVehicleServices.php',
                '../Lib/webservice/HeavyComDriverServices.php',
                '../Lib/webservice/AutoPartsServices.php',
                '../Lib/webservice/ToolServices.php',

                '../Lib/webservice/VehicleTypeItem.php',
                '../Lib/webservice/LightVehicles.php',
                '../Lib/webservice/HeavyVehicles.php',

                '../Lib/webservice/Agreements.php',
                '../Lib/webservice/AgreementItem.php',
            ),
            null,
            null,
            null,
            false,
            false
        );

        $wsdl->SoapServerOptions = array(
            'soap_version'    =>    SOAP_1_2,
            'encoding'        =>    'UTF-8',
            'compression'    =>    SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 9
        );

        ini_set('soap.wsdl_cache_enabled', 0);    // Disable caching in PHP
        PhpWsdl::$CacheTime = 0;                    // Disable caching in PhpWsdl

        // Run the SOAP server
        // WSDL requested by the client?
        if ($wsdl->IsWsdlRequested()) {
            $wsdl->Optimize = false;                // Don't optimize WSDL to send it human readable to the browser
        }
        $wsdl->RunServer();                        // Finally, run the server
    }
}
