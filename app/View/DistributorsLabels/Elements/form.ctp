<?php
echo $this->Form->create(
    'DistributorLabel',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('DistributorLabel.id');
echo $this->Form->hidden('DistributorLabel.distributor_id');
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
                    __t('Label.Labels'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_label',
                        $distributor_id
                    )
                ),
                __t('Label.Add_label'),
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
                    __t('Label.Labels'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_label',
                        $distributor_id
                    )
                ),
                __t('Label.Edit_label'),
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
            echo __t('Label.Add_label');
        } else {
            echo __t('Label.Edit_label');
        }
        ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <?php echo $this->Form->input(
            'label_type_id',
            array(
                'label' => __t('Label.Label'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'options' => $labels_types,
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
                'label' => __t('Label.Start_date'),
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
                'label' => __t('Label.End_date'),
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
                        'controller' => 'distributors_labels',
                        'action' => 'delete',
                        $label['DistributorLabel']['id'],
                        $distributor_id
                    ),
                    array(
                        'escape' => false,
                        'class' => 'aag-button medium outlined red',
                    )
                ); ?>
            </div>
        </div>
    <?php
    }
    echo $this->Form->end();
    ?>