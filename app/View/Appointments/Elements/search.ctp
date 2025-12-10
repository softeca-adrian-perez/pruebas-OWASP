<?php
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search m-0-i',
        'type' => 'get',
        'url' => array(
            'controller' => 'appointments',
            'action' => 'home_list',

        ),
    )
);
$user = CakeSession::read('Auth.User');
?>

<div class="cnt-form-search-title">
    <?php echo __t('General.Search'); ?>
</div>
<div class="cnt-form-inputs">
    <?php echo $this->Form->input(
        'name_customer',
        array(
            'type' => 'text',
            'id' => 'name_customer',
            'class' => 'search_ajax clear_field',
            'data-url' => Router::url(
                array(
                    'controller' => 'appointments',
                    'action' => 'ajax_search_customer_home_list',
                )
            ),
            'required' => true,
            'label' => __t('Appointment.Customer'),
        )
    );
    echo $this->Form->input(
        'user_assigned_id',
        array(
            'label' => __t('Alert.Assigned_to'),
            'type' => 'select',
            'class' => 'clear_field select2Dinamico_user_bdm update_users',
            'options' => isset($users) ? $users : array(),
            'empty' => true,
            'multiple' => false,
            'id' => 'user_assigned_id',
            'required' => true,
            'default' => isset($this->request->query['user']) ? $this->request->query['user'] : CakeSession::read('Auth.User.id')
        )
    );
    echo $this->Form->input(
        'from',
        array(
            'type' => 'text',
            'required' => true,
            'class' => 'fecha-js from-js clear_field',
            'id' => 'from',
            'data-to' => '#to',
            'label' => __t('General.From'),
        )
    );
    echo $this->Form->input(
        'to',
        array(
            'type' => 'text',
            'required' => true,
            'class' => 'fecha-js to-js clear_field',
            'id' => 'to',
            'data-from' => '#from',
            'label' => __t('General.To'),
        )
    );
    ?>
    <div class="two-columns">
        <?php
        echo $this->Form->input(
            'appointment_status_id',
            array(
                'type' => 'select',
                'options' => $status,
                'class' => 'select2-multiple clear_field',
                'required' => true,
                'multiple' => true,
                'empty' => true,
                'id' => 'appointment_status_id',
                'label' => __t('Appointment.Status'),
            )
        ); ?>
    </div>
    <?php
    echo $this->Form->input(
        'appointment_feeling_id',
        array(
            'label' => __t('Appointment.Feeling'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $feelings_list,
            'empty' => true,
            'id' => 'appointment_feeling_id',
            'required' => true,
            'data-feelings_images' => json_encode($feelings_list_img)
        )
    );
    echo $this->Form->input(
        'appointment_type_id',
        array(
            'label' => __t('Appointment.Type'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $appointment_types,
            'empty' => true,
            'id' => 'appointment_type_id',
            'required' => true,
        )
    );
    echo $this->Form->input(
        'feedback_fill_up',
        array(
            'label' => __t('Appointment.Feedback_fill_up'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $appointment_fill_up,
            'empty' => true,
            'id' => 'feedback_fill_up',
            'required' => true,
        )
    );
    ?>
    <div class="unselectable p-form cnt-check-visit cnt-form-inputs-max-width">
        <?php echo $this->Form->input(
            'requires_follow_up',
            array(
                'label' => __t('Appointment.Requires_follow_up'),
                'type' => 'checkbox',
                'class' => 'clear_field',
                'id' => 'requires_follow_up',
                'required' => true,
            )
        ); ?>
    </div>
</div>
<div class="cnt-form-search-buttons">
    <?php
    echo $this->Form->button(
        __t('General.Search'),
        array(
            'type' => 'submit',
            'class' => 'aag-button medium',
        )
    );
    echo $this->Form->button(
        "<span class='aag-icon-escoba'></span>",
        array(
            'id' => 'clear_field',
            'class' => 'aag-button medium four outlined',
            'escape' => false,
            'title' => __t('General.Clean_search')
        )
    );
    ?>
</div>
<?php echo $this->Form->end(); ?>
<br />