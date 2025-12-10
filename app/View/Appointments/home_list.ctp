<?php
echo $this->Html->script('agenda_list.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));

$user = CakeSession::read('Auth.User.id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('CRM.Crm'),
                array(
                    'controller' => 'dashboard',
                    'action' => 'home'
                )
            ),
            __t('Appointment.Agenda_list'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            '<span class="icon-agenda_list"></span>' . __t('Home.Calendar'),
            array(
                'controller' => 'appointments',
                'action' => 'home'
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium one'
            )
        );
        if(CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                '<span class="icon-Visit_create"></span>' . __t('Appointment.Create_visit'),
                array(
                    'controller' => 'appointments',
                    'action' => 'add'
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium one'
                )
            );
            echo $this->Html->link(
                '<span class="icon-evemt-create"></span>' . __t('Appointment.Create_event'),
                array(
                    'controller' => 'appointments',
                    'action' => 'add_event',
                    $user
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium one'
                )
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title m-bottom-1">
        <?php echo __t('Appointment.Agenda_list'); ?>
    </div>
    <?php echo $this->element('../Appointments/Elements/search'); ?>
    <div id="agenda-lists">
        <?php echo $this->element('../Appointments/Elements/ajax_home_list');?>
    </div>
</div>
