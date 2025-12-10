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

$config = CakeSession::read('Auth.User.Config');

//////////////////////// GARAGE - PAGE ////////////////////////

$this->PhpExcel->irAHoja(0);
$this->PhpExcel->row = 1;
$this->PhpExcel->cambiarTituloHoja(__t('Contact.Bdm'));

$table = array(
    array('label' => strtoupper(__t('Appointment.Customer')), 'width' => '30'),
    array('label' => strtoupper(__t('Appointment.Date')), 'width' => '15'),
    array('label' => strtoupper(__t('Appointment.End_date')), 'width' => '15'),
    array('label' => strtoupper(__t('Appointment.Start_time')), 'width' => '15'),
    array('label' => strtoupper(__t('Appointment.End_time')), 'width' => '15'),
    array('label' => strtoupper(__t('Alert.Assigned_to')), 'width' => '30'),
    array('label' => strtoupper(__t('Appointment.Feedback_fill_up')), 'width' => '30'),
    array('label' => strtoupper(__t('Appointment.Requires_follow_up')), 'width' => '30'),
    array('label' => strtoupper(__t('Appointment.Feeling')), 'width' => '30'),
    array('label' => strtoupper(__t('Appointment.Status')), 'width' => '30'),
    array('label' => strtoupper(__t('General.Type')), 'width' => '30'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
    array(
        1,
        2,
    )
);


//////////////////////// FILL PAGES ////////////////////////

$cont_row = 2;


//////////////////////// GARAGE - PAGE ////////////////////////
$this->PhpExcel->irAHoja($n_hojas[0]);

foreach ($appointments as $appointment) {
    $this->PhpExcel->row = $cont_row;
    $connection_row = array();
    $connection_row[] = $appointment['Appointment']['garage_id'] ? h($appointment['Garage']['name']) : h($appointment['Distributor']['name']);
    $connection_row[] = Fecha::toFormatoVistaFecha($appointment['Appointment']['date']);
    $connection_row[] = Fecha::isDate($appointment['Appointment']['end_date']) ? Fecha::toFormatoVistaFecha($appointment['Appointment']['end_date']) : Fecha::toFormatoVistaFecha($appointment['Appointment']['date']);
    $connection_row[] = h($appointment['Appointment']['start_time']);
    $connection_row[] = h($appointment['Appointment']['end_time']);
    $connection_row[] = h($users[$appointment['Appointment']['user_assigned_id']]);
    $connection_row[] = $appointment['Appointment']['feedback'] != '' ? __t('General.Yes') : __t('General.No');
    $connection_row[] = $appointment['Appointment']['requires_follow_up'] ? __t('General.Yes') : __t('General.No');
    $connection_row[] = $appointment['Appointment']['appointment_feeling_id'] ? h($feelings_list[$appointment['Appointment']['appointment_feeling_id']]) : '';
    $connection_row[] = h($status[$appointment['Appointment']['appointment_status_id']]);
    $connection_row[] = $appointment['Appointment']['garage_id'] ? __t('Garage.Garage') : ($appointment['Appointment']['distributor_id'] ? __t('Distributor.Distributor') : __t('General.Prospect_garage_visit'));

    $this->PhpExcel->addTableRow($connection_row, $font_color_row);
    $cont_row++;
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Contact.Bdm') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
