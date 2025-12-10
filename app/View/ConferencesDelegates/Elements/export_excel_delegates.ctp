<?php

$this->PhpExcel->createWorksheet();

$n_hojas = array(
    0 => '0',
);

$this->PhpExcel->cambiarTituloHoja('1');

$this->PhpExcel->setDefaultFont('Calibri', 12);
$this->PhpExcel->setDefaultBorder();

$rgbColor = 'FFFFFF';
$rgbBackgroundColor = '145474';
$font_color_row = '808080';

//////////////////////// CONFERENCE - PAGE ////////////////////////

$this->PhpExcel->irAHoja(0);
$this->PhpExcel->row = 1;
$this->PhpExcel->cambiarTituloHoja(__t('Conference.Conferences'));

$table = array(
    array('label' => strtoupper(__t('Delegate.Conference_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Delegate.Delegate_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Delegate.Venue_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Delegate.Distributor_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Delegate.Supplier_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Delegate.Garage_name')), 'width' => '36'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);


//////////////////////// FILL PAGES ////////////////////////

$cont_row_garages = 2;
foreach ($conferences_delegates as $delegate) {

    //////////////////////// GARAGE - PAGE ////////////////////////
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $this->PhpExcel->row = $cont_row_garages;
    $delegate_row = array();

    $delegate_row[] = $delegate['Conference']['name'];
    $delegate_row[] = $delegate['ConferenceDelegate']['contact'];
    $delegate_row[] = $delegate['Venue']['name'];
    $delegate_row[] = $delegate['Distributor']['name'];
    $delegate_row[] = $delegate['Supplier']['name'];
    $delegate_row[] = $delegate['Garage']['name'];

    $this->PhpExcel->addTableRow($delegate_row, $font_color_row);
    $cont_row_garages++;
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Delegate.Delegates') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
