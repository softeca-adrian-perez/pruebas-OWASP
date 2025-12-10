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
$this->PhpExcel->cambiarTituloHoja(__t('Training.Training'));

$table = array(
    array('label' => strtoupper(__t('TrainingProvider.Name')), 'width' => '36'),
    array('label' => strtoupper(__t('TrainingProvider.Provider_email')), 'width' => '36'),
    array('label' => strtoupper(__t('TrainingProvider.Provider_phone_number')), 'width' => '36'),
    array('label' => strtoupper(__t('TrainingTrainer.name')), 'width' => '36'),
    array('label' => strtoupper(__t('TrainingTrainer.Trainer_email')), 'width' => '36'),
    array('label' => strtoupper(__t('TrainingTrainer.Trainer_phone')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Course_type')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Course_duration')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Course_price')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Course_credits')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Cost_of_course') . ' (£)'), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Part_number')), 'width' => '36'),
    array('label' => strtoupper(__t('TrainingCourse.Invoice_number')), 'width' => '36'),
    array('label' => strtoupper(__t('TrainingPlannedCourse.Name')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Date_from')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Date_to')), 'width' => '36'),
    array('label' => strtoupper(__t('Event.Start_time')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Duration')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Availability')), 'width' => '36'),
    array('label' => strtoupper(__t('TrainingPlannedCourse.Invoice_number')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Venue')), 'width' => '36'),
    array('label' => strtoupper(__t('Visit.Address')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Postcode')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Town')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Delegate_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Delegate_general_detail')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Phone')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Purchase_order_number')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Invoice_number')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Credits_taken')), 'width' => '36'),
    array('label' => strtoupper(__t('CRM.Cancelled')), 'width' => '36'),
    array('label' => strtoupper(__t('General.Cancelled_reason')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Network_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Garage_name')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Town')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Postcode')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Province')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Garage_address')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Garage_email')), 'width' => '36'),
    array('label' => strtoupper(__t('Garage.Garage_phone_number')), 'width' => '36'),
    array('label' => strtoupper(__t('Distributor.Account_number')), 'width' => '36'),
);

//If it's Benelux there is not garage g_number, but if the role is Super Admin, garages from both regions can be present at the same time
if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) {
    array_push($table, array('label' => strtoupper(__t('Garage.G_number')), 'width' => '36'));
}


$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
    array(
        14,
        15,
    )
);

//////////////////////// FILL PAGES ////////////////////////

foreach ($courses as $course) {

    //////////////////////// GARAGE - PAGE ////////////////////////
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $status_value = null;
    if ($course['TrainingPlannedCourse']['status'] == ConstantsPlannnedCourseStatus::INACTIVE) {
        $status_value = ConstantsPlannnedCourseStatusName::INACTIVE;
    } elseif ($course['TrainingPlannedCourse']['status'] == ConstantsPlannnedCourseStatus::ACTIVE) {
        $status_value = ConstantsPlannnedCourseStatusName::ACTIVE;
    } else {
        $status_value = ConstantsPlannnedCourseStatusName::CANCELED;
    }
    $course_row = array();
    $course_row[] = $course['TrainingProvider']['name'];
    $course_row[] = $course['TrainingProvider']['email'];
    $course_row[] = $course['TrainingProvider']['phone'];
    $course_row[] = $course['TrainingTrainer']['name'];
    $course_row[] = $course['TrainingTrainer']['email'];
    $course_row[] = $course['TrainingTrainer']['phone'];
    $course_row[] = $course['CourseType']['name_' . __l()];
    $course_row[] = $course['TrainingCourse']['duration'];
    $course_row[] = $course['TrainingCourse']['price'];
    $course_row[] = $course['TrainingCourse']['price_credit'];
    $course_row[] = $course['TrainingCourse']['cost_training'];
    $course_row[] = $course['TrainingCourse']['part_number'];
    $course_row[] = $course['TrainingCourse']['invoice_number'];
    $course_row[] = $course['TrainingCourse']['name'];
    $course_row[] = Fecha::toFormatoVistaFecha($course['TrainingPlannedCourse']['date_from']);
    $course_row[] = Fecha::toFormatoVistaFecha($course['TrainingPlannedCourse']['date_to']);
    $course_row[] = $course['TrainingPlannedCourse']['starting_time'];
    $course_row[] = $course['TrainingPlannedCourse']['duration'];
    $course_row[] = $course['TrainingPlannedCourse']['availability'];
    $course_row[] = $course['TrainingPlannedCourse']['invoice_number'];
    $course_row[] = $course['Venue']['name'];
    $course_row[] = $course['Venue']['address_1'];
    $course_row[] = $course['Venue']['post_code'];
    $course_row[] = $course['Venue']['town'];
    $course_row[] = $course['Contact']['first_name'] . ' ' . $course['Contact']['last_name'];
    $course_row[] = $course['Contact']['email'];
    $course_row[] = $course['Contact']['phone'];
    $course_row[] = $course['TrainingDelegate']['order_number'];
    $course_row[] = $course['TrainingDelegate']['invoice_number'];
    $course_row[] = ($course['TrainingDelegate']['is_refund_eligible'] == ConstantsBooleans::ACTIVE) ? __t('General.Yes') : __t('General.No');
    $course_row[] = $course['TrainingDelegate']['cancelled'] == ConstantsBooleans::YES ? __t('General.Yes') : __t('General.No');
    $course_row[] = isset($course['TrainingDelegate']['reason_cancelled_id']) ? $reason_cancelled_list[$course['TrainingDelegate']['reason_cancelled_id']] : '';
    $course_row[] = $course[0]['network_concatenated_fields'];
    $course_row[] = $course['Garage']['name'] . ' - ' . $course['Garage']['ref_code'];
    $course_row[] = $course['Garage']['town'];
    $course_row[] = $course['Garage']['postcode'];
    $course_row[] = $course['Province']['name'];
    $course_row[] = $course['Garage']['address1'];
    $course_row[] = $course['Garage']['email'];
    $course_row[] = isset($course['Garage']['phone']) ? $course['Garage']['phone'] : $course['Garage']['mobile'];
    $course_row[] = $course['Distributor']['account_number'];

    if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) {
        $course_row[] = $course['Garage']['g_number_id'];
    }

    $this->PhpExcel->addTableRow($course_row, $font_color_row);
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Training.Training') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
