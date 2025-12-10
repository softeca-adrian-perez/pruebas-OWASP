<?php
echo $this->Html->script('garages_distributors.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create('Garage', array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data'));
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
echo $this->Form->hidden(
    'Garage.id',
    array(
        'id' => 'garage_id',
        'value' => $garage_id
    )
);

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
            __t('Network.Distributors_networks'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) { ?>
            <button type="button" id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_update_edit')); ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit'); ?></button>
        <?php } ?>
    </div>
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
                    'style' => 'margin-top:0 !important;',
                    'class' => 'aag-button medium',
                )
            );
        ?>
        <?php } ?>
    </div>
</div>
<?php echo $this->element('../Garages/tabs', array('selected' => 'd&n_garage')); ?>
<div class="cnt-data aag-padding">
    <div class="aag-title-background">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Distributor.Distributors'); ?>
    </div>
    <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) { ?>
        <div class="row btn-hide" hidden>
            <div class="cnt-buttons-v2 d-inline f-right">
                <?php echo $this->Html->link(
                    __t('Distributor.New_distributor'),
                    array(),
                    array(
                        'escape' => false,
                        'class' => 'aag-button small green m-left-1',
                        'title' => __t('Distributor.New_distributor'),
                        'id' => 'btn_add_distributor',
                        'data-url_info' => Router::url(array(
                            'controller' => 'distributors',
                            'action' => 'ajax_get_info_distributor',
                        )),
                    )
                );
                ?>
            </div>
            <div class="d-inline-block f-right" style="width:300px">
                <?php echo $this->Form->input(
                    'DistributorName',
                    array(
                        'label' => false,
                        'type' => 'select',
                        'class' => 'clear_field select2Dinamico_distributor_dynamic',
                        'required' => true,
                        'id' => 'distributor_name',
                        'data-garage_id' => $garage_id,
                    )
                );
                ?>
            </div>
            <div hidden>
                <?php echo $this->Form->input(
                    'GaragesDistributors',
                    array(
                        'type' => 'select',
                        'class' => 'select2-multiple',
                        'multiple' => true,
                        'empty' => false,
                        'id' => 'garages-distributors',
                    )
                );
                ?>
            </div>
            <div hidden>
                <?php echo $this->Form->input(
                    'DeleteGaragesDistributors',
                    array(
                        'type' => 'select',
                        'class' => 'select2-multiple',
                        'multiple' => true,
                        'empty' => false,
                        'id' => 'garages-distributors-delete',
                    )
                );
                ?>
            </div>
        </div>
    <?php } ?>
    <div>
        <table class="table-tracking">
            <thead>
                <tr>
                    <th width="40"><?php echo __t('General.Order'); ?></th>
                    <th><?php echo __t('Network.Distributor'); ?></th>
                    <th class="ta-center"><?php echo __t('Distributor.MAMID'); ?></th>
                    <th class="ta-center"><?php echo __t('Distributor.Account_number'); ?></th>
                    <th class="ta-center btn-hide" hidden><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody id="sort-distributors" data-page="<?php echo ConstantsPagination::FIRST_PAGE; ?>">
                <?php foreach ($garage_distributors as $garage_distributor) { ?>
                    <tr>
                        <td class="order-row">
                            <?php echo $garage_distributor['GarageDistributor']['order']; ?>
                        </td>
                        <td class="item-distributor" data-id="<?php echo $garage_distributor['GarageDistributor']['id']; ?>" data-distributor_id="<?php echo $garage_distributor['GarageDistributor']['distributor_id']; ?>">
                            <?php echo $this->Html->link(
                                $garage_distributor['Distributor']['name'],
                                array(
                                    'controller' => 'distributors',
                                    'action' => 'view',
                                    $garage_distributor['GarageDistributor']['distributor_id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            );
                            ?>
                        </td>
                        <td class="ta-center">
                            <?php echo h($garage_distributor['Distributor']['MAMID']); ?>
                        </td>
                        <td class="ta-center">
                            <?php echo h($garage_distributor['Distributor']['account_number']); ?>
                        </td>
                        <td class="ta-center btn-hide" hidden>
                            <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) { ?>
                                <span class="aag-icon-papelera c-fallo cursor-pointer delete-garage-distributor btn-hide" hidden></span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <hr />
    <div class="row">
        <div class="aag-subtitle">
            <?php echo __t('Network.Networks'); ?>
        </div>
    </div>
    <div class="row p-top-1">
        <div class="columns medium-12 ta-right right-0 cnt-buttons-v2">
            <div class="f-right btn-hide" hidden>
                <?php
                if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                    echo $this->Html->link(
                        __t('Network.New_internal_network'),
                        array(
                            'controller' => 'garages_networks',
                            'action' => 'add',
                            $garage_id,
                            true
                        ),
                        array(
                            'escape' => false,
                            'class' => 'aag-button small green'
                        )
                    );
                }
                ?>
            </div>
        </div>
    </div>
    <div>
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('Network.Internal_network'); ?></th>
                    <th class="ta-center"><?php echo __t('Garage.Trading_group'); ?></th>
                    <th><?php echo __t('Network.Received_date'); ?></th>
                    <th><?php echo __t('Network.Start_date'); ?></th>
                    <th><?php echo __t('Network.Leaving_date'); ?></th>
                    <th><?php echo __t('Distributor.Leaving_reason'); ?></th>
                    <th><?php echo __t('Network.Dd_active'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Status'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($garage_internal_networks as $internal_network) { ?>
                    <tr class="link-text">
                        <?php
                        foreach ($networks_image as $network) {
                            if ($network['Network']['id'] == $internal_network['GarageNetwork']['network_id']) {
                        ?>
                                <td>
                                    <div>
                                        <img title="<?php echo h($network['Network']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']); ?>" style="max-width: 125px">
                                    </div>
                                    <div>
                                        <?php
                                        if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                                            echo $this->Html->link(
                                                $network['Network']['name'],
                                                array(
                                                    'controller' => 'garages_networks',
                                                    'action' => 'edit',
                                                    $internal_network['GarageNetwork']['id']
                                                ),
                                                array(
                                                    'class' => 'c-primary'
                                                )
                                            );
                                        } else {
                                            echo h($network['Network']['name']);
                                        }
                                        ?>
                                    </div>
                                </td>
                        <?php
                            }
                        }
                        ?>
                        <td class="ta-center">
                            <?php foreach ($trading_groups as $trading_group) {
                                if ($trading_group['TradingGroup']['id'] == $internal_network['GarageNetwork']['trading_group_id']) {
                                    echo h($trading_group['TradingGroup']['name']);
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha($internal_network['GarageNetwork']['contract_received_date']); ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha($internal_network['GarageNetwork']['contract_start_date']); ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha($internal_network['GarageNetwork']['leaving_date']); ?>
                        </td>
                        <td>
                            <?php
                                if ($internal_network['GarageNetwork']['status'] != ConstantsNetworksStatus::LIVE) {
                                    echo isset($internal_network['GarageNetwork']['reason_leaving_id']) ? h($reasons_leaving[$internal_network['GarageNetwork']['reason_leaving_id']]) : '';
                                }
                            ?>
                        </td>
                        <td>
                            <?php echo $internal_network['GarageNetwork']['dd_active'] == ConstantsBooleans::YES ? __t('General.Yes') : __t('General.No'); ?>
                        </td>
                        <td class="ta-center">
                            <?php
                            if ($internal_network['GarageNetwork']['status'] == ConstantsNetworksStatus::UNSUBSCRIBE || $internal_network['GarageNetwork']['status'] == ConstantsNetworksStatus::NOT_CONVERTED) {
                                $class = 'c-fallo';
                            } elseif ($internal_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                                $class = 'c-exito';
                            } else {
                                $class = 'c-informacion';
                            }
                            if (isset($internal_network['GarageNetwork']['status']) && !empty($internal_network['GarageNetwork']['status'])) {
                            ?>
                                <strong class="<?php echo $class; ?>"><?php echo h($networks_statuses[$internal_network['GarageNetwork']['status']]); ?></strong>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="row p-top-1">
        <div class="columns medium-12 ta-right right-0 cnt-buttons-v2">
            <div class="f-right btn-hide" hidden>
                <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                    echo $this->Html->link(
                        __t('Network.New_external_network'),
                        array(
                            'controller' => 'garages_networks',
                            'action' => 'add',
                            $garage_id,
                            false
                        ),
                        array(
                            'escape' => false,
                            'class' => 'aag-button small green'
                        )
                    );
                } ?>
            </div>
        </div>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('Network.External_network'); ?></th>
                    <th class="ta-center"><?php echo __t('Garage.Trading_group'); ?></th>
                    <th><?php echo __t('Network.Received_date'); ?></th>
                    <th><?php echo __t('Network.Start_date'); ?></th>
                    <th><?php echo __t('Network.Leaving_date'); ?></th>
                    <th><?php echo __t('Distributor.Leaving_reason'); ?></th>
                    <th><?php echo __t('Network.Dd_active'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Status'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($garage_external_networks as $external_network) { ?>
                    <tr class="link-text">
                        <?php
                        foreach ($networks_image as $network) {
                            if ($network['Network']['id'] == $external_network['GarageNetwork']['network_id']) {
                        ?>
                                <td class="link-text">
                                    <div>
                                        <img title="<?php echo h($network['Network']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']); ?>" style="max-width: 125px">
                                    </div>
                                    <div>
                                        <?php
                                        if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                                            echo $this->Html->link(
                                                $network['Network']['name'],
                                                array(
                                                    'controller' => 'garages_networks',
                                                    'action' => 'edit',
                                                    $external_network['GarageNetwork']['id']
                                                ),
                                                array(
                                                    'class' => 'c-primary'
                                                )
                                            );
                                        } else {
                                            echo h($network['Network']['name']);
                                        }
                                        ?>
                                    </div>
                                </td>
                        <?php
                            }
                        }
                        ?>
                        <td class="ta-center">
                            <?php
                            foreach ($trading_groups as $trading_group) {
                                if ($trading_group['TradingGroup']['id'] == $external_network['GarageNetwork']['trading_group_id']) {
                                    echo h($trading_group['TradingGroup']['name']);
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha($external_network['GarageNetwork']['contract_received_date']); ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha($external_network['GarageNetwork']['contract_start_date']); ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha($external_network['GarageNetwork']['leaving_date']); ?>
                        </td>
                        <td>
                            <?php echo isset($external_network['GarageNetwork']['reason_leaving_id']) ? h($reasons_leaving[$external_network['GarageNetwork']['reason_leaving_id']]) : '' ?>
                        </td>
                        <td>
                            <?php echo $external_network['GarageNetwork']['dd_active'] == ConstantsBooleans::YES ? __t('General.Yes') : __t('General.No'); ?>
                        </td>
                        <td class="ta-center">
                            <?php
                            if ($external_network['GarageNetwork']['status'] == ConstantsNetworksStatus::UNSUBSCRIBE || $external_network['GarageNetwork']['status'] == ConstantsNetworksStatus::NOT_CONVERTED) {
                                $class = 'c-fallo';
                            } elseif ($external_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                                $class = 'c-exito';
                            } else {
                                $class = 'c-informacion';
                            }
                            ?>
                            <strong class="<?php echo $class; ?>"><?php echo h($networks_statuses[$external_network['GarageNetwork']['status']]); ?></strong>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    if (
        $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE) &&
        !$this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
    ) {
    ?>
        <div class="row p-top-1 btn-hide" hidden>
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
                        'name' => 'change_description input-disabled',
                        'rows' => 7,
                        'label' => false,
                        'disabled' => true,
                    )
                );
                ?>
            </div>
        </div>
    <?php } ?>
    <hr />
    <div class="row">
        <div class="aag-subtitle">
            <?php echo __t('Agreement.Agreements'); ?>
        </div>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('Agreement.Internal'); ?></th>
                    <th><?php echo __t('Agreement.Code'); ?></th>
                    <th><?php echo __t('Agreement.Fleet_reference'); ?></th>
                    <th><?php echo __t('General.Parent_acct'); ?></th>
                    <th><?php echo __t('Agreement.Garage_ref'); ?></th>
                    <th><?php echo __t('Agreement.Sent_date'); ?></th>
                    <th><?php echo __t('Agreement.Start_date'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($garage_internal_agreements as $internal_agreement) { ?>
                    <tr>
                        <?php if ($this->Acceso->rol() == ConstantsRoles::ADMIN) { ?>
                            <td>
                                <?php
                                if (isset($internal_agreement['GaAcuerdo']['id_agreement_alliance'])) {
                                    echo $this->Html->link(
                                        $internal_agreement['GaAcuerdo']['fleet_agreement'],
                                        array(
                                            'controller' => 'garages',
                                            'action' => 'edit_garage_agreement',
                                            $internal_agreement['GaAcuerdo']['id_agreement_alliance']
                                        ),
                                        array(
                                            'class' => 'c-primary'
                                        )
                                    );
                                } else {
                                    echo h($internal_agreement['GaAcuerdo']['fleet_agreement']);
                                }
                                ?>
                            </td>
                        <?php } ?>
                        <td>
                            <?php echo h($internal_agreement['GaAcuerdo']['agreement_code']); ?>
                        </td>
                        <td>
                            <?php echo h($internal_agreement['GaAcuerdo']['fleet_reference']); ?>
                        </td>
                        <td>
                            <?php echo h($internal_agreement['GaAcuerdo']['parent_acct']); ?>
                        </td>
                        <td>
                            <?php echo h($internal_agreement['GaAcuerdo']['garage_ref']); ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha($internal_agreement['GaAcuerdoTaller']['fecha_creacion']); ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha($internal_agreement['GaAcuerdo']['fecha_inicio_vigencia']); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="row p-top-1">
        <div class="columns medium-12 ta-right right-0 cnt-buttons-v2">
            <div class="f-right btn-hide" hidden>
                <?php if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_AGREEMENTS)) {
                    echo $this->Html->link(
                        __t('Agreement.Add_external_fleet'),
                        array(
                            'controller' => 'garages_agreements',
                            'action' => 'add',
                            $garage_id,
                            false
                        ),
                        array(
                            'escape' => false,
                            'class' => 'aag-button small green'
                        )
                    );
                } ?>
            </div>
        </div>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('Agreement.External'); ?></th>
                    <th><?php echo __t('Agreement.Sent_date'); ?></th>
                    <th><?php echo __t('Agreement.Received_date'); ?></th>
                    <th><?php echo __t('Agreement.Start_date'); ?></th>
                    <th><?php echo __t('Distributor.Leaving_reason'); ?></th>
                    <th class="ta-center"><?php echo __t('Agreement.Status'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($garage_external_agreements as $external_agreement) { ?>
                    <tr class="link-text">
                        <td>
                            <?php
                            if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::EDIT_AGREEMENTS)) {
                                echo $this->Html->link(
                                    $external_agreement['Agreement']['nombre'],
                                    array(
                                        'controller' => 'garages_agreements',
                                        'action' => 'edit',
                                        $external_agreement['GarageAgreement']['id']
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                            } else {
                                echo h($external_agreement['Agreement']['nombre']);
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha($external_agreement['GarageAgreement']['contract_sent_date']); ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha($external_agreement['GarageAgreement']['contract_received_date']); ?>
                        </td>
                        <td>
                            <?php echo Fecha::toFormatoVistaFecha($external_agreement['GarageAgreement']['contract_start_date']); ?>
                        </td>
                        <td>
                            <?php echo isset($external_agreement['GarageAgreement']['reason_leaving_id']) ? h($reasons_leaving[$external_agreement['GarageAgreement']['reason_leaving_id']]) : '' ?>
                        </td>
                        <td class="ta-center">
                            <?php
                            if ($external_agreement['GarageAgreement']['status'] == ConstantsNetworksStatus::UNSUBSCRIBE || $external_agreement['GarageAgreement']['status'] == ConstantsNetworksStatus::NOT_CONVERTED) {
                                $class = 'c-fallo';
                            } elseif ($external_agreement['GarageAgreement']['status'] == ConstantsNetworksStatus::LIVE) {
                                $class = 'c-exito';
                            } else {
                                $class = 'c-informacion';
                            }
                            ?>
                            <strong class="<?php echo $class; ?>"><?php echo h($networks_statuses[$external_agreement['GarageAgreement']['status']]); ?></strong>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <hr />
</div>
<?php echo $this->Form->end(); ?>