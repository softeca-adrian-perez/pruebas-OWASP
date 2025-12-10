<div class="row">
    <div class="medium-12 columns" style="padding-left: 15px;padding-right: 15px;">
        <?php foreach( $message_files as $file ){ ?>
            <div style="padding-top:5px">
                <?php
                    echo $this->Html->link(
                        $file['MessageFile']['source_name'],
                        array(
                            'controller' => 'messages_files',
                            'action' => 'download_file',
                            $file['MessageFile']['id']
                        )
                    );
                    echo "   " . $this->Html->link(
                        '<span class="aag-icon-papelera c-fallo"></span>',
                        array(),
                        array(
                            'data-form' => 'message',
                            'class' => 'swal-msg-ajax',
                            'data-confirmmsg' => __t('Message.Delete?'),
                            'data-ajax' => true,
                            'data-yes' => __t('General.Yes'),
                            'data-no' => __t('General.No'),
                            'data-type' => 'warning',
                            'data-url' => Router::url(array(
                                'controller' => 'messages_files',
                                'action' => 'ajax_delete_file',
                                $file['MessageFile']['id']
                            )),
                            'data-id' => $file['MessageFile']['id'],
                            'data-div' => '#file-list-js',
                            'escape' => false,
                            'title' => __t('General.Delete'),
                        )
                    );
                ?>
            </div>
            <hr class="files m-0"/>
        <?php } ?>
    </div>
</div>