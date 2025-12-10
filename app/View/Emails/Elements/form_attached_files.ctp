<div class="m-bottom-1 mailing-attached-files">
    <div class="row">
        <?php
        if (!isset($email['Attachments']['Event_action'])) {
            if (!empty($email['Attachments']['Files'])) {
        ?>
                <strong><?php echo __t('Email.Attachments') ?>: </strong>
                <?php
            }
            if (isset($email['Attachments']['Files'])) {
                if (isset($email['Attachments']['Title']) && $email['Attachments']['Title'] == $types[ConstantsEmailTypes::APPOINTMENT]) {
                    foreach ($email['Attachments']['Files'] as $file) {
                ?>
                        <div class="medium-12 columns">
                            <?php
                            echo $this->Html->link(
                                $file['AppointmentFile']['file_guid'],
                                array(
                                    'controller' => 'appointments_files',
                                    'action' => 'download_file',
                                    $file['AppointmentFile']['file_guid']
                                )
                            );
                            ?>
                        </div>
                    <?php
                    }
                } elseif (isset($email['Attachments']['Title']) && $email['Attachments']['Title'] == $types[ConstantsEmailTypes::TASK]) {
                    foreach ($email['Attachments']['Files'] as $file) {
                    ?>
                        <div class="medium-12 columns">
                            <?php
                            echo $this->Html->link(
                                $file['TaskFile']['file'],
                                array(
                                    'controller' => 'tasks_files',
                                    'action' => 'download_file',
                                    $file['TaskFile']['file_guid']
                                )
                            );
                            ?>
                        </div>
        <?php
                    }
                }
            }
        }
        ?>
    </div>
</div>