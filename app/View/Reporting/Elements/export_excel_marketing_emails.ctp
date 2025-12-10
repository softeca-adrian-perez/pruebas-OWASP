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

//////////////////////// MARKETING_EMAILS - PAGE ////////////////////////

$this->PhpExcel->irAHoja(0);
$this->PhpExcel->row = 1;
$this->PhpExcel->cambiarTituloHoja(__t('Garage.Marketing_emails'));

$table = array(
    array('label' => __t('General.Name'), 'width' => '22'),
    array('label' => __t('Contact.Email'),'width' => '22'),
    array('label' => __t('General.Plate'), 'width' => '22'),
    array('label' => __t('Reporting.DateAPK'), 'width' => '22'),
    array('label' => __t('Garage.City'), 'width' => '22'),
    array('label' => __t('Reporting.DateSubscription'), 'width' => '22'),
    array('label' => __t('Reporting.Job'), 'width' => '22'),
    array('label' => __t('Reporting.EnquiryBooking'), 'width' => '22'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(array(5));
$this->PhpExcel->addColumnDateFormat(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD);

//////////////////////// FILL PAGES ////////////////////////

$cont_row = 2;
foreach ($enquiriesBookings as $enquiryBooking) {
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $this->PhpExcel->row = $cont_row;

    $data = $enquiryBooking['Enquiry'] ?? $enquiryBooking['Booking'];
    $type = isset($enquiryBooking['Enquiry']) ? __t('Enquiry.Enquiry') : __t('Booking.Booking');

    $enquirie_row = array();
    $enquirie_row[] = Texto::encryptDecryptText($data['name']);
    $enquirie_row[] = Texto::encryptDecryptText($data['email']);
    $enquirie_row[] = Texto::encryptDecryptText($data['plate']);
    $enquirie_row[] = $data['mot_exp_date'];
    $enquirie_row[] = $enquiryBooking['City']['name'];
    $enquirie_row[] = $data['creation_date'];
    $enquirie_row[] = $enquiryBooking['Work']['name'];
    $enquirie_row[] = $type;

    $this->PhpExcel->addTableRow($enquirie_row, $font_color_row);
    $cont_row++;
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Garage.Marketing_emails') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
