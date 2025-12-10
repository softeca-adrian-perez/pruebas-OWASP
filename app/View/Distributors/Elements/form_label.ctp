<?php

echo $this->Form->create(
    'Distributor',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    ));

echo $this->Form->hidden('Distributor.id');
?>
    <div class="flex fw-wrap ai-center cnt-data-element gap-1 p-top-1">
        <div class="aag-subtitle m-right-auto">
            <?php echo __t('Label.Label');?>
        </div>
        <?php if($this->Acceso->haveTradingGroupPermission( ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR , $distributor['Distributor']['trading_group_id'] )){ ?>
            <div class="medium-6 columns ta-right cnt-buttons-v2">
                <?php
                echo $this->Html->link(
                    __t('Label.New_label'),
                    array(
                        'controller' => 'distributors_labels',
                        'action' => 'add',
                        $distributor_id
                    ),
                    array(
                        'escape' => false,
                        'class' => 'aag-button small green',
                        'style' => 'position:relative; z-index: 1;',
                    )
                );
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="o-auto p-top-1">
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo __t('Label.Label'); ?></th>
                <th><?php echo __t('Label.Start_date'); ?></th>
                <th><?php echo __t('Label.End_date'); ?></th>
                <th><?php echo __t('Label.Modification_date'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($distributor_labels as $distributor_label){ ?>
                <tr>
                    <td>
                        <?php echo $this->Html->link(
                            $label_types[$distributor_label['DistributorLabel']['label_type_id']],
                            array(
                                'controller' => 'distributors_labels',
                                'action' => 'edit',
                                $distributor_label['DistributorLabel']['id'],
                                $distributor_id,

                            ),
                            array(
                                'class' => 'c-primary'
                            )
                        ); ?>
                    </td>
                    <td>
                        <?php echo h(Fecha::toFormatoVista($distributor_label['DistributorLabel']['start_date']));?>
                    </td>
                    <td>
                        <?php echo h(Fecha::toFormatoVista($distributor_label['DistributorLabel']['end_date']));?>
                    </td>
                    <td>
                        <?php echo h(Fecha::toFormatoVista($distributor_label['DistributorLabel']['modification_date']));?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php 
    if(
        !$this->Acceso->haveTradingGroupPermission( ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR , $distributor['Distributor']['trading_group_id'] ) && 
        $this->Acceso->haveTradingGroupPermission( ConstantsPermissionsGrouping::REQUEST_CHANGE_DISTRIBUTOR , $distributor['Distributor']['trading_group_id'] )
    ){ ?>
    <div class="row p-top-1">
        <div class="columns medium-7">
            <div class="titulo2">
                <?php echo __t('RequestedChanges.Describe_change'); ?>
            </div>
        </div>
        <div class="medium-5 columns ta-right cnt-buttons-v2">
            <?php echo $this->Form->button(
                __t('RequestedChanges.Request_changes'),
                array(
                    'type' => 'submit',
                    'name' => 'request_changes',
                    'style' => 'margin-top:0 !important;',
                    'class' => 'btn-edit edit',
                )
            );
            ?>
        </div>
        <div class="medium-12 columns end">
        <?php 
            echo $this->Form->input(
                'ChangeDescription',
                array(
                    'type' => 'text',
                    'name' => 'change_description',
                    'rows' => 7,
                    'label' => false
                )
            );
            ?>
        </div>
    </div>
    <?php } ?>
        
    

<?php echo $this->Form->end(); ?>
