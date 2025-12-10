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
$this->PhpExcel->cambiarTituloHoja(__t('Training.Trainings_credits'));

$table = array(
    array('label' => strtoupper(__t('Training.Garage_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Default_credits')), 'width' => '36'),
    array('label' => strtoupper(__t('Conference.Start_date')), 'width' => '36'),
    array('label' => strtoupper(__t('Software.End_date')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Extra_credits')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Credits_taken')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.CreditsRemaining')), 'width' => '36'),
    array('label' => strtoupper(__t('Allowance.Actual_allowance')), 'width' => '36'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
    array(
        2,
        3,
    )
);

//////////////////////// FILL PAGES ////////////////////////

foreach ($garages_credits as $garage) {

    //////////////////////// GARAGE - PAGE ////////////////////////
    $this->PhpExcel->irAHoja($n_hojas[0]);

    $garage_row = array();
    $garage_row[] = $garage['Garage']['name'];
    $garage_row[] = $garage['Network']['credit'];
    $garage_row[] = isset($garage['TrainingAllowance']['start_date']) ? Fecha::toFormatoVistaFecha($garage['TrainingAllowance']['start_date']) : '';
    $garage_row[] = isset($garage['TrainingAllowance']['end_date']) ? Fecha::toFormatoVistaFecha($garage['TrainingAllowance']['end_date']) : '';

    $garage_row[] = $sums_given_list[$garage['TrainingAllowance']['id']];
    $garage_row[] = $sums_spent_list[$garage['TrainingAllowance']['id']];
    $garage_row[] = ($sums_given_list[$garage['TrainingAllowance']['id']] - $sums_spent_list[$garage['TrainingAllowance']['id']]) + $garage['Network']['credit'];

    $garage_row[] = $garage['TrainingAllowance']['is_actual'] == ConstantsBooleans::YES ? __t('General.Yes') : '';

    $this->PhpExcel->addTableRow($garage_row, $font_color_row);
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Training.Trainings_credits') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
