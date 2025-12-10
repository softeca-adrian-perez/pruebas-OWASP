<?php
echo $this->Html->script('garages.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'GarageAgreement',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'form-garages'
    )
);
echo $this->Form->hidden('GarageAgreement.id');
echo $this->Form->hidden('GarageAgreement.garage_id');
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
                __t('Agreement.New_external_agreement'),
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
        <?php echo $this->element(
            'Comun/form_actions_garage',
            $cancel_action
        ); ?>
        <?php if ($this->request->action == ConstantsActionsNames::EDIT) { ?>
            <div class="f-right">
                <button type="button" id="edit-btn-disable" value="1" class="aag-button medium"
                    data-url="<?php echo Router::url(
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'ajax_update_edit',
                                    )
                                ); ?>"
                    data-edit="<?php echo __t('General.Edit'); ?>"
                    data-view="<?php echo __t('General.View'); ?>"
                    data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>">
                    <?php echo __t('General.Edit'); ?>
                </button>
            </div>
        <?php } ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Agreement.New_external_agreement');
        } else {
            echo $agreements[$garage_agreement['GarageAgreement']['agreement_id']];
        }
        ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <?php if ($action == ConstantsActionsNames::EDIT) { ?>
            <div class="required">
                <?php echo $this->Form->input(
                    '',
                    array(
                        'label' => __t('Agreement.Agreement'),
                        'class' => 'select2-multiple',
                        'type' => 'select',
                        'disabled' => true,
                        'multiple' => false,
                        'empty' => true,
                        'options' => array(
                            $garage_agreement['GarageAgreement']['agreement_id'] => $agreements[$garage_agreement['GarageAgreement']['agreement_id']]
                        ),
                        'value' => $garage_agreement['GarageAgreement']['agreement_id']
                    )
                ); ?>
                <?php echo $this->Form->hidden(
                    'agreement_id',
                    array(
                        'value' => $garage_agreement['GarageAgreement']['agreement_id']
                    )

                ); ?>
            </div>
        <?php } else { ?>
            <div>
                <?php echo $this->Form->input(
                    'agreement_id',
                    array(
                        'label' => __t('Agreement.Agreement'),
                        'class' => 'select2-multiple input-disabled',
                        'id' => 'select-agreement',
                        'type' => 'select',
                        'multiple' => false,
                        'empty' => true,
                        'options' => $agreements,
                        'disabled' => $this->request->action == 'add' ? false : true,
                    )
                ); ?>
            </div>
        <?php } ?>

        <div>
            <?php echo $this->Form->input(
                'status',
                array(
                    'label' => __t('Agreement.Status'),
                    'class' => 'select2-multiple input-disabled',
                    'id' => 'status',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $agreements_statuses,
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            ); ?>
        </div>
    </div>
    <div class="aag-subtitle">
        <?php echo __t('Agreement.Contract'); ?>
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
                    'label' => __t('Agreement.Sent_date'),
                    'disabled' => $this->request->action == 'add' ? false : true,
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
                    'label' => __t('Agreement.Received_date'),
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            ); ?>
        </div>
        <div>
            <?php echo $this->Form->input(
                'contract_start_date',
                array(
                    'class' => 'fecha-js from-js input-disabled',
                    'id' => '#contract_start_date',
                    'data-to' => '#contract_end_date',
                    'type' => 'text',
                    'required' => true,
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Agreement.Start_date'),
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
                    'label' => __t('Agreement.Contract_end_date'),
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            ); ?>
        </div>
        <div class="<?php echo $action == ConstantsActionsNames::EDIT && $garage_agreement['GarageAgreement']['leaving_date'] ? '' : 'd-none' ?>" id="date-left">
            <?php echo $this->Form->input(
                'leaving_date',
                array(
                    'class' => 'fecha-js to-js input-disabled',
                    'id' => 'date_leaving',
                    'label' => __t('Agreement.Leaving_date'),
                    'type' => 'text',
                    'required' => false,
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            ); ?>
        </div>
        <div class="<?php echo $action == ConstantsActionsNames::EDIT && $garage_agreement['GarageAgreement']['date_on_hold'] ? '' : 'd-none' ?>" id="date-hold">
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
                    'label' => __t('Agreement.On_hold_date'),
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            ); ?>
        </div>
        <div class="<?php echo $action == ConstantsActionsNames::EDIT && ($garage_agreement['GarageAgreement']['status'] == 7 || $garage_agreement['GarageAgreement']['status'] == 8) ? '' : 'd-none' ?>" id="reason">
            <?php echo $this->Form->input(
                'reason',
                array(
                    'label' => __t('Agreement.Reason'),
                    'type' => 'select',
                    'class' => 'select2-multiple input-disabled',
                    'id' => 'reason-select2',
                    'empty' => true,
                    'required' => true,
                    'options' => (isset($reasons) ? $reasons : null),
                    'disabled' => $this->request->action == 'add' ? false : true,
                    'data-url' => Router::url(array(
                        'controller' => 'garages_agreements',
                        'action' => 'ajax_load_reasons',
                    ))
                )
            ); ?>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>