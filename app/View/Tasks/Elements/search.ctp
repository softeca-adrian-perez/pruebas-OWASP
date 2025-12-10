<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'tasks',
            'action' => 'home'
        )
    )
);
    $tab = isset($this->request->query['view']) ? $this->request->query['view'] : ConstantsTasksExistingViews::INDEX_ASSIGNED_TO_ME;
    echo $this->Form->hidden(
        'Search.tab',
        array(
            'id' => 'selected_tab',
            'value' => $tab
        )
    )
    ?>
    <div class="cnt-form-search-title">
        <?php echo __t('Task.Tasks'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'title',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'id' => 'task_title',
                'label' => __t('Task.Task_name'),
            )
        );
        ?>
        <div id="filter_created_by">
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
        <div id="filter_assigned_to">
            <?php echo $this->Form->input(
                'assigned_to',
                array(
                    'label' => __t('Task.Assigned_to'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'options' => $users,
                    'empty' => true,
                    'id' => 'assigned_to'
                )
            ); ?>
        </div>
        <div id="filter_limit_date">
            <?php echo $this->Form->input(
                'limit_date_from',
                array(
                    'class' => 'fecha-js from-js clear_field',
                    'type' => 'text',
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'required' => true,
                    'id' => 'dead_line_from',
                    'data-to' => '#dead_line_to',
                    'label' => __t('Task.Deadline_from'),
                )
            ); ?>
        </div>
        <?php
        echo $this->Form->input(
            'limit_date_to',
            array(
                'class' => 'fecha-js to-js clear_field',
                'type' => 'text',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'required' => true,
                'id' => 'dead_line_to',
                'data-from' => '#dead_line_from',
                'label' => __t('Task.Deadline_to'),
            )
        );
        echo $this->Form->input(
            'task_status_id',
            array(
                'label' => __t('Task.Status'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $status,
                'empty' => true,
                'id' => 'status',
            )
        );
        ?>
        <div>
            <?php echo $this->Form->input(
                'view',
                array(
                    'label' => 'Views',
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'options' => $existing_views,
                    'empty' => false,
                    'value' => $tab,
                    'id' => 'existing_view-js',
                    'data-url_' . ConstantsTasksExistingViews::INDEX_ASSIGNED_TO_ME => Router::url(
                        array('controller' => 'tasks', 'action' => 'ajax_assigned_me_task_de')
                    ),
                    'data-url_' . ConstantsTasksExistingViews::INDEX_CREATED_BY_ME => Router::url(
                        array('controller' => 'tasks', 'action' => 'ajax_created_me_task_de')
                    ),
                    'data-url_' . ConstantsTasksExistingViews::INDEX_ASSIGNED_TO_MY_GROUP => Router::url(
                        array('controller' => 'tasks', 'action' => 'ajax_assigned_group_task_de')
                    ),
                    'data-url_' . ConstantsTasksExistingViews::INDEX_ASSIGNED_TO_MY_CUSTOMERS => Router::url(
                        array('controller' => 'tasks', 'action' => 'ajax_assigned_customers_task_de')
                    ),
                    'data-url_' . ConstantsTasksExistingViews::INDEX_ALL => Router::url(
                        array('controller' => 'tasks', 'action' => 'ajax_all_task_de')
                    ),
                )
            ); ?>
        </div>
    </div>
    <div class="cnt-form-search-buttons">
        <?php
        echo $this->Form->button(
            __t('General.Search'),
            array(
                'type' => 'submit',
                'class' => 'aag-button medium'
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
        );
        ?>
    </div>
<?php echo $this->Form->end(); ?>
