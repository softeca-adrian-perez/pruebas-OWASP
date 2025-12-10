<?php
$controller = $this->request->controller;

echo $this->Html->script('gd_export_excel.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$user = $this->Acceso->user();
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('CRM.Crm'),
                array(
                    'controller' => 'dashboard',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Distributor.Distributor'),
                array(
                    'controller' => 'clients',
                    'action' => 'home_distributors'
                )
            ),
            __t('General.List'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->Form->button(
            __t('General.Export_distributors'),
            array(
                'class' => 'aag-button medium gd-export-js',
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
                <?php
                echo sprintf(__t('General.Export_limit_exceeded'), DISTRIBUTORS_EXPORT_LIMIT . ' ' . strtolower(__t('Distributor.Distributors')));
                ?>
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
                    'action' => 'add'
                ),
                array('class' => 'aag-button medium green')
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Clients/Elements/search_distributors'); ?>
    <div class="p-top-1 f-right cnt-legend">
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
    <div id="ajax_search_distributors" class="flex fd-column fg-1">
        <?php echo $this->element('../Clients/Elements/ajax_search_distributors'); ?>
    </div>
</div>