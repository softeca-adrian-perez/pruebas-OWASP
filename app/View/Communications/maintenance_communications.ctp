<?php echo $this->Html->script('communications_maintenance.js?v=' . Configure::read('VERSION_CACHE')); ?>

<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Maintenance.Maintenance'),
                array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Communication.Communications'),
                array(
                    'controller' => 'communications',
                    'action' => 'maintenance_communications'
                )
            ),
            __t('Communication.List'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('Communication.New_communication'),
                array(
                    'controller' => 'communications',
                    'action' => 'add',
                ),
                array('class' => 'aag-button medium green')
            );
        }
        ?>
    </div>
</div>

<div class="cnt-data">
    <?php echo $this->element('../Communications/Elements/search'); ?>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
            <tr>
                <th class="ta-center"><?php echo __t('General.Image'); ?></th>
                <th><?php echo $this->Paginator->sort('Communication.title', __t('General.Title')); ?></th>
                <th><?php echo __t('Communication.Communication_section'); ?></th>
                <th><?php echo __t('Communication.Communication_subsection'); ?></th>
                <th class="ta-center"><?php echo $this->Paginator->sort('Communication.is_popup', __t('Communication.Popup')); ?></th>
                <th><?php echo $this->Paginator->sort('Communication.start_date', __t('Communication.Start_date')); ?></th>
                <th><?php echo $this->Paginator->sort('Communication.end_date', __t('Communication.End_date')); ?></th>
                <th class="ta-center"><?php echo $this->Paginator->sort('Communication.active', __t('General.Active')); ?></th>
                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($communications as $communication) { ?>
                <tr>
                    <td class="ta-center">
                        <img
                            src="<?php echo FileManager::get_url(FilePaths::COMMUNICATIONS_IMAGES_RELATIVE . $communication['Communication']['image']); ?>"
                            style="max-height: 70px">
                    </td>
                    <td>
                        <?php echo $this->Html->link(
                            $communication['Communication']['title'],
                            array(
                                'controller' => 'communications',
                                'action' => 'edit',
                                $communication['Communication']['id']
                            ),
                            array(
                                'class' => 'c-primary'
                            )
                        ); ?>
                    </td>
                    <td>
                        <?php echo h($communications_section[$communication['Communication']['communication_section_id']]); ?>
                    </td>
                    <td>
                        <?php echo h($communications_subsection[$communication['Communication']['section_subsection_id']]); ?>
                    </td>
                    <?php $font = 'c-fallo';
                    if ($communication['Communication']['is_popup']) {
                        $font = 'c-exito';
                    } ?>
                    <td class="ta-center <?php echo $font; ?>">
                        <?php echo ($communication['Communication']['is_popup']) ? __t('General.Yes') : __t('General.No'); ?>
                    </td>
                    <td>
                        <?php echo Fecha::toFormatoVista(h($communication['Communication']['start_date'])); ?>
                    </td>
                    <td>
                        <?php echo Fecha::toFormatoVista(h($communication['Communication']['end_date'])); ?>
                    </td>
                    <?php $font = 'c-fallo';
                    if ($communication['Communication']['active']) {
                        $font = 'c-exito';
                    } ?>
                    <td class="ta-center <?php echo $font; ?>">
                        <?php echo ($communication['Communication']['active']) ? __t('General.Yes') : __t('General.No'); ?>
                    </td>
                    <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                        <td class="ta-center">
                            <?php echo $this->Html->link(
                                '<span class="aag-icon-papelera c-fallo"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'class' => 'delete-communication-js',
                                    'data-confirmmsg' => __t('Communication.Confirm_delete'),
                                    'data-yes' => __t('General.Yes'),
                                    'data-no' => __t('General.No'),
                                    'data-communication_id' => $communication['Communication']['id'],
                                    'data-url_delete' => Router::url(array(
                                        'controller' => 'communications',
                                        'action' => 'ajax_delete_communication',
                                    )),
                                    'data-url_redirect' => Router::url(array(
                                        'controller' => 'communications',
                                        'action' => 'maintenance_communications',
                                    ))
                                )
                            ); ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>