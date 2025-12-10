<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'emails',
            'action' => 'home'
        )
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('Email.Emails'); ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'to',
        array(
            'label' => __t('Email.Forward_to'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $to,
            'empty' => true,
            'id' => 'forward_to'
        )
    );
    echo $this->Form->input(
        'subject',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Email.Subject'),
        )
    );
    echo $this->Form->input(
        'type',
        array(
            'label' => __t('Email.Type'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $types,
            'empty' => true,
            'id' => 'type',
        )
    );
    echo $this->Form->input(
        'sent',
        array(
            'label' => __t('General.Sent'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $sent_types,
            'empty' => true,
            'id' => 'type',
        )
    );
    echo $this->Form->input(
        'creation_date',
        array(
            'class' => 'fecha-js clear_field',
            'type' => 'text',
            'div' => array(
                'class' => 'datepicker datepicker-label-block',
            ),
            'required' => true,
            'label' => __t('Email.Creation_date'),
        )
    );
    echo $this->Form->input(
        'platform',
        array(
            'label' => __t('General.Platform'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $platforms,
            'empty' => true
        )
    );
    if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
        echo $this->Form->input(
            'aag_region_id',
            array(
                'label' => __t('General.Region'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'options' => $aagRegions,
                'default' => CakeSession::read('Auth.User.aag_region_id'),
                'disabled' => true,
                'empty' => true,
            )
        );
    }
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