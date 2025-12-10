<?php echo $this->Html->script('debrief-tasks.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
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
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
    </div>
    <div>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('Task.New_debrief_task'),
                array(
                    'controller' => 'tasks',
                    'action' => 'add_debrief_task'
                ),
                array('class' => 'aag-button medium green')
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data p-top-1">
    <div class="aag-title cnt-data-element">
        <?php echo __t('Task.Debrief') . ' ' . __t('Task.Task'); ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('DebriefTask.title' . __s(), __t('Task.Title')); ?></th>
                    <th><?php echo $this->Paginator->sort('DebriefTask.description' . __s(), __t('Alert.Description')); ?></th>
                    <th><?php echo __t('Contact.Contact_list'); ?></th>
                    <th><?php echo __t('Task.Assigned_to'); ?></th>
                    <th><?php echo __t('Garage.Garages'); ?></th>
                    <th><?php echo __t('Distributor.Distributors'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($debrief_tasks as $debrief_task) { ?>
                    <tr>
                        <td>
                            <?php echo $this->Html->link(
                                $debrief_task['DebriefTask']['title' . __s()],
                                array(
                                    'controller' => 'tasks',
                                    'action' => 'edit_debrief_task',
                                    $debrief_task['DebriefTask']['id']
                                ),
                                array('class' => 'c-primary')
                            ); ?>
                        </td>
                        <td>
                            <?php echo h($debrief_task['DebriefTask']['description' . __s()]); ?>
                        </td>
                        <td>
                            <?php if (!is_null($debrief_task['DebriefTask']['contact_list_id'])) {
                                echo h($contact_lists[h($debrief_task['DebriefTask']['contact_list_id'])]);
                            } ?>
                        </td>
                        <td>
                            <?php if (!is_null($debrief_task['DebriefTask']['user_assigned_id'])) {
                                echo h($users[h($debrief_task['DebriefTask']['user_assigned_id'])]);
                            } ?>
                        </td>
                        <td>
                            <?php echo $debrief_task['garages']; ?>
                        </td>
                        <td>
                            <?php echo $debrief_task['distributors']; ?>
                        </td>
                        <td class="ta-center">
                            <?php
                            echo $this->Html->link(
                                "<span class='aag-icon-papelera c-fallo'></span>",
                                array(
                                    'controller' => 'tasks',
                                    'action' => 'delete_debrief_task',
                                    $debrief_task['DebriefTask']['id']
                                ),
                                array(
                                    'escape' => false,
                                    'id' => 'delete_task',
                                    'class' => 'delete-task-js',
                                    'data-confirmmsg' => __t('DebriefTask.Confirm_delete'),
                                    'data-yes' => __t('General.Yes'),
                                    'data-no' => __t('General.No'),
                                )
                            );
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>