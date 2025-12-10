<?php echo $this->Html->script('genarts_families.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<?php echo $this->Html->script('toggle.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
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
            __t('Configuration.Configuration')
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('Network.New_family'),
            array(
                'controller' => 'networks',
                'action' => 'add_families_configuration',
                $network_id,
            ),
            array('class' => 'aag-button medium green')
        );
        ?>
    </div>
</div>
<?php echo $this->element('../Networks/configuration_tabs', array('selected' => 'families_configuration')); ?>
<div class="cnt-data aag-padding">
    <div class="p-top-1">
        <div>
            <div class="toggle-js cnt-form-toggle-title-secondary opacity1 m-0-i cursor-pointer" data-toggle-id-js="1">
                <?php echo __t('Network.Families_configuration');?>
                <i class="ion-ios-arrow-down arrow-icon-js c-blanco"></i>
            </div>
            <div class="toggle-content-js list-container cnt-dropdown-data" data-toggle-id-js="1">
                <?php echo $this->element('../Networks/Elements/search_family'); ?>
                <table class="table-1" id="families_genarts">
                <thead>
                    <tr>
                        <th width="10%"><?php echo __t('Network.Name') ?></th>
                        <th width="60%"><?php echo __t('GarageNetwork.Genarts') ?></th>
                        <th class="ta-center"><?php echo __t('General.Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($families as $family){ ?>
                        <tr>
                            <td>
                                <?php echo($family['GenartFamily']['name_'.__l()]); ?>
                            </td>
                            <td>
                                <?php
                                foreach($family['GenartFamily']['genarts'] as $genarts){
                                    ?>
                                    <span>
                                        <?php echo $genarts['GenartMaster']['name_'.__l()].' '; ?>
                                    </span>
                                <?php } ?>
                            </td>
                            <td class="ta-center">
                                <?php
                                echo $this->Html->link(
                                    '<span class="aag-icon-editar c-primary"></span>',
                                    array(
                                        'controller' => 'networks',
                                        'action' => 'edit_genart_family',
                                        $network_id,
                                        $family['GenartFamily']['id'],
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('AppointmentObjective.Edit')
                                    )
                                );
                                ?>
                                <span class="aag-icon-papelera c-fallo delete-family cursor-pointer" style="background: none !important; border: none !important;" data-delete-url="
                                    <?php echo Router::url(
                                        array(
                                            'controller' => 'networks',
                                            'action' => 'ajax_delete_families',
                                            $family['GenartFamily']['id'],
                                        )
                                    ); ?>
                                    ">
                                </span>
                            </td>
                        </tr>
                    <?php }
                    if(!empty($genarts_no_family)){ ?>
                        <tr>
                            <td><?php echo __t('Genart.Other_parts') ?></td>
                            <td colspan="2">
                                <?php
                                foreach($genarts_no_family as $genart_no_family){ ?>
                                <span>
                                    <?php echo $genart_no_family['GenartMaster']['name_'.__l()].' '; ?>
                                </span>
                                <?php
                                } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                </table>
            </div>
        </div>
        <div class="p-top-1 p-bottom-1">
            <div class="toggle-js cnt-form-toggle-title-secondary opacity1 m-0-i cursor-pointer" data-toggle-id-js="2">
                <?php echo __t('Range.Hourly_labour_ranges');?>
                <i class="ion-ios-arrow-down arrow-icon-js c-blanco"></i>
            </div>
            <div class="toggle-content-js list-container cnt-dropdown-data" data-toggle-id-js="2">
                <table class="table-1" id="families_genarts">
                    <thead>
                        <tr>
                            <th class="ta-left"><?php echo __t('Network.Name') ?></th>
                            <th class="ta-left"><?php echo __t('Range.Lower_limit') ?></th>
                            <th class="ta-left"><?php echo __t('Range.Upper_limit') ?></th>
                            <th class="ta-left"><?php echo __t('Range.Slider_increment') ?></th>
                            <th class="ta-left"><?php echo __t('General.Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_genarts_master as $genart){ ?>
                            <tr>
                                <td>
                                    <?php echo($genart['GenartMaster']['name_'.__l()]); ?>
                                </td>
                                <td>
                                    <?php echo($genart['GenartMaster']['min_labour_price']); ?>
                                </td>
                                <td>
                                    <?php echo($genart['GenartMaster']['max_labour_price']); ?>
                                </td>
                                <td>
                                    <?php echo($genart['GenartMaster']['slider_increment']); ?>
                                </td>
                                <td class="ta-left">
                                    <?php
                                    if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                        echo $this->Html->link(
                                            '<span class="aag-icon-editar c-primary"></span>',
                                            array(
                                                'controller' => 'networks',
                                                'action' => 'edit_genart_labour_interval',
                                                $network_id,
                                                $genart['GenartMaster']['id'],
                                            ),
                                            array(
                                                'escape' => false,
                                                'title' => __t('AppointmentObjective.Edit')
                                            )
                                        );
                                        ?>
                                        <span class="aag-icon-papelera c-fallo delete-labour cursor-pointer" style="background: none !important; border: none !important;" data-delete-url="
                                            <?php echo Router::url(
                                                array(
                                                    'controller' => 'networks',
                                                    'action' => 'ajax_delete_genart_labour_interval',
                                                    $genart['GenartMaster']['id'],
                                                )
                                            ); ?>
                                            ">
                                        </span>
                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tbody>
                        <tr>
                            <td>
                                <?php echo __t('Network.Labour_price'); ?>
                            </td>
                            <td>
                                <?php echo $network['Network']['min_labour_price']; ?>
                            </td>
                            <td>
                                <?php echo $network['Network']['max_labour_price']; ?>
                            </td>
                            <td>
                                <?php echo $network['Network']['slider_increment']; ?>
                            </td>
                            <td class="ta-left">
                                <?php
                                if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                    echo $this->Html->link(
                                        '<span class="aag-icon-editar c-primary"></span>',
                                        array(
                                            'controller' => 'networks',
                                            'action' => 'edit_labour_interval',
                                            $network_id,
                                            0,
                                        ),
                                        array(
                                            'escape' => false,
                                            'title' => __t('AppointmentObjective.Edit')
                                        )
                                    );
                                    ?>
                                    <span class="aag-icon-papelera c-fallo delete-labour cursor-pointer" style="background: none !important; border: none !important;" data-delete-url="
                                        <?php echo Router::url(
                                            array(
                                                'controller' => 'networks',
                                                'action' => 'ajax_delete_labour_interval',
                                                $network_id,
                                                0,
                                            )
                                        ); ?>
                                        ">
                                    </span>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <?php echo  __t('Network.Labour_price_electric'); ?>
                            </td>
                            <td>
                                <?php echo $network['Network']['min_labour_price_ev']; ?>
                            </td>
                            <td>
                                <?php echo $network['Network']['max_labour_price_ev']; ?>
                            </td>
                            <td>
                                <?php echo $network['Network']['slider_increment_ev']; ?>
                            </td>
                            <td class="ta-left">
                                <?php
                                if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                    echo $this->Html->link(
                                        '<span class="aag-icon-editar c-primary"></span>',
                                        array(
                                            'controller' => 'networks',
                                            'action' => 'edit_labour_interval',
                                            $network_id,
                                            1,
                                        ),
                                        array(
                                            'escape' => false,
                                            'title' => __t('AppointmentObjective.Edit')
                                        )
                                    );
                                    ?>
                                    <span class="aag-icon-papelera c-fallo delete-labour cursor-pointer" style="background: none !important; border: none !important;" data-delete-url="
                                        <?php echo Router::url(
                                            array(
                                                'controller' => 'networks',
                                                'action' => 'ajax_delete_labour_interval',
                                                $network_id,
                                                1,
                                            )
                                        ); ?>
                                        ">
                                    </span>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
