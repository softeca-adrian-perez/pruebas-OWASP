<?php
echo $this->Form->create('User', array('id' => 'form', 'enctype' => 'multipart/form-data'));
echo $this->Form->hidden('User.id');

$action = $this->request->action;
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('User.Users'),
                    array(
                        'controller' => 'users',
                        'action' => 'listing'
                    )
                ),
                __t('User.New'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('User.Users'),
                    array(
                        'controller' => 'users',
                        'action' => 'listing'
                    )
                ),
                $this->Html->link(
                    __t('General.Edit'),
                    array(
                        'controller' => 'users',
                        'action' => 'my_data'
                    )
                ),
                __t('General.Reset_password')
            ));
        }
        ?>
        <?php
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions'); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        echo __t('General.Reset_password');
        ?>
    </div>
    <div class="cnt-form-inputs m-top-1 m-bottom-1">
        <?php
        echo $this->Form->input(
            'User.password',
            array(
                'type' => 'password',
                'required' => true,
                'label' => __t('User.New_password'),
            )
        );
        echo $this->Form->input(
            'User.password_repetido',
            array(
                'type' => 'password',
                'required' => true,
                'label' => __t('User.New_password_repeated'),
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>