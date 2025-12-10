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
$this->PhpExcel->cambiarTituloHoja(__t('Training.List_delegates'));

$table = array(
    array('label' => strtoupper(__t('Training.Delegate_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Employment_position')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Network_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Garage_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Planned_courses')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Date_from')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Date_to')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Credits')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Credits_taken')), 'width' => '36'),
    array('label' => strtoupper(__t('CRM.Cancelled')), 'width' => '36'),
    array('label' => strtoupper(__t('General.Cancelled_reason')), 'width' => '36'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
    array(
        5,
        6,
    )
);

//////////////////////// FILL PAGES ////////////////////////

foreach ($delegates as $delegate) {

    //////////////////////// GARAGE - PAGE ////////////////////////
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $delegate_row = array();
    $delegate_row[] = $delegate['Contact']['first_name'] . ' ' . $delegate['Contact']['last_name'];
    $delegate_row[] = $delegate['Position']['name_' . __l()];
    $delegate_row[] = $delegate['Network']['name'];
    $delegate_row[] = $delegate['Garage']['name'] . ' ' . $delegate['Garage']['g_number_id'];
    $delegate_row[] = $delegate['TrainingCourse']['name'];
    $delegate_row[] = Fecha::toFormatoVistaFecha(h($delegate['TrainingPlannedCourse']['date_from']));
    $delegate_row[] = Fecha::toFormatoVistaFecha(h($delegate['TrainingPlannedCourse']['date_to']));
    $delegate_row[] = $delegate['TrainingCourse']['price_credit'];
    $delegate_row[] = $delegate['TrainingDelegate']['is_refund_eligible'] == 1 ? __t('General.Yes') : __t('General.No');
    $delegate_row[] = $delegate['TrainingDelegate']['cancelled'] == ConstantsBooleans::YES ? __t('General.Yes') : __t('General.No');
    $delegate_row[] = isset($delegate['TrainingDelegate']['reason_cancelled_id']) ? $reason_cancelled_list[$delegate['TrainingDelegate']['reason_cancelled_id']] : '';

    $this->PhpExcel->addTableRow($delegate_row, $font_color_row);
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Delegate.Delegates') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
