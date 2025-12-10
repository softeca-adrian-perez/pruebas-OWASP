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

//////////////////////// CONFERENCE - PAGE ////////////////////////

$this->PhpExcel->irAHoja(0);
$this->PhpExcel->row = 1;
$this->PhpExcel->cambiarTituloHoja(__t('Training.Trainings_credits'));

$table = array(
    array('label' => strtoupper(__t('Training.Garage_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Network')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Changed_date')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Credits')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Description')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Course_date')), 'width' => '36'),
    array('label' => strtoupper(__t('Delegate.Delegate_name')), 'width' => '36'),
    array('label' => strtoupper(__t('CRM.Cancelled')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Purchase_order_number')), 'width' => '36'),
    array('label' => strtoupper(__t('Allowance.Actual_allowance')), 'width' => '36'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
    array(
        2,
        5,
    )
);
$this->PhpExcel->addColumnDateFormat(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY_TIME);

//////////////////////// FILL PAGES ////////////////////////

foreach ($credits_movements as $credit) {

    //////////////////////// GARAGE - PAGE ////////////////////////
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $credit_row = array();

    $credit_row[] = $credit['Garage']['name'];
    $credit_row[] = $network_list[$credit['GarageNetwork']['network_id']];
    $credit_row[] = $credit['TrainingCreditMovement']['creation_date'];

    $calculationGivenResult = $credit['TrainingCreditMovement']['credit_spent'] + $credit['TrainingCreditMovement']['credit_given'];
    if ($credit['TrainingCreditMovement']['description'] == ConstantsDescriptionCredits::REFUNDED_CREDITS) {

        $credit_row[]  = '+' . $calculationGivenResult;
    } elseif ($credit['TrainingCreditMovement']['credit_given'] != null && $calculationGivenResult >= 0) {

        $credit_row[]  = '+' . $calculationGivenResult;
    } elseif ($credit['TrainingCreditMovement']['credit_spent'] != null) {

        $credit_row[]  = '-' . $calculationGivenResult;
    } elseif ($credit['TrainingCreditMovement']['credit_given'] == null && $credit['TrainingCreditMovement']['credit_spent'] == null) {

        $credit_row[]  = '+' . $calculationGivenResult;
    } else {
        $credit_row[] = $calculationGivenResult;
    }

    if ($credit['TrainingCreditMovement']['description'] == ConstantsDescriptionCredits::EXTRA_GIVEN || $credit['TrainingCreditMovement']['description'] == ConstantsDescriptionCredits::YEARLY_RENEW || $credit['TrainingCreditMovement']['description'] == ConstantsDescriptionCredits::REFUNDED_CREDITS) {
        if (isset($credit['TrainingCreditMovement']['reason_allowance_id'])) {
            $credit_row[] = $credit['TrainingCreditMovement']['description'] . ' - ' . $reasons_allowance_list[$credit['TrainingCreditMovement']['reason_allowance_id']];
        } else {
            $credit_row[] = $credit['TrainingCreditMovement']['description'];
        }
    } else {
        $credit_row[] = $credit['TrainingCreditMovement']['description'];
    }

    $credit_row[] = Fecha::toFormatoVistaFecha($credit['TrainingPlannedCourse']['date_from']);

    if (isset($credit['ContactDelegate'])) {
        $credit_row[] = $credit['ContactDelegate']['first_name'] . ' ' .  $credit['ContactDelegate']['last_name'];
    } else {
        $credit_row[] = '';
    }

    $credit_row[] = $credit['TrainingDelegate']['cancelled'] == ConstantsBooleans::YES ? __t('General.Yes') : __t('General.No');
    $credit_row[] = $credit['TrainingDelegate']['order_number'];

    $credit_row[] = $credit['TrainingAllowance']['is_actual'] == ConstantsBooleans::YES ? __t('General.Yes') : '';

    $this->PhpExcel->addTableRow($credit_row, $font_color_row);
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Training.Credits_movements') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
