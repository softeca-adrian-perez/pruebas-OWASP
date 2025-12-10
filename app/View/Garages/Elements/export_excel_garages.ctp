<?php
$userAagRegionId = CakeSession::read('Auth.User.aag_region_id');
$userRole = CakeSession::read('Auth.User.role_id');
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
$status = Configure::read('Network_Status');

//////////////////////// GARAGE - PAGE ////////////////////////

$this->PhpExcel->irAHoja(0);
$this->PhpExcel->row = 1;
$this->PhpExcel->cambiarTituloHoja(__t('Garage.Garages'));

if ($controller == 'garages') {

    $table = array();

    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Garage.G_number')), 'width' => '15'),
        );
        $table = array_merge($table, $table_tmp);
    }

    $table_tmp = array(
        array('label' => strtoupper(__t('Training.Network_name')), 'width' => '25'),
        array('label' => strtoupper(__t('Appointment.Customer')), 'width' => '36'),
        array('label' => strtoupper(__t('Garage.Slug')), 'width' => '40')
    );
    $table = array_merge($table, $table_tmp);

    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Garage.BDM')), 'width' => '44'),
        );
        $table = array_merge($table, $table_tmp);
    }

    if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Garage.County')), 'width' => '44'),
        );
        $table = array_merge($table, $table_tmp);
    }

    $table_tmp = array(
        array('label' => strtoupper(__t('Garage.Town') . '/' . __t('Garage.City')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Address')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Postcode')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Address_2')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Address_3')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Address_4')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Sales_area')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Phone')), 'width' => '20'),
        array('label' => strtoupper(__t('Garage.Email_2')), 'width' => '40'),
    );
    $table = array_merge($table, $table_tmp);

    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Garage.Ref_code')), 'width' => '44'),
        );
        $table = array_merge($table, $table_tmp);
    }

    $table_tmp = array(
        array('label' => strtoupper(__t('Garage.Last_visit')), 'width' => '16'),
        array('label' => strtoupper(__t('Garage.Primary_contact')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Customer_email')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Member_number')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Member_name')), 'width' => '25'),
    );
    $table = array_merge($table, $table_tmp);
    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Distributor.MAMID')), 'width' => '25'),
        );
        $table = array_merge($table, $table_tmp);
    }
    $table_tmp = array(
        array('label' => strtoupper(__t('Garage.Member_address')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Member_town')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Member_postcode')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Trading_group')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Association')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Received_date')), 'width' => '25'),
        array('label' => strtoupper(__t('Equipment.Start_date')), 'width' => '25'),
        array('label' => strtoupper(__t('Network.On_hold_date')), 'width' => '25'),
        array('label' => strtoupper(__t('Activity.Left_date')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Region')), 'width' => '25'),
        array('label' => strtoupper(__t('Software.Technical_helpline')), 'width' => '25'),
        array('label' => strtoupper(__t('Software.Mam')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Painting')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Signage')), 'width' => '25'),
    );
    $table = array_merge($table, $table_tmp);

    $this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);

    $date_counter = 0;
    if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
        $date_counter++;
    }
    //change following values if append a column in the middle of them
    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $row_last_visit = $date_counter + 15;
        $this->PhpExcel->addColumnDateType(array($row_last_visit));
    } else {
        $row_last_visit = $date_counter + 12;
        $this->PhpExcel->addColumnDateType(array($row_last_visit));
    }

    //////////////////////// FILL PAGES ////////////////////////

    $cont_row_garages = 2;
    foreach ($garages as $garage) {

        //////////////////////// GARAGE - PAGE ////////////////////////
        $this->PhpExcel->irAHoja($n_hojas[0]);
        $this->PhpExcel->row = $cont_row_garages;

        $this->province = ClassRegistry::init('Province');

        $garage_row = array();

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $garage_row[] = $garage['Garage']['g_number_id'];
        }

        $garage_row[] = $garage[0]['network_concatenated_fields'];
        $garage_row[] = $garage['Garage']['name'];
        $garage_row[] = !empty(trim($garage['Garage']['slug'])) ? $garage['Garage']['slug'] : '--';

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            if (!empty($garage['BDMS'])) {
                $bdms_text = array();
                foreach ($garage['BDMS'] as $bdm) {
                    $bdms_text[] = $bdm[0]['full_name'];
                }
                $garage_row[] = implode("\n", $bdms_text);
            } else {
                $garage_row[] = '';
            }
        }

        if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
            if (isset($garage['Garage']['province_id'])) {
                $garage_row[] = $province_list[$garage['Garage']['province_id']];
            } else {
                $garage_row[] = '';
            }
        }

        $garage_row[] = $garage['Garage']['town'];
        $garage_row[] = $garage['Garage']['address1'];
        $garage_row[] = $garage['Garage']['postcode'];
        $garage_row[] = $garage['Garage']['address2'];
        $garage_row[] = $garage['Garage']['address3'];
        $garage_row[] = $garage['Garage']['address4'];
        $garage_row[] = isset($sales_area[$garage['Garage']['sales_area_id']]) ? $sales_area[$garage['Garage']['sales_area_id']] : "";
        $garage_row[] = $garage['Garage']['phone'];
        $garage_row[] = $garage['Garage']['email'];

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $garage_row[] = $garage['Garage']['ref_code'];
        }

        $garage_row[] = isset($garage['Garage']['last_visit']) ? $garage['Garage']['last_visit'] : '';
        $garage_row[] = $garage['Contact']['first_name'] . ' ' . $garage['Contact']['last_name'];
        $garage_row[] = $garage['Contact']['email'];
        $garage_row[] = $garage['Distributor']['account_number'];
        $garage_row[] = $garage['Distributor']['name'];

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $garage_row[] = $garage['Distributor']['MAMID'];
        }

        $garage_row[] = $garage['Distributor']['address1'];
        $garage_row[] = $garage['Distributor']['town'];
        $garage_row[] = $garage['Distributor']['postcode'];
        $garage_row[] = $garage['TradingGroup']['name'];
        $garage_row[] = (isset($garage['Distributor']['association_type_id']) &&
            (isset($association_list) && !empty($association_list)))
            ? $association_list[$garage['Distributor']['association_type_id']] : '';
        $garage_row[] = (!is_null($garage[0]['contract_received_date'])) ? $garage[0]['contract_received_date'] : '--';
        $garage_row[] = (!is_null($garage[0]['contract_start_date'])) ? $garage[0]['contract_start_date'] : '--';
        $garage_row[] = (!is_null($garage[0]['date_on_hold'])) ? $garage[0]['date_on_hold'] : '--';
        $garage_row[] = (!is_null($garage[0]['leaving_date'])) ? $garage[0]['leaving_date'] : '--';
        $garage_row[] = $garage['AagRegion']['name'];
        $garage_row[] = ($garage[0]['TECHNICAL_HELPLINE_6']) ? __t('General.Yes') : __t('General.No');
        $garage_row[] = ($garage[0]['MAM_7']) ? __t('General.Yes') : __t('General.No');
        $garage_row[] = ($garage[0]['PAINTING_1']) ? __t('General.Yes') : __t('General.No');
        $garage_row[] = ($garage[0]['SIGNAGE_2']) ? __t('General.Yes') : __t('General.No');
        $this->PhpExcel->addTableRow($garage_row, $font_color_row);
        $cont_row_garages++;
    }
} elseif ($controller == 'clients') {

    $table = array();

    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Garage.G_number')), 'width' => '15'),
        );
        $table = array_merge($table, $table_tmp);
    }

    $table_tmp = array(
        array('label' => strtoupper(__t('Training.Network_name')), 'width' => '25'),
        array('label' => strtoupper(__t('Appointment.Customer')), 'width' => '36'),
    );
    $table = array_merge($table, $table_tmp);

    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Garage.BDM')), 'width' => '44'),
        );
        $table = array_merge($table, $table_tmp);
    }

    if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Garage.County')), 'width' => '44'),
        );
        $table = array_merge($table, $table_tmp);
    }

    $table_tmp = array(
        array('label' => strtoupper(__t('Garage.Town')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Address')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Postcode')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Address_2')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Address_3')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Address_4')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Sales_area')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Phone')), 'width' => '20'),
        array('label' => strtoupper(__t('Garage.Email_2')), 'width' => '40'),
    );
    $table = array_merge($table, $table_tmp);

    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Garage.Ref_code')), 'width' => '44'),
        );
        $table = array_merge($table, $table_tmp);
    }

    $table_tmp = array(
        array('label' => strtoupper(__t('Garage.Last_visit')), 'width' => '16'),
        array('label' => strtoupper(__t('Garage.Primary_contact')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Member_number')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Member_name')), 'width' => '25'),
    );
    $table = array_merge($table, $table_tmp);

    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Distributor.MAMID')), 'width' => '25'),
        );
        $table = array_merge($table, $table_tmp);
    }

    $table_tmp = array(
        array('label' => strtoupper(__t('Garage.Member_address')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Member_postcode')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Trading_group')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Association')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Received_date')), 'width' => '25'),
        array('label' => strtoupper(__t('Equipment.Start_date')), 'width' => '25'),
        array('label' => strtoupper(__t('Network.On_hold_date')), 'width' => '25'),
        array('label' => strtoupper(__t('Activity.Left_date')), 'width' => '25'),
        array('label' => strtoupper(__t('CRM.Remaining_days')), 'width' => '16'),
        array('label' => strtoupper(__t('Software.Technical_helpline')), 'width' => '25'),
        array('label' => strtoupper(__t('Software.Mam')), 'width' => '25'),
        array('label' => strtoupper(__t('Distributor.Painting')), 'width' => '25'),
        array('label' => strtoupper(__t('Garage.Signage')), 'width' => '25'),
    );
    $table = array_merge($table, $table_tmp);

    $this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);

    $date_counter = 0;
    if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
        $date_counter++;
    }
    //change following values if append a column in the middle of them
    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $row_last_visit = $date_counter + 14;
        $this->PhpExcel->addColumnDateType(array($row_last_visit));
    } else {
        $row_last_visit = $date_counter + 11;
        $this->PhpExcel->addColumnDateType(array($row_last_visit));
    }

    //////////////////////// FILL PAGES ////////////////////////

    $cont_row_garages = 2;
    foreach ($garages as $garage) {

        //////////////////////// GARAGE - PAGE ////////////////////////
        $this->PhpExcel->irAHoja($n_hojas[0]);
        $this->PhpExcel->row = $cont_row_garages;

        $this->province = ClassRegistry::init('Province');

        $garage_row = array();

        if (isset($garage['Garage']['last_visit']) && !empty($garage['Garage']['last_visit'])) {
            $interval = strtotime(date('Y-m-d')) - strtotime($garage['Garage']['last_visit']);
            $garage['Garage']['LatestVisit'] = round($interval / 86400, 0); //86400 seconds you have one day
        } else {
            $garage['Garage']['LatestVisit'] = '--';
        }

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $garage_row[] = $garage['Garage']['g_number_id'];
        }

        $garage_row[] = $garage[0]['network_concatenated_fields'];
        $garage_row[] = $garage['Garage']['name'];

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            if (!empty($garage['BDMS'])) {
                $bdms_text = array();
                foreach ($garage['BDMS'] as $bdm) {
                    $bdms_text[] = $bdm[0]['full_name'];
                }
                $garage_row[] = implode("\n", $bdms_text);
            } else {
                $garage_row[] = '';
            }
        }

        if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
            if (isset($garage['Garage']['province_id'])) {
                $garage_row[] = $province_list[$garage['Garage']['province_id']];
            } else {
                $garage_row[] = '';
            }
        }

        $garage_row[] = $garage['Garage']['town'];
        $garage_row[] = $garage['Garage']['address1'];
        $garage_row[] = $garage['Garage']['postcode'];
        $garage_row[] = $garage['Garage']['address2'];
        $garage_row[] = $garage['Garage']['address3'];
        $garage_row[] = $garage['Garage']['address4'];
        $garage_row[] = isset($sales_area[$garage['Garage']['sales_area_id']]) ? $sales_area[$garage['Garage']['sales_area_id']] : "";
        $garage_row[] = $garage['Garage']['phone'];
        $garage_row[] = $garage['Garage']['email'];

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $garage_row[] = $garage['Garage']['ref_code'];
        }

        $garage_row[] = isset($garage['Garage']['last_visit']) ? $garage['Garage']['last_visit'] : '';
        $garage_row[] = $garage['Contact']['first_name'] . ' ' . $garage['Contact']['last_name'];
        $garage_row[] = $garage['Distributor']['account_number'];
        $garage_row[] = $garage['Distributor']['name'];

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $garage_row[] = $garage['Distributor']['MAMID'];
        }

        $garage_row[] = $garage['Distributor']['address1'];
        $garage_row[] = $garage['Distributor']['postcode'];
        $garage_row[] = $garage['TradingGroup']['name'];
        $garage_row[] = (isset($garage['Distributor']['association_type_id']) &&
            (isset($association_list) && !empty($association_list)))
            ? $association_list[$garage['Distributor']['association_type_id']] : '';
        $garage_row[] = (!is_null($garage[0]['contract_received_date'])) ? $garage[0]['contract_received_date'] : '--';
        $garage_row[] = (!is_null($garage[0]['contract_start_date'])) ? $garage[0]['contract_start_date'] : '--';
        $garage_row[] = (!is_null($garage[0]['date_on_hold'])) ? $garage[0]['date_on_hold'] : '--';
        $garage_row[] = (!is_null($garage[0]['leaving_date'])) ? $garage[0]['leaving_date'] : '--';
        $garage_row[] = $garage['Garage']['LatestVisit'] . ' days';
        $garage_row[] = ($garage[0]['TECHNICAL_HELPLINE_6']) ? __t('General.Yes') : __t('General.No');
        $garage_row[] = ($garage[0]['MAM_7']) ? __t('General.Yes') : __t('General.No');
        $garage_row[] = ($garage[0]['PAINTING_1']) ? __t('General.Yes') : __t('General.No');
        $garage_row[] = ($garage[0]['SIGNAGE_2']) ? __t('General.Yes') : __t('General.No');
        $this->PhpExcel->addTableRow($garage_row, $font_color_row);
        $cont_row_garages++;
    }
} elseif ($controller == 'visits') {
    $table = array();

    if ($userAagRegionId == ConstantsAAGRegionId::UK) {
        $table_tmp = array(
            array('label' => strtoupper(__t('Garage.G_number')), 'width' => '15'),
        );
        $table = array_merge($table, $table_tmp);
    }

    $table_tmp = array(
        array('label' => strtoupper(__t('Appointment.Customer')), 'width' => '36'),
        array('label' => strtoupper(__t('CRM.Postcode')), 'width' => '22'),
        array('label' => strtoupper(__t('Visit.City')), 'width' => '22'),
        array('label' => strtoupper(__t('CRM.Last_visit')), 'width' => '15'),
    );
    $table = array_merge($table, $table_tmp);

    $this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
    //////////////////////// FILL PAGES ////////////////////////

    $cont_row_garages = 2;

    foreach ($garages as $garage) {
        $this->PhpExcel->irAHoja($n_hojas[0]);
        $this->PhpExcel->row = $cont_row_garages;

        $garage_row = array();

        if ($userAagRegionId == ConstantsAAGRegionId::UK) {
            $garage_row[] = $garage['Garage']['g_number_id'];
        }

        $garage_row[] = $garage['Garage']['business_name'];
        $garage_row[] = $garage['Garage']['postcode'];
        $garage_row[] = $garage['Garage']['town'];
        $garage_row[] = (isset($garage['LatestVisit'])) ? Fecha::toDateViewFormatSlash($garage['LatestVisit']) : '--';

        $this->PhpExcel->addTableRow($garage_row, $font_color_row);
        $cont_row_garages++;
    }
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Garage.Garages') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
