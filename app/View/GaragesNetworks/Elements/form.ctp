<?php
$config = CakeSession::read('Auth.User.Config');

echo $this->Html->script('garages.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'GarageNetwork',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'form-garages'
    )
);
echo $this->Form->hidden('GarageNetwork.id');
echo $this->Form->hidden('GarageNetwork.garage_id');

$action = $this->request->action;
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Garage.Garages'),
                    array(
                        'controller' => 'garages',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Network.Distributor_link'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_dis_and_net_garage',
                        $garage_id
                    )
                ),
                __t('Network.New_network'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Garage.Garages'),
                    array(
                        'controller' => 'garages',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Network.Distributor_link'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_dis_and_net_garage',
                        $garage_id
                    )
                ),
                CakeSession::read('Auth.User.edit_enabled') ? __t('General.Edit') : __t('General.View'),
            ));
        }
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->element('Comun/form_actions_garage', $cancel_action);
        if (isset($garage['Garage']['status']) && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) {
            if ($this->request->action == ConstantsActionsNames::EDIT) { ?>
                <div class="f-right">
                    <button type="button" id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo Router::url(
                                                                                                                    array(
                                                                                                                        'controller' => 'garages',
                                                                                                                        'action' => 'ajax_update_edit',
                                                                                                                    )
                                                                                                                ); ?>" data-edit="<?php echo __t('General.Edit'); ?>" data-view="<?php echo __t('General.View'); ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>">
                        <?php echo __t('General.Edit'); ?>
                    </button>
                </div>
        <?php
            }
        }
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Network.New_network');
        } else {
            echo $networks[$garage_network['GarageNetwork']['network_id']];
        }
        ?>
    </div>
    <div class="cnt-form-inputs m-top-1">
        <?php
        if ($action == ConstantsActionsNames::EDIT) {
            echo $this->Form->input(
                '',
                array(
                    'label' => __t('Network.Network'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'disabled' => true,
                    'multiple' => false,
                    'empty' => true,
                    'options' => array(
                        $garage_network['GarageNetwork']['network_id'] => $networks[$garage_network['GarageNetwork']['network_id']]
                    ),
                    'value' => $garage_network['GarageNetwork']['network_id']
                )
            );
            echo $this->Form->hidden(
                'network_id',
                array(
                    'value' => $garage_network['GarageNetwork']['network_id']
                )
            );
            echo $this->Form->hidden(
                'trading_group_id',
                array()

            );
        } else {
            echo $this->Form->input(
                'network_id',
                array(
                    'label' => __t('Network.Network'),
                    'class' => 'select2-multiple input-disabled',
                    'id' => 'select-network',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $networks,
                    'disabled' => $this->request->action == 'add' ? false : true,
                    'required' => true,
                )
            );
        }
        ?>
        <div class="d-none" id="div_trading_group" data-url="
                <?php echo Router::url(
                    array(
                        'controller' => 'garages_networks',
                        'action' => 'ajax_load_trading_groups'
                    )
                );
                ?>">
            <?php echo $this->element('../GaragesNetworks/Elements/ajax_load_trading_groups'); ?>
        </div>

        <?php
        echo $this->Form->input(
            'status',
            array(
                'label' => __t('Network.Status'),
                'class' => 'select2-multiple input-disabled',
                'id' => 'status',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $networks_statuses,
                'disabled' => (isset($garage) && $garage['Garage']['status'] == ConstantsGarageStatus::INACTIVE) ? true : false,
                'required' => true,
            )
        ); ?>
    </div>
    <div class="cnt-form-inputs m-top-1">
        <div class="two-columns">
            <span class="<?php echo $action == ConstantsActionsNames::EDIT &&
                                ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LEFT) ? 'required' : 'd-none' ?>" id="leaving_comment">
                <?php
                $class_comment = 'd-none';
                $leaving_comment = null;
                if (isset($garage_network['GarageNetwork']['status']) && $garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LEFT && !empty($comments_leaving_reasons)) {
                    $class_comment = '';
                    $leaving_comment = (isset($comments_leaving_reasons) && !empty($comments_leaving_reasons)) ? end($comments_leaving_reasons)['LeavingReasonComment']['comment'] : null;
                }

                echo $this->Form->input(
                    'LeavingReasonComment.leaving_comment',
                    array(
                        'label' =>  __t('Distributor.Comments_add'),
                        'type' => 'textarea',
                        'id' => 'comment_reason',
                        'style' => 'border-radius: 5px;',
                        'class' => 'input-disabled ' . $class_comment,
                        'required' => true,
                        'disabled' => $this->request->action == 'add' ? false : true,
                        'value' => $leaving_comment,
                    )
                );
                ?>
            </span>
        </div>
        <div>
            <span class="<?php echo $action == ConstantsActionsNames::EDIT &&
                                ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LEFT) ? 'required' : 'd-none' ?>" id="reason_leaving_id">
                <?php
                $inactive = 'false';
                $required = true;
                $class = '';
                echo $this->Form->input(
                    'reason_leaving_id',
                    array(
                        'label' => __t('Network.Reason'),
                        'type' => 'select',
                        'class' => 'select2-multiple input-disabled',
                        'id' => 'reason-leaving-select2',
                        'required' => $required,
                        'options' => (isset($reasons) ? $reasons : null),
                        'empty' => true,
                        'disabled' => (isset($garage) && $garage['Garage']['status'] == ConstantsGarageStatus::INACTIVE) ? true : false,
                        'data-url' => Router::url(array(
                            'controller' => 'garages_networks',
                            'action' => 'ajax_load_reasons',
                        )),
                        'data-inactive' => $inactive,
                    )
                ); ?>
            </span>
            <span class="<?php echo $action == ConstantsActionsNames::EDIT &&
                                ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::ON_HOLD) ? 'required' : 'd-none' ?>" id="reason_hold_id">
                <?php echo $this->Form->input(
                    'reason_hold_id',
                    array(
                        'label' => __t('Network.Reason'),
                        'type' => 'select',
                        'class' => 'select2-multiple',
                        'id' => 'reason-hold-select2',
                        'empty' => true,
                        'required' => true,
                        'options' => (isset($reasons) ? $reasons : null),
                        'disabled' => $this->request->action == 'add' ? false : true,
                        'class' => 'input-disabled',
                        'data-url' => Router::url(array(
                            'controller' => 'garages_networks',
                            'action' => 'ajax_load_reasons',
                        ))
                    )
                ); ?>
            </span>
        </div>
    </div>
    <div class="row">
    </div>
    <div class="aag-subtitle">
        <?php echo __t('Network.Contract'); ?>
    </div>
    <div class="cnt-form-inputs">
        <div>
            <?php echo $this->Form->input(
                'contract_sent_date',
                array(
                    'class' => 'fecha-js from-js input-disabled',
                    'id' => 'contract_sent_date',
                    'data-to' => '#contract_received_date',
                    'type' => 'text',
                    'required' => true,
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Network.Sent_date'),
                    'disabled' => $this->request->action == 'add' ? false : true
                )
            ); ?>
        </div>
        <div>
            <?php echo $this->Form->input(
                'contract_received_date',
                array(
                    'class' => 'fecha-js to-js input-disabled',
                    'id' => 'contract_received_date',
                    'data-from' => '#contract_sent_date',
                    'type' => 'text',
                    'required' => true,
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Network.Received_date'),
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            ); ?>
        </div>
        <div>
            <?php echo $this->Form->input(
                'contract_start_date',
                array(
                    'class' => 'fecha-js from-js input-disabled',
                    'id' => 'contract_start_date',
                    'data-to' => '#contract_end_date',
                    'type' => 'text',
                    'required' => false,
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Network.Start_date'),
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            ); ?>
        </div>
        <div>
            <?php echo $this->Form->input(
                'contract_end_date',
                array(
                    'class' => 'fecha-js to-js input-disabled',
                    'id' => 'contract_end_date',
                    'data-from' => '#contract_start_date',
                    'type' => 'text',
                    'required' => true,
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Network.Contract_end_date'),
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            ); ?>
        </div>
        <div class="<?php echo $action == ConstantsActionsNames::EDIT && $garage_network['GarageNetwork']['leaving_date'] ? '' : 'd-none' ?>" id="date-left">
            <?php echo $this->Form->input(
                'leaving_date',
                array(
                    'class' => 'fecha-js to-js input-disabled',
                    'id' => 'date_leaving',
                    'label' => __t('Network.Leaving_date'),
                    'type' => 'text',
                    'required' => false,
                    'disabled' => $this->request->action == 'add' ? false : true,
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    )
                )
            ); ?>
        </div>
        <div class="<?php echo $action == ConstantsActionsNames::EDIT && $garage_network['GarageNetwork']['date_on_hold'] ? '' : 'd-none' ?>" id="date-hold">
            <?php echo $this->Form->input(
                'date_on_hold',
                array(
                    'class' => 'fecha-js to-js input-disabled',
                    'id' => 'date_on_hold',
                    'type' => 'text',
                    'required' => false,
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Network.On_hold_date'),
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            ); ?>
        </div>
        <div>
            <?php echo $this->Form->input(
                'annex_detail_id',
                array(
                    'label' => __t('Network.Annex_detail'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $networks_annex_details,
                    'disabled' => $this->request->action == 'add' ? false : true,
                    'class' => 'input-disabled',
                )
            ); ?>
        </div>
        <div class="cnt-check-visit cnt-form-inputs-max-width">
            <?php echo $this->Form->input(
                'dd_active',
                array(
                    'label' => __t('Network.Dd_active'),
                    'type' => 'checkbox',
                    'disabled' => $this->request->action == 'add' ? false : true,
                    'class' => 'input-disabled',
                )
            ); ?>
        </div>
    </div>
    <?php
    if (
        isset($garage_network['GarageNetwork']['network_id']) &&
        (in_array($garage_network['GarageNetwork']['network_id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC)))
    ) {
    ?>
        <div class="aag-subtitle">
            <?php echo __t('General.Garage_access'); ?>
        </div>
        <div class="row">
            <div id="kiyoh-input-container">
                <?php
                echo $this->Html->link(
                    $networks[$garage_network['GarageNetwork']['network_id']],
                    array(
                        'controller' => 'garages_networks',
                        'action' => 'network_dashboard',
                        $garage_network['GarageNetwork']['id']
                    ),
                    array(
                        'class' => 'c-primary'
                    )
                );
                ?>
            </div>
        </div>
    <?php } ?>
    <?php if (isset($garage_distributor_id)) { ?>
</div>
<div class="row">
    <div class="columns">
        <div class="titulo2">
            <?php echo __t('Network.Contract'); ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="medium-4 columns p-0">
        <?php echo $this->Form->input(
            'contract_sent_date',
            array(
                'class' => 'fecha-js from-js',
                'id' => 'contract_sent_date',
                'data-to' => '#contract_received_date',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Network.Sent_date'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        ); ?>
    </div>
    <div class="medium-4 columns p-0">
        <?php echo $this->Form->input(
            'contract_received_date',
            array(
                'class' => 'fecha-js to-js',
                'id' => 'contract_received_date',
                'data-from' => '#contract_sent_date',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Network.Received_date'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        ); ?>
    </div>
    <div class="medium-4 columns p-0 required">
        <?php echo $this->Form->input(
            'contract_start_date',
            array(
                'class' => 'fecha-js from-js',
                'id' => '#contract_start_date',
                'data-to' => '#contract_end_date',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Network.Start_date'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        ); ?>
    </div>
    <div class="medium-4 columns p-0">
        <?php echo $this->Form->input(
            'contract_end_date',
            array(
                'class' => 'fecha-js to-js',
                'id' => 'contract_end_date',
                'data-from' => '#contract_start_date',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Network.Contract_end_date'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        ); ?>
    </div>
    <div class="medium-4 columns <?php echo $action == ConstantsActionsNames::EDIT && $garage_network['GarageNetwork']['leaving_date'] ? '' : 'd-none' ?>" id="date-left">
        <?php echo $this->Form->input(
            'leaving_date',
            array(
                'class' => 'fecha-js to-js',
                'id' => 'date_leaving',
                'label' => __t('Network.Leaving_date'),
                'type' => 'text',
                'required' => false,
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                )
            )
        ); ?>
    </div>
    <div class="medium-4 columns p-0 <?php echo $action == ConstantsActionsNames::EDIT && $garage_network['GarageNetwork']['date_on_hold'] ? '' : 'd-none' ?>" id="date-hold">
        <?php echo $this->Form->input(
            'date_on_hold',
            array(
                'class' => 'fecha-js to-js',
                'id' => 'date_on_hold',
                'type' => 'text',
                'required' => false,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Network.On_hold_date'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        ); ?>
    </div>
    <div class="medium-4 columns p-0">
        <?php echo $this->Form->input(
            'annex_detail_id',
            array(
                'label' => __t('Network.Annex_detail'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'multiple' => false,
                'empty' => true,
                'options' => $networks_annex_details,
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        ); ?>
    </div>
    <div class="medium-2 columns p-boton">
        <?php echo $this->Form->input(
            'dd_active',
            array(
                'label' => __t('Network.Dd_active'),
                'type' => 'checkbox',
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        ); ?>
    </div>
    <?php
        if (
            isset($garage_network['GarageNetwork']['network_id']) &&
            (in_array($garage_network['GarageNetwork']['network_id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC)))
        ) {
    ?>
        <div class="medium-2 columns p-boton">
            <?php echo $this->Form->input(
                'recommended',
                array(
                    'label' => __t('General.Recommended'),
                    'type' => 'checkbox',
                    'class' => 'input-disabled',
                )
            ); ?>
        </div>
    <?php } ?>
</div>
<?php
        if (
            isset($garage_network['GarageNetwork']['network_id']) &&
            (in_array($garage_network['GarageNetwork']['network_id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC)))
        ) {
            if ($show_garage_access) {
?>
        <div class="row">
            <div class="columns">
                <div class="titulo2">
                    <?php echo __t('General.Garage_access'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="medium-4 columns p-0" id="kiyoh-input-container" style="padding: 10px;">
                <?php
                echo $this->Html->link(
                    $networks[$garage_network['GarageNetwork']['network_id']],
                    array(
                        'controller' => 'garages_networks',
                        'action' => 'network_dashboard',
                        $garage_network['GarageNetwork']['id']
                    ),
                    array(
                        'class' => 'c-primary'
                    )
                );
                ?>
            </div>
        </div>
    <?php
            }
            if (is_array($review_data) && count($review_data) > 0) {
    ?>
        <div class="row">
            <div class="medium-12 columns" style="text-align:center">
                <div class="titulo2">
                    <strong><?php echo __t('General.Total') . ': '; ?></strong>
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $average_rating) {
                    ?>
                            <img src="/img/star_yellow.svg">
                        <?php } elseif ($i == ceil($average_rating) && $average_rating != floor($average_rating)) { ?>
                            <img src="/img/star_half_yellow.svg">
                        <?php } else { ?>
                            <img src="/img/star_light.svg">
                    <?php
                        }
                    }
                    ?>
                    &nbsp;
                    <?php echo $average_rating . '/5' ?>
                    <br>
                    <strong><?php echo $total_reviews . ' ' ?></strong><?php echo __t('Review.Reviews'); ?>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="row">
            <div class="columns">
                <div class="titulo2">
                    <?php echo __t('General.Reviews'); ?>
                </div>
            </div>
        </div>
        <p><?php echo __t('Review.Error_no_reviews'); ?></p>
<?php
            }
        }
?>
<div class="row">
    <div class="medium-4 columns
                    <?php echo $action == ConstantsActionsNames::EDIT &&
                        ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::ON_HOLD ||
                            $garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LEFT) ? '' : 'd-none' ?>" id="reason">
        <?php echo $this->Form->input(
            'reason',
            array(
                'label' => __t('Network.Reason'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'id' => 'reason-select2',
                'empty' => true,
                'required' => true,
                'options' => (isset($reasons) ? $reasons : null),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
                'data-url' => Router::url(array(
                    'controller' => 'garages_networks',
                    'action' => 'ajax_load_reasons',
                ))
            )
        ); ?>
    </div>
</div>
<?php if (isset($garage_distributor_id)) { ?>
    <br />
    <div class="row">
        <div class="columns">
            <div class="titulo2">
                <?php echo __t('Network.Leaving'); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="medium-4 columns">
            <?php
            echo $this->Form->input(
                'reason_leaving',
                array(
                    'label' => __t('Network.Reason'),
                    'type' => 'textarea',
                    'disabled' => $this->request->action == 'add' ? false : true,
                    'class' => 'input-disabled',
                )
            );
            ?>
        </div>
    </div>
<?php } ?>
</div>
</div>
<div class="row">
    <div class="medium-8 columns">
        <?php
        echo $this->Form->input(
            'reason_on_hold',
            array(
                'label' => __t('Network.Reason'),
                'type' => 'textarea',
            )
        );
        ?>
    </div>
    <div class="medium-4 columns">
        <?php echo $this->Form->input(
            'date_on_hold',
            array(
                'class' => 'fecha-js',
                'label' => __t('Network.Date'),
                'type' => 'text',
                'required' => false,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                )
            )
        ); ?>
    </div>
</div>
<br />
<div class="row">
    <div class="columns">
        <div class="aag-subtitle">
            <?php echo __t('Network.Leaving'); ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="medium-4 columns">
        <?php
        echo $this->Form->input(
            'reason_leaving',
            array(
                'label' => __t('Network.Reason'),
                'type' => 'textarea',
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        );
        ?>
    </div>
</div>
<?php } ?>
<br />
<div class="row">
    <div class="aag-subtitle">
        <?php echo __t('Garage.Membership_fees'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'GarageNetwork.current_charge',
            array(
                'label' => __t('Garage.Current_charge') . ' ' . isset($country['Country']['symbol']) ?? '',
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled current_charge-js',
                'required' => $required_member_fees_fields,
            )
        );
        echo $this->Form->input(
            'GarageNetwork.member_pays',
            array(
                'label' => __t('Garage.Member_pays'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled member_pays-js',
                'required' => $required_member_fees_fields,
            )
        );
        echo $this->Form->input(
            'GarageNetwork.garage_pays',
            array(
                'label' => __t('Garage.Garage_pays'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled garage_pays-js',
                'required' => $required_member_fees_fields,
            )
        );
        ?>
    </div>
</div>
</div>
<?php if (!empty($comments_leaving_reasons)) { ?>
    <div class="cnt-data aag-padding">
        <div class="aag-title">
            <?php echo __t('GaragesNetworks.Comments_history'); ?>
        </div>
        <div class="o-auto">
            <table class="table-tracking">
                <thead>
                    <tr>
                        <th><?php echo __t('GaragesNetworks.Leaving_reason_comment'); ?></th>
                        <th><?php echo __t('Garage.Date'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments_leaving_reasons as $comments) { ?>
                        <tr>
                            <td><?php echo $comments['LeavingReasonComment']['comment']; ?></td>
                            <td><?php echo Fecha::toFormatoVista($comments['LeavingReasonComment']['date']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}
echo $this->Form->end();

if ($action == ConstantsActionsNames::EDIT && ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LEFT)) {
    $json = json_encode($inactive_leaving_reason);
?>
    <script>
        let inactiveName = <?php echo $json; ?>;
        inactiveName = inactiveName.substring(1, inactiveName.length - 1);
        $(document).ready(function() {
            $("#reason-leaving-select2").select2({
                placeholder: inactiveName,
                allowClear: true
            });
        });
    </script>
<?php } ?>