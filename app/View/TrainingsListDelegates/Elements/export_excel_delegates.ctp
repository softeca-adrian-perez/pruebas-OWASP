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
    array('label' => strtoupper(__t('Training.Training_provider_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Planned_courses')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Date_from')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Date_to')), 'width' => '36'),
    array('label' => strtoupper(__t('Distributor.Account_number')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Venue')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Credits')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Cost_of_course')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Credits_taken')), 'width' => '36'),
    array('label' => strtoupper(__t('CRM.Cancelled')), 'width' => '36'),
    array('label' => strtoupper(__t('General.Cancelled_reason')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Purchase_order_number')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Invoice_number')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Garage_address')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Address_2')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Address_3')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Postcode')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Town')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Country')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Province')), 'width' => '36'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
    array(
        6,
        7,
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
    $delegate_row[] = $delegate['TrainingProvider']['name'];
    $delegate_row[] = $delegate['TrainingCourse']['name'];
    $delegate_row[] = Fecha::toFormatoVistaFecha(h($delegate['TrainingPlannedCourse']['date_from']));
    $delegate_row[] = Fecha::toFormatoVistaFecha(h($delegate['TrainingPlannedCourse']['date_to']));
    $delegate_row[] = $delegate['Distributor']['account_number'];
    $delegate_row[] = $delegate['Venue']['name'];
    $delegate_row[] = $delegate['TrainingCourse']['price_credit'];
    $delegate_row[] = $delegate['TrainingCourse']['cost_training'];
    $delegate_row[] = $delegate['TrainingDelegate']['is_refund_eligible'] == ConstantsBooleans::YES ? __t('General.Yes') : __t('General.No');
    $delegate_row[] = $delegate['TrainingDelegate']['cancelled'] == ConstantsBooleans::YES ? __t('General.Yes') : __t('General.No');
    $delegate_row[] = isset($delegate['TrainingDelegate']['reason_cancelled_id']) ? $reason_cancelled_list[$delegate['TrainingDelegate']['reason_cancelled_id']] : '';
    $delegate_row[] = (isset($delegate['TrainingDelegate']['order_number']) && !empty($delegate['TrainingDelegate']['order_number']) && ($delegate['TrainingDelegate']['order_number'] != 'NULL')) ? $delegate['TrainingDelegate']['order_number'] : '';
    $delegate_row[] = $delegate['TrainingDelegate']['invoice_number'];
    $delegate_row[] = $delegate['Garage']['address1'];
    $delegate_row[] = $delegate['Garage']['address2'];
    $delegate_row[] = $delegate['Garage']['address3'];
    $delegate_row[] = $delegate['Garage']['postcode'];
    $delegate_row[] = $delegate['Garage']['town'];
    $delegate_row[] = $delegate['Country']['name'];
    $delegate_row[] = $delegate['Province']['name'];

    $this->PhpExcel->addTableRow($delegate_row, $font_color_row);
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Training.List_delegates') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
