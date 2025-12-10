<?php
echo $this->Form->create(
    'GarageNetwork',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
?>
<div class="buttons-fixed-double-tabs">
    <div>
        <?php
        if(isset($edit_fluids))
        {
            echo $this->element('Comun/form_actions', $cancel_action);
        }
        else
        {
            echo $this->Html->link( __t('General.Back'), CakeSession::read('url_referer'), array('class' => 'aag-button four medium'));
            if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                echo $this->Html->link(
                    __t('General.Edit'),
                    array(
                        'controller' => 'garages_networks',
                        'action' => 'edit_fluids',
                        $garage_network_id
                    ),
                    array(
                        'escape' => false,
                        'title' => __t('General.Edit'),
                        'class' => 'aag-button medium'
                    )
                );
            }
        }
        ?>
    </div>
</div>
<div class="cnt-legend">
    <div>
        <?php
        echo "*" . __t('General.Prices') . " ";
        if ($with_vat) { echo __t("General.With_vat"); }
        else { echo __t("General.Without_vat"); }
        ?>
    </div>
    <div>
        <?php echo __t('GarageNetwork.Price_per_litre'); ?>
    </div>
</div>
<div class="o-auto p-bottom-1">
    <table class="table-tracking">
        <thead>
            <th><?php echo __t('General.Name')?></th>
            <th><?php echo __t('General.Garage_price')?><br></th>
            <th><?php echo __t('General.Network_price_fluids')?></th>
        </thead>
        <tbody>
            <?php
            if(!empty($fluids))
            {
                foreach($fluids as $fluid)
                {
                    ?>
                    <tr>
                        <td><?php echo $fluid['Fluid']['name_' . $language_code] ?></td>
                        <td>
                            <?php
                            if(isset($edit_fluids))
                            {
                                $idFluid = $fluid['Fluid']['id'];
                                echo $this->Form->input(
                                    'fluid_price.' . $idFluid,
                                    array(
                                        'type' => 'number',
                                        'label' => false,
                                        'data-fluid-id' => $idFluid,
                                        'class' => 'w-15 fluid-js',
                                        'placeholder' => 'New price',
                                        'value' => $fluid['Fluid']['garage_price']
                                ));
                            }
                            else
                            {
                                if(!empty($fluid['Fluid']['garage_price'])) {
                            ?>
                            <div style="background-color: #BDECB6">
                            <?php
                                    echo $fluid['Fluid']['garage_price'];
                                }
                                else {
                                    echo "-";
                                }
                            }
                            ?>
                            </div>
                        </td>
                        <td>
                            <?php
                            if(!empty($fluid['Fluid']['price'])) { ?>
                                <div style="<?php echo empty($fluid['Fluid']['garage_price']) ? 'background-color: #BDECB6"' : '';?>">
                                <?php
                                    echo $fluid['Fluid']['price'];
                                } else {
                                    echo "-";
                                }
                            ?>
                        </td>
                    </tr>
                    <?php
                    if(isset($fluid['Fluid']['child_fluids']))
                    {
                        foreach($fluid['Fluid']['child_fluids'] as $child)
                        {
                            ?>
                            <tr>
                                <td><?php echo $child['name_' . $language_code] ?></td>
                                <td>
                                    <?php
                                    if(isset($edit_fluids))
                                    {
                                        $idFluid = $child['id'];
                                        echo $this->Form->input(
                                            'fluid_price.' . $idFluid,
                                            array(
                                                'type' => 'number',
                                                'label' => false,
                                                'data-fluid-id' => $idFluid,
                                                'class' => 'w-15 fluid-js',
                                                'placeholder' => 'New price',
                                                'value' => $child['garage_price']
                                        ));
                                    }
                                    else
                                    {
                                        if(!empty($child['garage_price'])) { ?>
                                            <div style="background-color: #BDECB6">
                                            <?php
                                                echo $child['garage_price'];
                                        } else {
                                            echo "-";
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        if(!empty($child['price'])) { ?>
                                            <div style="<?php echo empty($child['garage_price']) ? 'background-color: #BDECB6"' : '';?>">
                                            <?php
                                                echo $child['price'];
                                        } else {
                                            echo "-";
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                }
            }
            ?>
        </tbody>
    </table>
</div>