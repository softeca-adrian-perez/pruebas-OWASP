<?php
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('suppliers.js?v=' . Configure::read('VERSION_CACHE'));
?>
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
            __t('Suppliers.List'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
            <?php
            echo $this->Html->link(
                __t('Suppliers.New_supplier'),
                array(
                    'controller' => 'suppliers',
                    'action' => 'add',
                ),
                array('class' => 'aag-button medium green')
            );
            ?> <?php } ?>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Suppliers/Elements/search'); ?>
    <div class="o-auto suppliers">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th class="ta-center"><?php echo __t('Suppliers.Image'); ?></th>
                    <th><?php echo $this->Paginator->sort('Supplier.name', __t('Suppliers.Name')); ?></th>
                    <th><?php echo __t('Suppliers.Web'); ?></th>
                    <th><?php echo __t('Brands.Brands'); ?></th>
                    <th><?php echo $this->Paginator->sort(
                            'Supplier.publication_date',
                            __t('Suppliers.Publication_date')
                        ); ?></th>
                    <th class="ta-center"><?php echo __t('Suppliers.Active'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $supplier) { ?>
                    <tr>
                        <td class="ta-center">
                            <?php if ($supplier['SupplierImage']['file']) { ?>
                                <img src="<?php echo FileManager::get_url(FilePaths::SUPPLIERS_IMAGES_RELATIVE . $supplier['SupplierImage']['file']); ?>">
                            <?php } ?>
                        </td>
                        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                            <td>
                                <?php echo $this->Html->link(
                                    $supplier['Supplier']['name'],
                                    array(
                                        'controller' => 'suppliers',
                                        'action' => 'edit',
                                        $supplier['Supplier']['id']
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                ); ?>
                            </td>
                        <?php } else { ?>
                            <td>
                                <?php echo h($supplier['Supplier']['name']); ?>
                            </td>
                        <?php } ?>
                        <td>
                            <?php echo h($supplier['Supplier']['web']); ?>
                        </td>
                        <td>
                            <?php echo h($supplier['Brands']); ?>
                        </td>
                        <td class="ta-center">
                            <?php echo Fecha::toFormatoVistaFecha($supplier['Supplier']['publication_date']); ?>
                        </td>
                        <td class="ta-center <?php
                                                echo $supplier['Supplier']['active'] == ConstantsBooleans::ACTIVE ? 'c-exito' : 'c-fallo' ?>">
                            <?php echo Booleano::toString($supplier['Supplier']['active']); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>