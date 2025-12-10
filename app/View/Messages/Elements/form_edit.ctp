<?php
$action = $this->request->action;
echo $this->Html->script('mailbox.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'Message',
    array(
        'class' => 'form-horizontal',
        'id' => 'message',
        'enctype' => 'multipart/form-data'
    )
);
    echo $this->Form->hidden('Message.id');
    echo $this->Form->hidden('Message.type');
    ?>
    <div class="cnt-breadcrumb">
        <div>
            <?php
            if($action == 'edit_distributors')
            {
                echo $this->Html->breadcrumb(array(
                    $this->Html->link(
                    __t('Mailbox.Mailbox'),
                        array(
                            'controller' => 'messages',
                            'action' => 'home'
                        )
                    ),
                    __t('General.Edit'),
                ));
            }
            elseif($action == 'new_distributors')
            {
                echo $this->Html->breadcrumb(array(
                    $this->Html->link(
                    __t('Mailbox.Mailbox'),
                        array(
                            'controller' => 'messages',
                            'action' => 'home'
                        )
                    ),
                    __t('General.New')
                ));
            }
            elseif($action == 'edit_garages')
            {
                echo $this->Html->breadcrumb(array(
                    $this->Html->link(
                    __t('Mailbox.Mailbox'),
                        array(
                            'controller' => 'messages',
                            'action' => 'home'
                        )
                    ),
                    __t('General.Edit')
                ));
            }
            elseif($action == 'new_garages')
            {
                echo $this->Html->breadcrumb(array(
                    $this->Html->link(
                    __t('Mailbox.Mailbox'),
                        array(
                            'controller' => 'messages',
                            'action' => 'home'
                        )
                    ),
                    __t('General.New')
                ));
            }
            else
            {
                echo $this->Html->breadcrumb(array(
                    $this->Html->link(
                    __t('Mailbox.Mailbox'),
                        array(
                            'controller' => 'messages',
                            'action' => 'home'
                        )
                    )
                ));
            }
            ?>
        </div>
        <div>
            <?php
            echo $this->element('Comun/form_actions', $url_cancel);
            echo $this->Form->button(
                __t('General.Send'),
                array(
                    'type' => 'submit',
                    'name' => 'send',
                    'class' => 'aag-button medium green'
                )
            );
            ?>
        </div>
    </div>
    <div class="cnt-data aag-padding">
        <div class="aag-title m-bottom-1">
            <?php
            if($action == 'edit_distributors') { echo __t('Message.To_distributors'); }
            elseif($action == 'new_distributors') { echo __t('Message.To_distributors'); }
            elseif($action == 'edit_garages') { echo __t('Message.To_garages'); }
            elseif($action == 'new_garages') { echo __t('Message.To_garages'); }
            ?>
        </div>
        <?php
        echo $this->Form->input(
            'subject',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Message.Subject'),
            )
        );
        ?>
        <div class="m-top-1">
            <?php
            echo $this->Form->input(
                'body',
                array(
                    'type' => 'textarea',
                    'required' => true,
                    'label' => __t('Message.Body'),
                )
            );
            ?>
        </div>
        <div id="cnt_files" class="m-top-1">
            <?php
            echo $this->Form->input(
                'Message.files.',
                array(
                    'id' => 'files',
                    'class' => 'dragdrop-js dragdrop-multiple-js',
                    'label' => false,
                    'capture' => 'camera',
                    'type' => 'file',
                    'multiple' => true,
                    'accept' => '.' . ConstantsFileType::PDF,
                    'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                    'div' => array(
                        'class' => 'field_file cont-fileWrapper fileWrapperMultiple',
                    ),
                )
            );
            ?>
        </div>
        <div id="file-list-js">
            <?php
            if (isset($message_files) && !empty($message_files)) {
                echo $this->element('../Messages/Elements/form_attached_files');
            }
            ?>
        </div>
        <?php
        if($this->request->action == 'edit_garages' || $this->request->action == 'new_garages')
        {
            ?>
            <div class="p-top-1 cnt-assigned_to_garage" id="assigned_to_garage">
                <div class="m-top-1 p-1" style="background-color: var(--container-elements-color); border-radius: 7px;">
                    <div class="filters_cnt aag-subtitle" style="line-height: 1; padding-bottom: 4px;">
                        <?php echo __t('Garage.Filters'); ?>
                    </div>
                    <div class="cnt-form-inputs">
                        <?php
                        echo $this->Form->input(
                            'GarageFilter.branch',
                            array(
                                'label' => __t('Distributor.Distributors'),
                                'class' => 'select2-multiple',
                                'type' => 'select',
                                'multiple' => false,
                                'empty' => true,
                                'options' => $garage_branch,
                                'id' => 'garage_filter_branch',
                            )
                        );
                        // if($this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID || $this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID)
                        // {
                        //     $default_value = $this->Session->read('Auth.User.contact_id');
                        //     $disabled_value = true;
                        // }
                        // else
                        // {
                        //     $default_value = null;
                        //     $disabled_value = false;
                        // }
                        echo $this->Form->input(
                            'GarageFilter.RSM',
                            array(
                                'label' => __t('Garage.RSM'),
                                'class' => 'select2-multiple',
                                'type' => 'select',
                                'multiple' => false,
                                'empty' => true,
                                // 'default' => $default_value,
                                // 'disabled' => $disabled_value,
                                'options' => $garage_rsm,
                                'id' => 'garage_filter_rsm',
                            )
                        );
                        // if($this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG || $this->Session->read('Auth.User.roles_id') == ConstantsRoles::BDM_TG)
                        // {
                        //     $default_value = $this->Session->read('Auth.User.contact_id');
                        //     $disabled_value = true;
                        // }
                        // else
                        // {
                        //     $default_value = null;
                        //     $disabled_value = false;
                        // }
                        echo $this->Form->input(
                            'GarageFilter.BDM',
                            array(
                                'label' => __t('Garage.BDM'),
                                'class' => 'select2-multiple',
                                'type' => 'select',
                                'multiple' => false,
                                // 'default' => $default_value,
                                // 'disabled' => $disabled_value,
                                'empty' => true,
                                'options' => $garage_bdm,
                                'id' => 'garage_filter_bdm',
                            )
                        );
                        echo $this->Form->input(
                            'GarageFilter.Customer_status',
                            array(
                                'label' => __t('Garage.Customer_status'),
                                'class' => 'select2-multiple',
                                'type' => 'select',
                                'multiple' => false,
                                'empty' => true,
                                'options' => $garage_customer_status,
                                'id' => 'garage_filter_customer_status',
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="m-top-1">
                        <?php
                        if (!isset($recipients)) {
                            $recipients = array();
                        }
                        echo $this->Form->input(
                            'Message.recipients',
                            array(
                                'label' => array(
                                    'text' => __t('Garage.Garage'),
                                    'style' => 'position: relative; top: -3px;'
                                ),
                                'class' => 'select2-multiple',
                                'type' => 'select',
                                'multiple' => true,
                                'empty' => true,
                                'value' => array_keys($recipients),
                                'options' => $recipients,
                                'id' => 'garage_name_assigned_to',
                                'data-url' => Router::url(array(
                                    'controller' => 'garages',
                                    'action' => 'ajax_get_garages',
                                    $user_aag_region_id
                                ))
                            )
                        ); ?>
                    </div>
            </div>
            <?php
        }
        elseif($this->request->action == 'edit_distributors' || $this->request->action == 'new_distributors')
        {
            ?>
            <div class="m-top-1 p-1" style="background-color: var(--container-elements-color); border-radius: 7px;" id="assigned_to_distributor">
                <div class="filters_cnt aag-subtitle" style="line-height: 1; padding-bottom: 4px;">
                    <?php echo __t('Garage.Filters'); ?>
                </div>
                <div class="cnt-form-inputs">
                    <?php
                    // if( $this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID || $this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID)
                    // {
                    //     $default_value = $this->Session->read('Auth.User.contact_id');
                    //     $disabled_value = true;
                    // }
                    // else
                    // {
                    //     $default_value = null;
                    //     $disabled_value = false;
                    // }
                    echo $this->Form->input(
                        'DistributorFilter.RSM',
                        array(
                            'label' => __t('Garage.RSM'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            // 'default' => $default_value,
                            // 'disabled' => $disabled_value,
                            'options' => $distributor_rsm,
                            'id' => 'distributor_filter_rsm',
                        )
                    );
                    // if( $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG || $this->Session->read('Auth.User.roles_id') == ConstantsRoles::BDM_TG)
                    // {
                    //     $default_value = $this->Session->read('Auth.User.contact_id');
                    //     $disabled_value = true;
                    // }
                    // else
                    // {
                    //     $default_value = null;
                    //     $disabled_value = false;
                    // }
                    echo $this->Form->input(
                        'DistributorFilter.BDM',
                        array(
                            'label' => __t('Garage.BDM'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            // 'default' => $default_value,
                            // 'disabled' => $disabled_value,
                            'options' => $distributor_bdm,
                            'id' => 'distributor_filter_bdm',
                        )
                    );
                    ?>
                </div>
            </div>
            <div class="m-top-1">
                <?php
                if(!isset($recipients)) { $recipients = array(); }
                echo $this->Form->input(
                    'Message.recipients',
                    array(
                        'label' => array(
                            'text' => __t('Distributor.Distributor'),
                            'style' => 'position: relative; top: -3px;'
                        ),
                        'class' => 'select2-multiple',
                        'type' => 'select',
                        'multiple' => true,
                        'empty' => true,
                        'value' => array_keys($recipients),
                        'options' => $recipients,
                        'id' => 'distributor_name_assigned_to',
                        'data-url' => Router::url(array(
                            'controller' => 'distributors',
                            'action' => 'ajax_get_distributors'
                        ))
                    )
                );
                ?>
            </div>
            <?php
        }
        ?>
    </div>
<?php echo $this->Form->end(); ?>
