<div class="row">
    <div class="columns medium-12 cnt-shortcuts">
        <h2>
            <?php echo __t('Shortcut.Favourites'); ?>
        </h2>

        <div id="cnt-favorites">
            <?php echo $this->element('../Home/Elements/favorites'); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="d-inline-block w-100p cnt-shortcuts">
        <?php
        foreach ($shortcut_types as $key => $shortcut_type) {
            ?>
            <div class="columns medium-4">
                <h2>
                    <?php echo h($shortcut_type['ShortcutType']['name' . __s()]); ?>
                </h2>
                <?php
                if ($shortcut_type['ShortcutType']['id'] == ConstantsShortcutPosition::POSITION1) {
                    ?>
                    <div class="columns medium-12 cnt-tech-shortcuts">
                        <?php
                        if ($shortcuts_position_1) {
                            foreach ($shortcuts_position_1 as $shortcut_position_1) {
                                if ($shortcut_type['ShortcutType']['single']) {
                                    ?>
                                    <div class="columns medium-12 cnt-tech-shortcut" data-id="<?php echo $shortcut_position_1['Shortcut']['id']; ?>">
                                        <div class="f-left m-right-1 ta-center" style="width: 100px;">
                                            <img src="<?php echo FileManager::get_url(FilePaths::SHORTCUT_IMAGES_RELATIVE . $shortcut_position_1['Shortcut']['image']); ?>">
                                        </div>
                                        <div class="f-left fw-bold lh-1">
                                            <?php echo h($shortcut_position_1['Shortcut']['title']); ?>
                                        </div>
                                        <div style="margin-left: auto;">
                                            <span class="ion-ios-arrow-thin-right arrow-shortcut"></span>
                                        </div>
                                    </div>
                                    <?php
                                    echo $this->Html->link(
                                        '',
                                        array(
                                            'controller' => 'shortcuts',
                                            'action' => 'ajax_view',
                                            $shortcut_position_1['Shortcut']['id']
                                        ),
                                        array(
                                            'escape' => false,
                                            'class' => 'open-modal-js logotipo',
                                            'data-open' => 'myModalSSO',
                                            'data-is-sso' => $shortcut_position_1['Shortcut']['is_sso'],
                                            'hidden' => true,
                                            'id' => 'shortcut_' . $shortcut_position_1['Shortcut']['id'],
                                            'alt' => $shortcut_position_1['Shortcut']['title'],
                                            'title' => $shortcut_position_1['Shortcut']['tooltip'],
                                        )
                                    );
                                }
                                if (!$shortcut_type['ShortcutType']['single']) {
                                    ?>
                                    <div class="columns large-4 medium-6 end p-0">
                                        <div class="columns medium-12 cnt-tech-shortcut-single"
                                             data-id="<?php echo $shortcut_position_1['Shortcut']['id']; ?>">
                                            <img src="<?php echo FileManager::get_url(FilePaths::SHORTCUT_IMAGES_RELATIVE . $shortcut_position_1['Shortcut']['image']); ?>">

                                            <div class="overlay">
                                                <div class="text"><?php echo h($shortcut_position_1['Shortcut']['title']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    echo $this->Html->link(
                                        '',
                                        array(
                                            'controller' => 'shortcuts',
                                            'action' => 'ajax_view',
                                            $shortcut_position_1['Shortcut']['id']
                                        ),
                                        array(
                                            'escape' => false,
                                            'class' => 'open-modal-js logotipo',
                                            'data-open' => 'myModalSSO',
                                            'data-is-sso' => $shortcut_position_1['Shortcut']['is_sso'],
                                            'hidden' => true,
                                            'id' => 'shortcut_' . $shortcut_position_1['Shortcut']['id'],
                                            'alt' => $shortcut_position_1['Shortcut']['title'],
                                            'title' => $shortcut_position_1['Shortcut']['tooltip'],
                                        )
                                    );
                                }
                            }
                        }
                        ?>
                    </div>
                    <?php
                }
                if ($shortcut_type['ShortcutType']['id'] == ConstantsShortcutPosition::POSITION2) {
                    ?>
                    <div class="columns medium-12 cnt-merch-shortcuts">
                        <?php
                        if ($shortcuts_position_2) {
                            foreach ($shortcuts_position_2 as $shortcut_position_2) {
                                if ($shortcut_type['ShortcutType']['single']) {
                                    ?>
                                    <div class="columns medium-12 cnt-merch-shortcut" data-id="<?php echo $shortcut_position_2['Shortcut']['id']; ?>">
                                        <div class="f-left m-right-1 ta-center" style="width: 100px;">
                                            <img src="<?php echo FileManager::get_url(FilePaths::SHORTCUT_IMAGES_RELATIVE . $shortcut_position_2['Shortcut']['image']); ?>">
                                        </div>
                                        <div class="f-left fw-bold">
                                            <?php echo h($shortcut_position_2['Shortcut']['title']); ?>
                                        </div>
                                        <div style="margin-left: auto;">
                                            <span class="ion-ios-arrow-thin-right arrow-shortcut"></span>
                                        </div>
                                    </div>
                                    <?php echo $this->Html->link(
                                        '',
                                        array(
                                            'controller' => 'shortcuts',
                                            'action' => 'ajax_view',
                                            $shortcut_position_2['Shortcut']['id']
                                        ),
                                        array(
                                            'escape' => false,
                                            'class' => 'open-modal-js logotipo',
                                            'data-open' => 'myModalSSO',
                                            'data-is-sso' => $shortcut_position_2['Shortcut']['is_sso'],
                                            'hidden' => true,
                                            'id' => 'shortcut_' . $shortcut_position_2['Shortcut']['id'],
                                            'alt' => $shortcut_position_2['Shortcut']['title'],
                                            'title' => $shortcut_position_2['Shortcut']['tooltip'],
                                        )
                                    );
                                }
                                if (!$shortcut_type['ShortcutType']['single']) {
                                    ?>
                                    <div class="columns large-4 medium-6 end p-0">
                                        <div class="columns medium-12 cnt-merch-shortcut-single"
                                             data-id="<?php echo $shortcut_position_2['Shortcut']['id']; ?>">
                                            <img src="<?php echo FileManager::get_url(FilePaths::SHORTCUT_IMAGES_RELATIVE . $shortcut_position_2['Shortcut']['image']); ?>">

                                            <div class="overlay">
                                                <div class="text"><?php echo h($shortcut_position_2['Shortcut']['title']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    echo $this->Html->link(
                                        '',
                                        array(
                                            'controller' => 'shortcuts',
                                            'action' => 'ajax_view',
                                            $shortcut_position_2['Shortcut']['id']
                                        ),
                                        array(
                                            'escape' => false,
                                            'class' => 'open-modal-js logotipo',
                                            'data-open' => 'myModalSSO',
                                            'data-is-sso' => $shortcut_position_2['Shortcut']['is_sso'],
                                            'hidden' => true,
                                            'id' => 'shortcut_' . $shortcut_position_2['Shortcut']['id'],
                                            'alt' => $shortcut_position_2['Shortcut']['title'],
                                            'title' => $shortcut_position_2['Shortcut']['tooltip'],
                                        )
                                    );
                                }
                            }
                        }
                        ?>
                    </div>
                    <?php
                }
                if ($shortcut_type['ShortcutType']['id'] == ConstantsShortcutPosition::POSITION3) {
                    ?>
                    <div class="columns medium-12 cnt-direct-shortcuts" style="margin: 0 -5px;">
                        <?php
                        if ($shortcuts_position_3) {
                            foreach ($shortcuts_position_3 as $shortcut_position_3) {
                                if ($shortcut_type['ShortcutType']['single']) {
                                    ?>
                                    <div class="columns medium-12 cnt-direct-shortcut"
                                         data-id="<?php echo $shortcut_position_3['Shortcut']['id']; ?>">
                                        <div class="f-left m-right-1 ta-center" style="width: 100px;">
                                            <img src="<?php echo FileManager::get_url(FilePaths::SHORTCUT_IMAGES_RELATIVE . $shortcut_position_3['Shortcut']['image']); ?>">
                                        </div>
                                        <div class="f-left fw-bold lh-1">
                                            <?php echo h($shortcut_position_3['Shortcut']['title']); ?>
                                        </div>
                                        <div style="margin-left: auto;">
                                            <span class="ion-ios-arrow-thin-right arrow-shortcut"></span>
                                        </div>
                                    </div>
                                    <?php
                                    echo $this->Html->link(
                                        '',
                                        array(
                                            'controller' => 'shortcuts',
                                            'action' => 'ajax_view',
                                            $shortcut_position_3['Shortcut']['id']
                                        ),
                                        array(
                                            'escape' => false,
                                            'class' => 'open-modal-js logotipo',
                                            'data-open' => 'myModalSSO',
                                            'data-is-sso' => $shortcut_position_3['Shortcut']['is_sso'],
                                            'hidden' => true,
                                            'id' => 'shortcut_' . $shortcut_position_3['Shortcut']['id'],
                                            'alt' => $shortcut_position_3['Shortcut']['title'],
                                            'title' => $shortcut_position_3['Shortcut']['tooltip'],
                                        )
                                    );
                                }
                                if (!$shortcut_type['ShortcutType']['single']) {
                                    ?>
                                    <div class="columns large-4 medium-6 end p-0">
                                        <div class="cnt-direct-shortcut-single background-color-primary"
                                             data-id="<?php echo $shortcut_position_3['Shortcut']['id']; ?>">
                                            <img src="<?php echo FileManager::get_url(FilePaths::SHORTCUT_IMAGES_RELATIVE . $shortcut_position_3['Shortcut']['image']); ?>">

                                            <div class="overlay">
                                                <div class="text"><?php echo h($shortcut_position_3['Shortcut']['title']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    echo $this->Html->link(
                                        '',
                                        array(
                                            'controller' => 'shortcuts',
                                            'action' => 'ajax_view',
                                            $shortcut_position_3['Shortcut']['id']
                                        ),
                                        array(
                                            'escape' => false,
                                            'class' => 'open-modal-js logotipo',
                                            'data-open' => 'myModalSSO',
                                            'data-is-sso' => $shortcut_position_3['Shortcut']['is_sso'],
                                            'hidden' => true,
                                            'id' => 'shortcut_' . $shortcut_position_3['Shortcut']['id'],
                                            'alt' => $shortcut_position_3['Shortcut']['title'],
                                            'title' => $shortcut_position_3['Shortcut']['tooltip'],
                                        )
                                    );
                                }
                            }
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>