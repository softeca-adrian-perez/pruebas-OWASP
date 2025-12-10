<?php
echo $this->Html->css('../js/lib/fullcalendar-3.10.5/fullcalendar.min.css', array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/lib/moment.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/fullcalendar.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/locale/' . __l() . '.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('opening-times-general.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';

echo $this->Form->create(
    'Garage',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'opening-times-form'
    )
);

echo $this->Form->hidden('Garage.id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Garage.Garages'),
                array(
                    'controller' => 'garages',
                    'action' => 'home'
                )
            ),
            __t('Garage.Opening_times'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) { ?>
            <button type="button" id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_update_edit',)); ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit'); ?></button>
        <?php } ?>
        <div class="f-right btn-hide" hidden>
            <?php
            if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                echo $this->element(
                    'Comun/form_actions_garage',
                    $cancel_action
                );
            } elseif ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE)) {
                echo $this->Form->button(
                    __t('RequestedChanges.Request_changes'),
                    array(
                        'type' => 'submit',
                        'name' => 'request_changes',
                        'class' => 'aag-button medium',
                    )
                );
            }
            ?>
        </div>
    </div>
</div>
<?php echo $this->element('../Garages/tabs', array('selected' => 'opening_garage')); ?>
<div class="cnt-data aag-padding">
    <div class="aag-title-background">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
    <div class="row p-horizontal-1 pointer-disabled" style="pointer-events: none; cursor: not-allowed">
        <div class="aag-subtitle m-top-1">
            <?php echo __t('Garage.Opening_times'); ?>
        </div>
        <div class="p-1" id="calendar" data-url_events="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_events_list', $garage_id)); ?>"></div>
    </div>
</div>
<?php
$days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
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
}
echo $this->Form->end();
?>