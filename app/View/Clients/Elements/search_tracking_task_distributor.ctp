<?php
$param = null;
$action = $this->request->action;
$param = $distributor['Distributor']['id'];

echo $this->Form->create(
    'Search',
    array(
        'class' => 'search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'clients',
            'action' => $action,
            $param
        ),
    )
);
?>
    <div class="row">
        <div class="medium-11 columns m-top-1">
            <div class="medium-3 columns">
                <?php echo $this->Form->input(
                    'title',
                    array(
                        'class' => 'clear_field',
                        'type' => 'text',
                        'required' => true,
                        'id' => 'task_title',
                        'label' => __t('Task.Title'),
                    )
                ); ?>
            </div>

            <div class="medium-3 columns">
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
            <div class="medium-3 columns">

                <?php echo $this->Form->input(
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
                ); ?>

            </div>
            <div class="medium-3 columns">
                <?php echo $this->Form->input(
                    'completed',
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

            <div class="medium-3 columns clear">
                <?php echo $this->Form->input(
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
                ); ?>
            </div>
            <div class="medium-3 columns">
                <?php echo $this->Form->input(
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
                ); ?>
            </div>
            <div class="medium-3 columns">
                <?php echo $this->Form->input(
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
                ); ?>
            </div>
            <div class="medium-3 columns">
                <?php echo $this->Form->input(
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
        </div>
        <div class="medium-1 columns end cnt-buttons-search">
            <?php echo $this->Form->button(
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
<?php echo $this->Form->end(); ?>