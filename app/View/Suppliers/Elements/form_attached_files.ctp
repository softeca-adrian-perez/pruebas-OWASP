<?php
foreach ($supplier_files as $category_name => $suppliers_category) {
    $category_id = $suppliers_categories_reverse[$category_name] ?>
    <div id="cnt_category_name-<?php echo $category_id?>" class="medium-12 columns end titulo-category-suppliers" style="padding-top:15px;" data-num_items="<?php echo count($suppliers_category) ?>">
        <?php echo h($category_name); ?>
    </div>

    <div id="cnt_category_id-<?php echo $category_id?>-js">
        <?php
        foreach ($suppliers_category as $file) { ?>
            <div class="medium-12 columns end" id="file_id-<?php echo $file['SupplierFile']['id']?>-js" data-category_id="<?php echo $category_id?>">
                <?php echo
                $this->form->hidden(
                    'SupplierCategory.' . $file['SupplierFile']['id'],
                    array(
                        'id' => 'supplier_category_hidden-js' . $file['SupplierFile']['id'],
                        'value' => $file['SupplierFile']['supplier_category_id']
                    )
                )
                ?>
                <div class="medium-1 columns end" >
                    <?php
                    if($file['SupplierFile']['active'] == ConstantsBooleans::YES){?>
                        <span id="<?php echo 'supplier_active-js-' . $file['SupplierFile']['id']?>" class="ion-checkmark c-exito" title="<?php echo __t('General.Active')?>"></span>
                    <?php
                    }else{?>
                        <span id="<?php echo 'supplier_active-js-' . $file['SupplierFile']['id']?>" class="ion-close c-fallo" title="<?php echo __t('General.Inactive')?>"></span>
                    <?php
                    }?>
                </div>
                <div class="medium-5 columns end" >
                    <?php
                    echo $this->Html->link(
                        $file['SupplierFile']['name'],
                        array(
                            'controller' => 'suppliers_files',
                            'action' => 'download_file',
                            $file['SupplierFile']['id']
                        ),
                        array(
                            'class' => 'supplier_file_name-js',
                            'id' => 'supplier_file_name-js-' . $file['SupplierFile']['id'],
                        )
                    );
                    ?>
                </div>
                <div class="medium-2 columns ta-right f-right" style="user-select: none;">
                    <span
                        class="aag-icon-editar c-primary cursor-pointer supplier_category_edit-js fs-x-large"
                        id="<?php echo 'supplier_category_edit-js' . $file['SupplierFile']['id'] ?>"
                        data-file_id="<?php echo $file['SupplierFile']['id']?>"
                        data-category_id="<?php echo $file['SupplierFile']['supplier_category_id']?>"
                        data-name="<?php echo $file['SupplierFile']['name']?>"
                        data-active="<?php echo $file['SupplierFile']['active']?>"
                        data-checked="<?php echo ConstantsBooleans::ACTIVE ?>"
                        data-url_get_category="<?php echo Router::url(array(
                            'controller' => 'suppliers_files',
                            'action' => 'ajax_get_category_file'
                        ))?>"
                        data-active_options='<?php echo json_encode(array(0 => __t('General.No'), 1 => __t('General.Yes')))?>'
                        data-suppliers_categories='<?php echo json_encode($suppliers_categories)?>'
                    >
                    </span>
                    <?php
                    echo $this->Html->link(
                        '<span class="aag-icon-papelera c-fallo fs-x-large"></span>',
                        array(),
                        array(
                            'class' => 'supplier_file_delete-js p-left-1',
                            'data-confirmmsg' => __t('General.Delete_file?'),
                            'data-ajax' => true,
                            'data-yes' => __t('General.Yes'),
                            'data-no' => __t('General.No'),
                            'data-type' => 'warning',
                            'data-url' => Router::url(array(
                                'controller' => 'suppliers_files',
                                'action' => 'ajax_delete_file',
                                $file['SupplierFile']['id']
                            )),
                            'data-id' => $file['SupplierFile']['id'],
                            'data-div' => '#file-list-js',
                            'escape' => false,
                            'title' => __t('General.Delete'),
                        )
                    );
                    ?>
                </div>
            </div>
        <?php
        } ?>
    </div>
<?php
}

foreach($suppliers_categories as $key_category_id => $category_list_name){
    if(!in_array($category_list_name, array_keys($supplier_files))){?>
        <div id="cnt_category_name-<?php echo $key_category_id?>" class="medium-12 columns end titulo-category-suppliers d-none" style="padding-top:15px" data-num_items="0">
            <?php echo h($category_list_name); ?>
        </div>

        <div id="cnt_category_id-<?php echo $key_category_id?>-js">

        </div>
    <?php
    }
}?>



<script>
    JQueryHelper.load();
</script>
