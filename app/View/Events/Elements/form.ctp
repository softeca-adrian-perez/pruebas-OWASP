<?php
echo $this->Html->css('../js/lib/fullcalendar-3.10.5/fullcalendar.min.css', array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/lib/moment.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/fullcalendar.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/locale/' .  __l() . '.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

echo $this->Form->create(
    'Event',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'appointment-form'
    )
);

echo $this->Form->hidden('Event.id');
$action = $this->request->action;
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('CRM.Crm'),
                    array(
                        'controller' => 'dashboard',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Event.Events'),
                    array(
                        'controller' => 'appointments',
                        'action' => 'home'
                    )
                ),
                __t('General.Add')
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('CRM.Crm'),
                    array(
                        'controller' => 'dashboard',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Event.Events'),
                    array(
                        'controller' => 'appointments',
                        'action' => 'home'
                    )
                ),
                __t('General.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php
        echo $this->element(
            'Comun/form_actions',
            $cancel_action
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Event.Add_event');
        } else {
            echo __t('Event.Edit_event');
        }
        ?>
    </div>
    <div class="cnt-form-inputs">
        <?php echo $this->Form->input(
            'Event.title',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Event.Title'),
            )
        );
        echo $this->Form->input(
            'Event.description',
            array(
                'label' => __t('Event.Description'),
                'type' => 'textarea',
            )
        ); ?>
        <div class="aag-subtitle">
            <?php echo __t('Event.Notify_to'); ?>
        </div>
        <?php
        echo $this->Form->input(
            'Event.appointment_contact_lists',
            array(
                'label' => false,
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => true,
                'empty' => false,
                'options' => $contact_lists,
                'id' => 'notify_to'
            )
        );
        echo $this->Form->input(
            'Event.event_type_id',
            array(
                'type' => 'select',
                'options' => $event_types,
                'multiple' => false,
                'empty' => false,
                'required' => true,
                'default' => 1,
                'label' => __t('General.Type'),
            )
        );
        echo $this->Form->input(
            'Event.start_date',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'fecha-js from-js',
                'data-to' => '#appointment-date-end',
                'id' => 'appointment-date',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Event.Start_date'),
            )
        );
        echo $this->Form->input(
            'Event.end_date',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'fecha-js to-js',
                'data-from' => '#appointment-date',
                'id' => 'appointment-date-end',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Event.End_date'),
            )
        );
        echo $this->Form->input(
            'Event.start_time',
            array(
                'label' => false,
                'type' => 'text',
                'required' => true,
                'class' => 'timepicker',
                'after' =>
                '<label style="z-index: 3;">' . __t('Event.Start_time') . '</label>'
            )
        );
        echo $this->Form->input(
            'Event.end_time',
            array(
                'label' => false,
                'type' => 'text',
                'class' => 'timepicker',
                'required' => true,
                'after' =>
                '<label style="z-index: 3;">' . __t('Event.End_time') . '</label>'
            )
        ); ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>