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
                __t('Distributor.Distributor'),
                array(
                    'controller' => 'clients',
                    'action' => 'home_distributors'
                )
            ),
            __t('CRM.Task_history'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="d-inline-block p-right-1 cnt-title-report">
        <?php echo $this->element('../Clients/Elements/menu_distributor') ?>
    </div>
    <div class="clear cnt-form-animate">
        <?php echo $this->element('../Clients/Elements/search_tracking_task', array('param' => $distributor['Distributor']['id'])); ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
            <tr>
                <th class="ta-center"><?php echo __t('Task.Body'); ?></th>
                <th class="ta-center"><?php echo __t('Task.Assigned_to'); ?></th>
                <th class="ta-center"><?php echo __t('Task.Creation_date'); ?></th>
                <th class="ta-center"><?php echo __t('Task.Completed_date'); ?></th>
                <th class="ta-center"><?php echo __t('Task.Status'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach( $tasks as $key => $task )
            {
                ?>
                <tr>
                    <td class="ta-center">
                        <div>
                            <?php echo (substr($task['Task']['body'], 0, 30) != $task['Task']['body']) ?  h(substr($task['Task']['body'], 0, 30)) . '...' : h($task['Task']['body']) ?>
                        </div>
                    </td>
                    <td class="ta-center">
                        <div>
                            <?php
                            if($task['User']['name'] != null){
                                echo h($task['User']['name']) . ' ' . h($task['User']['surname']);
                            }
                            ?>
                        </div>
                    </td>
                    <td class="ta-center">
                        <div>
                            <?php echo Fecha::toFormatoVistaFecha(h($task['Task']['creation_date'])); ?>
                        </div>
                    </td>
                    <td class="ta-center">
                        <div>
                            <?php echo Fecha::toFormatoVistaFecha(h($task['TaskDistributor']['completed_date'])); ?>
                        </div>
                    </td>
                    <td class="ta-center">
                        <div>
                            <?php
                            echo $this->Form->hidden(
                                '',
                                array(
                                    'class' => 'distributor-data-check',
                                    'data-distributor-id' => $task['TaskDistributor']['distributor_id'],
                                    'data-url' => Router::url(
                                        array(
                                            'controller' => 'tasks',
                                            'action' => 'ajax_check_task_distributor',
                                        )
                                    )
                                )
                            );

                            echo $this->Form->hidden(
                                '',
                                array(
                                    'class' => 'distributor-data-uncheck',
                                    'data-distributor-id' => $task['TaskDistributor']['distributor_id'],
                                    'data-url' => Router::url(
                                        array(
                                            'controller' => 'tasks',
                                            'action' => 'ajax_uncheck_task_distributor',
                                        )
                                    )
                                )
                            );

                            if ($task['TaskDistributor']['completed'] == ConstantsBooleans::NO) {
                                echo $this->Html->link(
                                    '<span class="ion-ios-checkmark-outline c-informacion"></span>',
                                    array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_check_task_distributor',
                                        $task['Task']['id'],
                                        $task['TaskDistributor']['distributor_id']

                                    ),
                                    array(
                                        'escape' => false,
                                        'class' => 'check-task-js',
                                        'data-id' => $task['Task']['id'],
                                        'data-confirmmsg' => __t('Task.Confirm_checked'),
                                        'data-yes' => __t('General.Yes'),
                                        'data-no' => __t('General.No'),
                                        'title' => __t('Task.Mark_as_complete'),
                                        'data-title-complete' => __t('Task.Mark_as_complete'),
                                        'data-title-pending' => __t('Task.Mark_as_pending')
                                    )
                                );
                                ?>
                                <?php
                            } else {
                                echo $this->Html->link(
                                    '<span class="ion-ios-checkmark-outline c-exito"></span>',
                                    array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_uncheck_task_distributor',
                                        $task['Task']['id'],
                                        $task['TaskDistributor']['distributor_id']
                                    ),
                                    array(
                                        'escape' => false,
                                        'class' => 'uncheck-task-js',
                                        'data-id' => $task['Task']['id'],
                                        'data-confirmmsg' => __t('Task.Confirm_unchecked'),
                                        'data-yes' => __t('General.Yes'),
                                        'data-no' => __t('General.No'),
                                        'title' => __t('Task.Mark_as_pending'),
                                        'data-title-complete' => __t('Task.Mark_as_complete'),
                                        'data-title-pending' => __t('Task.Mark_as_pending')
                                    )
                                );
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <br />
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
