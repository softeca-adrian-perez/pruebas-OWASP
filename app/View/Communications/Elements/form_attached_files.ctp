<?php foreach ($communication_files as $file) { ?>
    <div class="medium-12 columns end" style="padding: 0.25em;">
        <?php
        echo $this->Html->link(
            $file['CommunicationFile']['source_name'],
            array(
                'controller' => 'communications_files',
                'action' => 'download_file',
                $file['CommunicationFile']['id']
            )
        );
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
                        'controller' => 'communications_files',
                        'action' => 'ajax_delete_file',
                        $file['CommunicationFile']['id']
                    )),
                    'data-id' => $file['CommunicationFile']['id'],
                    'data-div' => '#file-list-js',
                    'escape' => false,
                    'title' => __t('General.Delete'),
                )
            );
        ?>
        <hr class="files m-0"/>
    </div>
<?php } ?>
<script>
    JQueryHelper.load();
</script>
