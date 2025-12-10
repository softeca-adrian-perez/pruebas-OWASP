<?php
echo $this->Html->script('distributors_activities.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'DistributorCustomerActivity',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('DistributorCustomerActivity.id');
echo $this->Form->hidden('DistributorCustomerActivity.distributor_id');
$action = $this->request->action;
$config = CakeSession::read('Auth.User.Config');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Distributor.Distributors'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Distributor.Activity'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_activity',
                        $distributor_id
                    )
                ),
                __t('Distributor.Add_activity'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Distributor.Distributors'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Distributor.Activity'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_activity',
                        $distributor_id
                    )
                ),
                __t('Distributor.Edit_activity'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Distributor.Add_activity');
        } else {
            echo __t('Distributor.Edit_activity');
        }
        ?>
    </div>
    <div class="cnt-form-inputs m-top-1 m-bottom-1">
        <?php
        echo $this->Form->input(
            'customer_activity_id',
            array(
                'label' => __t('Activity.Activity'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'options' => $customer_activities,
                'required' => true,
            )
        );
        echo $this->Form->input(
            'start_date',
            array(
                'required' => true,
                'type' => 'text',
                'class' => 'fecha-js from-js',
                'data-to' => '#to',
                'id' => 'from',
                'label' => __t('Label.Start_date'),
            )
        );
        echo $this->Form->input(
            'end_date',
            array(
                'required' => true,
                'type' => 'text',
                'class' => 'fecha-js to-js',
                'data-from' => '#from',
                'id' => 'to',
                'label' => __t('Label.End_date'),
            )
        );
        ?>
        <?php if ($config[ConstantsConfig::ACTIVITY_TYPE]) { ?>
            <div class="columns medium-5" style="padding-top:2px;">
                <div class="fields_views" style="margin-left: 5px;margin-bottom: 7px;"><?php echo  __t('Activity.Type'); ?></div>
                <?php echo $this->Form->input(
                    'type',
                    array(
                        'type' => 'radio',
                        'label' => '_',
                        'options' => array(
                            '0' => __t('Distributor.Distributor'),
                            '1' => __t('Distributor.Workshop'),
                        ),
                        'legend' => false,
                        'div' => false,
                        'hiddenField' => false,
                    )
                ); ?>
            </div>
        <?php } ?>
        <?php if ($config[ConstantsConfig::WORKSHOP_ACTIVITIES]) { ?>
            <div class="columns medium-4 m-top-1 clear">
                <?php echo $this->Form->input(
                    'workshop_activity_id',
                    array(
                        'label' => __t('Distributor.Workshop_activities'),
                        'type' => 'select',
                        'class' => 'select2-multiple workshops',
                        'id' => 'workshop_activity_id',
                        'options' => $workshop_activities,
                        'required' => true,
                        'empty' => true,
                    )
                ); ?>
            </div>
            <div class="columns medium-4 m-top-1 end">
                <?php echo $this->Form->input(
                    'activity_details',
                    array(
                        'type' => 'text',
                        'required' => true,
                        'id' => 'activity_details',
                        'class' => 'details',
                        'label' => __t('Distributor.Activity_details'),
                    )
                ); ?>
            </div>
            <div class="columns medium-4 p-form cnt-buttons-v2">
                <button type='button' id='button_add' class="btn-add btn-guardar"><?php echo __t('General.Add') ?></button>
            </div>
            <div class="columns medium-12">
                <div class="medium-12 columns p-0 m-top-1">
                    <div class="o-auto">
                        <table class="table-tracking">
                            <thead>
                                <tr>
                                    <th><?php echo __t('Distributor.Workshop_activities'); ?></th>
                                    <th><?php echo __t('Distributor.Activity_details'); ?></th>
                                    <th><?php echo __t('General.Actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="table_workshop_detail">
                                <?php if ($this->request->action == ConstantsActionsNames::EDIT && isset($activity['DistributorCustomerActivity']['workshops'])) {
                                    foreach ($activity['DistributorCustomerActivity']['workshops'] as $workshop => $detail) { ?>
                                        <tr>
                                            <td class="select-option">
                                                <input type="hidden" value="<?php echo h($workshop); ?>" name="data[DistributorCustomerActivity][workshop_activity_id][]">
                                                <?php echo h($workshop_activities[$workshop]); ?>
                                            </td>
                                            <td>
                                                <input type="hidden" value="<?php echo h($detail); ?>" name="data[DistributorCustomerActivity][activity_details][]">
                                                <?php echo h($detail); ?>
                                            </td>
                                            <td>
                                                <span class="ion-android-cancel cursor-pointer c-fallo delete-workshop-activity"></span>
                                            </td>
                                        </tr>
                                <?php }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
            </div>
            <?php if ($this->action == ConstantsActionsNames::EDIT) { ?>
                <div class="m-bottom-1 p-left-0 ta-right">
                    <?php echo $this->Html->link(
                        "<span class='aag-icon-papelera'></span>" .
                            __t('General.Delete'),
                        array(
                            'controller' => 'distributors_activities',
                            'action' => 'delete',
                            $activity['DistributorCustomerActivity']['id'],
                            $distributor_id
                        ),
                        array(
                            'escape' => false,
                            'class' => 'aag-button medium red outlined delete-distributor-activity-js',
                            'data-confirmmsg' => __t('DistributorActivity.Confirm_delete'),
                            'data-yes' => __t('General.Yes'),
                            'data-no' => __t('General.No'),
                        )
                    ); ?>
                <?php } ?>
                </div>
    </div>

    <?php echo $this->Form->end(); ?>