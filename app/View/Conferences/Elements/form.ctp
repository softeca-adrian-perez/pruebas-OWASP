<?php
$action = $this->request->action;
echo $this->Form->create(
    'Conference',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden(
    'Conference.id'
);
$config = CakeSession::read('Auth.User.Config'); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Conference.Conferences'),
                    array(
                        'controller' => 'conferences',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Conference.Conferences'),
                    array(
                        'controller' => 'conferences',
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
            echo __t('Conference.Add_conference');
        } else {
            echo __t('Conference.Edit_conference');
        }
        ?>
    </div>
    <br />
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Conference.Name'),
            )
        );
        echo $this->Form->input(
            'start_date',
            array(
                'class' => 'fecha-js from-js clear_field',
                'type' => 'text',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Conference.Start_date'),
            )
        );
        echo $this->Form->input(
            'duration',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Conference.Duration'),
            )
        );
        echo $this->Form->input(
            'venue_id',
            array(
                'label' => __t('Conference.Venue'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'empty' => true,
                'options' => $venues
            )
        );
        echo $this->Form->input(
            'status',
            array(
                'label' => __t('Conference.Status'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $status,
                'empty' => true,
                'default' => ConstantsBooleans::YES
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>