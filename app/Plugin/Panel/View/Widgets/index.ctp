<?php
echo $this->Html->css('/panel/css/default.css');
echo $this->Html->script('/panel/js/jquery.gridster.js');
echo $this->Html->script('/panel/js/jquery.jrumble.1.3.min.js');
echo $this->Html->script('/panel/js/jquery.resize.js');
echo $this->Html->script('/panel/js/widgets.js');
echo $this->Html->script('/panel/js/highcharts/highcharts.js');
echo $this->Html->css('../js/lib/select2-4.0.0/dist/css/select2.min.css', array('block' => 'script'));
echo $this->Html->script('lib/select2-4.0.0/dist/js/select2.full.min.js', array('block' => 'script'));
echo $this->Html->script('select2.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
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
            ('Dashboard')
        ));
        ?>
    </div>
</div>
<?php
echo $this->element('../Home/Elements/home_bullets');
echo $this->element('../Home/Elements/home_header');
?>
<section class="general-container cnt-widgets-panel">
    <div class="cnt-data">
        <?php
        echo $this->Form->create(
            'Search',
            array(
                'class' => 'cnt-form-search',
                'type' => 'get',
                'url' => array(
                    'plugin' => 'panel',
                    'controller' => 'widgets',
                    'action' => 'index'
                )
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
                    'multiple' => false,
                    'options' => $regions_list,
                    'empty' => true,
                    'id' => 'region-id',
                    'value' => $current_region_id,
                    'disabled' => true,
                )
            );
            echo $this->Form->input(
                'country_id',
                array(
                    'label' => __t('Garage.Country'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'multiple' => true,
                    'options' => $countries,
                    'empty' => true,
                    'id' => 'country-id',
                    'default' => $current_country
                )
            );
            echo $this->Form->input(
                'network_id',
                array(
                    'label' => __t('Garage.Network'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'options' => $network_list,
                    'multiple' => true,
                    'empty' => true,
                    'id' => 'network-id',
                    'default' => $current_network['Network']['id']
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
                    'class' => 'aag-button medium',
                    'id' => 'submit-id'
                )
            );
            ?>
        </div>
        <?php echo $this->Form->end(); ?>
        <div class="cnt-map-legend">
            <?php
            foreach ($networks_availables as $network) {
                if (isset($network['Network']['image'])) {
                    echo "<div class='m-1'>";
                    echo $this->Html->image(
                        FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image'],
                        array(
                            'alt' => $network['Network']['name'],
                            'title' => $network['Network']['name'],
                        )
                    );
                    echo "</div>";
                }
            }
            ?>
        </div>
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

        <div class="ta-center p-widget p-top-1 p-bottom-1">
            <?php echo $this->element('../Garages/Elements/table_report_regions'); ?>
        </div>
    </div>
    <div class="aag-margin">
        <div class="container-widgets">
            <ul>
                <?php
                foreach ($widgets as $widget) {
                    if ($widget['PanelWidget']['name'] != 'Widget.Network_evolution') {
                ?>
                        <li>
                            <div class="aag-radius aag-background-container-color">
                                <div class="aag-title">
                                    <?php echo __t($widget['PanelWidget']['display_name']); ?>
                                </div>
                                <?php
                                echo $this->requestAction(
                                    array(
                                        'plugin' => 'panel',
                                        'controller' => 'widgets',
                                        'action' => 'get_data_widget',
                                        $widget['PanelWidget']['logic_model'],
                                        $widget['PanelWidget']['url_view'],
                                        $widget['PanelWidgetUser']['size_x']
                                    ),
                                    array('return')
                                );
                                ?>
                            </div>
                        </li>
                <?php
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</section>
</section>