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
            __t('General.View'),
        ));
        ?>
    </div>
    <div>
        <?php
        if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::EDIT_NETWORK)) {
        ?>
            <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
            echo $this->Html->link(
                __t('General.Edit'),
                array(
                    'controller' => 'garages_networks',
                    'action' => 'edit',
                    $garage_network['GarageNetwork']['id']
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium',
                    'style' => 'margin-top:0 !important;'
                )
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('General.View'); ?>
    </div>
    <div class="cnt-form-inputs">
        <div>
            <strong><?php echo __t('Garage.Garage') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($garage_network['Garage']['name']); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Network.Network') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($networks[$garage_network['GarageNetwork']['network_id']]); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Garage.Trading_group') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php foreach ($trading_groups as $trading_group) {
                    if ($trading_group['TradingGroup']['id'] == $garage_network['GarageNetwork']['trading_group_id']) {
                        echo h($trading_group['TradingGroup']['name']);
                    }
                }
                ?>
            </div>
        </div>
        <!-- <div class="columns medium-3 clear">
            <strong><?php echo __t('Garage.Contract_type') ?>: </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php if ($garage_network['GarageNetwork']['network_contract_type_id']) {
                    echo h($networks_contracts[$garage_network['GarageNetwork']['network_contract_type_id']]);
                } ?>
            </div>
        </div> -->
        <div>
            <strong><?php echo __t('Garage.Status') ?>: </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($networks_statuses[$garage_network['GarageNetwork']['status']]); ?>
            </div>
        </div>
    </div>

    <div class="aag-subtitle p-top-1">
        <?php echo __t('Network.Contract'); ?>
    </div>
    <div class="cnt-form-inputs">
        <div>
            <strong><?php echo __t('Network.Sent_date') ?>: </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo Fecha::toFormatoVistaFecha(h($garage_network['GarageNetwork']['contract_sent_date'])); ?>
            </div>
        </div>

        <div>
            <strong><?php echo __t('Network.Received_date') ?>: </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo Fecha::toFormatoVistaFecha(h($garage_network['GarageNetwork']['contract_received_date'])); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Network.Start_date') ?>: </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo Fecha::toFormatoVistaFecha(h($garage_network['GarageNetwork']['contract_start_date'])); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Network.End_date') ?>: </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo Fecha::toFormatoVistaFecha(h($garage_network['GarageNetwork']['contract_end_date'])); ?>
            </div>
        </div>
        <!-- <div class="row p-vertical-1">
            <div class="columns">
                <div class="titulo2">
                    <?php echo __t('Network.On_hold'); ?>
                </div>
            </div>
            <div class="columns medium-9">
                <strong><?php echo __t('Garage.Reason') ?>: </strong><br>

                <div class="b-bottom-1 height_input">
                    <?php echo Fecha::toFormatoVistaFecha(h($garage_network['GarageNetwork']['reason_on_hold'])); ?>
                </div>
            </div>
            <div>
                <strong><?php echo __t('Garage.Date') ?>: </strong>
                <br>

                <div class="b-bottom-1 height_input">
                    <?php echo Fecha::toFormatoVistaFecha(h($garage_network['GarageNetwork']['date_on_hold'])); ?>
                </div>
            </div>
         </div> -->
        <div>
            <strong><?php echo __t('Network.Annex_detail') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo ($garage_network['GarageNetwork']['annex_detail_id'] ? h($networks_annex_details[$garage_network['GarageNetwork']['annex_detail_id']]) : ""); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Network.Dd_active') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo h($garage_network['GarageNetwork']['dd_active'] == ConstantsBooleans::ACTIVE ? __t("General.Yes") : __t("General.No")); ?>
            </div>
        </div>
        <?php if ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::ON_HOLD || $garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LEFT) { ?>
            <div class="row p-vertical-1">
                <div class="columns">
                    <div class="aag-subtitle">
                        <?php echo ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::ON_HOLD ? __t('Network.On_hold') : __t('Network.Leaving')); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::ON_HOLD ? __t('Network.On_hold_date') : __t('Network.Leaving_date')) ?>: </strong>
                    <br>

                    <div class="b-bottom-1 height_input">
                        <?php echo ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::ON_HOLD ? Fecha::toFormatoVistaFecha(h($garage_network['GarageNetwork']['date_on_hold'])) : Fecha::toFormatoVistaFecha(h($garage_network['GarageNetwork']['leaving_date']))); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Network.Reason') ?>: </strong><br>

                    <div class="b-bottom-1 height_input">
                        <?php echo ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::ON_HOLD ? h($reasons[$garage_network['GarageNetwork']['reason_hold_id']]) : h($reasons[$garage_network['GarageNetwork']['reason_leaving_id']])) ?>
                    </div>
                </div>
                <!-- <div>
                    <strong><?php echo __t('Garage.Date') ?>: </strong>
                    <br>

                    <div class="b-bottom-1 height_input">
                    <?php echo Fecha::toFormatoVistaFecha(h($garage_network['GarageNetwork']['leaving_date'])); ?>
                    </div>
                </div> -->
            </div>
        <?php } ?>

        <!-- <div class="row p-vertical-1">
            <div class="columns">
                <div class="titulo2">
                    <?php echo __t('Network.Leaving'); ?>
                </div>
            </div>
            <div class="columns medium-9">
                <strong><?php echo __t('Garage.Reason') ?>: </strong><br>

                <div class="b-bottom-1 height_input">
                    <?php if(isset($garage_network['GarageNetwork']['reason_leaving_id']) && !empty($garage_network['GarageNetwork']['reason_leaving_id'])){
                        echo h($reasons[$garage_network['GarageNetwork']['reason_leaving_id']]);
                    } elseif(isset($garage_network['GarageNetwork']['reason_hold_id']) && !empty($garage_network['GarageNetwork']['reason_hold_id'])) {
                        echo h($reasons[$garage_network['GarageNetwork']['reason_hold_id']]);
                    } ?>
                </div>
            </div>

        </div> -->
    </div>
</div>