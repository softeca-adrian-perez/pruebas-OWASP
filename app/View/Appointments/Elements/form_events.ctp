<?php
echo $this->Html->script('appointments.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/fullcalendar-3.10.5/fullcalendar.min.css', array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/lib/moment.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/fullcalendar.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/locale/' .  __l() . '.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/purify.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;
$config = CakeSession::read('Auth.User.Config');
if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
    $hide_save = true;
}
echo $this->Form->create('Appointment', array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'id' => 'appointment-form'));
echo $this->Form->hidden('Appointment.id', array('id' => 'appointment-id'));
echo $this->Form->hidden('Appointment.id', array('id' => 'is_form_event'));
echo $this->Form->hidden('Appointment.appointment_status_id', array('value' => ConstantsStatusAppointments::EVENT));
echo $this->Form->hidden('Appointment.check_send_event', array('id' => 'check_send_event'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == 'add_event') {
            echo $this->Html->breadcrumb(
                array(
                    $this->Html->link(__t('CRM.Crm'), array('controller' => 'dashboard', 'action' => 'home')),
                    $this->Html->link(__t('Event.Events'), array('controller' => 'appointments', 'action' => 'home')),
                    __t('General.Add')
                )
            );
        } else {
            echo $this->Html->breadcrumb(
                array(
                    $this->Html->link(__t('CRM.Crm'), array('controller' => 'dashboard', 'action' => 'home')),
                    $this->Html->link(__t('Event.Events'), array('controller' => 'appointments', 'action' => 'home')),
                    __t('General.Edit')
                )
            );
        }
        ?>
    </div>
    <div>
        <?php
        if (!isset($hide_save)) {
            if ($action == 'edit_event') {
                echo $this->Html->link(
                    __t('Event.Cancel_event'),
                    array(
                        'controller' => 'appointments',
                        'action' => 'delete_event',
                        $appointment['Appointment']['id']
                    ),
                    array(
                        'escape' => false,
                        'id' => 'cancel_appointment',
                        'title' => __t('Event.Cancel_event'),
                        'class' => 'aag-button medium red',
                    )
                );
            }
        }
        echo $this->element('Comun/form_actions_appointment', $cancel_action);
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php if ($action == 'add_event') {
            echo __t('Event.Add_event');
        } else {
            echo __t('Event.Edit_event');
        } ?>
    </div>
    <div class="cnt-form-inputs m-vertical-1">
        <?php echo $this->Form->input(
            'Appointment.title',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Event.Title'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        ); ?>
    </div>
    <div class="cnt-form-inputs m-bottom-1">
        <?php
        echo $this->Form->input(
            'Appointment.appointment_type_id',
            array(
                'type' => 'select',
                'class' => 'select2-multiple',
                'options' => $event_types,
                'multiple' => false,
                'empty' => false,
                'required' => true,
                'default' => 1,
                'label' => __t('General.Type'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'Appointment.user_assigned_id',
            array(
                'label' => __t('Appointment.Assign_to'),
                'class' => 'clear_field select2Dinamico_user update_users',
                'type' => 'select',
                'multiple' => false,
                'default' => CakeSession::read('Auth.User.id'),
                'empty' => false,
                'options' => isset($users) ? $users : array(),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        ?>
        <div>
            <label>
                <?php echo __t('Appointment.Notify_to') . '<span style="color:red"> *</span>'; ?>
            </label>
            <?php
            echo $this->Form->input(
                'Appointment.appointment_contact_lists',
                array(
                    'label' => false,
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => true,
                    'empty' => true,
                    'options' => $contact_lists,
                    'id' => 'notify_to',
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
        </div>
        <?php
        if ($config[ConstantsConfig::CHECK_SEND_NOTIFICATION]) {
        ?>
            <div class="flex jc-end">
                <div class="cont-services follow_up m-0">
                    <label class="ai-center m-0-i" id="generate_alerts">
                        <?php
                        echo $this->Form->input(
                            'Notification.generate_alerts',
                            array(
                                'type' => 'checkbox',
                                'label' => false,
                                'checked' => false,
                                'div' => false,
                                'class' => 'custom-check',
                                'hidden' => true,
                                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                            )
                        );
                        ?>
                        <span class="icon-mail unselectable" style="color: inherit; font-size: 16px;"></span>
                        <?php echo __t('Task.Generate_alerts'); ?>
                    </label>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
    <div class="cnt-form-inputs m-bottom-1">
        <?php
        echo $this->Form->input(
            'Appointment.date',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'fecha-js from-js',
                'id' => 'appointment-date',
                'data-to' => '#appointment-date-end',
                'div' => array('class' => 'datepicker datepicker-label-block'),
                'label' => __t('Event.Start_date'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'Appointment.end_date',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'fecha-js to-js',
                'id' => 'appointment-date-end',
                'data-from' => '#appointment-date',
                'div' => array('class' => 'datepicker datepicker-label-block'),
                'label' => __t('Event.End_date'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'Appointment.start_time',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'timepicker',
                'id' => 'start_time',
                'label' => __t('Event.Start_time'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'Appointment.end_time',
            array(
                'label' => false,
                'type' => 'text',
                'class' => 'timepicker',
                'required' => true,
                'id' => 'end_time',
                'label' => __t('Event.End_time'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        ?>
    </div>
    <label class="m-bottom-1 m-right-0">
        <?php
        echo __t('Appointment.Feedback') . '<span style="color:red"> *</span>';
        echo $this->Form->input(
            'Appointment.feedback',
            array(
                'label' => false,
                'id' => 'feedback_area',
                'type' => 'textarea',
                'value' => isset($this->request->data['Appointment']['feedback']) ? $this->request->data['Appointment']['feedback'] : '',
                'rows' => 4,
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        ?>
    </label>
    <div class="cnt-form-inputs">
        <div>
            <label>
                <?php echo __t('Appointment.Feeling') . '<span style="color:red"> *</span>'; ?>
            </label>
            <div class="cont-radio-image w-100p p-vertical-1 cont-radio" style="flex-direction: row-reverse; gap: 8px 15px; flex-wrap: wrap; justify-content: flex-end;">
                <?php
                if (!isset($hide_save)) {
                    foreach ($appointment_feelings as $feeling) {
                ?>
                        <div class="d-inline-block end appointment_feelings">
                            <label class="ta-center" style="color: #9ea4b2; font-size: 14px; white-space: nowrap;margin: 0; display: flex; flex-direction: column; gap: 10px;">
                                <?php
                                echo $this->Form->input(
                                    'Appointment.appointment_feeling_id',
                                    array(
                                        'type' => 'radio',
                                        'label' => false,
                                        'options' => array($feeling['AppointmentFeeling']['id'] => ''),
                                        'title' => $feeling['AppointmentFeeling']['name' . __s()],
                                        'id' => 'radioImage',
                                        'class' => 'radio_feelings',
                                        'div' => false,
                                        'hiddenField' => false,
                                    )
                                );
                                ?>
                                <span class="d-block ta-center">
                                    <?php echo $this->Html->image(FilePaths::ICONS_IMAGES_RELATIVE . $feeling['AppointmentFeeling']['icon'], array('style' => 'padding: 0; width: 50px; min-width: 50px; height: 50px; min-height: 50px;')); ?>
                                </span>
                                <div>
                                    <?php echo $feeling['AppointmentFeeling']['name' . __s()]; ?>
                                </div>
                            </label>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="flex jc-center">
            <div class="cont-services follow_up m-0">
                <label class="ai-center m-0-i">
                    <?php
                    if (isset($appointment['Appointment']['requires_follow_up']) && $appointment['Appointment']['requires_follow_up'] == ConstantsBooleans::YES) {
                        $checked = true;
                    } else {
                        $checked = false;
                    }
                    echo $this->Form->input(
                        'Appointment.requires_follow_up',
                        array(
                            'type' => 'checkbox',
                            'label' => false,
                            'div' => false,
                            'value' => 1,
                            'checked' => $checked,
                            'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        )
                    );
                    ?>
                    <span class="icon-follow unselectable"></span>
                    <?php echo __t('Appointment.Requires_follow_up'); ?>
                </label>
            </div>
        </div>
        <div></div>
        <div></div>
    </div>
</div>
<?php
echo $this->Form->end();
if ($action == 'edit_event') {
?>
    <div class="cnt-data aag-padding">
        <div class="text-button">
            <div class="aag-title m-bottom-1">
                <?php echo __t('Appointment.Tasks'); ?>
            </div>
            <?php
            echo $this->Form->button(
                "<span class='aag-icon-editar'></span>" . __t('Appointment.Create_task'),
                array(
                    'id' => 'create_task',
                    'escape' => false,
                    'data-open' => "taskModal",
                    'type' => 'button',
                    'title' => __t('Appointment.Create_task'),
                    'class' => 'aag-button small outlined three',
                    'data-is_event' => true,
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
        </div>
        <?php
        // echo $this->Form->button(__t('General.Cancel'), array('type' => 'button'));
        // echo $this->Form->button(__t('Task.Save_task'), array('type' => 'button','data-url' => Router::url(array('controller' => 'tasks', 'action' => 'ajax_submit_form'))));
        ?>
        <div id="cnt_task">
            <?php
            if (empty($tasks)) {
            ?>
                <div class="ta-center p-bottom-1">
                    <?php echo __t('Task.No_task_created'); ?>
                </div>
            <?php
            } else {
                echo $this->element('../Appointments/Elements/tasks');
            }
            ?>
        </div>
    </div>
    <div class="cnt-data aag-padding">
        <div class="aag-title m-bottom-1">
            <?php echo __t('Appointment.Comments'); ?>
        </div>
        <?php
        if ($action == 'edit_event') {
        ?>
            <div style="display: grid; grid-template-columns: 75px 1fr; gap: 15px">
                <div>
                    <?php $user = CakeSession::read('Auth.User'); ?>
                    <div class="cnt-avatar-usuario">
                        <?php
                        $image = false;
                        foreach ($users_images as $user_image) {
                            if (CakeSession::read('Auth.User.id') == $user_image['UserImage']['user_id']) {
                                echo $this->Html->image(
                                    Router::url(
                                        array(
                                            'controller' => 'users_images',
                                            'action' => 'download_file',
                                            'plugin' => false,
                                            $user_image['UserImage']['id'],
                                        )
                                    ),
                                    array(
                                        'alt' => '',
                                        'class' => "img_comment_big"
                                    )
                                );
                                $image = true;
                            }
                        }
                        if (empty($users_images) || $image == false) {
                            $first = mb_substr($user['Contact']['first_name'], 0, 1, 'UTF-8');
                            $second = mb_substr($user['Contact']['last_name'], 0, 1, 'UTF-8');
                            echo $first . $second;
                        }
                        ?>
                    </div>
                    <div style="color: #606060; font-size: 11px; text-align: center;">
                        <?php echo $user['Contact']['first_name'] . '<br />' . $user['Contact']['last_name']; ?>
                    </div>
                </div>
                <div>
                    <label>
                        <?php echo __t('Appointment.Write_your_comment'); ?>
                    </label>
                    <?php
                    echo $this->Form->input(
                        'TaskComment.own',
                        array(
                            'label' => false,
                            'type' => 'textarea',
                            'id' => 'comment-body',
                            'style' => 'border-radius: 5px;',
                            'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        )
                    );
                    ?>
                </div>
            </div>
            <div class="columns medium-12 ta-right m-top-1">
                <?php
                echo $this->Form->button(
                    "<span class='ion-paper-airplane'></span>" . __t('Appointment.Send_comment'),
                    array(
                        'id' => 'send-comment',
                        'escape' => false,
                        'type' => 'button',
                        'title' => __t('Appointment.Send_comment'),
                        'class' => 'aag-button medium',
                        'data-appointment' => $appointment['Appointment']['id'],
                        'data-url' => Router::url(
                            array(
                                'controller' => 'appointments_comments',
                                'action' => 'ajax_add',
                                ConstantsBooleans::ACTIVE
                            )
                        ),
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    )
                );
                ?>
            </div>
            <div id="comment-ajax">
                <?php echo $this->element('../Appointments/Elements/comments'); ?>
            </div>
        <?php
        }
        ?>
    </div>
<?php
}
?>
<div style="display: none;" id="taskModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div id="modal_form_task" class="medium-12 columns p-right-0">
        <?php echo $this->element('../Appointments/Elements/form_create_task_germany'); ?>
    </div>
    <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
</div>
<script>
    <?php
    if (isset($debrief_tasks_list)) {
    ?> tasks_list_array_js = <?php echo json_encode($debrief_tasks_list) . ';';
                                }
                                if (isset($debrief_topics_list)) {
                                    ?> topics_list_array_js = <?php echo json_encode($debrief_topics_list) . ';';
                                }
                                if (isset($debrief_tasks)) {
                                    ?> tasks_array_js = <?php echo json_encode($debrief_tasks) . ';';
                                }
                                if (isset($debrief_topics)) {
                            ?> topics_array_js = <?php echo json_encode($debrief_topics) . ';';
                                }
                                ?>
</script>
<style>
    div.content div {
        /* box-shadow: 0 0 0 1px red inset; */
    }

    .select2-container .select2-selection--multiple ul.select2-selection__rendered {
        padding-top: 1px !important;
    }

    .select2-container .select2-selection--multiple ul.select2-selection__rendered li {
        margin-top: 2px !important;
        margin-bottom: -2px !important;
    }

    .cnt-avatar-usuario {
        margin-top: 26px;
    }

    #comment-ajax {
        display: grid !important;
        grid-template-columns: 75px 1fr;
        gap: 15px;
    }

    #comment-ajax .fieldset-comments {
        padding: 10px 15px !important;
        min-height: 75px
    }

    #comment-ajax .fieldset-comments * {
        font-size: 13px !important;
        font-weight: 300 !important;
    }

    #comment-ajax .cnt-avatar-usuario {
        margin-top: 23px;
    }

    #comment-ajax span.tip {
        display: none !important;
    }

    #comment-ajax .load-more {
        grid-column-start: 1;
        grid-column-end: -1;
    }

    .cnt-breadcrumb input#btn-guardar-no-exit.aag-button.medium.green {
        margin: 0;
    }
</style>