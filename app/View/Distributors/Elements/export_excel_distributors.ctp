<?php
$userAagRegionId = CakeSession::read('Auth.User.aag_region_id');
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

//////////////////////// DISTRIBUTOR - PAGE ////////////////////////

$this->PhpExcel->irAHoja(0);
$this->PhpExcel->row = 1;
$this->PhpExcel->cambiarTituloHoja(__t('Distributor.Distributors'));

if ($controller == 'distributors') {

    $table = array();
    $table_tmp = array(
        array('label' => strtoupper(__t('Distributor.Account_number')), 'width' => '20'),
        array('label' => strtoupper(__t('Appointment.Customer')), 'width' => '40'),
    );
    $table = array_merge($table, $table_tmp);

    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Distributor.MAMID')), 'width' => '40'),
        );
        $table = array_merge($table, $table_tmp);
    }

    $table_tmp = array(
        array('label' => strtoupper(__t('Distributor.Head_office')), 'width' => '40'),
        array('label' => strtoupper(__t('Distributor.Trading_group')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Association')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Postcode')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Town')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Sales_area')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Phone')), 'width' => '25'),
    );
    $table = array_merge($table, $table_tmp);

    $table_tmp = array(
        array('label' => strtoupper(__t('CRM.Last_visit')), 'width' => '25'),
    );
    $table = array_merge($table, $table_tmp);


    $this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
    $this->PhpExcel->addColumnDateType(
        array(
            array_search(array('label' => strtoupper(__t('CRM.Last_visit')), 'width' => '25'), $table),
        )
    );

    //////////////////////// FILL PAGES ////////////////////////

    $cont_row_distributors = 2;
    foreach ($distributors as $distributor) {

        //////////////////////// DISTRIBUTOR - PAGE ////////////////////////
        $this->PhpExcel->irAHoja($n_hojas[0]);
        $this->PhpExcel->row = $cont_row_distributors;


        $distributor_row = array();
        $distributor_row[] = $distributor['Distributor']['account_number'];
        $distributor_row[] = $distributor['Distributor']['name'];
        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $distributor_row[] = $distributor['Distributor']['MAMID'];
        }

        $distributor_row[] = Booleano::toString($distributor['Distributor']['head_office']);
        $distributor_row[] = $trading_groups[$distributor['Distributor']['trading_group_id']];
        $distributor_row[] = isset($distributor['Distributor']['association_type_id']) ? $associations[$distributor['Distributor']['association_type_id']] : '';
        $distributor_row[] = $distributor['Distributor']['postcode'];
        $distributor_row[] = $distributor['Distributor']['town'];
        $distributor_row[] = isset($distributor['Distributor']['sales_area_id']) ? $sales_area[$distributor['Distributor']['sales_area_id']] : '';
        $distributor_row[] = $distributor['Distributor']['phone'];

        $distributor_row[] = Fecha::toFormatoVistaFecha($distributor['Distributor']['last_visit']);

        $this->PhpExcel->addTableRow($distributor_row, $font_color_row);
        $cont_row_distributors++;
    }
} else if ($controller == 'clients') {

    $table = array();
    $table_tmp = array(
        array('label' => strtoupper(__t('Distributor.Account_number')), 'width' => '20'),
        array('label' => strtoupper(__t('Appointment.Customer')), 'width' => '40'),
    );
    $table = array_merge($table, $table_tmp);

    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Distributor.MAMID')), 'width' => '40'),
        );
        $table = array_merge($table, $table_tmp);
    }

    $table_tmp = array(
        array('label' => strtoupper(__t('Distributor.Head_office')), 'width' => '40'),
        array('label' => strtoupper(__t('Distributor.Trading_group')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Association')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Postcode')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Town')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Sales_area')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Phone')), 'width' => '25'),
    );
    $table = array_merge($table, $table_tmp);

    $table_tmp = array(
        array('label' => strtoupper(__t('CRM.Last_visit')), 'width' => '25'),
    );
    $table = array_merge($table, $table_tmp);


    $this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);

    //////////////////////// FILL PAGES ////////////////////////

    $cont_row_distributors = 2;

    foreach ($distributors as $distributor) {

        //////////////////////// DISTRIBUTOR - PAGE ////////////////////////
        $this->PhpExcel->irAHoja($n_hojas[0]);
        $this->PhpExcel->row = $cont_row_distributors;


        $distributor_row = array();
        $distributor_row[] = $distributor['Distributor']['account_number'];
        $distributor_row[] = $distributor['Distributor']['name'];
        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $distributor_row[] = $distributor['Distributor']['MAMID'];
        }

        $distributor_row[] = Booleano::toString($distributor['Distributor']['head_office']);
        $distributor_row[] = $trading_groups[$distributor['Distributor']['trading_group_id']];
        $distributor_row[] = isset($distributor['Distributor']['association_type_id']) ? $associations[$distributor['Distributor']['association_type_id']] : '';
        $distributor_row[] = $distributor['Distributor']['postcode'];
        $distributor_row[] = $distributor['Distributor']['town'];
        $distributor_row[] = isset($distributor['Distributor']['sales_area_id']) ? $sales_area[$distributor['Distributor']['sales_area_id']] : '';
        $distributor_row[] = $distributor['Distributor']['phone'];

        $distributor_row[] = Fecha::toFormatoVistaFecha($distributor['Distributor']['last_visit']);

        $this->PhpExcel->addTableRow($distributor_row, $font_color_row);
        $cont_row_distributors++;
    }
} else if ($controller == 'visits') {

    $table = array(
        // PROFILE
        array('label' => strtoupper(__t('Distributor.Account_number')), 'width' => '15'),
        array('label' => strtoupper(__t('Appointment.Customer')), 'width' => '36'),
        array('label' => strtoupper(__t('CRM.Postcode')), 'width' => '22'),
        array('label' => strtoupper(__t('Visit.City')), 'width' => '22'),
        array('label' => strtoupper(__t('CRM.Last_visit')), 'width' => '15'),
        array('label' => strtoupper(__t('Distributor.Association')), 'width' => '25'),
    );

    $this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);

    //////////////////////// FILL PAGES ////////////////////////

    $cont_row_distributors = 2;

    foreach ($distributors as $distributor) {

        //////////////////////// DISTRIBUTOR - PAGE ////////////////////////
        $this->PhpExcel->irAHoja($n_hojas[0]);
        $this->PhpExcel->row = $cont_row_distributors;

        $distributor_row = array();
        $distributor_row[] = $distributor['Distributor']['account_number'];
        $distributor_row[] = $distributor['Distributor']['name'];
        $distributor_row[] = $distributor['Distributor']['postcode'];
        $distributor_row[] = $distributor['Distributor']['town'];
        $distributor_row[] = (isset($distributor['LatestVisit'])) ? Fecha::toFormatoVistaFecha($distributor['LatestVisit']) : '--';

        $this->PhpExcel->addTableRow($distributor_row, $font_color_row);
        $cont_row_distributors++;
    }
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Distributor.Distributors') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
