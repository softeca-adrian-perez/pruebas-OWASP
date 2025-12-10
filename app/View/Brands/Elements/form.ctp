<?php
echo $this->Html->script('brands.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));

$action = $this->request->action;

echo $this->Form->create(
    'Brand',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);
echo $this->Form->hidden('Brand.id');
echo $this->Form->hidden('BrandImage.id'); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Brands.Brands'),
                    array(
                        'controller' => 'brands',
                        'action' => 'maintenance_brands'
                    )
                ),
                __t('Brands.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Brands.Brands'),
                    array(
                        'controller' => 'brands',
                        'action' => 'maintenance_brands'
                    )
                ),
                __t('Brands.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Brands.Brands'); ?>
    </div>
    <div class="cnt-form-inputs m-top-1">
        <?php
        echo $this->Form->input(
            'name',
            array(
                'required' => true,
                'type' => 'text',
                'id' => 'name',
                'label' => __t('Brands.Name'),
            )
        );
        ?>
        <label class="center-check p-top-1">
            <?php echo __t('Brands.Active'); ?>
            <div class="aag-switch round small">
                <?php echo $this->Form->input('active', array('id' => 'activeInput', 'label' => false, 'div' => false, 'type' => 'checkbox')); ?>
                <label for="activeInput"></label>
            </div>
        </label>
    </div>
    <div class="m-bottom-1" id="cnt-img">
        <div class="aag-subtitle">
            <?php echo __t('Brands.Image'); ?>
            <span class="c-fallo fs-smaller m-right-auto">*</span>
        </div>
        <span class="fs-small"><?php echo __t('Brands.Supported_formats') ?> (png, jpg)</span>
        <?php
        echo $this->Form->input(
            'BrandImage.file',
            array(
                'id' => 'image-input',
                'class' => 'dragdrop-js',
                'label' => false,
                'type' => 'file',
                'multiple' => false,
                'before' => '<div class="text-drop">' . __t('Brands.Image') . '</div>',
                'div' => array(
                    'class' => 'field_file cont-fileWrapper',
                ),
            )
        );
        echo $this->Form->hidden(
            'BrandImage.new_image',
            array(
                'id' => 'new-image-input',
            )
        );
        ?>
    </div>
    <?php if (!empty($brand['BrandImage']['file'])) { ?>
        <div id="currentTitle" class="m-top-1 m-bottom-1">
            <?php echo __t('Shortcut.Actually_image'); ?>
        </div>
        <div>
            <?php
            echo $this->Html->image(
                FileManager::get_url(FilePaths::BRANDS_IMAGES_RELATIVE . $brand['BrandImage']['file']),
                array(
                    'alt' => $brand['BrandImage']['file'],
                    'title' => $brand['BrandImage']['file'],
                    'class' => 'logotipo',
                    'style' => 'height:50px',
                )
            );
            ?>
        </div>
    <?php } ?>
    <?php if (isset($brand)) { ?>
        <div class="m-top-1 ta-right">
            <?php
            echo $this->Html->link(
                '<span class="aag-icon-papelera"></span>' . __t('Brands.Delete'),
                array(
                    'controller' => 'brands',
                    'action' => 'delete',
                    $brand['Brand']['id'],
                ),
                array(
                    'escape' => false,
                    'class' => 'delete-brand-js aag-button medium outlined red',
                    'data-confirmmsg' => __t('Brands.Confirm_delete'),
                    'data-yes' => __t('General.Yes'),
                    'data-no' => __t('General.No'),
                )
            )
            ?>
        </div>
    <?php } ?>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Suppliers.Suppliers'); ?>
        <span class="c-fallo fs-smaller m-right-auto">*</span>
    </div>
    <div class="p-top-1">
        <div class="d-inline-block cont-services w-100p">
            <?php foreach ($suppliers as $key => $supplier) { ?>
                <div class="columns end <?php echo ($key % 6 == 0) ? "clear" : "" ?> suppliers_checkbox"
                    data-id="<?php echo $key ?>">
                    <label>
                        <?php
                        $valor = false;
                        if (isset($brand) && $supplier['Supplier']['id'] == $brand['Brand']['supplier_id']) {
                            $valor = true;
                        }
                        if (isset($this->request->data['Brand']['supplier_id']) && $supplier['Supplier']['id'] == $this->request->data['Brand']['supplier_id']) {
                            $valor = true;
                        }

                        echo $this->Form->input(
                            'suppliers_id.',
                            array(
                                'type' => 'checkbox',
                                'label' => false,
                                'div' => false,
                                'data-id' => $key,
                                'value' => $supplier['Supplier']['id'],
                                'checked' => $valor
                            )
                        );
                        ?>
                        <span class="unselectable ta-center p-bottom-1" title="<?php echo h($supplier['Supplier']['name']) ?>">
                            <?php
                            echo $this->Html->image(
                                FileManager::get_url(FilePaths::SUPPLIERS_IMAGES_RELATIVE . $supplier['SupplierImage']['file']),
                                array(
                                    'alt' => $supplier['Supplier']['name']
                                )
                            )
                            ?>
                        </span>
                    </label>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>

<div style="display: none;" id="cropImageModal" class="reveal-modal background-color-primary"
    data-reveal
    aria-labelledby="modalTitle"
    aria-hidden="true"
    role="dialog"
    data-options="close_on_background_click:false">
    <h4 id="modalTitle"><?php echo __t('Crop.Crop_the_photo'); ?></h4>
    <img id="image-to-crop" alt="crop" src="" />
    <p class="text-center m-0-i p-top-1">
        <button class="aag-button medium green m-0-i tres" id="crop-image"><?php echo __t('General.Save'); ?></button>
    </p>
    <a class="close-modal" data-close aria-label="Close" id="close_modal">&#215;</a>
</div>