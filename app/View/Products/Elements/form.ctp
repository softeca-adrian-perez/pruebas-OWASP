<?php
echo $this->Html->script('products.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));

$action = $this->request->action;

echo $this->Form->create(
    'Product',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);

echo $this->Form->hidden('Product.id');
echo $this->Form->hidden('ProductImage.id');
echo $this->element('Comun/loader'); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Products.Products'),
                    array(
                        'controller' => 'products',
                        'action' => 'maintenance_products'
                    )
                ),
                __t('Products.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Products.Products'),
                    array(
                        'controller' => 'products',
                        'action' => 'maintenance_products'
                    )
                ),
                __t('Products.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $url_cancel); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Products.Products'); ?>
    </div>
    <div class="cnt-form-inputs m-top-1">
        <?php
        echo $this->Form->input(
            'name',
            array(
                'required' => true,
                'type' => 'text',
                'id' => 'name',
                'label' => __t('Products.Name'),
            )
        ); ?>
        <label class="center-check p-top-1">
            <?php echo __t('Products.Active'); ?>
            <div class="aag-switch round small">
                <?php echo $this->Form->input('active', array('id' => 'activeInput-7', 'label' => false, 'div' => false, 'type' => 'checkbox')); ?>
                <?php // echo !empty($garage_network['GarageNetwork']['quoting_active']) ? 'checked' : '' 
                ?>
                <label for="activeInput-7"></label>
            </div>
        </label>
    </div>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Products.Image'); ?>
        <span class="c-fallo fs-smaller m-right-auto">*</span>
    </div>
    <span class="fs-small"><?php echo __t('Products.Supported_formats') ?> (png, jpg)</span>
    <div id="cnt-img">
        <?php
        echo $this->Form->input(
            'ProductImage.file',
            array(
                'id' => 'image-input',
                'class' => 'dragdrop-js',
                'label' => false,
                'type' => 'file',
                'multiple' => false,
                'before' => '<div class="text-drop">' . __t('Products.Image') . '</div>',
                'div' => array(
                    'class' => 'field_file cont-fileWrapper',
                ),
            )
        );
        echo $this->Form->hidden(
            'ProductImage.new_image',
            array(
                'id' => 'new-image-input',
            )
        );
        ?>
    </div>
    <?php if (!empty($product['ProductImage']['file'])) { ?>
        <div id="currentTitle" class="columns medium-12 m-top-1 m-bottom-1">
            <?php echo __t('Shortcut.Actually_image'); ?>
        </div>
        <div class="columns medium-12">
            <?php
            echo $this->Html->image(
                FileManager::get_url(FilePaths::PRODUCTS_IMAGES_RELATIVE . $product['ProductImage']['file']),
                array(
                    'alt' => $product['ProductImage']['file'],
                    'title' => $product['ProductImage']['file'],
                    'class' => 'logotipo',
                    'style' => 'height:50px',
                )
            );
            ?>
        </div>
    <?php } ?>
    <?php if (isset($product_id)) { ?>
        <div class="columns medium-12 m-top-1 ta-right">
            <?php
            echo $this->Html->link(
                '<span class="aag-icon-papelera"></span>' . __t('Products.Delete'),
                array(
                    'controller' => 'products',
                    'action' => 'delete',
                    $product['Product']['id'],
                ),
                array(
                    'escape' => false,
                    'class' => 'delete-product-js aag-button medium outlined red',
                    'data-confirmmsg' => __t('Products.Confirm_delete'),
                    'data-yes' => __t('General.Yes'),
                    'data-no' => __t('General.No'),
                )
            )
            ?>
        </div>
    <?php } ?>
    <div class="aag-subtitle">
        <?php echo __t('Brands.Brands'); ?>
        <span class="c-fallo fs-smaller m-right-auto">*</span>
    </div>
    <div class="row">
        <div class="cnt-form-inputs cont-services w-100p">
            <?php foreach ($brands as $key => $brand) { ?>
                <div class="end <?php echo ($key % 6 == 0) ? "clear" : "" ?> brands_checkbox"
                    data-id="<?php echo $key ?>">
                    <label>
                        <?php
                        $valor = false;
                        if (isset($product) && !empty($product)) {
                            if ($brand['Brand']['id'] == $product['Product']['brand_id']) {
                                $valor = true;
                            }
                        }
                        if (!empty($this->request->data['Product']['brands_id'])) {
                            if ($this->request->data['Product']['brands_id'][$key] != ConstantsBooleans::NO) {
                                $valor = true;
                            }
                        }
                        ?>
                        <?php
                        echo $this->Form->input(
                            'Product.brands_id.' . $key,
                            array(
                                'type' => 'checkbox',
                                'label' => false,
                                'div' => false,
                                'data-id' => $key,
                                'value' => $brand['Brand']['id'],
                                'checked' => $valor
                            )
                        );
                        ?>
                        <span class="unselectable p-bottom-1 m-horizontal-auto" title="<?php echo h($brand['Brand']['name']) ?>">
                            <?php
                            echo $this->Html->image(
                                FileManager::get_url(FilePaths::BRANDS_IMAGES_RELATIVE . $brand['BrandImage']['file']),
                                array(
                                    'alt' => $brand['Brand']['name']
                                )
                            );
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