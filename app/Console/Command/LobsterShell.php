<?php

App::uses('Lobster', 'Lib');

class LobsterShell extends Shell
{

    /**
     * Sync all the garages of GV network.
     *
     * console\cake lobster garagesAXGVSync
     */
    public function garagesAXGVSync()
    {
        $lobster = new Lobster();
        $lobster->garagesGVSync(ConstantsErpCodes::AX);
    }

    /**
     * Sync all the garages of GV network.
     *
     * console\cake lobster garagesSAPGVSync
     */
    public function garagesSAPGVSync()
    {
        $lobster = new Lobster();
        $lobster->garagesGVSync(ConstantsErpCodes::SAP);
    }


    /**
     * Sync all the vehicles (brands) for GNM AAG.
     *
     * console\cake lobster vehiclesSync
     */
    public function vehiclesSync()
    {
        exit;
        // $lobster = new Lobster();
        // $lobster->vehiclesSync();
    }


    /**
     * Gets coordinates for cities and saves them in db
     *
     * console\cake lobster citiesSaveCoordinates
     */
    public function citiesSaveCoordinates()
    {
        $lobster = new Lobster();
        $lobster->citiesSaveCoordinates();
    }
}
