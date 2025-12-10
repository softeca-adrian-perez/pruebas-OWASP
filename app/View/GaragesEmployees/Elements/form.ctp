<?php
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'GarageEmployee',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('GarageEmployee.id');
echo $this->Form->hidden('GarageEmployee.garage_id');
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
                    __t('Employee.Employees'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_employee_garage',
                        $garage_id
                    )
                ),
                __t('Employee.New_employees'),
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
                    __t('Employee.Employees'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_employee_garage',
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
        <?php if ($action == ConstantsActionsNames::EDIT) { ?>
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
    <div class="aag-title m-bottom-1">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Employee.New_employees');
        } else {
            echo $employee_types[$this->request->data['GarageEmployee']['employee_type_id']];
        }
        ?>
    </div>
    <div class="cnt-form-inputs">
        <?php echo $this->Form->input(
            'GarageEmployee.employee_type_id',
            array(
                'label' => __t('Employee.Employee_type'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'empty' => false,
                'options' => $employee_types,
                'disabled' => $action == ConstantsActionsNames::ADD ? false : true,
                'class' => 'input-disabled',
            )
        );
        echo $this->Form->input(
            'GarageEmployee.number',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Employee.Number'),
                'disabled' => $action == ConstantsActionsNames::ADD ? false : true,
                'class' => 'input-disabled',
            )
        ); ?>
    </div>
</div>

<?php echo $this->Form->end(); ?>