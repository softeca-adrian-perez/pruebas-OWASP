<?php
echo $this->Html->script('/js/conferences_delegates.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$controller = $this->request->controller;
$action = $this->request->action;

echo $this->Form->create(
    'Buscador',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'conferences_delegates',
            'action' => 'home'
        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Delegate.Delegates'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'conference_name',
            array(
                'label' => __t('Delegate.Conference_name'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $conferences,
                'empty' => true,
            )
        );
        echo $this->Form->input(
            'venue_name',
            array(
                'label' => __t('Delegate.Venue_name'),
                'class' => 'clear_field dynamicSelect2_venues',
                'type' => 'select',
                'multiple' => false,
                'options' => $array_venue_name,
                'empty' => true,
            )
        );
        echo $this->Form->input(
            'contact',
            array(
                'label' => __t('Delegate.Delegate_name'),
                'type' => 'select',
                'class' => 'clear_field dynamicSelect2_contacts_delegates',
                'options' => $array_contact_name,
                'empty' => true,
                'multiple' => false,
            )
        );
        echo $this->Form->input(
            'distributor_name',
            array(
                'label' => __t('Delegate.Distributor_name'),
                'class' => 'clear_field dynamicSelect2_distributors',
                'type' => 'select',
                'multiple' => false,
                'options' => $array_distributor_name,
                'empty' => true,
            )
        );
        echo $this->Form->input(
            'supplier_name',
            array(
                'label' => __t('Delegate.Supplier_name'),
                'type' => 'select',
                'class' => 'clear_field dynamicSelect2_suppliers',
                'options' => $array_supplier_name,
                'empty' => true,
                'multiple' => false,
            )
        );
        echo $this->Form->input(
            'garage_name',
            array(
                'label' => __t('Delegate.Garage_name'),
                'class' => 'clear_field select2Dinamico_garage cargar_garages',
                'type' => 'select',
                'multiple' => false,
                'options' => $array_garage_name,
                'empty' => true,
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