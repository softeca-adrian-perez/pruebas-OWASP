<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Suppliers.Suppliers'),
                array(
                    'controller' => 'suppliers',
                    'action' => 'maintenance_suppliers'
                )
            ),
            __t('Suppliers.View'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('Suppliers.Back'),
            array(
                'controller' => 'suppliers',
                'action' => 'maintenance_suppliers',
            ),
            array(
                'class' => 'aag-button medium four',
            )
        );
        echo $this->Html->link(
            __t('Suppliers.Edit') . '<span class="aag-icon-editar c-informacion"></span> ',
            array(
                'controller' => 'suppliers',
                'action' => 'edit',
                $supplier['Supplier']['id'],
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium',
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Suppliers.Suppliers'); ?>
    </div>
        <div class="cnt-form-inputs p-top-1">
            <div>
                <strong><?php echo __t('Suppliers.Name') ?>: </strong>
                <br>
                <div class="b-bottom-1 height_input">
                    <?php echo h($supplier['Supplier']['name']); ?>
                </div>
            </div>
            <div>
                <strong><?php echo __t('Suppliers.Publication_date') ?>: </strong>
                <br>
                <div class="b-bottom-1 height_input">
                    <?php echo h($supplier['Supplier']['publication_date']); ?>
                </div>
            </div>
            <div>
                <strong><?php echo __t('Suppliers.Active') ?>: </strong>
                <br>
                <div class="b-bottom-1 height_input">
                    <?php echo ($supplier['Supplier']['active']) ? __t('General.Yes') : __t('General.No'); ?>
                </div>
            </div>
            <div>
                <strong><?php echo __t('Suppliers.Web') ?>: </strong>
                <br>
                <div class="b-bottom-1 height_input">
                    <?php echo h($supplier['Supplier']['web']); ?>
                </div>
            </div>
            <div class="two-columns">
                <strong><?php echo __t('Suppliers.Youtube_channel') ?>: </strong>
                <br>
                <div class="b-bottom-1 height_input">
                    <?php echo h($supplier['Supplier']['url_channel']); ?>
                </div>
            </div>
            <div class="two-columns">
                <strong><?php echo __t('Suppliers.Youtube_url') ?>: </strong>
                <br>
                <div class="b-bottom-1 height_input">
                    <?php echo h($supplier['Supplier']['url_video']); ?>
                </div>
            </div>
            <div class="three-columns">
                <strong><?php echo __t('Suppliers.Description') ?>: </strong>
                <br>
                <div class="b-bottom-1 height_input">
                    <?php echo h($supplier['Supplier']['description']); ?>
                </div>
            </div>

            <div>
                <?php if(!empty($supplier['SupplierImage']['file'])) { ?>
                <div class="p-top-1 ta-center">
                    <img
                        src="<?php echo FileManager::get_url(FilePaths::SUPPLIERS_IMAGES_RELATIVE . $supplier['SupplierImage']['source_name']); ?>"
                        style="max-height: 125px">
                </div>
                <?php } ?>
                <?php if(!empty($supplier['SupplierFile'])) { ?>
                <div class="p-top-1 p-normal">
                    <strong><?php echo __t('Suppliers.Files') ?></strong>
                    <br>
                    <div class="m-top-1">
                        <?php
                        foreach ($supplier['SupplierFile'] as $file) {
                            echo $this->Html->link(
                                $file['SupplierFile']['source_name'],
                                array(
                                    'controller' => 'suppliers_files',
                                    'action' => 'download_file',
                                    $file['SupplierFile']['id']
                                )
                            );
                            echo "<hr class='m-0' />";
                        }
                        ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>