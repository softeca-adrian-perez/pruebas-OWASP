<?php
echo $this->Html->script('gd_export_excel.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Training.Training'),
                array(
                    'controller' => 'trainings_providers',
                    'action' => 'home'
                )
            ),
            __t('Training.List_delegates'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        echo $this->Form->button(
            __t('General.Export_training'),
            array(
                'class' => 'gd-export-js aag-button medium blue',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-max_export' => TRAININGS_EXPORT_LIMIT,
                'data-modal_export' => 'modal_send_csv_email_training',
                'data-url_ajax_count' => '/trainings_planned_courses/ajax_count_trainings',
                'data-url' => Router::url(
                    array(
                        'controller' => 'trainings_planned_courses',
                        'action' => 'general_training_excel'
                    )
                )
            )
        );
        echo $this->Form->button(
            __t('General.Export_list_delegates'),
            array(
                'class' => 'gd-export-js aag-button medium blue',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-max_export' => TRAININGS_EXPORT_LIMIT,
                'data-modal_export' => 'modal_send_csv_email_delegates',
                'data-url_ajax_count' => '/trainings_list_delegates/ajax_count_delegates',
                'data-url' => Router::url(
                    array(
                        'controller' => 'trainings_list_delegates',
                        'action' => 'training_list_delegates_excel'
                    )
                )
            )
        );
        ?>
        <div style="display: none;" id="modal_send_csv_email_delegates" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-user-id="<?php echo CakeSession::read('Auth.User.id'); ?>">
            <div class="aag-subtitle">
            <?php echo sprintf(__t('General.Export_limit_exceeded'), TRAININGS_EXPORT_LIMIT . ' ' . strtolower(__t('General.Rows')));?>
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
                        'data-url_ajax' => '/trainings_list_delegates/ajax_get_csv_data',
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
        <div style="display: none;" id="modal_send_csv_email_training" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-user-id="<?php echo CakeSession::read('Auth.User.id'); ?>" data-close-on-click="false">
            <div class="aag-subtitle">
            <?php echo sprintf(__t('General.Export_limit_exceeded'), TRAININGS_EXPORT_LIMIT . ' ' . strtolower(__t('General.Rows')));?>
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
                        'data-url_ajax' => '/trainings_planned_courses/ajax_get_csv_data',
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
    </div>
</div>
<?php echo $this->element('../Elements/Comun/trainings_general_tab', array('selected' => 'training',)); ?>
<div class="cnt-data aag-padding">
    <div class="aag-padding">
        <?php echo $this->element('../Elements/Comun/trainings_sub_tab', array('selected' => 'list_delegates')); ?>
    </div>
    <?php echo $this->element('../TrainingsListDelegates/Elements/search_list_delegate'); ?>
    <div class="p-top-1" id="ajax_search_home">
        <?php echo $this->element('../TrainingsListDelegates/Elements/ajax_search_home'); ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>