<?php
$config = CakeSession::read('Auth.User.Config');
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';

echo $this->Form->create('Garage', array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data'));
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
            __t('Garage.Employees'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
            <button type="button" id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_update_edit')); ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit'); ?></button>
        <?php } ?>
    </div>
</div>
<?php echo $this->element('../Garages/tabs', array('selected' => 'employee_garage',)); ?>
<div class="cnt-data aag-padding">
    <div class="row">
        <div class="aag-title">
            <?php echo h($garage['Garage']['name']); ?>
        </div>
        <div class="aag-subtitle p-top-1">
            <?php echo __t('Garage.Employees'); ?>
        </div>
        <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) { ?>
            <div class="f-right btn-hide" hidden>
                <div class="cnt-buttons-v2 d-inline f-right">
                    <?php echo $this->Html->link(
                        __t('Garage.New_employees'),
                        array(
                            'controller' => 'garages_employees',
                            'action' => 'add',
                            $garage_id
                        ),
                        array(
                            'escape' => false,
                            'class' => 'aag-button small green',
                            'style' => 'position:relative; z-index: 1;',
                        )
                    ); ?>
                </div>
            </div>
        <?php } ?>
        <div class="o-auto">
            <table class="table-tracking">
                <thead>
                    <tr>
                        <th><?php echo __t('Garage.Employee_type'); ?></th>
                        <th class="ta-center"><?php echo __t('Garage.Employee_number'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($garage_employees as $garage_employee) { ?>
                        <tr>
                            <td class="link-text">
                                <?php
                                if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                                    echo $this->Html->link(
                                        $employee_types[$garage_employee['GarageEmployee']['employee_type_id']],
                                        array(
                                            'controller' => 'garages_employees',
                                            'action' => 'edit',
                                            $garage_employee['GarageEmployee']['id']
                                        ),
                                        array(
                                            'class' => 'c-primary'
                                        )
                                    );
                                } else {
                                    echo $employee_types[$garage_employee['GarageEmployee']['employee_type_id']];
                                }
                                ?>
                            </td>
                            <td class="ta-center">
                                <?php echo h($garage_employee['GarageEmployee']['number']); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php
        if (
            !$this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) &&
            $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE)
        ) {
        ?>
            <div class="row p-top-1">
                <?php echo $this->Form->create(); ?>
                <div>
                    <div class="aag-subtitle">
                        <?php echo __t('RequestedChanges.Describe_change'); ?>
                    </div>
                </div>
                <div class="ta-right cnt-buttons-v2">
                    <?php echo $this->Form->button(
                        __t('RequestedChanges.Request_changes'),
                        array(
                            'type' => 'submit',
                            'name' => 'request_changes',
                            'style' => 'margin-top:0 !important;',
                            'class' => 'btn-edit edit',
                        )
                    );
                    ?>
                </div>
                <div>
                    <?php
                    echo $this->Form->input(
                        'ChangeDescription',
                        array(
                            'type' => 'text',
                            'name' => 'change_description',
                            'rows' => 7,
                            'label' => false,
                        )
                    );
                    ?>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
        <?php } ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>