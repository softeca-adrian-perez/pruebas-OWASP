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
$this->PhpExcel->cambiarTituloHoja(__t('Statistics.Distributors'));

$table = array(
    array('label' => strtoupper(__t('Distributor.Account_number')), 'width' => '10'),
    array('label' => strtoupper(__t('Appointment.Customer')), 'width' => '45'),
    array('label' => strtoupper(__t('Contact.BDM')), 'width' => '45'),
    array('label' => strtoupper(__t('Distributor.Postcode')), 'width' => '10'),
    array('label' => strtoupper(__t('Distributor.Town')), 'width' => '20'),
    array('label' => strtoupper(__t('Distributor.Phone')), 'width' => '20'),
    array('label' => strtoupper(__t('Visit.Visits')), 'width' => '10'),
    array('label' => strtoupper(__t('Visit.Last_visit')), 'width' => '10'),
    array('label' => strtoupper(__t('Contract.Start_date')), 'width' => '10'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
    array(
        7,
        8,
    )
);

//////////////////////// FILL PAGES ////////////////////////

$cont_row = 2;


//////////////////////// GARAGE - PAGE ////////////////////////
$this->PhpExcel->irAHoja($n_hojas[0]);

foreach ($distributors as $distributor) {

    $this->PhpExcel->row = $cont_row;
    $connection_row = array();
    $connection_row[] = h($distributor['account_number']);
    $connection_row[] = $distributor['name'];
    $bdms = '';
    if ($distributor['bdms']) {
        foreach ($distributor['bdms'] as $bdm) {
            if (isset($contacts[$bdm])) {
                $bdms = $bdms . $contacts[$bdm];
                if ($bdm !== end($distributor['bdms'])) {
                    $bdms = $bdms . " - ";
                }
            }
        }
    }
    $connection_row[] = $bdms;
    $connection_row[] = h($distributor['postcode']);
    $connection_row[] = h($distributor['town']);
    $connection_row[] = h($distributor['phone']);
    $connection_row[] = h($distributor['count']);
    $connection_row[] = Fecha::toFormatoVista($distributor['last_visit']);
    $connection_row[] = Fecha::toFormatoVista($distributor['contract_start']);

    $this->PhpExcel->addTableRow($connection_row, $font_color_row);
    $cont_row++;
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Statistics.Distributors') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
