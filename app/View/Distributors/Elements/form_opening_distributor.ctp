<?php
echo $this->Html->css('../js/lib/fullcalendar-3.10.5/fullcalendar.min.css', array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/lib/moment.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/fullcalendar.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/locale/' . __l() . '.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('opening-times-general.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE,ConstantsRoles::DISTRIBUTOR))?'my_data':'view';
echo $this->Form->create(
    'Distributor',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'opening-times-form'
    ));

echo $this->Form->hidden('Distributor.id'); ?>

<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Distributor.Distributors'),
                array(
                    'controller' => 'distributors',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                $distributor['Distributor']['name'],
                array(
                    'controller' => 'distributors',
                    'action' => $action,
                    $distributor['Distributor']['id']
                )
            ),
            __t('Garage.Opening_times'),
        ));
        ?>
    </div>
    <div>
        <?php 
        if($this->Acceso->haveTradingGroupPermission( ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR , $distributor['Distributor']['trading_group_id'] )){
            echo $this->element(
                'Comun/form_actions',
                $cancel_action
            );
        } else if($this->Acceso->haveTradingGroupPermission( ConstantsPermissionsGrouping::REQUEST_CHANGE_DISTRIBUTOR , $distributor['Distributor']['trading_group_id'] )){
            echo $this->Form->button(
                __t('RequestedChanges.Request_changes'),
                array(
                    'type' => 'submit',
                    'name' => 'request_changes',
                    'style' => 'margin-top:0 !important;',
                    'class' => 'btn-edit edit',
                )
            );
        } ?>
    </div>
</div>
<?php echo $this->element('../Distributors/tabs', array('selected' => 'opening_distributor',)); ?>
<div class="cnt-data aag-padding">
    <div class="aag-title m-top-1">
        <?php echo h($distributor['Distributor']['name']); ?>
    </div>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Garage.Opening_times'); ?>
    </div>
    <div class="p-1" id="calendar"
        data-url_events="<?php echo Router::url(
            array(
                'controller' => 'distributors',
                'action' => 'ajax_events_list',
                $distributor_id
            )
        ); ?>">
    </div>
</div>

<?php
$days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');

foreach ($days as $day) {
    echo $this->Form->hidden(
        $day . '_open_1',
        array(
            'id' => $day . '-open-1',
        )
    );
    echo $this->Form->hidden(
        $day . '_closed_1',
        array(
            'id' => $day . '-closed-1',
        )
    );
    echo $this->Form->hidden(
        $day . '_open_2',
        array(
            'id' => $day . '-open-2',
        )
    );
    echo $this->Form->hidden(
        $day . '_closed_2',
        array(
            'id' => $day . '-closed-2',
        )
    );
} ?>

<?php echo $this->Form->end(); ?>
