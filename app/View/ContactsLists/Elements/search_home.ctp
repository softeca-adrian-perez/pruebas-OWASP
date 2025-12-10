<div class="cnt-form-search">
    <div class="cnt-form-search-title">
        <?php echo __t('CRM.My_contacts_lists');?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'search_home',
            array(
                'type' => 'text',
                'class' => 'search_home',
                'id' => 'search_contact_list',
                'required' => true,
                'label' => __t('CRM.By_contact_list'),
            )
        );
        echo $this->Form->input(
            'search_home',
            array(
                'type' => 'text',
                'class' => 'search_home',
                'id' => 'search_contact',
                'required' => true,
                'label' => __t('CRM.By_contact_name'),
            )
        );
        ?>
    </div>
</div>