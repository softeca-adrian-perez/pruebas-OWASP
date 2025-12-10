<?php echo $this->Html->script('debrief-topics.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
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
                __t('Task.Debrief') . ' ' .  __t('Task.Topics'),
                array(
                    'controller' => 'tasks',
                    'action' => 'maintenance_topics'
                )
            ),
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('Task.New_debrief_topic'),
                array(
                    'controller' => 'tasks',
                    'action' => 'add_debrief_topic',
                ),
                array('class' => 'aag-button medium green')
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data">
    <div class="aag-title cnt-data-element p-top-1">
        <?php echo __t('Task.Debrief') . ' ' . __t('Task.Topics'); ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('DebriefTopic.name_en', __t('General.Name_en'));?></th>
                <th><?php echo $this->Paginator->sort('DebriefTopic.name_fr', __t('General.Name_fr'));?></th>
                <th><?php echo $this->Paginator->sort('DebriefTopic.name_de', __t('General.Name_de'));?></th>
                <th class="ta-center"><?php echo __t('General.Actions');?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($debrief_topics as $debrief_topic) { ?>
                <tr>
                    <td class="ta-left">
                        <?php echo $this->Html->link(
                            $debrief_topic['DebriefTopic']['name_en'],
                            array(
                                'controller' => 'tasks',
                                'action' => 'edit_debrief_topic',
                                $debrief_topic['DebriefTopic']['id']
                            ),
                            array(
                                'class' => 'c-primary'
                            )
                        ); ?>
                    </td>
                    <td class="ta-left">
                        <?php echo h($debrief_topic['DebriefTopic']['name_fr']); ?>
                    </td>
                    <td class="ta-left">
                        <?php echo h($debrief_topic['DebriefTopic']['name_de']); ?>
                    </td>
                    <td  class="ta-center">
                        <?php
                            if( $topics_in_use[ $debrief_topic['DebriefTopic']['id']] ){
                                $message_confirm = __t('DebriefTopic.Confirm_delete_in_use');
                            } else {
                                $message_confirm = __t('DebriefTopic.Confirm_delete');
                            }
                            echo $this->Html->link(
                                "<span class='aag-icon-papelera c-fallo'></span>",
                                array(
                                    'controller' => 'tasks',
                                    'action' => 'delete_debrief_topic',
                                    $debrief_topic['DebriefTopic']['id']
                                ),
                                array(
                                    'escape' => false,
                                    'id' => 'delete_topic',
                                    'class' => 'delete-topic-js',
                                    'data-confirmmsg' => $message_confirm,
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