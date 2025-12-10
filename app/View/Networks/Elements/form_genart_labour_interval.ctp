<?php
echo $this->Form->create(
    'GenartMaster',
    array(
        'id' => 'form',
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Network.Networks'),
                array(
                    'controller' => 'networks',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Configuration.Configuration'),
                array(
                    'controller' => 'networks',
                    'action' => 'families_configuration',
                    $network_id
                )
            ),
            __t('Network.New_family')
        ));
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-subtitle m-top-1"><?php echo $genart_master['GenartMaster']['name_'.__l()]; ?></div>
    <div class="cnt-form-inputs required">
        <?php
        echo $this->Form->input(
            'min_labour_price',
            array(
                'type' => 'float',
                'required' => true,
                'value' => $genart_master['GenartMaster']['min_labour_price'],
                'label' => __t('Range.Lower_limit'),
            )
        );
        echo $this->Form->input(
            'max_labour_price',
            array(
                'type' => 'float',
                'required' => false,
                'value' => $genart_master['GenartMaster']['max_labour_price'],
                'label' => __t('Range.Upper_limit'),
            )
        );
        echo $this->Form->input(
            'slider_increment',
            array(
                'type' => 'float',
                'required' => false,
                'value' => $genart_master['GenartMaster']['slider_increment'],
                'label' => __t('Range.Slider_increment'),
            )
        );
        ?>
    </div>
	<br />
	<?php
		if (isset($garageNetworks) && !empty($garageNetworks)) { ?>
		<div class="big-error-formulary">
			<strong><?php echo __t('Labour.Error'); ?></strong>
			<span><?php echo __t('Labour.Details_error'); ?></span><br>
			<ul>
				<?php foreach ($garageNetworks as $garageNetwork) { ?>
					<li> <?php echo $garageNetwork; ?></li>
				<?php } ?>
			</ul>
		</div>
	<?php } ?>
</div>
