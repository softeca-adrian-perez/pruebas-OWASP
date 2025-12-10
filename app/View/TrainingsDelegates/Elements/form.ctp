<?php
$action = $this->request->action;
echo $this->Html->script('training_delegate.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('training_delegates_credits.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'TrainingDelegate',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'delegate_form',
    )
);
echo $this->Form->hidden('TrainingDelegate.id');
$readonly = false;
if ($action == ConstantsActionsNames::EDIT) {
    $readonly = true;
}
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Training.Trainings_delegates'),
                array(
                    'controller' => 'trainings_delegates',
                    'action' => 'home',
                    $training_planned_course_id
                )
            ),
            $action == ConstantsActionsNames::ADD ? __t('General.Add') : __t('General.Edit'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->element(
            $action == ConstantsActionsNames::ADD ? 'Comun/form_actions_delegates' : 'Comun/form_actions',
            $cancel_action
        );
        ?>
    </div>
</div>

<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo $action == ConstantsActionsNames::ADD ? __t('Training.Add_delegates') : __t('Training.Edit_delegates'); ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <div class="two-columns">
            <?php
            echo $this->Form->input(
                'training_course_id',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Training.Course_name'),
                    'disabled' => true,
                    'value' => $course['TrainingCourse']['name'],
                )
            );
            ?>
        </div>
        <div>
            <?php
            echo $this->Form->input(
                'start_date',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Conference.Start_date'),
                    'disabled' => true,
                    'value' => Fecha::toFormatoVistaFecha($planned_course['TrainingPlannedCourse']['date_from']),
                )
            );
            ?>
        </div>
        <div>
            <?php
            echo $this->Form->input(
                'training_provider_id',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('TrainingProvider.Name'),
                    'disabled' => true,
                    'value' => $provider['TrainingProvider']['name'],
                )
            );
            ?>
        </div>
        <div class='required'>
            <?php
            echo $this->Form->input(
                'garage_id',
                array(
                    'label' => __t('Training.Workshop'),
                    'type' => 'select',
                    'required' => true,
                    'class' => 'update_garages_only_live update_garages-js',
                    'empty' => false,
                    'multiple' => false,
                    'options' => isset($array_garage_name) ? $array_garage_name : array(),
                    'id' => 'garage_id',
                    'data-url' => Router::url(array(
                        'controller' => 'trainings_delegates',
                        'action' => 'ajax_select_network',
                    )),
                    'disabled' => $readonly,
                )
            );
            ?>
        </div>
        <div>
            <?php
            echo $this->Form->input(
                'network_id',
                array(
                    'label' => __t('Network.Network'),
                    'type' => 'select',
                    'class' => 'select2-multiple network_class networks_select-js',
                    'empty' => true,
                    'required' => true,
                    'id' => 'network_id',
                    'data-is_edit-js' => $readonly ? '1' : '0',
                    'data-url' => Router::url(array(
                        'controller' => 'trainings_delegates',
                        'action' => 'ajax_select_delegate',
                    )),
                    'disabled' => $readonly,
                )
            );
            ?>
        </div>
        <div>
            <?php
            echo $this->Form->input(
                'delegate_id',
                array(
                    'id' => 'delegate_id',
                    'label' => __t('Delegate.Delegate'),
                    'type' => 'select',
                    'class' => 'select2-multiple delegate_class',
                    'empty' => true,
                    'required' => true,
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
        </div>
        <div>
            <?php
            echo $this->Form->input(
                'position_id',
                array(
                    'label' => __t('Training.Employment_position'),
                    'type' => 'select',
                    'class' => 'select2-multiple position_id',
                    'options' => $position_list,
                    'empty' => true,
                    'id' => 'position_id',
                    'disabled' => true,
                )
            );
            ?>
        </div>
        <div>
            <?php
            echo $this->Form->input(
                'order_number',
                array(
                    'id' => 'order_number',
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Training.Purchase_order_number'),
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
        </div>
        <div>
            <?php
            echo $this->Form->input(
                'invoice_number',
                array(
                    'id' => 'invoice_number',
                    'type' => 'text',
                    'required' => false,
                    'label' => __t('Training.Invoice_number'),
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
        </div>
    </div>
    <?php if ($action == ConstantsActionsNames::EDIT) { ?>
        <div>
            <?php
            echo $this->Form->input(
                'is_refund_eligible',
                array(
                    'type' => 'select',
                    'class' => 'select2-multiple refund_eligible',
                    'options' => $is_refundable,
                    'empty' => false,
                    'required' => true,
                    'id' => 'is_refund_eligible',
                    'label' => __t('Training.Credits_taken'),
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
        </div>
        <div class="hidden-input">
            <?php
            echo $this->Form->input(
                'reason_delegate_id',
                array(
                    'type' => 'select',
                    'class' => 'select2-multiple reason_delegate',
                    'options' => $reasons,
                    'empty' => false,
                    'required' => true,
                    'id' => 'reason_delegate_id',
                    'label' => __t('Training.Not_taken_reason'),
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
        </div>
    <?php } ?>
</div>
<input type="hidden" name="training_planned_course_id" id="training_planned_course_id" value="<?php if (!empty($training_planned_course_id)) {
                                                                                                    echo $training_planned_course_id;
                                                                                                } ?>" />
<input type="hidden" name="add_credits_input" id="add_credits_input" />
<input type="hidden" name="reason_delegate_id" id="reason_delegate_id" />
<input type="hidden" name="contact_id_get" id="contact_id_get" value="<?php if (!empty($contact_id)) {
                                                                            echo $contact_id;
                                                                        } ?>" />
<input type="hidden" name="network_id_get" id="network_id_get" value="<?php if (!empty($network_id)) {
                                                                            echo $network_id;
                                                                        } ?>" />
</div>
<?php echo $this->Form->end(); ?>