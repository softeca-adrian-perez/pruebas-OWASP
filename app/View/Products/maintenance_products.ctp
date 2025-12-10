<?php echo $this->Html->script('products.js?v='.Configure::read('VERSION_CACHE')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Products.Products'),
                array(
                    'controller' => 'products',
                    'action' => 'maintenance_products'
                )
            ),
            __t('Products.List'),
        ));
        ?>
    </div>
    <div class="f-right">
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('Products.New_product'),
                array(
                    'controller' => 'products',
                    'action' => 'add',
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium green',
                )
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Products/Elements/search'); ?>
    <div class="o-auto maintenance_products">
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Product.name', __t('Products.Name')); ?></th>
                <th class="ta-center"><?php echo __t('Products.Image'); ?></th>
                <th class="ta-center"><?php echo __t('Brands.Brand'); ?></th>
                <th class="ta-center"><?php echo $this->Paginator->sort('Product.active', __t('Products.Active')); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product) { ?>
                <tr>
                    <td>
                        <?php
                            if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                echo $this->Html->link(
                                    $product['Product']['name'],
                                    array(
                                        'controller' => 'products',
                                        'action' => 'edit',
                                        $product['Product']['id']
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                            } else {
                                echo $product['Product']['name'];
                            }
                        ?>
                    </td>
                    <td class="ta-center">
                        <?php if( isset($product['ProductImage']['file']) && $product['ProductImage']['file'] ) { ?>
                            <img src="<?php echo FileManager::get_url(FilePaths::PRODUCTS_IMAGES_RELATIVE . $product['ProductImage']['file']); ?>">
                        <?php } else { echo $product['Brand']['name']; } ?>
                    </td>
                    <td class="ta-center">
                        <?php if( isset($product['BrandImage']['file']) && $product['BrandImage']['file'] ) { ?>
                            <img src="<?php echo FileManager::get_url(FilePaths::BRANDS_IMAGES_RELATIVE . $product['BrandImage']['file'])?>">
                        <?php } else { echo $product['Brand']['name']; } ?>
                    </td>
                    <td class="ta-center <?php
                        echo $product['Product']['active'] == ConstantsBooleans::ACTIVE ? 'c-exito' : 'c-fallo' ?>">
                        <?php echo Booleano::toString($product['Product']['active']); ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>