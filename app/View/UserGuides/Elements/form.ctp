<?php
echo $this->Html->script('tutorials.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));


$action = $this->request->action;
if ($action == ConstantsActionsNames::EDIT) {
    $title = $tutorial_bd['Tutorial']['title'];
    $url = $tutorial_bd['Tutorial']['url'];
    $order_default = $tutorial_bd['Tutorial']['order'];
} else {
    $title = null;
    $url = null;
    $order_default = 1;
}

echo $this->Form->create(
    'Tutorial',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);

echo $this->Form->hidden('order');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Menu.User_guides'),
                    array(
                        'controller' => 'user_guides',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Menu.User_guides'),
                    array(
                        'controller' => 'user_guides',
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
<div class="cnt-data p-top-1">
    <div class="cnt-data-element">
        <div class="aag-title">
            <?php
            if ($action == ConstantsActionsNames::ADD) {
                echo __t('Menu.User_guides');
            } else {
                echo __t('Menu.User_guides');
            }
            ?>
        </div>
        <div class="cnt-form-inputs m-top-1">
            <?php if ($action == ConstantsActionsNames::EDIT) {
                if (isset($tutorial_bd['Tutorial']['user_id'])) { ?>
            <?php
                    echo $this->Form->hidden(
                        'user_id',
                        array(
                            'type' => 'text',
                            'required' => true,
                            'value' => $tutorial_bd['Tutorial']['user_id'],
                            'readonly' => true,
                            'label' => __t('Tutorial.User_creator'),
                        )
                    );
                    echo $this->Form->input(
                        'user_name',
                        array(
                            'type' => 'text',
                            'required' => true,
                            'value' => $tutorial_bd['User']['name'] . " " . $tutorial_bd['User']['surname'],
                            'readonly' => true,
                            'label' => __t('Tutorial.User_creator'),
                        )
                    );
                    echo $this->Form->input(
                        'creation_date',
                        array(
                            'type' => 'text',
                            'required' => true,
                            'value' => $tutorial_bd['Tutorial']['creation_date'],
                            'id' => 'creation-date',
                            'readonly' => true,
                            'label' => __t('Tutorial.Creation_date'),
                        )
                    );
                }
            }
            echo $this->Form->input(
                'title',
                array(
                    'required' => true,
                    'type' => 'text',
                    'value' => $title,
                    'id' => 'title',
                    'label' => __t('Tutorial.Title'),
                )
            );
            echo $this->Form->input(
                'url',
                array(
                    'required' => true,
                    'type' => 'text',
                    'value' => $url,
                    'id' => 'url',
                    'label' => __t('Tutorial.Url').' (&#9888; '.__t('Tutorial.Valid_url').')',
                )
            ); ?>
        </div>
        <div class="columns medium-12 m-top-1">
            <div class="title-in-fieldset">
                <?php echo __t('Tutorial.Roles'); ?>
            </div>
        </div>
        <div class="columns medium-12 p-vertical-1">
            <?php echo $this->Form->input(
                'TutorialRole.roles',
                array(
                    'label' => false,
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'options' => $roles,
                    'multiple' => true,
                    'id' => 'role',
                )
            ); ?>
        </div>
        <?php if ($this->request->action == ConstantsActionsNames::EDIT) { ?>
            <div class="p-bottom-1 ta-right">
                <?php echo $this->Html->link(
                    __t('General.Delete') . '<span class="aag-icon-papelera"></span>',
                    array(
                        'controller' => 'user_guides',
                        'action' => 'delete_tutorial',
                        $tutorial_id
                    ),
                    array(
                        'escape' => false,
                        'id' => 'delete-shortcut',
                        'class' => 'delete-tutorial-js aag-button medium outlined red',
                        'data-confirmmsg' => __t('Tutorial.Confirm_delete'),
                        'data-yes' => __t('General.Yes'),
                        'data-no' => __t('General.No'),
                    )
                ); ?>
            </div>
        <?php } ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>