<?php
/* Smarty version 3.1.32-dev-38, created on 2018-10-10 14:18:53
  from 'C:\proyectos\gnmaag\app\Vendor\Smarty\libs\templates\style.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-38',
  'unifunc' => 'content_5bbdee2d3a5944_94888086',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '07969b4eefaab0efbea42975406c979a6c8ec64d' => 
    array (
      0 => 'C:\\proyectos\\gnmaag\\app\\Vendor\\Smarty\\libs\\templates\\style.tpl',
      1 => 1538567462,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5bbdee2d3a5944_94888086 (Smarty_Internal_Template $_smarty_tpl) {
?>//Variables

$font-family: 'Roboto', sans-serif;
$medida-de-referencia: 14px;

$primary-color: <?php echo $_smarty_tpl->tpl_vars['primaryColor']->value;?>
;
$primary-color-de-letra: <?php echo $_smarty_tpl->tpl_vars['fontColorPrimary']->value;?>
;
$background-primary-color: <?php echo $_smarty_tpl->tpl_vars['backgroundPrimaryColor']->value;?>
;

$secondary-color: <?php echo $_smarty_tpl->tpl_vars['secondaryColor']->value;?>
;
$secondary-color-de-letra: <?php echo $_smarty_tpl->tpl_vars['fontColorSecondary']->value;?>
;
$background-secondary-color: <?php echo $_smarty_tpl->tpl_vars['backgroundSecondaryColor']->value;?>
;

$tertiary-color: <?php echo $_smarty_tpl->tpl_vars['tertiaryColor']->value;?>
;
$tertiary-color-de-letra: <?php echo $_smarty_tpl->tpl_vars['fontColorTertiary']->value;?>
;
$background-tertiary-color: <?php echo $_smarty_tpl->tpl_vars['tertiaryColor']->value;?>
;

$menu-color: <?php echo $_smarty_tpl->tpl_vars['menuColor']->value;?>
;
$menu-color-active: <?php echo $_smarty_tpl->tpl_vars['menuColorActive']->value;?>
;
$menu-background: <?php echo $_smarty_tpl->tpl_vars['menuBackground']->value;?>
;

$color-borde-elementos: #ddd;
$radio-del-borde: 1em;
$color-exito: #7bd36f;
$color-fallo: #FE472F;
$color-informacion: #FE9F2F;
$ancho-maximo: 200rem; //62.5rem es la medida estandar de foundation.

$small-screen: 641px !default;
$medium-screen: 1280px !default;
$large-screen: 1440px !default;<?php }
}
