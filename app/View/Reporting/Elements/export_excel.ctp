<?php

$this->PhpExcel->createWorksheet();

$n_hojas = array(
    0 => '0',
);

$this->PhpExcel->cambiarTituloHoja('1');
$this->PhpExcel->setDefaultFont('Calibri', 12);
$this->PhpExcel->setDefaultBorder();

$rgbColor = 'FFFFFF';
$rgbBackgroundColor = '0077FF';
$font_color_row = '000000';

$this->PhpExcel->irAHoja(0);
$this->PhpExcel->row = 1;

if ($graphicType == ConstantesGraphicReports::GRAPHIC_SERVICES) {
	$title = __t('Reporting.QuotationsPerService');
	$table = array(
		array('label' => __t('Reporting.Service'), 'width' => '36'),
		array('label' => __t('Reporting.Quotations') . ' ' . '(%)', 'width' => '36'),
	);
	$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);

	foreach ($labels as $key => $label) {
		$this->PhpExcel->irAHoja($n_hojas[0]);
		$row = array();
		$row[] = $label;
		$row[] = $datasets[$key];

		$this->PhpExcel->addTableRow($row, $font_color_row);
	}
} elseif (ConstantesGraphicReports::GRAPHIC_STATISTICS) {
	$title = __t('Reporting.Statistics');
	$table = array(
		array('label' => __t('Reporting.Period'), 'width' => '36'),
		array('label' => __t('Reporting.Quotations'), 'width' => '36'),
		array('label' => __t('Reporting.Enquiries'), 'width' => '36'),
		array('label' => __t('Reporting.BookingsWithQuotation'), 'width' => '36'),
		array('label' => __t('Reporting.BookingsWithoutQuotation'), 'width' => '36'),
	);
	$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);

	foreach ($labels as $key => $label) {
		$this->PhpExcel->irAHoja($n_hojas[0]);
		$row = array();
		$row[] = $label;
		foreach ($datasets as $keyData => $dataset) {
			$row[] = $dataset['data'][$key];
		}

		$this->PhpExcel->addTableRow($row, $font_color_row);
	}
}

$this->PhpExcel->cambiarTituloHoja($title);
$this->PhpExcel->addTableFooter();
$this->PhpExcel->output($title . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
