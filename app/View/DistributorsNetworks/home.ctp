<?php
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps_distributors_networks.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('markerclusterer.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Menu.Distributors') . ' ' . __t('Menu.Networks'),
                array(
                    'controller' => 'distributors_networks',
                    'action' => 'home'
                )
            ),
            __t('Network.List'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <button class="aag-button medium" type='button' id='button_show_map'>
            <?php echo __t('Network.Show_map'); ?>
        </button>
        <div class="row_map overmap ta-center d-none">
            <?php
            $pos = 0;
            foreach ($networks as $network) {
                echo ($this->Form->Button(
                    $network['DistributorNetwork']['name'],
                    array(
                        'type' => 'button',
                        'data-net' => $network['DistributorNetwork']['name'],
                        'class' => 'aag-button medium outlined one'

                    )
                ));
                $pos++;
            }
            echo ($this->Form->Button(
                __t('Network.Without_network'),
                array(
                    'type' => 'button',
                    'data-net' => 'nonet',
                    'class' => 'aag-button medium outlined one'
                )
            ));
            ?>
        </div>
        <?php
        if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_DISTRIBUTOR_NETWORK)) {
            echo $this->Html->link(
                __t('Network.Add_network'),
                array(
                    'controller' => 'distributors_networks',
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
<div class="d-none" id="latitude-center">
    <?php
        echo 54.5;
    ?>
</div>
<div class="d-none" id="longitude-center">
    <?php
        echo -4;
    ?>
</div>
<div class="cnt-data fg-0 p-vertical-1">
    <div class="aag-title cnt-data-element">
        <?php echo __t('Network.Networks') ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking tabla-responsive">
            <thead>
                <tr>
                    <th class="ta-center" width="165"><?php echo __t('Network.Image'); ?></th>
                    <th><?php echo $this->Paginator->sort('DistributorNetwork.name', __t('Network.Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('DistributorNetwork.web', __t('Network.Web')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('DistributorNetwork.network_type', __t('Network.Type')); ?></th>
                    <th><?php echo __t('Network.Trading_groups'); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('DistributorNetwork.live', __t('Network.Live')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('DistributorNetwork.prospect', __t('Network.Prospect')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('DistributorNetwork.not_converted', __t('Network.Not_converted')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('DistributorNetwork.total', __t('General.Total')); ?></th>
                    <?php if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP)) { ?>
                        <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($networks as $network) { ?>
                    <tr>
                        <td class="ta-center">
                            <img title="<?php echo h($network['DistributorNetwork']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['DistributorNetwork']['image']); ?>" class="img_table">
                        </td>
                        <td>
                            <?php
                            echo $this->Html->link(
                                $network['DistributorNetwork']['name'],
                                array(
                                    'controller' => 'distributors_networks',
                                    'action' => 'view',
                                    $network['DistributorNetwork']['id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            );
                            ?>
                        </td>
                        <td class="c-primary">
                            <a target="_blank" title="<?php echo h($network['DistributorNetwork']['web']); ?>" href="<?php echo h($network['DistributorNetwork']['web']); ?>">
                                <?php echo h($network['DistributorNetwork']['web']); ?>
                            </a>
                        </td>
                        <td class="ta-center">
                            <?php echo h($networks_types[$network['DistributorNetwork']['network_type']]); ?>
                        </td>
                        <td>
                            <?php
                            $trading_groups_list = array();
                            foreach ($network['DistributorNetwork']['trading_groups'] as $trading_group) {
                                $trading_groups_list[] = $trading_groups[$trading_group['TradingGroupDistributorNetwork']['trading_group_id']] . " ";
                            }
                            if (!empty($trading_groups_list)) {
                                echo implode(" , ", $trading_groups_list);
                            }
                            ?>
                        </td>
                        <td class="ta-center c-exito">
                            <?php echo h($network['DistributorNetwork']['live']); ?>
                        </td>
                        <td class="ta-center c-informacion">
                            <?php echo h($network['DistributorNetwork']['prospect']); ?>
                        </td>
                        <td class="ta-center c-negro">
                            <?php echo h($network['DistributorNetwork']['not_converted']); ?>
                        </td>
                        <td class="ta-center">
                            <?php echo h($network['DistributorNetwork']['total']); ?>
                        </td>
                        <?php if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP)) { ?>
                            <td class="ta-center">
                                <?php echo $this->Html->Link(
                                    '<span class="aag-icon-papelera c-fallo"></span>',
                                    array(),
                                    array(
                                        'escape' => false,
                                        'title' => __t('General.Delete'),
                                        'class' => 'delete-js',
                                        'data-url' => Router::url(array(
                                            'controller' => 'distributors_networks',
                                            'action' => 'ajax_delete',
                                            $network['DistributorNetwork']['id']
                                        )),
                                        'data-url_redirect' => Router::url(array(
                                            'controller' => 'distributors_networks',
                                            'action' => 'home',
                                        )),
                                        'data-confirmmsg' => __t('Network.Delete_network?'),
                                        'data-msg_correct' => __t('Network.Correct_deleted'),
                                        'data-msg_bad' => __t('Constants.Message_bad_deleted'),
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
</div>
<div class="row_map d-none">
    <div class="cnt-data fg-0">
        <div class="overmap ta-center p-1">
            <?php
            $pos = 0;
            foreach ($networks as $network) {
                echo $this->Form->Button(
                    $network['DistributorNetwork']['name'],
                    array(
                        'type' => 'button',
                        'data-net' => $network['DistributorNetwork']['name'],
                        'class' => 'aag-button medium outlined one'

                    )
                );
                $pos++;
            }
            echo $this->Form->Button(
                __t('Network.Without_network'),
                array(
                    'type' => 'button',
                    'data-net' => 'nonet',
                    'class' => 'aag-button medium outlined one'
                )
            );
            ?>
        </div>
        <div class="cnt-data-element">
            <div class="wrapper-map">
                <div class="contenedor-mapa" id="map" style="height: 45rem;" data-url="
                    <?php echo Router::url(
                        array(
                            'controller' => 'distributors_networks',
                            'action' => 'getDataMap',
                        )
                    ) ?>" data-img="<?php echo FilePaths::PINS_IMAGES_REALTIVE ?>">
                </div>
            </div>
            <div class="alliance_bar m-bottom-1"></div>
            <br />
        </div>
    </div>
    <div>
        <span class="d-none" id="total_networks"><?php echo count($networks) ?></span>
    </div>
</div>