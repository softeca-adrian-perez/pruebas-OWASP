<?php
echo $this->Html->script('tinymce/tinymce.min.js');
echo $this->Html->script('tinymce_init.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Html->script('value_added_suppliers.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;

echo $this->Form->create(
    'ValueAddedSupplier',
    array(
        'id' => 'form',
        'type' => 'post',
        'enctype' => 'multipart/form-data'
    )
);

?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('ValueAddedSuppliers.ValueAddedSuppliers'),
                array(
                    'controller' => 'value_added_suppliers',
                    'action' => 'management_home'
                )
            ),
            $action == ConstantsActionsNames::ADD ? __t('ValueAddedSuppliers.Add') : __t('ValueAddedSuppliers.Edit')
        ));
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('ValueAddedSuppliers.Add');
        } else {
            echo __t('ValueAddedSuppliers.Edit');
        }
        ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <?php
        echo $this->Form->input(
            'title',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('ValueAddedSuppliers.Title'),
            )
        );
        echo $this->Form->input(
            'supplier_url',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('ValueAddedSuppliers.Url'),
            )
        );
        ?>
    </div>
    <div class="cnt-form-inputs-file-drop p-top-1">
        <?php if (isset($valueAddedSupplier['ValueAddedSupplier']['logo_image'])) { ?>
            <div class="clear-column">
                <img class="trading_image img_table" src="<?php echo FileManager::get_url(FilePaths::VALUE_ADDED_SUPPLIERS_LOGO_IMAGES_RELATIVE . $valueAddedSupplier['ValueAddedSupplier']['logo_image']); ?>" />
            </div>
        <?php } ?>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
        <?php
        echo $this->Form->input(
            'ValueAddedSupplier.logo_image',
            array(
                'id' => 'service-file-add',
                'class' => 'dragdrop-js',
                'label' => __t('ValueAddedSuppliers.Logo'),
                'type' => 'file',
                'multiple' => false,
                'before' => '<div class="text-drop">' . __t('Network.Drop_files') . '</div>',
                'after' => '<div class="ta-center">' . __t('Network.Img_extension') . '</div>',
                'div' => array(
                    'class' => 'field_file cont-fileWrapper right-dir-file-wrapper',
                ),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->hidden(
            'new_image',
            array(
                'id' => 'new-image-input',
            )
        )
        ?>
    </div>
    <?php
    echo $this->Form->input(
        'description',
        array(
            'label' => __t('ValueAddedSuppliers.Description'),
            'type' => 'textarea',
            'rows' => 30,
            'cols' => 25,
            'class' => 'ta-high description_tinymce-js',
            'data-type' => ConstantsTypesTinyMce::VALUE_ADD_SUPPLIER,
            'data-message_file_size' => __t(ConstantsMessages::MAX_FILE_SIZE),
            'data-general_error' => __t('General.Error'),
            'required' => false,
        )
    );
    ?>
</div>
<div style="display: none;" id="cropImageModal" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <h4 id="modalTitle"><?php echo __t('Crop.Crop_the_photo'); ?></h4>
    <img id="image-to-crop" alt="crop" src="" />
    <p class="text-center m-0-i p-top-1">
        <button class="aag-button medium green m-0-i tres" id="crop-image"><?php echo __t('General.Save'); ?></button>
    </p>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div>
<?php echo $this->Form->end(); ?>