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
$this->PhpExcel->cambiarTituloHoja(__t('Statistics.Devices'));

$table = array(
    array('label' => '', 'width' => '20'),
    array('label' => strtoupper(__t('Statistics.Desktop')), 'width' => '20'),
    array('label' => strtoupper(__t('Statistics.Mobile')), 'width' => '20'),
    array('label' => strtoupper(__t('Statistics.Tablet')), 'width' => '20'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);


//////////////////////// FILL PAGES ////////////////////////

$cont_row_garages = 2;


//////////////////////// GARAGE - PAGE ////////////////////////
$this->PhpExcel->irAHoja($n_hojas[0]);

$total_connections_desktop = 0;
$total_connections_mobile = 0;
$total_connections_tablet = 0;

foreach ($statistics_devices as $device) {
    $this->PhpExcel->row = $cont_row_garages;
    $connection_row = array();


    $connection_row[] = $device['month'];
    $connection_row[] = $device[ConstantsDevices::DESKTOP];
    $connection_row[] = $device[ConstantsDevices::MOBILE];
    $connection_row[] = $device[ConstantsDevices::TABLET];

    $total_connections_desktop += $device[ConstantsDevices::DESKTOP];
    $total_connections_mobile += $device[ConstantsDevices::MOBILE];
    $total_connections_tablet += $device[ConstantsDevices::TABLET];

    $this->PhpExcel->addTableRow($connection_row, $font_color_row);
    $cont_row_garages++;
}

$this->PhpExcel->row = $cont_row_garages;
$connection_row = array();

$connection_row[] = __t('General.Total');
$connection_row[] = $total_connections_desktop;
$connection_row[] = $total_connections_mobile;
$connection_row[] = $total_connections_tablet;

$this->PhpExcel->addTableRow($connection_row, $font_color_row);
$cont_row_garages++;



$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Statistics.Connections_per_devices') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
