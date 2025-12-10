<?php
$action = $this->request->action;
echo $this->Form->create(
    'Region',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);
    echo $this->Form->hidden('Region.id');
    ?>
    <div class="cnt-breadcrumb">
        <div>
            <?php
            if($action == 'add_region')
            {
                echo $this->Html->breadcrumb(array(
                    $this->Html->link(
                        __t('Maintenance.Maintenance'),
                        array(
                            'controller' => 'maintenance',
                            'action' => 'home'
                        )
                    ),
                    $this->Html->link(
                        __t('Maintenance.Regions'),
                        array(
                            'controller' => 'regions',
                            'action' => 'maintenance_regions'
                        )
                    ),
                    __t('General.Add'),
                ));
            }
            else
            {
                echo $this->Html->breadcrumb(array(
                    $this->Html->link(
                        __t('Maintenance.Maintenance'),
                        array(
                            'controller' => 'maintenance',
                            'action' => 'home'
                        )
                    ),
                    $this->Html->link(
                        __t('Maintenance.Regions'),
                        array(
                            'controller' => 'regions',
                            'action' => 'maintenance_regions'
                        )
                    ),
                    __t('General.Edit'),
                ));
            }
            ?>
        </div>
        <div>
            <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
        </div>
    </div>
    <div class="cnt-data aag-padding ">
        <div class="aag-title p-bottom-1">
            <?php echo __t('Maintenance.Region'); ?>
        </div>
		<div class="cnt-form-inputs">
            <?php
            echo $this->Form->input(
                'Region.name',
                array(
                    'required' => true,
                    'type' => 'text',
                    'label' => __t('Region.Name'),
                )
            );
            echo $this->Form->input(
                'Region.code',
                array(
                    'required' => true,
                    'type' => 'text',
                    'label' => __t('Region.Code'),
                )
            );
            ?>
        </div>
	</div>
<?php echo $this->Form->end(); ?>