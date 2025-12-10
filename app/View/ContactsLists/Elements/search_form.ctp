<div class="cnt-form-search m-0-i">
    <div class="cnt-form-search-title">
        <?php echo __t('CRM.My_list'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php if (isset($list)) {
            $data_id = $list['ContactList']['id'];
        } else {
            $data_id = '';
        }
        echo $this->Form->input(
            'search_edit_id',
            array(
                'type' => 'text',
                'id' => 'search_form',
                'data-url' => $url,
                'data-id' => $data_id,
                'required' => true,
                'label' => __t('CRM.By_contact_name'),
            )
        ); 
        echo $this->Form->input(
            'position',
            array(
                'label' => __t('CRM.By_position'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'options' => $positions,
                'id' => 'position_form',
                'empty' => true,
            )
        ); ?>
    </div>
</div>