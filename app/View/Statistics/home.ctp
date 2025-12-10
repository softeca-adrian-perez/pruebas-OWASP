<?php
echo $this->Html->script('statistics.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/Chart.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gd_export_excel.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/dataTables/css/dataTables.foundation.min.css', array('block' => 'script'));
echo $this->Html->script('lib/dataTables/js/jquery.dataTables.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Statistics.Statistics'),
                array(
                    'controller' => 'statistics',
                    'action' => 'home'
                )
            ),
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<div class="cnt-data fg-0">
    <?php echo $this->element('../Statistics/Elements/search'); ?>
    <hr class="hr-divisor" />
    <div class="aag-title cnt-data-element m-bottom-1">
        <?php
        echo __t('Contact.BDM');
        echo $this->Form->button(
            __t('General.Export') . ' ' . __t('Contact.BDM'),
            array(
                'class' => 'gd-export-excel-buscador-js aag-button medium',
                'value' => 'submit',
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'statistics',
                        'action' => 'ajax_bdms_excel'
                    )
                )
            )
        );
        ?>
    </div>
    <div class="row" id="cnt_bdms">
        <?php echo $this->element('../Statistics/Elements/bdms'); ?>
    </div>
    <hr class="hr-divisor" />
    <div class="aag-title cnt-data-element m-bottom-1">
        <?php echo __t('Statistics.Distributors'); ?>
        <div>
            <?php
            echo $this->Form->button(
                __t('General.Load_distributors'),
                array(
                    'id' => 'load-distributors-js',
                    'class' => 'aag-button medium',
                    'data-url' => Router::url(
                        array(
                            'controller' => 'statistics',
                            'action' => 'ajax_distributors',
                            $date_from,
                            $date_to
                        )
                    ),
                )
            );
            echo $this->Form->button(
                __t('General.Export') . ' ' . __t('Statistics.Distributors'),
                array(
                    'class' => 'gd-export-excel-buscador-js aag-button medium',
                    'value' => 'submit',
                    'escape' => false,
                    'name' => 'export',
                    'data-url' => Router::url(
                        array(
                            'controller' => 'statistics',
                            'action' => 'ajax_distributors_excel',
                            $date_from,
                            $date_to
                        )
                    ),
                )
            );
            ?>
        </div>
    </div>
    <div class="row" id="cnt_distributors">
    </div>
    <hr class="hr-divisor" />
    <div class="aag-title cnt-data-element m-bottom-1">
        <?php echo __t('Statistics.Garages'); ?>
        <div>
            <?php
            echo $this->Form->button(
                __t('General.Load') . ' ' . __t('Statistics.Garages'),
                array(
                    'id' => 'load-garages-js',
                    'class' => 'aag-button medium',
                    'data-url' => Router::url(
                        array(
                            'controller' => 'statistics',
                            'action' => 'ajax_garages',
                            $date_from,
                            $date_to
                        )
                    ),
                )
            );
            echo $this->Form->button(
                __t('General.Export') . ' ' . __t('Statistics.Garages'),
                array(
                    'class' => 'gd-export-excel-buscador-js aag-button medium',
                    'value' => 'submit',
                    'escape' => false,
                    'name' => 'export',
                    'data-url' => Router::url(
                        array(
                            'controller' => 'statistics',
                            'action' => 'ajax_garages_excel',
                            $date_from,
                            $date_to
                        )
                    ),
                )
            );
            ?>
        </div>
    </div>
    <div
        class="row has_g_number-js"
        id="cnt_garages"
        data-has-g-number=
        "<?php
        //If it's Benelux there is not garage g_number, but if the role is Super Admin, garages from both regions can be present at the same time, this is used to tell the jquery Datatable if one row is needed or not
        if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) {
            ?>true<?php
        } else { ?>false<?php } ?>"
    >
    </div>
    <hr class="hr-divisor" />
    <div class="aag-title cnt-data-element m-bottom-1">
        <?php
        echo __t('Statistics.Last_connection_date');
        echo $this->Form->button(
            __t('General.Export') . ' ' . __t('Statistics.Last_connection_date'),
            array(
                'class' => 'gd-export-excel-buscador-js aag-button medium',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'statistics',
                        'action' => 'ajax_connections_excel'
                    )
                ),
            )
        );
        ?>
    </div>
    <div class="row" id="cnt_connections">
        <?php echo $this->element('../Statistics/Elements/result_home'); ?>
    </div>
    <hr class="hr-divisor" />
    <div class="aag-title cnt-data-element m-bottom-1">
        <?php
        echo __t('Statistics.Connections_per_devices');
        echo $this->Form->button(
            __t('General.Export') . ' ' . __t('Statistics.Connections_per_devices'),
            array(
                'class' => 'gd-export-excel-buscador-js aag-button medium',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'statistics',
                        'action' => 'ajax_devices_excel'
                    )
                ),
            )
        );
        ?>
    </div>
    <div class="row" id="cnt_devices">
        <?php echo $this->element('../Statistics/Elements/connections_per_devices'); ?>
    </div>
</div>

<div class="row p-1 <?php echo  'd-none'; ?>">
    <div class="medium-12 columns background-color-primary p-bottom-1">
        <div class="row p-0 p-vertical-1">
            <div class="columns medium-6 titulo2">
                <?php echo __t('Statistics.Connections_per_sections'); ?>
            </div>
            <div class="columns medium-6 ta-right right-0 cnt-buttons-v2">
                <div class="f-right">
                    <?php echo $this->Form->button(
                        __t('General.Export') . ' ' . __t('Statistics.Connections_per_sections'),
                        array(
                            'class' => 'gd-export-excel-buscador-js aag-button medium',
                            'value' => 'submit',
                            'escape' => false,
                            'name' => 'export',
                            'data-url' => Router::url(
                                array(
                                    'controller' => 'statistics',
                                    'action' => 'ajax_sections_excel'
                                )
                            ),
                        )
                    ); ?>
                </div>
            </div>
        </div>

        <div class="row" id="cnt_sections">
            <?php echo $this->element('../Statistics/Elements/connections_per_section'); ?>
        </div>
    </div>
</div>

<div class="row p-1 <?php echo 'd-none'; ?>">
    <div class="medium-12 columns background-color-primary p-bottom-1">
        <div class="row p-1 p-vertical-1">
            <div class="columns medium-6 titulo2">
                <?php echo __t('Statistics.Connections_per_articles'); ?>
            </div>
            <div class="columns medium-6 ta-right right-0 cnt-buttons-v2">
                <div class="f-right">
                    <?php echo $this->Form->button(
                        __t('General.Export') . ' ' . __t('Statistics.Connections_per_articles'),
                        array(
                            'class' => 'gd-export-excel-buscador-js aag-button medium',
                            'value' => 'submit',
                            'escape' => false,
                            'name' => 'export',
                            'data-url' => Router::url(
                                array(
                                    'controller' => 'statistics',
                                    'action' => 'ajax_articles_excel'
                                )
                            ),
                        )
                    ); ?>
                </div>
            </div>
        </div>

        <div class="row" id="cnt_articles">
            <?php echo $this->element('../Statistics/Elements/connections_per_articles'); ?>
        </div>
    </div>
</div>

<script>
    var users_statistics_sections = <?php echo json_encode($users_statistics_sections); ?>;
    var users_statistics_sections_names = <?php echo json_encode($users_statistics_sections_names); ?>;
    var users_statistics_articles = <?php echo json_encode($users_statistics_articles); ?>;
    var users_statistics_articles_names = <?php echo json_encode($users_statistics_articles_names); ?>;
    var device_connections = <?php echo $device_connections; ?>;
    var months = <?php echo json_encode(array_values($months)); ?>;
    var bdms = <?php echo json_encode($bdms); ?>;
    var distributors = <?php echo json_encode($distributors); ?>;
</script>