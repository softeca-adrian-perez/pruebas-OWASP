<?php
echo $this->Html->script('conferences.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gd_export_excel.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Conference.Conferences'),
                array(
                    'controller' => 'conferences',
                    'action' => 'home'
                )
            ),
            __t('Conference.Conferences_list'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        echo $this->Form->button(
            __t('General.Export_conferences'),
            array(
                'class' => 'aag-button medium gd-export-js',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-max_export' => CONFERENCES_EXPORT_LIMIT,
                'data-modal_export' => 'modal_send_csv_email_conferences',
                'data-url_ajax_count' => '/conferences/ajax_count_conferences',
                'data-url' => Router::url(
                    array(
                        'controller' => 'conferences',
                        'action' => 'ajax_conferences_excel'
                    )
                )
            )
        );
        ?>
        <div style="display: none;" id="modal_send_csv_email_conferences" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-user-id="<?php echo CakeSession::read('Auth.User.id'); ?>" data-close-on-click="false">
            <div class="aag-subtitle">
                <?php
                echo sprintf(__t('General.Export_limit_exceeded'), CONFERENCES_EXPORT_LIMIT . ' ' . strtolower(__t('Conference.Conferences')));
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
                        'data-url_ajax' => '/conferences/ajax_get_csv_data',
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
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('Conference.New_conference'),
                array(
                    'controller' => 'conferences',
                    'action' => 'add'
                ),
                array('class' => 'aag-button green medium')
            );
        }
        ?>
    </div>
</div>
<?php echo $this->element('../Elements/Comun/conferen-deleg-tabs', array('selected' => 'conferences')); ?>
<div class="cnt-data">
    <?php echo $this->element('../Conferences/Elements/search_conference'); ?>
    <div class="o-auto">
        <table id="conferences_table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Conference.name',  __t('Conference.Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Conference.start_date',  __t('Conference.Start_date')); ?></th>
                    <th><?php echo $this->Paginator->sort('Conference.duration',  __t('Conference.Duration')); ?></th>
                    <th><?php echo $this->Paginator->sort('Venue.name',  __t('Conference.Venue')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('Conference.status',  __t('Conference.Status')); ?></th>
                    <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                        <th class="ta-center"><?php echo __t('General.Actions') ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conferences as $conference) { ?>
                    <tr>
                        <td>
                            <?php
                            if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                echo $this->Html->link(
                                    '<span class="c-primary">' . h($conference['Conference']['name']) . '</span>',
                                    array(
                                        'controller' => 'conferences',
                                        'action' => 'edit',
                                        $conference['Conference']['id'],
                                    ),
                                    array(
                                        'escape' => false,
                                    )
                                );
                            }else{
                                echo h($conference['Conference']['name']);
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo h(Fecha::toFormatoVista($conference['Conference']['start_date'])); ?>
                        </td>
                        <td>
                            <?php echo h($conference['Conference']['duration']); ?>
                        </td>
                        <td>
                            <?php echo h($conference['Venue']['name']); ?>
                        </td>
                        <td class="ta-center">
                        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                            <?php if ($conference['Conference']['status'] == ConstantsBooleans::YES) { ?>
                                <span class="cursor-pointer ico-toggle ion-toggle-filled c-exito icono-grande" id="<?php echo 'checkbox_' .  $conference['Conference']['id'] ?>"
                                    data-url="
                                        <?php echo Router::url(
                                            array(
                                                'controller' => 'conferences',
                                                'action' => 'ajax_toggle_status',
                                                $conference['Conference']['id'],
                                            )
                                        ); ?>"
                                    >
                                </span>
                            <?php } else { ?>
                                <span class="cursor-pointer ico-toggle ion-toggle c-fallo icono-grande" id="<?php echo 'checkbox_' .  $conference['Conference']['id'] ?>"
                                    data-url="
                                        <?php echo Router::url(
                                            array(
                                                'controller' => 'conferences',
                                                'action' => 'ajax_toggle_status',
                                                $conference['Conference']['id'],
                                            )
                                        ); ?>"
                                    >
                                </span>
                            <?php } ?>
                        <?php } else { ?>
                            <?php if ($conference['Conference']['status'] == ConstantsBooleans::YES) { ?>
                                <span class="cursor-pointer ion-toggle-filled c-exito icono-grande" id="<?php echo 'checkbox_' .  $conference['Conference']['id'] ?>"
                                    >
                                </span>
                            <?php } else { ?>
                                <span class="cursor-pointer ion-toggle c-fallo icono-grande" id="<?php echo 'checkbox_' .  $conference['Conference']['id'] ?>"
                                    >
                                </span>
                            <?php } ?>
                        <?php }?>
                        </td>
                        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                            <td class="ta-center">
                                <span class="cursor-pointer aag-icon-papelera c-fallo delete-conference" data-delete-url="
                                    <?php echo Router::url(
                                        array(
                                            'controller' => 'conferences',
                                            'action' => 'ajax_delete',
                                            $conference['Conference']['id'],
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
    <?php echo $this->element('Comun/paginacion'); ?>
</div>