<?php
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search m-0-i buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'distributors',
            $distributor_id
        ),
    )
);
?>  
    <div class="cnt-form-search-title">
        <?php echo h($distributor['Distributor']['name']); ?>
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
        ); 
        if($this->request->action != 'distributor_add_contacts'){ ?>
            <label class="center-check p-top-1">
                <?php echo __t('Distributor.Show_my_contacts'); ?>
                <div class="aag-switch round small">
                    <?php echo $this->Form->input('active', array('id'=> 'activeInput-6', 'label' => false, 'div' => false, 'type' => 'checkbox', 'class' => 'clear_field', 'checked' => $is_checked_my_contacts)); ?>
                    <?php // echo !empty($garage_network['GarageNetwork']['quoting_active']) ? 'checked' : '' ?>
                    <label for="activeInput-6"></label>
                </div>
            </label>
        <?php } ?>
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
<br/>