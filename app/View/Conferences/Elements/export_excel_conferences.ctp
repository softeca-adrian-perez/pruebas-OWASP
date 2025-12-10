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
    array('label' => strtoupper(__t('Conference.Name')), 'width' => '36'),
    array('label' => strtoupper(__t('Conference.Start_date')), 'width' => '36'),
    array('label' => strtoupper(__t('Conference.Duration')), 'width' => '36'),
    array('label' => strtoupper(__t('Conference.Venue')), 'width' => '36'),
    array('label' => strtoupper(__t('Conference.Status')), 'width' => '36'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
    array(
        1,
    )
);

//////////////////////// FILL PAGES ////////////////////////

$cont_row_garages = 2;
foreach ($conferences as $conference) {

    //////////////////////// GARAGE - PAGE ////////////////////////
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $this->PhpExcel->row = $cont_row_garages;
    $conference_row = array();

    $conference_row[] = $conference['Conference']['name'];
    $conference_row[] = Fecha::toFormatoVistaFecha($conference['Conference']['start_date']);
    $conference_row[] = $conference['Conference']['duration'];
    $conference_row[] = $conference['Venue']['name'];
    $conference_row[] = ($conference['Conference']['status'] == ConstantsBooleans::YES ? __t('General.Active') : __t('General.No_active'));

    $this->PhpExcel->addTableRow($conference_row, $font_color_row);
    $cont_row_garages++;
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Conference.Conferences') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
