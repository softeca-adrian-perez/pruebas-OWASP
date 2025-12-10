<?php
$action = $this->request->action;
echo $this->Html->script('inputsValidations.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'TrainingTrainer',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('TrainingTrainer.id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Training.Training'),
                    array(
                        'controller' => 'trainings_providers',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Training.Trainings_trainers'),
                    array(
                        'controller' => 'trainings_trainers',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Training.Training'),
                    array(
                        'controller' => 'trainings_providers',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Training.Trainings_trainers'),
                    array(
                        'controller' => 'trainings_trainers',
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
    <div class="aag-title m-bottom-1">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Training.Add_trainers');
        } else {
            echo __t('Training.Edit_trainers');
        }
        ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'training_provider_id',
            array(
                'label' => __t('Training.Training_provider'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'empty' => true,
                'options' => $trainings_providers
            )
        );
        echo $this->Form->input(
            'name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Training.Name'),
            )
        );
        echo $this->Form->input(
            'email',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Training.Email'),
            )
        );
        echo $this->Form->input(
            'phone',
            array(
                'type' => 'text',
                'label' => __t('Training.Phone'),
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>