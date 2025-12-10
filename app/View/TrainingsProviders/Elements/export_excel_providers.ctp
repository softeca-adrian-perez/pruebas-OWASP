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
$this->PhpExcel->cambiarTituloHoja(__t('Training.Trainings_providers'));

$table = array(
    array('label' => strtoupper(__t('Training.Training_provider_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Email')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Phone')), 'width' => '36'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);

//////////////////////// FILL PAGES ////////////////////////

foreach ($providers as $provider) {

    //////////////////////// GARAGE - PAGE ////////////////////////
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $provider_row = array();
    $provider_row[] = $provider['TrainingProvider']['name'];
    $provider_row[] = $provider['TrainingProvider']['email'];
    $provider_row[] = $provider['TrainingProvider']['phone'];


    $this->PhpExcel->addTableRow($provider_row, $font_color_row);
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Training.Trainings_providers') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
