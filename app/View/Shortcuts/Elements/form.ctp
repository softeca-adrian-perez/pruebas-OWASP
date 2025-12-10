<?php
echo $this->Html->css('../js/lib/fullcalendar-3.10.5/fullcalendar.min.css', array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/lib/moment.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/fullcalendar.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/locale/' .  __l() . '.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Html->script('shortcuts.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;
echo $this->Form->create(
    'Shortcut',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);
echo $this->Form->hidden('Shortcut.id'); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
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
                __t('Shortcut.Add'),
            ));
        } else {
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
                __t('Shortcut.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
        <?php
        if ($this->request->action == ConstantsActionsNames::EDIT && (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN)) {
            echo $this->Html->link(
                __t('General.Delete'),
                array(),
                array(
                    'escape' => false,
                    'id' => 'delete-shortcut',
                    'class' => 'aag-button medium red',
                    'data-delete' => $delete_shortcut,
                    'data-url' => Router::url(array(
                        'controller' => 'shortcuts',
                        'action' => 'delete',
                        $shortcut_id
                    )),
                )
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title p-bottom-1">
        <?php echo __t('Shortcut.Shortcuts'); ?>
    </div>
    <div class="cnt-two-columns">
        <div class="cnt-form-inputs">
            <?php
            echo $this->Form->input(
                'title',
                array(
                    'required' => true,
                    'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                    'type' => 'text',
                    'id' => 'title',
                    'label' => __t('Shortcut.Title'),
                )
            );
            echo $this->Form->input(
                'shortcut_type_id',
                array(
                    'label' => __t('General.Type'),
                    'type' => 'select',
                    'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                    'class' => 'select2-multiple',
                    'options' => $shortcut_types,
                    'multiple' => false,
                    'empty' => false,
                )
            );
            echo $this->Form->input(
                'start_date',
                array(
                    'type' => 'text',
                    'required' => true,
                    'class' => 'fecha-js from-js',
                    'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                    'data-to' => '#end_date',
                    'id' => 'start_date',
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Shortcut.Start_date'),
                )
            );
            echo $this->Form->input(
                'end_date',
                array(
                    'type' => 'text',
                    'required' => true,
                    'class' => 'fecha-js to-js',
                    'data-from' => '#start_date',
                    'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                    'id' => 'end_date',
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Shortcut.End_date'),
                )
            );
            // echo $this->Form->input(
            //     'Roles',
            //     array(
            //         'label' => __t('Shortcut.Roles'),
            //         'class' => 'select2-multiple',
            //         'type' => 'select',
            //         'multiple' => true,
            //         'empty' => true,
            //         'required' => true,
            //         'options' => $roles_list,
            //         'id' => 'roles_list'
            //     )
            // );
            ?>
            <div class="two-columns">
                <?php
                echo $this->Form->input(
                    'tooltip',
                    array(
                        'required' => true,
                        'type' => 'text',
                        'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                        'id' => 'url',
                        'label' => __t('Shortcut.Tooltip'),
                    )
                );
                ?>
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
                            'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true
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
                            'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
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
                <?php
                echo $this->Form->input(
                    'url',
                    array(
                        'required' => true,
                        'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                        'type' => 'text',
                        'id' => 'url',
                        'label' => __t('Shortcut.Url'),
                    )
                );
                ?>
            </div>
            <?php
            echo $this->Form->input(
                'parameter_name_1',
                array(
                    'required' => true,
                    'type' => 'text',
                    'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                    'id' => 'parameter_name_1',
                    'label' => __t('Shortcut.Parameter_name_1'),
                )
            );
            echo $this->Form->input(
                'parameter_name_2',
                array(
                    'required' => true,
                    'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                    'type' => 'text',
                    'id' => 'parameter_name_2',
                    'label' => __t('Shortcut.Parameter_name_2'),
                )
            );
            ?>
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
                        'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
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
            if (isset($shortcut)) {
            ?>
                <div class="two-columns">
                    <div id="currentTitle" class="aag-subtitle" style="align-items: flex-start">
                        <?php echo __t('Shortcut.Actually_image'); ?>
                    </div>
                    <?php
                    echo $this->Html->image(
                        FileManager::get_url(FilePaths::SHORTCUT_IMAGES_RELATIVE . $shortcut['Shortcut']['image']),
                        array(
                            'alt' => $shortcut['Shortcut']['title'],
                            'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
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
        foreach ($networks as $key_network => $network) {
        ?>
            <label class="jc-center m-0-i" style="display: flex !important;">
                <?php
                $valor = false;
                if ($action == ConstantsActionsNames::EDIT) {
                    if (isset($shortcuts_networks)) {
                        foreach ($shortcuts_networks as $key => $shortcut_network) {
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
                        'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                        'label' => false,
                        'div' => false,
                        'value' => $network['Network']['id'],
                        'checked' => $valor
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
        ?>
    </div>
    <div class="cnt-form-inputs-max-width p-top-1">
        <?php
        echo $this->Form->input(
            'without_networks',
            array(
                'label' => __t('Communication.Without_garage_network'),
                'required' => true,
                'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                'type' => 'checkbox',
                'id' => 'without_network',
                'checked' => ($action == ConstantsActionsNames::ADD) ? true : $shortcut['Shortcut']['without_networks']
            )
        );
        ?>
    </div>
    <div class="aag-subtitle m-top-1 clear">
        <?php echo __t('Network.Distributor_networks'); ?>
    </div>
    <div class="cnt-form-inputs cont-services w-100p">
        <?php
        if (!empty($distributors_networks)) {
            foreach ($distributors_networks as $key_distributor_network => $distributor_networks) {
        ?>
                <label class="jc-center m-0-i" style="display: flex !important;">
                    <?php
                    $valor = false;
                    if ($action == ConstantsActionsNames::EDIT) {
                        if (isset($shortcut_distributors_networks)) {
                            foreach ($shortcut_distributors_networks as $key => $shortcut_distributor_network) {
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
                            'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                            'div' => false,
                            'value' => $distributor_networks['DistributorNetwork']['id'],
                            'checked' => $valor
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
                'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                'id' => 'without_distributor_network',
                'checked' => ($action == ConstantsActionsNames::ADD) ? true : $shortcut['Shortcut']['without_distributor_networks']
            )
        );
        ?>
    </div>
    <div class="aag-subtitle m-top-1 clear">
        <?php echo __t('Network.Trading_groups'); ?>
    </div>
    <div class="cnt-form-inputs cont-services w-100p">
        <?php
        if (!empty($trading_groups)) {
            foreach ($trading_groups as $key_trading_group => $trading_group) {
        ?>
                <label class="jc-center m-0-i" style="display: flex !important;">
                    <?php
                    $valor = false;
                    if ($action == ConstantsActionsNames::EDIT) {
                        if (isset($shortcut_trading_groups)) {
                            foreach ($shortcut_trading_groups as $key => $shortcut_trading_group) {
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
                'checked' => ($action == ConstantsActionsNames::ADD) ? true : $shortcut['Shortcut']['aag_member_yes']
            )
        );
        echo $this->Form->input(
            'aag_member_no',
            array(
                'label' => __t('Distributor.Aag_member_no'),
                'required' => true,
                'type' => 'checkbox',
                'id' => 'aag_member_no',
                'checked' => ($action == ConstantsActionsNames::ADD) ? true : $shortcut['Shortcut']['aag_member_no']
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
                if (!empty($garage_positions)) {
                    foreach ($garage_positions as $position) {
                        $valor = true;
                        if ($action == ConstantsActionsNames::EDIT) {
                            $valor = false;
                            if (isset($shortcut_positions)) {
                                foreach ($shortcut_positions as $key => $shortcut_position) {
                                    if (intval($position['Position']['id']) == $shortcut_position) {
                                        $valor = true;
                                    }
                                }
                            }
                        }
                        echo $this->Form->input(
                            'GaragePosition.' . $position['Position']['id'],
                            array(
                                'type' => 'checkbox',
                                'label' => __t($position['Position']['name' . __s()]),
                                'checked' => $valor
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
                if (!empty($distributor_positions)) {
                    foreach ($distributor_positions as $position) {
                        $valor = true;
                        if ($action == ConstantsActionsNames::EDIT) {
                            $valor = false;
                            if (isset($shortcut_positions)) {
                                foreach ($shortcut_positions as $key => $shortcut_position) {
                                    if (intval($position['Position']['id']) == $shortcut_position) {
                                        $valor = true;
                                    }
                                }
                            }
                        }
                        echo $this->Form->input(
                            'DistributorPosition.' . $position['Position']['id'],
                            array(
                                'type' => 'checkbox',
                                'label' => __t($position['Position']['name' . __s()]),
                                'checked' => $valor
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
        if (!empty($customers_activities)) {
            foreach ($customers_activities as $activity_id => $activity_name) {
                $valor = true;
                if ($action == ConstantsActionsNames::EDIT) {
                    $valor = false;
                    if (isset($shortcut_customers_activities)) {
                        foreach ($shortcut_customers_activities as $key => $shortcut_customer_activity) {
                            if ($activity_id == $shortcut_customer_activity) {
                                $valor = true;
                            }
                        }
                    }
                }
                echo $this->Form->input(
                    'Activity.' . $activity_id,
                    array(
                        'type' => 'checkbox',
                        'label' => __t($activity_name),
                        'checked' => $valor
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
                'checked' => ($action == ConstantsActionsNames::ADD) ? true : $shortcut['Shortcut']['without_activity']
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<div style="display: none;" id="cropImageModal" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true"
    role="dialog">
    <h4 id="modalTitle"><?php echo __t('Crop.Crop_the_photo'); ?></h4>
    <img id="image-to-crop" alt="crop" src="" />
    <p class="text-center m-0-i p-top-1">
        <button class="aag-button medium green m-0-i tres" id="crop-image"><?php echo __t('General.Save'); ?></button>
    </p>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div>