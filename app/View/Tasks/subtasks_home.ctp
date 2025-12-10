<?php
echo $this->Html->script('tasks_germany.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->hidden('', array('id' => 'subtasks_home')) ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Task.Tasks'),
                array(
                    'controller' => 'tasks',
                    'action' => 'home'
                )
            ),
            __t('Task.Subtask'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            CakeSession::read('url_referer'),
            array(
                'class' => 'aag-button medium four',
            )
        );
        ?>
    </div>
</div>
<div class="d-none" id="confirm-uncheck" data-msg="<?php echo __t('Task.Confirm_unchecked'); ?>"
    data-url="<?php echo Router::url(
                    array(
                        'controller' => 'tasks',
                        'action' => 'ajax_uncheck_task'
                    )
                ); ?>">
</div>
<div class="d-none" id="confirm-check" data-msg="<?php echo __t('Task.Confirm_checked'); ?>"
    data-url="<?php echo Router::url(
                    array(
                        'controller' => 'tasks',
                        'action' => 'ajax_check_task'
                    )
                ); ?>">
</div>
<div class="d-none" id="confirm-delete" data-msg="<?php echo __t('Task.Confirm_delete'); ?>"
    data-url="<?php echo Router::url(
                    array(
                        'controller' => 'tasks',
                        'action' => 'ajax_delete_task'
                    )
                ); ?>">
</div>

<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Task.Subtasks'); ?>
    </div>
    <div class="p-top-1" id="results_table_ajax">
        <?php echo $this->element('../Tasks/Elements/results_table_subtasks'); ?>
    </div>
</div>
</div>