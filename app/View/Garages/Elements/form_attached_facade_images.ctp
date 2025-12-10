<div class="m-bottom-1 mailing-attached-files">
    <?php
    if (isset($garage_principal_image['GarageImage'])) {
        foreach ($garage_principal_image['GarageImage'] as $file) {
    ?>
            <div>
                <?php
                echo $this->Html->link(
                    '<span class="ion-trash-b c-fallo fs-1" style="display: inline-block;float: left;margin: 4px 5px 0 0;"></span>',
                    'javascript:;',
                    array(
                        'class' => 'delete-file-js',
                        'data-confirmmsg' => __t('General.Delete_file?'),
                        'data-url' => Router::url(array(
                            'controller' => 'garages_images',
                            'action' => 'ajax_delete_file',
                        )),
                        'data-id' => $file['id'],
                        'data-div' => '#file-image-js',
                        'data-yes' => __t('General.Yes'),
                        'data-no' => __t('General.No'),
                        'data-type' => 'warning',
                        'escape' => false,
                        'title' => __t('General.Delete'),
                    )
                );
                echo $this->Html->link(
                    $file['source_name'],
                    array(
                        'controller' => 'garages_images',
                        'action' => 'download_file',
                        $file['id']
                    )
                );
                ?>
            </div>
    <?php
        }
    } ?>
</div>