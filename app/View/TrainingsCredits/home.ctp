
<?php
echo $this->Form->create(
    'TrainingCredit',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
    echo $this->Form->hidden('TrainingCredit.id');
    ?>
    <div class="cnt-breadcrumb">
        <div>
            <?php
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Maintenance.Maintenance'),
                    array(
                        'controller' => 'maintenance',
                        'action' => 'home'
                    )
                ),
                __t('Credits.Credits')
            ));
            ?>
        </div>
        <div>
            <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
        </div>
    </div>
    <div class="cnt-data aag-padding">
        <div class="aag-title p-bottom-1">
            <?php echo __t('Credits.Credit_equivalence') ?>
        </div>
        <div class="cnt-form-inputs">
            <?php
            echo $this->Form->input(
                'pound',
                array(
                    'type' => 'decimal',
                    'required' => true,
                    'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                    'label' => __t('Credits.Pounds') . ' ' . $country['Country']['symbol'],
                    'value' => ($training_credit_exists) ? $training_credit_exists['TrainingCredit']['pound'] : null,
                )
            );
            echo $this->Form->input(
                'credit',
                array(
                    'type' => 'decimal',
                    'required' => true,
                    'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN)? false : true,
                    'label' => __t('Credits.Credits'),
                    'value' => ($training_credit_exists) ? $training_credit_exists['TrainingCredit']['credit'] : null,
                )
            );
            ?>
        </div>
    </div>
<?php echo $this->Form->end(); ?>