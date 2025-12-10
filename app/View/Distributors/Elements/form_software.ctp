<?php
echo $this->Form->create(
    'Distributor',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('Distributor.id');
$config = CakeSession::read('Auth.User.Config');
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
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
            __t('Distributor.Add_software'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            CakeSession::read('url_referer'),
            array(
                'class' => 'aag-button medium four',
            )
        );
        echo $this->Html->link(
            __t('General.Next'),
            array(
                'controller' => 'distributors',
                'action' => 'add_contract',
                $distributor_id
            ),
            array(
                'class' => 'aag-button medium two'
            )
        ); ?>
    </div>
</div>
<?php echo $this->element('../Distributors/tabs', array('selected' => 'software_distributor',)); ?>
<div class="cnt-data p-top-1">
    <div class="flex fw-wrap ai-center cnt-data-element gap-1">
        <div class="aag-title m-right-auto">
            <?php echo h($distributor['Distributor']['name']); ?>
        </div>
        <?php if ($this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])) { ?>
            <?php
            echo $this->Html->link(
                __t('Maintenance.Software_new'),
                array(
                    'controller' => 'distributors_software',
                    'action' => 'add',
                    $distributor_id
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium green',
                )
            );
            ?>
        <?php } ?>
    </div>
    <div class="cnt-data-element m-top-1">
        <div class="aag-subtitle">
            <?php echo __t('Distributor.Software'); ?>
        </div>
    </div>
    <div class="o-auto m-top-1 m-bottom-1">
        <table class="table-tracking table-responsive">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Software.name_' . __l(), __t('Distributor.Software')); ?></th>
                    <th><?php echo __t('Software.Type'); ?></th>
                    <th><?php echo __t('Software.Supplier'); ?></th>
                    <th><?php echo __t('Software.Manufacturer'); ?></th>
                    <th><?php echo $this->Paginator->sort('DistributorSoftware.start_date', __t('Distributor.Start_date')); ?></th>
                    <th><?php echo $this->Paginator->sort('DistributorSoftware.end_date', __t('Distributor.End_date')); ?></th>
                    <th><?php echo $this->Paginator->sort('DistributorSoftware.version', __t('Distributor.Version')); ?></th>
                    <?php if ($config[ConstantsConfig::SOFTWARE_USER_PASSWORD_DISTRIBUTOR]) { ?>
                        <th><?php echo $this->Paginator->sort('DistributorSoftware.username', __t('Distributor.Username')); ?></th>
                        <th><?php echo $this->Paginator->sort('DistributorSoftware.password', __t('Distributor.Password')); ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($distributor_software as $software_tmp) { ?>
                    <tr>
                        <td>
                            <?php echo $this->Html->link(
                                $software_tmp['Software']['name_' . __l()],
                                array(
                                    'controller' => 'distributors_software',
                                    'action' => 'edit',
                                    $software_tmp['DistributorSoftware']['id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            ); ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['DistributorSoftware']['software_type_id'] ? h($software_types[$software_tmp['DistributorSoftware']['software_type_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['DistributorSoftware']['supplier_id'] ? h($suppliers[$software_tmp['DistributorSoftware']['supplier_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['DistributorSoftware']['software_manufacture_id'] ? h($software_manufactures[$software_tmp['DistributorSoftware']['software_manufacture_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha(h($software_tmp['DistributorSoftware']['start_date'])); ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha(h($software_tmp['DistributorSoftware']['end_date'])); ?>
                        </td>
                        <td>
                            <?php echo h($software_tmp['DistributorSoftware']['version']); ?>
                        </td>
                        <?php if ($config[ConstantsConfig::SOFTWARE_USER_PASSWORD_DISTRIBUTOR]) { ?>
                            <td>
                                <?php echo h($software_tmp['DistributorSoftware']['username']); ?>
                            </td>
                            <td>
                                <?php echo h($software_tmp['DistributorSoftware']['password']); ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
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
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
</div>
<?php echo $this->Form->end(); ?>