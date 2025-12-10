<?php
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('distributors.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->hidden('', array('id' => 'distributor_id', 'value' => $distributor_id));
$config = CakeSession::read('Auth.User.Config');
$userAagRegionId = CakeSession::read('Auth.User.aag_region_id');
$userRole = CakeSession::read('Auth.User.role_id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(
            array(
                $this->Html->link(__t('CRM.Crm'), array('controller' => 'dashboard', 'action' => 'home')),
                $this->Html->link(__t('Distributor.Distributor'), array('controller' => 'clients', 'action' => 'home_distributors')),
                __t('CRM.About')
            )
        );
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="d-inline-block p-right-1 cnt-title-report titulo-nombre-taller">
        <?php echo $this->element('../Clients/Elements/menu_distributor'); ?>
    </div>
    <div class="d-inline-block cnt-items-logo">
        <?php
        foreach ($distributor_networks as $distributor_network) {
            foreach ($networks as $network) {
                if ($network['DistributorNetwork']['id'] == $distributor_network['DistributorDistributorNetwork']['network_id']) {
        ?>
                    <div class="d-inline-block ta-center end p-right-1 item-logo m-top-1">
                        <div>
                            <img style="height: 40px !important" class="logotipo" title="<?php echo h($network['DistributorNetwork']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . h($network['DistributorNetwork']['image'])); ?>" />
                        </div>
                        <div>
                            <?php
                            if ($distributor_network['DistributorDistributorNetwork']['status'] == ConstantsNetworksStatus::UNSUBSCRIBE || $distributor_network['DistributorDistributorNetwork']['status'] == ConstantsNetworksStatus::NOT_CONVERTED) {
                                $class = 'c-fallo';
                            } elseif ($distributor_network['DistributorDistributorNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                                $class = 'c-exito';
                            } else {
                                $class = 'c-informacion';
                            }
                            ?>
                            <strong class="<?php echo $class; ?>">
                                <?php echo h($networks_statuses[$distributor_network['DistributorDistributorNetwork']['status']]); ?>
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
            <span>
                <b class="fields_views">
                    <?php echo __t('Distributor.Creation_date'); ?>
                </b>
                <?php echo ': ' . Fecha::toFormatoVista(h($distributor['Distributor']['creation_date'])); ?>
            </span>
            <span class="p-left-1">
                <?php echo (''); ?>
            </span>
            <span>
                <b class="fields_views">
                    <?php echo __t('Distributor.Modification_date'); ?>
                </b>
                <?php echo ': ' . Fecha::toFormatoVista(h($distributor['Distributor']['modification_date'])); ?>
            </span>
            <span>
                <?php echo ' / ' . __t('Distributor.Number_garages') . ": "; ?>
            </span>
            <div class="paso-n d-inline-block uno">
                <a href="#link_garages">
                    <span id="total_garages_distributors">
                        <?php echo h($count_distributor_garages); ?>
                    </span>
                </a>
            </div>
        </div>
        <br />
        <br />
        <ul class="aag-subtabs clear m-bottom-1">
            <?php
            if (!empty($distributor_networks)) {
            ?> <li><a href="#network_anchor"><?php echo __t('Network.Networks'); ?></a></li> <?php
                                                                                            }
                                                                                                ?>
            <li><a href="#location_anchor"><?php echo __t('Distributor.Location'); ?></a></li>
            <li><a href="#opening_times_anchor"><?php echo __t('Garage.Opening_times'); ?></a></li>
            <?php
            if (!empty($distributor_activities)) {
            ?> <li><a href="#activity_anchor"><?php echo __t('Distributor.Activity'); ?></a></li> <?php
                                                                                                }
                                                                                                if ($config[ConstantsConfig::LABEL]) {
                                                                                                    if (!empty($distributor_labels)) {
                                                                                                    ?> <li><a href="#label_anchor"><?php echo __t('Label.Label'); ?></a></li> <?php
                                                                                                                                                                            }
                                                                                                                                                                        }
                                                                                                                                                                        if ($config[ConstantsConfig::AAG_SERVICES]) {
                                                                                                                                                                            if (!empty($distributor_services)) {
                                                                                                                                                                                ?> <li><a href="#service_anchor"><?php echo __t('Distributor.Aag_services'); ?></a></li> <?php
                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                    if (!empty($distributor_software)) {
                                                                                                                                                                                                                                                                            ?> <li><a href="#software_anchor"><?php echo __t('Distributor.Software'); ?></a></li> <?php
                                                                                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                                                                                if (!empty($distributor_contracts)) {
                                                                                                                                                                                                                                                                                                                                                                    ?> <li><a href="#contract_anchor"><?php echo __t('Distributor.Contracts'); ?></a></li> <?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                                                                                                                                                                                        if (count($images) > 0) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                            ?> <li><a href="#images_anchor"><?php echo __t('Distributor.Images'); ?></a></li> <?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            if (!empty($distributor_contacts_general_branch_manager)) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?> <li><a href="#general_branch_manager_anchor"><?php echo __t('Contact.General_branch_manager'); ?></a></li> <?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            if (!empty($distributor_contacts_bdm)) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?> <li><a href="#bdm_anchor"><?php echo __t('Contact.BDM'); ?></a></li> <?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    if (!empty($distributor_contacts_staff)) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?> <li><a href="#staff_anchor"><?php echo __t('Contact.Staff'); ?></a></li> <?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                if (!empty($distributor_comments)) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ?> <li><a href="#comments_anchor"><?php echo __t('Distributor.Comments'); ?></a></li> <?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        if (!empty($distributor_garages)) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ?> <li><a href="#link_garages"><?php echo __t('Distributor.Garages_associated'); ?></a></li> <?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        if (!empty($logs_changes)) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ?> <li><a href="#log_changes_anchor"><?php echo __t('Distributor.Log_changes'); ?></a></li> <?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?>
        </ul>
    </div>
    <div class="row p-bottom-1">
        <div class="columns medium-8 p-0">
            <div class="cnt-form-inputs">
                <div>
                    <strong><?php echo __t('Distributor.Account_number'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo h($distributor['Distributor']['account_number']); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Distributor.Abbreviation'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo h($distributor['Distributor']['abbreviation']); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Distributor.Trading_as'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo h($distributor['Distributor']['trading_as']); ?>
                    </div>
                </div>
                <div>
                    <?php if ($distributor['Distributor']['head_office']) { ?>
                        <strong><?php echo __t('Distributor.Head_office'); ?>:</strong>
                        <br />
                        <div class="b-bottom-1 height_input">
                            <?php echo __t('General.Yes'); ?>
                        </div>
                    <?php } ?>
                    <?php if (!$distributor['Distributor']['head_office']) { ?>
                        <strong><?php echo __t('Distributor.Parent_distributor'); ?>:</strong>
                        <br />
                        <div class="b-bottom-1 height_input">
                            <?php
                            if (isset($distributor_parent_account['Distributor']['name']) && isset($distributor_parent_account['Distributor']['account_number'])) {
                                echo h($distributor_parent_account['Distributor']['name']) . ' - ' . h($distributor_parent_account['Distributor']['account_number']);
                            } else {
                                echo '';
                            }
                            ?>
                        </div>
                    <?php } ?>
                </div>
                <div>
                    <strong><?php echo __t('Distributor.Distributor_type'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php if ($distributor['Distributor']['distributor_type_id']) { ?>
                            <?php echo h($distributor_types[$distributor['Distributor']['distributor_type_id']]); ?>
                        <?php } ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Distributor.Association_'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php if (isset($association['Association']['name'])) {
                            echo h($association['Association']['name']);
                        } ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Distributor.Association'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php if (isset($distributor['Distributor']['association_type_id'])) {
                            echo h($associations_types[$distributor['Distributor']['association_type_id']]);
                        } ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Distributor.Aag_member'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo $distributor['Distributor']['aag_member'] ? __t('General.Yes') : __t('General.No'); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Distributor.Trading_group'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo h($trading_groups[$distributor['Distributor']['trading_group_id']]); ?>
                    </div>
                </div>
                <?php if ($config[ConstantsConfig::SIRET_DISTRIBUTOR]) { ?>
                    <div>
                        <strong><?php echo __t('Distributor.Siret'); ?>: </strong>
                        <br />
                        <div class="b-bottom-1 height_input">
                            <?php
                            echo substr($distributor['Distributor']['siret'], 0, 3) . ' ' . substr($distributor['Distributor']['siret'], 3, 3) . ' ' . substr($distributor['Distributor']['siret'], 6, 3) . ' ' . substr($distributor['Distributor']['siret'], 9);
                            ?>
                        </div>
                    </div>
                <?php } ?>
                <div>
                    <strong><?php echo __t('Distributor.VAT_number'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo h($distributor['Distributor']['VAT_number']); ?>
                    </div>
                </div>
                <?php if ($config[ConstantsConfig::DETAX_DISTRIBUTOR]) { ?>
                    <div>
                        <strong><?php echo __t('Distributor.Detax_code'); ?>: </strong>
                        <br />
                        <div class="b-bottom-1 height_input">
                            <?php echo h($distributor['Distributor']['detax_code']); ?>
                        </div>
                    </div>
                <?php }
                if ($config[ConstantsConfig::CREDIT_WATCH]) { ?>
                    <div>
                        <strong><?php echo __t('Distributor.Credit_watch'); ?>: </strong>
                        <br />
                        <div class="b-bottom-1 height_input">
                            <?php echo Booleano::toString(($distributor['Distributor']['credit_watch'])); ?>
                        </div>
                    </div>
                <?php } ?>
                <div>
                    <strong><?php echo __t('Garage.Status'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo h($distributor_statuses[$distributor['Distributor']['status']]); ?>
                    </div>
                </div>
                <div class="clear-column">
                    <strong><?php echo __t('Distributor.Phone'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo h($distributor['Distributor']['phone']); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Distributor.Fax'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo h($distributor['Distributor']['fax']); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Distributor.Email'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo $this->Html->link(h($distributor['Distributor']['email']), 'mailto:' . h($distributor['Distributor']['email'])); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Distributor.Web'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <a target="_blank" title="<?php echo h($distributor['Distributor']['web']); ?>" href="<?php echo h($distributor['Distributor']['web']); ?>"><?php echo h($distributor['Distributor']['web']); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="columns medium-4">
            <div class="ta-center p-1" style="height: 214px;">
                <?php
                if (!isset($distributor_principal_image['DistributorImage']) || $distributor_principal_image['DistributorImage']['file'] == "") {
                ?> <img src="<?php echo '/img/default.png' ?>" style="max-height: 210px;" /> <?php
                                                                                            } else {
                                                                                                echo $this->Html->image(
                                                                                                    Router::url(
                                                                                                        array(
                                                                                                            'controller' => 'distributors_images',
                                                                                                            'action' => 'download_file',
                                                                                                            $distributor_principal_image['DistributorImage']['id'],
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
    <?php
    if (!empty($distributor_networks)) {
    ?>
        <div class="row">
            <div class="anchor_nav aag-subtitle" id="network_anchor"> <?php echo __t('Network.Networks'); ?></div>
            <div class="o-auto p-bottom-1">
                <table class="table-tracking table-responsive z-index-priority">
                    <thead>
                        <tr>
                            <th><?php echo __t('Garage.Network'); ?></th>
                            <th class="ta-center"><?php echo __t('Garage.Trading_group'); ?></th>
                            <th><?php echo __t('Garage.Start_date'); ?></th>
                            <th><?php echo __t('Garage.End_date'); ?></th>
                            <th><?php echo __t('Network.Leaving_reason'); ?></th>
                            <th class="ta-center"><?php echo __t('Garage.Status'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($distributor_networks as $distributor_network) { ?>
                            <tr>
                                <?php foreach ($networks as $network) {
                                    if ($network['DistributorNetwork']['id'] == $distributor_network['DistributorDistributorNetwork']['network_id']) { ?>
                                        <td>
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
                                        </td>
                                        </td>
                                <?php }
                                } ?>
                                <td class="ta-center">
                                    <?php foreach ($trading_groups as $key => $trading_group) {
                                        if ($key == $distributor_network['DistributorDistributorNetwork']['trading_group_id']) {
                                            echo h($trading_group);
                                        }
                                    } ?>
                                </td>
                                <td>
                                    <?php echo Fecha::toFormatoVista($distributor_network['DistributorDistributorNetwork']['contract_start_date']); ?>
                                </td>
                                <td>
                                    <?php echo Fecha::toFormatoVista($distributor_network['DistributorDistributorNetwork']['contract_end_date']); ?>
                                </td>
                                <td>
                                    <?php echo Fecha::toFormatoVista($distributor_network['DistributorDistributorNetwork']['reason_leaving_id']); ?>
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
                                    <strong class="<?php echo $class; ?>">
                                        <?php echo h($networks_statuses[$distributor_network['DistributorDistributorNetwork']['status']]); ?>
                                    </strong>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php
    }
    ?>

    <div class="row ">
        <div class="anchor_nav_dis aag-subtitle" id="location_anchor"> <?php echo __t('Distributor.Location'); ?></div>
        <div class="columns medium-8 p-0">
            <div class="cnt-form-inputs">
                <div>
                    <strong><?php echo __t('Distributor.Address'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php
                        echo h($distributor['Distributor']['address1']);
                        if (!empty($distributor['Distributor']['address2'])) {
                            echo ", " . h($distributor['Distributor']['address2']);
                        }
                        if (!empty($distributor['Distributor']['address3'])) {
                            echo ", " . h($distributor['Distributor']['address3']);
                        }
                        if (!empty($distributor['Distributor']['address4'])) {
                            echo ", " . h($distributor['Distributor']['address4']);
                        }
                        ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Garage.Postcode'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo h($distributor['Distributor']['postcode']); ?>
                    </div>
                </div>
                <?php if ($config[ConstantsConfig::COUNTY_COUNTRY_DISTRIBUTOR]) { ?>
                    <div>
                        <strong><?php echo __t('Distributor.Country'); ?>:</strong>
                        <br />
                        <div class="b-bottom-1 height_input">
                            <?php
                            if (isset($country['Country']['name'])) {
                                echo h($country['Country']['name']);
                            }
                            ?>
                        </div>
                    </div>
                    <div>
                        <strong><?php echo __t('Distributor.County'); ?>:</strong>
                        <br />
                        <div class="b-bottom-1 height_input">
                            <?php
                            if (isset($province_list[$distributor['Distributor']['province_id']])) {
                                echo h($province_list[$distributor['Distributor']['province_id']]);
                            } else {
                                echo "";
                            } ?>
                        </div>
                    </div>
                <?php } ?>
                <div>
                    <strong><?php echo __t('Distributor.Town'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo h($distributor['Distributor']['town']); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Distributor.Sales_area'); ?>:</strong>
                    <br />
                    <div class="b-bottom-1 height_input">
                        <?php echo (isset($distributor['Distributor']['sales_area_id']) && isset($sales_area[$distributor['Distributor']['sales_area_id']])) ? h($sales_area[$distributor['Distributor']['sales_area_id']]) : ''; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="columns medium-4">
            <?php
            if (!empty($distributor['Distributor']['latitude']) && !empty($distributor['Distributor']['longitude'])) { ?>
                <div id="map-single" class="contenedor-mapa" style="height: 150px;" data-editable="<?php echo ConstantsBooleans::NO; ?>" data-latitude="<?php echo $distributor['Distributor']['latitude'] ?>" data-longitude="<?php echo $distributor['Distributor']['longitude'] ?>">
                </div>
                <div class="alliance_bar"></div>
            <?php } ?>
        </div>
    </div>

    <div class="row p-top-1">
        <div class="anchor_nav_dis aag-subtitle" id="opening_times_anchor"> <?php echo __t('Garage.Opening_times'); ?></div>
        <fieldset class="fieldset-garage-list cnt-estilo-nuevo background-color-primary" style="font-size: small;">
            <div class="cnt-dias-semana">
                <div class="dia-semana">
                    <strong><?php echo __t('Garage.Monday'); ?>:</strong>
                    <br />
                    <span class="b-bottom-1 height_input">
                        <?php
                        if (empty($distributor['Distributor']['monday_open_1']) && empty($distributor['Distributor']['monday_closed_1']) && empty($distributor['Distributor']['monday_open_2']) && empty($distributor['Distributor']['monday_closed_2'])) {
                            echo __t('Garage.Closed');
                        }
                        if (!empty($distributor['Distributor']['monday_open_1'])) { ?>
                            <?php echo h($distributor['Distributor']['monday_open_1']) . ' - ' . h($distributor['Distributor']['monday_closed_1']); ?>
                        <?php }
                        if (!empty($distributor['Distributor']['monday_open_2'])) { ?>
                            <?php
                            echo '<br/>';
                            echo h($distributor['Distributor']['monday_open_2']) . ' - ' . h($distributor['Distributor']['monday_closed_2']); ?>
                        <?php } ?>
                    </span>
                </div>
                <div class="dia-semana">
                    <strong><?php echo __t('Garage.Tuesday'); ?>:</strong>
                    <br />
                    <span class="b-bottom-1 height_input">
                        <?php if (empty($distributor['Distributor']['tuesday_open_1']) && empty($distributor['Distributor']['tuesday_open_2']) && empty($distributor['Distributor']['tuesday_closed_1']) && empty($distributor['Distributor']['tuesday_closed_2'])) {
                            echo __t('Garage.Closed');
                        } ?>
                        <?php if (!empty($distributor['Distributor']['tuesday_open_1'])) { ?>
                            <?php echo h($distributor['Distributor']['tuesday_open_1']) . ' - ' . h($distributor['Distributor']['tuesday_closed_1']); ?>
                        <?php } ?>
                        <?php if (!empty($distributor['Distributor']['tuesday_open_2'])) { ?>
                            <?php
                            echo '<br/>';
                            echo h($distributor['Distributor']['tuesday_open_2']) . ' - ' . h($distributor['Distributor']['tuesday_closed_2']); ?>
                        <?php } ?>
                    </span>
                </div>
                <div class="dia-semana">
                    <strong><?php echo __t('Garage.Wednesday'); ?>:</strong>
                    <br />
                    <span class="b-bottom-1 height_input">
                        <?php if (empty($distributor['Distributor']['wednesday_open_1']) && empty($distributor['Distributor']['wednesday_open_2']) && empty($distributor['Distributor']['wednesday_closed_1']) && empty($distributor['Distributor']['wednesday_closed_2'])) {
                            echo __t('Garage.Closed');
                        } ?>
                        <?php if (!empty($distributor['Distributor']['wednesday_open_1'])) { ?>
                            <?php echo h($distributor['Distributor']['wednesday_open_1']) . ' - ' . h($distributor['Distributor']['wednesday_closed_1']); ?>
                        <?php } ?>
                        <?php if (!empty($distributor['Distributor']['wednesday_open_2'])) { ?>
                            <?php
                            echo '<br/>';
                            echo h($distributor['Distributor']['wednesday_open_2']) . ' - ' . h($distributor['Distributor']['wednesday_closed_2']); ?>
                        <?php } ?>
                    </span>
                </div>
                <div class="dia-semana">
                    <strong><?php echo __t('Garage.Thursday'); ?>:</strong>
                    <br />
                    <span class="b-bottom-1 height_input">
                        <?php if (empty($distributor['Distributor']['thursday_open_1']) && empty($distributor['Distributor']['thursday_open_2']) && empty($distributor['Distributor']['thursday_closed_1']) && empty($distributor['Distributor']['thursday_closed_2'])) {
                            echo __t('Garage.Closed');
                        } ?>
                        <?php if (!empty($distributor['Distributor']['thursday_open_1'])) { ?>
                            <?php echo h($distributor['Distributor']['thursday_open_1']) . ' - ' . h($distributor['Distributor']['thursday_closed_1']); ?>
                        <?php } ?>
                        <?php if (!empty($distributor['Distributor']['thursday_open_2'])) { ?>
                            <?php
                            echo '<br/>';
                            echo h($distributor['Distributor']['thursday_open_2']) . ' - ' . h($distributor['Distributor']['thursday_closed_2']); ?>
                        <?php } ?>
                    </span>
                </div>
                <div class="dia-semana">
                    <strong><?php echo __t('Garage.Friday'); ?>:</strong>
                    <br />
                    <span class="b-bottom-1 height_input">
                        <?php if (empty($distributor['Distributor']['friday_open_1']) && empty($distributor['Distributor']['friday_open_2']) && empty($distributor['Distributor']['friday_closed_1']) && empty($distributor['Distributor']['friday_closed_2'])) {
                            echo __t('Garage.Closed');
                        } ?>
                        <?php if (!empty($distributor['Distributor']['friday_open_1'])) { ?>
                            <?php echo h($distributor['Distributor']['friday_open_1']) . ' - ' . h($distributor['Distributor']['friday_closed_1']); ?>
                        <?php } ?>
                        <?php if (!empty($distributor['Distributor']['friday_open_2'])) { ?>
                            <?php
                            echo '<br/>';
                            echo h($distributor['Distributor']['friday_open_2']) . ' - ' . h($distributor['Distributor']['friday_closed_2']); ?>
                        <?php } ?>
                    </span>
                </div>
                <div class="dia-semana">
                    <strong><?php echo __t('Garage.Saturday'); ?>:</strong>
                    <br />
                    <span class="b-bottom-1 height_input">
                        <?php if (empty($distributor['Distributor']['saturday_open_1']) && empty($distributor['Distributor']['saturday_open_2']) && empty($distributor['Distributor']['saturday_closed_1']) && empty($distributor['Distributor']['saturday_closed_2'])) {
                            echo __t('Garage.Closed');
                        } ?>
                        <?php if (!empty($distributor['Distributor']['saturday_open_1'])) { ?>
                            <?php echo h($distributor['Distributor']['saturday_open_1']) . ' - ' . h($distributor['Distributor']['saturday_closed_1']); ?>
                        <?php } ?>
                        <?php if (!empty($distributor['Distributor']['saturday_open_2'])) { ?>
                            <?php
                            echo '<br/>';
                            echo h($distributor['Distributor']['saturday_open_2']) . ' - ' . h($distributor['Distributor']['saturday_closed_2']); ?>
                        <?php } ?>
                    </span>
                </div>
                <div class="dia-semana">
                    <strong><?php echo __t('Garage.Sunday'); ?>:</strong>
                    <br />
                    <span class="b-bottom-1 height_input">
                        <?php if (empty($distributor['Distributor']['sunday_open_1']) && empty($distributor['Distributor']['sunday_open_2']) && empty($distributor['Distributor']['sunday_closed_1']) && empty($distributor['Distributor']['sunday_closed_2'])) {
                            echo __t('Garage.Closed');
                        } ?>
                        <?php if (!empty($distributor['Distributor']['sunday_open_1'])) { ?>
                            <?php echo h($distributor['Distributor']['sunday_open_1']) . ' - ' . h($distributor['Distributor']['sunday_closed_1']); ?>
                        <?php } ?>
                        <?php if (!empty($distributor['Distributor']['sunday_open_2'])) { ?>
                            <?php
                            echo '<br/>';
                            echo h($distributor['Distributor']['sunday_open_2']) . ' - ' . h($distributor['Distributor']['sunday_closed_2']); ?>
                        <?php } ?>
                    </span>
                </div>
            </div>
        </fieldset>
    </div>
    <?php
    if (!empty($distributor_activities)) {
    ?>
        <div class="row p-top-1">
            <div class="anchor_nav_dis aag-subtitle" id="activity_anchor"><?php echo __t('Distributor.Activity'); ?></div>
            <div class="o-auto">
                <table class="table-tracking">
                    <thead>
                        <tr>
                            <th><?php echo __t('Activity.Activity'); ?></th>
                            <?php if ($config[ConstantsConfig::ACTIVITY_TYPE]) { ?>
                                <th width="200"><?php echo __t('Activity.Type'); ?></th>
                            <?php } ?>
                            <?php if ($config[ConstantsConfig::WORKSHOP_ACTIVITIES]) { ?>
                                <th colspan="2"><?php echo __t('Distributor.Workshop_activities'); ?></th>
                            <?php } ?>
                            <th><?php echo __t('Distributor.Start_date'); ?></th>
                            <th><?php echo __t('Distributor.End_date'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($distributor_activities as $distributor_activity) { ?>
                            <tr>
                                <td>
                                    <?php echo strtoupper(h($customer_activities[$distributor_activity['DistributorCustomerActivity']['customer_activity_id']])); ?>
                                </td>
                                <?php if ($config[ConstantsConfig::ACTIVITY_TYPE]) { ?>
                                    <td>
                                        <?php echo ($distributor_activity['DistributorCustomerActivity']['type']) ? __t('Distributor.Workshop') : __t('Distributor.Distributor'); ?>
                                    </td>
                                <?php } ?>
                                <?php if ($config[ConstantsConfig::WORKSHOP_ACTIVITIES]) { ?>
                                    <td width="1" style="width: 1px;" class="ws-nowrap">
                                        <?php if (isset($distributor_activity['DistributorCustomerActivity']['workshops'])) {
                                            foreach ($distributor_activity['DistributorCustomerActivity']['workshops'] as $workshop) {
                                                echo h($workshop) . '<br />';
                                            }
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if (isset($distributor_activity['DistributorCustomerActivity']['details'])) {
                                            foreach ($distributor_activity['DistributorCustomerActivity']['details'] as $details) {
                                                echo '<i>' . h($details) . '</i>' . '<br />';
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
        </div>
        <?php
    }
    if ($config[ConstantsConfig::LABEL]) {
        if (!empty($distributor_labels)) {
        ?>
            <div class="row p-top-1">
                <div class="anchor_nav aag-subtitle" id="label_anchor"><?php echo __t('Label.Label'); ?></div>
                <div class="o-auto">
                    <table class="table-tracking">
                        <thead>
                            <tr>
                                <th><?php echo __t('Label.Label'); ?></th>
                                <th><?php echo __t('Label.Start_date'); ?></th>
                                <th><?php echo __t('Label.End_date'); ?></th>
                                <th><?php echo __t('Label.Modification_date'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($distributor_labels as $distributor_label) { ?>
                                <tr>
                                    <td>
                                        <?php echo h($label_types[$distributor_label['DistributorLabel']['label_type_id']]); ?>
                                    </td>
                                    <td>
                                        <?php echo h(Fecha::toFormatoVista($distributor_label['DistributorLabel']['start_date'])); ?>
                                    </td>
                                    <td>
                                        <?php echo h(Fecha::toFormatoVista($distributor_label['DistributorLabel']['end_date'])); ?>
                                    </td>
                                    <td>
                                        <?php echo h(Fecha::toFormatoVista($distributor_label['DistributorLabel']['modification_date'])); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php
        }
    }
    if ($config[ConstantsConfig::AAG_SERVICES]) {
        if (!empty($distributor_services)) {
        ?>
            <div class="row p-top-1">
                <div class="anchor_nav aag-subtitle" id="service_anchor"><?php echo __t('Distributor.Aag_services'); ?></div>
                <div class="o-auto">
                    <table class="table-tracking">
                        <thead>
                            <tr>
                                <th><?php echo __t('AagService.Name'); ?></th>
                                <th><?php echo __t('Distributor.Start_date'); ?></th>
                                <th><?php echo __t('Distributor.End_date'); ?></th>
                                <th><?php echo __t('Distributor.Modification_date'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($distributor_services as $distributor_service) {
                            ?>
                                <tr>
                                    <td>
                                        <?php echo h($service_types[$distributor_service['DistributorService']['service_type_id']]); ?>
                                    </td>
                                    <td>
                                        <?php echo h(Fecha::toFormatoVista($distributor_service['DistributorService']['start_date'])); ?>
                                    </td>
                                    <td>
                                        <?php echo h(Fecha::toFormatoVista($distributor_service['DistributorService']['end_date'])); ?>
                                    </td>
                                    <td>
                                        <?php echo h(Fecha::toFormatoVista($distributor_service['DistributorService']['modification_date'])); ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php
        }
    }
    if (!empty($distributor_software)) {
        ?>
        <div class="row p-top-1">
            <div class="anchor_nav_dis aag-subtitle" id="software_anchor"><?php echo __t('Distributor.Software'); ?></div>
            <div class="cnt-form-inputs">
                <?php foreach ($distributor_software as $software_tmp) { ?>
                    <div class="row p-bottom-1">
                        <div>
                            <strong><?php echo __t('Distributor.Software'); ?>:</strong>
                            <br />
                            <div class="b-bottom-1 height_input"><?php echo h($software[$software_tmp['DistributorSoftware']['software_id']]); ?> </div>
                        </div>
                        <div>
                            <strong><?php echo __t('Software.Type'); ?>:</strong>
                            <br />
                            <div class="b-bottom-1 height_input">
                                <?php echo $software_tmp['DistributorSoftware']['software_type_id'] ? h($software_types[$software_tmp['DistributorSoftware']['software_type_id']]) : '';   ?>
                            </div>
                        </div>
                        <div>
                            <strong><?php echo __t('Software.Supplier'); ?>:</strong>
                            <br />
                            <div class="b-bottom-1 height_input">
                                <?php echo $software_tmp['DistributorSoftware']['supplier_id'] ? h($suppliers[$software_tmp['DistributorSoftware']['supplier_id']]) : ''; ?>
                            </div>
                        </div>
                        <div>
                            <strong><?php echo __t('Software.Manufacturer'); ?>:</strong>
                            <br />
                            <div class="b-bottom-1 height_input">
                                <?php echo $software_tmp['DistributorSoftware']['software_manufacture_id'] ? h($software_manufactures[$software_tmp['DistributorSoftware']['software_manufacture_id']]) : ''; ?>
                            </div>
                        </div>
                        <div>
                            <strong><?php echo __t('Distributor.Start_date'); ?>:</strong>
                            <br />
                            <div class="b-bottom-1 height_input"><?php echo Fecha::toFormatoVista(h($software_tmp['DistributorSoftware']['start_date'])); ?> </div>
                        </div>
                        <div>
                            <strong><?php echo __t('Distributor.Modification_date'); ?>:</strong>
                            <br />
                            <div class="b-bottom-1 height_input"><?php echo Fecha::toFormatoVista(h($software_tmp['DistributorSoftware']['modification_date'])); ?> </div>
                        </div>
                        <div>
                            <strong><?php echo __t('Distributor.End_date'); ?>:</strong>
                            <br />
                            <div class="b-bottom-1 height_input"><?php echo Fecha::toFormatoVista(h($software_tmp['DistributorSoftware']['end_date'])); ?> </div>
                        </div>
                        <div>
                            <strong><?php echo __t('Distributor.Version'); ?>:</strong>
                            <br />
                            <div class="b-bottom-1 height_input"><?php echo h($software_tmp['DistributorSoftware']['version']); ?>
                            </div>
                        </div>

                        <?php if ($config[ConstantsConfig::SOFTWARE_USER_PASSWORD_DISTRIBUTOR]) { ?>
                            <div>
                                <strong><?php echo __t('User.User'); ?>:</strong>
                                <br />
                                <div class="b-bottom-1 height_input"><?php echo h($software_tmp['DistributorSoftware']['username']); ?>
                                </div>
                            </div>
                            <div>
                                <strong><?php echo __t('Distributor.Password'); ?>:</strong>
                                <br />
                                <div class="b-bottom-1 height_input"><?php echo h($software_tmp['DistributorSoftware']['password']); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php
    }
    if (!empty($distributor_contracts)) { ?>
        <div class="row p-top-1">
            <div class="anchor_nav_dis aag-subtitle" id="contract_anchor"><?php echo __t('Distributor.Contracts'); ?></div>
            <?php
            foreach ($distributor_contracts as $contract_tmp) { ?>
                <div class="cnt-form-inputs">
                    <div>
                        <strong><?php echo __t('Distributor.Trading_group'); ?>:</strong>
                        <br />
                        <div class="b-bottom-1 height_input">
                            <?php echo $contract_tmp['DistributorContract']['trading_group_id'] ? h($trading_groups[$contract_tmp['DistributorContract']['trading_group_id']]) : '';   ?>
                        </div>
                    </div>
                    <div>
                        <strong><?php echo __t('Network.Network'); ?>:</strong>
                        <br />
                        <div class="b-bottom-1 height_input">
                            <?php echo $contract_tmp['DistributorContract']['network_id'] ? h($network_list[$contract_tmp['DistributorContract']['network_id']]) : '';   ?>
                        </div>
                    </div>
                    <div>
                        <strong><?php echo __t('Distributor.Start_date'); ?>:</strong>
                        <br />
                        <div class="b-bottom-1 height_input">
                            <?php echo Fecha::toFormatoVista(h($contract_tmp['DistributorContract']['start_date'])); ?>
                        </div>
                    </div>
                    <div>
                        <strong><?php echo __t('Distributor.End_date'); ?>:</strong>
                        <br />
                        <div class="b-bottom-1 height_input">
                            <?php echo Fecha::toFormatoVista(h($contract_tmp['DistributorContract']['end_date'])); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php
    }
    if (count($images) > 0) { ?>
        <div class="row">
            <div class="anchor_nav aag-subtitle" id="images_anchor"> <?php echo __t('Distributor.Images'); ?></div>
            <?php
            if (count($images) > 0) {
                echo $this->element('../DistributorsImages/Elements/galleryScroll');
            }
            ?>
        </div>
    <?php
    }
    if (!empty($distributor_contacts_general_branch_manager)) {
        ?>
        <div class="row p-top-1">
            <div class="anchor_nav_dis aag-subtitle" id="general_branch_manager_anchor"><?php echo __t('Contact.General_branch_manager'); ?></div>
            <div class="o-auto">
                <table class="table-tracking table-responsive tabla-limitada">
                    <thead>
                        <tr>
                            <th class="first-name"><?php echo __t('Distributor.First_name'); ?></th>
                            <th class="last-name"><?php echo __t('Distributor.Last_name'); ?></th>
                            <th class="position"><?php echo __t('Contact.Position'); ?></th>
                            <th class="phone"><?php echo __t('Distributor.Phone'); ?></th>
                            <th class="mobile-phone"><?php echo __t('Contact.Mobile_phone'); ?></th>
                            <th><?php echo __t('Distributor.Email'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($distributor_contacts_general_branch_manager as $contact_general_branch_manager) { ?>
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
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php
            if (count($distributor_contacts_general_branch_manager) == 10) {
                echo $this->Html->link(
                    __t('Distributor.View_more'),
                    array(
                        'action' => 'add_contacts_general_branch_manager',
                        $distributor['Distributor']['id']
                    ),
                    array(
                        'class' => 'button-general tres ta-center btn-full-width',
                    )
                );
            }
            ?>
        </div>
    <?php
    }
    if (!empty($distributor_contacts_bdm)) { ?>
        <div class="row p-top-1">
            <div class="anchor_nav aag-subtitle" id="bdm_anchor"><?php echo __t('Distributor.BDM'); ?></div>
            <div class="o-auto">
                <table class="table-tracking table-responsive z-index-priority tabla-limitada">
                    <thead>
                        <tr>
                            <th class="first-name"><?php echo __t('Distributor.First_name'); ?></th>
                            <th class="last-name"><?php echo __t('Distributor.Last_name'); ?></th>
                            <th class="position"><?php echo __t('Contact.Position'); ?></th>
                            <th class="phone"><?php echo __t('Distributor.Phone'); ?></th>
                            <th class="mobile-phone"><?php echo __t('Contact.Mobile_phone'); ?></th>
                            <th><?php echo __t('Distributor.Email'); ?></th>
                            <th><?php echo __t('Network.Distributor_networks'); ?></th>
                            <th><?php echo __t('Network.Garage_networks'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($distributor_contacts_bdm as $contact_bdm) { ?>
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
                                <td width="1" style="width: 1px;" class="ws-nowrap">
                                    <?php echo h($contact_bdm['Contact']['email']); ?>
                                </td>
                                <td>
                                    <?php
                                    if ($contact_bdm['DistributorNetwork']) {
                                        foreach ($contact_bdm['DistributorNetwork'] as $network) {
                                    ?>
                                            <img class="logotipo2" title="<?php echo h($network['DistributorNetwork']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['DistributorNetwork']['image']); ?>" />
                                    <?php
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($contact_bdm['Network']) {
                                        foreach ($contact_bdm['Network'] as $network) {
                                    ?>
                                            <img class="logotipo2" title="<?php echo h($network['Network']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']); ?>" />
                                    <?php
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php
            if (count($distributor_contacts_bdm) == 10) {
                echo $this->Html->link(
                    __t('Distributor.View_more'),
                    array(
                        'action' => 'add_contacts_bdm',
                        $distributor['Distributor']['id']
                    ),
                    array(
                        'class' => 'button-general tres ta-center btn-full-width',
                    )
                );
            }
            ?>
        </div>
    <?php
    }
    if (!empty($distributor_contacts_staff)) {
    ?>
        <div class="row p-top-1">
            <div class="anchor_nav aag-subtitle" id="staff_anchor"><?php echo __t('Distributor.Staff'); ?></div>
            <div class="o-auto">
                <table class="table-tracking table-responsive tabla-limitada">
                    <thead>
                        <tr>
                            <th class="first-name"><?php echo __t('Distributor.First_name'); ?></th>
                            <th class="last-name"><?php echo __t('Distributor.Last_name'); ?></th>
                            <th class="position"><?php echo __t('Contact.Position'); ?></th>
                            <th class="phone"><?php echo __t('Distributor.Phone'); ?></th>
                            <th class="mobile-phone"><?php echo __t('Contact.Mobile_phone'); ?></th>
                            <th><?php echo __t('Distributor.Email'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($distributor_contacts_staff as $contact_staff) { ?>
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
            if (count($distributor_contacts_staff) == 10) {
                echo $this->Html->link(
                    __t('Distributor.View_more'),
                    array(
                        'action' => 'add_contacts_staff',
                        $distributor['Distributor']['id']
                    ),
                    array(
                        'class' => 'button-general tres ta-center btn-full-width',
                    )
                );
            }
            ?>
        </div>
    <?php
    }
    if (!empty($distributor_comments)) {
    ?>
        <div class="row p-top-1">
            <div class="anchor_nav aag-subtitle" id="comments_anchor"><?php echo __t('Distributor.Comments'); ?></div>
            <div class="o-auto">
                <table class="table-tracking table-responsive">
                    <thead>
                        <tr>
                            <th><?php echo __t('Garage.Username'); ?></th>
                            <th><?php echo __t('Garage.Body'); ?></th>
                            <th width="150"><?php echo __t('Garage.Creation_date'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($distributor_comments as $comment) { ?>
                            <tr>
                                <td>
                                    <?php echo h($comment['User']['name']); ?>
                                </td>
                                <td>
                                    <?php
                                    $str = h($comment['DistributorComment']['body']);
                                    if (strlen($str) > 250) {
                                        $str = substr($str, 0, 245) . '...';
                                    }
                                    echo $str;
                                    ?>
                                </td>
                                <td>
                                    <?php echo Fecha::toFormatoVistaFecha(h($comment['DistributorComment']['creation_date'])); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php
            if (count($distributor_comments) == 5) {
                echo $this->Html->link(
                    __t('Distributor.View_more'),
                    array(
                        'action' => 'add_comments',
                        $distributor['Distributor']['id']
                    ),
                    array(
                        'class' => 'button-general tres ta-center btn-full-width',
                    )
                );
            }
            ?>
        </div>
    <?php } ?>
    <div>
        <?php if (!empty($distributor_garages)) { ?>
            <div class="flex ai-center gap-1 p-top-1">
                <div class="anchor_nav_dis aag-subtitle" id="link_garages"><?php echo __t('Distributor.Link_garages') ?></div>
                <?php
                if ($this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])) {
                    echo $this->Html->link(
                        '',
                        array(
                            'action' => 'home_associated',
                            $distributor['Distributor']['id']
                        ),
                        array(
                            'class' => 'ion-arrow-right-c c-primary'
                        )
                    );
                }
                ?>
            </div>
            <div class="cnt-form-inputs">
                <?php
                $selector_networks = array();
                if (!empty($networks_selected)) {
                    $selector_networks = explode(',', $networks_selected);
                }
                echo $this->Form->input(
                    'network_id',
                    array(
                        'label' => __t('Network.Networks'),
                        'type' => 'select',
                        'class' => 'select2-multiple clear_field',
                        'options' => $network_list,
                        'empty' => true,
                        'id' => 'network_id',
                        'multiple' => true,
                        'data-url' => Router::url(array(
                            'controller' => 'clients',
                            'action' => 'ajax_search_distributors_garages_associated',
                            $distributor['Distributor']['id']
                        )),
                        'selected' => $selector_networks ?? null
                    )
                );
                $selector_status = array();
                if (!empty($status_selected)) {
                    $selector_status = explode(',', $status_selected);
                }
                echo $this->Form->input(
                    'status_id',
                    array(
                        'label' => __t('General.Status'),
                        'type' => 'select',
                        'class' => 'select2-multiple clear_field',
                        'options' => $networks_statuses,
                        'empty' => true,
                        'id' => 'status_id',
                        'multiple' => true,
                        'selected' => $selector_status ?? null
                    )
                );
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="p-top-1" id="ajax_search_distributors_garages_associated">
        <?php echo $this->element('../Distributors/ajax_search_distributors_garages_associated'); ?>
    </div>
    <?php if (!empty($logs_changes)) { ?>
        <div class="row p-top-1">
            <div class="anchor_nav_dis aag-subtitle" id="log_changes_anchor"><?php echo __t('Distributor.Log_changes'); ?>
                <?php
                if (count($logs_changes) > 0) {
                    echo __t('General.Last') . ' ' . count($logs_changes) . ' ' . __t('General.Changes');
                ?>
            </div>
        <?php } ?>
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
                    <?php foreach ($logs_changes as $log_change) { ?>
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
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php
        if (count($logs_changes) == 15) {
            echo $this->Html->link(
                __t('Distributor.View_more'),
                array(
                    'controller' => 'distributors',
                    'action' => 'home_logs_changes',
                    $distributor['Distributor']['id']
                ),
                array(
                    'class' => 'button-general tres ta-center btn-full-width',
                )
            );
        }
        ?>
        </div>
    <?php } ?>
</div>