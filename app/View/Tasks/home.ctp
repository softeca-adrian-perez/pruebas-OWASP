<?php echo $this->Html->script('tasks_germany.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
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
                __t('Task.Tasks'),
                array(
                    'controller' => 'tasks',
                    'action' => 'home'
                )
            ),
            __t('General.Home'),
        ));
        $this->end();
        ?>
    </div>
        <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN){ ?>
            <?php
            echo $this->Html->link(
                __t('Appointment.Create_task'),
                array(
                    'controller' => 'tasks',
                    'action' => 'add',
                ),
                array('class' => 'aag-button medium green')
            );
            ?>
        <?php } ?>
        </div>
</div>
<?php
echo $this->Form->hidden('Home', array('id' => 'home'));
$tab = isset($this->request->query['tab']) ? $this->request->query['tab'] : null;
echo $this->Form->hidden(
    'selected_tab_search',
    array(
        'value' => $tab,
        'id' => 'selected_tab_search'
    )
)
?>
<div class="d-none" id="confirm-uncheck" data-msg="<?php echo __t('Task.Confirm_unchecked'); ?>" data-url="<?php echo Router::url(
    array(
        'controller' => 'tasks',
        'action' => 'ajax_uncheck_task'
    )
    ); ?>">
</div>
<div class="d-none" id="confirm-check" data-msg="<?php echo __t('Task.Confirm_checked'); ?>" data-url="<?php echo Router::url(
    array(
        'controller' => 'tasks',
        'action' => 'ajax_check_task'
    )
); ?>">
</div>
<div class="d-none" id="confirm-delete" data-msg="<?php echo __t('Task.Confirm_delete'); ?>" data-url="<?php echo Router::url(
    array(
        'controller' => 'tasks',
        'action' => 'ajax_delete_task'
    )
); ?>">
</div>

<div class="cnt-data">
    <?php echo $this->element('../Tasks/Elements/search'); ?>
    <div id="results_table_ajax" class="flex fd-column fg-1">
    </div>
</div>
