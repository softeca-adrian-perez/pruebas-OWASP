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
$this->PhpExcel->cambiarTituloHoja(__t('Statistics.Connections'));
if ($config[ConstantsConfig::STATISTICS_IP]) {
    $table = array(
        array('label' => strtoupper(__t('UserStatistic.Ip')), 'width' => '20'),
    );
} else {
    $table = array(
        array('label' => strtoupper(__t('User.Name')), 'width' => '20'),
        array('label' => strtoupper(__t('User.Surname')), 'width' => '20'),
    );
}

$table_tmp = array(
    array('label' => strtoupper(__t('Contact.Position')), 'width' => '20'),
    array('label' => strtoupper(__t('Distributor.Profile')), 'width' => '20'),
    array('label' => strtoupper(__t('General.Client_code')), 'width' => '20'),
    array('label' => strtoupper(__t('Garage.Company_name')), 'width' => '20'),
    array('label' => strtoupper(__t('Distributor.Distributors')), 'width' => '20'),
    array('label' => strtoupper(__t('Garage.Postcode')), 'width' => '20'),
    array('label' => strtoupper(__t('Visit.City')), 'width' => '20'),
    array('label' => strtoupper(__t('Contact.Phone')), 'width' => '20'),
    array('label' => strtoupper(__t('Contact.Email')), 'width' => '20'),
    array('label' => strtoupper(__t('Network.Networks')), 'width' => '20'),
    array('label' => strtoupper(__t('TradingGroup.Trading_groups')), 'width' => '20'),
    array('label' => strtoupper(__t('UserStatistic.Last_connection')), 'width' => '20')
);
$table = array_merge($table, $table_tmp);

foreach ($months as $month) {
    $table_tmp = array(
        array('label' => $month, 'width' => '5'),
    );
    $table = array_merge($table, $table_tmp);
}

$table_tmp = array(
    array('label' => __t('General.Total'), 'width' => '5'),

);
$table = array_merge($table, $table_tmp);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);


//////////////////////// FILL PAGES ////////////////////////

$cont_row_garages = 2;

foreach ($users_connections as $user_connection) {

    //////////////////////// GARAGE - PAGE ////////////////////////
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $this->PhpExcel->row = $cont_row_garages;

    $this->province = ClassRegistry::init('Province');

    $connection_row = array();
    if ($config[ConstantsConfig::STATISTICS_IP]) {
        $connection_row[] = $user_connection['UserStatistic']['ip'];
    } else {
        if (!empty($user_connection['User']['name'])) {
            $connection_row[] = $user_connection['User']['name'];
            $connection_row[] = $user_connection['User']['surname'];
        } else {
            $connection_row[] = $user_connection['UserStatistic']['user_name'];
            $connection_row[] = '';
            $this->PhpExcel->changeCellColorAndBackground('ADADAD', 'FFFFFF', $this->PhpExcel->getActualRowNumber(), 0);
        }
    }

    $connection_row[] = $user_connection['position'];
    $connection_row[] = $user_connection['profile'];
    $connection_row[] = $user_connection['client_code'];
    $connection_row[] = $user_connection['company_name'];
    $connection_row[] = $user_connection['distributors'];
    $connection_row[] = $user_connection['postcode'];
    $connection_row[] = $user_connection['city'];
    $connection_row[] = $user_connection['phone'];
    $connection_row[] = $user_connection['email'];
    $connection_row[] = $user_connection['networks'];
    $connection_row[] = $user_connection['trading_groups'];
    $connection_row[] = $user_connection['most_recent'];

    foreach ($months as $key_month => $month) {
        $connection_row[] = $user_connection[$key_month];
    }

    $connection_row[] = $user_connection['total'];

    $this->PhpExcel->addTableRow($connection_row, $font_color_row);
    $cont_row_garages++;
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Statistics.Last_connection_date') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
