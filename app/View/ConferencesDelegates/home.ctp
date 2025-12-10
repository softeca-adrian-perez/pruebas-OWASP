<?php
echo $this->Html->script('conferences_delegates.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
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
            __t('General.Export_list_delegates'),
            array(
                'class' => 'aag-button medium gd-export-js',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-max_export' => CONFERENCES_DELEGATES_EXPORT_LIMIT,
                'data-modal_export' => 'modal_send_csv_email_conferences_delegates',
                'data-url_ajax_count' => '/conferences_delegates/ajax_count_conferences_delegates',
                'data-url' => Router::url(
                    array(
                        'controller' => 'conferences_delegates',
                        'action' => 'ajax_delegates_excel'
                    )
                )
            )
        );
        ?>
        <div style="display: none;" id="modal_send_csv_email_conferences_delegates" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-user-id="<?php echo CakeSession::read('Auth.User.id'); ?>" data-close-on-click="false" data-close-on-click="false">
            <div class="aag-subtitle">
                <?php
                echo sprintf(__t('General.Export_limit_exceeded'), CONFERENCES_DELEGATES_EXPORT_LIMIT . ' ' . strtolower(__t('Delegate.Delegates')));
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
                        'data-url_ajax' => '/conferences_delegates/ajax_get_csv_data',
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
                __t('Delegate.New_Delegate'),
                array(
                    'controller' => 'conferences_delegates',
                    'action' => 'add',
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium green'
                )
            );
        }
        ?>
    </div>
</div>
<?php echo $this->element('../Elements/Comun/conferen-deleg-tabs', array('selected' => 'delegate')); ?>
<div class="cnt-data">
    <?php echo $this->element('../ConferencesDelegates/Elements/search_delegate'); ?>
    <div class="o-auto">
        <table id="conferences_table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Conference.name', __t('Delegate.Conference_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('ConferenceDelegate.contact', __t('Delegate.Delegate_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Venue.name', __t('Delegate.Venue_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Distributor.name', __t('Delegate.Distributor_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Supplier.name', __t('Delegate.Supplier_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Garage.name', __t('Delegate.Garage_name')); ?></th>
                    <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                        <th class="ta-center"><?php echo __t('General.Actions') ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($delegates as $delegate) { ?>
                    <tr>
                        <td>

                            <?php
                            if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                echo $this->Html->link(
                                    '<span class="c-primary">' . h($delegate['Conference']['name']) . '</span>',
                                    array(
                                        'controller' => 'conferences_delegates',
                                        'action' => 'edit',
                                        $delegate['ConferenceDelegate']['id'],
                                    ),
                                    array(
                                        'escape' => false,
                                    )
                                );
                            }else{
                                echo h($delegate['Conference']['name']);
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo h($delegate['ConferenceDelegate']['contact']); ?>
                        </td>
                        <td>
                            <?php echo h($delegate['Venue']['name']); ?>
                        </td>
                        <td>
                            <?php echo h($delegate['Distributor']['name']); ?>
                        </td>
                        <td>
                            <?php echo h($delegate['Supplier']['name']); ?>
                        </td>
                        <td>
                            <?php echo h($delegate['Garage']['name']); ?>
                        </td>
                        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                            <td class="ta-center">
                                <span class="aag-icon-papelera c-fallo delete-delegate" style="cursor: pointer;" data-delete-url="
                                    <?php echo Router::url(
                                        array(
                                            'controller' => 'conferences_delegates',
                                            'action' => 'ajax_delete',
                                            $delegate['ConferenceDelegate']['id'],
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