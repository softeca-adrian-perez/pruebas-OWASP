<?php
$action = $this->request->action;
echo $this->Html->script('agreements.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

echo $this->Form->create(
    'Agreement',
    array(
        'id' => 'form',
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('Agreement.id'); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Agreement.Agreements'),
                    array(
                        'controller' => 'agreements',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Agreement.Agreements'),
                    array(
                        'controller' => 'agreements',
                        'action' => 'home'
                    )
                ),
                __t('General.Edit'),
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
            echo __t('Agreement.New_external_agreement');
        } else {
            echo __t('Agreement.Edit_agreement');
        }
        ?>
    </div>
    <br />
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'Agreement.nombre',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Agreement.Name'),
            )
        );
        echo $this->Form->input(
            'Agreement.codigo',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Agreement.Code'),
            )
        );
        echo $this->Form->input(
            'Agreement.aag_region_id',
            array(
                'label' => __t('General.Region'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'required' => true,
                'options' => $aag_regions,
                'empty' => $user_role_id == ConstantsRoles::SUPER_ADMIN ? true : false,
                'disabled' => $user_role_id == ConstantsRoles::SUPER_ADMIN ? false : true,
                'value' => isset($network) ? $network['Network']['aag_region_id'] : $user_aag_region_id,
                'id' => 'aag-region-select',
            )
        ); ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>