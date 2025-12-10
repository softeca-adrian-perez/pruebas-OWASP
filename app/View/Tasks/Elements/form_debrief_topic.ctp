<?php
echo $this->Html->script('debrief-topics.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;
echo $this->Form->create(
    'DebriefTopic',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);
echo $this->Form->hidden('DebriefTopic.id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == 'add_debrief_topic') {
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
                __t('General.Add'),
            ));
        } else {
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
                __t('General.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php
        echo $this->element('Comun/form_actions', $cancel_action);
        if ($this->request->action == 'edit_debrief_topic') {
            echo $this->Html->link(
                __t('General.Delete'),
                array(
                    'controller' => 'tasks',
                    'action' => 'delete_debrief_topic',
                    $debrief_topic_id
                ),
                array(
                    'escape' => false,
                    'id' => 'delete_topic',
                    'class' => 'aag-button medium red delete-topic-js',
                    'data-confirmmsg' => empty($topic_in_use) ? __t('DebriefTopic.Confirm_delete') : __t('DebriefTopic.Confirm_delete_in_use'),
                    'data-yes' => __t('General.Yes'),
                    'data-no' => __t('General.No'),
                )
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title p-bottom-1">
        <?php echo __t('Task.Debrief') . ' ' . __t('Task.Topics'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'DebriefTopic.name_en',
            array(
                'required' => true,
                'type' => 'text',
                'label' => __t('General.Name_en'),
            )
        );
        echo $this->Form->input(
            'DebriefTopic.name_fr',
            array(
                'required' => false,
                'type' => 'text',
                'label' => __t('General.Name_fr'),
            )
        );
        echo $this->Form->input(
            'DebriefTopic.name_de',
            array(
                'required' => false,
                'type' => 'text',   
                'label' => __t('General.Name_de'),
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>