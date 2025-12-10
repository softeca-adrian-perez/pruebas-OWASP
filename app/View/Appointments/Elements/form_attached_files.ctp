<div class="row">
    <div class="medium-12 columns">
        <?php foreach ($appointment_files as $file) { ?>
            <div style="padding-top:5px">
                <?php
                echo $this->Html->link(
                    $file['AppointmentFile']['source_name'],
                    array(
                        'controller' => 'appointments_files',
                        'action' => 'download_file',
                        $file['AppointmentFile']['file_guid']
                    )
                );
                if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                    echo "   " . $this->Html->link(
                        '<span class="aag-icon-papelera c-fallo"></span>',
                        array(),
                        array(
                            'class' => 'swal-msg-ajax f-right',
                            'data-confirmmsg' => __t('General.Delete_file?'),
                            'data-ajax' => true,
                            'data-yes' => __t('General.Yes'),
                            'data-no' => __t('General.No'),
                            'data-type' => 'warning',
                            'data-url' => Router::url(array(
                                'controller' => 'appointments_files',
                                'action' => 'ajax_delete_file',
                                $file['AppointmentFile']['file_guid']
                            )),
                            'data-id' => $file['AppointmentFile']['file_guid'],
                            'data-div' => '#file-list-js',
                            'escape' => false,
                            'title' => __t('General.Delete'),
                        )
                    );
                }
                ?>
            </div>
            <hr class="files m-0" />
        <?php } ?>
    </div>
</div>