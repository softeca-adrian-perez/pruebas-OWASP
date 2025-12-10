<?php
$action = $this->request->action;

echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'style' => 'margin-top: 0;',
        'type' => 'get',
        'url' => array(
            'controller' => 'clients',
            'action' => $action,
            $param
        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('CRM.Task_history'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php echo $this->Form->input(
            'title',
            array(
                'type' => 'text',
                'class' => 'clear_field',
                'required' => true,
                'id' => 'task_title',
                'label' => __t('Task.Title'),
            )
        );
        echo $this->Form->input(
                'created_by',
                array(
                    'label' => __t('Task.Created_by'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'options' => $users,
                    'empty' => true,
                    'id' => 'created_by'
                )
            );
            echo $this->Form->input(
                'assigned_to',
                array(
                    'label' => __t('Task.Assigned_to'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'options' => $users,
                    'empty' => true,
                    'id' => 'assigned_to',
                    'data-me' => CakeSession::read('Auth.User.id')
                )
            );
            echo $this->Form->input(
                'completed',
                array(
                    'label' => __t('Task.Status'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'options' => $status,
                    'empty' => true,
                    'id' => 'status',
                )
            );
            echo $this->Form->input(
                'creation_date_from',
                array(
                    'class' => 'fecha-js from-js clear_field',
                    'type' => 'text',
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'required' => true,
                    'id' => 'creation_date_from',
                    'data-to' => '#creation_date_to',
                    'label' => __t('Task.Creation_date') . ' ' .  __t('General.From'),
                )
            );
            echo $this->Form->input(
                'creation_date_to',
                array(
                    'class' => 'fecha-js to-js clear_field',
                    'type' => 'text',
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'required' => true,
                    'id' => 'creation_date_to',
                    'data-from' => '#creation_date_from',
                    'label' => __t('Task.Creation_date') . ' ' . __t('General.To'),
                )
            );
            echo $this->Form->input(
                'completed_date_from',
                array(
                    'class' => 'fecha-js from-js clear_field',
                    'type' => 'text',
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'required' => true,
                    'id' => 'completed_date_from',
                    'data-to' => '#completed_date_to',
                    'label' => __t('Task.Completed_date') . ' ' .  __t('General.From'),
                )
            ); 
            echo $this->Form->input(
                'completed_date_to',
                array(
                    'class' => 'fecha-js to-js clear_field',
                    'type' => 'text',
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'required' => true,
                    'id' => 'completed_date_to',
                    'data-from' => '#completed_date_from',
                    'label' => __t('Task.Completed_date') . ' ' . __t('General.To'),
                )
            ); ?>
        </div>
        <div class="cnt-form-search-buttons">
            <?php
            echo $this->Form->button(
                __t('General.Search'),
                array(
                    'type' => 'submit',
                    'class' => 'aag-button medium',
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