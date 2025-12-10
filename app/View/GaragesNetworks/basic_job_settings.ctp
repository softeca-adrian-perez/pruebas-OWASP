<?php
echo $this->Html->script('form_genarts.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
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
            __t('GarageNetwork.Works'),
        ));
        ?>
    </div>
</div>
<?php echo $this->element('../GaragesNetworks/tabs_network', array('selected' => 'button_pricing_setup')); ?>
<div class="cnt-data buttons-fixed">
    <div class="aag-padding">
        <?php echo $this->element('../GaragesNetworks/tabs_pricing_setup', array('selected' => 'basic_job_settings')); ?>
    </div>
    <?php echo $this->element('../GaragesNetworks/Elements/form_basic_job_settings'); ?>
</div>
