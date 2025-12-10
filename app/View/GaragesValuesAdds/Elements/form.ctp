<?php
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'GarageValueAdd',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('GarageValueAdd.id');
echo $this->Form->hidden('GarageValueAdd.garage_id');
$config = CakeSession::read('Auth.User.Config');
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
                    __t('Garage.Value_add'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_value_add',
                        $garage_id
                    )
                ),
                __t('Garage.New_value_add'),
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
                    __t('Garage.Value_add'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_value_add',
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
        <?php echo $this->element(
            'Comun/form_actions_garage',
            $cancel_action
        );
        if (isset($garage['Garage']['status']) && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) {
            if ($this->request->action == ConstantsActionsNames::EDIT) { ?>
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
        <?php }
        } ?>
    </div>
</div>

<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Garage.Value_add_new');
        } else {
            echo __t('Garage.Value_add_edit');
        }
        ?>
    </div>
    <div class="cnt-form-inputs m-top-1">
        <?php echo $this->Form->input(
            'value_add_id',
            array(
                'label' => __t('Garage.Value_add'),
                'class' => 'select2-multiple input-disabled',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $value_add,
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'billing_schedule_id',
            array(
                'label' => __t('General.Billing_schedule'),
                'class' => 'select2-multiple input-disabled',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $billing_schedule,
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'version',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Maintenance.Version'),
                'class' => 'input-disabled',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'amount',
            array(
                'type' => 'text',
                'label' => isset($country['Country']['symbol']) ? __t('General.Amount') . ' ' . $country['Country']['symbol'] : __t('General.Amount'),
                'class' => 'input-disabled',
                'disabled' => $this->request->action == 'add' ? false : true,
                'type' => 'number',
            )
        );
        echo $this->Form->input(
            'member_pay',
            array(
                'type' => 'number',
                'class' => 'input-disabled',
                'disabled' => $this->request->action == 'add' ? false : true,
                'label' => __t('General.Member_pay'),
            )
        );
        echo $this->Form->input(
            'garage_pay',
            array(
                'type' => 'number',
                'label' => __t('General.Garage_pay'),
                'class' => 'input-disabled',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'number_subscription',
            array(
                'type' => 'text',
                'label' => __t('General.Number_subscription'),
                'class' => 'input-disabled',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'start_date',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'fecha-js from-js input-disabled',
                'id' => 'start_date',
                'data-to' => '#end_date',
                'label' => __t('General.From'),
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'end_date',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'fecha-js to-js input-disabled',
                'id' => 'end_date',
                'data-from' => '#start_date',
                'label' => __t('General.To'),
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        ); ?>
        <div class="cnt-check-visit cnt-form-inputs-max-width">
            <?php
            echo $this->Form->input(
                'billed_by_aag',
                array(
                    'type' => 'checkbox',
                    'value' => 1,
                    'label' => __t('General.Billed_by_aag'),
                    'class' => 'input-disabled',
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            );
            echo $this->Form->input(
                'online_ordering',
                array(
                    'type' => 'checkbox',
                    'value' => 1,
                    'label' => __t('General.Online_ordering'),
                    'disabled' => $this->request->action == 'add' ? false : true,
                    'class' => 'input-disabled',
                )
            ); ?>
        </div>
    </div>
    <div class="ta-right btn-hide" hidden>
        <?php
        if (isset($garage_value_add)) {
            echo $this->Html->link(
                __t('General.Delete'),
                array(),
                array(
                    'escape' => false,
                    'title' => __t('General.Delete'),
                    'class' => 'aag-button medium red swal-msg',
                    'data-confirmmsg' => __t('Garage.Value_add_delete?'),
                    'data-yes' => __t('General.Yes'),
                    'data-no' => __t('General.No'),
                    'data-type' => 'warning',
                    'data-url' => Router::url(array(
                        'controller' => 'garages_values_adds',
                        'action' => 'delete',
                        $garage_value_add['GarageValueAdd']['garage_id'],
                        $garage_value_add['GarageValueAdd']['id'],
                    )),
                )
            );
        }
        ?>
    </div>
</div>
</div>

<?php echo $this->Form->end(); ?>