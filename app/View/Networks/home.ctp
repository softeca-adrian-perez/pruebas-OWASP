<?php
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps_networks.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('markerclusterer.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Network.Networks'),
                array(
                    'controller' => 'networks',
                    'action' => 'home'
                )
            ),
            __t('Network.List')
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <button class="aag-button medium" type='button' id='button_show_map'>
            <?php echo __t('Network.Show_map'); ?>
        </button>
        <?php
        if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_NETWORK) && (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN)) {
            echo $this->Html->link(
                __t('Network.Add_network'),
                array(
                    'controller' => 'networks',
                    'action' => 'add'
                ),
                array('class' => 'aag-button medium green')
            );
        }
        ?>
    </div>
</div>
<div class="d-none" id="latitude-center">
    <?php echo 54.5; ?>
</div>
<div class="d-none" id="longitude-center">
    <?php echo -4; ?>
</div>

<div class="cnt-data fg-0 p-vertical-1">
    <div class="o-auto">
        <table class="table-tracking tabla-responsive">
            <thead>
                <tr>
                    <td colspan="11" style="padding: 0;"></td>
                </tr>
            </thead>
            <tbody>
                <tr class="title-in-td">
                    <td colspan="11">
                        <div class="aag-title">
                            <?php echo __t('Network.Internal_network'); ?>
                        </div>
                    </td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th class="ta-center" width="165"><?php echo __t('Network.Image'); ?></th>
                    <th><?php echo __t('Network.Name'); ?></th>
                    <th><?php echo __t('Network.Web'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Type'); ?></th>
                    <th><?php echo __t('Network.Trading_groups'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Live'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.On_hold'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Active_total'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Prospect'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Not_converted'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Total'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Enquiries_active'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Quoting_active'); ?></th>
                    <?php if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP)) { ?>
                        <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($networks as $network) {
                    if ($network['Network']['internal']) { ?>
                        <tr>
                            <td class="ta-center">
                                <img title="<?php echo h($network['Network']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']); ?>" class="img_table" style="max-height:42px">
                            </td>
                            <td>
                                <?php if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) || CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                    echo $this->Html->link(
                                        $network['Network']['name'],
                                        array(
                                            'controller' => 'networks',
                                            'action' => 'edit',
                                            $network['Network']['id']
                                        ),
                                        array(
                                            'class' => 'c-primary'
                                        )
                                    );
                                } else {
                                    echo h($network['Network']['name']);
                                } ?>
                            </td>
                            <td class="c-primary">
                                <a target="_blank" title="<?php echo h($network['Network']['web']); ?>" href="<?php echo h($network['Network']['web']); ?>">
                                    <?php echo h($network['Network']['web']); ?>
                                </a>
                            </td>
                            <td class="ta-center">
                                <?php echo h($networks_types[$network['Network']['network_type']]); ?>
                            </td>
                            <td>
                                <?php
                                $trading_groups_list = array();
                                foreach ($network['Network']['trading_groups'] as $trading_group) {
                                    $trading_groups_list[] = $trading_groups[$trading_group['TradingGroupNetwork']['trading_group_id']] . " ";
                                }
                                if (!empty($trading_groups_list)) {
                                    echo implode(" , ", $trading_groups_list);
                                }
                                ?>
                            </td>
                            <td class="ta-center c-exito">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['live'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'status_id' => ConstantsNetworksStatus::LIVE,
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center c-exito">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['on_hold'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'status_id' => ConstantsNetworksStatus::ON_HOLD,
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center c-informacion">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['live'] + $network['Network']['on_hold'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'status_id' => array(
                                                ConstantsNetworksStatus::LIVE,
                                                ConstantsNetworksStatus::ON_HOLD
                                            ),
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center c-informacion">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['prospect'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'status_id' => array(
                                                ConstantsNetworksStatus::PROSPECT,
                                                ConstantsNetworksStatus::AWAITING_DECISION,
                                                ConstantsNetworksStatus::AWAITING_VISIT
                                            ),
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center c-negro">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['not_converted'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'status_id' => ConstantsNetworksStatus::NOT_CONVERTED,
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['total'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'status_id' => array(
                                                ConstantsNetworksStatus::LIVE,
                                                ConstantsNetworksStatus::ON_HOLD,
                                                ConstantsNetworksStatus::PROSPECT,
                                                ConstantsNetworksStatus::NOT_CONVERTED
                                            ),
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center c-informacion">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['enquiries_active'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'enquiries_active' => ConstantsBooleans::YES,
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center c-informacion">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['quoting_active'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'quoting_active' => ConstantsBooleans::YES,
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <?php if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP)) { ?>
                                <td class="ta-center">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                                        <?php
                                        echo $this->Html->Link(
                                            '<span class="aag-icon-papelera c-fallo"></span>',
                                            array(),
                                            array(
                                                'escape' => false,
                                                'title' => __t('General.Delete'),
                                                'class' => 'flex delete-js',
                                                'data-url' => Router::url(array(
                                                    'controller' => 'networks',
                                                    'action' => 'ajax_delete',
                                                    $network['Network']['id']
                                                )),
                                                'data-url_redirect' => Router::url(array(
                                                    'controller' => 'networks',
                                                    'action' => 'home',
                                                )),
                                                'data-confirmmsg' => __t('Network.Delete_network?'),
                                                'data-msg_correct' => __t('Network.Correct_deleted'),
                                                'data-msg_bad' => __t('Constants.Message_bad_deleted'),
                                            )
                                        );
                                        if (CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::BENELUX && $network['Network']['loop'] == ConstantsBooleans::ACTIVE) {
                                            echo $this->Html->link(
                                                $this->Html->image('aag-loop.png', array('style' => 'vertical-align: top', 'title' => __t('Network.Loop'))),
                                                array(
                                                    'controller' => 'networks',
                                                    'action' => 'loop',
                                                    $network['Network']['id']
                                                ),
                                                array(
                                                    'style' => 'width: 17px; height: 17px;',
                                                    'escape' => false,
                                                )
                                            );
                                        }
                                        ?>
                                        <?php
                                        if (in_array($network['Network']['id'], array(NETWORK_ID_AGN, NETWORK_ID_GV, NETWORK_ID_GC))) {
                                            echo $this->Html->link(
                                                '<span class="ion-stats-bars"></span>',
                                                array(
                                                    'controller' => 'reporting',
                                                    'action' => 'home',
                                                    $network['Network']['id'],
                                                    true
                                                ),
                                                array(
                                                    'style' => 'font-size: 22px; color: #0d65ac;',
                                                    'escape' => false,
                                                )
                                            );
                                            $image = $network['Network']['id'] == NETWORK_ID_AGN ? 'agn_icon.png' : 'gv_icon.png';
                                            echo $this->Html->link(
                                                $this->Html->image($image, array('style' => 'vertical-align: top')),
                                                array(
                                                    'controller' => 'networks',
                                                    'action' => 'login_seo_admin_zone',
                                                    $network['Network']['guid']
                                                ),
                                                array(
                                                    'style' => 'width: 17px; height: 17px;',
                                                    'escape' => false,
                                                    'target' => '_blank'
                                                )
                                            );
                                            echo $this->Html->link(
                                                '<span class="aag-icon-configuracion"></span>',
                                                array(
                                                    'controller' => 'networks',
                                                    'action' => 'families_configuration',
                                                    $network['Network']['id']
                                                ),
                                                array(
                                                    'style' => 'font-size: 22px; display: flex;',
                                                    'escape' => false,
                                                )
                                            );
                                        } ?>
                                    </div>
                                </td>
                            <?php } ?>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
            <tbody>
                <tr class="title-in-td">
                    <td colspan="11" style="padding-top: 40px;">
                        <div class="aag-title">
                            <?php echo __t('Network.External_network'); ?>
                        </div>
                    </td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th class="ta-center" width="165"><?php echo __t('Network.Image'); ?></th>
                    <th><?php echo __t('Network.Name'); ?></th>
                    <th><?php echo __t('Network.Web'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Type'); ?></th>
                    <th><?php echo __t('Network.Trading_groups'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Live'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.On_hold'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Active_total'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Prospect'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Not_converted'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Total'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Enquiries_active'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Quoting_active'); ?></th>
                    <?php if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP)) { ?>
                        <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($networks as $network) {
                    if (!$network['Network']['internal']) { ?>
                        <tr>
                            <td class="ta-center">
                                <img title="<?php echo h($network['Network']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']); ?>" class="img_table" style="max-height:42px">
                            </td>
                            <td>
                                <?php if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP) || CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                    echo $this->Html->link(
                                        $network['Network']['name'],
                                        array(
                                            'controller' => 'networks',
                                            'action' => 'edit',
                                            $network['Network']['id']
                                        ),
                                        array(
                                            'class' => 'c-primary'
                                        )
                                    );
                                } else {
                                    echo h($network['Network']['name']);
                                } ?>
                            </td>
                            <td class="c-primary">
                                <a target="_blank" title="<?php echo h($network['Network']['web']); ?>" href="<?php echo h($network['Network']['web']); ?>">
                                    <?php echo h($network['Network']['web']); ?>
                                </a>
                            </td>
                            <td class="ta-center">
                                <?php echo h($networks_types[$network['Network']['network_type']]); ?>
                            </td>
                            <td>
                                <?php
                                $trading_groups_list = array();
                                foreach ($network['Network']['trading_groups'] as $trading_group) {
                                    $trading_groups_list[] = $trading_groups[$trading_group['TradingGroupNetwork']['trading_group_id']] . " ";
                                }
                                if (!empty($trading_groups_list)) {
                                    echo implode(" , ", $trading_groups_list);
                                }
                                ?>
                            </td>
                            <td class="ta-center c-exito">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['live'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'status_id' => ConstantsNetworksStatus::LIVE,
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center c-negro">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['on_hold'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'status_id' => ConstantsNetworksStatus::ON_HOLD,
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center c-informacion">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['live'] + $network['Network']['on_hold'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'status_id' => array(
                                                ConstantsNetworksStatus::LIVE,
                                                ConstantsNetworksStatus::ON_HOLD
                                            ),
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center c-informacion">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['prospect'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'status_id' => ConstantsNetworksStatus::PROSPECT,
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center c-negro">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['not_converted'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'status_id' => ConstantsNetworksStatus::NOT_CONVERTED,
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['total'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'status_id' => array(
                                                ConstantsNetworksStatus::LIVE,
                                                ConstantsNetworksStatus::ON_HOLD,
                                                ConstantsNetworksStatus::PROSPECT,
                                                ConstantsNetworksStatus::NOT_CONVERTED
                                            ),
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center c-informacion">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['enquiries_active'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'enquiries_active' => ConstantsBooleans::YES,
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <td class="ta-center c-informacion">
                                <?php
                                echo $this->Html->link(
                                    $network['Network']['quoting_active'],
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'home',
                                        '?' => array(
                                            'network_id' => $network['Network']['id'],
                                            'quoting_active' => ConstantsBooleans::YES,
                                        ),
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                                ?>
                            </td>
                            <?php if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP)) { ?>
                                <td class="ta-center">
                                    <?php
                                    echo $this->Html->Link(
                                        '<span class="aag-icon-papelera c-fallo"></span>',
                                        array(),
                                        array(
                                            'escape' => false,
                                            'title' => __t('General.Delete'),
                                            'class' => 'flex delete-js',
                                            'data-url' => Router::url(array(
                                                'controller' => 'networks',
                                                'action' => 'ajax_delete',
                                                $network['Network']['id']
                                            )),
                                            'data-url_redirect' => Router::url(array(
                                                'controller' => 'networks',
                                                'action' => 'home',
                                            )),
                                            'data-confirmmsg' => __t('Network.Delete_network?'),
                                            'data-msg_correct' => __t('Network.Correct_deleted'),
                                            'data-msg_bad' => __t('Constants.Message_bad_deleted'),
                                        )
                                    );
                                    ?>
                                </td>
                            <?php } ?>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row_map d-none">
    <div class="cnt-data fg-0">
        <div class="overmap ta-center p-1">
            <?php
            $pos = 0;
            foreach ($networks as $network) {
                echo $this->Form->Button(
                    $network['Network']['name'],
                    array(
                        'type' => 'button',
                        'data-net' => $network['Network']['name'],
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
                            'controller' => 'networks',
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
<style>
    .title-in-td {
        border: none !important;
    }

    .title-in-td td::before {
        content: unset !important;
    }
</style>