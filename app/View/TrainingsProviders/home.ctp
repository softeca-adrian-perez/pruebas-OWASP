<?php
echo $this->Html->script('training_provider.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gd_export_excel.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
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
            __t('Training.Trainings_providers'),
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
            __t('General.Export_training_providers'),
            array(
                'class' => 'gd-export-training-providers-js aag-button medium',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'trainings_providers',
                        'action' => 'training_providers_excel'
                    )
                )
            )
        );
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('Training.New_training_provider'),
                array(
                    'controller' => 'trainings_providers',
                    'action' => 'add'
                ),
                array('class' => ' aag-button medium green')
            );
        }
        ?>
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
<?php echo $this->element('../Elements/Comun/trainings_general_tab', array('selected' => 'training')); ?>
<div class="cnt-data">
    <div class="aag-padding">
        <?php echo $this->element('../Elements/Comun/trainings_sub_tab', array('selected' => 'trainings_providers')); ?>
    </div>
    <?php echo $this->element('../TrainingsProviders/Elements/search_training_provider'); ?>
    <div class="o-auto">
        <table id="training_table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('TrainingProvider.name', __t('Training.Training_provider_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingProvider.email', __t('Training.Email')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingProvider.phone', __t('Training.Phone')); ?></th>
                    <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN){ ?>
                        <th class="ta-center"><?php echo __t('General.Actions') ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trainings_providers as $provider) { ?>
                    <tr>
                        <td>
                            <b><?php echo h($provider['TrainingProvider']['name']); ?></b>
                        </td>
                        <td class="color-blue-text">
                            <?php echo h($provider['TrainingProvider']['email']); ?>
                        </td>
                        <td class="color-blue-text">
                            <?php echo h($provider['TrainingProvider']['phone']); ?>
                        </td>
                        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN){ ?>
                            <td class="td-icons">
                                <?php echo $this->Html->link(
                                    '<span class="cursor-pointer edit-provider aag-icon-editar"></span>',
                                    array(
                                        'controller' => 'trainings_providers',
                                        'action' => 'edit',
                                        $provider['TrainingProvider']['id'],
                                    ),
                                    array(
                                        'class' => 'flex',
                                        'escape' => false,
                                    )
                                );
                                ?>
                                <span class="aag-icon-papelera c-fallo delete-provider cursor-pointer" data-id-provider="<?php echo $provider['TrainingProvider']['id'] ?>" data-delete-url="
                                    <?php echo Router::url(
                                        array(
                                            'controller' => 'trainings_providers',
                                            'action' => 'ajax_delete',
                                            $provider['TrainingProvider']['id'],
                                        )
                                    ); ?>
                                    "
                                    data-delete-url-assoc="
                                    <?php echo Router::url(
                                        array(
                                            'controller' => 'trainings_providers',
                                            'action' => 'ajax_delete_assoc',
                                            $provider['TrainingProvider']['id'],
                                        )
                                    ); ?>
                                    ">
                                </span>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <br />
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
<?php echo $this->Form->end(); ?>