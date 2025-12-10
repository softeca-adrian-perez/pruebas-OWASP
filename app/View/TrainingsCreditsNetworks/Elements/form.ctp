<?php

echo $this->Form->create(
    'TrainingNetworkCredit',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('TrainingNetworkCredit.id');
$action = $this->request->action;
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Training.Trainings_credits'),
                    array(
                        'controller' => 'trainings_credits_networks',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Training.Trainings_credits'),
                    array(
                        'controller' => 'trainings_credits_networks',
                        'action' => 'home'
                    )
                ),
                __t('Training.Add_credits'),
            ));
        }
        ?>
    </div>
    <div>
        <?php
        echo $this->element(
            'Comun/form_actions',
            $cancel_action
        ); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Training.Add_credits'); ?>
    </div>
    <div class="cnt-form-inputs m-top-1">
        <?php echo $this->Form->hidden(
            'garage_network_id',
            array(
                'value' => $garage_network_id,
            )
        );
        echo $this->Form->hidden(
            'contact_id',
            array(
                'value' => $contact_id,
            )
        );
        echo $this->Form->input(
            'Garage_name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Training.Garage_name'),
                'disabled' => true,
                'value' => $garage['Garage']['business_name'],
            )
        );
        echo $this->Form->input(
            'credit_given',
            array(
                'type' => 'number',
                'required' => true,
                'label' => __t('Training.Amount_credits'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                'default' => 0
            )
        );
        echo $this->Form->input(
            'reason_allowance_id',
            array(
                'label' => __t('Training.Reason_extra_allowance'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'options' => $reasons_allowances,
                'empty' => true,
            )
        );
        ?>
    </div>
</div>
</div>
<?php echo $this->Form->end(); ?>