<?php
echo $this->Html->script('maintenance_events.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/purify.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
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
            __t('Maintenance.Events')
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
    </div>
</div>
<div class="aag-tabs">
    <ul>
        <li>
            <input id="tab-add" type="radio" class="d-none" name="tabs" checked>
            <label for="tab-add">
                <?php echo __t('General.Add'); ?>
            </label>
        </li>
        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
            <li>
                <input id="tab-edit" type="radio" class="d-none" name="tabs">
                <label for="tab-edit">
                    <?php echo __t('General.Edit'); ?>
                </label>
            </li>
        <?php } ?>
    </ul>
</div>
<div class="cnt-data">
    <div class="cnt-form-search">
        <?php echo $this->element('../Elements/Comun/search_maintenance'); ?>
    </div>
    <?php
    if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
    ?>
        <div id="form_click">
            <div id="form-add" class="aag-padding">
                <?php
                echo $this->Form->create('Appointment', array('url' => 'ajax_add_event', 'id' => 'FormAddEvent')); ?>
                <div class="aag-subtitle">
                    <?php echo __t('Maintenance.Appointment_type_add'); ?>
                </div>
                <div class="cnt-form-inputs">
                    <?php
                    echo $this->Form->input(
                        'AppointmentType.name_en',
                        array(
                            'label' => __t('Maintenance.Appointment_type_name_en') . $required_en,
                            'type' => 'text',
                            'id' => 'name-event-add-en'
                        )
                    );
                    echo $this->Form->input(
                        'AppointmentType.name_fr',
                        array(
                            'label' => __t('Maintenance.Appointment_type_name_fr'),
                            'type' => 'text',
                            'id' => 'name-event-add-fr'
                        )
                    );
                    echo $this->Form->input(
                        'AppointmentType.name_de',
                        array(
                            'label' => __t('Maintenance.Appointment_type_name_de'),
                            'type' => 'text',
                            'id' => 'name-event-add-de'
                        )
                    );
                    ?>
                </div>
                <div class="ta-right p-vertical-1">
                    <?php
                    echo $this->Form->Button(
                        __t('General.Save'),
                        array(
                            'class' => 'aag-button medium green',
                            'type' => 'submit',
                            'id' => 'add-event'
                        )
                    );
                    ?>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
            <div id="form-edit" class="d-none">
                <?php echo $this->Form->create('Appointment', array('id' => 'FormEditEvent', 'url' => 'ajax_edit_appointment_event')); ?>
                <div class="aag-padding">
                    <div class="aag-subtitle"><?php echo __t('Maintenance.Appointment_type_edit'); ?></div>
                    <div class="cnt-form-inputs">
                        <?php
                        echo $this->Form->input(
                            'AppointmentType.name_en',
                            array(
                                'label' => __t('Maintenance.Appointment_type_name_en') . $required_en,
                                'type' => 'text',
                                'id' => 'name-event-edit-en'
                            )
                        );
                        echo $this->Form->input(
                            'AppointmentType.name_fr',
                            array(
                                'label' => __t('Maintenance.Appointment_type_name_fr'),
                                'type' => 'text',
                                'id' => 'name-event-edit-fr'
                            )
                        );
                        echo $this->Form->input(
                            'AppointmentType.name_de',
                            array(
                                'label' => __t('Maintenance.Appointment_type_name_de'),
                                'type' => 'text',
                                'id' => 'name-event-edit-de'
                            )
                        );
                        ?>
                    </div>
                    <div class="p-vertical-1 ta-right">
                        <?php
                        echo $this->Form->button(
                            __t('General.Cancel'),
                            array(
                                'type' => 'button',
                                'class' => 'aag-button medium four',
                                'id' => 'unselect-event',
                                'disabled' => true
                            )
                        );
                        echo $this->Form->Button(
                            __t('General.Edit'),
                            array(
                                'class' => 'aag-button medium green',
                                'type' => 'submit',
                                'id' => 'edit-event'
                            )
                        );
                        ?>
                    </div>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
            <div id="form-edit-msg" class="d-none">
                <div class="aag-padding">
                    <div class="aag-subtitle"><?php echo __t('Maintenance.Appointment_type_edit'); ?></div>
                    <div class="cnt-form-inputs">
                        <?php
                        echo $this->Form->input(
                            '',
                            array(
                                'label' => __t('Maintenance.Appointment_type_name_en') . $required_en,
                                'type' => 'text',
                                'disabled' => true
                            )
                        );
                        echo $this->Form->input(
                            '',
                            array(
                                'label' => __t('Maintenance.Appointment_type_name_fr'),
                                'type' => 'text',
                                'disabled' => true
                            )
                        );
                        echo $this->Form->input(
                            '',
                            array(
                                'label' => __t('Maintenance.Appointment_type_name_de'),
                                'type' => 'text',
                                'disabled' => true
                            )
                        );
                        ?>
                    </div>
                    <div class="aag-subtitle"><?php echo __t('Maintenance.Appointment_type_select'); ?></div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div id="ajax_table_maintenance_events" class="aag-padding">
        <?php echo $this->element('../Appointments/Elements/table_maintenance_events'); ?>
    </div>
</div>

<script>
    $('.select_tr').on('click', function(e) {
        e.preventDefault();
        selected_id = $(this).attr('id');
        $('.dragdrop-delete-file-js').trigger('click');
        $(".select_tr").removeClass('bg-primary-i');
        $(this).addClass('bg-primary-i');
        var event_name_en = $(this).find('#language-en').text();
        var event_name_fr = $(this).find('#language-fr').text();
        var event_name_de = $(this).find('#language-de').text();
        event_name_en = $.trim(event_name_en);
        event_name_fr = $.trim(event_name_fr);
        event_name_de = $.trim(event_name_de);
        var url = $('#' + selected_id).attr('data-url');
        $('#name-event-edit-en').val(event_name_en);
        $('#name-event-edit-fr').val(event_name_fr);
        $('#name-event-edit-de').val(event_name_de);
        $('#img-event-edit').attr("src", '/img/iconos/' + url);
        $('#unselect-event').prop('disabled', false);
        $('#tab-add').prop("checked", false);
        $('#tab-edit').prop("checked", true);
        $('#form-edit-msg').hide();
        $('#form-add').hide();
        $('#form-edit').show();
    });

    $('#unselect-event').on('click', function(e) {
        e.preventDefault();
        $('#form-edit-msg').hide();
        $('#unselect-event').prop('disabled', true);
        $(".select_tr").removeClass('bg-primary-i');
        $('#img-event-add').attr("src", '/img/upload_pic.png');
        $('#form-add').show();
        $('#form-edit').hide();
        $('#name-event-add-en').val("");
        $('#name-event-add-fr').val("");
        $('#name-event-add-de').val("");
        $('#event-file-add').val("");
        $('.dragdrop-delete-file-js').trigger('click');
    });

    $('#tab-add').on('click', function() {
        $(".select_tr").removeClass('bg-primary-i');
        $('#form-add').show();
        $('#form-edit').hide();
        $('#form-edit-msg').hide();
    });

    $('#tab-edit').on('click', function() {
        $(".select_tr").removeClass('bg-primary-i');
        $('#form-edit-msg').show();
        $('#form-edit').hide();
        $('#form-add').hide();
    });

    $('#unselect-event').on('click', function() {
        $('#tab-add').trigger('click');
    });
</script>