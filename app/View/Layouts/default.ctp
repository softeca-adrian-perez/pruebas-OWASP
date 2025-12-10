<?php
echo $this->extend('layout');
echo $this->fetch('content');

$this->start('cabecera');
echo $this->element('Comun/cabecera');
$this->end();

$this->start('breadcrumbs');
echo $this->element('Comun/breadcrumb');
$this->end();

$this->start('pie');
echo $this->element('Comun/pie');
$this->end();
?>