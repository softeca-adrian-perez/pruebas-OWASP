<?php
echo $this->Form->create('Network', array('class' => 'form-horizontal','enctype' => 'multipart/form-data'));
echo $this->Form->hidden('Network.id');
echo $this->Form->hidden('id', array('label' => false, 'value' => $network_id,));
?>
<div class="cnt-form-inputs p-top-1">
    <?php
    echo $this->Form->input(
        'name',
        array(
            'type' => 'text',
            'required' => true,
            'label' => __t('Training.Network_name'),
            'disabled' => true
        )
    );
    echo $this->Form->input(
        'credit',
        array(
            'type' => 'number',
            'required' => true,
            'label' => __t('Training.Default_credit'),
        )
    ); 
    ?>
</div>

<?php echo $this->Form->end(); ?>