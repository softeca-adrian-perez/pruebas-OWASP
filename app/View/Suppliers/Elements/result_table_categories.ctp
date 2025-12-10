<div class="o-auto">
    <table class="table-tracking">
        <thead>
        <tr>
                <th><?php echo $this->Paginator->sort('SupplierCategory.name_en', __t('General.Name_en')); ?></th>
                <th><?php echo $this->Paginator->sort('SupplierCategory.name_fr', __t('General.Name_fr')); ?></th>
                <th><?php echo $this->Paginator->sort('SupplierCategory.name_de', __t('General.Name_de')); ?></th>
                <th><?php echo $this->Paginator->sort('SupplierCategory.name_nl', __t('General.Name_nl')); ?></th>
                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($suppliers_categories as $suppliers_category) {
                ?>
                <tr>
                    <td>
                        <?php
                            if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                echo $this->Html->link(
                                    $suppliers_category['SupplierCategory']['name_en'],
                                    array(
                                        'controller' => 'suppliers',
                                        'action' => 'edit_category',
                                        $suppliers_category['SupplierCategory']['id']
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                            } else {
                                echo h($suppliers_category['SupplierCategory']['name_en']);
                            }
                        ?>
                    </td>
                    <td>
                        <?php echo h($suppliers_category['SupplierCategory']['name_fr']); ?>
                    </td>
                    <td>
                        <?php echo h($suppliers_category['SupplierCategory']['name_de']); ?>
                    </td>
                    <td>
                        <?php echo h($suppliers_category['SupplierCategory']['name_nl']); ?>
                    </td>
                    <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                        <td class="ta-center">
                        <?php
							echo $this->Html->Link(
								'<span class="aag-icon-papelera c-fallo"></span>',
								array(),
								array(
									'escape' => false,
									'title' => __t('General.Delete'),
									'class' => 'new-delete-js',
									'data-url' => Router::url(array(
										'controller' => 'suppliers',
										'action' => 'ajax_delete_category',
                                        $suppliers_category['SupplierCategory']['id']
									)),
									'data-url_redirect' => Router::url(array(
										'controller' => 'suppliers',
										'action' => 'maintenance_suppliers_categories',
									)),
									'data-confirmmsg' => __t('Suppliers.Confirm_delete_category'),
								)
							); ?>
                        </td>
                    <?php } ?>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>
<?php echo $this->element('Comun/paginacion'); ?>
