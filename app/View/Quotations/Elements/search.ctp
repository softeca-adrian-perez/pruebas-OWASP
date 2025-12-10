<?php
echo $this->Html->script('gd_export_excel.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));

echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'quotations',
            'action' => 'home',
			$garage_network_id
        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Quotation.Quotations'); ?>
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
        echo $this->Form->input(
            'quotation_id',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('General.Quotation_id'),
            )
        );
        ?>
    </div>
    <div class="cnt-form-search-buttons">
        <?php
        echo $this->Form->button(
            __t('Quotations.Quotations_export'),
            array(
                'class' => 'aag-button medium gd-export-excel-quotations-js',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'quotations',
                        'action' => 'quotations_excel',
                        $garage_network_id
                    )
                ),
            )
        );
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
