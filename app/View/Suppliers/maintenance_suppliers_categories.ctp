<?php echo $this->Html->script('suppliers_maintenance.js?v=' . Configure::read('VERSION_CACHE')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Maintenance.Maintenance'),
                array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Suppliers.Supplier') .' '.__t('Suppliers.Categories'),
                array(
                    'controller' => 'suppliers',
                    'action' => 'maintenance_suppliers_categories'
                )
            ),
            __t('Communication.List'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
            if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                echo $this->Html->link(
                    __t('Suppliers.New_supplier_category'),
                    array(
                        'controller' => 'suppliers',
                        'action' => 'add_category',
                    ),
                    array('class' => 'aag-button medium green')
                );
            }
        ?>
    </div>
</div>
<div class="cnt-data p-vertical-1">
    <div class="aag-title cnt-data-element">
        <?php echo __t('Suppliers.Categories'); ?>
    </div>
    <div id="communication_sections-js" class="flex fg-1 fd-column">
        <?php echo $this->element('../Suppliers/Elements/result_table_categories'); ?>
    </div>
</div>