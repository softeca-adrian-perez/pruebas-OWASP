<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('GarageNetwork.Garage_network'),
                array(
                    'controller' => 'garages_networks',
                    'action' => 'view',
                    $garage_network_id
                )
            ),
            __t('Garage.Images'),
        ));
        ?>
    </div>
</div>
<?php echo $this->element('../GaragesNetworks/tabs_network', array('selected' => 'button_my_garage')); ?>
<div class="cnt-data buttons-fixed">
    <div class="aag-padding">
        <?php echo $this->element('../GaragesNetworks/tabs_my_garage', array('selected' => 'images')); ?>
    </div>
    <?php echo $this->element('../GaragesNetworks/Elements/form_images'); ?>
</div>
