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
$this->PhpExcel->cambiarTituloHoja(__t('Training.Planned_courses'));

$table = array(
    array('label' => strtoupper(__t('Training.Course')), 'width' => '45'),
    array('label' => strtoupper(__t('TrainingTrainer.name')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Course_type')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Date_from')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Date_to')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Duration')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Status')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Credits')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Cost_of_course') . ' (£)'), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Availability')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Invoice_number')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Venue')), 'width' => '36'),
	array('label' => strtoupper(__t('Training.Sales_area')), 'width' => '36'),
    array('label' => strtoupper(__t('Venue.Address_1')), 'width' => '36'),
    array('label' => strtoupper(__t('Venue.Address_2')), 'width' => '36'),
    array('label' => strtoupper(__t('Venue.Town')), 'width' => '36'),
    array('label' => strtoupper(__t('Venue.Post_code')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Active_delegates')), 'width' => '36'),
    array('label' => strtoupper(__t('Training.Total_delegates')), 'width' => '36'),
);

$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);
$this->PhpExcel->addColumnDateType(
    array(
        3,
        4,
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
    $course_row[] = $course['TrainingCourse']['name'];
    $course_row[] = $course['TrainingTrainer']['name'];
    $course_row[] = $course['CourseType']['name_' . __l()];
    $course_row[] = Fecha::toFormatoVistaFecha($course['TrainingPlannedCourse']['date_from']);
    $course_row[] = Fecha::toFormatoVistaFecha($course['TrainingPlannedCourse']['date_to']);
    $course_row[] = $course['TrainingPlannedCourse']['duration'];
    $course_row[] = $status_value;
    $course_row[] = $course['TrainingCourse']['price_credit'];
    $course_row[] = $course['TrainingCourse']['cost_training'];
    $course_row[] = $course['TrainingPlannedCourse']['availability'];
    $course_row[] = $course['TrainingPlannedCourse']['invoice_number'];
    $course_row[] = $course['Venue']['name'];
	$course_row[] = isset($course['Venue']['sales_area_id']) ? $sales_area[$course['Venue']['sales_area_id']] : '';
    $course_row[] = $course['Venue']['address_1'];
    $course_row[] = $course['Venue']['address_2'];
    $course_row[] = $course['Venue']['town'];
    $course_row[] = $course['Venue']['post_code'];
    $course_row[] = $course[0]['delegates_num'];
    $course_row[] = $course[0]['delegates_num_all'];

    $this->PhpExcel->addTableRow($course_row, $font_color_row);
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Training.Planned_courses') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
