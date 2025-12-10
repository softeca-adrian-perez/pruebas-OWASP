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

//////////////////////// DISTRIBUTOR - PAGE ////////////////////////

$this->PhpExcel->irAHoja(0);
$this->PhpExcel->row = 1;
$this->PhpExcel->cambiarTituloHoja(__t('Appointment.Visit_objectives'));



$table = array(
	array('label' => __t('AppointmentObjective.Visit_date'), 'width' => '20'),
	array('label' => __t('AppointmentObjective.BDM'), 'width' => '40'),
	array('label' => __t('AppointmentObjective.Objective'), 'width' => '40'),
	array('label' => __t('AppointmentObjective.Status'), 'width' => '20'),
	array('label' => __t('AppointmentObjective.Post_visit_comment'), 'width' => '50'),
	array('label' => __t('AppointmentObjective.Distributor'), 'width' => '25'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
	array(
		0,
	)
);

//////////////////////// FILL PAGES ////////////////////////

$cont_row_distributors = 2;

foreach ($objectives as $objective) {

	//////////////////////// DISTRIBUTOR - PAGE ////////////////////////
	$this->PhpExcel->irAHoja($n_hojas[0]);
	$this->PhpExcel->row = $cont_row_distributors;


	$objective_row = array();
	$objective_row[] = Fecha::toFormatoVistaFecha($objective['Appointment']['date']);
	$objective_row[] = $objective['User']['name'] . ' ' . $objective['User']['surname'];
	$objective_row[] = isset($management_objectives[$objective['AppointmentObjectiveComment']['objective_id']]) ? $management_objectives[$objective['AppointmentObjectiveComment']['objective_id']] : '';
	$objective_row[] = $objectives_status[$objective['AppointmentObjectiveComment']['status']];
	$objective_row[] = $objective['AppointmentObjectiveComment']['comment'];
	$objective_row[] = $objective['Distributor']['name'];

	$this->PhpExcel->addTableRow($objective_row, $font_color_row);
	$cont_row_distributors++;
}



$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Appointment.Visit_objectives') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
