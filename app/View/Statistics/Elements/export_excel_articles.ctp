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
$this->PhpExcel->cambiarTituloHoja(__t('Statistics.Articles'));

$table = array(
    array('label' => strtoupper(__t('UserStatistic.Article')), 'width' => '60'),
    array('label' => strtoupper(__t('Communication.Url')), 'width' => '60'),
    array('label' => strtoupper(__t('UserStatistic.Connections')), 'width' => '20'),
);


$this->PhpExcel->addTableHeader($table, array(), $rgbColor, $rgbBackgroundColor, false);


//////////////////////// FILL PAGES ////////////////////////

$cont_row_garages = 2;



foreach ($users_statistics_articles_names as $key => $article_name) {
    $this->PhpExcel->irAHoja($n_hojas[0]);
    $this->PhpExcel->row = $cont_row_garages;

    $connection_row = array();
    if (!is_array($article_name)) {
        $connection_row[] = $article_name;
    } else {
        $connection_row[] = $article_name[0];
        $this->PhpExcel->changeCellColorAndBackground('ADADAD', 'FFFFFF', $this->PhpExcel->getActualRowNumber(), 0);
    }
    $connection_row[] = $users_statistics_articles_url[$key];
    $connection_row[] = $users_statistics_articles[$key];


    $this->PhpExcel->addTableRow($connection_row, $font_color_row);
    $cont_row_garages++;
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(__t('Statistics.Connections_per_articles') . Fecha::getCompleteDate() . '.xlsx');
header('Set-Cookie: fileDownload=true; path=/');
