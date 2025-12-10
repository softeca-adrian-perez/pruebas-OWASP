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
$this->PhpExcel->cambiarTituloHoja(__t('Quotation.Quotations'));

$table = array(
    array('label' => __t('General.Date'), 'width' => '36'),
    array('label' => __t('General.Price'), 'width' => '36'),
    array('label' => __t('General.Quotation_id'), 'width' => '36'),
    array('label' => __t('Email.Email'), 'width' => '36'),
    array('label' => __t('General.Plate'), 'width' => '36'),
    array('label' => __t('General.Vin'), 'width' => '36'),
    array('label' => __t('Brands.Brand'), 'width' => '36'),
    array('label' => __t('General.Model'), 'width' => '50'),
    array('label' => __t('General.Version'), 'width' => '50'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
    array(
        0,
    )
);

//////////////////////// FILL PAGES ////////////////////////

foreach ($quotations as $quotation) {

    //////////////////////// GARAGE - PAGE ////////////////////////
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $quotation_row = array();

    $quotation_row[] = Fecha::toFormatoVista($quotation['creation_date']);
    $price = 0;
    if (!empty($quotation['price']) && $quotation['price'] != "0.00") {
        $price =  Numero::redondear($quotation['price'], 2);
    } else {
        $price = "-";
    }
    $quotation_row[] = $price;
    $quotation_row[] = $quotation['quotation_id'];
    $quotation_row[] = $quotation['email'];
    $quotation_row[] = $quotation['vehicle_plate'];
    $quotation_row[] = $quotation['vehicle_vin'];
    $quotation_row[] = $quotation['vehicle_brand_name'];
    $quotation_row[] = $quotation['vehicle_model_name'];
    $quotation_row[] = $quotation['vehicle_version_name'];

    $this->PhpExcel->addTableRow($quotation_row, $font_color_row);
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Quotation.Quotations') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
