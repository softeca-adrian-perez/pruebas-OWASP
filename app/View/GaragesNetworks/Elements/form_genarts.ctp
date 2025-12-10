<?php
echo $this->Html->script('form_genarts.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('toggle.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<?php
echo $this->Form->create(
    'GarageNetwork',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'type' => 'post'
    )
);
    $newPrice = 'New price';
    ?>
    <div class="buttons-fixed-double-tabs">
        <div>
            <?php
            if(isset($edit_genarts))
            {
                echo $this->element('Comun/form_actions', $cancel_action);
            }
            else
            {
                echo $this->Html->link(__t('General.Back'), CakeSession::read('url_referer'), array('class' => 'aag-button medium four'));
                if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                    echo $this->Html->link(
                        __t('General.Edit'),
                        array(
                            'controller' => 'garages_networks',
                            'action' => 'edit_genarts',
                            $garage_network_id
                        ),
                        array(
                            'escape' => false,
                            'title' => __t('General.Edit'),
                            'class' => 'aag-button medium'
                        )
                    );
                }
            }
            ?>
        </div>
    </div>
    <div class="cnt-legend">
        <div>
            <?php
            echo "*" . __t('General.Prices') . " ";
            $txtWithVat = $with_vat ? 'General.With_vat' : 'General.Without_vat';
            echo __t($txtWithVat);
            ?>
        </div>
        <div>
            <span class="icon-legend c-exito"></span> <?php echo __t('GarageNetwork.Applied_price'); ?>
        </div>
    </div>
    <div class="o-auto p-bottom-1">
        <div class="toggle-js cnt-form-toggle-title active" style="cursor: pointer" data-toggle-id-js="1">
            <?php echo __t('Genarts.Active_jobs'); ?>
            <i class="ion-ios-arrow-down arrow-icon-js c-blanco"></i>
        </div>
        <div class="toggle-content-js list-container" data-toggle-id-js="1">
            <?php if(count($works_ids_active) > 0) { ?>
                <table class="table-tracking tl-fixed">
                    <tbody>
                    <?php
                        foreach ($works as $work) {
                            if(in_array($work['Work']['id'], $works_ids_active)){
                                echo $this->element('../GaragesNetworks/Elements/list_works', ['work' => $work]);
                            }
                        }
                    ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="aag-margin"><?php echo __t('Works.No_active_jobs'); ?></div>
            <?php } ?>
        </div>
        <div class="toggle-js cnt-form-toggle-title-secondary" style="cursor: pointer" data-toggle-id-js="2">
            <?php echo __t('Genarts.Inactive_jobs'); ?>
            <i class="ion-ios-arrow-down arrow-icon-js c-blanco"></i>
        </div>
        <div class="toggle-content-js list-container inactive" style="display: none;" data-toggle-id-js="2">
        <?php if(count($works_ids_inactive) > 0) { ?>
                <table class="table-tracking tl-fixed">
                    <tbody>
                    <?php
                        foreach ($works as $work){
                            if(in_array($work['Work']['id'], $works_ids_inactive)){
                                echo $this->element('../GaragesNetworks/Elements/list_works', ['work' => $work]);
                            }
                        }
                    ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="aag-margin"><?php echo __t('Works.No_inactive_jobs'); ?></div>
            <?php } ?>
        </div>
    </div>
<?php echo $this->Form->end(); ?>
<script>
    <?php
    foreach($genarts_ids_repeated as $genart_id_repeated)
    {
        ?>
        $(".genart-js[data-genart-id='<?php echo $genart_id_repeated; ?>']").on('keyup', function(){
            $(".genart-js[data-genart-id='<?php echo $genart_id_repeated; ?>']").val($(this).val());
        });
        <?php
    }
    ?>
</script>