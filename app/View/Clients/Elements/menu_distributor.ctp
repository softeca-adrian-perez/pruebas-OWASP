<?php
$action = $this->request->action;
if($action == 'report_distributor') {
    $active1 = 'filled';
    $active2 = '';
    $active3 = '';
    $active4 = '';
}
elseif($action == 'tracking_distributor') {
    $active1 = '';
    $active2 = 'filled';
    $active3 = '';
    $active4 = '';
}
elseif($action == 'tracking_distributor_task') {
    $active1 = '';
    $active2 = '';
    $active3 = 'filled';
    $active4 = '';
}
elseif($action == 'distributor_sales') {
    $active1 = '';
    $active2 = '';
    $active3 = '';
    $active4 = 'filled';
}
?>

<div class="columns medium-6">
    <!-- <span class="icon-distributors img-header-client icono-grande"></span>
    <div class="title-header">
        <span><?php echo h($distributor['Distributor']['name']); ?></span>
        <div class="address-header">
            <?php echo h($distributor['Distributor']['address1']);
            if(!empty($distributor['Distributor']['address2'])){
                echo ", " . h($distributor['Distributor']['address2']);
            }
            $distributor['Distributor']['address2'];
                if(!empty($distributor['Distributor']['address3'])){
                    echo ", " . h($distributor['Distributor']['address3']);
                }
                if(!empty($distributor['Distributor']['address4'])){
                    echo ", " . h($distributor['Distributor']['address4']);
                }
            ?>
            <div>
                <?php echo h($distributor['Distributor']['postcode']) . ' ' . h($distributor['Distributor']['town']); ?>
            </div>
        </div>
    </div> -->
        <div class="aag-title">
            <?php echo h($distributor['Distributor']['name']).' - '.h($distributor['Distributor']['account_number']); ?>
        </div>
</div>
<div class="columns medium-6 ta-right right-0 p-bottom-1" style="padding-top: 0;">
    <?php echo $this->Html->link(
        "<span class='ion-android-textsms'></span> " . __t('CRM.About'),
        array(
            'controller' => 'clients',
            'action' => 'report_distributor',
            $distributor['Distributor']['id']
        ),
        array(
            'class' => 'aag-button medium one outlined ' . $active1,
            'escape' => false,
        )
    ); ?>
    <?php echo $this->Html->link(
        "<span class='ion-flag'></span> " . __t('CRM.Visit_history'),
        array(
            'controller' => 'clients',
            'action' => 'tracking_distributor',
            $distributor['Distributor']['id']
        ),
        array(
            'class' => 'aag-button medium one outlined ' . $active2,
            'escape' => false,
        )
    ); ?>
    <?php echo $this->Html->link(
        "<span class='ion-flag'></span> " . __t('CRM.Task_history'),
        array(
            'controller' => 'clients',
            'action' => 'tracking_distributor_task',
            $distributor['Distributor']['id']
        ),
        array(
            'class' => 'aag-button medium one outlined ' . $active3,
            'escape' => false,
        )
    ); ?>
</div>
