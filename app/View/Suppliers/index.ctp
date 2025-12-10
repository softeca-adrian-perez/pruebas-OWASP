<?php
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('suppliers.js?v=' . Configure::read('VERSION_CACHE'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('General.Home'),
                array(
                    'controller' => 'home',
                    'action' => 'home'
                )
            ),
            __t('Suppliers.Suppliers'),
        ));
        ?>
    </div>
</div>
<?php
echo $this->element('../Home/Elements/home_bullets');
?>

<div class="cnt-data fg-0">
    <div class="header-articles">
        <?php echo $this->element('../Home/Elements/home_header'); ?>
    </div>
</div>
<?php
echo $this->Form->hidden(
    '',
    array(
        'id' => 'products_url',
        'data-url' => Router::url(array(
            'controller' => 'brands',
            'action' => 'products',
        )),
    )
);
?>
<div class="cnt-data fg-0 p-1">
    <div class="columns aag-title">
        <?php echo __t('Suppliers.Suppliers'); ?>
        <span class="cnt-preview-name d-none"><?php echo __t('Suppliers.Current_view_as'); ?><span class="preview-name"></span></span>
        <?php if ($value_permission) { ?>
            <div class="f-right cnt-eyes-icons">
                <div class="preview_icon" data-tooltip-content="#tooltip_content" data-url="<?php echo Router::url(array('controller' => 'suppliers', 'action' => 'ajax_preview_suppliers')); ?>">
                    <span class="icono-grande aag-icon-ojo p-top-1"></span>
                </div>
                <div class="remove_preview_icon d-none" title="<?php echo __t('Suppliers.Remove_preview'); ?>" style="margin-right: 0px;">
                    <span class="icono-grande ion-eye-disabled"></span>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php if ($value_permission) { ?>
    <div class="d-none">
        <div id="tooltip_content">
            <div class="row">
                <div class="columns medium-12 ta-center">
                    <h1 class="p-0"><?php echo __t('Suppliers.Preview_as'); ?></h1>
                </div>
                <div class="columns medium-6 ta-center">
                    <h1><?php echo __t('Network.Networks'); ?></h1>
                    <div class="networks">
                        <?php foreach ($networks as $key => $network) {
                            echo $this->Html->image(
                                FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']),
                                array(
                                    'alt' => $network['Network']['name'],
                                    'title' => $network['Network']['name'],
                                    'data-network-id' => $network['Network']['id'],
                                    'class' => 'img_preview'
                                )
                            );
                        } ?>
                    </div>
                </div>
                <div class="columns medium-6 ta-center">
                    <h1><?php echo __t('TradingGroup.Trading_groups'); ?></h1>
                    <div class="trading_groups">
                        <?php foreach ($trading_groups as $key => $trading_group) {
                            echo $this->Html->image(
                                FileManager::get_url(FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $trading_group['TradingGroup']['image']),
                                array(
                                    'alt' => $trading_group['TradingGroup']['name'],
                                    'title' => $trading_group['TradingGroup']['name'],
                                    'data-trading-group-id' => $trading_group['TradingGroup']['id'],
                                    'class' => 'img_preview'
                                )
                            );
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<div class="tile cnt-page-suppliers cnt-data fg-0">
    <?php echo $this->element('../Suppliers/Elements/suppliers'); ?>
</div>
<div style="display: none;" id="productsInfo" class="reveal-modal background-color-primary modal-suppliers"
    data-reveal
    aria-labelledby="modalTitle"
    aria-hidden="true"
    role="dialog"
    data-options="close_on_background_click:true">
    <h4 id="modalTitle"></h4>
    <div id="products" class="products"></div>
    <a class="close-modal" data-close aria-label="Close" id="close_modal">&#215;</a>
</div>