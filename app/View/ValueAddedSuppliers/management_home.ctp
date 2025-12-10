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
            __t('Menu.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('ValueAddedSuppliers.Add'),
                array(
                    'controller' => 'value_added_suppliers',
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

<div class="cnt-data fg-0 p-vertical-1">
    <div class="o-auto">
    <?php echo $this->element('../ValueAddedSuppliers/Elements/search'); ?>
        <table class="table-tracking tabla-responsive">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('ValueAddedSupplier.title', __t('ValueAddedSuppliers.Title')); ?></th>
                    <th><?php echo __t('Garage.Address'); ?></th>
                    <th><?php echo $this->Paginator->sort('ValueAddedSupplier.supplier_url', __t('ValueAddedSuppliers.Url')); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($valueAddedSuppliers as $valueAddedSupplier) { ?>
                    <tr>
                        <td>
                        <?php
                        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                            echo $this->Html->link(
                                $valueAddedSupplier['ValueAddedSupplier']['title'],
                                array(
                                    'controller' => 'value_added_suppliers',
                                    'action' => 'edit',
                                    $valueAddedSupplier['ValueAddedSupplier']['guid']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            );
                        } else {
                            echo $valueAddedSupplier['ValueAddedSupplier']['title'];
                        }
                        ?>
                        </td>
                        <td>
                            <?php if (isset($valueAddedSupplier['ValueAddedSupplier']['logo_image'])) { ?>
                                <img class="trading_image img_table" style="max-height:42px" src="<?php echo FileManager::get_url(FilePaths::VALUE_ADDED_SUPPLIERS_LOGO_IMAGES_RELATIVE . $valueAddedSupplier['ValueAddedSupplier']['logo_image']); ?>"/>
                            <?php } ?>
                        </td>
                        <td>
                            <?php echo h($valueAddedSupplier['ValueAddedSupplier']['supplier_url']); ?>
                        </td>
                        <td>
                            <?php
                            echo $this->Html->Link(
                                '<span class="aag-icon-papelera c-fallo"></span>',
                                array(
                                    'controller' => 'value_added_suppliers',
                                    'action' => 'delete',
                                    $valueAddedSupplier['ValueAddedSupplier']['guid'],
                                ),
                                array(
                                    'class' => 'delete_contact',
                                    'escape' => false,
                                    'title' => __t('User.Delete_user?')
                                )
                            );
                            ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>