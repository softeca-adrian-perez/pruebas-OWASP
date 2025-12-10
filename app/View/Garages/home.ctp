<?php
$controller = $this->request->controller;
echo $this->Html->script('garages.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gd_export_excel.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Garage.Garages'),
                array(
                    'controller' => 'garages',
                    'action' => 'home'
                )
            ),
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->Form->button(
            __t('General.Export_garages'),
            array(
                'class' => 'gd-export-js aag-button medium',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-max_export' => GARAGES_EXPORT_LIMIT,
                'data-modal_export' => 'modal_send_csv_email_garages',
                'data-url_ajax_count' => '/garages/ajax_count_garages',
                'data-url' => Router::url(
                    array(
                        'controller' => 'garages',
                        'action' => 'garages_excel',
                        $controller
                    )
                )
            )
        );
        ?>
        <div style="display: none;" id="modal_send_csv_email_garages" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-user-id="<?php echo CakeSession::read('Auth.User.id'); ?>" data-close-on-click="false">
            <div class="aag-subtitle">
                <?php
                echo sprintf(__t('General.Export_limit_exceeded'), GARAGES_EXPORT_LIMIT . ' ' . strtolower(__t('Garage.Garages')));
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
                        'data-url_ajax' => '/garages/ajax_get_csv_data',
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
        if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_GARAGE) && (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN)) {
            echo $this->Html->link(
                __t('Garage.New_garage'),
                array(
                    'controller' => 'garages',
                    'action' => 'add',
                ),
                array('class' => 'aag-button medium green')
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Garages/Elements/search'); ?>
    <div class="p-top-1" id="ajax_search_home">
        <?php echo $this->element('../Garages/Elements/ajax_search_home'); ?>
    </div>
</div>
<div style="display: none;" id="ModalPermissions" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
    <?php echo $this->element('../Garages/Elements/garages_permissions_search'); ?>
</div>