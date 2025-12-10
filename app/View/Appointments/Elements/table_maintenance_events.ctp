<?php echo $this->Session->flash(); ?>
<div class="cnt-form-inputs">
    <?php
    foreach( $events_types_languages as $key => $event_type )
    {
        ?>
        <div class="item-remove-edit texto-elemento select_tr" id="event_<?php echo $event_type['AppointmentType']['id'] ?>" data-id="<?php echo $event_type['AppointmentType']['id'] ?>">
            <div id="language-en" <?php if($selected_language != 'name_en'){ echo "class='d-none'";}?>>
                <?php echo h($event_type['AppointmentType']['name_en']); ?>
            </div>
            <div id="language-fr" <?php if($selected_language != 'name_fr'){ echo "class='d-none'";}?>>
                <?php echo h($event_type['AppointmentType']['name_fr']); ?>
            </div>
            <div id="language-de" <?php if($selected_language != 'name_de'){ echo "class='d-none'";}?>>
                <?php echo h($event_type['AppointmentType']['name_de']); ?>
            </div>
            <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                echo $this->Html->link(
                    '<span class="aag-icon-papelera c-fallo"></span>',
                    'javascript:;',
                    array(
                        'class' => 'delete-event-js',
                        'data-confirmmsg' => __t('Maintenance.Event_delete?'),
                        'data-yes' => __t('General.Yes'),
                        'data-no' => __t('General.No'),
                        'data-url' => Router::url(array(
                            'controller' => 'appointments',
                            'action' => 'ajax_delete_event',
                            $event_type['AppointmentType']['id'],
                        )),
                        'data-id' => $event_type['AppointmentType']['id'],
                        'data-name' => $event_type['AppointmentType'][$selected_language],
                        'escape' => false,
                        'title' => __t('General.Delete'),
                    )
                );
            } ?>
        </div>
        <?php
    }
    ?>
</div>