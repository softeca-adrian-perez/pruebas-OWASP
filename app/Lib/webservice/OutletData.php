<?php

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 * Container for all the garage details
 *
 * @pw_element GeneralData $GeneralData
 * @pw_element Manager $Manager
 * @pw_element Company $Company
 * @pw_element Members $Members
 * @pw_element Coordinators $Coordinators
 * @pw_element OpeningHours $OpeningHours
 * @pw_element PassengersVehicleServices $PassengersVehicleServices
 * @pw_element PassengersDriverServices $PassengersDriverServices
 * @pw_element HeavyComVehicleServices $HeavyComVehicleServices
 * @pw_element HeavyComDriverServices $HeavyComDriverServices
 * @pw_element AutoPartsServices $AutoPartsServices
 * @pw_element ToolServices $ToolServices
 * @pw_element LightVehicles $LightVehicles
 * @pw_element HeavyVehicles $HeavyVehicles
 * @pw_element Agreements $Agreements
 *
 * @pw_complex OutletData Garage or Shop details
 */
class OutletData{

    public $GeneralData;
    public $Manager;
    public $Company;
    public $Members;
    public $Coordinators;
    public $OpeningHours;
    public $PassengersVehicleServices;
    public $PassengersDriverServices;
    public $HeavyComVehicleServices;
    public $HeavyComDriverServices;
    public $AutoPartsServices;
    public $ToolServices;
    public $LightVehicles;
    public $HeavyVehicles;
    public $Agreements;

    public function OutletData($datos_taller){
        $this->GeneralData = new GeneralData(isset($datos_taller['GeneralData']) && is_array($datos_taller['GeneralData']) ? $datos_taller['GeneralData'] : array());
        $this->Manager = new Manager(isset($datos_taller['Manager']) && is_array($datos_taller['Manager']) ? $datos_taller['Manager'] : array());
        $this->Company = new Company(isset($datos_taller['Company']) && is_array($datos_taller['Company']) ? $datos_taller['Company'] : array());
        $this->Members = new Members(isset($datos_taller['Members']) && is_array($datos_taller['Members']) ? $datos_taller['Members'] : array());
        $this->Coordinators = new Coordinators(isset($datos_taller['Coordinators']) && is_array($datos_taller['Coordinators']) ? $datos_taller['Coordinators'] : array());
        $this->OpeningHours = new OpeningHours(isset($datos_taller['OpeningHours']) && is_array($datos_taller['OpeningHours']) ? $datos_taller['OpeningHours'] : array());
        $this->PassengersVehicleServices = new PassengersVehicleServices(isset($datos_taller['PassengersVehicle']) && is_array($datos_taller['PassengersVehicle']) ? $datos_taller['PassengersVehicle'] : array());
        $this->PassengersDriverServices = new PassengersDriverServices(isset($datos_taller['PassengersDriver']) && is_array($datos_taller['PassengersDriver']) ? $datos_taller['PassengersDriver'] : array());
        $this->HeavyComVehicleServices = new HeavyComVehicleServices(isset($datos_taller['HeavyComVehicle']) && is_array($datos_taller['HeavyComVehicle']) ? $datos_taller['HeavyComVehicle'] : array());
        $this->HeavyComDriverServices = new HeavyComDriverServices(isset($datos_taller['HeavyComDriver']) && is_array($datos_taller['HeavyComDriver']) ? $datos_taller['HeavyComDriver'] : array());
        $this->AutoPartsServices = new AutoPartsServices(isset($datos_taller['AutoPartsServices']) && is_array($datos_taller['AutoPartsServices']) ? $datos_taller['AutoPartsServices'] : array());
        $this->ToolServices = new ToolServices(isset($datos_taller['ToolServices']) && is_array($datos_taller['ToolServices']) ? $datos_taller['ToolServices'] : array());
        $this->LightVehicles = new LightVehicles(isset($datos_taller['LightVehicles']) && is_array($datos_taller['LightVehicles']) ? $datos_taller['LightVehicles'] : array());
        $this->HeavyVehicles = new HeavyVehicles(isset($datos_taller['HeavyVehicles']) && is_array($datos_taller['HeavyVehicles']) ? $datos_taller['HeavyVehicles'] : array());
        $this->Agreements = new Agreements(array());
    }
}

/**
 * @pw_complex OutletDataArray An array of Garages or Shops
 */