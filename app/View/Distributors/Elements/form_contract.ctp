<?php
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
echo $this->Html->script('distributor_contracts.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'Distributor',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);

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
            __t('Distributor.Contract'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium four go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->Html->link(
            __t('General.Next'),
            array(
                'controller' => 'distributors_images',
                'action' => 'add_image_distributor',
                $distributor_id
            ),
            array(
                'class' => 'aag-button medium two'
            )
        ); ?>
    </div>
</div>
<?php echo $this->element('../Distributors/tabs', array('selected' => 'contract_distributor',)); ?>
<div class="cnt-data p-top-1 p-bottom-1">
    <div class="flex fw-wrap ai-center cnt-data-element gap-1">
        <div class="aag-title m-right-auto">
            <?php echo h($distributor['Distributor']['name']); ?>
        </div>
        <?php
        if ($this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])) { ?>
            <?php
            echo $this->Html->link(
                __t('Distributor.New_contract'),
                array(
                    'controller' => 'distributors_contracts',
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
        <div class="aag-subtitle m-bottom-1">
            <?php echo __t('Distributor.Contract'); ?>
        </div>
    </div>
    <div class="o-auto">
        <table class="table-tracking tabla-responsive">
            <thead>
                <tr>
                    <th><?php echo __t('Distributor.Trading_group'); ?></th>
                    <th><?php echo __t('Network.Network'); ?></th>
                    <th><?php echo $this->Paginator->sort('DistributorContract.start_date', __t('Distributor.Start_date')); ?></th>
                    <th><?php echo $this->Paginator->sort('DistributorContract.end_date', __t('Distributor.End_date')); ?></th>
                    <th><?php echo __t('Distributor.Leaving_reason'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($distributor_contracts as $distributor_contract) { ?>
                    <tr>
                        <td>
                            <?php
                            if ($this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])) {
                                echo $this->Html->link(
                                    $trading_groups[$distributor_contract['DistributorContract']['trading_group_id']],
                                    array(
                                        'controller' => 'distributors_contracts',
                                        'action' => 'edit',
                                        $distributor_contract['DistributorContract']['id'],
                                        $distributor_id,

                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                            } else {
                                echo h($trading_groups[$distributor_contract['DistributorContract']['trading_group_id']]);
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo isset($distributor_contract['DistributorContract']['network_id']) ? h($networks_distributors[$distributor_contract['DistributorContract']['network_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo h(Fecha::toFormatoVista($distributor_contract['DistributorContract']['start_date'])); ?>
                        </td>
                        <td>
                            <?php echo h(Fecha::toFormatoVista($distributor_contract['DistributorContract']['end_date'])); ?>
                        </td>
                        <td>
                            <?php echo ($distributor_contract['DistributorContract']['leaving_reason_id']) ? h($leaving_reasons[$distributor_contract['DistributorContract']['leaving_reason_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php
                            echo $this->Html->Link(
                                '<span class="aag-icon-papelera c-fallo"></span>',
                                array(
                                    'controller' => 'distributors_contracts',
                                    'action' => 'delete',
                                    $distributor_contract['DistributorContract']['id'],
                                    $distributor_id
                                ),
                                array(
                                    'class' => 'delete-distributor-contract-js',
                                    'escape' => false,
                                    'data-confirmmsg' => __t('Contract.Confirm_delete'),
                                    'data-yes' => __t('General.Yes'),
                                    'data-no' => __t('General.No'),
                                )
                            );
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php if (
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