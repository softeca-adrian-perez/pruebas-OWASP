<?php
$action = $this->request->action;
echo $this->Form->create(
    'DistributorComment',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('DistributorComment.id');
echo $this->Form->hidden('DistributorComment.distributor_id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Distributor.Distributors'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Distributor.Comments'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_comments',
                        $distributor_id
                    )
                ),
                __t('Distributor.Comments_add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Distributor.Distributors'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Distributor.Comments'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_comments',
                        $distributor_id
                    )
                ),
                __t('Distributor.Comments_edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data p-top-1 p-bottom-1">
    <div class="cnt-data-element">
        <div class="aag-title">
            <?php
            if ($action == ConstantsActionsNames::ADD) {
                echo __t('Distributor.Comments_add');
            } else {
                echo __t('Distributor.Comments_edit');
            }
            ?>
        </div>
        <div class="cnt-form-inputs p-top-1">
            <?php echo $this->Form->input(
                'body',
                array(
                    'label' => __t('Distributor.Comment'),
                    'type' => 'textarea',
                    'rows' => 15
                )
            ); ?>
        </div>
    </div>
</div>

<?php echo $this->Form->end(); ?>