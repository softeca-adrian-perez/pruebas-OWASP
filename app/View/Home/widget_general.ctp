<?php
echo $this->Html->css('/panel/css/default.css');
echo $this->Html->script('/panel/js/jquery.gridster.js');
echo $this->Html->script('/panel/js/jquery.jrumble.1.3.min.js');
echo $this->Html->script('/panel/js/jquery.resize.js');
echo $this->Html->script('/panel/js/widgets.js');
echo $this->Html->script('/panel/js/highcharts/highcharts.js');
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->css('../js/lib/select2-4.0.0/dist/css/select2.min.css', array('block' => 'script'));
echo $this->Html->script('lib/select2-4.0.0/dist/js/select2.full.min.js', array('block' => 'script'));
echo $this->Html->script('select2.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('/js/gd_export_excel.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gmaps_networks.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('markerclusterer.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/purify.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$this->assign('body_class', 'widgets');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('General.Home'),
                array(
                    'plugin' => false,
                    'controller' => 'home',
                    'action' => 'home_dashboard'
                )
            ),
            ('Quality data')
        ));
        ?>
    </div>
</div>
<?php
echo $this->element('../Home/Elements/home_bullets');
echo $this->element('../Home/Elements/home_header');
?>
<section class="general-container">
    <div class="cnt-data fg-0">
        <?php
        echo $this->Form->create(
        'Search',
        array(
            'class' => 'cnt-form-search',
            'type' => 'get',
            'url' => array(
                'plugin' => false,
                'controller' => 'home',
                'action' => 'widget_general',
            ),
        )
        );
            ?>
            <div class="cnt-form-search-title">
                <?php echo 'Networks' ?>
            </div>

            <div class="cnt-form-inputs">
                <?php
                echo $this->Form->input(
                    'aag_region_id',
                    array(
                        'label' => 'Region',
                        'type' => 'select',
                        'class' => 'select2-multiple clear_field',
                        'multiple' => 'multiple',
                        'options' => $regions,
                        'empty' => false,
                        'id' => 'region-id',
                        'value' => $current_region
                    )
                );
                echo $this->Form->input(
                    'country_id',
                    array(
                        'label' => __t('Garage.Country'),
                        'type' => 'select',
                        'class' => 'select2-multiple clear_field',
                        'multiple' => 'multiple',
                        'options' => $countries,
                        'empty' => false,
                        'id' => 'country-id',
                        'value' => $current_country
                    )
                );
                echo $this->Form->input(
                    'network_id',
                    array(
                        'label' => __t('Garage.Network'),
                        'type' => 'select',
                        'class' => 'select2-multiple clear_field',
                        'options' => $network_list,
                        'multiple' => 'multiple',
                        'empty' => false,
                        'id' => 'network-id',
                        'value' => $current_network['Network']['id']
                    )
                );
                ?>
            </div>
            <div class="cnt-form-search-buttons">
                <?php
                echo $this->Form->button(
                    __t('General.Search'),
                    array(
                        'type' => 'submit',
                        'class' => 'aag-button medium'
                    )
                );
                ?>
            </div>
        <?php echo $this->Form->end(); ?>
        <div class="wrapper-map cnt-data-element-small">
            <div class="contenedor-mapa"
                id="map"
                style="height: 45rem; border-radius: 20px; position: relative; overflow: hidden;"
                data-url="
                <?php echo Router::url(
                    array(
                        'plugin' => false,
                        'controller' => 'networks',
                        'action' => 'getDataDashboardMap',
                    )
                ) ?>" data-img="<?php echo FilePaths::PINS_IMAGES_REALTIVE ?>">
            </div>
        </div>
    </div>

    <div class="cnt-data fg-0 p-top-1">
        <div class="cnt-data-element aag-title">
            <?php echo('Quality Data'); ?>
        </div>
        <div class="o-auto ampliacion">
            <table class="table-tracking">
                <thead>
                <tr>
                    <th><?php echo __t('Garage.Network'); ?></th>
                    <th class="text-center"><?php echo __t('Audit.Total') . ' ' . __t('Garage.Garages'); ?></th>
                    <th class="text-center"><?php echo __t('Garage.Address'); ?></th>
                    <th class="text-center"><?php echo __t('Garage.City'); ?></th>
                    <th class="text-center"><?php echo __t('Garage.Postal_code'); ?></th>
                    <th class="text-center"><?php echo __t('Garage.Phone'); ?></th>
                    <th class="text-center"><?php echo __t('Garage.Email'); ?></th>
                    <th class="text-center"><?php echo __t('Garage.Opening_hours'); ?></th>
                    <th class="text-center"><?php echo __t('Garage.Services'); ?></th>
                    <th class="text-center"><?php echo __t('Garage.Vehicle_types'); ?></th>
                </tr>
                </thead>
                <tbody>
                    <?php
                        foreach($networks_total_garages_by_network as $network){?>
                        <tr>
                            <td>
                                <?php
                                echo $this->Html->image(
                                    FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image'],
                                    array(
                                        'title' => $network['Network']['name'],
                                        'style' => 'height: 30px; max-width: 100px'
                                    )
                                ); ?>
                            </td>
                            <td class="text-center">
                                <?php
                                echo h($network[0]['NumberGarages']) ?>
                            </td>
                            <td class="text-center">
                                <?php echo round($network['garages_statistics'][0]['address1'] / $network[0]['NumberGarages'], 4) * 100 . ' %' ?>
                            </td>
                            <td class="text-center">
                                <?php echo round($network['garages_statistics'][0]['city_id'] / $network[0]['NumberGarages'], 4) * 100 . ' %' ?>
                            </td>
                            <td class="text-center">
                                <?php echo round($network['garages_statistics'][0]['postcode'] / $network[0]['NumberGarages'], 4) * 100 . ' %' ?>
                            </td>
                            <td class="text-center">
                                <?php echo round($network['garages_statistics'][0]['phone'] / $network[0]['NumberGarages'], 4) * 100 . ' %' ?>
                            </td>
                            <td class="text-center">
                                <?php echo round($network['garages_statistics'][0]['email'] / $network[0]['NumberGarages'], 4) * 100 . ' %' ?>
                            </td>
                            <td class="text-center">
                                <?php echo round($network['garages_statistics'][0]['opening_hours'] / $network[0]['NumberGarages'], 4) * 100 . ' %' ?>
                            </td>
                            <td class="text-center">
                                <?php echo round($network['garages_number_services'] / $network[0]['NumberGarages'], 4) * 100 . ' %' ?>
                            </td>
                            <td class="text-center">
                                <?php echo round($network['garages_number_vehicles'] / $network[0]['NumberGarages'], 4) * 100 . ' %' ?>
                            </td>
                        </tr>

                    <?php
                    }?>
                </tbody>
            </table>
            <br/>
        </div>
    </div>
    <div class="aag-margin">
        <div class="container-widgets">
            <ul>
                <?php
                foreach($networks_availables as $network){
                    foreach($widgets as $widget){
                        if($widget['PanelWidget']['name'] == 'Widget.Network_evolution'){ ?>
                            <li>
                                <div class="aag-radius aag-background-container-color">
                                    <div class="aag-title">
                                        <?php echo __t($widget['PanelWidget']['display_name']) . " - " . h($network['Network']['name']); ?>
                                    </div>
                                    <?php
                                    echo $this->requestAction(
                                        array(
                                            'plugin' => 'panel',
                                            'controller' => 'widgets',
                                            'action' => 'get_data_widget_by_network',
                                            $widget['PanelWidget']['logic_model'],
                                            $widget['PanelWidget']['url_view'],
                                            $widget['PanelWidgetUser']['size_x'],
                                            $network['Network']['id']
                                        ),
                                        array('return')
                                    );
                                    ?>
                                </div>
                            </li>
                    <?php }
                    }
                } ?>
            </ul>
        </div>
    </div>
</section>