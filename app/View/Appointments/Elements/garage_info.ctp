<?php
$action = $this->request->action;
$customer_name = null;
$customer_id = null;
$link = null;
if (isset($garage['Garage'])) {
    $customer_name = $garage['Garage']['name'];
    $customer_id = $garage['Garage']['id'];
    $action_go_to = 'report';
} elseif (isset($distributor['Distributor'])) {
    $customer_name = $distributor['Distributor']['name'];
    $customer_id = $distributor['Distributor']['id'];
    $action_go_to = 'report_distributor';
}
?>
<div class="medium-12 columns p-0" id="title_garage" style="border-bottom: 10px solid #f0f5ff;">
    <div class="medium-12 columns ta-left p-0 cnt-buttons-link">
        <div class="go-to fw-bold">
            <?php
            if (isset($garage['Garage']) || isset($distributor['Distributor'])) {
                echo $this->Html->link(
                    __t('Appointment.Data_customer') . "<span class='ion-arrow-right-c'></span>",
                    array(
                        'controller' => 'clients',
                        'action' => $action_go_to,
                        $customer_id
                    ),
                    array(
                        'escape' => false,
                        'id' => 'go-to-garage',
                        'title' => __t('General.Open_in_a_new_tab'),
                        'target' => '_blank'
                    )
                );
            }
            if (isset($distributor['Distributor'])) {
                echo $this->Html->link(
                    __t('CRM.Visit_history') . "<span class='ion-arrow-right-c'></span>",
                    array(
                        'controller' => 'clients',
                        'action' => 'tracking_distributor',
                        $customer_id
                    ),
                    array(
                        'escape' => false,
                        'id' => 'go-to-garage',
                        'title' => __t('General.Open_in_a_new_tab'),
                        'target' => '_blank',
                        'style' => 'padding-left: 1em;'
                    )
                );
            }
            ?>
        </div>
        <span id="button_kpi" title="<?php echo __t('Sales.KPI'); ?>" class="ion-arrow-graph-up-right cursor-pointer btn-link active-button">
        </span>
        <span id="button_data" title="<?php echo __t('Appointment.Data_customer'); ?>" class="ion-stats-bars cursor-pointer btn-link">
        </span>
        <span id="button_img" title="<?php echo __t('Appointment.Image'); ?>" class="ion-ios-camera-outline cursor-pointer btn-link">
        </span>
        <?php if (isset($garage['Garage'])) { ?>
            <span id="button_map" title="<?php echo __t('General.Map'); ?>" class="ion-map cursor-pointer btn-link
                <?php if (empty($garage['Garage']['latitude']) || empty($garage['Garage']['longitude'])) {
                    echo 'no-coordinates';
                } ?>">
            </span>
        <?php } elseif (isset($distributor['Distributor'])) { ?>
            <span id="button_map" title="<?php echo __t('General.Map'); ?>" class="ion-map cursor-pointer btn-link
                <?php if (empty($distributor['Distributor']['latitude']) || empty($distributor['Distributor']['longitude'])) {
                    echo 'no-coordinates';
                } ?>">
            </span>
        <?php } else { ?>
            <span id="button_map" title="<?php echo __t('General.Map'); ?>" class="ion-map cursor-pointer btn-link no-coordinates">
            </span>
        <?php } ?>
    </div>
</div>
<div class="medium-12 columns p-0 btn-info" id="info_kpi">
    <?php if (!empty($distributor)) { ?>
        <div class="columns medium-12 p-1">
            <b class="fs-medium" style="margin-bottom: .5rem;">
                <span>
                    <?php echo __t('Distributor.Trading_group'); ?>
                </span>
            </b>
            <div class="row p-left-1 ta-center">
                <div class="columns medium-12">
                    <?php if (isset($distributor['TradingGroup']) && !empty($distributor['TradingGroup'])) { ?>
                        <div class="columns medium-4 end p-right-1">
                            <img title="<?php echo h($distributor['TradingGroup']['name']); ?>" src="<?php echo FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $distributor['TradingGroup']['image']; ?>" style="max-width: 100px" />
                            <br />
                            <b class="fs-medium" style="margin-bottom: .5rem;">
                                <?php echo h($distributor['TradingGroup']['name']); ?>
                            </b>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="columns medium-12 p-0 p-left-1">
        <b class="fs-medium" style="margin-bottom: .5rem;">
            <?php echo __t('Sales.Latest_visit') . ' :'; ?>
        </b>
        <b class="m-left-1 c-negro fs-medium" style="margin-bottom: .5rem;">
            <?php
            if (isset($garage['Garage']['last_visit'])) {
                echo ($garage['Garage']['last_visit']) ? Fecha::toFormatoVistaFecha($garage['Garage']['last_visit']) : null;
            } elseif (isset($distributor['Distributor']['last_visit'])) {
                echo ($distributor['Distributor']['last_visit']) ? Fecha::toFormatoVistaFecha($distributor['Distributor']['last_visit']) : null;
            }
            ?>
        </b>
    </div>
    <div class="columns medium-12 p-1">
        <div id="distributor-inputs-js" class="columns medium-12" <?php if (empty($distributor)) {
                                                                        echo 'hidden';
                                                                    } ?>>
            <div class="columns medium-12 p-left-0">
                <b class="fs-medium" style="margin-bottom: .5rem;">
                    <?php echo __t('Appointment.Visit_count_ltm') . ' :'; ?>
                </b>
                <b class="m-left-1 c-negro fs-medium" style="margin-bottom: .5rem;">
                    <?php echo isset($ltm) ? $ltm : ''; ?>
                </b>
            </div>
            <div class="columns medium-12 p-left-0">
                <b class="fs-medium" style="margin-bottom: .5rem;">
                    <?php echo __t('Sales.Figures'); ?>
                </b>
            </div>
            <div id="mtd-js" class="columns medium-6">
                <?php
                echo $this->Form->input(
                    'Appointment.mtd',
                    array(
                        'type' => 'text',
                        'required' => false,
                        'label' => 'MTD +/-',
                        'class' => 'disabled_fields_hidden',
                    )
                );
                ?>
            </div>
            <div id="qtd-js" class="clear columns medium-6">
                <?php
                echo $this->Form->input(
                    'Appointment.qtd',
                    array(
                        'type' => 'text',
                        'required' => false,
                        'label' => 'QTD +/-',
                        'class' => 'disabled_fields_hidden'
                    )
                );
                ?>
            </div>
            <div id="ytd-js" class="clear columns medium-6 end">
                <?php
                echo $this->Form->input(
                    'Appointment.ytd',
                    array(
                        'type' => 'text',
                        'required' => false,
                        'label' => 'YTD +/-',
                        'class' => 'disabled_fields_hidden'
                    )
                );
                ?>
            </div>
        </div>
    </div>
</div>
<div class="medium-12 columns p-0 btn-info hide_element" id="info_data">
    <?php if (!empty($garage)) { ?>
        <div class="columns medium-12 p-1">
            <h2>
                <span>
                    <?php echo __t('Distributor.Distributor'); ?>
                </span>
            </h2>
            <div class="row p-left-1">
                <div class="columns medium-12" style=" font-size: small;">
                    <?php
                    if (isset($garage['GarageDistributor']) && !empty($garage['GarageDistributor'])) {
                        foreach ($garage['GarageDistributor'] as $distributor) {
                    ?>
                            <div class="columns medium-6 end">
                                <strong>
                                    <?php
                                    if ($distributor['GarageDistributor']['principal']) {
                                        echo __t('Distributor.Principal') . ': ';
                                    }
                                    echo h($distributor['Distributor']['name']) . ' - ' . h($distributor['Distributor']['account_number']);
                                    ?>
                                </strong>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="columns medium-12 p-left-1 p-top-0">
            <h2>
                <span>
                    <?php echo __t('Network.Network'); ?>
                </span>
            </h2>
            <div class="row ">
                <div class="columns medium-12 p-1" style=" font-size: small;">
                    <?php
                    if (isset($garage['GarageNetwork']) && !empty($garage['GarageNetwork'])) {
                        $network_status = Configure::read('Network_Status');
                        foreach ($network_status as $key => $network_status_tmp) {
                            $network_status[$key] = __t($network_status_tmp);
                        }
                        foreach ($garage['GarageNetwork'] as $network) {
                    ?>
                            <div class="columns medium-4 end p-right-1 ta-center">
                                <img title="<?php echo h($network['Network']['name']); ?>" src="<?php echo FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']; ?>" style="max-width: 85px" />
                                <br />
                                <?php
                                if ($network['GarageNetwork']['status'] == ConstantsNetworksStatus::UNSUBSCRIBE || $network['GarageNetwork']['status'] == ConstantsNetworksStatus::NOT_CONVERTED) {
                                    $class = 'c-fallo';
                                } elseif ($network['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                                    $class = 'c-exito';
                                } else {
                                    $class = 'c-informacion';
                                }
                                ?>
                                <strong class="<?php echo $class; ?>">
                                    <?php echo h($network_status[$network['GarageNetwork']['status']]); ?>
                                </strong>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="columns medium-12 p-1">
            <h2>
                <span>
                    <?php echo __t('Garage.Opening_times'); ?>
                </span>
            </h2>
        </div>
        <div class="columns medium-12 m-bottom-1">
            <fieldset class="columns fieldset-garage-list background-color-secondary" style="font-size: small;">
                <div class="grid-x grid-padding-x p-top-1 p-left-1 p-right-1">
                    <div class="columns medium-6 large-3" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Monday') ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($garage['Garage']['monday_open_1']) && empty($garage['Garage']['monday_closed_1']) && empty($garage['Garage']['monday_open_2']) && empty($garage['Garage']['monday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['monday_open_1'])) {
                                echo h($garage['Garage']['monday_open_1']) . ' - ' . h($garage['Garage']['monday_closed_1']) . '<br />';
                            }
                            if (!empty($garage['Garage']['monday_open_2'])) {
                                echo h($garage['Garage']['monday_open_2']) . ' - ' . h($garage['Garage']['monday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="columns medium-6 large-3" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Tuesday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($garage['Garage']['tuesday_open_1']) && empty($garage['Garage']['tuesday_closed_1']) && empty($garage['Garage']['tuesday_open_2']) && empty($garage['Garage']['tuesday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['tuesday_open_1'])) {
                                echo h($garage['Garage']['tuesday_open_1']) . ' - ' . h($garage['Garage']['tuesday_closed_1']) . '<br />';
                            }
                            if (!empty($garage['Garage']['tuesday_open_2'])) {
                                echo h($garage['Garage']['tuesday_open_2']) . ' - ' . h($garage['Garage']['tuesday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="columns medium-6 large-3" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Wednesday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($garage['Garage']['wednesday_open_1']) && empty($garage['Garage']['wednesday_closed_1']) && empty($garage['Garage']['wednesday_open_2']) && empty($garage['Garage']['wednesday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['wednesday_open_1'])) {
                                echo h($garage['Garage']['wednesday_open_1']) . ' - ' . h($garage['Garage']['wednesday_closed_1']) . '<br />';
                            }
                            if (!empty($garage['Garage']['wednesday_open_2'])) {
                                echo h($garage['Garage']['wednesday_open_2']) . ' - ' . h($garage['Garage']['wednesday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="columns medium-6 large-3" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Thursday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($garage['Garage']['thursday_open_1']) && empty($garage['Garage']['thursday_closed_1']) && empty($garage['Garage']['thursday_open_2']) && empty($garage['Garage']['thursday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['thursday_open_1'])) {
                                echo h($garage['Garage']['thursday_open_1']) . ' - ' . h($garage['Garage']['thursday_closed_1']) . '<br />';
                            }
                            if (!empty($garage['Garage']['thursday_open_2'])) {
                                echo h($garage['Garage']['thursday_open_2']) . ' - ' . h($garage['Garage']['thursday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                </div>
                <div class="grid-x grid-padding-x p-left-1 p-right-1">
                    <div class="columns medium-6 large-3" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Friday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($garage['Garage']['friday_open_1']) && empty($garage['Garage']['friday_closed_1']) && empty($garage['Garage']['friday_open_2']) && empty($garage['Garage']['friday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['friday_open_1'])) {
                                echo h($garage['Garage']['friday_open_1']) . ' - ' . h($garage['Garage']['friday_closed_1']) . '<br />';
                            }
                            if (!empty($garage['Garage']['friday_open_2'])) {
                                echo h($garage['Garage']['friday_open_2']) . ' - ' . h($garage['Garage']['friday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="columns medium-6 large-3 end" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Saturday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($garage['Garage']['saturday_open_1']) && empty($garage['Garage']['saturday_closed_1']) && empty($garage['Garage']['saturday_open_2']) && empty($garage['Garage']['saturday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['saturday_open_1'])) {
                                echo h($garage['Garage']['saturday_open_1']) . ' - ' . h($garage['Garage']['saturday_closed_1']) . '<br />';
                            }
                            if (!empty($garage['Garage']['saturday_open_2'])) {
                                echo h($garage['Garage']['saturday_open_2']) . ' - ' . h($garage['Garage']['saturday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="columns medium-6 large-3 end" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Sunday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($garage['Garage']['sunday_open_1']) && empty($garage['Garage']['sunday_closed_1']) && empty($garage['Garage']['sunday_open_2']) && empty($garage['Garage']['sunday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($garage['Garage']['sunday_open_1'])) {
                                echo h($garage['Garage']['sunday_open_1']) . ' - ' . h($garage['Garage']['sunday_closed_1']) . '<br />';
                            }
                            if (!empty($garage['Garage']['sunday_open_2'])) {
                                echo h($garage['Garage']['sunday_open_2']) . ' - ' . h($garage['Garage']['sunday_closed_2']) . '<br />';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </fieldset>
        </div>
    <?php } elseif (!empty($distributor)) { ?>
        <div class="columns medium-12 p-left-1 p-top-1">
            <h2>
                <span>
                    <?php echo __t('Activity.Activities'); ?>
                </span>
            </h2>
            <div class="row p-left-1">
                <div class="columns medium-12" style=" font-size: small;">
                    <?php
                    if (isset($distributor['DistributorActivity']) && !empty($distributor['DistributorActivity'])) {
                        foreach ($distributor['DistributorActivity'] as $activity) {
                    ?>
                            <div class="columns medium-3 end">
                                <strong>
                                    <?php echo h($customer_activities[$activity['DistributorCustomerActivity']['customer_activity_id']]); ?>
                                </strong>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="columns medium-12 p-1">
            <h2>
                <span>
                    <?php echo __t('Garage.Opening_times'); ?>
                </span>
            </h2>
        </div>
        <div class="columns medium-12 m-bottom-1">
            <fieldset class="columns fieldset-garage-list background-color-secondary" style="font-size: small;">
                <div class="row p-1">
                    <div class="columns medium-3 ta-center">
                        <strong>
                            <?php echo __t('Garage.Monday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($distributor['Distributor']['monday_open_1']) && empty($distributor['Distributor']['monday_closed_1']) && empty($distributor['Distributor']['monday_open_2']) && empty($distributor['Distributor']['monday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($distributor['Distributor']['monday_open_1'])) {
                                echo h($distributor['Distributor']['monday_open_1']) . ' - ' . h($distributor['Distributor']['monday_closed_1']) . '<br />';
                            }
                            if (!empty($distributor['Distributor']['monday_open_2'])) {
                                echo h($distributor['Distributor']['monday_open_2']) . ' - ' . h($distributor['Distributor']['monday_closed_2']) . '<br />';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="columns medium-3 ta-center">
                        <strong>
                            <?php echo __t('Garage.Tuesday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($distributor['Distributor']['tuesday_open_1']) && empty($distributor['Distributor']['tuesday_closed_1']) && empty($distributor['Distributor']['tuesday_open_2']) && empty($distributor['Distributor']['tuesday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($distributor['Distributor']['tuesday_open_1'])) {
                                echo h($distributor['Distributor']['tuesday_open_1']) . ' - ' . h($distributor['Distributor']['tuesday_closed_1']) . '<br />';
                            }
                            if (!empty($distributor['Distributor']['tuesday_open_2'])) {
                                echo h($distributor['Distributor']['tuesday_open_2']) . ' - ' . h($distributor['Distributor']['tuesday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="columns medium-3 ta-center">
                        <strong>
                            <?php echo __t('Garage.Wednesday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($distributor['Distributor']['wednesday_open_1']) && empty($distributor['Distributor']['wednesday_closed_1']) && empty($distributor['Distributor']['wednesday_open_2']) && empty($distributor['Distributor']['wednesday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($distributor['Distributor']['wednesday_open_1'])) {
                                echo h($distributor['Distributor']['wednesday_open_1']) . ' - ' . h($distributor['Distributor']['wednesday_closed_1']) . '<br />';
                            }
                            if (!empty($distributor['Distributor']['wednesday_open_2'])) {
                                echo h($distributor['Distributor']['wednesday_open_2']) . ' - ' . h($distributor['Distributor']['wednesday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="columns medium-3 ta-center">
                        <strong>
                            <?php echo __t('Garage.Thursday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($distributor['Distributor']['thursday_open_1']) && empty($distributor['Distributor']['thursday_closed_1']) && empty($distributor['Distributor']['thursday_open_2']) && empty($distributor['Distributor']['thursday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($distributor['Distributor']['thursday_open_1'])) {
                                echo h($distributor['Distributor']['thursday_open_1']) . ' - ' . h($distributor['Distributor']['thursday_closed_1']) . '<br />';
                            }
                            if (!empty($distributor['Distributor']['thursday_open_2'])) {
                                echo h($distributor['Distributor']['thursday_open_2']) . ' - ' . h($distributor['Distributor']['thursday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                </div>
                <div class="row p-1 ">
                    <div class="columns medium-4 ta-center">
                        <strong>
                            <?php echo __t('Garage.Friday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($distributor['Distributor']['friday_open_1']) && empty($distributor['Distributor']['friday_closed_1']) && empty($distributor['Distributor']['friday_open_2']) && empty($distributor['Distributor']['friday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($distributor['Distributor']['friday_open_1'])) {
                                echo h($distributor['Distributor']['friday_open_1']) . ' - ' . h($distributor['Distributor']['friday_closed_1']) . '<br />';
                            }
                            if (!empty($distributor['Distributor']['friday_open_2'])) {
                                echo h($distributor['Distributor']['friday_open_2']) . ' - ' . h($distributor['Distributor']['friday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="columns medium-4 ta-center end">
                        <strong>
                            <?php echo __t('Garage.Saturday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($distributor['Distributor']['saturday_open_1']) && empty($distributor['Distributor']['saturday_closed_1']) && empty($distributor['Distributor']['saturday_open_2']) && empty($distributor['Distributor']['saturday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($distributor['Distributor']['saturday_open_1'])) {
                                echo h($distributor['Distributor']['saturday_open_1']) . ' - ' . h($distributor['Distributor']['saturday_closed_1']) . '<br />';
                            }
                            if (!empty($distributor['Distributor']['saturday_open_2'])) {
                                echo h($distributor['Distributor']['saturday_open_2']) . ' - ' . h($distributor['Distributor']['saturday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="columns medium-4 ta-center">
                        <strong>
                            <?php echo __t('Garage.Sunday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input ">
                            <?php
                            if (empty($distributor['Distributor']['sunday_open_1']) && empty($distributor['Distributor']['sunday_closed_1']) && empty($distributor['Distributor']['sunday_open_2']) && empty($distributor['Distributor']['sunday_closed_2'])) {
                                echo __t('Garage.Closed');
                            }
                            if (!empty($distributor['Distributor']['sunday_open_1'])) {
                                echo h($distributor['Distributor']['sunday_open_1']) . ' - ' . h($distributor['Distributor']['sunday_closed_1']) . '<br />';
                            }
                            if (!empty($distributor['Distributor']['sunday_open_2'])) {
                                echo h($distributor['Distributor']['sunday_open_2']) . ' - ' . h($distributor['Distributor']['sunday_closed_2']);
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </fieldset>
        </div>
    <?php } else { ?>
        <div class="columns medium-12 p-1">
            <h2>
                <span>
                    <?php echo __t('Distributor.Distributor'); ?>
                </span>
            </h2>
            <div class="row p-left-1">
                <div class="columns medium-12" style=" font-size: small;">
                </div>
            </div>
        </div>
        <div class="columns medium-12 p-left-1 p-top-0">
            <h2>
                <span>
                    <?php echo __t('Network.Network'); ?>
                </span>
            </h2>
            <div class="row ">
                <div class="columns medium-12 p-1" style=" font-size: small;">
                </div>
            </div>
        </div>
        <div class="columns medium-12 p-1">
            <h2>
                <span>
                    <?php echo __t('Garage.Opening_times'); ?>
                </span>
            </h2>
        </div>
        <div class="columns medium-12 m-bottom-1">
            <fieldset class="columns fieldset-garage-list background-color-secondary" style="font-size: small;">
                <div class="grid-x grid-padding-x p-top-1 p-left-1 p-right-1">
                    <div class="columns medium-6 large-3" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Monday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input">
                        </span>
                    </div>
                    <div class="columns medium-6 large-3" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Tuesday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input">
                        </span>
                    </div>
                    <div class="columns medium-6 large-3" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Wednesday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input">
                        </span>
                    </div>
                    <div class="columns medium-6 large-3" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Thursday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input">
                        </span>
                    </div>
                </div>
                <div class="grid-x grid-padding-x p-left-1 p-right-1">
                    <div class="columns medium-6 large-3" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Friday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input">
                        </span>
                    </div>
                    <div class="columns medium-6 large-3 end" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Saturday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input">
                        </span>
                    </div>
                    <div class="columns medium-6 large-3 end" style="padding-bottom: 1rem;">
                        <strong>
                            <?php echo __t('Garage.Sunday'); ?>:
                        </strong>
                        <br />
                        <span class="b-bottom-1 height_input">
                        </span>
                    </div>
                </div>
            </fieldset>
        </div>
    <?php
    }
    ?>
</div>
<div class="medium-12 columns p-0 btn-info hide_element" id="info_img">
    <div class="medium-12 columns m-0-auto ta-center p-0" style="background-color: #f6f7f8;">
        <?php
        if (
            (!isset($garage_principal_image['GarageImage']) || $garage_principal_image['GarageImage']['file'] == "") &&
            (!isset($distributor_principal_image['DistributorImage']) || $distributor_principal_image['DistributorImage']['file'] == "")
        ) {
        ?>
            <img src="<?php echo '/img/default.png' ?>" style="max-height: 200px;" />
        <?php
        } elseif (isset($garage_principal_image['GarageImage']) && $garage_principal_image['GarageImage']['file'] != "") {
            echo $this->Html->image(
                Router::url(
                    array(
                        'controller' => 'garages_images',
                        'action' => 'download_file',
                        $garage_principal_image['GarageImage']['id']
                    )
                ),
                array(
                    'alt' => __t('Garage.Picture_not_loaded'),
                    'style' => 'max-height: 200px;'
                )
            );
        } elseif (isset($distributor_principal_image['DistributorImage']) && $distributor_principal_image['DistributorImage']['file'] != "") {
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
                    'style' => 'max-height: 200px;'
                )
            );
        }
        ?>
    </div>
    <div class="medium-12 columns item-taller d-inline-block ta-center p-1">
        <span class="ion-ios-location-outline icon" title="<?php echo __t('Garage.Address') ?>">
        </span>
        <?php
        if (isset($garage['Garage'])) {
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
            if (!empty($garage['Garage']['postcode'])) {
                echo ", " . h($garage['Garage']['postcode']);
            }
            if (!empty($garage['Garage']['town'])) {
                echo ", " . h($garage['Garage']['town']);
            }
        } elseif (isset($distributor['Distributor'])) {
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
            if (!empty($distributor['Distributor']['postcode'])) {
                echo ", " . h($distributor['Distributor']['postcode']);
            }
            if (!empty($distributor['Distributor']['town'])) {
                echo ", " . h($distributor['Distributor']['town']);
            }
        }
        ?>
    </div>
    <div class="medium-12 columns item-taller d-inline-block ta-center p-1">
        <span class="ion-ios-person-outline icon" title="<?php echo __t('Garage.Leaders') ?>"></span>
        <?php
        if (isset($garage_manager) && $garage_manager) {
            $i = 1;
            foreach ($garage_manager as $manager) {
                echo h($manager['Contact']['first_name']) . ' ' . h($manager['Contact']['last_name']);
                if ($i < count($garage_manager)) {
                    echo ' - ';
                }
                $i++;
            }
        } elseif (isset($distributor_manager) && $distributor_manager) {
            $i = 1;
            foreach ($distributor_manager as $manager) {
                echo h($manager['Contact']['first_name']) . ' ' . h($manager['Contact']['last_name']);
                if ($i < count($distributor_manager)) {
                    echo ' - ';
                }
                $i++;
            }
        }
        ?>
    </div>
    <div class="medium-12 columns item-taller d-inline-block ta-center p-1">
        <span class="ion-ios-telephone-outline icon" title="<?php echo __t('Garage.Phone') ?>"></span>
        <?php
        if (isset($garage['Garage']['phone'])) {
            echo h($garage['Garage']['phone']);
        } elseif (isset($distributor['Distributor']['phone'])) {
            echo h($distributor['Distributor']['phone']);
        }
        ?>
    </div>
</div>
<div class="medium-12 columns p-0 btn-info hide_element" id="info_map">
    <?php if (!empty($garage['Garage']['latitude']) && !empty($garage['Garage']['longitude'])) { ?>
        <div id="map-single-without-info" class="contenedor-mapa" style="height: 200px;" data-editable="<?php echo ConstantsBooleans::NO; ?>" data-latitude="<?php echo $garage['Garage']['latitude'] ?>" data-longitude="<?php echo $garage['Garage']['longitude'] ?>"></div>
    <?php } elseif (!empty($distributor['Distributor']['latitude']) && !empty($distributor['Distributor']['longitude'])) { ?>
        <div id="map-single-without-info" class="contenedor-mapa" style="height: 200px;" data-editable="<?php echo ConstantsBooleans::NO; ?>" data-latitude="<?php echo $distributor['Distributor']['latitude'] ?>" data-longitude="<?php echo $distributor['Distributor']['longitude'] ?>"></div>
    <?php } else { ?>
        <div class="ta-center b-c" style="height: 200px;background-color: lightgrey"></div>
    <?php } ?>
    <div class="alliance_bar"></div>
    <div class="medium-12 columns item-taller d-inline-block ta-center p-1">
        <span class="ion-ios-location-outline icon" title="<?php echo __t('Garage.Address') ?>"></span>
        <?php
        if (isset($garage['Garage'])) {
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
            if (!empty($garage['Garage']['postcode'])) {
                echo ", " . h($garage['Garage']['postcode']);
            }
            if (!empty($garage['Garage']['town'])) {
                echo ", " . h($garage['Garage']['town']);
            }
        } elseif (isset($distributor['Distributor'])) {
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
            if (!empty($distributor['Distributor']['postcode'])) {
                echo ", " . h($distributor['Distributor']['postcode']);
            }
            if (!empty($distributor['Distributor']['town'])) {
                echo ", " . h($distributor['Distributor']['town']);
            }
        }
        ?>
    </div>
    <div class="medium-12 columns item-taller d-inline-block ta-center p-1">
        <span class="ion-ios-person-outline icon" title="<?php echo __t('Garage.Leaders') ?>"></span>
        <?php
        if (isset($garage_manager[0])) {
            foreach ($garage_manager as $manager) {
                echo h($manager['Contact']['first_name']) . ' ' . h($manager['Contact']['last_name']);
            }
        } elseif (isset($distributor_manager[0])) {
            foreach ($distributor_manager as $manager) {
                echo h($manager['Contact']['first_name']) . ' ' . h($manager['Contact']['last_name']);
            }
        }
        ?>
    </div>
    <div class="medium-12 columns item-taller d-inline-block ta-center p-1">
        <span class="ion-ios-telephone-outline icon" title="<?php echo __t('Garage.Phone') ?>"></span>
        <?php
        if (isset($garage['Garage']['phone'])) {
            echo h($garage['Garage']['phone']);
        } elseif (isset($distributor['Distributor']['phone'])) {
            echo h($distributor['Distributor']['phone']);
        }
        ?>
    </div>
</div>