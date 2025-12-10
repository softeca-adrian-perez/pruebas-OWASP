<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Maintenance.Maintenance'),
                array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Shortcut.Shortcuts'),
                array(
                    'controller' => 'shortcuts',
                    'action' => 'maintenance_shortcuts'
                )
            ),
            __t('Shortcut.View'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            array(
                'controller' => 'shortcuts',
                'action' => 'maintenance_shortcuts',
            ),
            array(
                'class' => 'aag-button medium four',
            )
        );
        echo $this->Html->link(
            __t('General.Edit'),
            array(
                'controller' => 'shortcuts',
                'action' => 'edit',
                $shortcut['Shortcut']['id'],
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium',
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="cnt-form-inputs">
        <div>
            <strong><?php echo __t('Shortcut.Title') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h($shortcut['Shortcut']['title']); ?></div>
        </div>
        <div>
            <strong><?php echo __t('Shortcut.Shortcut_type') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo h($shortcut_types[$shortcut['Shortcut']['shortcut_type_id']]); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Shortcut.Start_date') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h(date('d-m-Y', strtotime($shortcut['Shortcut']['start_date']))); ?></div>
        </div>
        <div>
            <strong><?php echo __t('Shortcut.End_date') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h(date('d-m-Y', strtotime($shortcut['Shortcut']['end_date']))); ?></div>
        </div>
        <div>
            <strong><?php echo __t('Shortcut.Roles') ?>: </strong>
            <br>
            <?php $roles = explode(",", $shortcut[0]['Roles']); ?>
            <?php if (!empty($shortcut[0]['Roles'])) { ?>
                <?php foreach ($roles as $role) { ?>
                    <div>
                        <?php echo ' - ' . h($roles_list[$role]); ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div>
            <strong><?php echo __t('Shortcut.Tooltip') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h($shortcut['Shortcut']['tooltip']); ?></div>
        </div>
        <div>
            <strong><?php echo __t('Network.Networks') ?>: </strong>
            <br>
        </div>
        <div>
            <div class="d-inline-block cont-services w-100p">
                <?php
                $cont = 0;
                foreach ($networks as $key_network => $network) { ?>
                    <div class="medium-3 columns end <?php if ($cont % 4 == 0) {
                        echo "clear";
                    } ?>">
                        <?php
                        $class_icono = 'grayscale_icons';

                        foreach ($shortcuts_networks as $key => $shortcut_network) {
                            if (isset($network)) {
                                if ($network['Network']['id'] == strval($key)) {
                                    $class_icono = '';
                                }
                            }
                        }
                        if ($class_icono == '') {
                            $cont++;
                            ?>
                            <div class="p-bottom-1 ta-center">
                                <?php echo $this->Html->image(
                                        '/img/networks/' . $network['Network']['image'],
                                        array(
                                            'class' => $class_icono
                                        )
                                    ) . "<br>" . $network['Network']['name'];
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php }
                ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Network.Trading_groups') ?>: </strong>
            <br>
        </div>
        <div>
            <div class="d-inline-block cont-services w-100p">
                <?php
                $cont = 0;
                foreach ($trading_groups as $key_trading_group => $trading_group) { ?>
                    <div class="medium-3 columns end <?php if ($cont % 4 == 0) {
                        echo "clear";
                    } ?>">
                        <?php
                        $class_icono = 'grayscale_icons';
                        foreach ($shortcuts_trading_groups as $key => $shortcut_trading_group) {
                            if (isset($trading_group)) {
                                if ($trading_group['TradingGroup']['id'] == strval($key)) {
                                    $class_icono = '';
                                }
                            }
                        }

                        if ($class_icono == '') {
                            $cont++;
                            ?>
                            <div class="p-bottom-1 ta-center">
                                <?php echo $this->Html->image(
                                        '/img/trading_groups/' . $trading_group['TradingGroup']['image'],
                                        array(
                                            'class' => $class_icono
                                        )
                                    ) . "<br>" . $trading_group['TradingGroup']['name']; ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php }
                ?>
            </div>
        </div>
        <div>
            <div>
                <strong><?php echo __t('Shortcut.Is_sso') ?>: </strong>
                <br>

                <div class="b-bottom-1 height_input">
                    <?php echo ($shortcut['Shortcut']['is_sso']) ? __t('General.Yes') : __t('General.No'); ?>
                </div>
            </div>
            <div>
                <strong><?php echo __t('Shortcut.Active') ?>: </strong>
                <br>

                <div class="b-bottom-1 height_input">
                    <?php echo ($shortcut['Shortcut']['active']) ? __t('General.Yes') : __t('General.No'); ?>
                </div>
            </div>
            <div>
                <strong><?php echo __t('Shortcut.Url') ?>: </strong>
                <br>

                <div class="b-bottom-1 height_input"><?php echo h($shortcut['Shortcut']['url']); ?></div>
            </div>
            <div>
                <strong><?php echo __t('Shortcut.Parameter_name_1') ?>: </strong>
                <br>

                <div
                    class="b-bottom-1 height_input"><?php echo h($shortcut['Shortcut']['parameter_name_1']); ?></div>
            </div>
            <div>
                <strong><?php echo __t('Shortcut.Parameter_name_2') ?>: </strong>
                <br>

                <div
                    class="b-bottom-1 height_input"><?php echo h($shortcut['Shortcut']['parameter_name_2']); ?></div>
            </div>
            <div>
                <strong><?php echo __t('Shortcut.Image') ?>: </strong>
                <br>
                <div class="ta-center">
                    <img
                        src="<?php echo FilePaths::SHORTCUT_IMAGES_RELATIVE . $shortcut['Shortcut']['image']; ?>"
                        style="max-width: 100px">
                </div>
            </div>
        </div>
    </div>
</div>