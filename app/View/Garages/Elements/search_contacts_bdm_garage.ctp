<?php

$controller = $this->request->controller;
$action = $this->request->action;

echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'garages',
            $garage_id
        ),
    )
);
?>

<div class="cnt-form-search-title">
    <?php echo __t('General.Search') ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'first_name',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Distributor.First_name'),
        )
    );
    echo $this->Form->input(
        'last_name',
        array(
            'class' => 'clear_field',
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Distributor.Last_name'),
                )
            );
        echo $this->Form->input(
            'email',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Email'),
            )
        );
        echo $this->Form->input(
            'position_id',
            array(
                'label' => __t('Contact.Position'),
                'class' => 'select2-multiple clear_field',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $positions_list,
            )
        );
        echo $this->Form->input(
            'phone',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Phone'),
            )
        );
        echo $this->Form->input(
            'identification_number',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Contact.Identification_number'),
            )
        ); ?>
</div>
<div class="cnt-form-search-buttons">
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
    );
    ?>
</div>
<?php echo $this->Form->end(); ?>
<br>