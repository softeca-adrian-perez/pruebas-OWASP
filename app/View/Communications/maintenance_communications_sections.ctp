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
                __t('Communication.Communications') . ' ' . __t('Communication.Communications_sections'),
                array(
                    'controller' => 'communications',
                    'action' => 'maintenance_communications_sections'
                )
            ),
            __t('Communication.List')
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('Communication.New_communication_section'),
                array(
                    'controller' => 'communications',
                    'action' => 'add_section',
                ),
                array('class' => 'aag-button medium green')
            );
        } ?>
    </div>
</div>

<div class="cnt-data">
    <div id="communication_sections-js" class="flex fd-column fg-1">
        <?php echo $this->element('../Communications/Elements/result_table_sections'); ?>
    </div>
</div>