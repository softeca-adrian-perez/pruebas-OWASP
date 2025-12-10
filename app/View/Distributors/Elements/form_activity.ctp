<?php

echo $this->Form->create(
    'Distributor',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);

echo $this->Form->hidden('Distributor.id');
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
$config = CakeSession::read('Auth.User.Config');
?>
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
            __t('Distributor.Activity'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium four go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        if ($config[ConstantsConfig::AAG_SERVICES]) {
            echo $this->Html->link(
                __t('General.Next'),
                array(
                    'controller' => 'distributors',
                    'action' => 'add_services',
                    $distributor_id
                ),
                array(
                    'class' => 'aag-button medium two'
                )
            );
        } else {
            echo $this->Html->link(
                __t('General.Next'),
                array(
                    'controller' => 'distributors',
                    'action' => 'add_software',
                    $distributor_id
                ),
                array(
                    'class' => 'aag-button medium two'
                )
            );
        } ?>
    </div>
</div>
<?php echo $this->element('../Distributors/tabs', array('selected' => 'activity_distributor',)); ?>
<div class="cnt-data p-top-1 p-bottom-1">
    <div class="flex fw-wrap ai-center cnt-data-element gap-1">
        <div class="aag-title m-top-1 m-right-auto">
            <?php echo h($distributor['Distributor']['name']); ?>
        </div>
        <?php
        if ($this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])) {
            echo $this->Html->link(
                __t('Distributor.Add_activity'),
                array(
                    'controller' => 'distributors_activities',
                    'action' => 'add',
                    $distributor_id
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium green m-top-1',
                )
            );
        } ?>
    </div>
    <div class="cnt-data-element m-top-1">
        <div class="aag-subtitle m-bottom-1">
            <?php echo __t('Distributor.Activity'); ?>
        </div>
    </div>
    <div class="o-auto">
        <table class="table-tracking tabla-responsive">
            <thead>
                <tr>
                    <th><?php echo __t('Activity.Activity'); ?></th>
                    <?php if ($config[ConstantsConfig::ACTIVITY_TYPE]) { ?>
                        <th><?php echo __t('Activity.Type'); ?></th>
                    <?php } ?>
                    <?php if ($config[ConstantsConfig::WORKSHOP_ACTIVITIES]) { ?>
                        <th><?php echo __t('Distributor.Workshop_activities'); ?></th>
                        <th><?php echo __t('Distributor.Activity_details'); ?></th>
                    <?php } ?>
                    <th><?php echo $this->Paginator->sort('DistributorCustomerActivity.start_date', __t('Distributor.Start_date')); ?></th>
                    <th><?php echo $this->Paginator->sort('DistributorCustomerActivity.end_date', __t('Distributor.End_date')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($distributor_activities as $distributor_activity) { ?>
                    <tr>
                        <td>
                            <?php
                            if ($this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])) {
                                echo $this->Html->link(
                                    $customer_activities[$distributor_activity['DistributorCustomerActivity']['customer_activity_id']],
                                    array(
                                        'controller' => 'distributors_activities',
                                        'action' => 'edit',
                                        $distributor_activity['DistributorCustomerActivity']['id'],
                                        $distributor_id,

                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                            } else {
                                echo h($customer_activities[$distributor_activity['DistributorCustomerActivity']['customer_activity_id']]);
                            }
                            ?>
                        </td>
                        <?php if ($config[ConstantsConfig::ACTIVITY_TYPE]) { ?>
                            <td>
                                <?php echo ($distributor_activity['DistributorCustomerActivity']['type']) ? __t('Distributor.Workshop') : __t('Distributor.Distributor'); ?>
                            </td>
                        <?php } ?>
                        <?php if ($config[ConstantsConfig::WORKSHOP_ACTIVITIES]) { ?>
                            <td>
                                <?php if (isset($distributor_activity['DistributorCustomerActivity']['workshops'])) {
                                    foreach ($distributor_activity['DistributorCustomerActivity']['workshops'] as $workshop) {
                                        echo h($workshop) . '<br>';
                                    }
                                } ?>
                            </td>
                            <td>
                                <?php if (isset($distributor_activity['DistributorCustomerActivity']['details'])) {
                                    foreach ($distributor_activity['DistributorCustomerActivity']['details'] as $details) {
                                        echo h($details) . '<br>';
                                    }
                                } ?>
                            </td>
                        <?php } ?>
                        <td>
                            <?php echo h(Fecha::toFormatoVista($distributor_activity['DistributorCustomerActivity']['start_date'])); ?>
                        </td>
                        <td>
                            <?php echo h(Fecha::toFormatoVista($distributor_activity['DistributorCustomerActivity']['end_date'])); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
</div>
<?php
if (
    !$this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id']) &&
    $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
) { ?>
    <div class="row p-top-1">
        <div class="columns medium-7">
            <div class="titulo2">
                <?php echo __t('RequestedChanges.Describe_change'); ?>
            </div>
        </div>
        <div class="medium-5 columns ta-right cnt-buttons-v2">
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
        <div class="medium-12 columns end">
            <?php
            echo $this->Form->input(
                'ChangeDescription',
                array(
                    'type' => 'text',
                    'name' => 'change_description',
                    'rows' => 7,
                    'label' => false
                )
            );
            ?>
        </div>
    </div>
<?php } ?>
</div>
<?php echo $this->Form->end(); ?>