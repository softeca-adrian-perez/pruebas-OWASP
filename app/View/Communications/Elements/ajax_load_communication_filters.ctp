<?php $action = $this->request->action; ?>
<div class="aag-subtitle p-top-1">
    <?php echo __t('Network.Garage_networks'); ?>
</div>
<div class="cnt-form-inputs cont-services w-100p">
    <?php
    if (!empty($networks)) {
        foreach ($networks as $key_network => $network) {
    ?>
            <label class="<?php if (is_array($subsections_networks) && !array_key_exists($network['Network']['id'], $subsections_networks)) {
                                echo "d-none";
                            } ?>">
                <?php
                $valor = false;
                if ($action == ConstantsActionsNames::EDIT) {
                    if (isset($communications_networks)) {
                        foreach ($communications_networks as $key => $communications_network) {
                            if (intval($network['Network']['id']) == $key) {
                                $valor = true;
                            }
                        }
                    }
                }
                echo $this->Form->input(
                    'Network.' . $network['Network']['id'],
                    array(
                        'type' => 'checkbox',
                        'label' => false,
                        'div' => false,
                        'value' => $network['Network']['id'],
                        'checked' => $valor
                    )
                ); ?>
                <span class="unselectable ta-center" title="<?php echo h($network['Network']['name']); ?>">
                    <?php echo $this->Html->image(
                        FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']),
                        array(
                            'alt' => $network['Network']['name']
                        )
                    ); ?>
                </span>
            </label>
    <?php
        }
    }
    ?>
</div>
<div class="cnt-form-inputs-max-width m-top-1 <?php if (isset($subsection['SectionSubsection']['without_networks']) && !$subsection['SectionSubsection']['without_networks']) { echo "d-none";} ?>">
    <?php
    $checked = false;
    if (isset($communication['Communication']['without_networks'])) {
        $checked = $communication['Communication']['without_networks'];
    } elseif (isset($subsection['SectionSubsection']['without_networks'])) {
        $checked = $subsection['SectionSubsection']['without_networks'];
    }
    echo $this->Form->input(
        'Communication.without_networks',
        array(
            'label' => __t('Communication.Without_garage_network'),
            'required' => true,
            'type' => 'checkbox',
            'id' => 'without_garage_network',
            'checked' => $checked
        )
    );
    ?>
</div>
<div class="aag-subtitle p-top-1">
    <?php echo __t('Network.Distributor_networks'); ?>
</div>
<div class="cnt-form-inputs cont-services w-100p">
    <?php
    if (!empty($distributors_networks)) {
        foreach ($distributors_networks as $key_distributor_network => $distributor_networks) {
    ?>
            <label class="<?php if (is_array($subsections_distributors_networks) && !array_key_exists($distributor_networks['DistributorNetwork']['id'], $subsections_distributors_networks)) { echo "d-none"; } ?>">
                <?php
                $valor = false;
                if ($action == ConstantsActionsNames::EDIT) {
                    if (isset($communications_distributors_networks)) {
                        foreach ($communications_distributors_networks as $key => $communication_distributors_networks) {
                            if (intval($distributor_networks['DistributorNetwork']['id']) == $key) {
                                $valor = true;
                            }
                        }
                    }
                }
                echo $this->Form->input(
                    'DistributorNetwork.' . $distributor_networks['DistributorNetwork']['id'],
                    array(
                        'type' => 'checkbox',
                        'label' => false,
                        'div' => false,
                        'value' => $distributor_networks['DistributorNetwork']['id'],
                        'checked' => $valor
                    )
                ); ?>
                <span class="unselectable ta-center" title="<?php echo h($distributor_networks['DistributorNetwork']['name']); ?>">
                    <?php echo $this->Html->image(
                        FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $distributor_networks['DistributorNetwork']['image']),
                        array(
                            'alt' => $distributor_networks['DistributorNetwork']['name']
                        )
                    ); ?>
                </span>
            </label>
    <?php
        }
    }
    ?>
</div>
<div class="cnt-form-inputs-max-width m-top-1 <?php if (isset($subsection['SectionSubsection']['without_distributor_networks']) && !$subsection['SectionSubsection']['without_distributor_networks']) { echo "d-none"; } ?>">
    <?php
        $checked = false;
        if (isset($communication['Communication']['without_distributor_networks'])) {
            $checked = $communication['Communication']['without_distributor_networks'];
        } elseif (isset($subsection['SectionSubsection']['without_distributor_networks'])) {
            $checked = $subsection['SectionSubsection']['without_distributor_networks'];
        }
        echo $this->Form->input(
            'Communication.without_distributor_networks',
            array(
                'label' => __t('Communication.Without_distributor_network'),
                'required' => true,
                'type' => 'checkbox',
                'id' => 'without_distributor_network',
                'checked' => $checked,
            )
        );
    ?>
</div>
<div class="aag-subtitle p-top-1">
    <?php echo __t('Network.Trading_groups'); ?>
</div>
<div class="cnt-form-inputs cont-services w-100p">
    <?php
    if (!empty($trading_groups)) {
        foreach ($trading_groups as $key_trading_group => $trading_group) {
    ?>
            <label class="<?php if (is_array($subsections_trading_groups) && !array_key_exists($trading_group['TradingGroup']['id'], $subsections_trading_groups)) {
                                echo "d-none";
                            } ?>">
                <?php
                $valor = false;
                if ($action == ConstantsActionsNames::EDIT) {
                    if (isset($communications_trading_groups)) {
                        foreach ($communications_trading_groups as $key => $communication_trading_groups) {
                            if (intval($trading_group['TradingGroup']['id']) == $key) {
                                $valor = true;
                            }
                        }
                    }
                }
                echo $this->Form->input(
                    'TradingGroup.' . $trading_group['TradingGroup']['id'],
                    array(
                        'type' => 'checkbox',
                        'label' => false,
                        'div' => false,
                        'value' => $trading_group['TradingGroup']['id'],
                        'checked' => $valor
                    )
                );
                ?>
                <span class="unselectable ta-center" title="<?php echo h($trading_group['TradingGroup']['name']); ?>">
                    <?php echo $this->Html->image(
                        FileManager::get_url(FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $trading_group['TradingGroup']['image']),
                        array(
                            'alt' => $trading_group['TradingGroup']['name']
                        )
                    ); ?>
                </span>
            </label>
    <?php
        }
    }
    ?>
</div>
<div class="aag-subtitle p-top-1">
    <?php echo __t('Distributor.Distributors'); ?>
</div>
<div class="cnt-form-inputs-max-width">
    <div class="<?php if (isset($subsection['SectionSubsection']['aag_member_yes']) && !$subsection['SectionSubsection']['aag_member_yes']) { echo "d-none"; } ?>">
        <?php
            $checked = false;
            if (isset($communication['Communication']['aag_member_yes'])) {
                $checked = $communication['Communication']['aag_member_yes'];
            } elseif (isset($subsection['SectionSubsection']['aag_member_yes'])) {
                $checked = $subsection['SectionSubsection']['aag_member_yes'];
            }
            echo $this->Form->input(
                'Communication.aag_member_yes',
                array(
                    'label' => __t('Communication.Aag_member_yes'),
                    'required' => true,
                    'type' => 'checkbox',
                    'id' => 'is_aag_member_yes',
                    'checked' => $checked,
                )
            );
        ?>
    </div>
    <div class="<?php if (isset($subsection['SectionSubsection']['aag_member_no']) && !$subsection['SectionSubsection']['aag_member_no']) { echo "d-none"; } ?>">
        <?php
            $checked = false;
            if (isset($communication['Communication']['aag_member_no'])) {
                $checked = $communication['Communication']['aag_member_no'];
            } elseif (isset($subsection['SectionSubsection']['aag_member_no'])) {
                $checked = $subsection['SectionSubsection']['aag_member_no'];
            }
            echo $this->Form->input(
                'Communication.aag_member_no',
                array(
                    'label' => __t('Communication.Aag_member_no'),
                    'required' => true,
                    'type' => 'checkbox',
                    'id' => 'is_aag_member_no',
                    'checked' => $checked,
                )
            );
        ?>
    </div>
</div>
<div class="aag-subtitle p-top-1">
    <?php echo __t('Distributor.Profile'); ?>
</div>
<div class="cnt-two-columns">
    <div>
        <div class="aag-subtitle" style="font-weight: normal;">
            <?php echo __t('Garage.Garage'); ?>
        </div>
        <div class="cnt-form-inputs-max-width">
            <?php
            if (!empty($garage_positions)) {
                foreach ($garage_positions as $position) {
                    $valor = false;
                    if ($action == ConstantsActionsNames::EDIT) {
                        $valor = false;
                        if (isset($communications_positions)) {
                            foreach ($communications_positions as $key => $communication_position) {
                                if (intval($position['Position']['id']) == $communication_position['CommunicationPosition']['position_id']) {
                                    $valor = true;
                                }
                            }
                        }
                    }
            ?>
                    <div class="<?php if (is_array($subsections_positions) && !in_array($position['Position']['id'], $subsections_positions)) {
                                    echo "d-none";
                                } ?>">
                        <?php
                        echo $this->Form->input(
                            'GaragePosition.' . $position['Position']['id'],
                            array(
                                'type' => 'checkbox',
                                'label' => __t($position['Position']['name' . __s()]),
                                'div' => false,
                                'checked' => $valor
                            )
                        ); ?>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </div>
    <div>
        <div class="aag-subtitle" style="font-weight: normal;">
            <?php echo __t('Distributor.Distributor'); ?>
        </div>
        <div class="cnt-form-inputs-max-width">
            <?php
            if (!empty($distributor_positions)) {
                foreach ($distributor_positions as $position) {
                    $valor = false;
                    if ($action == ConstantsActionsNames::EDIT) {
                        $valor = false;
                        if (isset($communications_positions)) {
                            foreach ($communications_positions as $key => $communication_position) {
                                if (intval($position['Position']['id']) == $communication_position['CommunicationPosition']['position_id']) {
                                    $valor = true;
                                }
                            }
                        }
                    }
            ?>
                    <div class="<?php if (is_array($subsections_positions) && !in_array($position['Position']['id'], $subsections_positions)) {
                                    echo "d-none";
                                } ?>">
                        <?php
                        echo $this->Form->input(
                            'DistributorPosition.' . $position['Position']['id'],
                            array(
                                'type' => 'checkbox',
                                'label' => __t($position['Position']['name' . __s()]),
                                'div' => false,
                                'checked' => $valor
                            )
                        ); ?>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </div>
</div>
<div class="aag-subtitle p-top-1">
    <?php echo __t('Activity.Activity'); ?>
</div>
<div class="cnt-form-inputs-max-width m-bottom-1">
    <?php
    if (!empty($customers_activities)) {
        foreach ($customers_activities as $activity_id => $activity_name) {
            $valor = false;
            if ($action == ConstantsActionsNames::EDIT) {
                $valor = false;
                if (isset($communications_activities)) {
                    foreach ($communications_activities as $key => $communication_activity) {
                        if ($activity_id == $communication_activity['CommunicationCustomerActivity']['customer_activity_id']) {
                            $valor = true;
                        }
                    }
                }
            }
    ?>
            <div class="<?php if (is_array($subsections_customers_activities) && !in_array($activity_id, $subsections_customers_activities)) {
                            echo "d-none";
                        } ?>">
                <?php
                echo $this->Form->input(
                    'Activity.' . $activity_id,
                    array(
                        'type' => 'checkbox',
                        'label' => __t($activity_name),
                        'div' => false,
                        'checked' => $valor
                    )
                );
                ?>
            </div>
        <?php
        }
        ?>
        <div class="<?php if (isset($subsection['SectionSubsection']['without_activity']) && !$subsection['SectionSubsection']['without_activity']) {
                        echo "d-none";
                    } ?>">
            <?php
            echo $this->Form->input(
                'Communication.without_activity',
                array(
                    'type' => 'checkbox',
                    'label' => __t('Communication.Without_activity'),
                    'div' => false,
                )
            ); ?>
        </div>
    <?php
    }
    ?>
</div>