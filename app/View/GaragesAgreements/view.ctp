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
            $this->Html->link(
                __t('Network.Distributor_link'),
                array(
                    'controller' => 'garages',
                    'action' => 'add_dis_and_net_garage',
                    $garage_id
                )
            ),
            __t('General.View'),
        ));
        ?>
    </div>
</div>
<div class="row">
    <div class="columns medium-6 title-big header-title">
        <?php echo __t('General.View'); ?>
    </div>
    <?php if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::EDIT_AGREEMENTS)) { ?>
        <div class="medium-6 columns ta-right header-title right-0 cnt-buttons-v2">
            <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
            <?php
            echo $this->Html->link(
                __t('General.Edit'),
                array(
                    'controller' => 'garages_agreements',
                    'action' => 'edit',
                    $garage_agreement['GarageAgreement']['id']
                ),
                array(
                    'escape' => false,
                    'class' => 'btn-edit edit',
                    'style' => 'margin-top:0 !important;'
                )
            ); ?>
        </div>
    <?php } ?>
</div>
<div class="row p-top-responsive">
    <div class="columns">
        <div class="columns background-color-primary p-vertical-1">
            <div class="row p-vertical-1">
                <div class="row p-1">
                    <div class="columns medium-4">
                        <strong><?php echo __t('Garage.Garage') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <?php echo h($garage_agreement['Garage']['name']); ?>
                        </div>
                    </div>
                    <div class="columns medium-4">
                        <strong><?php echo __t('Agreement.Agreement') ?>:</strong>
                        <br>

                        <div class="b-bottom-1 height_input">
                            <?php echo h($agreements[$garage_agreement['GarageAgreement']['agreement_id']]); ?>
                        </div>
                    </div>
                    <div class="columns medium-4 end">
                        <strong><?php echo __t('Agreement.Status') ?>: </strong>
                        <br>

                        <div class="b-bottom-1 height_input">
                            <?php echo h($agreements_statuses[$garage_agreement['GarageAgreement']['status']]); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row p-vertical-1">
                <div class="columns">
                    <div class="titulo2">
                        <?php echo __t('Agreement.Contract'); ?>
                    </div>
                </div>
                <div class="columns medium-3">
                    <strong><?php echo __t('Agreement.Sent_date') ?>: </strong>
                    <br>

                    <div class="b-bottom-1 height_input">
                        <?php echo Fecha::toFormatoVistaFecha(h($garage_agreement['GarageAgreement']['contract_sent_date'])); ?>
                    </div>
                </div>

                <div class="columns medium-3">
                    <strong><?php echo __t('Agreement.Received_date') ?>: </strong>
                    <br>

                    <div class="b-bottom-1 height_input">
                        <?php echo Fecha::toFormatoVistaFecha(h($garage_agreement['GarageAgreement']['contract_received_date'])); ?>
                    </div>
                </div>
                <div class="columns medium-3">
                    <strong><?php echo __t('Agreement.Start_date') ?>: </strong>
                    <br>

                    <div class="b-bottom-1 height_input">
                        <?php echo Fecha::toFormatoVistaFecha(h($garage_agreement['GarageAgreement']['contract_start_date'])); ?>
                    </div>
                </div>
                <div class="columns medium-3 end">
                    <strong><?php echo __t('Agreement.End_date') ?>: </strong>
                    <br>

                    <div class="b-bottom-1 height_input">
                        <?php echo Fecha::toFormatoVistaFecha(h($garage_agreement['GarageAgreement']['contract_end_date'])); ?>
                    </div>
                </div>
            </div>

            <?php if ($garage_agreement['GarageAgreement']['status'] == ConstantsNetworksStatus::ON_HOLD || $garage_agreement['GarageAgreement']['status'] == ConstantsNetworksStatus::LEFT) { ?>
                <div class="row p-vertical-1">
                    <div class="columns">
                        <div class="titulo2">
                            <?php echo ($garage_agreement['GarageAgreement']['status'] == ConstantsNetworksStatus::ON_HOLD ? __t('Agreement.On_hold') : __t('Agreement.Leaving')); ?>
                        </div>
                    </div>
                    <div class="columns medium-6">
                        <strong><?php echo ($garage_agreement['GarageAgreement']['status'] == ConstantsNetworksStatus::ON_HOLD ? __t('Agreement.On_hold_date') : __t('Agreement.Leaving_date')) ?>: </strong>
                        <br>

                        <div class="b-bottom-1 height_input">
                            <?php echo ($garage_agreement['GarageAgreement']['status'] == ConstantsNetworksStatus::ON_HOLD ? Fecha::toFormatoVistaFecha(h($garage_agreement['GarageAgreement']['date_on_hold'])) : Fecha::toFormatoVistaFecha(h($garage_agreement['GarageAgreement']['leaving_date']))); ?>
                        </div>
                    </div>
                    <div class="columns medium-6">
                        <strong><?php echo __t('Agreement.Reason') ?>: </strong><br>

                        <div class="b-bottom-1 height_input">
                            <?php echo ($garage_agreement['GarageAgreement']['status'] == ConstantsNetworksStatus::ON_HOLD ? h($reasons[$garage_agreement['GarageAgreement']['reason_hold_id']]) : h($reasons[$garage_agreement['GarageAgreement']['reason_leaving_id']])) ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>