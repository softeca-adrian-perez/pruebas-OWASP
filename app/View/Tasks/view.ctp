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
                'action' => 'home',
            ),
            array(
                'escape' => false,
                'title' => __t('General.Back'),
                'class' => 'aag-button medium four',
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('General.View'); ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <div class="p-bottom-1">
            <strong class="d-block"><?php echo __t('Task.Task') ?> </strong>
            <?php echo h($task['Task']['body']); ?>
        </div>
        <div class="p-bottom-1">
            <strong class="d-block"><?php echo __t('User.Name') ?> </strong>
            <?php echo h($task['User']['name']) . ' ' . h($task['User']['surname']); ?>
        </div>
        <div class="p-bottom-1">
            <strong class="d-block"><?php echo __t('Task.Creation_date') ?> </strong>
            <?php echo h($task['Task']['creation_date']); ?>
        </div>
        <div class="p-bottom-1">
            <strong class="d-block"><?php echo __t('Task.Deadline') ?> </strong>
            <?php echo h($task['Task']['limit_date']); ?>
        </div>
        <div class="p-bottom-1">
            <strong class="d-block"><?php echo __t('General.Actions') ?> </strong>
            <?php if (Booleano::toString($task['Task']['resolve']) == 'No') { ?>
                <span class="ion-ios-checkmark-outline c-fallo"></span>
            <?php } else { ?>
                <span class="ion-ios-checkmark-outline c-exito"></span>
            <?php } ?>
        </div>
    </div>
</div>