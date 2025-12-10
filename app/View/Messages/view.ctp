<?php
$action = in_array($this->Acceso->user('role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'recieved_messages' : 'home';

$user = $this->Acceso->user();
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Mailbox.Mailbox'),
                array(
                    'controller' => 'messages',
                    'action' => $action
                )
            ),
            __t('General.View')
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            array(
                'controller' => 'messages',
                'action' => $action
            ),
            array('class' => 'aag-button medium four')
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding fg-0">
    <div class="aag-title m-bottom-1">
        <?php echo __t('General.View'); ?>
    </div>
    <div class="cnt-form-inputs">
        <div class="all-columns">
            <b class="fields_views"><?php echo __t('Message.Subject') ?>:</b>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo $message['Message']['subject']; ?>
            </div>
        </div>
        <div>
            <b class="fields_views"><?php echo __t('Message.Creation_date') ?>: </b>
            <br>
            <div class="b-bottom-1 height_input">
                <?php
                $date = $message['Message']['creation_date'];
                if ($user['aag_region_id'] == Configure::read('AAG_REGION_ID_BENELUX')) {
                    $date = date(Fecha::_FORMATO_BD_FECHA_HORA, strtotime($date . ' +1 hours'));
                }
                echo Fecha::toFormatoVistaFechaHora($date);
                ?>
            </div>
        </div>
        <div>
            <b class="fields_views"><?php echo __t('Message.Sent_date') ?>: </b>
            <br>
            <div class="b-bottom-1 height_input">
                <?php
                if ($message['Message']['date_sent']) {
                    $date = $message['Message']['date_sent'];
                    if ($user['aag_region_id'] == Configure::read('AAG_REGION_ID_BENELUX')) {
                        $date = date(Fecha::_FORMATO_BD_FECHA_HORA, strtotime($date . ' +1 hours'));
                    }
                    echo Fecha::toFormatoVistaFechaHora($date);
                } else {
                    echo __t('Message.Not_sent');
                }
                ?>
            </div>
        </div>
        <div>
            <b class="fields_views"><?php echo __t('Message.Type') ?>: </b>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo $types[$message['Message']['type']]; ?>
            </div>
        </div>
        <div class="all-columns">
            <b class="fields_views"><?php echo __t('Message.Body') ?>: </b>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo $message['Message']['body']; ?>
            </div>
        </div>
        <?php if (!in_array($this->Acceso->user('role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR))) { ?>
            <div class="all-columns">
                <b class="fields_views"><?php echo __t('Message.Recipients') ?>: </b>
                <br>
                <div class="b-bottom-1 height_input">
                    <?php echo implode(', ', $recipients); ?>
                </div>
            </div>
        <?php } ?>
        <div class="all-columns">
            <div id="file-list-js">
                <div class="m-bottom-1 mailing-attached-files">
                    <div class="row">
                        <?php if (!empty($message_files)) { ?>
                            <b class="fields_views"><?php echo __t('Email.Attachments') . ':'; ?> </b>
                        <?php
                        }
                        foreach ($message_files as $file) {
                        ?>
                            <div>
                                <?php
                                echo $this->Html->link(
                                    $file['MessageFile']['source_name'],
                                    array(
                                        'controller' => 'messages_files',
                                        'action' => 'download_file',
                                        $file['MessageFile']['id']
                                    )
                                );
                                ?>
                            </div>
                        <?php
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>