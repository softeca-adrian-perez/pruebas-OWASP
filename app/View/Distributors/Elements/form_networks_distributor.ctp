<?php
echo $this->Form->create(
    'Garage',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
?>
<div class="cnt-data-element m-top-1">
    <div class="aag-subtitle">
        <?php echo __t('Network.Networks'); ?>
    </div>
</div>
<div class="o-auto">
    <table class="table-tracking tabla-responsive">
        <thead>
            <tr>
                <th><?php echo __t('Garage.Network'); ?></th>
                <th class="ta-center"><?php echo __t('Garage.Trading_group'); ?></th>
                <th><?php echo $this->Paginator->sort('DistributorDistributorNetwork.contract_start_date', __t('Garage.Start_date')); ?></th>
                <th><?php echo $this->Paginator->sort('DistributorDistributorNetwork.contract_end_date', __t('Garage.End_date')); ?></th>
                <th><?php echo __t('Network.Leaving_reason'); ?></th>
                <th class="ta-center"><?php echo __t('Garage.Status'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($distributor_networks as $distributor_network) { ?>
                <tr>
                    <?php foreach ($networks_image as $network) {
                        if ($network['DistributorNetwork']['id'] == $distributor_network['DistributorDistributorNetwork']['network_id']) { ?>
                            <td>
                                <div>
                                    <img title="<?php echo h($network['DistributorNetwork']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['DistributorNetwork']['image']); ?>" style="max-width: 125px">
                                </div>
                                <div>
                                    <?php
                                    if ($this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])) {
                                        echo $this->Html->link(
                                            $network['DistributorNetwork']['name'],
                                            array(
                                                'controller' => 'distributors_distributors_networks',
                                                'action' => 'view',
                                                $distributor_network['DistributorDistributorNetwork']['id']
                                            ),
                                            array(
                                                'class' => 'c-primary'
                                            )
                                        );
                                    } else {
                                        echo h($network['DistributorNetwork']['name']);
                                    }
                                    ?>
                                </div>
                            </td>
                    <?php }
                    } ?>
                    <td class="ta-center">
                        <?php foreach ($trading_groups as $trading_group) {
                            if ($trading_group['TradingGroup']['id'] == $distributor_network['DistributorDistributorNetwork']['trading_group_id']) {
                                echo h($trading_group['TradingGroup']['name']);
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo Fecha::toFormatoVistaFecha($distributor_network['DistributorDistributorNetwork']['contract_start_date']); ?>
                    </td>
                    <td>
                        <?php echo Fecha::toFormatoVista($distributor_network['DistributorDistributorNetwork']['contract_end_date']); ?>
                    </td>
                    <td>
                        <?php echo isset($distributor_network['DistributorDistributorNetwork']['reason_leaving_id']) ? h($reasons_leaving[$distributor_network['DistributorDistributorNetwork']['reason_leaving_id']]) : '' ?>
                    </td>
                    <td class="ta-center">
                        <?php
                        if ($distributor_network['DistributorDistributorNetwork']['status'] == ConstantsNetworksStatus::UNSUBSCRIBE || $distributor_network['DistributorDistributorNetwork']['status'] == ConstantsNetworksStatus::NOT_CONVERTED) {
                            $class = 'c-fallo';
                        } elseif ($distributor_network['DistributorDistributorNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                            $class = 'c-exito';
                        } else {
                            $class = 'c-informacion';
                        }
                        ?>
                        <strong class="<?php echo $class; ?>"><?php echo h($networks_statuses[$distributor_network['DistributorDistributorNetwork']['status']]); ?></strong>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php echo $this->element('Comun/paginacion'); ?>
</div>
<?php
if (
    !$this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id']) &&
    $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
) { ?>
    <div class="row p-top-1">
        <div class="columns medium-7">
            <div class="aag-subtitle">
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