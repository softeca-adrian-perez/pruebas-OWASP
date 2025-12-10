<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Network.Networks'),
                array(
                    'controller' => 'distributors_networks',
                    'action' => 'home'
                )
            ),
            __t('General.View'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR_NETWORK) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS_NETWORKS)
        ) {
            echo $this->Html->link(
                __t('General.Edit'),
                array(
                    'controller' => 'distributors_networks',
                    'action' => 'edit',
                    $distributor_network['DistributorNetwork']['id'],
                ),
                array(
                    'escape' => false,
                    'title' => __t('General.Edit'),
                    'class' => 'aag-button medium',
                )
            );
        } ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        echo __t('Network.View_network');
        ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <div>
            <strong><?php echo __t('Network.Name') ?>:</strong>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo h($distributor_network['DistributorNetwork']['name']); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Network.Web') ?>:</strong>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo h($distributor_network['DistributorNetwork']['web']); ?>
            </div>
        </div>
    </div>
    <div class="d-inline-block ta-center end p-right-1 item-logo m-top-1">
        <div class="p-1">
            <img class="trading_image img_table" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $distributor_network['DistributorNetwork']['image']); ?>" />
        </div>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <div>
            <strong><?php echo __t('Network.Network_type') ?>:</strong>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo $networks_types[$distributor_network['DistributorNetwork']['network_type']]; ?>
            </div>
        </div>
    </div>
    <div class="aag-title m-top-1">
        <?php echo __t('Network.Related_trading_group'); ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <?php
        if ($trading_groups != null) {
            foreach ($trading_groups as $trading_group) {
        ?>
                <?php
                $value = false;
                if (isset($distributor_network) && !empty($distributor_network)) {
                    $trading_group_tmp = explode(",", $distributor_network[0]['TradingGroupDistributorNetwork']);
                    if (in_array($trading_group['TradingGroup']['id'], $trading_group_tmp)) {
                        $value = true;
                    }
                }
                if ($value) { ?>
                    <div>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <?php echo h($trading_group['TradingGroup']['name']); ?>
                        </div>
                    </div>

                <?php } ?>
        <?php
            }
        }
        ?>
    </div>
    <div class="background-color-primary d-inline-block w-100p p-vertical-1">
        <div class="row p-top-1">
            <div class="aag-title" style="width: max-content; gap: 5px; align-items: flex-start;">
                <?php echo __t('Network.Preset') ?>
            </div>
            <div class="pins">
                <?php
                foreach ($presets as $key => $preset) {
                    if ($selected_preset == $key) { ?>
                        <div class="columns medium-2 large-1 small-6 ta-center end">
                            <div class="clear ta-center" style="padding-top: 10px; padding-bottom: 5px;">
                                <img src="<?php echo FilePaths::PINS_IMAGES_REALTIVE . $preset[0]; ?>">
                                <img src="<?php echo FilePaths::PINS_IMAGES_REALTIVE . $preset[1]; ?>">
                            </div>
                        </div>
                <?php }
                }
                ?>
            </div>
        </div>
        <div class="row p-top-1">
            <?php
            if (isset($selected_preset) && $selected_preset == false) { ?>
                <div class="aag-title">
                    <?php echo __t('Network.Custom') ?>
                </div>
                <div class="columns medium-12 aag-subtitle">
                    <?php echo __t('Network.Cluster') ?>
                </div>
                <div class="d-inline-block ta-center end p-right-1 item-logo m-top-1">
                    <div class="p-1">
                        <img class="trading_image img_table" src="<?php echo FileManager::get_url(FilePaths::PINS_IMAGES_REALTIVE . $distributor_network['DistributorNetwork']['image_cluster']); ?>" />
                    </div>
                </div>
                <div class="columns medium-12 aag-subtitle">
                    <?php echo __t('Network.Pins') ?>
                </div>
                <div class="d-inline-block ta-center end p-right-1 item-logo m-top-1">
                    <div class="p-1">
                        <img class="trading_image img_table" src="<?php echo FileManager::get_url(FilePaths::PINS_IMAGES_REALTIVE . $distributor_network['DistributorNetwork']['image_pin']); ?>" />
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php echo $this->Form->end(); ?>