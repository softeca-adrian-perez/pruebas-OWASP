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
$this->PhpExcel->cambiarTituloHoja(__t('Booking.Bookings'));

$table = array(
    array('label' => __t('General.Customer_name'), 'width' => '36'),
    array('label' => __t('General.Customer_phone'), 'width' => '36'),
    array('label' => __t('Email.Email'), 'width' => '36'),
    array('label' => __t('General.Date'), 'width' => '36'),
    array('label' => __t('General.Time'), 'width' => '36'),
    array('label' => __t('General.Time_to'), 'width' => '36'),
	array('label' => __t('General.Quotation_id'), 'width' => '36'),
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
	array('label' => __t('General.Creation_date'), 'width' => '36'),
);

if (isset($isNetwork) && $isNetwork) {
	array_unshift($table, array('label' => __t('Garage.Garage'), 'width' => '36'));
}

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
    array(
        3,
    )
);

//////////////////////// FILL PAGES ////////////////////////

foreach ($bookingsGarage as $booking) {

    //////////////////////// GARAGE - PAGE ////////////////////////
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $booking_row = array();

	if (isset($isNetwork) && $isNetwork) {
		$booking_row[] = $booking['Garage']['name'];
	}

    $booking_row[] = Texto::encryptDecryptText($booking['Booking']['customer_name'], false);
    $booking_row[] = Texto::encryptDecryptText($booking['Booking']['customer_phone'], false);
    $booking_row[] = Texto::encryptDecryptText($booking['Booking']['customer_email'], false);
    $booking_row[] = Fecha::toFormatoVista($booking['Booking']['date']);
    $booking_row[] = date("H:i", strtotime($booking['Booking']['time']));
    $booking_row[] = date("H:i", strtotime($booking['Booking']['time_to']));
    $booking_row[] = $booking['Booking']['quotation_id'];
	$booking_row[] = Texto::encryptDecryptText($booking['Booking']['plate'], false);
	$booking_row[] = Texto::encryptDecryptText($booking['Booking']['vin'], false);
	$booking_row[] = $booking['Booking']['brand'] ?? '';
	$booking_row[] = $booking['Booking']['model'] ?? '';
	$booking_row[] = $booking['Booking']['version'] ?? '';
	$booking_row[] = $booking['Booking']['mot_exp_date'] ?? '';
	$booking_row[] = $booking['Booking']['fuel'] ?? '';
	$booking_row[] = $booking['Booking']['registered_on'] ?? '';
	$booking_row[] = $booking['Booking']['mileage'] ?? '';
	$booking_row[] = $booking['Booking']['work_name'] ?? '';
	$booking_row[] = Fecha::toFormatoVista($booking['Booking']['creation_date']) ?? '';

    $this->PhpExcel->addTableRow($booking_row, $font_color_row);
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Booking.Bookings') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
