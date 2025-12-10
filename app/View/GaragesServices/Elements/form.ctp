<?php
$action = $this->request->action;
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'GarageValueAddSupplier',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('GarageValueAddSupplier.id');
echo $this->Form->hidden('GarageValueAddSupplier.garage_id', array('id' => 'garage_id', 'value' => $garage_id));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Garage.Garages'),
                    array(
                        'controller' => 'garages',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Equipment.Activities_Services'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_activities_and_services_garage',
                        $garage_id
                    )
                ),
                __t('Garage.New_Services'),
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
                    __t('Equipment.Activities_Services'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_activities_and_services_garage',
                        $garage_id
                    )
                ),
                __t('Garage.Edit_services'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element(
            'Comun/form_actions_garage',
            $cancel_action
        ); ?>
        <?php
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
        <?php
        } ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Garage.New_services');
        } else {
            echo __t('Garage.Edit_services');
        }
        ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <?php echo $this->Form->input(
            'value_add_supplier_id',
            array(
                'label' => __t('Garage.Value_add_suppliers'),
                'type' => 'select',
                'class' => 'select2-multiple input-disabled',
                'id' => 'select-value-add-supplier',
                'multiple' => false,
                'empty' => true,
                'options' => $value_and_suppliers,
                'disabled' => $this->request->action == 'add' ? false : true,

            )
        );
        echo $this->Form->input(
            'value_add_supplier_type_id',
            array(
                'label' => __t('Garage.Value_and_supplier_type'),
                'type' => 'select',
                'class' => 'select2-multiple input-disabled',
                'id' => 'select-value-add-supplier-type',
                'multiple' => false,
                'empty' => true,
                'options' => $value_and_supplier_type,
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'from_date',
            array(
                'class' => 'fecha-js from-js input-disabled',
                'type' => 'text',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Garage.From_date'),
                'id' => 'from-date',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        );
        echo $this->Form->input(
            'to_date',
            array(
                'class' => 'fecha-js from-js input-disabled',
                'type' => 'text',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Garage.To_date'),
                'id' => 'to-date',
                'disabled' => $this->request->action == 'add' ? false : true,
            )
        ); ?>
    </div>
</div>

<?php echo $this->Form->end(); ?>