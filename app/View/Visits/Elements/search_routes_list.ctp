<?php
echo $this->Form->create(
    'Search',
    array(
        'type' => 'get',
        'style' => 'margin: 0px',
        'class' => 'cnt-form-search',
        'url' => array(
            'controller' => 'visits',
            'action' => 'routes_list'
        ),
    )
);
?>

<div class="cnt-form-search-title">
    <?php echo __t('General.Search'); ?>
</div>
<div class="cnt-form-inputs">
    <div id="cnt-routes">
        <?php echo $this->Form->input(
            'name',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'route_search clear_field',
                'id' => 'route_name_search',
                'label' => __t('Visit.Route_name'),
            )
        ); ?>
    </div>
    <div>
        <?php echo $this->Form->input(
            'type',
            array(
                'label' => __t('Visit.Route_type'),
                'type' => 'select',
                'options' => $route_options,
                'class' => 'select2-multiple route_search clear_field',
                'empty' => true,
                'required' => true,
                'id' => 'route_type_search',
            )
        ); ?>
    </div>
    <div>
        <?php echo $this->Form->input(
            'from',
            array(
                'id' => 'date_from_search',
                'class' => 'fecha-js from-js route_search clear_field',
                'type' => 'text',
                'data-to' => '#date_to_search',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker',
                ),
                'label' => __t('General.From'),
            )
        ); ?>
    </div>
    <div>
        <?php echo $this->Form->input(
            'to',
            array(
                'id' => 'date_to_search',
                'class' => 'fecha-js to-js route_search clear_field',
                'data-from' => '#date_from_search',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker',
                ),
                'label' => __t('General.To'),
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