<?php
echo $this->Html->script('distributors.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'style' => 'margin: 0px',
        'type' => 'get',
        'url' => array(
            'controller' => 'distributors',
            'action' => 'branches',

        ),
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('Distributor.Branches'); ?>
</div>
<div class="cnt-form-inputs">
    <?php echo $this->Form->input(
        'name',
        array(
            'type' => 'text',
            'id' => 'name-distributors',
            'class' => 'search_ajax clear_field',
            'data-url' => Router::url(
                array(
                    'controller' => 'distributors',
                    'action' => 'ajax_search_home_branches',
                )
            ),
            'required' => true,
            'label' => __t('Appointment.Customer'),
        )
    );
    echo $this->Form->input(
        'account_number',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'id' => 'account-number',
            'required' => true,
            'label' => __t('Distributor.Account_number'),
        )
    );
    echo $this->Form->input(
        'VAT_number',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'id' => 'vat-number',
            'required' => true,
            'label' => __t('Distributor.VAT_number'),
        )
    );
    echo $this->Form->input(
        'town',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'id' => 'account-number',
            'required' => true,
            'label' => __t('Distributor.Town'),
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