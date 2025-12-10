<?php
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'GarageComment',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('GarageComment.id');
echo $this->Form->hidden('GarageComment.garage_id');
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
                    __t('Garage.Comments'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_comments',
                        $garage_id
                    )
                ),
                __t('Garage.Comments_add'),
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
                    __t('Garage.Comments'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_comments',
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
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Garage.Comments_add');
        } else {
            echo __t('Garage.Comment');
        }
        ?>
    </div>
    <div class="background-color-primary d-inline-block p-vertical-1">
        <?php if ($this->request->action == ConstantsActionsNames::EDIT) { ?>
            <div class="cnt-form-inputs m-bottom-1">
                <?php echo $this->Form->input(
                    'user_id',
                    array(
                        'type' => 'text',
                        'label' => __t('User.User'),
                        'value' => isset($users[$garage_comments['GarageComment']['user_id']]) ? $users[$garage_comments['GarageComment']['user_id']] : $garage_comments['GarageComment']['created_by_name'],
                        'disabled' => true,
                    )
                );
                echo $this->Form->input(
                    'creation_date',
                    array(
                        'type' => 'text',
                        'label' => __t('Garage.Creation_date'),
                        'value' => date("d-m-Y / H:i", strtotime($garage_comments['GarageComment']['creation_date'])),
                        'disabled' => true,
                    )
                ); ?>
            </div>
        <?php } ?>
        <div class="cnt-form-inputs">
            <?php echo $this->Form->input(
                'body',
                array(
                    'label' => __t('Garage.Comment'),
                    'type' => 'textarea',
                    'rows' => '15',
                    'disabled' => $this->request->action == 'add' ? false : true,
                )
            ); ?>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>