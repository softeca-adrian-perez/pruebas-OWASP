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
    array('label' => strtoupper(__t('Contact.BDM')), 'width' => '30'),
    array('label' => strtoupper(__t('Statistics.Number_of_garage_visits')), 'width' => '30'),
    array('label' => strtoupper(__t('Statistics.Number_of_distributor_visits')), 'width' => '30'),
    array('label' => strtoupper(__t('Statistics.Number_of_prospect_garage_visits')), 'width' => '30'),
    array('label' => strtoupper(__t('Statistics.Total_duration_garage_visits')), 'width' => '30'),
    array('label' => strtoupper(__t('Statistics.Total_duration_distributor_visits')), 'width' => '30'),
    array('label' => strtoupper(__t('Statistics.Average_duration_garage_visits')), 'width' => '30'),
    array('label' => strtoupper(__t('Statistics.Average_duration_distributor_visits')), 'width' => '30'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);


//////////////////////// FILL PAGES ////////////////////////

$cont_row = 2;


//////////////////////// GARAGE - PAGE ////////////////////////
$this->PhpExcel->irAHoja($n_hojas[0]);

foreach ($bdms as $bdm) {
    $this->PhpExcel->row = $cont_row;
    $connection_row = array();
    if (($bdm['GarageVisits'] + $bdm['DistributorVisits'] + $bdm['EventVisits']) > 0) {
        $connection_row[] = h($bdm['name']);
        $connection_row[] = $bdm['GarageVisits'] ? $bdm['GarageVisits'] : '';
        $connection_row[] = $bdm['DistributorVisits'] ? $bdm['DistributorVisits'] : '';
        $connection_row[] = $bdm['EventVisits'] ? $bdm['EventVisits'] : '';
        if ($bdm['DurationGarageVisits']) {
            $horas = intval($bdm['DurationGarageVisits'] / 3600);
            $minutos = intval(($bdm['DurationGarageVisits'] % 3600) / 60);
            $segundos = (($bdm['DurationGarageVisits'] % 3600)) % 60;
            $connection_row[] = $horas . 'h ' . $minutos . 'm ' . $segundos . 's';
        } else {
            $connection_row[] = '';
        }

        if ($bdm['DurationDistributorVisits']) {
            $horas = intval($bdm['DurationDistributorVisits'] / 3600);
            $minutos = intval(($bdm['DurationDistributorVisits'] % 3600) / 60);
            $segundos = (($bdm['DurationDistributorVisits'] % 3600)) % 60;
            $connection_row[] = $horas . 'h ' . $minutos . 'm ' . $segundos . 's';
        } else {
            $connection_row[] = '';
        }

        if ($bdm['AverageDurationGarageVisits']) {
            $horas = intval($bdm['AverageDurationGarageVisits'] / 3600);
            $minutos = intval(($bdm['AverageDurationGarageVisits'] % 3600) / 60);
            $segundos = (($bdm['AverageDurationGarageVisits'] % 3600)) % 60;
            $connection_row[] = $horas . 'h ' . $minutos . 'm ' . $segundos . 's';
        } else {
            $connection_row[] = '';
        }

        if ($bdm['AverageDurationDistributorVisits']) {
            $horas = intval($bdm['AverageDurationDistributorVisits'] / 3600);
            $minutos = intval(($bdm['AverageDurationDistributorVisits'] % 3600) / 60);
            $segundos = (($bdm['AverageDurationDistributorVisits'] % 3600)) % 60;
            $connection_row[] = $horas . 'h ' . $minutos . 'm ' . $segundos . 's';
        } else {
            $connection_row[] = '';
        }

        $this->PhpExcel->addTableRow($connection_row, $font_color_row);
        $cont_row++;
    }
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Contact.Bdm') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
