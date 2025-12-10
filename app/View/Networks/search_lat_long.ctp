<?php

$this->PhpExcel->createWorksheet();
$this->PhpExcel->cambiarTituloHoja(__('DistribuidoresFR'));
$this->PhpExcel->setDefaultFont('Calibri', 12);
$this->PhpExcel->setDefaultBorder();

$rgbColor = 'FFFFFF';
$rgbBackgroundColor = '0077FF';
$font_color_row = '000000';

$table = array(
    // PROFILE
    array('label' => __('id'), 'width' => '12'),
    array('label' => __('Latitude'), 'width' => '12'),
    array('label' => __('Longitude'), 'width' => '12'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
foreach($garages as $garage){
    $garage_row = array();
    $garage_row[] = $garage['Garage']['id'];
    $garage_row[] = $garage['Garage']['Latitude'];
    $garage_row[] = $garage['Garage']['Longitude'];

    $this->PhpExcel->addTableRow($garage_row, $font_color_row);
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__('DIstrbiuidoresFr') . "_" . date('Y') . '_' . date('m') . '_' . date('d') . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');