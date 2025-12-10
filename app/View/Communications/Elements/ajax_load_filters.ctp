<?php $action = $this->request->action; ?>

<div class="columns medium-12 titulo2 p-0 p-top-1">
            <?php echo __t('Section.Filters'); ?>
        </div>
        <div class="columns medium-12 p-form">
            <div class="columns medium-12 titulo2 p-left-1">
                <?php echo __t('Network.Garage_networks'); ?>
            </div>
            <div class="columns medium-12 p-top-1">
                <div class="d-inline-block cont-services w-100p">
                    <?php
                    if(!empty($networks)){
                        foreach ($networks as $key_network => $network) {
                            ?>
                            <div class="medium-2 columns end <?php if (!array_key_exists($network['Network']['id'],$sections_networks)) { echo "d-none"; } ?> <?php if ($key_network % 6 == 0) { echo "clear"; } ?>">
                                <label>
                                    <?php
                                    $valor = false;
                                    if ($action == 'edit_subsection') {
                                        if (isset($sections_subsections_networks)) {
                                            foreach ($sections_subsections_networks as $key => $sections_subsections_network) {
                                                if (intval($network['Network']['id']) == $key) {
                                                    $valor = true;
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    <?php
                                    
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
                                        );?>
                                    </span>
                                </label>
                            </div>
                        <?php 
                        } 
                    }?>
                </div>
            </div>
            <div class="columns medium-12 p-left-1 clear end <?php if( !$section['CommunicationSection']['without_networks']){ echo "d-none"; } ?>">
                <div class="columns medium-12 p-left-1 clear end">
                <?php 
                echo $this->Form->input(
                    'without_networks',
                    array(
                        'label' => __t('Communication.Without_garage_network'),
                        'required' => true,
                        'type' => 'checkbox',
                        'id' => 'without_garage_network',
                        'checked' => isset($section_subsection) ?  $section_subsection['SectionSubsection']['without_networks'] : true,
                    )
                ); ?>
                </div>
            </div>
        </div>
        <div class="columns medium-12 p-form">
            <div class="columns medium-12 titulo2 p-left-1">
                <?php echo __t('Network.Distributor_networks'); ?>
            </div>
            <div class="columns medium-12 p-top-1">
                <div class="d-inline-block cont-services w-100p">
                <?php
                    if(!empty($distributors_networks)){
                        foreach ($distributors_networks as $key_distributor_network => $distributor_networks) { 
                            ?>
                            <div class="medium-2 columns end <?php if (!array_key_exists($distributor_networks['DistributorNetwork']['id'],$sections_distributors_networks)) { echo "d-none"; } ?> <?php if ($key_distributor_network % 6 == 0) { echo "clear"; } ?>">
                                <label>
                                    <?php
                                    $valor = false;
                                    if ($action == 'edit_subsection') {
                                        if (isset($sections_subsections_distributors_networks)) {
                                            foreach ($sections_subsections_distributors_networks as $key => $sections_subsections_distributors_network) {
                                                if (intval($distributor_networks['DistributorNetwork']['id']) == $key) {
                                                    $valor = true;
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    <?php
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
                                        );?>
                                    </span>
                                </label>
                            </div>
                        <?php 
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="columns medium-12 p-left-1 end <?php if( !$section['CommunicationSection']['without_distributor_networks']){ echo "d-none"; } ?>">
                <div class="columns medium-12 p-left-1 end">
                    <?php 
                    echo $this->Form->input(
                        'without_distributor_networks',
                        array(
                            'label' => __t('Communication.Without_distributor_network'),
                            'required' => true,
                            'type' => 'checkbox',
                            'id' => 'without_distributor_network',
                            'checked' => isset($section_subsection) ?  $section_subsection['SectionSubsection']['without_distributor_networks'] : true,
                        )
                    ); ?>
                </div>
            </div>
        </div>
        <div class="columns medium-12 p-form">
            <div class="columns medium-12 titulo2 p-left-1">
                <?php echo __t('Network.Trading_groups'); ?>
            </div>
            <div class="columns medium-12 p-top-1">
                <div class="d-inline-block cont-services w-100p">
                    <?php
                    if(!empty($trading_groups)){
                        foreach ($trading_groups as $key_trading_group => $trading_group) {
                            ?>
                            <div class="medium-2 columns end <?php if (!array_key_exists($trading_group['TradingGroup']['id'],$sections_trading_groups)) { echo "d-none"; } ?> <?php if ($key_trading_group % 6 == 0) {
                                echo "clear";
                            } ?>">
                                <label>
                                    <?php
                                    $valor = false;
                                    if ($action == 'edit_subsection') {
                                        if (isset($sections_subsections_trading_groups)) {
                                            foreach ($sections_subsections_trading_groups as $key => $sections_subsections_trading_group) {
                                                if (intval($trading_group['TradingGroup']['id']) == $key) {
                                                    $valor = true;
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    <?php
                                    echo $this->Form->input(
                                        'TradingGroup.' . $trading_group['TradingGroup']['id'],
                                        array(
                                            'type' => 'checkbox',
                                            'label' => false,
                                            'div' => false,
                                            'value' => $trading_group['TradingGroup']['id'],
                                            'checked' => $valor
                                        )
                                    ); ?>
                                    
                                    <span class="unselectable ta-center" title="<?php echo h($trading_group['TradingGroup']['name']); ?>">
                                        <?php echo $this->Html->image(
                                            FileManager::get_url(FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $trading_group['TradingGroup']['image']),
                                            array(
                                                'alt' => $trading_group['TradingGroup']['name']
                                            )
                                        ); ?>
                                    </span>
                                </label>
                            </div>
                        <?php 
                        } 
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="columns medium-12 p-form">
            <div class="columns medium-12 titulo2 p-left-1">
                <?php echo __t('Distributor.Distributors'); ?>
            </div>
            <div class="columns medium-12 end">
                <div class="columns medium-3 p-left-0 end <?php if( !$section['CommunicationSection']['aag_member_yes']){ echo "d-none"; } ?>">
                    <?php 
                    echo $this->Form->input(
                        'aag_member_yes',
                        array(
                            'label' => __t('Communication.Aag_member_yes'),
                            'required' => true,
                            'type' => 'checkbox',
                            'id' => 'is_aag_member_yes',
                            'checked' => isset($section_subsection) ?  $section_subsection['SectionSubsection']['aag_member_yes'] : true,
                        )
                    ); ?>
                </div>
                <div class="columns medium-3 p-left-0 end <?php if( !$section['CommunicationSection']['aag_member_no']){ echo "d-none"; } ?>">
                    <?php 
                    echo $this->Form->input(
                        'aag_member_no',
                        array(
                            'label' => __t('Communication.Aag_member_no'),
                            'required' => true,
                            'type' => 'checkbox',
                            'id' => 'is_aag_member_no',
                            'checked' => isset($section_subsection) ?  $section_subsection['SectionSubsection']['aag_member_no'] : true,
                        )
                    ); ?>
                </div>
            </div>
        </div>

        <div class="columns medium-12 p-form">
            <div class="columns medium-12 titulo2 p-left-1">
                <?php echo __t('Distributor.Profile'); ?>
            </div>
            <div class="columns medium-12 clear end">
                <div class="columns medium-6 clear end p-left-0">
                    <div class="columns medium-12 clear end p-left-0">
                        <label>
                            <?php echo __t('Garage.Garage') ?>
                        </label>
                    </div>

                    <?php
                    if(!empty($garage_positions)){
                        foreach ($garage_positions as $position) {
                            $valor = false;
                            if ($action == 'edit_subsection') {
                                $valor = false;
                                if (isset($sections_subsections_positions)) {
                                    foreach ($sections_subsections_positions as $key => $sections_subsections_position) {
                                        if (intval($position['Position']['id']) == $sections_subsections_position['SectionSubsectionPosition']['position_id']) {
                                            $valor = true;
                                        }
                                    }
                                }
                            }
                            ?>
                            <div class="medium-4 columns end <?php if (!in_array($position['Position']['id'],$sections_positions)) { echo "d-none"; } ?>">
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
                <div class="columns medium-6 end">
                    <div class="columns medium-12 clear end p-left-0">
                        <label>
                            <?php echo __t('Distributor.Distributor') ?>
                        </label>
                    </div>

                    <?php
                    if(!empty($distributor_positions)){
                        foreach ($distributor_positions as $position) {
                            $valor = false;
                            if ($action == 'edit_subsection') {
                                $valor = false;
                                if (isset($sections_subsections_positions)) {
                                    foreach ($sections_subsections_positions as $key => $sections_subsections_position) {
                                        if (intval($position['Position']['id']) == $sections_subsections_position['SectionSubsectionPosition']['position_id']) {
                                            $valor = true;
                                        }
                                    }
                                }
                            }
                            ?>
                            <div class="medium-4 columns end <?php if (!in_array($position['Position']['id'],$sections_positions)) { echo "d-none"; } ?>">
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

        <div class="columns medium-12 p-form">
            <div class="columns medium-12 titulo2 p-left-1">
                <?php echo __t('Activity.Activity'); ?>
            </div>
            <div class="columns medium-12 clear end">
                <?php
                if(!empty($customers_activities)){
                    foreach ($customers_activities as $activity_id => $activity_name) {
                        $valor = false;
                            if ($action == 'edit_subsection') {
                                $valor = false;
                                if (isset($sections_subsections_activities)) {
                                    foreach ($sections_subsections_activities as $key => $sections_subsections_activity) {
                                        if ($activity_id == $sections_subsections_activity['SectionSubsectionCustomerActivity']['customer_activity_id']) {
                                            $valor = true;
                                        }
                                    }
                                }
                            }
                        ?>
                        <div class="medium-2 columns end <?php if (!in_array($activity_id,$sections_customers_activities)) { echo "d-none"; } ?>">
                            <?php
                            echo $this->Form->input(
                                'Activity.' . $activity_id,
                                array(
                                    'type' => 'checkbox',
                                    'label' => __t($activity_name),
                                    'div' => false,
                                    'checked' => $valor
                                )
                            ); ?>
                        </div>
                    <?php 
                    }
                    ?>
                    <div class="medium-2 columns end <?php if( !$section['CommunicationSection']['without_activity']){ echo "d-none"; } ?>">
                        <?php
                        echo $this->Form->input(
                            'without_activity',
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
        </div>