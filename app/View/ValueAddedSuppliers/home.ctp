<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('ValueAddedSuppliers.ValueAddedSuppliers'),
                array(
                    'controller' => 'suppliers',
                    'action' => 'maintenance_suppliers'
                )
            ),
            __t('Suppliers.List'),
        ));
        ?>
    </div>
</div>

<div class="cnt-data fg-0 p-vertical-1">
    <div class="aag-title cnt-data-element">
        <?php echo __t('ValueAddedSuppliers.ValueAddedSuppliers') ?>
    </div>
    <div class="supplier-card-grid">
        <?php foreach ($valueAddedSuppliers as $valueAddedSupplier) { ?>
            <div class="supplier-card-border">
            <?php echo $this->element('../ValueAddedSuppliers/Elements/supplier_card', array('valueAddedSupplier' => $valueAddedSupplier)); ?>
            </div>
        <?php } ?>
    </div>
</div>