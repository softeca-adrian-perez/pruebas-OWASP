<?php
echo $this->Form->create(
    'DistributorSoftware',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('DistributorSoftware.id');
echo $this->Form->hidden('DistributorSoftware.distributor_id');
$action = $this->request->action;
$config = CakeSession::read('Auth.User.Config');
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
                    __t('Distributor.Software'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_software',
                        $distributor_id
                    )
                ),
                __t('General.Add')
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
                    __t('Distributor.Software'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_software',
                        $distributor_id
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
<div class="cnt-data p-top-1">
    <div class="cnt-data-element m-bottom-1">
        <div class="aag-title m-bottom-1">
            <?php
            if ($action == ConstantsActionsNames::ADD) {
                echo __t('Maintenance.Software_new');
            } else {
                echo __t('Maintenance.Software_edit');
            }
            ?>
        </div>
        <div class="cnt-form-inputs m-bottom-1">
            <?php
            echo $this->Form->input(
                'software_id',
                array(
                    'label' => __t('Maintenance.Software'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $software,
                )
            );
            echo $this->Form->input(
                'software_type_id',
                array(
                    'label' => __t('Software.Type'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $software_types,
                )
            );
            echo $this->Form->input(
                'supplier_id',
                array(
                    'label' => __t('Software.Supplier'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $suppliers,
                )
            );
            echo $this->Form->input(
                'software_manufacture_id',
                array(
                    'label' => __t('Software.Manufacturer'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $software_manufactures,
                )
            );
            echo $this->Form->input(
                'version',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Distributor.Version'),
                )
            );
            echo $this->Form->input(
                'start_date',
                array(
                    'type' => 'text',
                    'required' => true,
                    'class' => 'fecha-js from-js',
                    'id' => 'start_date',
                    'data-to' => '#end_date',
                    'label' => __t('General.From'),
                )
            );
            echo $this->Form->input(
                'end_date',
                array(
                    'type' => 'text',
                    'required' => true,
                    'class' => 'fecha-js to-js',
                    'id' => 'end_date',
                    'data-from' => '#start_date',
                    'label' => __t('General.To'),
                )
            );
            if ($config[ConstantsConfig::SOFTWARE_USER_PASSWORD_DISTRIBUTOR]) {
                echo $this->Form->input(
                    'username',
                    array(
                        'type' => 'text',
                        'required' => true,
                        'label' => __t('Maintenance.Username'),
                    )
                );
                echo $this->Form->input(
                    'password',
                    array(
                        'type' => 'text',
                        'required' => true,
                        'label' => __t('Maintenance.Password'),
                    )
                );
            } ?>
        </div>
        <?php
        if (isset($distributor_software)) { ?>
            <div class="p-left-0">
                <?php
                echo $this->Html->link(
                    '<span class="icon-delete"></span>' . __t('General.Delete'),
                    array(),
                    array(
                        'escape' => false,
                        'title' => __t('General.Delete'),
                        'class' => 'aag-button medium red swal-msg',
                        'data-confirmmsg' => __t('Maintenance.Software_delete?'),
                        'data-yes' => __t('General.Yes'),
                        'data-no' => __t('General.No'),
                        'data-type' => 'warning',
                        'data-url' => Router::url(array(
                            'controller' => 'distributors_software',
                            'action' => 'delete',
                            $distributor_software['DistributorSoftware']['distributor_id'],
                            $distributor_software['DistributorSoftware']['id'],
                        )),
                    )
                ); ?>
            </div>
        <?php } ?>
    </div>
</div>
</div>
<?php echo $this->Form->end(); ?>