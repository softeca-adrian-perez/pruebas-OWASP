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
            $this->params['controller'],
            'home'
        ),
    )
);
$default_option;
if (CakeSession::read('Auth.Paginator.paginator_size') != null) {
    $default_option = CakeSession::read('Auth.Paginator.paginator_size');
} else {
    $default_option = ConstantsPagination::SIZE_PAGE_SMALL;
}
?>
<div class="default-paginator">
    <?php
    ?>
    <div>
        <?php echo __t('General.Results'); ?>:
        <strong id="total_paginator">
            <?php echo $this->Paginator->counter('{:count}'); ?>
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
    <?php
    if ($this->params['controller'] != "statistics" && $this->params['action'] != "bdm") {
    ?>
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
    <?php
    }
    ?>
</div>
<?php echo $this->Form->end(); ?>