<div class="aag-tabs">
    <div class="aag-title"><?php echo $garage_name; ?></div>
    <ul>
        <li <?php if ($selected == 'button_my_garage') { echo 'class="active"'; }?>>
            <?php
                echo $this->Html->link(
                    __t('General.My_garage'),
                    array(
                        'controller' => 'garages_networks',
                        'action' => 'configuration',
                        $garage_network_id
                    )
                );
            ?>
        </li>
        <?php if ($quoting_views_active) { ?>
            <li <?php if ($selected == 'button_pricing_setup') { echo 'class="active"'; }?>>
                <?php
                    echo $this->Html->link(
                        __t('General.Pricing_setup'),
                        array(
                            'controller' => 'garages_networks',
                            'action' => 'basic_job_settings',
                            $garage_network_id
                        )
                    );
                ?>
            </li>
            <li <?php if ($selected == 'button_list_quotations') { echo 'class="active"'; }?>">
                <?php
                    echo $this->Html->link(
                        __t('General.List_of_quotations'),
                        array(
                            'controller' => 'quotations',
                            'action' => 'home',
                            $garage_network_id
                        )
                    );
                ?>
            </li>
        <?php } ?>
    </ul>
</div>
