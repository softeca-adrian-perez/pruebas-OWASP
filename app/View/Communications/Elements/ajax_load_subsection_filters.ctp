<?php $action = $this->request->action; ?>
<div class="aag-title">
    <?php echo __t('Section.Filters'); ?>
</div>
<div class="aag-subtitle m-top-1">
    <?php echo __t('Network.Garage_networks'); ?>
</div>
<div class="cnt-form-inputs cont-services w-100p">
    <?php
    if(!empty($networks))
    {
        foreach ($networks as $key_network => $network)
        {
            ?>
            <label class="<?php if (!array_key_exists($network['Network']['id'],$sections_networks)) { echo "d-none"; } ?>">
                <?php
                $valor = false;
                if($action == 'edit_subsection')
                {
                    if(isset($sections_subsections_networks))
                    {
                        foreach($sections_subsections_networks as $key => $sections_subsections_network)
                        {
                            if(intval($network['Network']['id']) == $key) { $valor = true; }
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
                );
                ?>
                <span class="unselectable ta-center" title="<?php echo h($network['Network']['name']); ?>">
                    <?php
                    echo $this->Html->image(
                        FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']),
                        array('alt' => $network['Network']['name'])
                    );
                    ?>
                </span>
            </label>
            <?php
        }
    }
    ?>
</div>
<div class="cnt-form-inputs-max-width <?php if( isset($section) && !$section['CommunicationSection']['without_networks']){ echo "d-none"; } ?>">
    <?php
    $checked_without_networks = false;
    if (isset($section_subsection)) {
        $checked_without_networks = $section_subsection['SectionSubsection']['without_networks'];
    } elseif (isset($section)) {
        $checked_without_networks = $section['CommunicationSection']['without_networks'];
    }
    echo $this->Form->input(
        'SectionSubsection.without_networks',
        array(
            'label' => __t('Communication.Without_garage_network'),
            'required' => true,
            'type' => 'checkbox',
            'id' => 'without_garage_network',
            'checked' => $checked_without_networks,
        )
    );
    ?>
</div>
<div class="aag-subtitle m-top-1">
    <?php echo __t('Network.Distributor_networks'); ?>
</div>
<div class="cnt-form-inputs cont-services w-100p">
    <?php
    if(!empty($distributors_networks))
    {
        foreach($distributors_networks as $key_distributor_network => $distributor_networks)
        {
            ?>
            <label class="<?php if(!array_key_exists($distributor_networks['DistributorNetwork']['id'], $sections_distributors_networks)) { echo "d-none"; } ?>">
                <?php
                $valor = false;
                if($action == 'edit_subsection')
                {
                    if(isset($sections_subsections_distributors_networks))
                    {
                        foreach($sections_subsections_distributors_networks as $key => $sections_subsections_distributors_network)
                        {
                            if(intval($distributor_networks['DistributorNetwork']['id']) == $key) { $valor = true; }
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
                );
                ?>
                <span class="unselectable ta-center" title="<?php echo h($distributor_networks['DistributorNetwork']['name']); ?>">
                    <?php
                    echo $this->Html->image(
                        FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $distributor_networks['DistributorNetwork']['image']),
                        array('alt' => $distributor_networks['DistributorNetwork']['name'])
                    );
                    ?>
                </span>
            </label>
        <?php
        }
    }
    ?>
</div>
<div class="cnt-form-inputs-max-width <?php if(isset($section) && !$section['CommunicationSection']['without_distributor_networks']){ echo "d-none"; } ?>">
    <?php
    $checked_without_distributor_networks = false;
    if (isset($section_subsection)) {
        $checked_without_distributor_networks = $section_subsection['SectionSubsection']['without_distributor_networks'];
    } elseif (isset($section)) {
        $checked_without_distributor_networks = $section['CommunicationSection']['without_distributor_networks'];
    }
    echo $this->Form->input(
        'SectionSubsection.without_distributor_networks',
        array(
            'label' => __t('Communication.Without_distributor_network'),
            'required' => true,
            'type' => 'checkbox',
            'id' => 'without_distributor_network',
            'checked' => $checked_without_distributor_networks,
        )
    );
    ?>
</div>
<div class="aag-subtitle m-top-1">
    <?php echo __t('Network.Trading_groups'); ?>
</div>
<div class="cnt-form-inputs cont-services w-100p">
    <?php
    if(!empty($trading_groups))
    {
        foreach($trading_groups as $key_trading_group => $trading_group)
        {
            ?>
            <label class="<?php if (!array_key_exists($trading_group['TradingGroup']['id'],$sections_trading_groups)) { echo "d-none"; } ?>">
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
                    <?php
                    echo $this->Html->image(
                        FileManager::get_url(FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $trading_group['TradingGroup']['image']),
                        array('alt' => $trading_group['TradingGroup']['name'])
                    );
                    ?>
                </span>
            </label>
            <?php
        }
    }
    ?>
</div>
<div class="aag-subtitle m-top-1">
    <?php echo __t('Distributor.Distributors'); ?>
</div>
<div class="cnt-form-inputs-max-width">
    <div class="<?php if(isset($section) && !$section['CommunicationSection']['aag_member_yes']) { echo "d-none"; } ?>">
        <?php
        $checked_aag_member_yes = false;
        if (isset($section_subsection)) {
            $checked_aag_member_yes = $section_subsection['SectionSubsection']['aag_member_yes'];
        } elseif (isset($section)) {
            $checked_aag_member_yes = $section['CommunicationSection']['aag_member_yes'];
        }
        echo $this->Form->input(
            'SectionSubsection.aag_member_yes',
            array(
                'label' => __t('Communication.Aag_member_yes'),
                'required' => true,
                'type' => 'checkbox',
                'id' => 'is_aag_member_yes',
                'checked' => $checked_aag_member_yes,
            )
        ); ?>
    </div>
    <div class="<?php if(isset($section) && !$section['CommunicationSection']['aag_member_no']) { echo "d-none"; } ?>">
        <?php
        $checked_aag_member_no = false;
        if (isset($section_subsection)) {
            $checked_aag_member_no = $section_subsection['SectionSubsection']['aag_member_no'];
        } elseif (isset($section)) {
            $checked_aag_member_no = $section['CommunicationSection']['aag_member_no'];
        }
        echo $this->Form->input(
            'SectionSubsection.aag_member_no',
            array(
                'label' => __t('Communication.Aag_member_no'),
                'required' => true,
                'type' => 'checkbox',
                'id' => 'is_aag_member_no',
                'checked' => $checked_aag_member_no,
            )
        ); ?>
    </div>
</div>
<div class="aag-subtitle m-top-1">
    <?php echo __t('Distributor.Profile'); ?>
</div>
<div class="cnt-two-columns">
    <div>
        <div class="aag-subtitle" style="font-weight: normal;">
            <?php echo __t('Garage.Garage'); ?>
        </div>
        <div class="cnt-form-inputs-max-width">
            <?php
            if(!empty($garage_positions))
            {
                foreach($garage_positions as $position)
                {
                    $valor = false;
                    if($action == 'edit_subsection')
                    {
                        $valor = false;
                        if(isset($sections_subsections_positions))
                        {
                            foreach($sections_subsections_positions as $key => $sections_subsections_position)
                            {
                                if(intval($position['Position']['id']) == $sections_subsections_position['SectionSubsectionPosition']['position_id']) { $valor = true; }
                            }
                        }
                    }
                    ?>
                    <div class="<?php if (!in_array($position['Position']['id'],$sections_positions)) { echo "d-none"; } ?>">
                        <?php
                        echo $this->Form->input(
                            'GaragePosition.' . $position['Position']['id'],
                            array(
                                'type' => 'checkbox',
                                'label' => __t($position['Position']['name' . __s()]),
                                'div' => false,
                                'checked' => $valor
                            )
                        );
                        ?>
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
            if(!empty($distributor_positions))
            {
                foreach($distributor_positions as $position)
                {
                    $valor = false;
                    if($action == 'edit_subsection')
                    {
                        $valor = false;
                        if(isset($sections_subsections_positions))
                        {
                            foreach($sections_subsections_positions as $key => $sections_subsections_position)
                            {
                                if(intval($position['Position']['id']) == $sections_subsections_position['SectionSubsectionPosition']['position_id']) { $valor = true; }
                            }
                        }
                    }
                    ?>
                    <div class="<?php if (!in_array($position['Position']['id'],$sections_positions)) { echo "d-none"; } ?>">
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
<div class="aag-subtitle m-top-1">
    <?php echo __t('Activity.Activity'); ?>
</div>
<div class="cnt-form-inputs-max-width m-bottom-1">
    <?php
    if(!empty($customers_activities))
    {
        foreach($customers_activities as $activity_id => $activity_name)
        {
            $valor = false;
            if($action == 'edit_subsection')
            {
                $valor = false;
                if(isset($sections_subsections_activities))
                {
                    foreach($sections_subsections_activities as $key => $sections_subsections_activity)
                    {
                        if($activity_id == $sections_subsections_activity['SectionSubsectionCustomerActivity']['customer_activity_id']) { $valor = true; }
                    }
                }
            }
            ?>
            <div class="<?php if (!in_array($activity_id,$sections_customers_activities)) { echo "d-none"; } ?>">
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
        <div class="<?php if(isset($section) && !$section['CommunicationSection']['without_activity']) { echo "d-none"; } ?>">
            <?php
            echo $this->Form->input(
                'SectionSubsection.without_activity',
                array(
                    'type' => 'checkbox',
                    'label' => __t('Communication.Without_activity'),
                    'div' => false
                )
            );
            ?>
        </div>
        <?php
    }
    ?>
</div>