<?php
echo $this->Form->create('Shortcut',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);
    echo $this->Form->hidden('Shortcut.id'); ?>
    <div class="cnt-breadcrumb">
        <div>
            <?php
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Maintenance.Maintenance'),
                    array(
                        'controller' => 'maintenance',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Shortcut.Shortcuts'),
                    array(
                        'controller' => 'shortcuts',
                        'action' => 'maintenance_shortcuts'
                    )
                ),
                __t('Shortcut.View'),
            ));
            ?>
        </div>
        <div>
            <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
        </div>
    </div>
    <div class="cnt-data aag-padding">
        <div class="aag-title p-bottom-1">
            <?php echo __t('Shortcut.Shortcuts'); ?>
        </div>
        <div class="cnt-two-columns">
            <div class="cnt-form-inputs">
                <div>
                    <strong><?php echo __t('Shortcut.Title') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo h($shortcut['Shortcut']['title']); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('General.Type') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo h($shurtcut_type_list[$shortcut['Shortcut']['shortcut_type_id']]); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Shortcut.Start_date') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo h($shortcut['Shortcut']['start_date']); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Shortcut.End_date') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo h($shortcut['Shortcut']['end_date']); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Shortcut.Tooltip') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo h($shortcut['Shortcut']['tooltip']); ?>
                    </div>
                </div>
            </div>
            <div class="cnt-form-inputs">
                <label class="center-check">
                    <?php echo __t('Shortcut.Is_sso'); ?>
                    <div class="aag-switch round small">
                        <?php
                        echo $this->Form->input(
                            'is_sso',
                            array(
                                'required' => true,
                                'type' => 'checkbox',
                                'id' => 'is_sso',
                                'label' => false,
                                'div' => false,
                                'disabled' => true
                            )
                        ); ?>
                        <label for="is_sso"></label>
                    </div>
                </label>
                <label class="center-check">
                    <?php echo __t('Shortcut.Active'); ?>
                    <div class="aag-switch round small">
                        <?php
                        echo $this->Form->input(
                            'active',
                            array(
                                'required' => true,
                                'disabled' => true,
                                'type' => 'checkbox',
                                'id' => 'active',
                                'label' => false,
                                'div' => false
                            )
                        ); ?>
                        <label for="active"></label>
                    </div>
                </label>
                <div class="two-columns">
                    <div>
                        <strong><?php echo __t('Shortcut.Url') ?>:</strong>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <?php echo h($shortcut['Shortcut']['url']); ?>
                        </div>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Shortcut.Parameter_name_1') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo h($shortcut['Shortcut']['parameter_name_1']); ?>
                    </div>
                </div>
                <div>
                    <strong><?php echo __t('Shortcut.Parameter_name_2') ?>:</strong>
                    <br>
                    <div class="b-bottom-1 height_input">
                        <?php echo h($shortcut['Shortcut']['parameter_name_2']); ?>
                    </div>
                </div>
                <div class="two-columns">
                    <label for="image-input">
                        <?php echo __t('Shortcut.Image') . '<span class="c-fallo"> *</span>'; ?>
                    </label>
                    <?php
                    echo $this->Form->input(
                        'image',
                        array(
                            'id' => 'image-input',
                            'class' => 'dragdrop-js',
                            'label' => false,
                            'type' => 'file',
                            'disabled' => true,
                            'multiple' => false,
                            'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                            'div' => array(
                                'class' => 'field_file cont-fileWrapper',
                            ),
                        )
                    );
                    echo $this->Form->hidden('new_image', array('id' => 'new-image-input'));
                    ?>
                </div>
                <?php
                if(isset($shortcut))
                {
                    ?>
                    <div class="two-columns">
                        <div id="currentTitle" class="aag-subtitle" style="align-items: flex-start">
                            <?php echo __t('Shortcut.Actually_image'); ?>
                        </div>
                        <?php
                        echo $this->Html->image(
                            FileManager::get_url(FilePaths::SHORTCUT_IMAGES_RELATIVE.$shortcut['Shortcut']['image']),
                            array(
                                'alt' => $shortcut['Shortcut']['title'],
                                'disabled' => true,
                                'title' => $shortcut['Shortcut']['title'],
                                'class' => 'logotipo',
                                'style' => 'height: 50px; width: max-content;'
                            )
                        );
                        ?>
                    </div>
                   <?php
                }
                ?>
            </div>
        </div>
        <div class="aag-subtitle m-top-1 clear">
            <?php echo __t('Network.Networks'); ?>
        </div>
        <div class="cnt-form-inputs cont-services w-100p">
            <?php
            foreach($networks as $key_network => $network)
            {
                $valor = false;
                if(isset($shortcuts_networks))
                {
                    foreach($shortcuts_networks as $key => $shortcut_network)
                    {
                        if(intval($network['Network']['id']) == $key) { $valor = true; }
                    }
                }
                if ($valor == true) {
                ?>
                <label class="jc-center m-0-i" style="display: flex !important;">
                    <?php
                        echo $this->Form->input(
                            'Network.' . $network['Network']['id'],
                            array(
                                'type' => 'checkbox',
                                'disabled' => true,
                                'label' => false,
                                'div' => false,
                                'value' => $network['Network']['id'],
                                'checked' => $valor,
                            )
                        ); ?>
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
        <div class="cnt-form-inputs-max-width p-top-1">
            <?php
            echo $this->Form->input(
                'without_networks',
                array(
                    'label' => __t('Communication.Without_garage_network'),
                    'required' => true,
                    'disabled' => true,
                    'type' => 'checkbox',
                    'id' => 'without_network',
                    'checked' => $shortcut['Shortcut']['without_networks']
                )
            );
            ?>
        </div>
        <div class="aag-subtitle m-top-1 clear">
            <?php echo __t('Network.Distributor_networks'); ?>
        </div>
        <div class="cnt-form-inputs cont-services w-100p">
            <?php
            if(!empty($distributors_networks))
            {
                foreach($distributors_networks as $key_distributor_network => $distributor_networks)
                {
                    $valor = false;
                        if(isset($shortcut_distributors_networks))
                        {
                            foreach($shortcut_distributors_networks as $key => $shortcut_distributor_network)
                            {
                                if(intval($distributor_networks['DistributorNetwork']['id']) == $key) { $valor = true; }
                            }
                        }
                    if ($valor == true) {
                    ?>
                    <label class="jc-center m-0-i" style="display: flex !important;">
                        <?php
                        
                        echo $this->Form->input(
                            'DistributorNetwork.' . $distributor_networks['DistributorNetwork']['id'],
                            array(
                                'type' => 'checkbox',
                                'label' => false,
                                'disabled' => true,
                                'div' => false,
                                'value' => $distributor_networks['DistributorNetwork']['id'],
                                'checked' => $valor,
                            )
                        ); ?>
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
            }
            ?>
        </div>
        <div class="cnt-form-inputs-max-width p-top-1">
            <?php
            echo $this->Form->input(
                'without_distributor_networks',
                array(
                    'label' => __t('Communication.Without_distributor_network'),
                    'required' => true,
                    'type' => 'checkbox',
                    'disabled' => true,
                    'id' => 'without_distributor_network',
                    'checked' => $shortcut['Shortcut']['without_distributor_networks']
                )
            );
            ?>
        </div>
        <div class="aag-subtitle m-top-1 clear">
            <?php echo __t('Network.Trading_groups'); ?>
        </div>
        <div class="cnt-form-inputs cont-services w-100p">
            <?php
            if(!empty($trading_groups))
            {
                foreach($trading_groups as $key_trading_group => $trading_group)
                {
                    $valor = false;
                    if(isset($shortcut_trading_groups))
                    {
                        foreach($shortcut_trading_groups as $key => $shortcut_trading_group)
                        {
                            if(intval($trading_group['TradingGroup']['id']) == $key) { $valor = true; }
                        }
                    }
                    if ($valor == true) {
                    ?>
                    <label class="jc-center m-0-i" style="display: flex !important;">
                        <?php
                        
                        echo $this->Form->input(
                            'TradingGroup.' . $trading_group['TradingGroup']['id'],
                            array(
                                'type' => 'checkbox',
                                'label' => false,
                                'div' => false,
                                'value' => $trading_group['TradingGroup']['id'],
                                'checked' => $valor,
                                'disabled' => true,
                            )
                        );
                        ?>
                        <span class="unselectable ta-center" title="<?php echo h($trading_group['TradingGroup']['name']); ?>">
                            <?php
                            echo $this->Html->image(
                                FileManager::get_url(FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $trading_group['TradingGroup']['image']),
                                array('alt' => $trading_group['TradingGroup']['name'], 'class' => 'unselectable')
                            );
                            ?>
                        </span>
                    </label>
                    <?php
                    }
                }
            }
            ?>
        </div>
        <div class="aag-subtitle m-top-1 clear">
            <?php echo __t('Distributor.Distributors'); ?>
        </div>
        <div class="cnt-form-inputs-max-width">
            <?php
            echo $this->Form->input(
                'aag_member_yes',
                array(
                    'label' => __t('Distributor.Aag_member_yes'),
                    'required' => true,
                    'type' => 'checkbox',
                    'id' => 'aag_member_yes',
                    'checked' => $shortcut['Shortcut']['aag_member_yes'],
                    'disabled' => true
                )
            );
            echo $this->Form->input(
                'aag_member_no',
                array(
                    'label' => __t('Distributor.Aag_member_no'),
                    'required' => true,
                    'type' => 'checkbox',
                    'id' => 'aag_member_no',
                    'checked' => $shortcut['Shortcut']['aag_member_no'],
                    'disabled' => true
                )
            );
            ?>
        </div>
        <div class="aag-subtitle p-top-1 clear">
            <?php echo __t('Distributor.Profile'); ?>
        </div>
        <div class="cnt-two-columns clear">
            <div>
                <div class="aag-subtitle clear" style="font-weight: normal;">
                    <?php echo __t('Garage.Garage'); ?>
                </div>
                <div class="cnt-form-inputs-max-width">
                    <?php
                    if(!empty($garage_positions))
                    {
                        foreach($garage_positions as $position)
                        {
                            $valor = false;
                            if(isset($shortcut_positions))
                            {
                                foreach($shortcut_positions as $key => $shortcut_position)
                                {
                                    if(intval($position['Position']['id']) == $shortcut_position) { $valor = true; }
                                }
                            }
                            echo $this->Form->input(
                                'GaragePosition.' . $position['Position']['id'],
                                array(
                                    'type' => 'checkbox',
                                    'label' => __t($position['Position']['name' . __s()]),
                                    'checked' => $valor,
                                    'disabled' => true
                                )
                            );
                        }
                    }
                    ?>
                </div>
            </div>
            <div>
                <div class="aag-subtitle clear" style="font-weight: normal;">
                    <?php echo __t('Distributor.Distributor'); ?>
                </div>
                <div class="cnt-form-inputs-max-width">
                    <?php
                    if(!empty($distributor_positions))
                    {
                        foreach($distributor_positions as $position)
                        {
                            $valor = false;
                            if(isset($shortcut_positions))
                            {
                                foreach($shortcut_positions as $key => $shortcut_position)
                                {
                                    if(intval($position['Position']['id']) == $shortcut_position) { $valor = true; }
                                }
                            }
                            echo $this->Form->input(
                                'DistributorPosition.' . $position['Position']['id'],
                                array(
                                    'type' => 'checkbox',
                                    'label' => __t($position['Position']['name' . __s()]),
                                    'checked' => $valor,
                                    'disabled' => true
                                )
                            );
                        }
                    }
                    ?>
                   </div>
            </div>
        </div>
        <div class="aag-subtitle p-top-1 clear">
            <?php echo __t('Activity.Activity'); ?>
        </div>
        <div class="cnt-form-inputs-max-width m-bottom-1">
            <?php
            if(!empty($customers_activities))
            {
                foreach($customers_activities as $activity_id => $activity_name)
                {
                    $valor = true;
                    $valor = false;
                    if(isset($shortcut_customers_activities))
                    {
                        foreach($shortcut_customers_activities as $key => $shortcut_customer_activity)
                        {
                            if($activity_id == $shortcut_customer_activity) { $valor = true; }
                        }
                    }
                    echo $this->Form->input(
                        'Activity.' . $activity_id,
                        array(
                            'type' => 'checkbox',
                            'label' => __t($activity_name),
                            'checked' => $valor,
                            'disabled' => true
                        )
                    );
                }
            }
            echo $this->Form->input(
                'without_activity',
                array(
                    'label' => __t('Customer.Without_activity'),
                    'required' => true,
                    'type' => 'checkbox',
                    'id' => 'without_activity',
                    'checked' => $shortcut['Shortcut']['without_activity'],
                    'disabled' => true
                )
            );
            ?>
        </div>
    </div>
<?php echo $this->Form->end(); ?>