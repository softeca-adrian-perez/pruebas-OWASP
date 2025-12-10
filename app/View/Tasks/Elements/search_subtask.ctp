<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'buscador',
        'type' => 'get',
        'url' => array(
            'controller' => 'tasks',
            'action' => 'subtasks_home',

        ),
    )
);
?>
<div class="row p-top-1">
    <div class="medium-12 columns">
        <h1>
            <span class="fw-lighter">
                <div class="medium-12 columns">
                    <div class="title-before-table">
                        <?php echo __t('General.Search_tasks'); ?>
                    </div>
                </div>
            </span>
        </h1>
        <div class="row">
            <div class="medium-12 columns cnt-form-animate">
                <div class="row">
                    <div class="medium-12 columns m-top-1">
                        <div class="medium-3 columns">
                            <?php echo $this->Form->input(
                                'body',
                                array(
                                    'class' => 'clear_field',
                                    'type' => 'text',
                                    'required' => true,
                                    'label' => __t('Task.Task_name'),
                                )
                            ); ?>
                        </div>

                        <div class="medium-2 columns">
                            <?php echo $this->Form->input(
                                'created_by',
                                array(
                                    'label' => __t('Task.Created_by'),
                                    'type' => 'select',
                                    'class' => 'select2-multiple clear_field',
                                    'options' => $users,
                                    'empty' => true,
                                    'id' => 'created_by'
                                )
                            ); ?>
                        </div>
                        <div class="medium-2 columns">

                            <?php echo $this->Form->input(
                                'assigned_to',
                                array(
                                    'label' => __t('Task.Assigned_to'),
                                    'type' => 'select',
                                    'class' => 'select2-multiple clear_field',
                                    'options' => $users,
                                    'empty' => true,
                                    'id' => 'assigned_to',
                                )
                            ); ?>

                        </div>
                        <div class="medium-2 columns end">
                            <?php echo $this->Form->input(
                                'limit_date',
                                array(
                                    'class' => 'fecha-js clear_field',
                                    'type' => 'text',
                                    'div' => array(
                                        'class' => 'datepicker datepicker-label-block',
                                    ),
                                    'required' => true,
                                    'label' => __t('Task.Deadline'),
                                )
                            ); ?>
                        </div>
                        <div class="medium-2 columns">
                            <?php echo $this->Form->input(
                                'task_status_id',
                                array(
                                    'label' => __t('Task.Status'),
                                    'type' => 'select',
                                    'class' => 'select2-multiple clear_field',
                                    'options' => $status,
                                    'empty' => true,
                                    'id' => 'status',
                                )
                            ); ?>
                        </div>
                        <div class="medium-1 columns end cnt-buttons-search">
                            <?php
                            echo $this->Form->button(
                                "<span class='icon-search'></span>",
                                array(
                                    'type' => 'submit',
                                    'class' => 'button-search',
                                    'escape' => false,
                                    'title' =>  __t('General.Search')
                                )
                            );
                            echo $this->Form->button(
                                "<span class='aag-icon-escoba'></span>",
                                array(
                                    'id' => 'clear_field',
                                    'class' => 'aag-button medium four outlined',
                                    'escape' => false,
                                    'title' => __t('General.Clean_search')
                                )
                            ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<br/>