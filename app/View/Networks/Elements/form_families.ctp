<?php
echo $this->Form->create(
    'GenartFamily',
    array(
        'id' => 'form',
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Network.Networks'),
                array(
                    'controller' => 'networks',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Configuration.Configuration'),
                array(
                    'controller' => 'networks',
                    'action' => 'families_configuration',
                    $network_id
                )
            ),
            __t('Network.New_family')
        ));
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'name_en',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Network.Name_en'),
            )
        );
        echo $this->Form->input(
            'name_es',
            array(
                'type' => 'text',
                'required' => false,
                'label' => __t('Network.Name_es'),
            )
        );
        echo $this->Form->input(
            'name_nl',
            array(
                'type' => 'text',
                'required' => false,
                'label' => __t('Network.Name_nl'),
            )
        );
        echo $this->Form->input(
            'name_fr',
            array(
                'type' => 'text',
                'required' => false,
                'label' => __t('Network.Name_fr'),
            )
        );
        echo $this->Form->input(
            'name_de',
            array(
                'type' => 'text',
                'required' => false,
                'label' => __t('Network.Name_de'),
            )
        );
        echo $this->Form->hidden(
            'active',
            array(
                'value' => isset($genart_family) ? $genart_family['GenartFamily']['active'] : 1
            )
        );
        echo $this->Form->input(
            'network_id',
            array(
                'label' => __t('Network.Networks'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'required' => true,
                'options' => $network,  
                'empty' => $user_role_id == ConstantsRoles::SUPER_ADMIN ? true : false,
                'disabled' => $user_role_id == ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'genarts',
            array(
                'label' => __t('GarageNetwork.Genarts'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => true,
                'required' => true,
                'selected' => isset($selected_genarts) ? $selected_genarts : '',
                'options' => $genarts,
            )
        )
        ?>
    </div>
</div>