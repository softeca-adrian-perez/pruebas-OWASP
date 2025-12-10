<?php
echo $this->Html->script('gd_export_excel.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));

echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'bookings',
            'action' => 'home',
			$garage_network_id
        ),
    )
);
?>
<?php echo $this->element('../Bookings/Elements/info_search'); ?>
<?php echo $this->Form->end(); ?>