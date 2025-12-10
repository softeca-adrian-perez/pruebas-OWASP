<?php
$current = $this->Paginator->counter(array('format' => '{:page}'));
$numbers = $this->Paginator->numbers(array('separator' => '', 'tag' => 'li'));

echo $this->Form->create(
    'Paginator',
    array(
        'type' => 'post',
        'url' => array(
            'controller' => 'app',
            'action' => 'paginator_size',
            'clients',
            'report_distributor',
            $distributor['Distributor']['id']
        ),
    )
);
$default_option = CakeSession::read('Auth.Paginator.paginator_size') != null ? CakeSession::read('Auth.Paginator.paginator_size') : ConstantsPagination::SIZE_PAGE_SMALL;
?>
<div class="default-paginator">
    <?php
    ?>
    <div>
        <?php echo __t('General.Results'); ?>:
        <strong id="total_paginator">
            <?php echo $count_distributor_garages; ?>
        </strong>
    </div>
    <ul>
        <?php
        echo $this->Paginator->first(__('<<'), array('tag' => 'li'));
        echo $this->Paginator->prev(__('<'), array('tag' => 'li'));
        echo $numbers;
        echo $this->Paginator->next(__('>'), array('tag' => 'li'));
        echo $this->Paginator->last(__('>>'), array('tag' => 'li', 'id' => 'last_paginator'));
        ?>
    </ul>
    <div>
        <?php
        echo $this->Form->input(
            'pagination_size',
            array(
                'type' => 'select',
                'default' => $default_option,
                'required' => true,
                'div' => false,
                'options' => array(
                    ConstantsPagination::SIZE_PAGE_SMALL => ConstantsPagination::SIZE_PAGE_SMALL,
                    ConstantsPagination::SIZE_PAGE_MEDIUM => ConstantsPagination::SIZE_PAGE_MEDIUM,
                    ConstantsPagination::SIZE_PAGE_LARGE => ConstantsPagination::SIZE_PAGE_LARGE
                ),
                'style' => 'width:100px; display:inline-block; margin-bottom: 0;',
                'label' => __t('General.Rows'),
            )
        );
        echo $this->Form->submit(__t('General.Change'), array('class' => 'aag-button small'));
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>