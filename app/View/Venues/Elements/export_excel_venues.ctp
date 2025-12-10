<?php

$this->PhpExcel->createWorksheet();

$n_hojas = array(
    0 => '0',
);

$this->PhpExcel->cambiarTituloHoja('1');

$this->PhpExcel->setDefaultFont('Calibri', 12);
$this->PhpExcel->setDefaultBorder();

$rgbColor = 'FFFFFF';
$rgbBackgroundColor = '0077FF';
$font_color_row = '000000';

//////////////////////// VENUES - PAGE ////////////////////////

$this->PhpExcel->irAHoja(0);
$this->PhpExcel->row = 1;
$this->PhpExcel->cambiarTituloHoja(__t('Venue.Venues'));


$table = array(
    array('label' => __t('Venue.Name'), 'width' => '36'),
    array('label' => __t('Venue.Address_1'), 'width' => '44'),
    array('label' => __t('Venue.Address_2'), 'width' => '36'),
    array('label' => __t('Garage.Town') . '/' . __t('Garage.City'), 'width' => '25'),
    array('label' => __t('Garage.Sales_area'), 'width' => '25'),
    array('label' => __t('Venue.Post_code'), 'width' => '22'),
    array('label' => __t('Venue.Telephone'), 'width' => '20'),
    array('label' => __t('Venue.Venue_type'), 'width' => '20'),
    array('label' => __t('General.Active'), 'width' => '16'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);


//////////////////////// FILL PAGES ////////////////////////

$cont_row_venues = 2;
foreach ($venues as $venue) {

    //////////////////////// VENUE - PAGE ////////////////////////
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $this->PhpExcel->row = $cont_row_venues;

    $venue_row = array();

    $venue_row[] = $venue['Venue']['name'];
    $venue_row[] = $venue['Venue']['address_1'];
    $venue_row[] = $venue['Venue']['address_2'];
    $venue_row[] = $venue['Venue']['town'];
    $venue_row[] = isset($venue['Venue']['sales_area_id']) ? $sales_area[$venue['Venue']['sales_area_id']] : '';
    $venue_row[] = $venue['Venue']['post_code'];
    $venue_row[] = $venue['Venue']['telephone'];
    $venue_row[] = $venue['VenueType']['name_' . __l()];
    $venue_row[] = $venue['Venue']['active'] == 1 ? __t('General.Yes') : __t('General.No');


    $this->PhpExcel->addTableRow($venue_row, $font_color_row);
    $cont_row_venues++;
}




$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Venue.Venues') . Fecha::getCompleteDate() . '.xlsx');

header('Set-Cookie: fileDownload=true; path=/');
