<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'reporting',
            'action' => 'home',
			$isNetwork ? $network_id : $garage_network_id,
			$isNetwork ? true : false,
        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Reporting.ReportingDates') ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'from',
            array(
                'class' => 'fecha-js from-js clear_field',
                'type' => 'text',
                'data-to' => '#to',
                'required' => true,
                'id' => 'from',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('General.From'),
            )
        );
        echo $this->Form->input(
            'to',
            array(
                'class' => 'fecha-js to-js clear_field',
                'type' => 'text',
                'required' => true,
                'id' => 'to',
                'data-from' => '#from',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('General.To'),
            )
        );
        if ($has_child_networks) {
            echo $this->Form->input(
                'child_network_id',
                array(
                    'label' => __t('ChildNetworks.secondary_networks'),
                    'class' => 'disabled_fields',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $child_networks,
                    'id' => 'child_network_id'
                )
            );
        }
        if($isNetwork)
        {
            echo $this->Form->input(
                'garage_id',
                array(
                    'label' => __t('General.Garages'),
                    'class' => 'disabled_fields',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $garages_network,
                    'value' => $garage_id,
                    'id' => 'garage_name_reporting-js',
                    'data-url' => Router::url(array(
                        'controller' => 'garages',
                        'action' => 'ajax_get_garages',
                        $user_aag_region_id,
                        '?' => array(
                            'network_id' => $network_id
                        ),
                    ))
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
        ?>
    </div>
<?php echo $this->Form->end(); ?>
