<?php echo $this->Html->script('/js/distributor_sales.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('CRM.Crm'),
                array(
                    'controller' => 'dashboard',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Distributor.Distributor'),
                array(
                    'controller' => 'clients',
                    'action' => 'home_distributors'
                )
            ),
            __t('CRM.Sales'),
        
        ));
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="d-inline-block p-right-1 cnt-title-report titulo-nombre-taller p-bottom-1">
        <?php echo $this->element('../Clients/Elements/menu_distributor') ?>
    </div>
    
    <ul class="aag-subtabs clear m-bottom-1">
        <li><a onclick= "Disable1()",><?php echo __t('CRM.Table_1') ?></a></li>
        <li><a onclick= "Disable2()" ><?php echo __t('CRM.Table_2') ?></a></li>
        <li><a onclick= "Disable3()" ><?php echo __t('CRM.Table_3') ?></a></li>
        <li><a onclick= "Disable4()" ><?php echo __t('CRM.Table_4') ?></a></li>
        <li><a onclick= "Disable5()" ><?php echo __t('CRM.Table_5') ?></a></li>
        <li><a onclick= "Disable6()" ><?php echo __t('CRM.Table_6') ?></a></li>
    </ul>

    <div class="row p-top-1" id='data1'>
        <div class="columns medium-12">
            <h3><?php echo __t('CRM.Table_1') ?></h3>
            <img src="/img/coming_soon_2.png" alt="COMING SOON">
        </div>
    </div>

    <div class="row p-top-1 d-none" id='data2'>
        <div class="columns medium-12">
            <h3><?php echo __t('CRM.Table_2') ?></h3>
            <img src="/img/coming_soon_2.png" alt="COMING SOON">
        </div>
    </div>

    <div class="row p-top-1 d-none" id='data3'>
        <div class="columns medium-12">
            <h3><?php echo __t('CRM.Table_3') ?></h3>
            <img src="/img/coming_soon_2.png" alt="COMING SOON">
        </div>
    </div>

    <div class="row p-top-1 d-none" id='data4'>
        <div class="columns medium-12">
            <h3><?php echo __t('CRM.Table_4') ?></h3>
            <img src="/img/coming_soon_2.png" alt="COMING SOON">
        </div>
    </div>

    <div class="row p-top-1 d-none" id='data5'>
        <div class="columns medium-12">
            <h3><?php echo __t('CRM.Table_5') ?></h3>
            <img src="/img/coming_soon_2.png" alt="COMING SOON">
        </div>
    </div>

    <div class="row p-top-1 d-none" id='data6'>
        <div class="columns medium-12">
            <h3><?php echo __t('CRM.Table_6') ?></h3>
            <img src="/img/coming_soon_2.png" alt="COMING SOON">
        </div>
    </div>
</div>