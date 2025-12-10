<?php
$controller = $this->request->controller;

echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps_distributors.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('markerclusterer.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('distributors.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
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
            __t('General.Home')
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->Form->button(
            __t('General.Export_distributors'),
            array(
                'class' => 'aag-button two outlined medium gd-export-js',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-max_export' => DISTRIBUTORS_EXPORT_LIMIT,
                'data-modal_export' => 'modal_send_csv_email_distributors',
                'data-url_ajax_count' => '/distributors/ajax_count_distributors',
                'data-url' => Router::url(
                    array(
                        'controller' => 'distributors',
                        'action' => 'ajax_distributor_excel',
                        $controller
                    )
                )
            )
        );
        ?>
        <div style="display: none;" id="modal_send_csv_email_distributors" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-user-id="<?php echo CakeSession::read('Auth.User.id'); ?>" data-close-on-click="false">
            <div class="aag-subtitle">
                <?php echo sprintf(__t('General.Export_limit_exceeded'), DISTRIBUTORS_EXPORT_LIMIT . ' ' . strtolower(__t('Distributor.Distributors'))); ?>
            </div>
            <?php
            $user = $this->Acceso->user();
            echo $this->Form->input(
                'email',
                array(
                    'label' => __t('Contact.Email'),
                    'value' => $user['Contact']['email'],
                    'id' => 'contact-email-js',
                )
            );
            ?>
            <div class="ta-right p-top-1">
                <?php
                echo $this->Form->button(
                    __t('General.Send'),
                    array(
                        'id' => 'send-data-export-js',
                        'class' => 'aag-button medium green send-data-export-js',
                        'type' => 'submit',
                        'escape' => false,
                        'data-url_ajax' => '/distributors/ajax_get_csv_data',
                        'data-url' => Router::url(
                            array(
                                'controller' => 'app',
                                'action' => 'ajax_verify_email',
                            )
                        ),
                    )
                );
                ?>
            </div>
            <a class="close-modal" data-close aria-label="Close">&#215;</a>
        </div>
        <?php
        if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_DISTRIBUTOR)) {
            echo $this->Html->link(
                __t('Distributor.New_distributor'),
                array(
                    'controller' => 'distributors',
                    'action' => 'add',
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button green medium',
                )
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Distributors/Elements/search'); ?>
    <div class="cnt-legend">
        <div>
            <?php echo __t('Appointment.Period_of_time'); ?>
        </div>
        <div>
            <span class="icon-legend c-fallo"></span> <?php echo __t('Visit.Plus_6_months'); ?>
        </div>
        <div>
            <span class="icon-legend c-informacion"></span> <?php echo __t('Visit.3_months_6_months'); ?>
        </div>
        <div class="d-inline-block">
            <span class="icon-legend c-exito"></span> <?php echo __t('Visit.3_months'); ?>
        </div>
    </div>
    <div class="p-top-1" id="ajax_search_home">
        <?php echo $this->element('../Distributors/Elements/ajax_search_home'); ?>
    </div>
    <div style="display: none;" id="ModalPermissions" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
        <a class="close-modal" data-close aria-label="Close">&#215;</a>
        <?php echo $this->element('../Distributors/Elements/distributors_permissions_search'); ?>
    </div>
</div>