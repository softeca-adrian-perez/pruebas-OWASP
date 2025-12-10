<?php
$action = $this->request->action;
echo $this->Form->create('SupplierCategory', array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data',));
echo $this->Form->hidden('SupplierCategory.id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if($action == 'add_category')
        {
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
                __t('General.Add'),
            ));
        }
        else
        {
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
                __t('General.Edit'),
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
        <?php echo __t('Suppliers.Categories'); ?>
    </div>
    <div class="cnt-form-inputs m-top-1">
        <?php
        echo $this->Form->input(
            'name_en',
            array(
                'required' => true,
                'type' => 'text',
                'label' => __t('General.Name_en'),
            )
        );
        echo $this->Form->input(
            'name_fr',
            array(
                'required' => true,
                'type' => 'text',
                'label' => __t('General.Name_fr'),
            )
        );
        echo $this->Form->input(
            'name_de',
            array(
                'required' => true,
                'type' => 'text',
                'label' => __t('General.Name_de'),
            )
        );
        echo $this->Form->input(
            'name_nl',
            array(
                'required' => true,
                'type' => 'text',
                'label' => __t('General.Name_nl'),
            )
        );
        echo $this->Form->input(
            'aag_region_id',
            array(
                'id' => 'aag-region-select',
                'label' => __t('Garage.Region'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'options' => $aag_regions,
                'required' => true,
                'disabled' => true,
                'selected' => $aag_region
            )
        );
        echo $this->Form->input(
            'aag_region_id',
            array(
                'required' => true,
                'type' => 'hidden',
                'value' => $aag_region,
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
