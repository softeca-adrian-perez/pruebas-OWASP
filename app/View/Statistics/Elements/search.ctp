<?php
$config = CakeSession::read('Auth.User.Config');
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Html->script('garages.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'statistics',
            'action' => 'home',
        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Statistics.Statistics'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'network_id',
            array(
                'label' => __t('Network.Networks'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $networks,
                'empty' => true,
                'id' => 'network_id'
            )
        );
        echo $this->Form->input(
            'trading_group_id',
            array(
                'label' => __t('TradingGroup.Trading_groups'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $trading_groups,
                'empty' => true,
                'id' => 'trading_group_id'
            )
        );
        echo $this->Form->input(
            'from',
            array(
                'class' => 'fecha-js from-js clear_field',
                'type' => 'text',
                'data-to' => '#to',
                'required' => true,
                'default' => $date_from,
                'id' => 'from',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('General.From'),
            )
        );
        echo $this->Form->input(
            'to',
            array(
                'class' => 'fecha-js to-js max-stats-js clear_field',
                'type' => 'text',
                'required' => true,
                'id' => 'to',
                'default' => $date_to,
                'data-from' => '#from',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('General.To'),
            )
        );
        ?>
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