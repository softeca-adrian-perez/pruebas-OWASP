<?php echo $this->Html->script('brands.js?v='.Configure::read('VERSION_CACHE')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Brands.Brands'),
                array(
                    'controller' => 'brands',
                    'action' => 'maintenance_brands'
                )
            ),
            __t('Brands.List'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
            if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                echo $this->Html->link(
                    __t('Brands.New_brand'),
                    array(
                        'controller' => 'brands',
                        'action' => 'add',
                    ),
                    array('class' => 'aag-button medium green')
                );
            }
        ?>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Brands/Elements/search'); ?>
    <div class="o-auto brands">
        <table class="table-tracking">
            <thead>
            <tr>
                <th class="ta-center"><?php echo __t('Brands.Image'); ?></th>
                <th class="ta-center"><?php echo __t('Suppliers.Supplier'); ?></th>
                <th><?php echo $this->Paginator->sort('Brand.name', __t('Brands.Name')); ?></th>
                <th><?php echo __t('Brands.Products'); ?></th>
                <th class="ta-center"><?php echo $this->Paginator->sort('Brand.active', __t('Brands.Active')); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($brands as $brand) { ?>
                <tr>
                    <td class="ta-center">
                        <img src="<?php echo (!empty($brand['BrandImage']['file'])) ? FileManager::get_url(FilePaths::BRANDS_IMAGES_RELATIVE.$brand['BrandImage']['file']) : "" ?>">
                    </td>
                    <td class="ta-center">
                        <?php if( isset($brand['SupplierImage']['file']) && $brand['SupplierImage']['file'] ) { ?>
                            <img src="<?php echo (!empty($brand['SupplierImage']['file'])) ? FileManager::get_url(FilePaths::SUPPLIERS_IMAGES_RELATIVE.$brand['SupplierImage']['file']) : "" ?>">
                        <?php } ?>
                    </td>
                    <td>
                        <?php
                            if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                echo $this->Html->link(
                                    $brand['Brand']['name'],
                                    array(
                                        'controller' => 'brands',
                                        'action' => 'edit',
                                        $brand['Brand']['id']
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                            } else {
                                echo h($brand['Brand']['name']);
                            } ?>
                    </td>
                    <td>
                        <?php echo h($brand['Products']); ?>
                    </td>
                    <td class="ta-center <?php
                        echo $brand['Brand']['active'] == ConstantsBooleans::ACTIVE ? 'c-exito' : 'c-fallo' ?>">
                        <?php echo Booleano::toString($brand['Brand']['active']); ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>