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
$userAagRegionId = CakeSession::read('Auth.User.aag_region_id');
$userRole = CakeSession::read('Auth.User.role_id');

//////////////////////// GARAGE - PAGE ////////////////////////

$this->PhpExcel->irAHoja(0);
$this->PhpExcel->row = 1;
$this->PhpExcel->cambiarTituloHoja(__t('Statistics.Garages'));

$table = array(
    array('label' => strtoupper(__t('Appointment.Customer')), 'width' => '40'),
    array('label' => strtoupper(__t('Contact.BDM')), 'width' => '40'),
    array('label' => strtoupper(__t('Garage.Town')), 'width' => '20'),
    array('label' => strtoupper(__t('Garage.Address')), 'width' => '50'),
    array('label' => strtoupper(__t('Garage.Phone')), 'width' => '15'),
    array('label' => strtoupper(__t('Garage.Ref_code')), 'width' => '10'),
    array('label' => strtoupper(__t('Visit.Visits')), 'width' => '8'),
    array('label' => strtoupper(__t('Visit.Last_visit')), 'width' => '12'),
    array('label' => strtoupper(__t('Contract.Start_date')), 'width' => '18'),
);

//If it's Benelux there is not garage g_number, but if the role is Super Admin, garages from both regions can be present at the same time
if ((isset($userAagRegionId) && ($userAagRegionId != ConstantsAAGRegionId::BENELUX)) || ($userRole == ConstantsRoles::SUPER_ADMIN)) {
    array_push(
        $table,
        array('label' => strtoupper(__t('Garage.G_number')), 'width' => '10')
    );
}

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

foreach ($garages as $garage) {
    $this->PhpExcel->row = $cont_row;
    $connection_row = array();
    $connection_row[] = $garage['name'];
    $bdms = '';
    if ($garage['bdms']) {
        foreach ($garage['bdms'] as $bdm) {
            if (isset($contacts[$bdm])) {
                $bdms = $bdms . $contacts[$bdm];
                if ($bdm !== end($garage['bdms'])) {
                    $bdms = $bdms . ' - ';
                }
            }
        }
    }
    $connection_row[] = $bdms;
    $connection_row[] = h($garage['town']);
    $connection_row[] = h($garage['address']);
    $connection_row[] = h($garage['phone']);
    $connection_row[] = h($garage['ref_code']);
    $connection_row[] = h($garage['count']);
    $connection_row[] = Fecha::toFormatoVista($garage['last_visit']);
    $connection_row[] = isset($garage['contract_start']['GarageNetwork']) ? Fecha::toFormatoVista($garage['contract_start']['GarageNetwork']['contract_start_date']) : '';

    if ((isset($userAagRegionId) && ($userAagRegionId != ConstantsAAGRegionId::BENELUX)) || ($userRole == ConstantsRoles::SUPER_ADMIN)) {
        $connection_row[] = h($garage['g_number_id']);
    }

    $this->PhpExcel->addTableRow($connection_row, $font_color_row);
    $cont_row++;
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Statistics.Garages') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
