<?php if(!isset($data_form)){ $data_form = 'task'; } ?>
<div class="row">
    <div class="medium-12 columns">
        <?php foreach( $task_files as $file ){ ?>
            <div style="padding-top:5px">
                <?php
                    echo $this->Html->link(
                        $file['TaskFile']['source_name'],
                        array(
                            'controller' => 'tasks_files',
                            'action' => 'download_file',
                            $file['TaskFile']['file_guid']
                        )
                    );
                    echo "   " . $this->Html->link(
                        '<span class="aag-icon-papelera c-fallo"></span>',
                        array(),
                        array(
                            'data-form' => $data_form,
                            'class' => 'swal-msg-ajax f-right',
                            'data-confirmmsg' => __t('General.Delete_file?'),
                            'data-ajax' => true,
                            'data-yes' => __t('General.Yes'),
                            'data-no' => __t('General.No'),
                            'data-type' => 'warning',
                            'data-url' => Router::url(array(
                                'controller' => 'tasks_files',
                                'action' => 'ajax_delete_file',
                                $file['TaskFile']['file_guid']
                            )),
                            'data-id' => $file['TaskFile']['file_guid'],
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