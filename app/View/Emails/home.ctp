<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Email.Emails'),
                array(
                    'controller' => 'emails',
                    'action' => 'home'
                )
            ),
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Emails/Elements/search'); ?>
    <div class="row p-top-1" id="results_table_ajax">
        <?php echo $this->element('../Emails/Elements/results_table'); ?>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
