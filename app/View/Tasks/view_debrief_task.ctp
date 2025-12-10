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
                __t('Task.Debrief') . ' ' .  __t('Task.Task'),
                array(
                    'controller' => 'tasks',
                    'action' => 'maintenance_tasks'
                )
            ),
            __t('General.View'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            array(
                'controller' => 'tasks',
                'action' => 'maintenance_tasks',
            ),
            array(
                'class' => 'aag-button medium four',
            )
        );
        echo $this->Html->link(
            __t('General.Edit'),
            array(
                'controller' => 'tasks',
                'action' => 'edit_debrief_task',
                $debrief_task['DebriefTask']['id'],
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium',
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="cnt-form-inputs">
        <div>
            <strong><?php echo __t('Task.English_title') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h($debrief_task['DebriefTask']['title_en']); ?></div>
        </div>
        <div>
            <strong><?php echo __t('Task.French_title') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h($debrief_task['DebriefTask']['title_fr']); ?></div>
        </div>
        <div>
            <strong><?php echo __t('Task.German_title') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h($debrief_task['DebriefTask']['title_de']); ?></div>
        </div>
        <div>
            <strong><?php echo __t('Task.English_description') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h($debrief_task['DebriefTask']['description_en']); ?></div>
        </div>
        <div>
            <strong><?php echo __t('Task.French_description') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h($debrief_task['DebriefTask']['description_fr']); ?></div>
        </div>
        <div>
            <strong><?php echo __t('Task.German_description') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h($debrief_task['DebriefTask']['description_de']); ?></div>
        </div>
    </div>
</div>