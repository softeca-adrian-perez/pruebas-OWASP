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
                __t('Network.Networks'),
                array(
                    'controller' => 'distributors',
                    'action' => 'add_networks_distributor',
                    $distributor_id
                )
            ),
            __t('General.View'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            array(
                'controller' => 'distributors',
                'action' => 'add_networks_distributor',
                $distributor_id
            ),
            array(
                'class' => 'aag-button medium four',
                'style' => 'margin-top:0 !important;'
            )
        );
        echo $this->Html->link(
            __t('General.Edit'),
            array(
                'controller' => 'distributors_distributors_networks',
                'action' => 'edit',
                $distributor_network['DistributorDistributorNetwork']['id']
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium',
                'style' => 'margin-top:0 !important;'
            )
        ); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('General.View'); ?>
    </div>

    <div class="cnt-form-inputs p-top-1">
        <div>
            <strong><?php echo __t('Distributor.Distributor') ?>:</strong>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo h($distributor_network['Distributor']['name']); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Network.Network') ?>:</strong>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo h($distributors_networks[$distributor_network['DistributorDistributorNetwork']['network_id']]); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Garage.Trading_group') ?>:</strong>
            <br>
            <div class="b-bottom-1 height_input">
                <?php foreach ($trading_groups as $trading_group) {
                    if ($trading_group['TradingGroup']['id'] == $distributor_network['DistributorDistributorNetwork']['trading_group_id']) {
                        echo h($trading_group['TradingGroup']['name']);
                    }
                }
                ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Garage.Status') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo h($networks_statuses[$distributor_network['DistributorDistributorNetwork']['status']]); ?>
            </div>
        </div>
    </div>
    <div class="aag-subtitle p-top-1">
        <?php echo __t('Network.Contract'); ?>
    </div>
    <div class="cnt-form-inputs">
        <div>
            <strong><?php echo __t('Network.Start_date') ?>: </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo Fecha::toFormatoVistaFecha(h($distributor_network['DistributorDistributorNetwork']['contract_start_date'])); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Network.End_date') ?>: </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo Fecha::toFormatoVistaFecha(h($distributor_network['DistributorDistributorNetwork']['contract_end_date'])); ?>
            </div>
        </div>
        <?php if (isset($distributor_network) && in_array($distributor_network['DistributorDistributorNetwork']['status'], array(7, 8))) { ?>
            <div>
                <strong><?php echo __t('Network.Leaving_reason') ?>: </strong><br>

                <div class="b-bottom-1 height_input">
                    <?php echo ($distributor_network['DistributorDistributorNetwork']['reason_leaving_id']) ? h($reasons_leaving[$distributor_network['DistributorDistributorNetwork']['reason_leaving_id']]) : '' ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>