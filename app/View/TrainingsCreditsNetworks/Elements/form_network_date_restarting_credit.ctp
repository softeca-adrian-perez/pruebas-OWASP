<?php
echo $this->Form->create('Network', array('class' => 'form-horizontal','enctype' => 'multipart/form-data'));
echo $this->Form->hidden('Network.id');
echo $this->Form->hidden('id', array('label' => false,'value' => $network_id,));
?>
<div class="cnt-form-inputs p-top-1">
    <?php echo $this->Form->input(
        'name',
        array(
            'type' => 'text',
            'required' => true,
            'label' => __t('Training.Network_name'),
            'disabled' => true
        )
    );
    echo $this->Form->input(
        'date_restarting_credit',
        array(
            'class' => 'fecha-js from-js clear_field',
            'type' => 'text',
            'required' => true,
            'div' => array(
                'class' => 'datepicker datepicker-label-block',
            ),
            'label' => __t('Training.Date_restarting_credit'),
        )
    ); ?>
</div>
<?php echo $this->Form->end(); ?>