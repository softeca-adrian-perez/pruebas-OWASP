<?php
$controller = $this->request->controller;

echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps_distributors.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('markerclusterer.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('branches.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gd_export_excel.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Distributor.Distributors'),
                array(
                    'controller' => 'distributors',
                    'action' => 'home'
                )
            ),
            __t('Distributor.My_branches'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Form->button(
            __t('General.Export_branches'),
            array(
                'class' => 'aag-button medium gd-export-excel-buscador-js',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'distributors',
                        'action' => 'ajax_distributor_excel_branches',
                        $controller
                    )
                ),
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <?php echo $this->element('../Distributors/Elements/search_branches'); ?>
    <div class="p-form end m-tm-1 p-right-0">
        <div class="f-right cnt-legend">
            <div class="title-legend"><?php echo __t('Appointment.Period_of_time'); ?></div>
            <div class="d-inline-block m-right-1">
                <span class="icon-legend c-fallo"></span>
                <label for="check-cancelled" class="unselectable m-0-i"><?php echo __t('Visit.Plus_6_months') ?></label>
            </div>
            <div class="d-inline-block m-right-1">
                <span class="icon-legend c-informacion"></span>
                <label for="check-rescheduled" class="unselectable m-0-i"><?php echo __t('Visit.3_months_6_months') ?></label>
            </div>
            <div class="d-inline-block m-right-1">
                <span class="icon-legend c-exito"></span>
                <label for="check-accomplished" class="unselectable m-0-i"><?php echo __t('Visit.3_months') ?></label>
            </div>
        </div>
    </div>

    <div class="row p-top-1" id="ajax_search_home_branches">
        <?php echo $this->element('../Distributors/Elements/ajax_search_home_branches'); ?>
    </div>
    <div class="row">
        <div>
            <button type='button' class="button-general tres" id='button_show_map'><?php echo __t('Network.Show_map') ?></button>
        </div>
    </div>
    <div class="row ta-center m-0-auto d-none" id="row_map">
        <div class="m-0-auto p-1">
            <div class="wrapper-map">
                <div class="contenedor-mapa" id="map-distributors" style="height: 45rem;" data-url="
                    <?php
                    echo Router::url(
                        array(
                            'controller' => 'distributors',
                            'action' => 'location_my_branches',
                            CakeSession::read('Auth.User.distributor_id')
                        )
                    );
                    ?>">
                </div>
            </div>
            <div class="alliance_bar"></div>
        </div>
    </div>
</div>