<?php
echo $this->Form->create(
    'DistributorService',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('DistributorService.id');
echo $this->Form->hidden('DistributorService.distributor_id');
$action = $this->request->action;
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
                    __t('Distributor.Services'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_services',
                        $distributor_id
                    )
                ),
                __t('Distributor.Add_service'),
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
                    __t('Distributor.Services'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_services',
                        $distributor_id
                    )
                ),
                __t('Distributor.Edit_service'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Distributor.Add_service');
        } else {
            echo __t('Distributor.Edit_service');
        }
        ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <?php echo $this->Form->input(
            'service_type_id',
            array(
                'label' => __t('Distributor.Aag_service'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'options' => $services_types,
                'required' => true,
            )
        );
        echo $this->Form->input(
            'start_date',
            array(
                'required' => true,
                'type' => 'text',
                'class' => 'fecha-js from-js',
                'data-to' => '#to',
                'id' => 'from',
                'label' => __t('Distributor.Start_date'),
            )
        );
        echo $this->Form->input(
            'end_date',
            array(
                'required' => true,
                'type' => 'text',
                'class' => 'fecha-js to-js',
                'data-from' => '#from',
                'id' => 'to',
                'label' => __t('Distributor.End_date'),
            )
        ); ?>
    </div>
    <?php if ($this->action == ConstantsActionsNames::EDIT) { ?>
        <div class="row">
            <div class="ta-right m-top-1">
                <?php echo $this->Html->link(
                    "<span class='aag-icon-papelera'></span>" .
                        __t('General.Delete'),
                    array(
                        'controller' => 'distributors_services',
                        'action' => 'delete',
                        $service['DistributorService']['id'],
                        $distributor_id
                    ),
                    array(
                        'escape' => false,
                        'class' => 'aag-button medium red outlined',
                    )
                ); ?>
            </div>
        </div>
    <?php } ?>
</div>
<?php echo $this->Form->end(); ?>