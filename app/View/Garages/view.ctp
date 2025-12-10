<?php
$config = CakeSession::read('Auth.User.Config');
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

if (!isset($status) || $status != ConstantsGarageStatusDe::POTENTIAL) {
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
                __t('General.View'),
            ));
            ?>
        </div>
        <div>
            <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
            <?php
            if (
                $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) ||
                $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE)
            ) {
                echo $this->Html->link(
                    __t('General.Edit'),
                    array(
                        'controller' => 'garages',
                        'action' => 'edit',
                        $garage['Garage']['id'],
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
            <?php echo h($garage['Garage']['name']); ?>
        </div>
        <div class="d-inline-block cnt-items-logo">
            <?php
            foreach ($garage_networks as $garage_network) {
                foreach ($networks as $network) {
                    if ($network['Network']['id'] == $garage_network['GarageNetwork']['network_id']) {
            ?>
                        <div class="d-inline-block ta-center end p-right-1 item-logo m-top-1">
                            <div>
                                <img class="logotipo" title="<?php echo h($network['Network']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']); ?>" />
                            </div>
                            <div>
                                <?php
                                if ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::UNSUBSCRIBE || $garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::NOT_CONVERTED) {
                                    $class = 'c-fallo';
                                } elseif ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                                    $class = 'c-exito';
                                } else {
                                    $class = 'c-informacion';
                                }
                                ?>
                                <strong class="<?php echo $class; ?>">
                                    <?php echo h($networks_statuses[$garage_network['GarageNetwork']['status']]); ?>
                                </strong>
                            </div>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>
        <div class="row">
            <div class="columns medium-9">
                <span><b class="fields_views"><?php echo __t('Garage.Creation_date') ?> </b> <?php echo ': ' . Fecha::toFormatoVista(h($garage['Garage']['creation_date'])); ?></span>
                <span class="p-left-1"><?php echo (''); ?></span>
                <span><b class="fields_views"><?php echo __t('Garage.Modification_date') ?> </b><?php echo ': ' . Fecha::toFormatoVista(h($garage['Garage']['modification_date'])); ?></span>
            </div>
            <br />
            <br />
            <ul class="aag-subtabs clear m-bottom-1">
                <?php if (!empty($garage_distributors)) { ?>
                    <li><a href="#distributor_anchor"><?php echo __t('Garage.Distributors') ?></a></li>
                <?php } ?>
                <?php if (!empty($garage_networks) && $garage_networks[0]['GarageNetwork']['network_id'] != -1) { ?>
                    <li><a href="#network_anchor"><?php echo __t('Network.Networks') ?></a></li>
                <?php } ?>
                <?php if (!empty($garage_contacts_general_branch_manager && $get_list_config_tabs[ConstantsTabs::GENERAL_BRANCH_MANAGER] == ConstantsBooleans::ACTIVE)) { ?>
                    <li><a href="#general_branch_manager_anchor"><?php echo __t('Contact.General_branch_manager') ?></a></li>
                <?php } ?>
                <?php if (!empty($garage_contacts_bdm && $get_list_config_tabs[ConstantsTabs::BDM] == ConstantsBooleans::ACTIVE)) { ?>
                    <li><a href="#bdm_anchor"><?php echo __t('Contact.BDM') ?></a></li>
                <?php } ?>
                <?php if (!empty($garage_contacts_staff)) { ?>
                    <li><a href="#staff_anchor"><?php echo __t('Garage.Staff') ?></a></li>
                <?php } ?>
                <?php if (!empty($garage_employees && $get_list_config_tabs[ConstantsTabs::EMPLOYEES] == ConstantsBooleans::ACTIVE)) { ?>
                    <li><a href="#employee_anchor"><?php echo __t('Garage.Employees') ?></a></li>
                <?php } ?>
                <li><a href="#location_anchor"><?php echo __t('Garage.Location') ?></a></li>
                <li><a href="#opening_times_anchor"><?php echo __t('Garage.Opening_times') ?></a></li>
                <?php if (!empty($garage_activities)) { ?>
                    <li><a href="#activities_anchor"><?php echo __t('Garage.Activities') ?></a></li>
                <?php } ?>
                <?php if (count($images) > 0) { ?>
                    <li><a href="#images_anchor"><?php echo __t('Garage.Images') ?></a></li>
                <?php } ?>
                <?php if (count($garage_services) > 0) { ?>
                    <li><a href="#vehicle_services_anchor"><?php echo __t('Garage.Vehicle_services') ?></a></li>
                <?php } ?>
                <?php if (count($garage_vehicle_types) > 0) { ?>
                    <li><a href="#vehicle_types_anchor"><?php echo __t('Garage.Vehicle_types') ?></a></li>
                <?php } ?>
                <?php if (count($garage_vehicles) > 0) { ?>
                    <li><a href="#vehicle_brands_specialist_anchor"><?php echo __t('Garage.Vehicle_brands_specialist') ?></a></li>
                <?php } ?>
                <?php if ($config[ConstantsConfig::PARTS_BRANDS]) { ?>
                    <?php if (!empty($garages_parts_brands)) { ?>
                        <li><a href="#parts_brands"><?php echo __t('Garage.Parts_brands_allowed') ?></a></li>
                    <?php } ?>
                <?php } ?>
                <li><a href="#miscellaneous_anchor"><?php echo __t('Garage.Miscellaneous') ?></a></li>
                <?php if ($config[ConstantsConfig::EQUIPMENT]) { ?>
                    <?php if (!empty($garage_equipments)) { ?>
                        <li><a href="#equipment_anchor"><?php echo __t('Equipment.Equipment') ?></a></li>
                    <?php } ?>
                <?php } ?>
                <?php if (!empty($garage_software)) { ?>
                    <li><a href="#software_anchor"><?php echo __t('Garage.Software') ?></a></li>
                <?php } ?>
                <?php if (!empty($garage_websites) || $config[ConstantsConfig::LEAD_SOURCE] || $config[ConstantsConfig::INTEREST] || $config[ConstantsConfig::MARKETING_EMAIL]) { ?>
                    <li><a href="#marketing_anchor"><?php echo __t('Garage.Marketing') ?></a></li>
                <?php } ?>
                <?php if (!empty($garage_comments)) { ?>
                    <li><a href="#comments_anchor"><?php echo __t('Garage.Comments') ?></a></li>
                <?php } ?>
                <?php if (!empty($garage_comments)) { ?>
                    <li><a href="#log_changes_anchor"><?php echo __t('Garage.Log_changes') ?></a></li>
                <?php } ?>
            </ul>
        </div>
        <div class="row p-bottom-1">
            <div class="columns medium-8 p-0">
                <div class="cnt-form-inputs">
                    <div>
                        <strong><?php echo __t('Garage.Business_name') ?>: </strong>
                        <br>
                        <div class="b-bottom-1 height_input"><?php echo h($garage['Garage']['business_name']); ?></div>
                    </div>
                    <div>
                        <strong><?php echo __t('Garage.Name') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input"><?php echo h($garage['Garage']['name']); ?></div>
                    </div>
                    <?php
                    //If it's Benelux there is not garage g_number, but if the role is Super Admin, garages from both regions can be present at the same time
                    if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) { ?>
                        <div>
                            <strong><?php echo __t('Garage.G_number') ?>: </strong>
                            <br>
                            <div class="b-bottom-1 height_input">
                                <?php echo h($garage['Garage']['g_number_id']); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div>
                        <strong><?php echo __t('Garage.Ref_code') ?>: </strong>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <?php echo h($garage['Garage']['ref_code']); ?>
                        </div>
                    </div>
                    <?php if ($config[ConstantsConfig::SIRET]) { ?>
                        <div>
                            <strong><?php echo __t('Garage.Siret') ?>: </strong>
                            <br>
                            <div class="b-bottom-1 height_input">
                                <?php echo substr($garage['Garage']['siret'], 0, 3) . ' ' . substr($garage['Garage']['siret'], 3, 3) . ' ' . substr($garage['Garage']['siret'], 6, 3) . ' ' . substr($garage['Garage']['siret'], 9); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div>
                        <strong><?php echo __t('Garage.Status') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <?php
                            echo h($garage_statuses[$garage['Garage']['status']]);
                            ?>
                        </div>
                    </div>
                    <div class="clear-column">
                        <strong><?php echo __t('Garage.Phone') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input"><?php echo h($garage['Garage']['phone']); ?> </div>
                    </div>
                    <div>
                        <strong><?php echo __t('Garage.Mobile') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input"><?php echo h($garage['Garage']['mobile']); ?> </div>
                    </div>
                    <div>
                        <strong class="c-primary"><?php echo __t('Fax') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input"><?php echo h($garage['Garage']['fax']); ?> </div>
                    </div>
                    <div class="clear-column">
                        <strong class="c-primary"><?php echo __t('Garage.24h_phone') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input"><?php echo h($garage['Garage']['service_24h_phone']); ?>
                        </div>
                    </div>
                    <div>
                        <strong class="c-primary"><?php echo __t('Garage.Email') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <?php echo $this->Html->link(h($garage['Garage']['email']), 'mailto:' . h($garage['Garage']['email'])); ?>
                        </div>
                    </div>
                    <div>
                        <strong class="c-primary"><?php echo __t('Garage.Web') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <a target="_blank" title="<?php echo h($garage['Garage']['web']); ?>" href="<?php echo h($garage['Garage']['web']); ?>"><?php echo h($garage['Garage']['web']); ?></a></li>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="ta-center p-1" style="height: 214px;">
                    <?php
                    if (!isset($garage_principal_image['GarageImage']) || $garage_principal_image['GarageImage']['file'] == "") {
                    ?> <img src="<?php echo '/img/default.png' ?>" style="max-height: 210px;" /> <?php
                                                                                                } else {
                                                                                                    echo $this->Html->image(
                                                                                                        Router::url(
                                                                                                            array(
                                                                                                                'controller' => 'garages_images',
                                                                                                                'action' => 'download_file',
                                                                                                                $garage_principal_image['GarageImage']['id'],
                                                                                                            )
                                                                                                        ),
                                                                                                        array(
                                                                                                            'alt' => __t('Garage.Picture_not_loaded'),
                                                                                                            'style' => "max-height: 210px;"
                                                                                                        )
                                                                                                    );
                                                                                                }
                                                                                                    ?>
                </div>
            </div>
        </div>
        <div class="cnt-form-inputs">
            <div>
                <strong><?php echo __t('Garage.Visit_frequency') ?>:</strong>
                <br>
                <div class="b-bottom-1 height_input">
                    <?php echo $garage['Garage']['visit_frequency'] ? h($visit_frequency[$garage['Garage']['visit_frequency']]) : ''; ?>
                </div>
            </div>
            <div>
                <strong><?php echo __t('Garage.Visit_days') ?>:</strong>
                <br>
                <div class="b-bottom-1 height_input">
                    <?php if ($garage['Garage']['visit_monday']) { ?>
                        <?php echo __t('Garage.Monday'); ?>
                    <?php } ?>
                    <?php if ($garage['Garage']['visit_tuesday']) { ?>
                        <?php echo __t('Garage.Tuesday'); ?>
                    <?php } ?>
                    <?php if ($garage['Garage']['visit_wednesday']) { ?>
                        <?php echo __t('Garage.Wednesday'); ?>
                    <?php } ?>
                    <?php if ($garage['Garage']['visit_thursday']) { ?>
                        <?php echo __t('Garage.Thursday'); ?>
                    <?php } ?>
                    <?php if ($garage['Garage']['visit_friday']) { ?>
                        <?php echo __t('Garage.Friday'); ?>
                    <?php } ?>
                </div>
            </div>
            <div>
                <strong><?php echo __t('Garage.Last_visit') ?>:</strong>
                <br>
                <div class="b-bottom-1 height_input">
                    <?php echo Fecha::toFormatoVista($garage['Garage']['last_visit']); ?>
                </div>
            </div>
        </div>
        <div class="cnt-form-inputs m-top-1">
            <?php if ($config[ConstantsConfig::INSURANCE_AGREEMENT]) { ?>
                <div>
                    <strong><?php echo __t('Garage.Insurance_agreement') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo $garage['Garage']['insurance_agreement_id'] ? h($insurance_agreements[$garage['Garage']['insurance_agreement_id']]) : ''; ?>
                    </div>
                </div>
            <?php } ?>
            <?php if ($config[ConstantsConfig::AFFILIATION_ASSEMBLY]) { ?>
                <div>
                    <strong><?php echo __t('Garage.Affiliation_assembly') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo $garage['Garage']['affiliation_assembly'] ? __t('General.Yes') : __t('General.No'); ?>
                    </div>
                </div>
            <?php } ?>
            <?php if ($config[ConstantsConfig::DOCUMENTS_LEGAL]) { ?>
                <div>
                    <strong><?php echo __t('Garage.Documents_legal') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo $garage['Garage']['documents_legal'] ? __t('General.Yes') : __t('General.No'); ?>
                    </div>
                </div>
            <?php } ?>
            <?php if ($config[ConstantsConfig::DIESEL_LIABILITY]) { ?>
                <div>
                    <strong><?php echo __t('Garage.Diesel_liability') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo $garage['Garage']['diesel_liability'] ? __t('General.Yes') : __t('General.No'); ?>
                    </div>
                </div>
            <?php } ?>
            <div>
                <span class="fields_views wrd_label" style="margin-left:0px;"><?php echo __t('Distributor.Workshop_activities'); ?></span>
                <div class="b-bottom-1 height_input">
                    <?php foreach ($workshop_activities_garage as $workshop_activity_garage) {
                        echo h($workshop_activities_list[$workshop_activity_garage]) . ', ';
                    } ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="flex ai-center gap-1 p-top-1">
                <div class="anchor_nav aag-subtitle" id="aditional_anchor"> <?php echo __t('Garage.Aditional_info') ?></div>
                <?php
                if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                    echo $this->Html->link(
                        '',
                        array(
                            'action' => 'add_aditional_info_and_other_details_garage',
                            $garage['Garage']['id']
                        ),
                        array(
                            'class' => 'ion-arrow-right-c c-primary',
                            'style' => 'z-index: 4; position: relative;',
                        )
                    );
                }
                ?>
            </div>
        </div>

        <?php if (!empty($garage_distributors)) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="distributor_anchor"> <?php echo __t('Garage.Distributors') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'action' => 'add_dis_and_net_garage',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    }
                    ?>
                </div>
                <div class="o-auto p-bottom-1">
                    <table class="table-tracking table-responsive z-index-priority">
                        <thead>
                            <tr>
                                <th><?php echo __t('Network.Distributor'); ?></th>
                                <th><?php echo __t('Distributor.MAMID'); ?></th>
                                <th><?php echo __t('Distributor.Account_number'); ?></th>
                                <th class="ta-center"><?php echo __t('Distributor.Principal'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($garage_distributors as $garage_distributor) {
                            ?>
                                <tr>
                                    <td>
                                        <?php echo h($garage_distributor['Distributor']['name']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($garage_distributor['Distributor']['MAMID']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($garage_distributor['Distributor']['account_number']); ?>
                                    </td>
                                    <?php
                                    if ($garage_distributor['GarageDistributor']['principal'] == ConstantsBooleans::ACTIVE) {
                                        $class = 'c-exito';
                                        $text = __t('General.Yes');
                                    } else {
                                        $class = 'c-fallo';
                                        $text = __t('General.No');
                                    }
                                    ?>
                                    <td class="ta-center <?php echo $class; ?>">
                                        <strong class="<?php echo $class; ?>"><?php echo h($text); ?></strong>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($garage_networks) && $garage_networks[0]['GarageNetwork']['network_id'] != -1) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="network_anchor"> <?php echo __t('Network.Networks') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'action' => 'add_dis_and_net_garage',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    }
                    ?>
                </div>
                <div class="o-auto">
                    <table class="table-tracking table-responsive z-index-priority">
                        <thead>
                            <tr>
                                <th><?php echo __t('Network.Internal_network'); ?></th>
                                <th class="ta-center"><?php echo __t('Garage.Trading_group'); ?></th>
                                <th><?php echo __t('Garage.Contract'); ?></th>
                                <th><?php echo __t('Garage.Sent_date'); ?></th>
                                <th><?php echo __t('Garage.Received_date'); ?></th>
                                <th><?php echo __t('Garage.Start_date'); ?></th>
                                <th><?php echo __t('Garage.End_date'); ?></th>
                                <th class="ta-center"><?php echo __t('Garage.Status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($garage_internal_networks as $garage_network) { ?>
                                <?php if ($garage_network['GarageNetwork']['network_id'] != -1) { ?>
                                    <tr>
                                        <?php foreach ($networks as $network) {
                                            if ($network['Network']['id'] == $garage_network['GarageNetwork']['network_id']) { ?>
                                                <td>
                                                    <?php echo $this->Html->link(
                                                        $network['Network']['name'],
                                                        array(
                                                            'controller' => 'garages_networks',
                                                            'action' => 'view',
                                                            $garage_network['GarageNetwork']['id']
                                                        ),
                                                        array(
                                                            'class' => 'c-primary'
                                                        )
                                                    ); ?>
                                                </td>
                                        <?php }
                                        } ?>
                                        <td class="ta-center">
                                            <?php foreach ($trading_groups as $trading_group) {
                                                if ($trading_group['TradingGroup']['id'] == $garage_network['GarageNetwork']['trading_group_id']) {
                                                    echo h($trading_group['TradingGroup']['name']);
                                                }
                                            } ?>
                                        </td>
                                        <td>
                                            <?php if ($garage_network['GarageNetwork']['network_contract_type_id']) {
                                                echo h($networks_contracts[$garage_network['GarageNetwork']['network_contract_type_id']]);
                                            } ?>
                                        </td>
                                        <td>
                                            <?php echo Fecha::toFormatoVista($garage_network['GarageNetwork']['contract_sent_date']); ?>
                                        </td>
                                        <td>
                                            <?php echo Fecha::toFormatoVista($garage_network['GarageNetwork']['contract_received_date']); ?>
                                        </td>
                                        <td>
                                            <?php echo Fecha::toFormatoVista($garage_network['GarageNetwork']['contract_start_date']); ?>
                                        </td>
                                        <td>
                                            <?php echo Fecha::toFormatoVista($garage_network['GarageNetwork']['contract_end_date']); ?>
                                        </td>
                                        <td class="ta-center">
                                            <?php
                                            if ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::UNSUBSCRIBE || $garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::NOT_CONVERTED) {
                                                $class = 'c-fallo';
                                            } elseif ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                                                $class = 'c-exito';
                                            } else {
                                                $class = 'c-informacion';
                                            }
                                            ?>
                                            <strong class="<?php echo $class; ?>"><?php echo h($networks_statuses[$garage_network['GarageNetwork']['status']]); ?></strong>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="o-auto">
                    <table class="table-tracking table-responsive z-index-priority">
                        <thead>
                            <tr>
                                <th><?php echo __t('Network.External_network'); ?></th>
                                <th class="ta-center"><?php echo __t('Garage.Trading_group'); ?></th>
                                <th><?php echo __t('Garage.Contract'); ?></th>
                                <th><?php echo __t('Garage.Sent_date'); ?></th>
                                <th><?php echo __t('Garage.Received_date'); ?></th>
                                <th><?php echo __t('Garage.Start_date'); ?></th>
                                <th><?php echo __t('Garage.End_date'); ?></th>
                                <th class="ta-center"><?php echo __t('Garage.Status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($garage_external_networks as $garage_network) { ?>
                                <?php if ($garage_network['GarageNetwork']['network_id'] != -1) { ?>
                                    <tr>
                                        <?php foreach ($networks as $network) {
                                            if ($network['Network']['id'] == $garage_network['GarageNetwork']['network_id']) { ?>
                                                <td>
                                                    <?php echo $this->Html->link(
                                                        $network['Network']['name'],
                                                        array(
                                                            'controller' => 'garages_networks',
                                                            'action' => 'view',
                                                            $garage_network['GarageNetwork']['id']
                                                        ),
                                                        array(
                                                            'class' => 'c-primary'
                                                        )
                                                    ); ?>
                                                </td>
                                        <?php }
                                        } ?>
                                        <td class="ta-center">
                                            <?php foreach ($trading_groups as $trading_group) {
                                                if ($trading_group['TradingGroup']['id'] == $garage_network['GarageNetwork']['trading_group_id']) {
                                                    echo h($trading_group['TradingGroup']['name']);
                                                }
                                            } ?>
                                        </td>
                                        <td>
                                            <?php if ($garage_network['GarageNetwork']['network_contract_type_id']) {
                                                echo h($networks_contracts[$garage_network['GarageNetwork']['network_contract_type_id']]);
                                            } ?>
                                        </td>
                                        <td>
                                            <?php echo Fecha::toFormatoVista($garage_network['GarageNetwork']['contract_sent_date']); ?>
                                        </td>
                                        <td>
                                            <?php echo Fecha::toFormatoVista($garage_network['GarageNetwork']['contract_received_date']); ?>
                                        </td>
                                        <td>
                                            <?php echo Fecha::toFormatoVista($garage_network['GarageNetwork']['contract_start_date']); ?>
                                        </td>
                                        <td>
                                            <?php echo Fecha::toFormatoVista($garage_network['GarageNetwork']['contract_end_date']); ?>
                                        </td>
                                        <td class="ta-center">
                                            <?php
                                            if ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::UNSUBSCRIBE || $garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::NOT_CONVERTED) {
                                                $class = 'c-fallo';
                                            } elseif ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                                                $class = 'c-exito';
                                            } else {
                                                $class = 'c-informacion';
                                            }
                                            ?>
                                            <strong class="<?php echo $class; ?>"><?php echo h($networks_statuses[$garage_network['GarageNetwork']['status']]); ?></strong>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($garage_internal_agreements) || !empty($garage_external_agreements)) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="network_anchor"> <?php echo __t('Agreement.Agreements') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'action' => 'add_dis_and_net_garage',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    }
                    ?>
                </div>
                <div class="o-auto">
                    <table class="table-tracking table-responsive z-index-priority">
                        <thead>
                            <tr>
                                <th><?php echo __t('Agreement.Internal'); ?></th>
                                <th><?php echo __t('General.Cod'); ?></th>
                                <th><?php echo __t('General.Fleet_reference'); ?></th>
                                <th><?php echo __t('General.Parent_acct'); ?></th>
                                <th><?php echo __t('General.Garage_ref'); ?></th>
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
                <div class="o-auto">
                    <table class="table-tracking table-responsive z-index-priority">
                        <thead>
                            <tr>
                                <th><?php echo __t('Agreement.External'); ?></th>
                                <th><?php echo __t('Agreement.Sent_date'); ?></th>
                                <th><?php echo __t('Agreement.Received_date'); ?></th>
                                <th><?php echo __t('Agreement.Start_date'); ?></th>
                                <th class="ta-center"><?php echo __t('Agreement.Status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($garage_external_agreements as $external_agreement) { ?>
                                <tr>
                                    <td>
                                        <?php
                                        if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::EDIT_AGREEMENTS)) {
                                            echo $this->Html->link(
                                                $external_agreement['Agreement']['nombre'],
                                                array(
                                                    'controller' => 'garages_agreements',
                                                    'action' => 'view',
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
            </div>
        <?php } ?>

        <?php if (!empty($garage_contacts_general_branch_manager)) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="general_branch_manager_anchor">
                        <?php echo __t('Contact.General_branch_manager'); ?>
                    </div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'action' => 'add_contacts_general_branch_manager',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    }
                    ?>
                </div>
                <div class="o-auto">
                    <table class="table-tracking table-responsive z-index-priority tabla-limitada">
                        <thead>
                            <tr>
                                <th class="first-name"><?php echo __t('Distributor.First_name'); ?></th>
                                <th class="last-name"><?php echo __t('Distributor.Last_name'); ?></th>
                                <th class="position"><?php echo __t('Contact.Position'); ?></th>
                                <th class="phone"><?php echo __t('Distributor.Phone'); ?></th>
                                <th class="mobile-phone"><?php echo __t('Contact.Mobile_phone'); ?></th>
                                <th><?php echo __t('Garage.Email'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($garage_contacts_general_branch_manager as $contact_general_branch_manager) {
                            ?>
                                <tr>
                                    <td>
                                        <?php echo h($contact_general_branch_manager['Contact']['first_name']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($contact_general_branch_manager['Contact']['last_name']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($positions[$contact_general_branch_manager['Contact']['position_id']]); ?>
                                    </td>
                                    <td>
                                        <?php echo h($contact_general_branch_manager['Contact']['phone']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($contact_general_branch_manager['Contact']['mobile_phone']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($contact_general_branch_manager['Contact']['email']); ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                if (count($garage_contacts_general_branch_manager) == 10) {
                    echo $this->Html->link(
                        __t('Garage.View_more'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_contacts_general_branch_manager',
                            $garage['Garage']['id']
                        ),
                        array(
                            'class' => 'button-general tres ta-center btn-full-width',
                        )
                    );
                }
                ?>
            </div>
        <?php } ?>

        <?php if (!empty($garage_contacts_bdm)) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="bdm_anchor"><?php echo __t('Contact.BDM') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'action' => 'add_contacts_bdm',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    }
                    ?>
                </div>
                <div class="o-auto">
                    <table class="table-tracking table-responsive z-index-priority tabla-limitada">
                        <thead>
                            <tr>
                                <th class="first-name"><?php echo __t('Distributor.First_name'); ?></th>
                                <th class="last-name"><?php echo __t('Distributor.Last_name'); ?></th>
                                <th class="position"><?php echo __t('Contact.Position'); ?></th>
                                <th class="phone"><?php echo __t('Distributor.Phone'); ?></th>
                                <th class="mobile-phone"><?php echo __t('Contact.Mobile_phone'); ?></th>
                                <th><?php echo __t('Garage.Email'); ?></th>
                                <th><?php echo __t('Network.Garage_networks'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($garage_contacts_bdm as $contact_bdm) {
                            ?>
                                <tr>
                                    <td>
                                        <?php echo h($contact_bdm['Contact']['first_name']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($contact_bdm['Contact']['last_name']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($positions[$contact_bdm['Contact']['position_id']]); ?>
                                    </td>
                                    <td>
                                        <?php echo h($contact_bdm['Contact']['phone']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($contact_bdm['Contact']['mobile_phone']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($contact_bdm['Contact']['email']); ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($contact_bdm['Network']) {
                                            foreach ($contact_bdm['Network'] as $network) { ?>
                                                <img class="logotipo2" title="<?php echo h($network['Network']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']); ?>" />
                                        <?php }
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                if (count($garage_contacts_bdm) == 10) {
                    echo $this->Html->link(
                        __t('Garage.View_more'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_contacts_bdm',
                            $garage['Garage']['id']
                        ),
                        array(
                            'class' => 'button-general tres ta-center btn-full-width',
                        )
                    );
                }
                ?>
            </div>
        <?php } ?>

        <?php if (!empty($garage_contacts_staff)) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="staff_anchor"><?php echo __t('Garage.Staff') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'action' => 'add_contacts_staff',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    } ?>
                </div>
                <div class="o-auto">
                    <table class="table-tracking table-responsive z-index-priority tabla-limitada">
                        <thead>
                            <tr>
                                <th class="first-name"><?php echo __t('Distributor.First_name'); ?></th>
                                <th class="last-name"><?php echo __t('Distributor.Last_name'); ?></th>
                                <th class="position"><?php echo __t('Contact.Position'); ?></th>
                                <th class="phone"><?php echo __t('Distributor.Phone'); ?></th>
                                <th class="mobile-phone"><?php echo __t('Contact.Mobile_phone'); ?></th>
                                <th><?php echo __t('Garage.Email'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($garage_contacts_staff as $contact_staff) { ?>
                                <tr>
                                    <td>
                                        <?php echo h($contact_staff['Contact']['first_name']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($contact_staff['Contact']['last_name']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($positions[$contact_staff['Contact']['position_id']]); ?>
                                    </td>
                                    <td>
                                        <?php echo h($contact_staff['Contact']['phone']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($contact_staff['Contact']['mobile_phone']); ?>
                                    </td>
                                    <td>
                                        <?php echo h($contact_staff['Contact']['email']); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php
                if (count($garage_contacts_staff) == 10) {
                    echo $this->Html->link(
                        __t('Garage.View_more'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_contacts_staff',
                            $garage['Garage']['id']
                        ),
                        array(
                            'class' => 'button-general tres ta-center btn-full-width',
                        )
                    );
                }
                ?>
            </div>
        <?php } ?>

        <?php if (!empty($garage_employees)) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="employee_anchor"><?php echo __t('Garage.Employees') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'action' => 'add_employee_garage',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    }
                    ?>
                </div>
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
                                    <td>
                                        <?php echo $this->Html->link(
                                            $employee_types[$garage_employee['GarageEmployee']['employee_type_id']],
                                            array(
                                                'controller' => 'garages_employees',
                                                'action' => 'edit',
                                                $garage_employee['GarageEmployee']['id']
                                            ),
                                            array(
                                                'class' => 'c-primary'
                                            )
                                        ); ?>
                                    </td>
                                    <td class="ta-center">
                                        <?php echo h($garage_employee['GarageEmployee']['number']); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>

        <div class="row">
            <div class="flex ai-center gap-1 p-top-1">
                <div class="anchor_nav aag-subtitle" id="location_anchor"> <?php echo __t('Garage.Location') ?></div>
                <?php
                if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                    echo $this->Html->link(
                        '',
                        array(
                            'action' => 'edit',
                            $garage['Garage']['id']
                        ),
                        array(
                            'class' => 'ion-arrow-right-c c-primary',
                            'style' => 'z-index: 4; position: relative;',
                        )
                    );
                }
                ?>
            </div>
            <div class="cnt-form-inputs">
                <div class="two-columns">
                    <strong><?php echo __t('Garage.Address') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php
                        echo h($garage['Garage']['address1']);
                        if (!empty($garage['Garage']['address2'])) {
                            echo ", " . h($garage['Garage']['address2']);
                        }
                        if (!empty($garage['Garage']['address3'])) {
                            echo ", " . h($garage['Garage']['address3']);
                        }
                        if (!empty($garage['Garage']['address4'])) {
                            echo ", " . h($garage['Garage']['address4']);
                        }

                        ?>
                    </div>
                </div>
                <div class="clear-column">
                    <strong><?php echo __t('Garage.Postcode') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo h($garage['Garage']['postcode']); ?>
                    </div>
                </div>
                <?php if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) { ?>
                    <div>
                        <strong><?php echo __t('Garage.Country') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <?php
                            if (isset($country['Country']['name'])) {
                                echo h($country['Country']['name']);
                            }
                            ?>
                        </div>
                    </div>
                    <div>
                        <strong><?php echo __t('Garage.County') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <?php
                            if (isset($province_list[$garage['Garage']['province_id']])) {
                                echo h($province_list[$garage['Garage']['province_id']]);
                            } else {
                                echo "";
                            } ?>
                        </div>
                    </div>
                <?php } ?>
                <div>
                    <strong><?php echo __t('Garage.Town') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo h($garage['Garage']['town']); ?>
                    </div>
                </div>
                <div>
                    <?php
                    if (!empty($garage['Garage']['latitude']) && !empty($garage['Garage']['longitude'])) {
                    ?>
                        <div id="map-single" class="contenedor-mapa" style="height: 150px;" data-editable="<?php echo ConstantsBooleans::NO; ?>" data-latitude="<?php echo $garage['Garage']['latitude'] ?>" data-longitude="<?php echo $garage['Garage']['longitude'] ?>">
                        </div>
                        <div class="alliance_bar"></div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="flex ai-center gap-1 p-top-1">
                <div class="anchor_nav aag-subtitle" id="opening_times_anchor"> <?php echo __t('Garage.Opening_times') ?></div>
                <?php
                if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                    echo $this->Html->link(
                        '',
                        array(
                            'action' => 'add_opening_garage',
                            $garage['Garage']['id']
                        ),
                        array(
                            'class' => 'ion-arrow-right-c c-primary',
                            'style' => 'z-index: 4; position: relative;',
                        )
                    );
                }
                ?>
            </div>
            <fieldset class="fieldset-garage-list cnt-estilo-nuevo z-index-priority background-color-primary" style="font-size: small;">
                <div class="cnt-dias-semana">
                    <div class="dia-semana">
                        <strong><?php echo __t('Garage.Monday') ?>:</strong>
                        <br>
                        <span class="b-bottom-1 height_input">
                            <?php
                            if (empty($garage['Garage']['monday_open_1']) && empty($garage['Garage']['monday_closed_1']) && empty($garage['Garage']['monday_open_2']) && empty($garage['Garage']['monday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['monday_open_1'])) {
                                echo h($garage['Garage']['monday_open_1']) . ' - ' . h($garage['Garage']['monday_closed_1']);
                            }
                            if (!empty($garage['Garage']['monday_open_2'])) {
                                echo '<br/>';
                                echo h($garage['Garage']['monday_open_2']) . ' - ' . h($garage['Garage']['monday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="dia-semana">
                        <strong><?php echo __t('Garage.Tuesday') ?>:</strong>
                        <br>
                        <span class="b-bottom-1 height_input">
                            <?php
                            if (empty($garage['Garage']['tuesday_open_1']) && empty($garage['Garage']['tuesday_open_2']) && empty($garage['Garage']['tuesday_closed_1']) && empty($garage['Garage']['tuesday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['tuesday_open_1'])) {
                                echo h($garage['Garage']['tuesday_open_1']) . ' - ' . h($garage['Garage']['tuesday_closed_1']);
                            }
                            if (!empty($garage['Garage']['tuesday_open_2'])) {
                                echo '<br/>';
                                echo h($garage['Garage']['tuesday_open_2']) . ' - ' . h($garage['Garage']['tuesday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="dia-semana">
                        <strong><?php echo __t('Garage.Wednesday') ?>:</strong>
                        <br>
                        <span class="b-bottom-1 height_input">
                            <?php
                            if (empty($garage['Garage']['wednesday_open_1']) && empty($garage['Garage']['wednesday_open_2']) && empty($garage['Garage']['wednesday_closed_1']) && empty($garage['Garage']['wednesday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['wednesday_open_1'])) {
                                echo h($garage['Garage']['wednesday_open_1']) . ' - ' . h($garage['Garage']['wednesday_closed_1']);
                            }
                            if (!empty($garage['Garage']['wednesday_open_2'])) {
                                echo '<br/>';
                                echo h($garage['Garage']['wednesday_open_2']) . ' - ' . h($garage['Garage']['wednesday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="dia-semana">
                        <strong><?php echo __t('Garage.Thursday') ?>:</strong>
                        <br>
                        <span class="b-bottom-1 height_input">
                            <?php
                            if (empty($garage['Garage']['thursday_open_1']) && empty($garage['Garage']['thursday_open_2']) && empty($garage['Garage']['thursday_closed_1']) && empty($garage['Garage']['thursday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['thursday_open_1'])) {
                                echo h($garage['Garage']['thursday_open_1']) . ' - ' . h($garage['Garage']['thursday_closed_1']);
                            }
                            if (!empty($garage['Garage']['thursday_open_2'])) {
                                echo '<br/>';
                                echo h($garage['Garage']['thursday_open_2']) . ' - ' . h($garage['Garage']['thursday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="dia-semana">
                        <strong><?php echo __t('Garage.Friday') ?>:</strong>
                        <br>
                        <span class="b-bottom-1 height_input">
                            <?php
                            if (empty($garage['Garage']['friday_open_1']) && empty($garage['Garage']['friday_open_2']) && empty($garage['Garage']['friday_closed_1']) && empty($garage['Garage']['friday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['friday_open_1'])) {
                                echo h($garage['Garage']['friday_open_1']) . ' - ' . h($garage['Garage']['friday_closed_1']);
                            }
                            if (!empty($garage['Garage']['friday_open_2'])) {
                                echo '<br/>';
                                echo h($garage['Garage']['friday_open_2']) . ' - ' . h($garage['Garage']['friday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="dia-semana">
                        <strong><?php echo __t('Garage.Saturday') ?>:</strong>
                        <br>
                        <span class="b-bottom-1 height_input">
                            <?php
                            if (empty($garage['Garage']['saturday_open_1']) && empty($garage['Garage']['saturday_open_2']) && empty($garage['Garage']['saturday_closed_1']) && empty($garage['Garage']['saturday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['saturday_open_1'])) {
                                echo h($garage['Garage']['saturday_open_1']) . ' - ' . h($garage['Garage']['saturday_closed_1']);
                            }
                            if (!empty($garage['Garage']['saturday_open_2'])) {
                                echo '<br/>';
                                echo h($garage['Garage']['saturday_open_2']) . ' - ' . h($garage['Garage']['saturday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="dia-semana">
                        <strong><?php echo __t('Garage.Sunday') ?>:</strong>
                        <br>
                        <span class="b-bottom-1 height_input">
                            <?php
                            if (empty($garage['Garage']['sunday_open_1']) && empty($garage['Garage']['sunday_open_2']) && empty($garage['Garage']['sunday_closed_1']) && empty($garage['Garage']['sunday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['sunday_open_1'])) {
                                echo h($garage['Garage']['sunday_open_1']) . ' - ' . h($garage['Garage']['sunday_closed_1']);
                            }
                            if (!empty($garage['Garage']['sunday_open_2'])) {
                                echo '<br/>';
                                echo h($garage['Garage']['sunday_open_2']) . ' - ' . h($garage['Garage']['sunday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </fieldset>
        </div>

        <?php if (!empty($garage_activities)) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="activities_anchor"> <?php echo __t('Garage.Activities') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'controller' => 'garages',
                                'action' => 'add_activities_and_services_garage',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    }
                    ?>
                </div>
                <div class="o-auto">
                    <table class="table-tracking">
                        <thead>
                            <tr>
                                <th><?php echo __t('Garage.Activity'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($garage_activities as $garage_activity) {
                            ?>
                                <tr>
                                    <td>
                                        <?php echo strtoupper(h($customers_activities[$garage_activity['GarageCustomerActivity']['customer_activity_id']])); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>

        <?php if (count($images) > 0) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="images_anchor"> <?php echo __t('Garage.Images') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'controller' => 'garages',
                                'action' => 'add_marketing_and_image_garage',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    } ?>
                </div>
                <fieldset class="p-0 fieldset-garage-list cnt-estilo-nuevo">
                    <?php
                    if (count($images) > 0) {
                        echo $this->element('../GaragesImages/Elements/galleryScroll');
                    }
                    ?>
                </fieldset>
            </div>
        <?php } ?>

        <?php if (count($garage_services) > 0) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="vehicle_services_anchor"> <?php echo __t('Garage.Vehicle_services') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'action' => 'add_activities_and_services_garage',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    }
                    ?>
                </div>
                <div class="d-inline-block cont-services w-100p">
                    <?php
                    foreach ($services as $service) {
                        foreach ($garage_services as $garage_service) {
                            if (isset($garage_service)) {
                                if ($service['Service']['id'] == $garage_service['GarageService']['service_id']) {
                    ?>
                                    <div class="medium-2 columns end">
                                        <label class="cursor-default">
                                            <span>
                                                <?php echo $this->Html->image(
                                                    FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . $service['Service']['url'])
                                                ) . " " . h($service['Service']['name_' . __l()]);
                                                ?>
                                            </span>
                                        </label>
                                    </div>
                    <?php
                                }
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        <?php } ?>

        <?php if (count($garage_vehicle_types) > 0) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="vehicle_types_anchor"> <?php echo __t('Garage.Vehicle_types') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'action' => 'add_activities_and_services_garage',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    }
                    ?>
                </div>
                <div class="d-inline-block cont-services w-100p">
                    <?php
                    foreach ($vehicle_types as $vehicle_type) {
                        foreach ($garage_vehicle_types as $garage_vehicle_type) {
                            if (isset($garage_vehicle_type)) {
                                if ($vehicle_type['VehicleType']['id'] == $garage_vehicle_type['GarageVehicleType']['vehicle_type_id']) {
                    ?>
                                    <div class="medium-2 columns end">
                                        <label class="cursor-default">
                                            <span>
                                                <?php
                                                echo $this->Html->image(
                                                    FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . $vehicle_type['VehicleType']['url'])
                                                ) . " " . h($vehicle_type['VehicleType']['name_' . __l()]);
                                                ?>
                                            </span>
                                        </label>
                                    </div>
                    <?php
                                }
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        <?php } ?>

        <?php if (count($garage_vehicles) > 0) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="vehicle_brands_specialist_anchor"> <?php echo __t('Garage.Vehicle_brands_specialist') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'action' => 'add_activities_and_services_garage',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    }
                    ?>
                </div>
                <div class="d-inline-block cont-services w-100p">
                    <?php
                    foreach ($vehicles as $vehicle) {
                        $active = false;
                        $icon = '';
                        foreach ($garage_vehicles as $garage_vehicle) {
                            if (isset($garage_vehicle)) {
                                if ($vehicle['Vehicle']['id'] == $garage_vehicle['Vehicle']['id']) {
                                    $active = true;
                                }
                            }
                        }
                        foreach ($garage_vehicle_specialists as $garage_vehicle_specialist) {
                            if (isset($garage_vehicle)) {
                                if ($vehicle['Vehicle']['id'] == $garage_vehicle_specialist['GarageSpecialistMake']['vehicle_id']) {
                                    $icon = 'ion-ios-star';
                                }
                            }
                        }
                        if ($active == true) {
                    ?>
                            <div>
                                <label class="cursor-default">
                                    <span class="c-primary <?php echo " " . $icon ?>"><?php echo h($vehicle['Vehicle']['name_' . __l()]); ?></span>
                                </label>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        <?php } ?>

        <?php if ($config[ConstantsConfig::PARTS_BRANDS]) { ?>
            <?php if (!empty($garages_parts_brands)) { ?>
                <div class="row">
                    <div class="flex ai-center gap-1 p-top-1">
                        <div class="anchor_nav aag-subtitle" id="parts_brands"> <?php echo __t('Garage.Parts_brands_allowed') ?></div>
                        <?php
                        if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                            echo $this->Html->link(
                                '',
                                array(
                                    'action' => 'add_activities_and_services_garage',
                                    $garage['Garage']['id']
                                ),
                                array(
                                    'class' => 'ion-arrow-right-c c-primary',
                                    'style' => 'z-index: 4; position: relative;',
                                )
                            );
                        }
                        ?>
                    </div>
                    <div class="d-inline-block cont-services w-100p">
                        <?php
                        foreach ($parts_brands as $part_brand) {
                            $active = false;

                            foreach ($garages_parts_brands as $garages_part_brand) {
                                if (isset($garages_part_brand)) {
                                    if ($part_brand['Brand']['id'] == $garages_part_brand['Brand']['id']) {
                                        $active = true;
                                    }
                                }
                            }

                            if ($active == true) {
                        ?>
                                <div class="medium-2 columns end">
                                    <label class="cursor-default">
                                        <?php echo h($part_brand['Brand']['name']); ?>
                                    </label>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

        <div class="row">
            <div class="flex ai-center gap-1 p-top-1">
                <div class="anchor_nav aag-subtitle" id="miscellaneous_anchor"> <?php echo __t('Garage.Miscellaneous') ?></div>
                <?php
                if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                    echo $this->Html->link(
                        '',
                        array(
                            'action' => 'add_aditional_info_and_other_details_garage',
                            $garage['Garage']['id']
                        ),
                        array(
                            'class' => 'ion-arrow-right-c c-primary',
                            'style' => 'z-index: 4; position: relative;',
                        )
                    );
                }
                ?>
            </div>
            <div class="cnt-form-inputs">
                <div>
                    <strong><?php echo __t('Garage.Number_of_ramps') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo h($garage['Garage']['ramps']); ?>
                    </div>
                </div>
                <?php if ($config[ConstantsConfig::MOT]) { ?>
                    <div>
                        <strong><?php echo __t('Garage.Number_of_MOT_bays') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <?php echo h($garage['Garage']['MOT_bays']); ?>
                        </div>
                    </div>
                <?php } ?>
                <div>
                    <strong><?php echo __t('Garage.Foundation_year') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <strong class="c-primary">
                            <?php echo h($garage['Garage']['foundation_year']); ?>
                        </strong>
                    </div>
                </div>
                <?php if ($config[ConstantsConfig::SPEND]) { ?>
                    <div class="row p-top-1" style="padding-bottom: 7px;">
                        <div class="columns">
                            <strong style="font-size: 15px;"><?php echo __t('Garage.Spend'); ?></strong>
                        </div>
                        <div>
                            <strong><?php echo __t('Garage.This_month') ?>:</strong>
                            <br>
                            <div class="b-bottom-1 height_input">
                                <?php echo h($garage['Garage']['spend_this_month']); ?>
                            </div>
                        </div>
                        <div>
                            <strong><?php echo __t('Garage.Last_month') ?>:</strong>
                            <br>
                            <div class="b-bottom-1 height_input">
                                <?php echo h($garage['Garage']['spend_last_month']); ?>
                            </div>
                        </div>
                        <div>
                            <strong><?php echo __t('Garage.Last_12_month') ?>:</strong>
                            <br>
                            <div class="b-bottom-1 height_input">
                                <?php echo h($garage['Garage']['spend_12_month']); ?>
                            </div>
                        </div>
                        <div>
                            <strong><?php echo __t('Garage.Projected') ?>:</strong>
                            <br>
                            <div class="b-bottom-1 height_input">
                                <?php echo h($garage['Garage']['spend_projected']); ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php if ($config[ConstantsConfig::EQUIPMENT]) { ?>
            <?php if (!empty($garage_equipments)) { ?>
                <div class="row">
                    <div class="flex ai-center gap-1 p-top-1">
                        <div class="anchor_nav aag-subtitle" id="equipment_anchor"> <?php echo __t('Equipment.Equipment') ?></div>
                        <?php
                        if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                            echo $this->Html->link(
                                '',
                                array(
                                    'action' => 'add_equipment_and_software_garage',
                                    $garage['Garage']['id']
                                ),
                                array(
                                    'class' => 'ion-arrow-right-c c-primary',
                                    'style' => 'z-index: 4; position: relative;',
                                )
                            );
                        } ?>
                    </div>
                    <div class="cnt-form-inputs">
                        <?php
                        foreach ($garage_equipments as $garage_equipment) {
                        ?>
                            <div>
                                <strong><?php echo __t('Equipment.Equipment') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input"><?php echo h($equipments[$garage_equipment['GarageEquipment']['equipment_id']]) ?> </div>
                            </div>
                            <div>
                                <strong><?php echo __t('Equipment.Type') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input">
                                    <?php echo $garage_equipment['GarageEquipment']['equipment_type_id'] ? h($equipment_types[$garage_equipment['GarageEquipment']['equipment_type_id']]) : ''; ?>
                                </div>
                            </div>
                            <div>
                                <strong><?php echo __t('Equipment.Supplier') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input">
                                    <?php echo $garage_equipment['GarageEquipment']['supplier_id'] ? h($suppliers[$garage_equipment['GarageEquipment']['supplier_id']]) : ''; ?>
                                </div>
                            </div>
                            <div>
                                <strong><?php echo __t('Equipment.Brand') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input">
                                    <?php echo $garage_equipment['GarageEquipment']['brand_id'] ? h($brands[$garage_equipment['GarageEquipment']['brand_id']]) : ''; ?>
                                </div>
                            </div>
                            <div>
                                <strong><?php echo __t('Equipment.Start_date') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input"><?php echo Fecha::toFormatoVista(h($garage_equipment['GarageEquipment']['start_date'])); ?> </div>
                            </div>
                            <div>
                                <strong><?php echo __t('Equipment.End_date') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input"><?php echo Fecha::toFormatoVista(h($garage_equipment['GarageEquipment']['end_date'])); ?> </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

        <?php if (!empty($garage_software)) { ?>
            <div class="row">
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="software_anchor"> <?php echo __t('Garage.Software') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'action' => 'add_equipment_and_software_garage',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    } ?>
                </div>
                <div class="cnt-form-inputs">
                    <?php
                    foreach ($garage_software as $software_tmp) {
                    ?>
                        <div>
                            <div>
                                <strong><?php echo __t('Garage.Software') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input"><?php echo h($software[$software_tmp['GarageSoftware']['software_id']]) ?> </div>
                            </div>
                            <div>
                                <strong><?php echo __t('Software.Type') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input">
                                    <?php echo $software_tmp['GarageSoftware']['software_type_id'] ? h($software_types[$software_tmp['GarageSoftware']['software_type_id']]) : ''; ?>
                                </div>
                            </div>
                            <div>
                                <strong><?php echo __t('Software.Supplier') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input">
                                    <?php echo $software_tmp['GarageSoftware']['supplier_id'] ? h($suppliers[$software_tmp['GarageSoftware']['supplier_id']]) : ''; ?>
                                </div>
                            </div>
                            <div>
                                <strong><?php echo __t('Software.Manufacturer') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input">
                                    <?php echo $software_tmp['GarageSoftware']['software_manufacture_id'] ? h($software_manufactures[$software_tmp['GarageSoftware']['software_manufacture_id']]) : ''; ?>
                                </div>
                            </div>
                            <div>
                                <strong><?php echo __t('Garage.Start_date') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input"><?php echo Fecha::toFormatoVista(h($software_tmp['GarageSoftware']['start_date'])); ?> </div>
                            </div>
                            <div>
                                <strong><?php echo __t('Garage.End_date') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input"><?php echo Fecha::toFormatoVista(h($software_tmp['GarageSoftware']['end_date'])); ?> </div>
                            </div>
                            <div>
                                <strong><?php echo __t('Garage.Version') ?>:</strong>
                                <br>
                                <div class="b-bottom-1 height_input"><?php echo h($software_tmp['GarageSoftware']['version']); ?>
                                </div>
                            </div>
                            <?php if ($config[ConstantsConfig::SOFTWARE_USER_GARAGE]) { ?>
                                <div>
                                    <strong><?php echo __t('User.User') ?>:</strong>
                                    <br>
                                    <div class="b-bottom-1 height_input"><?php echo h($software_tmp['GarageSoftware']['username']); ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($config[ConstantsConfig::SOFTWARE_PASSWORD_GARAGE]) { ?>
                                <div>
                                    <strong><?php echo __t('Garage.Password') ?>:</strong>
                                    <br>
                                    <div class="b-bottom-1 height_input"><?php echo h($software_tmp['GarageSoftware']['password']); ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            <?php } ?>

            <?php if (!empty($garage_websites) || $config[ConstantsConfig::LEAD_SOURCE] || $config[ConstantsConfig::INTEREST] || $config[ConstantsConfig::MARKETING_EMAIL]) { ?>
                <div class="flex ai-center gap-1 p-top-1">
                    <div class="anchor_nav aag-subtitle" id="marketing_anchor"> <?php echo __t('Garage.Marketing') ?></div>
                    <?php
                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'action' => 'add_marketing_and_image_garage',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'ion-arrow-right-c c-primary',
                                'style' => 'z-index: 4; position: relative;',
                            )
                        );
                    }
                    ?>
                </div>
                <?php if ($config[ConstantsConfig::LEAD_SOURCE]) { ?>
                    <div class="cnt-form-inputs">
                        <div class="three-columns">
                            <strong><?php echo __t('Garage.Lead_source') ?>:</strong>
                            <br>
                            <div class="b-bottom-1 height_input">
                                <?php
                                if (isset($garage['Garage']['lead_source']) && $garage['Garage']['lead_source']) {
                                    echo h($lead_sources[$garage['Garage']['lead_source']]);
                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($config[ConstantsConfig::INTEREST]) { ?>
                        <div class="clear-column">
                            <strong><?php echo __t('Garage.Interest') ?>:</strong>
                            <br>
                            <div class="b-bottom-1 height_input">
                                <?php echo h($garage['Garage']['interests']); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($config[ConstantsConfig::MARKETING_EMAIL]) { ?>
                        <div>
                            <strong><?php echo __t('Garage.Marketing_email') ?>:</strong>
                            <br>
                            <div class="b-bottom-1 height_input">
                                <?php echo h($garage['Garage']['marketing_email']); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php
                    foreach ($garage_websites as $garage_website) {
                    ?>
                        <div>
                            <strong><?php echo __t('Garage.Website') ?>:</strong>
                            <br>
                            <div class="b-bottom-1 height_input">
                                <?php echo h($websites[$garage_website['GarageWebsite']['website_id']]); ?>
                            </div>
                        </div>
                        <div>
                            <strong><?php echo __t('Garage.URL') ?>:</strong>
                            <br>
                            <div class="b-bottom-1 height_input">
                                <?php echo h($garage_website['GarageWebsite']['url']); ?>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                    </div>
                <?php } ?>
                <?php if (!empty($garage_comments)) { ?>
                    <div class="flex ai-center gap-1 p-top-1">
                        <div class="anchor_nav aag-subtitle" id="comments_anchor"><?php echo __t('Garage.Comments') ?></div>
                        <?php
                        if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                            echo $this->Html->link(
                                '',
                                array(
                                    'action' => 'add_comments',
                                    $garage['Garage']['id']
                                ),
                                array(
                                    'class' => 'ion-arrow-right-c c-primary',
                                    'style' => 'z-index: 4; position: relative;',
                                )
                            );
                        }
                        ?>
                    </div>
                    <div class="o-auto">
                        <table class="table-tracking table-responsive">
                            <thead>
                                <tr>
                                    <th><?php echo __t('Garage.Username'); ?></th>
                                    <th><?php echo __t('Garage.Body'); ?></th>
                                    <th class="ta-center"><?php echo __t('Garage.Creation_date'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($garage_comments as $comment) {
                                ?>
                                    <tr>
                                        <td>
                                            <?php echo h($comment['User']['name']); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $str = h($comment['GarageComment']['body']);
                                            if (strlen($str) > 250) {
                                                $str = substr($str, 0, 245) . '...';
                                            }
                                            echo $str;
                                            ?>
                                        </td>
                                        <td class="ta-center">
                                            <?php echo Fecha::toFormatoVista(h($comment['GarageComment']['creation_date'])); ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    if (count($garage_comments) == 5) {
                        echo $this->Html->link(
                            __t('Garage.View_more'),
                            array(
                                'action' => 'add_comments',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'button-general tres ta-center btn-full-width z-index-priority button-general tres',
                            )
                        );
                    }
                    ?>

                <?php } ?>

                <?php if (!empty($logs_changes)) { ?>
                    <div class="flex ai-center gap-1 p-top-1">
                        <div class="anchor_nav aag-subtitle" id="log_changes_anchor"><?php echo __t('Garage.Log_changes') ?></div>
                        <?php
                        if (count($logs_changes) > 0) {
                        ?> <div class="aag-subtitle"><?php echo __t('General.Last') . ' ' . count($logs_changes) . ' ' . __t('General.Changes') ?></div> <?php
                                                                                                                            }
                                                                                                                            if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                                                                                                                                echo $this->Html->link(
                                                                                                                                    '',
                                                                                                                                    array(
                                                                                                                                        'action' => 'add_admin',
                                                                                                                                        $garage['Garage']['id']
                                                                                                                                    ),
                                                                                                                                    array(
                                                                                                                                        'class' => 'ion-arrow-right-c c-primary',
                                                                                                                                        'style' => 'z-index: 4; position: relative;',
                                                                                                                                    )
                                                                                                                                );
                                                                                                                            }
                                                                                                                                ?>
                    </div>
                    <div class="o-auto">
                        <table class="table-tracking table-responsive">
                            <thead>
                                <tr>
                                    <th><?php echo __t('Logs.Table'); ?></th>
                                    <th><?php echo __t('Logs.Field'); ?></th>
                                    <th><?php echo __t('Logs.User'); ?></th>
                                    <th><?php echo __t('Logs.Date'); ?></th>
                                    <th><?php echo __t('Logs.Old_value'); ?></th>
                                    <th><?php echo __t('Logs.New_value'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($logs_changes as $log_change) {
                                ?>
                                    <tr>
                                        <td>
                                            <?php echo h($log_change['LogTable']['name_' . __l()]); ?>
                                        </td>
                                        <td>
                                            <?php echo h($log_change['LogField']['name_' . __l()]); ?>
                                        </td>
                                        <td>
                                            <?php echo h($log_change['User']['name']); ?>
                                        </td>
                                        <td>
                                            <?php echo h(date("d-m-Y", strtotime($log_change['LogChange']['date']))); ?>
                                        </td>
                                        <td>
                                            <?php echo h(__t($log_change['LogChange']['old_value'])); ?>
                                        </td>
                                        <td>
                                            <?php echo h(__t($log_change['LogChange']['new_value'])); ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    if (count($logs_changes) == 15) {
                        echo $this->Html->link(
                            __t('Garage.View_more'),
                            array(
                                'controller' => 'garages',
                                'action' => 'add_admin',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'button-general tres ta-center btn-full-width',
                            )
                        );
                    }
                    ?>
                <?php } ?>
            <?php
        }
