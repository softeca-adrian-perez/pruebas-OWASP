<?php
$action = $this->request->action;

echo $this->Form->create(
    'Fleet',
    array(
        'id' => 'form',
        'type' => 'post',
        'enctype' => 'multipart/form-data'
    )
);
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Menu.Fleets'),
                array(
                    'controller' => 'fleets',
                    'action' => 'home'
                )
            ),
            $action == ConstantsActionsNames::ADD ? __t('Fleet.Add') : __t('Fleet.Edit')
        ));
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
            echo __t('Fleet.Add');
        } else {
            echo __t('Fleet.Edit');
        }
        ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <?php
        echo $this->Form->input(
            'name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('AagService.Name'),
            )
        );
        echo $this->Form->input(
            'address',
            array(
                'type' => 'text',
                'required' => false,
                'label' => __t('Garage.Address'),
            )
        );
        echo $this->Form->input(
            'postcode',
            array(
                'type' => 'text',
                'required' => false,
                'label' => __t('Venue.Post_code'),
            )
        );
        echo $this->Form->input(
            'country_id',
            array(
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $countries,
                'data-url' => Router::url(array(
                    'controller' => 'fleets',
                    'action' => $action == ConstantsActionsNames::ADD ? 'add' : 'edit',
                )),
                'id' => 'network-type-select',
                'required' => false,
                'label' => __t('Menu.Country'),
            )
        );
        echo $this->Form->input(
            'province_id',
            array(
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'required' => false,
                'empty' => true,
                'options' => $provinces,
                'data-url' => Router::url(array(
                    'controller' => 'fleets',
                    'action' => $action == ConstantsActionsNames::ADD ? 'add' : 'edit',
                )),
                'id' => 'network-type-select',
                'label' => __t('Garage.Province'),
            )
        );
        echo $this->Form->input(
            'erp_id',
            array(
                'value' => $garage['Garage']['erp_id'] ?? null,
                'required' => true,
                'id' => 'erp_id-select',
                'label' => __t('Garage.ERP'),
                'class' => 'select2-multiple toggle_edit_erp_id-js required',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'disabled' => false,
                'options' => $erp_providers,
            )
        );
        echo $this->Form->input(
            'ref_code',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Garage.Ref_code'),
            )
        );
        echo $this->Form->input(
            'payment_terms',
            array(
                'type' => 'text',
                'required' => false,
                'label' => __t('Fleet.Payment_terms'),
            )
        );
        echo $this->Form->input(
            'tax_code',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Fleet.Tax_code'),
            )
        );
        echo $this->Form->input(
            'company_code',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Fleet.Company_code'),
            )
        );
        echo $this->Form->input(
            'company_name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Fleet.Company_name'),
            )
        );
        echo $this->Form->input(
            'user_aag_region_name',
            array(
                'type' => 'text',
                'required' => true,
                'value' => $user_aag_region_name,
                'label' => __t('General.Region'),
                'disabled' => true
            )
        );
        ?> <div class="input select required"> <?php
                                                echo $this->Form->input(
                                                    'networks',
                                                    array(
                                                        'label' => __t('Garage.Network'),
                                                        'type' => 'select',
                                                        'options' => $networks,
                                                        'class' => 'select2-multiple input-disabled required',
                                                        'required' => true,
                                                        'multiple' => true
                                                    )
                                                )
                                                ?>
        </div>
        <label class="center-check m-bottom-1">
            <?php echo __t('Fleet.Activate_deactivate'); ?>
            <div class="aag-switch round small">
                <?php echo $this->Form->input('active', array(
                    'id' => 'activeInput-5',
                    'label' => false,
                    'div' => false,
                    'type' => 'checkbox',
                    'default' => false
                )); ?>
                <label for="activeInput-5"></label>
            </div>
        </label>
    </div>
</div>
<?php echo $this->Form->end(); ?>