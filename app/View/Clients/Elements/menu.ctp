<?php
$action = $this->request->action;
if( $action == 'resume' )
{
    $active1 = 'filled';
    $active2 = '';
    $active3 = '';
    $active4 = '';
}
elseif( $action == 'report' )
{
    $active1 = '';
    $active2 = 'filled';
    $active3 = '';
    $active4 = '';
}
elseif( $action == 'tracking' )
{
    $active1 = '';//button-crm
    $active2 = '';
    $active3 = 'filled';
    $active4 = '';
}
elseif( $action == 'tracking_task' )
{
    $active1 = '';//button-crm
    $active2 = '';
    $active3 = '';
    $active4 = 'filled';
}
if($garage['Garage']['status'] == ConstantsGarageStatusDe::POTENTIAL)
{
    $status = ' -  id: ' . $this->request['pass'][0];
}
else
{
    $status = '';
}
?>
<div class="columns medium-6">
    <!-- <span class="icon-garages img-header-client icono-grande"></span>
    <div class="title-header">
        <span><?php echo h($garage['Garage']['name']) . $status; ?></span>
        <div class="address-header">
            <?php echo h($garage['Garage']['address1']);
            if(!empty($garage['Garage']['address2'])){
                echo ", " . h($garage['Garage']['address2']);
            }
                if(!empty($garage['Garage']['address3'])){
                    echo ", " . h($garage['Garage']['address3']);
                }
                if(!empty($garage['Garage']['address4'])){
                    echo ", " . h($garage['Garage']['address4']);
                }
            ?>
            <div>
                <?php echo h($garage['Garage']['postcode']) . ' ' . h($garage['Garage']['town']); ?>
            </div>
        </div>
    </div> -->
    <div class="aag-title">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
</div>
<div class="columns medium-6 ta-right right-0 p-bottom-1">
    <?php
    echo $this->Html->link(
        "<span class='ion-android-textsms'></span> " . __t('CRM.About'),
        array(
            'controller' => 'clients',
            'action' => 'report',
            $garage['Garage']['id']
        ),
        array(
            'class' => 'aag-button medium one outlined ' . $active2,
            'escape' => false,
        )
    );
    echo $this->Html->link(
        "<span class='ion-flag'></span> " . __t('CRM.Visit_history'),
        array(
            'controller' => 'clients',
            'action' => 'tracking',
            $garage['Garage']['id']
        ),
        array(
            'class' => 'aag-button medium one outlined ' . $active3,
            'escape' => false,
        )
    );
    echo $this->Html->link(
        "<span class='ion-flag'></span> " . __t('CRM.Task_history'),
        array(
            'controller' => 'clients',
            'action' => 'tracking_task',
            $garage['Garage']['id']
        ),
        array(
            'class' => 'aag-button medium one outlined ' . $active4,
            'escape' => false,
        )
    );
    ?>
</div>
