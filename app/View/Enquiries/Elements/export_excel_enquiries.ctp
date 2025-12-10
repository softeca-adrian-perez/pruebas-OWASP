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

//////////////////////// CONFERENCE - PAGE ////////////////////////

$this->PhpExcel->irAHoja(0);
$this->PhpExcel->row = 1;
$this->PhpExcel->cambiarTituloHoja(__t('Enquiry.Enquiries'));

$table = array(
	array('label' => __t('Network.Network'), 'width' => '36'),
    array('label' => __t('General.Name'), 'width' => '36'),
    array('label' => __t('General.Description'), 'width' => '36'),
    array('label' => __t('Enquiry.Answered'), 'width' => '36'),
    array('label' => __t('General.Date'), 'width' => '36'),
	array('label' => __t('General.Plate'), 'width' => '36'),
	array('label' => __t('General.Vin'), 'width' => '36'),
	array('label' => __t('General.Brand'), 'width' => '36'),
	array('label' => __t('General.Model'), 'width' => '36'),
	array('label' => __t('General.Version'), 'width' => '36'),
	array('label' => __t('General.Mot_due_on'), 'width' => '36'),
	array('label' => __t('General.Fuel'), 'width' => '36'),
	array('label' => __t('General.Registered_on'), 'width' => '36'),
	array('label' => __t('General.Mileage'), 'width' => '36'),
	array('label' => __t('General.Work'), 'width' => '36'),
);

if (isset($isNetwork) && $isNetwork) {
	array_unshift($table, array('label' => __t('Garage.Garage'), 'width' => '36'));
}

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
	array(
		4,
	)
);
$this->PhpExcel->addColumnDateFormat(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY_TIME);
//////////////////////// FILL PAGES ////////////////////////

foreach ($enquiriesGarage as $enquiry) {

    //////////////////////// GARAGE - PAGE ////////////////////////
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $enquiry_row = array();

	if (isset($isNetwork) && $isNetwork) {
		$enquiry_row[] = $enquiry['Garage']['name'];
	}

	$enquiry_row[] = $enquiry['Enquiry']['network_name'];
    $enquiry_row[] = $enquiry['Enquiry']['name'] ? Texto::encryptDecryptText($enquiry['Enquiry']['name'], false) : '';
    $enquiry_row[] = Texto::is_utf8($enquiry['Enquiry']['description']) ? $enquiry['Enquiry']['description'] : 'Invalid Character';
    $enquiry_row[] = $enquiry['Enquiry']['answered'] ? __t('General.Yes') : __t('General.No');
    $enquiry_row[] = Fecha::toFormatoVistaFechaHora($enquiry['Enquiry']['creation_date']);
	$enquiry_row[] = $enquiry['Enquiry']['plate'] ? Texto::encryptDecryptText($enquiry['Enquiry']['plate'], false) : '';
	$enquiry_row[] = $enquiry['Enquiry']['vin'] ? Texto::encryptDecryptText($enquiry['Enquiry']['vin'], false) : '';
	$enquiry_row[] = $enquiry['Enquiry']['brand'] ?? '';
	$enquiry_row[] = $enquiry['Enquiry']['model'] ?? '';
	$enquiry_row[] = $enquiry['Enquiry']['version'] ?? '';
	$enquiry_row[] = $enquiry['Enquiry']['mot_exp_date'] ?? '';
	$enquiry_row[] = $enquiry['Enquiry']['fuel'] ?? '';
	$enquiry_row[] = $enquiry['Enquiry']['registered_on'] ?? '';
	$enquiry_row[] = $enquiry['Enquiry']['mileage'] ?? '';
	$enquiry_row[] = $enquiry['Enquiry']['work_name'] ?? '';

    $this->PhpExcel->addTableRow($enquiry_row, $font_color_row);
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Enquiry.Enquiries') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
