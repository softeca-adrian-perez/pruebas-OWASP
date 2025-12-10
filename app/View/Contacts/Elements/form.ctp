<?php
echo $this->Html->script('/js/users.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create('Contact');
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;
echo $this->Form->hidden(
    'Contact.id'
);
echo $this->Form->hidden(
    '',
    array(
        'id' => 'action_form',
        'data-param1' => $this->request->params['pass'][1],
        'data-param2' => isset($garage_id) ? $garage_id : null,
        'data-param3' => $this->request->params['pass'][0],
        'data-param4' => $action,
    )
);
echo $this->Form->hidden(
    'positions_bdm',
    array(
        'id' => 'positions_bdm_id',
        'value' => $positions_list_bdm
    )
);
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Contact.Contacts'),
                    array(
                        'controller' => 'contacts',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Contact.Contacts'),
                    array(
                        'controller' => 'contacts',
                        'action' => 'home'
                    )
                ),
                __t('General.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php
        echo $this->element('Comun/form_actions_garage', $cancel_action);
        if ($this->request->action == ConstantsActionsNames::EDIT && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            $data_url = Router::url(
                array(
                    'controller' => 'garages',
                    'action' => 'ajax_update_edit',
                )
            );
        ?>
            <button type="button" id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo $data_url; ?>" data-edit="<?php echo __t('General.Edit'); ?>" data-view="<?php echo __t('General.View'); ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>">
                <?php echo __t('General.Edit'); ?>
            </button>
        <?php
        }
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Contact.New_contact');
        } else {
            echo __t('Contact.Edit_contact');
        }
        ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <?php
        echo $this->Form->input(
            'Contact.title_id',
            array(
                'label' => __t('Contact.Title'),
                'class' => 'select2-multiple input-disabled',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $titles,
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'Contact.first_name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Contact.First_name'),
                'class' => 'input-disabled',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'Contact.last_name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Contact.Last_name'),
                'class' => 'input-disabled',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'Contact.email',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Contact.Email'),
                'class' => 'input-disabled',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'Contact.logistic_center_id',
            array(
                'label' => __t('Contact.Logistic_center'),
                'class' => 'select2-multiple input-disabled',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $logistic_centers,
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'Contact.phone',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Contact.Phone'),
                'class' => 'input-disabled',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'Contact.mobile_phone',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Contact.Mobile_phone'),
                'class' => 'input-disabled',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        if ($this->request->params['pass'][1] == ConstantsContactAction::ADD_STAFF && (isset($customer_id) || isset($garage_id))) {
            echo $this->Form->hidden(
                'GarageContactStaff.id',
                array(
                    'value' => isset($garage_contact[0]['GarageContactStaff']['id']) ? $garage_contact[0]['GarageContactStaff']['id'] : null,
                )
            );
            echo $this->Form->input(
                'GarageContactStaff.interest',
                array(
                    'type' => 'text',
                    'required' => false,
                    'label' => __t('Contact.Interests'),
                    'class' => 'input-disabled',
                    'value' => isset($garage_contact[0]['GarageContactStaff']['interest']) ? $garage_contact[0]['GarageContactStaff']['interest'] : null,
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            );
        }
        echo $this->Form->input(
            'Contact.position_id',
            array(
                'label' => __t('Contact.Position'),
                'class' => 'select2-multiple input-disabled',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $positions,
                'id' => 'position',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        ?>
        <div id="parent">
            <?php
            echo $this->Form->input(
                'Contact.contact_id',
                array(
                    'label' => __t('Contact.Parent'),
                    'class' => 'select2-multiple input-disabled',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $contacts_bdm,
                    'id' => 'parent_select',
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            );
            ?>
        </div>
        <?php
        if (!in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR))) {
            if (isset($garages_list) && !empty($garages_list)) {
                $option_garage = $garages_list;
            }
        ?>
            <div id="garage">
                <?php
                echo $this->Form->input(
                    'Contact.garage_id',
                    array(
                        'label' => __t('Garage.Garage') . '<span style="color:red"> *</span>',
                        'class' => 'select2-multiple',
                        'type' => 'select',
                        'multiple' => false,
                        'empty' => true,
                        'options' => $option_garage,
                        'default' => $option_garage,
                        'id' => 'garage_select',
                        'data-url' => Router::url(
                            array(
                                'controller' => 'garages',
                                'action' => 'ajax_get_garages',
                                $user_aag_region_id
                            )
                        ),
                        'disabled' => $this->request->action == 'add' ? false : true
                    )
                );
                ?>
            </div>
            <?php
            if (isset($garage_id) && empty($option_distributor)) {
                echo $this->Form->hidden(
                    'Contact.garage_id',
                    array(
                        'value' => $garage_id,
                    )
                );
            }
        } elseif (CakeSession::read('Auth.User.role_id') == ConstantsRoles::GARAGE) {
            echo $this->Form->hidden(
                'Contact.garage_id',
                array(
                    'value' => $customer_id
                )
            );
        } elseif (CakeSession::read('Auth.User.role_id') == ConstantsRoles::GARAGE) {
            echo $this->Form->hidden('Contact.garage_id', array('value' => $customer_id));
        }
        if (!in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR))) {
            if (isset($distributors_list) && !empty($distributors_list)) {
                $option_distributor = $distributors_list;
            }
            ?>
            <div id="distributor">
                <?php
                echo $this->Form->input(
                    'Contact.distributor_id',
                    array(
                        'label' => __t('Distributor.Distributor'),
                        'class' => 'select2Dinamico_distributor_dynamic',
                        'type' => 'select',
                        'multiple' => false,
                        'empty' => true,
                        'options' => $option_distributor,
                        'default' => $option_distributor,
                        'id' => 'distributor_select',
                        'disabled' => $this->request->action == 'add' ? false : true,
                        'data-url' => Router::url(
                            array(
                                'controller' => 'distributors',
                                'action' => 'ajax_get_distributors'
                            )
                        ),
                    )
                );
                if (isset($garage_id) && empty($option_garage)) {
                    echo $this->Form->hidden(
                        'Contact.distributor_id',
                        array(
                            'value' => $garage_id,
                        )
                    );
                }
                ?>
            </div>
        <?php
        } elseif (CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR) {
            echo $this->Form->hidden(
                'Contact.distributor_id',
                array(
                    'label' => __t('Distributor.Distributor'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $distributors,
                    'id' => 'distributor_select'
                )
            );
        } elseif (CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR) {
            echo $this->Form->hidden('Contact.distributor_id', array('value' => $customer_id));
        }
        ?>
    </div>
    <div class="cnt-form-inputs m-top-1" id="contact_networks_form">
        <?php
        echo $this->Form->input(
            'Contact.region',
            array(
                'label' => __t('Garage.Garage') . ' ' . __t('Network.Networks'),
                'class' => 'select2-multiple input-disabled',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $networks,
                'id' => 'selected_network',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'DistributorNetworkContactBdm.distributor_network_id',
            array(
                'label' => __t('Distributor.Distributor') . ' ' . __t('Network.Networks'),
                'class' => 'select2- input-disabled',
                'type' => 'select',
                'empty' => true,
                'multiple' => 'true',
                'options' => $distributors_networks,
                'id' => 'selected_distributor_network',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        ?>
    </div>
    <div id="contact_networks_form" class="m-top-1">
        <?php echo $this->Form->input(
            'NetworkContactBdm.network_id',
            array(
                'label' => __t('Garage.Garage') . ' ' . __t('Network.Networks'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => 'true',
                'empty' => true,
                'options' => $networks,
                'id' => 'selected_network'
            )
        ); ?>
        <?php echo $this->Form->input(
            'DistributorNetworkContactBdm.distributor_network_id',
            array(
                'label' => __t('Distributor.Distributor') . ' ' . __t('Network.Networks'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'empty' => true,
                'multiple' => 'true',
                'options' => $distributors_networks,
                'id' => 'selected_distributor_network'
            )
        ); ?>

        <?php
        if ($this->request->action == ConstantsActionsNames::EDIT) {
            $selected = $contact_aag_region;
        } elseif ($this->request->action == 'add') {
            $selected = CakeSession::read('Auth.User.aag_region_id');
        }

        echo $this->Form->input(
            'Contact.aag_region_id',
            array(
                'label' => __t('Garage.Region'),
                'required' => true,
                'type' => 'select',
                'options' => $aag_regions,
                'selected' => $selected,
                'disabled' => true,
            )
        );

        echo $this->Form->input(
            'Contact.aag_region_id',
            array(
                'required' => true,
                'type' => 'hidden',
                'value' => $selected,
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>